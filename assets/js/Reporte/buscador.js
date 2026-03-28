(function (global) {
    'use strict';

    class _LRU {
        constructor(max = 60) {
            this._max = max;
            this._map = new Map();
        }
        get(k) {
            if (!this._map.has(k)) return undefined;
            const v = this._map.get(k);
            this._map.delete(k);
            this._map.set(k, v);
            return v;
        }
        set(k, v) {
            if (this._map.has(k)) this._map.delete(k);
            else if (this._map.size >= this._max)
                this._map.delete(this._map.keys().next().value);
            this._map.set(k, v);
        }
        has(k)  { return this._map.has(k); }
        clear() { this._map.clear(); }
    }

    function _db(fn, ms) {
        let t;
        return (...a) => {
            clearTimeout(t);
            return new Promise(res => { t = setTimeout(() => res(fn(...a)), ms); });
        };
    }

    class BuscadorGlobal {

        constructor(categoriasReportes = [], opts = {}) {
            this.cats = Array.isArray(categoriasReportes) ? categoriasReportes : [];

            this.cfg = {
                apiUrl:      opts.apiUrl      || '../controlador/Reporte/api_reportes.php',
                debounceMs:  opts.debounceMs  ?? 280,
                cacheSize:   opts.cacheSize   ?? 60,
                minLength:   opts.minLength   ?? 2,
                maxPorGrupo: opts.maxPorGrupo ?? 5,
                rutas: {
                    participante: 'participante.php',
                    curso:        'cursos.php',
                    docente:      'docente.php',
                    ...(opts.rutas || {})
                }
            };

            this._cache     = new _LRU(this.cfg.cacheSize);
            this._abort     = null;
            this._selIdx    = -1;
            this._flatItems = [];
            this._isOpen    = false;
            this._idx       = this._buildIdx();

            this._debouncedSearch = _db(t => this._fetchAll(t), this.cfg.debounceMs);

            _injectStyles();
        }

        _norm(s) {
            return String(s || '').toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
        }

        _tokenize(text) {
            const norm  = this._norm(text);
            const words = norm.split(' ').filter(w => w.length > 1);
            const out   = [{ token: norm, w: 12 }];
            words.forEach(word => {
                out.push({ token: word, w: 8 });
                for (let i = 3; i < word.length; i++)
                    out.push({ token: word.slice(0, i), w: 4 });
            });
            return out;
        }

        _buildIdx() {
            const idx = new Map();
            this.cats.forEach(cat => {
                (cat.reportes || []).forEach(rep => {
                    this._tokenize(`${rep.nombre} ${cat.nombre}`)
                        .forEach(({ token, w }) => {
                            if (!idx.has(token)) idx.set(token, []);
                            idx.get(token).push({ catId: cat.id, repId: rep.id, w });
                        });
                });
            });
            return idx;
        }

        _lev(a, b) {
            if (!a) return b.length;
            if (!b) return a.length;
            const dp = Array.from({ length: a.length + 1 }, (_, i) => [i]);
            for (let j = 0; j <= b.length; j++) dp[0][j] = j;
            for (let i = 1; i <= a.length; i++)
                for (let j = 1; j <= b.length; j++)
                    dp[i][j] = a[i-1] === b[j-1]
                        ? dp[i-1][j-1]
                        : 1 + Math.min(dp[i-1][j], dp[i][j-1], dp[i-1][j-1]);
            return dp[a.length][b.length];
        }

        _searchReports(term) {
            const norm   = this._norm(term);
            const words  = norm.split(' ').filter(w => w.length > 1);
            const scores = new Map();

            const add = (catId, repId, pts) => {
                const k = `${repId}:${catId}`;
                scores.set(k, (scores.get(k) || 0) + pts);
            };

            words.forEach(w => {
                if (this._idx.has(w))
                    this._idx.get(w).forEach(e => add(e.catId, e.repId, e.w));

                this._idx.forEach((entries, token) => {
                    if (token !== w) {
                        if (token.startsWith(w))    entries.forEach(e => add(e.catId, e.repId, e.w * 0.6));
                        else if (token.includes(w)) entries.forEach(e => add(e.catId, e.repId, e.w * 0.3));
                    }
                });
            });

            if (words.length === 1 && norm.length >= 4) {
                this._idx.forEach((entries, token) => {
                    if (token.length > 3 && this._lev(norm, token) === 1)
                        entries.forEach(e => add(e.catId, e.repId, e.w * 0.4));
                });
            }

            const seen = new Set();
            return [...scores.entries()]
                .sort((a, b) => b[1] - a[1])
                .reduce((acc, [key, score]) => {
                    const [repId, catId] = key.split(':').map(Number);
                    if (seen.has(repId)) return acc;
                    seen.add(repId);
                    const cat = this.cats.find(c => c.id === catId);
                    const rep = cat?.reportes.find(r => r.id === repId);
                    if (cat && rep) acc.push({ cat, rep, score });
                    return acc;
                }, [])
                .slice(0, this.cfg.maxPorGrupo * 2);
        }

        async _fetchEntities(term) {
            const key = `api:${term.toLowerCase()}`;
            if (this._cache.has(key)) return this._cache.get(key);

            if (this._abort) { try { this._abort.abort(); } catch(e){} }
            this._abort = new AbortController();

            try {
                const url = `${this.cfg.apiUrl}?accion=busqueda_global`
                          + `&q=${encodeURIComponent(term)}`
                          + `&limite=${this.cfg.maxPorGrupo}`;
                const res = await fetch(url, {
                    signal:  this._abort.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                this._cache.set(key, data);
                return data;
            } catch (err) {
                if (err.name === 'AbortError') return null;
                console.warn('[BuscadorGlobal] API:', err.message);
                return { participantes: [], cursos: [], docentes: [] };
            }
        }

        async _fetchAll(term) {
            const [reportes, entidades] = await Promise.all([
                Promise.resolve(this._searchReports(term)),
                this._fetchEntities(term)
            ]);
            return { term, reportes, entidades };
        }

        buscarReportes(termino) {
            try {
                return this._searchReports(termino).map(({ cat, rep }) => ({
                    id:              rep.id,
                    nombre:          rep.nombre,
                    categoriaId:     cat.id,
                    categoriaNombre: cat.nombre,
                    categoriaColor:  cat.color,
                    icono:           cat.icono
                }));
            } catch(e) {
                console.warn('[BuscadorGlobal] buscarReportes:', e);
                return [];
            }
        }

        _goTo(ruta, buscar) {
            global.location.href = `${ruta}?buscar=${encodeURIComponent(buscar)}`;
        }

        _color(cls) {
            return {
                primary:'#0d6efd', success:'#198754', warning:'#ffc107',
                danger:'#dc3545',  info:'#0dcaf0',    secondary:'#6c757d'
            }[cls] || '#6c757d';
        }

        _hl(text, term) {
            if (!text || !term) return text || '';
            try {
                const re = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
                return String(text).replace(re, '<mark>$1</mark>');
            } catch { return text; }
        }

        _esc(s) {
            const d = global.document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        _badge(estado) {
            if (!estado) return '';
            const m = {
                'En formación':'success', 'Activo':'success',    'Disponible':'success',
                'Asignado':'primary',     'Inactivo':'secondary', 'En sistema':'info'
            };
            return `<span class="badge bg-${m[estado]||'secondary'} bg-opacity-75 fw-normal"
                         style="font-size:.68rem">${estado}</span>`;
        }

        _buildHTML(data, term) {
            this._flatItems = [];
            const { reportes = [], entidades = {} } = data;
            const html = [];

            if (reportes.length) {
                html.push(`<div class="bg-header">
                    <i class="bi bi-bar-chart-line-fill me-1"></i>Reportes
                    <span class="bg-badge">${reportes.length}</span>
                </div>`);
                reportes.slice(0, this.cfg.maxPorGrupo).forEach(({ cat, rep }) => {
                    const c   = this._color(cat.color);
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo:'reporte', catId: cat.id, repId: rep.id });
                    html.push(`<div class="bg-item" data-idx="${idx}" tabindex="-1" role="option">
                        <span class="bg-item-icon" style="color:${c}">${cat.icono}</span>
                        <span class="bg-item-body">
                            <span class="bg-item-name">${this._hl(rep.nombre, term)}</span>
                            <span class="bg-item-sub" style="color:${c}">${cat.nombre}</span>
                        </span>
                        <i class="bi bi-arrow-right-short bg-item-arrow" style="color:${c}"></i>
                    </div>`);
                });
            }

            const partics = entidades?.participantes || [];
            if (partics.length) {
                html.push(`<div class="bg-header">
                    <i class="bi bi-people-fill me-1"></i>Participantes
                    <span class="bg-badge">${partics.length}</span>
                </div>`);
                partics.forEach(p => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo:'participante', buscar: p.nombre, ruta: this.cfg.rutas.participante });
                    html.push(`<div class="bg-item" data-idx="${idx}" tabindex="-1" role="option">
                        <span class="bg-item-icon" style="color:#0d6efd"><i class="bi bi-person-circle"></i></span>
                        <span class="bg-item-body">
                            <span class="bg-item-name">${this._hl(p.nombre, term)}</span>
                            <span class="bg-item-sub">${p.cedula ? `C.I. ${p.cedula}&nbsp;` : ''}${this._badge(p.estado)}</span>
                        </span>
                        <span class="bg-item-goto"><i class="bi bi-box-arrow-up-right"></i></span>
                    </div>`);
                });
            }

            const cursos = entidades?.cursos || [];
            if (cursos.length) {
                html.push(`<div class="bg-header">
                    <i class="bi bi-book-half me-1"></i>Cursos
                    <span class="bg-badge">${cursos.length}</span>
                </div>`);
                cursos.forEach(c => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo:'curso', buscar: c.nombre, ruta: this.cfg.rutas.curso });
                    html.push(`<div class="bg-item" data-idx="${idx}" tabindex="-1" role="option">
                        <span class="bg-item-icon" style="color:#198754"><i class="bi bi-book-half"></i></span>
                        <span class="bg-item-body">
                            <span class="bg-item-name">${this._hl(c.nombre, term)}</span>
                            <span class="bg-item-sub">${c.estatus ? `<span class="bg-estatus">${c.estatus}</span>` : ''}</span>
                        </span>
                        <span class="bg-item-goto"><i class="bi bi-box-arrow-up-right"></i></span>
                    </div>`);
                });
            }

            const docs = entidades?.docentes || [];
            if (docs.length) {
                html.push(`<div class="bg-header">
                    <i class="bi bi-person-workspace me-1"></i>Docentes
                    <span class="bg-badge">${docs.length}</span>
                </div>`);
                docs.forEach(d => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo:'docente', buscar: d.nombre, ruta: this.cfg.rutas.docente });
                    html.push(`<div class="bg-item" data-idx="${idx}" tabindex="-1" role="option">
                        <span class="bg-item-icon" style="color:#ffc107"><i class="bi bi-person-workspace"></i></span>
                        <span class="bg-item-body">
                            <span class="bg-item-name">${this._hl(d.nombre, term)}</span>
                            <span class="bg-item-sub">
                                ${d.tipo_docente ? `<span class="bg-estatus bg-estatus--warn">${d.tipo_docente}</span>` : ''}
                                ${this._badge(d.estado_docente)}
                            </span>
                        </span>
                        <span class="bg-item-goto"><i class="bi bi-box-arrow-up-right"></i></span>
                    </div>`);
                });
            }

            return html.length
                ? html.join('')
                : `<div class="bg-empty">
                       <i class="bi bi-search display-5 text-muted d-block mb-2"></i>
                       Sin resultados para <strong>"${this._esc(term)}"</strong>
                   </div>`;
        }

        _move(dir, dd) {
            const items = [...dd.querySelectorAll('.bg-item')];
            if (!items.length) return;
            items[this._selIdx]?.classList.remove('bg-item--sel');
            this._selIdx = Math.max(0, Math.min(items.length - 1, this._selIdx + dir));
            items[this._selIdx].classList.add('bg-item--sel');
            items[this._selIdx].scrollIntoView({ block: 'nearest' });
        }

        _activate(dd) {
            dd.querySelectorAll('.bg-item')[this._selIdx]?.click();
        }

        crearBuscador(ui = {}) {
            const placeholder = ui.placeholder || 'Busca reportes, participantes, cursos, docentes...';
            const compact     = ui.compact ?? false;
            const onReporte   = typeof ui.onReporte === 'function'
                ? ui.onReporte
                : (rId, cId) => {
                    if (typeof global.seleccionarReporte === 'function')
                        global.seleccionarReporte(rId, cId);
                  };

            const wrap = document.createElement('div');
            wrap.className = compact ? 'bg-wrap bg-wrap--compact' : 'bg-wrap bg-wrap--hero';
            wrap.setAttribute('role', 'combobox');
            wrap.setAttribute('aria-haspopup', 'listbox');
            wrap.setAttribute('aria-expanded', 'false');

            const group = document.createElement('div');
            group.className = 'bg-group';

            const iconL = document.createElement('span');
            iconL.className = 'bg-icon-left';
            iconL.innerHTML = '<i class="bi bi-search"></i>';

            const input = document.createElement('input');
            input.type        = 'text';
            input.className   = 'bg-input';
            input.placeholder = placeholder;
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('spellcheck',   'false');
            input.setAttribute('aria-label',   'Búsqueda global');

            const spin = document.createElement('span');
            spin.className     = 'bg-spin';
            spin.innerHTML     = '<i class="bi bi-arrow-repeat"></i>';
            spin.style.display = 'none';

            const btnX = document.createElement('button');
            btnX.type             = 'button';
            btnX.className        = 'bg-btn-x';
            btnX.title            = 'Limpiar';
            btnX.innerHTML        = '<i class="bi bi-x-lg"></i>';
            btnX.style.visibility = 'hidden';

            group.appendChild(iconL);
            group.appendChild(input);
            group.appendChild(spin);
            group.appendChild(btnX);
            wrap.appendChild(group);

            const dd = document.createElement('div');
            dd.className     = 'bg-dropdown';
            dd.style.display = 'none';
            dd.setAttribute('role', 'listbox');
            wrap.appendChild(dd);

            let isOpen = false;

            const open  = () => { dd.style.display = 'block';  wrap.setAttribute('aria-expanded','true');  isOpen = true; };
            const close = () => { dd.style.display = 'none';   wrap.setAttribute('aria-expanded','false'); this._selIdx = -1; isOpen = false; };

            const render = (data) => {
                spin.style.display = 'none';
                if (!data) return;
                dd.innerHTML = this._buildHTML(data, data.term);
                open();
                dd.querySelectorAll('.bg-item').forEach(el => {
                    el.addEventListener('mousedown', e => e.preventDefault());
                    el.addEventListener('click', () => {
                        const item = this._flatItems[+el.dataset.idx];
                        if (!item) return;
                        close();
                        input.value           = '';
                        btnX.style.visibility = 'hidden';
                        item.tipo === 'reporte'
                            ? onReporte(item.repId, item.catId)
                            : this._goTo(item.ruta, item.buscar);
                    });
                });
            };

            input.addEventListener('input', async () => {
                const term = input.value.trim();
                btnX.style.visibility = term ? 'visible' : 'hidden';

                if (term.length < this.cfg.minLength) {
                    close();
                    spin.style.display = 'none';
                    return;
                }

                spin.style.display = 'flex';

                const instant = this._searchReports(term);
                if (instant.length) render({ term, reportes: instant, entidades: {} });

                const result = await this._debouncedSearch(term);
                if (result && input.value.trim() === term) render(result);
            });

            input.addEventListener('keydown', e => {
                if (!isOpen) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); this._move( 1, dd); }
                if (e.key === 'ArrowUp')   { e.preventDefault(); this._move(-1, dd); }
                if (e.key === 'Enter')     { e.preventDefault(); this._selIdx >= 0 ? this._activate(dd) : dd.querySelector('.bg-item')?.click(); }
                if (e.key === 'Escape')    { close(); input.blur(); }
                if (e.key === 'Tab')       { close(); }
            });

            input.addEventListener('focus', () => {
                group.classList.add('bg-group--focused');
                if (input.value.trim().length >= this.cfg.minLength && this._flatItems.length) open();
            });
            input.addEventListener('blur', () => {
                group.classList.remove('bg-group--focused');
                setTimeout(close, 160);
            });

            btnX.addEventListener('click', () => {
                input.value = '';
                btnX.style.visibility = 'hidden';
                close();
                input.focus();
            });

            document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); }, { passive: true });

            return wrap;
        }

        crearBuscadorTabs(categoria, onSeleccionarReporte) {
            return this.crearBuscador({
                placeholder: 'Buscar reporte, participante...',
                compact:     true,
                onReporte:   onSeleccionarReporte
            });
        }
    }

    function _injectStyles() {
        const ID = 'bg-styles-v31';
        if (document.getElementById(ID)) return;
        const s = document.createElement('style');
        s.id = ID;
        s.textContent = `
.bg-wrap{position:relative;width:100%}
.bg-wrap--hero{max-width:640px;margin:0 auto}
.bg-wrap--compact{max-width:360px}

.bg-group{display:flex;align-items:center;background:#fff;border:2px solid #dee2e6;
    border-radius:50px;padding:0 14px;gap:8px;transition:border-color .2s,box-shadow .2s}
.bg-wrap--hero .bg-group{padding:6px 18px;box-shadow:0 2px 12px rgba(0,0,0,.07)}
.bg-group--focused{border-color:#0d6efd !important;box-shadow:0 0 0 3px rgba(13,110,253,.18) !important}

.bg-icon-left{color:#6c757d;flex-shrink:0;font-size:1rem}
.bg-wrap--hero .bg-icon-left{font-size:1.15rem}

.bg-input{flex:1;border:none;outline:none;background:transparent;
    font-size:.9rem;color:#212529;min-width:0;padding:5px 0}
.bg-wrap--hero .bg-input{font-size:1rem;padding:7px 0}
.bg-input::placeholder{color:#adb5bd}

.bg-spin{display:flex;align-items:center;color:#6c757d;font-size:.9rem;flex-shrink:0}
.bg-spin i{animation:bg-rotate .7s linear infinite}
@keyframes bg-rotate{to{transform:rotate(360deg)}}

.bg-btn-x{background:none;border:none;padding:2px 4px;color:#adb5bd;cursor:pointer;
    flex-shrink:0;border-radius:50%;line-height:1;font-size:.85rem;
    transition:color .15s,background .15s}
.bg-btn-x:hover{color:#495057;background:#f1f3f5}

.bg-dropdown{position:absolute;top:calc(100% + 6px);left:0;right:0;z-index:1080;
    background:#fff;border:1px solid rgba(0,0,0,.1);border-radius:14px;
    box-shadow:0 8px 32px rgba(0,0,0,.14),0 2px 8px rgba(0,0,0,.08);
    overflow:hidden;max-height:420px;overflow-y:auto;
    animation:bg-fadein .18s ease;scrollbar-width:thin}
.bg-dropdown::-webkit-scrollbar{width:5px}
.bg-dropdown::-webkit-scrollbar-thumb{background:#dee2e6;border-radius:4px}
@keyframes bg-fadein{
    from{opacity:0;transform:translateY(-6px) scale(.98)}
    to{opacity:1;transform:translateY(0) scale(1)}}

.bg-header{display:flex;align-items:center;padding:6px 14px;font-size:.7rem;
    font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6c757d;
    background:#f8f9fa;border-bottom:1px solid #f1f3f5;
    position:sticky;top:0;z-index:1;gap:4px}
.bg-badge{margin-left:auto;background:#e9ecef;color:#495057;
    border-radius:20px;padding:1px 7px;font-size:.65rem;font-weight:600}

.bg-item{display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;
    transition:background .12s;border-bottom:1px solid #f8f9fa;outline:none}
.bg-item:last-child{border-bottom:none}
.bg-item:hover,.bg-item--sel{background:#f0f4ff}

.bg-item-icon{width:32px;height:32px;display:flex;align-items:center;
    justify-content:center;flex-shrink:0;font-size:1.05rem;border-radius:8px;
    background:rgba(0,0,0,.04)}
.bg-item-body{flex:1;min-width:0;display:flex;flex-direction:column;gap:2px}
.bg-item-name{font-size:.88rem;font-weight:500;color:#212529;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bg-item-name mark{background:#fff3cd;color:inherit;padding:0 1px;border-radius:2px}
.bg-item-sub{font-size:.74rem;color:#6c757d;display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.bg-item-arrow,.bg-item-goto{font-size:1rem;color:#adb5bd;flex-shrink:0;
    transition:transform .15s,color .15s}
.bg-item:hover .bg-item-arrow,.bg-item:hover .bg-item-goto{color:#495057;transform:translateX(3px)}

.bg-estatus{display:inline-flex;align-items:center;padding:1px 6px;
    border-radius:20px;font-size:.67rem;font-weight:600;
    background:rgba(25,135,84,.12);color:#198754}
.bg-estatus--warn{background:rgba(255,193,7,.15);color:#856404}

.bg-empty{padding:28px 20px;text-align:center;color:#6c757d;font-size:.88rem}

@media(max-width:576px){
    .bg-dropdown{position:fixed;left:10px;right:10px;width:auto;top:auto}
    .bg-wrap--hero{max-width:100%}}
        `;
        (document.head || document.documentElement).appendChild(s);
    }

    global.BuscadorGlobal = BuscadorGlobal;

}(window));
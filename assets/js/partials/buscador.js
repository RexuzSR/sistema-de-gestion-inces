(function (global) {
    'use strict';

    // ═══════════════════════════════════════════════════════════════
    // ─── LRU Cache ──────────────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════
    class _LRU {
        constructor(max = 60) { this._max = max; this._map = new Map(); }
        get(k) {
            if (!this._map.has(k)) return undefined;
            const v = this._map.get(k); this._map.delete(k); this._map.set(k, v); return v;
        }
        set(k, v) {
            if (this._map.has(k)) this._map.delete(k);
            else if (this._map.size >= this._max) this._map.delete(this._map.keys().next().value);
            this._map.set(k, v);
        }
        has(k) { return this._map.has(k); }
        clear() { this._map.clear(); }
    }

    function _db(fn, ms) {
        let t;
        return (...a) => { clearTimeout(t); return new Promise(res => { t = setTimeout(() => res(fn(...a)), ms); }); };
    }

    // ═══════════════════════════════════════════════════════════════
    // ─── Constantes de UI ───────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════
    const BADGE_CATEGORIA = {
        'Participantes': { color: 'blue', label: 'Participantes', icon: 'bi-people-fill' },
        'Cursos': { color: 'green', label: 'Cursos', icon: 'bi-book-half' },
        'Docentes': { color: 'amber', label: 'Docentes', icon: 'bi-person-workspace' },
        'Metas': { color: 'coral', label: 'Metas', icon: 'bi-bullseye' },
    };
    const BADGE_ENTIDAD = {
        participante: { color: 'blue', label: 'Participante', icon: 'bi-person-circle' },
        curso: { color: 'green', label: 'Curso', icon: 'bi-book-half' },
        docente: { color: 'amber', label: 'Docente', icon: 'bi-person-workspace' },
    };
    const LIMIT_INICIAL = 5;
    const LIMIT_EXPANDIDO = 15;

    // ═══════════════════════════════════════════════════════════════
    // ─── Motor de búsqueda fuzzy ─────────────────────────────────────
    // (funciones puras, sin estado, reutilizables)
    // ═══════════════════════════════════════════════════════════════

    // ── Mejora 12: Normalización extendida ───────────────────────────
    // Además de quitar acentos, iguala caracteres visualmente similares
    // y corrige errores comunes de teclado rápido.
    const _CONFUSABLES = [
        [/0/g, 'o'], [/1/g, 'l'], [/3/g, 'e'], [/4/g, 'a'], [/5/g, 's'],
        [/8/g, 'b'], [/ph/g, 'f'], [/rn/g, 'm'], [/vv/g, 'w'], [/ck/g, 'k'],
        [/qu/g, 'k'], [/x/g, 'ks'],
    ];

    function _norm(s) {
        let r = String(s || '').toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quitar acentos
            .replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
        // aplicar tabla de confusables
        for (const [pat, rep] of _CONFUSABLES) r = r.replace(pat, rep);
        return r;
    }

    // ── Mejora 2: Soundex en español ─────────────────────────────────
    // Codifica la pronunciación aproximada de una palabra.
    // "Jorge" y "Jorje" producen el mismo código → se encuentran.
    function _soundex(s) {
        if (!s) return '';
        const MAP = {
            b: 1, f: 1, p: 1, v: 1, c: 2, g: 2, j: 2, k: 2, q: 2, s: 2, x: 2, z: 2,
            d: 3, t: 3, l: 4, m: 5, n: 5, r: 6
        };
        const a = _norm(s).replace(/\s/g, '');
        if (!a) return '';
        let code = a[0].toUpperCase();
        let prev = MAP[a[0]] || 0;
        for (let i = 1; i < a.length && code.length < 4; i++) {
            const c = MAP[a[i]];
            if (c && c !== prev) { code += c; }
            prev = c || prev;
        }
        return code.padEnd(4, '0');
    }

    // Compara dos palabras fonéticamente. Además del código completo,
    // compara solo los dígitos (sin la primera letra) para capturar
    // pares como c/k, s/z, g/j, b/v que suenan igual pero codifican distinto.
    function _soundexMatch(a, b) {
        const ca = _soundex(a), cb = _soundex(b);
        if (ca === cb) return true;
        const digA = ca.slice(1), digB = cb.slice(1);
        return digA === digB && digA !== '000';
    }

    // ── Mejora 1+3: Levenshtein con detección de transposición ───────
    // Usa el algoritmo Damerau-Levenshtein: cuenta transponer dos letras
    // adyacentes ("ttoal" → "total") como 1 operación en lugar de 2.
    function _lev(a, b) {
        if (!a) return b.length;
        if (!b) return a.length;
        if (Math.abs(a.length - b.length) > 3) return 99; // poda rápida
        const R = a.length + 1, C = b.length + 1;
        const dp = new Uint8Array(R * C);
        for (let i = 0; i < R; i++) dp[i * C] = i;
        for (let j = 0; j < C; j++) dp[j] = j;
        for (let i = 1; i < R; i++) {
            for (let j = 1; j < C; j++) {
                const cost = a[i - 1] === b[j - 1] ? 0 : 1;
                dp[i * C + j] = Math.min(
                    dp[(i - 1) * C + j] + 1,       // borrar
                    dp[i * C + (j - 1)] + 1,        // insertar
                    dp[(i - 1) * C + (j - 1)] + cost  // sustituir
                );
                // transposición: "ab" → "ba" = 1 op
                if (i > 1 && j > 1 && a[i - 1] === b[j - 2] && a[i - 2] === b[j - 1])
                    dp[i * C + j] = Math.min(dp[i * C + j], dp[(i - 2) * C + (j - 2)] + cost);
            }
        }
        return dp[R * C - 1];
    }

    // ── Mejora 4: Subsecuencia fuzzy ─────────────────────────────────
    // Verifica si todos los caracteres del query aparecen en orden
    // dentro del texto, aunque no sean contiguos.
    // "ptcp" → "participante" ✓   "crss" → "cursos" ✓
    // Devuelve un score de 0-1 según qué tan compacta es la coincidencia.
    function _fuzzySeq(query, text) {
        let qi = 0, start = -1, gaps = 0, prev = -1;
        for (let ti = 0; ti < text.length && qi < query.length; ti++) {
            if (text[ti] === query[qi]) {
                if (qi === 0) start = ti;
                if (prev !== -1 && ti - prev > 1) gaps++;
                prev = ti; qi++;
            }
        }
        if (qi < query.length) return 0; // no todos los caracteres encontrados
        const span = prev - start + 1;
        const density = query.length / span;      // qué tan juntos están
        const pos = 1 - start / text.length;  // boost si está al inicio
        return density * 0.7 + pos * 0.3;
    }

    // ── Mejora 8: Boost por posición ─────────────────────────────────
    // Exact start: máximo. Inicio de palabra: alto. Contiene: medio.
    function _posBoost(token, text) {
        if (text === token) return 1.0;  // coincidencia exacta
        if (text.startsWith(token)) return 0.9;  // al inicio del string
        const re = new RegExp(`\\b${token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}`);
        if (re.test(text)) return 0.75; // inicio de palabra interna
        if (text.includes(token)) return 0.5;  // en el medio
        return 0;
    }

    // ═══════════════════════════════════════════════════════════════
    // ─── Clase Principal ────────────────────────────────────────────
    // ═══════════════════════════════════════════════════════════════
    class BuscadorGlobal {

        constructor(categoriasReportes = [], opts = {}) {
            this.cats = Array.isArray(categoriasReportes) ? categoriasReportes : [];
            this.cfg = {
                apiUrl: opts.apiUrl || '../controlador/Reporte/api_reportes.php',
                debounceMs: opts.debounceMs ?? 300,
                cacheSize: opts.cacheSize ?? 60,
                minLength: opts.minLength ?? 2,
                maxPorGrupo: LIMIT_EXPANDIDO,
                rutas: {
                    participante: 'participante.php',
                    curso: 'cursos.php',
                    docente: 'docente.php',
                    ...(opts.rutas || {})
                }
            };
            this._cache = new _LRU(this.cfg.cacheSize);
            this._abort = null;
            this._selIdx = -1;
            this._flatItems = [];
            this._expandedSecs = new Set();
            this._lastData = null;
            this._lastTerm = '';
            this._idx = this._buildIdx();      // índice invertido de tokens
            this._sdxIdx = this._buildSdxIdx();   // índice de soundex
            this._debouncedSearch = _db(t => this._fetchAll(t), this.cfg.debounceMs);
        }

        // ── Construcción de índices ───────────────────────────────────

        _tokenize(text) {
            const norm = _norm(text);
            const words = norm.split(' ').filter(w => w.length > 1);
            const out = [];
            // Token completo con máximo peso
            if (norm) out.push({ token: norm, w: 12 });
            words.forEach(word => {
                out.push({ token: word, w: 8 });
                // Prefijos (mínimo 3 caracteres)
                for (let i = 3; i < word.length; i++)
                    out.push({ token: word.slice(0, i), w: 3 });
            });
            return out;
        }

        _buildIdx() {
            const idx = new Map();
            this.cats.forEach(cat => {
                (cat.reportes || []).forEach(rep => {
                    // Mejora 7: campo nombre tiene más peso que campo categoría
                    const tokens = [
                        ..._norm(rep.nombre).split(' ').filter(w => w.length > 1).map(t => ({ token: t, w: 10, field: 'nombre' })),
                        ..._norm(cat.nombre).split(' ').filter(w => w.length > 1).map(t => ({ token: t, w: 6, field: 'cat' })),
                    ];
                    this._tokenize(`${rep.nombre} ${cat.nombre}`).forEach(({ token, w }) => {
                        if (!idx.has(token)) idx.set(token, []);
                        idx.get(token).push({ catId: cat.id, repId: rep.id, w });
                    });
                    // Agregar tokens de campo con sus pesos diferenciales
                    tokens.forEach(({ token, w }) => {
                        if (!idx.has(token)) idx.set(token, []);
                        // Solo añadir si no existe ya con mayor peso
                        const existing = idx.get(token).find(e => e.repId === rep.id && e.catId === cat.id);
                        if (!existing) idx.get(token).push({ catId: cat.id, repId: rep.id, w });
                        else if (w > existing.w) existing.w = w;
                    });
                });
            });

            // ── Términos base del sistema ───────────────────
            //palabras clave comunes siempre se puedan autocorregir
            const baseTerms = ['curso', 'cursos', 'participante', 'participantes', 'docente', 'docentes', 'meta', 'metas', 'historial', 'usuario', 'usuarios', 'reporte', 'reportes', 'configuracion', 'perfil'];
            baseTerms.forEach(t => {
                if (!idx.has(t)) idx.set(t, []);
                idx.get(t).push({ catId: 0, repId: 0, w: 5 });
            });

            return idx;
        }

        // Mejora 2: índice soundex para todos los reportes
        _buildSdxIdx() {
            const idx = new Map();
            this.cats.forEach(cat => {
                (cat.reportes || []).forEach(rep => {
                    const words = `${rep.nombre} ${cat.nombre}`.split(/\s+/).filter(Boolean);
                    words.forEach(word => {
                        const code = _soundex(word);
                        if (!idx.has(code)) idx.set(code, []);
                        const list = idx.get(code);
                        if (!list.find(e => e.repId === rep.id))
                            list.push({ catId: cat.id, repId: rep.id, w: 3 });
                    });
                });
            });
            return idx;
        }

        // ── Motor de búsqueda local de reportes ───────────────────────
        _searchReports(term) {
            const norm = _norm(term);
            const words = norm.split(' ').filter(w => w.length > 1);
            if (!words.length && norm.length > 1) words.push(norm);

            let results = this._searchPass(words, 1);
            if (results.length < 2) results = this._searchPass(words, 2);
            if (results.length === 0) results = this._searchPass(words, 3);
            return results.slice(0, LIMIT_EXPANDIDO);
        }

        // Dado un query-word, devuelve el mejor score de coincidencia
        // contra cualquier token del texto objetivo según el pass activo.
        _wordScore(w, targetWords, pass) {
            let best = 0;
            for (const token of targetWords) {
                const pb = _posBoost(w, token);
                if (pb > 0) { best = Math.max(best, pb); continue; }
                if (pass >= 2 && w.length >= 3 && token.length >= 3) {
                    const d = _lev(w, token);
                    if (d === 1) best = Math.max(best, 0.55);
                }
                if (pass >= 3 && w.length >= 5 && token.length >= 4) {
                    const d = _lev(w, token);
                    if (d === 2) best = Math.max(best, 0.28);
                }
            }
            if (pass >= 2 && best === 0) {
                if (targetWords.some(t => _soundexMatch(w, t)))
                    best = Math.max(best, 0.38);
            }
            if (pass >= 3 && best === 0) {
                const joined = targetWords.join(' ');
                const fs = _fuzzySeq(w, joined);
                if (fs > 0.3) best = Math.max(best, fs * 0.4);
            }
            return best;
        }

        // Calcula score acumulado por todas las palabras del query.
        // Palabras del query que no encuentran match pesan 0 → reporte baja en ranking.
        // Si TODAS las palabras del query hacen match → bonus multiplicador ×1.5.
        _searchPass(words, pass) {
            const scores = new Map();

            this.cats.forEach(cat => {
                (cat.reportes || []).forEach(rep => {
                    const tokensNombre = _norm(rep.nombre).split(' ').filter(Boolean);
                    const tokensCat = _norm(cat.nombre).split(' ').filter(Boolean);

                    let total = 0;
                    let allMatched = true;

                    words.forEach(w => {
                        // Campo nombre vale más (×1.7) que campo categoría (×1.0)
                        const sN = this._wordScore(w, tokensNombre, pass) * 1.7;
                        const sC = this._wordScore(w, tokensCat, pass) * 1.0;
                        const best = Math.max(sN, sC);
                        if (best === 0) allMatched = false;
                        total += best;
                    });

                    if (total === 0) return;

                    const k = `${rep.id}:${cat.id}`;
                    // Bonus si todas las palabras del query tuvieron match
                    scores.set(k, total * (allMatched && words.length > 1 ? 1.5 : 1.0));
                });
            });

            const seen = new Set();
            return [...scores.entries()]
                .sort((a, b) => b[1] - a[1])
                .reduce((acc, [key]) => {
                    const [repId, catId] = key.split(':').map(Number);
                    if (seen.has(repId)) return acc;
                    seen.add(repId);
                    const cat = this.cats.find(c => c.id === catId);
                    const rep = cat?.reportes.find(r => r.id === repId);
                    if (cat && rep) acc.push({ cat, rep });
                    return acc;
                }, []);
        }

        // Mejora 10: encontrar el término más cercano al buscado para la sugerencia
        _bestSuggestion(norm) {
            let best = null, bestD = Infinity;
            this._idx.forEach((_, token) => {
                if (token.length < 4) return;
                const d = _lev(norm, token);
                if (d < bestD && d <= 3) { bestD = d; best = token; }
            });
            return best;
        }

        // ── Fetch API ─────────────────────────────────────────────────
        async _fetchEntities(term) {
            const key = `api:${term.toLowerCase()}`;
            if (this._cache.has(key)) return this._cache.get(key);
            if (this._abort) { try { this._abort.abort(); } catch (e) { } }
            this._abort = new AbortController();
            try {
                const url = `${this.cfg.apiUrl}?accion=busqueda_global`
                    + `&q=${encodeURIComponent(term)}`
                    + `&limite=${this.cfg.maxPorGrupo}`;
                const res = await fetch(url, { signal: this._abort.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
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

        // ── Helpers de UI ─────────────────────────────────────────────
        // Highlight fuzzy: para cada palabra del query busca la palabra
        // del texto que más se le parece (exacta, prefijo, o Levenshtein ≤ 2)
        // y la resalta aunque haya errores tipográficos en el query.
        _hl(text, term) {
            if (!text || !term) return text || '';
            try {
                const queryWords = _norm(term).split(/\s+/).filter(Boolean);
                const textWords = text.split(/\b/); // split preservando separadores
                return textWords.map(segment => {
                    const segNorm = _norm(segment);
                    if (!segNorm || segNorm.length < 2) return segment;
                    const shouldMark = queryWords.some(qw => {
                        if (!qw || qw.length < 2) return false;
                        if (segNorm === qw) return true;  // exacto
                        if (segNorm.startsWith(qw)) return true;  // prefijo
                        if (qw.startsWith(segNorm)) return true;  // query es prefijo del texto
                        if (segNorm.includes(qw)) return true;  // contiene
                        const d = _lev(qw, segNorm);
                        if (qw.length >= 4 && d === 1) return true;  // lev-1
                        if (qw.length >= 6 && d === 2) return true;  // lev-2
                        if (_soundexMatch(qw, segNorm) && qw.length >= 3) return true; // fonético
                        return false;
                    });
                    return shouldMark ? `<mark>${segment}</mark>` : segment;
                }).join('');
            } catch { return text; }
        }

        _esc(s) { const d = global.document.createElement('div'); d.textContent = s; return d.innerHTML; }

        _initials(nombre) {
            if (!nombre) return '?';
            const p = nombre.trim().split(/\s+/);
            return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : nombre.slice(0, 2).toUpperCase();
        }

        _badge(tipo, label) {
            const cfg = tipo === 'reporte'
                ? (BADGE_CATEGORIA[label] || { color: 'blue', label, icon: 'bi-bar-chart-line-fill' })
                : (BADGE_ENTIDAD[tipo] || { color: 'gray', label: tipo, icon: 'bi-tag' });
            return `<span class="bg3-badge bg3-badge--${cfg.color}"><i class="bi ${cfg.icon}"></i>${cfg.label}</span>`;
        }

        _statusPill(estado) {
            if (!estado) return '';
            const map = {
                'Aprobado': 'green', 'Disponible': 'green', 'Activo': 'green', 'Culminado': 'green',
                'En formación': 'blue', 'Asignado': 'blue', 'En sistema': 'blue', 'En proceso': 'blue',
                'Inactivo': 'gray', 'Diferido': 'amber', 'En espera': 'amber',
                'Ausente': 'amber', 'Reprobado': 'coral',
            };
            return `<span class="bg3-status bg3-status--${map[estado] || 'gray'}">${estado}</span>`;
        }

        _sectionHeader(titulo, icono, total, tipo) {
            const colorMap = { section: 'purple', participante: 'blue', curso: 'green', docente: 'amber' };
            const color = colorMap[tipo] || 'gray';
            return `<div class="bg3-sec-hd bg3-sec--${color}">
                        <i class="bi ${icono}"></i><span>${titulo}</span>
                        <span class="bg3-sec-cnt">${total}</span>
                    </div>`;
        }

        _toggleBtn(secKey, total) {
            if (total <= LIMIT_INICIAL) return '';
            const expandido = this._expandedSecs.has(secKey);
            const restantes = total - LIMIT_INICIAL;
            return expandido
                ? `<button class="bg3-toggle-btn" data-sec="${secKey}" data-action="collapse">
                       <i class="bi bi-chevron-up"></i> Mostrar menos
                   </button>`
                : `<button class="bg3-toggle-btn" data-sec="${secKey}" data-action="expand">
                       <i class="bi bi-chevron-down"></i> Ver ${restantes} más
                   </button>`;
        }

        // ── Construcción HTML del dropdown ────────────────────────────
        _buildHTML(data, term) {
            this._flatItems = [];
            const { reportes = [], entidades = {} } = data;
            const html = [];
            let hasResults = false;

            // ── Reportes ──────────────────────────────────────────────
            if (reportes.length) {
                hasResults = true;
                const expandido = this._expandedSecs.has('reporte');
                const visibles = expandido ? reportes.slice(0, LIMIT_EXPANDIDO) : reportes.slice(0, LIMIT_INICIAL);
                html.push(this._sectionHeader('Reportes', 'bi-bar-chart-line-fill', reportes.length, 'section'));
                visibles.forEach(({ cat, rep }) => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo: 'reporte', catId: cat.id, repId: rep.id });
                    html.push(`<div class="bg3-item" data-idx="${idx}" tabindex="-1" role="option">
                        <div class="bg3-item-main">
                            <span class="bg3-item-name">${this._hl(rep.nombre, term)}</span>
                            <div class="bg3-item-meta">${this._badge('reporte', cat.nombre)}</div>
                        </div>
                        <i class="bi bi-arrow-right-short bg3-item-arrow"></i>
                    </div>`);
                });
                html.push(this._toggleBtn('reporte', reportes.length));
            }

            // ── Participantes ─────────────────────────────────────────
            const partics = entidades?.participantes || [];
            if (partics.length) {
                hasResults = true;
                const expandido = this._expandedSecs.has('participante');
                const visibles = expandido ? partics.slice(0, LIMIT_EXPANDIDO) : partics.slice(0, LIMIT_INICIAL);
                html.push(this._sectionHeader('Participantes', 'bi-people-fill', partics.length, 'participante'));
                visibles.forEach(p => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo: 'participante', buscar: p.cedula || p.nombre, ruta: this.cfg.rutas.participante });
                    html.push(`<div class="bg3-item" data-idx="${idx}" tabindex="-1" role="option">
                        <div class="bg3-item-avatar bg3-avatar--blue">${this._initials(p.nombre)}</div>
                        <div class="bg3-item-main">
                            <span class="bg3-item-name">${this._hl(p.nombre, term)}</span>
                            <div class="bg3-item-meta">
                                ${p.cedula ? `<span class="bg3-item-detail"><i class="bi bi-credit-card-2-front"></i>&nbsp;${this._hl(p.cedula, term)}</span>` : ''}
                                ${this._badge('participante', 'Participante')}
                                ${this._statusPill(p.estado)}
                            </div>
                        </div>
                        <i class="bi bi-box-arrow-up-right bg3-item-goto"></i>
                    </div>`);
                });
                html.push(this._toggleBtn('participante', partics.length));
            }

            // ── Cursos ────────────────────────────────────────────────
            const cursos = entidades?.cursos || [];
            if (cursos.length) {
                hasResults = true;
                const expandido = this._expandedSecs.has('curso');
                const visibles = expandido ? cursos.slice(0, LIMIT_EXPANDIDO) : cursos.slice(0, LIMIT_INICIAL);
                html.push(this._sectionHeader('Cursos', 'bi-book-half', cursos.length, 'curso'));
                visibles.forEach(c => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo: 'curso', buscar: String(c.id_curso || c.nombre), ruta: this.cfg.rutas.curso });
                    html.push(`<div class="bg3-item" data-idx="${idx}" tabindex="-1" role="option">
                        <div class="bg3-item-icon-wrap bg3-icon--green"><i class="bi bi-book-half"></i></div>
                        <div class="bg3-item-main">
                            <span class="bg3-item-name">${this._hl(c.nombre, term)}</span>
                            <div class="bg3-item-meta">
                                ${this._badge('curso', 'Curso')}
                                ${c.id_curso ? `<span class="bg3-item-detail"><i class="bi bi-hash"></i>${c.id_curso}</span>` : ''}
                                ${this._statusPill(c.estatus)}
                            </div>
                        </div>
                        <i class="bi bi-box-arrow-up-right bg3-item-goto"></i>
                    </div>`);
                });
                html.push(this._toggleBtn('curso', cursos.length));
            }

            // ── Docentes ──────────────────────────────────────────────
            const docs = entidades?.docentes || [];
            if (docs.length) {
                hasResults = true;
                const expandido = this._expandedSecs.has('docente');
                const visibles = expandido ? docs.slice(0, LIMIT_EXPANDIDO) : docs.slice(0, LIMIT_INICIAL);
                html.push(this._sectionHeader('Docentes', 'bi-person-workspace', docs.length, 'docente'));
                visibles.forEach(d => {
                    const idx = this._flatItems.length;
                    this._flatItems.push({ tipo: 'docente', buscar: d.cedula || d.nombre, ruta: this.cfg.rutas.docente });
                    html.push(`<div class="bg3-item" data-idx="${idx}" tabindex="-1" role="option">
                        <div class="bg3-item-avatar bg3-avatar--amber">${this._initials(d.nombre)}</div>
                        <div class="bg3-item-main">
                            <span class="bg3-item-name">${this._hl(d.nombre, term)}</span>
                            <div class="bg3-item-meta">
                                ${d.cedula ? `<span class="bg3-item-detail"><i class="bi bi-credit-card-2-front"></i>&nbsp;${this._hl(d.cedula, term)}</span>` : ''}
                                ${this._badge('docente', 'Docente')}
                                ${d.tipo_docente ? `<span class="bg3-item-detail">${d.tipo_docente}</span>` : ''}
                                ${this._statusPill(d.estado_docente)}
                            </div>
                        </div>
                        <i class="bi bi-box-arrow-up-right bg3-item-goto"></i>
                    </div>`);
                });
                html.push(this._toggleBtn('docente', docs.length));
            }

            // ── Mejora 10: Sin resultados → sugerencia ortográfica ────
            if (!hasResults) {
                const norm = _norm(term);
                const suggestion = this._bestSuggestion(norm);
                html.push(`<div class="bg3-empty">
                    <i class="bi bi-search"></i>
                    <p>Sin resultados para <strong>"${this._esc(term)}"</strong></p>
                    ${suggestion && suggestion !== norm
                        ? `<button class="bg3-suggest-btn" data-q="${this._esc(suggestion)}">
                               ¿Quisiste decir <em>${this._esc(suggestion)}</em>?
                           </button>`
                        : ''}
                </div>`);
            }

            return html.join('');
        }

        // ── Navegación por teclado ────────────────────────────────────
        _move(dir, dd) {
            const items = [...dd.querySelectorAll('.bg3-item')];
            if (!items.length) return;
            items[this._selIdx]?.classList.remove('bg3-item--sel');
            this._selIdx = Math.max(0, Math.min(items.length - 1, this._selIdx + dir));
            items[this._selIdx].classList.add('bg3-item--sel');
            items[this._selIdx].scrollIntoView({ block: 'nearest' });
        }

        _activate(dd) { dd.querySelectorAll('.bg3-item')[this._selIdx]?.click(); }

        // ── API pública ───────────────────────────────────────────────
        buscarReportes(termino) {
            try {
                return this._searchReports(termino).map(({ cat, rep }) => ({
                    id: rep.id, nombre: rep.nombre,
                    categoriaId: cat.id, categoriaNombre: cat.nombre,
                    categoriaColor: cat.color, icono: cat.icono
                }));
            } catch (e) { console.warn('[BuscadorGlobal] buscarReportes:', e); return []; }
        }

        _goTo(item) {
            global.location.href = `${item.ruta}?buscar=${encodeURIComponent(item.buscar)}`;
        }

        // ── Constructor DOM del buscador ──────────────────────────────
        crearBuscador(ui = {}) {
            const placeholder = ui.placeholder || 'Busca reportes, participantes, cursos, docentes...';
            const compact = ui.compact ?? false;
            const onReporte = typeof ui.onReporte === 'function'
                ? ui.onReporte
                : (rId, cId) => { if (typeof global.seleccionarReporte === 'function') global.seleccionarReporte(rId, cId); };

            this._expandedSecs.clear();

            const wrap = document.createElement('div');
            wrap.className = compact ? 'bg3-wrap bg3-wrap--compact' : 'bg3-wrap bg3-wrap--hero';
            wrap.setAttribute('role', 'combobox');
            wrap.setAttribute('aria-haspopup', 'listbox');
            wrap.setAttribute('aria-expanded', 'false');

            const group = document.createElement('div');
            group.className = 'bg3-group';

            const iconL = document.createElement('span');
            iconL.className = 'bg3-icon-left';
            iconL.innerHTML = '<i class="bi bi-search"></i>';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'bg3-input';
            input.placeholder = placeholder;
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('spellcheck', 'false');
            input.setAttribute('aria-label', 'Búsqueda global');

            const spin = document.createElement('span');
            spin.className = 'bg3-spin';
            spin.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
            spin.style.display = 'none';

            const btnX = document.createElement('button');
            btnX.type = 'button';
            btnX.className = 'bg3-btn-x';
            btnX.title = 'Limpiar';
            btnX.innerHTML = '<i class="bi bi-x-lg"></i>';
            btnX.style.visibility = 'hidden';

            group.appendChild(iconL);
            group.appendChild(input);
            group.appendChild(spin);
            group.appendChild(btnX);
            wrap.appendChild(group);

            const dd = document.createElement('div');
            dd.className = 'bg3-dropdown';
            dd.style.display = 'none';
            dd.setAttribute('role', 'listbox');
            wrap.appendChild(dd);

            let isOpen = false;
            let _ddInteracting = false;

            const open = () => { dd.style.display = 'block'; wrap.setAttribute('aria-expanded', 'true'); isOpen = true; };
            const close = () => { dd.style.display = 'none'; wrap.setAttribute('aria-expanded', 'false'); this._selIdx = -1; isOpen = false; };

            dd.addEventListener('mouseenter', () => { _ddInteracting = true; });
            dd.addEventListener('mouseleave', () => { _ddInteracting = false; });
            dd.addEventListener('mousedown', () => { _ddInteracting = true; });
            dd.addEventListener('mouseup', () => { setTimeout(() => { _ddInteracting = false; }, 200); });

            const render = (data, preserveScroll = false) => {
                spin.style.display = 'none';
                if (!data) return;
                this._lastData = data;
                const scrollY = preserveScroll ? dd.scrollTop : 0;
                dd.innerHTML = this._buildHTML(data, data.term);
                open();
                attachItemEvents();
                attachToggleEvents();
                attachSuggestEvents(input, render);
                if (preserveScroll) dd.scrollTop = scrollY;
            };

            const attachItemEvents = () => {
                dd.querySelectorAll('.bg3-item').forEach(el => {
                    el.addEventListener('mousedown', e => e.preventDefault());
                    el.addEventListener('click', () => {
                        const item = this._flatItems[+el.dataset.idx];
                        if (!item) return;
                        close();
                        input.value = '';
                        btnX.style.visibility = 'hidden';
                        this._expandedSecs.clear();
                        item.tipo === 'reporte' ? onReporte(item.repId, item.catId) : this._goTo(item);
                    });
                });
            };

            const attachToggleEvents = () => {
                dd.querySelectorAll('.bg3-toggle-btn').forEach(btn => {
                    btn.addEventListener('mousedown', e => e.preventDefault());
                    btn.addEventListener('click', () => {
                        const sec = btn.dataset.sec, action = btn.dataset.action;
                        action === 'expand' ? this._expandedSecs.add(sec) : this._expandedSecs.delete(sec);
                        if (this._lastData) render(this._lastData, true);
                    });
                });
            };

            // Mejora 10: click en sugerencia → rellenar input y re-buscar
            const attachSuggestEvents = (inp, renderFn) => {
                dd.querySelectorAll('.bg3-suggest-btn').forEach(btn => {
                    btn.addEventListener('mousedown', e => e.preventDefault());
                    btn.addEventListener('click', async () => {
                        const q = btn.dataset.q;
                        inp.value = q;
                        btnX.style.visibility = 'visible';
                        this._expandedSecs.clear();
                        spin.style.display = 'flex';
                        const instant = this._searchReports(q);
                        if (instant.length) renderFn({ term: q, reportes: instant, entidades: {} });
                        const result = await this._fetchAll(q);
                        if (result) renderFn(result);
                        inp.focus();
                    });
                });
            };

            input.addEventListener('input', async () => {
                const term = input.value.trim();
                btnX.style.visibility = term ? 'visible' : 'hidden';
                if (term.length < this.cfg.minLength) {
                    close(); spin.style.display = 'none'; this._expandedSecs.clear(); return;
                }
                this._lastTerm = term;
                this._expandedSecs.clear();
                spin.style.display = 'flex';

                // Mostrar reportes locales de inmediato (sin esperar la API)
                const instant = this._searchReports(term);
                if (instant.length) render({ term, reportes: instant, entidades: {} });

                // Fusionar con entidades de la API
                const result = await this._debouncedSearch(term);
                if (result && input.value.trim() === term) render(result);
            });

            input.addEventListener('keydown', e => {
                // ── Mejora 13: Autocorrección al presionar ESPACIO ─────
                if (e.key === ' ' || e.code === 'Space') {
                    const val = input.value;
                    const pos = input.selectionStart;
                    if (pos > 0 && (pos === val.length || val[pos] === ' ')) {
                        const before = val.slice(0, pos);
                        const words = before.split(/\s+/);
                        const last = words[words.length - 1];

                        if (last.length >= 3) {
                            const normLast = _norm(last);
                            // No corregir si ya existe exactamente en el índice
                            if (!this._idx.has(normLast)) {
                                const suggestion = this._bestSuggestion(normLast);
                                if (suggestion && suggestion !== normLast) {
                                    // Reemplazar la última palabra
                                    words[words.length - 1] = suggestion;
                                    const correctedBefore = words.join(' ');
                                    const after = val.slice(pos);
                                    input.value = correctedBefore + after;
                                    // Reposicionar cursor
                                    input.selectionStart = input.selectionEnd = correctedBefore.length;
                                    // Disparar input para que busque con la corrección
                                    input.dispatchEvent(new Event('input'));
                                }
                            }
                        }
                    }
                }

                if (!isOpen) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); this._move(1, dd); }
                if (e.key === 'ArrowUp') { e.preventDefault(); this._move(-1, dd); }
                if (e.key === 'Enter') { e.preventDefault(); this._selIdx >= 0 ? this._activate(dd) : dd.querySelector('.bg3-item')?.click(); }
                if (e.key === 'Escape') { close(); input.blur(); }
                if (e.key === 'Tab') { close(); }
            });

            input.addEventListener('focus', () => {
                group.classList.add('bg3-group--focused');
                if (input.value.trim().length >= this.cfg.minLength && this._flatItems.length) open();
            });
            input.addEventListener('blur', () => {
                group.classList.remove('bg3-group--focused');
                setTimeout(() => { if (!_ddInteracting) close(); }, 220);
            });

            btnX.addEventListener('click', () => {
                input.value = ''; btnX.style.visibility = 'hidden';
                this._expandedSecs.clear(); close(); input.focus();
            });

            document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); }, { passive: true });

            return wrap;
        }

        crearBuscadorTabs(categoria, onSeleccionarReporte) {
            return this.crearBuscador({
                placeholder: 'Buscar reporte, participante...',
                compact: true,
                onReporte: onSeleccionarReporte
            });
        }
    }


    global.BuscadorGlobal = BuscadorGlobal;

}(window));
// ── Inicialización automática en el header ────────────────────────────
// Este bloque monta el buscador dentro de #header-search-container,
// que está definido en partials/header.php.
// Se ejecuta al cargar el script, sin esperar DOMContentLoaded,
// porque el header ya está en el DOM cuando este script se evalúa.
// ── Inicialización en el header ──────────────────────────────────────
// Usa DOMContentLoaded para garantizar que el contenedor y document.head
// existen en todos los módulos, independientemente de dónde se cargue el script.
document.addEventListener('DOMContentLoaded', function _initHeaderBuscador() {
    const contenedor = document.getElementById('header-search-container');
    if (!contenedor) return; // página sin header estándar

    // categoriasReportes solo existe en reporte.php.
    // En otros módulos se usa array vacío — el buscador igual busca en la API.
    const cats = (typeof window.categoriasReportes !== 'undefined')
        ? window.categoriasReportes
        : [];

    const buscador = new BuscadorGlobal(cats, {
        apiUrl: '/Sistema%20de%20Gestion%20Inces/app/controlador/Reporte/api_reportes.php',
        rutas: {
            participante: 'participante.php',
            curso: 'cursos.php',
            docente: 'docente.php',
        }
    });

    const widget = buscador.crearBuscador({
        placeholder: 'Buscar reportes, participantes, cursos...',
        onReporte: function (repId, catId) {
            if (typeof window.seleccionarReporte === 'function') {
                // Dentro de reporte.php: navegación interna directa
                window.seleccionarReporte(repId, catId);
            } else {
                // Cualquier otro módulo: redirigir a reporte.php
                window.location.href = 'reporte.php?reporte=' + repId + '&categoria=' + catId;
            }
        }
    });

    contenedor.appendChild(widget);
});
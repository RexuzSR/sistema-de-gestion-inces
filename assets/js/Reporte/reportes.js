// La variable categoriasReportes ahora se carga desde assets/js/partials/categorias_reportes.js
// para que esté disponible globalmente en todas las páginas para el buscador.


const cardsContainer = document.getElementById('cardsContainer');

// Helper function to POST data and open the resulting PDF in a new tab inline (bypassing Chrome's POST-to-blank viewer bug)
window.postAndOpenPdf = function(url, data) {
    if (typeof mostrarLoading === 'function') mostrarLoading(true);
    
    const newWindow = window.open('about:blank', '_blank');
    if (newWindow) {
        newWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Generando PDF...</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #475569; }
                    .spinner { border: 4px solid #cbd5e1; border-top: 4px solid #0d6efd; border-radius: 50%; width: 45px; height: 45px; animation: spin 1s linear infinite; margin-bottom: 20px; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                    .message { font-size: 16px; font-weight: 500; }
                </style>
            </head>
            <body>
                <div class="spinner"></div>
                <div class="message">Generando reporte PDF, por favor espere...</div>
            </body>
            </html>
        `);
        newWindow.document.close();
    }

    const formData = new FormData();
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const val = data[key];
            formData.append(key, typeof val === 'object' ? JSON.stringify(val) : val);
        }
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al generar el PDF en el servidor');
        return response.blob();
    })
    .then(blob => {
        if (typeof ocultarLoading === 'function') ocultarLoading();
        const fileURL = URL.createObjectURL(new Blob([blob], { type: 'application/pdf' }));
        if (newWindow) {
            newWindow.location.href = fileURL;
        } else {
            window.open(fileURL, '_blank');
        }
    })
    .catch(error => {
        if (typeof ocultarLoading === 'function') ocultarLoading();
        if (newWindow) newWindow.close();
        console.error('Error al generar PDF:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Error al generar el reporte en PDF.');
        } else {
            alert('Error al generar el reporte en PDF.');
        }
    });
};
const reportContainer = document.getElementById('reportContainer');
const loadingOverlay = document.getElementById('loadingOverlay');
let tabsContainer = null;
let currentCategoryId = null;
let currentReportId = null;
let chartInstances = {};
let cleanupStickyTabs = null;
let categoriaActivaId = null;

function getColorFromCategory(colorClass) {
    const map = {
        primary: '#0d6efd', success: '#198754', warning: '#ffc107',
        danger: '#dc3545', info: '#0dcaf0', secondary: '#6c757d'
    };
    return map[colorClass] || '#6c757d';
}

function aplicarRippleCard(element, event, colorHex) {
    element.querySelectorAll('.rp-card-ripple').forEach(r => r.remove());
    const rect = element.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    const maxDist = Math.max(
        Math.hypot(x, y),
        Math.hypot(x - rect.width, y),
        Math.hypot(x, y - rect.height),
        Math.hypot(x - rect.width, y - rect.height)
    );
    const diameter = maxDist * 2.5;
    const ripple = document.createElement('div');
    ripple.className = 'rp-card-ripple';
    ripple.style.cssText = `
        width: ${diameter}px; height: ${diameter}px;
        left: ${x - diameter / 2}px; top: ${y - diameter / 2}px;
        background: ${colorHex};
    `;
    element.appendChild(ripple);
    setTimeout(() => ripple.remove(), 800);
}

function mostrarReporteSkeleton() {
    const reportContainer = document.getElementById('reportContainer');
    if (!reportContainer) return;
    if (reportContainer.querySelector('.rp-skeleton-wrapper')) return;
    reportContainer.innerHTML = `
        <div class="rp-skeleton-wrapper">
            <div class="rp-skeleton-header">
                <div class="rp-skeleton-title rp-skeleton-pulse"></div>
                <div class="rp-skeleton-subtitle rp-skeleton-pulse"></div>
            </div>
            <div class="rp-skeleton-kpis">
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
            </div>
            <div class="rp-skeleton-grid">
                <div class="rp-skeleton-main rp-skeleton-pulse"></div>
                <div class="rp-skeleton-side rp-skeleton-pulse"></div>
            </div>
        </div>
    `;
    reportContainer.style.display = 'block';
}

function mostrarLoading(isPdf = false) {
    if (isPdf) {
        const o = document.getElementById('loadingOverlay');
        if (o) { o.classList.add('active'); o.style.display = 'flex'; }
    } else {
        mostrarReporteSkeleton();
    }
}

function ocultarLoading() {
    const o = document.getElementById('loadingOverlay');
    if (o) { o.classList.remove('active'); o.style.display = 'none'; }
}

function validarEstructuraDatos() {
    try {
        if (!Array.isArray(categoriasReportes)) return false;
        categoriasReportes.forEach((cat, i) => {
            if (!cat.id || !cat.nombre) throw new Error(`Cat ${i}: falta id/nombre`);
            if (!cat.icono) cat.icono = '<i class="bi bi-card-text"></i>';
            if (!cat.color) cat.color = 'primary';
            if (!cat.bgColor) cat.bgColor = 'rgba(13,110,253,0.9)';
            if (!cat.lightColor) cat.lightColor = 'rgba(13,110,253,0.5)';
            if (!cat.darkColor) cat.darkColor = 'rgba(0,80,200,0.95)';
            if (!cat.bodyBgColor) cat.bodyBgColor = 'rgba(13,110,253,0.06)';
            if (!Array.isArray(cat.reportes)) throw new Error(`Cat ${cat.nombre}: sin reportes`);
            cat.reportes.forEach((r, j) => {
                if (!r.id || !r.nombre || !r.funcion)
                    throw new Error(`Rep ${j} de ${cat.nombre}: incompleto`);
            });
        });
        return true;
    } catch (e) { console.error('Error validando datos:', e); return false; }
}

function obtenerCategoriaPorId(id) { return categoriasReportes.find(c => c.id === id) || null; }

function obtenerReportePorIds(cId, rId) {
    const cat = obtenerCategoriaPorId(cId);
    if (!cat) return null;
    const rep = cat.reportes.find(r => r.id === rId);
    return rep ? { categoria: cat, reporte: rep } : null;
}

function debounce(fn, delay) {
    let t;
    return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), delay); };
}

function _abrirMetas() {
    // Usamos el flujo estándar que ahora maneja la carga diferida (lazy loading)
    seleccionarReporte(11, 4);
}

function _mostrarReporteMetasDirecto() {
    // Modo directo: mostrar el reporte Metas Totales usando el flujo estándar
    seleccionarReporte(11, 4);
}


function _cargarResumen() {
    fetch('../controlador/Reporte/api_reportes.php?accion=resumen_global', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.ok ? r.json() : Promise.reject(r.status))
        .then(data => {
            const setNum = (id, val) => {
                const el = document.getElementById(id);
                if (el) { el.textContent = val ?? '—'; _animarContador(el, parseInt(val) || 0); }
            };
            setNum('rsc-num-participantes', data.participantes);
            setNum('rsc-num-cursos', data.cursos);
            setNum('rsc-num-docentes', data.docentes);

            const pct = Math.min(100, Math.max(0, parseFloat(data.pct_metas) || 0));
            const numMetas = document.getElementById('rsc-num-metas');
            const barMetas = document.getElementById('rsc-bar-metas');
            if (numMetas) numMetas.textContent = pct.toFixed(1) + '%';
            if (barMetas) setTimeout(() => { barMetas.style.width = pct + '%'; }, 120);
        })
        .catch(() => {
            ['rsc-num-participantes', 'rsc-num-cursos', 'rsc-num-docentes', 'rsc-num-metas']
                .forEach(id => { const el = document.getElementById(id); if (el) el.textContent = 'N/D'; });
        });
}

function _animarContador(el, target) {
    if (!target || target <= 0) return;
    const dur = 800;
    const step = Math.ceil(target / (dur / 16));
    let cur = 0;
    const tick = () => {
        cur = Math.min(cur + step, target);
        el.textContent = cur.toLocaleString();
        if (cur < target) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

let _reportesInicializados = false;
function init() {
    if (_reportesInicializados) return;
    _reportesInicializados = true;

    if (!validarEstructuraDatos()) { mostrarErrorInicial('Estructura de datos inválida'); return; }
    try {
        crearTabsContainer();
        mostrarCategorias();

        const params = new URLSearchParams(window.location.search);
        const catId = params.get('cat');
        const repId = params.get('rep');
        const entityId = params.get('id');

        if (catId && repId) {
            setTimeout(() => {
                seleccionarReporte(parseInt(repId, 10), parseInt(catId, 10));
            }, 50);
        }
    } catch (e) { console.error('Error init:', e); mostrarErrorInicial('Error al inicializar'); }
}

function mostrarErrorInicial(msg) {
    if (!cardsContainer) return;
    cardsContainer.innerHTML = `
        <div class="alert alert-danger m-4">
            <h4><i class="bi bi-exclamation-triangle-fill me-2"></i>Error</h4>
            <p>${msg}</p>
            <button class="btn btn-outline-danger" onclick="window.location.reload()">Reintentar</button>
        </div>`;
}

function mostrarCategoriasForzado() { try { mostrarCategorias(); } catch (e) { } }

function crearTabsContainer() {
    if (tabsContainer) return;
    tabsContainer = document.createElement('div');
    tabsContainer.id = 'tabsContainer';
    tabsContainer.className = 'tabs-wrapper';
    const ca = document.getElementById('contentArea');
    if (ca) ca.insertBefore(tabsContainer, reportContainer);
}

function mostrarCategorias() {
    try {
        if (cleanupStickyTabs) { cleanupStickyTabs(); cleanupStickyTabs = null; }
        if (tabsContainer) { tabsContainer.style.display = 'none'; tabsContainer.classList.remove('sticky-active'); }
        if (reportContainer) reportContainer.style.display = 'none';
        if (!cardsContainer) return;

        currentReportId = null;
        currentCategoryId = null;
        categoriaActivaId = null;

        cardsContainer.style.display = 'block';
        cardsContainer.innerHTML = '';
        cardsContainer.className = 'mt-0 pt-0';
        cardsContainer.style.position = 'relative';

        // ── Título principal ──
        const title = document.createElement('h1');
        title.className = 'text-center mb-3 fw-bold text-primary rp-main-title';
        title.textContent = 'Reportes del Sistema';
        cardsContainer.appendChild(title);

        const subtitle = document.createElement('p');
        subtitle.className = 'text-center text-muted mb-4 rp-main-subtitle';
        subtitle.textContent = 'Selecciona una categoría para explorar los reportes disponibles';
        cardsContainer.appendChild(subtitle);

        // ── Cards de resumen (totales desde la API) ──
        const resumenGrid = document.createElement('div');
        resumenGrid.className = 'rp-resumen-grid mb-4';
        resumenGrid.id = 'rp-resumen-grid';
        resumenGrid.innerHTML = `
            <div class="rp-resumen-card rp-resumen-card--primary" id="rsc-participantes">
                <div class="rp-resumen-icon"><i class="bi bi-people-fill"></i></div>
                <div class="rp-resumen-body">
                    <span class="rp-resumen-num" id="rsc-num-participantes">—</span>
                    <span class="rp-resumen-label">Participantes</span>
                </div>
            </div>
            <div class="rp-resumen-card rp-resumen-card--success" id="rsc-cursos">
                <div class="rp-resumen-icon"><i class="bi bi-book-half"></i></div>
                <div class="rp-resumen-body">
                    <span class="rp-resumen-num" id="rsc-num-cursos">—</span>
                    <span class="rp-resumen-label">Cursos</span>
                </div>
            </div>
            <div class="rp-resumen-card rp-resumen-card--warning" id="rsc-docentes">
                <div class="rp-resumen-icon"><i class="bi bi-person-workspace"></i></div>
                <div class="rp-resumen-body">
                    <span class="rp-resumen-num" id="rsc-num-docentes">—</span>
                    <span class="rp-resumen-label">Docentes</span>
                </div>
            </div>
            <div class="rp-resumen-card rp-resumen-card--danger" id="rsc-metas">
                <div class="rp-resumen-icon"><i class="bi bi-bullseye"></i></div>
                <div class="rp-resumen-body">
                    <span class="rp-resumen-num" id="rsc-num-metas">—</span>
                    <span class="rp-resumen-label">Progreso de Metas</span>
                    <div class="rp-resumen-bar-wrap">
                        <div class="rp-resumen-bar" id="rsc-bar-metas" style="width:0%"></div>
                    </div>
                </div>
            </div>`;
        cardsContainer.appendChild(resumenGrid);
        _cargarResumen();

        // ── 4 botones de categorías ──
        const nav = document.createElement('nav');
        nav.className = 'rp-category-nav mb-4';

        categoriasReportes.forEach(categoria => {
            const color = getColorFromCategory(categoria.color);

            const btn = document.createElement('button');
            btn.className = `rp-cat-nav-item rp-cat-nav-item--${categoria.color}`;
            btn.dataset.catId = categoria.id;
            btn.innerHTML = `
                ${categoria.icono}
                ${categoria.nombre}
                <span class="rp-cat-nav-count">${categoria.reportes.length}</span>`;

            btn.addEventListener('click', (e) => {
                aplicarRippleCard(btn, e, color);
                toggleCategoriaExpansion(categoria.id);
            });

            nav.appendChild(btn);
        });

        cardsContainer.appendChild(nav);

        // ── Área de expansión de reportes ──
        const expansionArea = document.createElement('div');
        expansionArea.id = 'rp-expansion-area';
        expansionArea.className = 'rp-expansion-area';
        cardsContainer.appendChild(expansionArea);

        cardsContainer.classList.remove('fade-out');
        cardsContainer.classList.add('fade-in');

    } catch (e) { console.error('Error mostrarCategorias:', e); mostrarErrorInicial('Error al mostrar categorías'); }
}

function toggleCategoriaExpansion(categoriaId) {
    const categoria = obtenerCategoriaPorId(categoriaId);
    if (!categoria) return;

    if (categoriaActivaId === categoriaId) { cerrarExpansion(); return; }

    const esCambioDirecto = categoriaActivaId !== null;

    document.querySelectorAll('.rp-cat-nav-item').forEach(b => b.classList.remove('active'));

    const activeBtn = document.querySelector(`.rp-cat-nav-item[data-cat-id="${categoriaId}"]`);
    if (activeBtn) activeBtn.classList.add('active');

    categoriaActivaId = categoriaId;
    const area = document.getElementById('rp-expansion-area');

    if (esCambioDirecto && area) {
        area.style.transition = 'opacity 0.15s ease';
        area.style.opacity = '0';
        setTimeout(() => mostrarExpansionReportes(categoria, true), 150);
    } else {
        mostrarExpansionReportes(categoria, false);
    }
}

function cerrarExpansion() {
    categoriaActivaId = null;
    document.querySelectorAll('.rp-cat-nav-item').forEach(b => b.classList.remove('active'));
    const area = document.getElementById('rp-expansion-area');
    if (area) {
        area.style.transition = 'opacity 0.2s ease, max-height 0.28s ease';
        area.style.opacity = '0';
        area.style.maxHeight = '0';
        setTimeout(() => { area.innerHTML = ''; area.style.cssText = ''; }, 300);
    }
}

function mostrarExpansionReportes(categoria, esSwap = false) {
    const area = document.getElementById('rp-expansion-area');
    if (!area) return;

    const color = getColorFromCategory(categoria.color);

    if (!esSwap) {
        area.innerHTML = '';
        area.style.cssText = '';
        area.style.opacity = '0';
        area.style.maxHeight = '0';
        area.style.overflow = 'hidden';
        area.style.transition = 'none';
    } else {
        area.innerHTML = '';
    }

    const inner = document.createElement('div');
    inner.className = 'rp-expansion-inner';
    inner.style.borderColor = `${color}30`;

    const header = document.createElement('div');
    header.className = 'rp-expansion-header';
    header.innerHTML = `
        <div class="rp-expansion-category-badge" style="color:${color};border-color:${color}35;background:${color}10">
            ${categoria.icono}
            <span>${categoria.nombre}</span>
            <span class="rp-expansion-count" style="background:${color}">${categoria.reportes.length}</span>
        </div>
        <button class="rp-expansion-close" onclick="cerrarExpansion()" title="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>`;
    inner.appendChild(header);

    const chain = document.createElement('div');
    chain.className = 'rp-reports-chain';
    chain.id = 'rp-reports-chain';

    categoria.reportes.forEach(reporte => {
        const node = document.createElement('div');
        node.className = 'rp-report-node';
        node.id = `report-node-${reporte.id}`;
        node.style.cssText = `opacity:0; transform:translateY(18px) scale(0.92); --node-color:${color};`;
        node.innerHTML = `
            <div class="rp-report-node-icon-wrap" style="background:${color}12;color:${color}">
                ${categoria.icono}
            </div>
            <div class="rp-report-node-body">
                <div class="rp-report-node-name">${reporte.nombre}</div>
                <div class="rp-report-node-cta" style="color:${color}">
                    Ver reporte&nbsp;<i class="bi bi-arrow-right-short"></i>
                </div>
            </div>`;
        node.addEventListener('click', e => { e.stopPropagation(); seleccionarReporte(reporte.id, categoria.id); });
        chain.appendChild(node);
    });

    inner.appendChild(chain);
    area.appendChild(inner);

    if (esSwap) {
        requestAnimationFrame(() => {
            area.style.transition = 'opacity 0.2s ease';
            area.style.opacity = '1';
            setTimeout(() => animarNodos(chain), 60);
        });
    } else {
        requestAnimationFrame(() => requestAnimationFrame(() => {
            area.style.transition = 'opacity 0.28s ease, max-height 0.35s cubic-bezier(0.4,0,0.2,1)';
            area.style.opacity = '1';
            area.style.maxHeight = '700px';
            setTimeout(() => { area.style.overflow = 'visible'; }, 360);
            setTimeout(() => animarNodos(chain), 180);
        }));
    }
}

function animarNodos(chain) {
    Array.from(chain.querySelectorAll('.rp-report-node')).forEach((node, idx) => {
        setTimeout(() => {
            node.style.transition = 'opacity 270ms ease, transform 290ms cubic-bezier(0.34,1.56,0.64,1)';
            node.style.opacity = '1';
            node.style.transform = 'translateY(0) scale(1)';
        }, idx * 65);
    });
}

function crearTabs(categoriaId, reporteActivoId) {
    try {
        if (!tabsContainer) crearTabsContainer();
        tabsContainer.innerHTML = '';
        const categoria = obtenerCategoriaPorId(categoriaId);
        if (!categoria?.reportes?.length) return;

        tabsContainer.style.background = categoria.lightColor.replace(/[\d.]+\)$/, '0.15)');
        tabsContainer.style.border = `2px solid ${categoria.darkColor}`;
        tabsContainer.style.boxShadow = `0 4px 15px ${categoria.darkColor.replace(/[\d.]+\)$/, '0.2)')}`;

        const tc = document.createElement('div');
        tc.className = 'tabs-content';

        const homeBtn = document.createElement('button');
        homeBtn.className = 'tab-item';
        homeBtn.innerHTML = `<span class="tab-icon"><i class="bi bi-house-fill"></i></span><span class="tab-text">Inicio</span>`;
        _aplicarColorTab(homeBtn, categoria, false);
        homeBtn.addEventListener('mouseenter', function () { _aplicarColorTab(this, categoria, true); });
        homeBtn.addEventListener('mouseleave', function () { _aplicarColorTab(this, categoria, false); });
        homeBtn.onclick = mostrarCategorias;
        tc.appendChild(homeBtn);

        const backBtn = document.createElement('button');
        backBtn.className = 'tab-item';
        backBtn.innerHTML = `<span class="tab-icon"><i class="bi bi-arrow-left"></i></span><span class="tab-text">Salir a ${categoria.nombre}</span>`;
        _aplicarColorTab(backBtn, categoria, false);
        backBtn.addEventListener('mouseenter', function () { _aplicarColorTab(this, categoria, true); });
        backBtn.addEventListener('mouseleave', function () { _aplicarColorTab(this, categoria, false); });
        backBtn.onclick = volverCategorias;
        tc.appendChild(backBtn);

        const sep = document.createElement('div');
        sep.className = 'tab-separator';
        sep.innerHTML = '<i class="bi bi-chevron-right"></i>';
        tc.appendChild(sep);

        categoria.reportes.forEach((rep, idx) => {
            const tab = document.createElement('button');
            tab.className = `tab-item${rep.id === reporteActivoId ? ' active' : ''}`;
            tab.dataset.id = rep.id;
            tab.dataset.categoriaId = categoriaId;
            tab.style.animationDelay = `${idx * 0.07}s`;
            tab.innerHTML = `${categoria.icono.replace('class="', 'class="tab-icon ')}<span class="tab-text">${rep.nombre}</span>`;
            _aplicarColorTab(tab, categoria, rep.id === reporteActivoId);
            tab.addEventListener('mouseenter', function () { if (!this.classList.contains('active')) _aplicarColorTab(this, categoria, true); });
            tab.addEventListener('mouseleave', function () { if (!this.classList.contains('active')) _aplicarColorTab(this, categoria, false); });
            tab.addEventListener('click', e => { e.preventDefault(); cambiarReporte(rep.id, categoriaId); });
            tc.appendChild(tab);
        });

        tabsContainer.appendChild(tc);
        tabsContainer.style.display = 'block';
        cleanupStickyTabs = setupStickyTabs();
    } catch (e) { console.error('Error crearTabs:', e); }
}

function _aplicarColorTab(tab, cat, activo) {
    tab.style.background = activo ? cat.darkColor : 'white';
    tab.style.color = activo ? 'white' : cat.darkColor;
    tab.style.borderColor = activo ? cat.darkColor : cat.lightColor;
    tab.style.fontWeight = activo ? '600' : '500';
}

function setupStickyTabs() {
    if (!tabsContainer) return () => { };
    try {
        tabsContainer.style.position = 'sticky';
        tabsContainer.style.top = '0';
        tabsContainer.style.zIndex = '999';

        const sentinel = document.createElement('div');
        sentinel.id = '_sticky-sentinel';
        sentinel.style.cssText = 'position:absolute;height:1px;visibility:hidden;pointer-events:none;';
        tabsContainer.parentNode.insertBefore(sentinel, tabsContainer);

        const obs = new IntersectionObserver(
            ([e]) => {
                if (!e.isIntersecting) tabsContainer.classList.add('sticky-active');
                else tabsContainer.classList.remove('sticky-active');
            },
            { root: null, threshold: 0 }
        );
        obs.observe(sentinel);

        return () => {
            obs.disconnect();
            if (sentinel.parentNode) sentinel.remove();
            tabsContainer.classList.remove('sticky-active');
            tabsContainer.style.position = '';
            tabsContainer.style.top = '';
            tabsContainer.style.zIndex = '';
        };
    } catch (e) { console.error('Error sticky:', e); return () => { }; }
}

function seleccionarCategoria(id) { toggleCategoriaExpansion(id); }

function seleccionarReporte(reporteId, categoriaId) {
    try {
        const datos = obtenerReportePorIds(categoriaId, reporteId);
        if (!datos) { mostrarCategorias(); return; }
        if (currentReportId === reporteId && currentCategoryId === categoriaId && reportContainer.style.display !== 'none') return;

        currentReportId = reporteId;
        currentCategoryId = categoriaId;
        mostrarLoading();
        if (cleanupStickyTabs) { cleanupStickyTabs(); cleanupStickyTabs = null; }
        cardsContainer.classList.remove('fade-in');
        cardsContainer.classList.add('fade-out');
        crearTabs(categoriaId, reporteId);

        setTimeout(() => {
            try {
                cardsContainer.style.display = 'none';
                reportContainer.style.display = 'block';
                reportContainer.classList.remove('fade-out');
                reportContainer.classList.add('fade-in');
                tabsContainer.style.display = 'block';
                cargarReporte(reporteId, categoriaId);
                ocultarLoading();
            } catch (e) { console.error(e); ocultarLoading(); }
        }, 300);
    } catch (e) { console.error(e); ocultarLoading(); mostrarCategorias(); }
}

function cambiarReporte(reporteId, categoriaId) {
    try {
        if (currentReportId === reporteId && currentCategoryId === categoriaId) return;
        currentCategoryId = categoriaId;
        mostrarLoading();
        reportContainer.classList.remove('fade-in');
        reportContainer.classList.add('fade-out');
        destruirGraficos();
        const cat = obtenerCategoriaPorId(categoriaId);
        if (cat) tabsContainer.querySelectorAll('.tab-item').forEach(t => {
            const activo = parseInt(t.dataset.id) === reporteId && parseInt(t.dataset.categoriaId) === categoriaId;
            t.classList.toggle('active', activo);
            _aplicarColorTab(t, cat, activo);
        });
        setTimeout(() => {
            currentReportId = reporteId;
            cargarReporte(reporteId, categoriaId);
            ocultarLoading();
            reportContainer.classList.remove('fade-out');
            reportContainer.classList.add('fade-in');
        }, 300);
    } catch (e) { console.error(e); ocultarLoading(); }
}

function volverCategorias() {
    try {
        reportContainer.classList.remove('fade-in');
        reportContainer.classList.add('fade-out');
        destruirGraficos();
        if (cleanupStickyTabs) { cleanupStickyTabs(); cleanupStickyTabs = null; }
        if (tabsContainer) { tabsContainer.style.display = 'none'; tabsContainer.classList.remove('sticky-active'); }
        const savedId = currentCategoryId;
        setTimeout(() => {
            reportContainer.style.display = 'none';
            reportContainer.innerHTML = '';
            mostrarCategorias();
            if (savedId) setTimeout(() => toggleCategoriaExpansion(savedId), 480);
        }, 300);
    } catch (e) { console.error(e); mostrarCategorias(); }
}

function cargarReporte(reporteId, categoriaId) {
    const datos = obtenerReportePorIds(categoriaId, reporteId);
    if (!datos) {
        const rc = document.getElementById('reportContainer');
        if (rc) rc.innerHTML = '<div class="alert alert-danger">No se pudo encontrar la configuración del reporte.</div>';
        return;
    }
    
    const { categoria, reporte } = datos;

    // Si la función ya existe en el entorno global, la ejecutamos
    if (typeof window[reporte.funcion] === 'function') {
        _ejecutarCargaReporte(reporte, categoria);
        return;
    }

    // Si no existe, intentamos cargar el archivo .js definido en la configuración
    if (reporte.archivo) {
        mostrarLoading();
        const script = document.createElement('script');
        // La ruta es relativa a app/vista/reporte.php
        script.src = `../../assets/js/Reporte/${reporte.archivo}?v=${Date.now()}`;
        
        script.onload = () => {
            setTimeout(() => {
                if (typeof window[reporte.funcion] === 'function') {
                    ocultarLoading();
                    _ejecutarCargaReporte(reporte, categoria);
                } else {
                    ocultarLoading();
                    const contentDiv = document.getElementById('reportContent');
                    if (contentDiv) {
                        contentDiv.innerHTML = `<div class="alert alert-warning">El script se cargó pero la función <strong>${reporte.funcion}</strong> no está disponible.</div>`;
                    }
                }
            }, 100);
        };

        script.onerror = () => {
            ocultarLoading();
            const contentDiv = document.getElementById('reportContent');
            if (contentDiv) {
                contentDiv.innerHTML = `<div class="alert alert-danger">No se pudo cargar el archivo <strong>${reporte.archivo}</strong>.</div>`;
            }
        };

        document.head.appendChild(script);
    } else {
        // Si no hay función ni archivo, mostramos error
        const contentDiv = document.getElementById('reportContent');
        if (contentDiv) {
            contentDiv.innerHTML = `<div class="alert alert-danger">La función <strong>${reporte.funcion}</strong> no está disponible y no se especificó un archivo para carga diferida.</div>`;
        }
    }
}

function _ejecutarCargaReporte(reporte, categoria) {
    const reportContainer = document.getElementById('reportContainer');
    if (!reportContainer) return;

    reportContainer.innerHTML = '';

    const title = document.createElement('h2');
    title.className = `text-${categoria.color} fw-bold mb-3 report-title`;
    title.style.background = 'transparent';
    title.innerHTML = `${categoria.icono} ${reporte.nombre}`;
    reportContainer.appendChild(title);

    const contentDiv = document.createElement('div');
    contentDiv.id = 'reportContent';
    contentDiv.className = 'fade-in';
    // Placeholder de carga para evitar pantalla en blanco mientras el reporte inicializa
    contentDiv.innerHTML = `
        <div class="rp-skeleton-wrapper" style="border:none; box-shadow:none; padding:0; background:transparent;">
            <div class="rp-skeleton-kpis">
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
                <div class="rp-skeleton-kpi rp-skeleton-pulse"></div>
            </div>
            <div class="rp-skeleton-grid mt-4">
                <div class="rp-skeleton-main rp-skeleton-pulse"></div>
                <div class="rp-skeleton-side rp-skeleton-pulse"></div>
            </div>
        </div>`;

    contentDiv.style.marginTop = '20px';

    const toolbar = document.createElement('div');
    toolbar.className = 'rp-toolbar';
    toolbar.innerHTML = `
        <div class="rp-toolbar-group">
            <span class="rp-toolbar-label">Acciones del reporte</span>
        </div>
        <div class="rp-toolbar-group">
            <button type="button" id="btn-print-report" class="rp-btn rp-btn--cyan">
                <i class="bi bi-printer-fill"></i> Imprimir PDF
            </button>
            <button type="button" id="btn-back-to-categories" class="rp-btn rp-btn--ghost">
                <i class="bi bi-arrow-left-circle"></i> Volver a reportes
            </button>
        </div>`;
    reportContainer.appendChild(toolbar);

    reportContainer.appendChild(contentDiv);

    const printButton = toolbar.querySelector('#btn-print-report');
    if (printButton) {
        printButton.addEventListener('click', () => {
            if (reporte.id === 4) {
                // Generar PDF backend para "Cursos con más Participantes"
                let chartBase64 = '';
                if (typeof cmpChart !== 'undefined' && cmpChart) {
                    chartBase64 = cmpChart.toBase64Image();
                }
                const anio = typeof anioCmp !== 'undefined' ? anioCmp : new Date().getFullYear();
                
                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteCursoMasParticipantes.php', {
                    anio: anio,
                    chart_image: chartBase64
                });
            } else if (reporte.id === 1) {
                // Generar PDF backend para "Total de Participantes"
                let chartBase64 = '';
                if (typeof graficoInstance !== 'undefined' && graficoInstance) {
                    chartBase64 = graficoInstance.toBase64Image();
                }
                const anio = typeof anioBase !== 'undefined' ? anioBase : new Date().getFullYear();
                
                let resumenKpi = null;
                if (typeof cacheDatosAnuales !== 'undefined' && typeof estadosFiltrados !== 'undefined' && typeof calcularResumenFiltrado === 'function') {
                    const key = `${anio}_${estadosFiltrados.join(',')}`;
                    if (cacheDatosAnuales[key]) {
                        resumenKpi = calcularResumenFiltrado(cacheDatosAnuales[key]);
                    }
                }

                let panelData = {
                    tipo: typeof panelActivo !== 'undefined' ? panelActivo : null,
                    meses: typeof obtenerNombresMeses === 'function' ? obtenerNombresMeses() : [],
                    anios: typeof añosGlobales !== 'undefined' ? [...añosGlobales].sort((a,b)=>a-b) : [],
                    valores: {}
                };

                if (panelData.tipo && panelData.tipo !== 'total_sistema' && typeof cacheDatosAnuales !== 'undefined' && typeof valorFiltrado === 'function') {
                    const campo = { nuevos: 'nuevos', activos: 'activos', retirados: 'retirados', retencion: 'retencion' }[panelData.tipo];
                    panelData.anios.forEach(a => {
                        const d = cacheDatosAnuales[`${a}_${typeof estadosFiltrados !== 'undefined' ? estadosFiltrados.join(',') : ''}`] || [];
                        panelData.valores[a] = [];
                        for(let i=0; i<12; i++) {
                            const mesDatos = d[i];
                            if (!mesDatos || mesDatos.nuevos === null) {
                                panelData.valores[a].push(null);
                                continue;
                            }
                            if (panelData.tipo === 'retencion') {
                                const act = valorFiltrado(mesDatos, 'activos');
                                const ret = valorFiltrado(mesDatos, 'retirados');
                                if (act === null || act === 0) panelData.valores[a].push(null);
                                else panelData.valores[a].push(((act - ret) / act * 100).toFixed(1) + '%');
                            } else {
                                panelData.valores[a].push(valorFiltrado(mesDatos, campo));
                            }
                        }
                    });
                }

                let tablaMensual = [];
                const cacheKey = `${anio}_${estadosFiltrados.join(',')}`;
                if (typeof cacheDatosAnuales !== 'undefined' && cacheDatosAnuales[cacheKey]) {
                    const rawData = cacheDatosAnuales[cacheKey];
                    tablaMensual = rawData.map((m, idx) => {
                        const sd = m.nuevos === null;
                        return {
                            mes: m.mes,
                            mesNumero: m.mesNumero ?? (idx + 1),
                            nuevos: sd ? null : (typeof valorFiltrado === 'function' ? valorFiltrado(m, 'nuevos') : m.nuevos),
                            activos: sd ? null : (typeof valorFiltrado === 'function' ? valorFiltrado(m, 'activos') : m.activos),
                            retirados: sd ? null : (typeof valorFiltrado === 'function' ? valorFiltrado(m, 'retirados') : m.retirados),
                            totalAcumulado: sd ? null : m.totalAcumulado
                        };
                    });
                }

                const payload = {
                    anioBase: anio,
                    anioTabla: typeof anioTabla !== 'undefined' ? anioTabla : anio,
                    estadosFiltrados: typeof estadosFiltrados !== 'undefined' ? estadosFiltrados : [],
                    filtroDemo: typeof filtroDemo !== 'undefined' ? filtroDemo : { genero: [], discapacidad: [], edadUmbral: [] },
                    resumen: resumenKpi,
                    datosEstadosSistema: typeof datosEstadosSistema !== 'undefined' ? datosEstadosSistema : { total: 0, estados: {} },
                    panelData: panelData,
                    tablaMensual: tablaMensual
                };

                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteTotalParticipantes.php', {
                    chart_image: chartBase64,
                    payload_json: payload
                });
            } else if (reporte.id === 2) {
                // Generar PDF backend para "Participantes Aprobados"
                let chartBase64 = '';
                if (typeof graficoAprobInstance !== 'undefined' && graficoAprobInstance) {
                    chartBase64 = graficoAprobInstance.toBase64Image();
                }
                
                let chartCursosBase64 = '';
                if (typeof instanciasGraficosCursos !== 'undefined' && instanciasGraficosCursos.length > 0 && instanciasGraficosCursos[0].instance) {
                    chartCursosBase64 = instanciasGraficosCursos[0].instance.toBase64Image();
                }

                const anio = typeof anioAprobTabla !== 'undefined' ? anioAprobTabla : new Date().getFullYear();
                
                let resumenKpi = null;
                if (typeof calcularResumenAprob === 'function' && typeof datosAprobMensuales !== 'undefined') {
                    resumenKpi = calcularResumenAprob(datosAprobMensuales);
                }

                let panelData = {
                    tipo: typeof panelAprobActivo !== 'undefined' ? panelAprobActivo : null,
                    meses: typeof obtenerNombresMesesAprob === 'function' ? obtenerNombresMesesAprob() : [],
                    anios: typeof añosAprobGlobales !== 'undefined' ? [...añosAprobGlobales].sort((a,b)=>a-b) : [],
                    valores: {}
                };

                if (panelData.tipo && typeof cacheAprobAnuales !== 'undefined') {
                    const campoData = panelData.tipo === 'tasa' ? 'tasaAprobacion' : panelData.tipo;
                    panelData.anios.forEach(a => {
                        const d = cacheAprobAnuales[a] || [];
                        panelData.valores[a] = [];
                        for(let i=0; i<12; i++) {
                            const mesDatos = d[i];
                            if (!mesDatos || mesDatos.aprobados === null) {
                                panelData.valores[a].push(null);
                                continue;
                            }
                            if (panelData.tipo === 'tasa') {
                                panelData.valores[a].push(mesDatos.tasaAprobacion + '%');
                            } else {
                                panelData.valores[a].push(mesDatos[campoData]);
                            }
                        }
                    });
                }
                
                let tablaMensual = typeof filtrarMesesPorTiempo === 'function' && typeof cacheAprobAnuales !== 'undefined' && cacheAprobAnuales[anio] 
                    ? filtrarMesesPorTiempo(cacheAprobAnuales[anio]) 
                    : [];

                const payload = {
                    anioTabla: anio,
                    filtroTiempo: typeof filtroAprobTiempo !== 'undefined' ? filtroAprobTiempo : { tipo: 'anual', anio },
                    resumen: resumenKpi,
                    panelData: panelData,
                    tablaMensual: tablaMensual,
                    cursosSel: typeof cursosAprobSel !== 'undefined' && typeof cursosAprobDisp !== 'undefined' 
                        ? cursosAprobDisp.filter(c => cursosAprobSel.includes(c.id)) 
                        : []
                };

                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteParticipantesAprobados.php', {
                    chart_image: chartBase64,
                    chart_cursos_image: chartCursosBase64,
                    payload_json: payload
                });
            } else if (reporte.id === 9) {
                // Generar PDF backend para "Participantes por Ámbito" con Dompdf
                let chartBase64 = '';
                if (typeof graficoAmbito !== 'undefined' && graficoAmbito) {
                    chartBase64 = graficoAmbito.toBase64Image();
                }

                const anio = typeof document.getElementById('selectAnioAmbito') !== 'undefined' && document.getElementById('selectAnioAmbito') 
                    ? parseInt(document.getElementById('selectAnioAmbito').value) || new Date().getFullYear()
                    : new Date().getFullYear();

                const selectAmbito = document.getElementById('selectAmbitoEspecifico');
                const ambitoFiltro = selectAmbito ? selectAmbito.value : 'todos';

                const payload = {
                    anio: anio,
                    ambitoFiltro: ambitoFiltro,
                    datosAmbito: typeof datosAmbito !== 'undefined' ? datosAmbito : [],
                    datosAmbitoAnioAnterior: typeof datosAmbitoAnioAnterior !== 'undefined' ? datosAmbitoAnioAnterior : []
                };

                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteParticipantesAmbito.php', {
                    chart_image: chartBase64,
                    payload_json: payload
                });
            } else if (reporte.id === 3) {
                // Generar PDF backend para "Total de Cursos"
                let chartBase64 = '';
                if (typeof graficoCursosInstance !== 'undefined' && graficoCursosInstance) {
                    chartBase64 = graficoCursosInstance.toBase64Image();
                }
                const anio = typeof anioTablaCursos !== 'undefined' ? anioTablaCursos : new Date().getFullYear();
                
                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteTotalCursosMensual.php', {
                    anio: anio,
                    chart_image: chartBase64
                });
            } else if (reporte.id === 5) {
                // Generar PDF backend para "Curso más Requerido"
                const datos = window.datosCompletosRQ;
                if (!datos || datos.length === 0) {
                    alert('El reporte aún está cargando. Por favor espera a que se muestren los datos y vuelve a intentarlo.');
                    return;
                }

                let chartBase64 = '';
                if (window.cursoRequeridoChart) {
                    chartBase64 = window.cursoRequeridoChart.toBase64Image();
                }
                const anio = window.anioRQ || new Date().getFullYear();
                const datosJson = JSON.stringify(datos);

                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteCursoMasRequerido.php', {
                    anio: anio,
                    chart_image: chartBase64,
                    datos_json: datosJson
                });
            } else if (reporte.id === 13) {
                // Generar PDF backend para "Trayectoria de Cursos"
                const filtros = window.curTrayFiltros || { anio: '', modalidad: '', estatus: '', tipoPeriodo: '', subPeriodo: '' };
                const busqueda = window.curTrayBusqueda || '';
                const cursoSeleccionado = window.curTrayCursoAct || '';
                
                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteCursoTrayectoria.php', {
                    anio: filtros.anio || '',
                    modalidad: filtros.modalidad || '',
                    estatus: filtros.estatus || '',
                    busqueda: busqueda || '',
                    tipoPeriodo: filtros.tipoPeriodo || '',
                    subPeriodo: filtros.subPeriodo || '',
                    cursoSeleccionado: cursoSeleccionado || ''
                });
            } else if (reporte.id === 10) {
                // Generar PDF backend para "Total de Docentes"
                let chartBase64 = '';
                if (window.docGrafico) {
                    chartBase64 = window.docGrafico.toBase64Image();
                }
                const anioBase = window.docAnioBase || new Date().getFullYear();
                const anioTabla = window.docAnioTabla || anioBase;
                const aniosGrafico = window.docAniosGrafico || [anioBase];
                
                postAndOpenPdf('../controlador/fpdf/Reporte/ReporteTotalDocentes.php', {
                    chart_image: chartBase64,
                    anio_base: anioBase,
                    anio_tabla: anioTabla,
                    anios_grafico: JSON.stringify(aniosGrafico)
                });
            } else if (reporte.id === 12) {
                if (window.trayExportarPdf) {
                    window.trayExportarPdf();
                }
            } else if (reporte.id === 11) {
                if (window.exportarPdfMetasPorAmbito) {
                    window.exportarPdfMetasPorAmbito();
                }
            } else if (reporte.id === 6) {
                if (window.exportarPdfCargaHoraria) {
                    window.exportarPdfCargaHoraria();
                }
            } else if (reporte.id === 15) {
                if (window.exportarPdfHistorialDocente) {
                    window.exportarPdfHistorialDocente();
                }
            } else if (reporte.id === 16) {
                if (window.exportarPdfHistoricoMetas) {
                    window.exportarPdfHistoricoMetas();
                }
            } else if (reporte.id === 17) {
                if (window.exportarPdfDetalleMeta) {
                    window.exportarPdfDetalleMeta();
                }
            } else {
                const prevTitle = document.title;
                document.title = `Reporte - ${reporte.nombre}`;
                window.print();
                setTimeout(() => { document.title = prevTitle; }, 800);
            }
        });
    }

    const backButton = toolbar.querySelector('#btn-back-to-categories');
    if (backButton) {
        backButton.addEventListener('click', volverCategorias);
    }

    setTimeout(() => {
        try {
            // Ejecutar la función del reporte. Se espera que modifique #reportContent.
            const result = window[reporte.funcion]();
            // Si la función devuelve una promesa, esperar a su resolución para quitar el loader
            if (result && typeof result.then === 'function') {
                result.then(() => {
                    // si quedó vacío, mostrar nota
                    const rc = document.getElementById('reportContent');
                    if (rc && rc.innerHTML.trim() === '') {
                        rc.innerHTML = `<div class="alert alert-warning">El reporte se cargó correctamente pero no generó contenido.</div>`;
                    }
                }).catch(err => {
                    console.error(`Error Promise en ${reporte.funcion}:`, err);
                    const rc = document.getElementById('reportContent');
                    if (rc) rc.innerHTML = `<div class="alert alert-danger">Error al cargar el reporte.</div>`;
                });
            } else {
                // Si no devuelve promesa, verificar tras un breve retraso si dejó contenido
                setTimeout(() => {
                    const rc = document.getElementById('reportContent');
                    if (rc && rc.innerHTML.trim() === '') {
                        rc.innerHTML = `<div class="alert alert-warning">El reporte no generó contenido. Intenta recargar la página o selecciona otro reporte.</div>`;
                    }
                }, 900);
            }
        } catch (e) {
            console.error(`Error al ejecutar ${reporte.funcion}:`, e);
            contentDiv.innerHTML = `<div class="alert alert-danger">Error al ejecutar el reporte: ${e.message}</div>`;
        }
    }, 50);
}

function destruirGraficos() {
    try { Object.values(chartInstances).forEach(c => { if (c?.destroy) c.destroy(); }); chartInstances = {}; } catch (e) { }
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => { try { init(); } catch (e) { mostrarErrorInicial('Error crítico'); } }, 100);
});
if (document.readyState === 'complete') {
    setTimeout(() => { try { init(); } catch (e) { mostrarErrorInicial('Error crítico'); } }, 100);
}

window.obtenerAñosDisponibles = function () { const y = new Date().getFullYear(); return [y, y - 1, y - 2, y - 3]; };

window.mostrarCategorias = mostrarCategorias;
window.volverCategorias = volverCategorias;
window.seleccionarCategoria = seleccionarCategoria;
window.seleccionarReporte = seleccionarReporte;
window.mostrarReportesCategoria = mostrarReportesCategoria;
window.toggleCategoriaExpansion = toggleCategoriaExpansion;
window.cerrarExpansion = cerrarExpansion;
window.cargarReporte = cargarReporte;
window.cambiarReporte = cambiarReporte;
window.crearTabs = crearTabs;
window.destruirGraficos = destruirGraficos;
window.init = init;
window.categoriasReportes = categoriasReportes;
window.obtenerCategoriaPorId = obtenerCategoriaPorId;
window.obtenerReportePorIds = obtenerReportePorIds;
window.mostrarCategoriasForzado = mostrarCategoriasForzado;
window.mostrarErrorInicial = mostrarErrorInicial;


function mostrarReportesCategoria(cat) {
    try {
        if (!cat?.id) { mostrarCategorias(); return; }
        mostrarCategorias();
        setTimeout(() => toggleCategoriaExpansion(cat.id), 450);
    } catch (e) { mostrarCategorias(); }
}
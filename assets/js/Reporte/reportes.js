const categoriasReportes = [
    {
        id: 1, nombre: "Participantes",
        icono: `<i class="bi bi-people-fill"></i>`, color: "primary",
        bgColor: "rgba(13,110,253,0.9)", lightColor: "rgba(13,110,253,0.5)",
        darkColor: "rgba(0,80,200,0.95)", bodyBgColor: "rgba(13,110,253,0.06)",
        reportes: [
            { id: 1, nombre: "Total de Participantes",   funcion: 'cargarTotalParticipantes' },
            { id: 2, nombre: "Participantes Aprobados",  funcion: 'cargarParticipantesAprobados' },
            { id: 9, nombre: "Participantes por Ámbito", funcion: 'cargarParticipantesPorAmbito' }
        ]
    },
    {
        id: 2, nombre: "Cursos",
        icono: `<i class="bi bi-book-half"></i>`, color: "success",
        bgColor: "rgba(25,135,84,0.9)", lightColor: "rgba(25,135,84,0.5)",
        darkColor: "rgba(15,100,60,0.95)", bodyBgColor: "rgba(25,135,84,0.06)",
        reportes: [
            { id: 3, nombre: "Total de Cursos",              funcion: 'cargarTotalCursos' },
            { id: 4, nombre: "Cursos con más Participantes", funcion: 'cargarCursosMasParticipantes' },
            { id: 5, nombre: "Curso más Requerido",          funcion: 'cargarCursoMasRequerido' }
        ]
    },
    {
        id: 3, nombre: "Docentes",
        icono: `<i class="bi bi-person-workspace"></i>`, color: "warning",
        bgColor: "rgba(255,193,7,0.9)", lightColor: "rgba(255,193,7,0.5)",
        darkColor: "rgba(200,150,0,0.95)", bodyBgColor: "rgba(255,193,7,0.07)",
        reportes: [
            { id: 10, nombre: "Total de Docentes",        funcion: 'cargarTotalDocentes' },
            { id: 6,  nombre: "Docentes más Solicitados", funcion: 'cargarDocentesMasSolicitados' },
            { id: 7,  nombre: "Docentes Inactivos",       funcion: 'cargarDocentesInactivos' }
        ]
    },
    {
        id: 4, nombre: "Metas",
        icono: `<i class="bi bi-bullseye"></i>`, color: "danger",
        bgColor: "rgba(220,53,69,0.9)", lightColor: "rgba(220,53,69,0.5)",
        darkColor: "rgba(180,35,50,0.95)", bodyBgColor: "rgba(220,53,69,0.06)",
        reportes: [
            { id: 8,  nombre: "Generar Constancias", funcion: 'cargarGenerarConstancias' },
            { id: 11, nombre: "Meta por Ámbito",     funcion: 'cargarMetaPorAmbito' }
        ]
    }
];

const cardsContainer  = document.getElementById('cardsContainer');
const reportContainer = document.getElementById('reportContainer');
const loadingOverlay  = document.getElementById('loadingOverlay');
let tabsContainer     = null;
let currentCategoryId = null;
let currentReportId   = null;
let chartInstances    = {};
let cleanupStickyTabs = null;
let buscador          = null;
let categoriaActivaId = null;

function getColorFromCategory(colorClass) {
    const map = {
        primary: '#0d6efd', success: '#198754', warning: '#ffc107',
        danger: '#dc3545',  info: '#0dcaf0',    secondary: '#6c757d'
    };
    return map[colorClass] || '#6c757d';
}

function aplicarRippleCard(element, event, colorHex) {
    element.querySelectorAll('.rp-card-ripple').forEach(r => r.remove());
    const rect    = element.getBoundingClientRect();
    const x       = event.clientX - rect.left;
    const y       = event.clientY - rect.top;
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

function mostrarLoading() {
    const o = document.getElementById('loadingOverlay');
    if (o) { o.classList.add('active'); o.style.display = 'flex'; }
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
            if (!cat.icono)       cat.icono       = '<i class="bi bi-card-text"></i>';
            if (!cat.color)       cat.color       = 'primary';
            if (!cat.bgColor)     cat.bgColor     = 'rgba(13,110,253,0.9)';
            if (!cat.lightColor)  cat.lightColor  = 'rgba(13,110,253,0.5)';
            if (!cat.darkColor)   cat.darkColor   = 'rgba(0,80,200,0.95)';
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

function init() {
    if (!validarEstructuraDatos()) { mostrarErrorInicial('Estructura de datos inválida'); return; }
    try {
        if (typeof BuscadorGlobal === 'function') {
            buscador = new BuscadorGlobal(categoriasReportes);
        }
        crearTabsContainer();
        mostrarCategorias();
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

function mostrarCategoriasForzado() { try { mostrarCategorias(); } catch(e){} }

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

        currentReportId   = null;
        currentCategoryId = null;
        categoriaActivaId = null;

        cardsContainer.style.display  = 'block';
        cardsContainer.innerHTML      = '';
        cardsContainer.className      = 'mt-0 pt-0';
        cardsContainer.style.position = 'relative';

        const title = document.createElement('h1');
        title.className   = 'text-center mb-3 fw-bold text-primary rp-main-title';
        title.textContent = 'Reportes del Sistema';
        cardsContainer.appendChild(title);

        const searchSection = document.createElement('div');
        searchSection.className = 'rp-search-section rp-search-section--hero';
        const buscadorEl = crearBuscadorInicial();
        if (buscadorEl) searchSection.appendChild(buscadorEl);
        cardsContainer.appendChild(searchSection);

        const subtitle = document.createElement('p');
        subtitle.className   = 'text-center text-muted mb-3 rp-main-subtitle';
        subtitle.textContent = 'Selecciona una categoría para explorar los reportes disponibles';
        cardsContainer.appendChild(subtitle);

        const row = document.createElement('div');
        row.id        = 'rp-category-grid';
        row.className = 'row justify-content-center g-4 mb-0 mt-2';

        categoriasReportes.forEach(categoria => {
            const color = getColorFromCategory(categoria.color);

            const col = document.createElement('div');
            col.className     = 'col-md-6 col-lg-3 report-card-container';
            col.dataset.catId = categoria.id;

            const card = document.createElement('div');
            card.className  = `card card-report shadow h-100 border-0 rp-nav-card rp-nav-card--category hover-categoria-${categoria.color}`;
            card.id         = `cat-card-${categoria.id}`;
            card.dataset.id = categoria.id;

            card.innerHTML = `
                <div class="card-body text-center d-flex flex-column justify-content-center p-4">
                    <div class="rp-cat-icon mb-3 text-${categoria.color}">${categoria.icono}</div>
                    <h3 class="rp-cat-name fw-bold text-${categoria.color} mb-2">${categoria.nombre}</h3>
                    <p class="rp-cat-count text-muted mb-0">
                        ${categoria.reportes.length} reporte${categoria.reportes.length !== 1 ? 's' : ''} disponible${categoria.reportes.length !== 1 ? 's' : ''}
                    </p>
                </div>`;

            col.appendChild(card);
            col.addEventListener('click', (e) => {
                aplicarRippleCard(card, e, color);
                toggleCategoriaExpansion(categoria.id);
            });

            row.appendChild(col);
        });

        cardsContainer.appendChild(row);

        const expansionArea = document.createElement('div');
        expansionArea.id        = 'rp-expansion-area';
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

    document.querySelectorAll('.rp-nav-card--category').forEach(c => {
        c.classList.remove('active-category');
        c.style.setProperty('--active-border', 'transparent');
    });

    const catCard = document.getElementById(`cat-card-${categoriaId}`);
    if (catCard) {
        catCard.classList.add('active-category');
        catCard.style.setProperty('--active-border', getColorFromCategory(categoria.color));
    }

    categoriaActivaId = categoriaId;
    const area = document.getElementById('rp-expansion-area');

    if (esCambioDirecto && area) {
        area.style.transition = 'opacity 0.15s ease';
        area.style.opacity    = '0';
        setTimeout(() => mostrarExpansionReportes(categoria, true), 150);
    } else {
        mostrarExpansionReportes(categoria, false);
    }
}

function cerrarExpansion() {
    categoriaActivaId = null;
    document.querySelectorAll('.rp-nav-card--category').forEach(c => {
        c.classList.remove('active-category');
        c.style.setProperty('--active-border', 'transparent');
    });
    const area = document.getElementById('rp-expansion-area');
    if (area) {
        area.style.transition = 'opacity 0.2s ease, max-height 0.28s ease';
        area.style.opacity    = '0';
        area.style.maxHeight  = '0';
        setTimeout(() => { area.innerHTML = ''; area.style.cssText = ''; }, 300);
    }
}

function mostrarExpansionReportes(categoria, esSwap = false) {
    const area = document.getElementById('rp-expansion-area');
    if (!area) return;

    const color = getColorFromCategory(categoria.color);

    if (!esSwap) {
        area.innerHTML        = '';
        area.style.cssText    = '';
        area.style.opacity    = '0';
        area.style.maxHeight  = '0';
        area.style.overflow   = 'hidden';
        area.style.transition = 'none';
    } else {
        area.innerHTML = '';
    }

    const inner = document.createElement('div');
    inner.className       = 'rp-expansion-inner';
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
    chain.id        = 'rp-reports-chain';

    categoria.reportes.forEach(reporte => {
        const node = document.createElement('div');
        node.className     = 'rp-report-node';
        node.id            = `report-node-${reporte.id}`;
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
            area.style.opacity    = '1';
            setTimeout(() => animarNodos(chain), 60);
        });
    } else {
        requestAnimationFrame(() => requestAnimationFrame(() => {
            area.style.transition = 'opacity 0.28s ease, max-height 0.35s cubic-bezier(0.4,0,0.2,1)';
            area.style.opacity    = '1';
            area.style.maxHeight  = '700px';
            setTimeout(() => { area.style.overflow = 'visible'; }, 360);
            setTimeout(() => animarNodos(chain), 180);
        }));
    }
}

function animarNodos(chain) {
    Array.from(chain.querySelectorAll('.rp-report-node')).forEach((node, idx) => {
        setTimeout(() => {
            node.style.transition = 'opacity 270ms ease, transform 290ms cubic-bezier(0.34,1.56,0.64,1)';
            node.style.opacity    = '1';
            node.style.transform  = 'translateY(0) scale(1)';
        }, idx * 65);
    });
}

function crearBuscadorInicial() {
    if (!buscador) return null;
    return buscador.crearBuscador({
        placeholder: 'Busca un reporte, participante, curso o docente...',
        onReporte:   (repId, catId) => seleccionarReporte(repId, catId)
    });
}

function crearTabs(categoriaId, reporteActivoId) {
    try {
        if (!tabsContainer) crearTabsContainer();
        tabsContainer.innerHTML = '';
        const categoria = obtenerCategoriaPorId(categoriaId);
        if (!categoria?.reportes?.length) return;

        tabsContainer.style.background = categoria.lightColor.replace(/[\d.]+\)$/, '0.15)');
        tabsContainer.style.border     = `2px solid ${categoria.darkColor}`;
        tabsContainer.style.boxShadow  = `0 4px 15px ${categoria.darkColor.replace(/[\d.]+\)$/, '0.2)')}`;

        const tc = document.createElement('div');
        tc.className = 'tabs-content';

        const homeBtn = document.createElement('button');
        homeBtn.className = 'tab-item';
        homeBtn.innerHTML = `<span class="tab-icon"><i class="bi bi-house-fill"></i></span><span class="tab-text">Inicio</span>`;
        _aplicarColorTab(homeBtn, categoria, false);
        homeBtn.addEventListener('mouseenter', function() { _aplicarColorTab(this, categoria, true); });
        homeBtn.addEventListener('mouseleave', function() { _aplicarColorTab(this, categoria, false); });
        homeBtn.onclick = mostrarCategorias;
        tc.appendChild(homeBtn);

        const backBtn = document.createElement('button');
        backBtn.className = 'tab-item';
        backBtn.innerHTML = `<span class="tab-icon"><i class="bi bi-arrow-left"></i></span><span class="tab-text">Salir a ${categoria.nombre}</span>`;
        _aplicarColorTab(backBtn, categoria, false);
        backBtn.addEventListener('mouseenter', function() { _aplicarColorTab(this, categoria, true); });
        backBtn.addEventListener('mouseleave', function() { _aplicarColorTab(this, categoria, false); });
        backBtn.onclick = volverCategorias;
        tc.appendChild(backBtn);

        const sep = document.createElement('div');
        sep.className = 'tab-separator';
        sep.innerHTML = '<i class="bi bi-chevron-right"></i>';
        tc.appendChild(sep);

        categoria.reportes.forEach((rep, idx) => {
            const tab = document.createElement('button');
            tab.className            = `tab-item${rep.id === reporteActivoId ? ' active' : ''}`;
            tab.dataset.id           = rep.id;
            tab.dataset.categoriaId  = categoriaId;
            tab.style.animationDelay = `${idx * 0.07}s`;
            tab.innerHTML = `${categoria.icono.replace('class="', 'class="tab-icon ')}<span class="tab-text">${rep.nombre}</span>`;
            _aplicarColorTab(tab, categoria, rep.id === reporteActivoId);
            tab.addEventListener('mouseenter', function() { if (!this.classList.contains('active')) _aplicarColorTab(this, categoria, true); });
            tab.addEventListener('mouseleave', function() { if (!this.classList.contains('active')) _aplicarColorTab(this, categoria, false); });
            tab.addEventListener('click', e => { e.preventDefault(); cambiarReporte(rep.id, categoriaId); });
            tc.appendChild(tab);
        });

        tc.appendChild(_crearBuscadorTab(categoria));
        tabsContainer.appendChild(tc);
        tabsContainer.style.display = 'block';
        cleanupStickyTabs = setupStickyTabs();
    } catch (e) { console.error('Error crearTabs:', e); }
}

function _aplicarColorTab(tab, cat, activo) {
    tab.style.background  = activo ? cat.darkColor : 'white';
    tab.style.color       = activo ? 'white' : cat.darkColor;
    tab.style.borderColor = activo ? cat.darkColor : cat.lightColor;
    tab.style.fontWeight  = activo ? '600' : '500';
}

function _crearBuscadorTab(categoria) {
    const wrapper = document.createElement('div');
    wrapper.className = 'tab-search-wrapper';

    if (buscador?.crearBuscadorTabs) {
        const el = buscador.crearBuscadorTabs(
            categoria,
            (repId, catId) => seleccionarReporte(repId, catId)
        );
        wrapper.appendChild(el);
    }

    return wrapper;
}

function setupStickyTabs() {
    if (!tabsContainer) return () => {};
    let ph = null, isSticky = false;
    try {
        ph = document.createElement('div');
        ph.id = '_tabs-placeholder';
        ph.style.cssText = 'display:none;pointer-events:none;';
        tabsContainer.parentNode.insertBefore(ph, tabsContainer.nextSibling);

        const sentinel = document.createElement('div');
        sentinel.id = '_sticky-sentinel';
        sentinel.style.cssText = 'height:1px;visibility:hidden;pointer-events:none;';
        tabsContainer.parentNode.insertBefore(sentinel, tabsContainer);

        const activate = () => {
            if (isSticky) return;
            isSticky = true;
            ph.style.height = ph.style.display = '';
            ph.style.height = tabsContainer.offsetHeight + 'px';
            ph.style.display = 'block';
            tabsContainer.style.cssText += `position:fixed!important;top:0!important;left:0!important;width:${window.innerWidth}px!important;z-index:9999!important;border-radius:0!important;margin:0!important;`;
            tabsContainer.classList.add('sticky-active');
        };

        const deactivate = () => {
            if (!isSticky) return;
            isSticky = false;
            ph.style.display = 'none';
            ph.style.height  = '0';
            tabsContainer.style.position = tabsContainer.style.top = tabsContainer.style.left =
            tabsContainer.style.width = tabsContainer.style.zIndex =
            tabsContainer.style.borderRadius = tabsContainer.style.margin = '';
            tabsContainer.classList.remove('sticky-active');
        };

        const obs = new IntersectionObserver(
            ([e]) => e.isIntersecting ? deactivate() : activate(),
            { root: null, threshold: 0 }
        );
        obs.observe(sentinel);

        return () => {
            obs.disconnect();
            deactivate();
            if (sentinel.parentNode) sentinel.remove();
            if (ph.parentNode) ph.remove();
        };
    } catch (e) { console.error('Error sticky:', e); return () => {}; }
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
                cardsContainer.style.display  = 'none';
                reportContainer.style.display = 'block';
                reportContainer.innerHTML     = '';
                tabsContainer.style.display   = 'block';
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
            reportContainer.innerHTML = '';
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
            reportContainer.innerHTML     = '';
            mostrarCategorias();
            if (savedId) setTimeout(() => toggleCategoriaExpansion(savedId), 480);
        }, 300);
    } catch (e) { console.error(e); mostrarCategorias(); }
}

function cargarReporte(reporteId, categoriaId) {
    try {
        const datos = obtenerReportePorIds(categoriaId, reporteId);
        if (!datos) {
            reportContainer.innerHTML = '<div class="alert alert-danger">No se pudo cargar el reporte.</div>';
            return;
        }
        const { categoria, reporte } = datos;
        reportContainer.innerHTML = '';

        const backBtn = document.createElement('button');
        backBtn.className = 'btn btn-outline-primary btn-back mb-3 d-md-none';
        backBtn.innerHTML = `<i class="bi bi-arrow-left me-2"></i>Volver a ${categoria.nombre}`;
        backBtn.onclick   = volverCategorias;
        reportContainer.appendChild(backBtn);

        const title = document.createElement('h2');
        title.className        = `text-${categoria.color} fw-bold mb-3 report-title`;
        title.style.background = 'transparent';
        title.innerHTML        = `${categoria.icono} ${reporte.nombre}`;
        reportContainer.appendChild(title);

        const contentDiv = document.createElement('div');
        contentDiv.id        = 'reportContent';
        contentDiv.className = 'fade-in';
        contentDiv.style.marginTop = tabsContainer?.classList.contains('sticky-active') ? '80px' : '20px';
        reportContainer.appendChild(contentDiv);

        if (typeof window[reporte.funcion] !== 'function') {
            contentDiv.innerHTML = `<div class="alert alert-danger">La función <strong>${reporte.funcion}</strong> no está disponible.</div>`;
            return;
        }
        setTimeout(() => {
            try { window[reporte.funcion](); }
            catch (e) { contentDiv.innerHTML = `<div class="alert alert-danger">Error al cargar el reporte.<br><small>${e.message}</small></div>`; }
        }, 10);
    } catch (e) {
        reportContainer.innerHTML = '<div class="alert alert-danger">Error crítico al cargar el reporte.</div>';
    }
}

function destruirGraficos() {
    try { Object.values(chartInstances).forEach(c => { if (c?.destroy) c.destroy(); }); chartInstances = {}; } catch(e){}
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => { try { init(); } catch(e) { mostrarErrorInicial('Error crítico'); } }, 100);
});
if (document.readyState === 'complete') {
    setTimeout(() => { try { init(); } catch(e) { mostrarErrorInicial('Error crítico'); } }, 100);
}

window.obtenerAñosDisponibles = function() { const y = new Date().getFullYear(); return [y, y-1, y-2, y-3]; };

window.mostrarCategorias        = mostrarCategorias;
window.volverCategorias         = volverCategorias;
window.seleccionarCategoria     = seleccionarCategoria;
window.seleccionarReporte       = seleccionarReporte;
window.mostrarReportesCategoria = mostrarReportesCategoria;
window.toggleCategoriaExpansion = toggleCategoriaExpansion;
window.cerrarExpansion          = cerrarExpansion;
window.cargarReporte            = cargarReporte;
window.cambiarReporte           = cambiarReporte;
window.crearTabs                = crearTabs;
window.destruirGraficos         = destruirGraficos;
window.init                     = init;
window.categoriasReportes       = categoriasReportes;
window.obtenerCategoriaPorId    = obtenerCategoriaPorId;
window.obtenerReportePorIds     = obtenerReportePorIds;
window.mostrarCategoriasForzado = mostrarCategoriasForzado;
window.mostrarErrorInicial      = mostrarErrorInicial;

function mostrarReportesCategoria(cat) {
    try {
        if (!cat?.id) { mostrarCategorias(); return; }
        mostrarCategorias();
        setTimeout(() => toggleCategoriaExpansion(cat.id), 450);
    } catch(e) { mostrarCategorias(); }
}
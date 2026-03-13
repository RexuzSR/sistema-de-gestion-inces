// ========== REPORTE: DOCENTES INACTIVOS ==========
// Conectado a: api_reportes.php
// Acciones usadas: docentes_inactivos, anios_disponibles
//
// NOTA: La acción `docentes_inactivos` no filtra por año (muestra todos los
// docentes sin cursos en los últimos 12 meses). El selector de año es
// informativo y se usa para el contexto del reporte.

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_DOCENTES_INACT = '../controlador/Reporte/api_reportes.php';

let graficoInactivosTipoInst  = null;   // torres por tipo
let graficoInactivosDonaInst  = null;   // dona por tipo
let graficoInactivosMesesInst = null;   // barras meses inactivo

let panelInactivosActivo = 'lista';     // 'lista' | 'reciente' | 'distribucion'
let docentesInactivosData = [];         // datos crudos de la API

// ─── Paleta ───────────────────────────────────────────────────────────────────
const COLOR_INACT = [
    { border:'#3b82f6', bg:'rgba(59,130,246,.75)'  },
    { border:'#f59e0b', bg:'rgba(245,158,11,.75)'  },
    { border:'#8b5cf6', bg:'rgba(139,92,246,.75)'  },
    { border:'#10b981', bg:'rgba(16,185,129,.75)'  },
    { border:'#ec4899', bg:'rgba(236,72,153,.75)'  },
    { border:'#f97316', bg:'rgba(249,115,22,.75)'  },
    { border:'#06b6d4', bg:'rgba(6,182,212,.75)'   },
];

// ─── Helper: fetch ────────────────────────────────────────────────────────────
async function apiFetchInact(accion, params = {}) {
    const url = new URL(API_URL_DOCENTES_INACT, window.location.href);
    url.searchParams.set('accion', accion);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));

    const resp = await fetch(url.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();
    if (data?.error) throw new Error(data.error);
    return data;
}

// ─── Calcular meses inactivo ──────────────────────────────────────────────────
function calcularMesesInactivo(ultimaAsignacion) {
    if (!ultimaAsignacion) return null; // nunca tuvo asignación

    const ultima = new Date(ultimaAsignacion);
    const hoy    = new Date();
    const diff   = (hoy.getFullYear() - ultima.getFullYear()) * 12
                 + (hoy.getMonth() - ultima.getMonth());
    return Math.max(0, diff);
}

function formatearFecha(fechaStr) {
    if (!fechaStr) return 'Nunca asignado';
    const f = new Date(fechaStr);
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return `${meses[f.getMonth()]} ${f.getFullYear()}`;
}

// ─── Destruir gráficos ────────────────────────────────────────────────────────
function destruirGraficosInactivos() {
    [graficoInactivosTipoInst, graficoInactivosDonaInst, graficoInactivosMesesInst].forEach(g => {
        if (g?.destroy) g.destroy();
    });
    graficoInactivosTipoInst  = null;
    graficoInactivosDonaInst  = null;
    graficoInactivosMesesInst = null;
}

// ─── Función principal ────────────────────────────────────────────────────────
async function cargarDocentesInactivos() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 3;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                      || { color: 'warning' };

    destruirGraficosInactivos();
    docentesInactivosData = [];
    panelInactivosActivo  = 'lista';

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${categoria.color} text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                     viewBox="0 0 16 16" class="me-2 mb-1">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256
                             7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484
                             10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844
                             9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                    <path d="M15.5 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0
                             0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0
                             0-1h-1v-1a.5.5 0 0 0-.5-.5"/>
                </svg>Docentes Inactivos
            </h4>
            <p class="mb-0 small opacity-75 mt-1">
                Docentes sin cursos asignados en los últimos 12 meses
            </p>
        </div>
        <div class="card-body">

            <div id="loadingInactivos" class="text-center py-5">
                <div class="spinner-border text-${categoria.color}" role="status"></div>
                <p class="mt-3 text-muted">Consultando base de datos...</p>
            </div>

            <div id="errorInactivos" style="display:none;" class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="errorMsgInact"></span>
            </div>

            <div id="contenidoInactivos" style="display:none;"></div>

        </div>
    </div>`;

    try {
        const datos = await apiFetchInact('docentes_inactivos');
        docentesInactivosData = Array.isArray(datos) ? datos : [];
        renderContenidoInactivos();
    } catch (err) {
        document.getElementById('loadingInactivos').style.display  = 'none';
        document.getElementById('errorInactivos').style.display    = 'block';
        document.getElementById('errorMsgInact').textContent       = `No se pudieron cargar los datos: ${err.message}`;
        console.error('[DocInact]', err);
    }
}

// ─── Render contenido principal ───────────────────────────────────────────────
function renderContenidoInactivos() {
    const loadEl  = document.getElementById('loadingInactivos');
    const contEl  = document.getElementById('contenidoInactivos');
    const categoriaId = window.currentCategoryId || 3;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                      || { color: 'warning' };

    loadEl.style.display = 'none';
    contEl.style.display = 'block';

    const docs = docentesInactivosData;

    // Enriquecer datos con meses calculados
    const docsEnriquecidos = docs.map(d => ({
        ...d,
        mesesInactivo: calcularMesesInactivo(d.ultima_asignacion),
        ultimaFmt:     formatearFecha(d.ultima_asignacion),
        iniciales:     `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase(),
    }));

    // Ordenar por meses inactivo desc (más tiempo inactivo primero, nulls al final)
    docsEnriquecidos.sort((a, b) => {
        if (a.mesesInactivo === null && b.mesesInactivo === null) return 0;
        if (a.mesesInactivo === null) return -1; // "Nunca" va primero
        if (b.mesesInactivo === null) return -1;
        return b.mesesInactivo - a.mesesInactivo;
    });

    // Calcular KPIs
    const total        = docsEnriquecidos.length;
    const conMeses     = docsEnriquecidos.filter(d => d.mesesInactivo !== null);
    const sinAsig      = docsEnriquecidos.filter(d => d.mesesInactivo === null).length;
    const masReciente  = conMeses.length > 0
                         ? [...conMeses].sort((a, b) => a.mesesInactivo - b.mesesInactivo)[0]
                         : null;

    // Agrupar por tipo_docente
    const porTipo = {};
    docsEnriquecidos.forEach(d => {
        const t = d.tipo_docente || 'Sin tipo';
        porTipo[t] = (porTipo[t] || 0) + 1;
    });

    const svgUser  = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/><path d="M15.5 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5"/></svg>`;
    const svgClock = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>`;
    const svgPie   = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M7.5 1.018a7 7 0 0 0-4.79 11.566L7.5 7.793zm1 0V7.5h6.482A7.001 7.001 0 0 0 8.5 1.018zM14.982 8.5H8.207l-4.79 4.79A7 7 0 0 0 14.982 8.5z"/></svg>`;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    const kpiCards = [
        {
            tipo:  'lista',
            valor: total,
            esNum: true,
            label: 'Docentes Inactivos',
            sub:   `${sinAsig} nunca asignados`,
            color: 'warning',
            svg:   svgUser
        },
        {
            tipo:  'reciente',
            valor: masReciente ? `${masReciente.primer_nombre} ${masReciente.primer_apellido}` : '—',
            esNum: false,
            label: 'Inactivo Más Reciente',
            sub:   masReciente ? `${masReciente.mesesInactivo} mes${masReciente.mesesInactivo !== 1 ? 'es' : ''} sin actividad` : 'Sin datos',
            color: 'info',
            svg:   svgClock
        },
        {
            tipo:  'distribucion',
            valor: Object.keys(porTipo).length,
            esNum: true,
            label: 'Tipos de Docente',
            sub:   Object.entries(porTipo).map(([k, v]) => `${k}: ${v}`).join(' · ') || 'Sin datos',
            color: 'primary',
            svg:   svgPie
        },
    ];

    contEl.innerHTML = `
        <!-- ── KPI Cards ── -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.05em;">Resumen general</span>
        </div>
        <div id="kpiInactivosCards">
            <div class="row mb-2">
                ${kpiCards.map(c => `
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-3 kpi-card-inactivos"
                         role="button" data-tipo="${c.tipo}"
                         style="cursor:pointer;transition:transform .15s,box-shadow .15s;
                                ${panelInactivosActivo === c.tipo ? 'border-bottom:3px solid var(--bs-' + c.color + ')!important;' : ''}"
                         onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.13)'"
                         onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="card-body text-center py-3">
                            <span class="text-${c.color}">${c.svg}</span>
                            <h2 class="fw-bold text-${c.color} mt-2 mb-0"
                                style="font-size:${c.esNum ? '2.2rem' : '1.15rem'};word-break:break-word;">
                                ${c.esNum ? Number(c.valor).toLocaleString() : c.valor}
                            </h2>
                            <p class="fw-semibold text-uppercase small mb-0" style="letter-spacing:.05em;">${c.label}</p>
                            <p class="text-muted mb-2" style="font-size:.72rem;">${c.sub}</p>
                            <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgeInact-${c.tipo}">
                                ${panelInactivosActivo === c.tipo ? svgUp + 'Activo' : svgDown + 'Ver'}
                            </span>
                        </div>
                    </div>
                </div>`).join('')}
            </div>
        </div>

        <!-- ── Panel expandible ── -->
        <div id="panelInactivosDetalleWrapper" class="mb-4"></div>

        <!-- ── Distribución por Tipo de Docente ── -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Distribución por Tipo de Docente</h5>
                <div class="row">
                    <div class="col-md-7">
                        <h6 class="text-muted fw-semibold small mb-2 text-uppercase" style="letter-spacing:.04em;">
                            Docentes inactivos por tipo — Torres
                        </h6>
                        <div style="position:relative;height:260px;">
                            <canvas id="canvasInactivosTipoTorres"></canvas>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h6 class="text-muted fw-semibold small mb-2 text-uppercase" style="letter-spacing:.04em;">
                            Distribución — Dona
                        </h6>
                        <div style="position:relative;height:260px;">
                            <canvas id="canvasInactivosTipoDona"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Meses inactivo por docente ── -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Meses de Inactividad por Docente</h5>
                <div style="position:relative;height:320px;">
                    <canvas id="canvasInactivosMeses"></canvas>
                </div>
            </div>
        </div>`;

    // Eventos en KPI cards
    contEl.querySelectorAll('.kpi-card-inactivos').forEach(card => {
        card.addEventListener('click', () => {
            panelInactivosActivo = card.dataset.tipo;
            // Actualizar badges y bordes sin re-renderizar todo
            contEl.querySelectorAll('.kpi-card-inactivos').forEach(c => {
                const tipo = c.dataset.tipo;
                c.style.borderBottom = tipo === panelInactivosActivo
                    ? `3px solid var(--bs-${kpiCards.find(k => k.tipo === tipo)?.color || 'warning'})`
                    : '';
                const badge = document.getElementById(`badgeInact-${tipo}`);
                const col   = kpiCards.find(k => k.tipo === tipo)?.color || 'warning';
                if (badge) {
                    badge.className = `badge bg-${col} bg-opacity-10 text-${col} small`;
                    badge.innerHTML = tipo === panelInactivosActivo
                        ? svgUp + 'Activo' : svgDown + 'Ver';
                }
            });
            renderPanelInactivosDetalle(panelInactivosActivo, docsEnriquecidos);
        });
    });

    // Renderizar panel por defecto
    renderPanelInactivosDetalle('lista', docsEnriquecidos);

    // Gráficas
    renderGraficasTipoInactivos(porTipo);
    renderGraficaMesesInactivos(docsEnriquecidos);
}

// ─── Panel expandible ─────────────────────────────────────────────────────────
function renderPanelInactivosDetalle(tipo, docs) {
    const wrapper = document.getElementById('panelInactivosDetalleWrapper');
    if (!wrapper) return;

    if (tipo === 'lista')         renderPanelListaInact(wrapper, docs);
    else if (tipo === 'reciente') renderPanelRecienteInact(wrapper, docs);
    else if (tipo === 'distribucion') renderPanelDistribucionInact(wrapper, docs);
}

// ── Panel 1: Lista completa ───────────────────────────────────────────────────
function renderPanelListaInact(wrapper, docs) {
    if (docs.length === 0) {
        wrapper.innerHTML = `
            <div class="alert alert-success border-0 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>
                No se encontraron docentes inactivos. ¡Todos tienen actividad reciente!
            </div>`;
        return;
    }

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm mb-3" style="border-top:3px solid #f59e0b!important;">
        <div class="card-body">
            <h6 class="card-title mb-3 text-warning fw-semibold">
                <i class="bi bi-list-ul me-2"></i>Lista de Docentes Inactivos
                <span class="badge bg-warning text-dark ms-2">${docs.length}</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>Correo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Última Asignación</th>
                            <th>Inactividad</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${docs.map(d => {
                            const clsEst   = { Disponible:'success', Asignado:'warning text-dark', 'En formación':'info', Inactivo:'secondary' };
                            const clsTipo  = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
                            const meses    = d.mesesInactivo;
                            const clsMes   = meses === null ? 'dark' : meses >= 12 ? 'danger' : meses >= 7 ? 'warning text-dark' : 'info';
                            const labelMes = meses === null ? 'Nunca asignado' : `${meses} mes${meses !== 1 ? 'es' : ''}`;
                            return `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center
                                                    justify-content-center flex-shrink-0"
                                             style="width:36px;height:36px;font-size:.7rem;font-weight:bold;">
                                            ${d.iniciales}
                                        </div>
                                        <strong>${d.primer_nombre} ${d.primer_apellido}</strong>
                                    </div>
                                </td>
                                <td class="small text-muted">${d.correo || '—'}</td>
                                <td><span class="badge bg-${clsTipo[d.tipo_docente]||'secondary'}">${d.tipo_docente||'—'}</span></td>
                                <td><span class="badge bg-${(clsEst[d.estado_docente]||'secondary')}">${d.estado_docente||'—'}</span></td>
                                <td class="small text-muted">${d.ultimaFmt}</td>
                                <td><span class="badge bg-${clsMes}">${labelMes}</span></td>
                            </tr>`;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
}

// ── Panel 2: Docente más reciente ─────────────────────────────────────────────
function renderPanelRecienteInact(wrapper, docs) {
    const conMeses = docs.filter(d => d.mesesInactivo !== null);
    const doc = conMeses.length > 0
                ? [...conMeses].sort((a, b) => a.mesesInactivo - b.mesesInactivo)[0]
                : docs[0] || null;

    if (!doc) {
        wrapper.innerHTML = `<div class="alert alert-info border-0 shadow-sm">Sin datos disponibles.</div>`;
        return;
    }

    const meses   = doc.mesesInactivo;
    const clsMes  = meses === null ? 'dark' : meses >= 12 ? 'danger' : meses >= 7 ? 'warning' : 'info';
    const labelM  = meses === null ? 'Nunca asignado' : `${meses} mes${meses !== 1 ? 'es' : ''} inactivo`;
    const clsEst  = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };
    const clsTipo = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm mb-3" style="border-top:3px solid #06b6d4!important;">
        <div class="card-body">
            <h6 class="card-title mb-3 text-info fw-semibold">
                <i class="bi bi-clock-history me-2"></i>Docente Inactivo Más Reciente
            </h6>
            <div class="row">
                <div class="col-md-5">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body text-center py-4">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center
                                        justify-content-center mx-auto mb-3"
                                 style="width:72px;height:72px;font-size:1.4rem;font-weight:bold;">
                                ${doc.iniciales}
                            </div>
                            <h5 class="fw-bold mb-1">${doc.primer_nombre} ${doc.primer_apellido}</h5>
                            <div class="d-flex gap-2 justify-content-center flex-wrap mb-2">
                                <span class="badge bg-${clsTipo[doc.tipo_docente]||'secondary'}">${doc.tipo_docente||'—'}</span>
                                <span class="badge bg-${clsEst[doc.estado_docente]||'secondary'}">${doc.estado_docente||'—'}</span>
                            </div>
                            <span class="badge bg-${clsMes} fs-6">${labelM}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <h6 class="text-muted small text-uppercase fw-semibold mb-2" style="letter-spacing:.05em;">
                        Detalles de inactividad
                    </h6>
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <h4 class="fw-bold text-info mb-0">${meses === null ? '—' : meses}</h4>
                                    <small class="text-muted">Meses inactivo</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold text-warning mb-0 small">${doc.ultimaFmt}</h4>
                                    <small class="text-muted">Última asignación</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${doc.correo ? `
                    <div class="alert alert-light border py-2">
                        <small class="text-muted"><i class="bi bi-envelope me-1"></i></small>
                        <small>${doc.correo}</small>
                    </div>` : ''}
                </div>
            </div>
        </div>
    </div>`;
}

// ── Panel 3: Distribución detallada por tipo ──────────────────────────────────
function renderPanelDistribucionInact(wrapper, docs) {
    const porTipo = {};
    docs.forEach(d => {
        const t = d.tipo_docente || 'Sin tipo';
        porTipo[t] = (porTipo[t] || 0) + 1;
    });

    const total = docs.length;

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm mb-3" style="border-top:3px solid #3b82f6!important;">
        <div class="card-body">
            <h6 class="card-title mb-3 text-primary fw-semibold">
                <i class="bi bi-pie-chart-fill me-2"></i>Distribución por Tipo de Docente
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo de Docente</th>
                            <th>Cantidad</th>
                            <th>Proporción</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Object.entries(porTipo)
                            .sort(([,a],[,b]) => b - a)
                            .map(([tipo, cnt], idx) => {
                                const pal = COLOR_INACT[idx % COLOR_INACT.length];
                                const pct = total > 0 ? ((cnt / total) * 100).toFixed(1) : 0;
                                return `
                                <tr>
                                    <td class="text-muted small">${idx + 1}</td>
                                    <td><strong>${tipo}</strong></td>
                                    <td><span class="fw-bold" style="color:${pal.border};">${cnt}</span></td>
                                    <td style="min-width:160px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:10px;">
                                                <div class="progress-bar" style="width:${pct}%;background:${pal.border};"></div>
                                            </div>
                                            <small class="text-muted">${pct}%</small>
                                        </div>
                                    </td>
                                </tr>`;
                            }).join('')}
                    </tbody>
                    <tfoot style="font-weight:bold;background:#f8f9fa;">
                        <tr>
                            <td colspan="2">Total</td>
                            <td>${total}</td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>`;
}

// ─── Gráficas tipo docente (torres + dona) ────────────────────────────────────
function renderGraficasTipoInactivos(porTipo) {
    if (graficoInactivosTipoInst?.destroy)  { graficoInactivosTipoInst.destroy();  graficoInactivosTipoInst  = null; }
    if (graficoInactivosDonaInst?.destroy)  { graficoInactivosDonaInst.destroy();  graficoInactivosDonaInst  = null; }

    const labels   = Object.keys(porTipo);
    const values   = Object.values(porTipo);
    const bgColors = labels.map((_, i) => COLOR_INACT[i % COLOR_INACT.length].bg);
    const bdColors = labels.map((_, i) => COLOR_INACT[i % COLOR_INACT.length].border);

    // Torres
    const ctxT = document.getElementById('canvasInactivosTipoTorres')?.getContext('2d');
    if (ctxT && labels.length > 0) {
        graficoInactivosTipoInst = new Chart(ctxT, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Docentes inactivos',
                    data: values,
                    backgroundColor: bgColors,
                    borderColor: bdColors,
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: c => `Docentes: ${c.parsed.y}` } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: 'rgba(0,0,0,.07)' },
                        title: { display: true, text: 'Nº Docentes' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Dona
    const ctxD = document.getElementById('canvasInactivosTipoDona')?.getContext('2d');
    if (ctxD && labels.length > 0) {
        graficoInactivosDonaInst = new Chart(ctxD, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: bgColors, borderColor: bdColors, borderWidth: 2 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'right' },
                    tooltip: { callbacks: { label: c => `${c.label}: ${c.parsed} docente${c.parsed !== 1 ? 's' : ''}` } }
                }
            }
        });
    }
}

// ─── Gráfica meses de inactividad ─────────────────────────────────────────────
function renderGraficaMesesInactivos(docs) {
    if (graficoInactivosMesesInst?.destroy) {
        graficoInactivosMesesInst.destroy();
        graficoInactivosMesesInst = null;
    }

    const ctx = document.getElementById('canvasInactivosMeses')?.getContext('2d');
    if (!ctx) return;

    // Sólo docentes con datos de meses, ordenados desc
    const conMeses = docs
        .filter(d => d.mesesInactivo !== null)
        .sort((a, b) => b.mesesInactivo - a.mesesInactivo);

    if (conMeses.length === 0) return;

    const labels = conMeses.map(d => `${d.primer_nombre.charAt(0)}. ${d.primer_apellido}`);
    const values = conMeses.map(d => d.mesesInactivo);
    const bgColors = conMeses.map((d, i) => {
        const m = d.mesesInactivo;
        return m >= 12 ? 'rgba(239,68,68,.8)'
             : m >= 7  ? 'rgba(245,158,11,.8)'
                       : COLOR_INACT[i % COLOR_INACT.length].bg;
    });

    graficoInactivosMesesInst = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Meses inactivo',
                data: values,
                backgroundColor: bgColors,
                borderColor: bgColors.map(c => c.replace('.8)', '1)')),
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => {
                            const d = conMeses[c.dataIndex];
                            return [
                                `${d.primer_nombre} ${d.primer_apellido}`,
                                `Meses inactivo: ${c.raw}`,
                                `Última asignación: ${d.ultimaFmt}`,
                                `Estado: ${d.estado_docente}`,
                            ];
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,.82)',
                    padding: 12,
                    cornerRadius: 6
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: 'rgba(0,0,0,.07)' },
                    title: { display: true, text: 'Meses de inactividad' }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 35, minRotation: 20 }
                }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
        }
    });
}

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarDocentesInactivos() {
    destruirGraficosInactivos();
    docentesInactivosData = [];
    panelInactivosActivo  = 'lista';
}

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarDocentesInactivos  = cargarDocentesInactivos;
window.limpiarDocentesInactivos = limpiarDocentesInactivos;

console.log('RE-Docente-Inactivos.js');
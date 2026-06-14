// ========== REPORTE: TOTAL DE DOCENTES — conectado a API ==========
// Archivo: RE-Docente-Total.js
// API:     ../controlador/Reporte/api_reportes.php
//
// La API devuelve dos bloques por año:
//   totales         → conteos agregados por estado/tipo/género (sin dimensión mensual)
//   asignacionesMes → array de 12 meses con 'nuevos' = asignaciones a cursos ese mes

const API_DOC = '../controlador/Reporte/api_reportes.php';

// ── Estado del módulo ─────────────────────────────────────────────────────────
let docGrafico = null;       // instancia Chart.js activa
let docCacheAnios = {};         // { 2026: { totales, asignacionesMes } }
let docAniosGrafico = [];         // años seleccionados para el gráfico
let docTipoGrafico = 'line';     // 'line' | 'bar'
let docAnioTabla = null;       // año activo en la tabla mensual
let docAnioBase = null;       // año cargado al inicializar
let docPanelActivo = null;       // 'total'|'activos'|'asignados'|'retirados'|null
let docAniosPanel = [];         // años seleccionados en el panel de detalle
let docTotalesActuales = null;       // totales del año base (para paneles)
const DOC_MAX_ANIOS = 3;

// ── Helper fetch ──────────────────────────────────────────────────────────────
async function docFetch(params) {
    const qs = new URLSearchParams(params).toString();
    const res = await fetch(`${API_DOC}?${qs}`);
    if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
    const data = await res.json();
    if (data && data.error) throw new Error(data.error);
    return data;
}

// ── Obtener y cachear datos de un año ────────────────────────────────────────
async function docFetchAnio(anio) {
    if (docCacheAnios[anio]) return docCacheAnios[anio];
    const data = await docFetch({ accion: 'docentes_total', anio });
    docCacheAnios[anio] = data;
    return data;
}

// ── Nombres de meses ──────────────────────────────────────────────────────────
function docNombresMeses() {
    return ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
}

// ── Colores del gráfico ───────────────────────────────────────────────────────
const DOC_COLOR_BASE = [56, 189, 248]; // azul-celeste

function docColorBorde(idxAnio, totalAnios) {
    const [r, g, b] = DOC_COLOR_BASE;
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.45;
    return `rgb(${Math.round(r * f)},${Math.round(g * f)},${Math.round(b * f)})`;
}
function docColorFill(idxAnio, totalAnios, alpha = 0.18) {
    const [r, g, b] = DOC_COLOR_BASE;
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.45;
    return `rgba(${Math.round(r * f)},${Math.round(g * f)},${Math.round(b * f)},${alpha})`;
}

// ============================================================
// ── FUNCIÓN PRINCIPAL ────────────────────────────────────────
// ============================================================
function cargarTotalDocentes() {
    const contentDiv = document.getElementById('reportContent');

    // Reset de estado
    docGrafico = null;
    docCacheAnios = {};
    docAniosGrafico = [];
    docTipoGrafico = 'line';
    docAnioTabla = null;
    docAnioBase = null;
    docPanelActivo = null;
    docAniosPanel = [];
    docTotalesActuales = null;

    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                     viewBox="0 0 16 16" class="me-2 mb-1">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72zm.014.002L7 13l-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                </svg>Total de Docentes
            </h4>
        </div>
        <div class="card-body">

            <!-- Loading -->
            <div id="docLoading" class="text-center py-5">
                <div class="spinner-border text-warning" role="status"></div>
                <p class="mt-3 text-muted">Cargando datos de docentes desde la base de datos...</p>
            </div>

            <!-- Contenido (oculto hasta cargar) -->
            <div id="docContenido" style="display:none;">

                <!-- KPI Cards -->
                <div id="docKPIs" class="mb-3"></div>

                <!-- Panel colapsable de detalle -->
                <div id="docPanelDetalle" style="display:none;" class="mb-4"></div>

                <!-- Controles gráfico -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-start gap-3">

                            <!-- Años a comparar -->
                            <div style="flex:0 1 auto;min-width:220px;">
                                <div class="rp-toolbar-label mb-2">
                                    Años a comparar
                                    <span id="docAviso" class="badge bg-warning text-dark ms-1"
                                          style="display:none;font-size:.65rem;">máx. ${DOC_MAX_ANIOS}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <select class="form-select form-select-sm" id="docSelAnioGrafico"
                                            style="width:90px;"></select>
                                    <button class="btn btn-sm btn-outline-secondary" id="docBtnAddAnio">
                                        ${svgPlus} Agregar
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="docBtnLimpiarAnios">
                                        ${svgX} Limpiar
                                    </button>
                                </div>
                                <div id="docChipsAnios" class="d-flex gap-1 flex-wrap mt-2"></div>
                            </div>

                            <!-- Separador -->
                            <div class="rp-vdivider d-none d-md-block"></div>

                            <!-- Tipo de gráfica -->
                            <div style="flex:0 0 auto;">
                                <div class="rp-toolbar-label mb-2">Tipo de gráfica</div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-secondary active docBtnTipo"
                                            data-tipo="line" title="Líneas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/>
                                        </svg> Líneas
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary docBtnTipo"
                                            data-tipo="bar" title="Torres">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                                        </svg> Torres
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Gráfico -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-muted fw-semibold">
                            Asignaciones de Docentes a Cursos — por Mes
                        </h6>
                        <div id="docGraficoWrapper" class="rp-chart-wrap rp-chart-wrap--doc"></div>
                    </div>
                </div>

                <!-- Tabla mensual -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h5 class="mb-0">
                                    Registro Mensual —
                                    <span id="docAnioTablaLabel"></span>
                                </h5>
                                <select id="docSelAnioTabla" class="form-select form-select-sm"
                                        style="width:110px;"></select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-center">Asignaciones del mes</th>
                                        <th class="text-center">Total acumulado</th>
                                        <th>Variación mensual</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="docTbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Detalle Docentes Asignados -->
                <div class="modal fade" id="docDetalleModal" tabindex="-1" aria-labelledby="docDetalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-warning bg-opacity-10 border-warning border-opacity-25">
                                <h5 class="modal-title fw-semibold" id="docDetalleModalLabel">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1 text-warning">
                                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 13H1c0-.26.164-1.03.76-1.724C2.305 10.636 3.233 10 5 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72zM4.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m6 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                                    </svg>
                                    Docentes Asignados — <span id="docDetalleModalMes"></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body" id="docDetalleModalBody">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-warning" role="status"></div>
                                    <p class="mt-2 text-muted small">Cargando detalles…</p>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <small class="text-muted me-auto" id="docDetalleModalCount"></small>
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /docContenido -->
        </div>
    </div>`;

    docInit();
}

// ── Inicialización asíncrona ──────────────────────────────────────────────────
async function docInit() {
    try {
        const aniosDisp = await docFetch({ accion: 'anios_disponibles' });
        const anios = Array.isArray(aniosDisp) ? aniosDisp : [new Date().getFullYear()];
        const anioHoy = anios[0];

        docAnioBase = anioHoy;
        docAnioTabla = anioHoy;
        docAniosGrafico = [anioHoy];
        docAniosPanel = [anioHoy];

        // Poblar selects de año
        const optsAnios = anios.map(a => `<option value="${a}">${a}</option>`).join('');
        const selGraf = document.getElementById('docSelAnioGrafico');
        const selTabla = document.getElementById('docSelAnioTabla');
        if (selGraf) selGraf.innerHTML = optsAnios;
        if (selTabla) {
            selTabla.innerHTML = optsAnios;
            selTabla.value = anioHoy;
        }

        // Cargar datos del año actual
        const data = await docFetchAnio(anioHoy);
        docTotalesActuales = data.totales;

        docRenderKPIs(data.totales);
        docRenderTabla(data.asignacionesMes);
        docInitControlesGrafico(anios);
        docRenderGrafico();

        // Listener selector tabla
        selTabla?.addEventListener('change', async function () {
            const a = parseInt(this.value);
            docAnioTabla = a;
            document.getElementById('docAnioTablaLabel').textContent = a;
            try {
                const d = await docFetchAnio(a);
                docRenderTabla(d.asignacionesMes);
            } catch (err) {
                console.error('Error al cambiar año de tabla:', err);
            }
        });

        document.getElementById('docAnioTablaLabel').textContent = anioHoy;
        document.getElementById('docLoading').style.display = 'none';
        document.getElementById('docContenido').style.display = 'block';

    } catch (err) {
        const loading = document.getElementById('docLoading');
        if (loading) loading.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Error al cargar datos: ${err.message}
            </div>`;
    }
}

// ============================================================
// ── KPI CARDS ────────────────────────────────────────────────
// ============================================================
function docRenderKPIs(t) {
    const el = document.getElementById('docKPIs');
    if (!el || !t) return;

    const totalActivos = (parseInt(t.disponible) || 0) + (parseInt(t.en_formacion) || 0) + (parseInt(t.asignado) || 0);
    const totalRetirados = parseInt(t.inactivo) || 0;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const cards = [
        {
            tipo: 'total',
            valor: parseInt(t.total) || 0,
            label: 'Total Docentes',
            sub: 'registrados en el sistema',
            color: 'primary'
        },
        {
            tipo: 'activos',
            valor: totalActivos,
            label: 'Docentes Activos',
            sub: 'disponibles, en formación y asignados',
            color: 'success'
        },
        {
            tipo: 'asignados',
            valor: parseInt(t.asignado) || 0,
            label: 'Asignados',
            sub: 'con cursos activos',
            color: 'warning'
        },
        {
            tipo: 'retirados',
            valor: totalRetirados,
            label: 'Inactivos',
            sub: 'estado Inactivo',
            color: 'danger'
        }
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 kpi-card doc-kpi-card"
                 role="button" data-tipo="${c.tipo}">
                <div class="card-body text-center py-3">
                    <h2 class="fw-bold text-${c.color} mt-1 mb-0">${c.valor.toLocaleString()}</h2>
                    <p class="rp-kpi-card-label">${c.label}</p>
                    <p class="rp-kpi-card-sub">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small"
                          id="docBadge-${c.tipo}">${svgDown}Ver detalle</span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.doc-kpi-card').forEach(card =>
        card.addEventListener('click', () => docTogglePanel(card.dataset.tipo))
    );
}

// ── Toggle del panel de detalle ───────────────────────────────────────────────
function docTogglePanel(tipo) {
    const wrapper = document.getElementById('docPanelDetalle');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total', 'activos', 'asignados', 'retirados'].forEach(t => {
        const b = document.getElementById(`docBadge-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    if (docPanelActivo === tipo) {
        wrapper.style.display = 'none';
        docPanelActivo = null;
        return;
    }

    docPanelActivo = tipo;
    docRenderPanel(tipo);
    wrapper.style.display = 'block';
    const badge = document.getElementById(`docBadge-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
}

// ── Paneles de detalle (uno por cada KPI) ─────────────────────────────────────
const DOC_PANEL_CFG = {
    total: {
        titulo: 'Distribución por Tipo y Género',
        color: 'primary',
        tipo: 'breakdown'
    },
    activos: {
        titulo: 'Distribución por Estado',
        color: 'success',
        tipo: 'estado'
    },
    asignados: {
        titulo: 'Asignaciones por Mes (comparativo multi-año)',
        color: 'warning',
        tipo: 'mensual'
    },
    retirados: {
        titulo: 'Docentes Inactivos',
        color: 'danger',
        tipo: 'inactivos'
    }
};

async function docRenderPanel(tipo) {
    const wrapper = document.getElementById('docPanelDetalle');
    if (!wrapper) return;

    const cfg = DOC_PANEL_CFG[tipo];
    const t = docTotalesActuales;

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm mb-3 rp-card-top" data-color="${cfg.color}">
            <div class="card-body text-center py-4">
                <div class="spinner-border text-${cfg.color}" role="status"></div>
                <p class="mt-2 text-muted small">Cargando panel…</p>
            </div>
        </div>`;

    let contenidoPanel = '';

    // ── Breakdown por tipo y género ───────────────────────────────────────────
    if (cfg.tipo === 'breakdown') {
        contenidoPanel = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tipo</th><th class="text-center">Cantidad</th></tr></thead>
                        <tbody>
                            <tr><td>Fijo</td><td class="text-center fw-bold">${parseInt(t?.fijo) || 0}</td></tr>
                            <tr><td>Contratado</td><td class="text-center fw-bold">${parseInt(t?.contratado) || 0}</td></tr>
                            <tr><td>PDL</td><td class="text-center fw-bold">${parseInt(t?.pdl) || 0}</td></tr>
                            <tr><td>Voluntario</td><td class="text-center fw-bold">${parseInt(t?.voluntario) || 0}</td></tr>
                            <tr class="table-secondary fw-bold"><td>Total</td><td class="text-center">${parseInt(t?.total) || 0}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Género</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Género</th><th class="text-center">Cantidad</th></tr></thead>
                        <tbody>
                            <tr><td>Masculino</td><td class="text-center fw-bold">${parseInt(t?.masculino) || 0}</td></tr>
                            <tr><td>Femenino</td><td class="text-center fw-bold">${parseInt(t?.femenino) || 0}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    // ── Distribución por estado ────────────────────────────────────────────────
    else if (cfg.tipo === 'estado') {
        const totalActivos = (parseInt(t?.disponible) || 0) + (parseInt(t?.en_formacion) || 0) + (parseInt(t?.asignado) || 0);
        contenidoPanel = `
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr><th>Estado</th><th class="text-center">Cantidad</th><th>% del total activo</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-success">Disponible</span></td>
                        <td class="text-center fw-bold">${parseInt(t?.disponible) || 0}</td>
                        <td>${totalActivos > 0 ? Math.round(((parseInt(t?.disponible) || 0) / totalActivos) * 100) : 0}%</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-primary">Asignado</span></td>
                        <td class="text-center fw-bold">${parseInt(t?.asignado) || 0}</td>
                        <td>${totalActivos > 0 ? Math.round(((parseInt(t?.asignado) || 0) / totalActivos) * 100) : 0}%</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-warning">En formación</span></td>
                        <td class="text-center fw-bold">${parseInt(t?.en_formacion) || 0}</td>
                        <td>${totalActivos > 0 ? Math.round(((parseInt(t?.en_formacion) || 0) / totalActivos) * 100) : 0}%</td>
                    </tr>
                    <tr class="table-secondary fw-bold">
                        <td>Total activos</td>
                        <td class="text-center">${totalActivos}</td>
                        <td>100%</td>
                    </tr>
                </tbody>
            </table>`;
    }

    // ── Tabla mensual multi-año ────────────────────────────────────────────────
    else if (cfg.tipo === 'mensual') {
        const svgXS = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;
        const svgPl = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;

        try {
            await Promise.all(docAniosPanel.map(a => docFetchAnio(a)));
        } catch (e) { /* se manejará vacío */ }

        const meses = docNombresMeses();
        const totalesPorAnio = new Array(docAniosPanel.length).fill(0);

        const filas = meses.map((mes, i) => {
            let row = `<tr><td><strong>${mes}</strong></td>`;
            docAniosPanel.forEach((a, idx) => {
                const asig = docCacheAnios[a]?.asignacionesMes || [];
                const v = asig[i]?.nuevos ?? null;
                if (v !== null) totalesPorAnio[idx] += v;
                row += `<td class="text-center">${v !== null ? v.toLocaleString() : '<span class="text-muted">—</span>'}</td>`;
            });
            return row + '</tr>';
        }).join('');

        const filaTotal = `
            <tr class="rp-row-total-tp">
                <td>Total</td>
                ${docAniosPanel.map((_, idx) =>
            `<td class="text-center">${totalesPorAnio[idx] > 0 ? totalesPorAnio[idx].toLocaleString() : '—'}</td>`
        ).join('')}
            </tr>`;

        const allAnios = Object.keys(docCacheAnios).map(Number).sort((a, b) => b - a);
        const aniosFalt = allAnios.filter(a => !docAniosPanel.includes(a));

        contenidoPanel = `
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                ${aniosFalt.length > 0
                ? `<select class="form-select form-select-sm" id="docPanelSelAnio" style="width:auto;">
                           ${aniosFalt.map(a => `<option value="${a}">${a}</option>`).join('')}
                       </select>
                       <button class="btn btn-sm btn-outline-warning" id="docPanelBtnAdd">
                           ${svgPl}Agregar año
                       </button>`
                : `<span class="text-muted small">Todos los años disponibles añadidos</span>`}
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover align-middle mb-0 rp-table-fixed">
                    <colgroup>
                        <col style="width:110px;">
                        ${docAniosPanel.map(() => `<col style="width:110px;">`).join('')}
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th>Mes</th>
                            ${docAniosPanel.map(a => `
                                <th class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <span>${a}</span>
                                        <button class="doc-panel-rm rp-btn-remove-anio"
                                                data-anio="${a}" title="Quitar ${a}">
                                            ${svgXS}
                                        </button>
                                    </div>
                                </th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>${filas}</tbody>
                    <tfoot>${filaTotal}</tfoot>
                </table>
            </div>`;
    }

    // ── Inactivos ──────────────────────────────────────────────────────────────
    else if (cfg.tipo === 'inactivos') {
        contenidoPanel = `
            <div class="alert alert-danger d-flex align-items-start gap-3 mb-0">
                <i class="bi bi-person-x-fill fs-4 mt-1"></i>
                <div>
                    <h5 class="mb-1">
                        ${parseInt(t?.inactivo) || 0} docente${parseInt(t?.inactivo) !== 1 ? 's' : ''} con estado Inactivo
                    </h5>
                    <p class="mb-0">
                        Estos docentes están registrados en el sistema pero su estado actual
                        es <strong>Inactivo</strong>. Para obtener el listado detallado con
                        última asignación, use el reporte de
                        <em>Docentes Inactivos</em>.
                    </p>
                </div>
            </div>`;
    }

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm mb-3 rp-card-top" data-color="${cfg.color}">
            <div class="card-body">
                <h6 class="mb-3 text-${cfg.color} fw-semibold">
                    <i class="bi bi-table me-1"></i>${cfg.titulo}
                </h6>
                ${contenidoPanel}
            </div>
        </div>`;

    if (cfg.tipo === 'mensual') {
        document.getElementById('docPanelBtnAdd')?.addEventListener('click', async () => {
            const sel = document.getElementById('docPanelSelAnio');
            if (!sel) return;
            const a = parseInt(sel.value);
            if (docAniosPanel.includes(a)) return;
            try {
                await docFetchAnio(a);
                docAniosPanel = [...docAniosPanel, a].sort((x, y) => y - x);
                docRenderPanel('asignados');
            } catch (e) { console.error('Error al agregar año al panel:', e); }
        });

        wrapper.querySelectorAll('.doc-panel-rm').forEach(btn =>
            btn.addEventListener('click', () => {
                docAniosPanel = docAniosPanel.filter(x => x !== parseInt(btn.dataset.anio));
                docRenderPanel('asignados');
            })
        );
    }
}

// ============================================================
// ── GRÁFICO ──────────────────────────────────────────────────
// ============================================================
function docDestruirGrafico() {
    if (docGrafico) { docGrafico.destroy(); docGrafico = null; }
}

function docRenderGrafico() {
    const wrapper = document.getElementById('docGraficoWrapper');
    if (!wrapper) return;

    if (!docAniosGrafico.length) {
        docDestruirGrafico();
        wrapper.innerHTML = `
            <div class="rp-chart-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                     viewBox="0 0 16 16">
                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H1v3z"/>
                </svg>
                <p class="rp-chart-empty-text">Agregue al menos un año para ver el gráfico</p>
            </div>`;
        return;
    }

    if (!wrapper.querySelector('canvas')) {
        wrapper.innerHTML = '<canvas id="docCanvas"></canvas>';
    }

    docDestruirGrafico();
    const ctx = wrapper.querySelector('canvas').getContext('2d');
    const meses = docNombresMeses();
    const nAnios = docAniosGrafico.length;
    const esBar = docTipoGrafico === 'bar';

    // En modo línea, mostrar Dic del año anterior como punto de partida
    const labels = esBar
        ? meses
        : (nAnios === 1
            ? [`Dic ${docAniosGrafico[0] - 1}`, ...meses]
            : [`Dic (ant.)`, ...meses]);

    const aniosOrdenados = [...docAniosGrafico].sort((a, b) => b - a);

    const datasets = aniosOrdenados.map((anio, idx) => {
        const asig = docCacheAnios[anio]?.asignacionesMes || [];
        const valores = asig.map(m => m.nuevos);

        let datos;
        if (esBar) {
            datos = valores;
        } else {
            const dicAnterior = (docCacheAnios[anio - 1]?.asignacionesMes || [])[11]?.nuevos ?? null;
            datos = [dicAnterior, ...valores];
        }

        const colorB = docColorBorde(idx, nAnios);
        const colorF = docColorFill(idx, nAnios, esBar ? 0.7 : 0.18);
        const label = nAnios > 1 ? `Asignaciones ${anio}` : `Asignaciones`;

        // Fill — misma lógica que RE-Participante-Total.js (un solo tipo, multi-año)
        const fillConf = esBar ? false : (
            idx === nAnios - 1
                ? { target: 'origin', above: colorF }
                : { target: '+1', above: colorF }
        );

        const ds = {
            label,
            data: datos,
            borderColor: colorB,
            backgroundColor: colorF,
            borderWidth: esBar ? 0 : 2,
            fill: fillConf,
            tension: esBar ? 0 : 0.4,
            borderRadius: esBar ? 4 : 0,
            spanGaps: false,
            order: idx
        };

        if (!esBar) {
            ds.segment = {
                borderDash: c => c.p0DataIndex === 0 ? [5, 4] : undefined,
                borderColor: c => c.p0DataIndex === 0 ? colorB + '88' : colorB
            };
            ds.pointBackgroundColor = c => c.dataIndex === 0 ? colorB + '55' : colorB;
            ds.pointBorderColor = '#fff';
            ds.pointBorderWidth = 2;
            ds.pointRadius = c => c.dataIndex === 0 ? 3 : 4;
            ds.pointHoverRadius = 7;
        }

        return ds;
    });

    docGrafico = new Chart(ctx, {
        type: docTipoGrafico,
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 500 },
            plugins: {
                legend: { display: nAnios > 1, position: 'top' },
                filler: { propagate: true },
                tooltip: {
                    mode: 'index', intersect: false,
                    callbacks: {
                        label: c => `${c.dataset.label}: ${c.parsed.y !== null ? c.parsed.y.toLocaleString() : 'Sin dato'}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        color: c => (!esBar && c.index === 0) ? 'rgba(150,150,150,0.6)' : undefined,
                        font: c => (!esBar && c.index === 0) ? { style: 'italic' } : {}
                    }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Numero de Asignaciones' },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: v => v.toLocaleString() }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

// ── Controles del gráfico (años + tipo) ──────────────────────────────────────
function docInitControlesGrafico(aniosDisp) {
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;

    function actualizarChips() {
        const chips = document.getElementById('docChipsAnios');
        if (!chips) return;

        chips.innerHTML = docAniosGrafico.map(a => `
            <span class="rp-year-chip">
                ${a}
                <button class="doc-rm-chip rp-year-chip-remove"
                        data-anio="${a}" title="Quitar ${a}">${svgX}</button>
            </span>`).join('');

        chips.querySelectorAll('.doc-rm-chip').forEach(btn =>
            btn.addEventListener('click', () => {
                docAniosGrafico = docAniosGrafico.filter(x => x !== parseInt(btn.dataset.anio));
                actualizarChips();
                docRenderGrafico();
            })
        );
    }

    // Agregar año
    document.getElementById('docBtnAddAnio')?.addEventListener('click', async () => {
        const sel = document.getElementById('docSelAnioGrafico');
        if (!sel) return;
        const a = parseInt(sel.value);
        if (docAniosGrafico.includes(a)) return;
        if (docAniosGrafico.length >= DOC_MAX_ANIOS) {
            const av = document.getElementById('docAviso');
            if (av) {
                av.style.display = 'inline-block';
                setTimeout(() => av.style.display = 'none', 2500);
            }
            return;
        }
        try {
            await docFetchAnio(a);
            if (docTipoGrafico === 'line') await docFetchAnio(a - 1).catch(() => { });
            docAniosGrafico = [...docAniosGrafico, a].sort((x, y) => y - x);
            actualizarChips();
            docRenderGrafico();
        } catch (err) {
            console.error('Error al agregar año al gráfico:', err);
        }
    });

    // Limpiar años
    document.getElementById('docBtnLimpiarAnios')?.addEventListener('click', () => {
        docAniosGrafico = [];
        actualizarChips();
        docRenderGrafico();
    });

    // Tipo de gráfico (líneas / torres)
    document.querySelectorAll('.docBtnTipo').forEach(btn => {
        btn.addEventListener('click', () => {
            docTipoGrafico = btn.dataset.tipo;
            document.querySelectorAll('.docBtnTipo').forEach(b => {
                const activo = b.dataset.tipo === docTipoGrafico;
                b.classList.toggle('btn-secondary', activo);
                b.classList.toggle('btn-outline-secondary', !activo);
                b.classList.toggle('active', activo);
            });
            docRenderGrafico();
        });
    });

    actualizarChips();
}

// ============================================================
// ── TABLA MENSUAL ────────────────────────────────────────────
// ============================================================
function docRenderTabla(asignacionesMes) {
    const tbody = document.getElementById('docTbody');
    if (!tbody || !Array.isArray(asignacionesMes)) return;

    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/></svg>`;
    const svgDw = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/></svg>`;
    const svgDs = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/></svg>`;

    let totAsig = 0;

    const filas = asignacionesMes.map((mes, idx) => {
        const sinDato = mes.nuevos === null;
        const vN = sinDato ? null : (mes.nuevos || 0);
        const acum = mes.totalAcumulado !== null ? mes.totalAcumulado : null;
        if (vN !== null) totAsig += vN;

        let varHtml = '<span class="text-muted">—</span>';
        if (!sinDato && vN !== null) {
            const anterior = asignacionesMes.slice(0, idx).reverse().find(m => m.nuevos !== null);
            if (anterior) {
                const vAnt = anterior.nuevos || 0;
                if (vAnt > 0) {
                    const diff = vN - vAnt;
                    const pct = ((diff / vAnt) * 100).toFixed(1);
                    const col = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                    const ico = diff > 0 ? svgUp : diff < 0 ? svgDw : svgDs;
                    varHtml = `<span class="${col}">${ico}${diff > 0 ? '+' : ''}${diff} <small>(${diff >= 0 ? '+' : ''}${pct}%)</small></span>`;
                } else {
                    varHtml = '<span class="text-muted">Sin referencia</span>';
                }
            } else {
                varHtml = '<span class="text-muted">Primer mes</span>';
            }
        }

        const mesIdx = idx + 1;
        const btnDetalle = (!sinDato && vN > 0)
            ? `<button class="btn btn-sm btn-outline-warning doc-btn-detalle-mes" data-mes="${mesIdx}" data-mes-nombre="${mes.mes}" title="Ver docentes asignados en ${mes.mes}">
                 <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                     <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                 </svg>Ver detalle
               </button>`
            : '<span class="text-muted small">—</span>';

        return `<tr>
            <td><strong>${mes.mes}</strong></td>
            <td class="text-center fw-bold">${vN !== null ? vN.toLocaleString() : '—'}</td>
            <td class="text-center">${acum !== null ? acum.toLocaleString() : '—'}</td>
            <td>${sinDato ? '<span class="text-muted">—</span>' : varHtml}</td>
            <td class="text-center">${btnDetalle}</td>
        </tr>`;
    }).join('');

    tbody.innerHTML = filas + `
        <tr class="rp-row-total-tp">
            <td>Total</td>
            <td class="text-center">${totAsig.toLocaleString()}</td>
            <td class="text-center">—</td>
            <td>—</td>
            <td></td>
        </tr>`;

    // Vincular botones "Ver detalle"
    tbody.querySelectorAll('.doc-btn-detalle-mes').forEach(btn => {
        btn.addEventListener('click', () => {
            const mes = parseInt(btn.dataset.mes);
            const mesNombre = btn.dataset.mesNombre;
            docMostrarDetalleMes(mes, mesNombre);
        });
    });
}

// ============================================================
// ── MODAL DETALLE DOCENTES ASIGNADOS POR MES ─────────────────
// ============================================================
async function docMostrarDetalleMes(mes, mesNombre) {
    const modal = document.getElementById('docDetalleModal');
    const body = document.getElementById('docDetalleModalBody');
    const titulo = document.getElementById('docDetalleModalMes');
    const conteo = document.getElementById('docDetalleModalCount');
    if (!modal || !body) return;

    const anio = docAnioTabla || docAnioBase;
    titulo.textContent = `${mesNombre} ${anio}`;
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-2 text-muted small">Cargando detalles…</p>
        </div>`;
    conteo.textContent = '';

    // Mostrar modal con Bootstrap
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    try {
        const data = await docFetch({
            accion: 'docentes_asignados_mes_detalle',
            anio: anio,
            mes: mes
        });

        if (!Array.isArray(data) || data.length === 0) {
            body.innerHTML = `
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No se encontraron asignaciones de docentes en ${mesNombre} ${anio}.
                </div>`;
            conteo.textContent = '0 asignaciones';
            return;
        }

        // Agrupar por docente
        const porDocente = {};
        data.forEach(r => {
            const key = r.id_docente;
            if (!porDocente[key]) {
                porDocente[key] = {
                    nombre: r.nombre_completo.replace(/\s+/g, ' ').trim(),
                    cedula: r.cedula,
                    tipo: r.tipo_docente,
                    estado: r.estado_docente,
                    cursos: []
                };
            }
            porDocente[key].cursos.push({
                id_curso: r.id_curso,
                nombre_curso: r.nombre_curso,
                fecha: r.fecha_asignacion
            });
        });

        const docentes = Object.values(porDocente);
        conteo.textContent = `${data.length} asignación(es) — ${docentes.length} docente(s)`;

        const badgeColor = (estado) => {
            switch (estado) {
                case 'Disponible':   return 'success';
                case 'Asignado':     return 'primary';
                case 'En formación': return 'warning';
                case 'Inactivo':     return 'danger';
                default:             return 'secondary';
            }
        };

        const badgeTipo = (tipo) => {
            switch (tipo) {
                case 'Fijo':        return 'dark';
                case 'Contratado':  return 'info';
                case 'PDL':         return 'secondary';
                case 'Voluntario':  return 'success';
                default:            return 'secondary';
            }
        };

        let html = '';
        docentes.forEach((doc, idx) => {
            html += `
            <div class="card border-0 shadow-sm mb-3 ${idx > 0 ? '' : ''}">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div>
                            <h6 class="mb-0 fw-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16" class="me-1 text-warning">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                                ${doc.nombre}
                            </h6>
                            <small class="text-muted">
                                C.I. ${Number(doc.cedula).toLocaleString('es-VE')}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-${badgeTipo(doc.tipo)} bg-opacity-75">${doc.tipo}</span>
                            <span class="badge bg-${badgeColor(doc.estado)}">${doc.estado}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px;">ID</th>
                                    <th>Curso Asignado</th>
                                    <th style="width:130px;" class="text-center">Fecha Asignación</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${doc.cursos.map(c => `
                                    <tr>
                                        <td class="text-muted">#${c.id_curso}</td>
                                        <td class="fw-semibold">${c.nombre_curso}</td>
                                        <td class="text-center small">${new Date(c.fecha + 'T00:00:00').toLocaleDateString('es-VE', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                                    </tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
        });

        body.innerHTML = html;

    } catch (err) {
        body.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Error al cargar detalles: ${err.message}
            </div>`;
        conteo.textContent = 'Error';
    }
}

// ============================================================
// ── LIMPIEZA ─────────────────────────────────────────────────
// ============================================================
function limpiarTotalDocentes() {
    docDestruirGrafico();
    docCacheAnios = {};
    docAniosGrafico = [];
    docTipoGrafico = 'line';
    docAnioTabla = null;
    docAnioBase = null;
    docPanelActivo = null;
    docAniosPanel = [];
    docTotalesActuales = null;
}

// ── Registrar en window ───────────────────────────────────────────────────────
Object.defineProperty(window, 'docGrafico', { get: function() { return docGrafico; }, configurable: true });
Object.defineProperty(window, 'docAnioBase', { get: function() { return docAnioBase; }, configurable: true });
Object.defineProperty(window, 'docAnioTabla', { get: function() { return docAnioTabla; }, configurable: true });
Object.defineProperty(window, 'docAniosGrafico', { get: function() { return docAniosGrafico; }, configurable: true });
Object.defineProperty(window, 'docTipoGrafico', { get: function() { return docTipoGrafico; }, configurable: true });
Object.defineProperty(window, 'docAniosPanel', { get: function() { return docAniosPanel; }, configurable: true });

window.cargarTotalDocentes = cargarTotalDocentes;
window.limpiarTotalDocentes = limpiarTotalDocentes;
console.log('RE-Docente-Total.js cargado — conectado a CSS centralizado');
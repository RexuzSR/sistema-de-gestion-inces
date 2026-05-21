// ========== REPORTE: PARTICIPANTES APROBADOS — Conectado a API ==========
// Datos: api_reportes.php  acciones: participantes_aprobados, participantes_total,
//        cursos_mas_participantes, anios_disponibles

// ─── Constante de ruta API ───────────────────────────────────────────────────
const API_BASE_APROB = '../controlador/Reporte/api_reportes.php';

// ─── Estado global ────────────────────────────────────────────────────────────
let datosAprobMensuales    = [];
let graficoAprobInstance   = null;

/**
 * cacheAprobAnuales[anio] = array 12 meses:
 * { mes, mesNumero, aprobados, reprobados, evaluados, tasaAprobacion, totalAcumulado }
 * null en campos → mes futuro (sin datos)
 */
let cacheAprobAnuales      = {};

/**
 * cacheAprobCursos[anio] = array de cursos desde API:
 * { id, nombre_curso, total_participantes, estatus, modalidad, fecha_inicio, fecha_fin }
 */
let cacheAprobCursos       = {};

/** Años disponibles según la base de datos */
let añosDispAprobCache     = [];

let anioAprobBase          = null;
let tiposAprobActivos      = new Set(['evaluados', 'aprobados', 'reprobados']);
let aniosAprobActivos      = [];
let panelAprobActivo       = null;
let añosAprobGlobales      = [];
let anioAprobTabla         = null;

let filtroAprobTiempo = {
    tipo: 'anual', anio: new Date().getFullYear(),
    semestre: null, trimestre: null, mes: null
};

let tipoGraficaAprob       = 'line';
let tipoGraficaCursosAprob = 'bar';
const MAX_ANIOS_APROB      = 3;

let cursosAprobDisp          = [];
let cursosAprobSel           = [];
let instanciasGraficosCursos = [];

// ─── Paleta de colores ────────────────────────────────────────────────────────
const COLOR_APROB_MATRIZ = {
    evaluados:  ['#3b82f6', '#06b6d4', '#6366f1'],
    aprobados:  ['#22c55e', '#84cc16', '#10b981'],
    reprobados: ['#ef4444', '#f97316', '#ec4899'],
};

const COLOR_CURSOS_PALETTE = [
    { border:'#0ea5e9', bg:'rgba(14,165,233,0.75)',  bgLight:'rgba(14,165,233,0.18)'  },
    { border:'#f59e0b', bg:'rgba(245,158,11,0.75)',  bgLight:'rgba(245,158,11,0.18)'  },
    { border:'#8b5cf6', bg:'rgba(139,92,246,0.75)',  bgLight:'rgba(139,92,246,0.18)'  },
    { border:'#06b6d4', bg:'rgba(6,182,212,0.75)',   bgLight:'rgba(6,182,212,0.18)'   },
    { border:'#ec4899', bg:'rgba(236,72,153,0.75)',  bgLight:'rgba(236,72,153,0.18)'  },
    { border:'#84cc16', bg:'rgba(132,204,22,0.75)',  bgLight:'rgba(132,204,22,0.18)'  },
    { border:'#f97316', bg:'rgba(249,115,22,0.75)',  bgLight:'rgba(249,115,22,0.18)'  },
];

// ====================================================================
// ─────────────────── CAPA DE API ────────────────────────────────────
// ====================================================================

/**
 * Fetch genérico a la API centralizada.
 * Lanza Error si la respuesta no es ok o contiene { error }.
 */
async function apiFetch(accion, params = {}) {
    const url = new URL(API_BASE_APROB, window.location.href);
    url.searchParams.set('accion', accion);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));

    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    const data = await res.json();

    if (!res.ok || data.error) {
        throw new Error(data.error || `HTTP ${res.status}`);
    }
    return data;
}

/**
 * Carga y cachea datos mensuales para un año:
 *  - participantes_aprobados → nuevos = aprobados del mes
 *  - participantes_total     → retirados = reprobados/ausentes (proxy evaluados no aprobados)
 * Produce: { mes, mesNumero, aprobados, reprobados, evaluados, tasaAprobacion, totalAcumulado }
 */
async function cargarDatosAnioAprob(anio) {
    if (cacheAprobAnuales[anio]) return cacheAprobAnuales[anio];

    const [aprob, total] = await Promise.all([
        apiFetch('participantes_aprobados', { anio }),
        apiFetch('participantes_total',     { anio }),
    ]);

    const merged = (aprob || []).map(m => {
        const t          = (total || []).find(x => x.mesNumero === m.mesNumero) || {};
        const aprobados  = m.nuevos;  // null → mes futuro
        const reprobados = aprobados !== null ? (parseInt(t.retirados) || 0) : null;
        const evaluados  = aprobados !== null ? aprobados + reprobados : null;
        const tasaAprobacion = evaluados > 0
            ? +(aprobados / evaluados * 100).toFixed(1)
            : (aprobados !== null ? 0 : null);

        return {
            mes:           m.mes,
            mesNumero:     m.mesNumero,
            aprobados,
            reprobados,
            evaluados,
            tasaAprobacion,
            totalAcumulado: m.totalAcumulado,
        };
    });

    cacheAprobAnuales[anio] = merged;
    return merged;
}

/**
 * Carga y cachea cursos con más participantes para un año.
 * Asigna id secuencial para uso interno en la UI.
 */
async function cargarCursosAnio(anio) {
    if (cacheAprobCursos[anio]) return cacheAprobCursos[anio];
    const data = await apiFetch('cursos_mas_participantes', { anio, limite: 20 });
    const cursos = (Array.isArray(data) ? data : []).map((c, i) => ({ id: i + 1, ...c }));
    cacheAprobCursos[anio] = cursos;
    return cursos;
}

/** Carga y cachea los años disponibles en la base de datos. */
async function cargarAniosDisponibles() {
    if (añosDispAprobCache.length) return añosDispAprobCache;
    const data = await apiFetch('anios_disponibles');
    añosDispAprobCache = Array.isArray(data) && data.length
        ? data
        : [new Date().getFullYear()];
    return añosDispAprobCache;
}

// ====================================================================
// ─────────────────── HELPERS UI ─────────────────────────────────────
// ====================================================================

function obtenerNombresMesesAprob() {
    return ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
}

function destruirGraficoAprob() {
    if (graficoAprobInstance?.destroy) {
        graficoAprobInstance.destroy();
        graficoAprobInstance = null;
    }
}

function destruirGraficosCursos() {
    instanciasGraficosCursos.forEach(g => { if (g.instance?.destroy) g.instance.destroy(); });
    instanciasGraficosCursos = [];
}

function initAprobTooltips(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}

function colorAprobBorde(tipo, idxAnio) {
    const v = COLOR_APROB_MATRIZ[tipo];
    if (!v) return '#6c757d';
    return v[idxAnio % v.length];
}

function colorAprobFill(tipo, idxAnio, alpha = 0.18) {
    const hex = colorAprobBorde(tipo, idxAnio).replace('#', '');
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}

/** Muestra error en el área de loading */
function mostrarErrorAprob(msg) {
    const el = document.getElementById('loadingAprob');
    if (el) el.innerHTML = `
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
            </svg>
            <div><strong>Error al cargar datos:</strong> ${msg}</div>
        </div>`;
}

/** Calcula resumen KPI a partir de datos mensuales reales */
function calcularResumenAprob(datos) {
    const con = datos.filter(m => m.aprobados !== null);
    if (!con.length) return { aprobados: 0, evaluados: 0, reprobados: 0, tasa: '—', cursos: 0 };

    const totAprob = con.reduce((s, m) => s + (m.aprobados  || 0), 0);
    const totRepr  = con.reduce((s, m) => s + (m.reprobados || 0), 0);
    const totEval  = totAprob + totRepr;

    return {
        aprobados:  totAprob,
        reprobados: totRepr,
        evaluados:  totEval,
        tasa: totEval > 0 ? (totAprob / totEval * 100).toFixed(1) : '—',
        cursos: cursosAprobDisp.length,
    };
}

/** Filtra meses según el filtro de período activo */
function filtrarMesesPorTiempo(datos) {
    if (filtroAprobTiempo.tipo === 'anual') return datos;
    return datos.filter(m => {
        if (filtroAprobTiempo.tipo === 'semestral') {
            const s = filtroAprobTiempo.semestre;
            return s === 1 ? m.mesNumero <= 6 : m.mesNumero >= 7;
        }
        if (filtroAprobTiempo.tipo === 'trimestral') {
            const ini = (filtroAprobTiempo.trimestre - 1) * 3 + 1;
            return m.mesNumero >= ini && m.mesNumero <= ini + 2;
        }
        if (filtroAprobTiempo.tipo === 'mensual') return m.mesNumero === filtroAprobTiempo.mes;
        return true;
    });
}

// ====================================================================
// ─────────────────── HTML PRINCIPAL ─────────────────────────────────
// ====================================================================

function cargarParticipantesAprobados() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 1;
    const categorias  = window.categoriasReportes || [];
    const categoria   = categorias.find(c => c.id === categoriaId) || { color: 'primary' };
    const hoy         = new Date().getFullYear();

    // Limpiar estado global
    destruirGraficoAprob();
    destruirGraficosCursos();
    cacheAprobAnuales  = {};
    Object.keys(cacheAprobCursos).forEach(k => delete cacheAprobCursos[k]);
    añosDispAprobCache = [];
    panelAprobActivo   = null;
    añosAprobGlobales  = [hoy];
    anioAprobBase      = hoy;
    anioAprobTabla     = hoy;
    tiposAprobActivos  = new Set(['evaluados', 'aprobados', 'reprobados']);
    aniosAprobActivos  = [hoy];
    tipoGraficaAprob   = 'line';
    tipoGraficaCursosAprob = 'bar';
    filtroAprobTiempo  = { tipo: 'anual', anio: hoy, semestre: null, trimestre: null, mes: null };
    cursosAprobSel     = [];

    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgX    = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${categoria.color} text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">
                    <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2.905 2.27H14.5a.5.5 0 0 1 0 1H5.405a.5.5 0 0 1 0-1zM2.5 7.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2.905 2.27H14.5a.5.5 0 0 1 0 1H5.405a.5.5 0 0 1 0-1zM2.5 11.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1z"/>
                </svg>Participantes Aprobados
            </h4>
        </div>
        <div class="card-body">

            <div id="loadingAprob" class="text-center py-5">
                <div class="spinner-border text-${categoria.color}" role="status"></div>
                <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
            </div>

            <div id="contenidoAprob" style="display:none;">

                <div id="kpiAprobCards"></div>
                <div id="panelAprobDetalleWrapper" style="display:none;" class="mb-3"></div>

                <!-- ── Controles gráfico principal ── -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-start gap-3">

                            <!-- Dato a representar -->
                            <div style="flex:1 1 auto;min-width:260px;">
                                <div class="text-muted small fw-semibold mb-2" style="text-transform:uppercase;letter-spacing:.05em;">Dato a representar</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-primary btn-aprob-tipo active" data-tipo="evaluados">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/></svg>Evaluados
                                    </button>
                                    <button class="btn btn-sm btn-outline-success btn-aprob-tipo active" data-tipo="aprobados">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Aprobados
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-aprob-tipo active" data-tipo="reprobados">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M11 7.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Reprobados
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnAprobLimpiarTipos">${svgX} Limpiar</button>
                                </div>
                            </div>

                            <div style="width:1px;background:#dee2e6;align-self:stretch;flex-shrink:0;" class="d-none d-md-block"></div>

                            <!-- Años a comparar -->
                            <div style="flex:0 1 auto;min-width:200px;">
                                <div class="text-muted small fw-semibold mb-2" style="text-transform:uppercase;letter-spacing:.05em;">
                                    Años a comparar
                                    <span id="aprobAnioLimiteAviso" class="badge bg-warning text-dark ms-1" style="display:none;font-size:.65rem;">máx. ${MAX_ANIOS_APROB}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <select class="form-select form-select-sm" id="selectAnioAprobGrafico" style="width:90px;"></select>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnAddAnioAprobGrafico">${svgPlus} Agregar</button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarAprobAnios">${svgX} Limpiar</button>
                                </div>
                                <div id="chipsAprobAnios" class="d-flex gap-1 flex-wrap mt-2"></div>
                            </div>

                            <div style="width:1px;background:#dee2e6;align-self:stretch;flex-shrink:0;" class="d-none d-md-block"></div>

                            <!-- Tipo de gráfica -->
                            <div style="flex:0 0 auto;">
                                <div class="text-muted small fw-semibold mb-2" style="text-transform:uppercase;letter-spacing:.05em;">Tipo de gráfica</div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-secondary btn-aprob-chart-type active" data-tipo="line" title="Líneas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/></svg> Líneas
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary btn-aprob-chart-type" data-tipo="bar" title="Torres">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg> Torres
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Gráfico principal -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloAprobGrafico"></h6>
                        <div id="graficoAprobWrapper" style="height:420px;position:relative;"></div>
                    </div>
                </div>

                <!-- ══ Registro Mensual ══ -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="card-title mb-0">
                                Registro Mensual —
                                <span id="anioAprobSeleccionado">${hoy}</span>
                            </h5>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted small fw-semibold">Año:</span>
                                <select class="form-select form-select-sm" id="selectAnioAprobTabla" style="width:110px;"></select>
                                <span class="text-muted small fw-semibold ms-1">Período:</span>
                                <select class="form-select form-select-sm" id="selectAprobFiltroTipo" style="width:auto;">
                                    <option value="anual">Anual</option>
                                    <option value="semestral">Semestral</option>
                                    <option value="trimestral">Trimestral</option>
                                    <option value="mensual">Mensual</option>
                                </select>
                                <div id="aprobFiltroExtra" class="d-flex gap-2"></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th>Evaluados</th>
                                        <th>Aprobados</th>
                                        <th>Reprobados</th>
                                        <th>Tasa Aprobación</th>
                                        <th>Var. mensual
                                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Diferencia de aprobados respecto al mes anterior."
                                                style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;
                                                       width:15px;height:15px;border-radius:50%;border:1.5px solid currentColor;
                                                       font-size:9px;font-weight:bold;vertical-align:middle;margin-left:3px;">!</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tablaAprobMensual"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ══ Análisis por Curso ══ -->
                <div class="rounded-3 p-3 mb-4" style="background:#e2e8f0;border:2px solid #94a3b8;">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#475569" viewBox="0 0 16 16"><path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z"/></svg>
                            <span class="fw-bold text-secondary" style="letter-spacing:.04em;">ANÁLISIS POR CURSO</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Mostrando:</span>
                            <span class="badge bg-secondary" id="badgeAnioAprobCurso">${hoy}</span>
                            <span class="badge bg-light text-dark border" id="badgePeriodoAprobCurso">Anual</span>
                        </div>
                    </div>

                    <!-- Tabla de cursos -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Cursos con más participantes — Seleccione para visualizar</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:44px;"></th>
                                            <th>Curso</th>
                                            <th>Modalidad</th>
                                            <th>Estado</th>
                                            <th>Participantes</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaAprobCursos"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de gráficos de cursos -->
                    <div id="seccionGraficoCursos" style="display:none;">

                        <!-- Controles tipo gráfica -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span class="text-muted small fw-semibold">Tipo de gráfica:</span>
                                    <div class="d-flex gap-2 flex-wrap" id="botonesGraficaCursos">
                                        <button class="btn btn-sm btn-secondary btn-curso-chart-type active" data-tipo="bar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg>Barras
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary btn-curso-chart-type" data-tipo="doughnut">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8m0 1a3 3 0 1 1 0 6 3 3 0 0 1 0-6"/></svg>Dona
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary btn-curso-chart-type" data-tipo="polarArea" id="btnCursoChartPolar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0"/></svg>Área Polar
                                        </button>
                                    </div>
                                    <small class="text-muted fst-italic" id="avisoMinCursos" style="display:none;">Área Polar requiere mín. 3 cursos</small>
                                </div>
                            </div>
                        </div>

                        <!-- Grid de gráficas -->
                        <div id="graficasCursosGrid"></div>

                        <!-- Detalle del curso (1 seleccionado) -->
                        <div id="detalleCursoWrapper" style="display:none;" class="mt-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/></svg>
                                        Detalle del Curso
                                    </h6>
                                </div>
                                <div class="card-body" id="detalleCursoContenido"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══ fin contenedor compartido ══ -->

            </div>
        </div>
    </div>`;

    initAprobTooltips(contentDiv);

    // Iniciar carga asíncrona
    (async () => {
        try {
            const anios = await cargarAniosDisponibles();
            actualizarSelectoresAnioAprob(anios, hoy);
            await cargarDatosAprob(hoy, true);

            // Listener año tabla (compartido con análisis por curso)
            document.getElementById('selectAnioAprobTabla')?.addEventListener('change', async (e) => {
                const nuevoAnio = parseInt(e.target.value);
                if (!añosDispAprobCache.includes(nuevoAnio)) return;
                await cambiarAnioAprobTabla(nuevoAnio);
            });

        } catch (err) {
            console.error('[Aprobados] Error de inicialización:', err);
            mostrarErrorAprob(err.message);
        }
    })();
}

/** Rellena ambos selectores de año con los años disponibles */
function actualizarSelectoresAnioAprob(anios, anioActual) {
    const opts = anios.map(a => `<option value="${a}" ${a === anioActual ? 'selected' : ''}>${a}</option>`).join('');

    const selTabla   = document.getElementById('selectAnioAprobTabla');
    const selGrafico = document.getElementById('selectAnioAprobGrafico');

    if (selTabla)   selTabla.innerHTML   = opts;
    if (selGrafico) selGrafico.innerHTML = opts;
}

// ====================================================================
// ─────────────────── CARGA DE DATOS ─────────────────────────────────
// ====================================================================

async function cargarDatosAprob(anio, esCargaInicial = false) {
    const loadEl   = document.getElementById('loadingAprob');
    const contenEl = document.getElementById('contenidoAprob');

    if (loadEl)   loadEl.style.display   = 'block';
    if (contenEl) contenEl.style.display = 'none';

    try {
        const [datosMes, cursos] = await Promise.all([
            cargarDatosAnioAprob(anio),
            cargarCursosAnio(anio),
        ]);

        datosAprobMensuales = datosMes;
        anioAprobBase       = anio;
        anioAprobTabla      = anio;
        cursosAprobDisp     = cursos;
        cursosAprobSel      = [];

        const resumen = calcularResumenAprob(datosMes);

        if (loadEl)   loadEl.style.display   = 'none';
        if (contenEl) contenEl.style.display  = 'block';

        const labelAnio = document.getElementById('anioAprobSeleccionado');
        if (labelAnio) labelAnio.textContent = anio;
        const selTabla = document.getElementById('selectAnioAprobTabla');
        if (selTabla) selTabla.value = anio;

        if (esCargaInicial) {
            aniosAprobActivos = [anio];
            añosAprobGlobales = [anio];
        } else {
            if (!aniosAprobActivos.includes(anio)) {
                aniosAprobActivos.push(anio);
                aniosAprobActivos.sort((a, b) => a - b);
            }
        }

        renderKPIsAprob(resumen);
        renderTablaAprobMensual(datosMes);
        renderTablaAprobCursos(cursos);
        sincronizarChipsAprobAnios();
        renderGraficoAprob();
        inicializarControlesAprobGrafico();
        inicializarFiltroTiempoAprob();
        if (panelAprobActivo) renderPanelAprobDetalle(panelAprobActivo);

    } catch (err) {
        console.error('[Aprobados] Error cargando datos:', err);
        if (loadEl) mostrarErrorAprob(err.message);
    }
}

/** Cambia el año del análisis de tabla/cursos sin afectar el gráfico principal */
async function cambiarAnioAprobTabla(anio) {
    if (anio === anioAprobTabla) return;
    anioAprobTabla  = anio;
    cursosAprobSel  = [];

    const label = document.getElementById('anioAprobSeleccionado');
    if (label) label.textContent = anio;
    const badgeAnio = document.getElementById('badgeAnioAprobCurso');
    if (badgeAnio) badgeAnio.textContent = anio;

    try {
        const [datosMes, cursos] = await Promise.all([
            cargarDatosAnioAprob(anio),
            cargarCursosAnio(anio),
        ]);
        cursosAprobDisp = cursos;
        renderTablaAprobMensual(datosMes);
        renderTablaAprobCursos(cursos);
        actualizarVisualizacionCursos();
    } catch (err) {
        console.error('[Aprobados] Error cambiando año tabla:', err);
    }
}

// ====================================================================
// ─────────────────── FILTRO DE TIEMPO ───────────────────────────────
// ====================================================================

function inicializarFiltroTiempoAprob() {
    const sel   = document.getElementById('selectAprobFiltroTipo');
    const extra = document.getElementById('aprobFiltroExtra');
    if (!sel || !extra) return;

    const clonado = sel.cloneNode(true);
    sel.parentNode.replaceChild(clonado, sel);

    function actualizarExtra() {
        const tipo = clonado.value;
        filtroAprobTiempo.tipo = tipo;
        extra.innerHTML = '';
        const hoy = new Date();
        const badgePeriodo = document.getElementById('badgePeriodoAprobCurso');

        if (tipo === 'semestral') {
            extra.innerHTML = `<select class="form-select form-select-sm" id="selAprobSemestre" style="width:auto;">
                <option value="1">1er Semestre (Ene–Jun)</option><option value="2">2do Semestre (Jul–Dic)</option>
            </select>`;
            const def = hoy.getMonth() < 6 ? '1' : '2';
            const semSel = document.getElementById('selAprobSemestre');
            semSel.value = def;
            filtroAprobTiempo.semestre = parseInt(def);
            if (badgePeriodo) badgePeriodo.textContent = def === '1' ? '1er Semestre' : '2do Semestre';
            semSel.addEventListener('change', e => {
                filtroAprobTiempo.semestre = parseInt(e.target.value);
                if (badgePeriodo) badgePeriodo.textContent = e.target.value === '1' ? '1er Semestre' : '2do Semestre';
                aplicarFiltroTablas();
            });

        } else if (tipo === 'trimestral') {
            const trim = Math.ceil((hoy.getMonth() + 1) / 3);
            extra.innerHTML = `<select class="form-select form-select-sm" id="selAprobTrimestre" style="width:auto;">
                <option value="1">1er Trim (Ene–Mar)</option><option value="2">2do Trim (Abr–Jun)</option>
                <option value="3">3er Trim (Jul–Sep)</option><option value="4">4to Trim (Oct–Dic)</option>
            </select>`;
            document.getElementById('selAprobTrimestre').value = trim;
            filtroAprobTiempo.trimestre = trim;
            const trims = ['', '1er Trim', '2do Trim', '3er Trim', '4to Trim'];
            if (badgePeriodo) badgePeriodo.textContent = trims[trim];
            document.getElementById('selAprobTrimestre').addEventListener('change', e => {
                filtroAprobTiempo.trimestre = parseInt(e.target.value);
                if (badgePeriodo) badgePeriodo.textContent = trims[parseInt(e.target.value)];
                aplicarFiltroTablas();
            });

        } else if (tipo === 'mensual') {
            const meses = obtenerNombresMesesAprob();
            extra.innerHTML = `<select class="form-select form-select-sm" id="selAprobMes" style="width:auto;">
                ${meses.map((m, i) => `<option value="${i+1}" ${i+1 === hoy.getMonth()+1 ? 'selected' : ''}>${m}</option>`).join('')}
            </select>`;
            filtroAprobTiempo.mes = hoy.getMonth() + 1;
            if (badgePeriodo) badgePeriodo.textContent = meses[hoy.getMonth()];
            document.getElementById('selAprobMes').addEventListener('change', e => {
                filtroAprobTiempo.mes = parseInt(e.target.value);
                if (badgePeriodo) badgePeriodo.textContent = meses[parseInt(e.target.value) - 1];
                aplicarFiltroTablas();
            });

        } else {
            if (badgePeriodo) badgePeriodo.textContent = 'Anual';
        }
        aplicarFiltroTablas();
    }

    clonado.addEventListener('change', actualizarExtra);
}

function aplicarFiltroTablas() {
    const datos = cacheAprobAnuales[anioAprobTabla] || [];
    renderTablaAprobMensual(datos);
    actualizarVisualizacionCursos();
}

// ====================================================================
// ─────────────────── KPI CARDS ──────────────────────────────────────
// ====================================================================

function renderKPIsAprob(data) {
    const el = document.getElementById('kpiAprobCards');
    if (!el) return;

    const svgEval  = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/></svg>`;
    const svgCheck = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>`;
    const svgX2    = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11 7.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>`;
    const svgTasa  = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/></svg>`;
    const svgDown  = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const cards = [
        { tipo:'evaluados',  valor: data.evaluados,           label:'Evaluados',   sub:'aprobados + reprobados',  color:'primary', svg: svgEval  },
        { tipo:'aprobados',  valor: data.aprobados,           label:'Aprobados',   sub:'participantes aprobados',  color:'success', svg: svgCheck },
        { tipo:'reprobados', valor: data.reprobados,          label:'Reprobados',  sub:'ausentes y reprobados',    color:'danger',  svg: svgX2    },
        { tipo:'tasa',       valor: `${data.tasa}%`,          label:'Tasa Global', sub:'aprobados / evaluados',    color:'warning', svg: svgTasa  },
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 kpi-card-aprob" role="button" data-tipo="${c.tipo}"
                 style="cursor:pointer;transition:transform .15s,box-shadow .15s;"
                 onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.13)'"
                 onmouseleave="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body text-center py-3">
                    <span class="text-${c.color}">${c.svg}</span>
                    <h2 class="fw-bold text-${c.color} mt-2 mb-0">${typeof c.valor === 'number' ? c.valor.toLocaleString() : c.valor}</h2>
                    <p class="fw-semibold text-uppercase small mb-0" style="letter-spacing:.05em;">${c.label}</p>
                    <p class="text-muted mb-2" style="font-size:.72rem;">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelAprob-${c.tipo}">${svgDown}Ver detalle</span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-aprob').forEach(card =>
        card.addEventListener('click', () => togglePanelAprobDetalle(card.dataset.tipo))
    );
}

// ====================================================================
// ─────────────────── PANEL DETALLE MULTI-AÑO ────────────────────────
// ====================================================================

const CFG_PANEL_APROB = {
    evaluados:  { titulo:'Evaluados por Mes',      campo:'evaluados',      color:'primary', svgPath:'<path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>' },
    aprobados:  { titulo:'Aprobados por Mes',      campo:'aprobados',      color:'success', svgPath:'<path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>' },
    reprobados: { titulo:'Reprobados por Mes',     campo:'reprobados',     color:'danger',  svgPath:'<path fill-rule="evenodd" d="M11 7.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>' },
    tasa:       { titulo:'Tasa de Aprobación (%)', campo:'tasaAprobacion', color:'warning', svgPath:'<path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/>' },
};

function togglePanelAprobDetalle(tipo) {
    const wrapper = document.getElementById('panelAprobDetalleWrapper');
    if (!wrapper) return;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['evaluados','aprobados','reprobados','tasa'].forEach(t => {
        const b = document.getElementById(`badgePanelAprob-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    if (panelAprobActivo === tipo) {
        wrapper.style.display = 'none';
        panelAprobActivo = null;
        return;
    }
    panelAprobActivo = tipo;

    // Asegurar que el año base esté cargado
    añosAprobGlobales.forEach(a => { if (!cacheAprobAnuales[a]) cargarDatosAnioAprob(a); });

    renderPanelAprobDetalle(tipo);
    wrapper.style.display = 'block';
    const badge = document.getElementById(`badgePanelAprob-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
}

function renderPanelAprobDetalle(tipo) {
    const wrapper = document.getElementById('panelAprobDetalleWrapper');
    if (!wrapper) return;

    const cfg     = CFG_PANEL_APROB[tipo];
    const meses   = obtenerNombresMesesAprob();
    const años    = añosAprobGlobales;
    const aniosDisp = añosDispAprobCache;

    const totalesPorAño = new Array(años.length).fill(0);
    const filas = meses.map((mes, i) => {
        let row = `<tr><td><strong>${mes}</strong></td>`;
        años.forEach((a, idx) => {
            const cache = cacheAprobAnuales[a] || [];
            const d = cache[i];
            const v = (d && d[cfg.campo] !== null) ? d[cfg.campo] : null;
            if (v !== null && v !== undefined) totalesPorAño[idx] += v;
            const fmt = cfg.campo === 'tasaAprobacion'
                ? (v !== null && v !== undefined ? `${v}%` : '<span class="text-muted">—</span>')
                : (v !== null && v !== undefined ? Number(v).toLocaleString() : '<span class="text-muted">—</span>');
            row += `<td class="text-center">${fmt}</td>`;
        });
        return row + '</tr>';
    }).join('');

    const filaTotal = `<tr style="background:#f2f2f2;font-weight:bold;"><td>Total</td>${
        años.map((_, idx) => `<td class="text-center">${totalesPorAño[idx] > 0 ? totalesPorAño[idx].toLocaleString() : '—'}</td>`).join('')
    }</tr>`;

    const añosFaltantes = aniosDisp.filter(a => !años.includes(a));
    const svgXS  = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;
    const svgChk = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>`;
    const svgPl  = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm mb-3" style="border-top:3px solid var(--bs-${cfg.color},#0d6efd)!important;">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="mb-0 text-${cfg.color}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">${cfg.svgPath}</svg>${cfg.titulo}
                </h6>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    ${añosFaltantes.length > 0
                        ? `<select class="form-select form-select-sm" id="selAddAnioAprobPanel" style="width:auto;">${añosFaltantes.map(a => `<option value="${a}">${a}</option>`).join('')}</select>
                           <button class="btn btn-sm btn-outline-${cfg.color}" id="btnAddAnioAprobPanel">${svgPl}Agregar año</button>`
                        : `<span class="text-muted small">${svgChk}Todos los años añadidos</span>`}
                </div>
            </div>
            ${años.length === 0
                ? `<div class="text-center py-5 text-muted">No hay años seleccionados. Use "Agregar año" para comparar.</div>`
                : `<div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0" style="table-layout:fixed;">
                        <colgroup><col style="width:110px;">${años.map(() => `<col style="width:95px;">`).join('')}</colgroup>
                        <thead class="table-light">
                            <tr><th>Mes</th>${años.map(a => `
                                <th class="text-center">
                                    <div class="d-flex align-items-center justify-content:center gap-1">
                                        <span>${a}</span>
                                        <button class="btn-rm-anio-aprob-panel" data-anio="${a}" title="Quitar ${a}"
                                            style="background:#dc3545;border:none;color:#fff;border-radius:4px;
                                                   width:18px;height:18px;font-size:9px;padding:0;line-height:1;
                                                   display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">${svgXS}
                                        </button>
                                    </div>
                                </th>`).join('')}</tr>
                        </thead>
                        <tbody>${filas}</tbody>
                        <tfoot style="font-weight:bold;background:#f8f9fa;">${filaTotal}</tfoot>
                    </table>
                </div>`}
        </div>
    </div>`;

    // Agregar año al panel
    document.getElementById('btnAddAnioAprobPanel')?.addEventListener('click', async () => {
        const sel = document.getElementById('selAddAnioAprobPanel');
        if (!sel) return;
        const a = parseInt(sel.value);
        if (añosAprobGlobales.includes(a)) return;
        añosAprobGlobales = [...añosAprobGlobales, a].sort((x, y) => x - y);
        await cargarDatosAnioAprob(a).catch(console.error);
        renderPanelAprobDetalle(panelAprobActivo);
    });

    // Quitar año
    wrapper.querySelectorAll('.btn-rm-anio-aprob-panel').forEach(btn =>
        btn.addEventListener('click', () => {
            añosAprobGlobales = añosAprobGlobales.filter(x => x !== parseInt(btn.dataset.anio));
            renderPanelAprobDetalle(panelAprobActivo);
        })
    );
}

// ====================================================================
// ─────────────────── TABLA MENSUAL ──────────────────────────────────
// ====================================================================

function renderTablaAprobMensual(datos) {
    const tbody = document.getElementById('tablaAprobMensual');
    if (!tbody) return;

    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/></svg>`;
    const svgDw = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/></svg>`;
    const svgDs = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/></svg>`;

    const df = filtrarMesesPorTiempo(datos);
    let totA = 0, totE = 0, totR = 0;

    const filas = df.map((mes, idx) => {
        const sd = mes.aprobados === null;
        if (!sd) { totA += mes.aprobados; totE += mes.evaluados; totR += mes.reprobados; }

        let crec = '<span class="text-muted">—</span>';
        if (!sd) {
            const ant = df.slice(0, idx).reverse().find(m => m.aprobados !== null);
            if (ant) {
                if (ant.aprobados > 0) {
                    const diff = mes.aprobados - ant.aprobados;
                    const pct  = ((diff / ant.aprobados) * 100).toFixed(1);
                    const col  = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                    const ico  = diff > 0 ? svgUp : diff < 0 ? svgDw : svgDs;
                    const sig  = diff > 0 ? '+' : '';
                    crec = `<span class="${col}">${ico}${sig}${diff.toLocaleString()} <small>(${sig}${pct}%)</small></span>`;
                } else {
                    crec = '<span class="text-muted">Sin referencia</span>';
                }
            } else {
                crec = '<span class="text-muted">Primer mes</span>';
            }
        }

        const fmt = v => v !== null ? Number(v).toLocaleString() : '—';
        const tasaBadge = sd ? '—' : (() => {
            const t = mes.tasaAprobacion;
            return `<span class="badge bg-${t >= 80 ? 'success' : t >= 60 ? 'warning' : 'danger'}">${t}%</span>`;
        })();

        return `<tr>
            <td><strong>${mes.mes}</strong></td>
            <td>${fmt(sd ? null : mes.evaluados)}</td>
            <td>${fmt(sd ? null : mes.aprobados)}</td>
            <td>${fmt(sd ? null : mes.reprobados)}</td>
            <td>${tasaBadge}</td>
            <td>${sd ? '<span class="text-muted">—</span>' : crec}</td>
        </tr>`;
    }).join('');

    const tasaTotal = totE > 0 ? (totA / totE * 100).toFixed(1) + '%' : '—';
    tbody.innerHTML = filas + `<tr style="background:#f2f2f2;font-weight:bold;">
        <td>Total</td>
        <td>${totE.toLocaleString()}</td>
        <td>${totA.toLocaleString()}</td>
        <td>${totR.toLocaleString()}</td>
        <td>${tasaTotal}</td>
        <td>—</td>
    </tr>`;
}

// ====================================================================
// ─────────────────── TABLA DE CURSOS ────────────────────────────────
// ====================================================================

function renderTablaAprobCursos(cursos) {
    const tbody = document.getElementById('tablaAprobCursos');
    if (!tbody) return;

    if (!cursos.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No hay cursos con participantes para este año.</td></tr>`;
        return;
    }

    tbody.innerHTML = cursos.map((c, idx) => {
        const pal  = COLOR_CURSOS_PALETTE[idx % COLOR_CURSOS_PALETTE.length];
        const chk  = cursosAprobSel.includes(c.id) ? 'checked' : '';
        const fi   = c.fecha_inicio ? new Date(c.fecha_inicio).toLocaleDateString('es-VE') : '—';
        const ff   = c.fecha_fin    ? new Date(c.fecha_fin).toLocaleDateString('es-VE')    : '—';
        const estatusColor = (c.estatus || '').toLowerCase().includes('proceso') ? 'primary'
                           : (c.estatus || '').toLowerCase().includes('culmin')  ? 'success'
                           : (c.estatus || '').toLowerCase().includes('espera')  ? 'warning'
                           : 'secondary';

        return `<tr class="curso-aprob-fila ${chk ? 'table-primary' : ''}" data-curso-id="${c.id}" data-curso-idx="${idx}" style="cursor:pointer;">
            <td><div class="form-check"><input class="form-check-input curso-aprob-cb" type="checkbox" value="${c.id}" ${chk}></div></td>
            <td><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${pal.border};margin-right:6px;"></span><strong>${c.nombre_curso}</strong></td>
            <td class="text-muted small">${c.modalidad || '—'}</td>
            <td><span class="badge bg-${estatusColor}">${c.estatus || '—'}</span></td>
            <td><strong>${Number(c.total_participantes).toLocaleString()}</strong></td>
            <td class="small">${fi}</td>
            <td class="small">${ff}</td>
        </tr>`;
    }).join('');

    tbody.querySelectorAll('.curso-aprob-cb').forEach(cb => {
        cb.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) { if (!cursosAprobSel.includes(id)) cursosAprobSel.push(id); }
            else cursosAprobSel = cursosAprobSel.filter(x => x !== id);
            actualizarVisualizacionCursos();
        });
    });

    tbody.querySelectorAll('.curso-aprob-fila').forEach(fila => {
        fila.addEventListener('click', function (e) {
            if (e.target.type === 'checkbox') return;
            const cb = this.querySelector('.curso-aprob-cb');
            cb.checked = !cb.checked;
            cb.dispatchEvent(new Event('change'));
        });
    });
}

// ====================================================================
// ─────────────────── GRÁFICAS DE CURSOS ─────────────────────────────
// ====================================================================

function actualizarBotonesGraficaCursos(n) {
    const btnP  = document.getElementById('btnCursoChartPolar');
    const aviso = document.getElementById('avisoMinCursos');
    if (!btnP) return;
    const bloquear = n < 3;
    btnP.disabled = bloquear;
    btnP.classList.toggle('opacity-50', bloquear);
    if (aviso) aviso.style.display = bloquear ? 'inline' : 'none';
    if (bloquear && tipoGraficaCursosAprob === 'polarArea') {
        tipoGraficaCursosAprob = 'bar';
        document.querySelectorAll('.btn-curso-chart-type').forEach(b => {
            const a = b.dataset.tipo === 'bar' && !b.disabled;
            b.classList.toggle('active', a);
            b.classList.toggle('btn-secondary', a);
            b.classList.toggle('btn-outline-secondary', !a);
        });
    }
}

function actualizarVisualizacionCursos() {
    const seccion = document.getElementById('seccionGraficoCursos');
    if (!seccion) return;

    document.querySelectorAll('.curso-aprob-fila').forEach(f => {
        f.classList.toggle('table-primary', cursosAprobSel.includes(parseInt(f.dataset.cursoId)));
    });

    if (!cursosAprobSel.length) { seccion.style.display = 'none'; destruirGraficosCursos(); return; }

    seccion.style.display = 'block';
    const cf = cursosAprobDisp.filter(c => cursosAprobSel.includes(c.id));
    actualizarBotonesGraficaCursos(cf.length);

    const dw = document.getElementById('detalleCursoWrapper');
    if (dw) {
        if (cf.length === 1) { dw.style.display = 'block'; renderDetalleCurso(cf[0]); }
        else dw.style.display = 'none';
    }

    renderGraficasCursos(cf);

    // Registrar botones tipo gráfica una sola vez
    const bc = document.getElementById('botonesGraficaCursos');
    if (bc && !bc.dataset.init) {
        bc.dataset.init = '1';
        bc.querySelectorAll('.btn-curso-chart-type').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                tipoGraficaCursosAprob = btn.dataset.tipo;
                bc.querySelectorAll('.btn-curso-chart-type').forEach(b => {
                    const a = b.dataset.tipo === tipoGraficaCursosAprob && !b.disabled;
                    b.classList.toggle('active', a);
                    b.classList.toggle('btn-secondary', a);
                    b.classList.toggle('btn-outline-secondary', !a);
                });
                renderGraficasCursos(cursosAprobDisp.filter(c => cursosAprobSel.includes(c.id)));
            });
        });
    }
    if (bc) {
        bc.querySelectorAll('.btn-curso-chart-type').forEach(b => {
            const a = b.dataset.tipo === tipoGraficaCursosAprob && !b.disabled;
            b.classList.toggle('active', a);
            b.classList.toggle('btn-secondary', a);
            b.classList.toggle('btn-outline-secondary', !a);
        });
    }
}

function _opcionesCursoChart(esCircular, esPolar) {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: { callbacks: { label: c => {
                const v = c.parsed.y !== undefined ? c.parsed.y : (c.parsed.r !== undefined ? c.parsed.r : c.raw);
                return `${c.dataset.label}: ${v?.toLocaleString() ?? '—'}`;
            }}}
        },
        scales: (esCircular || esPolar) ? {} : {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.07)' }, title: { display: true, text: 'Número Participantes', color: '#64748b', font: { size: 11, weight: 600 } } },
            x: { grid: { color: 'rgba(0,0,0,0.07)' } }
        }
    };
}

function renderGraficasCursos(cursos) {
    const grid = document.getElementById('graficasCursosGrid');
    if (!grid) return;
    destruirGraficosCursos();

    const tipo     = tipoGraficaCursosAprob;
    const esCirc   = tipo === 'doughnut';
    const esPolar  = tipo === 'polarArea';
    const esCircOPol = esCirc || esPolar;

    if (cursos.length === 1) {
        // Un curso: gráfica + detalle lado a lado
        grid.innerHTML = `
        <div class="row mb-3">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <h6 class="card-title text-muted fw-semibold mb-3">${cursos[0].nombre_curso}</h6>
                    <div style="height:280px;position:relative;"><canvas id="canvas-curso-0"></canvas></div>
                </div></div>
            </div>
            <div class="col-md-5" id="detalleCursoInline"></div>
        </div>`;

        const ctx  = document.getElementById('canvas-curso-0').getContext('2d');
        const idxG = cursosAprobDisp.findIndex(c => c.id === cursos[0].id);
        const pal  = COLOR_CURSOS_PALETTE[idxG % COLOR_CURSOS_PALETTE.length];
        const inst = new Chart(ctx, {
            type: tipo,
            data: {
                labels: [cursos[0].nombre_curso],
                datasets: [{ label: 'Participantes', data: [cursos[0].total_participantes], backgroundColor: pal.bg, borderColor: pal.border, borderWidth: 0, borderRadius: 4 }]
            },
            options: _opcionesCursoChart(esCircOPol, esPolar)
        });
        instanciasGraficosCursos.push({ id: '0', instance: inst });

        const detalleInline = document.getElementById('detalleCursoInline');
        if (detalleInline) renderDetalleCursoInline(cursos[0], detalleInline);

        const dw = document.getElementById('detalleCursoWrapper');
        if (dw) dw.style.display = 'none';
        return;
    }

    // Múltiples cursos
    const n = cursos.length;
    const colClass = n <= 2 ? 'col-md-6' : n === 3 ? 'col-md-4' : 'col-md-3';

    let html = `<div class="row mb-3">`;
    cursos.forEach((curso, i) => {
        html += `<div class="${colClass} mb-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h6 class="card-title text-muted fw-semibold mb-1" style="font-size:.82rem;line-height:1.3;">${curso.nombre_curso}</h6>
            <div style="height:220px;position:relative;"><canvas id="canvas-curso-${i}"></canvas></div>
        </div></div></div>`;
    });
    html += `</div>`;

    // Gráfica combinada
    html += `<div class="card border-0 shadow-sm mb-3" style="border-top:3px solid #0ea5e9!important;">
        <div class="card-body">
            <h6 class="card-title text-info fw-semibold mb-3">Participantes — Comparativa de Cursos</h6>
            <div style="height:280px;position:relative;"><canvas id="canvas-cursos-combinado"></canvas></div>
        </div>
    </div>`;
    grid.innerHTML = html;

    // Renderizar cada gráfica individual
    cursos.forEach((curso, i) => {
        const canvas = document.getElementById(`canvas-curso-${i}`);
        if (!canvas) return;
        const ctx  = canvas.getContext('2d');
        const idxG = cursosAprobDisp.findIndex(c => c.id === curso.id);
        const pal  = COLOR_CURSOS_PALETTE[idxG % COLOR_CURSOS_PALETTE.length];

        const inst = new Chart(ctx, {
            type: tipo,
            data: {
                labels: [curso.nombre_curso],
                datasets: [{ label: 'Participantes', data: [curso.total_participantes], backgroundColor: pal.bg, borderColor: pal.border, borderWidth: 0, borderRadius: 4 }]
            },
            options: _opcionesCursoChart(esCircOPol, esPolar)
        });
        instanciasGraficosCursos.push({ id: `${i}`, instance: inst });
    });

    // Gráfica combinada
    const canvasComb = document.getElementById('canvas-cursos-combinado');
    if (canvasComb) {
        const ctxC   = canvasComb.getContext('2d');
        const instC  = new Chart(ctxC, {
            type: 'bar',
            data: {
                labels:   cursos.map(c => c.nombre_curso),
                datasets: [{
                    label: 'Participantes',
                    data:            cursos.map(c => c.total_participantes),
                    backgroundColor: cursos.map(c => COLOR_CURSOS_PALETTE[cursosAprobDisp.findIndex(x => x.id === c.id) % COLOR_CURSOS_PALETTE.length].bg),
                    borderColor:     cursos.map(c => COLOR_CURSOS_PALETTE[cursosAprobDisp.findIndex(x => x.id === c.id) % COLOR_CURSOS_PALETTE.length].border),
                    borderWidth: 0, borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => `Participantes: ${c.parsed.y.toLocaleString()}` } } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.07)' }, title: { display: true, text: 'Número Participantes', color: '#64748b', font: { size: 11, weight: 600 } } },
                    x: { grid: { color: 'rgba(0,0,0,0.07)' } }
                }
            }
        });
        instanciasGraficosCursos.push({ id: 'combinado', instance: instC });
    }
}

function renderDetalleCursoInline(curso, contenedor) {
    const fi = curso.fecha_inicio ? new Date(curso.fecha_inicio).toLocaleDateString('es-VE') : '—';
    const ff = curso.fecha_fin    ? new Date(curso.fecha_fin).toLocaleDateString('es-VE')    : '—';
    contenedor.innerHTML = `
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-info text-white py-2">
            <h6 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/></svg>
                Detalle del Curso
            </h6>
        </div>
        <div class="card-body">
            <h6 class="fw-bold mb-1">${curso.nombre_curso}</h6>
            <div class="d-flex gap-3 mb-3 text-muted small flex-wrap">
                <span>📋 ${curso.modalidad || '—'}</span>
                <span>📅 ${fi} → ${ff}</span>
            </div>
            <div class="card border-0 bg-light mb-3"><div class="card-body py-2 text-center">
                <h4 class="fw-bold text-primary mb-0">${Number(curso.total_participantes).toLocaleString()}</h4>
                <small class="text-muted">Total de participantes</small>
            </div></div>
            <span class="badge bg-secondary">${curso.estatus || '—'}</span>
        </div>
    </div>`;
}

function renderDetalleCurso(curso) {
    const contenido = document.getElementById('detalleCursoContenido');
    if (!contenido) return;
    const fi = curso.fecha_inicio ? new Date(curso.fecha_inicio).toLocaleDateString('es-VE') : '—';
    const ff = curso.fecha_fin    ? new Date(curso.fecha_fin).toLocaleDateString('es-VE')    : '—';
    contenido.innerHTML = `
        <div class="mb-3"><h5 class="fw-bold">${curso.nombre_curso}</h5></div>
        <div class="row mb-3">
            <div class="col-6"><h6 class="text-muted small">Modalidad</h6><p class="fw-semibold mb-0">${curso.modalidad || '—'}</p></div>
            <div class="col-6"><h6 class="text-muted small">Estado</h6><span class="badge bg-secondary">${curso.estatus || '—'}</span></div>
        </div>
        <div class="row mb-3">
            <div class="col-6"><h6 class="text-muted small">Fecha Inicio</h6><p class="fw-semibold mb-0">${fi}</p></div>
            <div class="col-6"><h6 class="text-muted small">Fecha Fin</h6><p class="fw-semibold mb-0">${ff}</p></div>
        </div>
        <div class="card border-0 bg-light"><div class="card-body py-2 text-center">
            <h3 class="fw-bold text-primary mb-0">${Number(curso.total_participantes).toLocaleString()}</h3>
            <small class="text-muted">Total de participantes</small>
        </div></div>`;
}

// ====================================================================
// ─────────────────── GRÁFICO PRINCIPAL ──────────────────────────────
// ====================================================================

function getLabelTipoAprob(t) {
    return { evaluados: 'Evaluados', aprobados: 'Aprobados', reprobados: 'Reprobados' }[t] || t;
}

function aniosAprobOrdenados() {
    return [...aniosAprobActivos].sort((a, b) => a - b);
}

function renderGraficoAprob() {
    const wrapper = document.getElementById('graficoAprobWrapper');
    if (!wrapper) return;

    if (!tiposAprobActivos.size || !aniosAprobActivos.length) {
        destruirGraficoAprob();
        wrapper.innerHTML = `<div style="height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:#adb5bd;">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor" viewBox="0 0 16 16"><path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H1v3z"/></svg>
            <p class="mb-0 fw-semibold" style="font-size:1rem;">Seleccione al menos una métrica y un año</p>
        </div>`;
        document.getElementById('tituloAprobGrafico').textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) {
        wrapper.innerHTML = '<canvas id="canvasGraficoAprob"></canvas>';
    }

    destruirGraficoAprob();
    const ctx    = wrapper.querySelector('canvas').getContext('2d');
    const tipos  = ['evaluados', 'aprobados', 'reprobados'].filter(t => tiposAprobActivos.has(t));
    const años   = aniosAprobOrdenados();
    const nTipos = tipos.length;
    const nAnios = años.length;
    const unoTipo = nTipos === 1;
    const unoAnio = nAnios === 1;
    const esBar   = tipoGraficaAprob === 'bar';
    const labels  = obtenerNombresMesesAprob();

    const datasets = [];
    tipos.forEach((t, tipoIdx) => {
        años.forEach((anio, anioIdx) => {
            const datosAnio  = cacheAprobAnuales[anio] || [];
            const colorBorde = colorAprobBorde(t, anioIdx);
            let label = getLabelTipoAprob(t);
            if (nAnios > 1) label += ` ${anio}`;

            let fillConf = false;
            if (!esBar && unoAnio && unoTipo) {
                fillConf = { target: 'origin', above: colorAprobFill(t, anioIdx, 0.15) };
            }

            datasets.push({
                label,
                data:               datosAnio.map(m => m[t]),
                borderColor:        colorBorde,
                backgroundColor:    esBar ? colorAprobFill(t, anioIdx, 0.80) : colorAprobFill(t, anioIdx, 0.15),
                borderWidth:        esBar ? 0 : 2.5,
                borderRadius:       esBar ? 4 : 0,
                fill:               fillConf,
                tension:            0.4,
                pointBackgroundColor: colorBorde,
                pointBorderColor:   '#fff',
                pointBorderWidth:   2,
                pointRadius:        4,
                pointHoverRadius:   7,
                spanGaps:           false,
                order:              tipoIdx * 100 + anioIdx,
            });
        });
    });

    actualizarTituloAprobGrafico(tipos, años);

    graficoAprobInstance = new Chart(ctx, {
        type: esBar ? 'bar' : 'line',
        data: { labels, datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                filler: { propagate: true },
                tooltip: { mode: 'index', intersect: false, callbacks: { label: c => `${c.dataset.label}: ${c.parsed.y?.toLocaleString() ?? '—'}` } }
            },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.07)' } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.07)' }, title: { display: true, text: 'Número Participantes', color: '#64748b', font: { size: 11, weight: 600 } } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

function actualizarTituloAprobGrafico(tipos, años) {
    const el = document.getElementById('tituloAprobGrafico');
    if (!el) return;
    if (!tipos.length || !años.length) { el.textContent = ''; return; }
    el.textContent = `${tipos.map(t => getLabelTipoAprob(t)).join(', ')} — ${años.join(', ')}`;
}

// ====================================================================
// ─────────────────── CONTROLES GRÁFICO PRINCIPAL ────────────────────
// ====================================================================

function inicializarControlesAprobGrafico() {
    // Botones de métrica
    document.querySelectorAll('.btn-aprob-tipo').forEach(btn => {
        const c = btn.cloneNode(true);
        btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-aprob-tipo').forEach(btn => {
        btn.classList.toggle('active', tiposAprobActivos.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const tipo = btn.dataset.tipo;
            if (tiposAprobActivos.has(tipo)) tiposAprobActivos.delete(tipo);
            else tiposAprobActivos.add(tipo);
            btn.classList.toggle('active', tiposAprobActivos.has(tipo));
            renderGraficoAprob();
        });
    });

    const btnLT = document.getElementById('btnAprobLimpiarTipos');
    if (btnLT) {
        const c = btnLT.cloneNode(true);
        btnLT.parentNode.replaceChild(c, btnLT);
        document.getElementById('btnAprobLimpiarTipos').addEventListener('click', () => {
            tiposAprobActivos.clear();
            document.querySelectorAll('.btn-aprob-tipo').forEach(b => b.classList.remove('active'));
            renderGraficoAprob();
        });
    }

    // Botones tipo gráfica
    document.querySelectorAll('.btn-aprob-chart-type').forEach(btn => {
        const c = btn.cloneNode(true);
        btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-aprob-chart-type').forEach(btn => {
        const activo = btn.dataset.tipo === tipoGraficaAprob;
        btn.classList.toggle('active', activo);
        btn.classList.toggle('btn-secondary', activo);
        btn.classList.toggle('btn-outline-secondary', !activo);
        btn.addEventListener('click', () => {
            tipoGraficaAprob = btn.dataset.tipo;
            document.querySelectorAll('.btn-aprob-chart-type').forEach(b => {
                const a = b.dataset.tipo === tipoGraficaAprob;
                b.classList.toggle('active', a);
                b.classList.toggle('btn-secondary', a);
                b.classList.toggle('btn-outline-secondary', !a);
            });
            renderGraficoAprob();
        });
    });

    // Agregar año al gráfico
    const btnAddA = document.getElementById('btnAddAnioAprobGrafico');
    if (btnAddA) {
        const c = btnAddA.cloneNode(true);
        btnAddA.parentNode.replaceChild(c, btnAddA);
        document.getElementById('btnAddAnioAprobGrafico').addEventListener('click', async () => {
            const sel = document.getElementById('selectAnioAprobGrafico');
            if (!sel) return;
            const a = parseInt(sel.value);
            if (aniosAprobActivos.includes(a)) return;
            if (aniosAprobActivos.length >= MAX_ANIOS_APROB) {
                const av = document.getElementById('aprobAnioLimiteAviso');
                if (av) { av.style.display = 'inline-block'; setTimeout(() => { av.style.display = 'none'; }, 2500); }
                return;
            }
            try {
                await cargarDatosAnioAprob(a);
                aniosAprobActivos.push(a);
                aniosAprobActivos.sort((x, y) => x - y);
                sincronizarChipsAprobAnios();
                renderGraficoAprob();
            } catch (err) {
                console.error('[Aprobados] Error agregando año al gráfico:', err);
            }
        });
    }

    const btnLA = document.getElementById('btnLimpiarAprobAnios');
    if (btnLA) {
        const c = btnLA.cloneNode(true);
        btnLA.parentNode.replaceChild(c, btnLA);
        document.getElementById('btnLimpiarAprobAnios').addEventListener('click', () => {
            aniosAprobActivos = [];
            sincronizarChipsAprobAnios();
            renderGraficoAprob();
        });
    }

    sincronizarChipsAprobAnios();
}

function sincronizarChipsAprobAnios() {
    const chips = document.getElementById('chipsAprobAnios');
    if (!chips) return;
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;
    chips.innerHTML = aniosAprobActivos.map(a =>
        `<span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background:rgba(0,0,0,0.12);color:#333;font-size:.8rem;padding:.3em .65em;">
            ${a}<button class="btn-rm-aprob-anio border-0 bg-transparent p-0 ms-1" data-anio="${a}" style="color:#dc3545;line-height:1;cursor:pointer;">${svgX}</button>
        </span>`
    ).join('');
    chips.querySelectorAll('.btn-rm-aprob-anio').forEach(btn =>
        btn.addEventListener('click', () => {
            aniosAprobActivos = aniosAprobActivos.filter(x => x !== parseInt(btn.dataset.anio));
            sincronizarChipsAprobAnios();
            renderGraficoAprob();
        })
    );
}

// ====================================================================
// ─────────────────── LIMPIEZA ───────────────────────────────────────
// ====================================================================

function limpiarRecursosParticipantesAprobados() {
    destruirGraficoAprob();
    destruirGraficosCursos();
    datosAprobMensuales    = [];
    cacheAprobAnuales      = {};
    Object.keys(cacheAprobCursos).forEach(k => delete cacheAprobCursos[k]);
    añosDispAprobCache     = [];
    panelAprobActivo       = null;
    añosAprobGlobales      = [];
    aniosAprobActivos      = [];
    tiposAprobActivos      = new Set(['evaluados', 'aprobados', 'reprobados']);
    anioAprobBase          = null;
    anioAprobTabla         = null;
    cursosAprobDisp        = [];
    cursosAprobSel         = [];
    filtroAprobTiempo      = { tipo: 'anual', anio: new Date().getFullYear(), semestre: null, trimestre: null, mes: null };
    tipoGraficaAprob       = 'line';
    tipoGraficaCursosAprob = 'bar';
}

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarParticipantesAprobados          = cargarParticipantesAprobados;
window.limpiarRecursosParticipantesAprobados = limpiarRecursosParticipantesAprobados;

console.log('RE-Participante-Aprobados.js cargado');
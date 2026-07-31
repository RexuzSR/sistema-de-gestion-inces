// ========== REPORTE: TOTAL DE CURSOS ==========
// Archivo: RE-Curso-Total.js
// Función: cargarTotalCursos
// Conectado a: ../controlador/Reporte/api_reportes.php

const API_URL_TC = '../controlador/Reporte/api_reportes.php';

let datosMensualesCursos = [];
let graficoCursosInstance = null;
let cacheDatosAnualesCursos = {};
let cacheCursosMasParticipantes = {};
let anioCursosBase = null;

let tiposActivosCursos = new Set(['nuevos']);
let aniosActivosCursos = [];
let tipoGraficoCursos = 'line';
const MAX_ANIOS_CURSOS = 3;

let panelActivoCursos = null;
let añosGlobalesCursos = [];
let anioTablaCursos = null;

const COLOR_BASE_CURSOS = {
    nuevos: [23, 162, 184],
    activos: [40, 167, 69],
    culminados: [220, 53, 69],
    enEspera: [255, 193, 7]
};

// ── Helpers visuales ─────────────────────────────────────────────────

function colorBordeCursos(tipo, idxAnio, totalAnios) {
    const [r, g, b] = COLOR_BASE_CURSOS[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    const h = v => Math.round(v * f).toString(16).padStart(2, '0');
    return `#${h(r)}${h(g)}${h(b)}`;
}

function colorFillCursos(tipo, idxAnio, totalAnios, alpha = 0.18) {
    const [r, g, b] = COLOR_BASE_CURSOS[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    return `rgba(${Math.round(r * f)},${Math.round(g * f)},${Math.round(b * f)},${alpha})`;
}

function obtenerNombresMesesCursos() {
    return ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
}

function obtenerAñosDisponiblesCursos() {
    const hoy = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => hoy - i);
}

function destruirGraficoCursos() {
    if (graficoCursosInstance?.destroy) {
        graficoCursosInstance.destroy();
        graficoCursosInstance = null;
    }
}

function initTooltipsCursos(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}

// ── Fetch de datos mensuales (con caché) ──────────────────────────────

async function fetchDatosAnuoCursos(anio) {
    if (cacheDatosAnualesCursos[anio]) return cacheDatosAnualesCursos[anio];

    const res = await fetch(`${API_URL_TC}?accion=cursos_total&anio=${anio}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    // Mapear campos API → campos JS
    // API: nuevos=total, activos=en_proceso, retirados=culminado, en_espera, diferido
    const mapped = data.map(m => {
        const esFuturo = m.nuevos === null;
        return {
            mes: m.mes,
            mesNumero: m.mesNumero,
            nuevos: m.nuevos,
            activos: esFuturo ? null : (m.activos ?? 0),
            culminados: esFuturo ? null : (m.retirados ?? 0),
            enEspera: esFuturo ? null : (m.en_espera ?? 0),
            totalAcumulado: m.totalAcumulado
        };
    });

    cacheDatosAnualesCursos[anio] = mapped;
    return mapped;
}

// Acceso síncrono a caché (sólo llamar después de fetch)
function generarDatosAnuoCursos(anio) {
    return cacheDatosAnualesCursos[anio] || [];
}

function getDicAnteriorCursos(tipo, anio) {
    const datos = cacheDatosAnualesCursos[anio - 1];
    if (!datos) return null;
    return datos[11]?.[tipo] ?? null;
}

// ── Fetch de cursos más participantes (con caché) ─────────────────────

async function fetchCursosMasParticipantes(anio, limite = 30, estatus = '') {
    const key = `${anio}_${limite}_${estatus}`;
    if (cacheCursosMasParticipantes[key]) return cacheCursosMasParticipantes[key];

    const res = await fetch(`${API_URL_TC}?accion=cursos_mas_participantes&anio=${anio}&limite=${limite}&estatus=${encodeURIComponent(estatus)}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    cacheCursosMasParticipantes[key] = Array.isArray(data) ? data : [];
    return cacheCursosMasParticipantes[key];
}

// ── Modal de detalle de curso ─────────────────────────────────────────

function renderModalDetalleCurso() {
    return `
    <div id="modalDetalleCursoTC" class="rp-tc-modal" aria-modal="true" role="dialog">
      <div id="tcModalBackdrop" class="rp-tc-modal-backdrop"></div>
      <div class="rp-tc-modal-dialog">
        <div class="rp-tc-modal-content">

          <div class="rp-tc-modal-scan"></div>

          <!-- Header -->
          <div class="rp-tc-modal-header">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <div class="rp-tc-modal-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                       stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                  </svg>
                </div>
                <div>
                  <h4 class="rp-tc-modal-title">Detalles del Curso</h4>
                  <p class="rp-tc-modal-subtitle">INFORMACIÓN DEL PROGRAMA</p>
                </div>
              </div>
              <button type="button" id="tcModalCloseBtn" class="rp-tc-modal-close" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="#0281fe" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Body -->
          <div class="rp-tc-modal-body">
            <h3 id="tcModal-nombre" class="rp-tc-modal-nombre">—</h3>
            <div><span id="tcModal-estatus" class="rp-tc-estatus-badge">—</span></div>

            <div class="row g-4">
              <div class="col-md-6">
                <div class="rp-tc-modal-section rp-tc-modal-section--blue">
                  <p class="rp-tc-modal-section-label">Información del Curso</p>
                  <div id="tcModal-infoBasica"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="rp-tc-modal-section rp-tc-modal-section--green">
                  <p class="rp-tc-modal-section-label">Participación</p>
                  <div id="tcModal-infoParticipacion"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="rp-tc-modal-footer">
            <button type="button" id="tcModalCloseBtnFooter" class="rp-tc-modal-btn-close">Cerrar</button>
          </div>

        </div>
      </div>
    </div>`;
}

function cerrarModalDetalleCurso() {
    const m = document.getElementById('modalDetalleCursoTC');
    if (m) m.style.display = 'none';
}

function abrirModalDetalleCurso(cursoData) {
    document.getElementById('tcModal-nombre').textContent = cursoData.nombre_curso || '—';
    document.getElementById('tcModal-estatus').textContent = cursoData.estatus || '—';

    const rowHtml = (l, v) => `
        <div class="rp-tc-info-row">
            <span class="rp-tc-info-label">${l}</span>
            <span class="rp-tc-info-value">${v || '—'}</span>
        </div>`;

    const fmtFecha = s => {
        if (!s) return '—';
        try { return new Date(s).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' }); }
        catch { return s; }
    };

    document.getElementById('tcModal-infoBasica').innerHTML = [
        rowHtml('Modalidad', cursoData.modalidad),
        rowHtml('Estatus', cursoData.estatus),
        rowHtml('Inicio', fmtFecha(cursoData.fecha_inicio)),
        rowHtml('Culminación', fmtFecha(cursoData.fecha_fin))
    ].join('');

    document.getElementById('tcModal-infoParticipacion').innerHTML = [
        rowHtml('Total participantes', cursoData.total_participantes?.toLocaleString())
    ].join('');

    ['tcModalCloseBtn', 'tcModalCloseBtnFooter'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            const nb = btn.cloneNode(true);
            btn.parentNode.replaceChild(nb, btn);
            nb.addEventListener('click', cerrarModalDetalleCurso);
        }
    });

    const backdrop = document.getElementById('tcModalBackdrop');
    if (backdrop) {
        const nb = backdrop.cloneNode(false);
        backdrop.parentNode.replaceChild(nb, backdrop);
        nb.addEventListener('click', cerrarModalDetalleCurso);
    }

    const escHandler = e => {
        if (e.key === 'Escape') {
            cerrarModalDetalleCurso();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);

    document.getElementById('modalDetalleCursoTC').style.display = 'block';
}

// ── Función principal ─────────────────────────────────────────────────

async function cargarTotalCursos() {
    console.log('🚀 Iniciando cargarTotalCursos...');
    const contentDiv = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 2;
    const categorias = window.categoriasReportes || [];
    const categoria = categorias.find(c => c.id === categoriaId) || { color: 'success' };
    const hoy = new Date().getFullYear();
    const añosDisp = obtenerAñosDisponiblesCursos();

    destruirGraficoCursos();
    cacheDatosAnualesCursos = {};
    cacheCursosMasParticipantes = {};
    panelActivoCursos = null;
    añosGlobalesCursos = [hoy];
    anioCursosBase = hoy;
    anioTablaCursos = hoy;
    tiposActivosCursos = new Set(['nuevos']);
    aniosActivosCursos = [hoy];
    tipoGraficoCursos = 'line';

    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    ${renderModalDetalleCurso()}

    <div class="card shadow-lg border-0">
        <div class="card-header bg-${categoria.color} text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">
                    <path fill-rule="evenodd" d="M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z"/>
                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                </svg>Total de Cursos
            </h4>
        </div>
        <div class="card-body">

            <div id="loadingTC" class="text-center py-5">
                <div class="spinner-border text-${categoria.color}" role="status"></div>
                <p class="mt-3 text-muted" id="loadingMsgTC">Cargando datos...</p>
            </div>

            <div id="errorTC" class="alert alert-danger" style="display:none;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="errorMsgTC">Error al cargar los datos.</span>
            </div>

            <div id="contenidoTC" style="display:none;">

                <div id="kpiCardsCursos"></div>
                <div id="panelDetalleWrapperCursos" style="display:none;" class="mb-3"></div>

                <!-- Controles gráfico -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-start gap-3">

                            <div style="flex:1 1 auto;min-width:220px;">
                                <div class="rp-toolbar-label mb-2">Dato a representar</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-primary btn-tipo-curso active" data-tipo="nuevos">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>Nuevos
                                    </button>
                                    <button class="btn btn-sm btn-outline-success btn-tipo-curso" data-tipo="activos">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Activos
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-tipo-curso" data-tipo="culminados">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>Culminados
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning btn-tipo-curso" data-tipo="enEspera">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>En espera
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarTiposCursos">${svgX} Limpiar</button>
                                </div>
                            </div>

                            <div class="rp-vdivider d-none d-md-block"></div>

                            <div style="flex:0 1 auto;min-width:200px;">
                                <div class="rp-toolbar-label mb-2">
                                    Años a comparar
                                    <span id="tcAnioLimiteAviso" class="badge bg-warning text-dark ms-1" style="display:none;font-size:.65rem;">máx. ${MAX_ANIOS_CURSOS}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <select class="form-select form-select-sm" id="selectAnioCursosGrafico" style="width:90px;">
                                        ${añosDisp.map(a => `<option value="${a}">${a}</option>`).join('')}
                                    </select>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnAddAnioCursosGrafico">${svgPlus} Agregar</button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarAniosCursos">${svgX} Limpiar</button>
                                </div>
                                <div id="chipsAniosCursos" class="d-flex gap-1 flex-wrap mt-2"></div>
                            </div>

                            <div class="rp-vdivider d-none d-md-block"></div>

                            <div style="flex:0 0 auto;">
                                <div class="rp-toolbar-label mb-2">Tipo de gráfica</div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type active" id="btnChartLine" data-tipo="line" title="Líneas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/></svg> Líneas
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type" id="btnChartBar" data-tipo="bar" title="Torres">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg> Torres
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Gráfico -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloGraficoCursos"></h6>
                        <div id="graficoWrapperCursos" class="rp-chart-wrap rp-chart-wrap--lg"></div>
                    </div>
                </div>

                <!-- Tabla mensual -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h5 class="card-title mb-0">
                                    Registro Mensual — <span id="anioSeleccionadoCursos">${hoy}</span>
                                </h5>
                                <select id="selectAnioTablaCursos" class="form-select form-select-sm" style="width:110px;">
                                    ${añosDisp.map(a => `<option value="${a}" ${a === hoy ? 'selected' : ''}>${a}</option>`).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th>Nuevos cursos</th>
                                        <th>Cursos activos</th>
                                        <th>Culminados</th>
                                        <th>En espera</th>
                                        <th>Total acumulado</th>
                                        <th>
                                            Var. mensual (nuevos)
                                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Diferencia de nuevos cursos respecto al mes anterior."
                                                class="rp-tooltip-icon">!</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tablaMensualCursos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- /contenidoTC -->
        </div>
    </div>`;

    initTooltipsCursos(contentDiv);
    await cargarDatosTC(hoy, true);

    document.getElementById('selectAnioTablaCursos')?.addEventListener('change', async (e) => {
        const nuevoAnio = parseInt(e.target.value);
        if (nuevoAnio > new Date().getFullYear()) return;
        await cambiarAnioTablaCursos(nuevoAnio);
    });
}

// ── Carga de datos del año seleccionado ───────────────────────────────

async function cargarDatosTC(anio, esCargaInicial = false) {
    const loadEl = document.getElementById('loadingTC');
    const errorEl = document.getElementById('errorTC');
    const contenEl = document.getElementById('contenidoTC');

    if (loadEl) loadEl.style.display = 'block';
    if (errorEl) errorEl.style.display = 'none';
    if (contenEl) contenEl.style.display = 'none';

    const msgEl = document.getElementById('loadingMsgTC');
    if (msgEl) msgEl.textContent = `Cargando datos de cursos del año ${anio}...`;

    const estadoAntes = esCargaInicial ? null : {
        tiposActivos: new Set(tiposActivosCursos),
        aniosActivos: [...aniosActivosCursos]
    };

    try {
        const datos = await fetchDatosAnuoCursos(anio);
        datosMensualesCursos = datos;
        anioCursosBase = anio;
        anioTablaCursos = anio;

        const conDatos = datos.filter(m => m.nuevos !== null);
        const ultimo = conDatos[conDatos.length - 1];
        const resumen = buildResumenCursos(conDatos, ultimo);

        if (loadEl) loadEl.style.display = 'none';
        if (contenEl) contenEl.style.display = 'block';

        document.getElementById('anioSeleccionadoCursos').textContent = anio;
        const selectTabla = document.getElementById('selectAnioTablaCursos');
        if (selectTabla) selectTabla.value = anio;

        if (esCargaInicial) {
            aniosActivosCursos = [anio];
            añosGlobalesCursos = [anio];
        } else {
            tiposActivosCursos = estadoAntes.tiposActivos;
            aniosActivosCursos = estadoAntes.aniosActivos;
            if (!aniosActivosCursos.includes(anio)) {
                aniosActivosCursos.push(anio);
                aniosActivosCursos.sort((a, b) => b - a);
            }
            if (!añosGlobalesCursos.includes(anio)) {
                añosGlobalesCursos = [anio, ...añosGlobalesCursos].sort((a, b) => b - a);
            }
        }

        renderKPIsCursos(resumen);
        renderTablaMensualCursos(datos);
        sincronizarChipsAniosCursos();
        renderGraficoCursos();
        inicializarControlesGraficoCursos();
        if (panelActivoCursos) await renderPanelDetalleCursos(panelActivoCursos);

    } catch (err) {
        if (loadEl) loadEl.style.display = 'none';
        if (errorEl) {
            errorEl.style.display = 'block';
            document.getElementById('errorMsgTC').textContent =
                `Error al cargar los datos del año ${anio}: ${err.message}`;
        }
        console.error('[RE-Curso-Total] Error al cargar datos:', err);
    }
}

function buildResumenCursos(conDatos, ultimo) {
    const totalNuevos = conDatos.reduce((s, m) => s + (m.nuevos || 0), 0);
    const totalCulminados = conDatos.reduce((s, m) => s + (m.culminados || 0), 0);
    // Sumar activos de todos los meses: la API agrupa por mes de creación,
    // por lo que un curso creado en Mayo que sigue "En proceso" en Junio
    // solo aparece en Mayo. Usar solo el último mes daría 0 incorrectamente.
    const activos = conDatos.reduce((s, m) => s + (m.activos || 0), 0);
    const enEspera = ultimo?.enEspera || 0;
    const tasaCompletitud = totalNuevos > 0
        ? ((totalCulminados / totalNuevos) * 100).toFixed(1)
        : '—';
    return {
        totalNuevos, activos, totalCulminados, enEspera, tasaCompletitud,
        totalAcumulado: ultimo?.totalAcumulado || 0
    };
}

async function cambiarAnioTablaCursos(anio) {
    if (anio === anioTablaCursos) return;

    const loadEl = document.getElementById('loadingTC');
    const errorEl = document.getElementById('errorTC');
    if (loadEl) loadEl.style.display = 'block';
    if (errorEl) errorEl.style.display = 'none';
    const msgEl = document.getElementById('loadingMsgTC');
    if (msgEl) msgEl.textContent = `Cargando datos del año ${anio}...`;

    try {
        const datosAnio = await fetchDatosAnuoCursos(anio);
        anioTablaCursos = anio;
        document.getElementById('anioSeleccionadoCursos').textContent = anio;
        const conDatos = datosAnio.filter(m => m.nuevos !== null);
        const ultimo = conDatos[conDatos.length - 1];
        renderKPIsCursos(buildResumenCursos(conDatos, ultimo));
        renderTablaMensualCursos(datosAnio);
    } catch (err) {
        if (errorEl) {
            errorEl.style.display = 'block';
            document.getElementById('errorMsgTC').textContent = `Error al cargar ${anio}: ${err.message}`;
        }
    } finally {
        if (loadEl) loadEl.style.display = 'none';
    }
}

// ── KPI Cards ─────────────────────────────────────────────────────────

function renderKPIsCursos(data) {
    const el = document.getElementById('kpiCardsCursos');
    if (!el) return;

    const svgNuevo = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgActivo = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>`;
    const svgCulm = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>`;
    const svgEspera = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>`;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const cards = [
        { tipo: 'nuevos', valor: data.totalNuevos, label: 'Nuevos Cursos', sub: 'creados en el año', color: 'primary', svg: svgNuevo },
        { tipo: 'activos', valor: data.activos, label: 'Cursos Activos', sub: 'en proceso (último mes)', color: 'success', svg: svgActivo },
        { tipo: 'culminados', valor: data.totalCulminados, label: 'Cursos Culminados', sub: 'finalizados en el año', color: 'danger', svg: svgCulm },
        { tipo: 'tasaCompletitud', valor: `${data.tasaCompletitud}%`, label: 'Tasa de Completitud', sub: 'culminados vs nuevos', color: 'warning', svg: svgEspera }
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 kpi-card kpi-card-curso" role="button" data-tipo="${c.tipo}">
                <div class="card-body text-center py-3">
                    <span class="text-${c.color}">${c.svg}</span>
                    <h2 class="fw-bold text-${c.color} mt-2 mb-0">
                        ${typeof c.valor === 'number' ? c.valor.toLocaleString() : c.valor}
                    </h2>
                    <p class="rp-kpi-card-label">${c.label}</p>
                    <p class="rp-kpi-card-sub">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelCurso-${c.tipo}">
                        ${svgDown}${c.tipo === 'tasaCompletitud' ? 'Ver tabla' : 'Ver cursos'}
                    </span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-curso').forEach(card =>
        card.addEventListener('click', () => togglePanelDetalleCursos(card.dataset.tipo))
    );
}

// ── Panel de detalle ──────────────────────────────────────────────────

async function togglePanelDetalleCursos(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperCursos');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['nuevos', 'activos', 'culminados', 'tasaCompletitud'].forEach(t => {
        const b = document.getElementById(`badgePanelCurso-${t}`);
        if (b) b.innerHTML = `${svgDown}${t === 'tasaCompletitud' ? 'Ver tabla' : 'Ver cursos'}`;
    });

    if (panelActivoCursos === tipo) {
        wrapper.style.display = 'none';
        panelActivoCursos = null;
        return;
    }

    panelActivoCursos = tipo;
    wrapper.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary"></div><p class="mt-2 text-muted">Cargando cursos...</p></div>`;
    wrapper.style.display = 'block';

    await renderPanelDetalleCursos(tipo);

    const badge = document.getElementById(`badgePanelCurso-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' }), 50);
}

const CFG_PANEL_CURSOS = {
    nuevos: { titulo: 'Cursos con más participantes', color: 'primary', svgPath: '<path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>' },
    activos: { titulo: 'Cursos en Proceso', color: 'success', svgPath: '<path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>' },
    culminados: { titulo: 'Cursos Culminados', color: 'danger', svgPath: '<path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>' },
    tasaCompletitud: { titulo: 'Tasa de Completitud', color: 'warning', svgPath: '<path d="M2 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>' }
};

async function renderPanelDetalleCursos(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperCursos');
    if (!wrapper) return;

    const anio = anioTablaCursos || new Date().getFullYear();

    try {
        let estatusFiltro = '';
        if (tipo === 'activos') {
            estatusFiltro = 'proceso';
        } else if (tipo === 'culminados') {
            estatusFiltro = 'culminad';
        }

        const cursos = await fetchCursosMasParticipantes(anio, 30, estatusFiltro);
        const cfg = CFG_PANEL_CURSOS[tipo];

        // Filtrar según tipo
        let listaMostrar;
        if (tipo === 'activos') {
            listaMostrar = cursos.filter(c => (c.estatus || '').toLowerCase().includes('proceso'));
        } else if (tipo === 'culminados') {
            listaMostrar = cursos.filter(c => (c.estatus || '').toLowerCase().includes('culminad'));
        } else {
            listaMostrar = cursos;
        }

        if (tipo === 'tasaCompletitud') {
            // Vista tabla de completitud
            const fmtF = s => {
                try { return new Date(s).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' }); }
                catch { return s || '—'; }
            };
            const filas = cursos.map(c => `
                <tr>
                    <td class="fw-semibold rp-tc-cell-trunc" title="${c.nombre_curso}">${c.nombre_curso}</td>
                    <td><span class="badge bg-light text-dark border">${c.estatus || '—'}</span></td>
                    <td>${c.modalidad || '—'}</td>
                    <td class="text-center">${parseInt(c.total_participantes || 0).toLocaleString()}</td>
                    <td>${fmtF(c.fecha_inicio)} → ${fmtF(c.fecha_fin)}</td>
                </tr>`).join('');

            wrapper.innerHTML = `
            <div class="card border-0 shadow-sm rp-card-top" data-color="${cfg.color}">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h6 class="mb-0 text-${cfg.color} d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">${cfg.svgPath}</svg>
                            ${cfg.titulo}
                            <span class="badge bg-${cfg.color} bg-opacity-20 text-${cfg.color} ms-1">${cursos.length} cursos</span>
                        </h6>
                        <small class="text-muted">Año ${anio}</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Curso</th><th>Estatus</th><th>Modalidad</th>
                                    <th class="text-center">Participantes</th><th>Período</th>
                                </tr>
                            </thead>
                            <tbody>${filas || '<tr><td colspan="5" class="text-center text-muted py-3">Sin datos disponibles</td></tr>'}</tbody>
                        </table>
                    </div>
                </div>
            </div>`;
            return;
        }

        // Vista tarjetas
        if (!listaMostrar.length) {
            wrapper.innerHTML = `
            <div class="card border-0 shadow-sm rp-card-top" data-color="${cfg.color}">
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No se encontraron cursos con estatus "${tipo}" para el año ${anio}.
                </div>
            </div>`;
            return;
        }

        const fmtF = s => {
            try { return new Date(s).toLocaleDateString('es-ES', { month: 'short', day: 'numeric' }); }
            catch { return s || '—'; }
        };

        const tarjetas = listaMostrar.slice(0, 12).map((c, idx) => `
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rp-curso-card"
                     style="border-left:3px solid var(--bs-${cfg.color},#0d6efd)!important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                            <h6 class="fw-semibold mb-0 text-truncate rp-report-node-name"
                                title="${c.nombre_curso}">${c.nombre_curso}</h6>
                            <span class="badge bg-light text-dark border"
                                  style="white-space:nowrap;font-size:.7rem;flex-shrink:0;">${c.estatus || '—'}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="rp-badge rp-badge--neutral">${c.modalidad || '—'}</span>
                            <span class="rp-badge rp-badge--neutral">${fmtF(c.fecha_inicio)} – ${fmtF(c.fecha_fin)}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.78rem;">
                            <span class="text-muted">Participantes:</span>
                            <strong class="text-${cfg.color}">${parseInt(c.total_participantes || 0).toLocaleString()}</strong>
                        </div>
                        <button class="btn btn-sm btn-outline-${cfg.color} w-100 btn-ver-curso-tc" data-curso-idx="${idx}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>Ver detalle
                        </button>
                    </div>
                </div>
            </div>`).join('');

        wrapper.innerHTML = `
        <div class="card border-0 shadow-sm rp-card-top" data-color="${cfg.color}">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 text-${cfg.color} d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">${cfg.svgPath}</svg>
                        ${cfg.titulo}
                        <span class="badge bg-${cfg.color} bg-opacity-10 text-${cfg.color} ms-1">${listaMostrar.length}</span>
                    </h6>
                    <small class="text-muted">Año ${anio}</small>
                </div>
                <div class="row g-3">${tarjetas}</div>
            </div>
        </div>`;

        wrapper.querySelectorAll('.btn-ver-curso-tc').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.cursoIdx);
                abrirModalDetalleCurso(listaMostrar[idx]);
            });
        });

    } catch (err) {
        wrapper.innerHTML = `<div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>No se pudieron cargar los detalles: ${err.message}
        </div>`;
        console.error('[RE-Curso-Total] Error al cargar panel detalle:', err);
    }
}

// ── Tabla mensual ─────────────────────────────────────────────────────

function renderTablaMensualCursos(datos) {
    const tbody = document.getElementById('tablaMensualCursos');
    if (!tbody) return;

    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/></svg>`;
    const svgDw = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/></svg>`;
    const svgDs = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/></svg>`;

    let totNuevos = 0, totActivos = 0, totCulm = 0, totEspera = 0, acum = 0;

    const filas = datos.map((mes, idx) => {
        const sd = mes.nuevos === null;
        const vNuevos = sd ? null : (mes.nuevos || 0);
        const vActivos = sd ? null : (mes.activos || 0);
        const vCulm = sd ? null : (mes.culminados || 0);
        const vEspera = sd ? null : (mes.enEspera || 0);

        if (!sd) {
            acum += vNuevos;
            totNuevos += vNuevos;
            totActivos += vActivos;
            totCulm += vCulm;
            totEspera += vEspera;
        }

        let crec = '<span class="text-muted">—</span>';
        if (!sd && vNuevos !== null) {
            const antArr = datos.slice(0, idx).reverse();
            const antIdx = antArr.findIndex(m => m.nuevos !== null);
            if (antIdx >= 0) {
                const vAnt = antArr[antIdx].nuevos || 0;
                if (vAnt > 0) {
                    const diff = vNuevos - vAnt;
                    const pct = ((diff / vAnt) * 100).toFixed(1);
                    const color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                    const icono = diff > 0 ? svgUp : diff < 0 ? svgDw : svgDs;
                    const signo = diff > 0 ? '+' : '';
                    crec = `<span class="${color}">${icono}${signo}${diff.toLocaleString()} <small>(${signo}${pct}%)</small></span>`;
                } else {
                    crec = '<span class="text-muted">Sin referencia</span>';
                }
            } else {
                crec = '<span class="text-muted">Primer mes</span>';
            }
        }

        const fmt = v => v !== null ? v.toLocaleString() : '—';
        return `<tr>
            <td><strong>${mes.mes}</strong></td>
            <td>${fmt(vNuevos)}</td>
            <td>${fmt(vActivos)}</td>
            <td>${fmt(vCulm)}</td>
            <td>${fmt(vEspera)}</td>
            <td>${sd ? '—' : acum.toLocaleString()}</td>
            <td>${sd ? '<span class="text-muted">—</span>' : crec}</td>
        </tr>`;
    }).join('');

    tbody.innerHTML = filas + `
        <tr class="rp-row-total-tp">
            <td>Total</td>
            <td>${totNuevos.toLocaleString()}</td>
            <td>${totActivos.toLocaleString()}</td>
            <td>${totCulm.toLocaleString()}</td>
            <td>${totEspera.toLocaleString()}</td>
            <td>${acum.toLocaleString()}</td>
            <td>—</td>
        </tr>`;
}

// ── Gráfico ───────────────────────────────────────────────────────────

function getLabelTipoCurso(t) {
    return { nuevos: 'Nuevos', activos: 'Activos', culminados: 'Culminados', enEspera: 'En espera' }[t] || t;
}

function aniosOrdenadosCursos() {
    return [...aniosActivosCursos].sort((a, b) => b - a);
}

function renderGraficoCursos() {
    const wrapper = document.getElementById('graficoWrapperCursos');
    if (!wrapper) return;

    if (!tiposActivosCursos.size || !aniosActivosCursos.length) {
        destruirGraficoCursos();
        wrapper.innerHTML = `
            <div class="rp-chart-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H1v3z"/>
                </svg>
                <p class="rp-chart-empty-text">Seleccione al menos una métrica y un año para visualizar</p>
                <p class="mb-0 text-muted small">Use los controles ubicados arriba</p>
            </div>`;
        document.getElementById('tituloGraficoCursos').textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) {
        wrapper.innerHTML = '<canvas id="canvasGraficoCursos"></canvas>';
    }

    destruirGraficoCursos();
    const ctx = wrapper.querySelector('canvas').getContext('2d');
    const tipos = ['nuevos', 'activos', 'culminados', 'enEspera'].filter(t => tiposActivosCursos.has(t));
    const años = aniosOrdenadosCursos();
    const nAnios = años.length;
    const nTipos = tipos.length;
    const unoTipo = nTipos === 1;
    const unoAnio = nAnios === 1;
    const esBar = tipoGraficoCursos === 'bar';
    const usarDic = unoTipo && !esBar;

    const labels = usarDic
        ? [`Dic ${Math.max(...años) - 1}`, ...obtenerNombresMesesCursos()]
        : obtenerNombresMesesCursos();

    // Para unoAnio en línea, ordenar tipos por promedio descendente (mayor al fondo = área más grande)
    const tiposOrdenados = (unoAnio && !esBar) ? [...tipos].sort((a, b) => {
        const datos = generarDatosAnuoCursos(años[0]);
        const avg = t => datos.filter(m => m[t] !== null).reduce((s, m) => s + (m[t] || 0), 0) /
            (datos.filter(m => m[t] !== null).length || 1);
        return avg(b) - avg(a);
    }) : tipos;

    const datasets = [];

    tiposOrdenados.forEach((tipo, tipoIdx) => {
        años.forEach((anio, anioIdx) => {
            const datosAnio = generarDatosAnuoCursos(anio);
            const colorBorde = colorBordeCursos(tipo, anioIdx, nAnios);
            const colorFill = colorFillCursos(tipo, anioIdx, nAnios, esBar ? 0.75 : (unoTipo && unoAnio ? 0.22 : 0.18));
            const datos = usarDic
                ? [getDicAnteriorCursos(tipo, anio), ...datosAnio.map(m => m[tipo])]
                : datosAnio.map(m => m[tipo]);
            let label = getLabelTipoCurso(tipo);
            if (nAnios > 1) label += ` ${anio}`;

            // Fill — misma lógica que RE-Participante-Total.js
            let fillConf = false;
            if (!esBar) {
                if (unoTipo) {
                    fillConf = anioIdx === nAnios - 1
                        ? { target: 'origin', above: colorFill }
                        : { target: `+${nTipos}`, above: colorFill };
                } else if (unoAnio) {
                    const posOrden = tiposOrdenados.indexOf(tipo);
                    fillConf = posOrden === nTipos - 1
                        ? { target: 'origin', above: colorFill }
                        : { target: `+1`, above: colorFill };
                }
            }

            const ds = {
                label,
                data: datos,
                borderColor: colorBorde,
                backgroundColor: colorFill,
                borderWidth: esBar ? 0 : (unoTipo && unoAnio ? 2.5 : 2),
                fill: esBar ? false : fillConf,
                tension: esBar ? 0 : 0.4,
                borderRadius: esBar ? 4 : 0,
                spanGaps: false,
                order: tipoIdx * 100 + anioIdx
            };

            if (!esBar) {
                if (usarDic) {
                    ds.segment = {
                        borderDash: c => c.p0DataIndex === 0 ? [5, 4] : undefined,
                        borderColor: c => c.p0DataIndex === 0 ? colorBorde + '88' : colorBorde
                    };
                    ds.pointBackgroundColor = c => c.dataIndex === 0 ? colorBorde + '55' : colorBorde;
                    ds.pointRadius = c => c.dataIndex === 0 ? 3 : 4;
                } else {
                    ds.pointBackgroundColor = colorBorde;
                    ds.pointRadius = 4;
                }
                ds.pointBorderColor = '#fff';
                ds.pointBorderWidth = 2;
                ds.pointHoverRadius = 7;
            }

            datasets.push(ds);
        });
    });

    document.getElementById('tituloGraficoCursos').textContent =
        `${tipos.map(getLabelTipoCurso).join(', ')} — ${años.join(', ')}`;

    graficoCursosInstance = new Chart(ctx, {
        type: tipoGraficoCursos,
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
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
                    stacked: false,
                    grid: { color: 'rgba(0,0,0,0.07)' },
                    ticks: {
                        color: c => (usarDic && c.index === 0) ? 'rgba(140,140,140,0.55)' : undefined,
                        font: c => (usarDic && c.index === 0) ? { style: 'italic' } : {}
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.07)' },
                    title: { display: true, text: 'Numero de Cursos' }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

// ── Controles del gráfico ─────────────────────────────────────────────

function inicializarControlesGraficoCursos() {
    // Botones de tipo métrica
    document.querySelectorAll('.btn-tipo-curso').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-tipo-curso').forEach(btn => {
        btn.classList.toggle('active', tiposActivosCursos.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const tipo = btn.dataset.tipo;
            tiposActivosCursos.has(tipo) ? tiposActivosCursos.delete(tipo) : tiposActivosCursos.add(tipo);
            btn.classList.toggle('active', tiposActivosCursos.has(tipo));
            renderGraficoCursos();
        });
    });

    const btnLT = document.getElementById('btnLimpiarTiposCursos');
    if (btnLT) {
        const clon = btnLT.cloneNode(true);
        btnLT.parentNode.replaceChild(clon, btnLT);
        document.getElementById('btnLimpiarTiposCursos').addEventListener('click', () => {
            tiposActivosCursos.clear();
            document.querySelectorAll('.btn-tipo-curso').forEach(b => b.classList.remove('active'));
            renderGraficoCursos();
        });
    }

    // Botones tipo gráfica
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tipo === tipoGraficoCursos);
        if (btn.classList.contains('active')) { btn.classList.remove('btn-outline-secondary'); btn.classList.add('btn-secondary'); }
        btn.addEventListener('click', () => {
            tipoGraficoCursos = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type').forEach(b => {
                b.classList.toggle('active', b.dataset.tipo === tipoGraficoCursos);
                if (b.classList.contains('active')) {
                    b.classList.remove('btn-outline-secondary'); b.classList.add('btn-secondary');
                } else {
                    b.classList.remove('btn-secondary'); b.classList.add('btn-outline-secondary');
                }
            });
            renderGraficoCursos();
        });
    });

    // Agregar año al gráfico
    const btnAddA = document.getElementById('btnAddAnioCursosGrafico');
    if (btnAddA) {
        const clon = btnAddA.cloneNode(true);
        btnAddA.parentNode.replaceChild(clon, btnAddA);
        document.getElementById('btnAddAnioCursosGrafico').addEventListener('click', async () => {
            const sel = document.getElementById('selectAnioCursosGrafico');
            if (!sel) return;
            const a = parseInt(sel.value);
            if (a > new Date().getFullYear() || aniosActivosCursos.includes(a)) return;
            if (aniosActivosCursos.length >= MAX_ANIOS_CURSOS) {
                const aviso = document.getElementById('tcAnioLimiteAviso');
                if (aviso) {
                    aviso.style.display = 'inline-block';
                    setTimeout(() => { aviso.style.display = 'none'; }, 2500);
                }
                return;
            }
            try {
                await fetchDatosAnuoCursos(a);
                // Pre-cargar año anterior para el punto Dic anterior
                if (!cacheDatosAnualesCursos[a - 1]) {
                    try { await fetchDatosAnuoCursos(a - 1); } catch (_) { }
                }
                aniosActivosCursos.push(a);
                aniosActivosCursos.sort((x, y) => y - x);
                sincronizarChipsAniosCursos();
                renderGraficoCursos();
            } catch (err) {
                console.warn(`[RE-Curso-Total] No se pudo cargar año ${a}:`, err);
            }
        });
    }

    // Limpiar años
    const btnLA = document.getElementById('btnLimpiarAniosCursos');
    if (btnLA) {
        const clon = btnLA.cloneNode(true);
        btnLA.parentNode.replaceChild(clon, btnLA);
        document.getElementById('btnLimpiarAniosCursos').addEventListener('click', () => {
            aniosActivosCursos = [];
            sincronizarChipsAniosCursos();
            renderGraficoCursos();
        });
    }

    sincronizarChipsAniosCursos();
}

function sincronizarChipsAniosCursos() {
    const chips = document.getElementById('chipsAniosCursos');
    if (!chips) return;
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;

    chips.innerHTML = aniosActivosCursos.map(a =>
        `<span class="rp-year-chip">
            ${a}
            <button class="btn-rm-anio-curso rp-year-chip-remove" data-anio="${a}" title="Quitar ${a}">${svgX}</button>
        </span>`
    ).join('');

    chips.querySelectorAll('.btn-rm-anio-curso').forEach(btn =>
        btn.addEventListener('click', () => {
            aniosActivosCursos = aniosActivosCursos.filter(x => x !== parseInt(btn.dataset.anio));
            sincronizarChipsAniosCursos();
            renderGraficoCursos();
        })
    );

    const sel = document.getElementById('selectAnioCursosGrafico');
    if (sel) {
        const prev = parseInt(sel.value);
        sel.innerHTML = obtenerAñosDisponiblesCursos().map(a =>
            `<option value="${a}" ${a === prev ? 'selected' : ''}>${a}</option>`
        ).join('');
    }
}

// ── Limpieza ──────────────────────────────────────────────────────────

function limpiarRecursosTotalCursos() {
    destruirGraficoCursos();
    datosMensualesCursos = [];
    cacheDatosAnualesCursos = {};
    cacheCursosMasParticipantes = {};
    panelActivoCursos = null;
    añosGlobalesCursos = [];
    aniosActivosCursos = [];
    tiposActivosCursos = new Set(['nuevos']);
    tipoGraficoCursos = 'line';
    anioCursosBase = null;
    anioTablaCursos = null;
    cerrarModalDetalleCurso();
}

// ── Registro global ───────────────────────────────────────────────────
window.cargarTotalCursos = cargarTotalCursos;
window.limpiarRecursosTotalCursos = limpiarRecursosTotalCursos;

console.log('✅ RE-Curso-Total.js cargado — conectado a CSS centralizado');
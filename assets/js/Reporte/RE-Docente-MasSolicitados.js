// ========== REPORTE: DOCENTES MÁS SOLICITADOS ==========
// Conectado a: api_reportes.php
// Acciones usadas: docentes_mas_solicitados, docentes_total, anios_disponibles

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_DOCENTES_SOL = '../controlador/Reporte/api_reportes.php';

let docentesSolicitadosChart = null;
let docentesSolicitadosData  = [];
let anioDocentesSol          = new Date().getFullYear();
let docSolTotalesActuales    = null;   // totales del año activo (para paneles de KPI)
let docSolPanelActivo        = null;   // 'total'|'cursos'|'inactivos'|'top'|null

// ─── Helper: fetch con manejo de errores ─────────────────────────────────────
async function apiFetchSol(accion, params = {}) {
    const url = new URL(API_URL_DOCENTES_SOL, window.location.href);
    url.searchParams.set('accion', accion);
    url.searchParams.set('anio', anioDocentesSol);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));

    const resp = await fetch(url.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();
    if (data.error) throw new Error(data.error);
    return data;
}

// ─── Función principal ────────────────────────────────────────────────────────
async function cargarDocentesMasSolicitados() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 3;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                     || { color: 'warning', nombre: 'Docentes' };

    // Reset de estado
    docentesSolicitadosChart = null;
    docentesSolicitadosData  = [];
    docSolTotalesActuales    = null;
    docSolPanelActivo        = null;

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-person-badge-fill me-2"></i>Docentes más Solicitados</h4>
                        <p class="mb-0 small opacity-75">Análisis de participación y asignación de cursos</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-white small fw-semibold mb-0">Año:</label>
                        <select id="selectAnioDocentesSol" class="form-select form-select-sm" style="width:110px;"></select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="loadingDocentesSolicitados">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
                </div>
                <div id="contenidoDocentesSolicitados" style="display:none;"></div>
                <div id="errorDocentesSolicitados" style="display:none;" class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="errorMsgDocSol"></span>
                </div>
            </div>
        </div>`;

    // Cargar años disponibles
    try {
        const anios  = await apiFetchSol('anios_disponibles');
        const select = document.getElementById('selectAnioDocentesSol');
        if (select && Array.isArray(anios) && anios.length > 0) {
            anios.forEach(a => {
                const opt       = document.createElement('option');
                opt.value       = a;
                opt.textContent = a;
                if (a === anioDocentesSol) opt.selected = true;
                select.appendChild(opt);
            });
            select.addEventListener('change', e => {
                anioDocentesSol   = parseInt(e.target.value);
                docSolPanelActivo = null;
                cargarContenidoDocSol();
            });
        }
    } catch (_) { /* continuar con el año actual */ }

    cargarContenidoDocSol();
}

// ─── Carga principal de contenido ────────────────────────────────────────────
async function cargarContenidoDocSol() {
    const loadEl  = document.getElementById('loadingDocentesSolicitados');
    const contEl  = document.getElementById('contenidoDocentesSolicitados');
    const errorEl = document.getElementById('errorDocentesSolicitados');
    const errMsg  = document.getElementById('errorMsgDocSol');

    loadEl.style.display       = 'flex';
    loadEl.style.flexDirection = 'column';
    loadEl.style.alignItems    = 'center';
    contEl.style.display       = 'none';
    errorEl.style.display      = 'none';
    docSolPanelActivo          = null;

    if (docentesSolicitadosChart) {
        docentesSolicitadosChart.destroy();
        docentesSolicitadosChart = null;
    }

    try {
        const [ranking, totalesObj] = await Promise.all([
            apiFetchSol('docentes_mas_solicitados', { limite: 15 }),
            apiFetchSol('docentes_total'),
        ]);

        docentesSolicitadosData = Array.isArray(ranking) ? ranking : [];
        docSolTotalesActuales   = totalesObj?.totales || {};
        renderDocentesSolicitados(docentesSolicitadosData, totalesObj);

    } catch (err) {
        loadEl.style.display  = 'none';
        errorEl.style.display = 'block';
        errMsg.textContent    = `No se pudieron cargar los datos: ${err.message}`;
        console.error('[DocSol]', err);
    }
}

// ─── Render principal ─────────────────────────────────────────────────────────
function renderDocentesSolicitados(data, totalesObj) {
    const loadEl = document.getElementById('loadingDocentesSolicitados');
    const contEl = document.getElementById('contenidoDocentesSolicitados');

    const categoriaId = window.currentCategoryId || 3;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                     || { color: 'warning' };

    loadEl.style.display = 'none';
    contEl.style.display = 'block';

    const tot              = totalesObj?.totales || {};
    const totalDocentes    = parseInt(tot.total      || 0);
    const totalDisponible  = parseInt(tot.disponible || 0);
    const totalAsignado    = parseInt(tot.asignado   || 0);
    const totalInactivo    = parseInt(tot.inactivo   || 0);
    const totalCursos      = data.reduce((s, d) => s + parseInt(d.cursos_asignados || 0), 0);
    const topDocente       = data.length > 0 ? data[0] : null;

    // Iconos SVG inline (misma técnica que RE-Docente-Total.js)
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    // KPI Cards — mismo marcado que RE-Docente-Total.js
    const kpiCards = [
        {
            tipo:  'total',
            valor: totalDocentes,
            label: 'Total Docentes',
            sub:   `${totalDisponible} disponibles · ${totalAsignado} asignados`,
            color: 'primary'
        },
        {
            tipo:  'cursos',
            valor: totalCursos,
            label: 'Cursos Asignados',
            sub:   `En el ranking top ${data.length}`,
            color: 'success'
        },
        {
            tipo:  'inactivos',
            valor: totalInactivo,
            label: 'Inactivos',
            sub:   'Sin cursos activos',
            color: 'danger'
        },
        {
            tipo:  'top',
            valor: topDocente
                ? `${topDocente.primer_nombre} ${topDocente.primer_apellido}`
                : '—',
            label: 'Más Solicitado',
            sub:   topDocente
                ? `${topDocente.cursos_asignados} cursos asignados`
                : 'Sin datos',
            color: 'warning',
            esTexto: true
        }
    ];

    const kpisHtml = `
        <div class="row mb-1" id="docSolKPIs">
            ${kpiCards.map(c => `
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-3 kpi-card doc-sol-kpi-card"
                     role="button" data-tipo="${c.tipo}">
                    <div class="card-body text-center py-3">
                        <h2 class="fw-bold text-${c.color} mt-1 mb-0"
                            style="${c.esTexto ? 'font-size:1.1rem;' : ''}">
                            ${c.esTexto ? c.valor : parseInt(c.valor).toLocaleString()}
                        </h2>
                        <p class="rp-kpi-card-label">${c.label}</p>
                        <p class="rp-kpi-card-sub">${c.sub}</p>
                        <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small"
                              id="docSolBadge-${c.tipo}">${svgDown}Ver detalle</span>
                    </div>
                </div>
            </div>`).join('')}
        </div>`;

    contEl.innerHTML = `
        ${kpisHtml}

        <!-- Panel colapsable de detalle -->
        <div id="docSolPanelDetalle" style="display:none;" class="mb-4"></div>

        <!-- Gráfico + Detalle de docente seleccionado -->
        <div class="row mt-4" style="min-height:350px;">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Cursos Asignados por Docente</span>
                            <small class="text-muted">Top ${Math.min(8, data.length)}</small>
                        </h5>
                        <div style="position:relative;height:280px;">
                            <canvas id="graficoDocentesSolicitados"></canvas>
                        </div>
                        <p class="text-center text-muted small mt-2">
                            <i class="bi bi-hand-index me-1"></i>Clic en una barra para ver detalles
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title" id="tituloDetalleDocSol">Detalle del Docente</h5>
                        <div id="detalleDocSol" class="text-center py-4 text-muted">
                            <i class="bi bi-bar-chart-fill" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-3">Selecciona un docente en la gráfica o en la tabla para ver sus detalles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla ranking -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">Ranking de Docentes</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-${categoria.color} active"
                                        onclick="ordenarTablaDocSol('cursos', event)">
                                    <i class="bi bi-book-fill me-1"></i>Por Cursos
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-${categoria.color}"
                                        onclick="ordenarTablaDocSol('nombre', event)">
                                    <i class="bi bi-sort-alpha-down me-1"></i>Por Nombre
                                </button>
                            </div>
                        </div>
                        ${data.length === 0
                            ? `<div class="text-center py-4 text-muted">
                                   <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                   <p class="mt-2">No hay docentes con cursos asignados en ${anioDocentesSol}.</p>
                               </div>`
                            : `<div class="table-responsive">
                                <table class="table table-hover" id="tablaDocSol">
                                    <thead>
                                        <tr>
                                            <th>Pos.</th>
                                            <th>Docente</th>
                                            <th>Cursos Asignados</th>
                                            <th>Nivel Demanda</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.map((item, idx) => renderFilaDocSol(item, idx, totalCursos, data.length, categoria)).join('')}
                                    </tbody>
                                </table>
                               </div>`
                        }
                    </div>
                </div>
            </div>
        </div>`;

    // Gráfico
    renderGraficoDocSol(data, categoria);

    // Eventos de las KPI cards (misma lógica que RE-Docente-Total.js)
    contEl.querySelectorAll('.doc-sol-kpi-card').forEach(card =>
        card.addEventListener('click', () => docSolTogglePanel(card.dataset.tipo))
    );
}

// ─── Toggle del panel colapsable ─────────────────────────────────────────────
function docSolTogglePanel(tipo) {
    const wrapper = document.getElementById('docSolPanelDetalle');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    // Resetear badges
    ['total', 'cursos', 'inactivos', 'top'].forEach(t => {
        const b = document.getElementById(`docSolBadge-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    // Si ya estaba abierto el mismo, cerrar
    if (docSolPanelActivo === tipo) {
        wrapper.style.display = 'none';
        docSolPanelActivo     = null;
        return;
    }

    docSolPanelActivo = tipo;
    docSolRenderPanel(tipo);
    wrapper.style.display = 'block';

    const badge = document.getElementById(`docSolBadge-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;

    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
}

// ─── Paneles de detalle por KPI ───────────────────────────────────────────────
function docSolRenderPanel(tipo) {
    const wrapper = document.getElementById('docSolPanelDetalle');
    if (!wrapper) return;

    const t = docSolTotalesActuales || {};

    const cfgMap = {
        total:    { titulo: 'Distribución de Docentes por Tipo y Estado', color: 'primary' },
        cursos:   { titulo: 'Distribución de Cursos en el Ranking',       color: 'success' },
        inactivos:{ titulo: 'Detalle de Docentes Inactivos',              color: 'danger'  },
        top:      { titulo: 'Información del Docente Más Solicitado',     color: 'warning' }
    };
    const cfg = cfgMap[tipo] || { titulo: 'Detalle', color: 'secondary' };

    let contenidoPanel = '';

    // ── Panel: Total Docentes ─────────────────────────────────────────────────
    if (tipo === 'total') {
        const totalActivos    = (parseInt(t.disponible)||0) + (parseInt(t.en_formacion)||0) + (parseInt(t.asignado)||0);
        const totalGeneral    = parseInt(t.total) || 0;
        const pctDisponible   = totalGeneral > 0 ? Math.round(((parseInt(t.disponible)||0)  / totalGeneral) * 100) : 0;
        const pctAsignado     = totalGeneral > 0 ? Math.round(((parseInt(t.asignado)||0)    / totalGeneral) * 100) : 0;
        const pctInactivo     = totalGeneral > 0 ? Math.round(((parseInt(t.inactivo)||0)    / totalGeneral) * 100) : 0;
        const pctEnFormacion  = totalGeneral > 0 ? Math.round(((parseInt(t.en_formacion)||0)/ totalGeneral) * 100) : 0;

        contenidoPanel = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Tipo</th><th class="text-center">Cantidad</th><th class="text-center">%</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><span class="badge bg-primary">Fijo</span></td>
                                <td class="text-center fw-bold">${parseInt(t.fijo)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.fijo)||0)/totalGeneral)*100) : 0}%</td></tr>
                            <tr><td><span class="badge bg-info text-dark">Contratado</span></td>
                                <td class="text-center fw-bold">${parseInt(t.contratado)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.contratado)||0)/totalGeneral)*100) : 0}%</td></tr>
                            <tr><td><span class="badge bg-success">PDL</span></td>
                                <td class="text-center fw-bold">${parseInt(t.pdl)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.pdl)||0)/totalGeneral)*100) : 0}%</td></tr>
                            <tr><td><span class="badge bg-secondary">Voluntario</span></td>
                                <td class="text-center fw-bold">${parseInt(t.voluntario)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.voluntario)||0)/totalGeneral)*100) : 0}%</td></tr>
                            <tr class="table-secondary fw-bold"><td>Total</td>
                                <td class="text-center">${totalGeneral}</td><td class="text-center">100%</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Estado</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <thead class="table-light">
                            <tr><th>Estado</th><th class="text-center">Cantidad</th><th>Participación</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-success">Disponible</span></td>
                                <td class="text-center fw-bold">${parseInt(t.disponible)||0}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-success" style="width:${pctDisponible}%;"></div>
                                        </div>
                                        <small>${pctDisponible}%</small>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark">Asignado</span></td>
                                <td class="text-center fw-bold">${parseInt(t.asignado)||0}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-warning" style="width:${pctAsignado}%;"></div>
                                        </div>
                                        <small>${pctAsignado}%</small>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info text-dark">En formación</span></td>
                                <td class="text-center fw-bold">${parseInt(t.en_formacion)||0}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-info" style="width:${pctEnFormacion}%;"></div>
                                        </div>
                                        <small>${pctEnFormacion}%</small>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Inactivo</span></td>
                                <td class="text-center fw-bold">${parseInt(t.inactivo)||0}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-danger" style="width:${pctInactivo}%;"></div>
                                        </div>
                                        <small>${pctInactivo}%</small>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-secondary fw-bold">
                                <td>Activos</td>
                                <td class="text-center">${totalActivos}</td>
                                <td><small class="text-muted">${totalGeneral > 0 ? Math.round((totalActivos/totalGeneral)*100) : 0}% del total</small></td>
                            </tr>
                        </tbody>
                    </table>
                    <h6 class="fw-semibold mb-2">Por Género</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Género</th><th class="text-center">Cantidad</th><th class="text-center">%</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Masculino</td>
                                <td class="text-center fw-bold">${parseInt(t.masculino)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.masculino)||0)/totalGeneral)*100) : 0}%</td></tr>
                            <tr><td>Femenino</td>
                                <td class="text-center fw-bold">${parseInt(t.femenino)||0}</td>
                                <td class="text-center">${totalGeneral > 0 ? Math.round(((parseInt(t.femenino)||0)/totalGeneral)*100) : 0}%</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    // ── Panel: Cursos en el ranking ───────────────────────────────────────────
    else if (tipo === 'cursos') {
        const data       = docentesSolicitadosData;
        const totalCursos = data.reduce((s, d) => s + parseInt(d.cursos_asignados || 0), 0);
        const maxCursos   = data.length > 0 ? parseInt(data[0].cursos_asignados || 1) : 1;

        // Agrupar por tipo de docente
        const porTipo = {};
        data.forEach(d => {
            const tipo_d = d.tipo_docente || 'Sin tipo';
            if (!porTipo[tipo_d]) porTipo[tipo_d] = { docentes: 0, cursos: 0 };
            porTipo[tipo_d].docentes++;
            porTipo[tipo_d].cursos += parseInt(d.cursos_asignados || 0);
        });

        // Promedio de carga
        const promedio = data.length > 0 ? (totalCursos / data.length).toFixed(1) : 0;

        contenidoPanel = `
            <div class="row">
                <div class="col-md-5">
                    <h6 class="fw-semibold mb-2">Resumen del Ranking</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tbody>
                            <tr><td class="text-muted">Docentes en ranking</td>
                                <td class="text-center fw-bold">${data.length}</td></tr>
                            <tr><td class="text-muted">Total cursos asignados</td>
                                <td class="text-center fw-bold text-success">${totalCursos}</td></tr>
                            <tr><td class="text-muted">Promedio por docente</td>
                                <td class="text-center fw-bold">${promedio}</td></tr>
                            <tr><td class="text-muted">Máximo (1er lugar)</td>
                                <td class="text-center fw-bold text-warning">${maxCursos}</td></tr>
                        </tbody>
                    </table>
                    <h6 class="fw-semibold mb-2">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Tipo</th><th class="text-center">Docentes</th><th class="text-center">Cursos</th></tr>
                        </thead>
                        <tbody>
                            ${Object.entries(porTipo).map(([tipo_d, vals]) => `
                            <tr>
                                <td>${tipo_d}</td>
                                <td class="text-center">${vals.docentes}</td>
                                <td class="text-center fw-bold">${vals.cursos}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2">Carga por Docente (Top ${Math.min(10, data.length)})</h6>
                    <div style="max-height:280px;overflow-y:auto;">
                        ${data.slice(0, 10).map((d, idx) => {
                            const cursos  = parseInt(d.cursos_asignados || 0);
                            const pct     = maxCursos > 0 ? Math.round((cursos / maxCursos) * 100) : 0;
                            const nivel   = cursos >= maxCursos * 0.7 ? 'danger'
                                          : cursos >= maxCursos * 0.4 ? 'warning' : 'info';
                            const iniciales = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
                            return `
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                     style="width:32px;height:32px;font-size:.7rem;">
                                    ${iniciales}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-semibold text-truncate">${d.primer_nombre} ${d.primer_apellido}</small>
                                        <small class="ms-2 text-${nivel} fw-bold flex-shrink-0">${cursos} c.</small>
                                    </div>
                                    <div class="progress" style="height:5px;">
                                        <div class="progress-bar bg-${nivel}" style="width:${pct}%;"></div>
                                    </div>
                                </div>
                            </div>`;
                        }).join('')}
                    </div>
                </div>
            </div>`;
    }

    // ── Panel: Inactivos ──────────────────────────────────────────────────────
    else if (tipo === 'inactivos') {
        const totalInactivo = parseInt(t.inactivo) || 0;
        const totalGeneral  = parseInt(t.total)    || 0;
        const pct           = totalGeneral > 0 ? ((totalInactivo / totalGeneral) * 100).toFixed(1) : 0;

        // Docentes del ranking con estado Inactivo
        const inactivosEnRanking = docentesSolicitadosData.filter(
            d => (d.estado_docente || '').toLowerCase() === 'inactivo'
        );

        contenidoPanel = `
            <div class="row">
                <div class="col-md-5">
                    <div class="alert alert-danger d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-person-x-fill fs-4 mt-1 flex-shrink-0"></i>
                        <div>
                            <h5 class="mb-1">${totalInactivo} docente${totalInactivo !== 1 ? 's' : ''} inactivo${totalInactivo !== 1 ? 's' : ''}</h5>
                            <p class="mb-0 small">
                                Representan el <strong>${pct}%</strong> del total de docentes
                                registrados en el sistema.
                            </p>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr><td class="text-muted">Total en sistema</td>
                                <td class="text-center fw-bold">${totalGeneral}</td></tr>
                            <tr><td class="text-muted">Inactivos</td>
                                <td class="text-center fw-bold text-danger">${totalInactivo}</td></tr>
                            <tr><td class="text-muted">% sobre el total</td>
                                <td class="text-center fw-bold">${pct}%</td></tr>
                        </tbody>
                    </table>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Para ver el listado completo de inactivos con última fecha de asignación,
                        use el reporte <em>Docentes Inactivos</em>.
                    </p>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2">Inactivos con asignaciones en ${anioDocentesSol}</h6>
                    ${inactivosEnRanking.length === 0
                        ? `<div class="text-center py-3 text-muted">
                               <i class="bi bi-check-circle-fill text-success" style="font-size:1.8rem;"></i>
                               <p class="mt-2 small">Ningún docente del ranking tiene estado Inactivo.</p>
                           </div>`
                        : `<div class="table-responsive">
                               <table class="table table-sm table-bordered">
                                   <thead class="table-light">
                                       <tr><th>Docente</th><th class="text-center">Cursos</th><th>Tipo</th></tr>
                                   </thead>
                                   <tbody>
                                       ${inactivosEnRanking.map(d => `
                                       <tr>
                                           <td>${d.primer_nombre} ${d.primer_apellido}</td>
                                           <td class="text-center fw-bold">${d.cursos_asignados}</td>
                                           <td><span class="badge bg-secondary">${d.tipo_docente||'—'}</span></td>
                                       </tr>`).join('')}
                                   </tbody>
                               </table>
                           </div>`
                    }
                </div>
            </div>`;
    }

    // ── Panel: Top Docente ────────────────────────────────────────────────────
    else if (tipo === 'top') {
        const topDocente  = docentesSolicitadosData.length > 0 ? docentesSolicitadosData[0] : null;
        const totalCursos = docentesSolicitadosData.reduce((s, d) => s + parseInt(d.cursos_asignados || 0), 0);

        if (!topDocente) {
            contenidoPanel = `<div class="text-center py-3 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p class="mt-2">No hay datos disponibles para ${anioDocentesSol}.</p>
            </div>`;
        } else {
            const cursos      = parseInt(topDocente.cursos_asignados || 0);
            const pctGlobal   = totalCursos > 0 ? ((cursos / totalCursos) * 100).toFixed(1) : 0;
            const iniciales   = `${(topDocente.primer_nombre||'')[0]||''}${(topDocente.primer_apellido||'')[0]||''}`.toUpperCase();
            const clsTipo     = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
            const clsEst      = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

            // Posición relativa respecto a los demás docentes del ranking
            const ranking = docentesSolicitadosData.map((d, i) => ({
                pos:    i + 1,
                nombre: `${d.primer_nombre} ${d.primer_apellido}`,
                cursos: parseInt(d.cursos_asignados || 0)
            }));

            contenidoPanel = `
                <div class="row">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                 style="width:64px;height:64px;font-size:1.3rem;">${iniciales}</div>
                            <div>
                                <h5 class="mb-0 fw-bold">${topDocente.primer_nombre} ${topDocente.primer_apellido}</h5>
                                <div class="d-flex gap-2 mt-1 flex-wrap">
                                    <span class="badge bg-${clsTipo[topDocente.tipo_docente]||'secondary'}">${topDocente.tipo_docente||'—'}</span>
                                    <span class="badge bg-${clsEst[topDocente.estado_docente]||'secondary'}">${topDocente.estado_docente||'—'}</span>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill me-1"></i>#1 del ranking</span>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="card border-0 bg-light py-2">
                                    <h4 class="fw-bold text-primary mb-0">${cursos}</h4>
                                    <small class="text-muted">Cursos asignados</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0 bg-light py-2">
                                    <h4 class="fw-bold text-warning mb-0">${pctGlobal}%</h4>
                                    <small class="text-muted">Del total del ranking</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Participación en cursos</span>
                                <strong>${pctGlobal}%</strong>
                            </div>
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar bg-warning" style="width:${pctGlobal}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-2">Posición relativa en el ranking</h6>
                        <div style="max-height:260px;overflow-y:auto;">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr><th>Pos.</th><th>Docente</th><th class="text-center">Cursos</th></tr>
                                </thead>
                                <tbody>
                                    ${ranking.slice(0, 10).map(r => `
                                    <tr class="${r.pos === 1 ? 'table-warning' : ''}">
                                        <td>
                                            <span class="badge ${r.pos === 1 ? 'bg-warning text-dark' : r.pos === 2 ? 'bg-secondary' : r.pos === 3 ? 'bg-danger' : 'bg-light text-dark border'}">
                                                ${r.pos}
                                            </span>
                                            ${r.pos === 1 ? '<i class="bi bi-trophy-fill text-warning ms-1"></i>' : ''}
                                        </td>
                                        <td class="${r.pos === 1 ? 'fw-bold' : ''}">${r.nombre}</td>
                                        <td class="text-center fw-bold">${r.cursos}</td>
                                    </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
        }
    }

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="mb-3 text-${cfg.color} fw-semibold">
                    <i class="bi bi-table me-1"></i>${cfg.titulo}
                </h6>
                ${contenidoPanel}
            </div>
        </div>`;
}

// ─── Fila de tabla ────────────────────────────────────────────────────────────
function renderFilaDocSol(item, idx, totalCursos, totalDocentes, categoria) {
    const cursos    = parseInt(item.cursos_asignados || 0);
    const maxCursos = docentesSolicitadosData.length > 0
                      ? parseInt(docentesSolicitadosData[0].cursos_asignados || 1)
                      : 1;
    const pct       = Math.min(Math.round((cursos / maxCursos) * 100), 100);
    const nivel     = cursos >= maxCursos * 0.7 ? 'Alto' : cursos >= maxCursos * 0.4 ? 'Moderado' : 'Básico';
    const clsNivel  = nivel === 'Alto' ? 'bg-danger' : nivel === 'Moderado' ? 'bg-warning text-dark' : 'bg-info';
    const clsPos    = idx === 0 ? 'bg-warning text-dark' : idx === 1 ? 'bg-secondary' : idx === 2 ? 'bg-danger' : 'bg-light text-dark border';
    const clsTipo   = { Fijo:'bg-primary', Contratado:'bg-info', PDL:'bg-success', Voluntario:'bg-secondary' };
    const clsEst    = { Disponible:'bg-success', Asignado:'bg-warning text-dark', 'En formación':'bg-info', Inactivo:'bg-secondary' };
    const iniciales = `${(item.primer_nombre||'')[0]||''}${(item.primer_apellido||'')[0]||''}`.toUpperCase();

    return `
    <tr style="cursor:pointer;" onclick="mostrarDetalleDocSolTabla(${idx})">
        <td>
            <div class="d-flex align-items-center gap-1">
                <span class="badge ${clsPos}">${idx + 1}</span>
                ${idx === 0 ? '<i class="bi bi-trophy-fill text-warning"></i>' : ''}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-${categoria.color} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:38px;height:38px;font-size:.75rem;font-weight:bold;">
                    ${iniciales}
                </div>
                <strong>${item.primer_nombre} ${item.primer_apellido}</strong>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-pill">${cursos}</span>
                <div class="progress flex-grow-1" style="height:8px;max-width:80px;">
                    <div class="progress-bar bg-primary" style="width:${pct}%;"></div>
                </div>
            </div>
        </td>
        <td><span class="badge ${clsNivel}">${nivel}</span></td>
        <td><span class="badge ${clsTipo[item.tipo_docente] || 'bg-secondary'}">${item.tipo_docente || '—'}</span></td>
        <td><span class="badge ${clsEst[item.estado_docente] || 'bg-secondary'}">${item.estado_docente || '—'}</span></td>
    </tr>`;
}

// ─── Gráfico de barras ────────────────────────────────────────────────────────
function renderGraficoDocSol(data, categoria) {
    const ctx = document.getElementById('graficoDocentesSolicitados')?.getContext('2d');
    if (!ctx) return;

    if (docentesSolicitadosChart) {
        docentesSolicitadosChart.destroy();
        docentesSolicitadosChart = null;
    }

    const top = data.slice(0, Math.min(8, data.length));
    if (top.length === 0) return;

    const tonosAmarillo = [
        'rgba(255,235,59,.85)', 'rgba(255,214,0,.85)',  'rgba(255,193,7,.85)',
        'rgba(255,167,38,.85)', 'rgba(255,143,0,.85)',  'rgba(255,111,0,.85)',
        'rgba(255,80,0,.85)',   'rgba(230,60,0,.85)',
    ];

    docentesSolicitadosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(d => `${d.primer_nombre.charAt(0)}. ${d.primer_apellido}`),
            datasets: [{
                label: 'Cursos Asignados',
                data: top.map(d => parseInt(d.cursos_asignados || 0)),
                backgroundColor: top.map((_, i) => tonosAmarillo[i % tonosAmarillo.length]),
                borderColor:     top.map((_, i) => tonosAmarillo[i % tonosAmarillo.length].replace('.85', '1')),
                borderWidth: 2,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const d = top[ctx.dataIndex];
                            return [
                                `${d.primer_nombre} ${d.primer_apellido}`,
                                `Cursos: ${ctx.raw}`,
                                `Tipo: ${d.tipo_docente}`,
                                `Estado: ${d.estado_docente}`
                            ];
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,.82)',
                    padding: 12,
                    cornerRadius: 6
                }
            },
            onClick(_, elements) {
                if (elements.length > 0) mostrarDetalleDocSol(elements[0].index);
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    title: { display: true, text: 'Cursos asignados', color: '#666', font: { size: 12 } },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    title: { display: true, text: 'Docentes', color: '#666', font: { size: 12 } },
                    grid: { display: false }
                }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
        }
    });
}

// ─── Panel lateral de detalle (clic en barra o fila) ─────────────────────────
function mostrarDetalleDocSol(idx) {
    const d = docentesSolicitadosData[idx];
    if (!d) return;

    const cursos    = parseInt(d.cursos_asignados || 0);
    const total     = docentesSolicitadosData.reduce((s, x) => s + parseInt(x.cursos_asignados || 0), 0);
    const pctGlobal = total > 0 ? ((cursos / total) * 100).toFixed(1) : 0;
    const iniciales = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
    const nivel     = cursos >= docentesSolicitadosData[0]?.cursos_asignados * 0.7 ? 'Alto'
                    : cursos >= docentesSolicitadosData[0]?.cursos_asignados * 0.4 ? 'Moderado' : 'Básico';
    const clsNivel  = nivel === 'Alto' ? 'danger' : nivel === 'Moderado' ? 'warning' : 'info';
    const clsTipo   = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
    const clsEst    = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

    document.getElementById('tituloDetalleDocSol').textContent = `Detalle: ${d.primer_nombre}`;

    document.getElementById('detalleDocSol').innerHTML = `
        <div class="text-start">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                     style="width:60px;height:60px;font-size:1.25rem;">${iniciales}</div>
                <div>
                    <h5 class="mb-0 fw-bold">${d.primer_nombre} ${d.primer_apellido}</h5>
                    <div class="d-flex gap-2 mt-1 flex-wrap">
                        <span class="badge bg-${clsTipo[d.tipo_docente]||'secondary'}">${d.tipo_docente||'—'}</span>
                        <span class="badge bg-${clsEst[d.estado_docente]||'secondary'}">${d.estado_docente||'—'}</span>
                    </div>
                </div>
            </div>

            <div class="row text-center mb-3">
                <div class="col-6">
                    <div class="card border-0 bg-light py-2">
                        <h4 class="fw-bold text-primary mb-0">${cursos}</h4>
                        <small class="text-muted">Cursos asignados</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 bg-light py-2">
                        <h4 class="fw-bold text-warning mb-0">${pctGlobal}%</h4>
                        <small class="text-muted">Del total del ranking</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Participación en cursos</span>
                    <strong>${pctGlobal}%</strong>
                </div>
                <div class="progress" style="height:10px;">
                    <div class="progress-bar bg-warning" style="width:${pctGlobal}%;"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                <span class="text-muted small">Nivel de demanda:</span>
                <span class="badge bg-${clsNivel} fs-6">${nivel}</span>
            </div>
        </div>`;
}

function mostrarDetalleDocSolTabla(idx) {
    mostrarDetalleDocSol(idx);
    if (idx < 8 && docentesSolicitadosChart) {
        const meta = docentesSolicitadosChart.getDatasetMeta(0);
        const bar  = meta.data[idx];
        if (bar) {
            const orig = bar.options.borderWidth;
            bar.options.borderWidth = 4;
            docentesSolicitadosChart.update('none');
            setTimeout(() => {
                bar.options.borderWidth = orig;
                docentesSolicitadosChart.update('none');
            }, 900);
        }
    }
}

// ─── Ordenar tabla ────────────────────────────────────────────────────────────
function ordenarTablaDocSol(criterio, evt) {
    const tbody = document.querySelector('#tablaDocSol tbody');
    if (!tbody) return;

    const categoriaId = window.currentCategoryId || 3;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color: 'warning' };
    const filas       = Array.from(tbody.querySelectorAll('tr'));

    filas.sort((a, b) => {
        if (criterio === 'nombre') {
            const nA = a.querySelector('strong')?.textContent.trim().toLowerCase() || '';
            const nB = b.querySelector('strong')?.textContent.trim().toLowerCase() || '';
            return nA.localeCompare(nB);
        }
        const vA = parseInt(a.querySelector('.badge.bg-primary')?.textContent || 0);
        const vB = parseInt(b.querySelector('.badge.bg-primary')?.textContent || 0);
        return vB - vA;
    });

    filas.forEach(f => tbody.appendChild(f));

    filas.forEach((fila, idx) => {
        const posCell = fila.querySelector('td:first-child');
        const clsPos  = idx === 0 ? 'bg-warning text-dark' : idx === 1 ? 'bg-secondary' : idx === 2 ? 'bg-danger' : 'bg-light text-dark border';
        posCell.innerHTML = `
            <div class="d-flex align-items-center gap-1">
                <span class="badge ${clsPos}">${idx + 1}</span>
                ${idx === 0 ? '<i class="bi bi-trophy-fill text-warning"></i>' : ''}
            </div>`;
    });

    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    if (evt?.target) evt.target.classList.add('active');
}

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarDocentesMasSolicitados() {
    if (docentesSolicitadosChart) {
        docentesSolicitadosChart.destroy();
        docentesSolicitadosChart = null;
    }
    docentesSolicitadosData  = [];
    anioDocentesSol          = new Date().getFullYear();
    docSolTotalesActuales    = null;
    docSolPanelActivo        = null;
}

// ─── Estilos ──────────────────────────────────────────────────────────────────
(function inyectarEstilosDocSol() {
    if (document.getElementById('_estDocSol')) return;
    const s = document.createElement('style');
    s.id    = '_estDocSol';
    s.textContent = `
        .kpi-card {
            transition: transform .25s, box-shadow .25s;
            cursor: pointer;
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,.13) !important;
        }
        .rp-kpi-card-label {
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #666;
            margin: .25rem 0 0;
        }
        .rp-kpi-card-sub {
            font-size: .72rem;
            color: #999;
            margin: 0;
        }
    `;
    document.head.appendChild(s);
})();

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarDocentesMasSolicitados  = cargarDocentesMasSolicitados;
window.limpiarDocentesMasSolicitados = limpiarDocentesMasSolicitados;
window.ordenarTablaDocSol            = ordenarTablaDocSol;
window.mostrarDetalleDocSolTabla     = mostrarDetalleDocSolTabla;

console.log('RE-Docente-MasSolicitados.js cargado');
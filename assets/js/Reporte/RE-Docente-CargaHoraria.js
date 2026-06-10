// ========== REPORTE: CARGA HORARIA POR DOCENTE ==========
// Conectado a: api_reportes.php
// Acciones usadas: docentes_carga_horaria, docentes_total, anios_disponibles
// Reemplaza a: RE-Docente-MasSolicitados.js

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_CARGA = '../controlador/Reporte/api_reportes.php';

let cargaHorariaChart = null;
let cargaHorariaData  = [];
let anioCarga         = new Date().getFullYear();
let cargaTotalesActuales = null;
let cargaPanelActivo     = null;

// ─── Helper fetch ─────────────────────────────────────────────────────────────
async function apiFetchCarga(accion, params = {}) {
    const url = new URL(API_URL_CARGA, window.location.href);
    url.searchParams.set('accion', accion);
    url.searchParams.set('anio', anioCarga);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
    const resp = await fetch(url.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();
    if (data.error) throw new Error(data.error);
    return data;
}

// ─── Función principal ────────────────────────────────────────────────────────
async function cargarCargaHorariaDocentes() {
    const contentDiv = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId)
        || { color: 'warning', nombre: 'Docentes' };

    cargaHorariaChart      = null;
    cargaHorariaData       = [];
    cargaTotalesActuales   = null;
    cargaPanelActivo       = null;

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Carga Horaria por Docente</h4>
                        <p class="mb-0 small opacity-75">Horas asignadas vs. completadas en cursos activos</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-white small fw-semibold mb-0">Año:</label>
                        <select id="selectAnioCarga" class="form-select form-select-sm" style="width:110px;"></select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="loadingCarga">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
                </div>
                <div id="contenidoCarga" style="display:none;"></div>
                <div id="errorCarga" style="display:none;" class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="errorMsgCarga"></span>
                </div>
            </div>
        </div>`;

    try {
        const anios = await apiFetchCarga('anios_disponibles');
        const select = document.getElementById('selectAnioCarga');
        if (select && Array.isArray(anios) && anios.length > 0) {
            anios.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a;
                opt.textContent = a;
                if (a === anioCarga) opt.selected = true;
                select.appendChild(opt);
            });
            select.addEventListener('change', e => {
                anioCarga = parseInt(e.target.value);
                cargaPanelActivo = null;
                cargarContenidoCarga();
            });
        }
    } catch (_) { /* continuar con año actual */ }

    cargarContenidoCarga();
}

// ─── Carga principal ──────────────────────────────────────────────────────────
async function cargarContenidoCarga() {
    const loadEl  = document.getElementById('loadingCarga');
    const contEl  = document.getElementById('contenidoCarga');
    const errorEl = document.getElementById('errorCarga');
    const errMsg  = document.getElementById('errorMsgCarga');

    loadEl.style.display = 'flex';
    loadEl.style.flexDirection = 'column';
    loadEl.style.alignItems = 'center';
    contEl.style.display = 'none';
    errorEl.style.display = 'none';
    cargaPanelActivo = null;

    if (cargaHorariaChart) {
        cargaHorariaChart.destroy();
        cargaHorariaChart = null;
    }

    try {
        const [ranking, totalesObj] = await Promise.all([
            apiFetchCarga('docentes_carga_horaria', { limite: 15 }),
            apiFetchCarga('docentes_total'),
        ]);

        cargaHorariaData     = Array.isArray(ranking) ? ranking : [];
        cargaTotalesActuales = totalesObj?.totales || {};
        renderCargaHoraria(cargaHorariaData, totalesObj);

    } catch (err) {
        loadEl.style.display = 'none';
        errorEl.style.display = 'block';
        errMsg.textContent = `No se pudieron cargar los datos: ${err.message}`;
        console.error('[CargaHoraria]', err);
    }
}

// ─── Render principal ─────────────────────────────────────────────────────────
function renderCargaHoraria(data, totalesObj) {
    const loadEl = document.getElementById('loadingCarga');
    const contEl = document.getElementById('contenidoCarga');

    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId)
        || { color: 'warning' };

    loadEl.style.display = 'none';
    contEl.style.display = 'block';

    const tot = totalesObj?.totales || {};
    const totalDocentes   = parseInt(tot.total   || 0);
    const totalAsignado   = parseInt(tot.asignado || 0);
    const totalDisponible = parseInt(tot.disponible || 0);

    const totalHorasAsig = data.reduce((s, d) => s + parseFloat(d.horas_asignadas || 0), 0);
    const totalHorasComp = data.reduce((s, d) => s + parseFloat(d.horas_completadas || 0), 0);
    const pctGlobal      = totalHorasAsig > 0
        ? Math.round((totalHorasComp / totalHorasAsig) * 100) : 0;

    const topDocente = data.length > 0 ? data[0] : null;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const kpiCards = [
        {
            tipo:  'total',
            valor: totalDocentes,
            label: 'Total Docentes',
            sub:   `${totalDisponible} disponibles · ${totalAsignado} asignados`,
            color: 'primary'
        },
        {
            tipo:  'horas',
            valor: Math.round(totalHorasAsig),
            label: 'Horas Asignadas',
            sub:   `En cursos activos del ranking`,
            color: 'success'
        },
        {
            tipo:  'avance',
            valor: `${pctGlobal}%`,
            label: 'Avance Global',
            sub:   `${Math.round(totalHorasComp)}h completadas`,
            color: pctGlobal >= 75 ? 'success' : pctGlobal >= 40 ? 'warning' : 'danger',
            esTexto: true
        },
        {
            tipo:  'top',
            valor: topDocente
                ? `${topDocente.primer_nombre} ${topDocente.primer_apellido}`
                : '—',
            label: 'Mayor Carga',
            sub:   topDocente
                ? `${Math.round(topDocente.horas_asignadas)}h asignadas`
                : 'Sin datos',
            color: 'warning',
            esTexto: true
        }
    ];

    const kpisHtml = `
        <div class="row mb-1" id="cargaKPIs">
            ${kpiCards.map(c => `
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-3 kpi-card carga-kpi-card"
                     role="button" data-tipo="${c.tipo}">
                    <div class="card-body text-center py-3">
                        <h2 class="fw-bold text-${c.color} mt-1 mb-0"
                            style="${c.esTexto ? 'font-size:1.1rem;' : ''}">
                            ${c.esTexto ? c.valor : parseInt(c.valor).toLocaleString()}
                        </h2>
                        <p class="rp-kpi-card-label">${c.label}</p>
                        <p class="rp-kpi-card-sub">${c.sub}</p>
                        <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small"
                              id="cargaBadge-${c.tipo}">${svgDown}Ver detalle</span>
                    </div>
                </div>
            </div>`).join('')}
        </div>`;

    contEl.innerHTML = `
        ${kpisHtml}

        <!-- Panel colapsable -->
        <div id="cargaPanelDetalle" style="display:none;" class="mb-4"></div>

        <!-- Gráfico + detalle lateral -->
        <div class="row mt-4" style="min-height:350px;">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Horas Asignadas vs. Completadas</span>
                            <small class="text-muted">Top ${Math.min(8, data.length)}</small>
                        </h5>
                        <div style="position:relative;height:280px;">
                            <canvas id="graficoCargaHoraria"></canvas>
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
                        <h5 class="card-title" id="tituloCargaDetalle">Detalle del Docente</h5>
                        <div id="detalleCarga" class="text-center py-4 text-muted">
                            <i class="bi bi-clock-history" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-3">Selecciona un docente en la gráfica o en la tabla para ver su carga horaria.</p>
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
                            <h5 class="card-title mb-0">Ranking de Carga Horaria</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-${categoria.color} active"
                                        onclick="ordenarTablaCarga('horas', event)">
                                    <i class="bi bi-hourglass-split me-1"></i>Por Horas
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-${categoria.color}"
                                        onclick="ordenarTablaCarga('avance', event)">
                                    <i class="bi bi-graph-up me-1"></i>Por Avance
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-${categoria.color}"
                                        onclick="ordenarTablaCarga('nombre', event)">
                                    <i class="bi bi-sort-alpha-down me-1"></i>Por Nombre
                                </button>
                            </div>
                        </div>
                        ${data.length === 0
            ? `<div class="text-center py-4 text-muted">
                               <i class="bi bi-inbox" style="font-size:2rem;"></i>
                               <p class="mt-2">No hay docentes con cursos activos en ${anioCarga}.</p>
                           </div>`
            : `<div class="table-responsive">
                                <table class="table table-hover" id="tablaCargaHoraria">
                                    <thead>
                                        <tr>
                                            <th>Pos.</th>
                                            <th>Docente</th>
                                            <th>Horas Asignadas</th>
                                            <th>Horas Completadas</th>
                                            <th>Avance</th>
                                            <th>Cursos Activos</th>
                                            <th>Nivel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.map((item, idx) => renderFilaCarga(item, idx, categoria)).join('')}
                                    </tbody>
                                </table>
                               </div>`
        }
                    </div>
                </div>
            </div>
        </div>`;

    renderGraficoCarga(data, categoria);

    contEl.querySelectorAll('.carga-kpi-card').forEach(card =>
        card.addEventListener('click', () => cargaTogglePanel(card.dataset.tipo))
    );
}

// ─── Toggle panel KPI ─────────────────────────────────────────────────────────
function cargaTogglePanel(tipo) {
    const wrapper = document.getElementById('cargaPanelDetalle');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total','horas','avance','top'].forEach(t => {
        const b = document.getElementById(`cargaBadge-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    if (cargaPanelActivo === tipo) {
        wrapper.style.display = 'none';
        cargaPanelActivo = null;
        return;
    }

    cargaPanelActivo = tipo;
    cargaRenderPanel(tipo);
    wrapper.style.display = 'block';

    const badge = document.getElementById(`cargaBadge-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;

    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
}

// ─── Paneles de detalle ───────────────────────────────────────────────────────
function cargaRenderPanel(tipo) {
    const wrapper = document.getElementById('cargaPanelDetalle');
    if (!wrapper) return;

    const t   = cargaTotalesActuales || {};
    const data = cargaHorariaData;

    const cfgMap = {
        total:  { titulo: 'Distribución de Docentes por Tipo y Estado',  color: 'primary'  },
        horas:  { titulo: 'Distribución de Horas Asignadas en el Ranking', color: 'success' },
        avance: { titulo: 'Avance por Tramo de Progreso',                 color: 'warning'  },
        top:    { titulo: 'Docente con Mayor Carga Horaria',              color: 'warning'  }
    };
    const cfg = cfgMap[tipo] || { titulo: 'Detalle', color: 'secondary' };

    let contenidoPanel = '';

    // ── Panel: Total ──────────────────────────────────────────────────────────
    if (tipo === 'total') {
        const totalGeneral = parseInt(t.total) || 0;
        const pct = v => totalGeneral > 0 ? Math.round((parseInt(v || 0) / totalGeneral) * 100) : 0;

        contenidoPanel = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tipo</th><th class="text-center">Cantidad</th><th class="text-center">%</th></tr></thead>
                        <tbody>
                            <tr><td><span class="badge bg-primary">Fijo</span></td><td class="text-center fw-bold">${parseInt(t.fijo)||0}</td><td class="text-center">${pct(t.fijo)}%</td></tr>
                            <tr><td><span class="badge bg-info text-dark">Contratado</span></td><td class="text-center fw-bold">${parseInt(t.contratado)||0}</td><td class="text-center">${pct(t.contratado)}%</td></tr>
                            <tr><td><span class="badge bg-success">PDL</span></td><td class="text-center fw-bold">${parseInt(t.pdl)||0}</td><td class="text-center">${pct(t.pdl)}%</td></tr>
                            <tr><td><span class="badge bg-secondary">Voluntario</span></td><td class="text-center fw-bold">${parseInt(t.voluntario)||0}</td><td class="text-center">${pct(t.voluntario)}%</td></tr>
                            <tr class="table-secondary fw-bold"><td>Total</td><td class="text-center">${totalGeneral}</td><td class="text-center">100%</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Por Estado</h6>
                    ${[
                        { label: 'Disponible',    val: t.disponible,    cls: 'success' },
                        { label: 'Asignado',      val: t.asignado,      cls: 'warning' },
                        { label: 'En formación',  val: t.en_formacion,  cls: 'info'    },
                        { label: 'Inactivo',      val: t.inactivo,      cls: 'danger'  }
                    ].map(row => {
                        const p = pct(row.val);
                        return `
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-${row.cls}" style="min-width:90px;">${row.label}</span>
                            <div class="progress flex-grow-1" style="height:7px;">
                                <div class="progress-bar bg-${row.cls}" style="width:${p}%;"></div>
                            </div>
                            <small class="fw-bold" style="min-width:40px;">${parseInt(row.val||0)} <span class="text-muted fw-normal">(${p}%)</span></small>
                        </div>`;
                    }).join('')}
                </div>
            </div>`;
    }

    // ── Panel: Horas ──────────────────────────────────────────────────────────
    else if (tipo === 'horas') {
        const totalAsig  = data.reduce((s, d) => s + parseFloat(d.horas_asignadas   || 0), 0);
        const totalComp  = data.reduce((s, d) => s + parseFloat(d.horas_completadas || 0), 0);
        const totalPend  = totalAsig - totalComp;
        const promAsig   = data.length > 0 ? (totalAsig / data.length).toFixed(1) : 0;
        const maxAsig    = data.length > 0 ? parseFloat(data[0].horas_asignadas || 0) : 0;

        // Agrupar por tipo
        const porTipo = {};
        data.forEach(d => {
            const tp = d.tipo_docente || 'Sin tipo';
            if (!porTipo[tp]) porTipo[tp] = { horas: 0, docentes: 0 };
            porTipo[tp].horas   += parseFloat(d.horas_asignadas || 0);
            porTipo[tp].docentes++;
        });

        contenidoPanel = `
            <div class="row">
                <div class="col-md-5">
                    <h6 class="fw-semibold mb-2">Resumen del Ranking</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tbody>
                            <tr><td class="text-muted">Docentes en ranking</td><td class="text-center fw-bold">${data.length}</td></tr>
                            <tr><td class="text-muted">Total horas asignadas</td><td class="text-center fw-bold text-success">${Math.round(totalAsig)}</td></tr>
                            <tr><td class="text-muted">Total horas completadas</td><td class="text-center fw-bold text-primary">${Math.round(totalComp)}</td></tr>
                            <tr><td class="text-muted">Horas pendientes</td><td class="text-center fw-bold text-danger">${Math.round(totalPend)}</td></tr>
                            <tr><td class="text-muted">Promedio por docente</td><td class="text-center fw-bold">${promAsig}h</td></tr>
                            <tr><td class="text-muted">Máximo (1er lugar)</td><td class="text-center fw-bold text-warning">${Math.round(maxAsig)}h</td></tr>
                        </tbody>
                    </table>
                    <h6 class="fw-semibold mb-2">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tipo</th><th class="text-center">Docentes</th><th class="text-center">Horas</th></tr></thead>
                        <tbody>
                            ${Object.entries(porTipo).map(([tp, v]) =>
                                `<tr><td>${tp}</td><td class="text-center">${v.docentes}</td><td class="text-center fw-bold">${Math.round(v.horas)}</td></tr>`
                            ).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2">Detalle Top ${Math.min(10, data.length)}</h6>
                    <div style="max-height:280px;overflow-y:auto;">
                        ${data.slice(0, 10).map(d => {
                            const asig = parseFloat(d.horas_asignadas || 0);
                            const comp = parseFloat(d.horas_completadas || 0);
                            const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
                            const cls  = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
                            const ini  = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
                            return `
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                     style="width:32px;height:32px;font-size:.7rem;">${ini}</div>
                                <div style="flex:1;min-width:0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-semibold text-truncate">${d.primer_nombre} ${d.primer_apellido}</small>
                                        <small class="ms-2 text-${cls} fw-bold flex-shrink-0">${pct}%</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height:5px;">
                                            <div class="progress-bar bg-${cls}" style="width:${pct}%;"></div>
                                        </div>
                                        <small class="text-muted flex-shrink-0">${Math.round(comp)}/${Math.round(asig)}h</small>
                                    </div>
                                </div>
                            </div>`;
                        }).join('')}
                    </div>
                </div>
            </div>`;
    }

    // ── Panel: Avance por tramos ──────────────────────────────────────────────
    else if (tipo === 'avance') {
        const tramos = [
            { label: 'Sin avance (0%)',        cls: 'danger',  min: 0,  max: 0  },
            { label: 'Bajo (1–39%)',            cls: 'danger',  min: 1,  max: 39 },
            { label: 'Moderado (40–74%)',       cls: 'warning', min: 40, max: 74 },
            { label: 'Avanzado (75–99%)',       cls: 'success', min: 75, max: 99 },
            { label: 'Completado (100%)',       cls: 'primary', min: 100,max: 100}
        ];

        const counts = tramos.map(tr => ({
            ...tr,
            n: data.filter(d => {
                const asig = parseFloat(d.horas_asignadas || 0);
                const comp = parseFloat(d.horas_completadas || 0);
                const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
                if (tr.min === 0 && tr.max === 0) return pct === 0;
                if (tr.min === 100)               return pct >= 100;
                return pct >= tr.min && pct <= tr.max;
            }).length
        }));

        contenidoPanel = `
            <div class="row">
                <div class="col-md-5">
                    <h6 class="fw-semibold mb-2">Docentes por tramo de avance</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tramo</th><th class="text-center">Docentes</th><th>Distribución</th></tr></thead>
                        <tbody>
                            ${counts.map(tr => {
                                const p = data.length > 0 ? Math.round((tr.n / data.length) * 100) : 0;
                                return `
                                <tr>
                                    <td><span class="badge bg-${tr.cls}">${tr.label}</span></td>
                                    <td class="text-center fw-bold">${tr.n}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar bg-${tr.cls}" style="width:${p}%;"></div>
                                            </div>
                                            <small>${p}%</small>
                                        </div>
                                    </td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2">Docentes con avance bajo (&lt;40%)</h6>
                    ${data.filter(d => {
                        const asig = parseFloat(d.horas_asignadas || 0);
                        const comp = parseFloat(d.horas_completadas || 0);
                        return asig > 0 && Math.round((comp / asig) * 100) < 40;
                    }).length === 0
                        ? `<div class="text-center py-3 text-muted">
                               <i class="bi bi-check-circle-fill text-success" style="font-size:1.8rem;"></i>
                               <p class="mt-2 small">Todos los docentes tienen avance ≥ 40%.</p>
                           </div>`
                        : `<div class="table-responsive">
                               <table class="table table-sm table-bordered">
                                   <thead class="table-light"><tr><th>Docente</th><th>Horas</th><th>Avance</th><th>Cursos</th></tr></thead>
                                   <tbody>
                                       ${data.filter(d => {
                                           const asig = parseFloat(d.horas_asignadas || 0);
                                           const comp = parseFloat(d.horas_completadas || 0);
                                           return asig > 0 && Math.round((comp / asig) * 100) < 40;
                                       }).map(d => {
                                           const asig = parseFloat(d.horas_asignadas || 0);
                                           const comp = parseFloat(d.horas_completadas || 0);
                                           const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
                                           return `
                                           <tr>
                                               <td>${d.primer_nombre} ${d.primer_apellido}</td>
                                               <td class="text-center">${Math.round(comp)}/${Math.round(asig)}h</td>
                                               <td><span class="badge bg-danger">${pct}%</span></td>
                                               <td class="text-center">${parseInt(d.cursos_activos||0)}</td>
                                           </tr>`;
                                       }).join('')}
                                   </tbody>
                               </table>
                           </div>`
                    }
                </div>
            </div>`;
    }

    // ── Panel: Top Docente ────────────────────────────────────────────────────
    else if (tipo === 'top') {
        const top = data.length > 0 ? data[0] : null;
        if (!top) {
            contenidoPanel = `<div class="text-center py-3 text-muted">
                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                <p class="mt-2">Sin datos para ${anioCarga}.</p>
            </div>`;
        } else {
            const asig = parseFloat(top.horas_asignadas   || 0);
            const comp = parseFloat(top.horas_completadas || 0);
            const pend = asig - comp;
            const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
            const cls  = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
            const ini  = `${(top.primer_nombre||'')[0]||''}${(top.primer_apellido||'')[0]||''}`.toUpperCase();
            const clsTipo = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
            const clsEst  = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

            contenidoPanel = `
                <div class="row">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                 style="width:64px;height:64px;font-size:1.3rem;">${ini}</div>
                            <div>
                                <h5 class="mb-0 fw-bold">${top.primer_nombre} ${top.primer_apellido}</h5>
                                <div class="d-flex gap-2 mt-1 flex-wrap">
                                    <span class="badge bg-${clsTipo[top.tipo_docente]||'secondary'}">${top.tipo_docente||'—'}</span>
                                    <span class="badge bg-${clsEst[top.estado_docente]||'secondary'}">${top.estado_docente||'—'}</span>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill me-1"></i>#1 carga</span>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="card border-0 bg-light py-2">
                                    <h4 class="fw-bold text-success mb-0">${Math.round(asig)}</h4>
                                    <small class="text-muted">H. asignadas</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-0 bg-light py-2">
                                    <h4 class="fw-bold text-primary mb-0">${Math.round(comp)}</h4>
                                    <small class="text-muted">H. completadas</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-0 bg-light py-2">
                                    <h4 class="fw-bold text-danger mb-0">${Math.round(pend)}</h4>
                                    <small class="text-muted">H. pendientes</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Avance</span>
                                <strong class="text-${cls}">${pct}%</strong>
                            </div>
                            <div class="progress" style="height:12px;border-radius:6px;">
                                <div class="progress-bar bg-${cls}" style="width:${pct}%;"></div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="badge bg-info text-dark fs-6">
                                <i class="bi bi-book-fill me-1"></i>
                                ${parseInt(top.cursos_activos||0)} curso${parseInt(top.cursos_activos||0) !== 1 ? 's' : ''} activo${parseInt(top.cursos_activos||0) !== 1 ? 's' : ''}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-2">Posición en el ranking</h6>
                        <div style="max-height:260px;overflow-y:auto;">
                            <table class="table table-sm table-hover">
                                <thead class="table-light"><tr><th>Pos.</th><th>Docente</th><th class="text-center">Horas</th><th class="text-center">Avance</th></tr></thead>
                                <tbody>
                                    ${data.slice(0, 10).map((d, i) => {
                                        const a = parseFloat(d.horas_asignadas || 0);
                                        const c = parseFloat(d.horas_completadas || 0);
                                        const p = a > 0 ? Math.round((c / a) * 100) : 0;
                                        const cl = p >= 75 ? 'success' : p >= 40 ? 'warning' : 'danger';
                                        const clsPos = i===0?'bg-warning text-dark':i===1?'bg-secondary':i===2?'bg-danger':'bg-light text-dark border';
                                        return `
                                        <tr class="${i===0?'table-warning':''}">
                                            <td><span class="badge ${clsPos}">${i+1}</span></td>
                                            <td class="${i===0?'fw-bold':''}">${d.primer_nombre} ${d.primer_apellido}</td>
                                            <td class="text-center">${Math.round(a)}h</td>
                                            <td class="text-center"><span class="badge bg-${cl}">${p}%</span></td>
                                        </tr>`;
                                    }).join('')}
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
function renderFilaCarga(item, idx, categoria) {
    const asig   = parseFloat(item.horas_asignadas   || 0);
    const comp   = parseFloat(item.horas_completadas || 0);
    const pct    = asig > 0 ? Math.round((comp / asig) * 100) : 0;
    const clsPct = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
    const nivel  = pct >= 75 ? 'Avanzado' : pct >= 40 ? 'Moderado' : 'Bajo';
    const clsPos = idx===0?'bg-warning text-dark':idx===1?'bg-secondary':idx===2?'bg-danger':'bg-light text-dark border';
    const clsTipo= { Fijo:'bg-primary', Contratado:'bg-info', PDL:'bg-success', Voluntario:'bg-secondary' };
    const ini    = `${(item.primer_nombre||'')[0]||''}${(item.primer_apellido||'')[0]||''}`.toUpperCase();

    return `
    <tr style="cursor:pointer;" onclick="mostrarDetalleCargaTabla(${idx})">
        <td>
            <div class="d-flex align-items-center gap-1">
                <span class="badge ${clsPos}">${idx+1}</span>
                ${idx === 0 ? '<i class="bi bi-trophy-fill text-warning"></i>' : ''}
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-${categoria.color} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:38px;height:38px;font-size:.75rem;font-weight:bold;">
                    ${ini}
                </div>
                <strong>${item.primer_nombre} ${item.primer_apellido}</strong>
            </div>
        </td>
        <td><span class="badge bg-success rounded-pill">${Math.round(asig)}h</span></td>
        <td><span class="badge bg-primary rounded-pill">${Math.round(comp)}h</span></td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px;max-width:80px;">
                    <div class="progress-bar bg-${clsPct}" style="width:${pct}%;"></div>
                </div>
                <small class="fw-bold text-${clsPct}">${pct}%</small>
            </div>
        </td>
        <td class="text-center"><span class="badge bg-info text-dark">${parseInt(item.cursos_activos||0)}</span></td>
        <td><span class="badge bg-${clsPct}">${nivel}</span></td>
    </tr>`;
}

// ─── Gráfico ──────────────────────────────────────────────────────────────────
function renderGraficoCarga(data, categoria) {
    const ctx = document.getElementById('graficoCargaHoraria')?.getContext('2d');
    if (!ctx) return;

    if (cargaHorariaChart) {
        cargaHorariaChart.destroy();
        cargaHorariaChart = null;
    }

    const top = data.slice(0, Math.min(8, data.length));
    if (top.length === 0) return;

    cargaHorariaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(d => `${d.primer_nombre.charAt(0)}. ${d.primer_apellido}`),
            datasets: [
                {
                    label: 'Horas Asignadas',
                    data: top.map(d => parseFloat(d.horas_asignadas || 0)),
                    backgroundColor: 'rgba(255,193,7,.5)',
                    borderColor:     'rgba(255,193,7,1)',
                    borderWidth: 2,
                    borderRadius: 4,
                },
                {
                    label: 'Horas Completadas',
                    data: top.map(d => parseFloat(d.horas_completadas || 0)),
                    backgroundColor: 'rgba(25,135,84,.7)',
                    borderColor:     'rgba(25,135,84,1)',
                    borderWidth: 2,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const d = top[ctx.dataIndex];
                            const asig = parseFloat(d.horas_asignadas || 0);
                            const comp = parseFloat(d.horas_completadas || 0);
                            const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
                            return ctx.datasetIndex === 0
                                ? [`${d.primer_nombre} ${d.primer_apellido}`, `Asignadas: ${Math.round(asig)}h`, `Avance: ${pct}%`]
                                : [`Completadas: ${Math.round(comp)}h`];
                        }
                    },
                    backgroundColor: 'rgba(0,0,0,.82)',
                    padding: 12,
                    cornerRadius: 6
                }
            },
            onClick(_, elements) {
                if (elements.length > 0) mostrarDetalleCarga(elements[0].index);
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Horas', color: '#666', font: { size: 12 } },
                    grid:  { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    title: { display: true, text: 'Docentes', color: '#666', font: { size: 12 } },
                    grid:  { display: false }
                }
            },
            animation: { duration: 900, easing: 'easeOutQuart' }
        }
    });
}

// ─── Panel lateral de detalle ─────────────────────────────────────────────────
function mostrarDetalleCarga(idx) {
    const d = cargaHorariaData[idx];
    if (!d) return;

    const asig = parseFloat(d.horas_asignadas   || 0);
    const comp = parseFloat(d.horas_completadas || 0);
    const pend = asig - comp;
    const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
    const cls  = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
    const ini  = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
    const clsTipo = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
    const clsEst  = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

    document.getElementById('tituloCargaDetalle').textContent = `Detalle: ${d.primer_nombre}`;

    document.getElementById('detalleCarga').innerHTML = `
        <div class="text-start">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                     style="width:60px;height:60px;font-size:1.25rem;">${ini}</div>
                <div>
                    <h5 class="mb-0 fw-bold">${d.primer_nombre} ${d.primer_apellido}</h5>
                    <div class="d-flex gap-2 mt-1 flex-wrap">
                        <span class="badge bg-${clsTipo[d.tipo_docente]||'secondary'}">${d.tipo_docente||'—'}</span>
                        <span class="badge bg-${clsEst[d.estado_docente]||'secondary'}">${d.estado_docente||'—'}</span>
                    </div>
                </div>
            </div>

            <div class="row text-center mb-3">
                <div class="col-4">
                    <div class="card border-0 bg-light py-2">
                        <h4 class="fw-bold text-success mb-0">${Math.round(asig)}</h4>
                        <small class="text-muted">H. Asignadas</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 bg-light py-2">
                        <h4 class="fw-bold text-primary mb-0">${Math.round(comp)}</h4>
                        <small class="text-muted">H. Completadas</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 bg-light py-2">
                        <h4 class="fw-bold text-danger mb-0">${Math.round(pend)}</h4>
                        <small class="text-muted">H. Pendientes</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Avance general</span>
                    <strong class="text-${cls}">${pct}%</strong>
                </div>
                <div class="progress" style="height:12px;border-radius:6px;">
                    <div class="progress-bar bg-${cls}" style="width:${pct}%;"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                <span class="text-muted small">Cursos activos:</span>
                <span class="badge bg-info text-dark fs-6">${parseInt(d.cursos_activos||0)}</span>
            </div>
        </div>`;
}

function mostrarDetalleCargaTabla(idx) {
    mostrarDetalleCarga(idx);
    if (idx < 8 && cargaHorariaChart) {
        const meta = cargaHorariaChart.getDatasetMeta(0);
        const bar  = meta.data[idx];
        if (bar) {
            const orig = bar.options.borderWidth;
            bar.options.borderWidth = 4;
            cargaHorariaChart.update('none');
            setTimeout(() => {
                bar.options.borderWidth = orig;
                cargaHorariaChart.update('none');
            }, 900);
        }
    }
}

// ─── Ordenar tabla ────────────────────────────────────────────────────────────
function ordenarTablaCarga(criterio, evt) {
    const tbody = document.querySelector('#tablaCargaHoraria tbody');
    if (!tbody) return;

    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color: 'warning' };
    let sorted = [...cargaHorariaData];

    if (criterio === 'nombre') {
        sorted.sort((a, b) =>
            `${a.primer_nombre} ${a.primer_apellido}`.localeCompare(`${b.primer_nombre} ${b.primer_apellido}`)
        );
    } else if (criterio === 'avance') {
        sorted.sort((a, b) => {
            const pa = parseFloat(a.horas_asignadas||0) > 0
                ? (parseFloat(a.horas_completadas||0) / parseFloat(a.horas_asignadas||0)) : 0;
            const pb = parseFloat(b.horas_asignadas||0) > 0
                ? (parseFloat(b.horas_completadas||0) / parseFloat(b.horas_asignadas||0)) : 0;
            return pb - pa;
        });
    } else {
        sorted.sort((a, b) => parseFloat(b.horas_asignadas||0) - parseFloat(a.horas_asignadas||0));
    }

    tbody.innerHTML = sorted.map((item, idx) => renderFilaCarga(item, idx, categoria)).join('');

    document.querySelectorAll('#tablaCargaHoraria').closest('.card-body')
        ?.querySelectorAll('.btn-group .btn')
        .forEach(b => b.classList.remove('active'));
    if (evt?.target) evt.target.classList.add('active');
}

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarCargaHorariaDocentes() {
    if (cargaHorariaChart) {
        cargaHorariaChart.destroy();
        cargaHorariaChart = null;
    }
    cargaHorariaData     = [];
    anioCarga            = new Date().getFullYear();
    cargaTotalesActuales = null;
    cargaPanelActivo     = null;
}

// ─── Estilos ──────────────────────────────────────────────────────────────────
(function inyectarEstilosCarga() {
    if (document.getElementById('_estCarga')) return;
    const s = document.createElement('style');
    s.id = '_estCarga';
    s.textContent = `
        .kpi-card { transition: transform .25s, box-shadow .25s; cursor: pointer; }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.13) !important; }
        .rp-kpi-card-label { font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#666; margin:.25rem 0 0; }
        .rp-kpi-card-sub   { font-size:.72rem; color:#999; margin:0; }
    `;
    document.head.appendChild(s);
})();

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarCargaHorariaDocentes   = cargarCargaHorariaDocentes;
window.limpiarCargaHorariaDocentes  = limpiarCargaHorariaDocentes;
window.ordenarTablaCarga            = ordenarTablaCarga;
window.mostrarDetalleCargaTabla     = mostrarDetalleCargaTabla;

console.log('RE-Docente-CargaHoraria.js cargado');

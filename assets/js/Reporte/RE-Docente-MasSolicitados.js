// ========== REPORTE: DOCENTES MÁS SOLICITADOS ==========
// Conectado a: api_reportes.php
// Acciones usadas: docentes_mas_solicitados, docentes_total, anios_disponibles

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_DOCENTES_SOL = '../controlador/Reporte/api_reportes.php';

let docentesSolicitadosChart = null;
let docentesSolicitadosData  = [];   // datos del ranking desde la API
let anioDocentesSol          = new Date().getFullYear();

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
    const contentDiv = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                   || { color: 'warning', nombre: 'Docentes' };

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
        const anios = await apiFetchSol('anios_disponibles');
        const select = document.getElementById('selectAnioDocentesSol');
        if (select && Array.isArray(anios) && anios.length > 0) {
            anios.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a;
                opt.textContent = a;
                if (a === anioDocentesSol) opt.selected = true;
                select.appendChild(opt);
            });
            select.addEventListener('change', e => {
                anioDocentesSol = parseInt(e.target.value);
                cargarContenidoDocSol();
            });
        }
    } catch (_) { /* continuar con el año actual */ }

    cargarContenidoDocSol();
}

async function cargarContenidoDocSol() {
    const loadEl  = document.getElementById('loadingDocentesSolicitados');
    const contEl  = document.getElementById('contenidoDocentesSolicitados');
    const errorEl = document.getElementById('errorDocentesSolicitados');
    const errMsg  = document.getElementById('errorMsgDocSol');

    loadEl.style.display  = 'flex';
    loadEl.style.flexDirection = 'column';
    loadEl.style.alignItems = 'center';
    contEl.style.display  = 'none';
    errorEl.style.display = 'none';

    // Destruir gráfico anterior
    if (docentesSolicitadosChart) {
        docentesSolicitadosChart.destroy();
        docentesSolicitadosChart = null;
    }

    try {
        const [ranking, totales] = await Promise.all([
            apiFetchSol('docentes_mas_solicitados', { limite: 15 }),
            apiFetchSol('docentes_total'),
        ]);

        docentesSolicitadosData = Array.isArray(ranking) ? ranking : [];
        renderDocentesSolicitados(docentesSolicitadosData, totales);

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
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId)
                   || { color: 'warning' };

    loadEl.style.display = 'none';
    contEl.style.display = 'block';

    const tot = totalesObj?.totales || {};
    const totalDocentes   = parseInt(tot.total         || 0);
    const totalDisponible = parseInt(tot.disponible    || 0);
    const totalAsignado   = parseInt(tot.asignado      || 0);
    const totalInactivo   = parseInt(tot.inactivo      || 0);

    // Totales del ranking
    const totalCursos = data.reduce((s, d) => s + parseInt(d.cursos_asignados || 0), 0);
    const topDocente  = data.length > 0 ? data[0] : null;

    contEl.innerHTML = `
        <!-- ── KPI Cards ── -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 kpi-hover">
                    <div class="card-body text-center">
                        <div class="display-6 text-${categoria.color}"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title mt-2">Total Docentes</h5>
                        <h3 class="text-primary">${totalDocentes.toLocaleString()}</h3>
                        <small class="text-muted">${totalDisponible} disponibles · ${totalAsignado} asignados</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 kpi-hover">
                    <div class="card-body text-center">
                        <div class="display-6 text-${categoria.color}"><i class="bi bi-book-fill"></i></div>
                        <h5 class="card-title mt-2">Cursos Asignados</h5>
                        <h3 class="text-success">${totalCursos.toLocaleString()}</h3>
                        <small class="text-muted">En el ranking top ${data.length}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 kpi-hover">
                    <div class="card-body text-center">
                        <div class="display-6 text-${categoria.color}"><i class="bi bi-person-x-fill"></i></div>
                        <h5 class="card-title mt-2">Inactivos</h5>
                        <h3 class="text-danger">${totalInactivo.toLocaleString()}</h3>
                        <small class="text-muted">Sin cursos activos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 kpi-hover">
                    <div class="card-body text-center">
                        <div class="display-6 text-${categoria.color}"><i class="bi bi-trophy-fill"></i></div>
                        <h5 class="card-title mt-2">Más Solicitado</h5>
                        <h3 class="text-warning" style="font-size:1.15rem;">
                            ${topDocente ? `${topDocente.primer_nombre} ${topDocente.primer_apellido}` : '—'}
                        </h3>
                        <small class="text-muted">${topDocente ? topDocente.cursos_asignados + ' cursos asignados' : 'Sin datos'}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Gráfico + Detalle ── -->
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

        <!-- ── Tabla ranking ── -->
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
}

// ─── Fila de tabla ────────────────────────────────────────────────────────────
function renderFilaDocSol(item, idx, totalCursos, totalDocentes, categoria) {
    const cursos     = parseInt(item.cursos_asignados || 0);
    const maxCursos  = docentesSolicitadosData.length > 0
                       ? parseInt(docentesSolicitadosData[0].cursos_asignados || 1)
                       : 1;
    const pct        = Math.min(Math.round((cursos / maxCursos) * 100), 100);
    const nivel      = cursos >= maxCursos * 0.7 ? 'Alto'
                     : cursos >= maxCursos * 0.4 ? 'Moderado' : 'Básico';
    const clsNivel   = nivel === 'Alto' ? 'bg-danger' : nivel === 'Moderado' ? 'bg-warning text-dark' : 'bg-info';
    const clsPos     = idx === 0 ? 'bg-warning text-dark' : idx === 1 ? 'bg-secondary' : idx === 2 ? 'bg-danger' : 'bg-light text-dark border';
    const clsTipo    = { Fijo:'bg-primary', Contratado:'bg-info', PDL:'bg-success', Voluntario:'bg-secondary' };
    const clsEst     = { Disponible:'bg-success', Asignado:'bg-warning text-dark', 'En formación':'bg-info', Inactivo:'bg-secondary' };
    const iniciales  = `${(item.primer_nombre||'')[0]||''}${(item.primer_apellido||'')[0]||''}`.toUpperCase();

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

// ─── Panel de detalle ─────────────────────────────────────────────────────────
function mostrarDetalleDocSol(idx) {
    const d = docentesSolicitadosData[idx];
    if (!d) return;

    const cursos   = parseInt(d.cursos_asignados || 0);
    const total    = docentesSolicitadosData.reduce((s, x) => s + parseInt(x.cursos_asignados || 0), 0);
    const pctGlobal = total > 0 ? ((cursos / total) * 100).toFixed(1) : 0;
    const iniciales = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
    const nivel     = cursos >= docentesSolicitadosData[0]?.cursos_asignados * 0.7 ? 'Alto'
                    : cursos >= docentesSolicitadosData[0]?.cursos_asignados * 0.4 ? 'Moderado' : 'Básico';
    const clsNivel  = nivel === 'Alto' ? 'danger' : nivel === 'Moderado' ? 'warning' : 'info';
    const clsTipo   = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
    const clsEst    = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

    const detEl  = document.getElementById('detalleDocSol');
    const titEl  = document.getElementById('tituloDetalleDocSol');

    titEl.textContent = `Detalle: ${d.primer_nombre}`;

    detEl.innerHTML = `
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
    // Resaltar barra si está en el top 8 visible en la gráfica
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

    const filas = Array.from(tbody.querySelectorAll('tr'));
    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color: 'warning' };

    filas.sort((a, b) => {
        if (criterio === 'nombre') {
            const nA = a.querySelector('strong')?.textContent.trim().toLowerCase() || '';
            const nB = b.querySelector('strong')?.textContent.trim().toLowerCase() || '';
            return nA.localeCompare(nB);
        }
        // cursos
        const vA = parseInt(a.querySelector('.badge.bg-primary')?.textContent || 0);
        const vB = parseInt(b.querySelector('.badge.bg-primary')?.textContent || 0);
        return vB - vA;
    });

    filas.forEach(f => tbody.appendChild(f));

    // Recalcular posiciones
    const totalCursos = docentesSolicitadosData.reduce((s, d) => s + parseInt(d.cursos_asignados || 0), 0);
    filas.forEach((fila, idx) => {
        const posCell = fila.querySelector('td:first-child');
        const clsPos  = idx === 0 ? 'bg-warning text-dark' : idx === 1 ? 'bg-secondary' : idx === 2 ? 'bg-danger' : 'bg-light text-dark border';
        posCell.innerHTML = `
            <div class="d-flex align-items-center gap-1">
                <span class="badge ${clsPos}">${idx + 1}</span>
                ${idx === 0 ? '<i class="bi bi-trophy-fill text-warning"></i>' : ''}
            </div>`;
    });

    // Botones activos
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    if (evt?.target) evt.target.classList.add('active');
}

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarDocentesMasSolicitados() {
    if (docentesSolicitadosChart) {
        docentesSolicitadosChart.destroy();
        docentesSolicitadosChart = null;
    }
    docentesSolicitadosData = [];
    anioDocentesSol = new Date().getFullYear();
}

// ─── Estilos ──────────────────────────────────────────────────────────────────
(function inyectarEstilosDocSol() {
    if (document.getElementById('_estDocSol')) return;
    const s = document.createElement('style');
    s.id = '_estDocSol';
    s.textContent = `
        .kpi-hover { transition: transform .25s, box-shadow .25s; }
        .kpi-hover:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.13) !important; }
    `;
    document.head.appendChild(s);
})();

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarDocentesMasSolicitados    = cargarDocentesMasSolicitados;
window.limpiarDocentesMasSolicitados   = limpiarDocentesMasSolicitados;
window.ordenarTablaDocSol              = ordenarTablaDocSol;
window.mostrarDetalleDocSolTabla       = mostrarDetalleDocSolTabla;

console.log('RE-Docente-MasSolicitados.js');
// ========== REPORTE: CARGA HORARIA POR DOCENTE ==========
// Conectado a: api_reportes.php
// Acciones usadas: docentes_carga_horaria, docentes_total, anios_disponibles, docente_detalle_cursos
// Reemplaza a: RE-Docente-MasSolicitados.js

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_CARGA = '../controlador/Reporte/api_reportes.php';

let cargaHorariaChart = null;
let cargaHorariaData  = [];
let anioCarga         = new Date().getFullYear();
let cargaTotalesActuales = null;
let cargaPanelActivo     = null;

// Filtros y ordenación globales
let cargaFiltroBusqueda = '';
let cargaFiltroTipo     = '';
let cargaFiltroEstado   = '';
let cargaCriterioOrden  = 'horas'; // 'horas', 'avance', 'nombre'
let periodoCarga        = 'year';
let periodoValorCarga   = null;

// ─── Helper fetch ─────────────────────────────────────────────────────────────
async function apiFetchCarga(accion, params = {}) {
    const url = new URL(API_URL_CARGA, window.location.href);
    url.searchParams.set('accion', accion);
    url.searchParams.set('anio', anioCarga);
    Object.entries(params).forEach(([k, v]) => {
        if (v !== null && v !== undefined && String(v).trim() !== '') {
            url.searchParams.set(k, v);
        }
    });
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

    // Limpiar estado
    cargaHorariaChart      = null;
    cargaHorariaData       = [];
    cargaTotalesActuales   = null;
    cargaPanelActivo       = null;
    cargaFiltroBusqueda    = '';
    cargaFiltroTipo        = '';
    cargaFiltroEstado      = '';
    cargaCriterioOrden     = 'horas';

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Carga Horaria por Docente</h4>
                        <p class="mb-0 small opacity-75">Horas asignadas vs. completadas en cursos activos e históricos</p>
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
        const params = {
            limite: 100,
            periodo: periodoCarga,
            periodo_valor: periodoValorCarga
        };
        const [ranking, totalesObj] = await Promise.all([
            apiFetchCarga('docentes_carga_horaria', params), // Usamos un límite alto para poder filtrar localmente
            apiFetchCarga('docentes_total', params),
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

// ─── Helper de Filtrado y Ordenación ──────────────────────────────────────────
function obtenerDatosFiltrados() {
    let datos = [...cargaHorariaData];

    // Filtro por búsqueda
    if (cargaFiltroBusqueda) {
        const q = cargaFiltroBusqueda.toLowerCase().trim();
        datos = datos.filter(d => 
            `${d.primer_nombre} ${d.primer_apellido}`.toLowerCase().includes(q) ||
            String(d.cedula || '').includes(q)
        );
    }

    // Filtro por Tipo de Docente
    if (cargaFiltroTipo) {
        datos = datos.filter(d => d.tipo_docente === cargaFiltroTipo);
    }

    // Filtro por Estado
    if (cargaFiltroEstado) {
        datos = datos.filter(d => d.estado_docente === cargaFiltroEstado);
    }

    return datos;
}

function obtenerDatosFiltradosYOrdenados() {
    let datos = obtenerDatosFiltrados();

    if (cargaCriterioOrden === 'nombre') {
        datos.sort((a, b) =>
            `${a.primer_nombre} ${a.primer_apellido}`.localeCompare(`${b.primer_nombre} ${b.primer_apellido}`)
        );
    } else if (cargaCriterioOrden === 'avance') {
        datos.sort((a, b) => {
            const pa = parseFloat(a.horas_asignadas||0) > 0
                ? (parseFloat(a.horas_completadas||0) / parseFloat(a.horas_asignadas||0)) : 0;
            const pb = parseFloat(b.horas_asignadas||0) > 0
                ? (parseFloat(b.horas_completadas||0) / parseFloat(b.horas_asignadas||0)) : 0;
            return pb - pa;
        });
    } else { // 'horas'
        datos.sort((a, b) => parseFloat(b.horas_asignadas||0) - parseFloat(a.horas_asignadas||0));
    }

    return datos;
}

function calcularProgresoCurso(curso) {
    const cAsig = parseFloat(curso.horas_totales || 0);
    const cComp = parseFloat(curso.horas_completadas || 0);
    
    if (curso.estatus_curso === 'Culminado') {
        return {
            porcentaje: 100,
            horasAsignadas: cAsig,
            horasCompletadas: cAsig,
            estimado: false
        };
    }

    let porcentaje = cAsig > 0 ? Math.min(100, Math.round((cComp / cAsig) * 100)) : 0;
    let estimado = false;

    if (cAsig > 0 && cComp <= 0 && curso.estatus_curso === 'En proceso' && curso.fecha_inicio && curso.fecha_fin) {
        const inicio = new Date(`${curso.fecha_inicio}T00:00:00`);
        const fin = new Date(`${curso.fecha_fin}T23:59:59`);
        const ahora = new Date();
        if (ahora >= inicio && fin > inicio) {
            const totalMs = fin - inicio;
            const transcurridoMs = Math.min(totalMs, Math.max(0, ahora - inicio));
            const estimadoPct = Math.min(100, Math.round((transcurridoMs / totalMs) * 100));
            if (estimadoPct > 0) {
                porcentaje = estimadoPct;
                estimado = true;
            }
        }
    }

    return {
        porcentaje,
        horasAsignadas: cAsig,
        horasCompletadas: cComp,
        estimado
    };
}

function getCurrentISOWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

function actualizarControlesPeriodoCarga() {
    const contenedor = document.getElementById('contenedorValorPeriodoCarga');
    const selectValor = document.getElementById('selectValorPeriodoCarga');
    if (!contenedor || !selectValor) return;

    if (periodoCarga === 'year') {
        contenedor.style.display = 'none';
        periodoValorCarga = null;
        selectValor.innerHTML = '';
        return;
    }

    contenedor.style.display = 'block';
    const hoy = new Date();

    if (periodoCarga === 'month') {
        const mesActual = hoy.getMonth() + 1;
        const options = Array.from({ length: 12 }, (_, i) => {
            const value = i + 1;
            return `<option value="${value}">${new Date(0, i).toLocaleString('es-VE', { month: 'long' })}</option>`;
        }).join('');
        selectValor.innerHTML = options;
        if (!periodoValorCarga || periodoValorCarga < 1 || periodoValorCarga > 12) {
            periodoValorCarga = mesActual;
        }
    } else if (periodoCarga === 'week') {
        const actualSemana = getCurrentISOWeekNumber(hoy);
        const weeks = Array.from({ length: 52 }, (_, i) => i + 1);
        selectValor.innerHTML = weeks.map(n => `<option value="${n}">Semana ${n}</option>`).join('');
        if (!periodoValorCarga || periodoValorCarga < 1 || periodoValorCarga > 52) {
            periodoValorCarga = actualSemana;
        }
    }

    selectValor.value = periodoValorCarga;
}

function setPeriodoCargaDefault() {
    if (periodoCarga === 'month') {
        periodoValorCarga = new Date().getMonth() + 1;
    } else if (periodoCarga === 'week') {
        periodoValorCarga = getCurrentISOWeekNumber(new Date());
    } else {
        periodoValorCarga = null;
    }
}

// ─── Aplicación de Filtros en Tiempo Real ─────────────────────────────────────
function aplicarFiltrosCarga() {
    const datos = obtenerDatosFiltradosYOrdenados();
    const tbody = document.querySelector('#tablaCargaHoraria tbody');
    const categoriaId = window.currentCategoryId || 3;
    const categoria = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color: 'warning' };

    if (tbody) {
        if (datos.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i>No se encontraron docentes con los criterios seleccionados.</td></tr>`;
        } else {
            tbody.innerHTML = datos.map((item, idx) => renderFilaCarga(item, idx, categoria)).join('');
        }
    }

    if (!cargaHorariaChart) {
        renderGraficoCarga(datos, categoria);
    } else {
        actualizarGraficoCarga(datos, categoria);
    }
}

function limpiarFiltrosCarga() {
    cargaFiltroBusqueda = '';
    cargaFiltroTipo = '';
    cargaFiltroEstado = '';

    const s = document.getElementById('buscarDocenteCarga');
    if (s) s.value = '';

    const t = document.getElementById('filtroTipoDocente');
    if (t) t.value = '';

    const e = document.getElementById('filtroEstadoDocente');
    if (e) e.value = '';

    aplicarFiltrosCarga();
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
            sub:   `En cursos del año seleccionado`,
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

    // Barra de búsqueda y filtros
    const filtrosHtml = `
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <!-- Buscador -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="buscarDocenteCarga" class="form-control bg-light border-start-0" 
                                   placeholder="Buscar por nombre o cédula..." style="font-size:0.85rem;">
                        </div>
                    </div>
                    <!-- Tipo de Docente -->
                    <div class="col-md-2">
                        <select id="filtroTipoDocente" class="form-select form-select-sm bg-light" style="font-size:0.85rem;">
                            <option value="">Todos los tipos</option>
                            <option value="Fijo">Fijo</option>
                            <option value="Contratado">Contratado</option>
                            <option value="PDL">PDL</option>
                            <option value="Voluntario">Voluntario</option>
                        </select>
                    </div>
                    <!-- Estado de Docente -->
                    <div class="col-md-2">
                        <select id="filtroEstadoDocente" class="form-select form-select-sm bg-light" style="font-size:0.85rem;">
                            <option value="">Todos los estados</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Asignado">Asignado</option>
                            <option value="En formación">En formación</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <!-- Periodo -->
                    <div class="col-md-2">
                        <select id="selectPeriodoCargaFiltro" class="form-select form-select-sm bg-light" style="font-size:0.85rem;">
                            <option value="year">Año</option>
                            <option value="month">Mes</option>
                            <option value="week">Semana</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="contenedorValorPeriodoCarga" style="display:none;">
                        <select id="selectValorPeriodoCarga" class="form-select form-select-sm bg-light" style="font-size:0.85rem;"></select>
                    </div>
                    <!-- Botones -->
                    <div class="col-md-2 d-flex justify-content-md-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosCarga()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

    contEl.innerHTML = `
        ${kpisHtml}

        <!-- Panel colapsable -->
        <div id="cargaPanelDetalle" style="display:none;" class="mb-4"></div>

        <!-- Barra de filtros -->
        ${filtrosHtml}

        <!-- Gráfico + detalle lateral -->
        <div class="row mt-3" style="min-height:350px;">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Horas Asignadas vs. Completadas</span>
                            <small class="text-muted" id="topGraficoLabel">Top 8</small>
                        </h5>
                        <div style="position:relative;height:300px;">
                            <canvas id="graficoCargaHoraria"></canvas>
                        </div>
                        <p class="text-center text-muted small mt-2">
                            <i class="bi bi-hand-index me-1"></i>Clic en una barra para ver detalles
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary" id="tituloCargaDetalle">Detalle del Docente</h5>
                        <div id="detalleCarga" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history" style="font-size:2.5rem;opacity:.3;"></i>
                            <p class="mt-3">Selecciona un docente en la gráfica o en la tabla para ver su carga horaria y cursos asignados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla ranking -->
        <div class="row mt-2">
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
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaCargaHoraria">
                                <thead>
                                    <tr>
                                        <th>Pos.</th>
                                        <th>Docente</th>
                                        <th>Horas Asignadas</th>
                                        <th>Horas Completadas</th>
                                        <th>Avance</th>
                                        <th class="text-center">Cursos Totales</th>
                                        <th>Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    // Render inicial de tabla y grafico
    aplicarFiltrosCarga();

    // Event listeners para filtros
    const searchInput = document.getElementById('buscarDocenteCarga');
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            cargaFiltroBusqueda = e.target.value;
            aplicarFiltrosCarga();
        });
    }

    const selectTipo = document.getElementById('filtroTipoDocente');
    if (selectTipo) {
        selectTipo.addEventListener('change', e => {
            cargaFiltroTipo = e.target.value;
            aplicarFiltrosCarga();
        });
    }

    const selectEstado = document.getElementById('filtroEstadoDocente');
    if (selectEstado) {
        selectEstado.addEventListener('change', e => {
            cargaFiltroEstado = e.target.value;
            aplicarFiltrosCarga();
        });
    }

    const selectPeriodo = document.getElementById('selectPeriodoCargaFiltro');
    const selectValorPeriodo = document.getElementById('selectValorPeriodoCarga');
    if (selectPeriodo) {
        selectPeriodo.value = periodoCarga;
        selectPeriodo.addEventListener('change', e => {
            periodoCarga = e.target.value;
            setPeriodoCargaDefault();
            actualizarControlesPeriodoCarga();
            cargarContenidoCarga();
        });
    }
    if (selectValorPeriodo) {
        selectValorPeriodo.addEventListener('change', e => {
            periodoValorCarga = parseInt(e.target.value, 10);
            cargarContenidoCarga();
        });
    }

    actualizarControlesPeriodoCarga();

    // Event listeners para KPI cards
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
                    <h6 class="fw-semibold mb-2 text-dark">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tipo</th><th class="text-center">Cantidad</th><th class="text-center">%</th></tr></thead>
                        <tbody>
                            <tr><td><span class="badge bg-primary">Fijo</span></td><td class="text-center fw-bold text-dark">${parseInt(t.fijo)||0}</td><td class="text-center text-dark">${pct(t.fijo)}%</td></tr>
                            <tr><td><span class="badge bg-info text-dark">Contratado</span></td><td class="text-center fw-bold text-dark">${parseInt(t.contratado)||0}</td><td class="text-center text-dark">${pct(t.contratado)}%</td></tr>
                            <tr><td><span class="badge bg-success">PDL</span></td><td class="text-center fw-bold text-dark">${parseInt(t.pdl)||0}</td><td class="text-center text-dark">${pct(t.pdl)}%</td></tr>
                            <tr><td><span class="badge bg-secondary">Voluntario</span></td><td class="text-center fw-bold text-dark">${parseInt(t.voluntario)||0}</td><td class="text-center text-dark">${pct(t.voluntario)}%</td></tr>
                            <tr class="table-secondary fw-bold text-dark"><td>Total</td><td class="text-center">${totalGeneral}</td><td class="text-center">100%</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2 text-dark">Por Estado</h6>
                    ${[
                        { label: 'Disponible',    val: t.disponible,    cls: 'success' },
                        { label: 'Asignado',      val: t.assigned || t.asignado,      cls: 'warning' },
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
                            <small class="fw-bold text-dark" style="min-width:40px;">${parseInt(row.val||0)} <span class="text-muted fw-normal">(${p}%)</span></small>
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
                    <h6 class="fw-semibold mb-2 text-dark">Resumen del Ranking</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tbody>
                            <tr><td class="text-muted">Docentes en ranking</td><td class="text-center fw-bold text-dark">${data.length}</td></tr>
                            <tr><td class="text-muted">Total horas asignadas</td><td class="text-center fw-bold text-success">${Math.round(totalAsig)}</td></tr>
                            <tr><td class="text-muted">Total horas completadas</td><td class="text-center fw-bold text-primary">${Math.round(totalComp)}</td></tr>
                            <tr><td class="text-muted">Horas pendientes</td><td class="text-center fw-bold text-danger">${Math.round(totalPend)}</td></tr>
                            <tr><td class="text-muted">Promedio por docente</td><td class="text-center fw-bold text-dark">${promAsig}h</td></tr>
                            <tr><td class="text-muted">Máximo (1er lugar)</td><td class="text-center fw-bold text-warning">${Math.round(maxAsig)}h</td></tr>
                        </tbody>
                    </table>
                    <h6 class="fw-semibold mb-2 text-dark">Por Tipo de Docente</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tipo</th><th class="text-center">Docentes</th><th class="text-center">Horas</th></tr></thead>
                        <tbody>
                            ${Object.entries(porTipo).map(([tp, v]) =>
                                `<tr><td>${tp}</td><td class="text-center text-dark">${v.docentes}</td><td class="text-center fw-bold text-dark">${Math.round(v.horas)}</td></tr>`
                            ).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2 text-dark">Detalle Top ${Math.min(10, data.length)}</h6>
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
                                        <small class="fw-semibold text-truncate text-dark">${d.primer_nombre} ${d.primer_apellido}</small>
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
                    <h6 class="fw-semibold mb-2 text-dark">Docentes por tramo de avance</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Tramo</th><th class="text-center">Docentes</th><th>Distribución</th></tr></thead>
                        <tbody>
                            ${counts.map(tr => {
                                const p = data.length > 0 ? Math.round((tr.n / data.length) * 100) : 0;
                                return `
                                <tr>
                                    <td><span class="badge bg-${tr.cls}">${tr.label}</span></td>
                                    <td class="text-center fw-bold text-dark">${tr.n}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height:6px;">
                                                <div class="progress-bar bg-${tr.cls}" style="width:${p}%;"></div>
                                            </div>
                                            <small class="text-dark">${p}%</small>
                                        </div>
                                    </td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h6 class="fw-semibold mb-2 text-dark">Docentes con avance bajo (&lt;40%)</h6>
                    ${data.filter(d => {
                        const asig = parseFloat(d.horas_asignadas || 0);
                        const comp = parseFloat(d.horas_completadas || 0);
                        return asig > 0 && Math.round((comp / asig) * 100) < 40;
                    }).length === 0
                        ? `<div class="text-center py-3 text-muted">
                               <i class="bi bi-check-circle-fill text-success" style="font-size:1.8rem;"></i>
                               <p class="mt-2 small text-dark">Todos los docentes tienen avance ≥ 40%.</p>
                           </div>`
                        : `<div class="table-responsive">
                               <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
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
                                               <td class="fw-semibold text-dark">${d.primer_nombre} ${d.primer_apellido}</td>
                                               <td class="text-center text-dark">${Math.round(comp)}/${Math.round(asig)}h</td>
                                               <td><span class="badge bg-danger">${pct}%</span></td>
                                               <td class="text-center text-dark">${parseInt(d.cursos_activos||0)}</td>
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
                                <h5 class="mb-0 fw-bold text-dark">${top.primer_nombre} ${top.primer_apellido}</h5>
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
                                ${parseInt(top.cursos_activos||0)} curso${parseInt(top.cursos_activos||0) !== 1 ? 's' : ''} asignado${parseInt(top.cursos_activos||0) !== 1 ? 's' : ''}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-2 text-dark">Posición en el ranking</h6>
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
                                        <tr class="${i===0?'table-warning':''}" style="cursor:pointer" onclick="mostrarDetalleCargaTabla(${d.id_docente}, ${i+1})">
                                            <td><span class="badge ${clsPos}">${i+1}</span></td>
                                            <td class="${i===0?'fw-bold text-dark':'text-dark'}">${d.primer_nombre} ${d.primer_apellido}</td>
                                            <td class="text-center text-dark">${Math.round(a)}h</td>
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
    
    // Nivel de Carga Horaria (Semáforo de Carga)
    let nivel = 'Bajo';
    let clsNivel = 'secondary';
    
    if (parseInt(item.sobrecarga_diaria || 0) > 0) {
        nivel = 'Alerta Diaria';
        clsNivel = 'danger';
    } else if (periodoCarga === 'week' && asig >= 40) {
        nivel = 'Sobrecarga';
        clsNivel = 'danger';
    } else if (periodoCarga === 'month' && asig >= 160) {
        nivel = 'Sobrecarga';
        clsNivel = 'danger';
    } else if (periodoCarga === 'year' && asig >= 120) {
        nivel = 'Sobrecarga';
        clsNivel = 'danger';
    } else if (asig >= 40) {
        nivel = 'Normal';
        clsNivel = 'success';
    } else if (asig > 0) {
        nivel = 'Bajo';
        clsNivel = 'secondary';
    } else {
        nivel = 'Sin Asignar';
        clsNivel = 'light text-dark border';
    }

    const clsPos = idx===0?'bg-warning text-dark':idx===1?'bg-secondary':idx===2?'bg-danger':'bg-light text-dark border';
    const ini    = `${(item.primer_nombre||'')[0]||''}${(item.primer_apellido||'')[0]||''}`.toUpperCase();

    return `
    <tr style="cursor:pointer;" onclick="mostrarDetalleCargaTabla(${item.id_docente})">
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
                <strong class="text-dark">${item.primer_nombre} ${item.primer_apellido}</strong>
            </div>
        </td>
        <td class="text-dark"><span class="badge bg-success rounded-pill">${Math.round(asig)}h</span></td>
        <td class="text-dark"><span class="badge bg-primary rounded-pill">${Math.round(comp)}h</span></td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:8px;max-width:80px;">
                    <div class="progress-bar bg-${clsPct}" style="width:${pct}%;"></div>
                </div>
                <small class="fw-bold text-${clsPct}">${pct}%</small>
            </div>
        </td>
        <td class="text-center text-dark"><span class="badge bg-info text-dark">${parseInt(item.cursos_activos||0)}</span></td>
        <td><span class="badge bg-${clsNivel}">${nivel}</span></td>
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
    document.getElementById('topGraficoLabel').textContent = `Top ${top.length}`;
    if (top.length === 0) return;

    cargaHorariaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(d => {
                const nombre = String(d.primer_nombre || '');
                const apellido = String(d.primer_apellido || '');
                return `${nombre.charAt(0) || '-'}${nombre ? '.' : ''} ${apellido}`.trim();
            }),
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
                if (elements.length > 0) {
                    const idx = elements[0].index;
                    const datosFiltrados = obtenerDatosFiltradosYOrdenados();
                    const topFiltered = datosFiltrados.slice(0, Math.min(8, data.length));
                    const docente = topFiltered[idx];
                    if (docente) {
                        const posicion = datosFiltrados.findIndex(x => parseInt(x.id_docente) === parseInt(docente.id_docente));
                        mostrarDetalleCarga(docente, posicion !== -1 ? posicion + 1 : undefined);
                    }
                }
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

function actualizarGraficoCarga(datosFiltrados, categoria) {
    if (!cargaHorariaChart) return;
    const top = datosFiltrados.slice(0, Math.min(8, datosFiltrados.length));
    
    document.getElementById('topGraficoLabel').textContent = `Top ${top.length}`;
    if (top.length === 0) {
        cargaHorariaChart.data.labels = [];
        cargaHorariaChart.data.datasets[0].data = [];
        cargaHorariaChart.data.datasets[1].data = [];
    } else {
        cargaHorariaChart.data.labels = top.map(d => {
            const nombre = String(d.primer_nombre || '');
            const apellido = String(d.primer_apellido || '');
            return `${nombre.charAt(0) || '-'}${nombre ? '.' : ''} ${apellido}`.trim();
        });
        cargaHorariaChart.data.datasets[0].data = top.map(d => parseFloat(d.horas_asignadas || 0));
        cargaHorariaChart.data.datasets[1].data = top.map(d => parseFloat(d.horas_completadas || 0));
    }
    cargaHorariaChart.update();
}

// ─── Panel lateral de detalle ─────────────────────────────────────────────────
async function mostrarDetalleCarga(d, posicion) {
    if (!d) return;

    const asig = parseFloat(d.horas_asignadas   || 0);
    const comp = parseFloat(d.horas_completadas || 0);
    const pend = asig - comp;
    const pct  = asig > 0 ? Math.round((comp / asig) * 100) : 0;
    const cls  = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
    const ini  = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
    const clsTipo = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
    const clsEst  = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

    // Nivel de Carga / Semáforo
    let cargaNivel = 'Baja';
    let cargaColor = 'secondary';
    let cargaIcon = 'bi-check-circle-fill';
    let cargaDesc = 'El docente tiene una carga de trabajo baja.';

    if (parseInt(d.sobrecarga_diaria || 0) > 0) {
        cargaNivel = 'Alerta diaria';
        cargaColor = 'danger';
        cargaIcon = 'bi-exclamation-triangle-fill';
        cargaDesc = 'En el período seleccionado, suma de horas por clase de cursos solapados excede las 8 horas diarias.';
    } else if (periodoCarga === 'week' && asig >= 40) {
        cargaNivel = 'Sobrecarga semanal';
        cargaColor = 'danger';
        cargaIcon = 'bi-exclamation-triangle-fill';
        cargaDesc = 'El docente tiene una carga excesiva para la semana seleccionada.';
    } else if (periodoCarga === 'month' && asig >= 160) {
        cargaNivel = 'Sobrecarga mensual';
        cargaColor = 'danger';
        cargaIcon = 'bi-exclamation-triangle-fill';
        cargaDesc = 'El docente tiene una carga excesiva para el mes seleccionado.';
    } else if (periodoCarga === 'year' && asig >= 120) {
        cargaNivel = 'Sobrecarga anual';
        cargaColor = 'danger';
        cargaIcon = 'bi-exclamation-triangle-fill';
        cargaDesc = 'El docente supera las 120 horas asignadas en el año. Riesgo de sobrecarga laboral.';
    } else if (asig >= 40) {
        cargaNivel = 'Carga Normal';
        cargaColor = 'success';
        cargaIcon = 'bi-person-check-fill';
        cargaDesc = 'Carga de trabajo apropiada para el período seleccionado.';
    }

    const tituloEl = document.getElementById('tituloCargaDetalle');
    if (tituloEl) {
        tituloEl.textContent = posicion ? `Detalle: ${d.primer_nombre} ${d.primer_apellido} — Pos. ${posicion}` : `Detalle: ${d.primer_nombre} ${d.primer_apellido}`;
    }

    document.getElementById('detalleCarga').innerHTML = `
        <div class="text-start">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                     style="width:60px;height:60px;font-size:1.25rem;">${ini}</div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">${d.primer_nombre} ${d.primer_apellido}</h5>
                    <div class="d-flex gap-2 mt-1 flex-wrap">
                        <span class="badge bg-${clsTipo[d.tipo_docente]||'secondary'}">${d.tipo_docente||'—'}</span>
                        <span class="badge bg-${clsEst[d.estado_docente]||'secondary'}">${d.estado_docente||'—'}</span>
                    </div>
                </div>
            </div>

            <!-- Semáforo de Carga -->
            <div class="alert alert-${cargaColor} bg-opacity-10 text-${cargaColor} border-${cargaColor} border-opacity-25 py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                <i class="bi ${cargaIcon} fs-5"></i>
                <div>
                    <strong class="text-uppercase">${cargaNivel}</strong>: ${cargaDesc}
                </div>
            </div>

            <div class="row text-center mb-3">
                <div class="col-4 col-sm-4">
                    <div class="card border-0 bg-light py-2 shadow-sm">
                        <h4 class="fw-bold text-success mb-0" style="font-size: 1.25rem;">${Math.round(asig)}</h4>
                        <small class="text-muted" style="font-size: 0.65rem;">H. Asignadas</small>
                    </div>
                </div>
                <div class="col-4 col-sm-4">
                    <div class="card border-0 bg-light py-2 shadow-sm">
                        <h4 class="fw-bold text-primary mb-0" style="font-size: 1.25rem;">${Math.round(comp)}</h4>
                        <small class="text-muted" style="font-size: 0.65rem;">H. Completadas</small>
                    </div>
                </div>
                <div class="col-4 col-sm-4">
                    <div class="card border-0 bg-light py-2 shadow-sm">
                        <h4 class="fw-bold text-danger mb-0" style="font-size: 1.25rem;">${Math.round(pend)}</h4>
                        <small class="text-muted" style="font-size: 0.65rem;">H. Pendientes</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Avance general</span>
                    <strong class="text-${cls}">${pct}%</strong>
                </div>
                <div class="progress" style="height:10px;border-radius:5px;">
                    <div class="progress-bar bg-${cls}" style="width:${pct}%;"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-3 mb-2">
                <span class="text-muted small">Cursos totales en el año:</span>
                <span class="badge bg-info text-dark fs-6">${parseInt(d.cursos_activos||0)}</span>
            </div>

            <!-- Listado de Cursos del Docente -->
            <div id="detalleCargaCursosList" class="mt-3">
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-muted small">Cargando cursos asignados...</span>
                </div>
            </div>
        </div>`;

    // Fetch asincrónico para obtener los cursos individuales
    try {
        const cursos = await apiFetchCarga('docente_detalle_cursos', {
            id_docente: d.id_docente,
            periodo: periodoCarga,
            periodo_valor: periodoValorCarga
        });
        const listDiv = document.getElementById('detalleCargaCursosList');
        if (!listDiv) return;

        if (!Array.isArray(cursos) || cursos.length === 0) {
            listDiv.innerHTML = `
                <div class="alert alert-light text-center border py-3 mt-3">
                    <i class="bi bi-info-circle text-muted fs-4"></i>
                    <p class="mb-0 mt-2 small text-muted">No se encontraron cursos registrados para este docente en el año ${anioCarga}.</p>
                </div>`;
            return;
        }

        const cursosHtml = `
            <div class="mt-3">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2" style="font-size: 0.85rem;">
                    <i class="bi bi-journal-bookmark-fill me-1 text-primary"></i> Cursos Asignados en ${anioCarga}
                </h6>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Horas</th>
                                <th>Progreso</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cursos.map(c => {
                                const progreso = calcularProgresoCurso(c);
                                const cAsig = progreso.horasAsignadas;
                                const cComp = progreso.horasCompletadas;
                                const cPct = progreso.porcentaje;
                                const notaEstimado = progreso.estimado ? ' *estimado' : '';

                                let badgeColor = 'bg-secondary';
                                if (c.estatus_curso === 'En proceso') badgeColor = 'bg-info text-dark';
                                else if (c.estatus_curso === 'Culminado') badgeColor = 'bg-success';
                                else if (c.estatus_curso === 'En espera') badgeColor = 'bg-warning text-dark';
                                else if (c.estatus_curso === 'Diferido') badgeColor = 'bg-danger';
                                
                                const pColor = cPct >= 75 ? 'success' : cPct >= 40 ? 'warning' : 'danger';
                                
                                return `
                                <tr class="button view-curso" data-id="${c.id_curso}" data-bs-toggle="modal" data-bs-target="#modal-viewCurso" style="cursor:pointer;">
                                    <td class="fw-semibold text-truncate text-dark" style="max-width: 120px;" title="${c.nombre_curso}">
                                        ${c.nombre_curso}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge ${badgeColor}" style="font-size: 0.6rem; padding: 0.2em 0.4em;">${c.estatus_curso}</span>
                                    </td>
                                    <td class="text-center fw-semibold text-nowrap text-dark">
                                        ${Math.round(cComp)}/${Math.round(cAsig)}h
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height: 5px; min-width: 40px;">
                                                <div class="progress-bar bg-${pColor}" style="width: ${cPct}%;"></div>
                                            </div>
                                            <span class="fw-bold text-${pColor}" style="font-size: 0.65rem;">${cPct}%${notaEstimado}</span>
                                        </div>
                                    </td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>`;
        
        listDiv.innerHTML = cursosHtml;
    } catch (err) {
        console.error('Error al cargar detalle de cursos:', err);
        const listDiv = document.getElementById('detalleCargaCursosList');
        if (listDiv) {
            listDiv.innerHTML = `
                <div class="alert alert-danger py-2 mt-3 small">
                    <i class="bi bi-exclamation-octagon me-1"></i> No se pudieron cargar los cursos del docente.
                </div>`;
        }
    }
}

function mostrarDetalleCargaPorId(idDocente, posicion) {
    const d = cargaHorariaData.find(x => parseInt(x.id_docente) === parseInt(idDocente));
    if (d) mostrarDetalleCarga(d, posicion);
}

function mostrarDetalleCargaTabla(idDocente, posicion) {
    mostrarDetalleCargaPorId(idDocente, posicion);
    
    // Buscar la posición en el gráfico del docente seleccionado en la tabla (si está en el Top 8)
    const datosFiltrados = obtenerDatosFiltradosYOrdenados();
    const top = datosFiltrados.slice(0, Math.min(8, datosFiltrados.length));
    const idx = top.findIndex(x => parseInt(x.id_docente) === parseInt(idDocente));

    if (idx !== -1 && idx < 8 && cargaHorariaChart) {
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

    // Hacer scroll suave hasta el panel de detalle y resaltar
    try {
        const el = document.getElementById('detalleCarga') || document.getElementById('tituloCargaDetalle');
        if (el) {
            const offset = 100; // ajustar si hay header fijo
            const top = el.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
            // resalto temporal
            const prev = el.style.boxShadow;
            el.style.boxShadow = '0 10px 30px rgba(2,129,254,0.18)';
            setTimeout(() => { el.style.boxShadow = prev || ''; }, 1200);
        }
    } catch (e) {
        console.warn('No fue posible desplazar hacia detalle:', e);
    }
}

// ─── Ordenar tabla ────────────────────────────────────────────────────────────
function ordenarTablaCarga(criterio, evt) {
    cargaCriterioOrden = criterio;

    // Quitar active de los botones
    if (evt?.target) {
        const btnGroup = evt.target.closest('.btn-group');
        if (btnGroup) {
            btnGroup.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
        }
        evt.target.classList.add('active');
    }

    aplicarFiltrosCarga();
}

// ─── Exportar a Excel ─────────────────────────────────────────────────────────
function exportarCargaExcel() {
    if (typeof XLSX === 'undefined') {
        alert('La librería para exportar a Excel no está disponible.');
        return;
    }

    const datosFiltrados = obtenerDatosFiltradosYOrdenados();
    if (datosFiltrados.length === 0) {
        alert('No hay datos para exportar.');
        return;
    }

    // Estructurar los datos
    const dataExcel = [
        ['INCES - REPORTES DE GESTIÓN'],
        [`REPORTE DE CARGA HORARIA POR DOCENTE - AÑO ${anioCarga}`],
        [],
        ['Pos.', 'Cédula', 'Docente', 'Tipo Docente', 'Estado Docente', 'Horas Asignadas', 'Horas Completadas', 'Cursos Totales', 'Avance %', 'Nivel de Carga']
    ];

    datosFiltrados.forEach((item, idx) => {
        const asig = parseFloat(item.horas_asignadas || 0);
        const comp = parseFloat(item.horas_completadas || 0);
        const pct = asig > 0 ? Math.round((comp / asig) * 100) : 0;

        let nivelCarga = 'Baja';
        if (asig >= 120) nivelCarga = 'Sobrecarga';
        else if (asig >= 40) nivelCarga = 'Normal';

        dataExcel.push([
            idx + 1,
            item.cedula || '—',
            `${item.primer_nombre} ${item.primer_apellido}`,
            item.tipo_docente || '—',
            item.estado_docente || '—',
            Math.round(asig),
            Math.round(comp),
            parseInt(item.cursos_activos || 0),
            `${pct}%`,
            nivelCarga
        ]);
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(dataExcel);

    // Ajustar columnas
    ws['!cols'] = [
        { wch: 6 },  // Pos
        { wch: 12 }, // Cédula
        { wch: 30 }, // Docente
        { wch: 15 }, // Tipo Docente
        { wch: 15 }, // Estado Docente
        { wch: 15 }, // Horas Asignadas
        { wch: 15 }, // Horas Completadas
        { wch: 15 }, // Cursos Totales
        { wch: 10 }, // Avance %
        { wch: 15 }  // Nivel de Carga
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Carga Horaria Docentes');
    const fechaStr = new Date().toISOString().slice(0, 10);
    XLSX.writeFile(wb, `Reporte_Carga_Horaria_${anioCarga}_${fechaStr}.xlsx`);
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
    cargaFiltroBusqueda  = '';
    cargaFiltroTipo      = '';
    cargaFiltroEstado    = '';
    cargaCriterioOrden   = 'horas';
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
window.mostrarDetalleCargaPorId     = mostrarDetalleCargaPorId;
window.limpiarFiltrosCarga          = limpiarFiltrosCarga;
window.exportarCargaExcel           = exportarCargaExcel;

console.log('RE-Docente-CargaHoraria.js cargado');

// ─── Exportar a PDF (DOMPDF) ──────────────────────────────────────────────────
async function exportarPdfCargaHoraria() {
    const btn = document.getElementById('btnExportarPdfCarga');
    const btnTop = document.getElementById('btn-print-report');
    const oldHtml = btn ? btn.innerHTML : '';
    const oldHtmlTop = btnTop ? btnTop.innerHTML : '';
    try {
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generando...';
            btn.disabled = true;
        }
        if (btnTop) {
            btnTop.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generando...';
            btnTop.disabled = true;
        }

        // 1. Obtener imagen del canvas
        const canvas = document.getElementById('graficoCargaHoraria');
        let chartBase64 = '';
        if (canvas) {
            chartBase64 = canvas.toDataURL('image/png', 1.0);
        }

        // 2. Obtener datos procesados
        let datosFiltrados = [];
        if (typeof obtenerDatosFiltradosYOrdenados === 'function') {
            datosFiltrados = obtenerDatosFiltradosYOrdenados();
        } else {
            // Fallback: usar cargaHorariaData y aplicar filtros manuales si es necesario
            datosFiltrados = [...cargaHorariaData];
        }
        
        // 3. Preparar el payload
        const payload = {
            anioCarga: anioCarga,
            periodoCarga: periodoCarga,
            periodoValorCarga: periodoValorCarga,
            cargaPanelActivo: cargaPanelActivo,
            cargaFiltroBusqueda: cargaFiltroBusqueda,
            cargaFiltroTipo: cargaFiltroTipo,
            cargaFiltroEstado: cargaFiltroEstado,
            cargaCriterioOrden: cargaCriterioOrden,
            datos: datosFiltrados,
            totales: cargaTotalesActuales,
            graficoBase64: chartBase64
        };

        // 4. Enviar vía POST con JSON + Fetch
        const url = new URL(API_URL_CARGA, window.location.href);
        url.searchParams.set('accion', 'exportar_pdf_carga_horaria');

        const response = await fetch(url.toString(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`Error en el servidor: ${response.status}`);
        }

        // 5. Recibir blob y abrir en nueva pestaña
        const blob = await response.blob();
        if (blob.size === 0) {
            throw new Error("El archivo PDF generado está vacío");
        }
        
        const fileUrl = URL.createObjectURL(blob);
        window.open(fileUrl, '_blank');

    } catch (error) {
        console.error("Error al exportar PDF:", error);
        alert("Ocurrió un error al generar el PDF: " + error.message);
    } finally {
        if (btn) {
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
        if (btnTop) {
            btnTop.innerHTML = oldHtmlTop;
            btnTop.disabled = false;
        }
    }
}
window.exportarPdfCargaHoraria = exportarPdfCargaHoraria;


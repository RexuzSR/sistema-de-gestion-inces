// ========== REPORTE: PARTICIPANTES POR ÁMBITO — Conectado a API ==========
// Datos: api_reportes.php  acciones: participantes_por_ambito, anios_disponibles
// La API agrupa por tb_ambito.nombre_ambito con totales: total, aprobados, en_formacion, retirados

// ─── Constante de ruta API ───────────────────────────────────────────────────
const API_BASE_AMBITO = '../controlador/Reporte/api_reportes.php';

// ─── Estado global ────────────────────────────────────────────────────────────

/** Datos del año seleccionado: [{ ambito, total, aprobados, en_formacion, retirados }] */
let datosAmbito            = [];

/** Datos del año anterior para comparación */
let datosAmbitoAnioAnterior = [];

/** Años disponibles desde la BD */
let añosDispAmbitoCache    = [];

let graficoAmbito          = null;
let ambitoSeleccionado     = null;
let coloresAmbitos         = [];
let sortColumn             = null;
let sortDirection          = 'asc';

// ====================================================================
// ─────────────────── CAPA DE API ────────────────────────────────────
// ====================================================================

async function apiFetchAmbito(accion, params = {}) {
    const url = new URL(API_BASE_AMBITO, window.location.href);
    url.searchParams.set('accion', accion);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));

    const res  = await fetch(url.toString(), { credentials: 'same-origin' });
    const data = await res.json();

    if (!res.ok || data.error) {
        throw new Error(data.error || `HTTP ${res.status}`);
    }
    return data;
}

/** Carga y cachea los años disponibles desde la BD */
async function cargarAniosDisponiblesAmbito() {
    if (añosDispAmbitoCache.length) return añosDispAmbitoCache;
    const data = await apiFetchAmbito('anios_disponibles');
    añosDispAmbitoCache = Array.isArray(data) && data.length
        ? data
        : [new Date().getFullYear()];
    return añosDispAmbitoCache;
}

// ====================================================================
// ─────────────────── HELPERS ────────────────────────────────────────
// ====================================================================

function destruirGraficoAmbito() {
    if (graficoAmbito && typeof graficoAmbito.destroy === 'function') {
        graficoAmbito.destroy();
        graficoAmbito = null;
    }
}

function mostrarLoadingAmbito(mostrar) {
    const loadEl   = document.getElementById('loadingAmbito');
    const contenEl = document.getElementById('contenidoAmbito');
    if (loadEl)   loadEl.style.display   = mostrar ? 'block' : 'none';
    if (contenEl) contenEl.style.display = mostrar ? 'none'  : 'block';
}

function mostrarErrorAmbitoUI(msg) {
    const loadEl = document.getElementById('loadingAmbito');
    if (loadEl) loadEl.innerHTML = `
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
            </svg>
            <div><strong>Error al cargar datos:</strong> ${msg}</div>
        </div>`;
}

// ====================================================================
// ─────────────────── FUNCIÓN PRINCIPAL ──────────────────────────────
// ====================================================================

function cargarParticipantesPorAmbito() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 1;
    const categorias  = window.categoriasReportes || [];
    const categoria   = categorias.find(c => c.id === categoriaId) || { color: 'primary' };

    // Limpiar estado
    destruirGraficoAmbito();
    datosAmbito             = [];
    datosAmbitoAnioAnterior = [];
    añosDispAmbitoCache     = [];
    ambitoSeleccionado      = null;
    sortColumn              = null;
    sortDirection           = 'asc';

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">
                        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h4.5a.5.5 0 0 1 0 1H2z"/>
                    </svg>
                    Participantes por Ámbito
                </h4>
                <p class="mb-0 small opacity-75">Distribución de participantes según ámbito registrado en el sistema</p>
            </div>
            <div class="card-body">

                <!-- Filtros -->
                <div class="row mb-4 align-items-end">
                    <div class="col-md-4">
                        <label for="selectAnioAmbito" class="form-label fw-semibold">Año:</label>
                        <select id="selectAnioAmbito" class="form-select">
                            <!-- Se llenará tras cargar años disponibles -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="selectAmbitoEspecifico" class="form-label fw-semibold">Filtrar por Ámbito:</label>
                        <select id="selectAmbitoEspecifico" class="form-select">
                            <option value="todos">Todos los ámbitos</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltrosAmbito" class="btn btn-${categoria.color} w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/></svg>
                            Aplicar Filtros
                        </button>
                    </div>
                </div>

                <!-- Loading -->
                <div class="text-center py-5" id="loadingAmbito">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
                </div>

                <!-- Contenido -->
                <div id="contenidoAmbito" style="display:none;">

                    <!-- KPIs -->
                    <div id="estadisticasGeneralesAmbito"></div>

                    <!-- Gráfico + Detalle -->
                    <div class="row mt-4" style="min-height:450px;">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title" id="tituloGraficoAmbito">Distribución de Participantes</h5>
                                    <div style="height:350px;"><canvas id="graficoAmbito"></canvas></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title" id="tituloDetalleAmbito">Detalle del Ámbito</h5>
                                    <div id="detalleAmbito" class="text-center py-5">
                                        <p class="text-muted">Selecciona un ámbito en la gráfica para ver detalles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <h5 class="card-title mb-0">Detalle por Ámbito</h5>
                                        <button class="btn btn-outline-secondary btn-sm" id="resetOrdenTabla">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>
                                            Restaurar orden
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="sortable" data-column="ambito" style="cursor:pointer;">
                                                        Ámbito <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="total" style="cursor:pointer;">
                                                        Total <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="aprobados" style="cursor:pointer;">
                                                        Aprobados <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="en_formacion" style="cursor:pointer;">
                                                        En Formación <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="retirados" style="cursor:pointer;">
                                                        Retirados <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="total_anterior" style="cursor:pointer;">
                                                        Total Año Anterior <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable text-end" data-column="diferencia" style="cursor:pointer;">
                                                        Diferencia <span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="text-end">Tasa Aprob.</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaAmbito"></tbody>
                                            <tfoot class="table-light fw-bold">
                                                <tr>
                                                    <td>Totales</td>
                                                    <td id="footTotal"       class="text-end"></td>
                                                    <td id="footAprobados"   class="text-end"></td>
                                                    <td id="footEnFormacion" class="text-end"></td>
                                                    <td id="footRetirados"   class="text-end"></td>
                                                    <td id="footAnterior"    class="text-end"></td>
                                                    <td id="footDiferencia"  class="text-end"></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/></svg>
                                        Haz clic en cualquier encabezado para ordenar · Haz clic en un ámbito de la gráfica para ver detalles
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>`;

    // Lanzar carga asíncrona
    (async () => {
        try {
            const anios = await cargarAniosDisponiblesAmbito();
            const hoy   = new Date().getFullYear();
            const selAnio = document.getElementById('selectAnioAmbito');
            selAnio.innerHTML = anios.map(a =>
                `<option value="${a}" ${a === hoy ? 'selected' : ''}>${a}</option>`
            ).join('');

            await cargarDatosAmbito(hoy);

            // Botón aplicar filtros
            document.getElementById('btnAplicarFiltrosAmbito').addEventListener('click', async () => {
                const anio    = parseInt(document.getElementById('selectAnioAmbito').value);
                const filtro  = document.getElementById('selectAmbitoEspecifico').value;
                await cargarDatosAmbito(anio, filtro);
            });

        } catch (err) {
            console.error('[Ámbito] Error de inicialización:', err);
            mostrarErrorAmbitoUI(err.message);
        }
    })();
}

// ====================================================================
// ─────────────────── CARGA Y PROCESAMIENTO DE DATOS ─────────────────
// ====================================================================

/**
 * Carga participantes por ámbito para el año y año anterior.
 * @param {number} anio - Año principal
 * @param {string} [filtroAmbito='todos'] - Nombre de ámbito a mostrar (o 'todos')
 */
async function cargarDatosAmbito(anio, filtroAmbito = 'todos') {
    mostrarLoadingAmbito(true);
    sortColumn    = null;
    sortDirection = 'asc';

    try {
        const anioAnterior = anio - 1;

        // Cargar año actual y año anterior en paralelo
        const [datosActual, datosAnterior] = await Promise.all([
            apiFetchAmbito('participantes_por_ambito', { anio }),
            apiFetchAmbito('participantes_por_ambito', { anio: anioAnterior }),
        ]);

        // Normalizar números (la API devuelve strings en algunos casos)
        const normalizar = arr => (Array.isArray(arr) ? arr : []).map(r => ({
            ambito:       r.ambito,
            total:        parseInt(r.total)        || 0,
            aprobados:    parseInt(r.aprobados)    || 0,
            en_formacion: parseInt(r.en_formacion) || 0,
            retirados:    parseInt(r.retirados)    || 0,
        }));

        let datosNorm    = normalizar(datosActual);
        let datosAntNorm = normalizar(datosAnterior);

        // Filtrar por ámbito específico si se seleccionó uno
        if (filtroAmbito && filtroAmbito !== 'todos') {
            datosNorm    = datosNorm.filter(d => d.ambito === filtroAmbito);
            datosAntNorm = datosAntNorm.filter(d => d.ambito === filtroAmbito);
        }

        datosAmbito             = datosNorm;
        datosAmbitoAnioAnterior = datosAntNorm;

        // Actualizar selector de ámbito específico con los datos reales
        actualizarSelectorAmbitoEspecifico(normalizar(datosActual), filtroAmbito);

        mostrarLoadingAmbito(false);

        if (!datosNorm.length) {
            mostrarSinDatosAmbito(anio);
            return;
        }

        mostrarEstadisticasAmbito(anio, anioAnterior);
        mostrarTablaAmbito(anio, anioAnterior);
        crearGraficoAmbito(anio);
        configurarOrdenacion();

        document.getElementById('tituloGraficoAmbito').textContent =
            `Distribución de Participantes (${anio})`;

    } catch (err) {
        console.error('[Ámbito] Error cargando datos:', err);
        mostrarErrorAmbitoUI(err.message);
    }
}

/** Rellena el selector de ámbito específico con los ámbitos reales de la BD */
function actualizarSelectorAmbitoEspecifico(todosLosAmbitos, seleccionActual = 'todos') {
    const sel = document.getElementById('selectAmbitoEspecifico');
    if (!sel) return;
    sel.innerHTML = `<option value="todos">Todos los ámbitos</option>` +
        todosLosAmbitos
            .map(d => `<option value="${d.ambito}" ${d.ambito === seleccionActual ? 'selected' : ''}>${d.ambito}</option>`)
            .join('');
}

function mostrarSinDatosAmbito(anio) {
    const contenEl = document.getElementById('contenidoAmbito');
    if (contenEl) {
        contenEl.style.display = 'block';
        contenEl.innerHTML = `
            <div class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16" class="mb-3 opacity-50">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                </svg>
                <h5>Sin datos para ${anio}</h5>
                <p class="small">No se encontraron inscripciones registradas para el año seleccionado.</p>
            </div>`;
    }
}

// ====================================================================
// ─────────────────── KPI CARDS ──────────────────────────────────────
// ====================================================================

function mostrarEstadisticasAmbito(anio, anioAnterior) {
    const el = document.getElementById('estadisticasGeneralesAmbito');
    if (!el) return;

    const totalAmbitos    = datosAmbito.length;
    const totalPart       = datosAmbito.reduce((s, a) => s + a.total, 0);
    const totalAprobados  = datosAmbito.reduce((s, a) => s + a.aprobados, 0);
    const totalFormacion  = datosAmbito.reduce((s, a) => s + a.en_formacion, 0);
    const totalRetirados  = datosAmbito.reduce((s, a) => s + a.retirados, 0);

    const totalAnterior   = datosAmbitoAnioAnterior.reduce((s, a) => s + a.total, 0);
    const diferencia      = totalPart - totalAnterior;
    const tasaGlobal      = totalPart > 0 ? (totalAprobados / totalPart * 100).toFixed(1) : '—';

    const kpis = [
        { valor: totalAmbitos,                  label: 'Ámbitos',         sub: 'ámbitos con datos',              color: 'secondary' },
        { valor: totalPart.toLocaleString(),     label: 'Participantes',   sub: `inscripciones en ${anio}`,       color: 'primary'   },
        { valor: totalAprobados.toLocaleString(), label: 'Aprobados',      sub: 'participantes aprobados',        color: 'success'   },
        { valor: totalFormacion.toLocaleString(), label: 'En Formación',   sub: 'cursando actualmente',           color: 'info'      },
        { valor: totalRetirados.toLocaleString(), label: 'Retirados',      sub: 'ausentes y reprobados',          color: 'danger'    },
        {
            valor: (diferencia >= 0 ? '+' : '') + diferencia.toLocaleString(),
            label: 'Variación Anual',
            sub: `vs ${anioAnterior}`,
            color: diferencia > 0 ? 'success' : diferencia < 0 ? 'danger' : 'secondary',
        },
    ];

    el.innerHTML = `<div class="row g-3 mb-4">
        ${kpis.map(k => `
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h3 class="fw-bold text-${k.color} mb-0">${k.valor}</h3>
                    <p class="fw-semibold small text-uppercase mb-0" style="letter-spacing:.05em;">${k.label}</p>
                    <p class="text-muted mb-0" style="font-size:.72rem;">${k.sub}</p>
                </div>
            </div>
        </div>`).join('')}
    </div>`;
}

// ====================================================================
// ─────────────────── TABLA ──────────────────────────────────────────
// ====================================================================

function mostrarTablaAmbito(anio, anioAnterior) {
    const tbody = document.getElementById('tablaAmbito');
    if (!tbody) return;

    const filas = construirFilasTabla(datosAmbito);
    tbody.innerHTML = filas;
    actualizarTotalesPie();
}

/**
 * Construye filas de tabla a partir de un array de datos de ámbito,
 * cruzando con datos del año anterior para calcular la diferencia.
 */
function construirFilasTabla(datos) {
    return datos.map((ambito, idx) => {
        const ant       = datosAmbitoAnioAnterior.find(a => a.ambito === ambito.ambito);
        const anterior  = ant ? ant.total : 0;
        const dif       = ambito.total - anterior;
        const difColor  = dif > 0 ? 'text-success' : dif < 0 ? 'text-danger' : 'text-muted';
        const difSigno  = dif > 0 ? '+' : '';
        const tasa      = ambito.total > 0 ? (ambito.aprobados / ambito.total * 100).toFixed(1) : '0.0';
        const tasaCls   = parseFloat(tasa) >= 70 ? 'success' : parseFloat(tasa) >= 50 ? 'warning' : 'danger';
        const pal       = coloresAmbitos[idx % coloresAmbitos.length] || '#6c757d';

        return `<tr data-ambito="${ambito.ambito}" style="cursor:pointer;" title="Clic para ver detalle en gráfica"
                    onclick="resaltarAmbitoEnGrafico('${ambito.ambito.replace(/'/g, "\\'")}')">
            <td>
                <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${pal};margin-right:6px;"></span>
                <strong>${ambito.ambito}</strong>
            </td>
            <td class="text-end">${ambito.total.toLocaleString()}</td>
            <td class="text-end text-success fw-semibold">${ambito.aprobados.toLocaleString()}</td>
            <td class="text-end text-info">${ambito.en_formacion.toLocaleString()}</td>
            <td class="text-end text-danger">${ambito.retirados.toLocaleString()}</td>
            <td class="text-end text-muted">${anterior.toLocaleString()}</td>
            <td class="text-end ${difColor} fw-semibold">${difSigno}${dif.toLocaleString()}</td>
            <td class="text-end"><span class="badge bg-${tasaCls}">${tasa}%</span></td>
        </tr>`;
    }).join('') || `<tr><td colspan="8" class="text-center text-muted py-4">Sin datos para este ámbito.</td></tr>`;
}

/** Actualiza los totales en el pie de la tabla */
function actualizarTotalesPie() {
    const totTotal      = datosAmbito.reduce((s, a) => s + a.total, 0);
    const totAprob      = datosAmbito.reduce((s, a) => s + a.aprobados, 0);
    const totForm       = datosAmbito.reduce((s, a) => s + a.en_formacion, 0);
    const totRet        = datosAmbito.reduce((s, a) => s + a.retirados, 0);
    const totAnt        = datosAmbitoAnioAnterior
        .filter(a => datosAmbito.some(d => d.ambito === a.ambito))
        .reduce((s, a) => s + a.total, 0);
    const totDif        = totTotal - totAnt;
    const difSigno      = totDif >= 0 ? '+' : '';
    const difColor      = totDif > 0 ? 'text-success' : totDif < 0 ? 'text-danger' : 'text-muted';

    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('footTotal',       totTotal.toLocaleString());
    set('footAprobados',   totAprob.toLocaleString());
    set('footEnFormacion', totForm.toLocaleString());
    set('footRetirados',   totRet.toLocaleString());
    set('footAnterior',    totAnt.toLocaleString());

    const footDif = document.getElementById('footDiferencia');
    if (footDif) {
        footDif.textContent  = `${difSigno}${totDif.toLocaleString()}`;
        footDif.className    = `text-end fw-bold ${difColor}`;
    }
}

// ====================================================================
// ─────────────────── GRÁFICO ────────────────────────────────────────
// ====================================================================

function crearGraficoAmbito(anio) {
    const ctx = document.getElementById('graficoAmbito')?.getContext('2d');
    if (!ctx) return;
    destruirGraficoAmbito();

    coloresAmbitos = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
        '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
        '#FF595E', '#6A4C93'
    ];

    graficoAmbito = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels:   datosAmbito.map(a => a.ambito),
            datasets: [{
                data:            datosAmbito.map(a => a.total),
                backgroundColor: coloresAmbitos.slice(0, datosAmbito.length),
                borderColor:     '#ffffff',
                borderWidth:     2,
                hoverOffset:     15,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'left', labels: { padding: 16, font: { size: 11 }, usePointStyle: true } },
                tooltip: { callbacks: { label: ctx => {
                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const pct   = total > 0 ? Math.round(ctx.raw / total * 100) : 0;
                    return `${ctx.label}: ${Number(ctx.raw).toLocaleString()} (${pct}%)`;
                }}}
            },
            onClick: (evt, elements) => {
                if (elements.length > 0) mostrarDetalleAmbito(elements[0].index);
            }
        }
    });

    // Mostrar el primer ámbito por defecto
    if (datosAmbito.length > 0) mostrarDetalleAmbito(0);
}

/** Resalta y muestra detalle de un ámbito por nombre (llamado desde click en tabla) */
function resaltarAmbitoEnGrafico(nombreAmbito) {
    const idx = datosAmbito.findIndex(a => a.ambito === nombreAmbito);
    if (idx >= 0) mostrarDetalleAmbito(idx);
}
window.resaltarAmbitoEnGrafico = resaltarAmbitoEnGrafico;

function mostrarDetalleAmbito(index) {
    const ambito = datosAmbito[index];
    if (!ambito) return;
    ambitoSeleccionado = ambito;

    const anio         = parseInt(document.getElementById('selectAnioAmbito')?.value || new Date().getFullYear());
    const anioAnterior = anio - 1;
    const ant          = datosAmbitoAnioAnterior.find(a => a.ambito === ambito.ambito);
    const anterior     = ant ? ant.total : 0;
    const dif          = ambito.total - anterior;
    const pctCambio    = anterior > 0 ? (dif / anterior * 100).toFixed(1) : null;
    const tasa         = ambito.total > 0 ? (ambito.aprobados / ambito.total * 100).toFixed(1) : '0.0';
    const tasaCls      = parseFloat(tasa) >= 70 ? 'success' : parseFloat(tasa) >= 50 ? 'warning' : 'danger';
    const difColor     = dif > 0 ? 'text-success' : dif < 0 ? 'text-danger' : 'text-muted';
    const colorAmbito  = coloresAmbitos[index % coloresAmbitos.length];

    const detalleEl = document.getElementById('detalleAmbito');
    if (!detalleEl) return;

    detalleEl.style.transition = 'background-color .3s ease';
    detalleEl.style.backgroundColor = `${colorAmbito}20`;
    setTimeout(() => { detalleEl.style.backgroundColor = ''; }, 600);

    detalleEl.innerHTML = `
    <div class="text-start">
        <h5 class="fw-bold mb-3 pb-2" style="color:${colorAmbito};border-bottom:2px solid ${colorAmbito};">${ambito.ambito}</h5>

        <!-- Barras de distribución -->
        <div class="mb-4">
            <div class="d-flex justify-content-between small text-muted mb-1"><span>Aprobados</span><span class="fw-bold text-success">${ambito.aprobados.toLocaleString()}</span></div>
            <div class="progress mb-2" style="height:10px;">
                <div class="progress-bar bg-success" style="width:${ambito.total > 0 ? (ambito.aprobados/ambito.total*100).toFixed(1) : 0}%"></div>
            </div>
            <div class="d-flex justify-content-between small text-muted mb-1"><span>En Formación</span><span class="fw-bold text-info">${ambito.en_formacion.toLocaleString()}</span></div>
            <div class="progress mb-2" style="height:10px;">
                <div class="progress-bar bg-info" style="width:${ambito.total > 0 ? (ambito.en_formacion/ambito.total*100).toFixed(1) : 0}%"></div>
            </div>
            <div class="d-flex justify-content-between small text-muted mb-1"><span>Retirados</span><span class="fw-bold text-danger">${ambito.retirados.toLocaleString()}</span></div>
            <div class="progress mb-2" style="height:10px;">
                <div class="progress-bar bg-danger" style="width:${ambito.total > 0 ? (ambito.retirados/ambito.total*100).toFixed(1) : 0}%"></div>
            </div>
        </div>

        <!-- Totales y comparación -->
        <div class="row text-center g-2 mb-3">
            <div class="col-6">
                <div class="card border-0 bg-light h-100"><div class="card-body py-2">
                    <div class="fw-semibold small text-muted">${anio}</div>
                    <div class="fs-4 fw-bold text-primary">${ambito.total.toLocaleString()}</div>
                </div></div>
            </div>
            <div class="col-6">
                <div class="card border-0 bg-light h-100"><div class="card-body py-2">
                    <div class="fw-semibold small text-muted">${anioAnterior}</div>
                    <div class="fs-4 fw-bold text-secondary">${anterior.toLocaleString()}</div>
                </div></div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background:#f8f9fa;">
            <div>
                <div class="small text-muted">Variación anual</div>
                <div class="fs-5 fw-bold ${difColor}">${dif >= 0 ? '+' : ''}${dif.toLocaleString()}</div>
            </div>
            ${pctCambio !== null ? `<span class="badge bg-${dif >= 0 ? 'success' : 'danger'} fs-6">${dif >= 0 ? '+' : ''}${pctCambio}%</span>` : ''}
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <span class="text-muted small">Tasa de aprobación</span>
            <span class="badge bg-${tasaCls} fs-6">${tasa}%</span>
        </div>
    </div>`;

    document.getElementById('tituloDetalleAmbito').textContent = `Detalle: ${ambito.ambito}`;
}

// ====================================================================
// ─────────────────── ORDENACIÓN DE TABLA ────────────────────────────
// ====================================================================

function configurarOrdenacion() {
    document.querySelectorAll('.sortable').forEach(th => {
        // Clonar para evitar listeners duplicados
        const nuevo = th.cloneNode(true);
        th.parentNode.replaceChild(nuevo, th);
        nuevo.addEventListener('click', () => {
            const col = nuevo.dataset.column;
            sortDirection = (sortColumn === col && sortDirection === 'asc') ? 'desc' : 'asc';
            sortColumn    = col;
            actualizarIndicadoresOrden();
            ordenarYRenderizarTabla();
        });
    });

    const btnReset = document.getElementById('resetOrdenTabla');
    if (btnReset) {
        const nuevo = btnReset.cloneNode(true);
        btnReset.parentNode.replaceChild(nuevo, btnReset);
        nuevo.addEventListener('click', () => {
            sortColumn    = null;
            sortDirection = 'asc';
            actualizarIndicadoresOrden();
            const tbody = document.getElementById('tablaAmbito');
            if (tbody) tbody.innerHTML = construirFilasTabla(datosAmbito);
        });
    }
}

function actualizarIndicadoresOrden() {
    document.querySelectorAll('.sort-indicator').forEach(s => { s.innerHTML = ''; });
    if (!sortColumn) return;
    const th = document.querySelector(`.sortable[data-column="${sortColumn}"] .sort-indicator`);
    if (th) th.innerHTML = sortDirection === 'asc'
        ? ' <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/></svg>'
        : ' <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/></svg>';
}

function ordenarYRenderizarTabla() {
    if (!sortColumn) return;

    const copia = [...datosAmbito].map(ambito => {
        const ant      = datosAmbitoAnioAnterior.find(a => a.ambito === ambito.ambito);
        const anterior = ant ? ant.total : 0;
        return { ...ambito, total_anterior: anterior, diferencia: ambito.total - anterior };
    });

    copia.sort((a, b) => {
        let va = a[sortColumn], vb = b[sortColumn];
        if (sortColumn === 'ambito') {
            va = (va || '').toLowerCase();
            vb = (vb || '').toLowerCase();
        } else {
            va = parseFloat(va) || 0;
            vb = parseFloat(vb) || 0;
        }
        if (sortDirection === 'asc') return va > vb ? 1 : va < vb ? -1 : 0;
        return va < vb ? 1 : va > vb ? -1 : 0;
    });

    const tbody = document.getElementById('tablaAmbito');
    if (tbody) tbody.innerHTML = construirFilasTabla(copia);
}

// ====================================================================
// ─────────────────── LIMPIEZA ───────────────────────────────────────
// ====================================================================

function limpiarRecursosAmbito() {
    destruirGraficoAmbito();
    datosAmbito             = [];
    datosAmbitoAnioAnterior = [];
    añosDispAmbitoCache     = [];
    coloresAmbitos          = [];
    ambitoSeleccionado      = null;
    sortColumn              = null;
    sortDirection           = 'asc';
}

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarParticipantesPorAmbito = cargarParticipantesPorAmbito;
window.limpiarRecursosAmbito        = limpiarRecursosAmbito;

console.log('RE-Participante-Ambito.js cargado');
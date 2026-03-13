// ========== REPORTE: CURSOS CON MÁS PARTICIPANTES — conectado a API ==========
// Archivo: RE-Curso-MasParticipantes.js
// API:     ../controlador/Reporte/api_reportes.php

const API_CMP = '../controlador/Reporte/api_reportes.php';

// ── Estado del módulo ─────────────────────────────────────────────────────────
let cmpChart         = null;
let cmpPaginaActual  = 1;
let cmpOrden         = { columna: 'total_participantes', direccion: 'desc' };
const CMP_POR_PAGINA = 10;
let cmpDatosCompletos = [];
let cmpUmbrales = {
    muyActivo: 0, activo: 0, promedio: 0,
    desviacion: 0, k: 1, totalCursos: 0
};

// ── Helper fetch ──────────────────────────────────────────────────────────────
async function cmpFetch(params) {
    const qs  = new URLSearchParams(params).toString();
    const res = await fetch(`${API_CMP}?${qs}`);
    if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
    const data = await res.json();
    if (data && data.error) throw new Error(data.error);
    return data;
}

// ── Clasificación automática por desviación estándar (k = 1) ─────────────────
function cmpClasificar(datos) {
    if (!datos || datos.length === 0) return null;

    const vals  = datos.map(d => parseInt(d.total_participantes) || 0);
    const prom  = vals.reduce((s, v) => s + v, 0) / vals.length;
    const desv  = Math.sqrt(vals.reduce((s, v) => s + Math.pow(v - prom, 2), 0) / vals.length);
    const umbSup = prom + desv;
    const umbInf = Math.max(0, prom - desv);

    const clasificar = n => {
        if (n >= umbSup) return { estado: 'Muy Activo', clase: 'bg-success' };
        if (n >= umbInf) return { estado: 'Activo',     clase: 'bg-warning' };
        return               { estado: 'Normal',        clase: 'bg-info'    };
    };

    const clasificados = datos.map(c => {
        const cl = clasificar(parseInt(c.total_participantes) || 0);
        return { ...c, clasificacion: cl.estado, claseBadge: cl.clase };
    });

    cmpUmbrales = {
        muyActivo:  Math.round(umbSup),
        activo:     Math.round(umbInf),
        promedio:   Math.round(prom),
        desviacion: Math.round(desv),
        k: 1, totalCursos: datos.length
    };

    return {
        clasificados,
        distribucion: {
            muyActivo: clasificados.filter(c => c.clasificacion === 'Muy Activo').length,
            activo:    clasificados.filter(c => c.clasificacion === 'Activo').length,
            normal:    clasificados.filter(c => c.clasificacion === 'Normal').length,
        }
    };
}

// ── Duración calculada a partir de fechas ─────────────────────────────────────
function cmpDuracion(fi, ff) {
    if (!fi || !ff) return 'N/D';
    const dias = Math.round((new Date(ff) - new Date(fi)) / 864e5);
    if (isNaN(dias) || dias < 0) return 'N/D';
    if (dias < 7)  return `${dias} d`;
    if (dias < 30) return `${Math.round(dias / 7)} sem`;
    return `${Math.round(dias / 30)} mes${Math.round(dias / 30) !== 1 ? 'es' : ''}`;
}

// ── Ordenamiento de la tabla ──────────────────────────────────────────────────
function cmpOrdenarDatos(datos, col, dir) {
    return [...datos].sort((a, b) => {
        const num = (va, vb) => dir === 'asc' ? va - vb : vb - va;
        const str = (va, vb) => dir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
        switch (col) {
            case 'nombre_curso':
                return str((a.nombre_curso || '').toLowerCase(), (b.nombre_curso || '').toLowerCase());
            case 'total_participantes':
                return num(parseInt(a.total_participantes) || 0, parseInt(b.total_participantes) || 0);
            case 'estatus':
                return str(a.estatus || '', b.estatus || '');
            case 'modalidad':
                return str(a.modalidad || '', b.modalidad || '');
            case 'clasificacion': {
                const p = { 'Muy Activo': 3, 'Activo': 2, 'Normal': 1 };
                return num(p[a.clasificacion] || 0, p[b.clasificacion] || 0);
            }
            default: return 0;
        }
    });
}

// ============================================================
// ── FUNCIÓN PRINCIPAL ────────────────────────────────────────
// ============================================================
function cargarCursosMasParticipantes() {
    const contentDiv = document.getElementById('reportContent');

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-graph-up-arrow me-2"></i>Cursos con más Participantes
                </h4>
                <p class="mb-0 mt-2 small opacity-75">Ranking de cursos por participación total</p>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="cmpLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
                </div>
                <div id="cmpContenido" style="display:none;"></div>
            </div>
        </div>`;

    cmpInit();
}

// ── Inicialización: obtiene años disponibles y carga el año más reciente ──────
async function cmpInit() {
    try {
        const anios = await cmpFetch({ accion: 'anios_disponibles' });
        const anio  = (Array.isArray(anios) && anios.length) ? anios[0] : new Date().getFullYear();
        await cmpCargar(anio, Array.isArray(anios) ? anios : [anio]);
    } catch (err) {
        cmpMostrarError(`No se pudo inicializar el reporte: ${err.message}`);
    }
}

// ── Carga principal: llama a la API y renderiza ───────────────────────────────
async function cmpCargar(anio, aniosDisp, estatusFiltro = '') {
    const loading   = document.getElementById('cmpLoading');
    const contenido = document.getElementById('cmpContenido');

    if (loading)   { loading.style.display   = 'flex'; loading.style.flexDirection = 'column'; loading.style.alignItems = 'center'; }
    if (contenido) contenido.style.display   = 'none';

    try {
        // Llamadas paralelas: ranking y top más requerido
        const [cursosRaw, topRequerido] = await Promise.all([
            cmpFetch({ accion: 'cursos_mas_participantes', anio, limite: 50 }),
            cmpFetch({ accion: 'curso_mas_requerido',      anio })
        ]);

        if (!Array.isArray(cursosRaw) || cursosRaw.length === 0) {
            cmpMostrarError('No hay datos de cursos para el año seleccionado.');
            return;
        }

        // Aplicar filtro de estatus si se seleccionó
        const datosFiltrados = estatusFiltro
            ? cursosRaw.filter(c => (c.estatus || '') === estatusFiltro)
            : [...cursosRaw];

        if (datosFiltrados.length === 0) {
            cmpMostrarError('No hay cursos con el estatus seleccionado.');
            return;
        }

        cmpDatosCompletos = datosFiltrados;

        cmpRender(datosFiltrados, topRequerido || [], anio, aniosDisp, estatusFiltro, cursosRaw);

        if (loading)   loading.style.display   = 'none';
        if (contenido) contenido.style.display = 'block';

    } catch (err) {
        cmpMostrarError(`Error al cargar los datos: ${err.message}`);
    }
}

// ── Mensaje de error ──────────────────────────────────────────────────────────
function cmpMostrarError(msg) {
    const loading   = document.getElementById('cmpLoading');
    const contenido = document.getElementById('cmpContenido');
    if (loading)   loading.style.display   = 'none';
    if (contenido) {
        contenido.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span>${msg}</span>
            </div>`;
        contenido.style.display = 'block';
    }
}

// ============================================================
// ── RENDER PRINCIPAL ─────────────────────────────────────────
// ============================================================
function cmpRender(datos, topRequerido, anio, aniosDisp, estatusFiltro, datosOriginales) {
    const contenido = document.getElementById('cmpContenido');
    if (!contenido) return;

    const clasif = cmpClasificar(datos);
    if (!clasif) { cmpMostrarError('Datos insuficientes para clasificar.'); return; }

    const { clasificados, distribucion } = clasif;
    const totalPart = clasificados.reduce((s, c) => s + (parseInt(c.total_participantes) || 0), 0);
    const promedio  = Math.round(totalPart / clasificados.length);

    // Opciones de estatus únicas para el filtro
    const estatusOpts = [...new Set(datosOriginales.map(c => c.estatus).filter(Boolean))].sort();

    contenido.innerHTML = `

        <!-- ── Cursos más requeridos (desde API curso_mas_requerido) ─────── -->
        <div class="row mb-4" id="cmpDestacados"></div>

        <!-- ── Tarjetas KPI ───────────────────────────────────────────────── -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h3 class="text-primary fw-bold">${totalPart.toLocaleString()}</h3>
                        <p class="text-muted mb-0 fw-semibold">Total Participantes</p>
                        <small class="text-muted">en cursos del año ${anio}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h3 class="text-success fw-bold">${promedio.toLocaleString()}</h3>
                        <p class="text-muted mb-0 fw-semibold">Promedio por Curso</p>
                        <small class="text-muted">participantes en promedio</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h3 class="text-warning fw-bold">${clasificados.length}</h3>
                        <p class="text-muted mb-0 fw-semibold">Cursos Analizados</p>
                        <small class="text-muted">con participantes inscritos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Filtros ────────────────────────────────────────────────────── -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Año:</label>
                        <select id="cmpSelectAnio" class="form-select">
                            ${(aniosDisp || [anio]).map(a =>
                                `<option value="${a}" ${a == anio ? 'selected' : ''}>${a}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estatus del Curso:</label>
                        <select id="cmpSelectEstatus" class="form-select">
                            <option value="" ${!estatusFiltro ? 'selected' : ''}>Todos los estatus</option>
                            ${estatusOpts.map(e =>
                                `<option value="${e}" ${e === estatusFiltro ? 'selected' : ''}>${e}</option>`
                            ).join('')}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button id="cmpBtnFiltrar" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>

        <!-- ── Gráfico de barras horizontales ─────────────────────────────── -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    Participantes por Curso — ${anio}
                    ${estatusFiltro ? `<span class="badge bg-info ms-2 fs-6">${estatusFiltro}</span>` : ''}
                </h5>
                <p class="text-muted small mb-3">
                    Top 20 cursos · Color según clasificación (
                    <span class="badge bg-success">Muy Activo</span>
                    <span class="badge bg-warning ms-1">Activo</span>
                    <span class="badge bg-info ms-1">Normal</span>)
                </p>
                <div style="height:480px;">
                    <canvas id="cmpCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- ── Tabla ranking con paginación ───────────────────────────────── -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0">Ranking Completo de Cursos</h5>
                    <div class="d-flex gap-1 flex-wrap">
                        <span class="badge bg-secondary">Año: ${anio}</span>
                        ${estatusFiltro
                            ? `<span class="badge bg-info">Estatus: ${estatusFiltro}</span>`
                            : `<span class="badge bg-secondary">Todos los estatus</span>`}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>
                                    <a href="#" class="cmpSort text-decoration-none text-dark" data-col="nombre_curso">
                                        Curso <i class="bi bi-arrow-down-up ms-1"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="#" class="cmpSort text-decoration-none text-dark" data-col="total_participantes">
                                        Participantes <i class="bi bi-arrow-down ms-1"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="#" class="cmpSort text-decoration-none text-dark" data-col="estatus">
                                        Estatus <i class="bi bi-arrow-down-up ms-1"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="#" class="cmpSort text-decoration-none text-dark" data-col="modalidad">
                                        Modalidad <i class="bi bi-arrow-down-up ms-1"></i>
                                    </a>
                                </th>
                                <th>Duración</th>
                                <th>
                                    <a href="#" class="cmpSort text-decoration-none text-dark" data-col="clasificacion">
                                        Estado <i class="bi bi-arrow-down-up ms-1"></i>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="cmpTbody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div class="text-muted small" id="cmpInfoPag"></div>
                    <ul class="pagination pagination-sm mb-0" id="cmpPaginacion"></ul>
                </div>
            </div>
        </div>

        <!-- ── Acordeón: info de clasificación automática ─────────────────── -->
        <div class="accordion mb-3" id="cmpAccordion">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#cmpCollapseClasif">
                        <i class="bi bi-calculator me-2"></i>Información de Clasificación Automática
                    </button>
                </h2>
                <div id="cmpCollapseClasif" class="accordion-collapse collapse"
                     data-bs-parent="#cmpAccordion">
                    <div class="accordion-body">
                        <p class="text-muted small mb-3">
                            Los estados se determinan mediante la desviación estándar (k=1)
                            de los participantes totales. Los umbrales se recalculan con cada
                            cambio de filtro.
                        </p>
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Estado</th>
                                            <th>Criterio</th>
                                            <th>Participantes mínimos</th>
                                            <th>Cursos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-success">Muy Activo</span></td>
                                            <td>≥ μ + σ</td>
                                            <td class="fw-bold">≥ ${cmpUmbrales.muyActivo}</td>
                                            <td><span class="badge bg-success">${distribucion.muyActivo}</span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning">Activo</span></td>
                                            <td>μ − σ ≤ x &lt; μ + σ</td>
                                            <td class="fw-bold">${cmpUmbrales.activo} – ${cmpUmbrales.muyActivo - 1}</td>
                                            <td><span class="badge bg-warning">${distribucion.activo}</span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-info">Normal</span></td>
                                            <td>&lt; μ − σ</td>
                                            <td class="fw-bold">&lt; ${cmpUmbrales.activo}</td>
                                            <td><span class="badge bg-info">${distribucion.normal}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-primary" style="border-width:2px!important;">
                                    <div class="card-body">
                                        <h6 class="text-primary">Estadísticas Base</h6>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Promedio (μ):</span>
                                                <strong>${cmpUmbrales.promedio}</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Desv. estándar (σ):</span>
                                                <strong>${cmpUmbrales.desviacion}</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Factor k:</span>
                                                <strong>${cmpUmbrales.k}</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>Total cursos:</span>
                                                <strong>${cmpUmbrales.totalCursos}</strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Fórmula:</strong> Umbral superior = μ + σ · k | Umbral inferior = μ − σ · k
                            (con k = 1 desviación estándar). Los umbrales se recalculan dinámicamente
                            según los datos filtrados.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .cmpSort { color: inherit !important; }
            .cmpSort:hover { color: #0d6efd !important; }
        </style>
    `;

    // ── Render de secciones ───────────────────────────────────────────────────
    cmpRenderDestacados(topRequerido);
    cmpRenderChart(clasificados);

    cmpPaginaActual = 1;
    cmpOrden        = { columna: 'total_participantes', direccion: 'desc' };
    cmpActualizarTabla(clasificados);

    // ── Listeners ─────────────────────────────────────────────────────────────
    document.getElementById('cmpBtnFiltrar')?.addEventListener('click', () => {
        const nuevoAnio    = parseInt(document.getElementById('cmpSelectAnio').value);
        const nuevoEstatus = document.getElementById('cmpSelectEstatus').value;
        cmpCargar(nuevoAnio, aniosDisp, nuevoEstatus);
    });

    document.querySelectorAll('.cmpSort').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const col = a.dataset.col;
            if (cmpOrden.columna === col)
                cmpOrden.direccion = cmpOrden.direccion === 'asc' ? 'desc' : 'asc';
            else { cmpOrden.columna = col; cmpOrden.direccion = 'asc'; }

            document.querySelectorAll('.cmpSort i').forEach(i => i.className = 'bi bi-arrow-down-up ms-1');
            const ico = a.querySelector('i');
            if (ico) ico.className = `bi bi-arrow-${cmpOrden.direccion === 'asc' ? 'up' : 'down'} ms-1`;

            cmpPaginaActual = 1;
            const reclasif  = cmpClasificar(cmpDatosCompletos);
            cmpActualizarTabla(reclasif?.clasificados || []);
        });
    });
}

// ── Sección "Cursos Más Requeridos" (datos de curso_mas_requerido) ─────────────
function cmpRenderDestacados(topRequerido) {
    const div = document.getElementById('cmpDestacados');
    if (!div || !Array.isArray(topRequerido) || topRequerido.length === 0) return;

    const top4    = topRequerido.slice(0, 4);
    const colores = ['primary', 'success', 'warning', 'info'];

    div.innerHTML = `
        <div class="col-12 mb-2">
            <h5 class="text-muted fw-semibold">
                <i class="bi bi-trophy-fill text-warning me-2"></i>
                Cursos Más Requeridos del Año
            </h5>
        </div>
        ${top4.map((c, i) => `
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100"
                     style="border-top:3px solid var(--bs-${colores[i]},#0d6efd)!important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-${colores[i]} me-2">#${i + 1}</span>
                            <small class="text-muted text-truncate">${c.area || c.estatus || ''}</small>
                        </div>
                        <h6 class="fw-bold" title="${c.nombre_curso}">${c.nombre_curso}</h6>
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <h5 class="mb-0 text-${colores[i]}">
                                    ${parseInt(c.total_inscripciones || 0).toLocaleString()}
                                </h5>
                                <small class="text-muted">Inscripciones</small>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-0 text-${colores[i]}">
                                    ${parseInt(c.participantes_unicos || 0).toLocaleString()}
                                </h5>
                                <small class="text-muted">Únicos</small>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <span class="badge bg-light text-dark">${c.estatus || 'N/D'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('')}
    `;
}

// ── Gráfico de barras horizontales (top 20) ───────────────────────────────────
function cmpRenderChart(clasificados) {
    const ctx = document.getElementById('cmpCanvas')?.getContext('2d');
    if (!ctx) return;
    if (cmpChart) { cmpChart.destroy(); cmpChart = null; }

    const top20 = [...clasificados]
        .sort((a, b) => (parseInt(b.total_participantes) || 0) - (parseInt(a.total_participantes) || 0))
        .slice(0, 20);

    const coloresBg = top20.map(c => {
        switch (c.clasificacion) {
            case 'Muy Activo': return 'rgba(25,135,84,0.85)';
            case 'Activo':     return 'rgba(255,193,7,0.85)';
            default:           return 'rgba(13,202,240,0.85)';
        }
    });

    const maxVal = Math.max(...top20.map(c => parseInt(c.total_participantes) || 0));

    cmpChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top20.map(c => c.nombre_curso),
            datasets: [{
                label: 'Participantes',
                data:  top20.map(c => parseInt(c.total_participantes) || 0),
                backgroundColor: coloresBg,
                borderColor:     coloresBg.map(c => c.replace('0.85', '1')),
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            animation: { duration: 600, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const item = top20[ctx.dataIndex];
                            return [
                                `Participantes: ${ctx.parsed.x.toLocaleString()}`,
                                `Estatus: ${item?.estatus || 'N/D'}`,
                                `Clasificación: ${item?.clasificacion || 'N/D'}`
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: Math.ceil(maxVal * 1.1),
                    title: { display: true, text: 'Cantidad de Participantes', font: { weight: 'bold' } },
                    ticks: { callback: v => v.toLocaleString() }
                },
                y: {
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

// ── Tabla paginada ────────────────────────────────────────────────────────────
function cmpActualizarTabla(clasificados) {
    const ordenados = cmpOrdenarDatos(clasificados, cmpOrden.columna, cmpOrden.direccion);
    const total  = ordenados.length;
    const inicio = (cmpPaginaActual - 1) * CMP_POR_PAGINA;
    const fin    = Math.min(inicio + CMP_POR_PAGINA, total);

    const tbody = document.getElementById('cmpTbody');
    if (tbody) {
        tbody.innerHTML = ordenados.slice(inicio, fin).map((item, idx) => {
            const num = inicio + idx + 1;
            return `
                <tr>
                    <td>
                        <span class="badge ${num <= 3 ? 'bg-warning text-dark' : 'bg-light text-dark'}">
                            ${num}
                        </span>
                    </td>
                    <td>
                        <strong>${item.nombre_curso}</strong>
                        ${parseInt(item.total_participantes) >= cmpUmbrales.muyActivo
                            ? '<i class="bi bi-star-fill text-warning ms-1" title="Curso de alta demanda"></i>'
                            : ''}
                    </td>
                    <td class="fw-bold text-center">
                        ${parseInt(item.total_participantes || 0).toLocaleString()}
                    </td>
                    <td><span class="badge bg-secondary">${item.estatus || 'N/D'}</span></td>
                    <td>${item.modalidad || 'N/D'}</td>
                    <td>${cmpDuracion(item.fecha_inicio, item.fecha_fin)}</td>
                    <td><span class="badge ${item.claseBadge}">${item.clasificacion}</span></td>
                </tr>`;
        }).join('');
    }

    const infoPag = document.getElementById('cmpInfoPag');
    if (infoPag) infoPag.textContent = `Mostrando ${inicio + 1}–${fin} de ${total} cursos`;

    cmpRenderPaginacion(total, ordenados);
}

function cmpRenderPaginacion(total, ordenados) {
    const pag = document.getElementById('cmpPaginacion');
    if (!pag) return;

    const totalPags = Math.ceil(total / CMP_POR_PAGINA);
    if (totalPags <= 1) { pag.innerHTML = ''; return; }

    let html = `
        <li class="page-item ${cmpPaginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-p="${cmpPaginaActual - 1}">«</a>
        </li>`;

    for (let i = 1; i <= totalPags; i++) {
        if (i === 1 || i === totalPags || Math.abs(i - cmpPaginaActual) <= 2) {
            html += `
                <li class="page-item ${i === cmpPaginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" data-p="${i}">${i}</a>
                </li>`;
        } else if (Math.abs(i - cmpPaginaActual) === 3) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }

    html += `
        <li class="page-item ${cmpPaginaActual === totalPags ? 'disabled' : ''}">
            <a class="page-link" href="#" data-p="${cmpPaginaActual + 1}">»</a>
        </li>`;

    pag.innerHTML = html;

    pag.querySelectorAll('.page-link[data-p]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const np = parseInt(a.dataset.p);
            if (np >= 1 && np <= totalPags && np !== cmpPaginaActual) {
                cmpPaginaActual = np;
                const reclasif  = cmpClasificar(cmpDatosCompletos);
                cmpActualizarTabla(reclasif?.clasificados || []);
            }
        });
    });
}

// ── Limpieza de recursos ──────────────────────────────────────────────────────
function limpiarCursosMasParticipantes() {
    if (cmpChart) { cmpChart.destroy(); cmpChart = null; }
    cmpDatosCompletos = [];
    cmpPaginaActual   = 1;
    cmpOrden          = { columna: 'total_participantes', direccion: 'desc' };
    cmpUmbrales       = { muyActivo: 0, activo: 0, promedio: 0, desviacion: 0, k: 1, totalCursos: 0 };
}

// ── Registrar en window ───────────────────────────────────────────────────────
window.cargarCursosMasParticipantes  = cargarCursosMasParticipantes;
window.limpiarCursosMasParticipantes = limpiarCursosMasParticipantes;
console.log('RE-Curso-MasParticipantes.js cargado');
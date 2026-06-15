// ==================== RE-Participante-Total.js ====================
// Reporte: Total de Participantes
// Datos: obtenidos desde api_reportes.php
// ==================================================================

// ── Estado global del reporte ──────────────────────────────────────
let datosMensuales = [];        // Datos del año base activo
let graficoInstance = null;
let cacheDatosAnuales = {};        // Cache: { "2025_Disponible,Asignado": [...], ... }
let anioBase = null;      // Año que controla el gráfico y paneles
let estadosFiltrados = []; // Estados activos del filtro: ['Disponible', 'En formacion']
let datosEstadosSistema = { total: 0, estados: {} }; // Datos globales del sistema

let tiposActivos = new Set(['nuevos']);
let aniosActivos = [];            // Años activos en el gráfico (orden asc)
let tipoGrafico = 'line';        // 'line' | 'bar'
const MAX_ANIOS = 3;

let panelActivo = null;
let añosGlobales = [];            // Años en el panel de detalle (orden asc)

let anioTabla = null;

// Filtros demográficos para la tabla
let filtroDemo = {
    genero: [],   // 'masculino', 'femenino'
    discapacidad: [],   // 'con', 'sin'
    edadUmbral: []    // 'adolescente', 'adulto', 'adultoMayor'
};

// Seguimiento de peticiones en vuelo para evitar condiciones de carrera
let peticionesEnCurso = {};

// ── Colores ────────────────────────────────────────────────────────
const COLOR_BASE = {
    nuevos: [23, 162, 184],
    activos: [40, 167, 69],
    retirados: [220, 53, 69]
};

function colorBordeParaAnio(tipo, idxAnio, totalAnios) {
    const [r, g, b] = COLOR_BASE[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    const h = v => Math.round(v * f).toString(16).padStart(2, '00');
    return `#${h(r)}${h(g)}${h(b)}`;
}

function colorFillParaAnio(tipo, idxAnio, totalAnios, alpha = 0.18) {
    const [r, g, b] = COLOR_BASE[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    return `rgba(${Math.round(r * f)},${Math.round(g * f)},${Math.round(b * f)},${alpha})`;
}

const UMBRALES_EDAD = {
    adolescente: { min: 15, max: 17, label: 'Adolescentes (15-17)' },
    adulto: { min: 18, max: 59, label: 'Adultos (18-59)' },
    adultoMayor: { min: 60, max: 120, label: 'Adultos mayores (60+)' }
};

function obtenerNombresMeses() {
    return ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
}

function destruirGrafico() {
    if (graficoInstance?.destroy) { graficoInstance.destroy(); graficoInstance = null; }
}

function initTooltips(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}

// ── API ────────────────────────────────────────────────────────────
const API_REPORTES_URL = '../controlador/Reporte/api_reportes.php';

async function fetchDatosAnio(anio) {
    const cacheKey = `${anio}_${estadosFiltrados.join(',')}`;
    if (cacheDatosAnuales[cacheKey]) return cacheDatosAnuales[cacheKey];

    if (peticionesEnCurso[cacheKey]) return peticionesEnCurso[cacheKey];

    const estadosParam = estadosFiltrados.length > 0 ? `&estados=${encodeURIComponent(estadosFiltrados.join(','))}` : '';
    const promise = fetch(`${API_REPORTES_URL}?accion=participantes_total&anio=${anio}${estadosParam}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            cacheDatosAnuales[cacheKey] = data;
            delete peticionesEnCurso[cacheKey];
            return data;
        })
        .catch(err => {
            delete peticionesEnCurso[cacheKey];
            throw err;
        });

    peticionesEnCurso[cacheKey] = promise;
    return promise;
}

async function fetchEstadosSistema() {
    const res = await fetch(`${API_REPORTES_URL}?accion=participantes_estados_sistema`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

async function fetchAniosDisponibles() {
    try {
        const res = await fetch(`${API_REPORTES_URL}?accion=anios_disponibles`);
        const data = await res.json();
        return Array.isArray(data) ? data : [new Date().getFullYear()];
    } catch {
        return [new Date().getFullYear()];
    }
}

// ── Helper: clave de caché estructurada ─────────────────────────────
function obtenerClaveCache(anio) {
    return `${anio}_${estadosFiltrados.join(',')}`;
}

// ── Helper: diciembre del año anterior (para gráfico y tabla) ──────
function getDicAnterior(tipo, anio) {
    const datos = cacheDatosAnuales[obtenerClaveCache(anio - 1)];
    if (!datos) return null;
    return datos[11]?.[tipo] ?? null;
}

function getDicAnteriorMesObj(anio) {
    const datos = cacheDatosAnuales[obtenerClaveCache(anio - 1)];
    if (!datos) return null;
    return datos[11] ?? null;
}

// ── Filtros demográficos ───────────────────────────────────────────

function calcularFactorFiltro(mesDatos, campo) {
    if (!mesDatos || !mesDatos.demo) return 1;
    const demoCampo = campo === 'totalAcumulado' ? 'nuevos' : campo;
    const demo = mesDatos.demo[demoCampo] || {};
    const total = mesDatos[campo === 'totalAcumulado' ? 'nuevos' : campo] || 1;
    let factor = 1;

    if (filtroDemo.genero.length > 0) {
        let suma = 0;
        if (filtroDemo.genero.includes('masculino')) suma += demo.masculino || 0;
        if (filtroDemo.genero.includes('femenino')) suma += demo.femenino || 0;
        factor *= (suma / total);
    }
    if (filtroDemo.discapacidad.length > 0) {
        let suma = 0;
        if (filtroDemo.discapacidad.includes('con')) suma += demo.conDisc || 0;
        if (filtroDemo.discapacidad.includes('sin')) suma += demo.sinDisc || 0;
        factor *= (suma / total);
    }
    if (filtroDemo.edadUmbral.length > 0) {
        let suma = 0;
        if (filtroDemo.edadUmbral.includes('adolescente')) suma += demo.adolescente || 0;
        if (filtroDemo.edadUmbral.includes('adulto')) suma += demo.adulto || 0;
        if (filtroDemo.edadUmbral.includes('adultoMayor')) suma += demo.adultoMayor || 0;
        factor *= (suma / total);
    }

    return Math.min(factor, 1);
}

function valorFiltrado(mesDatos, campo) {
    if (!mesDatos || mesDatos[campo] === null || mesDatos[campo] === undefined) return null;
    if (campo === 'totalAcumulado') return mesDatos[campo];
    const factor = calcularFactorFiltro(mesDatos, campo);
    if (factor === 1) return mesDatos[campo];
    return Math.round(mesDatos[campo] * factor);
}

function hayFiltroActivo() {
    return filtroDemo.genero.length > 0 ||
        filtroDemo.discapacidad.length > 0 ||
        filtroDemo.edadUmbral.length > 0 ||
        estadosFiltrados.length > 0;
}

// ── Resumen KPI ────────────────────────────────────────────────────

function calcularResumenFiltrado(datos) {
    const conDatos = datos.filter(m => m.nuevos !== null);
    if (!conDatos.length) return {
        totalNuevos: 0, activos: 0, retirados: 0,
        tasaRetencion: '—', totalAcumulado: 0
    };

    let totalNuevos = 0, totalRetirados = 0;
    const ultimo = conDatos[conDatos.length - 1];
    const activosUltimo = valorFiltrado(ultimo, 'activos') ?? 0;
    const retUltimo = valorFiltrado(ultimo, 'retirados') ?? 0;

    for (const m of conDatos) {
        const n = valorFiltrado(m, 'nuevos');
        if (n !== null) totalNuevos += n;
        const r = valorFiltrado(m, 'retirados');
        if (r !== null) totalRetirados += r;
    }

    const tasa = activosUltimo > 0
        ? ((activosUltimo - retUltimo) / activosUltimo * 100).toFixed(1)
        : '—';

    return {
        totalNuevos, activos: activosUltimo, retirados: totalRetirados,
        tasaRetencion: tasa, totalAcumulado: totalNuevos
    };
}

// ── HTML principal ─────────────────────────────────────────────────

async function cargarTotalParticipantes() {
    const contentDiv = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 1;
    const categorias = window.categoriasReportes || [];
    const categoria = categorias.find(c => c.id === categoriaId) || { color: 'primary' };

    destruirGrafico();
    cacheDatosAnuales = {};
    peticionesEnCurso = {};
    panelActivo = null;
    añosGlobales = [];
    anioBase = null;
    anioTabla = null;
    tiposActivos = new Set(['nuevos']);
    aniosActivos = [];
    tipoGrafico = 'line';
    filtroDemo = { genero: [], discapacidad: [], edadUmbral: [] };
    estadosFiltrados = [];
    datosEstadosSistema = { total: 0, estados: {} };

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${categoria.color} text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">
                    <path fill-rule="evenodd" d="M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z"/>
                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                </svg>Total de Participantes
            </h4>
        </div>
        <div class="card-body">
            <div id="loadingTP" class="text-center py-5">
                <div class="spinner-border text-${categoria.color}" role="status"></div>
                <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
            </div>
            <div id="contenidoTP" style="display:none;"></div>
            <div id="errorTP" style="display:none;"></div>
        </div>
    </div>`;

    try {
        datosEstadosSistema = await fetchEstadosSistema();
        const añosDisp = await fetchAniosDisponibles();
        const hoy = añosDisp[0] ?? new Date().getFullYear();

        anioBase = hoy;
        anioTabla = hoy;
        aniosActivos = [hoy];
        añosGlobales = [hoy];

        await cargarDatosTP(hoy, añosDisp, true);
    } catch (err) {
        mostrarErrorTP(`No se pudieron cargar los datos: ${err.message}`);
    }
}

// ── Carga de datos ─────────────────────────────────────────────────

async function cargarDatosTP(anio, añosDisp, esCargaInicial = false) {
    const loadEl = document.getElementById('loadingTP');
    const contenEl = document.getElementById('contenidoTP');
    const errorEl = document.getElementById('errorTP');

    if (loadEl) loadEl.style.display = 'flex';
    if (contenEl) contenEl.style.display = 'none';
    if (errorEl) errorEl.style.display = 'none';

    try {
        const datos = await fetchDatosAnio(anio);
        datosMensuales = datos;
        anioBase = anio;

        // Pre-cargar diciembre del año anterior (no bloqueante)
        if (!cacheDatosAnuales[obtenerClaveCache(anio - 1)]) {
            try { await fetchDatosAnio(anio - 1); } catch (_) { }
        }

        if (esCargaInicial) {
            renderContenidoTP(añosDisp);
        }

        const resumen = calcularResumenFiltrado(datosMensuales);
        renderKPIs(resumen);
        renderTablaMensual(datosMensuales, getDicAnteriorMesObj(anio));
        sincronizarChipsAnios();
        renderGrafico();
        inicializarControlesGrafico();
        inicializarFiltrosDemo();
        if (panelActivo) renderPanelDetalle(panelActivo);

        if (loadEl) loadEl.style.display = 'none';
        if (contenEl) contenEl.style.display = 'block';

    } catch (err) {
        if (loadEl) loadEl.style.display = 'none';
        mostrarErrorTP(err.message);
    }
}

function mostrarErrorTP(mensaje) {
    const errorEl = document.getElementById('errorTP');
    if (!errorEl) return;
    errorEl.style.display = 'block';
    errorEl.innerHTML = `
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error al cargar los datos:</strong> ${mensaje}
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-danger" onclick="cargarTotalParticipantes()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                </button>
            </div>
        </div>`;
}

// ── Render: estructura HTML del reporte (solo en carga inicial) ────

function renderContenidoTP(añosDisp) {
    const hoy = anioBase;
    const contenEl = document.getElementById('contenidoTP');
    if (!contenEl) return;

    const opcionesAnio = añosDisp.map(a =>
        `<option value="${a}" ${a === hoy ? 'selected' : ''}>${a}</option>`
    ).join('');

    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;
    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;

    contenEl.innerHTML = `
        <div id="kpiCards"></div>
        <div id="panelDetalleWrapper" style="display:none;" class="mb-3"></div>

        <!-- Controles gráfico — estructura unificada con RE-Curso-Total -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-start gap-3">

                    <!-- Dato a representar -->
                    <div style="flex:1 1 auto;min-width:220px;">
                        <div class="rp-toolbar-label mb-2">Dato a representar</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-outline-primary btn-tipo active" data-tipo="nuevos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/></svg>Nuevos
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-tipo" data-tipo="activos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Activos
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-tipo" data-tipo="retirados">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M11 7.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Retirados
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarTipos">${svgX} Limpiar</button>
                        </div>
                    </div>

                    <div class="rp-vdivider d-none d-md-block"></div>

                    <!-- Años a comparar -->
                    <div style="flex:0 1 auto;min-width:200px;">
                        <div class="rp-toolbar-label mb-2">
                            Años a comparar
                            <span id="tpAnioLimiteAviso" class="badge bg-warning text-dark ms-1" style="display:none;font-size:.65rem;">máx. ${MAX_ANIOS}</span>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <select class="form-select form-select-sm" id="selectAnioGrafico" style="width:90px;">
                                ${opcionesAnio}
                            </select>
                            <button class="btn btn-sm btn-outline-secondary" id="btnAddAnioGrafico">${svgPlus} Agregar</button>
                            <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarAnios">${svgX} Limpiar</button>
                        </div>
                        <div id="chipsAnios" class="d-flex gap-1 flex-wrap mt-2"></div>
                    </div>

                    <div class="rp-vdivider d-none d-md-block"></div>

                    <!-- Tipo de gráfica -->
                    <div style="flex:0 0 auto;">
                        <div class="rp-toolbar-label mb-2">Tipo de gráfica</div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-secondary btn-chart-type active" id="btnChartLine" data-tipo="line" title="Líneas">
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
                <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloGrafico"></h6>
                <div id="graficoWrapper" class="rp-chart-wrap rp-chart-wrap--lg"></div>
            </div>
        </div>

        <!-- Tabla mensual -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <h5 class="card-title mb-0">
                            Registro Mensual — <span id="anioSeleccionado">${hoy}</span>
                        </h5>
                        <select id="selectAnioTabla" class="form-select form-select-sm" style="width:110px;">
                            ${opcionesAnio}
                        </select>
                        <small id="subtituloFiltroDemo" class="text-muted" style="display:none;"></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" id="btnToggleFiltrosDemo" data-abierto="0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z"/></svg>Filtrar por perfil
                    </button>
                </div>

                <!-- Panel filtros demográficos -->
                <div id="panelFiltrosDemo" style="display:none;" class="mb-3 p-3 rounded-3 bg-light">
                    <div class="d-flex flex-wrap gap-4 align-items-start">
                        <div class="rp-filter-group-item">
                            <label class="form-label fw-semibold small text-muted text-uppercase d-block">Género</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fg_masculino" name="fGenero" value="masculino" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fg_masculino">Masculino</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fg_femenino" name="fGenero" value="femenino" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fg_femenino">Femenino</label>
                                </div>
                            </div>
                        </div>
                        <div class="rp-filter-group-item">
                            <label class="form-label fw-semibold small text-muted text-uppercase d-block">Discapacidad</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fd_con" name="fDisc" value="con" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fd_con">Con discapacidad</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fd_sin" name="fDisc" value="sin" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fd_sin">Sin discapacidad</label>
                                </div>
                            </div>
                        </div>
                        <div class="rp-filter-group-item rp-filter-group-item--wide">
                            <label class="form-label fw-semibold small text-muted text-uppercase d-block">Rango de edad</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_adolescente" name="fEdad" value="adolescente" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_adolescente">Adolescentes (15-17)</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_adulto" name="fEdad" value="adulto" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_adulto">Adultos (18-59)</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_adultoMayor" name="fEdad" value="adultoMayor" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_adultoMayor">Adultos mayores (60+)</label>
                                </div>
                            </div>
                        </div>
                        <div class="rp-filter-group-item rp-filter-group-item--wide">
                            <label class="form-label fw-semibold small text-muted text-uppercase d-block">Estado actual</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_en_formacion" name="fEstado" value="En formacion" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_en_formacion">En formación</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_disponible" name="fEstado" value="Disponible" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_disponible">Disponible</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_asignado" name="fEstado" value="Asignado" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_asignado">Asignado</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_inactivo" name="fEstado" value="Inactivo" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_inactivo">Inactivo</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_en_sistema" name="fEstado" value="En sistema" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_en_sistema">En sistema</label>
                                </div>
                                <div class="form-check">
                                    <input class="btn-check" type="checkbox" id="fe_diferido" name="fEstado" value="Diferido" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-secondary" for="fe_diferido">Diferido</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <button class="btn btn-sm btn-outline-danger" id="btnLimpiarFiltrosDemo">${svgX} Limpiar filtros</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Nuevos inscritos</th>
                                <th>Activos (cursando)</th>
                                <th>Retirados / Baja</th>
                                <th>Total acumulado</th>
                                <th>
                                    Var. mensual (nuevos)
                                    <span tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Diferencia de nuevos inscritos respecto al mes anterior."
                                        class="rp-tooltip-icon">!</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tablaMensual"></tbody>
                    </table>
                </div>
            </div>
        </div>`;

    initTooltips(contenEl);

    document.getElementById('selectAnioTabla')?.addEventListener('change', async (e) => {
        const nuevoAnio = parseInt(e.target.value);
        if (nuevoAnio > new Date().getFullYear()) return;
        await cambiarAnioTabla(nuevoAnio);
    });
}

// ── Cambiar año de la tabla ────────────────────────────────────────

async function cambiarAnioTabla(anio) {
    if (anio === anioTabla) return;
    anioTabla = anio;
    document.getElementById('anioSeleccionado').textContent = anio;

    try {
        // Pre-cargar diciembre del año anterior (no bloqueante)
        if (!cacheDatosAnuales[obtenerClaveCache(anio - 1)]) {
            try { await fetchDatosAnio(anio - 1); } catch (_) { }
        }

        const datos = await fetchDatosAnio(anio);
        const resumen = calcularResumenFiltrado(datos);
        renderKPIs(resumen);
        renderTablaMensual(datos, getDicAnteriorMesObj(anio));
    } catch (err) {
        mostrarErrorTP(`Error al cambiar de año: ${err.message}`);
    }
}

// ── Filtros demográficos ───────────────────────────────────────────

function inicializarFiltrosDemo() {
    const btnToggle = document.getElementById('btnToggleFiltrosDemo');
    const panel = document.getElementById('panelFiltrosDemo');
    if (!btnToggle || !panel) return;

    btnToggle.onclick = () => {
        const abierto = btnToggle.dataset.abierto === '1';
        panel.style.display = abierto ? 'none' : 'block';
        btnToggle.dataset.abierto = abierto ? '0' : '1';
    };

    async function actualizarFiltros() {
        filtroDemo.genero = [...document.querySelectorAll('input[name="fGenero"]:checked')].map(c => c.value);
        filtroDemo.discapacidad = [...document.querySelectorAll('input[name="fDisc"]:checked')].map(c => c.value);
        filtroDemo.edadUmbral = [...document.querySelectorAll('input[name="fEdad"]:checked')].map(c => c.value);

        const nuevosEstados = [...document.querySelectorAll('input[name="fEstado"]:checked')].map(c => c.value);
        const cambiaronEstados = JSON.stringify(nuevosEstados.sort()) !== JSON.stringify(estadosFiltrados.sort());

        if (cambiaronEstados) {
            estadosFiltrados = nuevosEstados;
            await aplicarFiltrosEstado();
        } else {
            actualizarSubtituloFiltro();
            const cacheKey = `${anioTabla}_${estadosFiltrados.join(',')}`;
            const datos = cacheDatosAnuales[cacheKey] || [];
            renderTablaMensual(datos, getDicAnteriorMesObj(anioTabla));
            renderKPIs(calcularResumenFiltrado(datos));
            if (panelActivo) renderPanelDetalle(panelActivo);
        }
    }

    document.querySelectorAll('input[name="fGenero"],input[name="fDisc"],input[name="fEdad"],input[name="fEstado"]')
        .forEach(cb => cb.addEventListener('change', actualizarFiltros));

    document.getElementById('btnLimpiarFiltrosDemo')?.addEventListener('click', async () => {
        document.querySelectorAll('input[name="fGenero"],input[name="fDisc"],input[name="fEdad"],input[name="fEstado"]')
            .forEach(cb => cb.checked = false);
        filtroDemo = { genero: [], discapacidad: [], edadUmbral: [] };
        if (estadosFiltrados.length > 0) {
            estadosFiltrados = [];
            await aplicarFiltrosEstado();
        } else {
            actualizarSubtituloFiltro();
            const cacheKey = `${anioTabla}_`;
            const datos = cacheDatosAnuales[cacheKey] || [];
            renderTablaMensual(datos, getDicAnteriorMesObj(anioTabla));
            renderKPIs(calcularResumenFiltrado(datos));
            if (panelActivo) renderPanelDetalle(panelActivo);
        }
    });
}

function actualizarSubtituloFiltro() {
    const sub = document.getElementById('subtituloFiltroDemo');
    if (!sub) return;
    if (!hayFiltroActivo()) { sub.style.display = 'none'; return; }

    const nombresEstados = {
        'En formacion': 'En formación',
        'Inactivo': 'Inactivo',
        'Disponible': 'Disponible',
        'Asignado': 'Asignado',
        'En sistema': 'En sistema',
        'Diferido': 'Diferido'
    };

    const partes = [];
    if (filtroDemo.genero.length)
        partes.push('Género: ' + filtroDemo.genero.map(g => g === 'masculino' ? 'Masculino' : 'Femenino').join(' o '));
    if (filtroDemo.discapacidad.length)
        partes.push('Discapacidad: ' + filtroDemo.discapacidad.map(d => d === 'con' ? 'Con' : 'Sin').join(' o '));
    if (filtroDemo.edadUmbral.length)
        partes.push('Edad: ' + filtroDemo.edadUmbral.map(e => UMBRALES_EDAD[e]?.label || e).join(' o '));
    if (estadosFiltrados.length)
        partes.push('Estado: ' + estadosFiltrados.map(e => nombresEstados[e] || e).join(' o '));

    sub.textContent = 'Filtrado por: ' + partes.join(' · ');
    sub.style.display = 'block';
}

// ── KPI Cards ──────────────────────────────────────────────────────

function renderKPIs(data) {
    const el = document.getElementById('kpiCards');
    if (!el) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    let totalSistemaValor = datosEstadosSistema.total;
    let totalSistemaSub = 'participantes registrados';
    if (estadosFiltrados.length > 0) {
        const sumaFiltrada = estadosFiltrados.reduce((acc, est) => acc + (datosEstadosSistema.estados[est] ?? 0), 0);
        totalSistemaValor = `${sumaFiltrada} de ${datosEstadosSistema.total}`;
        totalSistemaSub = 'filtrados en el sistema';
    }

    const cards = [
        { tipo: 'total_sistema', valor: totalSistemaValor, label: 'Total en Sistema', sub: totalSistemaSub, color: 'info' },
        { tipo: 'nuevos', valor: data.totalNuevos, label: 'Nuevos Participantes', sub: 'inscritos en el año', color: 'primary' },
        { tipo: 'activos', valor: data.activos, label: 'Activos Actualmente', sub: 'cursando este mes', color: 'success' },
        { tipo: 'retirados', valor: data.retirados, label: 'Finalizaron / Baja', sub: 'salidas acumuladas en el año', color: 'danger' },
        { tipo: 'retencion', valor: `${data.tasaRetencion}%`, label: 'Tasa de Retención', sub: 'activos que no dieron baja', color: 'warning' }
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md">
            <div class="card border-0 shadow-sm mb-3 kpi-card" role="button" data-tipo="${c.tipo}">
                <div class="card-body text-center py-3">
                    <h2 class="fw-bold text-${c.color} mt-2 mb-0" style="font-size: 1.8rem; white-space: nowrap;">
                        ${typeof c.valor === 'number' ? c.valor.toLocaleString() : c.valor}
                    </h2>
                    <p class="rp-kpi-card-label">${c.label}</p>
                    <p class="rp-kpi-card-sub">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanel-${c.tipo}">
                        ${svgDown}Ver detalle
                    </span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card').forEach(card =>
        card.addEventListener('click', () => togglePanelDetalle(card.dataset.tipo))
    );
}

// ── Panel de detalle ───────────────────────────────────────────────

function togglePanelDetalle(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapper');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total_sistema', 'nuevos', 'activos', 'retirados', 'retencion'].forEach(t => {
        const b = document.getElementById(`badgePanel-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    if (panelActivo === tipo) {
        wrapper.style.display = 'none';
        panelActivo = null;
        return;
    }

    panelActivo = tipo;
    renderPanelDetalle(tipo);
    wrapper.style.display = 'block';
    const badge = document.getElementById(`badgePanel-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar detalle`;
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' }), 50);
}

const CFG_PANEL = {
    nuevos: { titulo: 'Nuevos Inscritos por Mes', campo: 'nuevos', color: 'primary' },
    activos: { titulo: 'Participantes Activos por Mes', campo: 'activos', color: 'success' },
    retirados: { titulo: 'Finalizaciones y Bajas por Mes', campo: 'retirados', color: 'danger' },
    retencion: { titulo: 'Tasa de Retención Mensual (%)', campo: 'retencion', color: 'warning' }
};

function renderPanelDetalle(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapper');
    if (!wrapper) return;

    if (tipo === 'total_sistema') {
        const estados = datosEstadosSistema.estados || {};
        const nombresEstados = {
            'En formacion': 'En formación',
            'Disponible': 'Disponible',
            'Asignado': 'Asignado',
            'Inactivo': 'Inactivo',
            'En sistema': 'En sistema',
            'Diferido': 'Diferido'
        };
        const coloresEstados = {
            'En formacion': 'success',
            'Disponible': 'primary',
            'Asignado': 'warning',
            'Inactivo': 'danger',
            'En sistema': 'info',
            'Diferido': 'dark'
        };

        const htmlEstados = Object.keys(nombresEstados).map(est => {
            const cant = estados[est] ?? 0;
            const activo = estadosFiltrados.includes(est);
            const color = coloresEstados[est];
            
            // Colores e intensidades específicas para los estados activos (pastel suave y bordes nítidos)
            const estilosActivo = {
                'En formacion': 'background-color: rgba(40, 167, 69, 0.12); border-color: #28a745 !important; color: #1e7e34;',
                'Disponible': 'background-color: rgba(0, 123, 255, 0.12); border-color: #007bff !important; color: #0056b3;',
                'Asignado': 'background-color: rgba(255, 193, 7, 0.15); border-color: #ffc107 !important; color: #856404;',
                'Inactivo': 'background-color: rgba(220, 53, 69, 0.12); border-color: #dc3545 !important; color: #bd2130;',
                'En sistema': 'background-color: rgba(23, 162, 184, 0.12); border-color: #17a2b8 !important; color: #117a8b;',
                'Diferido': 'background-color: rgba(108, 117, 125, 0.15); border-color: #6c757d !important; color: #343a40;'
            };
            
            const estiloCSS = activo 
                ? estilosActivo[est] 
                : 'background-color: #ffffff; border-color: #dee2e6; color: #212529;';

            const badgeClase = activo
                ? `bg-${color} text-white`
                : 'border border-secondary-subtle text-secondary bg-light';

            const badgeTexto = activo
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-check-lg me-1" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/></svg>Filtrando'
                : 'Filtrar';

            return `
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card p-3 text-center btn-filtro-estado" 
                         data-estado="${est}" 
                         style="cursor: pointer; transition: all 0.2s ease-in-out; border-width: 2px; ${estiloCSS}"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <h3 class="fw-bold mb-1">${cant}</h3>
                        <div class="small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">${nombresEstados[est]}</div>
                        <div class="mt-2 text-center">
                            <span class="badge rounded-pill ${badgeClase}" style="font-size: 0.65rem; padding: 4px 10px;">
                                ${badgeTexto}
                            </span>
                        </div>
                    </div>
                </div>`;
        }).join('');

        wrapper.innerHTML = `
            <div class="card border-0 shadow-sm rp-card-top" data-color="info">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h6 class="mb-0 text-info">Distribución por Estado Actual en el Sistema</h6>
                        <div>
                            ${estadosFiltrados.length > 0 ? `
                                <button class="btn btn-sm btn-outline-danger" id="btnLimpiarEstadosDetalle">
                                    Quitar todos los filtros de estado
                                </button>
                            ` : '<span class="text-muted small">Haz clic en un estado para aplicar el filtro</span>'}
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        ${htmlEstados}
                    </div>
                    <p class="text-muted small mb-0 mt-2 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        Al filtrar por uno o más estados, se actualizarán los datos demográficos, mensuales y las gráficas del año correspondiente.
                    </p>
                </div>
            </div>`;

        // Añadir manejadores
        wrapper.querySelectorAll('.btn-filtro-estado').forEach(btn => {
            btn.addEventListener('click', async () => {
                const est = btn.dataset.estado;
                const idx = estadosFiltrados.indexOf(est);
                if (idx > -1) {
                    estadosFiltrados.splice(idx, 1);
                } else {
                    estadosFiltrados.push(est);
                }
                // Recargar datos con los estados filtrados
                await aplicarFiltrosEstado();
            });
        });

        document.getElementById('btnLimpiarEstadosDetalle')?.addEventListener('click', async () => {
            estadosFiltrados = [];
            await aplicarFiltrosEstado();
        });
        return;
    }

    const cfg = CFG_PANEL[tipo];
    const meses = obtenerNombresMeses();
    const años = [...añosGlobales].sort((a, b) => a - b);

    const calcValor = (a, i) => {
        const d = (cacheDatosAnuales[obtenerClaveCache(a)] || [])[i];
        if (!d || d.nuevos === null) return null;
        if (tipo === 'retencion') {
            const act = valorFiltrado(d, 'activos');
            const ret = valorFiltrado(d, 'retirados');
            if (act === null || act === 0) return null;
            return ((act - ret) / act * 100).toFixed(1) + '%';
        }
        return valorFiltrado(d, cfg.campo);
    };

    const totalesPorAño = new Array(años.length).fill(0);
    const filas = meses.map((mes, i) => {
        let row = `<tr><td><strong>${mes}</strong></td>`;
        años.forEach((a, idx) => {
            const v = calcValor(a, i);
            if (v !== null && typeof v === 'number') totalesPorAño[idx] += v;
            row += `<td class="text-center">${v ?? '<span class="text-muted">—</span>'}</td>`;
        });
        return row + '</tr>';
    }).join('');

    const filaTotal = `<tr class="rp-tfoot-total"><td>Total</td>${años.map((_, idx) => `<td class="text-center">${totalesPorAño[idx] > 0 ? totalesPorAño[idx].toLocaleString() : '—'}</td>`).join('')
        }</tr>`;

    const indicadorFiltro = hayFiltroActivo()
        ? `<span class="badge bg-warning text-dark ms-2 small">Filtro demográfico activo</span>` : '';
    const svgXS = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;
    const svgPl = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;

    const añosDisponiblesEnCache = [...new Set(Object.keys(cacheDatosAnuales).map(k => parseInt(k.split('_')[0])).filter(a => !isNaN(a)))];
    const añosFaltantes = añosDisponiblesEnCache.filter(a => !años.includes(a));

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm rp-card-top" data-color="${cfg.color}">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 text-${cfg.color}">${cfg.titulo}${indicadorFiltro}</h6>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        ${añosFaltantes.length > 0 ? `
                            <select class="form-select form-select-sm" id="selAddAnioPanel" style="width:auto;">
                                ${añosFaltantes.map(a => `<option value="${a}">${a}</option>`).join('')}
                            </select>
                            <button class="btn btn-sm btn-outline-${cfg.color}" id="btnAddAnioPanel">${svgPl}Agregar año</button>
                        ` : `<span class="text-muted small">Todos los años cargados añadidos</span>`}
                    </div>
                </div>
                ${años.length === 0 ? `<p class="text-muted text-center py-4">No hay años cargados. Agrega un año desde el gráfico.</p>` : `
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0 rp-table-fixed">
                        <colgroup><col style="width:110px;">${años.map(() => `<col style="width:95px;">`).join('')}</colgroup>
                        <thead class="table-light">
                            <tr>
                                <th>Mes</th>
                                ${años.map(a => `
                                <th class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <span>${a}</span>
                                        <button class="btn-remove-anio-panel rp-btn-remove-anio" data-anio="${a}" title="Quitar ${a}">
                                            ${svgXS}
                                        </button>
                                    </div>
                                </th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>${filas}</tbody>
                        <tfoot>${filaTotal}</tfoot>
                    </table>
                </div>`}
            </div>
        </div>`;

    document.getElementById('btnAddAnioPanel')?.addEventListener('click', async () => {
        const sel = document.getElementById('selAddAnioPanel');
        if (!sel) return;
        const a = parseInt(sel.value);
        if (añosGlobales.includes(a)) return;
        try {
            await fetchDatosAnio(a);
            añosGlobales.push(a);
            añosGlobales.sort((x, y) => x - y);
            renderPanelDetalle(panelActivo);
        } catch (err) {
            console.error('Error al agregar año al panel:', err);
        }
    });

    wrapper.querySelectorAll('.btn-remove-anio-panel').forEach(btn =>
        btn.addEventListener('click', () => {
            añosGlobales = añosGlobales.filter(x => x !== parseInt(btn.dataset.anio));
            renderPanelDetalle(panelActivo);
        })
    );
}

// ── Tabla mensual (con fila de diciembre año anterior) ─────────────

function renderTablaMensual(datos, dicAnterior = null) {
    const tbody = document.getElementById('tablaMensual');
    if (!tbody) return;

    const svgUp = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/></svg>`;
    const svgDw = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/></svg>`;
    const svgDs = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/></svg>`;

    let totalN = 0, totalA = 0, totalR = 0, acum = 0;

    // ── Fila de diciembre del año anterior ──────────────────────────
    let filaDicAnterior = '';
    if (dicAnterior && dicAnterior.nuevos !== null) {
        const anioAnterior = (anioTabla || anioBase) - 1;
        const vN = valorFiltrado(dicAnterior, 'nuevos');
        const vA = valorFiltrado(dicAnterior, 'activos');
        const vR = valorFiltrado(dicAnterior, 'retirados');
        const fmt = v => v !== null ? v.toLocaleString() : '—';
        filaDicAnterior = `
            <tr class="table-light text-muted" style="font-style:italic;opacity:.75;">
                <td><strong>Dic ${anioAnterior}</strong> <span class="badge bg-secondary bg-opacity-25 text-secondary ms-1" style="font-size:.65rem;">ref.</span></td>
                <td>${fmt(vN)}</td>
                <td>${fmt(vA)}</td>
                <td>${fmt(vR)}</td>
                <td>—</td>
                <td><span class="text-muted small">Mes de referencia</span></td>
            </tr>`;
    }

    // ── Filas del año activo ────────────────────────────────────────
    const filas = datos.map((mes, idx) => {
        const sd = mes.nuevos === null;
        const vNuevos = sd ? null : valorFiltrado(mes, 'nuevos');
        const vActivos = sd ? null : valorFiltrado(mes, 'activos');
        const vRet = sd ? null : valorFiltrado(mes, 'retirados');
        if (!sd && vNuevos !== null) acum += vNuevos;

        if (vNuevos !== null) totalN += vNuevos;
        if (vActivos !== null) totalA += vActivos;
        if (vRet !== null) totalR += vRet;

        let crec = '<span class="text-muted">—</span>';
        if (!sd && vNuevos !== null) {
            // Referencia anterior: mes previo dentro del año, o diciembre del año anterior si es enero
            let vAnt = null;
            if (idx === 0) {
                // Enero: comparar con diciembre del año anterior
                vAnt = dicAnterior ? valorFiltrado(dicAnterior, 'nuevos') : null;
                if (vAnt === null) {
                    crec = '<span class="text-muted">Primer mes</span>';
                }
            } else {
                const prevMes = datos.slice(0, idx).reverse().find(m => m.nuevos !== null);
                if (prevMes) vAnt = valorFiltrado(prevMes, 'nuevos');
            }

            if (vAnt !== null) {
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
            }
        }

        const fmt = v => v !== null ? v.toLocaleString() : '—';
        return `<tr>
            <td><strong>${mes.mes}</strong></td>
            <td>${fmt(vNuevos)}</td>
            <td>${fmt(vActivos)}</td>
            <td>${fmt(vRet)}</td>
            <td>${sd ? '—' : acum.toLocaleString()}</td>
            <td>${sd ? '<span class="text-muted">—</span>' : crec}</td>
        </tr>`;
    }).join('');

    tbody.innerHTML = filaDicAnterior + filas + `
        <tr class="rp-row-total-tp">
            <td>Total</td>
            <td>${totalN.toLocaleString()}</td>
            <td>${totalA.toLocaleString()}</td>
            <td>${totalR.toLocaleString()}</td>
            <td>${acum.toLocaleString()}</td>
            <td>—</td>
        </tr>`;
}

// ── Gráfico ────────────────────────────────────────────────────────

function getLabelTipo(t) {
    return { nuevos: 'Nuevos', activos: 'Activos', retirados: 'Retirados' }[t] || t;
}

function renderGrafico() {
    const wrapper = document.getElementById('graficoWrapper');
    if (!wrapper) return;

    if (!tiposActivos.size || !aniosActivos.length) {
        destruirGrafico();
        wrapper.innerHTML = `<div class="rp-chart-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor" viewBox="0 0 16 16"><path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1z"/></svg>
            <p class="rp-chart-empty-text">Seleccione al menos una métrica y un año</p>
        </div>`;
        document.getElementById('tituloGrafico').textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) wrapper.innerHTML = '<canvas id="canvasGrafico"></canvas>';
    destruirGrafico();

    const ctx = wrapper.querySelector('canvas').getContext('2d');
    const tipos = ['nuevos', 'activos', 'retirados'].filter(t => tiposActivos.has(t));
    const años = [...aniosActivos].sort((a, b) => a - b);
    const nAnios = años.length;
    const nTipos = tipos.length;
    const unoAnio = nAnios === 1;
    const unoTipo = nTipos === 1;
    const esBar = tipoGrafico === 'bar';

    // Mostrar diciembre anterior solo en línea con un único tipo
    const usarDic = unoTipo && !esBar;

    const labels = usarDic
        ? [`Dic ${Math.max(...años) - 1}`, ...obtenerNombresMeses()]
        : obtenerNombresMeses();

    const datasets = [];

    tipos.forEach((tipo, tipoIdx) => {
        años.forEach((anio, anioIdx) => {
            const datosAnio = cacheDatosAnuales[obtenerClaveCache(anio)] || [];
            const colorBorde = colorBordeParaAnio(tipo, anioIdx, nAnios);
            const colorFill = colorFillParaAnio(tipo, anioIdx, nAnios, esBar ? 0.75 : (unoTipo && unoAnio ? 0.22 : 0.15));

            const datosBase = datosAnio.map(m => m[tipo] ?? null);
            const datos = usarDic
                ? [getDicAnterior(tipo, anio), ...datosBase]
                : datosBase;

            let label = getLabelTipo(tipo);
            if (nAnios > 1) label += ` ${anio}`;

            let fillConf = false;
            if (!esBar) {
                if (unoTipo) {
                    fillConf = anioIdx === nAnios - 1
                        ? { target: 'origin', above: colorFill }
                        : { target: `+${nTipos}`, above: colorFill };
                } else if (unoAnio) {
                    const posOrden = tipos.indexOf(tipo);
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

    document.getElementById('tituloGrafico').textContent =
        `${tipos.map(getLabelTipo).join(', ')} — ${años.join(', ')}`;

    graficoInstance = new Chart(ctx, {
        type: tipoGrafico,
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
                    grid: { color: 'rgba(0,0,0,0.07)' },
                    ticks: {
                        color: c => (usarDic && c.index === 0) ? 'rgba(140,140,140,0.55)' : undefined,
                        font: c => (usarDic && c.index === 0) ? { style: 'italic' } : {}
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.07)' },
                    title: { display: true, text: 'Número de Participantes', color: '#64748b', font: { size: 11, weight: 600 } }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

// ── Controles del gráfico ──────────────────────────────────────────

function inicializarControlesGrafico() {
    // Botones de métrica
    document.querySelectorAll('.btn-tipo').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-tipo').forEach(btn => {
        btn.classList.toggle('active', tiposActivos.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const tipo = btn.dataset.tipo;
            tiposActivos.has(tipo) ? tiposActivos.delete(tipo) : tiposActivos.add(tipo);
            btn.classList.toggle('active', tiposActivos.has(tipo));
            renderGrafico();
        });
    });

    const replaceListener = (id, fn) => {
        const el = document.getElementById(id);
        if (!el) return;
        const clon = el.cloneNode(true);
        el.parentNode.replaceChild(clon, el);
        document.getElementById(id).addEventListener('click', fn);
    };

    replaceListener('btnLimpiarTipos', () => {
        tiposActivos.clear();
        document.querySelectorAll('.btn-tipo').forEach(b => b.classList.remove('active'));
        renderGrafico();
    });

    // Botones tipo de gráfica
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tipo === tipoGrafico);
        if (btn.classList.contains('active')) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        } else {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-outline-secondary');
        }
        btn.addEventListener('click', () => {
            tipoGrafico = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type').forEach(b => {
                const isActive = b.dataset.tipo === tipoGrafico;
                b.classList.toggle('active', isActive);
                if (isActive) {
                    b.classList.remove('btn-outline-secondary');
                    b.classList.add('btn-secondary');
                } else {
                    b.classList.remove('btn-secondary');
                    b.classList.add('btn-outline-secondary');
                }
            });
            renderGrafico();
        });
    });

    // Agregar año al gráfico
    replaceListener('btnAddAnioGrafico', async () => {
        const sel = document.getElementById('selectAnioGrafico');
        if (!sel) return;
        const a = parseInt(sel.value);
        if (a > new Date().getFullYear() || aniosActivos.includes(a)) return;
        if (aniosActivos.length >= MAX_ANIOS) {
            const aviso = document.getElementById('tpAnioLimiteAviso');
            if (aviso) {
                aviso.style.display = 'inline-block';
                setTimeout(() => { aviso.style.display = 'none'; }, 2500);
            }
            return;
        }
        try {
            await fetchDatosAnio(a);
            // Pre-cargar diciembre del año anterior para el punto de referencia
            if (!cacheDatosAnuales[obtenerClaveCache(a - 1)]) {
                try { await fetchDatosAnio(a - 1); } catch (_) { }
            }
            aniosActivos.push(a);
            aniosActivos.sort((x, y) => x - y);
            if (!añosGlobales.includes(a)) {
                añosGlobales.push(a);
                añosGlobales.sort((x, y) => x - y);
            }
            sincronizarChipsAnios();
            renderGrafico();
        } catch (err) {
            console.error('Error al agregar año al gráfico:', err);
        }
    });

    replaceListener('btnLimpiarAnios', () => {
        aniosActivos = [];
        sincronizarChipsAnios();
        renderGrafico();
    });

    sincronizarChipsAnios();
}

function sincronizarChipsAnios() {
    const chips = document.getElementById('chipsAnios');
    if (!chips) return;
    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>`;

    chips.innerHTML = aniosActivos.map(a =>
        `<span class="rp-year-chip">
            ${a}
            <button class="btn-rm-anio rp-year-chip-remove" data-anio="${a}" title="Quitar ${a}">${svgX}</button>
        </span>`
    ).join('');

    chips.querySelectorAll('.btn-rm-anio').forEach(btn =>
        btn.addEventListener('click', () => {
            aniosActivos = aniosActivos.filter(x => x !== parseInt(btn.dataset.anio));
            sincronizarChipsAnios();
            renderGrafico();
        })
    );
}

async function aplicarFiltrosEstado() {
    const anio = anioTabla || anioBase;
    try {
        // Recargar datos para el año de la tabla
        const datos = await fetchDatosAnio(anio);
        datosMensuales = datos;

        // Pre-cargar/recargar los años comparativos del gráfico
        for (const a of aniosActivos) {
            await fetchDatosAnio(a);
        }

        // Sincronizar checkboxes demográficos del panel
        sincronizarCheckboxesEstados();
        actualizarSubtituloFiltro();

        // Re-renderizar KPIs, tabla, gráfico y panel de detalle
        const resumen = calcularResumenFiltrado(datosMensuales);
        renderKPIs(resumen);
        renderTablaMensual(datosMensuales, getDicAnteriorMesObj(anio));
        renderGrafico();
        if (panelActivo) renderPanelDetalle(panelActivo);
    } catch (err) {
        mostrarErrorTP(`Error al aplicar filtros de estado: ${err.message}`);
    }
}

function sincronizarCheckboxesEstados() {
    document.querySelectorAll('input[name="fEstado"]').forEach(cb => {
        cb.checked = estadosFiltrados.includes(cb.value);
    });
}

// ── Limpieza ───────────────────────────────────────────────────────

function limpiarRecursosTotalParticipantes() {
    destruirGrafico();
    datosMensuales = [];
    cacheDatosAnuales = {};
    peticionesEnCurso = {};
    panelActivo = null;
    añosGlobales = [];
    aniosActivos = [];
    tiposActivos = new Set(['nuevos']);
    tipoGrafico = 'line';
    anioBase = null;
    anioTabla = null;
    filtroDemo = { genero: [], discapacidad: [], edadUmbral: [] };
    estadosFiltrados = [];
    datosEstadosSistema = { total: 0, estados: {} };
}

// ── Exports globales ───────────────────────────────────────────────
window.cargarTotalParticipantes = cargarTotalParticipantes;
window.limpiarRecursosTotalParticipantes = limpiarRecursosTotalParticipantes;

console.log('RE-Participante-Total.js cargado (BD real — sin datos aleatorios)');
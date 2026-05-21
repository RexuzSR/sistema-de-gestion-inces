// ========== REPORTE: METAS POR AMBITO ==========
// Archivo: reportes-metas.js
// Funcion: cargarMetaPorAmbito
// Conectado a: ../controlador/Reporte/api_reportes.php

const API_URL_METAS = '../controlador/Reporte/api_reportes.php';

let metasMensualesData   = [];
let graficoMetasInstance = null;
let cacheMetasAnuales    = {};
let anioMetasBase        = null;

let tiposActivosMetas  = new Set(['cumplimiento']);
let aniosActivosMetas  = [];
let tipoGraficoMetas   = 'line';
const MAX_ANIOS_METAS   = 3;

let panelActivoMetas   = null;
let anioTablaMetas     = null;

const COLOR_BASE_METAS = {
    cumplimiento: [34, 197, 94],
    noCumplidas:  [239, 68, 68],
    cobertura:    [59, 130, 246]
};

const METAS_COLORES_AMBITO = [
    { fill:'#3b82f6', light:'#eff6ff', border:'#bfdbfe', text:'#1d4ed8' },
    { fill:'#22c55e', light:'#f0fdf4', border:'#bbf7d0', text:'#15803d' },
    { fill:'#f59e0b', light:'#fffbeb', border:'#fde68a', text:'#b45309' },
    { fill:'#ef4444', light:'#fef2f2', border:'#fecaca', text:'#b91c1c' },
    { fill:'#8b5cf6', light:'#f5f3ff', border:'#ddd6fe', text:'#6d28d9' },
    { fill:'#0891b2', light:'#ecfeff', border:'#a5f3fc', text:'#0e7490' },
    { fill:'#ec4899', light:'#fdf2f8', border:'#fbcfe8', text:'#be185d' },
    { fill:'#10b981', light:'#ecfdf5', border:'#a7f3d0', text:'#047857' },
];

function colorBordeMetas(tipo, idxAnio, totalAnios) {
    const [r, g, b] = COLOR_BASE_METAS[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    const h = v => Math.round(v * f).toString(16).padStart(2, '0');
    return `#${h(r)}${h(g)}${h(b)}`;
}

function colorFillMetas(tipo, idxAnio, totalAnios, alpha = 0.18) {
    const [r, g, b] = COLOR_BASE_METAS[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    return `rgba(${Math.round(r*f)},${Math.round(g*f)},${Math.round(b*f)},${alpha})`;
}

function obtenerNombresMesesMetas() {
    return ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
}

function obtenerAniosDisponiblesMetas() {
    const hoy = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => hoy - i);
}

function destruirGraficoMetas() {
    if (graficoMetasInstance?.destroy) {
        graficoMetasInstance.destroy();
        graficoMetasInstance = null;
    }
}

function initTooltipsMetas(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}

async function fetchDatosAnualMetas(anio) {
    if (cacheMetasAnuales[anio]) return cacheMetasAnuales[anio];

    const res = await fetch(`${API_URL_METAS}?accion=metas_por_ambito&anio=${anio}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    cacheMetasAnuales[anio] = data;
    return data;
}

function generarDatosAnualMetas(anio) {
    return cacheMetasAnuales[anio] || [];
}

function procesarMetas(rawList, anio) {
    const ambitoIdx = {};
    let ci = 0;
    
    return rawList.map(m => {
        const obj  = parseInt(m.meta_objetivo)       || 0;
        const form = parseInt(m.estudiantes_formados) || 0;
        const pct  = obj > 0 ? parseFloat(((form / obj) * 100).toFixed(2)) : 0;
        const amb  = m.nombre_ambito || 'Sin ambito';
        if (!(amb in ambitoIdx)) ambitoIdx[amb] = ci++;
        const col  = METAS_COLORES_AMBITO[ambitoIdx[amb] % METAS_COLORES_AMBITO.length];

        let periodo = m.periodo_tiempo || '';
        if (!periodo && m.fecha_inicio && m.fecha_fin) {
            const a = new Date(m.fecha_inicio), b = new Date(m.fecha_fin);
            periodo = `${a.toLocaleDateString('es-ES')} – ${b.toLocaleDateString('es-ES')}`;
        }
        if (!periodo) periodo = '—';

        const yi = m.fecha_inicio ? new Date(m.fecha_inicio).getFullYear() : null;
        const yf = m.fecha_fin    ? new Date(m.fecha_fin).getFullYear()    : null;
        let vigencia = '—';
        if (yi && yf && yi !== yf) vigencia = `${yi} – ${yf}`;
        else if (yi) vigencia = `${yi}`;
        else if (yf) vigencia = `${yf}`;

        const mesInicio = m.fecha_inicio ? new Date(m.fecha_inicio).getMonth() : null;
        
        return { 
            id: m.id_meta || Math.random(), 
            nombre_ambito: amb, 
            tipo_objetivo: m.tipo_objetivo || 'Aprobados',
            meta_objetivo: obj, 
            estudiantes_formados: form, 
            pct, 
            estado: m.estado || 'Activa',
            periodo_tiempo: periodo, 
            vigencia, 
            color: col, 
            fecha_inicio: m.fecha_inicio, 
            fecha_fin: m.fecha_fin,
            mes: mesInicio,
            anio: yi || anio
        };
    });
}

function calcularCumplimientoMensual(metas, anio) {
    const meses = Array(12).fill().map(() => ({ objetivo: 0, formados: 0, pct: 0 }));
    
    metas.forEach(meta => {
        if (meta.anio === anio && meta.mes !== null) {
            meses[meta.mes].objetivo += meta.meta_objetivo;
            meses[meta.mes].formados += meta.estudiantes_formados;
        }
    });
    
    meses.forEach((mes, idx) => {
        if (mes.objetivo > 0) {
            mes.pct = (mes.formados / mes.objetivo) * 100;
        }
        mes.mes = obtenerNombresMesesMetas()[idx];
    });
    
    return meses;
}

async function cargarMetaPorAmbito() {
    console.log('Iniciando cargarMetaPorAmbito...');
    
    // Implementar tabs si están disponibles (desde reportes.js)
    if (typeof window.crearTabs === 'function') {
        window.crearTabs(4, 11);
    }
    
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 4;
    const categorias  = window.categoriasReportes || [];
    const categoria   = categorias.find(c => c.id === categoriaId) || { color: 'danger' };
    const hoy         = new Date().getFullYear();
    const aniosDisp    = obtenerAniosDisponiblesMetas();

    destruirGraficoMetas();
    cacheMetasAnuales     = {};
    panelActivoMetas      = null;
    anioMetasBase         = hoy;
    anioTablaMetas        = hoy;
    tiposActivosMetas     = new Set(['cumplimiento']);
    aniosActivosMetas     = [hoy];
    tipoGraficoMetas      = 'line';

    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgX    = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${categoria.color} text-white">
            <h4 class="mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2 mb-1">
                    <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                </svg>Metas Totales
            </h4>
        </div>
        <div class="card-body">

            <div id="loadingMetas" class="text-center py-5">
                <div class="spinner-border text-${categoria.color}" role="status"></div>
                <p class="mt-3 text-muted" id="loadingMsgMetas">Cargando datos de metas...</p>
            </div>

            <div id="errorMetas" class="alert alert-danger" style="display:none;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="errorMsgMetas">Error al cargar los datos.</span>
            </div>

            <div id="contenidoMetas" style="display:none;">

                <div id="kpiCardsMetas"></div>
                <div id="panelDetalleWrapperMetas" style="display:none;" class="mb-3"></div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-start gap-3">

                            <div style="flex:1 1 auto;min-width:220px;">
                                <div class="rp-toolbar-label mb-2">Metrica a representar</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-success btn-tipo-meta active" data-tipo="cumplimiento">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>Cumplimiento
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-tipo-meta" data-tipo="noCumplidas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>No Cumplidas
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary btn-tipo-meta" data-tipo="cobertura">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="me-1 mb-1"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>Cobertura
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarTiposMetas">${svgX} Limpiar</button>
                                </div>
                            </div>

                            <div class="rp-vdivider d-none d-md-block"></div>

                            <div style="flex:0 1 auto;min-width:200px;">
                                <div class="rp-toolbar-label mb-2">
                                    Anios a comparar
                                    <span id="metasAnioLimiteAviso" class="badge bg-warning text-dark ms-1" style="display:none;font-size:.65rem;">max. ${MAX_ANIOS_METAS}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <select class="form-select form-select-sm" id="selectAnioMetasGrafico" style="width:90px;">
                                        ${aniosDisp.map(a => `<option value="${a}">${a}</option>`).join('')}
                                    </select>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnAddAnioMetasGrafico">${svgPlus} Agregar</button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarAniosMetas">Limpiar</button>
                                </div>
                                <div id="chipsAniosMetas" class="d-flex gap-1 flex-wrap mt-2"></div>
                            </div>

                            <div class="rp-vdivider d-none d-md-block"></div>

                            <div style="flex:0 0 auto;">
                                <div class="rp-toolbar-label mb-2">Tipo de grafica</div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type active" id="btnChartLineMetas" data-tipo="line" title="Lineas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/></svg> Lineas
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type" id="btnChartBarMetas" data-tipo="bar" title="Torres">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg> Torres
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloGraficoMetas"></h6>
                        <div id="graficoWrapperMetas" class="rp-chart-wrap rp-chart-wrap--lg"></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h5 class="card-title mb-0">
                                    Detalle de Metas — <span id="anioSeleccionadoMetas">${hoy}</span>
                                </h5>
                                <select id="selectAnioTablaMetas" class="form-select form-select-sm" style="width:110px;">
                                    ${aniosDisp.map(a => `<option value="${a}" ${a===hoy?'selected':''}>${a}</option>`).join('')}
                                </select>
                            </div>
                        </div>
                        <div id="tablaMetasContainer"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>`;

    initTooltipsMetas(contentDiv);
    await cargarDatosMetas(hoy, true);

    document.getElementById('selectAnioTablaMetas')?.addEventListener('change', async (e) => {
        const nuevoAnio = parseInt(e.target.value);
        if (nuevoAnio > new Date().getFullYear()) return;
        await cambiarAnioTablaMetas(nuevoAnio);
    });
}

async function cargarDatosMetas(anio, esCargaInicial = false) {
    const loadEl   = document.getElementById('loadingMetas');
    const errorEl  = document.getElementById('errorMetas');
    const contenEl = document.getElementById('contenidoMetas');

    if (loadEl)   loadEl.style.display   = 'block';
    if (errorEl)  errorEl.style.display  = 'none';
    if (contenEl) contenEl.style.display = 'none';

    const msgEl = document.getElementById('loadingMsgMetas');
    if (msgEl) msgEl.textContent = `Cargando datos de metas del año ${anio}...`;

    const estadoAntes = esCargaInicial ? null : {
        tiposActivos: new Set(tiposActivosMetas),
        aniosActivos: [...aniosActivosMetas]
    };

    try {
        const rawData = await fetchDatosAnualMetas(anio);
        const metasProcesadas = procesarMetas(rawData, anio);
        const datosMensuales = calcularCumplimientoMensual(metasProcesadas, anio);
        
        metasMensualesData = datosMensuales;
        anioMetasBase      = anio;
        anioTablaMetas     = anio;

        const resumen = buildResumenMetas(metasProcesadas);

        if (loadEl)   loadEl.style.display   = 'none';
        if (contenEl) contenEl.style.display = 'block';

        document.getElementById('anioSeleccionadoMetas').textContent = anio;
        const selectTabla = document.getElementById('selectAnioTablaMetas');
        if (selectTabla) selectTabla.value = anio;

        if (esCargaInicial) {
            aniosActivosMetas = [anio];
        } else {
            tiposActivosMetas = estadoAntes.tiposActivos;
            aniosActivosMetas = estadoAntes.aniosActivos;
            if (!aniosActivosMetas.includes(anio)) {
                aniosActivosMetas.push(anio);
                aniosActivosMetas.sort((a, b) => b - a);
            }
        }

        renderKPIsMetas(resumen);
        renderTablaMetas(metasProcesadas);
        sincronizarChipsAniosMetas();
        renderGraficoMetas();
        inicializarControlesGraficoMetas();
        if (panelActivoMetas) await renderPanelDetalleMetas(panelActivoMetas);

    } catch (err) {
        if (loadEl)  loadEl.style.display = 'none';
        if (errorEl) {
            errorEl.style.display = 'block';
            document.getElementById('errorMsgMetas').textContent =
                `Error al cargar los datos del año ${anio}: ${err.message}`;
        }
        console.error('[RE-Metas] Error al cargar datos:', err);
    }
}

function buildResumenMetas(metas) {
    const total = metas.length;
    const cumplidas = metas.filter(m => m.pct >= 100).length;
    const noCumplidas = total - cumplidas;
    const promedio = total ? metas.reduce((s, m) => s + m.pct, 0) / total : 0;
    const totObj = metas.reduce((s, m) => s + m.meta_objetivo, 0);
    const totForm = metas.reduce((s, m) => s + m.estudiantes_formados, 0);
    const brecha = totObj - totForm;
    
    return { total, cumplidas, noCumplidas, promedio, totObj, totForm, brecha };
}

async function cambiarAnioTablaMetas(anio) {
    if (anio === anioTablaMetas) return;

    const loadEl  = document.getElementById('loadingMetas');
    const errorEl = document.getElementById('errorMetas');
    if (loadEl)  loadEl.style.display  = 'block';
    if (errorEl) errorEl.style.display = 'none';
    const msgEl = document.getElementById('loadingMsgMetas');
    if (msgEl) msgEl.textContent = `Cargando datos del año ${anio}...`;

    try {
        const rawData = await fetchDatosAnualMetas(anio);
        const metasProcesadas = procesarMetas(rawData, anio);
        anioTablaMetas = anio;
        document.getElementById('anioSeleccionadoMetas').textContent = anio;
        renderKPIsMetas(buildResumenMetas(metasProcesadas));
        renderTablaMetas(metasProcesadas);
    } catch (err) {
        if (errorEl) {
            errorEl.style.display = 'block';
            document.getElementById('errorMsgMetas').textContent = `Error al cargar ${anio}: ${err.message}`;
        }
    } finally {
        if (loadEl) loadEl.style.display = 'none';
    }
}

function renderKPIsMetas(data) {
    const el = document.getElementById('kpiCardsMetas');
    if (!el) return;

    const svgMeta   = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgCheck  = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>`;
    const svgGraph  = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>`;
    const svgPeople = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>`;
    const svgDown    = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const cards = [
        { tipo:'total',       valor: data.total,              label:'Metas Registradas',     sub:'en el año',                 color:'primary', svg: svgMeta   },
        { tipo:'cumplidas',   valor: data.cumplidas,          label:'Metas Cumplidas',       sub:`${data.total>0?((data.cumplidas/data.total)*100).toFixed(1):0}% del total`, color:'success', svg: svgCheck  },
        { tipo:'noCumplidas', valor: data.noCumplidas,        label:'Metas No Cumplidas',    sub:`${data.total>0?((data.noCumplidas/data.total)*100).toFixed(1):0}% del total`, color:'danger',  svg: svgPeople },
        { tipo:'promedio',    valor: `${data.promedio.toFixed(1)}%`, label:'Promedio de Cumplimiento', sub:'general de metas',   color:'warning', svg: svgGraph  }
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 kpi-card kpi-card-meta" role="button" data-tipo="${c.tipo}">
                <div class="card-body text-center py-3">
                    <span class="text-${c.color}">${c.svg}</span>
                    <h2 class="fw-bold text-${c.color} mt-2 mb-0">
                        ${typeof c.valor === 'number' ? c.valor.toLocaleString() : c.valor}
                    </h2>
                    <p class="rp-kpi-card-label">${c.label}</p>
                    <p class="rp-kpi-card-sub">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelMeta-${c.tipo}">
                        ${svgDown}Ver detalles
                    </span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-meta').forEach(card =>
        card.addEventListener('click', () => togglePanelDetalleMetas(card.dataset.tipo))
    );
}

async function togglePanelDetalleMetas(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperMetas');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total','cumplidas','noCumplidas','promedio'].forEach(t => {
        const b = document.getElementById(`badgePanelMeta-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalles`;
    });

    if (panelActivoMetas === tipo) {
        wrapper.style.display = 'none';
        panelActivoMetas = null;
        return;
    }

    panelActivoMetas = tipo;
    wrapper.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary"></div><p class="mt-2 text-muted">Cargando detalles...</p></div>`;
    wrapper.style.display = 'block';

    await renderPanelDetalleMetas(tipo);

    const badge = document.getElementById(`badgePanelMeta-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior:'smooth', block:'center' }), 50);
}

async function renderPanelDetalleMetas(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperMetas');
    if (!wrapper) return;

    const anio = anioTablaMetas || new Date().getFullYear();
    
    try {
        const rawData = await fetchDatosAnualMetas(anio);
        const metas = procesarMetas(rawData, anio);
        
        let listaMostrar = [];
        let titulo = '';
        let color = '';
        
        if (tipo === 'total') {
            listaMostrar = metas;
            titulo = 'Todas las Metas';
            color = 'primary';
        } else if (tipo === 'cumplidas') {
            listaMostrar = metas.filter(m => m.pct >= 100);
            titulo = 'Metas Cumplidas';
            color = 'success';
        } else if (tipo === 'noCumplidas') {
            listaMostrar = metas.filter(m => m.pct < 100);
            titulo = 'Metas No Cumplidas';
            color = 'danger';
        } else if (tipo === 'promedio') {
            listaMostrar = [...metas].sort((a,b) => b.pct - a.pct);
            titulo = 'Metas por Promedio de Cumplimiento';
            color = 'warning';
        }

        if (!listaMostrar.length) {
            wrapper.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No se encontraron metas para "${titulo}" en el año ${anio}.
                </div>
            </div>`;
            return;
        }

        const filas = listaMostrar.slice(0, 20).map(m => {
            const barColor = m.pct >= 100 ? '#22c55e' : m.pct >= 75 ? '#0891b2' : m.pct >= 40 ? '#f59e0b' : '#ef4444';
            return `
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:.6rem .8rem;vertical-align:middle;">
                    <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.22em .7em;border-radius:6px;font-weight:600;background:${m.color.light};color:${m.color.text};border:1px solid ${m.color.border};font-size:.72rem;">
                        ${m.nombre_ambito}
                    </span>
                 </td>
                <td style="padding:.6rem .8rem;vertical-align:middle;">${m.tipo_objetivo}</td>
                <td style="padding:.6rem .8rem;vertical-align:middle;text-align:right;font-weight:600;">${m.meta_objetivo.toLocaleString()}</td>
                <td style="padding:.6rem .8rem;vertical-align:middle;text-align:right;font-weight:600;">${m.estudiantes_formados.toLocaleString()}</td>
                <td style="padding:.6rem .8rem;vertical-align:middle;min-width:160px;">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;width:${Math.min(m.pct,100)}%;background:${barColor};border-radius:99px;"></div>
                        </div>
                        <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${barColor};">${m.pct.toFixed(1)}%</span>
                    </div>
                 </td>
                <td style="padding:.6rem .8rem;vertical-align:middle;">${m.estado}</td>
             </tr>`;
        }).join('');

        wrapper.innerHTML = `
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 text-${color} d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>
                        ${titulo}
                        <span class="badge bg-${color} bg-opacity-10 text-${color} ms-1">${listaMostrar.length} metas</span>
                    </h6>
                    <small class="text-muted">Año ${anio}</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Ambito</th><th>Tipo</th><th class="text-end">Objetivo</th><th class="text-end">Formados</th><th>Progreso</th><th>Estado</th></tr>
                        </thead>
                        <tbody>${filas}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
    } catch (err) {
        wrapper.innerHTML = `<div class="alert alert-warning">Error al cargar detalles: ${err.message}</div>`;
    }
}

function renderTablaMetas(metas) {
    const container = document.getElementById('tablaMetasContainer');
    if (!container) return;

    if (!metas.length) {
        container.innerHTML = `<div class="text-center py-5 text-muted">No hay metas registradas para este año.</div>`;
        return;
    }

    const filas = metas.map(m => {
        const barColor = m.pct >= 100 ? '#22c55e' : m.pct >= 75 ? '#0891b2' : m.pct >= 40 ? '#f59e0b' : '#ef4444';
        return `
        <tr>
            <td style="padding:.6rem .8rem;">
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.22em .7em;border-radius:6px;font-weight:600;background:${m.color.light};color:${m.color.text};border:1px solid ${m.color.border};font-size:.72rem;">
                    ${m.nombre_ambito}
                </span>
              </td>
            <td style="padding:.6rem .8rem;">${m.tipo_objetivo}</td>
            <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${m.meta_objetivo.toLocaleString()}</td>
            <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${m.estudiantes_formados.toLocaleString()}</td>
            <td style="padding:.6rem .8rem;min-width:180px;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:${Math.min(m.pct,100)}%;background:${barColor};border-radius:99px;"></div>
                    </div>
                    <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${barColor};">${m.pct.toFixed(1)}%</span>
                </div>
              </td>
            <td style="padding:.6rem .8rem;">
                <span class="badge ${m.estado === 'Activa' ? 'bg-success' : 'bg-secondary'}">${m.estado}</span>
              </td>
            <td style="padding:.6rem .8rem;font-size:.75rem;">${m.periodo_tiempo}</td>
        </tr>`;
    }).join('');

    container.innerHTML = `
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle">
            <thead class="table-light">
                <tr><th>Ambito</th><th>Tipo</th><th class="text-end">Objetivo</th><th class="text-end">Formados</th><th>Progreso</th><th>Estado</th><th>Periodo</th></tr>
            </thead>
            <tbody>${filas}</tbody>
        </table>
    </div>`;
}

function getLabelTipoMeta(t) {
    return { cumplimiento:'Cumplimiento (%)', noCumplidas:'No Cumplidas (%)', cobertura:'Cobertura (%)' }[t] || t;
}

function aniosOrdenadosMetas() {
    return [...aniosActivosMetas].sort((a, b) => b - a);
}

function renderGraficoMetas() {
    const wrapper = document.getElementById('graficoWrapperMetas');
    if (!wrapper) return;

    if (!tiposActivosMetas.size || !aniosActivosMetas.length) {
        destruirGraficoMetas();
        wrapper.innerHTML = `
            <div class="rp-chart-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1z"/>
                </svg>
                <p class="rp-chart-empty-text">Seleccione al menos una metrica y un año para visualizar</p>
            </div>`;
        document.getElementById('tituloGraficoMetas').textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) {
        wrapper.innerHTML = '<canvas id="canvasGraficoMetas"></canvas>';
    }

    destruirGraficoMetas();
    const ctx     = wrapper.querySelector('canvas').getContext('2d');
    const tipos   = ['cumplimiento', 'noCumplidas', 'cobertura'].filter(t => tiposActivosMetas.has(t));
    const años    = aniosOrdenadosMetas();
    const nAnios  = años.length;
    const nTipos  = tipos.length;
    const esBar   = tipoGraficoMetas === 'bar';
    const labels  = obtenerNombresMesesMetas();

    const datasets = [];

    tipos.forEach((tipo, tipoIdx) => {
        años.forEach((anio, anioIdx) => {
            const datosMensuales = calcularCumplimientoMensual([], anio);
            let datos = [];
            
            if (tipo === 'cumplimiento') {
                datos = datosMensuales.map(m => m.pct);
            } else if (tipo === 'noCumplidas') {
                datos = datosMensuales.map(m => 100 - m.pct);
            } else if (tipo === 'cobertura') {
                datos = datosMensuales.map(m => m.pct);
            }
            
            const colorBorde = colorBordeMetas(tipo, anioIdx, nAnios);
            const colorFill  = colorFillMetas(tipo, anioIdx, nAnios, 0.25);
            let label = getLabelTipoMeta(tipo);
            if (nAnios > 1) label += ` ${anio}`;

            let fillConf = false;
            if (!esBar) {
                if (nTipos === 1 && nAnios > 1) {
                    if (anioIdx === nAnios - 1) {
                        fillConf = { target: 'origin', above: colorFill };
                    } else {
                        fillConf = { target: `+${nTipos}`, above: colorFill };
                    }
                } else if (nAnios === 1 && nTipos > 1) {
                    const datosMeses = datos;
                    const colorLinea = colorBorde;
                    fillConf = false;
                    
                    if (tipoIdx === nTipos - 1) {
                        fillConf = { target: 'origin', above: colorLinea };
                    } else {
                        fillConf = { target: `+1`, above: colorLinea };
                    }
                }
            }

            datasets.push({
                label,
                data: datos,
                borderColor: colorBorde,
                backgroundColor: colorFill,
                borderWidth: esBar ? 0 : 2.5,
                fill: fillConf,
                tension: esBar ? 0 : 0.4,
                borderRadius: esBar ? 4 : 0,
                pointBackgroundColor: colorBorde,
                pointRadius: 4,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                spanGaps: false
            });
        });
    });

    let tituloTexto = '';
    tipos.forEach((tipo, i) => {
        tituloTexto += getLabelTipoMeta(tipo);
        if (i < tipos.length - 1) tituloTexto += ', ';
    });
    tituloTexto += ` — ${años.join(', ')}`;
    document.getElementById('tituloGraficoMetas').textContent = tituloTexto;

    graficoMetasInstance = new Chart(ctx, {
        type: tipoGraficoMetas,
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
                        label: c => `${c.dataset.label}: ${c.parsed.y !== null ? c.parsed.y.toFixed(1) : 'Sin dato'}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.07)' },
                    title: { display: true, text: 'Porcentaje (%)' }
                },
                x: {
                    grid: { color: 'rgba(0,0,0,0.07)' }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

function inicializarControlesGraficoMetas() {
    document.querySelectorAll('.btn-tipo-meta').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-tipo-meta').forEach(btn => {
        btn.classList.toggle('active', tiposActivosMetas.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const tipo = btn.dataset.tipo;
            tiposActivosMetas.has(tipo) ? tiposActivosMetas.delete(tipo) : tiposActivosMetas.add(tipo);
            btn.classList.toggle('active', tiposActivosMetas.has(tipo));
            renderGraficoMetas();
        });
    });

    const btnLT = document.getElementById('btnLimpiarTiposMetas');
    if (btnLT) {
        const clon = btnLT.cloneNode(true);
        btnLT.parentNode.replaceChild(clon, btnLT);
        document.getElementById('btnLimpiarTiposMetas').addEventListener('click', () => {
            tiposActivosMetas.clear();
            document.querySelectorAll('.btn-tipo-meta').forEach(b => b.classList.remove('active'));
            renderGraficoMetas();
        });
    }

    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-chart-type').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tipo === tipoGraficoMetas);
        if (btn.classList.contains('active')) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        }
        btn.addEventListener('click', () => {
            tipoGraficoMetas = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type').forEach(b => {
                b.classList.toggle('active', b.dataset.tipo === tipoGraficoMetas);
                if (b.classList.contains('active')) {
                    b.classList.remove('btn-outline-secondary');
                    b.classList.add('btn-secondary');
                } else {
                    b.classList.remove('btn-secondary');
                    b.classList.add('btn-outline-secondary');
                }
            });
            renderGraficoMetas();
        });
    });

    const btnAddA = document.getElementById('btnAddAnioMetasGrafico');
    if (btnAddA) {
        const clon = btnAddA.cloneNode(true);
        btnAddA.parentNode.replaceChild(clon, btnAddA);
        document.getElementById('btnAddAnioMetasGrafico').addEventListener('click', async () => {
            const sel = document.getElementById('selectAnioMetasGrafico');
            if (!sel) return;
            const a = parseInt(sel.value);
            if (a > new Date().getFullYear() || aniosActivosMetas.includes(a)) return;
            if (aniosActivosMetas.length >= MAX_ANIOS_METAS) {
                const aviso = document.getElementById('metasAnioLimiteAviso');
                if (aviso) {
                    aviso.style.display = 'inline-block';
                    setTimeout(() => { aviso.style.display = 'none'; }, 2500);
                }
                return;
            }
            try {
                await fetchDatosAnualMetas(a);
                aniosActivosMetas.push(a);
                aniosActivosMetas.sort((x, y) => y - x);
                sincronizarChipsAniosMetas();
                renderGraficoMetas();
            } catch (err) {
                console.warn(`[RE-Metas] No se pudo cargar año ${a}:`, err);
            }
        });
    }

    const btnLA = document.getElementById('btnLimpiarAniosMetas');
    if (btnLA) {
        const clon = btnLA.cloneNode(true);
        btnLA.parentNode.replaceChild(clon, btnLA);
        document.getElementById('btnLimpiarAniosMetas').addEventListener('click', () => {
            aniosActivosMetas = [];
            sincronizarChipsAniosMetas();
            renderGraficoMetas();
        });
    }

    sincronizarChipsAniosMetas();
}

function sincronizarChipsAniosMetas() {
    const chips = document.getElementById('chipsAniosMetas');
    if (!chips) return;

    chips.innerHTML = aniosActivosMetas.map(a =>
        `<span class="rp-year-chip" data-anio="${a}" style="cursor:pointer;">
            ${a}
        </span>`
    ).join('');

    chips.querySelectorAll('.rp-year-chip').forEach(chip =>
        chip.addEventListener('click', () => {
            const anio = parseInt(chip.dataset.anio);
            aniosActivosMetas = aniosActivosMetas.filter(x => x !== anio);
            sincronizarChipsAniosMetas();
            renderGraficoMetas();
        })
    );

    const sel = document.getElementById('selectAnioMetasGrafico');
    if (sel) {
        const prev = parseInt(sel.value);
        sel.innerHTML = obtenerAniosDisponiblesMetas().map(a =>
            `<option value="${a}" ${a===prev?'selected':''}>${a}</option>`
        ).join('');
    }
}

function limpiarRecursosMetas() {
    destruirGraficoMetas();
    metasMensualesData = [];
    cacheMetasAnuales = {};
    panelActivoMetas = null;
    aniosActivosMetas = [];
    tiposActivosMetas = new Set(['cumplimiento']);
    tipoGraficoMetas = 'line';
    anioMetasBase = null;
    anioTablaMetas = null;
}

window.cargarMetaPorAmbito      = cargarMetaPorAmbito;
window.limpiarRecursosMetas     = limpiarRecursosMetas;
window.iniciarReporteMetas      = cargarMetaPorAmbito;

console.log('reportes-metas.js cargado');
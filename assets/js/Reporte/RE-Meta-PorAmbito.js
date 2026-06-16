// ============================================================
// RE-Meta-PorAmbito.js
// Reporte : Metas por Ámbito
// Función : cargarMetaPorAmbito
// API     : api_reportes.php?accion=metas_por_ambito&anio=XXXX
//           api_reportes.php?accion=detalle_participantes_meta&id_meta=X
// ============================================================

const API_URL_METAS = '../controlador/Reporte/api_reportes.php';

// ── Estado global del reporte ──────────────────────────────
let _metasCache          = {};      // { anio: rawData[] }
let _metasProcesadas     = [];      // datos del año activo en tabla
let _metasGraficoAnios   = [];      // años seleccionados para el gráfico
let _metasGraficoTipos   = new Set(['cumplimiento']);
let _metasTipoGrafico    = 'bar';
let _metasAnioBase       = null;
let _metasAnioTabla      = null;
let _metasChartInstance  = null;
let _metasPanelActivo    = null;
const _METAS_MAX_ANIOS   = 3;

// ── Paleta de colores por ámbito ──────────────────────────
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

// ── Color base por métrica para el gráfico ────────────────
const METAS_COLOR_BASE = {
    cumplimiento: [34,  197,  94],
    noCumplidas:  [239,  68,  68],
    cobertura:    [ 59, 130, 246],
};

function _metasColorBorde(tipo, idxAnio, totalAnios) {
    const [r, g, b] = METAS_COLOR_BASE[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    const h = v => Math.round(v * f).toString(16).padStart(2, '0');
    return `#${h(r)}${h(g)}${h(b)}`;
}
function _metasColorFill(tipo, idxAnio, totalAnios, alpha = 0.20) {
    const [r, g, b] = METAS_COLOR_BASE[tipo];
    const f = totalAnios <= 1 ? 1 : 1 - (idxAnio / (totalAnios - 1)) * 0.50;
    return `rgba(${Math.round(r*f)},${Math.round(g*f)},${Math.round(b*f)},${alpha})`;
}

function _metasNombresMeses() {
    return ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
}
function _metasAniosDisponibles() {
    const hoy = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => hoy - i);
}
function _metasLabelTipo(t) {
    return { cumplimiento: 'Cumplimiento (%)', noCumplidas: 'No Cumplidas (%)', cobertura: 'Cobertura (%)' }[t] || t;
}

function _destruirGraficoMetas() {
    if (_metasChartInstance?.destroy) { _metasChartInstance.destroy(); _metasChartInstance = null; }
}

// ── Fetch con caché ───────────────────────────────────────
async function _metasFetch(anio) {
    if (_metasCache[anio]) return _metasCache[anio];
    const res = await fetch(`${API_URL_METAS}?accion=metas_por_ambito&anio=${anio}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (data?.error) throw new Error(data.error);
    _metasCache[anio] = Array.isArray(data) ? data : [];
    return _metasCache[anio];
}

// ── Procesar raw → objetos enriquecidos ───────────────────
function _metasProcesar(rawList, anio) {
    const ambitoIdx = {};
    let ci = 0;
    return rawList.map(m => {
        const obj  = parseInt(m.meta_objetivo)        || 0;
        const form = parseInt(m.estudiantes_formados)  || 0;
        const pct  = obj > 0 ? parseFloat(((form / obj) * 100).toFixed(2)) : 0;
        const amb  = m.nombre_ambito || 'Sin ámbito';
        if (!(amb in ambitoIdx)) ambitoIdx[amb] = ci++;
        const col  = METAS_COLORES_AMBITO[ambitoIdx[amb] % METAS_COLORES_AMBITO.length];

        let periodo = m.periodo_tiempo || '';
        if (!periodo && m.fecha_inicio && m.fecha_fin) {
            const a = new Date(m.fecha_inicio + 'T00:00:00');
            const b = new Date(m.fecha_fin   + 'T00:00:00');
            periodo = `${a.toLocaleDateString('es-VE')} – ${b.toLocaleDateString('es-VE')}`;
        }
        if (!periodo) periodo = '—';

        const yi = m.fecha_inicio ? new Date(m.fecha_inicio + 'T00:00:00').getFullYear() : null;
        let vigencia = yi ? String(yi) : '—';
        const yf = m.fecha_fin ? new Date(m.fecha_fin + 'T00:00:00').getFullYear() : null;
        if (yi && yf && yi !== yf) vigencia = `${yi} – ${yf}`;

        const mesInicio = m.fecha_inicio ? new Date(m.fecha_inicio + 'T00:00:00').getMonth() : null;

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
            fecha_fin:    m.fecha_fin,
            mes:          mesInicio,
            anio:         yi || anio,
        };
    });
}

// ── Datos por mes para el gráfico (a partir del caché procesado) ──
function _metasDatosMensuales(metas, anio) {
    const meses = Array(12).fill(null).map(() => ({ objetivo: 0, formados: 0, pct: 0 }));
    metas.forEach(m => {
        if (m.anio === anio && m.mes !== null) {
            meses[m.mes].objetivo += m.meta_objetivo;
            meses[m.mes].formados += m.estudiantes_formados;
        }
    });
    meses.forEach(mes => {
        mes.pct = mes.objetivo > 0 ? (mes.formados / mes.objetivo) * 100 : 0;
    });
    return meses;
}

// ── Resumen KPI ───────────────────────────────────────────
function _metasResumen(metas) {
    const total      = metas.length;
    const cumplidas  = metas.filter(m => m.pct >= 100).length;
    const noCumplidas = total - cumplidas;
    const promedio   = total ? metas.reduce((s, m) => s + m.pct, 0) / total : 0;
    const totObj     = metas.reduce((s, m) => s + m.meta_objetivo,        0);
    const totForm    = metas.reduce((s, m) => s + m.estudiantes_formados,  0);
    return { total, cumplidas, noCumplidas, promedio, totObj, totForm };
}

// ── RENDER: KPIs ──────────────────────────────────────────
function _metasRenderKPIs(data) {
    const el = document.getElementById('kpiCardsMetas');
    if (!el) return;

    const svgBullseye = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10m0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12"/><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/><path d="M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/></svg>`;
    const svgCheck    = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>`;
    const svgX        = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>`;
    const svgPct      = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>`;
    const svgDown     = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const pctTotal = data.total > 0 ? ((data.cumplidas / data.total) * 100).toFixed(1) : 0;
    const pctNo    = data.total > 0 ? ((data.noCumplidas / data.total) * 100).toFixed(1) : 0;

    const cards = [
        { tipo:'total',       valor: data.total,                     label:'Metas Registradas',        sub:`en el año seleccionado`,       color:'primary', svg: svgBullseye },
        { tipo:'cumplidas',   valor: data.cumplidas,                 label:'Metas Cumplidas',           sub:`${pctTotal}% del total`,       color:'success', svg: svgCheck    },
        { tipo:'noCumplidas', valor: data.noCumplidas,               label:'Metas No Cumplidas',        sub:`${pctNo}% del total`,          color:'danger',  svg: svgX        },
        { tipo:'promedio',    valor: `${data.promedio.toFixed(1)}%`, label:'Promedio de Cumplimiento',  sub:`general entre todas las metas`,color:'warning', svg: svgPct      },
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3 kpi-card kpi-card-meta" role="button" data-tipo="${c.tipo}" style="cursor:pointer;transition:transform .15s,box-shadow .15s;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.12)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body text-center py-3">
                    <span class="text-${c.color}">${c.svg}</span>
                    <h2 class="fw-bold text-${c.color} mt-2 mb-0">${c.valor}</h2>
                    <p class="rp-kpi-card-label">${c.label}</p>
                    <p class="rp-kpi-card-sub">${c.sub}</p>
                    <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelMeta-${c.tipo}">
                        ${svgDown}Ver detalles
                    </span>
                </div>
            </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-meta').forEach(card =>
        card.addEventListener('click', () => _metasTogglePanel(card.dataset.tipo))
    );
}

// ── RENDER: Panel desplegable de KPI ─────────────────────
async function _metasTogglePanel(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperMetas');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total','cumplidas','noCumplidas','promedio'].forEach(t => {
        const b = document.getElementById(`badgePanelMeta-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalles`;
    });

    if (_metasPanelActivo === tipo) {
        wrapper.style.display = 'none';
        _metasPanelActivo = null;
        return;
    }

    _metasPanelActivo = tipo;
    wrapper.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary"></div><p class="mt-2 text-muted">Cargando detalles...</p></div>`;
    wrapper.style.display = 'block';

    await _metasRenderPanel(tipo);

    const badge = document.getElementById(`badgePanelMeta-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' }), 50);
}

async function _metasRenderPanel(tipo) {
    const wrapper = document.getElementById('panelDetalleWrapperMetas');
    if (!wrapper) return;
    const anio = _metasAnioTabla || new Date().getFullYear();

    try {
        const raw    = await _metasFetch(anio);
        const metas  = _metasProcesar(raw, anio);

        const configs = {
            total:       { lista: metas,                              titulo:'Todas las Metas',                color:'primary' },
            cumplidas:   { lista: metas.filter(m => m.pct >= 100),    titulo:'Metas Cumplidas',                color:'success' },
            noCumplidas: { lista: metas.filter(m => m.pct < 100),     titulo:'Metas No Cumplidas',             color:'danger'  },
            promedio:    { lista: [...metas].sort((a,b) => b.pct - a.pct), titulo:'Metas por Cumplimiento',   color:'warning' },
        };
        const { lista, titulo, color } = configs[tipo] || configs.total;

        if (!lista.length) {
            wrapper.innerHTML = `<div class="card border-0 shadow-sm"><div class="card-body text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin metas en esta categoría para ${anio}.</div></div>`;
            return;
        }

        const filas = lista.slice(0, 20).map(m => {
            const bC = m.pct >= 100 ? '#22c55e' : m.pct >= 75 ? '#0891b2' : m.pct >= 40 ? '#f59e0b' : '#ef4444';
            return `<tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:.6rem .8rem;">
                    <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.22em .7em;border-radius:6px;font-weight:600;background:${m.color.light};color:${m.color.text};border:1px solid ${m.color.border};font-size:.72rem;">${m.nombre_ambito}</span>
                </td>
                <td style="padding:.6rem .8rem;font-size:.8rem;">${m.tipo_objetivo}</td>
                <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${m.meta_objetivo.toLocaleString()}</td>
                <td style="padding:.6rem .8rem;text-align:right;font-weight:600;color:${bC};">${m.estudiantes_formados.toLocaleString()}</td>
                <td style="padding:.6rem .8rem;min-width:160px;">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;width:${Math.min(m.pct,100)}%;background:${bC};border-radius:99px;transition:width .6s ease;"></div>
                        </div>
                        <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${bC};">${m.pct.toFixed(1)}%</span>
                    </div>
                </td>
                <td style="padding:.6rem .8rem;"><span class="badge ${m.estado==='Activa'?'bg-success':'bg-secondary'}">${m.estado}</span></td>
                <td style="padding:.6rem .8rem;font-size:.75rem;color:#64748b;">${m.periodo_tiempo}</td>
            </tr>`;
        }).join('');

        wrapper.innerHTML = `
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="mb-0 text-${color} d-flex align-items-center gap-2">
                        <i class="bi bi-bullseye"></i>${titulo}
                        <span class="badge bg-${color} bg-opacity-10 text-${color} ms-1">${lista.length} metas</span>
                    </h6>
                    <small class="text-muted">Año ${anio}</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light"><tr><th>Ámbito</th><th>Tipo</th><th class="text-end">Objetivo</th><th class="text-end">Alcanzado</th><th>Progreso</th><th>Estado</th><th>Período</th></tr></thead>
                        <tbody>${filas}</tbody>
                    </table>
                </div>
                ${lista.length > 20 ? `<p class="text-muted small mt-2 mb-0">Mostrando las primeras 20 de ${lista.length} metas.</p>` : ''}
            </div>
        </div>`;
    } catch (err) {
        wrapper.innerHTML = `<div class="alert alert-warning">Error al cargar detalles: ${err.message}</div>`;
    }
}

// ── RENDER: Tabla principal ───────────────────────────────
function _metasRenderTabla(metas) {
    const container = document.getElementById('tablaMetasContainer');
    if (!container) return;

    if (!metas.length) {
        container.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay metas registradas para este año.</div>`;
        return;
    }

    const filas = metas.map(m => {
        const bC = m.pct >= 100 ? '#22c55e' : m.pct >= 75 ? '#0891b2' : m.pct >= 40 ? '#f59e0b' : '#ef4444';
        return `<tr>
            <td style="padding:.6rem .8rem;">
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.22em .7em;border-radius:6px;font-weight:600;background:${m.color.light};color:${m.color.text};border:1px solid ${m.color.border};font-size:.72rem;">${m.nombre_ambito}</span>
            </td>
            <td style="padding:.6rem .8rem;font-size:.8rem;">${m.tipo_objetivo}</td>
            <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${m.meta_objetivo.toLocaleString()}</td>
            <td style="padding:.6rem .8rem;text-align:right;font-weight:600;color:${bC};">${m.estudiantes_formados.toLocaleString()}</td>
            <td style="padding:.6rem .8rem;min-width:180px;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:${Math.min(m.pct,100)}%;background:${bC};border-radius:99px;"></div>
                    </div>
                    <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${bC};">${m.pct.toFixed(1)}%</span>
                </div>
            </td>
            <td style="padding:.6rem .8rem;"><span class="badge ${m.estado==='Activa'?'bg-success':'bg-secondary'}">${m.estado}</span></td>
            <td style="padding:.6rem .8rem;font-size:.75rem;color:#64748b;">${m.periodo_tiempo}</td>
        </tr>`;
    }).join('');

    container.innerHTML = `
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle">
            <thead class="table-light">
                <tr><th>Ámbito</th><th>Tipo</th><th class="text-end">Objetivo</th><th class="text-end">Alcanzado</th><th>Progreso</th><th>Estado</th><th>Período</th></tr>
            </thead>
            <tbody>${filas}</tbody>
        </table>
    </div>`;
}

// ── RENDER: Gráfico multi-año ─────────────────────────────
function _metasRenderGrafico() {
    const wrapper = document.getElementById('graficoWrapperMetas');
    if (!wrapper) return;

    if (!_metasGraficoTipos.size || !_metasGraficoAnios.length) {
        _destruirGraficoMetas();
        wrapper.innerHTML = `
            <div class="rp-chart-empty">
                <i class="bi bi-bar-chart-line" style="font-size:3rem;opacity:.25;display:block;margin-bottom:.75rem;"></i>
                <p class="rp-chart-empty-text">Selecciona al menos una métrica y un año para visualizar</p>
            </div>`;
        const titulo = document.getElementById('tituloGraficoMetas');
        if (titulo) titulo.textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) wrapper.innerHTML = '<canvas id="canvasGraficoMetas"></canvas>';
    _destruirGraficoMetas();

    const ctx    = wrapper.querySelector('canvas').getContext('2d');
    const tipos  = ['cumplimiento','noCumplidas','cobertura'].filter(t => _metasGraficoTipos.has(t));
    const anios  = [..._metasGraficoAnios].sort((a,b) => b - a);
    const nAnios = anios.length;
    const esBar  = _metasTipoGrafico === 'bar';
    const labels = _metasNombresMeses();
    const datasets = [];

    tipos.forEach((tipo) => {
        anios.forEach((anio, anioIdx) => {
            const metas  = _metasCache[anio] ? _metasProcesar(_metasCache[anio], anio) : [];
            const mensal = _metasDatosMensuales(metas, anio);
            let datos;
            if (tipo === 'cumplimiento') dados = mensal.map(m => parseFloat(m.pct.toFixed(1)));
            else if (tipo === 'noCumplidas') dados = mensal.map(m => parseFloat((100 - m.pct).toFixed(1)));
            else dados = mensal.map(m => parseFloat(m.pct.toFixed(1)));
            // alias fix
            const datos2 = tipo === 'cumplimiento' ? mensal.map(m => parseFloat(m.pct.toFixed(1)))
                         : tipo === 'noCumplidas'  ? mensal.map(m => parseFloat((100 - m.pct).toFixed(1)))
                         : mensal.map(m => parseFloat(m.pct.toFixed(1)));

            const colorBorde = _metasColorBorde(tipo, anioIdx, nAnios);
            const colorFill  = _metasColorFill(tipo, anioIdx, nAnios, 0.22);
            let label = _metasLabelTipo(tipo);
            if (nAnios > 1) label += ` ${anio}`;

            datasets.push({
                label,
                data: datos2,
                borderColor: colorBorde,
                backgroundColor: colorFill,
                borderWidth: esBar ? 0 : 2.5,
                fill: esBar ? false : { target: 'origin', above: colorFill },
                tension: esBar ? 0 : 0.4,
                borderRadius: esBar ? 4 : 0,
                pointBackgroundColor: colorBorde,
                pointRadius: 4,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                spanGaps: false,
            });
        });
    });

    const tituloTexto = tipos.map(t => _metasLabelTipo(t)).join(', ') + ` — ${anios.join(', ')}`;
    const tituloEl = document.getElementById('tituloGraficoMetas');
    if (tituloEl) tituloEl.textContent = tituloTexto;

    _metasChartInstance = new Chart(ctx, {
        type: _metasTipoGrafico,
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                filler: { propagate: true },
                tooltip: {
                    mode: 'index', intersect: false,
                    callbacks: { label: c => `${c.dataset.label}: ${c.parsed.y !== null ? c.parsed.y.toFixed(1) : 'Sin dato'}` }
                }
            },
            scales: {
                y: { beginAtZero: true, max: 110, grid: { color:'rgba(0,0,0,.06)' }, title: { display:true, text:'Porcentaje (%)' } },
                x: { grid: { color:'rgba(0,0,0,.06)' } }
            },
            interaction: { intersect:false, mode:'index' }
        }
    });
}

// ── Chips de años ─────────────────────────────────────────
function _metasSincronizarChips() {
    const chips = document.getElementById('chipsAniosMetas');
    if (!chips) return;
    chips.innerHTML = _metasGraficoAnios.map(a =>
        `<span class="rp-year-chip" data-anio="${a}" style="cursor:pointer;">${a}</span>`
    ).join('');
    chips.querySelectorAll('.rp-year-chip').forEach(chip =>
        chip.addEventListener('click', () => {
            _metasGraficoAnios = _metasGraficoAnios.filter(x => x !== parseInt(chip.dataset.anio));
            _metasSincronizarChips();
            _metasRenderGrafico();
        })
    );
}

// ── Inicializar controles toolbar ─────────────────────────
function _metasInicializarControles() {
    // Botones tipo métrica
    document.querySelectorAll('.btn-tipo-meta').forEach(btn => {
        const clon = btn.cloneNode(true);
        btn.parentNode.replaceChild(clon, btn);
    });
    document.querySelectorAll('.btn-tipo-meta').forEach(btn => {
        btn.classList.toggle('active', _metasGraficoTipos.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const tipo = btn.dataset.tipo;
            _metasGraficoTipos.has(tipo) ? _metasGraficoTipos.delete(tipo) : _metasGraficoTipos.add(tipo);
            btn.classList.toggle('active', _metasGraficoTipos.has(tipo));
            _metasRenderGrafico();
        });
    });

    // Limpiar tipos
    const btnLT = document.getElementById('btnLimpiarTiposMetas');
    if (btnLT) { const c = btnLT.cloneNode(true); btnLT.parentNode.replaceChild(c,btnLT);
        document.getElementById('btnLimpiarTiposMetas').addEventListener('click', () => {
            _metasGraficoTipos.clear();
            document.querySelectorAll('.btn-tipo-meta').forEach(b => b.classList.remove('active'));
            _metasRenderGrafico();
        });
    }

    // Tipo de gráfica
    document.querySelectorAll('.btn-chart-type-metas').forEach(btn => {
        const c = btn.cloneNode(true); btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-chart-type-metas').forEach(btn => {
        const activo = btn.dataset.tipo === _metasTipoGrafico;
        btn.classList.toggle('active', activo);
        btn.classList.toggle('btn-secondary', activo);
        btn.classList.toggle('btn-outline-secondary', !activo);
        btn.addEventListener('click', () => {
            _metasTipoGrafico = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type-metas').forEach(b => {
                const a2 = b.dataset.tipo === _metasTipoGrafico;
                b.classList.toggle('active', a2);
                b.classList.toggle('btn-secondary', a2);
                b.classList.toggle('btn-outline-secondary', !a2);
            });
            _metasRenderGrafico();
        });
    });

    // Agregar año al gráfico
    const btnAddA = document.getElementById('btnAddAnioMetasGrafico');
    if (btnAddA) { const c = btnAddA.cloneNode(true); btnAddA.parentNode.replaceChild(c, btnAddA);
        document.getElementById('btnAddAnioMetasGrafico').addEventListener('click', async () => {
            const sel = document.getElementById('selectAnioMetasGrafico');
            if (!sel) return;
            const a = parseInt(sel.value);
            if (_metasGraficoAnios.includes(a)) return;
            if (_metasGraficoAnios.length >= _METAS_MAX_ANIOS) {
                const aviso = document.getElementById('metasAnioLimiteAviso');
                if (aviso) { aviso.style.display='inline-block'; setTimeout(()=>{ aviso.style.display='none'; },2500); }
                return;
            }
            try {
                await _metasFetch(a);
                _metasGraficoAnios.push(a);
                _metasGraficoAnios.sort((x,y) => y - x);
                _metasSincronizarChips();
                _metasRenderGrafico();
            } catch (err) { console.warn(`[RE-Meta-PorAmbito] No se pudo cargar año ${a}:`, err); }
        });
    }

    // Limpiar años
    const btnLA = document.getElementById('btnLimpiarAniosMetas');
    if (btnLA) { const c = btnLA.cloneNode(true); btnLA.parentNode.replaceChild(c, btnLA);
        document.getElementById('btnLimpiarAniosMetas').addEventListener('click', () => {
            _metasGraficoAnios = [];
            _metasSincronizarChips();
            _metasRenderGrafico();
        });
    }

    _metasSincronizarChips();
}

// ── Cambiar año en la tabla ───────────────────────────────
async function _metasCambiarAnioTabla(anio) {
    if (anio === _metasAnioTabla) return;
    const loadEl = document.getElementById('loadingMetas');
    const errEl  = document.getElementById('errorMetas');
    if (loadEl) { loadEl.style.display='block'; document.getElementById('loadingMsgMetas').textContent=`Cargando año ${anio}...`; }
    if (errEl)  errEl.style.display='none';

    try {
        const raw   = await _metasFetch(anio);
        const metas = _metasProcesar(raw, anio);
        _metasAnioTabla = anio;
        document.getElementById('anioSeleccionadoMetas').textContent = anio;
        _metasRenderKPIs(_metasResumen(metas));
        _metasRenderTabla(metas);
        _metasProcesadas = metas;
        if (_metasPanelActivo) await _metasRenderPanel(_metasPanelActivo);
    } catch (err) {
        if (errEl) { errEl.style.display='block'; document.getElementById('errorMsgMetas').textContent=`Error al cargar ${anio}: ${err.message}`; }
    } finally {
        if (loadEl) loadEl.style.display='none';
    }
}

// ── FUNCIÓN PRINCIPAL ─────────────────────────────────────
async function cargarMetaPorAmbito() {
    if (typeof window.crearTabs === 'function') window.crearTabs(4, 11);

    const contentDiv = document.getElementById('reportContent');
    const hoy        = new Date().getFullYear();
    const aniosDisp  = _metasAniosDisponibles();

    // Reset estado
    _destruirGraficoMetas();
    _metasCache          = {};
    _metasProcesadas     = [];
    _metasGraficoAnios   = [hoy];
    _metasGraficoTipos   = new Set(['cumplimiento']);
    _metasTipoGrafico    = 'bar';
    _metasAnioBase       = hoy;
    _metasAnioTabla      = hoy;
    _metasPanelActivo    = null;

    const svgPlus = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>`;
    const svgX    = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="bi bi-bullseye me-2"></i>Metas por Ámbito</h4>
        </div>
        <div class="card-body">

            <div id="loadingMetas" class="text-center py-5">
                <div class="spinner-border text-danger" role="status"></div>
                <p class="mt-3 text-muted" id="loadingMsgMetas">Cargando datos de metas...</p>
            </div>
            <div id="errorMetas" class="alert alert-danger" style="display:none;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><span id="errorMsgMetas"></span>
            </div>

            <div id="contenidoMetas" style="display:none;">

                <div id="kpiCardsMetas"></div>
                <div id="panelDetalleWrapperMetas" style="display:none;" class="mb-3"></div>

                <!-- Toolbar gráfico -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-start gap-3">

                            <div style="flex:1 1 auto;min-width:220px;">
                                <div class="rp-toolbar-label mb-2">Métrica a representar</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-outline-success btn-tipo-meta active" data-tipo="cumplimiento">
                                        <i class="bi bi-graph-up-arrow me-1"></i>Cumplimiento
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-tipo-meta" data-tipo="noCumplidas">
                                        <i class="bi bi-x-circle me-1"></i>No Cumplidas
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary btn-tipo-meta" data-tipo="cobertura">
                                        <i class="bi bi-bullseye me-1"></i>Cobertura
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarTiposMetas">${svgX} Limpiar</button>
                                </div>
                            </div>

                            <div class="rp-vdivider d-none d-md-block"></div>

                            <div style="flex:0 1 auto;min-width:200px;">
                                <div class="rp-toolbar-label mb-2">
                                    Años a comparar
                                    <span id="metasAnioLimiteAviso" class="badge bg-warning text-dark ms-1" style="display:none;font-size:.65rem;">máx. ${_METAS_MAX_ANIOS}</span>
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
                                <div class="rp-toolbar-label mb-2">Tipo de gráfica</div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type-metas" data-tipo="bar" title="Torres">
                                        <i class="bi bi-bar-chart-fill me-1"></i>Torres
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary btn-chart-type-metas" data-tipo="line" title="Líneas">
                                        <i class="bi bi-graph-up me-1"></i>Líneas
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Gráfico -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloGraficoMetas"></h6>
                        <div id="graficoWrapperMetas" class="rp-chart-wrap rp-chart-wrap--lg"></div>
                    </div>
                </div>

                <!-- Tabla detalle -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h5 class="card-title mb-0">Detalle de Metas — <span id="anioSeleccionadoMetas">${hoy}</span></h5>
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

    // Cargar datos
    const loadEl  = document.getElementById('loadingMetas');
    const errEl   = document.getElementById('errorMetas');
    const contEl  = document.getElementById('contenidoMetas');

    try {
        const raw   = await _metasFetch(hoy);
        const metas = _metasProcesar(raw, hoy);
        _metasProcesadas = metas;

        if (loadEl)  loadEl.style.display  = 'none';
        if (contEl)  contEl.style.display  = 'block';

        _metasRenderKPIs(_metasResumen(metas));
        _metasRenderTabla(metas);
        _metasInicializarControles();
        _metasRenderGrafico();

    } catch (err) {
        if (loadEl) loadEl.style.display = 'none';
        if (errEl)  { errEl.style.display='block'; document.getElementById('errorMsgMetas').textContent=`Error: ${err.message}`; }
        console.error('[RE-Meta-PorAmbito]', err);
    }

    document.getElementById('selectAnioTablaMetas')?.addEventListener('change', async e => {
        const a = parseInt(e.target.value);
        if (a > new Date().getFullYear()) return;
        await _metasCambiarAnioTabla(a);
    });
}

window.cargarMetaPorAmbito  = cargarMetaPorAmbito;
window.limpiarRecursosMetas = function () {
    _destruirGraficoMetas();
    _metasCache = {}; _metasProcesadas = []; _metasGraficoAnios = [];
    _metasGraficoTipos = new Set(['cumplimiento']); _metasTipoGrafico = 'bar';
    _metasPanelActivo = null; _metasAnioBase = null; _metasAnioTabla = null;
};

window.exportarPdfMetasPorAmbito = function() {
    let chartBase64 = '';
    if (_metasChartInstance) {
        // Asegurar que el fondo sea blanco antes de exportar
        const canvas = _metasChartInstance.canvas;
        const ctx = canvas.getContext('2d');
        ctx.save();
        ctx.globalCompositeOperation = 'destination-over';
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        chartBase64 = _metasChartInstance.toBase64Image();
        ctx.restore();
    }
    
    // Obtener los datos del panel activo si lo hay
    let panelData = null;
    if (_metasPanelActivo) {
        panelData = {
            tipo: _metasPanelActivo,
            titulo: {
                total: 'Todas las Metas',
                cumplidas: 'Metas Cumplidas',
                noCumplidas: 'Metas No Cumplidas',
                promedio: 'Metas por Cumplimiento'
            }[_metasPanelActivo] || _metasPanelActivo,
            metas: _metasPanelActivo === 'cumplidas' ? _metasProcesadas.filter(m => m.pct >= 100) :
                   _metasPanelActivo === 'noCumplidas' ? _metasProcesadas.filter(m => m.pct < 100) :
                   _metasPanelActivo === 'promedio' ? [..._metasProcesadas].sort((a,b) => b.pct - a.pct) :
                   _metasProcesadas
        };
    }

    // Extraer datos históricos consolidados de _metasCache
    const historial = [];
    if (_metasGraficoAnios && _metasGraficoAnios.length > 0) {
        _metasGraficoAnios.forEach(a => {
            if (_metasCache[a]) {
                const procesadas = _metasProcesar(_metasCache[a], a);
                const resumen = _metasResumen(procesadas);
                historial.push({
                    anio: a,
                    totalObj: resumen.totObj,
                    totalForm: resumen.totForm,
                    cumplimientoGlobal: resumen.totObj > 0 ? (resumen.totForm / resumen.totObj) * 100 : 0
                });
            }
        });
    }

    const payload = {
        anioTabla: _metasAnioTabla || new Date().getFullYear(),
        graficoAnios: [..._metasGraficoAnios].sort((a,b) => b - a),
        graficoTipos: Array.from(_metasGraficoTipos),
        panelData: panelData,
        resumen: _metasResumen(_metasProcesadas),
        metas: _metasProcesadas,
        historial: historial
    };
    
    if (typeof window.postAndOpenPdf === 'function') {
        window.postAndOpenPdf('../controlador/fpdf/Reporte/ReporteMetaPorAmbito.php', {
            chart_image: chartBase64,
            payload_json: JSON.stringify(payload)
        });
    } else {
        alert('La función de exportación no está disponible.');
    }
};

console.log('RE-Meta-PorAmbito.js cargado');

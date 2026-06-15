// ============================================================
// RE-Meta-Historico.js
// Función: cargarHistoricoMetas
// API: api_reportes.php?accion=metas_por_anio
//      api_reportes.php?accion=metas_por_trimestre&anio_meta=XXXX
// ============================================================

const API_URL_HIST = '../controlador/Reporte/api_reportes.php';

let graficoHistInstance = null;
let cacheHistAnual      = null;
let cacheHistTrim       = {};

let tiposActivosHist = new Set(['objetivo', 'alcanzado']);
let tipoGraficoHist  = 'bar';
let vistaActivaHist  = 'anual';
let anioHistTrim     = new Date().getFullYear();
let tipoMetaHist     = '';
let panelActivoHist  = null;

const COLOR_BASE_HIST = {
    objetivo:    [99,  102, 241],
    alcanzado:   [34,  197,  94],
    cumplimiento:[220,  53,  69],
};

const TRIM_NOMBRES = ['', 'T1 (Ene–Mar)', 'T2 (Abr–Jun)', 'T3 (Jul–Sep)', 'T4 (Oct–Dic)'];

// ─── Helpers ─────────────────────────────────────────────────
function colorBordeHist(tipo) {
    const [r, g, b] = COLOR_BASE_HIST[tipo];
    return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
}
function colorFillHist(tipo, alpha = 0.22) {
    const [r, g, b] = COLOR_BASE_HIST[tipo];
    return `rgba(${r},${g},${b},${alpha})`;
}
function destruirGraficoHist() {
    if (graficoHistInstance?.destroy) { graficoHistInstance.destroy(); graficoHistInstance = null; }
}
function initTooltipsHist(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}
function obtenerAniosDisponiblesHist() {
    const hoy = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => hoy - i);
}

// ─── Fetch con caché ─────────────────────────────────────────
async function fetchHistAnual(tipoMeta) {
    if (cacheHistAnual && cacheHistAnual._tipo === tipoMeta) return cacheHistAnual;
    const url = `${API_URL_HIST}?accion=metas_por_anio${tipoMeta ? '&tipo=' + encodeURIComponent(tipoMeta) : ''}`;
  const res = await fetchWithTimeout(url,
    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }, 15000);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const data = await res.json();
    if (data.error) throw new Error(data.error);
    cacheHistAnual = Array.isArray(data) ? data : (data.data || []);
    cacheHistAnual._tipo = tipoMeta;
    return cacheHistAnual;
}
async function fetchHistTrimestre(anio, tipoMeta) {
    const key = `${anio}_${tipoMeta}`;
    if (cacheHistTrim[key]) return cacheHistTrim[key];
    const url = `${API_URL_HIST}?accion=metas_por_trimestre&anio_meta=${anio}${tipoMeta ? '&tipo=' + encodeURIComponent(tipoMeta) : ''}`;
  const res = await fetchWithTimeout(url,
    { headers: { 'X-Requested-With': 'XMLHttpRequest' } }, 15000);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const data = await res.json();
  if (data.error) throw new Error(data.error);
  cacheHistTrim[key] = Array.isArray(data) ? data : (data.data || []);
    return cacheHistTrim[key];
}

// Helper: fetch con timeout para evitar bloqueos indefinidos
async function fetchWithTimeout(url, options = {}, timeout = 10000) {
  const controller = new AbortController();
  const id = setTimeout(() => controller.abort(), timeout);
  options.signal = controller.signal;
  try {
    const resp = await fetch(url, options);
    clearTimeout(id);
    return resp;
  } catch (err) {
    clearTimeout(id);
    throw err;
  }
}

// ─── Normalización de respuestas del backend ─────────────────
function normalizarAnual(raw) {
    return raw.map(d => {
        const objTotal = parseInt(d.objetivo_total || d.meta_objetivo || 0);
        const alcTotal = parseInt(d.alcanzado_total || d.estudiantes_formados || 0);
        return {
            label:          String(d.anio || d.year || '?'),
            total_metas:    parseInt(d.total_metas    || 1),
            objetivo_total: objTotal,
            alcanzado_total:alcTotal,
            pct:            objTotal > 0 ? parseFloat((alcTotal / objTotal * 100).toFixed(2)) : 0,
            metas_cumplidas:parseInt(d.metas_cumplidas || 0),
        };
    });
}
function normalizarTrimestre(raw) {
    return raw.map(d => {
        const objTotal = parseInt(d.objetivo_total || d.meta_objetivo || 0);
        const alcTotal = parseInt(d.alcanzado_total || d.estudiantes_formados || 0);
        return {
            trimestre:      parseInt(d.trimestre || d.quarter || 0),
            label:          TRIM_NOMBRES[parseInt(d.trimestre || d.quarter || 0)] || `T${d.trimestre}`,
            total_metas:    parseInt(d.total_metas    || 1),
            objetivo_total: objTotal,
            alcanzado_total:alcTotal,
            pct:            objTotal > 0 ? parseFloat((alcTotal / objTotal * 100).toFixed(2)) : 0,
            metas_cumplidas:parseInt(d.metas_cumplidas || 0),
        };
    });
}
function buildResumenHist(datos) {
    const totalPeriodos = datos.length;
    const cumplidos     = datos.filter(d => d.pct >= 100).length;
    const sumaObj       = datos.reduce((s, d) => s + d.objetivo_total,  0);
    const sumaAlc       = datos.reduce((s, d) => s + d.alcanzado_total, 0);
    const promPct       = totalPeriodos ? datos.reduce((s, d) => s + d.pct, 0) / totalPeriodos : 0;
    return { totalPeriodos, cumplidos, sumaObj, sumaAlc, promPct };
}

// ═══════════════════════════════════════════════════════════
//  FUNCIÓN PRINCIPAL
// ═══════════════════════════════════════════════════════════
async function cargarHistoricoMetas() {

    if (typeof window.crearTabs === 'function') window.crearTabs(4, 16);

    const contentDiv = document.getElementById('reportContent');
    const hoy        = new Date().getFullYear();
    const aniosDisp  = obtenerAniosDisponiblesHist();

    destruirGraficoHist();
    cacheHistAnual   = null;
    cacheHistTrim    = {};
    panelActivoHist  = null;
    vistaActivaHist  = 'anual';
    anioHistTrim     = hoy;
    tipoMetaHist     = '';
    tiposActivosHist = new Set(['objetivo', 'alcanzado']);
    tipoGraficoHist  = 'bar';

    const svgX = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`;

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Histórico de Metas</h4>
      </div>
      <div class="card-body">

        <div id="loadingHist" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
          <p class="mt-3 text-muted" id="loadingMsgHist">Cargando histórico...</p>
        </div>
        <div id="errorHist" class="alert alert-danger d-none">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <span id="errorMsgHist">Error al cargar los datos.</span>
        </div>

        <div id="contenidoHist" style="display:none;">

          <div id="kpiCardsHist"></div>
          <div id="panelDetalleWrapperHist" style="display:none;" class="mb-3"></div>

          <!-- Toolbar -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
              <div class="d-flex flex-wrap align-items-start gap-3">

                <!-- Vista -->
                <div style="flex:0 0 auto;">
                  <div class="rp-toolbar-label mb-2">Vista</div>
                  <div class="btn-group btn-group-sm">
                    <input type="radio" class="btn-check" name="histVista" id="histVistaAnual" value="anual" autocomplete="off" checked>
                    <label class="btn btn-outline-danger" for="histVistaAnual">
                      <i class="bi bi-calendar-range me-1"></i>Por Año
                    </label>
                    <input type="radio" class="btn-check" name="histVista" id="histVistaTrim" value="trimestre" autocomplete="off">
                    <label class="btn btn-outline-danger" for="histVistaTrim">
                      <i class="bi bi-calendar3 me-1"></i>Por Trimestre
                    </label>
                  </div>
                </div>

                <div id="histAnioWrap" style="display:none;flex:0 0 auto;">
                  <div class="rp-toolbar-label mb-2">Año</div>
                  <select id="selectAnioHistTrim" class="form-select form-select-sm" style="width:100px;">
                    ${aniosDisp.map(a => `<option value="${a}" ${a===hoy?'selected':''}>${a}</option>`).join('')}
                  </select>
                </div>

                <div class="rp-vdivider d-none d-md-block"></div>

                <div style="flex:0 0 auto;">
                  <div class="rp-toolbar-label mb-2">Tipo de Meta</div>
                  <select id="selectTipoMetaHist" class="form-select form-select-sm" style="width:140px;">
                    <option value="">Todos</option>
                    <option value="Aprobados">Aprobados</option>
                    <option value="Inscritos">Inscritos</option>
                    <option value="Ingresados">Ingresados</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                  </select>
                </div>

                <div class="rp-vdivider d-none d-md-block"></div>

                <!-- Métricas -->
                <div style="flex:1 1 auto;min-width:260px;">
                  <div class="rp-toolbar-label mb-2">Métricas a representar</div>
                  <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-primary btn-tipo-hist active"  data-tipo="objetivo">
                      <i class="bi bi-bullseye me-1"></i>Objetivo
                    </button>
                    <button class="btn btn-sm btn-outline-success btn-tipo-hist active"  data-tipo="alcanzado">
                      <i class="bi bi-check-circle me-1"></i>Alcanzado
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-tipo-hist"          data-tipo="cumplimiento">
                      <i class="bi bi-percent me-1"></i>% Cumplimiento
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarTiposHist">${svgX} Limpiar</button>
                  </div>
                </div>

                <div class="rp-vdivider d-none d-md-block"></div>

                <!-- Tipo gráfica -->
                <div style="flex:0 0 auto;">
                  <div class="rp-toolbar-label mb-2">Tipo de gráfica</div>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-secondary btn-chart-type-hist active" data-tipo="bar">
                      <i class="bi bi-bar-chart-fill me-1"></i>Torres
                    </button>
                    <button class="btn btn-sm btn-outline-secondary btn-chart-type-hist" data-tipo="line">
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
              <h6 class="card-title mb-3 text-muted fw-semibold" id="tituloGraficoHist"></h6>
              <div id="graficoWrapperHist" class="rp-chart-wrap rp-chart-wrap--lg"></div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-3" id="histTablaTitulo">Resumen por Período</h5>
              <div class="table-responsive" id="histTablaContainer"></div>
            </div>
          </div>

        </div>
      </div>
    </div>`;

    initTooltipsHist(contentDiv);

    document.querySelectorAll('input[name="histVista"]').forEach(radio => {
        radio.addEventListener('change', async function () {
            vistaActivaHist = this.value;
            const wrap = document.getElementById('histAnioWrap');
            if (wrap) wrap.style.display = vistaActivaHist === 'trimestre' ? 'block' : 'none';
            await _cargarVistaHist();
        });
    });

    document.getElementById('selectAnioHistTrim')?.addEventListener('change', async function () {
        anioHistTrim = parseInt(this.value);
        if (vistaActivaHist === 'trimestre') await _cargarVistaHist();
    });

    document.getElementById('selectTipoMetaHist')?.addEventListener('change', async function () {
        tipoMetaHist = this.value;
        await _cargarVistaHist();
    });

    await _cargarVistaHist();
}

// ─── Carga según vista ────────────────────────────────────────
async function _cargarVistaHist() {
    const loadEl   = document.getElementById('loadingHist');
    const errorEl  = document.getElementById('errorHist');
    const contenEl = document.getElementById('contenidoHist');

    if (loadEl)   loadEl.style.display   = 'block';
    if (errorEl)  errorEl.classList.add('d-none');
    if (contenEl) contenEl.style.display = 'none';

    const msgEl = document.getElementById('loadingMsgHist');
    if (msgEl) msgEl.textContent = vistaActivaHist === 'anual'
        ? 'Cargando histórico anual...'
        : `Cargando trimestres de ${anioHistTrim}...`;

    try {
        let datos = [];
        if (vistaActivaHist === 'anual') {
            datos = normalizarAnual(await fetchHistAnual(tipoMetaHist));
            document.getElementById('histTablaTitulo').textContent = 'Resumen Anual de Metas';
        } else {
            datos = normalizarTrimestre(await fetchHistTrimestre(anioHistTrim, tipoMetaHist));
            document.getElementById('histTablaTitulo').textContent = `Resumen Trimestral ${anioHistTrim}`;
        }

        if (loadEl)   loadEl.style.display   = 'none';
        if (contenEl) contenEl.style.display = 'block';

        renderKPIsHist(buildResumenHist(datos));
        renderGraficoHist(datos);
        renderTablaHist(datos);
        inicializarControlesHist(datos);
        if (panelActivoHist) await renderPanelDetalleHist(panelActivoHist, datos);

    } catch (err) {
      if (loadEl)  loadEl.style.display = 'none';
      if (errorEl) {
        errorEl.classList.remove('d-none');
        const msg = (err && err.name === 'AbortError') ? 'Tiempo de espera al obtener los datos.' : (err && err.message ? err.message : String(err));
        document.getElementById('errorMsgHist').textContent = `Error: ${msg}`;
      }
      console.error('[RE-Meta-Historico] Error:', err);
    }
}

// ─── KPI Cards ────────────────────────────────────────────────
function renderKPIsHist(r) {
    const el = document.getElementById('kpiCardsHist');
    if (!el) return;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const etiqPeriodo = vistaActivaHist === 'anual' ? 'años registrados' : 'trimestres con datos';
    const cards = [
        { tipo:'periodos',  valor: r.totalPeriodos,            label:`Períodos (${etiqPeriodo})`, sub:'en el histórico',                                                       color:'primary', icon:'bi-calendar3'      },
        { tipo:'cumplidos', valor: r.cumplidos,                label:'Períodos Cumplidos',         sub:`${r.totalPeriodos>0?((r.cumplidos/r.totalPeriodos)*100).toFixed(1):0}% del total`, color:'success', icon:'bi-trophy'         },
        { tipo:'objetivo',  valor: r.sumaObj.toLocaleString(), label:'Objetivo Total Acumulado',   sub:'participantes meta histórico',                                          color:'info',    icon:'bi-people'         },
        { tipo:'promedio',  valor: `${r.promPct.toFixed(1)}%`, label:'Promedio de Cumplimiento',   sub:`Alcanzado: ${r.sumaAlc.toLocaleString()}`,                              color:'warning', icon:'bi-graph-up-arrow' },
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
          <div class="card border-0 shadow-sm mb-3 kpi-card kpi-card-hist" role="button" data-tipo="${c.tipo}">
            <div class="card-body text-center py-3">
              <i class="bi ${c.icon} fs-2 text-${c.color}"></i>
              <h2 class="fw-bold text-${c.color} mt-2 mb-0">${c.valor}</h2>
              <p class="rp-kpi-card-label">${c.label}</p>
              <p class="rp-kpi-card-sub">${c.sub}</p>
              <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelHist-${c.tipo}">
                ${svgDown}Ver detalles
              </span>
            </div>
          </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-hist').forEach(card =>
        card.addEventListener('click', async () => {
            let datos = [];
            try {
                datos = vistaActivaHist === 'anual'
                    ? normalizarAnual(await fetchHistAnual(tipoMetaHist))
                    : normalizarTrimestre(await fetchHistTrimestre(anioHistTrim, tipoMetaHist));
            } catch {}
            await togglePanelDetalleHist(card.dataset.tipo, datos);
        })
    );
}

// ─── Panel colapsable ─────────────────────────────────────────
async function togglePanelDetalleHist(tipo, datos) {
    const wrapper = document.getElementById('panelDetalleWrapperHist');
    if (!wrapper) return;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['periodos','cumplidos','objetivo','promedio'].forEach(t => {
        const b = document.getElementById(`badgePanelHist-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalles`;
    });

    if (panelActivoHist === tipo) {
        wrapper.style.display = 'none';
        panelActivoHist = null;
        return;
    }

    panelActivoHist = tipo;
    wrapper.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary"></div><p class="mt-2 text-muted">Cargando detalles...</p></div>`;
    wrapper.style.display = 'block';
    await renderPanelDetalleHist(tipo, datos);

    const badge = document.getElementById(`badgePanelHist-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior:'smooth', block:'center' }), 50);
}

async function renderPanelDetalleHist(tipo, datos) {
    const wrapper = document.getElementById('panelDetalleWrapperHist');
    if (!wrapper || !datos?.length) return;

    let lista = [], titulo = '', color = '';
    if      (tipo === 'periodos')  { lista = datos;                                                 titulo = 'Todos los Períodos';               color = 'primary'; }
    else if (tipo === 'cumplidos') { lista = datos.filter(d => d.pct >= 100);                       titulo = 'Períodos Cumplidos al 100%';        color = 'success'; }
    else if (tipo === 'objetivo')  { lista = [...datos].sort((a,b) => b.objetivo_total - a.objetivo_total); titulo = 'Por Mayor Objetivo';        color = 'info';    }
    else if (tipo === 'promedio')  { lista = [...datos].sort((a,b) => b.pct - a.pct);               titulo = 'Por Mayor Cumplimiento';            color = 'warning'; }

    if (!lista.length) {
        wrapper.innerHTML = `<div class="card border-0 shadow-sm"><div class="card-body text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin datos para "${titulo}".</div></div>`;
        return;
    }

    const filas = lista.map(d => {
        const barColor = d.pct >= 100 ? '#22c55e' : d.pct >= 60 ? '#f59e0b' : '#ef4444';
        const badge    = d.pct >= 100
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Cumplido</span>`
            : `<span class="badge bg-danger-subtle text-danger border">No cumplido</span>`;
        return `
        <tr style="border-bottom:1px solid #f1f5f9;">
          <td style="padding:.6rem .8rem;font-weight:700;">${d.label}</td>
          <td style="padding:.6rem .8rem;text-align:right;">${d.total_metas}</td>
          <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${d.objetivo_total.toLocaleString()}</td>
          <td style="padding:.6rem .8rem;text-align:right;font-weight:600;color:${barColor};">${d.alcanzado_total.toLocaleString()}</td>
          <td style="padding:.6rem .8rem;min-width:160px;">
            <div style="display:flex;align-items:center;gap:.5rem;">
              <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:${Math.min(d.pct,100)}%;background:${barColor};border-radius:99px;"></div>
              </div>
              <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${barColor};">${d.pct.toFixed(1)}%</span>
            </div>
          </td>
          <td style="padding:.6rem .8rem;">${badge}</td>
        </tr>`;
    }).join('');

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
          <h6 class="mb-0 text-${color} d-flex align-items-center gap-2">
            <i class="bi bi-clock-history"></i>${titulo}
            <span class="badge bg-${color} bg-opacity-10 text-${color} ms-1">${lista.length} período${lista.length!==1?'s':''}</span>
          </h6>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr><th>Período</th><th class="text-end">Metas</th><th class="text-end">Objetivo</th><th class="text-end">Alcanzado</th><th>Progreso</th><th>Estado</th></tr>
            </thead>
            <tbody>${filas}</tbody>
          </table>
        </div>
      </div>
    </div>`;
}

// ─── Gráfico ──────────────────────────────────────────────────
function renderGraficoHist(datos) {
    const wrapper = document.getElementById('graficoWrapperHist');
    if (!wrapper) return;

    if (!tiposActivosHist.size || !datos.length) {
        destruirGraficoHist();
        wrapper.innerHTML = `<div class="rp-chart-empty"><i class="bi bi-bar-chart" style="font-size:3rem;color:#cbd5e1;"></i><p class="rp-chart-empty-text">Seleccione al menos una métrica para visualizar</p></div>`;
        document.getElementById('tituloGraficoHist').textContent = '';
        return;
    }

    if (!wrapper.querySelector('canvas')) wrapper.innerHTML = '<canvas id="canvasGraficoHist"></canvas>';
    destruirGraficoHist();

    const ctx    = wrapper.querySelector('canvas').getContext('2d');
    const labels = datos.map(d => d.label);
    const esBar  = tipoGraficoHist === 'bar';
    const datasets = [];

    if (tiposActivosHist.has('objetivo')) {
        datasets.push({
            label: 'Objetivo Total', data: datos.map(d => d.objetivo_total),
            borderColor: colorBordeHist('objetivo'),
            backgroundColor: colorFillHist('objetivo', esBar ? 0.55 : 0.2),
            borderWidth: esBar ? 0 : 2.5, borderRadius: esBar ? 5 : 0,
            tension: 0.4, fill: !esBar, type: tipoGraficoHist,
            pointRadius: 5, pointBorderColor:'#fff', pointBorderWidth:2, yAxisID:'y',
        });
    }
    if (tiposActivosHist.has('alcanzado')) {
        datasets.push({
            label: 'Alcanzado Total', data: datos.map(d => d.alcanzado_total),
            borderColor: colorBordeHist('alcanzado'),
            backgroundColor: colorFillHist('alcanzado', esBar ? 0.65 : 0.2),
            borderWidth: esBar ? 0 : 2.5, borderRadius: esBar ? 5 : 0,
            tension: 0.4, fill: !esBar, type: tipoGraficoHist,
            pointRadius: 5, pointBorderColor:'#fff', pointBorderWidth:2, yAxisID:'y',
        });
    }
    if (tiposActivosHist.has('cumplimiento')) {
        datasets.push({
            label: '% Cumplimiento', data: datos.map(d => d.pct),
            borderColor: colorBordeHist('cumplimiento'),
            backgroundColor: colorFillHist('cumplimiento', 0.15),
            borderWidth: 2.5, fill: false, type: 'line', tension: 0.4,
            pointRadius: 5, pointBackgroundColor: colorBordeHist('cumplimiento'),
            pointBorderColor:'#fff', pointBorderWidth:2, yAxisID:'yPct',
        });
    }

    const titulo = vistaActivaHist === 'anual'
        ? `Evolución Anual — ${datos[0]?.label} al ${datos[datos.length-1]?.label}`
        : `Evolución Trimestral ${anioHistTrim}`;
    document.getElementById('tituloGraficoHist').textContent = titulo;

    const escalas = {
        y: { beginAtZero:true, position:'left', grid:{ color:'rgba(0,0,0,0.07)' }, title:{ display:true, text:'Participantes' } }
    };
    if (tiposActivosHist.has('cumplimiento')) {
        escalas.yPct = { beginAtZero:true, max:130, position:'right', grid:{ drawOnChartArea:false }, title:{ display:true, text:'% Cumplimiento' } };
    }

    graficoHistInstance = new Chart(ctx, {
        type: tipoGraficoHist,
        data: { labels, datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display:true, position:'top' },
                tooltip: { mode:'index', intersect:false,
                    callbacks: { label: c => {
                        const v = c.parsed.y;
                        return `${c.dataset.label}: ${v !== null ? (c.dataset.yAxisID==='yPct' ? v.toFixed(1)+'%' : v.toLocaleString()) : 'Sin dato'}`;
                    }}
                }
            },
            scales: escalas,
            interaction: { intersect:false, mode:'index' }
        }
    });
}

// ─── Tabla ────────────────────────────────────────────────────
function renderTablaHist(datos) {
    const container = document.getElementById('histTablaContainer');
    if (!container) return;

    if (!datos.length) {
        container.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin datos para este período.</div>`;
        return;
    }

    const filas = datos.map(d => {
        const barColor = d.pct >= 100 ? '#22c55e' : d.pct >= 60 ? '#f59e0b' : '#ef4444';
        const badge    = d.pct >= 100
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Cumplido</span>`
            : d.pct <= 0
                ? `<span class="badge bg-secondary-subtle text-secondary border">Sin iniciar</span>`
                : `<span class="badge bg-warning-subtle text-warning border">En progreso</span>`;
        return `
        <tr>
          <td style="padding:.6rem .8rem;font-weight:700;">${d.label}</td>
          <td style="padding:.6rem .8rem;text-align:center;">${d.total_metas}</td>
          <td style="padding:.6rem .8rem;text-align:right;font-weight:600;">${d.objetivo_total.toLocaleString()}</td>
          <td style="padding:.6rem .8rem;text-align:right;font-weight:600;color:${barColor};">${d.alcanzado_total.toLocaleString()}</td>
          <td style="padding:.6rem .8rem;min-width:180px;">
            <div style="display:flex;align-items:center;gap:.5rem;">
              <div style="flex:1;height:6px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:${Math.min(d.pct,100)}%;background:${barColor};border-radius:99px;"></div>
              </div>
              <span style="font-family:monospace;font-size:.75rem;font-weight:700;color:${barColor};">${d.pct.toFixed(1)}%</span>
            </div>
          </td>
          <td style="padding:.6rem .8rem;">${badge}</td>
          <td style="padding:.6rem .8rem;text-align:center;">
            <span class="badge bg-secondary-subtle text-secondary border">${d.metas_cumplidas}/${d.total_metas}</span>
          </td>
        </tr>`;
    }).join('');

    container.innerHTML = `
    <table class="table table-striped table-sm align-middle">
      <thead class="table-light">
        <tr><th>Período</th><th class="text-center">Metas</th><th class="text-end">Objetivo</th><th class="text-end">Alcanzado</th><th>Progreso</th><th>Estado</th><th class="text-center">Cumplidas</th></tr>
      </thead>
      <tbody>${filas}</tbody>
    </table>`;
}

// ─── Controles del gráfico ────────────────────────────────────
function inicializarControlesHist(datos) {
    document.querySelectorAll('.btn-tipo-hist').forEach(btn => {
        const c = btn.cloneNode(true); btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-tipo-hist').forEach(btn => {
        btn.classList.toggle('active', tiposActivosHist.has(btn.dataset.tipo));
        btn.addEventListener('click', () => {
            const t = btn.dataset.tipo;
            tiposActivosHist.has(t) ? tiposActivosHist.delete(t) : tiposActivosHist.add(t);
            btn.classList.toggle('active', tiposActivosHist.has(t));
            renderGraficoHist(datos);
        });
    });

    const btnLT = document.getElementById('btnLimpiarTiposHist');
    if (btnLT) {
        const c = btnLT.cloneNode(true); btnLT.parentNode.replaceChild(c, btnLT);
        document.getElementById('btnLimpiarTiposHist').addEventListener('click', () => {
            tiposActivosHist.clear();
            document.querySelectorAll('.btn-tipo-hist').forEach(b => b.classList.remove('active'));
            renderGraficoHist(datos);
        });
    }

    document.querySelectorAll('.btn-chart-type-hist').forEach(btn => {
        const c = btn.cloneNode(true); btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-chart-type-hist').forEach(btn => {
        const activo = btn.dataset.tipo === tipoGraficoHist;
        btn.classList.toggle('active', activo);
        btn.classList.toggle('btn-secondary', activo);
        btn.classList.toggle('btn-outline-secondary', !activo);
        btn.addEventListener('click', () => {
            tipoGraficoHist = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type-hist').forEach(b => {
                const a = b.dataset.tipo === tipoGraficoHist;
                b.classList.toggle('active', a);
                b.classList.toggle('btn-secondary', a);
                b.classList.toggle('btn-outline-secondary', !a);
            });
            renderGraficoHist(datos);
        });
    });
}

// ─── Cleanup ──────────────────────────────────────────────────
function limpiarRecursosHist() {
    destruirGraficoHist();
    cacheHistAnual   = null;
    cacheHistTrim    = {};
    panelActivoHist  = null;
    tipoMetaHist     = '';
    tiposActivosHist = new Set(['objetivo','alcanzado']);
    tipoGraficoHist  = 'bar';
    vistaActivaHist  = 'anual';
}

window.cargarHistoricoMetas = cargarHistoricoMetas;
window.limpiarRecursosHist  = limpiarRecursosHist;

console.log('RE-Meta-Historico.js cargado');

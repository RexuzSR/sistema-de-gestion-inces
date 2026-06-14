// ============================================================
// RE-Meta-Detalle.js
// Función: cargarDetalleMeta
// API: api_reportes.php?accion=metas_por_ambito&anio=XXXX
//      api_reportes.php?accion=detalle_participantes_meta&id_meta=X
// ============================================================

const API_URL_DET = '../controlador/Reporte/api_reportes.php';

let graficoDetInstance   = null;
let cacheDetMetas        = {};   // keyed por anio → lista de metas
let cacheDetParticipantes= {};   // keyed por id_meta → lista de participantes
let anioDetActual        = new Date().getFullYear();
let metaDetSeleccionada  = null;
let datosPartOriginales  = [];
let panelActivoDet       = null;
let tipoGraficoDet       = 'bar';

const COLOR_BASE_DET = {
    aprobados:   [34, 197,  94],
    enFormacion: [59, 130, 246],
    otros:       [156,163, 175],
};

const METAS_COLORES_AMBITO_DET = [
    { fill:'#3b82f6', light:'#eff6ff', border:'#bfdbfe', text:'#1d4ed8' },
    { fill:'#22c55e', light:'#f0fdf4', border:'#bbf7d0', text:'#15803d' },
    { fill:'#f59e0b', light:'#fffbeb', border:'#fde68a', text:'#b45309' },
    { fill:'#ef4444', light:'#fef2f2', border:'#fecaca', text:'#b91c1c' },
    { fill:'#8b5cf6', light:'#f5f3ff', border:'#ddd6fe', text:'#6d28d9' },
    { fill:'#0891b2', light:'#ecfeff', border:'#a5f3fc', text:'#0e7490' },
    { fill:'#ec4899', light:'#fdf2f8', border:'#fbcfe8', text:'#be185d' },
    { fill:'#10b981', light:'#ecfdf5', border:'#a7f3d0', text:'#047857' },
];

// ─── Helpers ─────────────────────────────────────────────────
function destruirGraficoDet() {
    if (graficoDetInstance?.destroy) { graficoDetInstance.destroy(); graficoDetInstance = null; }
}
function initTooltipsDet(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
}
function obtenerAniosDisponiblesDet() {
    const hoy = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => hoy - i);
}
function formatFechaDet(f) {
    if (!f) return '—';
    const d = new Date(f + 'T00:00:00');
    return d.toLocaleDateString('es-VE', { day:'2-digit', month:'short', year:'numeric' });
}

// ─── Fetch con caché ─────────────────────────────────────────
async function fetchDetMetas(anio) {
    if (cacheDetMetas[anio]) return cacheDetMetas[anio];
    const res = await fetch(`${API_URL_DET}?accion=metas_por_ambito&anio=${anio}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    cacheDetMetas[anio] = Array.isArray(data) ? data : [];
    return cacheDetMetas[anio];
}

async function fetchDetParticipantes(idMeta) {
    if (cacheDetParticipantes[idMeta]) return cacheDetParticipantes[idMeta];
    const res = await fetch(`${API_URL_DET}?accion=detalle_participantes_meta&id_meta=${idMeta}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    cacheDetParticipantes[idMeta] = Array.isArray(data) ? data : [];
    return cacheDetParticipantes[idMeta];
}

// ─── Procesado ────────────────────────────────────────────────
function procesarMetasDet(rawList, anio) {
    const ambitoIdx = {};
    let ci = 0;
    return rawList.map(m => {
        const obj  = parseInt(m.meta_objetivo)        || 0;
        const form = parseInt(m.estudiantes_formados)  || 0;
        const pct  = obj > 0 ? parseFloat(((form / obj) * 100).toFixed(2)) : 0;
        const amb  = m.nombre_ambito || 'Sin ámbito';
        if (!(amb in ambitoIdx)) ambitoIdx[amb] = ci++;
        const col  = METAS_COLORES_AMBITO_DET[ambitoIdx[amb] % METAS_COLORES_AMBITO_DET.length];
        let periodo = m.periodo_tiempo || '';
        if (!periodo && m.fecha_inicio && m.fecha_fin) {
            const a = new Date(m.fecha_inicio + 'T00:00:00'), b = new Date(m.fecha_fin + 'T00:00:00');
            periodo = `${a.toLocaleDateString('es-ES')} – ${b.toLocaleDateString('es-ES')}`;
        }
        if (!periodo) periodo = '—';
        return {
            id:                m.id_meta || null,
            nombre_ambito:     amb,
            tipo_objetivo:     m.tipo_objetivo || 'Aprobados',
            meta_objetivo:     obj,
            estudiantes_formados: form,
            pct,
            estado:            m.estado || 'Activa',
            periodo_tiempo:    periodo,
            fecha_inicio:      m.fecha_inicio,
            fecha_fin:         m.fecha_fin,
            color:             col,
        };
    });
}

function buildKPIsDet(participantes, meta) {
    const total       = participantes.length;
    const aprobados   = participantes.filter(p => p.estatus_participante === 'Aprobado').length;
    const enFormacion = participantes.filter(p => p.estatus_participante === 'En formación').length;
    const otros       = total - aprobados - enFormacion;
    return { total, aprobados, enFormacion, otros };
}

// ═══════════════════════════════════════════════════════════
//  FUNCIÓN PRINCIPAL
// ═══════════════════════════════════════════════════════════
async function cargarDetalleMeta() {

    if (typeof window.crearTabs === 'function') window.crearTabs(4, 17);

    const contentDiv = document.getElementById('reportContent');
    const hoy        = new Date().getFullYear();
    const aniosDisp  = obtenerAniosDisponiblesDet();

    destruirGraficoDet();
    cacheDetMetas         = {};
    cacheDetParticipantes = {};
    anioDetActual         = hoy;
    metaDetSeleccionada   = null;
    datosPartOriginales   = [];
    panelActivoDet        = null;
    tipoGraficoDet        = 'bar';

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Detalle de Participantes por Meta</h4>
      </div>
      <div class="card-body">

        <!-- Selector -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-end gap-3">
              <div>
                <label class="rp-toolbar-label mb-2">Año</label>
                <select id="detAnioSelect" class="form-select form-select-sm" style="width:100px;">
                  ${aniosDisp.map(a=>`<option value="${a}" ${a===hoy?'selected':''}>${a}</option>`).join('')}
                </select>
              </div>
              <div style="flex:1 1 280px;">
                <label class="rp-toolbar-label mb-2">Meta</label>
                <select id="detMetaSelect" class="form-select form-select-sm" disabled>
                  <option value="">— Cargando metas... —</option>
                </select>
              </div>
              <div>
                <button id="detVerBtn" class="btn btn-sm btn-danger" disabled>
                  <i class="bi bi-search me-1"></i>Ver Detalle
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading / Error -->
        <div id="loadingDet" class="text-center py-5" style="display:none;">
          <div class="spinner-border text-danger" role="status"></div>
          <p class="mt-3 text-muted" id="loadingMsgDet">Cargando participantes...</p>
        </div>
        <div id="errorDet" class="alert alert-danger d-none">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <span id="errorMsgDet">Error al cargar los datos.</span>
        </div>

        <!-- Estado vacío -->
        <div id="detVacio" class="text-center py-5 text-muted">
          <i class="bi bi-bullseye" style="font-size:3.5rem;color:#e2e8f0;"></i>
          <p class="mt-3">Selecciona un año y una meta para ver los participantes.</p>
        </div>

        <!-- Contenido detalle -->
        <div id="contenidoDet" style="display:none;">
          <div id="kpiCardsDet"></div>
          <div id="panelDetalleWrapperDet" style="display:none;" class="mb-3"></div>

          <!-- Ficha de la meta -->
          <div id="detFichaMeta" class="mb-4"></div>

          <!-- Toolbar tabla + gráfico -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
              <div class="d-flex flex-wrap align-items-center gap-3">
                <div style="flex:1 1 auto;">
                  <input id="detBuscador" type="text" class="form-control form-control-sm"
                         placeholder="Buscar por nombre o cédula..." style="max-width:280px;">
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-secondary btn-chart-type-det active" data-tipo="bar">
                    <i class="bi bi-bar-chart-fill me-1"></i>Torres
                  </button>
                  <button class="btn btn-sm btn-outline-secondary btn-chart-type-det" data-tipo="doughnut">
                    <i class="bi bi-pie-chart me-1"></i>Dona
                  </button>
                </div>
                <button id="detExportBtn" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-file-earmark-arrow-down me-1"></i>CSV
                </button>
              </div>
            </div>
          </div>

          <!-- Gráfico distribución -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h6 class="card-title mb-3 text-muted fw-semibold">Distribución por Estatus</h6>
              <div id="graficoWrapperDet" class="rp-chart-wrap" style="max-height:260px;"></div>
            </div>
          </div>

          <!-- Tabla participantes -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-3" id="detTablaTitulo">Participantes</h5>
              <div id="detTablaContainer"></div>
              <div class="mt-2">
                <small id="detContador" class="text-muted"></small>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>`;

    initTooltipsDet(contentDiv);

    // Cargar metas del año inicial
    await _cargarListaMetas(hoy);

    document.getElementById('detAnioSelect')?.addEventListener('change', async function () {
        anioDetActual       = parseInt(this.value);
        metaDetSeleccionada = null;
        document.getElementById('contenidoDet').style.display = 'none';
        document.getElementById('detVacio').style.display     = 'block';
        datosPartOriginales = [];
        await _cargarListaMetas(anioDetActual);
    });

    document.getElementById('detMetaSelect')?.addEventListener('change', function () {
        const btn = document.getElementById('detVerBtn');
        if (btn) btn.disabled = !this.value;
    });

    document.getElementById('detVerBtn')?.addEventListener('click', async () => {
        const sel   = document.getElementById('detMetaSelect');
        const idMeta= sel?.value;
        if (!idMeta) return;
        const metas = procesarMetasDet(cacheDetMetas[anioDetActual] || [], anioDetActual);
        metaDetSeleccionada = metas.find(m => String(m.id) === String(idMeta)) || null;
        await _cargarDetalle(idMeta);
    });

    document.getElementById('detBuscador')?.addEventListener('input', function () {
        _filtrarParticipantes(this.value);
    });

    document.getElementById('detExportBtn')?.addEventListener('click', _exportarCSV);
}

// ─── Cargar lista de metas en el select ───────────────────────
async function _cargarListaMetas(anio) {
    const sel = document.getElementById('detMetaSelect');
    const btn = document.getElementById('detVerBtn');
    if (!sel) return;

    sel.disabled = true;
    sel.innerHTML = `<option value="">Cargando metas de ${anio}...</option>`;
    if (btn) btn.disabled = true;

    try {
        const raw   = await fetchDetMetas(anio);
        const metas = procesarMetasDet(raw, anio);

        if (!metas.length) {
            sel.innerHTML = `<option value="">Sin metas para ${anio}</option>`;
            return;
        }

        sel.innerHTML = `<option value="">— Selecciona una meta —</option>` +
            metas.map(m =>
                `<option value="${m.id}">${m.periodo_tiempo} | ${m.nombre_ambito} (${m.tipo_objetivo}) — ${m.pct.toFixed(1)}%</option>`
            ).join('');
        sel.disabled = false;

    } catch (err) {
        sel.innerHTML = `<option value="">Error al cargar metas: ${err.message}</option>`;
        console.error('[RE-Meta-Detalle] Error cargando metas:', err);
    }
}

// ─── Cargar participantes de la meta seleccionada ─────────────
async function _cargarDetalle(idMeta) {
    const loadEl   = document.getElementById('loadingDet');
    const errorEl  = document.getElementById('errorDet');
    const contenEl = document.getElementById('contenidoDet');
    const vacioEl  = document.getElementById('detVacio');

    if (loadEl)   loadEl.style.display   = 'block';
    if (errorEl)  errorEl.classList.add('d-none');
    if (contenEl) contenEl.style.display = 'none';
    if (vacioEl)  vacioEl.style.display  = 'none';

    const msgEl = document.getElementById('loadingMsgDet');
    if (msgEl) msgEl.textContent = 'Cargando participantes...';

    try {
        const participantes = await fetchDetParticipantes(idMeta);
        datosPartOriginales = participantes;

        if (loadEl)   loadEl.style.display   = 'none';
        if (contenEl) contenEl.style.display = 'block';

        renderFichaMeta(metaDetSeleccionada);
        const kpis = buildKPIsDet(participantes, metaDetSeleccionada);
        renderKPIsDet(kpis, participantes);
        renderGraficoDet(kpis);
        renderTablaDet(participantes);
        inicializarControlesDetGrafico(kpis);
        if (panelActivoDet) await renderPanelDetalleDetAsync(panelActivoDet, participantes);

    } catch (err) {
        if (loadEl)  loadEl.style.display = 'none';
        if (vacioEl) vacioEl.style.display= 'block';
        if (errorEl) {
            errorEl.classList.remove('d-none');
            document.getElementById('errorMsgDet').textContent = `Error: ${err.message}`;
        }
        console.error('[RE-Meta-Detalle] Error cargando participantes:', err);
    }
}

// ─── Ficha de la meta ─────────────────────────────────────────
function renderFichaMeta(meta) {
    const el = document.getElementById('detFichaMeta');
    if (!el || !meta) return;

    const barColor = meta.pct >= 100 ? '#22c55e' : meta.pct >= 60 ? '#f59e0b' : '#ef4444';
    const badgeColor = meta.pct >= 100 ? 'success' : meta.pct >= 60 ? 'warning' : 'danger';

    el.innerHTML = `
    <div class="card border-0 shadow-sm" style="border-left:4px solid ${barColor} !important;">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">
          <div class="col-md-5">
            <div class="text-muted small mb-1">Meta seleccionada</div>
            <div class="fw-bold fs-5">${meta.periodo_tiempo}</div>
            <div class="mt-1 d-flex gap-2 flex-wrap">
              <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.22em .7em;border-radius:6px;
                           font-weight:600;background:${meta.color.light};color:${meta.color.text};
                           border:1px solid ${meta.color.border};font-size:.72rem;">
                ${meta.nombre_ambito}
              </span>
              <span class="badge bg-secondary-subtle text-secondary border">${meta.tipo_objetivo}</span>
              <span class="badge ${meta.estado==='Activa'?'bg-success':'bg-secondary'}">${meta.estado}</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex gap-4">
              <div class="text-center">
                <div class="text-muted small">Objetivo</div>
                <div class="fw-bold fs-4 text-secondary">${meta.meta_objetivo.toLocaleString()}</div>
              </div>
              <div class="text-center">
                <div class="text-muted small">Alcanzado</div>
                <div class="fw-bold fs-4" style="color:${barColor};">${meta.estudiantes_formados.toLocaleString()}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="text-muted small">Progreso</span>
              <span class="badge bg-${badgeColor} bg-opacity-10 text-${badgeColor} ms-auto">${meta.pct.toFixed(1)}%</span>
            </div>
            <div style="height:8px;background:#e2e8f0;border-radius:99px;overflow:hidden;">
              <div style="height:100%;width:${Math.min(meta.pct,100)}%;background:${barColor};border-radius:99px;transition:width .4s;"></div>
            </div>
            <div class="text-muted small mt-1">
              <i class="bi bi-calendar-range me-1"></i>
              ${formatFechaDet(meta.fecha_inicio)} → ${formatFechaDet(meta.fecha_fin)}
            </div>
          </div>
        </div>
      </div>
    </div>`;
}

// ─── KPI Cards ────────────────────────────────────────────────
function renderKPIsDet(kpis, participantes) {
    const el = document.getElementById('kpiCardsDet');
    if (!el) return;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    const cards = [
        { tipo:'total',       valor: kpis.total,       label:'Total Participantes', sub:'en esta meta',                                        color:'primary', icon:'bi-people-fill'  },
        { tipo:'aprobados',   valor: kpis.aprobados,   label:'Aprobados',           sub:`${kpis.total>0?((kpis.aprobados/kpis.total)*100).toFixed(1):0}% del total`, color:'success', icon:'bi-patch-check'  },
        { tipo:'enFormacion', valor: kpis.enFormacion, label:'En Formación',         sub:`${kpis.total>0?((kpis.enFormacion/kpis.total)*100).toFixed(1):0}% del total`, color:'info',    icon:'bi-person-check' },
        { tipo:'otros',       valor: kpis.otros,       label:'Otros / Retirados',    sub:`${kpis.total>0?((kpis.otros/kpis.total)*100).toFixed(1):0}% del total`,      color:'secondary',icon:'bi-person-dash' },
    ];

    el.innerHTML = `<div class="row mb-1">${cards.map(c => `
        <div class="col-md-3">
          <div class="card border-0 shadow-sm mb-3 kpi-card kpi-card-det" role="button" data-tipo="${c.tipo}">
            <div class="card-body text-center py-3">
              <i class="bi ${c.icon} fs-2 text-${c.color}"></i>
              <h2 class="fw-bold text-${c.color} mt-2 mb-0">${c.valor.toLocaleString()}</h2>
              <p class="rp-kpi-card-label">${c.label}</p>
              <p class="rp-kpi-card-sub">${c.sub}</p>
              <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small" id="badgePanelDet-${c.tipo}">
                ${svgDown}Ver detalles
              </span>
            </div>
          </div>
        </div>`).join('')}</div>`;

    el.querySelectorAll('.kpi-card-det').forEach(card =>
        card.addEventListener('click', () => togglePanelDetalleDet(card.dataset.tipo, participantes))
    );
}

// ─── Panel colapsable ─────────────────────────────────────────
async function togglePanelDetalleDet(tipo, participantes) {
    const wrapper = document.getElementById('panelDetalleWrapperDet');
    if (!wrapper) return;
    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['total','aprobados','enFormacion','otros'].forEach(t => {
        const b = document.getElementById(`badgePanelDet-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalles`;
    });

    if (panelActivoDet === tipo) {
        wrapper.style.display = 'none';
        panelActivoDet = null;
        return;
    }

    panelActivoDet = tipo;
    wrapper.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary"></div></div>`;
    wrapper.style.display = 'block';
    await renderPanelDetalleDetAsync(tipo, participantes);

    const badge = document.getElementById(`badgePanelDet-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;
    setTimeout(() => wrapper.scrollIntoView({ behavior:'smooth', block:'center' }), 50);
}

async function renderPanelDetalleDetAsync(tipo, participantes) {
    const wrapper = document.getElementById('panelDetalleWrapperDet');
    if (!wrapper) return;

    let lista = [], titulo = '', color = '';
    if      (tipo === 'total')       { lista = participantes;                                                          titulo = 'Todos los Participantes'; color = 'primary';   }
    else if (tipo === 'aprobados')   { lista = participantes.filter(p => p.estatus_participante === 'Aprobado');       titulo = 'Aprobados';               color = 'success';   }
    else if (tipo === 'enFormacion') { lista = participantes.filter(p => p.estatus_participante === 'En formación');   titulo = 'En Formación';            color = 'info';      }
    else if (tipo === 'otros')       { lista = participantes.filter(p => !['Aprobado','En formación'].includes(p.estatus_participante)); titulo = 'Otros / Retirados'; color = 'secondary'; }

    if (!lista.length) {
        wrapper.innerHTML = `<div class="card border-0 shadow-sm"><div class="card-body text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin participantes en "${titulo}".</div></div>`;
        return;
    }

    const esAprobados = metaDetSeleccionada?.tipo_objetivo === 'Aprobados';
    const filas = lista.slice(0, 30).map((p, i) => {
        const estatusBadge = p.estatus_participante === 'Aprobado'
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Aprobado</span>`
            : p.estatus_participante === 'En formación'
                ? `<span class="badge bg-info-subtle text-info border">En formación</span>`
                : `<span class="badge bg-secondary-subtle text-secondary border">${p.estatus_participante || '—'}</span>`;
        const fecha = esAprobados
            ? formatFechaDet(p.fecha_aprobacion)
            : formatFechaDet(p.fecha_inscripcion);
        return `
        <tr style="border-bottom:1px solid #f1f5f9;">
          <td style="padding:.5rem .8rem;color:#94a3b8;font-size:.75rem;">${i+1}</td>
          <td style="padding:.5rem .8rem;font-weight:600;">${p.nombre_completo || '—'}</td>
          <td style="padding:.5rem .8rem;"><span class="badge bg-light text-dark border">${p.cedula || '—'}</span></td>
          <td style="padding:.5rem .8rem;font-size:.8rem;color:#64748b;">${p.nombre_curso || '—'}</td>
          <td style="padding:.5rem .8rem;text-align:center;">${estatusBadge}</td>
          <td style="padding:.5rem .8rem;font-size:.8rem;color:#64748b;">${fecha}</td>
        </tr>`;
    }).join('');

    const aviso = lista.length > 30 ? `<div class="text-center py-2 text-muted small">Mostrando 30 de ${lista.length} resultados. Usa el buscador para filtrar.</div>` : '';

    wrapper.innerHTML = `
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
          <h6 class="mb-0 text-${color} d-flex align-items-center gap-2">
            <i class="bi bi-people-fill"></i>${titulo}
            <span class="badge bg-${color} bg-opacity-10 text-${color} ms-1">${lista.length}</span>
          </h6>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr><th>#</th><th>Nombre</th><th>Cédula</th><th>Curso</th><th>Estatus</th><th>${esAprobados?'F. Aprobación':'F. Inscripción'}</th></tr>
            </thead>
            <tbody>${filas}</tbody>
          </table>
        </div>
        ${aviso}
      </div>
    </div>`;
}

// ─── Gráfico distribución ─────────────────────────────────────
function renderGraficoDet(kpis) {
    const wrapper = document.getElementById('graficoWrapperDet');
    if (!wrapper) return;
    if (!wrapper.querySelector('canvas')) wrapper.innerHTML = '<canvas id="canvasGraficoDet"></canvas>';
    destruirGraficoDet();

    const ctx  = wrapper.querySelector('canvas').getContext('2d');
    const data = [kpis.aprobados, kpis.enFormacion, kpis.otros];
    const labels = ['Aprobados', 'En Formación', 'Otros / Retirados'];
    const colors = [
        { bg:'rgba(34,197,94,0.7)',  border:'#22c55e' },
        { bg:'rgba(59,130,246,0.7)', border:'#3b82f6' },
        { bg:'rgba(156,163,175,0.7)',border:'#9ca3af' },
    ];

    if (tipoGraficoDet === 'doughnut') {
        graficoDetInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.map(c => c.bg),
                    borderColor:     colors.map(c => c.border),
                    borderWidth: 2,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { position:'right', labels:{ boxWidth:12 } },
                    tooltip: { callbacks: { label: c => ` ${c.label}: ${c.parsed} (${kpis.total>0?((c.parsed/kpis.total)*100).toFixed(1):0}%)` } }
                }
            }
        });
    } else {
        graficoDetInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Participantes',
                    data,
                    backgroundColor: colors.map(c => c.bg),
                    borderColor:     colors.map(c => c.border),
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display:false },
                    tooltip: { callbacks: { label: c => ` ${c.parsed.y} participantes (${kpis.total>0?((c.parsed.y/kpis.total)*100).toFixed(1):0}%)` } }
                },
                scales: {
                    y: { beginAtZero:true, grid:{ color:'rgba(0,0,0,0.07)' }, title:{ display:true, text:'Participantes' } },
                    x: { grid: { display:false } }
                }
            }
        });
    }
}

// ─── Tabla participantes ──────────────────────────────────────
function renderTablaDet(participantes) {
    const container = document.getElementById('detTablaContainer');
    const contador  = document.getElementById('detContador');
    if (!container) return;

    document.getElementById('detTablaTitulo').textContent =
        `Participantes — ${metaDetSeleccionada?.nombre_ambito || ''} ${metaDetSeleccionada?.periodo_tiempo || ''}`;

    if (!participantes.length) {
        container.innerHTML = `<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin participantes registrados en esta meta.</div>`;
        if (contador) contador.textContent = '0 resultados';
        return;
    }

    const esAprobados = metaDetSeleccionada?.tipo_objetivo === 'Aprobados';
    const filas = participantes.map((p, i) => {
        const estatusBadge = p.estatus_participante === 'Aprobado'
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Aprobado</span>`
            : p.estatus_participante === 'En formación'
                ? `<span class="badge bg-info-subtle text-info border">En formación</span>`
                : `<span class="badge bg-secondary-subtle text-secondary border">${p.estatus_participante || '—'}</span>`;
        const fecha = esAprobados
            ? formatFechaDet(p.fecha_aprobacion)
            : formatFechaDet(p.fecha_inscripcion);
        return `
        <tr data-nombre="${(p.nombre_completo||'').toLowerCase()}" data-cedula="${p.cedula||''}">
          <td style="padding:.5rem .8rem;color:#94a3b8;font-size:.75rem;">${i+1}</td>
          <td style="padding:.5rem .8rem;font-weight:600;">${p.nombre_completo || '—'}</td>
          <td style="padding:.5rem .8rem;"><span class="badge bg-light text-dark border">${p.cedula || '—'}</span></td>
          <td style="padding:.5rem .8rem;font-size:.8rem;color:#64748b;">${p.nombre_curso || '—'}</td>
          <td style="padding:.5rem .8rem;text-align:center;">${estatusBadge}</td>
          <td style="padding:.5rem .8rem;font-size:.8rem;color:#64748b;">${fecha}</td>
        </tr>`;
    }).join('');

    container.innerHTML = `
    <div class="table-responsive">
      <table class="table table-striped table-sm align-middle" id="detTablaEl">
        <thead class="table-light">
          <tr><th>#</th><th>Nombre</th><th>Cédula</th><th>Curso</th><th class="text-center">Estatus</th><th>${esAprobados?'F. Aprobación':'F. Inscripción'}</th></tr>
        </thead>
        <tbody>${filas}</tbody>
      </table>
    </div>`;

    if (contador) contador.textContent = `${participantes.length} participante${participantes.length!==1?'s':''}`;
}

// ─── Búsqueda ─────────────────────────────────────────────────
function _filtrarParticipantes(q) {
    const txt = (q || '').toLowerCase().trim();
    const tbody = document.querySelector('#detTablaEl tbody');
    if (!tbody) return;
    let visibles = 0;
    tbody.querySelectorAll('tr').forEach(tr => {
        const ok = !txt || tr.dataset.nombre?.includes(txt) || tr.dataset.cedula?.includes(txt);
        tr.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });
    const cont = document.getElementById('detContador');
    if (cont) cont.textContent = `${visibles} participante${visibles!==1?'s':''}`;
}

// ─── Exportar CSV ─────────────────────────────────────────────
function _exportarCSV() {
    if (!datosPartOriginales.length || !metaDetSeleccionada) return;
    const esAprobados = metaDetSeleccionada.tipo_objetivo === 'Aprobados';
    const headers = ['#','Nombre','Cédula','Curso','Estatus', esAprobados ? 'F. Aprobación' : 'F. Inscripción'];
    const filas   = datosPartOriginales.map((p, i) => [
        i+1,
        `"${(p.nombre_completo||'').replace(/"/g,'""')}"`,
        p.cedula || '',
        `"${(p.nombre_curso||'').replace(/"/g,'""')}"`,
        p.estatus_participante || '',
        esAprobados ? (p.fecha_aprobacion||'') : (p.fecha_inscripcion||''),
    ].join(','));

    const csv  = [headers.join(','), ...filas].join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type:'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `Meta_${metaDetSeleccionada.periodo_tiempo || metaDetSeleccionada.id}_participantes.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// ─── Controles del gráfico ────────────────────────────────────
function inicializarControlesDetGrafico(kpis) {
    document.querySelectorAll('.btn-chart-type-det').forEach(btn => {
        const c = btn.cloneNode(true); btn.parentNode.replaceChild(c, btn);
    });
    document.querySelectorAll('.btn-chart-type-det').forEach(btn => {
        const activo = btn.dataset.tipo === tipoGraficoDet;
        btn.classList.toggle('active', activo);
        btn.classList.toggle('btn-secondary', activo);
        btn.classList.toggle('btn-outline-secondary', !activo);
        btn.addEventListener('click', () => {
            tipoGraficoDet = btn.dataset.tipo;
            document.querySelectorAll('.btn-chart-type-det').forEach(b => {
                const a = b.dataset.tipo === tipoGraficoDet;
                b.classList.toggle('active', a);
                b.classList.toggle('btn-secondary', a);
                b.classList.toggle('btn-outline-secondary', !a);
            });
            renderGraficoDet(kpis);
        });
    });
}

// ─── Cleanup ──────────────────────────────────────────────────
function limpiarRecursosDet() {
    destruirGraficoDet();
    cacheDetMetas         = {};
    cacheDetParticipantes = {};
    metaDetSeleccionada   = null;
    datosPartOriginales   = [];
    panelActivoDet        = null;
    tipoGraficoDet        = 'bar';
}

window.cargarDetalleMeta  = cargarDetalleMeta;
window.limpiarRecursosDet = limpiarRecursosDet;

console.log('RE-Meta-Detalle.js cargado');

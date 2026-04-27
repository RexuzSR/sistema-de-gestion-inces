// ==================== RE-Curso-Trayectoria.js ====================
// Reporte: Trayectoria de Cursos
// ==================================================================

// ── Estado global ──────────────────────────────────────────────────
let curTrayDatosTabla      = [];
let curTrayPaginaActual    = 1;
let curTrayFilasPorPagina  = 10;
let curTrayOrdenCol        = null;
let curTrayOrdenAsc        = true;
let curTrayBusqueda        = '';
let curTrayCursoAct        = null;
let curTrayFiltros         = { anio:'', modalidad:'', estatus:'' };

const API_CUR_TRAY_URL = '../controlador/Reporte/api_reportes.php';

// ── Helpers ────────────────────────────────────────────────────────

function curTrayFecha(f) {
    if (!f || f === '0000-00-00') return '—';
    const [y, m, d] = f.split('-');
    if (!y || !m || !d) return f;
    return `${d.padStart(2,'0')}/${m.padStart(2,'0')}/${y}`;
}

function curTrayBadgeEstatus(est) {
    const map = {
        'En espera':  { bg:'#e7f0ff', border:'#0d6efd55', text:'#0a4fc4' },
        'En proceso': { bg:'#fff8e1', border:'#ffc10755', text:'#856404' },
        'Culminado':  { bg:'#e9f7ef', border:'#19875455', text:'#0f6840' },
        'Diferido':   { bg:'#fdecea', border:'#dc354555', text:'#a71d2a' },
        'Inactivo':   { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' },
    };
    const s = map[est] ?? { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' };
    return `<span style="background:${s.bg};border:1.5px solid ${s.border};color:${s.text};
                font-size:.72rem;font-weight:700;padding:.28em .75em;border-radius:999px;
                white-space:nowrap;display:inline-block;">${est ?? '—'}</span>`;
}

function curTrayBadgeParticipante(est) {
    const map = {
        'Aprobado':     { bg:'#e9f7ef', border:'#19875455', text:'#0f6840' },
        'En formación': { bg:'#e7f0ff', border:'#0d6efd55', text:'#0a4fc4' },
        'Pendiente':    { bg:'#fff8e1', border:'#ffc10755', text:'#856404' },
        'Reprobado':    { bg:'#fdecea', border:'#dc354555', text:'#a71d2a' },
        'Ausente':      { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' },
    };
    const s = map[est] ?? { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' };
    return `<span style="background:${s.bg};border:1.5px solid ${s.border};color:${s.text};
                font-size:.7rem;font-weight:700;padding:.22em .65em;border-radius:999px;
                white-space:nowrap;display:inline-block;">${est ?? '—'}</span>`;
}

function curTrayMostrarError(el, msg) {
    if (!el) return;
    el.innerHTML = `<div class="alert alert-danger mt-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error:</strong> ${msg}
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-danger"
                    onclick="cargarTrayectoriaCursos()">
                <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
            </button>
        </div>
    </div>`;
}

// ── Entrada principal ──────────────────────────────────────────────

async function cargarTrayectoriaCursos() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 2;
    const cat = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color:'success' };

    curTrayDatosTabla      = [];
    curTrayPaginaActual    = 1;
    curTrayOrdenCol        = null;
    curTrayOrdenAsc        = true;
    curTrayBusqueda        = '';
    curTrayCursoAct        = null;
    curTrayFiltros         = { anio:'', modalidad:'', estatus:'' };

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${cat.color} text-white">
            <h4 class="mb-0">
                <i class="bi bi-graph-up-arrow me-2"></i>Trayectoria de Cursos
            </h4>
        </div>
        <div class="card-body">

            <!-- KPI Cards -->
            <div id="curTrayKpiCards" class="row mb-4 g-3">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted small">Cargando resumen...</p>
                </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="d-flex justify-content-center mb-3">
                <div style="width:100%;max-width:560px;">
                    <div class="rp-search-group" id="curTraySearchGroup">
                        <button class="rp-search-icon-btn" type="button" tabindex="-1">
                            <i class="bi bi-search"></i>
                        </button>
                        <input type="text" class="rp-search-input" id="curTraySearchInput"
                               placeholder="Buscar por nombre de curso o código..."
                               autocomplete="off">
                    </div>
                </div>
            </div>

            <!-- Panel de filtros -->
            <div id="curTrayFiltrosPanel" class="mb-3">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;
                            padding:1rem 1.25rem;">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span style="font-size:.74rem;font-weight:700;text-transform:uppercase;
                                     letter-spacing:.06em;color:#475569;">
                            <i class="bi bi-funnel-fill me-1"></i>Filtros
                        </span>
                        <button class="btn btn-sm btn-outline-secondary"
                                id="curTrayBtnLimpiarFiltros" style="font-size:.75rem;">
                            <i class="bi bi-x-circle me-1"></i>Limpiar todos
                        </button>
                    </div>

                    <div class="row g-2 align-items-end">

                        <!-- Año -->
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Año de Inicio
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroAnio">
                                <option value="">Todos los años</option>
                            </select>
                        </div>

                        <!-- Modalidad -->
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Modalidad
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroModalidad">
                                <option value="">Todas</option>
                            </select>
                        </div>

                        <!-- Estatus -->
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Estatus
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroEstatus">
                                <option value="">Todos</option>
                            </select>
                        </div>

                    </div>

                    <!-- Chips de filtros activos -->
                    <div id="curTrayFiltrosActivos" class="d-flex flex-wrap gap-2 mt-3"
                         style="min-height:22px;"></div>
                </div>
            </div>

            <!-- Tabla -->
            <div id="curTrayTablaWrapper">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted small">Cargando cursos...</p>
                </div>
            </div>

            <!-- Panel detalle -->
            <div id="curTrayDetallePanel" style="display:none;" class="mt-4"></div>
        </div>
    </div>`;

    curTrayInicializarBusqueda();
    curTrayInicializarFiltros();

    try {
        const [kpiData, tablaData] = await Promise.all([
            fetch(`${API_CUR_TRAY_URL}?accion=trayectoria_cursos_resumen`)
                .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); }),
            fetch(`${API_CUR_TRAY_URL}?accion=trayectoria_cursos`)
                .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        ]);

        if (kpiData?.error)   throw new Error(kpiData.error);
        if (tablaData?.error) throw new Error(tablaData.error);

        curTrayDatosTabla = Array.isArray(tablaData) ? tablaData : [];

        // Poblar selects
        curTrayPoblarSelects(curTrayDatosTabla);

        curTrayRenderKPI(kpiData);
        curTrayRenderTabla();

    } catch (err) {
        curTrayMostrarError(document.getElementById('curTrayKpiCards'),     String(err));
        curTrayMostrarError(document.getElementById('curTrayTablaWrapper'), String(err));
    }
}

function curTrayPoblarSelects(datos) {
    const setAnios = new Set();
    const setMods  = new Set();
    const setEsts  = new Set();

    datos.forEach(c => {
        if (c.fecha_inicio) {
            const a = c.fecha_inicio.split('-')[0];
            if (a) setAnios.add(a);
        }
        if (c.nombre_modalidad) setMods.add(c.nombre_modalidad);
        if (c.estatus) setEsts.add(c.estatus);
    });

    const poblar = (id, items) => {
        const sel = document.getElementById(id);
        if (!sel) return;
        [...items].sort().forEach(it => {
            const opt = document.createElement('option');
            opt.value = it; opt.textContent = it; sel.appendChild(opt);
        });
    };

    poblar('curTrayFiltroAnio',      setAnios);
    poblar('curTrayFiltroModalidad', setMods);
    poblar('curTrayFiltroEstatus',   setEsts);
}

function curTrayRenderKPI(data) {
    const el = document.getElementById('curTrayKpiCards');
    if (!el) return;

    const cards = [
        { label:'Total Cursos', valor:data.total, icon:'bi-journal-text', color:'#0d6efd', bg:'#e7f0ff' },
        { label:'En Proceso',   valor:data.en_proceso, icon:'bi-play-circle', color:'#ffc107', bg:'#fff8e1' },
        { label:'Culminados',   valor:data.culminados, icon:'bi-check-circle', color:'#198754', bg:'#e9f7ef' },
        { label:'En Espera',    valor:data.en_espera, icon:'bi-clock-history', color:'#6c757d', bg:'#f0f0f0' }
    ];

    el.innerHTML = cards.map(c => `
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background:${c.bg};border-radius:16px;">
                <div class="card-body text-center p-4">
                    <i class="bi ${c.icon} fs-2" style="color:${c.color};"></i>
                    <div class="fs-1 fw-bold mt-2" style="color:${c.color};">${c.valor ?? 0}</div>
                    <div class="text-muted small fw-bold text-uppercase">${c.label}</div>
                </div>
            </div>
        </div>`).join('');
}

function curTrayInicializarFiltros() {
    document.getElementById('curTrayBtnLimpiarFiltros')?.addEventListener('click', () => {
        curTrayFiltros = { anio:'', modalidad:'', estatus:'' };
        ['curTrayFiltroAnio','curTrayFiltroModalidad','curTrayFiltroEstatus'].forEach(id => {
            const el = document.getElementById(id); if (el) el.value = '';
        });
        curTrayActualizarChipsFiltros();
        curTrayRenderTabla();
    });

    const ids = {
        'curTrayFiltroAnio':      'anio',
        'curTrayFiltroModalidad': 'modalidad',
        'curTrayFiltroEstatus':   'estatus'
    };

    Object.entries(ids).forEach(([id, campo]) => {
        document.getElementById(id)?.addEventListener('change', e => {
            curTrayFiltros[campo] = e.target.value;
            curTrayPaginaActual = 1;
            curTrayActualizarChipsFiltros();
            curTrayRenderTabla();
        });
    });
}

function curTrayActualizarChipsFiltros() {
    const contenedor = document.getElementById('curTrayFiltrosActivos');
    if (!contenedor) return;
    const chips = [];
    if (curTrayFiltros.anio)      chips.push({ label:`Año: ${curTrayFiltros.anio}`,      campo:'anio' });
    if (curTrayFiltros.modalidad) chips.push({ label:`Mod: ${curTrayFiltros.modalidad}`,  campo:'modalidad' });
    if (curTrayFiltros.estatus)   chips.push({ label:`Est: ${curTrayFiltros.estatus}`,    campo:'estatus' });

    contenedor.innerHTML = chips.map(ch => `
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
            ${ch.label}
            <i class="bi bi-x-lg ms-2 curTray-chip-close" style="cursor:pointer;" data-campo="${ch.campo}"></i>
        </span>`).join('');

    contenedor.querySelectorAll('.curTray-chip-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const campo = btn.dataset.campo;
            curTrayFiltros[campo] = '';
            const mapId = { anio:'curTrayFiltroAnio', modalidad:'curTrayFiltroModalidad', estatus:'curTrayFiltroEstatus' };
            const el = document.getElementById(mapId[campo]); if (el) el.value = '';
            curTrayActualizarChipsFiltros();
            curTrayRenderTabla();
        });
    });
}

function curTrayRenderTabla() {
    const wrapper = document.getElementById('curTrayTablaWrapper');
    if (!wrapper) return;

    let datos = [...curTrayDatosTabla];

    if (curTrayFiltros.anio)      datos = datos.filter(c => c.fecha_inicio?.startsWith(curTrayFiltros.anio));
    if (curTrayFiltros.modalidad) datos = datos.filter(c => c.nombre_modalidad === curTrayFiltros.modalidad);
    if (curTrayFiltros.estatus)   datos = datos.filter(c => c.estatus === curTrayFiltros.estatus);

    if (curTrayBusqueda.trim()) {
        const q = curTrayBusqueda.toLowerCase();
        datos = datos.filter(c =>
            String(c.id_curso).toLowerCase().includes(q) ||
            (c.nombre_curso ?? '').toLowerCase().includes(q)
        );
    }

    const total = datos.length;
    const totalPags = Math.max(1, Math.ceil(total / curTrayFilasPorPagina));
    if (curTrayPaginaActual > totalPags) curTrayPaginaActual = 1;
    const inicio = (curTrayPaginaActual - 1) * curTrayFilasPorPagina;
    const pagina = datos.slice(inicio, inicio + curTrayFilasPorPagina);

    const filas = pagina.length === 0
        ? `<tr><td colspan="7" class="text-center py-5 text-muted">No se encontraron cursos</td></tr>`
        : pagina.map(c => `
            <tr class="curTray-fila ${curTrayCursoAct === c.id_curso ? 'table-active' : ''}" 
                data-id="${c.id_curso}" style="cursor:pointer;">
                <td class="fw-bold">${c.id_curso}</td>
                <td>${c.nombre_curso}</td>
                <td>${c.nombre_modalidad ?? '—'}</td>
                <td class="text-center">${c.turno ?? '—'}</td>
                <td class="text-center">${curTrayFecha(c.fecha_inicio)}</td>
                <td class="text-center">${c.num_inscritos}/${c.max_participantes}</td>
                <td class="text-center">${curTrayBadgeEstatus(c.estatus)}</td>
            </tr>`).join('');

    wrapper.innerHTML = `
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Curso</th>
                        <th>Modalidad</th>
                        <th class="text-center">Turno</th>
                        <th class="text-center">Inicio</th>
                        <th class="text-center">Cupos</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3 px-2">
            <small class="text-muted">Mostrando ${pagina.length} de ${total} cursos</small>
            <nav id="curTrayPaginacion"></nav>
        </div>`;

    curTrayRenderPaginacion(totalPags);

    wrapper.querySelectorAll('.curTray-fila').forEach(tr => {
        tr.addEventListener('click', () => {
            const id = tr.dataset.id;
            if (curTrayCursoAct === id) {
                curTrayCursoAct = null;
                document.getElementById('curTrayDetallePanel').style.display = 'none';
            } else {
                curTrayCursoAct = id;
                curTrayAbrirDetalle(id);
            }
            curTrayRenderTabla();
        });
    });
}

function curTrayRenderPaginacion(totalPags) {
    const nav = document.getElementById('curTrayPaginacion');
    if (!nav || totalPags <= 1) { if(nav) nav.innerHTML = ''; return; }

    let html = `<ul class="pagination pagination-sm mb-0">`;
    for (let i = 1; i <= totalPags; i++) {
        html += `<li class="page-item ${i === curTrayPaginaActual ? 'active' : ''}">
            <a class="page-link curTray-pag-link" href="#" data-pag="${i}">${i}</a>
        </li>`;
    }
    html += `</ul>`;
    nav.innerHTML = html;

    nav.querySelectorAll('.curTray-pag-link').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            curTrayPaginaActual = parseInt(a.dataset.pag);
            curTrayRenderTabla();
        });
    });
}

let curTrayChartInstance = null;

async function curTrayAbrirDetalle(id) {
    const panel = document.getElementById('curTrayDetallePanel');
    panel.style.display = 'block';
    panel.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success"></div></div>`;
    setTimeout(() => panel.scrollIntoView({ behavior:'smooth', block:'start' }), 100);

    try {
        const res = await fetch(`${API_CUR_TRAY_URL}?accion=detalle_curso_trayectoria&id_curso=${id}`);
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        curTrayRenderChart(data);
    } catch (err) {
        panel.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
    }
}

function curTrayRenderChart(d) {
    const panel = document.getElementById('curTrayDetallePanel');
    const parts = d.participantes || [];
    
    const mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    
    let startYear, startMonth, endYear, endMonth;
    if (d.fecha_inicio && d.fecha_inicio !== '0000-00-00') {
        const [sy, sm] = d.fecha_inicio.split('-');
        startYear = parseInt(sy, 10); startMonth = parseInt(sm, 10);
    }
    if (d.fecha_fin && d.fecha_fin !== '0000-00-00') {
        const [ey, em] = d.fecha_fin.split('-');
        endYear = parseInt(ey, 10); endMonth = parseInt(em, 10);
    }
    
    parts.forEach(p => {
        if (!p.fecha_inscripcion || p.fecha_inscripcion === '0000-00-00') return;
        const [y, m] = p.fecha_inscripcion.split('-');
        const py = parseInt(y, 10), pm = parseInt(m, 10);
        if (!startYear || py < startYear || (py === startYear && pm < startMonth)) {
            startYear = py; startMonth = pm;
        }
        if (!endYear || py > endYear || (py === endYear && pm > endMonth)) {
            endYear = py; endMonth = pm;
        }
    });

    if (!startYear) {
        const now = new Date();
        startYear = endYear = now.getFullYear();
        startMonth = endMonth = now.getMonth() + 1;
    }
    if (!endYear) { endYear = startYear; endMonth = startMonth; }

    const labels = [];
    const dataPoints = [];
    const pointColors = [];
    const monthKeys = [];
    const mesesMap = {};
    
    parts.forEach(p => {
        if (!p.fecha_inscripcion || p.fecha_inscripcion === '0000-00-00') return;
        const [y, m] = p.fecha_inscripcion.split('-');
        const key = `${y}-${m.padStart(2, '0')}`;
        if (!mesesMap[key]) {
            mesesMap[key] = { count: 0, participantes: [] };
        }
        mesesMap[key].count++;
        mesesMap[key].participantes.push(p);
    });

    let curY = startYear, curM = startMonth;
    while (curY < endYear || (curY === endYear && curM <= endMonth)) {
        const key = `${curY}-${String(curM).padStart(2, '0')}`;
        const nombreMes = `${mesesNombres[curM - 1]} ${curY}`;
        
        labels.push(nombreMes);
        monthKeys.push(key);
        
        if (mesesMap[key]) {
            mesesMap[key].label = nombreMes;
            dataPoints.push(mesesMap[key].count);
            pointColors.push('#0dcaf0');
        } else {
            mesesMap[key] = { label: nombreMes, count: 0, participantes: [] };
            dataPoints.push(0);
            pointColors.push('#adb5bd'); // Gray for no data
        }
        
        curM++;
        if (curM > 12) { curM = 1; curY++; }
    }
    
    const fueDiferido = d.estatus === 'Diferido' || (d.nombre_curso || '').includes('(Diferido)');
    const warningDiferido = fueDiferido ? `<div class="alert alert-warning mt-3 py-2 text-center" style="font-size:0.85rem;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Atención:</strong> El curso muestra un estatus histórico o actual de Diferido.</div>` : '';

    panel.innerHTML = `
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px; overflow:hidden;">
        <div class="card-header bg-light border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">${d.nombre_curso}</h5>
                    <span class="text-muted small fw-mono">ID: ${d.id_curso}</span>
                </div>
                ${curTrayBadgeEstatus(d.estatus)}
            </div>
            ${warningDiferido}
        </div>
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 text-center text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                <i class="bi bi-graph-up text-info me-2"></i>Inscripciones por Mes
            </h6>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="curTrayCanvas"></canvas>
            </div>
            <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i>Haz clic en un punto de la gráfica para ver los participantes inscritos en ese mes.</p>
        </div>
    </div>
    <div id="curTrayDetalleMesPanel" style="display:none;" class="fade-in"></div>
    `;
    
    const ctx = document.getElementById('curTrayCanvas').getContext('2d');
    
    if (curTrayChartInstance) {
        curTrayChartInstance.destroy();
    }
    
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(13, 202, 240, 0.4)');
    gradient.addColorStop(1, 'rgba(13, 202, 240, 0.0)');
    
    curTrayChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length > 0 ? labels : ['Sin inscripciones'],
            datasets: [{
                label: 'Inscritos',
                data: dataPoints.length > 0 ? dataPoints : [0],
                borderColor: '#0dcaf0',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: pointColors.length > 0 ? pointColors : '#0dcaf0',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (e, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    if (monthKeys.length > 0) {
                        const key = monthKeys[index];
                        const monthData = mesesMap[key];
                        curTrayRenderDetalle(d, monthData);
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    title: { display: true, text: 'Participantes Inscritos', color: '#6c757d', font: { weight: 'bold' } }
                },
                x: {
                    grid: { display: false },
                    offset: true // This pushes points away from the Y-axis
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.raw} Participante(s) (Clic para detalles)`;
                        }
                    }
                }
            }
        }
    });
}

function curTrayRenderDetalle(d, monthData = null) {
    const panel = document.getElementById('curTrayDetalleMesPanel');
    panel.style.display = 'block';
    
    const parts = monthData ? monthData.participantes : (d.participantes || []);
    const titleText = monthData ? `Participantes inscritos en ${monthData.label}` : 'Todos los participantes';
    
    const aprobados = parts.filter(p => p.estatus_participante === 'Aprobado').length;
    const reprobados = parts.filter(p => ['Reprobado','Ausente'].includes(p.estatus_participante)).length;
    const enFormacion = parts.filter(p => p.estatus_participante === 'En formación' || p.estatus_participante === 'Pendiente').length;

    panel.innerHTML = `
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-header bg-light border-0 p-4">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>${titleText}</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Docente</label>
                    <div class="fw-bold"><i class="bi bi-person-workspace me-2"></i>${d.docente ?? 'No asignado'}</div>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Modalidad</label>
                    <div class="fw-bold"><i class="bi bi-mortarboard me-2"></i>${d.nombre_modalidad ?? '—'}</div>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Periodo</label>
                    <div class="fw-bold"><i class="bi bi-calendar-range me-2"></i>${curTrayFecha(d.fecha_inicio)} al ${curTrayFecha(d.fecha_fin)}</div>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Ámbito</label>
                    <div class="fw-bold"><i class="bi bi-geo-alt me-2"></i>${d.nombre_ambito ?? '—'}</div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <div class="fs-4 fw-bold text-success">${aprobados}</div>
                        <div class="text-muted small">Aprobados</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <div class="fs-4 fw-bold text-danger">${reprobados}</div>
                        <div class="text-muted small">Retirados/Reprobados</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <div class="fs-4 fw-bold text-primary">${enFormacion}</div>
                        <div class="text-muted small">En formación/Pendientes</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4">Lista de Participantes</h6>
            <div class="table-responsive rounded-3 border">
                <table class="table table-sm table-striped hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre y Apellido</th>
                            <th class="text-center">Inscripción</th>
                            <th class="text-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${parts.length === 0 ? '<tr><td colspan="4" class="text-center py-3">No hay participantes inscritos</td></tr>' : 
                          parts.map(p => `
                            <tr>
                                <td class="fw-bold">${p.cedula}</td>
                                <td>${p.primer_nombre} ${p.primer_apellido}</td>
                                <td class="text-center">${curTrayFecha(p.fecha_inscripcion)}</td>
                                <td class="text-center">${curTrayBadgeParticipante(p.estatus_participante)}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
    
    setTimeout(() => panel.scrollIntoView({ behavior:'smooth', block:'start' }), 100);
}

function curTrayInicializarBusqueda() {
    const input = document.getElementById('curTraySearchInput');
    if (!input) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            curTrayBusqueda = input.value;
            curTrayPaginaActual = 1;
            curTrayRenderTabla();
        }, 300);
    });
}

// Exportar
window.cargarTrayectoriaCursos = cargarTrayectoriaCursos;
console.log('RE-Curso-Trayectoria.js cargado');

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
let curTrayFiltros         = { anio:'', modalidad:'', estatus:'', tipoPeriodo:'', subPeriodo:'' };

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
        'Asignado':    { bg:'#fff8e1', border:'#ffc10755', text:'#856404' },
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
    curTrayFiltros         = { anio:'', modalidad:'', estatus:'', tipoPeriodo:'', subPeriodo:'' };

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
                        <div class="col-sm-6 col-md-2">
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
                        <div class="col-sm-6 col-md-2">
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
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Estatus
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroEstatus">
                                <option value="">Todos</option>
                            </select>
                        </div>

                        <!-- Periodo Temporal -->
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Periodo Temporal
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroTipoPeriodo">
                                <option value="">Todos</option>
                                <option value="mensual">Mensual</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                            </select>
                        </div>

                        <!-- Detalle de Periodo -->
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Selección de Periodo
                            </label>
                            <select class="form-select form-select-sm" id="curTrayFiltroSubPeriodo" disabled>
                                <option value="">Selecciona...</option>
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

function curTrayActualizarSubPeriodoSelect(tipo) {
    const sel = document.getElementById('curTrayFiltroSubPeriodo');
    if (!sel) return;
    
    if (!tipo) {
        sel.innerHTML = '<option value="">Selecciona...</option>';
        sel.disabled = true;
        return;
    }
    
    sel.disabled = false;
    let html = '<option value="">Todos</option>';
    
    if (tipo === 'mensual') {
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        meses.forEach((m, idx) => {
            html += `<option value="${idx + 1}">${m}</option>`;
        });
    } else if (tipo === 'trimestral') {
        html += '<option value="1">Trimestre 1 (Ene - Mar)</option>';
        html += '<option value="2">Trimestre 2 (Abr - Jun)</option>';
        html += '<option value="3">Trimestre 3 (Jul - Sep)</option>';
        html += '<option value="4">Trimestre 4 (Oct - Dic)</option>';
    } else if (tipo === 'semestral') {
        html += '<option value="1">Semestre 1 (Ene - Jun)</option>';
        html += '<option value="2">Semestre 2 (Jul - Dic)</option>';
    }
    
    sel.innerHTML = html;
}

function curTrayInicializarFiltros() {
    document.getElementById('curTrayBtnLimpiarFiltros')?.addEventListener('click', () => {
        curTrayFiltros = { anio:'', modalidad:'', estatus:'', tipoPeriodo:'', subPeriodo:'' };
        ['curTrayFiltroAnio','curTrayFiltroModalidad','curTrayFiltroEstatus','curTrayFiltroTipoPeriodo','curTrayFiltroSubPeriodo'].forEach(id => {
            const el = document.getElementById(id); if (el) el.value = '';
        });
        curTrayActualizarSubPeriodoSelect('');
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

    document.getElementById('curTrayFiltroTipoPeriodo')?.addEventListener('change', e => {
        curTrayFiltros.tipoPeriodo = e.target.value;
        curTrayFiltros.subPeriodo = '';
        curTrayActualizarSubPeriodoSelect(e.target.value);
        curTrayPaginaActual = 1;
        curTrayActualizarChipsFiltros();
        curTrayRenderTabla();
    });

    document.getElementById('curTrayFiltroSubPeriodo')?.addEventListener('change', e => {
        curTrayFiltros.subPeriodo = e.target.value;
        curTrayPaginaActual = 1;
        curTrayActualizarChipsFiltros();
        curTrayRenderTabla();
    });
}

function curTrayActualizarChipsFiltros() {
    const contenedor = document.getElementById('curTrayFiltrosActivos');
    if (!contenedor) return;
    const chips = [];
    if (curTrayFiltros.anio)      chips.push({ label:`Año: ${curTrayFiltros.anio}`,      campo:'anio' });
    if (curTrayFiltros.modalidad) chips.push({ label:`Mod: ${curTrayFiltros.modalidad}`,  campo:'modalidad' });
    if (curTrayFiltros.estatus)   chips.push({ label:`Est: ${curTrayFiltros.estatus}`,    campo:'estatus' });
    if (curTrayFiltros.tipoPeriodo) {
        let label = `Periodo: ${curTrayFiltros.tipoPeriodo}`;
        if (curTrayFiltros.subPeriodo) {
            if (curTrayFiltros.tipoPeriodo === 'mensual') {
                const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                label += ` (${meses[parseInt(curTrayFiltros.subPeriodo) - 1]})`;
            } else if (curTrayFiltros.tipoPeriodo === 'trimestral') {
                label += ` (Trimestre ${curTrayFiltros.subPeriodo})`;
            } else if (curTrayFiltros.tipoPeriodo === 'semestral') {
                label += ` (Semestre ${curTrayFiltros.subPeriodo})`;
            }
        }
        chips.push({ label: label, campo:'periodo' });
    }

    contenedor.innerHTML = chips.map(ch => `
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
            ${ch.label}
            <i class="bi bi-x-lg ms-2 curTray-chip-close" style="cursor:pointer;" data-campo="${ch.campo}"></i>
        </span>`).join('');

    contenedor.querySelectorAll('.curTray-chip-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const campo = btn.dataset.campo;
            if (campo === 'periodo') {
                curTrayFiltros.tipoPeriodo = '';
                curTrayFiltros.subPeriodo = '';
                const elTipo = document.getElementById('curTrayFiltroTipoPeriodo'); if (elTipo) elTipo.value = '';
                curTrayActualizarSubPeriodoSelect('');
            } else {
                curTrayFiltros[campo] = '';
                const mapId = { anio:'curTrayFiltroAnio', modalidad:'curTrayFiltroModalidad', estatus:'curTrayFiltroEstatus' };
                const el = document.getElementById(mapId[campo]); if (el) el.value = '';
            }
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

    if (curTrayFiltros.tipoPeriodo && curTrayFiltros.subPeriodo) {
        const sub = parseInt(curTrayFiltros.subPeriodo);
        datos = datos.filter(c => {
            if (!c.fecha_inicio || c.fecha_inicio === '0000-00-00') return false;
            const mes = parseInt(c.fecha_inicio.split('-')[1]);
            if (curTrayFiltros.tipoPeriodo === 'mensual') {
                return mes === sub;
            } else if (curTrayFiltros.tipoPeriodo === 'trimestral') {
                if (sub === 1) return mes >= 1 && mes <= 3;
                if (sub === 2) return mes >= 4 && mes <= 6;
                if (sub === 3) return mes >= 7 && mes <= 9;
                if (sub === 4) return mes >= 10 && mes <= 12;
            } else if (curTrayFiltros.tipoPeriodo === 'semestral') {
                if (sub === 1) return mes >= 1 && mes <= 6;
                if (sub === 2) return mes >= 7 && mes <= 12;
            }
            return true;
        });
    }

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
let curTrayMesesState = null;
let curTraySelectedMonthKey = null;
let curTrayCursoDetalle = null;

const CUR_TRAY_MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

function curTrayParseDate(f) {
    if (!f || f === '0000-00-00') return null;
    const [y, m, d] = f.split('-').map(Number);
    if (!y || !m) return null;
    return new Date(y, m - 1, d || 1);
}

function curTrayDiffDays(a, b) {
    if (!a || !b) return 0;
    return Math.max(0, Math.round((b - a) / (1000 * 60 * 60 * 24)));
}

function curTrayBuildMesesData(d) {
    const parts = d.participantes || [];
    let start = curTrayParseDate(d.fecha_inicio);
    let end = curTrayParseDate(d.fecha_fin);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    parts.forEach(p => {
        const fi = curTrayParseDate(p.fecha_inscripcion);
        if (!fi) return;
        if (!start || fi < start) start = fi;
        if (!end || fi > end) end = fi;
    });

    if (!start) start = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    if (!end) end = new Date(start);

    const mesesMap = {};
    parts.forEach(p => {
        if (!p.fecha_inscripcion || p.fecha_inscripcion === '0000-00-00') return;
        const [y, m] = p.fecha_inscripcion.split('-');
        const key = `${y}-${m.padStart(2, '0')}`;
        if (!mesesMap[key]) mesesMap[key] = { count: 0, participantes: [], label: '' };
        mesesMap[key].count++;
        mesesMap[key].participantes.push(p);
    });

    const labels = [];
    const monthKeys = [];
    const dataPoints = [];
    const pointColors = [];
    let curY = start.getFullYear();
    let curM = start.getMonth() + 1;
    const endY = end.getFullYear();
    const endM = end.getMonth() + 1;
    let guard = 0;

    while (curY < endY || (curY === endY && curM <= endM)) {
        if (++guard > 120) break;
        const key = `${curY}-${String(curM).padStart(2, '0')}`;
        const nombreMes = `${CUR_TRAY_MESES[curM - 1]} ${curY}`;
        labels.push(nombreMes);
        monthKeys.push(key);
        if (!mesesMap[key]) mesesMap[key] = { label: nombreMes, count: 0, participantes: [] };
        else mesesMap[key].label = nombreMes;
        dataPoints.push(mesesMap[key].count);
        pointColors.push(mesesMap[key].count > 0 ? '#0891b2' : '#cbd5e1');
        curM++;
        if (curM > 12) { curM = 1; curY++; }
    }

    const totalInscritos = parts.length;
    const mesesConDatos = Object.values(mesesMap).filter(m => m.count > 0);
    let peakKey = null;
    let peakCount = 0;
    Object.entries(mesesMap).forEach(([k, v]) => {
        if (v.count > peakCount) { peakCount = v.count; peakKey = k; }
    });

    const duracionDias = curTrayDiffDays(start, end);
    const transcurridos = curTrayDiffDays(start, hoy);
    const pctTiempo = duracionDias > 0 ? Math.min(100, Math.round((transcurridos / duracionDias) * 100)) : 0;
    const cupoPct = d.max_participantes > 0
        ? Math.round((totalInscritos / d.max_participantes) * 100) : 0;

    let pctHoy = null;
    if (start && end && hoy >= start && hoy <= end && duracionDias > 0) {
        pctHoy = Math.round((transcurridos / duracionDias) * 100);
    }

    return {
        labels, monthKeys, dataPoints, pointColors, mesesMap,
        start, end, hoy, totalInscritos, mesesConDatos: mesesConDatos.length,
        peakKey, peakCount, duracionDias, pctTiempo, cupoPct, pctHoy,
    };
}

function curTrayNarrativaTexto(d, stats) {
    const est = (d.estatus || '').toLowerCase();
    const periodo = `${curTrayFecha(d.fecha_inicio)} — ${curTrayFecha(d.fecha_fin)}`;
    let fase = '';

    if (est.includes('espera')) {
        fase = 'El curso está <strong>en espera</strong> de iniciar. Aún no ha comenzado el periodo lectivo programado.';
    } else if (est.includes('proceso')) {
        fase = `El curso está <strong>en proceso</strong>. Lleva aproximadamente <strong>${stats.pctTiempo}%</strong> del periodo transcurrido (${periodo}).`;
    } else if (est.includes('culminado')) {
        fase = `El curso <strong>culminó</strong> su periodo (${periodo}). Puedes revisar mes a mes cómo se fueron inscribiendo los participantes.`;
    } else if (est.includes('diferido')) {
        fase = 'Este curso fue <strong>diferido</strong>. Revisa las inscripciones por mes para entender su actividad histórica.';
    } else if (est.includes('pausa')) {
        fase = 'El curso está <strong>en pausa</strong>. Las inscripciones registradas reflejan la actividad hasta este punto.';
    } else {
        fase = `Periodo del curso: <strong>${periodo}</strong>.`;
    }

    if (stats.totalInscritos === 0) {
        return `${fase} Hasta ahora <strong>no hay participantes inscritos</strong> registrados en el sistema para este curso.`;
    }

    const pico = stats.peakKey && stats.mesesMap[stats.peakKey]
        ? stats.mesesMap[stats.peakKey].label : null;

    let insc = `Hay <strong>${stats.totalInscritos}</strong> participante(s) inscrito(s)`;
    if (stats.mesesConDatos === 1) insc += ' concentrados en un solo mes';
    else if (stats.mesesConDatos > 1) insc += ` distribuidos en <strong>${stats.mesesConDatos} meses</strong>`;
    if (pico && stats.peakCount > 0) insc += `; el mayor pico fue en <strong>${pico}</strong> (${stats.peakCount} inscripción${stats.peakCount !== 1 ? 'es' : ''})`;
    insc += '.';

    return `${fase} ${insc} Selecciona un mes abajo o haz clic en la gráfica para ver quiénes se inscribieron.`;
}

function curTrayEscape(s) {
    if (s == null) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

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

function curTraySelectMonth(key) {
    if (!curTrayMesesState || !curTrayCursoDetalle) return;
    curTraySelectedMonthKey = key;

    document.querySelectorAll('.ct-month-chip').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.monthKey === key);
    });

    if (curTrayChartInstance && curTrayMesesState) {
        const colors = curTrayMesesState.dataPoints.map((v, i) =>
            curTrayMesesState.monthKeys[i] === key ? '#0e7490' : (v > 0 ? '#0891b2' : '#e2e8f0'));
        curTrayChartInstance.data.datasets[0].backgroundColor = colors;
        curTrayChartInstance.data.datasets[0].borderColor = colors.map(c => (c === '#e2e8f0' ? '#cbd5e1' : c));
        curTrayChartInstance.update('none');
    }

    const monthData = key ? curTrayMesesState.mesesMap[key] : null;
    curTrayRenderDetalle(curTrayCursoDetalle, monthData);
}

function curTrayRenderLifecycleHtml(d, stats) {
    const { start, end, hoy, pctTiempo, peakKey, mesesMap } = stats;
    if (!start || !end) return '';

    const totalMs = end - start || 1;
    const pctFill = Math.min(100, pctTiempo);
    let pctToday = null;
    if (hoy >= start && hoy <= end) {
        pctToday = Math.round(((hoy - start) / totalMs) * 100);
    }

    const pos = (date) => {
        if (!date || date < start) return 0;
        if (date > end) return 100;
        return Math.round(((date - start) / totalMs) * 100);
    };

    const posStart = 0;
    const posEnd = 100;
    const posPeak = peakKey && mesesMap[peakKey]?.count > 0
        ? (() => {
            const [y, m] = peakKey.split('-').map(Number);
            const peakDate = new Date(y, m - 1, 15);
            return pos(peakDate);
        })() : null;

    const peakLabel = peakKey && mesesMap[peakKey]
        ? `Pico: ${mesesMap[peakKey].label}` : '';

    return `
        <div class="ct-lifecycle">
            <div class="ct-lifecycle-title"><i class="bi bi-signpost-split me-1"></i> Línea de tiempo del curso</div>
            <div class="ct-lifecycle-track">
                <div class="ct-lifecycle-fill" style="width:${pctFill}%;"></div>
                ${pctToday != null ? `<div class="ct-lifecycle-today" style="left:${pctToday}%;"></div>` : ''}
                <div class="ct-lifecycle-node" style="left:${posStart}%;">
                    <span class="ct-lifecycle-dot start"></span>
                    <span class="ct-lifecycle-label">Inicio<br>${curTrayFecha(d.fecha_inicio)}</span>
                </div>
                ${posPeak != null ? `
                <div class="ct-lifecycle-node" style="left:${posPeak}%;">
                    <span class="ct-lifecycle-dot peak"></span>
                    <span class="ct-lifecycle-label">${curTrayEscape(peakLabel)}</span>
                </div>` : ''}
                <div class="ct-lifecycle-node" style="left:${posEnd}%;">
                    <span class="ct-lifecycle-dot end"></span>
                    <span class="ct-lifecycle-label">Fin<br>${curTrayFecha(d.fecha_fin)}</span>
                </div>
            </div>
        </div>`;
}

function curTrayRenderChart(d) {
    const panel = document.getElementById('curTrayDetallePanel');
    curTrayCursoDetalle = d;
    curTraySelectedMonthKey = null;

    const stats = curTrayBuildMesesData(d);
    curTrayMesesState = stats;

    const fueDiferido = d.estatus === 'Diferido' || (d.nombre_curso || '').includes('(Diferido)');
    const warningDiferido = fueDiferido
        ? `<div class="alert alert-warning mx-3 mt-2 mb-0 py-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Curso con estatus <strong>Diferido</strong>.</div>`
        : '';

    const chipsHtml = stats.monthKeys.map(key => {
        const m = stats.mesesMap[key];
        const active = key === curTraySelectedMonthKey ? ' active' : '';
        const has = m.count > 0 ? ' has-data' : '';
        return `<button type="button" class="ct-month-chip${active}${has}" data-month-key="${key}">
            ${curTrayEscape(m.label)}<span class="chip-count">${m.count}</span>
        </button>`;
    }).join('');

    const narrativa = curTrayNarrativaTexto(d, stats);

    panel.innerHTML = `
    <div class="ct-journey-card mb-4">
        <div class="ct-journey-header">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">${curTrayEscape(d.nombre_curso)}</h5>
                    <div class="text-muted small">
                        <span class="me-3"><i class="bi bi-hash"></i>ID ${d.id_curso}</span>
                        <span class="me-3"><i class="bi bi-mortarboard me-1"></i>${curTrayEscape(d.nombre_modalidad ?? '—')}</span>
                        <span><i class="bi bi-person-workspace me-1"></i>${curTrayEscape(d.docente ?? 'Sin docente')}</span>
                    </div>
                </div>
                ${curTrayBadgeEstatus(d.estatus)}
            </div>
            ${warningDiferido}
        </div>

        <div class="ct-journey-kpis">
            <div class="ct-journey-kpi">
                <div class="val">${stats.duracionDias}</div>
                <div class="lbl">Días de trayectoria</div>
            </div>
            <div class="ct-journey-kpi">
                <div class="val">${stats.totalInscritos}</div>
                <div class="lbl">Inscritos totales</div>
            </div>
            <div class="ct-journey-kpi">
                <div class="val">${stats.cupoPct}%</div>
                <div class="lbl">Cupo usado (${stats.totalInscritos}/${d.max_participantes ?? '—'})</div>
            </div>
            <div class="ct-journey-kpi">
                <div class="val">${stats.peakCount || '—'}</div>
                <div class="lbl">Pico mensual</div>
            </div>
        </div>

        <div class="ct-narrative-box">${narrativa}</div>

        ${curTrayRenderLifecycleHtml(d, stats)}

        <div class="px-3 pb-2">
            <div class="ct-lifecycle-title mb-2"><i class="bi bi-calendar3 me-1"></i> Inscripciones por mes — selecciona para ver participantes</div>
            <div class="ct-month-chips" id="curTrayMonthChips">${chipsHtml || '<span class="text-muted small">Sin meses en el periodo</span>'}</div>
        </div>

        <div class="ct-chart-wrap">
            <canvas id="curTrayCanvas"></canvas>
        </div>
        <p class="text-center text-muted small pb-3 mb-0 px-3">
            <i class="bi bi-hand-index-thumb me-1"></i>Clic en un mes o en la barra para ver quiénes se inscribieron.
        </p>

        <div id="curTrayDetalleMesPanel" style="display:none;"></div>
    </div>`;

    document.getElementById('curTrayMonthChips')?.querySelectorAll('.ct-month-chip').forEach(btn => {
        btn.addEventListener('click', () => curTraySelectMonth(btn.dataset.monthKey));
    });

    const canvas = document.getElementById('curTrayCanvas');
    if (!canvas || typeof Chart === 'undefined') return;

    const ctx = canvas.getContext('2d');
    if (curTrayChartInstance) curTrayChartInstance.destroy();

    const maxY = Math.max(3, ...stats.dataPoints, 1);
    const barColors = stats.dataPoints.map((v, i) =>
        stats.monthKeys[i] === curTraySelectedMonthKey ? '#0e7490' : (v > 0 ? '#0891b2' : '#e2e8f0')
    );

    curTrayChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: stats.labels.length ? stats.labels : ['Sin periodo'],
            datasets: [{
                label: 'Inscripciones',
                data: stats.dataPoints.length ? stats.dataPoints : [0],
                backgroundColor: barColors,
                borderColor: barColors.map(c => c === '#e2e8f0' ? '#cbd5e1' : c),
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 48,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (_e, elements) => {
                if (elements.length > 0 && stats.monthKeys.length) {
                    curTraySelectMonth(stats.monthKeys[elements[0].index]);
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: maxY,
                    ticks: { stepSize: 1, precision: 0 },
                    title: {
                        display: true,
                        text: 'Nuevas inscripciones en el mes',
                        color: '#64748b',
                        font: { weight: '600', size: 11 },
                    },
                    grid: { color: '#f1f5f9' },
                },
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } },
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} inscripción${ctx.raw !== 1 ? 'es' : ''} — clic para ver lista`,
                    },
                },
            },
        },
    });

    if (stats.peakKey && stats.mesesMap[stats.peakKey]?.count > 0) {
        curTraySelectMonth(stats.peakKey);
    } else {
        const firstWithData = stats.monthKeys.find(k => stats.mesesMap[k]?.count > 0);
        if (firstWithData) curTraySelectMonth(firstWithData);
    }
}

function curTrayRenderDetalle(d, monthData = null) {
    const panel = document.getElementById('curTrayDetalleMesPanel');
    if (!panel) return;

    const parts = monthData ? monthData.participantes : (d.participantes || []);
    const titleText = monthData
        ? `Inscritos en ${monthData.label}`
        : 'Todos los participantes del curso';

    if (!monthData && parts.length === 0) {
        panel.style.display = 'none';
        return;
    }

    panel.style.display = 'block';

    const aprobados = parts.filter(p => p.estatus_participante === 'Aprobado').length;
    const reprobados = parts.filter(p => ['Reprobado', 'Ausente'].includes(p.estatus_participante)).length;
    const enFormacion = parts.filter(p => ['En formación', 'Asignado'].includes(p.estatus_participante)).length;

    panel.innerHTML = `
    <div class="ct-participants-panel p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i>${curTrayEscape(titleText)}
                <span class="badge bg-primary ms-2">${parts.length}</span>
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="curTrayVerTodosBtn">
                Ver todos los inscritos
            </button>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="ct-journey-kpi py-2">
                    <div class="val text-success" style="font-size:1.1rem;">${aprobados}</div>
                    <div class="lbl">Aprobados</div>
                </div>
            </div>
            <div class="col-4">
                <div class="ct-journey-kpi py-2">
                    <div class="val text-danger" style="font-size:1.1rem;">${reprobados}</div>
                    <div class="lbl">Reprob./Ausente</div>
                </div>
            </div>
            <div class="col-4">
                <div class="ct-journey-kpi py-2">
                    <div class="val text-primary" style="font-size:1.1rem;">${enFormacion}</div>
                    <div class="lbl">En formación</div>
                </div>
            </div>
        </div>

        <div class="table-responsive rounded-3 border bg-white">
            <table class="table table-sm table-hover align-middle mb-0" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th class="text-center">Inscripción</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    ${parts.length === 0
                        ? '<tr><td colspan="4" class="text-center py-4 text-muted">Nadie se inscribió en este mes</td></tr>'
                        : parts.map(p => `
                        <tr>
                            <td class="fw-semibold">${p.cedula ?? '—'}</td>
                            <td>${curTrayEscape(`${p.primer_nombre || ''} ${p.primer_apellido || ''}`.trim())}</td>
                            <td class="text-center">${curTrayFecha(p.fecha_inscripcion)}</td>
                            <td class="text-center">${curTrayBadgeParticipante(p.estatus_participante)}</td>
                        </tr>`).join('')}
                </tbody>
            </table>
        </div>
    </div>`;

    document.getElementById('curTrayVerTodosBtn')?.addEventListener('click', () => {
        curTraySelectedMonthKey = null;
        document.querySelectorAll('.ct-month-chip').forEach(b => b.classList.remove('active'));
        if (curTrayChartInstance) curTrayChartInstance.setActiveElements([]);
        curTrayRenderDetalle(d, null);
    });

    setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 80);
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
Object.defineProperty(window, 'curTrayFiltros', { get: function() { return curTrayFiltros; }, configurable: true });
Object.defineProperty(window, 'curTrayBusqueda', { get: function() { return curTrayBusqueda; }, configurable: true });
Object.defineProperty(window, 'curTrayCursoAct', { get: function() { return curTrayCursoAct; }, configurable: true });
Object.defineProperty(window, 'curTrayCursoDetalle', { get: function() { return curTrayCursoDetalle; }, configurable: true });
Object.defineProperty(window, 'curTrayChartInstance', { get: function() { return curTrayChartInstance; }, configurable: true });

window.cargarTrayectoriaCursos = cargarTrayectoriaCursos;
console.log('RE-Curso-Trayectoria.js cargado');

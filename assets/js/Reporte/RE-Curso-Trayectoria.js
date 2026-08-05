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

        // Autoselección si viene redirigido con ?id=... o ?preimpreso=...
        const urlParams = new URLSearchParams(window.location.search);
        const targetId = urlParams.get('id') || urlParams.get('preimpreso');
        if (targetId) {
            const targetClean = String(targetId).trim().toLowerCase();
            const cEncontrado = curTrayDatosTabla.find(c => {
                const idMatch = String(c.id_curso) === targetClean;
                const idsMatch = (c.todos_id_curso || '').split(',').includes(targetClean);
                const nameMatch = (c.nombre_curso || '').trim().toLowerCase() === targetClean;
                const preMatch = (c.preimpresos || '').toLowerCase().includes(targetClean);
                return idMatch || idsMatch || nameMatch || preMatch;
            });
            if (cEncontrado) {
                curTrayBusqueda = cEncontrado.nombre_curso;
                const sInput = document.getElementById('curTraySearchInput');
                if (sInput) sInput.value = curTrayBusqueda;
                curTrayCursoAct = cEncontrado.nombre_curso;
                curTrayPaginaActual = 1;
                curTrayRenderTabla();
                curTrayAbrirDetalle(cEncontrado.nombre_curso);
            }
        }

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
        if (c.fecha_minima) {
            const a = c.fecha_minima.split('-')[0];
            if (a) setAnios.add(a);
        }
        if (c.nombre_modalidad) setMods.add(c.nombre_modalidad);
        if (c.estatus) setEsts.add(c.estatus);
    });

    const poblar = (id, items, defOpt) => {
        const sel = document.getElementById(id);
        if (!sel) return;
        sel.innerHTML = defOpt;
        [...items].sort().forEach(it => {
            const opt = document.createElement('option');
            opt.value = it; opt.textContent = it; sel.appendChild(opt);
        });
    };

    poblar('curTrayFiltroAnio',      setAnios, '<option value="">Todos los años</option>');
    poblar('curTrayFiltroModalidad', setMods,  '<option value="">Todas</option>');
    poblar('curTrayFiltroEstatus',   setEsts,  '<option value="">Todos</option>');
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

    if (curTrayFiltros.anio)      datos = datos.filter(c => c.fecha_minima?.startsWith(curTrayFiltros.anio) || c.fecha_maxima?.startsWith(curTrayFiltros.anio));
    if (curTrayFiltros.modalidad) datos = datos.filter(c => c.nombre_modalidad === curTrayFiltros.modalidad);
    if (curTrayFiltros.estatus)   datos = datos.filter(c => c.estatus === curTrayFiltros.estatus);

    if (curTrayFiltros.tipoPeriodo && curTrayFiltros.subPeriodo) {
        const sub = parseInt(curTrayFiltros.subPeriodo);
        datos = datos.filter(c => {
            if (!c.fecha_minima || c.fecha_minima === '0000-00-00') return false;
            const mes = parseInt(c.fecha_minima.split('-')[1]);
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
        const queryClean = curTrayBusqueda.trim().toLowerCase();
        const terms = queryClean.split(/\s+/).filter(Boolean);

        datos = datos.filter(c => {
            const idCurso       = String(c.id_curso ?? '').toLowerCase();
            const todosIdCurso  = String(c.todos_id_curso ?? '').toLowerCase();
            const nombreCurso   = (c.nombre_curso ?? '').toLowerCase();
            const preimpresos   = (c.preimpresos   ?? '').toLowerCase();
            const modalidad     = (c.nombre_modalidad ?? '').toLowerCase();
            const ambito        = (c.nombre_ambito    ?? '').toLowerCase();
            const estatus       = (c.estatus          ?? '').toLowerCase();

            const searchBlob = `${idCurso} ${todosIdCurso} ${nombreCurso} ${preimpresos} ${modalidad} ${ambito} ${estatus}`;

            return terms.every(term => searchBlob.includes(term));
        });
    }

    const total = datos.length;
    const totalPags = Math.max(1, Math.ceil(total / curTrayFilasPorPagina));
    if (curTrayPaginaActual > totalPags) curTrayPaginaActual = 1;
    const inicio = (curTrayPaginaActual - 1) * curTrayFilasPorPagina;
    const pagina = datos.slice(inicio, inicio + curTrayFilasPorPagina);

    const filas = pagina.length === 0
        ? `<tr><td colspan="7" class="text-center py-5 text-muted">No se encontraron cursos</td></tr>`
        : pagina.map(c => `
            <tr class="curTray-fila ${curTrayCursoAct === c.nombre_curso ? 'table-active' : ''}" 
                data-id="${c.id_curso}"
                data-ids="${c.todos_id_curso ?? c.id_curso}"
                data-nombre="${curTrayEscape(c.nombre_curso)}"
                data-preimpresos="${curTrayEscape(c.preimpresos)}" style="cursor:pointer;">
                <td class="fw-bold">
                    ${curTrayEscape(c.nombre_curso)}
                    ${c.preimpresos ? `<div class="text-muted small" style="font-weight:normal; font-size:0.75rem;"><i class="bi bi-tag-fill me-1"></i>Preimpresos: ${curTrayEscape(c.preimpresos)}</div>` : ''}
                </td>
                <td class="text-center"><span class="badge bg-secondary rounded-pill" style="font-size:0.85rem; padding: 0.35em 0.65em;">${c.total_reinicios}</span></td>
                <td class="text-center" style="font-size:0.88rem;">${curTrayFecha(c.fecha_minima)} a ${curTrayFecha(c.fecha_maxima)}</td>
                <td>${curTrayEscape(c.nombre_modalidad ?? '—')}</td>
                <td>${curTrayEscape(c.nombre_ambito ?? '—')}</td>
                <td class="text-center fw-semibold">${c.total_inscritos} <span class="text-muted" style="font-size:0.75rem; font-weight:normal;">(prom: ${c.promedio_inscritos})</span></td>
                <td class="text-center">${curTrayBadgeEstatus(c.estatus)}</td>
            </tr>`).join('');

    wrapper.innerHTML = `
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Curso</th>
                        <th class="text-center">Reinicios</th>
                        <th class="text-center">Período Histórico</th>
                        <th>Modalidad</th>
                        <th>Ámbito</th>
                        <th class="text-center">Total Inscritos</th>
                        <th class="text-center">Estatus General</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3 px-2">
            <small class="text-muted">Mostrando ${pagina.length} de ${total} cursos únicos</small>
            <nav id="curTrayPaginacion"></nav>
        </div>`;

    curTrayRenderPaginacion(totalPags);

    wrapper.querySelectorAll('.curTray-fila').forEach(tr => {
        tr.addEventListener('click', () => {
            const nombre = tr.dataset.nombre;
            if (curTrayCursoAct === nombre) {
                curTrayCursoAct = null;
                document.getElementById('curTrayDetallePanel').style.display = 'none';
            } else {
                curTrayCursoAct = nombre;
                curTrayAbrirDetalle(nombre);
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
let curTrayCursoDetalle = null;

function curTrayEscape(s) {
    if (s == null) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

async function curTrayAbrirDetalle(nombre) {
    const panel = document.getElementById('curTrayDetallePanel');
    panel.style.display = 'block';
    panel.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success"></div></div>`;
    setTimeout(() => panel.scrollIntoView({ behavior:'smooth', block:'start' }), 100);

    try {
        const res = await fetch(`${API_CUR_TRAY_URL}?accion=detalle_curso_trayectoria&nombre_curso=${encodeURIComponent(nombre)}`);
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        curTrayRenderChart(data);
    } catch (err) {
        panel.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
    }
}

function curTrayObtenerNombreMes(fechaStr) {
    if (!fechaStr) return '';
    const partes = fechaStr.split('-');
    if (partes.length < 2) return '';
    const mesNum = parseInt(partes[1], 10);
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    return meses[mesNum - 1] || '';
}

function curTrayGetChartData(d, metric) {
    const labels = d.ejecuciones.map((ej, index) => {
        const mes = curTrayObtenerNombreMes(ej.fecha_inicio);
        const labelMes = mes ? ` (${mes})` : '';
        const p = ej.preimpreso ? `Preimpreso: ${ej.preimpreso}` : `ID: ${ej.id_curso}`;
        return [`Reinicio #${index + 1}${labelMes}`, `${curTrayFecha(ej.fecha_inicio)}`, p];
    });

    let datasets = [];

    if (metric === 'estatus') {
        datasets = [
            {
                label: 'Aprobados',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.estatus_participante === 'Aprobado').length),
                backgroundColor: '#28c76f',
                maxBarThickness: 45,
            },
            {
                label: 'Reprobados/Ausentes',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => ['Reprobado', 'Ausente'].includes(p.estatus_participante)).length),
                backgroundColor: '#ea5455',
                maxBarThickness: 45,
            },
            {
                label: 'En formación/Asignados',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => ['En formación', 'Asignado'].includes(p.estatus_participante)).length),
                backgroundColor: '#00cfe8',
                maxBarThickness: 45,
            }
        ];
    } else if (metric === 'genero') {
        datasets = [
            {
                label: 'Femenino',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.genero === 'Femenino').length),
                backgroundColor: '#ec4899',
                maxBarThickness: 45,
            },
            {
                label: 'Masculino',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.genero === 'Masculino').length),
                backgroundColor: '#3b82f6',
                maxBarThickness: 45,
            }
        ];
    } else if (metric === 'discapacidad') {
        datasets = [
            {
                label: 'Sin Discapacidad',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => !p.id_discapacidad || parseInt(p.id_discapacidad) === 1).length),
                backgroundColor: '#6366f1',
                maxBarThickness: 45,
            },
            {
                label: 'Con Discapacidad',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.id_discapacidad && parseInt(p.id_discapacidad) > 1).length),
                backgroundColor: '#f59e0b',
                maxBarThickness: 45,
            }
        ];
    } else if (metric === 'nacionalidad') {
        datasets = [
            {
                label: 'Venezolanos (V)',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.nacionalidad === 'V').length),
                backgroundColor: '#10b981',
                maxBarThickness: 45,
            },
            {
                label: 'Extranjeros (E)',
                data: d.ejecuciones.map(ej => ej.participantes.filter(p => p.nacionalidad && p.nacionalidad !== 'V').length),
                backgroundColor: '#8b5cf6',
                maxBarThickness: 45,
            }
        ];
    }

    return { labels, datasets };
}

function curTrayUpdateChart(metric) {
    if (!curTrayChartInstance || !curTrayCursoDetalle) return;
    const chartData = curTrayGetChartData(curTrayCursoDetalle, metric);
    curTrayChartInstance.data.labels = chartData.labels;
    curTrayChartInstance.data.datasets = [
        {
            type: 'line',
            label: 'Tendencia Inscritos',
            data: curTrayCursoDetalle.ejecuciones.map(ej => ej.participantes.length),
            borderColor: '#6366f1',
            borderWidth: 3,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#6366f1',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 8,
            fill: false,
            tension: 0.35,
            order: 0
        },
        ...chartData.datasets.map(ds => {
            ds.stack = 'stack1';
            ds.type = 'bar';
            ds.borderRadius = 6;
            ds.order = 1;
            ds.grouped = false;
            return ds;
        })
    ];
    curTrayChartInstance.update();
}

function curTrayRenderChart(d) {
    const panel = document.getElementById('curTrayDetallePanel');
    curTrayCursoDetalle = d;

    const totalReinicios = d.ejecuciones.length;
    const totalInscritos = d.ejecuciones.reduce((acc, ej) => acc + (ej.participantes ? ej.participantes.length : 0), 0);
    const totalAprobados = d.ejecuciones.reduce((acc, ej) => acc + (ej.participantes ? ej.participantes.filter(p => p.estatus_participante === 'Aprobado').length : 0), 0);
    const totalReprobados = d.ejecuciones.reduce((acc, ej) => acc + (ej.participantes ? ej.participantes.filter(p => ['Reprobado', 'Ausente'].includes(p.estatus_participante)).length : 0), 0);
    const totalEnFormacion = d.ejecuciones.reduce((acc, ej) => acc + (ej.participantes ? ej.participantes.filter(p => ['En formación', 'Asignado'].includes(p.estatus_participante)).length : 0), 0);
    const tasaAprobacion = totalInscritos > 0 ? Math.round((totalAprobados / totalInscritos) * 100) : 0;
    const promedioInscritos = totalReinicios > 0 ? Math.round(totalInscritos / totalReinicios) : 0;

    // Calcular promedios demográficos
    let totalFem = 0, totalMasc = 0;
    let totalConDisc = 0, totalSinDisc = 0;
    let totalVen = 0, totalExt = 0;
    let totalPartDemographics = 0;

    d.ejecuciones.forEach(ej => {
        if (ej.participantes) {
            ej.participantes.forEach(p => {
                totalPartDemographics++;
                // Género
                if (p.genero === 'Femenino') totalFem++;
                else if (p.genero === 'Masculino') totalMasc++;
                
                // Discapacidad
                if (p.id_discapacidad && parseInt(p.id_discapacidad) > 1) totalConDisc++;
                else totalSinDisc++;

                // Nacionalidad
                if (p.nacionalidad === 'V') totalVen++;
                else if (p.nacionalidad && p.nacionalidad !== 'V') totalExt++;
            });
        }
    });

    const pctFem = totalPartDemographics > 0 ? Math.round((totalFem / totalPartDemographics) * 100) : 0;
    const pctMasc = totalPartDemographics > 0 ? Math.round((totalMasc / totalPartDemographics) * 100) : 0;
    const pctConDisc = totalPartDemographics > 0 ? Math.round((totalConDisc / totalPartDemographics) * 100) : 0;
    const pctSinDisc = totalPartDemographics > 0 ? Math.round((totalSinDisc / totalPartDemographics) * 100) : 0;
    const pctVen = totalPartDemographics > 0 ? Math.round((totalVen / totalPartDemographics) * 100) : 0;
    const pctExt = totalPartDemographics > 0 ? Math.round((totalExt / totalPartDemographics) * 100) : 0;

    // Calcular Alza o Baja (Crecimiento entre el último y penúltimo reinicio)
    let trendHtml = '';
    if (totalReinicios >= 2) {
        const lastRun = d.ejecuciones[totalReinicios - 1].participantes.length;
        const prevRun = d.ejecuciones[totalReinicios - 2].participantes.length;
        if (prevRun > 0) {
            const trendPct = Math.round(((lastRun - prevRun) / prevRun) * 100);
            if (trendPct > 0) {
                trendHtml = `<span class="badge bg-success bg-opacity-10 text-success small ms-2" style="font-size:0.75rem;"><i class="bi bi-arrow-up-right me-1"></i>+${trendPct}% (Alza)</span>`;
            } else if (trendPct < 0) {
                trendHtml = `<span class="badge bg-danger bg-opacity-10 text-danger small ms-2" style="font-size:0.75rem;"><i class="bi bi-arrow-down-left me-1"></i>${trendPct}% (Baja)</span>`;
            } else {
                trendHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary small ms-2" style="font-size:0.75rem;"><i class="bi bi-dash me-1"></i>0% (Sin cambios)</span>`;
            }
        } else {
            trendHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary small ms-2">—</span>`;
        }
    } else {
        trendHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary small ms-2" style="font-size: 0.65rem;">Datos insuficientes</span>`;
    }

    // Calcular Ponderaje de Proyección del próximo reinicio
    let proyectado = 0;
    let confianza = 0;
    if (totalReinicios === 0) {
        proyectado = 0;
        confianza = 0;
    } else if (totalReinicios === 1) {
        proyectado = d.ejecuciones[0].participantes.length;
        confianza = 50;
    } else {
        let sumaPesos = 0;
        let sumaPonderada = 0;
        d.ejecuciones.forEach((ej, idx) => {
            const peso = idx + 1;
            sumaPesos += peso;
            sumaPonderada += ej.participantes.length * peso;
        });
        const promedioPonderado = sumaPonderada / sumaPesos;
        const last = d.ejecuciones[totalReinicios - 1].participantes.length;
        const prev = d.ejecuciones[totalReinicios - 2].participantes.length;
        const factorCrecimiento = prev > 0 ? last / prev : 1;
        const factorSuavizado = Math.max(0.8, Math.min(1.2, factorCrecimiento));
        proyectado = Math.round(promedioPonderado * factorSuavizado);
        confianza = Math.min(95, 50 + (totalReinicios * 10));
    }

    const chartData = curTrayGetChartData(d, 'estatus');

    const listHtml = d.ejecuciones.map((ej, index) => {
        const runAprobados = ej.participantes.filter(p => p.estatus_participante === 'Aprobado').length;
        const runReprobados = ej.participantes.filter(p => ['Reprobado', 'Ausente'].includes(p.estatus_participante)).length;
        const runEnFormacion = ej.participantes.filter(p => ['En formación', 'Asignado'].includes(p.estatus_participante)).length;
        const runTotal = ej.participantes.length;

        return `
        <div class="card border border-light shadow-sm mb-3" style="border-radius: 12px; background: #fff; overflow:hidden;">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 py-3" style="border-bottom: 1px solid #f1f5f9;">
                <div>
                    <span class="badge bg-primary text-white me-2" style="font-size:0.78rem;">Reinicio #${index + 1} (${curTrayObtenerNombreMes(ej.fecha_inicio)})</span>
                    <strong class="text-dark" style="font-size:0.95rem;">${curTrayFecha(ej.fecha_inicio)} — ${curTrayFecha(ej.fecha_fin)}</strong>
                    <span class="badge bg-light text-dark border ms-2" style="font-size:0.78rem;"><i class="bi bi-tag-fill me-1"></i>Preimpreso: <strong>${curTrayEscape(ej.preimpreso ?? '—')}</strong></span>
                    <span class="text-muted ms-3" style="font-size:0.85rem;"><i class="bi bi-person-workspace me-1"></i>Docente: <strong>${curTrayEscape(ej.docente ?? 'Sin docente')}</strong></span>
                </div>
                <div>
                    ${curTrayBadgeEstatus(ej.estatus)}
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="text-muted small" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.03em;">Inscritos</div>
                                <div class="fw-bold fs-6 text-dark">${runTotal}</div>
                            </div>
                            <div class="col-3">
                                <div class="text-success small" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.03em;">Aprobados</div>
                                <div class="fw-bold fs-6 text-success">${runAprobados}</div>
                            </div>
                            <div class="col-3">
                                <div class="text-danger small" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.03em;">Reprobados</div>
                                <div class="fw-bold fs-6 text-danger">${runReprobados}</div>
                            </div>
                            <div class="col-3">
                                <div class="text-info small" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.03em;">En formación</div>
                                <div class="fw-bold fs-6 text-info">${runEnFormacion}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end d-flex flex-wrap justify-content-end">
                        <button class="btn btn-sm btn-outline-primary button view-curso" 
                                data-id="${ej.id_curso}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modal-viewCurso"
                                style="font-size:0.78rem; border-radius:8px; padding:0.4em 1.2em;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Ver Curso y Gráficas
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    panel.innerHTML = `
    <div class="ct-journey-card mb-4 shadow border-0" style="border-radius:16px; overflow:hidden; background:#fff;">
        <div class="ct-journey-header p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom:1px solid #e2e8f0;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 fw-bold text-dark" style="font-size:1.45rem;"><i class="bi bi-journal-text me-2 text-primary"></i>${curTrayEscape(d.nombre_curso)}</h5>
                    <div class="text-muted small">
                        <span><i class="bi bi-mortarboard-fill text-secondary me-1"></i>Trayectoria e Inteligencia del Historial de Reinicios</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 1 KPIs y Tendencias -->
        <div class="ct-journey-kpis px-4 py-3" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; background:#fff;">
            <div class="ct-journey-kpi p-3 border rounded-3 bg-light bg-opacity-40">
                <div class="val fw-bold fs-3 text-primary">${totalReinicios}</div>
                <div class="lbl text-muted small" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.72rem;">Reinicios totales</div>
            </div>
            <div class="ct-journey-kpi p-3 border rounded-3 bg-light bg-opacity-40">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="val fw-bold fs-3 text-dark">${totalInscritos}</div>
                    ${trendHtml}
                </div>
                <div class="lbl text-muted small" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.72rem; margin-top:2px;">Inscritos históricos</div>
            </div>
            <div class="ct-journey-kpi p-3 border rounded-3 bg-light bg-opacity-40">
                <div class="val fw-bold fs-3 text-success">${tasaAprobacion}%</div>
                <div class="lbl text-muted small" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.72rem;">Tasa de Aprobación</div>
            </div>
            <div class="ct-journey-kpi p-3 border rounded-3 bg-light bg-opacity-40">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="val fw-bold fs-3 text-indigo">~${proyectado}</div>
                    <span class="badge bg-indigo bg-opacity-10 text-indigo small" style="font-size:0.68rem; padding: 0.35em 0.65em;">Confianza: ${confianza}%</span>
                </div>
                <div class="lbl text-muted small" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.72rem; margin-top:2px;">Proyección Próximo Reinicio</div>
            </div>
        </div>

        <!-- Fila 2 Promedios Demográficos -->
        <div class="ct-journey-demographics px-4 py-3 border-top border-bottom" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.25rem; background:#fafafa;">
            <!-- Promedio Género -->
            <div class="p-3 border rounded-3 bg-white shadow-sm">
                <div class="fw-bold mb-2 text-dark" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">
                    <i class="bi bi-gender-ambiguous text-pink me-2"></i>Promedio de Género
                </div>
                <div class="d-flex justify-content-between text-muted small mb-1" style="font-size:0.78rem;">
                    <span>Femenino: <strong>${pctFem}%</strong></span>
                    <span>Masculino: <strong>${pctMasc}%</strong></span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; overflow:hidden;">
                    <div class="progress-bar" role="progressbar" style="width: ${pctFem}%; background-color: #ec4899;" aria-valuenow="${pctFem}" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar" role="progressbar" style="width: ${pctMasc}%; background-color: #3b82f6;" aria-valuenow="${pctMasc}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <!-- Promedio Discapacidad -->
            <div class="p-3 border rounded-3 bg-white shadow-sm">
                <div class="fw-bold mb-2 text-dark" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">
                    <i class="bi bi-heart-pulse-fill text-warning me-2"></i>Promedio de Discapacidad
                </div>
                <div class="d-flex justify-content-between text-muted small mb-1" style="font-size:0.78rem;">
                    <span>Con Discapacidad: <strong>${pctConDisc}%</strong></span>
                    <span>Sin Discapacidad: <strong>${pctSinDisc}%</strong></span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; overflow:hidden;">
                    <div class="progress-bar" role="progressbar" style="width: ${pctConDisc}%; background-color: #f59e0b;" aria-valuenow="${pctConDisc}" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar" role="progressbar" style="width: ${pctSinDisc}%; background-color: #6366f1;" aria-valuenow="${pctSinDisc}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <!-- Promedio Nacionalidad -->
            <div class="p-3 border rounded-3 bg-white shadow-sm">
                <div class="fw-bold mb-2 text-dark" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;">
                    <i class="bi bi-globe2 text-success me-2"></i>Promedio de Nacionalidad
                </div>
                <div class="d-flex justify-content-between text-muted small mb-1" style="font-size:0.78rem;">
                    <span>Venezolanos: <strong>${pctVen}%</strong></span>
                    <span>Extranjeros: <strong>${pctExt}%</strong></span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px; overflow:hidden;">
                    <div class="progress-bar" role="progressbar" style="width: ${pctVen}%; background-color: #10b981;" aria-valuenow="${pctVen}" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar" role="progressbar" style="width: ${pctExt}%; background-color: #8b5cf6;" aria-valuenow="${pctExt}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <div class="px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="fw-bold text-dark" style="font-size:0.95rem;">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>Evolución de Ejecuciones y Tendencia de Alumnos
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Filtro de Gráfico">
                    <button type="button" class="btn btn-outline-primary active curTrayChartToggle" data-metric="estatus">Estatus Alumnos</button>
                    <button type="button" class="btn btn-outline-primary curTrayChartToggle" data-metric="genero">Género</button>
                    <button type="button" class="btn btn-outline-primary curTrayChartToggle" data-metric="discapacidad">Discapacidad</button>
                    <button type="button" class="btn btn-outline-primary curTrayChartToggle" data-metric="nacionalidad">Nacionalidad</button>
                </div>
            </div>
            <div class="ct-chart-wrap" style="height:280px;position:relative;">
                <canvas id="curTrayCanvas"></canvas>
            </div>
        </div>

        <div class="px-4 pb-4">
            <div class="fw-bold mb-3 text-dark" style="font-size:0.95rem;">
                <i class="bi bi-clock-history text-primary me-2"></i>Línea Temporal de Ejecuciones del Curso
            </div>
            <div class="ct-history-list">${listHtml}</div>
        </div>
    </div>`;

    panel.querySelectorAll('.curTrayChartToggle').forEach(btn => {
        btn.addEventListener('click', () => {
            panel.querySelectorAll('.curTrayChartToggle').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            curTrayUpdateChart(btn.dataset.metric);
        });
    });

    const canvas = document.getElementById('curTrayCanvas');
    if (!canvas || typeof Chart === 'undefined') return;

    const ctx = canvas.getContext('2d');
    if (curTrayChartInstance) curTrayChartInstance.destroy();

    curTrayChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Tendencia Inscritos',
                    data: d.ejecuciones.map(ej => ej.participantes.length),
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: false,
                    tension: 0.35,
                    order: 0
                },
                ...chartData.datasets.map(ds => {
                    ds.stack = 'stack1';
                    ds.type = 'bar';
                    ds.borderRadius = 6;
                    ds.order = 1;
                    ds.grouped = false;
                    return ds;
                })
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (_e, elements) => {
                if (elements.length > 0) {
                    const idx = elements[0].index;
                    const ej = d.ejecuciones[idx];
                    if (ej) {
                        const viewBtn = panel.querySelector(`.view-curso[data-id="${ej.id_curso}"]`);
                        if (viewBtn) {
                            viewBtn.click();
                        }
                    }
                }
            },
            scales: {
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: { stepSize: 5, precision: 0 },
                    title: {
                        display: true,
                        text: 'Cantidad de Alumnos',
                        color: '#64748b',
                        font: { weight: '600', size: 11 },
                    },
                    grid: { color: '#f1f5f9' },
                },
                x: {
                    stacked: false,
                    grid: { display: false },
                    ticks: { font: { size: 10 } },
                },
            },
            plugins: {
                legend: { display: true, position: 'top', labels: { boxWidth: 12, font: { size: 10 } } },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
            },
        },
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

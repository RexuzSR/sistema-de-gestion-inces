// ==================== RE-Participante-Trayectoria.js ====================
// Reporte: Trayectoria de Participantes
// ==================================================================

// ── Estado global ──────────────────────────────────────────────────
let trayDatosTabla      = [];
let trayPaginaActual    = 1;
let trayFilasPorPagina  = 20;
let trayOrdenCol        = null;
let trayOrdenAsc        = true;
let trayBusqueda        = '';
let trayParticipanteAct = null;
let trayFiltros         = { conCurso:null, anio:'', genero:'', estado:'', edadMin:'', edadMax:'' };

const API_TRAY_URL = '../controlador/Reporte/api_reportes.php';
const MESES_CORTOS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

// ── Helpers ────────────────────────────────────────────────────────

function trayFecha(f) {
    if (!f || f === '0000-00-00') return '—';
    const [y, m, d] = f.split('-');
    if (!y || !m || !d) return f;
    return `${d.padStart(2,'0')}/${m.padStart(2,'0')}/${y}`;
}

function trayToMs(f) {
    if (!f || f === '0000-00-00') return null;
    const [y, m, d] = f.split('-').map(Number);
    if (!y || !m || !d) return null;
    return Date.UTC(y, m - 1, d);
}

function trayFono(num) {
    if (!num) return '—';
    const s = String(num);
    if (s.length === 10) return `0${s.slice(0,3)}-${s.slice(3,6)}-${s.slice(6)}`;
    if (s.length === 11) return `${s.slice(0,4)}-${s.slice(4,7)}-${s.slice(7)}`;
    return s;
}

/**
 * Normaliza el estado del participante.
 * La BD a veces almacena 'En formaci?n' (corrupción de charset)
 * en lugar de 'En formación'. Los tratamos como equivalentes.
 */
function trayNormalizarEstado(estado) {
    if (!estado) return '';
    // Normalizar variantes con charset roto
    if (estado.includes('formaci') && (estado.includes('?') || estado.includes('n')))
        return 'En formación';
    return estado.trim();
}

// ── Badges ────────────────────────────────────────────────────────

function trayBadgeEstado(estadoRaw) {
    const estado = trayNormalizarEstado(estadoRaw);
    if (!estado) return '<span style="color:#64748b">—</span>';
    const map = {
        'En formación': { bg:'#e7f0ff', border:'#0d6efd55', text:'#0a4fc4' },
        'En sistema':   { bg:'#e0f7fa', border:'#0dcaf055', text:'#0a7a99' },
        'Disponible':   { bg:'#e9f7ef', border:'#19875455', text:'#0f6840' },
        'Asignado':     { bg:'#fff8e1', border:'#ffc10755', text:'#856404' },
        'Inactivo':     { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' },
        'Diferido':     { bg:'#fdecea', border:'#dc354555', text:'#a71d2a' },
    };
    const s = map[estado] ?? { bg:'#f0f0f0', border:'#6c757d55', text:'#495057' };
    return `<span style="background:${s.bg};border:1.5px solid ${s.border};color:${s.text};
                font-size:.72rem;font-weight:700;padding:.28em .75em;border-radius:999px;
                white-space:nowrap;display:inline-block;">${estado}</span>`;
}

function trayBadgeEstatusCurso(est) {
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

function trayMostrarError(el, msg) {
    if (!el) return;
    el.innerHTML = `<div class="alert alert-danger mt-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error:</strong> ${msg}
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-danger"
                    onclick="cargarTrayectoriaParticipantes()">
                <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
            </button>
        </div>
    </div>`;
}

// ── Extrae valores únicos presentes en los datos ───────────────────

function trayExtraerAnios(datos) {
    const set = new Set();
    datos.forEach(p => {
        if (p.fecha_registro && p.fecha_registro !== '0000-00-00') {
            const anio = p.fecha_registro.split('-')[0];
            if (anio && anio.length === 4) set.add(anio);
        }
    });
    return [...set].sort((a, b) => b - a); // desc
}

function trayExtraerEstados(datos) {
    const set = new Set();
    datos.forEach(p => {
        const e = trayNormalizarEstado(p.estado);
        if (e) set.add(e);
    });
    // Orden fijo para consistencia
    const orden = ['En formación','En sistema','Disponible','Asignado','Inactivo','Diferido'];
    return orden.filter(e => set.has(e));
}

// ── Entrada principal ──────────────────────────────────────────────

async function cargarTrayectoriaParticipantes() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 1;
    const cat = (window.categoriasReportes || []).find(c => c.id === categoriaId) || { color:'primary' };

    trayDatosTabla      = [];
    trayPaginaActual    = 1;
    trayOrdenCol        = null;
    trayOrdenAsc        = true;
    trayBusqueda        = '';
    trayParticipanteAct = null;
    trayFiltros         = { conCurso:null, anio:'', genero:'', estado:'', edadMin:'', edadMax:'' };

    contentDiv.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${cat.color} text-white">
            <h4 class="mb-0">
                <i class="bi bi-graph-up-arrow me-2"></i>Trayectoria de Participantes
            </h4>
        </div>
        <div class="card-body">

            <!-- KPI Cards -->
            <div id="trayKpiCards" class="row mb-4 g-3">
                <div class="col-12 text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Cargando resumen...</p>
                </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="d-flex justify-content-center mb-3">
                <div style="width:100%;max-width:560px;">
                    <div class="rp-search-group" id="traySearchGroup">
                        <button class="rp-search-icon-btn" type="button" tabindex="-1">
                            <i class="bi bi-search"></i>
                        </button>
                        <input type="text" class="rp-search-input" id="traySearchInput"
                               placeholder="Buscar por nombre, cédula, correo o estado..."
                               autocomplete="off">
                    </div>
                </div>
            </div>

            <!-- Panel de filtros -->
            <div id="trayFiltrosPanel" class="mb-3">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;
                            padding:1rem 1.25rem;">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span style="font-size:.74rem;font-weight:700;text-transform:uppercase;
                                     letter-spacing:.06em;color:#475569;">
                            <i class="bi bi-funnel-fill me-1"></i>Filtros
                        </span>
                        <button class="btn btn-sm btn-outline-secondary"
                                id="trayBtnLimpiarFiltros" style="font-size:.75rem;">
                            <i class="bi bi-x-circle me-1"></i>Limpiar todos
                        </button>
                    </div>

                    <div class="row g-2 align-items-end">

                        <!-- Participación en curso -->
                        <div class="col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Participación
                            </label>
                            <select class="form-select form-select-sm" id="trayFiltroConCurso">
                                <option value="">Todos</option>
                                <option value="1">Con registro de curso</option>
                                <option value="0">Sin registro de curso</option>
                            </select>
                        </div>

                        <!-- Año de registro (dinámico) -->
                        <div class="col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Año de registro
                            </label>
                            <select class="form-select form-select-sm" id="trayFiltroAnio">
                                <option value="">Todos los años</option>
                            </select>
                        </div>

                        <!-- Género -->
                        <div class="col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Género
                            </label>
                            <select class="form-select form-select-sm" id="trayFiltroGenero">
                                <option value="">Todos</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>

                        <!-- Estado (dinámico) -->
                        <div class="col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Estado
                            </label>
                            <select class="form-select form-select-sm" id="trayFiltroEstado">
                                <option value="">Todos</option>
                            </select>
                        </div>

                        <!-- Edad — contenedor unificado Desde/Hasta -->
                        <div class="col-sm-6 col-md-4 col-xl-2">
                            <label class="form-label mb-1"
                                   style="font-size:.7rem;font-weight:700;color:#64748b;
                                          text-transform:uppercase;letter-spacing:.05em;">
                                Edad
                            </label>
                            <div style="display:flex;border:1px solid #dee2e6;border-radius:.375rem;
                                        overflow:hidden;background:#fff;">
                                <input type="number" id="trayFiltroEdadMin"
                                       min="0" max="120" placeholder="Desde"
                                       style="flex:1;min-width:0;border:none;outline:none;
                                              padding:.25rem .5rem;font-size:.875rem;
                                              background:transparent;">
                                <div style="width:1px;background:#dee2e6;flex-shrink:0;"></div>
                                <input type="number" id="trayFiltroEdadMax"
                                       min="0" max="120" placeholder="Hasta"
                                       style="flex:1;min-width:0;border:none;outline:none;
                                              padding:.25rem .5rem;font-size:.875rem;
                                              background:transparent;">
                            </div>
                        </div>

                    </div>

                    <!-- Chips de filtros activos -->
                    <div id="trayFiltrosActivos" class="d-flex flex-wrap gap-2 mt-3"
                         style="min-height:22px;"></div>
                </div>
            </div>

            <!-- Tabla -->
            <div id="trayTablaWrapper">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Cargando participantes...</p>
                </div>
            </div>

            <!-- Panel detalle -->
            <div id="trayDetallePanel" style="display:none;" class="mt-4"></div>
        </div>
    </div>`;

    trayInicializarBusqueda();
    trayInicializarFiltros();

    try {
        const [kpiData, tablaData] = await Promise.all([
            fetch(`${API_TRAY_URL}?accion=trayectoria_resumen`)
                .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); }),
            fetch(`${API_TRAY_URL}?accion=trayectoria_participantes`)
                .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        ]);

        if (kpiData?.error)   throw new Error(kpiData.error);
        if (tablaData?.error) throw new Error(tablaData.error);

        // Normalizar estados en los datos cargados
        trayDatosTabla = (Array.isArray(tablaData) ? tablaData : []).map(p => ({
            ...p,
            estado: trayNormalizarEstado(p.estado)
        }));

        // Poblar selects dinámicos con valores reales
        trayPoblarSelectAnios(trayExtraerAnios(trayDatosTabla));
        trayPoblarSelectEstados(trayExtraerEstados(trayDatosTabla));

        trayRenderKPI(kpiData);
        trayRenderTabla();

    } catch (err) {
        trayMostrarError(document.getElementById('trayKpiCards'),     String(err));
        trayMostrarError(document.getElementById('trayTablaWrapper'), String(err));
    }
}

// ── Poblar selects dinámicos ───────────────────────────────────────

function trayPoblarSelectAnios(anios) {
    const sel = document.getElementById('trayFiltroAnio');
    if (!sel) return;
    anios.forEach(a => {
        const opt = document.createElement('option');
        opt.value       = a;
        opt.textContent = a;
        sel.appendChild(opt);
    });
}

function trayPoblarSelectEstados(estados) {
    const sel = document.getElementById('trayFiltroEstado');
    if (!sel) return;
    estados.forEach(e => {
        const opt = document.createElement('option');
        opt.value       = e;
        opt.textContent = e;
        sel.appendChild(opt);
    });
}

// ── KPI Cards ──────────────────────────────────────────────────────

function trayRenderKPI(data) {
    const el = document.getElementById('trayKpiCards');
    if (!el) return;

    const cards = [
        {
            conCurso: null,  valor: data.total     ?? '—',
            label:'Participantes Totales',    sub:'registrados en el sistema',
            bgCard:'#e7f0ff', borderCard:'#0d6efd55',
            colorNum:'#0a4fc4', colorLabel:'#0a4fc4', colorSub:'#4a6fa5',
            icon:'bi-people-fill', iconColor:'#0d6efd', rawKey:'null'
        },
        {
            conCurso: true,  valor: data.activos   ?? '—',
            label:'Participaron en un Curso', sub:'con al menos 1 inscripción',
            bgCard:'#e9f7ef', borderCard:'#19875455',
            colorNum:'#0f6840', colorLabel:'#0f6840', colorSub:'#3a7a56',
            icon:'bi-person-check-fill', iconColor:'#198754', rawKey:'true'
        },
        {
            conCurso: false, valor: data.sin_curso ?? '—',
            label:'Sin Registro de Curso',    sub:'sin ninguna inscripción',
            bgCard:'#fff8e1', borderCard:'#ffc10755',
            colorNum:'#856404', colorLabel:'#856404', colorSub:'#a07a10',
            icon:'bi-person-dash-fill', iconColor:'#ffc107', rawKey:'false'
        }
    ];

    el.innerHTML = cards.map(c => `
        <div class="col-md-4">
            <div class="tray-kpi-card h-100" data-raw-key="${c.rawKey}"
                 role="button" tabindex="0"
                 style="cursor:pointer;border-radius:16px;border:2px solid ${c.borderCard};
                        background:${c.bgCard};padding:1.5rem 1.25rem;text-align:center;
                        transition:transform .18s,box-shadow .18s;
                        box-shadow:0 2px 10px rgba(0,0,0,.06);user-select:none;
                        position:relative;overflow:hidden;">
                <span class="tray-kpi-check" style="display:none;position:absolute;
                      top:10px;right:12px;font-size:1rem;color:${c.colorNum};">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <div class="mb-2">
                    <i class="bi ${c.icon}"
                       style="font-size:2rem;color:${c.iconColor};opacity:.9;"></i>
                </div>
                <div style="font-family:var(--rp-mono);font-size:2.4rem;font-weight:700;
                            letter-spacing:-.03em;line-height:1;color:${c.colorNum};
                            margin-bottom:.35rem;">
                    ${typeof c.valor === 'number' ? c.valor.toLocaleString() : c.valor}
                </div>
                <div style="font-weight:700;font-size:.88rem;color:${c.colorLabel};
                            text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">
                    ${c.label}
                </div>
                <div style="font-size:.74rem;color:${c.colorSub};">${c.sub}</div>
            </div>
        </div>`
    ).join('');

    el.querySelectorAll('.tray-kpi-card').forEach(card => {
        const activar = () => {
            const raw = card.dataset.rawKey;
            const val = raw === 'null' ? null : raw === 'true';

            const esMismoFiltro = (trayFiltros.conCurso === null && raw === 'null') ||
                                  (trayFiltros.conCurso === true  && raw === 'true')  ||
                                  (trayFiltros.conCurso === false && raw === 'false');

            if (esMismoFiltro && raw !== 'null') {
                trayFiltros.conCurso = null;
                el.querySelectorAll('.tray-kpi-card').forEach(c => trayDesactivarCard(c));
                const s = document.getElementById('trayFiltroConCurso');
                if (s) s.value = '';
            } else {
                trayFiltros.conCurso = val;
                el.querySelectorAll('.tray-kpi-card').forEach(c =>
                    c.dataset.rawKey === raw ? trayActivarCard(c) : trayDesactivarCard(c)
                );
                const s = document.getElementById('trayFiltroConCurso');
                if (s) s.value = raw === 'null' ? '' : (val ? '1' : '0');
            }
            trayPaginaActual = 1;
            trayActualizarChipsFiltros();
            trayRenderTabla();
        };
        card.addEventListener('click', activar);
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') activar();
        });
    });
}

function trayActivarCard(card) {
    card.style.transform   = 'translateY(-4px)';
    card.style.boxShadow   = '0 8px 24px rgba(0,0,0,.15)';
    card.style.borderWidth = '2.5px';
    const ch = card.querySelector('.tray-kpi-check');
    if (ch) ch.style.display = 'inline';
}
function trayDesactivarCard(card) {
    card.style.transform   = '';
    card.style.boxShadow   = '0 2px 10px rgba(0,0,0,.06)';
    card.style.borderWidth = '2px';
    const ch = card.querySelector('.tray-kpi-check');
    if (ch) ch.style.display = 'none';
}

function traySincronizarCards() {
    const el = document.getElementById('trayKpiCards');
    if (!el) return;
    const cur = trayFiltros.conCurso;
    el.querySelectorAll('.tray-kpi-card').forEach(card => {
        const raw = card.dataset.rawKey;
        const val = raw === 'null' ? null : raw === 'true';
        const activa = (cur === null && raw === 'null') ? false :
                       JSON.stringify(cur) === JSON.stringify(val);
        activa ? trayActivarCard(card) : trayDesactivarCard(card);
    });
}

// ── Sistema de filtros ─────────────────────────────────────────────

function trayInicializarFiltros() {
    document.getElementById('trayBtnLimpiarFiltros')?.addEventListener('click', trayLimpiarFiltros);

    const bind = (id, campo, transform) => {
        document.getElementById(id)?.addEventListener('change', e => {
            trayFiltros[campo] = transform ? transform(e.target.value) : e.target.value;
            if (campo === 'conCurso') traySincronizarCards();
            trayPaginaActual = 1;
            trayActualizarChipsFiltros();
            trayRenderTabla();
        });
    };

    bind('trayFiltroConCurso', 'conCurso', v => v === '' ? null : v === '1');
    bind('trayFiltroAnio',    'anio');
    bind('trayFiltroGenero',  'genero');
    bind('trayFiltroEstado',  'estado');

    ['trayFiltroEdadMin', 'trayFiltroEdadMax'].forEach(id => {
        let t = null;
        document.getElementById(id)?.addEventListener('input', e => {
            clearTimeout(t);
            t = setTimeout(() => {
                trayFiltros[id === 'trayFiltroEdadMin' ? 'edadMin' : 'edadMax'] = e.target.value.trim();
                trayPaginaActual = 1;
                trayActualizarChipsFiltros();
                trayRenderTabla();
            }, 350);
        });
    });
}

function trayLimpiarFiltros() {
    trayFiltros = { conCurso:null, anio:'', genero:'', estado:'', edadMin:'', edadMax:'' };
    ['trayFiltroConCurso','trayFiltroAnio','trayFiltroGenero',
     'trayFiltroEstado','trayFiltroEdadMin','trayFiltroEdadMax'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    traySincronizarCards();
    trayPaginaActual = 1;
    trayActualizarChipsFiltros();
    trayRenderTabla();
}

function trayActualizarChipsFiltros() {
    const contenedor = document.getElementById('trayFiltrosActivos');
    if (!contenedor) return;

    const chips = [];
    const selConCurso = { null:'', true:'Con registro de curso', false:'Sin registro de curso' };
    if (trayFiltros.conCurso !== null) chips.push({ label: selConCurso[trayFiltros.conCurso], campo:'conCurso' });
    if (trayFiltros.anio)    chips.push({ label:`Año: ${trayFiltros.anio}`,        campo:'anio'    });
    if (trayFiltros.genero)  chips.push({ label:`Género: ${trayFiltros.genero}`,   campo:'genero'  });
    if (trayFiltros.estado)  chips.push({ label:`Estado: ${trayFiltros.estado}`,   campo:'estado'  });
    if (trayFiltros.edadMin) chips.push({ label:`Edad ≥ ${trayFiltros.edadMin}`,   campo:'edadMin' });
    if (trayFiltros.edadMax) chips.push({ label:`Edad ≤ ${trayFiltros.edadMax}`,   campo:'edadMax' });

    contenedor.innerHTML = chips.map(ch => `
        <span style="display:inline-flex;align-items:center;gap:.35rem;
                     background:#e7f0ff;border:1px solid #0d6efd44;color:#0a4fc4;
                     font-size:.72rem;font-weight:600;padding:.25em .7em;border-radius:999px;">
            ${ch.label}
            <button data-chip-campo="${ch.campo}"
                    style="background:none;border:none;padding:0;line-height:1;
                           cursor:pointer;color:#0a4fc4;font-size:.75rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </span>`
    ).join('');

    contenedor.querySelectorAll('[data-chip-campo]').forEach(btn => {
        btn.addEventListener('click', () => {
            const campo = btn.dataset.chipCampo;
            const mapId = {
                conCurso:'trayFiltroConCurso', anio:'trayFiltroAnio',
                genero:'trayFiltroGenero', estado:'trayFiltroEstado',
                edadMin:'trayFiltroEdadMin', edadMax:'trayFiltroEdadMax'
            };
            trayFiltros[campo] = campo === 'conCurso' ? null : '';
            const el = document.getElementById(mapId[campo]);
            if (el) el.value = '';
            if (campo === 'conCurso') traySincronizarCards();
            trayPaginaActual = 1;
            trayActualizarChipsFiltros();
            trayRenderTabla();
        });
    });
}

// ── Filtrado de datos ──────────────────────────────────────────────

function trayDatosFiltrados() {
    let datos = [...trayDatosTabla];

    if (trayFiltros.conCurso === true)  datos = datos.filter(p => parseInt(p.has_curso) === 1);
    if (trayFiltros.conCurso === false) datos = datos.filter(p => parseInt(p.has_curso) === 0);

    if (trayFiltros.anio)
        datos = datos.filter(p => p.fecha_registro?.startsWith(trayFiltros.anio));

    if (trayFiltros.genero)
        datos = datos.filter(p => (p.genero ?? '') === trayFiltros.genero);

    if (trayFiltros.estado)
        datos = datos.filter(p => (p.estado ?? '') === trayFiltros.estado);

    if (trayFiltros.edadMin !== '') {
        const min = parseInt(trayFiltros.edadMin);
        if (!isNaN(min)) datos = datos.filter(p => parseInt(p.edad ?? 0) >= min);
    }
    if (trayFiltros.edadMax !== '') {
        const max = parseInt(trayFiltros.edadMax);
        if (!isNaN(max)) datos = datos.filter(p => parseInt(p.edad ?? 0) <= max);
    }

    if (trayBusqueda.trim()) {
        const q = trayBusqueda.toLowerCase();
        datos = datos.filter(p =>
            String(p.cedula      ?? '').toLowerCase().includes(q) ||
            (p.primer_nombre     ?? '').toLowerCase().includes(q) ||
            (p.primer_apellido   ?? '').toLowerCase().includes(q) ||
            (p.correo            ?? '').toLowerCase().includes(q) ||
            String(p.telefono    ?? '').toLowerCase().includes(q) ||
            (p.estado            ?? '').toLowerCase().includes(q) ||
            (p.grado_institucion ?? '').toLowerCase().includes(q)
        );
    }

    if (trayOrdenCol) {
        datos.sort((a, b) => {
            const va = String(a[trayOrdenCol] ?? '').toLowerCase();
            const vb = String(b[trayOrdenCol] ?? '').toLowerCase();
            return (trayOrdenAsc ? 1 : -1) * va.localeCompare(vb, 'es', { numeric:true });
        });
    }
    return datos;
}

// ── Tabla ──────────────────────────────────────────────────────────

function trayRenderTabla() {
    const wrapper = document.getElementById('trayTablaWrapper');
    if (!wrapper) return;

    const datos     = trayDatosFiltrados();
    const total     = datos.length;
    const totalPags = Math.max(1, Math.ceil(total / trayFilasPorPagina));
    if (trayPaginaActual > totalPags) trayPaginaActual = 1;
    const inicio = (trayPaginaActual - 1) * trayFilasPorPagina;
    const pagina = datos.slice(inicio, inicio + trayFilasPorPagina);

    const thIcon = col => trayOrdenCol !== col
        ? `<i class="bi bi-chevron-expand text-muted ms-1" style="font-size:.6rem;opacity:.4;"></i>`
        : trayOrdenAsc
            ? `<i class="bi bi-chevron-up ms-1 text-primary" style="font-size:.6rem;"></i>`
            : `<i class="bi bi-chevron-down ms-1 text-primary" style="font-size:.6rem;"></i>`;

    const th = (col, label) =>
        `<th class="tray-th" data-col="${col}"
             style="cursor:pointer;white-space:nowrap;user-select:none;">
            ${label}${thIcon(col)}
         </th>`;

    const filas = pagina.length === 0
        ? `<tr><td colspan="9" class="text-center text-muted py-5">
               <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
               No se encontraron participantes
           </td></tr>`
        : pagina.map(p => {
            const activo = trayParticipanteAct === String(p.cedula);
            return `<tr class="tray-fila${activo?' table-active':''}"
                        data-cedula="${p.cedula}"
                        style="cursor:pointer;transition:background .12s;">
                <td><strong style="font-family:var(--rp-mono);font-size:.85rem;">
                    ${p.cedula ?? '—'}
                </strong></td>
                <td>${p.primer_nombre   ?? '—'}</td>
                <td>${p.primer_apellido ?? '—'}</td>
                <td>${p.genero         ?? '—'}</td>
                <td class="text-center">${p.edad ?? '—'}</td>
                <td style="font-family:var(--rp-mono);font-size:.8rem;">
                    ${trayFono(p.telefono)}
                </td>
                <td class="text-truncate" style="max-width:170px;" title="${p.correo ?? ''}">
                    ${p.correo ?? '—'}
                </td>
                <td style="white-space:nowrap;font-size:.8rem;">
                    ${trayFecha(p.fecha_registro)}
                </td>
                <td class="text-center">${trayBadgeEstado(p.estado)}</td>
            </tr>`;
        }).join('');

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="d-flex align-items-center justify-content-between
                            flex-wrap gap-2 p-3 border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-table me-1 text-primary"></i>Participantes
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-1"
                              style="font-size:.75rem;font-weight:600;">
                            ${total.toLocaleString()}
                        </span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Mostrar</span>
                        <select class="form-select form-select-sm" id="trayFilasSel"
                                style="width:72px;">
                            <option value="10"  ${trayFilasPorPagina===10  ?'selected':''}>10</option>
                            <option value="20"  ${trayFilasPorPagina===20  ?'selected':''}>20</option>
                            <option value="50"  ${trayFilasPorPagina===50  ?'selected':''}>50</option>
                            <option value="100" ${trayFilasPorPagina===100 ?'selected':''}>100</option>
                        </select>
                        <span class="text-muted small">por página</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                ${th('cedula','Cédula')}
                                ${th('primer_nombre','Nombre')}
                                ${th('primer_apellido','Apellido')}
                                ${th('genero','Género')}
                                ${th('edad','Edad')}
                                ${th('telefono','Teléfono')}
                                ${th('correo','Correo')}
                                ${th('fecha_registro','Registro')}
                                <th class="text-center" style="white-space:nowrap;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>${filas}</tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center justify-content-between
                            flex-wrap gap-2 p-3 border-top">
                    <small class="text-muted">
                        Mostrando
                        <strong>${total===0?0:inicio+1}–${
                            Math.min(inicio+trayFilasPorPagina,total)
                        }</strong>
                        de <strong>${total.toLocaleString()}</strong>
                    </small>
                    ${trayRenderPaginacion(totalPags)}
                </div>
            </div>
        </div>`;

    wrapper.querySelectorAll('.tray-th').forEach(thEl => {
        thEl.addEventListener('click', () => {
            const col = thEl.dataset.col;
            trayOrdenAsc   = trayOrdenCol === col ? !trayOrdenAsc : true;
            trayOrdenCol   = col;
            trayPaginaActual = 1;
            trayRenderTabla();
        });
    });
    document.getElementById('trayFilasSel')?.addEventListener('change', e => {
        trayFilasPorPagina = parseInt(e.target.value);
        trayPaginaActual   = 1;
        trayRenderTabla();
    });
    wrapper.querySelectorAll('.tray-pag-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const pag = parseInt(btn.dataset.pag);
            if (!isNaN(pag)) { trayPaginaActual = pag; trayRenderTabla(); }
        });
    });
    wrapper.querySelectorAll('.tray-fila').forEach(fila => {
        fila.addEventListener('click', () => {
            const cedula = fila.dataset.cedula;
            if (trayParticipanteAct === cedula) {
                trayParticipanteAct = null;
                trayRenderTabla();
                trayOcultarDetalle();
            } else {
                trayParticipanteAct = cedula;
                trayRenderTabla();
                trayAbrirDetalle(cedula);
            }
        });
    });
}

// ── Paginación ─────────────────────────────────────────────────────

function trayRenderPaginacion(totalPags) {
    if (totalPags <= 1) return '';
    const cur = trayPaginaActual, delta = 2;
    const pages = [];
    for (let i = 1; i <= totalPags; i++)
        if (i===1||i===totalPags||(i>=cur-delta&&i<=cur+delta)) pages.push(i);
    const items = [];
    let prev = null;
    for (const p of pages) {
        if (prev!==null&&p-prev>1) items.push('...');
        items.push(p); prev=p;
    }
    const btnP = cur>1
        ? `<button class="btn btn-sm btn-outline-secondary tray-pag-btn" data-pag="${cur-1}"><i class="bi bi-chevron-left"></i></button>`
        : `<button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-chevron-left"></i></button>`;
    const btnN = cur<totalPags
        ? `<button class="btn btn-sm btn-outline-secondary tray-pag-btn" data-pag="${cur+1}"><i class="bi bi-chevron-right"></i></button>`
        : `<button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-chevron-right"></i></button>`;
    const nums = items.map(item =>
        item==='...'
            ? `<span class="px-1 text-muted" style="line-height:2;">…</span>`
            : `<button class="btn btn-sm ${item===cur?'btn-primary':'btn-outline-secondary'} tray-pag-btn" data-pag="${item}">${item}</button>`
    ).join('');
    return `<nav><div class="d-flex align-items-center gap-1">${btnP}${nums}${btnN}</div></nav>`;
}

// ── Panel detalle ──────────────────────────────────────────────────

function trayOcultarDetalle() {
    const panel = document.getElementById('trayDetallePanel');
    if (panel) { panel.style.display = 'none'; panel.innerHTML = ''; }
}

async function trayAbrirDetalle(cedula) {
    const panel = document.getElementById('trayDetallePanel');
    if (!panel) return;

    panel.style.display = 'block';
    panel.innerHTML = `<div class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted small">Cargando datos del participante...</p>
    </div>`;
    setTimeout(() => panel.scrollIntoView({ behavior:'smooth', block:'start' }), 80);

    try {
        const res  = await fetch(`${API_TRAY_URL}?accion=detalle_participante&cedula=${cedula}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        // Normalizar estado en el detalle también
        data.estado = trayNormalizarEstado(data.estado);
        trayRenderDetalle(panel, data);
    } catch (err) {
        panel.innerHTML = `<div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Error al cargar el detalle: ${err.message}
        </div>`;
    }
}

function trayRenderDetalle(panel, d) {
    const cursos    = Array.isArray(d.cursos)    ? d.cursos    : [];
    const telefonos = Array.isArray(d.telefonos) ? d.telefonos : [];

    const nombreCompleto = [d.primer_nombre, d.segundo_nombre,
                            d.primer_apellido, d.segundo_apellido]
        .filter(Boolean).join(' ');
    const initiales  = ((d.primer_nombre?.[0]??'')+(d.primer_apellido?.[0]??'')).toUpperCase();
    const horasTotal = cursos.reduce((s,c) => s + parseFloat(c.horas_totales||0), 0);

    const filaCursos = cursos.length === 0
        ? `<tr><td colspan="8" class="text-center text-muted py-3">
               Sin cursos registrados
           </td></tr>`
        : cursos.map(c => `
            <tr class="tray-curso-fila" data-id-curso="${c.id_curso ?? ''}"
                style="cursor:pointer;transition:background .12s;"
                title="Abrir en módulo Cursos (nueva pestaña)">
                <td style="font-weight:600;font-size:.83rem;">${c.nombre_curso ?? '—'}</td>
                <td style="font-size:.8rem;white-space:nowrap;">
                    ${trayFecha(c.fecha_inscripcion)}</td>
                <td style="font-size:.8rem;white-space:nowrap;">
                    ${trayFecha(c.fecha_inicio)}</td>
                <td style="font-size:.8rem;white-space:nowrap;">
                    ${trayFecha(c.fecha_fin)}</td>
                <td>
                    <span style="font-size:.75rem;font-weight:600;padding:.2em .65em;
                                 border-radius:999px;display:inline-block;white-space:nowrap;
                                 background:${c.turno==='Mañana'?'#fff8e1':'#e7f0ff'};
                                 color:${c.turno==='Mañana'?'#856404':'#0a4fc4'};
                                 border:1px solid ${c.turno==='Mañana'?'#ffc10766':'#0d6efd44'};">
                        <i class="bi ${c.turno==='Mañana'?'bi-sun':'bi-moon'} me-1"></i>
                        ${c.turno ?? '—'}
                    </span>
                </td>
                <td style="font-size:.8rem;">${c.nombre_modalidad ?? '—'}</td>
                <td class="text-center">${trayBadgeEstatusCurso(c.estatus_participante)}</td>
                <td class="text-center">
                    <span style="display:inline-flex;align-items:center;gap:.3rem;
                                 font-size:.72rem;font-weight:600;color:#0a4fc4;
                                 white-space:nowrap;">
                        <i class="bi bi-box-arrow-up-right"></i>Ver
                    </span>
                </td>
            </tr>`
        ).join('');

    panel.innerHTML = `
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <!-- Cabecera -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2
                        p-3 border-bottom" style="background:#f8fafc;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;border-radius:50%;
                                background:#e7f0ff;border:2px solid #0d6efd44;
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.15rem;font-weight:700;color:#0a4fc4;
                                flex-shrink:0;">
                        ${initiales}
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color:#0f1924;">${nombreCompleto}</h5>
                        <span style="font-family:var(--rp-mono);font-size:.82rem;color:#475569;">
                            CI: ${d.cedula ?? '—'}
                        </span>
                        <span class="ms-2">${trayBadgeEstado(d.estado)}</span>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary"
                        onclick="trayCerrarDetalle()">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>

            <div class="p-3 p-md-4">

                <!-- Información personal -->
                <h6 class="fw-bold mb-3"
                    style="color:#0a4fc4;letter-spacing:.04em;
                           text-transform:uppercase;font-size:.78rem;">
                    <i class="bi bi-person-fill me-2"></i>Información Personal
                </h6>
                <div class="row g-3 mb-4">
                    ${trayInfoCard('Cédula',          String(d.cedula ?? '—'),             'bi-card-text')}
                    ${trayInfoCard('Nombre completo',  nombreCompleto || '—',               'bi-person')}
                    ${trayInfoCard('Género',           d.genero ?? '—',                     'bi-gender-ambiguous')}
                    ${trayInfoCard('Edad',             d.edad ? `${d.edad} años` : '—',    'bi-calendar2')}
                    ${trayInfoCard('Fecha nacimiento', trayFecha(d.fecha_nacimiento),       'bi-cake')}
                    ${trayInfoCard('Fecha registro',   trayFecha(d.fecha_registro),         'bi-calendar-check')}
                    ${trayInfoCard('Correo',           d.correo ?? '—',                    'bi-envelope')}
                    ${trayInfoCard('Discapacidad',     d.nombre_discapacidad ?? 'Ninguna', 'bi-heart-pulse')}
                    ${trayInfoCard('Teléfonos',
                        telefonos.length
                            ? telefonos.map(t =>
                                `${trayFono(t.numero_de_telefono)} <small style="color:#64748b;">(${t.tipo_de_telefono})</small>`
                              ).join('<br>')
                            : '—',
                        'bi-telephone')}
                </div>

                <hr class="my-3">

                <!-- Información académica -->
                <h6 class="fw-bold mb-3"
                    style="color:#198754;letter-spacing:.04em;
                           text-transform:uppercase;font-size:.78rem;">
                    <i class="bi bi-mortarboard-fill me-2"></i>Información Académica
                </h6>
                <div class="row g-3 mb-4">
                    ${trayInfoCard('Nivel de instrucción',
                        d.grado_institucion ?? '—', 'bi-book')}
                    ${trayInfoCard('Total de cursos',
                        String(cursos.length), 'bi-journal-bookmark')}
                    ${trayInfoCard('Cursos aprobados',
                        String(cursos.filter(c=>c.estatus_participante==='Aprobado').length),
                        'bi-patch-check')}
                    ${trayInfoCard('Horas acumuladas',
                        horasTotal.toFixed(0) + ' hrs', 'bi-clock')}
                </div>

                <!-- Tabla cursos -->
                <div class="table-responsive mb-4">
                    <p class="text-muted mb-2" style="font-size:.75rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Haz clic en una fila para abrir ese curso en el módulo de Cursos
                        (nueva pestaña).
                    </p>
                    <table class="table table-striped table-hover table-sm align-middle mb-0"
                           style="font-size:.83rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th style="white-space:nowrap;">Inscripción</th>
                                <th style="white-space:nowrap;">Inicio</th>
                                <th style="white-space:nowrap;">Fin</th>
                                <th>Turno</th>
                                <th>Modalidad</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Ir</th>
                            </tr>
                        </thead>
                        <tbody>${filaCursos}</tbody>
                    </table>
                </div>

                <hr class="my-3">

                <!-- Gantt -->
                <h6 class="fw-bold mb-1"
                    style="color:#6610f2;letter-spacing:.04em;
                           text-transform:uppercase;font-size:.78rem;">
                    <i class="bi bi-bar-chart-steps me-2"></i>Línea de Tiempo de Cursos
                </h6>
                <p class="text-muted mb-3" style="font-size:.78rem;">
                    Duración de cada curso (inicio → fin). Color por turno:
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;
                                 background:#f59e0b;margin:0 3px 0 6px;
                                 vertical-align:middle;"></span>Mañana
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;
                                 background:#3b82f6;margin:0 3px 0 6px;
                                 vertical-align:middle;"></span>Tarde
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;
                                 background:#94a3b8;margin:0 3px 0 6px;
                                 vertical-align:middle;"></span>Sin turno
                </p>
                <div id="trayGanttWrapper"
                     style="width:100%;overflow-x:auto;border-radius:10px;
                            border:1px solid #e2e8f0;background:#fafafa;">
                    <canvas id="trayGanttCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>`;

    // Navegación a Cursos en nueva pestaña
    panel.querySelectorAll('.tray-curso-fila').forEach(fila => {
        fila.addEventListener('mouseenter', () => { fila.style.background = '#e7f0ff'; });
        fila.addEventListener('mouseleave', () => { fila.style.background = ''; });
        fila.addEventListener('click', () => {
            const idCurso = fila.dataset.idCurso;
            if (!idCurso) return;
            fila.style.background = '#bfdbfe';
            setTimeout(() => {
                window.open(`cursos.php?buscar=${encodeURIComponent(idCurso)}`, '_blank');
                fila.style.background = '';
            }, 160);
        });
    });

    setTimeout(() => trayRenderGantt(cursos), 80);
}

function trayInfoCard(label, valor, icon) {
    return `
    <div class="col-sm-6 col-lg-4">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;
                    padding:.75rem 1rem;height:100%;">
            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;
                        letter-spacing:.06em;color:#64748b;margin-bottom:.3rem;">
                <i class="bi ${icon} me-1"></i>${label}
            </div>
            <div style="font-size:.88rem;color:#0f172a;font-weight:500;line-height:1.5;">
                ${valor}
            </div>
        </div>
    </div>`;
}

// ── Gantt Canvas 2D ────────────────────────────────────────────────

function trayRenderGantt(cursos) {
    const wrapper = document.getElementById('trayGanttWrapper');
    const canvas  = document.getElementById('trayGanttCanvas');
    if (!canvas || !wrapper) return;

    const validos = cursos.filter(c => trayToMs(c.fecha_inicio) && trayToMs(c.fecha_fin));

    if (!validos.length) {
        wrapper.innerHTML = `<p class="text-muted text-center py-4" style="font-size:.85rem;">
            Sin cursos con fechas registradas para mostrar
        </p>`;
        return;
    }

    const DPR       = window.devicePixelRatio || 1;
    const BAR_H     = 28;
    const ROW_PAD   = 14;
    const AXIS_LEFT = 160;
    const AXIS_BOT  = 36;
    const PAD_TOP   = 16;
    const PAD_RIGHT = 20;
    const n         = validos.length;
    const totalH    = PAD_TOP + n * (BAR_H + ROW_PAD) + ROW_PAD + AXIS_BOT;
    const totalW    = Math.max(wrapper.clientWidth || 600, 500);

    canvas.style.width  = totalW + 'px';
    canvas.style.height = totalH + 'px';
    canvas.width        = totalW * DPR;
    canvas.height       = totalH * DPR;

    const ctx = canvas.getContext('2d');
    ctx.scale(DPR, DPR);

    const todosMs = validos.flatMap(c => [trayToMs(c.fecha_inicio), trayToMs(c.fecha_fin)]);
    const minMs   = Math.min(...todosMs);
    const maxMs   = Math.max(...todosMs);
    const dMin    = new Date(minMs);
    const dMax    = new Date(maxMs);
    const escMin  = Date.UTC(dMin.getUTCFullYear(), dMin.getUTCMonth(), 1);
    const escMax  = Date.UTC(dMax.getUTCFullYear(), dMax.getUTCMonth() + 1, 1);
    const escSpan = escMax - escMin;
    const chartW  = totalW - AXIS_LEFT - PAD_RIGHT;
    const msToX   = ms => AXIS_LEFT + ((ms - escMin) / escSpan) * chartW;

    // Ticks mensuales
    const ticks = [];
    const td = new Date(escMin);
    while (td.getTime() < escMax) { ticks.push(td.getTime()); td.setUTCMonth(td.getUTCMonth() + 1); }
    ticks.push(escMax);

    const gridH = PAD_TOP + n * (BAR_H + ROW_PAD) + ROW_PAD;

    // Fondo
    ctx.fillStyle = '#fafafa';
    ctx.fillRect(0, 0, totalW, totalH);

    // Grid vertical
    ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 1;
    ticks.forEach(t => {
        const x = Math.round(msToX(t)) + 0.5;
        ctx.beginPath(); ctx.moveTo(x, PAD_TOP); ctx.lineTo(x, gridH); ctx.stroke();
    });

    // Etiquetas eje X
    ctx.fillStyle    = '#64748b';
    ctx.font         = '11px system-ui,sans-serif';
    ctx.textAlign    = 'center';
    ctx.textBaseline = 'top';
    let lastX = -Infinity;
    ticks.forEach(t => {
        const x = msToX(t);
        if (x - lastX >= 58) {
            const dd = new Date(t);
            ctx.fillText(`${MESES_CORTOS[dd.getUTCMonth()]} ${dd.getUTCFullYear()}`, x, gridH + 6);
            lastX = x;
        }
    });

    const colTurno = t => t === 'Mañana'
        ? { fill:'rgba(245,158,11,.22)', stroke:'#f59e0b', text:'#92400e' }
        : t === 'Tarde'
            ? { fill:'rgba(59,130,246,.18)', stroke:'#3b82f6', text:'#1e40af' }
            : { fill:'rgba(148,163,184,.2)', stroke:'#94a3b8', text:'#334155' };

    validos.forEach((c, i) => {
        const x1   = msToX(trayToMs(c.fecha_inicio));
        const x2   = msToX(trayToMs(c.fecha_fin));
        const barW = Math.max(x2 - x1, 6);
        const y    = PAD_TOP + ROW_PAD + i * (BAR_H + ROW_PAD);
        const col  = colTurno(c.turno);

        // Etiqueta eje Y
        ctx.fillStyle    = '#334155';
        ctx.font         = '12px system-ui,sans-serif';
        ctx.textAlign    = 'right';
        ctx.textBaseline = 'middle';
        let lbl = c.nombre_curso ?? '';
        const maxLblW = AXIS_LEFT - 10;
        if (ctx.measureText(lbl).width > maxLblW) {
            while (lbl.length && ctx.measureText(lbl + '…').width > maxLblW)
                lbl = lbl.slice(0, -1);
            lbl += '…';
        }
        ctx.fillText(lbl, AXIS_LEFT - 8, y + BAR_H / 2);

        // Barra
        ctx.fillStyle   = col.fill;
        ctx.strokeStyle = col.stroke;
        ctx.lineWidth   = 1.5;
        ctx.beginPath(); ctx.roundRect(x1, y, barW, BAR_H, 5);
        ctx.fill(); ctx.stroke();

        // Texto dentro de barra
        ctx.save();
        ctx.rect(x1 + 4, y, barW - 8, BAR_H); ctx.clip();
        ctx.fillStyle    = col.text;
        ctx.font         = 'bold 11px system-ui,sans-serif';
        ctx.textAlign    = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText(c.nombre_curso ?? '', x1 + 7, y + BAR_H / 2);
        ctx.restore();
    });

    // Línea base eje X
    ctx.strokeStyle = '#cbd5e1'; ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(AXIS_LEFT, gridH); ctx.lineTo(totalW - PAD_RIGHT, gridH);
    ctx.stroke();

    // Tooltip
    let tip = document.getElementById('trayGanttTooltip');
    if (tip) tip.remove();
    tip = document.createElement('div');
    tip.id = 'trayGanttTooltip';
    tip.style.cssText = `position:fixed;pointer-events:none;display:none;z-index:9999;
        background:#1e293b;color:#f1f5f9;font-size:12px;line-height:1.6;
        padding:8px 12px;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.3);
        max-width:240px;white-space:normal;`;
    document.body.appendChild(tip);

    canvas.onmousemove = e => {
        const rect = canvas.getBoundingClientRect();
        const mx = e.clientX - rect.left, my = e.clientY - rect.top;
        let found = false;
        validos.forEach((c, i) => {
            const x1 = msToX(trayToMs(c.fecha_inicio));
            const x2 = msToX(trayToMs(c.fecha_fin));
            const y  = PAD_TOP + ROW_PAD + i * (BAR_H + ROW_PAD);
            if (mx >= x1 && mx <= x2 && my >= y && my <= y + BAR_H) {
                found = true;
                tip.style.display = 'block';
                tip.style.left    = (e.clientX + 14) + 'px';
                tip.style.top     = (e.clientY - 10) + 'px';
                tip.innerHTML = `<strong>${c.nombre_curso}</strong><br>
                    Inicio: ${trayFecha(c.fecha_inicio)}<br>
                    Fin: ${trayFecha(c.fecha_fin)}<br>
                    Turno: ${c.turno ?? '—'}<br>
                    Estatus: ${c.estatus_participante ?? '—'}`;
                canvas.style.cursor = 'pointer';
            }
        });
        if (!found) { tip.style.display = 'none'; canvas.style.cursor = 'default'; }
    };
    canvas.onmouseleave = () => { tip.style.display = 'none'; canvas.style.cursor = 'default'; };
}

// ── Cerrar detalle ─────────────────────────────────────────────────

window.trayCerrarDetalle = function() {
    const tt = document.getElementById('trayGanttTooltip');
    if (tt) tt.remove();
    trayParticipanteAct = null;
    trayOcultarDetalle();
    trayRenderTabla();
};

// ── Buscador ───────────────────────────────────────────────────────

function trayInicializarBusqueda() {
    const input = document.getElementById('traySearchInput');
    const group = document.getElementById('traySearchGroup');
    if (!input) return;
    let timer = null;
    input.addEventListener('focus', () => group?.classList.add('rp-search-group--focused'));
    input.addEventListener('blur',  () => group?.classList.remove('rp-search-group--focused'));
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            trayBusqueda     = input.value;
            trayPaginaActual = 1;
            trayRenderTabla();
        }, 280);
    });
}

// ── Exports ────────────────────────────────────────────────────────
window.cargarTrayectoriaParticipantes = cargarTrayectoriaParticipantes;
console.log('RE-Participante-Trayectoria.js cargado');
// ========== REPORTE: CURSOS SIN DOCENTE ASIGNADO ==========
// Conectado a: api_reportes.php
// Acciones usadas: cursos_sin_docente, anios_disponibles
// Reemplaza a: RE-Docente-Inactivos.js

// ─── Estado global ────────────────────────────────────────────────────────────
const API_URL_SIN_DOC = '../controlador/Reporte/api_reportes.php';

let sinDocenteData        = [];
let graficaSinDocenteAmb  = null;
let graficaSinDocenteDias = null;
let panelSinDocActivo     = 'urgentes'; // 'urgentes' | 'proximos' | 'ambito'

// ─── Paleta ───────────────────────────────────────────────────────────────────
const COLOR_SINDOC = [
    { border: '#ef4444', bg: 'rgba(239,68,68,.75)'   },
    { border: '#f97316', bg: 'rgba(249,115,22,.75)'  },
    { border: '#f59e0b', bg: 'rgba(245,158,11,.75)'  },
    { border: '#3b82f6', bg: 'rgba(59,130,246,.75)'  },
    { border: '#8b5cf6', bg: 'rgba(139,92,246,.75)'  },
    { border: '#10b981', bg: 'rgba(16,185,129,.75)'  },
    { border: '#06b6d4', bg: 'rgba(6,182,212,.75)'   },
];

// ─── Helper fetch ─────────────────────────────────────────────────────────────
async function apiFetchSinDoc(accion, params = {}) {
    const url = new URL(API_URL_SIN_DOC, window.location.href);
    url.searchParams.set('accion', accion);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
    const resp = await fetch(url.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();
    if (data?.error) throw new Error(data.error);
    return data;
}

// ─── Días hasta inicio ────────────────────────────────────────────────────────
function diasHastaInicio(fechaInicio) {
    if (!fechaInicio) return null;
    const hoy   = new Date(); hoy.setHours(0,0,0,0);
    const inicio = new Date(fechaInicio); inicio.setHours(0,0,0,0);
    return Math.round((inicio - hoy) / (1000 * 60 * 60 * 24));
}

function urgenciaConfig(dias) {
    if (dias === null) return { label: 'Sin fecha',  cls: 'secondary', orden: 99 };
    if (dias < 0)      return { label: 'Vencido',    cls: 'dark',      orden: 0  };
    if (dias === 0)    return { label: 'Hoy',        cls: 'danger',    orden: 1  };
    if (dias <= 7)     return { label: `${dias}d`,   cls: 'danger',    orden: 2  };
    if (dias <= 15)    return { label: `${dias}d`,   cls: 'warning',   orden: 3  };
    if (dias <= 30)    return { label: `${dias}d`,   cls: 'primary',   orden: 4  };
    return              { label: `${dias}d`,          cls: 'success',   orden: 5  };
}

// ─── Función principal ────────────────────────────────────────────────────────
async function cargarCursosSinDocente() {
    const contentDiv  = document.getElementById('reportContent');
    const categoriaId = window.currentCategoryId || 2;
    const categoria   = (window.categoriasReportes || []).find(c => c.id === categoriaId)
        || { color: 'success', nombre: 'Cursos' };

    sinDocenteData        = [];
    graficaSinDocenteAmb  = null;
    graficaSinDocenteDias = null;
    panelSinDocActivo     = 'urgentes';

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Cursos sin Docente Asignado</h4>
                        <p class="mb-0 small opacity-75">Cursos en espera que aún no tienen docente — requieren atención</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="loadingSinDoc">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Consultando cursos sin docente...</p>
                </div>
                <div id="contenidoSinDoc" style="display:none;"></div>
                <div id="errorSinDoc" style="display:none;" class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="errorMsgSinDoc"></span>
                </div>
            </div>
        </div>`;

    try {
        const data = await apiFetchSinDoc('cursos_sin_docente');
        sinDocenteData = Array.isArray(data) ? data : [];
        renderSinDocente(sinDocenteData, categoria);
    } catch (err) {
        document.getElementById('loadingSinDoc').style.display = 'none';
        document.getElementById('errorSinDoc').style.display = 'block';
        document.getElementById('errorMsgSinDoc').textContent = `No se pudieron cargar los datos: ${err.message}`;
        console.error('[SinDocente]', err);
    }
}

// ─── Render principal ─────────────────────────────────────────────────────────
function renderSinDocente(data, categoria) {
    const loadEl = document.getElementById('loadingSinDoc');
    const contEl = document.getElementById('contenidoSinDoc');
    loadEl.style.display = 'none';
    contEl.style.display = 'block';

    // Clasificar
    const urgentes  = data.filter(c => { const d = diasHastaInicio(c.fecha_inicio); return d !== null && d <= 7; });
    const proximos  = data.filter(c => { const d = diasHastaInicio(c.fecha_inicio); return d !== null && d > 7 && d <= 30; });
    const sinFecha  = data.filter(c => !c.fecha_inicio);
    const resto     = data.filter(c => { const d = diasHastaInicio(c.fecha_inicio); return d !== null && d > 30; });

    // Agrupar por ámbito
    const porAmbito = {};
    data.forEach(c => {
        const amb = c.nombre_ambito || 'Sin ámbito';
        porAmbito[amb] = (porAmbito[amb] || 0) + 1;
    });

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;

    // KPI cards
    const kpiCards = [
        { tipo: 'urgentes', valor: urgentes.length,  label: 'Urgentes',      sub: 'Inician en ≤7 días',   color: 'danger'  },
        { tipo: 'proximos', valor: proximos.length,  label: 'Próximos',      sub: 'Inician en 8–30 días', color: 'warning' },
        { tipo: 'resto',    valor: resto.length,     label: 'Planificados',  sub: 'Inician en >30 días',  color: 'primary' },
        { tipo: 'ambito',   valor: data.length,      label: 'Total Pendientes', sub: `En ${Object.keys(porAmbito).length} ámbito(s)`, color: 'success' },
    ];

    contEl.innerHTML = `
        ${data.length === 0 ? `
            <div class="text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
                <h5 class="mt-3 text-success">¡Todo en orden!</h5>
                <p class="text-muted">No hay cursos en espera sin docente asignado.</p>
            </div>` : `

        <!-- KPI Cards -->
        <div class="row mb-1" id="sinDocKPIs">
            ${kpiCards.map(c => `
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-3 kpi-card sindoc-kpi-card" role="button" data-tipo="${c.tipo}">
                    <div class="card-body text-center py-3">
                        <h2 class="fw-bold text-${c.color} mt-1 mb-0">${c.valor}</h2>
                        <p class="rp-kpi-card-label">${c.label}</p>
                        <p class="rp-kpi-card-sub">${c.sub}</p>
                        <span class="badge bg-${c.color} bg-opacity-10 text-${c.color} small"
                              id="sinDocBadge-${c.tipo}">${svgDown}Ver detalle</span>
                    </div>
                </div>
            </div>`).join('')}
        </div>

        <!-- Panel colapsable -->
        <div id="sinDocPanelDetalle" style="display:none;" class="mb-4"></div>

        <!-- Gráficas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Cursos pendientes por Ámbito</h5>
                        <div style="position:relative;height:240px;">
                            <canvas id="graficaSinDocAmb"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Distribución por Urgencia</h5>
                        <div style="position:relative;height:240px;">
                            <canvas id="graficaSinDocDias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla principal -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0">Listado de Cursos sin Docente</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-success active"
                                            onclick="filtrarTablaSinDoc('todos', event)">
                                        <i class="bi bi-list me-1"></i>Todos (${data.length})
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="filtrarTablaSinDoc('urgentes', event)">
                                        <i class="bi bi-alarm me-1"></i>Urgentes (${urgentes.length})
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                            onclick="filtrarTablaSinDoc('proximos', event)">
                                        <i class="bi bi-calendar-event me-1"></i>Próximos (${proximos.length})
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaSinDocente">
                                <thead>
                                    <tr>
                                        <th>Urgencia</th>
                                        <th>Curso</th>
                                        <th>Fecha Inicio</th>
                                        <th>Horas</th>
                                        <th>Ámbito</th>
                                        <th>Participantes</th>
                                        <th>Turno</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaSinDocenteBody">
                                    ${data
                                        .slice()
                                        .sort((a, b) => {
                                            const da = diasHastaInicio(a.fecha_inicio) ?? 999;
                                            const db = diasHastaInicio(b.fecha_inicio) ?? 999;
                                            return da - db;
                                        })
                                        .map(c => renderFilaSinDoc(c))
                                        .join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>`}`;

    if (data.length === 0) return;

    // Gráfica por ámbito
    renderGraficaSinDocAmb(porAmbito);
    // Gráfica por urgencia
    renderGraficaSinDocDias(urgentes.length, proximos.length, resto.length, sinFecha.length);

    // Eventos KPI
    contEl.querySelectorAll('.sindoc-kpi-card').forEach(card =>
        card.addEventListener('click', () => sinDocTogglePanel(card.dataset.tipo))
    );

    // Abrir panel urgentes por defecto si hay casos urgentes
    if (urgentes.length > 0) {
        setTimeout(() => sinDocTogglePanel('urgentes'), 300);
    }
}

// ─── Toggle panel ─────────────────────────────────────────────────────────────
function sinDocTogglePanel(tipo) {
    const wrapper = document.getElementById('sinDocPanelDetalle');
    if (!wrapper) return;

    const svgDown = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const svgUp   = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    ['urgentes','proximos','resto','ambito'].forEach(t => {
        const b = document.getElementById(`sinDocBadge-${t}`);
        if (b) b.innerHTML = `${svgDown}Ver detalle`;
    });

    if (panelSinDocActivo === tipo && wrapper.style.display !== 'none') {
        wrapper.style.display = 'none';
        panelSinDocActivo = null;
        return;
    }

    panelSinDocActivo = tipo;
    sinDocRenderPanel(tipo);
    wrapper.style.display = 'block';

    const badge = document.getElementById(`sinDocBadge-${tipo}`);
    if (badge) badge.innerHTML = `${svgUp}Ocultar`;

    setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
}

// ─── Paneles de detalle ───────────────────────────────────────────────────────
function sinDocRenderPanel(tipo) {
    const wrapper = document.getElementById('sinDocPanelDetalle');
    if (!wrapper) return;

    const data = sinDocenteData;
    const cfgMap = {
        urgentes: { titulo: 'Cursos Urgentes — Inician en 7 días o menos',     color: 'danger'  },
        proximos: { titulo: 'Cursos Próximos — Inician entre 8 y 30 días',     color: 'warning' },
        resto:    { titulo: 'Cursos Planificados — Inician en más de 30 días', color: 'primary' },
        ambito:   { titulo: 'Distribución por Ámbito',                         color: 'success' },
    };
    const cfg = cfgMap[tipo] || { titulo: 'Detalle', color: 'secondary' };

    let contenidoPanel = '';

    // ── Listas de cursos por urgencia ─────────────────────────────────────────
    if (['urgentes','proximos','resto'].includes(tipo)) {
        const filtro = tipo === 'urgentes'
            ? d => { const x = diasHastaInicio(d.fecha_inicio); return x !== null && x <= 7; }
            : tipo === 'proximos'
            ? d => { const x = diasHastaInicio(d.fecha_inicio); return x !== null && x > 7 && x <= 30; }
            : d => { const x = diasHastaInicio(d.fecha_inicio); return x !== null && x > 30; };

        const lista = data.filter(filtro).sort((a, b) => {
            const da = diasHastaInicio(a.fecha_inicio) ?? 999;
            const db = diasHastaInicio(b.fecha_inicio) ?? 999;
            return da - db;
        });

        if (lista.length === 0) {
            contenidoPanel = `<div class="text-center py-3 text-muted">
                <i class="bi bi-check-circle-fill text-success" style="font-size:1.8rem;"></i>
                <p class="mt-2 small">No hay cursos en este tramo.</p>
            </div>`;
        } else {
            contenidoPanel = `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Días restantes</th>
                                <th>Curso</th>
                                <th>Fecha Inicio</th>
                                <th>Horas</th>
                                <th>Ámbito</th>
                                <th>Participantes inscritos</th>
                                <th>Turno</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${lista.map(c => {
                                const dias = diasHastaInicio(c.fecha_inicio);
                                const urg  = urgenciaConfig(dias);
                                return `
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-${urg.cls} fs-6">${urg.label}</span>
                                    </td>
                                    <td><strong>${c.nombre_curso}</strong></td>
                                    <td>${c.fecha_inicio ? new Date(c.fecha_inicio).toLocaleDateString('es-VE') : '—'}</td>
                                    <td class="text-center">${parseInt(c.horas_totales||0)}h</td>
                                    <td><span class="badge bg-secondary">${c.nombre_ambito||'—'}</span></td>
                                    <td class="text-center">${parseInt(c.participantes_inscritos||0)} / ${parseInt(c.max_participantes||0)}</td>
                                    <td>${c.turno||'—'}</td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>`;
        }
    }

    // ── Panel por ámbito ──────────────────────────────────────────────────────
    else if (tipo === 'ambito') {
        const porAmbito = {};
        data.forEach(c => {
            const amb = c.nombre_ambito || 'Sin ámbito';
            if (!porAmbito[amb]) porAmbito[amb] = { total: 0, urgentes: 0, proximos: 0, resto: 0 };
            porAmbito[amb].total++;
            const dias = diasHastaInicio(c.fecha_inicio);
            if (dias !== null && dias <= 7)        porAmbito[amb].urgentes++;
            else if (dias !== null && dias <= 30)  porAmbito[amb].proximos++;
            else                                   porAmbito[amb].resto++;
        });

        contenidoPanel = `
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Ámbito</th>
                            <th class="text-center">Total</th>
                            <th class="text-center text-danger">Urgentes</th>
                            <th class="text-center text-warning">Próximos</th>
                            <th class="text-center text-primary">Planificados</th>
                            <th>Distribución</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Object.entries(porAmbito)
                            .sort((a, b) => b[1].total - a[1].total)
                            .map(([amb, v]) => {
                                const pctUrg = data.length > 0 ? Math.round((v.urgentes / data.length) * 100) : 0;
                                return `
                                <tr>
                                    <td><span class="badge bg-secondary">${amb}</span></td>
                                    <td class="text-center fw-bold">${v.total}</td>
                                    <td class="text-center"><span class="badge bg-danger">${v.urgentes}</span></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">${v.proximos}</span></td>
                                    <td class="text-center"><span class="badge bg-primary">${v.resto}</span></td>
                                    <td>
                                        <div class="d-flex gap-1" style="height:8px;">
                                            ${v.urgentes > 0 ? `<div class="bg-danger rounded" style="flex:${v.urgentes};"></div>` : ''}
                                            ${v.proximos > 0 ? `<div class="bg-warning rounded" style="flex:${v.proximos};"></div>` : ''}
                                            ${v.resto    > 0 ? `<div class="bg-primary rounded" style="flex:${v.resto};"></div>`   : ''}
                                        </div>
                                    </td>
                                </tr>`;
                            }).join('')}
                    </tbody>
                </table>
            </div>`;
    }

    wrapper.innerHTML = `
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="mb-3 text-${cfg.color} fw-semibold">
                    <i class="bi bi-table me-1"></i>${cfg.titulo}
                </h6>
                ${contenidoPanel}
            </div>
        </div>`;
}

// ─── Fila de tabla ────────────────────────────────────────────────────────────
function renderFilaSinDoc(c) {
    const dias = diasHastaInicio(c.fecha_inicio);
    const urg  = urgenciaConfig(dias);
    return `
    <tr data-dias="${dias ?? 999}" data-urgencia="${urg.cls}">
        <td class="text-center">
            <span class="badge bg-${urg.cls} fs-6">${urg.label}</span>
        </td>
        <td><strong>${c.nombre_curso}</strong></td>
        <td>${c.fecha_inicio ? new Date(c.fecha_inicio).toLocaleDateString('es-VE') : '—'}</td>
        <td class="text-center">${parseInt(c.horas_totales||0)}h</td>
        <td><span class="badge bg-secondary">${c.nombre_ambito||'—'}</span></td>
        <td class="text-center">
            ${parseInt(c.participantes_inscritos||0)} / ${parseInt(c.max_participantes||0)}
        </td>
        <td>${c.turno||'—'}</td>
    </tr>`;
}

// ─── Filtrar tabla ────────────────────────────────────────────────────────────
function filtrarTablaSinDoc(filtro, evt) {
    const tbody = document.getElementById('tablaSinDocenteBody');
    if (!tbody) return;

    const data = sinDocenteData.slice().sort((a, b) => {
        const da = diasHastaInicio(a.fecha_inicio) ?? 999;
        const db = diasHastaInicio(b.fecha_inicio) ?? 999;
        return da - db;
    });

    const filtrados = filtro === 'urgentes'
        ? data.filter(c => { const d = diasHastaInicio(c.fecha_inicio); return d !== null && d <= 7; })
        : filtro === 'proximos'
        ? data.filter(c => { const d = diasHastaInicio(c.fecha_inicio); return d !== null && d > 7 && d <= 30; })
        : data;

    tbody.innerHTML = filtrados.map(c => renderFilaSinDoc(c)).join('');

    document.querySelectorAll('#tablaSinDocente').closest('.card-body')
        ?.querySelectorAll('.btn-group .btn')
        .forEach(b => b.classList.remove('active'));
    if (evt?.target) evt.target.classList.add('active');
}

// ─── Gráfica por ámbito ───────────────────────────────────────────────────────
function renderGraficaSinDocAmb(porAmbito) {
    const ctx = document.getElementById('graficaSinDocAmb')?.getContext('2d');
    if (!ctx) return;
    if (graficaSinDocenteAmb) { graficaSinDocenteAmb.destroy(); graficaSinDocenteAmb = null; }

    const labels = Object.keys(porAmbito);
    const values = Object.values(porAmbito);

    graficaSinDocenteAmb = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: COLOR_SINDOC.map(c => c.bg),
                borderColor:     COLOR_SINDOC.map(c => c.border),
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 14, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} curso(s)`
                    }
                }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });
}

// ─── Gráfica por urgencia ─────────────────────────────────────────────────────
function renderGraficaSinDocDias(urgentes, proximos, resto, sinFecha) {
    const ctx = document.getElementById('graficaSinDocDias')?.getContext('2d');
    if (!ctx) return;
    if (graficaSinDocenteDias) { graficaSinDocenteDias.destroy(); graficaSinDocenteDias = null; }

    const labels = ['Urgentes (≤7d)', 'Próximos (8–30d)', 'Planificados (>30d)', 'Sin fecha'];
    const values = [urgentes, proximos, resto, sinFecha];
    const colors = ['rgba(239,68,68,.8)', 'rgba(245,158,11,.8)', 'rgba(59,130,246,.8)', 'rgba(156,163,175,.8)'];
    const borders= ['#ef4444', '#f59e0b', '#3b82f6', '#9ca3af'];

    graficaSinDocenteDias = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Cursos',
                data: values,
                backgroundColor: colors,
                borderColor:     borders,
                borderWidth: 2,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw} curso(s) sin docente`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: { grid: { display: false } }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });
}

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarCursosSinDocente() {
    if (graficaSinDocenteAmb)  { graficaSinDocenteAmb.destroy();  graficaSinDocenteAmb  = null; }
    if (graficaSinDocenteDias) { graficaSinDocenteDias.destroy(); graficaSinDocenteDias = null; }
    sinDocenteData    = [];
    panelSinDocActivo = 'urgentes';
}

// ─── Estilos ──────────────────────────────────────────────────────────────────
(function inyectarEstilosSinDoc() {
    if (document.getElementById('_estSinDoc')) return;
    const s = document.createElement('style');
    s.id = '_estSinDoc';
    s.textContent = `
        .kpi-card { transition: transform .25s, box-shadow .25s; cursor: pointer; }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.13) !important; }
        .rp-kpi-card-label { font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#666; margin:.25rem 0 0; }
        .rp-kpi-card-sub   { font-size:.72rem; color:#999; margin:0; }
    `;
    document.head.appendChild(s);
})();

// ─── Registro global ──────────────────────────────────────────────────────────
window.cargarCursosSinDocente  = cargarCursosSinDocente;
window.limpiarCursosSinDocente = limpiarCursosSinDocente;
window.filtrarTablaSinDoc      = filtrarTablaSinDoc;

console.log('RE-Curso-SinDocente.js cargado');

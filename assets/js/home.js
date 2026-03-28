/* ============================================================
   home.js  —  INCES Sistema de Control
   ============================================================ */

const PALETA_ESTADO  = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
const PALETA_GENERO  = ['#0ea5e9', '#ec4899'];
const PALETA_GRADO   = ['#6366f1', '#f59e0b', '#10b981'];
const PALETA_TIPO    = ['#7367f0', '#28c76f', '#ff9f43', '#ea5455'];
const PALETA_ESTATUS = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#6c757d'];
const PALETA_MODAL   = ['#0ea5e9', '#7367f0', '#28c76f'];
const PALETA_TURNO   = ['#ff9f43', '#0ea5e9'];
const COLOR_METAS    = '#ef4444';
const PALETA_AMBITO_SOL = ['#6366f1','#0ea5e9','#28c76f','#f59e0b','#ef4444','#8b5cf6','#ec4899','#10b981'];

/* ──────────────────────────────────────────────────────────
   Mapa de gráficas por sección y tab
   Cada entrada define:
     - chartId  → id del canvas
     - render   → nombre de la función que dibuja la gráfica
   ────────────────────────────────────────────────────────── */
const CHART_MAP = {
    participantes: {
        estado:  { chartId: 'chartPartEstado',  render: 'renderPartEstado'  },
        genero:  { chartId: 'chartPartGenero',  render: 'renderPartGenero'  },
        grado:   { chartId: 'chartPartGrado',   render: 'renderPartGrado'   },
    },
    cursos: {
        estatus:   { chartId: 'chartCursosEstatus',   render: 'renderCursosEstatus'   },
        modalidad: { chartId: 'chartCursosModalidad', render: 'renderCursosModalidad' },
        turno:     { chartId: 'chartCursosTurno',     render: 'renderCursosTurno'     },
        cancelados:{ chartId: 'chartCursosEstatus2',  render: 'renderCursosCancelados'},
        ambito: { chartId: 'chartAmbitoSolicitadoTab', render: 'renderAmbitoSolicitado' },
    },
    docentes: {
        tipo:   { chartId: 'chartDocTipo',   render: 'renderDocTipo'   },
        estado: { chartId: 'chartDocEstado', render: 'renderDocEstado' },
        genero: { chartId: 'chartDocGenero', render: 'renderDocGenero' },
    },
    metas: {
        ambito: { chartId: 'chartCumplimientoAmbito', render: 'renderCumplimientoAmbito' },
        mes:    { chartId: 'chartMetasMes',            render: 'renderCalendarioMetas'    },
    },
};

/* ──────────────────────────────────────────────────────────
   Estado de gráficas ya renderizadas  { "section:chart": true }
   ────────────────────────────────────────────────────────── */
const graficasCreadas   = {};
const graficasRendered  = new Set();

function colorPorPct(pct) {
    if (pct >= 95) return '#28c76f';
    if (pct >= 80) return '#f59e0b';
    return '#ef4444';
}

function waitForChart(callback, maxAttempts = 20) {
    let attempts = 0;
    const interval = setInterval(() => {
        attempts++;
        if (typeof Chart !== 'undefined') { clearInterval(interval); callback(); }
        else if (attempts >= maxAttempts) { clearInterval(interval); }
    }, 100);
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    waitForChart(initHome);
});

/* ════════════════════════════
   INICIALIZACIÓN
════════════════════════════ */
function initHome() {
    initSidebar();
    animateProgressBars();
    initTooltips();
    initTableNavigation();
    initChartTabs();

    const btnVerTodos = document.getElementById('btn-metas-ver-todos');
    if (btnVerTodos) btnVerTodos.addEventListener('click', () => mostrarTodasMetas());

    // Mini charts del resumen
    setTimeout(() => { if (window.HOME_CHARTS) renderMiniChartsMensuales(); }, 300);
}

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('toggle-btn');
    if (sidebar && toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('expand'));
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('mouseenter', () => { link.style.transition = 'transform .3s ease'; link.style.transform = 'scale(1.05)'; });
            link.addEventListener('mouseleave', () => { link.style.transform = 'scale(1)'; });
        });
    }
    document.querySelectorAll('.sidebar-item.dropdown').forEach(item => {
        const link = item.querySelector('.sidebar-link');
        if (link) link.addEventListener('click', e => { e.preventDefault(); item.classList.toggle('sub-menu-toggle'); });
    });
}

function animateProgressBars() {
    document.querySelectorAll('.kpi-adv-barra').forEach(b => {
        const w = b.style.width; b.style.width = '0%';
        requestAnimationFrame(() => requestAnimationFrame(() => { b.style.width = w; }));
    });
}

function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }
}

function initTableNavigation() {
    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.part-row-link');
        if (!fila) return;
        const cedula = fila.dataset.cedula;
        if (!cedula) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(0,200,224,.18)';
        setTimeout(() => { window.location.href = `participante.php?buscar=${encodeURIComponent(cedula)}`; }, 150);
    });

    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.curso-row-link');
        if (!fila) return;
        const nombre = fila.dataset.nombre;
        if (!nombre) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(40,199,111,.18)';
        setTimeout(() => { window.location.href = `cursos.php?buscar=${encodeURIComponent(nombre)}`; }, 150);
    });

    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.meta-row-link');
        if (!fila) return;
        const id = fila.dataset.id;
        if (!id) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(239,68,68,.15)';
        setTimeout(() => { window.location.href = `metas.php?buscar=${encodeURIComponent(id)}`; }, 150);
    });

    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.docente-row-link');
        if (!fila) return;
        const cedula = fila.dataset.cedula;
        if (!cedula) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(115,103,240,.18)';
        setTimeout(() => { window.location.href = `docente.php?buscar=${encodeURIComponent(cedula)}`; }, 150);
    });
}

/* ════════════════════════════════════════════════════
   LÓGICA DE SUB-TABS DE GRÁFICAS
════════════════════════════════════════════════════ */
function initChartTabs() {
    document.querySelectorAll('.chart-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.dataset.section;
            const chart   = btn.dataset.chart;
            activateChartTab(section, chart, btn);
        });
    });
}

function activateChartTab(section, chartKey, btnEl) {
    // 1. Actualizar botones activos dentro del nav de esta sección
    const nav = document.getElementById(`chart-nav-${section}`);
    if (nav) {
        nav.querySelectorAll('.chart-tab-btn').forEach(b => b.classList.remove('active'));
    }
    if (btnEl) btnEl.classList.add('active');

    // 2. Ocultar todos los paneles de la sección y mostrar el target
    const sectionEl = document.getElementById(`section-${section}`);
    if (sectionEl) {
        sectionEl.querySelectorAll('.chart-tab-panel').forEach(p => p.classList.remove('active'));
    }
    const panel = document.getElementById(`chart-panel-${section}-${chartKey}`);
    if (panel) panel.classList.add('active');

    // 3. Renderizar la gráfica si aún no fue creada
    const key = `${section}:${chartKey}`;
    if (!graficasRendered.has(key)) {
        graficasRendered.add(key);
        const entry = CHART_MAP[section]?.[chartKey];
        if (entry && typeof window[entry.render] === 'function') {
            setTimeout(() => window[entry.render](), 60);
        }
    }
}

/* ════════════════════════════════════════════════════
   RENDERIZADO POR SECCIÓN (llamado desde el nav principal)
   Se renderiza SOLO el tab activo de cada sección
════════════════════════════════════════════════════ */
function renderGraficasBasicas(seccionId) {
    if (!window.HOME_CHARTS) { setTimeout(() => renderGraficasBasicas(seccionId), 200); return; }

    // Encontrar el tab activo de la sección recién activada y renderizarlo
    const nav = document.getElementById(`chart-nav-${seccionId}`);
    if (!nav) return;
    const activeBtn = nav.querySelector('.chart-tab-btn.active');
    if (!activeBtn) return;

    const chartKey = activeBtn.dataset.chart;
    activateChartTab(seccionId, chartKey, activeBtn);
}

/* ════════════════════════════════════════════════════
   FUNCIONES DE RENDERIZADO INDIVIDUALES
   Cada función dibuja UNA gráfica específica
════════════════════════════════════════════════════ */

/* ── Participantes ── */
function renderPartEstado() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartPartEstado', d.partEstado.labels, d.partEstado.data, PALETA_ESTADO);
}
function renderPartGenero() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartPartGenero', d.partGenero.labels, d.partGenero.data, PALETA_GENERO);
}
function renderPartGrado() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeBar('chartPartGrado', d.partGrado.labels, d.partGrado.data, PALETA_GRADO);
}

/* ── Cursos ── */
function renderCursosEstatus() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartCursosEstatus', d.cursosEstatus.labels, d.cursosEstatus.data, PALETA_ESTATUS);
}
function renderCursosModalidad() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartCursosModalidad', d.cursosModal.labels, d.cursosModal.data, PALETA_MODAL);
}
function renderCursosTurno() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeBar('chartCursosTurno', d.cursosTurno.labels, d.cursosTurno.data, PALETA_TURNO);
}
function renderCursosCancelados() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartCursosEstatus2', d.cursosEstatus.labels, d.cursosEstatus.data, PALETA_ESTATUS);
}
function renderAmbitoSolicitado() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    if (d.ambitoSolicitado?.labels?.length) {
        makeDonutAmbitoSol('chartAmbitoSolicitadoTab', d.ambitoSolicitado.labels, d.ambitoSolicitado.data);
    }
}

/* ── Docentes ── */
function renderDocTipo() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartDocTipo', d.docTipo.labels, d.docTipo.data, PALETA_TIPO);
}
function renderDocEstado() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeBar('chartDocEstado', d.docEstado.labels, d.docEstado.data, PALETA_ESTADO);
}
function renderDocGenero() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeDonut('chartDocGenero', d.docGenero.labels, d.docGenero.data, PALETA_GENERO);
}

/* ── Metas ── */
function renderCumplimientoAmbito() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    if (d.cumplimientoAmbito?.labels?.length) {
        makeBarAmbitoH('chartCumplimientoAmbito', d.cumplimientoAmbito.labels, d.cumplimientoAmbito.data);
    }
}
function renderCalendarioMetas() {
    const d = window.HOME_CHARTS;
    if (!d) return;
    makeCalendarioMetas('chartMetasMes', d.metasMes.data, COLOR_METAS);
}

/* ════════════════════════════
   MINI CHARTS (sección resumen)
════════════════════════════ */
function renderMiniChartsMensuales() {
    if (!window.HOME_CHARTS) return;
    const d = window.HOME_CHARTS;
    makeMiniBarMes('chartPartMes',     d.partMes.data,     '#00c8e0');
    makeMiniBarMes('chartCursosMes',   d.cursosMes.data,   '#28c76f');
    makeMiniBarMes('chartDocentesMes', d.docentesMes.data, '#7367f0');
}

/* ════════════════════════════
   GRÁFICAS — funciones base
════════════════════════════ */
function makeDonut(canvasId, labels, data, colores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: { labels, datasets: [{ data, backgroundColor: colores, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }] },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10, boxWidth: 12 } },
                    tooltip: { callbacks: { label: c => ` ${c.label}: ${c.parsed}` } }
                }
            }
        });
    } catch (e) {}
}

function makeBar(canvasId, labels, data, colores, horizontal = false) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ data, backgroundColor: colores, borderRadius: 6, borderSkipped: false }] },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 10 } }, beginAtZero: true }
                }
            }
        });
    } catch (e) {}
}

function makeMiniBarMes(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const mesActual    = new Date().getMonth();
    const bgColors     = data.map((_, i) => i === mesActual ? color : color + '55');
    const borderColors = data.map((_, i) => i === mesActual ? color : 'transparent');
    const lbls = typeof MESES_LABELS !== 'undefined' ? MESES_LABELS : ['E','F','M','A','M','J','J','A','S','O','N','D'];
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { labels: lbls, datasets: [{ data, backgroundColor: bgColors, borderColor: borderColors, borderWidth: 1, borderRadius: 3, borderSkipped: false }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: items => lbls[items[0].dataIndex] ?? items[0].label,
                            label: item => ` ${item.parsed.y}`
                        },
                        backgroundColor: 'rgba(15,23,42,.85)', titleFont: { size: 11, weight: 600 },
                        bodyFont: { size: 11 }, padding: 8, cornerRadius: 6
                    }
                },
                scales: {
                    x: {
                        display: true, grid: { display: false }, border: { display: false },
                        ticks: {
                            font: { size: 8 }, color: '#adb5bd', maxRotation: 0,
                            callback: (_, idx) => ['E','F','M','A','M','J','J','A','S','O','N','D'][idx] ?? ''
                        }
                    },
                    y: { display: false, beginAtZero: true }
                }
            }
        });
    } catch (e) {}
}

/* ════════════════════════════════════════════════════
   Gráfica horizontal: cumplimiento por ámbito
════════════════════════════════════════════════════ */
function makeBarAmbitoH(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();

    const colores = data.map(v => colorPorPct(v));

    const lineaMeta = {
        id: 'lineaMeta',
        afterDraw(chart) {
            const { ctx: c, chartArea, scales } = chart;
            if (!scales.x) return;
            const x = scales.x.getPixelForValue(100);
            if (x < chartArea.left || x > chartArea.right) return;
            c.save();
            c.beginPath();
            c.moveTo(x, chartArea.top);
            c.lineTo(x, chartArea.bottom);
            c.strokeStyle = 'rgba(100,116,139,.5)';
            c.lineWidth = 1.5;
            c.setLineDash([4, 4]);
            c.stroke();
            c.fillStyle = 'rgba(100,116,139,.8)';
            c.font = '10px sans-serif';
            c.textAlign = 'center';
            c.fillText('Meta 100%', x, chartArea.top - 4);
            c.restore();
        }
    };

    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ data, backgroundColor: colores, borderRadius: 5, borderSkipped: false, barThickness: 18 }] },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => ` ${c.parsed.x}% promedio de cumplimiento`,
                            afterLabel: c => {
                                const pct = c.parsed.x;
                                if (pct >= 95) return ' ✓ Óptimo (≥95%)';
                                if (pct >= 80) return ' ⚠ Atención (80–94%)';
                                return ' ✗ Crítico (<80%)';
                            }
                        },
                        backgroundColor: 'rgba(15,23,42,.88)', titleFont: { size: 12, weight: 600 },
                        bodyFont: { size: 11 }, padding: 10, cornerRadius: 7
                    }
                },
                scales: {
                    x: {
                        min: 0, max: 110,
                        grid: { color: 'rgba(0,0,0,.05)' }, border: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', callback: v => v + '%', stepSize: 20 }
                    },
                    y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 }, color: '#334155' } }
                }
            },
            plugins: [lineaMeta]
        });
    } catch (e) {}
}

/* ════════════════════════════
   Calendario de metas por mes
════════════════════════════ */
let mesMetaActivo = null;

function makeCalendarioMetas(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const lbls = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const bgColors     = data.map(v => v > 0 ? color : color + '28');
    const borderColors = data.map(v => v > 0 ? color : 'transparent');
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { labels: lbls, datasets: [{ data, backgroundColor: bgColors, borderColor: borderColors, borderWidth: 1.5, borderRadius: 7, borderSkipped: false }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: items => lbls[items[0].dataIndex],
                            label: item => {
                                const v = item.parsed.y;
                                return v > 0 ? ` ${v} meta${v !== 1 ? 's' : ''} — clic para filtrar` : ' Sin metas este mes';
                            }
                        },
                        backgroundColor: 'rgba(15,23,42,.88)', titleFont: { size: 12, weight: 600 },
                        bodyFont: { size: 11 }, padding: 10, cornerRadius: 7
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' }, border: { display: false }, ticks: { font: { size: 10 }, stepSize: 1, color: '#94a3b8' } }
                },
                onClick: (event, elements) => {
                    if (!elements.length) return;
                    const mesIdx = elements[0].index;
                    const mesNum = mesIdx + 1;
                    if (mesMetaActivo === mesNum) { mostrarTodasMetas(); }
                    else { filtrarMetasPorMes(mesNum, lbls[mesIdx]); }
                },
                onHover: (event, elements) => {
                    const tgt = event.native?.target;
                    if (tgt) tgt.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                }
            }
        });
    } catch (e) {}
}

function filtrarMetasPorMes(mesNum, mesLabel) {
    const tbody = document.querySelector('#tabla-metas-panel tbody');
    if (!tbody) return;
    mesMetaActivo = mesNum;
    const filas = tbody.querySelectorAll('tr[data-mes-meta]');
    let visibles = 0;
    filas.forEach(f => {
        const mostrar = parseInt(f.dataset.mesMeta) === mesNum;
        f.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
    const btnTodos = document.getElementById('btn-metas-ver-todos');
    const wrapLabel = document.getElementById('metas-mes-activo-wrap');
    const spanLabel = document.getElementById('metas-mes-label');
    const sinDatos  = document.getElementById('metas-sin-datos-mes');
    if (btnTodos)  btnTodos.classList.remove('d-none');
    if (wrapLabel) wrapLabel.classList.remove('d-none');
    if (spanLabel) spanLabel.textContent = mesLabel;
    if (sinDatos)  sinDatos.style.display = visibles === 0 ? '' : 'none';
    const chart = graficasCreadas['chartMetasMes'];
    if (chart) {
        const color = COLOR_METAS;
        chart.data.datasets[0].backgroundColor = chart.data.labels.map((_, i) => i === mesNum - 1 ? color : color + '28');
        chart.data.datasets[0].borderColor      = chart.data.labels.map((_, i) => i === mesNum - 1 ? color : 'transparent');
        chart.update('none');
    }
}

function mostrarTodasMetas() {
    const tbody = document.querySelector('#tabla-metas-panel tbody');
    if (!tbody) return;
    tbody.querySelectorAll('tr[data-mes-meta]').forEach(f => f.style.display = '');
    const btnTodos  = document.getElementById('btn-metas-ver-todos');
    const wrapLabel = document.getElementById('metas-mes-activo-wrap');
    const sinDatos  = document.getElementById('metas-sin-datos-mes');
    if (btnTodos)  btnTodos.classList.add('d-none');
    if (wrapLabel) wrapLabel.classList.add('d-none');
    if (sinDatos)  sinDatos.style.display = 'none';
    mesMetaActivo = null;
    const chart = graficasCreadas['chartMetasMes'];
    if (chart) {
        const color = COLOR_METAS;
        const data  = chart.data.datasets[0].data;
        chart.data.datasets[0].backgroundColor = data.map(v => v > 0 ? color : color + '28');
        chart.data.datasets[0].borderColor      = data.map(v => v > 0 ? color : 'transparent');
        chart.update('none');
    }
}

/* ════════════════════════════════════════════════════
   Donut ámbito más solicitado por mes
════════════════════════════════════════════════════ */
function makeDonutAmbitoSol(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const total = data.reduce((a, b) => a + b, 0);
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: labels.map((_, i) => PALETA_AMBITO_SOL[i % PALETA_AMBITO_SOL.length]), borderWidth: 2, borderColor: '#fff', hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8, boxWidth: 10 } },
                    tooltip: {
                        callbacks: {
                            label: c => {
                                const pct = total > 0 ? Math.round(c.parsed / total * 100) : 0;
                                return ` ${c.label}: ${c.parsed} inscripciones (${pct}%)`;
                            }
                        },
                        backgroundColor: 'rgba(15,23,42,.88)', titleFont: { size: 11, weight: 600 },
                        bodyFont: { size: 11 }, padding: 10, cornerRadius: 7
                    }
                }
            }
        });
    } catch (e) {}
}
/* ============================================================
   home.js  —  INCES Sistema de Control
   Dashboard principal: inicialización, navegación,
   renderizado de gráficas y utilidades de dibujo.
   ============================================================ */


/* ════════════════════════════════════════════════════════════
   PALETAS DE COLOR
   Centralizadas aquí para cambiar en un solo lugar.
════════════════════════════════════════════════════════════ */
const PALETA_ESTADO = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
const PALETA_GENERO = ['#0ea5e9', '#ec4899'];
const PALETA_GRADO = ['#6366f1', '#f59e0b', '#10b981'];
const PALETA_TIPO = ['#7367f0', '#28c76f', '#ff9f43', '#ea5455'];
const PALETA_ESTATUS = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#6c757d'];
const PALETA_MODAL = ['#0ea5e9', '#7367f0', '#28c76f'];
const PALETA_TURNO = ['#ff9f43', '#0ea5e9'];
const COLOR_METAS = '#ef4444';
const PALETA_AMBITO = ['#6366f1', '#0ea5e9', '#28c76f', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#10b981'];


/* ════════════════════════════════════════════════════════════
   MAPA DE GRÁFICAS
   Relaciona cada sección + tab con su canvas y función de render.
   Usado por activateChartTab() para saber qué dibujar.
════════════════════════════════════════════════════════════ */
const CHART_MAP = {
    participantes: {
        estado:  { chartId: 'chartPartEstado',  render: 'renderPartEstado'  },
        genero:  { chartId: 'chartPartGenero',  render: 'renderPartGenero'  },
        grado:   { chartId: 'chartPartGrado',   render: 'renderPartGrado'   },
        cursos:  { chartId: 'chartPartCursos',  render: 'renderPartCursos'  },
    },
    cursos: {
        estatus: { chartId: 'chartCursosEstatus', render: 'renderCursosEstatus' },
        modalidad: { chartId: 'chartCursosModalidad', render: 'renderCursosModalidad' },
        turno: { chartId: 'chartCursosTurno', render: 'renderCursosTurno' },
        ambito: { chartId: 'chartAmbitoSolicitadoTab', render: 'renderAmbitoSolicitado' },
        formacion: { chartId: 'chartFormacionSolicitadaTab', render: 'renderFormacionSolicitada' },
    },
    docentes: {
        tipo: { chartId: 'chartDocTipo', render: 'renderDocTipo' },
        estado: { chartId: 'chartDocEstado', render: 'renderDocEstado' },
        genero: { chartId: 'chartDocGenero', render: 'renderDocGenero' },
    },
    metas: {
        ambito: { chartId: 'chartCumplimientoAmbitoTab', render: 'renderCumplimientoAmbitoTab' },
        mes: { chartId: 'chartMetasMes', render: 'renderCalendarioMetas' },
    },
};


/* ════════════════════════════════════════════════════════════
   ESTADO INTERNO
   Registra instancias de Chart.js y tabs ya renderizados
   para evitar recrear gráficas innecesariamente.
════════════════════════════════════════════════════════════ */
const graficasCreadas = {};
const graficasRendered = new Set();


/* ════════════════════════════════════════════════════════════
   PUNTO DE ENTRADA
   Espera a que Chart.js esté disponible antes de inicializar.
════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    initHome();
});

function waitForChart(callback, maxAttempts = 20) {
    let attempts = 0;
    const interval = setInterval(() => {
        attempts++;
        if (typeof Chart !== 'undefined') { clearInterval(interval); callback(); }
        else if (attempts >= maxAttempts) { clearInterval(interval); }
    }, 100);
}


/* ════════════════════════════════════════════════════════════
   INICIALIZACIÓN DEL DASHBOARD
   Orquesta el arranque de todos los subsistemas.
════════════════════════════════════════════════════════════ */
function initHome() {
    initSidebar();
    animateProgressBars();
    initTooltips();
    initTableNavigation();

    // Solo lo relacionado con gráficas espera a Chart.js (Dashboard/Inicio)
    waitForChart(() => {
        initChartTabs();

        const btnVerTodos = document.getElementById('btn-metas-ver-todos');
        if (btnVerTodos) btnVerTodos.addEventListener('click', () => mostrarTodasMetas());

        setTimeout(() => {
            if (window.HOME_CHARTS) renderMiniChartsMensuales();
        }, 300);
    });
}

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('toggle-btn');
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


/* ════════════════════════════════════════════════════════════
   NAVEGACIÓN DE TABLAS
   Delegación de eventos para filas clicables de cada módulo.
   Navega a la página correspondiente al hacer clic en una fila.
════════════════════════════════════════════════════════════ */
function initTableNavigation() {
    const handlers = [
        { selector: 'tr.part-row-link', key: 'cedula', url: c => `participante.php?buscar=${c}`, color: 'rgba(0,200,224,.18)' },
        { selector: 'tr.curso-row-link', key: 'nombre', url: c => `cursos.php?buscar=${c}`, color: 'rgba(40,199,111,.18)' },
        { selector: 'tr.meta-row-link', key: 'id', url: c => `metas.php?buscar=${c}`, color: 'rgba(239,68,68,.15)' },
        { selector: 'tr.docente-row-link', key: 'cedula', url: c => `docente.php?buscar=${c}`, color: 'rgba(115,103,240,.18)' },
    ];

    handlers.forEach(({ selector, key, url, color }) => {
        document.addEventListener('click', e => {
            const fila = e.target.closest(selector);
            if (!fila) return;
            const valor = fila.dataset[key];
            if (!valor) return;
            fila.style.transition = 'background .15s';
            fila.style.background = color;
            setTimeout(() => { window.location.href = url(encodeURIComponent(valor)); }, 150);
        });
    });
}


/* ════════════════════════════════════════════════════════════
   SISTEMA DE TABS DE GRÁFICAS
   Controla qué panel y qué gráfica se muestran dentro
   de cada sección del dashboard.
   NOTA: inicio.php NO debe registrar sus propios listeners
         para .chart-tab-btn — este módulo es el único dueño.
════════════════════════════════════════════════════════════ */
function initChartTabs() {
    document.querySelectorAll('.chart-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            activateChartTab(btn.dataset.section, btn.dataset.chart, btn);
        });
    });
}

/* ── FIX 1 ──────────────────────────────────────────────────
   Se verifica graficasCreadas[entry.chartId] en lugar de solo
   el Set. Si la instancia Chart.js no existe (primera vez o fue
   destruida), se borra el flag del Set para forzar un re-render.
   Esto evita que una render fallida a causa de HOME_CHARTS aún
   no disponible deje el tab "marcado" sin gráfica real.
────────────────────────────────────────────────────────────── */
function activateChartTab(section, chartKey, btnEl) {
    const nav = document.getElementById(`chart-nav-${section}`);
    if (nav) nav.querySelectorAll('.chart-tab-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    const sectionEl = document.getElementById(`section-${section}`);
    if (sectionEl) sectionEl.querySelectorAll('.chart-tab-panel').forEach(p => p.classList.remove('active'));
    const panel = document.getElementById(`chart-panel-${section}-${chartKey}`);
    if (panel) panel.classList.add('active');

    const key = `${section}:${chartKey}`;
    const entry = CHART_MAP[section]?.[chartKey];

    // Si la instancia Chart.js no existe pero el key ya estaba en el Set
    // (render falló silenciosamente antes), limpiamos el flag para reintentar.
    if (entry && !graficasCreadas[entry.chartId]) {
        graficasRendered.delete(key);
    }

    if (!graficasRendered.has(key)) {
        graficasRendered.add(key);
        if (entry && typeof window[entry.render] === 'function') {
            setTimeout(() => window[entry.render](), 60);
        }
    }
}

function renderGraficasBasicas(seccionId) {
    if (!window.HOME_CHARTS) { setTimeout(() => renderGraficasBasicas(seccionId), 200); return; }
    const nav = document.getElementById(`chart-nav-${seccionId}`);
    if (!nav) return;
    const activeBtn = nav.querySelector('.chart-tab-btn.active');
    if (!activeBtn) return;
    activateChartTab(seccionId, activeBtn.dataset.chart, activeBtn);
}


/* ════════════════════════════════════════════════════════════
   RENDERS — PARTICIPANTES
════════════════════════════════════════════════════════════ */
function renderPartEstado() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartPartEstado', d.partEstado.labels, d.partEstado.data, PALETA_ESTADO);
}
function renderPartGenero() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartPartGenero', d.partGenero.labels, d.partGenero.data, PALETA_GENERO);
}
function renderPartGrado() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeBar('chartPartGrado', d.partGrado.labels, d.partGrado.data, PALETA_GRADO, false, 'N* Participantes');
}


/* ════════════════════════════════════════════════════════════
   RENDERS — CURSOS
════════════════════════════════════════════════════════════ */
function renderCursosEstatus() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartCursosEstatus', d.cursosEstatus.labels, d.cursosEstatus.data, PALETA_ESTATUS);
}
function renderCursosModalidad() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartCursosModalidad', d.cursosModal.labels, d.cursosModal.data, PALETA_MODAL);
}
function renderCursosTurno() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeBar('chartCursosTurno', d.cursosTurno.labels, d.cursosTurno.data, PALETA_TURNO, false, 'N* Cursos');
}

/* ── FIX 2 ──────────────────────────────────────────────────
   renderAmbitoSolicitado ahora siempre intenta dibujar.
   Si no hay datos muestra un mensaje en el canvas en lugar
   de no hacer absolutamente nada.
────────────────────────────────────────────────────────────── */
function renderAmbitoSolicitado() {
    const d = window.HOME_CHARTS; if (!d) return;
    const labels = d.ambitoSolicitado?.labels ?? [];
    const data   = d.ambitoSolicitado?.data   ?? [];
    if (labels.length) {
        makeDonutAmbitoSol('chartAmbitoSolicitadoTab', labels, data);
    } else {
        _mostrarSinDatos('chartAmbitoSolicitadoTab', 'Sin registros de ámbito este mes');
    }
}

/* ── FIX 3 ──────────────────────────────────────────────────
   renderFormacionSolicitada ahora siempre intenta dibujar.
   Si el array está vacío muestra un mensaje en el canvas.
────────────────────────────────────────────────────────────── */
function renderFormacionSolicitada() {
    const d = window.HOME_CHARTS; if (!d) return;
    const items = d.formacionSolicitada ?? [];
    if (items.length) {
        makeBarFormacionSolicitada('chartFormacionSolicitadaTab', items);
    } else {
        _mostrarSinDatos('chartFormacionSolicitadaTab', 'Sin actividad de formaciones registrada');
    }
}

/* ════════════════════════════════════════════════════════════
   RENDERS — DOCENTES
════════════════════════════════════════════════════════════ */
function renderDocTipo() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartDocTipo', d.docTipo.labels, d.docTipo.data, PALETA_TIPO);
}
function renderDocEstado() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeBar('chartDocEstado', d.docEstado.labels, d.docEstado.data, PALETA_ESTADO, false, 'N* Docentes');
}
function renderDocGenero() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeDonut('chartDocGenero', d.docGenero.labels, d.docGenero.data, PALETA_GENERO);
}


/* ════════════════════════════════════════════════════════════
   RENDERS — METAS
════════════════════════════════════════════════════════════ */
function renderCumplimientoAmbitoTab() {
    const d = window.HOME_CHARTS; if (!d) return;
    if (d.cumplimientoAmbito?.labels?.length) {
        makeBarAmbitoH('chartCumplimientoAmbitoTab', d.cumplimientoAmbito.labels, d.cumplimientoAmbito.data);
    }
}
function renderCumplimientoAmbito() {
    const d = window.HOME_CHARTS; if (!d) return;
    if (d.cumplimientoAmbito?.labels?.length) {
        makeBarAmbitoH('chartCumplimientoAmbito', d.cumplimientoAmbito.labels, d.cumplimientoAmbito.data);
    }
}
function renderCalendarioMetas() {
    const d = window.HOME_CHARTS; if (!d) return;
    makeCalendarioMetas('chartMetasMes', d.metasMes.data, COLOR_METAS);
    makeCalendarioMetas('chartMetasMesTabla', d.metasMes.data, COLOR_METAS);
}


/* ════════════════════════════════════════════════════════════
   RENDERS — RESUMEN (mini charts mensuales)
   Dibuja las barras pequeñas de las tarjetas KPI del resumen.
════════════════════════════════════════════════════════════ */
function renderMiniChartsMensuales() {
    if (!window.HOME_CHARTS) return;
    const d = window.HOME_CHARTS;
    makeMiniBarMes('chartPartMes', d.partMes.data, '#00c8e0');
    makeMiniBarMes('chartCursosMes', d.cursosMes.data, '#28c76f');
    makeMiniBarMes('chartDocentesMes', d.docentesMes.data, '#7367f0');
}


/* ════════════════════════════════════════════════════════════
   FILTRO DE METAS POR MES
   Controla el calendario interactivo de la sección metas.
   Al clic en una barra del calendario filtra la tabla inferior.
════════════════════════════════════════════════════════════ */
let mesMetaActivo = null;

function filtrarMetasPorMes(mesNum, mesLabel) {
    const tbody = document.querySelector('#tabla-metas-panel tbody');
    if (!tbody) return;
    mesMetaActivo = mesNum;

    let visibles = 0;
    tbody.querySelectorAll('tr[data-mes-meta]').forEach(f => {
        const mostrar = parseInt(f.dataset.mesMeta) === mesNum;
        f.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    const btnTodos = document.getElementById('btn-metas-ver-todos');
    const wrapLabel = document.getElementById('metas-mes-activo-wrap');
    const spanLabel = document.getElementById('metas-mes-label');
    const sinDatos = document.getElementById('metas-sin-datos-mes');
    if (btnTodos) btnTodos.classList.remove('d-none');
    if (wrapLabel) wrapLabel.classList.remove('d-none');
    if (spanLabel) spanLabel.textContent = mesLabel;
    if (sinDatos) sinDatos.style.display = visibles === 0 ? '' : 'none';

    ['chartMetasMes', 'chartMetasMesTabla'].forEach(id => {
        const chart = graficasCreadas[id];
        if (!chart) return;
        chart.data.datasets[0].backgroundColor = chart.data.labels.map((_, i) => i === mesNum - 1 ? COLOR_METAS : COLOR_METAS + '28');
        chart.data.datasets[0].borderColor = chart.data.labels.map((_, i) => i === mesNum - 1 ? COLOR_METAS : 'transparent');
        chart.update('none');
    });
}

function mostrarTodasMetas() {
    const tbody = document.querySelector('#tabla-metas-panel tbody');
    if (!tbody) return;
    tbody.querySelectorAll('tr[data-mes-meta]').forEach(f => f.style.display = '');
    mesMetaActivo = null;

    const btnTodos = document.getElementById('btn-metas-ver-todos');
    const wrapLabel = document.getElementById('metas-mes-activo-wrap');
    const sinDatos = document.getElementById('metas-sin-datos-mes');
    if (btnTodos) btnTodos.classList.add('d-none');
    if (wrapLabel) wrapLabel.classList.add('d-none');
    if (sinDatos) sinDatos.style.display = 'none';

    ['chartMetasMes', 'chartMetasMesTabla'].forEach(id => {
        const chart = graficasCreadas[id];
        if (!chart) return;
        const data = chart.data.datasets[0].data;
        chart.data.datasets[0].backgroundColor = data.map(v => v > 0 ? COLOR_METAS : COLOR_METAS + '28');
        chart.data.datasets[0].borderColor = data.map(v => v > 0 ? COLOR_METAS : 'transparent');
        chart.update('none');
    });
}


/* ════════════════════════════════════════════════════════════
   UTILIDADES DE DIBUJO — Chart.js
   Funciones base reutilizadas por todos los renders.
   Cada función destruye la instancia previa antes de redibujar.
════════════════════════════════════════════════════════════ */

/* ── UTILIDAD: mensaje "sin datos" en el canvas ─────────────
   Dibuja texto centrado y limpia cualquier instancia previa.
   Usada cuando un render recibe datos vacíos.
────────────────────────────────────────────────────────────── */
function _mostrarSinDatos(canvasId, mensaje) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    if (graficasCreadas[canvasId]) {
        graficasCreadas[canvasId].destroy();
        delete graficasCreadas[canvasId];
    }
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    ctx.font         = '13px Roboto, sans-serif';
    ctx.fillStyle    = '#94a3b8';
    ctx.textAlign    = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(mensaje, canvas.width / 2, canvas.height / 2);
    ctx.restore();
}

function colorPorPct(pct) {
    if (pct >= 95) return '#28c76f';
    if (pct >= 80) return '#f59e0b';
    return '#ef4444';
}

function makeDonut(canvasId, labels, data, colores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: { 
                labels, 
                datasets: [{ 
                    data, 
                    backgroundColor: colores, 
                    borderWidth: 2, 
                    borderColor: '#fff', 
                    hoverOffset: 8 
                }] 
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11, weight: '600' }, padding: 15, boxWidth: 12 } },
                    tooltip: { 
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: c => ` ${c.label}: ${c.parsed}` } 
                    }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            },
            plugins: [{
                id: 'donutCenterText',
                beforeDraw: function(chart) {
                    const { width, height, ctx } = chart;
                    ctx.restore();
                    const total = chart.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    
                    const fontSizeTotal = (height / 120).toFixed(2);
                    ctx.font = "bold " + fontSizeTotal + "em 'Inter', sans-serif";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#1e293b";
                    
                    const text = total.toString();
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;
                    
                    ctx.fillText(text, textX, textY);
                    
                    const fontSizeLabel = (height / 300).toFixed(2);
                    ctx.font = "600 " + fontSizeLabel + "em 'Inter', sans-serif";
                    ctx.fillStyle = "#64748b";
                    const labelText = "TOTAL";
                    const labelX = Math.round((width - ctx.measureText(labelText).width) / 2);
                    ctx.fillText(labelText, labelX, textY + 22);
                    
                    ctx.save();
                }
            }]
        });
    } catch (e) { }
}

function makeBar(canvasId, labels, data, colores, horizontal = false, labelEje = '') {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { 
                labels, 
                datasets: [{ 
                    data, 
                    backgroundColor: function(context) {
                        const c = context.chart.ctx;
                        const area = context.chart.chartArea;
                        if (!area) return colores[context.dataIndex % colores.length];
                        const gradient = horizontal 
                            ? c.createLinearGradient(area.left, 0, area.right, 0)
                            : c.createLinearGradient(0, area.bottom, 0, area.top);
                        const baseCol = colores[context.dataIndex % colores.length];
                        gradient.addColorStop(0, baseCol.replace(')', ', 0.6)').replace('rgb', 'rgba'));
                        gradient.addColorStop(1, baseCol);
                        return gradient;
                    },
                    borderRadius: 6, 
                    borderSkipped: false 
                }] 
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        ticks: { font: { size: 10 }, color: '#94a3b8' },
                        title: {
                            display: horizontal && labelEje !== '',
                            text: labelEje,
                            color: '#64748b',
                            font: { size: 11, weight: '600' }
                        }
                    },
                    y: { 
                        grid: { color: 'rgba(0,0,0,.05)', drawBorder: false }, 
                        ticks: { font: { size: 10 }, color: '#94a3b8' }, 
                        beginAtZero: true,
                        title: {
                            display: !horizontal && labelEje !== '',
                            text: labelEje,
                            color: '#64748b',
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    } catch (e) { }
}

function makeMiniBarMes(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const mesActual = new Date().getMonth();
    const lbls = typeof MESES_LABELS !== 'undefined' ? MESES_LABELS : ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { 
                labels: lbls, 
                datasets: [{ 
                    data, 
                    backgroundColor: function(context) {
                        const c = context.chart.ctx;
                        const area = context.chart.chartArea;
                        if (!area) return color;
                        const gradient = c.createLinearGradient(0, area.bottom, 0, area.top);
                        const isActual = context.dataIndex === mesActual;
                        const baseCol = isActual ? color : color + '66';
                        gradient.addColorStop(0, baseCol.replace(')', ', 0.5)').replace('rgb', 'rgba'));
                        gradient.addColorStop(1, baseCol);
                        return gradient;
                    },
                    borderRadius: 4, 
                    borderSkipped: false 
                }] 
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 8,
                        cornerRadius: 6,
                        callbacks: {
                            title: items => lbls[items[0].dataIndex] ?? items[0].label,
                            label: item => ` ${item.parsed.y}`
                        }
                    }
                },
                scales: {
                    x: {
                        display: true, grid: { display: false }, border: { display: false },
                        ticks: {
                            font: { size: 8, weight: '600' }, color: '#adb5bd', maxRotation: 0,
                            callback: (_, idx) => ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'][idx] ?? ''
                        }
                    },
                    y: { 
                        display: false, 
                        beginAtZero: true,
                        title: { display: true, text: 'Valor', color: '#64748b', font: { size: 9, weight: '600' } }
                    }
                }
            }
        });
    } catch (e) { }
}

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
                        min: 0, max: 100,
                        grid: { color: 'rgba(0,0,0,.05)' }, border: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', callback: v => v + '%', stepSize: 20 },
                        title: { display: true, text: '% Cumplimiento', color: '#64748b', font: { size: 11, weight: '600' } }
                    },
                    y: { 
                        grid: { display: false }, 
                        border: { display: false }, 
                        ticks: { font: { size: 11 }, color: '#334155' },
                        title: { display: true, text: 'Ámbito', color: '#64748b', font: { size: 11, weight: '600' } }
                    }
                }
            },
            plugins: [lineaMeta]
        });
    } catch (e) { }
}

function makeCalendarioMetas(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const lbls = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { 
                labels: lbls, 
                datasets: [{ 
                    data, 
                    backgroundColor: function(context) {
                        const c = context.chart.ctx;
                        const area = context.chart.chartArea;
                        if (!area) return color;
                        const gradient = c.createLinearGradient(0, area.bottom, 0, area.top);
                        const v = context.dataset.data[context.dataIndex];
                        const baseCol = v > 0 ? color : color + '28';
                        gradient.addColorStop(0, baseCol.replace(')', ', 0.6)').replace('rgb', 'rgba'));
                        gradient.addColorStop(1, baseCol);
                        return gradient;
                    },
                    borderRadius: 7, 
                    borderSkipped: false 
                }] 
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        cornerRadius: 8,
                        callbacks: {
                            label: item => {
                                const v = item.parsed.y;
                                return v > 0 ? ` ${v} meta${v !== 1 ? 's' : ''} — clic para filtrar` : ' Sin metas';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,.06)', drawBorder: false }, 
                        ticks: { font: { size: 10 }, stepSize: 1, color: '#94a3b8' },
                        title: { display: true, text: 'N* Metas', color: '#64748b', font: { size: 11, weight: '600' } }
                    }
                },
                onClick: (event, elements) => {
                    if (!elements.length) return;
                    const mesNum = elements[0].index + 1;
                    if (mesMetaActivo === mesNum) mostrarTodasMetas();
                    else filtrarMetasPorMes(mesNum, lbls[elements[0].index]);
                }
            }
        });
    } catch (e) { }
}

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
                datasets: [{ 
                    data, 
                    backgroundColor: labels.map((_, i) => PALETA_AMBITO[i % PALETA_AMBITO.length]), 
                    borderWidth: 2, 
                    borderColor: '#fff', 
                    hoverOffset: 12 
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10, weight: '600' }, padding: 12, boxWidth: 10 } },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: c => {
                                const pct = total > 0 ? Math.round(c.parsed / total * 100) : 0;
                                return ` ${c.label}: ${c.parsed} (${pct}%)`;
                            }
                        }
                    }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            },
            plugins: [{
                id: 'donutCenterTextAmbito',
                beforeDraw: function(chart) {
                    const { width, height, ctx } = chart;
                    ctx.restore();
                    const sum = chart.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    
                    const fontSizeTotal = (height / 120).toFixed(2);
                    ctx.font = "bold " + fontSizeTotal + "em 'Inter', sans-serif";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#1e293b";
                    
                    const text = sum.toString();
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;
                    
                    ctx.fillText(text, textX, textY);
                    
                    const fontSizeLabel = (height / 300).toFixed(2);
                    ctx.font = "600 " + fontSizeLabel + "em 'Inter', sans-serif";
                    ctx.fillStyle = "#64748b";
                    const labelText = "TOTAL";
                    const labelX = Math.round((width - ctx.measureText(labelText).width) / 2);
                    ctx.fillText(labelText, labelX, textY + 22);
                    
                    ctx.save();
                }
            }]
        });
    } catch (e) { }
}

function makeBarFormacionSolicitada(canvasId, dataItems) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !dataItems?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();

    const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const dataValues = dataItems.map(item => item.inscripciones);
    const courseNames = dataItems.map(item => item.nombre_curso);
    const maxVals = dataItems.map(item => item.max_participantes);

    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: function(context) {
                        const c = context.chart.ctx;
                        const area = context.chart.chartArea;
                        if (!area) return PALETA_AMBITO[context.dataIndex % PALETA_AMBITO.length];
                        const gradient = c.createLinearGradient(0, area.bottom, 0, area.top);
                        const baseCol = PALETA_AMBITO[context.dataIndex % PALETA_AMBITO.length];
                        gradient.addColorStop(0, baseCol.replace(')', ', 0.6)').replace('rgb', 'rgba'));
                        gradient.addColorStop(1, baseCol);
                        return gradient;
                    },
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: items => {
                                const idx = items[0].dataIndex;
                                return ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'][idx];
                            },
                            label: c => {
                                const idx = c.dataIndex;
                                const course = courseNames[idx];
                                const insc = dataValues[idx];
                                if (insc === 0) return ' Sin inscripciones registradas';
                                return ` Curso: ${course}`;
                            },
                            afterLabel: c => {
                                const idx = c.dataIndex;
                                const insc = dataValues[idx];
                                const max = maxVals[idx];
                                if (insc === 0) return '';
                                const pct = max > 0 ? Math.round(insc / max * 100) : 0;
                                return ` Inscripciones: ${insc} / ${max} (${pct}% ocupación)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,.05)', drawBorder: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: 1, precision: 0 },
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'N* Inscripciones',
                            color: '#64748b',
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    } catch (e) { }
}

/* ════════════════════════════════════════════════════════════
   CARDS SELECCIONABLES — Indicadores semáforo del resumen
════════════════════════════════════════════════════════════ */

const PANEL_CHARTS = {
    utilizacion: 'chartUtilizacionDonut',
    formacion: 'chartFormacionDonut',
    asignacion: 'chartAsignacionDonut',
    ejecucion: 'chartEjecucionDonut',
};

const PANEL_ACCENTS = {
    utilizacion: '#0ea5e9',
    formacion: '#28c76f',
    asignacion: '#7367f0',
    ejecucion: '#f59e0b',
};

let kpiPanelActivo = null;

/* ════════════════════════════════════════════════════════════
   Plugin interno: dibuja el valor numérico dentro de cada
   segmento del donut. Registrado una sola vez en Chart.js.
════════════════════════════════════════════════════════════ */
const pluginValoresDonut = {
    id: 'valoresDonut',
    afterDraw(chart) {
        if (chart.config.type !== 'doughnut') return;
        const { ctx, data } = chart;
        const meta = chart.getDatasetMeta(0);
        if (!meta || !meta.data) return;

        meta.data.forEach((arc, i) => {
            const value = data.datasets[0].data[i];
            if (!value || value === 0) return;

            const startAngle = arc.startAngle;
            const endAngle = arc.endAngle;
            const midAngle = (startAngle + endAngle) / 2;

            const innerR = arc.innerRadius;
            const outerR = arc.outerRadius;
            const midR = innerR + (outerR - innerR) * 0.58;

            const x = arc.x + midR * Math.cos(midAngle);
            const y = arc.y + midR * Math.sin(midAngle);

            const arcLength = Math.abs(endAngle - startAngle);
            if (arcLength < 0.28) return;

            ctx.save();
            ctx.font = 'bold 13px Roboto, sans-serif';
            ctx.fillStyle = '#fff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0,0,0,0.35)';
            ctx.shadowBlur = 3;
            ctx.fillText(value, x, y);
            ctx.restore();
        });
    }
};

/* Registrar el plugin una sola vez cuando Chart.js esté listo */
(function registrarPlugin() {
    if (typeof Chart !== 'undefined') {
        Chart.register(pluginValoresDonut);
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            const t = setInterval(() => {
                if (typeof Chart !== 'undefined') {
                    Chart.register(pluginValoresDonut);
                    clearInterval(t);
                }
            }, 100);
        });
    }
})();


/* ════════════════════════════════════════════════════════════
   INICIALIZACIÓN
════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    inicializarCardsSeleccionables();
});

function inicializarCardsSeleccionables() {
    document.querySelectorAll('.panel-mini-kpi.kpi-seleccionable').forEach(card => {
        card.addEventListener('click', () => toggleKpiPanel(card.dataset.kpi));
    });

    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-close-panel]');
        if (btn) { e.stopPropagation(); cerrarKpiPanel(btn.dataset.closePanel); }
    });

    document.querySelectorAll('.kpi-mini-tab-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            activarMiniTab(btn.dataset.panel, btn.dataset.tab, btn);
        });
    });
}


/* ════════════════════════════════════════════════════════════
   TOGGLE / ABRIR / CERRAR PANEL
════════════════════════════════════════════════════════════ */
function toggleKpiPanel(kpiId) {
    if (kpiPanelActivo === kpiId) cerrarKpiPanel(kpiId);
    else {
        if (kpiPanelActivo) cerrarKpiPanel(kpiPanelActivo, false);
        abrirKpiPanel(kpiId);
    }
}

function abrirKpiPanel(kpiId) {
    const panel = document.getElementById('panel-' + kpiId);
    const card = document.querySelector(`.kpi-seleccionable[data-kpi="${kpiId}"]`);
    const badge = document.getElementById('badge-' + kpiId);
    if (!panel || !card) return;

    kpiPanelActivo = kpiId;
    card.classList.add('kpi-activa');

    if (badge) {
        badge.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg> Ocultar`;
        badge.style.background = PANEL_ACCENTS[kpiId] + '18';
        badge.style.color = PANEL_ACCENTS[kpiId];
    }

    panel.style.display = 'block';
    panel.style.opacity = '0';
    panel.style.transform = 'translateY(-8px)';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        panel.style.transition = 'opacity .28s ease, transform .28s ease';
        panel.style.opacity = '1';
        panel.style.transform = 'translateY(0)';
    }));

    const chartId = PANEL_CHARTS[kpiId];
    if (chartId && !graficasCreadas[chartId]) {
        setTimeout(() => renderKpiMiniChart(kpiId), 130);
    }

    setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 70);
}

function cerrarKpiPanel(kpiId, resetActivo = true) {
    const panel = document.getElementById('panel-' + kpiId);
    const card = document.querySelector(`.kpi-seleccionable[data-kpi="${kpiId}"]`);
    const badge = document.getElementById('badge-' + kpiId);
    if (!panel) return;

    if (resetActivo) kpiPanelActivo = null;
    card?.classList.remove('kpi-activa');

    if (badge) {
        badge.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg> Ver detalle`;
        badge.style.background = '';
        badge.style.color = '';
    }

    panel.style.transition = 'opacity .2s ease, transform .2s ease';
    panel.style.opacity = '0';
    panel.style.transform = 'translateY(-6px)';
    setTimeout(() => {
        panel.style.display = 'none';
        panel.style.transition = '';
        panel.style.transform = '';
    }, 220);
}


/* ════════════════════════════════════════════════════════════
   TABS INTERNOS
════════════════════════════════════════════════════════════ */
function activarMiniTab(panelId, tabId, btnEl) {
    const accent = PANEL_ACCENTS[panelId] || '#6366f1';

    document.querySelectorAll(`.kpi-mini-tab-btn[data-panel="${panelId}"]`).forEach(b => {
        b.style.color = '#64748b';
        b.style.borderBottomColor = 'transparent';
        b.style.fontWeight = '600';
    });

    btnEl.style.color = accent;
    btnEl.style.borderBottomColor = accent;
    btnEl.style.fontWeight = '700';

    const panelEl = document.getElementById('panel-' + panelId);
    if (!panelEl) return;

    panelEl.querySelectorAll('.kpi-mini-tab-panel').forEach(p => p.style.display = 'none');

    const target = document.getElementById(`tab-${panelId}-${tabId}`);
    if (target) {
        target.style.display = 'block';
        target.style.animation = 'kpiFadeIn .2s ease';
    }
}


/* ════════════════════════════════════════════════════════════
   FUNCIÓN BASE: Donut con valores numéricos en segmentos
════════════════════════════════════════════════════════════ */
function makeDonutConValores(canvasId, labels, data, colores) {
    const ctx = document.getElementById(canvasId);
    if (!ctx || !labels?.length) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();

    try {
        graficasCreadas[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colores,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: '600' },
                            padding: 15,
                            boxWidth: 12,
                            generateLabels(chart) {
                                const ds = chart.data.datasets[0];
                                return chart.data.labels.map((label, i) => ({
                                    text: `${label}: ${ds.data[i]}`,
                                    fillStyle: ds.backgroundColor[i],
                                    strokeStyle: ds.backgroundColor[i],
                                    lineWidth: 0,
                                    hidden: false,
                                    index: i,
                                    datasetIndex: 0,
                                    pointStyle: 'rectRounded',
                                }));
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(c) {
                                const total = c.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round(c.parsed / total * 100) : 0;
                                return ` ${c.label}: ${c.parsed} (${pct}%)`;
                            }
                        }
                    }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    } catch (e) { }
}


/* ════════════════════════════════════════════════════════════
   RENDERS POR PANEL
════════════════════════════════════════════════════════════ */
function renderKpiMiniChart(kpiId) {
    const d = window.RESUMEN_KPI;
    if (!d) return;

    switch (kpiId) {

        case 'utilizacion':
            makeDonutConValores(
                'chartUtilizacionDonut',
                ['Asignados', 'Cupos libres'],
                [d.utilizacion.inscritos, d.utilizacion.libres],
                ['#0ea5e9', '#ef4444']
            );
            break;

        case 'formacion': {
            const labels = ['En formación', 'Disponibles'];
            const vals = [d.formacion.enFormacion, d.formacion.disponibles];
            const cols = ['#28c76f', '#f59e0b'];
            if (d.formacion.otros > 0) {
                labels.push('Otros estados');
                vals.push(d.formacion.otros);
                cols.push('#e2e8f0');
            }
            makeDonutConValores('chartFormacionDonut', labels, vals, cols);
            break;
        }

        case 'asignacion':
            makeDonutConValores(
                'chartAsignacionDonut',
                ['En formación', 'Asignados', 'Sin asignar'],
                [d.asignacion.enFormacion, d.asignacion.asignados, d.asignacion.sinAsignar],
                ['#7367f0', '#0ea5e9', '#cbd5e1']
            );
            break;

        case 'ejecucion':
            makeDonutConValores(
                'chartEjecucionDonut',
                ['Ejecutados', 'Pendientes'],
                [d.ejecucion.ejecutados, d.ejecucion.pendientes],
                ['#28c76f', '#ef4444']
            );
            break;
    }
}


/* ════════════════════════════════════════════════════════════
   KEYFRAME fade-in para tabs (inyectado una sola vez)
════════════════════════════════════════════════════════════ */
(function () {
    if (document.getElementById('kpi-mini-style')) return;
    const s = document.createElement('style');
    s.id = 'kpi-mini-style';
    s.textContent = `
        @keyframes kpiFadeIn {
            from { opacity:0; transform:translateY(4px); }
            to   { opacity:1; transform:translateY(0); }
        }
    `;
    document.head.appendChild(s);
})();
/* ============================================================
   home.js  —  INCES Sistema de Control
   ============================================================ */

const PALETA_ESTADO = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
const PALETA_GENERO = ['#0ea5e9', '#ec4899'];
const PALETA_GRADO = ['#6366f1', '#f59e0b', '#10b981'];
const PALETA_TIPO = ['#7367f0', '#28c76f', '#ff9f43', '#ea5455'];
const PALETA_ESTATUS = ['#28c76f', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#6c757d'];
const PALETA_MODAL = ['#0ea5e9', '#7367f0', '#28c76f'];
const PALETA_TURNO = ['#ff9f43', '#0ea5e9'];
const COLOR_METAS = '#ef4444';

const graficasCreadas = {};

function waitForChart(callback, maxAttempts = 20) {
    let attempts = 0;
    const interval = setInterval(() => {
        attempts++;
        if (typeof Chart !== 'undefined') { clearInterval(interval); callback(); }
        else if (attempts >= maxAttempts) { clearInterval(interval); }
    }, 100);
}

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Sidebar inmediatamente
    initSidebar();

    // Si estamos en una vista con gráficas (inicio, reportes, etc), intentamos cargar Home
    // HOME_CHARTS es usualmente inyectada en inicio.php
    waitForChart(initHome);
});

function initSidebar() {
    /* ── Sidebar ── */
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('toggle-btn');
    if (sidebar && toggle) {
        // Remover listeners viejos clonando el node si es necesario, o simplemente añadimos
        // En este caso como es una carga de página, solo añadimos.
        toggle.addEventListener('click', () => sidebar.classList.toggle('expand'));
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('mouseenter', () => { link.style.transition = 'transform .3s ease'; link.style.transform = 'scale(1.05)'; });
            link.addEventListener('mouseleave', () => { link.style.transform = 'scale(1)'; });
        });
    }

    // Sub-menús (Dropdowns)
    document.querySelectorAll('.sidebar-item.dropdown').forEach(item => {
        const link = item.querySelector('.sidebar-link');
        if (link) {
            link.addEventListener('click', e => {
                e.preventDefault();
                item.classList.toggle('sub-menu-toggle');
            });
        }
    });
}

function initHome() {

    /* ── Iconos toggle ── */
    const ICO_ABAJO = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>`;
    const ICO_ARRIBA = `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>`;

    /* ════ PANEL BÁSICO ════ */
    let panelBasicoActivo = null;

    function togglePanelBasico(kpiId) {
        const esMismo = (panelBasicoActivo === kpiId);
        if (panelBasicoActivo) {
            const prev = panelBasicoActivo;
            document.getElementById('panel-basic-' + prev)?.classList.remove('kpi-panel-abierto');
            document.getElementById('badge-basic-' + prev).innerHTML = ICO_ABAJO + ' Ver desglose';
            document.querySelector(`[data-kpi-basic="${prev}"]`)?.classList.remove('kpi-activo');
            panelBasicoActivo = null;
            if (prev === 'metas') { mesMetaActivo = null; }
        }
        if (esMismo) return;
        const panel = document.getElementById('panel-basic-' + kpiId);
        if (!panel) return;
        panelBasicoActivo = kpiId;
        panel.classList.add('kpi-panel-abierto');
        document.getElementById('badge-basic-' + kpiId).innerHTML = ICO_ARRIBA + ' Ocultar';
        document.querySelector(`[data-kpi-basic="${kpiId}"]`)?.classList.add('kpi-activo');
        setTimeout(() => { renderGraficasBasicas(kpiId); }, 80);
    }

    document.querySelectorAll('.kpi-basic-btn').forEach(card => {
        card.addEventListener('click', () => togglePanelBasico(card.dataset.kpiBasic));
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); togglePanelBasico(card.dataset.kpiBasic); }
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (panelBasicoActivo) togglePanelBasico(panelBasicoActivo);
    });

    /* ── Barras animadas ── */
    document.querySelectorAll('.kpi-adv-barra').forEach(b => {
        const w = b.style.width; b.style.width = '0%';
        requestAnimationFrame(() => requestAnimationFrame(() => { b.style.width = w; }));
    });

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }

    const btnVerTodos = document.getElementById('btn-metas-ver-todos');
    if (btnVerTodos) btnVerTodos.addEventListener('click', () => mostrarTodasMetas());

    /* ══════════════════════════════════════════════════════════
       NAVEGACIÓN — clic en fila de PARTICIPANTE
       <tr class="part-row-link" data-cedula="...">
       → participante.php?buscar=CEDULA
    ══════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.part-row-link');
        if (!fila) return;
        const cedula = fila.dataset.cedula;
        if (!cedula) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(0,200,224,.18)';
        setTimeout(() => { window.location.href = `participante.php?buscar=${encodeURIComponent(cedula)}`; }, 150);
    });

    /* ══════════════════════════════════════════════════════════
       NAVEGACIÓN — clic en fila de CURSO
       <tr class="curso-row-link" data-nombre="...">
       → cursos.php?buscar=NOMBRE_CURSO
    ══════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.curso-row-link');
        if (!fila) return;
        const nombre = fila.dataset.nombre;
        if (!nombre) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(40,199,111,.18)';
        setTimeout(() => { window.location.href = `cursos.php?buscar=${encodeURIComponent(nombre)}`; }, 150);
    });

    /* ══════════════════════════════════════════════════════════
       NAVEGACIÓN — clic en fila de META
       <tr class="meta-row-link" data-id="...">
       → metas.php?buscar=ID_META
    ══════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.meta-row-link');
        if (!fila) return;
        const id = fila.dataset.id;
        if (!id) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(239,68,68,.15)';
        setTimeout(() => { window.location.href = `metas.php?buscar=${encodeURIComponent(id)}`; }, 150);
    });

    /* ══════════════════════════════════════════════════════════
       NAVEGACIÓN — clic en fila de DOCENTE
       <tr class="docente-row-link" data-cedula="...">
       → docente.php?buscar=CEDULA
    ══════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const fila = e.target.closest('tr.docente-row-link');
        if (!fila) return;
        const cedula = fila.dataset.cedula;
        if (!cedula) return;
        fila.style.transition = 'background .15s';
        fila.style.background = 'rgba(115,103,240,.18)';
        setTimeout(() => { window.location.href = `docente.php?buscar=${encodeURIComponent(cedula)}`; }, 150);
    });

    setTimeout(() => { if (window.HOME_CHARTS) renderMiniChartsMensuales(); }, 300);
}

/* ══════════════════════════════
   GRÁFICAS
══════════════════════════════ */
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
    } catch (e) { }
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
    } catch (e) { }
}

function makeMiniBarMes(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const mesActual = new Date().getMonth();
    const bgColors = data.map((_, i) => i === mesActual ? color : color + '55');
    const borderColors = data.map((_, i) => i === mesActual ? color : 'transparent');
    const lbls = typeof MESES_LABELS !== 'undefined' ? MESES_LABELS : ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
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
                            callback: (_, idx) => ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'][idx] ?? ''
                        }
                    },
                    y: { display: false, beginAtZero: true }
                }
            }
        });
    } catch (e) { }
}

/* ══════════════════════════════════════════════════════
   CALENDARIO DE METAS
══════════════════════════════════════════════════════ */
let mesMetaActivo = null;

function makeCalendarioMetas(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    if (graficasCreadas[canvasId]) graficasCreadas[canvasId].destroy();
    const lbls = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const bgColors = data.map(v => v > 0 ? color : color + '28');
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
    } catch (e) { }
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
    const sinDatos = document.getElementById('metas-sin-datos-mes');
    if (btnTodos) btnTodos.classList.remove('d-none');
    if (wrapLabel) wrapLabel.classList.remove('d-none');
    if (spanLabel) spanLabel.textContent = mesLabel;
    if (sinDatos) sinDatos.style.display = visibles === 0 ? '' : 'none';
    const chart = graficasCreadas['chartMetasMes'];
    if (chart) {
        const color = COLOR_METAS;
        chart.data.datasets[0].backgroundColor = chart.data.labels.map((_, i) => i === mesNum - 1 ? color : color + '28');
        chart.data.datasets[0].borderColor = chart.data.labels.map((_, i) => i === mesNum - 1 ? color : 'transparent');
        chart.update('none');
    }
}

function mostrarTodasMetas() {
    const tbody = document.querySelector('#tabla-metas-panel tbody');
    if (!tbody) return;
    tbody.querySelectorAll('tr[data-mes-meta]').forEach(f => f.style.display = '');
    const btnTodos = document.getElementById('btn-metas-ver-todos');
    const wrapLabel = document.getElementById('metas-mes-activo-wrap');
    const sinDatos = document.getElementById('metas-sin-datos-mes');
    if (btnTodos) btnTodos.classList.add('d-none');
    if (wrapLabel) wrapLabel.classList.add('d-none');
    if (sinDatos) sinDatos.style.display = 'none';
    mesMetaActivo = null;
    const chart = graficasCreadas['chartMetasMes'];
    if (chart) {
        const color = COLOR_METAS;
        const data = chart.data.datasets[0].data;
        chart.data.datasets[0].backgroundColor = data.map(v => v > 0 ? color : color + '28');
        chart.data.datasets[0].borderColor = data.map(v => v > 0 ? color : 'transparent');
        chart.update('none');
    }
}

function renderMiniChartsMensuales() {
    if (!window.HOME_CHARTS) return;
    const d = window.HOME_CHARTS;
    makeMiniBarMes('chartPartMes', d.partMes.data, '#00c8e0');
    makeMiniBarMes('chartCursosMes', d.cursosMes.data, '#28c76f');
    makeMiniBarMes('chartDocentesMes', d.docentesMes.data, '#7367f0');
}

function renderGraficasBasicas(kpiId) {
    if (!window.HOME_CHARTS) { setTimeout(() => renderGraficasBasicas(kpiId), 200); return; }
    const d = window.HOME_CHARTS;
    switch (kpiId) {
        case 'participantes':
            makeDonut('chartPartEstado', d.partEstado.labels, d.partEstado.data, PALETA_ESTADO);
            makeDonut('chartPartGenero', d.partGenero.labels, d.partGenero.data, PALETA_GENERO);
            makeBar('chartPartGrado', d.partGrado.labels, d.partGrado.data, PALETA_GRADO);
            break;
        case 'cursos':
            makeDonut('chartCursosEstatus', d.cursosEstatus.labels, d.cursosEstatus.data, PALETA_ESTATUS);
            makeDonut('chartCursosModalidad', d.cursosModal.labels, d.cursosModal.data, PALETA_MODAL);
            makeBar('chartCursosTurno', d.cursosTurno.labels, d.cursosTurno.data, PALETA_TURNO);
            break;
        case 'docentes':
            makeDonut('chartDocTipo', d.docTipo.labels, d.docTipo.data, PALETA_TIPO);
            makeBar('chartDocEstado', d.docEstado.labels, d.docEstado.data, PALETA_ESTADO);
            makeDonut('chartDocGenero', d.docGenero.labels, d.docGenero.data, PALETA_GENERO);
            break;
        case 'metas':
            makeCalendarioMetas('chartMetasMes', d.metasMes.data, COLOR_METAS);
            break;
    }
}

/* ══════════════════════════════════════════════════════════════════════
   CALENDARIO — lógica completa
══════════════════════════════════════════════════════════════════════ */

// Llamar dentro de initHome() → al final, justo antes del cierre de la función:
//   initCalendario();

function initCalendario() {
    const MESES_NOMBRES = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const hoy = new Date();
    let calAnio = hoy.getFullYear();
    let calMes = hoy.getMonth();       // 0-based
    let diaSelec = null;                 // { anio, mes, dia }

    const grid = document.getElementById('cal-grid');
    const labelMesAnio = document.getElementById('cal-mes-anio-label');
    const labelSelec = document.getElementById('cal-sel-label');
    const btnPrev = document.getElementById('cal-prev');
    const btnNext = document.getElementById('cal-next');
    const btnHoy = document.getElementById('cal-hoy');

    if (!grid) return; // si no existe el elemento, salir

    /* ── Renderiza el mes activo ── */
    function renderMes() {
        // Actualizar etiqueta del mes
        if (labelMesAnio) {
            labelMesAnio.textContent = `${MESES_NOMBRES[calMes]} ${calAnio}`;
        }

        // Calcular primer día del mes y total de días
        const primerDia = new Date(calAnio, calMes, 1).getDay();    // 0=Dom
        const diasEnMes = new Date(calAnio, calMes + 1, 0).getDate();
        const diasMesAnt = new Date(calAnio, calMes, 0).getDate();

        // Animación de entrada
        grid.classList.remove('cal-anim');
        void grid.offsetWidth; // force reflow
        grid.classList.add('cal-anim');

        grid.innerHTML = '';

        // Días del mes anterior (relleno)
        for (let i = primerDia - 1; i >= 0; i--) {
            const dia = diasMesAnt - i;
            const el = crearDia(dia, true, false, false);
            grid.appendChild(el);
        }

        // Días del mes actual
        for (let d = 1; d <= diasEnMes; d++) {
            const esHoy = (d === hoy.getDate() && calMes === hoy.getMonth() && calAnio === hoy.getFullYear());
            const esFds = (() => {
                const dow = new Date(calAnio, calMes, d).getDay();
                return dow === 0 || dow === 6;
            })();
            const esSel = diaSelec
                && diaSelec.dia === d
                && diaSelec.mes === calMes
                && diaSelec.anio === calAnio;

            const el = crearDia(d, false, esHoy, esFds, esSel);
            el.addEventListener('click', () => seleccionarDia(d));
            grid.appendChild(el);
        }

        // Días del mes siguiente (relleno hasta completar 6 filas = 42 celdas)
        const totalCeldas = 42;
        const celdaActual = primerDia + diasEnMes;
        const restantes = totalCeldas - celdaActual;
        for (let d = 1; d <= restantes; d++) {
            const el = crearDia(d, true, false, false);
            grid.appendChild(el);
        }

        // Actualizar label de día seleccionado
        actualizarLabelSel();
    }

    /* ── Crea un elemento <div> para un día del grid ── */
    function crearDia(num, otroMes, esHoy, esFds, esSel = false) {
        const el = document.createElement('div');
        el.className = 'cal-dia';
        el.textContent = num;

        if (otroMes) el.classList.add('cal-dia-otro-mes');
        if (esHoy) el.classList.add('cal-dia-hoy');
        if (esFds && !otroMes) el.classList.add('cal-dia-fds');
        if (esSel) el.classList.add('cal-dia-seleccionado');

        return el;
    }

    /* ── Lógica de selección de día ── */
    function seleccionarDia(d) {
        const yaSel = diaSelec
            && diaSelec.dia === d
            && diaSelec.mes === calMes
            && diaSelec.anio === calAnio;

        diaSelec = yaSel ? null : { dia: d, mes: calMes, anio: calAnio };
        renderMes();
    }

    /* ── Actualiza el label inferior con la fecha seleccionada ── */
    function actualizarLabelSel() {
        if (!labelSelec) return;
        if (!diaSelec) {
            labelSelec.textContent = '';
            labelSelec.style.opacity = '0';
            return;
        }
        const diasSem = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const fecha = new Date(diaSelec.anio, diaSelec.mes, diaSelec.dia);
        labelSelec.textContent = `${diasSem[fecha.getDay()]} ${diaSelec.dia} de ${MESES_NOMBRES[diaSelec.mes]} ${diaSelec.anio}`;
        labelSelec.style.opacity = '1';
    }

    /* ── Controles de navegación ── */
    if (btnPrev) btnPrev.addEventListener('click', () => {
        calMes--;
        if (calMes < 0) { calMes = 11; calAnio--; }
        renderMes();
    });

    if (btnNext) btnNext.addEventListener('click', () => {
        calMes++;
        if (calMes > 11) { calMes = 0; calAnio++; }
        renderMes();
    });

    if (btnHoy) btnHoy.addEventListener('click', () => {
        calAnio = hoy.getFullYear();
        calMes = hoy.getMonth();
        diaSelec = null;
        renderMes();
    });

    // Render inicial
    renderMes();
}

/* ── API pública para usar desde otros módulos ──────────────────────
   Ejemplo de uso futuro para el historial:

   window.HISTORIAL = {
       agregar(tipo, accion, usuario, fecha) {
           // tipo: 'add' | 'edit' | 'delete' | 'login'
           const lista = document.getElementById('historial-lista');
           const empty = document.getElementById('historial-empty');
           if (!lista) return;
           const li = document.createElement('li');
           li.className = `hc-timeline-item hc-ev-${tipo}`;
           li.innerHTML = `
               <div class="hc-tl-dot"></div>
               <div class="hc-tl-body">
                   <span class="hc-tl-accion">${accion}</span>
                   <span class="hc-tl-meta">${usuario} · ${fecha}</span>
               </div>`;
           lista.prepend(li);
           if (empty) empty.style.display = 'none';
           lista.style.display = '';
       }
   };
───────────────────────────────────────────────────────────────────── */
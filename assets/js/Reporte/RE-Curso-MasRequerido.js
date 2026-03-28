// ========== REPORTE INDEPENDIENTE: CURSO MÁS REQUERIDO ==========
// Archivo: RE-Curso-MasRequerido.js
// Función: cargarCursoMasRequerido (ID: 5)
// Conectado a: ../controlador/Reporte/api_reportes.php

(function () {

    const API_URL = '../controlador/Reporte/api_reportes.php';

    // ── Estado ────────────────────────────────────────────────────────
    let cursoRequeridoChart  = null;
    let paginaActual         = 1;
    let ordenActual          = { columna: 'score', direccion: 'desc' };
    const CURSOS_POR_PAGINA  = 10;
    let datosCompletosTop3   = [];
    let datosCompletosTabla  = [];
    const cacheAnios         = {};
    const anioActual         = new Date().getFullYear();

    // ── Utilidades generales ──────────────────────────────────────────
    function opcionesAnio(sel = anioActual) {
        return Array.from({ length: 5 }, (_, i) => anioActual - i)
            .map(a => `<option value="${a}" ${a === sel ? 'selected' : ''}>${a}</option>`)
            .join('');
    }

    async function fetchCursosAnio(anio) {
        if (cacheAnios[anio]) return cacheAnios[anio];
        const res = await fetch(`${API_URL}?accion=curso_mas_requerido&anio=${anio}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        const datos = await res.json();
        if (datos.error) throw new Error(datos.error);
        if (!Array.isArray(datos) || datos.length === 0)
            throw new Error(`No se encontraron datos para el año ${anio}.`);
        const resultado = normalizarYCalcularScores(datos);
        cacheAnios[anio] = resultado;
        return resultado;
    }

    function normalizarYCalcularScores(datos) {
        const cursos = datos.map(d => ({
            curso:         d.nombre_curso || '—',
            inscripciones: parseInt(d.total_inscripciones) || 0,
            participantes: parseInt(d.participantes_unicos) || 0,
            estatus:       d.estatus || '—',
            area:          d.area    || 'Sin área',
            score:         0,
            tasaLlenado:   0   // participantes / inscripciones → qué tan "lleno" se matriculó
        }));
        const maxInscr  = Math.max(...cursos.map(c => c.inscripciones), 1);
        const maxPartic = Math.max(...cursos.map(c => c.participantes), 1);

        cursos.forEach(c => {
            // Tasa de llenado: proporción de participantes únicos vs inscripciones totales
            // Un curso que siempre se llenó tiene tasa cercana a 1
            c.tasaLlenado = c.inscripciones > 0
                ? Math.min(c.participantes / c.inscripciones, 1)
                : 0;

            // Nueva fórmula — tres componentes:
            //  30 % demanda bruta (inscripciones normalizadas)
            //  40 % alcance (participantes únicos normalizados)
            //  30 % tasa de llenado (si siempre se llenó → máximo aporte)
            const normInscr  = (c.inscripciones / maxInscr)  * 100;
            const normPartic = (c.participantes / maxPartic) * 100;
            const normLlenado = c.tasaLlenado * 100;

            c.score = Math.round(normInscr * 0.30 + normPartic * 0.40 + normLlenado * 0.30);
        });

        cursos.sort((a, b) => b.score - a.score);
        return cursos;
    }

    function poblarSelectAreas(id, datos, valorActual = 'todas') {
        const sel = document.getElementById(id);
        if (!sel) return;
        const areas = [...new Set(datos.map(c => c.area))].sort();
        sel.innerHTML = '<option value="todas">Todas las áreas</option>';
        areas.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a; opt.textContent = a;
            if (a === valorActual) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    function setLoading(seccion, visible) {
        const el = document.getElementById(`loading${seccion}`);
        if (el) el.style.display = visible ? 'block' : 'none';
    }

    function setError(seccion, msg) {
        const el = document.getElementById(`error${seccion}`);
        if (!el) return;
        el.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${msg}`;
        el.style.display = 'block';
    }

    function clearError(seccion) {
        const el = document.getElementById(`error${seccion}`);
        if (el) el.style.display = 'none';
    }

    // ── Filtrado y ordenamiento de tabla ──────────────────────────────
    function getDatosFiltradosTabla() {
        const area = document.getElementById('selectAreaTabla')?.value || 'todas';
        const term = (document.getElementById('searchRankingTable')?.value || '').toLowerCase().trim();
        let datos  = area === 'todas' ? datosCompletosTabla
            : datosCompletosTabla.filter(c => c.area === area);
        if (term) datos = datos.filter(c =>
            c.curso.toLowerCase().includes(term)  ||
            c.area.toLowerCase().includes(term)   ||
            c.estatus.toLowerCase().includes(term)
        );
        return ordenarDatos(datos, ordenActual.columna, ordenActual.direccion);
    }

    function aplicarFiltrosTabla() {
        paginaActual = 1;
        const datos = getDatosFiltradosTabla();
        actualizarTabla(datos);
        configurarPaginacion(datos.length);
    }

    function irAPagina(np) {
        paginaActual = np;
        const datos = getDatosFiltradosTabla();
        actualizarTabla(datos);
        configurarPaginacion(datos.length);
    }

    function ordenarDatos(datos, columna, direccion) {
        return [...datos].sort((a, b) => {
            if (columna === 'curso') {
                const r = a.curso.toLowerCase().localeCompare(b.curso.toLowerCase());
                return direccion === 'asc' ? r : -r;
            }
            const campo = { inscripciones: 'inscripciones', participantes: 'participantes', score: 'score' }[columna] || 'score';
            return direccion === 'asc' ? a[campo] - b[campo] : b[campo] - a[campo];
        });
    }

    // ── Función principal ─────────────────────────────────────────────
    function cargarCursoMasRequerido() {
        const contentDiv = document.getElementById('reportContent');

        contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">

            <!-- Cabecera del reporte -->
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-trophy-fill me-2"></i>Curso más Requerido
                </h4>
            </div>

            <div class="card-body p-0">

                <!-- ══════════════════════════════════════════
                     SECCIÓN 1 — TOP 3
                ══════════════════════════════════════════ -->
                <div class="rp-chart-card mb-0 border-0 rounded-0"
                     style="border-bottom:1px solid var(--rp-border)!important;">
                    <div class="rp-chart-card-header">
                        <span class="rp-chart-title">
                            <i class="bi bi-trophy-fill text-warning me-2"></i>
                            TOP 3 CURSOS MÁS REQUERIDOS
                        </span>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="rp-toolbar-label">Año</span>
                            <select id="selectAnioTop3" class="rp-select">${opcionesAnio()}</select>
                            <span class="rp-toolbar-label">Área</span>
                            <select id="selectAreaTop3" class="rp-select" style="min-width:160px;">
                                <option value="todas">Todas las áreas</option>
                            </select>
                        </div>
                    </div>
                    <div class="rp-chart-body">
                        <div id="loadingTop3" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted small">Cargando datos...</span>
                        </div>
                        <div id="errorTop3" class="alert alert-danger mb-0" style="display:none;"></div>
                        <div id="contenidoTop3" style="display:none;">
                            <div class="row justify-content-center align-items-stretch g-3"
                                 id="top3Cards"></div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECCIÓN 2 — ESTADÍSTICAS
                ══════════════════════════════════════════ -->
                <div class="rp-chart-card mb-0 border-0 rounded-0"
                     style="border-bottom:1px solid var(--rp-border)!important;">
                    <div class="rp-chart-card-header">
                        <span class="rp-chart-title">
                            <i class="bi bi-bar-chart-fill me-2"></i>
                            ESTADÍSTICAS DE REQUERIMIENTO
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rp-toolbar-label">Año</span>
                            <select id="selectAnioStats" class="rp-select">${opcionesAnio()}</select>
                        </div>
                    </div>
                    <div class="rp-chart-body">
                        <div id="loadingStats" class="text-center py-4" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        </div>
                        <div id="errorStats" class="alert alert-danger mb-0" style="display:none;"></div>
                        <div id="contenidoStats" style="display:none;">
                            <div class="rp-kpi-grid" id="statsGrid"></div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECCIÓN 3 — GRÁFICA
                ══════════════════════════════════════════ -->
                <div class="rp-chart-card mb-0 border-0 rounded-0"
                     style="border-bottom:1px solid var(--rp-border)!important;">
                    <div class="rp-chart-card-header">
                        <span class="rp-chart-title">
                            <i class="bi bi-graph-up me-2"></i>
                            ÍNDICE DE REQUERIMIENTO POR ÁREA FORMATIVA
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rp-toolbar-label">Año</span>
                            <select id="selectAnioGrafica" class="rp-select">${opcionesAnio()}</select>
                        </div>
                    </div>
                    <div class="rp-chart-body">
                        <div id="loadingGrafica" class="text-center py-3" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        </div>
                        <div id="errorGrafica" class="alert alert-danger mb-3" style="display:none;"></div>
                        <div class="rp-chart-wrap rp-chart-wrap--lg">
                            <canvas id="graficaCursoMasRequerido"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECCIÓN 4 — TABLA RANKING
                ══════════════════════════════════════════ -->
                <div class="rp-chart-card mb-0 border-0 rounded-0">
                    <div class="rp-chart-card-header" style="flex-wrap:wrap; gap:.6rem;">
                        <span class="rp-chart-title">
                            <i class="bi bi-list-ol me-2"></i>RANKING COMPLETO
                        </span>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="rp-toolbar-label">Año</span>
                            <select id="selectAnioTabla" class="rp-select">${opcionesAnio()}</select>
                            <span class="rp-toolbar-label">Área</span>
                            <select id="selectAreaTabla" class="rp-select" style="min-width:160px;">
                                <option value="todas">Todas las áreas</option>
                            </select>
                            <div class="input-group input-group-sm" style="width:210px;">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted" style="font-size:.75rem;"></i>
                                </span>
                                <input type="text" id="searchRankingTable"
                                       class="form-control form-control-sm border-start-0"
                                       placeholder="Buscar curso, área, estatus...">
                            </div>
                        </div>
                    </div>
                    <div class="rp-chart-body">
                        <div id="loadingTabla" class="text-center py-4" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        </div>
                        <div id="errorTabla" class="alert alert-danger mb-0" style="display:none;"></div>
                        <div id="contenidoTabla">
                            <div class="table-responsive">
                                <table class="rp-table">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>
                                                <a href="#" class="sortable-header-rq" data-columna="curso">
                                                    Curso <i class="bi bi-arrow-down-up"></i>
                                                </a>
                                            </th>
                                            <th class="text-center" width="130">
                                                <a href="#" class="sortable-header-rq" data-columna="inscripciones">
                                                    Inscripciones <i class="bi bi-arrow-down-up"></i>
                                                </a>
                                            </th>
                                            <th class="text-center" width="150">
                                                <a href="#" class="sortable-header-rq" data-columna="participantes">
                                                    Participantes únicos <i class="bi bi-arrow-down-up"></i>
                                                </a>
                                            </th>
                                            <th class="text-center" width="160">
                                                <a href="#" class="sortable-header-rq" data-columna="score">
                                                    Índice Requerimiento <i class="bi bi-arrow-down"></i>
                                                </a>
                                            </th>
                                            <th class="text-center" width="120">Área</th>
                                            <th class="text-center" width="120">Estatus</th>
                                            <th class="text-center" width="130">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaRankingBodyRequerido"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <span class="text-muted small">
                                    Mostrando <span id="inicioPagina">1</span>–<span id="finPagina">10</span>
                                    de <span id="totalPaginacion">0</span> cursos
                                </span>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="paginacionCursos"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>`;

        // Carga paralela de todas las secciones
        cargarTop3(anioActual);
        cargarStats(anioActual);
        cargarGrafica(anioActual);
        cargarTabla(anioActual);

        // ── Event listeners ────────────────────────────────────────────

        // Top 3 — año
        document.getElementById('selectAnioTop3')?.addEventListener('change', e => {
            poblarSelectAreas('selectAreaTop3', [], 'todas'); // reset áreas
            cargarTop3(parseInt(e.target.value));
        });
        // Top 3 — área
        document.getElementById('selectAreaTop3')?.addEventListener('change', () => {
            const area  = document.getElementById('selectAreaTop3').value;
            const datos = area === 'todas' ? datosCompletosTop3
                : datosCompletosTop3.filter(c => c.area === area);
            renderTop3(datos);
        });

        // Estadísticas — año
        document.getElementById('selectAnioStats')?.addEventListener('change', e => {
            cargarStats(parseInt(e.target.value));
        });

        // Gráfica — año
        document.getElementById('selectAnioGrafica')?.addEventListener('change', e => {
            cargarGrafica(parseInt(e.target.value));
        });

        // Tabla — año
        document.getElementById('selectAnioTabla')?.addEventListener('change', e => {
            poblarSelectAreas('selectAreaTabla', [], 'todas'); // reset áreas
            cargarTabla(parseInt(e.target.value));
        });
        // Tabla — área
        document.getElementById('selectAreaTabla')?.addEventListener('change', () => {
            aplicarFiltrosTabla();
        });
        // Tabla — búsqueda
        document.getElementById('searchRankingTable')?.addEventListener('input', () => {
            aplicarFiltrosTabla();
        });

        // Ordenamiento de columnas (headers estáticos)
        configurarOrdenamiento();
    }

    // ── Sección 1: Top 3 ──────────────────────────────────────────────
    async function cargarTop3(anio) {
        setLoading('Top3', true);
        clearError('Top3');
        document.getElementById('contenidoTop3').style.display = 'none';
        try {
            const datos = await fetchCursosAnio(anio);
            datosCompletosTop3 = datos;
            poblarSelectAreas('selectAreaTop3', datos);
            renderTop3(datos);
            setLoading('Top3', false);
            document.getElementById('contenidoTop3').style.display = 'block';
        } catch (err) {
            setLoading('Top3', false);
            setError('Top3', err.message);
            console.error('[RE-Curso-MasRequerido] Top3 error:', err);
        }
    }

    // ── Calcular umbrales de prioridad con percentiles sobre todos los datos ──
    function calcularUmbralesPrioridad(datos) {
        if (!datos.length) return { alta: 75, media: 40 };
        const scores = datos.map(c => c.score).sort((a, b) => a - b);
        const n = scores.length;
        // P75 → umbral de alta prioridad | P40 → umbral de media prioridad
        const p75 = scores[Math.floor(n * 0.75)] ?? scores[n - 1];
        const p40 = scores[Math.floor(n * 0.40)] ?? scores[0];
        return { alta: p75, media: p40 };
    }

    function getPrioridad(score, umbrales) {
        if (score >= umbrales.alta) return {
            badge: 'bg-success',         text: 'ALTA PRIORIDAD',
            accionClass: 'text-success', accionIcon: 'bi-check-circle',
            accionText:  'Reabrir inmediatamente'
        };
        if (score >= umbrales.media) return {
            badge: 'bg-warning text-dark', text: 'MEDIA PRIORIDAD',
            accionClass: 'text-warning',   accionIcon: 'bi-clock',
            accionText:  'Programar pronto'
        };
        return {
            badge: 'bg-info',          text: 'BAJA PRIORIDAD',
            accionClass: 'text-info',  accionIcon: 'bi-calendar',
            accionText:  'Considerar para futuro'
        };
    }

    function renderTop3(data) {
        const container = document.getElementById('top3Cards');
        if (!container) return;

        const iconosPosicion = [
            { icon: 'bi-trophy-fill', color: 'text-warning' },
            { icon: 'bi-award-fill',  color: 'text-secondary' },
            { icon: 'bi-award',       color: 'text-danger' }
        ];

        const top = data.slice(0, 3);

        if (!top.length) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="rp-empty">
                        <div class="rp-empty-icon"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                        <p class="rp-empty-title">Sin datos para mostrar</p>
                        <p class="rp-empty-desc">No hay cursos registrados con el filtro seleccionado.</p>
                    </div>
                </div>`;
            return;
        }

        // Umbrales calculados sobre el conjunto completo visible (data)
        const umbrales = calcularUmbralesPrioridad(data);

        const posLabels = ['1°', '2°', '3°'];

        const tarjetas = top.map((item, i) => {
            const ic  = iconosPosicion[i];
            const pr  = getPrioridad(item.score, umbrales);
            const cardId = `rp-cmr-flip-${i}`;
            return `
            <div class="col-md-4">
                <div class="rp-cmr-flip" id="${cardId}" role="button" tabindex="0"
                     title="Clic para ver más detalles"
                     onclick="this.classList.toggle('rp-cmr-flip--flipped')"
                     onkeydown="if(event.key==='Enter'||event.key===' ')this.classList.toggle('rp-cmr-flip--flipped')">
                    <!-- FRENTE -->
                    <div class="rp-cmr-flip__front card border-0 shadow overflow-hidden">
                        <div class="rp-cmr-corner-label rp-cmr-corner-label--${i}">${posLabels[i]}</div>
                        <div class="card-body text-center py-3 d-flex flex-column justify-content-center">
                            <div class="mb-2 mt-3">
                                <i class="bi ${ic.icon} ${ic.color}" style="font-size:2.8rem;"></i>
                            </div>
                            <h4 class="rp-cmr-nombre">${item.curso}</h4>
                            <div class="mt-1">
                                <div class="fs-5 fw-bold text-primary">${item.score}</div>
                                <small class="text-muted">Índice de requerimiento</small>
                            </div>
                            <div class="mt-1">
                                <span class="badge ${pr.badge}">
                                    <i class="bi bi-arrow-up me-1"></i>${pr.text}
                                </span>
                            </div>
                            <div class="mt-1">
                                <small class="${pr.accionClass}">
                                    <i class="bi ${pr.accionIcon} me-1"></i>
                                    <strong>${pr.accionText}</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                    <!-- REVERSO -->
                    <div class="rp-cmr-flip__back rp-cmr-flip__back--${i} card border-0 shadow overflow-hidden">
                        <div class="rp-cmr-corner-label rp-cmr-corner-label--${i}">${posLabels[i]}</div>
                        <div class="card-body pt-5 pb-3 px-3 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold mb-0" style="font-size:.88rem;line-height:1.4;">${item.curso}</h5>
                                <span class="badge ${pr.badge} ms-2 flex-shrink-0">${pr.text}</span>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-people-fill text-info me-1"></i>Inscritos únicos
                                </span>
                                <strong>${item.participantes.toLocaleString()}</strong>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-journal-bookmark text-secondary me-1"></i>Inscripciones
                                </span>
                                <strong>${item.inscripciones.toLocaleString()}</strong>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-percent text-success me-1"></i>Tasa de llenado
                                </span>
                                <strong class="text-success">${Math.round(item.tasaLlenado * 100)}%</strong>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-graph-up text-primary me-1"></i>Score
                                </span>
                                <strong class="text-primary">${item.score} / 100</strong>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-tag text-muted me-1"></i>Área
                                </span>
                                <span class="rp-badge rp-badge--neutral" style="font-size:.68rem;">${item.area}</span>
                            </div>
                            <div class="rp-cmr-back-row">
                                <span class="rp-cmr-back-label">
                                    <i class="bi bi-circle text-muted me-1"></i>Estatus
                                </span>
                                <span class="rp-badge rp-badge--neutral" style="font-size:.68rem;">${item.estatus}</span>
                            </div>
                            <div class="mt-auto pt-2">
                                <button class="btn btn-primary btn-sm w-100" disabled>
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver en Cursos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        // Contenedor de fórmula plegable debajo de los 3 cards
        const info = `
        <div class="col-12 mt-3">
            <div class="rp-cmr-formula-box">
                <!-- Cabecera siempre visible (toggle) -->
                <button class="rp-cmr-formula-toggle" type="button"
                        onclick="
                            const b=this.closest('.rp-cmr-formula-box').querySelector('.rp-cmr-formula-body');
                            const ico=this.querySelector('.rp-cmr-formula-chevron');
                            const open=b.style.display!=='none';
                            b.style.display=open?'none':'block';
                            ico.style.transform=open?'':'rotate(180deg)';
                        ">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill rp-cmr-formula-icon"></i>
                        <strong class="rp-cmr-formula-title">¿Cómo se determina la prioridad?</strong>
                    </span>
                    <i class="bi bi-chevron-down rp-cmr-formula-chevron"></i>
                </button>
                <!-- Cuerpo plegable (oculto por defecto) -->
                <div class="rp-cmr-formula-body" style="display:none;">
                    <p class="rp-cmr-formula-text mb-2 mt-3">
                        El <strong>Índice de Requerimiento</strong> combina tres factores para identificar
                        los cursos con mayor demanda real. Se le da especial importancia a la
                        <strong>tasa de llenado</strong>: un curso que se matriculó completo en cada edición
                        recibe el máximo aporte en ese componente, garantizando que quede en Alta Prioridad.
                    </p>
                    <div class="rp-cmr-formula-code">
                        Score = (Inscripciones / Máx) × 30
                              + (Participantes únicos / Máx) × 40
                              + (Participantes / Inscripciones) × 30
                    </div>
                    <p class="rp-cmr-formula-text mt-2 mb-2">
                        Los umbrales de prioridad se calculan sobre el conjunto de cursos del año/área activos
                        usando percentiles: el 25 % superior es <strong>Alta</strong>, el rango intermedio es
                        <strong>Media</strong> y el resto es <strong>Baja</strong>.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="badge bg-success">Alta — Score ≥ P75 (≥ ${umbrales.alta})</span>
                        <span class="badge bg-warning text-dark">Media — Score ≥ P40 (≥ ${umbrales.media})</span>
                        <span class="badge bg-info">Baja — Score &lt; P40 (&lt; ${umbrales.media})</span>
                    </div>
                </div>
            </div>
        </div>`;

        container.innerHTML = tarjetas + info;
    }

    // ── Sección 2: Estadísticas ───────────────────────────────────────
    async function cargarStats(anio) {
        setLoading('Stats', true);
        clearError('Stats');
        document.getElementById('contenidoStats').style.display = 'none';
        try {
            const datos = await fetchCursosAnio(anio);
            renderStats(datos);
            setLoading('Stats', false);
            document.getElementById('contenidoStats').style.display = 'block';
        } catch (err) {
            setLoading('Stats', false);
            setError('Stats', err.message);
            console.error('[RE-Curso-MasRequerido] Stats error:', err);
        }
    }

    function renderStats(data) {
        const grid = document.getElementById('statsGrid');
        if (!grid) return;

        const n         = data.length;
        const totInscr  = data.reduce((s, c) => s + c.inscripciones, 0);
        const avgPartic = n > 0 ? Math.round(data.reduce((s, c) => s + c.participantes, 0) / n) : 0;
        const avgScore  = n > 0 ? Math.round(data.reduce((s, c) => s + c.score, 0) / n) : 0;

        const items = [
            { val: n,                          label: 'Cursos Analizados',   sub: 'En el año seleccionado',   mod: 'primary', icon: 'bi-search' },
            { val: avgScore,                   label: 'Puntuación Promedio', sub: 'Índice de requerimiento',  mod: 'success', icon: 'bi-star-fill' },
            { val: totInscr.toLocaleString(),  label: 'Total Inscripciones', sub: 'Suma de todos los cursos', mod: 'warning', icon: 'bi-journal-bookmark-fill' },
            { val: avgPartic.toLocaleString(), label: 'Participantes Prom.', sub: 'Únicos por curso',          mod: 'cyan',    icon: 'bi-people-fill' }
        ];

        grid.innerHTML = items.map(it => `
            <div class="rp-kpi-card rp-kpi-card--${it.mod}">
                <div class="rp-kpi-header">
                    <div>
                        <div class="rp-kpi-value">${it.val}</div>
                        <div class="rp-kpi-label">${it.label}</div>
                        <div class="rp-kpi-sub">${it.sub}</div>
                    </div>
                    <div class="rp-kpi-icon bg-${it.mod === 'cyan' ? 'info' : it.mod} bg-opacity-10
                                text-${it.mod === 'cyan' ? 'info' : it.mod}">
                        <i class="bi ${it.icon}"></i>
                    </div>
                </div>
            </div>`).join('');
    }

    // ── Sección 3: Gráfica ────────────────────────────────────────────
    async function cargarGrafica(anio) {
        setLoading('Grafica', true);
        clearError('Grafica');
        try {
            const datos = await fetchCursosAnio(anio);
            crearGrafica(datos);
            setLoading('Grafica', false);
        } catch (err) {
            setLoading('Grafica', false);
            setError('Grafica', err.message);
            console.error('[RE-Curso-MasRequerido] Gráfica error:', err);
        }
    }

    function crearGrafica(cursosData) {
        const ctx = document.getElementById('graficaCursoMasRequerido')?.getContext('2d');
        if (!ctx) return;
        if (cursoRequeridoChart) { cursoRequeridoChart.destroy(); cursoRequeridoChart = null; }

        // Agrupar por área formativa
        const porArea = {};
        cursosData.forEach(c => {
            if (!porArea[c.area]) porArea[c.area] = { scoreSum: 0, count: 0 };
            porArea[c.area].scoreSum += c.score;
            porArea[c.area].count   += 1;
        });

        const labels    = Object.keys(porArea);
        const promedios = labels.map(a => Math.round(porArea[a].scoreSum / porArea[a].count));

        const palette = [
            'rgba(54,162,235,.75)', 'rgba(255,99,132,.75)', 'rgba(255,159,64,.75)',
            'rgba(75,192,192,.75)', 'rgba(153,102,255,.75)', 'rgba(255,205,86,.75)'
        ];

        cursoRequeridoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Índice de Requerimiento Promedio',
                    data:  promedios,
                    backgroundColor: palette.slice(0, labels.length),
                    borderColor:     palette.map(c => c.replace('.75', '1')).slice(0, labels.length),
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear', position: 'left', min: 0, max: 100,
                        title: { display: true, text: 'Índice de Requerimiento' },
                        grid:  { color: 'rgba(0,0,0,0.07)' }
                    },
                    x: { grid: { color: 'rgba(0,0,0,0.04)' } }
                },
                plugins: {
                    legend:  { display: false },
                    tooltip: { callbacks: { label: c => `Índice promedio: ${c.raw}` } }
                }
            }
        });
    }

    // ── Sección 4: Tabla Ranking ──────────────────────────────────────
    async function cargarTabla(anio) {
        setLoading('Tabla', true);
        clearError('Tabla');
        try {
            const datos = await fetchCursosAnio(anio);
            datosCompletosTabla = datos;
            poblarSelectAreas('selectAreaTabla', datos);
            const inp = document.getElementById('searchRankingTable');
            if (inp) inp.value = '';
            paginaActual = 1;
            ordenActual  = { columna: 'score', direccion: 'desc' };
            // Resetear icono del header de score
            document.querySelectorAll('.sortable-header-rq i')
                .forEach(i => i.className = 'bi bi-arrow-down-up');
            const scoreHeader = document.querySelector('.sortable-header-rq[data-columna="score"] i');
            if (scoreHeader) scoreHeader.className = 'bi bi-arrow-down';
            const ordenados = ordenarDatos(datos, 'score', 'desc');
            actualizarTabla(ordenados);
            configurarPaginacion(ordenados.length);
            setLoading('Tabla', false);
        } catch (err) {
            setLoading('Tabla', false);
            setError('Tabla', err.message);
            console.error('[RE-Curso-MasRequerido] Tabla error:', err);
        }
    }

    function actualizarTabla(datosOrdenados) {
        const tbody     = document.getElementById('tablaRankingBodyRequerido');
        const spanIni   = document.getElementById('inicioPagina');
        const spanFin   = document.getElementById('finPagina');
        const spanTotal = document.getElementById('totalPaginacion');
        if (!tbody) return;

        const inicio = (paginaActual - 1) * CURSOS_POR_PAGINA;
        const fin    = Math.min(inicio + CURSOS_POR_PAGINA, datosOrdenados.length);

        if (spanIni)   spanIni.textContent   = datosOrdenados.length === 0 ? 0 : inicio + 1;
        if (spanFin)   spanFin.textContent   = fin;
        if (spanTotal) spanTotal.textContent = datosOrdenados.length;

        if (!datosOrdenados.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        No se encontraron cursos con los filtros aplicados.
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = datosOrdenados.slice(inicio, fin).map((item, idx) => {
            const gi = inicio + idx;
            const badgeColor = gi === 0 ? 'bg-warning text-dark'
                             : gi === 1 ? 'bg-secondary'
                             : gi === 2 ? 'bg-danger'
                             : 'bg-light text-dark border';
            const barColor   = item.score > 75 ? 'bg-success'
                             : item.score > 50 ? 'bg-warning'
                             : 'bg-info';
            const [accionBadge, accionTxt] =
                item.score > 75 ? ['bg-success',           'Reabrir pronto'] :
                item.score > 50 ? ['bg-warning text-dark', 'Considerar']      :
                                  ['bg-secondary',         'Mantener'];

            return `
            <tr ${gi < 3 ? 'class="table-warning"' : ''}>
                <td class="text-center">
                    <span class="badge ${badgeColor}">${gi + 1}</span>
                </td>
                <td><strong>${item.curso}</strong></td>
                <td class="text-center rp-cell-num">${item.inscripciones.toLocaleString()}</td>
                <td class="text-center rp-cell-num">${item.participantes.toLocaleString()}</td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="progress flex-grow-1"
                             style="height:18px; min-width:70px; max-width:120px;">
                            <div class="progress-bar ${barColor}"
                                 style="width:${item.score}%">${item.score}</div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="rp-badge rp-badge--neutral">${item.area}</span>
                </td>
                <td class="text-center">
                    <span class="rp-badge rp-badge--neutral">${item.estatus}</span>
                </td>
                <td class="text-center">
                    <span class="badge ${accionBadge}">${accionTxt}</span>
                </td>
            </tr>`;
        }).join('');
    }

    function configurarPaginacion(total) {
        const el = document.getElementById('paginacionCursos');
        if (!el) return;
        const totalPag = Math.ceil(total / CURSOS_POR_PAGINA);
        if (!totalPag) { el.innerHTML = ''; return; }

        let html = `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual - 1}">Anterior</a></li>`;

        const maxV = 5;
        let ini = Math.max(1, paginaActual - Math.floor(maxV / 2));
        let fin = Math.min(totalPag, ini + maxV - 1);
        if (fin - ini + 1 < maxV) ini = Math.max(1, fin - maxV + 1);

        if (ini > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="1">1</a></li>`;
            if (ini > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let p = ini; p <= fin; p++) {
            html += `<li class="page-item ${p === paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" data-pagina="${p}">${p}</a></li>`;
        }
        if (fin < totalPag) {
            if (fin < totalPag - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${totalPag}">${totalPag}</a></li>`;
        }
        html += `<li class="page-item ${paginaActual === totalPag ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente</a></li>`;

        el.innerHTML = html;
        el.querySelectorAll('.page-link[data-pagina]').forEach(btn =>
            btn.addEventListener('click', e => {
                e.preventDefault();
                const np = parseInt(btn.dataset.pagina);
                if (isNaN(np) || np < 1 || np > totalPag || np === paginaActual) return;
                irAPagina(np);
            })
        );
    }

    function configurarOrdenamiento() {
        document.querySelectorAll('.sortable-header-rq').forEach(header => {
            header.addEventListener('click', e => {
                e.preventDefault();
                const col = header.getAttribute('data-columna');

                if (ordenActual.columna === col) {
                    ordenActual.direccion = ordenActual.direccion === 'asc' ? 'desc' : 'asc';
                } else {
                    ordenActual.columna   = col;
                    ordenActual.direccion = 'asc';
                }

                // Actualizar iconos
                document.querySelectorAll('.sortable-header-rq i')
                    .forEach(i => i.className = 'bi bi-arrow-down-up');
                const ico = header.querySelector('i');
                if (ico) ico.className = `bi bi-arrow-${ordenActual.direccion === 'asc' ? 'up' : 'down'}`;

                aplicarFiltrosTabla();
            });
        });
    }

    // ── Limpieza ──────────────────────────────────────────────────────
    function limpiarCursoMasRequerido() {
        if (cursoRequeridoChart) { cursoRequeridoChart.destroy(); cursoRequeridoChart = null; }
        paginaActual        = 1;
        ordenActual         = { columna: 'score', direccion: 'desc' };
        datosCompletosTop3  = [];
        datosCompletosTabla = [];
        // Limpiar caché para forzar re-fetch en la siguiente carga
        Object.keys(cacheAnios).forEach(k => delete cacheAnios[k]);
    }

    // ── Registro global ───────────────────────────────────────────────
    window.cargarCursoMasRequerido  = cargarCursoMasRequerido;
    window.limpiarCursoMasRequerido = limpiarCursoMasRequerido;
    console.log('✅ RE-Curso-MasRequerido.js cargado — estilos migrados a reporte.css');

})();
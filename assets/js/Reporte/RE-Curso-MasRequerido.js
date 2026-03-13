// ========== REPORTE INDEPENDIENTE: CURSO MÁS REQUERIDO ==========
// Archivo: RE-Curso-MasRequerido.js
// Función: cargarCursoMasRequerido (ID: 5)
// Conectado a: ../controlador/Reporte/api_reportes.php

(function () {

    const API_URL = '../controlador/Reporte/api_reportes.php';

    // ── Estado del reporte ────────────────────────────────────────────
    let cursoRequeridoChart = null;
    let paginaActual        = 1;
    let ordenActual         = { columna: 'score', direccion: 'desc' };
    const CURSOS_POR_PAGINA = 10;
    let datosCompletos      = [];

    // ── Función principal ─────────────────────────────────────────────
    function cargarCursoMasRequerido() {
        const contentDiv = document.getElementById('reportContent');
        const currentCategoryId   = 1;
        const categoriasReportes  = [{ id: 1, nombre: 'Cursos', color: 'primary', icono: 'bi-trophy-fill' }];
        const categoria           = categoriasReportes.find(c => c.id === currentCategoryId);
        const anioActual          = new Date().getFullYear();
        const aniosOpciones       = Array.from({ length: 5 }, (_, i) => anioActual - i)
            .map(a => `<option value="${a}" ${a === anioActual ? 'selected' : ''}>${a}</option>`)
            .join('');

        contentDiv.innerHTML = `
            <div class="card shadow-lg border-0">
                <div class="card-header bg-${categoria.color} text-white">
                    <h4 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>Curso más Requerido</h4>
                    <p class="mb-0 mt-2 small opacity-75" id="subtituloHeaderRequerido">
                        Análisis para determinar qué cursos reabrir
                    </p>
                </div>
                <div class="card-body">

                    <div class="text-center py-5" id="loadingCursoMasRequerido">
                        <div class="spinner-border text-${categoria.color}" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3" id="loadingMsgRequerido">Cargando datos de requerimiento de cursos...</p>
                    </div>

                    <div id="errorCursoMasRequerido" class="alert alert-danger" style="display:none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span id="errorMsgRequerido">Error al cargar los datos.</span>
                    </div>

                    <div id="contenidoCursoMasRequerido" style="display:none;">

                        <!-- Controles de filtro -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="selectAnioRequerido" class="form-label">Año de Análisis:</label>
                                                        <select id="selectAnioRequerido" class="form-select">
                                                            ${aniosOpciones}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="selectAreaRequerido" class="form-label">Área Formativa:</label>
                                                        <select id="selectAreaRequerido" class="form-select">
                                                            <option value="todas" selected>Todas las áreas</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <button id="btnActualizarReporteRequerido" class="btn btn-primary mt-4">
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Actualizar Análisis
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top 3 -->
                        <div id="contenedorTop3CursosRequeridos" class="text-center py-4">
                            <h5 class="text-muted mb-4" id="tituloTop3Requerido">TOP 3 CURSOS MÁS REQUERIDOS</h5>
                            <div class="row justify-content-center align-items-stretch">

                                <!-- 1er lugar -->
                                <div class="col-md-4 mb-4">
                                    <div class="card border-0 shadow h-100">
                                        <div class="card-body text-center py-4 d-flex flex-column">
                                            <div class="mb-3">
                                                <i class="bi bi-trophy-fill text-warning" style="font-size:3rem;"></i>
                                            </div>
                                            <h4 id="cursoPrimeroRequerido" class="curso-nombre-requerido flex-grow-1">—</h4>
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-center mb-2">
                                                    <span class="badge bg-info me-2">
                                                        <i class="bi bi-people-fill me-1"></i>
                                                        <span id="participantesPrimeroRequerido">0</span> inscritos
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-journal-bookmark me-1"></i>
                                                        <span id="inscripcionesPrimeroRequerido">0</span> inscripciones
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="fs-5 fw-bold text-primary" id="scorePrimeroRequerido">0</div>
                                                    <small class="text-muted">Índice de requerimiento</small>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-arrow-up me-1"></i>ALTA PRIORIDAD
                                                    </span>
                                                </div>
                                                <div class="mt-3">
                                                    <small class="text-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        <strong>Reabrir inmediatamente</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2do lugar -->
                                <div class="col-md-4 mb-4">
                                    <div class="card border-0 shadow h-100">
                                        <div class="card-body text-center py-4 d-flex flex-column">
                                            <div class="mb-3">
                                                <i class="bi bi-award-fill text-secondary" style="font-size:3rem;"></i>
                                            </div>
                                            <h4 id="cursoSegundoRequerido" class="curso-nombre-requerido flex-grow-1">—</h4>
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-center mb-2">
                                                    <span class="badge bg-info me-2">
                                                        <i class="bi bi-people-fill me-1"></i>
                                                        <span id="participantesSegundoRequerido">0</span> inscritos
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-journal-bookmark me-1"></i>
                                                        <span id="inscripcionesSegundoRequerido">0</span> inscripciones
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="fs-5 fw-bold text-primary" id="scoreSegundoRequerido">0</div>
                                                    <small class="text-muted">Índice de requerimiento</small>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-arrow-up me-1"></i>MEDIA PRIORIDAD
                                                    </span>
                                                </div>
                                                <div class="mt-3">
                                                    <small class="text-warning">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <strong>Programar pronto</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3er lugar -->
                                <div class="col-md-4 mb-4">
                                    <div class="card border-0 shadow h-100">
                                        <div class="card-body text-center py-4 d-flex flex-column">
                                            <div class="mb-3">
                                                <i class="bi bi-award text-danger" style="font-size:3rem;"></i>
                                            </div>
                                            <h4 id="cursoTerceroRequerido" class="curso-nombre-requerido flex-grow-1">—</h4>
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-center mb-2">
                                                    <span class="badge bg-info me-2">
                                                        <i class="bi bi-people-fill me-1"></i>
                                                        <span id="participantesTerceroRequerido">0</span> inscritos
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-journal-bookmark me-1"></i>
                                                        <span id="inscripcionesTerceroRequerido">0</span> inscripciones
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="fs-5 fw-bold text-primary" id="scoreTerceroRequerido">0</div>
                                                    <small class="text-muted">Índice de requerimiento</small>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-arrow-up me-1"></i>BAJA PRIORIDAD
                                                    </span>
                                                </div>
                                                <div class="mt-3">
                                                    <small class="text-info">
                                                        <i class="bi bi-calendar me-1"></i>
                                                        <strong>Considerar para futuro</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">ESTADÍSTICAS DE REQUERIMIENTO</h5>
                                        <div class="row text-center mt-4">
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3">
                                                    <h2 class="text-primary" id="totalAnalizadoRequerido">0</h2>
                                                    <p class="text-muted mb-0">Cursos Analizados</p>
                                                    <small class="text-muted">En el año seleccionado</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3">
                                                    <h2 class="text-success" id="promedioScoreRequerido">0</h2>
                                                    <p class="text-muted mb-0">Puntuación Promedio</p>
                                                    <small class="text-muted">Índice de requerimiento</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3">
                                                    <h2 class="text-warning" id="totalInscripcionesRequerido">0</h2>
                                                    <p class="text-muted mb-0">Total Inscripciones</p>
                                                    <small class="text-muted">Suma de todos los cursos</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3">
                                                    <h2 class="text-info" id="participantesPromedioRequerido">0</h2>
                                                    <p class="text-muted mb-0">Participantes Promedio</p>
                                                    <small class="text-muted">Únicos por curso</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfica por área -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">ÍNDICE DE REQUERIMIENTO POR ÁREA FORMATIVA</h5>
                                        <div style="height:300px; width:100%;">
                                            <canvas id="graficaCursoMasRequerido"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla ranking -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title mb-0">RANKING COMPLETO DE CURSOS REQUERIDOS</h5>
                                            <div class="search-container" style="width:300px;">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-search text-secondary"></i>
                                                    </span>
                                                    <input type="text" id="searchRankingTable"
                                                           class="form-control border-start-0"
                                                           placeholder="Buscar curso, área, estatus...">
                                                    <button id="clearSearchBtn" class="btn btn-outline-secondary" type="button">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-striped table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="60">#</th>
                                                        <th>
                                                            <a href="#" class="sortable-header" data-columna="curso">
                                                                Curso <i class="bi bi-arrow-down-up ms-1"></i>
                                                            </a>
                                                        </th>
                                                        <th width="130" class="text-center">
                                                            <a href="#" class="sortable-header" data-columna="inscripciones">
                                                                Inscripciones <i class="bi bi-arrow-down-up ms-1"></i>
                                                            </a>
                                                        </th>
                                                        <th width="130" class="text-center">
                                                            <a href="#" class="sortable-header" data-columna="participantes">
                                                                Participantes únicos <i class="bi bi-arrow-down-up ms-1"></i>
                                                            </a>
                                                        </th>
                                                        <th width="160" class="text-center">
                                                            <a href="#" class="sortable-header" data-columna="score">
                                                                Índice Requerimiento <i class="bi bi-arrow-down ms-1"></i>
                                                            </a>
                                                        </th>
                                                        <th width="120" class="text-center">Área</th>
                                                        <th width="130" class="text-center">Estatus</th>
                                                        <th width="140" class="text-center">Acción Sugerida</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tablaRankingBodyRequerido"></tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div id="infoPaginacion" class="text-muted">
                                                Mostrando <span id="inicioPagina">1</span>–<span id="finPagina">10</span>
                                                de <span id="totalPaginacion">0</span> cursos
                                            </div>
                                            <nav>
                                                <ul class="pagination pagination-sm mb-0" id="paginacionCursos"></ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /contenidoCursoMasRequerido -->
                </div>
            </div>

            <style>
                .curso-nombre-requerido {
                    font-weight:600; height:3em;
                    display:-webkit-box; -webkit-line-clamp:2;
                    -webkit-box-orient:vertical; overflow:hidden; margin-bottom:1rem;
                }
                .sortable-header { text-decoration:none; color:white;
                    display:flex; align-items:center; justify-content:center; }
                .sortable-header:hover { color:#86b7fe; }
                .sortable-header i { font-size:.8rem; opacity:.7; }
                #tablaRankingRequerido .table th,
                #tablaRankingRequerido .table td { padding:.75rem; vertical-align:middle; }
                @media (max-width:768px) {
                    .search-container { width:100%!important; margin-top:1rem; }
                }
            </style>
        `;

        cargarDatosCursoMasRequerido();
    }

    // ── Fetch principal ───────────────────────────────────────────────
    async function cargarDatosCursoMasRequerido() {
        const loadingEl  = document.getElementById('loadingCursoMasRequerido');
        const errorEl    = document.getElementById('errorCursoMasRequerido');
        const contenidoEl = document.getElementById('contenidoCursoMasRequerido');

        if (!loadingEl) return;

        // Resetear estados
        contenidoEl.style.display = 'none';
        errorEl.style.display     = 'none';
        loadingEl.style.display   = 'block';

        const anio = parseInt(document.getElementById('selectAnioRequerido')?.value || new Date().getFullYear());
        document.getElementById('loadingMsgRequerido').textContent =
            `Analizando datos de cursos del año ${anio}...`;

        try {
            const res = await fetch(`${API_URL}?accion=curso_mas_requerido&anio=${anio}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);

            const datos = await res.json();
            if (datos.error) throw new Error(datos.error);
            if (!Array.isArray(datos) || datos.length === 0) {
                throw new Error(`No se encontraron datos de cursos para el año ${anio}.`);
            }

            // Normalizar y calcular scores
            const cursosData = normalizarYCalcularScores(datos);

            // Filtrar por área si se seleccionó
            poblarSelectAreas(cursosData);
            const areaSelec = document.getElementById('selectAreaRequerido')?.value || 'todas';
            const datosFiltrados = areaSelec === 'todas'
                ? cursosData
                : cursosData.filter(c => c.area === areaSelec);

            // Actualizar subtítulo
            document.getElementById('subtituloHeaderRequerido').textContent =
                `Reporte del año ${anio} — Análisis para determinar qué cursos reabrir`;
            document.getElementById('tituloTop3Requerido').textContent =
                `TOP 3 CURSOS MÁS REQUERIDOS (${anio})`;

            mostrarCursoMasRequerido(datosFiltrados);

            loadingEl.style.display   = 'none';
            contenidoEl.style.display = 'block';

        } catch (err) {
            loadingEl.style.display  = 'none';
            errorEl.style.display    = 'block';
            document.getElementById('errorMsgRequerido').textContent =
                `Error al cargar los datos: ${err.message}`;
            console.error('[RE-Curso-MasRequerido] Error al cargar datos:', err);
        }
    }

    // ── Normalización y cálculo de score ─────────────────────────────
    function normalizarYCalcularScores(datos) {
        const cursos = datos.map(d => ({
            curso:          d.nombre_curso || '—',
            inscripciones:  parseInt(d.total_inscripciones) || 0,
            participantes:  parseInt(d.participantes_unicos) || 0,
            estatus:        d.estatus || '—',
            area:           d.area    || 'Sin área',
            score:          0
        }));

        const maxInscr  = Math.max(...cursos.map(c => c.inscripciones), 1);
        const maxPartic = Math.max(...cursos.map(c => c.participantes), 1);

        cursos.forEach(c => {
            const normInscr  = (c.inscripciones / maxInscr)  * 100;
            const normPartic = (c.participantes / maxPartic) * 100;
            c.score = Math.round((normInscr * 0.6) + (normPartic * 0.4));
        });

        // Ordenar por score desc
        cursos.sort((a, b) => b.score - a.score);
        return cursos;
    }

    // ── Poblar selector de áreas (una sola vez) ───────────────────────
    let areasYaPobladas = false;
    function poblarSelectAreas(cursosData) {
        if (areasYaPobladas) return;
        const sel = document.getElementById('selectAreaRequerido');
        if (!sel) return;
        const areas = [...new Set(cursosData.map(c => c.area))].sort();
        areas.forEach(a => {
            const opt = document.createElement('option');
            opt.value       = a;
            opt.textContent = a;
            sel.appendChild(opt);
        });
        areasYaPobladas = true;
    }

    // ── Renderizado principal ─────────────────────────────────────────
    function mostrarCursoMasRequerido(data) {
        // Top 3
        const top = data.slice(0, 3);
        const ids = ['Primero', 'Segundo', 'Tercero'];

        ids.forEach((id, i) => {
            const item = top[i] || { curso: '—', participantes: 0, inscripciones: 0, score: 0 };
            document.getElementById(`curso${id}Requerido`).textContent         = item.curso;
            document.getElementById(`participantes${id}Requerido`).textContent  = item.participantes.toLocaleString();
            document.getElementById(`inscripciones${id}Requerido`).textContent  = item.inscripciones.toLocaleString();
            document.getElementById(`score${id}Requerido`).textContent          = item.score;
        });

        // Estadísticas
        const totalCursos       = data.length;
        const totalInscripciones = data.reduce((s, c) => s + c.inscripciones, 0);
        const promedioPartic    = totalCursos > 0 ? Math.round(data.reduce((s, c) => s + c.participantes, 0) / totalCursos) : 0;
        const promedioScore     = totalCursos > 0 ? Math.round(data.reduce((s, c) => s + c.score, 0) / totalCursos) : 0;

        document.getElementById('totalAnalizadoRequerido').textContent      = totalCursos.toLocaleString();
        document.getElementById('promedioScoreRequerido').textContent        = promedioScore;
        document.getElementById('totalInscripcionesRequerido').textContent   = totalInscripciones.toLocaleString();
        document.getElementById('participantesPromedioRequerido').textContent = promedioPartic.toLocaleString();

        // Gráfica
        crearGraficaCursoMasRequerido(data);

        // Tabla paginada
        inicializarTablaPaginada(data);
        configurarEventListeners();
    }

    // ── Tabla paginada ────────────────────────────────────────────────
    function inicializarTablaPaginada(datos) {
        paginaActual = 1;
        ordenActual  = { columna: 'score', direccion: 'desc' };
        datosCompletos = [...datos];
        const ordenados = ordenarDatos(datos, ordenActual.columna, ordenActual.direccion);
        actualizarTablaPaginada(ordenados);
        configurarPaginacion(ordenados.length);
        configurarOrdenamiento();
    }

    function ordenarDatos(datos, columna, direccion) {
        return [...datos].sort((a, b) => {
            if (columna === 'curso') {
                const r = a.curso.toLowerCase().localeCompare(b.curso.toLowerCase());
                return direccion === 'asc' ? r : -r;
            }
            const map = { inscripciones: 'inscripciones', participantes: 'participantes', score: 'score' };
            const campo = map[columna] || 'score';
            return direccion === 'asc' ? a[campo] - b[campo] : b[campo] - a[campo];
        });
    }

    function actualizarTablaPaginada(datosOrdenados) {
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

        if (datosOrdenados.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>No se encontraron cursos con los filtros aplicados.
            </td></tr>`;
            return;
        }

        tbody.innerHTML = datosOrdenados.slice(inicio, fin).map((item, idx) => {
            const globalIdx  = inicio + idx;
            const badgeColor = globalIdx === 0 ? 'bg-warning text-dark' : globalIdx === 1 ? 'bg-secondary' : globalIdx === 2 ? 'bg-danger' : 'bg-info';
            const barColor   = item.score > 75 ? 'bg-success' : item.score > 50 ? 'bg-warning' : 'bg-info';
            const accion     = item.score > 75 ? ['bg-success', 'Reabrir pronto'] : item.score > 50 ? ['bg-warning', 'Considerar'] : ['bg-secondary', 'Mantener'];

            return `<tr ${globalIdx < 3 ? 'class="table-warning"' : ''}>
                <td class="text-center">
                    <span class="badge ${badgeColor}">${globalIdx + 1}</span>
                </td>
                <td><strong>${item.curso}</strong></td>
                <td class="text-center">
                    <i class="bi bi-journal-bookmark text-secondary me-1"></i>${item.inscripciones.toLocaleString()}
                </td>
                <td class="text-center">
                    <i class="bi bi-people-fill text-primary me-1"></i>${item.participantes.toLocaleString()}
                </td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="progress flex-grow-1" style="height:20px; min-width:80px; max-width:130px;">
                            <div class="progress-bar ${barColor}" style="width:${item.score}%" title="${item.score}">
                                ${item.score}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">${item.area}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">${item.estatus}</span>
                </td>
                <td class="text-center">
                    <span class="badge ${accion[0]}">${accion[1]}</span>
                </td>
            </tr>`;
        }).join('');
    }

    function configurarPaginacion(totalCursos) {
        const paginacionEl = document.getElementById('paginacionCursos');
        if (!paginacionEl) return;
        const totalPaginas = Math.ceil(totalCursos / CURSOS_POR_PAGINA);

        let html = `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual - 1}">Anterior</a></li>`;

        const maxBtn = 5;
        let ini = Math.max(1, paginaActual - Math.floor(maxBtn / 2));
        let fin = Math.min(totalPaginas, ini + maxBtn - 1);
        if (fin - ini + 1 < maxBtn) ini = Math.max(1, fin - maxBtn + 1);

        if (ini > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="1">1</a></li>`;
            if (ini > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let p = ini; p <= fin; p++) {
            html += `<li class="page-item ${p === paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" data-pagina="${p}">${p}</a></li>`;
        }
        if (fin < totalPaginas) {
            if (fin < totalPaginas - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${totalPaginas}">${totalPaginas}</a></li>`;
        }
        html += `<li class="page-item ${paginaActual === totalPaginas || totalPaginas === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente</a></li>`;

        paginacionEl.innerHTML = html;
        paginacionEl.querySelectorAll('.page-link[data-pagina]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const nueva = parseInt(btn.getAttribute('data-pagina'));
                if (isNaN(nueva) || nueva < 1 || nueva > totalPaginas || nueva === paginaActual) return;
                paginaActual = nueva;
                const ordenados = ordenarDatos(datosCompletos, ordenActual.columna, ordenActual.direccion);
                actualizarTablaPaginada(ordenados);
                configurarPaginacion(ordenados.length);
            });
        });
    }

    function configurarOrdenamiento() {
        document.querySelectorAll('.sortable-header').forEach(el => {
            el.addEventListener('click', e => {
                e.preventDefault();
                const col = el.getAttribute('data-columna');
                if (ordenActual.columna === col) {
                    ordenActual.direccion = ordenActual.direccion === 'asc' ? 'desc' : 'asc';
                } else {
                    ordenActual.columna   = col;
                    ordenActual.direccion = 'asc';
                }
                document.querySelectorAll('.sortable-header i').forEach(i => i.className = 'bi bi-arrow-down-up ms-1');
                const ico = el.querySelector('i');
                if (ico) ico.className = `bi bi-arrow-${ordenActual.direccion === 'asc' ? 'up' : 'down'} ms-1`;
                paginaActual = 1;
                const ordenados = ordenarDatos(datosCompletos, ordenActual.columna, ordenActual.direccion);
                actualizarTablaPaginada(ordenados);
                configurarPaginacion(ordenados.length);
            });
        });
    }

    function configurarBusqueda() {
        const input    = document.getElementById('searchRankingTable');
        const clearBtn = document.getElementById('clearSearchBtn');
        if (input) {
            input.addEventListener('input', () => filtrarPorBusqueda(input.value));
        }
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (input) { input.value = ''; filtrarPorBusqueda(''); input.focus(); }
            });
        }
    }

    function filtrarPorBusqueda(term) {
        if (!term.trim()) {
            const ordenados = ordenarDatos(datosCompletos, ordenActual.columna, ordenActual.direccion);
            paginaActual = 1;
            actualizarTablaPaginada(ordenados);
            configurarPaginacion(ordenados.length);
            return;
        }
        const t = term.toLowerCase();
        const filtrados = datosCompletos.filter(c =>
            c.curso.toLowerCase().includes(t) ||
            c.area.toLowerCase().includes(t)  ||
            c.estatus.toLowerCase().includes(t)
        );
        const ordenados = ordenarDatos(filtrados, ordenActual.columna, ordenActual.direccion);
        paginaActual = 1;
        actualizarTablaPaginada(ordenados);
        configurarPaginacion(ordenados.length);
    }

    // ── Gráfica por área ──────────────────────────────────────────────
    function crearGraficaCursoMasRequerido(cursosData) {
        const ctx = document.getElementById('graficaCursoMasRequerido')?.getContext('2d');
        if (!ctx) return;
        if (cursoRequeridoChart) { cursoRequeridoChart.destroy(); cursoRequeridoChart = null; }

        // Agrupar por área
        const porArea = {};
        cursosData.forEach(c => {
            if (!porArea[c.area]) porArea[c.area] = { scoreSum: 0, count: 0, inscripciones: 0 };
            porArea[c.area].scoreSum    += c.score;
            porArea[c.area].count       += 1;
            porArea[c.area].inscripciones += c.inscripciones;
        });

        const labels    = Object.keys(porArea);
        const promedios = labels.map(a => Math.round(porArea[a].scoreSum / porArea[a].count));
        const totInscr  = labels.map(a => porArea[a].inscripciones);

        const palette = [
            'rgba(54,162,235,.75)', 'rgba(255,99,132,.75)', 'rgba(255,159,64,.75)',
            'rgba(75,192,192,.75)', 'rgba(153,102,255,.75)', 'rgba(255,205,86,.75)'
        ];

        cursoRequeridoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Índice de Requerimiento Promedio',
                        data:  promedios,
                        backgroundColor: palette.slice(0, labels.length),
                        borderColor:     palette.map(c => c.replace('.75', '1')).slice(0, labels.length),
                        borderWidth: 2, borderRadius: 5, yAxisID: 'y'
                    },
                    {
                        label: 'Total Inscripciones',
                        data:  totInscr,
                        backgroundColor: 'rgba(201,203,207,.5)',
                        borderColor: 'rgba(201,203,207,1)',
                        borderWidth: 1, borderRadius: 3,
                        type: 'line', tension: 0.4, yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y:  { type:'linear', position:'left',  title:{ display:true, text:'Índice de Requerimiento' }, min:0, max:100 },
                    y1: { type:'linear', position:'right', title:{ display:true, text:'Inscripciones' }, min:0, grid:{ drawOnChartArea:false } }
                },
                plugins: {
                    legend: { position:'top' },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.raw}` } }
                }
            }
        });
    }

    // ── Event listeners ───────────────────────────────────────────────
    function configurarEventListeners() {
        document.getElementById('btnActualizarReporteRequerido')?.addEventListener('click', () => {
            areasYaPobladas = false; // Permitir repoblar si cambia el año
            // Limpiar área al actualizar
            const sel = document.getElementById('selectAreaRequerido');
            if (sel) { sel.innerHTML = '<option value="todas" selected>Todas las áreas</option>'; }
            cargarDatosCursoMasRequerido();
        });
        configurarBusqueda();
    }

    // ── Limpieza ──────────────────────────────────────────────────────
    function limpiarCursoMasRequerido() {
        if (cursoRequeridoChart) { cursoRequeridoChart.destroy(); cursoRequeridoChart = null; }
        paginaActual     = 1;
        ordenActual      = { columna: 'score', direccion: 'desc' };
        datosCompletos   = [];
        areasYaPobladas  = false;
    }

    // ── Registro global ───────────────────────────────────────────────
    window.cargarCursoMasRequerido  = cargarCursoMasRequerido;
    window.limpiarCursoMasRequerido = limpiarCursoMasRequerido;
    console.log('✅ RE-Curso-MasRequerido.js cargado — conectado a API real');

})();
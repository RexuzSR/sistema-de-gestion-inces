// ========== REPORTES DE CURSOS ==========

// Variables globales para control del slideshow
let slideshowInterval;
let currentSlideIndex = 0;
const slideshowDelay = 5000; // 5 segundos

// 1. Total de cursos - MODIFICADA CON SISTEMA DE FILTROS Y ACCORDION
function cargarTotalCursos() {
    const contentDiv = document.getElementById('reportContent');
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId);
    const fechaActual = obtenerFechaActual();
    const añosDisponibles = obtenerAñosDisponibles();

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Total de Cursos</h4>
            </div>
            <div class="card-body">
                <!-- Filtros de tiempo -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="selectAnioTotalCursos" class="form-label">Seleccionar Año:</label>
                                <select id="selectAnioTotalCursos" class="form-select">
                                    ${añosDisponibles.map(año =>
        `<option value="${año}" ${año === fechaActual.año ? 'selected' : ''}>${año}</option>`
    ).join('')}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="selectMesTotalCursos" class="form-label">Filtrar por Mes:</label>
                                <select id="selectMesTotalCursos" class="form-select">
                                    <option value="todos">Todos los Meses</option>
                                    ${obtenerNombresMeses().map((mes, index) => {
        const mesNumero = index + 1;
        return `<option value="${mesNumero}">${mes}</option>`;
    }).join('')}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltrosTotalCursos" class="btn btn-primary w-100" style="max-width: 300px;">
                            <i class="bi bi-filter me-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
                
                <div class="text-center py-5" id="loadingTotalCursos">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando datos...</p>
                </div>
                <div id="contenidoTotalCursos" style="display: none;"></div>
            </div>
        </div>
    `;

    // Event listeners
    document.getElementById('btnAplicarFiltrosTotalCursos')?.addEventListener('click', function () {
        const anio = parseInt(document.getElementById('selectAnioTotalCursos').value);
        const mes = document.getElementById('selectMesTotalCursos').value;
        cargarDatosTotalCursos(anio, mes);
    });

    // Cargar datos iniciales
    cargarDatosTotalCursos(fechaActual.año, 'todos');
}

function cargarDatosTotalCursos(anio, mesFiltro) {
    const loadingElement = document.getElementById('loadingTotalCursos');
    const contenidoElement = document.getElementById('contenidoTotalCursos');

    // Simular carga de datos (REEMPLAZAR CON TU API)
    setTimeout(() => {
        mostrarTotalCursos(anio, mesFiltro);

        loadingElement.style.display = 'none';
        contenidoElement.style.display = 'block';
    }, 1000);
}

function mostrarTotalCursos(anio, mesFiltro) {
    const contenidoElement = document.getElementById('contenidoTotalCursos');

    // Datos de ejemplo
    const cursosData = [
        {
            id: 1,
            nombre: "Programación en Python",
            categoria: "Programación",
            cantidadCursos: 5,
            porcentaje: "33.3%",
            participantesPromedio: 35,
            cursosActivos: 3,
            duracionPromedio: "40 horas",
            instructorPrincipal: "Juan Pérez",
            rating: 4.8,
            fechaUltimoCurso: "2024-07-15",
            estadisticas: {
                aprobacion: 92,
                asistencia: 88,
                satisfaccion: 4.7,
                desercion: 8,
                tareasCompletadas: 85
            },
            descripcion: "Curso de programación en Python desde cero hasta nivel avanzado. Incluye fundamentos, estructuras de datos, POO y proyectos prácticos."
        },
        {
            id: 2,
            nombre: "Diseño UX/UI",
            categoria: "Diseño",
            cantidadCursos: 3,
            porcentaje: "20.0%",
            participantesPromedio: 25,
            cursosActivos: 2,
            duracionPromedio: "35 horas",
            instructorPrincipal: "María García",
            rating: 4.6,
            fechaUltimoCurso: "2024-07-10",
            estadisticas: {
                aprobacion: 88,
                asistencia: 85,
                satisfaccion: 4.5,
                desercion: 12,
                tareasCompletadas: 82
            },
            descripcion: "Curso completo de diseño de experiencia de usuario e interfaces modernas. Herramientas: Figma, Adobe XD, Sketch."
        },
        {
            id: 3,
            nombre: "Machine Learning",
            categoria: "Programación",
            cantidadCursos: 4,
            porcentaje: "26.7%",
            participantesPromedio: 30,
            cursosActivos: 2,
            duracionPromedio: "50 horas",
            instructorPrincipal: "Carlos Rodríguez",
            rating: 4.9,
            fechaUltimoCurso: "2024-07-20",
            estadisticas: {
                aprobacion: 95,
                asistencia: 90,
                satisfaccion: 4.8,
                desercion: 5,
                tareasCompletadas: 90
            },
            descripcion: "Introducción al Machine Learning con Python. Algoritmos supervisados y no supervisados, scikit-learn, TensorFlow."
        },
        {
            id: 4,
            nombre: "Base de Datos Avanzada",
            categoria: "Base de Datos",
            cantidadCursos: 2,
            porcentaje: "13.3%",
            participantesPromedio: 28,
            cursosActivos: 1,
            duracionPromedio: "45 horas",
            instructorPrincipal: "Ana López",
            rating: 4.7,
            fechaUltimoCurso: "2024-07-05",
            estadisticas: {
                aprobacion: 90,
                asistencia: 87,
                satisfaccion: 4.6,
                desercion: 10,
                tareasCompletadas: 88
            },
            descripcion: "Curso avanzado de bases de datos relacionales y NoSQL. Optimización, seguridad y administración."
        },
        {
            id: 5,
            nombre: "Ciberseguridad",
            categoria: "Seguridad",
            cantidadCursos: 1,
            porcentaje: "6.7%",
            participantesPromedio: 22,
            cursosActivos: 1,
            duracionPromedio: "42 horas",
            instructorPrincipal: "Laura Sánchez",
            rating: 4.8,
            fechaUltimoCurso: "2024-07-18",
            estadisticas: {
                aprobacion: 93,
                asistencia: 86,
                satisfaccion: 4.7,
                desercion: 7,
                tareasCompletadas: 87
            },
            descripcion: "Fundamentos de ciberseguridad, pruebas de penetración y hacking ético. Herramientas y metodologías actuales."
        }
    ];

    // Calcular totales
    const totalCursos = cursosData.reduce((sum, item) => sum + item.cantidadCursos, 0);
    const totalActivos = cursosData.reduce((sum, item) => sum + item.cursosActivos, 0);
    const promedioParticipantes = Math.round(cursosData.reduce((sum, item) => sum + item.participantesPromedio, 0) / cursosData.length);

    contenidoElement.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-success">${totalCursos}</h1>
                        <p class="lead">Total de Cursos</p>
                        <small class="text-muted">Año: ${anio}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-primary">${totalActivos}</h1>
                        <p class="lead">Cursos Activos</p>
                        <small class="text-muted">${mesFiltro === 'todos' ? 'Todos los meses' : `Mes: ${obtenerNombreMes(mesFiltro)}`}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-warning">${cursosData.length}</h1>
                        <p class="lead">Categorías</p>
                        <small class="text-muted">Diferentes áreas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-info">${promedioParticipantes}</h1>
                        <p class="lead">Participantes Prom.</p>
                        <small class="text-muted">Por curso</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Distribución de Cursos por Categoría</h5>
                        <div class="accordion" id="accordionCursosDetalle">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTabla">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla" aria-expanded="false" aria-controls="collapseTabla">
                                        <i class="bi bi-table me-2"></i>Ver tabla resumen
                                    </button>
                                </h2>
                                <div id="collapseTabla" class="accordion-collapse collapse" aria-labelledby="headingTabla" data-bs-parent="#accordionCursosDetalle">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Categoría</th>
                                                        <th>Cantidad de Cursos</th>
                                                        <th>Porcentaje</th>
                                                        <th>Participantes Promedio</th>
                                                        <th>Cursos Activos</th>
                                                        <th>Duración Promedio</th>
                                                        <th>Instructor Principal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${cursosData.map(item => `
                                                        <tr>
                                                            <td>${item.categoria}</td>
                                                            <td>${item.cantidadCursos}</td>
                                                            <td>${item.porcentaje}</td>
                                                            <td>${item.participantesPromedio}</td>
                                                            <td>${item.cursosActivos}</td>
                                                            <td>${item.duracionPromedio}</td>
                                                            <td>${item.instructorPrincipal}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Detalle de Cursos por Categoría</h5>
                        <p class="text-muted mb-4">Haz clic en cada categoría para ver información detallada</p>
                        
                        <div class="accordion" id="accordionDetalleCursos">
                            ${cursosData.map((curso, index) => `
                                <div class="accordion-item mb-3 border-0 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCurso${curso.id}" 
                                                aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapseCurso${curso.id}">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div>
                                                    <span class="badge bg-primary me-2">${curso.categoria}</span>
                                                    <strong>${curso.nombre}</strong>
                                                </div>
                                                <div>
                                                    <span class="badge ${curso.cursosActivos > 0 ? 'bg-success' : 'bg-secondary'} me-2">
                                                        ${curso.cursosActivos} activos
                                                    </span>
                                                    <span class="badge bg-info">
                                                        ${curso.cantidadCursos} total
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseCurso${curso.id}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                                         data-bs-parent="#accordionDetalleCursos">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6>Descripción</h6>
                                                    <p>${curso.descripcion}</p>
                                                    
                                                    <h6 class="mt-3">Información General</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Instructor Principal:</strong> ${curso.instructorPrincipal}</p>
                                                            <p><strong>Duración Promedio:</strong> ${curso.duracionPromedio}</p>
                                                            <p><strong>Fecha Último Curso:</strong> ${curso.fechaUltimoCurso}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Rating:</strong> 
                                                                <span class="rating text-warning">
                                                                    ${'★'.repeat(Math.floor(curso.rating))}${'☆'.repeat(5 - Math.floor(curso.rating))}
                                                                </span>
                                                                (${curso.rating}/5)
                                                            </p>
                                                            <p><strong>Participantes Promedio:</strong> ${curso.participantesPromedio}</p>
                                                            <p><strong>Estado:</strong> 
                                                                <span class="badge ${curso.cursosActivos > 0 ? 'bg-success' : 'bg-secondary'}">
                                                                    ${curso.cursosActivos > 0 ? 'Activo' : 'Sin cursos activos'}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Estadísticas</h6>
                                                    <div class="card border-0 bg-light">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Tasa de Aprobación:</span>
                                                                <span class="fw-bold">${curso.estadisticas.aprobacion}%</span>
                                                            </div>
                                                            <div class="progress mb-3" style="height: 8px;">
                                                                <div class="progress-bar bg-success" style="width: ${curso.estadisticas.aprobacion}%"></div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Asistencia Promedio:</span>
                                                                <span class="fw-bold">${curso.estadisticas.asistencia}%</span>
                                                            </div>
                                                            <div class="progress mb-3" style="height: 8px;">
                                                                <div class="progress-bar bg-info" style="width: ${curso.estadisticas.asistencia}%"></div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Satisfacción:</span>
                                                                <span class="fw-bold">${curso.estadisticas.satisfaccion}/5</span>
                                                            </div>
                                                            <div class="progress mb-3" style="height: 8px;">
                                                                <div class="progress-bar bg-warning" style="width: ${(curso.estadisticas.satisfaccion / 5) * 100}%"></div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Tasa de Deserción:</span>
                                                                <span class="fw-bold">${curso.estadisticas.desercion}%</span>
                                                            </div>
                                                            <div class="progress mb-3" style="height: 8px;">
                                                                <div class="progress-bar bg-danger" style="width: ${curso.estadisticas.desercion}%"></div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between">
                                                                <span>Tareas Completadas:</span>
                                                                <span class="fw-bold">${curso.estadisticas.tareasCompletadas}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <button class="btn btn-sm btn-outline-primary w-100" onclick="verDetalleCurso(${curso.id})">
                                                            <i class="bi bi-eye me-2"></i>Ver Reporte Completo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Inicializar accordion de Bootstrap
    setTimeout(() => {
        const accordionElements = document.querySelectorAll('.accordion-button');
        accordionElements.forEach(element => {
            element.addEventListener('click', function () {
                // Cerrar otros accordions si es necesario
                if (this.getAttribute('aria-expanded') === 'true') {
                    const parentAccordion = this.closest('.accordion');
                    if (parentAccordion) {
                        const otherButtons = parentAccordion.querySelectorAll('.accordion-button:not([aria-expanded="false"])');
                        otherButtons.forEach(btn => {
                            if (btn !== this) {
                                btn.click();
                            }
                        });
                    }
                }
            });
        });
    }, 100);
}

// 2. Cursos con más participantes - MODIFICADA CON FILTROS Y SIN RATING/EDICIONES
function cargarCursosMasParticipantes() {
    const contentDiv = document.getElementById('reportContent');
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId);
    const fechaActual = obtenerFechaActual();
    const añosDisponibles = obtenerAñosDisponibles();

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Cursos con más Participantes</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="loadingCursosMasParticipantes">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando datos...</p>
                </div>
                <div id="contenidoCursosMasParticipantes" style="display: none;"></div>
            </div>
        </div>
    `;

    // Cargar datos iniciales
    cargarDatosCursosMasParticipantes(fechaActual.año, 'todos', 'todas');
}

function cargarDatosCursosMasParticipantes(anio, mesFiltro, categoriaFiltro) {
    const loadingElement = document.getElementById('loadingCursosMasParticipantes');
    const contenidoElement = document.getElementById('contenidoCursosMasParticipantes');

    // Simular carga de datos (REEMPLAZAR CON TU API)
    setTimeout(() => {
        // Datos de ejemplo - REEMPLAZA CON TU API
        const cursosData = [
            { curso: 'Programación en Python', participantes: 350, activos: 250, duracion: '40 horas', instructor: 'Juan Pérez' },
            { curso: 'Diseño UX/UI', participantes: 280, activos: 180, duracion: '35 horas', instructor: 'María García' },
            { curso: 'Machine Learning', participantes: 220, activos: 150, duracion: '50 horas', instructor: 'Carlos Rodríguez' },
            { curso: 'Desarrollo Web Full Stack', participantes: 300, activos: 220, duracion: '60 horas', instructor: 'Ana López' },
            { curso: 'Base de Datos Avanzada', participantes: 180, activos: 120, duracion: '45 horas', instructor: 'Pedro Martínez' },
            { curso: 'Ciberseguridad', participantes: 200, activos: 140, duracion: '42 horas', instructor: 'Laura Sánchez' },
            { curso: 'DevOps', participantes: 150, activos: 100, duracion: '48 horas', instructor: 'Miguel Ruiz' },
            { curso: 'Inteligencia Artificial', participantes: 190, activos: 130, duracion: '55 horas', instructor: 'Sofía Castro' }
        ];

        // Datos para el slider de popularidad - basado en periodos de tiempo reales
        const fechaActual = new Date();
        const ultimoMes = new Date(fechaActual);
        ultimoMes.setMonth(fechaActual.getMonth() - 1);

        const ultimos3Meses = new Date(fechaActual);
        ultimos3Meses.setMonth(fechaActual.getMonth() - 3);

        const ultimos6Meses = new Date(fechaActual);
        ultimos6Meses.setMonth(fechaActual.getMonth() - 6);

        const ultimoAno = new Date(fechaActual);
        ultimoAno.setFullYear(fechaActual.getFullYear() - 1);

        // Datos simulados para popularidad por periodos
        const popularidadData = {
            ultimoMes: {
                curso: 'Inteligencia Artificial',
                participantes: 85,
                crecimiento: 15,
                periodoTexto: 'Último Mes',
                fechaInicio: ultimoMes.toLocaleDateString(),
                fechaFin: fechaActual.toLocaleDateString(),
                tendencia: 'creciente',
                descripcion: 'Curso más demandado en los últimos 30 días'
            },
            ultimos3Meses: {
                curso: 'Machine Learning',
                participantes: 220,
                crecimiento: 25,
                periodoTexto: 'Últimos 3 Meses',
                fechaInicio: ultimos3Meses.toLocaleDateString(),
                fechaFin: fechaActual.toLocaleDateString(),
                tendencia: 'creciente',
                descripcion: 'Curso con mayor crecimiento trimestral'
            },
            ultimos6Meses: {
                curso: 'Programación en Python',
                participantes: 350,
                crecimiento: 18,
                periodoTexto: 'Últimos 6 Meses',
                fechaInicio: ultimos6Meses.toLocaleDateString(),
                fechaFin: fechaActual.toLocaleDateString(),
                tendencia: 'creciente',
                descripcion: 'Curso más consistente en el semestre'
            },
            ultimoAno: {
                curso: 'Desarrollo Web Full Stack',
                participantes: 620,
                crecimiento: 35,
                periodoTexto: 'Último Año',
                fechaInicio: ultimoAno.toLocaleDateString(),
                fechaFin: fechaActual.toLocaleDateString(),
                tendencia: 'creciente',
                descripcion: 'Curso con mayor demanda anual'
            }
        };

        mostrarCursosMasParticipantes(cursosData, popularidadData, anio, mesFiltro, categoriaFiltro);

        loadingElement.style.display = 'none';
        contenidoElement.style.display = 'block';
    }, 1000);
}

function mostrarCursosMasParticipantes(data, popularidadData, anio, mesFiltro, categoriaFiltro) {
    const contenidoElement = document.getElementById('contenidoCursosMasParticipantes');

    const totalParticipantesActivos = data.reduce((sum, item) => sum + item.activos, 0);
    const promedioPorCurso = Math.round(data.reduce((sum, item) => sum + item.activos, 0) / data.length);

    // Obtener las claves de los periodos
    const periodos = ['ultimoMes', 'ultimos3Meses', 'ultimos6Meses', 'ultimoAno'];

    contenidoElement.innerHTML = `
        <!-- Slider de popularidad -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Popularidad de Cursos por Periodo</h5>
                        
                        <!-- Carousel de Bootstrap -->
                        <div id="carouselPopularidad" class="carousel slide" data-bs-ride="false">
                            <!-- Indicadores -->
                            <div class="carousel-indicators">
                                ${periodos.map((_, index) => `
                                    <button type="button" data-bs-target="#carouselPopularidad" 
                                            data-bs-slide-to="${index}" 
                                            ${index === 0 ? 'class="active" aria-current="true"' : ''} 
                                            aria-label="Slide ${index + 1}"></button>
                                `).join('')}
                            </div>
                            
                            <!-- Slides del carousel -->
                            <div class="carousel-inner">
                                ${periodos.map((periodo, index) => {
        const periodoData = popularidadData[periodo];
        return `
                                        <div class="carousel-item ${index === 0 ? 'active' : ''}" data-periodo-index="${index}">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="p-4">
                                                        <h3 class="text-primary">${periodoData.curso}</h3>
                                                        <h6 class="text-muted">${periodoData.periodoTexto}</h6>
                                                        <p class="lead">${periodoData.descripcion}</p>
                                                        <div class="row mt-4">
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-people-fill text-success fs-4 me-3"></i>
                                                                    <div>
                                                                        <h4 class="mb-0">${periodoData.participantes}</h4>
                                                                        <small class="text-muted">Participantes</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-graph-up-arrow ${periodoData.tendencia === 'creciente' ? 'text-success' : 'text-danger'} fs-4 me-3"></i>
                                                                    <div>
                                                                        <h4 class="mb-0">${periodoData.crecimiento}%</h4>
                                                                        <small class="text-muted">Crecimiento</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar me-1"></i>
                                                                ${periodoData.fechaInicio} - ${periodoData.fechaFin}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center p-4">
                                                        <div class="position-relative">
                                                            <div class="progress-circle" 
                                                                 style="--progress: ${periodoData.crecimiento};">
                                                                <span class="progress-value">${periodoData.crecimiento}%</span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <span class="badge ${periodoData.tendencia === 'creciente' ? 'bg-success' : 'bg-danger'}">
                                                                <i class="bi ${periodoData.tendencia === 'creciente' ? 'bi-arrow-up' : 'bi-arrow-down'} me-1"></i>
                                                                ${periodoData.tendencia === 'creciente' ? 'En crecimiento' : 'En descenso'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
    }).join('')}
                            </div>
                            
                            <!-- Controles del carousel -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselPopularidad" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselPopularidad" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                        
                        <!-- Indicadores de navegación adicionales -->
                        <div class="row mt-3" id="botonesPeriodo">
                            ${periodos.map((periodo, index) => {
        const periodoData = popularidadData[periodo];
        return `
                                    <div class="col-md-3 text-center">
                                        <button type="button" 
                                                class="btn btn-sm ${index === 0 ? 'btn-primary' : 'btn-outline-primary'} btn-periodo"
                                                data-periodo-index="${index}">
                                            ${periodoData.periodoTexto}
                                        </button>
                                    </div>
                                `;
    }).join('')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas actuales -->
        <div class="row mt-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-primary">${totalParticipantesActivos.toLocaleString()}</h3>
                        <p class="text-muted mb-0">Participantes Activos</p>
                        <small class="text-muted">Actualmente en cursos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-success">${promedioPorCurso}</h3>
                        <p class="text-muted mb-0">Promedio por Curso</p>
                        <small class="text-muted">Participantes activos promedio</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-warning">${data.length}</h3>
                        <p class="text-muted mb-0">Total de Cursos</p>
                        <small class="text-muted">Activos y en progreso</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtros de tiempo -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <label for="selectAnioCursosParticipantes" class="form-label">Seleccionar Año:</label>
                        <select id="selectAnioCursosParticipantes" class="form-select" onchange="actualizarGraficaCursos()">
                            ${obtenerAñosDisponibles().map(año =>
        `<option value="${año}" ${año === anio ? 'selected' : ''}>${año}</option>`
    ).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="selectMesCursosParticipantes" class="form-label">Filtrar por Mes:</label>
                        <select id="selectMesCursosParticipantes" class="form-select" onchange="actualizarGraficaCursos()">
                            <option value="todos" ${mesFiltro === 'todos' ? 'selected' : ''}>Todos los Meses</option>
                            ${obtenerNombresMeses().map((mes, index) => {
        const mesNumero = index + 1;
        return `<option value="${mesNumero}" ${mesFiltro === mesNumero.toString() ? 'selected' : ''}>${mes}</option>`;
    }).join('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="selectCategoriaCursosParticipantes" class="form-label">Filtrar por Categoría:</label>
                        <select id="selectCategoriaCursosParticipantes" class="form-select" onchange="actualizarGraficaCursos()">
                            <option value="todas" ${categoriaFiltro === 'todas' ? 'selected' : ''}>Todas las Categorías</option>
                            <option value="programacion" ${categoriaFiltro === 'programacion' ? 'selected' : ''}>Programación</option>
                            <option value="diseno" ${categoriaFiltro === 'diseno' ? 'selected' : ''}>Diseño</option>
                            <option value="redes" ${categoriaFiltro === 'redes' ? 'selected' : ''}>Redes</option>
                            <option value="basedatos" ${categoriaFiltro === 'basedatos' ? 'selected' : ''}>Base de Datos</option>
                            <option value="seguridad" ${categoriaFiltro === 'seguridad' ? 'selected' : ''}>Seguridad</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button id="btnAplicarFiltrosCursosParticipantes" class="btn btn-primary w-100" style="max-width: 300px;" onclick="actualizarGraficaCursos()">
                    <i class="bi bi-filter me-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>
        
        <!-- Gráfica completa debajo de estadísticas -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Top Cursos por Participantes Activos</h5>
                        <div class="text-center mb-3">
                            <small class="text-muted">Año: ${anio} | ${mesFiltro === 'todos' ? 'Todos los meses' : `Mes: ${obtenerNombreMes(mesFiltro)}`} | Categoría: ${categoriaFiltro === 'todas' ? 'Todas' : categoriaFiltro}</small>
                        </div>
                        <div style="height: 400px; width: 100%;">
                            <canvas id="graficoCursosMasParticipantes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla debajo de la gráfica -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Lista Detallada de Cursos</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Curso</th>
                                        <th>Participantes Activos</th>
                                        <th>Total Inscritos</th>
                                        <th>Duración</th>
                                        <th>Instructor</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map((item, index) => `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.curso}</td>
                                            <td>${item.activos}</td>
                                            <td>${item.participantes}</td>
                                            <td>${item.duracion}</td>
                                            <td>${item.instructor}</td>
                                            <td>
                                                <span class="badge ${item.activos > 200 ? 'bg-success' : item.activos > 120 ? 'bg-warning' : 'bg-info'}">
                                                    ${item.activos > 200 ? 'Muy Activo' : item.activos > 120 ? 'Activo' : 'Normal'}
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estilos para el círculo de progreso -->
        <style>
            .progress-circle {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                background: conic-gradient(#4e54c8 calc(var(--progress) * 1%), #ddd 0%);
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                margin: 0 auto;
            }
            
            .progress-circle::before {
                content: '';
                position: absolute;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: white;
            }
            
            .progress-value {
                position: relative;
                font-size: 1.5rem;
                font-weight: bold;
                color: #4e54c8;
                z-index: 1;
            }
            
            .btn-periodo {
                width: 100%;
                transition: all 0.3s ease;
            }
            
            .btn-periodo:hover {
                transform: translateY(-2px);
            }
        </style>
    `;

    // Crear gráfico
    crearGraficaCursosParticipantes(data, anio, mesFiltro, categoriaFiltro);

    // Inicializar el carousel con sincronización de botones
    inicializarCarouselPopularidad();

    // Inicializar el slideshow manual
    setTimeout(() => {
        inicializarSlideshow();
    }, 100);
}

// Nueva función para crear/actualizar gráfica con filtros
function crearGraficaCursosParticipantes(data, anio, mesFiltro, categoriaFiltro) {
    const ctx = document.getElementById('graficoCursosMasParticipantes')?.getContext('2d');
    if (!ctx) return;

    // Destruir gráfico anterior si existe
    if (chartInstances['graficoCursosMasParticipantes']) {
        chartInstances['graficoCursosMasParticipantes'].destroy();
    }

    // Filtrar datos si es necesario
    let datosFiltrados = [...data];

    // Aquí iría la lógica de filtrado real según año, mes y categoría
    // Por ahora solo mostramos todos los datos

    // Determinar color basado en filtros
    let colorPrincipal;
    if (categoriaFiltro === 'programacion') {
        colorPrincipal = 'rgba(54, 162, 235, 0.8)';
    } else if (categoriaFiltro === 'diseno') {
        colorPrincipal = 'rgba(255, 99, 132, 0.8)';
    } else if (categoriaFiltro === 'redes') {
        colorPrincipal = 'rgba(255, 159, 64, 0.8)';
    } else if (categoriaFiltro === 'basedatos') {
        colorPrincipal = 'rgba(75, 192, 192, 0.8)';
    } else if (categoriaFiltro === 'seguridad') {
        colorPrincipal = 'rgba(153, 102, 255, 0.8)';
    } else {
        colorPrincipal = 'rgba(54, 162, 235, 0.8)';
    }

    chartInstances['graficoCursosMasParticipantes'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: datosFiltrados.map(item => item.curso),
            datasets: [{
                label: 'Participantes Activos',
                data: datosFiltrados.map(item => item.activos),
                backgroundColor: colorPrincipal,
                borderColor: colorPrincipal.replace('0.8', '1'),
                borderWidth: 2,
                borderRadius: 5,
                barPercentage: 0.6,
            }, {
                label: 'Total Inscritos',
                data: datosFiltrados.map(item => item.participantes),
                backgroundColor: 'rgba(201, 203, 207, 0.8)',
                borderColor: 'rgba(201, 203, 207, 1)',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 800,
                easing: 'easeInOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Participantes',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const curso = datosFiltrados[context.dataIndex];
                            const label = context.dataset.label === 'Participantes Activos' ?
                                `Activos: ${curso.activos} (${Math.round((curso.activos / curso.participantes) * 100)}% del total)` :
                                `Total: ${curso.participantes}`;
                            return label;
                        }
                    }
                }
            }
        }
    });
}

// Función para actualizar gráfica con filtros
function actualizarGraficaCursos() {
    const anio = parseInt(document.getElementById('selectAnioCursosParticipantes')?.value || new Date().getFullYear());
    const mes = document.getElementById('selectMesCursosParticipantes')?.value || 'todos';
    const categoria = document.getElementById('selectCategoriaCursosParticipantes')?.value || 'todas';

    // Simular recarga de datos con filtros
    const loadingElement = document.getElementById('loadingCursosMasParticipantes');
    const contenidoElement = document.getElementById('contenidoCursosMasParticipantes');

    // Mostrar loading
    const graficaContainer = document.querySelector('#graficoCursosMasParticipantes')?.parentElement;
    if (graficaContainer) {
        graficaContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Actualizando gráfica...</span>
                </div>
                <p class="mt-2">Aplicando filtros: ${anio} - ${mes === 'todos' ? 'Todos los meses' : obtenerNombreMes(mes)} - ${categoria === 'todas' ? 'Todas categorías' : categoria}</p>
            </div>
        `;
    }

    setTimeout(() => {
        // Simular datos filtrados (en realidad deberías hacer una llamada API aquí)
        const cursosFiltrados = [
            { curso: 'Programación en Python', participantes: 350, activos: 250, duracion: '40 horas', instructor: 'Juan Pérez' },
            { curso: 'Machine Learning', participantes: 220, activos: 150, duracion: '50 horas', instructor: 'Carlos Rodríguez' },
            { curso: 'Desarrollo Web Full Stack', participantes: 300, activos: 220, duracion: '60 horas', instructor: 'Ana López' },
            { curso: 'Ciberseguridad', participantes: 200, activos: 140, duracion: '42 horas', instructor: 'Laura Sánchez' },
            { curso: 'Inteligencia Artificial', participantes: 190, activos: 130, duracion: '55 horas', instructor: 'Sofía Castro' }
        ];

        // Actualizar gráfica con datos filtrados
        crearGraficaCursosParticipantes(cursosFiltrados, anio, mes, categoria);

        // Actualizar texto de filtros en la gráfica
        const tituloGrafica = document.querySelector('#graficoCursosMasParticipantes')?.closest('.card-body').querySelector('.text-center');
        if (tituloGrafica) {
            tituloGrafica.innerHTML = `
                <small class="text-muted">
                    Año: ${anio} | 
                    ${mes === 'todos' ? 'Todos los meses' : `Mes: ${obtenerNombreMes(mes)}`} | 
                    Categoría: ${categoria === 'todas' ? 'Todas' : categoria}
                </small>
            `;
        }
    }, 500);
}

function inicializarCarouselPopularidad() {
    // Inicializar el carousel de Bootstrap
    const carouselElement = document.getElementById('carouselPopularidad');
    if (carouselElement && typeof bootstrap !== 'undefined') {
        // Configurar sin auto-play inicial
        const carousel = new bootstrap.Carousel(carouselElement, {
            interval: false, // Desactivar auto-play inicial
            wrap: true,
            keyboard: true
        });

        // Obtener todos los botones de periodo
        const botonesPeriodo = document.querySelectorAll('.btn-periodo');

        // Función para actualizar el botón activo
        function actualizarBotonActivo(index) {
            botonesPeriodo.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-primary');
                } else {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                }
            });
        }

        // Evento cuando el carousel cambia de slide
        carouselElement.addEventListener('slid.bs.carousel', function (event) {
            const activeIndex = Array.from(carouselElement.querySelectorAll('.carousel-item')).indexOf(event.relatedTarget);
            actualizarBotonActivo(activeIndex);
        });

        // Agregar eventos a los botones de periodo
        botonesPeriodo.forEach((boton, index) => {
            boton.addEventListener('click', function () {
                // Ir al slide correspondiente
                carousel.to(index);
                actualizarBotonActivo(index);

                // Reiniciar slideshow después de clic manual
                iniciarSlideshowAutomatico();
            });
        });

        // Agregar controles de teclado
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                carousel.prev();
                iniciarSlideshowAutomatico();
            } else if (e.key === 'ArrowRight') {
                carousel.next();
                iniciarSlideshowAutomatico();
            }
        });

        // Inicializar el botón activo al primer slide
        setTimeout(() => {
            actualizarBotonActivo(0);

            // Iniciar slideshow automático después de 2 segundos
            setTimeout(() => {
                iniciarSlideshowAutomatico();
            }, 2000);
        }, 100);
    }
}

function iniciarSlideshowAutomatico() {
    const carouselElement = document.getElementById('carouselPopularidad');
    if (!carouselElement || typeof bootstrap === 'undefined') return;

    // Limpiar intervalo anterior
    if (slideshowInterval) {
        clearInterval(slideshowInterval);
    }

    // Iniciar nuevo intervalo
    slideshowInterval = setInterval(() => {
        const carousel = bootstrap.Carousel.getInstance(carouselElement);
        if (carousel) {
            carousel.next();
        }
    }, slideshowDelay);
}

// 3. Curso más requerido - VERSIÓN MODIFICADA: 3 meses en lugar de 6
function cargarCursoMasRequerido() {
    const contentDiv = document.getElementById('reportContent');
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId);

    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>Curso más Requerido</h4>
                <p class="mb-0 mt-2 small opacity-75">Reporte basado en los últimos 90 días (3 meses) - Análisis para determinar qué cursos reabrir</p>
            </div>
            <div class="card-body">
                <!-- Sección 1: Los 3 cursos más requeridos con trofeos -->
                <div id="contenedorTop3Cursos" class="text-center py-4" style="display: none;">
                    <h5 class="text-muted mb-4">TOP 3 CURSOS MÁS REQUERIDOS (ÚLTIMOS 90 DÍAS)</h5>
                    <div class="row justify-content-center align-items-stretch">
                        <!-- Primer curso - REQUERIMIENTO MÁS ALTO -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow h-100">
                                <div class="card-body text-center py-4 d-flex flex-column">
                                    <!-- Trofeo para primer lugar -->
                                    <div class="mb-3">
                                        <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    
                                    <!-- Nombre del curso -->
                                    <h4 id="cursoPrimero" class="curso-nombre flex-grow-1">Cargando...</h4>
                                    
                                    <!-- Estadísticas -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-center mb-2">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-people-fill me-1"></i>
                                                <span id="participantesPrimero">0</span>
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-graph-up me-1"></i>
                                                <span id="crecimientoPrimero">0</span>%
                                            </span>
                                        </div>
                                        
                                        <!-- Score simple -->
                                        <div class="mt-2">
                                            <div class="fs-5 fw-bold text-primary" id="scorePrimero">0</div>
                                            <small class="text-muted">Índice de requerimiento</small>
                                        </div>
                                        
                                        <!-- Tendencia -->
                                        <div class="mt-2">
                                            <span class="badge bg-success" id="tendenciaPrimero">
                                                <i class="bi bi-arrow-up me-1"></i>ALTA PRIORIDAD
                                            </span>
                                        </div>
                                        
                                        <!-- Recomendación -->
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
                        
                        <!-- Segundo curso -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow h-100">
                                <div class="card-body text-center py-4 d-flex flex-column">
                                    <!-- Medalla para segundo lugar -->
                                    <div class="mb-3">
                                        <i class="bi bi-award-fill text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                    
                                    <!-- Nombre del curso -->
                                    <h4 id="cursoSegundo" class="curso-nombre flex-grow-1">Cargando...</h4>
                                    
                                    <!-- Estadísticas -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-center mb-2">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-people-fill me-1"></i>
                                                <span id="participantesSegundo">0</span>
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-graph-up me-1"></i>
                                                <span id="crecimientoSegundo">0</span>%
                                            </span>
                                        </div>
                                        
                                        <!-- Score simple -->
                                        <div class="mt-2">
                                            <div class="fs-5 fw-bold text-primary" id="scoreSegundo">0</div>
                                            <small class="text-muted">Índice de requerimiento</small>
                                        </div>
                                        
                                        <!-- Tendencia -->
                                        <div class="mt-2">
                                            <span class="badge bg-warning" id="tendenciaSegundo">
                                                <i class="bi bi-arrow-up me-1"></i>MEDIA PRIORIDAD
                                            </span>
                                        </div>
                                        
                                        <!-- Recomendación -->
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
                        
                        <!-- Tercer curso -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow h-100">
                                <div class="card-body text-center py-4 d-flex flex-column">
                                    <!-- Medalla para tercer lugar -->
                                    <div class="mb-3">
                                        <i class="bi bi-award text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    
                                    <!-- Nombre del curso -->
                                    <h4 id="cursoTercero" class="curso-nombre flex-grow-1">Cargando...</h4>
                                    
                                    <!-- Estadísticas -->
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-center mb-2">
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-people-fill me-1"></i>
                                                <span id="participantesTercero">0</span>
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-graph-up me-1"></i>
                                                <span id="crecimientoTercero">0</span>%
                                            </span>
                                        </div>
                                        
                                        <!-- Score simple -->
                                        <div class="mt-2">
                                            <div class="fs-5 fw-bold text-primary" id="scoreTercero">0</div>
                                            <small class="text-muted">Índice de requerimiento</small>
                                        </div>
                                        
                                        <!-- Tendencia -->
                                        <div class="mt-2">
                                            <span class="badge bg-info" id="tendenciaTercero">
                                                <i class="bi bi-arrow-up me-1"></i>BAJA PRIORIDAD
                                            </span>
                                        </div>
                                        
                                        <!-- Recomendación -->
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
                
                <!-- Sección 2: Estadísticas de solicitudes -->
                <div class="row mt-5" id="estadisticasSolicitudes" style="display: none;">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">ESTADÍSTICAS DE REQUERIMIENTO (ÚLTIMOS 90 DÍAS)</h5>
                                <div class="row text-center mt-4">
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3">
                                            <h2 class="text-primary" id="totalAnalizado">0</h2>
                                            <p class="text-muted mb-0">Cursos Analizados</p>
                                            <small class="text-muted">En los últimos 90 días</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3">
                                            <h2 class="text-success" id="promedioScore">0</h2>
                                            <p class="text-muted mb-0">Puntuación Promedio</p>
                                            <small class="text-muted">Índice de requerimiento</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3">
                                            <h2 class="text-warning" id="crecimientoPromedio">0%</h2>
                                            <p class="text-muted mb-0">Crecimiento Promedio</p>
                                            <small class="text-muted">Tendencia general</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3">
                                            <h2 class="text-info" id="participantesPromedio">0</h2>
                                            <p class="text-muted mb-0">Participantes Promedio</p>
                                            <small class="text-muted">Por curso (90 días)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 3: Filtros simplificados -->
                <div class="row mt-4" id="filtrosSimplificados" style="display: none;">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">FILTROS DE ANÁLISIS</h5>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="selectPeriodoAnalisis" class="form-label">Período de Análisis:</label>
                                                <select id="selectPeriodoAnalisis" class="form-select" onchange="actualizarPeriodoAnalisis()">
                                                    <option value="90" selected>Últimos 90 días (3 meses)</option>
                                                    <option value="180">Últimos 180 días (6 meses)</option>
                                                    <option value="365">Último año</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="selectCategoriaAnalisis" class="form-label">Categoría:</label>
                                                <select id="selectCategoriaAnalisis" class="form-select" onchange="actualizarPeriodoAnalisis()">
                                                    <option value="todas" selected>Todas las categorías</option>
                                                    <option value="tecnologia">Tecnología</option>
                                                    <option value="desarrollo">Desarrollo</option>
                                                    <option value="diseno">Diseño</option>
                                                    <option value="negocios">Negocios</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button id="btnActualizarReporte" class="btn btn-primary mt-4">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Actualizar Análisis
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 4: Tabla de ranking completo -->
                <div class="row mt-4" id="tablaRankingCompleto" style="display: none;">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">RANKING COMPLETO DE CURSOS MÁS REQUERIDOS</h5>
                                <p class="text-muted">Ordenado por índice de requerimiento (basado en últimos 90 días)</p>
                                
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="80">#</th>
                                                <th>Curso</th>
                                                <th width="150">Participantes (90 días)</th>
                                                <th width="150">Crecimiento (%)</th>
                                                <th width="150">Índice Requerimiento</th>
                                                <th width="150">Tendencia</th>
                                                <th width="120">Acción Sugerida</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaRankingBody">
                                            <!-- Los datos se cargarán aquí -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading inicial -->
                <div class="text-center py-5" id="loadingCursoMasRequerido">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Analizando datos de los últimos 90 días...</p>
                </div>
            </div>
        </div>
        
        <!-- Estilos específicos para este reporte -->
        <style>
            .curso-nombre {
                font-weight: 600;
                height: 3em;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                margin-bottom: 1rem;
            }
            
            .align-items-stretch .card {
                display: flex;
                flex-direction: column;
            }
            
            .align-items-stretch .card-body {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            
            .flex-grow-1 {
                flex-grow: 1;
            }
            
            @media (max-width: 768px) {
                .row.justify-content-center {
                    flex-direction: column;
                }
            }
        </style>
    `;

    // Event listeners
    document.getElementById('btnActualizarReporte')?.addEventListener('click', function () {
        cargarDatosCursoMasRequerido();
    });

    // Cargar datos iniciales (últimos 90 días - 3 meses)
    cargarDatosCursoMasRequerido();
}

function cargarDatosCursoMasRequerido() {
    const loadingElement = document.getElementById('loadingCursoMasRequerido');
    const contenedorTop3 = document.getElementById('contenedorTop3Cursos');
    const estadisticas = document.getElementById('estadisticasSolicitudes');
    const filtros = document.getElementById('filtrosSimplificados');
    const tablaRanking = document.getElementById('tablaRankingCompleto');

    // Obtener período seleccionado
    const periodoSelect = document.getElementById('selectPeriodoAnalisis');
    const diasAnalisis = periodoSelect ? parseInt(periodoSelect.value) : 90; // 90 días por defecto (3 meses)
    const categoriaSelect = document.getElementById('selectCategoriaAnalisis');
    const categoriaAnalisis = categoriaSelect ? categoriaSelect.value : 'todas';

    // Ocultar secciones y mostrar loading
    if (contenedorTop3) contenedorTop3.style.display = 'none';
    if (estadisticas) estadisticas.style.display = 'none';
    if (filtros) filtros.style.display = 'none';
    if (tablaRanking) tablaRanking.style.display = 'none';
    if (loadingElement) {
        loadingElement.style.display = 'block';
        loadingElement.querySelector('p').textContent = `Analizando datos de los últimos ${diasAnalisis} días...`;
    }

    // Simular carga de datos (REEMPLAZAR CON TU API)
    setTimeout(() => {
        // Obtener fecha actual y calcular fecha de inicio
        const fechaFin = new Date();
        const fechaInicio = new Date();
        fechaInicio.setDate(fechaInicio.getDate() - diasAnalisis);

        // Datos simulados basados en participantes y crecimiento de los últimos días
        const cursosData = [
            {
                curso: 'Inteligencia Artificial Aplicada',
                participantes: 185,
                crecimiento: 42,  // Porcentaje de crecimiento
                duracion: '60 horas',
                instructor: 'Dr. Carlos Rodríguez',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Machine Learning Avanzado',
                participantes: 165,
                crecimiento: 35,
                duracion: '55 horas',
                instructor: 'Dra. Sofía Castro',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Desarrollo Web Full Stack',
                participantes: 155,
                crecimiento: 28,
                duracion: '70 horas',
                instructor: 'Ing. Ana López',
                categoria: 'desarrollo',
                tendencia: 'creciente'
            },
            {
                curso: 'Ciberseguridad y Hacking Ético',
                participantes: 140,
                crecimiento: 55,
                duracion: '65 horas',
                instructor: 'Lic. Laura Sánchez',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Blockchain y Criptomonedas',
                participantes: 125,
                crecimiento: 65,
                duracion: '50 horas',
                instructor: 'Mtro. Miguel Ruiz',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Data Science con Python',
                participantes: 135,
                crecimiento: 38,
                duracion: '45 horas',
                instructor: 'Dr. Juan Pérez',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Diseño UX/UI Avanzado',
                participantes: 115,
                crecimiento: 25,
                duracion: '40 horas',
                instructor: 'Diseñadora María García',
                categoria: 'diseno',
                tendencia: 'creciente'
            },
            {
                curso: 'DevOps y CI/CD',
                participantes: 105,
                crecimiento: 22,
                duracion: '48 horas',
                instructor: 'Ing. Pedro Martínez',
                categoria: 'desarrollo',
                tendencia: 'creciente'
            },
            {
                curso: 'IoT y Sistemas Embebidos',
                participantes: 95,
                crecimiento: 35,
                duracion: '52 horas',
                instructor: 'Ing. Roberto Díaz',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            },
            {
                curso: 'Realidad Virtual y Aumentada',
                participantes: 85,
                crecimiento: 48,
                duracion: '58 horas',
                instructor: 'Lic. Patricia Morales',
                categoria: 'tecnologia',
                tendencia: 'creciente'
            }
        ];

        // Filtrar por categoría si no es "todas"
        let datosFiltrados = cursosData;
        if (categoriaAnalisis !== 'todas') {
            datosFiltrados = cursosData.filter(curso => curso.categoria === categoriaAnalisis);
        }

        // Encontrar valores máximos para normalización
        const maxParticipantes = Math.max(...datosFiltrados.map(c => c.participantes));
        const maxCrecimiento = Math.max(...datosFiltrados.map(c => c.crecimiento));

        // Calcular score para cada curso (fórmula ajustada para 0-100)
        datosFiltrados.forEach(curso => {
            // Normalizar participantes (0-100)
            const participantesNormalizado = (curso.participantes / maxParticipantes) * 100;

            // Normalizar crecimiento (0-100)
            const crecimientoNormalizado = (curso.crecimiento / maxCrecimiento) * 100;

            // Calcular score (60% participantes + 40% crecimiento) - Más peso al crecimiento reciente
            // Esto produce un valor entre 0-100
            curso.score = Math.round((participantesNormalizado * 0.6) + (crecimientoNormalizado * 0.4));
        });

        // Ordenar por score (de mayor a menor)
        datosFiltrados.sort((a, b) => b.score - a.score);

        // Calcular estadísticas
        const totalCursos = datosFiltrados.length;
        const totalParticipantes = datosFiltrados.reduce((sum, item) => sum + item.participantes, 0);
        const promedioParticipantes = Math.round(totalParticipantes / totalCursos);
        const totalCrecimiento = datosFiltrados.reduce((sum, item) => sum + item.crecimiento, 0);
        const promedioCrecimiento = Math.round(totalCrecimiento / totalCursos);
        const promedioScore = Math.round(datosFiltrados.reduce((sum, item) => sum + item.score, 0) / totalCursos);

        // Mostrar los datos
        mostrarCursoMasRequerido(datosFiltrados, {
            fechaInicio: fechaInicio.toLocaleDateString(),
            fechaFin: fechaFin.toLocaleDateString(),
            totalCursos,
            promedioParticipantes,
            promedioCrecimiento,
            promedioScore,
            diasAnalisis
        });

        // Mostrar secciones
        if (loadingElement) loadingElement.style.display = 'none';
        if (contenedorTop3) contenedorTop3.style.display = 'block';
        if (estadisticas) estadisticas.style.display = 'block';
        if (filtros) filtros.style.display = 'block';
        if (tablaRanking) tablaRanking.style.display = 'block';
    }, 1500);
}

function mostrarCursoMasRequerido(cursosData, estadisticas) {
    // Mostrar top 3 cursos
    if (cursosData.length >= 3) {
        // Primer curso
        document.getElementById('cursoPrimero').textContent = cursosData[0].curso;
        document.getElementById('participantesPrimero').textContent = cursosData[0].participantes;
        document.getElementById('crecimientoPrimero').textContent = cursosData[0].crecimiento;
        document.getElementById('scorePrimero').textContent = cursosData[0].score;

        // Segundo curso
        document.getElementById('cursoSegundo').textContent = cursosData[1].curso;
        document.getElementById('participantesSegundo').textContent = cursosData[1].participantes;
        document.getElementById('crecimientoSegundo').textContent = cursosData[1].crecimiento;
        document.getElementById('scoreSegundo').textContent = cursosData[1].score;

        // Tercer curso
        document.getElementById('cursoTercero').textContent = cursosData[2].curso;
        document.getElementById('participantesTercero').textContent = cursosData[2].participantes;
        document.getElementById('crecimientoTercero').textContent = cursosData[2].crecimiento;
        document.getElementById('scoreTercero').textContent = cursosData[2].score;
    }

    // Actualizar estadísticas
    document.getElementById('totalAnalizado').textContent = estadisticas.totalCursos;
    document.getElementById('promedioScore').textContent = estadisticas.promedioScore;
    document.getElementById('crecimientoPromedio').textContent = `${estadisticas.promedioCrecimiento}%`;
    document.getElementById('participantesPromedio').textContent = estadisticas.promedioParticipantes;

    // Actualizar título del reporte
    const headerSubtitle = document.querySelector('.card-header p.small');
    if (headerSubtitle) {
        headerSubtitle.textContent = `Reporte basado en los últimos ${estadisticas.diasAnalisis} días (${Math.round(estadisticas.diasAnalisis / 30)} meses) - Análisis para determinar qué cursos reabrir`;
    }

    // Actualizar texto en tabla
    const tablaTitle = document.querySelector('#tablaRankingCompleto p.text-muted');
    if (tablaTitle) {
        tablaTitle.textContent = `Ordenado por índice de requerimiento (basado en últimos ${estadisticas.diasAnalisis} días)`;
    }

    // Generar tabla de ranking
    const tablaBody = document.getElementById('tablaRankingBody');
    if (tablaBody) {
        tablaBody.innerHTML = cursosData.map((curso, index) => `
            <tr ${index < 3 ? 'class="table-warning"' : ''}>
                <td>
                    <span class="badge ${index === 0 ? 'bg-warning text-dark' : index === 1 ? 'bg-secondary' : index === 2 ? 'bg-danger' : 'bg-info'}">
                        ${index + 1}
                    </span>
                </td>
                <td>
                    <strong>${curso.curso}</strong>
                    <br>
                    <small class="text-muted">${curso.duracion} • ${curso.instructor}</small>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        <span>${curso.participantes}</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi ${curso.crecimiento > 30 ? 'bi-graph-up-arrow text-success' : 'bi-graph-up text-warning'} me-2"></i>
                        <span>${curso.crecimiento}%</span>
                    </div>
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar ${curso.score > 75 ? 'bg-success' : curso.score > 50 ? 'bg-warning' : 'bg-info'}" 
                             style="width: ${curso.score}%">
                            ${curso.score}
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge ${curso.tendencia === 'creciente' ? 'bg-success' : 'bg-danger'}">
                        <i class="bi ${curso.tendencia === 'creciente' ? 'bi-arrow-up' : 'bi-arrow-down'} me-1"></i>
                        ${curso.tendencia}
                    </span>
                </td>
                <td>
                    <span class="badge ${curso.score > 75 ? 'bg-success' : curso.score > 50 ? 'bg-warning' : 'bg-secondary'}">
                        ${curso.score > 75 ? 'Reabrir pronto' : curso.score > 50 ? 'Considerar' : 'Mantener'}
                    </span>
                </td>
            </tr>
        `).join('');
    }
}

function actualizarPeriodoAnalisis() {
    cargarDatosCursoMasRequerido();
}

// Funciones auxiliares
function obtenerNombreMes(numeroMes) {
    const meses = obtenerNombresMeses();
    return meses[parseInt(numeroMes) - 1] || 'Desconocido';
}

function inicializarSlideshow() {
    const slides = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicators button');

    if (slides.length === 0) return;

    // Reiniciar el slideshow
    if (slideshowInterval) {
        clearInterval(slideshowInterval);
    }

    // Mostrar el primer slide activo
    slides.forEach((slide, index) => {
        slide.classList.remove('active');
        if (indicators[index]) {
            indicators[index].classList.remove('active');
            indicators[index].setAttribute('aria-current', 'false');
        }
    });

    slides[0].classList.add('active');
    if (indicators[0]) {
        indicators[0].classList.add('active');
        indicators[0].setAttribute('aria-current', 'true');
    }

    // Configurar el intervalo después de 2 segundos de inactividad
    setTimeout(() => {
        slideshowInterval = setInterval(() => {
            cambiarSlide(1); // Cambiar al siguiente slide
        }, slideshowDelay);
    }, 2000);

    // Configurar eventos para los controles
    document.querySelectorAll('.carousel-control-prev').forEach(control => {
        control.addEventListener('click', () => {
            cambiarSlide(-1);
            reiniciarIntervalo();
        });
    });

    document.querySelectorAll('.carousel-control-next').forEach(control => {
        control.addEventListener('click', () => {
            cambiarSlide(1);
            reiniciarIntervalo();
        });
    });

    // Configurar eventos para los indicadores
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlideIndex = index;
            mostrarSlide(currentSlideIndex);
            reiniciarIntervalo();
        });
    });
}

function cambiarSlide(direction) {
    const slides = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicators button');

    if (slides.length === 0) return;

    // Calcular nuevo índice
    currentSlideIndex = (currentSlideIndex + direction + slides.length) % slides.length;

    // Mostrar el slide
    mostrarSlide(currentSlideIndex);
}

function mostrarSlide(index) {
    const slides = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicators button');

    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => {
        indicator.classList.remove('active');
        indicator.setAttribute('aria-current', 'false');
    });

    slides[index].classList.add('active');
    if (indicators[index]) {
        indicators[index].classList.add('active');
        indicators[index].setAttribute('aria-current', 'true');
    }

    currentSlideIndex = index;
}

function reiniciarIntervalo() {
    // Reiniciar el intervalo después de interacción manual
    if (slideshowInterval) {
        clearInterval(slideshowInterval);
    }

    // Iniciar nuevo intervalo después de 5 segundos
    setTimeout(() => {
        slideshowInterval = setInterval(() => {
            cambiarSlide(1);
        }, slideshowDelay);
    }, 5000);
}

function verDetalleCurso(idCurso) {
    alert(`Mostrando detalles completos del curso #${idCurso}...\n\nEsta funcionalidad mostraría un reporte detallado del curso seleccionado.`);
}

// Asegurar que las funciones sean accesibles globalmente
window.cargarTotalCursos = cargarTotalCursos;
window.cargarCursosMasParticipantes = cargarCursosMasParticipantes;
window.cargarCursoMasRequerido = cargarCursoMasRequerido;
window.actualizarGraficaCursos = actualizarGraficaCursos;
window.actualizarPeriodoAnalisis = actualizarPeriodoAnalisis;
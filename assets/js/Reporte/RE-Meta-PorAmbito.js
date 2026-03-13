// ========== REPORTE INDEPENDIENTE: META POR ÁMBITO ==========
// Archivo: RE-Meta-PorAmbito.js
// Función: cargarMetaPorAmbito (ID: 11)

// Variables locales específicas de este reporte
let metaPorAmbitoChart = null;

// Función principal del reporte
function cargarMetaPorAmbito() {
    const contentDiv = document.getElementById('reportContent');
    const currentCategoryId = 4; // Metas
    const categoriasReportes = [
        { id: 4, nombre: 'Metas', color: 'danger', icono: 'bi-bullseye' }
    ];
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId);
    
    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0"><i class="bi bi-building me-2"></i>Meta por Ámbito</h4>
                <p class="mb-0 mt-2 small opacity-75">Reporte de metas establecidas por cada ámbito de formación</p>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="selectAmbitoMeta" class="form-label">Seleccionar Ámbito:</label>
                                <select id="selectAmbitoMeta" class="form-select">
                                    <option value="todos">Todos los Ámbitos</option>
                                    <option value="Centro de Formacion Socialista">Centro de Formación Socialista</option>
                                    <option value="Comunas">Comunas</option>
                                    <option value="Instalaciones Militares">Instalaciones Militares</option>
                                    <option value="Unidades educativas">Unidades Educativas</option>
                                    <option value="Sistema de Misiones y Grandes Misiones">Sistema de Misiones</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="selectEstadoMeta" class="form-label">Estado de la Meta:</label>
                                <select id="selectEstadoMeta" class="form-select">
                                    <option value="todos">Todos los Estados</option>
                                    <option value="activa">Activa</option>
                                    <option value="completada">Completada</option>
                                    <option value="inconclusa">Inconclusa</option>
                                    <option value="en espera">En espera</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltrosMetaAmbito" class="btn btn-${categoria.color} w-100" style="max-width: 300px;">
                            <i class="bi bi-filter me-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
                
                <div class="text-center py-5" id="loadingMetaPorAmbito">
                    <div class="spinner-border text-${categoria.color}" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando datos de metas por ámbito...</p>
                </div>
                <div id="contenidoMetaPorAmbito" style="display: none;"></div>
            </div>
        </div>
    `;
    
    // Event listeners
    document.getElementById('btnAplicarFiltrosMetaAmbito')?.addEventListener('click', function() {
        const ambito = document.getElementById('selectAmbitoMeta').value;
        const estado = document.getElementById('selectEstadoMeta').value;
        cargarDatosMetaPorAmbito(ambito, estado);
    });
    
    // Cargar datos iniciales
    cargarDatosMetaPorAmbito('todos', 'todos');
}

// Función para cargar datos del reporte
function cargarDatosMetaPorAmbito(ambitoFiltro, estadoFiltro) {
    const loadingElement = document.getElementById('loadingMetaPorAmbito');
    const contenidoElement = document.getElementById('contenidoMetaPorAmbito');
    
    // Simular carga de datos
    setTimeout(() => {
        mostrarMetaPorAmbito(ambitoFiltro, estadoFiltro);
        
        loadingElement.style.display = 'none';
        contenidoElement.style.display = 'block';
    }, 1200);
}

// Función para mostrar los datos del reporte
function mostrarMetaPorAmbito(ambitoFiltro, estadoFiltro) {
    const contenidoElement = document.getElementById('contenidoMetaPorAmbito');
    
    // Datos simulados basados en la estructura de la base de datos
    const metasData = [
        {
            id: 1,
            nombre: "Formación en Programación 2024",
            ambito: "Centro de Formacion Socialista",
            cantidadParticipantes: 150,
            participantesActuales: 142,
            porcentajeCompletado: 94.7,
            fechaCreacion: "2024-01-15",
            fechaLimite: "2024-12-31",
            estado: "completada",
            cursosAsociados: ["Python Básico", "Java Intermedio", "Desarrollo Web"],
            instructorResponsable: "Juan Pérez",
            areaFormativa: "Tecnología electrónica"
        },
        {
            id: 2,
            nombre: "Capacitación Comunitaria",
            ambito: "Comunas",
            cantidadParticipantes: 200,
            participantesActuales: 175,
            porcentajeCompletado: 87.5,
            fechaCreacion: "2024-03-10",
            fechaLimite: "2024-11-30",
            estado: "activa",
            cursosAsociados: ["Primeros Auxilios", "Liderazgo Comunitario", "Gestión de Proyectos"],
            instructorResponsable: "María García",
            areaFormativa: "Planificación y formulación de proyectos"
        },
        {
            id: 3,
            nombre: "Formación Militar Técnica",
            ambito: "Instalaciones Militares",
            cantidadParticipantes: 80,
            participantesActuales: 65,
            porcentajeCompletado: 81.3,
            fechaCreacion: "2024-02-20",
            fechaLimite: "2024-10-15",
            estado: "activa",
            cursosAsociados: ["Comunicaciones", "Electrónica Básica", "Mantenimiento de Equipos"],
            instructorResponsable: "Carlos Rodríguez",
            areaFormativa: "Seguridad pública"
        },
        {
            id: 4,
            nombre: "Educación Tecnológica Escolar",
            ambito: "Unidades educativas",
            cantidadParticipantes: 300,
            participantesActuales: 210,
            porcentajeCompletado: 70.0,
            fechaCreacion: "2024-04-05",
            fechaLimite: "2024-12-20",
            estado: "activa",
            cursosAsociados: ["Informática Básica", "Robótica Educativa", "Programación para Niños"],
            instructorResponsable: "Ana López",
            areaFormativa: "Tecnología electrónica"
        },
        {
            id: 5,
            nombre: "Misión Agroalimentaria",
            ambito: "Sistema de Misiones y Grandes Misiones",
            cantidadParticipantes: 500,
            participantesActuales: 380,
            porcentajeCompletado: 76.0,
            fechaCreacion: "2024-01-30",
            fechaLimite: "2024-12-15",
            estado: "activa",
            cursosAsociados: ["Agricultura Sostenible", "Procesamiento de Alimentos", "Marketing Agropecuario"],
            instructorResponsable: "Pedro Martínez",
            areaFormativa: "Alimentos y bebidas"
        },
        {
            id: 6,
            nombre: "Formación en Confecciones 2023",
            ambito: "Centro de Formacion Socialista",
            cantidadParticipantes: 120,
            participantesActuales: 120,
            porcentajeCompletado: 100,
            fechaCreacion: "2023-08-15",
            fechaLimite: "2023-12-10",
            estado: "completada",
            cursosAsociados: ["Costura Básica", "Diseño de Modas", "Patronaje Industrial"],
            instructorResponsable: "Laura Fernández",
            areaFormativa: "Confecciones"
        },
        {
            id: 7,
            nombre: "Capacitación Forestal",
            ambito: "Comunas",
            cantidadParticipantes: 90,
            participantesActuales: 45,
            porcentajeCompletado: 50.0,
            fechaCreacion: "2024-06-01",
            fechaLimite: "2024-09-30",
            estado: "inconclusa",
            cursosAsociados: ["Manejo Forestal", "Conservación Ambiental", "Viveros Forestales"],
            instructorResponsable: "Jorge Bringtown",
            areaFormativa: "Forestal"
        }
    ];
    
    // Filtrar datos según los filtros
    let metasFiltradas = metasData;
    
    if (ambitoFiltro !== 'todos') {
        metasFiltradas = metasFiltradas.filter(meta => meta.ambito === ambitoFiltro);
    }
    
    if (estadoFiltro !== 'todos') {
        metasFiltradas = metasFiltradas.filter(meta => meta.estado === estadoFiltro);
    }
    
    // Calcular estadísticas generales
    const totalMetas = metasFiltradas.length;
    const totalParticipantesMeta = metasFiltradas.reduce((sum, meta) => sum + meta.cantidadParticipantes, 0);
    const totalParticipantesActual = metasFiltradas.reduce((sum, meta) => sum + meta.participantesActuales, 0);
    const promedioCumplimiento = totalMetas > 0 ? 
        (metasFiltradas.reduce((sum, meta) => sum + meta.porcentajeCompletado, 0) / totalMetas).toFixed(1) : 0;
    
    // Agrupar por ámbito para estadísticas
    const metasPorAmbito = {};
    metasFiltradas.forEach(meta => {
        if (!metasPorAmbito[meta.ambito]) {
            metasPorAmbito[meta.ambito] = {
                count: 0,
                totalParticipantes: 0,
                participantesActuales: 0,
                porcentajeTotal: 0
            };
        }
        metasPorAmbito[meta.ambito].count++;
        metasPorAmbito[meta.ambito].totalParticipantes += meta.cantidadParticipantes;
        metasPorAmbito[meta.ambito].participantesActuales += meta.participantesActuales;
    });
    
    // Calcular porcentaje por ámbito
    Object.keys(metasPorAmbito).forEach(ambito => {
        const data = metasPorAmbito[ambito];
        data.porcentajeTotal = (data.participantesActuales / data.totalParticipantes * 100).toFixed(1);
    });
    
    contenidoElement.innerHTML = `
        <!-- Resumen estadístico -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-danger">${totalMetas}</h1>
                        <p class="lead">Total de Metas</p>
                        <small class="text-muted">${ambitoFiltro === 'todos' ? 'Todos los ámbitos' : `Ámbito: ${ambitoFiltro}`}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-primary">${totalParticipantesMeta}</h1>
                        <p class="lead">Participantes Meta</p>
                        <small class="text-muted">Total programado</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-success">${totalParticipantesActual}</h1>
                        <p class="lead">Participantes Actuales</p>
                        <small class="text-muted">Total alcanzado</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-1 text-warning">${promedioCumplimiento}%</h1>
                        <p class="lead">Cumplimiento Prom.</p>
                        <small class="text-muted">Porcentaje promedio</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráfica de distribución por ámbito -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Distribución de Metas por Ámbito</h5>
                        <div style="height: 300px; width: 100%;">
                            <canvas id="graficaMetasPorAmbito"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de metas -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Detalle de Metas por Ámbito</h5>
                        <p class="text-muted mb-4">Mostrando ${metasFiltradas.length} metas</p>
                        
                        <div class="accordion" id="accordionMetas">
                            ${metasFiltradas.map((meta, index) => `
                                <div class="accordion-item mb-3 border-0 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMeta${meta.id}" 
                                                aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapseMeta${meta.id}">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div>
                                                    <span class="badge bg-danger me-2">${meta.ambito}</span>
                                                    <strong>${meta.nombre}</strong>
                                                </div>
                                                <div>
                                                    <span class="badge ${
                                                        meta.estado === 'completada' ? 'bg-success' :
                                                        meta.estado === 'activa' ? 'bg-primary' :
                                                        meta.estado === 'inconclusa' ? 'bg-warning' : 'bg-secondary'
                                                    } me-2">
                                                        ${meta.estado}
                                                    </span>
                                                    <span class="badge bg-info">
                                                        ${meta.porcentajeCompletado}% cumplido
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseMeta${meta.id}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                                         data-bs-parent="#accordionMetas">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6>Información General</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Participantes Meta:</strong> ${meta.cantidadParticipantes}</p>
                                                            <p><strong>Participantes Actuales:</strong> ${meta.participantesActuales}</p>
                                                            <p><strong>Porcentaje Completado:</strong> ${meta.porcentajeCompletado}%</p>
                                                            <p><strong>Área Formativa:</strong> ${meta.areaFormativa}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Fecha Creación:</strong> ${meta.fechaCreacion}</p>
                                                            <p><strong>Fecha Límite:</strong> ${meta.fechaLimite}</p>
                                                            <p><strong>Instructor Responsable:</strong> ${meta.instructorResponsable}</p>
                                                            <p><strong>Estado:</strong> 
                                                                <span class="badge ${
                                                                    meta.estado === 'completada' ? 'bg-success' :
                                                                    meta.estado === 'activa' ? 'bg-primary' :
                                                                    meta.estado === 'inconclusa' ? 'bg-warning' : 'bg-secondary'
                                                                }">
                                                                    ${meta.estado}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <h6 class="mt-3">Cursos Asociados</h6>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        ${meta.cursosAsociados.map(curso => `
                                                            <span class="badge bg-light text-dark border">${curso}</span>
                                                        `).join('')}
                                                    </div>
                                                    
                                                    <h6 class="mt-3">Progreso</h6>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar ${
                                                            meta.porcentajeCompletado >= 100 ? 'bg-success' :
                                                            meta.porcentajeCompletado >= 75 ? 'bg-primary' :
                                                            meta.porcentajeCompletado >= 50 ? 'bg-warning' : 'bg-danger'
                                                        }" 
                                                        style="width: ${meta.porcentajeCompletado}%">
                                                            <strong>${meta.porcentajeCompletado}%</strong>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <small>0 participantes</small>
                                                        <small>${meta.participantesActuales} de ${meta.cantidadParticipantes}</small>
                                                        <small>${meta.cantidadParticipantes} participantes</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Estadísticas</h6>
                                                    <div class="card border-0 bg-light">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Porcentaje Completado:</span>
                                                                <span class="fw-bold">${meta.porcentajeCompletado}%</span>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Participantes Faltantes:</span>
                                                                <span class="fw-bold">${meta.cantidadParticipantes - meta.participantesActuales}</span>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Tasa de Avance:</span>
                                                                <span class="fw-bold">
                                                                    ${
                                                                        ((meta.participantesActuales / meta.cantidadParticipantes) * 100).toFixed(1)
                                                                    }%
                                                                </span>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Días Restantes:</span>
                                                                <span class="fw-bold">${Math.floor(Math.random() * 100) + 10}</span>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span>Cursos Asociados:</span>
                                                                <span class="fw-bold">${meta.cursosAsociados.length}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <button class="btn btn-sm btn-outline-danger w-100" onclick="verDetalleMeta(${meta.id})">
                                                            <i class="bi bi-eye me-2"></i>Ver Detalles Completos
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
        
        <!-- Resumen por estado -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Resumen por Estado de Meta</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3">
                                    <h1 class="display-6 text-success">${metasFiltradas.filter(m => m.estado === 'completada').length}</h1>
                                    <p class="mb-0">Completadas</p>
                                    <small class="text-muted">100% cumplidas</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3">
                                    <h1 class="display-6 text-primary">${metasFiltradas.filter(m => m.estado === 'activa').length}</h1>
                                    <p class="mb-0">Activas</p>
                                    <small class="text-muted">En progreso</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3">
                                    <h1 class="display-6 text-warning">${metasFiltradas.filter(m => m.estado === 'inconclusa').length}</h1>
                                    <p class="mb-0">Inconclusas</p>
                                    <small class="text-muted">Por debajo del 50%</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3">
                                    <h1 class="display-6 text-secondary">${metasFiltradas.filter(m => m.estado === 'en espera').length}</h1>
                                    <p class="mb-0">En espera</p>
                                    <small class="text-muted">Sin iniciar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Crear gráfica de distribución por ámbito
    crearGraficaMetasPorAmbito(metasPorAmbito);
    
    // Inicializar accordion
    setTimeout(() => {
        const accordionElements = document.querySelectorAll('.accordion-button');
        accordionElements.forEach(element => {
            element.addEventListener('click', function() {
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

// Función para crear gráfica de distribución por ámbito
function crearGraficaMetasPorAmbito(metasPorAmbito) {
    const ctx = document.getElementById('graficaMetasPorAmbito')?.getContext('2d');
    if (!ctx) return;
    
    // Destruir gráfico anterior si existe
    if (metaPorAmbitoChart) {
        metaPorAmbitoChart.destroy();
    }
    
    const labels = Object.keys(metasPorAmbito);
    const data = Object.values(metasPorAmbito).map(item => item.count);
    
    // Colores para los ámbitos
    const backgroundColors = [
        'rgba(220, 53, 69, 0.8)',    // Rojo
        'rgba(13, 110, 253, 0.8)',   // Azul
        'rgba(25, 135, 84, 0.8)',    // Verde
        'rgba(255, 193, 7, 0.8)',    // Amarillo
        'rgba(108, 117, 125, 0.8)'   // Gris
    ];
    
    metaPorAmbitoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de Metas',
                data: data,
                backgroundColor: backgroundColors.slice(0, labels.length),
                borderColor: backgroundColors.map(color => color.replace('0.8', '1')).slice(0, labels.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Cantidad de Metas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Ámbito'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const ambito = context.label;
                            const item = metasPorAmbito[ambito];
                            return `Participantes: ${item.participantesActuales}/${item.totalParticipantes}`;
                        }
                    }
                }
            }
        }
    });
}

// Función para ver detalle de meta
function verDetalleMeta(idMeta) {
    alert(`Meta por Ámbito - Detalle de la meta #${idMeta}\n\nEsta funcionalidad mostraría un reporte detallado de la meta seleccionada.`);
}

// Función de limpieza para recursos
function limpiarMetaPorAmbito() {
    if (metaPorAmbitoChart) {
        metaPorAmbitoChart.destroy();
        metaPorAmbitoChart = null;
    }
}

// Registrar la función principal en el objeto window
window.cargarMetaPorAmbito = cargarMetaPorAmbito;
window.limpiarMetaPorAmbito = limpiarMetaPorAmbito;
console.log('RE-Meta-PorAmbito.js cargado - Función: cargarMetaPorAmbito');
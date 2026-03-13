<!-- Modal de Visualización de Metas - Header Mejorado -->
<div class="modal fade" id="modal-viewMeta" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-viewMeta" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 overflow-hidden">

            <!-- HEADER SÓLIDO CON GRADIENTE AZUL PROFUNDO -->
            <div class="modal-header border-0 p-4" id="modalHeader" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center gap-3 w-100">
                    <!-- Icono circular con fondo sólido -->
                    <div class="status-icon-wrapper" id="headerStatusIcon" style="background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.3);">
                        <i class="bi bi-flag-fill fs-2 text-white"></i>
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="modal-title fw-bold text-white mb-0" style="font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Meta</h4>
                                <span class="badge px-3 py-1 rounded-pill" id="headerIdBadge" style="background: rgba(255, 255, 255, 0.2); color: white; font-weight: 500; border: 1px solid rgba(255,255,255,0.1);">#000</span>
                            </div>
                            <span class="badge px-3 py-1 rounded-pill" id="headerTipoBadge" style="background: rgba(255, 255, 255, 0.2); color: white; font-weight: 500; border: 1px solid rgba(255,255,255,0.1);">-</span>
                        </div>
                        
                        <!-- Barra de progreso mejorada -->
                        <div class="header-progress-container">
                            <div class="progress mb-2" style="height: 8px; background: rgba(0, 0, 0, 0.2); border-radius: 10px;">
                                <div class="progress-bar" id="headerProgressBar" style="width: 0%; background: linear-gradient(90deg, #f6d365 0%, #fda085 100%); border-radius: 10px;"></div>
                            </div>
                            <div class="d-flex justify-content-between text-white small">
                                <span class="d-flex align-items-center gap-1"><i class="bi bi-flag-fill"></i> <strong id="headerMetaObjetivo">0</strong></span>
                                <span class="d-flex align-items-center gap-1"><i class="bi bi-people-fill"></i> <strong id="headerEstudiantesFormados">0</strong></span>
                                <span class="d-flex align-items-center gap-1"><i class="bi bi-percent"></i> <strong id="headerPorcentaje">0%</strong></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar" style="opacity: 0.8;"></button>
                </div>
            </div>

            <!-- BODY CON ORGANIZACIÓN MEJORADA (se mantiene igual) -->
            <div class="modal-body p-4">
                <!-- ALERTAS DE INFORMACIÓN IMPORTANTE -->
                <div class="alert-container mb-4" id="alertContainer"></div>

                <!-- SECCIÓN 1: PROGRESO Y PRONÓSTICO (VISUAL) -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-semibold mb-0">
                                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Progreso actual
                                    </h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill" id="porcentajeSimplificado">0%</span>
                                </div>
                                
                                <!-- Gráfico tipo dona -->
                                <div class="text-center mb-2">
                                    <div class="progress-ring mx-auto" style="position: relative; width: 140px; height: 140px;">
                                        <canvas id="metaProgressChart" width="140" height="140"></canvas>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <span class="fs-3 fw-bold" id="chartPercentage">0%</span>
                                            <span class="d-block small text-secondary">completado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Pronóstico
                                </h6>
                                
                                <!-- PREDICTOR DE CUMPLIMIENTO -->
                                <div class="predictor-card p-3 bg-light rounded-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-secondary">Proyección</span>
                                        <span class="badge" id="predictorBadge">-</span>
                                    </div>
                                    <div class="fs-4 fw-bold mb-2" id="predictorDias">-</div>
                                    <small class="text-secondary d-block" id="predictorTexto">-</small>
                                </div>
                                
                                <!-- MINI COMPARACIÓN -->
                                <div class="comparison-mini p-2 bg-light rounded-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-secondary">Meta:</small>
                                        <span class="fw-semibold" id="view_meta_objetivo">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-secondary">Formados:</small>
                                        <span class="fw-semibold" id="view_estudiantes_formados">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1 border-top">
                                        <small class="text-secondary">Diferencia:</small>
                                        <span class="fw-semibold" id="diferenciaMeta">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: INFORMACIÓN CLAVE EN 3 COLUMNAS -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="info-icon-small bg-primary bg-opacity-10 rounded-3 p-2">
                                        <i class="bi bi-info-circle text-primary"></i>
                                    </div>
                                    <small class="text-secondary">Tipo</small>
                                </div>
                                <span class="fw-semibold fs-6" id="view_tipo_objetivo">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="info-icon-small bg-info bg-opacity-10 rounded-3 p-2">
                                        <i class="bi bi-diagram-3 text-info"></i>
                                    </div>
                                    <small class="text-secondary">Ámbito</small>
                                </div>
                                <span class="fw-semibold fs-6" id="view_ambito">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="info-icon-small bg-success bg-opacity-10 rounded-3 p-2">
                                        <i class="bi bi-calendar text-success"></i>
                                    </div>
                                    <small class="text-secondary">Creado</small>
                                </div>
                                <span class="fw-semibold fs-6" id="view_created_at">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: CRONOGRAMA Y ESTADOS (2 COLUMNAS) -->
                <div class="row g-4">
                    <!-- Columna izquierda: Cronograma -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-calendar-range me-2 text-primary"></i>Cronograma
                                </h6>
                                
                                <!-- Fechas en formato compacto -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-center flex-grow-1">
                                        <small class="text-secondary d-block">Inicio</small>
                                        <span class="fw-semibold" id="view_fecha_inicio">-</span>
                                    </div>
                                    <div class="px-3">
                                        <i class="bi bi-arrow-right text-secondary"></i>
                                    </div>
                                    <div class="text-center flex-grow-1">
                                        <small class="text-secondary d-block">Fin</small>
                                        <span class="fw-semibold" id="view_fecha_fin">-</span>
                                    </div>
                                </div>
                                
                                <!-- Barra de progreso temporal -->
                                <div class="timeline-visual mb-2">
                                    <div class="progress timeline-progress mb-2" style="height: 8px;">
                                        <div class="progress-bar" id="timeProgressBar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-center">
                                        <span class="fw-semibold small" id="timeProgressText">-</span>
                                    </div>
                                </div>

                                <!-- Indicador de días -->
                                <div class="days-indicator p-2 rounded-3 text-center mt-2" id="daysIndicator"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Estados -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-traffic-light me-2 text-primary"></i>Estados
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="status-compact p-3 bg-light rounded-3 text-center">
                                            <div class="status-led mb-2 mx-auto" id="estadoLed"></div>
                                            <small class="text-secondary d-block mb-1">General</small>
                                            <div id="view_estado"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="status-compact p-3 bg-light rounded-3 text-center">
                                            <div class="status-led mb-2 mx-auto" id="cumplimientoLed"></div>
                                            <small class="text-secondary d-block mb-1">Cumplimiento</small>
                                            <div id="view_estado_cumplimiento"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER CON ACCIONES CLARAS (se mantiene igual) -->
            <div class="modal-footer border-0 p-3 bg-light">
                <div class="d-flex justify-content-between w-100">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3" id="quickViewParticipants">
                            <i class="bi bi-people me-1"></i>
                            <span class="badge bg-primary ms-1" id="participantCount">0</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" id="quickExport">
                            <i class="bi bi-download me-1"></i>Exportar
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-2"></i>Cerrar
                        </button>
                        <button type="button" class="btn btn-primary px-4" id="btnEditarMeta">
                            <i class="bi bi-pencil-square me-2"></i>Editar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* ===== ESTILOS ORGANIZADOS (se mantienen igual pero con header mejorado) ===== */

/* Header con gradiente azul sólido - ya no es dinámico por estado */
#modalHeader {
    /* El gradiente ahora es fijo y más sólido */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

/* Icono de estado - más sólido */
.status-icon-wrapper {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Barra de progreso del header - más visible */
.header-progress {
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
}

.header-progress .progress-bar {
    box-shadow: 0 0 10px rgba(253, 160, 133, 0.5);
}

/* Textos del header - más legibles */
#modalHeader .small {
    font-size: 0.85rem;
    font-weight: 400;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

#modalHeader strong {
    font-size: 1.1rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Badges del header - más visibles */
#modalHeader .badge {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    backdrop-filter: blur(5px);
}

/* Predictor card */
.predictor-card {
    border-left: 4px solid #0d6efd;
    transition: all 0.3s ease;
}

.predictor-card.alerta { border-left-color: #dc3545; }
.predictor-card.exito { border-left-color: #198754; }
.predictor-card.atencion { border-left-color: #ffc107; }

/* Timeline visual */
.timeline-visual {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 12px;
}

.timeline-progress {
    background: #e9ecef;
}

.timeline-progress .progress-bar {
    background: linear-gradient(90deg, #0d6efd, #0b5ed7);
}

/* Indicador de días */
.days-indicator {
    font-size: 0.9rem;
    font-weight: 500;
}

.days-indicator.positivo {
    background: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.days-indicator.negativo {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.days-indicator.neutro {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

/* LEDs de estado */
.status-led {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-led.verde {
    background: #198754;
    box-shadow: 0 0 10px #198754;
}

.status-led.amarillo {
    background: #ffc107;
    box-shadow: 0 0 10px #ffc107;
}

.status-led.rojo {
    background: #dc3545;
    box-shadow: 0 0 10px #dc3545;
}

.status-led.azul {
    background: #0dcaf0;
    box-shadow: 0 0 10px #0dcaf0;
}

.status-led.gris {
    background: #6c757d;
    box-shadow: 0 0 10px #6c757d;
}

@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}

/* Status compact */
.status-compact {
    transition: all 0.3s ease;
}

.status-compact:hover {
    background: white !important;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

/* Badges de estado */
.badge-estado-verde,
.badge-estado-amarillo,
.badge-estado-rojo,
.badge-estado-azul,
.badge-estado-gris {
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-estado-verde { background: rgba(25, 135, 84, 0.15); color: #198754; }
.badge-estado-amarillo { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
.badge-estado-rojo { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
.badge-estado-azul { background: rgba(13, 202, 240, 0.15); color: #0dcaf0; }
.badge-estado-gris { background: rgba(108, 117, 125, 0.15); color: #6c757d; }

/* Info icon small */
.info-icon-small {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Comparison mini */
.comparison-mini {
    font-size: 0.9rem;
}

/* Loading overlay */
.export-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999;
}

.export-loading-content {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    max-width: 300px;
}

.export-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #0d6efd;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-body {
        padding: 1rem;
    }
    
    .status-icon-wrapper {
        width: 44px;
        height: 44px;
    }
    
    .status-icon-wrapper i {
        font-size: 1.3rem;
    }
    
    #modalHeader strong {
        font-size: 1rem;
    }
}
</style>

<script>
// ==================== VARIABLES GLOBALES ====================
let metaProgressChart = null;

// ==================== FUNCIONES DE UTILIDAD ====================

function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

function formatDateTime(dateTimeString) {
    if (!dateTimeString) return '-';
    try {
        const date = new Date(dateTimeString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (e) {
        return dateTimeString;
    }
}

function getColorByPercentage(percentage) {
    percentage = parseFloat(percentage) || 0;
    if (percentage < 30) return 'rojo';
    if (percentage < 70) return 'amarillo';
    return 'verde';
}

function getColorByEstado(estado) {
    const estadoLower = (estado || '').toLowerCase();
    if (estadoLower.includes('activa') || estadoLower.includes('cumplida')) return 'verde';
    if (estadoLower.includes('pendiente') || estadoLower.includes('progreso')) return 'amarillo';
    if (estadoLower.includes('cerrada') || estadoLower.includes('no cumplida')) return 'rojo';
    if (estadoLower.includes('espera')) return 'azul';
    return 'gris';
}

function getHeaderIcon(data) {
    const porcentaje = parseFloat(data.porcentaje_cumplimiento) || 0;
    if (porcentaje >= 100) return '<i class="bi bi-trophy-fill fs-2 text-white"></i>';
    if (porcentaje >= 80) return '<i class="bi bi-star-fill fs-2 text-white"></i>';
    if (porcentaje >= 50) return '<i class="bi bi-arrow-up-circle-fill fs-2 text-white"></i>';
    if (porcentaje >= 20) return '<i class="bi bi-play-circle-fill fs-2 text-white"></i>';
    return '<i class="bi bi-flag-fill fs-2 text-white"></i>';
}

function getEstadoBadge(estado, tipo = 'estado') {
    let badgeClass = 'gris';
    let icon = 'bi-question-circle';
    let texto = estado || 'Sin estado';
    
    const estadoLower = (estado || '').toLowerCase();
    
    if (tipo === 'cumplimiento') {
        if (estadoLower.includes('cumplida') || estadoLower.includes('completado')) {
            badgeClass = 'verde';
            icon = 'bi-check-circle';
        } else if (estadoLower.includes('progreso') || estadoLower.includes('curso')) {
            badgeClass = 'amarillo';
            icon = 'bi-arrow-clockwise';
        } else if (estadoLower.includes('pendiente')) {
            badgeClass = 'azul';
            icon = 'bi-clock';
        } else if (estadoLower.includes('no cumplida') || estadoLower.includes('atrasada')) {
            badgeClass = 'rojo';
            icon = 'bi-x-circle';
        }
    } else {
        if (estadoLower.includes('activa')) {
            badgeClass = 'verde';
            icon = 'bi-play-circle';
        } else if (estadoLower.includes('cerrada')) {
            badgeClass = 'rojo';
            icon = 'bi-pause-circle';
        } else if (estadoLower.includes('pendiente')) {
            badgeClass = 'amarillo';
            icon = 'bi-clock';
        }
    }
    
    return `<span class="badge-estado-${badgeClass}">
                <i class="bi ${icon} me-1"></i>${texto}
            </span>`;
}

// ==================== FUNCIONES DEL GRÁFICO ====================

function createDonaChart(data) {
    try {
        const ctx = document.getElementById('metaProgressChart').getContext('2d');
        const porcentaje = parseFloat(data.porcentaje_cumplimiento) || 0;
        
        let chartColor = '#0d6efd';
        const colorType = getColorByPercentage(porcentaje);
        if (colorType === 'rojo') chartColor = '#dc3545';
        if (colorType === 'amarillo') chartColor = '#ffc107';
        if (colorType === 'verde') chartColor = '#198754';
        
        if (metaProgressChart) metaProgressChart.destroy();
        
        metaProgressChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [porcentaje, 100 - porcentaje],
                    backgroundColor: [chartColor, '#e9ecef'],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
        
    } catch (error) {
        console.error('Error en gráfico:', error);
    }
}

// ==================== FUNCIONES DE PREDICCIÓN ====================

function calcularPredictor(data) {
    if (!data.fecha_inicio || !data.fecha_fin || !data.meta_objetivo || !data.estudiantes_formados) {
        document.getElementById('predictorDias').textContent = '-';
        document.getElementById('predictorBadge').textContent = 'Sin datos';
        document.getElementById('predictorTexto').textContent = 'Complete los datos para ver pronóstico';
        return;
    }
    
    const inicio = new Date(data.fecha_inicio);
    const fin = new Date(data.fecha_fin);
    const hoy = new Date();
    const totalDias = (fin - inicio) / (1000 * 60 * 60 * 24);
    const diasTranscurridos = (hoy - inicio) / (1000 * 60 * 60 * 24);
    const metaObjetivo = parseFloat(data.meta_objetivo) || 0;
    const formados = parseFloat(data.estudiantes_formados) || 0;
    
    let badgeClass = 'bg-secondary';
    let badgeTexto = '';
    let diasTexto = '';
    
    if (hoy < inicio) {
        const diasFaltantes = Math.ceil((inicio - hoy) / (1000 * 60 * 60 * 24));
        diasTexto = `Inicia en ${diasFaltantes} días`;
        badgeClass = 'bg-info';
        badgeTexto = 'POR INICIAR';
    } else if (hoy > fin) {
        if (formados >= metaObjetivo) {
            diasTexto = '¡Meta cumplida!';
            badgeClass = 'bg-success';
            badgeTexto = 'CUMPLIDA';
        } else {
            const faltante = metaObjetivo - formados;
            diasTexto = `Finalizó (faltaron ${faltante})`;
            badgeClass = 'bg-danger';
            badgeTexto = 'NO CUMPLIDA';
        }
    } else {
        const ritmoActual = diasTranscurridos > 0 ? formados / diasTranscurridos : 0;
        const diasRestantes = totalDias - diasTranscurridos;
        const proyeccion = Math.round(formados + (ritmoActual * diasRestantes));
        const diferencia = proyeccion - metaObjetivo;
        
        if (diferencia >= 0) {
            diasTexto = `${proyeccion} (${Math.abs(diferencia)} sobre)`;
            badgeClass = 'bg-success';
            badgeTexto = 'ENCAMINADA';
        } else if (diferencia > -10) {
            diasTexto = `${proyeccion} (faltan ${Math.abs(diferencia)})`;
            badgeClass = 'bg-warning';
            badgeTexto = 'CON RIESGO';
        } else {
            diasTexto = `${proyeccion} (faltan ${Math.abs(diferencia)})`;
            badgeClass = 'bg-danger';
            badgeTexto = 'CRÍTICA';
        }
    }
    
    document.getElementById('predictorDias').textContent = diasTexto;
    document.getElementById('predictorBadge').textContent = badgeTexto;
    document.getElementById('predictorBadge').className = `badge ${badgeClass}`;
    document.getElementById('predictorTexto').textContent = badgeTexto === 'CUMPLIDA' ? '¡Felicidades!' : 
                                                             badgeTexto === 'ENCAMINADA' ? 'Vas bien' :
                                                             badgeTexto === 'CON RIESGO' ? 'Acelera' :
                                                             badgeTexto === 'CRÍTICA' ? 'Acción requerida' :
                                                             badgeTexto === 'POR INICIAR' ? 'Prepara estrategias' : 
                                                             'Revisa datos';
    
    const predictorCard = document.querySelector('.predictor-card');
    predictorCard.classList.remove('alerta', 'exito', 'atencion');
    if (badgeClass.includes('success')) predictorCard.classList.add('exito');
    else if (badgeClass.includes('warning')) predictorCard.classList.add('atencion');
    else if (badgeClass.includes('danger')) predictorCard.classList.add('alerta');
}

// ==================== FUNCIÓN PRINCIPAL ====================

function loadMetaData(data) {
    try {
        console.log('Cargando datos:', data);
        
        // Elementos básicos
        const elements = {
            'view_id_meta': data.id_meta || '-',
            'view_meta_objetivo': data.meta_objetivo || '0',
            'view_tipo_objetivo': data.tipo_objetivo || '-',
            'view_ambito': data.ambito_nombre || data.nombre_ambito || data.ambito || '-',
            'view_estudiantes_formados': data.estudiantes_formados || '0',
            'view_fecha_inicio': formatDate(data.fecha_inicio) || '-',
            'view_fecha_fin': formatDate(data.fecha_fin) || '-',
            'view_porcentaje_cumplimiento': (data.porcentaje_cumplimiento || '0') + '%',
            'view_created_at': formatDateTime(data.created_at) || '-',
            'chartPercentage': (data.porcentaje_cumplimiento || '0') + '%',
            'porcentajeSimplificado': (data.porcentaje_cumplimiento || '0') + '%',
            'headerIdBadge': '#' + (data.id_meta || '000'),
            'headerTipoBadge': data.tipo_objetivo || '-',
            'headerMetaObjetivo': data.meta_objetivo || '0',
            'headerEstudiantesFormados': data.estudiantes_formados || '0',
            'headerPorcentaje': (data.porcentaje_cumplimiento || '0') + '%',
            'participantCount': data.estudiantes_formados || '0'
        };
        
        Object.keys(elements).forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = elements[id];
        });
        
        // Badges
        document.getElementById('view_estado').innerHTML = getEstadoBadge(data.estado);
        document.getElementById('view_estado_cumplimiento').innerHTML = 
            getEstadoBadge(data.estado_cumplimiento || 'No definido', 'cumplimiento');
        
        // LEDs de estado (ahora solo para el body, el header es fijo)
        const estadoColor = getColorByEstado(data.estado);
        const cumplimientoColor = getColorByEstado(data.estado_cumplimiento) || getColorByPercentage(data.porcentaje_cumplimiento);
        
        document.getElementById('estadoLed').className = `status-led ${estadoColor}`;
        document.getElementById('cumplimientoLed').className = `status-led ${cumplimientoColor}`;
        
        // Icono del header (dinámico según progreso)
        const iconoHeader = document.getElementById('headerStatusIcon');
        iconoHeader.innerHTML = getHeaderIcon(data);
        
        // Barra de progreso del header
        const porcentaje = parseFloat(data.porcentaje_cumplimiento) || 0;
        document.getElementById('headerProgressBar').style.width = `${porcentaje}%`;
        
        // Diferencia
        const diferencia = (data.meta_objetivo || 0) - (data.estudiantes_formados || 0);
        document.getElementById('diferenciaMeta').textContent = diferencia;
        document.getElementById('diferenciaMeta').className = `fw-semibold ${diferencia > 0 ? 'text-warning' : diferencia < 0 ? 'text-success' : 'text-secondary'}`;
        
        // Timeline
        updateTimeProgressBar(data.fecha_inicio, data.fecha_fin);
        
        // Predictor
        calcularPredictor(data);
        
        // Alertas
        mostrarAlertas(data);
        
        // Gráfico
        if (typeof Chart !== 'undefined') createDonaChart(data);
        
        // Guardar datos para exportación
        window._currentMetaData = data;
        
    } catch (error) {
        console.error('Error cargando datos:', error);
    }
}

function mostrarAlertas(data) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    const porcentaje = parseFloat(data.porcentaje_cumplimiento) || 0;
    const metaObjetivo = parseFloat(data.meta_objetivo) || 0;
    const formados = parseFloat(data.estudiantes_formados) || 0;
    const fechaFin = new Date(data.fecha_fin);
    const hoy = new Date();
    
    let alertas = [];
    
    if (fechaFin > hoy) {
        const diasRestantes = Math.ceil((fechaFin - hoy) / (1000 * 60 * 60 * 24));
        if (diasRestantes <= 7 && formados < metaObjetivo) {
            alertas.push({
                tipo: 'warning',
                icon: 'bi-exclamation-triangle',
                mensaje: `¡Solo ${diasRestantes} días para cumplir la meta!`
            });
        }
    }
    
    if (porcentaje < 30 && fechaFin > hoy) {
        alertas.push({
            tipo: 'danger',
            icon: 'bi-graph-down',
            mensaje: 'Progreso bajo. ¡Necesitas acelerar!'
        });
    }
    
    if (formados >= metaObjetivo && metaObjetivo > 0) {
        alertas.push({
            tipo: 'success',
            icon: 'bi-trophy',
            mensaje: '¡Meta cumplida! Felicidades.'
        });
    }
    
    if (fechaFin < hoy && formados < metaObjetivo) {
        alertas.push({
            tipo: 'danger',
            icon: 'bi-clock-history',
            mensaje: 'Meta vencida sin cumplir.'
        });
    }
    
    if (alertas.length > 0) {
        alertContainer.innerHTML = alertas.map(alerta => `
            <div class="alert alert-${alerta.tipo} alert-dismissible fade show d-flex align-items-center py-2" role="alert">
                <i class="bi ${alerta.icon} me-2"></i>
                <span class="small">${alerta.mensaje}</span>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `).join('');
    } else {
        alertContainer.innerHTML = '';
    }
}

function updateTimeProgressBar(fechaInicio, fechaFin) {
    if (!fechaInicio || !fechaFin) return;
    
    try {
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);
        const hoy = new Date();
        
        let porcentaje = 0;
        let texto = '';
        let diasIndicator = '';
        let indicatorClass = '';
        
        if (hoy < inicio) {
            porcentaje = 0;
            const diasFaltantes = Math.ceil((inicio - hoy) / (1000 * 60 * 60 * 24));
            texto = `Comienza en ${diasFaltantes}d`;
            diasIndicator = `Faltan ${diasFaltantes} días para iniciar`;
            indicatorClass = 'neutro';
        } else if (hoy > fin) {
            porcentaje = 100;
            texto = 'Finalizado';
            const diasVencidos = Math.ceil((hoy - fin) / (1000 * 60 * 60 * 24));
            diasIndicator = `Vencido hace ${diasVencidos} días`;
            indicatorClass = 'negativo';
        } else {
            const total = (fin - inicio) / (1000 * 60 * 60 * 24);
            const transcurrido = (hoy - inicio) / (1000 * 60 * 60 * 24);
            porcentaje = (transcurrido / total) * 100;
            const restante = Math.ceil(total - transcurrido);
            texto = `${restante} días`;
            diasIndicator = `${restante} días para finalizar`;
            indicatorClass = 'positivo';
        }
        
        const bar = document.getElementById('timeProgressBar');
        const text = document.getElementById('timeProgressText');
        const daysIndicator = document.getElementById('daysIndicator');
        
        if (bar) bar.style.width = `${porcentaje}%`;
        if (text) text.textContent = texto;
        if (daysIndicator) {
            daysIndicator.textContent = diasIndicator;
            daysIndicator.className = `days-indicator p-2 rounded-3 text-center ${indicatorClass}`;
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}

// ==================== EXPORTACIÓN EXCEL ====================

async function exportMetaReport(metaData) {
    try {
        showExportLoading('Generando reporte...');
        
        const participantes = await fetchParticipants(metaData);
        
        if (!participantes?.length) {
            hideExportLoading();
            alert('No hay participantes');
            return;
        }
        
        if (typeof XLSX === 'undefined') {
            hideExportLoading();
            alert('Error: Librería Excel no disponible');
            return;
        }
        
        const wb = XLSX.utils.book_new();
        
        const summary = [
            ['REPORTE DE META'],
            ['Fecha:', new Date().toLocaleString('es-ES')],
            [],
            ['Meta:', metaData.id_meta],
            ['Objetivo:', metaData.meta_objetivo],
            ['Participantes:', participantes.length],
            ['Porcentaje:', metaData.porcentaje_cumplimiento + '%']
        ];
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(summary), 'Resumen');
        
        const headers = ['ID', 'Cédula', 'Nombre', 'Curso', 'Estado'];
        const rows = participantes.map(p => [
            p.id_participante,
            p.cedula,
            `${p.primer_nombre || ''} ${p.primer_apellido || ''}`.trim(),
            p.nombre_curso || '-',
            p.estatus_participante || '-'
        ]);
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet([headers, ...rows]), 'Participantes');
        
        XLSX.writeFile(wb, `Meta_${metaData.id_meta}_${Date.now()}.xlsx`);
        
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        hideExportLoading();
    }
}

async function fetchParticipants(metaData) {
    try {
        const url = `../apis/get-meta-participants.php?id_meta=${metaData.id_meta}&tipo_objetivo=${encodeURIComponent(metaData.tipo_objetivo || 'Inscritos')}`;
        const response = await fetch(url);
        const result = await response.json();
        return result.participantes || result.data || [];
    } catch (e) {
        console.error('Error en fetchParticipants:', e);
        return [];
    }
}

// ==================== LOADING OVERLAY ====================

function showExportLoading(msg) {
    let overlay = document.getElementById('exportLoadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'exportLoadingOverlay';
        overlay.className = 'export-loading-overlay';
        overlay.innerHTML = `
            <div class="export-loading-content">
                <div class="export-spinner"></div>
                <p class="mb-0">${msg}</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideExportLoading() {
    const overlay = document.getElementById('exportLoadingOverlay');
    if (overlay) overlay.style.display = 'none';
}

// ==================== ACCIONES RÁPIDAS ====================

function initQuickActions() {
    document.getElementById('quickViewParticipants')?.addEventListener('click', () => {
        alert('Funcionalidad: Ver lista de participantes');
    });
    
    document.getElementById('quickExport')?.addEventListener('click', () => {
        if (window._currentMetaData) exportMetaReport(window._currentMetaData);
        else alert('No hay datos para exportar');
    });
}

// ==================== INICIALIZACIÓN ====================

function initModal() {
    const modal = document.getElementById('modal-viewMeta');
    if (!modal) return;
    
    modal.addEventListener('hidden.bs.modal', () => {
        if (metaProgressChart) {
            metaProgressChart.destroy();
            metaProgressChart = null;
        }
    });
    
    modal.addEventListener('shown.bs.modal', () => {
        if (window._metaToShow) {
            loadMetaData(window._metaToShow);
            window._metaToShow = null;
        }
    });
    
    document.getElementById('btnEditarMeta')?.addEventListener('click', () => {
        const data = window._currentMetaData;
        if (!data) return;

        const viewModalEl = document.getElementById('modal-viewMeta');
        const viewModal = bootstrap.Modal.getInstance(viewModalEl);

        const onHidden = () => {
            viewModalEl.removeEventListener('hidden.bs.modal', onHidden);
            if (typeof loadEditMetaData === 'function') {
                loadEditMetaData(data);
            }
            const editModalEl = document.getElementById('modal-editMeta');
            if (editModalEl) {
                new bootstrap.Modal(editModalEl).show();
            }
        };

        viewModalEl.addEventListener('hidden.bs.modal', onHidden);
        if (viewModal) viewModal.hide();
    });
    
    // Botones de exportar (dos posibles IDs para mayor compatibilidad)
    ['quickExport', 'btnExportarReporte'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', () => {
            if (window._currentMetaData) exportMetaReport(window._currentMetaData);
            else alert('No hay datos para exportar');
        });
    });
    
    initQuickActions();
}

window.showMetaModal = (metaData) => {
    window._metaToShow = metaData;
    new bootstrap.Modal(document.getElementById('modal-viewMeta')).show();
};

document.addEventListener('DOMContentLoaded', initModal);
console.log('✅ Modal con Header Mejorado cargado correctamente');
</script>
<!-- Modal de Visualización de Metas - Header Mejorado -->
<div class="modal fade" id="modal-viewMeta" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-viewMeta" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" id="modalDialogMeta">
        <div class="modal-content rounded-4 border-0 overflow-hidden">
            
            <!-- CONTENEDOR DE DETALLES -->
            <div id="viewMetaDetails">

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
                        <button type="button" class="btn btn-outline-secondary px-3" id="quickViewParticipants" title="Ver participantes que alimentan esta meta">
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

            </div> <!-- Fin de viewMetaDetails -->

            <!-- CONTENEDOR DE PREVISUALIZACIÓN (tema claro) -->
            <div id="viewMetaPreview" class="d-none meta-preview-light">
                
                <div class="modal-header border-bottom p-3 px-4 d-flex align-items-center justify-content-between meta-preview-header">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-link text-secondary p-0 text-decoration-none" id="btnBackToMeta" title="Volver a Detalles">
                            <i class="bi bi-arrow-left fs-4"></i>
                        </button>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="previewTitle">¿Qué alimenta esta meta?</h5>
                            <p class="text-secondary mb-0 small" id="previewSubtitle">Meta #00 - Tipo: Aprobados</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-success btn-sm px-3 d-flex align-items-center gap-2" id="btnPreviewExport">
                            <i class="bi bi-file-earmark-excel"></i>
                            <span>Exportar Excel</span>
                        </button>
                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                </div>

                <!-- BODY: Cursos → Participantes → Previsualización (flujo admin) -->
                <div class="modal-body p-0 drive-preview-body">
                    <div class="drive-preview-toolbar px-3 py-2 d-flex align-items-center gap-3 flex-wrap">
                        <div class="input-group drive-search flex-grow-1" style="max-width: 440px;">
                            <span class="input-group-text border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0" id="searchParticipantPreview" placeholder="Buscar curso, participante o cédula...">
                        </div>
                        <div class="drive-preview-stats d-flex gap-2 ms-md-auto flex-wrap">
                            <span class="drive-stat-pill"><i class="bi bi-journal-bookmark-fill me-1"></i><strong id="previewCourseCountBadge">0</strong> cursos</span>
                            <span class="drive-stat-pill"><i class="bi bi-people-fill me-1"></i><strong id="previewCountBadge">0</strong> participantes</span>
                            <span class="drive-stat-pill"><i class="bi bi-flag-fill me-1"></i>Meta: <strong id="previewMetaGoal">0</strong></span>
                        </div>
                    </div>

                    <div class="drive-breadcrumb px-3 py-2" id="previewBreadcrumb">
                        <span class="text-secondary small">Meta</span>
                        <i class="bi bi-chevron-right mx-1 text-secondary" style="font-size: 0.65rem;"></i>
                        <span class="small text-dark fw-medium" id="previewBreadcrumbText">Resumen</span>
                    </div>

                    <div class="drive-preview-layout drive-preview-layout-3col">
                        <!-- 1. Cursos que aportan a la meta -->
                        <aside class="drive-panel drive-courses-panel">
                            <div class="drive-panel-head px-3 py-2">
                                <i class="bi bi-journal-bookmark me-1"></i> Cursos
                            </div>
                            <div class="drive-panel-list" id="previewCourseList" role="listbox" aria-label="Cursos que alimentan la meta"></div>
                        </aside>

                        <!-- 2. Participantes del curso seleccionado -->
                        <aside class="drive-panel drive-students-panel">
                            <div class="drive-panel-head px-3 py-2 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-people me-1"></i> <span id="previewStudentPanelTitle">Participantes</span></span>
                                <span class="badge rounded-pill bg-primary" id="previewStudentPanelCount">0</span>
                            </div>
                            <div class="drive-panel-list" id="previewStudentList" role="listbox" aria-label="Participantes del curso"></div>
                        </aside>

                        <!-- 3. Vista previa (resumen / curso / participante) -->
                        <main class="drive-preview-canvas" id="previewStudentCanvas" aria-live="polite">
                            <div class="drive-canvas-content" id="previewCanvasContent"></div>
                        </main>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 px-4 meta-preview-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" id="btnBackToMetaFooter">
                        <i class="bi bi-arrow-left me-2"></i>Volver a Detalles
                    </button>
                </div>

            </div> <!-- Fin de viewMetaPreview -->

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

/* ===== Previsualización meta — tema claro ===== */
.meta-preview-light {
    background: #f8fafc;
}

.meta-preview-header,
.meta-preview-footer {
    background: #fff;
}

.modal-drive-preview {
    max-width: min(1140px, 96vw);
}

.modal-drive-preview .drive-preview-body {
    min-height: min(70vh, 560px);
}

.drive-preview-body {
    background: #f8fafc;
    min-height: 520px;
    display: flex;
    flex-direction: column;
}

.drive-preview-toolbar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
}

.drive-search .input-group-text {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-right: none;
}

.drive-search .form-control {
    background: #fff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-left: none;
}

.drive-search .form-control:focus {
    background: #fff;
    color: #1e293b;
    box-shadow: none;
    border-color: #cbd5e1;
}

.drive-search .form-control::placeholder {
    color: #94a3b8;
}

.drive-stat-pill {
    background: #f1f5f9;
    color: #64748b;
    font-size: 0.8rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
}

.drive-stat-pill strong {
    color: #0f172a;
}

.drive-breadcrumb {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
}

.drive-preview-layout {
    display: flex;
    flex: 1;
    min-height: 460px;
    overflow: hidden;
}

.drive-preview-layout-3col .drive-courses-panel {
    width: 240px;
    min-width: 200px;
}

.drive-preview-layout-3col .drive-students-panel {
    width: 260px;
    min-width: 220px;
}

.drive-panel {
    background: #fff;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
}

.drive-panel-head {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.drive-panel-list {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem;
}

.drive-course-item {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    width: 100%;
    padding: 0.7rem 0.75rem;
    margin-bottom: 0.25rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #334155;
    text-align: left;
    cursor: pointer;
    transition: background 0.15s ease;
}

.drive-course-item:hover {
    background: #f1f5f9;
}

.drive-course-item.active {
    background: #eff6ff;
    box-shadow: inset 3px 0 0 #2563eb;
}

.drive-course-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.drive-course-item.active .drive-course-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.drive-course-count {
    font-size: 0.7rem;
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    font-weight: 600;
}

.drive-students-hint {
    padding: 1.25rem 1rem;
    text-align: center;
    color: #64748b;
    font-size: 0.8rem;
    line-height: 1.4;
}

.drive-overview-hero {
    text-align: center;
    padding: 1rem 0 1.5rem;
    border-bottom: 1px dashed #e2e8f0;
    margin-bottom: 1.5rem;
}

.drive-overview-hero h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
}

.drive-overview-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.drive-overview-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}

.drive-overview-card .num {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1d4ed8;
    line-height: 1;
}

.drive-overview-card .lbl {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
}

.drive-course-sheet .course-contrib-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.drive-course-sheet .course-contrib-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
}

.drive-canvas-hint {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    font-size: 0.85rem;
    color: #1e40af;
    margin-top: 1rem;
}

.drive-file-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.65rem 0.75rem;
    margin-bottom: 0.25rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #334155;
    text-align: left;
    cursor: pointer;
    transition: background 0.15s ease, box-shadow 0.15s ease;
}

.drive-file-item:hover {
    background: #f1f5f9;
}

.drive-file-item.active {
    background: #eff6ff;
    box-shadow: inset 3px 0 0 #2563eb;
}

.drive-file-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.drive-file-meta {
    min-width: 0;
    flex: 1;
}

.drive-file-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.drive-file-sub {
    font-size: 0.75rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.drive-preview-canvas {
    flex: 1;
    background: #f1f5f9;
    overflow-y: auto;
    display: flex;
    align-items: stretch;
    justify-content: center;
}

.drive-canvas-empty {
    margin: auto;
    text-align: center;
    color: #64748b;
    padding: 2rem;
}

.drive-canvas-empty i {
    font-size: 3.5rem;
    opacity: 0.35;
    display: block;
    margin-bottom: 1rem;
}

.drive-canvas-content {
    width: 100%;
    max-width: 640px;
    margin: 1.5rem auto;
    padding: 0 1.5rem 1.5rem;
}

.drive-doc-sheet {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

.drive-doc-header {
    padding: 1.75rem 2rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
}

.drive-doc-header h3 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.25rem;
}

.drive-doc-header .doc-subtitle {
    color: #64748b;
    font-size: 0.9rem;
}

.drive-doc-body {
    padding: 1.5rem 2rem 2rem;
}

.drive-field-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem 1.5rem;
}

.drive-field label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    margin-bottom: 0.25rem;
}

.drive-field .value {
    font-size: 0.95rem;
    font-weight: 500;
    color: #1e293b;
    word-break: break-word;
}

.drive-field.full-width {
    grid-column: 1 / -1;
}

.drive-status-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.drive-status-chip.aprobado {
    background: #dcfce7;
    color: #166534;
}

.drive-status-chip.pendiente {
    background: #fef3c7;
    color: #92400e;
}

.drive-status-chip.otro {
    background: #e0e7ff;
    color: #3730a3;
}

.drive-list-loading,
.drive-list-empty {
    padding: 2rem 1rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.875rem;
}

@media (max-width: 992px) {
    .drive-preview-layout-3col {
        flex-direction: column;
    }
    .drive-preview-layout-3col .drive-courses-panel,
    .drive-preview-layout-3col .drive-students-panel {
        width: 100%;
        max-height: 180px;
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
    }
    .drive-overview-cards {
        grid-template-columns: 1fr;
    }
    .drive-field-grid {
        grid-template-columns: 1fr;
    }
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
        
        // Mostrar u ocultar el botón de editar según si la meta está vencida o cerrada
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const isVencida = data.fecha_fin ? (new Date(data.fecha_fin + 'T00:00:00') < today) : false;
        const btnEditar = document.getElementById('btnEditarMeta');
        if (btnEditar) {
            if (data.estado === 'Cerrada' || isVencida) {
                btnEditar.style.display = 'none';
            } else {
                btnEditar.style.display = 'block';
            }
        }

        // Guardar datos para exportación y vista de participantes
        window._currentMetaData = data;
        
    } catch (error) {
        console.error('Error cargando datos:', error);
    }
}

window.loadMetaData = loadMetaData;

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
        
        const participantes = await fetchParticipants(metaData) || [];
        
        if (typeof XLSX === 'undefined') {
            hideExportLoading();
            alert('Error: Librería Excel no disponible');
            return;
        }
        
        const wb = XLSX.utils.book_new();
        
        // Calcular valores calculados precisos
        const totalObjetivo = parseInt(metaData.meta_objetivo) || 0;
        const totalFormados = participantes.length;
        const faltante = Math.max(0, totalObjetivo - totalFormados);
        const pctLogrado = totalObjetivo > 0 ? (totalFormados / totalObjetivo) : 0;
        
        // Construir datos del Resumen estructurado
        const summaryData = [
            [], // Fila 1 en blanco
            ['REPORTE DETALLADO DE META - INCES'], // Fila 2 (Título)
            [], // Fila 3 en blanco
            ['DATOS GENERALES DE LA META'], // Fila 4 (Subtítulo de Sección)
            ['ID Meta:', { v: parseInt(metaData.id_meta) || metaData.id_meta, t: 'n', z: '0' }],
            ['Objetivo / Descripción:', metaData.meta_objetivo || '-'],
            ['Tipo de Objetivo:', metaData.tipo_objetivo || 'Aprobados'],
            ['Ámbito Geográfico:', metaData.ambito_nombre || metaData.nombre_ambito || '-'],
            ['Estatus de la Meta:', metaData.estado || 'Activa'],
            ['Estatus de Cumplimiento:', metaData.estado_cumplimiento || 'En progreso'],
            [], // Fila 11 en blanco
            ['CRONOGRAMA DE EJECUCIÓN'], // Fila 12 (Subtítulo de Sección)
            ['Fecha de Inicio:', formatDate(metaData.fecha_inicio)],
            ['Fecha de Vencimiento:', formatDate(metaData.fecha_fin)],
            [], // Fila 15 en blanco
            ['MÉTRICAS Y AVANCE DE CUMPLIMIENTO'], // Fila 16 (Subtítulo de Sección)
            ['Meta Objetivo:', { v: totalObjetivo, t: 'n', z: '#,##0' }],
            ['Estudiantes Formados:', { v: totalFormados, t: 'n', z: '#,##0' }],
            ['Faltante para la Meta:', { v: faltante, t: 'n', z: '#,##0' }],
            ['Porcentaje Logrado:', { v: pctLogrado, t: 'n', z: '0.00%' }],
            [], // Fila 21 en blanco
            ['INFORMACIÓN DE EXPORTACIÓN'], // Fila 22 (Subtítulo de Sección)
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')]
        ];
        
        const ws_resumen = XLSX.utils.aoa_to_sheet(summaryData);
        
        // Aplicar fusiones (merges) para títulos y secciones
        ws_resumen['!merges'] = [
            { s: { r: 1, c: 0 }, e: { r: 1, c: 2 } },   // Título A2:C2
            { s: { r: 3, c: 0 }, e: { r: 3, c: 2 } },   // Sección A4:C4
            { s: { r: 11, c: 0 }, e: { r: 11, c: 2 } }, // Sección A12:C12
            { s: { r: 15, c: 0 }, e: { r: 15, c: 2 } }, // Sección A16:C16
            { s: { r: 21, c: 0 }, e: { r: 21, c: 2 } }  // Sección A22:C22
        ];
        
        // Ajustar anchos de columnas para Resumen
        ws_resumen['!cols'] = [
            { wch: 30 }, // Columna A (Etiquetas)
            { wch: 45 }  // Columna B (Valores)
        ];
        
        XLSX.utils.book_append_sheet(wb, ws_resumen, 'Resumen');
        
        // Construir datos de Participantes
        const headers = ['ID Participante', 'Cédula de Identidad', 'Nombre Completo', 'Curso Realizado', 'Estatus del Participante'];
        const rows = participantes.map(p => {
            const n = normalizeParticipant(p);
            return [
                parseInt(n.id_participante) || n.id_participante,
                n.cedula !== '' ? n.cedula : '-',
                participantFullName(n),
                n.nombre_curso || '-',
                n.estatus_participante || '-'
            ];
        });
        
        const participantsData = [headers, ...rows];
        const ws_participantes = XLSX.utils.aoa_to_sheet(participantsData);
        
        // Auto-ajustar anchos de columnas para Participantes
        const maxLens = [];
        participantsData.forEach(row => {
            row.forEach((val, colIdx) => {
                const len = val ? String(val).length : 0;
                maxLens[colIdx] = Math.max(maxLens[colIdx] || 12, len + 3);
            });
        });
        ws_participantes['!cols'] = maxLens.map(w => ({ wch: w }));
        
        XLSX.utils.book_append_sheet(wb, ws_participantes, 'Participantes');
        
        // Formatear nombre del archivo
        const todayStr = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, `Reporte_Meta_${metaData.id_meta}_${todayStr}.xlsx`);
        
    } catch (error) {
        alert('Error al exportar: ' + error.message);
    } finally {
        hideExportLoading();
    }
}

async function fetchParticipants(metaData) {
    try {
        const url = `../../app/controlador/Metas/obtenerParticipantesMeta.php?id_meta=${encodeURIComponent(metaData.id_meta)}`;
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Error al obtener participantes');
        }
        const list = result.participantes || result.data || [];
        return list.map(normalizeParticipant);
    } catch (e) {
        console.error('Error en fetchParticipants:', e);
        throw e;
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

// ==================== PREVISUALIZADOR: CURSOS → PARTICIPANTES → FICHA ====================

let allPreviewParticipants = [];
let previewCoursesGrouped = [];
let previewMetaContext = null;
let previewSelectedCourseIdx = -1; // índice en previewCoursesGrouped
let previewSelectedStudentIdx = -1; // índice dentro del curso seleccionado

function normalizeParticipant(raw) {
    if (!raw || typeof raw !== 'object') return {};
    const get = (...keys) => {
        for (const k of keys) {
            if (raw[k] !== undefined && raw[k] !== null && raw[k] !== '') return raw[k];
        }
        return '';
    };
    return {
        id_participante: get('id_participante', 'ID_PARTICIPANTE'),
        cedula: get('cedula', 'CEDULA'),
        primer_nombre: get('primer_nombre', 'PRIMER_NOMBRE'),
        segundo_nombre: get('segundo_nombre', 'SEGUNDO_NOMBRE'),
        primer_apellido: get('primer_apellido', 'PRIMER_APELLIDO'),
        segundo_apellido: get('segundo_apellido', 'SEGUNDO_APELLIDO'),
        genero: get('genero', 'GENERO'),
        correo: get('correo', 'CORREO'),
        id_curso: get('id_curso', 'ID_CURSO'),
        nombre_curso: get('nombre_curso', 'NOMBRE_CURSO'),
        estatus_participante: get('estatus_participante', 'ESTATUS_PARTICIPANTE'),
        fecha_inscripcion: get('fecha_inscripcion', 'FECHA_INSCRIPCION'),
        fecha_fin_curso: get('fecha_fin_curso', 'FECHA_FIN_CURSO'),
    };
}

function courseGroupKey(p) {
    if (p.id_curso) return 'c_' + p.id_curso;
    const nombre = (p.nombre_curso || '').trim();
    return nombre ? 'n_' + nombre : 'general';
}

function groupParticipantsByCourse(participants) {
    const map = new Map();
    for (const raw of participants) {
        const p = normalizeParticipant(raw);
        const key = courseGroupKey(p);
        const nombre = (p.nombre_curso || '').trim() || 'Registro general (sin curso)';
        if (!map.has(key)) {
            map.set(key, {
                key,
                id_curso: p.id_curso || null,
                nombre,
                participantes: [],
            });
        }
        map.get(key).participantes.push(p);
    }
    return Array.from(map.values()).sort((a, b) => {
        if (a.key === 'general') return 1;
        if (b.key === 'general') return -1;
        return a.nombre.localeCompare(b.nombre, 'es');
    });
}

function getSelectedCourse() {
    if (previewSelectedCourseIdx < 0 || previewSelectedCourseIdx >= previewCoursesGrouped.length) return null;
    return previewCoursesGrouped[previewSelectedCourseIdx];
}

function updatePreviewBreadcrumb() {
    const el = document.getElementById('previewBreadcrumbText');
    if (!el) return;
    const parts = ['Resumen'];
    const course = getSelectedCourse();
    if (course) parts.push(course.nombre);
    if (course && previewSelectedStudentIdx >= 0 && course.participantes[previewSelectedStudentIdx]) {
        parts.push(participantFullName(course.participantes[previewSelectedStudentIdx]));
    }
    el.textContent = parts.join(' › ');
}

function countByStatus(participantes) {
    const counts = {};
    participantes.forEach(p => {
        const s = p.estatus_participante || 'Sin estatus';
        counts[s] = (counts[s] || 0) + 1;
    });
    return counts;
}

function renderMetaOverview() {
    const meta = previewMetaContext || {};
    const totalCursos = previewCoursesGrouped.length;
    const totalParticipantes = allPreviewParticipants.length;
    const tipo = meta.tipo_objetivo || 'Aprobados';
    const ambito = meta.ambito_nombre || meta.nombre_ambito || 'Global';
    const tipoHelp = {
        'Aprobados': 'Participantes con estatus <strong>Aprobado</strong> cuyo fin de curso cae dentro del periodo de la meta.',
        'Inscritos': 'Participantes <strong>inscritos</strong> en cursos del ámbito durante el periodo de la meta.',
        'Ingresados': 'Personas <strong>registradas</strong> en el sistema en el periodo (sin vínculo a un curso específico).',
        'Masculino': 'Participantes <strong>masculinos</strong> registrados en el periodo.',
        'Femenino': 'Participantes <strong>femeninos</strong> registrados en el periodo.',
    };

    return `
        <div class="drive-doc-sheet">
            <div class="drive-doc-header">
                <h3 class="mb-1">Origen del cumplimiento</h3>
                <p class="doc-subtitle mb-0">Meta #${escapeHtml(meta.id_meta)} · ${escapeHtml(tipo)} · ${escapeHtml(ambito)}</p>
            </div>
            <div class="drive-doc-body">
                <div class="drive-overview-hero">
                    <p class="text-secondary mb-2 small">Como administrador, aquí ves <strong>qué cursos</strong> aportan participantes y <strong>quiénes</strong> son.</p>
                </div>
                <div class="drive-overview-cards">
                    <div class="drive-overview-card">
                        <div class="num">${totalCursos}</div>
                        <div class="lbl">Cursos que alimentan</div>
                    </div>
                    <div class="drive-overview-card">
                        <div class="num">${totalParticipantes}</div>
                        <div class="lbl">Participantes contados</div>
                    </div>
                    <div class="drive-overview-card">
                        <div class="num">${escapeHtml(meta.meta_objetivo ?? '0')}</div>
                        <div class="lbl">Meta objetivo</div>
                    </div>
                </div>
                <p class="mb-2"><strong>Criterio de conteo:</strong></p>
                <p class="text-secondary small mb-0">${tipoHelp[tipo] || 'Según el tipo de objetivo configurado en la meta.'}</p>
                <div class="drive-canvas-hint mt-3">
                    <i class="bi bi-arrow-left me-1"></i>
                    Seleccione un <strong>curso</strong> en el panel izquierdo para ver sus participantes. Luego elija una persona para ver su ficha.
                </div>
            </div>
        </div>
    `;
}

function renderCourseDetail(course) {
    const statusCounts = countByStatus(course.participantes);
    const statusHtml = Object.entries(statusCounts)
        .map(([s, n]) => `<li><span>${escapeHtml(s)}</span><span class="badge bg-light text-dark">${n}</span></li>`)
        .join('');

    const listPreview = course.participantes.slice(0, 8).map(p => {
        const name = participantFullName(p);
        const cedula = p.cedula !== '' && p.cedula != null ? String(p.cedula) : '—';
        return `<li><span>${escapeHtml(name)} <span class="text-secondary">(CI ${escapeHtml(cedula)})</span></span></li>`;
    }).join('');

    const more = course.participantes.length > 8
        ? `<li class="text-secondary small">… y ${course.participantes.length - 8} más (ver panel central)</li>`
        : '';

    return `
        <div class="drive-doc-sheet drive-course-sheet">
            <div class="drive-doc-header">
                <div class="d-flex align-items-start gap-3">
                    <div class="drive-course-icon" style="width:48px;height:48px;font-size:1.25rem;">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">${escapeHtml(course.nombre)}</h3>
                        <p class="doc-subtitle mb-0">
                            ${course.participantes.length} participante${course.participantes.length !== 1 ? 's' : ''} aportan a esta meta
                            ${course.id_curso ? ' · ID curso ' + escapeHtml(course.id_curso) : ''}
                        </p>
                    </div>
                </div>
            </div>
            <div class="drive-doc-body">
                <p class="fw-semibold mb-2">Desglose por estatus</p>
                <ul class="course-contrib-list mb-4">${statusHtml || '<li class="text-secondary">Sin datos de estatus</li>'}</ul>
                <p class="fw-semibold mb-2">Participantes de este curso</p>
                <ul class="course-contrib-list">${listPreview}${more}</ul>
                <div class="drive-canvas-hint">
                    <i class="bi bi-hand-index-thumb me-1"></i>
                    Elija un participante en el panel central para ver su ficha completa.
                </div>
            </div>
        </div>
    `;
}

function renderPreviewCanvas() {
    const canvas = document.getElementById('previewCanvasContent');
    if (!canvas) return;

    const course = getSelectedCourse();
    if (!course) {
        canvas.innerHTML = renderMetaOverview();
        updatePreviewBreadcrumb();
        return;
    }

    if (previewSelectedStudentIdx >= 0 && course.participantes[previewSelectedStudentIdx]) {
        const p = course.participantes[previewSelectedStudentIdx];
        canvas.innerHTML = renderStudentDetail(
            p,
            previewSelectedStudentIdx,
            course.participantes.length,
            course.nombre
        );
    } else {
        canvas.innerHTML = renderCourseDetail(course);
    }
    updatePreviewBreadcrumb();
}

function participantFullName(p) {
    return [
        p.primer_nombre,
        p.segundo_nombre,
        p.primer_apellido,
        p.segundo_apellido,
    ].filter(Boolean).join(' ').trim() || 'Sin nombre';
}

function participantInitials(p) {
    const n = (p.primer_nombre || '').charAt(0);
    const a = (p.primer_apellido || '').charAt(0);
    return (n + a).toUpperCase() || '?';
}

function formatPreviewDate(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return String(value);
        return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch {
        return String(value);
    }
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function statusChipClass(estatus) {
    const e = (estatus || '').toLowerCase();
    if (e.includes('aprobado')) return 'aprobado';
    if (e.includes('pendiente')) return 'pendiente';
    return 'otro';
}

function renderStudentDetail(p, index, total, courseName) {
    const name = participantFullName(p);
    const cedula = p.cedula !== '' && p.cedula !== null ? String(p.cedula) : '—';
    const estatus = p.estatus_participante || '—';
    const chipClass = statusChipClass(estatus);
    const cursoLabel = courseName || p.nombre_curso || '—';

    return `
        <div class="drive-doc-sheet">
            <div class="drive-doc-header">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h3>${escapeHtml(name)}</h3>
                        <p class="doc-subtitle mb-0">
                            Participante #${escapeHtml(p.id_participante)} · ${index + 1} de ${total} en
                            <strong>${escapeHtml(cursoLabel)}</strong>
                        </p>
                    </div>
                    <span class="drive-status-chip ${chipClass}">
                        <i class="bi bi-circle-fill" style="font-size: 0.45rem;"></i>
                        ${escapeHtml(estatus)}
                    </span>
                </div>
            </div>
            <div class="drive-doc-body">
                <div class="drive-field-grid">
                    <div class="drive-field">
                        <label>Cédula de identidad</label>
                        <div class="value">${escapeHtml(cedula)}</div>
                    </div>
                    <div class="drive-field">
                        <label>Género</label>
                        <div class="value">${escapeHtml(p.genero || '—')}</div>
                    </div>
                    <div class="drive-field full-width">
                        <label>Correo electrónico</label>
                        <div class="value">${escapeHtml(p.correo || '—')}</div>
                    </div>
                    <div class="drive-field full-width">
                        <label>Curso que aporta a la meta</label>
                        <div class="value">${escapeHtml(cursoLabel)}</div>
                    </div>
                    <div class="drive-field">
                        <label>Fecha de inscripción</label>
                        <div class="value">${escapeHtml(formatPreviewDate(p.fecha_inscripcion))}</div>
                    </div>
                    <div class="drive-field">
                        <label>Fecha fin de curso</label>
                        <div class="value">${escapeHtml(formatPreviewDate(p.fecha_fin_curso))}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function selectPreviewCourse(courseIdx) {
    previewSelectedCourseIdx = (courseIdx === null || courseIdx === undefined || Number.isNaN(courseIdx)) ? -1 : courseIdx;
    previewSelectedStudentIdx = -1;

    document.querySelectorAll('.drive-course-item').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.courseIdx, 10) === previewSelectedCourseIdx);
    });

    const course = getSelectedCourse();
    const titleEl = document.getElementById('previewStudentPanelTitle');
    const countEl = document.getElementById('previewStudentPanelCount');
    const listEl = document.getElementById('previewStudentList');

    if (!course) {
        if (titleEl) titleEl.textContent = 'Participantes';
        if (countEl) countEl.textContent = '0';
        if (listEl) {
            listEl.innerHTML = `
                <div class="drive-students-hint">
                    <i class="bi bi-journal-bookmark d-block fs-4 mb-2 opacity-50"></i>
                    Elija un curso para ver quiénes aportan a la meta
                </div>`;
        }
        renderPreviewCanvas();
        return;
    }

    if (titleEl) {
        const short = course.nombre.length > 28 ? course.nombre.slice(0, 26) + '…' : course.nombre;
        titleEl.textContent = short;
        titleEl.title = course.nombre;
    }
    if (countEl) countEl.textContent = String(course.participantes.length);

    if (listEl) {
        if (!course.participantes.length) {
            listEl.innerHTML = `<div class="drive-list-empty">Este curso no tiene participantes en el periodo.</div>`;
        } else {
            listEl.innerHTML = course.participantes.map((p, i) => {
                const name = participantFullName(p);
                const cedula = p.cedula !== '' && p.cedula != null ? String(p.cedula) : '—';
                return `
                    <button type="button" class="drive-file-item${i === previewSelectedStudentIdx ? ' active' : ''}"
                        data-student-idx="${i}" role="option">
                        <div class="drive-file-avatar">${escapeHtml(participantInitials(p))}</div>
                        <div class="drive-file-meta">
                            <div class="drive-file-name">${escapeHtml(name)}</div>
                            <div class="drive-file-sub">CI ${escapeHtml(cedula)} · ${escapeHtml(p.estatus_participante || '—')}</div>
                        </div>
                    </button>`;
            }).join('');

            listEl.querySelectorAll('.drive-file-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    selectPreviewStudent(parseInt(btn.dataset.studentIdx, 10));
                });
            });
        }
    }

    renderPreviewCanvas();
}

function selectPreviewStudent(index) {
    const course = getSelectedCourse();
    if (!course || index < 0 || index >= course.participantes.length) {
        previewSelectedStudentIdx = -1;
        document.querySelectorAll('#previewStudentList .drive-file-item').forEach(el => el.classList.remove('active'));
        renderPreviewCanvas();
        return;
    }

    previewSelectedStudentIdx = index;
    document.querySelectorAll('#previewStudentList .drive-file-item').forEach((el, i) => {
        el.classList.toggle('active', i === index);
    });

    renderPreviewCanvas();
    document.querySelector(`#previewStudentList .drive-file-item[data-student-idx="${index}"]`)
        ?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

function renderPreviewCourseList(courses) {
    const listEl = document.getElementById('previewCourseList');
    const courseCountEl = document.getElementById('previewCourseCountBadge');
    const partCountEl = document.getElementById('previewCountBadge');
    if (!listEl) return;

    const totalParts = courses.reduce((s, c) => s + c.participantes.length, 0);
    if (courseCountEl) courseCountEl.textContent = String(courses.length);
    if (partCountEl) partCountEl.textContent = String(totalParts);

    if (!courses.length) {
        listEl.innerHTML = `<div class="drive-list-empty">Ningún curso aporta participantes con el criterio actual.</div>`;
        selectPreviewCourse(-1);
        return;
    }

    listEl.innerHTML = courses.map((c, idx) => `
        <button type="button" class="drive-course-item${idx === previewSelectedCourseIdx ? ' active' : ''}"
            data-course-idx="${idx}" role="option" title="${escapeHtml(c.nombre)}">
            <div class="drive-course-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div class="drive-file-meta">
                <div class="drive-file-name">${escapeHtml(c.nombre)}</div>
                <div class="drive-file-sub">
                    <span class="drive-course-count">${c.participantes.length}</span>
                    participante${c.participantes.length !== 1 ? 's' : ''}
                </div>
            </div>
        </button>
    `).join('');

    listEl.querySelectorAll('.drive-course-item').forEach(btn => {
        btn.addEventListener('click', () => selectPreviewCourse(parseInt(btn.dataset.courseIdx, 10)));
    });
}

function renderPreviewDrive(participants) {
    allPreviewParticipants = participants || [];
    previewCoursesGrouped = groupParticipantsByCourse(allPreviewParticipants);

    if (previewSelectedCourseIdx < 0 || previewSelectedCourseIdx >= previewCoursesGrouped.length) {
        previewSelectedCourseIdx = -1;
        previewSelectedStudentIdx = -1;
    } else {
        const course = getSelectedCourse();
        if (course && (previewSelectedStudentIdx < 0 || previewSelectedStudentIdx >= course.participantes.length)) {
            previewSelectedStudentIdx = -1;
        }
    }

    renderPreviewCourseList(previewCoursesGrouped);
    selectPreviewCourse(previewSelectedCourseIdx);
}

/** @deprecated alias */
function renderPreviewTable(participants) {
    renderPreviewDrive(participants);
}

// ==================== ACCIONES RÁPIDAS ====================

function openParticipantsPreview(data) {
        if (!data) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Espere a que carguen los datos de la meta');
            } else {
                alert('Espere a que carguen los datos de la meta');
            }
            return;
        }

        const viewMetaDetails = document.getElementById('viewMetaDetails');
        const viewMetaPreview = document.getElementById('viewMetaPreview');
        const modalDialog = document.getElementById('modalDialogMeta');

        previewMetaContext = data;
        const searchInput = document.getElementById('searchParticipantPreview');
        if (searchInput) searchInput.value = '';
        allPreviewParticipants = [];
        previewCoursesGrouped = [];
        previewSelectedCourseIdx = -1;
        previewSelectedStudentIdx = -1;

        document.getElementById('previewSubtitle').textContent =
            `Meta #${data.id_meta} · ${data.tipo_objetivo || 'Aprobados'} · ${data.ambito_nombre || 'Global'}`;
        document.getElementById('previewMetaGoal').textContent = data.meta_objetivo || '0';

        const loadingHtml = `
            <div class="drive-list-loading py-5 text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <p class="mt-2 mb-0 small">Cargando...</p>
            </div>`;
        const courseListEl = document.getElementById('previewCourseList');
        const listEl = document.getElementById('previewStudentList');
        const canvas = document.getElementById('previewCanvasContent');
        if (courseListEl) courseListEl.innerHTML = loadingHtml;
        if (listEl) listEl.innerHTML = loadingHtml;
        if (canvas) canvas.innerHTML = loadingHtml;

        // Intercambiar vistas internamente en el mismo modal sin alterar backdrop
        if (viewMetaDetails && viewMetaPreview && modalDialog) {
            viewMetaDetails.classList.add('d-none');
            viewMetaPreview.classList.remove('d-none');
            modalDialog.classList.remove('modal-lg');
            modalDialog.classList.add('modal-xl', 'modal-drive-preview');
        }

        // Cargar participantes de forma asíncrona
        fetchParticipants(data).then(participantes => {
            allPreviewParticipants = participantes || [];
            renderPreviewDrive(allPreviewParticipants);
        }).catch(err => {
            console.error(err);
            if (listEl) {
                listEl.innerHTML = `
                    <div class="drive-list-empty text-danger">
                        <i class="bi bi-exclamation-triangle d-block fs-4 mb-2"></i>
                        Error al cargar participantes.
                    </div>`;
            }
        });
}

function initQuickActions() {
    document.getElementById('quickViewParticipants')?.addEventListener('click', function () {
        const data = window._currentMetaData || window._metaToShow;
        openParticipantsPreview(data);
    });

    // Filtro buscador cliente-side
    document.getElementById('searchParticipantPreview')?.addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase().trim();
        previewSelectedCourseIdx = -1;
        previewSelectedStudentIdx = -1;

        if (!query) {
            renderPreviewDrive(allPreviewParticipants);
            return;
        }

        const filtered = allPreviewParticipants.filter(p => {
            const fullName = participantFullName(p).toLowerCase();
            const cedula = String(p.cedula ?? '').toLowerCase();
            const curso = (p.nombre_curso || '').toLowerCase();
            return fullName.includes(query) || cedula.includes(query) || curso.includes(query);
        });

        renderPreviewDrive(filtered);

        // Si hay un solo curso en el resultado, abrirlo para agilizar la revisión
        if (previewCoursesGrouped.length === 1) {
            selectPreviewCourse(0);
        }
    });

    // Botones para volver al modal de detalles de la meta
    const goBack = () => {
        const viewMetaDetails = document.getElementById('viewMetaDetails');
        const viewMetaPreview = document.getElementById('viewMetaPreview');
        const modalDialog = document.getElementById('modalDialogMeta');

        if (viewMetaDetails && viewMetaPreview && modalDialog) {
            viewMetaPreview.classList.add('d-none');
            viewMetaDetails.classList.remove('d-none');
            modalDialog.classList.remove('modal-xl', 'modal-drive-preview');
            modalDialog.classList.add('modal-lg');
        }
        previewSelectedCourseIdx = -1;
        previewSelectedStudentIdx = -1;
    };

    document.getElementById('btnBackToMeta')?.addEventListener('click', goBack);
    document.getElementById('btnBackToMetaFooter')?.addEventListener('click', goBack);

    // Botones de exportación
    const triggerExport = () => {
        const data = window._currentMetaData;
        if (data) exportMetaReport(data);
        else alert('No hay datos para exportar');
    };

    document.getElementById('btnPreviewExport')?.addEventListener('click', triggerExport);
    document.getElementById('quickExport')?.addEventListener('click', triggerExport);
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
        // Asegurar de reiniciar a la vista de detalles al cerrar el modal
        const viewMetaDetails = document.getElementById('viewMetaDetails');
        const viewMetaPreview = document.getElementById('viewMetaPreview');
        const modalDialog = document.getElementById('modalDialogMeta');
        if (viewMetaDetails && viewMetaPreview && modalDialog) {
            viewMetaPreview.classList.add('d-none');
            viewMetaDetails.classList.remove('d-none');
            modalDialog.classList.remove('modal-xl', 'modal-drive-preview');
            modalDialog.classList.add('modal-lg');
        }
        previewSelectedCourseIdx = -1;
        previewSelectedStudentIdx = -1;
    });
    
    modal.addEventListener('shown.bs.modal', () => {
        const pending = window._metaToShow;
        if (pending) {
            loadMetaData(pending);
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
    window._currentMetaData = metaData;
    window._metaToShow = null;
    const modalEl = document.getElementById('modal-viewMeta');
    loadMetaData(metaData);
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModal);
} else {
    initModal();
}
console.log('✅ Modal con Header Mejorado cargado correctamente');
</script>
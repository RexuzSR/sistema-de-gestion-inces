<div class="modal fade" id="modal-editMeta" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-editMeta" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden modern-modal">

            <!-- HEADER ELEGANTE -->
            <div class="modal-header border-0 pt-4 px-4 position-relative bg-gradient-modern rounded-top-4">
                <div class="w-100 text-center">
                    <div class="modern-icon-wrapper mb-2">
                        <i class="bi bi-feather text-white"></i>
                    </div>
                    <h4 class="modal-title fw-bold text-white mb-1">Editar Meta</h4>
                    <p class="text-white-85 mb-0 small text-white">Actualiza los detalles de la meta formativa</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY MODERNO -->
            <div class="modal-body p-4">
                <form id="form-editMeta" method="POST">
                    <input type="hidden" id="edit_id_meta" name="id_meta">
                    
                    <div class="row g-4">
                        <!-- META OBJETIVO -->
                        <div class="col-md-6">
                            <div class="form-modern">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    Meta Objetivo
                                </label>
                                <div class="input-group input-modern">
                                        <span class="input-group-text bg-light border-end-0">
                                    </span>
                                    <input type="number" class="form-control modern-input" 
                                           id="edit_meta_objetivo" name="meta_objetivo" 
                                           placeholder="Ej: 100" min="0" required>
                                    <span class="input-group-text bg-light border-start-0 text-muted">
                                        estudiantes
                                    </span>
                                </div>
                                <div class="form-text text-muted mt-1">
                                    Cantidad deseada de estudiantes
                                </div>
                            </div>
                        </div>

                        <!-- ESTUDIANTES FORMADOS -->
                        <div class="col-md-6">
                            <div class="form-modern">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="bi bi-people"></i>Estudiantes Formados
                                </label>
                                <div class="input-group input-modern">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                    <input type="number" class="form-control modern-input" 
                                           id="edit_estudiantes_formados" name="estudiantes_formados" 
                                           placeholder="Calculado automáticamente" readonly>
                                    <span class="input-group-text bg-light border-start-0 text-muted">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                </div>
                                <div class="form-text text-muted mt-1">
                                    Se actualiza automáticamente de acuerdo al tipo de meta seleccionada
                                </div>
                            </div>
                        </div>

                        <!-- PERÍODO DE EJECUCIÓN -->
                        <div class="col-12">
                            <!-- Periodo de Tiempo -->
                            <div class="col-12 mb-3">
                                <div class="form-modern">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                        <i class="bi bi-clock-history me-1"></i>Periodo de Tiempo
                                    </label>
                                    <select class="form-select modern-select" id="edit_periodo_tiempo" name="periodo_tiempo" required>
                                        <option value="">Seleccione un periodo</option>
                                        <option value="mensual">Mensual (1 mes)</option>
                                        <option value="trimestral">Trimestral (3 meses)</option>
                                        <option value="semestral">Semestral (6 meses)</option>
                                        <option value="anual">Anual (12 meses)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-modern mb-3">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-bg-primary me-3">
                                            <i class="bi bi-calendar-range text-white"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-0 text-black">Período de Ejecución</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-modern">
                                                <label class="form-label fw-medium text-secondary mb-2">
                                                    Fecha Inicio
                                                </label>
                                                <div class="input-group input-modern">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-calendar"></i>
                                                    </span>
                                                    <input type="date" class="form-control modern-input" 
                                                           id="edit_fecha_inicio" name="fecha_inicio" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-modern">
                                                <label class="form-label fw-medium text-secondary mb-2">
                                                    Fecha Fin
                                                </label>
                                                <div class="input-group input-modern">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-calendar"></i>
                                                    </span>
                                                    <input type="date" class="form-control modern-input" 
                                                           id="edit_fecha_fin" name="fecha_fin"
                                                           readonly disabled
                                                           style="background:#f8f9fa;cursor:not-allowed;"
                                                           required>
                                                </div>
                                                <div class="form-text text-muted mt-1">
                                                    <i class="bi bi-info-circle me-1"></i>Se calcula automáticamente según el periodo seleccionado.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ESTADO -->
                        <div class="col-md-6">
                            <div class="form-modern">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    Estado
                                </label>
                                <div class="btn-group btn-group-modern w-100" role="group">
                                    <input type="radio" class="btn-check" name="estado" id="edit_estado_activa" value="Activa">
                                        <label class="btn btn-outline-modern btn-radio-modern" for="edit_estado_activa">
                                        <span>Activa</span>
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="estado" id="edit_estado_cerrada" value="Cerrada">
                                        <label class="btn btn-outline-modern btn-radio-modern" for="edit_estado_cerrada">
                                        <span>Cerrada</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- TIPO DE OBJETIVO -->
                        <div class="col-md-6">
                            <div class="form-modern">
                                    <label class="form-label fw-semibold text-dark mb-2">
                                    Tipo de Objetivo
                                </label>
                                <select class="form-select modern-select" id="edit_tipo_objetivo" name="tipo_objetivo" required>
                                    <option value="">Seleccione el tipo de objetivo</option>
                                    <option value="Aprobados">Participantes Aprobados</option>
                                    <option value="Inscritos">Participantes Inscritos</option>
                                    <option value="Ingresados">Participantes Ingresados al Sistema</option>
                                    <option value="Masculino">Participantes por Género Masculino</option>
                                    <option value="Femenino">Participantes por Género Femenino</option>
                                </select>
                            </div>
                        </div>

                        <!-- ÁMBITO -->
                        <div class="col-12">
                            <div class="form-modern">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="bi bi-diagram-3"></i>Ámbito
                                    <span class="badge bg-secondary ms-1" style="font-size:0.7rem">Opcional</span>
                                </label>
                                <div class="input-group input-modern">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <select class="form-select modern-select" id="edit_id_ambito" name="id_ambito">
                                        <option value="">
                                            🌐 Global (todos los ámbitos)
                                        </option>
                                        <?php
                                        try {
                                            $ambitos = $conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($ambitos as $ambito) {
                                                echo "<option value='{$ambito['id_ambito']}'>{$ambito['nombre_ambito']}</option>";
                                            }
                                        } catch (Exception $e) {
                                            echo '<option disabled class="text-danger">Error al cargar ámbitos</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-text text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Global</strong>: suma de <em>todos</em> los ámbitos.
                                    <strong>Ámbito específico</strong>: solo cuenta participantes de cursos en ese ámbito.
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER ELEGANTE -->
                    <div class="modal-footer border-top pt-4 mt-2 px-0">
                        <div class="d-flex justify-content-between w-100">
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold modern-btn">
                                <i class="bi bi-check-circle"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
:root {
    --modern-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    --primary-color: #6366f1;
    --primary-hover: #4f46e5;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --purple-color: #8b5cf6;
    --light-bg: #f8fafc;
    --border-color: #e2e8f0;
}

/* MODAL MODERNO */
.modern-modal {
    background: #ffffff;
    border: 1px solid var(--border-color);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
}

.bg-gradient-modern {
    background: var(--modern-gradient);
}

.modern-icon-wrapper {
    width: 56px;
    height: 56px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.icon-bg-primary {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* FORMULARIOS MODERNOS */
.form-modern {
    position: relative;
    margin-bottom: 1.5rem;
}

.input-modern {
    border-radius: 10px;
    overflow: hidden;
}

.modern-input {
    border: 2px solid var(--border-color);
    background: var(--light-bg);
    transition: all 0.3s ease;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
}

.modern-input:focus {
    border-color: var(--primary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

.modern-input:disabled,
.modern-input[readonly] {
    background: #f1f5f9;
    opacity: 0.8;
    cursor: not-allowed;
}

.modern-select {
    border: 2px solid var(--border-color);
    background: var(--light-bg);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 16px 12px;
    padding-right: 3rem;
}

.modern-select:focus {
    border-color: var(--primary-color);
    background-color: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

/* BOTONES MODERNOS */
.btn-outline-modern {
    border: 2px solid var(--border-color);
    background: white;
    color: #64748b;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.75rem 1rem;
}

.btn-outline-modern:hover {
    background: var(--light-bg);
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-1px);
}

.btn-check:checked + .btn-outline-modern {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.btn-check:checked + .btn-outline-modern[for="edit_estado_cerrada"] {
    background: var(--danger-color);
    border-color: var(--danger-color);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.btn-group-modern .btn-radio-modern {
    border-radius: 8px !important;
    margin: 0 2px;
}

.modern-btn {
    background: var(--modern-gradient);
    border: none;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.modern-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.modern-btn:active {
    transform: translateY(0);
}

/* CARD MODERNA */
.card-modern {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 16px;
    transition: all 0.3s ease;
}

.card-modern:hover {
    border-color: var(--primary-color);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.1);
}

/* ANIMACIONES */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-body .row > div {
    animation: slideInUp 0.4s ease forwards;
}

.modal-body .row > div:nth-child(1) { animation-delay: 0.1s; }
.modal-body .row > div:nth-child(2) { animation-delay: 0.2s; }
.modal-body .row > div:nth-child(3) { animation-delay: 0.3s; }
.modal-body .row > div:nth-child(4) { animation-delay: 0.4s; }
.modal-body .row > div:nth-child(5) { animation-delay: 0.5s; }
.modal-body .row > div:nth-child(6) { animation-delay: 0.6s; }

/* COLORES DE ICONOS */
.text-purple {
    color: var(--purple-color) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.text-success {
    color: var(--success-color) !important;
}

.text-warning {
    color: var(--warning-color) !important;
}

.text-info {
    color: var(--info-color) !important;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .btn-group-modern .btn-radio-modern {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .modern-input, .modern-select {
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 768px) {
    .modal-body {
        padding: 1.5rem !important;
    }
    
    .card-modern .card-body {
        padding: 1.25rem !important;
    }
    
    .btn-group-modern {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-radio-modern {
        width: 100%;
        margin: 0;
    }
    
    .modern-icon-wrapper {
        width: 48px;
        height: 48px;
    }
}

/* MEJORAS VISUALES */
.input-group-text {
    background: #f1f5f9;
    border: 2px solid var(--border-color);
    color: #64748b;
    font-weight: 500;
}

.input-group .input-group-text:first-child {
    border-right: none;
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
}

.input-group .input-group-text:last-child {
    border-left: none;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

.input-group .modern-input:not(:first-child):not(:last-child) {
    border-left: none;
    border-right: none;
}

/* EFECTOS HOVER */
.btn-outline-secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #475569;
}

/* ESTILOS PARA LOS ICONOS */
.bi {
    transition: transform 0.2s ease;
}

.modern-btn:hover .bi,
.btn-outline-modern:hover .bi {
    transform: scale(1.1);
}

/* SCROLL SUAVE */
.modern-modal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.modern-modal .modal-body::-webkit-scrollbar {
    width: 6px;
}

.modern-modal .modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.modern-modal .modal-body::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

.modern-modal .modal-body::-webkit-scrollbar-thumb:hover {
    background: var(--primary-hover);
}

/* ESTADOS DE FOCO */
.modern-input:focus,
.modern-select:focus {
    transform: translateY(-1px);
}

/* EFECTO DE ELEVACIÓN AL HACER HOVER EN CARD */
.card-modern {
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-2px);
}
</style>

<script>
// Inicializar el modal de edición
function initEditMetaModal() {
    const form = document.getElementById('form-editMeta');
    const fechaInicio = document.getElementById('edit_fecha_inicio');
    const fechaFin = document.getElementById('edit_fecha_fin');
    const periodoSelect = document.getElementById('edit_periodo_tiempo');

    // Función para calcular fecha fin en edición
    function calcularFechaFinEdit() {
        const inicio = fechaInicio.value;
        const periodo = periodoSelect.value;
        if (!inicio || !periodo) { fechaFin.value = ''; return; }
        const fecha = new Date(inicio + 'T00:00:00');
        switch (periodo) {
            case 'mensual':    fecha.setMonth(fecha.getMonth() + 1); break;
            case 'trimestral': fecha.setMonth(fecha.getMonth() + 3); break;
            case 'semestral':  fecha.setMonth(fecha.getMonth() + 6); break;
            case 'anual':      fecha.setFullYear(fecha.getFullYear() + 1); break;
        }
        const y = fecha.getFullYear();
        const m = String(fecha.getMonth() + 1).padStart(2, '0');
        const d = String(fecha.getDate()).padStart(2, '0');
        fechaFin.value = `${y}-${m}-${d}`;
    }

    // Habilitar/deshabilitar fecha fin según periodo
    function actualizarFechaFinEdit() {
        if (periodoSelect.value) {
            fechaFin.disabled = false;
            fechaFin.style.cursor = '';
            calcularFechaFinEdit();
        } else {
            fechaFin.disabled = true;
            fechaFin.style.cursor = 'not-allowed';
            fechaFin.value = '';
        }
    }

    // Event listeners
    periodoSelect.addEventListener('change', actualizarFechaFinEdit);
    fechaInicio.addEventListener('change', function() {
        if (periodoSelect.value) calcularFechaFinEdit();
    });

    // Inicializar estado
    actualizarFechaFinEdit();
    
    // Mejorar la experiencia del select de ámbito
    const ambitoSelect = document.getElementById('edit_id_ambito');
    if (ambitoSelect && ambitoSelect.options.length > 8) {
        // Agregar búsqueda simple
        const searchContainer = document.createElement('div');
        searchContainer.className = 'mb-3';
        
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control modern-input';
        searchInput.placeholder = 'Buscar ámbito...';
        
        searchContainer.appendChild(searchInput);
        ambitoSelect.parentNode.insertBefore(searchContainer, ambitoSelect);
        
        // Función de búsqueda
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = ambitoSelect.options;
            
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
            
            // Seleccionar la primera opción visible
            for (let i = 0; i < options.length; i++) {
                if (options[i].style.display !== 'none' && options[i].value) {
                    ambitoSelect.value = options[i].value;
                    break;
                }
            }
        });
    }
    
    // Manejar el envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar que la fecha fin sea mayor o igual a la fecha inicio
        if (fechaInicio.value && fechaFin.value && fechaFin.value < fechaInicio.value) {
            showToast('La fecha fin debe ser posterior o igual a la fecha inicio', 'warning');
            fechaFin.focus();
            return false;
        }
        
        // Meta objetivo obligatoria
        const metaObjetivo = document.getElementById('edit_meta_objetivo');
        if (!metaObjetivo.value || metaObjetivo.value <= 0) {
            showToast('La meta objetivo debe ser mayor a 0', 'warning');
            metaObjetivo.focus();
            return false;
        }

        // Tipo objetivo obligatorio
        const tipoObjetivo = document.getElementById('edit_tipo_objetivo');
        if (!tipoObjetivo.value) {
            showToast('Debe seleccionar el tipo de objetivo de la meta', 'warning');
            tipoObjetivo.focus();
            return false;
        }
        
        // Mostrar loading en el botón
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
        submitBtn.disabled = true;
        
        // Aquí iría la lógica de envío AJAX
        console.log('Enviando formulario de edición:', new FormData(this));
        
        // Simular envío (reemplazar con tu lógica real)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Mostrar mensaje de éxito
            showToast('Meta actualizada correctamente', 'success');
            
            // Cerrar modal después de 1.5 segundos
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modal-editMeta'));
                if (modal) modal.hide();
                
                // Recargar la página o actualizar la tabla
                if (typeof reloadMetaTable === 'function') {
                    reloadMetaTable();
                }
            }, 1500);
        }, 1500);
        
        return false;
    });
}

// Función para cargar datos en el modal de edición
function loadEditMetaData(data) {
    console.log('Cargando datos para editar:', data);
    
    // Datos básicos
    document.getElementById('edit_id_meta').value = data.id_meta || '';
    document.getElementById('edit_meta_objetivo').value = data.meta_objetivo || '';
    document.getElementById('edit_estudiantes_formados').value = data.estudiantes_formados || '0';
    document.getElementById('edit_fecha_inicio').value = data.fecha_inicio || '';

    // Cargar el periodo primero
    const periodoSelect = document.getElementById('edit_periodo_tiempo');
    const fechaFin = document.getElementById('edit_fecha_fin');
    if (periodoSelect && data.periodo_tiempo) {
        periodoSelect.value = data.periodo_tiempo;
        // Habilitar fecha_fin y asignar el valor guardado
        fechaFin.disabled = false;
        fechaFin.style.cursor = '';
        fechaFin.value = data.fecha_fin || '';
    } else {
        if (periodoSelect) periodoSelect.value = '';
        fechaFin.disabled = true;
        fechaFin.style.cursor = 'not-allowed';
        fechaFin.value = '';
    }
    
    // Configurar radio buttons de estado
    if (data.estado === 'Activa') {
        document.getElementById('edit_estado_activa').checked = true;
        document.getElementById('edit_estado_activa').dispatchEvent(new Event('change'));
    } else if (data.estado === 'Cerrada') {
        document.getElementById('edit_estado_cerrada').checked = true;
        document.getElementById('edit_estado_cerrada').dispatchEvent(new Event('change'));
    }
    
    // Tipo objetivo
    document.getElementById('edit_tipo_objetivo').value = data.tipo_objetivo || '';
    
    // Ámbito: si es null/vacío => Global (value="")
    const ambitoSelect = document.getElementById('edit_id_ambito');
    if (ambitoSelect) {
        ambitoSelect.value = data.id_ambito ?? '';
    }
    
    // Actualizar validación de fechas
    const fechaInicio = document.getElementById('edit_fecha_inicio');
    const fechaFin = document.getElementById('edit_fecha_fin');
    
    if (fechaInicio.value) {
        fechaFin.min = fechaInicio.value;
    }
}

// Función para mostrar toasts
function showToast(message, type = 'info') {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Crear toast
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Icono según tipo
    let icon = 'bi-info-circle';
    if (type === 'success') icon = 'bi-check-circle';
    if (type === 'warning') icon = 'bi-exclamation-triangle';
    if (type === 'danger') icon = 'bi-x-circle';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="${icon} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Mostrar toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });
    bsToast.show();
    
    // Remover del DOM después de ocultar
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initEditMetaModal);

// Función para abrir el modal de edición
function openEditMetaModal(metaData) {
    loadEditMetaData(metaData);
    const modal = new bootstrap.Modal(document.getElementById('modal-editMeta'));
    modal.show();
}

// Estilos adicionales para toasts
const toastStyles = document.createElement('style');
toastStyles.textContent = `
.toast {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 0.5rem;
    animation: toastSlideIn 0.3s ease;
}

@keyframes toastSlideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-container {
    z-index: 1060;
}
`;
document.head.appendChild(toastStyles);
</script>
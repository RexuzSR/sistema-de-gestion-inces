<div class="modal fade" id="modal-addMeta" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-addMeta" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0">

            <!-- HEADER MEJORADO -->
            <div class="modal-header border-0 pt-4 px-4 position-relative bg-gradient-primary rounded-top-4">
                <div class="w-100 text-center">
                    <div class="icon-wrapper mb-2">
                        <i class="bi bi-bullseye fs-1 text-white opacity-90"></i>
                    </div>
                    <h4 class="modal-title fw-bold text-white mb-1">Nueva Meta de Formación</h4>
                    <p class="text-white-75 mb-0 small">Define los objetivos y parámetros de la meta formativa</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY REDISEÑADO -->
            <div class="modal-body p-4">
                <form id="form-addMeta" method="POST">
                    <div class="row g-4">
                        
                        <!-- SECCIÓN: INFORMACIÓN BÁSICA -->
                        <div class="col-12">
                            <div class="section-header mb-3">
                                <h6 class="fw-semibold text-primary mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Información General
                                </h6>
                                <div class="section-divider"></div>
                            </div>
                        </div>

                        <!-- Meta objetivo con validación mejorada -->
                        <div class="col-12">
                            <label class="form-label fw-medium text-secondary mb-2">
                                <i class="bi bi-target me-1"></i>Meta Objetivo
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-people text-primary"></i>
                                </span>
                                <input type="number" class="form-control" 
                                       id="meta_objetivo" name="meta_objetivo" 
                                       placeholder="Introduzca una cantidad" 
                                       min="1" 
                                       max="990000" 
                                       maxlength="6"
                                       oninput="validarMetaObjetivo(this)"
                                       onkeypress="return validarTecla(event)"
                                       required>
                                <span class="input-group-text bg-light">
                                    participantes
                                </span>
                            </div>
                            <div class="form-text text-muted mt-1" id="meta_objetivo_help">
                                Número total de participantes que se espera alcanzar (máximo 990,000)
                            </div>
                            <div class="invalid-feedback" id="meta_objetivo_error" style="display: none;"></div>
                        </div>

                        <!-- Periodo de tiempo -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-secondary mb-2">
                                <i class="bi bi-clock-history me-1"></i>Periodo de Tiempo
                            </label>
                            <select class="form-select" id="periodo_tiempo" name="periodo_tiempo" required>
                                <option value="">Seleccione un periodo</option>
                                <option value="mensual">Mensual (1 mes)</option>
                                <option value="trimestral">Trimestral (3 meses)</option>
                                <option value="semestral">Semestral (6 meses)</option>
                                <option value="anual">Anual (12 meses)</option>
                            </select>
                        </div>

                        <!-- Fechas en tarjeta separada -->
                        <div class="col-12">
                            <div class="card border-light shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-3 text-secondary">
                                        <i class="bi bi-calendar-range me-2"></i>Período de Ejecución
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium text-secondary mb-2">Fecha Inicio</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-calendar-plus text-primary"></i>
                                                </span>
                                                <input type="date" class="form-control" 
                                                       id="fecha_inicio" name="fecha_inicio" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium text-secondary mb-2">Fecha Fin</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-calendar-check text-primary"></i>
                                                </span>
                                                <input type="date" class="form-control" 
                                                       id="fecha_fin" name="fecha_fin" 
                                                        readonly
                                                        disabled
                                                        style="background:#f8f9fa;cursor:not-allowed;"
                                                        required>
                                            </div>
                                            <div class="form-text text-muted mt-1" id="fecha_fin_help">
                                                <i class="bi bi-info-circle me-1"></i>Se calcula automáticamente según el periodo seleccionado.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN: CONFIGURACIÓN -->
                        <div class="col-12 mt-2">
                            <div class="section-header mb-3">
                                <h6 class="fw-semibold text-primary mb-0">
                                    <i class="bi bi-gear me-2"></i>Configuración
                                </h6>
                                <div class="section-divider"></div>
                            </div>
                        </div>

                        <!-- Estado con badges visuales -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-secondary mb-2">
                                <i class="bi bi-toggle-on me-1"></i>Estado
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="estado_activa" 
                                       value="Activa" checked>
                                <label class="btn btn-outline-success d-flex align-items-center justify-content-center" 
                                       for="estado_activa">
                                    <i class="bi bi-play-circle me-2"></i>
                                    <span>Activa</span>
                                </label>
                                
                                <input type="radio" class="btn-check" name="estado" id="estado_cerrada" 
                                       value="Cerrada">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center" 
                                       for="estado_cerrada">
                                    <i class="bi bi-pause-circle me-2"></i>
                                    <span>Cerrada</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tipo objetivo mejorado -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-secondary mb-2">
                                <i class="bi bi-bullseye me-1"></i>Tipo de Objetivo
                            </label>
                            <select class="form-select" id="tipo_objetivo" name="tipo_objetivo" required>
                                <option value="">Seleccione el tipo de objetivo</option>
                                <option value="Aprobados">Participantes Aprobados</option>
                                <option value="Inscritos">Participantes Inscritos</option>
                                <option value="Ingresados">Participantes Ingresados al Sistema</option>
                                <option value="Masculino">Participantes por Género Masculino</option>
                                <option value="Femenino">Participantes por Género Femenino</option>
                            </select>
                        </div>

                        <!-- Ámbito -->
                        <div class="col-12">
                            <label class="form-label fw-medium text-secondary mb-2">
                                <i class="bi bi-diagram-3 me-1"></i>Ámbito
                                <span class="badge bg-secondary ms-1" style="font-size:0.7rem">Opcional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-search text-primary"></i>
                                </span>
                                <select class="form-select" id="id_ambito" name="id_ambito">
                                    <option value="" selected>
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

                    <!-- BOTONES MEJORADOS -->
                    <div class="modal-footer border-0 pt-4 mt-2 px-0">
                        <div class="d-flex justify-content-between w-100">
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" 
                                    data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-medium shadow-sm">
                                <i class="bi bi-check-circle me-2"></i>Guardar Meta
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
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-primary {
    background: var(--primary-gradient);
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.section-header {
    position: relative;
    padding-bottom: 8px;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, #667eea, transparent);
    border-radius: 2px;
}

/* Estilos para inputs */
.input-group-text {
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.input-group:focus-within .input-group-text {
    background-color: #e3f2fd;
    border-color: #667eea;
    z-index: 3;
}

.form-control, .form-select {
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.input-group:focus-within .form-control,
.input-group:focus-within .form-select {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    position: relative;
    z-index: 2;
}

.form-select:focus,
.form-control:focus:not(.input-group .form-control) {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

/* Estilos de validación */
.form-control.is-valid {
    border-color: #198754;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.btn-outline-success.active, .btn-outline-secondary.active {
    background-color: rgba(102, 126, 234, 0.1);
    border-color: #667eea;
    color: #667eea;
}

.rounded-top-4 {
    border-top-left-radius: 1rem !important;
    border-top-right-radius: 1rem !important;
}

.btn-group .btn {
    padding: 0.75rem 1rem;
    border-radius: 0.5rem !important;
    margin: 0 2px;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754 !important;
    color: white !important;
    border-color: #198754 !important;
}

.btn-check:checked + .btn-outline-secondary {
    background-color: #6c757d !important;
    color: white !important;
    border-color: #6c757d !important;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
}

input[type="date"]:hover::-webkit-calendar-picker-indicator {
    opacity: 1;
}

.form-control::placeholder {
    color: #6c757d;
    opacity: 0.7;
}

.btn-outline-success:hover:not(.active),
.btn-outline-secondary:hover:not(.active) {
    background-color: rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .btn-group .btn {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    
    .input-group-lg {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Función principal de validación para meta objetivo
function validarMetaObjetivo(input) {
    const valor = input.value;
    const errorDiv = document.getElementById('meta_objetivo_error');
    const helpDiv = document.getElementById('meta_objetivo_help');
    
    // Limpiar estilos previos
    input.classList.remove('is-invalid', 'is-valid');
    errorDiv.style.display = 'none';
    
    // Si el campo está vacío, no validar (el required se encargará)
    if (!valor) {
        return true;
    }
    
    // Convertir a número
    const numero = parseInt(valor, 10);
    
    // Validación 1: No más de 6 dígitos (para 990,000)
    if (valor.length > 6) {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'La meta no puede tener más de 6 dígitos.';
        return false;
    }
    
    // Validación 2: No mayor a 990,000
    if (numero > 990000) {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'La meta no puede superar los 990,000 participantes.';
        return false;
    }
    
    // Validación 3: Número positivo
    if (numero < 1) {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'La meta debe ser al menos 1 participante.';
        return false;
    }
    
    // Si pasa todas las validaciones
    input.classList.add('is-valid');
    
    // Formatear el número para mostrar
    if (helpDiv) {
        const formateado = new Intl.NumberFormat('es-ES').format(numero);
        helpDiv.innerHTML = `Número total de participantes que se espera alcanzar <span class="badge bg-light text-dark">${formateado}</span> (máximo 990,000)`;
    }
    
    return true;
}

// Función para limitar teclas
function validarTecla(event) {
    const input = event.target;
    const tecla = event.key;
    
    // Permitir teclas de control
    if (event.ctrlKey || event.altKey || event.metaKey || 
        tecla === 'Backspace' || tecla === 'Delete' || tecla === 'Tab' || 
        tecla === 'Escape' || tecla === 'Enter' || tecla === 'ArrowLeft' || 
        tecla === 'ArrowRight' || tecla === 'Home' || tecla === 'End') {
        return true;
    }
    
    // Verificar si ya se alcanzó el límite de 6 dígitos
    if (input.value.length >= 6 && /^[0-9]$/.test(tecla)) {
        event.preventDefault();
        // Mostrar notificación opcional
        return false;
    }
    
    // Solo permitir números
    if (!/^[0-9]$/.test(tecla)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

// Función para calcular fecha fin basada en período
function calcularFechaFin() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const periodo = document.getElementById('periodo_tiempo').value;
    const fechaFinInput = document.getElementById('fecha_fin');

    if (!fechaInicio || !periodo) {
        fechaFinInput.value = '';
        return;
    }

    const fecha = new Date(fechaInicio);

    switch (periodo) {
        case 'mensual':
            fecha.setMonth(fecha.getMonth() + 1);
            break;
        case 'trimestral':
            fecha.setMonth(fecha.getMonth() + 3);
            break;
        case 'semestral':
            fecha.setMonth(fecha.getMonth() + 6);
            break;
        case 'anual':
            fecha.setFullYear(fecha.getFullYear() + 1);
            break;
    }

    const year = fecha.getFullYear();
    const month = String(fecha.getMonth() + 1).padStart(2, '0');
    const day = String(fecha.getDate()).padStart(2, '0');

    fechaFinInput.value = `${year}-${month}-${day}`;
}

// Script principal al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-addMeta');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const metaObjetivo = document.getElementById('meta_objetivo');
    const periodoSelect = document.getElementById('periodo_tiempo');
    
    // Establecer fecha mínima para fecha inicio (hoy)
    const hoy = new Date().toISOString().split('T')[0];
    if (!fechaInicio.value) {
        fechaInicio.value = hoy;
    }
    
    // Bloquear fecha_fin hasta que se seleccione un periodo
    const fechaFinInput = document.getElementById('fecha_fin');
    fechaFinInput.disabled = true;
    fechaFinInput.style.cursor = 'not-allowed';

    // Función para actualizar el estado de fecha_fin
    function actualizarFechaFin() {
        const periodoVal = periodoSelect.value;
        if (periodoVal) {
            fechaFin.disabled = false;
            fechaFin.style.cursor = '';
            calcularFechaFin();
        } else {
            fechaFin.disabled = true;
            fechaFin.style.cursor = 'not-allowed';
            fechaFin.value = '';
        }
    }

    // Event listeners para fecha
    fechaInicio.addEventListener('change', function() {
        if (this.value) {
            fechaFin.min = this.value;
            // Recalcular fecha fin si hay período seleccionado
            if (periodoSelect.value) {
                calcularFechaFin();
            }
        }
    });

    // Fecha fin es solo lectura — no permitir cambio manual
    fechaFin.addEventListener('mousedown', function(e) {
        if (!periodoSelect.value) {
            e.preventDefault();
        }
    });
    
    // Event listener para período: recalcula automaticamente
    periodoSelect.addEventListener('change', actualizarFechaFin);
    
    // Inicializar estado
    actualizarFechaFin();
    
    // Validaciones para meta objetivo
    if (metaObjetivo) {
        metaObjetivo.addEventListener('input', function() {
            validarMetaObjetivo(this);
        });
        
        metaObjetivo.addEventListener('blur', function() {
            validarMetaObjetivo(this);
        });
        
        metaObjetivo.addEventListener('paste', function(e) {
            e.preventDefault();
            const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
            const soloNumeros = textoPegado.replace(/\D/g, '');
            
            if (soloNumeros) {
                if (soloNumeros.length <= 6 && parseInt(soloNumeros, 10) <= 990000) {
                    this.value = soloNumeros;
                    validarMetaObjetivo(this);
                }
            }
        });
    }
    
    // Mejorar select de ámbito con buscador
    const ambitoSelect = document.getElementById('id_ambito');
    if (ambitoSelect && ambitoSelect.options.length > 8) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mb-2';
        searchInput.placeholder = 'Buscar ámbito...';
        
        ambitoSelect.parentNode.insertBefore(searchInput, ambitoSelect);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = ambitoSelect.options;
            
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }
    
    // Validación al enviar formulario
    form.addEventListener('submit', function(e) {
        const metaObjetivoInput = document.getElementById('meta_objetivo');
        const fechaInicioInput = document.getElementById('fecha_inicio');
        
        // Validar meta objetivo
        if (!validarMetaObjetivo(metaObjetivoInput)) {
            e.preventDefault();
            metaObjetivoInput.focus();
            return;
        }
        
        const tipoObjetivoInput = document.getElementById('tipo_objetivo');
        if (!tipoObjetivoInput.value) {
            e.preventDefault();
            alert('Debe seleccionar el tipo de objetivo de la meta.');
            tipoObjetivoInput.focus();
            return;
        }
        
        // Validar fecha de inicio (año actual)
        const fechaInicio = new Date(fechaInicioInput.value);
        const fechaActual = new Date();
        
        if (fechaInicio.getFullYear() !== fechaActual.getFullYear()) {
            e.preventDefault();
            alert('La fecha de inicio debe estar dentro del año actual.');
            return;
        }
        
        // Cambiar texto del botón
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Guardando...';
        submitBtn.disabled = true;
    });
});
</script>
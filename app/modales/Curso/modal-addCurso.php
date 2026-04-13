<!-- Modal Agregar Curso -->
<div class="modal fade" id="modal-addCurso" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-addCurso">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <!-- Header con gradiente azul -->
            <div class="modal-header py-3 px-4 text-white" style="
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3498db 100%);
                border-bottom: none;
            ">
                <div class="d-flex align-items-center w-100">
                    <div class="modal-icon me-3 bg-white bg-opacity-25 rounded-circle p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-book-fill" viewBox="0 0 16 16">
                            <path d="M8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="modal-title mb-1 fw-bold">Añadir Nuevo Curso</h4>
                        <p class="mb-0 small opacity-75" id="modal-step-indicator">Paso 1 de 2 - Información Básica</p>
                    </div>
                </div>
                <button class="btn-close btn-close-white opacity-100" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-0">
                <div class="p-4">
                    <form id="form_addCurso" class="row g-3 needs-validation" novalidate method="POST" action="../controlador/Curso/CargarCurso.php">
                        
                        <!-- Campos ocultos para valores fijos -->
                        <input type="hidden" name="tipo_formacion" value="Unidad Curricular">
                        <input type="hidden" name="programa_formacion" value="Programa de Formación Productiva (PFP)">
                        
                        <!-- PASO 1: Información Básica y Clasificación -->
                        <div id="paso-1">
                            <!-- Sección 1: Información Básica -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2a5298" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-primary fw-semibold">Información Básica del Curso</h6>
                                </div>
                                
                                <div class="row g-3">
                                    <!-- Perfil Productivo Laboral (tb_ppl) -->
                                    <div class="col-12 mb-2" id="div-ppl">
                                        <label for="id_ppl" class="form-label fw-medium">
                                            Perfil Productivo Laboral
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="id_ppl" name="id_ppl" required>
                                                <option value="" disabled selected>Seleccione un perfil productivo</option>
                                                <?php
                                                try {
                                                    $ppls = $conn->query("SELECT id_ppl, nombre_ppl FROM tb_ppl ORDER BY nombre_ppl")->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($ppls as $ppl) {
                                                        $id = (int) $ppl['id_ppl'];
                                                        $nombre = htmlspecialchars($ppl['nombre_ppl'], ENT_QUOTES, 'UTF-8');
                                                        echo "<option value=\"{$id}\">{$nombre}</option>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<option disabled>Error al cargar perfiles</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-id_ppl"></div>
                                    </div>

                                    <!-- Nombre del Curso (catálogo tb_nombrecurso por PPL) -->
                                    <div class="col-12 mb-2">
                                        <label for="catalogo_nombre_curso" class="form-label fw-medium">
                                            Nombre del Curso
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="catalogo_nombre_curso" disabled>
                                                <option value="">Seleccione primero un perfil productivo</option>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div id="wrapper-nombre-otro" class="mt-2 d-none">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="nombre_curso_otro" class="form-label fw-medium small mb-0">Otro nombre (no está en la lista)</label>
                                                <span class="text-muted small" style="font-size: 0.7rem;">Máximo 70 caracteres</span>
                                            </div>
                                            <input class="form-control form-control-sm input-shadow" type="text" id="nombre_curso_otro" maxlength="70" placeholder="Escriba el nombre del curso" autocomplete="off">
                                        </div>
                                        <input type="hidden" name="id_nombrecurso" id="id_nombrecurso" value="">
                                        <input type="hidden" name="nombre_curso" id="nombre_curso" value="" required>
                                        <input type="hidden" name="confirmar_nuevo_curso" id="confirmar_nuevo_curso" value="0">
                                        <div class="error-feedback small text-danger mt-1" id="error-nombre_curso"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 2: Clasificación -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2a5298" class="bi bi-tags" viewBox="0 0 16 16">
                                            <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/>
                                            <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"/>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-primary fw-semibold">Clasificación del Curso</h6>
                                </div>
                                
                                <div class="row g-3">
                                    <!-- Modalidad y Sector Económico -->
                                    <div class="col-md-6 mb-2">
                                        <label for="id_modalidad" class="form-label fw-medium">
                                            Modalidad
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-laptop" viewBox="0 0 16 16">
                                                        <path d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5"/>
                                                    </svg>
                                                </span>
                                                <select class="form-select form-select-sm border-start-0 input-shadow" id="id_modalidad" name="id_modalidad" required>
                                                    <option value="" disabled selected>Seleccione una modalidad</option>
                                                    <?php
                                                    $modalidades = $conn->query("SELECT * FROM tb_modalidad ORDER BY nombre_modalidad")->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($modalidades as $modalidad) {
                                                        echo "<option value='{$modalidad['id_modalidad']}'>{$modalidad['nombre_modalidad']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-id_modalidad"></div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="id_sectoreconomico" class="form-label fw-medium">
                                            Sector Económico
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="id_sectoreconomico" name="id_sectoreconomico" required>
                                                <option value="" disabled selected>Seleccione un sector</option>
                                                <?php
                                                try {
                                                    $sectores = $conn->query("SELECT * FROM tb_sectoreconomico ORDER BY nombre_sectoreconomico")->fetchAll(PDO::FETCH_ASSOC);
                                                    if (empty($sectores)) {
                                                        $conn->exec("INSERT INTO tb_sectoreconomico (id_sectoreconomico, nombre_sectoreconomico) VALUES 
                                                                    (1, 'Comercio y Servicios'),
                                                                    (2, 'Industria'),
                                                                    (3, 'Investigación, Desarrollo, Innovación e Información')");
                                                        $sectores = $conn->query("SELECT * FROM tb_sectoreconomico ORDER BY nombre_sectoreconomico")->fetchAll(PDO::FETCH_ASSOC);
                                                    }
                                                    
                                                    foreach ($sectores as $sector) {
                                                        echo "<option value='{$sector['id_sectoreconomico']}'>{$sector['nombre_sectoreconomico']}</option>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<option disabled>Error al cargar sectores</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-id_sectoreconomico"></div>
                                    </div>

                                    <!-- Subtipo y Ámbito -->
                                    <div class="col-md-6 mb-2">
                                        <label for="id_subtipo" class="form-label fw-medium">
                                            Subtipo
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="id_subtipo" name="id_subtipo" required>
                                                <option value="" disabled selected>Seleccione un subtipo</option>
                                                <?php
                                                try {
                                                    $subtipos = $conn->query("SELECT * FROM tb_subtipo ORDER BY nombre_subtipo")->fetchAll(PDO::FETCH_ASSOC);
                                                    if (empty($subtipos)) {
                                                        $conn->exec("INSERT INTO tb_subtipo (id_subtipo, nombre_subtipo) VALUES 
                                                                    (1, 'ESPECÍFICA/TÉCNICA'),
                                                                    (2, 'TRANSVERSAL/GENÉRICA'),
                                                                    (3, 'BÁSICA/COMÚN')");
                                                        $subtipos = $conn->query("SELECT * FROM tb_subtipo ORDER BY nombre_subtipo")->fetchAll(PDO::FETCH_ASSOC);
                                                    }
                                                    
                                                    foreach ($subtipos as $subtipo) {
                                                        echo "<option value='{$subtipo['id_subtipo']}'>{$subtipo['nombre_subtipo']}</option>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<option disabled>Error al cargar subtipos</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-id_subtipo"></div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="id_ambito" class="form-label fw-medium">
                                            Ámbito
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="id_ambito" name="id_ambito" required>
                                                <option value="" disabled selected>Seleccione un ámbito</option>
                                                <?php
                                                try {
                                                    $ambitos = $conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito")->fetchAll(PDO::FETCH_ASSOC);
                                                    if (empty($ambitos)) {
                                                        $conn->exec("INSERT INTO tb_ambito (id_ambito, nombre_ambito) VALUES 
                                                                    (1, 'Centro de Formacion Socialista'),
                                                                    (2, 'Comunas'),
                                                                    (3, 'Instalaciones Militares'),
                                                                    (4, 'Unidades educativas'),
                                                                    (5, 'Sistema de Misiones y Grandes Misiones')");
                                                        $ambitos = $conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito")->fetchAll(PDO::FETCH_ASSOC);
                                                    }
                                                    
                                                    foreach ($ambitos as $ambito) {
                                                        echo "<option value='{$ambito['id_ambito']}'>{$ambito['nombre_ambito']}</option>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<option disabled>Error al cargar ámbitos</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-id_ambito"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 2: Programación y Participantes -->
                        <div id="paso-2" style="display: none;">
                            <!-- Sección 3: Programación -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2a5298" class="bi bi-calendar-event" viewBox="0 0 16 16">
                                            <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-primary fw-semibold">Programación y Horarios</h6>
                                </div>
                                
                                <div class="row g-3">
                                    <!-- Turno y Fechas -->
                                    <div class="col-md-6 mb-2">
                                        <label for="turno" class="form-label fw-medium">
                                            Turno
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <select class="form-select form-select-sm input-shadow" id="turno" name="turno" required>
                                                <option value="" disabled selected>Seleccione un turno</option>
                                                <option value="Mañana">Mañana</option>
                                                <option value="Tarde">Tarde</option>
                                            </select>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-turno"></div>
                                    </div>

                                    <!-- FECHA DE INICIO -->
                                    <div class="col-md-6 mb-2">
                                        <label for="fecha_inicio" class="form-label fw-medium">
                                            Fecha de Inicio
                                            <span class="text-danger">*</span>
                                            <small class="d-block text-muted fw-normal" style="font-size: 0.75rem;">Mínimo 7 días hábiles de antelación</small>
                                        </label>
                                        <div class="input-wrapper">
                                            <input type="date" class="form-control form-control-sm input-shadow" id="fecha_inicio" name="fecha_inicio" required>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-fecha_inicio"></div>
                                    </div>

                                    <!-- FECHA LÍMITE INICIO -->
                                    <div class="col-md-6 mb-2">
                                        <label for="fecha_limite_inicio" class="form-label fw-medium">Fecha Límite Inicio</label>
                                        <div class="input-wrapper">
                                            <input type="date" class="form-control form-control-sm input-shadow" id="fecha_limite_inicio" name="fecha_limite_inicio" readonly>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-fecha_limite_inicio"></div>
                                    </div>

                                    <!-- HORAS TOTALES (solo enteros) -->
                                    <div class="col-md-6 mb-2">
                                        <label for="horas_totales" class="form-label fw-medium">Horas Totales</label>
                                        <div class="input-wrapper">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control form-control-sm input-shadow" type="number" min="40" max="1200" step="1" name="horas_totales" id="horas_totales" placeholder="40 - 1200 horas" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" oninput="this.value = Math.min(Math.max(parseInt(this.value) || 0, 0), 1200); if(this.value === '0') this.value = ''">
                                                <span class="input-group-text">horas</span>
                                            </div>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-horas_totales"></div>
                                    </div>

                                    <!-- HORAS POR CLASE (solo enteros) - Ahora en la posición 5 -->
                                    <div class="col-md-6 mb-2">
                                        <label for="horas_por_clase" class="form-label fw-medium">Horas por Clase</label>
                                        <div class="input-wrapper">
                                            <div class="input-group input-group-sm">
                                                <select class="form-control form-control-sm input-shadow" name="horas_por_clase" id="horas_por_clase" required>
                                                    <option value="" selected disabled>Seleccione horas por clase</option>
                                                    <option value="4">4 horas</option>
                                                    <option value="5">5 horas</option>
                                                </select>
                                                <span class="input-group-text">horas</span>
                                            </div>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-horas_por_clase"></div>
                                    </div>

                                    <!-- FECHA DE CULMINACIÓN - Ahora en la posición 6 -->
                                    <div class="col-md-6 mb-2">
                                        <label for="fecha_fin" class="form-label fw-medium">
                                            Fecha de Culminación
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <input type="date" class="form-control form-control-sm input-shadow" id="fecha_fin" name="fecha_fin" readonly>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-fecha_fin"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 4: Participantes -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2a5298" class="bi bi-people" viewBox="0 0 16 16">
                                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-primary fw-semibold">Configuración de Participantes</h6>
                                </div>
                                
                                <div class="row g-3">
                                    <!-- Mínimo y Máximo de Participantes -->
                                    <div class="col-md-6 mb-2">
                                        <label for="min_participantes" class="form-label fw-medium">Mínimo de Participantes</label>
                                        <div class="input-wrapper">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control form-control-sm input-shadow" type="number" name="min_participantes" id="min_participantes" value="12" readonly style="background-color:#e9ecef; cursor:not-allowed;">
                                                <span class="input-group-text">participantes</span>
                                            </div>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-min_participantes"></div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="max_participantes" class="form-label fw-medium">
                                            Máximo de Participantes
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-wrapper">
                                            <div class="input-group input-group-sm">
                                                <select class="form-select form-select-sm input-shadow" name="max_participantes" id="max_participantes" required>
                                                    <option value="" disabled selected>Seleccione capacidad</option>
                                                    <option value="40">40 participantes</option>
                                                    <option value="50">50 participantes</option>
                                                </select>
                                            </div>
                                            <div class="focus-line"></div>
                                        </div>
                                        <div class="error-feedback small text-danger mt-1" id="error-max_participantes"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de navegación y envío -->
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">
                                    Cancelar
                                </button>
                                <div>
                                    <button type="button" id="btn-anterior" class="btn btn-outline-primary btn-sm px-4 me-2" style="display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                                        </svg>
                                        Anterior
                                    </button>
                                    <button type="button" id="btn-siguiente" class="btn btn-primary btn-sm px-4 fw-medium">
                                        Siguiente
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </button>
                                    <button id="btn-submit-addCurso" class="btn btn-primary btn-sm px-4 fw-medium" type="submit" style="
                                        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                                        border: none;
                                        box-shadow: 0 2px 8px rgba(30, 60, 114, 0.3);
                                        display: none;
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle me-2" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                        </svg>
                                        Guardar Curso
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paso1 = document.getElementById('paso-1');
    const paso2 = document.getElementById('paso-2');
    const btnSiguiente = document.getElementById('btn-siguiente');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnSubmit = document.getElementById('btn-submit-addCurso');
    const stepIndicator = document.getElementById('modal-step-indicator');
    
    btnSiguiente.addEventListener('click', function() {
        if (typeof window.syncNombreCursoAddCurso === 'function') {
            window.syncNombreCursoAddCurso();
        }
        // Obtenemos el input de nombre_curso y su estado de validación
        const nombreInput = document.getElementById('nombre_curso');
        
        // Si la validacion asincróna marcó que no es válido (ej. falta ámbito)
        if ($(nombreInput).data('valido') === false) {
            alert('Hay un problema con el nombre del curso o el ámbito. Por favor revise el mensaje en rojo debajo del nombre del curso.');
            nombreInput.classList.add('is-invalid');
            return;
        }

        // Validar campos del paso 1 antes de continuar
        const camposPaso1 = paso1.querySelectorAll('[required]');
        let valido = true;
        
        camposPaso1.forEach(campo => {
            if (!campo.value) {
                valido = false;
                campo.classList.add('is-invalid');
            } else {
                campo.classList.remove('is-invalid');
            }
        });
        
        if (valido) {
            paso1.style.display = 'none';
            paso2.style.display = 'block';
            btnSiguiente.style.display = 'none';
            btnAnterior.style.display = 'inline-block';
            btnSubmit.style.display = 'inline-block';
            stepIndicator.textContent = 'Paso 2 de 2 - Programación y Participantes';
        } else {
            alert('Por favor complete todos los campos requeridos del paso 1');
        }
    });
    
    btnAnterior.addEventListener('click', function() {
        paso1.style.display = 'block';
        paso2.style.display = 'none';
        btnSiguiente.style.display = 'inline-block';
        btnAnterior.style.display = 'none';
        btnSubmit.style.display = 'none';
        stepIndicator.textContent = 'Paso 1 de 2 - Información Básica';
    });

    // Validación antes de enviar el formulario
    document.getElementById('form_addCurso').addEventListener('submit', function(e) {
        let formularioValido = true;

        // Validar todos los campos requeridos del paso 2
        const camposPaso2 = paso2.querySelectorAll('[required]');
        camposPaso2.forEach(campo => {
            const errorDiv = document.getElementById('error-' + campo.id);
            if (!campo.value) {
                formularioValido = false;
                campo.classList.add('is-invalid');
                if (errorDiv) errorDiv.textContent = 'Este campo es obligatorio.';
            } else {
                campo.classList.remove('is-invalid');
                if (errorDiv) errorDiv.textContent = '';
            }
        });

        // Validar rango de horas_totales
        const horasInput = document.getElementById('horas_totales');
        const errorHoras = document.getElementById('error-horas_totales');
        if (horasInput.value !== '') {
            const horas = parseInt(horasInput.value);
            if (isNaN(horas) || horas < 40 || horas > 1200) {
                formularioValido = false;
                horasInput.classList.add('is-invalid');
                if (errorHoras) errorHoras.textContent = 'Las horas deben estar entre 40 y 1200.';
            } else {
                horasInput.classList.remove('is-invalid');
                if (errorHoras) errorHoras.textContent = '';
            }
        }

        if (!formularioValido) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
</script>

<style>
/* Estilos para los inputs con línea sombreada */
.input-wrapper {
    position: relative;
    width: 100%;
}

.input-shadow {
    border: 1px solid #dee2e6 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    background-color: #fff;
    border-radius: 0.375rem;
}

.input-shadow:focus {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
    outline: none;
}

/* Línea decorativa inferior */
.focus-line {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #2a5298, #3498db);
    transition: width 0.3s ease;
    border-radius: 1px;
}

.input-shadow:focus + .focus-line {
    width: 100%;
}

/* Mejora para select con icono */
.input-group .input-shadow {
    border-left: 0 !important;
}

.input-group-text {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.input-group .input-shadow:focus + .input-group-text {
    border-color: #86b7fe !important;
}

/* Mejora para selects sin input-group */
.form-select.input-shadow {
    padding: 0.25rem 2.25rem 0.25rem 0.5rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 16px 12px;
}

/* Efecto hover suave */
.input-shadow:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    border-color: #adb5bd !important;
}

/* Estilo para inputs deshabilitados */
.input-shadow:disabled {
    background-color: #e9ecef;
    box-shadow: none;
}

/* Ajuste para inputs de fecha */
input[type="date"].input-shadow {
    padding: 0.25rem 0.5rem;
}

/* Estilo consistente para todos los inputs */
.form-control.input-shadow,
.form-select.input-shadow {
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Para inputs dentro de input-group que ya tienen sombra */
.input-group-sm > .form-control.input-shadow,
.input-group-sm > .form-select.input-shadow {
    font-size: 0.875rem;
}

/* Estilo para campos inválidos */
.is-invalid {
    border-color: #dc3545 !important;
}
</style>
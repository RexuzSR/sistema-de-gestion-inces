<!-- Modal Editar Curso -->
<div class="modal fade" id="modal-editCurso" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-editCurso">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <!-- Header con gradiente azul -->
            <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3498db 100%); border-bottom: none;">
                <div class="d-flex align-items-center w-100">
                    <div class="modal-icon me-3 bg-white bg-opacity-25 rounded-circle p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="modal-title mb-1 fw-bold">Editar Curso</h4>
                        <p class="mb-0 small opacity-75">Modifique los campos que desee actualizar</p>
                    </div>
                </div>
                <button class="btn-close btn-close-white opacity-100" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-0">
                <div class="p-4">
                    <form id="form_editCurso" class="row g-3 needs-validation" novalidate method="POST">
                        
                        <!-- Campos ocultos -->
                        <input type="hidden" name="tipo_formacion-Edit" value="Unidad Curricular">
                        <input type="hidden" name="id_curso" id="id_curso-Edit">
                        <input type="hidden" name="programa_formacion-Edit" value="Programa de Formación Productiva (PFP)">
                        
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
                                <!-- Perfil Productivo Laboral -->
                                <div class="col-12 mb-2">
                                    <label for="id_ppl-Edit" class="form-label fw-medium">
                                        Perfil Productivo Laboral
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" 
                                                id="id_ppl-Edit" 
                                                name="id_ppl-Edit" 
                                                required>
                                            <option value="" disabled selected>Seleccione un perfil productivo</option>
                                            <?php
                                            $ppls = $conn->query("SELECT id_ppl, nombre_ppl FROM tb_ppl ORDER BY nombre_ppl")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($ppls as $ppl) {
                                                echo "<option value='" . (int) $ppl['id_ppl'] . "'>" . htmlspecialchars($ppl['nombre_ppl'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-id_ppl-Edit"></div>
                                </div>

                                <!-- Nombre del Curso -->
                                <div class="col-12 mb-2">
                                    <label for="catalogo_nombre_curso-Edit" class="form-label fw-medium">
                                        Nombre del Curso
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" id="catalogo_nombre_curso-Edit" disabled>
                                            <option value="">Seleccione primero un perfil productivo</option>
                                        </select>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div id="wrapper-nombre-otro-Edit" class="mt-2 d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="nombre_curso_otro-Edit" class="form-label fw-medium small mb-0">Otro nombre (no está en la lista)</label>
                                            <span class="text-muted small" style="font-size: 0.7rem;">Máximo 70 caracteres</span>
                                        </div>
                                        <input class="form-control form-control-sm input-shadow" type="text" id="nombre_curso_otro-Edit" maxlength="70" placeholder="Escriba el nombre del curso" autocomplete="off">
                                        <div class="error-feedback small text-danger mt-1" id="error-nombre_curso_otro-Edit"></div>
                                    </div>
                                    <input type="hidden" name="id_nombrecurso-Edit" id="id_nombrecurso-Edit" value="">
                                    <input type="hidden" name="nombre_curso-Edit" id="nombre_curso-Edit" value="" required>
                                    <div class="error-feedback small text-danger mt-1" id="error-catalogo_nombre_curso-Edit"></div>
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
                                    <label for="id_modalidad-Edit" class="form-label fw-medium">
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
                                            <select class="form-select form-select-sm border-start-0 input-shadow" 
                                                    id="id_modalidad-Edit" 
                                                    name="id_modalidad-Edit" 
                                                    required>
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
                                    <div class="error-feedback small text-danger mt-1" id="error-id_modalidad-Edit"></div>
                                </div>

                                <!-- Sector Económico -->
                                <div class="col-md-6 mb-2">
                                    <label for="id_sectoreconomico-Edit" class="form-label fw-medium">
                                        Sector Económico
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" 
                                                id="id_sectoreconomico-Edit" 
                                                name="id_sectoreconomico-Edit" 
                                                required>
                                            <option value="" disabled selected>Seleccione un sector</option>
                                            <?php
                                            try {
                                                $sectores = $conn->query("SELECT * FROM tb_sectoreconomico ORDER BY nombre_sectoreconomico")->fetchAll(PDO::FETCH_ASSOC);
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
                                    <div class="error-feedback small text-danger mt-1" id="error-id_sectoreconomico-Edit"></div>
                                </div>

                                <!-- Subtipo -->
                                <div class="col-md-6 mb-2">
                                    <label for="id_subtipo-Edit" class="form-label fw-medium">
                                        Subtipo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" 
                                                id="id_subtipo-Edit" 
                                                name="id_subtipo-Edit" 
                                                required>
                                            <option value="" disabled selected>Seleccione un subtipo</option>
                                            <?php
                                            try {
                                                $subtipos = $conn->query("SELECT * FROM tb_subtipo ORDER BY nombre_subtipo")->fetchAll(PDO::FETCH_ASSOC);
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
                                    <div class="error-feedback small text-danger mt-1" id="error-id_subtipo-Edit"></div>
                                </div>

                                <!-- Ámbito -->
                                <div class="col-md-6 mb-2">
                                    <label for="id_ambito-Edit" class="form-label fw-medium">
                                        Ámbito
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" 
                                                id="id_ambito-Edit" 
                                                name="id_ambito-Edit" 
                                                required>
                                            <option value="" disabled selected>Seleccione un ámbito</option>
                                            <?php
                                            try {
                                                $ambitos = $conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito")->fetchAll(PDO::FETCH_ASSOC);
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
                                    <div class="error-feedback small text-danger mt-1" id="error-id_ambito-Edit"></div>
                                </div>
                            </div>
                        </div>

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
                                <!-- Turno -->
                                <div class="col-md-6 mb-2">
                                    <label for="turno-Edit" class="form-label fw-medium">
                                        Turno
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <select class="form-select form-select-sm input-shadow" 
                                                id="turno-Edit" 
                                                name="turno-Edit" 
                                                required>
                                            <option value="" disabled selected>Seleccione un turno</option>
                                            <option value="Mañana">Mañana</option>
                                            <option value="Tarde">Tarde</option>
                                        </select>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-turno-Edit"></div>
                                </div>

                                <!-- Fecha Límite Inicio -->
                                <div class="col-md-6 mb-2">
                                    <label for="fecha_limite_inicio-Edit" class="form-label fw-medium">Fecha Límite Inicio</label>
                                    <div class="input-wrapper">
                                        <input type="date" 
                                               class="form-control form-control-sm input-shadow" 
                                               id="fecha_limite_inicio-Edit" 
                                               name="fecha_limite_inicio-Edit">
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-fecha_limite_inicio-Edit"></div>
                                </div>

                                <!-- Fecha Inicio -->
                                <div class="col-md-6 mb-2">
                                    <label for="fecha_inicio-Edit" class="form-label fw-medium">
                                        Fecha de Inicio
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="date" 
                                               class="form-control form-control-sm input-shadow" 
                                               id="fecha_inicio-Edit" 
                                               name="fecha_inicio-Edit" 
                                               required>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-fecha_inicio-Edit"></div>
                                </div>

                                <!-- Fecha Culminación -->
                                <div class="col-md-6 mb-2">
                                    <label for="fecha_fin-Edit" class="form-label fw-medium">
                                        Fecha de Culminación
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="date" 
                                               class="form-control form-control-sm input-shadow" 
                                               id="fecha_fin-Edit" 
                                               name="fecha_fin-Edit" 
                                               required>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-fecha_fin-Edit"></div>
                                </div>

                                <!-- Horas por Clase (mismo criterio que alta: solo 4 u 5) -->
                                <div class="col-md-6 mb-2">
                                    <label for="horas_por_clase-Edit" class="form-label fw-medium">
                                        Horas por Clase
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <div class="input-group input-group-sm">
                                            <select class="form-select form-select-sm input-shadow" 
                                                    name="horas_por_clase-Edit" 
                                                    id="horas_por_clase-Edit" 
                                                    required>
                                                <option value="" disabled>Seleccione horas por clase</option>
                                                <option value="4">4 horas</option>
                                                <option value="5">5 horas</option>
                                            </select>
                                            <span class="input-group-text">horas</span>
                                        </div>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-horas_por_clase-Edit"></div>
                                    <small class="text-muted">Solo bloques de 4 o 5 horas (reglamento de formación).</small>
                                </div>

                                <!-- Horas Totales (READONLY) -->
                                <div class="col-md-6 mb-2">
                                    <label for="horas_totales-Edit" class="form-label fw-medium">Horas Totales</label>
                                    <div class="input-wrapper">
                                        <div class="input-group input-group-sm">
                                            <input class="form-control form-control-sm input-shadow" 
                                                   type="number" 
                                                   step="1" 
                                                   min="0" 
                                                   name="horas_totales-Edit" 
                                                   id="horas_totales-Edit" 
                                                   placeholder="Calculado automáticamente"
                                                   readonly>
                                            <span class="input-group-text">horas</span>
                                        </div>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-horas_totales-Edit"></div>
                                    <small class="text-muted">Calculado automáticamente</small>
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
                                <!-- Mínimo Participantes -->
                                <div class="col-md-6 mb-2">
                                    <label for="min_participantes-Edit" class="form-label fw-medium">
                                        Mínimo de Participantes
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <div class="input-group input-group-sm">
                                            <input class="form-control form-control-sm input-shadow" 
                                                   type="number" 
                                                   min="12" 
                                                   max="40" 
                                                   name="min_participantes-Edit" 
                                                   id="min_participantes-Edit" 
                                                   value="12"
                                                   required>
                                            <span class="input-group-text">personas</span>
                                        </div>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-min_participantes-Edit"></div>
                                </div>

                                <!-- Máximo Participantes -->
                                <div class="col-md-6 mb-2">
                                    <label for="max_participantes-Edit" class="form-label fw-medium">
                                        Máximo de Participantes
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <div class="input-group input-group-sm">
                                            <input class="form-control form-control-sm input-shadow" 
                                                   type="number" 
                                                   min="12" 
                                                   max="40" 
                                                   name="max_participantes-Edit" 
                                                   id="max_participantes-Edit" 
                                                   placeholder="Ej: 30"
                                                   required>
                                            <span class="input-group-text">personas</span>
                                        </div>
                                        <div class="focus-line"></div>
                                    </div>
                                    <div class="error-feedback small text-danger mt-1" id="error-max_participantes-Edit"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">
                                    Cancelar
                                </button>
                                <button id="btn-submit-editCurso" class="btn btn-primary btn-sm px-4 fw-medium" type="submit" style="
                                    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                                    border: none;
                                    box-shadow: 0 2px 8px rgba(30, 60, 114, 0.3);
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle me-2" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                    </svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ESTILOS COPIADOS DE ADDCURSO */
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

.form-select.input-shadow {
    padding: 0.25rem 2.25rem 0.25rem 0.5rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 16px 12px;
}

.input-shadow:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    border-color: #adb5bd !important;
}

.input-shadow:disabled, .input-shadow[readonly] {
    background-color: #e9ecef;
    box-shadow: none;
    color: #6c757d;
}

input[type="date"].input-shadow {
    padding: 0.25rem 0.5rem;
}

.form-control.input-shadow,
.form-select.input-shadow {
    font-size: 0.875rem;
    line-height: 1.5;
}

.input-group-sm > .form-control.input-shadow,
.input-group-sm > .form-select.input-shadow {
    font-size: 0.875rem;
}

.is-invalid {
    border-color: #dc3545 !important;
}
</style>
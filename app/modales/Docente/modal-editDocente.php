<!-- modal-editDocente - ESTILO CLARO PROFESIONAL -->
<div class="modal fade" id="modal-editDocente" tabindex="-1" aria-labelledby="modal-editDocenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <!-- HEADER -->
            <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #eef2ff;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: #0f172a; font-size: 1.35rem;">Editar Docente</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.75rem;">Actualizar información del docente</p>
                    </div>
                </div>
                <button type="button" class="btn-close opacity-75" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4 pt-3">
                <form id="form-editDocente">
                    <input type="hidden" id="idDocente" name="id_docente">

                    <!-- SECCIÓN 1: DATOS PERSONALES -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="fw-semibold" style="color: #1e293b; font-size: 0.85rem;">IDENTIDAD</span>
                            <hr class="flex-grow-1 m-0" style="border-color: #e2e8f0;">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">CÉDULA <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="cedula-Edit" name="cedula" required maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);" placeholder="Ej: 12345678" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-cedula-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">TELÉFONO</label>
                                <div class="d-flex gap-2">
                                    <select class="form-select bg-light border-0 rounded-3 py-2" id="prefijo_telefono-Edit" style="width: 85px; background-color: #f8fafc;">
                                        <option value="0412">0412</option>
                                        <option value="0414">0414</option>
                                        <option value="0416">0416</option>
                                        <option value="0424">0424</option>
                                        <option value="0426">0426</option>
                                        <option value="0212">0212</option>
                                        <option value="0293">0293</option>
                                    </select>
                                    <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="telefono_numero-Edit" placeholder="1234567" maxlength="7" style="background-color: #f8fafc;">
                                </div>
                                <input type="hidden" name="telefono" id="telefono-Edit">
                                <div class="invalid-feedback small" id="error-telefono-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">PRIMER NOMBRE <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="primer_nombre-Edit" name="primer_nombre" required placeholder="Ej: Héctor" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-primer_nombre-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">SEGUNDO NOMBRE</label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="segundo_nombre-Edit" name="segundo_nombre" placeholder="Opcional" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-segundo_nombre-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">PRIMER APELLIDO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="primer_apellido-Edit" name="primer_apellido" required placeholder="Ej: Guzmán" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-primer_apellido-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">SEGUNDO APELLIDO</label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="segundo_apellido-Edit" name="segundo_apellido" placeholder="Opcional" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-segundo_apellido-Edit"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: CONTACTO Y DETALLES -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="fw-semibold" style="color: #1e293b; font-size: 0.85rem;">CONTACTO</span>
                            <hr class="flex-grow-1 m-0" style="border-color: #e2e8f0;">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">CORREO <span class="text-danger">*</span></label>
                                <input type="email" class="form-control bg-light border-0 rounded-3 py-2" id="correo-Edit" name="correo" required placeholder="ejemplo@dominio.com" style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-correo-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <?php
                                $maxFechaNac = date('Y-m-d', strtotime('-15 years'));
                                $minFechaNac = date('Y-m-d', strtotime('-75 years'));
                                ?>
                                <label class="form-label fw-semibold small text-secondary mb-1">FECHA NACIMIENTO <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-light border-0 rounded-3 py-2" id="fechaNacimiento-Edit" name="fecha_nacimiento" min="<?= $minFechaNac ?>" max="<?= $maxFechaNac ?>" required style="background-color: #f8fafc;">
                                <div class="invalid-feedback small" id="error-fechaNacimiento-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-2">GÉNERO <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="genero" id="genero-masculino-Edit" value="Masculino">
                                        <label class="form-check-label small" for="genero-masculino-Edit">Masculino</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="genero" id="genero-femenino-Edit" value="Femenino">
                                        <label class="form-check-label small" for="genero-femenino-Edit">Femenino</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback small" id="error-genero"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">TIPO DOCENTE <span class="text-danger">*</span></label>
                                <select class="form-select bg-light border-0 rounded-3 py-2" id="tipo_docente-Edit" name="tipo_docente" required style="background-color: #f8fafc;">
                                    <option value="">Seleccione...</option>
                                    <option value="Fijo">Fijo</option>
                                    <option value="Contratado">Contratado</option>
                                    <option value="PDL">PDL</option>
                                    <option value="Voluntario">Voluntario</option>
                                </select>
                                <div class="invalid-feedback small" id="error-tipo_docente-Edit"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">EDAD</label>
                                <input type="text" class="form-control bg-light border-0 rounded-3 py-2" id="edad-Edit" readonly style="background-color: #f1f5f9; color: #64748b;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary mb-1">ESTADO <span class="text-danger">*</span></label>
                                <select class="form-select bg-light border-0 rounded-3 py-2" id="estado_docente-Edit" name="estado_docente" required style="background-color: #f8fafc;">
                                    <option value="">Seleccione...</option>
                                    <option value="Disponible">Disponible</option>
                                    <option value="En formación">En formación</option>
                                    <option value="Asignado">Asignado</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <div class="invalid-feedback small" id="error-estado_docente-Edit"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn px-4 py-2 rounded-3" data-bs-dismiss="modal" style="background-color: #f1f5f9; color: #475569; font-weight: 500; font-size: 0.85rem;">CERRAR</button>
                <button type="submit" form="form-editDocente" class="btn px-4 py-2 rounded-3" id="btn-editDocente-submit" style="background-color: #0f172a; color: white; font-weight: 500; font-size: 0.85rem;">ACTUALIZAR DATOS</button>
            </div>
        </div>
    </div>
</div>
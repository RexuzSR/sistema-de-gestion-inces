<!-- Modal Asignar Docente a Curso -->
<div
    class="modal fade"
    id="modal-AsignarDocenteCurso"
    tabindex="-1"
    aria-hidden="true"
    aria-labelledby="label-modal-AsignarDocenteCurso"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <div class="d-flex justify-content-center align-items-center w-100">
                    <h4 class="modal-title text-center">Asignar Docente</h4>
                </div>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form class="formAsignarDocente" method="POST">
                    <div class="row">
                        <div class="modal-section">
                            <div class="modal-section-title">Datos del Curso</div>
                        </div>
                        <div class="col-md-6">
                            <p><b>Nombre del Curso: </b><span id="asignar-nombre-curso"></span></p>
                            <p><b>Modalidad: </b><span id="asignar-modalidad"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><b>Área Formativa: </b><span id="asignar-area-formativa"></span></p>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="docente" class="form-label"><b>Seleccionar Docente</b></label>
                            <select class="form-select form-select-sm" id="asignarDocente" name="docente" required>
                                <option value="">Seleccione</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, seleccione un docente.
                            </div>
                        </div>

                        <div class="col-12 mb-4 d-none" id="info-docente-seleccionado">
                            <div class="card border-0" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3">
                                    <h6 class="mb-2" style="color: #0281fe;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>Información del Docente</h6>
                                    <div class="row g-2 text-muted" style="font-size: 0.85rem;">
                                        <div class="col-6">
                                            <strong style="color: #475569;">Cédula:</strong> <span id="info-docente-cedula"></span>
                                        </div>
                                        <div class="col-6">
                                            <strong style="color: #475569;">Edad:</strong> <span id="info-docente-edad"></span> años
                                        </div>
                                        <div class="col-12 mt-1">
                                            <strong style="color: #475569;">Correo:</strong> <span id="info-docente-correo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="cursoIdHidden" name="cursoId">

                        <div class="col-12">
                            <button class="btn-submmit text-white" type="submit">Asignar docente al curso</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

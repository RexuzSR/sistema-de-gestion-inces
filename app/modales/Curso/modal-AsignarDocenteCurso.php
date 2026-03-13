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
                            <p><b>Motor: </b><span id="asignar-motor"></span></p>
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

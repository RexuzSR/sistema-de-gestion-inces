<div class="modal-body">
    <form id="formAsignarCursoDocente" class="needs-validation" novalidate>
        <input type="hidden" id="id_docente" name="id_docente">
        <div class="row">
            <div class="col-md-6">
                <p><b>Nombre: </b><span id="asignar-nombre-docente"></span></p>
                <p><b>Cédula: </b><span id="asignar-cedula-docente"></span></p>
            </div>
            <div class="col-md-6">
                <p><b>Apellido: </b><span id="asignar-apellido-docente"></span></p>
                <p><b>Email: </b><span id="asignar-correo-docente"></span></p>
            </div>
            <div class="col-12 mb-4">
                <label for="asignarCursoDocente" class="form-label"><b>Cursos Disponibles</b></label>
                <select class="form-select" id="asignarCursoDocente" name="curso" required>
                    <option value="" disabled selected>Cargando cursos disponibles...</option>
                </select>
                <div class="invalid-feedback">Por favor seleccione un curso.</div>
            </div>
        </div>

        <hr class="my-4">

        <div>
            <h5><i class="bi bi-list-ul me-2"></i> Cursos Asignados</h5>
            <div id="cursos-asignados" class="mt-3"></div>
        </div>

        <div class="modal-footer mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cerrar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i> Guardar Asignación
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="modal-participanteCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #020d19;">
                <h5 class="modal-title">Gestión de Participantes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Cabecera con estado -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Cupos: <span id="cupos-actuales" class="badge bg-primary">0/0</span></h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <span id="estado-curso" class="badge"></span>
                    </div>
                </div>

                <!-- Sección de búsqueda y agregar -->
                <div class="mb-4">
                    <h6 class="mb-3">Agregar Participantes</h6>
                    <input type="text" id="busqueda-participantes" 
                           class="form-control mb-3" 
                           placeholder="Buscar por nombre, apellido o cédula...">
                    <div id="seccion-agregar" class="scrollable-list"></div>
                </div>

                <!-- Sección de inscritos -->
                <div class="mt-4">
                    <h6 class="mb-3">Participantes Inscritos</h6>
                    <div id="seccion-inscritos" class="scrollable-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>
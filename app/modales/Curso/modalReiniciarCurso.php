<div class="modal fade" id="modalReiniciarCurso" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #020d19;">
        <h5 class="modal-title">Reiniciar Curso</h5>
        <button type="button" class="btn-close btn-close-white btn-cancel-reiniciar" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Aviso dinámico: JS escribe aquí según estado del curso -->
        <div id="avisoReinicio" class="mb-2"></div>

        <form id="formReiniciarCurso">
          <input type="hidden" id="reiniciarCursoId">

          <!-- Checkbox: solo habilitado para Diferido -->
          <div class="mb-3" id="checkboxMantenerContainer">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="chkMantenerParticipantes" disabled>
              <label class="form-check-label" for="chkMantenerParticipantes">
                Mantener participantes actuales
              </label>
              <div>
                <small class="text-muted" id="textoCheckbox">
                  Solo disponible para cursos en estado Diferido.
                </small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="nuevaFechaInicio" class="form-label fw-medium">
              Nueva Fecha de Inicio <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="nuevaFechaInicio" required>
            <div class="text-danger small mt-1" id="errorFechaInicio" style="display:none;"></div>
          </div>

          <div class="mb-3">
            <label for="fechaLimiteInicio" class="form-label fw-medium">
              Fecha Límite de Inicio
              <span class="badge bg-secondary ms-1" style="font-size:0.7rem;">Auto</span>
            </label>
            <input type="date" class="form-control" id="fechaLimiteInicio"
                   readonly style="background-color:#e9ecef; cursor:not-allowed;">
            <small class="text-muted">Se calcula automáticamente: 9 días después del inicio.</small>
          </div>

          <div class="mb-3">
            <label for="nuevaFechaFin" class="form-label fw-medium">
              Nueva Fecha de Finalización <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="nuevaFechaFin" required>
            <div class="text-danger small mt-1" id="errorFechaFin" style="display:none;"></div>
            <small class="text-muted">Mínimo 21 días después de la fecha de inicio.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancel-reiniciar">Cancelar</button>
        <button type="button" class="btn text-white" id="confirmarReinicioCurso"
                style="background-color:#0A369D;">Reiniciar Curso</button>
      </div>
    </div>
  </div>
</div>
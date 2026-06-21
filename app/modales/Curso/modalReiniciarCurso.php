<div class="modal fade" id="modalReiniciarCurso" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <!-- Modal Header - Gradient Blue -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #0A369D 0%, #1a4fc3 100%); border-bottom: none;">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-arrow-repeat me-2"></i>Reiniciar Curso
        </h5>
        <button type="button" class="btn-close btn-close-white btn-cancel-reiniciar" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4" style="background-color: #f8fafc;">
        <!-- Warning Alert -->
        <div id="avisoReinicio" class="mb-3"></div>

        <form id="formReiniciarCurso">
          <input type="hidden" id="reiniciarCursoId">

          <!-- Checkbox section -->
          <div id="checkboxMantenerContainer" class="mb-4">
            <div class="form-check p-3 rounded-3" style="background-color: #e8f0fe; border-left: 4px solid #0A369D;">
              <input class="form-check-input" type="checkbox" id="chkMantenerParticipantes" disabled style="border-color: #0A369D;">
              <label class="form-check-label fw-semibold" for="chkMantenerParticipantes" style="color: #0A369D;">
                Mantener participantes actuales
              </label>
              <div>
                <small class="text-muted" id="textoCheckbox">
                  Solo disponible para cursos en estado Diferido.
                </small>
              </div>
            </div>
          </div>

          <!-- Classification Section -->
          <div class="mb-4 border rounded-3 p-4" style="border-color: #dee2e6 !important; background-color: white;">
            <h6 class="mb-3 fw-bold" style="color: #0A369D;">
              <i class="bi bi-tags me-2"></i>Clasificación del Curso
            </h6>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="id_modalidad_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Modalidad <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="id_modalidad_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione una modalidad</option>
                  <?php
                  $modalidades = $conn->query("SELECT * FROM tb_modalidad ORDER BY nombre_modalidad")->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($modalidades as $modalidad) {
                      echo "<option value='" . (int)$modalidad['id_modalidad'] . "'>" . htmlspecialchars($modalidad['nombre_modalidad'], ENT_QUOTES, 'UTF-8') . "</option>";
                  }
                  ?>
                </select>
                <div class="text-danger small mt-1" id="error-id_modalidad_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="id_sectoreconomico_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Sector Económico <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="id_sectoreconomico_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione un sector</option>
                  <?php
                  $sectores = $conn->query("SELECT * FROM tb_sectoreconomico ORDER BY nombre_sectoreconomico")->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($sectores as $sector) {
                      echo "<option value='" . (int)$sector['id_sectoreconomico'] . "'>" . htmlspecialchars($sector['nombre_sectoreconomico'], ENT_QUOTES, 'UTF-8') . "</option>";
                  }
                  ?>
                </select>
                <div class="text-danger small mt-1" id="error-id_sectoreconomico_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="id_subtipo_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Subtipo <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="id_subtipo_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione un subtipo</option>
                  <?php
                  $subtipos = $conn->query("SELECT * FROM tb_subtipo ORDER BY nombre_subtipo")->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($subtipos as $subtipo) {
                      echo "<option value='" . (int)$subtipo['id_subtipo'] . "'>" . htmlspecialchars($subtipo['nombre_subtipo'], ENT_QUOTES, 'UTF-8') . "</option>";
                  }
                  ?>
                </select>
                <div class="text-danger small mt-1" id="error-id_subtipo_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="id_ambito_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Ámbito <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="id_ambito_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione un ámbito</option>
                  <?php
                  $ambitos = $conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito")->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($ambitos as $ambito) {
                      echo "<option value='" . (int)$ambito['id_ambito'] . "'>" . htmlspecialchars($ambito['nombre_ambito'], ENT_QUOTES, 'UTF-8') . "</option>";
                  }
                  ?>
                </select>
                <div class="text-danger small mt-1" id="error-id_ambito_Reinicio" style="display:none;"></div>
              </div>
            </div>
          </div>

          <!-- Schedule Section -->
          <div class="mb-4 border rounded-3 p-4" style="border-color: #dee2e6 !important; background-color: white;">
            <h6 class="mb-3 fw-bold" style="color: #0A369D;">
              <i class="bi bi-calendar-week me-2"></i>Programación y Horarios
            </h6>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="preimpreso_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Código Preimpreso <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-2" id="preimpreso_Reinicio" placeholder="Ej. 111-2026" maxlength="50" required style="border-color: #cbd5e1; border-radius: 8px;">
                <small class="text-muted d-block mt-1" id="preimpreso-anterior-help">Preimpreso anterior: -</small>
                <div class="text-danger small mt-1" id="error-preimpreso_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="turno_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Turno <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="turno_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione un turno</option>
                  <option value="Mañana">Mañana</option>
                  <option value="Tarde">Tarde</option>
                </select>
                <div class="text-danger small mt-1" id="error-turno_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="nuevaFechaInicio" class="form-label fw-semibold" style="color: #1e293b;">Nueva Fecha de Inicio <span class="text-danger">*</span></label>
                <input type="text" class="form-control border-2" id="nuevaFechaInicio" required placeholder="Seleccione una fecha" autocomplete="off" style="border-color: #cbd5e1; border-radius: 8px;">
                <div id="textoInicioMinimo" class="form-text text-muted">Inicio mínimo 7 días después de hoy.</div>
                <div class="text-danger small mt-1" id="errorFechaInicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="fechaLimiteInicio" class="form-label fw-semibold" style="color: #1e293b;">
                  Fecha Límite de Inicio
                  <span class="badge ms-1" style="background-color: #0A369D; font-size: 0.7rem;">Auto</span>
                </label>
                <input type="date" class="form-control border-2" id="fechaLimiteInicio" readonly style="background-color: #f1f5f9; border-color: #cbd5e1; border-radius: 8px; cursor: not-allowed;">
                <small class="text-muted">Se calcula automáticamente: 9 días después de la fecha de inicio.</small>
              </div>
              <div class="col-12 col-md-6">
                <label for="horas_por_clase_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Horas por Clase <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="horas_por_clase_Reinicio" required style="border-color: #cbd5e1; border-radius: 8px;">
                  <option value="" disabled selected>Seleccione horas por clase</option>
                  <option value="4">4 horas</option>
                  <option value="5">5 horas</option>
                </select>
                <div class="text-danger small mt-1" id="error-horas_por_clase_Reinicio" style="display:none;"></div>
                <small class="text-muted">Solo bloques de 4 o 5 horas.</small>
              </div>
              <div class="col-12 col-md-6">
                <label for="horas_totales_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Horas Totales</label>
                <input type="text" class="form-control border-2" id="horas_totales_Reinicio" readonly style="background-color: #f1f5f9; border-color: #cbd5e1; border-radius: 8px; cursor: not-allowed;">
                <small class="text-muted">Se calcula automáticamente según fecha de inicio, fecha de fin y horas por clase.</small>
              </div>
              <div class="col-12 col-md-6">
                <label for="nuevaFechaFin" class="form-label fw-semibold" style="color: #1e293b;">Nueva Fecha de Finalización <span class="text-danger">*</span></label>
                <input type="date" class="form-control border-2" id="nuevaFechaFin" readonly style="background-color: #f1f5f9; border-color: #cbd5e1; border-radius: 8px; cursor: not-allowed;">
                <div class="text-danger small mt-1" id="errorFechaFin" style="display:none;"></div>
                <small class="text-muted">Se calcula automáticamente según fecha de inicio, horas totales y horas por clase.</small>
              </div>
            </div>
          </div>

          <!-- Participants Section -->
          <div class="mb-4 border rounded-3 p-4" style="border-color: #dee2e6 !important; background-color: white;">
            <h6 class="mb-3 fw-bold" style="color: #0A369D;">
              <i class="bi bi-people me-2"></i>Configuración de Participantes
            </h6>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="min_participantes_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Mínimo de Participantes <span class="text-danger">*</span></label>
                <input type="number" class="form-control border-2" id="min_participantes_Reinicio" min="12" max="40" placeholder="12" required style="border-color: #cbd5e1; border-radius: 8px;">
                <div class="text-danger small mt-1" id="error-min_participantes_Reinicio" style="display:none;"></div>
              </div>
              <div class="col-12 col-md-6">
                <label for="max_participantes_Reinicio" class="form-label fw-semibold" style="color: #1e293b;">Máximo de Participantes <span class="text-danger">*</span></label>
                <input type="number" class="form-control border-2" id="max_participantes_Reinicio" min="12" max="40" placeholder="40" required style="border-color: #cbd5e1; border-radius: 8px;">
                <div class="text-danger small mt-1" id="error-max_participantes_Reinicio" style="display:none;"></div>
              </div>
            </div>
          </div>

          <!-- Note: Removed duplicate fields (fechaLimiteInicio and nuevaFechaFin appeared twice) -->
        </form>
      </div>
      
      <!-- Modal Footer -->
      <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #dee2e6;">
        <button type="button" class="btn btn-outline-secondary px-4 btn-cancel-reiniciar" style="border-radius: 8px; border-color: #cbd5e1;">
          Cancelar
        </button>
        <button type="button" class="btn px-4 text-white" id="confirmarReinicioCurso"
                style="background: linear-gradient(135deg, #0A369D 0%, #1a4fc3 100%); border-radius: 8px; border: none;">
          <i class="bi bi-arrow-repeat me-2"></i>Reiniciar Curso
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Required CSS for icons (Bootstrap Icons) - Add to your head section -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
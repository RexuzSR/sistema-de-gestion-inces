<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmarGuardarModal" tabindex="-1" aria-labelledby="confirmarGuardarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #020d19;">
        <h5 class="modal-title text-white" id="confirmarGuardarModalLabel">Confirmar Guardado de Notas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas guardar las siguientes notas?</p>
        <ul id="confirmarListaNotas">
          <!-- Las notas se agregarán aquí dinámicamente con JavaScript -->
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancel-modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="confirmarGuardarBtn">Confirmar</button>
      </div>
    </div>
  </div>
</div>

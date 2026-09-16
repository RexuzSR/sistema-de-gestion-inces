<!-- Modal Eliminar Usuario -->
<div
class="modal fade"
id="modal-deleteUsuario"
tabindex="-1"
aria-hidden="true"
aria-labelledby="label-modal-deleteUsuario"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <div class="d-flex justify-content-center align-items-center w-100">
                    <h4 class="modal-title text-center">Porfavor Confirma tu Acción</h4>
                </div>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este usuario?</p>
            
            <!-- Campo de Contraseña Maestra -->
            <div class="mt-3">
                <label for="master_password_delete" class="form-label" style="color: #64748b; font-size: 0.85rem; font-weight: 600;">
                    🔒 Contraseña Maestra
                </label>
                <input type="password" 
                       class="form-control form-control-sm" 
                       id="master_password_delete" 
                       name="master_password" 
                       placeholder="Ingrese la contraseña maestra para confirmar"
                       required
                       autocomplete="off"
                       style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;">
                <div class="form-text" style="color: #94a3b8; font-size: 0.75rem;">
                    Se requiere contraseña maestra para eliminar usuarios.
                </div>
            </div>
            </div>

            <div class="modal-footer">
            <button class="cancel-modal" data-bs-dismiss="modal">Cancelar</button>
            <button class="delete-modal">Eliminar Usuario</button>
            </div>
        </div>
    </div>
</div>

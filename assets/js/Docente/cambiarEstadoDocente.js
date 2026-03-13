// Script para cambiar el estado del docente (Activar/Inactivar) - VERSIÓN MEJORADA
$(document).ready(function() {
    // Evento para el botón de cambiar estado (activar/inactivar)
    $(document).on('click', '.button.activate, .button.inactivate', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation(); // Prevenir múltiples ejecuciones
        
        const docenteId = $(this).data('id');
        const estadoActual = $(this).data('estado');
        const esInactivo = estadoActual === 'Inactivo';
        
        // Determinar el nuevo estado
        const nuevoEstado = esInactivo ? 'Disponible' : 'Inactivo';
        const accionTexto = esInactivo ? 'activar' : 'inactivar';
        
        // Mensaje de confirmación
        let mensajeConfirmacion = `¿Está seguro de que desea ${accionTexto} este docente?`;
        
        // Advertencia adicional si se va a inactivar
        if (!esInactivo) {
            mensajeConfirmacion += '\n\n⚠️ Nota: Si el docente tiene cursos asignados, no podrá ser inactivado.';
        }
        
        if (confirm(mensajeConfirmacion)) {
            cambiarEstadoDocente(docenteId, nuevoEstado, this);
        }
    });
    
    // Función para cambiar el estado del docente via AJAX
    function cambiarEstadoDocente(docenteId, nuevoEstado, buttonElement) {
        const $button = $(buttonElement);
        const originalHtml = $button.html();
        const esInactivar = nuevoEstado === 'Inactivo';
        
        // Mostrar spinner en el botón durante la petición
        $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $button.prop('disabled', true);
        
        $.ajax({
            url: '../../app/controlador/Docente/cambiarEstadoDocente.php',
            method: 'POST',
            data: {
                id_docente: docenteId,
                nuevo_estado: nuevoEstado
            },
            dataType: 'json',
            success: function(response) {
                // Restaurar el botón
                $button.html(originalHtml);
                $button.prop('disabled', false);
                
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Recargar la tabla de docentes según las funciones disponibles
                    if (typeof cargarTablaConFiltros === 'function') {
                        cargarTablaConFiltros();
                    } else if (typeof cargarTabla === 'function') {
                        cargarTabla();
                    } else if (typeof cargarRegistros === 'function') {
                        // Si usas la función del archivo unificado con paginación
                        cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, currentSearchTerm);
                    } else {
                        // Recarga simple como fallback
                        location.reload();
                    }
                    
                } else {
                    // Manejar errores específicos
                    const errorMsg = response.message || 'Error al cambiar el estado';
                    
                    // Si es error por asignaciones activas, mostrar mensaje más detallado
                    if (esInactivar && (errorMsg.includes('asignado') || errorMsg.includes('cursos') || errorMsg.includes('reasignar'))) {
                        // Usar SweetAlert2 si está disponible para mejor presentación
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'No se puede inactivar',
                                html: `
                                    <div class="text-start">
                                        <p class="mb-3"><strong>${errorMsg}</strong></p>
                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                <strong>Acciones requeridas:</strong>
                                            </small>
                                            <ul class="small mt-2 mb-0">
                                                <li>Revise las asignaciones del docente</li>
                                                <li>Reasigne los cursos a otros docentes</li>
                                                <li>O espere a que finalicen los cursos actuales</li>
                                            </ul>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#dc3545',
                                allowOutsideClick: false
                            });
                        } else {
                            // Fallback a toastr con mensaje más largo
                            toastr.error(errorMsg, 'No se puede inactivar', { 
                                timeOut: 8000,
                                closeButton: true 
                            });
                        }
                    } else {
                        toastr.error(errorMsg);
                    }
                }
            },
            error: function(xhr, status, error) {
                // Restaurar el botón
                $button.html(originalHtml);
                $button.prop('disabled', false);
                
                console.error('Error en la solicitud AJAX:', error);
                
                // Mostrar error según el tipo
                if (xhr.status === 403) {
                    toastr.error('No tiene permisos para realizar esta acción');
                } else if (xhr.status === 404) {
                    toastr.error('Recurso no encontrado');
                } else {
                    toastr.error('Error de conexión con el servidor');
                }
            }
        });
    }
});
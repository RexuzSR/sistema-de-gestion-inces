// La función cargarTabla ha sido eliminada por redundancia. 
// Se utiliza la versión unificada en tabla-participantes.js (cargarRegistros / aplicarFiltros).


$(document).ready(function () {
    $(document).on('click', '.button.delete', function () {
        const idParticipante = $(this).data('id');
        $('#modal-deleteParticipante').data('id', idParticipante);
    });

    $(document).on('click', '.delete-modal', function () {
        const idParticipante = $('#modal-deleteParticipante').data('id');

        if (idParticipante) {
            $.ajax({
                url: '../../app/controlador/eliminarParticipante.php',
                type: 'POST',
                data: { id: idParticipante },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#modal-deleteParticipante').modal('hide');
                        toastr.success("¡El participante se eliminó correctamente!");
                        if (typeof aplicarFiltros === 'function') {
                            aplicarFiltros();
                        } else if (typeof cargarRegistros === 'function') {
                            cargarRegistros(1, 8);
                        }
                    } else {
                        toastr.error(data.message);
                        $('#modal-deleteParticipante').modal('hide');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX:', error);
                    toastr.error("Ocurrió un error al procesar la solicitud.");
                }
            });
        } else {
            toastr.error("El ID del participante no es válido");
        }
    });
});
$(document).on('click', '.view', function() {
    let id_participante = $(this).attr("data-id");
    
    // Mostrar spinner de carga
    $('#historial-content').html(`
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </td>
        </tr>
    `);

    // Llamada AJAX
    $.ajax({
        url: '../../app/controlador/ParticipanteCurso/historialParticipante.php',
        method: 'POST',
        dataType: 'json',
        data: { id_participante: id_participante },
        success: function(response) {
            if (response.success) {
                let html = '';
                
                if (response.data.length > 0) {
                    response.data.forEach(registro => {
                        html += `
                            <tr>
                                <td>${registro.nombre_curso}</td>
                                <td>${registro.fecha_inscripcion}</td>
                                <td>${registro.fecha_inicio}</td>
                                <td>${registro.fecha_fin}</td>
                                <td><span class="badge ${estiloEstado(registro.estatus_participante)}">${registro.estatus_participante}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay registros históricos</td>
                        </tr>
                    `;
                }
                
                $('#historial-content').html(html);
            } else {
                $('#historial-content').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">${response.message}</td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            $('#historial-content').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">Error: ${xhr.status} - ${error}</td>
                </tr>
            `);
        }
    });
});

// Función para asignar el estilo del badge dependiendo del estado del participante
function estiloEstado(estado) {
    switch (estado) {
        case 'Aprobado':
            return 'bg-success text-white'; // Verde con texto blanco
        case 'Reprobado':
            return 'bg-danger text-white'; // Rojo con texto blanco
        case 'Ausente':
            return 'bg-warning text-dark'; // Amarillo con texto oscuro
        default:
            return 'bg-secondary text-white'; // Gris si el estado es desconocido
    }
}

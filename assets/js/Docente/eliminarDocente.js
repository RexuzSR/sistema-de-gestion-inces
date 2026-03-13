// Función para cargar la tabla de participantes
function cargarTabla() {

    $.ajax({
        url: '../../app/controlador/Docente/obtenerDocentes.php', 
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            
            let rows = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(participante => {
                    const estado = participante.estado === "En formación"
? '<span class="badge bg-success">En formación</span>'
: participante.estado === "Disponible"
    ? '<span class="badge bg-warning text-black">Disponible</span>'
    : participante.estado === "Asignado"
        ? '<span class="badge bg-primary">Asignado</span>'
        : participante.estado;


                    rows += `
                        <tr>
                            <td>${participante.id_docente}</td>
                            <td>${participante.cedula}</td>
                            <td>${participante.nombre}</td>
                            <td>${participante.apellido}</td>
                            <td>${participante.genero}</td>
                            <td>${participante.edad}</td>
                            <td>${participante.correo}</td>
                            <td>${estado}</td>
                            <td>
                                <!-- Botones de acción -->
                                <a href="#"><button class='button view' title="Ver Docente" data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-viewDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                </svg></button></a>

                                <a href="#"><button class='button curso' title="Asignar Curso"  data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-AsignarCursoDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-journal-bookmark-fill' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z'/>
                                <path d='M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2'/>
                                <path d='M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z'/>
                                </svg></button></a>

                                <a href="#"><button class='button edit' title="Editar Docente"  data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-editDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                </svg></button></a>

                                <a href="#"><button class='button delete' title="Eliminar Docente" data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-deleteDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                </svg></button></a>
                            </td>
                        </tr>
                    `;
                });
            } else if (data.error) {
                console.error("Error en el servidor:", data.message); 
                rows = `<tr><td colspan="10" class="text-center">Error: ${data.message}</td></tr>`;
            } else {
                rows = '<tr><td colspan="10" class="text-center">No hay Docentes registrados</td></tr>';
            }
            $('#tabla-docentes tbody').html(rows);

            if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
                // Eliminar todos los botones excepto el de clase 'view'
                document.querySelectorAll('#tabla-docentes tbody tr').forEach(row => {
                    const viewButton = row.querySelector('button.view');
                    const cell = viewButton?.closest('td');
            
                    if (cell && viewButton) {
                        // Limpiar la celda y volver a insertar solo el botón 'view'
                        cell.innerHTML = '';
                        cell.appendChild(viewButton);
                    }
                });
            
                // Eliminar también el botón superior de "Agregar Participante"
                const btnAdd = document.getElementById('btn-addDocente');
                if (btnAdd) {
                    btnAdd.remove();
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error); 
            console.error('Respuesta del servidor:', xhr.responseText); 
        }
    });
}

// Eliminar Docente
// Eliminar Docente
$(document).ready(function () {
    // Al hacer clic en el botón de eliminar
    $(document).on('click', '.button.delete', function () {
        // Obtener el ID del docente a eliminar
        const idParticipante = $(this).data('id');

        // Guardar el ID en un atributo data-id dentro del modal
        $('#modal-deleteDocente').data('id', idParticipante);
    });

    // Cuando se haga clic en el botón "Eliminar Docente" dentro del modal
    $(document).on('click', '.delete-modal', function () {
        // Obtener el ID guardado en el modal
        const idParticipante = $('#modal-deleteDocente').data('id');

        if (idParticipante) {
            $.ajax({
                url: '../../app/controlador/Docente/eliminarDocente.php', 
                type: 'POST', 
                data: { id: idParticipante }, 
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // Cerrar el modal
                        $('#modal-deleteDocente').modal('hide');
                        Command: toastr["success"]("¡El Docente se eliminó correctamente!");
                        // Actualizar la tabla
                        cargarTabla();
                    } else {
                        $('#modal-deleteDocente').modal('hide');
                        Command: toastr["error"](data.message || "Hubo un problema al eliminar el docente.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX:', error);
                    Command: toastr["error"]("Error de conexión al intentar eliminar el docente.");
                }
            });
        } else {
            console.error('El ID del participante no es válido');
            Command: toastr["error"]("ID de docente no válido.");
        }
    });
});
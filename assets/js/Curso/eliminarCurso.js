  // Función para cargar la tabla de cursos
  function cargarTablaCursos() {
    $.ajax({
        url: '../../app/controlador/Curso/obtenerCursos.php', 
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            let rows = '';

            if (data.success === false) {
                // Error desde el servidor
                rows = `<tr><td colspan="10" class="text-center">Error: ${data.message}</td></tr>`;
            } else if (Array.isArray(data)) {
                // Filtrar cursos y excluir el curso con id_curso = 1
                data = data.filter(curso => curso.id_curso !== 1);

                if (data.length > 0) {
                    // Ordenar los cursos de mayor a menor por id_curso (más recientes primero)
                    data.sort((a, b) => b.id_curso - a.id_curso);

                    // Generar las filas de la tabla
                    data.forEach(curso => {
                        const cuposDisponibles = curso.max_participantes - curso.num_inscritos;
                        const regParticipanteCurso = parseInt(curso.registros_participante_curso, 10) || 0;
                        const puedeEliminarCurso = regParticipanteCurso === 0;
                        const estadoBadgeHtml = window.estadoCurso.renderBadge(curso.estatus);

                        rows += `
                            <tr>
                                <td>${curso.id_curso}</td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">${curso.nombre_curso}</div>
                                    ${curso.preimpreso ? `<span class="badge bg-secondary text-white mt-1" style="font-size: 0.7rem; font-weight: 500;">${curso.preimpreso}</span>` : ''}
                                    ${parseInt(curso.reiniciado, 10) === 1 ? `<span class="badge text-white mt-1 ms-1" style="font-size: 0.7rem; font-weight: 500; background: linear-gradient(135deg, #f59e0b, #d97706) !important; border: none;" title="Este curso ya ha sido clonado/reiniciado"><i class="bi bi-arrow-repeat me-1"></i>Reiniciado</span>` : ''}
                                </td>
                                <td>${curso.nombre_modalidad}</td>
                                <td>${curso.num_inscritos}</td>
                                <td>${cuposDisponibles}</td>
                                <td>${curso.turno}</td>
                                <td>${curso.fecha_inicio}</td>
                                <td>${curso.fecha_fin}</td>
                                <td>${estadoBadgeHtml}</td>
                                <td>
                                   <!-- Botones de acción -->
                                        <a href="#"><button class='button view' title="Ver Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-viewCurso'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                        <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                        </svg></button></a>
  
                                        <a href="#"><button class='button participante' title="Asignar Participantes" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-participanteCurso'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-person-add" viewBox="0 0 16 16">
                                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                                        <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                                        </svg></button></a>

                                        <a href="#"><button class='button config' title="Calificación" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#cursoModal'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
</svg></button></a>
    
                                        <a href="#"><button type="button" class='button edit' title="Editar Curso" data-edit-curso="1" data-id="${curso.id_curso}"><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                        </svg></button></a>
    
                                        ${puedeEliminarCurso ? `<a href="#"><button class='button delete' title="Eliminar Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-deleteCurso'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                        <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                        <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                        </svg></button></a>` : ''}
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    // Si no hay cursos después del filtrado
                    rows = '<tr><td colspan="10" class="text-center">No hay Cursos registrados</td></tr>';
                }
            } else {
                // Caso por defecto si la respuesta no tiene un formato esperado
                rows = '<tr><td colspan="10" class="text-center">No hay Cursos registrados</td></tr>';
            }

            $('#tabla-cursos tbody').html(rows);
            if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
                // Eliminar todos los botones excepto el de clase 'view'
                document.querySelectorAll('#tabla-cursos tbody tr').forEach(row => {
                    const viewButton = row.querySelector('button.view');
                    const cell = viewButton?.closest('td');
            
                    if (cell && viewButton) {
                        // Limpiar la celda y volver a insertar solo el botón 'view'
                        cell.innerHTML = '';
                        cell.appendChild(viewButton);
                    }
                });
            
                // Eliminar también el botón superior de "Agregar Participante"
                const btnAdd = document.getElementById('btn-add');
                if (btnAdd) {
                    btnAdd.remove();
                }
            }
        },
        error: function (xhr, status, error) {
            // Error en la llamada AJAX
            $('#tabla-cursos tbody').html('<tr><td colspan="10" class="text-center">Error al cargar los datos</td></tr>');
        }
    });
}




// Eliminar Curso
$(document).ready(function () {
    // Al hacer clic en el botón de eliminar
    $(document).on('click', '.button.delete', function () {
        // Obtener el ID del curso a eliminar
        const idCurso = $(this).data('id');

        // Guardar el ID en un atributo data-id dentro del modal
        $('#modal-deleteCurso').data('id', idCurso);
    });

    // Cuando se haga clic en el botón "Eliminar Curso" dentro del modal
    $(document).on('click', '.delete-modal', function () {
        // Obtener el ID guardado en el modal
        const idCurso = $('#modal-deleteCurso').data('id');

        if (idCurso) {
            $.ajax({
                url: '../../app/controlador/Curso/eliminarCurso.php', // Ruta al archivo PHP
                type: 'POST', 
                data: { id: idCurso }, 
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // Cerrar el modal
                        $('#modal-deleteCurso').modal('hide'); // Cierra el modal
                       toastr["success"]("¡El Curso se eliminó correctamente!");
                        // Notificar recarga centralizada de cursos
                        $(document).trigger('cursos:actualizados');
                    } else {
                        toastr["error"]("¡No se puede eliminar un curso si tiene un docente o si tiene registros historicos!");
                        
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX:', error);
                }
            });
        } else {
            console.error('El ID del curso no es válido');
        }
    });
});

// Función para cargar la tabla según los filtros
function cargarTablaConFiltros() {
    // Obtener valores de los filtros
    const sexo = $('#sexo-filter').val();
    const edad = $('#edad-filter').val();
    const estado = $('#estado-filter').val();

    // Realizar la solicitud AJAX
    $.ajax({
        url: '../../app/controlador/Docente/obtenerDocentesConFiltros.php',
        method: 'GET',
        data: { sexo, edad, estado },
        dataType: 'json',
        success: function (data) {
            let rows = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(docente => {
                    // Determinar el badge del estado
                    const estadoBadge = docente.estado_docente === "En formación"
                        ? '<span class="badge bg-success">En formación</span>'
                        : docente.estado_docente === "Disponible"
                            ? '<span class="badge bg-warning text-black">Disponible</span>'
                            : docente.estado_docente === "Asignado"
                                ? '<span class="badge bg-primary">Asignado</span>'
                                : docente.estado_docente === "Inactivo"
                                    ? '<span class="badge bg-secondary">Inactivo</span>'
                                    : docente.estado_docente;

                    // Determinar el icono y clase del botón de estado
                    const esInactivo = docente.estado_docente === "Inactivo";
                    const estadoBtnIcon = esInactivo 
                        ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-check-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>'
                        : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
                    
                    const estadoBtnTitle = esInactivo ? "Activar Docente" : "Inactivar Docente";
                    const estadoBtnClass = esInactivo ? "button activate" : "button inactivate";

                    rows += `
                        <tr>
                            <td>${docente.id_docente}</td>
                            <td>${docente.cedula}</td>
                            <td>${docente.nombre}</td>
                            <td>${docente.apellido}</td>
                            <td>${docente.genero}</td>
                            <td>${docente.edad}</td>
                            <td>${docente.correo}</td>
                            <td>${estadoBadge}</td>
                            <td>
                                <!-- Botones de acción -->
                                <a href="#"><button class='button view' title="Ver Docente" data-id="${docente.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-viewDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/></svg></button></a>

                                <a href="#"><button class='button curso' title="Asignar Curso" data-id="${docente.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-AsignarCursoDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-journal-bookmark-fill' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z'/>
                                <path d='M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2'/>
                                <path d='M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z'/></svg></button></a>

                                <a href="#"><button class='button edit' title="Editar Docente" data-id="${docente.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-editDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                </svg></button></a>

                                <a href="#"><button class='${estadoBtnClass}' title="${estadoBtnTitle}" data-id="${docente.id_docente}" data-estado="${docente.estado_docente}">${estadoBtnIcon}</button></a>
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

            // Aplicar restricciones de rol
            if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
                document.querySelectorAll('#tabla-docentes tbody tr').forEach(row => {
                    const viewButton = row.querySelector('button.view');
                    const cell = viewButton?.closest('td');
            
                    if (cell && viewButton) {
                        cell.innerHTML = '';
                        cell.appendChild(viewButton);
                    }
                });
            
                const btnAdd = document.getElementById('btn-addDocente');
                if (btnAdd) {
                    btnAdd.remove();
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los datos:', error);
        }
    });
}

$(document).ready(function() {
    // Función para aplicar filtros
    function aplicarFiltros() {
        cargarTablaConFiltros();
    }
    
    // Evento para el botón de filtro
    $('#btn-filter').on('click', function(e) {
        e.preventDefault();
        aplicarFiltros();
    });
    
    // Evento para cambios en los selects
    $('#sexo-filter, #edad-filter, #estado-filter').on('change', function() {
        aplicarFiltros();
    });
    
    // Evento para el botón de cambiar estado (activar/inactivar)
    $(document).on('click', '.button.activate, .button.inactivate', function(e) {
        e.preventDefault();
        
        const docenteId = $(this).data('id');
        const estadoActual = $(this).data('estado');
        const esInactivo = estadoActual === 'Inactivo';
        
        // Determinar el nuevo estado
        const nuevoEstado = esInactivo ? 'Disponible' : 'Inactivo';
        const accionTexto = esInactivo ? 'activar' : 'inactivar';
        const mensajeConfirmacion = `¿Está seguro de que desea ${accionTexto} este docente?`;
        
        if (confirm(mensajeConfirmacion)) {
            cambiarEstadoDocente(docenteId, nuevoEstado);
        }
    });
});

// Función para cambiar el estado del docente via AJAX
function cambiarEstadoDocente(docenteId, nuevoEstado) {
    $.ajax({
        url: '../../app/controlador/Docente/cambiarEstadoDocente.php',
        method: 'POST',
        data: {
            id_docente: docenteId,
            nuevo_estado: nuevoEstado
        },
        dataType: 'json',
        beforeSend: function() {
            toastr.info('Procesando solicitud...');
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                // Recargar la tabla
                cargarTablaConFiltros();
            } else {
                toastr.error(response.message || 'Error al cambiar el estado');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            toastr.error('Error de conexión al servidor');
        }
    });
}

// Llamar a la función al cambiar un filtro
$('#sexo-filter, #edad-filter, #estado-filter').on('change', cargarTablaConFiltros);
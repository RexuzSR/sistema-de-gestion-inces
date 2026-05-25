// La función cargarTabla ha sido eliminada por redundancia. 
// Se utiliza la versión unificada en tabla-participantes.js (cargarRegistros / aplicarFiltros).



function cargarCursosPendientes(idParticipante) {
    $.ajax({
        url: '../../app/controlador/ParticipanteCurso/obtenerCursosParticipante.php',
        method: 'GET',
        data: { id_participante: idParticipante },
        dataType: 'json',
        success: function (data) {
            let container = $('#cursos-pendientes');
            container.empty();

            if (data && data.length > 0) {
                let cursosHTML = '<div class="row g-3">';

                data.forEach(curso => {
                    const badgeType = curso.estatus_participante === 'En formación'
                        ? '<span class="badge bg-success" title="En formación"><i class="bi bi-arrow-repeat"></i> En formación</span>'
                        : '<span class="badge bg-warning text-dark" title="Asignado"><i class="bi bi-clock-history"></i> Asignado</span>';

                    const botonDesinscribir = curso.estatus_participante === 'Asignado'
                        ? `<button class="btn btn-sm btn-danger desinscribir-curso"  data-id="${curso.id_participante_curso}">
                                Desinscribir Curso
                           </button>`
                        : '';

                    cursosHTML += `
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card asignar-card shadow-lg border-light bg-white">
                                <div class="card-body d-flex flex-column justify-content-between h-100">
                                    <div class="mb-2">
                                        <h6 class="fw-semibold mb-1 text-truncate" title="${curso.nombre_curso}" style="color: #020d19;">
                                            <i class="bi bi-book me-1"></i>${curso.nombre_curso}
                                        </h6>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        ${badgeType}
                                        ${botonDesinscribir}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });

                cursosHTML += '</div>';
                container.html(cursosHTML);
            } else {
                container.html(`
                    <div class="text-center py-5 bg-light rounded-3">
                        <i class="bi bi-journal-x display-6 text-muted"></i>
                        <h6 class="text-muted mt-3 mb-0">No hay cursos asignados</h6>
                        <small class="text-muted">Comienza asignando un nuevo curso</small>
                    </div>
                `);
            }
        },
        error: function () {
            $('#cursos-pendientes').html(`
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar los cursos
                </div>
            `);
        }
    });
}


function cargarCursosNoInscritos(idParticipante) {
    $.ajax({
        url: '../../app/controlador/ParticipanteCurso/obtenerCursosNoInscritos.php',
        method: 'GET',
        data: { id_participante: idParticipante },
        dataType: 'json',
        success: function (data) {
            let selectCurso = $('#asignarCurso');
            selectCurso.empty().append('<option value="" disabled selected>Seleccione un curso</option>');

            if (data && data.length > 0) {
                // Agregar la opción de crear nuevo curso como primera opción válida
                selectCurso.append('<option value="nuevo_curso" class="fw-bold text-success">➕ Crear Nuevo Curso</option>');

                data.forEach(curso => {
                    selectCurso.append(
                        `<option value="${curso.id_curso}">${curso.display}</option>`
                    );
                });
            } else {
                selectCurso.append('<option value="" disabled>No hay cursos disponibles</option>');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr.responseText);
            alert('Error al cargar cursos. Ver consola para detalles.');
        }
    });
}



$(document).on('click', '.curso', function () {
    let id_participante = $(this).attr("data-id");

    $.ajax({
        url: '../../app/controlador/ParticipanteCurso/obtenerDatos.php',
        method: 'GET',
        data: { id_participante: id_participante },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert(data.error);
            } else {
                $('#asignar-nombre').text(data.nombre);
                $('#asignar-apellido').text(data.apellido);
                $('#asignar-cedula').text(data.cedula);
                $('#asignar-edad').text(data.edad);
                cargarCursosPendientes(id_participante);
                cargarCursosNoInscritos(id_participante); // Cargar los cursos no inscritos
            }
        },
        error: function () {
            alert('Error al cargar los datos del participante.');
        }
    });
});


// 1. Capturar el click en botones con clase .curso y obtener el ID
$(document).on('click', '.curso', function () {
    let id_participante = $(this).attr("data-id");

    // 2. Asignar el ID al campo oculto del formulario
    $('#id_participante').val(id_participante);

    // 3. Mostrar el modal (si es necesario)
    $('#modal-AsignarCursoParticipante').modal('show');
});

// 4. Manejar el cambio en el select
$('#asignarCurso').on('change', function () {
    if ($(this).val() === 'nuevo_curso') {
        // Resetear el select a la opción por defecto
        $(this).val('');

        // Ocultar el modal actual y mostrar el de crear curso
        $('#modal-AsignarCursoParticipante').modal('hide');
        $('#modal-addCurso').modal('show');
    }
});

// 5. Manejar el envío del formulario
$('#formAsignarCurso').submit(function (e) {
    e.preventDefault();

    // Obtener datos del formulario
    let formData = {
        id_participante: $('#id_participante').val(),
        curso: $('#asignarCurso').val()
    };

    // Validación básica
    if (!formData.curso) {
        toastr.error('Seleccione un curso válido');
        return;
    }

    // 5. Enviar por AJAX
    $.ajax({
        type: "POST",
        url: "../../app/controlador/ParticipanteCurso/asignarCurso.php",
        data: formData,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                toastr.success('Curso asignado correctamente');
                cargarTabla();
                $('#formAsignarCurso')[0].reset(); // Limpiar el formulario
                // Actualizamos la lista de cursos pendientes sin cerrar el modal
                cargarCursosPendientes(formData.id_participante);
                cargarCursosNoInscritos(formData.id_participante);

            } else {

            }
        },
        error: function (xhr, status, error) {
            toastr.error('Error de conexión: ' + error);
        }
    });
});


$(document).on('click', '.desinscribir-curso', function () {
    // Obtener el ID del curso a eliminar
    const idParticipanteCurso = $(this).data('id');
    const idParticipante = $('#id_participante').val();

    // Cerrar el modal activo (por ejemplo, #modal-AsignarCursoParticipante)
    $('#modal-AsignarCursoParticipante').modal('hide');

    // Mostrar el modal de confirmación
    $('#modal-deleteCurso').modal('show');

    // Manejar el clic en el botón "Confirmar" dentro del modal
    $('#confirm-delete-curso').off('click').on('click', function () {
        // Realizar la solicitud para eliminar el curso
        $.ajax({
            url: '../../app/controlador/ParticipanteCurso/eliminarCurso.php',
            method: 'POST',
            data: {
                id_participante_curso: idParticipanteCurso,
                id_participante: idParticipante
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Mostrar una alerta de éxito
                    toastr.success('El participante se desinscribió del curso correctamente.');

                    // Actualizar los cursos pendientes, no inscritos, y la tabla
                    cargarCursosPendientes(idParticipante);
                    cargarCursosNoInscritos(idParticipante);
                    cargarTabla();
                } else {
                    // Mostrar una alerta en caso de error en la respuesta
                    toastr.error('No se pudo desinscribir al participante. Intente nuevamente.');
                }
                // Cerrar el modal de confirmación
                $('#modal-deleteCurso').modal('hide');

                // Volver a mostrar el modal anterior
                $('#modal-AsignarCursoParticipante').modal('show');
            },
            error: function (xhr, status, error) {
                // Registrar el error en la consola para depuración
                console.error('Error al eliminar el curso:', error);

                // Mostrar una alerta de error
                toastr.error('Error de conexión al intentar desinscribir al participante.');

                // Cerrar el modal de confirmación y volver al modal anterior
                $('#modal-deleteCurso').modal('hide');
                $('#modal-AsignarCursoParticipante').modal('show');
            }
        });
    });

    // Manejar el clic en el botón "Cancelar" dentro del modal
    $('.cancel-modal').off('click').on('click', function () {
        // Cerrar el modal de confirmación
        $('#modal-deleteCurso').modal('hide');

        // Volver al modal anterior
        $('#modal-AsignarCursoParticipante').modal('show');
    });
});


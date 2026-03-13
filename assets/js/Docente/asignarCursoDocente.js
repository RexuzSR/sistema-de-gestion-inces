// asignarCursoDocente.js
$(document).ready(function () {
    let docenteIdGlobal = null;

    // Cuando se abre el modal
    $('#modal-AsignarCursoDocente').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        docenteIdGlobal = button.data('id');

        console.log('Abriendo modal para docente ID:', docenteIdGlobal);

        // Limpiar y cargar datos
        limpiarModal();
        cargarDatosDocente(docenteIdGlobal);
        cargarCursosDisponibles(docenteIdGlobal);
        cargarCursosAsignados(docenteIdGlobal);
    });

    // Cuando se cierra el modal
    $('#modal-AsignarCursoDocente').on('hidden.bs.modal', function () {
        docenteIdGlobal = null;
        limpiarModal();
    });

    // Limpiar modal
    function limpiarModal() {
        $('#asignar-nombre-docente').text('');
        $('#asignar-cedula-docente').text('');
        $('#asignar-correo-docente').text('');
        $('#asignar-estado-docente').html('');
        $('#id_docente').val('');
        $('#asignarCursoDocente').html('<option value="" disabled selected>Cargando cursos disponibles...</option>');
        $('#cursos-asignados').html('<div class="text-center text-muted py-3">Cargando cursos asignados...</div>');
    }

    // Cargar datos del docente
    function cargarDatosDocente(docenteId) {
        $.ajax({
            url: '../../app/controlador/Docente/obtenerDocente.php',
            method: 'GET',
            data: { id: docenteId },
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    const docente = response.data;

                    // Construir nombre completo
                    const nombreCompleto = construirNombreCompleto(
                        docente.primer_nombre,
                        docente.segundo_nombre,
                        docente.primer_apellido,
                        docente.segundo_apellido
                    );

                    // Actualizar UI
                    $('#asignar-nombre-docente').text(nombreCompleto);
                    $('#asignar-cedula-docente').text(docente.cedula || 'No especificado');
                    $('#asignar-correo-docente').text(docente.correo || 'No especificado');

                    // Estado con badge
                    const estadoHtml = obtenerBadgeEstado(docente.estado_docente || 'Disponible');
                    $('#asignar-estado-docente').html(estadoHtml);

                    // Asignar ID
                    $('#id_docente').val(docenteId);

                } else {
                    toastr.error(response.message || 'Error al cargar los datos del docente');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                toastr.error('Error de conexión al cargar datos del docente');
            }
        });
    }

    // Cargar cursos disponibles
    function cargarCursosDisponibles(docenteId) {
        $.ajax({
            url: '../../app/controlador/Docente/obtenerCursosDisponibles.php',
            method: 'GET',
            data: { id_docente: docenteId },
            dataType: 'json',
            success: function (cursos) {
                const select = $('#asignarCursoDocente');
                select.empty();

                if (cursos && cursos.length > 0) {
                    select.append('<option value="" disabled selected>Seleccione un curso...</option>');

                    cursos.forEach(curso => {
                        const fechaInicio = formatearFecha(curso.fecha_inicio);
                        const fechaFin = formatearFecha(curso.fecha_fin);
                        select.append(`<option value="${curso.id_curso}">${curso.nombre_curso} (${fechaInicio} - ${fechaFin})</option>`);
                    });
                } else {
                    select.append('<option value="" disabled selected>No hay cursos disponibles</option>');
                }
            },
            error: function (xhr) {
                const select = $('#asignarCursoDocente');
                select.empty();
                let msg = 'Error al cargar cursos';
                try {
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr && xhr.responseText) {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) msg = parsed.message;
                    }
                } catch (e) {
                    console.error('Error parseando respuesta de error:', e);
                }

                console.error('Error al cargar cursos disponibles:', xhr);
                select.append(`<option value="" disabled selected>${msg}</option>`);
            }
        });
    }

    // Cargar cursos asignados
    function cargarCursosAsignados(docenteId) {
        const container = $('#cursos-asignados');
        container.html('<div class="text-center text-muted py-3">Cargando cursos asignados...</div>');

        $.ajax({
            url: '../../app/controlador/Docente/obtenerCursosAsignados.php',
            method: 'GET',
            data: { id_docente: docenteId },
            dataType: 'json',
            success: function (cursos) {
                container.empty();

                if (cursos && cursos.length > 0) {
                    cursos.forEach(curso => {
                        const fechaInicio = formatearFecha(curso.fecha_inicio);
                        const fechaFin = formatearFecha(curso.fecha_fin);
                        const fechaAsignacion = formatearFecha(curso.fecha_asignacion);

                        const cursoCard = `
                            <div class="card mb-3 border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-primary mb-2">${curso.nombre_curso}</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 small">
                                                        <strong>Fecha del curso:</strong><br>
                                                        ${fechaInicio} al ${fechaFin}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 small">
                                                        <strong>Asignado el:</strong><br>
                                                        ${fechaAsignacion}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge ${obtenerBadgeColorCurso(curso.estado)}">
                                                    ${curso.estado || 'Sin estado'}
                                                </span>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger btn-eliminar-asignacion ms-2" 
                                                data-id-asignacion="${curso.id_docente_curso}"
                                                title="Eliminar asignación">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(cursoCard);
                    });
                } else {
                    container.html(`
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                    <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                    <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>
                                <span>Este docente no tiene cursos asignados.</span>
                            </div>
                        </div>
                    `);
                }
            },
            error: function () {
                container.html(`
                    <div class="alert alert-danger mb-0">
                        <span>Error al cargar los cursos asignados.</span>
                    </div>
                `);
            }
        });
    }

    // Manejar envío del formulario
    $('#formAsignarCursoDocente').on('submit', function (e) {
        e.preventDefault();

        const docenteId = $('#id_docente').val();
        const cursoId = $('#asignarCursoDocente').val();

        if (!cursoId) {
            toastr.warning('Por favor seleccione un curso');
            $('#asignarCursoDocente').focus();
            return;
        }

        if (!docenteId) {
            toastr.error('Error: No se identificó al docente');
            return;
        }

        // Deshabilitar botón
        const submitBtn = $(this).find('button[type="submit"]');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Asignando...
        `);

        $.ajax({
            url: '../../app/controlador/Docente/asignarCursoDocente.php',
            method: 'POST',
            data: {
                id_docente: docenteId,
                id_curso: cursoId
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    // Recargar datos
                    cargarCursosAsignados(docenteId);
                    cargarCursosDisponibles();

                    // Resetear select
                    $('#asignarCursoDocente').val('');

                    // Actualizar tabla principal si existe
                    if (typeof aplicarFiltros === 'function') {
                        setTimeout(() => aplicarFiltros(), 500);
                    }
                } else {
                    toastr.error(response.message || 'Error al asignar el curso');
                }
            },
            error: function () {
                toastr.error('Error de conexión al asignar el curso');
            },
            complete: function () {
                // Rehabilitar botón
                submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Eliminar asignación
    $(document).on('click', '.btn-eliminar-asignacion', function () {
        const idAsignacion = $(this).data('id-asignacion');
        const docenteId = docenteIdGlobal;

        if (confirm('¿Está seguro de eliminar esta asignación?')) {
            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: '../../app/controlador/Docente/eliminarAsignacionCurso.php',
                method: 'POST',
                data: { id_docente_curso: idAsignacion },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        cargarCursosAsignados(docenteId);
                        cargarCursosDisponibles();

                        if (typeof aplicarFiltros === 'function') {
                            setTimeout(() => aplicarFiltros(), 500);
                        }
                    } else {
                        toastr.error(response.message);
                        btn.prop('disabled', false);
                    }
                },
                error: function () {
                    toastr.error('Error de conexión al eliminar la asignación');
                    btn.prop('disabled', false);
                }
            });
        }
    });

    // Funciones auxiliares
    function construirNombreCompleto(primerNombre, segundoNombre, primerApellido, segundoApellido) {
        let nombreCompleto = primerNombre || '';

        if (segundoNombre) {
            nombreCompleto += ' ' + segundoNombre;
        }

        if (primerApellido) {
            nombreCompleto += ' ' + primerApellido;
        }

        if (segundoApellido) {
            nombreCompleto += ' ' + segundoApellido;
        }

        return nombreCompleto.trim();
    }

    function formatearFecha(fecha) {
        if (!fecha) return 'No especificada';

        try {
            const fechaObj = new Date(fecha);
            return fechaObj.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return fecha;
        }
    }

    function obtenerBadgeEstado(estado) {
        const estados = {
            'Disponible': { class: 'bg-warning text-dark', text: 'Disponible' },
            'En formación': { class: 'bg-info text-white', text: 'En formación' },
            'Asignado': { class: 'bg-primary text-white', text: 'Asignado' },
            'Inactivo': { class: 'bg-secondary text-white', text: 'Inactivo' }
        };

        const estadoInfo = estados[estado] || { class: 'bg-light text-dark', text: estado };
        return `<span class="badge ${estadoInfo.class}">${estadoInfo.text}</span>`;
    }

    function obtenerBadgeColorCurso(estado) {
        const colores = {
            'Culminado': 'bg-success',
            'En proceso': 'bg-primary',
            'En espera': 'bg-warning text-dark',
            'Aprobado': 'bg-info',
            'default': 'bg-secondary'
        };

        return colores[estado] || colores.default;
    }
});
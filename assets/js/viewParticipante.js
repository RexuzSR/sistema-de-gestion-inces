$(document).on('click', '.button.view', function () {
    const idParticipante = $(this).data('id');

    // Cargar datos básicos
    fetch(`../../app/controlador/verParticipante.php?id=${idParticipante}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const participante = data.data;

                // Formatear teléfono con 0 al inicio si es necesario
                let telefono = participante.telefono ?
                    (participante.telefono.toString().startsWith('0')
                        ? participante.telefono
                        : '0' + participante.telefono)
                    : 'No registrado';

                // Llenar datos personales
                $('#nombre-view').text(participante.nombre);
                $('#apellido-view').text(participante.apellido);
                $('#cedula-view').text(participante.cedula);
                $('#edad-view').text(participante.edad + ' años');
                $('#correo-view').text(participante.correo);
                $('#telefono-view').text(telefono);
                $('#fechaNacimiento-view').text(participante.fecha_nacimiento);
                $('#fechaRegistro-view').text(participante.fecha_registro);
                $('#gradoInstruccion-view').text(participante.grado_institucion);
                $('#sexo-view').text(participante.genero);


                $('#modal-viewParticipante')
                    .find('#btn-historial-participante, #btn-constancia-participante, #btn-recorrido-participante')
                    .attr('data-id', idParticipante);

                // Mostrar modal
                $('#modal-viewParticipante').modal('show');

                // Cargar cursos activos
                cargarCursosActivos(idParticipante);
            } else {
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Error al cargar datos del participante');
        });
});

// ==== Eventos para descargar PDFs  ====
// Historial PDF
$(document).on('click', '#btn-historial-participante', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    window.open(`../controlador/fpdf/HistorialParticipante.php?id=${id}`, '_blank');
});

// Constancia PDF
$(document).on('click', '#btn-constancia-participante', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    window.open(`../controlador/fpdf/constanciaParticipante.php?id=${id}`, '_blank');
});

function cargarCursosActivos(idParticipante) {
    fetch(`../../app/controlador/obtenerCursosParticipante.php?id=${idParticipante}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ordenar cursos: 1ro En formación, luego Pendiente, por fecha de inicio
                const cursosOrdenados = data.cursosActivos.sort((a, b) => {
                    // Prioridad por estado
                    const ordenEstados = { 'En formación': 1, 'Pendiente': 2 };
                    if (ordenEstados[a.estado] !== ordenEstados[b.estado]) {
                        return ordenEstados[a.estado] - ordenEstados[b.estado];
                    }

                    // Ordenar por fecha de inicio (convertir a Date)
                    const fechaA = a.fecha_inicio.split('/').reverse().join('-');
                    const fechaB = b.fecha_inicio.split('/').reverse().join('-');
                    return new Date(fechaA) - new Date(fechaB);
                });

                // Limpiar contenedor
                $('#cursos-activos-container').empty();

                if (cursosOrdenados.length > 0) {
                    cursosOrdenados.forEach(curso => {
                        // Sincronizar con el gestor central de estados de cursos
                        const infoEstado = window.estadoCurso.getInfo(curso.id_estado_curso);
                        let displayEstado = infoEstado.texto;
                        let badgeColor = '';

                        // Mapeo dinámico de clases según el estado central
                        switch(displayEstado) {
                            case 'En proceso':
                                badgeColor = 'estado-badge en-curso';
                                break;
                            case 'Culminado':
                                badgeColor = 'estado-badge culminado';
                                break;
                            case 'Diferido':
                                badgeColor = 'estado-badge diferido';
                                break;
                            case 'En espera':
                                badgeColor = 'estado-badge en-espera';
                                break;
                            case 'En pausa':
                                badgeColor = 'estado-badge en-pausa';
                                break;
                            default:
                                badgeColor = curso.estado === 'Pendiente' ? 'estado-badge pendiente' : 'estado-badge en-curso';
                        }

                        const cursoHTML = `
                            <div class="col-md-6">
                                <div class="curso-card p-3 rounded-3 mb-3 view-curso-card cursor-pointer" 
                                     data-id="${curso.id_curso}" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modal-viewCurso"
                                     style="cursor: pointer; transition: transform 0.2s;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-bookmark-check-fill text-primary fs-5 me-2"></i>
                                            <span class="fw-medium">${curso.nombre_curso}</span>
                                        </div>
                                         <span class="badge ${badgeColor}">${displayEstado}</span>
                                    </div>
                                    <div class="mt-2 text-muted">
                                        <small>Inicio: ${curso.fecha_inicio}</small>
                                    </div>
                                </div>
                            </div>`;
                        $('#cursos-activos-container').append(cursoHTML);
                    });
                    $('#sin-cursos').hide();
                } else {
                    $('#sin-cursos').html(`
                        <div class="text-center py-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Actualmente no tiene cursos activos...
                        </div>
                    `).show();
                }
            } else {
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Error al cargar los cursos');
        });
}
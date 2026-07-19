function cargarTablaCursos() {
    $.ajax({
        url: '../../app/controlador/Curso/obtenerCursos.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            let rows = '';
            if (data.success === false) {
                rows = `<tr><td colspan="10" class="text-center">Error: ${data.message}</td></tr>`;
            } else if (Array.isArray(data)) {
                data = data.filter(c => c.id_curso !== 1);
                if (data.length > 0) {
                    data.sort((a, b) => a.id_curso - b.id_curso);
                    data.forEach(curso => {
                        const cupos = curso.max_participantes - curso.num_inscritos;
                        const regParticipanteCurso = parseInt(curso.registros_participante_curso, 10) || 0;
                        const puedeEliminarCurso = regParticipanteCurso === 0;
                        const badge = window.estadoCurso.renderBadge(curso.estatus);
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
                                <td>${cupos}</td>
                                <td>${curso.turno}</td>
                                <td>${curso.fecha_inicio}</td>
                                <td>${curso.fecha_fin}</td>
                                <td>${badge}</td>
                                <td>
                                    <a href="#"><button class='button view' title="Ver Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-viewCurso'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'><path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/></svg></button></a>
                                    <a href="#"><button class='button participante' title="Asignar Participantes" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-participanteCurso'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-person-add" viewBox="0 0 16 16"><path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/><path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/></svg></button></a>
                                    <a href="#"><button class='button config' title="Calificación" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#cursoModal'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/></svg></button></a>
                                    <a href="#"><button type="button" class='button edit' title="Editar Curso" data-edit-curso="1" data-id="${curso.id_curso}"><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'><path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/></svg></button></a>
                                    ${puedeEliminarCurso ? `<a href="#"><button class='button delete' title="Eliminar Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-deleteCurso'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'><path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/><path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/></svg></button></a>` : ''}
                                </td>
                            </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="10" class="text-center">No hay Cursos registrados</td></tr>';
                }
            } else {
                rows = '<tr><td colspan="10" class="text-center">No hay Cursos registrados</td></tr>';
            }
            $('#tabla-cursos tbody').html(rows);
            if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
                document.querySelectorAll('#tabla-cursos tbody tr').forEach(row => {
                    const v = row.querySelector('button.view');
                    const cell = v?.closest('td');
                    if (cell && v) { cell.innerHTML = ''; cell.appendChild(v); }
                });
                const btnAdd = document.getElementById('btn-add');
                if (btnAdd) btnAdd.remove();
            }
        },
        error: function () {
            $('#tabla-cursos tbody').html('<tr><td colspan="10" class="text-center">Error al cargar los datos</td></tr>');
        }
    });
}


$(document).ready(function () {

    // Guarda el estado actual del curso mientras el modal está abierto
    let estadoActualCurso = null;
    let cambiosSinGuardar = false;

    // =========================================================
    // ABRIR MODAL GESTIÓN (.button.config)
    // =========================================================
    $(document).on('click', '.button.config', function () {
        const idCurso = $(this).data('id');
        $('#btnFinalizar').data('id', idCurso);
        $('#btnGuardar').data('id', idCurso);
        $('#btnReiniciarCurso').data('id', idCurso);
        
        cambiosSinGuardar = false;

        fetch(`../../app/controlador/Curso/obtenerParticipantes.php?id_curso=${idCurso}`)
            .then(r => r.json())
            .then(data => {
                const participantes = data.participantes;
                const s = data.id_estatus;

                // == en lugar de === porque PDO puede devolver string "2"
                const enEspera  = s == 1;
                const diferido  = s == 2;
                const enProceso = s == 3;
                const enPausa   = s == 4;
                const culminado = s == 5;
                const inactivo  = s == 6;

                estadoActualCurso = parseInt(s);

                // Inicialmente deshabilitado, se habilitará en validarNotasCompletas si aplica
                $('#btnGuardar').prop('disabled', true);
                $('#btnFinalizar').prop('disabled', !enProceso);
                $('#btnReiniciarCurso').prop('disabled', !(diferido || culminado));

                const alerta = $('#cursoFinalizadoAlert');
                alerta.removeClass('d-none');

                if (enEspera)       alerta.html(`<div class="alert alert-info mt-3 mx-3"><strong>Curso en espera</strong><br>El curso aún no ha iniciado.</div>`);
                else if (diferido)  alerta.html(`<div class="alert alert-warning mt-3 mx-3"><strong>Curso diferido</strong><br>Puedes reiniciar este curso. Elige si deseas mantener los participantes actuales.</div>`);
                else if (enProceso) alerta.html(`<div class="alert alert-warning mt-3 mx-3"><strong>Curso en proceso</strong><br>Finaliza el curso para asignar calificaciones.</div>`);
                else if (enPausa)   alerta.html(`<div class="alert alert-secondary mt-3 mx-3"><strong>Curso en pausa</strong><br>Este curso está en pausa.</div>`);
                else if (culminado) alerta.html(`<div class="alert alert-success mt-3 mx-3"><strong>Curso culminado</strong><br>Asigna las calificaciones a todos los participantes y luego podrás reiniciar el curso.</div>`);
                else if (inactivo)  alerta.html(`<div class="alert alert-danger mt-3 mx-3"><strong>Curso inactivo</strong><br>No disponible.</div>`);

                const lista = $('#listaParticipantes');
                lista.empty();

                if (!participantes || participantes.length === 0) {
                    lista.html(`<div class="text-center text-muted my-4"><i class="fas fa-user-slash fa-2x mb-2"></i><br>No hay participantes inscritos.</div>`);
                } else {
                    participantes.forEach(p => {
                        const estado = p.estatus_participante;
                        lista.append(`
                            <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center w-100">
                                    <div style="flex-grow:1;">
                                        <span class="d-block">${p.primer_nombre} ${p.primer_apellido}</span>
                                        <small class="text-muted">C.I. ${p.cedula}</small>
                                    </div>
                                    <div>
                                        <select class="form-select form-select-sm w-auto estado-select" style="max-width:140px;"
                                                data-id="${p.id_participante_curso}" ${culminado ? '' : 'disabled'}>
                                            <option value="">Seleccionar</option>
                                            <option value="Aprobado"  ${(!estado || (estado !== 'Reprobado' && estado !== 'Ausente')) ? 'selected' : ''}>Aprobado</option>
                                            <option value="Reprobado" ${estado === 'Reprobado' ? 'selected' : ''}>Reprobado</option>
                                            <option value="Ausente"   ${estado === 'Ausente'   ? 'selected' : ''}>Ausente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>`);
                    });
                }

                $('#contadorEstudiantes').text(
                    `${participantes ? participantes.length : 0} estudiante${participantes && participantes.length !== 1 ? 's' : ''}`
                );
                
                // Validar estado inicial de las notas
                validarNotasCompletas();
                
                $('#cursoModal').modal('show');
            })
            .catch(err => { console.error(err); toastr.error('Error al cargar los participantes'); });
    });

    // =========================================================
    // VALIDACIÓN DE NOTAS COMPLETAS
    // =========================================================
    function validarNotasCompletas() {
        if (estadoActualCurso !== 5) {
            $('#btnGuardar').prop('disabled', true);
            return;
        }

        let todasCompletas = true;
        const selects = $('.estado-select');
        
        if (selects.length === 0) {
            todasCompletas = false;
        } else {
            selects.each(function() {
                if ($(this).val() === "") {
                    todasCompletas = false;
                    return false;
                }
            });
        }

        $('#btnGuardar').prop('disabled', !todasCompletas);
    }

    $(document).on('change', '.estado-select', function() {
        cambiosSinGuardar = true;
        validarNotasCompletas();
    });

    // =========================================================
    // BOTÓN FINALIZAR
    // =========================================================
    $('#btnFinalizar').on('click', function () {
        const idCurso = $(this).data('id');
        fetch(`../../app/controlador/Curso/obtenerCurso.php?id_curso=${idCurso}`)
            .then(r => r.json())
            .then(curso => {
                const fechaFin = new Date(curso.fecha_fin);
                const hoy = new Date(); hoy.setHours(0,0,0,0);
                const msg = fechaFin > hoy
                    ? `<p><strong>¡Atención!</strong> La fecha de finalización aún no ha llegado (<strong>${curso.fecha_fin}</strong>).</p><p>¿Deseas finalizar <strong>${curso.nombre_curso}</strong> antes de tiempo?</p>`
                    : `<p>¿Deseas finalizar el curso <strong>${curso.nombre_curso}</strong> ahora?</p>`;
                $('#mensajeConfirmacionCurso').html(msg);
                $('#modal-confirmFinalizarCurso').modal('show');
                $('#confirmarFinalizarCurso').data('id', idCurso);
            })
            .catch(err => { console.error(err); toastr.error('Error al obtener datos del curso'); });
    });

    $('#confirmarFinalizarCurso').on('click', function () {
        const idCurso = $(this).data('id');
        fetch('../../app/controlador/Curso/finalizarCurso.php', {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({id_curso: idCurso})
        })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    $('#modal-confirmFinalizarCurso').modal('hide');
                    $('.button.config[data-id="' + idCurso + '"]').click();
                    cargarTablaCursos();
                    toastr.success('¡Curso finalizado correctamente!');
                } else {
                    toastr.error(resp.message || 'Error al finalizar el curso');
                }
            })
            .catch(err => { console.error(err); toastr.error('No se pudo finalizar el curso'); });
    });

    // =========================================================
    // BOTÓN GUARDAR NOTAS
    // =========================================================
    $('#btnGuardar').on('click', function () {
        const idCurso = $(this).data('id');
        const notas = [];
        let faltanNotas = false;

        $('.estado-select').each(function () {
            const estado = $(this).val();
            if (!estado) {
                toastr.error('Debes asignar una nota a todos los participantes.');
                faltanNotas = true;
                return false;
            }
            notas.push({
                id_participante_curso: $(this).data('id'),
                estado,
                nombre: $(this).closest('.list-group-item').find('.d-block').text().trim(),
                cedula:  $(this).closest('.list-group-item').find('.text-muted').text().replace('C.I. ', '').trim()
            });
        });

        if (faltanNotas || notas.length === 0) return;

        let listaHtml = '';
        notas.forEach(n => {
            listaHtml += `<li><div class="d-flex justify-content-between">
                <span><strong>${n.nombre} (C.I. ${n.cedula}):</strong></span>
                <span>${n.estado}</span></div></li>`;
        });

        $('#confirmarListaNotas').html(listaHtml);
        $('#confirmarGuardarModal').modal('show');
        $('#cursoModal').modal('hide');

        $('#confirmarGuardarBtn').off('click').on('click', function () {
            $.ajax({
                url: '../../app/controlador/Curso/guardarNotas.php',
                method: 'POST', contentType: 'application/json',
                data: JSON.stringify({id_curso: idCurso, participantes_notas: notas}),
                success: function (resp) {
                    if (resp.success) {
                        cambiosSinGuardar = false;
                        toastr.success('¡Notas guardadas correctamente!');
                        $('#confirmarGuardarModal').modal('hide');
                        // Reabrir modal para reflejar notas y habilitar Reiniciar
                        $('.button.config[data-id="' + idCurso + '"]').click();
                        cargarTablaCursos();
                    } else {
                        toastr.error(resp.error || 'Error al guardar las notas');
                    }
                },
                error: function () { toastr.error('Error al procesar la solicitud'); }
            });
        });
    });

    $('#confirmarGuardarModal').on('hidden.bs.modal', function () {
        $('#cursoModal').modal('show');
    });

    // =========================================================
    // BOTÓN REINICIAR CURSO
    // =========================================================
    $('#btnReiniciarCurso').on('click', function () {
        const idCurso = $(this).data('id');

        if (estadoActualCurso === 5) {
            // --- CULMINADO ---
            // Validar que todos los participantes tengan nota final
            const selects = $('.estado-select');

            let notasFaltantes  = 0;
            let estadosInvalidos = 0;

            selects.each(function () {
                const val = $(this).val();
                if (!val || val === '')                          notasFaltantes++;
                if (val === 'Asignado' || val === 'En formación') estadosInvalidos++;
            });

            if (notasFaltantes > 0) {
                toastr.warning(`Faltan notas por asignar a ${notasFaltantes} participante(s). Asigna todas las calificaciones antes de reiniciar.`);
                return;
            }
            if (estadosInvalidos > 0) {
                toastr.warning(`${estadosInvalidos} participante(s) tienen estado "Asignado" o "En formación". Solo se aceptan Aprobado, Reprobado o Ausente.`);
                return;
            }

            // Aviso: sin participantes, checkbox deshabilitado
            $('#avisoReinicio').html(`
                <div class="alert alert-warning mb-2">
                    <strong>⚠ Curso Culminado</strong><br>
                    El curso se reiniciará <strong>sin participantes</strong> en estado "En espera".
                </div>`);
            $('#chkMantenerParticipantes').prop('checked', false).prop('disabled', true);
            $('#textoCheckbox').text('No disponible para cursos Culminados.');

            $('#confirmarReinicioCurso').data('id', idCurso).data('modo', 'culminado');
            $('#modalReiniciarCurso').data('idCurso', idCurso).modal('show');

        } else if (estadoActualCurso === 2) {
            // --- DIFERIDO ---
            // Checkbox habilitado para elegir si mantiene participantes
            $('#avisoReinicio').html(`
                <div class="alert alert-info mb-2">
                    <strong>Curso Diferido</strong><br>
                    Elige si deseas conservar los participantes actuales o reiniciar sin ellos.
                </div>`);
            $('#chkMantenerParticipantes').prop('checked', false).prop('disabled', false);
            $('#textoCheckbox').text('Si lo marcas, los participantes se conservarán con las nuevas fechas.');

            $('#confirmarReinicioCurso').data('id', idCurso).data('modo', 'diferido');
            $('#modalReiniciarCurso').data('idCurso', idCurso).modal('show');
        }
    });

    // =========================================================
    // VALIDACIÓN GENERAL / CAMPOS REINICIO
    // =========================================================
    function showErrorReinicio(id, msg) {
        $('#' + id).addClass('is-invalid');
        $('#error-' + id).text(msg).show();
    }

    function clearErrorReinicio(id) {
        $('#' + id).removeClass('is-invalid');
        $('#error-' + id).text('').hide();
    }

    function validateSelectReinicio(id, msg) {
        const value = $('#' + id).val();
        if (!value || value === '') {
            showErrorReinicio(id, msg);
            return false;
        }
        clearErrorReinicio(id);
        return true;
    }

    function validateNumberRangeReinicio(id, min, max, msg) {
        const value = Number($('#' + id).val());
        if (!Number.isInteger(value) || value < min || value > max) {
            showErrorReinicio(id, msg);
            return false;
        }
        clearErrorReinicio(id);
        return true;
    }

    function validarParticipantesReinicio() {
        const min = parseInt($('#min_participantes_Reinicio').val(), 10);
        const max = parseInt($('#max_participantes_Reinicio').val(), 10);
        let ok = true;

        if (!validateNumberRangeReinicio('min_participantes_Reinicio', 12, 40, 'Debe ser entre 12 y 40')) {
            ok = false;
        }
        if (!validateNumberRangeReinicio('max_participantes_Reinicio', 12, 40, 'Debe ser entre 12 y 40')) {
            ok = false;
        }
        if (ok && min > max) {
            showErrorReinicio('max_participantes_Reinicio', 'Debe ser mayor o igual al mínimo');
            ok = false;
        }
        return ok;
    }

    function esFinDeSemana(fecha) {
        return fecha.getDay() === 0 || fecha.getDay() === 6;
    }

    function contarDiasLectivosReinicio(iniStr, finStr) {
        if (!iniStr || !finStr) return 0;
        const ini = new Date(iniStr + 'T00:00:00');
        const fin = new Date(finStr + 'T00:00:00');
        if (ini > fin) return 0;
        let dias = 0;
        const cur = new Date(ini);
        while (cur <= fin) {
            if (!esFinDeSemana(cur)) dias++;
            cur.setDate(cur.getDate() + 1);
        }
        return dias;
    }

    function calcularFechaFinReinicio(iniStr, horasTotales, horasPorClase) {
        if (!iniStr || !horasTotales || !horasPorClase || horasPorClase <= 0) return '';
        const diasNecesarios = Math.ceil(horasTotales / horasPorClase);
        const ini = new Date(iniStr + 'T00:00:00');
        let diasContados = 0;
        const cur = new Date(ini);
        while (diasContados < diasNecesarios) {
            if (!esFinDeSemana(cur)) {
                diasContados++;
            }
            if (diasContados < diasNecesarios) {
                cur.setDate(cur.getDate() + 1);
            }
        }
        const pad = n => String(n).padStart(2, '0');
        return `${cur.getFullYear()}-${pad(cur.getMonth()+1)}-${pad(cur.getDate())}`;
    }

    function calcularHorasTotalesReinicio() {
        const iniStr = $('#nuevaFechaInicio').val();
        const finStr = $('#nuevaFechaFin').val();
        const hpc = parseInt($('#horas_por_clase_Reinicio').val(), 10);
        if (!iniStr || !finStr || ![4,5].includes(hpc)) {
            $('#horas_totales_Reinicio').val('');
            return;
        }
        const dias = contarDiasLectivosReinicio(iniStr, finStr);
        if (dias <= 0) {
            $('#horas_totales_Reinicio').val('');
            return;
        }
        $('#horas_totales_Reinicio').val(String(dias * hpc));
    }

    function actualizarFinSegunHorasReinicio() {
        const iniStr = $('#nuevaFechaInicio').val();
        const hpc = parseInt($('#horas_por_clase_Reinicio').val(), 10);
        const ht = parseInt($('#horas_totales_Reinicio').val(), 10);
        if (!iniStr || ![4,5].includes(hpc) || !isFinite(ht) || ht <= 0) {
            return;
        }
        const nuevaFin = calcularFechaFinReinicio(iniStr, ht, hpc);
        if (nuevaFin) {
            $('#nuevaFechaFin').val(nuevaFin);
        }
    }

    function validarFechasReinicio() {
        const hoy = new Date(); hoy.setHours(0,0,0,0);
        const minInicio = new Date(hoy); minInicio.setDate(minInicio.getDate() + 7);
        const iniStr = $('#nuevaFechaInicio').val();
        const finStr = $('#nuevaFechaFin').val();
        $('#errorFechaInicio, #errorFechaFin').hide().text('');
        let ok = true;

        if (iniStr) {
            const ini = new Date(iniStr + 'T00:00:00');
            if (ini < minInicio) {
                $('#errorFechaInicio').text('La fecha de inicio debe ser al menos 7 días después de hoy.').show();
                $('#nuevaFechaInicio').addClass('is-invalid');
                ok = false;
            } else {
                $('#errorFechaInicio').hide().text('');
                $('#nuevaFechaInicio').removeClass('is-invalid');
            }
        }
        if (iniStr && finStr) {
            const ini = new Date(iniStr + 'T00:00:00');
            const fin = new Date(finStr + 'T00:00:00');
            if (fin < ini) { $('#errorFechaFin').text('La fecha fin no puede ser anterior al inicio.').show(); ok = false; }
        }
        return ok;
    }

    $('#nuevaFechaInicio').on('change', function () {
        const iniStr = $(this).val();
        $('#fechaLimiteInicio').val('');

        if (!iniStr) {
            validarFechasReinicio();
            return;
        }

        const ini = new Date(iniStr + 'T00:00:00');
        const pad = n => String(n).padStart(2, '0');
        const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

        $('#errorFechaInicio').hide().text('');
        $(this).removeClass('is-invalid');

        const limite = new Date(ini); limite.setDate(limite.getDate() + 9);
        $('#fechaLimiteInicio').val(fmt(limite));

        // Auto-calcular fecha de fin a partir de horas totales y horas por clase
        const hpc = parseInt($('#horas_por_clase_Reinicio').val(), 10);
        const ht = parseInt($('#horas_totales_Reinicio').val(), 10);
        if ([4,5].includes(hpc) && isFinite(ht) && ht > 0) {
            const nuevaFin = calcularFechaFinReinicio(iniStr, ht, hpc);
            if (nuevaFin) {
                $('#nuevaFechaFin').val(nuevaFin);
            }
        }

        // Recalcular horas totales con las nuevas fechas
        calcularHorasTotalesReinicio();
        validarFechasReinicio();
    });

    $('#nuevaFechaFin').on('change input', function () {
        calcularHorasTotalesReinicio();
        validarFechasReinicio();
    });

    $('#horas_por_clase_Reinicio').on('change', function () {
        actualizarFinSegunHorasReinicio();
        calcularHorasTotalesReinicio();
        validateSelectReinicio('horas_por_clase_Reinicio', 'Seleccione horas por clase');
    });

    $('#min_participantes_Reinicio, #max_participantes_Reinicio').on('input', validarParticipantesReinicio);

    // =========================================================
    // CONFIRMAR REINICIO
    // =========================================================
    $('#confirmarReinicioCurso').on('click', function (e) {
        e.preventDefault();
        const validFields = [
            validateSelectReinicio('id_modalidad_Reinicio', 'Seleccione una modalidad'),
            validateSelectReinicio('id_sectoreconomico_Reinicio', 'Seleccione un sector económico'),
            validateSelectReinicio('id_subtipo_Reinicio', 'Seleccione un subtipo'),
            validateSelectReinicio('id_ambito_Reinicio', 'Seleccione un ámbito'),
            validateSelectReinicio('turno_Reinicio', 'Seleccione un turno'),
            validateSelectReinicio('horas_por_clase_Reinicio', 'Seleccione horas por clase'),
            validarParticipantesReinicio()
        ];

        const idCurso             = $(this).data('id');
        const modo                = $(this).data('modo') || 'diferido';
        const mantenerParticipantes = $('#chkMantenerParticipantes').is(':checked');

        const preimpresoVal = ($('#preimpreso_Reinicio').val() || '').trim();
        if (modo !== 'diferido') {
            if (!preimpresoVal) {
                $('#preimpreso_Reinicio').addClass('is-invalid');
                $('#error-preimpreso_Reinicio').text('El código preimpreso es requerido').show();
                toastr.warning('Complete el código preimpreso');
                return;
            } else {
                $('#preimpreso_Reinicio').removeClass('is-invalid');
                $('#error-preimpreso_Reinicio').hide();
            }
        }

        if (!$('#nuevaFechaInicio').val() || !$('#nuevaFechaFin').val()) {
            toastr.warning('Complete todos los campos de fecha'); return;
        }
        if (!validarFechasReinicio() || !validFields.every(v => v === true)) {
            toastr.warning('Verifique los errores en el formulario antes de reiniciar'); return;
        }

        // Recopilar calificaciones de los participantes actualmente seleccionadas en la UI
        const notas = [];
        $('.estado-select').each(function () {
            const idPartCurso = $(this).data('id');
            const estado = $(this).val();
            if (idPartCurso && estado) {
                notas.push({
                    id_participante_curso: idPartCurso,
                    estado: estado
                });
            }
        });

        $.ajax({
            url: '../../app/controlador/Curso/reiniciarCurso.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_curso:              idCurso,
                preimpreso:            preimpresoVal,
                id_modalidad:          $('#id_modalidad_Reinicio').val(),
                id_sectoreconomico:    $('#id_sectoreconomico_Reinicio').val(),
                id_subtipo:            $('#id_subtipo_Reinicio').val(),
                id_ambito:             $('#id_ambito_Reinicio').val(),
                turno:                 $('#turno_Reinicio').val(),
                horas_por_clase:       $('#horas_por_clase_Reinicio').val(),
                horas_totales:         $('#horas_totales_Reinicio').val(),
                min_participantes:     $('#min_participantes_Reinicio').val(),
                max_participantes:     $('#max_participantes_Reinicio').val(),
                nueva_fecha_inicio:    $('#nuevaFechaInicio').val(),
                nueva_fecha_fin:       $('#nuevaFechaFin').val(),
                modo:                  modo,
                mantener_participantes: mantenerParticipantes,
                participantes_notas:    notas
            }),
            success: function (resp) {
                if (resp.success) {
                    toastr.success(resp.message || '¡Curso reiniciado correctamente!');
                    $('#modalReiniciarCurso').modal('hide');
                    $('#cursoModal').modal('hide');
                    cargarTablaCursos();
                } else {
                    if (resp.error === 'preimpreso_duplicado') {
                        $('#preimpreso_Reinicio').addClass('is-invalid');
                        $('#error-preimpreso_Reinicio').text(resp.message).show();
                    } else {
                        toastr.error(resp.message || 'Error al reiniciar el curso');
                    }
                }
            },
            error: function () { toastr.error('Error del servidor al reiniciar el curso'); }
        });
    });

    // Al abrir modal: limpiar campos y ocultar cursoModal
    $('#modalReiniciarCurso').on('show.bs.modal', function () {
        $('#cursoModal').modal('hide');
        const hoy = new Date();
        hoy.setHours(0,0,0,0);
        const minInicio = new Date(hoy);
        minInicio.setDate(minInicio.getDate() + 7);
        const minInicioStr = minInicio.toISOString().split('T')[0];

            $('#reiniciarCursoId').val('');
            $('#preimpreso_Reinicio').val('');
            $('#preimpreso-anterior-help').text('Preimpreso anterior: -');
            $('#nuevaFechaInicio, #nuevaFechaFin, #fechaLimiteInicio, #horas_totales_Reinicio').val('');
            $('#id_modalidad_Reinicio, #id_sectoreconomico_Reinicio, #id_subtipo_Reinicio, #id_ambito_Reinicio, #turno_Reinicio, #horas_por_clase_Reinicio').val('');
            $('#min_participantes_Reinicio, #max_participantes_Reinicio').val('');
            $('#textoInicioMinimo').text('Inicio mínimo: ' + minInicioStr + '. No puede ser sábado ni domingo.');
            $('#errorFechaInicio, #errorFechaFin').hide().text('');
            $('.text-danger.small').hide();
            $('.form-control').removeClass('is-invalid');
            $('#chkMantenerParticipantes').prop('checked', false);

            // Inicializar flatpickr (deshabilita sábados y domingos en el calendario)
            const fpInput = document.getElementById('nuevaFechaInicio');
            if (fpInput._flatpickr) fpInput._flatpickr.destroy();
            flatpickr(fpInput, {
                locale: 'es',
                dateFormat: 'Y-m-d',
                minDate: minInicioStr,
                disable: [
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ],
                onChange: function(selectedDates, dateStr) {
                    $('#nuevaFechaInicio').trigger('change');
                }
            });

            const idCurso = $(this).data('idCurso');
            if (idCurso) {
                cargarDatosCursoReinicio(idCurso);
            }
        });

    function cargarDatosCursoReinicio(idCurso) {
        fetch(`../../app/controlador/Curso/obtenerCursoEditar.php?id=${idCurso}`)
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                if (data.error) throw new Error(data.error);
                $('#reiniciarCursoId').val(idCurso);
                
                // Cargar preimpreso anterior y sugerir el siguiente o mantener
                const preimpresoAnt = data.preimpreso || '';
                if (data.id_estatusCurso == 2) {
                    $('#preimpreso_Reinicio').prop('disabled', true).val(preimpresoAnt);
                    $('#preimpreso-anterior-help').text('Se mantendrá el preimpreso original (Curso Diferido).');
                } else {
                    $('#preimpreso_Reinicio').prop('disabled', false).val(sugerirSiguientePreimpreso(preimpresoAnt));
                    $('#preimpreso-anterior-help').text('Preimpreso anterior: ' + (preimpresoAnt || '-'));
                }

                $('#id_modalidad_Reinicio').val(data.id_modalidad || '');
                $('#id_sectoreconomico_Reinicio').val(data.id_sectoreconomico || '');
                $('#id_subtipo_Reinicio').val(data.id_subtipo || '');
                $('#id_ambito_Reinicio').val(data.id_ambito || '');
                $('#turno_Reinicio').val(data.turno || '');
                $('#horas_por_clase_Reinicio').val(data.horas_por_clase ? String(Math.round(Number(data.horas_por_clase))) : '');
                $('#horas_totales_Reinicio').val(data.horas_totales ? String(Math.round(Number(data.horas_totales))) : '');
                $('#min_participantes_Reinicio').val(data.min_participantes || '');
                $('#max_participantes_Reinicio').val(data.max_participantes || '');
            })
            .catch(err => {
                console.error('Error cargando datos del curso para reinicio:', err);
            });
        }

    function sugerirSiguientePreimpreso(actual) {
        if (!actual) return '';
        try {
            const match = String(actual).trim().match(/^(\d+)(.*)$/);
            if (match) {
                const num = parseInt(match[1], 10);
                const resto = match[2];
                return (num + 1) + resto;
            }
        } catch (e) {
            console.error("Error al sugerir siguiente preimpreso:", e);
        }
        return actual;
    }
});
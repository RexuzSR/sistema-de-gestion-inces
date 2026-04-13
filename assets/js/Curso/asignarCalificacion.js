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
                                <td>${curso.nombre_curso}</td>
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

    // =========================================================
    // ABRIR MODAL GESTIÓN (.button.config)
    // =========================================================
    $(document).on('click', '.button.config', function () {
        const idCurso = $(this).data('id');
        $('#btnFinalizar').data('id', idCurso);
        $('#btnGuardar').data('id', idCurso);
        $('#btnReiniciarCurso').data('id', idCurso);

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

                $('#btnGuardar').prop('disabled', !culminado);
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
                                            <option value="" ${!estado?'selected':''}>Seleccionar</option>
                                            <option value="Aprobado"     ${estado==='Aprobado'    ?'selected':''}>Aprobado</option>
                                            <option value="Reprobado"    ${estado==='Reprobado'   ?'selected':''}>Reprobado</option>
                                            <option value="Ausente"      ${estado==='Ausente'     ?'selected':''}>Ausente</option>
                                            <option value="Pendiente"    ${estado==='Pendiente'   ?'selected':''}>Pendiente</option>
                                            <option value="En formación" ${estado==='En formación'?'selected':''}>En formación</option>
                                        </select>
                                    </div>
                                </div>
                            </div>`);
                    });
                }

                $('#contadorEstudiantes').text(
                    `${participantes ? participantes.length : 0} estudiante${participantes && participantes.length !== 1 ? 's' : ''}`
                );
                $('#cursoModal').modal('show');
            })
            .catch(err => { console.error(err); toastr.error('Error al cargar los participantes'); });
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
                if (val === 'Pendiente' || val === 'En formación') estadosInvalidos++;
            });

            if (notasFaltantes > 0) {
                toastr.warning(`Faltan notas por asignar a ${notasFaltantes} participante(s). Asigna y guarda todas las calificaciones antes de reiniciar.`);
                return;
            }
            if (estadosInvalidos > 0) {
                toastr.warning(`${estadosInvalidos} participante(s) tienen estado "Pendiente" o "En formación". Solo se aceptan Aprobado, Reprobado o Ausente.`);
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
            $('#modalReiniciarCurso').modal('show');

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
            $('#modalReiniciarCurso').modal('show');
        }
    });

    // =========================================================
    // VALIDACIÓN DE FECHAS
    // =========================================================
    function validarFechasReinicio() {
        const hoy = new Date(); hoy.setHours(0,0,0,0);
        const iniStr = $('#nuevaFechaInicio').val();
        const finStr = $('#nuevaFechaFin').val();
        $('#errorFechaInicio, #errorFechaFin').hide().text('');
        let ok = true;

        if (iniStr && new Date(iniStr + 'T00:00:00') < hoy) {
            $('#errorFechaInicio').text('La fecha de inicio no puede ser en el pasado.').show();
            ok = false;
        }
        if (iniStr && finStr) {
            const ini = new Date(iniStr + 'T00:00:00');
            const fin = new Date(finStr + 'T00:00:00');
            if (fin < ini)                          { $('#errorFechaFin').text('La fecha fin no puede ser anterior al inicio.').show(); ok = false; }
            if ((fin-ini)/86400000 < 21)            { $('#errorFechaFin').text('Mínimo 3 semanas (21 días) entre fechas.').show(); ok = false; }
        }
        return ok;
    }

    $('#nuevaFechaInicio').on('change', function () {
        const iniStr = $(this).val();
        if (!iniStr) { $('#fechaLimiteInicio').val(''); return; }

        const ini    = new Date(iniStr + 'T00:00:00');
        const limite = new Date(ini); limite.setDate(limite.getDate() + 9);
        const minFin = new Date(ini); minFin.setDate(minFin.getDate() + 21);
        const pad    = n => String(n).padStart(2, '0');
        const fmt    = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

        $('#fechaLimiteInicio').val(fmt(limite));
        $('#nuevaFechaFin').attr('min', fmt(minFin));
        if ($('#nuevaFechaFin').val() && new Date($('#nuevaFechaFin').val()+'T00:00:00') < minFin) $('#nuevaFechaFin').val('');
        validarFechasReinicio();
    });

    $('#nuevaFechaFin').on('change input', validarFechasReinicio);

    // =========================================================
    // CONFIRMAR REINICIO
    // =========================================================
    $('#confirmarReinicioCurso').on('click', function (e) {
        e.preventDefault();
        if (!$('#nuevaFechaInicio').val() || !$('#nuevaFechaFin').val()) {
            toastr.warning('Complete todos los campos de fecha'); return;
        }
        if (!validarFechasReinicio()) {
            toastr.warning('Verifique los errores en las fechas'); return;
        }

        const idCurso             = $(this).data('id');
        const modo                = $(this).data('modo') || 'diferido';
        const mantenerParticipantes = $('#chkMantenerParticipantes').is(':checked');

        $.ajax({
            url: '../../app/controlador/Curso/reiniciarCurso.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_curso:              idCurso,
                nueva_fecha_inicio:    $('#nuevaFechaInicio').val(),
                nueva_fecha_fin:       $('#nuevaFechaFin').val(),
                modo:                  modo,
                mantener_participantes: mantenerParticipantes
            }),
            success: function (resp) {
                if (resp.success) {
                    toastr.success(resp.message || '¡Curso reiniciado correctamente!');
                    $('#modalReiniciarCurso').modal('hide');
                    $('#cursoModal').modal('hide');
                    cargarTablaCursos();
                } else {
                    toastr.error(resp.message || 'Error al reiniciar el curso');
                }
            },
            error: function () { toastr.error('Error del servidor al reiniciar el curso'); }
        });
    });

    // Al abrir modal: limpiar campos y ocultar cursoModal
    $('#modalReiniciarCurso').on('show.bs.modal', function () {
        $('#cursoModal').modal('hide');
        const hoy = new Date().toISOString().split('T')[0];
        $('#nuevaFechaInicio, #nuevaFechaFin, #fechaLimiteInicio').val('');
        $('#nuevaFechaInicio').attr('min', hoy);
        $('#nuevaFechaFin').attr('min', hoy);
        $('#errorFechaInicio, #errorFechaFin').hide().text('');
        $('#chkMantenerParticipantes').prop('checked', false);
    });

    // Cancelar: volver a cursoModal
    $(document).on('click', '.btn-cancel-reiniciar', function () {
        $('#modalReiniciarCurso').modal('hide');
        $('#cursoModal').modal('show');
    });
});
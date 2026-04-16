$(document).ready(function () {

    const FESTIVOS_2026 = [
        '2026-01-01', '2026-01-06', '2026-02-16', '2026-02-17',
        '2026-04-02', '2026-04-03', '2026-04-19', '2026-05-01',
        '2026-06-24', '2026-07-05', '2026-07-24', '2026-10-12',
        '2026-12-24', '2026-12-25', '2026-12-31'
    ];

    function esFestivo(fecha) { return FESTIVOS_2026.includes(fecha.toISOString().split('T')[0]); }
    function esDomingo(f) { return f.getDay() === 0; }
    function esSabado(f) { return f.getDay() === 6; }

    /** Parseo seguro de números desde la API (coma decimal, strings, etc.) */
    function parseNumApi(v) {
        if (v === null || v === undefined || v === '') return NaN;
        const s = String(v).trim().replace(/\s/g, '').replace(',', '.');
        return parseFloat(s);
    }

    function normNombreCursoEdit(s) {
        return (s || '').trim().toLowerCase().replace(/\s+/g, ' ');
    }

    function syncNombreCursoEdit() {
        const $sel = $('#catalogo_nombre_curso-Edit');
        const v = $sel.val();
        const $nombre = $('#nombre_curso-Edit');
        const $idNc = $('#id_nombrecurso-Edit');
        const $otro = $('#nombre_curso_otro-Edit');

        if (v === '__otro__') {
            $('#wrapper-nombre-otro-Edit').removeClass('d-none');
            $idNc.val('');
            $nombre.val(($otro.val() || '').trim());
        } else if (v) {
            $('#wrapper-nombre-otro-Edit').addClass('d-none');
            $otro.val('');
            const $opt = $sel.find('option:selected');
            $idNc.val(v);
            const oficial = $opt.data('nombre');
            $nombre.val(oficial != null && oficial !== '' ? String(oficial) : $opt.text());
        } else {
            $('#wrapper-nombre-otro-Edit').addClass('d-none');
            $idNc.val('');
            $nombre.val('');
        }
        $nombre.trigger('input');
    }

    function cargarCursosPorPplEdit(idPpl) {
        const $sel = $('#catalogo_nombre_curso-Edit');
        $sel.prop('disabled', true).html('<option value="">Cargando…</option>');
        $('#id_nombrecurso-Edit').val('');
        $('#nombre_curso-Edit').val('');
        $('#nombre_curso_otro-Edit').val('');
        $('#wrapper-nombre-otro-Edit').addClass('d-none');

        return $.getJSON('../controlador/Curso/cursosPorPpl.php', { id_ppl: idPpl })
            .done(function (resp) {
                $sel.empty();
                $sel.append($('<option>', { value: '', text: 'Seleccione un nombre de curso', selected: true, disabled: true }));
                if (!resp || !resp.success || !resp.cursos || !resp.cursos.length) {
                    if (!resp || !resp.success) {
                        toastr.warning((resp && resp.message) || 'Ejecute sql/tb_ppl_nombrecurso.sql para cargar el catálogo por perfil.');
                    } else {
                        toastr.info('No hay cursos en catálogo para este perfil. Use la opción Otro para escribir el nombre.');
                    }
                    $sel.append($('<option>', { value: '__otro__', text: 'Otro (escribir nombre)…' }));
                    $sel.prop('disabled', false);
                    return;
                }
                resp.cursos.forEach(function (c) {
                    const opt = $('<option></option>')
                        .val(c.id_nombrecurso)
                        .attr('data-nombre', c.nombre_curso)
                        .text(c.nombre_curso);
                    $sel.append(opt);
                });
                $sel.append($('<option>', { value: '__otro__', text: 'Otro (escribir nombre)…' }));
                $sel.prop('disabled', false);
            })
            .fail(function () {
                $sel.html('<option value="">Error al cargar cursos</option>').prop('disabled', true);
                toastr.error('No se pudo cargar el catálogo de cursos.');
            });
    }

    /** Misma regla que calcularHorasTotales: días hábiles entre fechas Y-m-d */
    function contarDiasLectivos(fiStr, ffStr) {
        if (!fiStr || !ffStr) return 0;
        const ini = new Date(fiStr + 'T00:00:00');
        const fin = new Date(ffStr + 'T00:00:00');
        if (ini >= fin) return 0;
        let dias = 0;
        const cur = new Date(ini);
        while (cur <= fin) {
            if (!esDomingo(cur) && !esSabado(cur) && !esFestivo(cur)) dias++;
            cur.setDate(cur.getDate() + 1);
        }
        return dias;
    }

    /** Coherente con app/controlador/Curso/obtenerCursoEditar.php */
    function inferirHorasPorClaseDesdeApi(data) {
        const fi = data.fecha_inicio;
        const ff = data.fecha_fin;
        const ht = parseNumApi(data.horas_totales);
        if (!fi || !ff || !isFinite(ht) || ht <= 0) return NaN;
        const dias = contarDiasLectivos(fi, ff);
        if (dias <= 0) return NaN;
        const ratio = ht / dias;
        if (ratio >= 3.55 && ratio <= 4.45) return 4;
        if (ratio >= 4.55 && ratio <= 5.45) return 5;
        const r = Math.round(ratio);
        if (r === 4 || r === 5) return r;
        return NaN;
    }

    function leerHorasPorClaseApi(data) {
        let v = data.horas_por_clase;
        if (v === undefined || v === null) {
            const k = Object.keys(data).find(function (key) {
                return key.toLowerCase() === 'horas_por_clase';
            });
            if (k) v = data[k];
        }
        return v;
    }

    function calcularHorasTotales() {
        const fi = $('#fecha_inicio-Edit').val();
        const ff = $('#fecha_fin-Edit').val();
        const hpc = parseFloat($('#horas_por_clase-Edit').val()) || 0;

        if (!fi || !ff || hpc <= 0) { $('#horas_totales-Edit').val(''); return; }

        const dias = contarDiasLectivos(fi, ff);
        if (dias <= 0) { $('#horas_totales-Edit').val(''); return; }
        $('#horas_totales-Edit').val(Math.round(dias * hpc));
    }

    function showError(id, msg) {
        $('#' + id).addClass('is-invalid').removeClass('is-valid');
        $('#error-' + id).text(msg);
    }
    function showSuccess(id) {
        $('#' + id).addClass('is-valid').removeClass('is-invalid');
        $('#error-' + id).text('');
    }

    function validateNombreCursoEdit() {
        const selected = $('#catalogo_nombre_curso-Edit').val();
        const hiddenName = ($('#nombre_curso-Edit').val() || '').trim();

        if (!selected) {
            showError('catalogo_nombre_curso-Edit', 'Seleccione un nombre de curso');
            return false;
        }

        if (selected === '__otro__') {
            const otro = ($('#nombre_curso_otro-Edit').val() || '').trim();
            if (!otro) {
                showError('nombre_curso_otro-Edit', 'Escriba el nombre del curso');
                return false;
            }
            if (otro.length > 70) {
                showError('nombre_curso_otro-Edit', 'Máximo 70 caracteres');
                return false;
            }
            if (!/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/.test(otro)) {
                showError('nombre_curso_otro-Edit', 'Solo letras, números y espacios');
                return false;
            }
            showSuccess('catalogo_nombre_curso-Edit');
            showSuccess('nombre_curso_otro-Edit');
            return true;
        }

        if (!hiddenName) {
            showError('catalogo_nombre_curso-Edit', 'Nombre del curso inválido');
            return false;
        }

        showSuccess('catalogo_nombre_curso-Edit');
        $('#nombre_curso_otro-Edit').removeClass('is-invalid is-valid');
        $('#error-nombre_curso_otro-Edit').text('');
        return true;
    }

    /**
     * Coherente con agregarCurso.js (límite suele ser posterior al inicio).
     * min/max en el input evitan elegir fuera del rango inicio–culminación.
     * @param {boolean} clampLimite si true, corrige la límite al salirse del rango (solo al cambiar fechas).
     */
    function actualizarRestriccionesFechasEdit(clampLimite) {
        const ini = $('#fecha_inicio-Edit').val();
        const fin = $('#fecha_fin-Edit').val();
        const $lim = $('#fecha_limite_inicio-Edit');

        $('#fecha_fin-Edit').attr('min', ini || '');

        if (ini && fin) {
            const dI = new Date(ini + 'T00:00:00');
            const dF = new Date(fin + 'T00:00:00');
            if (dF <= dI) {
                $lim.removeAttr('min').removeAttr('max');
                return;
            }
            $lim.attr('min', ini).attr('max', fin);
            if (clampLimite) {
                const limVal = $lim.val();
                if (limVal) {
                    if (limVal < ini) { $lim.val(ini); }
                    else if (limVal > fin) { $lim.val(fin); }
                }
            }
        } else {
            $lim.removeAttr('min').removeAttr('max');
        }
    }

    function validateFechas() {
        let ok = true;
        const lim = $('#fecha_limite_inicio-Edit').val();
        const ini = $('#fecha_inicio-Edit').val();
        const fin = $('#fecha_fin-Edit').val();

        if (!ini) { showError('fecha_inicio-Edit', 'Fecha inicio requerida'); ok = false; }
        else { showSuccess('fecha_inicio-Edit'); }
        if (!fin) { showError('fecha_fin-Edit', 'Fecha fin requerida'); ok = false; }
        else { showSuccess('fecha_fin-Edit'); }

        if (ini && fin) {
            const dI = new Date(ini + 'T00:00:00');
            const dF = new Date(fin + 'T00:00:00');
            if (dF <= dI) {
                showError('fecha_fin-Edit', 'Debe ser posterior al inicio');
                ok = false;
                $('#fecha_limite_inicio-Edit').removeClass('is-invalid is-valid');
                $('#error-fecha_limite_inicio-Edit').text('');
            } else if (lim) {
                const dL = new Date(lim + 'T00:00:00');
                if (dL < dI) {
                    showError('fecha_limite_inicio-Edit', 'No puede ser anterior a la fecha de inicio');
                    ok = false;
                } else if (dL > dF) {
                    showError('fecha_limite_inicio-Edit', 'No puede ser posterior a la fecha de culminación');
                    ok = false;
                } else {
                    showSuccess('fecha_limite_inicio-Edit');
                }
            } else {
                $('#fecha_limite_inicio-Edit').removeClass('is-invalid is-valid');
                $('#error-fecha_limite_inicio-Edit').text('');
            }
        }
        return ok;
    }

    function validateHorasPorClase() {
        const raw = $('#horas_por_clase-Edit').val();
        const h = parseFloat(raw);
        if (raw === '' || raw === null || isNaN(h)) {
            showError('horas_por_clase-Edit', 'Seleccione horas por clase');
            return false;
        }
        if (h !== 4 && h !== 5) {
            showError('horas_por_clase-Edit', 'Solo se permiten 4 o 5 horas por clase');
            return false;
        }
        showSuccess('horas_por_clase-Edit');
        return true;
    }

    function validateParticipantes() {
        const min = parseInt($('#min_participantes-Edit').val()) || 0;
        const max = parseInt($('#max_participantes-Edit').val()) || 0;
        let ok = true;
        if (min < 12 || min > 40) { showError('min_participantes-Edit', 'Entre 12 y 40'); ok = false; }
        else { showSuccess('min_participantes-Edit'); }
        if (max < 12 || max > 40) { showError('max_participantes-Edit', 'Entre 12 y 40'); ok = false; }
        else { showSuccess('max_participantes-Edit'); }
        if (ok && min > max) { showError('max_participantes-Edit', 'Debe ser ≥ al mínimo'); ok = false; }
        return ok;
    }

    function validateSelect(id, msg) {
        const v = $('#' + id).val();
        if (!v || v === '') { showError(id, msg); return false; }
        showSuccess(id);
        return true;
    }

    // Eventos en tiempo real
    $('#id_ppl-Edit').on('change', function () {
        const id = $(this).val();
        if (!id) {
            $('#catalogo_nombre_curso-Edit').prop('disabled', true).html('<option value="">Seleccione primero un perfil productivo</option>');
            syncNombreCursoEdit();
            return;
        }
        cargarCursosPorPplEdit(id).always(function () {
            syncNombreCursoEdit();
        });
    });

    $('#catalogo_nombre_curso-Edit').on('change', function () {
        syncNombreCursoEdit();
        validateNombreCursoEdit();
    });

    $('#nombre_curso_otro-Edit').on('input', function () {
        if ($('#catalogo_nombre_curso-Edit').val() !== '__otro__') {
            return;
        }
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            syncNombreCursoEdit();
            validateNombreCursoEdit();
        }, 200));
    });

    $('#fecha_limite_inicio-Edit, #fecha_inicio-Edit, #fecha_fin-Edit').on('change', function () {
        actualizarRestriccionesFechasEdit(true);
        validateFechas();
        calcularHorasTotales();
    });
    $('#horas_por_clase-Edit').on('change input', function () {
        validateHorasPorClase();
        calcularHorasTotales();
    });
    $('#min_participantes-Edit, #max_participantes-Edit').on('input', validateParticipantes);

    // =====================================================
    // CARGAR DATOS AL ABRIR — FIX CRÍTICO
    // Antes: obtenerCurso.php → solo 3 campos
    // Ahora: obtenerCursoEditar.php → todos los campos
    // =====================================================
    $(document).on('click', '[data-edit-curso]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const idCurso = $(this).data('id');
        if (!idCurso || isNaN(idCurso)) { toastr.error('ID de curso inválido'); return; }

        const $btn = $('#btn-submit-editCurso');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Cargando...');

        fetch(`../../app/controlador/Curso/obtenerCursoEditar.php?id=${idCurso}`)
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                if (data.error) throw new Error(data.error);

                $('#id_curso-Edit').val(idCurso);
                $('#nombre_curso-Edit').val(data.nombre_curso || '');
                $('#turno-Edit').val(data.turno || '');
                const hpcEl = document.getElementById('horas_por_clase-Edit');
                const $hpc = hpcEl ? $(hpcEl) : $();
                let hpcNum = parseNumApi(leerHorasPorClaseApi(data));
                let hpcRounded = !isNaN(hpcNum) ? Math.round(hpcNum) : NaN;
                if (hpcRounded !== 4 && hpcRounded !== 5) {
                    const inferred = inferirHorasPorClaseDesdeApi(data);
                    if (inferred === 4 || inferred === 5) {
                        hpcRounded = inferred;
                    }
                }
                if (hpcRounded === 4 || hpcRounded === 5) {
                    $hpc.val(String(hpcRounded));
                } else {
                    $hpc.val('4');
                }
                $('#horas_totales-Edit').val(data.horas_totales || '');
                $('#min_participantes-Edit').val(data.min_participantes || 12);
                $('#max_participantes-Edit').val(data.max_participantes || '');
                $('#fecha_inicio-Edit').val(data.fecha_inicio || '');
                $('#fecha_fin-Edit').val(data.fecha_fin || '');
                $('#fecha_limite_inicio-Edit').val(data.fecha_limite_inicio || '');

                $('#id_ppl-Edit').val(data.id_ppl || '');
                if (data.id_ppl) {
                    cargarCursosPorPplEdit(data.id_ppl).done(function () {
                        if (data.id_nombre_especifico) {
                            $('#catalogo_nombre_curso-Edit').val(String(data.id_nombre_especifico));
                        } else {
                            const nombreNorm = normNombreCursoEdit(data.nombre_curso || '');
                            const $match = $('#catalogo_nombre_curso-Edit option').filter(function () {
                                const val = $(this).val();
                                return val !== '' && val !== '__otro__' && normNombreCursoEdit($(this).text()) === nombreNorm;
                            }).first();

                            if ($match.length) {
                                $match.prop('selected', true);
                            } else {
                                $('#catalogo_nombre_curso-Edit').val('__otro__');
                                $('#wrapper-nombre-otro-Edit').removeClass('d-none');
                                $('#nombre_curso_otro-Edit').val(data.nombre_curso || '');
                            }
                        }
                        syncNombreCursoEdit();
                    }).fail(function () {
                        $('#catalogo_nombre_curso-Edit').prop('disabled', true).html('<option value="">Seleccione primero un perfil productivo</option>');
                        $('#nombre_curso-Edit').val(data.nombre_curso || '');
                    });
                } else {
                    $('#catalogo_nombre_curso-Edit').prop('disabled', true).html('<option value="">Seleccione primero un perfil productivo</option>');
                    $('#nombre_curso-Edit').val(data.nombre_curso || '');
                }

                // Selects: asignar y aplicar fallback por entero
                const selects = [
                    ['id_modalidad-Edit', data.id_modalidad],
                    ['id_sectoreconomico-Edit', data.id_sectoreconomico],
                    ['id_subtipo-Edit', data.id_subtipo],
                    ['id_ambito-Edit', data.id_ambito]
                ];

                selects.forEach(function ([id, val]) {
                    if (val !== null && val !== undefined && val !== '') {
                        const $s = $('#' + id);
                        $s.val(String(val));
                        if ($s.val() === null || $s.val() === '') {
                            $s.find('option').each(function () {
                                if (parseInt($(this).val()) === parseInt(val)) {
                                    $(this).prop('selected', true);
                                }
                            });
                        }
                    }
                });

                if (data.fecha_inicio && data.fecha_fin && (hpcRounded === 4 || hpcRounded === 5)) {
                    calcularHorasTotales();
                }

                actualizarRestriccionesFechasEdit(false);

                $btn.prop('disabled', false).html(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle me-2" viewBox="0 0 16 16">' +
                    '<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>' +
                    '<path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>' +
                    '</svg>Guardar Cambios'
                );
                $('#modal-editCurso').modal('show');

                // Disparar validaciones iniciales para mostrar estado visual inmediato
                setTimeout(() => {
                    validateNombreCursoEdit();
                    actualizarRestriccionesFechasEdit(false);
                    validateFechas();
                    validateHorasPorClase();
                    validateParticipantes();
                    validateSelect('id_modalidad-Edit', 'Seleccione una modalidad');
                    validateSelect('id_ppl-Edit', 'Seleccione un perfil productivo laboral');
                    validateSelect('id_sectoreconomico-Edit', 'Seleccione un sector económico');
                    validateSelect('id_subtipo-Edit', 'Seleccione un subtipo');
                    validateSelect('id_ambito-Edit', 'Seleccione un ámbito');
                    validateSelect('turno-Edit', 'Seleccione un turno');
                    validateNombreCursoEdit();
                    calcularHorasTotales();
                }, 500);
            })
            .catch(err => {
                console.error(err);
                toastr.error('Error al cargar los datos: ' + err.message);
                $btn.prop('disabled', false).html('Guardar Cambios');
            });
    });

    // Enviar formulario
    $('#form_editCurso').on('submit', function (e) {
        e.preventDefault();

        const checks = [
            validateNombreCursoEdit(),
            validateSelect('id_modalidad-Edit', 'Seleccione una modalidad'),
            validateSelect('id_ppl-Edit', 'Seleccione un perfil productivo laboral'),
            validateSelect('id_sectoreconomico-Edit', 'Seleccione un sector económico'),
            validateSelect('id_subtipo-Edit', 'Seleccione un subtipo'),
            validateSelect('id_ambito-Edit', 'Seleccione un ámbito'),
            validateSelect('turno-Edit', 'Seleccione un turno'),
            validateFechas(),
            validateHorasPorClase(),
            validateParticipantes()
        ];

        if (!checks.every(v => v === true)) {
            toastr.warning('Complete todos los campos correctamente antes de guardar');
            return;
        }

        calcularHorasTotales();

        const formData = new FormData(this);
        const $btn = $('#btn-submit-editCurso');
        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '../../app/controlador/Curso/editarCurso.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (resp) {
                if (resp && resp.success) {
                    toastr.success(resp.message || 'Curso actualizado correctamente');
                    $('#modal-editCurso').modal('hide');
                    $(document).trigger('cursos:actualizados');
                } else {
                    toastr.error(resp.message || 'Error al actualizar el curso');
                }
            },
            error: function (xhr, status, err) {
                console.error('AJAX error:', err, xhr.responseText);
                toastr.error('Error en el servidor al guardar los cambios');
            },
            complete: function () { $btn.prop('disabled', false).html(origHtml); }
        });
    });

    // Limpiar al cerrar
    $('#modal-editCurso').on('hidden.bs.modal', function () {
        $('#form_editCurso')[0].reset();
        $('#id_ppl-Edit').prop('selectedIndex', 0);
        $('#catalogo_nombre_curso-Edit').prop('disabled', true).html('<option value="">Seleccione primero un perfil productivo</option>');
        $('#wrapper-nombre-otro-Edit').addClass('d-none');
        $('#nombre_curso_otro-Edit').val('');
        $('#id_nombrecurso-Edit').val('');
        $('#fecha_limite_inicio-Edit, #fecha_inicio-Edit, #fecha_fin-Edit').removeAttr('min').removeAttr('max');
        $(this).find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $(this).find('.error-feedback').text('');
        $('#horas_totales-Edit').val('');
    });
});
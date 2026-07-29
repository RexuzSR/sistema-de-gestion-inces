// Manejar el envío del formulario de registro
$(document).ready(function () {
    const $form = $('#form-participante');

    // Configurar límites dinámicos de fecha de nacimiento (rango de 15 a 75 años)
    const todayObj = new Date();
    const yyyyVal = todayObj.getFullYear();
    const mmVal = String(todayObj.getMonth() + 1).padStart(2, '0');
    const ddVal = String(todayObj.getDate()).padStart(2, '0');
    $('#fecha_nacimiento').attr({
        'max': `${yyyyVal - 15}-${mmVal}-${ddVal}`,
        'min': `${yyyyVal - 75}-${mmVal}-${ddVal}`
    });

    // Funciones de validación
    function mostrarError(campoId, show = true) {
        const $campo = $(`#${campoId}`);
        const $error = $(`#error-${campoId}`);
        if (show) {
            $campo.addClass('is-invalid').removeClass('is-valid');
            $error.show();
        } else {
            $campo.removeClass('is-invalid').addClass('is-valid');
            $error.hide();
        }
    }

    function mostrarValido(campoId) {
        $(`#${campoId}`).addClass('is-valid').removeClass('is-invalid');
        $(`#error-${campoId}`).hide();
    }

    function limpiarValidacion(campoId) {
        $(`#${campoId}`).removeClass('is-valid is-invalid');
        $(`#error-${campoId}`).hide();
    }

    // Función auxiliar para validar campos de texto
    function validarCampoTexto(campoId, valor) {
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;

        if (valor === "") {
            const isRequired = $(`#${campoId}`).prop('required');
            if (isRequired) {
                mostrarError(campoId, true);
                $(`#error-${campoId}`).text('Campo obligatorio');
                return false;
            }
            limpiarValidacion(campoId);
            return true;
        }

        if (valor.length < 3) {
            mostrarError(campoId, true);
            $(`#error-${campoId}`).text('Mínimo 3 caracteres');
            return false;
        }

        if (valor.length > 30) {
            mostrarError(campoId, true);
            $(`#error-${campoId}`).text('Máximo 30 caracteres');
            return false;
        }

        if (regex.test(valor)) {
            mostrarValido(campoId);
            return true;
        } else {
            mostrarError(campoId, true);
            $(`#error-${campoId}`).text('Solo letras y espacios');
            return false;
        }
    }

    // Validaciones específicas
    const validaciones = {
        cedula: function () {
            const val = $('#cedula').val().trim();
            const isValid = /^\d{7,8}$/.test(val);
            mostrarError('cedula', !isValid);
            // Optionally set custom message if invalid
            if (!isValid && val.length > 0) {
                $('#error-cedula').text(val.length < 7 ? 'Mínimo 7 caracteres' : (val.length > 8 ? 'Máximo 8 caracteres' : 'Cédula inválida (7-8 dígitos)'));
            } else if (!isValid) {
                $('#error-cedula').text('Campo obligatorio');
            }
            return isValid;
        },
        primer_nombre: () => validarCampoTexto('primer_nombre', $('#primer_nombre').val().trim()),
        segundo_nombre: () => {
            const val = $('#segundo_nombre').val().trim();
            return val === "" ? (limpiarValidacion('segundo_nombre'), true) : validarCampoTexto('segundo_nombre', val);
        },
        primer_apellido: () => validarCampoTexto('primer_apellido', $('#primer_apellido').val().trim()),
        segundo_apellido: () => {
            const val = $('#segundo_apellido').val().trim();
            return val === "" ? (limpiarValidacion('segundo_apellido'), true) : validarCampoTexto('segundo_apellido', val);
        },
        telefono: () => {
            const prefijo = $('#prefijo_telefono').val();
            const numero = $('#telefono_numero').val().trim();
            const isValid = /^\d{7}$/.test(numero);

            if (isValid) {
                $('#telefono').val(prefijo + numero);
                mostrarValido('telefono');
            } else {
                mostrarError('telefono');
            }
            return isValid;
        },
        fecha_nacimiento: () => {
            const val = $('#fecha_nacimiento').val();
            if (!val) {
                mostrarError('fecha_nacimiento');
                $('#edad_calculada').val('--');
                return false;
            }
            const birthDate = new Date(val + 'T00:00:00');
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;

            if (!isNaN(age) && age >= 0 && age <= 120) {
                $('#edad_calculada').val(age);
            } else {
                $('#edad_calculada').val('--');
            }

            const isValid = age >= 15 && age <= 75;
            mostrarError('fecha_nacimiento', !isValid);
            return isValid;
        },
        genero: () => {
            const isValid = $('input[name="genero"]:checked').length > 0;
            const $container = $('.d-flex.gap-4.p-3'); // Contenedor de radio buttons
            if (isValid) {
                $container.css('border-color', 'rgba(56, 189, 248, 0.4)');
                $('#error-genero').hide();
            } else {
                $container.css('border-color', '#f87171');
                $('#error-genero').show();
            }
            return isValid;
        },
        correo: () => {
            const val = $('#correo').val().trim();
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const isValid = regex.test(val);
            mostrarError('correo', !isValid);
            return isValid;
        }
    };

    // Verificar registro cruzado en Cédula blur
    $('#cedula').on('blur', function () {
        const cedula = $(this).val();
        validaciones.cedula();

        // Limpiar estado previo
        $('#cedula-repetida').hide();
        $form.find('input, select, button[type="submit"]').not('#cedula').prop('disabled', false);

        if (cedula.length >= 7) {
            $.ajax({
                url: "../../app/controlador/verificarCedulaGlobal.php",
                method: 'GET',
                data: { cedula: cedula },
                dataType: 'json',
                success: function (response) {
                    if (response.exists) {
                        if (response.table === 'participantes') {
                            const data = response.data;
                            $('#cedula-repetida').show();
                            
                             // Autocompletar campos con info del participante existente
                             $('#primer_nombre').val(data.primer_nombre).trigger('input');
                             $('#segundo_nombre').val(data.segundo_nombre || '').trigger('input');
                             $('#primer_apellido').val(data.primer_apellido).trigger('input');
                             $('#segundo_apellido').val(data.segundo_apellido || '').trigger('input');
                             $('#correo').val(data.correo).trigger('input');
                             const cedNum = parseInt(data.cedula);
                             const nacVal = (!isNaN(cedNum) && cedNum >= 80000000) ? 'E' : 'V';
                             $('#nacionalidad').val(nacVal).trigger('change');
                            
                            // Rellenar teléfono dividido
                            if (data.telefono) {
                                let telStr = String(data.telefono);
                                if (telStr.length === 10) telStr = '0' + telStr;
                                
                                if (telStr.length >= 11) {
                                    const prefijo = telStr.substring(0, 4);
                                    const numero = telStr.substring(4);
                                    $('#prefijo_telefono').val(prefijo).trigger('change');
                                    $('#telefono_numero').val(numero).trigger('input');
                                    $('#telefono').val(telStr);
                                } else {
                                    $('#telefono').val(telStr).trigger('input');
                                }
                            }
                            
                            $('#fecha_nacimiento').val(data.fecha_nacimiento).trigger('change');
                            
                            const rawGender = data.genero || data.sexo;
                            if (rawGender) {
                                const generoStr = rawGender.toLowerCase();
                                if (generoStr.startsWith('m')) {
                                    $('#genero_masculino').prop('checked', true).trigger('change');
                                } else if (generoStr.startsWith('f')) {
                                    $('#genero_femenino').prop('checked', true).trigger('change');
                                }
                            }
                            
                            if (data.id_discapacidad) {
                                $('#id_discapacidad').val(data.id_discapacidad).trigger('change');
                            }
                            if (data.grado_institucion) {
                                $('#grado_institucion').val(data.grado_institucion).trigger('change');
                            }

                            $form.find('input, select, button[type="submit"]').not('#cedula').prop('disabled', true);
                            toastr.error('Cédula ya registrada como Participante.');
                        } else if (response.table === 'docentes') {
                            const data = response.data;
                            // Bloquear campos inmediatamente
                            $form.find('input, select, button[type="submit"]').not('#cedula').prop('disabled', true);

                            $('#crossRegisterMessage').html(`La cédula <b>${cedula}</b> ya está registrada como <b>Docente</b> (${data.primer_nombre} ${data.primer_apellido}). ¿Desea registrar también como Participante?`);
                            $('#modal-confirmCrossRegister').modal('show');

                            // Manejar "SI"
                            $('#btn-crossRegister-SI').off('click').on('click', function () {
                                $('#modal-confirmCrossRegister').modal('hide');
                                // Desbloquear campos para autocompletar
                                $form.find('input, select, button[type="submit"]').not('#cedula').prop('disabled', false);

                                setTimeout(() => {
                                    $('#primer_nombre').val(data.primer_nombre).trigger('input');
                                    $('#segundo_nombre').val(data.segundo_nombre || '').trigger('input');
                                    $('#primer_apellido').val(data.primer_apellido).trigger('input');
                                    $('#segundo_apellido').val(data.segundo_apellido || '').trigger('input');
                                    $('#correo').val(data.correo).trigger('input');
                                    
                                    if (data.telefono) {
                                        let telStr = String(data.telefono);
                                        if (telStr.length === 10) telStr = '0' + telStr;
                                        
                                        if (telStr.length >= 11) {
                                            const prefijo = telStr.substring(0, 4);
                                            const numero = telStr.substring(4);
                                            $('#prefijo_telefono').val(prefijo).trigger('change');
                                            $('#telefono_numero').val(numero).trigger('input');
                                            $('#telefono').val(telStr);
                                        } else {
                                            $('#telefono').val(telStr).trigger('input');
                                        }
                                    }

                                    $('#fecha_nacimiento').val(data.fecha_nacimiento).trigger('change');

                                    const rawGender = data.genero || data.sexo;
                                    if (rawGender) {
                                        const generoStr = rawGender.toLowerCase();
                                        if (generoStr.startsWith('m')) {
                                            $('#genero_masculino').prop('checked', true).trigger('change');
                                        } else if (generoStr.startsWith('f')) {
                                            $('#genero_femenino').prop('checked', true).trigger('change');
                                        }
                                    }
                                    
                                    if (data.id_discapacidad) {
                                        $('#id_discapacidad').val(data.id_discapacidad).trigger('change');
                                    }
                                    if (data.grado_institucion) {
                                        $('#grado_institucion').val(data.grado_institucion).trigger('change');
                                    }
                                    
                                    toastr.success('Campos autocompletados desde el registro de Docentes');
                                }, 300);
                            });

                            // Manejar "NO"
                            $('#btn-crossRegister-NO').off('click').on('click', function () {
                                $('#modal-confirmCrossRegister').modal('hide');
                                $('#cedula').val('').trigger('input').focus();
                                // Los campos permanecen bloqueados porque cedula.length será < 7
                            });
                        }
                    }
                }
            });
        }
    });

    // Eventos en tiempo real
    $('#cedula').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '');
        // Limitar a máximo 9 caracteres en tiempo real
        if (this.value.length > 9) {
            this.value = this.value.slice(0, 9);
        }
        if (this.value.length >= 7) validaciones.cedula();
        else mostrarError('cedula', true); // Mostrar error si es < 7 mientras escribe (opcional, o dejar que 'blur' se encargue)
    });

    $('#primer_nombre, #segundo_nombre, #primer_apellido, #segundo_apellido').on('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        if (this.value.length > 30) this.value = this.value.slice(0, 30);
        // Auto-capitalizar primera letra de cada palabra
        this.value = this.value.split(' ').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');
        validaciones[this.id]();
    });

    $('#telefono_numero').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 7) validaciones.telefono();
    });

    $('#prefijo_telefono').on('change', validaciones.telefono);

    $('#correo').on('input', validaciones.correo);
    $('#fecha_nacimiento').on('change input', validaciones.fecha_nacimiento);
    $('input[name="genero"]').on('change', validaciones.genero);

    // Interacción de selección múltiple de discapacidades
    $(document).on('change', '.disc-checkbox', function () {
        const isNinguna = $(this).val() === '1';
        if (isNinguna && $(this).is(':checked')) {
            $('.disc-checkbox').not(this).prop('checked', false);
        } else if ($(this).is(':checked')) {
            $('#disc-1').prop('checked', false);
        }
        // Si no queda ninguna seleccionada, marcar "Ninguna"
        if ($('.disc-checkbox:checked').length === 0) {
            $('#disc-1').prop('checked', true);
        }
    });

    // Envío del formulario
    $form.on('submit', function (e) {
        e.preventDefault();

        const formValido =
            validaciones.cedula() &&
            validaciones.primer_nombre() &&
            validaciones.segundo_nombre() &&
            validaciones.primer_apellido() &&
            validaciones.segundo_apellido() &&
            validaciones.telefono() &&
            validaciones.fecha_nacimiento() &&
            validaciones.genero() &&
            validaciones.correo();

        if (!formValido) {
            toastr.warning('Por favor, complete los campos correctamente.');
            return;
        }

        // Mostrar loading
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="bi bi-hourglass-split"></i> Procesando...');
        $submitBtn.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "../controlador/cargar.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modal-addParticipante').modal('hide');
                    $form[0].reset();
                    $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                    $('.invalid-feedback').hide();

                    if (response.id) {
                        sessionStorage.setItem('participanteRecienCreado', response.id);
                    }

                    if (typeof window.recargarTablaParticipantes === 'function') {
                        window.recargarTablaParticipantes();
                    } else if (typeof aplicarFiltros === 'function') {
                        aplicarFiltros();
                    }
                    if (typeof actualizarContadores === 'function') actualizarContadores();

                    // LÓGICA DE RETORNO: Si venimos del modal de asignación de curso
                    const cursoRetorno = $('#modal-addParticipante').data('curso-retorno');
                    if (cursoRetorno) {
                        $('#modal-addParticipante').removeData('curso-retorno');
                        console.log("Detectado retorno a curso:", cursoRetorno);

                        // Esperar a que el modal de registro se cierre completamente antes de abrir el otro
                        setTimeout(() => {
                            // Crear un elemento dummy para disparar el modal con el ID correcto 
                            // tal como lo hace el botón de la tabla
                            const $dummyBtn = $('<button>')
                                .attr('data-id', cursoRetorno)
                                .attr('data-bs-toggle', 'modal')
                                .attr('data-bs-target', '#modal-participanteCurso')
                                .hide()
                                .appendTo('body');

                            $dummyBtn.click();
                            $dummyBtn.remove();
                        }, 500);
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error('Error de conexión. Por favor, intente nuevamente.');
            },
            complete: function () {
                $submitBtn.html(originalText);
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Limpiar formulario al cerrar
    $('#modal-addParticipante').on('hidden.bs.modal', function () {
        $form[0].reset();
        $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $form.find('.invalid-feedback').hide();
    });
});

$(document).ready(function () {
    const $form = $('#form-addDocente');

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
                mostrarError(campoId);
                return false;
            }
            limpiarValidacion(campoId);
            return true;
        }

        if (valor.length > 30) {
            mostrarError(campoId);
            $(`#error-${campoId}`).text('Máximo 30 caracteres');
            return false;
        }

        if (regex.test(valor)) {
            mostrarValido(campoId);
            return true;
        } else {
            mostrarError(campoId);
            return false;
        }
    }

    // Validaciones específicas
    const validaciones = {
        cedula: function () {
            const val = $('#cedula').val().trim();
            const isValid = /^\d{7,9}$/.test(val);
            mostrarError('cedula', !isValid);
            if (!isValid && val.length > 0) {
                $('#error-cedula').text(val.length < 7 ? 'Mínimo 7 caracteres' : (val.length > 9 ? 'Máximo 9 caracteres' : 'Cédula inválida'));
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
            const completo = prefijo + numero;

            const regex = /^(0412|0414|0416|0424|0426|0293|0212)\d{7}$/;
            const isValid = numero === "" || regex.test(completo);

            mostrarError('telefono', !isValid);
            if (isValid && numero !== "") {
                $('#telefono').val(completo);
            } else {
                $('#telefono').val('');
            }
            return isValid;
        },
        fechaNacimiento: () => {
            const val = $('#fechaNacimiento').val();
            if (!val) {
                mostrarError('fechaNacimiento');
                return false;
            }
            const birthDate = new Date(val);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;

            const isValid = age >= 15 && age <= 75;
            mostrarError('fechaNacimiento', !isValid);
            if (isValid) $('#edad').val(age);
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

    // Eventos en tiempo real
    $('#cedula').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 9) {
            this.value = this.value.slice(0, 9);
        }
        if (this.value.length >= 7) validaciones.cedula();
        else mostrarError('cedula', true);
    });

    $('#primer_nombre, #segundo_nombre, #primer_apellido, #segundo_apellido').on('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        if (this.value.length > 30) this.value = this.value.slice(0, 30);
        validaciones[this.id]();
    });

    $('#prefijo_telefono').on('change', validaciones.telefono);
    $('#telefono_numero').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        validaciones.telefono();
    });

    $('#correo').on('input', validaciones.correo);
    $('#fechaNacimiento').on('change', validaciones.fechaNacimiento);

    // Verificar registro cruzado en Cédula blur
    $('#cedula').on('blur', function () {
        const cedula = $(this).val();
        validaciones.cedula();

        // Limpiar estado previo
        $('#cedula-repetida').hide();
        $form.find('input, select, button[type="submit"], #btn-addDocente-submit').not('#cedula').prop('disabled', false);

        if (cedula.length >= 7) {
            $.ajax({
                url: '../../app/controlador/verificarCedulaGlobal.php',
                method: 'GET',
                data: { cedula: cedula },
                dataType: 'json',
                success: function (response) {
                    if (response.exists) {
                        if (response.table === 'docentes') {
                            $('#cedula-repetida').show();
                            $form.find('input, select, #btn-addDocente-submit').not('#cedula').prop('disabled', true);
                            toastr.error('Cédula ya registrada como Docente.');
                        } else if (response.table === 'participantes') {
                            const data = response.data;
                            // Bloquear campos inmediatamente
                            $form.find('input, select, #btn-addDocente-submit').not('#cedula').prop('disabled', true);

                            $('#crossRegisterMessage').html(`La cédula <b>${cedula}</b> ya está registrada como <b>Participante</b> (${data.primer_nombre} ${data.primer_apellido}). ¿Desea registrar también como Docente?`);
                            $('#modal-confirmCrossRegister').modal('show');

                            // Manejar "SI"
                            $('#btn-crossRegister-SI').off('click').on('click', function () {
                                $('#modal-confirmCrossRegister').modal('hide');
                                // Desbloquear campos para autocompletar
                                $form.find('input, select, #btn-addDocente-submit').not('#cedula').prop('disabled', false);

                                setTimeout(() => {
                                    $('#primer_nombre').val(data.primer_nombre).trigger('input');
                                    $('#segundo_nombre').val(data.segundo_nombre || '').trigger('input');
                                    $('#primer_apellido').val(data.primer_apellido).trigger('input');
                                    $('#segundo_apellido').val(data.segundo_apellido || '').trigger('input');
                                    $('#correo').val(data.correo).trigger('input');
                                    if (data.telefono && data.telefono.length >= 10) {
                                        const pref = data.telefono.substring(0, 4);
                                        const num = data.telefono.substring(4);
                                        $('#prefijo_telefono').val(pref);
                                        $('#telefono_numero').val(num);
                                        $('#telefono').val(data.telefono);
                                    }
                                    $('#fechaNacimiento').val(data.fecha_nacimiento).trigger('change');

                                    const rawGender = data.genero || data.sexo;
                                    if (rawGender) {
                                        const generoStr = rawGender.toLowerCase();
                                        const targetValue = generoStr.startsWith('m') ? 'Masculino' : (generoStr.startsWith('f') ? 'Femenino' : null);
                                        if (targetValue) $('#genero').val(targetValue);
                                    }
                                    toastr.success('Campos autocompletados desde el registro de Participantes');
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

    // Manejar el envío del formulario de agregar docente
    $form.on('submit', function (e) {
        e.preventDefault();

        const formValido =
            validaciones.cedula() &&
            validaciones.primer_nombre() &&
            validaciones.segundo_nombre() &&
            validaciones.primer_apellido() &&
            validaciones.segundo_apellido() &&
            validaciones.telefono() &&
            validaciones.fechaNacimiento() &&
            validaciones.correo();

        if (!formValido) {
            toastr.warning('Por favor, complete los campos correctamente.');
            return;
        }

        // Mostrar carga
        $('#btn-addDocente-submit').prop('disabled', true).html('Guardando...');

        $.ajax({
            url: '../../app/controlador/Docente/cargardocente.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modal-addDocente').modal('hide');
                    $form[0].reset();
                    $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                    $('.invalid-feedback').hide();

                    if (typeof aplicarFiltros === 'function') aplicarFiltros();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error('Error de conexión con el servidor');
            },
            complete: function () {
                $('#btn-addDocente-submit').prop('disabled', false).html('Guardar');
            }
        });
    });

    // Limpiar formulario al cerrar
    $('#modal-addDocente').on('hidden.bs.modal', function () {
        $form[0].reset();
        $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $form.find('.invalid-feedback').hide();
    });
});

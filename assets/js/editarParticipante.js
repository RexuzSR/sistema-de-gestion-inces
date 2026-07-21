// FORMULARIO DE EDICIÓN
$(document).ready(function () {
    const $formEdit = $('#form-editar-participante');

    // Configurar límites dinámicos de fecha de nacimiento (rango de 15 a 75 años)
    const todayObj = new Date();
    const yyyyVal = todayObj.getFullYear();
    const mmVal = String(todayObj.getMonth() + 1).padStart(2, '0');
    const ddVal = String(todayObj.getDate()).padStart(2, '0');
    $formEdit.find('#fecha_nacimiento-Edit').attr({
        'max': `${yyyyVal - 15}-${mmVal}-${ddVal}`,
        'min': `${yyyyVal - 75}-${mmVal}-${ddVal}`
    });

    // Funciones de validación contextualizadas
    function mostrarError(campoId, mensaje) {
        $formEdit.find(`#${campoId}`).addClass('is-invalid').removeClass('is-valid');
        $formEdit.find(`#error-${campoId}`).text(mensaje).show();
    }

    function mostrarValido(campoId) {
        $formEdit.find(`#${campoId}`).addClass('is-valid').removeClass('is-invalid');
        $formEdit.find(`#error-${campoId}`).text('').hide();
    }

    // Función auxiliar para validar campos de texto
    function validarCampoTexto(campoId, valor) {
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;

        if (valor.length < 3 && valor !== "") {
            mostrarError(campoId, 'Mínimo 3 caracteres');
            return false;
        }
        if (valor.length > 30) {
            mostrarError(campoId, 'Máximo 30 caracteres');
            return false;
        }

        return regex.test(valor)
            ? (mostrarValido(campoId), true)
            : (mostrarError(campoId, 'Solo letras válidas'), false);
    }

    // Validaciones específicas para este formulario
    const validaciones = {
        cedula: async () => {
            const valor = $formEdit.find('#cedula-Edit').val().trim();
            const idParticipante = $formEdit.find('#idParticipante').val();

            if (!/^\d{7,8}$/.test(valor)) {
                const msg = valor.length === 0
                    ? 'Campo obligatorio'
                    : (valor.length < 7 ? 'Mínimo 7 caracteres' : (valor.length > 8 ? 'Máximo 8 caracteres' : 'Cédula inválida (7-8 dígitos)'));
                mostrarError('cedula-Edit', msg);
                return false;
            }

            try {
                const response = await fetch(`../../app/controlador/verificarcedulaeditar.php?cedula=${valor}&idParticipante=${idParticipante}`);
                const data = await response.json();

                if (data.existe) {
                    mostrarError('cedula-Edit', 'Cédula ya registrada');
                    return false;
                }

                mostrarValido('cedula-Edit');
                return true;
            } catch (error) {
                console.error('Error:', error);
                mostrarError('cedula-Edit', 'Error verificando cédula');
                return false;
            }
        },

        primer_nombre: () => validarCampoTexto('primer_nombre-Edit', $formEdit.find('#primer_nombre-Edit').val().trim()),
        segundo_nombre: () => {
            const val = $formEdit.find('#segundo_nombre-Edit').val().trim();
            return val === "" ? (mostrarValido('segundo_nombre-Edit'), true) : validarCampoTexto('segundo_nombre-Edit', val);
        },
        primer_apellido: () => validarCampoTexto('primer_apellido-Edit', $formEdit.find('#primer_apellido-Edit').val().trim()),
        segundo_apellido: () => {
            const val = $formEdit.find('#segundo_apellido-Edit').val().trim();
            return val === "" ? (mostrarValido('segundo_apellido-Edit'), true) : validarCampoTexto('segundo_apellido-Edit', val);
        },

        telefono: () => {
            const prefijo = $formEdit.find('#prefijo_telefono-Edit').val();
            const numero = $formEdit.find('#telefono_numero-Edit').val().trim();
            const isValid = /^\d{7}$/.test(numero);

            if (isValid) {
                $formEdit.find('#telefono-Edit').val(prefijo + numero);
                mostrarValido('telefono-Edit');
            } else {
                mostrarError('telefono-Edit', 'Debe ingresar 7 dígitos');
            }
            return isValid;
        },

        fecha_nacimiento: () => {
            const fechaInput = $formEdit.find('#fecha_nacimiento-Edit').val();
            if (!fechaInput) {
                mostrarError('fecha_nacimiento-Edit', 'Campo obligatorio');
                $formEdit.find('#edad_calculada-Edit').val('--');
                return false;
            }

            const birthDate = new Date(fechaInput + 'T00:00:00');
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;

            if (!isNaN(age) && age >= 0 && age <= 120) {
                $formEdit.find('#edad_calculada-Edit').val(age);
            } else {
                $formEdit.find('#edad_calculada-Edit').val('--');
            }

            if (birthDate > today) {
                mostrarError('fecha_nacimiento-Edit', 'Fecha futura no permitida');
                return false;
            }
            if (age < 15 || age > 75) {
                mostrarError('fecha_nacimiento-Edit', 'Edad debe ser entre 15 y 75 años');
                return false;
            }
            mostrarValido('fecha_nacimiento-Edit');
            return true;
        },

        genero: () => {
            const isValid = $formEdit.find('input[name="genero"]:checked').length > 0;
            return isValid
                ? (mostrarValido('genero'), true)
                : (mostrarError('genero', 'Seleccione un género'), false);
        },

        correo: () => {
            const valor = $formEdit.find('#correo-Edit').val().trim();
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return regex.test(valor)
                ? (mostrarValido('correo-Edit'), true)
                : (mostrarError('correo-Edit', 'Correo inválido'), false);
        },

        grado_institucion: () => {
            const isValid = $formEdit.find('#grado_institucion-Edit').val() !== "";
            return isValid
                ? (mostrarValido('grado_institucion-Edit'), true)
                : (mostrarError('grado_institucion-Edit', 'Seleccione un grado'), false);
        },

        discapacidad: () => {
            const valor = $formEdit.find('#id_discapacidad-Edit').val();
            const isValid = valor !== "" && valor !== null;
            return isValid
                ? (mostrarValido('id_discapacidad-Edit'), true)
                : (mostrarError('id_discapacidad-Edit', 'Seleccione una discapacidad'), false);
        }
    };

    // Eventos de validación
    $formEdit.find('#cedula-Edit').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 9) this.value = this.value.slice(0, 9);
        validaciones.cedula();
    });
    $formEdit.find('#primer_nombre-Edit').on('input', validaciones.primer_nombre);
    $formEdit.find('#segundo_nombre-Edit').on('input', validaciones.segundo_nombre);
    $formEdit.find('#primer_apellido-Edit').on('input', validaciones.primer_apellido);
    $formEdit.find('#segundo_apellido-Edit').on('input', validaciones.segundo_apellido);

    // Restringir entrada a solo letras para nombres y apellidos
    $formEdit.find('#primer_nombre-Edit, #segundo_nombre-Edit, #primer_apellido-Edit, #segundo_apellido-Edit').on('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        if (this.value.length > 30) this.value = this.value.slice(0, 30);
        // Auto-capitalizar primera letra de cada palabra
        this.value = this.value.split(' ').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');

        // Disparar validación visual inmediata
        const fieldName = this.id.replace('-Edit', '');
        if (validaciones[fieldName]) {
            validaciones[fieldName]();
        }
    });

    $formEdit.find('#telefono_numero-Edit').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 7) validaciones.telefono();
    });

    $formEdit.find('#prefijo_telefono-Edit').on('change', validaciones.telefono);

    $formEdit.find('#fecha_nacimiento-Edit').on('change input', validaciones.fecha_nacimiento);
    $formEdit.find('input[name="genero"]').on('change', validaciones.genero);
    $formEdit.find('#correo-Edit').on('input', validaciones.correo);
    $formEdit.find('#grado_institucion-Edit').on('change', validaciones.grado_institucion);
    $formEdit.find('#id_discapacidad-Edit').on('change', validaciones.discapacidad);

    // Cargar datos en el modal
    $(document).on('click', '.button.edit', function (e) {
        e.preventDefault();
        const idParticipante = $(this).data('id');
        console.log('ID del participante clickeado:', idParticipante);

        // Resetear el formulario primero
        $formEdit[0].reset();
        $formEdit.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $formEdit.find('.error-feedback').hide();

        // Hacer la petición con GET (como está configurado en tu PHP)
        fetch(`../../app/controlador/obtenerParticipanteEditar.php?id=${idParticipante}`)
            .then(response => {
                console.log('Respuesta HTTP:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos JSON recibidos:', data);

                if (data.success) {
                    const participante = data.data;

                    // Llenar formulario con los campos correctos
                    $('#idParticipante').val(participante.id_participante);
                    $('#cedula-Edit').val(participante.cedula);
                    const nacVal = participante.nacionalidad || 'V';
                    $('#nacionalidad-Edit').val(nacVal).trigger('change');

                    // Separar prefijo y número
                    if (participante.telefono) {
                        let telStr = String(participante.telefono);
                        if (telStr.length === 10) telStr = '0' + telStr;
                        
                        if (telStr.length >= 11) {
                            const pref = telStr.substring(0, 4);
                            const num = telStr.substring(4);
                            $('#prefijo_telefono-Edit').val(pref);
                            $('#telefono_numero-Edit').val(num);
                            $('#telefono-Edit').val(telStr);
                        } else {
                            $('#telefono-Edit').val(telStr);
                        }
                    } else {
                        $('#prefijo_telefono-Edit').val('');
                        $('#telefono_numero-Edit').val('');
                        $('#telefono-Edit').val('');
                    }

                    $('#primer_nombre-Edit').val(participante.primer_nombre);
                    $('#segundo_nombre-Edit').val(participante.segundo_nombre || '');
                    $('#primer_apellido-Edit').val(participante.primer_apellido);
                    $('#segundo_apellido-Edit').val(participante.segundo_apellido || '');
                    $('#correo-Edit').val(participante.correo);
                    $('#fecha_nacimiento-Edit').val(participante.fecha_nacimiento);
                    $('#grado_institucion-Edit').val(participante.grado_institucion);
                    $('#id_discapacidad-Edit').val(participante.id_discapacidad || '');

                    // Seleccionar el género correcto
                    if (participante.genero === 'Masculino') {
                        $('#genero_masculino-Edit').prop('checked', true);
                        $('#genero_femenino-Edit').prop('checked', false);
                    } else if (participante.genero === 'Femenino') {
                        $('#genero_masculino-Edit').prop('checked', false);
                        $('#genero_femenino-Edit').prop('checked', true);
                    }

                    // Ejecutar validaciones iniciales para mostrar estado
                    setTimeout(() => {
                        validaciones.primer_nombre();
                        validaciones.primer_apellido();
                        validaciones.cedula();
                        validaciones.telefono();
                        validaciones.fecha_nacimiento();
                        validaciones.genero();
                        validaciones.correo();
                        validaciones.grado_institucion();
                        validaciones.discapacidad();
                    }, 100);

                    $('#modal-editParticipante').modal('show');

                } else if (data.error) {
                    console.error('Error del servidor:', data.error);
                    toastr.error('Error al cargar datos: ' + data.error);
                } else {
                    console.error('Estructura de respuesta inesperada:', data);
                    toastr.error('Error inesperado al cargar datos');
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                toastr.error('Error al cargar datos: ' + error.message);
            });
    });

    // Envío del formulario
    $formEdit.on('submit', async function (e) {
        e.preventDefault();
        console.log('Enviando formulario de edición...');

        // Ejecutar validaciones
        const resultados = await Promise.all([
            validaciones.cedula(),
            validaciones.primer_nombre(),
            validaciones.segundo_nombre(),
            validaciones.primer_apellido(),
            validaciones.segundo_apellido(),
            validaciones.telefono(),
            validaciones.fecha_nacimiento(),
            validaciones.genero(),
            validaciones.correo(),
            validaciones.grado_institucion(),
            validaciones.discapacidad()
        ]);

        if (resultados.every(r => r)) {
            // Crear FormData con todos los campos necesarios
            const formData = new FormData();

            // Agregar todos los campos del formulario
            formData.append('id_participante', $('#idParticipante').val());
            formData.append('primer_nombre', $('#primer_nombre-Edit').val());
            formData.append('segundo_nombre', $('#segundo_nombre-Edit').val() || '');
            formData.append('primer_apellido', $('#primer_apellido-Edit').val());
            formData.append('segundo_apellido', $('#segundo_apellido-Edit').val() || '');
            formData.append('cedula', $('#cedula-Edit').val());
            formData.append('telefono', $('#telefono-Edit').val() || '');
            formData.append('correo', $('#correo-Edit').val());
            formData.append('fecha_nacimiento', $('#fecha_nacimiento-Edit').val());
            formData.append('grado_institucion', $('#grado_institucion-Edit').val());
            formData.append('genero', $('input[name="genero"]:checked').val());
            formData.append('id_discapacidad', $('#id_discapacidad-Edit').val() || '1'); // Valor por defecto 1 = Ninguna

            // Mostrar datos que se enviarán (para debug)
            console.log('Datos a enviar:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            try {
                const response = await fetch('../../app/controlador/editarParticipante.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                console.log('Respuesta del servidor:', data);

                if (data.success) {
                    $('#modal-editParticipante').modal('hide');
                    if (typeof aplicarFiltros === 'function') {
                        aplicarFiltros();
                    } else if (typeof cargarRegistros === 'function') {
                        cargarRegistros(1, 8);
                    }
                    toastr.success(data.message || "Participante actualizado correctamente");
                } else {
                    toastr.error(data.message || "Error en la actualización");
                }
            } catch (error) {
                console.error('Error en fetch:', error);
                toastr.error("Error de conexión: " + error.message);
            }
        } else {
            toastr.warning("Complete los campos requeridos correctamente");
        }
    });

    // Limpiar formulario al cerrar
    $('#modal-editParticipante').on('hidden.bs.modal', function () {
        $formEdit[0].reset();
        $formEdit.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $formEdit.find('.error-feedback').hide();
    });
});
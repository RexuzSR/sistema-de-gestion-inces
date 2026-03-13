// Función para calcular la edad
function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();

    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    return edad;
}

// Función para validar correo
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Función para mostrar errores
function mostrarError(campoId, mensaje) {
    const $campo = $(`#${campoId}`);
    const $error = $(`#error-${campoId}`);

    $campo.addClass('is-invalid').removeClass('is-valid');
    if ($error.length) {
        $error.text(mensaje).show();
    }
}

// Función para mostrar campo válido
function mostrarValido(campoId) {
    const $campo = $(`#${campoId}`);
    const $error = $(`#error-${campoId}`);

    $campo.addClass('is-valid').removeClass('is-invalid');
    if ($error.length) {
        $error.text('').hide();
    }
}

// Validaciones para el formulario de edición
const validacionesEditDocente = {
    cedula: async () => {
        const valor = $('#cedula-Edit').val().trim();
        const idDocente = $('#idDocente').val();

        if (!valor) {
            mostrarError('cedula-Edit', 'La cédula es obligatoria');
            return false;
        }

        if (!/^\d{7,9}$/.test(valor)) {
            const msg = valor.length < 7 ? 'Mínimo 7 caracteres' : (valor.length > 9 ? 'Máximo 9 caracteres' : 'Cédula inválida');
            mostrarError('cedula-Edit', msg);
            return false;
        }

        try {
            const response = await $.ajax({
                url: '../../app/controlador/Docente/verificarCedulaDocenteEditar.php',
                method: 'GET',
                data: { cedula: valor, idDocente: idDocente },
                dataType: 'json'
            });

            if (response.existe) {
                mostrarError('cedula-Edit', 'Esta cédula ya está registrada por otro docente');
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

    primer_nombre: () => {
        const valor = $('#primer_nombre-Edit').val().trim();
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,30}$/;

        if (!valor) {
            mostrarError('primer_nombre-Edit', 'El primer nombre es obligatorio');
            return false;
        }

        if (!regex.test(valor)) {
            mostrarError('primer_nombre-Edit', 'Solo letras (2-30 caracteres)');
            return false;
        }

        mostrarValido('primer_nombre-Edit');
        return true;
    },

    segundo_nombre: () => {
        const valor = $('#segundo_nombre-Edit').val().trim();
        if (!valor) {
            mostrarValido('segundo_nombre-Edit');
            return true;
        }

        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,30}$/;
        if (!regex.test(valor)) {
            mostrarError('segundo_nombre-Edit', 'Solo letras (2-30 caracteres)');
            return false;
        }

        mostrarValido('segundo_nombre-Edit');
        return true;
    },

    primer_apellido: () => {
        const valor = $('#primer_apellido-Edit').val().trim();
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,30}$/;

        if (!valor) {
            mostrarError('primer_apellido-Edit', 'El primer apellido es obligatorio');
            return false;
        }

        if (!regex.test(valor)) {
            mostrarError('primer_apellido-Edit', 'Solo letras (2-30 caracteres)');
            return false;
        }

        mostrarValido('primer_apellido-Edit');
        return true;
    },

    segundo_apellido: () => {
        const valor = $('#segundo_apellido-Edit').val().trim();
        if (!valor) {
            mostrarValido('segundo_apellido-Edit');
            return true;
        }

        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,30}$/;
        if (!regex.test(valor)) {
            mostrarError('segundo_apellido-Edit', 'Solo letras (2-30 caracteres)');
            return false;
        }

        mostrarValido('segundo_apellido-Edit');
        return true;
    },

    telefono: () => {
        const prefijo = $('#prefijo_telefono-Edit').val();
        const numero = $('#telefono_numero-Edit').val().trim();
        const completo = prefijo + numero;

        if (!numero) {
            mostrarValido('telefono_numero-Edit');
            $('#telefono-Edit').val('');
            return true;
        }

        const regex = /^(0412|0414|0416|0424|0426|0293|0212)\d{7}$/;
        if (!regex.test(completo)) {
            mostrarError('telefono_numero-Edit', 'Formato inválido (7 dígitos)');
            return false;
        }

        mostrarValido('telefono_numero-Edit');
        $('#telefono-Edit').val(completo);
        return true;
    },

    correo: () => {
        const valor = $('#correo-Edit').val().trim();

        if (!valor) {
            mostrarError('correo-Edit', 'El correo es obligatorio');
            return false;
        }

        if (!validarEmail(valor)) {
            mostrarError('correo-Edit', 'Correo electrónico inválido');
            return false;
        }

        mostrarValido('correo-Edit');
        return true;
    },

    fechaNacimiento: () => {
        const fechaStr = $('#fechaNacimiento-Edit').val();

        if (!fechaStr) {
            mostrarError('fechaNacimiento-Edit', 'La fecha de nacimiento es obligatoria');
            return false;
        }

        const fecha = new Date(fechaStr);
        const hoy = new Date();
        const minFecha = new Date();
        minFecha.setFullYear(hoy.getFullYear() - 70);
        const maxFecha = new Date();
        maxFecha.setFullYear(hoy.getFullYear() - 15);

        if (fecha > hoy) {
            mostrarError('fechaNacimiento-Edit', 'La fecha no puede ser futura');
            return false;
        }

        if (fecha < minFecha) {
            mostrarError('fechaNacimiento-Edit', 'La edad máxima permitida es 70 años');
            return false;
        }

        if (fecha > maxFecha) {
            mostrarError('fechaNacimiento-Edit', 'La edad mínima permitida es 15 años');
            return false;
        }

        // Calcular y mostrar edad
        const edad = calcularEdad(fechaStr);
        $('#edad-Edit').val(edad);

        mostrarValido('fechaNacimiento-Edit');
        return true;
    },

    genero: () => {
        const valor = $('input[name="genero"]:checked').val();

        if (!valor) {
            $('#error-genero').text('Seleccione un género').show();
            return false;
        }

        $('#error-genero').text('').hide();
        return true;
    },

    tipo_docente: () => {
        const valor = $('#tipo_docente-Edit').val();

        if (!valor) {
            mostrarError('tipo_docente-Edit', 'Seleccione un tipo de docente');
            return false;
        }

        mostrarValido('tipo_docente-Edit');
        return true;
    },

    estado_docente: () => {
        const valor = $('#estado_docente-Edit').val();

        if (!valor) {
            mostrarError('estado_docente-Edit', 'Seleccione un estado');
            return false;
        }

        mostrarValido('estado_docente-Edit');
        return true;
    }
};

// Evento para abrir modal de edición
$(document).on('click', '.button.edit', function () {
    const idDocente = $(this).data('id');

    // Limpiar validaciones anteriores
    $('#form-editDocente .form-control').removeClass('is-valid is-invalid');
    $('#form-editDocente .form-check-input').removeClass('is-invalid');
    $('.invalid-feedback').text('').hide();

    // Mostrar loading
    $('#btn-editDocente-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...');

    $.ajax({
        url: '../../app/controlador/Docente/obtenerDocenteEditar.php',
        method: 'GET',
        data: { id: idDocente },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                toastr.error(data.error);
                $('#modal-editDocente').modal('hide');
                return;
            }

            // Llenar campos del formulario
            $('#idDocente').val(data.id_docente);
            $('#cedula-Edit').val(data.cedula);

            // Tratar teléfono
            if (data.telefono && data.telefono.length >= 10) {
                const pref = data.telefono.substring(0, 4);
                const num = data.telefono.substring(4);
                $('#prefijo_telefono-Edit').val(pref);
                $('#telefono_numero-Edit').val(num);
                $('#telefono-Edit').val(data.telefono);
            } else {
                $('#telefono_numero-Edit').val('');
                $('#telefono-Edit').val('');
            }

            $('#primer_nombre-Edit').val(data.primer_nombre);
            $('#segundo_nombre-Edit').val(data.segundo_nombre || '');
            $('#primer_apellido-Edit').val(data.primer_apellido);
            $('#segundo_apellido-Edit').val(data.segundo_apellido || '');
            $('#correo-Edit').val(data.correo);
            $('#fechaNacimiento-Edit').val(data.fecha_nacimiento);

            // Configurar género
            $(`input[name="genero"][value="${data.genero}"]`).prop('checked', true);

            // Configurar selects
            $('#tipo_docente-Edit').val(data.tipo_docente);
            $('#estado_docente-Edit').val(data.estado_docente);

            // Calcular y mostrar edad
            if (data.fecha_nacimiento) {
                const edad = calcularEdad(data.fecha_nacimiento);
                $('#edad-Edit').val(edad);
            }

            $('#modal-editDocente').modal('show');

            // Disparar validaciones visuales iniciales después de un ciclo para asegurar renderizado
            setTimeout(() => {
                validacionesEditDocente.primer_nombre();
                validacionesEditDocente.segundo_nombre();
                validacionesEditDocente.primer_apellido();
                validacionesEditDocente.segundo_apellido();
                validacionesEditDocente.telefono();
                validacionesEditDocente.correo();
                validacionesEditDocente.fechaNacimiento();
                validacionesEditDocente.genero();
                validacionesEditDocente.tipo_docente();
                validacionesEditDocente.estado_docente();
                // Cedula no la validamos visualmente de entrada para evitar salto de red innecesario
                // pero si el campo está lleno, podemos mostrarlo como válido
                if ($('#cedula-Edit').val()) mostrarValido('cedula-Edit');
            }, 500);
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener datos:", error);
            toastr.error("Error al obtener los datos del docente");
        },
        complete: function () {
            $('#btn-editDocente-submit').prop('disabled', false).html('Actualizar');
        }
    });
});

// Validación en tiempo real
// Restringir entrada a solo letras para nombres y apellidos
$('#primer_nombre-Edit, #segundo_nombre-Edit, #primer_apellido-Edit, #segundo_apellido-Edit').on('input', function () {
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
    if (this.value.length > 30) this.value = this.value.slice(0, 30);
    // Disparar validación visual inmediata
    const fieldId = this.id;
    const fieldName = fieldId.replace('-Edit', '');
    if (validacionesEditDocente[fieldName]) {
        validacionesEditDocente[fieldName]();
    }
});

// Evento en tiempo real para la cédula en edición
$('#cedula-Edit').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 9) this.value = this.value.slice(0, 9);
    validacionesEditDocente.cedula();
});

$('#prefijo_telefono-Edit').on('change', validacionesEditDocente.telefono);
$('#telefono_numero-Edit').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    validacionesEditDocente.telefono();
});
$('#correo-Edit').on('input', validacionesEditDocente.correo);
$('#fechaNacimiento-Edit').on('change', validacionesEditDocente.fechaNacimiento);
$('#tipo_docente-Edit').on('change', validacionesEditDocente.tipo_docente);
$('#estado_docente-Edit').on('change', validacionesEditDocente.estado_docente);
$('input[name="genero"]').on('change', validacionesEditDocente.genero);

// Envío del formulario
$('#form-editDocente').on('submit', async function (e) {
    e.preventDefault();

    // Ejecutar todas las validaciones
    const validaciones = [
        await validacionesEditDocente.cedula(),
        validacionesEditDocente.primer_nombre(),
        validacionesEditDocente.segundo_nombre(),
        validacionesEditDocente.primer_apellido(),
        validacionesEditDocente.segundo_apellido(),
        validacionesEditDocente.telefono(),
        validacionesEditDocente.correo(),
        validacionesEditDocente.fechaNacimiento(),
        validacionesEditDocente.genero(),
        validacionesEditDocente.tipo_docente(),
        validacionesEditDocente.estado_docente()
    ];

    const todosValidos = validaciones.every(v => v === true);

    if (todosValidos) {
        // Mostrar loading en el botón
        $('#btn-editDocente-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...');

        const formData = $(this).serialize();

        $.ajax({
            url: '../../app/controlador/Docente/editarDocente.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#modal-editDocente').modal('hide');
                    toastr.success(response.message || "¡El docente se actualizó correctamente!");

                    // Recargar la tabla
                    if (typeof cargarRegistros === 'function') {
                        const filters = {
                            sexo: $('#sexo-filter').val(),
                            edad: $('#edad-filter').val(),
                            estado: $('#estado-filter').val()
                        };
                        cargarRegistros(1, 8, filters);
                    } else if (typeof aplicarFiltros === 'function') {
                        aplicarFiltros();
                    } else {
                        // Recargar la página después de 1 segundo
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    toastr.error(response.message || "Error al actualizar el docente");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                toastr.error("Error de conexión con el servidor");
            },
            complete: function () {
                $('#btn-editDocente-submit').prop('disabled', false).html('Actualizar');
            }
        });
    } else {
        toastr.warning("Complete correctamente todos los campos obligatorios");
    }
});

// Limpiar validaciones al cerrar modal
$('#modal-editDocente').on('hidden.bs.modal', function () {
    $('#form-editDocente .form-control').removeClass('is-valid is-invalid');
    $('#form-editDocente .form-check-input').removeClass('is-invalid');
    $('.invalid-feedback').text('').hide();
    $('#form-editDocente')[0].reset();
});
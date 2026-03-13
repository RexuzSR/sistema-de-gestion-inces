

$(document).ready(function () {
    const $form = $('#form_editUsuario');

    // Declara las variables timeout
    let timeoutUsuario, timeoutCorreo;
    let editingUserId = null;

    // ==================== FUNCIONES DE VALIDACIÓN ====================
    function showError(fieldId, message) {
        $form.find(`#${fieldId}`).removeClass('is-valid').addClass('is-invalid');
        $form.find(`#error-${fieldId.split('-')[0]}-edit`).text(message).show();
    }

    function showValid(fieldId) {
        $form.find(`#${fieldId}`).removeClass('is-invalid').addClass('is-valid');
        $form.find(`#error-${fieldId.split('-')[0]}-edit`).text('').hide();
    }

    // ==================== VALIDACIONES EN TIEMPO REAL ====================
    // Validación de primer nombre (requerido)
    $form.find('#primer_nombre-Edit').on('input', function () {
        const valor = $(this).val().trim();
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
        regex.test(valor) ? showValid('primer_nombre-Edit') : showError('primer_nombre-Edit', 'Solo letras y espacios válidos');
    });

    // Validación de segundo nombre (opcional)
    $form.find('#segundo_nombre-Edit').on('input', function () {
        const valor = $(this).val().trim();
        if (valor === '') { showValid('segundo_nombre-Edit'); return; }
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
        regex.test(valor) ? showValid('segundo_nombre-Edit') : showError('segundo_nombre-Edit', 'Solo letras y espacios válidos');
    });

    // Validación de primer apellido (requerido)
    $form.find('#primer_apellido-Edit').on('input', function () {
        const valor = $(this).val().trim();
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
        regex.test(valor) ? showValid('primer_apellido-Edit') : showError('primer_apellido-Edit', 'Solo letras y espacios válidos');
    });

    // Validación de segundo apellido (opcional)
    $form.find('#segundo_apellido-Edit').on('input', function () {
        const valor = $(this).val().trim();
        if (valor === '') { showValid('segundo_apellido-Edit'); return; }
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
        regex.test(valor) ? showValid('segundo_apellido-Edit') : showError('segundo_apellido-Edit', 'Solo letras y espacios válidos');
    });

    // Validación de nombre de usuario (4-20 caracteres alfanuméricos)
    $form.find('#nombre_usuario-Edit').on('input', function () {
        const username = $(this).val().trim();
        clearTimeout(timeoutUsuario); // Limpiar el timeout previo

        if (!/^[a-zA-Z0-9_]{4,20}$/.test(username)) {
            showError('nombre_usuario-Edit', '4-20 caracteres alfanuméricos');
            return;
        }

        timeoutUsuario = setTimeout(() => {
            if (!editingUserId) return;

            fetch(`../../app/controlador/Usuario/verificarNombreUsuario.php?nombre_usuario=${encodeURIComponent(username)}&id_usuario=${editingUserId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.existe) {
                        showError('nombre_usuario-Edit', 'Usuario ya registrado');
                    } else {
                        showValid('nombre_usuario-Edit');
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Error en la verificación del usuario');
                });
        }, 500);
    });

    // Validación de correo (formato de correo)
    $form.find('#correo-Edit').on('input', function () {
        const correo = $(this).val().trim();
        clearTimeout(timeoutCorreo); // Limpiar el timeout previo

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            showError('correo-Edit', 'Formato de correo inválido');
            return;
        }

        timeoutCorreo = setTimeout(() => {
            if (!editingUserId) return;

            fetch(`../../app/controlador/Usuario/verificarCorreo.php?correo=${encodeURIComponent(correo)}&id_usuario=${editingUserId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.existe) {
                        showError('correo-Edit', 'Correo ya registrado');
                    } else {
                        showValid('correo-Edit');
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Error en la verificación del correo');
                });
        }, 500);
    });

    // ==================== MANEJO DEL MODAL ====================
    $('#modal-editUsuario')
        .on('show.bs.modal', function () {
            $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
            $form.find('.invalid-feedback').hide().text('');
        })
        .on('hidden.bs.modal', function () {
            $form[0].reset();
            editingUserId = null;
            clearTimeout(timeoutUsuario); // Limpiar el timeout
            clearTimeout(timeoutCorreo);  // Limpiar el timeout
        });

    // ==================== CARGA DE DATOS AL EDITAR ====================
    $(document).on('click', '.button.edit', function () {
        editingUserId = $(this).data('id');

        fetch(`../../app/controlador/Usuario/obtenerUsuarioEditar.php?id=${editingUserId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) throw new Error(data.error);

                // Rellenar nombres y apellidos separados (si no existen, usar los combinados)
                $form.find('#primer_nombre-Edit').val(data.primer_nombre ?? data.nombre ?? '');
                $form.find('#segundo_nombre-Edit').val(data.segundo_nombre ?? '');
                $form.find('#primer_apellido-Edit').val(data.primer_apellido ?? data.apellido ?? '');
                $form.find('#segundo_apellido-Edit').val(data.segundo_apellido ?? '');
                $form.find('#nombre_usuario-Edit').val(data.nombre_usuario);
                $form.find('#rol-Edit').val(data.rol);
                $form.find('#correo-Edit').val(data.correo);
                $form.find('#estado_usuario-Edit').val(data.estado_usuario);
                $form.find('#avatarUsuario').attr('src', `../../${data.imagen}?t=${Date.now()}`);
                $form.find('#imagen_actual-Edit').val(data.imagen);
                $form.find('#idUsuario').val(editingUserId);

                // ==================== VALIDACIÓN: PREVENIR CAMBIO DE ROL Y ESTADO PROPIO ====================
                const currentLoggedUserId = Number($('#current-user-id').val());
                const editingId = Number(editingUserId);

                const $rolField = $form.find('#rol-Edit');
                const $estadoField = $form.find('#estado_usuario-Edit');

                // Remover mensajes previos
                $form.find('.profile-info-message').remove();

                if (editingId === currentLoggedUserId) {
                    // Usuario está editando su propio perfil - DESHABILITAR campos críticos
                    $rolField.prop('disabled', true).css({ 'opacity': '0.7', 'cursor': 'not-allowed' });
                    $estadoField.prop('disabled', true).css({ 'opacity': '0.7', 'cursor': 'not-allowed' });

                    // Agregar mensaje informativo
                    const infoMessage = `
                        <small class="d-block mt-1 profile-info-message" style="color: #38bdf8; font-weight: 500;">
                            <i class="bi bi-info-circle-fill me-1"></i> No puedes cambiar tu propio rol o estado
                        </small>
                    `;
                    $rolField.after(infoMessage);
                } else {
                    // HABILITAR campos para otros perfiles
                    $rolField.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'default' });
                    $estadoField.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'default' });
                }

                $('#modal-editUsuario').modal('show');

                // Disparar validaciones iniciales después de que el modal sea visible
                setTimeout(() => {
                    $form.find('#primer_nombre-Edit, #segundo_nombre-Edit, #primer_apellido-Edit, #segundo_apellido-Edit, #nombre_usuario-Edit, #correo-Edit').trigger('input');
                }, 500);
            })
            .catch(error => {
                console.error(error);
                toastr.error('Error al cargar datos del usuario');
            });
    });

    // ==================== ENVÍO DEL FORMULARIO ====================
    $form.on('submit', function (e) {
        e.preventDefault();

        // Habilitar temporalmente los campos para que se envíen en el formulario
        const $rolField = $form.find('#rol-Edit');
        const $estadoField = $form.find('#estado_usuario-Edit');
        const wasRolDisabled = $rolField.prop('disabled');
        const wasEstadoDisabled = $estadoField.prop('disabled');

        if (wasRolDisabled) $rolField.prop('disabled', false);
        if (wasEstadoDisabled) $estadoField.prop('disabled', false);

        $form.find('input, select').trigger('input').trigger('change');

        if ($form.find('.is-invalid').length > 0) {
            toastr.warning("Complete correctamente los campos marcados");
            return;
        }

        const formData = new FormData(this);

        fetch('../../app/controlador/Usuario/editarUsuario.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    $('#modal-editUsuario').modal('hide');
                    toastr.success("¡Usuario actualizado correctamente!");
                    cargarTabla();
                } else {
                    throw new Error(data.message || "Error en el servidor");
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error(error.message);
            });
    });
});

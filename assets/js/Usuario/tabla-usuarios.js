$(document).ready(function () {

    // Variables para debounce
    let timeoutUsername, timeoutCorreo;

    window.cargarTabla = function () {
        $.ajax({
            url: '../../app/controlador/Usuario/obtenerUsuarios.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {

                let rows = '';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(participante => {

                        // Clases de Bootstrap para rol con ancho mínimo de 100px
                        let rolBadgeClass = participante.rol === 'Administrador' ? 'badge bg-primary" style="min-width: 100px;' :
                            participante.rol === 'Apoyo' ? 'badge bg-secondary" style="min-width: 100px;' :
                                'badge bg-secondary" style="min-width: 100px;';

                        // Clases de Bootstrap para estado con ancho mínimo de 70px
                        let estadoBadgeClass = participante.estado_usuario === 'Activo' ? 'badge bg-success" style="min-width: 70px;' :
                            participante.estado_usuario === 'Inactivo' ? 'badge bg-danger" style="min-width: 70px;' :
                                'badge bg-secondary" style="min-width: 100px;';

                        // Verificar si el usuario de la fila es el mismo que el logueado
                        const isSelf = Number(participante.id_usuario) === Number(currentUserId);

                        rows += `
                            <tr>
                                <td>${participante.id_usuario}</td>
                                <td>${participante.nombre}</td>
                                <td>${participante.apellido}</td>
                                <td>${participante.nombre_usuario}</td>
                                <td>${participante.correo}</td>
                                <td><span class="${rolBadgeClass}">${participante.rol}</span></td>
                                <td><span class="${estadoBadgeClass}">${participante.estado_usuario}</span></td>
                                <td>
                                <img src="http://localhost/Sistema%20de%20Gestion%20Inces/${participante.imagen ? participante.imagen : 'assets/img/avatar/default-user.png'}" alt="Avatar" class="rounded-circle" width="50" height="50" style="object-fit:cover;">    
                                </td>
                                <td>
                                    <!-- Botones de acción -->
                                    <a href="#"><button class='button view' data-id="${participante.id_usuario}" data-bs-toggle='modal' data-bs-target='#modal-viewUsuario'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                    <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                    </svg></button></a>


                                    <a href="#"><button class='button edit' data-id="${participante.id_usuario}" data-bs-toggle='modal' data-bs-target='#modal-editUsuario'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                    </svg></button></a>

                                    ${(!isSelf && !(participante.rol.toLowerCase() === 'administrador' && typeof currentUserRol !== 'undefined' && currentUserRol.toLowerCase() === 'administrador')) ? `
                                    <a href="#"><button class='button delete' data-id="${participante.id_usuario}" data-bs-toggle='modal' data-bs-target='#modal-deleteUsuario'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                    <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                    <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                    </svg></button></a>
                                    ` : ''}
                                </td>
                            </tr>
                        `;

                    });
                } else if (data.error) {
                    console.error("❌ Error del servidor:", data.message);
                    rows = `<tr><td colspan="8" class="text-center">Error: ${data.message}</td></tr>`;
                } else {
                    console.warn("⚠️ No hay usuarios registrados.");
                    rows = '<tr><td colspan="8" class="text-center">No hay Usuarios registrados</td></tr>';
                }

                $('#tabla-usuarios tbody').html(rows);
                // Hacer tabla responsive después de cargar los datos
                makeTableResponsive();
            },
            error: function (xhr, status, error) {
                console.error("❌ Error en la solicitud AJAX:", error);
                $('#tabla-usuarios tbody').html('<tr><td colspan="8" class="text-center">Error al cargar los datos</td></tr>');
            }
        });
    }

    // Funciones de validación
    function showError(fieldId, message) {
        $(`#${fieldId}`).removeClass('is-valid').addClass('is-invalid');
        $(`#error-${fieldId}`).text(message).show();
    }

    function showValid(fieldId) {
        $(`#${fieldId}`).removeClass('is-invalid').addClass('is-valid');
        $(`#error-${fieldId}`).text('').hide();
    }

    // Hacer tabla responsive
    function makeTableResponsive() {
        if ($(window).width() <= 768) {
            $('#tabla-usuarios thead th').each(function (i) {
                var headerText = $(this).text();
                $('#tabla-usuarios tbody td:nth-child(' + (i + 1) + ')').attr('data-label', headerText);
            });
        }
    }

    // Configurar validaciones del formulario
    function setupFormValidation() {
        const $form = $('#form-usuario');

        // Validación de primer nombre (requerido)
        $form.find('#primer_nombre').on('input', function () {
            const valor = $(this).val().trim();
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(valor) ? showValid('primer_nombre') : showError('primer_nombre', 'Solo letras y espacios válidos');
        });

        // Validación de segundo nombre (opcional)
        $form.find('#segundo_nombre').on('input', function () {
            const valor = $(this).val().trim();
            if (valor === '') { showValid('segundo_nombre'); return; }
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(valor) ? showValid('segundo_nombre') : showError('segundo_nombre', 'Solo letras y espacios válidos');
        });

        // Validación de primer apellido (requerido)
        $form.find('#primer_apellido').on('input', function () {
            const valor = $(this).val().trim();
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(valor) ? showValid('primer_apellido') : showError('primer_apellido', 'Solo letras y espacios válidos');
        });

        // Validación de segundo apellido (opcional)
        $form.find('#segundo_apellido').on('input', function () {
            const valor = $(this).val().trim();
            if (valor === '') { showValid('segundo_apellido'); return; }
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(valor) ? showValid('segundo_apellido') : showError('segundo_apellido', 'Solo letras y espacios válidos');
        });

        // Validación de nombre de usuario
        $form.find('#nombre_usuario').on('input', function () {
            const username = $(this).val().trim();
            clearTimeout(timeoutUsername);

            if (!/^[a-zA-Z0-9_]{4,20}$/.test(username)) {
                showError('nombre_usuario', '4-20 caracteres (letras, números o _)');
                return;
            }

            timeoutUsername = setTimeout(() => {
                $.ajax({
                    url: '../../app/controlador/Usuario/verificarUsuario.php',
                    method: 'GET',
                    data: { nombre_usuario: username },
                    dataType: 'json',
                    success: function (response) {
                        response.existsUsername
                            ? showError('nombre_usuario', 'Usuario no disponible')
                            : showValid('nombre_usuario');
                    },
                    error: function () {
                        showError('nombre_usuario', 'Error de verificación');
                    }
                });
            }, 300);
        });

        // Validación de contraseña
        $form.find('#password').on('input', function () {
            const pass = $(this).val();
            pass.length >= 8
                ? showValid('password')
                : showError('password', 'La contraseña debe tener al menos 8 caracteres');
        });

        // Validación de correo
        $form.find('#correo').on('input', function () {
            const correo = $(this).val().trim();
            clearTimeout(timeoutCorreo);

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                showError('correo', 'Formato de correo inválido');
                return;
            }

            timeoutCorreo = setTimeout(() => {
                $.ajax({
                    url: '../../app/controlador/Usuario/verificarUsuario.php',
                    method: 'GET',
                    data: { correo: correo },
                    dataType: 'json',
                    success: function (response) {
                        response.existsCorreo
                            ? showError('correo', 'Correo ya registrado')
                            : showValid('correo');
                    },
                    error: function () {
                        showError('correo', 'Error de verificación');
                    }
                });
            }, 300);
        });

        // Validación de rol
        $form.find('#rol').on('change', function () {
            $(this).val()
                ? showValid('rol')
                : showError('rol', 'Seleccione un rol');
        });

        // Validación de estado
        $form.find('#estado_usuario').on('change', function () {
            $(this).val()
                ? showValid('estado_usuario')
                : showError('estado_usuario', 'Seleccione un estado');
        });

        // Validación de imagen
        $form.find('#imagen').on('change', function () {
            const file = this.files[0];

            if (!file) {
                showValid('imagen');
                return;
            }

            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showError('imagen', 'Solo JPG, PNG o GIF');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showError('imagen', 'Tamaño máximo 2MB');
                return;
            }

            showValid('imagen');
        });

        // Reiniciar el modal
        $('#modal-addUsuario').on('hidden.bs.modal', function () {
            $form[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.error-feedback').hide();
            clearTimeout(timeoutUsername);
            clearTimeout(timeoutCorreo);
            $('#master_password_add').val(''); // Limpiar contraseña maestra
        });

        // Envío del formulario
        $form.on('submit', function (e) {
            e.preventDefault();

            $form.find('input:not(#master_password_add), select').trigger('input').trigger('change');

            // Validar contraseña maestra
            const masterPwd = $('#master_password_add').val();
            if (!masterPwd || masterPwd.trim() === '') {
                toastr.warning("🔒 Debe ingresar la contraseña maestra para registrar.");
                $('#master_password_add').focus();
                return;
            }

            if ($form.find('.is-invalid').length === 0) {
                const formData = new FormData(this);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#modal-addUsuario').modal('hide');
                            toastr.success("¡El Usuario se registró correctamente!");
                            cargarTabla();
                        } else {
                            toastr.error(response.message || "Error en el servidor");
                        }
                    },
                    error: function (xhr) {
                        toastr.error("Error de conexión: " + xhr.statusText);
                    }
                });
            } else {
                toastr.warning("Complete correctamente los campos marcados");
            }
        });
    }

    // Inicializar todo cuando el documento esté listo
    function init() {
        // Cargar la tabla al iniciar
        cargarTabla();

        // Configurar validaciones del formulario
        setupFormValidation();

        // Configurar tabla responsive al redimensionar
        $(window).on('resize', makeTableResponsive);

        // Configurar búsqueda si existe
        if ($('#search-input').length) {
            $('#search-input').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#tabla-usuarios tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        }

        // Configurar botones de acción si es necesario
        $(document).on('click', '.button.view', function () {
            var userId = $(this).data('id');
            console.log('Ver usuario:', userId);
            // Aquí puedes agregar la lógica para ver detalles del usuario
        });

        $(document).on('click', '.button.edit', function () {
            var userId = $(this).data('id');
            console.log('Editar usuario:', userId);
            // Aquí puedes agregar la lógica para editar usuario
        });

        $(document).on('click', '.button.delete', function () {
            var userId = $(this).data('id');
            console.log('Eliminar usuario:', userId);
            // Aquí puedes agregar la lógica para eliminar usuario
        });
    }

    // Inicializar la aplicación
    init();

});
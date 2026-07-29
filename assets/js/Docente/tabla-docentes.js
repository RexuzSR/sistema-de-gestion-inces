$(document).ready(function () {
    // Función para cargar la tabla de participantes
    function cargarTabla() {

        $.ajax({
            url: '../../app/controlador/Docente/obtenerDocentes.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                let rows = '';
                let docentes = [];

                if (response.data && Array.isArray(response.data)) {
                    docentes = response.data;
                } else if (Array.isArray(response)) {
                    docentes = response;
                }

                if (docentes.length > 0) {
                    docentes.forEach(participante => {
                        const estado = participante.estado === "En formación"
                            ? '<span class="badge bg-success">En formación</span>'
                            : participante.estado === "Disponible"
                                ? '<span class="badge bg-warning text-black">Disponible</span>'
                                : participante.estado === "Asignado"
                                    ? '<span class="badge bg-primary">Asignado</span>'
                                    : participante.estado === "Inactivo"
                                        ? '<span class="badge bg-danger">Inactivo</span>'
                                        : participante.estado;


                        const docCedNum = parseInt(participante.cedula);
                        const docPref = (!isNaN(docCedNum) && docCedNum >= 80000000) ? 'E-' : 'V-';
                        const docCedStr = participante.cedula ? (docPref + participante.cedula) : '';

                        rows += `
                            <tr>
                                <td>${participante.id_docente}</td>
                                <td>${docCedStr}</td>
                                <td>${participante.nombre}</td>
                                <td>${participante.apellido}</td>
                                <td>${participante.genero}</td>
                                <td>${participante.edad}</td>
                                <td>${participante.correo}</td>
                                <td>${estado}</td>
                                <td>
                                    <!-- Botones de acción -->
                                    <a href="#"><button class='button view' title="Ver Docente" data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-viewDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                    <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                    </svg></button></a>

                                    <a href="#"><button class='button curso' title="Asignar Curso" data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-AsignarCursoDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-journal-bookmark-fill' viewBox='0 0 16 16'>
                                    <path fill-rule='evenodd' d='M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z'/>
                                    <path d='M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2'/>
                                    <path d='M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z'/>
                                    </svg></button></a>

                                    <a href="#"><button class='button edit' title="Editar Docente" data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-editDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                    </svg></button></a>

                                    <a href="#"><button class='button delete' title="Eliminar Docente"  data-id="${participante.id_docente}" data-bs-toggle='modal' data-bs-target='#modal-deleteDocente'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                    <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                    <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                    </svg></button></a>
                                </td>
                            </tr>
                        `;
                    });
                } else if (response.error) {
                    console.error("Error en el servidor:", response.message);
                    rows = `<tr><td colspan="10" class="text-center">Error: ${response.message}</td></tr>`;
                } else {
                    rows = '<tr><td colspan="10" class="text-center">No hay Docentes registrados</td></tr>';
                }
                $('#tabla-docentes tbody').html(rows);

                if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
                    // Eliminar todos los botones excepto el de clase 'view'
                    document.querySelectorAll('#tabla-docentes tbody tr').forEach(row => {
                        const viewButton = row.querySelector('button.view');
                        const cell = viewButton?.closest('td');

                        if (cell && viewButton) {
                            // Limpiar la celda y volver a insertar solo el botón 'view'
                            cell.innerHTML = '';
                            cell.appendChild(viewButton);
                        }
                    });

                    // Eliminar también el botón superior de "Agregar Participante"
                    const btnAdd = document.getElementById('btn-addDocente');
                    if (btnAdd) {
                        btnAdd.remove();
                    }
                }

            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    }

    // Cargar la tabla al cargar la página
    cargarTabla();

    // Validación del formulario docente
    function showError(fieldId, message) {
        $(`#${fieldId}`).removeClass('is-valid').addClass('is-invalid');
        $(`#error-${fieldId}`).text(message).show();
    }

    function showValid(fieldId) {
        $(`#${fieldId}`).removeClass('is-invalid').addClass('is-valid');
        $(`#error-${fieldId}`).text('').hide();
    }

    let timeoutCedula;

    $(document).ready(function () {
        // ==================== VALIDACIONES EN TIEMPO REAL ====================

        // Validación de cédula para docentes
        $('#cedula').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '');
            const cedula = $(this).val().trim();
            clearTimeout(timeoutCedula);

            if (!/^\d{7,8}$/.test(cedula)) {
                showError('cedula', 'Cédula inválida (7-8 dígitos)');
                return;
            }

            timeoutCedula = setTimeout(() => {
                $.ajax({
                    url: '../../app/controlador/Docente/verificarCedulaDocente.php',
                    method: 'GET',
                    data: { cedula: cedula },
                    dataType: 'json',
                    success: function (response) {
                        if (response.exists) {
                            showError('cedula', 'Cédula ya registrada');
                        } else {
                            showValid('cedula');
                        }
                    },
                    error: function () {
                        showError('cedula', 'Error al verificar cédula');
                    }
                });
            }, 500);  // Ajusta el tiempo de espera según sea necesario
        });

        // Validación de nombre
        $('#primer_nombre').on('input', function () {
            const nombre = $(this).val().trim();
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(nombre) ? showValid('primer_nombre') : showError('primer_nombre', 'Solo letras sin espacios vacíos');
        });

        // Validación de apellido
        $('#primer_apellido').on('input', function () {
            const apellido = $(this).val().trim();
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúñÑ]+)*$/;
            regex.test(apellido) ? showValid('primer_apellido') : showError('primer_apellido', 'Solo letras sin espacios vacíos');
        });

        // Validación de teléfono
        $('#telefono').on('input', function () {
            let valor = $(this).val().trim();
            if (!/^0/.test(valor)) valor = valor;
            $(this).val(valor);

            const regex = /^(0412|0414|0416|0424|0426|0252|0264|0293)\d{7}$/;
            regex.test(valor) ? showValid('telefono') : showError('telefono', 'Formato válido: 0412 | 0414 | 0416 | 0424 | 0426 | 0293');
        });

        // Validación de fecha de nacimiento
        $('#fechaNacimiento').on('change', function () {
            const fecha = new Date($(this).val());
            const hoy = new Date();
            const minEdad = new Date(hoy.getFullYear() - 70, hoy.getMonth(), hoy.getDate());
            const maxEdad = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate());

            if (!$(this).val()) {
                showError('fechaNacimiento', 'Campo obligatorio');
            } else if (fecha > hoy) {
                showError('fechaNacimiento', 'Fecha futura no permitida');
            } else if (fecha < minEdad || fecha > maxEdad) {
                showError('fechaNacimiento', 'Debe tener entre 18 y 70 años');
            } else {
                showValid('fechaNacimiento');
            }
        });

        // Validación de género
        $('input[name="id_genero"]').on('change', function () {
            setTimeout(() => {
                const isValid = $('input[name="id_genero"]:checked').length > 0;
                isValid ? showValid('id_genero') : showError('id_genero', 'Seleccione su género');
            }, 50);
        });

        // Validación de correo
        $('#correo').on('input', function () {
            const correo = $(this).val().trim();
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            regex.test(correo) ? showValid('correo') : showError('correo', 'Correo electrónico inválido');
        });

        // ==================== MANEJO DEL MODAL ====================
        $('#modal-addDocente').on('show.bs.modal', function () {
            $('input[name="id_genero"]').prop('checked', false);
            $('#error-id_genero').hide().text('');
        });

        $('#modal-addDocente').on('hidden.bs.modal', function (e) {
            // Solo limpiar si el modal que se cerró es efectivamente el de agregar docente
            if (e.target.id === 'modal-addDocente') {
                // Resetear formulario
                $('#form-docente')[0].reset();

                // Limpiar validaciones visuales
                $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                $('.error-feedback').hide().text('');

                // Limpiar timeout de cédula
                clearTimeout(timeoutCedula);

                // Resetear estado del género
                $('input[name="id_genero"]').prop('checked', false)
                    .closest('.form-check').removeClass('is-valid is-invalid');
            }
        });

        // ==================== ENVÍO DEL FORMULARIO ====================
        $('#form-docente').on('submit', function (e) {
            e.preventDefault();

            // Disparar validaciones
            $('input, select').trigger('input').trigger('change');

            // Verificación final de cédula
            let cedulaValida = false;
            const cedula = $('#cedula').val().trim();

            if (/^\d{7,8}$/.test(cedula)) {
                $.ajax({
                    url: '../../app/controlador/Docente/verificarCedulaDocente.php',
                    method: 'GET',
                    data: { cedula: cedula },
                    async: false,
                    dataType: 'json',
                    success: function (response) {
                        cedulaValida = !response.exists;
                        response.exists && showError('cedula', 'Cédula ya registrada en docentes');
                    },
                    error: function () {
                        showError('cedula', 'Error al verificar cédula');
                        cedulaValida = false;
                    }
                });
            }

            if ($('.is-invalid').length === 0 && cedulaValida) {
                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#modal-addDocente').modal('hide');
                            cargarTabla();
                            toastr.success("¡El Docente se registró correctamente!");
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
    });

});


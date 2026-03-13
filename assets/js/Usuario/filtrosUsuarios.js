// Función para cargar la tabla según los filtros
function cargarTablaConFiltros() {
    // Obtener valores de los filtros
    const rol = $('#rol-filter').val();
    const estado = $('#estado-filter').val();


    // Realizar la solicitud AJAX
    $.ajax({
        url: '../../app/controlador/Usuario/obtenerUsuariosConFiltros.php', // Ruta del archivo PHP
        method: 'GET',
        data: { rol, estado }, // Enviar filtros como parámetros
        dataType: 'json',
        success: function (data) {
            let rows = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(participante => {
                    let rolBadgeClass = participante.rol === 'Administrador' ? 'badge bg-primary" style="min-width: 100px;' :
                        participante.rol === 'Apoyo' ? 'badge bg-secondary" style="min-width: 100px;' :
                            'badge bg-secondary" style="min-width: 100px;';

                    let estadoBadgeClass = participante.estado_usuario === 'Activo' ? 'badge bg-success" style="min-width: 70px;' :
                        participante.estado_usuario === 'Inactivo' ? 'badge bg-danger" style="min-width: 70px;' :
                            'badge bg-secondary" style="min-width: 100px;';

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

                                    ${participante.id_usuario != currentUserId ? `
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
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los datos:', error);
        }
    });
}

// Llamar a la función al cambiar un filtro
$('#rol-filter, #estado-filter').on('change', cargarTablaConFiltros);

// Archivo unificado para manejar la tabla de docentes
$(document).ready(function () {
    let currentPage = 1;
    let recordsPerPage = 8;
    let currentFilters = {};
    let currentSort = { field: 'id_docente', direction: 'desc' };
    let currentSearchTerm = '';
    let allDocentes = [];

    // Función principal para cargar los registros
    function cargarRegistros(page, perPage, filters = {}, sort = null, searchTerm = '') {
        currentFilters = filters;
        currentSearchTerm = searchTerm;

        if (sort) {
            currentSort = sort;
        }

        // Construir parámetros
        const params = new URLSearchParams();

        // Agregar filtros
        if (filters.sexo) params.append('sexo', filters.sexo);
        if (filters.edad) params.append('edad', filters.edad);
        if (filters.estado) params.append('estado', filters.estado);

        // Agregar término de búsqueda
        if (searchTerm && searchTerm.trim() !== '') {
            params.append('search', searchTerm.trim());
        }

        // Agregar ordenamiento
        params.append('sort_field', currentSort.field);
        params.append('sort_direction', currentSort.direction);

        // Agregar paginación
        params.append('page', page);
        params.append('per_page', perPage);

        console.log('Solicitando datos con parámetros:', params.toString());

        $.ajax({
            url: '../../app/controlador/Docente/obtenerDocentes.php',
            method: 'GET',
            data: params.toString(),
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta del servidor:', response);

                if (response.error) {
                    console.error('Error del servidor:', response.message);
                    $('#tabla-docentes tbody').html(`<tr><td colspan="9" class="text-center">Error: ${response.message}</td></tr>`);
                    return;
                }

                // Verificar estructura de respuesta
                let docentes = [];
                let total = 0;

                if (response.data && Array.isArray(response.data)) {
                    docentes = response.data;
                    total = response.total || docentes.length;
                } else if (Array.isArray(response)) {
                    docentes = response;
                    total = response.length;
                } else {
                    console.error('Formato de respuesta inválido:', response);
                    $('#tabla-docentes tbody').html('<tr><td colspan="9" class="text-center">Formato de datos inválido</td></tr>');
                    return;
                }

                allDocentes = docentes;

                // Actualizar la tabla
                actualizarTabla(docentes);

                // Actualizar paginación
                if (perPage !== 0 && total > 0) {
                    const totalPages = Math.ceil(total / perPage);
                    renderizarPaginacion(totalPages, page, total);
                } else {
                    $('#paginacion-container').html('');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                console.error('Status:', status);
                console.error('Respuesta:', xhr.responseText);

                $('#tabla-docentes tbody').html('<tr><td colspan="9" class="text-center">Error al cargar los datos. Verifique la consola.</td></tr>');
            }
        });
    }

    function actualizarTabla(docentes) {
        let rows = '';

        if (docentes.length === 0) {
            rows = '<tr><td colspan="9" class="text-center">No se encontraron docentes con los criterios de búsqueda</td></tr>';
            $('#tabla-docentes tbody').html(rows);
            return;
        }

        docentes.forEach(docente => {
            const id = docente.id_docente || docente.id || 0;
            const cedula = docente.cedula || '';
            const nombre = docente.nombre || docente.primer_nombre || '';
            const apellido = docente.apellido || docente.primer_apellido || '';
            const genero = docente.genero || docente.sexo || '';
            const edad = docente.edad || 0;
            const correo = docente.correo || '';
            const estado = docente.estado || docente.estado_docente || 'Disponible';

            // Determinar el badge del estado
            let estadoBadge;
            switch (estado) {
                case "En formación":
                    estadoBadge = '<span class="badge bg-success">En formación</span>';
                    break;
                case "Disponible":
                    estadoBadge = '<span class="badge bg-warning text-black">Disponible</span>';
                    break;
                case "Asignado":
                    estadoBadge = '<span class="badge bg-primary">Asignado</span>';
                    break;
                case "Inactivo":
                    estadoBadge = '<span class="badge bg-danger">Inactivo</span>';
                    break;
                default:
                    estadoBadge = `<span class="badge bg-light text-dark">${estado}</span>`;
            }

            // Determinar el icono y clase del botón de estado
            const esInactivo = estado === "Inactivo";
            const estadoBtnIcon = esInactivo
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-check-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000000" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';

            const estadoBtnTitle = esInactivo ? "Activar Docente" : "Inactivar Docente";
            const estadoBtnClass = esInactivo ? "button activate" : "button inactivate";

            rows += `
                <tr>
                    <td>${id}</td>
                    <td>${cedula}</td>
                    <td>${nombre}</td>
                    <td>${apellido}</td>
                    <td>${genero}</td>
                    <td>${edad}</td>
                    <td>${correo}</td>
                    <td>${estadoBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class='button view' title="Ver Docente" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-viewDocente'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                    <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                </svg>
                            </button>

                            <button class='button curso' title="Asignar Curso" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-AsignarCursoDocente'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-journal-bookmark-fill' viewBox='0 0 16 16'>
                                    <path fill-rule='evenodd' d='M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z'/>
                                    <path d='M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2'/>
                                    <path d='M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z'/>
                                </svg>
                            </button>

                            <button class='button edit' title="Editar Docente" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-editDocente'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                </svg>
                            </button>

                            <button class='${estadoBtnClass}' title="${estadoBtnTitle}" data-id="${id}" data-estado="${estado}">
                                ${estadoBtnIcon}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#tabla-docentes tbody').html(rows);

        // Aplicar restricciones de rol
        if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
            document.querySelectorAll('#tabla-docentes tbody tr').forEach(row => {
                const viewButton = row.querySelector('button.view');
                const cell = viewButton?.closest('td');

                if (cell && viewButton) {
                    cell.innerHTML = '';
                    cell.appendChild(viewButton);
                }
            });

            const btnAdd = document.getElementById('btn-addDocente');
            if (btnAdd) {
                btnAdd.remove();
            }
        }
    }

    function renderizarPaginacion(totalPages, activePage, totalRegistros) {
        const container = $('#paginacion-container');

        if (totalPages <= 1 || recordsPerPage === 0) {
            container.html('');
            return;
        }

        let paginationHTML = `
            <div class="info-paginacion-crud">
                Mostrando ${(activePage - 1) * recordsPerPage + 1} a ${Math.min(activePage * recordsPerPage, totalRegistros)} de ${totalRegistros} registros
            </div>
            <div class="paginacion-crud">
        `;

        // Botón Anterior
        paginationHTML += `
            <button data-page="${Math.max(1, activePage - 1)}" ${activePage === 1 ? 'disabled' : ''}>
                ← Anterior
            </button>
        `;

        // Lógica de números de página (estilo Historial)
        const inicio = Math.max(1, activePage - 2);
        const fin = Math.min(totalPages, activePage + 2);

        if (inicio > 1) {
            paginationHTML += `<button data-page="1">1</button>`;
            if (inicio > 2) paginationHTML += `<span>...</span>`;
        }

        for (let i = inicio; i <= fin; i++) {
            paginationHTML += `
                <button class="${activePage === i ? 'activa' : ''}" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        if (fin < totalPages) {
            if (fin < totalPages - 1) paginationHTML += `<span>...</span>`;
            paginationHTML += `<button data-page="${totalPages}">${totalPages}</button>`;
        }

        // Botón Siguiente
        paginationHTML += `
            <button data-page="${Math.min(totalPages, activePage + 1)}" ${activePage === totalPages ? 'disabled' : ''}>
                Siguiente →
            </button>
        `;

        paginationHTML += '</div>';
        container.html(paginationHTML);
    }

    // Función para aplicar filtros
    function aplicarFiltros() {
        currentPage = 1;
        const filters = {
            sexo: $('#sexo-filter').val(),
            edad: $('#edad-filter').val(),
            estado: $('#estado-filter').val()
        };
        cargarRegistros(currentPage, recordsPerPage, filters, currentSort, currentSearchTerm);
    }

    // Función para ordenar
    function ordenarTabla(field) {
        // Cambiar dirección si es el mismo campo
        if (currentSort.field === field) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.field = field;
            currentSort.direction = 'asc';
        }

        // Actualizar iconos de ordenamiento
        $('#tabla-docentes thead th').each(function () {
            const th = $(this);
            const sortField = th.data('sort');
            if (sortField === field) {
                th.find('.sort-icon').remove();
                th.append(`<span class="sort-icon">${currentSort.direction === 'asc' ? '↑' : '↓'}</span>`);
            } else {
                th.find('.sort-icon').remove();
            }
        });

        // Recargar con nuevo ordenamiento
        cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, currentSearchTerm);
    }

    // Eventos de paginación
    $(document).on('click', '.paginacion-crud button', function (e) {
        e.preventDefault();
        if ($(this).prop('disabled') || $(this).hasClass('activa')) return;

        const newPage = parseInt($(this).data('page'));
        if (!isNaN(newPage)) {
            currentPage = newPage;
            cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, currentSearchTerm);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Evento para cambiar registros por página
    $('#registros').on('change', function () {
        recordsPerPage = $(this).val() === "0" ? 0 : parseInt($(this).val());
        currentPage = 1;
        cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, currentSearchTerm);
    });

    // Eventos para filtros
    $('#sexo-filter, #edad-filter, #estado-filter').on('change', function () {
        aplicarFiltros();
    });

    // Evento para ordenamiento
    $(document).on('click', '#tabla-docentes thead th[data-sort]', function () {
        const field = $(this).data('sort');
        if (field) {
            ordenarTabla(field);
        }
    });

    // Variable para el temporizador de búsqueda (debounce)
    let searchTimer;

    // Evento para búsqueda con AJAX
    $('#searchInput').on('keyup', function () {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();

        searchTimer = setTimeout(() => {
            currentPage = 1;
            currentSearchTerm = searchTerm;
            cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, searchTerm);
        }, 300); // Retardo de 300ms para evitar muchas peticiones
    });

    // Evento único para el botón de cambiar estado
    $(document).on('click', '.button.activate, .button.inactivate', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const docenteId = $(this).data('id');
        const estadoActual = $(this).data('estado');
        const esInactivo = estadoActual === 'Inactivo';

        // Determinar el nuevo estado
        const nuevoEstado = esInactivo ? 'Disponible' : 'Inactivo';
        const accionTexto = esInactivo ? 'activar' : 'inactivar';
        const mensajeConfirmacion = `¿Está seguro de que desea ${accionTexto} este docente?`;

        if (confirm(mensajeConfirmacion)) {
            cambiarEstadoDocente(docenteId, nuevoEstado);
        }
    });

    // Función para cambiar el estado del docente via AJAX
    function cambiarEstadoDocente(docenteId, nuevoEstado) {
        $.ajax({
            url: '../../app/controlador/Docente/cambiarEstadoDocente.php',
            method: 'POST',
            data: {
                id_docente: docenteId,
                nuevo_estado: nuevoEstado
            },
            dataType: 'json',
            beforeSend: function () {
                toastr.info('Procesando solicitud...');
            },
            success: function (response) {
                if (response && response.success) {
                    toastr.success(response.message);
                    // Recargar la tabla manteniendo los filtros y búsqueda actuales
                    cargarRegistros(currentPage, recordsPerPage, currentFilters, currentSort, currentSearchTerm);
                } else {
                    toastr.error(response.message || 'Error al cambiar el estado');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                toastr.error('Error de conexión al servidor');
            }
        });
    }

    // Hacer funciones disponibles globalmente
    window.aplicarFiltros = aplicarFiltros;
    window.cargarRegistros = cargarRegistros;
    window.ordenarTabla = ordenarTabla;

    // Inicializar tabla
    cargarRegistros(currentPage, recordsPerPage);
});
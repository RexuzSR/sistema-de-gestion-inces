// Archivo unificado para manejar la tabla de participantes
$(document).ready(function () {
    let currentPage = 1;
    let recordsPerPage = 8;
    let currentFilters = { genero: '', edad: '', estado: '' };
    let currentSort = { field: 'id_participante', direction: 'desc' };
    let currentSearchTerm = '';

    // Almacenamiento local de los datos
    let allParticipantes = []; // Todos los datos originales
    let filteredParticipantes = []; // Datos después de filtros y búsqueda

    // Función para normalizar texto (quitar acentos y pasar a minúsculas)
    function normalizeString(str) {
        if (!str) return '';
        return str.toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    // Función inicial para cargar TODOS los registros del servidor
    function initTabla() {
        // Mostrar estado de carga
        $('#tabla-participantes tbody').html('<tr><td colspan="9" class="text-center">Cargando participantes...</td></tr>');

        $.ajax({
            url: '../../app/controlador/obtenerParticipantes.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    console.error('Error del servidor:', response.message);
                    $('#tabla-participantes tbody').html(`<tr><td colspan="9" class="text-center text-danger">Error: ${response.message}</td></tr>`);
                    return;
                }

                // Extraer datos (manejar tanto {data: [...]} como un array directo)
                if (response.data && Array.isArray(response.data)) {
                    allParticipantes = response.data;
                } else if (Array.isArray(response)) {
                    allParticipantes = response;
                } else {
                    allParticipantes = [];
                }

                // Procesar la tabla por primera vez
                procesarTabla();
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                $('#tabla-participantes tbody').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar los datos. Verifique su conexión.</td></tr>');
            }
        });
    }

    // Función principal que aplica filtros, búsqueda, ordenamiento y paginación localmente
    function procesarTabla() {
        // 1. Aplicar Filtros
        filteredParticipantes = allParticipantes.filter(p => {
            const genero = p.genero || p.sexo || '';
            const matchGenero = currentFilters.genero === '' || genero === currentFilters.genero;

            let matchEdad = true;
            if (currentFilters.edad !== '') {
                const edad = parseInt(p.edad) || 0;
                const [min, max] = currentFilters.edad.split('-').map(Number);
                matchEdad = edad >= min && edad <= max;
            }

            const estado = (p.estado || p.estado_participante || 'En sistema').toLowerCase();
            const matchEstado = currentFilters.estado === '' || estado === currentFilters.estado.toLowerCase();

            return matchGenero && matchEdad && matchEstado;
        });

        // 2. Aplicar Búsqueda
        if (currentSearchTerm.trim() !== '') {
            const searchTermNormalized = normalizeString(currentSearchTerm);
            const tokens = searchTermNormalized.split(/\s+/).filter(t => t.length > 0);

            filteredParticipantes = filteredParticipantes.filter(p => {
                // Construir una cadena única con todos los campos por los que se puede buscar
                const searchableData = normalizeString([
                    p.nombre || p.primer_nombre || '',
                    p.segundo_nombre || '',
                    p.apellido || p.primer_apellido || '',
                    p.segundo_apellido || '',
                    p.cedula || '',
                    p.correo || ''
                ].join(' '));

                // El registro debe contener TODAS las palabras buscadas (en cualquier orden)
                return tokens.every(token => searchableData.includes(token));
            });
        }

        // 3. Aplicar Ordenamiento
        filteredParticipantes.sort((a, b) => {
            let valA = a[currentSort.field] || '';
            let valB = b[currentSort.field] || '';

            // Tratamiento especial para campos numéricos
            if (currentSort.field === 'id_participante' || currentSort.field === 'id' || currentSort.field === 'edad') {
                valA = parseInt(valA) || 0;
                valB = parseInt(valB) || 0;
            } else {
                valA = valA.toString().toLowerCase();
                valB = valB.toString().toLowerCase();
            }

            if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
            if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        // 4. Calcular Paginación
        const totalRegistros = filteredParticipantes.length;
        let totalPages = 1;
        let datosPagina = filteredParticipantes;

        if (recordsPerPage > 0) {
            totalPages = Math.ceil(totalRegistros / recordsPerPage) || 1;

            // Asegurar que la página actual sea válida
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;

            datosPagina = filteredParticipantes.slice(startIndex, endIndex);
        } else {
            // Si recordsPerPage es 0 (Mostrar Todos)
            currentPage = 1;
        }

        // 5. Renderizar
        actualizarTabla(datosPagina);
        renderizarPaginacion(totalPages, currentPage);
    }

    function actualizarTabla(participantes) {
        let rows = '';

        if (participantes.length === 0) {
            rows = '<tr><td colspan="9" class="text-center">No se encontraron participantes con los criterios actuales</td></tr>';
            $('#tabla-participantes tbody').html(rows);
            return;
        }

        participantes.forEach(participante => {
            const id = participante.id_participante || participante.id || 0;
            const cedula = participante.cedula || '';
            const nombre = participante.nombre || participante.primer_nombre || '';
            const apellido = participante.apellido || participante.primer_apellido || '';
            const genero = participante.genero || participante.sexo || '';
            const edad = participante.edad || 0;
            const correo = participante.correo || '';
            const estado = participante.estado || participante.estado_participante || 'En sistema';

            // Determinar el badge del estado
            let estadoBadge;
            switch (estado) {
                case "En formación":
                    estadoBadge = '<span class="badge bg-success text-black">En formación</span>';
                    break;
                case "En sistema":
                    estadoBadge = '<span class="badge bg-warning text-black">En sistema</span>';
                    break;
                case "Asignado":
                    estadoBadge = '<span class="badge bg-primary text-black">Asignado</span>';
                    break;
                case "Inactivo":
                case "Disponible":
                    estadoBadge = '<span class="badge bg-info text-black">Disponible</span>';
                    break;
                case "Diferido":
                    estadoBadge = '<span class="badge bg-danger text-white">Diferido</span>';
                    break;
                default:
                    estadoBadge = `<span class="badge bg-light text-black">${estado}</span>`;
            }

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
                            <button class='button view' title="Ver Participante" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-viewParticipante'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                    <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                </svg>
                            </button>

                            <button class='button curso' title="Asignar Curso" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-AsignarCursoParticipante'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-journal-bookmark-fill' viewBox='0 0 16 16'>
                                    <path fill-rule='evenodd' d='M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8z'/>
                                    <path d='M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2'/>
                                    <path d='M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z'/>
                                </svg>
                            </button>

                            <button class='button edit' title="Editar Participante" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-editParticipante'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                </svg>
                            </button>

                            <button class='button delete' title="Eliminar Participante" data-id="${id}" data-bs-toggle='modal' data-bs-target='#modal-deleteParticipante'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                    <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                    <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#tabla-participantes tbody').html(rows);

        // Aplicar restricciones de rol (si aplica)
        if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
            // Ocultar botón "Agregar Participantes"
            const btnAdd = document.getElementById('btn-addParticipante');
            if (btnAdd) btnAdd.remove();

            // Eliminar botones curso, edit y delete
            document.querySelectorAll('#tabla-participantes tbody .button.curso, #tabla-participantes tbody .button.edit, #tabla-participantes tbody .button.delete').forEach(boton => {
                boton.remove();
            });

            // Asegurar que solo quede botón 'view'
            document.querySelectorAll('#tabla-participantes tbody .button.view').forEach(boton => {
                const td = boton.closest('td');
                if (td) {
                    const parentDiv = boton.closest('.btn-group');
                    if (parentDiv) {
                        parentDiv.innerHTML = '';
                        parentDiv.appendChild(boton);
                    }
                }
            });
        }
    }

    function renderizarPaginacion(totalPages, activePage) {
        if (totalPages <= 1 || recordsPerPage === 0) {
            $('#pagination').html('');
            return;
        }

        let paginationHTML = '';

        // Botón Primero y Anterior
        paginationHTML += `
            <li class="page-item ${activePage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="1">Primero</a>
            </li>
            <li class="page-item ${activePage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${Math.max(1, activePage - 1)}">Anterior</a>
            </li>
        `;

        // Determinar rango de páginas a mostrar
        let startPage = Math.max(1, activePage - 2);
        let endPage = Math.min(totalPages, activePage + 2);

        if (activePage <= 3) {
            endPage = Math.min(5, totalPages);
        }

        if (activePage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }

        // Mostrar páginas numéricas
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${activePage === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Botones Siguiente y Último
        paginationHTML += `
            <li class="page-item ${activePage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${Math.min(totalPages, activePage + 1)}">Siguiente</a>
            </li>
            <li class="page-item ${activePage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${totalPages}">Último</a>
            </li>
        `;

        $('#pagination').html(paginationHTML);
    }

    // --- EVENT LISTENERS (Delegación de eventos para evitar crear y destruir constantemente) ---

    // Eventos de paginación
    $(document).on('click', '#pagination .page-link', function (e) {
        e.preventDefault();
        const parentLi = $(this).parent();
        if (parentLi.hasClass('disabled') || parentLi.hasClass('active')) return;

        const newPage = parseInt($(this).data('page'));
        if (!isNaN(newPage)) {
            currentPage = newPage;
            procesarTabla();
        }
    });

    // Evento para cambiar registros por página
    $('#registros').on('change', function () {
        const val = $(this).val();
        recordsPerPage = val === "Todos" ? 0 : parseInt(val);
        currentPage = 1;
        procesarTabla();
    });

    // Eventos para filtros
    $('#sexo-filter, #edad-filter, #estado-filter').on('change', function () {
        currentPage = 1;
        currentFilters.genero = $('#sexo-filter').val();
        currentFilters.edad = $('#edad-filter').val();
        currentFilters.estado = $('#estado-filter').val();
        procesarTabla();
    });

    // Evento para limpiar filtros (click en el botón btn-filter)
    $('#btn-filter').on('click', function (e) {
        e.preventDefault();

        // Resetear selectores de interfaz
        $('#sexo-filter').val('');
        $('#edad-filter').val('');
        $('#estado-filter').val('');
        $('#searchInput').val('');

        // Resetear variables internas
        currentFilters = { genero: '', edad: '', estado: '' };
        currentSearchTerm = '';
        currentPage = 1;
        recordsPerPage = parseInt($('#registros').val()) || 0; // Se mantiene el valor del selector actual
        if ($('#registros').val() === "Todos") recordsPerPage = 0;

        procesarTabla();
    });

    // Variable para el temporizador de búsqueda (debounce)
    let searchTimer;

    // Función centralizada de búsqueda
    function ejecutarBusqueda() {
        clearTimeout(searchTimer);
        const searchTerm = $('#searchInput').val();
        searchTimer = setTimeout(() => {
            currentPage = 1;
            currentSearchTerm = searchTerm;
            procesarTabla();
        }, 300);
    }

    // Evento para búsqueda — keyup, input y paste
    $('#searchInput').on('keyup input', ejecutarBusqueda);
    $('#searchInput').on('paste', function() {
        setTimeout(ejecutarBusqueda, 50); // esperar a que el valor se pegue
    });

    // Evitar que el enter haga submit en el campo de busqueda
    $('#searchInput').on('keydown', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Evento para ordenamiento
    $(document).on('click', '#tabla-participantes thead th[data-sort]', function () {
        const field = $(this).data('sort');
        if (!field) return;

        // Cambiar dirección si es el mismo campo
        if (currentSort.field === field) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.field = field;
            currentSort.direction = 'asc'; // Por defecto asc al cambiar campo
        }

        // Actualizar iconos de ordenamiento visualmente
        $('#tabla-participantes thead th').each(function () {
            $(this).find('.sort-icon').remove();
            if ($(this).data('sort') === field) {
                $(this).append(`<span class="sort-icon ml-1">${currentSort.direction === 'asc' ? '↑' : '↓'}</span>`);
            }
        });

        procesarTabla();
    });

    // Agregar atributos data-sort a las cabeceras si no existen
    $('#tabla-participantes thead th').each(function (index) {
        const text = $(this).text().trim().toLowerCase();
        let field = '';

        switch (text) {
            case 'id': field = 'id_participante'; break;
            case 'cédula': field = 'cedula'; break;
            case 'nombre': field = 'nombre'; break;
            case 'apellido': field = 'apellido'; break;
            case 'sexo': field = 'genero'; break;
            case 'edad': field = 'edad'; break;
            case 'correo': field = 'correo'; break;
            case 'estado': field = 'estado'; break;
        }

        if (field) {
            $(this).attr('data-sort', field);
            $(this).css('cursor', 'pointer');
            // Agregar ícono inicial al campo por defecto
            if (field === currentSort.field) {
                $(this).append(`<span class="sort-icon ml-1">${currentSort.direction === 'asc' ? '↑' : '↓'}</span>`);
            }
        }
    });

    // Para hacer disponible cargarRegistros si algun otro script lo llama
    window.cargarRegistros = function () {
        initTabla();
    };

    window.aplicarFiltros = function () {
        $('#sexo-filter').trigger('change');
    };

    // Inicializar tabla la primera vez
    initTabla();
});

// Actualización de cursos
let intervaloActualizacion = null;

$('#modal-addParticipante').on('shown.bs.modal', function () {
    cargarCursos();
    intervaloActualizacion = setInterval(cargarCursos, 30000);
});

$('#modal-addParticipante').on('hidden.bs.modal', function () {
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
        intervaloActualizacion = null;
    }
});

function cargarCursos() {
    $.ajax({
        url: '../../app/controlador/cursosDisponibles.php',
        method: 'GET',
        dataType: 'json',
        beforeSend: function () {
            $('#id_curso').html('<option value="">Cargando...</option>');
        },
        success: function (response) {
            const select = $('#id_curso').empty();
            if (response.success) {
                select.append('<option value="">Seleccione</option>');
                const noAsignado = response.data.find(curso => curso.id_curso === 1);
                const otrosCursos = response.data.filter(curso => curso.id_curso !== 1);
                if (noAsignado) {
                    select.append(`<option value="${noAsignado.id_curso}">${noAsignado.display}</option>`);
                }
                otrosCursos.forEach(curso => {
                    select.append(`<option value="${curso.id_curso}">${curso.display}</option>`);
                });
            } else {
                select.append(`<option value="" disabled>${response.error}</option>`);
            }
        },
        error: function (xhr) {
            $('#id_curso').html('<option value="" disabled>Error de conexión</option>');
            console.error('Detalles del error:', xhr.responseText);
        }
    });
}
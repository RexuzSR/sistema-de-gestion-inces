// Archivo unificado para manejar la tabla de Cursos
$(document).ready(function () {
    let currentPage = 1;
    let recordsPerPage = 8;
    let currentFilters = { turno: '', modalidad: '', estado: '', anio: '' };
    let currentSort = { field: 'id_curso', direction: 'desc' };
    let currentSearchTerm = '';
    let isInitialLoad = true;

    function animateCounter(selector, endVal, decimalPlaces = 0) {
        const el = $(selector)[0];
        if (!el) return;
        const startVal = parseFloat(el.textContent) || 0;
        if (startVal === endVal) {
            el.innerHTML = endVal.toFixed(decimalPlaces);
            return;
        }
        let startTimestamp = null;
        const duration = 600;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentVal = progress * (endVal - startVal) + startVal;
            el.innerHTML = currentVal.toFixed(decimalPlaces);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                el.innerHTML = endVal.toFixed(decimalPlaces);
            }
        };
        window.requestAnimationFrame(step);
    }

    function formatFecha(fechaStr) {
        if (!fechaStr) return '-';
        const parts = fechaStr.split('-');
        if (parts.length === 3) {
            return `${parseInt(parts[2], 10)}/${parseInt(parts[1], 10)}/${parts[0].substring(2)}`;
        }
        return fechaStr;
    }

    function calcularDuracion(inicioStr, finStr) {
        if (!inicioStr || !finStr) return '-';
        const d1 = new Date(inicioStr + 'T00:00:00');
        const d2 = new Date(finStr + 'T00:00:00');
        const diffTime = d2.getTime() - d1.getTime();
        const totalDays = Math.round(diffTime / (1000 * 3600 * 24)) + 1;
        if (totalDays <= 0) return '0 d';
        if (totalDays < 7) {
            return `${totalDays} d`;
        }
        const weeks = Math.floor(totalDays / 7);
        const remainingDays = totalDays % 7;
        if (remainingDays === 0) {
            return `${weeks} sem`;
        } else {
            return `${weeks} sem ${remainingDays} d`;
        }
    }

    function actualizarKPIs(cursos) {
        let totalCursos = cursos.length;
        let enProceso = 0;
        let enEspera = 0;
        let diferidos = 0;
        let culminados = 0;
        let cuposLibres = 0;
        let totalCupos = 0;
        
        cursos.forEach(c => {
            const estatusReal = c.nombre_estatuscurso || c.estatus;
            
            if (estatusReal === 'En proceso') enProceso++;
            else if (estatusReal === 'En espera') enEspera++;
            else if (estatusReal === 'Diferido') diferidos++;
            else if (estatusReal === 'Culminado') culminados++;
            
            const maxPart = parseInt(c.max_participantes, 10) || 0;
            const inscritos = parseInt(c.num_inscritos, 10) || 0;
            cuposLibres += Math.max(0, maxPart - inscritos);
            totalCupos += maxPart;
        });
        
        if (isInitialLoad) {
            animateCounter('#kpi-total', totalCursos);
            animateCounter('#kpi-proceso', enProceso);
            animateCounter('#kpi-espera', enEspera);
            animateCounter('#kpi-diferidos', diferidos);
            animateCounter('#kpi-culminados', culminados);
            animateCounter('#kpi-cupos', cuposLibres);
            $('#kpi-cupos-total').text(' / ' + totalCupos);
            isInitialLoad = false;
        } else {
            $('#kpi-total').text(totalCursos);
            $('#kpi-proceso').text(enProceso);
            $('#kpi-espera').text(enEspera);
            $('#kpi-diferidos').text(diferidos);
            $('#kpi-culminados').text(culminados);
            $('#kpi-cupos').text(cuposLibres);
            $('#kpi-cupos-total').text(' / ' + totalCupos);
        }
        
        // Resumen Contextual
        let summaryText = `Actualmente existen <strong>${totalCursos}</strong> cursos filtrados: <strong>${enProceso}</strong> en proceso, <strong>${enEspera}</strong> en espera, <strong>${diferidos}</strong> diferidos y <strong>${culminados}</strong> culminados.`;
        if (totalCursos === 0) {
            summaryText = `No existen cursos registrados para los criterios seleccionados.`;
        }
        $('#contextual-summary').html(summaryText).show();

        // Re-inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Cargar metas y actualizar semáforo de cupos
        cargarMetasYActualizarSemaforo(currentFilters.anio);
    }

    // Almacenamiento local de los datos
    let allCursos = []; // Todos los datos originales
    let filteredCursos = []; // Datos después de filtros y búsqueda

    // Función inicial para cargar TODOS los registros del servidor
    function initTabla() {
        // Mostrar estado de carga
        $('#tabla-cursos tbody').html('<tr><td colspan="11" class="text-center">Cargando cursos...</td></tr>');

        $.ajax({
            url: '../../app/controlador/Curso/obtenerCursos.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                // El backend devuelve a veces arreglos directos o {success: false, message: ...}
                if (response.success === false) {
                    console.error('Error del servidor:', response.message);
                    $('#tabla-cursos tbody').html(`<tr><td colspan="11" class="text-center text-danger">Error: ${response.message}</td></tr>`);
                    return;
                }

                // Extraer datos
                if (Array.isArray(response)) {
                    // Filtrar cursos y excluir el curso con id_curso = 1 (comportamiento esperado original)
                    allCursos = response.filter(curso => parseInt(curso.id_curso) !== 1);
                    allCursos.forEach(curso => {
                        curso.cupos_disponibles = (parseInt(curso.max_participantes, 10) || 0) - (parseInt(curso.num_inscritos, 10) || 0);
                    });
                } else {
                    allCursos = [];
                }

                // Procesar la tabla por primera vez
                procesarTabla();

                // Notificar si hay un curso nuevo para resaltarlo visualmente
                resaltarCursoNuevo();
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                $('#tabla-cursos tbody').html('<tr><td colspan="11" class="text-center text-danger">Error al cargar los datos. Verifique su conexión.</td></tr>');
            }
        });
    }

    // Función principal que aplica filtros, búsqueda, ordenamiento y paginación localmente
    function procesarTabla() {
        // 1. Aplicar Filtros
        filteredCursos = allCursos.filter(c => {
            const turno = c.turno || '';
            const matchTurno = currentFilters.turno === '' || turno === currentFilters.turno;

            const modalidad = c.id_modalidad || c.nombre_modalidad || '';
            // Si el filtro envía ID pero el objeto tiene el nombre, puede fallar el eq estricto
            // por seguridad comparamos tanto por id como por nombre si es posible,
            // pero el filtro (HTML) envía el nombre de turno, id modalidad, y el id de estatus.
            const matchModalidad = currentFilters.modalidad === '' ||
                String(c.id_modalidad) === String(currentFilters.modalidad) ||
                String(c.nombre_modalidad) === String(currentFilters.modalidad);

            const estado = c.id_estatusCurso || c.estatus || c.nombre_estatuscurso || '';
            const matchEstado = currentFilters.estado === '' ||
                String(c.estatus) === String(currentFilters.estado) ||
                String(c.id_estatusCurso) === String(currentFilters.estado);

            const fechaInicio = c.fecha_inicio || '';
            const cursoAnio = fechaInicio ? fechaInicio.substring(0, 4) : '';
            const matchAnio = currentFilters.anio === '' || cursoAnio === currentFilters.anio;

            return matchTurno && matchModalidad && matchEstado && matchAnio;
        });

        // 2. Aplicar Búsqueda
        if (currentSearchTerm.trim() !== '') {
            const term = currentSearchTerm.toLowerCase().trim();
            filteredCursos = filteredCursos.filter(c => {
                const id = String(c.id_curso || '').toLowerCase();
                const nombre = String(c.nombre_curso || '').toLowerCase();
                const modalidad = String(c.nombre_modalidad || '').toLowerCase();
                const turno = String(c.turno || '').toLowerCase();

                return id.includes(term) || nombre.includes(term) || modalidad.includes(term) || turno.includes(term);
            });
        }

        // 3. Aplicar Ordenamiento
        filteredCursos.sort((a, b) => {
            let valA = a[currentSort.field] !== undefined ? a[currentSort.field] : '';
            let valB = b[currentSort.field] !== undefined ? b[currentSort.field] : '';

            // Tratamiento especial para campos numéricos
            if (['id_curso', 'num_inscritos', 'max_participantes', 'id_estatusCurso', 'id_modalidad', 'cupos_disponibles', 'horas_totales'].includes(currentSort.field)) {
                valA = parseInt(valA) || 0;
                valB = parseInt(valB) || 0;
            } else if (['fecha_inicio', 'fecha_fin'].includes(currentSort.field)) {
                valA = new Date(valA).getTime() || 0;
                valB = new Date(valB).getTime() || 0;
            } else {
                valA = String(valA).toLowerCase();
                valB = String(valB).toLowerCase();
            }

            if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
            if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        // 4. Calcular Paginación
        const totalRegistros = filteredCursos.length;
        let totalPages = 1;
        let datosPagina = filteredCursos;

        if (recordsPerPage > 0) {
            totalPages = Math.ceil(totalRegistros / recordsPerPage) || 1;

            // Asegurar que la página actual sea válida
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;

            datosPagina = filteredCursos.slice(startIndex, endIndex);
        } else {
            // Si recordsPerPage es 0 (Mostrar Todos)
            currentPage = 1;
        }

        // 5. Renderizar
        actualizarKPIs(filteredCursos);
        actualizarTabla(datosPagina);
        renderizarPaginacion(totalPages, currentPage);
    }

    function actualizarTabla(cursos) {
        let rows = '';

        if (cursos.length === 0) {
            rows = '<tr><td colspan="11" class="text-center">No se encontraron cursos con los criterios actuales</td></tr>';
            $('#tabla-cursos tbody').html(rows);
            return;
        }

        cursos.forEach(curso => {
            const cuposDisponibles = curso.max_participantes - curso.num_inscritos;
            const regParticipanteCurso = parseInt(curso.registros_participante_curso, 10) || 0;
            const puedeEliminarCurso = regParticipanteCurso === 0;
            // Manejar tanto estatus como nombre_estatuscurso por las diferentes estructuras devueltas por los controladores
            const estatusReal = curso.nombre_estatuscurso || curso.estatus;

            // Evaluar si es "Próximo a iniciar"
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const fInicio = curso.fecha_inicio ? new Date(curso.fecha_inicio + 'T00:00:00') : null;
            const diffTime = fInicio ? (fInicio - today) : null;
            const diffDays = diffTime !== null ? Math.ceil(diffTime / (1000 * 60 * 60 * 24)) : null;

            const esEnEspera = String(curso.id_estatusCurso) === '1' || estatusReal === 'En espera';
            const esProximo = esEnEspera && diffDays !== null && diffDays >= 0 && diffDays <= 7;

            let estadoBadgeHtml = '';
            if (esProximo) {
                estadoBadgeHtml = `<span class="badge badge-proximo-iniciar" data-bs-toggle="tooltip" data-bs-placement="top" title="El curso está próximo a iniciar en ${diffDays} día(s)."><i class="bi bi-clock-fill me-1"></i>Próximo</span>`;
            } else {
                const classMap = {
                    'En espera': 'badge-espera',
                    'En proceso': 'badge-proceso',
                    'Culminado': 'badge-culminado',
                    'Diferido': 'badge-diferido',
                    'En pausa': 'badge-pausa',
                    'Inactivo': 'badge-espera',
                    'Aprobado': 'badge-proceso'
                };
                const badgeClass = classMap[estatusReal] || 'bg-secondary';
                estadoBadgeHtml = `<span class="badge ${badgeClass}">${estatusReal}</span>`;
            }

            // Ocupación
            const inscritos = parseInt(curso.num_inscritos, 10) || 0;
            const maxPart = parseInt(curso.max_participantes, 10) || 30;
            const pct = maxPart > 0 ? (inscritos / maxPart) * 100 : 0;
            const safePct = Math.min(Math.max(pct, 0), 100);
            
            let barClass = 'progress-fill-available';
            let alertClass = 'alert-available';
            let alertText = 'Disponible';
            
            if (pct === 100) {
                barClass = 'progress-fill-full';
                alertClass = 'alert-full';
                alertText = 'Completo';
            } else if (pct > 80) {
                barClass = 'progress-fill-warning';
                alertClass = 'alert-warning';
                alertText = 'Últimos Cupos';
            }
            
            const occupancyHtml = `
                <div class="occupancy-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Ocupación: ${inscritos} de ${maxPart} cupos ocupados.">
                    <span class="occupancy-text">${inscritos} / ${maxPart} (${pct.toFixed(0)}%)</span>
                    <div class="occupancy-progress-bar">
                        <div class="occupancy-progress-fill ${barClass}" style="width: ${safePct}%;"></div>
                    </div>
                    <span class="occupancy-alert ${alertClass}">${alertText}</span>
                </div>
            `;

            // Duración (en horas)
            const duracionText = curso.horas_totales && parseInt(curso.horas_totales, 10) > 0
                ? `${parseInt(curso.horas_totales, 10)} horas`
                : calcularDuracion(curso.fecha_inicio, curso.fecha_fin);

            rows += `
                <tr>
                    <td>${curso.id_curso}</td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">${curso.nombre_curso}</div>
                        ${curso.preimpreso ? `<span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem; font-weight: 500;">${curso.preimpreso}</span>` : ''}
                    </td>
                    <td>${curso.nombre_modalidad === 'Semi presencial' ? 'Semi-pres.' : curso.nombre_modalidad}</td>
                    <td class="text-center">${occupancyHtml}</td>
                    <td class="text-center font-weight-bold">${cuposDisponibles}</td>
                    <td>${curso.turno}</td>
                    <td>${duracionText}</td>
                    <td>${curso.fecha_inicio ? formatFecha(curso.fecha_inicio) : '-'}</td>
                    <td>${curso.fecha_fin ? formatFecha(curso.fecha_fin) : '-'}</td>
                    <td>${estadoBadgeHtml}</td>
                    <td>
                        <!-- Botones de acción -->
                        <div class="btn-group" role="group">
                            <button class='button view view-curso' title="Ver Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-viewCurso'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-binoculars' viewBox='0 0 16 16'>
                                <path d='M3 2.5A1.5 1.5 0 0 1 4.5 1h1A1.5 1.5 0 0 1 7 2.5V5h2V2.5A1.5 1.5 0 0 1 10.5 1h1A1.5 1.5 0 0 1 13 2.5v2.382a.5.5 0 0 0 .276.447l.895.447A1.5 1.5 0 0 1 15 7.118V14.5a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 14.5v-3a.5.5 0 0 1 .146-.354l.854-.853V9.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v.793l.854.853A.5.5 0 0 1 7 11.5v3A1.5 1.5 0 0 1 5.5 16h-3A1.5 1.5 0 0 1 1 14.5V7.118a1.5 1.5 0 0 1 .83-1.342l.894-.447A.5.5 0 0 0 3 4.882zM4.5 2a.5.5 0 0 0-.5.5V3h2v-.5a.5.5 0 0 0-.5-.5zM6 4H4v.882a1.5 1.5 0 0 1-.83 1.342l-.894.447A.5.5 0 0 0 2 7.118V13h4v-1.293l-.854-.853A.5.5 0 0 1 5 10.5v-1A1.5 1.5 0 0 1 6.5 8h3A1.5 1.5 0 0 1 11 9.5v1a.5.5 0 0 1-.146.354l-.854.853V13h4V7.118a.5.5 0 0 0-.276-.447l-.895-.447A1.5 1.5 0 0 1 12 4.882V4h-2v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zm4-1h2v-.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5zm4 11h-4v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zm-8 0H2v.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5z'/>
                                </svg>
                            </button>
                    
                            <button class='button participante' title="Asignar Participantes" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-participanteCurso'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-person-add" viewBox="0 0 16 16">
                                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                                <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                                </svg>
                            </button>

                            <button class='button config' title="Calificación" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#cursoModal'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                                </svg>
                            </button>
                    
                            <button type="button" class='button edit' title="Editar Curso" data-edit-curso="1" data-id="${curso.id_curso}">
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>
                                </svg>
                            </button>
                    
                            ${puedeEliminarCurso ? `
                            <button class='button delete' title="Eliminar Curso" data-id="${curso.id_curso}" data-bs-toggle='modal' data-bs-target='#modal-deleteCurso'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='#fff' class='bi bi-trash' viewBox='0 0 16 16'>
                                <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                </svg>
                            </button>` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#tabla-cursos tbody').html(rows);

        // Aplicar restricciones de rol (si aplica)
        if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
            // Eliminar botón superior de "Agregar Curso"
            const btnAdd = document.getElementById('btn-addcurso');
            if (btnAdd) btnAdd.remove();

            // Eliminar todos los botones excepto el de clase 'view'
            document.querySelectorAll('#tabla-cursos tbody tr').forEach(row => {
                const viewButton = row.querySelector('button.view');
                const btnGroup = viewButton?.closest('.btn-group');

                if (btnGroup && viewButton) {
                    btnGroup.innerHTML = '';
                    btnGroup.appendChild(viewButton);
                }
            });
        }
    }

    function renderizarPaginacion(totalPages, activePage) {
        const container = $('#paginacion-container');
        const totalRegistros = filteredCursos.length;

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

    // Función especial que estaba en el código original para resaltar curso nuevo
    function resaltarCursoNuevo() {
        const cursoNuevoId = sessionStorage.getItem('cursoRecienCreado');

        if (cursoNuevoId) {
            setTimeout(() => {
                const filas = document.querySelectorAll('#tabla-cursos tbody tr');
                let encontrado = false;

                filas.forEach(fila => {
                    const idCelda = fila.querySelector('td:first-child');
                    if (idCelda && idCelda.textContent.trim() === String(cursoNuevoId)) {
                        fila.classList.add('curso-nuevo');
                        fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        encontrado = true;
                    }
                });

                if (encontrado) {
                    sessionStorage.removeItem('cursoRecienCreado');
                }
            }, 500);
        }
    }

    // --- EVENT LISTENERS ---

    // Eventos de paginación
    $(document).on('click', '.paginacion-crud button', function (e) {
        e.preventDefault();
        if ($(this).prop('disabled') || $(this).hasClass('activa')) return;

        const newPage = parseInt($(this).data('page'));
        if (!isNaN(newPage)) {
            currentPage = newPage;
            procesarTabla();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Evento para cambiar registros por página
    $('#registros').on('change', function () {
        const val = $(this).val();
        recordsPerPage = (val === "Todos" || val === "0") ? 0 : parseInt(val);
        currentPage = 1;
        procesarTabla();
    });

    // Eventos para filtros (formulario "filtros_curso")
    $('#turno_filter, #modalidad_filter, #estado_filter, #anio_filter').on('change', function () {
        currentPage = 1;
        currentFilters.turno = $('#turno_filter').val();
        currentFilters.modalidad = $('#modalidad_filter').val();
        currentFilters.estado = $('#estado_filter').val();
        currentFilters.anio = $('#anio_filter').val();
        procesarTabla();
    });

    // Evitar recarga del formulario al aplicar filtros
    $('#filtros_curso').submit(function (event) {
        event.preventDefault();
        $('#turno_filter').trigger('change');
    });

    // Evento para limpiar filtros 
    $('#btn-filter').on('click', function (e) {
        e.preventDefault();

        // Resetear selectores
        $('#turno_filter').val('');
        $('#modalidad_filter').val('');
        $('#estado_filter').val('');
        $('#anio_filter').val('');
        $('#searchInput').val('');

        // Resetear variables internas
        currentFilters = { turno: '', modalidad: '', estado: '', anio: '' };
        currentSearchTerm = '';
        currentPage = 1;

        recordsPerPage = parseInt($('#registros').val()) || 0;
        if ($('#registros').val() === "Todos") recordsPerPage = 0;

        procesarTabla();
    });

    // Variable para el temporizador de búsqueda (debounce)
    let searchTimer;

    // Evento para búsqueda
    $('#searchInput').on('keyup input', function () {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();

        searchTimer = setTimeout(() => {
            currentPage = 1;
            currentSearchTerm = searchTerm;
            procesarTabla();
        }, 300); // Retardo de 300ms
    });

    // Evitar submit en busqueda si está en un form
    $('.search-bar').on('submit', function (e) {
        e.preventDefault();
    });

    // Evento para ordenamiento haciendo click en los encabezados
    $(document).on('click', '#tabla-cursos thead th[data-sort]', function () {
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
        $('#tabla-cursos thead th').each(function () {
            $(this).find('.sort-icon').remove();
            if ($(this).data('sort') === field) {
                $(this).append(`<span class="sort-icon ml-1">${currentSort.direction === 'asc' ? '↑' : '↓'}</span>`);
            }
        });

        procesarTabla();
    });

    // Agregar atributos data-sort a las cabeceras visuales
    $('#tabla-cursos thead th').each(function (index) {
        const text = $(this).text().trim().toLowerCase();
        let field = '';

        switch (text) {
            case 'id': field = 'id_curso'; break;
            case 'curso': field = 'nombre_curso'; break;
            case 'modalidad': field = 'id_modalidad'; break;
            case 'ocupación':
            case 'ocupacion': field = 'num_inscritos'; break;
            case 'cupos disponibles':
            case 'cupos libres': field = 'cupos_disponibles'; break;
            case 'turno': field = 'turno'; break;
            case 'duración':
            case 'duracion': field = 'horas_totales'; break;
            case 'fecha inicio': field = 'fecha_inicio'; break;
            case 'fecha fin': field = 'fecha_fin'; break;
            case 'estado': field = 'id_estatusCurso'; break;
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

    // Hacemos la inicialización disponible si otros ficheros la requieren
    window.cargarTablaCursos = function () {
        initTabla();
    };

    // Escuchar evento para recargar cursos sin recargar la página
    $(document).on('cursos:actualizados', function () {
        initTabla();
    });

    let allMetas = [];

    function cargarMetasYActualizarSemaforo(selectedYear) {
        if (allMetas.length > 0) {
            actualizarSemaforoCupos(selectedYear);
            return;
        }

        $.ajax({
            url: '../../app/controlador/Metas/obtenerMetas.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.success !== false && Array.isArray(response.data)) {
                    allMetas = response.data;
                } else if (Array.isArray(response)) {
                    allMetas = response;
                }
                actualizarSemaforoCupos(selectedYear);
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar metas para semáforo:', error);
                actualizarSemaforoCupos(selectedYear);
            }
        });
    }

    function actualizarSemaforoCupos(selectedYear) {
        // Año por defecto si no hay filtro (usar año actual)
        const anio = selectedYear || new Date().getFullYear().toString();
        
        // Buscar la meta global anual del año seleccionado
        const metaGlobalAnual = allMetas.find(m => {
            // Meta global (sin ámbito)
            const esGlobal = !m.id_ambito || m.ambito_nombre === 'Global';
            
            // Del año seleccionado
            const metaAnio = m.fecha_inicio ? m.fecha_inicio.substring(0, 4) : '';
            const esMismoAnio = metaAnio === anio;
            
            // Periodo de tiempo anual
            let esAnual = false;
            if (m.fecha_inicio && m.fecha_fin) {
                const d1 = new Date(m.fecha_inicio + 'T00:00:00');
                const d2 = new Date(m.fecha_fin + 'T00:00:00');
                const diff = Math.round((d2.getTime() - d1.getTime()) / (1000 * 3600 * 24));
                // Rango cercano a 365 días
                esAnual = diff >= 360 && diff <= 370;
            }
            if (!esAnual && m.periodo_tiempo) {
                const pName = m.periodo_tiempo.toLowerCase();
                esAnual = pName.includes('anual') || pName.includes('año') || pName.includes('ano');
            }
            
            return esGlobal && esMismoAnio && esAnual;
        });

        const $kpiCard = $('.kpi-card.kpi-cupos');
        $kpiCard.removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue');

        if (metaGlobalAnual) {
            const metaObjetivo = parseInt(metaGlobalAnual.meta_objetivo, 10) || 0;
            const estudiantesFormados = parseInt(metaGlobalAnual.estudiantes_formados, 10) || 0;
            
            // Avance
            const pct = metaObjetivo > 0 ? (estudiantesFormados / metaObjetivo) * 100 : 0;
            
            // Semáforo: Verde (>=90), Amarillo (70-89), Rojo (<70)
            let semClass = 'kpi-state-red';
            if (pct >= 90) {
                semClass = 'kpi-state-green';
            } else if (pct >= 70) {
                semClass = 'kpi-state-yellow';
            }
            
            $kpiCard.addClass(semClass);
            
            // Actualizar tooltip
            const titleMsg = `Semáforo de Meta Global Anual ${anio}: ${estudiantesFormados} / ${metaObjetivo} formados (${pct.toFixed(1)}%). Estado: ${pct >= 90 ? 'Óptimo' : (pct >= 70 ? 'Atención' : 'Crítico')}`;
            $kpiCard.find('.kpi-info-icon').attr('data-bs-original-title', titleMsg).attr('title', titleMsg);
        } else {
            $kpiCard.addClass('kpi-state-blue');
            const titleMsg = `Sin meta global anual registrada para el año ${anio}.`;
            $kpiCard.find('.kpi-info-icon').attr('data-bs-original-title', titleMsg).attr('title', titleMsg);
        }
    }

    // Inicializar tabla la primera vez
    initTabla();
});
$(document).ready(function () {
    let currentPage = 1;
    let recordsPerPage = parseInt($('#registros').val()) || 8;
    let currentSearch = '';

    function cargarMetas(page = 1, perPage = recordsPerPage, search = '') {
        currentPage = page;
        $.ajax({
            url: '../../app/controlador/Metas/obtenerMetas.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $('#tabla-metas tbody').html(`<tr><td colspan="9" class="text-center message-error">Error: ${response.message}</td></tr>`);
                    return;
                }

                let metas = response.data || [];
                
                // Aplicar búsqueda en cliente
                if (search && search.trim() !== '') {
                    const s = search.toLowerCase();
                    metas = metas.filter(m => (
                        String(m.id_meta).includes(s) ||
                        String(m.meta_objetivo).toLowerCase().includes(s) ||
                        String(m.estudiantes_formados).includes(s) ||
                        String(m.porcentaje_cumplimiento).includes(s) ||
                        String(m.fecha_inicio).includes(s) ||
                        String(m.fecha_fin).includes(s) ||
                        String(m.estado).toLowerCase().includes(s)
                    ));
                }

                // Aplicar filtros adicionales
                const tipoFilter = $('#TipoDeMeta-filter').val();
                const estadoFilter = $('#estado-filter').val();
                
                if (tipoFilter) {
                    metas = metas.filter(m => m.tipo_objetivo === tipoFilter);
                }
                
                if (estadoFilter) {
                    metas = metas.filter(m => m.estado === estadoFilter);
                }

                const total = metas.length;
                // Paginación en cliente
                let items = metas;
                if (perPage !== 0) {
                    const start = (page - 1) * perPage;
                    items = metas.slice(start, start + perPage);
                }

                renderTabla(items);

                if (perPage !== 0 && total > 0) {
                    const totalPages = Math.ceil(total / perPage);
                    renderPaginacion(totalPages, page);
                } else {
                    $('#pagination').html('');
                }
            },
            error: function () {
                $('#tabla-metas tbody').html('<tr><td colspan="9" class="text-center message-error">Error al cargar los datos.</td></tr>');
            }
        });
    }

    function getProgressClass(pct) {
        if (pct >= 86) return 'progress-bar-success';
        if (pct >= 70) return 'progress-bar-warning';
        return 'progress-bar-danger';
    }

    function getEstadoClass(estado, cumplimiento) {
        estado = (estado || '').toLowerCase();
        cumplimiento = (cumplimiento || '').toLowerCase();

        if (estado === 'cerrada') {
            if (cumplimiento === 'cumplida') return 'status-active';
            return 'status-danger';
        }

        if (estado === 'activa') return 'status-pending';
        if (estado === 'alcanzada') return 'status-active';
        if (estado === 'no alcanzada') return 'status-danger';
        return 'status-pending';
    }

    function getEstadoText(estado) {
        switch(estado.toLowerCase()) {
            case 'alcanzada': return 'Alcanzada';
            case 'no alcanzada': return 'No Alcanzada';
            case 'en proceso': return 'En Proceso';
            default: return estado;
        }
    }

    function renderTabla(metas) {
        if (!Array.isArray(metas) || metas.length === 0) {
            $('#tabla-metas tbody').html('<tr><td colspan="9" class="text-center message-empty">No hay metas registradas</td></tr>');
            return;
        }

        let rows = '';
        metas.forEach((m, index) => {
            const pct = parseFloat(m.porcentaje_cumplimiento) || 0;
            const safePct = Math.min(Math.max(pct, 0), 100);
            const overlayTextColor = (pct >= 70 && pct < 86) ? '#000' : '#fff';
            
            const progressHtml = `
                <div class="progress-container">
                    <div class="progress-bar-custom ${getProgressClass(pct)}" style="width: ${safePct}%;">
                        <span class="progress-text ${overlayTextColor === '#000' ? 'progress-text-dark' : ''}">
                            ${safePct.toFixed(2)}%
                        </span>
                    </div>
                </div>
            `;

            const estadoTexto = getEstadoText(m.estado);
            const estadoClass = getEstadoClass(m.estado, m.estado_cumplimiento);
            
            rows += `
                <tr style="--row-index: ${index};">
                    <td class="font-weight-semibold">${m.id_meta}</td>
                    <td>${m.meta_objetivo}</td>
                    <td>${m.tipo_objetivo || 'Aprobados'}</td>
                    <td class="text-end">${m.estudiantes_formados}</td>
                    <td class="text-center">${progressHtml}</td>
                    <td>${new Date(m.fecha_inicio).toLocaleDateString('es-ES')}</td>
                    <td>${new Date(m.fecha_fin).toLocaleDateString('es-ES')}</td>
                    <td><span class="status-badge ${estadoClass}">${estadoTexto}</span></td>
                    <td>
                        <div class="actions-container">
                            <button class="button-action view" data-id="${m.id_meta}" data-bs-toggle="modal" data-bs-target="#modal-viewMeta" title="Ver detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill text-white" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg>
                            </button>
                            <button class="button-action edit" data-id="${m.id_meta}" data-bs-toggle="modal" data-bs-target="#modal-editMeta" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-wide text-white" viewBox="0 0 16 16">
                                    <path d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434zM8 12.997a4.998 4.998 0 1 1 0-9.995 4.998 4.998 0 0 1 0 9.996z"/>
                                </svg>
                            </button>
                            <button class="button-action delete" data-id="${m.id_meta}" data-bs-toggle="modal" data-bs-target="#modal-deleteMeta" title="Eliminar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill text-white" viewBox="0 0 16 16">
                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#tabla-metas tbody').html(rows);

        // Ocultar botones de editar y eliminar para usuarios de Apoyo
        if (typeof userRole !== 'undefined' && userRole === 'Apoyo') {
            $('#tabla-metas .button-action.edit, #tabla-metas .button-action.delete').hide();
        }
    }

    function renderPaginacion(totalPages, activePage) {
        if (totalPages <= 1) { 
            $('#pagination').html(''); 
            return; 
        }
        
        let html = '';

        html += `<li class="pagination-item ${activePage === 1 ? 'disabled' : ''}">
                    <a class="pagination-link" href="#" data-page="1">Primero</a>
                </li>`;
        
        html += `<li class="pagination-item ${activePage === 1 ? 'disabled' : ''}">
                    <a class="pagination-link" href="#" data-page="${Math.max(1, activePage-1)}">Anterior</a>
                </li>`;

        let start = Math.max(1, activePage - 2);
        let end = Math.min(totalPages, activePage + 2);
        if (activePage <= 3) end = Math.min(5, totalPages);
        if (activePage >= totalPages - 2) start = Math.max(1, totalPages - 4);

        for (let i = start; i <= end; i++) {
            html += `<li class="pagination-item ${activePage === i ? 'active' : ''}">
                        <a class="pagination-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
        }

        html += `<li class="pagination-item ${activePage === totalPages ? 'disabled' : ''}">
                    <a class="pagination-link" href="#" data-page="${Math.min(totalPages, activePage+1)}">Siguiente</a>
                </li>`;
        
        html += `<li class="pagination-item ${activePage === totalPages ? 'disabled' : ''}">
                    <a class="pagination-link" href="#" data-page="${totalPages}">Último</a>
                </li>`;

        $('#pagination').html(html);
    }

    // Paginar
    $(document).on('click', '#pagination .pagination-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page')) || 1;
        cargarMetas(page, recordsPerPage, currentSearch);
    });

    // Cambiar registros por página
    $('#registros').on('change', function () {
        recordsPerPage = $(this).val() === '0' ? 0 : parseInt($(this).val());
        cargarMetas(1, recordsPerPage, currentSearch);
    });

    // Búsqueda
    let timer;
    $('#searchInput').on('keyup', function () {
        clearTimeout(timer);
        const val = $(this).val();
        timer = setTimeout(() => {
            currentSearch = val;
            cargarMetas(1, recordsPerPage, currentSearch);
        }, 300);
    });

    // Filtros
    $('#TipoDeMeta-filter, #estado-filter').on('change', function () {
        cargarMetas(1, recordsPerPage, currentSearch);
    });

    // Crear meta
    $(document).on('submit', '#form-addMeta', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('../../app/controlador/Metas/crearMeta.php', data, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                toastr.success(res.message || 'Meta creada');
                $('#modal-addMeta').modal('hide');
                cargarMetas(1, recordsPerPage, currentSearch);
                $('#form-addMeta')[0].reset();
            } else {
                toastr.error(res.message || 'Error al crear meta');
            }
        });
    });

    // Abrir modal ver
    $(document).on('click', '.button-action.view', function () {
        const id = $(this).data('id');
        $.get('../../app/controlador/Metas/verMeta.php', { id }, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                const m = res.data;
                $('#view_id_meta').text(m.id_meta);
                $('#view_meta_objetivo').text(m.meta_objetivo);
                $('#view_tipo_objetivo').text(m.tipo_objetivo || 'Aprobados');
                $('#view_estudiantes_formados').text(m.estudiantes_formados);
                $('#view_ambito').text(m.ambito_nombre || '');
                $('#view_fecha_inicio').text(m.fecha_inicio);
                $('#view_fecha_fin').text(m.fecha_fin);
                
                const pct = parseFloat(m.porcentaje_cumplimiento) || 0;
                const safePct = Math.min(Math.max(pct, 0), 100);
                const progressHtml = `
                    <div class="progress-container">
                        <div class="progress-bar-custom ${getProgressClass(pct)}" style="width: ${safePct}%;">
                            <span class="progress-text">
                                ${safePct.toFixed(2)}%
                            </span>
                        </div>
                    </div>
                `;
                
                $('#view_porcentaje_cumplimiento').html(progressHtml);
                
                const estadoTexto = getEstadoText(m.estado);
                const estadoClass = getEstadoClass(m.estado, m.estado_cumplimiento);
                $('#view_estado_cumplimiento').attr('class', 'status-badge ' + estadoClass).text(m.estado_cumplimiento || 'En Proceso');
                $('#view_estado').text(estadoTexto);
                $('#view_created_at').text(m.created_at);
                
                // Guardar datos para que el modal los procese cuando esté completamente mostrado
                try { window._metaToShow = m; } catch (e) { console.error('set _metaToShow', e); }
            } else {
                toastr.error(res.message || 'No se encontró la meta');
            }
        });
    });

    // Abrir modal editar (llenar campos)
    $(document).on('click', '.button-action.edit', function () {
        const id = $(this).data('id');
        $.get('../../app/controlador/Metas/verMeta.php', { id }, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                const m = res.data;
                $('#edit_id_meta').val(m.id_meta);
                $('#edit_meta_objetivo').val(m.meta_objetivo);
                $('#edit_estudiantes_formados').val(m.estudiantes_formados);
                $('#edit_id_ambito').val(m.id_ambito || '');
                $('#edit_fecha_inicio').val(m.fecha_inicio);
                $('#edit_fecha_fin').val(m.fecha_fin);
                $('#edit_tipo_objetivo').val(m.tipo_objetivo || 'Aprobados');
            } else {
                toastr.error(res.message || 'No se encontró la meta');
            }
        });
    });

    // Guardar cambios
    $(document).on('submit', '#form-editMeta', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('../../app/controlador/Metas/actualizarMeta.php', data, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                toastr.success(res.message || 'Meta actualizada');
                $('#modal-editMeta').modal('hide');
                cargarMetas(currentPage, recordsPerPage, currentSearch);
            } else {
                toastr.error(res.message || 'Error al actualizar meta');
            }
        });
    });

    // Eliminar (confirm)
    let deleteId = null;
    $(document).on('click', '.button-action.delete', function () {
        deleteId = $(this).data('id');
    });

    $('#confirm-deleteMeta').on('click', function () {
        if (!deleteId) return;
        $.post('../../app/controlador/Metas/eliminarMeta.php', { id: deleteId }, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                toastr.success(res.message || 'Meta eliminada');
                $('#modal-deleteMeta').modal('hide');
                // Remover fila del DOM para respuesta instantánea en vez de recargar toda la tabla
                const btn = $(`button.button-action.delete[data-id="${deleteId}"]`);
                if (btn.length) {
                    btn.closest('tr').remove();
                } else {
                    // fallback: recargar si no se encuentra la fila
                    cargarMetas(1, recordsPerPage, currentSearch);
                }
                deleteId = null;
            } else {
                toastr.error(res.message || 'Error al eliminar meta');
            }
        });
    });

    // Inicializar
    cargarMetas(currentPage, recordsPerPage, currentSearch);
});
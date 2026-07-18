$(document).ready(function () {
    let currentPage = 1;
    let recordsPerPage = parseInt($('#registros').val()) || 8;
    let currentSearch = '';
    let filterAmbitoId = new URLSearchParams(window.location.search).get('ambito_id');
    let isInitialLoad = true;

    function getMetaPeriodo(m) {
        if (!m.fecha_inicio || !m.fecha_fin) return 'global';
        const d1 = new Date(m.fecha_inicio + 'T00:00:00');
        const d2 = new Date(m.fecha_fin + 'T00:00:00');
        const timeDiff = d2.getTime() - d1.getTime();
        const daysDiff = Math.round(timeDiff / (1000 * 3600 * 24));
        
        if (daysDiff >= 27 && daysDiff <= 32) return 'mensual';
        if (daysDiff >= 88 && daysDiff <= 93) return 'trimestral';
        if (daysDiff >= 179 && daysDiff <= 185) return 'semestral';
        if (daysDiff >= 364 && daysDiff <= 367) return 'anual';
        return 'global';
    }

    function getVencimientoThreshold(periodo) {
        if (periodo === 'mensual') return 7;
        if (periodo === 'trimestral') return 15;
        return 30; // semestral, anual, global, etc.
    }

    function formatFecha(fechaStr) {
        if (!fechaStr) return '-';
        const parts = fechaStr.split('-');
        if (parts.length === 3) {
            return `${parseInt(parts[2], 10)}/${parseInt(parts[1], 10)}/${parts[0]}`;
        }
        return fechaStr;
    }

    function animateCounter(selector, endVal, isPercent = false, decimalPlaces = 0) {
        const el = $(selector)[0];
        if (!el) return;
        const startVal = parseFloat(el.textContent) || 0;
        if (startVal === endVal) {
            el.innerHTML = endVal.toFixed(decimalPlaces) + (isPercent ? '%' : '');
            return;
        }
        let startTimestamp = null;
        const duration = 600;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentVal = progress * (endVal - startVal) + startVal;
            el.innerHTML = currentVal.toFixed(decimalPlaces) + (isPercent ? '%' : '');
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                el.innerHTML = endVal.toFixed(decimalPlaces) + (isPercent ? '%' : '');
            }
        };
        window.requestAnimationFrame(step);
    }

    function cargarMetas(page = 1, perPage = recordsPerPage, search = '') {
        currentPage = page;
        $.ajax({
            url: '../../app/controlador/Metas/obtenerMetas.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $('#tabla-metas tbody').html(`<tr><td colspan="8" class="text-center message-error">Error: ${response.message}</td></tr>`);
                    return;
                }

                let metas = response.data || [];
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Poblar filtro de Año dinámicamente
                const selectAnio = $('#anio-filter');
                const currentYear = new Date().getFullYear();
                
                // Obtener años únicos de las metas
                const years = [...new Set(metas.map(m => {
                    if (!m.fecha_inicio) return null;
                    return new Date(m.fecha_inicio + 'T00:00:00').getFullYear();
                }).filter(Boolean))].sort((a, b) => b - a);

                // Asegurar que el año actual esté en la lista
                if (!years.includes(currentYear)) {
                    years.push(currentYear);
                    years.sort((a, b) => b - a);
                }

                // Re-poblar opciones manteniendo la seleccionada
                selectAnio.find('option:not([value=""])').remove();
                years.forEach(yr => {
                    selectAnio.append(`<option value="${yr}">${yr}</option>`);
                });

                // Si es la carga inicial, pre-seleccionar el año vigente
                let activeAnio = selectAnio.val();
                if (isInitialLoad && !activeAnio) {
                    activeAnio = String(currentYear);
                    selectAnio.val(activeAnio);
                } else if (activeAnio && years.includes(parseInt(activeAnio, 10))) {
                    selectAnio.val(activeAnio);
                }

                // 1. Recalcular estado_cumplimiento basado en las nuevas reglas para todos los registros
                metas.forEach(m => {
                    const pct = parseFloat(m.porcentaje_cumplimiento) || 0;
                    const isVencida = m.fecha_fin ? (new Date(m.fecha_fin + 'T00:00:00') < today) : false;
                    const diffTime = m.fecha_fin ? (new Date(m.fecha_fin + 'T00:00:00') - today) : null;
                    const diffDays = diffTime !== null ? Math.ceil(diffTime / (1000 * 60 * 60 * 24)) : null;
                    
                    const periodo = getMetaPeriodo(m);
                    const threshold = getVencimientoThreshold(periodo);
                    const isProxima = diffDays !== null && diffDays >= 0 && diffDays <= threshold;

                    if (pct > 100) {
                        m.estado_cumplimiento = 'Sobrecumplida';
                    } else if (pct === 100) {
                        m.estado_cumplimiento = 'Cumplida';
                    } else if (isVencida) {
                        m.estado_cumplimiento = 'Vencida';
                    } else if (isProxima && pct < 100) {
                        m.estado_cumplimiento = 'Próxima a vencer';
                    } else if (pct >= 75) {
                        m.estado_cumplimiento = 'Encaminada';
                    } else {
                        m.estado_cumplimiento = 'En riesgo';
                    }
                });

                // 2. Obtener filtros para calcular la cohorte de KPIs
                const tipoFilter = $('#TipoDeMeta-filter').val();
                const estadoFilter = $('#estado-filter').val();
                const periodoFilter = $('#periodo-filter').val();
                const ambitoFilter = $('#ambito-filter').val();
                const anioFilter = selectAnio.val();

                let metasCohort = metas;

                // Filtrar por Tipo de Meta
                if (tipoFilter) {
                    metasCohort = metasCohort.filter(m => m.tipo_objetivo === tipoFilter);
                }
                // Filtrar por Período
                if (periodoFilter) {
                    metasCohort = metasCohort.filter(m => {
                        const p = getMetaPeriodo(m);
                        return p === periodoFilter;
                    });
                }

                // Filtrar por Ámbito
                if (ambitoFilter) {
                    if (ambitoFilter === 'Global') {
                        metasCohort = metasCohort.filter(m => !m.id_ambito || m.ambito_nombre === 'Global');
                    } else {
                        metasCohort = metasCohort.filter(m => String(m.id_ambito) === String(ambitoFilter));
                    }
                }

                // Filtrar por Año
                if (anioFilter) {
                    metasCohort = metasCohort.filter(m => {
                        if (!m.fecha_inicio) return false;
                        const year = new Date(m.fecha_inicio + 'T00:00:00').getFullYear();
                        return String(year) === String(anioFilter);
                    });
                }

                // 3. CALCULAR TARJETAS KPI (sobre metasCohort: filtrado por año si está seleccionado, de lo contrario todas)
                const metasKpi = anioFilter ? metasCohort.filter(m => {
                    if (!m.fecha_inicio) return false;
                    const year = new Date(m.fecha_inicio + 'T00:00:00').getFullYear();
                    return String(year) === String(anioFilter);
                }) : metasCohort;

                const totalMetas = metasKpi.length;
                let cumplidasCount = 0;
                let sobrecumplidasCount = 0;
                let enRiesgoCount = 0;
                let vencidasCount = 0;
                let sumCumplimiento = 0;

                metasKpi.forEach(m => {
                    const pct = parseFloat(m.porcentaje_cumplimiento) || 0;
                    sumCumplimiento += pct;

                    if (m.estado_cumplimiento === 'Sobrecumplida') {
                        sobrecumplidasCount++;
                    } else if (m.estado_cumplimiento === 'Cumplida') {
                        cumplidasCount++;
                    } else if (m.estado_cumplimiento === 'En riesgo' || m.estado_cumplimiento === 'Próxima a vencer') {
                        enRiesgoCount++;
                    } else if (m.estado_cumplimiento === 'Vencida') {
                        vencidasCount++;
                    }
                });

                const promCumplimiento = totalMetas > 0 ? (sumCumplimiento / totalMetas) : 0;

                // Pintar valores KPI en DOM
                const labelText = anioFilter ? `Total de Metas ${anioFilter}` : 'Total de Metas (Todos)';
                $('#kpi-total-label').text(labelText);
                if (isInitialLoad) {
                    animateCounter('#kpi-total', totalMetas);
                    animateCounter('#kpi-cumplidas', cumplidasCount);
                    animateCounter('#kpi-sobrecumplidas', sobrecumplidasCount);
                    animateCounter('#kpi-riesgo', enRiesgoCount);
                    animateCounter('#kpi-vencidas', vencidasCount);
                    animateCounter('#kpi-promedio', promCumplimiento, true, 1);
                    isInitialLoad = false;
                } else {
                    $('#kpi-total').text(totalMetas);
                    $('#kpi-cumplidas').text(cumplidasCount);
                    $('#kpi-sobrecumplidas').text(sobrecumplidasCount);
                    $('#kpi-riesgo').text(enRiesgoCount);
                    $('#kpi-vencidas').text(vencidasCount);
                    $('#kpi-promedio').text(promCumplimiento.toFixed(1) + '%');
                }

                // APLICAR CLASES DE SEMÁFORO VISUAL A TARJETAS KPI
                // Total Metas -> azul (neutro/informativo)
                $('.kpi-total').removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue')
                               .addClass('kpi-state-blue');

                // Cumplidas -> verde >= 80%, amarillo 50-79%, rojo < 50%
                const pctCumplidas = totalMetas > 0 ? (cumplidasCount / totalMetas) * 100 : 0;
                let classCumplidas = 'kpi-state-red';
                if (pctCumplidas >= 80) {
                    classCumplidas = 'kpi-state-green';
                } else if (pctCumplidas >= 50) {
                    classCumplidas = 'kpi-state-yellow';
                }
                $('.kpi-cumplidas').removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue')
                                   .addClass(classCumplidas);

                // Sobrecumplidas -> verde si > 0, azul si 0
                let classSobrecumplidas = sobrecumplidasCount > 0 ? 'kpi-state-green' : 'kpi-state-blue';
                $('.kpi-sobrecumplidas').removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue')
                                       .addClass(classSobrecumplidas);

                // En Riesgo -> verde if 0, amarillo if 1-2, rojo if >= 3
                let classRiesgo = 'kpi-state-red';
                if (enRiesgoCount === 0) {
                    classRiesgo = 'kpi-state-green';
                } else if (enRiesgoCount <= 2) {
                    classRiesgo = 'kpi-state-yellow';
                }
                $('.kpi-riesgo').removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue')
                                .addClass(classRiesgo);

                // Vencidas -> verde if 0, amarillo if 1, rojo if >= 2
                let classVencidas = 'kpi-state-red';
                if (vencidasCount === 0) {
                    classVencidas = 'kpi-state-green';
                } else if (vencidasCount === 1) {
                    classVencidas = 'kpi-state-yellow';
                }
                $('.kpi-vencidas').removeClass('kpi-state-green kpi-state-yellow kpi-state-red kpi-state-blue')
                                 .addClass(classVencidas);

                // Pintar resumen contextual
                const scopeText = anioFilter ? `el año ${anioFilter}` : 'el período seleccionado';
                let summaryText = `Actualmente existen <strong>${totalMetas}</strong> metas para ${scopeText}, de las cuales <strong>${cumplidasCount + sobrecumplidasCount}</strong> están cumplidas, <strong>${enRiesgoCount}</strong> en riesgo y <strong>${vencidasCount}</strong> vencidas.`;
                if (totalMetas === 0) {
                    summaryText = anioFilter ? `No existen metas registradas para el año ${anioFilter}.` : 'No existen metas registradas para el período seleccionado.';
                }
                $('#contextual-summary').html(summaryText).show();

                // 4. APLICAR FILTRO DE ESTADO Y BÚSQUEDA PARA LA TABLA (sobre metasCohort)
                let metasTabla = metasCohort;

                if (estadoFilter) {
                    if (estadoFilter === 'En riesgo') {
                        metasTabla = metasTabla.filter(m => m.estado_cumplimiento === 'En riesgo' || m.estado_cumplimiento === 'Próxima a vencer');
                    } else {
                        metasTabla = metasTabla.filter(m => m.estado_cumplimiento === estadoFilter);
                    }
                }

                if (search && search.trim() !== '') {
                    const s = search.toLowerCase();
                    metasTabla = metasTabla.filter(m => (
                        String(m.id_meta).includes(s) ||
                        String(m.meta_objetivo).toLowerCase().includes(s) ||
                        String(m.estudiantes_formados).includes(s) ||
                        String(m.porcentaje_cumplimiento).includes(s) ||
                        String(m.fecha_inicio).includes(s) ||
                        String(m.fecha_fin).includes(s) ||
                        String(m.estado_cumplimiento || '').toLowerCase().includes(s)
                    ));
                }



                // 5. ORDENAR METAS POR IMPORTANCIA (Crítica, Vencida, Próxima a vencer, En riesgo, Encaminada, Cumplida, Sobrecumplida)
                const statePriority = {
                    'Crítica': 1,
                    'Vencida': 2,
                    'Próxima a vencer': 3,
                    'En riesgo': 4,
                    'Encaminada': 5,
                    'Cumplida': 6,
                    'Sobrecumplida': 7
                };
                
                metasTabla.sort((a, b) => {
                    const pA = statePriority[a.estado_cumplimiento] || 99;
                    const pB = statePriority[b.estado_cumplimiento] || 99;
                    return pA - pB;
                });

                // Mostrar/ocultar aviso de filtro activo (solo cuando se filtra por un ámbito específico de la URL)
                if (filterAmbitoId) {
                    $('#active-filter-alert').fadeIn();
                } else {
                    $('#active-filter-alert').hide();
                }

                const total = metasTabla.length;
                // Paginación en cliente
                let items = metasTabla;
                if (perPage !== 0) {
                    const start = (page - 1) * perPage;
                    items = metasTabla.slice(start, start + perPage);
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
                $('#tabla-metas tbody').html('<tr><td colspan="8" class="text-center message-error">Error al cargar los datos.</td></tr>');
            }
        });
    }

    function getProgressClass(pct) {
        if (pct >= 90) return 'progress-bar-success';
        if (pct >= 70) return 'progress-bar-warning';
        return 'progress-bar-danger';
    }

    function getEstadoClass(cumplimiento) {
        const c = (cumplimiento || '').toLowerCase();
        if (c === 'sobrecumplida') return 'status-active';
        if (c === 'cumplida') return 'status-active';
        if (c === 'no cumplida' || c === 'crítica') return 'status-danger';
        if (c === 'en progreso' || c === 'en proceso' || c === 'en riesgo' || c === 'encaminada') return 'status-pending';
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
            $('#tabla-metas tbody').html('<tr><td colspan="8" class="text-center message-empty">No hay metas registradas</td></tr>');
            return;
        }

        let rows = '';
        metas.forEach((m, index) => {
            const pct = parseFloat(m.porcentaje_cumplimiento) || 0;
            const safePct = Math.min(Math.max(pct, 0), 100);
            const overlayTextColor = (pct >= 70 && pct < 90) ? '#000' : '#fff';
            
            const logrado = parseInt(m.estudiantes_formados, 10) || 0;
            const metaVal = parseInt(m.meta_objetivo, 10) || 0;
            let subtextHtml = '';
            if (logrado < metaVal) {
                if (m.estado_cumplimiento === 'Vencida') {
                    subtextHtml = `<span class="cumplimiento-subtext resta"><i class="bi bi-arrow-right-short"></i>Faltaron ${metaVal - logrado} para cumplir</span>`;
                } else {
                    subtextHtml = `<span class="cumplimiento-subtext resta"><i class="bi bi-arrow-right-short"></i>Restan ${metaVal - logrado} para cumplir</span>`;
                }
            } else if (logrado > metaVal) {
                subtextHtml = `<span class="cumplimiento-subtext supera"><i class="bi bi-chevron-double-up"></i>Superada por ${logrado - metaVal}</span>`;
            } else {
                subtextHtml = `<span class="cumplimiento-subtext exacta"><i class="bi bi-check-all"></i>Cumplida exactamente</span>`;
            }

            const progressHtml = `
                <div class="progress-container">
                    <div class="progress-bar-custom ${getProgressClass(pct)}" style="width: ${safePct}%;">
                        <span class="progress-text ${overlayTextColor === '#000' ? 'progress-text-dark' : ''}">
                            ${pct.toFixed(2)}%
                        </span>
                    </div>
                </div>
                ${subtextHtml}
            `;

            // Determinar clases de fila y badges inteligentes
            let badgeHtml = '';
            let rowClass = '';
            switch(m.estado_cumplimiento) {
                case 'Sobrecumplida':
                    badgeHtml = `<span class="status-badge badge-sobrecumplida"><i class="bi bi-chevron-double-up me-1"></i>SOBRECUMPLIDA</span>`;
                    break;
                case 'Cumplida':
                    badgeHtml = `<span class="status-badge badge-cumplida"><i class="bi bi-check-circle-fill me-1"></i>CUMPLIDA</span>`;
                    break;
                case 'Encaminada':
                    badgeHtml = `<span class="status-badge badge-encaminada"><i class="bi bi-play-circle-fill me-1"></i>ENCAMINADA</span>`;
                    break;
                case 'Próxima a vencer':
                    badgeHtml = `<span class="status-badge badge-proxima"><i class="bi bi-clock me-1"></i>PRÓXIMA A VENCER</span>`;
                    rowClass = 'row-proxima';
                    break;
                case 'En riesgo':
                    badgeHtml = `<span class="status-badge badge-riesgo"><i class="bi bi-exclamation-triangle-fill me-1"></i>EN RIESGO</span>`;
                    break;
                case 'Crítica':
                    badgeHtml = `<span class="status-badge badge-critica"><i class="bi bi-exclamation-octagon-fill me-1"></i>CRÍTICA</span>`;
                    rowClass = 'row-critica';
                    break;
                case 'Vencida':
                    badgeHtml = `<span class="status-badge badge-vencida"><i class="bi bi-clock-history me-1"></i>VENCIDA</span>`;
                    rowClass = 'row-vencida';
                    break;
                default:
                    badgeHtml = `<span class="status-badge badge-vencida"><i class="bi bi-question-circle me-1"></i>INDEFINIDO</span>`;
            }

            // Determinar si la meta está vencida
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const isVencida = m.fecha_fin ? (new Date(m.fecha_fin + 'T00:00:00') < today) : false;
            const canEditOrDelete = m.estado !== 'Cerrada' && !isVencida;
            
            rows += `
                <tr class="${rowClass}" style="--row-index: ${index};">
                    <td class="font-weight-semibold">${m.id_meta}</td>
                    <td>${m.tipo_objetivo || 'Aprobados'}</td>
                    <td class="text-center logrado-meta-cell">${m.estudiantes_formados} / ${m.meta_objetivo}</td>
                    <td class="text-center">${progressHtml}</td>
                    <td>${m.fecha_inicio ? formatFecha(m.fecha_inicio) : '-'}</td>
                    <td>${m.fecha_fin ? formatFecha(m.fecha_fin) : '-'}</td>
                    <td>${badgeHtml}</td>
                    <td>
                        <div class="actions-container">
                            <button class="button-action view" data-id="${m.id_meta}" title="Ver detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill text-white" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg>
                            </button>
                            ${canEditOrDelete ? `
                                <button class="button-action edit" data-id="${m.id_meta}" data-bs-toggle="modal" data-bs-target="#modal-editMeta" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-wide text-white" viewBox="0 0 16 16">
                                        <path d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434zM8 12.997a4.998 4.998 0 1 1 0-9.995 4.998 4.998 0 0 1 0 9.996z"/>
                                    </svg>
                                </button>
                                ${m.estudiantes_formados == 0 ? `
                                <button class="button-action delete" data-id="${m.id_meta}" data-bs-toggle="modal" data-bs-target="#modal-deleteMeta" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill text-white" viewBox="0 0 16 16">
                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                    </svg>
                                </button>
                                ` : ''}
                            ` : ''}
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

    $(document).on('submit', 'form.search-bar', function (e) {
        e.preventDefault();
    });

    // Filtros
    $('#TipoDeMeta-filter, #estado-filter, #periodo-filter, #ambito-filter, #anio-filter').on('change', function () {
        const val = $(this).val();
        if ($(this).attr('id') === 'ambito-filter') {
            if (val === '' || val === 'Global') {
                filterAmbitoId = null;
                // Limpiar URL sin recargar
                const url = new URL(window.location);
                url.searchParams.delete('ambito_id');
                window.history.replaceState({}, '', url);
            } else {
                filterAmbitoId = val;
                // Actualizar URL sin recargar
                const url = new URL(window.location);
                url.searchParams.set('ambito_id', val);
                window.history.replaceState({}, '', url);
            }
        }
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

    // Abrir modal ver (cargar datos antes de mostrar para que funcionen acciones del footer)
    $(document).on('click', '.button-action.view', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.get('../../app/controlador/Metas/verMeta.php', { id }, function (res) {
            try { res = JSON.parse(res); } catch (err) {}
            if (res.success) {
                const m = res.data;
                if (typeof window.showMetaModal === 'function') {
                    window.showMetaModal(m);
                } else if (typeof window.loadMetaData === 'function') {
                    window._currentMetaData = m;
                    window.loadMetaData(m);
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-viewMeta')).show();
                } else {
                    toastr.error('No se pudo abrir el detalle de la meta');
                }
            } else {
                toastr.error(res.message || 'No se encontró la meta');
            }
        }).fail(function () {
            toastr.error('Error al cargar la meta');
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

    // Limpiar filtro de ámbito
    $(document).on('click', '#clear-ambito-filter', function() {
        filterAmbitoId = null;
        
        // Limpiar URL sin recargar
        const url = new URL(window.location);
        url.searchParams.delete('ambito_id');
        window.history.replaceState({}, '', url);
        
        // Resetear selector
        $('#ambito-filter').val('');
        
        // Ocultar aviso y recargar tabla
        $('#active-filter-alert').fadeOut();
        cargarMetas(1, recordsPerPage, currentSearch);
    });

    // Pre-seleccionar el filtro de ámbito si viene en la URL al cargar
    if (filterAmbitoId) {
        $('#ambito-filter').val(filterAmbitoId);
    }

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar
    cargarMetas(currentPage, recordsPerPage, currentSearch);
});
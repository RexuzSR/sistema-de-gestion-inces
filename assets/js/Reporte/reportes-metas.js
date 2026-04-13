// ========== REPORTE DE METAS ==========
// Archivo: assets/js/Reporte/reportes-metas.js
// Estilo visual unificado con el sistema de reportes INCES

// Variables globales para filtros
let currentFilterType = 'anual'; // 'trimestral' o 'anual'
let currentAnio = new Date().getFullYear();
let currentTrimestre = 1;
let rawMetasData = []; // Almacenar datos originales sin procesar

// Función principal que espera el sistema de reportes
function cargarMetaPorAmbito() {
    cargarReporteMetas();
}

// Función de inicialización para compatibilidad
function iniciarReporteMetas() {
    cargarReporteMetas();
}

async function cargarReporteMetas() {
    const contentDiv = document.getElementById('reportContent');
    if (!contentDiv) {
        console.error('No se encontró el contenedor reportContent');
        return;
    }
    
    // Mostrar loader con estilo moderno
    contentDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="rp-spinner mx-auto"></div>
            <p class="mt-3 text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
                Cargando datos de metas...
            </p>
        </div>
    `;
    
    const categoriasReportes = window.categoriasReportes || [];
    const currentCategoryId = window.currentCategoryId;
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId) || { color: 'primary' };

    try {
        const anioActual = new Date().getFullYear();
        currentAnio = anioActual;
        
        // Rutas posibles para la API
        const rutasPosibles = [
            '../../app/controlador/Reporte/api_reportes.php',
            '../../../app/controlador/Reporte/api_reportes.php',
            '/app/controlador/Reporte/api_reportes.php',
            'app/controlador/Reporte/api_reportes.php'
        ];
        
        let response = null;
        let rutaUsada = '';
        
        for (const ruta of rutasPosibles) {
            try {
                const testResponse = await fetch(`${ruta}?accion=metas_por_ambito&anio=${anioActual}`);
                if (testResponse.ok) {
                    response = testResponse;
                    rutaUsada = ruta;
                    break;
                }
            } catch (e) {
                // Continuar
            }
        }
        
        if (!response) {
            throw new Error('No se pudo conectar con la API');
        }
        
        const metasData = await response.json();
        rawMetasData = metasData; // Guardar datos originales
        
        // Aplicar filtro inicial
        const metasProcesadas = aplicarFiltroYTiempo(metasData);
        
        renderizarReporteMetas(contentDiv, metasProcesadas, categoria, anioActual);
        
    } catch (error) {
        console.error('Error al cargar metas:', error);
        contentDiv.innerHTML = `
            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error al cargar el reporte
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Error:</strong> ${error.message}
                    </div>
                    <button class="rp-btn rp-btn--ghost mt-3" onclick="reiniciarReporteMetas()">
                        <i class="bi bi-arrow-repeat me-1"></i>Reiniciar Reporte
                    </button>
                </div>
            </div>
        `;
    }
}

/**
 * Reinicia el reporte a sus valores por defecto
 */
function reiniciarReporteMetas() {
    currentFilterType = 'anual';
    currentAnio = new Date().getFullYear();
    currentTrimestre = 1;
    cargarReporteMetas();
}

/**
 * Verifica si una meta pertenece a un año específico
 * Considera metas que abarcan múltiples años (ej: 2025-2026)
 */
function metaPerteneceAnio(meta, anio) {
    // Obtener año de inicio y fin de la meta
    let anioInicio = null;
    let anioFin = null;
    
    // Intentar obtener año de inicio
    if (meta.anio) {
        anioInicio = parseInt(meta.anio);
        anioFin = parseInt(meta.anio);
    }
    
    if (meta.fecha_inicio) {
        const fechaInicio = new Date(meta.fecha_inicio);
        if (!isNaN(fechaInicio)) {
            anioInicio = fechaInicio.getFullYear();
        }
    }
    
    if (meta.fecha_fin) {
        const fechaFin = new Date(meta.fecha_fin);
        if (!isNaN(fechaFin)) {
            anioFin = fechaFin.getFullYear();
        }
    }
    
    // Si no hay fechas definidas, usar el año por defecto
    if (!anioInicio && !anioFin) {
        return anio === currentAnio;
    }
    
    // Si solo hay año de inicio
    if (anioInicio && !anioFin) {
        return anioInicio === anio;
    }
    
    // Si solo hay año de fin
    if (!anioInicio && anioFin) {
        return anioFin === anio;
    }
    
    // Si hay ambos años (inicio y fin), verificar si el año está en el rango
    if (anioInicio && anioFin) {
        return anio >= anioInicio && anio <= anioFin;
    }
    
    return false;
}

/**
 * Aplica filtro por tiempo (trimestral o anual)
 */
function aplicarFiltroYTiempo(metasData) {
    if (!Array.isArray(metasData) || metasData.length === 0) {
        return [];
    }
    
    if (currentFilterType === 'anual') {
        // Filtrar por año actual - incluye metas que abarcan múltiples años
        const metasFiltradas = metasData.filter(meta => {
            return metaPerteneceAnio(meta, currentAnio);
        });
        return procesarMetasData(metasFiltradas);
    } 
    else if (currentFilterType === 'trimestral') {
        // Filtrar por trimestre específico (1, 2, 3)
        const metasFiltradas = metasData.filter(meta => {
            // Primero verificar si la meta pertenece al año seleccionado
            if (!metaPerteneceAnio(meta, currentAnio)) {
                return false;
            }
            
            // Luego verificar el trimestre
            const trimestreMeta = obtenerTrimestre(meta);
            return trimestreMeta === currentTrimestre;
        });
        return procesarMetasData(metasFiltradas);
    }
    
    return procesarMetasData(metasData);
}

/**
 * Obtiene el trimestre de una meta basado en sus fechas
 * Para metas que abarcan múltiples trimestres/años, retorna el trimestre de inicio
 */
function obtenerTrimestre(meta) {
    let fecha = null;
    let fechaFin = null;
    
    // Intentar obtener fecha de inicio
    if (meta.fecha_inicio) {
        fecha = new Date(meta.fecha_inicio);
    } else if (meta.fecha_creacion) {
        fecha = new Date(meta.fecha_creacion);
    } else if (meta.periodo_tiempo) {
        // Intentar extraer trimestre del período
        const periodo = meta.periodo_tiempo.toLowerCase();
        if (periodo.includes('trimestre 1') || periodo.includes('1er trimestre') || periodo.includes('ene-mar')) return 1;
        if (periodo.includes('trimestre 2') || periodo.includes('2do trimestre') || periodo.includes('abr-jun')) return 2;
        if (periodo.includes('trimestre 3') || periodo.includes('3er trimestre') || periodo.includes('jul-sep')) return 3;
        // Ignorar 4to trimestre
        if (periodo.includes('trimestre 4') || periodo.includes('4to trimestre') || periodo.includes('oct-dic')) return null;
    }
    
    // Para metas que abarcan múltiples períodos, usar la fecha de inicio para determinar el trimestre
    if (fecha && !isNaN(fecha)) {
        const mes = fecha.getMonth() + 1;
        const trimestre = Math.ceil(mes / 3);
        // Solo retornar trimestres 1, 2, 3 (excluir 4)
        return trimestre === 4 ? null : trimestre;
    }
    
    // Si hay fecha de fin pero no de inicio, usar la fecha de fin
    if (meta.fecha_fin && !fecha) {
        fechaFin = new Date(meta.fecha_fin);
        if (fechaFin && !isNaN(fechaFin)) {
            const mes = fechaFin.getMonth() + 1;
            const trimestre = Math.ceil(mes / 3);
            return trimestre === 4 ? null : trimestre;
        }
    }
    
    return null; // Sin trimestre definido o es trimestre 4
}

/**
 * Actualiza el reporte al cambiar el filtro
 */
function actualizarReportePorFiltro() {
    const contentDiv = document.getElementById('reportContent');
    if (!contentDiv) return;
    
    const categoriasReportes = window.categoriasReportes || [];
    const currentCategoryId = window.currentCategoryId;
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId) || { color: 'primary' };
    
    const metasProcesadas = aplicarFiltroYTiempo(rawMetasData);
    
    // Verificar si no hay datos y mostrar opción de reinicio
    if (!metasProcesadas || metasProcesadas.length === 0) {
        contentDiv.innerHTML = `
            <div class="rp-empty">
                <div class="rp-empty-icon">
                    <i class="bi bi-flag display-4 text-muted"></i>
                </div>
                <p class="rp-empty-title">No hay metas registradas</p>
                <p class="rp-empty-desc">No se encontraron metas ${currentFilterType === 'anual' ? `para el año ${currentAnio}` : `para el Trimestre ${currentTrimestre} de ${currentAnio}`}</p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button class="rp-btn rp-btn--ghost" onclick="reiniciarReporteMetas()">
                        <i class="bi bi-arrow-repeat me-1"></i>Reiniciar Reporte
                    </button>
                    <button class="rp-btn rp-btn--ghost" onclick="cambiarFiltroTiempo('anual')">
                        <i class="bi bi-calendar-year me-1"></i>Ver Anual
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    renderizarReporteMetas(contentDiv, metasProcesadas, categoria, currentAnio);
}

/**
 * Renderiza el reporte de metas con estilo moderno
 */
function renderizarReporteMetas(contentDiv, metasProcesadas, categoria, anioActual) {
    // Calcular estadísticas generales
    const totalMetas = metasProcesadas.length;
    const metasCumplidas = metasProcesadas.filter(m => m.porcentaje_cumplimiento >= 100).length;
    const promedioCumplimiento = metasProcesadas.reduce((sum, m) => sum + m.porcentaje_cumplimiento, 0) / totalMetas;
    const totalObjetivo = metasProcesadas.reduce((sum, m) => sum + m.meta_objetivo, 0);
    const totalFormados = metasProcesadas.reduce((sum, m) => sum + m.estudiantes_formados, 0);
    
    // Agrupar por ámbito (para anual: suma de todas las metas del ámbito)
    const metasPorAmbito = {};
    metasProcesadas.forEach(meta => {
        if (!metasPorAmbito[meta.nombre_ambito]) {
            metasPorAmbito[meta.nombre_ambito] = {
                nombre: meta.nombre_ambito,
                color: meta.color,
                metas: [],
                totalObjetivo: 0,
                totalFormados: 0
            };
        }
        metasPorAmbito[meta.nombre_ambito].metas.push(meta);
        metasPorAmbito[meta.nombre_ambito].totalObjetivo += meta.meta_objetivo;
        metasPorAmbito[meta.nombre_ambito].totalFormados += meta.estudiantes_formados;
    });
    
    // Determinar color de acento basado en categoría
    const accentColor = getAccentColor(categoria.color);
    
    // Construir HTML con estilo moderno
    let html = `
        <div class="fade-in">
            <!-- Cabecera del reporte estilo moderno -->
            <div class="rp-report-header" style="background: linear-gradient(120deg, #0f1924 0%, #0c2340 55%, #0e2a4a 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rp-header-icon-wrap">
                        <i class="bi bi-flag-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="rp-header-title mb-0">Reporte de Metas por Ámbito</h5>
                        <p class="rp-header-subtitle mb-0">
                            ${currentFilterType === 'anual' ? `Año ${anioActual}` : `Trimestre ${currentTrimestre} · ${anioActual}`} · ${totalMetas} metas registradas
                        </p>
                    </div>
                </div>
                <button class="rp-btn rp-btn--ghost" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.2);" onclick="reiniciarReporteMetas()">
                    <i class="bi bi-arrow-repeat me-1"></i>Reiniciar
                </button>
            </div>
            
            <!-- Filtros de Tiempo -->
            <div class="rp-filter-bar mt-3">
                <div class="rp-filter-group">
                    <label class="rp-filter-label">Filtrar por:</label>
                    <div class="rp-filter-buttons">
                        <button class="rp-filter-btn ${currentFilterType === 'anual' ? 'rp-filter-btn--active' : ''}" 
                                onclick="cambiarFiltroTiempo('anual')">
                            <i class="bi bi-calendar-year me-1"></i>Anual
                        </button>
                        <button class="rp-filter-btn ${currentFilterType === 'trimestral' ? 'rp-filter-btn--active' : ''}" 
                                onclick="cambiarFiltroTiempo('trimestral')">
                            <i class="bi bi-calendar-range me-1"></i>Trimestral
                        </button>
                    </div>
                </div>
                
                ${currentFilterType === 'trimestral' ? `
                <div class="rp-filter-group">
                    <label class="rp-filter-label">Trimestre:</label>
                    <div class="rp-filter-buttons">
                        <button class="rp-filter-btn ${currentTrimestre === 1 ? 'rp-filter-btn--active' : ''}" 
                                onclick="cambiarTrimestre(1)">
                            <i class="bi bi-calendar-week me-1"></i>1er Trim
                        </button>
                        <button class="rp-filter-btn ${currentTrimestre === 2 ? 'rp-filter-btn--active' : ''}" 
                                onclick="cambiarTrimestre(2)">
                            <i class="bi bi-calendar-week me-1"></i>2do Trim
                        </button>
                        <button class="rp-filter-btn ${currentTrimestre === 3 ? 'rp-filter-btn--active' : ''}" 
                                onclick="cambiarTrimestre(3)">
                            <i class="bi bi-calendar-week me-1"></i>3er Trim
                        </button>
                    </div>
                </div>
                ` : ''}
                
                <div class="rp-filter-group">
                    <label class="rp-filter-label">Año:</label>
                    <select class="rp-filter-select" id="anioSelector" onchange="cambiarAnio(this.value)">
                        ${generarOpcionesAnios(anioActual)}
                    </select>
                </div>
            </div>
            
            <!-- KPIs Grid -->
            <div class="rp-kpi-grid mt-3">
                <div class="rp-kpi-card rp-kpi-card--primary" data-tipo="metas">
                    <div class="rp-kpi-header">
                        <div class="rp-kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-flag-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="rp-kpi-value">${totalMetas}</div>
                    <div class="rp-kpi-label">Metas Totales</div>
                    <div class="rp-kpi-sub">Registradas en el sistema</div>
                </div>
                
                <div class="rp-kpi-card rp-kpi-card--success" data-tipo="cumplidas">
                    <div class="rp-kpi-header">
                        <div class="rp-kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="rp-kpi-value">${metasCumplidas}</div>
                    <div class="rp-kpi-label">Metas Cumplidas</div>
                    <div class="rp-kpi-sub">${((metasCumplidas/totalMetas)*100).toFixed(1)}% del total</div>
                </div>
                
                <div class="rp-kpi-card rp-kpi-card--cyan" data-tipo="promedio">
                    <div class="rp-kpi-header">
                        <div class="rp-kpi-icon bg-cyan bg-opacity-10 text-cyan">
                            <i class="bi bi-graph-up fs-5"></i>
                        </div>
                    </div>
                    <div class="rp-kpi-value">${promedioCumplimiento.toFixed(1)}%</div>
                    <div class="rp-kpi-label">Promedio Cumplimiento</div>
                    <div class="rp-kpi-sub">General de todas las metas</div>
                    <div class="rp-kpi-bar-track mt-2">
                        <div class="rp-kpi-bar-fill bg-cyan" style="width: ${Math.min(promedioCumplimiento, 100)}%"></div>
                    </div>
                </div>
                
                <div class="rp-kpi-card rp-kpi-card--warning" data-tipo="formados">
                    <div class="rp-kpi-header">
                        <div class="rp-kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="rp-kpi-value">${totalFormados} / ${totalObjetivo}</div>
                    <div class="rp-kpi-label">Estudiantes Formados</div>
                    <div class="rp-kpi-sub">Meta objetivo general</div>
                    <div class="rp-kpi-bar-track mt-2">
                        <div class="rp-kpi-bar-fill bg-warning" style="width: ${totalObjetivo > 0 ? (totalFormados/totalObjetivo)*100 : 0}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Detalle por Ámbito -->
            <div class="rp-section-title mt-4">
                <span class="rp-section-label">Detalle por Ámbito</span>
                <span class="rp-section-line"></span>
            </div>
    `;
    
    // Renderizar cada ámbito como tarjeta expandible
    Object.values(metasPorAmbito).forEach((ambito, idx) => {
        const porcentajeAmbito = ambito.totalObjetivo > 0 
            ? (ambito.totalFormados / ambito.totalObjetivo) * 100 
            : 0;
        
        const cardColor = ambito.color || getColorByIndex(idx);
        const isExpanded = idx === 0; // Primero expandido por defecto
        
        html += `
            <div class="rp-detail-panel mb-3" data-color="${cardColor}" data-ambito="${escapeHtml(ambito.nombre)}">
                <div class="rp-detail-header" style="cursor: pointer;" onclick="toggleAmbitoDetail(this)">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rp-kpi-icon" style="background: rgba(var(--${cardColor}-rgb, 0.1)); width: 40px; height: 40px;">
                            <i class="bi bi-building fs-5 text-${cardColor}"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">${escapeHtml(ambito.nombre)}</h6>
                            <div class="rp-semaphore rp-semaphore--${getSemaphoreClass(porcentajeAmbito)} mt-1">
                                <span class="rp-semaphore-dot"></span>
                                <span class="small">${ambito.totalFormados} / ${ambito.totalObjetivo} estudiantes · ${porcentajeAmbito.toFixed(1)}% cumplido</span>
                            </div>
                        </div>
                    </div>
                    <div class="rp-detail-close">
                        <i class="bi bi-chevron-${isExpanded ? 'up' : 'down'} fs-6"></i>
                    </div>
                </div>
                <div class="rp-detail-body" style="display: ${isExpanded ? 'block' : 'none'};">
                    <div class="table-responsive">
                        <table class="rp-table">
                            <thead>
                                <tr>
                                    <th>Tipo de Meta</th>
                                    <th>Objetivo</th>
                                    <th>Formados</th>
                                    <th>Progreso</th>
                                    <th>Estado</th>
                                    <th>Periodo</th>
                                    <th>Vigencia</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        ambito.metas.forEach(meta => {
            const porcentaje = meta.porcentaje_cumplimiento;
            let barraColor = 'primary';
            if (porcentaje >= 100) barraColor = 'success';
            else if (porcentaje >= 75) barraColor = 'cyan';
            else if (porcentaje >= 50) barraColor = 'warning';
            else barraColor = 'danger';
            
            const estadoBadge = meta.estado === 'Activa' 
                ? '<span class="rp-badge rp-badge--ok">Activa</span>'
                : '<span class="rp-badge rp-badge--neutral">Cerrada</span>';
            
            const tipoBadge = meta.tipo_objetivo === 'Aprobados'
                ? '<span class="rp-badge rp-badge--info">Aprobados</span>'
                : '<span class="rp-badge rp-badge--cyan">Inscritos</span>';
            
            // Mostrar período de vigencia de la meta
            const vigencia = meta.vigencia || meta.periodo_tiempo || 'No definido';
            
            html += `
                <tr>
                    <td>${tipoBadge}</td>
                    <td class="rp-cell-num">${meta.meta_objetivo}</td>
                    <td class="rp-cell-num">${meta.estudiantes_formados}</td>
                    <td>
                        <div class="rp-inline-bar">
                            <div class="rp-inline-bar-track">
                                <div class="rp-inline-bar-fill bg-${barraColor}" style="width: ${Math.min(porcentaje, 100)}%"></div>
                            </div>
                            <span class="rp-inline-bar-pct">${porcentaje.toFixed(1)}%</span>
                        </div>
                    </td>
                    <td>${estadoBadge}</td>
                    <td><span class="rp-badge rp-badge--neutral">${escapeHtml(meta.periodo_tiempo || 'No definido')}</span></td>
                    <td><span class="rp-badge rp-badge--info">${escapeHtml(vigencia)}</span></td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>Progreso General del Ámbito</strong></td>
                                    <td colspan="5">
                                        <div class="rp-inline-bar">
                                            <div class="rp-inline-bar-track">
                                                <div class="rp-inline-bar-fill bg-${cardColor}" style="width: ${Math.min(porcentajeAmbito, 100)}%"></div>
                                            </div>
                                            <span class="rp-inline-bar-pct">${porcentajeAmbito.toFixed(1)}%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Pie de página (sin botones de exportar/imprimir)
    html += `
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="rp-tag-live">
                    <span>Actualizado</span>
                    <span>${new Date().toLocaleDateString('es-ES')}</span>
                </div>
            </div>
        </div>
    `;
    
    contentDiv.innerHTML = html;
    initTooltips(contentDiv);
}

/**
 * Genera opciones de años para el selector
 */
function generarOpcionesAnios(anioActual) {
    let options = '';
    for (let i = anioActual - 2; i <= anioActual + 2; i++) {
        options += `<option value="${i}" ${i === currentAnio ? 'selected' : ''}>${i}</option>`;
    }
    return options;
}

/**
 * Cambia el filtro de tiempo (anual/trimestral)
 */
function cambiarFiltroTiempo(tipo) {
    currentFilterType = tipo;
    if (tipo === 'anual') {
        currentTrimestre = 1;
    }
    actualizarReportePorFiltro();
}

/**
 * Cambia el trimestre seleccionado (solo 1, 2, 3)
 */
function cambiarTrimestre(trimestre) {
    if (trimestre >= 1 && trimestre <= 3) {
        currentTrimestre = trimestre;
        actualizarReportePorFiltro();
    }
}

/**
 * Cambia el año seleccionado
 */
function cambiarAnio(anio) {
    currentAnio = parseInt(anio);
    actualizarReportePorFiltro();
}

/**
 * Obtiene color de acento basado en categoría
 */
function getAccentColor(categoriaColor) {
    const colors = {
        primary: '#3b82f6',
        success: '#22c55e',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#0891b2',
        secondary: '#64748b'
    };
    return colors[categoriaColor] || colors.primary;
}

/**
 * Obtiene clase de semáforo según porcentaje
 */
function getSemaphoreClass(porcentaje) {
    if (porcentaje >= 90) return 'ok';
    if (porcentaje >= 60) return 'warn';
    return 'danger';
}

/**
 * Obtiene color por índice para ámbitos sin color asignado
 */
function getColorByIndex(index) {
    const colors = ['primary', 'success', 'cyan', 'warning', 'danger', 'secondary'];
    return colors[index % colors.length];
}

/**
 * Toggle para expandir/colapsar detalle de ámbito
 */
function toggleAmbitoDetail(element) {
    const panel = element.closest('.rp-detail-panel');
    const body = panel.querySelector('.rp-detail-body');
    const icon = element.querySelector('.rp-detail-close i');
    
    if (body.style.display === 'none') {
        body.style.display = 'block';
        icon.className = 'bi bi-chevron-up fs-6';
        panel.style.boxShadow = 'var(--rp-shadow-md)';
    } else {
        body.style.display = 'none';
        icon.className = 'bi bi-chevron-down fs-6';
        panel.style.boxShadow = 'var(--rp-shadow-sm)';
    }
}

/**
 * Inicializa tooltips
 */
function initTooltips(container) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }
}

/**
 * Procesa los datos de metas recibidos de la API
 */
function procesarMetasData(metasData) {
    if (!Array.isArray(metasData) || metasData.length === 0) {
        console.warn('No se recibieron datos de metas');
        return [];
    }
    
    const colores = ['primary', 'success', 'cyan', 'warning', 'danger', 'secondary'];
    const ambitoColorMap = {};
    let colorIndex = 0;
    
    return metasData
        .map(meta => {
            const metaObjetivo = parseInt(meta.meta_objetivo) || 0;
            const estudiantesFormados = parseInt(meta.estudiantes_formados) || 0;
            
            let porcentaje = 0;
            if (metaObjetivo > 0) {
                porcentaje = (estudiantesFormados / metaObjetivo) * 100;
            }
            
            let periodo = meta.periodo_tiempo;
            if (!periodo && meta.fecha_inicio && meta.fecha_fin) {
                const fechaInicio = new Date(meta.fecha_inicio);
                const fechaFin = new Date(meta.fecha_fin);
                if (!isNaN(fechaInicio) && !isNaN(fechaFin)) {
                    periodo = `${fechaInicio.toLocaleDateString('es-ES')} - ${fechaFin.toLocaleDateString('es-ES')}`;
                    // Crear vigencia para mostrar el rango de años
                    const anioInicio = fechaInicio.getFullYear();
                    const anioFin = fechaFin.getFullYear();
                    if (anioInicio !== anioFin) {
                        meta.vigencia = `${anioInicio} - ${anioFin}`;
                    } else {
                        meta.vigencia = `${anioInicio}`;
                    }
                }
            } else if (!periodo) {
                periodo = 'Periodo no definido';
                meta.vigencia = 'No definida';
            }
            
            // Si no se estableció vigencia, crearla desde las fechas disponibles
            if (!meta.vigencia) {
                let anioInicio = null;
                let anioFin = null;
                
                if (meta.fecha_inicio) {
                    const fechaInicio = new Date(meta.fecha_inicio);
                    if (!isNaN(fechaInicio)) anioInicio = fechaInicio.getFullYear();
                }
                if (meta.fecha_fin) {
                    const fechaFin = new Date(meta.fecha_fin);
                    if (!isNaN(fechaFin)) anioFin = fechaFin.getFullYear();
                }
                
                if (anioInicio && anioFin && anioInicio !== anioFin) {
                    meta.vigencia = `${anioInicio} - ${anioFin}`;
                } else if (anioInicio) {
                    meta.vigencia = `${anioInicio}`;
                } else if (anioFin) {
                    meta.vigencia = `${anioFin}`;
                } else if (meta.anio) {
                    meta.vigencia = `${meta.anio}`;
                } else {
                    meta.vigencia = 'No definida';
                }
            }
            
            const ambito = meta.nombre_ambito || 'Sin ámbito asignado';
            if (!ambitoColorMap[ambito]) {
                ambitoColorMap[ambito] = colores[colorIndex % colores.length];
                colorIndex++;
            }
            
            return {
                id: meta.id_meta || Math.random(),
                nombre_ambito: ambito,
                tipo_objetivo: meta.tipo_objetivo || 'Aprobados',
                meta_objetivo: metaObjetivo,
                estudiantes_formados: estudiantesFormados,
                porcentaje_cumplimiento: parseFloat(meta.porcentaje_cumplimiento) || porcentaje,
                estado: meta.estado || 'Activa',
                periodo_tiempo: periodo,
                vigencia: meta.vigencia,
                color: ambitoColorMap[ambito],
                fecha_inicio: meta.fecha_inicio,
                fecha_fin: meta.fecha_fin,
                anio: meta.anio
            };
        })
        .sort((a, b) => b.porcentaje_cumplimiento - a.porcentaje_cumplimiento);
}

/**
 * Escapa caracteres HTML
 */
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Exponer funciones globalmente
window.cargarMetaPorAmbito = cargarMetaPorAmbito;
window.cargarReporteMetas = cargarReporteMetas;
window.iniciarReporteMetas = iniciarReporteMetas;
window.reiniciarReporteMetas = reiniciarReporteMetas;
window.toggleAmbitoDetail = toggleAmbitoDetail;
window.cambiarFiltroTiempo = cambiarFiltroTiempo;
window.cambiarTrimestre = cambiarTrimestre;
window.cambiarAnio = cambiarAnio;
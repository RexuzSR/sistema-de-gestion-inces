// viewDocente.js
$(document).ready(function() {
    // Manejar el evento click del botón "Ver Docente"
    $(document).on('click', '.button.view', function(e) {
        e.preventDefault();
        
        const docenteId = $(this).data('id');
        console.log('Cargando docente ID:', docenteId);
        
        // Limpiar datos anteriores
        limpiarModalView();
        
        // Mostrar estado de carga
        mostrarEstadoCarga();
        
        // Obtener datos del docente via AJAX
        $.ajax({
            url: '../../app/controlador/Docente/obtenerDocente.php',
            method: 'GET',
            data: { id: docenteId },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success && response.data) {
                    const docente = response.data;
                    cargarDatosDocente(docente);
                    actualizarEnlacePDF(docenteId);
                } else {
                    mostrarError(response.message || 'Error al cargar los datos del docente');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                mostrarError('Error de conexión con el servidor');
            }
        });
    });
    
    // Limpiar el modal
    function limpiarModalView() {
        $('#nombre-completo-view').text('');
        $('#cedula-view').text('');
        $('#telefono-view').text('');
        $('#correo-view').text('');
        $('#fechaNacimiento-view').text('');
        $('#edad-view').text('');
        $('#genero-view').text('');
        $('#tipo-docente-view').text('');
        $('#estado-view').html('');
        $('#cursos-asociados').html('');
    }
    
    // Mostrar estado de carga
    function mostrarEstadoCarga() {
        $('#cursos-asociados').html(`
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <span>Cargando información del docente...</span>
                    </div>
                </div>
            </div>
        `);
    }
    
    // Mostrar error
    function mostrarError(mensaje) {
        toastr.error(mensaje);
        $('#cursos-asociados').html(`
            <div class="col-12">
                <div class="alert alert-danger mb-0">
                    <span>${mensaje}</span>
                </div>
            </div>
        `);
    }
    
    // Cargar datos del docente en el modal
    function cargarDatosDocente(docente) {
        // Construir nombre completo
        const nombreCompleto = construirNombreCompleto(
            docente.primer_nombre,
            docente.segundo_nombre,
            docente.primer_apellido,
            docente.segundo_apellido
        );
        
        // Llenar datos personales
        $('#nombre-completo-view').text(nombreCompleto);
        $('#cedula-view').text(docente.cedula || 'No especificado');
        $('#telefono-view').text(docente.telefono || 'No especificado');
        $('#correo-view').text(docente.correo || 'No especificado');
        $('#fechaNacimiento-view').text(formatearFecha(docente.fecha_nacimiento));
        $('#edad-view').text(docente.edad || 'No especificado');
        $('#genero-view').text(docente.genero || 'No especificado');
        $('#tipo-docente-view').text(docente.tipo_docente || 'No especificado');
        
        // Estado con badge
        const estadoHtml = obtenerBadgeEstado(docente.estado_docente || 'Disponible');
        $('#estado-view').html(estadoHtml);
        
        // Mostrar cursos asociados
        mostrarCursosAsociados(docente.cursos || []);
    }
    
    // Construir nombre completo
    function construirNombreCompleto(primerNombre, segundoNombre, primerApellido, segundoApellido) {
        let nombreCompleto = primerNombre || '';
        
        if (segundoNombre) {
            nombreCompleto += ' ' + segundoNombre;
        }
        
        if (primerApellido) {
            nombreCompleto += ' ' + primerApellido;
        }
        
        if (segundoApellido) {
            nombreCompleto += ' ' + segundoApellido;
        }
        
        return nombreCompleto.trim();
    }
    
    // Formatear fecha
    function formatearFecha(fecha) {
        if (!fecha) return 'No especificado';
        
        try {
            const fechaObj = new Date(fecha);
            return fechaObj.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return fecha;
        }
    }
    
    // Obtener badge de estado
    function obtenerBadgeEstado(estado) {
        const estados = {
            'Disponible': { class: 'bg-warning text-dark', text: 'Disponible' },
            'En formación': { class: 'bg-info text-white', text: 'En formación' },
            'Asignado': { class: 'bg-primary text-white', text: 'Asignado' },
            'Inactivo': { class: 'bg-secondary text-white', text: 'Inactivo' }
        };
        
        const estadoInfo = estados[estado] || { class: 'bg-light text-dark', text: estado };
        return `<span class="badge ${estadoInfo.class}">${estadoInfo.text}</span>`;
    }
    
    // Mostrar cursos asociados con estilo premium y funcionalidad de clic
    function mostrarCursosAsociados(cursos) {
        const container = $('#cursos-asociados');
        container.empty();
        
        if (!cursos || cursos.length === 0) {
            container.html(`
                <div class="col-12">
                    <div class="text-center py-4" style="background: #f8fafc; border-radius: 20px; border: 1px dashed #e2e8f0;">
                        <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; border: 2px dashed #94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </div>
                        <p style="color: #64748b;">No tiene cursos asociados</p>
                    </div>
                </div>
            `);
            return;
        }
        
        cursos.forEach(curso => {
            const idCurso = curso.id_curso || curso.id;
            const cardHtml = `
                <div class="col-md-6">
                    <div class="curso-card view-curso-card" data-id="${idCurso}" style="cursor: pointer;">
                        <div class="curso-titulo">${curso.nombre || curso.nombre_curso || 'Sin nombre'}</div>
                        <div class="curso-detalle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Horario: ${curso.horario || 'No especificado'}
                        </div>
                        <div class="curso-detalle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            ${formatearFecha(curso.fecha_inicio)} - ${formatearFecha(curso.fecha_fin)}
                        </div>
                        <div class="mt-2">
                            <span class="badge-status" style="${obtenerEstiloStatus(curso.estado)}">
                                ${curso.estado || 'Desconocido'}
                            </span>
                        </div>
                        ${curso.fecha_asignacion ? `
                            <div class="mt-2 pt-2 border-top" style="font-size: 0.75rem; color: #94a3b8;">
                                <i class="bi bi-calendar-check me-1"></i> Asignado el: ${formatearFecha(curso.fecha_asignacion)}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });
    }

    // Función auxiliar para los colores de estado de los cursos
    function obtenerEstiloStatus(estado) {
        const color = (estado === 'En formación' || estado === 'Activo' || estado === 'Culminado') ? '#10b981' : '#ef4444';
        const bg = (estado === 'En formación' || estado === 'Activo' || estado === 'Culminado') ? '#d1fae5' : '#fee2e2';
        
        if (estado === 'DIFERIDO' || estado === 'En espera') {
            return `background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; border: 1px solid #cbd5e1;`;
        }

        return `background: ${bg}; color: ${color}; padding: 4px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; border: 1px solid ${color};`;
    }
    
    // Obtener color del badge del curso
    function obtenerBadgeColorCurso(estado) {
        const colores = {
            'Culminado': 'bg-success',
            'En proceso': 'bg-primary',
            'En espera': 'bg-warning text-dark',
            'Aprobado': 'bg-info',
            'default': 'bg-secondary'
        };
        
        return colores[estado] || colores.default;
    }
    
    // Actualizar enlace del PDF
    function actualizarEnlacePDF(docenteId) {
        const pdfUrl = `../../app/controlador/fpdf/constanciaDocente.php?id=${docenteId}`;
        $('#btn-constanciaDocente-pdf').attr('href', pdfUrl);
    }
});
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
    
    // Mostrar cursos asociados
    function mostrarCursosAsociados(cursos) {
        const cursosContainer = $('#cursos-asociados');
        cursosContainer.empty();
        
        if (cursos.length > 0) {
            cursos.forEach((curso, index) => {
                const cursoCard = `
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-primary">${curso.nombre || curso.nombre_curso || 'Curso sin nombre'}</h6>
                                <div class="mb-2">
                                    <span class="badge ${obtenerBadgeColorCurso(curso.estado)}">
                                        ${curso.estado || 'Sin estado'}
                                    </span>
                                </div>
                                ${curso.fecha_asignacion ? `
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <strong>Asignado el:</strong> ${formatearFecha(curso.fecha_asignacion)}
                                        </small>
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                cursosContainer.append(cursoCard);
            });
        } else {
            cursosContainer.html(`
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                            <span>Este docente no tiene cursos asignados.</span>
                        </div>
                    </div>
                </div>
            `);
        }
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
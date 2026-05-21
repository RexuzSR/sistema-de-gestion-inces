document.addEventListener('DOMContentLoaded', function () {
  document.body.addEventListener('click', function (event) {
    // Buscar tarjeta del modal participante O botón de la tabla cursos
    const target = event.target.closest('.view-curso-card') || event.target.closest('.button.view-curso');

    if (target) {
      const cursoId = target.getAttribute('data-id');
      console.log('Click en tarjeta curso, ID:', cursoId); // Depuración

      if (cursoId) {
        // Cargar información del curso
        fetch(`../../app/controlador/Curso/verCurso.php?id=${cursoId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const curso = data.data;

              console.log('Datos del curso:', curso); // Depuración

              // Llenar los campos del modal con la información del curso
              const nombreView = document.getElementById('nombre-curso-view');
              if (nombreView) {
                nombreView.textContent = curso.nombre || 'Nombre no disponible';
              } else {
                console.error('Elemento #nombre-curso-view no encontrado en el DOM');
              }

              // Mostrar PFP completo
              const programaText = curso.programa_formacion === 'Programa de Formación Productiva (PFP)'
                ? 'Programa de Formación Productiva (PFP)'
                : '-';
              document.getElementById('programaFormación-view').textContent = programaText;

              document.getElementById('modalidad-view').textContent = curso.modalidad || '-';
              document.getElementById('turno-view').textContent = curso.turno || '-';
              document.getElementById('tipoFormacion-view').textContent = curso.tipoFormacion || '-';
              document.getElementById('areaFormativa-view').textContent = curso.areaFormativa || '-';
              document.getElementById('sectorEconomico-view').textContent = curso.sectorEconomico || '-';
              document.getElementById('ambito-view').textContent = curso.ambito || '-';
              document.getElementById('cupos-view').textContent = curso.maxParticipantes || '-';

              console.log('ID Ámbito recibido:', curso.id_ambito); // Depuración

              // Guardar id_ambito en el panel de progreso para filtrado en metas
              const panelProgreso = document.querySelector('.panel-progreso-cupos');
              if (panelProgreso) {
                panelProgreso.setAttribute('data-id-ambito', curso.id_ambito || '');
              }

              // Mostrar el estado actual del curso usando el módulo central
              const infoEstado = window.estadoCurso.getInfo(curso.estado || curso.id_estatus || curso.id_estatuscurso);
              document.getElementById('estadoCurso-view').textContent = infoEstado.texto;

              // Motivos de diferimiento
              const esDiferido = (curso.estado || '').toLowerCase() === 'diferido';
              const contenedorMotivos = document.getElementById('motivos-diferido-view');
              const motivoParticipantes = document.getElementById('motivo-sin-participantes');
              const motivoDocente = document.getElementById('motivo-sin-docente');

              if (contenedorMotivos && motivoParticipantes && motivoDocente) {
                // Resetear estado previo
                contenedorMotivos.classList.add('d-none');
                motivoParticipantes.classList.add('d-none');
                motivoDocente.classList.add('d-none');

                if (esDiferido) {
                  const inscritos = parseInt(curso.numInscritos) || 0;
                  const minParticipantes = parseInt(curso.minParticipantes) || 12;
                  const sinParticipantes = inscritos < minParticipantes;

                  if (sinParticipantes) motivoParticipantes.classList.remove('d-none');
                  // El motivo de docente se resolverá tras el fetch de obtenerDocente.php
                  contenedorMotivos._pendingDocente = true;
                  if (sinParticipantes) contenedorMotivos.classList.remove('d-none');
                }
              }

              // Actualizar data-id del botón de matrícula
              $('#btn-matricula-pdf, #btn-recorrido-curso').attr('data-id', cursoId);

              // Formatear fechas (sin sumar 1 día)
              const fechaInicio = curso.fechaInicio && curso.fechaInicio !== '-'
                ? new Date(curso.fechaInicio)
                : null;

              const fechaFin = curso.fechaCulminacion && curso.fechaCulminacion !== '-'
                ? new Date(curso.fechaCulminacion)
                : null;

              const fechaActual = new Date();

              // Mostrar fechas formateadas
              document.getElementById('fechaInicio-view').textContent = fechaInicio
                ? formatDate(fechaInicio)
                : '-';

              document.getElementById('fechaCulminación-view').textContent = fechaFin
                ? formatDate(fechaFin)
                : '-';

              // Badge de estado y control de progreso según info
              const estadoBadge = document.getElementById('estadoCurso-view');
              estadoBadge.className = `badge ${infoEstado.badgeClass}`;
              const mostrarBarraProgreso = infoEstado.mostrarBarraProgreso;

              // Obtener datos del docente asociado al curso
              fetch(`../../app/controlador/Curso/obtenerDocente.php?id=${cursoId}`)
                .then(response => response.json())
                .then(docenteData => {
                  const defaultText = 'No asignado';
                  const contenedorDocente = document.getElementById('contenedor-docente-view');

                  // Guardar el id del curso en el contenedor para la asignación si es necesario
                  if (contenedorDocente) {
                    contenedorDocente.setAttribute('data-curso-id', cursoId);
                  }

                  // Completar motivos de diferimiento con resultado del docente
                  if (esDiferido && contenedorMotivos) {
                    const sinDocente = !docenteData.success;
                    if (sinDocente) {
                      motivoDocente.classList.remove('d-none');
                      contenedorMotivos.classList.remove('d-none');
                    }
                  }
                  
                  if (docenteData.success) {
                    const d = docenteData.docente;
                    document.getElementById('docente-nombre').textContent = d.nombre || defaultText;
                    document.getElementById('docente-cedula').textContent = d.cedula || defaultText;
                    document.getElementById('docente-edad').textContent = d.edad || defaultText;

                    if (contenedorDocente) {
                      contenedorDocente.setAttribute('data-docente-id', d.id_docente || '');
                      contenedorDocente.setAttribute('data-docente-cedula', d.cedula || '');
                      // Cambiamos el hover para indicar que se verá el perfil
                      contenedorDocente.title = "Ver perfil del docente";
                    }
                  } else {
                    document.getElementById('docente-nombre').textContent = defaultText;
                    document.getElementById('docente-cedula').textContent = defaultText;
                    document.getElementById('docente-edad').textContent = defaultText;

                    if (contenedorDocente) {
                      contenedorDocente.removeAttribute('data-docente-id');
                      contenedorDocente.removeAttribute('data-docente-cedula');
                      // Cambiamos el hover para indicar que se asignará uno nuevo
                      contenedorDocente.title = "Asignar docente al curso";
                    }
                    console.warn(docenteData.message);
                  }
                })
                .catch(err => {
                  console.error('Error obteniendo docente:', err);
                  document.getElementById('docente-nombre').textContent = 'No asignado';
                  document.getElementById('docente-cedula').textContent = 'No asignado';
                  document.getElementById('docente-edad').textContent = 'No asignado';

                  const contenedorDocente = document.getElementById('contenedor-docente-view');
                  if (contenedorDocente) {
                    contenedorDocente.removeAttribute('data-docente-id');
                    contenedorDocente.removeAttribute('data-docente-cedula');
                    contenedorDocente.title = "Asignar docente al curso";
                    contenedorDocente.setAttribute('data-curso-id', cursoId);
                  }
                });

              // Círculo de cupo
              const max = parseInt(curso.maxParticipantes) || 0;
              const inscritos = parseInt(curso.numInscritos) || 0;
              const porcentajeCupos = max ? (inscritos / max) * 100 : 0;

              const radius = 45;
              const circumference = 2 * Math.PI * radius;
              const progressCircle = document.getElementById('progress-circle');
              const circleText = document.getElementById('circle-text');

              // Actualizar círculo de progreso
              if (progressCircle) {
                progressCircle.style.strokeDasharray = `${(porcentajeCupos / 100) * circumference} ${circumference}`;
              }

              if (circleText) {
                circleText.textContent = `${Math.round(porcentajeCupos)}%`;
              }

              // Actualizar contadores de participantes
              if (document.getElementById('contador-participantes')) document.getElementById('contador-participantes').textContent = inscritos;
              if (document.getElementById('contador-participantes-tabla')) document.getElementById('contador-participantes-tabla').textContent = inscritos;

              // Mostrar u ocultar progreso temporal
              const progressBar = document.getElementById('progress-bar');
              const temporalProgress = document.querySelector('.temporal-progress');
              const progressCircleWrapper = document.querySelector('.progress-circle');

              if (mostrarBarraProgreso && fechaInicio && fechaFin) {
                const total = fechaFin - fechaInicio;
                const transcurrido = fechaActual - fechaInicio;
                const porcentaje = (transcurrido / total) * 100;

                if (progressBar) {
                  progressBar.style.width = `${Math.min(100, Math.max(0, porcentaje))}%`;
                }

                if (temporalProgress) {
                  temporalProgress.style.display = 'block';
                }

                if (progressCircleWrapper) {
                  progressCircleWrapper.style.display = 'block';
                }
              } else {
                if (progressBar) {
                  progressBar.style.width = '0%';
                }

                if (temporalProgress) {
                  temporalProgress.style.display = 'none';
                }

                if (progressCircleWrapper) {
                  progressCircleWrapper.style.display = 'block'; // Mostrar siempre el círculo
                }
              }

              // Cargar los participantes
              cargarParticipantesCurso(cursoId); // Renombrado para evitar conflicto

              // CERRAR MODAL DOCENTE SI ESTÁ ABIERTO (para evitar solapamiento)
              const modalDocente = $('#modal-viewDocente');
              if (modalDocente.length && modalDocente.hasClass('show')) {
                modalDocente.modal('hide');
                // Pequeña espera para que finalice la animación de cierre antes de abrir el otro
                setTimeout(() => {
                  $('#modal-viewCurso').modal('show');
                }, 400);
              } else {
                $('#modal-viewCurso').modal('show');
              }

            } else {
              console.error('Error en datos:', data.message);
              toastr.error('Error al cargar información del curso');
            }
          })
          .catch(error => {
            console.error('Error en solicitud:', error);
            toastr.error('Error de conexión');
          });
      }
    }
  });

  // Función para cargar los participantes del curso
  function cargarParticipantesCurso(idCurso) {
    console.log('Intentando cargar participantes para curso ID:', idCurso);
    const $tablaParticipantes = $('#tabla-participantes-curso');
    console.log('Elemento tabla encontrado:', $tablaParticipantes.length > 0 ? 'SÍ' : 'NO');
    $tablaParticipantes.empty();

    $.ajax({
      url: '../../app/controlador/Curso/obtenerInscritos.php',
      method: 'GET',
      dataType: 'json',
      data: { id_curso: idCurso },
      success: function (response) {
        console.log('Respuesta de obtenerInscritos.php:', response);
        // Manejar varios formatos: array directo, o { success: true, data: [...] }, o { success:false }
        if (response && response.success === false) {
          console.warn('Endpoint devolvió error:', response.error || response.message);
          $tablaParticipantes.html(`<tr><td colspan="5" class="text-warning text-center">${response.error || response.message || 'No se pudo obtener inscritos'}</td></tr>`);
          return;
        }

        let participantes = [];
        if (Array.isArray(response)) participantes = response;
        else if (response && Array.isArray(response.data)) participantes = response.data;

        if (participantes.length > 0) {
          participantes.forEach(p => {
            const ced = p.cedula || 'N/A';
            const nombre = p.nombre || p.nombre_completo || 'N/A';
            const apellido = p.apellido || '';
            const edad = p.edad || 'N/A';
            const genero = p.genero || 'N/A';

            const sexoClass = genero === 'F' ? 'badge-femenino' : 'badge-masculino';
            const sexoText = genero === 'F' ? 'Femenino' : 'Masculino';

            $tablaParticipantes.append(`
                <tr>
                  <td style="padding: 12px; color: #1e293b;">${ced}</td>
                  <td style="padding: 12px; color: #1e293b;">${nombre}</td>
                  <td style="padding: 12px; color: #1e293b;">${apellido}</td>
                  <td style="padding: 12px; color: #1e293b;">${edad}</td>
                  <td style="padding: 12px;">
                    <span class="${sexoClass}">${sexoText}</span>
                  </td>
                </tr>
              `);
          });
        } else {
          $tablaParticipantes.append('<tr><td colspan="5" class="text-muted text-center">No hay participantes inscritos</td></tr>');
        }
      },

      error: function (xhr, status, error) {
        console.error('Error cargando participantes:', {
          status: xhr.status,
          statusText: xhr.statusText,
          responseText: xhr.responseText,
          error: error
        });
        $tablaParticipantes.html(`<tr><td colspan="5" class="text-danger text-center">Error cargando inscritos (${xhr.status} ${xhr.statusText})</td></tr>`);
      }
    });
  }

  // Función para formatear las fechas
  function formatDate(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }
});


// ========== MANEJO CLIC PANEL PROGRESO A METAS ==========
$(document).on('click', '.panel-progreso-cupos', function() {
  const ambitoId = $(this).attr('data-id-ambito');
  if (ambitoId) {
    window.location.href = `metas.php?ambito_id=${ambitoId}`;
  } else {
    // Fallback si no tiene ámbito (meta global)
    window.location.href = `metas.php`;
  }
});

// ========== MANEJO CLIC CONTENEDOR DOCENTE ==========
function manejarClicDocente() {
  const contenedor = document.getElementById('contenedor-docente-view');
  if (!contenedor) return;

  const docenteId = contenedor.getAttribute('data-docente-id');
  const docenteCedula = contenedor.getAttribute('data-docente-cedula');
  const cursoId = contenedor.getAttribute('data-curso-id');

  // Cerrar el modal actual de detalle de curso para abrir el otro
  // O podemos mantenerlo abierto atrás, usémoslo dependiendo de la estructura
  // de BootStrap. Si abrimos otro modal encima suele funcionar bien.

  if (docenteId) {
    // Si hay docente, mostramos su perfil
    // Cerrar el modal actual de detalle del curso
    $('#modal-viewCurso').modal('hide');

    // Necesitamos que el modal-viewDocente cargue su info, 
    // comúnmente en el sistema se lanza abriendo el modal e inyectando con ajax
    // Simulamos que estamos en la tabla de Docentes y abrimos la vista
    setTimeout(() => {
      // Asumiendo que existe un modal-viewDocente
      const modalVerDocente = $('#modal-viewDocente');
      if (modalVerDocente.length) {
        // Normalmente Docentes usa un fetch similar a Curso para llenarlo.
        // Para asegurar la transición, simplemente tratamos de abrir y llenar si es posible:
        fetch(`../../app/controlador/Docente/verDocente.php?id=${docenteId}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              const dt = data.docente;

              // Ajustar los IDs según los que se usan en verDocente.php/.js 
              // Estos suelen ser los estándares en el sistema:
              if (document.getElementById('nombres')) document.getElementById('nombres').textContent = dt.nombre;
              if (document.getElementById('docente-nombres')) document.getElementById('docente-nombres').textContent = dt.nombre;
              if (document.getElementById('apellidos')) document.getElementById('apellidos').textContent = dt.apellido;
              if (document.getElementById('cedula')) document.getElementById('cedula').textContent = dt.cedula;
              if (document.getElementById('docente-cedula2')) document.getElementById('docente-cedula2').textContent = dt.cedula;
              // Puedes verificar qué campos usa exactamente modal-viewDocente del HTML

              modalVerDocente.modal('show');
            } else {
              toastr.error('Error al cargar datos del docente');
            }
          }).catch(err => {
            toastr.error('Solo disponible en sección de Docentes');
            // Redirigir a ventana docente como fallback
            // USAMOS CÉDULA EN LUGAR DE ID PARA EL BUSCADOR
            window.location.href = `docente.php?buscar=${docenteCedula || docenteId}`;
          });
      } else {
        // Si el modal no está importado en cursos.php, saltaremos allá directamente:
        // USAMOS CÉDULA EN LUGAR DE ID PARA EL BUSCADOR
        window.location.href = `docente.php?buscar=${docenteCedula || docenteId}`;
      }
    }, 400);
  } else if (cursoId) {
    // Si no hay docente, abrimos el modal de asignar docente
    // Ocultar modal actual (opcional)
    // $('#modal-viewCurso').modal('hide');

    // Necesitamos pasarle el ID del curso al modal de asignar docente
    // Verificamos si existe el botón que normalmente usaríamos
    const btnAsignar = document.querySelector(`button.assign-docente[data-id="${cursoId}"]`);
    if (btnAsignar) {
      btnAsignar.click();
    } else {
      // Ejecutamos manualmente la apertura del modal de Docentes
      $('#modal-viewCurso').modal('hide');
      setTimeout(() => {
        const asignarModal = $('#modal-AsignarDocenteCurso');
        if (asignarModal.length) {
          // Guardamos el id del curso en el modal Asignar
          asignarModal.find('#cursoIdHidden').val(cursoId);

          // Actualizamos el nombre en el modal si existe el elemento
          const nombreCurso = document.getElementById('nombre-curso-view')?.innerText || '-';
          asignarModal.find('#asignar-nombre-curso').text(nombreCurso);

          const modalidadCurso = document.getElementById('modalidad-view')?.innerText || '-';
          asignarModal.find('#asignar-modalidad').text(modalidadCurso);

          const areaCurso = document.getElementById('areaFormativa-view')?.innerText || '-';
          asignarModal.find('#asignar-area-formativa').text(areaCurso);

          if (typeof initTablaDocentesParaAsignar === 'function') {
            initTablaDocentesParaAsignar(cursoId);
          }

          asignarModal.modal('show');
        }
      }, 400); // Dar tiempo a que finalice la animación de cierre del primer modal
    }
  }
}

// ========== VER PERFIL DOCENTE (Boton específico) ==========
function verPerfilDocente() {
  // Simplemente llamamos a manejarClicDocente ya que tiene la lógica de redirección/modal
  manejarClicDocente();
}

// ========== INICIALIZAR Y ASIGNAR DOCENTE A CURSO DESDE el modal de ver curso ==========
window.initTablaDocentesParaAsignar = function (cursoId) {
  const selectDocente = $('#asignarDocente');
  selectDocente.empty().append('<option value="" disabled selected>Cargando docentes...</option>');
  $('#info-docente-seleccionado').addClass('d-none');

  // Obtener docentes disponibles
  $.ajax({
    url: '../../app/controlador/Docente/obtenerDocentes.php',
    method: 'GET',
    // Podemos traer gran cantidad, y si aplica, filtrarlo a estado "Disponible" u omitirlo
    data: { per_page: 1000, curso_id: cursoId },
    dataType: 'json',
    success: function (response) {
      selectDocente.empty().append('<option value="" disabled selected>Seleccione un docente</option>');

      if (response.success && response.data && response.data.length > 0) {
        window.docentesDisponiblesCache = response.data;
        response.data.forEach(function (docente) {
          const estadoText = docente.estado || '';
          if (estadoText !== 'Inactivo') {
            // Opcional: Mostrar insignia si está asignado o en formación
            const extraInfo = estadoText !== 'Disponible' ? ` (${estadoText})` : '';
            selectDocente.append(`<option value="${docente.id_docente}">${docente.cedula} - ${docente.nombre} ${docente.apellido}${extraInfo}</option>`);
          }
        });
      } else {
        selectDocente.empty().append('<option value="" disabled selected>No hay docentes disponibles</option>');
      }
    },
    error: function (xhr) {
      console.error('Error cargando docentes:', xhr);
      selectDocente.empty().append('<option value="" disabled selected>Error al cargar docentes</option>');
      toastr.error('Ocurrió un error al obtener la lista de docentes.');
    }
  });

  // Agregar evento change para mostrar detalles del docente seleccionado
  $('#asignarDocente').off('change').on('change', function() {
    const selectedId = $(this).val();
    const infoContainer = $('#info-docente-seleccionado');
    if (!selectedId) {
        infoContainer.addClass('d-none');
        return;
    }
    
    if (window.docentesDisponiblesCache) {
        const docente = window.docentesDisponiblesCache.find(d => d.id_docente == selectedId);
        if (docente) {
            $('#info-docente-cedula').text(docente.cedula || 'N/A');
            $('#info-docente-edad').text(docente.edad || 'N/A');
            $('#info-docente-correo').text(docente.correo || 'N/A');
            infoContainer.removeClass('d-none');
        } else {
            infoContainer.addClass('d-none');
        }
    }
  });
};

$(document).on('submit', '.formAsignarDocente', function (e) {
  e.preventDefault();

  // El id del curso está oculto en #id_curso_asignar_docente en asignar modal DOCENTES, 
  // PERO nuestro HTML actual tiene id="cursoIdHidden", verifiquemos esto.
  let cursoId = $('#cursoIdHidden').val() || $('#id_curso_asignar_docente').val(); // fallback 
  const docenteId = $('#asignarDocente').val();

  // Tratamiento especial: La lógica que escribimos arriba guarda el id en el modal manual 
  // si usamos $('#id_curso_asignar_docente'). Para consistencia en curso.php, extraemos de ambos.
  // O usar data atributo nativo. Si está vacío, buscamos el activo del view.
  if (!cursoId) {
    cursoId = document.getElementById('contenedor-docente-view')?.getAttribute('data-curso-id');
  }

  if (!docenteId || !cursoId) {
    toastr.warning('Por favor seleccione un docente válido');
    return;
  }

  const submitBtn = $(this).find('button[type="submit"]');
  const oldText = submitBtn.text();
  submitBtn.prop('disabled', true).text('Asignando...');

  $.ajax({
    url: '../../app/controlador/Curso/asignarDocenteCurso.php',
    method: 'POST',
    data: {
      curso_id: cursoId,
      docente_id: docenteId
    },
    dataType: 'json',
    success: function (res) {
      submitBtn.prop('disabled', false).text(oldText);

      if (res.success || res.status === 'success') {
        toastr.success(res.message || 'Docente asignado correctamente.');
        $('#modal-AsignarDocenteCurso').modal('hide');

        // Refrescar tabla si es necesario
        if (typeof initTabla === 'function') {
          // No está en este scope, pero usamos trigger
          $(document).trigger('cursos:actualizados');
        } else if (typeof cargarTablaCursos === 'function') {
          cargarTablaCursos();
        } else {
          location.reload();
        }
      } else {
        toastr.error(res.message || 'Error al asignar docente.');
      }
    },
    error: function (xhr) {
      submitBtn.prop('disabled', false).text(oldText);
      console.error('Error en asignación', xhr);
      toastr.error('Error de comunicación con el servidor.');
    }
  });
});

// ========== EVENTO PARA DESCARGAR MATRÍCULA PDF ==========
$(document).on('click', '#btn-matricula-pdf', function (e) {
  e.preventDefault();
  const cursoId = $(this).data('id');

  if (cursoId) {
    // Ruta corregida para PDF
    window.open(
      `../../app/controlador/fpdf/CursoMatricula.php?id=${cursoId}`,
      '_blank',
      'noopener,noreferrer'
    );
  } else {
    console.error('ID de curso no encontrado:', cursoId);
    toastr.error('Error: No se pudo identificar el curso');
  }
});
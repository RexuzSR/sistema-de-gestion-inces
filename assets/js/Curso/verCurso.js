// Variables globales para las gráficas del modal
var modalGeneroChartInst = modalGeneroChartInst || null;
var modalEdadChartInst = modalEdadChartInst || null;
var modalDiscapacidadChartInst = modalDiscapacidadChartInst || null;
var modalRendimientoChartInst = modalRendimientoChartInst || null;
var cachedStats = cachedStats || null;

document.addEventListener('DOMContentLoaded', function () {

  document.body.addEventListener('click', function (event) {
    // Buscar tarjeta del modal participante O botón de la tabla cursos
    const target = event.target.closest('.view-curso-card') || event.target.closest('.button.view-curso');

    if (target) {
      const cursoId = target.getAttribute('data-id');
      console.log('Click en tarjeta curso, ID:', cursoId); // Depuración

      if (cursoId) {
        // Reiniciar pestaña activa a Información del Curso (de forma segura usando clases)
        $('#courseModalTab button').removeClass('active');
        $('#courseModalTab button#info-tab').addClass('active');
        $('#courseModalTabContent .tab-pane').removeClass('show active');
        $('#info-tab-pane').addClass('show active');
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

              document.getElementById('preimpreso-view').textContent = curso.preimpreso || '-';
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
                    const docCedNum = parseInt(d.cedula);
                    const docPref = (!isNaN(docCedNum) && docCedNum >= 80000000) ? 'E-' : 'V-';
                    document.getElementById('docente-cedula').textContent = d.cedula ? (docPref + d.cedula) : defaultText;
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
              const statusId = parseInt(curso.id_estatus) || 0;
              cargarParticipantesCurso(cursoId, statusId); // Renombrado para evitar conflicto

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
  function cargarParticipantesCurso(idCurso, idEstatus = null) {
    console.log('Intentando cargar participantes para curso ID:', idCurso, 'Estatus:', idEstatus);
    // Limpiar cualquier filtro previo al cargar un curso
    if (typeof window.limpiarFiltroParticipantes === 'function') {
      window.limpiarFiltroParticipantes();
    }
    if (typeof window.limpiarFiltroParticipantesTab2 === 'function') {
      window.limpiarFiltroParticipantesTab2();
    }
    const $tablaParticipantes = $('#tabla-participantes-curso');
    const $headerRow = $tablaParticipantes.closest('table').find('thead tr');
    const colCount = (idEstatus === 5) ? 6 : 5;
    
    // Renderizar cabecera dinámica según si está Culminado
    if (idEstatus === 5) {
        $headerRow.html(`
            <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 12px 0 0 12px;">Cédula</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Nombre</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Apellido</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Edad</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Sexo</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 0 12px 12px 0;">Calificación</th>
        `);
    } else {
        $headerRow.html(`
            <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 12px 0 0 12px;">Cédula</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Nombre</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Apellido</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px;">Edad</th>
            <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 0 12px 12px 0;">Sexo</th>
        `);
    }

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
          $tablaParticipantes.html(`<tr><td colspan="${colCount}" class="text-warning text-center">${response.error || response.message || 'No se pudo obtener inscritos'}</td></tr>`);
          return;
        }
 
        let participantes = [];
        if (Array.isArray(response)) participantes = response;
        else if (response && Array.isArray(response.data)) participantes = response.data;
 
        // Contadores para las estadísticas de las donas
        let mascCount = 0;
        let femCount = 0;
        let bajo18 = 0;
        let de18a29 = 0;
        let mas30 = 0;
        let conDisc = 0;
        let sinDisc = 0;
        let discapacidadesLista = {};
        let edadSuma = 0;
        let edadConDatos = 0;

        // Contadores para rendimiento académico (calificaciones)
        let aprobados = 0;
        let reprobados = 0;
        let ausentes = 0;
 
        if (participantes.length > 0) {
          participantes.forEach(p => {
            const cedNum = parseInt(p.cedula);
            const pref = (!isNaN(cedNum) && cedNum >= 80000000) ? 'E-' : 'V-';
            const ced = p.cedula ? (pref + p.cedula) : 'N/A';
            const nombre = p.nombre || p.nombre_completo || 'N/A';
            const apellido = p.apellido || '';
            const edad = p.edad || 'N/A';
            const genero = p.genero || 'N/A';
 
            // Incrementar género
            const genStr = String(genero).toUpperCase();
            if (genStr.startsWith('M') || genStr === 'MASCULINO') mascCount++;
            else if (genStr.startsWith('F') || genStr === 'FEMENINO') femCount++;
 
            // Incrementar rango de edad
            const edadInt = parseInt(edad);
            if (!isNaN(edadInt)) {
              edadSuma += edadInt;
              edadConDatos++;
              if (edadInt < 18) bajo18++;
              else if (edadInt <= 29) de18a29++;
              else mas30++;
            }
 
            // Incrementar discapacidad
            const discId = parseInt(p.id_discapacidad);
            const discNombre = p.nombre_discapacidad || 'Ninguna';
            if (!isNaN(discId) && discId > 1) {
              conDisc++;
              discapacidadesLista[discNombre] = (discapacidadesLista[discNombre] || 0) + 1;
            } else {
              sinDisc++;
            }

            // Incrementar rendimiento académico
            const est = p.estatus_participante || '';
            if (est === 'Aprobado') aprobados++;
            else if (est === 'Reprobado') reprobados++;
            else if (est === 'Ausente') ausentes++;
 
            const esFemenino = genStr.startsWith('F') || genStr === 'FEMENINO';
            const sexoClass = esFemenino ? 'badge-femenino' : 'badge-masculino';
            const sexoText = esFemenino ? 'Femenino' : 'Masculino';

            // Celda de calificación si el curso está culminado
            let califTd = '';
            if (idEstatus === 5) {
                const calif = p.estatus_participante || '-';
                let badgeCalifClass = 'badge-ausente';
                if (calif === 'Aprobado') badgeCalifClass = 'badge-aprobado';
                else if (calif === 'Reprobado') badgeCalifClass = 'badge-reprobado';
                else if (calif === 'Ausente') badgeCalifClass = 'badge-ausente';
                else if (calif === 'Asignado') badgeCalifClass = 'badge-asignado';
                else if (calif === 'En formación') badgeCalifClass = 'badge-formacion';
                
                califTd = `<td style="padding: 12px;"><span class="${badgeCalifClass}">${calif}</span></td>`;
            }
 
            $tablaParticipantes.append(`
                <tr class="ver-participante-row" data-id="${p.id_participante}" data-genero="${sexoText}" data-edad="${edad}" data-discapacidad="${discId > 1 ? 'si' : 'no'}" data-rendimiento="${p.estatus_participante || ''}" title="Haga clic para ver detalles del participante" style="cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor=''">
                  <td style="padding: 12px; color: #1e293b;">${ced}</td>
                  <td style="padding: 12px; color: #1e293b;">${nombre}</td>
                  <td style="padding: 12px; color: #1e293b;">${apellido}</td>
                  <td style="padding: 12px; color: #1e293b;">${edad}</td>
                  <td style="padding: 12px;">
                    <span class="${sexoClass}">${sexoText}</span>
                  </td>
                  ${califTd}
                </tr>
              `);
          });
        } else {
          $tablaParticipantes.append(`<tr><td colspan="${colCount}" class="text-muted text-center">No hay participantes inscritos</td></tr>`);
        }
 
        const edadPromedio = edadConDatos > 0 ? Math.round(edadSuma / edadConDatos) : '-';
 
        // Guardar las estadísticas para cuando la pestaña se haga visible
        cachedStats = { 
          masc: mascCount, 
          fem: femCount, 
          bajo18, 
          de18a29, 
          mas30, 
          conDisc, 
          sinDisc,
          total: participantes.length,
          edadPromedio,
          discapacidadesLista,
          aprobados,
          reprobados,
          ausentes,
          idEstatus
        };
 
        // Si la pestaña ya estuviera activa, renderizar de inmediato
        if ($('#participants-tab-pane').hasClass('active')) {
          renderModalCharts(mascCount, femCount, bajo18, de18a29, mas30, conDisc, sinDisc, participantes.length, edadPromedio, discapacidadesLista, aprobados, reprobados, ausentes, idEstatus);
        }
      },
 
      error: function (xhr, status, error) {
        console.error('Error cargando participantes:', {
          status: xhr.status,
          statusText: xhr.statusText,
          responseText: xhr.responseText,
          error: error
        });
        $tablaParticipantes.html(`<tr><td colspan="${colCount}" class="text-danger text-center">Error cargando inscritos (${xhr.status} ${xhr.statusText})</td></tr>`);
      }
    });
  }

  // Función para renderizar los gráficos de donas
  function renderModalCharts(
      masc, fem, bajo18, de18a29, mas30, conDisc, sinDisc, total = 0, edadPromedio = '-', discapacidadesLista = {},
      aprobados = 0, reprobados = 0, ausentes = 0, idEstatus = null
  ) {
    try {
      if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está cargado. Reintentando renderizar gráficas en 200ms...');
        setTimeout(() => renderModalCharts(masc, fem, bajo18, de18a29, mas30, conDisc, sinDisc, total, edadPromedio, discapacidadesLista, aprobados, reprobados, ausentes, idEstatus), 200);
        return;
      }

      // Ajustar diseño de columnas según estatus del curso (Culminado = 5)
      if (idEstatus === 5) {
          $('#col-genero-chart, #col-edad-chart, #col-discapacidad-chart, #card-rendimiento-academico')
              .removeClass('col-md-4')
              .addClass('col-md-3');
          $('#card-rendimiento-academico').removeClass('d-none');
      } else {
          $('#col-genero-chart, #col-edad-chart, #col-discapacidad-chart')
              .removeClass('col-md-3')
              .addClass('col-md-4');
          $('#card-rendimiento-academico').addClass('d-none');
      }

      const totalGen = masc + fem || 1;
      const totalEdad = bajo18 + de18a29 + mas30 || 1;
      const totalDisc = conDisc + sinDisc || 1;

      // Actualizar textos de diagnóstico
      const gText = document.getElementById('debug-genero-text');
      if (gText) gText.textContent = `(Masc: ${masc} / Fem: ${fem})`;
      const eText = document.getElementById('debug-edad-text');
      if (eText) eText.textContent = `(<18: ${bajo18} / 18-29: ${de18a29} / 30+: ${mas30})`;
      const dText = document.getElementById('debug-disc-text');
      if (dText) {
        const listaDetalle = Object.entries(discapacidadesLista).map(([n, c]) => `${n}: ${c}`).join(', ');
        dText.textContent = listaDetalle ? `(Con: ${conDisc} [${listaDetalle}] / Sin: ${sinDisc})` : `(Con: ${conDisc} / Sin: ${sinDisc})`;
      }

      // Actualizar resumen rápido de datos
      const resTotal = document.getElementById('resumen-total');
      if (resTotal) resTotal.textContent = total;

      const resEdad = document.getElementById('resumen-edad-promedio');
      if (resEdad) resEdad.textContent = edadPromedio !== '-' ? `${edadPromedio} años` : '-';

      const resPctMasc = document.getElementById('resumen-porcentaje-hombres');
      if (resPctMasc) resPctMasc.textContent = `${Math.round((masc / totalGen) * 100)}%`;

      const resPctFem = document.getElementById('resumen-porcentaje-mujeres');
      if (resPctFem) resPctFem.textContent = `${Math.round((fem / totalGen) * 100)}%`;

      // 1. Gráfica de Género
      const ctxGen = document.getElementById('modalCursoGeneroChart');
      if (ctxGen) {
        if (modalGeneroChartInst) { modalGeneroChartInst.destroy(); }
        modalGeneroChartInst = new Chart(ctxGen, {
          type: 'doughnut',
          data: {
            labels: ['Masculino', 'Femenino'],
            datasets: [{
              data: [masc, fem],
              backgroundColor: ['#0281fe', '#ec4899'],
              borderWidth: 0,
              borderRadius: 8,
              spacing: 3,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            onClick: (evt, elements) => {
              if (elements && elements.length > 0) {
                const index = elements[0].index;
                const label = modalGeneroChartInst.data.labels[index];
                window.filtrarYMostrarEnTab2('genero', label);
              }
            },
            onHover: (evt, activeElements) => {
              evt.native.target.style.cursor = activeElements && activeElements.length ? 'pointer' : 'default';
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.9)',
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                  label: c => ` ${c.label}: ${c.parsed} (${Math.round(c.parsed / totalGen * 100)}%)`
                }
              },
              valoresDonut: { display: false }
            }
          },
          plugins: [{
            id: 'donutCenterText',
            beforeDraw: function(chart) {
              const { width, height, ctx } = chart;
              ctx.save();
              const totalVal = chart.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
              ctx.font = "bold 1.35rem 'Inter', sans-serif";
              ctx.textBaseline = "middle";
              ctx.fillStyle = "#0f172a";
              const text = totalVal.toString();
              const textX = Math.round((width - ctx.measureText(text).width) / 2);
              const textY = (height / 2) - 10;
              ctx.fillText(text, textX, textY);
              ctx.font = "600 0.65rem 'Inter', sans-serif";
              ctx.fillStyle = "#94a3b8";
              const label = "ALUMNOS";
              const labelX = Math.round((width - ctx.measureText(label).width) / 2);
              const labelY = (height / 2) + 12;
              ctx.fillText(label, labelX, labelY);
              ctx.restore();
            }
          }]
        });
      }

      // 2. Gráfica de Edad
      const ctxEdad = document.getElementById('modalCursoEdadChart');
      if (ctxEdad) {
        if (modalEdadChartInst) { modalEdadChartInst.destroy(); }
        modalEdadChartInst = new Chart(ctxEdad, {
          type: 'doughnut',
          data: {
            labels: ['<18 años', '18-29 años', '30+ años'],
            datasets: [{
              data: [bajo18, de18a29, mas30],
              backgroundColor: ['#38bdf8', '#7367f0', '#f59e0b'],
              borderWidth: 0,
              borderRadius: 8,
              spacing: 3,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            onClick: (evt, elements) => {
              if (elements && elements.length > 0) {
                const index = elements[0].index;
                const label = modalEdadChartInst.data.labels[index];
                window.filtrarYMostrarEnTab2('edad', label);
              }
            },
            onHover: (evt, activeElements) => {
              evt.native.target.style.cursor = activeElements && activeElements.length ? 'pointer' : 'default';
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.9)',
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                  label: c => ` ${c.label}: ${c.parsed} (${Math.round(c.parsed / totalEdad * 100)}%)`
                }
              },
              valoresDonut: { display: false }
            }
          },
          plugins: [{
            id: 'donutCenterText',
            beforeDraw: function(chart) {
              const { width, height, ctx } = chart;
              ctx.save();
              const totalVal = chart.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
              ctx.font = "bold 1.35rem 'Inter', sans-serif";
              ctx.textBaseline = "middle";
              ctx.fillStyle = "#0f172a";
              const text = totalVal.toString();
              const textX = Math.round((width - ctx.measureText(text).width) / 2);
              const textY = (height / 2) - 10;
              ctx.fillText(text, textX, textY);
              ctx.font = "600 0.65rem 'Inter', sans-serif";
              ctx.fillStyle = "#94a3b8";
              const label = "EDADES";
              const labelX = Math.round((width - ctx.measureText(label).width) / 2);
              const labelY = (height / 2) + 12;
              ctx.fillText(label, labelX, labelY);
              ctx.restore();
            }
          }]
        });
      }

      // 3. Gráfica de Discapacidad
      const ctxDisc = document.getElementById('modalCursoDiscapacidadChart');
      if (ctxDisc) {
        if (modalDiscapacidadChartInst) { modalDiscapacidadChartInst.destroy(); }
        modalDiscapacidadChartInst = new Chart(ctxDisc, {
          type: 'doughnut',
          data: {
            labels: ['Con Discapacidad', 'Sin Discapacidad'],
            datasets: [{
              data: [conDisc, sinDisc],
              backgroundColor: ['#f59e0b', '#10b981'],
              borderWidth: 0,
              borderRadius: 8,
              spacing: 3,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            onClick: (evt, elements) => {
              if (elements && elements.length > 0) {
                const index = elements[0].index;
                const label = modalDiscapacidadChartInst.data.labels[index];
                window.filtrarYMostrarEnTab2('discapacidad', label);
              }
            },
            onHover: (evt, activeElements) => {
              evt.native.target.style.cursor = activeElements && activeElements.length ? 'pointer' : 'default';
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.9)',
                padding: 8,
                cornerRadius: 8,
                titleFont: { size: 11 },
                bodyFont: { size: 11 },
                callbacks: {
                  label: c => {
                    if (c.label === 'Con Discapacidad' && Object.keys(discapacidadesLista).length > 0) {
                      const detalles = Object.entries(discapacidadesLista).map(([n, val]) => `${n}: ${val}`).join(', ');
                      return [
                        ` ${c.label}: ${c.parsed}`,
                        ` (${detalles})`
                      ];
                    }
                    return ` ${c.label}: ${c.parsed} (${Math.round(c.parsed / totalDisc * 100)}%)`;
                  }
                }
              },
              valoresDonut: { display: false }
            }
          },
          plugins: [{
            id: 'donutCenterText',
            beforeDraw: function(chart) {
              const { width, height, ctx } = chart;
              ctx.save();
              const totalVal = chart.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
              ctx.font = "bold 1.35rem 'Inter', sans-serif";
              ctx.textBaseline = "middle";
              ctx.fillStyle = "#0f172a";
              const text = totalVal.toString();
              const textX = Math.round((width - ctx.measureText(text).width) / 2);
              const textY = (height / 2) - 10;
              ctx.fillText(text, textX, textY);
              ctx.font = "600 0.65rem 'Inter', sans-serif";
              ctx.fillStyle = "#94a3b8";
              const label = "TOTAL";
              const labelX = Math.round((width - ctx.measureText(label).width) / 2);
              const labelY = (height / 2) + 12;
              ctx.fillText(label, labelX, labelY);
              ctx.restore();
            }
          }]
        });
      }

      // 4. Gráfica de Rendimiento Académico (Aprobados)
      const ctxRend = document.getElementById('modalCursoRendimientoChart');
      if (ctxRend && idEstatus === 5) {
        const totalRend = aprobados + reprobados + ausentes || 1;
        
        // Actualizar texto de diagnóstico
        const rText = document.getElementById('debug-rendimiento-text');
        if (rText) rText.textContent = `(Aprobados: ${aprobados} / Reprobados: ${reprobados} / Ausentes: ${ausentes})`;

        if (modalRendimientoChartInst) { modalRendimientoChartInst.destroy(); }
        modalRendimientoChartInst = new Chart(ctxRend, {
          type: 'doughnut',
          data: {
            labels: ['Aprobados', 'Reprobados', 'Ausentes'],
            datasets: [{
              data: [aprobados, reprobados, ausentes],
              backgroundColor: ['#10b981', '#ef4444', '#6b7280'],
              borderWidth: 0,
              borderRadius: 8,
              spacing: 3,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            onClick: (evt, elements) => {
              if (elements && elements.length > 0) {
                const index = elements[0].index;
                const label = modalRendimientoChartInst.data.labels[index];
                window.filtrarYMostrarEnTab2('rendimiento', label);
              }
            },
            onHover: (evt, activeElements) => {
              evt.native.target.style.cursor = activeElements && activeElements.length ? 'pointer' : 'default';
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.9)',
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                  label: c => ` ${c.label}: ${c.parsed} (${Math.round(c.parsed / totalRend * 100)}%)`
                }
              },
              valoresDonut: { display: false }
            }
          },
          plugins: [{
            id: 'donutCenterTextRend',
            beforeDraw: function(chart) {
              const { width, height, ctx } = chart;
              ctx.save();
              ctx.font = "bold 1.35rem 'Inter', sans-serif";
              ctx.textBaseline = "middle";
              ctx.fillStyle = "#0f172a";
              
              // Mostrar porcentaje de aprobados en el centro
              const pct = totalRend ? Math.round((aprobados / (aprobados + reprobados + ausentes || 1)) * 100) : 0;
              const text = pct + "%";
              
              const textX = Math.round((width - ctx.measureText(text).width) / 2);
              const textY = (height / 2) - 10;
              ctx.fillText(text, textX, textY);
              
              ctx.font = "600 0.65rem 'Inter', sans-serif";
              ctx.fillStyle = "#94a3b8";
              const label = "APROBADOS";
              const labelX = Math.round((width - ctx.measureText(label).width) / 2);
              const labelY = (height / 2) + 12;
              ctx.fillText(label, labelX, labelY);
              ctx.restore();
            }
          }]
        });
      }
    } catch (err) {
      alert("Error en renderModalCharts: " + err.message + "\nStack: " + err.stack);
    }
  }
  window.renderModalCharts = renderModalCharts;

  // Función para filtrar y mostrar en Tab 1
  window.filtrarYMostrarEnTab1 = function(tipo, valor) {
    // 1. Cambiar a la pestaña de "Información del Curso"
    $('#courseModalTab button').removeClass('active');
    $('#courseModalTab button#info-tab').addClass('active');
    $('#courseModalTabContent .tab-pane').removeClass('show active');
    $('#info-tab-pane').addClass('show active');

    // 2. Aplicar el filtro a la tabla
    const rows = $('#tabla-participantes-curso tr');
    let coincidentes = 0;

    rows.each(function() {
      const $row = $(this);
      if ($row.find('td').length === 1 && ($row.text().includes('No hay') || $row.text().includes('cargando'))) {
        return;
      }

      let mostrar = true;
      if (tipo === 'genero') {
        const gen = $row.attr('data-genero');
        mostrar = (gen === valor);
      } else if (tipo === 'edad') {
        const edadVal = parseInt($row.attr('data-edad'));
        if (!isNaN(edadVal)) {
          if (valor === '< 18 años') mostrar = (edadVal < 18);
          else if (valor === '18-29 años') mostrar = (edadVal >= 18 && edadVal <= 29);
          else if (valor === '30+ años') mostrar = (edadVal >= 30);
        } else {
          mostrar = false;
        }
      } else if (tipo === 'discapacidad') {
        const disc = $row.attr('data-discapacidad');
        if (valor === 'Con Discapacidad') mostrar = (disc === 'si');
        else mostrar = (disc === 'no');
      } else if (tipo === 'rendimiento') {
        const rend = $row.attr('data-rendimiento') || $row.find('td:nth-child(6)').text().trim();
        mostrar = (rend.toLowerCase() === valor.toLowerCase());
      }

      if (mostrar) {
        $row.show();
        coincidentes++;
      } else {
        $row.hide();
      }
    });

    // 3. Mostrar el badge del filtro
    let textoFiltro = '';
    if (tipo === 'genero') textoFiltro = `Género: ${valor}`;
    else if (tipo === 'edad') textoFiltro = `Rango de Edad: ${valor}`;
    else if (tipo === 'discapacidad') textoFiltro = valor;
    else if (tipo === 'rendimiento') textoFiltro = `Rendimiento: ${valor}`;

    $('#filtro-activo-texto').text(`${textoFiltro} (${coincidentes} encontrados)`);
    $('#filtro-activo-container').removeClass('d-none');
    
    // Hacer scroll suave hacia el contenedor del filtro
    const badgeEl = document.getElementById('filtro-activo-container');
    if (badgeEl) {
      badgeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  };

  window.limpiarFiltroParticipantes = function() {
    $('#tabla-participantes-curso tr').show();
    $('#filtro-activo-container').addClass('d-none');
  };

  // Función para filtrar y mostrar en Tab 2 (la misma viñeta)
  window.filtrarYMostrarEnTab2 = function(tipo, valor) {
    const $tablaDestino = $('#tabla-participantes-filtrados-tab2');
    $tablaDestino.empty();

    const rows = $('#tabla-participantes-curso tr');
    let coincidentes = 0;

    rows.each(function() {
      const $row = $(this);
      if ($row.find('td').length === 1 && ($row.text().includes('No hay') || $row.text().includes('cargando'))) {
        return;
      }

      let mostrar = false;
      const ced = $row.find('td:nth-child(1)').text();
      const nombre = $row.find('td:nth-child(2)').text();
      const apellido = $row.find('td:nth-child(3)').text();
      const edad = $row.find('td:nth-child(4)').text();
      const sexoText = $row.find('td:nth-child(5)').text();
      const sexoClass = sexoText.includes('Femenino') ? 'badge-femenino' : 'badge-masculino';

      if (tipo === 'genero') {
        const gen = $row.attr('data-genero');
        mostrar = (gen === valor);
      } else if (tipo === 'edad') {
        const edadVal = parseInt($row.attr('data-edad'));
        if (!isNaN(edadVal)) {
          if (valor === '< 18 años') mostrar = (edadVal < 18);
          else if (valor === '18-29 años') mostrar = (edadVal >= 18 && edadVal <= 29);
          else if (valor === '30+ años') mostrar = (edadVal >= 30);
        }
      } else if (tipo === 'discapacidad') {
        const disc = $row.attr('data-discapacidad');
        if (valor === 'Con Discapacidad') mostrar = (disc === 'si');
        else mostrar = (disc === 'no');
      } else if (tipo === 'rendimiento') {
        const rend = $row.attr('data-rendimiento') || $row.find('td:nth-child(6)').text().trim();
        mostrar = (rend.toLowerCase() === valor.toLowerCase());
      }

      if (mostrar) {
        $tablaDestino.append(`
          <tr class="ver-participante-row" data-id="${$row.attr('data-id')}" title="Haga clic para ver detalles del participante" style="cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor=''">
            <td style="padding: 12px; color: #1e293b;">${ced}</td>
            <td style="padding: 12px; color: #1e293b;">${nombre}</td>
            <td style="padding: 12px; color: #1e293b;">${apellido}</td>
            <td style="padding: 12px; color: #1e293b;">${edad}</td>
            <td style="padding: 12px;">
              <span class="${sexoClass}">${sexoText}</span>
            </td>
          </tr>
        `);
        coincidentes++;
      }
    });

    // Actualizar título y mostrar contenedor
    let textoFiltro = '';
    if (tipo === 'genero') textoFiltro = `Género: ${valor}`;
    else if (tipo === 'edad') textoFiltro = `Rango de Edad: ${valor}`;
    else if (tipo === 'discapacidad') textoFiltro = valor;
    else if (tipo === 'rendimiento') textoFiltro = `Rendimiento: ${valor}`;

    $('#filtrados-titulo-tab2').text(`${textoFiltro} (${coincidentes} alumnos)`);
    $('#filtrados-container-tab2').removeClass('d-none');

    // Scroll suave hacia la tabla
    const badgeEl = document.getElementById('filtrados-container-tab2');
    if (badgeEl) {
      badgeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  };

  window.limpiarFiltroParticipantesTab2 = function() {
    $('#tabla-participantes-filtrados-tab2').empty();
    $('#filtrados-container-tab2').addClass('d-none');
  };

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
    toastr.error('Error: No se pudo identificar el curso');
  }
});

// ========== SINCRONIZAR CIERRE DE MODALES ANIDADOS ==========
$('#modal-viewCurso').on('hidden.bs.modal', function () {
  // Destruir las gráficas de donas locales para evitar conflictos de canvas
  if (modalGeneroChartInst) { modalGeneroChartInst.destroy(); modalGeneroChartInst = null; }
  if (modalEdadChartInst) { modalEdadChartInst.destroy(); modalEdadChartInst = null; }
  if (modalDiscapacidadChartInst) { modalDiscapacidadChartInst.destroy(); modalDiscapacidadChartInst = null; }
  if (modalRendimientoChartInst) { modalRendimientoChartInst.destroy(); modalRendimientoChartInst = null; }

  // Limpiar filtros de la viñeta 2
  if (typeof window.limpiarFiltroParticipantesTab2 === 'function') {
    window.limpiarFiltroParticipantesTab2();
  }

  // Si el modal de ver participante está abierto, ciérralo también
  if ($('#modal-viewParticipante').hasClass('show')) {
    $('#modal-viewParticipante').modal('hide');
  }
});

// ========== CONTROL MANUAL DE PESTAÑAS EN EL MODAL ==========
$(document).on('click', '#courseModalTab button', function (e) {
  e.preventDefault();
  const $btn = $(this);
  
  // Quitar active de todos los botones en este grupo
  $('#courseModalTab button').removeClass('active');
  $btn.addClass('active');

  // Quitar show y active de todos los paneles
  $('#courseModalTabContent .tab-pane').removeClass('show active');

  // Activar el panel destino
  const targetId = $btn.attr('data-bs-target');
  $(targetId).addClass('show active');

  if (targetId === '#participants-tab-pane' && cachedStats) {
    setTimeout(() => {
      window.renderModalCharts(
        cachedStats.masc,
        cachedStats.fem,
        cachedStats.bajo18,
        cachedStats.de18a29,
        cachedStats.mas30,
        cachedStats.conDisc,
        cachedStats.sinDisc,
        cachedStats.total,
        cachedStats.edadPromedio,
        cachedStats.discapacidadesLista,
        cachedStats.aprobados || 0,
        cachedStats.reprobados || 0,
        cachedStats.ausentes || 0,
        cachedStats.idEstatus
      );
    }, 150);
  }
});

// ========== MANEJO BOTÓN LIMPIAR FILTRO PARTICIPANTES ==========
$(document).on('click', '#btn-limpiar-filtro-part', function() {
  if (typeof window.limpiarFiltroParticipantes === 'function') {
    window.limpiarFiltroParticipantes();
  }
});

// ========== MANEJO BOTÓN LIMPIAR FILTRO TAB 2 ==========
$(document).on('click', '#btn-limpiar-filtro-tab2', function() {
  if (typeof window.limpiarFiltroParticipantesTab2 === 'function') {
    window.limpiarFiltroParticipantesTab2();
  }
});
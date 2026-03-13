<!-- Modal Ver Detalles del Curso -->
<div class="modal fade" id="modal-viewCurso" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 overflow-hidden" style="
      background: #ffffff;
      box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(226, 232, 240, 0.8) inset, 0 0 30px rgba(2, 129, 254, 0.3);
      border-radius: 24px;
      position: relative;
    ">
      <!-- Efecto neón sutil en bordes -->
      <div style="
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #0281fe, #38bdf8, #7dd3fc, #0281fe);
        border-radius: 26px;
        opacity: 0.3;
        z-index: -1;
        filter: blur(8px);
        animation: neonPulse 3s ease-in-out infinite;
      "></div>

      <!-- Efecto de partículas interactivas que siguen al mouse -->
      <div id="particle-overlay-light" style="
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 0;
        transition: background-position 0.1s ease;
      "></div>

      <!-- Efecto de escaneo láser sutil -->
      <div class="laser-scan-light"></div>

      <!-- HEADER -->
      <div class="modal-header py-4 px-4 animate-on-show" data-delay="0" style="
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid #0281fe;
        border-radius: 24px 24px 0 0;
        position: relative;
        box-shadow: 0 4px 20px rgba(2, 129, 254, 0.2);
      ">
        <!-- Línea neón superior -->
        <div style="
          position: absolute;
          top: 0;
          left: 10%;
          right: 10%;
          height: 3px;
          background: linear-gradient(90deg, transparent, #0281fe, #38bdf8, #7dd3fc, #0281fe, transparent);
          filter: blur(2px);
          animation: neonLine 3s linear infinite;
        "></div>
        
        <!-- Efecto de brillo esquinas -->
        <div class="corner-brilliance-light top-left"></div>
        <div class="corner-brilliance-light bottom-right"></div>
        
        <div class="d-flex justify-content-between align-items-center w-100 position-relative" style="z-index: 2;">
          <div class="d-flex align-items-center gap-3">
            <!-- Icono con efecto neón -->
            <div class="floating-icon" style="
              width: 55px;
              height: 55px;
              background: linear-gradient(135deg, #0281fe, #38bdf8);
              border-radius: 16px;
              display: flex;
              align-items: center;
              justify-content: center;
              box-shadow: 0 8px 16px -4px rgba(2, 129, 254, 0.5), 0 0 15px rgba(2, 129, 254, 0.6);
              animation: iconGlow 2s ease-in-out infinite;
            ">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
              </svg>
            </div>
            <div>
              <h4 class="modal-title fw-bold mb-1" style="
                background: linear-gradient(135deg, #0f172a, #1e293b);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-size: 1.8rem;
                letter-spacing: -0.02em;
                text-shadow: 0 0 10px rgba(2, 129, 254, 0.3);
              ">Detalles del Curso</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot-light"></span>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                  INFORMACIÓN COMPLETA DEL PROGRAMA
                </p>
              </div>
            </div>
          </div>
          
          <!-- BOTÓN DE CERRAR PERSONALIZADO -->
          <button type="button" class="btn-close-custom-light" data-bs-dismiss="modal" aria-label="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- BODY - Contenido principal -->
      <div class="modal-body p-4" style="background: #ffffff; position: relative; z-index: 1;">
        
        <!-- Encabezado con título y estado -->
        <div class="row mb-4 animate-on-show" data-delay="100">
          <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="floating-icon" style="
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #0281fe, #38bdf8);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 5px 15px rgba(2, 129, 254, 0.4);
              ">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
              </div>
              <h3 id="nombre-curso-view" class="fw-bold mb-0" style="
                color: #0f172a;
                font-size: 2rem;
              ">-</h3>
            </div>
            <div class="d-flex flex-wrap gap-2 ms-5 ps-3">
              <span id="estadoCurso-view" class="status-badge-light" style="
                display: inline-block;
                padding: 6px 16px;
                border-radius: 30px;
                font-size: 0.85rem;
                font-weight: 600;
                letter-spacing: 0.5px;
                background: #e0f2fe;
                color: #0281fe;
                border: 1px solid #0281fe;
                box-shadow: 0 0 10px rgba(2, 129, 254, 0.3);
              ">Estado: -</span>
            </div>
          </div>
        </div>

        <!-- Tarjetas de información principal -->
        <div class="row g-4">
          <!-- Columna Izquierda -->
          <div class="col-md-6">
            <!-- Información Básica -->
            <div class="info-card-modern-light animate-on-show" data-delay="200" style="
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              border-radius: 24px;
              padding: 25px;
              transition: all 0.3s ease;
              position: relative;
              overflow: hidden;
            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(2,129,254,0.2), 0 0 20px rgba(2,129,254,0.3)'; this.style.borderColor='#0281fe'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
              <div class="card-shine-light"></div>
              <div class="d-flex align-items-center gap-2 mb-4">
                <div class="icon-glow-light" style="
                  width: 32px;
                  height: 32px;
                  background: #dbeafe;
                  border-radius: 8px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                ">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                </div>
                <h6 class="fw-semibold mb-0" style="color: #1e293b;">Información Básica</h6>
                <span style="
                  background: linear-gradient(135deg, #0281fe, #38bdf8);
                  color: white;
                  font-size: 0.7rem;
                  padding: 4px 10px;
                  border-radius: 30px;
                  margin-left: 8px;
                  box-shadow: 0 0 15px rgba(2, 129, 254, 0.5);
                ">CURSO</span>
              </div>

              <div class="info-grid">
                <div class="info-row-light">
                  <span class="info-label-light">Programa de Formación</span>
                  <span id="programaFormación-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Modalidad</span>
                  <span id="modalidad-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Turno</span>
                  <span id="turno-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Tipo de Formación</span>
                  <span id="tipoFormacion-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Motor</span>
                  <span id="motor-view" class="info-value-light">-</span>
                </div>
              </div>
            </div>

            <!-- Información Adicional -->
            <div class="info-card-modern-light animate-on-show" data-delay="300" style="
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              border-radius: 24px;
              padding: 25px;
              transition: all 0.3s ease;
              position: relative;
              overflow: hidden;
            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
              <div class="card-shine-light"></div>
              <div class="d-flex align-items-center gap-2 mb-4">
                <div class="icon-glow-light" style="
                  width: 32px;
                  height: 32px;
                  background: #dbeafe;
                  border-radius: 8px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                ">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                  </svg>
                </div>
                <h6 class="fw-semibold mb-0" style="color: #1e293b;">Información Adicional</h6>
              </div>

              <div class="info-grid">
                <div class="info-row-light">
                  <span class="info-label-light">Área Formativa</span>
                  <span id="areaFormativa-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Sector Económico</span>
                  <span id="sectorEconomico-view" class="info-value-light">-</span>
                </div>
                <div class="info-row-light">
                  <span class="info-label-light">Ámbito</span>
                  <span id="ambito-view" class="info-value-light">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Columna Derecha -->
          <div class="col-md-6">
            <!-- Panel de Progreso y Cupos -->
            <div class="info-card-modern-light animate-on-show" data-delay="400" style="
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              border-radius: 24px;
              padding: 25px;
              transition: all 0.3s ease;
              position: relative;
              overflow: hidden;
            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(16,185,129,0.2), 0 0 20px rgba(16,185,129,0.3)'; this.style.borderColor='#10b981'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
              <div class="card-shine-light"></div>
              <div class="d-flex align-items-center gap-2 mb-4">
                <div class="icon-glow-light" style="
                  width: 32px;
                  height: 32px;
                  background: #d1fae5;
                  border-radius: 8px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                ">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3"></path>
                    <path d="M7 8l5-5 5 5"></path>
                    <line x1="12" y1="3" x2="12" y2="12"></line>
                  </svg>
                </div>
                <h6 class="fw-semibold mb-0" style="color: #1e293b;">Progreso y Cupos</h6>
              </div>

              <!-- Progreso Circular y Stats -->
              <div class="row align-items-center">
                <div class="col-6 text-center">
                  <div class="progress-circle-light" style="width: 120px; height: 120px; margin: 0 auto; position: relative;">
                    <svg class="progress-svg" viewBox="0 0 100 100">
                      <circle class="progress-bg" cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                      <circle class="progress-ring-bar-light" cx="50" cy="50" r="45" fill="none" stroke="#0281fe" 
                              stroke-width="8" stroke-linecap="round" id="progress-circle" style="stroke-dasharray: 283; stroke-dashoffset: 283;"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                      <span id="circle-text" class="h3 fw-bold mb-0" style="color: #0f172a;">0%</span>
                      <small class="d-block" style="color: #64748b;">Cupo</small>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stats-box-light p-3" style="background: #f1f5f9; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <div class="text-center mb-2">
                      <span class="d-block" style="color: #64748b; font-size: 0.85rem;">Cupos Creados</span>
                      <span id="cupos-view" class="h3 fw-bold" style="color: #10b981;">0</span>
                    </div>
                    <div class="text-center">
                      <span class="d-block" style="color: #64748b; font-size: 0.85rem;">Inscritos</span>
                      <span id="contador-participantes" class="h4 fw-bold" style="color: #0281fe;">0</span>
                    </div>
                  </div>
                  <!-- Mini gráfico de distribución por sexo -->
                  <div class="mt-2 text-center">
                    <canvas id="sexoChart" width="80" height="40" style="max-width: 100%; height: auto;"></canvas>
                    <div class="d-flex justify-content-center gap-2 mt-1">
                      <span class="badge-masculino-light" style="font-size: 0.7rem;">M</span>
                      <span class="badge-femenino-light" style="font-size: 0.7rem;">F</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Fechas del Curso -->
              <div class="row mt-4 g-2">
                <div class="col-6">
                  <div class="date-box-light p-3 text-center" style="background: #f1f5f9; border-radius: 16px; border: 1px solid #e2e8f0;" title="Fecha de inicio del curso">
                    <small style="color: #64748b; display: block; margin-bottom: 5px;">📅 Inicio</small>
                    <span id="fechaInicio-view" class="fw-semibold" style="color: #0f172a;">-</span>
                  </div>
                </div>
                <div class="col-6">
                  <div class="date-box-light p-3 text-center" style="background: #f1f5f9; border-radius: 16px; border: 1px solid #e2e8f0;" title="Fecha de finalización del curso">
                    <small style="color: #64748b; display: block; margin-bottom: 5px;">🎯 Culminación</small>
                    <span id="fechaCulminación-view" class="fw-semibold" style="color: #0f172a;">-</span>
                  </div>
                </div>
              </div>

              <!-- Barra de Progreso Temporal -->
              <div class="mt-4 position-relative">
                <div class="progress" style="height: 8px; background: #e2e8f0; border-radius: 4px; position: relative;">
                  <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #0281fe, #38bdf8); border-radius: 4px;"></div>
                  <!-- Hitos del curso -->
                  <div class="milestones-light" id="milestones-container"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                  <small class="text-muted" style="color: #94a3b8;">Inicio</small>
                  <small class="text-muted" style="color: #94a3b8;">25%</small>
                  <small class="text-muted" style="color: #94a3b8;">50%</small>
                  <small class="text-muted" style="color: #94a3b8;">75%</small>
                  <small class="text-muted" style="color: #94a3b8;">Fin</small>
                </div>
              </div>
            </div>

            <!-- Información del Docente -->
            <div id="contenedor-docente-view" class="info-card-modern-light animate-on-show" data-delay="500" style="
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              border-radius: 24px;
              padding: 25px;
              transition: all 0.3s ease;
              position: relative;
              overflow: hidden;
              cursor: pointer;
            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(139,92,246,0.2), 0 0 20px rgba(139,92,246,0.3)'; this.style.borderColor='#8b5cf6'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'"
              onclick="manejarClicDocente()">
              <div class="card-shine-light"></div>
              <div class="d-flex align-items-center gap-2 mb-4">
                <div class="icon-glow-light" style="
                  width: 32px;
                  height: 32px;
                  background: #ede9fe;
                  border-radius: 8px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                ">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                </div>
                <h6 class="fw-semibold mb-0" style="color: #1e293b;">Docente Asignado</h6>
              </div>

              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="docente-avatar-light position-relative">
                  <div id="docente-avatar-container" style="
                    width: 60px;
                    height: 60px;
                    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 5px 15px rgba(139, 92, 246, 0.3);
                    overflow: hidden;
                  ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                  </div>
                  <span class="status-indicator-light" style="
                    position: absolute;
                    bottom: 2px;
                    right: 2px;
                    width: 12px;
                    height: 12px;
                    background: #10b981;
                    border-radius: 50%;
                    border: 2px solid #ffffff;
                    box-shadow: 0 0 10px #10b981;
                  "></span>
                </div>
                <div style="flex: 1;">
                  <h6 class="fw-bold mb-1" id="docente-nombre" style="color: #0f172a; font-size: 1.1rem;">-</h6>
                  <div class="d-flex gap-2">
                    <span style="color: #64748b; font-size: 0.85rem;">Docente del curso</span>
                    <div class="quick-actions-light">
                      <button class="action-icon-light" onclick="event.stopPropagation(); enviarEmailDocente()" title="Enviar email">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                          <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                      </button>
                      <button class="action-icon-light" onclick="event.stopPropagation(); verPerfilDocente()" title="Ver perfil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                          <circle cx="12" cy="12" r="3"></circle>
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <div class="info-mini-light" style="background: #f1f5f9; border-radius: 12px; padding: 10px; border: 1px solid #e2e8f0;">
                    <small style="color: #64748b; display: block;">📋 Cédula</small>
                    <span id="docente-cedula" class="fw-semibold" style="color: #0f172a;">-</span>
                  </div>
                </div>
                <div class="col-6">
                  <div class="info-mini-light" style="background: #f1f5f9; border-radius: 12px; padding: 10px; border: 1px solid #e2e8f0;">
                    <small style="color: #64748b; display: block;">🎂 Edad</small>
                    <span id="docente-edad" class="fw-semibold" style="color: #0f172a;">-</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Participantes -->
        <div class="info-card-modern-light mt-4 animate-on-show" data-delay="600" style="
          background: #f8fafc;
          border: 1px solid #e2e8f0;
          border-radius: 24px;
          padding: 25px;
          transition: all 0.3s ease;
          position: relative;
          overflow: hidden;
        " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(2,129,254,0.2), 0 0 20px rgba(2,129,254,0.3)'; this.style.borderColor='#0281fe'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
          <div class="card-shine-light"></div>
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
              <div class="icon-glow-light" style="
                width: 32px;
                height: 32px;
                background: #dbeafe;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
              ">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h6 class="fw-semibold mb-0" style="color: #1e293b;">Participantes Inscritos</h6>
            </div>
            <div class="d-flex align-items-center gap-3">
              <span style="
                background: #dbeafe;
                color: #0281fe;
                padding: 6px 16px;
                border-radius: 30px;
                font-size: 0.85rem;
                border: 1px solid #0281fe;
                box-shadow: 0 0 10px rgba(2, 129, 254, 0.3);
              ">Total: <span id="contador-participantes-tabla">0</span></span>
              <button class="btn action-icon-light" onclick="enviarRecordatorioMasivo()" title="Enviar recordatorio a todos">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2">
                  <path d="M22 2L11 13"></path>
                  <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table futuristic-table-light" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #e2e8f0; border-bottom: 2px solid #0281fe;">
                  <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 12px 0 0 12px;">Cédula</th>
                  <th style="color: #0281fe; font-weight: 600; padding: 12px;">Nombre</th>
                  <th style="color: #0281fe; font-weight: 600; padding: 12px;">Apellido</th>
                  <th style="color: #0281fe; font-weight: 600; padding: 12px;">Edad</th>
                  <th style="color: #0281fe; font-weight: 600; padding: 12px; border-radius: 0 12px 12px 0;">Sexo</th>
                </tr>
              </thead>
              <tbody id="tabla-participantes-curso">
                <tr id="no-participantes">
                  <td colspan="5" class="text-center py-5">
                    <div style="
                      width: 60px;
                      height: 60px;
                      background: #f1f5f9;
                      border-radius: 50%;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      margin: 0 auto 15px;
                      border: 2px dashed #94a3b8;
                    ">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M5.37 20c.92-2.52 3.46-4 6.63-4s5.71 1.48 6.63 4"></path>
                      </svg>
                    </div>
                    <p style="color: #64748b;">No hay participantes inscritos</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Botón "Ver más" si hay muchos participantes -->
          <div id="ver-mas-participantes-container" class="text-center mt-3" style="display: none;">
            <button class="btn futuristic-button-outline-light" onclick="cargarMasParticipantes()">
              Ver lista completa
            </button>
          </div>
        </div>
      </div>

      <!-- FOOTER con acciones -->
      <div class="modal-footer justify-content-between px-4 py-3 animate-on-show" data-delay="700" style="
        background: #f8fafc;
        border-top: 2px solid #0281fe;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 -4px 20px rgba(2, 129, 254, 0.2);
        position: relative;
        z-index: 2;
      ">
        <!-- Efecto de gradiente en el borde -->
        <div class="footer-glow-light"></div>
        
        <button type="button" class="btn futuristic-button-secondary-light" data-bs-dismiss="modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
          CERRAR
        </button>
        
        <div class="d-flex gap-2">
          <button class="btn futuristic-button-outline-light" id="btn-recordatorio" onclick="enviarRecordatorioMasivo()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 2L11 13"></path>
              <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
            </svg>
            RECORDATORIO
          </button>
          
          <button class="btn futuristic-button-primary-light" id="btn-matricula-pdf" data-id="">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            DESCARGAR MATRÍCULA
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Animaciones neón */
@keyframes neonPulse {
    0%, 100% {
        opacity: 0.3;
        filter: blur(8px);
    }
    50% {
        opacity: 0.5;
        filter: blur(12px);
    }
}

@keyframes neonLine {
    0% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0.5;
    }
}

@keyframes iconGlow {
    0%, 100% {
        box-shadow: 0 8px 16px -4px rgba(2, 129, 254, 0.5), 0 0 15px rgba(2, 129, 254, 0.6);
    }
    50% {
        box-shadow: 0 8px 16px -4px rgba(2, 129, 254, 0.7), 0 0 25px rgba(2, 129, 254, 0.9);
    }
}

@keyframes laserScan {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(200%); }
}

/* Elementos decorativos claros */
.laser-scan-light {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, transparent, rgba(2, 129, 254, 0.03), transparent);
    animation: laserScan 8s linear infinite;
    pointer-events: none;
    z-index: 0;
}

.corner-brilliance-light {
    position: absolute;
    width: 150px;
    height: 150px;
    pointer-events: none;
}

.corner-brilliance-light.top-left {
    top: 0;
    left: 0;
    background: radial-gradient(circle at top left, rgba(2, 129, 254, 0.1), transparent 70%);
}

.corner-brilliance-light.bottom-right {
    bottom: 0;
    right: 0;
    background: radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.1), transparent 70%);
}

.pulse-dot-light {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 10px #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.2); box-shadow: 0 0 20px #10b981; }
}

/* Efecto de brillo en tarjetas */
.card-shine-light {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    pointer-events: none;
}

.info-card-modern-light:hover .card-shine-light {
    left: 100%;
    transition: left 1s ease;
}

/* Estilo para las tarjetas de información */
.info-card-modern-light {
    transition: all 0.3s ease !important;
    position: relative;
}

.info-card-modern-light:hover {
    transform: translateY(-3px) scale(1.01) !important;
}

/* Estilo para las filas de información */
.info-row-light {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-row-light:last-child {
    border-bottom: none;
}

.info-label-light {
    color: #64748b;
    font-size: 0.85rem;
}

.info-value-light {
    color: #0f172a;
    font-weight: 500;
    background: #ffffff;
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.info-value-light:hover {
    background: #dbeafe;
    border-color: #0281fe;
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(2, 129, 254, 0.3);
}

/* Estilo para la tabla futurista */
.futuristic-table-light {
    width: 100%;
    border-collapse: collapse;
}

.futuristic-table-light tbody tr {
    background: #ffffff;
    transition: all 0.3s ease;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
}

.futuristic-table-light tbody tr:hover {
    background: #f1f5f9;
    box-shadow: 0 0 15px rgba(2, 129, 254, 0.2);
    transform: scale(1.01) translateX(5px);
}

.futuristic-table-light td {
    padding: 12px;
    color: #334155;
}

/* Progreso circular */
.progress-circle-light {
    position: relative;
}

.progress-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.progress-ring-bar-light {
    transition: stroke-dashoffset 1s ease-in-out;
}

/* Badges de sexo claros */
.badge-masculino-light {
    background: #dbeafe;
    color: #0281fe;
    border: 1px solid #0281fe;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.badge-masculino-light:hover {
    background: #0281fe;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 0 15px #0281fe;
}

.badge-femenino-light {
    background: #fce7f3;
    color: #ec4899;
    border: 1px solid #ec4899;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.badge-femenino-light:hover {
    background: #ec4899;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 0 15px #ec4899;
}

/* Scroll personalizado */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #0281fe, #38bdf8);
    border-radius: 10px;
    box-shadow: 0 0 10px #0281fe;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #38bdf8, #0281fe);
}

/* Info mini cards claras */
.info-mini-light {
    transition: all 0.3s ease;
}

.info-mini-light:hover {
    border-color: #0281fe !important;
    box-shadow: 0 0 15px rgba(2, 129, 254, 0.3);
    transform: translateY(-2px);
}

/* Date boxes claros */
.date-box-light {
    transition: all 0.3s ease;
}

.date-box-light:hover {
    transform: translateY(-3px);
    border-color: #0281fe !important;
    box-shadow: 0 0 20px rgba(2, 129, 254, 0.3);
}

/* Stats box claro */
.stats-box-light {
    transition: all 0.3s ease;
}

.stats-box-light:hover {
    transform: scale(1.02);
    border-color: #0281fe !important;
    box-shadow: 0 0 20px rgba(2, 129, 254, 0.3);
}

/* Botón de cerrar personalizado */
.btn-close-custom-light {
    background: transparent;
    border: 2px solid #0281fe;
    border-radius: 12px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 0 15px rgba(2, 129, 254, 0.5);
    cursor: pointer;
}

.btn-close-custom-light:hover {
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 0 25px rgba(2, 129, 254, 0.8);
}

/* Botones futuristas claros */
.futuristic-button-primary-light {
    background: linear-gradient(135deg, #0281fe, #38bdf8);
    border: none;
    padding: 10px 25px;
    border-radius: 12px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    box-shadow: 0 5px 20px rgba(2, 129, 254, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
}

.futuristic-button-primary-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(2, 129, 254, 0.7);
}

.futuristic-button-secondary-light {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 10px 25px;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.futuristic-button-secondary-light:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.futuristic-button-outline-light {
    background: transparent;
    border: 1px solid #0281fe;
    color: #0281fe;
    padding: 10px 25px;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.futuristic-button-outline-light:hover {
    background: #dbeafe;
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(2, 129, 254, 0.4);
}

/* Iconos de acción rápida */
.quick-actions-light {
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.info-card-modern-light:hover .quick-actions-light {
    opacity: 1;
}

.action-icon-light {
    background: #ede9fe;
    border: none;
    border-radius: 8px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}

.action-icon-light:hover {
    background: #8b5cf6;
    color: white;
    transform: scale(1.1);
}

.action-icon-light:hover svg {
    stroke: white;
}

/* Hitos de progreso claros */
.milestones-light {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.milestone-light {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #38bdf8;
    border-radius: 50%;
    top: -2px;
    transform: translateX(-50%);
    box-shadow: 0 0 10px #38bdf8;
    cursor: pointer;
    pointer-events: auto;
    transition: all 0.2s ease;
}

.milestone-light:hover {
    transform: translateX(-50%) scale(1.5);
    background: #0281fe;
    box-shadow: 0 0 20px #0281fe;
}

.milestone-light::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    color: #0f172a;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
    border: 1px solid #0281fe;
    box-shadow: 0 0 15px rgba(2, 129, 254, 0.3);
}

.milestone-light:hover::after {
    opacity: 1;
}

/* Footer glow claro */
.footer-glow-light {
    position: absolute;
    top: -1px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #0281fe, #38bdf8, #0281fe, transparent);
    filter: blur(2px);
}

/* Animaciones de entrada */
.animate-on-show {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.5s forwards;
}

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Status badge */
.status-badge-light {
    transition: all 0.3s ease;
}

.status-badge-light:hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px currentColor;
}
</style>
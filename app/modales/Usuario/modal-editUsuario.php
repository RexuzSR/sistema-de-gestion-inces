<!-- Modal Editar Usuario - Versión Profesional con Efectos Neón Azul -->
<div class="modal fade" id="modal-editUsuario" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-editUsuario">
  <div class="modal-dialog modal-dialog-centered modal-lg">
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

      <!-- HEADER con efecto profesional -->
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
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
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
              ">Editar Usuario</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="pulse-dot-light"></span>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                  CONFIGURACIÓN DE PERFIL
                </p>
              </div>
            </div>
          </div>
          
          <!-- BOTÓN DE CERRAR CON ESTILO NEÓN -->
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
        
        <!-- Stepper futurista -->
        <div class="stepper-wrapper mb-5 position-relative" style="padding: 0 20px;">
          <!-- Línea de progreso con efecto neón -->
          <div class="progress-line position-absolute top-50 start-0 end-0" style="height: 3px; background: #e2e8f0; z-index: 1; transform: translateY(-50%);">
            <div class="progress-fill" id="progressFill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #0281fe, #38bdf8, #7dd3fc); transition: width 0.3s ease; box-shadow: 0 0 15px #0281fe;"></div>
          </div>
          
          <div class="stepper d-flex justify-content-between position-relative">
            <!-- Paso 1 -->
            <div class="step position-relative d-flex flex-column align-items-center" style="z-index: 2;" id="step1-indicator">
              <div class="step-circle d-flex align-items-center justify-content-center rounded-circle" style="
                width: 45px; 
                height: 45px; 
                background: #dbeafe;
                border: 2px solid #0281fe;
                box-shadow: 0 0 20px rgba(2, 129, 254, 0.3);
                transition: all 0.3s ease;
              ">
                <span class="step-number fw-bold" style="color: #0281fe;">1</span>
                <i class="step-icon bi bi-check-lg d-none" style="color: #10b981;"></i>
              </div>
              <span class="step-label mt-2 small fw-medium" style="color: #0281fe; letter-spacing: 1px;">DATOS PERSONALES</span>
            </div>
            
            <!-- Paso 2 -->
            <div class="step position-relative d-flex flex-column align-items-center" style="z-index: 2;" id="step2-indicator">
              <div class="step-circle d-flex align-items-center justify-content-center rounded-circle" style="
                width: 45px; 
                height: 45px; 
                background: #f1f5f9;
                border: 2px solid #cbd5e1;
                transition: all 0.3s ease;
              ">
                <span class="step-number fw-bold" style="color: #94a3b8;">2</span>
                <i class="step-icon bi bi-check-lg d-none" style="color: #10b981;"></i>
              </div>
              <span class="step-label mt-2 small fw-medium" style="color: #94a3b8; letter-spacing: 1px;">DATOS DE SISTEMA</span>
            </div>
          </div>
        </div>

        <section>
          <form id="form_editUsuario" class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
            
            <!-- PASO 1: Datos Personales -->
            <div id="step1-content" class="step-content">
              <!-- Información Personal -->
              <div class="info-card-light mb-4" style="
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 24px;
                padding: 25px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
              " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(2,129,254,0.2), 0 0 20px rgba(2,129,254,0.3)'; this.style.borderColor='#0281fe'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                <div class="card-shine-light"></div>
                
                <div class="d-flex align-items-center gap-2 mb-4">
                  <div style="
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
                  <h6 class="fw-semibold mb-0" style="color: #1e293b;">Información Personal</h6>
                  <span class="badge-neon" style="
                    background: linear-gradient(135deg, #0281fe, #38bdf8);
                    color: white;
                    font-size: 0.7rem;
                    padding: 4px 12px;
                    border-radius: 30px;
                    margin-left: 8px;
                    box-shadow: 0 0 15px rgba(2, 129, 254, 0.5);
                  ">PASO 1</span>
                </div>

                <div class="row g-4">
                  <div class="col-md-6">
                    <label for="primer_nombre" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Primer Nombre <span style="color: #0281fe;">*</span>
                    </label>
                    <input class="form-control neon-input" type="text" name="primer_nombre" id="primer_nombre-Edit" required placeholder="Ej: Juan" style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <div class="invalid-feedback" id="error-primer_nombre-edit" style="color: #ef4444;"></div>
                  </div>

                  <div class="col-md-6">
                    <label for="segundo_nombre" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">Segundo Nombre</label>
                    <input class="form-control neon-input" type="text" id="segundo_nombre-Edit" name="segundo_nombre" placeholder="Ej: Carlos" style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <div class="invalid-feedback" id="error-segundo_nombre-edit" style="color: #ef4444;"></div>
                  </div>

                  <div class="col-md-6">
                    <label for="primer_apellido" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Primer Apellido <span style="color: #0281fe;">*</span>
                    </label>
                    <input class="form-control neon-input" type="text" id="primer_apellido-Edit" name="primer_apellido" required placeholder="Ej: Pérez" style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <div class="invalid-feedback" id="error-primer_apellido-edit" style="color: #ef4444;"></div>
                  </div>

                  <div class="col-md-6">
                    <label for="segundo_apellido" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">Segundo Apellido</label>
                    <input class="form-control neon-input" type="text" id="segundo_apellido-Edit" name="segundo_apellido" placeholder="Ej: García" style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <div class="invalid-feedback" id="error-segundo_apellido-edit" style="color: #ef4444;"></div>
                  </div>
                </div>
              </div>

              <!-- Avatar -->
              <div class="info-card-light" style="
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 24px;
                padding: 25px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
              " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                <div class="card-shine-light"></div>
                
                <div class="d-flex align-items-center gap-2 mb-4">
                  <div style="
                    width: 32px;
                    height: 32px;
                    background: #dbeafe;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                  ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                      <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                  </div>
                  <h6 class="fw-semibold mb-0" style="color: #1e293b;">Avatar</h6>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="avatar-upload d-flex align-items-center gap-4">
                      <div class="avatar-preview position-relative">
                        <img id="avatarUsuario" src="https://via.placeholder.com/100" alt="Avatar del usuario" class="rounded-circle object-fit-cover" width="100" height="100" style="
                          border: 3px solid #0281fe;
                          box-shadow: 0 0 30px rgba(2, 129, 254, 0.3);
                          background: #ffffff;
                        ">
                        <div class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded-circle" style="
                          background: rgba(2, 129, 254, 0.2);
                          backdrop-filter: blur(5px);
                          opacity: 0;
                          transition: opacity 0.3s;
                          cursor: pointer;
                        ">
                          <i class="bi bi-camera-fill text-white" style="font-size: 1.5rem; text-shadow: 0 0 10px rgba(255,255,255,0.5);"></i>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <label for="imagen-Edit" class="btn" style="
                          background: transparent;
                          border: 1.5px solid #0281fe;
                          color: #0281fe;
                          padding: 8px 25px;
                          border-radius: 30px;
                          font-weight: 500;
                          transition: all 0.3s ease;
                          cursor: pointer;
                          box-shadow: 0 0 15px rgba(2, 129, 254, 0.3);
                        " onmouseover="this.style.background='#0281fe'; this.style.color='white'; this.style.boxShadow='0 0 25px rgba(2,129,254,0.6)'" onmouseout="this.style.background='transparent'; this.style.color='#0281fe'; this.style.boxShadow='0 0 15px rgba(2,129,254,0.3)'">
                          <i class="bi bi-upload me-2"></i>Cambiar avatar
                        </label>
                        <input class="d-none" type="file" name="imagen" id="imagen-Edit" accept="image/jpeg, image/png, image/gif">
                        <p class="small mt-2 mb-0" style="color: #64748b;">Formatos: JPG, PNG, GIF. Máx 2MB</p>
                        <div class="invalid-feedback" id="error-imagen-edit" style="color: #ef4444;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PASO 2: Datos de Sistema -->
            <div id="step2-content" class="step-content" style="display: none;">
              <div class="info-card-light" style="
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 24px;
                padding: 25px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
              " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(16,185,129,0.2), 0 0 20px rgba(16,185,129,0.3)'; this.style.borderColor='#10b981'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                <div class="card-shine-light"></div>
                
                <div class="d-flex align-items-center gap-2 mb-4">
                  <div style="
                    width: 32px;
                    height: 32px;
                    background: #d1fae5;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                  ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  </div>
                  <h6 class="fw-semibold mb-0" style="color: #1e293b;">Configuración de Cuenta</h6>
                  <span class="badge-neon" style="
                    background: linear-gradient(135deg, #10b981, #34d399);
                    color: white;
                    font-size: 0.7rem;
                    padding: 4px 12px;
                    border-radius: 30px;
                    margin-left: 8px;
                    box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
                  ">PASO 2</span>
                </div>

                <div class="row g-4">
                  <!-- Usuario -->
                  <div class="col-md-6">
                    <label for="nombre_usuario" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Usuario <span style="color: #0281fe;">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text" style="
                        background: #f1f5f9;
                        border: 1.5px solid #e2e8f0;
                        border-right: none;
                        border-radius: 12px 0 0 12px;
                        color: #0281fe;
                      "><i class="bi bi-person"></i></span>
                      <input class="form-control neon-input" type="text" name="nombre_usuario" id="nombre_usuario-Edit" required placeholder="@usuario" style="
                        border: 1.5px solid #e2e8f0;
                        border-left: none;
                        border-radius: 0 12px 12px 0;
                        padding: 12px 16px;
                        font-size: 0.95rem;
                        transition: all 0.3s ease;
                        background: #ffffff;
                      " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                    <div class="invalid-feedback" id="error-nombre_usuario-edit" style="color: #ef4444;"></div>
                  </div>

                  <!-- Correo -->
                  <div class="col-md-6">
                    <label for="correo-Edit" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Correo Electrónico <span style="color: #0281fe;">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text" style="
                        background: #f1f5f9;
                        border: 1.5px solid #e2e8f0;
                        border-right: none;
                        border-radius: 12px 0 0 12px;
                        color: #0281fe;
                      "><i class="bi bi-envelope"></i></span>
                      <input type="email" class="form-control neon-input" id="correo-Edit" name="correo" required placeholder="ejemplo@correo.com" style="
                        border: 1.5px solid #e2e8f0;
                        border-left: none;
                        border-radius: 0 12px 12px 0;
                        padding: 12px 16px;
                        font-size: 0.95rem;
                        transition: all 0.3s ease;
                        background: #ffffff;
                      " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                    <div class="invalid-feedback" id="error-correo-edit" style="color: #ef4444;"></div>
                  </div>

                  <!-- Nueva Contraseña -->
                  <div class="col-md-6">
                    <label for="password-Edit" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">Nueva Contraseña</label>
                    <div class="input-group">
                      <span class="input-group-text" style="
                        background: #f1f5f9;
                        border: 1.5px solid #e2e8f0;
                        border-right: none;
                        border-radius: 12px 0 0 12px;
                        color: #0281fe;
                      "><i class="bi bi-lock"></i></span>
                      <input class="form-control neon-input" type="password" id="password-Edit" name="password" placeholder="••••••••" style="
                        border: 1.5px solid #e2e8f0;
                        border-left: none;
                        border-radius: 0 12px 12px 0;
                        padding: 12px 16px;
                        font-size: 0.95rem;
                        transition: all 0.3s ease;
                        background: #ffffff;
                      " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                    <small style="color: #64748b;">Vacío = mantener actual</small>
                    <div class="invalid-feedback" id="error-password-edit" style="color: #ef4444;"></div>
                  </div>

                  <!-- Confirmar Contraseña -->
                  <div class="col-md-6">
                    <label for="confirm_password-Edit" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">Confirmar Contraseña</label>
                    <div class="input-group">
                      <span class="input-group-text" style="
                        background: #f1f5f9;
                        border: 1.5px solid #e2e8f0;
                        border-right: none;
                        border-radius: 12px 0 0 12px;
                        color: #0281fe;
                      "><i class="bi bi-lock-fill"></i></span>
                      <input class="form-control neon-input" type="password" id="confirm_password-Edit" name="confirm_password" placeholder="••••••••" style="
                        border: 1.5px solid #e2e8f0;
                        border-left: none;
                        border-radius: 0 12px 12px 0;
                        padding: 12px 16px;
                        font-size: 0.95rem;
                        transition: all 0.3s ease;
                        background: #ffffff;
                      " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                    <small style="color: #64748b;">Repite la nueva contraseña</small>
                    <div class="invalid-feedback" id="error-confirm_password-edit" style="color: #ef4444;"></div>
                  </div>

                  <!-- Rol -->
                  <div class="col-md-3">
                    <label for="rol-Edit" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Rol <span style="color: #0281fe;">*</span>
                    </label>
                    <select class="form-select neon-select" id="rol-Edit" name="rol" required style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                      <option value="Administrador">Administrador</option>
                      <option value="Apoyo">Apoyo</option>
                    </select>
                    <div class="invalid-feedback" id="error-rol-edit" style="color: #ef4444;"></div>
                  </div>

                  <!-- Estado -->
                  <div class="col-md-3">
                    <label for="estado_usuario-Edit" class="form-label fw-medium small text-uppercase" style="color: #475569; letter-spacing: 1px;">
                      Estado <span style="color: #0281fe;">*</span>
                    </label>
                    <select class="form-select neon-select" id="estado_usuario-Edit" name="estado_usuario" required style="
                      border: 1.5px solid #e2e8f0;
                      border-radius: 12px;
                      padding: 12px 16px;
                      font-size: 0.95rem;
                      transition: all 0.3s ease;
                      background: #ffffff;
                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                      <option value="Activo" style="color: #10b981;">🟢 Activo</option>
                      <option value="Inactivo" style="color: #ef4444;">🔴 Inactivo</option>
                    </select>
                    <div class="invalid-feedback" id="error-estado-edit" style="color: #ef4444;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Campos ocultos -->
            <input type="hidden" name="imagen_actual" id="imagen_actual-Edit">
            <input type="hidden" id="idUsuario" name="id_usuario">
            <input type="hidden" id="current-user-id" value="<?php echo $_SESSION['id_usuario'] ?? ''; ?>">
                   
            <!-- Botones de navegación futuristas -->
            <div class="d-flex justify-content-between align-items-center mt-5">
              <button type="button" class="btn" id="prevBtn" style="display: none; background: transparent; border: 1.5px solid #64748b; color: #64748b; padding: 10px 25px; border-radius: 12px; font-weight: 500; transition: all 0.3s ease; letter-spacing: 0.5px;" onmouseover="this.style.background='#64748b'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 0 20px rgba(100,116,139,0.5)'" onmouseout="this.style.background='transparent'; this.style.color='#64748b'; this.style.transform='translateY(0)'; this.style.boxShadow='none'" onclick="changeStep(-1)">
                <i class="bi bi-arrow-left me-2"></i>ANTERIOR
              </button>
              <div class="flex-grow-1"></div>
              <button type="button" class="btn" id="nextBtn" style="background: linear-gradient(135deg, #0281fe, #38bdf8); border: none; padding: 10px 35px; border-radius: 12px; color: white; font-weight: 500; transition: all 0.3s ease; letter-spacing: 0.5px; box-shadow: 0 5px 20px rgba(2, 129, 254, 0.4);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(2,129,254,0.7)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(2,129,254,0.4)'" onclick="changeStep(1)">
                SIGUIENTE <i class="bi bi-arrow-right ms-2"></i>
              </button>
              <button type="submit" class="btn" id="submitBtn" style="display: none; background: linear-gradient(135deg, #10b981, #34d399); border: none; padding: 10px 35px; border-radius: 12px; color: white; font-weight: 500; transition: all 0.3s ease; letter-spacing: 0.5px; box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(16,185,129,0.7)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(16,185,129,0.4)'">
                <i class="bi bi-check-circle me-2"></i>ACTUALIZAR
              </button>
            </div>
          </form>
        </section>
      </div>

      <!-- FOOTER con efecto -->
      <div class="modal-footer justify-content-center px-4 py-2" style="
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        position: relative;
        z-index: 2;
      ">
        <!-- Línea de luz inferior -->
        <div style="
          position: absolute;
          top: -1px;
          left: 10%;
          right: 10%;
          height: 2px;
          background: linear-gradient(90deg, transparent, #0281fe, #38bdf8, #0281fe, transparent);
          filter: blur(2px);
        "></div>
        <p style="color: #94a3b8; font-size: 0.75rem; letter-spacing: 1px; margin: 0;">
          SISTEMA DE GESTIÓN DE USUARIOS • VERSIÓN 2.0
        </p>
      </div>
    </div>
  </div>
</div>

<!-- ESTILOS Y SCRIPTS INTEGRADOS -->
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

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeOutLeft {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(-30px);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(30px);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.info-card-light:hover .card-shine-light {
    left: 100%;
    transition: left 1s ease;
}

/* Estilo para las tarjetas de información */
.info-card-light {
    transition: all 0.3s ease !important;
    position: relative;
}

.info-card-light:hover {
    transform: translateY(-3px) scale(1.01) !important;
}

/* Estilo para inputs con efecto hover neón */
.neon-input {
    transition: all 0.3s ease !important;
}

.neon-input:hover {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.1), 0 0 20px rgba(2, 129, 254, 0.4) !important;
}

.neon-input:focus {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.2), 0 0 25px rgba(2, 129, 254, 0.5) !important;
    outline: none;
}

/* Estilo para selects */
.neon-select {
    transition: all 0.3s ease !important;
}

.neon-select:hover {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.1), 0 0 20px rgba(2, 129, 254, 0.4) !important;
}

.neon-select:focus {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.2), 0 0 25px rgba(2, 129, 254, 0.5) !important;
    outline: none;
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

/* Step indicators */
.step-circle {
    transition: all 0.3s ease;
}

.step-circle.bg-success {
    background: #d1fae5 !important;
    border-color: #10b981 !important;
}

.step-circle.bg-success .step-number {
    display: none;
}

.step-circle.bg-success i {
    display: block !important;
    color: #10b981 !important;
    font-size: 1.5rem;
}

/* Invalid feedback */
.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.invalid-feedback {
    font-size: 0.8rem;
    margin-top: 5px;
    color: #ef4444;
}

/* Avatar hover effect */
.avatar-preview {
    cursor: pointer;
    overflow: hidden;
    border-radius: 50%;
}

.avatar-preview:hover .avatar-overlay {
    opacity: 1 !important;
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

/* Animación de entrada del modal */
.modal.fade .modal-dialog {
    transform: scale(0.98) translateY(-10px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Icono flotante */
.floating-icon {
    animation: float 3s ease-in-out infinite;
}

/* Animaciones de entrada */
.animate-on-show {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.5s forwards;
}

/* Badge neón */
.badge-neon {
    transition: all 0.3s ease;
}

.badge-neon:hover {
    transform: scale(1.05);
    filter: brightness(1.2);
}

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .step-label {
        font-size: 0.7rem !important;
    }
    
    .step-circle {
        width: 35px !important;
        height: 35px !important;
    }
    
    .modal-title {
        font-size: 1.3rem !important;
    }
}
</style>

<!-- Script del modal -->
<script>
// Variables globales
let currentStep = 1;

// Función para cambiar de paso
window.changeStep = function(direction) {
    const step1 = document.getElementById('step1-content');
    const step2 = document.getElementById('step2-content');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressFill = document.getElementById('progressFill');
    
    // Elementos del stepper
    const step1Indicator = document.getElementById('step1-indicator');
    const step2Indicator = document.getElementById('step2-indicator');
    const step1Circle = step1Indicator.querySelector('.step-circle');
    const step2Circle = step2Indicator.querySelector('.step-circle');
    const step1Label = step1Indicator.querySelector('.step-label');
    const step2Label = step2Indicator.querySelector('.step-label');
    
    if (direction === 1 && currentStep === 1) {
        // Validar campos del paso 1 antes de avanzar
        if (validateStep1()) {
            // Cambiar al paso 2
            step1.style.display = 'none';
            step2.style.display = 'block';
            
            // Actualizar UI
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
            
            // Actualizar stepper visual
            step1Circle.style.background = '#d1fae5';
            step1Circle.style.borderColor = '#10b981';
            step1Circle.innerHTML = '<i class="bi bi-check-lg" style="color: #10b981; font-size: 1.5rem;"></i>';
            step1Label.style.color = '#10b981';
            
            step2Circle.style.background = '#dbeafe';
            step2Circle.style.borderColor = '#0281fe';
            step2Circle.innerHTML = '<span class="step-number fw-bold" style="color: #0281fe;">2</span>';
            step2Label.style.color = '#0281fe';
            
            // Actualizar barra de progreso
            if (progressFill) progressFill.style.width = '100%';
            
            currentStep = 2;
        }
    } else if (direction === -1 && currentStep === 2) {
        // Volver al paso 1
        step2.style.display = 'none';
        step1.style.display = 'block';
        
        // Actualizar UI
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
        
        // Actualizar stepper visual
        step1Circle.style.background = '#dbeafe';
        step1Circle.style.borderColor = '#0281fe';
        step1Circle.innerHTML = '<span class="step-number fw-bold" style="color: #0281fe;">1</span>';
        step1Label.style.color = '#0281fe';
        
        step2Circle.style.background = '#f1f5f9';
        step2Circle.style.borderColor = '#cbd5e1';
        step2Circle.innerHTML = '<span class="step-number fw-bold" style="color: #94a3b8;">2</span>';
        step2Label.style.color = '#94a3b8';
        
        // Actualizar barra de progreso
        if (progressFill) progressFill.style.width = '0%';
        
        currentStep = 1;
    }
};

// Función para validar el paso 1
window.validateStep1 = function() {
    const primerNombre = document.getElementById('primer_nombre-Edit');
    const primerApellido = document.getElementById('primer_apellido-Edit');
    
    let isValid = true;
    
    // Remover clases de error previas
    [primerNombre, primerApellido].forEach(field => {
        if (field) field.classList.remove('is-invalid');
    });
    
    // Validar primer nombre
    if (!primerNombre || !primerNombre.value.trim()) {
        if (primerNombre) {
            primerNombre.classList.add('is-invalid');
            document.getElementById('error-primer_nombre-edit').textContent = 'El primer nombre es requerido';
        }
        isValid = false;
        shakeElement(primerNombre);
    }
    
    // Validar primer apellido
    if (!primerApellido || !primerApellido.value.trim()) {
        if (primerApellido) {
            primerApellido.classList.add('is-invalid');
            document.getElementById('error-primer_apellido-edit').textContent = 'El primer apellido es requerido';
        }
        isValid = false;
        shakeElement(primerApellido);
    }
    
    if (!isValid) {
        showToast('Por favor complete los campos requeridos', 'warning');
    }
    
    return isValid;
};

// Función para validar el paso 2
window.validateStep2 = function() {
    const nombreUsuario = document.getElementById('nombre_usuario-Edit');
    const correo = document.getElementById('correo-Edit');
    const rol = document.getElementById('rol-Edit');
    const estado = document.getElementById('estado_usuario-Edit');
    
    let isValid = true;
    
    // Remover clases de error previas
    [nombreUsuario, correo, rol, estado].forEach(field => {
        if (field) field.classList.remove('is-invalid');
    });
    
    // Validar nombre de usuario
    if (!nombreUsuario || !nombreUsuario.value.trim()) {
        if (nombreUsuario) {
            nombreUsuario.classList.add('is-invalid');
            document.getElementById('error-nombre_usuario-edit').textContent = 'El nombre de usuario es requerido';
        }
        isValid = false;
        shakeElement(nombreUsuario);
    }
    
    // Validar correo electrónico
    if (!correo || !correo.value.trim()) {
        if (correo) {
            correo.classList.add('is-invalid');
            document.getElementById('error-correo-edit').textContent = 'El correo electrónico es requerido';
        }
        isValid = false;
        shakeElement(correo);
    } else if (!isValidEmail(correo.value)) {
        correo.classList.add('is-invalid');
        document.getElementById('error-correo-edit').textContent = 'Ingresa un correo electrónico válido';
        isValid = false;
        shakeElement(correo);
    }
    
    // Validar rol
    if (!rol || !rol.value) {
        if (rol) {
            rol.classList.add('is-invalid');
            document.getElementById('error-rol-edit').textContent = 'Selecciona un rol';
        }
        isValid = false;
        shakeElement(rol);
    }
    
    // Validar estado
    if (!estado || !estado.value) {
        if (estado) {
            estado.classList.add('is-invalid');
            document.getElementById('error-estado-edit').textContent = 'Selecciona un estado';
        }
        isValid = false;
        shakeElement(estado);
    }
    
    // Validar contraseñas
    if (!validatePasswords()) {
        isValid = false;
    }
    
    return isValid;
};

// Función para validar contraseñas
window.validatePasswords = function() {
    const password = document.getElementById('password-Edit');
    const confirmPassword = document.getElementById('confirm_password-Edit');
    let isValid = true;
    
    if (password && confirmPassword) {
        // Remover clases de error previas
        password.classList.remove('is-invalid');
        confirmPassword.classList.remove('is-invalid');
        
        // Si se ingresó una nueva contraseña
        if (password.value.trim() !== '') {
            // Validar que la contraseña tenga al menos 6 caracteres
            if (password.value.length < 6) {
                password.classList.add('is-invalid');
                document.getElementById('error-password-edit').textContent = 'La contraseña debe tener al menos 6 caracteres';
                isValid = false;
                shakeElement(password);
            }
            
            // Validar que las contraseñas coincidan
            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                document.getElementById('error-confirm_password-edit').textContent = 'Las contraseñas no coinciden';
                isValid = false;
                shakeElement(confirmPassword);
            }
        } else {
            // Si no se ingresó contraseña, el campo de confirmación debe estar vacío
            if (confirmPassword.value.trim() !== '') {
                confirmPassword.classList.add('is-invalid');
                document.getElementById('error-confirm_password-edit').textContent = 'No puedes confirmar una contraseña vacía';
                isValid = false;
                shakeElement(confirmPassword);
            }
        }
    }
    
    return isValid;
};

// Función para validar email
window.isValidEmail = function(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

// Función para mostrar toast
window.showToast = function(message, type = 'success') {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? '#10b981' : (type === 'warning' ? '#f59e0b' : '#0281fe');
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: ${bgColor}; border-radius: 12px; box-shadow: 0 0 20px ${bgColor};">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}-fill me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
};

// Función para animación shake
window.shakeElement = function(element) {
    if (!element) return;
    element.style.animation = 'shake 0.5s ease';
    setTimeout(() => {
        element.style.animation = '';
    }, 500);
};

// Inicializar eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen
    const imagenInput = document.getElementById('imagen-Edit');
    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    this.classList.add('is-invalid');
                    document.getElementById('error-imagen-edit').textContent = 'La imagen no debe superar los 2MB';
                    this.value = '';
                    return;
                }
                
                if (!file.type.match('image.*')) {
                    this.classList.add('is-invalid');
                    document.getElementById('error-imagen-edit').textContent = 'Solo se permiten imágenes';
                    this.value = '';
                    return;
                }
                
                this.classList.remove('is-invalid');
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatar = document.getElementById('avatarUsuario');
                    if (avatar) avatar.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Hover effect para avatar
    const avatarPreview = document.querySelector('.avatar-preview');
    if (avatarPreview) {
        avatarPreview.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.avatar-overlay');
            if (overlay) overlay.style.opacity = '1';
        });
        avatarPreview.addEventListener('mouseleave', function() {
            const overlay = this.querySelector('.avatar-overlay');
            if (overlay) overlay.style.opacity = '0';
        });
    }
    
    // Validación en tiempo real para contraseñas
    const password = document.getElementById('password-Edit');
    const confirmPassword = document.getElementById('confirm_password-Edit');
    
    if (password) {
        password.addEventListener('input', function() {
            if (confirmPassword && confirmPassword.value) {
                if (this.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    document.getElementById('error-confirm_password-edit').textContent = 'Las contraseñas no coinciden';
                } else {
                    confirmPassword.classList.remove('is-invalid');
                }
            }
        });
    }
    
    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (password && password.value) {
                if (this.value !== password.value) {
                    this.classList.add('is-invalid');
                    document.getElementById('error-confirm_password-edit').textContent = 'Las contraseñas no coinciden';
                } else {
                    this.classList.remove('is-invalid');
                }
            }
        });
    }
    
    // Manejar el envío del formulario
    const form = document.getElementById('form_editUsuario');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (currentStep === 2) {
                if (validateStep2()) {
                    showToast('¡Usuario actualizado exitosamente!', 'success');
                    
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modal-editUsuario'));
                        if (modal) modal.hide();
                    }, 1500);
                }
            }
        });
    }
    
    // Reset al cerrar el modal
    const modal = document.getElementById('modal-editUsuario');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            currentStep = 1;
            
            const step1 = document.getElementById('step1-content');
            const step2 = document.getElementById('step2-content');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const progressFill = document.getElementById('progressFill');
            const form = document.getElementById('form_editUsuario');
            
            if (step1) step1.style.display = 'block';
            if (step2) step2.style.display = 'none';
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'block';
            if (submitBtn) submitBtn.style.display = 'none';
            
            // Resetear el formulario
            if (form) form.reset();
            
            // Remover clases de validación
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            // Resetear avatar a la imagen original
            const avatarImg = document.getElementById('avatarUsuario');
            if (avatarImg && avatarImg.dataset.originalSrc) {
                avatarImg.src = avatarImg.dataset.originalSrc;
            }
            
            // Resetear stepper
            const step1Indicator = document.getElementById('step1-indicator');
            const step2Indicator = document.getElementById('step2-indicator');
            
            if (step1Indicator) {
                const step1Circle = step1Indicator.querySelector('.step-circle');
                const step1Label = step1Indicator.querySelector('.step-label');
                if (step1Circle) {
                    step1Circle.style.background = '#dbeafe';
                    step1Circle.style.borderColor = '#0281fe';
                    step1Circle.innerHTML = '<span class="step-number fw-bold" style="color: #0281fe;">1</span>';
                }
                if (step1Label) step1Label.style.color = '#0281fe';
            }
            
            if (step2Indicator) {
                const step2Circle = step2Indicator.querySelector('.step-circle');
                const step2Label = step2Indicator.querySelector('.step-label');
                if (step2Circle) {
                    step2Circle.style.background = '#f1f5f9';
                    step2Circle.style.borderColor = '#cbd5e1';
                    step2Circle.innerHTML = '<span class="step-number fw-bold" style="color: #94a3b8;">2</span>';
                }
                if (step2Label) step2Label.style.color = '#94a3b8';
            }
            
            if (progressFill) progressFill.style.width = '0%';
        });
        
        // Guardar la URL original del avatar al cargar el modal
        modal.addEventListener('show.bs.modal', function () {
            const avatarImg = document.getElementById('avatarUsuario');
            if (avatarImg && avatarImg.src && !avatarImg.dataset.originalSrc) {
                avatarImg.dataset.originalSrc = avatarImg.src;
            }
        });
    }
    
    // Efecto de partículas interactivas que siguen al mouse
    const particleOverlay = document.getElementById('particle-overlay-light');
    if (particleOverlay) {
        document.addEventListener('mousemove', function(e) {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;
            
            particleOverlay.style.background = `
                radial-gradient(circle at ${x}% ${y}%, rgba(2, 129, 254, 0.08) 0%, transparent 25%),
                radial-gradient(circle at ${100 - x}% ${100 - y}%, rgba(56, 189, 248, 0.08) 0%, transparent 30%)
            `;
        });
    }
});
</script>

<!-- Bootstrap Icons y Bootstrap JS (necesarios para el funcionamiento) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

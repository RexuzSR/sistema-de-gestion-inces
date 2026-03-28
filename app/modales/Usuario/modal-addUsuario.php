<!-- Modal Agregar Usuario - Versión Profesional con Efectos Neón Azul -->
<div class="modal fade" id="modal-addUsuario" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-addUsuario">
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
            <div id="particle-overlay-light-add" style="
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
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bold mb-1" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.7rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(2, 129, 254, 0.3);
                            ">Registrar Nuevo Usuario</h4>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot-light"></span>
                                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                                    COMPLETA TODOS LOS CAMPOS REQUERIDOS
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
                <section>
                    <form id="form-usuario" class="row g-4 needs-validation" method="POST" action="/Sistema%20de%20Gestion%20Inces/app/controlador/Usuario/cargarUsuario.php" enctype="multipart/form-data">
                        
                        <!-- Tarjeta de Información Personal -->
                        <div class="col-12">
                            <div class="info-card-light mb-3" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(2,129,254,0.2), 0 0 20px rgba(2,129,254,0.3)'; this.style.borderColor='#0281fe'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <div class="d-flex align-items-center gap-2 mb-3">
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
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="primer_nombre" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Primer Nombre <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input class="form-control neon-input" type="text" name="primer_nombre" id="primer_nombre" required placeholder="Ej: Juan" style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <div class="error-feedback text-danger" id="error-primer_nombre" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="segundo_nombre" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Segundo Nombre <span style="color: #94a3b8;">(opcional)</span>
                                        </label>
                                        <input class="form-control neon-input" type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: Carlos" style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <div class="error-feedback text-danger" id="error-segundo_nombre" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="primer_apellido" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Primer Apellido <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input class="form-control neon-input" type="text" id="primer_apellido" name="primer_apellido" required placeholder="Ej: Pérez" style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <div class="error-feedback text-danger" id="error-primer_apellido" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="segundo_apellido" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Segundo Apellido <span style="color: #94a3b8;">(opcional)</span>
                                        </label>
                                        <input class="form-control neon-input" type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ej: González" style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <div class="error-feedback text-danger" id="error-segundo_apellido" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta de Datos de Cuenta -->
                        <div class="col-12">
                            <div class="info-card-light mb-3" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(16,185,129,0.2), 0 0 20px rgba(16,185,129,0.3)'; this.style.borderColor='#10b981'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <div class="d-flex align-items-center gap-2 mb-3">
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
                                    <h6 class="fw-semibold mb-0" style="color: #1e293b;">Datos de Cuenta</h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombre_usuario" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Nombre de Usuario <span style="color: #ef4444;">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="
                                                background: #f1f5f9;
                                                border: 1.5px solid #e2e8f0;
                                                border-right: none;
                                                border-radius: 12px 0 0 12px;
                                                color: #0281fe;
                                            "><i class="bi bi-person"></i></span>
                                            <input class="form-control neon-input" type="text" name="nombre_usuario" id="nombre_usuario" required placeholder="usuario" maxlength="64" style="
                                                border: 1.5px solid #e2e8f0;
                                                border-left: none;
                                                border-radius: 0 12px 12px 0;
                                                padding: 10px 14px;
                                                font-size: 0.95rem;
                                                transition: all 0.3s ease;
                                                background: #ffffff;
                                            " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        </div>
                                        <div class="error-feedback text-danger" id="error-nombre_usuario" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Contraseña <span style="color: #ef4444;">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="
                                                background: #f1f5f9;
                                                border: 1.5px solid #e2e8f0;
                                                border-right: none;
                                                border-radius: 12px 0 0 12px;
                                                color: #0281fe;
                                            "><i class="bi bi-lock"></i></span>
                                            <input class="form-control neon-input" type="password" id="password" name="password" required placeholder="••••••••" maxlength="64" style="
                                                border: 1.5px solid #e2e8f0;
                                                border-left: none;
                                                border-radius: 0 12px 12px 0;
                                                padding: 10px 14px;
                                                font-size: 0.95rem;
                                                transition: all 0.3s ease;
                                                background: #ffffff;
                                            " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        </div>
                                        <div class="error-feedback text-danger" id="error-password" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="correo" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Correo Electrónico <span style="color: #ef4444;">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="
                                                background: #f1f5f9;
                                                border: 1.5px solid #e2e8f0;
                                                border-right: none;
                                                border-radius: 12px 0 0 12px;
                                                color: #0281fe;
                                            "><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control neon-input" id="correo" name="correo" required placeholder="ejemplo@correo.com" style="
                                                border: 1.5px solid #e2e8f0;
                                                border-left: none;
                                                border-radius: 0 12px 12px 0;
                                                padding: 10px 14px;
                                                font-size: 0.95rem;
                                                transition: all 0.3s ease;
                                                background: #ffffff;
                                            " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        </div>
                                        <div class="error-feedback text-danger" id="error-correo" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta de Configuración -->
                        <div class="col-12">
                            <div class="info-card-light mb-3" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(139,92,246,0.2), 0 0 20px rgba(139,92,246,0.3)'; this.style.borderColor='#8b5cf6'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div style="
                                        width: 32px;
                                        height: 32px;
                                        background: #ede9fe;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                    </div>
                                    <h6 class="fw-semibold mb-0" style="color: #1e293b;">Configuración</h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="rol" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Rol <span style="color: #ef4444;">*</span>
                                        </label>
                                        <select class="form-select neon-select" id="rol" name="rol" required style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1), 0 0 20px rgba(139,92,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2), 0 0 25px rgba(139,92,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="">Seleccione</option>
                                            <option value="Administrador">Administrador</option>
                                            <option value="Apoyo">Apoyo</option>
                                        </select>
                                        <div class="error-feedback text-danger" id="error-rol" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="estado_usuario" class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                                            Estado <span style="color: #ef4444;">*</span>
                                        </label>
                                        <select class="form-select neon-select" id="estado_usuario" name="estado_usuario" required style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 12px;
                                            padding: 10px 14px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.1), 0 0 20px rgba(16,185,129,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.2), 0 0 25px rgba(16,185,129,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="">Seleccione</option>
                                            <option value="Activo" style="color: #10b981;">🟢 Activo</option>
                                            <option value="Inactivo" style="color: #ef4444;">🔴 Inactivo</option>
                                        </select>
                                        <div class="error-feedback text-danger" id="error-estado" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta de Avatar -->
                        <div class="col-12">
                            <div class="info-card-light mb-2" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <div class="d-flex align-items-center gap-2 mb-3">
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
                                    <h6 class="fw-semibold mb-0" style="color: #1e293b;">Foto de Perfil</h6>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="avatar-upload d-flex align-items-center gap-3">
                                            <div class="avatar-preview position-relative">
                                                <div id="avatarPlaceholder" style="
                                                    width: 80px;
                                                    height: 80px;
                                                    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
                                                    border-radius: 50%;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    border: 3px solid #0281fe;
                                                    box-shadow: 0 0 30px rgba(2, 129, 254, 0.3);
                                                ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                </div>
                                                <div class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded-circle" style="
                                                    background: rgba(2, 129, 254, 0.2);
                                                    backdrop-filter: blur(5px);
                                                    opacity: 0;
                                                    transition: opacity 0.3s;
                                                    cursor: pointer;
                                                    border-radius: 50%;
                                                " onclick="document.getElementById('imagen').click()">
                                                    <i class="bi bi-camera-fill text-white" style="font-size: 1.5rem; text-shadow: 0 0 10px rgba(255,255,255,0.5);"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input class="form-control neon-input" type="file" name="imagen" id="imagen" accept="image/jpeg, image/png, image/gif" style="
                                                    border: 1.5px solid #e2e8f0;
                                                    border-radius: 12px;
                                                    padding: 8px 12px;
                                                    font-size: 0.9rem;
                                                    transition: all 0.3s ease;
                                                    background: #ffffff;
                                                " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                                <div class="form-text" style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">
                                                    <i class="bi bi-info-circle me-1"></i> Si no seleccionas una imagen, se asignará una por defecto. Formatos: JPG, PNG, GIF. Máx 2MB.
                                                </div>
                                                <div class="error-feedback text-danger" id="error-imagen" style="font-size: 0.8rem; margin-top: 4px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de registro -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn futuristic-button-primary-light" id="actualizar-tabla" style="
                                background: linear-gradient(135deg, #0281fe, #38bdf8);
                                border: none;
                                padding: 12px 40px;
                                border-radius: 14px;
                                color: white;
                                font-weight: 600;
                                font-size: 1rem;
                                letter-spacing: 0.5px;
                                transition: all 0.3s ease;
                                box-shadow: 0 5px 20px rgba(2, 129, 254, 0.4);
                                display: inline-flex;
                                align-items: center;
                                gap: 10px;
                            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(2,129,254,0.7)'; this.style.background='linear-gradient(135deg, #38bdf8, #0281fe)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(2,129,254,0.4)'; this.style.background='linear-gradient(135deg, #0281fe, #38bdf8)'">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Registrar Nuevo Usuario
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <!-- FOOTER con efecto sutil -->
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
                    SISTEMA DE GESTIÓN DE USUARIOS • NUEVO REGISTRO
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Script específico para el modal de agregar usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen para avatar
    const imagenInput = document.getElementById('imagen');
    const avatarPlaceholder = document.getElementById('avatarPlaceholder');
    
    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    this.classList.add('is-invalid');
                    document.getElementById('error-imagen').textContent = 'La imagen no debe superar los 2MB';
                    this.value = '';
                    return;
                }
                
                // Validar tipo de archivo
                if (!file.type.match('image.*')) {
                    this.classList.add('is-invalid');
                    document.getElementById('error-imagen').textContent = 'Solo se permiten imágenes';
                    this.value = '';
                    return;
                }
                
                this.classList.remove('is-invalid');
                document.getElementById('error-imagen').textContent = '';
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPlaceholder) {
                        avatarPlaceholder.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                    }
                }
                reader.readAsDataURL(file);
            } else {
                // Resetear placeholder
                if (avatarPlaceholder) {
                    avatarPlaceholder.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    `;
                }
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
    
    // Validación del formulario antes de enviar
    const form = document.getElementById('form-usuario');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Limpiar errores anteriores
            document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');
            document.querySelectorAll('.neon-input, .neon-select').forEach(el => el.classList.remove('is-invalid'));
            
            // Validar campos requeridos
            const requiredFields = [
                { id: 'primer_nombre', name: 'Primer nombre' },
                { id: 'primer_apellido', name: 'Primer apellido' },
                { id: 'nombre_usuario', name: 'Nombre de usuario' },
                { id: 'password', name: 'Contraseña' },
                { id: 'correo', name: 'Correo electrónico' },
                { id: 'rol', name: 'Rol' },
                { id: 'estado_usuario', name: 'Estado' }
            ];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    if (element) {
                        element.classList.add('is-invalid');
                        const errorElement = document.getElementById(`error-${field.id}`);
                        if (errorElement) errorElement.textContent = `${field.name} es requerido`;
                    }
                    isValid = false;
                    shakeElement(element);
                }
            });
            
            // Validar formato de correo
            const correo = document.getElementById('correo');
            if (correo && correo.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(correo.value)) {
                    correo.classList.add('is-invalid');
                    document.getElementById('error-correo').textContent = 'Ingresa un correo electrónico válido';
                    isValid = false;
                    shakeElement(correo);
                }
            }
            
            // Validar nombre de usuario (máximo 64 caracteres)
            const nombreUsuario = document.getElementById('nombre_usuario');
            if (nombreUsuario && nombreUsuario.value.trim()) {
                if (nombreUsuario.value.length > 64) {
                    nombreUsuario.classList.add('is-invalid');
                    document.getElementById('error-nombre_usuario').textContent = 'El nombre de usuario no puede superar los 64 caracteres';
                    isValid = false;
                    shakeElement(nombreUsuario);
                }
            }

            // Validar contraseña (mínimo 6, máximo 64 caracteres)
            const password = document.getElementById('password');
            if (password && password.value.trim()) {
                if (password.value.length < 6) {
                    password.classList.add('is-invalid');
                    document.getElementById('error-password').textContent = 'La contraseña debe tener al menos 6 caracteres';
                    isValid = false;
                    shakeElement(password);
                } else if (password.value.length > 64) {
                    password.classList.add('is-invalid');
                    document.getElementById('error-password').textContent = 'La contraseña no puede superar los 64 caracteres';
                    isValid = false;
                    shakeElement(password);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showToast('Por favor complete todos los campos requeridos correctamente', 'warning');
            } else {
                showToast('Registrando usuario...', 'success');
            }
        });
    }
    
    // Efecto de partículas interactivas que siguen al mouse
    const particleOverlay = document.getElementById('particle-overlay-light-add');
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

// Función para animación shake
function shakeElement(element) {
    if (!element) return;
    element.style.animation = 'shake 0.5s ease';
    setTimeout(() => {
        element.style.animation = '';
    }, 500);
}

// Función para mostrar toast
function showToast(message, type = 'success') {
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
}

// Reset al cerrar el modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-addUsuario');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('form-usuario');
            if (form) form.reset();
            
            // Resetear avatar placeholder
            const avatarPlaceholder = document.getElementById('avatarPlaceholder');
            if (avatarPlaceholder) {
                avatarPlaceholder.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                `;
            }
            
            // Remover clases de validación
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            // Limpiar mensajes de error
            document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');
        });
    }
});
</script>

<!-- Los estilos compartidos están en el modal anterior, pero los incluyo aquí por si acaso -->
<style>
/* Reutilizar los mismos estilos de los modales anteriores */
/* Animaciones neón */
@keyframes neonPulse {
    0%, 100% { opacity: 0.3; filter: blur(8px); }
    50% { opacity: 0.5; filter: blur(12px); }
}

@keyframes neonLine {
    0% { opacity: 0.5; }
    50% { opacity: 1; }
    100% { opacity: 0.5; }
}

@keyframes iconGlow {
    0%, 100% { box-shadow: 0 8px 16px -4px rgba(2, 129, 254, 0.5), 0 0 15px rgba(2, 129, 254, 0.6); }
    50% { box-shadow: 0 8px 16px -4px rgba(2, 129, 254, 0.7), 0 0 25px rgba(2, 129, 254, 0.9); }
}

@keyframes laserScan {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(200%); }
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
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
    to { opacity: 1; transform: translateY(0); }
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

/* Invalid feedback */
.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

/* Avatar hover effect */
.avatar-preview {
    cursor: pointer;
    overflow: hidden;
    border-radius: 50%;
    position: relative;
}

.avatar-preview:hover .avatar-overlay {
    opacity: 1 !important;
}

.avatar-overlay {
    border-radius: 50%;
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

/* Botón primario */
.futuristic-button-primary-light {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.futuristic-button-primary-light:hover {
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-title {
        font-size: 1.3rem !important;
    }
}
</style>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
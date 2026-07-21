<!-- Modal Agregar Docente -->
<div class="modal fade" id="modal-addDocente" tabindex="-1" aria-labelledby="modal-addDocenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden" style="
            background: #ffffff;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(226, 232, 240, 0.8) inset, 0 0 30px rgba(56, 189, 248, 0.3);
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
                background: linear-gradient(45deg, #38bdf8, #0281fe, #7dd3fc, #38bdf8);
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
                border-bottom: 2px solid #38bdf8;
                border-radius: 24px 24px 0 0;
                position: relative;
                box-shadow: 0 4px 20px rgba(56, 189, 248, 0.2);
            ">
                <!-- Línea neón superior -->
                <div style="
                    position: absolute;
                    top: 0;
                    left: 10%;
                    right: 10%;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #38bdf8, #0281fe, #7dd3fc, #38bdf8, transparent);
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
                            background: linear-gradient(135deg, #38bdf8, #0281fe);
                            border-radius: 16px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 8px 16px -4px rgba(56, 189, 248, 0.5), 0 0 15px rgba(56, 189, 248, 0.6);
                            animation: iconGlow 2s ease-in-out infinite;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="modal-addDocenteLabel" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.7rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
                            ">Agregar Nuevo Docente</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot-light"></span>
                                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                                    FORMULARIO DE REGISTRO · COMPLETA LOS DATOS
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BOTÓN DE CERRAR -->
                    <button type="button" class="btn-close-custom-light" data-bs-dismiss="modal" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY DEL FORMULARIO -->
            <div class="modal-body p-4" style="background: #ffffff; position: relative; z-index: 1;">
                <form id="form-addDocente">
                    <!-- Tarjeta contenedora del formulario con estilo claro -->
                    <div class="info-card-modern-light" style="
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 30px;
                        padding: 30px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                        position: relative;
                        overflow: hidden;
                    " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                        <div class="card-shine-light"></div>
                        
                        <!-- Indicador de sección: Información Personal -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="icon-glow-light" style="
                                width: 40px;
                                height: 40px;
                                background: #dbeafe;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 5px 15px rgba(56, 189, 248, 0.3);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.2rem;">Información Personal</h6>
                            <span class="badge-neon" style="
                                background: linear-gradient(135deg, #38bdf8, #0281fe);
                                color: white;
                                font-size: 0.7rem;
                                padding: 4px 12px;
                                border-radius: 30px;
                                margin-left: 8px;
                                box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
                            ">DATOS PRINCIPALES</span>
                        </div>

                        <!-- Filas del formulario con estilos claros y neón -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cedula" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        CÉDULA <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" class="form-control neon-input" id="cedula" name="cedula" required maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);" placeholder="Ingrese su cédula" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="text-danger mt-1 fw-medium" id="cedula-repetida" style="display: none; font-size: 0.85rem; color: #dc2626 !important;">CÉDULA YA REGISTRADA</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono_numero" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        TELÉFONO <span style="color: #ef4444;">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select neon-select" id="prefijo_telefono" style="
                                            max-width: 100px;
                                            border: 1.5px solid #e2e8f0;
                                            border-right: none;
                                            border-radius: 16px 0 0 16px;
                                            padding: 12px 12px;
                                            background: #ffffff;
                                            transition: all 0.3s ease;
                                        " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="0412">0412</option>
                                            <option value="0414">0414</option>
                                            <option value="0416">0416</option>
                                            <option value="0424">0424</option>
                                            <option value="0426">0426</option>
                                            <option value="0293">0293</option>
                                            <option value="0212">0212</option>
                                        </select>
                                        <input type="text" class="form-control neon-input" id="telefono_numero" placeholder="1234567" maxlength="7" required style="
                                            border: 1.5px solid #e2e8f0;
                                            border-radius: 0 16px 16px 0;
                                            padding: 12px 18px;
                                            font-size: 0.95rem;
                                            transition: all 0.3s ease;
                                            background: #ffffff;
                                        " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    </div>
                                    <input type="hidden" name="telefono" id="telefono">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Debe ingresar los 7 dígitos del número.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primer_nombre" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        PRIMER NOMBRE <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" class="form-control neon-input" id="primer_nombre" name="primer_nombre" required placeholder="Ej: Juan" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="segundo_nombre" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        SEGUNDO NOMBRE
                                    </label>
                                    <input type="text" class="form-control neon-input" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: Carlos" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primer_apellido" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        PRIMER APELLIDO <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="text" class="form-control neon-input" id="primer_apellido" name="primer_apellido" required placeholder="Ej: Pérez" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="segundo_apellido" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        SEGUNDO APELLIDO
                                    </label>
                                    <input type="text" class="form-control neon-input" id="segundo_apellido" name="segundo_apellido" placeholder="Ej: González" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="correo" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        CORREO ELECTRÓNICO <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="email" class="form-control neon-input" id="correo" name="correo" required placeholder="ejemplo@correo.com" style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Correo electrónico inválido</div>
                                </div>
                            </div>
                            
                            <?php
                             $maxFechaNac = date('Y-m-d', strtotime('-15 years'));
                             $minFechaNac = date('Y-m-d', strtotime('-75 years'));
                             ?>
                             <div class="col-md-6">
                                 <div class="form-group">
                                     <label for="fechaNacimiento" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                         FECHA DE NACIMIENTO <span style="color: #ef4444;">*</span>
                                     </label>
                                     <input type="date" class="form-control neon-input" id="fechaNacimiento" name="fechaNacimiento" min="<?= $minFechaNac ?>" max="<?= $maxFechaNac ?>" required style="
                                         border: 1.5px solid #e2e8f0;
                                         border-radius: 16px;
                                         padding: 11px 18px;
                                         font-size: 0.95rem;
                                         transition: all 0.3s ease;
                                         background: #ffffff;
                                     " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                     <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Edad debe ser entre 15 y 75 años</div>
                                 </div>
                             </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="genero" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        GÉNERO <span style="color: #ef4444;">*</span>
                                    </label>
                                    <select class="form-select neon-select" id="genero" name="genero" required style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.1), 0 0 20px rgba(56,189,248,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 3px rgba(56,189,248,0.2), 0 0 25px rgba(56,189,248,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione un género</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_docente" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        TIPO DE DOCENTE <span style="color: #ef4444;">*</span>
                                    </label>
                                    <select class="form-select neon-select" id="tipo_docente" name="tipo_docente" required style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        transition: all 0.3s ease;
                                        background: #ffffff;
                                    " onmouseover="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.1), 0 0 20px rgba(2,129,254,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#0281fe'; this.style.boxShadow='0 0 0 3px rgba(2,129,254,0.2), 0 0 25px rgba(2,129,254,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        <option value="Fijo">Fijo</option>
                                        <option value="Contratado">Contratado</option>
                                        <option value="PDL">PDL</option>
                                        <option value="Voluntario">Voluntario</option>
                                    </select>
                                    <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione un tipo de docente</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edad" class="form-label" style="color: #475569; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 8px;">
                                        EDAD (Calculada)
                                    </label>
                                    <input type="text" class="form-control" id="edad" readonly style="
                                        border: 1.5px solid #e2e8f0;
                                        border-radius: 16px;
                                        padding: 12px 18px;
                                        font-size: 0.95rem;
                                        background: #f1f5f9;
                                        color: #64748b;
                                        cursor: not-allowed;
                                    ">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Confirmation (Hidden initially) -->
                    <div class="step-container" id="step-3-Docente" style="display: none;">
                        <h5 class="fw-semibold mb-4" style="color: #1e293b;">Confirmar registro</h5>
                        <div class="text-center py-4">
                            <div style="
                                width: 80px;
                                height: 80px;
                                background: #d1fae5;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 20px;
                                border: 2px solid #10b981;
                                box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <h4 class="fw-bold" style="color: #1e293b;">¡Todo listo!</h4>
                            <p style="color: #64748b;">Presiona guardar para registrar al docente.</p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER CON BOTONES FUTURISTAS -->
            <div class="modal-footer justify-content-between px-4 py-3" style="
                background: #f8fafc;
                border-top: 2px solid #38bdf8;
                border-radius: 0 0 24px 24px;
                box-shadow: 0 -4px 20px rgba(56, 189, 248, 0.2);
                position: relative;
                z-index: 2;
            ">
                <!-- Efecto de gradiente en el borde superior -->
                <div class="footer-glow-light"></div>
                
                <!-- Botón Cancelar -->
                <button type="button" class="btn futuristic-button-outline-light" data-bs-dismiss="modal" style="
                    background: transparent;
                    border: 1.5px solid #ef4444;
                    color: #ef4444;
                    padding: 12px 32px;
                    border-radius: 14px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.95rem;
                    letter-spacing: 0.5px;
                " onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 0 20px rgba(239,68,68,0.5)'" onmouseout="this.style.background='transparent'; this.style.color='#ef4444'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    CANCELAR
                </button>

                <!-- Botón Guardar (Submit) -->
                <button type="submit" form="form-addDocente" class="btn futuristic-button-primary-light" id="btn-addDocente-submit" style="
                    background: linear-gradient(135deg, #10b981, #059669);
                    border: none;
                    padding: 12px 35px;
                    border-radius: 14px;
                    color: white;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.95rem;
                    letter-spacing: 0.5px;
                    box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
                " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 30px rgba(16, 185, 129, 0.7)'; this.style.background='linear-gradient(135deg, #059669, #10b981)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(16, 185, 129, 0.4)'; this.style.background='linear-gradient(135deg, #10b981, #059669)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    GUARDAR DOCENTE
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para registro cruzado - Versión Clara con Neón -->
<div class="modal fade" id="modal-confirmCrossRegister" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 20px rgba(56, 189, 248, 0.3);
            position: relative;
            overflow: hidden;
        ">
            <!-- Efecto neón sutil -->
            <div style="
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(45deg, #38bdf8, #0281fe, #7dd3fc, #38bdf8);
                border-radius: 22px;
                opacity: 0.2;
                z-index: -1;
                filter: blur(8px);
                animation: neonPulse 3s ease-in-out infinite;
            "></div>

            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-semibold" style="color: #1e293b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; filter: drop-shadow(0 0 8px #38bdf8);">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <circle cx="12" cy="8" r="0.5" fill="#38bdf8" stroke="#38bdf8"></circle>
                    </svg>
                    Persona encontrada
                </h5>
            </div>
            <div class="modal-body py-3 px-4">
                <p id="crossRegisterMessage" class="mb-0 fs-6" style="color: #475569;"></p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn px-4" data-bs-dismiss="modal" id="btn-crossRegister-NO" style="
                    background: #f1f5f9;
                    color: #475569;
                    border: none;
                    border-radius: 10px;
                    padding: 8px 24px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='#e2e8f0'; this.style.boxShadow='0 0 15px rgba(100,116,139,0.5)'" onmouseout="this.style.background='#f1f5f9'; this.style.boxShadow='none'">Cancelar</button>
                <button type="button" class="btn px-4 text-white" id="btn-crossRegister-SI" style="
                    background: linear-gradient(135deg, #38bdf8, #0281fe);
                    border: none;
                    border-radius: 10px;
                    padding: 8px 24px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
                " onmouseover="this.style.boxShadow='0 0 25px rgba(56, 189, 248, 0.8)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 0 15px rgba(56, 189, 248, 0.5)'; this.style.transform='translateY(0)'">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- ESTILOS ADICIONALES -->
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
        box-shadow: 0 8px 16px -4px rgba(56, 189, 248, 0.5), 0 0 15px rgba(56, 189, 248, 0.6);
    }
    50% {
        box-shadow: 0 8px 16px -4px rgba(56, 189, 248, 0.7), 0 0 25px rgba(56, 189, 248, 0.9);
    }
}

@keyframes laserScan {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(200%); }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 10px #10b981;
    }
    50% {
        opacity: 0.8;
        transform: scale(1.2);
        box-shadow: 0 0 20px #10b981;
    }
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
    background: linear-gradient(180deg, transparent, rgba(56, 189, 248, 0.03), transparent);
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
    background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.1), transparent 70%);
}

.corner-brilliance-light.bottom-right {
    bottom: 0;
    right: 0;
    background: radial-gradient(circle at bottom right, rgba(2, 129, 254, 0.1), transparent 70%);
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

/* Estilo para inputs con efecto hover neón */
.neon-input {
    transition: all 0.3s ease !important;
}

.neon-input:hover {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1), 0 0 20px rgba(56, 189, 248, 0.4) !important;
}

.neon-input:focus {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2), 0 0 25px rgba(56, 189, 248, 0.5) !important;
    outline: none;
}

/* Estilo especial para campos opcionales */
.neon-input[placeholder*="Carlos"]:hover,
.neon-input[placeholder*="González"]:hover {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.1), 0 0 20px rgba(2, 129, 254, 0.4) !important;
}

.neon-input[placeholder*="Carlos"]:focus,
.neon-input[placeholder*="González"]:focus {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.2), 0 0 25px rgba(2, 129, 254, 0.5) !important;
}

/* Estilo para selects */
.neon-select {
    transition: all 0.3s ease !important;
}

.neon-select:hover {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1), 0 0 20px rgba(56, 189, 248, 0.4) !important;
}

.neon-select:focus {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2), 0 0 25px rgba(56, 189, 248, 0.5) !important;
    outline: none;
}

/* Estilo para selects específicos */
#tipo_docente.neon-select:hover {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.1), 0 0 20px rgba(2, 129, 254, 0.4) !important;
}

#tipo_docente.neon-select:focus {
    border-color: #0281fe !important;
    box-shadow: 0 0 0 3px rgba(2, 129, 254, 0.2), 0 0 25px rgba(2, 129, 254, 0.5) !important;
}

/* Estilo para opciones de select */
select option {
    background-color: #ffffff;
    color: #0f172a;
}

/* Botón de cerrar personalizado */
.btn-close-custom-light {
    background: transparent;
    border: 2px solid #38bdf8;
    border-radius: 12px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
    cursor: pointer;
}

.btn-close-custom-light:hover {
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 0 25px rgba(56, 189, 248, 0.8);
}

/* Botones futuristas claros */
.futuristic-button-primary-light {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.futuristic-button-primary-light:hover {
    transform: translateY(-3px);
}

.futuristic-button-outline-light {
    transition: all 0.3s ease;
}

.futuristic-button-outline-light:hover {
    transform: translateY(-3px);
}

/* Footer glow */
.footer-glow-light {
    position: absolute;
    top: -1px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #38bdf8, #0281fe, #38bdf8, transparent);
    filter: blur(2px);
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
    background: linear-gradient(180deg, #38bdf8, #0281fe);
    border-radius: 10px;
    box-shadow: 0 0 10px #38bdf8;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #0281fe, #38bdf8);
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

/* Ajustes para selects y inputs de fecha */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.5) sepia(1) hue-rotate(180deg) saturate(5);
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Estilo para el botón flotante */
.floating-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Animaciones de entrada */
.animate-on-show {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.5s forwards;
}
</style>

<!-- Font Awesome opcional -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
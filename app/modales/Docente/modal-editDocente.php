<!-- modal-editDocente -->
<div class="modal fade" id="modal-editDocente" tabindex="-1" aria-labelledby="modal-editDocenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden" style="
            background: radial-gradient(ellipse at top, #1a1f35 0%, #0a0c1a 100%);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(56, 189, 248, 0.15) inset, 0 0 30px rgba(56, 189, 248, 0.2);
            position: relative;
        ">
            <!-- Efecto de partículas de fondo -->
            <div style="
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: radial-gradient(circle at 20% 30%, rgba(56, 189, 248, 0.1) 0%, transparent 20%),
                                  radial-gradient(circle at 80% 70%, rgba(129, 140, 248, 0.1) 0%, transparent 25%),
                                  radial-gradient(circle at 40% 80%, rgba(56, 189, 248, 0.08) 0%, transparent 30%),
                                  radial-gradient(circle at 90% 20%, rgba(129, 140, 248, 0.08) 0%, transparent 35%);
                pointer-events: none;
                z-index: 0;
            "></div>

            <!-- HEADER con efecto glassmorphism avanzado (mismo estilo del ejemplo) -->
            <div class="modal-header py-4 px-4" style="
                background: rgba(10, 12, 26, 0.85);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border-bottom: 1px solid rgba(56, 189, 248, 0.25);
                position: relative;
                overflow: hidden;
                z-index: 1;
            ">
                <!-- Línea de luz superior animada -->
                <div style="
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 200%;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, #38bdf8, #818cf8, #a78bfa, #38bdf8, transparent);
                    animation: scanLine 4s linear infinite;
                    box-shadow: 0 0 20px #38bdf8;
                "></div>
                
                <!-- Efecto de brillo esquinas -->
                <div style="
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 150px;
                    height: 150px;
                    background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.2), transparent 70%);
                    pointer-events: none;
                "></div>
                <div style="
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 150px;
                    height: 150px;
                    background: radial-gradient(circle at bottom right, rgba(129, 140, 248, 0.2), transparent 70%);
                    pointer-events: none;
                "></div>
                
                <div class="d-flex justify-content-between align-items-center w-100 position-relative" style="z-index: 2;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Icono futurista 3D (Lápiz de edición) -->
                        <div style="
                            width: 55px;
                            height: 55px;
                            background: linear-gradient(135deg, #38bdf8, #818cf8, #a78bfa);
                            border-radius: 16px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 10px 25px rgba(56, 189, 248, 0.5), 0 0 0 2px rgba(255, 255, 255, 0.1) inset;
                            transform: rotate(5deg);
                            transition: transform 0.3s ease;
                        " onmouseover="this.style.transform='rotate(0deg) scale(1.1)'" onmouseout="this.style.transform='rotate(5deg)'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="modal-editDocenteLabel" style="
                                background: linear-gradient(135deg, #ffffff, #cbd5e1, #94a3b8);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.7rem;
                                text-shadow: 0 2px 10px rgba(56, 189, 248, 0.3);
                            ">Editar Docente</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span style="
                                    display: inline-block;
                                    width: 8px;
                                    height: 8px;
                                    background: #10b981;
                                    border-radius: 50%;
                                    box-shadow: 0 0 10px #10b981;
                                    animation: pulse 2s infinite;
                                "></span>
                                <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                                    MODO EDICIÓN · ACTUALIZAR INFORMACIÓN
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BOTÓN DE CERRAR CORREGIDO -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="
                        background: transparent;
                        border: 1.5px solid rgba(56, 189, 248, 0.5);
                        border-radius: 12px;
                        width: 38px;
                        height: 38px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.3s ease;
                        position: relative;
                        opacity: 1;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.transform='rotate(90deg) scale(1.1)'; this.style.boxShadow='0 0 20px rgba(56,189,248,0.5)'" onmouseout="this.style.borderColor='rgba(56,189,248,0.5)'; this.style.transform='rotate(0deg) scale(1)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.3)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY DEL FORMULARIO DE EDICIÓN -->
            <div class="modal-body p-4" style="background: transparent; position: relative; z-index: 1;">
                <form id="form-editDocente">
                    <input type="hidden" id="idDocente" name="id_docente">

                    <!-- SECCIÓN 1: INFORMACIÓN PERSONAL (con colores azules) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="
                                width: 36px;
                                height: 36px;
                                background: linear-gradient(135deg, #38bdf8, #2563eb);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 5px 12px rgba(56, 189, 248, 0.4);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <h6 class="fw-semibold mb-0" style="color: #f1f5f9; font-size: 1.1rem;">Información Personal</h6>
                            <span style="
                                background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(129, 140, 248, 0.15));
                                color: #38bdf8;
                                font-size: 0.65rem;
                                padding: 3px 10px;
                                border-radius: 30px;
                                border: 1px solid rgba(56, 189, 248, 0.3);
                            ">DATOS BASE</span>
                        </div>
                        
                        <!-- Tarjeta contenedora para información personal -->
                        <div style="
                            background: rgba(15, 23, 42, 0.4);
                            backdrop-filter: blur(10px);
                            -webkit-backdrop-filter: blur(10px);
                            border: 1px solid rgba(56, 189, 248, 0.2);
                            border-radius: 24px;
                            padding: 22px;
                            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                        ">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="cedula-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            CÉDULA <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="cedula-Edit" name="cedula" required maxlength="8" style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(56, 189, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 20px rgba(56, 189, 248, 0.5)'" onblur="this.style.borderColor='rgba(56, 189, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-cedula-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="telefono_numero-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">TELÉFONO</label>
                                        <div class="input-group">
                                            <select class="form-select" id="prefijo_telefono-Edit" style="
                                                max-width: 100px;
                                                background: rgba(10, 12, 26, 0.7);
                                                border: 1.5px solid rgba(129, 140, 248, 0.3);
                                                border-right: none;
                                                border-radius: 14px 0 0 14px;
                                                color: #f1f5f9;
                                                font-size: 0.95rem;
                                            " onfocus="this.style.borderColor='#818cf8';" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)';">
                                                <option value="0412">0412</option>
                                                <option value="0414">0414</option>
                                                <option value="0416">0416</option>
                                                <option value="0424">0424</option>
                                                <option value="0426">0426</option>
                                                <option value="0293">0293</option>
                                                <option value="0212">0212</option>
                                            </select>
                                            <input type="text" class="form-control" id="telefono_numero-Edit" placeholder="1234567" maxlength="7" style="
                                                background: rgba(10, 12, 26, 0.7);
                                                border: 1.5px solid rgba(129, 140, 248, 0.3);
                                                border-radius: 0 14px 14px 0;
                                                padding: 10px 16px;
                                                color: #f1f5f9;
                                                font-size: 0.95rem;
                                            " onfocus="this.style.borderColor='#818cf8';" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)';">
                                        </div>
                                        <input type="hidden" name="telefono" id="telefono-Edit">
                                        <div class="invalid-feedback" id="error-telefono-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="primer_nombre-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            PRIMER NOMBRE <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="primer_nombre-Edit" name="primer_nombre" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(56, 189, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 20px rgba(56, 189, 248, 0.5)'" onblur="this.style.borderColor='rgba(56, 189, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-primer_nombre-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="segundo_nombre-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">SEGUNDO NOMBRE</label>
                                        <input type="text" class="form-control" id="segundo_nombre-Edit" name="segundo_nombre" style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(129, 140, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#818cf8'; this.style.boxShadow='0 0 20px rgba(129, 140, 248, 0.5)'" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-segundo_nombre-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="primer_apellido-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            PRIMER APELLIDO <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="primer_apellido-Edit" name="primer_apellido" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(56, 189, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 20px rgba(56, 189, 248, 0.5)'" onblur="this.style.borderColor='rgba(56, 189, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-primer_apellido-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="segundo_apellido-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">SEGUNDO APELLIDO</label>
                                        <input type="text" class="form-control" id="segundo_apellido-Edit" name="segundo_apellido" style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(129, 140, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#818cf8'; this.style.boxShadow='0 0 20px rgba(129, 140, 248, 0.5)'" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-segundo_apellido-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: INFORMACIÓN DE CONTACTO Y DETALLES -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="
                                width: 36px;
                                height: 36px;
                                background: linear-gradient(135deg, #818cf8, #4f46e5);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 5px 12px rgba(129, 140, 248, 0.4);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="2.18"></rect>
                                    <line x1="2" y1="6" x2="22" y2="6"></line>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <line x1="2" y1="18" x2="22" y2="18"></line>
                                </svg>
                            </div>
                            <h6 class="fw-semibold mb-0" style="color: #f1f5f9; font-size: 1.1rem;">Contacto y Detalles</h6>
                            <span style="
                                background: linear-gradient(135deg, rgba(129, 140, 248, 0.15), rgba(79, 70, 229, 0.15));
                                color: #818cf8;
                                font-size: 0.65rem;
                                padding: 3px 10px;
                                border-radius: 30px;
                                border: 1px solid rgba(129, 140, 248, 0.3);
                            ">INFORMACIÓN COMPLEMENTARIA</span>
                        </div>
                        
                        <!-- Tarjeta contenedora para contacto y detalles -->
                        <div style="
                            background: rgba(15, 23, 42, 0.4);
                            backdrop-filter: blur(10px);
                            -webkit-backdrop-filter: blur(10px);
                            border: 1px solid rgba(129, 140, 248, 0.2);
                            border-radius: 24px;
                            padding: 22px;
                            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                        ">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="correo-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            CORREO ELECTRÓNICO <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="correo-Edit" name="correo" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(56, 189, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 20px rgba(56, 189, 248, 0.5)'" onblur="this.style.borderColor='rgba(56, 189, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-correo-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="fechaNacimiento-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            FECHA DE NACIMIENTO <span style="color: #ef4444;">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="fechaNacimiento-Edit" name="fecha_nacimiento" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(129, 140, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#818cf8'; this.style.boxShadow='0 0 20px rgba(129, 140, 248, 0.5)'" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                        <div class="invalid-feedback" id="error-fechaNacimiento-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            GÉNERO <span style="color: #ef4444;">*</span>
                                        </label>
                                        <div class="d-flex gap-4 p-2" style="
                                            background: rgba(10, 12, 26, 0.5);
                                            border-radius: 14px;
                                            border: 1px solid rgba(56, 189, 248, 0.2);
                                            padding: 8px 15px !important;
                                        ">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genero" id="genero-masculino-Edit" value="Masculino" style="
                                                    background: rgba(10, 12, 26, 0.8);
                                                    border-color: #38bdf8;
                                                    box-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
                                                ">
                                                <label class="form-check-label" for="genero-masculino-Edit" style="color: #e2e8f0;">Masculino</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genero" id="genero-femenino-Edit" value="Femenino" style="
                                                    background: rgba(10, 12, 26, 0.8);
                                                    border-color: #38bdf8;
                                                    box-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
                                                ">
                                                <label class="form-check-label" for="genero-femenino-Edit" style="color: #e2e8f0;">Femenino</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="error-genero" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="tipo_docente-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            TIPO DE DOCENTE <span style="color: #ef4444;">*</span>
                                        </label>
                                        <select class="form-select" id="tipo_docente-Edit" name="tipo_docente" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(56, 189, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 20px rgba(56, 189, 248, 0.5)'" onblur="this.style.borderColor='rgba(56, 189, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                            <option value="" style="background: #0f172a; color: #e2e8f0;">Seleccione...</option>
                                            <option value="Fijo" style="background: #0f172a; color: #e2e8f0;">Fijo</option>
                                            <option value="Contratado" style="background: #0f172a; color: #e2e8f0;">Contratado</option>
                                            <option value="PDL" style="background: #0f172a; color: #e2e8f0;">PDL</option>
                                            <option value="Voluntario" style="background: #0f172a; color: #e2e8f0;">Voluntario</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-tipo_docente-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="edad-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">EDAD</label>
                                        <input type="text" class="form-control" id="edad-Edit" readonly style="
                                            background: rgba(10, 12, 26, 0.4);
                                            border: 1.5px solid rgba(148, 163, 184, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #94a3b8;
                                            font-size: 0.95rem;
                                            cursor: not-allowed;
                                        ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="futuristic-input">
                                        <label for="estado_docente-Edit" class="form-label" style="color: #94a3b8; font-weight: 500; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 0.85rem;">
                                            ESTADO <span style="color: #ef4444;">*</span>
                                        </label>
                                        <select class="form-select" id="estado_docente-Edit" name="estado_docente" required style="
                                            background: rgba(10, 12, 26, 0.7);
                                            border: 1.5px solid rgba(129, 140, 248, 0.3);
                                            border-radius: 14px;
                                            padding: 10px 16px;
                                            color: #f1f5f9;
                                            font-size: 0.95rem;
                                            backdrop-filter: blur(5px);
                                            transition: all 0.3s ease;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                                        " onfocus="this.style.borderColor='#818cf8'; this.style.boxShadow='0 0 20px rgba(129, 140, 248, 0.5)'" onblur="this.style.borderColor='rgba(129, 140, 248, 0.3)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)'">
                                            <option value="" style="background: #0f172a; color: #e2e8f0;">Seleccione...</option>
                                            <option value="Disponible" style="background: #0f172a; color: #e2e8f0;">Disponible</option>
                                            <option value="En formación" style="background: #0f172a; color: #e2e8f0;">En formación</option>
                                            <option value="Asignado" style="background: #0f172a; color: #e2e8f0;">Asignado</option>
                                            <option value="Inactivo" style="background: #0f172a; color: #e2e8f0;">Inactivo</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-estado_docente-Edit" style="color: #fca5a5;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER CON BOTONES FUTURISTAS (mismo estilo del ejemplo) -->
            <div class="modal-footer justify-content-between px-4 py-3" style="
                background: rgba(10, 12, 26, 0.9);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border-top: 1px solid rgba(56, 189, 248, 0.25);
                position: relative;
                z-index: 2;
            ">
                <!-- Efecto de gradiente en el borde -->
                <div style="
                    position: absolute;
                    top: -1px;
                    left: 10%;
                    right: 10%;
                    height: 1px;
                    background: linear-gradient(90deg, transparent, #38bdf8, #818cf8, #38bdf8, transparent);
                "></div>
                
                <!-- Botón Cancelar -->
                <button type="button" class="btn futuristic-button-sm" data-bs-dismiss="modal" style="
                    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
                    border: 1px solid #ef4444;
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
                    box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
                " onmouseover="this.style.background='linear-gradient(135deg, #ef4444, #dc2626)'; this.style.color='white'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px rgba(239, 68, 68, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1))'; this.style.color='#ef4444'; this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(239, 68, 68, 0.2)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    CANCELAR
                </button>

                <!-- Botón Actualizar -->
                <button type="submit" form="form-editDocente" class="btn futuristic-button-sm" id="btn-editDocente-submit" style="
                    background: linear-gradient(135deg, rgba(56, 189, 248, 0.1), rgba(37, 99, 235, 0.1));
                    border: 1px solid #38bdf8;
                    color: #38bdf8;
                    padding: 12px 35px;
                    border-radius: 14px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.95rem;
                    letter-spacing: 0.5px;
                    box-shadow: 0 5px 15px rgba(56, 189, 248, 0.2);
                " onmouseover="this.style.background='linear-gradient(135deg, #38bdf8, #2563eb)'; this.style.color='white'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px rgba(56, 189, 248, 0.4)'" onmouseout="this.style.background='linear-gradient(135deg, rgba(56, 189, 248, 0.1), rgba(37, 99, 235, 0.1))'; this.style.color='#38bdf8'; this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(56, 189, 248, 0.2)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                    </svg>
                    ACTUALIZAR DATOS
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ESTILOS ADICIONALES (mismos del ejemplo) -->
<style>
/* Animaciones */
@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.2); box-shadow: 0 0 20px #10b981; }
}

/* Estilo para los radio buttons */
.form-check-input:checked {
    background-color: #38bdf8 !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 15px rgba(56, 189, 248, 0.6) !important;
}

/* Estilo para el campo de fecha */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) hue-rotate(180deg) saturate(5);
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Estilo para opciones del select */
select option {
    background-color: #0f172a;
    color: #e2e8f0;
    padding: 10px;
}

/* Efecto hover para las tarjetas */
.futuristic-input {
    transition: transform 0.2s ease;
}

.futuristic-input:hover {
    transform: translateY(-2px);
}

/* Scroll personalizado */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.4);
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #38bdf8, #818cf8);
    border-radius: 10px;
    box-shadow: 0 0 10px #38bdf8;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #818cf8, #38bdf8);
}
</style>
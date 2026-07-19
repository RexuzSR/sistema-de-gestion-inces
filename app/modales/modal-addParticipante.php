<!-- Modal Agregar Participante -->
<div class="modal fade" id="modal-addParticipante" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-addParticipante">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden" style="
            background: #ffffff;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(226, 232, 240, 0.8) inset, 0 0 20px rgba(59, 130, 246, 0.3);
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
                background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
                border-radius: 26px;
                opacity: 0.3;
                z-index: -1;
                filter: blur(8px);
                animation: neonPulse 3s ease-in-out infinite;
            "></div>

            <!-- HEADER con acentos neón -->
            <div class="modal-header py-4 px-4" style="
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 2px solid #3b82f6;
                border-radius: 24px 24px 0 0;
                position: relative;
                box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
            ">
                <!-- Línea neón superior -->
                <div style="
                    position: absolute;
                    top: 0;
                    left: 10%;
                    right: 10%;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #3b82f6, #8b5cf6, #ec4899, #3b82f6, transparent);
                    filter: blur(2px);
                    animation: neonLine 3s linear infinite;
                "></div>

                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Icono con efecto neón -->
                        <div style="
                            width: 52px;
                            height: 52px;
                            background: linear-gradient(135deg, #2563eb, #3b82f6);
                            border-radius: 16px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.5), 0 0 15px rgba(59, 130, 246, 0.6);
                            animation: iconGlow 2s ease-in-out infinite;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bold mb-1" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.6rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
                            ">Nuevo Participante</h4>
                            <p style="color: #64748b; font-size: 0.9rem; margin: 0; display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; box-shadow: 0 0 10px #3b82f6; animation: pulse 2s infinite;"></span>
                                Complete la información del participante
                            </p>
                        </div>
                    </div>
                    
                    <!-- Botón cerrar con efecto neón -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="
                        background: transparent;
                        border: 2px solid #3b82f6;
                        font-size: 1.2rem;
                        width: 36px;
                        height: 36px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
                        transition: all 0.3s ease;
                    " onmouseover="this.style.boxShadow='0 0 25px rgba(59, 130, 246, 0.8)'; this.style.transform='rotate(90deg) scale(1.1)'" onmouseout="this.style.boxShadow='0 0 15px rgba(59, 130, 246, 0.5)'; this.style.transform='rotate(0deg) scale(1)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY PROFESIONAL -->
            <div class="modal-body p-4" style="background: #ffffff;">
                <form id="form-participante" class="row g-4" method="POST">

                    <!-- SECCIÓN DATOS PERSONALES -->
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="
                                width: 32px;
                                height: 32px;
                                background: linear-gradient(135deg, #dbeafe, #bfdbfe);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.1rem;">Datos Personales</h6>
                            <span style="
                                background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                                color: white;
                                font-size: 0.7rem;
                                padding: 4px 12px;
                                border-radius: 30px;
                                margin-left: 8px;
                                box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
                            ">Información básica</span>
                        </div>
                    </div>

                    <!-- CÉDULA -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Cédula <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="input-group">
                            <select class="form-select neon-input" id="nacionalidad" name="nacionalidad" style="
                                max-width: 80px;
                                border: 1.5px solid #e2e8f0;
                                border-radius: 12px 0 0 12px;
                                padding: 10px 12px;
                                font-size: 0.95rem;
                                transition: all 0.3s ease;
                                background: #ffffff;
                            " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <option value="V" selected>V</option>
                                <option value="E">E</option>
                            </select>
                            <input type="number" class="form-control neon-input" id="cedula" name="cedula" required placeholder="Ej: 12345678" style="
                                border: 1.5px solid #e2e8f0;
                                border-radius: 0 12px 12px 0;
                                padding: 10px 16px;
                                font-size: 0.95rem;
                                transition: all 0.3s ease;
                                background: #ffffff;
                            " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>
                        <div class="invalid-feedback" id="error-cedula" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Cédula inválida (7-8 dígitos)</div>
                        <div class="text-danger mt-1 fw-medium" id="cedula-repetida" style="display: none; font-size: 0.8rem; color: #dc2626 !important;">CÉDULA YA REGISTRADA</div>
                    </div>

                    <!-- TELÉFONO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Teléfono <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="input-group">
                            <select class="form-select neon-input" id="prefijo_telefono" name="prefijo_telefono" style="
                                max-width: 100px;
                                border: 1.5px solid #e2e8f0;
                                border-radius: 12px 0 0 12px;
                                padding: 10px 12px;
                                background: #ffffff;
                                border-right: none;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <option value="0412">0412</option>
                                <option value="0414">0414</option>
                                <option value="0416">0416</option>
                                <option value="0424">0424</option>
                                <option value="0426">0426</option>
                                <option value="0293">0293</option>
                            </select>
                            <input type="tel" class="form-control neon-input" id="telefono_numero" name="telefono_numero" required placeholder="1234567" maxlength="7" style="
                                border: 1.5px solid #e2e8f0;
                                border-radius: 0 12px 12px 0;
                                padding: 10px 16px;
                                font-size: 0.95rem;
                                border-left: none;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <input type="hidden" id="telefono" name="telefono">
                        </div>
                        <div class="invalid-feedback" id="error-telefono" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Debe ingresar los 7 dígitos del número.</div>
                    </div>

                    <!-- PRIMER NOMBRE -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Primer Nombre <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" class="form-control neon-input" id="primer_nombre" name="primer_nombre" required placeholder="Ej: Juan" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-primer_nombre" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- SEGUNDO NOMBRE -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Segundo Nombre
                        </label>
                        <input type="text" class="form-control neon-input" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: Carlos" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1), 0 0 20px rgba(139,92,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2), 0 0 25px rgba(139,92,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-segundo_nombre" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- PRIMER APELLIDO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Primer Apellido <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" class="form-control neon-input" id="primer_apellido" name="primer_apellido" required placeholder="Ej: Pérez" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-primer_apellido" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- SEGUNDO APELLIDO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Segundo Apellido
                        </label>
                        <input type="text" class="form-control neon-input" id="segundo_apellido" name="segundo_apellido" placeholder="Ej: González" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1), 0 0 20px rgba(139,92,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2), 0 0 25px rgba(139,92,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-segundo_apellido" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- FECHA DE NACIMIENTO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Fecha de Nacimiento <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" class="form-control neon-input" id="fecha_nacimiento" name="fecha_nacimiento" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 9px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-fecha_nacimiento" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Edad debe ser entre 15 y 75 años</div>
                    </div>

                    <!-- GÉNERO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Género <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="d-flex gap-4 p-3" style="
                            background: #f8fafc;
                            border-radius: 12px;
                            border: 1.5px solid #e2e8f0;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.3)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <div class="form-check">
                                <input class="form-check-input neon-radio" type="radio" name="genero" id="genero_masculino" value="Masculino" required style="
                                    border: 2px solid #cbd5e1;
                                    width: 18px;
                                    height: 18px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 15px rgba(59,130,246,0.5)'" onmouseout="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                                <label class="form-check-label" for="genero_masculino" style="color: #334155; cursor: pointer;">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input neon-radio" type="radio" name="genero" id="genero_femenino" value="Femenino" style="
                                    border: 2px solid #cbd5e1;
                                    width: 18px;
                                    height: 18px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                " onmouseover="this.style.borderColor='#ec4899'; this.style.boxShadow='0 0 15px rgba(236,72,153,0.5)'" onmouseout="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                                <label class="form-check-label" for="genero_femenino" style="color: #334155; cursor: pointer;">Femenino</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-genero" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione un género</div>
                    </div>

                    <!-- DISCAPACIDAD -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Discapacidad <span style="color: #ef4444;">*</span>
                        </label>
                        <select class="form-select neon-input" id="id_discapacidad" name="id_discapacidad" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            background: #ffffff;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1), 0 0 20px rgba(139,92,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2), 0 0 25px rgba(139,92,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1">Ninguna</option>
                            <option value="2">Visual</option>
                            <option value="3">Auditiva</option>
                            <option value="4">Motora</option>
                            <option value="5">Cognitiva</option>
                        </select>
                        <div class="invalid-feedback" id="error-id_discapacidad" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;"></div>
                    </div>

                    <!-- SECCIÓN DATOS INSCRIPCIÓN -->
                    <div class="col-12 mt-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="
                                width: 32px;
                                height: 32px;
                                background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 0 15px rgba(100, 116, 139, 0.3);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.1rem;">Datos de Inscripción</h6>
                            <span style="
                                background: linear-gradient(135deg, #64748b, #475569);
                                color: white;
                                font-size: 0.7rem;
                                padding: 4px 12px;
                                border-radius: 30px;
                                margin-left: 8px;
                                box-shadow: 0 0 15px rgba(100, 116, 139, 0.5);
                            ">Contacto</span>
                        </div>
                    </div>

                    <!-- CORREO ELECTRÓNICO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Correo Electrónico <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" class="form-control neon-input" id="correo" name="correo" required placeholder="ejemplo@correo.com" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1), 0 0 20px rgba(59,130,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2), 0 0 25px rgba(59,130,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-correo" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Correo electrónico inválido</div>
                    </div>

                    <!-- GRADO DE INSTRUCCIÓN -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Grado de Instrucción <span style="color: #ef4444;">*</span>
                        </label>
                        <select class="form-select neon-input" id="grado_institucion" name="grado_institucion" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            background: #ffffff;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1), 0 0 20px rgba(139,92,246,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2), 0 0 25px rgba(139,92,246,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <option value="" disabled selected>Seleccione su nivel educativo</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Bachillerato">Bachiller</option>
                            <option value="Universitario">Universitario</option>
                        </select>
                        <div class="invalid-feedback" id="error-grado_institucion" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;"></div>
                    </div>

                    <!-- Campo oculto para curso -->
                    <input type="hidden" id="id_curso" name="id_curso" value="">

                    <!-- BOTÓN DE REGISTRO CON EFECTO NEÓN -->
                    <div class="col-12 text-center mt-5">
                        <button type="submit" class="btn neon-button" style="
                            background: linear-gradient(135deg, #2563eb, #3b82f6, #8b5cf6);
                            border: none;
                            padding: 12px 40px;
                            border-radius: 14px;
                            color: white;
                            font-weight: 600;
                            font-size: 1rem;
                            letter-spacing: 0.3px;
                            transition: all 0.3s ease;
                            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.5), 0 0 20px rgba(59, 130, 246, 0.4);
                            position: relative;
                            overflow: hidden;
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px -4px rgba(37, 99, 235, 0.7), 0 0 30px rgba(59, 130, 246, 0.7)'; this.style.background='linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -4px rgba(37, 99, 235, 0.5), 0 0 20px rgba(59, 130, 246, 0.4)'; this.style.background='linear-gradient(135deg, #2563eb, #3b82f6, #8b5cf6)'">
                            <span style="display: flex; align-items: center; justify-content: center; gap: 10px; position: relative; z-index: 2;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Registrar Participante
                            </span>
                            <!-- Efecto de brillo en hover -->
                            <div style="
                                position: absolute;
                                top: -50%;
                                left: -50%;
                                width: 200%;
                                height: 200%;
                                background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
                                opacity: 0;
                                transition: opacity 0.5s ease;
                                transform: translate(-50%, -50%) scale(0.5);
                                pointer-events: none;
                                z-index: 1;
                            " class="button-glow"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para registro cruzado - Versión con Neón -->
<div class="modal fade" id="modal-confirmCrossRegister" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 20px rgba(59, 130, 246, 0.3);
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
                background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
                border-radius: 22px;
                opacity: 0.2;
                z-index: -1;
                filter: blur(8px);
                animation: neonPulse 3s ease-in-out infinite;
            "></div>

            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-semibold" style="color: #1e293b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px; filter: drop-shadow(0 0 8px #3b82f6);">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <circle cx="12" cy="8" r="0.5" fill="#3b82f6" stroke="#3b82f6"></circle>
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
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                    border: none;
                    border-radius: 10px;
                    padding: 8px 24px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
                " onmouseover="this.style.boxShadow='0 0 25px rgba(59, 130, 246, 0.8)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 0 15px rgba(59, 130, 246, 0.5)'; this.style.transform='translateY(0)'">Aceptar</button>
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
        box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.5), 0 0 15px rgba(59, 130, 246, 0.6);
    }
    50% {
        box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.7), 0 0 25px rgba(59, 130, 246, 0.9);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 10px #3b82f6;
    }
    50% {
        opacity: 0.8;
        transform: scale(1.2);
        box-shadow: 0 0 20px #3b82f6;
    }
}

.modal.fade .modal-dialog {
    transform: scale(0.98) translateY(-10px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Estilo para inputs con efecto hover neón */
.neon-input {
    transition: all 0.3s ease !important;
}

.neon-input:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 20px rgba(59, 130, 246, 0.4) !important;
}

.neon-input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2), 0 0 25px rgba(59, 130, 246, 0.5) !important;
    outline: none;
}

/* Estilo especial para campos opcionales (segundo nombre, segundo apellido) */
.neon-input[placeholder*="Carlos"]:hover,
.neon-input[placeholder*="González"]:hover {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1), 0 0 20px rgba(139, 92, 246, 0.4) !important;
}

.neon-input[placeholder*="Carlos"]:focus,
.neon-input[placeholder*="González"]:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2), 0 0 25px rgba(139, 92, 246, 0.5) !important;
}

/* Estilo para radios con efecto neón */
.neon-radio:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5) !important;
}

/* Estilo para radios checked */
.form-check-input:checked {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5) !important;
}

/* Efecto hover para el botón */
.neon-button:hover .button-glow {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

/* Scroll personalizado con efecto neón */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #3b82f6, #8b5cf6);
    border-radius: 10px;
    box-shadow: 0 0 10px #3b82f6;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #8b5cf6, #3b82f6);
}

/* Efecto hover para selects */
select.neon-input:hover {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1), 0 0 20px rgba(139, 92, 246, 0.4) !important;
}

select.neon-input:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2), 0 0 25px rgba(139, 92, 246, 0.5) !important;
}

/* Efecto hover para el contenedor de género */
.d-flex.gap-4.p-3:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 20px rgba(59, 130, 246, 0.3) !important;
}
</style>

<!-- Font Awesome opcional -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
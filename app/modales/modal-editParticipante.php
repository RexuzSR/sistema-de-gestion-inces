<!-- Modal Editar Participante -->
<div class="modal fade" id="modal-editParticipante" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-editParticipante">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 overflow-hidden" style="
            background: #ffffff;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(226, 232, 240, 0.8) inset, 0 0 20px rgba(245, 158, 11, 0.3);
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
                background: linear-gradient(45deg, #f59e0b, #f97316, #ef4444, #f59e0b);
                border-radius: 26px;
                opacity: 0.3;
                z-index: -1;
                filter: blur(8px);
                animation: neonPulse 3s ease-in-out infinite;
            "></div>

            <!-- HEADER con acentos neón naranja -->
            <div class="modal-header py-4 px-4" style="
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 2px solid #f59e0b;
                border-radius: 24px 24px 0 0;
                position: relative;
                box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2);
            ">
                <!-- Línea neón superior -->
                <div style="
                    position: absolute;
                    top: 0;
                    left: 10%;
                    right: 10%;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #f59e0b, #f97316, #ef4444, #f59e0b, transparent);
                    filter: blur(2px);
                    animation: neonLine 3s linear infinite;
                "></div>

                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Icono con efecto neón naranja -->
                        <div style="
                            width: 52px;
                            height: 52px;
                            background: linear-gradient(135deg, #f59e0b, #f97316);
                            border-radius: 16px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 8px 16px -4px rgba(245, 158, 11, 0.5), 0 0 15px rgba(245, 158, 11, 0.6);
                            animation: iconGlow 2s ease-in-out infinite;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
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
                                text-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
                            ">Editar Participante</h4>
                            <p style="color: #64748b; font-size: 0.9rem; margin: 0; display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; box-shadow: 0 0 10px #f59e0b; animation: pulse 2s infinite;"></span>
                                MODIFICAR DATOS DEL PARTICIPANTE
                            </p>
                        </div>
                    </div>
                    
                    <!-- Botón cerrar con efecto neón naranja -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="
                        background: transparent;
                        border: 2px solid #f59e0b;
                        font-size: 1.2rem;
                        width: 36px;
                        height: 36px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
                        transition: all 0.3s ease;
                    " onmouseover="this.style.boxShadow='0 0 25px rgba(245, 158, 11, 0.8)'; this.style.transform='rotate(90deg) scale(1.1)'" onmouseout="this.style.boxShadow='0 0 15px rgba(245, 158, 11, 0.5)'; this.style.transform='rotate(0deg) scale(1)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY PROFESIONAL -->
            <div class="modal-body p-4" style="background: #ffffff;">
                <form id="form-editar-participante" class="row g-4" method="POST">

                    <!-- ================= DATOS PERSONALES ================= -->
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
                                background: linear-gradient(135deg, #f59e0b, #f97316);
                                color: white;
                                font-size: 0.7rem;
                                padding: 4px 12px;
                                border-radius: 30px;
                                margin-left: 8px;
                                box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
                            ">PERFIL</span>
                        </div>
                    </div>

                    <!-- CÉDULA -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Cédula <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="input-group">
                            <select class="form-select neon-input" id="nacionalidad-Edit" name="nacionalidad" style="
                                max-width: 80px;
                                border: 1.5px solid #e2e8f0;
                                border-radius: 12px 0 0 12px;
                                padding: 10px 12px;
                                font-size: 0.95rem;
                                transition: all 0.3s ease;
                                background: #ffffff;
                            " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <option value="V" selected>V</option>
                                <option value="E">E</option>
                            </select>
                            <input type="number" class="form-control neon-input" id="cedula-Edit" name="cedula" required maxlength="8" oninput="if(this.value.length > 8) this.value = this.value.slice(0, 8);" placeholder="Ingrese su cédula" style="
                                border: 1.5px solid #e2e8f0;
                                border-radius: 0 12px 12px 0;
                                padding: 10px 16px;
                                font-size: 0.95rem;
                                transition: all 0.3s ease;
                                background: #ffffff;
                            " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>
                        <div class="invalid-feedback" id="error-cedula-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Cédula inválida (7-8 dígitos)</div>
                    </div>

                    <!-- TELÉFONO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Teléfono <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="input-group">
                            <select class="form-select neon-select" id="prefijo_telefono-Edit" name="prefijo_telefono" style="
                                max-width: 100px;
                                border: 1.5px solid #e2e8f0;
                                border-radius: 12px 0 0 12px;
                                padding: 10px 12px;
                                background: #ffffff;
                                border-right: none;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <option value="0412">0412</option>
                                <option value="0414">0414</option>
                                <option value="0416">0416</option>
                                <option value="0424">0424</option>
                                <option value="0426">0426</option>
                                <option value="0293">0293</option>
                            </select>
                            <input type="tel" class="form-control neon-input" id="telefono_numero-Edit" name="telefono_numero" required placeholder="1234567" maxlength="7" style="
                                border: 1.5px solid #e2e8f0;
                                border-radius: 0 12px 12px 0;
                                padding: 10px 16px;
                                font-size: 0.95rem;
                                border-left: none;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <input type="hidden" id="telefono-Edit" name="telefono">
                        </div>
                        <div class="invalid-feedback" id="error-telefono-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Debe ingresar los 7 dígitos del número.</div>
                    </div>

                    <!-- PRIMER NOMBRE -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Primer Nombre <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" class="form-control neon-input" id="primer_nombre-Edit" name="primer_nombre" required placeholder="Ej: Juan" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-primer_nombre-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- SEGUNDO NOMBRE -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Segundo Nombre
                        </label>
                        <input type="text" class="form-control neon-input" id="segundo_nombre-Edit" name="segundo_nombre" placeholder="Ej: Carlos" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1), 0 0 20px rgba(249,115,22,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.2), 0 0 25px rgba(249,115,22,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-segundo_nombre-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- PRIMER APELLIDO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Primer Apellido <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" class="form-control neon-input" id="primer_apellido-Edit" name="primer_apellido" required placeholder="Ej: Pérez" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-primer_apellido-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- SEGUNDO APELLIDO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Segundo Apellido
                        </label>
                        <input type="text" class="form-control neon-input" id="segundo_apellido-Edit" name="segundo_apellido" placeholder="Ej: González" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1), 0 0 20px rgba(249,115,22,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.2), 0 0 25px rgba(249,115,22,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-segundo_apellido-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Solo letras válidas</div>
                    </div>

                    <!-- FECHA DE NACIMIENTO -->
                    <?php
                    $maxFechaNac = date('Y-m-d', strtotime('-15 years'));
                    $minFechaNac = date('Y-m-d', strtotime('-75 years'));
                    ?>
                    <div class="col-md-4">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Fecha de Nacimiento <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" class="form-control neon-input" id="fecha_nacimiento-Edit" name="fecha_nacimiento" min="<?= $minFechaNac ?>" max="<?= $maxFechaNac ?>" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 9px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-fecha_nacimiento-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Edad debe ser entre 15 y 75 años</div>
                    </div>

                    <!-- EDAD AUTOMÁTICA -->
                    <div class="col-md-3">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Edad
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control neon-input text-center fw-bold" id="edad_calculada-Edit" readonly placeholder="--" style="
                                border: 1.5px solid #e2e8f0;
                                border-radius: 12px 0 0 12px;
                                padding: 9px 12px;
                                font-size: 0.95rem;
                                background: #f8fafc;
                                color: #0f172a;
                            ">
                            <span class="input-group-text bg-light text-muted" style="border: 1.5px solid #e2e8f0; border-left: none; border-radius: 0 12px 12px 0; font-size: 0.85rem; font-weight: 500;">años</span>
                        </div>
                    </div>

                    <!-- GÉNERO -->
                    <div class="col-md-5">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Género <span style="color: #ef4444;">*</span>
                        </label>
                        <div class="d-flex gap-4 p-3" style="
                            background: #f8fafc;
                            border-radius: 12px;
                            border: 1.5px solid #e2e8f0;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.3)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <div class="form-check">
                                <input class="form-check-input neon-radio" type="radio" name="genero" id="genero_masculino-Edit" value="Masculino" required style="
                                    border: 2px solid #cbd5e1;
                                    width: 18px;
                                    height: 18px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 15px rgba(245,158,11,0.5)'" onmouseout="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                                <label class="form-check-label" for="genero_masculino-Edit" style="color: #334155; cursor: pointer;">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input neon-radio" type="radio" name="genero" id="genero_femenino-Edit" value="Femenino" style="
                                    border: 2px solid #cbd5e1;
                                    width: 18px;
                                    height: 18px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                " onmouseover="this.style.borderColor='#ef4444'; this.style.boxShadow='0 0 15px rgba(239,68,68,0.5)'" onmouseout="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                                <label class="form-check-label" for="genero_femenino-Edit" style="color: #334155; cursor: pointer;">Femenino</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-genero-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione un género</div>
                    </div>

                    <!-- DISCAPACIDAD -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Discapacidad <span style="color: #ef4444;">*</span>
                        </label>
                        <select class="form-select neon-select" id="id_discapacidad-Edit" name="id_discapacidad" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            background: #ffffff;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1), 0 0 20px rgba(249,115,22,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.2), 0 0 25px rgba(249,115,22,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="1">Ninguna</option>
                            <option value="2">Visual</option>
                            <option value="3">Auditiva</option>
                            <option value="4">Motora</option>
                            <option value="5">Cognitiva</option>
                        </select>
                        <div class="invalid-feedback" id="error-id_discapacidad-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione una opción válida</div>
                    </div>

                    <!-- ================= DATOS INSCRIPCIÓN ================= -->
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
                                background: linear-gradient(135deg, #f59e0b, #f97316);
                                color: white;
                                font-size: 0.7rem;
                                padding: 4px 12px;
                                border-radius: 30px;
                                margin-left: 8px;
                                box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
                            ">REGISTRO</span>
                        </div>
                    </div>

                    <!-- CORREO ELECTRÓNICO -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Correo Electrónico <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" class="form-control neon-input" id="correo-Edit" name="correo" required placeholder="ejemplo@correo.com" style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            background: #ffffff;
                        " onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.1), 0 0 20px rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2), 0 0 25px rgba(245,158,11,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <div class="invalid-feedback" id="error-correo-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Correo electrónico inválido</div>
                    </div>

                    <!-- GRADO DE INSTRUCCIÓN -->
                    <div class="col-md-6">
                        <label class="form-label" style="color: #475569; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">
                            Grado de Instrucción <span style="color: #ef4444;">*</span>
                        </label>
                        <select class="form-select neon-select" id="grado_institucion-Edit" name="grado_institucion" required style="
                            border: 1.5px solid #e2e8f0;
                            border-radius: 12px;
                            padding: 10px 16px;
                            font-size: 0.95rem;
                            background: #ffffff;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1), 0 0 20px rgba(249,115,22,0.4)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'" onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.2), 0 0 25px rgba(249,115,22,0.5)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            <option value="" disabled selected>Seleccione su nivel educativo</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Bachillerato">Bachiller</option>
                            <option value="Universidad">Universitario</option>
                        </select>
                        <div class="invalid-feedback" id="error-grado_institucion-Edit" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">Seleccione una opción válida</div>
                    </div>

                    <!-- Campo oculto para ID del participante -->
                    <input type="hidden" id="idParticipante" name="id_participante" value="">
                    
                    <!-- Campo oculto para curso -->
                    <input type="hidden" id="id_curso-Edit" name="id_curso" value="">

                    <!-- ================= BOTÓN DE ACTUALIZAR ================= -->
                    <div class="col-12 text-center mt-5">
                        <button type="submit" class="btn neon-button" style="
                            background: linear-gradient(135deg, #f59e0b, #f97316, #ef4444);
                            border: none;
                            padding: 12px 40px;
                            border-radius: 14px;
                            color: white;
                            font-weight: 600;
                            font-size: 1rem;
                            letter-spacing: 0.3px;
                            transition: all 0.3s ease;
                            box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.5), 0 0 20px rgba(245, 158, 11, 0.4);
                            position: relative;
                            overflow: hidden;
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px -4px rgba(245, 158, 11, 0.7), 0 0 30px rgba(245, 158, 11, 0.7)'; this.style.background='linear-gradient(135deg, #f97316, #ef4444, #f59e0b)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -4px rgba(245, 158, 11, 0.5), 0 0 20px rgba(245, 158, 11, 0.4)'; this.style.background='linear-gradient(135deg, #f59e0b, #f97316, #ef4444)'">
                            <span style="display: flex; align-items: center; justify-content: center; gap: 10px; position: relative; z-index: 2;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                </svg>
                                ACTUALIZAR PARTICIPANTE
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
        box-shadow: 0 8px 16px -4px rgba(245, 158, 11, 0.5), 0 0 15px rgba(245, 158, 11, 0.6);
    }
    50% {
        box-shadow: 0 8px 16px -4px rgba(245, 158, 11, 0.7), 0 0 25px rgba(245, 158, 11, 0.9);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 10px #f59e0b;
    }
    50% {
        opacity: 0.8;
        transform: scale(1.2);
        box-shadow: 0 0 20px #f59e0b;
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
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1), 0 0 20px rgba(245, 158, 11, 0.4) !important;
}

.neon-input:focus {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2), 0 0 25px rgba(245, 158, 11, 0.5) !important;
    outline: none;
}

/* Estilo especial para campos opcionales (segundo nombre, segundo apellido) */
.neon-input[placeholder*="Carlos"]:hover,
.neon-input[placeholder*="González"]:hover {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1), 0 0 20px rgba(249, 115, 22, 0.4) !important;
}

.neon-input[placeholder*="Carlos"]:focus,
.neon-input[placeholder*="González"]:focus {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2), 0 0 25px rgba(249, 115, 22, 0.5) !important;
}

/* Estilo para selects */
.neon-select:hover {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1), 0 0 20px rgba(249, 115, 22, 0.4) !important;
}

.neon-select:focus {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2), 0 0 25px rgba(249, 115, 22, 0.5) !important;
}

/* Estilo para radios con efecto neón */
.neon-radio:hover {
    border-color: #f59e0b !important;
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.5) !important;
}

/* Estilo para radios checked */
.form-check-input:checked {
    background-color: #f59e0b !important;
    border-color: #f59e0b !important;
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.5) !important;
}

/* Efecto hover para el botón */
.neon-button:hover .button-glow {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

/* Scroll personalizado con efecto neón naranja */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #f59e0b, #f97316);
    border-radius: 10px;
    box-shadow: 0 0 10px #f59e0b;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #f97316, #f59e0b);
}

/* Efecto hover para el contenedor de género */
.d-flex.gap-4.p-3:hover {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1), 0 0 20px rgba(245, 158, 11, 0.3) !important;
}

/* Placeholder para campos de fecha */
input[type="date"] {
    color-scheme: light;
}
</style>
<!-- Modal Ver Participante -->
<div class="modal fade" id="modal-viewParticipante" tabindex="-1" aria-labelledby="labelModalParticipante" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                            border-radius: 16px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.5), 0 0 15px rgba(59, 130, 246, 0.6);
                            animation: iconGlow 2s ease-in-out infinite;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="labelModalParticipante" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.6rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
                            ">Información del Participante</h5>
                            <p style="color: #64748b; font-size: 0.9rem; margin: 0; display: flex; align-items: center; gap: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; box-shadow: 0 0 10px #3b82f6;"></span>
                                PERFIL COMPLETO · VISUALIZACIÓN DE DATOS
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

            <!-- BODY con acentos neón -->
            <div class="modal-body p-4" style="background: #ffffff;">
                
                <!-- ================= DATOS PERSONALES ================= -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-2 mb-4">
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
                        ">IDENTIDAD</span>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Tarjeta izquierda -->
                        <div class="col-md-6">
                            <div class="info-card" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                height: 100%;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(59,130,246,0.2), 0 0 20px rgba(59,130,246,0.3)'; this.style.borderColor='#3b82f6'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
                                <!-- Efecto neón interno en hover -->
                                <div style="
                                    position: absolute;
                                    top: -50%;
                                    left: -50%;
                                    width: 200%;
                                    height: 200%;
                                    background: radial-gradient(circle at 30% 30%, rgba(59, 130, 246, 0.1), transparent 70%);
                                    opacity: 0;
                                    transition: opacity 0.3s ease;
                                " class="card-neon-glow"></div>
                                
                                <ul class="list-unstyled mb-0 position-relative" style="z-index: 2;">
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            NOMBRE COMPLETO
                                        </span>
                                        <span id="nombre-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            CÉDULA
                                        </span>
                                        <span id="cedula-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            FECHA NACIMIENTO
                                        </span>
                                        <span id="fechaNacimiento-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            TELÉFONO
                                        </span>
                                        <span id="telefono-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            GRADO INSTRUCCIÓN
                                        </span>
                                        <span id="gradoInstruccion-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Tarjeta derecha -->
                        <div class="col-md-6">
                            <div class="info-card" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 20px;
                                padding: 20px;
                                height: 100%;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(139,92,246,0.2), 0 0 20px rgba(139,92,246,0.3)'; this.style.borderColor='#8b5cf6'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
                                <!-- Efecto neón interno en hover -->
                                <div style="
                                    position: absolute;
                                    top: -50%;
                                    right: -50%;
                                    width: 200%;
                                    height: 200%;
                                    background: radial-gradient(circle at 70% 30%, rgba(139, 92, 246, 0.1), transparent 70%);
                                    opacity: 0;
                                    transition: opacity 0.3s ease;
                                " class="card-neon-glow-right"></div>
                                
                                <ul class="list-unstyled mb-0 position-relative" style="z-index: 2;">
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            APELLIDO
                                        </span>
                                        <span id="apellido-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            EDAD
                                        </span>
                                        <span id="edad-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            CORREO
                                        </span>
                                        <span id="correo-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            SEXO
                                        </span>
                                        <span id="sexo-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            FECHA REGISTRO
                                        </span>
                                        <span id="fechaRegistro-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            DISCAPACIDAD
                                        </span>
                                        <span id="discapacidad-view" class="fw-medium" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 4px 12px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= CURSOS ACTIVOS ================= -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="
                            width: 32px;
                            height: 32px;
                            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
                            border-radius: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 0 15px rgba(22, 163, 74, 0.4);
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.1rem;">Cursos Activos</h6>
                        <span style="
                            background: linear-gradient(135deg, #16a34a, #15803d);
                            color: white;
                            font-size: 0.7rem;
                            padding: 4px 12px;
                            border-radius: 30px;
                            margin-left: 8px;
                            box-shadow: 0 0 15px rgba(22, 163, 74, 0.5);
                        ">EN CURSO</span>
                    </div>
                    
                    <div style="
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 20px;
                        padding: 20px;
                        position: relative;
                        overflow: hidden;
                    ">
                        <!-- Efecto neón sutil -->
                        <div style="
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            height: 2px;
                            background: linear-gradient(90deg, transparent, #16a34a, transparent);
                            filter: blur(2px);
                        "></div>
                        
                        <div class="row g-3" id="cursos-activos-container">
                            <!-- Contenido dinámico desde JS -->
                        </div>
                        <div id="sin-cursos" class="text-center py-5" style="display: none;">
                            <div style="
                                width: 70px;
                                height: 70px;
                                background: #f1f5f9;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 15px;
                                border: 2px solid #16a34a;
                                box-shadow: 0 0 20px rgba(22, 163, 74, 0.3);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <p style="color: #475569; font-size: 1rem; margin: 0; font-weight: 500;">
                                No tiene cursos activos actualmente
                            </p>
                            <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                                Los cursos aparecerán aquí cuando se inscriba
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ================= HISTORIAL ACADÉMICO ================= -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="
                            width: 32px;
                            height: 32px;
                            background: linear-gradient(135deg, #fed7aa, #fed7aa);
                            border-radius: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 0 15px rgba(194, 65, 12, 0.4);
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c2410c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.1rem;">Historial Académico</h6>
                        <span style="
                            background: linear-gradient(135deg, #c2410c, #9a3412);
                            color: white;
                            font-size: 0.7rem;
                            padding: 4px 12px;
                            border-radius: 30px;
                            margin-left: 8px;
                            box-shadow: 0 0 15px rgba(194, 65, 12, 0.5);
                        ">TRAYECTORIA</span>
                    </div>
                    
                    <div style="
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 20px;
                        padding: 20px;
                        overflow-x: auto;
                        position: relative;
                    ">
                        <!-- Efecto neón sutil -->
                        <div style="
                            position: absolute;
                            top: 0;
                            left: 10%;
                            right: 10%;
                            height: 2px;
                            background: linear-gradient(90deg, transparent, #c2410c, transparent);
                            filter: blur(2px);
                        "></div>

                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e2e8f0;">
                                    <th style="color: #475569; font-weight: 600; padding: 12px 8px; text-align: left; font-size: 0.85rem;">CURSO</th>
                                    <th style="color: #475569; font-weight: 600; padding: 12px 8px; text-align: left; font-size: 0.85rem;">INSCRIPCIÓN</th>
                                    <th style="color: #475569; font-weight: 600; padding: 12px 8px; text-align: left; font-size: 0.85rem;">INICIO</th>
                                    <th style="color: #475569; font-weight: 600; padding: 12px 8px; text-align: left; font-size: 0.85rem;">FIN</th>
                                    <th style="color: #475569; font-weight: 600; padding: 12px 8px; text-align: left; font-size: 0.85rem;">ESTADO</th>
                                </tr>
                            </thead>
                            <tbody id="historial-content">
                                <!-- Contenido dinámico -->
                            </tbody>
                        </table>
                        
                        <!-- Mensaje cuando no hay historial -->
                        <div id="sin-historial" class="text-center py-5" style="display: none;">
                            <div style="
                                width: 70px;
                                height: 70px;
                                background: #f1f5f9;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 15px;
                                border: 2px solid #c2410c;
                                box-shadow: 0 0 20px rgba(194, 65, 12, 0.3);
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c2410c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <p style="color: #475569; font-size: 1rem; margin: 0; font-weight: 500;">
                                No hay historial académico disponible
                            </p>
                            <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                                Los cursos finalizados aparecerán aquí
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER con acentos neón -->
            <div class="modal-footer justify-content-between px-4 py-3" style="
                background: #f8fafc;
                border-top: 2px solid #3b82f6;
                border-radius: 0 0 24px 24px;
                box-shadow: 0 -4px 20px rgba(59, 130, 246, 0.2);
                position: relative;
            ">
                <!-- Línea neón inferior -->
                <div style="
                    position: absolute;
                    bottom: 0;
                    left: 10%;
                    right: 10%;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #3b82f6, #8b5cf6, #ec4899, #3b82f6, transparent);
                    filter: blur(2px);
                    animation: neonLine 3s linear infinite reverse;
                "></div>

                <div class="d-flex gap-2">
                    <a id="btn-recorrido-participante" class="btn" onclick="window.location.href='reporte.php?cat=1&rep=12&id=' + document.getElementById('btn-historial-participante').getAttribute('data-id')" style="
                        background: transparent;
                        border: 2px solid #3b82f6;
                        color: #3b82f6;
                        padding: 8px 20px;
                        border-radius: 12px;
                        font-weight: 500;
                        font-size: 0.85rem;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
                        cursor: pointer;
                    " onmouseover="this.style.background='#3b82f6'; this.style.color='white'; this.style.boxShadow='0 0 25px rgba(59, 130, 246, 0.6)'" onmouseout="this.style.background='transparent'; this.style.color='#3b82f6'; this.style.boxShadow='0 0 15px rgba(59, 130, 246, 0.3)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                        RECORRIDO
                    </a>
                    <a id="btn-historial-participante" target="_blank" class="btn" data-id="" style="
                        background: transparent;
                        border: 2px solid #16a34a;
                        color: #16a34a;
                        padding: 8px 20px;
                        border-radius: 12px;
                        font-weight: 500;
                        font-size: 0.85rem;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        box-shadow: 0 0 15px rgba(22, 163, 74, 0.3);
                    " onmouseover="this.style.background='#16a34a'; this.style.color='white'; this.style.boxShadow='0 0 25px rgba(22, 163, 74, 0.6)'" onmouseout="this.style.background='transparent'; this.style.color='#16a34a'; this.style.boxShadow='0 0 15px rgba(22, 163, 74, 0.3)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        HISTORIAL PDF
                    </a>
                    <a id="btn-constancia-participante" target="_blank" class="btn" data-id="" style="
                        background: transparent;
                        border: 2px solid #dc2626;
                        color: #dc2626;
                        padding: 8px 20px;
                        border-radius: 12px;
                        font-weight: 500;
                        font-size: 0.85rem;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        box-shadow: 0 0 15px rgba(220, 38, 38, 0.3);
                    " onmouseover="this.style.background='#dc2626'; this.style.color='white'; this.style.boxShadow='0 0 25px rgba(220, 38, 38, 0.6)'" onmouseout="this.style.background='transparent'; this.style.color='#dc2626'; this.style.boxShadow='0 0 15px rgba(220, 38, 38, 0.3)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        CONSTANCIA
                    </a>
                </div>
                <button type="button" class="btn" data-bs-dismiss="modal" style="
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                    border: none;
                    color: white;
                    padding: 8px 30px;
                    border-radius: 12px;
                    font-weight: 500;
                    font-size: 0.85rem;
                    transition: all 0.3s ease;
                    box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
                " onmouseover="this.style.boxShadow='0 0 30px rgba(59, 130, 246, 0.8)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 0 20px rgba(59, 130, 246, 0.5)'; this.style.transform='translateY(0)'">
                    CERRAR
                </button>
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
        box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.5), 0 0 15px rgba(59, 130, 246, 0.6);
    }
    50% {
        box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.7), 0 0 25px rgba(59, 130, 246, 0.9);
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

/* Efecto hover para tarjetas */
.info-card:hover .card-neon-glow,
.info-card:hover .card-neon-glow-right {
    opacity: 1 !important;
}

/* Estilo para los items de cursos activos */
.curso-activo-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.curso-activo-item:hover {
    border-color: #16a34a;
    box-shadow: 0 8px 16px -4px rgba(22, 163, 74, 0.2), 0 0 20px rgba(22, 163, 74, 0.2);
}

/* Estilo para la tabla */
tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

tbody tr:hover {
    background: #f1f5f9;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

td {
    padding: 12px 8px;
    color: #334155;
    font-size: 0.9rem;
}

/* Badges de estado - Estilo Sólido y Vibrante */
.estado-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 12px; /* Estilo más moderno y menos redondeado extremo */
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: white !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.estado-badge.en-curso {
    background: linear-gradient(135deg, #16a34a, #22c55e);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    border: none;
}

.estado-badge.culminado {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    border: none;
}

.estado-badge.pendiente, 
.estado-badge.diferido {
    background: linear-gradient(135deg, #ea580c, #f97316);
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    border: none;
}

.estado-badge.finalizado {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    border: none;
}

.estado-badge.en-espera {
    background: linear-gradient(135deg, #64748b, #94a3b8);
    box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
    border: none;
}

.estado-badge.en-pausa {
    background: linear-gradient(135deg, #0891b2, #06b6d4);
    box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    border: none;
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
    background: linear-gradient(180deg, #3b82f6, #8b5cf6);
    border-radius: 10px;
    box-shadow: 0 0 10px #3b82f6;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #8b5cf6, #3b82f6);
}
</style>
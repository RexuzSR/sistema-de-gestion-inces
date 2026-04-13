<!-- Modal Ver Docente - Versión Profesional con Efectos Neón Azul -->
<div class="modal fade" id="modal-viewDocente" tabindex="-1" aria-hidden="true" aria-labelledby="label-modal-viewDocente">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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

            <!-- HEADER con efecto profesional -->
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
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="label-modal-viewDocente" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.7rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
                            ">Perfil del Docente</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot-light"></span>
                                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                                    VISUALIZACIÓN DE DATOS · INFORMACIÓN COMPLETA
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BOTÓN DE CERRAR CON ESTILO NEÓN -->
                    <button type="button" class="btn-close-custom-light" data-bs-dismiss="modal" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4" style="background: #ffffff; position: relative; z-index: 1;">
                
                <!-- ================= DATOS PERSONALES ================= -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-2 mb-4">
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
                        <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.3rem;">Datos Personales</h6>
                        <span class="badge-neon" style="
                            background: linear-gradient(135deg, #38bdf8, #0281fe);
                            color: white;
                            font-size: 0.7rem;
                            padding: 4px 12px;
                            border-radius: 30px;
                            margin-left: 8px;
                            box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
                        ">IDENTIDAD</span>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Tarjeta izquierda -->
                        <div class="col-md-6">
                            <div class="info-card-light" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 24px;
                                padding: 25px;
                                height: 100%;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <ul class="list-unstyled mb-0 position-relative" style="z-index: 2;">
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            NOMBRE COMPLETO
                                        </span>
                                        <span id="nombre-completo-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            CÉDULA
                                        </span>
                                        <span id="cedula-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            TELÉFONO
                                        </span>
                                        <span id="telefono-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            CORREO
                                        </span>
                                        <span id="correo-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
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
                            <div class="info-card-light" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 24px;
                                padding: 25px;
                                height: 100%;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(2,129,254,0.2), 0 0 20px rgba(2,129,254,0.3)'; this.style.borderColor='#0281fe'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
                                <div class="card-shine-light"></div>
                                
                                <ul class="list-unstyled mb-0 position-relative" style="z-index: 2;">
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            FECHA NACIMIENTO
                                        </span>
                                        <span id="fechaNacimiento-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            EDAD
                                        </span>
                                        <span id="edad-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        "><span id="edad-valor"></span> años</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            GÉNERO
                                        </span>
                                        <span id="genero-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom" style="border-color: #e2e8f0 !important;">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            TIPO DOCENTE
                                        </span>
                                        <span id="tipo-docente-view" class="info-value-light" style="
                                            color: #0f172a;
                                            background: #ffffff;
                                            padding: 5px 15px;
                                            border-radius: 30px;
                                            border: 1px solid #e2e8f0;
                                            font-size: 0.9rem;
                                        ">-</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-3">
                                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                            ESTADO
                                        </span>
                                        <span id="estado-view" class="badge-status" style="
                                            padding: 6px 16px;
                                            border-radius: 30px;
                                            font-size: 0.8rem;
                                            font-weight: 600;
                                            letter-spacing: 0.5px;
                                            text-transform: uppercase;
                                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                                        ">-</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= CURSOS ASOCIADOS ================= -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="icon-glow-light" style="
                            width: 40px;
                            height: 40px;
                            background: #fed7aa;
                            border-radius: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <h6 class="fw-semibold mb-0" style="color: #1e293b; font-size: 1.3rem;">Cursos Asociados</h6>
                        <span class="badge-neon" style="
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            color: white;
                            font-size: 0.7rem;
                            padding: 4px 12px;
                            border-radius: 30px;
                            margin-left: 8px;
                            box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
                        ">ASIGNACIONES</span>
                    </div>
                    
                    <div id="cursos-asociados" class="row gy-3">
                        <!-- Los cursos se inyectarán aquí mediante JavaScript -->
                        <!-- Loading state -->
                        <div class="col-12">
                            <div class="loading-container" style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 24px;
                                padding: 30px;
                                text-align: center;
                            ">
                                <div class="loading-spinner" style="
                                    width: 60px;
                                    height: 60px;
                                    background: #f1f5f9;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    margin: 0 auto 15px;
                                    border: 2px dashed #f59e0b;
                                    animation: float 3s ease-in-out infinite;
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </div>
                                <p style="color: #64748b; font-size: 1rem; margin: 0;">
                                    Cargando cursos asociados...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER CON BOTONES FUTURISTAS -->
            <div class="modal-footer justify-content-between px-4 py-3 animate-on-show" data-delay="700" style="
                background: #f8fafc;
                border-top: 2px solid #38bdf8;
                border-radius: 0 0 24px 24px;
                box-shadow: 0 -4px 20px rgba(56, 189, 248, 0.2);
                position: relative;
                z-index: 2;
            ">
                <!-- Efecto de gradiente en el borde -->
                <div class="footer-glow-light"></div>
                
                <!-- Botón Constancia (PDF) -->
                <a id="btn-constanciaDocente-pdf" target="_blank" style="text-decoration: none;">
                    <button class="btn futuristic-button-outline-light" style="
                        background: transparent;
                        border: 1.5px solid #ef4444;
                        color: #ef4444;
                        padding: 12px 28px;
                        border-radius: 14px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        display: inline-flex;
                        align-items: center;
                        gap: 10px;
                        font-size: 0.95rem;
                        letter-spacing: 0.5px;
                    " onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 0 20px rgba(239,68,68,0.5)'" onmouseout="this.style.background='transparent'; this.style.color='#ef4444'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        GENERAR CONSTANCIA
                    </button>
                </a>
                
                <!-- Botón Cerrar -->
                <button type="button" class="btn futuristic-button-primary-light" data-bs-dismiss="modal" style="
                    background: linear-gradient(135deg, #38bdf8, #0281fe);
                    border: none;
                    padding: 12px 35px;
                    border-radius: 14px;
                    color: white;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    font-size: 0.95rem;
                    letter-spacing: 0.5px;
                    box-shadow: 0 5px 20px rgba(56, 189, 248, 0.4);
                " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 30px rgba(56, 189, 248, 0.7)'; this.style.background='linear-gradient(135deg, #0281fe, #38bdf8)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 20px rgba(56, 189, 248, 0.4)'; this.style.background='linear-gradient(135deg, #38bdf8, #0281fe)'">
                    CERRAR
                </button>
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

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
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
    transform: translateY(-5px) scale(1.02) !important;
}

/* Estilo para los valores de información */
.info-value-light {
    transition: all 0.3s ease;
    cursor: default;
}

.info-value-light:hover {
    background: #dbeafe !important;
    border-color: #38bdf8 !important;
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
}

/* Badge de estado dinámico */
.badge-status {
    transition: all 0.3s ease;
    cursor: default;
}

.badge-status:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
    box-shadow: 0 0 20px currentColor !important;
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

/* Estilo para los cursos cards */
.curso-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 20px;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.curso-card:hover {
    transform: translateX(5px) scale(1.02);
    border-color: #f59e0b;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
    background: #ffffff;
}

.curso-titulo {
    color: #f59e0b;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 8px;
}

.curso-detalle {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.curso-detalle svg {
    width: 16px;
    height: 16px;
    stroke: #f59e0b;
}
</style>

<!-- Script de ejemplo para llenar el estado badge (agrega esto a tu JS existente) -->
<script>
// Función para actualizar el color del badge según el estado
function actualizarBadgeEstado(estado) {
    const badge = document.getElementById('estado-view');
    badge.textContent = estado;
    
    // Aplicar estilos según el estado
    switch(estado) {
        case 'Disponible':
            badge.style.background = '#d1fae5';
            badge.style.color = '#10b981';
            badge.style.border = '1px solid #10b981';
            break;
        case 'En formación':
            badge.style.background = '#fed7aa';
            badge.style.color = '#f59e0b';
            badge.style.border = '1px solid #f59e0b';
            break;
        case 'Asignado':
            badge.style.background = '#dbeafe';
            badge.style.color = '#0281fe';
            badge.style.border = '1px solid #0281fe';
            break;
        case 'Inactivo':
            badge.style.background = '#fee2e2';
            badge.style.color = '#ef4444';
            badge.style.border = '1px solid #ef4444';
            break;
        default:
            badge.style.background = '#f1f5f9';
            badge.style.color = '#64748b';
            badge.style.border = '1p}
</script>
.innerHTML = html;
}
</script>
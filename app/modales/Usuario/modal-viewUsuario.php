<!-- Modal Ver Usuario -->
<div class="modal fade" id="modal-viewUsuario" tabindex="-1" aria-labelledby="label-modal-viewUsuario" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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
                            <h4 class="modal-title fw-bold mb-1" style="
                                background: linear-gradient(135deg, #0f172a, #1e293b);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                font-size: 1.7rem;
                                letter-spacing: -0.02em;
                                text-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
                            ">Perfil de Usuario</h4>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot-light"></span>
                                <p style="color: #64748b; font-size: 0.85rem; margin: 0; letter-spacing: 1px;">
                                    INFORMACIÓN DETALLADA
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
                <!-- AVATAR con efecto de brillo mejorado -->
                <div class="text-center mb-4 position-relative">
                    <!-- Círculo de brillo detrás del avatar -->
                    <div style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 130px;
                        height: 130px;
                        background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, transparent 70%);
                        border-radius: 50%;
                        filter: blur(10px);
                        z-index: -1;
                        animation: pulse 3s infinite;
                    "></div>
                    
                    <!-- Avatar con borde animado -->
                    <div class="position-relative d-inline-block">
                        <img id="avatar" class="rounded-circle shadow-lg border border-3" alt="Avatar del usuario" width="120" height="120" style="
                            border-color: #38bdf8 !important;
                            box-shadow: 0 0 30px rgba(56, 189, 248, 0.3);
                            object-fit: cover;
                            transition: all 0.3s ease;
                            background: #f1f5f9;
                        " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 0 40px rgba(56, 189, 248, 0.6)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 0 30px rgba(56, 189, 248, 0.3)'">
                        
                        <!-- Icono de verificado (opcional, se puede mostrar según el rol) -->
                        <div style="
                            position: absolute;
                            bottom: 5px;
                            right: 5px;
                            width: 30px;
                            height: 30px;
                            background: linear-gradient(135deg, #10b981, #059669);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border: 2px solid #ffffff;
                            box-shadow: 0 0 15px #10b981;
                        ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- TARJETA DE INFORMACIÓN CLARA -->
                <div class="info-card-light p-4" style="
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 24px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                    position: relative;
                    overflow: hidden;
                " onmouseover="this.style.boxShadow='0 10px 25px -5px rgba(56,189,248,0.2), 0 0 20px rgba(56,189,248,0.3)'; this.style.borderColor='#38bdf8'" onmouseout="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#e2e8f0'">
                    <!-- Líneas decorativas sutiles -->
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        height: 2px;
                        background: linear-gradient(90deg, transparent, #38bdf8, #0281fe, #38bdf8, transparent);
                        opacity: 0.5;
                    "></div>
                    
                    <div style="
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        height: 2px;
                        background: linear-gradient(90deg, transparent, #0281fe, #38bdf8, #0281fe, transparent);
                        opacity: 0.5;
                    "></div>

                    <!-- Efecto de brillo en hover -->
                    <div class="card-shine-light"></div>

                    <div class="row g-4 position-relative" style="z-index: 2;">
                        <!-- Nombre -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #dbeafe;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(56, 189, 248, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0281fe" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">NOMBRE</span>
                                </div>
                                <div id="nombre-view" class="fw-bold" style="
                                    color: #0f172a;
                                    font-size: 1.1rem;
                                    padding-left: 36px;
                                "></div>
                            </div>
                        </div>

                        <!-- Apellido -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #ede9fe;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(139, 92, 246, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">APELLIDO</span>
                                </div>
                                <div id="apellido-view" class="fw-bold" style="
                                    color: #0f172a;
                                    font-size: 1.1rem;
                                    padding-left: 36px;
                                "></div>
                            </div>
                        </div>

                        <!-- Usuario -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #f3e8ff;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(168, 85, 247, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="4"></circle>
                                            <path d="M5.37 20c.92-3 4.29-4 6.63-4s5.71 1 6.63 4"></path>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">USUARIO</span>
                                </div>
                                <div id="nombre_usuario-view" class="fw-bold" style="
                                    color: #0f172a;
                                    font-size: 1.1rem;
                                    padding-left: 36px;
                                "></div>
                            </div>
                        </div>

                        <!-- Correo -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #fed7aa;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(245, 158, 11, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                            <path d="m22 7-10 7L2 7"></path>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">CORREO</span>
                                </div>
                                <div id="correo-view" class="fw-bold" style="
                                    color: #0f172a;
                                    font-size: 1rem;
                                    padding-left: 36px;
                                    word-break: break-all;
                                "></div>
                            </div>
                        </div>

                        <!-- Rol -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #d1fae5;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(16, 185, 129, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10A10 10 0 0 1 2 12 10 10 0 0 1 12 2z"></path>
                                            <path d="M12 6v6l4 2"></path>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">ROL</span>
                                </div>
                                <div id="rol-view" class="fw-bold" style="
                                    color: #0f172a;
                                    font-size: 1.1rem;
                                    padding-left: 36px;
                                "></div>
                            </div>
                        </div>

                        <!-- Estado con indicador visual -->
                        <div class="col-6">
                            <div class="info-item-light">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="
                                        width: 28px;
                                        height: 28px;
                                        background: #fee2e2;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(239, 68, 68, 0.2);
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                    </div>
                                    <span style="color: #64748b; font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
                                </div>
                                <div id="estado-view" class="fw-bold d-flex align-items-center gap-2" style="padding-left: 36px;">
                                    <!-- El contenido se llenará dinámicamente con JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Efecto de brillo sutil en la esquina -->
                    <div style="
                        position: absolute;
                        bottom: 20px;
                        right: 20px;
                        width: 100px;
                        height: 100px;
                        background: radial-gradient(circle, rgba(56, 189, 248, 0.1) 0%, transparent 70%);
                        border-radius: 50%;
                        pointer-events: none;
                    "></div>
                </div>

                <!-- Badge de seguridad -->
                <div class="text-center mt-4">
                    <span style="
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        background: #f1f5f9;
                        padding: 6px 16px;
                        border-radius: 30px;
                        border: 1px solid #e2e8f0;
                        color: #64748b;
                        font-size: 0.8rem;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 15px rgba(56,189,248,0.3)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        DATOS PROTEGIDOS · ACCESO SEGURO
                    </span>
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
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.1);
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
    background: #38bdf8;
    border-radius: 50%;
    box-shadow: 0 0 10px #38bdf8;
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

/* Estilo para la tarjeta de información */
.info-card-light {
    transition: all 0.3s ease !important;
    position: relative;
}

.info-card-light:hover {
    transform: translateY(-3px) scale(1.01) !important;
}

/* Estilo para items de información */
.info-item-light {
    transition: all 0.3s ease;
    padding: 8px;
    border-radius: 12px;
}

.info-item-light:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.info-item-light:hover .fw-bold {
    color: #0281fe !important;
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

/* Estilo para el estado (activo/inactivo) */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.status-badge:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

.status-active {
    background: #d1fae5;
    color: #10b981;
    border: 1px solid #10b981;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}

.status-inactive {
    background: #fee2e2;
    color: #ef4444;
    border: 1px solid #ef4444;
    box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
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

/* Avatar imagen por defecto */
#avatar[src=""] {
    display: none;
}

#avatar:not([src]), 
#avatar[src=""],
#avatar[src="undefined"] {
    content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="%23cbd5e1" stroke="%2394a3b8" stroke-width="1"><circle cx="12" cy="8" r="4"/><path d="M5.37 20c.92-3 4.29-4 6.63-4s5.71 1 6.63 4"/></svg>');
}
</style>

<!-- Script para llenar el estado dinámicamente -->
<script>
// Función para actualizar el badge de estado
function actualizarEstadoUsuario(estado) {
    const estadoContainer = document.getElementById('estado-view');
    const isActive = estado === 'Activo' || estado === 'activo' || estado === 'active' || estado === '1';
    
    const badgeClass = isActive ? 'status-active' : 'status-inactive';
    const badgeText = isActive ? 'Activo' : 'Inactivo';
    
    estadoContainer.innerHTML = `
        <span class="status-badge ${badgeClass}">
            <span style="width: 8px; height: 8px; background: currentColor; border-radius: 50%; display: inline-block;"></span>
            ${badgeText}
        </span>
    `;
}

// Función para cargar datos del usuario en el modal
function cargarDatosUsuario(usuario) {
    document.getElementById('nombre-view').textContent = usuario.nombre || '-';
    document.getElementById('apellido-view').textContent = usuario.apellido || '-';
    document.getElementById('nombre_usuario-view').textContent = usuario.nombre_usuario || '-';
    document.getElementById('correo-view').textContent = usuario.correo || '-';
    document.getElementById('rol-view').textContent = usuario.rol || '-';
    
    actualizarEstadoUsuario(usuario.estado);
    
    // Cargar avatar si existe
    const avatarImg = document.getElementById('avatar');
    if (usuario.avatar && usuario.avatar !== '') {
        avatarImg.src = usuario.avatar;
    } else {
        avatarImg.src = ''; // Esto activará el fallback CSS
    }
}
</script>
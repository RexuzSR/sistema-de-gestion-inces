<style>
    .header-modern {
        background: linear-gradient(135deg, #081c75 0%, #078cf8b0 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0 24px;
        height: 70px;
        position: relative;
        z-index: 1030;
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        position: relative;
    }

    .page-title {
        font-size: 22px;
        font-weight: 600;
        color: white;
        margin: 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(45deg, #00d2ff, #3a7bd5);
        border-radius: 2px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
    }

    /* ==================== NOTIFICACIONES==================== */
    .notification-container {
        position: relative;
    }

    .notification-btn-modern {
        background: rgba(238, 11, 11, 0.1);
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: white;
        backdrop-filter: blur(4px);
    }

    .notification-btn-modern:hover {
        background: rgba(14, 58, 99, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.97);
    }

    .notification-btn-modern:active {
        transform: scale(0.95);
    }
    /* puntico de la campana */
    .notification-badge-modern {
        display: none; /* Oculto por defecto para evitar parpadeos */
        position: absolute;
        top: 8px;
        right: 8px;
        width: 10px;
        height: 10px;
        background: linear-gradient(135deg, #ff416c, #ff4b2b);
        border-radius: 50%;
        border: 2px solid white;
        animation: pulse-glow 2s infinite;
        box-shadow: 0 0 6px rgba(255, 65, 108, 0.6);
    }

    @keyframes pulse-glow {
        0% { 
            transform: scale(1); 
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(255, 65, 108, 0.7);
        }
        50% { 
            transform: scale(1.15); 
            opacity: 0.9;
            box-shadow: 0 0 0 4px rgba(255, 65, 108, 0);
        }
        100% { 
            transform: scale(1); 
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(255, 65, 108, 0);
        }
    }

    /* Dropdown mejorado */
    .notifications-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 380px;
        max-width: 90vw;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px) scale(0.96);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        overflow: hidden;
        pointer-events: none;
        backdrop-filter: blur(0);
    }

    .notifications-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Flecha decorativa */
    .notifications-dropdown::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 18px;
        width: 16px;
        height: 16px;
        background: white;
        transform: rotate(45deg);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        border-left: 1px solid rgba(0, 0, 0, 0.05);
        z-index: -1;
    }

    .notifications-header {
        padding: 12px 20px;
        background: linear-gradient(135deg, #0bcbecff 0%, #0494e7ff 100%);
        color: white;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 20px 20px 0 0;
        gap: 10px;
    }

    /* Estilos para el selector de filtro */
    .notification-filter-select {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        font-size: 11px;
        border-radius: 8px;
        padding: 4px 8px;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        max-width: 120px;
        font-weight: 500;
    }

    .notification-filter-select:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .notification-filter-select option {
        background: #0494e7;
        color: white;
    }

    .notifications-header span {
        font-size: 15px;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-clear {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 11px;
        cursor: pointer;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-clear:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.02);
    }

    .notifications-content {
        max-height: 420px;
        overflow-y: auto;
        padding: 12px;
    }

    /* Items de notificación  */
    .notification-item {
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid #f0f0f0;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        position: relative;
    }

    .notification-item:hover {
        background: #fafbfc;
        transform: translateX(3px);
        border-color: #e0e0e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .notification-item.unread {
        background: linear-gradient(135deg, #f0f7ff 0%, #faf5ff 100%);
        border-left: 4px solid #1bcfe7ff;
    }

    /* Icono de notificación */
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .notification-icon.diferido {
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
        color: #f59e0b;
    }

    .notification-icon.culminado {
        background: linear-gradient(135deg, #fee2e2, #fff5f5);
        color: #ef4444;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 4px;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .notification-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
    }

    .notification-badge.diferido {
        background: #fef3c7;
        color: #d97706;
    }

    .notification-badge.culminado {
        background: #fee2e2;
        color: #dc2626;
    }

    .notification-message {
        font-size: 12px;
        color: #4b5563;
        line-height: 1.4;
        margin-bottom: 6px;
    }

    .notification-time {
        font-size: 10px;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .notification-time::before {
        content: '🕐';
        font-size: 9px;
    }

    /* Indicador de no leído (punto) */
    .unread-dot {
        width: 8px;
        height: 8px;
        background: #0a33e7ff;
        border-radius: 50%;
        position: absolute;
        top: 14px;
        right: 14px;
        box-shadow: 0 0 0 2px white;
    }

    /* Estado vacío */
    .notifications-empty {
        text-align: center;
        padding: 48px 24px;
        color: #9ca3af;
    }

    .notifications-empty svg {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .notifications-empty p {
        font-size: 13px;
        margin: 0;
    }

    /* ==================== PERFIL DE USUARIO ==================== */
    .profile-container {
        position: relative;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 16px 6px 12px;
        border-radius: 40px;
        background: rgba(255, 255, 255, 0.1);
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.15);
        position: relative;
        backdrop-filter: blur(4px);
    }

    .user-profile:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.25);
    }

    .user-profile:active {
        transform: scale(0.98);
    }

    .user-avatar-modern {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.4);
        transition: transform 0.2s ease;
    }

    .user-profile:hover .user-avatar-modern {
        transform: scale(1.02);
    }

    .user-info-compact {
        color: white;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        line-height: 1.3;
    }

    .user-role {
        font-size: 11px;
        opacity: 0.85;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .user-role::before {
        content: '●';
        font-size: 7px;
        color: #10b981;
    }

    .dropdown-arrow {
        transition: transform 0.2s ease;
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
    }

    .user-profile.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown de perfil mejorado */
    .modern-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        background: white;
        min-width: 280px;
        padding: 8px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px) scale(0.96);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        pointer-events: none;
    }

    .modern-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .modern-dropdown::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 20px;
        width: 16px;
        height: 16px;
        background: white;
        transform: rotate(45deg);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        border-left: 1px solid rgba(0, 0, 0, 0.05);
    }

    .dropdown-header-modern {
        padding: 16px;
        background: linear-gradient(135deg, #03a9ebff 0%, #0494e7ff 100%);
        margin: -8px -8px 12px -8px;
        border-radius: 20px 20px 16px 16px;
        color: white;
    }

    .dropdown-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dropdown-user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }

    .dropdown-user-text h6 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
    }

    .dropdown-user-text small {
        opacity: 0.85;
        font-size: 11px;
    }

    .dropdown-item-modern {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
        font-size: 13px;
        margin: 4px 0;
    }

    .dropdown-item-modern:hover {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #12c5f1ff;
        transform: translateX(3px);
    }

    .dropdown-item-modern svg {
        width: 18px;
        height: 18px;
        opacity: 0.7;
    }

    .dropdown-item-modern:hover svg {
        opacity: 1;
        color: #667eea;
    }

    .dropdown-divider-modern {
        margin: 8px 0;
        border-top: 1px solid #e5e7eb;
    }

    .logout-item-modern {
        color: #ef4444 !important;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .logout-item-modern:hover {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
        color: #dc2626 !important;
        transform: translateX(3px);
    }

    /* Scrollbar moderno */
    .notifications-content::-webkit-scrollbar {
        width: 5px;
    }

    .notifications-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .notifications-content::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #cbd5e1, #94a3b8);
        border-radius: 10px;
    }

    .notifications-content::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #94a3b8, #64748b);
    }

    /* Animación de entrada */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .notification-item {
        animation: slideInRight 0.2s ease backwards;
    }

    .notification-item:nth-child(1) { animation-delay: 0.02s; }
    .notification-item:nth-child(2) { animation-delay: 0.04s; }
    .notification-item:nth-child(3) { animation-delay: 0.06s; }

    /* Responsive */
    @media (max-width: 768px) {
        .header-modern {
            padding: 0 16px;
        }
        
        .page-title {
            font-size: 18px;
        }
        
        .user-info-compact {
            display: none;
        }
        
        .user-profile {
            padding: 8px 10px;
        }
        
        .notifications-dropdown,
        .modern-dropdown {
            position: fixed;
            top: 70px;
            left: 10px;
            right: 10px;
            width: auto;
            min-width: auto;
            max-width: calc(100% - 20px);
        }
        
        .notifications-dropdown::before,
        .modern-dropdown::before {
            right: 15px;
        }
    }
</style>

<div class="header-modern">
    <div class="header-content">
        <h1 class="page-title">
            <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Control de Participantes'; ?>
        </h1>
        
        <div class="header-actions">
            <!-- Notificaciones -->
            <div class="notification-container">
                <button id="notification-btn-modern" class="notification-btn-modern" type="button" aria-label="Notificaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92z"/>
                    </svg>
                    <span class="notification-badge-modern"></span>
                </button>
                <div class="notifications-dropdown" id="notifications-dropdown">
                    <div class="notifications-header">
                        <span style="font-size: 13px;">Notificaciones</span>
                        <select id="notif-filter" class="notification-filter-select" title="Filtrar por tipo">
                            <option value="all">Todas</option>
                            <option value="diferido">Diferidas</option>
                            <option value="culminado">Culminadas</option>
                        </select>
                        <button class="btn-clear" onclick="markAllAsRead()" style="background: none; border: none; color: white; font-size: 10px; cursor: pointer; padding: 4px 8px; opacity: 0.8;">
                            Leídas
                        </button>
                    </div>
                    <div class="notifications-content" id="notifList">
                        <!-- Generado por notifications.js -->
                    </div>
                </div>
            </div>
            
            <!-- Perfil de usuario -->
            <div class="profile-container">
                <div class="user-profile" id="user-profile-toggle">
                    <img src="<?php echo $imagen; ?>" 
                         alt="<?php echo $nombre . ' ' . $apellido; ?>" 
                         class="user-avatar-modern"
                         onerror="this.src='<?php echo $imagenBase; ?>'; this.classList.add('error-image')">
                    
                    <div class="user-info-compact">
                        <div class="user-name"><?php echo $nombre; ?></div>
                        <div class="user-role"><?php echo $rol; ?></div>
                    </div>
                    
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="dropdown-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </div>
                
                <div class="modern-dropdown" id="user-dropdown">
                    <div class="dropdown-header-modern">
                        <div class="dropdown-user-info">
                            <img src="<?php echo $imagen; ?>" 
                                 alt="<?php echo $nombre . ' ' . $apellido; ?>" 
                                 class="dropdown-user-avatar"
                                 onerror="this.src='<?php echo $imagenBase; ?>'">
                            <div class="dropdown-user-text">
                                <h6><?php echo $nombre . ' ' . $apellido; ?></h6>
                                <small><?php echo $rol; ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <a class="dropdown-item-modern" href="usuario.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        Ver usuarios
                    </a>
                    
                    <div class="dropdown-divider-modern"></div>
                    
                    <a class="dropdown-item-modern logout-item-modern" href="../controlador/Usuario/cerrar_sesion.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                        Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias directas
    const notificationBtn = document.getElementById('notification-btn-modern');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    const userProfileToggle = document.getElementById('user-profile-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    
    let activeDropdown = null;
    
    // Cerrar cualquier dropdown abierto
    function closeDropdowns() {
        if (activeDropdown) {
            activeDropdown.classList.remove('show');
            
            if (activeDropdown === userDropdown && userProfileToggle) {
                userProfileToggle.classList.remove('active');
            }
            
            activeDropdown = null;
        }
    }
    
    // Abrir un dropdown específico
    function openDropdown(dropdown, toggle) {
        closeDropdowns();
        
        dropdown.classList.add('show');
        if (toggle) {
            toggle.classList.add('active');
        }
        
        activeDropdown = dropdown;
    }
    
    // Evento para notificaciones - Simple y directo
    if (notificationBtn && notificationsDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (notificationsDropdown.classList.contains('show')) {
                closeDropdowns();
            } else {
                openDropdown(notificationsDropdown, null);
            }
            
            // Feedback táctil inmediato
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    }
    
    // Evento para perfil de usuario
    if (userProfileToggle && userDropdown) {
        userProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (userDropdown.classList.contains('show')) {
                closeDropdowns();
            } else {
                openDropdown(userDropdown, this);
            }
            
            // Feedback táctil inmediato
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    }
    
    // Cerrar al hacer clic en cualquier parte
    document.addEventListener('click', function(e) {
        if (!activeDropdown) return;
        
        const clickedInsideDropdown = activeDropdown.contains(e.target);
        const clickedOnNotificationBtn = notificationBtn && notificationBtn.contains(e.target);
        const clickedOnUserToggle = userProfileToggle && userProfileToggle.contains(e.target);
        
        if (!clickedInsideDropdown && !clickedOnNotificationBtn && !clickedOnUserToggle) {
            closeDropdowns();
        }
    });
    
    // Cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activeDropdown) {
            closeDropdowns();
        }
    });
    
    // Función para marcar notificaciones como leídas
    window.markAllAsRead = function() {
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => {
            item.classList.remove('unread');
        });
        
        const badge = notificationBtn.querySelector('.notification-badge-modern');
        if (badge) {
            badge.style.display = 'none';
        }
        
        // Cerrar después de un breve momento
        setTimeout(() => {
            closeDropdowns();
        }, 150);
    };
    
    // Simular notificaciones (ligero retraso para no bloquear)
    setTimeout(() => {
        const badge = notificationBtn.querySelector('.notification-badge-modern');
        if (badge) {
            badge.style.animation = 'pulse 1.5s infinite';
        }
    }, 500);
});
</script>
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

    /* Notificaciones */
    .notification-container {
        position: relative;
    }

    .notification-btn-modern {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        color: white;
    }

    .notification-btn-modern:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .notification-btn-modern:active {
        transform: scale(0.95);
        transition: transform 0.1s;
    }

    .notification-badge-modern {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: linear-gradient(45deg, #ff416c, #ff4b2b);
        border-radius: 50%;
        border: 2px solid #667eea;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Dropdown de notificaciones - Optimizado */
    .notifications-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.08);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.98);
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        overflow: hidden;
        pointer-events: none;
        will-change: transform, opacity;
    }

    .notifications-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .notifications-header {
        padding: 14px 16px;
        background: linear-gradient(135deg, #04bbf3 0%, #05436d 100%);
        color: white;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notifications-content {
        max-height: 400px;
        overflow-y: auto;
        padding: 6px;
    }

    .notification-item {
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 6px;
        background: #f8f9fa;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #e9ecef;
        transform: translateX(2px);
    }

    .notification-item.unread {
        background: #e3f2fd;
        border-left: 3px solid #2196f3;
    }

    /* Perfil de usuario */
    .profile-container {
        position: relative;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    .user-profile:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .user-profile:active {
        transform: scale(0.98);
        transition: transform 0.1s;
    }

    .user-avatar-modern {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .user-info-compact {
        color: white;
    }

    .user-name {
        font-weight: 600;
        font-size: 13px;
        line-height: 1.2;
    }

    .user-role {
        font-size: 11px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .user-role::before {
        content: '●';
        font-size: 6px;
        color: #00ff88;
    }

    .dropdown-arrow {
        transition: transform 0.15s ease;
        color: rgba(255, 255, 255, 0.7);
    }

    .user-profile.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Modern Dropdown Menu - Optimizado */
    .modern-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        background: white;
        min-width: 260px;
        padding: 6px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.08);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.98);
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        pointer-events: none;
        will-change: transform, opacity;
    }

    .modern-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .dropdown-header-modern {
        padding: 14px 16px;
        background: linear-gradient(135deg, #04bbf3 0%, #05436d 100%);
        margin: -6px -6px 6px -6px;
        color: white;
    }

    .dropdown-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dropdown-user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
    }

    .dropdown-user-text h6 {
        margin: 0;
        font-weight: 600;
        font-size: 15px;
    }

    .dropdown-user-text small {
        opacity: 0.9;
        font-size: 11px;
    }

    .dropdown-item-modern {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        transition: all 0.15s ease;
        font-weight: 500;
        font-size: 13px;
        margin: 3px 0;
    }

    .dropdown-item-modern:hover {
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        color: #667eea;
        transform: translateX(2px);
    }

    .dropdown-item-modern svg {
        width: 16px;
        height: 16px;
        opacity: 0.7;
    }

    .dropdown-item-modern:hover svg {
        opacity: 1;
    }

    .dropdown-divider-modern {
        margin: 6px 0;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    .logout-item-modern {
        color: #ff416c !important;
        background: linear-gradient(135deg, #ff416c15 0%, #ff4b2b15 100%);
    }

    .logout-item-modern:hover {
        background: linear-gradient(135deg, #ff416c25 0%, #ff4b2b25 100%) !important;
    }

    /* Scrollbar styling */
    .notifications-content::-webkit-scrollbar {
        width: 6px;
    }

    .notifications-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .notifications-content::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .notifications-content::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

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
            padding: 6px;
        }
        
        .notifications-dropdown,
        .modern-dropdown {
            position: fixed;
            top: 70px;
            left: 10px;
            right: 10px;
            min-width: auto;
            max-width: calc(100% - 20px);
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
                        <span>Notificaciones</span>
                        <button class="btn-clear" onclick="markAllAsRead()" style="background: none; border: none; color: white; font-size: 11px; cursor: pointer; padding: 4px 8px;">
                            Marcar como leídas
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
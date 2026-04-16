<link rel="stylesheet" href="../../assets/css/partials/header.css">

<div class="header-modern">
    <div class="header-content">
        <h1 class="page-title">
            <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Sistema de Gestión Inces'; ?>
        </h1>

        <div class="header-actions">

            <!-- ── Buscador global ── -->
            <div class="header-search-wrapper" id="header-search-container">
                <!-- El buscador se monta aquí por buscador.js al cargar -->
            </div>

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
                        <button class="btn-clear" onclick="markAllAsRead()" style="background:none;border:none;color:white;font-size:11px;cursor:pointer;padding:4px 8px;">
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

<!-- Lógica de dropdowns del header -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn        = document.getElementById('notification-btn-modern');
    const notificationsDropdown  = document.getElementById('notifications-dropdown');
    const userProfileToggle      = document.getElementById('user-profile-toggle');
    const userDropdown           = document.getElementById('user-dropdown');

    let activeDropdown = null;

    function closeDropdowns() {
        if (activeDropdown) {
            activeDropdown.classList.remove('show');
            if (activeDropdown === userDropdown && userProfileToggle)
                userProfileToggle.classList.remove('active');
            activeDropdown = null;
        }
    }

    function openDropdown(dropdown, toggle) {
        closeDropdowns();
        dropdown.classList.add('show');
        if (toggle) toggle.classList.add('active');
        activeDropdown = dropdown;
    }

    if (notificationBtn && notificationsDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsDropdown.classList.contains('show')
                ? closeDropdowns()
                : openDropdown(notificationsDropdown, null);
            this.style.transform = 'scale(0.95)';
            setTimeout(() => { this.style.transform = ''; }, 100);
        });
    }

    if (userProfileToggle && userDropdown) {
        userProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.contains('show')
                ? closeDropdowns()
                : openDropdown(userDropdown, this);
            this.style.transform = 'scale(0.98)';
            setTimeout(() => { this.style.transform = ''; }, 100);
        });
    }

    document.addEventListener('click', function(e) {
        if (!activeDropdown) return;
        const insideDropdown    = activeDropdown.contains(e.target);
        const onNotificationBtn = notificationBtn && notificationBtn.contains(e.target);
        const onUserToggle      = userProfileToggle && userProfileToggle.contains(e.target);
        if (!insideDropdown && !onNotificationBtn && !onUserToggle) closeDropdowns();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activeDropdown) closeDropdowns();
    });

    window.markAllAsRead = function() {
        document.querySelectorAll('.notification-item.unread')
                .forEach(item => item.classList.remove('unread'));
        const badge = notificationBtn.querySelector('.notification-badge-modern');
        if (badge) badge.style.display = 'none';
        setTimeout(() => closeDropdowns(), 150);
    };

    setTimeout(() => {
        const badge = notificationBtn.querySelector('.notification-badge-modern');
        if (badge) badge.style.animation = 'pulse 1.5s infinite';
    }, 500);
});
</script>

<!-- Buscador global — debe cargarse después del script de dropdowns -->
<script src="../../assets/js/partials/buscador.js"></script>
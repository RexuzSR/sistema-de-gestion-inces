<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
    <title>INCES Industrial — Iniciar Sesión</title>
</head>
<body>

    <!-- ══════════════════════════════════════════════════════════════
         CAPA 1 — FONDO: esferas sobre el gradiente del body
    ══════════════════════════════════════════════════════════════ -->
    <div class="orbs-layer">
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
        <div class="lava-orb"></div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         CAPA 2 — IMAGEN DIAGONAL
    ══════════════════════════════════════════════════════════════ -->
    <div class="login-bg-image"></div>

    <?php
    $loginErrorMsg = $_SESSION['error_login'] ?? '';
    ?>

    <!-- ══════════════════════════════════════════════════════════════
         CAPA 2 — PANEL + CARD
    ══════════════════════════════════════════════════════════════ -->
    <div class="login-left-panel">
        <div class="login-card">

            <!-- ── VISTA FORMULARIO ─────────────────────────────── -->
            <div class="card-form-view" id="cardFormView">

                <!-- Banner de credenciales inválidas o estado de usuario -->
                <div class="credentials-error" id="credentialsError">
                    <?php echo htmlspecialchars($loginErrorMsg ?: 'Credenciales Inválidas. Verifica tu usuario y contraseña.'); ?>
                </div>

                <!-- MARCA -->
                <div class="login-brand-wrap">
                    <img class="inces__logo" src="../../assets/img/inces-logo.png" alt="Logo INCES">
                    <div class="login-brand-divider"></div>
                    <div class="login-brand-text">
                        <span class="login-brand-name">INCES Industrial</span>
                        <span class="login-brand-sub">Bolivariano</span>
                        <div class="login-brand-dot"></div>
                    </div>
                </div>

                <h1 class="login-headline">Iniciar Sesión</h1>
                <p class="login-subheadline">Accede a tu cuenta institucional</p>
                <span class="login-accent-line"></span>

                <form id="loginForm" action="../controlador/Usuario/login.php" method="POST" autocomplete="off">

                    <label class="login-label" for="nombre_usuario">Usuario</label>
                    <div class="input-wrap">
                        <input class="input" type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Introduce tu usuario" maxlength="64" required>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#1a1a2e" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                        </span>
                    </div>

                    <label class="login-label" for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <input class="input" type="password" id="contrasena" name="contraseña" placeholder="Introduce tu contraseña" maxlength="64" required>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#1a1a2e" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                            </svg>
                        </span>
                    </div>

                    <button type="submit" class="boton" id="loginBtn">Iniciar Sesión</button>

                </form>

                <p class="login-footer-note">Sistema de Gestión · INCES Industrial Bolivariano</p>
            </div>

            <!-- ── VISTA FEEDBACK ────────────────────────────────── -->
            <div class="card-feedback-view" id="cardFeedbackView">
                <div class="feedback-icon-wrap">

                    <!-- Spinner -->
                    <div class="feedback-spinner" id="feedbackSpinner"></div>

                    <!-- Checkmark OK -->
                    <svg class="feedback-svg" id="feedbackOk" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="36" cy="36" r="32" stroke="#16a34a" stroke-width="4"/>
                        <polyline points="20,37 31,48 52,26" stroke="#16a34a" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <!-- X Error -->
                    <svg class="feedback-svg" id="feedbackError" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="36" cy="36" r="32" stroke="#dc2626" stroke-width="4"/>
                        <line x1="23" y1="23" x2="49" y2="49" stroke="#dc2626" stroke-width="4.5" stroke-linecap="round"/>
                        <line x1="49" y1="23" x2="23" y2="49" stroke="#dc2626" stroke-width="4.5" stroke-linecap="round"/>
                    </svg>

                </div>
                <span class="feedback-label" id="feedbackLabel">Verificando credenciales…</span>
            </div>

        </div>
    </div>

    <!-- Pasamos el error de sesión PHP a JS de forma segura -->
    <?php if (!empty($loginErrorMsg)): ?>
    <script>
        window.__loginError = true;
        window.__loginErrorMessage = <?php echo json_encode($loginErrorMsg); ?>;
    </script>
    <?php unset($_SESSION['error_login']); ?>
    <?php else: ?>
    <script>
        window.__loginError = false;
        window.__loginErrorMessage = '';
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] == '1'): ?>
    <script>window.__logoutSuccess = true;</script>
    <?php else: ?>
    <script>window.__logoutSuccess = false;</script>
    <?php endif; ?>

    <script src="../../assets/js/login.js"></script>
</body>
</html>
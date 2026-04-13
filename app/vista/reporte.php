<?php
// ==================== SEGURIDAD DE SESIÓN ====================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php'); exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// ==================== FIN SEGURIDAD ====================

require_once '../modelo/conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConexion();

$nombre   = htmlspecialchars($_SESSION['nombre']   ?? '');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol      = htmlspecialchars($_SESSION['rol']      ?? '');

$imagenBase    = '../../assets/img/avatar/default-user.png';
$imagenUsuario = $_SESSION['imagen'] ?? '';
if (!empty($imagenUsuario)) {
    $imagen = (strpos($imagenUsuario, 'assets/') === 0) ? '../../' . $imagenUsuario : $imagenUsuario;
} else {
    $imagen = $imagenBase;
}
$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/toastr.min.css">
    <link rel="stylesheet" href="../../assets/css/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/home.css">
    <link rel="stylesheet" href="../../assets/css/reporte.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Reportes del Sistema</title>
</head>
<body>
<div class="wrapper">
    <!-- SideBar -->
    <aside id="sidebar" class="expand">
        <div class="d-flex">
            <button id="toggle-btn" type="button" class="btn btn-dark d-flex align-items-center justify-content-center p-2 me-2 mt-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" class="bi bi-list" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                </svg>
            </button>
            <div class="sidebar-logo">
                <img src="../../assets/img/inces-logo.png" alt="inces-logo">
            </div>
        </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item"><a href="inicio.php" class="sidebar-link active-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg><span>Inicio</span></a></li>
                <li class="sidebar-item"><a href="participante.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg><span>Participantes</span></a></li>
                <li class="sidebar-item"><a href="cursos.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg><span>Cursos</span></a></li>
                <li class="sidebar-item"><a href="docente.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-workspace" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg><span>Docentes</span></a></li>
                <li id="sidebar-usuario" class="sidebar-item"><a href="usuario.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-vcard-fill" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg><span>Usuarios</span></a></li>
                <li class="sidebar-item"><a href="metas.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16"><path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.002-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001"/></svg><span>Metas</span></a></li>
                <li class="sidebar-item"><a href="reporte.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill" viewBox="0 0 16 16"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg><span>Reportes</span></a></li>
                <li class="sidebar-item"><a href="Historial.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/></svg><span>Historial</span></a></li>
            </ul>
    </aside>

    <!-- Main -->
    <div class="main">
        <?php $pageTitle = 'Reportes'; include __DIR__ . '/partials/header.php'; ?>

        <!-- Contenido directamente sobre el fondo, sin caja/tarjeta contenedora -->
        <div class="rp-page-root">
            <div id="bookmarkSidebar" style="display:none;"></div>

            <div id="contentArea">
                <div id="cardsContainer"></div>
                <div id="reportContainer" style="display:none;"></div>
            </div>

            <div id="loadingOverlay" class="loading-overlay">
                <div class="text-center">
                    <div class="rp-spinner"></div>
                    <p class="rp-loading-text mt-3">Cargando reporte...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/chart.js"></script>

<script src="../../assets/js/Reporte/buscador.js"></script>
<script src="../../assets/js/Reporte/RE-Participante-Total.js"></script>
<script src="../../assets/js/Reporte/RE-Participante-Aprobados.js"></script>
<script src="../../assets/js/Reporte/RE-Participante-Ambito.js"></script>
<script src="../../assets/js/Reporte/RE-Curso-Total.js"></script>
<script src="../../assets/js/Reporte/RE-Curso-MasParticipantes.js"></script>
<script src="../../assets/js/Reporte/RE-Curso-MasRequerido.js"></script>
<script src="../../assets/js/Reporte/reportes-docentes.js"></script>
<script src="../../assets/js/Reporte/RE-Docente-MasSolicitados.js"></script>
<script src="../../assets/js/Reporte/RE-Docente-Total.js"></script>
<script src="../../assets/js/Reporte/RE-Docente-Inactivos.js"></script>
<script src="../../assets/js/Reporte/reportes-metas.js"></script>
<script src="../../assets/js/Reporte/reportes.js"></script>
<script src="../../assets/js/home.js"></script>
<script src="../../assets/js/notifications.js"></script>

<script>
    const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";
    if (userRole === "Apoyo") {
        const usuarioItem = document.querySelector('.sidebar-item a[href="usuario.php"]');
        if (usuarioItem) usuarioItem.closest('.sidebar-item').remove();
    }
</script>

<script>
(function () {
    const INACTIVIDAD_MS = 10 * 60 * 1000; // 10 minutos exactos
    const AVISO_MS       =  1 * 60 * 1000; // aviso al minuto 9
    const LOGOUT_URL     = '../../controlador/Usuario/cerrar_sesion.php';
    const LOGIN_URL      = 'index.php?sesion=expirada';

    let timerLogout, timerAviso;
    let avisoActivo = false;

    function resetTimer() {
        clearTimeout(timerLogout);
        clearTimeout(timerAviso);

        if (avisoActivo) {
            const aviso = document.getElementById('aviso-inactividad');
            if (aviso) aviso.remove();
            avisoActivo = false;
        }

        // Aviso al minuto 9
        timerAviso = setTimeout(mostrarAviso, INACTIVIDAD_MS - AVISO_MS);

        // Cierre al minuto 10
        timerLogout = setTimeout(cerrarSesion, INACTIVIDAD_MS);
    }

    function mostrarAviso() {
        if (document.getElementById('aviso-inactividad')) return;
        avisoActivo = true;

        let segundos = 60;

        const div = document.createElement('div');
        div.id = 'aviso-inactividad';
        div.innerHTML = `
            <div style="
                position:fixed; bottom:24px; right:24px; z-index:99999;
                background:#1e293b; color:#f1f5f9;
                border-left:4px solid #f59e0b; border-radius:8px;
                padding:16px 20px; max-width:320px;
                box-shadow:0 4px 20px rgba(0,0,0,0.35);
                font-family:sans-serif; font-size:14px;
            ">
                <strong style="color:#f59e0b; display:block; margin-bottom:6px;">
                    ⚠️ Sesión por expirar
                </strong>
                <p style="margin:0 0 12px; color:#cbd5e1;">
                    Tu sesión se cerrará en <span id="cuenta-regresiva"><strong>60</strong></span> segundos por inactividad.
                </p>
                <button id="btn-seguir-conectado" style="
                    background:#3b82f6; color:#fff; border:none;
                    border-radius:5px; padding:7px 16px;
                    cursor:pointer; font-size:13px;
                ">
                    Seguir conectado
                </button>
            </div>
        `;
        document.body.appendChild(div);

        // Cuenta regresiva visual
        const intervalo = setInterval(() => {
            segundos--;
            const el = document.getElementById('cuenta-regresiva');
            if (el) el.innerHTML = `<strong>${segundos}</strong>`;
            if (segundos <= 0) clearInterval(intervalo);
        }, 1000);

        document.getElementById('btn-seguir-conectado')
            .addEventListener('click', () => {
                clearInterval(intervalo);
                resetTimer();
            });
    }

    function cerrarSesion() {
        fetch(LOGOUT_URL, { method: 'POST', credentials: 'same-origin' })
            .finally(() => { window.location.href = LOGIN_URL; });
    }

    // Cualquier actividad del usuario reinicia el contador
    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click']
        .forEach(ev => document.addEventListener(ev, resetTimer, { passive: true }));

    // Arrancar al cargar la página
    resetTimer();
})();
</script>

</body>
</html>
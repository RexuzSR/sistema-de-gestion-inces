<?php
// ==================== SEGURIDAD DE SESIÓN ====================
// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');

session_name('SISTEMA_CONTROL_INCES');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Prevenir cache para evitar botón "Atrás"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// ==================== FIN SEGURIDAD ====================

require_once '../modelo/conexion.php';

// Crear instancia de conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Preparar los datos primero
$nombre = htmlspecialchars($_SESSION['nombre'] ?? '');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol = htmlspecialchars($_SESSION['rol'] ?? '');

// Manejar la ruta de la imagen correctamente
$imagenBase = '../../assets/img/avatar/default-user.png';
$imagenUsuario = $_SESSION['imagen'] ?? '';

// Construir la ruta completa de la imagen
if (!empty($imagenUsuario)) {
    if (strpos($imagenUsuario, 'assets/') === 0) {
        $imagen = '../../' . $imagenUsuario;
    } else {
        $imagen = $imagenUsuario;
    }
} else {
    $imagen = $imagenBase;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/toastr.min.css">
    <link rel="stylesheet" href="../../assets/css/home.css">
    <link rel="stylesheet" href="../../assets/css/crud.css">
    <link rel="stylesheet" href="../../assets/css/docente.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Control - Docentes</title>
</head>
<body>

    <div class="wrapper">
        <!-- SideBar -->
        <?php $activePage = 'docente'; include __DIR__ . '/partials/sidebar.php'; ?>
        <!-- Main -->
        <div class="main">

            <!-- Header -->
            <?php 
            $pageTitle = 'Docentes'; 
            include __DIR__ . '/partials/header.php'; 
            ?>

            <!-- Main Content -->
            <div class="contenedor">
                    
                <div class="box d-flex justify-content-center flex-column shadow-lg px-3 mt-4">
                
                <!-- Barra de búsqueda -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <form class="input-group search-bar" style="max-width: 526px">
                        <input 
                            type="text" 
                            id="searchInput"
                            class="form-control form-control-sm" 
                            placeholder="Buscar...">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </span>
                    </form>
                </div>

                    <div class="filters-container d-flex justify-content-between align-items-center pt-2">
                        <!-- Filtros -->
                        <form id="filters-form" class="d-flex align-items-center gap-2">
                            <label class="label" for="sexo">Sexo:</label>
                            <select class="form-select form-select-sm" name="sexo" id="sexo-filter">
                                <option value="">Todos</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
            
                            <label class="label" for="edad">Edad:</label>
                            <select class="form-select form-select-sm" name="edad" id="edad-filter">
                                <option value="">Todos</option>
                                <?php
                                    // selección de la edad
                                    for ($i = 18; $i <= 65; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
            
                            <label class="label" for="estado">Estado:</label>
                            <select class="form-select form-select-sm" name="estado" id="estado-filter">
                                <option value="">Todos</option>
                                <option value="Disponible">Disponible</option>
                                <option value="En formación">En formación</option>
                                <option value="Asignado">Asignado</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </form>

                        <!-- Botón para agregar Docentes-->
                        <?php if (($_SESSION['rol'] ?? '') !== 'Apoyo'): ?>
                        <a href="#" id="btn-addDocente" class="btn-add d-flex justify-content-center align-items-center gap-3 text-white" data-bs-toggle="modal" data-bs-target="#modal-addDocente" style="width: 19.5%">
                            Agregar Docentes
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Tabla de docentes -->
                    <div class="table-responsive mt-3">
                        <table id="tabla-docentes" class="table table-hover">
                            <thead>
                                <tr>
                                    <th data-sort="id_docente">ID <span class="sort-icon">↓</span></th>
                                    <th data-sort="cedula">Cédula</th>
                                    <th data-sort="nombre">Nombre</th>
                                    <th data-sort="apellido">Apellido</th>
                                    <th data-sort="genero">Género</th>
                                    <th data-sort="edad">Edad</th>
                                    <th data-sort="correo">Correo</th>
                                    <th data-sort="estado">Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div id="paginacion-container"></div>
                    <div class="registros-crud-container">
                        <div class="registros-crud-selector">
                            <label for="registros">Mostrar</label>
                            <select id="registros">
                                <option value="8" selected>8</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="0">Todos</option>
                            </select>
                            <span>registros por página</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php include '../modales/Docente/modal-addDocente.php'; ?>
    <?php include '../modales/Docente/modal-desinscribirDocente.php'; ?>
    <?php include '../modales/Docente/modal-editDocente.php'; ?>
    <?php include '../modales/Docente/modal-viewDocente.php'; ?>
    <?php include '../modales/Docente/modal-AsignarCursoDocente.php'; ?>
    <?php include '../modales/Curso/modal-viewCurso.php'; ?>

    <!-- Scripts -->
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/toastr/toastr.min.js"></script>
    <script src="../../assets/js/toastr/toastr-config.js"></script>
    <script src="../../assets/js/chart.js"></script>
    <script src="../../assets/js/home.js"></script>
    <script src="../../assets/js/Docente/tabla-unificada-docentes.js"></script>
    <script src="../../assets/js/Docente/agregarDocente.js"></script>
    <script src="../../assets/js/Docente/editarDocente.js"></script>
    <script src="../../assets/js/Docente/asignarCursoDocente.js"></script>
    <script src="../../assets/js/Docente/viewDocente.js"></script>
    <script src="../../assets/js/Curso/estadoCurso.js"></script>
    <script src="../../assets/js/Curso/verCurso.js"></script>
    <script src="../../assets/js/pdf.js"></script>
    <script src="../../assets/js/notifications.js"></script>

    <script>
        const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";

        if (userRole === "Apoyo") {
            const usuarioItem = document.getElementById('sidebar-usuario');
            if (usuarioItem) {
                usuarioItem.remove();
            }
        }
        
        (function () {
            const params  = new URLSearchParams(window.location.search);
            const termino = params.get('buscar');
            if (!termino) return;

            const MAX = 40;
            let intentos = 0;

            const iv = setInterval(function () {
                intentos++;
                const input = document.getElementById('searchInput');
                const filas = document.querySelectorAll('#tabla-docentes tbody tr');

                if (input && filas.length > 0) {
                    clearInterval(iv);

                    input.value = termino;
                    /* tabla-unificada-docentes.js escucha 'input' y 'keyup' */
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('keyup',  { bubbles: true }));

                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    input.focus();

                    /* Limpiar la URL sin recargar */
                    const url = new URL(window.location);
                    url.searchParams.delete('buscar');
                    history.replaceState({}, '', url);
                }

                if (intentos >= MAX) clearInterval(iv);
            }, 100);
        })();
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
        fetch(LOGOUT_URL + '?inactividad=1', { method: 'POST', credentials: 'same-origin' })
            .finally(() => { window.location.replace(LOGIN_URL); });
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

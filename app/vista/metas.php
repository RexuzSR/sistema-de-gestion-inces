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
    <link rel="stylesheet" href="../../assets/css/bootstrap-icons/bootstrap-icons.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/metas.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <title>Control - Metas</title>
</head>
<body>

    <div class="wrapper">
        <!-- SideBar -->
        <?php $activePage = 'metas'; include __DIR__ . '/partials/sidebar.php'; ?>
        <!-- Fin SideBar -->
        <div class="main">
            <!-- Header -->
            <?php 
            $pageTitle = 'Metas'; 
            include __DIR__ . '/partials/header.php'; 
            ?>

            <!-- Main Content -->
            <div class="contenedor">
                    
                <div class="box d-flex justify-content-center flex-column shadow-lg px-3 mt-4 metas-container">
                
                <!-- Barra de búsqueda -->
                <div class="d-flex justify-content-between align-items-center mt-4 controls-container">
                    <div class="search-container">
                        <input 
                            type="text" 
                            id="searchInput"
                            class="form-control form-control-sm" 
                            placeholder="Buscar metas...">
                    </div>

                    <div class="records-selector d-flex align-items-center gap-2">
                        <label class="form-label-meta mb-0">Mostrar:</label>
                        <select class="form-control-meta form-select-sm" id="registros">
                            <option value="5">5</option>
                            <option value="8" selected>8</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="0">Todos</option>
                        </select>
                    </div>
                </div>

                <!-- Aviso de Filtro Activo -->
                <div id="active-filter-alert" class="mt-2" style="display: none;">
                    <div class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-0" style="border-radius: 12px; background: rgba(2, 129, 254, 0.1); border: 1px solid rgba(2, 129, 254, 0.2);">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0281fe" class="bi bi-filter-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M3.5 5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1 0-1M5 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m2 3.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5"/>
                            </svg>
                            <span style="color: #0f172a; font-size: 0.9rem;">Filtrando por el ámbito del curso seleccionado</span>
                        </div>
                        <button type="button" id="clear-ambito-filter" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-size: 0.75rem; padding: 2px 12px; border-width: 1.5px; font-weight: 600;">
                            VER TODAS LAS METAS
                        </button>
                    </div>
                </div>

                <div class="filters-container d-flex justify-content-between align-items-center pt-2">
                        <!-- Filtros -->
                        <form id="filters-form" class="d-flex align-items-center gap-2">
                            <label class="label text-nowrap" for="tipo de meta">Tipo de objetivo:</label>
                            <select class="form-select form-select-sm" name="tipoDeMeta" id="TipoDeMeta-filter">
                                <option value="">Todos</option>
                                <option value="Aprobados">Aprobados</option>
                                <option value="Inscritos">Inscritos</option>
                                <option value="Ingresados">Ingresados</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
            
                            <label class="label" for="estado">Estado:</label>
                            <select class="form-select form-select-sm" name="estado" id="estado-filter">
                                <option value="">Todos</option>
                                <option value="Cumplida">Cumplida</option>
                                <option value="En progreso">En progreso</option>
                                <option value="No cumplida">No cumplida</option>
                            </select>

                            <label class="label" for="periodo">Período:</label>
                            <select class="form-select form-select-sm" name="periodo" id="periodo-filter">
                                <option value="">Todos</option>
                                <option value="mensual">Mensual</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </form>

                        <?php if (($_SESSION['rol'] ?? '') !== 'Apoyo'): ?>
                        <button id="btn-addMeta" class="btn-add-meta d-flex justify-content-center align-items-center gap-2 text-white" data-bs-toggle="modal" data-bs-target="#modal-addMeta">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                            Agregar Meta
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive mt-3 table-container">
                        <table id="tabla-metas" class="table table-hover table-metas">
                            <thead>
                                <tr>
                                    <th data-sort="id_meta">ID</th>
                                    <th data-sort="meta_objetivo">Meta Objetivo</th>
                                    <th data-sort="tipo_objetivo">Tipo Objetivo</th>
                                    <th data-sort="estudiantes_formados">Estudiantes Formados</th>
                                    <th data-sort="porcentaje_cumplimiento" class="text-center">% Cumplimiento</th>
                                    <th data-sort="fecha_inicio">Fecha Inicio</th>
                                    <th data-sort="fecha_fin">Fecha Fin</th>
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
                    <div class="d-flex justify-content-between align-items-center mt-3 pagination-container">
                        <nav aria-label="Page navigation">
                            <ul class="pagination-list" id="pagination">
                                <!-- La paginación se generará dinámicamente -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../modales/Metas/modal-addmetas.php'; ?>
    <?php include '../modales/Metas/modal-editmetas.php'; ?>
    <?php include '../modales/Metas/modal-viewmetas.php'; ?>
    <?php include '../modales/Metas/modal-deletedmetas.php'; ?>
    
    <!-- Scripts -->
    <script src="../../assets/js/chart.js"></script>
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/toastr/toastr.min.js"></script>
    <script src="../../assets/js/toastr/toastr-config.js"></script>
    <script src="../../assets/js/home.js"></script>
    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/Metas/tabla-metas.js"></script>

    <script>
        const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";

        if (userRole === "Apoyo") {
            const usuarioItem = document.getElementById('sidebar-usuario');
            if (usuarioItem) {
                usuarioItem.remove();
            }
        }
    </script>
    
    <script>
        // Función auxiliar para determinar clase de progreso
        function getProgressClass(pct) {
            if (pct >= 86) return 'progress-bar-success';
            if (pct >= 70) return 'progress-bar-warning';
            return 'progress-bar-danger';
        }
        
        // Función auxiliar para determinar clase de estado
        function getEstadoClass(estado) {
            switch(estado.toLowerCase()) {
                case 'alcanzada': return 'status-active';
                case 'en proceso': return 'status-pending';
                case 'no alcanzada': return 'status-inactive';
                default: return 'status-pending';
            }
        }

        
        (function () {
            const params  = new URLSearchParams(window.location.search);
            const termino = params.get('buscar');
            if (!termino) return;

            const MAX = 50;   // hasta 5 seg porque tabla-metas.js carga async
            let intentos = 0;

            const iv = setInterval(function () {
                intentos++;

                const input = document.getElementById('searchInput');
                const filas = document.querySelectorAll('#tabla-metas tbody tr');

                if (input && filas.length > 0) {
                    clearInterval(iv);

                    // Escribir el término en el buscador y disparar los eventos
                    // que tabla-metas.js escucha para filtrar
                    input.value = termino;
                    input.dispatchEvent(new Event('input',  { bubbles: true }));
                    input.dispatchEvent(new Event('keyup',  { bubbles: true }));
                    input.dispatchEvent(new KeyboardEvent('keydown', { bubbles: true }));

                    // Scroll suave al input y foco
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    input.focus();

                    // Resaltar la fila que coincide con el ID para que sea fácil de ver
                    setTimeout(function () {
                        document.querySelectorAll('#tabla-metas tbody tr').forEach(function (tr) {
                            const idCell = tr.querySelector('td:first-child');
                            if (idCell && idCell.textContent.trim() === termino) {
                                tr.style.outline        = '2px solid #ef4444';
                                tr.style.outlineOffset  = '-2px';
                                tr.style.background     = 'rgba(239,68,68,.08)';
                                tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                    }, 350);

                    // Limpiar URL
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

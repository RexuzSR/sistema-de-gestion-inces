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

// Verificar si hay sesión activa - SOLO REDIRIGIR, NO DESTRUIR
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Solo redirigir al login, NO destruir sesión aquí
    header('Location: index.php');
    exit;
}

// Prevenir cache para evitar botón "Atrás"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// ==================== FIN SEGURIDAD ====================

require_once '../modelo/conexion.php';


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirigir al login si no hay sesión activa
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Crear instancia de conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Consultar opciones para el selector de Turno
$query_turnos = "SELECT DISTINCT turno FROM tb_curso";
$result_turnos = $conn->query($query_turnos);

// Consultar opciones para el selector de Modalidad desde tb_modalidad
$query_modalidades = "SELECT id_modalidad, nombre_modalidad FROM tb_modalidad";
$result_modalidades = $conn->query($query_modalidades);

// Consultar opciones para el selector de Estado desde tb_estatuscurso
$query_estados = "SELECT id_estatusCurso, nombre_estatusCurso FROM tb_estatuscurso WHERE nombre_estatusCurso != 'En pausa'";
$result_estados = $conn->query($query_estados);

// Consultar opciones para el selector de Año
$query_anios = "SELECT DISTINCT YEAR(fecha_inicio) AS anio FROM tb_curso WHERE fecha_inicio IS NOT NULL AND fecha_inicio != '0000-00-00' ORDER BY anio DESC";
$result_anios = $conn->query($query_anios);

// Preparar los datos primero
$nombre = htmlspecialchars($_SESSION['nombre'] ?? '');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol = htmlspecialchars($_SESSION['rol'] ?? '');

// Manejar la ruta de la imagen correctamente
$imagenBase = '../../assets/img/avatar/default-user.png';
$imagenUsuario = $_SESSION['imagen'] ?? '';

// Construir la ruta completa de la imagen
if (!empty($imagenUsuario)) {
    // Si la imagen comienza con 'assets/', ajustamos la ruta relativa
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
    <link rel="stylesheet" href="../../assets/css/curso.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <!-- Flatpickr (datepicker que permite deshabilitar fines de semana) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Cursos</title>
    <style>
        /* Animación para cuando cree un curso */
        /* no toquen esto funciona y solo Dios sabe */
        /* y cuidado con paginacionCurso.js eso ahi es del demonio */
        @keyframes cursoNuevoPulso {
            0%   { background-color: transparent; }
            20%  { background-color: rgba(12, 98, 228, 0.3); }
            50%  { background-color: rgba(12, 98, 228, 0.15); }
            80%  { background-color: rgba(12, 98, 228, 0.3); }
            100% { background-color: transparent; }
        }
        
        tr.curso-nuevo td {
            animation: cursoNuevoPulso 1.5s ease-in-out 3;
            position: relative;
        }

        tr.curso-nuevo td:first-child {
            border-left: 5px solid #0c62e4 !important;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- SideBar -->
                <?php $activePage = 'cursos'; include __DIR__ . '/partials/sidebar.php'; ?>
        <!-- Main -->
        <div class="main">

            <!-- Header -->
            <?php $pageTitle = 'Cursos'; include __DIR__ . '/partials/header.php'; ?>

            <!-- Main Content -->
            <div class="contenedor">

                <div class="box d-flex justify-content-center flex-column shadow-lg px-3 mt-4 pt-1" style="width: 98% !important;">
                    <!-- Tarjetas KPI -->
                    <div class="row g-3 mb-4 kpi-row">
                        <!-- Total Cursos -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-total">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Total de cursos registrados bajo los filtros seleccionados."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div class="kpi-content">
                                    <span class="kpi-value" id="kpi-total">0</span>
                                    <span class="kpi-label">Total Cursos</span>
                                </div>
                            </div>
                        </div>
                        <!-- En Proceso -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-proceso">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Cursos que se encuentran actualmente en dictado/formación."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                                <div class="kpi-content">
                                    <span class="kpi-value" id="kpi-proceso">0</span>
                                    <span class="kpi-label">En Proceso</span>
                                </div>
                            </div>
                        </div>
                        <!-- En Espera -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-espera">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Cursos planificados en espera de fecha de inicio o requisitos mínimos."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="kpi-content">
                                    <span class="kpi-value" id="kpi-espera">0</span>
                                    <span class="kpi-label">En Espera</span>
                                </div>
                            </div>
                        </div>
                        <!-- Diferidos -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-diferidos">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Cursos cuya fecha de inicio se ha pospuesto por falta de requisitos o participantes mínimos."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                </div>
                                <div class="kpi-content">
                                    <span class="kpi-value" id="kpi-diferidos">0</span>
                                    <span class="kpi-label">Diferidos</span>
                                </div>
                            </div>
                        </div>
                        <!-- Culminados -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-culminados">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Cursos finalizados con carga de notas y participantes aprobados/reprobados."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="kpi-content">
                                    <span class="kpi-value" id="kpi-culminados">0</span>
                                    <span class="kpi-label">Culminados</span>
                                </div>
                            </div>
                        </div>
                        <!-- Cupos Disponibles -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="kpi-card kpi-cupos">
                                <i class="bi bi-info-circle kpi-info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Total de cupos libres / capacidad total de participantes en los cursos."></i>
                                <div class="kpi-icon-wrapper">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="kpi-content">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="kpi-value" id="kpi-cupos">0</span>
                                        <span class="text-muted" id="kpi-cupos-total" style="font-size: 0.9rem; font-weight: 500;">/ 0</span>
                                    </div>
                                    <span class="kpi-label">Cupos Disponibles</span>
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- Barra de búsqueda -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <form class="input-group search-bar">
                            <input 
                                type="text" 
                                id="searchInput" 
                                onkeyup="buscarCurso()" 
                                class="form-control form-control-sm" 
                                placeholder="Buscar...">
                            <span class="input-group-text">
                              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="24" fill="#000000" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                              </svg>
                            </span>
                        </form>
                    </div>

                    <!-- Filtros -->
                    <div class="filters-container d-flex justify-content-between pt-2 pb-4">
    <form class="d-flex align-items-center gap-2" id="filtros_curso">
        
        <label class="label" for="turno_filter">Turno:</label>
        <select class="form-select form-select-sm" name="turno_filter" id="turno_filter">
            <option value="">Todos</option>
            <?php while ($row = $result_turnos->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['turno']); ?>"><?= htmlspecialchars($row['turno']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <label class="label" for="modalidad_filter">Modalidad:</label>
        <select class="form-select form-select-sm" name="modalidad_filter" id="modalidad_filter">
            <option value="">Todas</option>
            <?php while ($row = $result_modalidades->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['id_modalidad']); ?>"><?= htmlspecialchars($row['nombre_modalidad']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <label class="label" for="estado_filter">Estado:</label>
        <select class="form-select form-select-sm" name="estado_filter" id="estado_filter">
            <option value="">Todos</option>
            <?php while ($row = $result_estados->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['id_estatusCurso']); ?>"><?= htmlspecialchars($row['nombre_estatusCurso']); ?></option>
            <?php endwhile; ?>
        </select>

        <label class="label" for="anio_filter">Año:</label>
        <select class="form-select form-select-sm" name="anio_filter" id="anio_filter">
            <option value="">Todos</option>
            <?php while ($row = $result_anios->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= htmlspecialchars($row['anio']); ?>"><?= htmlspecialchars($row['anio']); ?></option>
            <?php endwhile; ?>
        </select>

        <!-- botón de Filtros -->
        <button id="btn-filter" class="filter" title="Limpiar Filtros" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-filter" viewBox="0 0 16 16">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </button>
    </form>

                        <!-- Botón para agregar Cursos-->
                        <?php if (($_SESSION['rol'] ?? '') !== 'Apoyo'): ?>
                        <a href="#" id="btn-addcurso" class="btn-add d-flex justify-content-center align-items-center gap-3 text-white" data-bs-toggle="modal" data-bs-target="#modal-addCurso">Agregar Cursos<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                          </svg></a>
                        <?php endif; ?>
    
                    </div>

                    
                     <!-- Contenedor del Resumen Contextual -->
                     <div class="row g-3 mb-3 mt-1">
                         <div class="col-12">
                             <div id="contextual-summary" class="p-3 shadow-sm mb-2" style="display: none;">
                                 <!-- Se poblará dinámicamente -->
                             </div>
                         </div>
                     </div>

                     <!-- Tabla de estudiantes -->
            <div class="table-responsive">
                <table id="tabla-cursos" class="table rounded-5 mt-3 table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Modalidad</th>
                            <th class="text-center">Ocupación</th>
                            <th class="text-center">Cupos Libres</th>
                            <th>Turno</th>
                            <th>Duración</th>
                            <th class="text-nowrap">Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- Se generarn dinamicamente los registros através del archivo tabla-participante.js -->
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

    <?php include '../modales/Curso/cursoModal.php'; ?>
    <?php include '../modales/Curso/modal-addCurso.php'; ?>
    <?php include '../modales/modal-addParticipante.php'; ?>
    <?php include '../modales/Curso/GuardarModal.php'; ?>
    <?php include '../modales/Curso/modalReiniciarCurso.php'; ?>
    <?php include '../modales/Curso/modal-deleteCurso.php'; ?>
    <?php include '../modales/Curso/modal-finalizarCurso.php'; ?>
    <?php include '../modales/Curso/modal-editCurso.php'; ?>
    <?php include '../modales/Curso/modal-viewCurso.php'; ?>
    <?php include '../modales/Curso/modal-AsignarDocenteCurso.php'; ?>
    <?php include '../modales/Curso/modal-participanteCurso.php'; ?>

    <script src="../../assets/js/Curso/estadoCurso.js"></script>
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/toastr/toastr.min.js"></script>
    <script src="../../assets/js/toastr/toastr-config.js"></script>
    <script src="../../assets/js/home.js"></script>
    <script src="../../assets/js/Curso/tabla-curso.js?v=<?php echo time(); ?>"></script>
    <script src="../../assets/js/Curso/verCurso.js"></script>
    <script src="../../assets/js/Curso/editarCurso.js"></script>
    <script src="../../assets/js/Curso/AsignarParticipante.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="../../assets/js/Curso/asignarCalificacion.js"></script>
    <script src="../../assets/js/Curso/eliminarCurso.js"></script>
    <script src="../../assets/js/Curso/agregarCurso.js"></script>
    <script src="../../assets/js/registrarParticipante.js"></script>
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
            const params = new URLSearchParams(window.location.search);
            const termino = params.get('buscar');
            if (!termino) return;
            let intentos = 0;
            const iv = setInterval(() => {
                const input = document.getElementById('searchInput');
                const filas = document.querySelectorAll('#tabla-cursos tbody tr');
                if (input && filas.length > 0) {
                    clearInterval(iv);
                    input.value = termino;
                    if (typeof buscarCurso === 'function') buscarCurso();
                    else input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    input.focus();
                    const url = new URL(window.location);
                    url.searchParams.delete('buscar');
                    history.replaceState({}, '', url);
                }
                if (++intentos >= 40) clearInterval(iv);
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


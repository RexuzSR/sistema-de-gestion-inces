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
$query_estados = "SELECT id_estatusCurso, nombre_estatusCurso FROM tb_estatuscurso";
$result_estados = $conn->query($query_estados);

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
    <link rel="stylesheet" href="../../assets/css/curso.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
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
        <aside id="sidebar" class="expand">
            <div class="d-flex">
                <!-- Btn Menu -->
                <button id="toggle-btn" type="button" class="btn btn-dark d-flex align-items-center justify-content-center p-2 me-2 mt-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                </button>

                <!-- Logo Menu-->
                <div class="sidebar-logo">
                    <img src="../../assets/img/inces-logo.png" alt="inces-logo">
                </div>
            </div>

            <!-- Links Menu -->
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="inicio.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="Inicio" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                            <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                        </svg>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="participante.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="Participantes" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                        <span>Participantes</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="cursos.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="cursos" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16">
                            <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                        <span>Cursos</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="docente.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="Docentes" width="16" height="16" fill="currentColor" class="bi bi-person-workspace" viewBox="0 0 16 16">
                            <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/>
                        </svg>
                        <span>Docentes</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="usuario.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="usuarios" width="16" height="16" fill="currentColor" class="bi bi-person-vcard-fill" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/>
                        </svg>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="sidebar-item dropdown">
                    <a href="#" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" title="programacion" width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                            <path d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5"/>
                        </svg>
                        <span>Programacion</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill flechita" viewBox="0 0 16 16">
                            <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                        </svg>
                    </a>
                    <ul class="sub-menu">
                    <li><a href="#" class="sidebar-link sub-menu-link">Curso a curso</a></li>
                    <li><a href="metas.php" class="sidebar-link sub-menu-link">Metas</a></li>
                </ul>
                </li>
                <li class="sidebar-item">
                    <a href="reporte.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill" viewBox="0 0 16 16">
                            <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                        </svg><span>Reportes</span></a>
                    </li>
                <li class="sidebar-item">
                    <a href="Historial.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                            <span>Historial</span>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- Main -->
        <div class="main">

            <!-- Header -->
            <?php $pageTitle = 'Cursos'; include __DIR__ . '/partials/header.php'; ?>

            <!-- Main -->
            <div>

                <div class="mt-3"> 

                   <div class="box container container-fluid pt-1" >
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

                    
                     <!-- Tabla de estudiantes -->
            <div class="table-responsive">
                <table id="tabla-cursos" class="table rounded-5 mt-3 table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Modalidad</th>
                            <th class="text-nowrap">Num Inscritos</th>
                            <th class="text-nowrap">Cupos Disponibles</th>
                            <th>Turno</th>
                            <th class="text-nowrap">Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th class="px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!-- Se generarn dinamicamente los registros através del archivo tabla-participante.js -->
                    </tbody>
                </table>
            </div>

                                <!-- Paginación -->

<div class="d-flex justify-content-between align-items-center">

<div class="registros-container d-flex align-items-center gap-2">
    <label class="label">Mostrar</label>
    <select class="form-select form-select form-select-sm" id="registros">
    <option value="1">1</option>
      <option value="5">5</option>
      <option value="8" selected>8</option>
      <option value="10">10</option>
      <option value="15">15</option>
      <option value="Todos">Todos</option>
    </select>
    <span class="label">Registros</span>
  </div>

<nav aria-label="Page navigation" class="mt-1">
    <ul class="pagination pagination-sm" id="pagination">
      <!-- Generado dinámicamente -->
    </ul>
  </nav>

</div>
                  <!-- Borré este tramo que también era estático para la paginación -->

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
    <script src="../../assets/js/Curso/tabla-curso.js"></script>
    <script src="../../assets/js/Curso/verCurso.js"></script>
    <script src="../../assets/js/Curso/editarCurso.js"></script>
    <script src="../../assets/js/Curso/AsignarParticipante.js"></script>
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
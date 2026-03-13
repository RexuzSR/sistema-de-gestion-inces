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
    <link rel="stylesheet" href="../../assets/css/crud.css">
    <link rel="stylesheet" href="../../assets/css/docente.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <title>Control - Docentes</title>
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
                </ul>
        </aside>
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
                        <a href="#" id="btn-addDocente" class="btn-add d-flex justify-content-center align-items-center gap-3 text-white" data-bs-toggle="modal" data-bs-target="#modal-addDocente" style="width: 19.5%">
                            Agregar Docentes
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                        </a>
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="registros-container d-flex align-items-center gap-2">
                            <label class="label">Mostrar</label>
                            <select class="form-select form-select form-select-sm" id="registros">
                                <option value="5">5</option>
                                <option value="8" selected>8</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="0">Todos</option>
                            </select>
                            <span class="label">Registros</span>
                        </div>

                        <nav aria-label="Page navigation" class="mt-1">
                            <ul class="pagination pagination-sm" id="pagination">
                                <!-- La paginación se generará dinámicamente -->
                            </ul>
                        </nav>
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

    <!-- Scripts -->
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/toastr/toastr.min.js"></script>
    <script src="../../assets/js/toastr/toastr-config.js"></script>
    <script src="../../assets/js/home.js"></script>
    <script src="../../assets/js/Docente/tabla-unificada-docentes.js"></script>
    <script src="../../assets/js/Docente/agregarDocente.js"></script>
    <script src="../../assets/js/Docente/editarDocente.js"></script>
    <script src="../../assets/js/Docente/asignarCursoDocente.js"></script>
    <script src="../../assets/js/Docente/viewDocente.js"></script>
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

</body>
</html>
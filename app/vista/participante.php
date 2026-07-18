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

// Definir la consulta SQL (devolvemos nombre y apellido concatenados para compatibilidad)
$sql = "SELECT id_participante, CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, edad, correo, DATE_FORMAT(fecha_registro, '%d/%m/%Y') as fecha_registro, grado_institucion, genero, estado FROM tb_participante";

// Ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->execute();

// Obtener los resultados
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <title>Control</title>
</head>
<body>

    <div class="wrapper">
        <!-- SideBar -->
        <?php $activePage = 'participante'; include __DIR__ . '/partials/sidebar.php'; ?>
        <!-- Main -->
        <div class="main">

            <!-- Header -->
            <?php $pageTitle = 'Participantes'; include __DIR__ . '/partials/header.php'; ?>

            <!-- Main -->
            <div class="contenedor">
                    
            <div class="box d-flex justify-content-center flex-column shadow-lg px-3 mt-4">
              
              <!-- Barra de búsqueda -->
              <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="input-group search-bar" style="max-width: 515px; min-width: 295px;">
                    <input 
                        type="text" 
                        id="searchInput"
                        name="buscar"
                        class="form-control form-control-sm" 
                        autocomplete="off"
                        placeholder="Buscar por nombre, apellido, cédula o correo...">
                    <span class="input-group-text">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                      </svg>
                    </span>
                </div>
            </div>

            <div class="filters-container d-flex justify-content-between  pt-2">

                <!-- Filtros -->
                <form class="d-flex align-items-center gap-2">

<select class="form-select form-select-sm" name="sexo" id="sexo-filter">
    <option value="">Todos</option>
    <?php
    $generos = ['Masculino', 'Femenino'];
    $selectedSexo = $_GET['sexo'] ?? '';
    
    foreach ($generos as $genero) {
        $safeGenero = htmlspecialchars($genero, ENT_QUOTES, 'UTF-8');
        $selected = ($selectedSexo === $genero) ? 'selected' : '';
        echo "<option value='$safeGenero' $selected>$safeGenero</option>";
    }
    ?>
</select>
                
                    <!-- Filtro Edad -->
    <label class="label" for="edad">Edad:</label>
    <select class="form-select form-select-sm" name="edad" id="edad-filter">
        <option value="">Todos</option>
        <?php
        $rangosEdad = [
            '14-18' => '14-18 años',
            '19-25' => '19-25 años',
            '26-35' => '26-35 años',
            '36-45' => '36-45 años',
            '46-55' => '46-55 años',
            '56-65' => '56-65 años',
            '66-75' => '66-75 años'
        ];
        
        $selectedEdad = $_GET['edad'] ?? '';
        
        foreach ($rangosEdad as $valor => $texto) {
            $safeValor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $safeTexto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
            $selected = ($selectedEdad === $valor) ? 'selected' : '';
            echo "<option value='$safeValor' $selected>$safeTexto</option>";
        }
        ?>
    </select>

                    <label class="label" for="estado">Estado:</label>
                        <select class="form-select form-select-sm" name="estado" id="estado-filter">
                            <option value="">Todos</option>
                            <?php
                                // Use the ENUM values directly
                                $estados = ['En formación', 'Disponible', 'En sistema', 'Asignado', 'Diferido'];
                                foreach ($estados as $estado) {
                                    echo "<option value='" . $estado . "'>" . $estado . "</option>";
                                }
                                ?>
                        </select>

                    <!-- botón de Filtros -->
                    <button id="btn-filter" class="filter" title="Limpiar Filtros" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-filter" viewBox="0 0 16 16">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    </button>

                </form>

                

                <!-- Botón para agregar participantes-->
                <?php if (($_SESSION['rol'] ?? '') !== 'Apoyo'): ?>
                <a id="btn-addParticipante" class="btn-add d-flex justify-content-center align-items-center gap-3 text-white" data-bs-toggle="modal" data-bs-target="#modal-addParticipante">Agregar Participantes<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg>
                </a>
                <?php endif; ?>
                
            </div>

            <!-- Tabla de estudiantes -->
            <div class="table-responsive">
                <table id="tabla-participantes" class="table rounded-5 mt-4  table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Sexo</th>
                            <th>Edad</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
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





<!-- El Modal -->
<div class="modal fade" id="modal-AsignarCursoParticipante" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      
            <div class="modal-header text-white" style="background-color: #020d19;" data-bs-theme="dark">
                <h5 class="modal-title" id="labelModalAsignarCurso">
                    Gestión de Cursos
                </h5>
                <button type="button" class="btn-close btn-close-white" style="filter: invert(1);" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold  mb-3" style="color: #020d19;">
                                Datos del Participante
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><b>Nombre:</b> <span id="asignar-nombre" class="text-muted">-</span></p>
                                    <p class="mb-1"><b>Cédula:</b> <span id="asignar-cedula" class="text-muted">-</span></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-1"><b>Apellido:</b> <span id="asignar-apellido" class="text-muted">-</span></p>
                                    <p class="mb-1"><b>Edad:</b> <span id="asignar-edad" class="text-muted">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3" style="color: #020d19;">
                                Cursos en Progreso
                            </h5>
                            <div id="cursos-pendientes" class="list-group"></div>
                        </div>
                    </div>

                    <form id="formAsignarCurso" class="needs-validation" novalidate>
                    <!-- Campo oculto para ID del participante -->
                    <input type="hidden" name="id_participante" id="id_participante">

                    <div class="row">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3" style="color: #020d19;">
                                Asignar Nuevo Curso
                            </h5>
                            <div class="mb-3">
                                <label for="asignarCurso" class="form-label">Seleccione un curso:</label>
                                <select class="form-select" id="asignarCurso" name="curso" required>
                                    <option value="" disabled selected>Cargando cursos disponibles...</option>
                                </select>
                                <div class="invalid-feedback">Por favor seleccione un curso</div>
                            </div>
                            <div class="alert alert-warning mt-3" role="alert">
                                El participante puede tener múltiples cursos activos
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="cancel-modal" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-success" style="height: 35px;">
                            Guardar Asignación
                        </button>
                    </div>
                </form>
            </div>

    </div>
  </div>
</div>

    <!-- Incluir Modales -->
    <?php include '../modales/modal-addParticipante.php'; ?>
    <?php include '../modales/modal-deleteParticipante.php'; ?>
    <?php include '../modales/modal-deleteCurso.php'; ?>
    <?php include '../modales/modal-editParticipante.php'; ?>
    <?php include '../modales/modal-viewParticipante.php'; ?>
    <?php include '../modales/Curso/modal-viewCurso.php'; ?>
    <?php include '../modales/Curso/modal-addCurso.php'; ?>




    


    
    <!-- archivos Js -->
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/toastr/toastr.min.js"></script>
    <script src="../../assets/js/toastr/toastr-config.js"></script>
    <script src="../../assets/js/home.js"></script>
    <script src="../../assets/js/tabla-participantes.js"></script>
    <script src="../../assets/js/asignarCurso.js"></script>
    <script src="../../assets/js/viewParticipante.js"></script>
    <script src="../../assets/js/historialParticipante.js"></script>
    <script src="../../assets/js/Curso/estadoCurso.js"></script>
    <script src="../../assets/js/Curso/verCurso.js"></script>

    <script src="../../assets/js/editarParticipante.js"></script>
    <script src="../../assets/js/eliminarParticipante.js"></script>
    <script src="../../assets/js/registrarParticipante.js"></script>
    <script src="../../assets/js/Curso/agregarCurso.js"></script>
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

    /* Espera a que la tabla esté renderizada por tabla-participantes.js */
    const MAX_INTENTOS = 40;   // 40 × 100 ms = 4 s máximo
    let intentos = 0;

    const intervalo = setInterval(function () {
        intentos++;
        const input = document.getElementById('searchInput');
        const filas = document.querySelectorAll('#tabla-participantes tbody tr');

        if (input && filas.length > 0) {
            clearInterval(intervalo);

            /* Llenar el input y disparar el evento que usa buscar.js */
            input.value = termino;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('keyup', { bubbles: true }));

            /* Hacer scroll suave al input de búsqueda */
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            input.focus();

            /* Limpiar el parámetro de la URL sin recargar la página */
            const url = new URL(window.location);
            url.searchParams.delete('buscar');
            history.replaceState({}, '', url);
        }

        if (intentos >= MAX_INTENTOS) clearInterval(intervalo);
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

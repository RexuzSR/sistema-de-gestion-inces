<?php
// ==================== SEGURIDAD DE SESIÓN ====================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php'); exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('../modelo/conexion.php');
$conexion = new Conexion();
$conn = $conexion->getConexion();
$conn->exec("SET NAMES 'utf8mb4'");

// === DATOS DE USUARIO ===
$nombre = htmlspecialchars($_SESSION['nombre'] ?? '');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol = htmlspecialchars($_SESSION['rol'] ?? '');
$imagenUsuario = $_SESSION['imagen'] ?? '';
$imagenBase = '../../assets/img/avatar/default-user.png';
$imagen = !empty($imagenUsuario) ? (strpos($imagenUsuario, 'assets/') === 0 ? '../../' . $imagenUsuario : $imagenUsuario) : $imagenBase;

$pageTitle = 'Historial';
$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/home.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <title>Historial — INCES</title>
    <link rel="stylesheet" href="../../assets/css/historial.css">
</head>
<body>
<div class="wrapper">

<!-- SideBar -->
        <?php $activePage = 'historial'; include __DIR__ . '/partials/sidebar.php'; ?>

<div class="main">
    <?php include 'partials/header.php'; ?>
    
    <div class="historial-container px-3 pb-5">
        <!-- Header -->
        <div class="historial-header">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem; vertical-align: middle;">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                Historial de Actividades
            </h1>
            <p>Registro completo de todas las acciones realizadas en el sistema</p>
        </div>

        <!-- Estadísticas -->
        <div class="estadisticas-section" id="estadisticas-container">
            <!-- Se cargará dinámicamente -->
        </div>

        <!-- Filtros -->
        <div class="filtros-section">
            <h5>Filtros de Búsqueda</h5>
            <div class="filtro-group">
                <div class="filtro-item">
                    <label for="filtro-tipo">Tipo de Actividad</label>
                    <select id="filtro-tipo">
                        <option value="">Todas las actividades</option>
                    </select>
                </div>
                <div class="filtro-item">
                    <label for="filtro-fecha-desde">Desde</label>
                    <input type="date" id="filtro-fecha-desde">
                </div>
                <div class="filtro-item">
                    <label for="filtro-fecha-hasta">Hasta</label>
                    <input type="date" id="filtro-fecha-hasta">
                </div>
            </div>
            <div class="filtro-buttons">
                <button class="btn-filtrar" id="btn-filtrar">Aplicar Filtros</button>
                <button class="btn-limpiar" id="btn-limpiar">Limpiar Filtros</button>
            </div>
        </div>

        <!-- Tabla de Historial -->
        <div class="tabla-historial">
            <div id="contenedor-tabla">
                <div class="cargando">Cargando historial...</div>
            </div>
        </div>

        <!-- Paginación -->
        <div id="paginacion-container"></div>
        <div class="registros-historial-container">
            <div class="registros-selector">
                <label for="historial-registros">Mostrar</label>
                <select id="historial-registros">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>registros por página</span>
            </div>
        </div>
    </div>
</div>

</div>

<script src="../../assets/js/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/toastr/toastr.min.js"></script>
<script src="../../assets/js/toastr/toastr-config.js"></script>
<script src="../../assets/js/home.js"></script>
<script src="../../assets/js/notifications.js"></script>

<script>
    const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";
    if (userRole === "Apoyo") {
        const usuarioItem = document.getElementById('sidebar-usuario');
        if (usuarioItem) usuarioItem.remove();
    }
</script>

<script>
let itemsPorPagina = 10;
let paginaActual = 1;
let totalRegistros = 0;

// Mapeo de tipos de actividad a colores
const tiposActividad = {
    'Crear': 'badge-add',
    'Editar': 'badge-edit',
    'Eliminar': 'badge-delete',
    'Login': 'badge-login',
    'Ver': 'badge-view'
};

// Cargar estadísticas
function cargarEstadisticas() {
    $.ajax({
        url: '../controlador/Historial/obtenerEstadisticas.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarEstadisticas(response.estadisticas, response.actividades_recientes);
                cargarTiposActividad(response.tipos_actividad);
            }
        },
        error: function() {
            console.error('Error al cargar estadísticas');
        }
    });
}

function mostrarEstadisticas(estadisticas, actividadesRecientes) {
    const container = $('#estadisticas-container');
    container.empty();

    // Contar por tipo
    const porTipo = {};
    estadisticas.forEach(stat => {
        if (!porTipo[stat.tipo_actividad]) {
            porTipo[stat.tipo_actividad] = 0;
        }
        porTipo[stat.tipo_actividad] += stat.total;
    });

    // Mostrar tarjetas
    let html = '';
    
    html += `
        <div class="stat-card">
            <h6>Total de Actividades</h6>
            <div class="valor">${estadisticas.reduce((sum, s) => sum + s.total, 0)}</div>
            <div class="subtexto">Registros en el sistema</div>
        </div>
    `;

    Object.entries(porTipo).forEach(([tipo, total]) => {
        html += `
            <div class="stat-card">
                <h6>${tipo}</h6>
                <div class="valor">${total}</div>
                <div class="subtexto">Actividades registradas</div>
            </div>
        `;
    });

    container.html(html);
}

function cargarTiposActividad(tipos) {
    const select = $('#filtro-tipo');
    tipos.forEach(tipo => {
        select.append(`<option value="${tipo}">${tipo}</option>`);
    });
}

// Cargar historial
function cargarHistorial(pagina = 1) {
    const offset = (pagina - 1) * itemsPorPagina;
    const filtro = $('#filtro-tipo').val();
    const fechaDesde = $('#filtro-fecha-desde').val();
    const fechaHasta = $('#filtro-fecha-hasta').val();

    $('#contenedor-tabla').html('<div class="cargando">Cargando historial...</div>');

    $.ajax({
        url: '../controlador/Historial/obtenerHistorial.php',
        type: 'GET',
        dataType: 'json',
        data: {
            limite: itemsPorPagina,
            offset: offset,
            filtro: filtro,
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        },
        success: function(response) {
            if (response.success) {
                totalRegistros = response.total;
                mostrarHistorial(response.data);
                mostrarPaginacion(pagina, Math.ceil(totalRegistros / itemsPorPagina));
            } else {
                mostrarSinDatos();
            }
        },
        error: function() {
            toastr.error('Error al cargar el historial');
        }
    });
}

function mostrarHistorial(datos) {
    const container = $('#contenedor-tabla');
    
    if (datos.length === 0) {
        mostrarSinDatos();
        return;
    }

    let html = `
        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Tipo</th>
                    <th>Descripción</th>

                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
    `;

    datos.forEach(registro => {
        const fecha = new Date(registro.fecha_hora);
        const fechaFormato = fecha.toLocaleDateString('es-ES') + ' ' + fecha.toLocaleTimeString('es-ES');
        const tipoClase = tiposActividad[registro.tipo_actividad] || 'badge-view';
        const iniciales = (registro.nombre || 'U').charAt(0).toUpperCase();

        html += `
            <tr>
                <td class="fecha-hora">${fechaFormato}</td>
                <td><span class="badge-tipo ${tipoClase}">${registro.tipo_actividad}</span></td>
                <td>${registro.descripcion || '—'}</td>

                <td>
                    <div class="usuario-info">
                        <div class="usuario-avatar">${iniciales}</div>
                        <span>${registro.nombre || 'Sistema'}</span>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.html(html);
}

function mostrarSinDatos() {
    const container = $('#contenedor-tabla');
    container.html(`
        <div class="sin-datos">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg>
            <p>No se encontraron registros en el historial</p>
        </div>
    `);
}

function mostrarPaginacion(paginaActual, totalPaginas) {
    const container = $('#paginacion-container');
    
    if (totalPaginas <= 1) {
        container.html('');
        return;
    }

    let html = `<div class="info-paginacion">Mostrando ${(paginaActual - 1) * itemsPorPagina + 1} a ${Math.min(paginaActual * itemsPorPagina, totalRegistros)} de ${totalRegistros} registros</div>`;
    html += '<div class="paginacion">';

    // Botón anterior
    if (paginaActual > 1) {
        html += `<button onclick="irAPagina(${paginaActual - 1})">← Anterior</button>`;
    } else {
        html += `<button disabled>← Anterior</button>`;
    }

    // Números de página
    const inicio = Math.max(1, paginaActual - 2);
    const fin = Math.min(totalPaginas, paginaActual + 2);

    if (inicio > 1) {
        html += `<button onclick="irAPagina(1)">1</button>`;
        if (inicio > 2) html += `<span>...</span>`;
    }

    for (let i = inicio; i <= fin; i++) {
        if (i === paginaActual) {
            html += `<button class="activa">${i}</button>`;
        } else {
            html += `<button onclick="irAPagina(${i})">${i}</button>`;
        }
    }

    if (fin < totalPaginas) {
        if (fin < totalPaginas - 1) html += `<span>...</span>`;
        html += `<button onclick="irAPagina(${totalPaginas})">${totalPaginas}</button>`;
    }

    // Botón siguiente
    if (paginaActual < totalPaginas) {
        html += `<button onclick="irAPagina(${paginaActual + 1})">Siguiente →</button>`;
    } else {
        html += `<button disabled>Siguiente →</button>`;
    }

    html += '</div>';
    container.html(html);
}

function irAPagina(pagina) {
    paginaActual = pagina;
    cargarHistorial(pagina);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Event listeners
$(document).ready(function() {
    cargarEstadisticas();
    cargarHistorial(1);

    $('#btn-filtrar').click(function() {
        paginaActual = 1;
        cargarHistorial(1);
    });

    $('#btn-limpiar').click(function() {
        $('#filtro-tipo').val('');
        $('#filtro-fecha-desde').val('');
        $('#filtro-fecha-hasta').val('');
        paginaActual = 1;
        cargarHistorial(1);
    });

    // Cambiar registros por página
    $('#historial-registros').on('change', function() {
        itemsPorPagina = parseInt($(this).val());
        paginaActual = 1;
        cargarHistorial(1);
    });

    // Permitir Enter en los filtros
    $('#filtro-fecha-desde, #filtro-fecha-hasta').keypress(function(e) {
        if (e.which === 13) {
            $('#btn-filtrar').click();
        }
    });
});
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



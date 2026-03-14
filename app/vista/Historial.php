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

<aside id="sidebar" class="expand">
    <div class="d-flex">
        <button id="toggle-btn" type="button" class="btn btn-dark d-flex align-items-center justify-content-center p-2 me-2 mt-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>
        </button>
        <div class="sidebar-logo"><img src="../../assets/img/inces-logo.png" alt="inces-logo"></div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item"><a href="Inicio.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg><span>Inicio</span></a></li>
        <li class="sidebar-item"><a href="participante.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg><span>Participantes</span></a></li>
        <li class="sidebar-item"><a href="cursos.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg><span>Cursos</span></a></li>
        <li class="sidebar-item"><a href="docente.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-workspace" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg><span>Docentes</span></a></li>
        <li id="sidebar-usuario" class="sidebar-item"><a href="usuario.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-vcard-fill" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg><span>Usuarios</span></a></li>
        <li class="sidebar-item dropdown">
            <a href="#" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16"><path d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5"/></svg><span>Programacion</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill flechita" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg></a>
            <ul class="sub-menu"><li><a href="#" class="sidebar-link sub-menu-link">Curso a curso</a></li><li><a href="metas.php" class="sidebar-link sub-menu-link">Metas</a></li></ul>
        </li>
        <li class="sidebar-item"><a href="reporte.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill" viewBox="0 0 16 16"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg><span>Reportes</span></a></li>
        <li class="sidebar-item">
            <a href="Historial.php" class="sidebar-link active-link">
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
    </div>
</div>

</div>

<script src="../../assets/js/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/toastr/toastr.min.js"></script>
<script src="../../assets/js/toastr/toastr-config.js"></script>

<script>
const ITEMS_POR_PAGINA = 50;
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
    const offset = (pagina - 1) * ITEMS_POR_PAGINA;
    const filtro = $('#filtro-tipo').val();
    const fechaDesde = $('#filtro-fecha-desde').val();
    const fechaHasta = $('#filtro-fecha-hasta').val();

    $.ajax({
        url: '../controlador/Historial/obtenerHistorial.php',
        type: 'GET',
        dataType: 'json',
        data: {
            limite: ITEMS_POR_PAGINA,
            offset: offset,
            filtro: filtro,
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        },
        success: function(response) {
            if (response.success) {
                totalRegistros = response.total;
                mostrarHistorial(response.data);
                mostrarPaginacion(pagina, Math.ceil(totalRegistros / ITEMS_POR_PAGINA));
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
                    <th>Tabla</th>
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
                <td><small>${registro.tabla_afectada || '—'}</small></td>
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

    let html = `<div class="info-paginacion">Mostrando ${(paginaActual - 1) * ITEMS_POR_PAGINA + 1} a ${Math.min(paginaActual * ITEMS_POR_PAGINA, totalRegistros)} de ${totalRegistros} registros</div>`;
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

    // Permitir Enter en los filtros
    $('#filtro-fecha-desde, #filtro-fecha-hasta').keypress(function(e) {
        if (e.which === 13) {
            $('#btn-filtrar').click();
        }
    });
});
</script>
</body>
</html>

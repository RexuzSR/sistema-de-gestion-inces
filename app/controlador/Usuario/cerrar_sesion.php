<?php
// app/controlador/Usuario/cerrar_sesion.php

// Configuración de sesión directa
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

// Registrar cierre de sesión en el historial
if (isset($_SESSION['id_usuario']) && isset($_SESSION['nombre_usuario'])) {
    require_once '../../modelo/HistorialUtil.php';
    if (isset($_GET['inactividad']) && $_GET['inactividad'] == '1') {
        HistorialUtil::registrar(
            'Logout',
            "Usuario " . $_SESSION['nombre_usuario'] . " cerró sesión por inactividad",
            'tb_usuario',
            $_SESSION['id_usuario'],
            $_SESSION['id_usuario']
        );
    } else {
        HistorialUtil::registrarLogout($_SESSION['id_usuario'], $_SESSION['nombre_usuario']);
    }
}

// SOLO AQUÍ se destruye la sesión completamente
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirigir al login
header('Location: ../../../app/vista/index.php?logout=1');
exit();
?>
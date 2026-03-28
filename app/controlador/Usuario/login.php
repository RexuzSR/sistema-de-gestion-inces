<?php
// Configuración de sesión directa
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

session_name('SISTEMA_CONTROL_INCES');
session_start();
require_once 'managerUsuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir y limpiar los datos
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contraseña = $_POST['contraseña'];
    
    // Validar campos vacíos
    if (empty($nombre_usuario) || empty($contraseña)) {
        $_SESSION['error_login'] = "Todos los campos son obligatorios";
        header('Location: ../../../app/vista/index.php');
        exit;
    }

    // Crear instancia y autenticar
    $usuario = new Usuario();
    $resultado = $usuario->iniciarSesion($nombre_usuario, $contraseña);

    // Manejar el resultado
    if ($resultado === true) {
        // Registrar inicio de sesión en el historial
        require_once '../../modelo/HistorialUtil.php';
        HistorialUtil::registrarLogin($_SESSION['id_usuario'], $_SESSION['nombre_usuario']);

        // Redirigir al dashboard si es exitoso
        header('Location: ../../../app/vista/Inicio.php');
        exit;
    } else {
        // Mostrar error si falla
        $_SESSION['error_login'] = $resultado;
        header('Location: ../../../app/vista/index.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al login
    header('Location: ../../../app/vista/index.php');
    exit;
}
?>
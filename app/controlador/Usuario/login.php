<?php
// Configuración de sesión directa
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

session_name('SISTEMA_CONTROL_INCES');
session_start();
require_once 'managerUsuario.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir y limpiar los datos
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';
    
    // Validar campos vacíos
    if (empty($nombre_usuario) || empty($contraseña)) {
        $errorMsg = "Todos los campos son obligatorios";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMsg]);
            exit;
        } else {
            $_SESSION['error_login'] = $errorMsg;
            header('Location: ../../../app/vista/index.php');
            exit;
        }
    }

    // Crear instancia y autenticar
    $usuario = new Usuario();
    $resultado = $usuario->iniciarSesion($nombre_usuario, $contraseña);

    // Manejar el resultado
    
    if ($resultado === true) {
        // Registrar inicio de sesión en el historial
        require_once '../../modelo/HistorialUtil.php';
        HistorialUtil::registrarLogin($_SESSION['id_usuario'], $_SESSION['nombre_usuario']);
        $_SESSION['ultimo_acceso'] = time();

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => 'Inicio.php']);
            exit;
        } else {
            // Redirigir al dashboard si es exitoso (para POST sin JS)
            header('Location: ../../../app/vista/Inicio.php');
            exit;
        }
    } else {
        // Mostrar error si falla
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $resultado]);
            exit;
        } else {
            $_SESSION['error_login'] = $resultado;
            header('Location: ../../../app/vista/index.php');
            exit;
        }
    }
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Solicitud no válida']);
        exit;
    } else {
        // Si no es POST, redirigir al login
        header('Location: ../../../app/vista/index.php');
        exit;
    }
}
?>
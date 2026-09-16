<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once '../../modelo/conexion.php';
require_once '../../modelo/HistorialUtil.php';
require_once 'managerUsuario.php';
require_once 'master_password.php';

header('Content-Type: application/json');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "❌ Error: No se recibieron datos en el servidor"]);
    exit;
}

// ==================== PROTECCIÓN: CONTRASEÑA MAESTRA ====================
if (!validarAccesoMaestro($_POST['master_password'] ?? null)) {
    exit;
}
// ========================================================================

// Combinar datos de POST y FILES
$data = array_merge($_POST, $_FILES);

try {
    $usuario = new Usuario();
    
    // Desbloquear TRIGGER MySQL en la misma conexión que usará el manager
    desbloquearTriggerMySQL($usuario->getConexion());

    $response = $usuario->registrarUsuario($data);
    
    if ($response['success'] && isset($_SESSION['id_usuario'])) {
        $nombreUsuario = $_POST['nombre_usuario'] ?? 'Nuevo Usuario';
        try {
            HistorialUtil::registrar('Crear', "Se creó el usuario: $nombreUsuario", 'tb_usuario', $response['id_usuario'] ?? 0, $_SESSION['id_usuario']);
        } catch (Exception $e) {
            error_log("Error al registrar historial de usuario: " . $e->getMessage());
        }
    }

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ Error del servidor: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ Error inesperado: ' . $e->getMessage()]);
}

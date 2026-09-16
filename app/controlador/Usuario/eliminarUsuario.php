<?php
// llamado al archivo managerUsuario.php 
require_once 'managerUsuario.php';
require_once '../../modelo/conexion.php';
require_once '../../modelo/HistorialUtil.php';
require_once 'master_password.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {

    // ==================== PROTECCIÓN: CONTRASEÑA MAESTRA ====================
    if (!validarAccesoMaestro($_POST['master_password'] ?? null)) {
        exit;
    }
    // ========================================================================

    $idParticipante = $_POST['id'];

    try {
        // VALIDACIÓN DE SEGURIDAD: Prevenir eliminación por parte del mismo usuario
        $currentLoggedId = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
        
        if (intval($idParticipante) === $currentLoggedId) {
            echo json_encode([
                "success" => false,
                "message" => "❌ No puedes eliminar tu propia cuenta de usuario."
            ]);
            exit;
        }

        // Instanciar la clase y llamar al método eliminar
        $usuarioMgr = new usuario();
        
        // Desbloquear TRIGGER MySQL en la misma conexión que usará el manager
        desbloquearTriggerMySQL($usuarioMgr->getConexion());
        
        // Obtener datos antes de borrar
        $userDataJson = $usuarioMgr->obtenerUsuarioPorId($idParticipante);
        $userData = json_decode($userDataJson, true);
        $nombreUsuario = $userData['nombre_usuario'] ?? "ID: $idParticipante";

        // VALIDACIÓN DE SEGURIDAD: Un Administrador no puede eliminar a otro Administrador
        $currentLoggedRol = $_SESSION['rol'] ?? '';
        if (isset($userData['rol']) && $userData['rol'] === 'Administrador' && $currentLoggedRol === 'Administrador') {
            echo json_encode([
                "success" => false,
                "message" => "❌ Un Administrador no tiene permisos para eliminar a otro Administrador."
            ]);
            exit;
        }

        $response = $usuarioMgr->eliminar($idParticipante);

        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            HistorialUtil::registrar('Eliminar', "Se eliminó el usuario: $nombreUsuario", 'tb_usuario', $idParticipante, $_SESSION['id_usuario']);
        }

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error interno: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID no recibido o método HTTP incorrecto"
    ]);
}
?>

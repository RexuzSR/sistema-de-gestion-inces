<?php
// llamado al archivo managerUsuario.php 
require_once 'managerUsuario.php';
require_once '../../modelo/HistorialUtil.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
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
        
        // Obtener datos antes de borrar
        $userDataJson = $usuarioMgr->obtenerUsuarioPorId($idParticipante);
        $userData = json_decode($userDataJson, true);
        $nombreUsuario = $userData['nombre_usuario'] ?? "ID: $idParticipante";

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

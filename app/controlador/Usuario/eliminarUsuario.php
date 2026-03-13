<?php
// llamado al archivo managerUsuario.php 
require_once 'managerUsuario.php';

session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) {
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
        $participante = new usuario();
        $response = $participante->eliminar($idParticipante);

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

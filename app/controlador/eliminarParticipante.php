<?php
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// llamado al archivo managerParticipante.php 
require_once 'managerParticipante.php';
require_once '../modelo/HistorialUtil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idParticipante = $_POST['id'];

    try {
        // Instanciar la clase y llamar al método eliminar
        $participante = new Participante();
        
        // Obtener datos antes de borrar
        $partData = $participante->obtenerParticipantePorId($idParticipante);
        $nombreParticipante = isset($partData['primer_nombre']) ? $partData['primer_nombre'] . " " . $partData['primer_apellido'] : "ID: $idParticipante";

        $response = $participante->eliminar($idParticipante);

        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            HistorialUtil::registrarEliminarParticipante($idParticipante, $nombreParticipante, $_SESSION['id_usuario']);
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

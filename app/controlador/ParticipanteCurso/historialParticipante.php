<?php
// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para formato JSON y CORS
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once '../../modelo/conexion.php';
require_once 'managerParticipanteCurso.php';

try {
    // Obtener ID por POST
    $idParticipante = filter_input(INPUT_POST, 'id_participante', FILTER_VALIDATE_INT);
    
    // Validar ID
    if (!$idParticipante || $idParticipante < 1) {
        throw new Exception('ID de participante inválido o no proporcionado');
    }

    // Obtener datos
    $participante = new ParticipanteCurso();
    $historial = $participante->historialParticipante($idParticipante);
    
    // Debug en archivo log
    error_log("Historial obtenido para ID $idParticipante: " . print_r($historial, true));
    
    // Respuesta JSON
    echo json_encode([
        'success' => true,
        'data' => $historial,
        'count' => count($historial)
    ]);
    
} catch (Exception $e) {
    // Registrar error
    error_log("Error: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'received_id' => $_POST['id_participante'] ?? 'No recibido'
    ]);
}
?>
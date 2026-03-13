<?php
require_once 'managerCurso.php';

header('Content-Type: application/json');

try {
    // Recibir y validar datos
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validaciones estrictas
    if ($data === null) {
        throw new Exception("Error: Datos JSON inválidos o vacíos");
    }
    
    if (!isset($data['id_participante_curso']) || !is_numeric($data['id_participante_curso'])) {
        throw new InvalidArgumentException("Error: ID de participante inválido");
    }

    $curso = new Curso();
    $resultado = $curso->eliminarParticipante($data['id_participante_curso']);
    
    echo json_encode([
        'success' => $resultado['success'] ?? false,
        'message' => $resultado['message'] ?? 'Error desconocido'
    ]);

} catch(InvalidArgumentException $e) {
    error_log($e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("Error general: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}
<?php
require_once 'managerCurso.php';
ob_start();

header('Content-Type: application/json');

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    // Recibir y validar datos
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception("Datos JSON no recibidos", 400);
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON inválido: " . json_last_error_msg(), 400);
    }

    // Validar campos requeridos
    $camposRequeridos = ['id_curso', 'id_participante'];
    foreach ($camposRequeridos as $campo) {
        if (!isset($data[$campo]) || empty($data[$campo])) {
            throw new Exception("Campo requerido faltante: $campo", 400);
        }
    }

    $curso = new Curso();
    $resultado = $curso->agregarParticipante(
        intval($data['id_participante']),
        intval($data['id_curso'])
    );

    ob_end_clean();
    echo json_encode([
        'success' => $resultado['success'] ?? false,
        'message' => $resultado['message'] ?? 'Error desconocido',
        'error' => $resultado['message'] ?? '' // Para compatibilidad con frontend
    ]);

} catch(Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
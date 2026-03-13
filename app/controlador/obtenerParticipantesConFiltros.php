<?php
// Desactivar errores en producción
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

require_once 'managerParticipante.php';

try {
    // Obtener y sanitizar parámetros
    $filtros = [
        'sexo' => filter_input(INPUT_GET, 'sexo', FILTER_SANITIZE_SPECIAL_CHARS),
        'edad' => filter_input(INPUT_GET, 'edad', FILTER_SANITIZE_SPECIAL_CHARS),
        'estado' => filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_SPECIAL_CHARS)
    ];

    $participante = new Participante();
    $resultado = $participante->filtrarParticipantes($filtros);
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'message' => filter_var($e->getMessage(), FILTER_SANITIZE_STRING)
    ]);
}

exit;
?>
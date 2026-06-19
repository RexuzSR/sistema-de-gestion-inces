<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye la conexión si más adelante quieres consultar la BD
include '../../modelo/conexion.php';

try {
    // Respuesta mínima para evitar 404 y mantener la UI sin errores
    $response = [
        'pendientes' => [],
        'por_empezar' => [],
        'en_curso' => []
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'pendientes' => [],
        'por_empezar' => [],
        'en_curso' => [],
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

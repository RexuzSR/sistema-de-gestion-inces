<?php
require_once '../../modelo/conexion.php';
require_once 'managerUsuario.php';

header('Content-Type: application/json');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "❌ Error: No se recibieron datos en el servidor"]);
    exit;
}

// Combinar datos de POST y FILES
$data = array_merge($_POST, $_FILES);

try {
    $usuario = new Usuario();
    $response = $usuario->registrarUsuario($data);
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ Error del servidor: ' . $e->getMessage()]);
}

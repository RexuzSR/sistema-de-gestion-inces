<?php
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../modelo/conexion.php';
require_once '../../modelo/HistorialUtil.php';
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
    
    if ($response['success'] && isset($_SESSION['id_usuario'])) {
        $nombreUsuario = $_POST['usuario'] ?? 'Nuevo Usuario';
        HistorialUtil::registrar('Crear', "Se creó el usuario: $nombreUsuario", 'tb_usuario', $response['id_usuario'], $_SESSION['id_usuario']);
    }

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ Error del servidor: ' . $e->getMessage()]);
}

<?php
require_once 'managerUsuario.php';

header('Content-Type: application/json');

try {
    // Validar parámetros
    if (!isset($_GET['nombre_usuario']) && !isset($_GET['correo'])) {
        throw new Exception('Se requiere nombre de usuario o correo');
    }

    $username = $_GET['nombre_usuario'] ?? null;
    $correo = $_GET['correo'] ?? null;

    $manager = new usuario();
    $resultado = $manager->verificarUsuario($username, $correo);
    
    echo json_encode([
        'existsUsername' => $resultado['existsUsername'],
        'existsCorreo' => $resultado['existsCorreo']
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
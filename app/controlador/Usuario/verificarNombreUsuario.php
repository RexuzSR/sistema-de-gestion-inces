<?php
require_once 'managerUsuario.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['nombre_usuario']) || !isset($_GET['id_usuario'])) {
        throw new Exception('Parámetros incompletos');
    }

    $verificador = new usuario();
    $existe = $verificador->verificarUsuarioEdit(
        trim($_GET['nombre_usuario']),
        $_GET['id_usuario']
    );
    
    echo json_encode(['existe' => $existe]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
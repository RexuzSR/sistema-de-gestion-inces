<?php
// Llamado al archivo que contiene la clase Usuario
require_once 'managerUsuario.php';

// Asegurar que el contenido de la respuesta sea JSON
header('Content-Type: application/json');

try {
    // Instanciar la clase Usuario
    $usuario = new Usuario();

    // Llamar al método para obtener los usuarios
    $resultado = $usuario->obtenerUsuarios();

    // Devolver la respuesta como JSON
    echo json_encode($resultado);

} catch (Exception $e) {
    // Manejo de errores general
    error_log("❌ Error en obtenerUsuarios.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los usuarios: ' . $e->getMessage()
    ]);
}
?>

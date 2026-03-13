<?php
// Llamado al archivo que contiene la clase Usuario
require_once 'managerUsuario.php';

// Asegurar que el contenido de la respuesta sea JSON
header('Content-Type: application/json');

// Verificar que el ID esté presente
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Instanciar la clase Usuario con la conexión
        $conexion = new Conexion();
        $usuario = new Usuario($conexion);

        // Llamar al método para obtener el usuario por ID
        $resultado = $usuario->obtenerUsuarioPorId($id);

        // Devolver la respuesta como JSON
        echo $resultado;

    } catch (Exception $e) {
        // Manejo de errores general
        error_log("❌ Error en obtenerUsuarioPorId.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener el usuario: ' . $e->getMessage()
        ]);
    }
} else {
    // Si no se proporciona un ID
    echo json_encode(['error' => 'ID no proporcionado']);
}
?>


<?php
// llamado al archivo managerParticipante.php 
require_once 'managerUsuario.php';

// Asegurar que el contenido de la respuesta sea JSON
header('Content-Type: application/json');

// Verificar si se pasa el ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Validar y sanitizar el ID

    // Crear una instancia de la clase Participante
    $participante = new usuario();
    $resultado = $participante->verUsuario($id);

    // Devolver la respuesta como JSON
    echo json_encode($resultado);
} else {
    // Si no se pasa un ID, devolver un mensaje de error
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
}
?>


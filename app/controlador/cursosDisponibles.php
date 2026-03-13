<?php
require_once '../modelo/conexion.php';
require_once 'managerParticipante.php';

header('Content-Type: application/json');

try {
    // Crear instancia de Participante (manejará su propia conexión)
    $participante = new Participante();
    $cursos = $participante->obtenerCursosDisponibles();
    
    echo json_encode([
        'success' => true,
        'data' => $cursos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
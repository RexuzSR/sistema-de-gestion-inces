<?php
// Incluir el manager correcto
require_once 'managerDocente.php';

header('Content-Type: application/json');

try {
    // Verificar que se reciba el ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de docente no válido'
        ]);
        exit;
    }
    
    $id_docente = (int)$_GET['id'];
    $docenteManager = new docente();
    
    // Obtener datos del docente usando el método verDocente
    $resultado = $docenteManager->verDocente($id_docente);
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log("Error en obtenerDocente.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
    exit;
}
?>
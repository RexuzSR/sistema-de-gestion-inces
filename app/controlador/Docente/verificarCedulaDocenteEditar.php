<?php
require_once 'managerDocente.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Este archivo solo debe verificar la cédula, NO editar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cedula = $_GET['cedula'] ?? '';
    $idDocente = $_GET['idDocente'] ?? 0;
    
    if (empty($cedula)) {
        echo json_encode(['existe' => false, 'message' => 'Cédula no proporcionada']);
        exit;
    }
    
    try {
        $docenteManager = new Docente();
        $existe = $docenteManager->verificarCedulaEditar($cedula, $idDocente);
        
        echo json_encode(['existe' => $existe]);
    } catch (Exception $e) {
        error_log("Error en verificarCedulaDocenteEditar.php: " . $e->getMessage());
        echo json_encode(['existe' => false, 'message' => 'Error en la verificación']);
    }
} else {
    echo json_encode(['existe' => false, 'message' => 'Método no permitido']);
}
exit;
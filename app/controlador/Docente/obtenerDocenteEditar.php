<?php
require_once 'managerDocente.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($id <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    try {
        $docenteManager = new Docente();
        $result = $docenteManager->obtenerDocentePorId($id);

        if ($result['success']) {
            echo json_encode($result['data']);
        } else {
            echo json_encode(['error' => $result['message']]);
        }
    } catch (Exception $e) {
        error_log("Error en obtenerDocenteEditar.php: " . $e->getMessage());
        echo json_encode(['error' => 'Error interno del servidor']);
    }
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}
exit;
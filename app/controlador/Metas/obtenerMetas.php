<?php
require_once '../Metas/managerMetas.php';

$metasManager = new Metas();

try {
    $metas = $metasManager->obtenerMetas();
    echo json_encode(['data' => $metas, 'total' => count($metas)]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error al obtener metas: ' . $e->getMessage()]);
}

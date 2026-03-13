<?php
require_once '../Metas/managerMetas.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

$metasManager = new Metas();
$meta = $metasManager->obtenerPorId($id);

if ($meta) {
    echo json_encode(['success' => true, 'data' => $meta]);
} else {
    echo json_encode(['success' => false, 'message' => 'Meta no encontrada']);
}

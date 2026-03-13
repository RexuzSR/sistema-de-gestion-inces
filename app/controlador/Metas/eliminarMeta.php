<?php
require_once '../Metas/managerMetas.php';

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

$metasManager = new Metas();
$result = $metasManager->eliminar($id);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Meta eliminada']);
} else {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error al eliminar meta']);
}

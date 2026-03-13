<?php
require_once '../Metas/managerMetas.php';

$data = $_POST;
if (empty($data['id_meta'])) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

$metasManager = new Metas();
$result = $metasManager->actualizar($data);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Meta actualizada']);
} else {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error al actualizar meta']);
}

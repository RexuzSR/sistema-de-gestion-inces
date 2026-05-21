<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/managerMetas.php';

$id = $_GET['id_meta'] ?? $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de meta requerido']);
    exit;
}

try {
    $metasManager = new Metas();
    $resultado = $metasManager->obtenerParticipantesPorMeta($id);

    if (!$resultado) {
        echo json_encode(['success' => false, 'message' => 'Meta no encontrada']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'meta' => $resultado['meta'],
        'participantes' => $resultado['participantes'],
        'total' => count($resultado['participantes']),
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener participantes']);
}

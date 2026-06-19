<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once '../Metas/managerMetas.php';
require_once '../../modelo/HistorialUtil.php';

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

$metasManager = new Metas();

// Obtener info para el historial antes de borrar
$metaData = $metasManager->obtenerPorId($id);
$tipoObj = $metaData['tipo_objetivo'] ?? 'Meta';
$metaVal = $metaData['meta_objetivo'] ?? '0';
$descripcion = "$tipoObj ($metaVal)";

$result = $metasManager->eliminar($id);

if ($result['success']) {
    if (isset($_SESSION['id_usuario'])) {
        HistorialUtil::registrarEliminarMeta($id, $descripcion, $_SESSION['id_usuario']);
    }
    echo json_encode(['success' => true, 'message' => 'Meta eliminada']);
} else {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error al eliminar meta']);
}

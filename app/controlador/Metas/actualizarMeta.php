<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once '../Metas/managerMetas.php';
require_once '../../modelo/HistorialUtil.php';

$data = $_POST;
if (empty($data['id_meta'])) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit;
}

$metasManager = new Metas();
$result = $metasManager->actualizar($data);

if ($result['success']) {
    if (isset($_SESSION['id_usuario'])) {
        $tipoObj = $data['tipo_objetivo'] ?? 'Meta';
        $metaVal = $data['meta_objetivo'] ?? '0';
        $descripcion = "$tipoObj ($metaVal)";
        HistorialUtil::registrarEditarMeta($data['id_meta'], $descripcion, $_SESSION['id_usuario']);
    }
    echo json_encode(['success' => true, 'message' => 'Meta actualizada']);
} else {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error al actualizar meta']);
}

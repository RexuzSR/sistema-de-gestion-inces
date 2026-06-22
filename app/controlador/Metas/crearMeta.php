<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once '../Metas/managerMetas.php';
require_once '../../modelo/HistorialUtil.php';

$metasManager = new Metas();

$data = $_POST;

// Validacióncita
if (!isset($data['meta_objetivo']) || $data['meta_objetivo'] === '') {
    echo json_encode(['success' => false, 'message' => 'meta_objetivo es requerido']);
    exit;
}

if (!is_numeric($data['meta_objetivo']) || intval($data['meta_objetivo']) < 0) {
    echo json_encode(['success' => false, 'message' => 'meta_objetivo debe ser un número entero >= 0']);
    exit;
}

if (empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
    echo json_encode(['success' => false, 'message' => 'Fechas inicio y fin son requeridas']);
    exit;
}

// Convertir a entero
$data['meta_objetivo'] = intval($data['meta_objetivo']);

// Validar ambito si se envía
if (isset($data['id_ambito']) && $data['id_ambito'] !== '') {
    if (!is_numeric($data['id_ambito']) || intval($data['id_ambito']) < 0) {
        echo json_encode(['success' => false, 'message' => 'id_ambito inválido']);
        exit;
    }
    $data['id_ambito'] = intval($data['id_ambito']);
} else {
    $data['id_ambito'] = null;
}

// Validar tipo de objetivo
$allowedTipos = ['Aprobados','Inscritos','Ingresados','Masculino','Femenino','aprobados','inscritos','ingresados','masculino','femenino'];
if (isset($data['tipo_objetivo']) && $data['tipo_objetivo'] !== '') {
    $tipo = $data['tipo_objetivo'];
    if (!in_array($tipo, $allowedTipos)) {
        echo json_encode(['success' => false, 'message' => 'tipo_objetivo inválido']);
        exit;
    }
    // Normalizar a mayúscula inicial que espera la BD
    $data['tipo_objetivo'] = ucfirst(strtolower($tipo));
} else {
    $data['tipo_objetivo'] = 'Aprobados';
}

$result = $metasManager->registrar($data);

if ($result['success']) {
    if (isset($_SESSION['id_usuario'])) {
        $tipoObj = $data['tipo_objetivo'] ?? 'Meta';
        $metaVal = $data['meta_objetivo'] ?? '0';
        $descripcion = "$tipoObj ($metaVal)";
        HistorialUtil::registrarCrearMeta($result['id'], $descripcion, $_SESSION['id_usuario']);
    }
    echo json_encode(['success' => true, 'message' => 'Meta creada', 'id' => $result['id']]);
} else {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error al crear meta']);
}

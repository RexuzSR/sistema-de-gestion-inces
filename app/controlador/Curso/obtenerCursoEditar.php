<?php
require_once 'managerCurso.php';

header('Content-Type: application/json');

try {
    $id = $_GET['id'] ?? $_GET['id_curso'] ?? null;
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $cursoManager = new Curso();
    $curso = $cursoManager->obtenerCursoPorId($id);

    if (isset($curso['error'])) {
        http_response_code(404);
        echo json_encode($curso);
        exit;
    }

    $campos_fecha = ['fecha_inicio', 'fecha_fin', 'fecha_limite_inicio', 'fecha_creacion', 'fecha_actualizacion'];
    foreach ($campos_fecha as $campo) {
        if (!empty($curso[$campo]) && $curso[$campo] !== '0000-00-00' && $curso[$campo] !== '0000-00-00 00:00:00') {
            $curso[$campo] = date('Y-m-d', strtotime($curso[$campo]));
        } else {
            $curso[$campo] = null;
        }
    }

    echo json_encode($curso);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
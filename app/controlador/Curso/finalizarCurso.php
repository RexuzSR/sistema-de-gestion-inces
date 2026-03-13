<?php
require_once 'managerCurso.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$idCurso = isset($data['id_curso']) ? (int)$data['id_curso'] : null;

if (!$idCurso) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$model = new Curso();

if ($model->finalizarCurso($idCurso)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
}

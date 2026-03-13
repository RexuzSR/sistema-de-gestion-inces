<?php
require_once 'managerCurso.php';
header('Content-Type: application/json');

$idCurso = filter_input(INPUT_GET, 'id_curso', FILTER_VALIDATE_INT);

if (!$idCurso) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}

$model = new Curso();
$curso = $model->obtenerCursoBasico($idCurso);

if ($curso) {
    echo json_encode($curso);
} else {
    echo json_encode(['error' => 'Curso no encontrado']);
}

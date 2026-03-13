<?php
require_once 'managerCurso.php';
header('Content-Type: application/json');

if (!isset($_GET['id_curso'])) {
    echo json_encode(['error' => 'Falta el parámetro id_curso']);
    exit;
}

$id_curso = filter_input(INPUT_GET, 'id_curso', FILTER_VALIDATE_INT);
if (!$id_curso) {
    echo json_encode(['error' => 'ID de curso inválido']);
    exit;
}

$model = new Curso();
$participantes  = $model->obtenerParticipantesPorCurso($id_curso);

// CRÍTICO: cast a (int) — PDO devuelve string, JS compara con ==
$estatusCursoId = (int) $model->obtenerIdEstatusCurso($id_curso);

echo json_encode([
    'participantes' => $participantes,
    'id_estatus'    => $estatusCursoId
]);
exit;
?>
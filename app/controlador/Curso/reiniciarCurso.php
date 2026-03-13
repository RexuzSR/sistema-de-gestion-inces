<?php
require_once 'managerCurso.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) $input = $_POST;

$idCurso              = $input['id_curso']              ?? null;
$nuevaFechaInicio     = $input['nueva_fecha_inicio']    ?? null;
$nuevaFechaFin        = $input['nueva_fecha_fin']       ?? null;
$modo                 = $input['modo']                  ?? 'diferido';
$mantenerParticipantes = (bool)($input['mantener_participantes'] ?? false);

if (!$idCurso || !$nuevaFechaInicio || !$nuevaFechaFin) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

if (!in_array($modo, ['diferido', 'culminado'])) {
    echo json_encode(['success' => false, 'message' => 'Modo inválido.']);
    exit;
}

$cursoManager = new Curso();
$resultado = $cursoManager->reiniciarCurso(
    $idCurso,
    $nuevaFechaInicio,
    $nuevaFechaFin,
    $modo,
    $mantenerParticipantes
);

echo json_encode($resultado);
?>
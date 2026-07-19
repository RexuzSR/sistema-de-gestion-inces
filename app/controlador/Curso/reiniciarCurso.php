<?php
require_once 'managerCurso.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) $input = $_POST;

$idCurso              = $input['id_curso']              ?? null;
$id_modalidad         = $input['id_modalidad']          ?? null;
$id_sectoreconomico   = $input['id_sectoreconomico']    ?? null;
$id_subtipo           = $input['id_subtipo']            ?? null;
$id_ambito            = $input['id_ambito']             ?? null;
$turno                = $input['turno']                ?? null;
$horas_por_clase      = $input['horas_por_clase']      ?? null;
$horas_totales        = $input['horas_totales']        ?? null;
$min_participantes    = $input['min_participantes']    ?? null;
$max_participantes    = $input['max_participantes']    ?? null;
$nuevaFechaInicio     = $input['nueva_fecha_inicio']    ?? null;
$nuevaFechaFin        = $input['nueva_fecha_fin']       ?? null;
$modo                 = $input['modo']                  ?? 'diferido';
$mantenerParticipantes = (bool)($input['mantener_participantes'] ?? false);
$preimpreso           = $input['preimpreso']            ?? null;
$participantesNotas   = $input['participantes_notas']   ?? null;

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
    $mantenerParticipantes,
    $id_modalidad,
    $id_sectoreconomico,
    $id_subtipo,
    $id_ambito,
    $turno,
    $horas_por_clase,
    $horas_totales,
    $min_participantes,
    $max_participantes,
    $preimpreso,
    $participantesNotas
);

echo json_encode($resultado);
?>
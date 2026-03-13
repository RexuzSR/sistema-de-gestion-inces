<?php
require_once 'managerCurso.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$idCurso = isset($data['id_curso']) ? (int)$data['id_curso'] : null;
$participantesNotas = isset($data['participantes_notas']) ? $data['participantes_notas'] : null;

if (!$idCurso || !$participantesNotas) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$model = new Curso();

try {
    $actualizado = true;
    
    foreach ($participantesNotas as $nota) {
        $idParticipanteCurso = isset($nota['id_participante_curso']) ? (int)$nota['id_participante_curso'] : null;
        $estado = isset($nota['estado']) ? $nota['estado'] : null;
        
        if (!$idParticipanteCurso || !$estado) {
            $actualizado = false;
            break;
        }
        
        // Actualizar estado del participante
        if (!$model->guardarNotas($idParticipanteCurso, $estado)) {
            $actualizado = false;
            break;
        }
    }

    if ($actualizado) {
        $model->cambiarEstadoCurso($idCurso, 5);
        echo json_encode(['success' => true, 'message' => 'Calificaciones guardadas correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudieron guardar todas las notas']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
?>
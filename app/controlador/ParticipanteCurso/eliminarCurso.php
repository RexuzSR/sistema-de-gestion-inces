<?php
require_once 'managerParticipanteCurso.php';
require_once '../../modelo/conexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    $id_participante_curso = filter_input(INPUT_POST, 'id_participante_curso', FILTER_VALIDATE_INT);
    $id_participante = filter_input(INPUT_POST, 'id_participante', FILTER_VALIDATE_INT);

    if (!$id_participante_curso || !$id_participante) {
        // En vez de lanzar un error con el mensaje de "Datos inválidos", simplemente retorna success: false
        echo json_encode(['success' => false]);
        exit;
    }

    $participanteCurso = new ParticipanteCurso();
    $resultado = $participanteCurso->eliminarCurso($id_participante_curso, $id_participante);

    echo json_encode($resultado);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;

<?php
require_once 'managerParticipanteCurso.php';
header('Content-Type: application/json');

if (isset($_GET['id_participante'])) {
    $id_participante = $_GET['id_participante'];

    $participanteCurso = new ParticipanteCurso();
    $cursos = $participanteCurso->obtenerCursosEnProgreso($id_participante);

    echo json_encode($cursos);
} else {

}
?>
<?php
require_once 'managerParticipanteCurso.php'; // Reemplaza con el nombre de tu archivo

if (isset($_GET['id_participante'])) {
    $id_participante = $_GET['id_participante'];

    $participante = new ParticipanteCurso();
    $datos = $participante->obtenerDatosParticipante($id_participante);

    if (isset($datos['error'])) {
       
    } else {
        echo json_encode($datos); // Envía los datos del participante como JSON
    }
} else {

}
?>
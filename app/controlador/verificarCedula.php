<?php
require_once 'managerParticipante.php';

if (isset($_GET['cedula'])) {
    $participante = new Participante();
    $cedula = $_GET['cedula'];

    echo json_encode(["exists" => $participante->verificarCedula($cedula)]);
}
?>

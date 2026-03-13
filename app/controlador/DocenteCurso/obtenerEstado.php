<?php
require_once 'managerDocenteCurso.php';
header('Content-Type: application/json');

if (isset($_GET['id_docente'])) {
    $gestor = new DocenteCurso();
    $estado = $gestor->obtenerEstadoDocente($_GET['id_docente']);

    if ($estado) {
        echo json_encode($estado);
    } else {
        echo json_encode(['error' => 'Docente no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID de docente no proporcionado']);
}

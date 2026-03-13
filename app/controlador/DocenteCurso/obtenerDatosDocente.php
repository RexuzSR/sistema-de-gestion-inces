<?php
require_once 'managerDocenteCurso.php';

$id = $_GET['id_docente'] ?? null;

if ($id) {
    $modelo = new DocenteCurso();
    echo json_encode($modelo->obtenerDatosDocente($id));
} else {
    echo json_encode([]);
}

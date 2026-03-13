<?php
require_once 'managerDocenteCurso.php';

$id_docente = $_GET['id_docente'] ?? null;

if ($id_docente) {
    $modelo = new DocenteCurso();
    $cursos = $modelo->obtenerCursosAsignados($id_docente);
    echo json_encode($cursos);
} else {
    echo json_encode([]);
}

<?php
require_once 'managerDocenteCurso.php';

$id_docente = $_GET['id_docente'] ?? null;

if ($id_docente) {
    $modelo = new DocenteCurso();
    $cursos = $modelo->obtenerCursosNoAsignados($id_docente);
    $data = [];

    foreach ($cursos as $curso) {
        $data[] = [
            'id_curso' => $curso['id_curso'],
            'display' => $curso['nombre_curso']
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode([]);
}

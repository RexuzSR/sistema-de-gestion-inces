<?php
require_once 'managerDocenteCurso.php';
header('Content-Type: application/json');

if (isset($_POST['id_docente_curso']) && !empty($_POST['id_docente_curso'])) {
    $gestor = new DocenteCurso();
    $id = intval($_POST['id_docente_curso']);
    $success = $gestor->eliminarCursoDocente($id);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el curso de la base de datos']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos o inválidos']);
}

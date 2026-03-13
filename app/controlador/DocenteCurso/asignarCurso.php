<?php
require_once 'managerDocenteCurso.php';
header('Content-Type: application/json');

if (isset($_POST['id_docente']) && isset($_POST['curso'])) {
    $gestor = new DocenteCurso();
    $success = $gestor->asignarCurso($_POST['id_docente'], $_POST['curso']);
    
    // Si la asignación fue exitosa, actualizar el estado del docente
    if ($success) {
        $gestor->actualizarEstadoDocente($_POST['id_docente'], 'Asignado');
    }

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}

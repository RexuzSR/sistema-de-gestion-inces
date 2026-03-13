<?php
// Reemplazamos la lógica manual por la clase Curso que ya tiene las validaciones
require_once '../Curso/managerCurso.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_docente = $_POST['id_docente'] ?? null;
        $id_curso = $_POST['id_curso'] ?? null;
        
        if (!$id_docente || !$id_curso) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
            exit;
        }
        
        // Instanciar la clase Curso y llamar al método de asignación (que incluye validaciones horarias)
        $cursoManager = new Curso();
        $response = $cursoManager->asignarDocenteCurso((int)$id_curso, (int)$id_docente);
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Error en asignarCursoDocente.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al asignar el curso: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
<?php
require_once __DIR__ . '/../Curso/managerCurso.php';

header('Content-Type: application/json');

try {
    $id_docente = $_GET['id_docente'] ?? null;
    
    if (!$id_docente) {
        echo json_encode(['error' => 'ID de docente no proporcionado']);
        exit;
    }

    $cursoManager = new Curso();
    $conn = $cursoManager->getConexion();
    
    // 1. Obtener cursos que están en espera o diferidos y que NO tienen docente asignado todavía
    $sql = "SELECT c.id_curso, c.nombre_curso, c.fecha_inicio, c.fecha_fin,
                   c.turno, c.horas_por_clase
            FROM tb_curso c
            WHERE c.id_estatusCurso IN (1, 2) -- En espera o Diferido
            AND NOT EXISTS (SELECT 1 FROM tb_docente_curso dc WHERE dc.id_curso = c.id_curso)
            ORDER BY c.fecha_inicio ASC, c.nombre_curso";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $cursosPotenciales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Filtrar los cursos según la disponibilidad del docente (choques horarias, etc)
    $cursosValidos = [];
    foreach ($cursosPotenciales as $curso) {
        $resultado = $cursoManager->comprobarDisponibilidadDocente($id_docente, $curso['id_curso']);
        if ($resultado['success']) {
            $cursosValidos[] = $curso;
        }
    }

    // Responder con la lista filtrada
    echo json_encode($cursosValidos);
    
} catch (Exception $e) {
    error_log("Error en obtenerCursosDisponibles.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener cursos: ' . $e->getMessage()
    ]);
}
?>
<?php
require_once '../../modelo/conexion.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id_docente']) || !is_numeric($_GET['id_docente'])) {
        echo json_encode([]);
        exit;
    }
    
    $id_docente = (int)$_GET['id_docente'];
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    $sql = "SELECT dc.id_docente_curso, c.nombre_curso, 
                   c.fecha_inicio, c.fecha_fin, 
                   dc.fecha_asignacion,
                   ec.nombre_estatusCurso as estado
            FROM tb_docente_curso dc
            INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
            LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
            WHERE dc.id_docente = :id_docente
            ORDER BY dc.fecha_asignacion DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_docente' => $id_docente]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($cursos);
    
} catch (Exception $e) {
    error_log("Error en obtenerCursosAsignados.php: " . $e->getMessage());
    echo json_encode([]);
}
?>
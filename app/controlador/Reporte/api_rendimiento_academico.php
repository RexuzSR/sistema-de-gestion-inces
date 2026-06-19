<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
require_once '../../modelo/conexion.php'; // Ajusta la ruta si es necesario

try {
    $db = new Conexion();
    $conn = $db->getConexion();
    
    // Obtener el valor del checkbox
    $data = json_decode(file_get_contents('php://input'), true);
    $incluirHistoricos = $data['incluirHistoricos'];

    // Ajustar la consulta según si se incluyen historicos o solo los más recientes
    if ($incluirHistoricos) {
        $sql = "
            SELECT 
                c.nombre_curso,
                SUM(CASE WHEN pc.estatus_participante = 'Aprobado' THEN 1 ELSE 0 END) AS aprobados,
                SUM(CASE WHEN pc.estatus_participante = 'Reprobado' THEN 1 ELSE 0 END) AS reprobados,
                SUM(CASE WHEN pc.estatus_participante = 'Ausente' THEN 1 ELSE 0 END) AS ausentes,
                COUNT(pc.id_participante_curso) AS total_participantes
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            GROUP BY c.id_curso
            ORDER BY c.nombre_curso ASC
        ";
    } else {
        $sql = "
            SELECT 
                c.nombre_curso,
                SUM(CASE WHEN pc.estatus_participante = 'Aprobado' THEN 1 ELSE 0 END) AS aprobados,
                SUM(CASE WHEN pc.estatus_participante = 'Reprobado' THEN 1 ELSE 0 END) AS reprobados,
                SUM(CASE WHEN pc.estatus_participante = 'Ausente' THEN 1 ELSE 0 END) AS ausentes,
                COUNT(pc.id_participante_curso) AS total_participantes
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE pc.FechaInicio = (SELECT MAX(FechaInicio) FROM tb_participante_curso WHERE id_curso = pc.id_curso)
            GROUP BY c.id_curso
            ORDER BY c.nombre_curso ASC
        ";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultado);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
include_once '../../modelo/conexion.php';

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();

    $idAreaFormativa = isset($_GET['areaFormativa']) ? intval($_GET['areaFormativa']) : null;
    $incluirParticipantes = isset($_GET['incluirParticipantes']) ? filter_var($_GET['incluirParticipantes'], FILTER_VALIDATE_BOOLEAN) : true;

    $query = "
        SELECT 
            DATE_FORMAT(c.fecha_inicio, '%b %Y') AS mes,
            COUNT(DISTINCT c.id_curso) AS total_cursos" . 
            ($incluirParticipantes ? ", COUNT(DISTINCT pc.id_participante) AS total_participantes" : "") . "
        FROM tb_curso c
        LEFT JOIN tb_participante_curso pc 
            ON c.id_curso = pc.id_curso 
            AND pc.estatus_participante IN ('Asignado', 'En formación')
        " . ($idAreaFormativa ? "WHERE c.id_areaFormativa = :id_areaFormativa AND YEAR(c.fecha_inicio) = YEAR(CURDATE())" : "WHERE YEAR(c.fecha_inicio) = YEAR(CURDATE())") . "
        GROUP BY YEAR(c.fecha_inicio), MONTH(c.fecha_inicio)
        ORDER BY YEAR(c.fecha_inicio), MONTH(c.fecha_inicio)
    ";

    $stmt = $db->prepare($query);
    if ($idAreaFormativa) {
        $stmt->bindParam(':id_areaFormativa', $idAreaFormativa, PDO::PARAM_INT);
    }

    $stmt->execute();

    $meses = [];
    $cursos = [];
    $participantes = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $meses[] = $row['mes'];
        $cursos[] = (int)$row['total_cursos'];
        if ($incluirParticipantes && isset($row['total_participantes'])) {
            $participantes[] = (int)$row['total_participantes'];
        }
    }

    echo json_encode([
        'meses' => $meses,
        'cursos' => $cursos,
        'participantes' => $participantes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}

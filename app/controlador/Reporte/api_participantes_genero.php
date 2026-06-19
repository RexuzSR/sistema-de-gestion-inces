<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
require_once("../../modelo/conexion.php"); // Ajusta si cambia

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    $sql = "
        SELECT c.nombre_curso, p.genero, COUNT(*) as cantidad
        FROM tb_participante_curso pc
        INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
        INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
        WHERE pc.estatus_participante = 'Asignado'
        GROUP BY c.nombre_curso, p.genero
        ORDER BY c.nombre_curso ASC, p.genero ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'curso' => $row['nombre_curso'],
            'genero' => $row['genero'],
            'cantidad' => (int)$row['cantidad']
        ];
    }

    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

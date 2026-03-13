<?php
// api_ocupacion_cursos.php
include '../../modelo/conexion.php'; // Incluimos la conexión PDO

$conexion = new Conexion();
$conn = $conexion->getConexion();

$query = "SELECT 
             id_curso,
             nombre_curso, 
             (num_inscritos / max_participantes) * 100 AS ocupacion 
          FROM tb_curso 
          WHERE max_participantes > 0 
            AND id_curso != 1"; // NO traer el curso con id = 1

$stmt = $conn->prepare($query);
$stmt->execute();

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'nombre_curso' => $row['nombre_curso'],
        'ocupacion' => round($row['ocupacion'], 2)
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>

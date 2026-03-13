<?php
header('Content-Type: application/json');
include('../../modelo/conexion.php');

try {
    // Crear conexión
    $conexion = new Conexion();
    $pdo = $conexion->getConexion();

    // Consulta SQL con LEFT JOIN para incluir áreas sin cursos asignados
    $query = "
        SELECT 
            af.nombre_areaFormativa AS area, 
            COUNT(c.id_curso) AS cantidad, 
            SUM(c.num_inscritos) AS num_inscritos
        FROM 
            tb_areaformativa af 
        LEFT JOIN 
            tb_curso c 
        ON 
            c.id_areaFormativa = af.id_areaFormativa 
        GROUP BY 
            af.nombre_areaFormativa
    ";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Obtener resultados
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el total de cursos
    $total = array_sum(array_column($result, 'cantidad'));

    // Agregar el porcentaje de cursos por área
    foreach ($result as &$row) {
        $row['porcentaje'] = $total > 0 ? round(($row['cantidad'] / $total) * 100, 2) : 0;
    }

    // Responder con los datos en formato JSON
    echo json_encode(['data' => $result]);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => $e->getMessage()]);
}
?>

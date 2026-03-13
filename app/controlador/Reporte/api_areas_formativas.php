<?php
include_once '../../modelo/conexion.php';

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();

    $query = "SELECT id_areaFormativa AS id, nombre_areaFormativa AS nombre FROM tb_areaformativa";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($areas);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>

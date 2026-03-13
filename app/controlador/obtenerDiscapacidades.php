<?php
require_once '../modelo/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConexion();

try {
    $sql = "SELECT id_discapacidad, nombre_discapacidad FROM tb_discapacidad ORDER BY nombre_discapacidad";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $discapacidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $discapacidades
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar discapacidades: ' . $e->getMessage()
    ]);
}
?>
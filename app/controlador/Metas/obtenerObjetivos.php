<?php
require_once __DIR__ . '/../../modelo/conexion.php';

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    $stmt = $conn->prepare("SELECT id_objetivo, descripcion FROM tb_objetivos ORDER BY descripcion");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

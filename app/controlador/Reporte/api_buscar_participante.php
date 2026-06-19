<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
require('../../modelo/conexion.php');

header('Content-Type: application/json');

if (!isset($_GET['cedula']) || !is_numeric($_GET['cedula'])) {
    echo json_encode(['error' => 'Cédula no proporcionada o inválida']);
    exit;
}

$cedula = intval($_GET['cedula']);

$conexion = new Conexion();
$conn = $conexion->getConexion();

try {
    $query = "SELECT id_participante, CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, estado FROM tb_participante WHERE cedula = :cedula";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cedula', $cedula, PDO::PARAM_INT);
    $stmt->execute();

    $participante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($participante) {
        echo json_encode($participante);
    } else {
        echo json_encode(['error' => 'Participante no encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}

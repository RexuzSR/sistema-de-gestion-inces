<?php
require_once __DIR__ . '/../app/modelo/conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConexion();

echo "=== DISTINCT nacionalidad in tb_participante ===\n";
$stmt = $conn->query("SELECT nacionalidad, COUNT(*) as cant FROM tb_participante GROUP BY nacionalidad");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "=== DISTINCT genero in tb_participante ===\n";
$stmt = $conn->query("SELECT genero, COUNT(*) as cant FROM tb_participante GROUP BY genero");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "=== DISTINCT id_discapacidad in tb_participante ===\n";
$stmt = $conn->query("SELECT id_discapacidad, COUNT(*) as cant FROM tb_participante GROUP BY id_discapacidad");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

<?php
require_once 'app/modelo/conexion.php';
$conn = (new Conexion())->getConexion();
$stmt = $conn->query("DESCRIBE tb_curso");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

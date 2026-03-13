<?php
require_once '../../modelo/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Filtros (string porque son ENUMs, no IDs)
$sexo = isset($_GET['sexo']) && $_GET['sexo'] !== '' ? $_GET['sexo'] : null;
$edad = isset($_GET['edad']) && $_GET['edad'] !== '' ? intval($_GET['edad']) : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null;

// Consulta base
$sql = "SELECT * FROM tb_docente WHERE 1=1";

$params = [];

if ($sexo) {
    $sql .= " AND genero = :sexo";
    $params[':sexo'] = $sexo;
}
if ($edad) {
    $sql .= " AND edad = :edad";
    $params[':edad'] = $edad;
}
if ($estado) {
    $sql .= " AND estado_docente = :estado";
    $params[':estado'] = $estado;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode($data);
?>

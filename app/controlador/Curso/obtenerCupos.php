<?php
require_once '../../modelo/conexion.php';

$conn = (new Conexion())->getConexion();
$stmt = $conn->prepare("SELECT 
    num_inscritos as inscritos, 
    max_participantes as maximo,
    id_estatusCurso as estatus
    FROM tb_curso WHERE id_curso = ?");
$stmt->execute([$_GET['id_curso']]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
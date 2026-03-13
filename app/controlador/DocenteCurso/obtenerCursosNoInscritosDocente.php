<?php
require_once '../../modelo/conexion.php';

header('Content-Type: application/json');

if (isset($_GET['id_docente'])) {
    $id_docente = intval($_GET['id_docente']);
    $conexion = new Conexion();
    $db = $conexion->getConexion();

    $sql = "SELECT c.id_curso, c.nombre_curso 
            FROM tb_curso c
            WHERE c.id_curso NOT IN (
                SELECT id_curso FROM tb_docente_curso WHERE id_docente = ?
            )";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_docente]);

    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cursos);
} else {
    echo json_encode([]);
}

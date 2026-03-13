<?php
require_once '../../modelo/conexion.php';

$id_docente = $_POST['id_docente'];
$nuevo_estado = $_POST['estado'];

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    // Solo actualiza si el estado actual NO es EN FORMACIÓN
    $stmt = $conn->prepare("SELECT estado_docente FROM tb_docente WHERE id_docente = ?");
    $stmt->execute([$id_docente]);
    $docente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($docente && strtoupper($docente['estado_docente']) != 'EN FORMACIÓN') {
        $update = $conn->prepare("UPDATE tb_docente SET estado_docente = ? WHERE id_docente = ?");
        $update->execute([$nuevo_estado, $id_docente]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

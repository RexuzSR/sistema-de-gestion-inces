<?php
require_once 'managerCurso.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['nombre_curso'])) {
        echo json_encode(["exists" => false]);
        exit;
    }

    $curso = new Curso();
    $nombre_curso = $_GET['nombre_curso'];

    // Si viene la flag validar_ambito
    if (isset($_GET['validar_ambito']) && $_GET['validar_ambito'] == '1') {
        $id_ambito = isset($_GET['id_ambito']) ? $_GET['id_ambito'] : null;
        $resultado = $curso->verificarNombreYAmbito($nombre_curso, $id_ambito);
        echo json_encode($resultado);
        exit;
    }

    // Si vienen fecha_inicio y turno, usar verificación compuesta para creación
    if (isset($_GET['fecha_inicio']) && isset($_GET['turno'])) {
        $fecha_inicio = $_GET['fecha_inicio'];
        $turno = $_GET['turno'];
        $exists = $curso->verificarDuplicadoCreacion($nombre_curso, $fecha_inicio, $turno);
        echo json_encode(["exists" => $exists]);
        exit;
    }

    // Fallback: verificación solo por nombre (normalizada)
    echo json_encode(["exists" => $curso->verificarNombreCurso($nombre_curso)]);
} catch (Throwable $e) {
    error_log('Error verificarCurso: ' . $e->getMessage());
    echo json_encode(["exists" => false, "error" => true]);
}
?>
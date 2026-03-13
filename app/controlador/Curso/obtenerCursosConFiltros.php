<?php
header('Content-Type: application/json');
require_once 'managerCurso.php';

$filtros = [
    'modalidad' => !empty($_GET['modalidad_filter']) ? (int)$_GET['modalidad_filter'] : null,
    'estado'    => !empty($_GET['estado_filter']) ? (int)$_GET['estado_filter'] : null,
    'turno'     => !empty($_GET['turno_filter']) ? $_GET['turno_filter'] : null,
];

try {
    $curso = new Curso();
    $resultado = $curso->obtenerCursoFiltros($filtros);

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

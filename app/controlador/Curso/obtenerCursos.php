<?php
require_once 'managerCurso.php';
ob_start();

header('Content-Type: application/json');

try {
    $curso = new Curso();

    $resultado = $curso->obtenerCursos();

    if (!$resultado || !is_array($resultado)) {
        ob_end_clean();
        echo json_encode([
            "success" => false,
            "message" => "No se encontraron cursos registrados o error al procesar los datos."
        ]);
    } else {
        ob_end_clean();
        echo json_encode($resultado);
    }
} catch (Exception $e) {
    error_log("Error en obtenerCursos.php: " . $e->getMessage());
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Error inesperado: " . $e->getMessage()
    ]);
}
?>
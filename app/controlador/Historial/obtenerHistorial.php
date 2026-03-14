<?php
require_once 'managerHistorial.php';
ob_start();

header('Content-Type: application/json');

try {
    $historial = new Historial();

    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
    $fechaDesde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
    $fechaHasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

    $resultado = $historial->obtenerHistorial($limite, $offset, $filtro, $fechaDesde, $fechaHasta);
    $total = $historial->obtenerTotalHistorial($filtro, $fechaDesde, $fechaHasta);

    ob_end_clean();
    echo json_encode([
        "success" => true,
        "data" => is_array($resultado) ? $resultado : [],
        "total" => $total,
        "limite" => $limite,
        "offset" => $offset
    ]);
} catch (Exception $e) {
    error_log("Error en obtenerHistorial.php: " . $e->getMessage());
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Error inesperado: " . $e->getMessage()
    ]);
}
?>

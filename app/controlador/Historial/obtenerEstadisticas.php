<?php
require_once 'managerHistorial.php';
ob_start();

header('Content-Type: application/json');

try {
    $historial = new Historial();

    $fechaDesde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
    $fechaHasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

    $estadisticas = $historial->obtenerEstadisticas($fechaDesde, $fechaHasta);
    $actividadesRecientes = $historial->obtenerActividadesRecientes(10);
    $tiposActividad = $historial->obtenerTiposActividad();

    ob_end_clean();
    echo json_encode([
        "success" => true,
        "estadisticas" => $estadisticas,
        "actividades_recientes" => $actividadesRecientes,
        "tipos_actividad" => $tiposActividad
    ]);

} catch (Exception $e) {
    error_log("Error en obtenerEstadisticas.php: " . $e->getMessage());
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Error inesperado: " . $e->getMessage()
    ]);
}
?>

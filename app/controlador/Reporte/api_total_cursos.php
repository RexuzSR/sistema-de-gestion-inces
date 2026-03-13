<?php
header('Content-Type: application/json');
ini_set('display_errors', 1); // Mostrar errores solo en desarrollo
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../modelo/conexion.php'; // Asegúrate de que esta ruta sea correcta

try {
    $conexionObj = new Conexion();
    $conexion = $conexionObj->getConexion();

    // Fechas de este mes
    $inicioMes = date('Y-m-01');
    $finMes = date('Y-m-t');

    // Fechas del mes pasado
    $inicioMesPasado = date('Y-m-01', strtotime('-1 month'));
    $finMesPasado = date('Y-m-t', strtotime('-1 month'));

    // Cursos creados este mes
    $sqlCursosActual = "SELECT COUNT(*) as total_actual FROM tb_curso WHERE fecha_inicio BETWEEN :inicio AND :fin";
    $stmt = $conexion->prepare($sqlCursosActual);
    $stmt->execute([':inicio' => $inicioMes, ':fin' => $finMes]);
    $totalCursosActual = $stmt->fetchColumn();

    // Cursos creados el mes pasado
    $sqlCursosAnterior = "SELECT COUNT(*) as total_anterior FROM tb_curso WHERE fecha_inicio BETWEEN :inicio AND :fin";
    $stmt = $conexion->prepare($sqlCursosAnterior);
    $stmt->execute([':inicio' => $inicioMesPasado, ':fin' => $finMesPasado]);
    $totalCursosAnterior = $stmt->fetchColumn();

    // Participantes inscritos este mes
    $sqlPartActual = "SELECT COUNT(*) as total_participantes_actual FROM tb_participante WHERE fecha_registro BETWEEN :inicio AND :fin";
    $stmt = $conexion->prepare($sqlPartActual);
    $stmt->execute([':inicio' => $inicioMes, ':fin' => $finMes]);
    $totalPartActual = $stmt->fetchColumn();

    // Participantes inscritos el mes pasado
    $sqlPartAnterior = "SELECT COUNT(*) as total_participantes_anterior FROM tb_participante WHERE fecha_registro BETWEEN :inicio AND :fin";
    $stmt = $conexion->prepare($sqlPartAnterior);
    $stmt->execute([':inicio' => $inicioMesPasado, ':fin' => $finMesPasado]);
    $totalPartAnterior = $stmt->fetchColumn();

    // Variaciones
    $variacionCursos = $totalCursosAnterior > 0 ? (($totalCursosActual - $totalCursosAnterior) / $totalCursosAnterior) * 100 : 0;
    $variacionPart = $totalPartAnterior > 0 ? (($totalPartActual - $totalPartAnterior) / $totalPartAnterior) * 100 : 0;

    // Respuesta JSON
    echo json_encode([
        "total_actual" => intval($totalCursosActual),
        "total_anterior" => intval($totalCursosAnterior),
        "variacion" => round($variacionCursos, 2),
        "total_participantes_actual" => intval($totalPartActual),
        "total_participantes_anterior" => intval($totalPartAnterior),
        "variacion_participantes" => round($variacionPart, 2)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

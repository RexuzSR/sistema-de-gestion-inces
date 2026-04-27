<?php
require_once 'app/modelo/conexion.php';
require_once 'app/controlador/Reporte/api_reportes_cursos_trayectoria.php';

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $idCurso = 408; // ID from user screenshot
    $resultado = getDetalleCursoTrayectoria($conn, $idCurso);
    echo json_encode($resultado, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

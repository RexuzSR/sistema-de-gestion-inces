<?php
require_once '../../modelo/Conexion.php';

try {
    $conn = (new Conexion())->getConexion();

    $anio1 = isset($_GET['anio1']) ? intval($_GET['anio1']) : null;
    $anio2 = isset($_GET['anio2']) && $_GET['anio2'] !== '' ? intval($_GET['anio2']) : null;

    $response = [];

    if ($anio1) {
        $stmt = $conn->prepare("
            SELECT MONTH(fecha_creacion) AS mes, COUNT(*) AS cantidad
            FROM tb_curso
            WHERE YEAR(fecha_creacion) = :anio
            GROUP BY mes
        ");
        $stmt->execute(['anio' => $anio1]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $meses = array_fill(1, 12, 0);
        foreach ($result as $row) {
            $meses[intval($row['mes'])] = intval($row['cantidad']);
        }
        $response['anio1'] = array_map(function($mes, $cantidad) {
            return ['mes' => $mes, 'cantidad' => $cantidad];
        }, array_keys($meses), array_values($meses));
    }

    if ($anio2) {
        $stmt = $conn->prepare("
            SELECT MONTH(fecha_creacion) AS mes, COUNT(*) AS cantidad
            FROM tb_curso
            WHERE YEAR(fecha_creacion) = :anio
            GROUP BY mes
        ");
        $stmt->execute(['anio' => $anio2]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $meses = array_fill(1, 12, 0);
        foreach ($result as $row) {
            $meses[intval($row['mes'])] = intval($row['cantidad']);
        }
        $response['anio2'] = array_map(function($mes, $cantidad) {
            return ['mes' => $mes, 'cantidad' => $cantidad];
        }, array_keys($meses), array_values($meses));
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}

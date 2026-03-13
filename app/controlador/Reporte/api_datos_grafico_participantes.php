<?php
require_once '../../modelo/conexion.php';
header('Content-Type: application/json');

try {
    $anios = isset($_GET['anios']) ? explode(',', $_GET['anios']) : [];

    if (empty($anios)) {
        echo json_encode([]);
        exit;
    }

    $conexionDB = new Conexion();
    $conexion = $conexionDB->getConexion();
    $resultadoFinal = [];

    foreach ($anios as $anio) {
        $anio = intval($anio);

        // Inicializar meses en 0
        $datosMeses = array_fill(1, 12, 0);

        // Consulta para contar por mes
        $sql = "
            SELECT MONTH(fecha_registro) AS mes, COUNT(*) AS total
            FROM tb_participante
            WHERE YEAR(fecha_registro) = ?
            GROUP BY MONTH(fecha_registro)
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$anio]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mes = intval($row['mes']);
            $datosMeses[$mes] = intval($row['total']);
        }

        // Agregar al array final
        $resultadoFinal[] = [
            "anio" => $anio,
            "meses" => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            "participantes" => array_values($datosMeses)
        ];
    }

    echo json_encode($resultadoFinal);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

<?php
// Devuelve los ámbitos y sus totales desde tb_curso en formato JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    require_once __DIR__ . '/../modelo/conexion.php';
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    // Filtrado por periodo usando fecha_creacion
    $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '';

    $whereFecha = '';
    if ($periodo === 'semanal') {
        $whereFecha = " AND YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)"; // semana ISO actual
    } elseif ($periodo === 'mensual') {
        $whereFecha = " AND YEAR(fecha_creacion) = YEAR(CURDATE()) AND MONTH(fecha_creacion) = MONTH(CURDATE())";
    } elseif ($periodo === 'trimestral') {
        $whereFecha = " AND YEAR(fecha_creacion) = YEAR(CURDATE()) AND QUARTER(fecha_creacion) = QUARTER(CURDATE())";
    } elseif ($periodo === 'anual') {
        $whereFecha = " AND YEAR(fecha_creacion) = YEAR(CURDATE())";
    }

    // Obtener catálogo de todos los ámbitos posibles presentes en la tabla (sin filtrar por tiempo)
    $sqlTodos = "SELECT DISTINCT ambito
                 FROM tb_curso
                 WHERE ambito IS NOT NULL AND ambito <> ''
                 ORDER BY ambito";
    $stmtTodos = $conn->prepare($sqlTodos);
    $stmtTodos->execute();
    $todosAmbitos = [];
    while ($r = $stmtTodos->fetch(PDO::FETCH_ASSOC)) {
        $todosAmbitos[] = $r['ambito'];
    }

    // Contar por ámbito aplicando el filtro de periodo
    $sql = "SELECT ambito, COUNT(*) AS total
            FROM tb_curso
            WHERE ambito IS NOT NULL AND ambito <> ''" . $whereFecha . "
            GROUP BY ambito
            ORDER BY ambito";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Map con conteos por ámbito encontrados en el periodo
    $conteos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteos[$row['ambito']] = (int)$row['total'];
    }

    // Construir salida garantizando todos los ámbitos con 0 si no existen en el periodo
    $labels = $todosAmbitos;
    $data = [];
    foreach ($todosAmbitos as $amb) {
        $data[] = isset($conteos[$amb]) ? $conteos[$amb] : 0;
    }

    echo json_encode([
        'ok' => true,
        'labels' => $labels,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Error al consultar ámbitos: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}

<?php
require_once __DIR__ . '/app/modelo/conexion.php';
$conn = (new Conexion())->getConexion();
header('Content-Type: text/plain; charset=utf-8');

$conn->exec("SET NAMES utf8");

// Mostrar el ENUM actual
$r = $conn->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='inces1' AND TABLE_NAME='tb_participante' AND COLUMN_NAME='estado'")->fetchColumn();
echo "ENUM actual: " . $r . "\n\n";

// Mostrar distribución real
$rows = $conn->query("SELECT estado, COUNT(*) t FROM tb_participante GROUP BY estado ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo "Estado: [" . $row['estado'] . "] HEX: " . bin2hex($row['estado']) . " -> " . $row['t'] . " participantes\n";
}

echo "\n--- Test de búsqueda ---\n";
$test1 = $conn->query("SELECT COUNT(*) FROM tb_participante WHERE estado = 'En formaci\xc3\xb3n'")->fetchColumn();
echo "Con UTF-8 (ó = xc3xb3): $test1\n";

$test2 = $conn->query("SELECT COUNT(*) FROM tb_participante WHERE estado = 'En formacion'")->fetchColumn();
echo "Sin acento (formacion): $test2\n";

$test3 = $conn->query("SELECT COUNT(*) FROM tb_participante WHERE estado LIKE 'En form%'")->fetchColumn();
echo "LIKE 'En form%': $test3\n";
?>

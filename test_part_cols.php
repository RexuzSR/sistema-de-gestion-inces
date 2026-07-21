<?php
require_once __DIR__ . '/app/modelo/conexion.php';
$c = (new Conexion())->getConexion();
$stmt = $c->query("DESCRIBE tb_participante");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

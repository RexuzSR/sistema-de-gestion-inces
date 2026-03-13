<?php
require_once 'managerCurso.php';
ob_start();

header('Content-Type: application/json');

try {
    $curso = new Curso();
    $response = $curso->obtenerInfoCurso($_GET['id_curso']);
    ob_end_clean();
    echo json_encode($response ?: []);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode([]);
}
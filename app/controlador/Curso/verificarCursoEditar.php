<?php
require_once 'managerCurso.php';

header('Content-Type: application/json');

try {
    $nombre = isset($_GET['nombre_curso']) ? trim($_GET['nombre_curso']) : null;
    $idCurso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

    if (!$nombre) throw new Exception('Parámetro faltante');

    $manager = new Curso();
    $existe = $manager->verificarNombreCursoEditar($nombre, $idCurso);

    echo json_encode(['exists' => $existe]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
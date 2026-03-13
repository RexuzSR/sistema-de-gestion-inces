<?php
require_once 'managerDocente.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['cedula'])) {
        throw new Exception('Parámetro cédula requerido');
    }

    $cedula = $_GET['cedula'];
    $manager = new docente();
    $existe = $manager->verificarCedula($cedula);

    echo json_encode(['exists' => $existe]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
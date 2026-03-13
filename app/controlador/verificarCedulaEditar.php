<?php
require_once 'ManagerParticipante.php'; // Nombre de archivo debe coincidir

header('Content-Type: application/json');

try {
    if (!isset($_GET['cedula']) || !isset($_GET['idParticipante'])) {
        throw new Exception('Parámetros requeridos');
    }

    $cedula = $_GET['cedula'];
    $idParticipante = (int)$_GET['idParticipante']; // Sanitización

    $manager = new participante(); // Instanciar clase correcta
    $existe = $manager->verificarCedulaEditar($cedula, $idParticipante);

    echo json_encode(['existe' => $existe]); // Clave debe coincidir con frontend

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}
?>
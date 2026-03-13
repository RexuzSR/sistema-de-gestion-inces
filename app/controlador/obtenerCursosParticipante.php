<?php
require_once 'managerParticipante.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        throw new Exception('ID inválido');
    }

    $participante = new Participante();
    $resultado = $participante->obtenerCursosParticipante($id);
    
    echo json_encode($resultado);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
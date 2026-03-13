<?php
require_once 'managerParticipante.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    try {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        
        if (!$id) {
            throw new Exception('ID inválido');
        }

        $participante = new Participante();
        $resultado = $participante->verParticipante($id);
        
        echo json_encode($resultado);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
}
?>
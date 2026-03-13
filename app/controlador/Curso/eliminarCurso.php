<?php
// llamado al archivo managerCurso.php 
require_once 'managerCurso.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idCurso = $_POST['id'];

    try {
        // Instanciar la clase y llamar al método eliminarCurso
        $cursoManager = new Curso();
        $response = $cursoManager->eliminarCurso($idCurso);

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error interno: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID no recibido o método HTTP incorrecto"
    ]);
}
?>

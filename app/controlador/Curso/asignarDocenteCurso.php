<?php
// Llamado al archivo managerCurso.php
require_once 'managerCurso.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['docente_id']) && isset($_POST['curso_id'])) {
    $idDocente = $_POST['docente_id'];
    $idCurso = $_POST['curso_id'];

    try {
        // Instanciar la clase y llamar al método asignarDocente
        $cursoManager = new Curso();
        $response = $cursoManager->asignarDocenteCurso($idCurso, $idDocente);

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
        "message" => "Datos insuficientes o método HTTP incorrecto."
    ]);
}
?>

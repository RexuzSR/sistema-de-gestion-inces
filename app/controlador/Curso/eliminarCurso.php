<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
// llamado al archivo managerCurso.php 
require_once 'managerCurso.php';
require_once '../../modelo/HistorialUtil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idCurso = $_POST['id'];

    try {
        // Instanciar la clase y llamar al método eliminarCurso
        $cursoManager = new Curso();
        
        // Obtener nombre antes de borrar para el historial
        $cursoData = $cursoManager->obtenerCursoPorId($idCurso);
        $nombreCurso = isset($cursoData['nombre_curso']) ? $cursoData['nombre_curso'] : "ID: $idCurso";

        $response = $cursoManager->eliminarCurso($idCurso);

        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            HistorialUtil::registrarEliminarCurso($idCurso, $nombreCurso, $_SESSION['id_usuario']);
        }

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

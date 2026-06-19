<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
// llamado al archivo managerDocente.php 
require_once 'managerDocente.php';
require_once '../../modelo/HistorialUtil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idParticipante = $_POST['id'];

    try {
        // Instanciar la clase y llamar al método eliminar
        $docenteMgr = new docente();
        
        // Obtener datos antes de borrar
        $docData = $docenteMgr->obtenerPorId($idParticipante);
        $nombreDocente = isset($docData['primer_nombre']) ? $docData['primer_nombre'] . " " . $docData['primer_apellido'] : "ID: $idParticipante";

        $response = $docenteMgr->eliminar($idParticipante);

        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            HistorialUtil::registrarEliminarDocente($idParticipante, $nombreDocente, $_SESSION['id_usuario']);
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

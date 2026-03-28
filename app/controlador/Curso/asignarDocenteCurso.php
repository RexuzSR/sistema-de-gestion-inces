<?php
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Llamado al archivo managerCurso.php
require_once 'managerCurso.php';
require_once '../../modelo/HistorialUtil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['docente_id']) && isset($_POST['curso_id'])) {
    $idDocente = $_POST['docente_id'];
    $idCurso = $_POST['curso_id'];

    try {
        // Instanciar la clase y llamar al método asignarDocente
        $cursoManager = new Curso();
        $response = $cursoManager->asignarDocenteCurso($idCurso, $idDocente);

        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            // Obtener nombres para el historial
            $cursoData = $cursoManager->obtenerCursoPorId($idCurso);
            $nombreCurso = $cursoData['nombre_curso'] ?? "ID: $idCurso";
            
            require_once '../Docente/managerDocente.php';
            $docenteMgr = new Docente();
            $docDataRes = $docenteMgr->obtenerDocentePorId($idDocente);
            $docData = $docDataRes['data'] ?? [];
            $nombreDocente = isset($docData['primer_nombre']) ? $docData['primer_nombre'] . " " . $docData['primer_apellido'] : "ID: $idDocente";

            HistorialUtil::registrarAsignarDocenteCurso($idDocente, $nombreDocente, $idCurso, $nombreCurso, $_SESSION['id_usuario']);
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
        "message" => "Datos insuficientes o método HTTP incorrecto."
    ]);
}
?>

<?php
// Iniciar buffer de salida
ob_start();

require_once 'managerParticipanteCurso.php';
require_once '../../modelo/conexion.php';


header('Content-Type: application/json');

try {
    // Verificar el método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    // Obtener y decodificar los datos (JSON o form-data)
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    if (empty($input['id_participante']) || empty($input['curso'])) {
        throw new Exception("Datos incompletos", 400);
    }

    // Validación de datos: asegurarse de que sean enteros válidos
    $id_participante = filter_var($input['id_participante'], FILTER_VALIDATE_INT);
    $id_curso = filter_var($input['curso'], FILTER_VALIDATE_INT);

    if (!$id_participante || !$id_curso) {
        throw new Exception("IDs inválidos", 400);
    }

    // Procesar la asignación
    $participanteCurso = new ParticipanteCurso();
    $resultado = $participanteCurso->asignarCurso($id_participante, $id_curso);

    // Limpiar buffer de salida y enviar respuesta JSON
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Curso asignado exitosamente'
    ]);
    
} catch (Throwable $e) { // Capturar cualquier error
    ob_end_clean();
    http_response_code($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'code'    => $e->getCode()
    ]);
    
} finally {
    exit;
}
?>

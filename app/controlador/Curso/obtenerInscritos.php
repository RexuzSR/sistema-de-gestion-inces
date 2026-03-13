<?php
require_once 'managerCurso.php';
ob_start();

header('Content-Type: application/json');

try {
    if (!isset($_GET['id_curso']) || empty($_GET['id_curso'])) {
        throw new Exception("Parámetro id_curso requerido", 400);
    }
    
    $idCurso = (int)$_GET['id_curso'];
    error_log("obtenerInscritos.php - solicitando inscritos para id_curso: $idCurso");

    $curso = new Curso();
    
    $participantes = $curso->obtenerParticipantesInscritos($idCurso);
    
    // Formatear para frontend
    $participantesFormateados = [];
    foreach ($participantes as $p) {
        $participantesFormateados[] = [
            'id_participante_curso' => $p['id_participante_curso'],
            'id_participante' => $p['id_participante'],
            'cedula' => $p['cedula'],
            'nombre' => $p['nombre'] ?? ($p['primer_nombre'] ?? ''),
            'apellido' => $p['apellido'] ?? ($p['primer_apellido'] ?? ''),
            'nombre_completo' => trim((($p['nombre'] ?? ($p['primer_nombre'] ?? '')) . ' ' . ($p['apellido'] ?? ($p['primer_apellido'] ?? '')))),
            'edad' => isset($p['edad']) ? $p['edad'] : null,
            'genero' => $p['genero'] ?? null
        ];
    }
    

    
    ob_end_clean();
    echo json_encode($participantesFormateados);
    
} catch(Exception $e) {
    http_response_code($e->getCode() ?: 500);
    error_log("Error en obtenerInscritos.php - " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch(Throwable $t) {
    http_response_code(500);
    error_log("FATAL en obtenerInscritos.php - " . $t->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}
?>
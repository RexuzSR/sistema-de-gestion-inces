<?php
require_once 'managerCurso.php';
ob_start();

header('Content-Type: application/json');

try {
    // Validar parámetro requerido
    if (!isset($_GET['id_curso']) || empty($_GET['id_curso'])) {
        throw new InvalidArgumentException("Parámetro id_curso requerido", 400);
    }
    
    $idCurso = (int)$_GET['id_curso'];
    $busqueda = $_GET['busqueda'] ?? '';
    
    $curso = new Curso();
    
    // 1. Verificar estado del curso
    $estadoCurso = $curso->obtenerIdEstatusCurso($idCurso);
    
    if ($estadoCurso === null) {
        throw new Exception("Curso no encontrado", 404);
    }
    
    // Solo permitir cursos en estado 1 (En espera)
    if ($estadoCurso != 1) {
        $estadoTexto = getTextoEstado($estadoCurso);
        echo json_encode([
            'success' => false,
            'error' => 'Curso no disponible para asignaciones',
            'estado_curso' => $estadoCurso,
            'estado_texto' => $estadoTexto,
            'disponible' => false
        ]);
        exit;
    }
    
    // 2. Obtener información del curso para cupos
    $infoCurso = $curso->obtenerInfoCurso($idCurso);
    
    if ($infoCurso === false) {
        throw new Exception("Error al obtener información del curso", 500);
    }
    
    // Verificar cupos
    if ($infoCurso['num_inscritos'] >= $infoCurso['max_participantes']) {
        echo json_encode([
            'success' => false,
            'error' => 'Cupos completos',
            'cupos_llenos' => true,
            'inscritos' => $infoCurso['num_inscritos'],
            'maximo' => $infoCurso['max_participantes'],
            'disponible' => false
        ]);
        exit;
    }

    // 3. Obtener participantes disponibles
    $participantes = $curso->obtenerParticipantesDisponibles($idCurso, $busqueda);
    
    // 4. Devolver respuesta exitosa
    ob_end_clean(); // Limpiar cualquier salida previa (warnings, espacios)
    echo json_encode([
        'success' => true,
        'disponible' => true,
        'cupos_llenos' => false,
        'inscritos' => $infoCurso['num_inscritos'],
        'maximo' => $infoCurso['max_participantes'],
        'participantes' => $participantes,
        'busqueda' => $busqueda
    ]);
    
} catch(InvalidArgumentException $e) {
    error_log("Error de validación: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
} catch(Exception $e) {
    error_log("ERROR obtenerDisponibles: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
} catch(Throwable $t) {
    error_log("FATAL ERROR obtenerDisponibles: " . $t->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}

// Función auxiliar para obtener texto del estado
function getTextoEstado($idEstado) {
    $estados = [
        1 => 'En espera',
        2 => 'Diferido',
        3 => 'En proceso',
        4 => 'En pausa',
        5 => 'Culminado',
        6 => 'Inactivo'
    ];
    return $estados[$idEstado] ?? 'Desconocido';
}
?>
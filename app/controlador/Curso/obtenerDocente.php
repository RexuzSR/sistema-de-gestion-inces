<?php
require_once 'managerCurso.php';
header('Content-Type: application/json');

// Aceptar tanto 'id' como 'id_curso' para compatibilidad
$idCurso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idCurso) {
    $idCurso = filter_input(INPUT_GET, 'id_curso', FILTER_VALIDATE_INT);
}

if (!$idCurso) {
    echo json_encode(['success' => false, 'message' => 'ID de curso no proporcionado']);
    exit;
}

try {
    $cursoManager = new Curso();
    $docente = $cursoManager->obtenerDocentePorCurso($idCurso);

    if ($docente) {
        // Normalizar campos y nombre
        $nombre = trim(($docente['primer_nombre'] ?? '') . ' ' . ($docente['primer_apellido'] ?? '')) ?: 'No asignado';
        $cedula = $docente['cedula'] ?? 'No asignado';
        $edad = $docente['edad'] ?? 'No asignado';

        echo json_encode([
            'success' => true,
            'docente' => [
                'nombre' => $nombre,
                'cedula' => $cedula,
                'edad' => $edad
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No hay docente asignado para este curso'
        ]);
    }
} catch (Exception $e) {
    error_log("Error en obtenerDocente.php - ID Curso: $idCurso - " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno al obtener docente'
    ]);
}

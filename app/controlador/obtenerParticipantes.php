<?php
// obtenerParticipante.php
require_once '../modelo/conexion.php';
require_once 'managerParticipante.php'; // Asegúrate de que esta ruta sea correcta

$participante = new Participante();

try {
    // Obtener parámetros de búsqueda y filtros
    $filtros = [
        'search' => $_GET['search'] ?? '',
        'sexo' => $_GET['genero'] ?? '', // Nota: tabla-participantes.js envía 'genero', manager espera 'sexo' o ajustamos
        'edad' => $_GET['edad'] ?? '',
        'estado' => $_GET['estado'] ?? ''
    ];

    // Mapeo simple si el JS envía 'genero' pero la clase espera 'sexo'
    // En managerParticipante.php:494 usa $filtros['sexo']
    if (isset($_GET['genero'])) {
        $filtros['sexo'] = $_GET['genero'];
    }

    $participantes = $participante->filtrarParticipantes($filtros);
    
    if ($participantes) {
        echo json_encode(['data' => $participantes, 'total' => count($participantes)]);
    } else {
        echo json_encode(['data' => [], 'total' => 0]); // Devolver array vacío válido
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error al obtener participantes: ' . $e->getMessage()]);
}
?>
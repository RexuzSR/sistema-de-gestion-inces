<?php
require_once '../modelo/conexion.php';
require_once 'managerParticipante.php';

header('Content-Type: application/json');

try {
    // Verificar si el ID se envía por GET o POST
    if (isset($_POST['id_participante'])) {
        $id = intval($_POST['id_participante']);
    } elseif (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        throw new Exception('ID no proporcionado');
    }

    $participanteModel = new Participante();
    $participante = $participanteModel->obtenerPorId($id);

    if ($participante) {
        // Estructura que espera el JavaScript (con 'success' y 'data')
        echo json_encode([
            'success' => true,
            'data' => [
                'id_participante' => $participante->id_participante,
                'primer_nombre' => $participante->primer_nombre,
                'segundo_nombre' => $participante->segundo_nombre,
                'primer_apellido' => $participante->primer_apellido,
                'segundo_apellido' => $participante->segundo_apellido,
                'cedula' => $participante->cedula,
                'nacionalidad' => $participante->nacionalidad ?? 'V',
                'telefono' => $participante->telefono,
                'correo' => $participante->correo,
                'fecha_nacimiento' => $participante->fecha_nacimiento, // Formato Y-m-d
                'grado_institucion' => $participante->grado_institucion,
                'genero' => $participante->genero,
                'id_discapacidad' => $participante->id_discapacidad
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Participante no encontrado'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
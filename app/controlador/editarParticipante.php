<?php
require_once '../modelo/conexion.php';
require_once 'managerParticipante.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar campos requeridos (ajustados según la base de datos)
        $requiredFields = [
            'id_participante', 'primer_nombre', 'primer_apellido', 'cedula',
            'genero', 'correo', 'fecha_nacimiento',
            'grado_institucion', 'id_discapacidad'
        ];
        
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            throw new Exception("Faltan campos requeridos: " . implode(', ', $missingFields));
        }

        // Validar opciones permitidas
        $generosPermitidos = ['Masculino', 'Femenino'];
        $gradosPermitidos = ['Primaria', 'Bachillerato', 'Universidad'];
        
        if (!in_array($_POST['genero'], $generosPermitidos)) {
            throw new Exception("Género no válido");
        }
        
        if (!in_array($_POST['grado_institucion'], $gradosPermitidos)) {
            throw new Exception("Grado de instrucción no válido");
        }

        // Preparar datos según la estructura de la tabla tb_participante
        $data = [
            'id_participante' => $_POST['id_participante'],
            'primer_nombre' => $_POST['primer_nombre'],
            'segundo_nombre' => $_POST['segundo_nombre'] ?? null, // Campo opcional
            'primer_apellido' => $_POST['primer_apellido'],
            'segundo_apellido' => $_POST['segundo_apellido'] ?? null, // Campo opcional
            'cedula' => $_POST['cedula'],
            'correo' => $_POST['correo'],
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'grado_institucion' => $_POST['grado_institucion'],
            'genero' => $_POST['genero'],
            'id_discapacidad' => $_POST['id_discapacidad'],
            'telefono' => $_POST['telefono'] ?? null // Para tabla tb_telefono
        ];

        // Actualizar en base de datos
        $dao = new Participante();
        $resultado = $dao->actualizarParticipante($data);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Datos actualizados correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se realizaron cambios en el registro'
            ]);
        }
        
    } catch (PDOException $e) {
        $mensaje = ($e->errorInfo[1] == 1062) 
                 ? 'La cédula ya está registrada en otro participante' 
                 : 'Error de base de datos: ' . $e->getMessage();
        
        echo json_encode([
            'success' => false,
            'message' => $mensaje
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Se requiere POST'
    ]);
}
?>
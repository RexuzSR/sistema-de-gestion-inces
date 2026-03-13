<?php
require_once 'managerDocente.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos requeridos estén presentes
    $requiredFields = [
        'id_docente',
        'cedula', 
        'primer_nombre',
        'primer_apellido',
        'correo',
        'fecha_nacimiento',
        'genero',
        'tipo_docente',
        'estado_docente'
    ];
    
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Campos obligatorios faltantes: ' . implode(', ', $missingFields)
        ]);
        exit;
    }
    
    // Preparar datos con validación
    $data = [
        'id_docente' => intval($_POST['id_docente']),
        'cedula' => trim($_POST['cedula']),
        'telefono' => !empty($_POST['telefono']) ? trim($_POST['telefono']) : null,
        'primer_nombre' => trim($_POST['primer_nombre']),
        'segundo_nombre' => isset($_POST['segundo_nombre']) ? trim($_POST['segundo_nombre']) : null,
        'primer_apellido' => trim($_POST['primer_apellido']),
        'segundo_apellido' => isset($_POST['segundo_apellido']) ? trim($_POST['segundo_apellido']) : null,
        'correo' => trim($_POST['correo']),
        'fechaNacimiento' => $_POST['fecha_nacimiento'],
        'genero' => $_POST['genero'],
        'tipo_docente' => $_POST['tipo_docente'],
        'estado_docente' => $_POST['estado_docente']
    ];
    
    // Validaciones adicionales
    if (!preg_match('/^\d{7,8}$/', $data['cedula'])) {
        echo json_encode(['success' => false, 'message' => 'Cédula inválida (7-8 dígitos)']);
        exit;
    }
    
    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido']);
        exit;
    }
    
    // Validar fecha de nacimiento
    $fechaNac = DateTime::createFromFormat('Y-m-d', $data['fechaNacimiento']);
    if (!$fechaNac) {
        echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido']);
        exit;
    }
    
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y;
    if ($edad < 18 || $edad > 70) {
        echo json_encode(['success' => false, 'message' => 'La edad debe estar entre 18 y 70 años']);
        exit;
    }
    
    if ($fechaNac > $hoy) {
        echo json_encode(['success' => false, 'message' => 'La fecha de nacimiento no puede ser futura']);
        exit;
    }
    
    // Validar teléfono si está presente
    if ($data['telefono'] && !preg_match('/^0[0-9]{10}$/', $data['telefono'])) {
        echo json_encode(['success' => false, 'message' => 'Teléfono inválido (debe tener 11 dígitos comenzando con 0)']);
        exit;
    }
    
    // Validar que el género sea válido
    if (!in_array($data['genero'], ['Masculino', 'Femenino'])) {
        echo json_encode(['success' => false, 'message' => 'Género inválido']);
        exit;
    }
    
    // Validar tipo de docente
    $tiposValidos = ['Fijo', 'Contratado', 'PDL', 'Voluntario'];
    if (!in_array($data['tipo_docente'], $tiposValidos)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de docente inválido']);
        exit;
    }
    
    // Validar estado del docente
    $estadosValidos = ['Disponible', 'En formación', 'Asignado', 'Inactivo'];
    if (!in_array($data['estado_docente'], $estadosValidos)) {
        echo json_encode(['success' => false, 'message' => 'Estado de docente inválido']);
        exit;
    }
    
    try {
        $docente = new Docente();
        $response = $docente->editar($data);
        
        echo json_encode($response);
    } catch (Exception $e) {
        error_log("Error en editarDocente.php: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método HTTP no permitido']);
}
exit;
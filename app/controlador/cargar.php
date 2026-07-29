<?php
// Activar reporte de errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Iniciar buffer de salida
ob_start();

// Headers para JSON
header('Content-Type: application/json');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

// Incluir dependencias
require_once 'managerParticipante.php';
require_once '../modelo/HistorialUtil.php';

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    ob_end_flush();
    exit;
}

// FUNCIÓN PARA LIMPIAR DATOS
function limpiarDato($dato) {
    return isset($dato) ? trim($dato) : '';
}

// Procesar discapacidades múltiples y calcular id_discapacidad para retrocompatibilidad
$discapacidades = isset($_POST['discapacidades']) && is_array($_POST['discapacidades']) ? array_map('intval', $_POST['discapacidades']) : [1];
$id_discapacidad = 1;
foreach ($discapacidades as $id_d) {
    if ($id_d > 1) {
        $id_discapacidad = $id_d;
        break;
    }
}

// Obtener datos del formulario usando la función limpiarDato
$data = [
    'primer_nombre' => limpiarDato($_POST['primer_nombre'] ?? ''),
    'segundo_nombre' => limpiarDato($_POST['segundo_nombre'] ?? ''),
    'primer_apellido' => limpiarDato($_POST['primer_apellido'] ?? ''),
    'segundo_apellido' => limpiarDato($_POST['segundo_apellido'] ?? ''),
    'cedula' => ltrim(limpiarDato($_POST['cedula'] ?? ''), '0'),
    'fecha_nacimiento' => limpiarDato($_POST['fecha_nacimiento'] ?? ''),
    'telefono' => limpiarDato($_POST['telefono'] ?? ''),
    'correo' => limpiarDato($_POST['correo'] ?? ''),
    'genero' => limpiarDato($_POST['genero'] ?? ''),
    'id_discapacidad' => $id_discapacidad,
    'discapacidades' => $discapacidades,
    'grado_institucion' => limpiarDato($_POST['grado_institucion'] ?? ''),
    'id_curso' => isset($_POST['id_curso']) && $_POST['id_curso'] !== '' ? (int)$_POST['id_curso'] : null
];

// Validaciones básicas
$camposRequeridos = [
    'primer_nombre' => 'Primer nombre',
    'primer_apellido' => 'Primer apellido',
    'cedula' => 'Cédula',
    'fecha_nacimiento' => 'Fecha de nacimiento',
    'telefono' => 'Teléfono',
    'correo' => 'Correo electrónico',
    'genero' => 'Género',
    'grado_institucion' => 'Grado de instrucción'
];

$errores = [];
foreach ($camposRequeridos as $campo => $nombre) {
    if (empty($data[$campo])) {
        $errores[] = "El campo $nombre es obligatorio";
    }
}

// Si hay errores, enviarlos
if (!empty($errores)) {
    echo json_encode([
        'success' => false, 
        'message' => implode('. ', $errores)
    ]);
    ob_end_flush();
    exit;
}

// Validar cédula (7-8 dígitos)
if (!preg_match('/^\d{7,8}$/', $data['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'La cédula debe tener 7 u 8 dígitos']);
    ob_end_flush();
    exit;
}

// Validar teléfono (10-11 dígitos venezolanos)
if (!preg_match('/^\d{10,11}$/', $data['telefono'])) {
    echo json_encode(['success' => false, 'message' => 'El teléfono debe tener 10 u 11 dígitos']);
    ob_end_flush();
    exit;
}

// Validar correo electrónico
if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
    ob_end_flush();
    exit;
}

// Validar fecha de nacimiento
try {
    $fechaNacimiento = new DateTime($data['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;

    if ($edad < 14) {
        echo json_encode(['success' => false, 'message' => 'El participante debe tener al menos 14 años']);
        ob_end_flush();
        exit;
    }
    
    if ($edad > 100) {
        echo json_encode(['success' => false, 'message' => 'Edad no válida']);
        ob_end_flush();
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Fecha de nacimiento no válida']);
    ob_end_flush();
    exit;
}

// Crear instancia del manager
try {
    $participanteManager = new Participante();
    
    // Verificar si la cédula ya existe
    if ($participanteManager->verificarCedula($data['cedula'])) {
        echo json_encode(['success' => false, 'message' => 'La cédula ya está registrada']);
        ob_end_flush();
        exit;
    }
    
    // Intentar registrar el participante
    $resultado = $participanteManager->registrar($data);
    
    if ($resultado['success'] && isset($_SESSION['id_usuario'])) {
        $nombreCompleto = $data['primer_nombre'] . " " . $data['primer_apellido'];
        HistorialUtil::registrarCrearParticipante($resultado['id'], $nombreCompleto, $_SESSION['id_usuario']);
    }

    // Limpiar cualquier salida buffer antes de enviar JSON
    ob_clean();
    echo json_encode($resultado);
    ob_end_flush();
    
} catch (Exception $e) {
    // Limpiar buffer y enviar error
    ob_clean();
    error_log("Error en cargar.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar participante: ' . $e->getMessage()
    ]);
    ob_end_flush();
}
?>
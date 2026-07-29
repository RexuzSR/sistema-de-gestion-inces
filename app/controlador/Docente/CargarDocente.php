<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
// Incluir el manager correcto
require_once 'managerDocente.php';
require_once '../../modelo/HistorialUtil.php';

header('Content-Type: application/json');

// Activar depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recoger los datos del formulario
        $data = [
            'primer_nombre' => $_POST['primer_nombre'] ?? '',
            'segundo_nombre' => $_POST['segundo_nombre'] ?? null,
            'primer_apellido' => $_POST['primer_apellido'] ?? '',
            'segundo_apellido' => $_POST['segundo_apellido'] ?? null,
            'cedula' => ltrim(trim($_POST['cedula'] ?? ''), '0'),
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'fechaNacimiento' => $_POST['fechaNacimiento'] ?? '',
            'genero' => $_POST['genero'] ?? 'Masculino',
            'tipo_docente' => $_POST['tipo_docente'] ?? 'Contratado'
        ];

        // Validar campos obligatorios
        $camposObligatorios = [
            'primer_nombre' => 'Primer Nombre',
            'primer_apellido' => 'Primer Apellido',
            'cedula' => 'Cédula',
            'correo' => 'Correo Electrónico',
            'fechaNacimiento' => 'Fecha de Nacimiento',
            'genero' => 'Género',
            'tipo_docente' => 'Tipo de Docente'
        ];
        
        $camposFaltantes = [];
        foreach ($camposObligatorios as $campo => $nombre) {
            if (empty(trim($data[$campo]))) {
                $camposFaltantes[] = $nombre;
            }
        }
        
        if (!empty($camposFaltantes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Los siguientes campos son obligatorios: ' . implode(', ', $camposFaltantes)
            ]);
            exit;
        }

        // Validar formato de cédula
        if (!preg_match('/^[0-9]+$/', $data['cedula'])) {
            echo json_encode([
                'success' => false,
                'message' => 'La cédula debe contener solo números'
            ]);
            exit;
        }

        // Validar formato de correo
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo electrónico no tiene un formato válido'
            ]);
            exit;
        }

        // Instanciar la clase docente
        $docenteManager = new docente();
        
        // Verificar si la cédula ya existe
        if ($docenteManager->verificarCedula($data['cedula'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe un docente registrado con esta cédula'
            ]);
            exit;
        }
        
        // Registrar el docente
        $response = $docenteManager->registrar($data);
        
        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            $nombreCompleto = $data['primer_nombre'] . " " . $data['primer_apellido'];
            HistorialUtil::registrarCrearDocente($response['id_docente'], $nombreCompleto, $_SESSION['id_usuario']);
        }

        // Devolver respuesta JSON
        echo json_encode($response);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Se requiere POST.'
    ]);
}
?>
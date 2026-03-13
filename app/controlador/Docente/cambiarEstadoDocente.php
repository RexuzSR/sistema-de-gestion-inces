<?php
// Incluir el archivo con la clase Docente
require_once 'managerDocente.php';

// Asegurar salida en formato JSON
header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['id_docente']) || !isset($_POST['nuevo_estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id_docente = $_POST['id_docente'];
$nuevo_estado = $_POST['nuevo_estado'];

try {
    $docenteManager = new docente();
    $resultado = $docenteManager->cambiarEstado($id_docente, $nuevo_estado);
    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
?>
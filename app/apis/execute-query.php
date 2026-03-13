<?php
// api/execute-query.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración XAMPP
$config = [
    'host' => 'localhost',
    'database' => 'inces1',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Obtener datos
$input = json_decode(file_get_contents('php://input'), true);
$query = $input['query'] ?? '';
$params = $input['params'] ?? [];

if (empty($query)) {
    echo json_encode(['success' => false, 'error' => 'No query provided']);
    exit;
}

try {
    // Conexión
    $conn = new mysqli($config['host'], $config['user'], $config['password'], $config['database']);
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset($config['charset']);
    
    // Preparar consulta
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    // Bind parameters si hay
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    // Ejecutar
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Obtener resultados
    $result = $stmt->get_result();
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'rowCount' => count($data)
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'query' => $query
    ]);
}
?>
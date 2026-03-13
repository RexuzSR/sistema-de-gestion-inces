<?php
// Incluir el archivo con la clase Docente
require_once 'managerDocente.php';

// Asegurar salida en formato JSON
header('Content-Type: application/json');

// Activar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $docenteManager = new docente();
    
    // Obtener parámetros de la solicitud
    $sexo = isset($_GET['sexo']) && $_GET['sexo'] !== '' ? $_GET['sexo'] : null;
    $edad = isset($_GET['edad']) && $_GET['edad'] !== '' ? intval($_GET['edad']) : null;
    $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null;
    $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
    $sort_field = isset($_GET['sort_field']) ? $_GET['sort_field'] : 'id_docente';
    $sort_direction = isset($_GET['sort_direction']) ? strtoupper($_GET['sort_direction']) : 'DESC';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 8;
    
    // Validar parámetros
    $page = max(1, $page);
    $per_page = max(0, $per_page);
    
    // Crear conexión PDO
    require_once '../../modelo/conexion.php';
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Construir consulta base CON filtros en SQL (más eficiente)
    $sql = "SELECT 
                d.id_docente,
                d.cedula,
                d.primer_nombre,
                d.segundo_nombre,
                d.primer_apellido,
                d.segundo_apellido,
                d.correo,
                d.fecha_nacimiento,
                d.edad,
                d.genero,
                d.estado_docente as estado,
                d.tipo_docente,
                t.numero_de_telefono as telefono
            FROM tb_docente d
            LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
            WHERE 1=1";
    
    $params = [];
    $conditions = [];
    
    // Aplicar filtros en SQL (más eficiente que hacerlo en PHP)
    if ($sexo !== null) {
        $conditions[] = "d.genero = :sexo";
        $params[':sexo'] = $sexo;
    }
    
    if ($edad !== null) {
        $conditions[] = "d.edad = :edad";
        $params[':edad'] = $edad;
    }
    
    if ($estado !== null) {
        $conditions[] = "d.estado_docente = :estado";
        $params[':estado'] = $estado;
    }
    
    // Aplicar búsqueda si se proporciona
    if ($search !== null) {
        $conditions[] = "(d.cedula LIKE :search OR 
                         CONCAT(d.primer_nombre, ' ', COALESCE(d.segundo_nombre, '')) LIKE :search OR 
                         CONCAT(d.primer_apellido, ' ', COALESCE(d.segundo_apellido, '')) LIKE :search OR 
                         d.primer_nombre LIKE :search OR 
                         d.segundo_nombre LIKE :search OR 
                         d.primer_apellido LIKE :search OR 
                         d.segundo_apellido LIKE :search OR 
                         d.correo LIKE :search)";
        $params[':search'] = "%" . $search . "%";
    }
    
    // Agregar condiciones a la consulta
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    
    // Agrupar para evitar duplicados por teléfono
    $sql .= " GROUP BY d.id_docente";
    
    // Validar y aplicar ordenamiento
    $allowed_fields = ['id_docente', 'cedula', 'primer_nombre', 'primer_apellido', 'edad', 'genero', 'estado_docente', 'correo', 'estado'];
    $sort_field = in_array($sort_field, $allowed_fields) ? $sort_field : 'id_docente';
    $sort_direction = in_array(strtoupper($sort_direction), ['ASC', 'DESC']) ? strtoupper($sort_direction) : 'DESC';
    
    // Mapear campos de ordenamiento
    $sort_mapping = [
        'nombre' => 'primer_nombre',
        'apellido' => 'primer_apellido',
        'estado' => 'estado_docente',
        'genero' => 'genero'
    ];
    
    $db_sort_field = $sort_mapping[$sort_field] ?? $sort_field;
    
    // Aplicar ordenamiento
    if ($db_sort_field === 'primer_nombre') {
        $sql .= " ORDER BY d.primer_nombre, d.segundo_nombre $sort_direction";
    } elseif ($db_sort_field === 'primer_apellido') {
        $sql .= " ORDER BY d.primer_apellido, d.segundo_apellido $sort_direction";
    } else {
        $sql .= " ORDER BY d.$db_sort_field $sort_direction";
    }
    
    // Consulta para contar total de registros
    $sql_count = "SELECT COUNT(DISTINCT d.id_docente) as total 
                  FROM tb_docente d
                  LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
                  WHERE 1=1";
    
    if (!empty($conditions)) {
        $sql_count .= " AND " . implode(" AND ", $conditions);
    }
    
    // Ejecutar consulta de conteo
    $stmt_count = $conn->prepare($sql_count);
    foreach ($params as $key => $value) {
        // Asegurar el tipo correcto de parámetro
        if ($key === ':edad') {
            $stmt_count->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt_count->bindValue($key, $value);
        }
    }
    $stmt_count->execute();
    $total_row = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $total = $total_row['total'];
    
    // Aplicar paginación
    if ($per_page > 0) {
        $offset = ($page - 1) * $per_page;
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $per_page;
        $params[':offset'] = $offset;
    }
    
    // Ejecutar consulta principal
    $stmt = $conn->prepare($sql);
    
    foreach ($params as $key => $value) {
        $param_type = PDO::PARAM_STR;
        if ($key === ':limit' || $key === ':offset' || $key === ':edad') {
            $param_type = PDO::PARAM_INT;
        }
        $stmt->bindValue($key, $value, $param_type);
    }
    
    $stmt->execute();
    $docentes_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear los datos para la tabla
    $docentes = [];
    foreach ($docentes_raw as $docente) {
        // Construir nombre completo
        $nombreCompleto = trim($docente['primer_nombre'] . ' ' . ($docente['segundo_nombre'] ?? ''));
        $apellidoCompleto = trim($docente['primer_apellido'] . ' ' . ($docente['segundo_apellido'] ?? ''));
        
        $docentes[] = [
            'id_docente' => $docente['id_docente'] ?? 0,
            'nombre' => $nombreCompleto,
            'apellido' => $apellidoCompleto,
            'cedula' => $docente['cedula'] ?? '',
            'telefono' => $docente['telefono'] ?? '',
            'correo' => $docente['correo'] ?? '',
            'fecha_nacimiento' => $docente['fecha_nacimiento'] ?? '',
            'edad' => $docente['edad'] ?? 0,
            'estado' => $docente['estado'] ?? 'Disponible',
            'genero' => $docente['genero'] ?? '',
            'tipo_docente' => $docente['tipo_docente'] ?? ''
        ];
    }
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'data' => $docentes,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => $per_page > 0 ? ceil($total / $per_page) : 1,
        'filters' => [
            'sexo' => $sexo,
            'edad' => $edad,
            'estado' => $estado,
            'search' => $search
        ],
        'sort' => [
            'field' => $sort_field,
            'direction' => $sort_direction
        ]
    ];
    
    echo json_encode($response);
    
    // Cerrar conexiones
    $stmt = null;
    $stmt_count = null;
    $conn = null;
    
} catch (Exception $e) {
    error_log("Error en obtenerDocentes.php: " . $e->getMessage());
    echo json_encode([
        'error' => true, 
        'message' => 'Error al obtener los Docentes: ' . $e->getMessage(),
        'data' => []
    ]);
    exit;
}
?>
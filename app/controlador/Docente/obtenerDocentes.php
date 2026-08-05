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
    $nacionalidad = isset($_GET['nacionalidad']) && $_GET['nacionalidad'] !== '' ? strtoupper($_GET['nacionalidad']) : null;
    $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
    $sort_field = isset($_GET['sort_field']) ? $_GET['sort_field'] : 'id_docente';
    $sort_direction = isset($_GET['sort_direction']) ? strtoupper($_GET['sort_direction']) : 'DESC';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 8;
    $curso_id = isset($_GET['curso_id']) && $_GET['curso_id'] !== '' ? intval($_GET['curso_id']) : null;
    
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
                d.nacionalidad,
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
    
    if ($nacionalidad !== null) {
        $conditions[] = "d.nacionalidad = :nacionalidad";
        $params[':nacionalidad'] = $nacionalidad;
    }
    
    // Aplicar búsqueda si se proporciona
    if ($search !== null) {
        $conditions[] = "(d.id_docente LIKE :search OR 
                         d.cedula LIKE :search OR 
                         CONCAT(d.primer_nombre, ' ', COALESCE(d.segundo_nombre, '')) LIKE :search OR 
                         CONCAT(d.primer_apellido, ' ', COALESCE(d.segundo_apellido, '')) LIKE :search OR 
                         d.primer_nombre LIKE :search OR 
                         d.segundo_nombre LIKE :search OR 
                         d.primer_apellido LIKE :search OR 
                         d.segundo_apellido LIKE :search OR 
                         d.correo LIKE :search)";
        $params[':search'] = "%" . $search . "%";
    }
    
    // Filtro adicional si se busca asignar a un curso en específico
    if ($curso_id !== null) {
        require_once '../Curso/managerCurso.php';
        $cursoManager = new Curso();
        $curso = $cursoManager->obtenerCursoPorId($curso_id);
        
        if (!isset($curso['error'])) {
            $fechaInicio = $curso['fecha_inicio'];
            $fechaFin = $curso['fecha_fin'];
            $turno = $curso['turno'];
            $horas = (float)$curso['horas_por_clase'];

            // Excluir docentes con conflictos de horario
            $conditions[] = "NOT EXISTS (
                SELECT 1 FROM tb_docente_curso dc2
                JOIN tb_curso c2 ON dc2.id_curso = c2.id_curso
                WHERE dc2.id_docente = d.id_docente
                AND c2.id_curso != :curso_id_check
                AND c2.id_estatusCurso IN (1, 2, 3)
                AND TRIM(LOWER(c2.turno)) = TRIM(LOWER(:turno_check))
                AND (
                    (:fecha_inicio_check IS NULL OR :fecha_fin_check IS NULL OR c2.fecha_inicio IS NULL OR c2.fecha_fin IS NULL)
                    OR (c2.fecha_inicio <= :fecha_fin_check AND c2.fecha_fin >= :fecha_inicio_check)
                )
            )";
            $params[':curso_id_check'] = $curso_id;
            $params[':turno_check'] = $turno;
            $params[':fecha_inicio_check'] = $fechaInicio;
            $params[':fecha_fin_check'] = $fechaFin;

            // Excluir docentes que excederían las 8 horas diarias
            $conditions[] = "(:horas_check + COALESCE((
                SELECT SUM(c3.horas_por_clase) 
                FROM tb_docente_curso dc3
                JOIN tb_curso c3 ON dc3.id_curso = c3.id_curso
                WHERE dc3.id_docente = d.id_docente
                AND c3.id_curso != :curso_id_check2
                AND c3.id_estatusCurso IN (1, 2, 3)
                AND (
                    (:fecha_inicio_check2 IS NULL OR :fecha_fin_check2 IS NULL OR c3.fecha_inicio IS NULL OR c3.fecha_fin IS NULL)
                    OR (c3.fecha_inicio <= :fecha_fin_check2 AND c3.fecha_fin >= :fecha_inicio_check2)
                )
            ), 0)) <= 8";
            $params[':horas_check'] = $horas;
            $params[':curso_id_check2'] = $curso_id;
            $params[':fecha_inicio_check2'] = $fechaInicio;
            $params[':fecha_fin_check2'] = $fechaFin;
        }
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
        if ($key === ':edad' || $key === ':curso_id_check' || $key === ':curso_id_check2') {
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
        if ($key === ':limit' || $key === ':offset' || $key === ':edad' || $key === ':curso_id_check' || $key === ':curso_id_check2') {
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
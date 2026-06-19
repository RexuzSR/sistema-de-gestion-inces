<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// api_cursos_completos.php
require_once '../../modelo/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Crear conexión
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Consulta optimizada para reportes PDF
    $query = "SELECT 
                c.id_curso,
                c.nombre_curso,
                c.max_participantes,
                c.num_inscritos,
                c.fecha_inicio,
                c.fecha_fin,
                ec.nombre_estatusCurso as estado,
                m.nombre_modalidad,
                c.turno,
                af.nombre_areaFormativa,
                se.nombre_sectoreconomico,
                am.nombre_ambito
              FROM tb_curso c
              LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
              LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
              LEFT JOIN tb_areaformativa af ON c.id_areaFormativa = af.id_areaFormativa
              LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
              LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
              WHERE c.id_estatusCurso IS NOT NULL
              ORDER BY c.nombre_curso";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $data = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Calcular cupos disponibles
        $cupos_disponibles = $row['max_participantes'] - $row['num_inscritos'];
        $porcentaje_ocupacion = $row['max_participantes'] > 0 
            ? round(($row['num_inscritos'] / $row['max_participantes']) * 100, 2)
            : 0;
        
        // Formatear fechas
        $fecha_inicio = !empty($row['fecha_inicio']) 
            ? date('d/m/Y', strtotime($row['fecha_inicio'])) 
            : 'No definida';
        
        $fecha_fin = !empty($row['fecha_fin']) 
            ? date('d/m/Y', strtotime($row['fecha_fin'])) 
            : 'No definida';
        
        // Determinar estado visual
        $estado_badge = '';
        switch($row['estado']) {
            case 'En proceso':
                $estado_badge = 'success';
                break;
            case 'Culminado':
                $estado_badge = 'danger';
                break;
            case 'En espera':
                $estado_badge = 'warning';
                break;
            case 'En pausa':
                $estado_badge = 'info';
                break;
            default:
                $estado_badge = 'secondary';
        }
        
        $data[] = [
            'id_curso' => (int)$row['id_curso'],
            'nombre_curso' => htmlspecialchars($row['nombre_curso']),
            'max_participantes' => (int)$row['max_participantes'],
            'num_inscritos' => (int)$row['num_inscritos'],
            'cupos_disponibles' => max(0, $cupos_disponibles),
            'porcentaje_ocupacion' => $porcentaje_ocupacion,
            'estado' => htmlspecialchars($row['estado']),
            'estado_badge' => $estado_badge,
            'modalidad' => htmlspecialchars($row['nombre_modalidad']),
            'turno' => htmlspecialchars($row['turno']),
            'area_formativa' => htmlspecialchars($row['nombre_areaFormativa']),
            'sector_economico' => htmlspecialchars($row['nombre_sectoreconomico']),
            'ambito' => htmlspecialchars($row['nombre_ambito']),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'duracion_dias' => !empty($row['fecha_inicio']) && !empty($row['fecha_fin'])
                ? ceil((strtotime($row['fecha_fin']) - strtotime($row['fecha_inicio'])) / (60 * 60 * 24))
                : 0
        ];
    }
    
    // Agregar estadísticas generales
    $estadisticas = [
        'total_cursos' => count($data),
        'total_inscritos' => array_sum(array_column($data, 'num_inscritos')),
        'total_cupos' => array_sum(array_column($data, 'max_participantes')),
        'cursos_en_proceso' => count(array_filter($data, function($curso) {
            return $curso['estado'] === 'En proceso';
        })),
        'cursos_culminados' => count(array_filter($data, function($curso) {
            return $curso['estado'] === 'Culminado';
        }))
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'estadisticas' => $estadisticas,
        'generado' => date('d/m/Y H:i:s')
    ]);
    
} catch (PDOException $e) {
    error_log("Error en api_cursos_completos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'data' => []
    ]);
} catch (Exception $e) {
    error_log("Error en api_cursos_completos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error del sistema',
        'data' => []
    ]);
}
<?php
require_once '../../modelo/conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    // Crear conexión
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Consulta para obtener información completa del curso
    $sql = "SELECT 
                c.id_curso,
                c.nombre_curso,
                c.fecha_inicio,
                c.fecha_fin,
                c.max_participantes,
                c.min_participantes,
                c.num_inscritos,
                c.turno,
                c.tipo_formacion,
                c.programa_formacion,
                c.horas_totales,
                c.horas_por_clase,
                m.nombre_modalidad,
                ec.nombre_estatusCurso,
                af.nombre_areaFormativa,
                se.nombre_sectoreconomico,
                am.nombre_ambito,
                st.nombre_subtipo
            FROM tb_curso c
            LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
            LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
            LEFT JOIN tb_areaformativa af ON c.id_areaFormativa = af.id_areaFormativa
            LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
            LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
            LEFT JOIN tb_subtipo st ON c.id_subtipo = st.id_subtipo
            WHERE c.id_curso = :id_curso";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_curso', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $cursoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cursoInfo) {
        // Formatear datos para el frontend
        $data = [
            'success' => true,
            'data' => [
                'nombre' => $cursoInfo['nombre_curso'] ?? '-',
                'programa_formacion' => $cursoInfo['programa_formacion'] ?? '-',
                'modalidad' => $cursoInfo['nombre_modalidad'] ?? '-',
                'turno' => $cursoInfo['turno'] ?? '-',
                'tipoFormacion' => $cursoInfo['tipo_formacion'] ?? '-',
                'areaFormativa' => $cursoInfo['nombre_areaFormativa'] ?? '-',
                'sectorEconomico' => $cursoInfo['nombre_sectoreconomico'] ?? '-',
                'ambito' => $cursoInfo['nombre_ambito'] ?? '-',
                'maxParticipantes' => $cursoInfo['max_participantes'] ?? '-',
                'numInscritos' => $cursoInfo['num_inscritos'] ?? 0,
                'estado' => $cursoInfo['nombre_estatusCurso'] ?? '-',
                'fechaInicio' => $cursoInfo['fecha_inicio'] ?? '-',
                'fechaCulminacion' => $cursoInfo['fecha_fin'] ?? '-'
            ]
        ];
    } else {
        $data = [
            'success' => false,
            'message' => 'Curso no encontrado'
        ];
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    error_log("Error en verCurso.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error del servidor'
    ]);
}
<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ====================================================================
// ─────────────────── TRAYECTORIA CURSOS ─────────────────────────────
// Archivo: api_reportes_cursos_trayectoria.php
// Se incluye desde api_reportes.php
// ====================================================================

function getTrayectoriaCursosResumen(PDO $conn): array {
    // Cursos únicos
    $totalUnicos = (int)$conn->query("SELECT COUNT(DISTINCT nombre_curso) FROM tb_curso WHERE id_curso != 1")->fetchColumn();
    // Ejecuciones totales
    $totalEjecuciones = (int)$conn->query("SELECT COUNT(*) FROM tb_curso WHERE id_curso != 1")->fetchColumn();
    // Por estatus de las ejecuciones
    $stmt = $conn->query("
        SELECT 
            SUM(CASE WHEN id_estatusCurso = 1 THEN 1 ELSE 0 END) as en_espera,
            SUM(CASE WHEN id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN id_estatusCurso = 5 THEN 1 ELSE 0 END) as culminados
        FROM tb_curso
        WHERE id_curso != 1
    ");
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $res['total'] = $totalUnicos;
    $res['total_ejecuciones'] = $totalEjecuciones;
    return $res;
}

function getTrayectoriaCursos(PDO $conn): array {
    $stmt = $conn->query("
        SELECT 
            MIN(c.id_curso) as id_curso,
            GROUP_CONCAT(DISTINCT c.id_curso SEPARATOR ',') as todos_id_curso,
            c.nombre_curso,
            GROUP_CONCAT(c.preimpreso SEPARATOR ', ') as preimpresos,
            COUNT(*) as total_reinicios,
            MIN(c.fecha_inicio) as fecha_minima,
            MAX(c.fecha_fin) as fecha_maxima,
            SUM(c.num_inscritos) as total_inscritos,
            ROUND(AVG(c.num_inscritos), 1) as promedio_inscritos,
            (SELECT m2.nombre_modalidad 
             FROM tb_curso c2 
             LEFT JOIN tb_modalidad m2 ON m2.id_modalidad = c2.id_modalidad 
             WHERE c2.nombre_curso = c.nombre_curso 
             ORDER BY c2.fecha_inicio DESC LIMIT 1) as nombre_modalidad,
            (SELECT a2.nombre_ambito 
             FROM tb_curso c3 
             LEFT JOIN tb_ambito a2 ON a2.id_ambito = c3.id_ambito 
             WHERE c3.nombre_curso = c.nombre_curso 
             ORDER BY c3.fecha_inicio DESC LIMIT 1) as nombre_ambito,
            CASE 
                WHEN SUM(CASE WHEN c.id_estatusCurso = 3 THEN 1 ELSE 0 END) > 0 THEN 'En proceso'
                WHEN SUM(CASE WHEN c.id_estatusCurso = 1 THEN 1 ELSE 0 END) > 0 THEN 'En espera'
                ELSE 'Culminado'
            END as estatus
        FROM tb_curso c
        WHERE c.id_curso != 1
        GROUP BY c.nombre_curso
        ORDER BY total_reinicios DESC, c.nombre_curso ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDetalleCursoTrayectoria(PDO $conn, string $nombreCurso): array {
    $nombreCurso = trim($nombreCurso);
    
    // 1. Obtener todas las ejecuciones (lapsos/reinicios) de este curso
    $stmt = $conn->prepare("
        SELECT 
            c.id_curso,
            c.preimpreso,
            c.nombre_curso,
            c.turno,
            c.fecha_inicio,
            c.fecha_fin,
            c.horas_totales,
            c.num_inscritos,
            c.max_participantes,
            m.nombre_modalidad,
            e.nombre_estatusCurso as estatus,
            a.nombre_ambito,
            af.nombre_areaFormativa,
            se.nombre_sectoreconomico,
            st.nombre_subtipo,
            (SELECT CONCAT(d.primer_nombre, ' ', d.primer_apellido) 
             FROM tb_docente_curso dc 
             JOIN tb_docente d ON d.id_docente = dc.id_docente 
             WHERE dc.id_curso = c.id_curso LIMIT 1) as docente
        FROM tb_curso c
        LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa = c.id_areaFormativa
        LEFT JOIN tb_sectoreconomico se ON se.id_sectoreconomico = c.id_sectoreconomico
        LEFT JOIN tb_subtipo st ON st.id_subtipo = c.id_subtipo
        WHERE TRIM(c.nombre_curso) = :nombre_curso AND c.id_curso != 1
        ORDER BY c.fecha_inicio ASC
    ");
    $stmt->execute([':nombre_curso' => $nombreCurso]);
    $ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($ejecuciones)) return ['error' => 'Curso no encontrado'];

    // 2. Para cada ejecución, obtener su lista de participantes
    foreach ($ejecuciones as $key => $ejecucion) {
        $stmtP = $conn->prepare("
            SELECT 
                p.cedula,
                p.primer_nombre,
                p.primer_apellido,
                p.genero,
                p.nacionalidad,
                p.id_discapacidad,
                pc.fecha_inscripcion,
                pc.estatus_participante
            FROM tb_participante_curso pc
            JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE pc.id_curso = :id_curso
            ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
        ");
        $stmtP->execute([':id_curso' => $ejecucion['id_curso']]);
        $ejecuciones[$key]['participantes'] = $stmtP->fetchAll(PDO::FETCH_ASSOC);
    }

    return [
        'nombre_curso' => $nombreCurso,
        'ejecuciones' => $ejecuciones
    ];
}

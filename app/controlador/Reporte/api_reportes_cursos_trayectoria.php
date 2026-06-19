<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ====================================================================
// ─────────────────── TRAYECTORIA CURSOS ─────────────────────────────
// Archivo: api_reportes_cursos_trayectoria.php
// Se incluye desde api_reportes.php
// ====================================================================

function getTrayectoriaCursosResumen(PDO $conn): array {
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN id_estatusCurso = 1 THEN 1 ELSE 0 END) as en_espera,
            SUM(CASE WHEN id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN id_estatusCurso = 5 THEN 1 ELSE 0 END) as culminados
        FROM tb_curso
    ");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTrayectoriaCursos(PDO $conn): array {
    $stmt = $conn->query("
        SELECT 
            c.id_curso,
            c.nombre_curso,
            m.nombre_modalidad,
            c.turno,
            c.fecha_inicio,
            c.fecha_fin,
            c.num_inscritos,
            c.max_participantes,
            e.nombre_estatusCurso as estatus,
            c.id_estatusCurso,
            a.nombre_ambito
        FROM tb_curso c
        LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        ORDER BY c.fecha_inicio DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDetalleCursoTrayectoria(PDO $conn, int $idCurso): array {
    // ── Datos básicos del curso ─────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT 
            c.id_curso,
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
        WHERE c.id_curso = :id_curso
        LIMIT 1
    ");
    $stmt->execute([':id_curso' => $idCurso]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) return ['error' => 'Curso no encontrado'];

    // ── Lista de participantes ──────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT 
            p.cedula,
            p.primer_nombre,
            p.primer_apellido,
            pc.fecha_inscripcion,
            pc.estatus_participante
        FROM tb_participante_curso pc
        JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE pc.id_curso = :id_curso
        ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    $stmt->execute([':id_curso' => $idCurso]);
    $curso['participantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $curso;
}

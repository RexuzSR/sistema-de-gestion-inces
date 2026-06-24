<?php
/**
 * ============================================================
 *  datos_participantes.php
 *  Lógica de base de datos — Panel Participantes
 *  Incluido desde: app/vista/inicio.php
 *  Requiere: $conn (PDO) ya inicializado
 * ============================================================
 */

// ── Distribución por estado ──
$partEstado = $conn->query("
    SELECT
        CASE WHEN estado = 'Inactivo' THEN 'Sin actividad'
             ELSE estado END AS estado,
        COUNT(*) t
    FROM tb_participante
    GROUP BY CASE WHEN estado = 'Inactivo' THEN 'Sin actividad'
                  ELSE estado END
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por género ──
$partGenero = $conn->query("
    SELECT genero, COUNT(*) t
    FROM tb_participante
    GROUP BY genero
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por grado de institución ──
$partGrado = $conn->query("
    SELECT grado_institucion, COUNT(*) t
    FROM tb_participante
    GROUP BY grado_institucion
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Últimos 5 participantes registrados ──
$ultimosPart = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, estado, genero,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante
    ORDER BY fecha_registro DESC, id_participante DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ── KPI: Disponibles sin asignar ──
$kDisponibles = IndicadorHelper::getParticipantesDisponiblesSinAsignacion($conn);

// ── KPI: En formación ──
$kPctForm = IndicadorHelper::getParticipantesEnFormacion($conn);

// ── KPI: Inactivos (incluye "En sistema") ──
$kInactivos = (function ($c) {
    $tot   = $c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
    $inact = $c->query("SELECT COUNT(*) FROM tb_participante p WHERE p.estado IN ('Inactivo','En sistema')")->fetchColumn();
    return [
        'inactivos' => $inact,
        'total'     => $tot,
        'pct'       => $tot > 0 ? round($inact / $tot * 100, 1) : 0,
    ];
})($conn);

// ── KPI: Índice de asignación ──
$kAsignacion = IndicadorHelper::getAsignacionEfectiva($conn);
$kAsignacion['asignados'] = (int)$conn->query("SELECT COUNT(*) FROM tb_participante p WHERE p.estado = 'Asignado'")->fetchColumn();
$kAsignacion['en_formacion'] = $kPctForm['en_formacion'];

// ── KPI: Promedio de inscritos por curso activo ──
$kPromedio = $conn->query("
    SELECT COUNT(*) total_cursos,
           COALESCE(ROUND(AVG(num_inscritos), 1), 0) promedio
    FROM tb_curso
    WHERE id_estatusCurso IN (1,2,3,4)
")->fetch(PDO::FETCH_ASSOC);

// ── Distribución de estados (para gráfica resumen) ──
$distEstados = $conn->query("
    SELECT
        CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
             ELSE estado END AS estado,
        COUNT(*) total
    FROM tb_participante
    GROUP BY CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
                  ELSE estado END
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Tabla: disponibles sin asignación (máx. 15) ──
$partDisponibles = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero,
           grado_institucion, fecha_registro
    FROM tb_participante
    WHERE estado = 'Disponible'
    ORDER BY fecha_registro DESC
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);

// ── Datos mensuales (año actual) — para mini chart del resumen ──
$partPorMes = (function ($c) {
    $rows = $c->query("
        SELECT MONTH(fecha_registro) mes, COUNT(*) t
        FROM tb_participante
        WHERE YEAR(fecha_registro) = YEAR(CURDATE())
        GROUP BY MONTH(fecha_registro)
        ORDER BY mes
    ")->fetchAll(PDO::FETCH_ASSOC);
    $data = array_fill(0, 12, 0);
    foreach ($rows as $r) $data[(int)$r['mes'] - 1] = (int)$r['t'];
    return $data;
})($conn);

// ── Cards seleccionables ──
$cardDisponibles = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion, estado,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante
    WHERE estado = 'Disponible'
    ORDER BY fecha_registro DESC
")->fetchAll(PDO::FETCH_ASSOC);

$cardFormacion = $conn->query(" 
    SELECT p.primer_nombre, p.primer_apellido, p.cedula, p.genero,
           p.grado_institucion,
           TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
           GROUP_CONCAT(c.nombre_curso SEPARATOR ', ') AS cursos
    FROM tb_participante p
    LEFT JOIN tb_participante_curso pc ON pc.id_participante = p.id_participante
    LEFT JOIN tb_curso c
           ON pc.id_curso = c.id_curso
          AND pc.estatus_participante IN ('En formación', 'Pendiente')
    WHERE LOWER(p.estado) LIKE '%en form%'
    GROUP BY p.id_participante
    ORDER BY p.primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

$cardAsignados = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante
    WHERE estado = 'Asignado'
    ORDER BY primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

$cardInactivos = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion,
           estado,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante
    WHERE estado IN ('Inactivo','En sistema','Diferido')
    ORDER BY primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

// ── Semáforos específicos de participantes ──
$promPct  = $kPromedio['promedio'];
$s_disp   = IndicadorHelper::evaluarSemaforo('US_PART_DISP', $kDisponibles['pct'])['alerta'];
$s_form   = IndicadorHelper::evaluarSemaforo('AC_PART_FORMACION', $kPctForm['pct'])['alerta'];
$s_asig   = IndicadorHelper::evaluarSemaforo('PI_ASIG_EFECTIVA', $kAsignacion['pct'])['alerta'];
$s_inact  = sem($kInactivos['pct'], 20, 35, true);
$s_prom   = IndicadorHelper::evaluarSemaforo('PI_PROM_PARTICIPANTES', $promPct)['alerta'];

// ── JSON para JS (cards seleccionables) ──
$jsCardDisponibles = json_encode($cardDisponibles, JSON_UNESCAPED_UNICODE);
$jsCardFormacion   = json_encode($cardFormacion,   JSON_UNESCAPED_UNICODE);
$jsCardAsignados   = json_encode($cardAsignados,   JSON_UNESCAPED_UNICODE);
$jsCardInactivos   = json_encode($cardInactivos,   JSON_UNESCAPED_UNICODE);

/* ════════════════════════════════════════════════════════════
   AÑADIR AL FINAL de datos_participantes.php
   Participantes formados — con al menos un curso Aprobado
   en el año actual (filtrable por año en el futuro)
════════════════════════════════════════════════════════════ */

// ── KPI: Participantes formados en el año actual ──
$anioFormados = (int)date('Y');   // año actual; fácil de convertir en parámetro

$cardFormados = $conn->prepare("
    SELECT
        p.id_participante,
        p.primer_nombre,
        p.primer_apellido,
        p.cedula,
        p.genero,
        p.grado_institucion,
        TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
        p.fecha_registro,
        p.estado,
        COUNT(DISTINCT pc.id_curso)      AS total_cursos_aprobados,
        GROUP_CONCAT(
            c.nombre_curso
            ORDER BY pc.FechaFin DESC
            SEPARATOR ' · '
        )                                AS cursos_aprobados,
        MAX(pc.FechaFin)                 AS ultimo_curso_fecha
    FROM tb_participante p
    JOIN tb_participante_curso pc
        ON pc.id_participante = p.id_participante
        AND pc.estatus_participante = 'Aprobado'
        AND YEAR(pc.FechaFin) = :anio
    JOIN tb_curso c
        ON c.id_curso = pc.id_curso
    GROUP BY
        p.id_participante, p.primer_nombre, p.primer_apellido,
        p.cedula, p.genero, p.grado_institucion,
        p.fecha_nacimiento, p.fecha_registro, p.estado
    ORDER BY ultimo_curso_fecha DESC, p.primer_apellido ASC
");
$cardFormados->execute([':anio' => $anioFormados]);
$cardFormados = $cardFormados->fetchAll(PDO::FETCH_ASSOC);

// ── KPI resumen ──
$kFormados = [
    'total'          => count($cardFormados),
    'anio'           => $anioFormados,
    'total_cursos'   => array_sum(array_column($cardFormados, 'total_cursos_aprobados')),
];

// ── Semáforo: proporción de formados vs total de participantes ──
$pctFormados  = $kDisponibles['total'] > 0
    ? round($kFormados['total'] / $kDisponibles['total'] * 100, 1)
    : 0;
$s_formados   = sem($pctFormados, 30, 59);   // <30% rojo · 30-59% amarillo · ≥60% verde

// ── JSON para JS ──
$jsCardFormados = json_encode($cardFormados, JSON_UNESCAPED_UNICODE);
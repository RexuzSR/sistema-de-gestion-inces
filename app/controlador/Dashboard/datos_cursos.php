<?php
/**
 * ============================================================
 *  datos_cursos.php
 *  Lógica de base de datos — Panel Cursos
 *  Incluido desde: app/vista/inicio.php
 *  Requiere: $conn (PDO) ya inicializado
 * ============================================================
 */

// ── Distribución por estatus ──
$cursosEstatus = $conn->query("
    SELECT e.nombre_estatusCurso estatus, COUNT(*) t
    FROM tb_curso c
    JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
    WHERE c.id_curso != 1
    GROUP BY e.id_estatusCurso, e.nombre_estatusCurso
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por modalidad ──
$cursosModalidad = $conn->query("
    SELECT m.nombre_modalidad modalidad, COUNT(*) t
    FROM tb_curso c
    JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
    WHERE c.id_curso != 1
    GROUP BY m.id_modalidad, m.nombre_modalidad
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por turno ──
$cursosTurno = $conn->query("
    SELECT turno, COUNT(*) t
    FROM tb_curso
    WHERE id_curso != 1
    GROUP BY turno
")->fetchAll(PDO::FETCH_ASSOC);

// ── Top 5 cursos del mes actual con tendencia + género ──
$topCursosMesActual = $conn->query("
    SELECT
        c.nombre_curso,
        c.max_participantes,
        c.horas_totales,
        m.nombre_modalidad,
        e.nombre_estatusCurso,
        c.num_inscritos,
        (c.max_participantes - c.num_inscritos) AS cupos_disp,
        ROUND(c.num_inscritos/NULLIF(c.max_participantes,0)*100,1) AS uso_pct,
        COUNT(pc.id_participante) AS inscritos_mes,
        SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
        SUM(CASE WHEN p.genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino
    FROM tb_curso c
    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
    JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
    LEFT JOIN tb_participante_curso pc
        ON pc.id_curso = c.id_curso
        AND MONTH(pc.fecha_inscripcion) = MONTH(CURDATE())
        AND YEAR(pc.fecha_inscripcion)  = YEAR(CURDATE())
    LEFT JOIN tb_participante p ON pc.id_participante = p.id_participante
    WHERE c.id_curso != 1
    GROUP BY c.id_curso, c.nombre_curso, c.max_participantes, c.horas_totales,
             m.nombre_modalidad, e.nombre_estatusCurso, c.num_inscritos
    ORDER BY inscritos_mes DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ── Tendencia: comparar top 5 con mes anterior ──
$topCursosMesAnterior = [];
if (!empty($topCursosMesActual)) {
    $nombresTop   = array_column($topCursosMesActual, 'nombre_curso');
    $inParams     = implode(',', array_fill(0, count($nombresTop), '?'));
    $stmtAnterior = $conn->prepare("
        SELECT c.nombre_curso, COUNT(pc.id_participante) AS inscritos_anterior
        FROM tb_curso c
        LEFT JOIN tb_participante_curso pc
            ON pc.id_curso = c.id_curso
            AND MONTH(pc.fecha_inscripcion) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
            AND YEAR(pc.fecha_inscripcion)  = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
        WHERE c.nombre_curso IN ($inParams)
        GROUP BY c.nombre_curso
    ");
    $stmtAnterior->execute($nombresTop);
    foreach ($stmtAnterior->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $topCursosMesAnterior[$row['nombre_curso']] = (int)$row['inscritos_anterior'];
    }
}

foreach ($topCursosMesActual as &$tc) {
    $anterior = $topCursosMesAnterior[$tc['nombre_curso']] ?? 0;
    $actual   = (int)$tc['inscritos_mes'];
    if ($anterior === 0 && $actual === 0) { $tc['tendencia'] = 'igual'; $tc['tendencia_diff'] = 0; }
    elseif ($actual > $anterior)          { $tc['tendencia'] = 'sube';  $tc['tendencia_diff'] = $actual - $anterior; }
    elseif ($actual < $anterior)          { $tc['tendencia'] = 'baja';  $tc['tendencia_diff'] = $anterior - $actual; }
    else                                  { $tc['tendencia'] = 'igual'; $tc['tendencia_diff'] = 0; }
}
unset($tc);

// ── Detalle de todos los cursos activos con género ──
$cursosDetalle = $conn->query("
    SELECT
        c.nombre_curso,
        c.num_inscritos,
        c.max_participantes,
        (c.max_participantes - c.num_inscritos) AS cupos_disp,
        ROUND(c.num_inscritos/NULLIF(c.max_participantes,0)*100,1) uso_pct,
        e.nombre_estatusCurso,
        COALESCE(SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END),0) AS masculino,
        COALESCE(SUM(CASE WHEN p.genero = 'Femenino'  THEN 1 ELSE 0 END),0) AS femenino
    FROM tb_curso c
    JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
    LEFT JOIN tb_participante_curso pc ON pc.id_curso = c.id_curso
    LEFT JOIN tb_participante p ON pc.id_participante = p.id_participante
    WHERE c.id_estatusCurso IN (1,2,3,4)
    GROUP BY c.id_curso, c.nombre_curso, c.num_inscritos,
             c.max_participantes, e.nombre_estatusCurso
    ORDER BY uso_pct DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── KPI: Utilización de cupos ──
$kUtilizacion = (function ($c) {
    $r = $c->query("
        SELECT COALESCE(SUM(num_inscritos),0) i,
               COALESCE(SUM(max_participantes),1) q
        FROM tb_curso
        WHERE id_estatusCurso IN (1,2,3,4)
    ")->fetch(PDO::FETCH_ASSOC);
    $p = $r['q'] > 0 ? round($r['i'] / $r['q'] * 100, 1) : 0;
    return ['inscritos' => $r['i'], 'cupos' => $r['q'], 'pct' => $p];
})($conn);

// ── KPI: Cupos perdidos ──
$kCuposPerdidos = [
    'cupos_vacios'    => max(0, $kUtilizacion['cupos'] - $kUtilizacion['inscritos']),
    'total_cupos'     => $kUtilizacion['cupos'],
    'pct_desperdicio' => $kUtilizacion['cupos'] > 0
        ? round((($kUtilizacion['cupos'] - $kUtilizacion['inscritos']) / $kUtilizacion['cupos']) * 100, 1)
        : 0,
];

// ── KPI: Promedio de inscritos por curso activo ──
$kPromedio = $conn->query("
    SELECT COUNT(*) total_cursos,
           COALESCE(ROUND(AVG(num_inscritos),1),0) promedio
    FROM tb_curso
    WHERE id_estatusCurso IN (1,2,3,4)
")->fetch(PDO::FETCH_ASSOC);

// ── KPI: Programas ejecutados (iniciados) ──
$kEjecucion = ['ejecutados' => 0, 'planificados' => 0, 'pct' => 0, 'fuente' => 'cursos'];
try {
    $metasCursos = $conn->query("
        SELECT COUNT(*) AS total,
               COALESCE(ROUND(AVG(porcentaje_cumplimiento),1),0) AS pct_promedio,
               SUM(CASE WHEN estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END) AS cumplidas
        FROM tb_metas
        WHERE estado = 'Activa'
          AND QUARTER(fecha_inicio) = QUARTER(CURDATE())
          AND YEAR(fecha_inicio)    = YEAR(CURDATE())
          AND (LOWER(tipo_objetivo) LIKE '%curso%'
            OR LOWER(tipo_objetivo) LIKE '%formac%'
            OR LOWER(tipo_objetivo) LIKE '%program%')
    ")->fetch(PDO::FETCH_ASSOC);
    if ((int)$metasCursos['total'] > 0) {
        $kEjecucion = [
            'ejecutados'   => (int)$metasCursos['cumplidas'],
            'planificados' => (int)$metasCursos['total'],
            'pct'          => (float)$metasCursos['pct_promedio'],
            'fuente'       => 'metas',
        ];
    } else {
        $planif = (int)$conn->query("
            SELECT COUNT(*) FROM tb_curso
            WHERE id_curso != 1
              AND QUARTER(fecha_inicio) = QUARTER(CURDATE())
              AND YEAR(fecha_inicio)    = YEAR(CURDATE())
        ")->fetchColumn();
        $ejecut = (int)$conn->query("
            SELECT COUNT(*) FROM tb_curso c
            JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
            WHERE c.id_curso != 1
              AND QUARTER(c.fecha_inicio) = QUARTER(CURDATE())
              AND YEAR(c.fecha_inicio)    = YEAR(CURDATE())
              AND (LOWER(e.nombre_estatusCurso) LIKE '%finaliz%'
                OR LOWER(e.nombre_estatusCurso) LIKE '%complet%'
                OR LOWER(e.nombre_estatusCurso) LIKE '%en curso%')
        ")->fetchColumn();
        $kEjecucion = [
            'ejecutados'   => $ejecut,
            'planificados' => $planif,
            'pct'          => $planif > 0 ? round($ejecut / $planif * 100, 1) : 0,
            'fuente'       => 'cursos',
        ];
    }
} catch(Exception $e) {}

// ── Años disponibles para filtro ──
$aniosDisponibles = [];
try {
    $aniosDisponibles = array_column($conn->query("
        SELECT DISTINCT YEAR(fecha_inicio) AS anio
        FROM tb_curso
        WHERE id_curso != 1 AND fecha_inicio IS NOT NULL
        ORDER BY anio DESC
    ")->fetchAll(PDO::FETCH_ASSOC), 'anio');
} catch(Exception $e) {}
if (empty($aniosDisponibles)) $aniosDisponibles = [(int)date('Y')];

// ── Cursos diferidos — histórico para filtrar en JS ──
$cursosDiferidos = [];
try {
    $cursosDiferidos = $conn->query("
        SELECT
            c.nombre_curso,
            c.fecha_inicio,
            c.fecha_fin,
            e.nombre_estatusCurso,
            COALESCE(m.nombre_modalidad,'—') AS nombre_modalidad,
            c.num_inscritos,
            c.max_participantes,
            YEAR(c.fecha_inicio)    AS anio,
            MONTH(c.fecha_inicio)   AS mes,
            QUARTER(c.fecha_inicio) AS trimestre
        FROM tb_curso c
        JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
        LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
        WHERE c.id_curso != 1
          AND LOWER(e.nombre_estatusCurso) LIKE '%dif%'
        ORDER BY c.fecha_inicio DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// ── Iniciados por mes/trimestre/año — histórico para filtrar en JS ──
$iniciados_raw = [];
try {
    $iniciados_raw = $conn->query("
        SELECT
            YEAR(c.fecha_inicio)     AS anio,
            MONTH(c.fecha_inicio)    AS mes,
            QUARTER(c.fecha_inicio)  AS trimestre,
            COUNT(*)                 AS planificados,
            SUM(CASE
                WHEN LOWER(e.nombre_estatusCurso) LIKE '%finaliz%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%complet%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%en curso%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%inici%'
                THEN 1 ELSE 0 END) AS iniciados
        FROM tb_curso c
        JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
        WHERE c.id_curso != 1 AND c.fecha_inicio IS NOT NULL
        GROUP BY YEAR(c.fecha_inicio), MONTH(c.fecha_inicio), QUARTER(c.fecha_inicio)
        ORDER BY anio DESC, mes DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// ── [NUEVO] Detalle histórico de todos los cursos para cards clickeables ──
// Incluye estado para distinguir: pendiente / en formación / diferido
$cursosHistoricoDetalle = [];
try {
    $cursosHistoricoDetalle = $conn->query("
        SELECT
            c.id_curso,
            c.nombre_curso,
            c.fecha_inicio,
            c.fecha_fin,
            c.num_inscritos,
            c.max_participantes,
            c.horas_totales,
            e.nombre_estatusCurso,
            COALESCE(m.nombre_modalidad,'—') AS nombre_modalidad,
            COALESCE(a.nombre_ambito,'—')    AS nombre_ambito,
            YEAR(c.fecha_inicio)             AS anio,
            MONTH(c.fecha_inicio)            AS mes,
            QUARTER(c.fecha_inicio)          AS trimestre,
            CASE
                WHEN LOWER(e.nombre_estatusCurso) LIKE '%dif%'
                    THEN 'diferido'
                WHEN LOWER(e.nombre_estatusCurso) LIKE '%finaliz%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%complet%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%en curso%'
                  OR LOWER(e.nombre_estatusCurso) LIKE '%inici%'
                    THEN 'iniciado'
                ELSE 'pendiente'
            END AS tipo_ejecucion
        FROM tb_curso c
        JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
        LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1
          AND c.fecha_inicio IS NOT NULL
        ORDER BY c.fecha_inicio DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// ── [NUEVO] Detalle histórico de formaciones por ámbito para filtrar en JS ──
// Carga todos los cursos con ámbito y periodo para filtrado dinámico en cliente
$ambitoHistoricoJS = [];
try {
    $ambitoHistoricoJS = $conn->query("
        SELECT
            COALESCE(a.nombre_ambito,'Sin ámbito') AS ambito,
            COUNT(*)                               AS total,
            YEAR(c.fecha_inicio)                   AS anio,
            MONTH(c.fecha_inicio)                  AS mes,
            QUARTER(c.fecha_inicio)                AS trimestre
        FROM tb_curso c
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1
          AND c.fecha_inicio IS NOT NULL
        GROUP BY a.id_ambito, a.nombre_ambito,
                 YEAR(c.fecha_inicio), MONTH(c.fecha_inicio), QUARTER(c.fecha_inicio)
        ORDER BY anio DESC, mes DESC, total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// ── Formaciones por ámbito — mes y trimestre actual (para render PHP inicial) ──
$formacionesPorAmbitoMes  = [];
$formacionesPorAmbitoTrim = [];
$totalFormMes             = 0;
$totalFormTrim            = 0;
try {
    $rowsMes = $conn->query("
        SELECT COALESCE(a.nombre_ambito,'Sin ámbito') AS ambito, COUNT(*) AS total
        FROM tb_curso c
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1
          AND MONTH(c.fecha_inicio) = MONTH(CURDATE())
          AND YEAR(c.fecha_inicio)  = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $formacionesPorAmbitoMes = $rowsMes;
    $totalFormMes            = array_sum(array_column($rowsMes, 'total'));

    $rowsTrim = $conn->query("
        SELECT COALESCE(a.nombre_ambito,'Sin ámbito') AS ambito, COUNT(*) AS total
        FROM tb_curso c
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1
          AND QUARTER(c.fecha_inicio) = QUARTER(CURDATE())
          AND YEAR(c.fecha_inicio)    = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $formacionesPorAmbitoTrim = $rowsTrim;
    $totalFormTrim            = array_sum(array_column($rowsTrim, 'total'));
} catch(Exception $e) {}

// ── Ámbito más solicitado por mes ──
$ambitoSolicitadoMes = [];
try {
    $ambitoSolicitadoMes = $conn->query("
        SELECT COALESCE(a.nombre_ambito,'Sin ámbito') AS ambito,
               COUNT(pc.id_participante) AS inscripciones,
               COUNT(DISTINCT pc.id_curso) AS cursos_activos
        FROM tb_participante_curso pc
        JOIN tb_curso c ON pc.id_curso = c.id_curso
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE MONTH(pc.fecha_inscripcion) = MONTH(CURDATE())
          AND YEAR(pc.fecha_inscripcion)  = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY inscripciones DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}
$totalInscMes = array_sum(array_column($ambitoSolicitadoMes, 'inscripciones'));

// ── Datos mensuales — para mini chart del resumen ──
$cursosPorMes = (function ($c) {
    $rows = $c->query("
        SELECT MONTH(fecha_inicio) mes, COUNT(*) t
        FROM tb_curso
        WHERE YEAR(fecha_inicio) = YEAR(CURDATE()) AND id_curso != 1
        GROUP BY MONTH(fecha_inicio)
        ORDER BY mes
    ")->fetchAll(PDO::FETCH_ASSOC);
    $data = array_fill(0, 12, 0);
    foreach ($rows as $r) $data[(int)$r['mes'] - 1] = (int)$r['t'];
    return $data;
})($conn);

// ── Semáforos específicos de cursos ──
$s_util      = sem($kUtilizacion['pct'], 70, 89);
$s_ejecucion = $kEjecucion['pct'] >= 85 ? 'verde' : ($kEjecucion['pct'] >= 75 ? 'amarillo' : 'rojo');
$s_formMes   = $totalFormMes  > 15 ? 'verde' : ($totalFormMes  >= 10 ? 'amarillo' : 'rojo');
$s_formTrim  = $totalFormTrim > 45 ? 'verde' : ($totalFormTrim >= 30 ? 'amarillo' : 'rojo');

// ── Auxiliares de período para los selectores ──
$mesActualNum       = (int)date('n');
$anioActualNum      = (int)date('Y');
$trimestreActualNum = (int)ceil($mesActualNum / 3);
$semestreActualNum  = $mesActualNum <= 6 ? 1 : 2;

// ── JSON para JS ──
$jsCursosDiferidos      = json_encode($cursosDiferidos,          JSON_UNESCAPED_UNICODE);
$jsIniciados            = json_encode($iniciados_raw,             JSON_UNESCAPED_UNICODE);
$jsAnios                = json_encode($aniosDisponibles,          JSON_UNESCAPED_UNICODE);
$jsMesesES              = json_encode(array_values($mesesES),     JSON_UNESCAPED_UNICODE);
$jsCursosHistorico      = json_encode($cursosHistoricoDetalle,    JSON_UNESCAPED_UNICODE);  // [NUEVO]
$jsAmbitoHistorico      = json_encode($ambitoHistoricoJS,         JSON_UNESCAPED_UNICODE);  // [NUEVO]
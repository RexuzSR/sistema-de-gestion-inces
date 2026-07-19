<?php
/**
 * ============================================================
 *  datos_metas.php  —  Panel Metas
 *  Requiere: $conn (PDO) ya inicializado
 *
 *  REGLA DE NEGOCIO:
 *  Las metas relevantes para todos los bloques de este panel son
 *  aquellas con estado = 'Activa' Y YEAR(fecha_inicio) = año actual.
 *  El campo estado_cumplimiento NUNCA se usa como filtro de exclusión;
 *  solo se usa para mostrar información (conteos, badges, etc.).
 * ============================================================
 */

$anioActual = (int)date('Y');

// ────────────────────────────────────────────────────────────
// SINCRONIZACIÓN DINÁMICA DE METAS ACTIVAS EN BASE DE DATOS
// Para asegurar que los KPIs y listados muestren conteos reales de estudiantes_formados
// ────────────────────────────────────────────────────────────
try {
    $realCountSql = "
        CASE 
            WHEN m.tipo_objetivo = 'Aprobados' THEN (
                SELECT COUNT(DISTINCT CONCAT(pc2.id_participante, '-', pc2.id_curso))
                FROM tb_participante_curso pc2
                INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                WHERE pc2.estatus_participante = 'Aprobado'
                  AND pc2.FechaFin BETWEEN m.fecha_inicio AND m.fecha_fin
                  AND (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito)
            )
            WHEN m.tipo_objetivo = 'Inscritos' THEN (
                SELECT COUNT(DISTINCT CONCAT(pc2.id_participante, '-', pc2.id_curso))
                FROM tb_participante_curso pc2
                INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                WHERE pc2.fecha_inscripcion BETWEEN m.fecha_inicio AND m.fecha_fin
                  AND (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito)
            )
            WHEN m.tipo_objetivo = 'Ingresados' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            WHEN m.tipo_objetivo = 'Masculino' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.genero = 'Masculino'
                  AND p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            WHEN m.tipo_objetivo = 'Femenino' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.genero = 'Femenino'
                  AND p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            ELSE 0
        END
    ";
    $stSync = $conn->prepare("
        UPDATE tb_metas m
        SET m.estudiantes_formados = {$realCountSql}
        WHERE m.estado = 'Activa'
          AND YEAR(m.fecha_inicio) = :anio
    ");
    $stSync->execute([':anio' => $anioActual]);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// KPI GENERAL — metas Activas del año actual
// Usado en la cabecera "Progresión de Metas Activas"
// ────────────────────────────────────────────────────────────
$kMetas = ['total_metas' => 0, 'cumplidas' => 0, 'promedio_pct' => 0];
try {
    $st = $conn->prepare("
        SELECT
            COUNT(*)                                                            AS total_metas,
            SUM(CASE WHEN estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END)  AS cumplidas,
            COALESCE(ROUND(AVG(porcentaje_cumplimiento), 1), 0)                 AS promedio_pct
        FROM tb_metas
        WHERE estado = 'Activa'
          AND YEAR(fecha_inicio) = :anio
    ");
    $st->execute([':anio' => $anioActual]);
    $kMetas = $st->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// PROGRESO GLOBAL — barra única del año
// Filtra: estado = 'Activa' + año actual
// ────────────────────────────────────────────────────────────
$progresoAnual = [
    'total_metas' => 0, 'cumplidas' => 0, 'en_progreso' => 0,
    'no_cumplidas' => 0, 'obj_total' => 0, 'real_total' => 0, 'pct_global' => 0
];
try {
    $st = $conn->prepare("
        SELECT
            COUNT(*)                                                                AS total_metas,
            SUM(CASE WHEN estado_cumplimiento = 'Cumplida'    THEN 1 ELSE 0 END)  AS cumplidas,
            SUM(CASE WHEN estado_cumplimiento = 'En progreso' THEN 1 ELSE 0 END)  AS en_progreso,
            SUM(CASE WHEN estado_cumplimiento = 'No cumplida' THEN 1 ELSE 0 END)  AS no_cumplidas,
            COALESCE(SUM(meta_objetivo), 0)                                         AS obj_total,
            COALESCE(SUM(LEAST(estudiantes_formados, meta_objetivo)), 0)            AS real_total,
            COALESCE(ROUND(
                SUM(LEAST(estudiantes_formados, meta_objetivo))
                / NULLIF(SUM(meta_objetivo), 0) * 100
            , 1), 0)                                                                AS pct_global
        FROM tb_metas
        WHERE estado = 'Activa'
          AND YEAR(fecha_inicio) = :anio
    ");
    $st->execute([':anio' => $anioActual]);
    $progresoAnual = $st->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// PROGRESO GLOBAL DESGLOSADO POR ÁMBITO (panel expandible)
// Filtra: estado = 'Activa' + año actual
// ────────────────────────────────────────────────────────────
$progresoAnualPorAmbito = [];
try {
    $st = $conn->prepare("
        SELECT
            COALESCE(a.nombre_ambito, 'Sin ámbito')                                AS ambito,
            COUNT(*)                                                                AS total_metas,
            SUM(m.meta_objetivo)                                                    AS obj_total,
            SUM(LEAST(m.estudiantes_formados, m.meta_objetivo))                    AS real_total,
            COALESCE(ROUND(
                SUM(LEAST(m.estudiantes_formados, m.meta_objetivo))
                / NULLIF(SUM(m.meta_objetivo), 0) * 100
            , 1), 0)                                                                AS pct_ambito,
            SUM(CASE WHEN m.estado_cumplimiento = 'Cumplida'    THEN 1 ELSE 0 END) AS cumplidas,
            SUM(CASE WHEN m.estado_cumplimiento = 'En progreso' THEN 1 ELSE 0 END) AS en_progreso
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
          AND YEAR(m.fecha_inicio) = :anio
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY pct_ambito DESC
    ");
    $st->execute([':anio' => $anioActual]);
    $progresoAnualPorAmbito = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// DETALLE INDIVIDUAL DE CADA META (cards del bloque Avance)
// Filtra: estado = 'Activa' + año actual
// estado_cumplimiento se expone para mostrar badge, NO para filtrar
// ────────────────────────────────────────────────────────────
$metasAnioDetalle = [];
try {
    $st = $conn->prepare("
        SELECT
            m.id_meta,
            m.tipo_objetivo,
            m.meta_objetivo,
            m.estudiantes_formados,
            m.porcentaje_cumplimiento,
            m.estado_cumplimiento,
            m.estado,
            m.periodo_tiempo,
            m.fecha_inicio,
            m.fecha_fin,
            MONTH(m.fecha_inicio)                                  AS mes_inicio,
            QUARTER(m.fecha_inicio)                                AS trimestre_inicio,
            COALESCE(a.id_ambito, 0)                               AS id_ambito,
            COALESCE(a.nombre_ambito, 'Sin ámbito')                AS nombre_ambito,
            LEAST(m.estudiantes_formados, m.meta_objetivo)         AS real_aportado
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
          AND YEAR(m.fecha_inicio) = :anio
        ORDER BY m.porcentaje_cumplimiento DESC
    ");
    $st->execute([':anio' => $anioActual]);
    $metasAnioDetalle = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// METAS ACTIVAS POR MES — para mini chart de tabs
// Filtra: estado = 'Activa' + año actual
// ────────────────────────────────────────────────────────────
$metasPorMes = array_fill(0, 12, 0);
try {
    $st = $conn->prepare("
        SELECT MONTH(fecha_inicio) AS mes, COUNT(*) AS t
        FROM tb_metas
        WHERE estado = 'Activa'
          AND fecha_inicio IS NOT NULL
          AND YEAR(fecha_inicio) = :anio
        GROUP BY MONTH(fecha_inicio)
        ORDER BY mes
    ");
    $st->execute([':anio' => $anioActual]);
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $metasPorMes[(int)$r['mes'] - 1] = (int)$r['t'];
    }
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// DETALLE DE METAS ACTIVAS — tabla del panel inferior
// Filtra: estado = 'Activa' + año actual
// ────────────────────────────────────────────────────────────
$metasDetalle = [];
try {
    $st = $conn->prepare("
        SELECT
            m.*,
            a.nombre_ambito,
            MONTH(m.fecha_inicio) AS mes_meta
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
          AND YEAR(m.fecha_inicio) = :anio
        ORDER BY m.porcentaje_cumplimiento DESC
    ");
    $st->execute([':anio' => $anioActual]);
    $metasDetalle = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback por si fecha_inicio no existe en versiones antiguas
    try {
        $st2 = $conn->prepare("
            SELECT m.*, a.nombre_ambito, NULL AS mes_meta
            FROM tb_metas m
            LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
            WHERE m.estado = 'Activa'
        ");
        $st2->execute();
        $metasDetalle = $st2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e2) {}
}

// ────────────────────────────────────────────────────────────
// CUMPLIMIENTO PROMEDIO POR ÁMBITO — para tab + chart
// Filtra: estado = 'Activa' + año actual
// ────────────────────────────────────────────────────────────
$cumplimientoAmbito = [];
try {
    $st = $conn->prepare("
        SELECT
            COALESCE(a.nombre_ambito, 'Sin ámbito')                               AS ambito,
            COUNT(*)                                                                AS total_metas,
            ROUND(AVG(m.porcentaje_cumplimiento), 1)                               AS pct_promedio,
            SUM(CASE WHEN m.estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END)   AS cumplidas
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
          AND YEAR(m.fecha_inicio) = :anio
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY pct_promedio DESC
    ");
    $st->execute([':anio' => $anioActual]);
    $cumplimientoAmbito = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// ────────────────────────────────────────────────────────────
// NUEVOS KPIs PARA EL TABULADOR METAS
// ────────────────────────────────────────────────────────────

// 1. Tasa de cumplimiento de metas por ámbitos (metas activas con ámbito asignado)
$tasaCumplimientoAmbitos = IndicadorHelper::getCumplimientoMetasAmbito($conn)['pct'];

// 2. Tasa de cumplimiento del objetivo de participantes anuales
$resAnual = IndicadorHelper::getCumplimientoParticipantesAnual($conn);
$tasaMetaAnual = $resAnual['pct'];
$metaAnualObj = $resAnual['objetivo'];
$metaAnualReal = $resAnual['real'];

// ────────────────────────────────────────────────────────────
// SEMÁFORO GENERAL de metas
// ────────────────────────────────────────────────────────────
$s_meta = IndicadorHelper::evaluarSemaforo('US_CUMP_METAS', min((float)($kMetas['promedio_pct'] ?? 0), 100.0))['alerta'];
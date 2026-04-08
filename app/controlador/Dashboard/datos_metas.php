<?php
/**
 * ============================================================
 *  datos_metas.php
 *  Lógica de base de datos — Panel Metas
 *  Incluido desde: app/vista/inicio.php
 *  Requiere: $conn (PDO) ya inicializado
 * ============================================================
 */

// ── KPI general de metas activas ──
$kMetas = $conn->query("
    SELECT
        COUNT(*) total_metas,
        SUM(CASE WHEN estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END) cumplidas,
        COALESCE(ROUND(AVG(porcentaje_cumplimiento), 1), 0) promedio_pct
    FROM tb_metas
    WHERE estado = 'Activa'
")->fetch(PDO::FETCH_ASSOC);

// ── Metas activas por mes (año actual) — para mini chart del resumen ──
$metasPorMes = array_fill(0, 12, 0);
try {
    foreach ($conn->query("
        SELECT MONTH(fecha_inicio) mes, COUNT(*) t
        FROM tb_metas
        WHERE estado = 'Activa' AND fecha_inicio IS NOT NULL
        GROUP BY MONTH(fecha_inicio)
        ORDER BY mes
    ")->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $metasPorMes[(int)$r['mes'] - 1] = (int)$r['t'];
    }
} catch(Exception $e) {}

// ── Detalle de metas activas (con mes para filtro por calendario) ──
$metasDetalle = [];
try {
    $metasDetalle = $conn->query("
        SELECT m.*, a.nombre_ambito, MONTH(m.fecha_inicio) AS mes_meta
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
        ORDER BY m.porcentaje_cumplimiento DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    // Fallback sin fecha_inicio (por si la columna no existe en versiones antiguas)
    try {
        $metasDetalle = $conn->query("
            SELECT m.*, a.nombre_ambito, NULL AS mes_meta
            FROM tb_metas m
            LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
            WHERE m.estado = 'Activa'
            ORDER BY m.porcentaje_cumplimiento DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e2) {}
}

// ── Cumplimiento promedio por ámbito ──
$cumplimientoAmbito = [];
try {
    $cumplimientoAmbito = $conn->query("
        SELECT
            COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito,
            COUNT(*) AS total_metas,
            ROUND(AVG(m.porcentaje_cumplimiento), 1) AS pct_promedio,
            SUM(CASE WHEN m.estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END) AS cumplidas
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY pct_promedio DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// ── Semáforo de metas (usado en sección resumen también) ──
$s_meta = sem($kMetas['promedio_pct'] ?? 0, 80, 95);
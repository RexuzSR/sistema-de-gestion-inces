<?php
/**
 * ============================================================
 *  datos_docentes.php
 *  Lógica de base de datos — Panel Docentes
 *  Incluido desde: app/vista/inicio.php
 *  Requiere: $conn (PDO) ya inicializado
 * ============================================================
 */

// ── Distribución por tipo de docente ──
$docTipo = $conn->query("
    SELECT tipo_docente tipo, COUNT(*) t
    FROM tb_docente
    GROUP BY tipo_docente
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por estado ──
$docEstado = $conn->query("
    SELECT estado_docente estado, COUNT(*) t
    FROM tb_docente
    GROUP BY estado_docente
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Distribución por género ──
$docGenero = $conn->query("
    SELECT genero, COUNT(*) t
    FROM tb_docente
    GROUP BY genero
")->fetchAll(PDO::FETCH_ASSOC);

// ── Listado primeros 10 (tabla principal) ──
$listaDoc = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula,
           tipo_docente, estado_docente, genero,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
    FROM tb_docente
    ORDER BY primer_apellido
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// ── Datos mensuales — para mini chart del resumen ──
$docentesPorMes = array_fill(0, 12, 0);
foreach (
    $conn->query("
        SELECT MONTH(dc.fecha_asignacion) AS mes, COUNT(DISTINCT d.id_docente) AS t
        FROM tb_docente d
        INNER JOIN tb_docente_curso dc ON d.id_docente = dc.id_docente
        WHERE YEAR(dc.fecha_asignacion) = YEAR(CURDATE())
        GROUP BY mes
        ORDER BY mes
    ")->fetchAll(PDO::FETCH_ASSOC)
    as $r
) {
    $docentesPorMes[(int)$r['mes'] - 1] = (int)$r['t'];
}
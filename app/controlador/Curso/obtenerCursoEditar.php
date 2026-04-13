<?php
/**
 * Respuesta JSON para el modal Editar Curso.
 * Si horas_por_clase en BD es null o inválido, se infiere desde horas_totales + calendario
 * (misma regla que assets/js/Curso/editarCurso.js).
 */
require_once 'managerCurso.php';

header('Content-Type: application/json');

/** @see assets/js/Curso/editarCurso.js FESTIVOS_2026 */
function contarDiasLectivosEditarCurso(string $fi, string $ff): int
{
    static $festivos = [
        '2026-01-01', '2026-01-06', '2026-02-16', '2026-02-17',
        '2026-04-02', '2026-04-03', '2026-04-19', '2026-05-01',
        '2026-06-24', '2026-07-05', '2026-07-24', '2026-10-12',
        '2026-12-24', '2026-12-25', '2026-12-31',
    ];
    try {
        $ini = new DateTimeImmutable($fi);
        $fin = new DateTimeImmutable($ff);
    } catch (Exception $e) {
        return 0;
    }
    if ($ini >= $fin) {
        return 0;
    }
    $d = 0;
    $cur = $ini;
    while ($cur <= $fin) {
        $w = (int) $cur->format('w');
        $ymd = $cur->format('Y-m-d');
        if ($w !== 0 && $w !== 6 && !in_array($ymd, $festivos, true)) {
            $d++;
        }
        $cur = $cur->modify('+1 day');
    }
    return $d;
}

function inferirHorasPorClaseDesdeTotales(?float $ht, ?string $fi, ?string $ff): ?float
{
    if ($ht === null || $ht <= 0 || !$fi || !$ff) {
        return null;
    }
    $dias = contarDiasLectivosEditarCurso($fi, $ff);
    if ($dias <= 0) {
        return null;
    }
    $ratio = $ht / $dias;
    if ($ratio >= 3.55 && $ratio <= 4.45) {
        return 4.0;
    }
    if ($ratio >= 4.55 && $ratio <= 5.45) {
        return 5.0;
    }
    $r = (int) round($ratio);
    if ($r === 4 || $r === 5) {
        return (float) $r;
    }
    return null;
}

try {
    $id = $_GET['id'] ?? $_GET['id_curso'] ?? null;
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $cursoManager = new Curso();
    $curso = $cursoManager->obtenerCursoPorId($id);

    if (isset($curso['error'])) {
        http_response_code(404);
        echo json_encode($curso);
        exit;
    }

    $campos_fecha = ['fecha_inicio', 'fecha_fin', 'fecha_limite_inicio', 'fecha_creacion', 'fecha_actualizacion'];
    foreach ($campos_fecha as $campo) {
        if (!empty($curso[$campo]) && $curso[$campo] !== '0000-00-00' && $curso[$campo] !== '0000-00-00 00:00:00') {
            $curso[$campo] = date('Y-m-d', strtotime($curso[$campo]));
        } else {
            $curso[$campo] = null;
        }
    }

    $ht = null;
    if (isset($curso['horas_totales']) && $curso['horas_totales'] !== '' && $curso['horas_totales'] !== null) {
        $ht = round((float) $curso['horas_totales'], 2);
        $curso['horas_totales'] = $ht;
    } else {
        $curso['horas_totales'] = null;
    }

    $hRaw = $curso['horas_por_clase'] ?? null;
    if ($hRaw === null || $hRaw === '' || $hRaw === false) {
        $hpc = null;
    } else {
        $hpc = round((float) $hRaw, 2);
    }

    $hpcInt = $hpc === null ? null : (int) round($hpc);
    $hpcOk = $hpcInt === 4 || $hpcInt === 5;

    if (!$hpcOk) {
        $infer = inferirHorasPorClaseDesdeTotales($ht, $curso['fecha_inicio'] ?? null, $curso['fecha_fin'] ?? null);
        if ($infer !== null) {
            $curso['horas_por_clase'] = $infer;
        } else {
            // Solo 4 y 5 son válidos; si no hay forma de deducir, 4 h es el valor por defecto del reglamento
            $curso['horas_por_clase'] = 4.0;
        }
    } else {
        $curso['horas_por_clase'] = (float) $hpcInt;
    }

    echo json_encode($curso);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

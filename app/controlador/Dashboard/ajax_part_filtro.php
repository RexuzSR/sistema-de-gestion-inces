<?php
/**
 * AJAX endpoint — Inscripciones filtradas por curso
 * Recibe parámetros GET: ambito, ppl, anio, mes, sexo, discapacidad
 * Devuelve JSON con campos: nombre_curso, inscritos, cupos, participantes[]
 */
header('Content-Type: application/json; charset=utf-8');

// ── Conexión ──────────────────────────────────────────────────
$host   = 'localhost';
$dbname = 'inces1';
$user   = 'root';
$pass   = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// ── Leer parámetros (sanitizados) ────────────────────────────
$ambito      = trim($_GET['ambito']      ?? '');
$ppl         = trim($_GET['ppl']         ?? '');
$anio        = trim($_GET['anio']        ?? '');
$mes         = trim($_GET['mes']         ?? '');
$sexo        = trim($_GET['sexo']        ?? '');
$discapacidad= trim($_GET['discapacidad']?? '');

// ── Construir query con filtros dinámicos ────────────────────
$where  = ['c.id_curso != 1'];
$params = [];

if ($ambito !== '') {
    $where[]           = 'a.nombre_ambito = :ambito';
    $params[':ambito'] = $ambito;
}
if ($ppl !== '') {
    $where[]        = 'ppl.nombre_ppl = :ppl';
    $params[':ppl'] = $ppl;
}
if ($anio !== '' && ctype_digit($anio)) {
    $where[]         = 'YEAR(c.fecha_inicio) = :anio';
    $params[':anio'] = (int)$anio;
}
if ($mes !== '' && ctype_digit($mes)) {
    $where[]        = 'MONTH(c.fecha_inicio) = :mes';
    $params[':mes'] = (int)$mes;
}
if ($sexo !== '') {
    $where[]         = 'p.genero = :sexo';
    $params[':sexo'] = $sexo;
}
if ($discapacidad !== '') {
    $where[]               = 'COALESCE(d.nombre_discapacidad, \'Ninguna\') = :disc';
    $params[':disc']       = $discapacidad;
}

$whereSQL = implode(' AND ', $where);

$sql = "
    SELECT
        p.primer_nombre,
        p.primer_apellido,
        p.cedula,
        p.genero,
        COALESCE(p.nacionalidad, IF(p.cedula >= 80000000, 'E', 'V')) AS nacionalidad,
        COALESCE(d.nombre_discapacidad, 'Ninguna') AS nombre_discapacidad,
        c.id_curso,
        c.nombre_curso,
        COALESCE(a.nombre_ambito, 'Sin ambito')   AS nombre_ambito,
        YEAR(c.fecha_inicio)                        AS anio,
        MONTH(c.fecha_inicio)                       AS mes,
        COALESCE(ppl.nombre_ppl, 'Sin PPL')         AS nombre_ppl,
        c.max_participantes
    FROM tb_participante_curso pc
    JOIN tb_participante p   ON pc.id_participante = p.id_participante
    LEFT JOIN tb_discapacidad d ON p.id_discapacidad = d.id_discapacidad
    JOIN tb_curso c          ON pc.id_curso   = c.id_curso
    LEFT JOIN tb_ambito a    ON c.id_ambito   = a.id_ambito
    LEFT JOIN tb_ppl ppl     ON c.id_ppl      = ppl.id_ppl
    WHERE $whereSQL
    ORDER BY c.fecha_inicio DESC
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// ── Agrupar por curso para la gráfica ────────────────────────
$cursos = [];
foreach ($rows as $r) {
    $key = $r['id_curso'];
    if (!isset($cursos[$key])) {
        $cursos[$key] = [
            'id_curso'      => (int)$r['id_curso'],
            'nombre_curso'  => $r['nombre_curso'],
            'inscritos'     => 0,
            'cupos'         => (int)$r['max_participantes'],
        ];
    }
    $cursos[$key]['inscritos']++;
}

// Ordenar por inscritos desc, top 15
usort($cursos, fn($a,$b) => $b['inscritos'] - $a['inscritos']);
$cursos = array_values(array_slice($cursos, 0, 15));

echo json_encode([
    'cursos'       => $cursos,
    'participantes'=> $rows,
    'total'        => count($rows),
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

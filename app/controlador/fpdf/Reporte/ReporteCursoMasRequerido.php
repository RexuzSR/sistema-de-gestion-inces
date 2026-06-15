<?php
require('../fpdf.php');
require_once('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $anio;
    private $usuario;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setAnio($anio)
    {
        $this->anio = $anio;
    }

    function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    function Header()
    {
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 28);
        $this->SetFont('Arial', 'B', 16);
        $this->SetXY(38, 10);
        $this->Cell(220, 8, utf8_decode('Curso más Requerido'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 11);
        $this->SetXY(38, 19);
        $this->Cell(220, 6, utf8_decode('Año Seleccionado: ' . $this->anio), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $usuarioTxt = isset($this->usuario) && strlen(trim($this->usuario)) ? $this->usuario : 'Usuario no identificado';
        $this->Cell(0, 6, utf8_decode('Generado por: ' . $usuarioTxt), 0, 1, 'L');
        $this->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'R');
        $this->Ln(4);
    }

    function Footer()
    {
        $this->SetY(-13);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, utf8_decode('Sistema de Gestión INCES   —   Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// ─── Parámetros POST ───────────────────────────────────────────────────────
$anio             = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = $_POST['chart_image'] ?? '';
$datosJson        = $_POST['datos_json']  ?? '[]';

$cursos = json_decode($datosJson, true);
if (!is_array($cursos) || empty($cursos)) {
    die("No se recibieron datos de cursos.");
}

// ─── Calcular umbrales de prioridad (P75 / P40) ───────────────────────────
$scores = array_column($cursos, 'score');
sort($scores);
$n   = count($scores);
$p75 = $scores[(int)floor($n * 0.75)] ?? $scores[$n - 1];
$p40 = $scores[(int)floor($n * 0.40)] ?? $scores[0];

function getPrioridad($score, $p75, $p40) {
    if ($score >= $p75) return ['label' => 'ALTA',  'flag' => 'A'];
    if ($score >= $p40) return ['label' => 'MEDIA', 'flag' => 'M'];
    return                    ['label' => 'BAJA',   'flag' => 'B'];
}

// ─── Estadísticas ─────────────────────────────────────────────────────────
$totInscr  = array_sum(array_column($cursos, 'inscripciones'));
$avgScore  = $n > 0 ? round(array_sum($scores) / $n) : 0;
$avgPartic = $n > 0 ? round(array_sum(array_column($cursos, 'participantes')) / $n) : 0;

// ─── Inicializar PDF ──────────────────────────────────────────────────────
$conexion = new Conexion();
$conn = $conexion->getConexion();
$pdf = new PDF('L', 'mm', 'A4');
$pdf->setAnio($anio);
// Incluir usuario desde base de datos
date_default_timezone_set('America/Caracas');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) session_start();
$usuarioFull = 'Usuario no identificado';
if (isset($_SESSION['id_usuario'])) {
    $sqlUsuario = "SELECT TRIM(CONCAT(
                        COALESCE(primer_nombre, ''), ' ',
                        COALESCE(segundo_nombre, ''), ' ',
                        COALESCE(primer_apellido, ''), ' ',
                        COALESCE(segundo_apellido, '')
                    )) AS nombre_completo
                   FROM tb_usuario WHERE id_usuario = :id_usuario";
    try {
        $stmtUsuario = $conn->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
        $stmtUsuario->execute();
        $resultUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if ($resultUsuario && !empty(trim($resultUsuario['nombre_completo']))) {
            $usuarioFull = trim($resultUsuario['nombre_completo']);
        }
    } catch (Exception $e) {
        // Fallback si la consulta falla
    }
}
$pdf->setUsuario($usuarioFull);
$pdf->AliasNbPages();
$pdf->AddPage();

$pageW  = 297; // A4 Landscape
$margin = 10;
$usable = $pageW - ($margin * 2); // 277mm

// ═══════════════════════════════════════════════════════════════════════════
//  SECCIÓN 1: TOP 3
// ═══════════════════════════════════════════════════════════════════════════
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(230, 240, 255);
$pdf->Cell(0, 9, utf8_decode(' TOP 3 — Cursos más Requeridos'), 0, 1, 'L', true);
$pdf->Ln(4);

$top3     = array_slice($cursos, 0, 3);
$posLabel = ['1°', '2°', '3°'];
$cardW    = 88;
$cardH    = 55;
$yTop     = $pdf->GetY();

for ($i = 0; $i < min(3, count($top3)); $i++) {
    $c  = $top3[$i];
    $pr = getPrioridad($c['score'], $p75, $p40);
    $x  = $margin + $i * ($cardW + 5);

    // Fondo de tarjeta
    if ($i === 0) $pdf->SetFillColor(255, 248, 220);
    elseif ($i === 1) $pdf->SetFillColor(235, 235, 235);
    else              $pdf->SetFillColor(255, 235, 235);

    $pdf->Rect($x, $yTop, $cardW, $cardH, 'FD');

    // Posición
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetXY($x, $yTop + 2);
    $pdf->Cell($cardW, 12, $posLabel[$i], 0, 1, 'C');

    // Nombre (truncar si es muy largo)
    $nom = utf8_decode($c['curso']);
    if (strlen($nom) > 40) $nom = substr($nom, 0, 37) . '...';
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY($x + 2, $yTop + 14);
    $pdf->MultiCell($cardW - 4, 5, $nom, 0, 'C');

    // Score
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetXY($x, $yTop + 27);
    $pdf->SetTextColor(13, 110, 253);
    $pdf->Cell($cardW, 8, $c['score'] . ' / 100', 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);

    // Badge prioridad
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x + 15, $yTop + 36);
    $pdf->Cell($cardW - 30, 6, utf8_decode($pr['label'] . ' PRIORIDAD'), 1, 1, 'C');

    // Detalles reverso
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetXY($x + 2, $yTop + 44);
    $pdf->Cell(($cardW - 4) / 2, 4, 'Inscritos: ' . number_format($c['participantes']), 0, 0, 'L');
    $pdf->Cell(($cardW - 4) / 2, 4, 'Inscripciones: ' . number_format($c['inscripciones']), 0, 1, 'L');
    $pdf->SetXY($x + 2, $yTop + 49);
    $tasa = $c['inscripciones'] > 0 ? round(($c['participantes'] / $c['inscripciones']) * 100) . '%' : '—';
    $pdf->Cell($cardW - 4, 4, 'Tasa llenado: ' . $tasa . '   Área: ' . utf8_decode($c['area']), 0, 1, 'L');
}

$pdf->SetXY($margin, $yTop + $cardH + 5);

// ═══════════════════════════════════════════════════════════════════════════
//  SECCIÓN 2: ESTADÍSTICAS
// ═══════════════════════════════════════════════════════════════════════════
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(230, 240, 255);
$pdf->Cell(0, 9, utf8_decode(' Estadísticas de Requerimiento'), 0, 1, 'L', true);
$pdf->Ln(4);

$yStats   = $pdf->GetY();
$statW    = $usable / 4;
$stats    = [
    ['val' => $n,                           'lbl' => 'Cursos Analizados',   'sub' => 'En el año seleccionado'],
    ['val' => $avgScore,                    'lbl' => utf8_decode('Puntuación Promedio'), 'sub' => utf8_decode('Índice de requerimiento')],
    ['val' => number_format($totInscr),     'lbl' => 'Total Inscripciones', 'sub' => 'Suma de todos los cursos'],
    ['val' => number_format($avgPartic),    'lbl' => 'Participantes Prom.', 'sub' => utf8_decode('Únicos por curso')],
];

foreach ($stats as $idx => $st) {
    $sx = $margin + $idx * $statW;
    $pdf->SetFillColor(245, 247, 255);
    $pdf->Rect($sx, $yStats, $statW - 2, 20, 'FD');
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetXY($sx, $yStats + 2);
    $pdf->Cell($statW - 2, 8, $st['val'], 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($sx, $yStats + 10);
    $pdf->Cell($statW - 2, 4, $st['lbl'], 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetXY($sx, $yStats + 14);
    $pdf->Cell($statW - 2, 4, $st['sub'], 0, 1, 'C');
}

$pdf->SetXY($margin, $yStats + 25);

// ═══════════════════════════════════════════════════════════════════════════
//  SECCIÓN 3: GRÁFICO
// ═══════════════════════════════════════════════════════════════════════════
if (!empty($chartImageBase64)) {
    $imgData = str_replace('data:image/png;base64,', '', $chartImageBase64);
    $imgData = base64_decode($imgData);
    $tempImg = sys_get_temp_dir() . '/chart_cmr_' . uniqid() . '.png';
    file_put_contents($tempImg, $imgData);

    // Verificar espacio restante (A4 Landscape: 210mm alto, footer en 197mm)
    if ($pdf->GetY() + 75 > 192) {
        $pdf->AddPage();
    }

    $pdf->SetFont('Arial', 'B', 13);
    $pdf->SetFillColor(230, 240, 255);
    $pdf->Cell(0, 9, utf8_decode(' Índice de Requerimiento por Área Formativa'), 0, 1, 'L', true);
    $pdf->Ln(3);

    $pdf->Image($tempImg, $margin, $pdf->GetY(), $usable, 70);
    unlink($tempImg);
    $pdf->Ln(75);
}

// ═══════════════════════════════════════════════════════════════════════════
//  SECCIÓN 4: TABLA RANKING COMPLETO
// ═══════════════════════════════════════════════════════════════════════════
if ($pdf->GetY() + 30 > 192) {
    $pdf->AddPage();
}

$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(230, 240, 255);
$pdf->Cell(0, 9, utf8_decode(' Ranking Completo de Cursos'), 0, 1, 'L', true);
$pdf->Ln(4);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(210, 220, 240);
$wCols = [12, 75, 25, 28, 28, 42, 32, 35]; // Pos, Curso, Insc, Part, Score, Area, Estatus, Accion
$headers = ['#', 'Curso', 'Inscripciones', utf8_decode('Participantes'), 'Score (%)', utf8_decode('Área'), 'Estatus', utf8_decode('Acción')];

foreach ($headers as $hi => $h) {
    $pdf->Cell($wCols[$hi], 7, $h, 1, 0, 'C', true);
}
$pdf->Ln();

// Filas
$pdf->SetFont('Arial', '', 7.5);
foreach ($cursos as $gi => $row) {
    // Salto de página si no cabe la fila
    if ($pdf->GetY() + 7 > 192) {
        $pdf->AddPage();
        // Reimprimir cabecera
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(210, 220, 240);
        foreach ($headers as $hi => $h) {
            $pdf->Cell($wCols[$hi], 7, $h, 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 7.5);
    }

    $pr  = getPrioridad($row['score'], $p75, $p40);
    $accion = $row['score'] > 75 ? 'Reabrir pronto' : ($row['score'] > 50 ? 'Considerar' : 'Mantener');

    // Highlight top 3
    if ($gi < 3) $pdf->SetFillColor(255, 248, 220);
    else          $pdf->SetFillColor(255, 255, 255);

    $fill = ($gi < 3);

    $nom = utf8_decode($row['curso']);
    if (strlen($nom) > 42) $nom = substr($nom, 0, 39) . '...';

    $area = utf8_decode($row['area']);
    if (strlen($area) > 24) $area = substr($area, 0, 21) . '...';

    $pdf->Cell($wCols[0], 6, $gi + 1,                             1, 0, 'C', $fill);
    $pdf->Cell($wCols[1], 6, $nom,                                 1, 0, 'L', $fill);
    $pdf->Cell($wCols[2], 6, number_format($row['inscripciones']), 1, 0, 'C', $fill);
    $pdf->Cell($wCols[3], 6, number_format($row['participantes']), 1, 0, 'C', $fill);
    $pdf->Cell($wCols[4], 6, $row['score'] . ' / 100',            1, 0, 'C', $fill);
    $pdf->Cell($wCols[5], 6, $area,                                1, 0, 'C', $fill);
    $pdf->Cell($wCols[6], 6, utf8_decode($row['estatus']),         1, 0, 'C', $fill);
    $pdf->Cell($wCols[7], 6, utf8_decode($accion),                 1, 0, 'C', $fill);
    $pdf->Ln();
}

// ═══════════════════════════════════════════════════════════════════════════
//  SECCIÓN 5: NOTA EXPLICATIVA DE LA FÓRMULA
// ═══════════════════════════════════════════════════════════════════════════
if ($pdf->GetY() + 35 > 192) {
    $pdf->AddPage();
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(230, 240, 255);
$pdf->Cell(0, 9, utf8_decode(' Metodología del Índice de Requerimiento'), 0, 1, 'L', true);
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(248, 249, 250);
$pdf->Rect($margin, $pdf->GetY(), $usable, 28, 'FD');

$yNota = $pdf->GetY() + 2;
$pdf->SetXY($margin + 2, $yNota);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode('Fórmula del Score:'), 0, 1);
$pdf->SetXY($margin + 2, $pdf->GetY());
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 6, 'Score = (Inscripciones / Max) x 30  +  (Participantes unicos / Max) x 40  +  (Participantes / Inscripciones) x 30', 0, 1);

$pdf->SetXY($margin + 2, $pdf->GetY() + 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, 'Umbrales de Prioridad calculados para este año:', 0, 1);
$pdf->SetXY($margin + 2, $pdf->GetY());
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(70, 5, utf8_decode('Alta Prioridad  — Score >= P75 (>= ' . $p75 . ')'), 0, 0, 'L');
$pdf->Cell(80, 5, utf8_decode('Media Prioridad — Score >= P40 (>= ' . $p40 . ')'), 0, 0, 'L');
$pdf->Cell(80, 5, utf8_decode('Baja Prioridad  — Score  < P40 ( < ' . $p40 . ')'), 0, 1, 'L');

// ─── Salida ───────────────────────────────────────────────────────────────
$pdf->Output('I', 'Reporte_Curso_Mas_Requerido.pdf');
?>

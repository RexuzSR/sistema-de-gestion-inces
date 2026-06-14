<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $anio;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setAnio($anio)
    {
        $this->anio = $anio;
    }

    function Header()
    {
        // Logo de INCES
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 30);
        $this->SetFont('Arial', 'B', 16);
        // Título
        $this->Cell(80);
        $this->Cell(120, 10, utf8_decode('Cursos con más Participantes'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(80);
        $this->Cell(120, 10, utf8_decode('Año Seleccionado: ' . $this->anio), 0, 1, 'C');
        // Fecha de generación
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, utf8_decode('Generado el: ' . date('d/m/Y h:i A')), 0, 1, 'R');
        $this->Ln(5);
    }

    function Footer()
    {
        // Pie de página
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema de Gestión INCES - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// 1. Obtener parámetros
$anio = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';

// 2. Obtener datos de la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

$limite = 50; // Para obtener todos los cursos relevantes (usualmente 19 en el ejemplo)

$sql = "SELECT
            c.id_curso,
            c.nombre_curso,
            COUNT(DISTINCT pc.id_participante) AS total_participantes,
            e.nombre_estatusCurso AS estatus,
            m.nombre_modalidad    AS modalidad,
            c.fecha_inicio,
            c.fecha_fin
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_modalidad    m ON m.id_modalidad    = c.id_modalidad
        WHERE YEAR(pc.fecha_inscripcion) = :anio
        GROUP BY c.id_curso, c.nombre_curso, e.nombre_estatusCurso, m.nombre_modalidad, c.fecha_inicio, c.fecha_fin
        ORDER BY total_participantes DESC
        LIMIT :limite";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($cursos) === 0) {
    die("No hay datos de cursos para el año seleccionado.");
}

// 3. Procesar datos (Cálculos de Promedio, Desviación, Clasificación)
$totalParticipantesGlobal = 0;
$participantesArray = [];
foreach ($cursos as $c) {
    $p = intval($c['total_participantes']);
    $totalParticipantesGlobal += $p;
    $participantesArray[] = $p;
}
$cantidadCursos = count($cursos);
$promedio = $cantidadCursos > 0 ? $totalParticipantesGlobal / $cantidadCursos : 0;

$sumaCuadrados = 0;
foreach ($participantesArray as $p) {
    $sumaCuadrados += pow($p - $promedio, 2);
}
$desviacion = $cantidadCursos > 0 ? sqrt($sumaCuadrados / $cantidadCursos) : 0;

$umbSup = $promedio + $desviacion;
$umbInf = max(0, $promedio - $desviacion);

foreach ($cursos as &$c) {
    $p = intval($c['total_participantes']);
    if ($p >= $umbSup) {
        $c['clasificacion'] = 'Muy Activo';
    } elseif ($p >= $umbInf) {
        $c['clasificacion'] = 'Activo';
    } else {
        $c['clasificacion'] = 'Normal';
    }
}
unset($c);

$cursoMasActivo = $cursos[0]; // Ya viene ordenado DESC

// Función de duración
function getDuracionLegible($fi, $ff) {
    if (!$fi || !$ff) return 'Sin informacion';
    $ts1 = strtotime($fi);
    $ts2 = strtotime($ff);
    if (!$ts1 || !$ts2) return 'Sin informacion';
    $dias = round(($ts2 - $ts1) / 86400);
    if ($dias < 0) return 'Sin informacion';
    if ($dias == 0) return '1 dia';
    if ($dias < 7) return $dias . ' dia' . ($dias != 1 ? 's' : '');
    if ($dias < 30) {
        $sem = round($dias / 7);
        return $sem . ' sem (' . $dias . ' d)';
    }
    $m = round($dias / 30);
    return $m . ' mes(es) (' . $dias . ' d)';
}

// 4. Inicializar PDF (Horizontal para acomodar gráficos y tabla ancha)
$pdf = new PDF('L', 'mm', 'A4');
$pdf->setAnio($anio);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- SECCIÓN: Top 3 Cursos ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, utf8_decode(' Clasificación Top 3 '), 0, 1, 'L', true);
$pdf->Ln(5);

$xInicio = $pdf->GetX();
$yInicio = $pdf->GetY();
$cardWidth = 85;

for ($i = 0; $i < min(3, count($cursos)); $i++) {
    $curso = $cursos[$i];
    $xOffset = $xInicio + ($i * ($cardWidth + 10)); // 10mm gap
    $pdf->SetXY($xOffset, $yInicio);
    
    // Dibujar borde de tarjeta
    $pdf->Rect($xOffset, $yInicio, $cardWidth, 35);
    
    // Contenido de tarjeta
    $pdf->SetFont('Arial', 'B', 12);
    $nombre = utf8_decode($curso['nombre_curso']);
    if (strlen($nombre) > 35) $nombre = substr($nombre, 0, 32) . '...';
    
    $pdf->SetXY($xOffset + 2, $yInicio + 5);
    $pdf->Cell($cardWidth - 4, 6, ($i+1) . '. ' . $nombre, 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY($xOffset + 2, $yInicio + 15);
    $pdf->Cell($cardWidth - 4, 6, utf8_decode($curso['total_participantes'] . ' participantes'), 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetXY($xOffset + 2, $yInicio + 25);
    $pdf->Cell($cardWidth - 4, 6, utf8_decode('Estado: ' . $curso['clasificacion']), 0, 1, 'C');
}

$pdf->Ln(45); // Espacio después de las tarjetas

// --- SECCIÓN: Métricas Resumen ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode(' Métricas Resumen '), 0, 1, 'L', true);
$pdf->Ln(5);

$yMetrics = $pdf->GetY();
$metricWidth = 65;

// Métrica 1
$pdf->SetXY(10, $yMetrics);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($metricWidth, 6, utf8_decode('Total Participantes:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10, $yMetrics + 8);
$pdf->Cell($metricWidth, 6, number_format($totalParticipantesGlobal), 0, 1, 'C');

// Métrica 2
$pdf->SetXY(10 + $metricWidth, $yMetrics);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($metricWidth, 6, utf8_decode('Promedio / Curso:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10 + $metricWidth, $yMetrics + 8);
$pdf->Cell($metricWidth, 6, round($promedio), 0, 1, 'C');

// Métrica 3
$pdf->SetXY(10 + ($metricWidth * 2), $yMetrics);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($metricWidth, 6, utf8_decode('Cursos Analizados:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10 + ($metricWidth * 2), $yMetrics + 8);
$pdf->Cell($metricWidth, 6, $cantidadCursos, 0, 1, 'C');

// Métrica 4
$pdf->SetXY(10 + ($metricWidth * 3), $yMetrics);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 6, utf8_decode('Curso más Activo:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(10 + ($metricWidth * 3), $yMetrics + 8);
$nomMax = utf8_decode($cursoMasActivo['nombre_curso']);
if(strlen($nomMax) > 40) $nomMax = substr($nomMax, 0, 37).'...';
$pdf->Cell(80, 6, $nomMax, 0, 1, 'C');

$pdf->Ln(20);

// --- SECCIÓN: Gráfico ---
if (!empty($chartImageBase64)) {
    $imgData = str_replace('data:image/png;base64,', '', $chartImageBase64);
    $imgData = base64_decode($imgData);
    $tempImg = sys_get_temp_dir() . '/chart_cmp_' . uniqid() . '.png';
    file_put_contents($tempImg, $imgData);
    
    // Check if chart fits on page (A4 landscape height is 210mm, footer is at 195mm)
    if ($pdf->GetY() + 90 > 190) {
        $pdf->AddPage();
    } else {
        $pdf->Ln(10);
    }
    
    // Posicionar gráfico
    // Asumimos orientación Horizontal. A4 Ancho = 297mm. Dejamos margen.
    $pdf->Image($tempImg, 15, $pdf->GetY(), 260, 80);
    unlink($tempImg);
    $pdf->Ln(85);
}

// Check si hay espacio para la tabla, si no, nueva página
if ($pdf->GetY() > 140) {
    $pdf->AddPage();
} else {
    $pdf->Ln(10);
}

// --- SECCIÓN: Tabla de Clasificación ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode(' Tabla de Clasificación de Cursos '), 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
// Anchos de columnas (Total 277 en horizontal)
$w = array(15, 85, 30, 40, 40, 35, 30);
// Posición, Curso, Participantes, Estatus, Modalidad, Duración y Estado
$pdf->Cell($w[0], 8, 'Pos.', 1, 0, 'C', true);
$pdf->Cell($w[1], 8, 'Curso', 1, 0, 'C', true);
$pdf->Cell($w[2], 8, 'Participantes', 1, 0, 'C', true);
$pdf->Cell($w[3], 8, 'Estatus', 1, 0, 'C', true);
$pdf->Cell($w[4], 8, 'Modalidad', 1, 0, 'C', true);
$pdf->Cell($w[5], 8, utf8_decode('Duración'), 1, 0, 'C', true);
$pdf->Cell($w[6], 8, 'Estado', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
$pos = 1;
foreach ($cursos as $row) {
    // Control de salto de página
    if ($pdf->GetY() > 185) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($w[0], 8, 'Pos.', 1, 0, 'C', true);
        $pdf->Cell($w[1], 8, 'Curso', 1, 0, 'C', true);
        $pdf->Cell($w[2], 8, 'Participantes', 1, 0, 'C', true);
        $pdf->Cell($w[3], 8, 'Estatus', 1, 0, 'C', true);
        $pdf->Cell($w[4], 8, 'Modalidad', 1, 0, 'C', true);
        $pdf->Cell($w[5], 8, utf8_decode('Duración'), 1, 0, 'C', true);
        $pdf->Cell($w[6], 8, 'Estado', 1, 0, 'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
    }
    
    $nom = utf8_decode($row['nombre_curso']);
    if (strlen($nom) > 48) $nom = substr($nom, 0, 45) . '...';
    
    $pdf->Cell($w[0], 7, $pos, 1, 0, 'C');
    $pdf->Cell($w[1], 7, $nom, 1, 0, 'L');
    $pdf->Cell($w[2], 7, $row['total_participantes'], 1, 0, 'C');
    $pdf->Cell($w[3], 7, utf8_decode($row['estatus'] ?? 'N/D'), 1, 0, 'C');
    $pdf->Cell($w[4], 7, utf8_decode($row['modalidad'] ?? 'N/D'), 1, 0, 'C');
    $pdf->Cell($w[5], 7, utf8_decode(getDuracionLegible($row['fecha_inicio'], $row['fecha_fin'])), 1, 0, 'C');
    $pdf->Cell($w[6], 7, utf8_decode($row['clasificacion']), 1, 0, 'C');
    $pdf->Ln();
    $pos++;
}

// Salida
$pdf->Output('I', 'Reporte_Cursos_Mas_Participantes.pdf');
?>

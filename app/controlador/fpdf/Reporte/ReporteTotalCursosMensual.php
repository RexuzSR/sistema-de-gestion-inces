<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $anio;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setAnio($anio)
    {
        $this->anio = $anio;
    }

    function Header()
    {
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Total de Cursos'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Año Seleccionado: ' . $this->anio), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, utf8_decode('Generado el: ' . date('d/m/Y h:i A')), 0, 1, 'R');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema de Gestión INCES - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$anio = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Funciones Auxiliares
function inicializarMesesLocal($anio) {
    $nombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $hoyAnio = intval(date('Y'));
    $hoyMes  = intval(date('n'));
    $meses   = [];
    for ($i = 1; $i <= 12; $i++) {
        $esFuturo = ($anio > $hoyAnio) || ($anio === $hoyAnio && $i > $hoyMes);
        $meses[$i] = [
            'mes' => $nombres[$i - 1],
            'nuevos' => $esFuturo ? null : 0,
            'activos' => $esFuturo ? null : 0,
            'culminados' => $esFuturo ? null : 0,
            'enEspera' => $esFuturo ? null : 0,
            'totalAcumulado' => 0
        ];
    }
    return $meses;
}

$meses = inicializarMesesLocal($anio);

$sql = "SELECT MONTH(c.fecha_creacion) AS mes, COUNT(*) AS total,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En proceso' THEN 1 ELSE 0 END) AS en_proceso,
               SUM(CASE WHEN e.nombre_estatusCurso = 'Culminado'  THEN 1 ELSE 0 END) AS culminado,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En espera'  THEN 1 ELSE 0 END) AS en_espera
        FROM tb_curso c
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        WHERE YEAR(c.fecha_creacion) = :anio 
        GROUP BY MONTH(c.fecha_creacion)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmt->execute();

$totNuevos = 0;
$totActivos = 0;
$totCulm = 0;
$totEspera = 0;
$ultimoMesValido = null;

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $m = intval($row['mes']);
    if (isset($meses[$m]) && $meses[$m]['nuevos'] !== null) {
        $meses[$m]['nuevos'] = intval($row['total']);
        $meses[$m]['activos'] = intval($row['en_proceso']);
        $meses[$m]['culminados'] = intval($row['culminado']);
        $meses[$m]['enEspera'] = intval($row['en_espera']);
    }
}

$acumulado = 0;
$datosMeses = [];
foreach ($meses as $m) {
    if ($m['nuevos'] !== null) {
        $acumulado += $m['nuevos'];
        $m['totalAcumulado'] = $acumulado;
        $totNuevos += $m['nuevos'];
        $totActivos += $m['activos'];
        $totCulm += $m['culminados'];
        $totEspera += $m['enEspera'];
        $ultimoMesValido = $m;
    }
    $datosMeses[] = $m;
}

// Métricas de resumen
// Cursos Activos: contar directamente los que ACTUALMENTE tienen estatus "En proceso"
// (creados en el año o que siguen activos) — mismo cálculo que el frontend al hacer click en KPI
$sqlActivos = "SELECT COUNT(*) AS total
               FROM tb_curso c
               LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
               WHERE e.nombre_estatusCurso = 'En proceso'
                 AND YEAR(c.fecha_creacion) = :anio";
$stmtActivos = $conn->prepare($sqlActivos);
$stmtActivos->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmtActivos->execute();
$activosUltimoMes = intval($stmtActivos->fetchColumn());

$tasaCompletitud = $totNuevos > 0 ? number_format(($totCulm / $totNuevos) * 100, 1) . '%' : '-';

// Iniciar PDF (Vertical P es apropiado)
$pdf = new PDF('P', 'mm', 'A4');
$pdf->setAnio($anio);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- SECCIÓN: Métricas Resumen ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, utf8_decode(' Métricas Resumen '), 0, 1, 'L', true);
$pdf->Ln(5);

$yMetrics = $pdf->GetY();
$metricWidth = 47.5; // (190 / 4)

// Tarjeta 1: Nuevos
$pdf->SetXY(10, $yMetrics);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($metricWidth, 6, utf8_decode('Nuevos Cursos:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10, $yMetrics + 8);
$pdf->Cell($metricWidth, 6, number_format($totNuevos), 0, 1, 'C');

// Tarjeta 2: Activos
$pdf->SetXY(10 + $metricWidth, $yMetrics);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($metricWidth, 6, utf8_decode('Cursos Activos:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10 + $metricWidth, $yMetrics + 8);
$pdf->Cell($metricWidth, 6, number_format($activosUltimoMes), 0, 1, 'C');

// Tarjeta 3: Culminados
$pdf->SetXY(10 + ($metricWidth * 2), $yMetrics);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($metricWidth, 6, utf8_decode('Cursos Culminados:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10 + ($metricWidth * 2), $yMetrics + 8);
$pdf->Cell($metricWidth, 6, number_format($totCulm), 0, 1, 'C');

// Tarjeta 4: Completitud
$pdf->SetXY(10 + ($metricWidth * 3), $yMetrics);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($metricWidth, 6, utf8_decode('Tasa Completitud:'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->SetXY(10 + ($metricWidth * 3), $yMetrics + 8);
$pdf->Cell($metricWidth, 6, utf8_decode($tasaCompletitud), 0, 1, 'C');

$pdf->Ln(20);

// --- SECCIÓN: Gráfico ---
if (!empty($chartImageBase64)) {
    $imgData = str_replace('data:image/png;base64,', '', $chartImageBase64);
    $imgData = base64_decode($imgData);
    $tempImg = sys_get_temp_dir() . '/chart_tc_' . uniqid() . '.png';
    file_put_contents($tempImg, $imgData);
    
    // Check if chart fits on page (A4 Portrait height is 297mm, footer is at 282mm)
    if ($pdf->GetY() + 80 > 275) {
        $pdf->AddPage();
    }
    
    // Posicionar gráfico
    $pdf->Image($tempImg, 15, $pdf->GetY(), 180, 75);
    unlink($tempImg);
    $pdf->Ln(80);
}

// --- SECCIÓN: Tabla Mensual ---
if ($pdf->GetY() + 60 > 275) {
    $pdf->AddPage();
}

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode(' Registro Mensual '), 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);

$w = array(30, 30, 30, 30, 30, 40);
// Mes, Nuevos, Activos, Culminados, En espera, Total
$pdf->Cell($w[0], 8, 'Mes', 1, 0, 'C', true);
$pdf->Cell($w[1], 8, 'Nuevos cursos', 1, 0, 'C', true);
$pdf->Cell($w[2], 8, 'Cursos activos', 1, 0, 'C', true);
$pdf->Cell($w[3], 8, 'Culminados', 1, 0, 'C', true);
$pdf->Cell($w[4], 8, 'En espera', 1, 0, 'C', true);
$pdf->Cell($w[5], 8, 'Total acumulado', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($datosMeses as $mes) {
    if ($mes['nuevos'] === null) {
        $n = '-'; $a = '-'; $c = '-'; $e = '-'; $ac = '-';
    } else {
        $n = number_format($mes['nuevos']);
        $a = number_format($mes['activos']);
        $c = number_format($mes['culminados']);
        $e = number_format($mes['enEspera']);
        $ac = number_format($mes['totalAcumulado']);
    }
    
    $pdf->Cell($w[0], 7, utf8_decode($mes['mes']), 1, 0, 'L');
    $pdf->Cell($w[1], 7, $n, 1, 0, 'C');
    $pdf->Cell($w[2], 7, $a, 1, 0, 'C');
    $pdf->Cell($w[3], 7, $c, 1, 0, 'C');
    $pdf->Cell($w[4], 7, $e, 1, 0, 'C');
    $pdf->Cell($w[5], 7, $ac, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($w[0], 8, 'Total', 1, 0, 'L', true);
$pdf->Cell($w[1], 8, number_format($totNuevos), 1, 0, 'C', true);
$pdf->Cell($w[2], 8, number_format($totActivos), 1, 0, 'C', true);
$pdf->Cell($w[3], 8, number_format($totCulm), 1, 0, 'C', true);
$pdf->Cell($w[4], 8, number_format($totEspera), 1, 0, 'C', true);
$pdf->Cell($w[5], 8, number_format($acumulado), 1, 0, 'C', true);
$pdf->Ln();

// Salida
$pdf->Output('I', 'Reporte_Total_Cursos.pdf');
?>

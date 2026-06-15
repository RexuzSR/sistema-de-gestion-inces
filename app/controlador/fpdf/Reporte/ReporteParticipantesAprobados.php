<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $anioBase;
    private $periodoTxt;
    private $usuario;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setInfo($anio, $periodoTxt, $usuario)
    {
        $this->anioBase = $anio;
        $this->periodoTxt = $periodoTxt;
        $this->usuario = $usuario;
    }

    function Header()
    {
        // Logo
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(80);
        $this->Cell(30, 8, utf8_decode('Participantes Aprobados'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(80);
        $this->Cell(30, 6, utf8_decode('Año: ' . $this->anioBase . '  |  Período: ' . $this->periodoTxt), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $usuarioTxt = isset($this->usuario) && strlen(trim($this->usuario)) ? $this->usuario : 'Usuario no identificado';
        $this->Cell(0, 5, utf8_decode('Generado por: ' . $usuarioTxt), 0, 1, 'L');
        $this->Cell(0, 5, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'R');
        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema de Gestión INCES - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function drawKpiCard($x, $y, $w, $h, $title, $value, $rColor, $gColor, $bColor)
    {
        $this->SetFillColor(248, 250, 252);
        $this->Rect($x, $y, $w, $h, 'F');
        
        $this->SetFillColor($rColor, $gColor, $bColor);
        $this->Rect($x, $y, 3, $h, 'F');
        
        $this->SetXY($x + 5, $y + 3);
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor($rColor, $gColor, $bColor);
        $this->Cell($w - 10, 6, utf8_decode($value), 0, 1, 'L');
        
        $this->SetXY($x + 5, $y + 10);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(100, 110, 120);
        $this->Cell($w - 10, 4, strtoupper(utf8_decode($title)), 0, 1, 'L');
        
        $this->SetTextColor(0, 0, 0);
    }
}

$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';
$chartCursosBase64 = isset($_POST['chart_cursos_image']) ? $_POST['chart_cursos_image'] : '';
$payloadRaw = isset($_POST['payload_json']) ? $_POST['payload_json'] : '{}';
$payload = json_decode($payloadRaw, true);

if (!$payload) $payload = [];

$anioTabla = isset($payload['anioTabla']) ? $payload['anioTabla'] : date('Y');
$filtroTiempo = isset($payload['filtroTiempo']) ? $payload['filtroTiempo'] : ['tipo' => 'anual'];
$resumen = isset($payload['resumen']) ? $payload['resumen'] : [];
$panelData = isset($payload['panelData']) ? $payload['panelData'] : [];
$tablaMensual = isset($payload['tablaMensual']) ? $payload['tablaMensual'] : [];
$cursosSel = isset($payload['cursosSel']) ? $payload['cursosSel'] : [];

// Determinar el texto del período activo
$periodoTxt = 'Anual';
if ($filtroTiempo['tipo'] === 'semestral') {
    $periodoTxt = ($filtroTiempo['semestre'] ?? 1) == 1 ? '1er Semestre' : '2do Semestre';
} elseif ($filtroTiempo['tipo'] === 'trimestral') {
    $periodoTxt = ($filtroTiempo['trimestre'] ?? 1) . "er Trimestre";
} elseif ($filtroTiempo['tipo'] === 'mensual') {
    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $idx = ($filtroTiempo['mes'] ?? 1) - 1;
    if (isset($meses[$idx])) {
        $periodoTxt = "Mes: " . $meses[$idx];
    }
}

$pdf = new PDF('P', 'mm', 'A4');

date_default_timezone_set('America/Caracas');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) session_start();
$conexion = new Conexion();
$conn = $conexion->getConexion();
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
    } catch (Exception $e) { }
}
$pdf->setInfo($anioTabla, $periodoTxt, $usuarioFull);
$pdf->AliasNbPages();
$pdf->AddPage();

// ── SECCIÓN: KPIs principales ──
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(' KPIs de Resumen (Año ' . $anioTabla . ') '), 0, 1, 'L', true);
$pdf->Ln(3);

$yKpi = $pdf->GetY();
$kpiW = 44;
$kpiGap = 4;
$pdf->drawKpiCard(10, $yKpi, $kpiW, 16, 'Evaluados', isset($resumen['evaluados']) ? number_format($resumen['evaluados'], 0, '', '.') : '0', 13, 110, 253);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap), $yKpi, $kpiW, 16, 'Aprobados', isset($resumen['aprobados']) ? number_format($resumen['aprobados'], 0, '', '.') : '0', 25, 135, 84);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap)*2, $yKpi, $kpiW, 16, 'Reprobados', isset($resumen['reprobados']) ? number_format($resumen['reprobados'], 0, '', '.') : '0', 220, 53, 69);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap)*3, $yKpi, $kpiW, 16, 'Tasa Global', isset($resumen['tasa']) ? $resumen['tasa'] . '%' : '0%', 255, 193, 7);

$pdf->Ln(20);

// ── SECCIÓN: Panel de detalle (si existe) ──
if (!empty($panelData) && !empty($panelData['tipo'])) {
    $titulosTipo = [
        'evaluados' => 'Evaluados por Mes',
        'aprobados' => 'Aprobados por Mes',
        'reprobados' => 'Reprobados por Mes',
        'tasa' => 'Tasa de Aprobación (%)'
    ];
    $tituloPanel = $titulosTipo[$panelData['tipo']] ?? 'Detalle Mensual';
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, utf8_decode(" $tituloPanel "), 0, 1, 'L', true);
    $pdf->Ln(3);
    
    $meses = $panelData['meses'] ?? [];
    $anios = $panelData['anios'] ?? [];
    $valores = $panelData['valores'] ?? [];
    
    if (!empty($anios) && !empty($meses)) {
        $nCol = count($anios);
        $colW = 140 / ($nCol > 0 ? $nCol : 1);
        
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetFillColor(240, 242, 245);
        $pdf->Cell(50, 6.5, 'Mes', 1, 0, 'C', true);
        foreach ($anios as $a) {
            $pdf->Cell($colW, 6.5, $a, 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8.5);
        $totalesPorAnio = array_fill_keys($anios, 0);
        
        for ($i = 0; $i < 12; $i++) {
            $pdf->Cell(50, 6, utf8_decode($meses[$i]), 1, 0, 'C');
            foreach ($anios as $a) {
                $val = isset($valores[$a][$i]) ? $valores[$a][$i] : null;
                $txtVal = ($val !== null) ? $val : '-';
                
                if ($val !== null && $panelData['tipo'] !== 'tasa') {
                    $totalesPorAnio[$a] += floatval($val);
                }
                
                $pdf->Cell($colW, 6, utf8_decode($txtVal), 1, 0, 'C');
            }
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(50, 6.5, 'Total', 1, 0, 'C', true);
        foreach ($anios as $a) {
            $valTot = $panelData['tipo'] !== 'tasa' ? number_format($totalesPorAnio[$a], 0, '', '.') : '-';
            $pdf->Cell($colW, 6.5, utf8_decode($valTot), 1, 0, 'C', true);
        }
        $pdf->Ln();
    }
    $pdf->Ln(5);
}

// ── SECCIÓN: Gráfico Principal ──
if (!empty($chartImageBase64) && strpos($chartImageBase64, 'data:image') === 0) {
    $imgData = preg_replace('/^data:image\/\w+;base64,/', '', $chartImageBase64);
    $imgData = base64_decode($imgData);
    $tempImg = sys_get_temp_dir() . '/chart_aprob_' . uniqid() . '.png';
    file_put_contents($tempImg, $imgData);
    
    if (file_exists($tempImg) && filesize($tempImg) > 0 && @getimagesize($tempImg)) {
        if ($pdf->GetY() > 160) {
            $pdf->AddPage();
        }
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 8, utf8_decode(' Gráfico: Participantes Aprobados '), 0, 1, 'L', true);
        $pdf->Ln(2);
        
        $pdf->Image($tempImg, 15, $pdf->GetY(), 180, 65);
        $pdf->Ln(70);
    }
    if (file_exists($tempImg)) {
        unlink($tempImg);
    }
}

if ($pdf->GetY() > 210) {
    $pdf->AddPage();
} else {
    $pdf->Ln(5);
}

// ── SECCIÓN: Tabla Mensual ──
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(" Registro Mensual — $anioTabla "), 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(240, 242, 245);
$wT = [35, 28, 28, 28, 32, 38];
$pdf->Cell($wT[0], 7, 'Mes', 1, 0, 'C', true);
$pdf->Cell($wT[1], 7, 'Evaluados', 1, 0, 'C', true);
$pdf->Cell($wT[2], 7, 'Aprobados', 1, 0, 'C', true);
$pdf->Cell($wT[3], 7, 'Reprobados', 1, 0, 'C', true);
$pdf->Cell($wT[4], 7, 'Tasa Aprobacion', 1, 0, 'C', true);
$pdf->Cell($wT[5], 7, 'Variacion mensual', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8.5);

$totE = 0; $totA = 0; $totR = 0;
$antAprobados = null;

if (!empty($tablaMensual)) {
    foreach ($tablaMensual as $mes) {
        $sd = !isset($mes['aprobados']) || $mes['aprobados'] === null;
        
        if (!$sd) {
            $totA += $mes['aprobados'];
            $totE += $mes['evaluados'];
            $totR += $mes['reprobados'];
        }
        
        $crec = '-';
        $crecColor = [0, 0, 0];
        
        if (!$sd) {
            if ($antAprobados !== null) {
                if ($antAprobados > 0) {
                    $diff = $mes['aprobados'] - $antAprobados;
                    $pct = number_format(($diff / $antAprobados) * 100, 1, '.', '');
                    if ($diff > 0) {
                        $crec = "+$diff (+$pct%)";
                        $crecColor = [25, 135, 84]; // verde
                    } elseif ($diff < 0) {
                        $crec = "$diff ($pct%)";
                        $crecColor = [220, 53, 69]; // rojo
                    } else {
                        $crec = "0 (0%)";
                        $crecColor = [108, 117, 125]; // gris
                    }
                } else {
                    $crec = "Sin ref.";
                    $crecColor = [108, 117, 125];
                }
            } else {
                $crec = "Primer mes";
                $crecColor = [108, 117, 125];
            }
            // Update previous for next iteration ONLY if this is a valid month
            $antAprobados = $mes['aprobados'];
        }

        $pdf->Cell($wT[0], 6, utf8_decode($mes['mes'] ?? ''), 1, 0, 'C');
        $pdf->Cell($wT[1], 6, $sd ? '-' : number_format($mes['evaluados'], 0, '', '.'), 1, 0, 'C');
        $pdf->Cell($wT[2], 6, $sd ? '-' : number_format($mes['aprobados'], 0, '', '.'), 1, 0, 'C');
        $pdf->Cell($wT[3], 6, $sd ? '-' : number_format($mes['reprobados'], 0, '', '.'), 1, 0, 'C');
        
        // Tasa color
        $tasa = $mes['tasaAprobacion'] ?? 0;
        if (!$sd) {
            if ($tasa >= 80) $pdf->SetTextColor(25, 135, 84);
            elseif ($tasa >= 60) $pdf->SetTextColor(255, 193, 7);
            else $pdf->SetTextColor(220, 53, 69);
            $pdf->Cell($wT[4], 6, $tasa . '%', 1, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);
        } else {
            $pdf->Cell($wT[4], 6, '-', 1, 0, 'C');
        }
        
        $pdf->SetTextColor($crecColor[0], $crecColor[1], $crecColor[2]);
        $pdf->Cell($wT[5], 6, utf8_decode($crec), 1, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
    }
}

// Fila total
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(248, 249, 250);
$pdf->Cell($wT[0], 7, 'Total', 1, 0, 'C', true);
$pdf->Cell($wT[1], 7, number_format($totE, 0, '', '.'), 1, 0, 'C', true);
$pdf->Cell($wT[2], 7, number_format($totA, 0, '', '.'), 1, 0, 'C', true);
$pdf->Cell($wT[3], 7, number_format($totR, 0, '', '.'), 1, 0, 'C', true);
$tasaTotal = $totE > 0 ? number_format(($totA / $totE * 100), 1, '.', '') . '%' : '-';
$pdf->Cell($wT[4], 7, $tasaTotal, 1, 0, 'C', true);
$pdf->Cell($wT[5], 7, '-', 1, 1, 'C', true);

$pdf->Ln(10);

// ── SECCIÓN: Análisis por Curso ──
if (!empty($cursosSel)) {
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(0, 8, utf8_decode(' Análisis por Curso (' . count($cursosSel) . ' seleccionados) '), 0, 1, 'L', true);
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(240, 242, 245);
    $wCur = [75, 25, 25, 20, 22, 22];
    $pdf->Cell($wCur[0], 7, 'Nombre del Curso', 1, 0, 'C', true);
    $pdf->Cell($wCur[1], 7, 'Modalidad', 1, 0, 'C', true);
    $pdf->Cell($wCur[2], 7, 'Estado', 1, 0, 'C', true);
    $pdf->Cell($wCur[3], 7, 'Particip.', 1, 0, 'C', true);
    $pdf->Cell($wCur[4], 7, 'F. Inicio', 1, 0, 'C', true);
    $pdf->Cell($wCur[5], 7, 'F. Fin', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 8);
    foreach ($cursosSel as $c) {
        $fi = !empty($c['fecha_inicio']) ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-';
        $ff = !empty($c['fecha_fin']) ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-';
        
        $nombre = substr(utf8_decode($c['nombre_curso'] ?? ''), 0, 50);
        if (strlen($c['nombre_curso']) > 50) $nombre .= '...';

        $pdf->Cell($wCur[0], 6, $nombre, 1, 0, 'L');
        $pdf->Cell($wCur[1], 6, utf8_decode($c['modalidad'] ?? '-'), 1, 0, 'C');
        $pdf->Cell($wCur[2], 6, utf8_decode($c['estatus'] ?? '-'), 1, 0, 'C');
        $pdf->Cell($wCur[3], 6, number_format($c['total_participantes'] ?? 0, 0, '', '.'), 1, 0, 'C');
        $pdf->Cell($wCur[4], 6, $fi, 1, 0, 'C');
        $pdf->Cell($wCur[5], 6, $ff, 1, 1, 'C');
    }
    
    $pdf->Ln(5);

    // Si hay un solo curso, imprimir ficha
    if (count($cursosSel) === 1) {
        $c = $cursosSel[0];
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, utf8_decode('Ficha de Detalle del Curso'), 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 9);
        $fi = !empty($c['fecha_inicio']) ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-';
        $ff = !empty($c['fecha_fin']) ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-';
        
        $pdf->SetFillColor(248, 250, 252);
        $pdf->Rect(10, $pdf->GetY(), 189, 25, 'F');
        
        $pdf->SetXY(15, $pdf->GetY() + 3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(180, 5, utf8_decode($c['nombre_curso'] ?? ''), 0, 1, 'L');
        
        $pdf->SetXY(15, $pdf->GetY() + 2);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(45, 4, 'Modalidad: ' . utf8_decode($c['modalidad'] ?? '-'), 0, 0, 'L');
        $pdf->Cell(45, 4, 'Estado: ' . utf8_decode($c['estatus'] ?? '-'), 0, 0, 'L');
        $pdf->Cell(45, 4, 'Fechas: ' . $fi . ' a ' . $ff, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(13, 110, 253);
        $pdf->Cell(45, 4, 'Total: ' . number_format($c['total_participantes'] ?? 0, 0, '', '.') . ' participantes', 0, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Ln(10);
    } 
    // Si hay varios, imprimir el gráfico
    else if (!empty($chartCursosBase64) && strpos($chartCursosBase64, 'data:image') === 0) {
        $imgData = preg_replace('/^data:image\/\w+;base64,/', '', $chartCursosBase64);
        $imgData = base64_decode($imgData);
        $tempImg = sys_get_temp_dir() . '/chart_cursos_' . uniqid() . '.png';
        file_put_contents($tempImg, $imgData);
        
        if (file_exists($tempImg) && filesize($tempImg) > 0 && @getimagesize($tempImg)) {
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, utf8_decode('Comparativa de Participantes por Curso'), 0, 1, 'C');
            $pdf->Ln(2);
            
            // Adjust height if it's a polar area / doughnut (square) or a bar chart
            $pdf->Image($tempImg, 15, $pdf->GetY(), 180, 75);
            $pdf->Ln(80);
        }
        if (file_exists($tempImg)) {
            unlink($tempImg);
        }
    }
}

// Output
$pdf->Output('I', 'Reporte_Participantes_Aprobados.pdf');
?>

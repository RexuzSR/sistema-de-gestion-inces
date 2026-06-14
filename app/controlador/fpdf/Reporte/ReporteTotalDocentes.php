<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $anioBase;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setAnioBase($anio)
    {
        $this->anioBase = $anio;
    }

    function Header()
    {
        // Logo de INCES
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Total de Docentes'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Año Base Activo: ' . $this->anioBase), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode('Generado el: ' . date('d/m/Y h:i A')), 0, 1, 'R');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema de Gestión INCES - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Dibujar tarjeta de KPI
    function drawKpiCard($x, $y, $w, $h, $title, $value, $rColor, $gColor, $bColor)
    {
        $this->SetFillColor(248, 250, 252); // Fondo gris muy claro
        $this->Rect($x, $y, $w, $h, 'F');
        
        // Borde izquierdo de color
        $this->SetFillColor($rColor, $gColor, $bColor);
        $this->Rect($x, $y, 3, $h, 'F');
        
        // Valor
        $this->SetXY($x + 5, $y + 3);
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor($rColor, $gColor, $bColor);
        $this->Cell($w - 10, 6, utf8_decode($value), 0, 1, 'L');
        
        // Etiqueta
        $this->SetXY($x + 5, $y + 9);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(100, 110, 120);
        $this->Cell($w - 10, 4, strtoupper(utf8_decode($title)), 0, 1, 'L');
        
        // Restablecer color de texto
        $this->SetTextColor(0, 0, 0);
    }
}

// 1. Obtener parámetros
$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';
$anioBase = isset($_POST['anio_base']) ? intval($_POST['anio_base']) : date('Y');
$anioTabla = isset($_POST['anio_tabla']) ? intval($_POST['anio_tabla']) : $anioBase;
$aniosGraficoRaw = isset($_POST['anios_grafico']) ? $_POST['anios_grafico'] : '';

$aniosGrafico = [$anioBase];
if (!empty($aniosGraficoRaw)) {
    $decoded = json_decode($aniosGraficoRaw, true);
    if (is_array($decoded)) {
        $aniosGrafico = array_map('intval', $decoded);
    }
}

// 2. Conectar a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

// 3. Consultar totales globales
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total,
           SUM(CASE WHEN estado_docente = 'Disponible'   THEN 1 ELSE 0 END) AS disponible,
           SUM(CASE WHEN estado_docente = 'En formación' THEN 1 ELSE 0 END) AS en_formacion,
           SUM(CASE WHEN estado_docente = 'Asignado'     THEN 1 ELSE 0 END) AS asignado,
           SUM(CASE WHEN estado_docente = 'Inactivo'     THEN 1 ELSE 0 END) AS inactivo,
           SUM(CASE WHEN tipo_docente   = 'Fijo'         THEN 1 ELSE 0 END) AS fijo,
           SUM(CASE WHEN tipo_docente   = 'Contratado'   THEN 1 ELSE 0 END) AS contratado,
           SUM(CASE WHEN tipo_docente   = 'PDL'          THEN 1 ELSE 0 END) AS pdl,
           SUM(CASE WHEN tipo_docente   = 'Voluntario'   THEN 1 ELSE 0 END) AS voluntario,
           SUM(CASE WHEN genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
           SUM(CASE WHEN genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino
    FROM tb_docente
");
$stmt->execute();
$totales = $stmt->fetch(PDO::FETCH_ASSOC);

$totalActivos = (intval($totales['disponible']) + intval($totales['en_formacion']) + intval($totales['asignado']));
$totalInactivos = intval($totales['inactivo']);

// 4. Inicializar PDF (Vertical A4)
$pdf = new PDF('P', 'mm', 'A4');
$pdf->setAnioBase($anioBase);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- SECCIÓN: KPIs principales ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(' KPIs de Resumen (Año Base Activo) '), 0, 1, 'L', true);
$pdf->Ln(3);

$yKpi = $pdf->GetY();
$kpiW = 45;
$kpiGap = 3.3;
$pdf->drawKpiCard(10, $yKpi, $kpiW, 16, 'Total Docentes', $totales['total'], 13, 110, 253);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap), $yKpi, $kpiW, 16, 'Docentes Activos', $totalActivos, 25, 135, 84);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap) * 2, $yKpi, $kpiW, 16, 'Asignados', $totales['asignado'], 255, 193, 7);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap) * 3, $yKpi, $kpiW, 16, 'Inactivos', $totalInactivos, 220, 53, 69);

$pdf->Ln(20);

// --- SECCIÓN: Distribución por Tipo y Género ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode(' Distribución por Tipo y Género '), 0, 1, 'L', true);
$pdf->Ln(3);

$yTables = $pdf->GetY();
// Tabla 1: Tipo (Ancho 92mm)
$pdf->SetXY(10, $yTables);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(92, 5, 'Por Tipo de Docente', 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(240, 242, 245);
$pdf->Cell(52, 6, 'Tipo', 1, 0, 'L', true);
$pdf->Cell(40, 6, 'Cantidad', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8.5);
$tipos = [
    'Fijo' => $totales['fijo'],
    'Contratado' => $totales['contratado'],
    'PDL' => $totales['pdl'],
    'Voluntario' => $totales['voluntario']
];
foreach ($tipos as $k => $v) {
    $pdf->Cell(52, 6, $k, 1, 0, 'L');
    $pdf->Cell(40, 6, $v, 1, 1, 'C');
}
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(52, 6, 'Total', 1, 0, 'L', true);
$pdf->Cell(40, 6, $totales['total'], 1, 1, 'C', true);

// Tabla 2: Género (Ancho 92mm)
$pdf->SetXY(108, $yTables);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(92, 5, utf8_decode('Por Género'), 0, 1, 'L');
$pdf->SetX(108);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(52, 6, utf8_decode('Género'), 1, 0, 'L', true);
$pdf->Cell(40, 6, 'Cantidad', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8.5);
$generos = [
    'Masculino' => $totales['masculino'],
    'Femenino' => $totales['femenino']
];
foreach ($generos as $k => $v) {
    $pdf->SetX(108);
    $pdf->Cell(52, 6, utf8_decode($k), 1, 0, 'L');
    $pdf->Cell(40, 6, $v, 1, 1, 'C');
}
$pdf->SetX(108);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(52, 6, 'Total', 1, 0, 'L', true);
$pdf->Cell(40, 6, intval($totales['masculino']) + intval($totales['femenino']), 1, 1, 'C', true);

$pdf->Ln(8);

// --- SECCIÓN: Distribución por Estado ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode(' Distribución por Estado Activo '), 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(240, 242, 245);
$wEst = [60, 60, 70];
$pdf->Cell($wEst[0], 6.5, 'Estado', 1, 0, 'L', true);
$pdf->Cell($wEst[1], 6.5, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell($wEst[2], 6.5, '% del total activo', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8.5);
$estados = [
    'Disponible' => $totales['disponible'],
    'Asignado' => $totales['asignado'],
    'En formación' => $totales['en_formacion']
];
foreach ($estados as $k => $v) {
    $pct = $totalActivos > 0 ? round(($v / $totalActivos) * 100) : 0;
    $pdf->Cell($wEst[0], 6, utf8_decode($k), 1, 0, 'L');
    $pdf->Cell($wEst[1], 6, $v, 1, 0, 'C');
    $pdf->Cell($wEst[2], 6, $pct . '%', 1, 1, 'C');
}
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell($wEst[0], 6.5, 'Total Activos', 1, 0, 'L', true);
$pdf->Cell($wEst[1], 6.5, $totalActivos, 1, 0, 'C', true);
$pdf->Cell($wEst[2], 6.5, '100%', 1, 1, 'C', true);

$pdf->Ln(8);

// --- SECCIÓN: Panel de inactivos ---
$pdf->SetFillColor(253, 237, 237); // Fondo rojo claro/alerta
$pdf->SetDrawColor(220, 53, 69);
$pdf->Rect(10, $pdf->GetY(), 190, 18);
$pdf->SetXY(12, $pdf->GetY() + 2);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->SetTextColor(220, 53, 69);
$pdf->Cell(0, 5, utf8_decode("Docentes Inactivos: $totalInactivos docente" . ($totalInactivos != 1 ? 's' : '') . " con estado Inactivo"), 0, 1, 'L');
$pdf->SetXY(12, $pdf->GetY());
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(0, 4, utf8_decode('Estos docentes están registrados pero inactivos. Para un listado detallado, consulte el reporte "Docentes Inactivos".'), 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);

$pdf->Ln(10);

// --- SECCIÓN: Gráfico ---
if (!empty($chartImageBase64)) {
    $imgData = str_replace('data:image/png;base64,', '', $chartImageBase64);
    $imgData = base64_decode($imgData);
    $tempImg = sys_get_temp_dir() . '/chart_doc_' . uniqid() . '.png';
    file_put_contents($tempImg, $imgData);
    
    // Nueva página para gráfico y tabla mensual si el espacio es reducido
    if ($pdf->GetY() > 140) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(0, 8, utf8_decode(' Historial de Asignaciones por Mes '), 0, 1, 'L', true);
    $pdf->Ln(2);
    
    $pdf->Image($tempImg, 15, $pdf->GetY(), 180, 65);
    unlink($tempImg);
    $pdf->Ln(70);
}

// Check space for table
if ($pdf->GetY() > 220) {
    $pdf->AddPage();
} else {
    $pdf->Ln(2);
}

// --- SECCIÓN: Tabla mensual ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(' Registro Mensual '), 0, 1, 'L', true);
$pdf->Ln(3);

$mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

// Helper para traer asignaciones de un año
function getAsignacionesPorAnio($conn, $anio) {
    $meses = array_fill(1, 12, 0);
    $stmt = $conn->prepare("
        SELECT MONTH(dc.fecha_asignacion) AS mes, COUNT(*) AS total
        FROM tb_docente_curso dc
        WHERE YEAR(dc.fecha_asignacion) = :anio AND dc.id_docente IS NOT NULL
        GROUP BY MONTH(dc.fecha_asignacion)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $meses[intval($row['mes'])] = intval($row['total']);
    }
    return $meses;
}

if (count($aniosGrafico) <= 1) {
    // Tabla para UN SOLO AÑO: Mes, Asignaciones, Total Acumulado, Variación
    $wTab = [30, 45, 45, 70];
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetFillColor(240, 242, 245);
    $pdf->Cell($wTab[0], 6.5, 'Mes', 1, 0, 'C', true);
    $pdf->Cell($wTab[1], 6.5, 'Asignaciones del mes', 1, 0, 'C', true);
    $pdf->Cell($wTab[2], 6.5, 'Total acumulado', 1, 0, 'C', true);
    $pdf->Cell($wTab[3], 6.5, utf8_decode('Variación mensual'), 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 8.5);
    $dataAsig = getAsignacionesPorAnio($conn, $anioTabla);
    
    $acum = 0;
    $prev = null;
    $totalAsig = 0;
    
    for ($i = 1; $i <= 12; $i++) {
        $val = $dataAsig[$i];
        $acum += $val;
        $totalAsig += $val;
        
        $varStr = '—';
        if ($i === 1) {
            $varStr = 'Primer mes';
        } else {
            $valAnt = $dataAsig[$i - 1];
            if ($valAnt > 0) {
                $diff = $val - $valAnt;
                $pct = round(($diff / $valAnt) * 100, 1);
                $sign = $diff > 0 ? '+' : '';
                $varStr = "$sign$diff ($sign$pct%)";
            } else if ($val > 0) {
                $varStr = "+$val (N/A)";
            }
        }

        $pdf->Cell($wTab[0], 6, $mesesNombres[$i - 1], 1, 0, 'C');
        $pdf->Cell($wTab[1], 6, $val, 1, 0, 'C');
        $pdf->Cell($wTab[2], 6, $acum, 1, 0, 'C');
        
        // Colorear variación
        if ($i > 1 && $dataAsig[$i - 1] > 0) {
            $diff = $val - $dataAsig[$i - 1];
            if ($diff > 0) {
                $pdf->SetTextColor(25, 135, 84); // Verde
            } else if ($diff < 0) {
                $pdf->SetTextColor(220, 53, 69); // Rojo
            }
        }
        $pdf->Cell($wTab[3], 6, utf8_decode($varStr), 1, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); // Reset
    }
    
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell($wTab[0], 6.5, 'Total', 1, 0, 'C', true);
    $pdf->Cell($wTab[1], 6.5, $totalAsig, 1, 0, 'C', true);
    $pdf->Cell($wTab[2], 6.5, '—', 1, 0, 'C', true);
    $pdf->Cell($wTab[3], 6.5, '—', 1, 1, 'C', true);

} else {
    // Tabla MULTI-AÑO: columnas por año
    $nCol = count($aniosGrafico);
    $colW = 140 / $nCol;
    
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetFillColor(240, 242, 245);
    $pdf->Cell(50, 6.5, 'Mes', 1, 0, 'C', true);
    foreach ($aniosGrafico as $a) {
        $pdf->Cell($colW, 6.5, $a, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $dataAnios = [];
    foreach ($aniosGrafico as $a) {
        $dataAnios[$a] = getAsignacionesPorAnio($conn, $a);
    }

    $totalesPorAnio = array_fill_keys($aniosGrafico, 0);
    $pdf->SetFont('Arial', '', 8.5);
    
    for ($i = 1; $i <= 12; $i++) {
        $pdf->Cell(50, 6, $mesesNombres[$i - 1], 1, 0, 'C');
        foreach ($aniosGrafico as $a) {
            $val = $dataAnios[$a][$i];
            $totalesPorAnio[$a] += $val;
            $pdf->Cell($colW, 6, $val, 1, 0, 'C');
        }
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(50, 6.5, 'Total', 1, 0, 'C', true);
    foreach ($aniosGrafico as $a) {
        $pdf->Cell($colW, 6.5, $totalesPorAnio[$a], 1, 0, 'C', true);
    }
    $pdf->Ln();
}

// --- SECCIÓN: Detalle de Docentes Asignados por Mes ---
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(' Detalle de Docentes Asignados por Mes — ') . $anioTabla, 0, 1, 'L', true);
$pdf->Ln(3);

$mesesNombres2 = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

$stmtDetalle = $conn->prepare("
    SELECT dc.id_docente_curso,
           d.id_docente,
           CONCAT(d.primer_nombre, ' ', COALESCE(d.segundo_nombre, ''), ' ', d.primer_apellido, ' ', COALESCE(d.segundo_apellido, '')) AS nombre_completo,
           d.cedula,
           d.tipo_docente,
           d.estado_docente,
           c.id_curso,
           c.nombre_curso,
           dc.fecha_asignacion,
           MONTH(dc.fecha_asignacion) AS mes
    FROM tb_docente_curso dc
    JOIN tb_docente d ON d.id_docente = dc.id_docente
    JOIN tb_curso c ON c.id_curso = dc.id_curso
    WHERE YEAR(dc.fecha_asignacion) = :anio
      AND dc.id_docente IS NOT NULL
    ORDER BY MONTH(dc.fecha_asignacion) ASC, d.primer_apellido ASC, d.primer_nombre ASC
");
$stmtDetalle->execute([':anio' => $anioTabla]);
$registros = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

if (count($registros) > 0) {
    // Agrupar por mes
    $porMes = [];
    foreach ($registros as $reg) {
        $mes = intval($reg['mes']);
        if (!isset($porMes[$mes])) $porMes[$mes] = [];
        $porMes[$mes][] = $reg;
    }

    foreach ($porMes as $numMes => $asignaciones) {
        // Verificar espacio y agregar página si necesario
        if ($pdf->GetY() > 240) {
            $pdf->AddPage();
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(255, 243, 205); // Fondo amarillo suave
        $pdf->SetTextColor(133, 100, 4);
        $pdf->Cell(0, 7, utf8_decode('  ' . $mesesNombres2[$numMes - 1] . ' ' . $anioTabla . ' — ' . count($asignaciones) . ' asignación(es)'), 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);

        // Tabla de detalle
        $wDet = [15, 55, 30, 25, 40, 25];
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(240, 242, 245);
        $pdf->Cell($wDet[0], 6, 'ID', 1, 0, 'C', true);
        $pdf->Cell($wDet[1], 6, 'Docente', 1, 0, 'L', true);
        $pdf->Cell($wDet[2], 6, utf8_decode('Cédula'), 1, 0, 'C', true);
        $pdf->Cell($wDet[3], 6, 'Tipo', 1, 0, 'C', true);
        $pdf->Cell($wDet[4], 6, 'Curso Asignado', 1, 0, 'L', true);
        $pdf->Cell($wDet[5], 6, 'Fecha', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 7.5);
        foreach ($asignaciones as $a) {
            // Verificar espacio
            if ($pdf->GetY() > 270) {
                $pdf->AddPage();
                // Re-dibujar encabezado de tabla
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetFillColor(240, 242, 245);
                $pdf->Cell($wDet[0], 6, 'ID', 1, 0, 'C', true);
                $pdf->Cell($wDet[1], 6, 'Docente', 1, 0, 'L', true);
                $pdf->Cell($wDet[2], 6, utf8_decode('Cédula'), 1, 0, 'C', true);
                $pdf->Cell($wDet[3], 6, 'Tipo', 1, 0, 'C', true);
                $pdf->Cell($wDet[4], 6, 'Curso Asignado', 1, 0, 'L', true);
                $pdf->Cell($wDet[5], 6, 'Fecha', 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 7.5);
            }

            $nombre = preg_replace('/\s+/', ' ', trim($a['nombre_completo']));
            $curso = $a['nombre_curso'];
            // Truncar si es muy largo
            if (strlen($nombre) > 28) $nombre = substr($nombre, 0, 26) . '..';
            if (strlen($curso) > 22) $curso = substr($curso, 0, 20) . '..';

            $fecha = date('d/m/Y', strtotime($a['fecha_asignacion']));

            $pdf->Cell($wDet[0], 5.5, $a['id_docente'], 1, 0, 'C');
            $pdf->Cell($wDet[1], 5.5, utf8_decode($nombre), 1, 0, 'L');
            $pdf->Cell($wDet[2], 5.5, number_format($a['cedula'], 0, '', '.'), 1, 0, 'C');
            $pdf->Cell($wDet[3], 5.5, utf8_decode($a['tipo_docente']), 1, 0, 'C');
            $pdf->Cell($wDet[4], 5.5, utf8_decode($curso), 1, 0, 'L');
            $pdf->Cell($wDet[5], 5.5, $fecha, 1, 1, 'C');
        }

        $pdf->Ln(4);
    }
} else {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 8, utf8_decode('No se encontraron asignaciones de docentes en el año ' . $anioTabla . '.'), 0, 1, 'L');
}

// 7. Output
$pdf->Output('I', 'Reporte_Total_Docentes.pdf');
?>

<?php
ob_start();
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';
$payloadRaw = isset($_POST['payload_json']) ? $_POST['payload_json'] : '{}';
$payload = json_decode($payloadRaw, true);

if (!$payload) $payload = [];

$anioBase = isset($payload['anioBase']) ? $payload['anioBase'] : date('Y');
$estadosFiltrados = isset($payload['estadosFiltrados']) ? $payload['estadosFiltrados'] : [];
$filtroDemo = isset($payload['filtroDemo']) ? $payload['filtroDemo'] : [];
$resumen = isset($payload['resumen']) ? $payload['resumen'] : [];
$datosEstadosSistema = isset($payload['datosEstadosSistema']) ? $payload['datosEstadosSistema'] : ['total' => 0, 'estados' => []];
$panelData = isset($payload['panelData']) ? $payload['panelData'] : [];
$tablaMensual = isset($payload['tablaMensual']) ? $payload['tablaMensual'] : [];

// ── Filtros Demográficos ──
$filtrosActivos = [];
if (!empty($filtroDemo['genero'])) {
    $filtrosActivos[] = "Género: " . implode(" o ", array_map('ucfirst', $filtroDemo['genero']));
}
if (!empty($filtroDemo['discapacidad'])) {
    $filtrosActivos[] = "Discapacidad: " . implode(" o ", array_map(function($d){return $d=='con'?'Con':'Sin';}, $filtroDemo['discapacidad']));
}
if (!empty($filtroDemo['edadUmbral'])) {
    $nombresEdad = ['adolescente' => 'Adolescentes', 'adulto' => 'Adultos', 'adultoMayor' => 'Adultos mayores'];
    $edadesTxt = array_map(function($e) use ($nombresEdad) { return $nombresEdad[$e] ?? $e; }, $filtroDemo['edadUmbral']);
    $filtrosActivos[] = "Edad: " . implode(" o ", $edadesTxt);
}
if (!empty($estadosFiltrados)) {
    $filtrosActivos[] = "Estado: " . implode(" o ", $estadosFiltrados);
}

$filtrosHtml = "";
if (!empty($filtrosActivos)) {
    $filtrosHtml = "<div class='filter-box'>
        <strong>Filtros Activos:</strong> " . htmlspecialchars(implode(' · ', $filtrosActivos)) . "
    </div>";
}

// Convert Logo
$logoPath = __DIR__ . '/../../../../assets/img/inces-logo.png';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,'.$logoData;
} else {
    $logoSrc = '';
}

// KPIs
$totalSistemaValor = $datosEstadosSistema['total'] ?? 0;
if (!empty($estadosFiltrados)) {
    $sumaFiltrada = 0;
    foreach($estadosFiltrados as $est) {
        $sumaFiltrada += $datosEstadosSistema['estados'][$est] ?? 0;
    }
    $totalSistemaValor = "$sumaFiltrada de {$datosEstadosSistema['total']}";
}
$nuevosVal = $resumen['totalNuevos'] ?? 0;
$activosVal = $resumen['activos'] ?? 0;
$retiradosVal = $resumen['retirados'] ?? 0;
$retencionVal = $resumen['tasaRetencion'] ?? '0.0';

// Estados
$estadosDisponibles = [
    'En formacion' => 'En formación',
    'Disponible' => 'Disponible',
    'Asignado' => 'Asignado',
    'Inactivo' => 'Inactivo',
    'En sistema' => 'En sistema',
    'Diferido' => 'Diferido'
];
$tablaEstadosHtml = "";
foreach ($estadosDisponibles as $key => $label) {
    $val = $datosEstadosSistema['estados'][$key] ?? 0;
    $isFiltered = in_array($key, $estadosFiltrados);
    $txtFiltered = $isFiltered ? "<span style='color:#198754; font-weight:bold;'>SÍ</span>" : "<span style='color:#64748b;'>NO</span>";
    
    $tablaEstadosHtml .= "<tr>
        <td>{$label}</td>
        <td style='text-align:center;'>".number_format($val, 0, '', '.')."</td>
        <td style='text-align:center;'>{$txtFiltered}</td>
    </tr>";
}

// Detalle del dato activo (Panel mensual comparativo)
$panelDetalleHtml = "";
if (!empty($panelData) && !empty($panelData['tipo']) && $panelData['tipo'] !== 'total_sistema') {
    $titulosTipo = [
        'nuevos' => 'Nuevos Inscritos por Mes',
        'activos' => 'Participantes Activos por Mes',
        'retirados' => 'Finalizaciones y Bajas por Mes',
        'retencion' => 'Tasa de Retención Mensual (%)'
    ];
    $tituloPanel = $titulosTipo[$panelData['tipo']] ?? 'Detalle Mensual';
    
    $mesesPanel = $panelData['meses'] ?? [];
    $aniosPanel = $panelData['anios'] ?? [];
    $valoresPanel = $panelData['valores'] ?? [];
    
    if (!empty($aniosPanel) && !empty($mesesPanel)) {
        $nCol = count($aniosPanel);
        $thAnios = "";
        foreach ($aniosPanel as $a) {
            $thAnios .= "<th style='text-align:center;'>{$a}</th>";
        }
        
        $filasPanel = "";
        $totalesPorAnio = array_fill_keys($aniosPanel, 0);
        
        for ($i = 0; $i < 12; $i++) {
            $filasPanel .= "<tr>
                <td><strong>{$mesesPanel[$i]}</strong></td>";
            foreach ($aniosPanel as $a) {
                $val = isset($valoresPanel[$a][$i]) ? $valoresPanel[$a][$i] : null;
                $txtVal = ($val !== null) ? $val : '—';
                if ($val !== null && $panelData['tipo'] !== 'retencion') {
                    $totalesPorAnio[$a] += floatval($val);
                }
                $filasPanel .= "<td style='text-align:center;'>{$txtVal}</td>";
            }
            $filasPanel .= "</tr>";
        }
        
        $tfootAnios = "";
        foreach ($aniosPanel as $a) {
            $valTot = $panelData['tipo'] !== 'retencion' ? number_format($totalesPorAnio[$a], 0, '', '.') : '—';
            $tfootAnios .= "<td style='text-align:center;'>{$valTot}</td>";
        }
        
        $panelDetalleHtml = "
        <div class='section-title'>{$tituloPanel}</div>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    {$thAnios}
                </tr>
            </thead>
            <tbody>
                {$filasPanel}
            </tbody>
            <tfoot>
                <tr class='tfoot-total'>
                    <td>Total</td>
                    {$tfootAnios}
                </tr>
            </tfoot>
        </table>";
    }
}

// Registro mensual y agrupaciones (Fase 2)
$seccionMensualHtml = "";
if (!empty($tablaMensual)) {
    $totalNuevos = 0;
    $totalActivos = 0;
    $totalRetirados = 0;
    $totalAcumuladoFinal = 0;
    $acumulado = 0;

    $trimestres = [
        1 => ['label' => 'Trimestre I (Ene-Mar)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0],
        2 => ['label' => 'Trimestre II (Abr-Jun)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0],
        3 => ['label' => 'Trimestre III (Jul-Sep)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0],
        4 => ['label' => 'Trimestre IV (Oct-Dic)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0]
    ];

    $semestres = [
        1 => ['label' => 'Semestre I (Ene-Jun)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0],
        2 => ['label' => 'Semestre II (Jul-Dic)', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0]
    ];

    $anual = ['label' => 'Total Anual', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0];
    
    $filasMensual = "";
    foreach ($tablaMensual as $idx => $m) {
        $mesNom = $m['mes'] ?? 'Mes ' . ($idx + 1);
        $numMes = intval($m['mesNumero'] ?? ($idx + 1));
        
        $n = floatval($m['nuevos'] ?? 0);
        $a = floatval($m['activos'] ?? 0);
        $r = floatval($m['retirados'] ?? 0);
        $sd = !isset($m['nuevos']) || $m['nuevos'] === null;
        
        if (!$sd) {
            $acumulado += $n;
            $totalNuevos += $n;
            $totalActivos += $a;
            $totalRetirados += $r;
            $totalAcumuladoFinal = $acumulado;
            
            $q = (int)ceil($numMes / 3);
            if (isset($trimestres[$q])) {
                $trimestres[$q]['nuevos'] += $n;
                $trimestres[$q]['activos'] += $a;
                $trimestres[$q]['retirados'] += $r;
            }
            
            $s = $numMes <= 6 ? 1 : 2;
            if (isset($semestres[$s])) {
                $semestres[$s]['nuevos'] += $n;
                $semestres[$s]['activos'] += $a;
                $semestres[$s]['retirados'] += $r;
            }
            
            $anual['nuevos'] += $n;
            $anual['activos'] += $a;
            $anual['retirados'] += $r;
        }
        
        $txtN = $sd ? '—' : number_format($n, 0, '', '.');
        $txtA = $sd ? '—' : number_format($a, 0, '', '.');
        $txtR = $sd ? '—' : number_format($r, 0, '', '.');
        $txtAcum = $sd ? '—' : number_format($acumulado, 0, '', '.');
        
        $filasMensual .= "<tr>
            <td><strong>{$mesNom}</strong></td>
            <td style='text-align:center;'>{$txtN}</td>
            <td style='text-align:center;'>{$txtA}</td>
            <td style='text-align:center;'>{$txtR}</td>
            <td style='text-align:center;'>{$txtAcum}</td>
        </tr>";
    }

    $tablaAgrupacionesHtml = "";
    foreach ($trimestres as $t) {
        $tablaAgrupacionesHtml .= "<tr>
            <td>{$t['label']}</td>
            <td style='text-align:center;'>".number_format($t['nuevos'], 0, '', '.')."</td>
            <td style='text-align:center;'>".number_format($t['activos'], 0, '', '.')."</td>
            <td style='text-align:center;'>".number_format($t['retirados'], 0, '', '.')."</td>
        </tr>";
    }
    $tablaAgrupacionesHtml .= "<tr style='background-color:#f8fafc; height:4px; font-size:1px;'><td colspan='4' style='padding:0; border:1px solid #cbd5e1; background-color:#f1f5f9;'></td></tr>";
    foreach ($semestres as $s) {
        $tablaAgrupacionesHtml .= "<tr>
            <td>{$s['label']}</td>
            <td style='text-align:center;'>".number_format($s['nuevos'], 0, '', '.')."</td>
            <td style='text-align:center;'>".number_format($s['activos'], 0, '', '.')."</td>
            <td style='text-align:center;'>".number_format($s['retirados'], 0, '', '.')."</td>
        </tr>";
    }
    $tablaAgrupacionesHtml .= "<tr style='background-color:#f8fafc; height:4px; font-size:1px;'><td colspan='4' style='padding:0; border:1px solid #cbd5e1; background-color:#f1f5f9;'></td></tr>";
    $tablaAgrupacionesHtml .= "<tr class='tfoot-total'>
        <td>{$anual['label']}</td>
        <td style='text-align:center;'>".number_format($anual['nuevos'], 0, '', '.')."</td>
        <td style='text-align:center;'>".number_format($anual['activos'], 0, '', '.')."</td>
        <td style='text-align:center;'>".number_format($anual['retirados'], 0, '', '.')."</td>
    </tr>";

    $seccionMensualHtml = "
    <div class='page-break'></div>
    <div class='section-title'>Registro Mensual de Participantes</div>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th style='text-align:center;'>Nuevos Inscritos</th>
                <th style='text-align:center;'>Activos</th>
                <th style='text-align:center;'>Retirados / Baja</th>
                <th style='text-align:center;'>Total Acumulado</th>
            </tr>
        </thead>
        <tbody>
            {$filasMensual}
        </tbody>
        <tfoot>
            <tr class='tfoot-total'>
                <td>Total</td>
                <td style='text-align:center;'>".number_format($totalNuevos, 0, '', '.')."</td>
                <td style='text-align:center;'>".number_format($totalActivos, 0, '', '.')."</td>
                <td style='text-align:center;'>".number_format($totalRetirados, 0, '', '.')."</td>
                <td style='text-align:center;'>".number_format($totalAcumuladoFinal, 0, '', '.')."</td>
            </tr>
        </tfoot>
    </table>
    
    <div class='section-title' style='margin-top:15px;'>Resumen de Agrupaciones (Trimestral, Semestral y Anual)</div>
    <table>
        <thead>
            <tr>
                <th>Período de Agrupación</th>
                <th style='text-align:center;'>Nuevos Inscritos</th>
                <th style='text-align:center;'>Activos</th>
                <th style='text-align:center;'>Retirados / Baja</th>
            </tr>
        </thead>
        <tbody>
            {$tablaAgrupacionesHtml}
        </tbody>
    </table>";
}

// Chart
$chartHtml = "";
if (!empty($chartImageBase64) && strpos($chartImageBase64, 'data:image') === 0) {
    $chartHtml = "
    <div class='page-break'></div>
    <div class='section-title'>Gráfico: Total de Participantes</div>
    <div style='text-align:center; margin-top:10px;'>
        <img src='{$chartImageBase64}' style='width:100%; max-height:280px; border:1px solid #cbd5e1; border-radius:6px;'>
    </div>";
}

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Reporte_Total_Participantes</title>
<style>
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #334155; margin: 0; padding: 0; }
    .header { width: 100%; border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 15px; }
    .header td { vertical-align: middle; }
    .logo { width: 120px; }
    .title { font-size: 18px; font-weight: bold; color: #0d6efd; margin-bottom: 3px; }
    .subtitle { font-size: 12px; color: #475569; margin-bottom: 2px; }
    .info { text-align: right; font-size: 9px; color: #64748b; line-height: 1.3; }
    
    .section-title { font-size: 12px; font-weight: bold; color: #0d6efd; margin-top: 15px; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .filter-box { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; font-size: 10px; }
    .filter-box strong { color: #856404; }
    
    /* KPI Cards layout */
    .kpis-container { width: 100%; margin-bottom: 15px; display: block; height: 50px; }
    .kpi-card { width: 18.8%; float: left; margin-right: 1.5%; background: #f8fafc; padding: 8px 5px; border-radius: 6px; border: 1px solid #e2e8f0; box-sizing: border-box; text-align: center; height: 48px; }
    .kpi-card:last-child { margin-right: 0; }
    .kpi-card.info { border-left: 3px solid #0dcaf0; }
    .kpi-card.primary { border-left: 3px solid #0d6efd; }
    .kpi-card.success { border-left: 3px solid #198754; }
    .kpi-card.danger { border-left: 3px solid #dc3545; }
    .kpi-card.warning { border-left: 3px solid #ffc107; }
    
    .kpi-val { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
    .kpi-card.info .kpi-val { color: #079ab8; }
    .kpi-card.primary .kpi-val { color: #0a4fc4; }
    .kpi-card.success .kpi-val { color: #0f6840; }
    .kpi-card.danger .kpi-val { color: #b02a37; }
    .kpi-card.warning .kpi-val { color: #997404; }
    
    .kpi-title { font-size: 8px; font-weight: bold; color: #64748b; text-transform: uppercase; line-height: 1; }
    
    /* Tables */
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
    th { background-color: #f1f5f9; color: #1e293b; padding: 6px 8px; text-align: left; font-weight: bold; border: 1px solid #cbd5e1; text-transform: uppercase; font-size: 9px; }
    td { border: 1px solid #cbd5e1; padding: 6px 8px; }
    tr:nth-child(even) td { background-color: #f8fafc; }
    .tfoot-total td { background-color: #f1f5f9 !important; font-weight: bold; color: #0f172a; border-top: 2px solid #94a3b8; }
    
    .page-break { page-break-after: always; }
</style>
</head>
<body>
    <!-- HEADER -->
    <table class='header' style='border:none; margin:0;'>
        <tr style='background:none;'>
            <td style='width: 25%; border:none; background:none;'><img src='{$logoSrc}' class='logo'></td>
            <td style='width: 50%; border:none; text-align:center; background:none;'>
                <div class='title'>Sistema de Gestión INCES</div>
                <div class='subtitle' style='font-weight:bold; font-size:13px;'>Reporte: Total de Participantes</div>
                <div class='subtitle'>Año Base Activo: {$anioBase}</div>
            </td>
            <td style='width: 25%; border:none; background:none;' class='info'>
                Generado por: {$usuarioFull}<br>
                Fecha: " . date('d/m/Y H:i') . "
            </td>
        </tr>
    </table>

    <!-- FILTROS -->
    {$filtrosHtml}

    <!-- KPIs -->
    <div class='section-title'>Resumen General</div>
    <div class='kpis-container'>
        <div class='kpi-card info'>
            <div class='kpi-val'>{$totalSistemaValor}</div>
            <div class='kpi-title'>Total en Sistema</div>
        </div>
        <div class='kpi-card primary'>
            <div class='kpi-val'>{$nuevosVal}</div>
            <div class='kpi-title'>Nuevos Particip.</div>
        </div>
        <div class='kpi-card success'>
            <div class='kpi-val'>{$activosVal}</div>
            <div class='kpi-title'>Activos Actual</div>
        </div>
        <div class='kpi-card danger'>
            <div class='kpi-val'>{$retiradosVal}</div>
            <div class='kpi-title'>Finalizaron / Baja</div>
        </div>
        <div class='kpi-card warning'>
            <div class='kpi-val'>{$retencionVal}%</div>
            <div class='kpi-title'>Tasa Retencion</div>
        </div>
    </div>

    <!-- DISTRIBUCIÓN ESTADOS -->
    <div class='section-title'>Distribución por Estado Actual</div>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th style='text-align:center;'>Cantidad Total</th>
                <th style='text-align:center;'>Filtrado Actualmente</th>
            </tr>
        </thead>
        <tbody>
            {$tablaEstadosHtml}
        </tbody>
    </table>

    <!-- DETALLE KPI SELECCIONADO (Si aplica) -->
    {$panelDetalleHtml}

    <!-- REGISTRO MENSUAL Y AGRUPACIONES (PÁGINA 2) -->
    {$seccionMensualHtml}

    <!-- GRÁFICO (PÁGINA 3) -->
    {$chartHtml}

    <script type='text/php'>
    if (isset(\$pdf)) {
        \$text = 'Sistema de Gestión INCES | Fecha de generación: " . date('d/m/Y H:i') . " | Página ' . \$PAGE_NUM . ' de ' . \$PAGE_COUNT;
        \$font = \$fontMetrics->get_font('Helvetica', 'normal');
        \$pdf->page_text(160, 815, \$text, \$font, 7.5, array(0.4,0.4,0.4));
    }
    </script>
</body>
</html>";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
if (ob_get_length()) ob_end_clean();
$dompdf->stream('Reporte_Total_Participantes.pdf', ['Attachment' => false]);
?>

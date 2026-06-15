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

$usuarioFull = 'Usuario no identificado';
if (isset($_SESSION['id_usuario'])) {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
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
    } catch (Exception $e) {}
}

$chartImageBase64 = $_POST['chart_image'] ?? '';
$payloadRaw = $_POST['payload_json'] ?? '{}';
$payload = json_decode($payloadRaw, true);
if (!$payload) $payload = [];

$anio = $payload['anio'] ?? date('Y');
$datosAmbito = $payload['datosAmbito'] ?? [];
$datosAmbitoAnioAnterior = $payload['datosAmbitoAnioAnterior'] ?? [];

// Calculate KPIs
$totalAmbitos = count($datosAmbito);
$totalPart = 0; $totalAprobados = 0; $totalFormacion = 0; $totalRetirados = 0;
foreach ($datosAmbito as $d) {
    $totalPart += $d['total'];
    $totalAprobados += $d['aprobados'];
    $totalFormacion += $d['en_formacion'];
    $totalRetirados += $d['retirados'];
}

$totalAnterior = 0;
foreach ($datosAmbitoAnioAnterior as $d) {
    $totalAnterior += $d['total'];
}
$diferencia = $totalPart - $totalAnterior;
$difStr = ($diferencia > 0 ? '+' : '') . number_format($diferencia, 0, '', '.');

// Resumen ejecutivo calculations
$maxPart = ['ambito' => '-', 'val' => 0];
$maxAprob = ['ambito' => '-', 'val' => 0];
$maxTasa = ['ambito' => '-', 'val' => 0];
$maxCrecimiento = ['ambito' => '-', 'val' => -999999];

$tablaHtml = "";
$totAnt = 0;

usort($datosAmbito, function($a, $b) {
    return $b['total'] <=> $a['total'];
});

$rankingHtml = "";
$interanualHtml = "";

foreach ($datosAmbito as $idx => $d) {
    if ($d['total'] > $maxPart['val']) { $maxPart = ['ambito' => $d['ambito'], 'val' => $d['total']]; }
    if ($d['aprobados'] > $maxAprob['val']) { $maxAprob = ['ambito' => $d['ambito'], 'val' => $d['aprobados']]; }
    
    $tasa = $d['total'] > 0 ? round(($d['aprobados'] / $d['total']) * 100, 1) : 0;
    if ($tasa > $maxTasa['val']) { $maxTasa = ['ambito' => $d['ambito'], 'val' => $tasa]; }

    $ant = 0;
    foreach ($datosAmbitoAnioAnterior as $a) {
        if ($a['ambito'] === $d['ambito']) { $ant = $a['total']; break; }
    }
    $totAnt += $ant;
    
    $dif = $d['total'] - $ant;
    if ($dif > $maxCrecimiento['val']) { $maxCrecimiento = ['ambito' => $d['ambito'], 'val' => $dif]; }

    // Tasa color
    $tasaColor = '#dc3545';
    if ($tasa >= 70) $tasaColor = '#198754';
    elseif ($tasa >= 50) $tasaColor = '#ffc107';

    $difColor = $dif > 0 ? '#198754' : ($dif < 0 ? '#dc3545' : '#6c757d');
    $difSigno = $dif > 0 ? '+' : '';

    $tablaHtml .= "<tr>
        <td>{$d['ambito']}</td>
        <td style='text-align:right'>" . number_format($d['total'], 0, '', '.') . "</td>
        <td style='text-align:right'>" . number_format($d['aprobados'], 0, '', '.') . "</td>
        <td style='text-align:right'>" . number_format($d['en_formacion'], 0, '', '.') . "</td>
        <td style='text-align:right'>" . number_format($d['retirados'], 0, '', '.') . "</td>
        <td style='text-align:right'>" . number_format($ant, 0, '', '.') . "</td>
        <td style='text-align:right; color: {$difColor}'>{$difSigno}" . number_format($dif, 0, '', '.') . "</td>
        <td style='text-align:center; color: white; font-weight:bold;'><span style='background-color: {$tasaColor}; padding: 2px 6px; border-radius: 4px;'>{$tasa}%</span></td>
    </tr>";

    $rankingHtml .= "<tr>
        <td>" . ($idx + 1) . "</td>
        <td>{$d['ambito']}</td>
        <td style='text-align:right'>" . number_format($d['total'], 0, '', '.') . "</td>
    </tr>";

    $interanualHtml .= "<tr>
        <td>{$d['ambito']}</td>
        <td style='text-align:right'>" . number_format($d['total'], 0, '', '.') . "</td>
        <td style='text-align:right'>" . number_format($ant, 0, '', '.') . "</td>
        <td style='text-align:right; color: {$difColor}'>{$difSigno}" . number_format($dif, 0, '', '.') . "</td>
    </tr>";
}

$totDif = $totalPart - $totAnt;
$difColorTot = $totDif > 0 ? '#198754' : ($totDif < 0 ? '#dc3545' : '#6c757d');
$difSignoTot = $totDif >= 0 ? '+' : '';

$tasaTotal = $totalPart > 0 ? round(($totalAprobados / $totalPart) * 100, 1) : 0;
$tasaColorTot = '#dc3545';
if ($tasaTotal >= 70) $tasaColorTot = '#198754';
elseif ($tasaTotal >= 50) $tasaColorTot = '#ffc107';

$tablaHtml .= "<tr style='font-weight:bold; background-color: #e9ecef'>
    <td>Totales</td>
    <td style='text-align:right'>" . number_format($totalPart, 0, '', '.') . "</td>
    <td style='text-align:right'>" . number_format($totalAprobados, 0, '', '.') . "</td>
    <td style='text-align:right'>" . number_format($totalFormacion, 0, '', '.') . "</td>
    <td style='text-align:right'>" . number_format($totalRetirados, 0, '', '.') . "</td>
    <td style='text-align:right'>" . number_format($totAnt, 0, '', '.') . "</td>
    <td style='text-align:right; color: {$difColorTot}'>{$difSignoTot}" . number_format($totDif, 0, '', '.') . "</td>
    <td style='text-align:center; color: white;'><span style='background-color: {$tasaColorTot}; padding: 2px 6px; border-radius: 4px;'>{$tasaTotal}%</span></td>
</tr>";

// Build HTML
$logoPath = __DIR__ . '/../../../../assets/img/inces-logo.png';
$logoData = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,'.$logoData;

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Reporte_Participantes_Ambito</title>
<style>
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
    .header { width: 100%; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
    .header td { vertical-align: middle; }
    .logo { width: 130px; }
    .title { text-align: center; font-size: 20px; font-weight: bold; color: #0056b3; margin-bottom: 4px; }
    .subtitle { text-align: center; font-size: 14px; color: #555; margin-bottom: 2px; }
    .info { text-align: right; font-size: 10px; color: #777; line-height: 1.4; }
    
    .kpi-container { width: 100%; margin-bottom: 20px; text-align: center; }
    .kpi-box { width: 14%; display: inline-block; text-align: center; padding: 12px 5px; margin: 0 0.5%; background: #f8f9fa; border-top: 3px solid #0056b3; border-radius: 4px; box-sizing: border-box; }
    .kpi-val { font-size: 16px; font-weight: bold; margin-bottom: 6px; color: #333; }
    .kpi-label { font-size: 9px; text-transform: uppercase; color: #666; font-weight: bold; }
    
    .chart-container { text-align: center; margin: 20px 0; }
    .chart { max-width: 500px; height: auto; display: inline-block; }
    
    .summary-box { background: #f1f3f5; padding: 15px; border-left: 4px solid #0056b3; margin-bottom: 20px; }
    .summary-title { font-weight: bold; margin-bottom: 12px; font-size: 14px; color: #0056b3; text-transform: uppercase; }
    .summary-item { margin-bottom: 6px; font-size: 12px; }
    .summary-item strong { display: inline-block; width: 220px; color: #495057; }
    
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
    th { background-color: #0056b3; color: white; padding: 10px 8px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; }
    td { border-bottom: 1px solid #dee2e6; padding: 10px 8px; }
    tr:nth-child(even) td { background-color: #f8f9fa; }
    
    .section-title { font-size: 16px; font-weight: bold; color: #0056b3; margin-bottom: 15px; border-bottom: 1px solid #dee2e6; padding-bottom: 5px; }
    
    .page-break { page-break-after: always; }
</style>
</head>
<body>
    <!-- HEADER -->
    <table class='header'>
        <tr>
            <td style='width: 25%'><img src='{$logoSrc}' class='logo'></td>
            <td style='width: 50%'>
                <div class='title'>Sistema de Gestión INCES</div>
                <div class='subtitle'>Reporte Participantes por Ámbito</div>
                <div class='subtitle'>Año Seleccionado: {$anio}</div>
            </td>
            <td style='width: 25%' class='info'>
                Generado por: {$usuarioFull}<br>
                Fecha: " . date('d/m/Y H:i') . "
            </td>
        </tr>
    </table>

    <!-- PÁGINA 1: KPIs y Gráfico -->
    <div class='kpi-container'>
        <div class='kpi-box' style='border-top-color: #6c757d'>
            <div class='kpi-val'>{$totalAmbitos}</div>
            <div class='kpi-label'>Total Ámbitos</div>
        </div>
        <div class='kpi-box' style='border-top-color: #0d6efd'>
            <div class='kpi-val'>" . number_format($totalPart, 0, '', '.') . "</div>
            <div class='kpi-label'>Participantes</div>
        </div>
        <div class='kpi-box' style='border-top-color: #198754'>
            <div class='kpi-val'>" . number_format($totalAprobados, 0, '', '.') . "</div>
            <div class='kpi-label'>Aprobados</div>
        </div>
        <div class='kpi-box' style='border-top-color: #0dcaf0'>
            <div class='kpi-val'>" . number_format($totalFormacion, 0, '', '.') . "</div>
            <div class='kpi-label'>En Formación</div>
        </div>
        <div class='kpi-box' style='border-top-color: #dc3545'>
            <div class='kpi-val'>" . number_format($totalRetirados, 0, '', '.') . "</div>
            <div class='kpi-label'>Retirados</div>
        </div>
        <div class='kpi-box' style='border-top-color: " . ($diferencia > 0 ? '#198754' : ($diferencia < 0 ? '#dc3545' : '#6c757d')) . "'>
            <div class='kpi-val'>{$difStr}</div>
            <div class='kpi-label'>Variación Anual</div>
        </div>
    </div>

    <div class='chart-container'>";
if (!empty($chartImageBase64)) {
    $html .= "<img src='{$chartImageBase64}' class='chart'>";
}
$html .= "</div>

    <div class='summary-box'>
        <div class='summary-title'>Resumen Ejecutivo</div>
        <div class='summary-item'><strong>Ámbito con más participantes:</strong> {$maxPart['ambito']} (" . number_format($maxPart['val'], 0, '', '.') . ")</div>
        <div class='summary-item'><strong>Ámbito con más aprobados:</strong> {$maxAprob['ambito']} (" . number_format($maxAprob['val'], 0, '', '.') . ")</div>
        <div class='summary-item'><strong>Mejor tasa de aprobación:</strong> {$maxTasa['ambito']} ({$maxTasa['val']}%)</div>
        <div class='summary-item'><strong>Mayor crecimiento anual:</strong> {$maxCrecimiento['ambito']} (" . ($maxCrecimiento['val'] > 0 ? '+' : '') . number_format($maxCrecimiento['val'], 0, '', '.') . ")</div>
    </div>

    <div class='page-break'></div>

    <!-- PÁGINA 2: Tabla Detallada -->
    <div class='section-title'>Detalle por Ámbito</div>
    <table>
        <thead>
            <tr>
                <th>Ámbito</th>
                <th style='text-align:right'>Total</th>
                <th style='text-align:right'>Aprobados</th>
                <th style='text-align:right'>En Form.</th>
                <th style='text-align:right'>Retirados</th>
                <th style='text-align:right'>Año Anterior</th>
                <th style='text-align:right'>Diferencia</th>
                <th style='text-align:center'>Tasa Aprob.</th>
            </tr>
        </thead>
        <tbody>
            {$tablaHtml}
        </tbody>
    </table>

    <div class='page-break'></div>

    <!-- PÁGINA 3: Ranking y Comparación -->
    <div class='section-title'>Ranking de Ámbitos por Participantes</div>
    <table>
        <thead>
            <tr>
                <th style='width: 15%'>Posición</th>
                <th style='width: 55%'>Ámbito</th>
                <th style='width: 30%; text-align:right'>Total Participantes</th>
            </tr>
        </thead>
        <tbody>
            {$rankingHtml}
        </tbody>
    </table>

    <div style='margin-top: 40px;' class='section-title'>Comparación Interanual</div>
    <table>
        <thead>
            <tr>
                <th style='width: 40%'>Ámbito</th>
                <th style='width: 20%; text-align:right'>Total Año Actual</th>
                <th style='width: 20%; text-align:right'>Total Año Anterior</th>
                <th style='width: 20%; text-align:right'>Variación</th>
            </tr>
        </thead>
        <tbody>
            {$interanualHtml}
        </tbody>
    </table>

    <!-- FOOTER Paginacion (Dompdf) -->
    <script type='text/php'>
    if (isset(\$pdf)) {
        \$text = 'Sistema de Gestión INCES - Página ' . \$PAGE_NUM . ' de ' . \$PAGE_COUNT;
        \$font = \$fontMetrics->get_font('Helvetica', 'italic');
        // x, y, text, font, size, color(RGB 0-1)
        \$pdf->page_text(240, 810, \$text, \$font, 8, array(0.3,0.3,0.3));
    }
    </script>
</body>
</html>
";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
if (ob_get_length()) ob_end_clean();
$dompdf->stream('Reporte_Participantes_Ambito.pdf', ['Attachment' => false]);
?>

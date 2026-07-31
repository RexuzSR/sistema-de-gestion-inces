<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
require_once __DIR__ . '/../../dompdf/autoload.inc.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
if (isset($_SESSION['id_usuario'])) {
    require_once __DIR__ . '/../../../modelo/HistorialUtil.php';
    HistorialUtil::registrarReporte('Total de Participantes', $_SESSION['id_usuario']);
}
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
        <strong>Filtros Activos Aplicados:</strong> " . htmlspecialchars(implode(' · ', $filtrosActivos)) . "
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
if ($activosVal == 0 && isset($datosEstadosSistema['estados']['En formacion']) && $datosEstadosSistema['estados']['En formacion'] > 0) {
    $activosVal = $datosEstadosSistema['estados']['En formacion'];
}
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
    $txtFiltered = $isFiltered ? "<span style='color:#16a34a; font-weight:bold;'>SÍ</span>" : "<span style='color:#64748b;'>NO</span>";
    
    $tablaEstadosHtml .= "<tr>
        <td>{$label}</td>
        <td style='text-align:center; font-weight:bold;'>".number_format($val, 0, '', '.')."</td>
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

// Registro mensual y agrupaciones
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

    $anual = ['label' => 'Total Anual Consolidado', 'nuevos' => 0, 'activos' => 0, 'retirados' => 0];
    
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
    
    <div class='section-title' style='margin-top:15px;'>Resumen de Agrupaciones Estratégicas (Trimestral, Semestral y Anual)</div>
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
    $chartHtml = "<div class='page-break'></div>
    <div class='section-title'>Gráfico Comparativo de Matriculación</div>
    <div class='chart-box'>
        <img src='{$chartImageBase64}' alt='Gráfico Comparativo'>
    </div>";
}

// ── Insights Administrativos para PDF ──
$mesPicoNombre = '—';
$mesPicoValor = 0;
$promedioMensual = '0,0';
$activosFormacion = $datosEstadosSistema['estados']['En formacion'] ?? 0;
$totalReg = ($datosEstadosSistema['total'] ?? 0) > 0 ? $datosEstadosSistema['total'] : 1;
$pctFormacionActiva = number_format(($activosFormacion / $totalReg) * 100, 1, ',', '.');

$demoGlobal = $datosEstadosSistema['demo'] ?? ['masculino' => 0, 'femenino' => 0, 'conDisc' => 0, 'sinDisc' => 0];
$masculinoVal = $demoGlobal['masculino'] ?? 0;
$femeninoVal = $demoGlobal['femenino'] ?? 0;
$conDiscVal = $demoGlobal['conDisc'] ?? 0;
$sinDiscVal = $demoGlobal['sinDisc'] ?? 0;

$pctMasculino = number_format(($masculinoVal / $totalReg) * 100, 1, ',', '.');
$pctFemenino = number_format(($femeninoVal / $totalReg) * 100, 1, ',', '.');
$pctConDisc = number_format(($conDiscVal / $totalReg) * 100, 1, ',', '.');
$pctSinDisc = number_format(($sinDiscVal / $totalReg) * 100, 1, ',', '.');

if (!empty($tablaMensual)) {
    $sumaNuevos = 0;
    $cantMesesConDatos = 0;
    foreach ($tablaMensual as $m) {
        if (isset($m['nuevos']) && $m['nuevos'] !== null) {
            $n = floatval($m['nuevos']);
            $sumaNuevos += $n;
            $cantMesesConDatos++;
            if ($n > $mesPicoValor) {
                $mesPicoValor = $n;
                $mesPicoNombre = $m['mes'] ?? '—';
            }
        }
    }
    if ($cantMesesConDatos > 0) {
        $promedioMensual = number_format($sumaNuevos / $cantMesesConDatos, 1, ',', '.');
    }
}

$pathLogoGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'anioBase' => $anioBase,
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'filtrosHtml' => $filtrosHtml,
    'totalSistemaValor' => $totalSistemaValor,
    'nuevosVal' => $nuevosVal,
    'activosVal' => $activosVal,
    'retiradosVal' => $retiradosVal,
    'retencionVal' => $retencionVal,
    'mesPicoNombre' => $mesPicoNombre,
    'mesPicoValor' => $mesPicoValor,
    'promedioMensual' => $promedioMensual,
    'activosFormacion' => $activosFormacion,
    'pctFormacionActiva' => $pctFormacionActiva,
    'masculinoVal' => $masculinoVal,
    'femeninoVal' => $femeninoVal,
    'conDiscVal' => $conDiscVal,
    'sinDiscVal' => $sinDiscVal,
    'pctMasculino' => $pctMasculino,
    'pctFemenino' => $pctFemenino,
    'pctConDisc' => $pctConDisc,
    'pctSinDisc' => $pctSinDisc,
    'tablaEstadosHtml' => $tablaEstadosHtml,
    'panelDetalleHtml' => $panelDetalleHtml,
    'seccionMensualHtml' => $seccionMensualHtml,
    'chartHtml' => $chartHtml
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/total_participantes.php';
$html = ob_get_clean();

$nombreArchivo = 'Reporte_Total_Participantes_' . $anioBase . '.pdf';
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . htmlspecialchars(str_replace('.pdf', '', $nombreArchivo)) . '</title>', $html);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

if (ob_get_length()) ob_end_clean();
$dompdf->stream($nombreArchivo, ['Attachment' => false]);

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
}$conn = null;
try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
} catch (Exception $e) {}

$usuarioFull = 'Usuario no identificado';
if (isset($_SESSION['id_usuario']) && $conn) {
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
$ambitoFiltro = $payload['ambitoFiltro'] ?? 'todos';
$datosAmbito = $payload['datosAmbito'] ?? [];
$datosAmbitoAnioAnterior = $payload['datosAmbitoAnioAnterior'] ?? [];

if (!function_exists('inicializarMesesAmbito')) {
    function inicializarMesesAmbito(int $anio): array {
        $nombres  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                     'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $hoyAnio  = intval(date('Y'));
        $hoyMes   = intval(date('n'));
        $meses    = [];
        for ($i = 1; $i <= 12; $i++) {
            $esFuturo = ($anio > $hoyAnio) || ($anio === $hoyAnio && $i > $hoyMes);
            $meses[$i] = [
                'mes'            => $nombres[$i - 1],
                'mesNumero'      => $i,
                'nuevos'         => $esFuturo ? null : 0,
                'activos'        => $esFuturo ? null : 0,
                'retirados'      => $esFuturo ? null : 0,
                'totalAcumulado' => $esFuturo ? null : 0
            ];
        }
        return $meses;
    }
}

if (!function_exists('getMonthlyDataAmbito')) {
    function getMonthlyDataAmbito(PDO $conn, int $anio, string $ambitoFiltro): array {
        $meses = inicializarMesesAmbito($anio);
        
        // 1. Nuevos
        $sqlNuevos = "
            SELECT MONTH(pc.fecha_inscripcion) AS mes, COUNT(DISTINCT pc.id_participante) AS total
            FROM tb_participante_curso pc
            JOIN tb_curso c ON c.id_curso = pc.id_curso
            JOIN tb_ambito a ON a.id_ambito = c.id_ambito
            WHERE YEAR(pc.fecha_inscripcion) = :anio
        ";
        if ($ambitoFiltro !== 'todos') {
            $sqlNuevos .= " AND a.nombre_ambito = :ambito ";
        }
        $sqlNuevos .= " GROUP BY MONTH(pc.fecha_inscripcion)";
        
        try {
            $stmt = $conn->prepare($sqlNuevos);
            $params = [':anio' => $anio];
            if ($ambitoFiltro !== 'todos') {
                $params[':ambito'] = $ambitoFiltro;
            }
            $stmt->execute($params);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $m = intval($row['mes']);
                if (isset($meses[$m])) {
                    $meses[$m]['nuevos'] = intval($row['total']);
                }
            }
        } catch (Exception $e) {}
        
        // 2. Activos
        $sqlActivos = "
            SELECT meses.mes_activo AS mes, COUNT(DISTINCT pc.id_participante) AS total
            FROM (SELECT 1 AS mes_activo UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) meses
            JOIN tb_participante_curso pc
              ON pc.FechaInicio <= LAST_DAY(MAKEDATE(:anio1, 1) + INTERVAL (meses.mes_activo - 1) MONTH)
             AND pc.FechaFin >= DATE_FORMAT(MAKEDATE(:anio2, 1) + INTERVAL (meses.mes_activo - 1) MONTH, '%Y-%m-01')
             AND pc.estatus_participante = 'En formación'
            JOIN tb_curso c ON c.id_curso = pc.id_curso
            JOIN tb_ambito a ON a.id_ambito = c.id_ambito
            WHERE YEAR(pc.FechaInicio) <= :anio3 AND YEAR(pc.FechaFin) >= :anio4
        ";
        if ($ambitoFiltro !== 'todos') {
            $sqlActivos .= " AND a.nombre_ambito = :ambito ";
        }
        $sqlActivos .= " GROUP BY meses.mes_activo";
        
        try {
            $stmt = $conn->prepare($sqlActivos);
            $paramsActivos = [
                ':anio1' => $anio,
                ':anio2' => $anio,
                ':anio3' => $anio,
                ':anio4' => $anio
            ];
            if ($ambitoFiltro !== 'todos') {
                $paramsActivos[':ambito'] = $ambitoFiltro;
            }
            $stmt->execute($paramsActivos);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $m = intval($row['mes']);
                if (isset($meses[$m])) {
                    $meses[$m]['activos'] = intval($row['total']);
                }
            }
        } catch (Exception $e) {}
        
        // 3. Retirados
        $sqlRetirados = "
            SELECT MONTH(pc.FechaFin) AS mes, COUNT(DISTINCT pc.id_participante) AS total
            FROM tb_participante_curso pc
            JOIN tb_curso c ON c.id_curso = pc.id_curso
            JOIN tb_ambito a ON a.id_ambito = c.id_ambito
            WHERE YEAR(pc.FechaFin) = :anio AND pc.estatus_participante IN ('Ausente', 'Reprobado')
        ";
        if ($ambitoFiltro !== 'todos') {
            $sqlRetirados .= " AND a.nombre_ambito = :ambito ";
        }
        $sqlRetirados .= " GROUP BY MONTH(pc.FechaFin)";
        
        try {
            $stmt = $conn->prepare($sqlRetirados);
            $paramsRetirados = [':anio' => $anio];
            if ($ambitoFiltro !== 'todos') {
                $paramsRetirados[':ambito'] = $ambitoFiltro;
            }
            $stmt->execute($paramsRetirados);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $m = intval($row['mes']);
                if (isset($meses[$m])) {
                    $meses[$m]['retirados'] = intval($row['total']);
                }
            }
        } catch (Exception $e) {}
        
        // Compute totalAcumulado
        $acumulado = 0;
        $resultado = [];
        foreach ($meses as $m) {
            if ($m['nuevos'] !== null) {
                $acumulado += $m['nuevos'];
                $m['totalAcumulado'] = $acumulado;
            }
            $resultado[] = $m;
        }
        return $resultado;
    }
}

$tablaMensual = [];
if ($conn) {
    $tablaMensual = getMonthlyDataAmbito($conn, intval($anio), $ambitoFiltro);
}

// Compute Monthly & Grouped Aggregations
$acumulado = 0;
$totalNuevos = 0;
$totalActivos = 0;
$totalRetirados = 0;
$totalAcumuladoFinal = 0;

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
$tablaAgrupacionesHtml .= "<tr style='font-weight:bold; background-color: #e9ecef'>
    <td>{$anual['label']}</td>
    <td style='text-align:center;'>".number_format($anual['nuevos'], 0, '', '.')."</td>
    <td style='text-align:center;'>".number_format($anual['activos'], 0, '', '.')."</td>
    <td style='text-align:center;'>".number_format($anual['retirados'], 0, '', '.')."</td>
</tr>";

$seccionMensualHtml = "";
if (!empty($tablaMensual)) {
    $tituloMensual = "Registro Mensual de Participantes";
    if ($ambitoFiltro !== 'todos') {
        $tituloMensual .= " (Ámbito: " . htmlspecialchars($ambitoFiltro, ENT_QUOTES, 'UTF-8') . ")";
    }
    
    $seccionMensualHtml = "
    <div class='page-break'></div>
    <div class='section-title'>{$tituloMensual}</div>
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
            <tr style='font-weight:bold; background-color: #e9ecef'>
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
$logoPathGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$logoPathInces = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($logoPathGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPathGobierno)) : '';
$logoInces = file_exists($logoPathInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPathInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'anio' => $anio,
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'totalAmbitos' => $totalAmbitos,
    'totalPart' => $totalPart,
    'totalAprobados' => $totalAprobados,
    'totalFormacion' => $totalFormacion,
    'totalRetirados' => $totalRetirados,
    'difStr' => $difStr,
    'diferencia' => $diferencia,
    'maxPart' => $maxPart,
    'maxAprob' => $maxAprob,
    'maxTasa' => $maxTasa,
    'maxCrecimiento' => $maxCrecimiento,
    'tablaHtml' => $tablaHtml,
    'rankingHtml' => $rankingHtml,
    'interanualHtml' => $interanualHtml,
    'seccionMensualHtml' => $seccionMensualHtml,
    'chartImageBase64' => $chartImageBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/participantes_ambito.php';
$html = ob_get_clean();

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

<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// pdf_meta_por_ambito.php
// Controlador PDF - Informe Gerencial "Metas por Ámbito"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

$chartBase64 = $_POST['chart_image'] ?? '';
$payloadJson = $_POST['payload_json'] ?? '{}';
$data = json_decode($payloadJson, true);

if (!$data) {
    die("Datos inválidos.");
}

// Cargar sesión y obtener nombre del usuario generador
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}require_once __DIR__ . '/../../../modelo/conexion.php';
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

// ---------------------------------------------------------
// 1. EXTRAER Y PREPARAR VARIABLES GLOBALES
// ---------------------------------------------------------
$anioTabla = $data['anioTabla'] ?? date('Y');
$graficoAnios = $data['graficoAnios'] ?? [];
$resumen = $data['resumen'] ?? [];
$metas = $data['metas'] ?? [];
$historial = $data['historial'] ?? [];
$panelData = $data['panelData'] ?? null;

// Rutas de logos
$pathLogoGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobiernoBase64 = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoIncesBase64 = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

// ---------------------------------------------------------
// 2. CÁLCULOS INSTITUCIONALES Y SEMÁFORO
// ---------------------------------------------------------

$totObj = floatval($resumen['totObj'] ?? 0);
$totForm = floatval($resumen['totForm'] ?? 0);
$cumplimientoGlobal = $totObj > 0 ? ($totForm / $totObj) * 100 : 0;
$brechaGlobal = $totObj - $totForm;

$alertas = [
    'critica' => [],   // < 50%
    'atencion' => [],  // 50% - 79%
    'aceptable' => [], // 80% - 99%
    'cumplida' => []   // >= 100%
];

// Agrupar metas por ámbito para Ranking y Distribución
$ambitosDict = [];

foreach ($metas as &$m) {
    $pct = isset($m['pct']) ? floatval($m['pct']) : 0;
    
    // Clasificación semáforo
    if ($pct >= 100) {
        $m['clasificacion'] = 'cumplida';
        $alertas['cumplida'][] = $m;
    } elseif ($pct >= 80) {
        $m['clasificacion'] = 'aceptable';
        $alertas['aceptable'][] = $m;
    } elseif ($pct >= 50) {
        $m['clasificacion'] = 'atencion';
        $alertas['atencion'][] = $m;
    } else {
        $m['clasificacion'] = 'critica';
        $alertas['critica'][] = $m;
    }

    // Agrupación por ámbito
    $amb = $m['nombre_ambito'];
    if (!isset($ambitosDict[$amb])) {
        $ambitosDict[$amb] = [
            'nombre' => $amb,
            'objetivo' => 0,
            'alcanzado' => 0,
            'metas_count' => 0
        ];
    }
    $ambitosDict[$amb]['objetivo'] += $m['meta_objetivo'];
    $ambitosDict[$amb]['alcanzado'] += $m['estudiantes_formados'];
    $ambitosDict[$amb]['metas_count']++;
}
unset($m);

// Ordenar alertas de menor a mayor cumplimiento para el reporte
usort($alertas['critica'], fn($a, $b) => $a['pct'] <=> $b['pct']);
usort($alertas['atencion'], fn($a, $b) => $a['pct'] <=> $b['pct']);
usort($alertas['aceptable'], fn($a, $b) => $a['pct'] <=> $b['pct']);
usort($alertas['cumplida'], fn($a, $b) => $b['pct'] <=> $a['pct']); // Las cumplidas de mayor a menor

// ---------------------------------------------------------
// 3. RANKING Y BRECHAS POR ÁMBITO
// ---------------------------------------------------------
$rankingAmbitos = [];
foreach ($ambitosDict as $amb) {
    $amb['pct'] = $amb['objetivo'] > 0 ? ($amb['alcanzado'] / $amb['objetivo']) * 100 : 0;
    $amb['brecha'] = $amb['objetivo'] - $amb['alcanzado'];
    $rankingAmbitos[] = $amb;
}

// Ordenar para mejor ámbito (mayor cumplimiento, desempate por alcanzado)
usort($rankingAmbitos, function($a, $b) {
    if (abs($b['pct'] - $a['pct']) > 0.01) {
        return $b['pct'] <=> $a['pct'];
    }
    return $b['alcanzado'] <=> $a['alcanzado'];
});
$top10Ambitos = array_slice($rankingAmbitos, 0, 10);

$mejorAmbito = count($rankingAmbitos) > 0 ? $rankingAmbitos[0] : null;

// Ordenar para ámbito con mayor brecha
$ambitosBrecha = $rankingAmbitos;
usort($ambitosBrecha, fn($a, $b) => $b['brecha'] <=> $a['brecha']);
$peorAmbitoBrecha = (count($ambitosBrecha) > 0 && $ambitosBrecha[0]['brecha'] > 0) ? $ambitosBrecha[0] : null;

// ---------------------------------------------------------
// 4. TENDENCIA HISTÓRICA
// ---------------------------------------------------------
$historialOrdenado = [];
if (count($historial) > 1) {
    // Ordenar cronológicamente
    usort($historial, fn($a, $b) => $a['anio'] <=> $b['anio']);
    $historialOrdenado = $historial;
    
    // Calcular variaciones
    for ($i = 0; $i < count($historialOrdenado); $i++) {
        if ($i === 0) {
            $historialOrdenado[$i]['variacion'] = null;
        } else {
            $prev = $historialOrdenado[$i - 1]['cumplimientoGlobal'];
            $curr = $historialOrdenado[$i]['cumplimientoGlobal'];
            if ($prev > 0) {
                $historialOrdenado[$i]['variacion'] = (($curr - $prev) / $prev) * 100;
            } else {
                $historialOrdenado[$i]['variacion'] = 100;
            }
        }
    }
}

// ---------------------------------------------------------
// 5. OBSERVACIONES AUTOMÁTICAS
// ---------------------------------------------------------
$observaciones = [];

// Observación Brecha
if ($brechaGlobal > 0) {
    $observaciones[] = "El sistema presenta una brecha institucional global de " . number_format($brechaGlobal, 0, ',', '.') . " participantes respecto al objetivo planificado.";
} else {
    $observaciones[] = "Se ha superado el objetivo institucional global por " . number_format(abs($brechaGlobal), 0, ',', '.') . " participantes.";
}

// Observación Alertas
$totalMetas = count($metas);
$criticas = count($alertas['critica']);
if ($criticas > 0 && $totalMetas > 0) {
    $pctCrit = ($criticas / $totalMetas) * 100;
    $observaciones[] = "El " . number_format($pctCrit, 1) . "% de las metas (" . $criticas . ") se encuentran en estado CRÍTICO (cumplimiento menor al 50%).";
}

// Observación Ámbitos Extremos
if ($mejorAmbito) {
    $observaciones[] = "El ámbito '" . $mejorAmbito['nombre'] . "' lidera la ejecución con un cumplimiento de " . number_format($mejorAmbito['pct'], 1) . "% y " . number_format($mejorAmbito['alcanzado'], 0, ',', '.') . " formados.";
}
if ($peorAmbitoBrecha && $peorAmbitoBrecha['brecha'] > 0) {
    $observaciones[] = "El ámbito '" . $peorAmbitoBrecha['nombre'] . "' acumula la mayor brecha pendiente (" . number_format($peorAmbitoBrecha['brecha'], 0, ',', '.') . " cupos faltantes), representando un riesgo para el alcance general.";
}

// Tendencia
if (count($historialOrdenado) > 1) {
    $ultimo = end($historialOrdenado);
    if ($ultimo['variacion'] !== null) {
        if ($ultimo['variacion'] > 0) {
            $observaciones[] = "Se observa una tendencia creciente respecto al año anterior, con una mejora del " . number_format($ultimo['variacion'], 1) . "% en la tasa de cumplimiento global.";
        } elseif ($ultimo['variacion'] < 0) {
            $observaciones[] = "Se registra un decrecimiento del " . number_format(abs($ultimo['variacion']), 1) . "% en el cumplimiento global en comparación con el período anterior.";
        }
    }
}

// ---------------------------------------------------------
// 6. RENDERIZADO DOMPDF
// ---------------------------------------------------------

// Variables pasadas al template
$pdfData = [
    'usuarioFull' => $usuarioFull,
    'anioTabla' => $anioTabla,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobiernoBase64,
    'logoInces' => $logoIncesBase64,
    'totObj' => $totObj,
    'totForm' => $totForm,
    'cumplimientoGlobal' => $cumplimientoGlobal,
    'brechaGlobal' => $brechaGlobal,
    'alertas' => $alertas,
    'chartBase64' => $chartBase64,
    'top10Ambitos' => $top10Ambitos,
    'ambitosBrecha' => $ambitosBrecha,
    'historialOrdenado' => $historialOrdenado,
    'observaciones' => $observaciones,
    'metas' => $metas,
    'mejorAmbito' => $mejorAmbito,
    'peorAmbitoBrecha' => $peorAmbitoBrecha
];

// Capturar el HTML desde la vista
ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/meta_por_ambito.php';
$html = ob_get_clean();

$nombreArchivo = 'Informe_Gerencial_Metas_' . $anioTabla . '.pdf';
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . htmlspecialchars(str_replace('.pdf', '', $nombreArchivo)) . '</title>', $html);

// Inicializar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

// Renderizar
$dompdf->render();

// Enviar
$dompdf->stream($nombreArchivo, ["Attachment" => false]);

<?php
// ============================================================
// pdf_historial_docente.php
// Controlador DOMPDF - Reporte "Historial de Docente"
// ============================================================

require_once __DIR__ . '/../dompdf/autoload.inc.php';
require_once __DIR__ . '/../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Leer los datos enviados por JSON fetch
$jsonInput = file_get_contents('php://input');
if (!$jsonInput) {
    die("No se recibieron datos para generar el PDF.");
}

$payload = json_decode($jsonInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON: " . json_last_error_msg());
}

// 2. Extraer variables del payload
$perfil = $payload['perfil'] ?? [];
$cursos = $payload['cursos'] ?? [];
$resumen = $payload['resumen'] ?? [];
$desempeno = $payload['desempeno'] ?? [];
$resumenEjecutivo = $payload['resumenEjecutivo'] ?? '';
$evolucion = $payload['evolucion'] ?? [];
$especializacion = $payload['especializacion'] ?? [];
$filtros = $payload['filtros'] ?? [];

$graficoDona = $payload['graficoDona'] ?? '';
$graficoBar = $payload['graficoBar'] ?? '';
$graficoLinea = $payload['graficoLinea'] ?? '';
$graficoEsp = $payload['graficoEsp'] ?? '';

// 3. Preparar variables para análisis estadístico (Sección 7)
$stats = [
    'totalCursos' => count($cursos),
    'culminados' => 0,
    'enProceso' => 0,
    'cancelados' => 0,
    'horasAsignadas' => 0,
    'horasCompletadas' => 0,
    'totalParticipantes' => 0,
    'totalAprobados' => 0,
    'ambitos' => [],
    'promedioParticipantes' => 0,
    'tasaAprobacion' => 0,
    'ambitoMayor' => '—',
    'anioMayor' => '—'
];

foreach ($cursos as $c) {
    $estatus = $c['estatus_curso'] ?? '';
    if ($estatus === 'Culminado') $stats['culminados']++;
    else if ($estatus === 'En proceso') $stats['enProceso']++;
    else if ($estatus === 'Cancelado') $stats['cancelados']++;

    $stats['horasAsignadas'] += floatval($c['horas_totales'] ?? 0);
    $stats['horasCompletadas'] += floatval($c['horas_completadas'] ?? 0);
    
    $stats['totalParticipantes'] += intval($c['total_inscritos'] ?? 0);
    $stats['totalAprobados'] += intval($c['aprobados'] ?? 0);

    $ambito = $c['nombre_ambito'] ?? 'Desconocido';
    if (!isset($stats['ambitos'][$ambito])) $stats['ambitos'][$ambito] = 0;
    $stats['ambitos'][$ambito]++;
}

if ($stats['totalCursos'] > 0) {
    $stats['promedioParticipantes'] = round($stats['totalParticipantes'] / $stats['totalCursos'], 1);
}
if ($stats['totalParticipantes'] > 0) {
    $stats['tasaAprobacion'] = round(($stats['totalAprobados'] / $stats['totalParticipantes']) * 100, 1);
}

// Determinar ámbito mayor
$maxAmbitoCount = 0;
foreach ($stats['ambitos'] as $amb => $count) {
    if ($count > $maxAmbitoCount) {
        $maxAmbitoCount = $count;
        $stats['ambitoMayor'] = $amb;
    }
}

// Determinar año con mayor actividad (desde evolucion)
$maxAnioCursos = 0;
if (!empty($evolucion['labels'])) {
    foreach ($evolucion['labels'] as $idx => $anioLabel) {
        $cantidad = $evolucion['data'][$idx] ?? 0;
        if ($cantidad > $maxAnioCursos) {
            $maxAnioCursos = $cantidad;
            $stats['anioMayor'] = $anioLabel;
        }
    }
}

// 4. Usuario generador
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
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

$pathLogoGobierno = __DIR__ . '/../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    
    // Variables del reporte
    'perfil' => $perfil,
    'cursos' => $cursos,
    'resumen' => $resumen,
    'desempeno' => $desempeno,
    'resumenEjecutivo' => $resumenEjecutivo,
    'filtros' => $filtros,
    'stats' => $stats,
    
    // Gráficos
    'graficoDona' => $graficoDona,
    'graficoBar' => $graficoBar,
    'graficoLinea' => $graficoLinea,
    'graficoEsp' => $graficoEsp
];

// 5. Generar HTML y Renderizar con DOMPDF
try {
    ob_start();
    extract($pdfData);
    include __DIR__ . '/../../vista/reportes/pdf_templates/historial_docente.php';
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream('Reporte_Historial_Docente.pdf', ['Attachment' => false]);
} catch (Exception $e) {
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error de DOMPDF', 'detalle' => $e->getMessage()]);
} catch (Throwable $e) {
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error Crítico de PHP en DOMPDF', 'detalle' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
?>

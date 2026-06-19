<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// pdf_detalle_participantes_meta.php
// Controlador DOMPDF - Reporte "Detalle de Participantes por Meta"
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
$anio            = $payload['anio'] ?? null;
$meta            = $payload['meta'] ?? null;
$participantes   = $payload['participantes'] ?? [];
$kpis            = $payload['kpis'] ?? [];
$panelActivo     = $payload['panelActivo'] ?? null;
$tipoGrafico     = $payload['tipoGrafico'] ?? 'bar';
$graficoBase64   = $payload['graficoBase64'] ?? '';

if (!$meta) {
    die("No se seleccionó una meta válida.");
}

// 3. Obtener Usuario generador
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
    'usuarioFull'     => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno'    => $logoGobierno,
    'logoInces'       => $logoInces,
    
    // Variables del reporte
    'anio'            => $anio,
    'meta'            => $meta,
    'participantes'   => $participantes,
    'kpis'            => $kpis,
    'panelActivo'     => $panelActivo,
    'tipoGrafico'     => $tipoGrafico,
    'graficoBase64'   => $graficoBase64
];

// 4. Generar HTML y Renderizar con DOMPDF
try {
    ob_start();
    extract($pdfData);
    include __DIR__ . '/../../vista/reportes/pdf_templates/detalle_participantes_meta.php';
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait'); // Orientación RETRATO (Portrait)
    $dompdf->render();

    $dompdf->stream('Reporte_Detalle_Meta_Participantes.pdf', ['Attachment' => false]);
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

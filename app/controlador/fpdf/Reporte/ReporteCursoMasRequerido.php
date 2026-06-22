<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteCursoMasRequerido.php
// Controlador DOMPDF (Migrado) - Reporte "Curso más Requerido"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

// ─── Parámetros POST ───────────────────────────────────────────────────────
$anio             = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = $_POST['chart_image'] ?? '';
$datosJson        = $_POST['datos_json']  ?? '[]';

$cursos = json_decode($datosJson, true);
if (!is_array($cursos) || empty($cursos)) {
    die("No se recibieron datos de cursos.");
}

// ─── Calcular umbrales de prioridad (P75 / P40) ───────────────────────────
$scores = array_column($cursos, 'score');
sort($scores);
$n   = count($scores);
$p75 = $scores[(int)floor($n * 0.75)] ?? $scores[$n - 1];
$p40 = $scores[(int)floor($n * 0.40)] ?? $scores[0];

// ─── Estadísticas ─────────────────────────────────────────────────────────
$totInscr  = array_sum(array_column($cursos, 'inscripciones'));
$avgScore  = $n > 0 ? round(array_sum($scores) / $n) : 0;
$avgPartic = $n > 0 ? round(array_sum(array_column($cursos, 'participantes')) / $n) : 0;

// Usuario
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

$pathLogoGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'anio' => $anio,
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'cursos' => $cursos,
    'p75' => $p75,
    'p40' => $p40,
    'totInscr' => $totInscr,
    'avgScore' => $avgScore,
    'avgPartic' => $avgPartic,
    'n' => $n,
    'chartImageBase64' => $chartImageBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/curso_mas_requerido.php';
$html = ob_get_clean();

$nombreArchivo = 'Reporte_Cursos_Mas_Requeridos_' . $anio . '.pdf';
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . htmlspecialchars(str_replace('.pdf', '', $nombreArchivo)) . '</title>', $html);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

if (ob_get_length()) ob_end_clean();
$dompdf->stream($nombreArchivo, ['Attachment' => false]);

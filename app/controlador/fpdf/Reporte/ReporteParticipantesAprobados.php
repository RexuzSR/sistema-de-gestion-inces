<?php
// ============================================================
// ReporteParticipantesAprobados.php
// Controlador DOMPDF (Migrado) - Reporte "Participantes Aprobados"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
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

// Conexión y Usuario
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

$pathLogoGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

// PDF Data Array
$pdfData = [
    'anioTabla' => $anioTabla,
    'periodoTxt' => $periodoTxt,
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'resumen' => $resumen,
    'panelData' => $panelData,
    'tablaMensual' => $tablaMensual,
    'cursosSel' => $cursosSel,
    'chartBase64' => $chartImageBase64,
    'chartCursosBase64' => $chartCursosBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/participantes_aprobados.php';
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
$dompdf->stream('Reporte_Participantes_Aprobados_' . $anioTabla . '.pdf', ['Attachment' => false]);
?>

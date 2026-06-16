<?php
// ============================================================
// ReporteOcupacionCurso.php
// Controlador DOMPDF (Migrado) - Reporte "Ocupación de Cursos"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Acceso denegado.");
}

// CONEXIÓN Y CONSULTA COMPLETA
$conexion = new Conexion();
$conn = $conexion->getConexion();

$cursosSeleccionados = isset($_POST['cursosSeleccionados']) ? json_decode($_POST['cursosSeleccionados'], true) : [];
$chartImageBase64 = $_POST['chart_image'] ?? '';

$sql = "SELECT 
            nombre_curso,
            num_inscritos,
            (max_participantes - num_inscritos) AS cupos_disponibles,
            ROUND((num_inscritos / max_participantes) * 100, 2) AS ocupacion,
            max_participantes
        FROM tb_curso
        WHERE max_participantes > 0
        AND id_curso != 1";

if (!empty($cursosSeleccionados)) {
    $placeholders = rtrim(str_repeat('?,', count($cursosSeleccionados)), ',');
    $sql .= " AND nombre_curso IN ($placeholders)";
}

$stmt = $conn->prepare($sql);

if (!empty($cursosSeleccionados)) {
    foreach ($cursosSeleccionados as $k => $curso) {
        $stmt->bindValue($k + 1, $curso);
    }
}

$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totInscritos = array_sum(array_column($cursos, 'num_inscritos'));
$totCupos = array_sum(array_column($cursos, 'cupos_disponibles'));
$totMax = array_sum(array_column($cursos, 'max_participantes'));
$ocupacionGlobal = $totMax > 0 ? round(($totInscritos / $totMax) * 100, 2) : 0;

// Incluir usuario desde base de datos
date_default_timezone_set('America/Caracas');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) session_start();
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
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'cursos' => $cursos,
    'totInscritos' => $totInscritos,
    'totCupos' => $totCupos,
    'ocupacionGlobal' => $ocupacionGlobal,
    'chartImageBase64' => $chartImageBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/ocupacion_curso.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

if (ob_get_length()) ob_end_clean();
$dompdf->stream('Reporte_Ocupacion_Cursos.pdf', ['Attachment' => false]);
?>
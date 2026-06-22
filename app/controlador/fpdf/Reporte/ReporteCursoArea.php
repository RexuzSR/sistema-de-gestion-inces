<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteCursoArea.php
// Controlador DOMPDF (Migrado) - Reporte "Cursos por Área"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Acceso denegado.");
}

// Procesar parámetros GET
$areasFiltro = [];
if (isset($_GET['areas']) && !empty($_GET['areas'])) {
    $areasFiltro = array_map('urldecode', explode(',', $_GET['areas']));
    $areasFiltro = array_filter(array_map('trim', $areasFiltro));
}

// Conexión a la base de datos
$conexion = new Conexion();
$pdo = $conexion->getConexion();

// Consulta principal
$sql = "SELECT 
            ca.nombre_areaFormativa, 
            COUNT(c.id_curso) as cantidad, 
            SUM(c.num_inscritos) as num_inscritos 
        FROM tb_curso c
        INNER JOIN tb_areaformativa ca ON c.id_areaformativa = ca.id_areaformativa";

$params = [];
$placeholders = '';
if (!empty($areasFiltro)) {
    $placeholders = implode(',', array_fill(0, count($areasFiltro), '?'));
    $sql .= " WHERE ca.nombre_areaFormativa IN ($placeholders)";
    $params = array_values($areasFiltro);
}
$sql .= " GROUP BY ca.nombre_areaFormativa ORDER BY cantidad DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total de cursos
$sqlTotal = "SELECT COUNT(id_curso) as total FROM tb_curso";
if (!empty($areasFiltro)) {
    $sqlTotal = "SELECT COUNT(c.id_curso) as total 
                 FROM tb_curso c
                 INNER JOIN tb_areaformativa ca ON c.id_areaformativa = ca.id_areaformativa
                 WHERE ca.nombre_areaFormativa IN ($placeholders)";
}

$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($params);
$totalCursos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$filtrosText = '';
if (!empty($areasFiltro)) {
    $filtrosText = 'Filtro aplicado: ' . implode(', ', $areasFiltro);
}

// Incluir usuario desde base de datos
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}$usuarioFull = 'Usuario no identificado';
if (isset($_SESSION['id_usuario'])) {
    $sqlUsuario = "SELECT TRIM(CONCAT(
                        COALESCE(primer_nombre, ''), ' ',
                        COALESCE(segundo_nombre, ''), ' ',
                        COALESCE(primer_apellido, ''), ' ',
                        COALESCE(segundo_apellido, '')
                    )) AS nombre_completo
                   FROM tb_usuario WHERE id_usuario = :id_usuario";
    try {
        $stmtUsuario = $pdo->prepare($sqlUsuario);
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

function isGdAvailable()
{
    return extension_loaded('gd');
}

$logoGobierno = '';
$logoInces = '';
if (isGdAvailable()) {
    $logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
    $logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';
}

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'datos' => $datos,
    'totalCursos' => $totalCursos,
    'filtrosText' => $filtrosText
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/curso_area.php';
$html = ob_get_clean();

// Nombre del archivo
$nombreArchivo = 'Reporte_Cursos_';
if (!empty($areasFiltro)) {
    $nombreArchivo .= 'Filtrado_' . substr(implode('_', $areasFiltro), 0, 30);
} else {
    $nombreArchivo .= 'Completo';
}
$nombreArchivo .= '.pdf';

// Reemplazar el título de la página con el nombre de archivo descriptivo para metadatos
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . htmlspecialchars(str_replace('.pdf', '', $nombreArchivo)) . '</title>', $html);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');

try {
    $dompdf->render();
} catch (Exception $e) {
    if (stripos($e->getMessage(), 'PHP GD extension is required') !== false) {
        echo '<h2>Error interno de generación de PDF</h2>';
        echo '<p>La extensión GD de PHP no está habilitada. Habilite <code>extension=gd</code> en <code>php.ini</code> y reinicie Apache.</p>';
        exit;
    }
    throw $e;
}

if (ob_get_length()) ob_end_clean();

$dompdf->stream($nombreArchivo, ['Attachment' => false]);


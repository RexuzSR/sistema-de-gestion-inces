<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteTotalDocentes.php
// Controlador DOMPDF (Migrado) - Reporte "Total de Docentes"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

$chartBase64 = $_POST['chart_image'] ?? '';
$anioBase = isset($_POST['anio_base']) ? intval($_POST['anio_base']) : date('Y');
$anioTabla = isset($_POST['anio_tabla']) ? intval($_POST['anio_tabla']) : $anioBase;
$aniosGraficoRaw = $_POST['anios_grafico'] ?? '';

$aniosGrafico = [$anioBase];
if (!empty($aniosGraficoRaw)) {
    $decoded = json_decode($aniosGraficoRaw, true);
    if (is_array($decoded)) {
        $aniosGrafico = array_map('intval', $decoded);
    }
}

// Conectar a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Cargar sesión y obtener nombre del usuario generador
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
        $stmtUsuario = $conn->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
        $stmtUsuario->execute();
        $resultUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if ($resultUsuario && !empty(trim($resultUsuario['nombre_completo']))) {
            $usuarioFull = trim($resultUsuario['nombre_completo']);
        }
    } catch (Exception $e) { }
}

// Rutas de logos
$pathLogoGobierno = __DIR__ . '/../../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../../assets/img/inces-logo.png';

$logoGobiernoBase64 = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoIncesBase64 = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

// Consultar totales globales
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total,
           SUM(CASE WHEN estado_docente = 'Disponible'   THEN 1 ELSE 0 END) AS disponible,
           SUM(CASE WHEN estado_docente = 'En formación' THEN 1 ELSE 0 END) AS en_formacion,
           SUM(CASE WHEN estado_docente = 'Asignado'     THEN 1 ELSE 0 END) AS asignado,
           SUM(CASE WHEN estado_docente = 'Inactivo'     THEN 1 ELSE 0 END) AS inactivo,
           SUM(CASE WHEN tipo_docente   = 'Fijo'         THEN 1 ELSE 0 END) AS fijo,
           SUM(CASE WHEN tipo_docente   = 'Contratado'   THEN 1 ELSE 0 END) AS contratado,
           SUM(CASE WHEN tipo_docente   = 'PDL'          THEN 1 ELSE 0 END) AS pdl,
           SUM(CASE WHEN tipo_docente   = 'Voluntario'   THEN 1 ELSE 0 END) AS voluntario,
           SUM(CASE WHEN genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
           SUM(CASE WHEN genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino
    FROM tb_docente
");
$stmt->execute();
$totales = $stmt->fetch(PDO::FETCH_ASSOC);

$totalActivos = (intval($totales['disponible']) + intval($totales['en_formacion']) + intval($totales['asignado']));
$totalInactivos = intval($totales['inactivo']);

// Helper para traer asignaciones de un año
function getAsignacionesPorAnio($conn, $anio) {
    $meses = array_fill(1, 12, 0);
    $stmt = $conn->prepare("
        SELECT MONTH(dc.fecha_asignacion) AS mes, COUNT(*) AS total
        FROM tb_docente_curso dc
        WHERE YEAR(dc.fecha_asignacion) = :anio AND dc.id_docente IS NOT NULL
        GROUP BY MONTH(dc.fecha_asignacion)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $meses[intval($row['mes'])] = intval($row['total']);
    }
    return $meses;
}

$dataAnios = [];
foreach ($aniosGrafico as $a) {
    $dataAnios[$a] = getAsignacionesPorAnio($conn, $a);
}

// Detalle de Docentes Asignados por Mes
$stmtDetalle = $conn->prepare("
    SELECT dc.id_docente_curso,
           d.id_docente,
           CONCAT(d.primer_nombre, ' ', COALESCE(d.segundo_nombre, ''), ' ', d.primer_apellido, ' ', COALESCE(d.segundo_apellido, '')) AS nombre_completo,
           d.cedula,
           d.tipo_docente,
           d.estado_docente,
           c.id_curso,
           c.nombre_curso,
           dc.fecha_asignacion,
           MONTH(dc.fecha_asignacion) AS mes
    FROM tb_docente_curso dc
    JOIN tb_docente d ON d.id_docente = dc.id_docente
    JOIN tb_curso c ON c.id_curso = dc.id_curso
    WHERE YEAR(dc.fecha_asignacion) = :anio
      AND dc.id_docente IS NOT NULL
    ORDER BY MONTH(dc.fecha_asignacion) ASC, d.primer_apellido ASC, d.primer_nombre ASC
");
$stmtDetalle->execute([':anio' => $anioTabla]);
$registros = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

$porMes = [];
foreach ($registros as $reg) {
    $mes = intval($reg['mes']);
    if (!isset($porMes[$mes])) $porMes[$mes] = [];
    $porMes[$mes][] = $reg;
}

// Renderizar Vista
$pdfData = [
    'usuarioFull' => $usuarioFull,
    'anioBase' => $anioBase,
    'anioTabla' => $anioTabla,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobiernoBase64,
    'logoInces' => $logoIncesBase64,
    'totales' => $totales,
    'totalActivos' => $totalActivos,
    'totalInactivos' => $totalInactivos,
    'chartBase64' => $chartBase64,
    'aniosGrafico' => $aniosGrafico,
    'dataAnios' => $dataAnios,
    'porMes' => $porMes
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/total_docentes.php';
$html = ob_get_clean();

$nombreArchivo = 'Informe_Total_Docentes_' . $anioBase . '.pdf';
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

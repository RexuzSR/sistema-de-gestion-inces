<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteTotalCursosMensual.php
// Controlador DOMPDF (Migrado) - Reporte "Total de Cursos"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

$anio = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Funciones Auxiliares
function inicializarMesesLocal($anio) {
    $nombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $hoyAnio = intval(date('Y'));
    $hoyMes  = intval(date('n'));
    $meses   = [];
    for ($i = 1; $i <= 12; $i++) {
        $esFuturo = ($anio > $hoyAnio) || ($anio === $hoyAnio && $i > $hoyMes);
        $meses[$i] = [
            'mes' => $nombres[$i - 1],
            'nuevos' => $esFuturo ? null : 0,
            'activos' => $esFuturo ? null : 0,
            'culminados' => $esFuturo ? null : 0,
            'enEspera' => $esFuturo ? null : 0,
            'totalAcumulado' => 0
        ];
    }
    return $meses;
}

$meses = inicializarMesesLocal($anio);

$sql = "SELECT MONTH(c.fecha_creacion) AS mes, COUNT(*) AS total,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En proceso' THEN 1 ELSE 0 END) AS en_proceso,
               SUM(CASE WHEN e.nombre_estatusCurso = 'Culminado'  THEN 1 ELSE 0 END) AS culminado,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En espera'  THEN 1 ELSE 0 END) AS en_espera
        FROM tb_curso c
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        WHERE YEAR(c.fecha_creacion) = :anio 
        GROUP BY MONTH(c.fecha_creacion)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmt->execute();

$totNuevos = 0;
$totActivos = 0;
$totCulm = 0;
$totEspera = 0;

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $m = intval($row['mes']);
    if (isset($meses[$m]) && $meses[$m]['nuevos'] !== null) {
        $meses[$m]['nuevos'] = intval($row['total']);
        $meses[$m]['activos'] = intval($row['en_proceso']);
        $meses[$m]['culminados'] = intval($row['culminado']);
        $meses[$m]['enEspera'] = intval($row['en_espera']);
    }
}

$acumulado = 0;
$datosMeses = [];
foreach ($meses as $m) {
    if ($m['nuevos'] !== null) {
        $acumulado += $m['nuevos'];
        $m['totalAcumulado'] = $acumulado;
        $totNuevos += $m['nuevos'];
        $totActivos += $m['activos'];
        $totCulm += $m['culminados'];
        $totEspera += $m['enEspera'];
    }
    $datosMeses[] = $m;
}

// Cursos Activos: contar directamente los que ACTUALMENTE tienen estatus "En proceso"
$sqlActivos = "SELECT COUNT(*) AS total
               FROM tb_curso c
               LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
               WHERE e.nombre_estatusCurso = 'En proceso'
                 AND YEAR(c.fecha_creacion) = :anio";
$stmtActivos = $conn->prepare($sqlActivos);
$stmtActivos->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmtActivos->execute();
$activosUltimoMes = intval($stmtActivos->fetchColumn());

$tasaCompletitud = $totNuevos > 0 ? number_format(($totCulm / $totNuevos) * 100, 1) . '%' : '-';

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
    'totNuevos' => $totNuevos,
    'activosUltimoMes' => $activosUltimoMes,
    'totCulm' => $totCulm,
    'tasaCompletitud' => $tasaCompletitud,
    'datosMeses' => $datosMeses,
    'totActivos' => $totActivos,
    'totEspera' => $totEspera,
    'acumulado' => $acumulado,
    'chartImageBase64' => $chartImageBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/total_cursos_mensual.php';
$html = ob_get_clean();

$nombreArchivo = 'Reporte_Total_Cursos_' . $anio . '.pdf';
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

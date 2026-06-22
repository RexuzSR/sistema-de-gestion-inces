<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteTotalCurso.php
// Controlador DOMPDF (Migrado) - Reporte "Comparativo Mensual de Cursos"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}// Conexión a la base de datos usando PDO
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Fechas de este mes
$inicioMes = date('Y-m-01');
$finMes = date('Y-m-t');

// Consulta para este mes usando PDO
$sqlActual = "SELECT COUNT(*) as total_actual FROM tb_curso WHERE fecha_inicio BETWEEN :inicioMes AND :finMes";
$stmtActual = $conn->prepare($sqlActual);
$stmtActual->bindParam(':inicioMes', $inicioMes);
$stmtActual->bindParam(':finMes', $finMes);
$stmtActual->execute();
$dataActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

// Opcional: comparación contra el mes pasado
$inicioMesPasado = date('Y-m-01', strtotime('-1 month'));
$finMesPasado = date('Y-m-t', strtotime('-1 month'));
$sqlAnterior = "SELECT COUNT(*) as total_anterior FROM tb_curso WHERE fecha_inicio BETWEEN :inicioMesPasado AND :finMesPasado";
$stmtAnterior = $conn->prepare($sqlAnterior);
$stmtAnterior->bindParam(':inicioMesPasado', $inicioMesPasado);
$stmtAnterior->bindParam(':finMesPasado', $finMesPasado);
$stmtAnterior->execute();
$dataAnterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

// Cálculo de variación
$variacion = 0;
if ($dataAnterior['total_anterior'] > 0) {
    $variacion = (($dataActual['total_actual'] - $dataAnterior['total_anterior']) / $dataAnterior['total_anterior']) * 100;
}

// Consulta para obtener el total de cursos por estatus usando PDO
$sqlEstatus = "
    SELECT ec.nombre_estatusCurso, COUNT(c.id_curso) as total,
    ROUND((COUNT(c.id_curso) / (SELECT COUNT(*) FROM tb_curso WHERE fecha_inicio BETWEEN :inicioMes2 AND :finMes2)) * 100, 2) as porcentaje
    FROM tb_curso c
    JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
    WHERE c.fecha_inicio BETWEEN :inicioMes AND :finMes
    GROUP BY ec.id_estatusCurso
";

$stmtEstatus = $conn->prepare($sqlEstatus);
$stmtEstatus->bindParam(':inicioMes', $inicioMes);
$stmtEstatus->bindParam(':finMes', $finMes);
$stmtEstatus->bindParam(':inicioMes2', $inicioMes);
$stmtEstatus->bindParam(':finMes2', $finMes);
$stmtEstatus->execute();
$estatusData = [];
while ($row = $stmtEstatus->fetch(PDO::FETCH_ASSOC)) {
    // Si el total de cursos en el mes es 0, evitamos valor null en porcentaje
    if ($row['porcentaje'] === null) {
        $row['porcentaje'] = 0;
    }
    $estatusData[] = $row;
}

$comparativaData = [
    'total_actual' => $dataActual['total_actual'],
    'total_anterior' => $dataAnterior['total_anterior'],
    'variacion' => $variacion,
    'estatus' => $estatusData
];

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
    'comparativaData' => $comparativaData
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/total_curso.php';
$html = ob_get_clean();

$nombreArchivo = 'Reporte_Cursos_Comparativo_' . date('Y') . '.pdf';
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

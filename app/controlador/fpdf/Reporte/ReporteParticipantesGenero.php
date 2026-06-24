<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteParticipantesGenero.php
// Controlador DOMPDF (Migrado) - Reporte "Participantes por Género"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a la base de datos
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}$conexion = new Conexion();
$conn = $conexion->getConexion();

$sql = "
    SELECT 
        c.nombre_curso,
        SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END) as masculino,
        SUM(CASE WHEN p.genero = 'Femenino' THEN 1 ELSE 0 END) as femenino
    FROM tb_participante_curso pc
    INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
    WHERE pc.estatus_participante = 'Pendiente'
    GROUP BY c.id_curso
    ORDER BY c.nombre_curso
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($datos)) {
    die("No hay cursos con participantes pendientes");
}

// Totalizar
$totalMasculino = 0;
$totalFemenino = 0;
foreach ($datos as $curso) {
    $totalMasculino += $curso['masculino'];
    $totalFemenino += $curso['femenino'];
}
$totalGeneral = $totalMasculino + $totalFemenino;

// Usuario generado
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
    'datos' => $datos,
    'totalMasculino' => $totalMasculino,
    'totalFemenino' => $totalFemenino,
    'totalGeneral' => $totalGeneral
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/participantes_genero.php';
$html = ob_get_clean();

$fechaNombreArchivo = date('Y-m-d');
$nombreArchivo = 'INCES_Participantes_por_Genero_' . date('Y') . '_' . $fechaNombreArchivo . '.pdf';
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

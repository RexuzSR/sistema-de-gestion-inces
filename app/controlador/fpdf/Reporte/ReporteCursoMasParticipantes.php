<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteCursoMasParticipantes.php
// Controlador DOMPDF (Migrado) - Reporte "Cursos con más Participantes"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

// 1. Obtener parámetros
$anio = isset($_POST['anio']) ? intval($_POST['anio']) : date('Y');
$chartImageBase64 = isset($_POST['chart_image']) ? $_POST['chart_image'] : '';

// 2. Obtener datos de la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

$limite = 50; // Para obtener todos los cursos relevantes (usualmente 19 en el ejemplo)

$sql = "SELECT
            c.id_curso,
            c.nombre_curso,
            COUNT(DISTINCT pc.id_participante) AS total_participantes,
            e.nombre_estatusCurso AS estatus,
            m.nombre_modalidad    AS modalidad,
            c.fecha_inicio,
            c.fecha_fin
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_modalidad    m ON m.id_modalidad    = c.id_modalidad
        WHERE YEAR(pc.fecha_inscripcion) = :anio
        GROUP BY c.id_curso, c.nombre_curso, e.nombre_estatusCurso, m.nombre_modalidad, c.fecha_inicio, c.fecha_fin
        ORDER BY total_participantes DESC
        LIMIT :limite";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($cursos) === 0) {
    die("No hay datos de cursos para el año seleccionado.");
}

// 3. Procesar datos (Cálculos de Promedio, Desviación, Clasificación)
$totalParticipantesGlobal = 0;
$participantesArray = [];
foreach ($cursos as $c) {
    $p = intval($c['total_participantes']);
    $totalParticipantesGlobal += $p;
    $participantesArray[] = $p;
}
$cantidadCursos = count($cursos);
$promedio = $cantidadCursos > 0 ? $totalParticipantesGlobal / $cantidadCursos : 0;

$sumaCuadrados = 0;
foreach ($participantesArray as $p) {
    $sumaCuadrados += pow($p - $promedio, 2);
}
$desviacion = $cantidadCursos > 0 ? sqrt($sumaCuadrados / $cantidadCursos) : 0;

$umbSup = $promedio + $desviacion;
$umbInf = max(0, $promedio - $desviacion);

// Función de duración
function getDuracionLegible($fi, $ff) {
    if (!$fi || !$ff) return 'Sin informacion';
    $ts1 = strtotime($fi);
    $ts2 = strtotime($ff);
    if (!$ts1 || !$ts2) return 'Sin informacion';
    $dias = round(($ts2 - $ts1) / 86400);
    if ($dias < 0) return 'Sin informacion';
    if ($dias == 0) return '1 dia';
    if ($dias < 7) return $dias . ' dia' . ($dias != 1 ? 's' : '');
    if ($dias < 30) {
        $sem = round($dias / 7);
        return $sem . ' sem (' . $dias . ' d)';
    }
    $m = round($dias / 30);
    return $m . ' mes(es) (' . $dias . ' d)';
}

foreach ($cursos as &$c) {
    $p = intval($c['total_participantes']);
    if ($p >= $umbSup) {
        $c['clasificacion'] = 'Muy Activo';
    } elseif ($p >= $umbInf) {
        $c['clasificacion'] = 'Activo';
    } else {
        $c['clasificacion'] = 'Normal';
    }
    $c['duracion_legible'] = getDuracionLegible($c['fecha_inicio'], $c['fecha_fin']);
}
unset($c);

$cursoMasActivo = $cursos[0]; // Ya viene ordenado DESC

// Usuario
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
$usuarioFull = 'Usuario no identificado';
if (isset($_SESSION['id_usuario'])) {
    require_once __DIR__ . '/../../../modelo/HistorialUtil.php';
    HistorialUtil::registrarReporte('Cursos con más Participantes', $_SESSION['id_usuario']);
}
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
    'totalParticipantesGlobal' => $totalParticipantesGlobal,
    'promedio' => $promedio,
    'cantidadCursos' => $cantidadCursos,
    'cursoMasActivo' => $cursoMasActivo,
    'chartImageBase64' => $chartImageBase64
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/curso_mas_participantes.php';
$html = ob_get_clean();

$fechaNombreArchivo = date('Y-m-d');
$nombreArchivo = 'INCES_Cursos_Mas_Participantes_' . $anio . '_' . $fechaNombreArchivo . '.pdf';
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

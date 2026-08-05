<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

require_once __DIR__ . '/../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validar ID o Cédula
$idParam = trim($_GET['id'] ?? '');
if (empty($idParam)) {
    die("ID o Cédula de participante inválido");
}

date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Obtener datos del Participante
$stmtParticipante = $conn->prepare("
    SELECT p.id_participante, p.cedula, p.primer_nombre, p.segundo_nombre,
           p.primer_apellido, p.segundo_apellido, p.genero, p.edad,
           p.fecha_nacimiento, p.correo, p.estado,
           p.grado_institucion, p.fecha_registro,
           d.nombre_discapacidad,
           (SELECT GROUP_CONCAT(t.numero_de_telefono SEPARATOR ', ') FROM tb_telefono t WHERE t.id_participante = p.id_participante) AS telefono
    FROM tb_participante p
    LEFT JOIN tb_discapacidad d ON d.id_discapacidad = p.id_discapacidad
    WHERE p.id_participante = :id OR p.cedula = :id
    LIMIT 1
");
$stmtParticipante->bindValue(':id', $idParam);
$stmtParticipante->execute();
$participante = $stmtParticipante->fetch(PDO::FETCH_ASSOC);

if (!$participante) {
    die("Participante no encontrado");
}

$id_participante = $participante['id_participante'];

// Registrar en bitácora de historial
if (isset($_SESSION['id_usuario'])) {
    require_once __DIR__ . '/../../modelo/HistorialUtil.php';
    HistorialUtil::registrar(
        'PDF',
        "Se generó el historial de participante para: " . $participante['primer_nombre'] . " " . $participante['primer_apellido'] . " (C.I. " . $participante['cedula'] . ")",
        'tb_participante',
        $id_participante,
        $_SESSION['id_usuario']
    );
}

// Obtener Usuario emisor del PDF
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
    } catch (Exception $e) {}
}

// Obtener Cursos del Participante
$stmtCursos = $conn->prepare("
    SELECT 
        c.id_curso,
        c.nombre_curso,
        c.preimpreso,
        c.horas_totales,
        m.nombre_modalidad AS modalidad,
        ec.nombre_estatusCurso AS estatus_curso,
        a.nombre_ambito,
        DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
        DATE_FORMAT(c.fecha_fin, '%d/%m/%Y') AS fecha_fin,
        pc.estatus_participante,
        pc.fecha_inscripcion
    FROM tb_participante_curso pc
    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
    LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
    LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
    WHERE pc.id_participante = :id
    ORDER BY c.fecha_inicio DESC, c.id_curso DESC
");
$stmtCursos->bindParam(':id', $id_participante, PDO::PARAM_INT);
$stmtCursos->execute();
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

// KPIs Estadísticos
$totalCursos = count($cursos);
$aprobados = 0;
$enCurso = 0;
$totalHoras = 0;

foreach ($cursos as $c) {
    $st = $c['estatus_participante'] ?? '';
    if ($st === 'Aprobado' || $st === 'Culminado') {
        $aprobados++;
        $totalHoras += floatval($c['horas_totales'] ?? 0);
    } elseif ($st === 'En formación' || $st === 'En proceso') {
        $enCurso++;
    }
}

$kpis = [
    'total_cursos' => $totalCursos,
    'aprobados'    => $aprobados,
    'en_curso'     => $enCurso,
    'total_horas'  => $totalHoras
];

// Logos en Base64
$pathLogoGobierno = __DIR__ . '/../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces    = file_exists($pathLogoInces)    ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces))    : '';

$fechaGeneracion = date('d/m/Y h:i A');

// Cargar plantilla HTML y extraer variables
$pdfData = [
    'participante'    => $participante,
    'cursos'          => $cursos,
    'kpis'            => $kpis,
    'usuarioFull'     => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno'    => $logoGobierno,
    'logoInces'       => $logoInces
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../vista/reportes/pdf_templates/historial_participante.php';
$html = ob_get_clean();

$nombreArchivo = 'INCES_Historial_Participante_' . ($participante['cedula'] ?? $id_participante) . '_' . date('Y-m-d') . '.pdf';
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

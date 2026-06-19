<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteCursoTrayectoria.php
// Controlador DOMPDF (Migrado) - Reporte "Trayectoria de Cursos"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

// 1. Cargar filtros
$anio = isset($_POST['anio']) ? $_POST['anio'] : '';
$modalidad = isset($_POST['modalidad']) ? $_POST['modalidad'] : '';
$estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$tipoPeriodo = isset($_POST['tipoPeriodo']) ? $_POST['tipoPeriodo'] : '';
$subPeriodo = isset($_POST['subPeriodo']) ? $_POST['subPeriodo'] : '';

// 2. Conectar a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

// 3. Obtener resumen de KPIs globales
$stmtKpi = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN id_estatusCurso = 1 THEN 1 ELSE 0 END) as en_espera,
        SUM(CASE WHEN id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
        SUM(CASE WHEN id_estatusCurso = 5 THEN 1 ELSE 0 END) as culminados
    FROM tb_curso
");
$kpis = $stmtKpi->fetch(PDO::FETCH_ASSOC);

// 4. Armar consulta de cursos aplicando filtros
$sql = "SELECT 
            c.id_curso,
            c.nombre_curso,
            m.nombre_modalidad,
            c.turno,
            c.fecha_inicio,
            c.fecha_fin,
            c.num_inscritos,
            c.max_participantes,
            e.nombre_estatusCurso as estatus,
            c.id_estatusCurso,
            a.nombre_ambito
        FROM tb_curso c
        LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        WHERE 1=1";

$params = [];
if (!empty($anio)) {
    $sql .= " AND YEAR(c.fecha_inicio) = :anio";
    $params[':anio'] = intval($anio);
}
if (!empty($modalidad)) {
    $sql .= " AND m.nombre_modalidad = :modalidad";
    $params[':modalidad'] = $modalidad;
}
if (!empty($estatus)) {
    $sql .= " AND e.nombre_estatusCurso = :estatus";
    $params[':estatus'] = $estatus;
}
if (!empty($busqueda)) {
    $sql .= " AND (c.id_curso LIKE :busqueda OR c.nombre_curso LIKE :busqueda)";
    $params[':busqueda'] = '%' . $busqueda . '%';
}
if (!empty($tipoPeriodo) && !empty($subPeriodo)) {
    $sub = intval($subPeriodo);
    if ($tipoPeriodo === 'mensual') {
        $sql .= " AND MONTH(c.fecha_inicio) = :mes";
        $params[':mes'] = $sub;
    } else if ($tipoPeriodo === 'trimestral') {
        if ($sub === 1) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 1 AND 3";
        } else if ($sub === 2) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 4 AND 6";
        } else if ($sub === 3) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 7 AND 9";
        } else if ($sub === 4) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 10 AND 12";
        }
    } else if ($tipoPeriodo === 'semestral') {
        if ($sub === 1) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 1 AND 6";
        } else if ($sub === 2) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 7 AND 12";
        }
    }
}

$sql .= " ORDER BY c.fecha_inicio DESC";

$stmtCursos = $conn->prepare($sql);
$stmtCursos->execute($params);
$cursosList = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

// Construir texto de filtros
$filtrosTextParts = [];
if ($anio) $filtrosTextParts[] = "Año: $anio";
if ($modalidad) $filtrosTextParts[] = "Mod: $modalidad";
if ($estatus) $filtrosTextParts[] = "Est: $estatus";
if ($busqueda) $filtrosTextParts[] = "Busq: \"$busqueda\"";
if ($tipoPeriodo) {
    $periodLabel = "Periodo: $tipoPeriodo";
    if ($subPeriodo) {
        if ($tipoPeriodo === 'mensual') {
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $periodLabel .= " (" . $meses[intval($subPeriodo) - 1] . ")";
        } else if ($tipoPeriodo === 'trimestral') {
            $periodLabel .= " (T$subPeriodo)";
        } else if ($tipoPeriodo === 'semestral') {
            $periodLabel .= " (S$subPeriodo)";
        }
    }
    $filtrosTextParts[] = $periodLabel;
}
$filtrosText = implode(' | ', $filtrosTextParts);

// Helper
function buildCursoStats($fecha_inicio, $fecha_fin, $participantes, $max_participantes) {
    $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    
    $start = !empty($fecha_inicio) ? strtotime($fecha_inicio) : time();
    $end = !empty($fecha_fin) ? strtotime($fecha_fin) : $start;
    if ($end < $start) $end = $start;
    
    $duracionDias = max(0, round(($end - $start) / 86400));
    
    $counts = [];
    foreach ($participantes as $p) {
        if (!empty($p['fecha_inscripcion']) && $p['fecha_inscripcion'] !== '0000-00-00') {
            $key = date('Y-m', strtotime($p['fecha_inscripcion']));
            if (!isset($counts[$key])) $counts[$key] = 0;
            $counts[$key]++;
        }
    }
    
    $labels = [];
    $dataPoints = [];
    $monthKeys = [];
    
    $curr = $start;
    $guard = 0;
    
    while ($curr <= $end && $guard < 12) {
        $guard++;
        $key = date('Y-m', $curr);
        $m = intval(date('n', $curr)) - 1;
        $y = date('Y', $curr);
        
        $labels[] = $mesesNombres[$m] . ' ' . $y;
        $monthKeys[] = $key;
        $val = isset($counts[$key]) ? $counts[$key] : 0;
        $dataPoints[] = $val;
        
        $curr = strtotime("+1 month", $curr);
    }
    
    $peakMonth = '';
    $peakCount = 0;
    for ($i = 0; $i < count($dataPoints); $i++) {
        if ($dataPoints[$i] > $peakCount) {
            $peakCount = $dataPoints[$i];
            $peakMonth = $labels[$i] . " ($peakCount)";
        }
    }
    
    $inscritosTotales = count($participantes);
    $cupoPct = $max_participantes > 0 ? min(100, round(($inscritosTotales / $max_participantes) * 100)) : 0;
    
    return [
        'duracionDias' => $duracionDias,
        'inscritosTotales' => $inscritosTotales,
        'cupoPct' => $cupoPct,
        'peakMonth' => $peakMonth,
        'labels' => $labels,
        'dataPoints' => $dataPoints
    ];
}

$cursosDetalles = [];
foreach ($cursosList as $c) {
    $idCurso = intval($c['id_curso']);
    
    $stmtDocente = $conn->prepare("
        SELECT CONCAT(d.primer_nombre, ' ', d.primer_apellido) as docente
        FROM tb_docente_curso dc 
        JOIN tb_docente d ON d.id_docente = dc.id_docente 
        WHERE dc.id_curso = :id_curso LIMIT 1
    ");
    $stmtDocente->execute([':id_curso' => $idCurso]);
    $docData = $stmtDocente->fetch(PDO::FETCH_ASSOC);
    $docente = $docData ? $docData['docente'] : 'Sin docente';
    
    $stmtParts = $conn->prepare("
        SELECT 
            p.cedula,
            p.primer_nombre,
            p.primer_apellido,
            pc.fecha_inscripcion,
            pc.estatus_participante
        FROM tb_participante_curso pc
        JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE pc.id_curso = :id_curso
        ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    $stmtParts->execute([':id_curso' => $idCurso]);
    $participantes = $stmtParts->fetchAll(PDO::FETCH_ASSOC);
    
    $stats = buildCursoStats($c['fecha_inicio'], $c['fecha_fin'], $participantes, $c['max_participantes']);
    
    $aprobados = 0;
    $reprobados = 0;
    $enFormacion = 0;
    foreach ($participantes as $p) {
        $estPart = $p['estatus_participante'];
        if ($estPart === 'Aprobado') {
            $aprobados++;
        } elseif (in_array($estPart, ['Reprobado', 'Ausente'])) {
            $reprobados++;
        } else {
            $enFormacion++;
        }
    }
    
    $cursosDetalles[$idCurso] = [
        'docente' => $docente,
        'participantes' => $participantes,
        'stats' => $stats,
        'aprobados' => $aprobados,
        'reprobados' => $reprobados,
        'enFormacion' => $enFormacion
    ];
}

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
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'filtrosText' => $filtrosText,
    'kpis' => $kpis,
    'cursosList' => $cursosList,
    'cursosDetalles' => $cursosDetalles
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/curso_trayectoria.php';
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
$dompdf->stream('Reporte_Trayectoria_Cursos.pdf', ['Attachment' => false]);

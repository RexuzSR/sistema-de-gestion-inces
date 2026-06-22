<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// ReporteTrayectoriaParticipantes.php
// Controlador DOMPDF (Migrado) - Reporte "Trayectoria de Participantes"
// ============================================================

require_once __DIR__ . '/../../dompdf/vendor/autoload.php';
require_once __DIR__ . '/../../../modelo/conexion.php';
// Asumimos que las funciones getTrayectoriaResumen, getTrayectoriaParticipantes, etc. 
// están disponibles porque este archivo formaba parte del sistema antiguo o 
// incluimos api_reportes.php (se nota que las usa globalmente, revisaremos si necesita require adicional, 
// el original asume que están pero api_reportes.php es llamado por AJAX usualmente.
// En el original dice: "// Obtener la data (funciones globales de api_reportes.php)"
// Mejor incluimos api_reportes si es necesario, pero el original NO lo incluye explícitamente (asume que algo lo carga o está en la misma sesión/directorio. Ah, el original es llamado directamente. Mmm, vamos a incluir api_reportes.php si es que define esas funciones, o extraer la lógica. El original de la línea 60 dice: "$kpiData = getTrayectoriaResumen($conn);").
// Para no romperlo, usaré require_once de api_reportes.php.
require_once __DIR__ . '/../../Reporte/api_reportes.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso denegado.");
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Start session if not started
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}// Obtener datos del usuario
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

// Recibir POST
$conCurso = $_POST['conCurso'] ?? '';
$conAprobado = $_POST['conAprobado'] ?? '';
$anio = $_POST['anio'] ?? '';
$genero = $_POST['genero'] ?? '';
$estado = $_POST['estado'] ?? '';
$edadMin = $_POST['edadMin'] ?? '';
$edadMax = $_POST['edadMax'] ?? '';
$mesesJson = $_POST['meses'] ?? '[]';
$meses = json_decode($mesesJson, true);
if (!is_array($meses)) $meses = [];
$cedulaSelected = $_POST['cedula'] ?? '';
$ganttImage = $_POST['gantt_image'] ?? '';

// Filtros para mostrar
$txtConCurso = $conCurso === '' ? 'Todos' : ($conCurso == '1' ? 'Con registro de curso' : 'Sin registro de curso');
$txtConAprobado = $conAprobado === '' ? 'Todos' : ($conAprobado == '1' ? 'Con formaciones aprobadas' : 'Sin formaciones aprobadas');
$txtAnio = $anio === '' ? 'Todos' : $anio;
$txtGenero = $genero === '' ? 'Todos' : $genero;
$txtEstado = $estado === '' ? 'Todos' : $estado;
$txtEdad = 'Todas';
if ($edadMin !== '' && $edadMax !== '') $txtEdad = "{$edadMin} a {$edadMax} años";
elseif ($edadMin !== '') $txtEdad = ">= {$edadMin} años";
elseif ($edadMax !== '') $txtEdad = "<= {$edadMax} años";

$nombresMeses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
$txtMeses = empty($meses) ? 'Todos' : implode(', ', array_map(function($m) use ($nombresMeses) { return $nombresMeses[$m] ?? $m; }, $meses));

// Funciones locales de consulta (para reemplazar getTrayectoriaResumen y demás si fallan)
function local_getTrayectoriaResumen($conn) {
    $res = ['total'=>0, 'activos'=>0, 'sin_curso'=>0];
    try {
        $stmt = $conn->query("SELECT COUNT(id_participante) as tot FROM tb_participante");
        $res['total'] = $stmt->fetchColumn() ?: 0;
        
        $stmt = $conn->query("SELECT COUNT(DISTINCT id_participante) as act FROM tb_participante_curso");
        $res['activos'] = $stmt->fetchColumn() ?: 0;
        
        $res['sin_curso'] = $res['total'] - $res['activos'];
    } catch(Exception $e) {}
    return $res;
}

function local_getTrayectoriaParticipantes($conn) {
    $sql = "SELECT p.id_participante, p.cedula, p.primer_nombre, p.primer_apellido, 
            p.fecha_registro, p.genero, p.correo, p.telefono, 
            TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
            (SELECT COUNT(*) FROM tb_participante_curso pc WHERE pc.id_participante = p.id_participante) as has_curso,
            (SELECT COUNT(*) FROM tb_participante_curso pc WHERE pc.id_participante = p.id_participante AND pc.estatus_participante = 'Aprobado') as has_aprobado,
            IFNULL((
                SELECT GROUP_CONCAT(c.nombre_curso SEPARATOR ', ')
                FROM tb_participante_curso pc
                JOIN tb_curso c ON c.id_curso = pc.id_curso
                WHERE pc.id_participante = p.id_participante AND pc.estatus_participante = 'Aprobado'
            ), '') as cursos_aprobados,
            COALESCE(
                (SELECT pc.estatus_participante FROM tb_participante_curso pc JOIN tb_curso c ON c.id_curso = pc.id_curso WHERE pc.id_participante = p.id_participante ORDER BY c.fecha_inicio DESC LIMIT 1),
                'Disponible'
            ) as estado
            FROM tb_participante p
            ORDER BY p.fecha_registro DESC";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function local_getDetalleParticipante($conn, $cedula) {
    $sql = "SELECT * FROM tb_participante WHERE cedula = :cedula";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cedula' => $cedula]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) return ['error' => 'No encontrado'];
    
    // Calcular edad
    if (!empty($p['fecha_nacimiento']) && $p['fecha_nacimiento'] !== '0000-00-00') {
        $from = new DateTime($p['fecha_nacimiento']);
        $to   = new DateTime('today');
        $p['edad'] = $from->diff($to)->y;
    } else {
        $p['edad'] = '—';
    }
    
    // Teléfonos
    $sqlTelf = "SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = :id_participante";
    $stmtTelf = $conn->prepare($sqlTelf);
    $stmtTelf->execute([':id_participante' => $p['id_participante']]);
    $p['telefonos'] = $stmtTelf->fetchAll(PDO::FETCH_ASSOC);
    
    // Cursos
    $sqlCursos = "SELECT c.*, pc.fecha_inscripcion, pc.estatus_participante, m.nombre_modalidad
                  FROM tb_participante_curso pc
                  JOIN tb_curso c ON c.id_curso = pc.id_curso
                  LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
                  WHERE pc.id_participante = :id_participante
                  ORDER BY c.fecha_inicio DESC";
    $stmtCursos = $conn->prepare($sqlCursos);
    $stmtCursos->execute([':id_participante' => $p['id_participante']]);
    $p['cursos'] = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
    
    return $p;
}

$kpiData = function_exists('getTrayectoriaResumen') ? getTrayectoriaResumen($conn) : local_getTrayectoriaResumen($conn);
$todos = function_exists('getTrayectoriaParticipantes') ? getTrayectoriaParticipantes($conn) : local_getTrayectoriaParticipantes($conn);

$estadoEnFormacion = 0;
$estadoEnSistema = 0;
$estadoDisponible = 0;
foreach ($todos as $p) {
    $est = $p['estado'];
    if (strpos($est, 'formaci') !== false && (strpos($est, '?') !== false || strpos($est, 'n') !== false)) {
        $est = 'En formación';
    }
    if ($est === 'En formación') $estadoEnFormacion++;
    if ($est === 'En sistema') $estadoEnSistema++;
    if ($est === 'Disponible') $estadoDisponible++;
}

// Filtrar
$filtrados = [];
foreach ($todos as $p) {
    if ($conCurso !== '' && (int)$p['has_curso'] !== (int)$conCurso) continue;
    if ($conAprobado !== '' && (int)$p['has_aprobado'] !== (int)$conAprobado) continue;
    if ($anio !== '' && strpos($p['fecha_registro'], $anio) !== 0) continue;
    if (!empty($meses)) {
        if (empty($p['fecha_registro']) || $p['fecha_registro'] === '0000-00-00') continue;
        $mesReg = substr($p['fecha_registro'], 5, 2);
        if (!in_array($mesReg, $meses)) continue;
    }
    if ($genero !== '' && $p['genero'] !== $genero) continue;
    if ($estado !== '') {
        $estRaw = $p['estado'];
        if (strpos($estRaw, 'formaci') !== false && (strpos($estRaw, '?') !== false || strpos($estRaw, 'n') !== false)) {
            $estRaw = 'En formación';
        }
        if (trim($estRaw) !== $estado) continue;
    }
    if ($edadMin !== '' && (int)$p['edad'] < (int)$edadMin) continue;
    if ($edadMax !== '' && (int)$p['edad'] > (int)$edadMax) continue;
    $filtrados[] = $p;
}

$totalEncontrados = count($filtrados);

// Detalle del Participante
$detalleHtml = "";
if ($cedulaSelected) {
    $detalleData = function_exists('getDetalleParticipante') ? getDetalleParticipante($conn, $cedulaSelected) : local_getDetalleParticipante($conn, $cedulaSelected);
    
    if (!isset($detalleData['error'])) {
        $nombreComp = trim($detalleData['primer_nombre'].' '.($detalleData['segundo_nombre']??'').' '.$detalleData['primer_apellido'].' '.($detalleData['segundo_apellido']??''));
        $fechaN = (!empty($detalleData['fecha_nacimiento']) && $detalleData['fecha_nacimiento'] !== '0000-00-00') ? date('d/m/Y', strtotime($detalleData['fecha_nacimiento'])) : '—';
        
        $telefonos = array_column($detalleData['telefonos'] ?? [], 'numero_de_telefono');
        $telfStr = !empty($telefonos) ? implode(', ', $telefonos) : '—';
        
        $cursos = $detalleData['cursos'] ?? [];
        $totCursos = count($cursos);
        $aprobados = count(array_filter($cursos, fn($c) => $c['estatus_participante'] === 'Aprobado'));
        $horas = array_sum(array_column($cursos, 'horas_totales'));

        $filaCursos = "";
        foreach ($cursos as $c) {
            $fInsc = (!empty($c['fecha_inscripcion']) && $c['fecha_inscripcion'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_inscripcion'])) : '—';
            $fIni = (!empty($c['fecha_inicio']) && $c['fecha_inicio'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '—';
            $fFin = (!empty($c['fecha_fin']) && $c['fecha_fin'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_fin'])) : '—';
            
            $filaCursos .= "<tr>
                <td>{$c['nombre_curso']}</td>
                <td style='text-align:center'>{$fInsc}</td>
                <td style='text-align:center'>{$fIni}</td>
                <td style='text-align:center'>{$fFin}</td>
                <td style='text-align:center'>{$c['turno']}</td>
                <td style='text-align:center'>{$c['nombre_modalidad']}</td>
                <td style='text-align:center'>{$c['estatus_participante']}</td>
            </tr>";
        }
        if (empty($cursos)) {
            $filaCursos = "<tr><td colspan='7' style='text-align:center; color:#666;'>Sin cursos registrados</td></tr>";
        }

        $detalleHtml = "
        <div class='page-break'></div>
        <div class='section-title'>Detalle del Participante Seleccionado</div>
        <div class='detalle-box'>
            <table style='width:100%; border:none; margin:0;'>
                <tr>
                    <td style='border:none; width:50%; vertical-align:top; padding:0; background:transparent;'>
                        <h4>Datos Personales</h4>
                        <p><strong>Cédula:</strong> {$detalleData['cedula']}</p>
                        <p><strong>Nombre Completo:</strong> {$nombreComp}</p>
                        <p><strong>Fecha de Nacimiento:</strong> {$fechaN}</p>
                        <p><strong>Edad:</strong> {$detalleData['edad']}</p>
                        <p><strong>Género:</strong> {$detalleData['genero']}</p>
                    </td>
                    <td style='border:none; width:50%; vertical-align:top; padding:0; background:transparent;'>
                        <h4>Contacto y Académico</h4>
                        <p><strong>Teléfonos:</strong> {$telfStr}</p>
                        <p><strong>Correo:</strong> {$detalleData['correo']}</p>
                        <p><strong>Nivel de Instrucción:</strong> " . ($detalleData['grado_institucion'] ?? '—') . "</p>
                        <p><strong>Total de Cursos:</strong> {$totCursos} (Aprobados: {$aprobados})</p>
                        <p><strong>Horas Acumuladas:</strong> {$horas}</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class='section-title'>Historial de Cursos</div>
        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width:30%; text-align:left;'>Curso</th>
                    <th style='width:12%;'>Inscripción</th>
                    <th style='width:12%;'>Inicio</th>
                    <th style='width:12%;'>Fin</th>
                    <th style='width:10%;'>Turno</th>
                    <th style='width:12%;'>Modalidad</th>
                    <th style='width:12%;'>Estatus</th>
                </tr>
            </thead>
            <tbody>
                {$filaCursos}
            </tbody>
        </table>";

        if (!empty($ganttImage)) {
            $detalleHtml .= "
            <div class='section-title' style='margin-top:20px;'>Línea de Tiempo de Cursos</div>
            <div style='text-align:center;'>
                <img src='{$ganttImage}' style='max-width:100%; max-height:300px; border:1px solid #ccc; border-radius:4px;'>
            </div>";
        } else if (!empty($cursos)) {
            $timelineHtml = "";
            $cursosValidos = array_filter($cursos, fn($c) => !empty($c['fecha_inicio']) && !empty($c['fecha_fin']) && $c['fecha_inicio'] !== '0000-00-00');
            usort($cursosValidos, fn($a, $b) => strtotime($a['fecha_inicio']) <=> strtotime($b['fecha_inicio']));
            foreach ($cursosValidos as $c) {
                $fIni = date('d/m/Y', strtotime($c['fecha_inicio']));
                $fFin = date('d/m/Y', strtotime($c['fecha_fin']));
                $dias = round((strtotime($c['fecha_fin']) - strtotime($c['fecha_inicio'])) / (60 * 60 * 24));
                $timelineHtml .= "<tr>
                    <td>{$c['nombre_curso']}</td>
                    <td class='text-center'>{$fIni}</td>
                    <td class='text-center'>{$fFin}</td>
                    <td class='text-center'>{$dias} días</td>
                </tr>";
            }
            if ($timelineHtml) {
                $detalleHtml .= "
                <div class='section-title' style='margin-top:20px;'>Línea de Tiempo Simplificada</div>
                <table class='data-table'>
                    <thead>
                        <tr>
                            <th style='width:50%; text-align:left;'>Curso</th>
                            <th style='width:16%;'>Inicio</th>
                            <th style='width:16%;'>Fin</th>
                            <th style='width:18%;'>Duración</th>
                        </tr>
                    </thead>
                    <tbody>{$timelineHtml}</tbody>
                </table>";
            }
        }
    }
}

// Convert Logos
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
    'txtAnio' => $txtAnio,
    'txtMeses' => $txtMeses,
    'txtGenero' => $txtGenero,
    'txtEstado' => $txtEstado,
    'txtEdad' => $txtEdad,
    'txtConCurso' => $txtConCurso,
    'txtConAprobado' => $txtConAprobado,
    'kpiData' => $kpiData,
    'estadoEnSistema' => $estadoEnSistema,
    'estadoDisponible' => $estadoDisponible,
    'estadoEnFormacion' => $estadoEnFormacion,
    'filtrados' => $filtrados,
    'totalEncontrados' => $totalEncontrados,
    'conAprobado' => $conAprobado,
    'detalleHtml' => $detalleHtml
];

ob_start();
extract($pdfData);
include __DIR__ . '/../../../vista/reportes/pdf_templates/trayectoria_participantes.php';
$html = ob_get_clean();

$nombreArchivo = 'Reporte_Trayectoria_Participantes_';
if ($anio !== '') {
    $nombreArchivo .= $anio;
} else {
    $nombreArchivo .= date('Y');
}
$nombreArchivo .= '.pdf';

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

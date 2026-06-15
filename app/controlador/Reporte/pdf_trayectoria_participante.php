<?php
ob_start();
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Start session if not started
if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('America/Caracas');

// Obtener datos del usuario
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

// Obtener la data (funciones globales de api_reportes.php)
$kpiData = getTrayectoriaResumen($conn);
$todos = getTrayectoriaParticipantes($conn);

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

// Filtrar en PHP igual que en JS
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

// Preparar Tabla de Participantes
$tablaParticipantesHtml = "";
$countP = 0;

if ($conAprobado == '1') {
    $tableHeaderHtml = "
        <tr>
            <th style='text-align:center; width:4%;'>#</th>
            <th style='width:12%;'>Cédula</th>
            <th style='width:16%;'>Nombre</th>
            <th style='width:16%;'>Apellido</th>
            <th style='width:12%;'>Teléfono</th>
            <th style='width:18%;'>Correo</th>
            <th style='width:22%;'>Cursos Aprobados</th>
        </tr>
    ";
} else {
    $tableHeaderHtml = "
        <tr>
            <th style='text-align:center'>#</th>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Género</th>
            <th style='text-align:center'>Edad</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Fecha Registro</th>
            <th style='text-align:center'>Estado</th>
        </tr>
    ";
}

foreach ($filtrados as $p) {
    $countP++;
    if ($conAprobado == '1') {
        $cursosStr = !empty($p['cursos_aprobados']) ? $p['cursos_aprobados'] : '—';
        $tablaParticipantesHtml .= "<tr>
            <td style='text-align:center'>{$countP}</td>
            <td>{$p['cedula']}</td>
            <td>{$p['primer_nombre']}</td>
            <td>{$p['primer_apellido']}</td>
            <td>{$p['telefono']}</td>
            <td>{$p['correo']}</td>
            <td>{$cursosStr}</td>
        </tr>";
    } else {
        $est = $p['estado'];
        if (strpos($est, 'formaci') !== false && (strpos($est, '?') !== false || strpos($est, 'n') !== false)) {
            $est = 'En formación';
        }
        $fechaR = (!empty($p['fecha_registro']) && $p['fecha_registro'] !== '0000-00-00') ? date('d/m/Y', strtotime($p['fecha_registro'])) : '—';
        $tablaParticipantesHtml .= "<tr>
            <td style='text-align:center'>{$countP}</td>
            <td>{$p['cedula']}</td>
            <td>{$p['primer_nombre']}</td>
            <td>{$p['primer_apellido']}</td>
            <td>{$p['genero']}</td>
            <td style='text-align:center'>{$p['edad']}</td>
            <td>{$p['telefono']}</td>
            <td>{$p['correo']}</td>
            <td>{$fechaR}</td>
            <td style='text-align:center'>{$est}</td>
        </tr>";
    }
}

// Detalle del Participante
$detalleHtml = "";
if ($cedulaSelected) {
    $detalleData = getDetalleParticipante($conn, $cedulaSelected);
    if (!isset($detalleData['error'])) {
        $nombreComp = trim($detalleData['primer_nombre'].' '.$detalleData['segundo_nombre'].' '.$detalleData['primer_apellido'].' '.$detalleData['segundo_apellido']);
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
        <div style='background:#f8f9fa; padding:15px; border-radius:5px; border-left:4px solid #0056b3; margin-bottom:20px;'>
            <table style='width:100%; border:none; margin:0;'>
                <tr>
                    <td style='border:none; width:50%; vertical-align:top; padding:0;'>
                        <h4 style='color:#0056b3; margin-top:0;'>Datos Personales</h4>
                        <p><strong>Cédula:</strong> {$detalleData['cedula']}</p>
                        <p><strong>Nombre Completo:</strong> {$nombreComp}</p>
                        <p><strong>Fecha de Nacimiento:</strong> {$fechaN}</p>
                        <p><strong>Edad:</strong> {$detalleData['edad']}</p>
                        <p><strong>Género:</strong> {$detalleData['genero']}</p>
                    </td>
                    <td style='border:none; width:50%; vertical-align:top; padding:0;'>
                        <h4 style='color:#0056b3; margin-top:0;'>Contacto y Académico</h4>
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
        <table>
            <thead>
                <tr>
                    <th>Curso</th>
                    <th style='text-align:center'>Inscripción</th>
                    <th style='text-align:center'>Inicio</th>
                    <th style='text-align:center'>Fin</th>
                    <th style='text-align:center'>Turno</th>
                    <th style='text-align:center'>Modalidad</th>
                    <th style='text-align:center'>Estatus</th>
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
                <img src='{$ganttImage}' style='max-width:100%; max-height:400px; border:1px solid #ccc;'>
            </div>";
        } else if (!empty($cursos)) {
            // Textual timeline as fallback
            $timelineHtml = "";
            $cursosValidos = array_filter($cursos, fn($c) => !empty($c['fecha_inicio']) && !empty($c['fecha_fin']) && $c['fecha_inicio'] !== '0000-00-00');
            usort($cursosValidos, fn($a, $b) => strtotime($a['fecha_inicio']) <=> strtotime($b['fecha_inicio']));
            foreach ($cursosValidos as $c) {
                $fIni = date('d/m/Y', strtotime($c['fecha_inicio']));
                $fFin = date('d/m/Y', strtotime($c['fecha_fin']));
                $dias = round((strtotime($c['fecha_fin']) - strtotime($c['fecha_inicio'])) / (60 * 60 * 24));
                $timelineHtml .= "<tr>
                    <td>{$c['nombre_curso']}</td>
                    <td style='text-align:center'>{$fIni}</td>
                    <td style='text-align:center'>{$fFin}</td>
                    <td style='text-align:center'>{$dias} días</td>
                </tr>";
            }
            if ($timelineHtml) {
                $detalleHtml .= "
                <div class='section-title' style='margin-top:20px;'>Línea de Tiempo Simplificada</div>
                <table>
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th style='text-align:center'>Inicio</th>
                            <th style='text-align:center'>Fin</th>
                            <th style='text-align:center'>Duración</th>
                        </tr>
                    </thead>
                    <tbody>{$timelineHtml}</tbody>
                </table>";
            }
        }
    }
}

// Convert Logo
$logoPath = __DIR__ . '/../../../assets/img/inces-logo.png';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,'.$logoData;
} else {
    $logoSrc = '';
}

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Reporte_Trayectoria_Participantes</title>
<style>
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
    .header { width: 100%; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
    .header td { vertical-align: middle; }
    .logo { width: 130px; }
    .title { text-align: center; font-size: 20px; font-weight: bold; color: #0056b3; margin-bottom: 4px; }
    .subtitle { text-align: center; font-size: 14px; color: #555; margin-bottom: 2px; }
    .info { text-align: right; font-size: 10px; color: #777; line-height: 1.4; }
    
    .kpi-container { width: 100%; margin-bottom: 20px; text-align: center; }
    .kpi-box { display: inline-block; text-align: center; padding: 12px 5px; margin: 0 0.5% 10px 0.5%; background: #f8f9fa; border-top: 3px solid #0056b3; border-radius: 4px; box-sizing: border-box; }
    .kpi-val { font-size: 16px; font-weight: bold; margin-bottom: 6px; color: #333; }
    .kpi-label { font-size: 9px; text-transform: uppercase; color: #666; font-weight: bold; }
    
    .filter-box { background: #e9ecef; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 11px; }
    .filter-box strong { color: #0056b3; }
    
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
    th { background-color: #0056b3; color: white; padding: 10px 8px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; }
    td { border-bottom: 1px solid #dee2e6; padding: 10px 8px; }
    tr:nth-child(even) td { background-color: #f8f9fa; }
    
    .section-title { font-size: 16px; font-weight: bold; color: #0056b3; margin-bottom: 15px; border-bottom: 1px solid #dee2e6; padding-bottom: 5px; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>
    <!-- HEADER -->
    <table class='header'>
        <tr>
            <td style='width: 25%'><img src='{$logoSrc}' class='logo'></td>
            <td style='width: 50%'>
                <div class='title'>Sistema de Gestión INCES</div>
                <div class='subtitle'>Reporte Trayectoria de Participantes</div>
                <div class='subtitle'>Año Seleccionado: {$txtAnio}</div>
            </td>
            <td style='width: 25%' class='info'>
                Generado por: {$usuarioFull}<br>
                Fecha: " . date('d/m/Y H:i') . "
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 1: RESUMEN GENERAL (KPIs) -->
    <div class='section-title'>Resumen General</div>
    <div class='kpi-container'>
        <div class='kpi-box' style='border-top-color: #0d6efd; width: 15%;'>
            <div class='kpi-val'>" . number_format($kpiData['total'], 0, '', '.') . "</div>
            <div class='kpi-label'>Totales</div>
        </div>
        <div class='kpi-box' style='border-top-color: #198754; width: 15%;'>
            <div class='kpi-val'>" . number_format($kpiData['activos'], 0, '', '.') . "</div>
            <div class='kpi-label'>Con Cursos</div>
        </div>
        <div class='kpi-box' style='border-top-color: #ffc107; width: 15%;'>
            <div class='kpi-val'>" . number_format($kpiData['sin_curso'], 0, '', '.') . "</div>
            <div class='kpi-label'>Sin Cursos</div>
        </div>
        <div class='kpi-box' style='border-top-color: #0dcaf0; width: 15%;'>
            <div class='kpi-val'>" . number_format($estadoEnSistema, 0, '', '.') . "</div>
            <div class='kpi-label'>En Sistema</div>
        </div>
        <div class='kpi-box' style='border-top-color: #20c997; width: 15%;'>
            <div class='kpi-val'>" . number_format($estadoDisponible, 0, '', '.') . "</div>
            <div class='kpi-label'>Disponibles</div>
        </div>
        <div class='kpi-box' style='border-top-color: #6f42c1; width: 15%;'>
            <div class='kpi-val'>" . number_format($estadoEnFormacion, 0, '', '.') . "</div>
            <div class='kpi-label'>En Formación</div>
        </div>
    </div>

    <!-- SECCIÓN 2: FILTROS APLICADOS -->
    <div style='background:#e9ecef; border-radius:4px; padding:8px 12px; margin-bottom:20px; font-size:10px;'>
        <b style='color:#0056b3;'>Filtros Aplicados:</b> 
        Participación: {$txtConCurso} | Certificados: {$txtConAprobado} | Año: {$txtAnio} | Mes: {$txtMeses} | Género: {$txtGenero} | Estado: {$txtEstado} | Edad: {$txtEdad}
    </div>

    <!-- SECCIÓN 3: TABLA DE PARTICIPANTES -->
    <div class='section-title'>Listado de Participantes <span style='color:#666; font-size:10px;'>({$totalEncontrados} registros)</span></div>
    <table>
        <thead>
            {$tableHeaderHtml}
        </thead>
        <tbody>
            {$tablaParticipantesHtml}
        </tbody>
    </table>

    <!-- SECCIÓN 4, 5 y 6: DETALLE DEL PARTICIPANTE -->
    {$detalleHtml}

    <script type='text/php'>
    if (isset(\$pdf)) {
        \$text = 'Sistema de Gestión INCES | Fecha de generación: " . date('d/m/Y H:i') . " | Página ' . \$PAGE_NUM . ' de ' . \$PAGE_COUNT;
        \$font = \$fontMetrics->get_font('Helvetica', 'normal');
        \$pdf->page_text(180, 815, \$text, \$font, 8, array(0.4,0.4,0.4));
    }
    </script>
</body>
</html>";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
if (ob_get_length()) ob_end_clean();
$dompdf->stream('Reporte_Trayectoria_Participantes.pdf', ['Attachment' => false]);
?>

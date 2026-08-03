<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();

require_once __DIR__ . '/../dompdf/autoload.inc.php';
require_once __DIR__ . '/../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Caracas');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}

$id_curso = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_curso <= 0) {
    die('ID de curso inválido');
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// 1. Obtener usuario logueado
$usuarioFull = 'Administrador del Sistema';
if (isset($_SESSION['id_usuario'])) {
    try {
        $sqlUsuario = "SELECT TRIM(CONCAT(
                            COALESCE(primer_nombre, ''), ' ',
                            COALESCE(segundo_nombre, ''), ' ',
                            COALESCE(primer_apellido, ''), ' ',
                            COALESCE(segundo_apellido, '')
                        )) AS nombre_completo
                       FROM tb_usuario WHERE id_usuario = :id_usuario";
        $stmtUsuario = $conn->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
        $stmtUsuario->execute();
        $resUsr = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if ($resUsr && !empty(trim($resUsr['nombre_completo']))) {
            $usuarioFull = trim($resUsr['nombre_completo']);
        }
    } catch (Exception $e) {}
}

// 2. Obtener datos del Curso y Docente
$sql_curso = "SELECT 
                c.id_curso,
                c.nombre_curso,
                c.preimpreso,
                c.fecha_inicio,
                c.fecha_fin,
                c.horas_totales,
                c.max_participantes,
                c.num_inscritos,
                c.turno,
                m.nombre_modalidad,
                ec.nombre_estatusCurso as estado,
                c.id_estatusCurso,
                TRIM(CONCAT(
                    COALESCE(d.primer_nombre, ''), ' ',
                    COALESCE(d.segundo_nombre, ''), ' ',
                    COALESCE(d.primer_apellido, ''), ' ',
                    COALESCE(d.segundo_apellido, '')
                )) as docente_nombre,
                d.cedula as docente_cedula,
                d.correo as docente_correo
              FROM tb_curso c
              LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
              LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
              LEFT JOIN tb_docente_curso dc ON c.id_curso = dc.id_curso
              LEFT JOIN tb_docente d ON dc.id_docente = d.id_docente
              WHERE c.id_curso = :id_curso";

$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
$stmt_curso->execute();
$curso = $stmt_curso->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    die('Curso no encontrado');
}

// 3. Obtener Participantes del Curso
$sql_participantes = "SELECT 
                        p.id_participante,
                        COALESCE(p.nacionalidad, IF(p.cedula >= 80000000, 'E', 'V')) AS nacionalidad,
                        p.cedula,
                        TRIM(CONCAT(
                            COALESCE(p.primer_nombre, ''), ' ',
                            COALESCE(p.segundo_nombre, '')
                        )) as nombres,
                        TRIM(CONCAT(
                            COALESCE(p.primer_apellido, ''), ' ',
                            COALESCE(p.segundo_apellido, '')
                        )) as apellidos,
                        p.edad,
                        p.fecha_nacimiento,
                        p.genero,
                        p.id_discapacidad,
                        d.nombre_discapacidad,
                        pc.estatus_participante
                      FROM tb_participante_curso pc
                      INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
                      LEFT JOIN tb_discapacidad d ON p.id_discapacidad = d.id_discapacidad
                      WHERE pc.id_curso = :id_curso
                      ORDER BY p.primer_apellido, p.primer_nombre";

$stmt_part = $conn->prepare($sql_participantes);
$stmt_part->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
$stmt_part->execute();
$participantes = $stmt_part->fetchAll(PDO::FETCH_ASSOC);

// 4. Procesar Métricas Estadísticas
$totalPart = count($participantes);
$maxPart = intval($curso['max_participantes']) > 0 ? intval($curso['max_participantes']) : 30;
$pctOcupacion = round(($totalPart / $maxPart) * 100, 1);

$aprobados = 0;
$reprobados = 0;
$ausentes = 0;
$enFormacion = 0;

$femenino = 0;
$masculino = 0;

$conDisc = 0;
$sinDisc = 0;
$discDesglose = [];

$bajo18 = 0;
$de18a29 = 0;
$mas30 = 0;

$sumaEdades = 0;

foreach ($participantes as $p) {
    // Rendimiento
    $est = strtolower(trim($p['estatus_participante'] ?? ''));
    if ($est === 'aprobado') $aprobados++;
    elseif ($est === 'reprobado') $reprobados++;
    elseif ($est === 'ausente') $ausentes++;
    else $enFormacion++;

    // Género
    $gen = strtolower(trim($p['genero'] ?? ''));
    if ($gen === 'femenino') $femenino++;
    elseif ($gen === 'masculino') $masculino++;

    // Discapacidad
    $discId = intval($p['id_discapacidad'] ?? 1);
    $discNom = trim($p['nombre_discapacidad'] ?? '');
    if ($discId > 1 && !empty($discNom) && strtolower($discNom) !== 'ninguna' && strtolower($discNom) !== 'sin discapacidad') {
        $conDisc++;
        $discDesglose[$discNom] = ($discDesglose[$discNom] ?? 0) + 1;
    } else {
        $sinDisc++;
    }

    // Edad
    $edad = intval($p['edad']);
    if ($edad < 18) $bajo18++;
    elseif ($edad <= 29) $de18a29++;
    else $mas30++;
    $sumaEdades += $edad;
}

$evaluados = $aprobados + $reprobados + $ausentes;
$pctAprobados = $evaluados > 0 ? round(($aprobados / $evaluados) * 100, 1) : 0;
$pctReprobados = $evaluados > 0 ? round(($reprobados / $evaluados) * 100, 1) : 0;
$pctAusentes = $evaluados > 0 ? round(($ausentes / $evaluados) * 100, 1) : 0;
$pctFormacion = $totalPart > 0 ? round(($enFormacion / $totalPart) * 100, 1) : 0;

$pctFem = $totalPart > 0 ? round(($femenino / $totalPart) * 100, 1) : 0;
$pctMas = $totalPart > 0 ? round(($masculino / $totalPart) * 100, 1) : 0;

$pctDisc = $totalPart > 0 ? round(($conDisc / $totalPart) * 100, 1) : 0;
$pctSinDisc = $totalPart > 0 ? round(($sinDisc / $totalPart) * 100, 1) : 0;

$pctBajo18 = $totalPart > 0 ? round(($bajo18 / $totalPart) * 100, 1) : 0;
$pctDe18a29 = $totalPart > 0 ? round(($de18a29 / $totalPart) * 100, 1) : 0;
$pctMas30 = $totalPart > 0 ? round(($mas30 / $totalPart) * 100, 1) : 0;

$edadPromedio = $totalPart > 0 ? round($sumaEdades / $totalPart, 1) : 0;

// Convertir Logos a Base64
$pathLogoGobierno = __DIR__ . '/../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

// Formatear Fechas
$fInicioStr = !empty($curso['fecha_inicio']) ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Por definir';
$fFinStr = !empty($curso['fecha_fin']) ? date('d/m/Y', strtotime($curso['fecha_fin'])) : 'Por definir';
$docenteStr = !empty($curso['docente_nombre']) ? htmlspecialchars($curso['docente_nombre']) : 'Sin asignar';
$docenteCiStr = !empty($curso['docente_cedula']) ? ' (C.I. ' . number_format($curso['docente_cedula'], 0, '', '.') . ')' : '';

// 5. CONSTRUIR HTML DEL PDF
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Matrícula de Curso #' . $id_curso . '</title>
<style>
    @page {
        margin: 115px 40px 60px 40px;
    }
    body {
        font-family: "Helvetica", Arial, sans-serif;
        color: #334155;
        font-size: 9.5px;
        line-height: 1.45;
    }
    header { 
        position: fixed; 
        top: -90px; 
        left: 0px; 
        right: 0px; 
        height: 75px; 
        border-bottom: 3px solid #0284c7; 
        padding-bottom: 8px;
    }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; }
    .logo { max-height: 50px; }
    .header-title { text-align: center; }
    .header-title h1 { margin: 0; font-size: 15px; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
    .header-title p { margin: 2px 0 0 0; font-size: 10px; color: #0284c7; font-weight: bold; text-transform: uppercase; }
    .header-doc-code { font-size: 8px; color: #64748b; margin-top: 3px; }
    footer {
        position: fixed;
        bottom: -45px;
        left: 0px;
        right: 0px;
        height: 30px;
        border-top: 1px solid #cbd5e1;
        font-size: 8px;
        color: #64748b;
        padding-top: 5px;
    }
    .footer-content {
        width: 100%;
    }
    .footer-left { float: left; text-align: left; }
    .footer-right { float: right; text-align: right; }
    .pagenum:before {
        content: counter(page);
    }
    .clear { clear: both; }

    .section-title {
        font-size: 10.5px;
        font-weight: bold;
        color: #1e3a8a;
        border-bottom: 2px solid #cbd5e1;
        padding-bottom: 3px;
        margin: 18px 0 10px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Ficha Técnica */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    .info-table td {
        padding: 6px 10px;
        border-bottom: 1px solid #edf2f7;
        font-size: 9px;
    }
    .info-label {
        font-weight: bold;
        color: #475569;
        width: 18%;
        background-color: #f1f5f9;
        border-right: 1px solid #e2e8f0;
    }
    .info-val {
        color: #0f172a;
        width: 32%;
        border-right: 1px solid #e2e8f0;
    }
    .info-val:last-child {
        border-right: none;
    }

    /* KPI Cards */
    .kpi-container {
        width: 100%;
        margin-bottom: 15px;
    }
    .kpi-card-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px 0;
    }
    .kpi-card {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 5px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.01);
    }
    .kpi-title {
        font-size: 7.5px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: bold;
        margin-bottom: 3px;
    }
    .kpi-num {
        font-size: 15px;
        font-weight: bold;
        color: #1e3a8a;
    }
    .kpi-sub {
        font-size: 7.5px;
        color: #475569;
        margin-top: 2px;
    }

    /* Tablas Estadísticas */
    .stat-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
    }
    .stat-table th {
        background-color: #1e293b;
        color: #ffffff;
        font-size: 8.5px;
        font-weight: bold;
        padding: 6px 8px;
        text-align: left;
        text-transform: uppercase;
        border-bottom: 1px solid #0f172a;
    }
    .stat-table td {
        padding: 5px 8px;
        border-bottom: 1px solid #cbd5e1;
        font-size: 9px;
    }
    .stat-table tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .stat-table tr:last-child td {
        border-bottom: none;
    }

    /* Tabla de Alumnos */
    .part-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        border: 1px solid #cbd5e1;
    }
    .part-table th {
        background-color: #0f172a;
        color: #ffffff;
        font-size: 8.5px;
        font-weight: bold;
        padding: 6px 7px;
        text-align: center;
        border: 1px solid #0f172a;
        text-transform: uppercase;
    }
    .part-table td {
        padding: 5px 7px;
        border: 1px solid #cbd5e1;
        font-size: 8.5px;
        vertical-align: middle;
    }
    .part-table tr:nth-child(even) {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 2.5px 7px;
        font-size: 8px;
        font-weight: bold;
        border-radius: 999px;
        text-align: center;
        text-transform: uppercase;
    }
    .badge-aprobado { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-reprobado { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .badge-ausente { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-formacion { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

    .badge-disc { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-no-disc { background-color: #f1f5f9; color: #64748b; }

    /* Barra de Progreso Interna */
    .progress-bar-container {
        display: inline-block;
        width: 45px;
        height: 6px;
        background-color: #e2e8f0;
        border-radius: 3px;
        vertical-align: middle;
        overflow: hidden;
        margin-right: 5px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 3px;
    }

    /* Firmas */
    .signatures-box {
        width: 100%;
        margin-top: 35px;
        page-break-inside: avoid;
    }
    .sig-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sig-cell {
        width: 45%;
        text-align: center;
        vertical-align: bottom;
    }
    .sig-line {
        border-top: 1px dashed #475569;
        margin-top: 45px;
        padding-top: 5px;
        font-weight: bold;
        font-size: 9px;
        color: #1e293b;
    }
    .sig-sub {
        font-size: 8px;
        color: #64748b;
    }
</style>
</head>
<body>

<header>
    <table class="header-table">
        <tr>
            <td style="width: 20%;">' . (!empty($logoGobierno) ? '<img src="' . $logoGobierno . '" class="logo" alt="Gobierno">' : '') . '</td>
            <td style="width: 60%;" class="header-title">
                <h1>Informe Gerencial de Administración</h1>
                <p>Matrícula y Evaluación Académica</p>
                <div class="header-doc-code">DOCUMENTO OFICIAL DE GESTIÓN ADMINISTRATIVA · INCES INDUSTRIAL</div>
            </td>
            <td style="width: 20%; text-align: right;">' . (!empty($logoInces) ? '<img src="' . $logoInces . '" class="logo" alt="INCES">' : '') . '</td>
        </tr>
    </table>
</header>

<footer>
    <div class="footer-content">
        <div class="footer-left">
            INCES · Reporte de Matrícula - Curso #' . $id_curso . ' · Emisor: <strong>' . htmlspecialchars($usuarioFull) . '</strong>
        </div>
        <div class="footer-right">
            Página <span class="pagenum"></span>
        </div>
        <div class="clear"></div>
    </div>
</footer>

<!-- 1. FICHA TÉCNICA DEL CURSO -->
<div class="section-title">1. Ficha Técnica del Curso</div>
<table class="info-table">
    <tr>
        <td class="info-label">Curso:</td>
        <td class="info-val"><strong>' . htmlspecialchars($curso['nombre_curso']) . '</strong></td>
        <td class="info-label">Código / Preimpreso:</td>
        <td class="info-val">' . (!empty($curso['preimpreso']) ? htmlspecialchars($curso['preimpreso']) : 'N/A') . '</td>
    </tr>
    <tr>
        <td class="info-label">Modalidad:</td>
        <td class="info-val">' . htmlspecialchars($curso['nombre_modalidad'] ?? 'Presencial') . '</td>
        <td class="info-label">Estatus Curso:</td>
        <td class="info-val"><span class="badge badge-formacion">' . htmlspecialchars($curso['estado'] ?? 'En proceso') . '</span></td>
    </tr>
    <tr>
        <td class="info-label">Turno / Duración:</td>
        <td class="info-val">' . htmlspecialchars($curso['turno']) . ' (' . intval($curso['horas_totales']) . ' Horas)</td>
        <td class="info-label">Cupos ocupados:</td>
        <td class="info-val"><strong>' . $totalPart . ' / ' . $maxPart . '</strong> (' . $pctOcupacion . '% Ocupado)</td>
    </tr>
    <tr>
        <td class="info-label">Periodo Formativo:</td>
        <td class="info-val">' . $fInicioStr . ' al ' . $fFinStr . '</td>
        <td class="info-label">Docente Facilitador:</td>
        <td class="info-val">' . $docenteStr . $docenteCiStr . '</td>
    </tr>
</table>

<!-- 2. KPI CARDS -->
<div class="section-title">2. Indicadores Clave de Desempeño (KPIs)</div>
<div class="kpi-container">
    <table class="kpi-card-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Matrícula Total</div>
                    <div class="kpi-num">' . $totalPart . '</div>
                    <div class="kpi-sub">' . $pctOcupacion . '% Ocupación</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Tasa Aprobación</div>
                    <div class="kpi-num" style="color: #059669;">' . $pctAprobados . '%</div>
                    <div class="kpi-sub">' . $aprobados . ' de ' . $evaluados . ' eval.</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Femenino (F)</div>
                    <div class="kpi-num" style="color: #ec4899;">' . $pctFem . '%</div>
                    <div class="kpi-sub">' . $femenino . ' de ' . $totalPart . ' alumnos</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Tasa Inclusión</div>
                    <div class="kpi-num" style="color: #d97706;">' . $pctDisc . '%</div>
                    <div class="kpi-sub">' . $conDisc . ' con Discapacidad</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Edad Promedio</div>
                    <div class="kpi-num" style="color: #6366f1;">' . $edadPromedio . '</div>
                    <div class="kpi-sub">Años de edad prom.</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- 3. ANÁLISIS ESTADÍSTICO RESUMIDO -->
<div class="section-title">3. Análisis Demográfico, Rendimiento e Inclusión</div>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
    <tr>
        <!-- Columna 1: Rendimiento -->
        <td style="width: 32%; vertical-align: top; padding-right: 8px;">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th colspan="3">A. Rendimiento Académico</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Aprobados</strong></td>
                        <td style="text-align: center;">' . $aprobados . '</td>
                        <td style="text-align: right; color: #059669; font-weight: bold;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctAprobados . '%; background-color:#10b981;"></div></div>' . $pctAprobados . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Reprobados</strong></td>
                        <td style="text-align: center;">' . $reprobados . '</td>
                        <td style="text-align: right; color: #dc2626;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctReprobados . '%; background-color:#ef4444;"></div></div>' . $pctReprobados . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Ausentes</strong></td>
                        <td style="text-align: center;">' . $ausentes . '</td>
                        <td style="text-align: right; color: #64748b;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctAusentes . '%; background-color:#94a3b8;"></div></div>' . $pctAusentes . '%</td>
                    </tr>
                    <tr>
                        <td><strong>En Formación</strong></td>
                        <td style="text-align: center;">' . $enFormacion . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctFormacion . '%; background-color:#3b82f6;"></div></div>' . $pctFormacion . '%</td>
                    </tr>
                    <tr style="background-color: #cbd5e1; font-weight: bold;">
                        <td>Total Matrícula</td>
                        <td style="text-align: center;">' . $totalPart . '</td>
                        <td style="text-align: right;">100%</td>
                    </tr>
                </tbody>
            </table>
        </td>

        <!-- Columna 2: Género y Edad -->
        <td style="width: 34%; vertical-align: top; padding-right: 8px;">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th colspan="3">B. Distribución por Género y Edad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Femenino (Mujeres)</strong></td>
                        <td style="text-align: center;">' . $femenino . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctFem . '%; background-color:#ec4899;"></div></div>' . $pctFem . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Masculino (Hombres)</strong></td>
                        <td style="text-align: center;">' . $masculino . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctMas . '%; background-color:#3b82f6;"></div></div>' . $pctMas . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Adolescentes (&lt;18)</strong></td>
                        <td style="text-align: center;">' . $bajo18 . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctBajo18 . '%; background-color:#10b981;"></div></div>' . $pctBajo18 . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Jóvenes (18 - 29)</strong></td>
                        <td style="text-align: center;">' . $de18a29 . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctDe18a29 . '%; background-color:#f59e0b;"></div></div>' . $pctDe18a29 . '%</td>
                    </tr>
                    <tr>
                        <td><strong>Adultos (30+)</strong></td>
                        <td style="text-align: center;">' . $mas30 . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctMas30 . '%; background-color:#6366f1;"></div></div>' . $pctMas30 . '%</td>
                    </tr>
                </tbody>
            </table>
        </td>

        <!-- Columna 3: Inclusión y Discapacidad -->
        <td style="width: 34%; vertical-align: top;">
            <table class="stat-table">
                <thead>
                    <tr>
                        <th colspan="3">C. Registro de Inclusión Social</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Sin Discapacidad</strong></td>
                        <td style="text-align: center;">' . $sinDisc . '</td>
                        <td style="text-align: right;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctSinDisc . '%; background-color:#64748b;"></div></div>' . $pctSinDisc . '%</td>
                    </tr>
                    <tr style="background-color: #fef3c7;">
                        <td><strong style="color: #92400e;">Con Discapacidad</strong></td>
                        <td style="text-align: center; font-weight: bold; color: #92400e;">' . $conDisc . '</td>
                        <td style="text-align: right; font-weight: bold; color: #92400e;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pctDisc . '%; background-color:#d97706;"></div></div>' . $pctDisc . '%</td>
                    </tr>';

if (!empty($discDesglose)) {
    foreach ($discDesglose as $nDisc => $cDisc) {
        $pDisc = round(($cDisc / $totalPart) * 100, 1);
        $html .= '
                    <tr>
                        <td style="padding-left: 12px; font-size: 8.5px; color: #475569;">· ' . htmlspecialchars($nDisc) . '</td>
                        <td style="text-align: center; font-size: 8.5px;">' . $cDisc . '</td>
                        <td style="text-align: right; font-size: 8.5px;"><div class="progress-bar-container"><div class="progress-bar-fill" style="width:' . $pDisc . '%; background-color:#f59e0b;"></div></div>' . $pDisc . '%</td>
                    </tr>';
    }
} else {
    $html .= '
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; font-size: 8.5px; padding: 10px 0;">Sin casos de discapacidad registrados</td>
                    </tr>';
}

$html .= '
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- 4. MATRÍCULA NOMINAL DE PARTICIPANTES -->
<div class="section-title">4. Matrícula Nominal Detallada de Participantes (' . $totalPart . ')</div>';

if ($totalPart > 0) {
    $html .= '
    <table class="part-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 12%;">Cédula</th>
                <th style="width: 32%;">Apellidos y Nombres</th>
                <th style="width: 7%;">Edad</th>
                <th style="width: 11%;">Género</th>
                <th style="width: 18%;">Discapacidad Específica</th>
                <th style="width: 16%;">Estatus / Calificación</th>
            </tr>
        </thead>
        <tbody>';

    $i = 1;
    foreach ($participantes as $p) {
        $nombreCompleto = htmlspecialchars($p['apellidos'] . ', ' . $p['nombres']);
        $cedulaFmt = htmlspecialchars($p['nacionalidad']) . '-' . number_format($p['cedula'], 0, '', '.');
        $generoFmt = htmlspecialchars($p['genero'] ?? '-');

        // Discapacidad badge / texto
        $discId = intval($p['id_discapacidad'] ?? 1);
        $discNom = trim($p['nombre_discapacidad'] ?? '');
        $discHtml = '<span class="badge badge-no-disc">Ninguna</span>';
        if ($discId > 1 && !empty($discNom) && strtolower($discNom) !== 'ninguna' && strtolower($discNom) !== 'sin discapacidad') {
            $discHtml = '<span class="badge badge-disc">' . htmlspecialchars($discNom) . '</span>';
        }

        // Estatus badge
        $estRaw = trim($p['estatus_participante'] ?? '');
        $estLower = strtolower($estRaw);
        $estBadgeClass = 'badge-formacion';
        if ($estLower === 'aprobado') $estBadgeClass = 'badge-aprobado';
        elseif ($estLower === 'reprobado') $estBadgeClass = 'badge-reprobado';
        elseif ($estLower === 'ausente') $estBadgeClass = 'badge-ausente';

        $estText = !empty($estRaw) ? htmlspecialchars($estRaw) : 'En formación';

        $html .= '
            <tr>
                <td style="text-align: center;">' . $i . '</td>
                <td style="text-align: center; font-weight: bold; color: #1e293b;">' . $cedulaFmt . '</td>
                <td>' . $nombreCompleto . '</td>
                <td style="text-align: center;">' . intval($p['edad']) . '</td>
                <td style="text-align: center;">' . $generoFmt . '</td>
                <td style="text-align: center;">' . $discHtml . '</td>
                <td style="text-align: center;"><span class="badge ' . $estBadgeClass . '">' . $estText . '</span></td>
            </tr>';
        $i++;
    }

    $html .= '
        </tbody>
    </table>';
} else {
    $html .= '
    <div style="text-align: center; padding: 20px; color: #64748b; font-style: italic; background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px;">
        No hay participantes registrados ni inscritos en este curso.
    </div>';
}

// 5. BLOQUE DE FIRMAS Y VALIDACIÓN
$html .= '
<div class="signatures-box">
    <table class="sig-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-line">' . $docenteStr . '</div>
                <div class="sig-sub">Facilitador / Docente del Curso</div>
                <div class="sig-sub">' . $docenteCiStr . '</div>
            </td>
            <td style="width: 10%;"></td>
            <td class="sig-cell">
                <div class="sig-line">Coordinación de Formación Profesional</div>
                <div class="sig-sub">Control Académico · Sello Institucional INCES</div>
                <div class="sig-sub">Fecha de emisión: ' . date('d/m/Y') . '</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>';

// 6. GENERAR Y DESCARGAR PDF MEDIANTE DOMPDF
try {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    ob_end_clean();
    $filename = 'Matricula_Curso_' . $id_curso . '_' . date('Ymd') . '.pdf';
    $dompdf->stream($filename, array('Attachment' => false));
    exit;

} catch (Exception $e) {
    die('Error al generar el documento PDF: ' . $e->getMessage());
}
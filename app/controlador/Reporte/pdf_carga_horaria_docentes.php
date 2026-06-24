<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
// ============================================================
// pdf_carga_horaria_docentes.php
// Controlador DOMPDF - Reporte "Carga Horaria por Docente"
// ============================================================

require_once __DIR__ . '/../dompdf/autoload.inc.php';
require_once __DIR__ . '/../../modelo/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Leer los datos enviados por JSON fetch
$jsonInput = file_get_contents('php://input');
if (!$jsonInput) {
    die("No se recibieron datos para generar el PDF.");
}

$payload = json_decode($jsonInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON: " . json_last_error_msg());
}

// 2. Extraer variables del payload
$anioCarga = $payload['anioCarga'] ?? date('Y');
$periodoCarga = $payload['periodoCarga'] ?? 'year';
$periodoValorCarga = $payload['periodoValorCarga'] ?? null;
$cargaPanelActivo = $payload['cargaPanelActivo'] ?? null;
$cargaFiltroBusqueda = $payload['cargaFiltroBusqueda'] ?? '';
$cargaFiltroTipo = $payload['cargaFiltroTipo'] ?? '';
$cargaFiltroEstado = $payload['cargaFiltroEstado'] ?? '';
$datos = $payload['datos'] ?? [];
$totales = $payload['totales'] ?? [];
$graficoBase64 = $payload['graficoBase64'] ?? '';

// 3. Preparar textos de filtros para la vista
$filtrosTextParts = [];
if (!empty($cargaFiltroTipo)) {
    $filtrosTextParts[] = "Tipo: $cargaFiltroTipo";
}
if (!empty($cargaFiltroEstado)) {
    $filtrosTextParts[] = "Estado: $cargaFiltroEstado";
}
if (!empty($cargaFiltroBusqueda)) {
    $filtrosTextParts[] = "Búsqueda: \"$cargaFiltroBusqueda\"";
}

$textoPeriodo = $anioCarga;
if ($periodoCarga === 'month' && !empty($periodoValorCarga)) {
    $mesesStr = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $mesNombre = $mesesStr[intval($periodoValorCarga) - 1] ?? $periodoValorCarga;
    $textoPeriodo = "$mesNombre $anioCarga";
} else if ($periodoCarga === 'week' && !empty($periodoValorCarga)) {
    $textoPeriodo = "Semana $periodoValorCarga, $anioCarga";
}

// 4. Calcular métricas generales para la sección 2 (Análisis General)
$promedioHoras = 0;
$totalHorasPendientes = 0;
$docenteMejorAvance = null;
$docenteMenorAvance = null;
$distribucionTipo = ['Fijo' => 0, 'Contratado' => 0, 'PDL' => 0, 'Voluntario' => 0];
$distribucionEstado = ['Disponible' => 0, 'Asignado' => 0, 'En formación' => 0, 'Inactivo' => 0];
$docenteMayorCargaFull = null;

if (count($datos) > 0) {
    $totalAsignadas = 0;
    foreach ($datos as &$d) {
        $asignadas = floatval($d['horas_asignadas'] ?? 0);
        $completadas = floatval($d['horas_completadas'] ?? 0);
        
        // Calcular propiedades faltantes que espera la plantilla HTML
        $d['avance_porcentaje'] = $asignadas > 0 ? round(($completadas / $asignadas) * 100, 1) : 0;
        $d['nombre_docente'] = trim(($d['primer_nombre'] ?? '') . ' ' . ($d['primer_apellido'] ?? ''));
        $d['total_cursos'] = intval($d['cursos_activos'] ?? 0);
        
        $avance = $d['avance_porcentaje'];
        
        $totalAsignadas += $asignadas;
        $totalHorasPendientes += max(0, $asignadas - $completadas);
        
        // Distribuciones
        $tipo = $d['tipo_docente'] ?? 'Desconocido';
        if (isset($distribucionTipo[$tipo])) $distribucionTipo[$tipo]++;
        else $distribucionTipo[$tipo] = 1;
        
        $est = $d['estado_docente'] ?? 'Desconocido';
        if (isset($distribucionEstado[$est])) $distribucionEstado[$est]++;
        else $distribucionEstado[$est] = 1;
        
        // Avances (Solo docentes con horas asignadas)
        if ($asignadas > 0) {
            if ($docenteMejorAvance === null || $avance > floatval($docenteMejorAvance['avance_porcentaje'])) {
                $docenteMejorAvance = $d;
            }
            if ($docenteMenorAvance === null || $avance < floatval($docenteMenorAvance['avance_porcentaje'])) {
                $docenteMenorAvance = $d;
            }
        }
    }
    
    $promedioHoras = $totalAsignadas / count($datos);
    
    // El mayor carga real (puede ser distinto al enviado en el kpi de la interfaz, recalculamos por si acaso sobre los filtrados)
    $datosOrdenadosPorCarga = $datos;
    usort($datosOrdenadosPorCarga, function($a, $b) {
        return floatval($b['horas_asignadas']) <=> floatval($a['horas_asignadas']);
    });
    $docenteMayorCargaFull = $datosOrdenadosPorCarga[0] ?? null;
}

// 5. Usuario generador
date_default_timezone_set('America/Caracas');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
$conexion = new Conexion();
$conn = $conexion->getConexion();
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

$pathLogoGobierno = __DIR__ . '/../../../assets/img/gobierno-logo.png';
$pathLogoInces    = __DIR__ . '/../../../assets/img/inces-logo.png';

$logoGobierno = file_exists($pathLogoGobierno) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoGobierno)) : '';
$logoInces = file_exists($pathLogoInces) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoInces)) : '';

$fechaGeneracion = date('d/m/Y h:i A');

$pdfData = [
    'usuarioFull' => $usuarioFull,
    'fechaGeneracion' => $fechaGeneracion,
    'logoGobierno' => $logoGobierno,
    'logoInces' => $logoInces,
    'filtrosTextParts' => $filtrosTextParts,
    'textoPeriodo' => $textoPeriodo,
    
    // Variables enviadas por frontend
    'cargaPanelActivo' => $cargaPanelActivo,
    'datos' => $datos,
    'totales' => $totales,
    'graficoBase64' => $graficoBase64,
    'periodoCarga' => $periodoCarga,
    
    // Variables calculadas aquí
    'promedioHoras' => $promedioHoras,
    'totalHorasPendientes' => $totalHorasPendientes,
    'docenteMejorAvance' => $docenteMejorAvance,
    'docenteMenorAvance' => $docenteMenorAvance,
    'distribucionTipo' => $distribucionTipo,
    'distribucionEstado' => $distribucionEstado,
    'docenteMayorCargaFull' => $docenteMayorCargaFull
];

// 6. Generar HTML
ob_start();
extract($pdfData);
include __DIR__ . '/../../vista/reportes/pdf_templates/carga_horaria_docentes.php';
$html = ob_get_clean();

// 7. Renderizar DOMPDF
$fechaNombreArchivo = date('Y-m-d');
$nombreArchivo = 'INCES_Carga_Horaria_Docentes_' . $anioCarga . '_' . $fechaNombreArchivo . '.pdf';
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . htmlspecialchars(str_replace('.pdf', '', $nombreArchivo)) . '</title>', $html);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Forzar apaisado para acomodar las 9 columnas
$dompdf->render();

$dompdf->stream($nombreArchivo, ['Attachment' => false]);

<?php
require_once('../fpdf.php');
require_once('../../../modelo/conexion.php');

class PDF extends FPDF {
    private $cursoData;
    private $totalCursos;
    private $rutaBase;
    private $usuario;

    function __construct() {
        parent::__construct('L'); // Orientación horizontal
        $this->rutaBase = $_SERVER['DOCUMENT_ROOT'] . '/Sistema de Gestion Inces/';
    }

    function Header() {
        // Logo INCES (posición y tamaño ajustados)
        $this->Image($this->rutaBase . 'assets/img/inces-logo.png', 15, 10, 35);
        
        // Texto institucional
        $this->SetFont('Arial', '', 7);
        $this->SetXY(15, 28);
        $this->Cell(100, 5, utf8_decode('Instituto Nacional de Capacitación y'), 0, 1, 'L');
        $this->SetXY(23, 32);
        $this->Cell(100, 5, utf8_decode('Educación Socialista'), 0, 1, 'L');

        // Código de identificación
        $this->SetFont('Arial', '', 10);
        $this->SetXY(15, 38);
        $this->Cell(100, 5, utf8_decode('CFSB/N°570021211/0020'), 0, 1, 'L');

        // Fecha alineada a la derecha
        $this->SetFont('Arial', '', 10);
        $this->SetXY(200, 36);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = 'Cumaná, ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
        $this->Cell(0, 10, utf8_decode($fecha), 0, 0, 'R');

        // Usuario y fecha completa
        $this->SetFont('Arial', '', 9);
        $usuarioTxt = isset($this->usuario) && strlen(trim($this->usuario)) ? $this->usuario : 'Usuario no identificado';
        $this->Cell(0, 6, utf8_decode('Generado por: ' . $usuarioTxt), 0, 1, 'L');
        $this->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'R');

        // Línea decorativa
        $this->SetDrawColor(128, 0, 32);
        $this->SetLineWidth(1.5);
        $this->Line(15, 26, 60, 26);

        $this->Ln(30);
    }

    function Footer() {


        // Franja inferior
        $this->SetY(-10);
        $this->SetFillColor(128, 0, 32);
        $this->Rect(0, $this->GetY(), 297, 10, 'F'); // 297mm = ancho página landscape

        // Texto pie de página
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, $this->GetY() + 2);
        $this->Cell(277, 5, utf8_decode('Av. Principal Bolivariano INCES INDUSTRIAL, Parroquia Altagracia, Estado Sucre    Teléfonos: 0293-4514290 / 4516753'), 0, 1, 'C');
    }

    function setCursoData($data, $totalCursos) {
        $this->cursoData = $data;
        $this->totalCursos = $totalCursos;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function generarContenido() {
        $this->AddPage('L'); // Forzar landscape
        
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('REPORTE DE CURSOS POR ÁREA FORMATIVA'), 0, 1, 'C');
        $this->Ln(15);

        // Configurar tabla
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        
        // Encabezados (anchos ajustados para landscape)
        $this->Cell(130, 10, 'Area Formativa', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Total Cursos', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Inscritos', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Porcentaje', 1, 1, 'C', true);

        // Datos
        $this->SetFont('Arial', '', 12);
       foreach ($this->cursoData as $row) {
            // LÍNEA CORREGIDA ↓
            $porcentaje = $this->totalCursos > 0 ? number_format(($row['cantidad'] / $this->totalCursos) * 100, 2) : 0;
            
            $this->Cell(130, 10, utf8_decode($row['nombre_areaFormativa']), 1);
            $this->Cell(50, 10, $row['cantidad'], 1, 0, 'C');
            $this->Cell(50, 10, $row['num_inscritos'], 1, 0, 'C');
            $this->Cell(50, 10, $porcentaje . '%', 1, 1, 'C');
        }

        // Total general
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(130, 10, 'TOTAL GENERAL', 1);
        $this->Cell(50, 10, $this->totalCursos, 1, 0, 'C');
        $this->Cell(50, 10, array_sum(array_column($this->cursoData, 'num_inscritos')), 1, 0, 'C');
        $this->Cell(50, 10, '100%', 1, 1, 'C');
    }
}

// Procesar parámetros GET
$areasFiltro = [];
if (isset($_GET['areas']) && !empty($_GET['areas'])) {
    $areasFiltro = array_map('urldecode', explode(',', $_GET['areas']));
    $areasFiltro = array_filter(array_map('trim', $areasFiltro));
}

// Conexión a la base de datos
$conexion = new Conexion();
$pdo = $conexion->getConexion();

// Consulta principal
$sql = "SELECT 
            ca.nombre_areaFormativa, 
            COUNT(c.id_curso) as cantidad, 
            SUM(c.num_inscritos) as num_inscritos 
        FROM tb_curso c
        INNER JOIN tb_areaformativa ca ON c.id_areaformativa = ca.id_areaformativa";

$params = [];
if (!empty($areasFiltro)) {
    $placeholders = implode(',', array_fill(0, count($areasFiltro), '?'));
    $sql .= " WHERE ca.nombre_areaFormativa IN ($placeholders)";
    $params = $areasFiltro;
}
$sql .= " GROUP BY ca.nombre_areaFormativa ORDER BY cantidad DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total de cursos
$sqlTotal = "SELECT COUNT(id_curso) as total FROM tb_curso";
if (!empty($areasFiltro)) {
    $sqlTotal = "SELECT COUNT(c.id_curso) as total 
                 FROM tb_curso c
                 INNER JOIN tb_areaformativa ca ON c.id_areaformativa = ca.id_areaformativa
                 WHERE ca.nombre_areaFormativa IN ($placeholders)";
}

$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($params);
$total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Generar PDF
$pdf = new PDF();
// Incluir usuario desde base de datos
date_default_timezone_set('America/Caracas');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) session_start();
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
    } catch (Exception $e) {
        // Fallback si la consulta falla
    }
}
$pdf->setUsuario($usuarioFull);
$pdf->setCursoData($datos, $total);
$pdf->generarContenido();

// Nombre del archivo
$nombreArchivo = 'Reporte_Cursos_';
if (!empty($areasFiltro)) {
    $nombreArchivo .= 'Filtrado_' . substr(implode('_', $areasFiltro), 0, 30);
} else {
    $nombreArchivo .= 'Completo';
}
$nombreArchivo .= '.pdf';

$pdf->Output('I', $nombreArchivo);
?>
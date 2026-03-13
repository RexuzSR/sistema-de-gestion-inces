<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $cursos = [];
    private $rutaBase;

    function __construct() {
        parent::__construct('L');
        $this->rutaBase = $_SERVER['DOCUMENT_ROOT'] . '/Sistema de Gestion Inces/';
    }

    function Header() {
        $this->Image($this->rutaBase . 'assets/img/inces-logo.png', 15, 10, 35);
        
        $this->SetFont('Arial', '', 7);
        $this->SetXY(15, 28);
        $this->Cell(100, 5, utf8_decode('Instituto Nacional de Capacitación y'), 0, 1, 'L');
        $this->SetXY(23, 32);
        $this->Cell(100, 5, utf8_decode('Educación Socialista'), 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->SetXY(15, 38);
        $this->Cell(100, 5, utf8_decode('CFSB/N°570021211/0020'), 0, 1, 'L');

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = 'Cumaná, ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
        $this->SetXY(200, 36);
        $this->Cell(0, 10, utf8_decode($fecha), 0, 0, 'R');

        $this->SetDrawColor(128, 0, 32);
        $this->SetLineWidth(1.5);
        $this->Line(15, 26, 60, 26);

        $this->Ln(30);
    }

    function Footer() {
        $this->SetY(-10);
        $this->SetFillColor(128, 0, 32);
        $this->Rect(0, $this->GetY(), 297, 10, 'F');
        
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, $this->GetY() + 2);
        $this->Cell(277, 5, utf8_decode('Av. Principal Bolivariano INCES INDUSTRIAL, Parroquia Altagracia, Estado Sucre    Teléfonos: 0293-4514290 / 4516753'), 0, 1, 'C');
    }

    function setCursos($data) {
        $this->cursos = $data;
    }

    function generarContenido() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('REPORTE DE OCUPACIÓN DE CURSOS'), 0, 1, 'C');
        $this->Ln(15);

        // Centrar tabla
        $anchoPagina = 297;
        $anchoTabla = 220;
        $margenIzquierdo = ($anchoPagina - $anchoTabla) / 2;
        $this->SetLeftMargin($margenIzquierdo);

        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        
        $this->Cell(100, 10, 'Curso', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Inscritos', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Cupos Disp.', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Ocupacion (%)', 1, 1, 'C', true);

        $this->SetFont('Arial', '', 12);
        foreach ($this->cursos as $curso) {
            $this->Cell(100, 10, utf8_decode($curso['nombre_curso']), 1);
            $this->Cell(40, 10, $curso['num_inscritos'], 1, 0, 'C');
            $this->Cell(40, 10, $curso['cupos_disponibles'], 1, 0, 'C');
            $this->Cell(40, 10, $curso['ocupacion'] . '%', 1, 1, 'C');
        }

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(100, 10, 'TOTAL GENERAL', 1);
        $this->Cell(40, 10, array_sum(array_column($this->cursos, 'num_inscritos')), 1, 0, 'C');
        $this->Cell(40, 10, array_sum(array_column($this->cursos, 'cupos_disponibles')), 1, 0, 'C');
        $this->Cell(40, 10, '100%', 1, 1, 'C');

        $this->SetLeftMargin(15);
    }
}

// CONEXIÓN Y CONSULTA COMPLETA
$conexion = new Conexion();
$conn = $conexion->getConexion();

$cursosSeleccionados = isset($_POST['cursosSeleccionados']) ? json_decode($_POST['cursosSeleccionados'], true) : [];

$sql = "SELECT 
            nombre_curso,
            num_inscritos,
            (max_participantes - num_inscritos) AS cupos_disponibles,
            ROUND((num_inscritos / max_participantes) * 100, 2) AS ocupacion
        FROM tb_curso
        WHERE max_participantes > 0
        AND id_curso != 1";

if (!empty($cursosSeleccionados)) {
    $placeholders = rtrim(str_repeat('?,', count($cursosSeleccionados)), ',');
    $sql .= " AND nombre_curso IN ($placeholders)";
}

$stmt = $conn->prepare($sql);

if (!empty($cursosSeleccionados)) {
    foreach ($cursosSeleccionados as $k => $curso) {
        $stmt->bindValue($k + 1, $curso);
    }
}

$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF();
$pdf->AddPage();
$pdf->setCursos($cursos);
$pdf->generarContenido();
$pdf->Output('I', 'Reporte_Ocupacion_Cursos.pdf');
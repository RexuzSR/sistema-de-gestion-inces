<?php
require('./fpdf.php');
require('../../modelo/conexion.php');

class PDF extends FPDF
{
    private $participanteData;
    private $cursosData;

    function Header()
    {
        $this->Image('../../../assets/img/inces-logo.png', 10, 8, 40);

        $this->SetFont('Arial', '', 7);
        $this->SetXY(12, 26);
        $this->Cell(100, 5, utf8_decode('Instituto Nacional de Capacitación y'), 0, 1, 'L');
        $this->SetXY(20, 30);
        $this->Cell(100, 5, utf8_decode('Educación Socialista'), 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->SetXY(11, 36);
        $this->Cell(100, 5, utf8_decode('CFSB/N°570021211/0020'), 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->SetXY(140, 34);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = 'Cumaná, ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
        $this->Cell(0, 10, utf8_decode($fecha), 0, 0, 'R');

        $this->SetDrawColor(128, 0, 32);
        $this->SetLineWidth(1.5);
        $this->Line(10, 24, 54, 24);

        $this->Ln(30);
    }

    function Footer()
    {
        $this->SetY(-35);
        $this->Image('../../../assets/img/gobierno-logo.png', 55, $this->GetY(), 100);

        $this->SetY(-10);
        $this->SetFillColor(128, 0, 32);
        $this->Rect(0, $this->GetY(), 210, 10, 'F');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, $this->GetY() + 2);
        $this->Cell(190, 5, utf8_decode('Av. Principal Bolivariano INCES INDUSTRIAL, Parroquia Altagracia, Estado Sucre    Teléfonos: 0293-4514290 / 4516753'), 0, 1, 'C');
    }

    function setParticipanteData($data)
    {
        $this->participanteData = $data;
    }

    function setCursosData($data)
    {
        $this->cursosData = $data;
    }

    function generarContenido()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('HISTORIAL ACADÉMICO DEL PARTICIPANTE'), 0, 1, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 10, utf8_decode('Nombre completo:'));
        $this->Cell(0, 10, utf8_decode($this->participanteData['nombre'] . ' ' . $this->participanteData['apellido']));
        $this->Ln(8);

        $this->Cell(50, 10, utf8_decode('Cédula:'));
        $this->Cell(0, 10, utf8_decode($this->participanteData['cedula']));
        $this->Ln(8);

        $this->Cell(50, 10, utf8_decode('Estado actual:'));
        $this->Cell(0, 10, utf8_decode($this->participanteData['estado']));
        $this->Ln(12);

        if (!empty($this->cursosData)) {
            // Encabezado de la tabla
            $this->SetFont('Arial', 'B', 11);
            $this->SetFillColor(220, 220, 220);
            $this->SetTextColor(0);
            $this->Cell(70, 10, utf8_decode('Curso'), 1, 0, 'C', true);
            $this->Cell(35, 10, utf8_decode('Fecha Inicio'), 1, 0, 'C', true);
            $this->Cell(35, 10, utf8_decode('Fecha Fin'), 1, 0, 'C', true);
            $this->Cell(50, 10, utf8_decode('Estado'), 1, 1, 'C', true);

            $this->SetFont('Arial', '', 11);
            foreach ($this->cursosData as $curso) {
                $this->Cell(70, 10, utf8_decode($curso['nombre_curso']), 1);
                $this->Cell(35, 10, $curso['fecha_inicio'], 1);
                $this->Cell(35, 10, $curso['fecha_fin'], 1);
                $this->Cell(50, 10, utf8_decode($curso['estatus_participante']), 1);
                $this->Ln();
            }
        } else {
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 10, utf8_decode('No se encontraron cursos certificados.'), 0, 1, 'C');
        }
    }

    function generarFirma()
    {
        $this->AddPage();
        $this->Ln(60);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 7, '___________________________', 0, 1, 'C');
        $this->Cell(0, 7, utf8_decode('Abraham Rodríguez'), 0, 1, 'C');
        $this->Cell(0, 7, utf8_decode('Jefe de Centro INCES Bolivariano'), 0, 1, 'C');
    }
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de participante inválido");
}
$id_participante = intval($_GET['id']);

// Conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Participante
$stmtParticipante = $conn->prepare("SELECT id_participante, CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, estado FROM tb_participante WHERE id_participante = :id");
$stmtParticipante->bindParam(':id', $id_participante, PDO::PARAM_INT);
$stmtParticipante->execute();
$participante = $stmtParticipante->fetch(PDO::FETCH_ASSOC);
if (!$participante) {
    die("Participante no encontrado");
}

// Cursos
$stmtCursos = $conn->prepare("
    SELECT 
        c.nombre_curso,
        DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y') as fecha_inicio,
        DATE_FORMAT(c.fecha_fin, '%d/%m/%Y') as fecha_fin,
        pc.estatus_participante
    FROM tb_participante_curso pc
    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
    WHERE pc.id_participante = :id
    AND pc.estatus_participante NOT IN ('En formación', 'Pendiente')
    ORDER BY c.fecha_inicio DESC
");
$stmtCursos->bindParam(':id', $id_participante, PDO::PARAM_INT);
$stmtCursos->execute();
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

// Generar PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->setParticipanteData($participante);
$pdf->setCursosData($cursos);
$pdf->generarContenido();

// Salida
$pdf->Output('I', 'Historial_' . $participante['cedula'] . '.pdf');
?>

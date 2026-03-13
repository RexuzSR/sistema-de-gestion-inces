<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $cursoData;
    private $comparativaData;

    function Header()
    {
        // Logo de INCES
        $this->Image('../../../../assets/img/inces-logo.png', 185, 5, 20);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(45);
        $this->Cell(110, 15, utf8_decode('INCES INDUSTRIAL BOLIVARIANO'), 0, 1, 'C', 0);
        $this->Ln(10);
    }

    function Footer()
    {
        // Pie de página
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function setCursoData($data)
    {
        $this->cursoData = $data;
    }

    function setComparativaData($data)
    {
        $this->comparativaData = $data;
    }

    function generarContenido()
    {
        // Título de la sección de curso (centrado)
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('REPORTE DE CURSOS'), 0, 1, 'C');
        $this->Ln(10);

        // Mostrar datos de la comparación
        $this->SetFont('Arial', '', 12);
        $this->Cell(100, 10, utf8_decode('Cursos Este Mes: ' . $this->comparativaData['total_actual']), 0, 1);
        $this->Cell(100, 10, utf8_decode('Cursos Mes Pasado: ' . $this->comparativaData['total_anterior']), 0, 1);
        $this->Cell(100, 10, utf8_decode('Variación en %: ' . round($this->comparativaData['variacion'], 2) . '%'), 0, 1);
        $this->Ln(10);

        // Mostrar detalles de cursos por estatus
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, utf8_decode('Estatus'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Total de Cursos'), 1, 0, 'C');
        $this->Cell(50, 10, utf8_decode('Porcentaje'), 1, 1, 'C');
        $this->SetFont('Arial', '', 12);

        foreach ($this->comparativaData['estatus'] as $estatus) {
            $this->Cell(40, 10, utf8_decode($estatus['nombre_estatusCurso']), 1, 0, 'C');
            $this->Cell(50, 10, utf8_decode($estatus['total']), 1, 0, 'C');
            $this->Cell(50, 10, utf8_decode($estatus['porcentaje'] . '%'), 1, 1, 'C');
        }

        $this->Ln(10);
    }
}

// Conexión a la base de datos usando PDO
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Fechas de este mes
$inicioMes = date('Y-m-01');
$finMes = date('Y-m-t');

// Consulta para este mes usando PDO
$sqlActual = "SELECT COUNT(*) as total_actual FROM tb_curso WHERE fecha_inicio BETWEEN :inicioMes AND :finMes";
$stmtActual = $conn->prepare($sqlActual);
$stmtActual->bindParam(':inicioMes', $inicioMes);
$stmtActual->bindParam(':finMes', $finMes);
$stmtActual->execute();
$dataActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

// Opcional: comparación contra el mes pasado
$inicioMesPasado = date('Y-m-01', strtotime('-1 month'));
$finMesPasado = date('Y-m-t', strtotime('-1 month'));
$sqlAnterior = "SELECT COUNT(*) as total_anterior FROM tb_curso WHERE fecha_inicio BETWEEN :inicioMesPasado AND :finMesPasado";
$stmtAnterior = $conn->prepare($sqlAnterior);
$stmtAnterior->bindParam(':inicioMesPasado', $inicioMesPasado);
$stmtAnterior->bindParam(':finMesPasado', $finMesPasado);
$stmtAnterior->execute();
$dataAnterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

// Cálculo de variación
$variacion = 0;
if ($dataAnterior['total_anterior'] > 0) {
    $variacion = (($dataActual['total_actual'] - $dataAnterior['total_anterior']) / $dataAnterior['total_anterior']) * 100;
}

// Consulta para obtener el total de cursos por estatus usando PDO
$sqlEstatus = "
    SELECT ec.nombre_estatusCurso, COUNT(c.id_curso) as total,
    ROUND((COUNT(c.id_curso) / (SELECT COUNT(*) FROM tb_curso)) * 100, 2) as porcentaje
    FROM tb_curso c
    JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
    WHERE c.fecha_inicio BETWEEN :inicioMes AND :finMes
    GROUP BY ec.id_estatusCurso
";

$stmtEstatus = $conn->prepare($sqlEstatus);
$stmtEstatus->bindParam(':inicioMes', $inicioMes);
$stmtEstatus->bindParam(':finMes', $finMes);
$stmtEstatus->execute();
$estatusData = [];
while ($row = $stmtEstatus->fetch(PDO::FETCH_ASSOC)) {
    $estatusData[] = $row;
}

$comparativaData = [
    'total_actual' => $dataActual['total_actual'],
    'total_anterior' => $dataAnterior['total_anterior'],
    'variacion' => $variacion,
    'estatus' => $estatusData
];

// Generar PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->setComparativaData($comparativaData);
$pdf->generarContenido();

// Salida del PDF
$pdf->Output('I', 'Reporte_Cursos_Comparativo.pdf');
?>

?>

<?php
require('./fpdf.php');
require('../../modelo/conexion.php');

class PDF extends FPDF
{
    function Header()
    {
        // Logo INCES más grande
        $this->Image('../../../assets/img/inces-logo.png', 10, 8, 40);

        // Nombre del instituto (2 líneas)
        $this->SetFont('Arial', '', 7);
        $this->SetXY(12, 26);
        $this->Cell(100, 5, utf8_decode('Instituto Nacional de Capacitación y'), 0, 1, 'L');
        $this->SetXY(20, 30);
        $this->Cell(100, 5, utf8_decode('Educación Socialista'), 0, 1, 'L');

        // Código de identificación
        $this->SetFont('Arial', '', 10);
        $this->SetXY(11, 36);
        $this->Cell(100, 5, utf8_decode('CFSB/N°570021211/0020'), 0, 1, 'L');

        // Fecha a la derecha
        $this->SetFont('Arial', '', 10);
        $this->SetXY(140, 34);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = 'Cumaná, ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
        $this->Cell(0, 10, utf8_decode($fecha), 0, 0, 'R');

        // Franja vinotinto debajo del logo
        $this->SetDrawColor(128, 0, 32); // Vinotinto
        $this->SetLineWidth(1.5);
        $this->Line(10, 24, 54, 24);

        $this->Ln(30);
    }

    function Footer()
    {
        // Imagen del gobierno (centrada y más grande)
        $this->SetY(-35);
        $this->Image('../../../assets/img/gobierno-logo.png', 55, $this->GetY(), 100);

        // Franja vinotinto más alta justo pegada a la imagen
        $this->SetY(-10);
        $this->SetFillColor(128, 0, 32);
        $this->Rect(0, $this->GetY(), 210, 10, 'F');

        // Texto blanco sobre la franja
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, $this->GetY() + 2);
        $this->Cell(190, 5, utf8_decode('Av. Principal Bolivariano INCES INDUSTRIAL, Parroquia Altagracia, Estado Sucre    Teléfonos: 0293-4514290 / 4516753'), 0, 1, 'C');
    }
}

// Verificar ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_participante = intval($_GET['id']);
} else {
    die("ID del participante no proporcionado.");
}

// Conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Consulta
$query = "SELECT CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, estado, (SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = tb_participante.id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1) AS telefono FROM tb_participante WHERE id_participante = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id_participante, PDO::PARAM_INT);
$stmt->execute();

$participante = $stmt->fetch(PDO::FETCH_ASSOC);

if ($participante) {
    $nombre = $participante['nombre'];
    $apellido = $participante['apellido'];
    $cedula = $participante['cedula'];
    $estado = $participante['estado'];
    $telefono = $participante['telefono'] ?? '';
    $estado_activo = in_array($estado, ['En formación', 'Asignado']) ? 'ACTIVO' : 'INACTIVO';

    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $mes_actual = $meses[date('n')];

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AliasNbPages();

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, utf8_decode('CONSTANCIA DE ESTADO'), 0, 1, 'C');
    $pdf->Ln(5); // Reducido para que quede más pegado

    $pdf->SetFont('Arial', '', 12);
    $texto = utf8_decode(
        "Por medio de la presente se hace constar que el ciudadano(a) $nombre $apellido, "
        . "portador(a) de la Cédula de Identidad N° $cedula, se encuentra actualmente "
        . "en estado $estado_activo dentro de nuestra institución.\n\n"
        . "El Instituto Nacional de Capacitación y Educación Socialista (INCES) Industrial Bolivariano "
        . "certifica que el mencionado participante tiene estatus '$estado' en nuestros registros al "
        . date('d') . " de $mes_actual de " . date('Y') . ".\n\n"
        . "Este documento se expide a solicitud del interesado(a) para los fines que estime conveniente."
    );

    // Añadir teléfono si está disponible
    if (!empty($telefono)) {
        $texto .= "\n\nTeléfono: " . $telefono;
    }
    $pdf->MultiCell(0, 7, $texto); // Espaciado menor para texto más pegado
    $pdf->Ln(15); // Menor espacio antes de la firma

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, utf8_decode('Atentamente,'), 0, 1, 'C');
    $pdf->Ln(18);
    $pdf->Cell(0, 7, '___________________________', 0, 1, 'C');
    $pdf->Cell(0, 7, utf8_decode('Abraham Rodríguez'), 0, 1, 'C');
    $pdf->Cell(0, 7, utf8_decode('Jefe de Centro INCES Bolivariano'), 0, 1, 'C');

    $pdf->Output('I', 'Constancia_Estado_' . $cedula . '.pdf');
} else {
    echo "No se encontró el participante.";
}
?>

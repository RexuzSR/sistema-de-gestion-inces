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

// Validar ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_docente = intval($_GET['id']);
} else {
    die("ID del docente no proporcionado.");
}

// Conexión a BD
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Obtener datos del docente - CONSULTA CORREGIDA
$query = "SELECT 
            d.primer_nombre, 
            d.segundo_nombre, 
            d.primer_apellido, 
            d.segundo_apellido, 
            d.cedula, 
            d.estado_docente,
            d.correo,
            d.edad,
            d.genero,
            d.tipo_docente,
            t.numero_de_telefono as telefono
          FROM tb_docente d
          LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
          WHERE d.id_docente = :id
          LIMIT 1";
          
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id_docente, PDO::PARAM_INT);
$stmt->execute();
$docente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($docente) {
    session_name('SISTEMA_CONTROL_INCES');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['id_usuario'])) {
        require_once '../../modelo/HistorialUtil.php';
        HistorialUtil::registrar(
            'PDF',
            "Se generó la constancia de docente para: " . $docente['primer_nombre'] . " " . $docente['primer_apellido'] . " (C.I. " . $docente['cedula'] . ")",
            'tb_docente',
            $id_docente,
            $_SESSION['id_usuario']
        );
    }
    // Construir nombre completo
    $nombreCompleto = trim(
        $docente['primer_nombre'] . ' ' . 
        ($docente['segundo_nombre'] ? $docente['segundo_nombre'] . ' ' : '') . 
        $docente['primer_apellido'] . ' ' . 
        ($docente['segundo_apellido'] ? $docente['segundo_apellido'] : '')
    );
    
    $cedula = $docente['cedula'];
    $estado = $docente['estado_docente'];
    $correo = $docente['correo'] ?? '';
    $edad = $docente['edad'] ?? '';
    $genero = $docente['genero'] ?? '';
    $tipo_docente = $docente['tipo_docente'] ?? '';
    $telefono = $docente['telefono'] ?? '';

    // Obtener cursos asignados
    $queryCursos = "SELECT c.nombre_curso 
                    FROM tb_docente_curso dc 
                    INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                    WHERE dc.id_docente = :id";
    $stmtCursos = $conn->prepare($queryCursos);
$stmtCursos->bindParam(':id', $id_docente, PDO::PARAM_INT);
    $stmtCursos->execute();
    $cursos = $stmtCursos->fetchAll(PDO::FETCH_COLUMN);

    // Mes actual en texto
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $mes_actual = $meses[date('n')];

    // Iniciar PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AliasNbPages();

    // Título
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('CONSTANCIA DE ESTADO DEL DOCENTE'), 0, 1, 'C');
    $pdf->Ln(5);

    // Texto principal
    $pdf->SetFont('Arial', '', 12);
    $texto = utf8_decode(
        "Por medio de la presente se hace constar que el ciudadano(a) $nombreCompleto, "
        . "portador(a) de la Cédula de Identidad N° $cedula, forma parte del equipo docente del "
        . "INCES Industrial Bolivariano, y actualmente posee estatus '$estado'.\n\n"
    );

    // Agregar información adicional
    $texto .= utf8_decode("Datos adicionales del docente:\n");
    $texto .= utf8_decode("- Edad: $edad años\n");
    $texto .= utf8_decode("- Género: $genero\n");
    $texto .= utf8_decode("- Tipo de docente: $tipo_docente\n");
    $texto .= utf8_decode("- Correo electrónico: $correo\n");
    $texto .= utf8_decode("- Teléfono: " . ($telefono ? $telefono : "No registrado") . "\n\n");

    if (count($cursos) > 0) {
        $texto .= utf8_decode("El mencionado docente se encuentra asignado a los siguientes cursos:\n");
        foreach ($cursos as $curso) {
            $texto .= utf8_decode("- $curso\n");
        }
    } else {
        $texto .= utf8_decode("Actualmente, el docente no se encuentra asignado a ningún curso. "
            . "Sin embargo, forma parte activa del registro de docentes del instituto.\n");
    }

    $texto .= utf8_decode(
        "\nEsta constancia se emite a solicitud del interesado(a), a los " 
        . date('d') . " días del mes de $mes_actual del año " . date('Y') . 
        ", para los fines que estime conveniente."
    );

    $pdf->MultiCell(0, 7, $texto);
    $pdf->Ln(15);

    // Firma
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, utf8_decode('Atentamente,'), 0, 1, 'C');
    $pdf->Ln(18);
    $pdf->Cell(0, 7, '___________________________', 0, 1, 'C');
    $pdf->Cell(0, 7, utf8_decode('Abraham Rodríguez'), 0, 1, 'C');
    $pdf->Cell(0, 7, utf8_decode('Jefe de Centro INCES Bolivariano'), 0, 1, 'C');

    // Descargar PDF
    $pdf->Output('I', 'Constancia_Docente_' . $cedula . '.pdf');
} else {
    echo "No se encontró el docente.";
}
?>
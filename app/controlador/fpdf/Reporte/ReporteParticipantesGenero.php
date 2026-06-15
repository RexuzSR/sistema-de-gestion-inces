<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF {
    private $participantesPorCurso;
    private $rutaBase;
    private $usuario;

    function __construct() {
        parent::__construct('L');  // Orientación horizontal
        $this->rutaBase = $_SERVER['DOCUMENT_ROOT'] . '/Sistema de Gestion Inces/';
    }

    function Header() {
        // Logo INCES
        $this->Image($this->rutaBase . 'assets/img/inces-logo.png', 10, 8, 35);
        
        // Texto institucional
        $this->SetFont('Arial', '', 7);
        $this->SetXY(12, 26);
        $this->Cell(100, 5, utf8_decode('Instituto Nacional de Capacitación y'), 0, 1, 'L');
        $this->SetXY(20, 30);
        $this->Cell(100, 5, utf8_decode('Educación Socialista'), 0, 1, 'L');
        
        // Código institucional
        $this->SetFont('Arial', '', 10);
        $this->SetXY(11, 36);
        $this->Cell(100, 5, utf8_decode('CFSB/N°570021211/0020'), 0, 1, 'L');
        
        // Fecha
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $fecha = 'Cumaná, ' . date('d') . ' de ' . $meses[date('n')] . ' de ' . date('Y');
        $this->SetXY(140, 34);
        $this->Cell(0, 10, utf8_decode($fecha), 0, 0, 'R');

        // Línea decorativa
        $this->SetDrawColor(128, 0, 32);
        $this->SetLineWidth(1.5);
        $this->Line(10, 24, 54, 24);

        // Título principal
        $this->SetY(45);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('REPORTE DE GÉNEROS POR CURSO'), 0, 1, 'C');
        $this->Ln(12);
        // Usuario generado
        $this->SetFont('Arial', '', 9);
        $usuarioTxt = isset($this->usuario) && strlen(trim($this->usuario)) ? $this->usuario : 'Usuario no identificado';
        $this->Cell(0, 6, utf8_decode('Generado por: ' . $usuarioTxt), 0, 1, 'L');
        $this->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1, 'R');
    }

    function Footer() {
       
        // Barra inferior
        $this->SetY(-10);
        $this->SetFillColor(128, 0, 32);
        $this->Rect(0, $this->GetY(), 297, 10, 'F'); // 297mm = tamaño página horizontal
        
        // Texto pie de página
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(10, $this->GetY() + 2);
        $this->Cell(277, 5, utf8_decode('Av. Principal Bolivariano INCES INDUSTRIAL, Parroquia Altagracia, Estado Sucre    Teléfonos: 0293-4514290 / 4516753'), 0, 1, 'C');
    }

    function setParticipantesPorCurso($data) {
        $this->participantesPorCurso = $data;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function generarContenido() {
        // Configurar tabla
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        
        // Encabezados de tabla
        $this->Cell(120, 10, utf8_decode('Curso'), 1, 0, 'C', true);
        $this->Cell(50, 10, utf8_decode('Masculino'), 1, 0, 'C', true);
        $this->Cell(50, 10, utf8_decode('Femenino'), 1, 0, 'C', true);
        $this->Cell(45, 10, utf8_decode('Total'), 1, 1, 'C', true);

        // Datos
        $this->SetFont('Arial', '', 11);
        $this->SetFillColor(255, 255, 255);
        foreach ($this->participantesPorCurso as $curso) {
            $total = $curso['masculino'] + $curso['femenino'];
            
            $this->Cell(120, 10, utf8_decode($curso['nombre_curso']), 1, 0, 'L');
            $this->Cell(50, 10, $curso['masculino'], 1, 0, 'C');
            $this->Cell(50, 10, $curso['femenino'], 1, 0, 'C');
            $this->Cell(45, 10, $total, 1, 1, 'C');
        }
    }
}

// Conexión a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

$sql = "
    SELECT 
        c.nombre_curso,
        SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END) as masculino,
        SUM(CASE WHEN p.genero = 'Femenino' THEN 1 ELSE 0 END) as femenino
    FROM tb_participante_curso pc
    INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
    WHERE pc.estatus_participante = 'Pendiente'
    GROUP BY c.id_curso
    ORDER BY c.nombre_curso
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar PDF
if (!empty($datos)) {
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
    $pdf->AddPage();
    $pdf->setParticipantesPorCurso($datos);
    $pdf->generarContenido();
    $pdf->Output('I', 'Reporte_Generos_Cursos.pdf');
} else {
    die("No hay cursos con participantes pendientes");
}
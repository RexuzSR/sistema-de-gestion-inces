<?php
require('./fpdf.php');
require('../../modelo/conexion.php');

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        // Logo
        $this->Image('../../../assets/img/inces-logo.png', 10, 8, 30);
        
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'MATRÍCULA DE CURSO'), 0, 1, 'C');
        $this->Ln(10);
    }
    
    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Función para limpiar texto
    function limpiarTexto($texto) {
        if (empty($texto)) {
            return '';
        }
        
        // Intentar diferentes codificaciones
        if (mb_detect_encoding($texto, 'UTF-8', true)) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
        }
        
        // Si ya está en ISO-8859-1 o similar, devolverlo tal cual
        return $texto;
    }
}

// Obtener ID del curso
$id_curso = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_curso <= 0) {
    die('ID de curso inválido');
}

// Configurar headers para UTF-8
header('Content-Type: text/html; charset=utf-8');

try {
    // Conectar a la base de datos con UTF-8
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Forzar UTF-8 en la conexión
    $conn->exec("SET NAMES 'utf8'");
    $conn->exec("SET CHARACTER SET utf8");
    
    // 1. OBTENER INFORMACIÓN DEL CURSO
    $sql_curso = "SELECT 
                    c.id_curso,
                    c.nombre_curso,
                    c.fecha_inicio,
                    c.fecha_fin,
                    c.max_participantes,
                    c.num_inscritos,
                    c.turno,
                    m.nombre_modalidad,
                    ec.nombre_estatusCurso as estado
                  FROM tb_curso c
                  LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
                  LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                  WHERE c.id_curso = :id_curso";
    
    $stmt_curso = $conn->prepare($sql_curso);
    $stmt_curso->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
    $stmt_curso->execute();
    $curso = $stmt_curso->fetch(PDO::FETCH_ASSOC);
    
    if (!$curso) {
        die('Curso no encontrado');
    }
    
    // 2. OBTENER PARTICIPANTES INSCRITOS - CORREGIDO PARA MANEJAR NOMBRES NULL
    $sql_participantes = "SELECT 
                            p.cedula,
                            TRIM(CONCAT(
                                COALESCE(p.primer_nombre, ''),
                                ' ',
                                COALESCE(p.segundo_nombre, '')
                            )) as nombres,
                            TRIM(CONCAT(
                                COALESCE(p.primer_apellido, ''),
                                ' ',
                                COALESCE(p.segundo_apellido, '')
                            )) as apellidos,
                            p.edad,
                            p.genero
                          FROM tb_participante p
                          INNER JOIN tb_participante_curso pc ON p.id_participante = pc.id_participante
                          WHERE pc.id_curso = :id_curso
                          ORDER BY p.primer_apellido, p.primer_nombre";
    
    $stmt_participantes = $conn->prepare($sql_participantes);
    $stmt_participantes->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
    $stmt_participantes->execute();
    $participantes = $stmt_participantes->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. CREAR PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    
    // Información del curso
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $pdf->limpiarTexto('Información del Curso'), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Curso:'), 0, 0);
    $pdf->Cell(0, 8, $pdf->limpiarTexto($curso['nombre_curso']), 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Fecha Inicio:'), 0, 0);
    $fecha_inicio = !empty($curso['fecha_inicio']) ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'No definida';
    $pdf->Cell(0, 8, $fecha_inicio, 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Fecha Fin:'), 0, 0);
    $fecha_fin = !empty($curso['fecha_fin']) ? date('d/m/Y', strtotime($curso['fecha_fin'])) : 'No definida';
    $pdf->Cell(0, 8, $fecha_fin, 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Modalidad:'), 0, 0);
    $pdf->Cell(0, 8, $pdf->limpiarTexto($curso['nombre_modalidad']), 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Turno:'), 0, 0);
    $pdf->Cell(0, 8, $pdf->limpiarTexto($curso['turno']), 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Cupos:'), 0, 0);
    $pdf->Cell(0, 8, $curso['num_inscritos'] . '/' . $curso['max_participantes'], 0, 1);
    
    $pdf->Cell(50, 8, $pdf->limpiarTexto('Estado:'), 0, 0);
    $pdf->Cell(0, 8, $pdf->limpiarTexto($curso['estado']), 0, 1);
    
    $pdf->Ln(10);
    
    // Lista de participantes
    if (!empty($participantes)) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $pdf->limpiarTexto('Lista de Participantes (' . count($participantes) . ')'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        
        // Cabecera de la tabla
        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 8, '#', 1, 0, 'C', true);
        $pdf->Cell(30, 8, $pdf->limpiarTexto('Cédula'), 1, 0, 'C', true);
        $pdf->Cell(50, 8, $pdf->limpiarTexto('Nombres'), 1, 0, 'C', true);
        $pdf->Cell(50, 8, $pdf->limpiarTexto('Apellidos'), 1, 0, 'C', true);
        $pdf->Cell(20, 8, $pdf->limpiarTexto('Edad'), 1, 0, 'C', true);
        $pdf->Cell(25, 8, $pdf->limpiarTexto('Género'), 1, 1, 'C', true);
        
        // Datos de participantes
        $pdf->SetFont('Arial', '', 10);
        $contador = 1;
        foreach ($participantes as $participante) {
            // Limpiar espacios extras
            $nombres = trim($participante['nombres']);
            $apellidos = trim($participante['apellidos']);
            
            $pdf->Cell(25, 8, $contador, 1, 0, 'C');
            $pdf->Cell(30, 8, $participante['cedula'], 1, 0, 'C');
            $pdf->Cell(50, 8, $pdf->limpiarTexto($nombres), 1, 0, 'L');
            $pdf->Cell(50, 8, $pdf->limpiarTexto($apellidos), 1, 0, 'L');
            $pdf->Cell(20, 8, $participante['edad'], 1, 0, 'C');
            $pdf->Cell(25, 8, $pdf->limpiarTexto($participante['genero']), 1, 1, 'C');
            $contador++;
        }
    } else {
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Cell(0, 10, $pdf->limpiarTexto('No hay participantes inscritos en este curso.'), 0, 1, 'C');
    }
    
    $pdf->Ln(10);
    
    // Pie del documento
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, $pdf->limpiarTexto('Documento generado el: ') . date('d/m/Y H:i:s'), 0, 1, 'C');
    
    // Salida del PDF
    $filename = 'Matricula_Curso_' . $curso['id_curso'] . '.pdf';
    $pdf->Output('I', $filename);
    
} catch (Exception $e) {
    die('Error al generar el PDF: ' . $e->getMessage());
}
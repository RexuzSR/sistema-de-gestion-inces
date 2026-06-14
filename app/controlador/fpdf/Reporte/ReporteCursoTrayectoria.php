<?php
require('../fpdf.php');
require('../../../modelo/conexion.php');

class PDF extends FPDF
{
    private $filtrosText = '';

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    function setFiltrosText($text)
    {
        $this->filtrosText = $text;
    }

    function Header()
    {
        // Logo de INCES
        $this->Image('../../../../assets/img/inces-logo.png', 10, 8, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Trayectoria de Cursos'), 0, 1, 'C');
        
        // Filtros y Fecha
        $this->SetFont('Arial', 'I', 9);
        if ($this->filtrosText) {
            $this->Cell(0, 5, utf8_decode('Filtros: ' . $this->filtrosText), 0, 1, 'R');
        }
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode('Generado el: ' . date('d/m/Y h:i A')), 0, 1, 'R');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema de Gestión INCES - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Dibujar tarjeta de KPI
    function drawKpiCard($x, $y, $w, $h, $title, $value, $rColor, $gColor, $bColor)
    {
        $this->SetFillColor(248, 250, 252); // Fondo gris muy claro
        $this->Rect($x, $y, $w, $h, 'F');
        
        // Borde izquierdo de color
        $this->SetFillColor($rColor, $gColor, $bColor);
        $this->Rect($x, $y, 3, $h, 'F');
        
        // Valor
        $this->SetXY($x + 6, $y + 4);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor($rColor, $gColor, $bColor);
        $this->Cell($w - 12, 6, utf8_decode($value), 0, 1, 'L');
        
        // Etiqueta
        $this->SetXY($x + 6, $y + 11);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(100, 110, 120);
        $this->Cell($w - 12, 4, strtoupper(utf8_decode($title)), 0, 1, 'L');
        
        // Restablecer color de texto
        $this->SetTextColor(0, 0, 0);
    }

    // Dibujar línea de tiempo de curso
    function drawTimeline($x, $y, $w, $startDate, $endDate, $peakMonth)
    {
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(1);
        $this->Line($x, $y, $x + $w, $y);
        
        // Nodos
        // Inicio
        $this->SetFillColor(13, 110, 253); // Azul
        $this->Circle($x, $y, 2.5, 'F');
        $this->SetXY($x - 15, $y + 4);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 4, 'Inicio', 0, 1, 'C');
        $this->SetXY($x - 15, $y + 8);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 4, $startDate, 0, 1, 'C');

        // Fin
        $this->SetFillColor(25, 135, 84); // Verde
        $this->Circle($x + $w, $y, 2.5, 'F');
        $this->SetXY($x + $w - 15, $y + 4);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 4, 'Fin', 0, 1, 'C');
        $this->SetXY($x + $w - 15, $y + 8);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 4, $endDate, 0, 1, 'C');

        // Pico si existe
        if ($peakMonth) {
            $this->SetFillColor(220, 53, 69); // Rojo
            $this->Circle($x + ($w / 2), $y, 2.5, 'F');
            $this->SetXY($x + ($w / 2) - 25, $y + 4);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(50, 4, utf8_decode('Pico Inscripciones'), 0, 1, 'C');
            $this->SetXY($x + ($w / 2) - 25, $y + 8);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(50, 4, utf8_decode($peakMonth), 0, 1, 'C');
        }
        
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
    }

    // Dibujar gráfico de barras vectorial
    function drawBarChart($x, $y, $w, $h, $labels, $dataPoints)
    {
        $this->SetDrawColor(226, 232, 240);
        $this->Line($x, $y + $h, $x + $w, $y + $h); // Eje X
        
        $n = count($dataPoints);
        if ($n === 0) return;

        $maxVal = max($dataPoints);
        if ($maxVal <= 0) $maxVal = 1;

        $barGap = 4;
        $totalGaps = $n - 1;
        $barWidth = ($w - ($barGap * $totalGaps)) / $n;
        if ($barWidth > 15) $barWidth = 15; // Límite de ancho de barra

        // Dibujar barras y etiquetas
        for ($i = 0; $i < $n; $i++) {
            $val = $dataPoints[$i];
            $barHeight = ($val / $maxVal) * ($h - 10);
            
            $bx = $x + $i * ($barWidth + $barGap);
            $by = $y + $h - $barHeight;
            
            if ($val > 0) {
                $this->SetFillColor(8, 145, 178); // Teal
                $this->Rect($bx, $by, $barWidth, $barHeight, 'F');
                
                // Texto de valor arriba
                $this->SetXY($bx - 2, $by - 4);
                $this->SetFont('Arial', 'B', 7);
                $this->Cell($barWidth + 4, 3, $val, 0, 0, 'C');
            } else {
                $this->SetFillColor(241, 245, 249); // Gris
                $this->Rect($bx, $y + $h - 2, $barWidth, 2, 'F');
            }

            // Etiqueta (Mes)
            $label = $labels[$i];
            // Simplificar etiqueta si es larga
            $parts = explode(' ', $label);
            $shortLabel = isset($parts[0]) ? substr($parts[0], 0, 3) : '';
            if (isset($parts[1])) $shortLabel .= ' ' . substr($parts[1], 2, 2);

            $this->SetXY($bx - 4, $y + $h + 2);
            $this->SetFont('Arial', '', 7);
            $this->Cell($barWidth + 8, 3, utf8_decode($shortLabel), 0, 0, 'C');
        }
    }

    // Dibujar un círculo relleno
    function Circle($x, $y, $r, $style='D')
    {
        $this->_Arc($x + $r, $y, $x, $y + $r, $x - $r, $y, $x, $y - $r, $style);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4, $style='D')
    {
        $op = ($style === 'F') ? 'f' : (($style === 'FD' || $style === 'DF') ? 'B' : 'S');
        $hp = $this->h;
        $this->_out(sprintf('%.2F %.2F m', $x1*$this->k, ($hp-$y1)*$this->k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1*$this->k, ($hp-$y1)*$this->k, $x2*$this->k, ($hp-$y2)*$this->k, $x2*$this->k, ($hp-$y2)*$this->k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x2*$this->k, ($hp-$y2)*$this->k, $x3*$this->k, ($hp-$y3)*$this->k, $x3*$this->k, ($hp-$y3)*$this->k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x3*$this->k, ($hp-$y3)*$this->k, $x4*$this->k, ($hp-$y4)*$this->k, $x4*$this->k, ($hp-$y4)*$this->k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x4*$this->k, ($hp-$y4)*$this->k, $x1*$this->k, ($hp-$y1)*$this->k, $x1*$this->k, ($hp-$y1)*$this->k));
        $this->_out($op);
    }
}

// 1. Cargar filtros
$anio = isset($_POST['anio']) ? $_POST['anio'] : '';
$modalidad = isset($_POST['modalidad']) ? $_POST['modalidad'] : '';
$estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$tipoPeriodo = isset($_POST['tipoPeriodo']) ? $_POST['tipoPeriodo'] : '';
$subPeriodo = isset($_POST['subPeriodo']) ? $_POST['subPeriodo'] : '';

// 2. Conectar a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConexion();

// 3. Obtener resumen de KPIs globales
$stmtKpi = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN id_estatusCurso = 1 THEN 1 ELSE 0 END) as en_espera,
        SUM(CASE WHEN id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
        SUM(CASE WHEN id_estatusCurso = 5 THEN 1 ELSE 0 END) as culminados
    FROM tb_curso
");
$kpis = $stmtKpi->fetch(PDO::FETCH_ASSOC);

// 4. Armar consulta de cursos aplicando filtros
$sql = "SELECT 
            c.id_curso,
            c.nombre_curso,
            m.nombre_modalidad,
            c.turno,
            c.fecha_inicio,
            c.fecha_fin,
            c.num_inscritos,
            c.max_participantes,
            e.nombre_estatusCurso as estatus,
            c.id_estatusCurso,
            a.nombre_ambito
        FROM tb_curso c
        LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        WHERE 1=1";

$params = [];
if (!empty($anio)) {
    $sql .= " AND YEAR(c.fecha_inicio) = :anio";
    $params[':anio'] = intval($anio);
}
if (!empty($modalidad)) {
    $sql .= " AND m.nombre_modalidad = :modalidad";
    $params[':modalidad'] = $modalidad;
}
if (!empty($estatus)) {
    $sql .= " AND e.nombre_estatusCurso = :estatus";
    $params[':estatus'] = $estatus;
}
if (!empty($busqueda)) {
    $sql .= " AND (c.id_curso LIKE :busqueda OR c.nombre_curso LIKE :busqueda)";
    $params[':busqueda'] = '%' . $busqueda . '%';
}
if (!empty($tipoPeriodo) && !empty($subPeriodo)) {
    $sub = intval($subPeriodo);
    if ($tipoPeriodo === 'mensual') {
        $sql .= " AND MONTH(c.fecha_inicio) = :mes";
        $params[':mes'] = $sub;
    } else if ($tipoPeriodo === 'trimestral') {
        if ($sub === 1) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 1 AND 3";
        } else if ($sub === 2) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 4 AND 6";
        } else if ($sub === 3) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 7 AND 9";
        } else if ($sub === 4) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 10 AND 12";
        }
    } else if ($tipoPeriodo === 'semestral') {
        if ($sub === 1) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 1 AND 6";
        } else if ($sub === 2) {
            $sql .= " AND MONTH(c.fecha_inicio) BETWEEN 7 AND 12";
        }
    }
}

$sql .= " ORDER BY c.fecha_inicio DESC";

$stmtCursos = $conn->prepare($sql);
$stmtCursos->execute($params);
$cursosList = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

// Construir texto de filtros para la cabecera
$filtrosTextParts = [];
if ($anio) $filtrosTextParts[] = "Año: $anio";
if ($modalidad) $filtrosTextParts[] = "Mod: $modalidad";
if ($estatus) $filtrosTextParts[] = "Est: $estatus";
if ($busqueda) $filtrosTextParts[] = "Busq: \"$busqueda\"";
if ($tipoPeriodo) {
    $periodLabel = "Periodo: $tipoPeriodo";
    if ($subPeriodo) {
        if ($tipoPeriodo === 'mensual') {
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $periodLabel .= " (" . $meses[intval($subPeriodo) - 1] . ")";
        } else if ($tipoPeriodo === 'trimestral') {
            $periodLabel .= " (T$subPeriodo)";
        } else if ($tipoPeriodo === 'semestral') {
            $periodLabel .= " (S$subPeriodo)";
        }
    }
    $filtrosTextParts[] = $periodLabel;
}
$filtrosText = implode(' | ', $filtrosTextParts);

// 5. Inicializar PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->setFiltrosText($filtrosText);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- KPIs CARD ROW ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, utf8_decode(' Resumen General de Cursos '), 0, 1, 'L', true);
$pdf->Ln(3);

$yKpi = $pdf->GetY();
$kpiW = 45;
$kpiGap = 3;
$pdf->drawKpiCard(10, $yKpi, $kpiW, 20, 'Total Cursos', $kpis['total'] ?? 0, 13, 110, 253);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap), $yKpi, $kpiW, 20, 'En Espera', $kpis['en_espera'] ?? 0, 108, 117, 125);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap) * 2, $yKpi, $kpiW, 20, 'En Proceso', $kpis['en_proceso'] ?? 0, 255, 193, 7);
$pdf->drawKpiCard(10 + ($kpiW + $kpiGap) * 3, $yKpi, $kpiW, 20, 'Culminados', $kpis['culminados'] ?? 0, 25, 135, 84);

$pdf->Ln(25);

// --- TABLA DE CURSOS ---
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode(' Listado de Cursos Filtrados '), 0, 1, 'L', true);
$pdf->Ln(3);

$w = [15, 65, 30, 20, 22, 20, 18]; // Total 190
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(230, 235, 240);
$pdf->Cell($w[0], 7, 'ID', 1, 0, 'C', true);
$pdf->Cell($w[1], 7, 'Nombre del Curso', 1, 0, 'L', true);
$pdf->Cell($w[2], 7, 'Modalidad', 1, 0, 'C', true);
$pdf->Cell($w[3], 7, 'Turno', 1, 0, 'C', true);
$pdf->Cell($w[4], 7, 'Inicio', 1, 0, 'C', true);
$pdf->Cell($w[5], 7, 'Cupos', 1, 0, 'C', true);
$pdf->Cell($w[6], 7, 'Estatus', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($cursosList as $c) {
    // Verificar si hay salto de página en la lista principal
    if ($pdf->GetY() > 260) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetFillColor(230, 235, 240);
        $pdf->Cell($w[0], 7, 'ID', 1, 0, 'C', true);
        $pdf->Cell($w[1], 7, 'Nombre del Curso', 1, 0, 'L', true);
        $pdf->Cell($w[2], 7, 'Modalidad', 1, 0, 'C', true);
        $pdf->Cell($w[3], 7, 'Turno', 1, 0, 'C', true);
        $pdf->Cell($w[4], 7, 'Inicio', 1, 0, 'C', true);
        $pdf->Cell($w[5], 7, 'Cupos', 1, 0, 'C', true);
        $pdf->Cell($w[6], 7, 'Estatus', 1, 0, 'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
    }

    $nom = utf8_decode($c['nombre_curso']);
    if (strlen($nom) > 38) $nom = substr($nom, 0, 35) . '...';
    
    $fechaInicio = ($c['fecha_inicio'] && $c['fecha_inicio'] !== '0000-00-00')
        ? date('d/m/Y', strtotime($c['fecha_inicio']))
        : '—';

    $pdf->Cell($w[0], 6.5, $c['id_curso'], 1, 0, 'C');
    $pdf->Cell($w[1], 6.5, $nom, 1, 0, 'L');
    $pdf->Cell($w[2], 6.5, utf8_decode($c['nombre_modalidad'] ?? '—'), 1, 0, 'C');
    $pdf->Cell($w[3], 6.5, utf8_decode($c['turno'] ?? '—'), 1, 0, 'C');
    $pdf->Cell($w[4], 6.5, $fechaInicio, 1, 0, 'C');
    $pdf->Cell($w[5], 6.5, $c['num_inscritos'] . '/' . $c['max_participantes'], 1, 0, 'C');
    $pdf->Cell($w[6], 6.5, utf8_decode($c['estatus'] ?? '—'), 1, 0, 'C');
    $pdf->Ln();
}

// Helper para calcular meses e inscripciones de un curso
function buildCursoStats($fecha_inicio, $fecha_fin, $participantes, $max_participantes) {
    $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    
    $start = !empty($fecha_inicio) ? strtotime($fecha_inicio) : time();
    $end = !empty($fecha_fin) ? strtotime($fecha_fin) : $start;
    if ($end < $start) $end = $start;
    
    $duracionDias = max(0, round(($end - $start) / 86400));
    
    $counts = [];
    foreach ($participantes as $p) {
        if (!empty($p['fecha_inscripcion']) && $p['fecha_inscripcion'] !== '0000-00-00') {
            $key = date('Y-m', strtotime($p['fecha_inscripcion']));
            if (!isset($counts[$key])) $counts[$key] = 0;
            $counts[$key]++;
        }
    }
    
    $labels = [];
    $dataPoints = [];
    $monthKeys = [];
    
    $curr = $start;
    $guard = 0;
    
    // Generar secuencia mensual entre inicio y fin
    while ($curr <= $end && $guard < 12) {
        $guard++;
        $key = date('Y-m', $curr);
        $m = intval(date('n', $curr)) - 1;
        $y = date('Y', $curr);
        
        $labels[] = $mesesNombres[$m] . ' ' . $y;
        $monthKeys[] = $key;
        $val = isset($counts[$key]) ? $counts[$key] : 0;
        $dataPoints[] = $val;
        
        $curr = strtotime("+1 month", $curr);
    }
    
    // Buscar pico mensual
    $peakMonth = '';
    $peakCount = 0;
    for ($i = 0; $i < count($dataPoints); $i++) {
        if ($dataPoints[$i] > $peakCount) {
            $peakCount = $dataPoints[$i];
            $peakMonth = $labels[$i] . " ($peakCount)";
        }
    }
    
    $inscritosTotales = count($participantes);
    $cupoPct = $max_participantes > 0 ? min(100, round(($inscritosTotales / $max_participantes) * 100)) : 0;
    
    return [
        'duracionDias' => $duracionDias,
        'inscritosTotales' => $inscritosTotales,
        'cupoPct' => $cupoPct,
        'peakMonth' => $peakMonth,
        'labels' => $labels,
        'dataPoints' => $dataPoints
    ];
}

// 6. Sección de detalles por cada curso
foreach ($cursosList as $c) {
    $pdf->AddPage(); // Cada curso comienza en una página limpia
    
    $idCurso = intval($c['id_curso']);
    
    // Obtener detalles completos de la base de datos
    // ── Docente ──
    $stmtDocente = $conn->prepare("
        SELECT CONCAT(d.primer_nombre, ' ', d.primer_apellido) as docente
        FROM tb_docente_curso dc 
        JOIN tb_docente d ON d.id_docente = dc.id_docente 
        WHERE dc.id_curso = :id_curso LIMIT 1
    ");
    $stmtDocente->execute([':id_curso' => $idCurso]);
    $docData = $stmtDocente->fetch(PDO::FETCH_ASSOC);
    $docente = $docData ? $docData['docente'] : 'Sin docente';
    
    // ── Participantes ──
    $stmtParts = $conn->prepare("
        SELECT 
            p.cedula,
            p.primer_nombre,
            p.primer_apellido,
            pc.fecha_inscripcion,
            pc.estatus_participante
        FROM tb_participante_curso pc
        JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE pc.id_curso = :id_curso
        ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    $stmtParts->execute([':id_curso' => $idCurso]);
    $participantes = $stmtParts->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estadísticas
    $stats = buildCursoStats($c['fecha_inicio'], $c['fecha_fin'], $participantes, $c['max_participantes']);
    
    // Conteo de estatus participantes
    $aprobados = 0;
    $reprobados = 0;
    $enFormacion = 0;
    foreach ($participantes as $p) {
        $estPart = $p['estatus_participante'];
        if ($estPart === 'Aprobado') {
            $aprobados++;
        } elseif (in_array($estPart, ['Reprobado', 'Ausente'])) {
            $reprobados++;
        } else {
            $enFormacion++;
        }
    }

    // Cabecera del Curso
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, utf8_decode($c['nombre_curso']), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(100, 110, 120);
    $pdf->Cell(0, 5, utf8_decode("ID: {$c['id_curso']}  |  Modalidad: " . ($c['nombre_modalidad'] ?? '—') . "  |  Docente: $docente  |  Estatus: " . ($c['estatus'] ?? '—')), 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(3);
    
    // KPIs del Curso
    $yC = $pdf->GetY();
    $pdf->drawKpiCard(10, $yC, 45, 16, 'Días Trayectoria', $stats['duracionDias'], 50, 50, 50);
    $pdf->drawKpiCard(58, $yC, 42, 16, 'Inscritos Totales', $stats['inscritosTotales'], 8, 145, 178);
    $pdf->drawKpiCard(103, $yC, 42, 16, 'Cupo Usado', $stats['cupoPct'] . '%', 13, 110, 253);
    $pdf->drawKpiCard(148, $yC, 52, 16, 'Pico Mensual', $stats['peakMonth'] ? $stats['peakMonth'] : '—', 220, 53, 69);
    
    $pdf->Ln(21);
    
    // Línea de tiempo
    $startDateStr = ($c['fecha_inicio'] && $c['fecha_inicio'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '—';
    $endDateStr = ($c['fecha_fin'] && $c['fecha_fin'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_fin'])) : '—';
    
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->Cell(0, 5, utf8_decode('Línea de tiempo del curso'), 0, 1, 'L');
    $pdf->Ln(3);
    $pdf->drawTimeline(25, $pdf->GetY() + 4, 160, $startDateStr, $endDateStr, $stats['peakMonth']);
    
    $pdf->Ln(18);
    
    // Gráfico de barras
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->Cell(0, 5, utf8_decode('Inscripciones por mes'), 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->drawBarChart(15, $pdf->GetY(), 180, 26, $stats['labels'], $stats['dataPoints']);
    
    $pdf->Ln(35);
    
    // Conteo de Aprobados, Reprobados, En formación
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->Cell(0, 6, utf8_decode('Resumen de participantes'), 0, 1, 'L');
    $pdf->Ln(1);
    
    $ySummary = $pdf->GetY();
    $pdf->drawKpiCard(10, $ySummary, 60, 14, 'Aprobados', $aprobados, 25, 135, 84);
    $pdf->drawKpiCard(75, $ySummary, 60, 14, 'Reprobados / Ausentes', $reprobados, 220, 53, 69);
    $pdf->drawKpiCard(140, $ySummary, 60, 14, 'En formación / Asignados', $enFormacion, 13, 110, 253);
    
    $pdf->Ln(18);
    
    // Tabla de Participantes del Curso
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->Cell(0, 6, utf8_decode('Detalle de Participantes Inscritos'), 0, 1, 'L');
    $pdf->Ln(1);
    
    $wPart = [25, 85, 40, 40];
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell($wPart[0], 6.5, 'Cédula', 1, 0, 'C', true);
    $pdf->Cell($wPart[1], 6.5, 'Nombre Completo', 1, 0, 'L', true);
    $pdf->Cell($wPart[2], 6.5, 'Fecha de Inscripción', 1, 0, 'C', true);
    $pdf->Cell($wPart[3], 6.5, 'Estatus', 1, 0, 'C', true);
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 8);
    if (count($participantes) === 0) {
        $pdf->Cell(190, 8, utf8_decode('No hay participantes registrados en este curso.'), 1, 1, 'C');
    } else {
        foreach ($participantes as $p) {
            // Salto de página para participantes
            if ($pdf->GetY() > 270) {
                $pdf->AddPage();
                // Cabecera repetida en nueva página
                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell($wPart[0], 6.5, 'Cédula', 1, 0, 'C', true);
                $pdf->Cell($wPart[1], 6.5, 'Nombre Completo', 1, 0, 'L', true);
                $pdf->Cell($wPart[2], 6.5, 'Fecha de Inscripción', 1, 0, 'C', true);
                $pdf->Cell($wPart[3], 6.5, 'Estatus', 1, 0, 'C', true);
                $pdf->Ln();
                $pdf->SetFont('Arial', '', 8);
            }
            
            $fullname = trim(($p['primer_nombre'] ?? '') . ' ' . ($p['primer_apellido'] ?? ''));
            $fullname = utf8_decode($fullname);
            if (strlen($fullname) > 50) $fullname = substr($fullname, 0, 47) . '...';
            
            $fechaInscr = ($p['fecha_inscripcion'] && $p['fecha_inscripcion'] !== '0000-00-00')
                ? date('d/m/Y', strtotime($p['fecha_inscripcion']))
                : '—';
                
            $pdf->Cell($wPart[0], 6, $p['cedula'] ?? '—', 1, 0, 'C');
            $pdf->Cell($wPart[1], 6, $fullname, 1, 0, 'L');
            $pdf->Cell($wPart[2], 6, $fechaInscr, 1, 0, 'C');
            $pdf->Cell($wPart[3], 6, utf8_decode($p['estatus_participante'] ?? '—'), 1, 0, 'C');
            $pdf->Ln();
        }
    }
}

// 7. Output PDF
$pdf->Output('I', 'Reporte_Trayectoria_Cursos.pdf');
?>

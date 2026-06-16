<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $filtrosText, $kpis (total, en_espera, en_proceso, culminados), $cursosList (array de cursos)
// $cursosDetalles (array indexado por id_curso, con docente, participantes, stats, aprobados, reprobados, enFormacion)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Trayectoria de Cursos</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 */
        @page { margin: 120px 50px 70px 50px; }
        
        body { 
            font-family: "Helvetica", "Arial", sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.4; 
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -90px; 
            left: 0px; 
            right: 0px; 
            height: 70px; 
            border-bottom: 3px solid #dc3545; 
            padding-bottom: 10px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 55px; }
        .header-title { text-align: center; }
        .header-title h1 { margin: 0; font-size: 18px; color: #dc3545; text-transform: uppercase; letter-spacing: 1px; }
        .header-title p { margin: 3px 0 0 0; font-size: 11px; color: #666; }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            border-top: 1px solid #ccc; 
            font-size: 9px; 
            color: #777; 
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; }
        .page-number:after { content: counter(page); }

        /* Utilidades */
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
        .badge { display:inline-block; padding:3px 6px; border-radius:4px; font-size:9px; font-weight:bold; color:#fff; }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }

        /* Títulos de Sección */
        .section-title {
            background-color: #343a40;
            color: #fff;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0 15px 0;
            border-radius: 3px;
        }

        /* Tarjetas de Resumen */
        .kpi-row { width: 100%; margin-bottom: 20px; text-align: center; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 10px 0; }
        .kpi-card { 
            background: #fff; 
            border: 1px solid #e9ecef; 
            border-radius: 6px; 
            padding: 10px 5px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 18px; margin: 0 0 5px 0; color: #212529; }
        .kpi-card p { font-size: 9px; margin: 0; color: #6c757d; text-transform: uppercase; font-weight: bold; }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 10px; 
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 8px 6px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) td { background-color: #f8f9fa; }

        /* Estilos de Curso Específico */
        .curso-header { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .curso-header h2 { margin: 0 0 10px 0; color: #0d6efd; font-size: 16px; }
        .curso-header p { margin: 0; font-size: 11px; color: #495057; }

        .timeline-box { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; background: #fff; border-radius: 4px; text-align: center;}
        
        .chart-box { text-align: center; margin: 20px 0; padding: 15px; border: 1px solid #dee2e6; background: #fff; border-radius: 4px; }
        /* Simple CSS Bar Chart para sustituir FPDF vectorial si no hay base64 */
        .css-bar-chart { display: table; width: 100%; height: 120px; margin-top: 20px; border-bottom: 1px solid #ccc; }
        .css-bar-col { display: table-cell; vertical-align: bottom; text-align: center; padding: 0 5px; }
        .css-bar-inner { background: #0891b2; width: 100%; margin: 0 auto; min-height: 1px; display: block; }
        .css-bar-val { font-size: 8px; font-weight: bold; margin-bottom: 2px; }
        .css-bar-label { font-size: 8px; margin-top: 5px; display: block; color: #666; }
    </style>
</head>
<body>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 25%;"><img src="<?php echo $logoGobierno; ?>" class="logo" alt="Gobierno"></td>
                <td style="width: 50%;" class="header-title">
                    <h1>Informe Gerencial</h1>
                    <p>Trayectoria y Ocupación de Cursos</p>
                </td>
                <td style="width: 25%; text-align: right;"><img src="<?php echo $logoInces; ?>" class="logo" alt="INCES"></td>
            </tr>
        </table>
    </header>

    <!-- Footer Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 35%;">Generado por: <?php echo htmlspecialchars($usuarioFull); ?></td>
                <td style="width: 30%; text-align: center;">Página <span class="page-number"></span></td>
                <td style="width: 35%; text-align: right;">Fecha: <?php echo $fechaGeneracion; ?></td>
            </tr>
        </table>
    </footer>

    <!-- RESUMEN GENERAL (PÁGINA 1) -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; color: #343a40; margin-bottom: 5px;">Resumen Global de Oferta Formativa</h2>
        <?php if($filtrosText): ?>
            <p style="color: #6c757d; font-size: 11px;">Filtros: <?php echo htmlspecialchars($filtrosText); ?></p>
        <?php endif; ?>
    </div>

    <!-- KPIs Globales -->
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo $kpis['total']; ?></h3>
                        <p>Total Cursos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #6c757d;">
                        <h3 style="color:#6c757d"><?php echo $kpis['en_espera']; ?></h3>
                        <p>En Espera</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #ffc107;">
                        <h3 style="color:#ffc107"><?php echo $kpis['en_proceso']; ?></h3>
                        <p>En Proceso</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo $kpis['culminados']; ?></h3>
                        <p>Culminados</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla General -->
    <div class="section-title">Listado de Cursos Filtrados</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 32%; text-align:left;">Nombre del Curso</th>
                <th style="width: 15%;">Modalidad</th>
                <th style="width: 10%;">Turno</th>
                <th style="width: 15%;">Inicio</th>
                <th style="width: 10%;">Cupos</th>
                <th style="width: 10%;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cursosList as $c): 
                $fechaInicio = ($c['fecha_inicio'] && $c['fecha_inicio'] !== '0000-00-00') ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '—';
            ?>
                <tr>
                    <td class="text-center"><?php echo $c['id_curso']; ?></td>
                    <td><?php echo htmlspecialchars($c['nombre_curso']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($c['nombre_modalidad'] ?? '—'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($c['turno'] ?? '—'); ?></td>
                    <td class="text-center"><?php echo $fechaInicio; ?></td>
                    <td class="text-center"><?php echo $c['num_inscritos'] . '/' . $c['max_participantes']; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($c['estatus'] ?? '—'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($cursosList)): ?>
                <tr><td colspan="7" class="text-center py-3">No hay cursos registrados bajo estos filtros.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- DETALLES POR CURSO -->
    <?php foreach ($cursosList as $c): 
        $idCurso = $c['id_curso'];
        if (!isset($cursosDetalles[$idCurso])) continue;
        $detalles = $cursosDetalles[$idCurso];
        $stats = $detalles['stats'];
    ?>
    <div class="page-break"></div>

    <div class="curso-header">
        <h2><?php echo htmlspecialchars($c['nombre_curso']); ?></h2>
        <p>
            <strong>ID:</strong> <?php echo $idCurso; ?> &nbsp;|&nbsp;
            <strong>Modalidad:</strong> <?php echo htmlspecialchars($c['nombre_modalidad'] ?? '—'); ?> &nbsp;|&nbsp;
            <strong>Docente:</strong> <?php echo htmlspecialchars($detalles['docente']); ?> &nbsp;|&nbsp;
            <strong>Estatus:</strong> <?php echo htmlspecialchars($c['estatus'] ?? '—'); ?>
        </p>
    </div>

    <!-- KPIs del Curso -->
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #6c757d;">
                        <h3><?php echo $stats['duracionDias']; ?></h3>
                        <p>Días Trayectoria</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #0891b2;">
                        <h3><?php echo $stats['inscritosTotales']; ?></h3>
                        <p>Inscritos Totales</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #0d6efd;">
                        <h3><?php echo $stats['cupoPct']; ?>%</h3>
                        <p>Cupo Usado</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #dc3545;">
                        <h3 style="font-size: 13px; margin-top:4px;"><?php echo $stats['peakMonth'] ?: '—'; ?></h3>
                        <p>Pico Mensual</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Gráfico de CSS (Inscripciones por mes) -->
    <div class="section-title">Inscripciones por Mes</div>
    <div class="chart-box">
        <?php 
            $maxData = !empty($stats['dataPoints']) ? max($stats['dataPoints']) : 1;
            if ($maxData <= 0) $maxData = 1;
        ?>
        <div class="css-bar-chart">
            <?php foreach($stats['dataPoints'] as $i => $val): 
                $hPct = ($val / $maxData) * 100;
                $labelParts = explode(' ', $stats['labels'][$i]);
                $lbl = (isset($labelParts[0]) ? substr($labelParts[0], 0, 3) : '') . (isset($labelParts[1]) ? ' '.substr($labelParts[1], 2, 2) : '');
            ?>
            <div class="css-bar-col">
                <div class="css-bar-val"><?php echo $val > 0 ? $val : ''; ?></div>
                <div class="css-bar-inner" style="height: <?php echo $val > 0 ? $hPct : 1; ?>%;"></div>
                <div class="css-bar-label"><?php echo htmlspecialchars($lbl); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- KPIs Participantes -->
    <div class="section-title">Resumen de Participantes</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #198754;">
                        <h3><?php echo $detalles['aprobados']; ?></h3>
                        <p>Aprobados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #dc3545;">
                        <h3><?php echo $detalles['reprobados']; ?></h3>
                        <p>Reprobados / Ausentes</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #ffc107;">
                        <h3><?php echo $detalles['enFormacion']; ?></h3>
                        <p>En formación / Asignados</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla Participantes -->
    <div class="section-title">Detalle de Participantes Inscritos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Cédula</th>
                <th style="width: 40%; text-align:left;">Nombre Completo</th>
                <th style="width: 20%;">Fecha Inscripción</th>
                <th style="width: 20%;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($detalles['participantes'])): ?>
                <tr><td colspan="4" class="text-center py-3">No hay participantes registrados en este curso.</td></tr>
            <?php else: ?>
                <?php foreach($detalles['participantes'] as $p): 
                    $fullname = trim(($p['primer_nombre'] ?? '') . ' ' . ($p['primer_apellido'] ?? ''));
                    $fechaInscr = ($p['fecha_inscripcion'] && $p['fecha_inscripcion'] !== '0000-00-00') ? date('d/m/Y', strtotime($p['fecha_inscripcion'])) : '—';
                ?>
                <tr>
                    <td class="text-center"><?php echo htmlspecialchars($p['cedula'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($fullname); ?></td>
                    <td class="text-center"><?php echo $fechaInscr; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($p['estatus_participante'] ?? '—'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php endforeach; ?>

</body>
</html>

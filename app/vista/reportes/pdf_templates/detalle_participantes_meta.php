<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Participantes por Meta</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 / 3.0 */
        @page { 
            size: A4 portrait; 
            margin: 95px 35px 55px 35px; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #2d3748; 
            line-height: 1.45; 
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -80px; 
            left: 0px; 
            right: 0px; 
            height: 65px; 
            border-bottom: 3px solid #dc3545; 
            padding-bottom: 6px;
        }
        .header-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .header-table td { 
            vertical-align: middle; 
        }
        .logo { 
            max-height: 48px; 
        }
        .header-title { 
            text-align: center; 
        }
        .header-title h1 { 
            margin: 0; 
            font-size: 15px; 
            color: #dc3545; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            font-weight: bold;
        }
        .header-title p { 
            margin: 3px 0 0 0; 
            font-size: 10px; 
            color: #4a5568; 
            font-weight: 500;
        }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 25px; 
            border-top: 1px solid #e2e8f0; 
            font-size: 8px; 
            color: #718096; 
            padding-top: 4px;
        }
        .footer-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .footer-table td { 
            vertical-align: top; 
        }
        .page-number:after { 
            content: counter(page); 
        }

        /* Utilidades */
        .page-break { 
            page-break-after: always; 
        }
        .text-center { 
            text-align: center; 
        }
        .text-right { 
            text-align: right; 
        }
        .text-left { 
            text-align: left; 
        }
        .text-primary { color: #0d6efd; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #fd7e14; font-weight: bold; }
        .text-info { color: #0dcaf0; font-weight: bold; }
        .text-muted { color: #718096; }
        
        .fw-bold { font-weight: bold; }
        
        /* Títulos de Sección */
        .section-title {
            background-color: #2d3748;
            color: #ffffff;
            padding: 5px 10px;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 8px 0;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }

        /* Ficha de la Meta */
        .meta-ficha-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 12px;
        }
        .meta-ficha-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-ficha-table td {
            vertical-align: middle;
        }
        .meta-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #cbd5e0;
        }

        /* Grid de KPIs usando tablas */
        .kpi-table { 
            width: 100%; 
            table-layout: fixed; 
            border-collapse: separate; 
            border-spacing: 10px 0; 
            margin-bottom: 15px; 
            margin-left: -10px;
            margin-right: -10px;
        }
        .kpi-card { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 5px; 
            padding: 10px 8px; 
            text-align: center;
        }
        .kpi-card h3 { 
            font-size: 17px; 
            margin: 0 0 2px 0; 
            font-weight: bold;
        }
        .kpi-card p { 
            font-size: 8px; 
            margin: 0 0 2px 0; 
            color: #4a5568; 
            text-transform: uppercase; 
            font-weight: bold; 
        }
        .kpi-card span { 
            font-size: 8px; 
            color: #718096; 
        }

        /* Gráfico */
        .chart-box { 
            text-align: center; 
            margin: 8px 0; 
            padding: 6px; 
            border: 1px solid #e2e8f0; 
            background: #ffffff; 
            border-radius: 4px; 
        }
        .chart-box img { 
            max-width: 100%; 
            max-height: 180px; 
        }
        .chart-legend {
            font-size: 8.5px;
            color: #4a5568;
            margin-top: 4px;
            font-style: italic;
        }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9px; 
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #cbd5e0; 
            padding: 4px 6px; 
        }
        table.data-table th { 
            background-color: #2d3748; 
            color: #ffffff; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) { 
            background-color: #f7fafc; 
        }

        /* Semáforo e Indicadores */
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            text-align: center;
        }
        .bg-badge-success { background-color: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .bg-badge-info { background-color: #ebf8ff; color: #2b6cb0; border: 1px solid #90cdf4; }
        .bg-badge-secondary { background-color: #edf2f7; color: #2d3748; border: 1px solid #e2e8f0; }
        .bg-badge-danger { background-color: #fed7d7; color: #742a2a; border: 1px solid #feb2b2; }

        /* Barras de progreso en CSS puro */
        .progress-container {
            width: 90px;
            height: 6px;
            background-color: #edf2f7;
            border-radius: 99px;
            display: inline-block;
            overflow: hidden;
            margin-right: 5px;
            vertical-align: middle;
        }
        .progress-bar {
            height: 100%;
            border-radius: 99px;
        }

        /* Estructuras de 2 Columnas */
        .row-container {
            width: 100%;
            margin-bottom: 8px;
        }
        .row-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .col-50 {
            width: 50%;
            vertical-align: top;
        }
        .col-48 {
            width: 48%;
            vertical-align: top;
        }
        .col-4 {
            width: 4%;
        }

        /* Cuadros de Análisis */
        .analysis-box {
            background-color: #f7fafc;
            border-left: 4px solid #4a5568;
            padding: 8px 10px;
            border-radius: 0 4px 4px 0;
            font-size: 9px;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #dc3545;
        }
        .analysis-table {
            width: 100%;
            border-collapse: collapse;
        }
        .analysis-table td {
            padding: 3px 5px;
            font-size: 9px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .analysis-table tr:last-child td {
            border-bottom: none;
        }

    </style>
</head>
<body>

    <?php
    // Helper para formatear fechas
    function formatFechaLocal($f) {
        if (!$f) return '—';
        $d = date_create($f);
        if (!$d) return $f;
        
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $dia = date_format($d, 'd');
        $mesIdx = intval(date_format($d, 'm')) - 1;
        $anio = date_format($d, 'Y');
        
        return $dia . '/' . $meses[$mesIdx] . '/' . $anio;
    }
    ?>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    <?php if (!empty($logoGobierno)): ?>
                        <img src="<?php echo $logoGobierno; ?>" class="logo" alt="Gobierno">
                    <?php endif; ?>
                </td>
                <td style="width: 60%;" class="header-title">
                    <h1>Sistema de Gestión INCES</h1>
                    <p>Reporte: Detalle de Participantes por Meta</p>
                </td>
                <td style="width: 20%; text-align: right;">
                    <?php if (!empty($logoInces)): ?>
                        <img src="<?php echo $logoInces; ?>" class="logo" alt="INCES">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </header>

    <!-- Pie de Página Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 40%;">Sistema de Gestión INCES — Detalle de Participantes</td>
                <td style="width: 20%; text-align: center;">Página <span class="page-number"></span></td>
                <td style="width: 40%; text-align: right;">Generado por: <?php echo htmlspecialchars($usuarioFull); ?> | <?php echo $fechaGeneracion; ?></td>
            </tr>
        </table>
    </footer>

    <!-- ========================================== -->
    <!-- SECCIÓN 1: FICHA DE LA META                -->
    <!-- ========================================== -->
    <?php
    $pct = $meta['pct'] ?? 0;
    $metaObj = $meta['meta_objetivo'] ?? 0;
    $formados = $meta['estudiantes_formados'] ?? 0;
    
    if ($pct >= 100) {
        $barColor = '#198754';
        $badgeColorClass = 'bg-badge-success';
    } elseif ($pct >= 60) {
        $barColor = '#f59e0b';
        $badgeColorClass = 'bg-badge-info';
    } else {
        $barColor = '#dc3545';
        $badgeColorClass = 'bg-badge-danger';
    }
    ?>
    <div class="meta-ficha-card" style="border-left: 4px solid <?php echo $barColor; ?>;">
        <table class="meta-ficha-table">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 8px; color: #718096; margin-bottom: 2px;">Meta seleccionada</div>
                    <div style="font-size: 13px; font-weight: bold; color: #1a202c;"><?php echo htmlspecialchars($meta['periodo_tiempo'] ?? '—'); ?></div>
                    <div style="margin-top: 5px;">
                        <span class="meta-badge" style="background: <?php echo $meta['color']['light']; ?>; color: <?php echo $meta['color']['text']; ?>; border-color: <?php echo $meta['color']['border']; ?>;">
                            <?php echo htmlspecialchars($meta['nombre_ambito'] ?? 'Sin ámbito'); ?>
                        </span>
                        <span class="badge bg-badge-secondary"><?php echo htmlspecialchars($meta['tipo_objetivo'] ?? 'Aprobados'); ?></span>
                        <span class="badge <?php echo (($meta['estado'] ?? 'Activa') === 'Activa') ? 'bg-badge-success' : 'bg-badge-secondary'; ?>">
                            <?php echo htmlspecialchars($meta['estado'] ?? 'Activa'); ?>
                        </span>
                    </div>
                </td>
                <td style="width: 22%; text-align: center;">
                    <div style="font-size: 8px; color: #718096; margin-bottom: 2px;">Meta Objetivo</div>
                    <div style="font-size: 13px; font-weight: bold; color: #4a5568;"><?php echo number_format($metaObj, 0, ',', '.'); ?></div>
                </td>
                <td style="width: 22%; text-align: center;">
                    <div style="font-size: 8px; color: #718096; margin-bottom: 2px;">Alcanzado</div>
                    <div style="font-size: 13px; font-weight: bold; color: <?php echo $barColor; ?>;"><?php echo number_format($formados, 0, ',', '.'); ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 10px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 75%;">
                                <div class="progress-container" style="width: 90%;">
                                    <div class="progress-bar" style="background-color: <?php echo $barColor; ?>; width: <?php echo min($pct, 100); ?>%;"></div>
                                </div>
                                <span style="font-family: monospace; font-size: 8.5px; font-weight: bold; color: <?php echo $barColor; ?>;"><?php echo number_format($pct, 1); ?>% Progreso</span>
                            </td>
                            <td style="width: 25%; text-align: right; font-size: 8px; color: #718096;">
                                Rango: <?php echo formatFechaLocal($meta['fecha_inicio'] ?? ''); ?> → <?php echo formatFechaLocal($meta['fecha_fin'] ?? ''); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- SECCIÓN 2: RESUMEN EJECUTIVO (KPIs)       -->
    <!-- ========================================== -->
    <?php
    $totalPart = $kpis['total'] ?? 0;
    $aprobados = $kpis['aprobados'] ?? 0;
    $enFormacion = $kpis['enFormacion'] ?? 0;
    $otros = $kpis['otros'] ?? 0;
    ?>
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-card" style="border-top: 3.5px solid #0d6efd;">
                    <p>Total Participantes</p>
                    <h3 style="color:#0d6efd"><?php echo number_format($totalPart, 0, ',', '.'); ?></h3>
                    <span>En esta meta</span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 3.5px solid #198754;">
                    <p>Aprobados</p>
                    <h3 style="color:#198754"><?php echo number_format($aprobados, 0, ',', '.'); ?></h3>
                    <span><?php echo $totalPart > 0 ? number_format(($aprobados / $totalPart) * 100, 1) : 0; ?>% del total</span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 3.5px solid #0dcaf0;">
                    <p>En Formación</p>
                    <h3 style="color:#0dcaf0"><?php echo number_format($enFormacion, 0, ',', '.'); ?></h3>
                    <span><?php echo $totalPart > 0 ? number_format(($enFormacion / $totalPart) * 100, 1) : 0; ?>% del total</span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 3.5px solid #6c757d;">
                    <p>Otros / Retirados</p>
                    <h3 style="color:#6c757d"><?php echo number_format($otros, 0, ',', '.'); ?></h3>
                    <span><?php echo $totalPart > 0 ? number_format(($otros / $totalPart) * 100, 1) : 0; ?>% del total</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- ========================================== -->
    <!-- SECCIÓN 3: GRÁFICO DE DISTRIBUCIÓN         -->
    <!-- ========================================== -->
    
    <?php
    // Cálculos de Análisis (Sección 6)
    $cursosAsoc = [];
    $cursosConteo = [];
    foreach ($participantes as $p) {
        $cName = $p['nombre_curso'] ?? 'Desconocido';
        $cursosAsoc[$cName] = true;
        if (!isset($cursosConteo[$cName])) {
            $cursosConteo[$cName] = 0;
        }
        $cursosConteo[$cName]++;
    }
    $totalCursosUnicos = count($cursosAsoc);
    
    $cursoMasParticipantes = 'Ninguno';
    $maxPartEnCurso = 0;
    foreach ($cursosConteo as $cName => $count) {
        if ($count > $maxPartEnCurso) {
            $maxPartEnCurso = $count;
            $cursoMasParticipantes = $cName;
        }
    }
    
    $tasaAprobacion = $totalPart > 0 ? ($aprobados / $totalPart) * 100 : 0;
    $tasaEnFormacion = $totalPart > 0 ? ($enFormacion / $totalPart) * 100 : 0;
    ?>
    
    <div class="row-container">
        <table>
            <tr>
                <!-- Columna Gráfico -->
                <td class="col-48">
                    <div class="section-title" style="margin-top:0px;">Visualización Gráfica</div>
                    <div class="chart-box">
                        <?php if (!empty($graficoBase64)): ?>
                            <img src="<?php echo $graficoBase64; ?>" alt="Gráfico de Distribución">
                        <?php else: ?>
                            <div style="height: 100px; line-height: 100px; color: #a0aec0; text-align: center;">Gráfico no disponible</div>
                        <?php endif; ?>
                        <div class="chart-legend">
                            Distribución por Estatus — <strong><?php echo ($tipoGrafico === 'doughnut') ? 'Dona' : 'Torres'; ?></strong>
                        </div>
                    </div>
                </td>
                
                <td class="col-4"></td> <!-- Separador -->
                
                <!-- Columna Análisis -->
                <td class="col-48">
                    <div class="section-title" style="margin-top:0px;">Análisis de Participantes</div>
                    <div class="analysis-box">
                        <table class="analysis-table">
                            <tr>
                                <td class="fw-bold" style="width: 60%;">Tasa de Aprobación Global:</td>
                                <td class="text-right text-success fw-bold"><?php echo number_format($tasaAprobacion, 1); ?>%</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">En Formación Activa:</td>
                                <td class="text-right text-primary fw-bold"><?php echo number_format($tasaEnFormacion, 1); ?>%</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cursos Distintos Vinculados:</td>
                                <td class="text-right fw-bold"><?php echo $totalCursosUnicos; ?></td>
                            </tr>
                            <tr style="height: 38px;">
                                <td class="fw-bold" style="vertical-align: top; padding-top: 4px;">Curso Mayor Demanda:</td>
                                <td class="text-right text-muted" style="font-size: 8px; vertical-align: top; padding-top: 4px;">
                                    <strong><?php echo strlen($cursoMasParticipantes) > 35 ? substr($cursoMasParticipantes, 0, 32) . '...' : htmlspecialchars($cursoMasParticipantes); ?></strong>
                                    <?php if ($maxPartEnCurso > 0): ?>
                                        <span class="text-primary fw-bold">(<?php echo $maxPartEnCurso; ?> part.)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="margin-top: 15px; text-align: left; padding: 4px 8px; background: #ebf8ff; border: 1px solid #90cdf4; border-radius: 4px;">
                        <span style="font-size: 8px; color: #2b6cb0; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 2px;">Estatus Leyenda:</span>
                        <table style="width: 100%; font-size: 7.5px; color: #2d3748;">
                            <tr>
                                <td>Aprobados: <strong><?php echo $aprobados; ?></strong> (<?php echo number_format($tasaAprobacion, 1); ?>%)</td>
                                <td>En Formación: <strong><?php echo $enFormacion; ?></strong> (<?php echo number_format($tasaEnFormacion, 1); ?>%)</td>
                                <td>Otros: <strong><?php echo $otros; ?></strong> (<?php echo $totalPart > 0 ? number_format(($otros / $totalPart) * 100, 1) : 0; ?>%)</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Salto de página natural para los listados detallados -->
    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN 4: PANEL KPI ACTIVO (Si aplica)     -->
    <!-- ========================================== -->
    <?php if (!empty($panelActivo)): ?>
        <?php
        $datosPanel = [];
        $tituloPanel = '';
        $colorCabeceraPanel = '';
        if ($panelActivo === 'total') {
            $datosPanel = $participantes;
            $tituloPanel = 'Todos los Participantes Registrados';
            $colorCabeceraPanel = '#0d6efd';
        } elseif ($panelActivo === 'aprobados') {
            $datosPanel = array_values(array_filter($participantes, function($p) { return $p['estatus_participante'] === 'Aprobado'; }));
            $tituloPanel = 'Desglose: Participantes Aprobados';
            $colorCabeceraPanel = '#198754';
        } elseif ($panelActivo === 'enFormacion') {
            $datosPanel = array_values(array_filter($participantes, function($p) { return $p['estatus_participante'] === 'En formación'; }));
            $tituloPanel = 'Desglose: Participantes En Formación Activa';
            $colorCabeceraPanel = '#0dcaf0';
        } elseif ($panelActivo === 'otros') {
            $datosPanel = array_values(array_filter($participantes, function($p) { return !in_array($p['estatus_participante'], ['Aprobado', 'En formación']); }));
            $tituloPanel = 'Desglose: Participantes Estatus Retirados u Otros';
            $colorCabeceraPanel = '#6c757d';
        }
        
        $esAprobados = ($meta['tipo_objetivo'] ?? 'Aprobados') === 'Aprobados';
        ?>

        <?php if (!empty($datosPanel)): ?>
            <div class="section-title" style="background-color: <?php echo $colorCabeceraPanel; ?>;"><?php echo htmlspecialchars($tituloPanel); ?> (<?php echo count($datosPanel); ?>)</div>
            <table class="data-table">
                <thead>
                    <tr style="background-color: <?php echo $colorCabeceraPanel; ?>;">
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 5%;" class="text-center">#</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 25%;">Nombre Completo</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 12%;" class="text-center">Cédula</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 35%;">Curso Formativo</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 12%;" class="text-center">Estatus</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff; width: 11%;" class="text-center"><?php echo $esAprobados ? 'F. Aprobación' : 'F. Inscripción'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $idx = 1; foreach ($datosPanel as $p): ?>
                        <?php
                        $est = $p['estatus_participante'] ?? '';
                        $badgeClass = ($est === 'Aprobado') ? 'bg-badge-success' : (($est === 'En formación') ? 'bg-badge-info' : 'bg-badge-secondary');
                        $fechaVal = $esAprobados ? ($p['fecha_aprobacion'] ?? '') : ($p['fecha_inscripcion'] ?? '');
                        ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $idx++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($p['nombre_completo'] ?? '—'); ?></td>
                            <td class="text-center"><span class="badge bg-badge-secondary"><?php echo htmlspecialchars($p['cedula'] ?? '—'); ?></span></td>
                            <td style="font-size: 8px; color: #4a5568;"><?php echo htmlspecialchars($p['nombre_curso'] ?? '—'); ?></td>
                            <td class="text-center"><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($est); ?></span></td>
                            <td class="text-center" style="font-size: 8px;"><?php echo formatFechaLocal($fechaVal); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="page-break"></div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ========================================== -->
    <!-- SECCIÓN 5: TABLA COMPLETA DE PARTICIPANTES -->
    <!-- ========================================== -->
    <div class="section-title">Listado Completo de Participantes (Anexo)</div>
    
    <?php if (empty($participantes)): ?>
        <div class="text-center py-5 text-muted" style="border: 1px solid #e2e8f0; border-radius: 4px; background: #ffffff;">
            No se encontraron participantes registrados para esta meta.
        </div>
    <?php else: ?>
        <?php $esAprobados = ($meta['tipo_objetivo'] ?? 'Aprobados') === 'Aprobados'; ?>
        <table class="data-table" style="page-break-inside: auto;">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 25%;">Nombre Completo</th>
                    <th style="width: 12%;" class="text-center">Cédula</th>
                    <th style="width: 35%;">Curso Formativo</th>
                    <th style="width: 12%;" class="text-center">Estatus</th>
                    <th style="width: 11%;" class="text-center"><?php echo $esAprobados ? 'F. Aprobación' : 'F. Inscripción'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $idx = 1; foreach ($participantes as $p): ?>
                    <?php
                    $est = $p['estatus_participante'] ?? '';
                    $badgeClass = ($est === 'Aprobado') ? 'bg-badge-success' : (($est === 'En formación') ? 'bg-badge-info' : 'bg-badge-secondary');
                    $fechaVal = $esAprobados ? ($p['fecha_aprobacion'] ?? '') : ($p['fecha_inscripcion'] ?? '');
                    ?>
                    <tr style="page-break-inside: avoid; page-break-after: auto;">
                        <td class="text-center text-muted"><?php echo $idx++; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($p['nombre_completo'] ?? '—'); ?></td>
                        <td class="text-center"><span class="badge bg-badge-secondary"><?php echo htmlspecialchars($p['cedula'] ?? '—'); ?></span></td>
                        <td style="font-size: 8px; color: #4a5568;"><?php echo htmlspecialchars($p['nombre_curso'] ?? '—'); ?></td>
                        <td class="text-center"><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($est); ?></span></td>
                        <td class="text-center" style="font-size: 8px;"><?php echo formatFechaLocal($fechaVal); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>

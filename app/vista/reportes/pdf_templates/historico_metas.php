<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - Histórico de Metas</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 / 3.0 */
        @page { 
            size: A4 landscape; 
            margin: 100px 40px 60px 40px; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10.5px; 
            color: #2d3748; 
            line-height: 1.4; 
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -85px; 
            left: 0px; 
            right: 0px; 
            height: 70px; 
            border-bottom: 3px solid #dc3545; 
            padding-bottom: 8px;
        }
        .header-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .header-table td { 
            vertical-align: middle; 
        }
        .logo { 
            max-height: 52px; 
        }
        .header-title { 
            text-align: center; 
        }
        .header-title h1 { 
            margin: 0; 
            font-size: 17px; 
            color: #dc3545; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            font-weight: bold;
        }
        .header-title p { 
            margin: 4px 0 0 0; 
            font-size: 11px; 
            color: #4a5568; 
            font-weight: 500;
        }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -45px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            border-top: 1px solid #e2e8f0; 
            font-size: 9px; 
            color: #718096; 
            padding-top: 5px;
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
        
        .fw-bold { font-weight: bold; }
        
        /* Títulos de Sección */
        .section-title {
            background-color: #2d3748;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 11.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 10px 0;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }

        /* Grid de KPIs usando tablas */
        .kpi-table { 
            width: 100%; 
            table-layout: fixed; 
            border-collapse: separate; 
            border-spacing: 12px 0; 
            margin-bottom: 15px; 
            margin-left: -12px;
            margin-right: -12px;
        }
        .kpi-card { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 6px; 
            padding: 12px 10px; 
            text-align: center;
        }
        .kpi-card h3 { 
            font-size: 19px; 
            margin: 0 0 3px 0; 
            font-weight: bold;
        }
        .kpi-card p { 
            font-size: 9px; 
            margin: 0 0 2px 0; 
            color: #4a5568; 
            text-transform: uppercase; 
            font-weight: bold; 
        }
        .kpi-card span { 
            font-size: 8.5px; 
            color: #718096; 
        }

        /* Gráfico */
        .chart-box { 
            text-align: center; 
            margin: 10px 0; 
            padding: 8px; 
            border: 1px solid #e2e8f0; 
            background: #ffffff; 
            border-radius: 5px; 
        }
        .chart-box img { 
            max-width: 100%; 
            max-height: 220px; 
        }
        .chart-legend {
            font-size: 9px;
            color: #4a5568;
            margin-top: 6px;
            font-style: italic;
        }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9.5px; 
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #cbd5e0; 
            padding: 5px 7px; 
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
            padding: 2px 5px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            text-align: center;
        }
        .bg-badge-success { background-color: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .bg-badge-warning { background-color: #feebc8; color: #744210; border: 1px solid #fbd38d; }
        .bg-badge-danger { background-color: #fed7d7; color: #742a2a; border: 1px solid #feb2b2; }
        .bg-badge-secondary { background-color: #edf2f7; color: #2d3748; border: 1px solid #e2e8f0; }

        /* Barras de progreso en CSS puro */
        .progress-container {
            width: 80px;
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
            margin-bottom: 10px;
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
            padding: 10px 12px;
            border-radius: 0 4px 4px 0;
            font-size: 9.5px;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #dc3545;
        }
        .analysis-table {
            width: 100%;
            border-collapse: collapse;
        }
        .analysis-table td {
            padding: 4px 6px;
            font-size: 9.5px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .analysis-table tr:last-child td {
            border-bottom: none;
        }

    </style>
</head>
<body>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 25%;">
                    <?php if (!empty($logoGobierno)): ?>
                        <img src="<?php echo $logoGobierno; ?>" class="logo" alt="Gobierno">
                    <?php endif; ?>
                </td>
                <td style="width: 50%;" class="header-title">
                    <h1>Sistema de Gestión INCES</h1>
                    <p>Reporte: Histórico de Metas</p>
                </td>
                <td style="width: 25%; text-align: right;">
                    <?php if (!empty($logoInces)): ?>
                        <img src="<?php echo $logoInces; ?>" class="logo" alt="INCES">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </header>

    <!-- Footer Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 40%;">Sistema de Gestión INCES — Histórico de Metas</td>
                <td style="width: 20%; text-align: center;">Página <span class="page-number"></span></td>
                <td style="width: 40%; text-align: right;">Generado por: <?php echo htmlspecialchars($usuarioFull); ?> | <?php echo $fechaGeneracion; ?></td>
            </tr>
        </table>
    </footer>

    <!-- ========================================== -->
    <!-- SECCIÓN 1: RESUMEN EJECUTIVO (KPIs)       -->
    <!-- ========================================== -->
    <div class="section-title">Resumen Ejecutivo</div>
    
    <?php
    $etiqPeriodo = ($vista === 'anual') ? 'Años Registrados' : 'Trimestres Registrados';
    $totalPeriodos = $resumen['totalPeriodos'] ?? 0;
    $cumplidos = $resumen['cumplidos'] ?? 0;
    $pctCumplidos = $totalPeriodos > 0 ? ($cumplidos / $totalPeriodos) * 100 : 0;
    $sumaObj = $resumen['sumaObj'] ?? 0;
    $sumaAlc = $resumen['sumaAlc'] ?? 0;
    $promPct = $resumen['promPct'] ?? 0;
    ?>

    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                    <p>Períodos registrados</p>
                    <h3 style="color:#0d6efd"><?php echo $totalPeriodos; ?></h3>
                    <span><?php echo htmlspecialchars($etiqPeriodo); ?></span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 4px solid #198754;">
                    <p>Períodos Cumplidos</p>
                    <h3 style="color:#198754"><?php echo $cumplidos; ?></h3>
                    <span><?php echo number_format($pctCumplidos, 1); ?>% del total</span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 4px solid #0dcaf0;">
                    <p>Objetivo Acumulado</p>
                    <h3 style="color:#0dcaf0"><?php echo number_format($sumaObj, 0, ',', '.'); ?></h3>
                    <span>Metas institucionales</span>
                </div>
            </td>
            <td>
                <div class="kpi-card" style="border-top: 4px solid #fd7e14;">
                    <p>Promedio de Cumplimiento</p>
                    <h3 style="color:#fd7e14"><?php echo number_format($promPct, 1); ?>%</h3>
                    <span>Alcanzado: <?php echo number_format($sumaAlc, 0, ',', '.'); ?></span>
                </div>
            </td>
        </tr>
    </table>

    <!-- ========================================== -->
    <!-- SECCIÓN 2 & 3: GRÁFICO Y ANÁLISIS GENERAL  -->
    <!-- ========================================== -->
    
    <?php
    // Calcular Datos para Sección de Análisis (Sección 3)
    $maxObjetivoVal = -1; $maxObjetivoLabel = 'N/A';
    $maxAlcanzadoVal = -1; $maxAlcanzadoLabel = 'N/A';
    $maxPctVal = -1; $maxPctLabel = 'N/A';
    $minPctVal = 999999; $minPctLabel = 'N/A';

    foreach ($datos as $d) {
        $obj = $d['objetivo_total'];
        $alc = $d['alcanzado_total'];
        $pct = $d['pct'];
        $lbl = $d['label'];

        if ($obj > $maxObjetivoVal) {
            $maxObjetivoVal = $obj;
            $maxObjetivoLabel = $lbl;
        }
        if ($alc > $maxAlcanzadoVal) {
            $maxAlcanzadoVal = $alc;
            $maxAlcanzadoLabel = $lbl;
        }
        if ($pct > $maxPctVal) {
            $maxPctVal = $pct;
            $maxPctLabel = $lbl;
        }
        if ($pct < $minPctVal) {
            $minPctVal = $pct;
            $minPctLabel = $lbl;
        }
    }

    if ($minPctVal == 999999) {
        $minPctVal = 0;
    }

    $cumplimientoGlobal = $sumaObj > 0 ? ($sumaAlc / $sumaObj) * 100 : 0;
    
    // Calcular tendencia
    $tendencia = 'Estable';
    $variacion = 0;
    if (count($datos) > 1) {
        $primero = $datos[0];
        $ultimo = $datos[count($datos) - 1];
        $pctPrimero = $primero['pct'];
        $pctUltimo = $ultimo['pct'];
        $variacion = $pctUltimo - $pctPrimero;
        if ($variacion > 2) {
            $tendencia = 'Creciente';
        } elseif ($variacion < -2) {
            $tendencia = 'Decreciente';
        } else {
            $tendencia = 'Estable';
        }
    }
    ?>

    <div class="row-container">
        <table>
            <tr>
                <!-- Columna Gráfico -->
                <td class="col-48">
                    <div class="section-title" style="margin-top:0px;">Visualización Gráfica</div>
                    <div class="chart-box">
                        <?php if (!empty($graficoBase64)): ?>
                            <img src="<?php echo $graficoBase64; ?>" alt="Gráfico Histórico de Metas">
                        <?php else: ?>
                            <div style="height: 120px; line-height: 120px; color: #a0aec0; text-align: center;">Gráfico no disponible</div>
                        <?php endif; ?>
                        <div class="chart-legend">
                            Gráfica del tipo <strong><?php echo ($tipoGrafico === 'bar') ? 'Torres (Barra)' : 'Líneas'; ?></strong>.
                            Métricas activas: <strong><?php echo implode(', ', array_map('ucfirst', $metricasActivas)); ?></strong>.<br>
                            Vista: <strong><?php echo ($vista === 'anual') ? 'Anual' : 'Trimestral (' . $anio . ')'; ?></strong>.
                            <?php if (!empty($tipoMeta)): ?>
                                Filtro de meta: <strong><?php echo htmlspecialchars($tipoMeta); ?></strong>.
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                
                <td class="col-4"></td> <!-- Separador -->
                
                <!-- Columna Análisis -->
                <td class="col-48">
                    <div class="section-title" style="margin-top:0px;">Análisis de Desempeño</div>
                    <div class="analysis-box">
                        <table class="analysis-table">
                            <tr>
                                <td class="fw-bold" style="width: 55%;">Período con mayor objetivo:</td>
                                <td class="text-right"><?php echo htmlspecialchars($maxObjetivoLabel); ?> <span class="text-primary">(<?php echo number_format($maxObjetivoVal, 0, ',', '.'); ?>)</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Período con mayor alcanzado:</td>
                                <td class="text-right"><?php echo htmlspecialchars($maxAlcanzadoLabel); ?> <span class="text-success">(<?php echo number_format($maxAlcanzadoVal, 0, ',', '.'); ?>)</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Mejor porcentaje de cumplimiento:</td>
                                <td class="text-right"><?php echo htmlspecialchars($maxPctLabel); ?> <span class="text-success">(<?php echo number_format($maxPctVal, 1); ?>%)</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Menor porcentaje de cumplimiento:</td>
                                <td class="text-right"><?php echo htmlspecialchars($minPctLabel); ?> <span class="text-danger">(<?php echo number_format($minPctVal, 1); ?>%)</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cumplimiento Global Consolidado:</td>
                                <td class="text-right fw-bold <?php echo ($cumplimientoGlobal >= 100) ? 'text-success' : ($cumplimientoGlobal >= 60 ? 'text-warning' : 'text-danger'); ?>">
                                    <?php echo number_format($cumplimientoGlobal, 1); ?>%
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tendencia de la Serie:</td>
                                <td class="text-right fw-bold <?php echo ($tendencia === 'Creciente') ? 'text-success' : ($tendencia === 'Decreciente' ? 'text-danger' : 'text-primary'); ?>">
                                    <?php echo htmlspecialchars($tendencia); ?> (<?php echo ($variacion >= 0 ? '+' : '') . number_format($variacion, 1); ?>% var.)
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="margin-top: 10px; font-size: 8.5px; color: #718096; line-height: 1.3;">
                        * El análisis de tendencia compara el primer período registrado en la serie frente al último período de la visualización actual. Variaciones superiores al ±2% se catalogan como Crecientes o Decrecientes.
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Salto de página para la tabla y detalles -->
    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN 4: PANEL KPI ACTIVO (Si aplica)     -->
    <!-- ========================================== -->
    <?php if (!empty($panelActivo)): ?>
        <?php
        $datosPanel = [];
        $tituloPanel = '';
        $colorCabeceraPanel = '';
        if ($panelActivo === 'periodos') {
            $datosPanel = $datos;
            $tituloPanel = 'Detalle Completo: Todos los Períodos Registrados';
            $colorCabeceraPanel = '#0d6efd';
        } elseif ($panelActivo === 'cumplidos') {
            $datosPanel = array_values(array_filter($datos, function($d) { return $d['pct'] >= 100; }));
            $tituloPanel = 'Detalle Completo: Períodos con Cumplimiento al 100%';
            $colorCabeceraPanel = '#198754';
        } elseif ($panelActivo === 'objetivo') {
            $datosPanel = $datos;
            usort($datosPanel, function($a, $b) { return $b['objetivo_total'] <=> $a['objetivo_total']; });
            $tituloPanel = 'Detalle Completo: Ordenado por Mayor Objetivo';
            $colorCabeceraPanel = '#17a2b8';
        } elseif ($panelActivo === 'promedio') {
            $datosPanel = $datos;
            usort($datosPanel, function($a, $b) { return $b['pct'] <=> $a['pct']; });
            $tituloPanel = 'Detalle Completo: Ordenado por Mayor Porcentaje de Cumplimiento';
            $colorCabeceraPanel = '#fd7e14';
        }
        ?>

        <?php if (!empty($datosPanel)): ?>
            <div class="section-title" style="background-color: <?php echo $colorCabeceraPanel; ?>;"><?php echo htmlspecialchars($tituloPanel); ?></div>
            <table class="data-table">
                <thead>
                    <tr style="background-color: <?php echo $colorCabeceraPanel; ?>;">
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;">Período</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;" class="text-center">Metas</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;" class="text-right">Objetivo</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;" class="text-right">Alcanzado</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;">Progreso</th>
                        <th style="background-color: <?php echo $colorCabeceraPanel; ?>; color: #fff;" class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datosPanel as $d): ?>
                        <?php
                        $pct = $d['pct'];
                        $barColor = $pct >= 100 ? '#198754' : ($pct >= 60 ? '#f59e0b' : '#dc3545');
                        $badgeClass = $pct >= 100 ? 'bg-badge-success' : 'bg-badge-danger';
                        $badgeText = $pct >= 100 ? 'Cumplido' : 'No cumplido';
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($d['label']); ?></td>
                            <td class="text-center"><?php echo $d['total_metas']; ?></td>
                            <td class="text-right fw-bold"><?php echo number_format($d['objetivo_total'], 0, ',', '.'); ?></td>
                            <td class="text-right fw-bold" style="color: <?php echo $barColor; ?>;"><?php echo number_format($d['alcanzado_total'], 0, ',', '.'); ?></td>
                            <td style="width: 130px;">
                                <div class="progress-container">
                                    <div class="progress-bar" style="background-color: <?php echo $barColor; ?>; width: <?php echo min($pct, 100); ?>%;"></div>
                                </div>
                                <span style="font-family: monospace; font-size: 8px; font-weight: bold; color: <?php echo $barColor; ?>;"><?php echo number_format($pct, 1); ?>%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ========================================== -->
    <!-- SECCIÓN 5: TABLA COMPLETA (RESUMEN PERÍODO)-->
    <!-- ========================================== -->
    <div class="section-title">Resumen por Período (Datos de Vista)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Período</th>
                <th class="text-center">Metas</th>
                <th class="text-right">Objetivo</th>
                <th class="text-right">Alcanzado</th>
                <th>Progreso</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Cumplidas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $d): ?>
                <?php
                $pct = $d['pct'];
                $metasCumplidas = $d['metas_cumplidas'] ?? 0;
                $totalMetas = $d['total_metas'] ?? 1;

                if ($pct >= 100) {
                    $barColor = '#198754'; // Verde
                    $badgeClass = 'bg-badge-success';
                    $badgeText = 'Cumplido';
                } elseif ($pct >= 60) {
                    $barColor = '#f59e0b'; // Amarillo/Naranja
                    $badgeClass = 'bg-badge-warning';
                    $badgeText = 'En progreso';
                } elseif ($pct > 0) {
                    $barColor = '#dc3545'; // Rojo
                    $badgeClass = 'bg-badge-danger';
                    $badgeText = 'Insuficiente';
                } else {
                    $barColor = '#a0aec0'; // Gris
                    $badgeClass = 'bg-badge-secondary';
                    $badgeText = 'Sin iniciar';
                }
                ?>
                <tr>
                    <td class="fw-bold"><?php echo htmlspecialchars($d['label']); ?></td>
                    <td class="text-center"><?php echo $totalMetas; ?></td>
                    <td class="text-right fw-bold"><?php echo number_format($d['objetivo_total'], 0, ',', '.'); ?></td>
                    <td class="text-right fw-bold" style="color: <?php echo $barColor; ?>;"><?php echo number_format($d['alcanzado_total'], 0, ',', '.'); ?></td>
                    <td style="width: 140px;">
                        <div class="progress-container" style="width: 85px;">
                            <div class="progress-bar" style="background-color: <?php echo $barColor; ?>; width: <?php echo min($pct, 100); ?>%;"></div>
                        </div>
                        <span style="font-family: monospace; font-size: 8px; font-weight: bold; color: <?php echo $barColor; ?>;"><?php echo number_format($pct, 1); ?>%</span>
                    </td>
                    <td class="text-center">
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-badge-secondary"><?php echo $metasCumplidas; ?> / <?php echo $totalMetas; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ========================================== -->
    <!-- SECCIÓN 6: RANKINGS DE PERÍODOS           -->
    <!-- ========================================== -->
    <div class="section-title">Análisis de Posicionamiento (Top 5)</div>
    
    <?php
    // Generar los dos rankings
    $rankingCumplimiento = $datos;
    usort($rankingCumplimiento, function($a, $b) { return $b['pct'] <=> $a['pct']; });
    $rankingCumplimiento = array_slice($rankingCumplimiento, 0, 5);

    $rankingObjetivo = $datos;
    usort($rankingObjetivo, function($a, $b) { return $b['objetivo_total'] <=> $a['objetivo_total']; });
    $rankingObjetivo = array_slice($rankingObjetivo, 0, 5);
    ?>

    <div class="row-container">
        <table>
            <tr>
                <!-- Top 5 Cumplimiento -->
                <td class="col-48">
                    <table class="data-table">
                        <thead>
                            <tr style="background-color: #198754;">
                                <th style="background-color: #198754; color: #fff; width: 15%;" class="text-center">#</th>
                                <th style="background-color: #198754; color: #fff; width: 50%;">Período (Mejor Cumplimiento)</th>
                                <th style="background-color: #198754; color: #fff; width: 35%;" class="text-center">% Ejecución</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $pos = 1; foreach ($rankingCumplimiento as $rc): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?php echo $pos++; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($rc['label']); ?></td>
                                    <td class="text-center fw-bold text-success"><?php echo number_format($rc['pct'], 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>

                <td class="col-4"></td> <!-- Separador -->

                <!-- Top 5 Volumen Meta -->
                <td class="col-48">
                    <table class="data-table">
                        <thead>
                            <tr style="background-color: #0d6efd;">
                                <th style="background-color: #0d6efd; color: #fff; width: 15%;" class="text-center">#</th>
                                <th style="background-color: #0d6efd; color: #fff; width: 50%;">Período (Mayor Meta Objetivo)</th>
                                <th style="background-color: #0d6efd; color: #fff; width: 35%;" class="text-right">Meta Objetivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $pos = 1; foreach ($rankingObjetivo as $ro): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?php echo $pos++; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($ro['label']); ?></td>
                                    <td class="text-right fw-bold text-primary"><?php echo number_format($ro['objetivo_total'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

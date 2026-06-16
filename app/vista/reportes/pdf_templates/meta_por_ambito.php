<?php
// Asegurar que las variables pasadas desde el controlador estén disponibles
// $anioTabla, $fechaGeneracion, $logoGobierno, $logoInces, etc.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Metas por Ámbito</title>
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
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #fd7e14; font-weight: bold; }
        .text-info { color: #0dcaf0; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
        .bg-gray { background-color: #f8f9fa; }

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

        /* Tarjetas de Resumen (Grid manual usando floats o tablas para DOMPDF) */
        .kpi-row { width: 100%; margin-bottom: 20px; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 10px 0; }
        .kpi-card { 
            background: #fff; 
            border: 1px solid #e9ecef; 
            border-radius: 6px; 
            padding: 15px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 22px; margin: 0 0 5px 0; color: #212529; }
        .kpi-card p { font-size: 10px; margin: 0; color: #6c757d; text-transform: uppercase; font-weight: bold; }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 10px; 
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 6px 8px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) { background-color: #f8f9fa; }

        /* Observaciones */
        .obs-box { 
            background: #e9ecef; 
            border-left: 4px solid #0d6efd; 
            padding: 10px 15px; 
            margin-bottom: 10px; 
            font-size: 11px;
            color: #495057;
        }

        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 350px; }
        
        /* Semáforo Íconos */
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 5px; margin-right: 5px; }
        .dot-red { background-color: #dc3545; }
        .dot-yellow { background-color: #fd7e14; }
        .dot-green { background-color: #198754; }
        .dot-blue { background-color: #0dcaf0; }

        /* Cover Page */
        .cover { padding-top: 100px; }
        .cover h1 { font-size: 32px; color: #dc3545; text-align: center; margin-bottom: 10px; }
        .cover h2 { font-size: 18px; color: #495057; text-align: center; margin-bottom: 50px; }
        .cover-details { width: 50%; margin: 0 auto; border-top: 2px solid #dc3545; padding-top: 20px; }
        .cover-details p { font-size: 14px; margin: 8px 0; display: block; }
        .cover-details strong { display: inline-block; width: 150px; }

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
                    <p>Metas por Ámbito</p>
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

    <!-- ========================================== -->
    <!-- PÁGINA 1: RESUMEN EJECUTIVO PARA DIRECCIÓN -->
    <!-- ========================================== -->
    <div class="cover">
        <h1>Resumen Ejecutivo Institucional</h1>
        <h2>Estado General de Metas - Año <?php echo $anioTabla; ?></h2>

        <div class="section-title" style="margin-top: 40px;">Impacto Institucional</div>
        <div class="kpi-row">
            <table>
                <tr>
                    <td>
                        <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                            <h3 style="color:#0d6efd"><?php echo number_format($totObj, 0, ',', '.'); ?></h3>
                            <p>Objetivo Institucional</p>
                        </div>
                    </td>
                    <td>
                        <div class="kpi-card" style="border-top: 4px solid #198754;">
                            <h3 style="color:#198754"><?php echo number_format($totForm, 0, ',', '.'); ?></h3>
                            <p>Total Formados</p>
                        </div>
                    </td>
                    <td>
                        <div class="kpi-card" style="border-top: 4px solid #fd7e14;">
                            <h3 style="color:#fd7e14"><?php echo number_format($cumplimientoGlobal, 1); ?>%</h3>
                            <p>Cobertura / Cumplimiento</p>
                        </div>
                    </td>
                    <td>
                        <div class="kpi-card" style="border-top: 4px solid #dc3545;">
                            <h3 style="color:#dc3545"><?php echo number_format($brechaGlobal, 0, ',', '.'); ?></h3>
                            <p>Brecha Institucional</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Destacados y Alertas</div>
        <table class="data-table">
            <tr>
                <th style="width: 30%; text-align:left;">Mejor Ámbito (Ejecución):</th>
                <td style="width: 70%;">
                    <?php if ($mejorAmbito): ?>
                        <strong><?php echo htmlspecialchars($mejorAmbito['nombre']); ?></strong> 
                        (<?php echo number_format($mejorAmbito['pct'], 1); ?>% Cumplimiento / <?php echo number_format($mejorAmbito['alcanzado'], 0, ',', '.'); ?> Formados)
                    <?php else: ?>N/A<?php endif; ?>
                </td>
            </tr>
            <tr>
                <th style="text-align:left;">Mayor Riesgo (Brecha):</th>
                <td>
                    <?php if ($peorAmbitoBrecha): ?>
                        <strong><?php echo htmlspecialchars($peorAmbitoBrecha['nombre']); ?></strong> 
                        (Faltan <?php echo number_format($peorAmbitoBrecha['brecha'], 0, ',', '.'); ?> participantes para alcanzar el objetivo)
                    <?php else: ?>N/A<?php endif; ?>
                </td>
            </tr>
            <tr>
                <th style="text-align:left;">Estado de Metas (Semaforización):</th>
                <td>
                    <span class="dot dot-red"></span> <?php echo count($alertas['critica']); ?> Críticas |
                    <span class="dot dot-yellow"></span> <?php echo count($alertas['atencion']); ?> En Atención |
                    <span class="dot dot-blue"></span> <?php echo count($alertas['aceptable']); ?> Aceptables |
                    <span class="dot dot-green"></span> <?php echo count($alertas['cumplida']); ?> Cumplidas
                </td>
            </tr>
        </table>
        
        <div class="section-title">Observaciones Clave</div>
        <?php foreach ($observaciones as $obs): ?>
            <div class="obs-box"><?php echo htmlspecialchars($obs); ?></div>
        <?php endforeach; ?>
    </div>

    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN: ALERTAS DE GESTIÓN Y SEMÁFORO     -->
    <!-- ========================================== -->
    <div class="section-title">Alertas de Gestión (Semaforización)</div>
    <p style="font-size: 11px;">Clasificación de todas las metas de acuerdo a su nivel de ejecución frente a la meta objetivo.</p>

    <?php
    $tiposAlerta = [
        ['key' => 'critica', 'titulo' => 'Riesgo Crítico (< 50%)', 'colorClase' => 'text-danger', 'dot' => 'dot-red'],
        ['key' => 'atencion', 'titulo' => 'En Atención (50% - 79%)', 'colorClase' => 'text-warning', 'dot' => 'dot-yellow'],
        ['key' => 'aceptable', 'titulo' => 'Nivel Aceptable (80% - 99%)', 'colorClase' => 'text-info', 'dot' => 'dot-blue'],
        ['key' => 'cumplida', 'titulo' => 'Metas Cumplidas (>= 100%)', 'colorClase' => 'text-success', 'dot' => 'dot-green']
    ];
    ?>

    <?php foreach ($tiposAlerta as $tipo): ?>
        <h3 style="font-size: 13px; margin: 15px 0 5px 0;"><span class="dot <?php echo $tipo['dot']; ?>"></span> <?php echo $tipo['titulo']; ?> (<?php echo count($alertas[$tipo['key']]); ?>)</h3>
        <?php if (count($alertas[$tipo['key']]) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Ámbito</th>
                        <th style="width: 35%;">Tipo Objetivo</th>
                        <th style="width: 15%;">Objetivo</th>
                        <th style="width: 15%;">Alcanzado</th>
                        <th style="width: 10%;">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Limitar a las primeras 10 por categoría para no hacer el reporte infinito, o mostrarlas todas si no son muchas
                    $mostradas = 0;
                    foreach ($alertas[$tipo['key']] as $m): 
                        if ($mostradas++ >= 20) break;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m['nombre_ambito']); ?></td>
                            <td><?php echo htmlspecialchars($m['tipo_objetivo']); ?></td>
                            <td class="text-right"><?php echo number_format($m['meta_objetivo'], 0, ',', '.'); ?></td>
                            <td class="text-right"><?php echo number_format($m['estudiantes_formados'], 0, ',', '.'); ?></td>
                            <td class="text-center <?php echo $tipo['colorClase']; ?>"><?php echo number_format($m['pct'], 1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (count($alertas[$tipo['key']]) > 20): ?>
                <p style="font-size: 9px; color: #777; margin-top: -10px;">Mostrando las 20 más relevantes de esta categoría.</p>
            <?php endif; ?>
        <?php else: ?>
            <p style="font-size: 10px; color: #777; font-style: italic;">No hay metas en esta categoría.</p>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN: RANKING Y DISTRIBUCIÓN POR ÁMBITO -->
    <!-- ========================================== -->
    <div class="section-title">Distribución y Ranking por Ámbito</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Ámbito</th>
                <th style="width: 15%;">Metas Asignadas</th>
                <th style="width: 15%;">Meta Objetivo</th>
                <th style="width: 15%;">Impacto (Formados)</th>
                <th style="width: 20%;">Cumplimiento (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $pos = 1;
            foreach ($top10Ambitos as $amb): 
                $pct = $amb['pct'];
                $color = $pct >= 100 ? 'text-success' : ($pct >= 80 ? 'text-info' : ($pct >= 50 ? 'text-warning' : 'text-danger'));
            ?>
            <tr>
                <td class="text-center"><?php echo $pos++; ?></td>
                <td><strong><?php echo htmlspecialchars($amb['nombre']); ?></strong></td>
                <td class="text-center"><?php echo $amb['metas_count']; ?></td>
                <td class="text-right"><?php echo number_format($amb['objetivo'], 0, ',', '.'); ?></td>
                <td class="text-right" style="font-weight: bold;"><?php echo number_format($amb['alcanzado'], 0, ',', '.'); ?></td>
                <td class="text-center <?php echo $color; ?>"><?php echo number_format($pct, 1); ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 30px;">Análisis de Brechas (Riesgos de Ejecución)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35%;">Ámbito</th>
                <th style="width: 20%;">Objetivo Total</th>
                <th style="width: 20%;">Alcanzado</th>
                <th style="width: 25%;">Brecha (Pendientes)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $mostradosBrecha = 0;
            foreach ($ambitosBrecha as $amb): 
                if ($amb['brecha'] <= 0) continue; // Solo mostrar los que tienen brecha (pendiente)
                if ($mostradosBrecha++ >= 10) break;
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($amb['nombre']); ?></strong></td>
                <td class="text-right"><?php echo number_format($amb['objetivo'], 0, ',', '.'); ?></td>
                <td class="text-right"><?php echo number_format($amb['alcanzado'], 0, ',', '.'); ?></td>
                <td class="text-right text-danger">-<?php echo number_format($amb['brecha'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($mostradosBrecha === 0): ?>
            <tr><td colspan="4" class="text-center">No se registran brechas (todos los ámbitos han superado sus objetivos).</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN: TENDENCIA HISTÓRICA Y GRÁFICO     -->
    <!-- ========================================== -->
    <div class="section-title">Análisis de Tendencia y Gráfico Principal</div>
    
    <?php if (!empty($chartBase64)): ?>
        <div class="chart-box">
            <img src="<?php echo $chartBase64; ?>" alt="Gráfico de Metas">
        </div>
    <?php endif; ?>

    <?php if (count($historialOrdenado) > 1): ?>
        <h3 style="font-size: 13px; margin: 15px 0 5px 0;">Tendencia Histórica Consolidada</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Año Base</th>
                    <th>Objetivo Institucional</th>
                    <th>Participantes Formados</th>
                    <th>Cumplimiento Global</th>
                    <th>Variación vs Año Anterior</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historialOrdenado as $h): ?>
                <tr>
                    <td class="text-center" style="font-weight: bold;"><?php echo $h['anio']; ?></td>
                    <td class="text-right"><?php echo number_format($h['totalObj'], 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($h['totalForm'], 0, ',', '.'); ?></td>
                    <td class="text-center"><?php echo number_format($h['cumplimientoGlobal'], 1); ?>%</td>
                    <td class="text-center">
                        <?php 
                        if ($h['variacion'] === null) {
                            echo '-';
                        } else {
                            $varStr = number_format($h['variacion'], 1) . '%';
                            if ($h['variacion'] > 0) {
                                echo '<span class="text-success">▲ +' . $varStr . '</span>';
                            } elseif ($h['variacion'] < 0) {
                                echo '<span class="text-danger">▼ ' . $varStr . '</span>';
                            } else {
                                echo '<span>= 0%</span>';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="font-size: 10px; color: #777; font-style: italic;">No hay suficientes años seleccionados en el gráfico para calcular la tendencia histórica.</p>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- ========================================== -->
    <!-- SECCIÓN: DETALLE COMPLETO DE METAS         -->
    <!-- ========================================== -->
    <div class="section-title">Detalle Completo (Anexo Técnico - Año <?php echo $anioTabla; ?>)</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Ámbito</th>
                <th style="width: 15%;">Tipo</th>
                <th style="width: 12%;">Objetivo</th>
                <th style="width: 12%;">Formados</th>
                <th style="width: 10%;">%</th>
                <th style="width: 15%;">Brecha</th>
                <th style="width: 16%;">Período</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($metas)): ?>
                <tr><td colspan="7" class="text-center">No hay metas para este año.</td></tr>
            <?php else: ?>
                <?php foreach ($metas as $m): 
                    $brecha = $m['meta_objetivo'] - $m['estudiantes_formados'];
                    $brechaText = $brecha > 0 ? "-".number_format($brecha, 0, ',', '.') : "+".number_format(abs($brecha), 0, ',', '.');
                    $brechaClass = $brecha > 0 ? "text-danger" : "text-success";
                    
                    $dotClass = 'dot-red';
                    $colorTxt = 'text-danger';
                    if ($m['clasificacion'] === 'cumplida') { $dotClass = 'dot-green'; $colorTxt = 'text-success'; }
                    elseif ($m['clasificacion'] === 'aceptable') { $dotClass = 'dot-blue'; $colorTxt = 'text-info'; }
                    elseif ($m['clasificacion'] === 'atencion') { $dotClass = 'dot-yellow'; $colorTxt = 'text-warning'; }
                ?>
                <tr>
                    <td><span class="dot <?php echo $dotClass; ?>"></span> <?php echo htmlspecialchars($m['nombre_ambito']); ?></td>
                    <td><?php echo htmlspecialchars($m['tipo_objetivo']); ?></td>
                    <td class="text-right"><?php echo number_format($m['meta_objetivo'], 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($m['estudiantes_formados'], 0, ',', '.'); ?></td>
                    <td class="text-center <?php echo $colorTxt; ?>"><?php echo number_format($m['pct'], 1); ?>%</td>
                    <td class="text-right <?php echo $brechaClass; ?>"><?php echo $brechaText; ?></td>
                    <td class="text-center" style="font-size:8px;"><?php echo htmlspecialchars($m['periodo_tiempo']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

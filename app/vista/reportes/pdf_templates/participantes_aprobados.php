<?php
// Variables disponibles:
// $anioTabla, $periodoTxt, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $resumen, $panelData, $tablaMensual, $cursosSel, $chartBase64, $chartCursosBase64
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Participantes Aprobados</title>
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
        .text-success { color: #198754; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #ffc107; font-weight: bold; }
        .text-muted { color: #6c757d; }
        
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
        .kpi-row { width: 100%; margin-bottom: 20px; }
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
            padding: 6px 8px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        table.data-table tfoot tr td, .row-total td { background-color: #f1f5f9 !important; font-weight: bold; border-top: 2px solid #94a3b8; }

        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 280px; }
        
        /* Ficha Curso */
        .ficha-curso { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .ficha-curso h4 { margin: 0 0 10px 0; color: #0d6efd; font-size: 14px; }
        .ficha-table { width: 100%; font-size: 11px; }
        .ficha-table td { padding: 4px 0; color: #495057; }
        .ficha-table td strong { color: #212529; }
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
                    <p>Participantes Aprobados</p>
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

    <!-- RESUMEN EJECUTIVO -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Rendimiento de Participantes</h2>
        <p style="color: #6c757d; font-size: 13px;">Año: <?php echo $anioTabla; ?> &nbsp;|&nbsp; Período: <?php echo $periodoTxt; ?></p>
    </div>

    <!-- KPIs -->
    <div class="section-title">KPIs de Resumen</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo isset($resumen['evaluados']) ? number_format($resumen['evaluados'], 0, '', '.') : '0'; ?></h3>
                        <p>Evaluados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo isset($resumen['aprobados']) ? number_format($resumen['aprobados'], 0, '', '.') : '0'; ?></h3>
                        <p>Aprobados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #dc3545;">
                        <h3 style="color:#dc3545"><?php echo isset($resumen['reprobados']) ? number_format($resumen['reprobados'], 0, '', '.') : '0'; ?></h3>
                        <p>Reprobados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #ffc107;">
                        <h3 style="color:#ffc107"><?php echo isset($resumen['tasa']) ? $resumen['tasa'] . '%' : '0%'; ?></h3>
                        <p>Tasa Global</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Panel de Detalle Mensual (Si existe panelData) -->
    <?php if (!empty($panelData) && !empty($panelData['tipo'])): ?>
        <?php 
            $titulosTipo = [
                'evaluados' => 'Evaluados por Mes',
                'aprobados' => 'Aprobados por Mes',
                'reprobados' => 'Reprobados por Mes',
                'tasa' => 'Tasa de Aprobación (%)'
            ];
            $tituloPanel = $titulosTipo[$panelData['tipo']] ?? 'Detalle Mensual';
            $meses = $panelData['meses'] ?? [];
            $anios = $panelData['anios'] ?? [];
            $valores = $panelData['valores'] ?? [];
        ?>
        <?php if (!empty($anios) && !empty($meses)): ?>
            <div class="section-title"><?php echo $tituloPanel; ?></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Mes</th>
                        <?php foreach ($anios as $a): ?>
                            <th><?php echo $a; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalesPorAnio = array_fill_keys($anios, 0);
                    for ($i = 0; $i < 12; $i++): 
                    ?>
                        <tr>
                            <td class="text-center"><strong><?php echo $meses[$i]; ?></strong></td>
                            <?php foreach ($anios as $a): 
                                $val = isset($valores[$a][$i]) ? $valores[$a][$i] : null;
                                $txtVal = ($val !== null) ? $val : '-';
                                if ($val !== null && $panelData['tipo'] !== 'tasa') {
                                    $totalesPorAnio[$a] += floatval($val);
                                }
                            ?>
                                <td class="text-center"><?php echo $txtVal; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td class="text-center">Total</td>
                        <?php foreach ($anios as $a): 
                            $valTot = $panelData['tipo'] !== 'tasa' ? number_format($totalesPorAnio[$a], 0, '', '.') : '-';
                        ?>
                            <td class="text-center"><?php echo $valTot; ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Gráfico Principal -->
    <?php if (!empty($chartBase64)): ?>
        <div class="page-break"></div>
        <div class="section-title">Gráfico: Participantes Aprobados</div>
        <div class="chart-box">
            <img src="<?php echo $chartBase64; ?>" alt="Gráfico">
        </div>
    <?php endif; ?>

    <!-- Tabla Mensual -->
    <div class="section-title">Registro Mensual — <?php echo $anioTabla; ?></div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Evaluados</th>
                <th>Aprobados</th>
                <th>Reprobados</th>
                <th>Tasa Aprobación</th>
                <th>Variación Mensual</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totE = 0; $totA = 0; $totR = 0;
            $antAprobados = null;
            
            if (!empty($tablaMensual)):
                foreach ($tablaMensual as $mes):
                    $sd = !isset($mes['aprobados']) || $mes['aprobados'] === null;
                    
                    if (!$sd) {
                        $totA += $mes['aprobados'];
                        $totE += $mes['evaluados'];
                        $totR += $mes['reprobados'];
                    }
                    
                    $crec = '-';
                    $crecClass = '';
                    
                    if (!$sd) {
                        if ($antAprobados !== null) {
                            if ($antAprobados > 0) {
                                $diff = $mes['aprobados'] - $antAprobados;
                                $pct = number_format(($diff / $antAprobados) * 100, 1, '.', '');
                                if ($diff > 0) {
                                    $crec = "+$diff (+$pct%)";
                                    $crecClass = 'text-success';
                                } elseif ($diff < 0) {
                                    $crec = "$diff ($pct%)";
                                    $crecClass = 'text-danger';
                                } else {
                                    $crec = "0 (0%)";
                                    $crecClass = 'text-muted';
                                }
                            } else {
                                $crec = "Sin ref.";
                                $crecClass = 'text-muted';
                            }
                        } else {
                            $crec = "Primer mes";
                            $crecClass = 'text-muted';
                        }
                        $antAprobados = $mes['aprobados'];
                    }
                    
                    $txtE = $sd ? '-' : number_format($mes['evaluados'], 0, '', '.');
                    $txtA = $sd ? '-' : number_format($mes['aprobados'], 0, '', '.');
                    $txtR = $sd ? '-' : number_format($mes['reprobados'], 0, '', '.');
                    
                    $tasa = $mes['tasaAprobacion'] ?? 0;
                    $tasaClass = '';
                    if (!$sd) {
                        if ($tasa >= 80) $tasaClass = 'text-success';
                        elseif ($tasa >= 60) $tasaClass = 'text-warning';
                        else $tasaClass = 'text-danger';
                    }
                    $txtTasa = $sd ? '-' : $tasa . '%';
            ?>
                <tr>
                    <td class="text-center"><strong><?php echo htmlspecialchars($mes['mes'] ?? ''); ?></strong></td>
                    <td class="text-center"><?php echo $txtE; ?></td>
                    <td class="text-center"><?php echo $txtA; ?></td>
                    <td class="text-center"><?php echo $txtR; ?></td>
                    <td class="text-center <?php echo $tasaClass; ?>"><?php echo $txtTasa; ?></td>
                    <td class="text-center <?php echo $crecClass; ?>"><?php echo $crec; ?></td>
                </tr>
            <?php 
                endforeach; 
            endif;
            
            $tasaTotal = $totE > 0 ? number_format(($totA / $totE * 100), 1, '.', '') . '%' : '-';
            ?>
        </tbody>
        <tfoot>
            <tr class="row-total">
                <td class="text-center">Total</td>
                <td class="text-center"><?php echo number_format($totE, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totA, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totR, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo $tasaTotal; ?></td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <!-- Análisis por Curso -->
    <?php if (!empty($cursosSel)): ?>
        <div class="page-break"></div>
        <div class="section-title">Análisis por Curso (<?php echo count($cursosSel); ?> seleccionados)</div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40%; text-align: left;">Nombre del Curso</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Participantes</th>
                    <th>F. Inicio</th>
                    <th>F. Fin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursosSel as $c): 
                    $fi = !empty($c['fecha_inicio']) ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-';
                    $ff = !empty($c['fecha_fin']) ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-';
                    $nombre = htmlspecialchars($c['nombre_curso'] ?? '');
                ?>
                <tr>
                    <td><?php echo $nombre; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($c['modalidad'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($c['estatus'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo number_format($c['total_participantes'] ?? 0, 0, '', '.'); ?></td>
                    <td class="text-center"><?php echo $fi; ?></td>
                    <td class="text-center"><?php echo $ff; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (count($cursosSel) === 1): $c = $cursosSel[0]; ?>
            <div class="ficha-curso">
                <h4>Detalle del Curso Seleccionado</h4>
                <table class="ficha-table">
                    <tr>
                        <td style="width: 50%;"><strong>Nombre:</strong> <?php echo htmlspecialchars($c['nombre_curso'] ?? ''); ?></td>
                        <td style="width: 50%; text-align: right;"><strong style="color: #0d6efd; font-size: 14px;">Total: <?php echo number_format($c['total_participantes'] ?? 0, 0, '', '.'); ?> participantes</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Modalidad:</strong> <?php echo htmlspecialchars($c['modalidad'] ?? '-'); ?></td>
                        <td><strong>Fechas:</strong> <?php echo !empty($c['fecha_inicio']) ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-'; ?> a <?php echo !empty($c['fecha_fin']) ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Estado:</strong> <?php echo htmlspecialchars($c['estatus'] ?? '-'); ?></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        <?php elseif (!empty($chartCursosBase64)): ?>
            <div class="section-title">Comparativa de Participantes por Curso</div>
            <div class="chart-box">
                <img src="<?php echo $chartCursosBase64; ?>" alt="Comparativa Cursos" style="max-height: 350px;">
            </div>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>

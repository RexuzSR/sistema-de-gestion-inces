<?php
// Variables disponibles:
// $anio, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $cursos, $p75, $p40, $totInscr, $avgScore, $avgPartic, $n
// $chartImageBase64

function getPrioridad($score, $p75, $p40) {
    if ($score >= $p75) return ['label' => 'ALTA',  'flag' => 'A'];
    if ($score >= $p40) return ['label' => 'MEDIA', 'flag' => 'M'];
    return                    ['label' => 'BAJA',   'flag' => 'B'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Cursos más Requeridos</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 - Landscape */
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

        /* Top 3 Tarjetas */
        .top-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 15px 0; margin-bottom: 20px;}
        .top-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; text-align: center; border-top: 4px solid #0d6efd;}
        .top-card.rank-1 { border-top-color: #ffc107; background: #fffdf5; }
        .top-card.rank-2 { border-top-color: #adb5bd; }
        .top-card.rank-3 { border-top-color: #fd7e14; }
        .top-card h2 { font-size: 24px; margin: 0 0 5px 0; color: #0d6efd;}
        .top-card.rank-1 h2 { color: #ffc107;}
        .top-card.rank-2 h2 { color: #adb5bd;}
        .top-card.rank-3 h2 { color: #fd7e14;}
        .top-card h4 { font-size: 13px; margin: 0 0 10px 0; color: #343a40; height: 32px; overflow: hidden; }
        .top-card .score { font-size: 20px; font-weight: bold; color: #0d6efd; margin-bottom: 5px;}
        .top-card .badge-prioridad { display: inline-block; padding: 4px 8px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 9px; font-weight: bold; margin-bottom: 10px;}
        .top-card .details { font-size: 10px; color: #6c757d; text-align: left; background: #fff; padding: 8px; border-radius: 4px; border: 1px solid #e9ecef;}
        
        /* Estadísticas */
        .kpi-row { width: 100%; margin-bottom: 20px; text-align: center; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 10px 0; }
        .kpi-card { 
            background: #f4f6f9; 
            border: 1px solid #e9ecef; 
            border-radius: 6px; 
            padding: 10px 5px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 20px; margin: 0 0 5px 0; color: #212529; }
        .kpi-card p { font-size: 10px; margin: 0; color: #495057; font-weight: bold; }
        .kpi-card span { font-size: 8px; color: #6c757d;}

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
        table.data-table tbody tr.top-row-table td { background-color: #fffdf5; }
        
        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 280px; }

        .methodology-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin-top: 10px; }
        .methodology-box h4 { margin: 0 0 10px 0; color: #343a40;}
        .methodology-box p { margin: 0 0 8px 0; font-size: 11px;}
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
                    <p>Cursos más Requeridos</p>
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
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Análisis de Requerimientos Formativos</h2>
        <p style="color: #6c757d; font-size: 13px;">Año Seleccionado: <?php echo $anio; ?></p>
    </div>

    <!-- Top 3 Cursos -->
    <div class="section-title">TOP 3 — Cursos más Requeridos</div>
    <div class="top-row">
        <table>
            <tr>
                <?php 
                $top3 = array_slice($cursos, 0, 3);
                for ($i = 0; $i < min(3, count($top3)); $i++): 
                    $c = $top3[$i]; 
                    $pr = getPrioridad($c['score'], $p75, $p40);
                    $tasa = $c['inscripciones'] > 0 ? round(($c['participantes'] / $c['inscripciones']) * 100) . '%' : '—';
                ?>
                <td>
                    <div class="top-card rank-<?php echo ($i+1); ?>">
                        <h2><?php echo ($i+1); ?>&deg;</h2>
                        <h4><?php echo htmlspecialchars($c['curso']); ?></h4>
                        <div class="score"><?php echo $c['score']; ?> / 100</div>
                        <div class="badge-prioridad"><?php echo $pr['label']; ?> PRIORIDAD</div>
                        <div class="details">
                            <table style="width: 100%; border:none;">
                                <tr>
                                    <td style="border:none; padding: 2px;">Inscritos: <?php echo number_format($c['participantes'], 0, '', '.'); ?></td>
                                    <td style="border:none; padding: 2px;">Inscripciones: <?php echo number_format($c['inscripciones'], 0, '', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:none; padding: 2px;">Tasa llenado: <?php echo $tasa; ?></td>
                                    <td style="border:none; padding: 2px;">Área: <?php echo htmlspecialchars($c['area']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <?php endfor; ?>
            </tr>
        </table>
    </div>

    <!-- KPIs -->
    <div class="section-title">Estadísticas de Requerimiento</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #6c757d;">
                        <h3><?php echo $n; ?></h3>
                        <p>Cursos Analizados</p>
                        <span>En el año seleccionado</span>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3><?php echo $avgScore; ?></h3>
                        <p>Puntuación Promedio</p>
                        <span>Índice de requerimiento</span>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3><?php echo number_format($totInscr, 0, '', '.'); ?></h3>
                        <p>Total Inscripciones</p>
                        <span>Suma de todos los cursos</span>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0dcaf0;">
                        <h3><?php echo number_format($avgPartic, 0, '', '.'); ?></h3>
                        <p>Participantes Prom.</p>
                        <span>Únicos por curso</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <?php if (!empty($chartImageBase64)): ?>
        <div class="page-break"></div>
        <div class="section-title">Índice de Requerimiento por Área Formativa</div>
        <div class="chart-box">
            <img src="<?php echo $chartImageBase64; ?>" alt="Gráfico de Áreas Formativas" style="max-height: 400px;">
        </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- Tabla Detallada -->
    <div class="section-title">Ranking Completo de Cursos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 30%; text-align:left;">Curso</th>
                <th style="width: 10%;">Inscripciones</th>
                <th style="width: 10%;">Participantes</th>
                <th style="width: 10%;">Score (%)</th>
                <th style="width: 16%;">Área</th>
                <th style="width: 10%;">Estatus</th>
                <th style="width: 10%;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($cursos as $gi => $row): 
                $accion = $row['score'] > 75 ? 'Reabrir pronto' : ($row['score'] > 50 ? 'Considerar' : 'Mantener');
                $classTr = ($gi < 3) ? 'top-row-table' : '';
            ?>
                <tr class="<?php echo $classTr; ?>">
                    <td class="text-center"><?php echo $gi + 1; ?></td>
                    <td><?php echo htmlspecialchars($row['curso']); ?></td>
                    <td class="text-center"><?php echo number_format($row['inscripciones'], 0, '', '.'); ?></td>
                    <td class="text-center"><?php echo number_format($row['participantes'], 0, '', '.'); ?></td>
                    <td class="text-center"><strong><?php echo $row['score']; ?> / 100</strong></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['area']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['estatus']); ?></td>
                    <td class="text-center"><?php echo $accion; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Metodología -->
    <div class="section-title">Metodología del Índice de Requerimiento</div>
    <div class="methodology-box">
        <h4>Fórmula del Score:</h4>
        <p><i>Score = (Inscripciones / Max) x 30  +  (Participantes unicos / Max) x 40  +  (Participantes / Inscripciones) x 30</i></p>
        
        <h4 style="margin-top: 15px;">Umbrales de Prioridad calculados para este año:</h4>
        <p><strong>Alta Prioridad</strong> &mdash; Score >= P75 (>= <?php echo $p75; ?>)</p>
        <p><strong>Media Prioridad</strong> &mdash; Score >= P40 (>= <?php echo $p40; ?>)</p>
        <p><strong>Baja Prioridad</strong> &mdash; Score < P40 (< <?php echo $p40; ?>)</p>
    </div>

</body>
</html>

<?php
// Variables disponibles:
// $anio, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $totNuevos, $activosUltimoMes, $totCulm, $tasaCompletitud
// $datosMeses (array con: mes, nuevos, activos, culminados, enEspera, totalAcumulado)
// $totActivos, $totEspera, $acumulado
// $chartImageBase64
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Total de Cursos</title>
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
            font-size: 11px; 
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 8px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        table.data-table tfoot tr td { background-color: #f1f5f9 !important; font-weight: bold; border-top: 2px solid #94a3b8; }

        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 280px; }
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
                    <p>Total de Cursos Creados</p>
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
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Métricas de Resumen de la Oferta Formativa</h2>
        <p style="color: #6c757d; font-size: 13px;">Año Seleccionado: <?php echo $anio; ?></p>
    </div>

    <!-- KPIs -->
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totNuevos, 0, '', '.'); ?></h3>
                        <p>Nuevos Cursos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo number_format($activosUltimoMes, 0, '', '.'); ?></h3>
                        <p>Cursos Activos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #6f42c1;">
                        <h3 style="color:#6f42c1"><?php echo number_format($totCulm, 0, '', '.'); ?></h3>
                        <p>Cursos Culminados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #ffc107;">
                        <h3 style="color:#ffc107"><?php echo $tasaCompletitud; ?></h3>
                        <p>Tasa Completitud</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <?php if (!empty($chartImageBase64)): ?>
        <div class="section-title">Evolución Mensual</div>
        <div class="chart-box">
            <img src="<?php echo $chartImageBase64; ?>" alt="Gráfico de Evolución Mensual">
        </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- Tabla Detallada -->
    <div class="section-title">Registro Mensual</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align:left;">Mes</th>
                <th>Nuevos cursos</th>
                <th>Cursos activos</th>
                <th>Culminados</th>
                <th>En espera</th>
                <th>Total acumulado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datosMeses as $mes): 
                if ($mes['nuevos'] === null) {
                    $n = '-'; $a = '-'; $c = '-'; $e = '-'; $ac = '-';
                } else {
                    $n = number_format($mes['nuevos'], 0, '', '.');
                    $a = number_format($mes['activos'], 0, '', '.');
                    $c = number_format($mes['culminados'], 0, '', '.');
                    $e = number_format($mes['enEspera'], 0, '', '.');
                    $ac = number_format($mes['totalAcumulado'], 0, '', '.');
                }
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($mes['mes']); ?></strong></td>
                    <td class="text-center"><?php echo $n; ?></td>
                    <td class="text-center"><?php echo $a; ?></td>
                    <td class="text-center"><?php echo $c; ?></td>
                    <td class="text-center"><?php echo $e; ?></td>
                    <td class="text-center"><?php echo $ac; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-center"><?php echo number_format($totNuevos, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totActivos, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totCulm, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totEspera, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($acumulado, 0, '', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

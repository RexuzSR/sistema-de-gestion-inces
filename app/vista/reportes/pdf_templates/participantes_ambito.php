<?php
// Variables disponibles:
// $anio, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $totalAmbitos, $totalPart, $totalAprobados, $totalFormacion, $totalRetirados, $difStr, $diferencia
// $maxPart, $maxAprob, $maxTasa, $maxCrecimiento
// $tablaHtml, $rankingHtml, $interanualHtml, $seccionMensualHtml, $chartImageBase64
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Participantes por Ámbito</title>
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

        /* Resumen Ejecutivo */
        .summary-box { background: #f8f9fa; border: 1px solid #dee2e6; border-left: 4px solid #0d6efd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .summary-title { font-weight: bold; margin-bottom: 12px; font-size: 13px; color: #0d6efd; text-transform: uppercase; }
        .summary-item { margin-bottom: 6px; font-size: 11px; }
        .summary-item strong { display: inline-block; width: 200px; color: #495057; }
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
                    <p>Participantes por Ámbito</p>
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
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Distribución Territorial de Participantes</h2>
        <p style="color: #6c757d; font-size: 13px;">Año Seleccionado: <?php echo $anio; ?></p>
    </div>

    <!-- KPIs -->
    <div class="section-title">KPIs de Resumen</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #6c757d;">
                        <h3 style="color:#6c757d"><?php echo $totalAmbitos; ?></h3>
                        <p>Total Ámbitos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totalPart, 0, '', '.'); ?></h3>
                        <p>Participantes</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo number_format($totalAprobados, 0, '', '.'); ?></h3>
                        <p>Aprobados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0dcaf0;">
                        <h3 style="color:#0dcaf0"><?php echo number_format($totalFormacion, 0, '', '.'); ?></h3>
                        <p>En Formación</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #dc3545;">
                        <h3 style="color:#dc3545"><?php echo number_format($totalRetirados, 0, '', '.'); ?></h3>
                        <p>Retirados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid <?php echo $diferencia > 0 ? '#198754' : ($diferencia < 0 ? '#dc3545' : '#6c757d'); ?>;">
                        <h3 style="color:<?php echo $diferencia > 0 ? '#198754' : ($diferencia < 0 ? '#dc3545' : '#6c757d'); ?>"><?php echo $difStr; ?></h3>
                        <p>Variación Anual</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-title">Resumen Estratégico</div>
        <div class="summary-item"><strong>Ámbito con más participantes:</strong> <?php echo $maxPart['ambito']; ?> (<?php echo number_format($maxPart['val'], 0, '', '.'); ?>)</div>
        <div class="summary-item"><strong>Ámbito con más aprobados:</strong> <?php echo $maxAprob['ambito']; ?> (<?php echo number_format($maxAprob['val'], 0, '', '.'); ?>)</div>
        <div class="summary-item"><strong>Mejor tasa de aprobación:</strong> <?php echo $maxTasa['ambito']; ?> (<?php echo $maxTasa['val']; ?>%)</div>
        <div class="summary-item"><strong>Mayor crecimiento anual:</strong> <?php echo $maxCrecimiento['ambito']; ?> (<?php echo ($maxCrecimiento['val'] > 0 ? '+' : '') . number_format($maxCrecimiento['val'], 0, '', '.'); ?>)</div>
    </div>

    <?php if (!empty($chartImageBase64)): ?>
        <div class="chart-box">
            <img src="<?php echo $chartImageBase64; ?>" alt="Gráfico de Ámbitos">
        </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- PÁGINA 2: Tabla Detallada -->
    <div class="section-title">Detalle de Participantes por Ámbito</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align:left;">Ámbito</th>
                <th>Total</th>
                <th>Aprobados</th>
                <th>En Form.</th>
                <th>Retirados</th>
                <th>Año Anterior</th>
                <th>Diferencia</th>
                <th>Tasa Aprob.</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $tablaHtml; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- PÁGINA 3: Ranking y Comparación -->
    <div class="section-title">Ranking de Ámbitos por Participantes</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%">Posición</th>
                <th style="width: 55%; text-align:left;">Ámbito</th>
                <th style="width: 30%;">Total Participantes</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $rankingHtml; ?>
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 30px;">Comparación Interanual</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%; text-align:left;">Ámbito</th>
                <th style="width: 20%;">Total Año Actual</th>
                <th style="width: 20%;">Total Año Anterior</th>
                <th style="width: 20%;">Variación</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $interanualHtml; ?>
        </tbody>
    </table>

    <?php 
    if (!empty($seccionMensualHtml)) {
        // Adapt table inside seccionMensualHtml to use data-table class
        $seccionMensualHtml = str_replace('<table>', '<table class="data-table">', $seccionMensualHtml);
        echo $seccionMensualHtml;
    }
    ?>

</body>
</html>

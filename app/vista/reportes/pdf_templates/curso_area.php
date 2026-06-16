<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $datos (array con nombre_areaFormativa, cantidad, num_inscritos)
// $totalCursos
// Opcional: gráfico de pastel o barras si se envía
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Cursos por Área</title>
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
            border-bottom: 3px solid #ffc107; 
            padding-bottom: 10px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 55px; }
        .header-title { text-align: center; }
        .header-title h1 { margin: 0; font-size: 18px; color: #b48600; text-transform: uppercase; letter-spacing: 1px; }
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

        /* KPIs Globales */
        .kpi-row { width: 100%; margin-bottom: 20px; text-align: center; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 15px 0; }
        .kpi-card { 
            background: #f8f9fa; 
            border: 1px solid #e9ecef; 
            border-radius: 6px; 
            padding: 15px 5px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 24px; margin: 0 0 5px 0; color: #333; }
        .kpi-card p { font-size: 11px; margin: 0; color: #666; text-transform: uppercase; font-weight: bold; line-height: 1.1; }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 12px; 
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 12px 10px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) td { background-color: #f8f9fa; }
        table.data-table tbody tr.row-totales td {
            background-color: #e9ecef;
            font-weight: bold;
            color: #212529;
            text-align: center;
        }
        table.data-table tbody tr.row-totales td:first-child { text-align: left; }

        /* Barras de progreso en tabla */
        .prog-bar-container { width: 100%; background: #e9ecef; border-radius: 10px; height: 14px; margin-top: 5px; overflow: hidden;}
        .prog-bar-fill { height: 100%; border-radius: 10px; background-color: #0891b2; }
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
                    <p>Cursos por Área Formativa</p>
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

    <!-- RESUMEN GENERAL -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Distribución de la Oferta Formativa</h2>
        <?php if(!empty($filtrosText)): ?>
            <p style="color: #6c757d; font-size: 12px;"><?php echo htmlspecialchars($filtrosText); ?></p>
        <?php endif; ?>
    </div>

    <!-- KPIs Globales -->
    <?php 
    $totInscritos = array_sum(array_column($datos, 'num_inscritos'));
    ?>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0891b2;">
                        <h3 style="color:#0891b2"><?php echo count($datos); ?></h3>
                        <p>Áreas Analizadas</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo number_format($totalCursos, 0, '', '.'); ?></h3>
                        <p>Total Cursos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totInscritos, 0, '', '.'); ?></h3>
                        <p>Participantes Inscritos</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla General -->
    <div class="section-title">Detalle de Cursos por Área</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%; text-align:left;">Área Formativa</th>
                <th style="width: 15%;">Total Cursos</th>
                <th style="width: 15%;">Inscritos</th>
                <th style="width: 20%;">Peso del Área (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            foreach ($datos as $row): 
                $porcentaje = $totalCursos > 0 ? ($row['cantidad'] / $totalCursos) * 100 : 0;
            ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_areaFormativa']); ?></td>
                    <td class="text-center" style="font-weight:bold; font-size:14px;"><?php echo number_format($row['cantidad'], 0, '', '.'); ?></td>
                    <td class="text-center"><?php echo number_format($row['num_inscritos'], 0, '', '.'); ?></td>
                    <td class="text-center">
                        <strong><?php echo number_format($porcentaje, 2); ?>%</strong>
                        <div class="prog-bar-container">
                            <div class="prog-bar-fill" style="width: <?php echo min(100, $porcentaje); ?>%;"></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if(empty($datos)): ?>
                <tr><td colspan="5" class="text-center py-4">No hay información disponible.</td></tr>
            <?php else: ?>
                <tr class="row-totales">
                    <td colspan="2">TOTAL GENERAL</td>
                    <td style="font-size:14px;"><?php echo number_format($totalCursos, 0, '', '.'); ?></td>
                    <td><?php echo number_format($totInscritos, 0, '', '.'); ?></td>
                    <td>100%</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

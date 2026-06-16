<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $datos (array con nombre_curso, masculino, femenino)
// $totalMasculino, $totalFemenino, $totalGeneral
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Participantes por Género</title>
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
                    <p>Participantes por Género en Cursos</p>
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
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Distribución de Participantes por Género</h2>
        <p style="color: #6c757d; font-size: 13px;">(Participantes con estatus Pendiente)</p>
    </div>

    <!-- KPIs -->
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totalMasculino, 0, '', '.'); ?></h3>
                        <p>Total Masculino</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #d63384;">
                        <h3 style="color:#d63384"><?php echo number_format($totalFemenino, 0, '', '.'); ?></h3>
                        <p>Total Femenino</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo number_format($totalGeneral, 0, '', '.'); ?></h3>
                        <p>Total General</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla Detallada -->
    <div class="section-title">Detalle por Curso</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 55%; text-align:left;">Curso</th>
                <th style="width: 15%;">Masculino</th>
                <th style="width: 15%;">Femenino</th>
                <th style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $curso): 
                $tot = $curso['masculino'] + $curso['femenino'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($curso['nombre_curso']); ?></td>
                    <td class="text-center"><?php echo $curso['masculino']; ?></td>
                    <td class="text-center"><?php echo $curso['femenino']; ?></td>
                    <td class="text-center"><strong><?php echo $tot; ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-center">Totales</td>
                <td class="text-center"><?php echo number_format($totalMasculino, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totalFemenino, 0, '', '.'); ?></td>
                <td class="text-center"><?php echo number_format($totalGeneral, 0, '', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

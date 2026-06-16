<?php
// Variables disponibles:
// $anioBase, $anioTabla, $fechaGeneracion, $logoGobierno, $logoInces
// $totales, $totalActivos, $totalInactivos, $chartBase64
// $aniosGrafico, $dataAnios, $porMes
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Total de Docentes</title>
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
        .text-success { color: #198754; font-weight: bold; }
        
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

        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 250px; }
        
        /* Observaciones / Alertas */
        .alert-box { 
            background: #f8d7da; 
            border-left: 4px solid #dc3545; 
            padding: 10px 15px; 
            margin-bottom: 10px; 
            font-size: 11px;
            color: #842029;
        }
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
                    <p>Total de Docentes</p>
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
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Estado General del Personal Docente</h2>
        <p style="color: #6c757d; font-size: 13px;">Año Base Activo: <?php echo $anioBase; ?></p>
    </div>

    <div class="section-title">KPIs de Resumen</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totales['total'], 0, ',', '.'); ?></h3>
                        <p>Total Registrados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo number_format($totalActivos, 0, ',', '.'); ?></h3>
                        <p>Docentes Activos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #ffc107;">
                        <h3 style="color:#ffc107"><?php echo number_format($totales['asignado'], 0, ',', '.'); ?></h3>
                        <p>Docentes Asignados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #dc3545;">
                        <h3 style="color:#dc3545"><?php echo number_format($totalInactivos, 0, ',', '.'); ?></h3>
                        <p>Docentes Inactivos</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Distribución por Tipo, Género y Estado</div>
    <table style="width: 100%; border-spacing: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="data-table">
                    <thead>
                        <tr><th colspan="2">Por Tipo de Docente</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Fijo</td><td class="text-center"><?php echo $totales['fijo']; ?></td></tr>
                        <tr><td>Contratado</td><td class="text-center"><?php echo $totales['contratado']; ?></td></tr>
                        <tr><td>PDL</td><td class="text-center"><?php echo $totales['pdl']; ?></td></tr>
                        <tr><td>Voluntario</td><td class="text-center"><?php echo $totales['voluntario']; ?></td></tr>
                        <tr style="font-weight: bold; background-color: #f1f3f5;"><td>Total</td><td class="text-center"><?php echo $totales['total']; ?></td></tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="data-table">
                    <thead>
                        <tr><th colspan="2">Por Género</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Masculino</td><td class="text-center"><?php echo $totales['masculino']; ?></td></tr>
                        <tr><td>Femenino</td><td class="text-center"><?php echo $totales['femenino']; ?></td></tr>
                        <tr style="font-weight: bold; background-color: #f1f3f5;"><td>Total</td><td class="text-center"><?php echo intval($totales['masculino']) + intval($totales['femenino']); ?></td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%; text-align: left;">Estado de Actividad</th>
                <th style="width: 30%;">Cantidad</th>
                <th style="width: 30%;">% del Total Activo</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $estados = [
                'Disponible' => $totales['disponible'],
                'Asignado' => $totales['asignado'],
                'En formación' => $totales['en_formacion']
            ];
            foreach ($estados as $k => $v) {
                $pct = $totalActivos > 0 ? round(($v / $totalActivos) * 100) : 0;
                echo "<tr>
                        <td><strong>{$k}</strong></td>
                        <td class='text-center'>{$v}</td>
                        <td class='text-center'>{$pct}%</td>
                      </tr>";
            }
            ?>
            <tr style="font-weight: bold; background-color: #e9ecef;">
                <td>Total Activos</td>
                <td class="text-center"><?php echo $totalActivos; ?></td>
                <td class="text-center">100%</td>
            </tr>
        </tbody>
    </table>

    <?php if ($totalInactivos > 0): ?>
    <div class="alert-box">
        <strong>Atención:</strong> Existen <?php echo $totalInactivos; ?> docentes registrados con estado <em>Inactivo</em> en el sistema.
    </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- SECCIÓN GRÁFICO Y TENDENCIA MENSUAL -->
    <div class="section-title">Historial de Asignaciones</div>
    
    <?php if (!empty($chartBase64)): ?>
        <div class="chart-box">
            <img src="<?php echo $chartBase64; ?>" alt="Gráfico de Docentes">
        </div>
    <?php endif; ?>

    <?php 
    $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    if (count($aniosGrafico) <= 1): 
        $dataAsig = $dataAnios[$anioTabla];
        $acum = 0;
        $totalAsig = 0;
    ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Asignaciones del mes</th>
                <th>Total acumulado</th>
                <th>Variación mensual</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            for ($i = 1; $i <= 12; $i++): 
                $val = $dataAsig[$i];
                $acum += $val;
                $totalAsig += $val;
                
                $varStr = '—';
                $varClass = '';
                if ($i === 1) {
                    $varStr = 'Primer mes';
                } else {
                    $valAnt = $dataAsig[$i - 1];
                    if ($valAnt > 0) {
                        $diff = $val - $valAnt;
                        $pct = round(($diff / $valAnt) * 100, 1);
                        $sign = $diff > 0 ? '+' : '';
                        $varStr = "$sign$diff ($sign$pct%)";
                        $varClass = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : '');
                    } else if ($val > 0) {
                        $varStr = "+$val (N/A)";
                        $varClass = 'text-success';
                    }
                }
            ?>
            <tr>
                <td class="text-center"><?php echo $mesesNombres[$i - 1]; ?></td>
                <td class="text-center"><?php echo $val; ?></td>
                <td class="text-center"><?php echo $acum; ?></td>
                <td class="text-center <?php echo $varClass; ?>"><?php echo $varStr; ?></td>
            </tr>
            <?php endfor; ?>
            <tr style="font-weight: bold; background-color: #f1f3f5;">
                <td class="text-center">Total</td>
                <td class="text-center"><?php echo $totalAsig; ?></td>
                <td class="text-center">—</td>
                <td class="text-center">—</td>
            </tr>
        </tbody>
    </table>
    
    <?php else: ?>
    <!-- Tabla Comparativa Multi-año -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Mes</th>
                <?php foreach ($aniosGrafico as $a): ?>
                    <th><?php echo $a; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalesPorAnio = array_fill_keys($aniosGrafico, 0);
            for ($i = 1; $i <= 12; $i++): 
            ?>
            <tr>
                <td class="text-center"><strong><?php echo $mesesNombres[$i - 1]; ?></strong></td>
                <?php foreach ($aniosGrafico as $a): 
                    $val = $dataAnios[$a][$i];
                    $totalesPorAnio[$a] += $val;
                ?>
                    <td class="text-center"><?php echo $val; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endfor; ?>
            <tr style="font-weight: bold; background-color: #f1f3f5;">
                <td class="text-center">Total</td>
                <?php foreach ($aniosGrafico as $a): ?>
                    <td class="text-center"><?php echo $totalesPorAnio[$a]; ?></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- SECCIÓN DETALLE MENSUAL -->
    <div class="section-title">Anexo: Detalle de Docentes Asignados por Mes — <?php echo $anioTabla; ?></div>
    
    <?php 
    $mesesNombres2 = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    if (empty($porMes)): 
    ?>
        <p style="text-align: center; font-style: italic; color: #777; margin-top: 20px;">No se encontraron asignaciones de docentes en el año <?php echo $anioTabla; ?>.</p>
    <?php else: ?>
        
        <?php foreach ($porMes as $numMes => $asignaciones): ?>
            <h3 style="background-color: #fff3cd; color: #856404; padding: 5px 10px; font-size: 12px; margin-bottom: 5px;">
                Mes: <?php echo $mesesNombres2[$numMes - 1]; ?> (<?php echo count($asignaciones); ?> asignaciones)
            </h3>
            <table class="data-table" style="margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 30%;">Docente</th>
                        <th style="width: 15%;">Cédula</th>
                        <th style="width: 15%;">Tipo</th>
                        <th style="width: 20%;">Curso Asignado</th>
                        <th style="width: 10%;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asignaciones as $a): ?>
                    <tr>
                        <td class="text-center"><?php echo $a['id_docente']; ?></td>
                        <td><?php echo htmlspecialchars($a['nombre_completo']); ?></td>
                        <td class="text-center"><?php echo number_format($a['cedula'], 0, '', '.'); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($a['tipo_docente']); ?></td>
                        <td><?php echo htmlspecialchars($a['nombre_curso']); ?></td>
                        <td class="text-center"><?php echo date('d/m/Y', strtotime($a['fecha_asignacion'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

    <?php endif; ?>

</body>
</html>

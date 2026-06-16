<?php
// Variables disponibles:
// $anio, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $cursos (array con id_curso, nombre_curso, total_participantes, estatus, modalidad, fecha_inicio, fecha_fin, clasificacion, duracion_legible)
// $totalParticipantesGlobal, $promedio, $cantidadCursos, $cursoMasActivo
// $chartImageBase64
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial - Cursos con más Participantes</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 - Orientación Horizontal (Landscape) */
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

        /* Top 3 Tarjetas */
        .top-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 15px 0; margin-bottom: 20px;}
        .top-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; text-align: center; border-top: 4px solid #0d6efd;}
        .top-card.rank-1 { border-top-color: #ffc107; background: #fffdf5; }
        .top-card.rank-2 { border-top-color: #adb5bd; }
        .top-card.rank-3 { border-top-color: #fd7e14; }
        .top-card h4 { font-size: 14px; margin: 0 0 10px 0; color: #343a40; height: 35px; overflow: hidden; }
        .top-card .part-count { font-size: 16px; font-weight: bold; color: #0d6efd; margin-bottom: 5px;}
        .top-card.rank-1 .part-count { color: #d39e00; }
        .top-card .status { font-size: 10px; color: #6c757d; text-transform: uppercase;}

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
        
        /* Gráfico */
        .chart-box { text-align: center; margin: 20px 0; padding: 10px; border: 1px solid #dee2e6; background: #fff; border-radius: 5px; }
        .chart-box img { max-width: 100%; max-height: 280px; }
        
        .badge-muy-activo { background-color: #198754; color: white; padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold;}
        .badge-activo { background-color: #0dcaf0; color: white; padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold;}
        .badge-normal { background-color: #6c757d; color: white; padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold;}
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
                    <p>Cursos con Más Participantes</p>
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
        <h2 style="font-size: 20px; color: #343a40; margin-bottom: 5px;">Análisis de Demanda por Curso</h2>
        <p style="color: #6c757d; font-size: 13px;">Año Seleccionado: <?php echo $anio; ?></p>
    </div>

    <!-- Top 3 Cursos -->
    <div class="section-title">Clasificación Top 3</div>
    <div class="top-row">
        <table>
            <tr>
                <?php for ($i = 0; $i < min(3, count($cursos)); $i++): $curso = $cursos[$i]; ?>
                <td>
                    <div class="top-card rank-<?php echo ($i+1); ?>">
                        <h4><?php echo ($i+1) . '. ' . htmlspecialchars($curso['nombre_curso']); ?></h4>
                        <div class="part-count"><?php echo number_format($curso['total_participantes'], 0, '', '.'); ?> participantes</div>
                        <div class="status">Estado: <?php echo htmlspecialchars($curso['clasificacion']); ?></div>
                    </div>
                </td>
                <?php endfor; ?>
            </tr>
        </table>
    </div>

    <!-- KPIs -->
    <div class="section-title">Métricas de Resumen</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #0d6efd;">
                        <h3 style="color:#0d6efd"><?php echo number_format($totalParticipantesGlobal, 0, '', '.'); ?></h3>
                        <p>Total Participantes</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #198754;">
                        <h3 style="color:#198754"><?php echo round($promedio); ?></h3>
                        <p>Promedio / Curso</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #6f42c1;">
                        <h3 style="color:#6f42c1"><?php echo $cantidadCursos; ?></h3>
                        <p>Cursos Analizados</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 4px solid #ffc107;">
                        <?php 
                            $nomMax = htmlspecialchars($cursoMasActivo['nombre_curso']);
                            if(strlen($nomMax) > 40) $nomMax = substr($nomMax, 0, 37).'...';
                        ?>
                        <h3 style="color:#ffc107; font-size: 14px; margin-top:4px;"><?php echo $nomMax; ?></h3>
                        <p>Curso más Activo</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <?php if (!empty($chartImageBase64)): ?>
        <div class="page-break"></div>
        <div class="section-title">Distribución de Participantes (Top Cursos)</div>
        <div class="chart-box">
            <img src="<?php echo $chartImageBase64; ?>" alt="Gráfico Top Cursos" style="max-height: 400px;">
        </div>
    <?php endif; ?>

    <div class="page-break"></div>

    <!-- Tabla Detallada -->
    <div class="section-title">Tabla de Clasificación de Cursos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">Pos.</th>
                <th style="width: 40%; text-align:left;">Curso</th>
                <th style="width: 12%;">Participantes</th>
                <th style="width: 12%;">Estatus</th>
                <th style="width: 10%;">Modalidad</th>
                <th style="width: 11%;">Duración</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $pos = 1;
            foreach ($cursos as $row): 
                $badgeClass = 'badge-normal';
                if ($row['clasificacion'] == 'Muy Activo') $badgeClass = 'badge-muy-activo';
                elseif ($row['clasificacion'] == 'Activo') $badgeClass = 'badge-activo';
            ?>
                <tr>
                    <td class="text-center"><?php echo $pos; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_curso']); ?></td>
                    <td class="text-center"><strong><?php echo $row['total_participantes']; ?></strong></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['estatus'] ?? 'N/D'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['modalidad'] ?? 'N/D'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['duracion_legible']); ?></td>
                    <td class="text-center"><span class="<?php echo $badgeClass; ?>"><?php echo htmlspecialchars($row['clasificacion']); ?></span></td>
                </tr>
            <?php 
                $pos++;
            endforeach; 
            ?>
        </tbody>
    </table>

</body>
</html>

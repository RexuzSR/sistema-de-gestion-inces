<?php
// Variables disponibles:
// $anioBase, $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $filtrosHtml, $totalSistemaValor, $nuevosVal, $activosVal, $retiradosVal, $retencionVal
// $mesPicoNombre, $mesPicoValor, $promedioMensual, $activosFormacion, $pctFormacionActiva
// $tablaEstadosHtml, $panelDetalleHtml, $seccionMensualHtml, $chartHtml
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Gerencial — Total de Participantes</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 */
        @page { 
            margin: 110px 40px 60px 40px; 
        }
        
        body { 
            font-family: "Helvetica", "Arial", sans-serif; 
            font-size: 10px; 
            color: #1e293b; 
            line-height: 1.4; 
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -90px; 
            left: 0px; 
            right: 0px; 
            height: 75px; 
            border-bottom: 3px solid #0284c7; 
            padding-bottom: 8px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 50px; }
        .header-title { text-align: center; }
        .header-title h1 { margin: 0; font-size: 16px; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .header-title p { margin: 2px 0 0 0; font-size: 11px; color: #0284c7; font-weight: bold; }
        .header-doc-code { font-size: 8px; color: #64748b; margin-top: 3px; }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -35px; 
            left: 0px; 
            right: 0px; 
            height: 25px; 
            border-top: 1px solid #cbd5e1; 
            font-size: 8px; 
            color: #64748b; 
            padding-top: 5px;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; }
        .page-number:after { content: counter(page); }

        /* Utilidades */
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        /* Títulos de Sección */
        .section-title {
            background-color: #1e293b;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 18px 0 10px 0;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }

        /* Ficha de Metadatos de Auditoría */
        .audit-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
        }
        .audit-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
        .audit-table td { padding: 3px 6px; }
        .audit-label { color: #64748b; font-weight: bold; width: 15%; }
        .audit-val { color: #0f172a; font-weight: 500; }

        /* Filtros Activos */
        .filter-box { 
            background: #e0f2fe; 
            border: 1px solid #bae6fd; 
            color: #0369a1; 
            padding: 6px 10px; 
            border-radius: 4px; 
            margin-top: 5px; 
            font-size: 9px; 
        }

        /* Tarjetas KPI (Executive Summary Grid) */
        .kpi-row { width: 100%; margin-bottom: 15px; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 6px 0; }
        .kpi-card { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px; 
            padding: 8px 4px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 16px; margin: 0 0 3px 0; font-weight: bold; }
        .kpi-card p { font-size: 8px; margin: 0; color: #64748b; text-transform: uppercase; font-weight: bold; }

        /* Insights Cards Grid */
        .insight-row { width: 100%; margin-bottom: 15px; }
        .insight-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 6px 0; }
        .insight-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #0284c7;
            border-radius: 4px;
            padding: 7px 8px;
        }
        .insight-card-title { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 3px; }
        .insight-card-val { font-size: 13px; color: #0f172a; font-weight: bold; }
        .insight-card-sub { font-size: 7.5px; color: #0284c7; margin-top: 2px; }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9.5px; 
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #cbd5e1; 
            padding: 5px 7px; 
        }
        table.data-table th { 
            background-color: #f1f5f9; 
            color: #334155; 
            font-weight: bold; 
            text-align: center; 
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.data-table tfoot tr td { background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #64748b; color: #0f172a; }

        /* Gráfico Box */
        .chart-box { text-align: center; margin: 15px 0; padding: 10px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 4px; }
        .chart-box img { max-width: 100%; max-height: 270px; }

        /* Firmas de Validación */
        .signatures-box { margin-top: 40px; width: 100%; }
        .signatures-table { width: 100%; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 20px; }
        .signature-line { border-top: 1px solid #0f172a; width: 70%; margin: 0 auto 5px auto; }
        .signature-name { font-size: 9.5px; font-weight: bold; color: #0f172a; }
        .signature-role { font-size: 8px; color: #64748b; text-transform: uppercase; }
    </style>
</head>
<body>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%;"><img src="<?php echo $logoGobierno; ?>" class="logo" alt="Gobierno"></td>
                <td style="width: 60%;" class="header-title">
                    <h1>Informe Gerencial de Administración</h1>
                    <p>REPORTE CONSOLIDADO: TOTAL DE PARTICIPANTES</p>
                    <div class="header-doc-code">DOCUMENTO OFICIAL DE GESTIÓN ADMINISTRATIVA · INCES INDUSTRIAL</div>
                </td>
                <td style="width: 20%; text-align: right;"><img src="<?php echo $logoInces; ?>" class="logo" alt="INCES"></td>
            </tr>
        </table>
    </header>

    <!-- Footer Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 35%;">Emisión: <?php echo htmlspecialchars($usuarioFull); ?> (Admin. de Sistema)</td>
                <td style="width: 30%; text-align: center;">Página <span class="page-number"></span></td>
                <td style="width: 35%; text-align: right;"><?php echo $fechaGeneracion; ?></td>
            </tr>
        </table>
    </footer>

    <!-- FICHA DE METADATOS DE AUDITORÍA -->
    <div class="audit-card">
        <table class="audit-table">
            <tr>
                <td class="audit-label">Período Base:</td>
                <td class="audit-val">Año <?php echo $anioBase; ?></td>
                <td class="audit-label">Fecha de Emisión:</td>
                <td class="audit-val"><?php echo $fechaGeneracion; ?></td>
            </tr>
            <tr>
                <td class="audit-label">Generado Por:</td>
                <td class="audit-val"><?php echo htmlspecialchars($usuarioFull); ?></td>
                <td class="audit-label">N° Correlativo:</td>
                <td class="audit-val">INF-ADM-<?php echo $anioBase; ?>-<?php echo date('md'); ?></td>
            </tr>
        </table>
        <?php echo $filtrosHtml; ?>
    </div>

    <!-- RESUMEN EJECUTIVO (KPIs) -->
    <div class="section-title">Resumen Ejecutivo de Indicadores</div>
    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #0284c7;">
                        <h3 style="color:#0284c7"><?php echo $totalSistemaValor; ?></h3>
                        <p>Total Sistema</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #2563eb;">
                        <h3 style="color:#2563eb"><?php echo $nuevosVal; ?></h3>
                        <p>Nuevos Inscritos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #16a34a;">
                        <h3 style="color:#16a34a"><?php echo $activosVal; ?></h3>
                        <p>Activos Cursando</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #dc2626;">
                        <h3 style="color:#dc2626"><?php echo $retiradosVal; ?></h3>
                        <p>Finalizados / Bajas</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #d97706;">
                        <h3 style="color:#d97706"><?php echo $retencionVal; ?>%</h3>
                        <p>Tasa Retención</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- PANEL DE INSIGHTS ADMINISTRATIVOS (TOMA DE DECISIONES) -->
    <div class="section-title">Panel de Insights Estratégicos</div>
    <div class="insight-row">
        <table>
            <tr>
                <td>
                    <div class="insight-card" style="border-left-color: #2563eb;">
                        <div class="insight-card-title">🏆 Mes Pico de Matriculación</div>
                        <div class="insight-card-val"><?php echo htmlspecialchars($mesPicoNombre); ?></div>
                        <div class="insight-card-sub"><?php echo number_format($mesPicoValor, 0, '', '.'); ?> nuevos inscritos</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #0284c7;">
                        <div class="insight-card-title">📊 Promedio Mensual</div>
                        <div class="insight-card-val"><?php echo $promedioMensual; ?></div>
                        <div class="insight-card-sub">participantes / mes</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #16a34a;">
                        <div class="insight-card-title">🎓 En Formación Activa</div>
                        <div class="insight-card-val"><?php echo number_format($activosFormacion, 0, '', '.'); ?></div>
                        <div class="insight-card-sub"><?php echo $pctFormacionActiva; ?>% del universo registrado</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #d97706;">
                        <div class="insight-card-title">⚡ Período Auditado</div>
                        <div class="insight-card-val">12 Meses</div>
                        <div class="insight-card-sub">Consolidado Anual <?php echo $anioBase; ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- PERFIL DEMOGRÁFICO Y DIVERSIDAD INSTITUCIONAL -->
    <div class="section-title">Perfil Demográfico & Inclusión Institucional</div>
    <div class="insight-row">
        <table>
            <tr>
                <td>
                    <div class="insight-card" style="border-left-color: #2563eb;">
                        <div class="insight-card-title">Masculinos</div>
                        <div class="insight-card-val"><?php echo number_format($masculinoVal, 0, '', '.'); ?></div>
                        <div class="insight-card-sub"><?php echo $pctMasculino; ?>% del total general</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #dc2626;">
                        <div class="insight-card-title">Femeninos</div>
                        <div class="insight-card-val"><?php echo number_format($femeninoVal, 0, '', '.'); ?></div>
                        <div class="insight-card-sub"><?php echo $pctFemenino; ?>% del total general</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #0284c7;">
                        <div class="insight-card-title">Con Discapacidad</div>
                        <div class="insight-card-val"><?php echo number_format($conDiscVal, 0, '', '.'); ?></div>
                        <div class="insight-card-sub"><?php echo $pctConDisc; ?>% inclusión registrada</div>
                    </div>
                </td>
                <td>
                    <div class="insight-card" style="border-left-color: #64748b;">
                        <div class="insight-card-title">Sin Discapacidad</div>
                        <div class="insight-card-val"><?php echo number_format($sinDiscVal, 0, '', '.'); ?></div>
                        <div class="insight-card-sub"><?php echo $pctSinDisc; ?>% sin discapacidad</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- DISTRIBUCIÓN OPERATIVA POR ESTATUS ACTUAL -->
    <div class="section-title">Distribución Operativa por Estatus en Sistema</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align:left;">Estado Operativo</th>
                <th>Total de Participantes</th>
                <th>Filtro Activo</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $tablaEstadosHtml; ?>
        </tbody>
    </table>

    <?php 
    if (!empty($panelDetalleHtml)) {
        $panelDetalleHtml = str_replace('<table>', '<table class="data-table">', $panelDetalleHtml);
        echo $panelDetalleHtml; 
    }
    ?>

    <?php 
    if (!empty($seccionMensualHtml)) {
        $seccionMensualHtml = str_replace('<table>', '<table class="data-table">', $seccionMensualHtml);
        echo $seccionMensualHtml;
    }
    ?>

    <?php 
    if (!empty($chartHtml)) {
        $chartHtml = str_replace("<div style='text-align:center; margin-top:10px;'>", "<div class='chart-box'>", $chartHtml);
        echo $chartHtml;
    }
    ?>

    <!-- BLOQUE DE FIRMAS Y CONFORMIDAD INSTITUCIONAL -->
    <div class="signatures-box">
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo htmlspecialchars($usuarioFull); ?></div>
                    <div class="signature-role">Administrador del Sistema / Control Operativo</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-name">Dirección General / Coordinación Academicó-Administrativa</div>
                    <div class="signature-role">Conformidad y Validación Institucional</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

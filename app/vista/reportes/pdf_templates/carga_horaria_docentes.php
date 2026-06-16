<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces, $filtrosTextParts, $textoPeriodo
// $cargaPanelActivo, $datos (array), $totales, $graficoBase64, $periodoCarga
// $promedioHoras, $totalHorasPendientes, $docenteMejorAvance, $docenteMenorAvance,
// $distribucionTipo, $distribucionEstado, $docenteMayorCargaFull
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Carga Horaria por Docente</title>
    <style>
        @page { margin: 120px 40px 60px 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; line-height: 1.3; }
        
        /* ─── Encabezado y Pie de página fijos ─── */
        header { position: fixed; top: -90px; left: 0; right: 0; height: 70px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; border-top: 1px solid #ddd; padding-top: 5px; font-size: 9px; color: #777; }
        .page-number:after { content: counter(page); }
        .total-pages:after { content: counter(pages); }
        
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-gob { width: 140px; }
        .logo-inces { width: 70px; }
        .header-title { text-align: center; color: #0d6efd; margin: 0; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .header-subtitle { text-align: center; color: #555; margin: 3px 0 0 0; font-size: 11px; }
        
        /* ─── Utilidades ─── */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-primary { color: #0d6efd; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-muted { color: #6c757d; }
        .fw-bold { font-weight: bold; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; color: white; display: inline-block; }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-secondary { background-color: #6c757d; }
        .bg-info { background-color: #0dcaf0; color: #000; }
        
        /* ─── Secciones ─── */
        .section-title { background-color: #f8f9fa; color: #0d6efd; padding: 6px 10px; margin: 15px 0 10px 0; border-left: 4px solid #0d6efd; font-size: 13px; font-weight: bold; page-break-after: avoid; }
        
        /* ─── Tarjetas KPI (Sección 1) ─── */
        .kpi-container { width: 100%; margin-bottom: 15px; }
        .kpi-card { width: 23%; display: inline-block; background-color: #fff; border: 1px solid #dee2e6; border-radius: 6px; padding: 10px; box-sizing: border-box; vertical-align: top; margin-right: 1.5%; }
        .kpi-card.last { margin-right: 0; }
        .kpi-card-blue { border-left: 4px solid #0d6efd; }
        .kpi-card-green { border-left: 4px solid #198754; }
        .kpi-card-purple { border-left: 4px solid #6f42c1; }
        .kpi-card-orange { border-left: 4px solid #fd7e14; }
        .kpi-title { font-size: 10px; color: #6c757d; text-transform: uppercase; margin-bottom: 5px; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #212529; margin-bottom: 2px; }
        .kpi-sub { font-size: 9px; color: #adb5bd; }
        
        /* ─── Grilla 2 columnas ─── */
        .row-50 { width: 100%; margin-bottom: 10px; }
        .col-50 { width: 48%; display: inline-block; vertical-align: top; }
        .col-50.right { margin-left: 3%; }
        
        /* ─── Tablas ─── */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #dee2e6; padding: 6px 8px; }
        table.data-table th { background-color: #f1f3f5; color: #495057; font-weight: bold; text-align: left; }
        table.data-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        
        /* ─── Barras de progreso ─── */
        .progress-bg { background-color: #e9ecef; border-radius: 4px; height: 10px; width: 100%; overflow: hidden; }
        .progress-bar { height: 10px; border-radius: 4px; }
        
        /* ─── Gráfico ─── */
        .chart-box { text-align: center; margin: 10px 0; border: 1px solid #dee2e6; padding: 10px; background: #fff; border-radius: 6px; }
        .chart-box img { max-width: 100%; max-height: 300px; }
        
        /* ─── Alertas ─── */
        .alert-box { padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .alert-success { background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; }
        .alert-danger { background-color: #f8d7da; border: 1px solid #f5c2c7; color: #842029; }
    </style>
</head>
<body>

    <!-- ==================== ENCABEZADO Y PIE FIJOS ==================== -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%;"><img src="<?= $logoGobierno ?>" class="logo-gob" alt="Gobierno"></td>
                <td style="width: 60%;">
                    <h2 class="header-title">SISTEMA DE GESTIÓN INCES</h2>
                    <p class="header-subtitle">Reporte Ejecutivo: Carga Horaria por Docente</p>
                    <p class="header-subtitle" style="margin-top:2px;">Período: <b><?= htmlspecialchars($textoPeriodo) ?></b></p>
                    <?php if (!empty($filtrosTextParts)): ?>
                        <p class="header-subtitle" style="font-size: 9px; margin-top:2px; color: #0d6efd;">
                            Filtros: <?= htmlspecialchars(implode(' | ', $filtrosTextParts)) ?>
                        </p>
                    <?php endif; ?>
                </td>
                <td style="width: 20%; text-align: right;"><img src="<?= $logoInces ?>" class="logo-inces" alt="INCES"></td>
            </tr>
        </table>
    </header>
    <footer>
        <table width="100%">
            <tr>
                <td width="33%" class="text-left">Generado por: <?= htmlspecialchars($usuarioFull) ?></td>
                <td width="33%" class="text-center">Fecha: <?= $fechaGeneracion ?></td>
                <td width="33%" class="text-right">Página <span class="page-number"></span> de <span class="total-pages"></span></td>
            </tr>
        </table>
    </footer>

    <!-- ==================== SECCIÓN 1: KPIs GLOBALES ==================== -->
    <div class="kpi-container">
        <?php 
            $totDocentes = intval($totales['total'] ?? 0);
            $disponibles = intval($totales['disponibles'] ?? 0);
            $asignados = intval($totales['asignados'] ?? 0);
            $totHoras = number_format($totales['horas_totales'] ?? 0, 1, ',', '.');
            $totAvance = number_format($totales['avance_global'] ?? 0, 1, ',', '.');
            
            $mejorNombre = $docenteMayorCargaFull ? htmlspecialchars($docenteMayorCargaFull['nombre_docente']) : 'N/A';
            $mejorHoras = $docenteMayorCargaFull ? number_format($docenteMayorCargaFull['horas_asignadas'], 1, ',', '.') . 'h' : '0h';
        ?>
        <div class="kpi-card kpi-card-blue">
            <div class="kpi-title">Total Docentes</div>
            <div class="kpi-value"><?= $totDocentes ?></div>
            <div class="kpi-sub"><?= $disponibles ?> disp. · <?= $asignados ?> asig.</div>
        </div>
        <div class="kpi-card kpi-card-orange">
            <div class="kpi-title">Horas Asignadas</div>
            <div class="kpi-value"><?= $totHoras ?> <small style="font-size:12px;">hrs</small></div>
            <div class="kpi-sub">Total en el período</div>
        </div>
        <div class="kpi-card kpi-card-green">
            <div class="kpi-title">Avance Global</div>
            <div class="kpi-value"><?= $totAvance ?>%</div>
            <div class="kpi-sub">Horas completadas / asignadas</div>
        </div>
        <div class="kpi-card kpi-card-purple last">
            <div class="kpi-title">Mayor Carga Asignada</div>
            <div class="kpi-value" style="font-size:15px; margin-top:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= $mejorNombre ?></div>
            <div class="kpi-sub"><?= $mejorHoras ?></div>
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: ANÁLISIS GENERAL ==================== -->
    <div class="section-title">Análisis General</div>
    <div class="row-50">
        <div class="col-50">
            <table class="data-table">
                <tr><th colspan="2" class="text-center">Métricas de Desempeño</th></tr>
                <tr>
                    <td width="50%">Docente con Mejor Avance:</td>
                    <td><b><?= $docenteMejorAvance ? htmlspecialchars($docenteMejorAvance['nombre_docente']) . ' ('.number_format($docenteMejorAvance['avance_porcentaje'],1). '%)' : 'N/A' ?></b></td>
                </tr>
                <tr>
                    <td>Docente con Menor Avance:</td>
                    <td class="text-danger"><b><?= $docenteMenorAvance ? htmlspecialchars($docenteMenorAvance['nombre_docente']) . ' ('.number_format($docenteMenorAvance['avance_porcentaje'],1). '%)' : 'N/A' ?></b></td>
                </tr>
                <tr>
                    <td>Promedio Horas Asignadas:</td>
                    <td><b><?= number_format($promedioHoras, 1, ',', '.') ?>h por docente</b></td>
                </tr>
                <tr>
                    <td>Total Horas Pendientes:</td>
                    <td class="text-warning"><b><?= number_format($totalHorasPendientes, 1, ',', '.') ?>h</b></td>
                </tr>
            </table>
        </div>
        <div class="col-50 right">
            <table class="data-table">
                <tr><th colspan="4" class="text-center">Distribución de Plantilla Docente</th></tr>
                <tr>
                    <td class="bg-primary text-white text-center">Fijo</td>
                    <td class="text-center fw-bold"><?= $distribucionTipo['Fijo'] ?? 0 ?></td>
                    <td class="bg-success text-white text-center">Disponible</td>
                    <td class="text-center fw-bold"><?= $distribucionEstado['Disponible'] ?? 0 ?></td>
                </tr>
                <tr>
                    <td class="bg-primary text-white text-center" style="opacity: 0.8;">Contratado</td>
                    <td class="text-center fw-bold"><?= $distribucionTipo['Contratado'] ?? 0 ?></td>
                    <td class="bg-warning text-center">Asignado</td>
                    <td class="text-center fw-bold"><?= $distribucionEstado['Asignado'] ?? 0 ?></td>
                </tr>
                <tr>
                    <td class="bg-primary text-white text-center" style="opacity: 0.6;">PDL</td>
                    <td class="text-center fw-bold"><?= $distribucionTipo['PDL'] ?? 0 ?></td>
                    <td class="bg-info text-center">En Formación</td>
                    <td class="text-center fw-bold"><?= $distribucionEstado['En formación'] ?? 0 ?></td>
                </tr>
                <tr>
                    <td class="bg-primary text-white text-center" style="opacity: 0.4;">Voluntario</td>
                    <td class="text-center fw-bold"><?= $distribucionTipo['Voluntario'] ?? 0 ?></td>
                    <td class="bg-secondary text-white text-center">Inactivo</td>
                    <td class="text-center fw-bold"><?= $distribucionEstado['Inactivo'] ?? 0 ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ==================== SECCIÓN 3: GRÁFICO PRINCIPAL ==================== -->
    <?php if (!empty($graficoBase64)): ?>
    <div class="section-title">Comparativa: Horas Asignadas vs Completadas (Top 8)</div>
    <div class="chart-box">
        <img src="<?= $graficoBase64 ?>" alt="Gráfico de Carga Horaria">
        <p style="font-size:10px; color:#666; margin-top:5px; margin-bottom:0;">Basado en los filtros actuales. Se muestran los 8 docentes con mayor asignación de horas.</p>
    </div>
    <div style="page-break-after: always;"></div>
    <?php endif; ?>

    <!-- ==================== SECCIÓN 4: PANEL KPI ACTIVO (Opcional) ==================== -->
    <?php if ($cargaPanelActivo !== null && $cargaPanelActivo !== '' && $cargaPanelActivo !== 'top'): ?>
        <div class="section-title">Análisis Detallado: <?= ucfirst($cargaPanelActivo) ?></div>
        <?php
            // Lógica simple para emular el contenido del panel si existe
            // Si el usuario tenía abierto "total", "horas" o "avance", mostramos las tablas en el PDF
            if ($cargaPanelActivo === 'total') {
                echo '<div class="row-50">';
                echo '<div class="col-50">';
                echo '<table class="data-table"><tr><th colspan="2">Resumen por Tipo</th></tr>';
                foreach($distribucionTipo as $k=>$v) echo "<tr><td>$k</td><td class='text-right fw-bold'>$v</td></tr>";
                echo '</table></div>';
                echo '<div class="col-50 right">';
                echo '<table class="data-table"><tr><th colspan="2">Resumen por Estado</th></tr>';
                foreach($distribucionEstado as $k=>$v) echo "<tr><td>$k</td><td class='text-right fw-bold'>$v</td></tr>";
                echo '</table></div>';
                echo '</div>';
            } else if ($cargaPanelActivo === 'avance') {
                $tramo0 = 0; $tramo1 = 0; $tramo2 = 0; $tramo3 = 0;
                foreach($datos as $d) {
                    $av = floatval($d['avance_porcentaje']);
                    if($av < 25) $tramo0++;
                    else if($av < 50) $tramo1++;
                    else if($av < 75) $tramo2++;
                    else $tramo3++;
                }
                echo '<table class="data-table"><tr><th>Tramo de Avance</th><th>Cantidad de Docentes</th></tr>';
                echo "<tr><td>0% - 24%</td><td class='fw-bold text-danger'>$tramo0</td></tr>";
                echo "<tr><td>25% - 49%</td><td class='fw-bold text-warning'>$tramo1</td></tr>";
                echo "<tr><td>50% - 74%</td><td class='fw-bold text-info'>$tramo2</td></tr>";
                echo "<tr><td>75% - 100%</td><td class='fw-bold text-success'>$tramo3</td></tr>";
                echo '</table>';
            }
        ?>
    <?php endif; ?>

    <!-- ==================== SECCIÓN 6: ALERTAS DE SOBRECARGA ==================== -->
    <div class="section-title">Alertas de Sobrecarga</div>
    <?php 
        $alertas = [];
        // Umbrales realistas (basados en 40h semanales)
        $umbral = 1920; 
        $textoUmbral = "1.920h anuales";
        if ($periodoCarga === 'month') { $umbral = 160; $textoUmbral = "160h mensuales"; }
        else if ($periodoCarga === 'week') { $umbral = 40; $textoUmbral = "40h semanales"; }

        foreach ($datos as $d) {
            $hAsig = floatval($d['horas_asignadas']);
            if ($hAsig >= $umbral) {
                $alertas[] = $d;
            }
        }
    ?>
    <?php if (count($alertas) > 0): ?>
        <div class="alert-box alert-danger">
            <b><span style="font-family: DejaVu Sans, sans-serif;">⚠</span> ATENCIÓN:</b> Se detectaron <?= count($alertas) ?> docente(s) superando el umbral de <?= $textoUmbral ?> en el período evaluado.
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Horas Asignadas</th>
                    <th>Motivo de Alerta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($alertas as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre_docente']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($a['tipo_docente']) ?></td>
                        <td class="text-center fw-bold text-danger"><?= number_format($a['horas_asignadas'], 1, ',', '.') ?></td>
                        <td>Supera límite recomendado de <?= $textoUmbral ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert-box alert-success">
            <b><span style="font-family: DejaVu Sans, sans-serif;">✓</span> ESTADO ÓPTIMO:</b> Ningún docente en la lista actual supera los umbrales de sobrecarga de horas (<?= $textoUmbral ?>).
        </div>
    <?php endif; ?>

    <!-- ==================== SECCIÓN 5: RANKING COMPLETO (TABLA PRINCIPAL) ==================== -->
    <div class="section-title">Ranking General de Carga Horaria</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" width="4%">#</th>
                <th width="20%">Docente</th>
                <th width="10%">Tipo</th>
                <th width="10%">Estado</th>
                <th class="text-center" width="9%">Cursos</th>
                <th class="text-center" width="10%">H. Asignadas</th>
                <th class="text-center" width="10%">H. Complet.</th>
                <th class="text-center" width="12%">Avance %</th>
                <th class="text-center" width="15%">Nivel</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($datos) === 0): ?>
                <tr><td colspan="9" class="text-center">No se encontraron docentes con los criterios de búsqueda actuales.</td></tr>
            <?php else: ?>
                <?php $pos = 1; foreach ($datos as $d): 
                    $hAsig = floatval($d['horas_asignadas']);
                    $hComp = floatval($d['horas_completadas']);
                    $avance = floatval($d['avance_porcentaje']);
                    $cursos = intval($d['total_cursos']);
                    
                    // Semáforo Avance
                    $claseAvance = 'bg-danger text-white';
                    if ($avance >= 75) $claseAvance = 'bg-success text-white';
                    else if ($avance >= 40) $claseAvance = 'bg-warning';
                    
                    // Semáforo Nivel
                    $claseNivel = 'bg-success text-white';
                    $textoNivel = 'Moderado';
                    if ($hAsig >= $umbral) { $claseNivel = 'bg-danger text-white'; $textoNivel = 'Avanzado (Sobrecarga)'; }
                    else if ($hAsig == 0) { $claseNivel = 'bg-secondary text-white'; $textoNivel = 'Sin Asignación'; }
                    
                    $estiloFila = ($pos === 1) ? 'background-color: #fff3cd;' : ''; // Amarillo claro para el #1
                ?>
                <tr style="<?= $estiloFila ?>">
                    <td class="text-center fw-bold"><?= $pos ?></td>
                    <td><?= htmlspecialchars($d['nombre_docente']) ?></td>
                    <td><?= htmlspecialchars($d['tipo_docente']) ?></td>
                    <td><?= htmlspecialchars($d['estado_docente']) ?></td>
                    <td class="text-center"><?= $cursos ?></td>
                    <td class="text-center fw-bold"><?= number_format($hAsig, 1, ',', '.') ?></td>
                    <td class="text-center"><?= number_format($hComp, 1, ',', '.') ?></td>
                    <td class="text-center">
                        <span class="badge <?= $claseAvance ?>" style="width: 40px;"><?= number_format($avance, 1, ',', '.') ?>%</span>
                    </td>
                    <td class="text-center">
                        <span class="badge <?= $claseNivel ?>"><?= $textoNivel ?></span>
                    </td>
                </tr>
                <?php $pos++; endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- ==================== SECCIÓN 7: RANKING POR AVANCE (Top 10) ==================== -->
    <div style="page-break-inside: avoid;">
        <div class="section-title">Top 10: Mejores Avances de Ejecución</div>
        <?php 
            $datosAvance = $datos;
            // Filtrar solo los que tienen horas asignadas para ser justos
            $datosAvance = array_filter($datosAvance, function($x) { return floatval($x['horas_asignadas']) > 0; });
            usort($datosAvance, function($a, $b) {
                return floatval($b['avance_porcentaje']) <=> floatval($a['avance_porcentaje']);
            });
            $top10 = array_slice($datosAvance, 0, 10);
        ?>
        <?php if (count($top10) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="35%">Docente</th>
                        <th width="20%" class="text-center">Ejecución (Comp / Asig)</th>
                        <th width="40%">Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $p = 1; foreach($top10 as $t): 
                        $av = floatval($t['avance_porcentaje']);
                        $bgProg = '#dc3545';
                        if ($av >= 75) $bgProg = '#198754';
                        else if ($av >= 40) $bgProg = '#ffc107';
                        
                        // Fix overflow de %
                        $w = $av > 100 ? 100 : $av;
                    ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $p ?></td>
                        <td><?= htmlspecialchars($t['nombre_docente']) ?></td>
                        <td class="text-center"><?= number_format($t['horas_completadas'],1,',','.') ?> / <?= number_format($t['horas_asignadas'],1,',','.') ?>h</td>
                        <td>
                            <div style="float: left; width: 85%;">
                                <div class="progress-bg" style="margin-top:2px;">
                                    <div class="progress-bar" style="width: <?= $w ?>%; background-color: <?= $bgProg ?>;"></div>
                                </div>
                            </div>
                            <div style="float: right; width: 15%; text-align: right; font-weight: bold; font-size: 9px;">
                                <?= number_format($av, 1, ',', '.') ?>%
                            </div>
                            <div style="clear:both;"></div>
                        </td>
                    </tr>
                    <?php $p++; endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted text-center" style="font-size:11px;">No hay datos de avance suficientes.</p>
        <?php endif; ?>
    </div>

</body>
</html>

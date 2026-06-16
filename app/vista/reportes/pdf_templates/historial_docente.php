<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $perfil, $cursos, $resumen, $desempeno, $resumenEjecutivo, $filtros, $stats
// $graficoDona, $graficoBar, $graficoLinea, $graficoEsp

$nombreCompleto = trim(($perfil['primer_nombre'] ?? '') . ' ' . ($perfil['primer_apellido'] ?? ''));
$cedula = $perfil['cedula'] ?? '—';
$tipoDocente = $perfil['tipo_docente'] ?? '—';
$estadoDocente = $perfil['estado_docente'] ?? '—';

$coloresEstado = [
    'Fijo' => '#0d6efd',
    'Contratado' => '#0dcaf0',
    'PDL' => '#198754',
    'Voluntario' => '#6c757d'
];
$colorEstado = $coloresEstado[$estadoDocente] ?? '#6c757d';

$coloresEstatusCurso = [
    'Culminado' => '#198754',
    'En proceso' => '#0d6efd',
    'En pausa' => '#ffc107',
    'En espera' => '#6c757d',
    'Cancelado' => '#dc3545'
];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Historial de Docente</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        @page { margin: 120px 40px 60px 40px; }
        
        /* Header & Footer */
        header { position: fixed; top: -90px; left: 0; right: 0; height: 70px; border-bottom: 2px solid #004d99; padding-bottom: 10px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-gobierno { width: 140px; }
        .logo-inces { width: 70px; }
        .header-title { text-align: center; color: #004d99; font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .header-subtitle { text-align: center; color: #555; font-size: 12px; margin: 3px 0 0 0; }
        .header-meta { text-align: right; font-size: 9px; color: #777; }
        
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; border-top: 1px solid #ddd; padding-top: 5px; font-size: 9px; color: #777; }
        .footer-table { width: 100%; }
        .page-num:after { content: counter(page); }
        
        /* General Layout */
        .section-title { font-size: 14px; font-weight: bold; color: #004d99; background: #eef5fa; padding: 6px 10px; margin: 20px 0 10px 0; border-left: 4px solid #004d99; }
        .grid-2 { width: 100%; border-collapse: separate; border-spacing: 15px 0; }
        .grid-2 td { width: 50%; vertical-align: top; }
        .mb-2 { margin-bottom: 10px; }
        
        /* Perfil */
        .profile-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .profile-table td { padding: 5px; border-bottom: 1px solid #eee; }
        .profile-label { font-weight: bold; color: #555; width: 120px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; color: #fff; font-size: 10px; font-weight: bold; }
        .badge-outline { display: inline-block; padding: 2px 6px; border: 1px solid #ccc; border-radius: 4px; color: #555; font-size: 9px; margin-right: 3px; background: #fafafa; }
        
        /* KPIs */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 15px; }
        .kpi-card { background: #f8f9fa; border: 1px solid #e9ecef; border-left: 4px solid #004d99; border-radius: 4px; padding: 10px; text-align: center; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #004d99; margin-bottom: 3px; }
        .kpi-label { font-size: 9px; color: #666; text-transform: uppercase; }
        
        /* Barras de progreso */
        .progress-container { width: 100%; background: #e9ecef; border-radius: 4px; overflow: hidden; height: 12px; margin-top: 4px; margin-bottom: 4px; }
        .progress-bar { height: 100%; background: #004d99; }
        
        /* Desempeño */
        .perf-card { border: 1px solid #eee; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .perf-header { font-weight: bold; font-size: 11px; margin-bottom: 5px; }
        .perf-desc { font-size: 9px; color: #666; margin-top: 5px; }
        
        /* Resumen */
        .executive-summary { background: #fcfcfc; border: 1px solid #eaeaea; border-left: 4px solid #198754; padding: 12px; font-size: 12px; line-height: 1.5; text-align: justify; color: #444; }
        
        /* Gráficos */
        .chart-box { border: 1px solid #eee; padding: 10px; text-align: center; margin-bottom: 15px; }
        .chart-box img { max-width: 100%; max-height: 200px; }
        .chart-title { font-weight: bold; font-size: 11px; margin-bottom: 10px; color: #444; }
        
        /* Filtros */
        .filter-box { background: #fffbe6; border: 1px solid #ffe58f; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        .filter-item { display: inline-block; margin-right: 15px; }
        .filter-item strong { color: #d48806; }
        
        /* Tabla de cursos */
        .data-table { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 15px; }
        .data-table th { background: #343a40; color: #fff; text-align: left; padding: 6px; }
        .data-table td { border-bottom: 1px solid #eee; padding: 6px; vertical-align: middle; }
        .data-table tr:nth-child(even) td { background: #f8f9fa; }
        
        /* Stats Table */
        .stats-table { width: 100%; border-collapse: collapse; }
        .stats-table td { padding: 8px 5px; border-bottom: 1px solid #eee; }
        .stats-label { font-weight: bold; color: #555; width: 60%; }
        .stats-value { text-align: right; font-weight: bold; color: #004d99; }
    </style>
</head>
<body>

<header>
    <table class="header-table">
        <tr>
            <td width="25%"><img src="<?= $logoGobierno ?>" class="logo-gobierno" alt="Gobierno"></td>
            <td width="50%">
                <p class="header-title">Sistema de Gestión INCES</p>
                <p class="header-subtitle">Historial de Docente</p>
            </td>
            <td width="25%" style="text-align: right;">
                <img src="<?= $logoInces ?>" class="logo-inces" alt="INCES">
                <div class="header-meta">
                    C.I: <?= $cedula ?><br>
                    <?= $fechaGeneracion ?>
                </div>
            </td>
        </tr>
    </table>
</header>

<footer>
    <table class="footer-table">
        <tr>
            <td width="33%">Generado por: <?= htmlspecialchars($usuarioFull) ?></td>
            <td width="33%" style="text-align: center;">Sistema de Gestión INCES — Historial de Docente</td>
            <td width="33%" style="text-align: right;">Página <span class="page-num"></span></td>
        </tr>
    </table>
</footer>

<main>
    <!-- SECCIÓN 1: PERFIL -->
    <div class="section-title">1. Perfil del Docente</div>
    
    <table class="profile-table">
        <tr>
            <td class="profile-label">Nombre Completo:</td>
            <td><strong><?= htmlspecialchars($nombreCompleto) ?></strong></td>
            <td class="profile-label">Cédula:</td>
            <td><?= htmlspecialchars($cedula) ?></td>
        </tr>
        <tr>
            <td class="profile-label">Tipo y Estado:</td>
            <td>
                <?= htmlspecialchars($tipoDocente) ?> | 
                <span class="badge" style="background-color: <?= $colorEstado ?>"><?= htmlspecialchars($estadoDocente) ?></span>
            </td>
            <td class="profile-label">Correo:</td>
            <td><?= htmlspecialchars($perfil['correo'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="profile-label">Teléfonos:</td>
            <td><?= htmlspecialchars(($perfil['telefono'] ?? '—') . ' / ' . ($perfil['telefono2'] ?? '—')) ?></td>
            <td class="profile-label">Edad / Género:</td>
            <td><?= htmlspecialchars(($perfil['edad'] ?? '—') . ' años / ' . ($perfil['genero'] ?? '—')) ?></td>
        </tr>
        <tr>
            <td class="profile-label">Especializaciones:</td>
            <td colspan="3">
                <?php 
                $areasRaw = $perfil['areas'] ?? $perfil['areas_formativas'] ?? '';
                $areas = is_array($areasRaw) ? $areasRaw : array_filter(array_map('trim', explode(',', $areasRaw)));

                $ambitosRaw = $perfil['ambitos'] ?? '';
                $ambitos = is_array($ambitosRaw) ? $ambitosRaw : array_filter(array_map('trim', explode(',', $ambitosRaw)));

                $esps = array_unique(array_merge($areas, $ambitos));
                if (count($esps) > 0) {
                    foreach ($esps as $e) {
                        echo '<span class="badge-outline">'.htmlspecialchars($e).'</span>';
                    }
                } else {
                    echo '—';
                }
                ?>
            </td>
        </tr>
    </table>

    <table class="kpi-table">
        <tr>
            <td width="25%">
                <div class="kpi-card" style="border-left-color: #0dcaf0;">
                    <div class="kpi-value"><?= intval($resumen['cursos_asignados'] ?? 0) ?></div>
                    <div class="kpi-label">Cursos Asignados</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-card" style="border-left-color: #198754;">
                    <div class="kpi-value"><?= intval($resumen['cursos_completados'] ?? 0) ?></div>
                    <div class="kpi-label">Cursos Completados</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-card" style="border-left-color: #ffc107;">
                    <div class="kpi-value"><?= round(floatval($resumen['horas_asignadas'] ?? 0)) ?></div>
                    <div class="kpi-label">Horas Asignadas</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-card" style="border-left-color: #198754;">
                    <div class="kpi-value"><?= round(floatval($resumen['horas_completadas'] ?? 0)) ?></div>
                    <div class="kpi-label">Horas Completadas</div>
                </div>
            </td>
        </tr>
    </table>

    <?php
    $hAsig = floatval($resumen['horas_asignadas'] ?? 0);
    $hComp = floatval($resumen['horas_completadas'] ?? 0);
    $pctHoras = $hAsig > 0 ? round(($hComp / $hAsig) * 100) : 0;
    $colorProg = $pctHoras >= 75 ? '#198754' : ($pctHoras >= 40 ? '#ffc107' : '#dc3545');
    ?>
    <div style="font-size: 10px; font-weight: bold; margin-bottom: 2px;">Avance Global de Horas (<?= $pctHoras ?>%)</div>
    <div class="progress-container mb-2">
        <div class="progress-bar" style="width: <?= $pctHoras ?>%; background-color: <?= $colorProg ?>;"></div>
    </div>


    <!-- SECCIÓN 2: ÍNDICE DE DESEMPEÑO -->
    <div class="section-title" style="page-break-before: auto;">2. Índice de Desempeño</div>
    
    <?php
    $puntajeTotal = $desempeno['indice'] ?? 0;
    $nivel = $desempeno['nivel'] ?? 'No evaluado';
    
    $bootstrapColores = [
        'success' => '#198754',
        'primary' => '#0d6efd',
        'info' => '#0dcaf0',
        'warning' => '#ffc107',
        'danger' => '#dc3545',
        'secondary' => '#6c757d'
    ];
    
    $colorDesempenoKey = $desempeno['color'] ?? '';
    $colorDesempeno = $bootstrapColores[$colorDesempenoKey] ?? $colorDesempenoKey ?: '#6c757d';
    
    $comps = $desempeno['componentes'] ?? [];
    ?>
    
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 12px; margin-bottom: 15px; text-align: center;">
        <span style="font-size: 12px; color: #555;">Puntuación Global:</span>
        <strong style="font-size: 24px; color: <?= $colorDesempeno ?>; margin: 0 15px;"><?= round($puntajeTotal, 1) ?> / 100</strong>
        <span class="badge" style="background-color: <?= $colorDesempeno ?>; font-size: 12px;"><?= $nivel ?></span>
    </div>

    <table class="grid-2">
        <tr>
            <?php 
            $count = 0;
            foreach ($comps as $idx => $comp): 
                if ($count > 0 && $count % 2 === 0) {
                    echo '</tr><tr>';
                }
                $count++;
                
                $compNombre = $comp['label'] ?? $comp['nombre'] ?? '';
                $compObtenido = $comp['puntaje'] ?? $comp['obtenido'] ?? 0;
                $compMaximo = $comp['max'] ?? $comp['maximo'] ?? 0;
                $compDesc = $comp['descripcion'] ?? '';
                
                $compClrKey = $comp['color'] ?? '';
                $compColor = $bootstrapColores[$compClrKey] ?? $colorDesempeno;
                
                $pMax = $compMaximo > 0 ? $compMaximo : 1;
                $pctComp = min(100, max(0, ($compObtenido / $pMax) * 100)); 
            ?>
            <td>
                <div class="perf-card" style="border-left: 4px solid <?= $compColor ?>;">
                    <div class="perf-header">
                        <?= htmlspecialchars($compNombre) ?>
                        <span style="float: right; color: <?= $compColor ?>; font-weight: bold;"><?= round($compObtenido, 1) ?> / <?= $compMaximo ?> pts</span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?= $pctComp ?>%; background-color: <?= $compColor ?>;"></div>
                    </div>
                    <div class="perf-desc"><?= htmlspecialchars($compDesc) ?></div>
                </div>
            </td>
            <?php endforeach; ?>
        </tr>
    </table>


    <!-- SECCIÓN 3: RESUMEN EJECUTIVO -->
    <div class="section-title">3. Resumen Ejecutivo</div>
    <div class="executive-summary">
        <?= nl2br(htmlspecialchars($resumenEjecutivo)) ?>
    </div>


    <!-- SECCIÓN 4: GRÁFICOS -->
    <div class="section-title" style="page-break-before: always;">4. Representación Gráfica</div>
    
    <table class="grid-2">
        <tr>
            <td>
                <div class="chart-box">
                    <div class="chart-title">Cursos por Estatus</div>
                    <?php if(!empty($graficoDona)): ?>
                        <img src="<?= $graficoDona ?>" alt="Gráfico Dona">
                    <?php else: ?>
                        <p style="color:#999; padding: 40px 0;">Sin datos suficientes</p>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="chart-box">
                    <div class="chart-title">Horas Asignadas vs Completadas</div>
                    <?php if(!empty($graficoBar)): ?>
                        <img src="<?= $graficoBar ?>" alt="Gráfico Barras">
                    <?php else: ?>
                        <p style="color:#999; padding: 40px 0;">Sin datos suficientes</p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="chart-box">
                    <div class="chart-title">Evolución Histórica (Cursos por Año)</div>
                    <?php if(!empty($graficoLinea)): ?>
                        <img src="<?= $graficoLinea ?>" alt="Gráfico Línea">
                    <?php else: ?>
                        <p style="color:#999; padding: 40px 0;">Sin datos suficientes</p>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="chart-box">
                    <div class="chart-title">Especialización (Áreas y Ámbitos)</div>
                    <?php if(!empty($graficoEsp)): ?>
                        <img src="<?= $graficoEsp ?>" alt="Gráfico Especialización">
                    <?php else: ?>
                        <p style="color:#999; padding: 40px 0;">Sin datos suficientes</p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>


    <!-- SECCIÓN 5: FILTROS ACTIVOS -->
    <?php 
    $fAnio = $filtros['anio'] ?? 'todos';
    $fEstatus = $filtros['estatus'] ?? 'todos';
    $fAmbito = $filtros['ambito'] ?? 'todos';
    
    if ($fAnio !== 'todos' || $fEstatus !== 'todos' || $fAmbito !== 'todos'): 
    ?>
    <div class="section-title">5. Filtros Aplicados</div>
    <div class="filter-box">
        Este reporte de historial ha sido filtrado con los siguientes criterios:<br><br>
        <?php if($fAnio !== 'todos'): ?>
            <div class="filter-item"><strong>Año:</strong> <?= htmlspecialchars($fAnio) ?></div>
        <?php endif; ?>
        <?php if($fEstatus !== 'todos'): ?>
            <div class="filter-item"><strong>Estatus:</strong> <?= htmlspecialchars($fEstatus) ?></div>
        <?php endif; ?>
        <?php if($fAmbito !== 'todos'): ?>
            <div class="filter-item"><strong>Ámbito:</strong> <?= htmlspecialchars($fAmbito) ?></div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Sin filtros adicionales -->
    <?php endif; ?>


    <!-- SECCIÓN 6: HISTORIAL DE CURSOS -->
    <div class="section-title" <?= ($fAnio === 'todos' && $fEstatus === 'todos' && $fAmbito === 'todos') ? 'style="page-break-before: always;"' : '' ?>>
        <?= ($fAnio !== 'todos' || $fEstatus !== 'todos' || $fAmbito !== 'todos') ? '6.' : '5.' ?> Historial de Cursos
    </div>
    
    <?php if (count($cursos) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th width="22%">Curso</th>
                <th width="12%">Período</th>
                <th width="10%">Horas (A/C)</th>
                <th width="18%">Avance</th>
                <th width="10%">Particip.</th>
                <th width="10%">Aprob.</th>
                <th width="10%">Ámbito</th>
                <th width="8%">Estatus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cursos as $c): 
                $asig = floatval($c['horas_totales'] ?? 0);
                $comp = floatval($c['horas_completadas'] ?? 0);
                $pct = $asig > 0 ? round(($comp / $asig) * 100) : 0;
                $clsColor = $pct >= 75 ? '#198754' : ($pct >= 40 ? '#ffc107' : '#dc3545');
                
                $est = $c['estatus_curso'] ?? '—';
                $estColor = $coloresEstatusCurso[$est] ?? '#6c757d';
                
                $fini = !empty($c['fecha_inicio']) ? date('d/m/y', strtotime($c['fecha_inicio'])) : '—';
                $ffin = !empty($c['fecha_fin']) ? date('d/m/y', strtotime($c['fecha_fin'])) : '—';
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($c['nombre_curso'] ?? '—') ?></strong><br>
                    <span style="color:#777; font-size:8px;"><?= htmlspecialchars($c['tipo_formacion'] ?? '') ?></span>
                </td>
                <td><?= $fini ?><br><span style="color:#777; font-size:8px;"><?= $ffin ?></span></td>
                <td style="text-align: center;">
                    <?= round($asig) ?>h<br>
                    <span style="color:#0d6efd; font-size:8px;"><?= round($comp) ?>h</span>
                </td>
                <td>
                    <div style="float: left; width: 60%; margin-top: 3px;">
                        <div class="progress-container" style="height: 6px; margin: 0;">
                            <div class="progress-bar" style="width: <?= $pct ?>%; background-color: <?= $clsColor ?>;"></div>
                        </div>
                    </div>
                    <div style="float: right; width: 35%; text-align: right; color: <?= $clsColor ?>; font-weight: bold; font-size: 9px;">
                        <?= $pct ?>%
                    </div>
                </td>
                <td style="text-align: center;">
                    <?= intval($c['total_inscritos'] ?? 0) ?><br>
                    <span style="color:#777; font-size:8px;">cap. <?= intval($c['max_participantes'] ?? 0) ?></span>
                </td>
                <td style="text-align: center;">
                    <?= intval($c['aprobados'] ?? 0) ?>
                </td>
                <td><?= htmlspecialchars($c['nombre_ambito'] ?? '—') ?></td>
                <td>
                    <span class="badge" style="background-color: <?= $estColor ?>; font-size: 8px;">
                        <?= htmlspecialchars($est) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 30px; border: 1px dashed #ccc; color: #777;">
        No se encontraron cursos con los filtros aplicados.
    </div>
    <?php endif; ?>


    <!-- SECCIÓN 7: ANÁLISIS ESTADÍSTICO -->
    <div class="section-title">
        <?= ($fAnio !== 'todos' || $fEstatus !== 'todos' || $fAmbito !== 'todos') ? '7.' : '6.' ?> Análisis Estadístico
    </div>
    
    <table class="grid-2">
        <tr>
            <td>
                <table class="stats-table">
                    <tr><td class="stats-label">Total de Cursos en Período</td><td class="stats-value"><?= $stats['totalCursos'] ?></td></tr>
                    <tr><td class="stats-label">Cursos Culminados</td><td class="stats-value" style="color:#198754;"><?= $stats['culminados'] ?></td></tr>
                    <tr><td class="stats-label">Cursos En Proceso</td><td class="stats-value" style="color:#0d6efd;"><?= $stats['enProceso'] ?></td></tr>
                    <tr><td class="stats-label">Cursos Cancelados</td><td class="stats-value" style="color:#dc3545;"><?= $stats['cancelados'] ?></td></tr>
                    <tr><td class="stats-label">Total Horas Asignadas</td><td class="stats-value"><?= round($stats['horasAsignadas']) ?>h</td></tr>
                    <tr><td class="stats-label">Total Horas Completadas</td><td class="stats-value"><?= round($stats['horasCompletadas']) ?>h</td></tr>
                </table>
            </td>
            <td>
                <table class="stats-table">
                    <tr><td class="stats-label">Total de Participantes</td><td class="stats-value"><?= $stats['totalParticipantes'] ?></td></tr>
                    <tr><td class="stats-label">Promedio de Participantes / Curso</td><td class="stats-value"><?= $stats['promedioParticipantes'] ?></td></tr>
                    <tr><td class="stats-label">Tasa Global de Aprobación</td><td class="stats-value"><?= $stats['tasaAprobacion'] ?>%</td></tr>
                    <tr><td class="stats-label">Ámbito con Mayor Frecuencia</td><td class="stats-value"><?= htmlspecialchars($stats['ambitoMayor']) ?></td></tr>
                    <tr><td class="stats-label">Año de Mayor Actividad</td><td class="stats-value"><?= htmlspecialchars($stats['anioMayor']) ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

</main>

</body>
</html>

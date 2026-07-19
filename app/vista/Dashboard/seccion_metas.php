<div class="dashboard-section" id="section-metas">

<?php
$ambitoPaleta = ['#3b82f6','#8b5cf6','#ef4444','#f59e0b','#10b981','#06b6d4','#f97316','#ec4899'];

function _semColor(float $p): array {
    if ($p >= 95) return ['#28c76f', 'verde'];
    if ($p >= 80) return ['#f59e0b', 'amarillo'];
    if ($p >= 50) return ['#f97316', 'naranja'];
    return ['#ef4444', 'rojo'];
}

function _semLabel(float $p): string {
    if ($p >= 95) return 'Excelente';
    if ($p >= 80) return 'Favorable';
    if ($p >= 50) return 'En progreso';
    return 'Atención';
}

function _kpiColors(float $p): array {
    if ($p >= 95) return ['#28c76f', '#e6f7ed', 'verde'];
    if ($p >= 80) return ['#f59e0b', '#fef8e6', 'amarillo'];
    if ($p >= 50) return ['#f97316', '#fff1e6', 'naranja'];
    return ['#ef4444', '#fdf2f2', 'rojo'];
}

$resSemAmbitos = IndicadorHelper::evaluarSemaforo('US_CUMP_METAS', $tasaCumplimientoAmbitos);
$semAmbitos = $resSemAmbitos['alerta'];
$colorAmbitos = $semAmbitos === 'verde' ? '#28c76f' : ($semAmbitos === 'amarillo' ? '#f59e0b' : '#ef4444');
$bgAmbitos = $semAmbitos === 'verde' ? '#e6f7ed' : ($semAmbitos === 'amarillo' ? '#fef8e6' : '#fdf2f2');

$resSemAnual = IndicadorHelper::evaluarSemaforo('AC_OBJ_PARTICIPANTES_ANUAL', $tasaMetaAnual);
$semAnual = $resSemAnual['alerta'];
$colorAnual = $semAnual === 'verde' ? '#28c76f' : ($semAnual === 'amarillo' ? '#f59e0b' : '#ef4444');
$bgAnual = $semAnual === 'verde' ? '#e6f7ed' : ($semAnual === 'amarillo' ? '#fef8e6' : '#fdf2f2');

$pctA = (float)($progresoAnual['pct_global']  ?? 0);
$totA = (int)  ($progresoAnual['total_metas'] ?? 0);
$cumA = (int)  ($progresoAnual['cumplidas']    ?? 0);
$actA = (int)  ($progresoAnual['en_progreso']  ?? 0);
$ncA  = (int)  ($progresoAnual['no_cumplidas'] ?? 0);
$objA = (int)  ($progresoAnual['obj_total']    ?? 0);
$rlA  = (int)  ($progresoAnual['real_total']   ?? 0);
[$cA, $sA] = _semColor($pctA);

// KPI Card 1 Data
$c1_empty = ($tasaCumplimientoAmbitos <= 0.0);
if ($c1_empty) {
    $c1_color = '#e2e8f0'; $c1_bg = '#f8fafc'; $c1_icon_color = '#94a3b8';
    $c1_val = '0.0%'; $c1_sub = 'Sin datos registrados'; $c1_prog = 0; $c1_bar = '#e2e8f0';
} else {
    $c1_color = $colorAmbitos; $c1_bg = $bgAmbitos; $c1_icon_color = $colorAmbitos;
    $c1_val = number_format($tasaCumplimientoAmbitos, 1) . '%';
    $c1_sub = 'Promedio de metas activas con ámbito asignado';
    $c1_prog = min($tasaCumplimientoAmbitos, 100); $c1_bar = $colorAmbitos;
}

// KPI Card 2 Data
$c2_empty = ($tasaMetaAnual <= 0.0);
if ($c2_empty) {
    $c2_color = '#e2e8f0'; $c2_bg = '#f8fafc'; $c2_icon_color = '#94a3b8';
    $c2_val = '0.0%'; $c2_sub = 'Sin datos registrados'; $c2_prog = 0; $c2_bar = '#e2e8f0';
} else {
    $c2_color = $colorAnual; $c2_bg = $bgAnual; $c2_icon_color = $colorAnual;
    $c2_val = number_format($tasaMetaAnual, 1) . '%';
    $c2_sub = 'Logrado: <strong>' . number_format($metaAnualReal) . '</strong> / ' . number_format($metaAnualObj);
    $c2_prog = min($tasaMetaAnual, 100); $c2_bar = $colorAnual;
}

// KPI Card 3 Data
$c3_total = (int)($kMetas['total_metas'] ?? 0);
$c3_cum = (int)($kMetas['cumplidas'] ?? 0);
$c3_prog_count = (int)($kMetas['en_progreso'] ?? 0);
$c3_pct = $c3_total > 0 ? ($c3_cum / $c3_total) * 100 : 0.0;
$c3_empty = ($c3_total <= 0);
if ($c3_empty) {
    $c3_color = '#e2e8f0'; $c3_bg = '#f8fafc'; $c3_icon_color = '#94a3b8';
    $c3_val = '0 metas'; $c3_sub = 'Sin datos registrados'; $c3_prog = 0; $c3_bar = '#e2e8f0';
} else {
    [$c3_color, $c3_bg, $c3_sem] = _kpiColors($c3_pct);
    $c3_icon_color = $c3_color;
    $c3_val = $c3_total . ' metas';
    $c3_sub = $c3_cum . ' cumplidas · ' . $c3_prog_count . ' en progreso';
    $c3_prog = $c3_pct; $c3_bar = $c3_color;
}

// / KPI Card 4 Data
$c4_val_raw = min((float)($kMetas['promedio_pct'] ?? 0.0), 100.0);
$c4_empty = ($c4_val_raw <= 0.0);
if ($c4_empty) {
    $c4_color = '#e2e8f0'; $c4_bg = '#f8fafc'; $c4_icon_color = '#94a3b8';
    $c4_val = '0.0%'; $c4_sub = 'Sin datos registrados'; $c4_prog = 0; $c4_bar = '#e2e8f0';
} else {
    [$c4_color, $c4_bg, $c4_sem] = _kpiColors($c4_val_raw);
    $c4_icon_color = $c4_color;
    $c4_val = number_format($c4_val_raw, 1) . '%';
    $c4_label = _semLabel($c4_val_raw);
    $c4_sub = '<span class="metas-badge-status semaforo-' . $c4_sem . '">' . $c4_label . '</span>';
    $c4_prog = min($c4_val_raw, 100); $c4_bar = $c4_color;
}
?>

    <!-- BLOQUE 1: KPIs Principales -->
    <div class="metas-kpi-grid">
        <!-- Tarjeta 1: Tasa Cumplimiento por Ámbitos -->
        <div class="metas-kpi-card" style="border-left: 4px solid <?php echo $c1_color; ?>;">
            <div class="metas-kpi-info-btn" role="button" aria-label="Información sobre este indicador" tabindex="0">
                !
                <span class="metas-tooltip-text">Muestra el porcentaje promedio de avance de todas las metas de formación activas que tienen asignado un ámbito (sector o dirección).</span>
            </div>
            <div class="metas-kpi-top">
                <div class="metas-kpi-icon-wrap" style="background: <?php echo $c1_bg; ?>; color: <?php echo $c1_icon_color; ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.742c-.934-.53-2.118-.6-3.213-.492-1.18.118-2.368.46-3.287.81zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                    </svg>
                </div>
                <div class="metas-kpi-text">
                    <span class="metas-kpi-val"><?php echo $c1_val; ?></span>
                    <span class="metas-kpi-lbl">Tasa Cumplimiento Ámbitos</span>
                    <span class="metas-kpi-sub"><?php echo $c1_sub; ?></span>
                </div>
            </div>
            <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $c1_prog; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 6px;">
                <div class="metas-progress-fill" style="width: <?php echo $c1_prog; ?>%; background-color: <?php echo $c1_bar; ?>;"></div>
            </div>
        </div>

        <!-- Tarjeta 2: Cumplimiento Participantes Anuales -->
        <div class="metas-kpi-card" style="border-left: 4px solid <?php echo $c2_color; ?>;">
            <div class="metas-kpi-info-btn" role="button" aria-label="Información sobre este indicador" tabindex="0">
                !
                <span class="metas-tooltip-text">Representa el progreso acumulado en la formación de estudiantes/participantes frente al objetivo anual global establecido.</span>
            </div>
            <div class="metas-kpi-top">
                <div class="metas-kpi-icon-wrap" style="background: <?php echo $c2_bg; ?>; color: <?php echo $c2_icon_color; ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </div>
                <div class="metas-kpi-text">
                    <span class="metas-kpi-val"><?php echo $c2_val; ?></span>
                    <span class="metas-kpi-lbl">Cumplimiento Participantes</span>
                    <span class="metas-kpi-sub"><?php echo $c2_sub; ?></span>
                </div>
            </div>
            <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $c2_prog; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 6px;">
                <div class="metas-progress-fill" style="width: <?php echo $c2_prog; ?>%; background-color: <?php echo $c2_bar; ?>;"></div>
            </div>
        </div>

        <!-- Tarjeta 3: Total Metas Activas -->
        <div class="metas-kpi-card" style="border-left: 4px solid <?php echo $c3_color; ?>;">
            <div class="metas-kpi-info-btn" role="button" aria-label="Información sobre este indicador" tabindex="0">
                !
                <span class="metas-tooltip-text">Indica la cantidad total de metas de capacitación que están vigentes en el sistema para este período, detallando cuántas ya fueron cumplidas o están en progreso.</span>
            </div>
            <div class="metas-kpi-top">
                <div class="metas-kpi-icon-wrap" style="background: <?php echo $c3_bg; ?>; color: <?php echo $c3_icon_color; ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 1.5 2z"/>
                        <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.085a.5.5 0 0 1 0 .707l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.085a.5.5 0 0 1 0 .707l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </div>
                <div class="metas-kpi-text">
                    <span class="metas-kpi-val"><?php echo $c3_val; ?></span>
                    <span class="metas-kpi-lbl">Total Metas Activas</span>
                    <span class="metas-kpi-sub"><?php echo $c3_sub; ?></span>
                </div>
            </div>
            <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $c3_prog; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 6px;">
                <div class="metas-progress-fill" style="width: <?php echo $c3_prog; ?>%; background-color: <?php echo $c3_bar; ?>;"></div>
            </div>
        </div>

        <!-- Tarjeta 4: Promedio Global de Cumplimiento -->
        <div class="metas-kpi-card" style="border-left: 4px solid <?php echo $c4_color; ?>;">
            <div class="metas-kpi-info-btn" role="button" aria-label="Información sobre este indicador" tabindex="0">
                !
                <span class="metas-tooltip-text">Es la media aritmética de avance porcentual de todas las metas vigentes. Clasifica el estado general mediante un semáforo de desempeño.</span>
            </div>
            <div class="metas-kpi-top">
                <div class="metas-kpi-icon-wrap" style="background: <?php echo $c4_bg; ?>; color: <?php echo $c4_icon_color; ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m-1.914-2.354a.5.5 0 0 1 0-.708l.914-.914a.5.5 0 1 1 .708.708l-.914.914a.5.5 0 0 1-.707 0M8 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6m0 1a4 4 0 1 1 0-8 4 4 0 0 1 0 8"/>
                    </svg>
                </div>
                <div class="metas-kpi-text">
                    <span class="metas-kpi-val"><?php echo $c4_val; ?></span>
                    <span class="metas-kpi-lbl">Promedio Global</span>
                    <span class="metas-kpi-sub"><?php echo $c4_sub; ?></span>
                </div>
            </div>
            <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $c4_prog; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 6px;">
                <div class="metas-progress-fill" style="width: <?php echo $c4_prog; ?>%; background-color: <?php echo $c4_bar; ?>;"></div>
            </div>
        </div>
    </div>

    <!-- BLOQUE 2: Progreso Global Anual -->
    <div class="metas-card">
        <div class="metas-progreso-global-body">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="<?php echo $cA; ?>" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                    <span class="fw-bold" style="font-size: 1rem; color: #1e293b;">Progreso Global de Metas — <?php echo $anioActual; ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="metas-badge-status semaforo-<?php echo $sA; ?>" style="font-size: 0.8rem;">
                        <?php echo $pctA; ?>% · <?php echo _semLabel($pctA); ?>
                    </span>
                    <button class="metas-ver-ambito-btn" id="btn-toggle-ambitos-a" aria-expanded="false" aria-controls="metas-desglose-a">
                        <span class="metas-btn-texto">Ver por ámbito</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="metas-toggle-chevron" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <p style="font-size: 0.8rem; color: #64748b; margin: 0;">
                <strong style="color: #1e293b;"><?php echo $totA; ?></strong> metas en el año &nbsp;·&nbsp; 
                <strong style="color: #28c76f;"><?php echo $cumA; ?></strong> cumplidas &nbsp;·&nbsp; 
                <strong style="color: #3b82f6;"><?php echo $actA; ?></strong> activas 
                <?php if ($ncA > 0): ?>&nbsp;·&nbsp; <span style="color: #ef4444; font-weight: 600;"><?php echo $ncA; ?> no cumplidas</span><?php endif; ?>
                &nbsp;·&nbsp; <strong><?php echo number_format($rlA); ?></strong> / <?php echo number_format($objA); ?> objetivo
            </p>

            <div class="metas-barra-global-wrap" role="progressbar" aria-valuenow="<?php echo $pctA; ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="metas-barra-global-fill" style="width: <?php echo min($pctA, 100); ?>%; background-color: <?php echo $cA; ?>;">
                    <?php if ($pctA > 10): ?>
                        <span class="metas-barra-global-fill-label"><?php echo $pctA; ?>%</span>
                    <?php endif; ?>
                </div>
                <?php if ($pctA <= 10): ?>
                    <span class="metas-barra-global-ext-label" style="left: <?php echo min($pctA, 100); ?>%;"><?php echo $pctA; ?>%</span>
                <?php endif; ?>
            </div>
            
            <div class="metas-escala">
                <span>0%</span>
                <span>25%</span>
                <span>50%</span>
                <span>75%</span>
                <span>100%</span>
            </div>

            <!-- Panel colapsable de ámbitos -->
            <div class="metas-ambitos-desglose" id="metas-desglose-a" style="display: none;" aria-hidden="true">
                <div style="border-top: 1px dashed #e2e8f0; margin-top: 16px; padding-top: 16px;">
                    <?php if (empty($progresoAnualPorAmbito)): ?>
                        <p class="text-center text-muted py-4" style="font-size: 0.82rem; margin: 0;">Sin datos de ámbitos para <?php echo $anioActual; ?>.</p>
                    <?php else: ?>
                        <div class="metas-ambitos-grid">
                            <?php foreach ($progresoAnualPorAmbito as $pa):
                                [$cPa, $sPa] = _semColor((float)$pa['pct_ambito']);
                            ?>
                                <div class="metas-card" style="padding: 14px; background-color: #f8fafc; border-color: #e2e8f0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <span class="fw-bold" style="font-size: 0.82rem; color: #1e293b;"><?php echo htmlspecialchars($pa['ambito']); ?></span>
                                        <span class="metas-badge-status semaforo-<?php echo $sPa; ?>" style="font-size: 0.65rem;"><?php echo $pa['pct_ambito']; ?>%</span>
                                    </div>
                                    <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $pa['pct_ambito']; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 8px; margin-bottom: 8px;">
                                        <div class="metas-progress-fill" style="width: <?php echo min((float)$pa['pct_ambito'], 100); ?>%; background-color: <?php echo $cPa; ?>;"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.72rem; color: #64748b; font-weight: 500;">
                                        <span><?php echo number_format($pa['real_total']); ?> / <?php echo number_format($pa['obj_total']); ?></span>
                                        <span><?php echo $pa['total_metas']; ?> meta<?php echo $pa['total_metas'] != 1 ? 's' : ''; ?> · <?php echo $pa['cumplidas']; ?> ✓</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- BLOQUE 3: Avance por Meta -->
    <div class="metas-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#64748b" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5"/>
                </svg>
                <span class="fw-bold" style="font-size: 0.9rem; color: #1e293b;">Avance por Meta — <?php echo $anioActual; ?></span>
            </div>
            <span id="metas-avances-count" style="font-size: 0.72rem; color: #475569; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2px 10px; font-weight: 600;">
                <?php echo count($metasAnioDetalle); ?> meta<?php echo count($metasAnioDetalle) != 1 ? 's' : ''; ?>
            </span>
        </div>
        <p style="font-size: 0.78rem; color: #64748b; margin-top: -4px; margin-bottom: 16px; line-height: 1.4;">
            Tarjetas de avance por meta. Utiliza la barra de filtros a continuación para buscar por período específico (Trimestre/Semestre) o Ámbito asignado.
        </p>

        <!-- Filtros compactos -->
        <div class="metas-toolbar">
            <div class="metas-filtro-grupo">
                <span class="metas-filtro-label">Período</span>
                <div class="metas-filtro-btns" id="filtro-tiempo">
                    <button class="metas-filtro-btn active" data-tiempo="todos" aria-label="Filtrar por período: Todo">Todo</button>
                    <button class="metas-filtro-btn" data-tiempo="1" aria-label="Filtrar por período: Ene–Mar">Ene–Mar</button>
                    <button class="metas-filtro-btn" data-tiempo="2" aria-label="Filtrar por período: Abr–Jun">Abr–Jun</button>
                    <button class="metas-filtro-btn" data-tiempo="3" aria-label="Filtrar por período: Jul–Sep">Jul–Sep</button>
                    <button class="metas-filtro-btn" data-tiempo="4" aria-label="Filtrar por período: Oct–Dic">Oct–Dic</button>
                    <button class="metas-filtro-btn" data-tiempo="s1" aria-label="Filtrar por período: Sem I">Sem I</button>
                    <button class="metas-filtro-btn" data-tiempo="s2" aria-label="Filtrar por período: Sem II">Sem II</button>
                </div>
            </div>
            <div class="metas-filtro-grupo">
                <span class="metas-filtro-label">Ámbito</span>
                <div class="metas-filtro-btns" id="filtro-ambito">
                    <button class="metas-filtro-btn active" data-ambito="todos" aria-label="Filtrar por ámbito: Todos">Todos</button>
                    <?php
                    $ambitosEnMetas = [];
                    foreach ($metasAnioDetalle as $md) {
                        $aid = (int)$md['id_ambito'];
                        if (!isset($ambitosEnMetas[$aid])) {
                            $ambitosEnMetas[$aid] = htmlspecialchars($md['nombre_ambito']);
                        }
                    }
                    foreach ($ambitosEnMetas as $aid => $anombre): ?>
                        <button class="metas-filtro-btn" data-ambito="<?php echo $aid; ?>" aria-label="Filtrar por ámbito: <?php echo $anombre; ?>"><?php echo $anombre; ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Grid de Cards -->
        <?php if (empty($metasAnioDetalle)): ?>
            <div class="text-center py-5 text-muted" style="font-size: 0.85rem;">
                No hay metas registradas para <?php echo $anioActual; ?>.
            </div>
        <?php else: ?>
            <div class="metas-avances-grid" id="metas-avances-grid">
                <?php foreach ($metasAnioDetalle as $ma):
                    [$cMa, $sMa] = _semColor((float)$ma['porcentaje_cumplimiento']);
                    $pMa    = min((float)$ma['porcentaje_cumplimiento'], 100);
                    $cumMa  = ($ma['estado_cumplimiento'] === 'Cumplida');
                    $colAmb = $ambitoPaleta[(int)$ma['id_ambito'] % count($ambitoPaleta)];
                    $fechaI = !empty($ma['fecha_inicio']) ? date('d/m/Y', strtotime($ma['fecha_inicio'])) : '—';
                    $fechaF = !empty($ma['fecha_fin'])    ? date('d/m/Y', strtotime($ma['fecha_fin']))    : '—';
                ?>
                    <div class="metas-avance-card"
                         data-ambito="<?php echo (int)$ma['id_ambito']; ?>"
                         data-trimestre="<?php echo (int)$ma['trimestre_inicio']; ?>"
                         data-mes="<?php echo (int)$ma['mes_inicio']; ?>">
                        <div class="metas-avance-card-header" style="background: <?php echo $colAmb; ?>26; border-bottom: 1px solid <?php echo $colAmb; ?>33;">
                            <span class="metas-avance-card-ambito fw-bold" style="color: <?php echo $colAmb; ?>;">
                                <?php echo htmlspecialchars($ma['nombre_ambito']); ?>
                            </span>
                            <span class="metas-badge-status semaforo-<?php echo $cumMa ? 'verde' : $sMa; ?>">
                                <?php echo $cumMa ? '✓ Cumplida' : htmlspecialchars($ma['estado_cumplimiento']); ?>
                            </span>
                        </div>
                        <div class="metas-avance-card-body">
                            <div class="metas-avance-tipo">
                                <span class="fw-bold" style="color: #475569;"><?php echo htmlspecialchars($ma['tipo_objetivo']); ?></span>
                                <?php if (!empty($ma['periodo_tiempo'])): ?>
                                    <span style="font-size: 0.7rem; color: #64748b; background: #f1f5f9; padding: 1px 6px; border-radius: 4px;"><?php echo htmlspecialchars($ma['periodo_tiempo']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="metas-avance-cifras">
                                <div class="metas-avance-cifra">
                                    <span class="metas-avance-cifra-val" style="color: <?php echo $cMa; ?>;"><?php echo number_format($pMa, 1); ?>%</span>
                                    <span class="metas-avance-cifra-lbl">cumplimiento</span>
                                </div>
                                <div class="metas-avance-cifra">
                                    <span class="metas-avance-cifra-val"><?php echo number_format((int)$ma['estudiantes_formados']); ?></span>
                                    <span class="metas-avance-cifra-lbl">logrado</span>
                                </div>
                                <div class="metas-avance-cifra">
                                    <span class="metas-avance-cifra-val"><?php echo number_format((int)$ma['meta_objetivo']); ?></span>
                                    <span class="metas-avance-cifra-lbl">objetivo</span>
                                </div>
                            </div>
                            
                            <div class="metas-progress-track" role="progressbar" aria-valuenow="<?php echo $pMa; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 7px; margin: 4px 0;">
                                <div class="metas-progress-fill" style="width: <?php echo $pMa; ?>%; background-color: <?php echo $cMa; ?>;"></div>
                            </div>
                            
                            <div class="metas-avance-fechas">
                                <span><?php echo $fechaI; ?></span>
                                <span class="metas-fecha-separator">→</span>
                                <span><?php echo $fechaF; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div id="metas-avances-empty" style="display: none;" class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#ced4da" class="bi bi-x-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                </svg>
                <p class="mt-2" style="font-size: 0.82rem;">Sin metas para el filtro seleccionado.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- BLOQUE 4: Progresión de Metas Activas (Tabs y Gráficos) -->
    <div class="metas-card">
        <div class="metas-card-header" style="border-bottom: none; margin-bottom: 12px; padding-bottom: 0;">
            <div style="display: flex; flex-direction: column; gap: 4px; width: 100%;">
                <span class="metas-card-title" style="color: #ef4444; font-size: 1.05rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                    Progresión de Metas Activas
                </span>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                    <span><strong style="color: #1e293b;"><?php echo (int)($kMetas['total_metas'] ?? 0); ?></strong> activas</span>
                    <span>·</span>
                    <span><strong style="color: #28c76f;"><?php echo (int)($kMetas['cumplidas'] ?? 0); ?></strong> cumplidas</span>
                    <span>·</span>
                    <span class="metas-badge-status semaforo-<?php echo $s_meta; ?>"><?php echo $kMetas['promedio_pct'] ?? 0; ?>% promedio · <?php echo semL($s_meta); ?></span>
                    <span>·</span>
                    <span>año <?php echo $anioActual; ?></span>
                </div>
            </div>
        </div>

        <div class="chart-tabs-wrapper">
            <nav role="tablist" class="chart-tabs-nav" id="chart-nav-metas" aria-label="Vistas de progresión de metas">
                <button role="tab" class="chart-tab-btn active" aria-selected="true" aria-controls="panel-ambito" id="tab-ambito" data-section="metas" data-chart="ambito">Cumplimiento por Ámbito</button>
                <button role="tab" class="chart-tab-btn" aria-selected="false" aria-controls="panel-mes" id="tab-mes" data-section="metas" data-chart="mes">Metas por Mes</button>
            </nav>

            <div class="chart-tabs-body">
                <!-- Tab Panel 1: Cumplimiento por Ámbito -->
                <div role="tabpanel" id="panel-ambito" aria-labelledby="tab-ambito">
                    <div id="chart-panel-metas-ambito" class="chart-tab-panel active">
                        <p class="chart-tab-panel-title">
                        Promedio de cumplimiento por ámbito
                        <span class="chart-tab-panel-hint">ⓘ Clic en una fila para filtrar el detalle</span>
                    </p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap">
                            <canvas id="chartCumplimientoAmbitoTab"></canvas>
                        </div>
                        <div class="chart-tab-side-table">
                            <table class="metas-table" id="tabla-tab-ambito">
                                <thead>
                                    <tr>
                                        <th>Ámbito</th>
                                        <th style="text-align: center;">Metas</th>
                                        <th style="text-align: center;">Cumplidas</th>
                                        <th style="text-align: center;">%</th>
                                        <th class="tabla-tab-accion-th"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ambitoNombreAId = [];
                                foreach ($metasAnioDetalle as $md) {
                                    $ambitoNombreAId[strtolower(trim($md['nombre_ambito']))] = (int)$md['id_ambito'];
                                }
                                foreach ($cumplimientoAmbito as $amb):
                                    [$aC2,$sC2] = _semColor((float)$amb['pct_promedio']);
                                    $ambitoIdParaFiltro = $ambitoNombreAId[strtolower(trim($amb['ambito']))] ?? 'todos';
                                ?>
                                    <tr class="tabla-tab-row tabla-tab-row--ambito"
                                        data-filtro-ambito="<?php echo $ambitoIdParaFiltro; ?>"
                                        data-nombre-ambito="<?php echo htmlspecialchars($amb['ambito']); ?>">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                        <td style="text-align: center;"><?php echo $amb['total_metas']; ?></td>
                                        <td style="text-align: center; color: #28c76f; font-weight: 600;"><?php echo $amb['cumplidas']; ?></td>
                                        <td style="text-align: center;">
                                            <span class="metas-badge-status semaforo-<?php echo $sC2; ?>"><?php echo $amb['pct_promedio']; ?>%</span>
                                        </td>
                                        <td class="tabla-tab-accion-td">
                                            <button class="tabla-tab-filtrar-btn" aria-label="Filtrar por ámbito: <?php echo htmlspecialchars($amb['ambito']); ?>">Filtrar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($cumplimientoAmbito)): ?>
                                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Sin metas activas.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Tab Panel 2: Metas por Mes -->
                <div role="tabpanel" id="panel-mes" aria-labelledby="tab-mes">
                    <div id="chart-panel-metas-mes" class="chart-tab-panel">
                        <p class="chart-tab-panel-title">
                        Metas activas por mes — año <?php echo $anioActual; ?>
                        <span class="chart-tab-panel-hint">ⓘ Clic en una fila para filtrar el detalle</span>
                    </p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap">
                            <canvas id="chartMetasMes"></canvas>
                        </div>
                        <div class="chart-tab-side-table">
                            <table class="metas-table" id="tabla-tab-mes">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th style="text-align: center;">Metas</th>
                                        <th>Progreso</th>
                                        <th class="tabla-tab-accion-th"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $mesesNombres = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                $maxMetaMes   = max(array_merge($metasPorMes, [1]));
                                $mesActualIdx = (int)date('n') - 1;
                                foreach ($metasPorMes as $mi => $mv):
                                    $mBar     = $maxMetaMes > 0 ? round($mv/$maxMetaMes*100) : 0;
                                    $esActual = ($mi === $mesActualIdx);
                                    $numMes   = $mi + 1;
                                    $hasMetas = ($mv > 0);
                                    $rowClass = "tabla-tab-row tabla-tab-row--mes";
                                    if (!$hasMetas) {
                                        $rowClass .= " tabla-tab-row--disabled";
                                    }
                                    $esActualStyle = $esActual ? 'style="background-color: rgba(239, 68, 68, 0.05); color: #ef4444;"' : '';
                                ?>
                                    <tr class="<?php echo $rowClass; ?>"
                                        data-filtro-mes="<?php echo $numMes; ?>"
                                        data-nombre-mes="<?php echo $mesesNombres[$mi]; ?>"
                                        <?php echo $esActualStyle; ?>>
                                        <td class="fw-semibold" style="<?php echo $esActual ? 'color: #ef4444;' : ''; ?>">
                                            <?php echo $mesesNombres[$mi]; ?>
                                            <?php if ($esActual): ?><span class="metas-badge-hoy">hoy</span><?php endif; ?>
                                        </td>
                                        <td style="text-align: center; font-weight: 700; color: <?php echo $mv > 0 ? '#ef4444' : '#94a3b8'; ?>;"><?php echo $mv; ?></td>
                                        <td>
                                            <div class="metas-progress-track" style="height: 6px;">
                                                <div class="metas-progress-fill" style="width: <?php echo $mBar; ?>%; height: 6px; background-color: #ef4444; opacity: <?php echo $mv > 0 ? '1' : '0.3'; ?>;"></div>
                                            </div>
                                        </td>
                                        <td class="tabla-tab-accion-td">
                                            <?php if ($hasMetas): ?>
                                                <button class="tabla-tab-filtrar-btn" aria-label="Filtrar por mes: <?php echo $mesesNombres[$mi]; ?>">Filtrar</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- BLOQUE 5: Detalle de Metas (Tabla Inferior) -->
    <div class="tables-container">
        <div class="tables-container-header">
            <span class="metas-dot semaforo-fill-rojo" style="margin-right: 2px;"></span>
            <span class="fw-bold" style="font-size: 0.95rem;">Detalle de Metas</span>
            
            <!-- Badge flotante de filtro activo -->
            <span id="detalle-filtro-activo-badge">
                🔽 <span id="detalle-filtro-activo-texto">—</span>
                <button id="btn-detalle-limpiar-filtro" aria-label="Limpiar filtro activo">✕</button>
            </span>
            
            <span style="font-size: 0.72rem; color: #94a3b8; margin-left: auto;">≥95% verde · ≥80% amarillo · ≥50% naranja · &lt;50% rojo</span>
        </div>

        <?php if (empty($metasDetalle)): ?>
            <div class="text-center py-5 text-muted">
                <p>No hay metas activas registradas.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="metas-table" id="tabla-metas-panel">
                    <thead>
                        <tr>
                            <th>Ámbito</th>
                            <th style="text-align: center;">Tipo</th>
                            <th style="text-align: center;">Objetivo</th>
                            <th style="min-width: 200px;">Progreso</th>
                            <th style="text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($metasDetalle as $m):
                        $mPct    = $m['porcentaje_cumplimiento'] ?? 0;
                        [,$mSem] = _semColor((float)$mPct);
                        $cumplida = ($m['estado_cumplimiento'] === 'Cumplida');
                        $mesMeta  = isset($m['mes_meta']) && $m['mes_meta'] !== null ? (int)$m['mes_meta'] : 0;
                        $ambitoId = isset($m['id_ambito']) ? (int)$m['id_ambito'] : 0;
                    ?>
                        <tr data-mes-meta="<?php echo $mesMeta; ?>"
                            data-ambito-id="<?php echo $ambitoId; ?>"
                            data-id="<?php echo $m['id_meta']; ?>"
                            class="metas-full-row meta-row-link" style="cursor: pointer;">
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <span class="metas-dot semaforo-fill-<?php echo $mSem; ?>"></span>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($m['nombre_ambito'] ?? 'General'); ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 0.72rem; color: #475569; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                    <?php echo htmlspecialchars($m['tipo_objetivo']); ?>
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: 700; font-size: 0.9rem;"><?php echo number_format($m['meta_objetivo']); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="metas-progress-track" style="height: 6px; flex-grow: 1;">
                                        <div class="metas-progress-fill semaforo-fill-<?php echo $mSem; ?>" style="width: <?php echo min($mPct, 100); ?>%;"></div>
                                    </div>
                                    <span class="fw-semibold semaforo-text-<?php echo $mSem; ?>" style="font-size: 0.75rem; min-width: 45px; text-align: right;"><?php echo min($mPct, 100); ?>%</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="metas-badge-status semaforo-<?php echo $cumplida ? 'verde' : $mSem; ?>">
                                    <?php echo $cumplida ? '✓ ' : ''; ?><?php echo $m['estado_cumplimiento']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div id="metas-sin-datos-mes" class="text-center py-5 text-muted" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                    <p class="mt-2" style="font-size: 0.85rem;">No hay metas registradas para este filtro.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

#section-metas {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  color: #1e293b;
  background-color: #ffffff;
  display: none;
  flex-direction: column;
  gap: 24px;
}

#section-metas.active {
  display: flex;
}

#section-metas * {
  box-sizing: border-box;
}

/* Responsive grids */
.metas-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 32px;
  margin-bottom: 8px;
}

.metas-ambitos-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.metas-avances-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

/* Base Card */
.metas-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
  padding: 20px;
}

.metas-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.metas-card-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* KPI Card */
.metas-kpi-card {
  position: relative;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
  padding: 16px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.metas-kpi-info-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background-color: #f1f5f9;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  cursor: help;
  transition: all 0.2s ease;
  border: 1px solid #e2e8f0;
  user-select: none;
  z-index: 10;
}

.metas-kpi-info-btn:hover {
  background-color: #ef4444;
  color: #ffffff;
  border-color: #ef4444;
}

/* Tooltip text wrapper */
.metas-tooltip-text {
  visibility: hidden;
  width: 220px;
  background-color: #1e293b;
  color: #ffffff;
  text-align: left;
  border-radius: 6px;
  padding: 8px 12px;
  position: absolute;
  z-index: 1000;
  top: calc(100% + 8px);
  right: -6px; /* align to the right side of the button */
  opacity: 0;
  transition: opacity 0.2s, transform 0.2s;
  transform: translateY(-4px);
  font-size: 0.72rem;
  font-weight: 500;
  line-height: 1.4;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  pointer-events: none;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  text-transform: none;
}

/* Tooltip arrow */
.metas-tooltip-text::after {
  content: "";
  position: absolute;
  bottom: 100%;
  right: 10px;
  border-width: 6px;
  border-style: solid;
  border-color: transparent transparent #1e293b transparent;
}

/* Show the tooltip text when hovering */
.metas-kpi-info-btn:hover .metas-tooltip-text {
  visibility: visible;
  opacity: 1;
  transform: translateY(0);
}

.metas-kpi-top {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.metas-kpi-icon-wrap {
  width: 42px;
  height: 42px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.metas-kpi-text {
  display: flex;
  flex-direction: column;
}

.metas-kpi-val {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.1;
}

.metas-kpi-lbl {
  font-size: 0.68rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-top: 4px;
}

.metas-kpi-sub {
  font-size: 0.72rem;
  color: #94a3b8;
  margin-top: 4px;
  line-height: 1.3;
}

/* Progress bar global styling */
.metas-progress-track {
  width: 100%;
  background-color: #f1f5f9;
  border-radius: 4px;
  overflow: hidden;
}

.metas-progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Status Badges */
.metas-badge-status {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  font-size: 0.68rem;
  font-weight: 700;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.metas-badge-hoy {
  background-color: #fee2e2;
  color: #ef4444;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 4px;
  margin-left: 6px;
  display: inline-flex;
  align-items: center;
}

/* Semaforo classes definition */
.semaforo-verde { background-color: #e6f7ed; color: #28c76f; border: 1px solid rgba(40,199,111,0.25); }
.semaforo-amarillo { background-color: #fef8e6; color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
.semaforo-naranja { background-color: #fff1e6; color: #f97316; border: 1px solid rgba(249,115,22,0.25); }
.semaforo-rojo { background-color: #fdf2f2; color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }

.semaforo-fill-verde { background-color: #28c76f; }
.semaforo-fill-amarillo { background-color: #f59e0b; }
.semaforo-fill-naranja { background-color: #f97316; }
.semaforo-fill-rojo { background-color: #ef4444; }

.semaforo-text-verde { color: #28c76f; }
.semaforo-text-amarillo { color: #f59e0b; }
.semaforo-text-naranja { color: #f97316; }
.semaforo-text-rojo { color: #ef4444; }

/* Bloque 2 Progreso Global */
.metas-progreso-global-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.metas-barra-global-wrap {
  position: relative;
  height: 24px;
  background-color: #f1f5f9;
  border-radius: 12px;
  overflow: hidden;
  width: 100%;
}

.metas-barra-global-fill {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 12px;
  border-radius: 12px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.metas-barra-global-fill-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: #ffffff;
}

.metas-barra-global-ext-label {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.72rem;
  font-weight: 700;
  color: #475569;
  padding-left: 8px;
}

.metas-escala {
  display: flex;
  justify-content: space-between;
  font-size: 0.7rem;
  color: #94a3b8;
  padding: 0 4px;
  margin-top: -6px;
}

.metas-ver-ambito-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.76rem;
  font-weight: 600;
  color: #475569;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 5px 10px;
  cursor: pointer;
  transition: all 0.15s;
  user-select: none;
}

.metas-ver-ambito-btn:hover {
  background-color: #f8fafc;
  color: #1e293b;
  border-color: #cbd5e1;
}

.metas-toggle-chevron {
  transition: transform 0.25s ease;
}

.metas-ver-ambito-btn[aria-expanded="true"] .metas-toggle-chevron {
  transform: rotate(180deg);
}

.metas-ambitos-desglose {
  max-height: 0;
  overflow: hidden;
  opacity: 0;
  transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
}

.metas-ambitos-desglose.active {
  max-height: 1000px;
  opacity: 1;
}

/* Bloque 3 Avance Card */
.metas-toolbar {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 18px;
}

.metas-filtro-grupo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.metas-filtro-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  min-width: 65px;
}

.metas-filtro-btns {
  display: flex;
  gap: 6px;
}

.metas-filtro-btn {
  font-size: 0.72rem;
  font-weight: 600;
  color: #475569;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 4px 10px;
  cursor: pointer;
  transition: all 0.15s;
}

.metas-filtro-btn:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

.metas-filtro-btn.active {
  background-color: #1e293b;
  color: #ffffff;
  border-color: #1e293b;
}

.metas-avance-card {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}

.metas-avance-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px 0 rgba(0,0,0,0.05);
}

.metas-avance-card-header {
  padding: 10px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.metas-avance-card-body {
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex-grow: 1;
}

.metas-avance-card-ambito {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

.metas-avance-tipo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  color: #64748b;
}

.metas-avance-cifras {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  border: 1px solid #f1f5f9;
  border-radius: 6px;
  background-color: #f8fafc;
  overflow: hidden;
  text-align: center;
}

.metas-avance-cifra {
  padding: 8px 4px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.metas-avance-cifra:not(:last-child) {
  border-right: 1px solid #f1f5f9;
}

.metas-avance-cifra-val {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
}

.metas-avance-cifra-lbl {
  font-size: 0.6rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 600;
}

.metas-avance-fechas {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.7rem;
  color: #64748b;
  margin-top: 4px;
}

.metas-fecha-separator {
  color: #cbd5e1;
}

/* Tabs & Duo Layouts */
.chart-tabs-nav {
  display: flex;
  gap: 4px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 16px;
}

.chart-tabs-nav button {
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  padding: 8px 16px;
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s;
}

.chart-tabs-nav button:hover {
  color: #1e293b;
}

.chart-tabs-nav button.active {
  color: #ef4444;
  border-bottom-color: #ef4444;
}

.chart-tab-panel {
  display: none;
}

.chart-tab-panel.active {
  display: block;
}

.chart-tab-panel-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.chart-tab-panel-hint {
  font-size: 0.7rem;
  font-weight: 600;
  color: #ef4444;
  background-color: #fff1f2;
  border: 1px solid rgba(239, 68, 68, 0.15);
  padding: 2px 8px;
  border-radius: 4px;
  margin-left: auto;
}

.chart-tab-duo-layout {
  display: flex;
  gap: 24px;
}

.chart-tab-canvas-wrap {
  flex: 55;
  min-height: 280px;
  position: relative;
}

.chart-tab-side-table {
  flex: 45;
  max-height: 320px;
  overflow-y: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}

/* Table layout styling */
.metas-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.metas-table th {
  background-color: #f8fafc;
  color: #64748b;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 10px 12px;
  border-bottom: 1px solid #e2e8f0;
}

.metas-table td {
  padding: 10px 12px;
  font-size: 0.78rem;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.tabla-tab-row {
  cursor: pointer;
  transition: all 0.15s;
}

.tabla-tab-row:not(.tabla-tab-row--disabled):hover {
  background-color: #f8fafc;
}

.tabla-tab-row--disabled {
  opacity: 0.5;
  cursor: default;
}

.tabla-tab-row--active {
  background-color: #fff1f2 !important;
  box-shadow: inset 3px 0 0 #ef4444;
}

.tabla-tab-row--active td {
  color: #b91c1c;
  font-weight: 600;
}

.tabla-tab-accion-td {
  text-align: right;
  width: 80px;
}

.tabla-tab-filtrar-btn {
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #475569;
  border-radius: 4px;
  padding: 2px 8px;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  opacity: 0.65;
  transition: opacity 0.15s, background-color 0.15s, color 0.15s;
}

.tabla-tab-row:hover .tabla-tab-filtrar-btn {
  opacity: 1;
  background-color: #e2e8f0;
}

.tabla-tab-row--active .tabla-tab-filtrar-btn {
  opacity: 1;
  background-color: #ef4444;
  border-color: #ef4444;
  color: #ffffff;
}

.tabla-tab-row--active .tabla-tab-filtrar-btn:hover {
  background-color: #dc2626;
  border-color: #dc2626;
}

.metas-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 6px;
  vertical-align: middle;
}

/* Detalle Section bottom */
.tables-container {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
  padding: 20px;
}

.tables-container-header {
  display: flex;
  align-items: center;
  gap: 8px;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

#detalle-filtro-activo-badge {
  display: none;
  align-items: center;
  gap: 6px;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 0.72rem;
  font-weight: 600;
  color: #1e293b;
  margin-left: 8px;
}

#btn-detalle-limpiar-filtro {
  background: none;
  border: none;
  color: #94a3b8;
  font-weight: 700;
  cursor: pointer;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: color 0.15s;
  font-size: 0.75rem;
}

#btn-detalle-limpiar-filtro:hover {
  color: #ef4444;
}

.metas-full-row.detalle-oculto {
  display: none;
}

.metas-full-row.metas-row-activa {
  background-color: #fff1f2 !important;
  box-shadow: inset 3px 0 0 #ef4444;
}

@keyframes detalle-row-flash {
  0% { background-color: #fef9c3; }
  100% { background-color: transparent; }
}

.metas-full-row.detalle-flash {
  animation: detalle-row-flash 0.6s ease-out forwards;
}

/* Responsive queries */
@media (max-width: 900px) {
  .metas-kpi-grid { grid-template-columns: repeat(2, 1fr); }
  .chart-tab-duo-layout { flex-direction: column; }
  .metas-ambitos-grid { grid-template-columns: 1fr; }
  
  .chart-tab-canvas-wrap, .chart-tab-side-table {
    width: 100%;
    flex: none;
  }
}

@media (max-width: 560px) {
  .metas-kpi-grid { grid-template-columns: 1fr; }
  .metas-avances-grid { grid-template-columns: 1fr; }
  .metas-filtro-btns { flex-wrap: wrap; }
  .metas-filtro-grupo { flex-direction: column; align-items: flex-start; gap: 6px; }
}
</style>

<script>
(function () {
    'use strict';
    
    // Initialize the dashboard features immediately to avoid blocking on Chart.js loading
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardMetas);
    } else {
        initDashboardMetas();
    }
    
    function initDashboardMetas() {
        // Wire up toggle button in Bloque 2
        const btnToggle = document.getElementById('btn-toggle-ambitos-a');
        const panelToggle = document.getElementById('metas-desglose-a');
        if (btnToggle && panelToggle) {
            btnToggle.addEventListener('click', function() {
                const open = btnToggle.getAttribute('aria-expanded') === 'true';
                const nextState = !open;
                btnToggle.setAttribute('aria-expanded', nextState ? 'true' : 'false');
                panelToggle.setAttribute('aria-hidden', nextState ? 'false' : 'true');
                
                const chevron = btnToggle.querySelector('.metas-toggle-chevron');
                if (chevron) {
                    chevron.style.transform = nextState ? 'rotate(180deg)' : 'rotate(0deg)';
                }
                
                if (nextState) {
                    panelToggle.style.display = 'block';
                    panelToggle.offsetHeight; // force reflow
                    panelToggle.classList.add('active');
                } else {
                    panelToggle.classList.remove('active');
                    setTimeout(function() {
                        if (!panelToggle.classList.contains('active')) {
                            panelToggle.style.display = 'none';
                        }
                    }, 300); // Wait for transition to complete
                }
            });
        }
        
        // Bloque 3 Filters
        const cards = document.querySelectorAll('.metas-avance-card');
        const countEl = document.getElementById('metas-avances-count');
        const emptyEl = document.getElementById('metas-avances-empty');
        let selectedTiempo = 'todos';
        let selectedAmbito = 'todos';
        
        function applyFiltrosBloque3() {
            let visibleCount = 0;
            cards.forEach(card => {
                const cardAmbito = card.getAttribute('data-ambito');
                const cardMes = parseInt(card.getAttribute('data-mes'), 10);
                
                const okAmb = (selectedAmbito === 'todos' || cardAmbito === selectedAmbito);
                let okTime = true;
                
                if (selectedTiempo === '1') okTime = (cardMes >= 1 && cardMes <= 3);
                else if (selectedTiempo === '2') okTime = (cardMes >= 4 && cardMes <= 6);
                else if (selectedTiempo === '3') okTime = (cardMes >= 7 && cardMes <= 9);
                else if (selectedTiempo === '4') okTime = (cardMes >= 10 && cardMes <= 12);
                else if (selectedTiempo === 's1') okTime = (cardMes >= 1 && cardMes <= 6);
                else if (selectedTiempo === 's2') okTime = (cardMes >= 7 && cardMes <= 12);
                
                if (okAmb && okTime) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (countEl) {
                countEl.textContent = `${visibleCount} meta${visibleCount !== 1 ? 's' : ''}`;
            }
            if (emptyEl) {
                emptyEl.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }
        
        const ftEl = document.getElementById('filtro-tiempo');
        if (ftEl) {
            ftEl.addEventListener('click', function(e) {
                const btn = e.target.closest('.metas-filtro-btn');
                if (!btn) return;
                ftEl.querySelectorAll('.metas-filtro-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedTiempo = btn.getAttribute('data-tiempo');
                applyFiltrosBloque3();
            });
        }
        
        const faEl = document.getElementById('filtro-ambito');
        if (faEl) {
            faEl.addEventListener('click', function(e) {
                const btn = e.target.closest('.metas-filtro-btn');
                if (!btn) return;
                faEl.querySelectorAll('.metas-filtro-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedAmbito = btn.getAttribute('data-ambito');
                applyFiltrosBloque3();
            });
        }
        
        // Bloque 4 Tabs
        const tabs = document.querySelectorAll('#chart-nav-metas [role="tab"]');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    t.classList.remove('active');
                });
                
                const panels = document.querySelectorAll('.chart-tab-panel');
                panels.forEach(p => {
                    p.classList.remove('active');
                });
                
                tab.setAttribute('aria-selected', 'true');
                tab.classList.add('active');
                
                const targetId = tab.getAttribute('aria-controls');
                const chartPanelId = targetId === 'panel-ambito' ? 'chart-panel-metas-ambito' : 'chart-panel-metas-mes';
                const targetPanel = document.getElementById(chartPanelId);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    setTimeout(function() {
                        initLazyChart(targetId);
                    }, 50);
                }
            });
        });
        
        // Lazy initialize charts UMD API v4
        function initLazyChart(panelId) {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js is not defined. Skipping chart render for: ' + panelId);
                return;
            }
            if (panelId === 'panel-ambito') {
                const canvas = document.getElementById('chartCumplimientoAmbitoTab');
                if (!canvas) return;
                if (canvas.offsetWidth === 0) return; // Defer if hidden
                if (canvas.dataset.chartInit) return; // Skip if already initialized
                
                canvas.dataset.chartInit = 'true';
                    const ctx = canvas.getContext('2d');
                    
                    const labels = <?php echo json_encode(array_column($cumplimientoAmbito, 'ambito')); ?>;
                    const data = <?php echo json_encode(array_map('floatval', array_column($cumplimientoAmbito, 'pct_promedio'))); ?>;
                    const colors = data.map(v => {
                        if (v >= 95) return '#28c76f';
                        if (v >= 80) return '#f59e0b';
                        if (v >= 50) return '#f97316';
                        return '#ef4444';
                    });
                    
                    const existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: colors,
                                borderRadius: 5,
                                borderSkipped: false,
                                barThickness: 16
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (c) => ` ${c.raw}% cumplimiento`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    min: 0,
                                    max: 100,
                                    ticks: { callback: v => v + '%' },
                                    grid: { color: '#f1f5f9' }
                                },
                                y: { grid: { display: false } }
                            }
                        }
                    });
            } else if (panelId === 'panel-mes') {
                const canvas = document.getElementById('chartMetasMes');
                if (!canvas) return;
                if (canvas.offsetWidth === 0) return; // Defer if hidden
                if (canvas.dataset.chartInit) return; // Skip if already initialized
                
                canvas.dataset.chartInit = 'true';
                    const ctx = canvas.getContext('2d');
                    
                    const data = <?php echo json_encode($metasPorMes); ?>;
                    
                    const existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            datasets: [{
                                data: data,
                                backgroundColor: data.map((v, i) => {
                                    const currentMonth = new Date().getMonth();
                                    if (i === currentMonth) return '#ef4444';
                                    return v > 0 ? '#ef444480' : '#e2e8f0';
                                }),
                                borderRadius: 6,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (c) => ` ${c.raw} meta${c.raw !== 1 ? 's' : ''}`
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 },
                                    grid: { color: '#f1f5f9' }
                                }
                            },
                            onClick: (event, elements) => {
                                if (!elements.length) return;
                                const index = elements[0].index;
                                const mesNum = index + 1;
                                
                                const row = document.querySelector(`.tabla-tab-row--mes[data-filtro-mes="${mesNum}"]`);
                                if (row && !row.classList.contains('tabla-tab-row--disabled')) {
                                    row.click();
                                }
                            }
                        }
                    });
            }
        }
        
        // Expose render hooks globally to work with home.js
        window.renderCumplimientoAmbitoTab = function() {
            initLazyChart('panel-ambito');
        };
        window.renderCalendarioMetas = function() {
            initLazyChart('panel-mes');
        };
        
        // Initial chart render (poll for Chart to be loaded if not available yet)
        if (typeof Chart !== 'undefined') {
            initLazyChart('panel-ambito');
        } else {
            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                if (typeof Chart !== 'undefined') {
                    clearInterval(interval);
                    initLazyChart('panel-ambito');
                } else if (attempts >= 50) {
                    clearInterval(interval);
                }
            }, 100);
        }
        
        // Bloque 5 Interactive Filtering
        let currentFilterType = null;
        let currentFilterVal = null;
        let currentFilterName = null;
        
        const detailTable = document.getElementById('tabla-metas-panel');
        const sinDatosDetail = document.getElementById('metas-sin-datos-mes');
        const badgeEl = document.getElementById('detalle-filtro-activo-badge');
        const badgeTextEl = document.getElementById('detalle-filtro-activo-texto');
        const btnLimpiar = document.getElementById('btn-detalle-limpiar-filtro');
        
        function applyFilterToDetail() {
            if (!detailTable) return;
            const rows = detailTable.querySelectorAll('tbody tr.metas-full-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                let show = true;
                if (currentFilterType === 'ambito') {
                    const rowAmbito = row.getAttribute('data-ambito-id');
                    show = (String(rowAmbito) === String(currentFilterVal));
                } else if (currentFilterType === 'mes') {
                    const rowMes = row.getAttribute('data-mes-meta');
                    show = (String(rowMes) === String(currentFilterVal));
                }
                
                if (show) {
                    row.classList.remove('detalle-oculto');
                    visibleCount++;
                    
                    if (currentFilterType !== null) {
                        row.classList.add('metas-row-activa');
                    } else {
                        row.classList.remove('metas-row-activa');
                    }
                    
                    // Trigger flash animation
                    row.classList.remove('detalle-flash');
                    void row.offsetWidth; // trigger layout reflow
                    row.classList.add('detalle-flash');
                    setTimeout(() => {
                        row.classList.remove('detalle-flash');
                    }, 600);
                } else {
                    row.classList.add('detalle-oculto');
                    row.classList.remove('metas-row-activa');
                }
            });
            
            if (sinDatosDetail) {
                sinDatosDetail.style.display = (visibleCount === 0 && currentFilterType !== null) ? 'block' : 'none';
            }
            
            if (currentFilterType !== null && badgeEl && badgeTextEl) {
                badgeTextEl.textContent = (currentFilterType === 'ambito' ? 'Ámbito: ' : 'Mes: ') + currentFilterName;
                badgeEl.style.display = 'inline-flex';
            } else if (badgeEl) {
                badgeEl.style.display = 'none';
            }
        }
        
        function clearDetailFilter() {
            currentFilterType = null;
            currentFilterVal = null;
            currentFilterName = null;
            
            document.querySelectorAll('.tabla-tab-row--active').forEach(r => {
                r.classList.remove('tabla-tab-row--active');
            });
            
            if (detailTable) {
                detailTable.querySelectorAll('tbody tr.metas-full-row').forEach(row => {
                    row.classList.remove('detalle-oculto', 'detalle-flash', 'metas-row-activa');
                });
            }
            
            if (sinDatosDetail) sinDatosDetail.style.display = 'none';
            if (badgeEl) badgeEl.style.display = 'none';
        }
        
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function(e) {
                e.stopPropagation();
                clearDetailFilter();
            });
        }
        
        // Row clicks for Ambito table
        const tabAmbitoRows = document.querySelectorAll('.tabla-tab-row--ambito');
        tabAmbitoRows.forEach(row => {
            row.addEventListener('click', function() {
                const val = row.getAttribute('data-filtro-ambito');
                const name = row.getAttribute('data-nombre-ambito');
                const isActive = row.classList.contains('tabla-tab-row--active');
                
                document.querySelectorAll('.tabla-tab-row--active').forEach(r => {
                    r.classList.remove('tabla-tab-row--active');
                });
                
                if (isActive) {
                    clearDetailFilter();
                } else {
                    row.classList.add('tabla-tab-row--active');
                    currentFilterType = 'ambito';
                    currentFilterVal = val;
                    currentFilterName = name;
                    applyFilterToDetail();
                    
                    const container = document.querySelector('.tables-container');
                    if (container) {
                        setTimeout(() => {
                            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 80);
                    }
                }
            });
        });
        
        // Row clicks for Mes table
        const tabMesRows = document.querySelectorAll('.tabla-tab-row--mes');
        tabMesRows.forEach(row => {
            if (row.classList.contains('tabla-tab-row--disabled')) return;
            row.addEventListener('click', function() {
                const val = row.getAttribute('data-filtro-mes');
                const name = row.getAttribute('data-nombre-mes');
                const isActive = row.classList.contains('tabla-tab-row--active');
                
                document.querySelectorAll('.tabla-tab-row--active').forEach(r => {
                    r.classList.remove('tabla-tab-row--active');
                });
                
                if (isActive) {
                    clearDetailFilter();
                } else {
                    row.classList.add('tabla-tab-row--active');
                    currentFilterType = 'mes';
                    currentFilterVal = val;
                    currentFilterName = name;
                    applyFilterToDetail();
                    
                    const container = document.querySelector('.tables-container');
                    if (container) {
                        setTimeout(() => {
                            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 80);
                    }
                }
            });
        });
    }
})();
</script>

</div>
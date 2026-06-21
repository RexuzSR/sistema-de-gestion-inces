<div class="dashboard-section" id="section-metas">

<?php
$ambitoPaleta = ['#3b82f6','#8b5cf6','#ef4444','#f59e0b','#10b981','#06b6d4','#f97316','#ec4899'];
function _semColor(float $p): array {
    if ($p >= 95) return ['#28c76f','verde'];
    if ($p >= 80) return ['#f59e0b','amarillo'];
    if ($p >= 50) return ['#f97316','amarillo'];
    return ['#ef4444','rojo'];
}
function _semLabel(float $p): string {
    if ($p >= 95) return 'Excelente';
    if ($p >= 80) return 'Favorable';
    if ($p >= 50) return 'En progreso';
    return 'Atención';
}
function _kpiColors(float $p): array {
    if ($p >= 95) return ['#28c76f', '#d4f4e3'];
    if ($p >= 80) return ['#f59e0b', '#fef3c7'];
    if ($p >= 50) return ['#f97316', '#ffedd5'];
    return ['#ef4444', '#fee2e2'];
}
[$colorAmbitos, $bgAmbitos] = _kpiColors($tasaCumplimientoAmbitos);
[$colorAnual, $bgAnual] = _kpiColors($tasaMetaAnual);

$pctA = (float)($progresoAnual['pct_global']  ?? 0);
$totA = (int)  ($progresoAnual['total_metas'] ?? 0);
$cumA = (int)  ($progresoAnual['cumplidas']    ?? 0);
$actA = (int)  ($progresoAnual['en_progreso']  ?? 0);
$ncA  = (int)  ($progresoAnual['no_cumplidas'] ?? 0);
$objA = (int)  ($progresoAnual['obj_total']    ?? 0);
$rlA  = (int)  ($progresoAnual['real_total']   ?? 0);
[$cA, $sA] = _semColor($pctA);
?>

<div class="row g-3 mt-1 mb-2">
    <!-- Card 1: Tasa de cumplimiento de metas por ámbitos -->
    <div class="col-md-6">
        <div class="kpi-card-basic shadow-sm p-3" style="cursor: default; border-left: 4px solid <?php echo $colorAmbitos; ?>; background: #fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="kpi-basic-label" style="font-size: 0.78rem; text-transform: uppercase; font-weight: 600; color: #64748b; display: block;">Tasa Cumplimiento de Metas por Ámbitos</span>
                    <h3 class="kpi-basic-valor mt-1" style="font-size: 2.1rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.2;"><?php echo number_format($tasaCumplimientoAmbitos, 1); ?>%</h3>
                    <span class="text-muted" style="font-size: 0.72rem;">Promedio de metas activas con ámbito asignado</span>
                </div>
                <div class="kpi-basic-icon-wrap" style="background: <?php echo $bgAmbitos; ?>; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="<?php echo $colorAmbitos; ?>" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.742c-.934-.53-2.118-.6-3.213-.492-1.18.118-2.368.46-3.287.81zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2" style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                <div style="width: <?php echo min($tasaCumplimientoAmbitos, 100); ?>%; height: 100%; background: <?php echo $colorAmbitos; ?>; border-radius: 3px;"></div>
            </div>
        </div>
    </div>

    <!-- Card 2: Cumplimiento del objetivo de participantes anuales -->
    <div class="col-md-6">
        <div class="kpi-card-basic shadow-sm p-3" style="cursor: default; border-left: 4px solid <?php echo $colorAnual; ?>; background: #fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="kpi-basic-label" style="font-size: 0.78rem; text-transform: uppercase; font-weight: 600; color: #64748b; display: block;">Cumplimiento Participantes Anuales</span>
                    <h3 class="kpi-basic-valor mt-1" style="font-size: 2.1rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.2;"><?php echo number_format($tasaMetaAnual, 1); ?>%</h3>
                    <span class="text-muted" style="font-size: 0.72rem;">Logrado: <strong><?php echo number_format($metaAnualReal); ?></strong> / <?php echo number_format($metaAnualObj); ?> participantes</span>
                </div>
                <div class="kpi-basic-icon-wrap" style="background: <?php echo $bgAnual; ?>; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="<?php echo $colorAnual; ?>" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2" style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                <div style="width: <?php echo min($tasaMetaAnual, 100); ?>%; height: 100%; background: <?php echo $colorAnual; ?>; border-radius: 3px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="kpi-panel-inner mt-3" style="border-top-color:#ef4444;">

    <div class="metas-progreso-anual-wrap">

        <div class="metas-progreso-header">
            <div class="metas-progreso-header-left">
                <span class="metas-progreso-icono" style="color:<?php echo $cA; ?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </span>
                <div>
                    <span class="metas-progreso-titulo">Progreso Global de Metas — <?php echo $anioActual; ?></span>
                    <span class="metas-progreso-subtitulo">
                        <strong style="color:#1e293b;"><?php echo $totA; ?></strong> metas en el año
                        &nbsp;·&nbsp; <strong style="color:#28c76f;"><?php echo $cumA; ?></strong> cumplidas
                        &nbsp;·&nbsp; <strong style="color:#3b82f6;"><?php echo $actA; ?></strong> activas
                        <?php if ($ncA > 0): ?>&nbsp;·&nbsp; <span style="color:#ef4444;"><?php echo $ncA; ?> no cumplidas</span><?php endif; ?>
                        &nbsp;·&nbsp; <?php echo number_format($rlA); ?> / <?php echo number_format($objA); ?> objetivo
                    </span>
                </div>
            </div>
            <div class="metas-progreso-header-right">
                <span class="semaforo-badge semaforo-<?php echo $sA; ?>" style="font-size:.85rem;font-weight:700;">
                    <?php echo $pctA; ?>% · <?php echo _semLabel($pctA); ?>
                </span>
                <button class="metas-progreso-toggle-btn" id="btn-toggle-ambitos-a" type="button" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" class="metas-toggle-chevron">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                    <span class="metas-btn-texto">Ver por ámbito</span>
                </button>
            </div>
        </div>

        <div class="metas-progreso-bar-wrap">
            <div class="metas-progreso-track">
                <div class="metas-progreso-fill" style="width:<?php echo min($pctA,100); ?>%;background:<?php echo $cA; ?>;">
                    <?php if ($pctA > 10): ?><span class="metas-progreso-fill-label"><?php echo $pctA; ?>%</span><?php endif; ?>
                </div>
                <?php if ($pctA <= 10 && $pctA > 0): ?>
                <span class="metas-progreso-pct-ext" style="left:<?php echo min($pctA,100); ?>%;"><?php echo $pctA; ?>%</span>
                <?php endif; ?>
            </div>
            <div class="metas-progreso-escala"><span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span></div>
        </div>

        <div class="metas-ambitos-desglose" id="metas-desglose-a" style="display:none;">
            <?php if (empty($progresoAnualPorAmbito)): ?>
                <p class="text-center text-muted py-3" style="font-size:.8rem;">Sin datos de ámbitos para <?php echo $anioActual; ?>.</p>
            <?php else: ?>
            <div class="metas-ambitos-grid">
                <?php foreach ($progresoAnualPorAmbito as $pa):
                    [$cPa,$sPa] = _semColor((float)$pa['pct_ambito']);
                ?>
                <div class="metas-ambito-card">
                    <div class="metas-ambito-card-header">
                        <span class="metas-ambito-card-nombre"><?php echo htmlspecialchars($pa['ambito']); ?></span>
                        <span class="semaforo-badge semaforo-<?php echo $sPa; ?>" style="font-size:.66rem;"><?php echo $pa['pct_ambito']; ?>%</span>
                    </div>
                    <div class="metas-ambito-card-bar-track">
                        <div class="metas-ambito-card-bar-fill" style="width:<?php echo min((float)$pa['pct_ambito'],100); ?>%;background:<?php echo $cPa; ?>;"></div>
                    </div>
                    <div class="metas-ambito-card-meta">
                        <span><?php echo $pa['real_total']; ?> / <?php echo $pa['obj_total']; ?></span>
                        <span><?php echo $pa['total_metas']; ?> meta<?php echo $pa['total_metas']!=1?'s':''; ?> · <?php echo $pa['cumplidas']; ?> ✓</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="metas-avances-wrap" id="metas-avances-wrap">

        <div class="metas-avances-header">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#64748b" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5"/>
                </svg>
                <span style="font-size:.82rem;font-weight:700;color:#1e293b;">Avance por Meta — <?php echo $anioActual; ?></span>
                <span id="metas-avances-count" style="font-size:.72rem;color:#64748b;background:#f1f5f9;border-radius:10px;padding:1px 8px;border:1px solid #e2e8f0;">
                    <?php echo count($metasAnioDetalle); ?> meta<?php echo count($metasAnioDetalle)!=1?'s':''; ?>
                </span>
            </div>
            <div class="metas-avances-filtros">
                <div class="metas-filtro-grupo">
                    <span class="metas-filtro-label">Período</span>
                    <div class="metas-filtro-btns" id="filtro-tiempo">
                        <button class="metas-filtro-btn active" data-tiempo="todos">Todo</button>
                        <button class="metas-filtro-btn" data-tiempo="1">Ene–Mar</button>
                        <button class="metas-filtro-btn" data-tiempo="2">Abr–Jun</button>
                        <button class="metas-filtro-btn" data-tiempo="3">Jul–Sep</button>
                        <button class="metas-filtro-btn" data-tiempo="4">Oct–Dic</button>
                        <button class="metas-filtro-btn" data-tiempo="s1">Sem I</button>
                        <button class="metas-filtro-btn" data-tiempo="s2">Sem II</button>
                    </div>
                </div>
                <div class="metas-filtro-grupo">
                    <span class="metas-filtro-label">Ámbito</span>
                    <div class="metas-filtro-btns" id="filtro-ambito">
                        <button class="metas-filtro-btn active" data-ambito="todos">Todos</button>
                        <?php
                        $ambitosEnMetas = [];
                        foreach ($metasAnioDetalle as $md) {
                            $aid = (int)$md['id_ambito'];
                            if (!isset($ambitosEnMetas[$aid])) {
                                $ambitosEnMetas[$aid] = htmlspecialchars($md['nombre_ambito']);
                            }
                        }
                        foreach ($ambitosEnMetas as $aid => $anombre): ?>
                        <button class="metas-filtro-btn" data-ambito="<?php echo $aid; ?>"><?php echo $anombre; ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($metasAnioDetalle)): ?>
            <div class="text-center py-4 text-muted" style="font-size:.82rem;">
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
                <div class="metas-avance-card-top" style="background:<?php echo $colAmb; ?>15;border-bottom:2px solid <?php echo $colAmb; ?>20;">
                    <span class="metas-avance-ambito-badge" style="background:<?php echo $colAmb; ?>18;color:<?php echo $colAmb; ?>;border:1px solid <?php echo $colAmb; ?>30;">
                        <?php echo htmlspecialchars($ma['nombre_ambito']); ?>
                    </span>
                    <span class="semaforo-badge semaforo-<?php echo $cumMa ? 'verde' : $sMa; ?>" style="font-size:.66rem;">
                        <?php echo $cumMa ? '✓ Cumplida' : htmlspecialchars($ma['estado_cumplimiento']); ?>
                    </span>
                </div>
                <div class="metas-avance-card-body">
                    <div class="metas-avance-tipo">
                        <span class="metas-tipo-badge"><?php echo htmlspecialchars($ma['tipo_objetivo']); ?></span>
                        <?php if (!empty($ma['periodo_tiempo'])): ?>
                        <span class="metas-avance-periodo"><?php echo htmlspecialchars($ma['periodo_tiempo']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="metas-avance-cifras">
                        <div class="metas-avance-cifra">
                            <span class="metas-avance-cifra-val" style="color:<?php echo $cMa; ?>;"><?php echo number_format((float)$ma['porcentaje_cumplimiento'], 1); ?>%</span>
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
                    <div class="metas-avance-bar-track">
                        <div class="metas-avance-bar-fill" style="width:<?php echo $pMa; ?>%;background:<?php echo $cMa; ?>;"></div>
                    </div>
                    <div class="metas-avance-fechas">
                        <span><?php echo $fechaI; ?></span>
                        <span style="color:#cbd5e1;">→</span>
                        <span><?php echo $fechaF; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="metas-avances-empty" style="display:none;" class="text-center py-4 text-muted">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
            <p class="mt-2 mb-0">Sin metas para el filtro seleccionado.</p>
        </div>
        <?php endif; ?>

    </div>

</div>


<div class="kpi-panel-inner mt-4" style="border-top-color:#ef4444;">

    <div class="kpi-panel-header">
        <h6 class="kpi-panel-titulo" style="color:#ef4444;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
            Progresión de Metas Activas
        </h6>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span style="font-size:.78rem;color:#64748b;"><strong style="color:#1e293b;"><?php echo (int)($kMetas['total_metas'] ?? 0); ?></strong> activas</span>
            <span style="font-size:.78rem;color:#64748b;"><strong style="color:#28c76f;"><?php echo (int)($kMetas['cumplidas'] ?? 0); ?></strong> cumplidas</span>
            <span class="semaforo-badge semaforo-<?php echo $s_meta; ?>"><?php echo $kMetas['promedio_pct'] ?? 0; ?>% promedio · <?php echo semL($s_meta); ?></span>
            <small class="text-muted">año <?php echo $anioActual; ?></small>
        </div>
    </div>

    <div class="chart-tabs-wrapper mt-3">
        <nav class="chart-tabs-nav" id="chart-nav-metas">
            <button class="chart-tab-btn active" data-section="metas" data-chart="ambito">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M11 0H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm-1 7H4a.5.5 0 0 1 0-1h6a.5.5 0 0 1 0 1zm0 3H4a.5.5 0 0 1 0-1h6a.5.5 0 0 1 0 1z"/></svg>
                Cumplimiento por Ámbito
            </button>
            <button class="chart-tab-btn" data-section="metas" data-chart="mes">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                Metas por Mes
            </button>
        </nav>
        <div class="chart-tabs-body">

            <div class="chart-tab-panel active" id="chart-panel-metas-ambito">
                <p class="chart-panel-title">
                    Promedio de cumplimiento por ámbito
                    <span class="tab-tabla-hint">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>
                        Clic en una fila para filtrar el detalle
                    </span>
                </p>
                <div class="chart-tab-duo-layout">
                    <div class="chart-tab-canvas-wrap"><canvas id="chartCumplimientoAmbitoTab" data-suggested-min="-10"></canvas></div>
                    <div class="chart-tab-side-table">
                        <table class="table table-sm table-striped mb-0" id="tabla-tab-ambito">
                            <thead>
                                <tr>
                                    <th>Ámbito</th>
                                    <th class="text-center">Metas</th>
                                    <th class="text-center">Cumplidas</th>
                                    <th class="text-center">%</th>
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
                                    data-nombre-ambito="<?php echo htmlspecialchars($amb['ambito']); ?>"
                                    title="Filtrar detalle por: <?php echo htmlspecialchars($amb['ambito']); ?>">
                                    <td class="fw-semibold" style="font-size:.75rem;"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                    <td class="text-center"><?php echo $amb['total_metas']; ?></td>
                                    <td class="text-center text-success fw-semibold"><?php echo $amb['cumplidas']; ?></td>
                                    <td class="text-center"><span class="badge-mini" style="background:<?php echo $aC2; ?>22;color:<?php echo $aC2; ?>;"><?php echo $amb['pct_promedio']; ?>%</span></td>
                                    <td class="tabla-tab-accion-td">
                                        <span class="tabla-tab-filtrar-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/></svg>
                                            Filtrar
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($cumplimientoAmbito)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-2">Sin metas activas.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="chart-tab-panel" id="chart-panel-metas-mes">
                <p class="chart-panel-title">
                    Metas activas por mes — año <?php echo $anioActual; ?>
                    <span class="tab-tabla-hint">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>
                        Clic en una fila para filtrar el detalle
                    </span>
                </p>
                <div class="chart-tab-duo-layout">
                    <div class="chart-tab-canvas-wrap"><canvas id="chartMetasMes"></canvas></div>
                    <div class="chart-tab-side-table">
                        <table class="table table-sm table-striped mb-0" id="tabla-tab-mes">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-center">Metas</th>
                                    <th style="min-width:70px;">Barra</th>
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
                            ?>
                                <tr class="tabla-tab-row tabla-tab-row--mes<?php echo $mv === 0 ? ' tabla-tab-row--disabled' : ''; ?>"
                                    data-filtro-mes="<?php echo $numMes; ?>"
                                    data-nombre-mes="<?php echo $mesesNombres[$mi]; ?>"
                                    <?php echo $mv === 0 ? 'title="Sin metas en este mes"' : 'title="Filtrar detalle por: ' . $mesesNombres[$mi] . '"'; ?>
                                    <?php echo $esActual ? ' style="background:#fee2e210;"' : ''; ?>>
                                    <td class="fw-semibold" style="<?php echo $esActual ? 'color:#ef4444;' : ''; ?>">
                                        <?php echo $mesesNombres[$mi]; ?>
                                        <?php if ($esActual): ?><span class="badge-mini" style="background:#fee2e2;color:#b91c1c;">hoy</span><?php endif; ?>
                                    </td>
                                    <td class="text-center fw-bold" style="color:<?php echo $mv>0?'#ef4444':'#94a3b8'; ?>;"><?php echo $mv; ?></td>
                                    <td><div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;"><div style="width:<?php echo $mBar; ?>%;height:7px;background:#ef4444;border-radius:4px;opacity:<?php echo $mv>0?'1':'.3'; ?>;"></div></div></td>
                                    <td class="tabla-tab-accion-td">
                                        <?php if ($mv > 0): ?>
                                        <span class="tabla-tab-filtrar-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/></svg>
                                            Filtrar
                                        </span>
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

    <div class="tables-container bg-light p-3 rounded-3 mt-3">
        <div class="panel-section">
            <div class="panel-section-header">
                <span class="panel-section-dot" style="background:#ef4444;"></span>
                <span class="panel-section-label">Detalle de Metas</span>
                <span id="detalle-filtro-activo-badge" style="display:none;align-items:center;gap:5px;margin-left:8px;font-size:.73rem;font-weight:600;color:#1e293b;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:6px;padding:2px 8px 2px 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#64748b" viewBox="0 0 16 16"><path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/></svg>
                    <span id="detalle-filtro-activo-texto">—</span>
                    <button id="btn-detalle-limpiar-filtro" type="button" title="Quitar filtro" style="background:none;border:none;padding:0;margin-left:2px;cursor:pointer;display:inline-flex;align-items:center;color:#64748b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 0 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
                    </button>
                </span>
                <small class="text-muted ms-auto">≥95% verde · ≥80% atención · &lt;80% crítico</small>
            </div>

            <div class="metas-filter-toolbar">
                <span id="metas-mes-activo-wrap" class="metas-mes-activo-label d-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                    Mes: <strong id="metas-mes-label">—</strong>
                </span>
                <button id="btn-metas-ver-todos" class="btn-metas-ver-todos d-none" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 0 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
                    Ver todos los meses
                </button>
            </div>

            <div id="metas-sin-datos-mes" class="metas-sin-datos-mes" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                <p>No hay metas registradas para este mes.</p>
            </div>

            <?php if (empty($metasDetalle)): ?>
                <div class="text-center py-4 text-muted">
                    <p class="mt-2">No hay metas activas registradas.</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0" id="tabla-metas-panel">
                    <thead class="table-light">
                        <tr><th>Ámbito</th><th class="text-center">Tipo</th><th class="text-center">Objetivo</th><th style="min-width:200px;">Progreso</th><th class="text-center">Estado</th></tr>
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
                            class="metas-full-row meta-row-link<?php echo $cumplida ? ' metas-row-cumplida' : ''; ?>" style="cursor:pointer;">
                            <td>
                                <div class="metas-ambito-cell">
                                    <div class="metas-ambito-dot metas-dot-<?php echo $mSem; ?>"></div>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($m['nombre_ambito'] ?? 'General'); ?></span>
                                </div>
                            </td>
                            <td class="text-center"><span class="metas-tipo-badge"><?php echo htmlspecialchars($m['tipo_objetivo']); ?></span></td>
                            <td class="text-center fw-bold" style="font-size:1rem;"><?php echo $m['meta_objetivo']; ?></td>
                            <td>
                                <div class="metas-progress-cell">
                                    <div class="metas-progress-track">
                                        <div class="metas-progress-fill semaforo-fill-<?php echo $mSem; ?>" style="width:<?php echo min($mPct,100); ?>%;"></div>
                                    </div>
                                    <span class="metas-progress-pct semaforo-text-<?php echo $mSem; ?>"><?php echo $mPct; ?>%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="semaforo-badge semaforo-<?php echo $cumplida ? 'verde' : $mSem; ?>">
                                    <?php echo $cumplida ? '✓ ' : ''; ?><?php echo $m['estado_cumplimiento']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>

</div>


<style>
.metas-progreso-anual-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px 14px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.metas-progreso-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
.metas-progreso-header-left { display:flex; align-items:flex-start; gap:10px; }
.metas-progreso-icono { flex-shrink:0; margin-top:2px; }
.metas-progreso-titulo { display:block; font-size:.85rem; font-weight:700; color:#1e293b; line-height:1.3; }
.metas-progreso-subtitulo { display:block; font-size:.78rem; color:#1e293b; margin-top:2px; line-height:1.7; }
.metas-progreso-header-right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.metas-progreso-toggle-btn { display:inline-flex; align-items:center; gap:5px; font-size:.73rem; font-weight:600; color:#475569; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; padding:4px 10px; cursor:pointer; transition:background .15s,color .15s; white-space:nowrap; }
.metas-progreso-toggle-btn:hover { background:#e2e8f0; color:#1e293b; border-color:#94a3b8; }
.metas-progreso-toggle-btn[aria-expanded="true"] { background:#fee2e2; color:#b91c1c; border-color:#fca5a5; }
.metas-toggle-chevron { transition:transform .25s ease; }
.metas-progreso-toggle-btn[aria-expanded="true"] .metas-toggle-chevron { transform:rotate(180deg); }
.metas-progreso-track { position:relative; height:22px; background:#f1f5f9; border-radius:11px; overflow:hidden; }
.metas-progreso-fill { height:100%; border-radius:11px; display:flex; align-items:center; justify-content:flex-end; padding-right:8px; transition:width .6s cubic-bezier(.4,0,.2,1); }
.metas-progreso-fill-label { font-size:.7rem; font-weight:700; color:#fff; text-shadow:0 1px 2px rgba(0,0,0,.25); white-space:nowrap; }
.metas-progreso-pct-ext { position:absolute; top:50%; transform:translateY(-50%); font-size:.7rem; font-weight:700; color:#475569; padding-left:4px; }
.metas-progreso-escala { display:flex; justify-content:space-between; font-size:.72rem; color:#64748b; margin-top:3px; padding:0 2px; }
.metas-ambitos-desglose { margin-top:14px; padding-top:14px; border-top:1px dashed #e2e8f0; }
.metas-ambitos-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }
.metas-ambito-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px 12px; }
.metas-ambito-card-header { display:flex; align-items:center; justify-content:space-between; gap:6px; margin-bottom:10px; }
.metas-ambito-card-nombre { font-size:.82rem; font-weight:700; color:#1e293b; line-height:1.3; }
.metas-ambito-card-bar-track { height:11px; background:#e2e8f0; border-radius:6px; overflow:hidden; margin-bottom:8px; }
.metas-ambito-card-bar-fill { height:100%; border-radius:6px; transition:width .5s cubic-bezier(.4,0,.2,1); }
.metas-ambito-card-meta { display:flex; justify-content:space-between; gap:4px; font-size:.72rem; color:#1e293b; font-weight:500; flex-wrap:wrap; }

.metas-avances-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px 16px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
.metas-avances-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
.metas-avances-filtros { display:flex; flex-direction:column; gap:8px; align-items:flex-end; }
.metas-filtro-grupo { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.metas-filtro-label { font-size:.74rem; font-weight:600; color:#475569; white-space:nowrap; }
.metas-filtro-btns { display:flex; flex-wrap:wrap; gap:4px; }
.metas-filtro-btn { font-size:.74rem; font-weight:600; padding:4px 10px; border-radius:6px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; cursor:pointer; transition:background .14s,color .14s,border-color .14s; white-space:nowrap; }
.metas-filtro-btn:hover { background:#f1f5f9; border-color:#94a3b8; color:#1e293b; }
.metas-filtro-btn.active { background:#1e293b; border-color:#1e293b; color:#fff; }
.metas-avances-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:12px; }
.metas-avance-card { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; transition:box-shadow .18s,border-color .18s; }
.metas-avance-card:hover { box-shadow:0 3px 12px rgba(0,0,0,.09); border-color:#cbd5e1; }
.metas-avance-card-top { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:8px 12px; flex-wrap:wrap; }
.metas-avance-ambito-badge { font-size:.72rem; font-weight:700; border-radius:6px; padding:2px 8px; white-space:nowrap; }
.metas-avance-card-body { padding:10px 13px 12px; }
.metas-avance-tipo { display:flex; align-items:center; gap:6px; margin-bottom:8px; flex-wrap:wrap; }
.metas-avance-periodo { font-size:.72rem; color:#475569; background:#f1f5f9; border-radius:4px; padding:1px 6px; }
.metas-avance-cifras { display:flex; margin-bottom:8px; border:1px solid #e2e8f0; border-radius:7px; overflow:hidden; }
.metas-avance-cifra { flex:1; display:flex; flex-direction:column; align-items:center; padding:6px 4px; background:#fff; border-right:1px solid #e2e8f0; }
.metas-avance-cifra:last-child { border-right:none; }
.metas-avance-cifra-val { font-size:1rem; font-weight:800; line-height:1.1; color:#1e293b; }
.metas-avance-cifra-lbl { font-size:.65rem; color:#475569; text-transform:uppercase; letter-spacing:.03em; font-weight:600; }
.metas-avance-bar-track { height:7px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-bottom:7px; }
.metas-avance-bar-fill { height:100%; border-radius:4px; transition:width .5s cubic-bezier(.4,0,.2,1); }
.metas-avance-fechas { display:flex; align-items:center; gap:5px; font-size:.72rem; color:#475569; }

@keyframes metas-fade-in { from{opacity:0;transform:translateY(-5px)} to{opacity:1;transform:translateY(0)} }

/* Tablas interactivas */
.tabla-tab-row {
    cursor: pointer;
    transition: background .14s;
}
.tabla-tab-row:not(.tabla-tab-row--disabled):hover {
    background: #fff7ed !important;
}
.tabla-tab-row--disabled {
    cursor: default;
    opacity: .5;
}
.tabla-tab-row--active {
    background: #fff1f2 !important;
    box-shadow: inset 3px 0 0 #ef4444;
}
.tabla-tab-row--active td:first-child {
    color: #b91c1c;
    font-weight: 700 !important;
}

/* Botón Filtrar en la última columna */
.tabla-tab-accion-th { width: 72px; }
.tabla-tab-accion-td { width: 72px; padding-right: 10px !important; }
.tabla-tab-filtrar-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .7rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    padding: 3px 8px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity .15s, background .15s, color .15s, border-color .15s, transform .12s;
    pointer-events: none;
}
.tabla-tab-row:not(.tabla-tab-row--disabled):hover .tabla-tab-filtrar-btn {
    opacity: 1;
    pointer-events: auto;
}
.tabla-tab-row--active .tabla-tab-filtrar-btn {
    opacity: 1;
    pointer-events: auto;
    background: #ef4444;
    border-color: #dc2626;
    color: #fff;
}
.tabla-tab-row--active .tabla-tab-filtrar-btn:hover {
    background: #dc2626;
    transform: scale(.97);
}

/* Hint "Clic para filtrar" */
.tab-tabla-hint {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .72rem;
    font-weight: 500;
    color: #94a3b8;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1px 8px;
    margin-left: 8px;
    vertical-align: middle;
    user-select: none;
}

@keyframes detalle-row-flash {
    0%   { background: #fef9c3; }
    100% { background: transparent; }
}
.metas-full-row.detalle-flash {
    animation: detalle-row-flash .55s ease-out forwards;
}
.metas-full-row.detalle-oculto {
    display: none;
}
</style>


<script>
(function () {
    'use strict';

    function slideToggle(el, open, cb) {
        el.style.overflow = 'hidden';
        el.style.transition = 'max-height .28s ease, opacity .22s ease';
        if (open) {
            el.style.display   = 'block';
            el.style.maxHeight = '0';
            el.style.opacity   = '0';
            requestAnimationFrame(function(){ requestAnimationFrame(function(){
                el.style.maxHeight = (el.scrollHeight + 500) + 'px';
                el.style.opacity   = '1';
            }); });
            el.addEventListener('transitionend', function done(){
                el.style.maxHeight = ''; el.style.overflow = ''; el.style.transition = '';
                el.removeEventListener('transitionend', done);
                if (cb) cb();
            });
        } else {
            el.style.maxHeight = el.scrollHeight + 'px';
            requestAnimationFrame(function(){ requestAnimationFrame(function(){
                el.style.maxHeight = '0';
                el.style.opacity   = '0';
            }); });
            el.addEventListener('transitionend', function done(){
                el.style.display = 'none';
                el.style.maxHeight = ''; el.style.overflow = '';
                el.style.transition = ''; el.style.opacity = '';
                el.removeEventListener('transitionend', done);
                if (cb) cb();
            });
        }
    }

    (function(){
        var btn   = document.getElementById('btn-toggle-ambitos-a');
        var panel = document.getElementById('metas-desglose-a');
        if (!btn || !panel) return;
        btn.addEventListener('click', function(){
            var open = this.getAttribute('aria-expanded') !== 'true';
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
            this.querySelector('.metas-btn-texto').textContent = open ? 'Ocultar ámbitos' : 'Ver por ámbito';
            slideToggle(panel, open);
        });
    })();

    (function(){
        var grid    = document.getElementById('metas-avances-grid');
        var empty   = document.getElementById('metas-avances-empty');
        var countEl = document.getElementById('metas-avances-count');
        if (!grid) return;

        var filtroTiempo = 'todos';
        var filtroAmbito = 'todos';

        function mesEnPeriodo(mes, periodo) {
            mes = parseInt(mes, 10);
            if (periodo === 'todos') return true;
            if (periodo === '1')  return mes >= 1  && mes <= 3;
            if (periodo === '2')  return mes >= 4  && mes <= 6;
            if (periodo === '3')  return mes >= 7  && mes <= 9;
            if (periodo === '4')  return mes >= 10 && mes <= 12;
            if (periodo === 's1') return mes >= 1  && mes <= 6;
            if (periodo === 's2') return mes >= 7  && mes <= 12;
            return true;
        }

        function aplicarFiltros() {
            var cards   = grid.querySelectorAll('.metas-avance-card');
            var visible = 0;
            cards.forEach(function(c){
                var okAmb  = filtroAmbito === 'todos' || c.getAttribute('data-ambito') === filtroAmbito;
                var okTime = mesEnPeriodo(c.getAttribute('data-mes'), filtroTiempo);
                var show   = okAmb && okTime;
                c.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (countEl) countEl.textContent = visible + ' meta' + (visible !== 1 ? 's' : '');
            if (empty)   empty.style.display = visible === 0 ? 'block' : 'none';
        }

        var ftEl = document.getElementById('filtro-tiempo');
        if (ftEl) ftEl.addEventListener('click', function(e){
            var b = e.target.closest('.metas-filtro-btn');
            if (!b) return;
            this.querySelectorAll('.metas-filtro-btn').forEach(function(x){ x.classList.remove('active'); });
            b.classList.add('active');
            filtroTiempo = b.getAttribute('data-tiempo');
            aplicarFiltros();
        });

        var faEl = document.getElementById('filtro-ambito');
        if (faEl) faEl.addEventListener('click', function(e){
            var b = e.target.closest('.metas-filtro-btn');
            if (!b) return;
            this.querySelectorAll('.metas-filtro-btn').forEach(function(x){ x.classList.remove('active'); });
            b.classList.add('active');
            filtroAmbito = b.getAttribute('data-ambito');
            aplicarFiltros();
        });
    })();

    (function(){
        ['chartCumplimientoAmbitoTab','chartCumplimientoAmbito'].forEach(function(id){
            var canvas = document.getElementById(id);
            if (!canvas) return;
            canvas.dispatchEvent(new CustomEvent('metas:suggestedMin', {
                detail: { suggestedMin: -10 },
                bubbles: true
            }));
        });
    })();

    (function(){
        var estadoFiltroTab    = { tipo: null, valor: null, nombre: null };
        var tablaDetalle       = document.getElementById('tabla-metas-panel');
        var badgeFiltroActivo  = document.getElementById('detalle-filtro-activo-badge');
        var textoFiltroActivo  = document.getElementById('detalle-filtro-activo-texto');
        var btnLimpiarFiltro   = document.getElementById('btn-detalle-limpiar-filtro');
        var sinDatosMes        = document.getElementById('metas-sin-datos-mes');

        function aplicarFiltroTabEnDetalle() {
            if (!tablaDetalle) return;
            var rows     = tablaDetalle.querySelectorAll('tbody tr.metas-full-row');
            var visibles = 0;
            var tipo     = estadoFiltroTab.tipo;
            var valor    = estadoFiltroTab.valor;

            rows.forEach(function(row) {
                var mostrar = true;
                if (tipo === 'ambito' && valor !== null) {
                    mostrar = (String(row.getAttribute('data-ambito-id')) === String(valor));
                } else if (tipo === 'mes' && valor !== null) {
                    mostrar = (String(row.getAttribute('data-mes-meta')) === String(valor));
                }
                if (mostrar) {
                    row.classList.remove('detalle-oculto');
                    visibles++;
                    row.classList.remove('detalle-flash');
                    void row.offsetWidth;
                    row.classList.add('detalle-flash');
                    setTimeout(function(r){ r.classList.remove('detalle-flash'); }, 600, row);
                } else {
                    row.classList.add('detalle-oculto');
                }
            });

            if (sinDatosMes) {
                sinDatosMes.style.display = (visibles === 0 && tipo !== null) ? 'block' : 'none';
            }
            if (tipo !== null && badgeFiltroActivo && textoFiltroActivo) {
                textoFiltroActivo.textContent = (tipo === 'ambito' ? 'Ámbito: ' : 'Mes: ') + (estadoFiltroTab.nombre || '');
                badgeFiltroActivo.style.display = 'inline-flex';
            } else if (badgeFiltroActivo) {
                badgeFiltroActivo.style.display = 'none';
            }
        }

        function limpiarFiltroTab() {
            estadoFiltroTab = { tipo: null, valor: null, nombre: null };
            document.querySelectorAll('.tabla-tab-row--active').forEach(function(r){
                r.classList.remove('tabla-tab-row--active');
            });
            if (tablaDetalle) {
                tablaDetalle.querySelectorAll('tbody tr.metas-full-row').forEach(function(row){
                    row.classList.remove('detalle-oculto', 'detalle-flash');
                });
            }
            if (sinDatosMes)       sinDatosMes.style.display = 'none';
            if (badgeFiltroActivo) badgeFiltroActivo.style.display = 'none';
        }

        function scrollHaciaDetalle() {
            var contenedor = tablaDetalle ? tablaDetalle.closest('.tables-container') : null;
            if (contenedor) {
                setTimeout(function(){ contenedor.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }, 80);
            }
        }

        function manejarClicFilaTab(row, tipo, valor, nombre) {
            var estaActivo = row.classList.contains('tabla-tab-row--active');
            document.querySelectorAll('.tabla-tab-row--active').forEach(function(r){
                r.classList.remove('tabla-tab-row--active');
            });
            if (estaActivo) {
                limpiarFiltroTab();
            } else {
                row.classList.add('tabla-tab-row--active');
                estadoFiltroTab = { tipo: tipo, valor: valor, nombre: nombre };
                aplicarFiltroTabEnDetalle();
                scrollHaciaDetalle();
            }
        }

        (function(){
            var tabla = document.getElementById('tabla-tab-ambito');
            if (!tabla) return;
            tabla.addEventListener('click', function(e){
                var row = e.target.closest('.tabla-tab-row--ambito');
                if (!row || row.classList.contains('tabla-tab-row--disabled')) return;
                manejarClicFilaTab(row, 'ambito', row.getAttribute('data-filtro-ambito'), row.getAttribute('data-nombre-ambito'));
            });
        })();

        (function(){
            var tabla = document.getElementById('tabla-tab-mes');
            if (!tabla) return;
            tabla.addEventListener('click', function(e){
                var row = e.target.closest('.tabla-tab-row--mes');
                if (!row || row.classList.contains('tabla-tab-row--disabled')) return;
                manejarClicFilaTab(row, 'mes', row.getAttribute('data-filtro-mes'), row.getAttribute('data-nombre-mes'));
            });
        })();

        if (btnLimpiarFiltro) {
            btnLimpiarFiltro.addEventListener('click', function(e){
                e.stopPropagation();
                limpiarFiltroTab();
            });
        }

        var btnVerTodos = document.getElementById('btn-metas-ver-todos');
        if (btnVerTodos) {
            btnVerTodos.addEventListener('click', function(){
                estadoFiltroTab = { tipo: null, valor: null, nombre: null };
                document.querySelectorAll('.tabla-tab-row--active').forEach(function(r){
                    r.classList.remove('tabla-tab-row--active');
                });
                if (badgeFiltroActivo) badgeFiltroActivo.style.display = 'none';
            });
        }

    })();

})();
</script>
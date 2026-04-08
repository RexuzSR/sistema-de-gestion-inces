<!-- ════════════════════════════════════════════════════════════
     SECCIÓN 5 — METAS
     Vista: app/vista/Dashboard/seccion_metas.php
     Datos: app/controlador/Dashboard/datos_metas.php
     Variables requeridas:
       $kMetas, $metasDetalle, $cumplimientoAmbito,
       $metasPorMes, $s_meta, $anioActual
════════════════════════════════════════════════════════════ -->
<div class="dashboard-section" id="section-metas">
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

        <!-- ── GRÁFICAS METAS ── -->
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

                <!-- TAB: Cumplimiento por Ámbito -->
                <div class="chart-tab-panel active" id="chart-panel-metas-ambito">
                    <p class="chart-panel-title">Promedio de cumplimiento por ámbito</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartCumplimientoAmbitoTab"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Ámbito</th><th class="text-center">Metas</th><th class="text-center">Cumplidas</th><th class="text-center">%</th></tr></thead>
                                <tbody>
                                <?php foreach ($cumplimientoAmbito as $amb):
                                    $aSem2   = ($amb['pct_promedio'] >= 95) ? 'verde' : (($amb['pct_promedio'] >= 80) ? 'amarillo' : 'rojo');
                                    $aColor2 = ['verde' => '#28c76f', 'amarillo' => '#f59e0b', 'rojo' => '#ef4444'][$aSem2];
                                ?>
                                    <tr>
                                        <td class="fw-semibold" style="font-size:.75rem;"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                        <td class="text-center"><?php echo $amb['total_metas']; ?></td>
                                        <td class="text-center text-success fw-semibold"><?php echo $amb['cumplidas']; ?></td>
                                        <td class="text-center">
                                            <span class="badge-mini" style="background:<?php echo $aColor2; ?>22;color:<?php echo $aColor2; ?>;">
                                                <?php echo $amb['pct_promedio']; ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($cumplimientoAmbito)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-2">Sin metas activas.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB: Metas por Mes -->
                <div class="chart-tab-panel" id="chart-panel-metas-mes">
                    <p class="chart-panel-title">Metas activas por mes — año <?php echo $anioActual; ?> · clic para filtrar tabla</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartMetasMes"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Mes</th><th class="text-center">Metas</th><th style="min-width:70px;">Barra</th></tr></thead>
                                <tbody>
                                <?php
                                $mesesNombres  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                $maxMetaMes    = max(array_merge($metasPorMes, [1]));
                                $mesActualIdx  = (int)date('n') - 1;
                                foreach ($metasPorMes as $mi => $mv):
                                    $mBar    = $maxMetaMes > 0 ? round($mv / $maxMetaMes * 100) : 0;
                                    $esActual = ($mi === $mesActualIdx);
                                ?>
                                    <tr<?php echo $esActual ? ' style="background:#fee2e210;"' : ''; ?>>
                                        <td class="fw-semibold" style="<?php echo $esActual ? 'color:#ef4444;' : ''; ?>">
                                            <?php echo $mesesNombres[$mi]; ?>
                                            <?php if ($esActual): ?>
                                                <span class="badge-mini" style="background:#fee2e2;color:#b91c1c;">hoy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center fw-bold" style="color:<?php echo $mv > 0 ? '#ef4444' : '#94a3b8'; ?>;"><?php echo $mv; ?></td>
                                        <td>
                                            <div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                                                <div style="width:<?php echo $mBar; ?>%;height:7px;background:#ef4444;border-radius:4px;opacity:<?php echo $mv > 0 ? '1' : '.3'; ?>;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /chart-tabs-wrapper metas -->

        <!-- ── TABLAS DE METAS ── -->
        <div class="tables-container bg-light p-3 rounded-3 mt-3">

            <!-- Cumplimiento por ámbito (gráfica + tabla) -->
            <?php if (!empty($cumplimientoAmbito)): ?>
            <div class="panel-section">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#8b5cf6;"></span>
                    <span class="panel-section-label">Cumplimiento por Ámbito</span>
                    <small class="text-muted ms-auto">Meta 100% · Verde ≥95% · Amarillo ≥80% · Rojo &lt;80%</small>
                </div>
                <div class="row g-3 align-items-start">
                    <div class="col-md-7">
                        <div class="ambito-chart-wrap"><canvas id="chartCumplimientoAmbito"></canvas></div>
                    </div>
                    <div class="col-md-5">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Ámbito</th><th class="text-center">Metas</th><th class="text-center">Cumplidas</th><th class="text-center">Promedio</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($cumplimientoAmbito as $amb):
                                    $aSem = ($amb['pct_promedio'] >= 95) ? 'verde' : (($amb['pct_promedio'] >= 80) ? 'amarillo' : 'rojo');
                                ?>
                                    <tr>
                                        <td class="fw-semibold" style="font-size:.8rem;"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                        <td class="text-center"><?php echo $amb['total_metas']; ?></td>
                                        <td class="text-center text-success fw-semibold"><?php echo $amb['cumplidas']; ?></td>
                                        <td class="text-center">
                                            <span class="semaforo-badge semaforo-<?php echo $aSem; ?>" style="font-size:.68rem;">
                                                <?php echo $amb['pct_promedio']; ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Calendario de metas por mes -->
            <div class="panel-section mt-3">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#ef4444;"></span>
                    <span class="panel-section-label">Metas por Mes</span>
                    <small class="text-muted ms-auto">Haz clic en un mes para filtrar la tabla</small>
                </div>
                <div class="kpi-metas-calendar-wrap"><canvas id="chartMetasMesTabla"></canvas></div>
            </div>

            <!-- Detalle de metas -->
            <div class="panel-section mt-3">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#ef4444;"></span>
                    <span class="panel-section-label">Detalle de Metas</span>
                    <small class="text-muted ms-auto">Meta ≥95% verde · ≥80% atención · &lt;80% crítico</small>
                </div>

                <!-- Toolbar de filtro por mes -->
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

                <!-- Aviso sin datos para el mes filtrado -->
                <div id="metas-sin-datos-mes" class="metas-sin-datos-mes" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                    <p>No hay metas registradas para este mes.</p>
                </div>

                <?php if (empty($metasDetalle)): ?>
                    <div class="text-center py-4 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                        <p class="mt-2">No hay metas activas registradas.</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped align-middle mb-0" id="tabla-metas-panel">
                        <thead class="table-light">
                            <tr>
                                <th>Ámbito</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Objetivo</th>
                                <th style="min-width:200px;">Progreso</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($metasDetalle as $m):
                            $mPct     = $m['porcentaje_cumplimiento'] ?? 0;
                            $mSem     = ($mPct >= 95) ? 'verde' : (($mPct >= 80) ? 'amarillo' : 'rojo');
                            $cumplida = ($m['estado_cumplimiento'] === 'Cumplida');
                            $mesMeta  = isset($m['mes_meta']) && $m['mes_meta'] !== null ? (int)$m['mes_meta'] : 0;
                        ?>
                            <tr data-mes-meta="<?php echo $mesMeta; ?>"
                                data-id="<?php echo $m['id_meta']; ?>"
                                class="metas-full-row meta-row-link<?php echo $cumplida ? ' metas-row-cumplida' : ''; ?>"
                                style="cursor:pointer;">
                                <td>
                                    <div class="metas-ambito-cell">
                                        <div class="metas-ambito-dot metas-dot-<?php echo $mSem; ?>"></div>
                                        <span class="fw-semibold"><?php echo htmlspecialchars($m['nombre_ambito'] ?? 'General'); ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="metas-tipo-badge"><?php echo htmlspecialchars($m['tipo_objetivo']); ?></span>
                                </td>
                                <td class="text-center fw-bold" style="font-size:1rem;"><?php echo $m['meta_objetivo']; ?></td>
                                <td>
                                    <div class="metas-progress-cell">
                                        <div class="metas-progress-track">
                                            <div class="metas-progress-fill semaforo-fill-<?php echo $mSem; ?>" style="width:<?php echo min($mPct, 100); ?>%;"></div>
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

            </div><!-- /panel-section detalle -->
        </div><!-- /tables-container -->
    </div><!-- /kpi-panel-inner -->
</div><!-- /section-metas -->
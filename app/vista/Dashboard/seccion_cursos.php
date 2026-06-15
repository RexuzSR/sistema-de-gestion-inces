<!-- ════════════════════════════════════════════════════════════
     SECCIÓN 3 — CURSOS
     Vista: app/vista/Dashboard/seccion_cursos.php
     Datos: app/controlador/Dashboard/datos_cursos.php
════════════════════════════════════════════════════════════ -->
<div class="dashboard-section" id="section-cursos">

    <!-- ══════════════════════════════════════════════════════════════
         BLOQUE: Cursos iniciados por mes — año actual
    ══════════════════════════════════════════════════════════════════ -->
    <div class="mt-4" id="cursos-mensual-bloque">
        <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem 1rem;box-shadow:0 2px 12px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.1rem;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#e2fce8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="#28c76f" viewBox="0 0 16 16">
                            <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:.72rem;color:#64748b;line-height:1.2;font-weight:500;">Cursos iniciados por mes &middot; <?php echo $anioActual; ?></p>
                        <p style="margin:0;font-size:.88rem;font-weight:700;color:#1e293b;line-height:1.3;">Actividad de formaciones en el a&ntilde;o</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <?php
                    $cTotalAnio     = array_sum($cursosPorMes);
                    $cMesMax        = array_search(max($cursosPorMes), $cursosPorMes);
                    $cMesActualIdx  = (int)date('n') - 1;
                    $cMesesNombres  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                    $cMesesFull     = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    $cMesActualVal  = $cursosPorMes[$cMesActualIdx] ?? 0;
                    $cMesesConDatos = array_filter($cursosPorMes, fn($v) => $v > 0);
                    $cPromMensual   = count($cMesesConDatos) > 0 ? round(array_sum($cursosPorMes) / count($cMesesConDatos), 1) : 0;
                    ?>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#28c76f;line-height:1;"><?php echo $cTotalAnio; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Total a&ntilde;o</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#7367f0;line-height:1;"><?php echo $cMesActualVal; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;"><?php echo $cMesesNombres[$cMesActualIdx]; ?> actual</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#0ea5e9;line-height:1;"><?php echo $cPromMensual; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Promedio/mes</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#f59e0b;line-height:1;"><?php echo max($cursosPorMes); ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Pico &middot; <?php echo $cMesesNombres[$cMesMax]; ?></div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#8b5cf6;line-height:1;"><?php echo $kPromedio['promedio']; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Prom. Partic.</div>
                    </div>
                </div>
            </div>
            <div style="position:relative;height:190px;width:100%;"><canvas id="chartCursosMensual"></canvas></div>
            <div style="display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#28c76f;"></span>Mes con cursos</span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#28c76f33;"></span>Sin cursos</span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#7367f0;"></span>Mes actual (<?php echo $cMesesFull[$cMesActualIdx]; ?>)</span>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         PANEL PRINCIPAL: Análisis de Cursos
    ══════════════════════════════════════════════════════════════════ -->
    <div class="kpi-panel-inner mt-4" style="border-top-color:#28c76f;">
        <div class="kpi-panel-header">
            <h6 class="kpi-panel-titulo" style="color:#28a745;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                An&aacute;lisis de Cursos
            </h6>
            <small class="text-muted">Distribuci&oacute;n actual del cat&aacute;logo de formaciones</small>
        </div>

        <!-- GRÁFICAS CURSOS -->
        <div class="chart-tabs-wrapper mt-3">
            <nav class="chart-tabs-nav" id="chart-nav-cursos">
                <button class="chart-tab-btn active" data-section="cursos" data-chart="estatus">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                    Por Estatus
                </button>
                <button class="chart-tab-btn" data-section="cursos" data-chart="modalidad">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793z"/></svg>
                    Por Modalidad
                </button>
                <button class="chart-tab-btn" data-section="cursos" data-chart="turno">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
                    Por Turno
                </button>
                <button class="chart-tab-btn" data-section="cursos" data-chart="ambito">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.786-.4 2.363.158 3.27.465.527.181.86.294.73.035C10.044 2.31 7.96 1.5 7 1.5c-1 0-3.5 1.326-4.96 2.826zm7.027 5.978c.29.29.29.76 0 1.05a.74.74 0 0 1-1.05 0 .74.74 0 0 1 0-1.05.74.74 0 0 1 1.05 0"/></svg>
                    &Aacute;mbito Solicitado
                </button>
                <button class="chart-tab-btn" data-section="cursos" data-chart="formacion">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.742c-.934-.53-2.118-.602-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                    Formaci&oacute;n Solicitada
                </button>
            </nav>
            <div class="chart-tabs-body">

                <!-- TAB: Por Estatus -->
                <div class="chart-tab-panel active" id="chart-panel-cursos-estatus">
                    <p class="chart-panel-title">Distribuci&oacute;n por estatus de curso</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartCursosEstatus"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Estatus</th><th class="text-center">Cantidad</th><th style="min-width:80px;">Barra</th></tr></thead>
                                <tbody>
                                <?php
                                $totalEst = array_sum(array_column($cursosEstatus, 't'));
                                $palEst   = ['#28c76f','#0ea5e9','#f59e0b','#ef4444','#8b5cf6','#6c757d'];
                                foreach ($cursosEstatus as $i => $e):
                                    $ep = $totalEst > 0 ? round($e['t'] / $totalEst * 100) : 0;
                                    $ec = $palEst[$i % count($palEst)];
                                ?>
                                    <tr style="cursor:default;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($e['estatus']); ?></td>
                                        <td class="text-center fw-bold"><?php echo $e['t']; ?></td>
                                        <td><div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;"><div style="width:<?php echo $ep; ?>%;height:7px;background:<?php echo $ec; ?>;border-radius:4px;"></div></div></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0m-.5 5.5a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zM5 6.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
                                Totales por estatus &mdash; sin navegaci&oacute;n directa
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Modalidad -->
                <div class="chart-tab-panel" id="chart-panel-cursos-modalidad">
                    <p class="chart-panel-title">Distribuci&oacute;n por modalidad</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartCursosModalidad"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Modalidad</th><th class="text-center">Cursos</th><th style="min-width:80px;">%</th></tr></thead>
                                <tbody>
                                <?php
                                $totalMod = array_sum(array_column($cursosModalidad, 't'));
                                $palMod   = ['#0ea5e9','#7367f0','#28c76f'];
                                foreach ($cursosModalidad as $i => $m):
                                    $mp = $totalMod > 0 ? round($m['t'] / $totalMod * 100) : 0;
                                    $mc = $palMod[$i % count($palMod)];
                                ?>
                                    <tr style="cursor:default;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($m['modalidad']); ?></td>
                                        <td class="text-center fw-bold"><?php echo $m['t']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $mc; ?>22;color:<?php echo $mc; ?>;"><?php echo $mp; ?>%</span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0m-.5 5.5a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zM5 6.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
                                Totales por modalidad &mdash; sin navegaci&oacute;n directa
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Turno -->
                <div class="chart-tab-panel" id="chart-panel-cursos-turno">
                    <p class="chart-panel-title">Distribuci&oacute;n por turno</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartCursosTurno"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Turno</th><th class="text-center">Cursos</th><th style="min-width:80px;">%</th></tr></thead>
                                <tbody>
                                <?php
                                $totalTur = array_sum(array_column($cursosTurno, 't'));
                                $palTur   = ['#ff9f43','#0ea5e9'];
                                foreach ($cursosTurno as $i => $t):
                                    $tp = $totalTur > 0 ? round($t['t'] / $totalTur * 100) : 0;
                                    $tc = $palTur[$i % count($palTur)];
                                ?>
                                    <tr style="cursor:default;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($t['turno']); ?></td>
                                        <td class="text-center fw-bold"><?php echo $t['t']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $tc; ?>22;color:<?php echo $tc; ?>;"><?php echo $tp; ?>%</span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0m-.5 5.5a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3.5a.5.5 0 0 0 0-1zM5 6.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm0 3a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
                                Totales por turno &mdash; sin navegaci&oacute;n directa
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Ámbito Solicitado -->
                <div class="chart-tab-panel" id="chart-panel-cursos-ambito">
                    <p class="chart-panel-title">&Aacute;mbito m&aacute;s solicitado &mdash; <?php echo $mesActualES . ' ' . $anioActual; ?></p>
                    <?php if (empty($ambitoSolicitadoMes)): ?>
                        <p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin inscripciones registradas este mes.</p>
                    <?php else: ?>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartAmbitoSolicitadoTab"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>#</th><th>&Aacute;mbito</th><th class="text-center">Inscrip.</th><th class="text-center">Cursos</th><th style="min-width:100px;">% del mes</th></tr></thead>
                                <tbody>
                                <?php foreach ($ambitoSolicitadoMes as $i => $amb):
                                    $pctAmb = $totalInscMes > 0 ? round($amb['inscripciones'] / $totalInscMes * 100, 1) : 0;
                                    $ac     = $coloresFase2[$i % count($coloresFase2)];
                                ?>
                                    <tr style="cursor:default;">
                                        <td style="color:#94a3b8;font-weight:600;font-size:.7rem;"><?php echo str_pad($i+1, 2, '0', STR_PAD_LEFT); ?></td>
                                        <td class="fw-semibold"><?php if ($i === 0): ?><span style="color:#f59e0b;">&#9733;</span> <?php endif; ?><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                        <td class="text-center fw-bold" style="color:<?php echo $ac; ?>;"><?php echo $amb['inscripciones']; ?></td>
                                        <td class="text-center text-muted"><?php echo $amb['cursos_activos']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="kpi-adv-barra-fondo flex-grow-1" style="height:7px;"><div style="width:<?php echo $pctAmb; ?>%;height:7px;background:<?php echo $ac; ?>;border-radius:4px;"></div></div>
                                                <span style="font-size:.7rem;font-weight:600;color:<?php echo $ac; ?>;min-width:30px;"><?php echo $pctAmb; ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <p class="text-muted mt-2 mb-0" style="font-size:.7rem;">Total inscripciones este mes: <strong><?php echo $totalInscMes; ?></strong></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- TAB: Formación más solicitada por mes -->
                <div class="chart-tab-panel" id="chart-panel-cursos-formacion">
                    <p class="chart-panel-title">Formaci&oacute;n m&aacute;s solicitada por mes &mdash; <?php echo $anioActual; ?></p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartFormacionSolicitadaTab"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th>Curso m&aacute;s solicitado</th>
                                        <th class="text-center">Inscrip.</th>
                                        <th class="text-center">% Ocupaci&oacute;n</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $cMesesES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                                $cMesesFull = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                foreach ($formacionSolicitadaMes as $i => $fm):
                                    $mesNombre = $cMesesFull[$i];
                                    $nombreCurso = htmlspecialchars($fm['nombre_curso']);
                                    $inscripciones = $fm['inscripciones'];
                                    $maxPart = $fm['max_participantes'];
                                    $pctOcupacion = $maxPart > 0 ? round($inscripciones / $maxPart * 100, 1) : 0;
                                    $rowColor = $coloresFase2[$i % count($coloresFase2)];
                                ?>
                                    <tr class="<?php echo $inscripciones > 0 ? 'curso-row-link' : ''; ?>" data-nombre="<?php echo htmlspecialchars($fm['nombre_curso']); ?>" style="<?php echo $inscripciones > 0 ? 'cursor:pointer;' : ''; ?>">
                                        <td class="fw-semibold"><?php echo $cMesesES[$i]; ?></td>
                                        <td class="fw-semibold" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo $nombreCurso; ?>">
                                            <?php echo $nombreCurso; ?>
                                        </td>
                                        <td class="text-center fw-bold" style="color:<?php echo $rowColor; ?>;"><?php echo $inscripciones; ?></td>
                                        <td class="text-center">
                                            <?php if ($inscripciones > 0): ?>
                                                <span class="badge-mini" style="background:<?php echo $rowColor; ?>22;color:<?php echo $rowColor; ?>;font-weight:600;"><?php echo $pctOcupacion; ?>%</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                                Haz clic en una fila para ver el detalle del curso
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /chart-tabs-wrapper cursos -->

        <!-- ══════════════════════════════════════════════════════════
             BLOQUE: PROGRAMAS EJECUTADOS SEGÚN PLANIFICACIÓN
        ══════════════════════════════════════════════════════════ -->
        <div class="tables-container bg-light p-3 rounded-3 mt-3" id="bloque-ejecucion">
            <div class="panel-section">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#10b981;"></span>
                    <span class="panel-section-label">Programas Ejecutados seg&uacute;n Planificaci&oacute;n</span>
                </div>
                <div class="ej-filtros-wrap">
                    <div class="ej-filtros-grupo">
                        <button class="ej-filtro-btn active" data-periodo="mes">Mes</button>
                        <button class="ej-filtro-btn" data-periodo="trimestre">Trimestre</button>
                        <button class="ej-filtro-btn" data-periodo="semestre">Semestre</button>
                        <button class="ej-filtro-btn" data-periodo="anio">A&ntilde;o</button>
                    </div>
                    <div class="ej-filtros-sel">
                        <div id="ej-sel-mes">
                            <select id="ej-mes-select" class="ej-select">
                                <?php foreach ($mesesES as $n => $nm): ?>
                                    <option value="<?php echo $n; ?>" <?php echo $n == $mesActualNum ? 'selected' : ''; ?>><?php echo $nm; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="ej-sel-trimestre" style="display:none;">
                            <select id="ej-trim-select" class="ej-select">
                                <?php for ($t = 1; $t <= 4; $t++): ?>
                                    <option value="<?php echo $t; ?>" <?php echo $t == $trimestreActualNum ? 'selected' : ''; ?>>T<?php echo $t; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div id="ej-sel-semestre" style="display:none;">
                            <select id="ej-sem-select" class="ej-select">
                                <option value="1" <?php echo $semestreActualNum === 1 ? 'selected' : ''; ?>>1er semestre (Ene&ndash;Jun)</option>
                                <option value="2" <?php echo $semestreActualNum === 2 ? 'selected' : ''; ?>>2do semestre (Jul&ndash;Dic)</option>
                            </select>
                        </div>
                        <span style="color:#e2e8f0;font-size:1.1rem;user-select:none;">|</span>
                        <div id="ej-sel-anio">
                            <select id="ej-anio-select" class="ej-select">
                                <?php foreach ($aniosDisponibles as $ay): ?>
                                    <option value="<?php echo $ay; ?>" <?php echo $ay == $anioActualNum ? 'selected' : ''; ?>><?php echo $ay; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ej-cards-row">
                    <div class="ej-card">
                        <div class="ej-card-icon" style="background:#d1fae5;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#10b981" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg></div>
                        <div class="ej-card-body">
                            <span class="ej-card-val semaforo-text-<?php echo $s_ejecucion; ?>" id="ej-pct-val"><?php echo $kEjecucion['pct']; ?>%</span>
                            <span class="ej-card-lbl">% Iniciados</span>
                            <span class="ej-card-sub" id="ej-iniciados-sub"><?php echo $kEjecucion['ejecutados']; ?> de <?php echo $kEjecucion['planificados']; ?> planificados</span>
                        </div>
                        <span class="ej-card-hint">Ver detalle</span>
                    </div>
                    <div class="ej-card ej-card-clickable" id="card-planificados" data-tipo="pendiente" style="cursor:pointer;">
                        <div class="ej-card-icon" style="background:#dbeafe;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#3b82f6" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg></div>
                        <div class="ej-card-body">
                            <span class="ej-card-val" style="color:#1e293b;" id="ej-plan-val"><?php echo $kEjecucion['planificados']; ?></span>
                            <span class="ej-card-lbl">Planificados</span>
                            <span class="ej-card-sub" id="ej-plan-sub">pendientes de iniciar</span>
                        </div>
                        <span class="ej-card-hint">Ver listado</span>
                    </div>
                    <div class="ej-card ej-card-clickable" id="card-iniciados" data-tipo="iniciado" style="cursor:pointer;">
                        <div class="ej-card-icon" style="background:#f0fdf4;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#22c55e" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg></div>
                        <div class="ej-card-body">
                            <span class="ej-card-val" style="color:#1e293b;" id="ej-ejec-val"><?php echo $kEjecucion['ejecutados']; ?></span>
                            <span class="ej-card-lbl">Iniciados</span>
                            <span class="ej-card-sub" id="ej-ejec-sub">en formaci&oacute;n</span>
                        </div>
                        <span class="ej-card-hint">Ver listado</span>
                    </div>
                    <div class="ej-card ej-card-diferidos ej-card-clickable" id="card-diferidos" data-tipo="diferido" style="cursor:pointer;">
                        <div class="ej-card-icon" style="background:#fef3c7;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#d97706" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg></div>
                        <div class="ej-card-body">
                            <span class="ej-card-val" style="color:#d97706;" id="ej-dif-val">0</span>
                            <span class="ej-card-lbl">Diferidos</span>
                            <span class="ej-card-sub" id="ej-dif-sub">en el periodo</span>
                        </div>
                        <span class="ej-card-hint">Ver listado</span>
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-2">
                    <div class="col-md-8">
                        <div style="position:relative;margin-bottom:6px;">
                            <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#64748b;margin-bottom:4px;">
                                <span>0%</span>
                                <span class="fw-semibold" id="ej-barra-label" style="color:<?php echo $s_ejecucion==='verde'?'#28c76f':($s_ejecucion==='amarillo'?'#f59e0b':'#ef4444'); ?>;">Actual: <span id="ej-barra-pct"><?php echo $kEjecucion['pct']; ?>%</span></span>
                                <span>100%</span>
                            </div>
                            <div style="height:16px;border-radius:8px;background:#f1f5f9;position:relative;overflow:visible;">
                                <div id="ej-barra-fill" style="position:absolute;top:0;left:0;width:<?php echo min($kEjecucion['pct'],100); ?>%;height:16px;border-radius:8px;background:<?php echo $s_ejecucion==='verde'?'#28c76f':($s_ejecucion==='amarillo'?'#f59e0b':'#ef4444'); ?>;transition:width .7s ease,background .4s ease;"></div>
                                <div style="position:absolute;top:-4px;left:70%;transform:translateX(-50%);width:2px;height:24px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
                                <div style="position:absolute;top:-4px;left:85%;transform:translateX(-50%);width:2px;height:24px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
                            </div>
                            <div style="position:relative;height:18px;margin-top:3px;">
                                <span style="position:absolute;left:70%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;">70%</span>
                                <span style="position:absolute;left:85%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;">85%</span>
                            </div>
                        </div>
                        <div id="ej-periodo-label" style="font-size:.72rem;color:#64748b;margin-top:4px;text-align:center;">Meta &ge; 85% &middot; T<?php echo $trimestreActualNum; ?> <?php echo $anioActualNum; ?></div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#f8fafc;border-radius:8px;padding:12px;border:1px solid rgba(0,0,0,.06);font-size:.75rem;">
                            <p style="font-weight:600;color:#1e293b;margin-bottom:8px;">Sem&aacute;foro CMI</p>
                            <div style="display:flex;flex-direction:column;gap:5px;">
                                <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#ef4444;display:inline-block;"></span><span style="color:#64748b;">Cr&iacute;tico: &lt; 75%</span></div>
                                <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#f59e0b;display:inline-block;"></span><span style="color:#64748b;">Atenci&oacute;n: 75&ndash;84%</span></div>
                                <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#28c76f;display:inline-block;"></span><span style="color:#64748b;">&Oacute;ptimo: &ge; 85%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="ej-detalle-wrap" style="display:none;margin-top:20px;">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" id="ej-detalle-dot" style="background:#3b82f6;"></span>
                        <span class="panel-section-label" id="ej-detalle-titulo">Detalle de cursos</span>
                        <button id="btn-cerrar-ej-detalle" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#64748b;font-size:.8rem;padding:2px 8px;border-radius:4px;" title="Cerrar">&#x2715; cerrar</button>
                    </div>
                    <div id="ej-detalle-body" class="table-responsive mt-2"></div>
                </div>
            </div>
        </div><!-- /bloque-ejecucion -->

        <!-- TABLA: Cursos inscritos + género + ocupación + tendencia -->
        <div class="tables-container bg-light p-3 rounded-3 mt-3">
            <div class="panel-section">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#0ea5e9;"></span>
                    <span class="panel-section-label">Cursos &mdash; Inscritos, G&eacute;nero y Utilizaci&oacute;n de Cupos</span>
                    <span class="text-muted ms-auto" style="font-size:.72rem;">
                        Mes actual: <strong><?php echo $mesActualES . ' ' . $anioActual; ?></strong>
                        &middot; Haz clic en una fila para ver el curso
                    </span>
                </div>
                <p class="text-muted mb-2" style="font-size:.73rem;">
                    Meta &ge;85% ocupaci&oacute;n &middot;
                    <span class="semaforo-text-rojo fw-semibold">Rojo &lt;70%</span> &middot;
                    <span class="semaforo-text-amarillo fw-semibold">Amarillo 70&ndash;89%</span> &middot;
                    <span class="semaforo-text-verde fw-semibold">Verde &ge;90%</span> &middot;
                    <em>&uarr; Subi&oacute; &middot; &darr; Baj&oacute; &middot; &mdash; Sin cambio vs mes anterior</em> &middot;
                    <span style="color:#0dcaf0;">&#9794; Masculino</span> &middot; <span style="color:#e83e8c;">&#9792; Femenino</span>
                </p>
                <?php if (empty($topCursosMesActual) && empty($cursosDetalle)): ?>
                    <p class="text-muted py-3 text-center">No hay cursos activos con datos disponibles.</p>
                <?php else:
                    $topNombres = array_column($topCursosMesActual, 'nombre_curso');
                    $filasTop   = $topCursosMesActual;
                    $filasResto = array_filter($cursosDetalle, fn($c) => !in_array($c['nombre_curso'], $topNombres));
                ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th class="text-center">Inscritos</th>
                                <th class="text-center" style="color:#0dcaf0;">&#9794;</th>
                                <th class="text-center" style="color:#e83e8c;">&#9792;</th>
                                <th class="text-center">Cupos Disp.</th>
                                <th class="text-center">Cupo M&aacute;x.</th>
                                <th style="min-width:140px;">Ocupaci&oacute;n</th>
                                <th class="text-center">Tendencia</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filasTop as $tc):
                            $ocPct = $tc['max_participantes'] > 0 ? round($tc['num_inscritos'] / $tc['max_participantes'] * 100, 1) : 0;
                            $ocSem = ($ocPct >= 90) ? 'verde' : (($ocPct >= 70) ? 'amarillo' : 'rojo');
                            $disp  = max(0, (int)$tc['max_participantes'] - (int)$tc['num_inscritos']);
                            $dispColor = ($disp <= 0) ? '#ef4444' : (($disp <= 5) ? '#f59e0b' : '#28a745');
                            if ($tc['tendencia'] === 'sube')     { $tendIcon = '&uarr;'; $tendClass = 'tendencia-sube';  $tendTitle = '+' . $tc['tendencia_diff'] . ' vs mes anterior'; }
                            elseif ($tc['tendencia'] === 'baja') { $tendIcon = '&darr;'; $tendClass = 'tendencia-baja';  $tendTitle = '-' . $tc['tendencia_diff'] . ' vs mes anterior'; }
                            else                                 { $tendIcon = '&mdash;'; $tendClass = 'tendencia-igual'; $tendTitle = 'Sin cambio vs mes anterior'; }
                            $masc    = (int)($tc['masculino'] ?? 0);
                            $fem     = (int)($tc['femenino']  ?? 0);
                            $pctMasc = ($masc + $fem) > 0 ? round($masc / ($masc + $fem) * 100) : 0;
                        ?>
                            <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($tc['nombre_curso']); ?>" style="cursor:pointer;">
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($tc['nombre_curso']); ?>">
                                    <span class="fw-semibold"><?php echo htmlspecialchars($tc['nombre_curso']); ?></span>
                                </td>
                                <td class="text-center fw-bold"><?php echo $tc['num_inscritos']; ?></td>
                                <td class="text-center fw-semibold" style="color:#0dcaf0;"><?php echo $masc; ?></td>
                                <td class="text-center fw-semibold" style="color:#e83e8c;"><?php echo $fem; ?></td>
                                <td class="text-center fw-semibold" style="color:<?php echo $dispColor; ?>;"><?php echo $disp; ?></td>
                                <td class="text-center"><?php echo $tc['max_participantes']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="kpi-adv-barra-fondo flex-grow-1" style="height:9px;overflow:hidden;border-radius:4px;">
                                            <div style="display:flex;height:9px;width:<?php echo min($ocPct,100); ?>%;">
                                                <div style="width:<?php echo $pctMasc; ?>%;background:#0dcaf0;"></div>
                                                <div style="width:<?php echo 100-$pctMasc; ?>%;background:#e83e8c;"></div>
                                            </div>
                                        </div>
                                        <span class="small semaforo-text-<?php echo $ocSem; ?>" style="min-width:40px;"><?php echo $ocPct; ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="tendencia-badge <?php echo $tendClass; ?>" title="<?php echo $tendTitle; ?>"><?php echo $tendIcon; ?><?php if ($tc['tendencia'] !== 'igual'): ?> <?php echo $tc['tendencia_diff']; ?><?php endif; ?></span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($tc['nombre_estatusCurso'] ?? '&mdash;'); ?></span></td>
                            </tr>
                        <?php endforeach;
                        foreach ($filasResto as $c):
                            $uPct      = $c['uso_pct'] ?? 0;
                            $uSem      = ($uPct >= 90) ? 'verde' : (($uPct >= 70) ? 'amarillo' : 'rojo');
                            $disp      = max(0, (int)$c['cupos_disp']);
                            $dispColor = ($disp <= 0) ? '#ef4444' : (($disp <= 5) ? '#f59e0b' : '#28a745');
                            $masc2     = (int)($c['masculino'] ?? 0);
                            $fem2      = (int)($c['femenino']  ?? 0);
                            $pctMasc2  = ($masc2 + $fem2) > 0 ? round($masc2 / ($masc2 + $fem2) * 100) : 0;
                        ?>
                            <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($c['nombre_curso']); ?>" style="cursor:pointer;">
                                <td class="fw-semibold" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($c['nombre_curso']); ?>"><?php echo htmlspecialchars($c['nombre_curso']); ?></td>
                                <td class="text-center fw-bold"><?php echo $c['num_inscritos']; ?></td>
                                <td class="text-center fw-semibold" style="color:#0dcaf0;"><?php echo $masc2; ?></td>
                                <td class="text-center fw-semibold" style="color:#e83e8c;"><?php echo $fem2; ?></td>
                                <td class="text-center fw-semibold" style="color:<?php echo $dispColor; ?>;"><?php echo $disp; ?></td>
                                <td class="text-center"><?php echo $c['max_participantes']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="kpi-adv-barra-fondo flex-grow-1" style="height:9px;overflow:hidden;border-radius:4px;">
                                            <div style="display:flex;height:9px;width:<?php echo min($uPct,100); ?>%;">
                                                <div style="width:<?php echo $pctMasc2; ?>%;background:#0dcaf0;"></div>
                                                <div style="width:<?php echo 100-$pctMasc2; ?>%;background:#e83e8c;"></div>
                                            </div>
                                        </div>
                                        <span class="small semaforo-text-<?php echo $uSem; ?>" style="min-width:40px;"><?php echo $uPct; ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center"><span class="tendencia-badge tendencia-igual" title="Sin datos de tendencia este mes">&mdash;</span></td>
                                <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($c['nombre_estatusCurso']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($filasTop) && empty($filasResto)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-3">Sin cursos activos con datos disponibles.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="cupos-perdidos-resumen mt-3">
                    <div class="cp-item"><span class="cp-valor"><?php echo $kCuposPerdidos['cupos_vacios']; ?></span><span class="cp-lbl">Cupos sin ocupar</span></div>
                    <div class="cp-item"><span class="cp-valor"><?php echo $kCuposPerdidos['total_cupos']; ?></span><span class="cp-lbl">Cupos totales disponibles</span></div>
                    <div class="cp-item"><span class="cp-valor semaforo-text-<?php echo $s_util; ?>"><?php echo $kCuposPerdidos['pct_desperdicio']; ?>%</span><span class="cp-lbl">% capacidad sin aprovechar</span></div>
                    <div class="cp-item"><span class="cp-valor" style="color:#8b5cf6;"><?php echo $kPromedio['promedio']; ?></span><span class="cp-lbl">Prom. Participantes</span></div>
                    <div class="cp-item" style="border-left:2px solid #fde68a;padding-left:12px;"><span style="font-size:.72rem;color:#92400e;line-height:1.5;">Cada cupo vac&iacute;o representa un recurso formativo no aprovechado. Meta: &ge; 85% de ocupaci&oacute;n.</span></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══════════════════════════════════════════════════════
                 BLOQUE: FORMACIONES POR ÁMBITO
                 CAMBIOS: se eliminaron las cards de KPI y de instrucciones.
                 La gráfica ocupa el 100% del ancho. El contorno de selección
                 se dibuja mediante el plugin pluginContornoSeleccion.
            ══════════════════════════════════════════════════════════ -->
            <div class="panel-section mt-3" id="bloque-ambito">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#6366f1;"></span>
                    <span class="panel-section-label">Formaciones por &Aacute;mbito</span>
                </div>

                <!-- Filtros de periodo -->
                <div class="ej-filtros-wrap" style="margin-top:10px;">
                    <div class="ej-filtros-grupo">
                        <button class="amb-filtro-btn active" data-periodo="mes">Mes</button>
                        <button class="amb-filtro-btn" data-periodo="trimestre">Trimestre</button>
                        <button class="amb-filtro-btn" data-periodo="semestre">Semestre</button>
                        <button class="amb-filtro-btn" data-periodo="anio">A&ntilde;o</button>
                    </div>
                    <div class="ej-filtros-sel">
                        <div id="amb-sel-mes">
                            <select id="amb-mes-select" class="ej-select">
                                <?php foreach ($mesesES as $n => $nm): ?>
                                    <option value="<?php echo $n; ?>" <?php echo $n == $mesActualNum ? 'selected' : ''; ?>><?php echo $nm; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="amb-sel-trimestre" style="display:none;">
                            <select id="amb-trim-select" class="ej-select">
                                <?php for ($t = 1; $t <= 4; $t++): ?>
                                    <option value="<?php echo $t; ?>" <?php echo $t == $trimestreActualNum ? 'selected' : ''; ?>>T<?php echo $t; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div id="amb-sel-semestre" style="display:none;">
                            <select id="amb-sem-select" class="ej-select">
                                <option value="1" <?php echo $semestreActualNum===1?'selected':''; ?>>1er semestre (Ene&ndash;Jun)</option>
                                <option value="2" <?php echo $semestreActualNum===2?'selected':''; ?>>2do semestre (Jul&ndash;Dic)</option>
                            </select>
                        </div>
                        <span style="color:#e2e8f0;font-size:1.1rem;user-select:none;">|</span>
                        <div id="amb-sel-anio">
                            <select id="amb-anio-select" class="ej-select">
                                <?php foreach ($aniosDisponibles as $ay): ?>
                                    <option value="<?php echo $ay; ?>" <?php echo $ay==$anioActualNum?'selected':''; ?>><?php echo $ay; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="amb-contenido">
                    <!-- Gráfica a ancho completo — sin cards laterales -->
                    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 12px rgba(0,0,0,.04); border:1px solid rgba(0,0,0,.05);">
                        <div style="position:relative; height:280px; width:100%;"><canvas id="chartAmbitoPeriodo"></canvas></div>
                    </div>

                    <!-- Detalle de cursos — se muestra al hacer clic en una barra -->
                    <div class="mt-4 bg-white p-3 rounded-3 border" id="amb-detalle-cursos-wrap" style="display:none; box-shadow:0 2px 12px rgba(0,0,0,.04);">
                        <div class="panel-section-header mb-3" style="display:flex; align-items:center; border-bottom:1px solid rgba(0,0,0,.07); padding-bottom:10px;">
                            <span class="panel-section-dot" style="background:#8b5cf6;"></span>
                            <span class="panel-section-label" id="amb-detalle-cursos-titulo" style="font-weight:700; color:#1e293b;">Cursos del per&iacute;odo</span>
                            <button id="btn-cerrar-amb-detalle" style="margin-left:auto; background:none; border:none; cursor:pointer; color:#64748b; font-size:.8rem; padding:2px 8px; border-radius:4px;" title="Cerrar">&#x2715; cerrar</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped align-middle mb-0" id="tabla-amb-cursos" style="font-size:.78rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Curso</th>
                                        <th class="text-center">Modalidad</th>
                                        <th class="text-center">&Aacute;mbito</th>
                                        <th class="text-center">Inscritos</th>
                                        <th class="text-center">Cupo M&aacute;x.</th>
                                        <th class="text-center">Fecha Inicio</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div style="padding:6px 8px 2px; font-size:.68rem; color:#94a3b8; display:flex; align-items:center; gap:5px; margin-top:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                                Haz clic en una fila para abrir el curso en su panel
                            </div>
                        </div>
                    </div>
                </div><!-- /amb-contenido -->
            </div><!-- /bloque-ambito -->
        </div><!-- /tables-container -->
    </div><!-- /kpi-panel-inner -->
</div><!-- /section-cursos -->

<style>
.ej-filtros-wrap{display:flex;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid rgba(0,0,0,.07);}
.ej-filtros-grupo{display:flex;gap:4px;flex-wrap:wrap;}
.ej-filtro-btn,.amb-filtro-btn{padding:5px 15px;border-radius:20px;border:1.5px solid #e2e8f0;background:#fff;font-size:.76rem;font-weight:600;color:#64748b;cursor:pointer;transition:all .18s;}
.ej-filtro-btn:hover,.amb-filtro-btn:hover{border-color:#94a3b8;color:#1e293b;}
.ej-filtro-btn.active,.amb-filtro-btn.active{background:#1e293b;border-color:#1e293b;color:#fff;}
.ej-filtros-sel{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.ej-select{padding:5px 10px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;font-size:.78rem;color:#1e293b;cursor:pointer;outline:none;transition:border-color .15s;}
.ej-select:focus{border-color:#6366f1;}
.ej-cards-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
.ej-card{flex:1;min-width:150px;background:#fff;border-radius:10px;border:1.5px solid #e2e8f0;padding:14px 16px 28px;display:flex;align-items:flex-start;gap:12px;position:relative;transition:box-shadow .2s,border-color .2s;}
.ej-card:hover{box-shadow:0 2px 14px rgba(0,0,0,.08);}
.ej-card-clickable{cursor:pointer;}
.ej-card-clickable:hover{border-color:#6366f1;box-shadow:0 4px 18px rgba(99,102,241,.13);}
.ej-card-clickable.ej-card-activa{border-color:#6366f1;background:#fafaff;box-shadow:0 4px 18px rgba(99,102,241,.18);}
.ej-card-diferidos{border-color:#fde68a;}
.ej-card-diferidos:hover{border-color:#f59e0b;box-shadow:0 4px 18px rgba(245,158,11,.13);}
.ej-card-diferidos.ej-card-activa{border-color:#f59e0b;background:#fffbeb;}
.ej-card-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.ej-card-body{display:flex;flex-direction:column;gap:2px;min-width:0;}
.ej-card-val{font-size:1.35rem;font-weight:700;line-height:1.1;}
.ej-card-lbl{font-size:.68rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;}
.ej-card-sub{font-size:.69rem;color:#94a3b8;line-height:1.3;}
.ej-card-hint{position:absolute;bottom:7px;right:10px;font-size:.62rem;color:#94a3b8;font-weight:600;letter-spacing:.03em;pointer-events:none;transition:color .15s;}
.ej-card-clickable:hover .ej-card-hint{color:#6366f1;}
.ej-card-diferidos:hover .ej-card-hint{color:#d97706;}
.ej-card-clickable.ej-card-activa .ej-card-hint{color:#6366f1;}
.ej-card-diferidos.ej-card-activa .ej-card-hint{color:#d97706;}
:root{--sem-verde:#28c76f;--sem-amarillo:#f59e0b;--sem-rojo:#ef4444;}
</style>

<script>
/* ════════════════════════════════════════════════════════════
   FIX 1 — chartCursosMensual
   La gráfica se construye en buildCursosMensual(), que se llama:
   - Al cargar la sección (si Chart.js ya está listo).
   - Desde window.buildCursosMensual() cuando la sección
     se activa por primera vez (disparado por home.js / inicio.php).
   Esto soluciona el canvas width=0 al estar la sección oculta.
════════════════════════════════════════════════════════════ */
(function () {
    const DATA       = <?php echo json_encode(array_values($cursosPorMes)); ?>;
    const MES_ACTUAL = <?php echo (int)date('n') - 1; ?>;
    const LABELS     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const COLOR_BASE = '#28c76f', COLOR_CURR = '#7367f0', COLOR_NONE = '#28c76f33';

    function buildCursosMensual() {
        const canvas = document.getElementById('chartCursosMensual');
        if (!canvas) return;
        if (typeof Chart === 'undefined') { setTimeout(buildCursosMensual, 100); return; }

        if (canvas.offsetWidth === 0) { setTimeout(buildCursosMensual, 80); return; }

        if (window._chartCursosMensual) { window._chartCursosMensual.destroy(); }

        const bgColors     = DATA.map((v, i) => i === MES_ACTUAL ? COLOR_CURR : (v > 0 ? COLOR_BASE : COLOR_NONE));
        const borderColors = DATA.map((v, i) => i === MES_ACTUAL ? COLOR_CURR : (v > 0 ? COLOR_BASE : 'transparent'));
        const maxVal = Math.max(...DATA, 1);

        const tickColors = LABELS.map((_, i) => i === MES_ACTUAL ? COLOR_CURR : '#64748b');

        const pluginVals = {
            id: 'valsC',
            afterDatasetsDraw(chart) {
                const { ctx: c, data: d } = chart;
                d.datasets[0].data.forEach((val, i) => {
                    if (!val) return;
                    const bar = chart.getDatasetMeta(0).data[i];
                    c.save();
                    c.fillStyle = i === MES_ACTUAL ? COLOR_CURR : '#475569';
                    c.font = '600 11px Roboto,sans-serif';
                    c.textAlign = 'center';
                    c.textBaseline = 'bottom';
                    c.fillText(val, bar.x, bar.y - 3);
                    c.restore();
                });
            }
        };

        window._chartCursosMensual = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: LABELS,
                datasets: [{
                    data: DATA,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: DATA.map((_, i) => i === MES_ACTUAL ? '#5b52d6' : '#22a85f')
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                layout: { padding: { top: 20 } },
                onClick: (event, elements) => {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    const mesNum = index + 1;
                    const names = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    const mesLabel = names[index];
                    mostrarDetalleCursosMes(mesNum, mesLabel);
                },
                onHover: (event, chartElement) => {
                    const target = event.native ? event.native.target : (event.target || event.srcElement);
                    if (target) {
                        target.style.cursor = chartElement.length ? 'pointer' : 'default';
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title:      items => LABELS[items[0].dataIndex],
                            label:      item  => ` ${item.parsed.y} curso${item.parsed.y !== 1 ? 's' : ''} iniciado${item.parsed.y !== 1 ? 's' : ''}`,
                            afterLabel: item  => { const t = DATA.reduce((a, b) => a + b, 0); return ` ${t > 0 ? Math.round(item.parsed.y / t * 100) : 0}% del total anual`; }
                        },
                        backgroundColor: 'rgba(15,23,42,.88)', titleFont: { size: 12, weight: 600 }, bodyFont: { size: 11 }, padding: 10, cornerRadius: 7
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11 }, color: tickColors }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxVal + Math.ceil(maxVal * 0.18),
                        grid: { color: 'rgba(0,0,0,.05)' },
                        border: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: Math.max(1, Math.ceil(maxVal / 5)), precision: 0 },
                        title: { display: true, text: 'Número de Cursos', color: '#64748b', font: { size: 11, weight: 600 } }
                    }
                }
            },
            plugins: [pluginVals]
        });
    }

    window.buildCursosMensual = buildCursosMensual;
    buildCursosMensual();
})();

/* ════════════════════════════════════════════════════════════
   BLOQUE PRINCIPAL — Filtros de ejecución + gráfica Ámbito por Período
   CAMBIOS:
     · renderAmbito() ya no actualiza KPIs de cards (eliminadas).
     · buildChartAmbitoPeriodo() incorpora pluginContornoSeleccion:
       dibuja un rect redondeado alrededor de la columna apilada
       seleccionada, con gap=4px y sombra sutil.
     · ambBarraSeleccionada gestiona el toggle de selección.
     · Periodo inicial siempre es 'mes' con el mes actual.
════════════════════════════════════════════════════════════ */
(function () {
    const INICIADOS_DATA   = <?php echo $jsIniciados; ?>;
    const CURSOS_HISTORICO = <?php echo $jsCursosHistorico; ?>;
    const AMBITO_HISTORICO = <?php echo $jsAmbitoHistorico; ?>;
    const MESES_NOM        = <?php echo $jsMesesES; ?>;
    const COLORES_AMBITO   = <?php echo json_encode(array_values($coloresFase2), JSON_UNESCAPED_UNICODE); ?>;

    let ejPeriodoActivo    = 'mes';
    let ejCardActiva       = null;
    let ambPeriodoActivo   = 'mes';   // arranca en mes actual (predeterminado)
    let ambBarraSeleccionada = null;  // índice de columna seleccionada en la gráfica

    const esc = s => String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const semC = p => p >= 85 ? '#28c76f' : p >= 75 ? '#f59e0b' : '#ef4444';
    const semK = p => p >= 85 ? 'semaforo-text-verde' : p >= 75 ? 'semaforo-text-amarillo' : 'semaforo-text-rojo';
    const gv   = id => { const e = document.getElementById(id); return e ? parseInt(e.value) : 0; };
    const st   = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v; };
    const sv   = (id, v) => { const e = document.getElementById(id); if (e) e.style.display = v ? '' : 'none'; };

    /* ── Ejecución ──────────────────────────────────────────── */
    function matchEj(a, m, t) {
        if (parseInt(a) !== gv('ej-anio-select')) return false;
        const mi = parseInt(m), ti = parseInt(t);
        if (ejPeriodoActivo === 'mes')       return mi === gv('ej-mes-select');
        if (ejPeriodoActivo === 'trimestre') return ti === gv('ej-trim-select');
        if (ejPeriodoActivo === 'semestre')  return gv('ej-sem-select') === 1 ? mi <= 6 : mi > 6;
        return true;
    }
    function labelEj() {
        const a = gv('ej-anio-select');
        if (ejPeriodoActivo === 'mes')       return (MESES_NOM[gv('ej-mes-select') - 1] || '') + ' ' + a;
        if (ejPeriodoActivo === 'trimestre') return 'T' + gv('ej-trim-select') + ' ' + a;
        if (ejPeriodoActivo === 'semestre')  return (gv('ej-sem-select') === 1 ? '1er Sem.' : '2do Sem.') + ' ' + a;
        return 'Año ' + a;
    }
    function calcEj() {
        let p = 0, i = 0;
        INICIADOS_DATA.forEach(r => {
            if (!matchEj(r.anio, r.mes, r.trimestre)) return;
            p += parseInt(r.planificados) || 0;
            i += parseInt(r.iniciados)    || 0;
        });
        return { planif: p, inic: i, pct: p > 0 ? Math.round(i / p * 100) : 0 };
    }
    function filtrarTipo(tipo) {
        return CURSOS_HISTORICO.filter(c => c.tipo_ejecucion === tipo && matchEj(c.anio, c.mes, c.trimestre));
    }

    function renderDetalleCards(cursos, tipo, label) {
        const wrap  = document.getElementById('ej-detalle-wrap');
        const body  = document.getElementById('ej-detalle-body');
        const titulo = document.getElementById('ej-detalle-titulo');
        const dot   = document.getElementById('ej-detalle-dot');
        if (!wrap || !body) return;
        const cfg = {
            pendiente: { label: 'Cursos Pendientes',    color: '#3b82f6' },
            iniciado:  { label: 'Cursos en Formación',  color: '#22c55e' },
            diferido:  { label: 'Cursos Diferidos',      color: '#d97706' }
        };
        const c = cfg[tipo] || { label: 'Detalle', color: '#6366f1' };
        if (titulo) titulo.textContent = c.label + ' — ' + (label || '');
        if (dot)    dot.style.background = c.color;
        if (!cursos.length) {
            body.innerHTML = `<p class="text-muted text-center py-3" style="font-size:.82rem;">Sin cursos en el periodo seleccionado.</p>`;
        } else {
            let rows = '';
            cursos.forEach((cur, i) => {
                rows += `<tr class="curso-row-link" data-nombre="${esc(cur.nombre_curso)}" style="cursor:pointer;" title="Ver curso">
                    <td style="color:#94a3b8;font-size:.72rem;">${String(i + 1).padStart(2, '0')}</td>
                    <td class="fw-semibold" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(cur.nombre_curso)}</td>
                    <td class="text-center"><span class="badge-mini" style="background:#e0f2fe;color:#0369a1;">${esc(cur.nombre_modalidad)}</span></td>
                    <td class="text-center"><span class="badge-mini" style="background:#ede9fe;color:#5b21b6;">${esc(cur.nombre_ambito)}</span></td>
                    <td class="text-center fw-bold">${cur.num_inscritos}</td>
                    <td class="text-center text-muted">${cur.max_participantes}</td>
                    <td class="text-center" style="font-size:.75rem;">${esc(cur.fecha_inicio || '—')}</td>
                    <td class="text-center"><span class="badge-mini" style="background:${c.color}22;color:${c.color};">${esc(cur.nombre_estatusCurso)}</span></td>
                </tr>`;
            });
            body.innerHTML = `<table class="table table-sm table-hover table-striped align-middle mb-0">
                <thead class="table-light"><tr><th>#</th><th>Curso</th><th class="text-center">Modalidad</th><th class="text-center">Ámbito</th><th class="text-center">Inscritos</th><th class="text-center">Máx.</th><th class="text-center">Fecha inicio</th><th class="text-center">Estado</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;">↗ Haz clic en una fila para abrir el curso</div>`;
        }
        wrap.style.display = '';
        setTimeout(() => wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
    }

    function actualizarEj() {
        const { planif, inic, pct } = calcEj();
        const dif   = filtrarTipo('diferido');
        const pen   = filtrarTipo('pendiente');
        const label = labelEj();
        const color = semC(pct);

        const elPct = document.getElementById('ej-pct-val');
        if (elPct) { elPct.textContent = pct + '%'; elPct.className = 'ej-card-val ' + semK(pct); }
        st('ej-iniciados-sub', inic + ' de ' + planif + ' planificados');
        st('ej-plan-val', pen.length); st('ej-plan-sub', 'pendientes de iniciar');
        st('ej-ejec-val', inic);       st('ej-ejec-sub', label);
        st('ej-dif-val',  dif.length); st('ej-dif-sub',  label);

        const fill = document.getElementById('ej-barra-fill');
        if (fill) { fill.style.width = Math.min(pct, 100) + '%'; fill.style.background = color; }
        st('ej-barra-pct', pct + '%');
        const bLbl = document.getElementById('ej-barra-label');
        if (bLbl) bLbl.style.color = color;
        st('ej-periodo-label', 'Meta ≥ 85% · ' + label);

        if (ejCardActiva) {
            const datos = ejCardActiva === 'pendiente' ? pen
                        : ejCardActiva === 'iniciado'  ? filtrarTipo('iniciado')
                        : dif;
            renderDetalleCards(datos, ejCardActiva, label);
        }
    }

    ['card-planificados', 'card-iniciados', 'card-diferidos'].forEach(id => {
        const card = document.getElementById(id);
        if (!card) return;
        card.addEventListener('click', () => {
            const tipo = card.dataset.tipo;
            if (ejCardActiva === tipo) {
                ejCardActiva = null;
                card.classList.remove('ej-card-activa');
                const w = document.getElementById('ej-detalle-wrap');
                if (w) w.style.display = 'none';
                return;
            }
            ['card-planificados','card-iniciados','card-diferidos'].forEach(cid => {
                const c = document.getElementById(cid);
                if (c) c.classList.remove('ej-card-activa');
            });
            ejCardActiva = tipo;
            card.classList.add('ej-card-activa');
            renderDetalleCards(
                tipo === 'pendiente' ? filtrarTipo('pendiente')
                : tipo === 'iniciado'  ? filtrarTipo('iniciado')
                : filtrarTipo('diferido'),
                tipo, labelEj()
            );
        });
    });

    const btnCEj = document.getElementById('btn-cerrar-ej-detalle');
    if (btnCEj) btnCEj.addEventListener('click', () => {
        ejCardActiva = null;
        const w = document.getElementById('ej-detalle-wrap');
        if (w) w.style.display = 'none';
        ['card-planificados','card-iniciados','card-diferidos'].forEach(cid => {
            const c = document.getElementById(cid);
            if (c) c.classList.remove('ej-card-activa');
        });
    });

    document.querySelectorAll('.ej-filtro-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            ejPeriodoActivo = btn.dataset.periodo;
            document.querySelectorAll('.ej-filtro-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            sv('ej-sel-mes',       ejPeriodoActivo === 'mes');
            sv('ej-sel-trimestre', ejPeriodoActivo === 'trimestre');
            sv('ej-sel-semestre',  ejPeriodoActivo === 'semestre');
            actualizarEj();
        });
    });
    ['ej-mes-select','ej-trim-select','ej-sem-select','ej-anio-select'].forEach(id => {
        const e = document.getElementById(id);
        if (e) e.addEventListener('change', actualizarEj);
    });
    actualizarEj();

    /* ════════════════════════════════════════════════════════
       ÁMBITO — renderAmbito simplificada (sin cards de KPI)
    ════════════════════════════════════════════════════════ */
    function renderAmbito() {
        buildChartAmbitoPeriodo();
        // Ocultar detalle al cambiar filtro
        const wrap = document.getElementById('amb-detalle-cursos-wrap');
        if (wrap) wrap.style.display = 'none';
    }

    /* ════════════════════════════════════════════════════════
       buildChartAmbitoPeriodo
       Incluye pluginContornoSeleccion: dibuja un rectángulo
       redondeado alrededor de la columna apilada seleccionada,
       con un gap de 4 px entre la barra y el contorno.
    ════════════════════════════════════════════════════════ */
    function buildChartAmbitoPeriodo() {
        const canvas = document.getElementById('chartAmbitoPeriodo');
        if (!canvas) return;
        if (typeof Chart === 'undefined') return;
        if (canvas.offsetWidth === 0) return;

        const selectAnio  = parseInt(document.getElementById('amb-anio-select').value) || new Date().getFullYear();
        const yearCourses = CURSOS_HISTORICO.filter(c => c.anio === selectAnio);
        const ambitos     = [...new Set(CURSOS_HISTORICO.map(c => c.nombre_ambito).filter(Boolean))];

        let labels = [], getPeriodIndex, periodKeys = [];

        if (ambPeriodoActivo === 'mes') {
            labels         = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            getPeriodIndex = c => c.mes - 1;
            periodKeys     = Array.from({ length: 12 }, (_, i) => i);
        } else if (ambPeriodoActivo === 'trimestre') {
            labels         = ['T1','T2','T3','T4'];
            getPeriodIndex = c => c.trimestre - 1;
            periodKeys     = [0, 1, 2, 3];
        } else if (ambPeriodoActivo === 'semestre') {
            labels         = ['Semestre 1','Semestre 2'];
            getPeriodIndex = c => (c.mes <= 6 ? 0 : 1);
            periodKeys     = [0, 1];
        } else {
            const aniosDisp = [...new Set(CURSOS_HISTORICO.map(c => c.anio).filter(Boolean))].sort((a, b) => a - b);
            labels          = aniosDisp.map(y => String(y));
            getPeriodIndex  = c => aniosDisp.indexOf(c.anio);
            periodKeys      = aniosDisp;
        }

        const coursesToCount = ambPeriodoActivo === 'anio' ? CURSOS_HISTORICO : yearCourses;

        const datasets = ambitos.map((ambito, ambIdx) => {
            const data = periodKeys.map(() => 0);
            coursesToCount.forEach(c => {
                if (c.nombre_ambito === ambito) {
                    const idx = getPeriodIndex(c);
                    if (idx >= 0 && idx < data.length) data[idx]++;
                }
            });
            const color = COLORES_AMBITO[ambIdx % COLORES_AMBITO.length];
            return {
                label: ambito,
                data,
                backgroundColor: color,
                borderColor: '#fff',
                borderWidth: 1,
                borderRadius: 4
            };
        });

        // Destruir instancia previa y resetear selección
        if (graficasCreadas['chartAmbitoPeriodo']) {
            graficasCreadas['chartAmbitoPeriodo'].destroy();
        }
        ambBarraSeleccionada = null;

        /* ── Plugin: contorno de selección ─────────────────────
           Se dibuja en afterDraw(), por encima de todas las barras.
           Calcula el bounding box de la columna apilada completa
           (todos los datasets en ese índice) y traza un rectángulo
           redondeado con gap=4 px y radio de esquina=6 px.
        ──────────────────────────────────────────────────────── */
        const pluginContornoSeleccion = {
            id: 'contornoSeleccion',
            afterDraw(chart) {
                if (ambBarraSeleccionada === null) return;

                const idx = ambBarraSeleccionada;
                const { ctx } = chart;

                // Recorrer todos los datasets para obtener el bounding box
                let top    =  Infinity;
                let bottom = -Infinity;
                let left   =  Infinity;
                let right  = -Infinity;
                let hayDatos = false;

                chart.data.datasets.forEach((_, di) => {
                    const meta = chart.getDatasetMeta(di);
                    if (!meta || !meta.data[idx]) return;
                    const bar = meta.data[idx];
                    // Solo considerar segmentos con altura real
                    if (!bar.height || bar.height <= 0) return;
                    hayDatos = true;

                    const bTop    = bar.y;
                    const bBottom = bar.y + bar.height;
                    const bLeft   = bar.x - bar.width / 2;
                    const bRight  = bar.x + bar.width / 2;

                    if (bTop    < top)    top    = bTop;
                    if (bBottom > bottom) bottom = bBottom;
                    if (bLeft   < left)   left   = bLeft;
                    if (bRight  > right)  right  = bRight;
                });

                if (!hayDatos) return;

                const gap = 4;   // espacio entre la barra y el contorno (px)
                const r   = 6;   // radio de las esquinas redondeadas (px)
                const x   = left   - gap;
                const y   = top    - gap;
                const w   = (right  - left) + gap * 2;
                const h   = (bottom - top)  + gap * 2;

                ctx.save();
                ctx.beginPath();
                // ctx.roundRect disponible en navegadores modernos (Chrome 99+, Firefox 112+, Safari 15.4+)
                if (ctx.roundRect) {
                    ctx.roundRect(x, y, w, h, r);
                } else {
                    // Fallback sin esquinas redondeadas para navegadores antiguos
                    ctx.rect(x, y, w, h);
                }
                ctx.strokeStyle  = 'rgba(30, 41, 59, 0.80)';
                ctx.lineWidth    = 2;
                ctx.setLineDash([]);
                ctx.shadowColor  = 'rgba(30, 41, 59, 0.20)';
                ctx.shadowBlur   = 8;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 2;
                ctx.stroke();
                ctx.restore();
            }
        };

        try {
            graficasCreadas['chartAmbitoPeriodo'] = new Chart(canvas, {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, font: { size: 10, weight: '600' } }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.9)',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,.05)', drawBorder: false },
                            ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: 1, precision: 0 },
                            title: { display: true, text: 'N* Cursos', color: '#64748b', font: { size: 11, weight: 600 } }
                        }
                    },
                    onClick: (event, elements) => {
                        if (!elements.length) {
                            // Clic en zona vacía → deseleccionar
                            if (ambBarraSeleccionada !== null) {
                                ambBarraSeleccionada = null;
                                graficasCreadas['chartAmbitoPeriodo'].update('none');
                                const wrap = document.getElementById('amb-detalle-cursos-wrap');
                                if (wrap) wrap.style.display = 'none';
                            }
                            return;
                        }
                        const clickedIdx = elements[0].index;
                        if (ambBarraSeleccionada === clickedIdx) {
                            // Segundo clic en la misma barra → deseleccionar
                            ambBarraSeleccionada = null;
                            graficasCreadas['chartAmbitoPeriodo'].update('none');
                            const wrap = document.getElementById('amb-detalle-cursos-wrap');
                            if (wrap) wrap.style.display = 'none';
                        } else {
                            // Nueva selección → mostrar contorno + detalle
                            ambBarraSeleccionada = clickedIdx;
                            graficasCreadas['chartAmbitoPeriodo'].update('none');
                            mostrarDetalleCursosPeriodo(clickedIdx);
                        }
                    }
                },
                plugins: [pluginContornoSeleccion]
            });
        } catch (e) { console.error('chartAmbitoPeriodo error:', e); }
    }

    /* ════════════════════════════════════════════════════════
       mostrarDetalleCursosPeriodo
       Filtra CURSOS_HISTORICO según el índice de columna
       clicado y lo vuelca en la tabla #tabla-amb-cursos.
    ════════════════════════════════════════════════════════ */
    function mostrarDetalleCursosPeriodo(clickedIdx) {
        const selectAnio = parseInt(document.getElementById('amb-anio-select').value) || new Date().getFullYear();
        let filtered = [], labelPeriodo = '';
        const mesesFull = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        if (ambPeriodoActivo === 'mes') {
            const mesNum = clickedIdx + 1;
            filtered     = CURSOS_HISTORICO.filter(c => c.anio === selectAnio && c.mes === mesNum);
            labelPeriodo = mesesFull[clickedIdx] + ' ' + selectAnio;
        } else if (ambPeriodoActivo === 'trimestre') {
            const trimNum = clickedIdx + 1;
            filtered      = CURSOS_HISTORICO.filter(c => c.anio === selectAnio && c.trimestre === trimNum);
            labelPeriodo  = 'T' + trimNum + ' ' + selectAnio;
        } else if (ambPeriodoActivo === 'semestre') {
            const semNum = clickedIdx + 1;
            filtered     = CURSOS_HISTORICO.filter(c => c.anio === selectAnio && (semNum === 1 ? c.mes <= 6 : c.mes > 6));
            labelPeriodo = (semNum === 1 ? '1er Semestre' : '2do Semestre') + ' ' + selectAnio;
        } else {
            const aniosDisp = [...new Set(CURSOS_HISTORICO.map(c => c.anio).filter(Boolean))].sort((a, b) => a - b);
            const anioVal   = aniosDisp[clickedIdx];
            filtered        = CURSOS_HISTORICO.filter(c => c.anio === anioVal);
            labelPeriodo    = 'Año ' + anioVal;
        }

        const wrap   = document.getElementById('amb-detalle-cursos-wrap');
        const tbody  = document.querySelector('#tabla-amb-cursos tbody');
        const titulo = document.getElementById('amb-detalle-cursos-titulo');
        if (!wrap || !tbody) return;
        if (titulo) titulo.textContent = 'Cursos de ' + labelPeriodo + ' — por Ámbito';

        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-muted text-center py-3">Sin cursos registrados en este período.</td></tr>`;
        } else {
            let rows = '';
            filtered.forEach((cur, i) => {
                rows += `<tr class="curso-row-link" data-nombre="${esc(cur.nombre_curso)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${String(i + 1).padStart(2, '0')}</td>
                    <td class="fw-semibold" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(cur.nombre_curso)}</td>
                    <td class="text-center"><span class="badge-mini" style="background:#e0f2fe;color:#0369a1;">${esc(cur.nombre_modalidad)}</span></td>
                    <td class="text-center"><span class="badge-mini" style="background:#ede9fe;color:#5b21b6;">${esc(cur.nombre_ambito)}</span></td>
                    <td class="text-center fw-bold">${cur.num_inscritos}</td>
                    <td class="text-center text-muted">${cur.max_participantes}</td>
                    <td class="text-center" style="font-size:.75rem;">${esc(cur.fecha_inicio || '—')}</td>
                    <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;">${esc(cur.nombre_estatusCurso)}</span></td>
                </tr>`;
            });
            tbody.innerHTML = rows;
        }

        wrap.style.display = '';
        setTimeout(() => wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
    }

    /* Botón cerrar detalle ámbito */
    const btnCerrarAmb = document.getElementById('btn-cerrar-amb-detalle');
    if (btnCerrarAmb) btnCerrarAmb.addEventListener('click', () => {
        const wrap = document.getElementById('amb-detalle-cursos-wrap');
        if (wrap) wrap.style.display = 'none';
        // Quitar selección visual también
        ambBarraSeleccionada = null;
        if (graficasCreadas['chartAmbitoPeriodo']) {
            graficasCreadas['chartAmbitoPeriodo'].update('none');
        }
    });

    /* Listeners de filtros de ámbito */
    document.querySelectorAll('.amb-filtro-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            ambPeriodoActivo = btn.dataset.periodo;
            document.querySelectorAll('.amb-filtro-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            sv('amb-sel-mes',       ambPeriodoActivo === 'mes');
            sv('amb-sel-trimestre', ambPeriodoActivo === 'trimestre');
            sv('amb-sel-semestre',  ambPeriodoActivo === 'semestre');
            sv('amb-sel-anio',      ambPeriodoActivo !== 'anio');
            renderAmbito();
        });
    });

    ['amb-mes-select','amb-trim-select','amb-sem-select','amb-anio-select'].forEach(id => {
        const e = document.getElementById(id);
        if (e) e.addEventListener('change', renderAmbito);
    });

    /* ── Exposición global ──────────────────────────────────
       window.buildAmbitoPeriodo() es llamado por inicio.php /
       home.js cuando la sección cursos se activa por primera vez.
       Inicializa los selectores visibles y renderiza la gráfica
       mostrando el mes actual (predeterminado).
    ──────────────────────────────────────────────────────── */
    window.buildAmbitoPeriodo = function () {
        sv('amb-sel-mes',       ambPeriodoActivo === 'mes');
        sv('amb-sel-trimestre', ambPeriodoActivo === 'trimestre');
        sv('amb-sel-semestre',  ambPeriodoActivo === 'semestre');
        sv('amb-sel-anio',      ambPeriodoActivo !== 'anio');
        renderAmbito();
    };

    // NO llamar renderAmbito() aquí en el arranque: la sección está
    // oculta y el canvas tendría width=0. Se invoca desde
    // window.buildAmbitoPeriodo() al activar la sección.
})();
</script>

<!-- Modal Detalle Cursos Registrados por Mes -->
<style>
.modal-curso-row {
    transition: all 0.2s ease-in-out;
}
.modal-curso-row:hover td {
    background-color: #f8fafc !important;
}
.modal-curso-row:hover {
    transform: translateY(-1px);
}
.modal-curso-row:hover svg.chevron-arrow {
    transform: translateX(4px);
    fill: #28c76f;
}
.modal-curso-table td {
    padding: 8px 12px !important;
    white-space: nowrap;
    font-size: 0.8rem;
    background-color: #ffffff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.modal-curso-table td:first-child {
    border-left: 1px solid #e2e8f0;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}
.modal-curso-table td:last-child {
    border-right: 1px solid #e2e8f0;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}
.modal-curso-search-input {
    border-radius: 20px;
    padding: 10px 16px 10px 42px;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    font-size: 0.85rem;
    transition: all 0.2s ease-in-out;
}
.modal-curso-search-input:focus {
    border-color: #28c76f;
    box-shadow: 0 0 0 3px rgba(40, 199, 111, 0.15);
    outline: none;
}
.empty-state-icon-curso {
    background: #f0fdf4;
    color: #28c76f;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}
</style>

<div class="modal fade" id="modal-DetalleCursosMes" tabindex="-1" aria-labelledby="modalDetalleCursosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <!-- Header con gradiente premium -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #15803d, #28c76f, #0ea5e9); padding: 18px 24px; border-bottom: none;">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalDetalleCursosLabel">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
          </svg>
          Cursos iniciados en <span id="modal-curso-mes-nombre">Mes</span> &middot; <span id="modal-curso-mes-anio"><?php echo $anioActual; ?></span>
        </h5>
        <button type="button" class="modal-close-btn-custom" data-bs-dismiss="modal" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
          </svg>
        </button>
      </div>
      
      <!-- Cuerpo del modal -->
      <div class="modal-body p-4" style="background-color: #f8fafc; min-height: 250px;">
        
        <!-- Barra de búsqueda e información -->
        <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
          <div class="position-relative flex-grow-1" style="min-width: 250px;">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="color: #94a3b8;">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
              </svg>
            </span>
            <input type="text" id="modal-curso-search" class="form-control modal-curso-search-input ps-5 w-100 shadow-sm" placeholder="Buscar por nombre, modalidad, turno o estado...">
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-teal-soft text-teal border border-teal-soft fw-semibold px-3 py-2" style="font-size: .75rem; border-radius: 20px; display: none; background-color: #e2fce8; color: #15803d;" id="modal-curso-filtered-wrapper">
              Mostrados: <span id="modal-curso-filtered-count">0</span>
            </span>
          </div>
        </div>

        <!-- Spinner de carga -->
        <div id="modal-curso-loading" class="text-center py-5" style="display: none;">
          <div class="spinner-border text-success" role="status" style="color: #28c76f;">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="text-muted mt-3 small fw-medium">Obteniendo listado de cursos...</p>
        </div>
        
        <!-- Estado sin datos -->
        <div id="modal-curso-sin-datos" class="text-center py-5 text-muted" style="display: none;">
          <div class="empty-state-icon-curso">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
              <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
            </svg>
          </div>
          <h6 class="fw-bold text-dark mb-1">Sin registros</h6>
          <p class="text-muted small mb-0">No se encontraron cursos iniciados en este mes.</p>
        </div>

        <!-- Tabla interactiva con diseño premium -->
        <div id="modal-curso-contenido" class="table-responsive" style="max-height: 400px; display: none; border-radius: 8px;">
          <table class="table align-middle mb-0 modal-curso-table" style="border-collapse: separate; border-spacing: 0 6px;">
            <thead class="sticky-top sticky-thead">
              <tr>
                <th style="border-top-left-radius: 8px;">Curso</th>
                <th>Modalidad</th>
                <th>Turno</th>
                <th class="text-center">Participantes</th>
                <th>Fechas (Inicio - Fin)</th>
                <th>Estatus</th>
                <th style="border-top-right-radius: 8px; width: 4%;"></th>
              </tr>
            </thead>
            <tbody id="modal-curso-tbody" style="background-color: transparent;">
              <!-- rows dynamically filled here -->
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer con resumen -->
      <div class="modal-footer border-top-0 d-flex justify-content-between p-3" style="background-color: #f1f5f9;">
        <span class="badge bg-dark bg-opacity-10 text-dark-emphasis px-3 py-2 fw-semibold" id="modal-curso-count" style="font-size: 0.78rem; border-radius: 20px;">0 cursos</span>
        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 600; padding: 6px 16px; background-color: #64748b; border: none;">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Registrar el buscador del modal de cursos
(function setupModalCursoSearch() {
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('modal-curso-search');
        if (input) {
            input.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#modal-curso-tbody tr');
                let visibles = 0;
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                        visibles++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                const wrapper = document.getElementById('modal-curso-filtered-wrapper');
                if (query.length > 0) {
                    wrapper.style.display = 'inline-block';
                    document.getElementById('modal-curso-filtered-count').textContent = visibles;
                } else {
                    wrapper.style.display = 'none';
                }
            });
        }
    });
})();

if (typeof window.escHtml !== 'function') {
    window.escHtml = function(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };
}

function mostrarDetalleCursosMes(mesNum, mesLabel) {
    document.getElementById('modal-curso-mes-nombre').textContent = mesLabel;
    
    // Limpiar buscador
    document.getElementById('modal-curso-search').value = '';
    document.getElementById('modal-curso-filtered-wrapper').style.display = 'none';
    
    // Mostrar loading
    document.getElementById('modal-curso-loading').style.display = 'block';
    document.getElementById('modal-curso-sin-datos').style.display = 'none';
    document.getElementById('modal-curso-contenido').style.display = 'none';
    document.getElementById('modal-curso-count').textContent = '—';
    
    // Mostrar modal usando Bootstrap
    const modalEl = document.getElementById('modal-DetalleCursosMes');
    let modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalEl);
    }
    modalInstance.show();
    
    // Llamar a la API
    const url = `../controlador/Reporte/api_reportes.php?accion=cursos_por_mes_detalle&mes=${mesNum}&anio=<?php echo $anioActual; ?>`;
    fetch(url, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            document.getElementById('modal-curso-loading').style.display = 'none';
            if (data.error || !Array.isArray(data) || data.length === 0) {
                document.getElementById('modal-curso-sin-datos').style.display = 'block';
                document.getElementById('modal-curso-count').textContent = '0 cursos';
                return;
            }
            
            const tbody = document.getElementById('modal-curso-tbody');
            tbody.innerHTML = '';
            
            data.forEach(c => {
                const tr = document.createElement('tr');
                tr.className = 'curso-row-link modal-curso-row';
                tr.dataset.nombre = c.nombre_curso;
                tr.style.cursor = 'pointer';
                tr.style.backgroundColor = '#ffffff';
                
                // Color semántico de pastilla de estatus
                let pillStyle = 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;';
                let dotColor = '#64748b';
                const est = (c.nombre_estatusCurso || '').toLowerCase();
                
                if (est.includes('culmin') || est.includes('complet') || est.includes('finaliz')) {
                    pillStyle = 'background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;';
                    dotColor = '#28c76f';
                } else if (est.includes('proces') || est.includes('curso') || est.includes('inici')) {
                    pillStyle = 'background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;';
                    dotColor = '#3b82f6';
                } else if (est.includes('difer')) {
                    pillStyle = 'background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a;';
                    dotColor = '#f59e0b';
                } else if (est.includes('esper')) {
                    pillStyle = 'background-color: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5;';
                    dotColor = '#ef4444';
                }
                
                // Formato de Fechas
                const fInicio = c.fecha_inicio ? c.fecha_inicio.split('-').reverse().join('/') : '—';
                const fFin    = c.fecha_fin ? c.fecha_fin.split('-').reverse().join('/') : '—';
                
                // Cupos y progreso visual
                const pct = c.max_participantes > 0 ? Math.round((c.num_inscritos / c.max_participantes) * 100) : 0;
                let barColor = '#64748b';
                if (pct >= 90) barColor = '#ef4444';      // casi lleno o lleno
                else if (pct >= 50) barColor = '#28c76f'; // moderado
                else barColor = '#3b82f6';                // pocos
                
                tr.innerHTML = `
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 700; background-color: #e8f5e9; color: #1b5e20; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-journal-bookmark" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6 8V1h1v7L6.5 7.5z"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1c0-.188.04-.368.113-.532L1 12.045V11h1v.045a1 1 0 0 0 .11.455L3 13.045V12h1v1.045a1 1 0 0 0 .11-.455L5.5 11.5v-1h1v1.045a1 1 0 0 0 .11.455L8 13.045V12h1v1.045a1 1 0 0 0 .11-.455L10.5 11.5v-1h1v1.045a1 1 0 0 0 .11.455L13 13.045V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v12c0 .584.237 1.114.62 1.492A1 1 0 0 1 2 14V2a2 2 0 0 1 2-2z"/>
                          </svg>
                        </div>
                        <span class="fw-bold text-dark" style="font-size: .83rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${window.escHtml(c.nombre_curso)}">
                          ${window.escHtml(c.nombre_curso)}
                        </span>
                      </div>
                    </td>
                    <td class="text-center"><span class="badge-mini" style="background:#e0f2fe; color:#0369a1; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">${window.escHtml(c.nombre_modalidad || '—')}</span></td>
                    <td class="text-secondary fw-semibold" style="font-size: .8rem;">${window.escHtml(c.turno || '—')}</td>
                    <td>
                      <div class="d-flex flex-column" style="min-width: 80px;">
                        <span class="fw-bold text-dark mb-1" style="font-size: 0.76rem;">${c.num_inscritos} / ${c.max_participantes} <span class="text-muted" style="font-size: 0.68rem; font-weight: normal;">(${pct}%)</span></span>
                        <div class="progress" style="height: 4px; background-color: #e2e8f0; border-radius: 2px; overflow: hidden;">
                          <div class="progress-bar" role="progressbar" style="width: ${pct}%; background-color: ${barColor};"></div>
                        </div>
                      </div>
                    </td>
                    <td class="text-secondary" style="font-size: .8rem;">
                      <span style="display:inline-flex; align-items:center; gap:4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                        ${fInicio} - ${fFin}
                      </span>
                    </td>
                    <td>
                      <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:12px; font-size:.7rem; font-weight:600; ${pillStyle}">
                        <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background-color:${dotColor};"></span>
                        ${window.escHtml(c.nombre_estatusCurso)}
                      </span>
                    </td>
                    <td style="text-align: right;">
                      <svg class="chevron-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#cbd5e1" viewBox="0 0 16 16" style="transition: all 0.2s ease;">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                      </svg>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            document.getElementById('modal-curso-contenido').style.display = 'block';
            document.getElementById('modal-curso-count').textContent = `${data.length} curso${data.length !== 1 ? 's' : ''}`;
        })
        .catch(err => {
            console.error('Error fetching monthly courses:', err);
            document.getElementById('modal-curso-loading').style.display = 'none';
            document.getElementById('modal-curso-sin-datos').style.display = 'block';
            document.getElementById('modal-curso-count').textContent = 'Error';
        });
}
</script>
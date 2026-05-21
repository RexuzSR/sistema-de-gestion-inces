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
            </nav>
            <div class="chart-tabs-body">

                <!-- TAB: Por Estatus — tabla lateral navegable a cursos.php -->
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

                <!-- TAB: Por Modalidad — tabla lateral navegable a cursos.php -->
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

                <!-- TAB: Por Turno — tabla lateral navegable a cursos.php -->
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

                <!-- TAB: Ámbito Solicitado — tabla con filas navegables a cursos.php -->
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
                    <div class="cp-item" style="border-left:2px solid #fde68a;padding-left:12px;"><span style="font-size:.72rem;color:#92400e;line-height:1.5;">Cada cupo vac&iacute;o representa un recurso formativo no aprovechado. Meta: &ge; 85% de ocupaci&oacute;n.</span></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- BLOQUE: FORMACIONES POR ÁMBITO -->
            <div class="panel-section mt-3" id="bloque-ambito">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#6366f1;"></span>
                    <span class="panel-section-label">Formaciones por &Aacute;mbito</span>
                </div>
                <div class="ej-filtros-wrap" style="margin-top:10px;">
                    <div class="ej-filtros-grupo">
                        <button class="amb-filtro-btn active" data-periodo="mes">Mes</button>
                        <button class="amb-filtro-btn" data-periodo="trimestre">Trimestre</button>
                        <button class="amb-filtro-btn" data-periodo="semestre">Semestre</button>
                        <button class="amb-filtro-btn" data-periodo="anio">A&ntilde;o</button>
                    </div>
                    <div class="ej-filtros-sel">
                        <div id="amb-sel-mes"><select id="amb-mes-select" class="ej-select"><?php foreach ($mesesES as $n => $nm): ?><option value="<?php echo $n; ?>" <?php echo $n == $mesActualNum ? 'selected' : ''; ?>><?php echo $nm; ?></option><?php endforeach; ?></select></div>
                        <div id="amb-sel-trimestre" style="display:none;"><select id="amb-trim-select" class="ej-select"><?php for ($t = 1; $t <= 4; $t++): ?><option value="<?php echo $t; ?>" <?php echo $t == $trimestreActualNum ? 'selected' : ''; ?>>T<?php echo $t; ?></option><?php endfor; ?></select></div>
                        <div id="amb-sel-semestre" style="display:none;"><select id="amb-sem-select" class="ej-select"><option value="1" <?php echo $semestreActualNum===1?'selected':''; ?>>1er semestre (Ene&ndash;Jun)</option><option value="2" <?php echo $semestreActualNum===2?'selected':''; ?>>2do semestre (Jul&ndash;Dic)</option></select></div>
                        <span style="color:#e2e8f0;font-size:1.1rem;user-select:none;">|</span>
                        <div id="amb-sel-anio"><select id="amb-anio-select" class="ej-select"><?php foreach ($aniosDisponibles as $ay): ?><option value="<?php echo $ay; ?>" <?php echo $ay==$anioActualNum?'selected':''; ?>><?php echo $ay; ?></option><?php endforeach; ?></select></div>
                    </div>
                </div>
                <div id="amb-contenido">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_formMes; ?>" id="amb-kpi-box">
                                <div class="panel-mini-kpi-header">
                                    <span class="panel-mini-kpi-label" id="amb-kpi-label">Total formaciones este mes</span>
                                    <span class="semaforo-badge semaforo-<?php echo $s_formMes; ?>" id="amb-kpi-sem"><?php echo semL($s_formMes); ?></span>
                                </div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_formMes; ?>" id="amb-kpi-valor"><?php echo $totalFormMes; ?></div>
                                <div class="panel-mini-kpi-sub" id="amb-kpi-sub"><?php echo count($formacionesPorAmbitoMes); ?> &aacute;mbito<?php echo count($formacionesPorAmbitoMes) !== 1 ? 's' : ''; ?> activos en <?php echo $mesActualES . ' ' . $anioActual; ?></div>
                                <div class="kpi-adv-barra-fondo mt-2"><div class="kpi-adv-barra" id="amb-kpi-barra" style="width:<?php echo min(round($totalFormMes/15*100),100); ?>%;background:var(--sem-<?php echo $s_formMes; ?>,#28c76f);"></div></div>
                            </div>
                        </div>
                        <div class="col-md-8" id="amb-barras-col">
                            <?php if (empty($formacionesPorAmbitoMes)): ?>
                                <p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin formaciones iniciadas este mes.</p>
                            <?php else:
                                $maxMes = max(array_column($formacionesPorAmbitoMes, 'total'));
                                foreach ($formacionesPorAmbitoMes as $i => $amb):
                                    $barW  = $maxMes > 0 ? round($amb['total'] / $maxMes * 100) : 0;
                                    $color = $coloresFase2[$i % count($coloresFase2)];
                            ?>
                                <div class="ambito-row">
                                    <span class="ambito-row-lbl" title="<?php echo htmlspecialchars($amb['ambito']); ?>"><?php echo htmlspecialchars($amb['ambito']); ?></span>
                                    <div class="kpi-adv-barra-fondo" style="height:10px;"><div style="width:<?php echo $barW; ?>%;height:10px;background:<?php echo $color; ?>;border-radius:5px;transition:width .4s ease;"></div></div>
                                    <span class="ambito-row-val" style="color:<?php echo $color; ?>;"><?php echo $amb['total']; ?></span>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
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
/* ── Gráfica mensual de cursos ────────────────────────────── */
(function() {
    function build() {
        const ctx = document.getElementById('chartCursosMensual');
        if (!ctx) return;
        if (typeof Chart === 'undefined') { setTimeout(build, 100); return; }
        const data       = <?php echo json_encode(array_values($cursosPorMes)); ?>;
        const mesActual  = <?php echo (int)date('n') - 1; ?>;
        const labels     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const COLOR_BASE = '#28c76f', COLOR_CURR = '#7367f0', COLOR_NONE = '#28c76f33';
        const bgColors     = data.map((v,i) => i===mesActual ? COLOR_CURR : (v>0 ? COLOR_BASE : COLOR_NONE));
        const borderColors = data.map((v,i) => i===mesActual ? COLOR_CURR : (v>0 ? COLOR_BASE : 'transparent'));
        const maxVal = Math.max(...data, 1);
        const pluginVals = {
            id:'valsC',
            afterDatasetsDraw(chart) {
                const {ctx:c, data:d} = chart;
                d.datasets[0].data.forEach((val,i) => {
                    if (!val) return;
                    const bar = chart.getDatasetMeta(0).data[i];
                    c.save(); c.fillStyle = i===mesActual ? COLOR_CURR : '#475569';
                    c.font = '600 11px Roboto,sans-serif'; c.textAlign = 'center'; c.textBaseline = 'bottom';
                    c.fillText(val, bar.x, bar.y - 3); c.restore();
                });
            }
        };
        if (window._chartCursosMensual) window._chartCursosMensual.destroy();
        window._chartCursosMensual = new Chart(ctx, {
            type:'bar',
            data:{ labels, datasets:[{ data, backgroundColor:bgColors, borderColor:borderColors,
                borderWidth:1.5, borderRadius:6, borderSkipped:false,
                hoverBackgroundColor:data.map((_,i)=>i===mesActual?'#5b52d6':'#22a85f') }]},
            options:{
                responsive:true, maintainAspectRatio:false,
                animation:{duration:800,easing:'easeOutQuart'},
                layout:{padding:{top:20}},
                plugins:{
                    legend:{display:false},
                    tooltip:{
                        callbacks:{
                            title: items => labels[items[0].dataIndex],
                            label: item  => ` ${item.parsed.y} curso${item.parsed.y!==1?'s':''} iniciado${item.parsed.y!==1?'s':''}`,
                            afterLabel: item => { const t=data.reduce((a,b)=>a+b,0); return ` ${t>0?Math.round(item.parsed.y/t*100):0}% del total anual`; }
                        },
                        backgroundColor:'rgba(15,23,42,.88)', titleFont:{size:12,weight:600}, bodyFont:{size:11}, padding:10, cornerRadius:7
                    }
                },
                scales:{
                    x:{grid:{display:false},border:{display:false},ticks:{font:{size:11},color:(ctx)=>ctx.index===mesActual?COLOR_CURR:'#64748b'}},
                    y:{beginAtZero:true,suggestedMax:maxVal+Math.ceil(maxVal*.18),grid:{color:'rgba(0,0,0,.05)'},border:{display:false},
                       ticks:{font:{size:10},color:'#94a3b8',stepSize:Math.max(1,Math.ceil(maxVal/5)),precision:0},
                       title: { display: true, text: 'Número de Cursos', color: '#64748b', font: { size: 11, weight: 600 } }
                    }
                }
            },
            plugins:[pluginVals]
        });
    }
    build();
})();

/* ── Filtros ejecución + ámbito ───────────────────────────── */
(function () {
    const INICIADOS_DATA   = <?php echo $jsIniciados; ?>;
    const CURSOS_HISTORICO = <?php echo $jsCursosHistorico; ?>;
    const AMBITO_HISTORICO = <?php echo $jsAmbitoHistorico; ?>;
    const MESES_NOM        = <?php echo $jsMesesES; ?>;
    const COLORES_AMBITO   = <?php echo json_encode(array_values($coloresFase2), JSON_UNESCAPED_UNICODE); ?>;

    let ejPeriodoActivo = 'mes', ejCardActiva = null, ambPeriodoActivo = 'mes';
    const esc = s => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const semC = p => p>=85?'#28c76f':p>=75?'#f59e0b':'#ef4444';
    const semK = p => p>=85?'semaforo-text-verde':p>=75?'semaforo-text-amarillo':'semaforo-text-rojo';
    const gv   = id => { const e=document.getElementById(id); return e?parseInt(e.value):0; };
    const st   = (id,v) => { const e=document.getElementById(id); if(e) e.textContent=v; };
    const sv   = (id,v) => { const e=document.getElementById(id); if(e) e.style.display=v?'':'none'; };

    function matchEj(a,m,t){ if(parseInt(a)!==gv('ej-anio-select')) return false; const mi=parseInt(m),ti=parseInt(t); if(ejPeriodoActivo==='mes') return mi===gv('ej-mes-select'); if(ejPeriodoActivo==='trimestre') return ti===gv('ej-trim-select'); if(ejPeriodoActivo==='semestre') return gv('ej-sem-select')===1?mi<=6:mi>6; return true; }
    function matchAmb(a,m,t){ if(parseInt(a)!==gv('amb-anio-select')) return false; const mi=parseInt(m),ti=parseInt(t); if(ambPeriodoActivo==='mes') return mi===gv('amb-mes-select'); if(ambPeriodoActivo==='trimestre') return ti===gv('amb-trim-select'); if(ambPeriodoActivo==='semestre') return gv('amb-sem-select')===1?mi<=6:mi>6; return true; }
    function labelEj(){ const a=gv('ej-anio-select'); if(ejPeriodoActivo==='mes') return (MESES_NOM[gv('ej-mes-select')-1]||'')+' '+a; if(ejPeriodoActivo==='trimestre') return 'T'+gv('ej-trim-select')+' '+a; if(ejPeriodoActivo==='semestre') return (gv('ej-sem-select')===1?'1er Sem.':'2do Sem.')+' '+a; return 'Año '+a; }
    function labelAmb(){ const a=gv('amb-anio-select'); if(ambPeriodoActivo==='mes') return (MESES_NOM[gv('amb-mes-select')-1]||'')+' '+a; if(ambPeriodoActivo==='trimestre') return 'T'+gv('amb-trim-select')+' '+a; if(ambPeriodoActivo==='semestre') return (gv('amb-sem-select')===1?'1er Sem.':'2do Sem.')+' '+a; return 'Año '+a; }
    function calcEj(){ let p=0,i=0; INICIADOS_DATA.forEach(r=>{ if(!matchEj(r.anio,r.mes,r.trimestre)) return; p+=parseInt(r.planificados)||0; i+=parseInt(r.iniciados)||0; }); return {planif:p,inic:i,pct:p>0?Math.round(i/p*100):0}; }
    function filtrarTipo(tipo){ return CURSOS_HISTORICO.filter(c=>c.tipo_ejecucion===tipo&&matchEj(c.anio,c.mes,c.trimestre)); }

    function renderDetalleCards(cursos,tipo,label){
        const wrap=document.getElementById('ej-detalle-wrap'), body=document.getElementById('ej-detalle-body');
        const titulo=document.getElementById('ej-detalle-titulo'), dot=document.getElementById('ej-detalle-dot');
        if(!wrap||!body) return;
        const cfg={pendiente:{label:'Cursos Pendientes',color:'#3b82f6'},iniciado:{label:'Cursos en Formación',color:'#22c55e'},diferido:{label:'Cursos Diferidos',color:'#d97706'}};
        const c=cfg[tipo]||{label:'Detalle',color:'#6366f1'};
        if(titulo) titulo.textContent=c.label+' — '+(label||'');
        if(dot) dot.style.background=c.color;
        if(!cursos.length){ body.innerHTML=`<p class="text-muted text-center py-3" style="font-size:.82rem;">Sin cursos en el periodo seleccionado.</p>`; }
        else {
            let rows='';
            cursos.forEach((cur,i)=>{ rows+=`<tr class="curso-row-link" data-nombre="${esc(cur.nombre_curso)}" style="cursor:pointer;" title="Ver curso"><td style="color:#94a3b8;font-size:.72rem;">${String(i+1).padStart(2,'0')}</td><td class="fw-semibold" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(cur.nombre_curso)}</td><td class="text-center"><span class="badge-mini" style="background:#e0f2fe;color:#0369a1;">${esc(cur.nombre_modalidad)}</span></td><td class="text-center"><span class="badge-mini" style="background:#ede9fe;color:#5b21b6;">${esc(cur.nombre_ambito)}</span></td><td class="text-center fw-bold">${cur.num_inscritos}</td><td class="text-center text-muted">${cur.max_participantes}</td><td class="text-center" style="font-size:.75rem;">${esc(cur.fecha_inicio||'—')}</td><td class="text-center"><span class="badge-mini" style="background:${c.color}22;color:${c.color};">${esc(cur.nombre_estatusCurso)}</span></td></tr>`; });
            body.innerHTML=`<table class="table table-sm table-hover table-striped align-middle mb-0"><thead class="table-light"><tr><th>#</th><th>Curso</th><th class="text-center">Modalidad</th><th class="text-center">Ámbito</th><th class="text-center">Inscritos</th><th class="text-center">Máx.</th><th class="text-center">Fecha inicio</th><th class="text-center">Estado</th></tr></thead><tbody>${rows}</tbody></table><div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;">↗ Haz clic en una fila para abrir el curso</div>`;
        }
        wrap.style.display='';
        setTimeout(()=>wrap.scrollIntoView({behavior:'smooth',block:'nearest'}),60);
    }

    function actualizarEj(){
        const {planif,inic,pct}=calcEj(); const dif=filtrarTipo('diferido'); const pen=filtrarTipo('pendiente');
        const label=labelEj(); const color=semC(pct);
        const elPct=document.getElementById('ej-pct-val'); if(elPct){elPct.textContent=pct+'%';elPct.className='ej-card-val '+semK(pct);}
        st('ej-iniciados-sub',inic+' de '+planif+' planificados');
        st('ej-plan-val',pen.length); st('ej-plan-sub','pendientes de iniciar');
        st('ej-ejec-val',inic); st('ej-ejec-sub',label);
        st('ej-dif-val',dif.length); st('ej-dif-sub',label);
        const fill=document.getElementById('ej-barra-fill'); if(fill){fill.style.width=Math.min(pct,100)+'%';fill.style.background=color;}
        st('ej-barra-pct',pct+'%');
        const bLbl=document.getElementById('ej-barra-label'); if(bLbl) bLbl.style.color=color;
        st('ej-periodo-label','Meta ≥ 85% · '+label);
        if(ejCardActiva){ const datos=ejCardActiva==='pendiente'?pen:ejCardActiva==='iniciado'?filtrarTipo('iniciado'):dif; renderDetalleCards(datos,ejCardActiva,label); }
    }

    ['card-planificados','card-iniciados','card-diferidos'].forEach(id=>{
        const card=document.getElementById(id); if(!card) return;
        card.addEventListener('click',()=>{
            const tipo=card.dataset.tipo;
            if(ejCardActiva===tipo){ ejCardActiva=null; card.classList.remove('ej-card-activa'); const w=document.getElementById('ej-detalle-wrap'); if(w) w.style.display='none'; return; }
            ['card-planificados','card-iniciados','card-diferidos'].forEach(cid=>{ const c=document.getElementById(cid); if(c) c.classList.remove('ej-card-activa'); });
            ejCardActiva=tipo; card.classList.add('ej-card-activa');
            renderDetalleCards(tipo==='pendiente'?filtrarTipo('pendiente'):tipo==='iniciado'?filtrarTipo('iniciado'):filtrarTipo('diferido'), tipo, labelEj());
        });
    });
    const btnC=document.getElementById('btn-cerrar-ej-detalle');
    if(btnC) btnC.addEventListener('click',()=>{ ejCardActiva=null; const w=document.getElementById('ej-detalle-wrap'); if(w) w.style.display='none'; ['card-planificados','card-iniciados','card-diferidos'].forEach(cid=>{ const c=document.getElementById(cid); if(c) c.classList.remove('ej-card-activa'); }); });

    document.querySelectorAll('.ej-filtro-btn').forEach(btn=>{ btn.addEventListener('click',()=>{ ejPeriodoActivo=btn.dataset.periodo; document.querySelectorAll('.ej-filtro-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); sv('ej-sel-mes',ejPeriodoActivo==='mes'); sv('ej-sel-trimestre',ejPeriodoActivo==='trimestre'); sv('ej-sel-semestre',ejPeriodoActivo==='semestre'); actualizarEj(); }); });
    ['ej-mes-select','ej-trim-select','ej-sem-select','ej-anio-select'].forEach(id=>{ const e=document.getElementById(id); if(e) e.addEventListener('change',actualizarEj); });
    actualizarEj();

    /* Ámbito */
    function calcAmbitoData(){ const m={}; AMBITO_HISTORICO.forEach(r=>{ if(!matchAmb(r.anio,r.mes,r.trimestre)) return; m[r.ambito]=(m[r.ambito]||0)+parseInt(r.total||0); }); return Object.entries(m).map(([a,t])=>({ambito:a,total:t})).sort((a,b)=>b.total-a.total); }
    function semAmb(t){ if(ambPeriodoActivo==='mes') return t>15?'verde':t>=10?'amarillo':'rojo'; if(ambPeriodoActivo==='trimestre') return t>45?'verde':t>=30?'amarillo':'rojo'; if(ambPeriodoActivo==='semestre') return t>90?'verde':t>=60?'amarillo':'rojo'; return t>180?'verde':t>=120?'amarillo':'rojo'; }
    function metaAmb(){ if(ambPeriodoActivo==='mes') return 15; if(ambPeriodoActivo==='trimestre') return 45; if(ambPeriodoActivo==='semestre') return 90; return 180; }
    function semLbl(s){ return s==='verde'?'Óptimo':s==='amarillo'?'Atención':'Crítico'; }
    const semColors={verde:'#28c76f',amarillo:'#f59e0b',rojo:'#ef4444'};

    function renderAmbito(){
        const datos=calcAmbitoData(); const total=datos.reduce((a,d)=>a+d.total,0); const label=labelAmb(); const sem=semAmb(total); const meta=metaAmb(); const barPct=meta>0?Math.min(Math.round(total/meta*100),100):0;
        const labelTipo=ambPeriodoActivo==='mes'?'este mes':ambPeriodoActivo==='trimestre'?'este trimestre':ambPeriodoActivo==='semestre'?'este semestre':'este año';
        const kL=document.getElementById('amb-kpi-label'); if(kL) kL.textContent='Total formaciones '+labelTipo;
        const kS=document.getElementById('amb-kpi-sem'); if(kS){kS.textContent=semLbl(sem);kS.className='semaforo-badge semaforo-'+sem;}
        const kV=document.getElementById('amb-kpi-valor'); if(kV){kV.textContent=total;kV.className='panel-mini-kpi-valor semaforo-text-'+sem;}
        const kSub=document.getElementById('amb-kpi-sub'); if(kSub) kSub.textContent=datos.length+' ámbito'+(datos.length!==1?'s':'')+' activos en '+label;
        const kB=document.getElementById('amb-kpi-barra'); if(kB){kB.style.width=barPct+'%';kB.style.background=semColors[sem]||'#28c76f';}
        const kBox=document.getElementById('amb-kpi-box'); if(kBox) kBox.className='panel-mini-kpi semaforo-borde-'+sem;
        const col=document.getElementById('amb-barras-col'); if(!col) return;
        if(!datos.length){col.innerHTML=`<p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin formaciones en el periodo seleccionado.</p>`;return;}
        const maxVal=datos[0].total;
        col.innerHTML=datos.map((d,i)=>{ const barW=maxVal>0?Math.round(d.total/maxVal*100):0; const color=COLORES_AMBITO[i%COLORES_AMBITO.length]; return `<div class="ambito-row"><span class="ambito-row-lbl" title="${esc(d.ambito)}">${esc(d.ambito)}</span><div class="kpi-adv-barra-fondo" style="height:10px;"><div style="width:${barW}%;height:10px;background:${color};border-radius:5px;transition:width .4s ease;"></div></div><span class="ambito-row-val" style="color:${color};">${d.total}</span></div>`; }).join('');
    }
    document.querySelectorAll('.amb-filtro-btn').forEach(btn=>{ btn.addEventListener('click',()=>{ ambPeriodoActivo=btn.dataset.periodo; document.querySelectorAll('.amb-filtro-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); sv('amb-sel-mes',ambPeriodoActivo==='mes'); sv('amb-sel-trimestre',ambPeriodoActivo==='trimestre'); sv('amb-sel-semestre',ambPeriodoActivo==='semestre'); renderAmbito(); }); });
    ['amb-mes-select','amb-trim-select','amb-sem-select','amb-anio-select'].forEach(id=>{ const e=document.getElementById(id); if(e) e.addEventListener('change',renderAmbito); });
})();
</script>
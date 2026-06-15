<!-- ════════════════════════════════════════════════════════════
     SECCIÓN 2 — PARTICIPANTES
     Vista: app/vista/Dashboard/seccion_participantes.php
════════════════════════════════════════════════════════════ -->
<div class="dashboard-section" id="section-participantes">

    <!-- ══════════════════════════════════════════════════════════════
         BLOQUE: Registros mensuales del año actual
         Gráfica de barras — participantes registrados por mes
    ══════════════════════════════════════════════════════════════════ -->
    <div class="mt-4" id="part-mensual-bloque">
        <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem 1rem;box-shadow:0 2px 12px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);">

            <!-- Cabecera -->
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.1rem;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#e0f9fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="#00c8e0" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:.72rem;color:#64748b;line-height:1.2;font-weight:500;">
                            Registros por mes · <?php echo $anioActual; ?>
                        </p>
                        <p style="margin:0;font-size:.88rem;font-weight:700;color:#1e293b;line-height:1.3;">
                            Participantes registrados en el año
                        </p>
                    </div>
                </div>

                <!-- Stats rápidas -->
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <?php
                    $totalAnio   = array_sum($partPorMes);
                    $mesMax      = array_search(max($partPorMes), $partPorMes);
                    $mesActualIdx = (int)date('n') - 1;
                    $mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                    $mesesFull    = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    $mesActualVal = $partPorMes[$mesActualIdx] ?? 0;

                    /* Promedio solo sobre meses con datos */
                    $mesesConDatos = array_filter($partPorMes, fn($v) => $v > 0);
                    $promMensual   = count($mesesConDatos) > 0 ? round(array_sum($partPorMes) / count($mesesConDatos), 1) : 0;
                    ?>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#00c8e0;line-height:1;"><?php echo $totalAnio; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Total año</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#7367f0;line-height:1;"><?php echo $mesActualVal; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;"><?php echo $mesesNombres[$mesActualIdx]; ?> actual</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#28c76f;line-height:1;"><?php echo $promMensual; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Promedio/mes</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#f59e0b;line-height:1;"><?php echo max($partPorMes); ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Pico · <?php echo $mesesNombres[$mesMax]; ?></div>
                    </div>
                </div>
            </div>

            <!-- Gráfica -->
            <div style="position:relative;height:190px;width:100%;">
                <canvas id="chartPartMensual"></canvas>
            </div>

            <!-- Leyenda de mes actual -->
            <div style="display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#00c8e0;"></span>
                    Mes con registros
                </span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#00c8e055;"></span>
                    Sin registros
                </span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#7367f0;"></span>
                    Mes actual (<?php echo $mesesFull[$mesActualIdx]; ?>)
                </span>
            </div>

        </div>
    </div>


    <!-- ══════════════════════════════════════════════════════════════
         PANEL PRINCIPAL: Análisis de Participantes
    ══════════════════════════════════════════════════════════════════ -->
    <div class="kpi-panel-inner mt-4" style="border-top-color:#00c8e0;">
        <div class="kpi-panel-header">
            <h6 class="kpi-panel-titulo" style="color:#00afc5;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                Análisis de Participantes
            </h6>
            <small class="text-muted">Datos en tiempo real · Semáforos activos</small>
        </div>

        <!-- ── GRÁFICAS PARTICIPANTES ── -->
        <div class="chart-tabs-wrapper mt-3">
            <nav class="chart-tabs-nav" id="chart-nav-participantes">
                <button class="chart-tab-btn active" data-section="participantes" data-chart="estado">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                    Por Estado
                </button>
                <button class="chart-tab-btn" data-section="participantes" data-chart="genero">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5"/></svg>
                    Por Género
                </button>
                <button class="chart-tab-btn" data-section="participantes" data-chart="cursos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                    Por Curso
                </button>
            </nav>
            <div class="chart-tabs-body">

                <!-- TAB: Por Estado -->
                <div class="chart-tab-panel active" id="chart-panel-participantes-estado">
                    <p class="chart-panel-title">
                        Distribución por estado
                        <small class="text-muted" style="font-size:.72rem;">(Inactivo y En sistema se muestran como Sin curso activo)</small>
                    </p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartPartEstado"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Nombre</th><th>Cédula</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php foreach ($ultimosPart as $p):
                                    $estadoLabel = in_array($p['estado'], ['Inactivo','En sistema']) ? 'Sin curso activo' : $p['estado'];
                                    $ce = ['En formación' => '#28c76f', 'Disponible' => '#f59e0b', 'Asignado' => '#0ea5e9', 'Sin curso activo' => '#94a3b8'][$estadoLabel] ?? '#94a3b8';
                                ?>
                                    <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'] . ' ' . $p['primer_apellido']); ?></td>
                                        <td><?php echo $p['cedula']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $ce; ?>22;color:<?php echo $ce; ?>;"><?php echo $estadoLabel; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Género -->
                <div class="chart-tab-panel" id="chart-panel-participantes-genero">
                    <p class="chart-panel-title">Distribución por género</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartPartGenero"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Nombre</th><th>Cédula</th><th>Género</th></tr></thead>
                                <tbody>
                                <?php foreach ($ultimosPart as $p):
                                    $gc = $p['genero'] === 'Masculino' ? '#0ea5e9' : '#ec4899';
                                ?>
                                    <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'] . ' ' . $p['primer_apellido']); ?></td>
                                        <td><?php echo $p['cedula']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $gc; ?>22;color:<?php echo $gc; ?>;"><?php echo $p['genero']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Curso -->
                <div class="chart-tab-panel" id="chart-panel-participantes-cursos">
                    <p class="chart-panel-title">Distribución de inscritos por curso</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap" style="height:280px;">
                            <canvas id="chartPartCursos"></canvas>
                        </div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Participante</th><th>Cédula</th><th>Curso</th></tr></thead>
                                <tbody>
                                <?php foreach ($cardFormacion as $p): ?>
                                    <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($p['cedula']); ?></td>
                                        <td style="font-size:.73rem;color:#475569;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                            title="<?php echo htmlspecialchars($p['cursos'] ?? '—'); ?>">
                                            <?php echo htmlspecialchars($p['cursos'] ?? '—'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($cardFormacion)): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">Sin participantes en formación.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /chart-tabs-wrapper participantes -->

        <!-- ── INDICADORES DE DESEMPEÑO ── -->
        <div class="card-group-container bg-light p-3 rounded-3 mt-3">
            <div class="panel-section-header">
                <span class="panel-section-dot" style="background:#6366f1;"></span>
                <span class="panel-section-label">Indicadores de Desempeño</span>
                <small class="text-muted ms-auto">Haz clic en una tarjeta para ver el detalle</small>
            </div>

            <?php
            /*
             * ── Lógica unificada de estados ──────────────────────────────
             * Con curso activo  : En formación + Asignado
             * Sin curso activo  : Disponible + En sistema + Diferido
             *   (todo participante sin curso sigue siendo hábil)
             * ─────────────────────────────────────────────────────────────
             */
            $total_part = (int)$kAsignacion['total'];

            /* Con curso activo */
            $conCurso    = (int)$kAsignacion['activos'];    /* En formación + Asignado */
            $pctConCurso = $total_part > 0 ? round($conCurso / $total_part * 100, 1) : 0;
            $pctFormKpi  = $total_part > 0 ? round((int)$kAsignacion['en_formacion'] / $total_part * 100, 1) : 0;
            $pctAsigKpi  = $total_part > 0 ? round((int)$kAsignacion['asignados']    / $total_part * 100, 1) : 0;
            $sConCurso   = sem($pctConCurso, 70, 84);

            /* Sin curso activo (disponibles para asignar) */
            $sinCurso    = $total_part - $conCurso;         /* todos los demás */
            $pctSinCurso = $total_part > 0 ? round($sinCurso / $total_part * 100, 1) : 0;
            /* Desglose: Disponible | En sistema | Diferido */
            $cntDisp     = (int)$kDisponibles['disponibles'];
            $cntSistema  = (int)$kInactivos['inactivos'];   /* kInactivos agrupa En sistema */
            $cntDiferido = max(0, $sinCurso - $cntDisp - $cntSistema);
            /* Semáforo invertido: muchos sin curso = rojo */
            $sSinCurso   = sem($pctSinCurso, 20, 35, true);
            ?>

            <div class="row g-3 justify-content-center mx-auto" id="kpi-cards-participantes" style="max-width:1050px;">

                <!-- ── Card A: Con curso activo (En formación + Asignado) ── -->
                <div class="col-6 col-xl-4">
                    <div class="panel-mini-kpi semaforo-borde-<?php echo $sConCurso; ?> kpi-seleccionable"
                         data-kpi="activos">
                        <span class="kpi-selector-dot"></span>
                        <div class="panel-mini-kpi-header">
                            <span class="panel-mini-kpi-label">Con curso activo</span>
                            <span class="semaforo-badge semaforo-<?php echo $sConCurso; ?>"><?php echo semL($sConCurso); ?></span>
                        </div>
                        <div class="panel-mini-kpi-valor semaforo-text-<?php echo $sConCurso; ?>"><?php echo $pctConCurso; ?>%</div>
                        <div class="panel-mini-kpi-sub d-flex flex-column gap-1 mt-1">
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#28c76f;margin-right:4px;"></span>
                                En formación: <strong><?php echo $kAsignacion['en_formacion']; ?></strong>
                            </span>
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0ea5e9;margin-right:4px;"></span>
                                Asignados: <strong><?php echo $kAsignacion['asignados']; ?></strong>
                            </span>
                            <span style="color:#64748b;">Total: <?php echo $conCurso; ?> de <?php echo $total_part; ?></span>
                        </div>
                        <div class="kpi-adv-barra-fondo mt-2">
                            <div style="display:flex;height:8px;border-radius:4px;overflow:hidden;">
                                <div style="width:<?php echo $pctFormKpi; ?>%;background:#28c76f;"></div>
                                <div style="width:<?php echo $pctAsigKpi; ?>%;background:#0ea5e9;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Card B: Disponibles para asignar (sin curso activo) ── -->
                <div class="col-6 col-xl-4">
                    <div class="panel-mini-kpi semaforo-borde-<?php echo $sSinCurso; ?> kpi-seleccionable"
                         data-kpi="disponibles">
                        <span class="kpi-selector-dot"></span>
                        <div class="panel-mini-kpi-header">
                            <span class="panel-mini-kpi-label">Disponibles para asignar</span>
                            <span class="semaforo-badge semaforo-<?php echo $sSinCurso; ?>"><?php echo semL($sSinCurso); ?></span>
                        </div>
                        <div class="panel-mini-kpi-valor semaforo-text-<?php echo $sSinCurso; ?>"><?php echo $pctSinCurso; ?>%</div>
                        <div class="panel-mini-kpi-sub d-flex flex-column gap-1 mt-1">
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#f59e0b;margin-right:4px;"></span>
                                Disponible: <strong><?php echo $cntDisp; ?></strong>
                            </span>
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#94a3b8;margin-right:4px;"></span>
                                En sistema: <strong><?php echo $cntSistema; ?></strong>
                            </span>
                            <?php if ($cntDiferido > 0): ?>
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#c084fc;margin-right:4px;"></span>
                                Diferido: <strong><?php echo $cntDiferido; ?></strong>
                            </span>
                            <?php endif; ?>
                            <span style="color:#64748b;">Total: <?php echo $sinCurso; ?> de <?php echo $total_part; ?></span>
                        </div>
                        <div class="kpi-adv-barra-fondo mt-2">
                            <?php
                            $pctD2 = $total_part > 0 ? round($cntDisp    / $total_part * 100, 1) : 0;
                            $pctS2 = $total_part > 0 ? round($cntSistema  / $total_part * 100, 1) : 0;
                            $pctDF = $total_part > 0 ? round($cntDiferido / $total_part * 100, 1) : 0;
                            ?>
                            <div style="display:flex;height:8px;border-radius:4px;overflow:hidden;">
                                <div style="width:<?php echo $pctD2; ?>%;background:#f59e0b;"></div>
                                <div style="width:<?php echo $pctS2; ?>%;background:#94a3b8;"></div>
                                <div style="width:<?php echo $pctDF; ?>%;background:#c084fc;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Card C: Participantes formados ── -->
                <div class="col-6 col-xl-4">
                    <div class="panel-mini-kpi kpi-seleccionable semaforo-borde-<?php echo $s_formados; ?>"
                         style="color:<?php echo $s_formados==='verde'?'#28c76f':($s_formados==='amarillo'?'#f59e0b':'#ef4444'); ?>;"
                         data-kpi="formados">
                        <span class="kpi-selector-dot"></span>
                        <div class="panel-mini-kpi-header">
                            <span class="panel-mini-kpi-label">Participantes formados</span>
                            <span class="semaforo-badge semaforo-<?php echo $s_formados; ?>"><?php echo semL($s_formados); ?></span>
                        </div>
                        <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_formados; ?>"><?php echo $kFormados['total']; ?></div>
                        <div class="panel-mini-kpi-sub d-flex flex-column gap-1 mt-1">
                            <span>
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#28c76f;margin-right:4px;"></span>
                                Participantes aprobados: <strong><?php echo $kFormados['total_cursos']; ?></strong>
                            </span>
                            <span style="color:#94a3b8;font-size:.7rem;">
                                <?php echo $pctFormados; ?>% del total · año <?php echo $kFormados['anio']; ?>
                            </span>
                        </div>
                        <div class="kpi-adv-barra-fondo mt-2">
                            <div class="kpi-adv-barra semaforo-fill-<?php echo $s_formados; ?>"
                                 style="width:<?php echo min($pctFormados, 100); ?>%;"></div>
                        </div>
                    </div>
                </div>

            </div><!-- /row kpi-cards-participantes -->

            <!-- ── PANEL DESPLEGABLE DE DETALLE ── -->
            <div id="kpi-detalle-wrapper" class="kpi-detalle-wrapper mt-3">
                <div class="kpi-detalle-header">
                    <span class="kpi-detalle-titulo" id="kpi-detalle-titulo"></span>
                    <button class="kpi-detalle-close" id="kpi-detalle-close" title="Cerrar">✕</button>
                </div>
                <div class="kpi-detalle-scroll">
                    <table class="table table-sm table-striped align-middle mb-0" id="kpi-detalle-tabla">
                        <thead class="table-light" id="kpi-detalle-thead"></thead>
                        <tbody id="kpi-detalle-tbody"></tbody>
                    </table>
                </div>
                <div class="kpi-detalle-footer">
                    <span id="kpi-detalle-count">—</span>
                    <span style="color:#94a3b8;">Haz clic en una fila para ver el participante</span>
                </div>
            </div>

            <!-- ── TABLA: Sin curso activo · disponibles para asignar ── -->
            <div class="panel-section-header mt-3">
                <span class="panel-section-dot" style="background:#f59e0b;"></span>
                <span class="panel-section-label">Sin curso activo · disponibles para asignar</span>
                <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
            </div>
            <?php
            /* Todos los participantes sin curso activo:
               estado Disponible + Inactivo + En sistema */
            $todosSinCurso = array_merge(
                $cardDisponibles,
                $cardInactivos
            );
            usort($todosSinCurso, fn($a,$b) => strcmp($a['primer_apellido']??'', $b['primer_apellido']??''));
            $estadoColorMap = [
                'Disponible' => ['bg'=>'#fef9ec','fg'=>'#92400e'],
                'En sistema' => ['bg'=>'#f1f5f9','fg'=>'#475569'],
                'Inactivo'   => ['bg'=>'#f8fafc','fg'=>'#64748b'],
            ];
            ?>
            <?php if (empty($todosSinCurso)): ?>
                <p class="text-success py-2 fw-semibold mb-0" style="font-size:.85rem;">✓ Todos los participantes tienen un curso activo asignado.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Nombre</th><th>Cédula</th><th>Estado</th><th>Género</th><th>Grado</th><th>Fecha Registro</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($todosSinCurso as $p):
                        $eBg = $estadoColorMap[$p['estado']]['bg'] ?? '#f8fafc';
                        $eFg = $estadoColorMap[$p['estado']]['fg'] ?? '#64748b';
                    ?>
                        <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                            <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'] . ' ' . $p['primer_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($p['cedula']); ?></td>
                            <td><span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:.68rem;font-weight:600;background:<?php echo $eBg; ?>;color:<?php echo $eFg; ?>;"><?php echo htmlspecialchars($p['estado']); ?></span></td>
                            <td><?php echo htmlspecialchars($p['genero']); ?></td>
                            <td><?php echo htmlspecialchars($p['grado_institucion'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($p['fecha_registro'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- ── TABLA: Últimos 5 registrados ── -->
            <div class="panel-section-header mt-3">
                <span class="panel-section-dot" style="background:#00c8e0;"></span>
                <span class="panel-section-label">Últimos 5 Registrados</span>
                <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Nombre</th><th>Cédula</th><th>Estado</th><th>Género</th><th class="text-center">Edad</th><th>Fecha Registro</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ultimosPart as $p):
                        $estadoLabel2 = in_array($p['estado'], ['Inactivo','En sistema']) ? 'Sin curso activo' : $p['estado'];
                        $ce2 = ['En formación' => 'success', 'Disponible' => 'warning', 'Asignado' => 'info', 'Sin curso activo' => 'secondary'][$estadoLabel2] ?? 'secondary';
                    ?>
                        <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                            <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'] . ' ' . $p['primer_apellido']); ?></td>
                            <td><?php echo $p['cedula']; ?></td>
                            <td><span class="badge bg-<?php echo $ce2; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $estadoLabel2; ?></span></td>
                            <td><?php echo $p['genero']; ?></td>
                            <td class="text-center fw-semibold"><?php echo isset($p['edad']) ? $p['edad'] . ' años' : '—'; ?></td>
                            <td><?php echo $p['fecha_registro']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ultimosPart)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Sin registros.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /card-group-container indicadores -->
    </div><!-- /kpi-panel-inner -->
</div><!-- /section-participantes -->


<!-- ════════════════════════════════════════════════════════
     JS — Gráfica mensual de participantes
     + Cards KPI seleccionables (panel de detalle)
════════════════════════════════════════════════════════ -->
<script>
/* ── Gráfica de barras mensual ─────────────────────────────── */
(function renderPartMensual() {
    function build() {
        const ctx = document.getElementById('chartPartMensual');
        if (!ctx) return;
        if (typeof Chart === 'undefined') { setTimeout(build, 100); return; }

        const data       = <?php echo json_encode(array_values($partPorMes)); ?>;
        const mesActual  = <?php echo (int)date('n') - 1; ?>; // índice 0-based
        const labels     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const COLOR_BASE = '#00c8e0';
        const COLOR_CURR = '#7367f0';
        const COLOR_NONE = '#00c8e033';

        const bgColors     = data.map((v, i) => i === mesActual ? COLOR_CURR : (v > 0 ? COLOR_BASE : COLOR_NONE));
        const borderColors = data.map((v, i) => i === mesActual ? COLOR_CURR : (v > 0 ? COLOR_BASE : 'transparent'));
        const maxVal       = Math.max(...data, 1);

        /* Plugin: valor encima de cada barra */
        const pluginValoresBarra = {
            id: 'valoresBarra',
            afterDatasetsDraw(chart) {
                const { ctx: c, data: d, scales: { x, y } } = chart;
                d.datasets[0].data.forEach((val, i) => {
                    if (!val) return;
                    const bar = chart.getDatasetMeta(0).data[i];
                    c.save();
                    c.fillStyle    = i === mesActual ? COLOR_CURR : '#475569';
                    c.font         = `600 11px Roboto, sans-serif`;
                    c.textAlign    = 'center';
                    c.textBaseline = 'bottom';
                    c.fillText(val, bar.x, bar.y - 3);
                    c.restore();
                });
            }
        };

        if (window._chartPartMensual) window._chartPartMensual.destroy();
        window._chartPartMensual = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor:  bgColors,
                    borderColor:      borderColors,
                    borderWidth:      1.5,
                    borderRadius:     6,
                    borderSkipped:    false,
                    hoverBackgroundColor: data.map((v, i) => i === mesActual ? '#5b52d6' : '#00afc5'),
                }]
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                layout: { padding: { top: 20 } },
                onClick: (event, elements) => {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    const mesNum = index + 1;
                    const names = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    const mesLabel = names[index];
                    mostrarDetalleParticipantesMes(mesNum, mesLabel);
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
                            title:  items => labels[items[0].dataIndex],
                            label:  item  => {
                                const v = item.parsed.y;
                                const s = v === 1 ? 'participante' : 'participantes';
                                return ` ${v} ${s} registrados`;
                            },
                            afterLabel: item => {
                                const total = data.reduce((a,b) => a+b, 0);
                                const pct   = total > 0 ? Math.round(item.parsed.y / total * 100) : 0;
                                return ` ${pct}% del total anual`;
                            }
                        },
                        backgroundColor: 'rgba(15,23,42,.88)',
                        titleFont: { size: 12, weight: 600 },
                        bodyFont:  { size: 11 },
                        padding: 10, cornerRadius: 7
                    }
                },
                scales: {
                    x: {
                        grid:   { display: false },
                        border: { display: false },
                        ticks:  {
                            font:  { size: 11, weight: (ctx) => ctx.index === mesActual ? 700 : 400 },
                            color: (ctx) => ctx.index === mesActual ? COLOR_CURR : '#64748b',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxVal + Math.ceil(maxVal * 0.18),
                        grid:   { color: 'rgba(0,0,0,.05)' },
                        border: { display: false },
                        ticks:  {
                            font:      { size: 10 },
                            color:     '#94a3b8',
                            stepSize:  Math.max(1, Math.ceil(maxVal / 5)),
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Número Participantes',
                            color: '#64748b',
                            font: { size: 11, weight: 600 }
                        }
                    }
                }
            },
            plugins: [pluginValoresBarra]
        });
    }
    build();
})();


/* ── Gráfica de barras horizontales: inscritos por curso ───── */
window.renderPartCursos = function () {
    const ctx = document.getElementById('chartPartCursos');
    if (!ctx || typeof Chart === 'undefined') return;
    if (window._chartPartCursos) window._chartPartCursos.destroy();

    const raw = <?php
        /* Agrupa cursosDetalle por nombre y toma solo los que tienen inscritos */
        $cd_js = array_filter($cursosDetalle ?? [], fn($c) => (int)$c['num_inscritos'] > 0);
        usort($cd_js, fn($a,$b) => (int)$b['num_inscritos'] - (int)$a['num_inscritos']);
        $cd_js = array_values(array_slice($cd_js, 0, 15)); /* máx 15 para legibilidad */
        echo json_encode(array_map(fn($c) => [
            'nombre'    => $c['nombre_curso'],
            'inscritos' => (int)$c['num_inscritos'],
            'cupos'     => (int)$c['max_participantes'],
        ], $cd_js), JSON_UNESCAPED_UNICODE);
    ?>;

    if (!raw.length) return;

    const labels  = raw.map(c => c.nombre.length > 28 ? c.nombre.slice(0,26)+'…' : c.nombre);
    const vals    = raw.map(c => c.inscritos);
    const maxI    = vals.indexOf(Math.max(...vals));
    const PALETA  = ['#00c8e0','#7367f0','#28c76f','#f59e0b','#0ea5e9','#ec4899',
                     '#10b981','#6366f1','#ef4444','#8b5cf6','#f97316','#14b8a6',
                     '#a855f7','#fb923c','#22d3ee'];
    const bgColors = vals.map((_, i) => i === maxI ? '#7367f0' : PALETA[i % PALETA.length]);

    /* Plugin: valor al final de cada barra */
    const pluginValH = {
        id: 'valH',
        afterDatasetsDraw(chart) {
            const { ctx: c, scales: { x } } = chart;
            chart.getDatasetMeta(0).data.forEach((bar, i) => {
                const v = vals[i];
                c.save();
                c.fillStyle    = '#475569';
                c.font         = '600 10px Roboto, sans-serif';
                c.textAlign    = 'left';
                c.textBaseline = 'middle';
                c.fillText(v, bar.x + 5, bar.y);
                c.restore();
            });
        }
    };

    window._chartPartCursos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data:            vals,
                backgroundColor: bgColors,
                borderRadius:    5,
                borderSkipped:   false,
                barThickness:    18,
            }]
        },
        options: {
            indexAxis:           'y',
            responsive:          true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
            layout:   { padding: { right: 36 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title:  items => raw[items[0].dataIndex].nombre,
                        label:  item  => {
                            const r   = raw[item.dataIndex];
                            const pct = r.cupos > 0 ? Math.round(r.inscritos / r.cupos * 100) : 0;
                            return ` ${r.inscritos} inscritos de ${r.cupos} cupos (${pct}% uso)`;
                        }
                    },
                    backgroundColor: 'rgba(15,23,42,.88)',
                    titleFont: { size: 12, weight: 600 },
                    bodyFont:  { size: 11 },
                    padding: 10, cornerRadius: 7
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid:   { color: 'rgba(0,0,0,.05)' },
                    border: { display: false },
                    ticks:  { font: { size: 10 }, color: '#94a3b8', precision: 0 }
                },
                y: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { font: { size: 10 }, color: '#334155' }
                }
            }
        },
        plugins: [pluginValH]
    });
};

/* Registrar en CHART_MAP para que home.js lo active al cambiar de tab */
if (typeof CHART_MAP !== 'undefined') {
    if (!CHART_MAP.participantes) CHART_MAP.participantes = {};
    CHART_MAP.participantes.cursos = {
        chartId: 'chartPartCursos',
        render:  'renderPartCursos'
    };
}


/* ── Cards KPI seleccionables (panel de detalle) ──────────── */
(function () {
    const DATA = {
        disponibles: <?php echo $jsCardDisponibles; ?>,
        formacion:   <?php echo $jsCardFormacion; ?>,
        asignacion:  <?php echo $jsCardAsignados; ?>,
        inactivos:   <?php echo $jsCardInactivos; ?>
    };

    /* Con curso activo = En formación + Asignados */
    DATA.activos = [
        ...DATA.formacion.map(p  => ({ ...p, _subtipo: 'En formación' })),
        ...DATA.asignacion.map(p => ({ ...p, _subtipo: 'Asignado' }))
    ].sort((a, b) => (a.primer_apellido || '').localeCompare(b.primer_apellido || ''));

    /* Sin curso activo = Disponible + Inactivo + En sistema */
    const _sinCursoRaw = [
        ...DATA.disponibles.map(p => ({ ...p, _subtipo: 'Disponible' })),
        ...DATA.inactivos.map(p   => ({ ...p, _subtipo: p.estado === 'En sistema' ? 'En sistema' : 'Sin curso' }))
    ].sort((a, b) => (a.primer_apellido || '').localeCompare(b.primer_apellido || ''));

    /* La card data-kpi="disponibles" debe mostrar el conjunto completo sin curso */
    DATA.disponibles = _sinCursoRaw;

    /* Género: fusión completa ordenada por apellido */
    DATA.genero = [
        ...DATA.formacion,
        ...DATA.asignacion,
        ...DATA.inactivos,
        /* cardDisponibles viene ya en DATA original antes de ser sobreescrito */
    ].sort((a, b) => (a.primer_apellido || '').localeCompare(b.primer_apellido || ''));
    /* Nota: se usa _sinCursoRaw para no perder los disponibles originales */
    DATA.genero = [
        ...DATA.formacion.map(p  => ({ ...p, _subtipo: 'En formación' })),
        ...DATA.asignacion.map(p => ({ ...p, _subtipo: 'Asignado' })),
        ..._sinCursoRaw
    ].sort((a, b) => (a.primer_apellido || '').localeCompare(b.primer_apellido || ''));

    /* Participantes formados — viene de $jsCardFormados inyectado por datos_participantes.php */
    DATA.formados = <?php echo $jsCardFormados; ?>;

    const CONFIG = {
        disponibles: {
            titulo: 'Sin curso activo · disponibles para asignar',
            color:  '#f59e0b',
            thead:  ['#','Nombre','Cédula','Género','Grado','Edad','Estado'],
            row: (p, i) => {
                const subColor = p._subtipo === 'Disponible' ? '#92400e'
                               : p._subtipo === 'En sistema' ? '#475569' : '#64748b';
                const subBg    = p._subtipo === 'Disponible' ? '#fef9ec'
                               : p._subtipo === 'En sistema' ? '#f1f5f9' : '#f8fafc';
                return `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td><span class="badge-mini" style="background:${subBg};color:${subColor};">${esc(p._subtipo)}</span></td>
                </tr>`;
            }
        },
        activos: {
            titulo: 'Con curso activo (En formación + Asignados)',
            color:  '#6366f1',
            thead:  ['#','Nombre','Cédula','Género','Grado','Edad','Situación'],
            row: (p, i) => {
                const subColor = p._subtipo === 'En formación' ? '#28c76f' : '#0ea5e9';
                return `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td><span class="badge-mini" style="background:${subColor}22;color:${subColor};">${esc(p._subtipo)}</span>
                        ${p._subtipo==='En formación' ? `<br><span style="font-size:.7rem;color:#64748b;">${esc(p.cursos||'—')}</span>` : ''}
                    </td>
                </tr>`;
            }
        },
        inactivos: {
            /* Alias de seguridad */
            titulo: '📋 Sin curso activo · disponibles para asignar',
            color:  '#f59e0b',
            thead:  ['#','Nombre','Cédula','Género','Grado','Edad','Estado'],
            row: (p, i) => CONFIG.disponibles.row(p, i)
        },
        formados: {
            titulo: `Participantes formados · año ${new Date().getFullYear()}`,
            color:  '#28c76f',
            thead:  ['#','Nombre','Cédula','Género','Grado','Cursos aprobados','Último curso'],
            row: (p, i) => `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td style="text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:4px;font-weight:700;color:#28c76f;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#28c76f" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                            ${p.total_cursos_aprobados}
                        </span>
                        <div style="font-size:.68rem;color:#64748b;margin-top:2px;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${esc(p.cursos_aprobados||'—')}">${esc(p.cursos_aprobados||'—')}</div>
                    </td>
                    <td style="font-size:.73rem;color:#64748b;">${esc(p.ultimo_curso_fecha||'—')}</td>
                </tr>`
        }
    };

    let cardActiva = null;

    function esc(str) {
        return String(str || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function abrirDetalle(kpi, cardEl) {
        const cfg  = CONFIG[kpi];
        const data = DATA[kpi];
        if (!cfg || !data) return;

        document.querySelectorAll('#section-participantes .kpi-seleccionable').forEach(c => c.classList.remove('kpi-activa'));

        if (cardActiva === kpi) { cerrarDetalle(); return; }
        cardActiva = kpi;
        cardEl.classList.add('kpi-activa');

        const tituloEl = document.getElementById('kpi-detalle-titulo');
        if (tituloEl) tituloEl.innerHTML = `<span style="width:10px;height:10px;border-radius:50%;background:${cfg.color};display:inline-block;"></span> ${cfg.titulo}`;

        const thead = document.getElementById('kpi-detalle-thead');
        if (thead) thead.innerHTML = '<tr>' + cfg.thead.map(h => `<th style="font-size:.73rem;white-space:nowrap;">${h}</th>`).join('') + '</tr>';

        const tbody = document.getElementById('kpi-detalle-tbody');
        if (tbody) tbody.innerHTML = data.length
            ? data.map((p, i) => cfg.row(p, i)).join('')
            : `<tr><td colspan="${cfg.thead.length}" class="text-center text-muted py-3">Sin registros para mostrar.</td></tr>`;

        const countEl = document.getElementById('kpi-detalle-count');
        if (countEl) countEl.textContent = `${data.length} registro${data.length !== 1 ? 's' : ''}`;

        const wrapper = document.getElementById('kpi-detalle-wrapper');
        if (wrapper) {
            wrapper.classList.remove('visible');
            void wrapper.offsetHeight;
            wrapper.classList.add('visible');
            setTimeout(() => wrapper.scrollIntoView({ behavior:'smooth', block:'nearest' }), 50);
        }
    }

    function cerrarDetalle() {
        cardActiva = null;
        document.querySelectorAll('#section-participantes .kpi-seleccionable').forEach(c => c.classList.remove('kpi-activa'));
        const wrapper = document.getElementById('kpi-detalle-wrapper');
        if (wrapper) wrapper.classList.remove('visible');
    }

    document.querySelectorAll('#section-participantes .kpi-seleccionable[data-kpi]').forEach(card => {
        card.addEventListener('click', () => abrirDetalle(card.dataset.kpi, card));
    });

    const btnClose = document.getElementById('kpi-detalle-close');
    if (btnClose) btnClose.addEventListener('click', cerrarDetalle);
})();
</script>

<!-- Modal Detalle Participantes Registrados por Mes -->
<style>
.modal-part-row {
    transition: all 0.2s ease-in-out;
}
.modal-part-row:hover td {
    background-color: #f8fafc !important;
}
.modal-part-row:hover {
    transform: translateY(-1px);
}
.modal-part-row:hover svg.chevron-arrow {
    transform: translateX(4px);
    fill: #0d9488;
}
.sticky-thead th {
    background-color: #f8fafc !important;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: none !important;
    padding: 10px 12px !important;
    white-space: nowrap;
}
.modal-part-table td {
    padding: 8px 12px !important;
    white-space: nowrap;
    font-size: 0.8rem;
    background-color: #ffffff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.modal-part-table td:first-child {
    border-left: 1px solid #e2e8f0;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}
.modal-part-table td:last-child {
    border-right: 1px solid #e2e8f0;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}
.modal-part-search-input {
    border-radius: 20px;
    padding: 10px 16px 10px 42px;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    font-size: 0.85rem;
    transition: all 0.2s ease-in-out;
}
.modal-part-search-input:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
    outline: none;
}
.modal-close-btn-custom {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.2s ease-in-out;
}
.modal-close-btn-custom:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
    color: white;
}
.empty-state-icon {
    background: #f0fdfa;
    color: #0d9488;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}
</style>

<div class="modal fade" id="modal-DetalleParticipantesMes" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <!-- Header con gradiente premium -->
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f766e, #0d9488, #0ea5e9); padding: 18px 24px; border-bottom: none;">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalDetalleLabel">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.047 1.09-2.904.243-.294.526-.569.846-.816M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m1.89-3.921a3 3 0 1 1-4.226 4.226 3 3 0 0 1 4.226-4.226"/>
          </svg>
          Registros de <span id="modal-part-mes-nombre">Mes</span> · <span id="modal-part-mes-anio"><?php echo $anioActual; ?></span>
        </h5>
        <button type="button" class="modal-close-btn-custom" data-bs-dismiss="modal" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
          </svg>
        </button>
      </div>
      
      <!-- Cuerpo del modal -->
      <div class="modal-body p-4" style="background-color: #f8fafc; min-height: 250px;">
        
        <!-- Barra de búsqueda e información con mejor diseño -->
        <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
          <div class="position-relative flex-grow-1" style="min-width: 250px;">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="color: #94a3b8;">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
              </svg>
            </span>
            <input type="text" id="modal-part-search" class="form-control modal-part-search-input ps-5 w-100 shadow-sm" placeholder="Buscar por nombre, cédula o estado...">
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-teal-soft text-teal border border-teal-soft fw-semibold px-3 py-2" style="font-size: .75rem; border-radius: 20px; display: none; background-color: #e0f2fe; color: #0369a1;" id="modal-part-filtered-wrapper">
              Mostrados: <span id="modal-part-filtered-count">0</span>
            </span>
          </div>
        </div>

        <!-- Spinner de carga -->
        <div id="modal-part-loading" class="text-center py-5" style="display: none;">
          <div class="spinner-border text-teal" role="status" style="color: #0d9488;">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="text-muted mt-3 small fw-medium">Obteniendo listado de participantes...</p>
        </div>
        
        <!-- Estado sin datos -->
        <div id="modal-part-sin-datos" class="text-center py-5 text-muted" style="display: none;">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
              <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252(1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
            </svg>
          </div>
          <h6 class="fw-bold text-dark mb-1">Sin registros</h6>
          <p class="text-muted small mb-0">No se encontraron participantes registrados en este mes.</p>
        </div>

        <!-- Tabla interactiva con diseño premium -->
        <div id="modal-part-contenido" class="table-responsive" style="max-height: 400px; display: none; border-radius: 8px;">
          <table class="table align-middle mb-0 modal-part-table" style="border-collapse: separate; border-spacing: 0 6px;">
            <thead class="sticky-top sticky-thead">
              <tr>
                <th style="border-top-left-radius: 8px;">Participante</th>
                <th>Cédula</th>
                <th>Estado</th>
                <th>Género</th>
                <th>Instrucción</th>
                <th class="text-center">Edad</th>
                <th>Fecha Registro</th>
                <th style="border-top-right-radius: 8px; width: 4%;"></th>
              </tr>
            </thead>
            <tbody id="modal-part-tbody" style="background-color: transparent;">
              <!-- rows dynamically filled here -->
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer con resumen -->
      <div class="modal-footer border-top-0 d-flex justify-content-between p-3" style="background-color: #f1f5f9;">
        <span class="badge bg-dark bg-opacity-10 text-dark-emphasis px-3 py-2 fw-semibold" id="modal-part-count" style="font-size: 0.78rem; border-radius: 20px;">0 participantes</span>
        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 600; padding: 6px 16px; background-color: #64748b; border: none;">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Funciones auxiliares para avatares e iniciales
function getInitials(name) {
    if (!name) return '??';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0].substring(0, 2).toUpperCase();
}

function getAvatarColor(name) {
    if (!name) return { bg: '#e2e8f0', text: '#475569' };
    const colors = [
        { bg: '#fee2e2', text: '#b91c1c' }, // rojo
        { bg: '#ffedd5', text: '#c2410c' }, // naranja
        { bg: '#fef3c7', text: '#b45309' }, // ámbar
        { bg: '#ecfdf5', text: '#047857' }, // esmeralda
        { bg: '#f0fdf4', text: '#15803d' }, // verde
        { bg: '#e0f2fe', text: '#0369a1' }, // sky
        { bg: '#eff6ff', text: '#1d4ed8' }, // azul
        { bg: '#f5f3ff', text: '#6d28d9' }, // violeta
        { bg: '#fdf2f8', text: '#be185d' }, // rosa
        { bg: '#e0f7fa', text: '#00838f' }  // cian oscuro
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
}

// Registramos el buscador del modal una sola vez
(function setupModalSearch() {
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('modal-part-search');
        if (input) {
            input.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#modal-part-tbody tr');
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
                
                const wrapper = document.getElementById('modal-part-filtered-wrapper');
                if (query.length > 0) {
                    wrapper.style.display = 'inline-block';
                    document.getElementById('modal-part-filtered-count').textContent = visibles;
                } else {
                    wrapper.style.display = 'none';
                }
            });
        }
    });
})();

function mostrarDetalleParticipantesMes(mesNum, mesLabel) {
    document.getElementById('modal-part-mes-nombre').textContent = mesLabel;
    
    // Clear search input
    document.getElementById('modal-part-search').value = '';
    document.getElementById('modal-part-filtered-wrapper').style.display = 'none';
    
    // Show loading, hide others
    document.getElementById('modal-part-loading').style.display = 'block';
    document.getElementById('modal-part-sin-datos').style.display = 'none';
    document.getElementById('modal-part-contenido').style.display = 'none';
    document.getElementById('modal-part-count').textContent = '—';
    
    // Show Modal using Bootstrap
    const modalEl = document.getElementById('modal-DetalleParticipantesMes');
    let modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalEl);
    }
    modalInstance.show();
    
    // Fetch data from API
    const url = `../controlador/Reporte/api_reportes.php?accion=participantes_por_mes_detalle&mes=${mesNum}&anio=<?php echo $anioActual; ?>`;
    fetch(url, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(data => {
            document.getElementById('modal-part-loading').style.display = 'none';
            if (data.error || !Array.isArray(data) || data.length === 0) {
                document.getElementById('modal-part-sin-datos').style.display = 'block';
                document.getElementById('modal-part-count').textContent = '0 participantes';
                return;
            }
            
            const tbody = document.getElementById('modal-part-tbody');
            tbody.innerHTML = '';
            
            data.forEach(p => {
                const tr = document.createElement('tr');
                tr.className = 'part-row-link modal-part-row';
                tr.dataset.cedula = p.cedula;
                tr.style.cursor = 'pointer';
                tr.style.backgroundColor = '#ffffff';
                
                // Normalización de estados
                const rawEstado = p.estado;
                const estadoLabel = ['Inactivo','En sistema'].includes(rawEstado) ? 'Sin curso activo' : rawEstado;
                
                // Estilos para las pastillas de estado con mejor contraste y diseño
                let pillClass = '';
                let dotColor = '';
                if (estadoLabel === 'En formación' || estadoLabel === 'En formacion') {
                    pillClass = 'background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;';
                    dotColor = '#10b981';
                } else if (estadoLabel === 'Disponible') {
                    pillClass = 'background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a;';
                    dotColor = '#f59e0b';
                } else if (estadoLabel === 'Asignado') {
                    pillClass = 'background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;';
                    dotColor = '#3b82f6';
                } else if (estadoLabel === 'Diferido') {
                    pillClass = 'background-color: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8;';
                    dotColor = '#ec4899';
                } else {
                    pillClass = 'background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1;';
                    dotColor = '#64748b';
                }
                
                // Generador de Avatares
                const fullName = p.primer_nombre + ' ' + p.primer_apellido;
                const initials = getInitials(fullName);
                const avatarTheme = getAvatarColor(fullName);
                
                const avatarHTML = `
                    <div class="d-flex align-items-center gap-2">
                      <div style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 700; background-color: ${avatarTheme.bg}; color: ${avatarTheme.text}; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                        ${initials}
                      </div>
                      <span class="fw-bold text-dark" style="font-size: .83rem;">${escHtml(fullName)}</span>
                    </div>
                `;
                
                // Badges de género con iconos SVG
                let genderBadge = '';
                if (p.genero === 'Masculino') {
                    genderBadge = `<span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:12px; font-size:.7rem; font-weight:600; background-color:#e0f2fe; color:#0369a1;"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M9.5 2a.5.5 0 0 1 0-1h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V3.707L9.854 6.854a.5.5 0 0 1-.708-.708L12.293 3H9.5zM6 6a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg> Masc</span>`;
                } else {
                    genderBadge = `<span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:12px; font-size:.7rem; font-weight:600; background-color:#fce7f3; color:#be185d;"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm-4 3a4 4 0 1 1 8 0 4 4 0 0 1-8 0zM8 8a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 8 8z"/></svg> Fem</span>`;
                }
                
                const formattedFecha = p.fecha_registro ? p.fecha_registro.split(' ')[0].split('-').reverse().join('/') : '—';
                
                // Formateo de Cédula (V-XXXXXXXX)
                const cedStr = p.cedula ? p.cedula.toString() : '';
                let cedulaFormat = escHtml(cedStr);
                if (cedStr && !cedStr.includes('-')) {
                    cedulaFormat = 'V-' + parseInt(cedStr).toLocaleString('de-DE');
                }
                
                tr.innerHTML = `
                    <td>${avatarHTML}</td>
                    <td class="text-secondary fw-semibold" style="font-family: var(--bs-font-monospace, monospace); font-size: .8rem;">${cedulaFormat}</td>
                    <td>
                      <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:12px; font-size:.7rem; font-weight:600; ${pillClass}">
                        <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background-color:${dotColor};"></span>
                        ${escHtml(estadoLabel)}
                      </span>
                    </td>
                    <td>${genderBadge}</td>
                    <td class="text-secondary fw-semibold" style="font-size: .8rem;">
                      <span style="display:inline-flex; align-items:center; gap:4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.14 3.57a.5.5 0 0 0 0 .893l7.14 3.56a.5.5 0 0 0 .422 0l7.14-3.56a.5.5 0 0 0 0-.893L8.211 2.047z"/><path d="M3.21 9.77a.5.5 0 0 1 .582-.24l4.208 1.402a.5.5 0 0 0 .317 0l4.208-1.402a.5.5 0 0 1 .582.24l.322.643a.5.5 0 0 1-.211.666l-5 2.5a.5.5 0 0 1-.482 0l-5-2.5a.5.5 0 0 1-.211-.666l.322-.643z"/></svg>
                        ${escHtml(p.grado_institucion ?? '—')}
                      </span>
                    </td>
                    <td class="text-center fw-bold text-dark" style="font-size: .8rem;">${p.edad ? p.edad + ' años' : '—'}</td>
                    <td class="text-secondary" style="font-size: .8rem;">
                      <span style="display:inline-flex; align-items:center; gap:4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                        ${formattedFecha}
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
            
            document.getElementById('modal-part-contenido').style.display = 'block';
            document.getElementById('modal-part-count').textContent = `${data.length} participante${data.length !== 1 ? 's' : ''}`;
        })
        .catch(err => {
            console.error('Error fetching monthly participants:', err);
            document.getElementById('modal-part-loading').style.display = 'none';
            document.getElementById('modal-part-sin-datos').style.display = 'block';
            document.getElementById('modal-part-count').textContent = 'Error';
        });
}

function escHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
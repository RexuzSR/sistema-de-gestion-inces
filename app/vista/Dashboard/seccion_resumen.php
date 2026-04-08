<!-- ════════════════════════════════════════════════════════════
     SECCIÓN 1 — RESUMEN GENERAL  (versión con cards seleccionables)
     Vista: app/vista/Dashboard/seccion_resumen.php
════════════════════════════════════════════════════════════ -->
<div class="dashboard-section active" id="section-resumen">

    <!-- ── Barra de cumplimiento de metas ── -->
    <?php
    $metaPct   = (float)($kMetas['promedio_pct'] ?? 0);
    $metaColor = $s_meta === 'verde' ? '#28c76f' : ($s_meta === 'amarillo' ? '#f59e0b' : '#ef4444');
    $metaBg    = $s_meta === 'verde' ? '#d4f4e3'  : ($s_meta === 'amarillo' ? '#fef3c7'  : '#fee2e2');
    ?>
    <div class="barra-metas-banner mt-4" style="border-left:4px solid <?php echo $metaColor; ?>;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:8px;background:<?php echo $metaBg; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="<?php echo $metaColor; ?>" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </div>
                <div>
                    <p style="margin:0;font-size:.72rem;color:#64748b;line-height:1.2;">Cumplimiento promedio de metas activas · año <?php echo $anioActual; ?></p>
                    <p style="margin:0;font-size:.82rem;font-weight:600;color:#1e293b;line-height:1.3;">
                        <?php echo (int)($kMetas['cumplidas'] ?? 0); ?> de <?php echo (int)($kMetas['total_metas'] ?? 0); ?> metas cumplidas
                    </p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <?php foreach ([['Crítico','#ef4444'],['Atención','#f59e0b'],['Óptimo','#28c76f']] as $seg): ?>
                <span style="display:flex;align-items:center;gap:5px;font-size:.71rem;color:#64748b;">
                    <span style="width:9px;height:9px;border-radius:2px;background:<?php echo $seg[1]; ?>;display:inline-block;"></span>
                    <?php echo $seg[0]; ?>
                </span>
                <?php endforeach; ?>
                <span style="font-size:.78rem;font-weight:700;color:<?php echo $metaColor; ?>;background:<?php echo $metaBg; ?>;border-radius:20px;padding:3px 11px;">
                    <?php echo $metaPct; ?>% · <?php echo semL($s_meta); ?>
                </span>
            </div>
        </div>
        <div style="position:relative;height:14px;border-radius:7px;background:#f1f5f9;overflow:visible;">
            <div style="position:absolute;top:0;left:0;width:<?php echo min($metaPct, 100); ?>%;height:14px;border-radius:7px;background:<?php echo $metaColor; ?>;transition:width .9s ease;box-shadow:0 2px 6px <?php echo $metaColor; ?>55;"></div>
            <div style="position:absolute;top:-3px;left:80%;transform:translateX(-50%);width:2px;height:20px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
            <div style="position:absolute;top:-3px;left:95%;transform:translateX(-50%);width:2px;height:20px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
        </div>
        <div style="position:relative;height:18px;margin-top:3px;">
            <span style="position:absolute;left:80%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;white-space:nowrap;">80%</span>
            <span style="position:absolute;left:95%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;white-space:nowrap;">95%</span>
            <span style="position:absolute;right:0;font-size:.65rem;color:#94a3b8;">100% meta</span>
        </div>
    </div>

    <!-- ── Tarjetas KPI de resumen ── -->
    <div class="card-group-container bg-light p-3 rounded-3 mt-4">
        <div class="row g-3">
            <?php
            $resumenCards = [
                ['id'=>'participantes','seccion'=>'participantes','label'=>'Participantes Totales','valor'=>$total_participantes,'iconBg'=>'#e0f9fd','iconColor'=>'#00c8e0','chartId'=>'chartPartMes','svg'=>'<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>'],
                ['id'=>'cursos','seccion'=>'cursos','label'=>'Total Cursos','valor'=>$total_cursos,'iconBg'=>'#e2fce8','iconColor'=>'#28c76f','chartId'=>'chartCursosMes','svg'=>'<path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 1 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>'],
                ['id'=>'docentes','seccion'=>'docentes','label'=>'Total Docentes','valor'=>$total_docentes,'iconBg'=>'#ede8ff','iconColor'=>'#7367f0','chartId'=>'chartDocentesMes','svg'=>'<path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>'],
            ];
            foreach ($resumenCards as $c): ?>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-basic shadow-sm kpi-card-nav" data-nav-section="<?php echo $c['seccion']; ?>"
                     title="Ir a <?php echo $c['label']; ?>">
                    <div class="kpi-basic-body">
                        <div>
                            <p class="kpi-basic-valor"><?php echo $c['valor']; ?></p>
                            <span class="kpi-basic-label"><?php echo $c['label']; ?></span>
                        </div>
                        <div class="kpi-basic-icon-wrap" style="background:<?php echo $c['iconBg']; ?>;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="<?php echo $c['iconColor']; ?>" viewBox="0 0 16 16"><?php echo $c['svg']; ?></svg>
                        </div>
                    </div>
                    <div class="kpi-mes-chart-wrap"><canvas id="<?php echo $c['chartId']; ?>"></canvas></div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Tarjeta Metas -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-basic shadow-sm kpi-card-nav" data-nav-section="metas"
                     title="Ir a Metas">
                    <div class="kpi-basic-body">
                        <div>
                            <p class="kpi-basic-valor"><?php echo (int)($kMetas['total_metas'] ?? 0); ?></p>
                            <span class="kpi-basic-label">Metas Activas</span>
                        </div>
                        <div class="kpi-basic-icon-wrap" style="background:#fee2e2;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#ef4444" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                            </svg>
                        </div>
                    </div>
                    <div class="kpi-metas-progress-wrap">
                        <div class="kpi-metas-progress-label">
                            <span>Avance promedio</span>
                            <span class="pct-val semaforo-text-<?php echo $s_meta; ?>"><?php echo $kMetas['promedio_pct'] ?? 0; ?>%</span>
                        </div>
                        <div class="kpi-metas-barra-fondo">
                            <div class="kpi-metas-barra" style="width:<?php echo min((float)($kMetas['promedio_pct'] ?? 0), 100); ?>%;"></div>
                        </div>
                        <div class="kpi-metas-progress-stats">
                            <span class="cumplidas-val">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#28c76f" viewBox="0 0 16 16" style="margin-right:.2rem;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                                <?php echo (int)($kMetas['cumplidas'] ?? 0); ?> cumplidas
                            </span>
                            <span class="semaforo-badge semaforo-<?php echo $s_meta; ?>"><?php echo semL($s_meta); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         INDICADORES SEMÁFORO — SELECCIONABLES CON PANEL EXPANDIBLE
    ══════════════════════════════════════════════════════════════════ -->
    <div class="card-group-container bg-light p-3 rounded-3 mt-3">

        <?php
        /* ── Helper: botón de tab ── */
        function mkTabBtn(string $panel, string $tab, string $label, bool $active, string $accent): string {
            $c = $active ? $accent : '#64748b';
            $b = $active ? $accent : 'transparent';
            $f = $active ? '700'   : '600';
            return "<button class=\"kpi-mini-tab-btn\" data-panel=\"{$panel}\" data-tab=\"{$tab}\"
                style=\"padding:7px 16px;border:none;background:transparent;font-size:.76rem;font-weight:{$f};
                color:{$c};border-bottom:2px solid {$b};margin-bottom:-2px;cursor:pointer;
                border-radius:4px 4px 0 0;transition:all .15s;\">{$label}</button>";
        }
        /* ── Helper: <th> ── */
        function mkTh(string $l, string $a = 'left'): string {
            return "<th style=\"padding:8px 10px;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;
                    color:#64748b;font-weight:700;border-bottom:2px solid #e9ecef;text-align:{$a};\">{$l}</th>";
        }
        /* ── Helper: badge de género ── */
        function mkGenero(string $g): string {
            $bg = $g === 'Femenino' ? '#fce7f3' : '#dbeafe';
            $fg = $g === 'Femenino' ? '#be185d' : '#1d4ed8';
            return "<span style=\"display:inline-block;padding:2px 7px;border-radius:10px;font-size:.68rem;
                    font-weight:600;background:{$bg};color:{$fg};\">" . htmlspecialchars($g) . "</span>";
        }
        /* ── Helper: badge genérico ── */
        function mkBadge(string $text, string $bg, string $fg): string {
            return "<span style=\"display:inline-block;padding:2px 8px;border-radius:10px;font-size:.68rem;
                    font-weight:600;background:{$bg};color:{$fg};\">{$text}</span>";
        }
        /* ── Helper: tooltip de navegación ── */
        function mkNavTip(string $destino): string {
            return "<span style=\"margin-left:5px;font-size:.65rem;color:#94a3b8;font-style:italic;\">
                        → Ver en {$destino}
                    </span>";
        }
        /* ── Estilos de TR navegable compartidos ── */
        $trNavStyle = 'cursor:pointer;transition:background .14s;';
        $trNavHover = 'onmouseover="this.style.background=\'#f0f9ff\'" onmouseout="this.style.background=\'\'"';
        /* Mismo hover en rojo para diferidos/pendientes */
        $trNavHoverWarm = 'onmouseover="this.style.background=\'#fff8f0\'" onmouseout="this.style.background=\'\'"';
        ?>

        <div class="row g-3" id="mini-kpi-row">

            <!-- ══ CARD 1: Utilización de cupos ══ -->
            <div class="col-6 col-xl-3">
                <div class="panel-mini-kpi kpi-seleccionable semaforo-borde-<?php echo $s_util; ?>"
                     data-kpi="utilizacion"
                     style="color:<?php echo $s_util==='verde'?'#28c76f':($s_util==='amarillo'?'#f59e0b':'#ef4444'); ?>;">
                    <div class="kpi-selector-dot"></div>
                    <div class="panel-mini-kpi-header">
                        <span class="panel-mini-kpi-label">Utilización de cupos</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_util; ?>"><?php echo semL($s_util); ?></span>
                    </div>
                    <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_util; ?>"><?php echo $kUtilizacion['pct']; ?>%</div>
                    <div class="panel-mini-kpi-sub"><?php echo $kUtilizacion['inscritos']; ?> inscritos de <?php echo $kUtilizacion['cupos']; ?> cupos</div>
                    <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_util; ?>" style="width:<?php echo min($kUtilizacion['pct'],100); ?>%"></div></div>
                    <div style="margin-top:.55rem;display:flex;justify-content:flex-end;">
                        <span class="kpi-adv-toggle-badge" id="badge-utilizacion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                            Ver detalle
                        </span>
                    </div>
                </div>
            </div>

            <!-- ══ CARD 2: En formación ══ -->
            <div class="col-6 col-xl-3">
                <div class="panel-mini-kpi kpi-seleccionable semaforo-borde-<?php echo $s_form; ?>"
                     data-kpi="formacion"
                     style="color:<?php echo $s_form==='verde'?'#28c76f':($s_form==='amarillo'?'#f59e0b':'#ef4444'); ?>;">
                    <div class="kpi-selector-dot"></div>
                    <div class="panel-mini-kpi-header">
                        <span class="panel-mini-kpi-label">En formación</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_form; ?>"><?php echo semL($s_form); ?></span>
                    </div>
                    <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_form; ?>"><?php echo $kPctForm['pct']; ?>%</div>
                    <div class="panel-mini-kpi-sub"><?php echo $kPctForm['en_formacion']; ?> activos de <?php echo $kPctForm['total']; ?></div>
                    <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_form; ?>" style="width:<?php echo min($kPctForm['pct'],100); ?>%"></div></div>
                    <div style="margin-top:.55rem;display:flex;justify-content:flex-end;">
                        <span class="kpi-adv-toggle-badge" id="badge-formacion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                            Ver detalle
                        </span>
                    </div>
                </div>
            </div>

            <!-- ══ CARD 3: Índice de asignación ══ -->
            <div class="col-6 col-xl-3">
                <div class="panel-mini-kpi kpi-seleccionable semaforo-borde-<?php echo $s_asig; ?>"
                     data-kpi="asignacion"
                     style="color:<?php echo $s_asig==='verde'?'#28c76f':($s_asig==='amarillo'?'#f59e0b':'#ef4444'); ?>;">
                    <div class="kpi-selector-dot"></div>
                    <div class="panel-mini-kpi-header">
                        <span class="panel-mini-kpi-label">Índice de asignación</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_asig; ?>"><?php echo semL($s_asig); ?></span>
                    </div>
                    <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_asig; ?>"><?php echo $kAsignacion['pct']; ?>%</div>
                    <div class="panel-mini-kpi-sub"><?php echo $kAsignacion['activos']; ?> activos de <?php echo $kAsignacion['total']; ?></div>
                    <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_asig; ?>" style="width:<?php echo min($kAsignacion['pct'],100); ?>%"></div></div>
                    <div style="margin-top:.55rem;display:flex;justify-content:flex-end;">
                        <span class="kpi-adv-toggle-badge" id="badge-asignacion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                            Ver detalle
                        </span>
                    </div>
                </div>
            </div>

            <!-- ══ CARD 4: Programas ejecutados ══ -->
            <div class="col-6 col-xl-3">
                <div class="panel-mini-kpi kpi-seleccionable semaforo-borde-<?php echo $s_ejecucion; ?>"
                     data-kpi="ejecucion"
                     style="color:<?php echo $s_ejecucion==='verde'?'#28c76f':($s_ejecucion==='amarillo'?'#f59e0b':'#ef4444'); ?>;">
                    <div class="kpi-selector-dot"></div>
                    <div class="panel-mini-kpi-header">
                        <span class="panel-mini-kpi-label">Programas ejecutados</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_ejecucion; ?>"><?php echo semL($s_ejecucion); ?></span>
                    </div>
                    <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_ejecucion; ?>"><?php echo $kEjecucion['pct']; ?>%</div>
                    <div class="panel-mini-kpi-sub"><?php echo $kEjecucion['ejecutados']; ?> de <?php echo $kEjecucion['planificados']; ?> planificados · T<?php echo $trimestreActual; ?></div>
                    <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_ejecucion; ?>" style="width:<?php echo min($kEjecucion['pct'],100); ?>%"></div></div>
                    <div style="margin-top:.55rem;display:flex;justify-content:flex-end;">
                        <span class="kpi-adv-toggle-badge" id="badge-ejecucion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
                            Ver detalle
                        </span>
                    </div>
                </div>
            </div>

        </div><!-- /row mini-kpi -->

        <!-- ── Hint de navegación (se inyecta en footer de cada tabla) ── -->
        <?php
        $navHintPart  = '<div style="padding:6px 10px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                            Haz clic en una fila para abrir el participante
                         </div>';
        $navHintCurso = '<div style="padding:6px 10px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                            Haz clic en una fila para abrir el curso
                         </div>';
        ?>

        <!-- ══════════════════════════════════════════════════════════════
             PANEL 1 — UTILIZACIÓN DE CUPOS
             Tab "Asignados"    → participantes inscritos + su curso  → participante.php
             Tab "Cupos libres" → cursos con cupos disponibles        → cursos.php
        ══════════════════════════════════════════════════════════════════ -->
        <div class="kpi-mini-panel" id="panel-utilizacion" style="display:none;">
            <div class="kpi-panel-inner mt-3" style="border-top-color:#0ea5e9;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#0ea5e9" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                        Utilización de cupos
                    </h6>
                    <button class="kpi-panel-close-btn" data-close-panel="utilizacion" title="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                    </button>
                </div>
                <div class="row g-3 align-items-start">
                    <!-- Donut -->
                    <div class="col-lg-4">
                        <p class="kpi-chart-label mb-2">Distribución de cupos</p>
                        <div style="position:relative;height:220px;"><canvas id="chartUtilizacionDonut"></canvas></div>
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <div style="flex:1;background:#e0f9fd;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#0ea5e9;"><?php echo $kUtilizacion['inscritos']; ?></div>
                                <div style="font-size:.68rem;color:#0369a1;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Asignados</div>
                            </div>
                            <div style="flex:1;background:#fee2e2;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#ef4444;"><?php echo max(0,$kUtilizacion['cupos']-$kUtilizacion['inscritos']); ?></div>
                                <div style="font-size:.68rem;color:#b91c1c;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Cupos libres</div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabla tabs -->
                    <div class="col-lg-8">
                        <?php
                        $cnt_asig  = count($cardFormacion) + count($cardAsignados);
                        $cnt_libre = count(array_filter($cursosDetalle, fn($c) => (int)$c['cupos_disp'] > 0));
                        ?>
                        <div style="display:flex;gap:0;margin-bottom:12px;border-bottom:2px solid #e9ecef;">
                            <?php echo mkTabBtn('utilizacion','asignados','Asignados ('.$cnt_asig.')', true, '#0ea5e9'); ?>
                            <?php echo mkTabBtn('utilizacion','libres','Cupos libres ('.$cnt_libre.')', false, '#0ea5e9'); ?>
                        </div>

                        <!-- ▶ Tab Asignados → participante.php?buscar=cedula -->
                        <div class="kpi-mini-tab-panel" id="tab-utilizacion-asignados">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#f0f9ff;z-index:1;"><tr>
                                            <?php echo mkTh('Participante').mkTh('Cédula').mkTh('Género').mkTh('Curso inscrito'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cardFormacion as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                                <td style="padding:7px 10px;font-size:.73rem;color:#334155;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($p['cursos']??'—'); ?>"><?php echo htmlspecialchars($p['cursos']??'—'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php foreach ($cardAsignados as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                                <td style="padding:7px 10px;font-size:.73rem;color:#94a3b8;font-style:italic;">Asignado · pendiente de inicio</td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cardFormacion) && empty($cardAsignados)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin participantes asignados actualmente</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintPart; ?>
                            </div>
                        </div>

                        <!-- ▶ Tab Cupos libres → cursos.php?buscar=nombre_curso -->
                        <div class="kpi-mini-tab-panel" id="tab-utilizacion-libres" style="display:none;">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#fff5f5;z-index:1;"><tr>
                                            <?php echo mkTh('Curso').mkTh('Cupos libres','center').mkTh('Capacidad total','center').mkTh('% libre','right'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php
                                            $libres = array_filter($cursosDetalle, fn($c) => (int)$c['cupos_disp'] > 0);
                                            usort($libres, fn($a,$b) => (int)$b['cupos_disp'] - (int)$a['cupos_disp']);
                                            foreach ($libres as $cd):
                                                $pL = $cd['max_participantes'] > 0 ? round($cd['cupos_disp']/$cd['max_participantes']*100,1) : 0;
                                            ?>
                                            <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($cd['nombre_curso']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHoverWarm; ?> title="Ver curso">
                                                <td style="padding:7px 10px;font-weight:600;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($cd['nombre_curso']); ?>"><?php echo htmlspecialchars($cd['nombre_curso']); ?></td>
                                                <td style="padding:7px 10px;text-align:center;font-size:1rem;font-weight:800;color:#ef4444;"><?php echo $cd['cupos_disp']; ?></td>
                                                <td style="padding:7px 10px;text-align:center;color:#64748b;"><?php echo $cd['max_participantes']; ?></td>
                                                <td style="padding:7px 10px;text-align:right;font-weight:700;color:#ef4444;"><?php echo $pL; ?>%</td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($libres)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Todos los cupos están ocupados ✓</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintCurso; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /panel-utilizacion -->


        <!-- ══════════════════════════════════════════════════════════════
             PANEL 2 — EN FORMACIÓN
             Tab "En formación" → participante.php?buscar=cedula
             Tab "Disponibles"  → participante.php?buscar=cedula
        ══════════════════════════════════════════════════════════════════ -->
        <div class="kpi-mini-panel" id="panel-formacion" style="display:none;">
            <?php $fC = $s_form==='verde'?'#28c76f':($s_form==='amarillo'?'#f59e0b':'#ef4444'); ?>
            <div class="kpi-panel-inner mt-3" style="border-top-color:<?php echo $fC; ?>;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="<?php echo $fC; ?>" viewBox="0 0 16 16"><path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/></svg>
                        Participantes en formación
                    </h6>
                    <button class="kpi-panel-close-btn" data-close-panel="formacion" title="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                    </button>
                </div>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <p class="kpi-chart-label mb-2">Distribución de estados</p>
                        <div style="position:relative;height:220px;"><canvas id="chartFormacionDonut"></canvas></div>
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <div style="flex:1;background:#e2fce8;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#28c76f;"><?php echo $kPctForm['en_formacion']; ?></div>
                                <div style="font-size:.68rem;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">En formación</div>
                            </div>
                            <div style="flex:1;background:#fef9ec;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#d97706;"><?php echo $kDisponibles['disponibles']; ?></div>
                                <div style="font-size:.68rem;color:#92400e;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Disponibles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div style="display:flex;gap:0;margin-bottom:12px;border-bottom:2px solid #e9ecef;">
                            <?php echo mkTabBtn('formacion','activos','En formación ('.count($cardFormacion).')', true, '#28c76f'); ?>
                            <?php echo mkTabBtn('formacion','disponibles','Disponibles ('.count($cardDisponibles).')', false, '#28c76f'); ?>
                        </div>

                        <!-- ▶ Tab En formación → participante.php -->
                        <div class="kpi-mini-tab-panel" id="tab-formacion-activos">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#f0fdf4;z-index:1;"><tr>
                                            <?php echo mkTh('Participante').mkTh('Cédula').mkTh('Género').mkTh('Curso activo'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cardFormacion as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                                <td style="padding:7px 10px;font-size:.73rem;color:#334155;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($p['cursos']??'—'); ?>"><?php echo htmlspecialchars($p['cursos']??'—'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cardFormacion)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin participantes en formación actualmente</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintPart; ?>
                            </div>
                        </div>

                        <!-- ▶ Tab Disponibles → participante.php -->
                        <div class="kpi-mini-tab-panel" id="tab-formacion-disponibles" style="display:none;">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#fffbeb;z-index:1;"><tr>
                                            <?php echo mkTh('Participante').mkTh('Cédula').mkTh('Género').mkTh('Grado institución'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cardDisponibles as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                                <td style="padding:7px 10px;font-size:.73rem;color:#64748b;"><?php echo htmlspecialchars($p['grado_institucion']??'—'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cardDisponibles)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">No hay participantes disponibles sin asignación</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintPart; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /panel-formacion -->


        <!-- ══════════════════════════════════════════════════════════════
             PANEL 3 — ÍNDICE DE ASIGNACIÓN
             Tab "Asignados"   → participante.php?buscar=cedula
             Tab "Sin asignar" → participante.php?buscar=cedula
        ══════════════════════════════════════════════════════════════════ -->
        <div class="kpi-mini-panel" id="panel-asignacion" style="display:none;">
            <?php $aC = $s_asig==='verde'?'#28c76f':($s_asig==='amarillo'?'#f59e0b':'#ef4444'); ?>
            <div class="kpi-panel-inner mt-3" style="border-top-color:<?php echo $aC; ?>;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="<?php echo $aC; ?>" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                        Índice de asignación
                    </h6>
                    <button class="kpi-panel-close-btn" data-close-panel="asignacion" title="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                    </button>
                </div>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <p class="kpi-chart-label mb-2">Desglose de participantes</p>
                        <div style="position:relative;height:220px;"><canvas id="chartAsignacionDonut"></canvas></div>
                        <div style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap;">
                            <div style="flex:1;min-width:72px;background:#ede8ff;border-radius:8px;padding:8px 10px;text-align:center;">
                                <div style="font-size:1.2rem;font-weight:800;color:#7367f0;"><?php echo $kAsignacion['en_formacion']; ?></div>
                                <div style="font-size:.63rem;color:#5b52d6;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">En formación</div>
                            </div>
                            <div style="flex:1;min-width:72px;background:#e0f9fd;border-radius:8px;padding:8px 10px;text-align:center;">
                                <div style="font-size:1.2rem;font-weight:800;color:#0ea5e9;"><?php echo $kAsignacion['asignados']; ?></div>
                                <div style="font-size:.63rem;color:#0369a1;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Asignados</div>
                            </div>
                            <div style="flex:1;min-width:72px;background:#f1f5f9;border-radius:8px;padding:8px 10px;text-align:center;">
                                <div style="font-size:1.2rem;font-weight:800;color:#64748b;"><?php echo $kAsignacion['total']-$kAsignacion['activos']; ?></div>
                                <div style="font-size:.63rem;color:#475569;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Sin asignar</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <?php
                        $cntAsig   = count($cardFormacion) + count($cardAsignados);
                        $cntNoAsig = count($cardDisponibles) + count($cardInactivos);
                        ?>
                        <div style="display:flex;gap:0;margin-bottom:12px;border-bottom:2px solid #e9ecef;">
                            <?php echo mkTabBtn('asignacion','asignados','Asignados ('.$cntAsig.')', true, '#7367f0'); ?>
                            <?php echo mkTabBtn('asignacion','noasignados','Sin asignar ('.$cntNoAsig.')', false, '#7367f0'); ?>
                        </div>

                        <!-- ▶ Tab Asignados → participante.php -->
                        <div class="kpi-mini-tab-panel" id="tab-asignacion-asignados">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#f5f3ff;z-index:1;"><tr>
                                            <?php echo mkTh('Participante').mkTh('Cédula').mkTh('Estado').mkTh('Género'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cardFormacion as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge('En formación','#e2fce8','#15803d'); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php foreach ($cardAsignados as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge('Asignado','#ede8ff','#5b52d6'); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cardFormacion) && empty($cardAsignados)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin participantes asignados</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintPart; ?>
                            </div>
                        </div>

                        <!-- ▶ Tab Sin asignar → participante.php -->
                        <div class="kpi-mini-tab-panel" id="tab-asignacion-noasignados" style="display:none;">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#f8fafc;z-index:1;"><tr>
                                            <?php echo mkTh('Participante').mkTh('Cédula').mkTh('Estado').mkTh('Género'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cardDisponibles as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge('Disponible','#fef9ec','#92400e'); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php foreach ($cardInactivos as $p): ?>
                                            <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver participante">
                                                <td style="padding:7px 10px;font-weight:600;"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td style="padding:7px 10px;color:#64748b;"><?php echo htmlspecialchars($p['cedula']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge(htmlspecialchars($p['estado']),'#f1f5f9','#475569'); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkGenero($p['genero']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cardDisponibles) && empty($cardInactivos)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin participantes sin asignar</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintPart; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /panel-asignacion -->


        <!-- ══════════════════════════════════════════════════════════════
             PANEL 4 — PROGRAMAS EJECUTADOS
             Tab "Ejecutados"          → cursos.php?buscar=nombre_curso
             Tab "Pendientes/Diferidos"→ cursos.php?buscar=nombre_curso
        ══════════════════════════════════════════════════════════════════ -->
        <div class="kpi-mini-panel" id="panel-ejecucion" style="display:none;">
            <?php $eC = $s_ejecucion==='verde'?'#28c76f':($s_ejecucion==='amarillo'?'#f59e0b':'#ef4444'); ?>
            <div class="kpi-panel-inner mt-3" style="border-top-color:<?php echo $eC; ?>;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="<?php echo $eC; ?>" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                        Programas ejecutados · T<?php echo $trimestreActual; ?> <?php echo $anioActual; ?>
                    </h6>
                    <button class="kpi-panel-close-btn" data-close-panel="ejecucion" title="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                    </button>
                </div>
                <?php
                $cursosIniciados = array_values(array_filter($cursosHistoricoDetalle ?? [], fn($c) => $c['tipo_ejecucion'] === 'iniciado'));
                $cursosPendDif   = array_values(array_filter($cursosHistoricoDetalle ?? [], fn($c) => in_array($c['tipo_ejecucion'], ['pendiente','diferido'])));
                ?>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <p class="kpi-chart-label mb-2">Ejecución del trimestre</p>
                        <div style="position:relative;height:220px;"><canvas id="chartEjecucionDonut"></canvas></div>
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <div style="flex:1;background:#e2fce8;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#28c76f;"><?php echo $kEjecucion['ejecutados']; ?></div>
                                <div style="font-size:.68rem;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Ejecutados</div>
                            </div>
                            <div style="flex:1;background:#fee2e2;border-radius:8px;padding:10px 12px;text-align:center;">
                                <div style="font-size:1.4rem;font-weight:800;color:#ef4444;"><?php echo max(0,$kEjecucion['planificados']-$kEjecucion['ejecutados']); ?></div>
                                <div style="font-size:.68rem;color:#b91c1c;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Pendientes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div style="display:flex;gap:0;margin-bottom:12px;border-bottom:2px solid #e9ecef;">
                            <?php echo mkTabBtn('ejecucion','ejecutados','Ejecutados ('.count($cursosIniciados).')', true, '#28c76f'); ?>
                            <?php echo mkTabBtn('ejecucion','pendientes','Pendientes / Diferidos ('.count($cursosPendDif).')', false, '#28c76f'); ?>
                        </div>

                        <!-- ▶ Tab Ejecutados → cursos.php -->
                        <div class="kpi-mini-tab-panel" id="tab-ejecucion-ejecutados">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#f0fdf4;z-index:1;"><tr>
                                            <?php echo mkTh('Curso').mkTh('Estatus').mkTh('Inscritos','center').mkTh('Modalidad'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cursosIniciados as $ci): ?>
                                            <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($ci['nombre_curso']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHover; ?> title="Ver curso">
                                                <td style="padding:7px 10px;font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($ci['nombre_curso']); ?>"><?php echo htmlspecialchars($ci['nombre_curso']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge(htmlspecialchars($ci['nombre_estatusCurso']),'#e2fce8','#15803d'); ?></td>
                                                <td style="padding:7px 10px;text-align:center;font-weight:700;color:#28c76f;"><?php echo $ci['num_inscritos']; ?></td>
                                                <td style="padding:7px 10px;font-size:.73rem;color:#64748b;"><?php echo htmlspecialchars($ci['nombre_modalidad']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cursosIniciados)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin cursos ejecutados en este período</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintCurso; ?>
                            </div>
                        </div>

                        <!-- ▶ Tab Pendientes/Diferidos → cursos.php -->
                        <div class="kpi-mini-tab-panel" id="tab-ejecucion-pendientes" style="display:none;">
                            <div style="border-radius:8px;border:1px solid #e9ecef;overflow:hidden;">
                                <div style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-hover mb-0" style="font-size:.78rem;">
                                        <thead style="position:sticky;top:0;background:#fff5f5;z-index:1;"><tr>
                                            <?php echo mkTh('Curso').mkTh('Estatus').mkTh('Inscritos','center').mkTh('Tipo'); ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php foreach ($cursosPendDif as $cp):
                                                $isDif = $cp['tipo_ejecucion'] === 'diferido';
                                            ?>
                                            <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($cp['nombre_curso']); ?>"
                                                style="<?php echo $trNavStyle; ?>" <?php echo $trNavHoverWarm; ?> title="Ver curso">
                                                <td style="padding:7px 10px;font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($cp['nombre_curso']); ?>"><?php echo htmlspecialchars($cp['nombre_curso']); ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge(htmlspecialchars($cp['nombre_estatusCurso']), $isDif?'#fef3c7':'#f1f5f9', $isDif?'#92400e':'#475569'); ?></td>
                                                <td style="padding:7px 10px;text-align:center;color:#64748b;"><?php echo $cp['num_inscritos']; ?></td>
                                                <td style="padding:7px 10px;"><?php echo mkBadge($isDif?'Diferido':'Pendiente', $isDif?'#fee2e2':'#e0f9fd', $isDif?'#b91c1c':'#0369a1'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($cursosPendDif)): ?>
                                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Sin cursos pendientes o diferidos ✓</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $navHintCurso; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /panel-ejecucion -->

    </div><!-- /card-group-container mini-kpi -->

</div><!-- /section-resumen -->


<!-- ══════════════════════════════════════════════════════════════
     JS — Cards principales navegables hacia su sección
══════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.kpi-card-nav[data-nav-section]').forEach(function (card) {
        card.addEventListener('click', function () {
            var seccion = card.dataset.navSection;
            /* Reutiliza exactamente el mismo flujo que los botones del dashboard-nav */
            var navBtn = document.querySelector('.dashboard-nav-item[data-section="' + seccion + '"]');
            if (navBtn) {
                /* Flash visual en la card antes de navegar */
                card.style.transition = 'transform .12s ease, box-shadow .12s ease';
                card.style.transform  = 'scale(.97)';
                setTimeout(function () {
                    card.style.transform = '';
                    navBtn.click();
                    /* Scroll suave al nav para orientar al usuario */
                    var nav = document.querySelector('.dashboard-nav');
                    if (nav) nav.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 120);
            }
        });
    });
});
</script>

<!-- ══════════════════════════════════════════════════════════════
     DATOS PARA JS — gráficas donut de los 4 paneles
══════════════════════════════════════════════════════════════════ -->
<script>
window.RESUMEN_KPI = {
    utilizacion: {
        inscritos: <?php echo (int)$kUtilizacion['inscritos']; ?>,
        libres:    <?php echo max(0,(int)$kUtilizacion['cupos']-(int)$kUtilizacion['inscritos']); ?>
    },
    formacion: {
        enFormacion: <?php echo (int)$kPctForm['en_formacion']; ?>,
        disponibles: <?php echo (int)$kDisponibles['disponibles']; ?>,
        otros: <?php echo max(0,(int)$kPctForm['total']-(int)$kPctForm['en_formacion']-(int)$kDisponibles['disponibles']); ?>
    },
    asignacion: {
        enFormacion: <?php echo (int)$kAsignacion['en_formacion']; ?>,
        asignados:   <?php echo (int)$kAsignacion['asignados']; ?>,
        sinAsignar:  <?php echo max(0,(int)$kAsignacion['total']-(int)$kAsignacion['activos']); ?>
    },
    ejecucion: {
        ejecutados: <?php echo (int)$kEjecucion['ejecutados']; ?>,
        pendientes: <?php echo max(0,(int)$kEjecucion['planificados']-(int)$kEjecucion['ejecutados']); ?>
    }
};
</script>
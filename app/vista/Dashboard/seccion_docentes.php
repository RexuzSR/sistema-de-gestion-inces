<!-- ════════════════════════════════════════════════════════════
     SECCIÓN 4 — DOCENTES
     Vista: app/vista/Dashboard/seccion_docentes.php
════════════════════════════════════════════════════════════ -->
<div class="dashboard-section" id="section-docentes">

    <!-- ══════════════════════════════════════════════════════════════
         BLOQUE: Docentes registrados por mes — año actual
    ══════════════════════════════════════════════════════════════════ -->
    <div class="mt-4" id="docentes-mensual-bloque">
        <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem 1rem;box-shadow:0 2px 12px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.06);">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.1rem;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#ede8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="#7367f0" viewBox="0 0 16 16">
                            <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:.72rem;color:#64748b;line-height:1.2;font-weight:500;">Docentes registrados por mes &middot; <?php echo $anioActual; ?></p>
                        <p style="margin:0;font-size:.88rem;font-weight:700;color:#1e293b;line-height:1.3;">Actividad del plantel docente en el a&ntilde;o</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <?php
                    $dTotalAnio     = array_sum($docentesPorMes);
                    $dMesMax        = array_search(max($docentesPorMes), $docentesPorMes);
                    $dMesActualIdx  = (int)date('n') - 1;
                    $dMesesNombres  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                    $dMesesFull     = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    $dMesActualVal  = $docentesPorMes[$dMesActualIdx] ?? 0;
                    $dMesesConDatos = array_filter($docentesPorMes, fn($v) => $v > 0);
                    $dPromMensual   = count($dMesesConDatos) > 0 ? round(array_sum($docentesPorMes) / count($dMesesConDatos), 1) : 0;
                    ?>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#7367f0;line-height:1;"><?php echo $dTotalAnio; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Total a&ntilde;o</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#ec4899;line-height:1;"><?php echo $dMesActualVal; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;"><?php echo $dMesesNombres[$dMesActualIdx]; ?> actual</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#0ea5e9;line-height:1;"><?php echo $dPromMensual; ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Promedio/mes</div>
                    </div>
                    <div style="width:1px;height:32px;background:#e2e8f0;"></div>
                    <div style="text-align:center;">
                        <div style="font-size:1.35rem;font-weight:800;color:#f59e0b;line-height:1;"><?php echo max($docentesPorMes); ?></div>
                        <div style="font-size:.65rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Pico &middot; <?php echo $dMesesNombres[$dMesMax]; ?></div>
                    </div>
                </div>
            </div>
            <div style="position:relative;height:190px;width:100%;"><canvas id="chartDocentesMensual"></canvas></div>
            <div style="display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#7367f0;"></span>Mes con registros</span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#7367f033;"></span>Sin registros</span>
                <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;color:#64748b;"><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#ec4899;"></span>Mes actual (<?php echo $dMesesFull[$dMesActualIdx]; ?>)</span>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         PANEL PRINCIPAL: Análisis de Docentes
    ══════════════════════════════════════════════════════════════════ -->
    <div class="kpi-panel-inner mt-4" style="border-top-color:#7367f0;">
        <div class="kpi-panel-header">
            <h6 class="kpi-panel-titulo" style="color:#7367f0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg>
                An&aacute;lisis de Docentes
            </h6>
            <small class="text-muted">Distribuci&oacute;n del plantel docente activo</small>
        </div>

        <!-- GRÁFICAS DOCENTES -->
        <div class="chart-tabs-wrapper mt-3">
            <nav class="chart-tabs-nav" id="chart-nav-docentes">
                <button class="chart-tab-btn active" data-section="docentes" data-chart="tipo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                    Por Tipo
                </button>
                <button class="chart-tab-btn" data-section="docentes" data-chart="estado">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                    Por Estado
                </button>
                <button class="chart-tab-btn" data-section="docentes" data-chart="genero">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5"/></svg>
                    Por G&eacute;nero
                </button>
            </nav>
            <div class="chart-tabs-body">

                <!-- TAB: Por Tipo — filas navegables a docente.php -->
                <div class="chart-tab-panel active" id="chart-panel-docentes-tipo">
                    <p class="chart-panel-title">Distribuci&oacute;n por tipo de docente</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartDocTipo"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Nombre</th><th>C&eacute;dula</th><th>Tipo</th></tr></thead>
                                <tbody>
                                <?php
                                $palTipo      = ['#7367f0','#28c76f','#ff9f43','#ea5455'];
                                $tiposDocList = array_column($docTipo, 'tipo');
                                foreach ($listaDoc as $d):
                                    $tIdx   = array_search($d['tipo_docente'], $tiposDocList);
                                    $tColor = $tIdx !== false ? $palTipo[$tIdx % count($palTipo)] : '#6c757d';
                                ?>
                                    <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" style="cursor:pointer;" title="Ver docente">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'] . ' ' . $d['primer_apellido']); ?></td>
                                        <td><?php echo $d['cedula']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $tColor; ?>22;color:<?php echo $tColor; ?>;"><?php echo htmlspecialchars($d['tipo_docente']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                                Haz clic en una fila para abrir el docente
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Estado — filas navegables a docente.php -->
                <div class="chart-tab-panel" id="chart-panel-docentes-estado">
                    <p class="chart-panel-title">Distribuci&oacute;n por estado del docente</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartDocEstado"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Nombre</th><th>C&eacute;dula</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php
                                $colEstDoc = ['Disponible' => '#28c76f', 'En formaci&oacute;n' => '#f59e0b', 'Asignado' => '#0ea5e9', 'Inactivo' => '#ef4444'];
                                foreach ($listaDoc as $d):
                                    $deColor = $colEstDoc[$d['estado_docente']] ?? '#6c757d';
                                ?>
                                    <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" style="cursor:pointer;" title="Ver docente">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'] . ' ' . $d['primer_apellido']); ?></td>
                                        <td><?php echo $d['cedula']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $deColor; ?>22;color:<?php echo $deColor; ?>;"><?php echo htmlspecialchars($d['estado_docente']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                                Haz clic en una fila para abrir el docente
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Por Género — filas navegables a docente.php -->
                <div class="chart-tab-panel" id="chart-panel-docentes-genero">
                    <p class="chart-panel-title">Distribuci&oacute;n por g&eacute;nero</p>
                    <div class="chart-tab-duo-layout">
                        <div class="chart-tab-canvas-wrap"><canvas id="chartDocGenero"></canvas></div>
                        <div class="chart-tab-side-table">
                            <table class="table table-sm table-striped mb-0">
                                <thead><tr><th>Nombre</th><th>C&eacute;dula</th><th>G&eacute;nero</th><th class="text-center">Edad</th></tr></thead>
                                <tbody>
                                <?php foreach ($listaDoc as $d):
                                    $dgc = $d['genero'] === 'Masculino' ? '#0ea5e9' : '#ec4899';
                                ?>
                                    <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" style="cursor:pointer;" title="Ver docente">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'] . ' ' . $d['primer_apellido']); ?></td>
                                        <td><?php echo $d['cedula']; ?></td>
                                        <td><span class="badge-mini" style="background:<?php echo $dgc; ?>22;color:<?php echo $dgc; ?>;"><?php echo $d['genero']; ?></span></td>
                                        <td class="text-center"><?php echo isset($d['edad']) ? $d['edad'] . 'a' : '&mdash;'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div style="padding:6px 8px 2px;font-size:.68rem;color:#94a3b8;display:flex;align-items:center;gap:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>
                                Haz clic en una fila para abrir el docente
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /chart-tabs-wrapper docentes -->

        <!-- TABLA LISTADO DE DOCENTES — navegable a docente.php -->
        <div class="tables-container bg-light p-3 rounded-3 mt-3">
            <div class="panel-section">
                <div class="panel-section-header">
                    <span class="panel-section-dot" style="background:#7367f0;"></span>
                    <span class="panel-section-label">Listado de Docentes</span>
                    <small class="text-muted ms-auto">Primeros 10 &middot; Haz clic en una fila para ver el docente</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>C&eacute;dula</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">G&eacute;nero</th>
                                <th class="text-center">Edad</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listaDoc as $d):
                            $cd = ['Disponible' => 'success', 'En formaci&oacute;n' => 'warning', 'Asignado' => 'info', 'Inactivo' => 'danger'][$d['estado_docente']] ?? 'secondary';
                        ?>
                            <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" style="cursor:pointer;" title="Ver docente">
                                <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'] . ' ' . $d['primer_apellido']); ?></td>
                                <td><?php echo $d['cedula']; ?></td>
                                <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.71rem;"><?php echo $d['tipo_docente']; ?></span></td>
                                <td class="text-center"><span class="badge bg-<?php echo $cd; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $d['estado_docente']; ?></span></td>
                                <td class="text-center"><?php echo $d['genero']; ?></td>
                                <td class="text-center fw-semibold"><?php echo isset($d['edad']) ? $d['edad'] . ' a&ntilde;os' : '&mdash;'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($listaDoc)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Sin docentes registrados.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /tables-container -->

    </div><!-- /kpi-panel-inner -->
</div><!-- /section-docentes -->

<!-- ════════════════════════════════════════════════════════
     JS — Gráfica mensual de docentes
════════════════════════════════════════════════════════ -->
<script>
(function() {
    function build() {
        const ctx = document.getElementById('chartDocentesMensual');
        if (!ctx) return;
        if (typeof Chart === 'undefined') { setTimeout(build, 100); return; }

        const data       = <?php echo json_encode(array_values($docentesPorMes)); ?>;
        const mesActual  = <?php echo (int)date('n') - 1; ?>;
        const labels     = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const COLOR_BASE = '#7367f0';
        const COLOR_CURR = '#ec4899';
        const COLOR_NONE = '#7367f033';

        const bgColors     = data.map((v,i) => i===mesActual ? COLOR_CURR : (v>0 ? COLOR_BASE : COLOR_NONE));
        const borderColors = data.map((v,i) => i===mesActual ? COLOR_CURR : (v>0 ? COLOR_BASE : 'transparent'));
        const maxVal       = Math.max(...data, 1);

        const pluginVals = {
            id: 'valsD',
            afterDatasetsDraw(chart) {
                const {ctx: c, data: d} = chart;
                d.datasets[0].data.forEach((val, i) => {
                    if (!val) return;
                    const bar = chart.getDatasetMeta(0).data[i];
                    c.save();
                    c.fillStyle    = i === mesActual ? COLOR_CURR : '#475569';
                    c.font         = '600 11px Roboto, sans-serif';
                    c.textAlign    = 'center';
                    c.textBaseline = 'bottom';
                    c.fillText(val, bar.x, bar.y - 3);
                    c.restore();
                });
            }
        };

        if (window._chartDocentesMensual) window._chartDocentesMensual.destroy();
        window._chartDocentesMensual = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor:      bgColors,
                    borderColor:          borderColors,
                    borderWidth:          1.5,
                    borderRadius:         6,
                    borderSkipped:        false,
                    hoverBackgroundColor: data.map((_,i) => i===mesActual ? '#c4347a' : '#5b52d6')
                }]
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                layout:   { padding: { top: 20 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title:  items => labels[items[0].dataIndex],
                            label:  item  => {
                                const v = item.parsed.y;
                                return ` ${v} docente${v !== 1 ? 's' : ''} registrado${v !== 1 ? 's' : ''}`;
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
                            font:  { size: 11 },
                            color: (ctx) => ctx.index === mesActual ? COLOR_CURR : '#64748b'
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
                        }
                    }
                }
            },
            plugins: [pluginVals]
        });
    }
    build();
})();
</script>
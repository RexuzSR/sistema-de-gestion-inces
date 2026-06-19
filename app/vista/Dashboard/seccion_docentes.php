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
                onClick: function(evt, elements) {
                    if (!elements || elements.length === 0) return;
                    const idx = elements[0].index;
                    const val = data[idx];
                    if (val === 0) return;
                    const mesNum = idx + 1;
                    const labelsFull = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    mostrarDetalleDocentesMes(mesNum, labelsFull[idx]);
                },
                onHover: function(evt, elements) {
                    evt.native.target.style.cursor = (elements && elements.length > 0 && data[elements[0].index] > 0) ? 'pointer' : 'default';
                },
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
                        },
                        title: {
                            display: true,
                            text: 'Número de Docentes',
                            color: '#64748b',
                            font: { size: 11, weight: 600 }
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

<!-- Modal Detalle Docentes con Cursos Asignados por Mes -->
<style>
.modal-docente-row {
    transition: all 0.2s ease-in-out;
}
.modal-docente-row:hover td {
    background-color: #f8fafc !important;
}
.modal-docente-row:hover {
    transform: translateY(-1px);
}
.modal-docente-row:hover svg.chevron-arrow-doc {
    transform: translateX(4px);
    fill: #7367f0;
}
.modal-docente-table td {
    padding: 8px 12px !important;
    white-space: nowrap;
    font-size: 0.8rem;
    background-color: #ffffff;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.modal-docente-table td:first-child {
    border-left: 1px solid #e2e8f0;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}
.modal-docente-table td:last-child {
    border-right: 1px solid #e2e8f0;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}
.modal-docente-search-input {
    border-radius: 20px;
    padding: 10px 16px 10px 42px;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    font-size: 0.85rem;
    transition: all 0.2s ease-in-out;
}
.modal-docente-search-input:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15);
    outline: none;
}
.empty-state-icon-docente {
    background: #ede8ff;
    color: #7367f0;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
</style>

<div class="modal fade" id="modal-DetalleDocentesMes" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);">
      <!-- Header -->
      <div class="modal-header border-0" style="background:linear-gradient(135deg,#5b21b6 0%,#7367f0 50%,#a78bfa 100%);padding:1.25rem 1.5rem;">
        <div>
          <h5 class="modal-title text-white" style="font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 16 16">
              <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
              <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/>
            </svg>
            Docentes &mdash; <span id="modal-docente-mes-nombre">Mes</span>
          </h5>
          <p class="text-white-50 mb-0" style="font-size:.78rem;margin-top:2px;">Docentes con cursos asignados en este mes</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <!-- Search -->
      <div style="padding:1rem 1.5rem .5rem;background:#fff;">
        <div style="position:relative;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#94a3b8" viewBox="0 0 16 16" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
          </svg>
          <input type="text" class="form-control modal-docente-search-input" id="modal-docente-search" placeholder="Buscar por nombre, c&eacute;dula, tipo o estado...">
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
          <span style="font-size:.72rem;color:#94a3b8;">
            <span id="modal-docente-filtered-wrapper" style="display:none;">
              <span id="modal-docente-filtered-count">0</span> encontrados &middot;
            </span>
            Clic en una fila para ver el docente
          </span>
          <span id="modal-docente-count" style="font-size:.72rem;font-weight:600;color:#7367f0;">0 docentes</span>
        </div>
      </div>
      <!-- Body -->
      <div class="modal-body" style="padding:.5rem 1.5rem 1.25rem;max-height:420px;overflow-y:auto;">
        <!-- Loading -->
        <div id="modal-docente-loading" style="text-align:center;padding:3rem 0;">
          <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;border-width:.22rem;color:#7367f0 !important;">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p style="margin-top:12px;color:#64748b;font-size:.85rem;">Cargando docentes&hellip;</p>
        </div>
        <!-- Sin datos -->
        <div id="modal-docente-sin-datos" style="display:none;text-align:center;padding:2.5rem 0;">
          <div class="empty-state-icon-docente">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
              <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>
          </div>
          <p style="color:#64748b;font-weight:600;">No se encontraron docentes</p>
          <p style="color:#94a3b8;font-size:.82rem;">No hay docentes con cursos asignados en este mes.</p>
        </div>
        <!-- Contenido -->
        <div id="modal-docente-contenido" style="display:none;">
          <table class="modal-docente-table" style="width:100%;border-collapse:separate;border-spacing:0 6px;">
            <tbody id="modal-docente-tbody"></tbody>
          </table>
        </div>
      </div>
      <!-- Footer -->
      <div class="modal-footer border-0" style="padding:.75rem 1.5rem;background:#f8fafc;">
        <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="border-radius:10px;padding:6px 22px;font-size:.82rem;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('modal-docente-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.toLowerCase().trim();
                var rows  = document.querySelectorAll('#modal-docente-tbody tr');
                var visibles = 0;
                rows.forEach(function(row) {
                    var text = (row.getAttribute('data-nombre') || '').toLowerCase()
                             + ' ' + (row.getAttribute('data-cedula') || '').toLowerCase()
                             + ' ' + (row.getAttribute('data-tipo') || '').toLowerCase()
                             + ' ' + (row.getAttribute('data-estado') || '').toLowerCase();
                    if (!query || text.indexOf(query) !== -1) {
                        row.style.display = '';
                        visibles++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                var wrapper = document.getElementById('modal-docente-filtered-wrapper');
                if (query.length > 0) {
                    wrapper.style.display = 'inline-block';
                    document.getElementById('modal-docente-filtered-count').textContent = visibles;
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
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };
}

function mostrarDetalleDocentesMes(mesNum, mesLabel) {
    document.getElementById('modal-docente-mes-nombre').textContent = mesLabel;
    
    // Limpiar buscador
    document.getElementById('modal-docente-search').value = '';
    document.getElementById('modal-docente-filtered-wrapper').style.display = 'none';
    
    // Mostrar loading
    document.getElementById('modal-docente-loading').style.display = 'block';
    document.getElementById('modal-docente-sin-datos').style.display = 'none';
    document.getElementById('modal-docente-contenido').style.display = 'none';
    document.getElementById('modal-docente-count').textContent = 'Cargando...';
    
    var modal = new bootstrap.Modal(document.getElementById('modal-DetalleDocentesMes'));
    modal.show();
    
    var apiUrl = '../controlador/Reporte/api_reportes.php?accion=docentes_por_mes_detalle&anio=<?php echo $anioActual; ?>&mes=' + mesNum;
    
    fetch(apiUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('modal-docente-loading').style.display = 'none';
            
            if (!data || data.length === 0) {
                document.getElementById('modal-docente-sin-datos').style.display = 'block';
                document.getElementById('modal-docente-count').textContent = '0 docentes';
                return;
            }
            
            var tbody = document.getElementById('modal-docente-tbody');
            tbody.innerHTML = '';
            
            var colEstado = {
                'Disponible':     '#28c76f',
                'En formación':   '#f59e0b',
                'Asignado':       '#0ea5e9',
                'Inactivo':       '#ef4444'
            };
            
            var colTipo = {
                'Fijo':        '#7367f0',
                'Contratado':  '#0ea5e9',
                'PDL':         '#f59e0b',
                'Voluntario':  '#28c76f'
            };
            
            data.forEach(function(d) {
                var nombre = escHtml((d.primer_nombre || '') + ' ' + (d.segundo_nombre || '')).trim()
                           + ' ' + escHtml((d.primer_apellido || '') + ' ' + (d.segundo_apellido || '')).trim();
                nombre = nombre.replace(/\s+/g, ' ').trim();
                
                var fNac = '';
                if (d.fecha_nacimiento) {
                    var parts = d.fecha_nacimiento.split('-');
                    fNac = parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                
                var estadoColor = colEstado[d.estado_docente] || '#6c757d';
                var tipoColor   = colTipo[d.tipo_docente] || '#6c757d';
                var generoIcon  = d.genero === 'Masculino'
                    ? '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#0ea5e9" viewBox="0 0 16 16"><path d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5"/></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#ec4899" viewBox="0 0 16 16"><path d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5"/></svg>';
                var generoColor = d.genero === 'Masculino' ? '#0ea5e9' : '#ec4899';
                
                var tr = document.createElement('tr');
                tr.className = 'modal-docente-row docente-row-link';
                tr.style.cssText = 'cursor:pointer;';
                tr.setAttribute('data-cedula', d.cedula || '');
                tr.setAttribute('data-nombre', nombre);
                tr.setAttribute('data-tipo', d.tipo_docente || '');
                tr.setAttribute('data-estado', d.estado_docente || '');
                tr.title = 'Ver docente';
                
                tr.innerHTML = ''
                    + '<td style="min-width:220px;max-width:280px;">'
                    + '  <div style="display:flex;align-items:center;gap:10px;">'
                    + '    <div style="width:32px;height:32px;border-radius:8px;background:#ede8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
                    + '      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#7367f0" viewBox="0 0 16 16">'
                    + '        <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>'
                    + '      </svg>'
                    + '    </div>'
                    + '    <div style="min-width:0;">'
                    + '      <div style="font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;max-width:220px;" title="' + nombre + '">' + nombre + '</div>'
                    + '      <div style="font-size:.7rem;color:#94a3b8;">C.I. ' + escHtml(d.cedula) + '</div>'
                    + '    </div>'
                    + '  </div>'
                    + '</td>'
                    + '<td>'
                    + '  <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:' + tipoColor + '18;color:' + tipoColor + ';">'
                    +      escHtml(d.tipo_docente)
                    + '  </span>'
                    + '</td>'
                    + '<td>'
                    + '  <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:' + estadoColor + '18;color:' + estadoColor + ';">'
                    + '    <span style="width:6px;height:6px;border-radius:50%;background:' + estadoColor + ';"></span>'
                    +      escHtml(d.estado_docente)
                    + '  </span>'
                    + '</td>'
                    + '<td style="text-align:center;">'
                    + '  <span style="display:inline-flex;align-items:center;gap:4px;font-size:.78rem;color:' + generoColor + ';font-weight:500;">'
                    +      generoIcon + ' ' + escHtml(d.genero)
                    + '  </span>'
                    + '</td>'
                    + '<td style="text-align:center;">'
                    + '  <span style="font-weight:700;font-size:.85rem;color:#1e293b;">' + (d.edad || '—') + '</span>'
                    + '  <span style="font-size:.68rem;color:#94a3b8;"> años</span>'
                    + '</td>'
                    + '<td>'
                    + '  <span style="font-size:.78rem;color:#64748b;">'
                    + '    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#94a3b8" viewBox="0 0 16 16" style="margin-right:3px;">'
                    + '      <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>'
                    + '    </svg>'
                    +      fNac
                    + '  </span>'
                    + '</td>'
                    + '<td style="text-align:right;">'
                    + '  <svg class="chevron-arrow-doc" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#cbd5e1" viewBox="0 0 16 16" style="transition:all 0.2s ease;">'
                    + '    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>'
                    + '  </svg>'
                    + '</td>';
                
                tbody.appendChild(tr);
            });
            
            document.getElementById('modal-docente-contenido').style.display = 'block';
            document.getElementById('modal-docente-count').textContent = data.length + ' docente' + (data.length !== 1 ? 's' : '');
        })
        .catch(function(err) {
            console.error('Error fetching monthly docentes:', err);
            document.getElementById('modal-docente-loading').style.display = 'none';
            document.getElementById('modal-docente-sin-datos').style.display = 'block';
            document.getElementById('modal-docente-count').textContent = 'Error';
        });
}
</script>
// ========== REPORTE: CURSOS CON MÁS PARTICIPANTES ==========
// Conectado a: ../controlador/Reporte/api_reportes.php

const API_URL_CMP = '../controlador/Reporte/api_reportes.php';

let cmpChart = null;
let cmpDatosCompletos = [];
let anioCmp = new Date().getFullYear();

// ─── Estilos ──────────────────────────────────────────────────────────────────
(function inyectarEstilosCmp() {
    if (document.getElementById('_estCmp')) return;
    const s = document.createElement('style');
    s.id = '_estCmp';
    s.textContent = `
        .cmp-kpi-hover { transition: transform .25s, box-shadow .25s; cursor: pointer; position: relative; }
        .cmp-kpi-hover:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.15) !important; }

        /* ── Tooltip KPI ── */
        .cmp-kpi-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            background: rgba(30,41,59,.96);
            color: #f1f5f9;
            font-size: .82rem;
            line-height: 1.45;
            padding: .55rem .85rem;
            border-radius: 8px;
            white-space: nowrap;
            z-index: 99;
            pointer-events: none;
            box-shadow: 0 4px 14px rgba(0,0,0,.22);
            max-width: 240px;
            white-space: normal;
            text-align: center;
        }
        .cmp-kpi-tooltip::after {
            content: '';
            position: absolute;
            top: 100%; left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: rgba(30,41,59,.96);
        }
        .cmp-kpi-hover:hover .cmp-kpi-tooltip { display: block; }

        /* ── Flip card ── */
        .cmp-flip { perspective: 1200px; height: 360px; cursor: pointer; outline: none; position: relative; }
        .cmp-flip__front, .cmp-flip__back {
            position: absolute; inset: 0; width: 100%; height: 100%;
            backface-visibility: hidden; -webkit-backface-visibility: hidden;
            border-radius: 12px !important;
            transition: transform .55s cubic-bezier(.4,0,.2,1); overflow: hidden;
        }
        .cmp-flip__front { transform: rotateY(0deg); z-index: 2; }
        .cmp-flip__back  { transform: rotateY(180deg); }
        .cmp-flip--flipped .cmp-flip__front { transform: rotateY(-180deg); }
        .cmp-flip--flipped .cmp-flip__back  { transform: rotateY(0deg); }

        .cmp-flip__back--0 { background: linear-gradient(160deg,#fffbeb,#fef3c7) !important; border-top: 3px solid #f59e0b !important; }
        .cmp-flip__back--1 { background: linear-gradient(160deg,#f8fafc,#e2e8f0) !important; border-top: 3px solid #94a3b8 !important; }
        .cmp-flip__back--2 { background: linear-gradient(160deg,#fff5f5,#fee2e2) !important; border-top: 3px solid #ef4444 !important; }

        .cmp-corner-label {
            position: absolute; top: 0; left: 0;
            padding: .3rem .9rem; font-size: .85rem; font-weight: 800;
            letter-spacing: .04em; border-radius: 0 0 8px 0;
            color: #fff; z-index: 3; line-height: 1.6;
        }
        .cmp-corner-label--0 { background: #f59e0b; }
        .cmp-corner-label--1 { background: #94a3b8; }
        .cmp-corner-label--2 { background: #ef4444; }
        .cmp-flip__front--0 { border-top: 4px solid #f59e0b !important; }
        .cmp-flip__front--1 { border-top: 4px solid #94a3b8 !important; }
        .cmp-flip__front--2 { border-top: 4px solid #ef4444 !important; }

        .cmp-flip-nombre {
            font-weight: 700; font-size: 1rem; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 3;
            -webkit-box-orient: vertical; overflow: hidden; text-align: center;
        }
        .cmp-back-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: .5rem 0; border-bottom: 1px solid rgba(0,0,0,.07); font-size: .92rem;
        }
        .cmp-back-row:last-of-type { border-bottom: none; }
        .cmp-back-label { color: #475569; font-size: .88rem; }
        .cmp-flip-hint { font-size: .78rem; color: #94a3b8; margin-top: .6rem; display: flex; align-items: center; justify-content: center; gap: .3rem; }

        #graficoCmp { cursor: pointer; }

        /* ── Panel de detalle ── */
        #cmpDetalleWrapper { display: none; overflow: hidden; }
        #cmpDetalleWrapper.cmp-detalle-visible {
            display: block;
            animation: cmpSlideDown .3s cubic-bezier(.4,0,.2,1) both;
        }
        @keyframes cmpSlideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .cmpSortLink { color: inherit !important; text-decoration: none !important; }
        .cmpSortLink:hover { color: #0d6efd !important; }

        /* ── Lista scrolleable de participantes ── */
        .cmp-participantes-scroll {
            max-height: 260px; overflow-y: auto;
            border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;
        }
        .cmp-participantes-scroll::-webkit-scrollbar { width: 6px; }
        .cmp-participantes-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
        .cmp-participantes-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .cmp-participantes-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .cmp-part-item {
            display: flex; align-items: center; gap: .7rem;
            padding: .55rem .85rem; border-bottom: 1px solid #e2e8f0;
            font-size: .93rem; transition: background .15s;
        }
        .cmp-part-item:last-child { border-bottom: none; }
        .cmp-part-item:hover { background: #eff6ff; }
        .cmp-part-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #dbeafe; color: #1d4ed8;
            font-size: .78rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .cmp-part-nombre { font-weight: 600; font-size: .93rem; }
        .cmp-part-cedula { color: #64748b; font-size: .82rem; }

        /* ── Campo diferencia ── */
        .cmp-diff-toggle {
            cursor: pointer; user-select: none;
            border-radius: 8px; padding: .65rem .9rem;
            background: #f1f5f9; border: 1px dashed #cbd5e1;
            transition: background .2s, border-color .2s;
        }
        .cmp-diff-toggle:hover { background: #e0e7ff; border-color: #818cf8; }
        .cmp-diff-inner {
            transition: opacity .22s ease, transform .22s ease;
            opacity: 1; transform: translateY(0);
        }
        .cmp-diff-inner.cmp-diff-fade-out {
            opacity: 0; transform: translateY(4px); pointer-events: none;
        }

        /* ── Bloque fechas/duración ── */
        .cmp-fechas-grupo {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: .75rem 1rem;
        }
        .cmp-fechas-titulo {
            color: #475569; font-weight: 700; font-size: .82rem;
            letter-spacing: .05em; text-transform: uppercase; margin-bottom: .5rem;
        }
        .cmp-fecha-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: .38rem 0; font-size: .92rem;
            border-bottom: 1px solid #e9eef5;
        }
        .cmp-fecha-row:last-child { border-bottom: none; }
        .cmp-fecha-label { color: #475569; font-size: .88rem; display: flex; align-items: center; gap: .4rem; }
        .cmp-fecha-valor { font-weight: 600; font-size: .92rem; }

        /* ── Duración restante ── */
        .cmp-duracion-restante {
            font-size: .88rem; padding: .28rem .7rem; border-radius: 6px;
            font-weight: 700; display: inline-flex; align-items: center; gap: .35rem;
        }
        .cmp-duracion-restante.en-curso   { background: #dcfce7; color: #166534; }
        .cmp-duracion-restante.finalizado { background: #fee2e2; color: #991b1b; }
        .cmp-duracion-restante.pendiente  { background: #fef3c7; color: #92400e; }
        .cmp-duracion-restante.sin-fecha  { background: #f1f5f9; color: #64748b; }

        .cmp-metric-card {
            background: #f1f5f9; border-radius: 10px;
            padding: .75rem 1rem; text-align: center;
        }
        .cmp-metric-value { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
        .cmp-metric-label { font-size: .85rem; color: #475569; margin-top: .2rem; }

        .btn-view-info-curso {
            background: #0a369d; color: white;
            border-radius: 10px; padding: .75rem 1rem;
            font-weight: 600; font-size: .9rem; border: none;
            width: 100%; transition: all 0.2s ease;
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-view-info-curso:hover {
            background: #0281fe; color: white; transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 129, 254, 0.2);
        }
    `;
    document.head.appendChild(s);
})();

// ─── Helper fetch ─────────────────────────────────────────────────────────────
async function apiFetchCmp(accion, params = {}) {
    const url = new URL(API_URL_CMP, window.location.href);
    url.searchParams.set('accion', accion);
    url.searchParams.set('anio', anioCmp);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
    const resp = await fetch(url.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const data = await resp.json();
    if (data && data.error) throw new Error(data.error);
    return data;
}

// ─── Clasificación ────────────────────────────────────────────────────────────
function cmpClasificar(datos) {
    if (!datos || datos.length === 0) return null;
    const vals = datos.map(d => parseInt(d.total_participantes) || 0);
    const prom = vals.reduce((s, v) => s + v, 0) / vals.length;
    const desv = Math.sqrt(vals.reduce((s, v) => s + Math.pow(v - prom, 2), 0) / vals.length);
    const umbSup = prom + desv;
    const umbInf = Math.max(0, prom - desv);
    const clasificar = n => {
        if (n >= umbSup) return { estado: 'Muy Activo', clase: 'bg-danger' };
        if (n >= umbInf) return { estado: 'Activo', clase: 'bg-warning text-dark' };
        return { estado: 'Normal', clase: 'bg-info' };
    };
    return datos.map(c => {
        const cl = clasificar(parseInt(c.total_participantes) || 0);
        return { ...c, clasificacion: cl.estado, claseBadge: cl.clase };
    });
}

// ─── Duración legible ─────────────────────────────────────────────────────────
function cmpDuracion(fi, ff) {
    if (!fi || !ff) return 'Sin información';
    const dias = Math.round((new Date(ff) - new Date(fi)) / 864e5);
    if (isNaN(dias) || dias < 0) return 'Sin información';
    if (dias === 0) return '1 día';
    if (dias < 7) return `${dias} día${dias !== 1 ? 's' : ''}`;
    if (dias < 30) {
        const sem = Math.round(dias / 7);
        return `${sem} semana${sem !== 1 ? 's' : ''} (${dias} días)`;
    }
    const m = Math.round(dias / 30);
    return `${m} mes${m !== 1 ? 'es' : ''} (aprox. ${dias} días)`;
}

// ─── Duración restante ────────────────────────────────────────────────────────
function cmpDuracionRestante(fi, ff) {
    if (!fi || !ff) return { texto: 'Sin fechas registradas', clase: 'sin-fecha', icono: 'bi-calendar-x' };
    const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
    const inicio = new Date(fi);
    const fin = new Date(ff);
    if (isNaN(inicio.getTime()) || isNaN(fin.getTime()))
        return { texto: 'Sin fechas registradas', clase: 'sin-fecha', icono: 'bi-calendar-x' };
    if (hoy < inicio) {
        const d = Math.round((inicio - hoy) / 864e5);
        return { texto: `Inicia en ${d} día${d !== 1 ? 's' : ''}`, clase: 'pendiente', icono: 'bi-hourglass-split' };
    }
    if (hoy > fin) return { texto: 'Curso finalizado', clase: 'finalizado', icono: 'bi-check-circle-fill' };
    const dr = Math.round((fin - hoy) / 864e5);
    if (dr === 0) return { texto: 'Finaliza el día de hoy', clase: 'en-curso', icono: 'bi-flag-fill' };
    if (dr < 7) return { texto: `${dr} día${dr !== 1 ? 's' : ''} para culminar`, clase: 'en-curso', icono: 'bi-clock-fill' };
    if (dr < 30) {
        const sw = Math.round(dr / 7);
        return { texto: `${sw} semana${sw !== 1 ? 's' : ''} para culminar`, clase: 'en-curso', icono: 'bi-clock-fill' };
    }
    const sm = Math.round(dr / 30);
    return { texto: `${sm} mes${sm !== 1 ? 'es' : ''} para culminar`, clase: 'en-curso', icono: 'bi-clock-fill' };
}

// ─── Función principal ────────────────────────────────────────────────────────
async function cargarCursosMasParticipantes() {
    const contentDiv = document.getElementById('reportContent');
    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Cursos con más Participantes</h4>
                        <p class="mb-0 small opacity-75">Clasificación de cursos por participación total</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-white small fw-semibold mb-0">Año:</label>
                        <select id="selectAnioCmp" class="form-select form-select-sm" style="width:110px;"></select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-5" id="loadingCmp" style="display:flex;flex-direction:column;align-items:center;">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
                    <p class="mt-3 text-muted">Cargando datos desde la base de datos...</p>
                </div>
                <div id="contenidoCmp" style="display:none;"></div>
                <div id="errorCmp" style="display:none;" class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="errorMsgCmp"></span>
                </div>
            </div>
        </div>`;

    try {
        const anios = await apiFetchCmp('anios_disponibles');
        const select = document.getElementById('selectAnioCmp');
        if (select && Array.isArray(anios) && anios.length > 0) {
            anios.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a; opt.textContent = a;
                if (a == anioCmp) opt.selected = true;
                select.appendChild(opt);
            });
            select.addEventListener('change', e => { anioCmp = parseInt(e.target.value); cargarContenidoCmp(); });
        }
    } catch (_) { }

    cargarContenidoCmp();
}

async function cargarContenidoCmp() {
    const loadEl = document.getElementById('loadingCmp');
    const contEl = document.getElementById('contenidoCmp');
    const errorEl = document.getElementById('errorCmp');
    const errMsg = document.getElementById('errorMsgCmp');

    loadEl.style.cssText = 'display:flex;flex-direction:column;align-items:center;';
    contEl.style.display = 'none';
    errorEl.style.display = 'none';
    if (cmpChart) { cmpChart.destroy(); cmpChart = null; }

    try {
        const cursosRaw = await apiFetchCmp('cursos_mas_participantes', { limite: 50 });
        if (!Array.isArray(cursosRaw) || cursosRaw.length === 0)
            throw new Error('No hay datos de cursos para el año seleccionado.');
        cmpDatosCompletos = cursosRaw;
        renderCmp(cursosRaw);
        loadEl.style.display = 'none';
        contEl.style.display = 'block';
    } catch (err) {
        loadEl.style.display = 'none';
        errorEl.style.display = 'block';
        errMsg.textContent = `No se pudieron cargar los datos: ${err.message}`;
        console.error('[CMP]', err);
    }
}

// ─── Render principal ─────────────────────────────────────────────────────────
function renderCmp(datos) {
    const contEl = document.getElementById('contenidoCmp');
    if (!contEl) return;

    const clasificados = cmpClasificar(datos);
    if (!clasificados) return;

    console.log('[CMP] Primer curso de muestra:', clasificados[0]);

    const totalPart = clasificados.reduce((s, c) => s + (parseInt(c.total_participantes) || 0), 0);
    const promedio = Math.round(totalPart / clasificados.length);
    const top10 = [...clasificados]
        .sort((a, b) => (parseInt(b.total_participantes) || 0) - (parseInt(a.total_participantes) || 0))
        .slice(0, 10);
    const topCurso = top10[0] || null;

    const estatusOpts = [...new Set(datos.map(c => c.estatus).filter(Boolean))].sort();
    const modalidadOpts = [...new Set(datos.map(c => c.modalidad).filter(Boolean))].sort();

    contEl.innerHTML = `
        <!-- Clasificación Top 3 -->
        <div class="mb-4">
            <h5 class="fw-semibold mb-3" style="font-size:1.1rem;">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Clasificación de Cursos más Activos
            </h5>
            <div class="row g-3 justify-content-center" id="cmpTop3Cards"></div>
        </div>

        <!-- KPIs -->
        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 cmp-kpi-hover">
                    <div class="cmp-kpi-tooltip">Suma de todos los participantes inscritos en los cursos registrados durante el año ${anioCmp}.</div>
                    <div class="card-body text-center py-3">
                        <div style="font-size:2rem;" class="text-primary"><i class="bi bi-people-fill"></i></div>
                        <div class="fw-semibold mt-1" style="font-size:.95rem;">Total de Participantes</div>
                        <div class="fw-bold text-primary" style="font-size:1.75rem;">${totalPart.toLocaleString()}</div>
                        <div class="text-muted" style="font-size:.85rem;">en cursos del año ${anioCmp}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 cmp-kpi-hover">
                    <div class="cmp-kpi-tooltip">Cantidad promedio de participantes por curso, calculada sobre el total de cursos analizados en el año ${anioCmp}.</div>
                    <div class="card-body text-center py-3">
                        <div style="font-size:2rem;" class="text-success"><i class="bi bi-bar-chart-fill"></i></div>
                        <div class="fw-semibold mt-1" style="font-size:.95rem;">Promedio por Curso</div>
                        <div class="fw-bold text-success" style="font-size:1.75rem;">${promedio.toLocaleString()}</div>
                        <div class="text-muted" style="font-size:.85rem;">participantes en promedio</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 cmp-kpi-hover">
                    <div class="cmp-kpi-tooltip">Número total de cursos con al menos un participante inscrito que fueron considerados en este análisis.</div>
                    <div class="card-body text-center py-3">
                        <div style="font-size:2rem;" class="text-warning"><i class="bi bi-journal-text"></i></div>
                        <div class="fw-semibold mt-1" style="font-size:.95rem;">Cursos Analizados</div>
                        <div class="fw-bold text-warning" style="font-size:1.75rem;">${clasificados.length}</div>
                        <div class="text-muted" style="font-size:.85rem;">con participantes inscritos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 cmp-kpi-hover">
                    <div class="cmp-kpi-tooltip">El curso con la mayor cantidad de participantes inscritos en el período seleccionado. Haz clic en la tabla para ver sus detalles.</div>
                    <div class="card-body text-center py-3">
                        <div style="font-size:2rem;" class="text-danger"><i class="bi bi-fire"></i></div>
                        <div class="fw-semibold mt-1" style="font-size:.95rem;">Curso más Activo</div>
                        <div class="fw-bold text-danger lh-sm mt-1" style="font-size:1rem;">${topCurso ? topCurso.nombre_curso : '—'}</div>
                        <div class="text-muted" style="font-size:.85rem;">${topCurso ? parseInt(topCurso.total_participantes).toLocaleString() + ' participantes' : 'Sin datos'}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size:1.1rem;">Participantes por Curso</h5>
                        <div style="position:relative;height:340px;"><canvas id="graficoCmp"></canvas></div>
                        <p class="text-center text-muted mt-2 mb-0" style="font-size:.9rem;">
                            <i class="bi bi-hand-index me-1"></i>Haga clic en una barra para ver los detalles del curso
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de detalle -->
        <div class="row mt-3" id="cmpDetalleWrapper">
            <div class="col-12">
                <div class="card border-0 shadow-sm" id="cmpDetalleCard" style="border-left:4px solid #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0" style="font-size:1.1rem;">Detalle del Curso</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="cmpCerrarDetalle()">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div id="detalleCmp"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla clasificación -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="card-title mb-0" style="font-size:1.1rem;">Clasificación de Cursos</h5>
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <select id="cmpFiltroEstatus" class="form-select form-select-sm" style="width:auto;" onchange="cmpFiltrarTabla()">
                                    <option value="">Todos los estatus</option>
                                    ${estatusOpts.map(e => `<option value="${e}">${e}</option>`).join('')}
                                </select>
                                <select id="cmpFiltroModalidad" class="form-select form-select-sm" style="width:auto;" onchange="cmpFiltrarTabla()">
                                    <option value="">Todas las modalidades</option>
                                    ${modalidadOpts.map(m => `<option value="${m}">${m}</option>`).join('')}
                                </select>
                                <select id="cmpFiltroClasif" class="form-select form-select-sm" style="width:auto;" onchange="cmpFiltrarTabla()">
                                    <option value="">Todos los estados</option>
                                    <option value="Muy Activo">Muy Activo</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Normal">Normal</option>
                                </select>
                                <div class="input-group input-group-sm" style="width:210px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="cmpFiltroBusqueda" class="form-control form-control-sm border-start-0" placeholder="Buscar curso..." oninput="cmpFiltrarTabla()">
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="cmpLimpiarFiltros()">
                                    <i class="bi bi-x-circle me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tablaCmp">
                                <thead class="table-light">
                                    <tr style="font-size:.93rem;">
                                        <th style="width:60px;">Pos.</th>
                                        <th><a href="#" class="cmpSortLink" data-col="nombre_curso">Curso <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                        <th><a href="#" class="cmpSortLink" data-col="preimpreso">Preimpreso <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                        <th><a href="#" class="cmpSortLink" data-col="fecha_inicio">Mes Reinicio <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                        <th><a href="#" class="cmpSortLink" data-col="total_participantes">Participantes <i class="bi bi-arrow-down ms-1"></i></a></th>
                                        <th><a href="#" class="cmpSortLink" data-col="estatus">Estatus <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                        <th><a href="#" class="cmpSortLink" data-col="modalidad">Modalidad <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                        <th>Duración</th>
                                        <th><a href="#" class="cmpSortLink" data-col="clasificacion">Estado <i class="bi bi-arrow-down-up ms-1"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody id="cmpTbody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                            <div class="text-muted" style="font-size:.9rem;" id="cmpInfoPag"></div>
                            <ul class="pagination pagination-sm mb-0" id="cmpPaginacion"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // ── Estado interno ────────────────────────────────────────────────────────
    const CMP_POR_PAGINA = 10;
    let cmpPagActual = 1;
    let cmpOrdenCol = 'total_participantes';
    let cmpOrdenDir = 'desc';
    let cmpDatosFilt = [...clasificados];
    let cmpCursoActivoId = null;

    // ── Top 3 flip cards ──────────────────────────────────────────────────────
    const top3 = top10.slice(0, 3);
    const posLabels = ['1°', '2°', '3°'];
    const posIcons = [
        { icon: 'bi-trophy-fill', color: 'text-warning' },
        { icon: 'bi-award-fill', color: 'text-secondary' },
        { icon: 'bi-award', color: 'text-danger' },
    ];

    const top3Container = document.getElementById('cmpTop3Cards');
    if (top3Container && top3.length > 0) {
        top3Container.innerHTML = top3.map((curso, i) => {
            const part = parseInt(curso.total_participantes || 0);
            const pctTotal = totalPart > 0 ? ((part / totalPart) * 100).toFixed(1) : 0;
            const ic = posIcons[i];
            const clsCla = { 'Muy Activo': 'bg-danger', 'Activo': 'bg-warning text-dark', 'Normal': 'bg-info' }[curso.clasificacion] || 'bg-secondary';
            const nombreEsc = (curso.nombre_curso || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const idCurso = parseInt(curso.id_curso) || 0;

            return `
            <div class="col-md-4">
                <div class="cmp-flip" id="cmpFlip${i}" role="button" tabindex="0"
                     title="Clic para girar / ver detalles"
                     onclick="cmpFlipClick(this, ${idCurso}, '${nombreEsc}')"
                     onkeydown="if(event.key==='Enter'||event.key===' ')cmpFlipClick(this,${idCurso},'${nombreEsc}')">

                    <div class="cmp-flip__front cmp-flip__front--${i} card border-0 shadow">
                        <div class="cmp-corner-label cmp-corner-label--${i}">${posLabels[i]}</div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4 px-3">
                            <div class="mb-3 mt-2"><i class="bi ${ic.icon} ${ic.color}" style="font-size:3rem;"></i></div>
                            <h5 class="cmp-flip-nombre mb-3">${curso.nombre_curso}</h5>
                            <div class="mb-2">
                                <div class="fw-bold text-primary" style="font-size:1.6rem;">${part.toLocaleString()}</div>
                                <div class="text-muted" style="font-size:.9rem;">participantes totales</div>
                            </div>
                            <span class="badge ${clsCla} mt-1" style="font-size:.85rem;">${curso.clasificacion}</span>
                            <div class="cmp-flip-hint mt-2"><i class="bi bi-arrow-repeat"></i> Clic para ver detalles</div>
                        </div>
                    </div>

                    <div class="cmp-flip__back cmp-flip__back--${i} card border-0 shadow">
                        <div class="cmp-corner-label cmp-corner-label--${i}">${posLabels[i]}</div>
                        <div class="card-body pt-5 pb-3 px-3 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <h6 class="fw-bold mb-0 lh-sm" style="font-size:.95rem;flex:1;">${curso.nombre_curso}</h6>
                                <span class="badge ${clsCla} ms-2 flex-shrink-0">${curso.clasificacion}</span>
                            </div>
                            <div class="cmp-back-row"><span class="cmp-back-label"><i class="bi bi-people-fill text-primary me-1"></i>Participantes</span><strong>${part.toLocaleString()}</strong></div>
                            <div class="cmp-back-row"><span class="cmp-back-label"><i class="bi bi-pie-chart text-warning me-1"></i>% del total</span><strong class="text-warning">${pctTotal}%</strong></div>
                            <div class="cmp-back-row"><span class="cmp-back-label"><i class="bi bi-tag text-secondary me-1"></i>Estatus</span><span class="badge bg-secondary">${curso.estatus || 'N/D'}</span></div>
                            <div class="cmp-back-row"><span class="cmp-back-label"><i class="bi bi-layers text-info me-1"></i>Modalidad</span><span class="badge bg-info">${curso.modalidad || 'N/D'}</span></div>
                            <div class="cmp-back-row"><span class="cmp-back-label"><i class="bi bi-clock text-muted me-1"></i>Duración</span><span>${cmpDuracion(curso.fecha_inicio, curso.fecha_fin)}</span></div>
                            <div class="mt-auto pt-2 text-center"><small class="text-muted">${pctTotal}% del total de participantes</small></div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ── Flip: gira la card y abre detalles SIN auto-scroll ────────────────────
    window.cmpFlipClick = function (el, idCurso, nombreCurso) {
        el.classList.toggle('cmp-flip--flipped');
        const curso = clasificados.find(c => parseInt(c.id_curso) === idCurso);
        if (curso) {
            const chartIdx = top10.findIndex(c => parseInt(c.id_curso) === idCurso);
            mostrarDetalleCmp(curso, chartIdx, false);
        }
    };

    // ── Paleta ────────────────────────────────────────────────────────────────
    const tonosAzulCmp = [
        'rgba(13,110,253,.90)', 'rgba(11,94,215,.88)', 'rgba(9,79,181,.86)',
        'rgba(7,64,150,.84)', 'rgba(5,51,122,.82)', 'rgba(4,42,101,.80)',
        'rgba(3,34,84,.78)', 'rgba(2,27,68,.76)', 'rgba(1,20,52,.74)',
        'rgba(0,14,40,.72)',
    ];
    function generarPaletaCmp(n) {
        const base = [...tonosAzulCmp];
        while (base.length < n) {
            const idx = base.length % tonosAzulCmp.length;
            base.push(tonosAzulCmp[idx].replace(/[\d.]+\)$/, m => (parseFloat(m) * 0.85).toFixed(2) + ')'));
        }
        return base.slice(0, n);
    }

    let cmpActiveIndex = -1;

    // ── Cache de participantes por id_curso ───────────────────────────────────
    const cmpPartCache = {};

    async function cargarParticipantesCurso(idCurso) {
        const key = String(idCurso);
        if (cmpPartCache[key] !== undefined) return cmpPartCache[key];
        try {
            console.log('[CMP] Cargando participantes para id_curso:', idCurso);
            const data = await apiFetchCmp('participantes_por_curso', { id_curso: idCurso });
            console.log('[CMP] Respuesta participantes:', data);
            cmpPartCache[key] = Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('[CMP] Error cargando participantes:', e);
            cmpPartCache[key] = [];
        }
        return cmpPartCache[key];
    }

    function renderParticipantesScroll(participantes, contenedor) {
        if (!contenedor) return;
        if (!participantes || participantes.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people" style="font-size:2rem;opacity:.35;"></i>
                    <p class="mb-0 mt-2" style="font-size:.95rem;">No hay participantes registrados en este curso.</p>
                </div>`;
            return;
        }
        contenedor.innerHTML = participantes.map(p => {
            const nombre = (`${p.primer_nombre || ''} ${p.primer_apellido || ''}`).trim() || 'Sin nombre';
            const iniciales = nombre.split(' ').slice(0, 2).map(w => w[0] || '').join('').toUpperCase();
            const estatus = p.estatus_participante || '';
            const clasBadge = estatus === 'Aprobado' ? 'bg-success'
                : estatus === 'En formación' ? 'bg-primary'
                    : estatus === 'Ausente' || estatus === 'Reprobado' ? 'bg-danger'
                        : 'bg-secondary';
            return `
                <div class="cmp-part-item">
                    <div class="cmp-part-avatar">${iniciales}</div>
                    <div class="flex-grow-1">
                        <div class="cmp-part-nombre">${nombre}</div>
                        ${p.cedula ? `<div class="cmp-part-cedula">C.I. ${parseInt(p.cedula).toLocaleString()}</div>` : ''}
                    </div>
                    ${estatus ? `<span class="badge ${clasBadge}" style="font-size:.8rem;">${estatus}</span>` : ''}
                </div>`;
        }).join('');
    }

    // ── Panel de detalle ──────────────────────────────────────────────────────
    async function mostrarDetalleCmp(d, chartIdx = -1, autoScroll = true) {
        if (!d) return;

        const idCurso = parseInt(d.id_curso) || 0;

        if (cmpCursoActivoId === idCurso && idCurso > 0) {
            const wrapper = document.getElementById('cmpDetalleWrapper');
            if (wrapper && wrapper.classList.contains('cmp-detalle-visible')) {
                if (autoScroll) wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }
        }
        cmpCursoActivoId = idCurso;

        const part = parseInt(d.total_participantes || 0);
        const maxPart = parseInt(top10[0]?.total_participantes || 1);
        const pctGlobal = totalPart > 0 ? ((part / totalPart) * 100).toFixed(1) : 0;
        const diffPart = maxPart - part;
        const diffPct = maxPart > 0 ? (((maxPart - part) / maxPart) * 100).toFixed(1) : '0.0';
        const esMasActivo = (part === maxPart);
        const iniciales = (d.nombre_curso || 'XX').split(' ').slice(0, 2).map(w => w[0] || '').join('').toUpperCase();
        const clsCla = { 'Muy Activo': 'danger', 'Activo': 'warning', 'Normal': 'info' }[d.clasificacion] || 'secondary';
        const colorBorde = clsCla === 'danger' ? '#dc3545' : clsCla === 'warning' ? '#ffc107' : '#0dcaf0';
        const durRest = cmpDuracionRestante(d.fecha_inicio, d.fecha_fin);
        const fechaIniStr = d.fecha_inicio ? new Date(d.fecha_inicio).toLocaleDateString('es-VE', { day: '2-digit', month: 'long', year: 'numeric' }) : 'Sin información';
        const fechaFinStr = d.fecha_fin ? new Date(d.fecha_fin).toLocaleDateString('es-VE', { day: '2-digit', month: 'long', year: 'numeric' }) : 'Sin información';

        const wrapper = document.getElementById('cmpDetalleWrapper');
        const card = document.getElementById('cmpDetalleCard');
        if (wrapper) { wrapper.classList.remove('cmp-detalle-visible'); void wrapper.offsetWidth; wrapper.classList.add('cmp-detalle-visible'); }
        if (card) card.style.borderLeftColor = colorBorde;

        // Actualizar gráfico
        if (cmpChart && chartIdx >= 0) {
            cmpActiveIndex = chartIdx;
            const tot = cmpChart.data.datasets[0].data.length;
            const paleta = generarPaletaCmp(tot);
            cmpChart.data.datasets[0].backgroundColor = paleta.map((c, i) => i === chartIdx ? 'rgba(253,126,20,.95)' : c);
            cmpChart.data.datasets[0].borderColor = paleta.map((c, i) => i === chartIdx ? 'rgba(200,90,0,1)' : c.replace(/[\d.]+\)$/, '1)'));
            cmpChart.data.datasets[0].borderWidth = Array.from({ length: tot }, (_, i) => i === chartIdx ? 3 : 2);
            cmpChart.update('none');
        }

        const diffHtml = esMasActivo
            ? `<div class="cmp-diff-toggle" style="cursor:default;border-style:solid;pointer-events:none;">
                   <div class="text-center">
                       <i class="bi bi-fire text-danger" style="font-size:1.3rem;"></i>
                       <div class="fw-bold text-danger mt-1" style="font-size:.95rem;line-height:1.3;">Actualmente viendo el curso más activo</div>
                   </div>
               </div>`
            : `<div class="cmp-diff-toggle" id="cmpDiffToggle" title="Haga clic para alternar entre porcentaje y número de participantes">
                   <div class="cmp-diff-inner" id="cmpDiffInner">
                       <div class="text-muted fw-semibold" style="font-size:.8rem;margin-bottom:.15rem;">Diferencia con el curso más activo</div>
                       <div class="fw-bold text-danger" style="font-size:1.3rem;" id="cmpDiffValor">-${diffPct}%</div>
                       <div class="text-muted" style="font-size:.78rem;">Toque para ver en número de participantes</div>
                   </div>
               </div>`;

        const detEl = document.getElementById('detalleCmp');
        if (!detEl) return;

        detEl.innerHTML = `
            <div class="row g-3">

                <!-- Col izquierda -->
                <div class="col-md-4 d-flex flex-column gap-3">

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:54px;height:54px;font-size:1.1rem;">${iniciales}</div>
                        <div>
                            <div class="fw-bold lh-sm" style="font-size:1rem;">${d.nombre_curso}</div>
                            <div class="d-flex gap-1 flex-wrap mt-1">
                                <span class="badge bg-secondary" style="font-size:.82rem;">${d.estatus || 'N/D'}</span>
                                <span class="badge bg-${clsCla}" style="font-size:.82rem;">${d.clasificacion}</span>
                            </div>
                        </div>
                    </div>

                    ${diffHtml}

                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold" style="font-size:.95rem;"><i class="bi bi-people-fill me-1 text-primary"></i>Participantes inscritos</span>
                            <span class="badge bg-primary rounded-pill" id="cmpPartCount" style="font-size:.82rem;">
                                <span class="spinner-border spinner-border-sm" style="width:.65rem;height:.65rem;"></span>
                            </span>
                        </div>
                        <div class="cmp-participantes-scroll" id="cmpPartScroll">
                            <div class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                <p class="mb-0" style="font-size:.9rem;">Cargando participantes…</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col derecha -->
                <div class="col-md-8 d-flex flex-column gap-3">

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="cmp-metric-card">
                                <div class="cmp-metric-value text-primary">${part.toLocaleString()}</div>
                                <div class="cmp-metric-label">Total de Participantes Inscritos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="cmp-metric-card">
                                <div class="cmp-metric-value text-warning">${pctGlobal}%</div>
                                <div class="cmp-metric-label">Participación sobre el total del año ${anioCmp}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bloque fechas/duración -->
                    <div class="cmp-fechas-grupo">
                        <div class="cmp-fechas-titulo"><i class="bi bi-calendar3 me-1"></i>Fechas y Duración del Curso</div>
                        <div class="cmp-fecha-row">
                            <span class="cmp-fecha-label"><i class="bi bi-calendar-check text-success"></i>Inicio del curso</span>
                            <span class="cmp-fecha-valor">${fechaIniStr}</span>
                        </div>
                        <div class="cmp-fecha-row">
                            <span class="cmp-fecha-label"><i class="bi bi-calendar-x text-danger"></i>Finalización del curso</span>
                            <span class="cmp-fecha-valor">${fechaFinStr}</span>
                        </div>
                        <div class="cmp-fecha-row">
                            <span class="cmp-fecha-label"><i class="bi bi-hourglass-split text-secondary"></i>Duración total del curso</span>
                            <span class="badge bg-light text-dark border" style="font-size:.85rem;">${cmpDuracion(d.fecha_inicio, d.fecha_fin)}</span>
                        </div>
                        <div class="cmp-fecha-row">
                            <span class="cmp-fecha-label"><i class="bi bi-clock-fill text-primary"></i>Tiempo restante</span>
                            <span class="cmp-duracion-restante ${durRest.clase}">
                                <i class="bi ${durRest.icono}"></i>${durRest.texto}
                            </span>
                        </div>
                    </div>

                    <!-- Modalidad -->
                    <div class="cmp-metric-card">
                        <div class="cmp-metric-value text-info" style="font-size:1.2rem;">${d.modalidad || 'N/D'}</div>
                        <div class="cmp-metric-label">Modalidad de Impartición</div>
                    </div>
                    <button class="btn-view-info-curso view-curso-card mt-2" data-id="${d.id_curso}" data-bs-toggle="modal" data-bs-target="#modal-viewCurso">
                        <i class="bi bi-info-circle me-1"></i> Ver info del curso
                    </button>
                </div>
            </div>`;

        // ── Toggle diferencia — bug fix: sin animación CSS, control manual ──
        const toggleEl = document.getElementById('cmpDiffToggle');
        const innerEl = document.getElementById('cmpDiffInner');
        const valorEl = document.getElementById('cmpDiffValor');

        if (toggleEl && innerEl && valorEl && !esMasActivo) {
            let diffState = false; // false = porcentaje, true = participantes

            toggleEl.addEventListener('click', () => {
                if (innerEl.classList.contains('cmp-diff-fade-out')) return; // evitar doble clic

                innerEl.classList.add('cmp-diff-fade-out');

                setTimeout(() => {
                    diffState = !diffState;
                    if (diffState) {
                        valorEl.textContent = `-${diffPart.toLocaleString()} participantes`;
                        innerEl.querySelector('.text-muted:last-child').textContent = 'Toque para ver en porcentaje';
                    } else {
                        valorEl.textContent = `-${diffPct}%`;
                        innerEl.querySelector('.text-muted:last-child').textContent = 'Toque para ver en número de participantes';
                    }
                    innerEl.classList.remove('cmp-diff-fade-out');
                }, 230);
            });
        }

        // Cargar participantes usando id_curso numérico
        const partScroll = document.getElementById('cmpPartScroll');
        const partCount = document.getElementById('cmpPartCount');

        if (!idCurso || idCurso <= 0) {
            if (partCount) partCount.textContent = '0';
            if (partScroll) partScroll.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size:1.5rem;"></i>
                    <p class="mb-0 mt-2" style="font-size:.9rem;">No se pudo identificar el curso (id_curso inválido).</p>
                </div>`;
        } else {
            cargarParticipantesCurso(idCurso).then(participantes => {
                if (partCount) partCount.textContent = participantes.length;
                renderParticipantesScroll(participantes, partScroll);
            });
        }

        if (autoScroll) wrapper?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── Tabla ─────────────────────────────────────────────────────────────────
    function renderTabla() {
        const maxPart = cmpDatosFilt.length > 0
            ? Math.max(...cmpDatosFilt.map(c => parseInt(c.total_participantes) || 0)) : 1;

        const ordenados = [...cmpDatosFilt].sort((a, b) => {
            const dir = cmpOrdenDir === 'asc' ? 1 : -1;
            switch (cmpOrdenCol) {
                case 'nombre_curso': return dir * (a.nombre_curso || '').localeCompare(b.nombre_curso || '');
                case 'preimpreso': return dir * (a.preimpreso || '').localeCompare(b.preimpreso || '');
                case 'fecha_inicio': return dir * (a.fecha_inicio || '').localeCompare(b.fecha_inicio || '');
                case 'total_participantes': return dir * ((parseInt(a.total_participantes) || 0) - (parseInt(b.total_participantes) || 0));
                case 'estatus': return dir * (a.estatus || '').localeCompare(b.estatus || '');
                case 'modalidad': return dir * (a.modalidad || '').localeCompare(b.modalidad || '');
                case 'clasificacion': { const p = { 'Muy Activo': 3, 'Activo': 2, 'Normal': 1 }; return dir * ((p[a.clasificacion] || 0) - (p[b.clasificacion] || 0)); }
                default: return 0;
            }
        });

        const total = ordenados.length;
        const inicio = (cmpPagActual - 1) * CMP_POR_PAGINA;
        const fin = Math.min(inicio + CMP_POR_PAGINA, total);
        const tbody = document.getElementById('cmpTbody');
        if (!tbody) return;

        function cmpObtenerNombreMes(fechaStr) {
            if (!fechaStr) return '—';
            const partes = fechaStr.split('-');
            if (partes.length < 2) return '—';
            const mesNum = parseInt(partes[1], 10);
            const meses = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            return meses[mesNum - 1] || '—';
        }

        tbody.innerHTML = ordenados.slice(inicio, fin).length === 0
            ? `<tr><td colspan="9" class="text-center py-5 text-muted">
                   <i class="bi bi-inbox" style="font-size:2.2rem;opacity:.35;"></i>
                   <p class="mt-2 mb-0" style="font-size:.95rem;">No se encontraron cursos.</p>
               </td></tr>`
            : ordenados.slice(inicio, fin).map((item, idx) => {
                const num = inicio + idx + 1;
                const part = parseInt(item.total_participantes || 0);
                const pct = maxPart > 0 ? Math.min(Math.round((part / maxPart) * 100), 100) : 0;
                const clsPos = num === 1 ? 'bg-warning text-dark' : num === 2 ? 'bg-secondary' : num === 3 ? 'bg-danger' : 'bg-light text-dark border';
                const idC = parseInt(item.id_curso) || 0;
                const mesNombre = cmpObtenerNombreMes(item.fecha_inicio);
                const anioCurso = item.fecha_inicio ? item.fecha_inicio.split('-')[0] : '';
                const mesAnioStr = mesNombre !== '—' ? `${mesNombre} ${anioCurso}` : '—';
                return `
                <tr style="cursor:pointer;font-size:.93rem;" onclick="cmpClickFila(${idC})">
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge ${clsPos}">${num}</span>
                            ${num === 1 ? '<i class="bi bi-fire text-danger"></i>' : ''}
                        </div>
                    </td>
                    <td><strong title="${item.nombre_curso}">${item.nombre_curso}</strong></td>
                    <td><span class="text-muted fw-semibold">${item.preimpreso || '—'}</span></td>
                    <td><span class="text-muted fw-semibold">${mesAnioStr}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill">${part.toLocaleString()}</span>
                            <div class="progress flex-grow-1" style="height:8px;max-width:80px;">
                                <div class="progress-bar bg-primary" style="width:${pct}%;"></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary">${item.estatus || 'N/D'}</span></td>
                    <td>${item.modalidad || 'N/D'}</td>
                    <td><span class="badge bg-light text-dark border" style="font-size:.82rem;">${cmpDuracion(item.fecha_inicio, item.fecha_fin)}</span></td>
                    <td><span class="badge ${item.claseBadge}">${item.clasificacion}</span></td>
                </tr>`;
            }).join('');

        const infoPag = document.getElementById('cmpInfoPag');
        if (infoPag) infoPag.textContent = total > 0 ? `Mostrando ${inicio + 1}–${fin} de ${total} cursos` : 'Sin resultados';

        const pag = document.getElementById('cmpPaginacion');
        const totalPags = Math.ceil(total / CMP_POR_PAGINA);
        if (!pag) return;
        if (totalPags <= 1) { pag.innerHTML = ''; return; }

        let html = `<li class="page-item ${cmpPagActual === 1 ? 'disabled' : ''}"><a class="page-link cmpPageBtn" href="#" data-p="${cmpPagActual - 1}">«</a></li>`;
        for (let i = 1; i <= totalPags; i++) {
            if (i === 1 || i === totalPags || Math.abs(i - cmpPagActual) <= 2)
                html += `<li class="page-item ${i === cmpPagActual ? 'active' : ''}"><a class="page-link cmpPageBtn" href="#" data-p="${i}">${i}</a></li>`;
            else if (Math.abs(i - cmpPagActual) === 3)
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        html += `<li class="page-item ${cmpPagActual === totalPags ? 'disabled' : ''}"><a class="page-link cmpPageBtn" href="#" data-p="${cmpPagActual + 1}">»</a></li>`;
        pag.innerHTML = html;

        pag.querySelectorAll('.cmpPageBtn[data-p]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const np = parseInt(a.dataset.p);
                if (np >= 1 && np <= totalPags && np !== cmpPagActual) { cmpPagActual = np; renderTabla(); }
            });
        });
    }

    document.querySelectorAll('.cmpSortLink').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const col = a.dataset.col;
            cmpOrdenDir = (cmpOrdenCol === col) ? (cmpOrdenDir === 'asc' ? 'desc' : 'asc') : (col === 'total_participantes' ? 'desc' : 'asc');
            cmpOrdenCol = col;
            document.querySelectorAll('.cmpSortLink i').forEach(i => i.className = 'bi bi-arrow-down-up ms-1');
            const ico = a.querySelector('i');
            if (ico) ico.className = `bi bi-arrow-${cmpOrdenDir === 'asc' ? 'up' : 'down'} ms-1`;
            cmpPagActual = 1; renderTabla();
        });
    });

    window.cmpFiltrarTabla = function () {
        const fEst = (document.getElementById('cmpFiltroEstatus')?.value) || '';
        const fMod = (document.getElementById('cmpFiltroModalidad')?.value) || '';
        const fCla = (document.getElementById('cmpFiltroClasif')?.value) || '';
        const fBus = ((document.getElementById('cmpFiltroBusqueda')?.value) || '').toLowerCase().trim();
        cmpDatosFilt = clasificados.filter(c => {
            if (fEst && c.estatus !== fEst) return false;
            if (fMod && c.modalidad !== fMod) return false;
            if (fCla && c.clasificacion !== fCla) return false;
            if (fBus && !((c.nombre_curso || '').toLowerCase().includes(fBus) || (c.preimpreso || '').toLowerCase().includes(fBus))) return false;
            return true;
        });
        cmpPagActual = 1; renderTabla();
    };
    window.cmpLimpiarFiltros = function () {
        ['cmpFiltroEstatus', 'cmpFiltroModalidad', 'cmpFiltroClasif', 'cmpFiltroBusqueda']
            .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        cmpDatosFilt = [...clasificados]; cmpPagActual = 1; renderTabla();
    };
    window.cmpClickFila = function (idCurso) {
        const curso = clasificados.find(c => parseInt(c.id_curso) === idCurso);
        if (!curso) return;
        const chartIdx = top10.findIndex(c => parseInt(c.id_curso) === idCurso);
        mostrarDetalleCmp(curso, chartIdx, true);
    };
    window.cmpCerrarDetalle = function () {
        const wrapper = document.getElementById('cmpDetalleWrapper');
        if (wrapper) wrapper.classList.remove('cmp-detalle-visible');
        cmpCursoActivoId = null;
        if (cmpChart) {
            const tot = cmpChart.data.datasets[0].data.length;
            const paleta = generarPaletaCmp(tot);
            cmpChart.data.datasets[0].backgroundColor = paleta;
            cmpChart.data.datasets[0].borderColor = paleta.map(c => c.replace(/[\d.]+\)$/, '1)'));
            cmpChart.data.datasets[0].borderWidth = Array(tot).fill(2);
            cmpChart.update('none');
        }
        cmpActiveIndex = -1;
    };

    renderTabla();

    // ── Gráfico: todos los cursos ─────────────────────────────────────────────
    (function initChart() {
        const canvasEl = document.getElementById('graficoCmp');
        if (!canvasEl) return;
        if (cmpChart) { cmpChart.destroy(); cmpChart = null; }

        const todosOrdenados = [...clasificados].sort((a, b) => (parseInt(b.total_participantes) || 0) - (parseInt(a.total_participantes) || 0));
        const paleta = generarPaletaCmp(todosOrdenados.length);

        cmpChart = new Chart(canvasEl, {
            type: 'bar',
            data: {
                labels: todosOrdenados.map(c => { const n = c.nombre_curso || ''; return n.length > 20 ? n.substring(0, 18) + '…' : n; }),
                datasets: [{
                    label: 'Participantes',
                    data: todosOrdenados.map(c => parseInt(c.total_participantes) || 0),
                    backgroundColor: paleta,
                    borderColor: paleta.map(c => c.replace(/[\d.]+\)$/, '1)')),
                    borderWidth: 2, borderRadius: 5,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title(items) { return todosOrdenados[items[0].dataIndex]?.nombre_curso || ''; },
                            label(ctx) {
                                const d = todosOrdenados[ctx.dataIndex];
                                return [
                                    `Participantes: ${ctx.raw.toLocaleString()}`,
                                    `Estatus: ${d?.estatus || 'N/D'}`,
                                    `Modalidad: ${d?.modalidad || 'N/D'}`,
                                    `Estado: ${d?.clasificacion || 'N/D'}`,
                                ];
                            }
                        },
                        backgroundColor: 'rgba(0,0,0,.85)',
                        padding: 12, cornerRadius: 6,
                        titleFont: { size: 13 }, bodyFont: { size: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v.toLocaleString(), font: { size: 12 } },
                        title: { display: true, text: 'Numero de Participantes', color: '#475569', font: { size: 13 } },
                        grid: { color: 'rgba(0,0,0,.05)' }
                    },
                    x: {
                        title: { display: true, text: 'Cursos', color: '#475569', font: { size: 13 } },
                        grid: { display: false },
                        ticks: { maxRotation: 35, font: { size: 11 } }
                    }
                },
                animation: { duration: 900, easing: 'easeOutQuart' }
            }
        });

        canvasEl.addEventListener('click', function (event) {
            const elements = cmpChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
            if (elements && elements.length > 0) {
                const idx = elements[0].index;
                const curso = todosOrdenados[idx];
                if (curso) mostrarDetalleCmp(curso, idx, true);
            }
        });
    })();

}  // fin renderCmp

// ─── Limpieza ─────────────────────────────────────────────────────────────────
function limpiarCursosMasParticipantes() {
    if (cmpChart) { cmpChart.destroy(); cmpChart = null; }
    cmpDatosCompletos = [];
    anioCmp = new Date().getFullYear();
    delete window.cmpFiltrarTabla;
    delete window.cmpLimpiarFiltros;
    delete window.cmpClickFila;
    delete window.cmpCerrarDetalle;
    delete window.cmpFlipClick;
}

window.cargarCursosMasParticipantes = cargarCursosMasParticipantes;
window.limpiarCursosMasParticipantes = limpiarCursosMasParticipantes;

console.log('RE-Curso-MasParticipantes.js cargado');
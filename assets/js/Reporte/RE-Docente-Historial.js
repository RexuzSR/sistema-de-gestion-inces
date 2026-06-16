// ========== REPORTE: HISTORIAL DE DOCENTE (v3 - CON PUNTAJE BASE POR ACTIVIDAD) ==========
// Conectado a: api_reportes.php

const API_URL_HIST = '../controlador/Reporte/api_reportes.php';

let histDocente       = null;
let histCursos        = [];
let histCursosFilt    = [];
let histListaDocentes = [];
let histFiltroNombre  = '';
let histGrafBar       = null;
let histGrafDona      = null;
let histGrafLinea     = null;
let histGrafEsp       = null;
let histFiltAnio      = 'todos';
let histFiltEstatus   = 'todos';
let histFiltAmbito    = 'todos';
let histOrden         = 'fecha_desc';

async function apiFetchHist(accion, params = {}) {
    const url = new URL(API_URL_HIST, window.location.href);
    url.searchParams.set('accion', accion);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
    const r = await fetch(url, { credentials: 'same-origin' });
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    const d = await r.json();
    if (d?.error) throw new Error(d.error);
    return d;
}

function destruirGrafsHist() {
    [histGrafBar, histGrafDona, histGrafLinea, histGrafEsp].forEach(g => g?.destroy?.());
    histGrafBar = histGrafDona = histGrafLinea = histGrafEsp = null;
}

const HIST_ESTATUS_CFG = {
    'Culminado':   { cls:'success',  bg:'rgba(25,135,84,.8)',   br:'#198754' },
    'En proceso':  { cls:'primary',  bg:'rgba(13,110,253,.8)',  br:'#0d6efd' },
    'En pausa':    { cls:'warning',  bg:'rgba(255,193,7,.8)',   br:'#ffc107' },
    'En espera':   { cls:'secondary',bg:'rgba(108,117,125,.7)', br:'#6c757d' },
    'Cancelado':   { cls:'danger',   bg:'rgba(220,53,69,.8)',   br:'#dc3545' },
};
const clsEst  = e => HIST_ESTATUS_CFG[e]?.cls  || 'secondary';
const bgEst   = e => HIST_ESTATUS_CFG[e]?.bg   || 'rgba(108,117,125,.6)';
const brEst   = e => HIST_ESTATUS_CFG[e]?.br   || '#6c757d';

const HIST_TIPO_CLR = { Fijo:'primary', Contratado:'info', PDL:'success', Voluntario:'secondary' };
const HIST_ESTADO_CLR = { Disponible:'success', Asignado:'warning', 'En formación':'info', Inactivo:'secondary' };

// ⭐ ÍNDICE DE DESEMPEÑO CON PUNTAJE BASE POR ACTIVIDAD
function calcularIndiceDesempenoDetallado(r) {
    const cursosAsignados = parseInt(r.cursos_asignados || 0);
    const cursosCompletados = parseInt(r.cursos_completados || 0);
    const horasAsignadas = parseFloat(r.horas_asignadas || 0);
    const horasCompletadas = parseFloat(r.horas_completadas || 0);
    const totalInscritos = parseInt(r.total_inscritos || 0);
    const totalAprobados = parseInt(r.total_aprobados || 0);
    const cursosTotales = parseInt(r.total_cursos || 0);
    
    // 1. CUMPLIMIENTO DE CURSOS ASIGNADOS (30 puntos)
    let scoreCursos = 0;
    let cursosPorcentaje = 0;
    let cursosDetalle = '';
    
    if (cursosAsignados > 0) {
        cursosPorcentaje = (cursosCompletados / cursosAsignados) * 100;
        scoreCursos = Math.min((cursosPorcentaje / 100) * 30, 30);
        cursosDetalle = `${cursosCompletados} de ${cursosAsignados} cursos completados (${Math.round(cursosPorcentaje)}%)`;
    } else {
        cursosDetalle = 'Sin cursos asignados';
    }
    
    // 2. CUMPLIMIENTO DE HORAS (20 puntos)
    let scoreHoras = 0;
    let horasPorcentaje = 0;
    let horasDetalle = '';
    
    if (horasAsignadas > 0) {
        horasPorcentaje = (horasCompletadas / horasAsignadas) * 100;
        scoreHoras = Math.min((horasPorcentaje / 100) * 20, 20);
        horasDetalle = `${Math.round(horasCompletadas)}h de ${Math.round(horasAsignadas)}h (${Math.round(horasPorcentaje)}%)`;
    } else {
        horasDetalle = 'Sin horas asignadas';
    }
    
    // 3. CALIDAD DE ENSEÑANZA (40 puntos)
    let scoreParticipantes = 0;
    let aprobadosPorcentaje = 0;
    let participantesDetalle = '';
    
    if (totalInscritos > 0) {
        aprobadosPorcentaje = (totalAprobados / totalInscritos) * 100;
        scoreParticipantes = Math.min((aprobadosPorcentaje / 100) * 40, 40);
        participantesDetalle = `${totalAprobados} aprobados de ${totalInscritos} inscritos (${Math.round(aprobadosPorcentaje)}%)`;
    } else {
        participantesDetalle = 'Sin participantes inscritos';
    }
    
    // ⭐ 4. ANTIGÜEDAD (10 puntos) - CON PUNTAJE BASE POR ESTAR ACTIVO
    // Base: 5 puntos por estar activo + hasta 5 puntos adicionales por experiencia
    let scoreAntiguedad = 5; // Puntaje base por estar activo en el sistema
    
    // Puntos adicionales por experiencia (máximo 5 puntos adicionales = 10 total)
    const experienciaAdicional = Math.min(cursosTotales, 5);
    scoreAntiguedad = Math.min(scoreAntiguedad + experienciaAdicional, 10);
    
    let antiguedadDetalle = '';
    if (cursosTotales === 0) {
        antiguedadDetalle = `Docente activo (puntaje base 5/10)`;
    } else {
        antiguedadDetalle = `${cursosTotales} cursos (${experienciaAdicional} extra) + 5 base = ${scoreAntiguedad}/10`;
    }
    
    // CÁLCULO FINAL
    const indice = Math.min(Math.round(scoreParticipantes + scoreCursos + scoreHoras + scoreAntiguedad), 100);
    
    // Clasificación
    let nivel = 'Crítico', color = 'danger', icono = 'bi-emoji-frown';
    if (indice >= 85) { nivel = 'Excelente'; color = 'success'; icono = 'bi-stars'; }
    else if (indice >= 70) { nivel = 'Bueno'; color = 'primary'; icono = 'bi-emoji-smile'; }
    else if (indice >= 50) { nivel = 'Regular'; color = 'warning'; icono = 'bi-emoji-neutral'; }
    
    return {
        indice, nivel, color, icono,
        cursosPorcentaje: Math.round(cursosPorcentaje),
        horasPorcentaje: Math.round(horasPorcentaje),
        aprobadosPorcentaje: Math.round(aprobadosPorcentaje),
        componentes: {
            participantes: { 
                valor: Math.round(aprobadosPorcentaje), 
                puntaje: Math.round(scoreParticipantes), 
                max: 40, 
                label: 'Calidad de Enseñanza', 
                icono: 'bi-trophy-fill', 
                color: 'success',
                descripcion: participantesDetalle
            },
            cursos: { 
                valor: Math.round(cursosPorcentaje), 
                puntaje: Math.round(scoreCursos), 
                max: 30, 
                label: 'Cumplimiento de Cursos', 
                icono: 'bi-calendar-check', 
                color: 'primary',
                descripcion: cursosDetalle
            },
            horas: { 
                valor: Math.round(horasPorcentaje), 
                puntaje: Math.round(scoreHoras), 
                max: 20, 
                label: 'Cumplimiento de Horas', 
                icono: 'bi-hourglass-split', 
                color: 'info',
                descripcion: horasDetalle
            },
            antiguedad: { 
                valor: scoreAntiguedad, 
                puntaje: Math.round(scoreAntiguedad), 
                max: 10, 
                label: 'Antigüedad', 
                icono: 'bi-calendar3', 
                color: 'secondary',
                descripcion: antiguedadDetalle
            }
        }
    };
}

function generarResumenEjecutivo(d, r, desempeno) {
    const totalCursos = parseInt(r.total_cursos || 0);
    const cursosActivos = parseInt(r.cursos_activos || 0);
    const ambitos = d.ambitos || [];
    const principalAmbito = ambitos.length > 0 ? ambitos[0] : 'ningún ámbito específico';
    
    let texto = `${d.primer_nombre} ${d.primer_apellido} es un docente activo en el sistema. `;
    
    if (r.total_inscritos > 0) {
        texto += `De los ${r.total_inscritos} participantes inscritos en sus cursos, ${r.total_aprobados} han aprobado (${desempeno.aprobadosPorcentaje}% de efectividad). `;
    }
    
    if (r.cursos_asignados > 0) {
        texto += `Ha completado ${r.cursos_completados} de ${r.cursos_asignados} cursos asignados (${desempeno.cursosPorcentaje}% de cumplimiento). `;
    }
    
    if (r.horas_asignadas > 0) {
        texto += `Ha completado ${Math.round(r.horas_completadas)} de ${Math.round(r.horas_asignadas)} horas (${desempeno.horasPorcentaje}% de avance). `;
    }
    
    texto += `Su especialidad principal es ${principalAmbito}. `;
    texto += `Posee un índice de desempeño de ${desempeno.indice}/100 (${desempeno.nivel}).`;
    
    if (desempeno.indice >= 85) {
        texto += ` Es un docente destacado con excelente rendimiento.`;
    } else if (desempeno.indice >= 70) {
        texto += ` Mantiene un buen desempeño.`;
    } else if (desempeno.indice >= 50) {
        texto += ` Se recomienda continuar con su formación para mejorar.`;
    } else {
        texto += ` Requiere acompañamiento pedagógico para mejorar su desempeño.`;
    }
    
    return texto;
}

function agruparCursosPorAnio(cursos) {
    const evolucion = {};
    cursos.forEach(c => {
        if (!c.fecha_inicio) return;
        const anio = new Date(c.fecha_inicio).getFullYear();
        evolucion[anio] = (evolucion[anio] || 0) + 1;
    });
    const aniosOrdenados = Object.keys(evolucion).sort((a, b) => a - b);
    return { labels: aniosOrdenados, data: aniosOrdenados.map(a => evolucion[a]) };
}

function agruparPorEspecializacion(d) {
    let categorias = [];
    if (d.areas && d.areas.length > 0) categorias = d.areas;
    else if (d.ambitos && d.ambitos.length > 0) categorias = d.ambitos;
    else return { labels: ['Sin especialización'], data: [1] };
    
    const conteo = {};
    categorias.forEach(cat => { conteo[cat] = 0; });
    histCursos.forEach(curso => {
        const areaCurso = curso.nombre_area || curso.nombre_ambito;
        if (areaCurso && conteo[areaCurso] !== undefined) conteo[areaCurso]++;
        else if (areaCurso) conteo[areaCurso] = (conteo[areaCurso] || 0) + 1;
    });
    const sorted = Object.entries(conteo).sort((a, b) => b[1] - a[1]);
    return { labels: sorted.map(item => item[0]), data: sorted.map(item => item[1]) };
}

async function cargarListaDocentes() {
    try {
        const r = await apiFetchHist('lista_docentes');
        histListaDocentes = Array.isArray(r) ? r : [];
        return histListaDocentes;
    } catch (err) {
        console.error('Error cargando lista de docentes:', err);
        try {
            const fallback = await apiFetchHist('busqueda_global', { q: 'a', tipo: 'docentes', limite: 100 });
            histListaDocentes = fallback?.docentes || [];
        } catch (e) {
            histListaDocentes = [];
        }
        return histListaDocentes;
    }
}

function filtrarDocentesPorNombre() {
    const filtro = histFiltroNombre.toLowerCase().trim();
    if (!filtro) return histListaDocentes;
    return histListaDocentes.filter(d => 
        (d.primer_nombre?.toLowerCase() || '').includes(filtro) ||
        (d.primer_apellido?.toLowerCase() || '').includes(filtro) ||
        `${d.primer_nombre} ${d.primer_apellido}`.toLowerCase().includes(filtro) ||
        (d.cedula?.toString() || '').includes(filtro)
    );
}

function renderListaDocentes() {
    const container = document.getElementById('histListaDocentes');
    if (!container) return;
    
    const docentesFiltrados = filtrarDocentesPorNombre();
    const cat = (window.categoriasReportes||[]).find(c=>c.id===(window.currentCategoryId||3))||{color:'warning'};
    
    if (docentesFiltrados.length === 0) {
        container.innerHTML = `<div class="text-center py-4 text-muted"><i class="bi bi-people fs-1 opacity-25"></i><p class="mt-2 small">No se encontraron docentes con ese nombre o cédula</p></div>`;
        return;
    }
    
    container.innerHTML = docentesFiltrados.map(d => {
        const ini = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
        const clsTipo = HIST_TIPO_CLR[d.tipo_docente] || 'secondary';
        const clsEst2 = HIST_ESTADO_CLR[d.estado_docente] || 'secondary';
        return `
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 docente-card" 
                     onclick="histSeleccionarDocente(${d.id_docente}, '${(d.primer_nombre+' '+d.primer_apellido).replace(/'/g, "\\'")}', ${d.cedula})"
                     style="cursor: pointer; transition: all 0.2s;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-${cat.color} text-dark d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:50px;height:50px;font-size:1.1rem;">${ini}</div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${d.primer_nombre} ${d.primer_apellido}</div>
                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    <small class="text-muted">CI: ${d.cedula}</small>
                                    <span class="badge bg-${clsTipo}" style="font-size:.6rem;">${d.tipo_docente||'—'}</span>
                                    <span class="badge bg-${clsEst2}" style="font-size:.6rem;">${d.estado_docente||'—'}</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>`;
    }).join('');
    
    if (!document.getElementById('docenteCardHover')) {
        const style = document.createElement('style');
        style.id = 'docenteCardHover';
        style.textContent = `
            .docente-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1) !important; border-left: 4px solid #ffc107 !important; }
            .componente-card { transition: all 0.2s ease; cursor: pointer; }
            .componente-card:hover { transform: translateY(-2px); background: #fff8e7 !important; }
        `;
        document.head.appendChild(style);
    }
}

async function histSeleccionarDocente(idDocente, nombre, cedula) {
    const detalleContainer = document.getElementById('histDetalleContainer');
    const loadingEl = document.getElementById('histLoading');
    const errorEl = document.getElementById('histError');
    
    if (detalleContainer) detalleContainer.style.display = 'none';
    if (errorEl) errorEl.style.display = 'none';
    if (loadingEl) {
        loadingEl.style.display = 'flex';
        loadingEl.style.flexDirection = 'column';
        loadingEl.style.alignItems = 'center';
    }
    if (detalleContainer) detalleContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    destruirGrafsHist();
    
    try {
        const [perfil, cursos] = await Promise.all([
            apiFetchHist('detalle_docente', { cedula }),
            apiFetchHist('historial_cursos_docente', { id_docente: idDocente }),
        ]);
        
        histDocente = perfil;
        histCursos = Array.isArray(cursos) ? cursos : [];
        histCursosFilt = [...histCursos];
        histFiltAnio = histFiltEstatus = histFiltAmbito = 'todos';
        histOrden = 'fecha_desc';
        
        if (loadingEl) loadingEl.style.display = 'none';
        if (detalleContainer) {
            detalleContainer.style.display = 'block';
            renderDetalleDocente();
        }
        const searchInput = document.getElementById('histSearchInput');
        if (searchInput) searchInput.value = nombre;
    } catch (err) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) {
            errorEl.style.display = 'block';
            document.getElementById('histErrMsg').textContent = `Error al cargar: ${err.message}`;
        }
    }
}

function renderDetalleDocente() {
    const d = histDocente;
    const r = d.resumen || {};
    const cat = (window.categoriasReportes||[]).find(c=>c.id===(window.currentCategoryId||3))||{color:'warning'};
    const cursos = histCursosFilt;
    
    const totalH = parseFloat(r.horas_asignadas || 0);
    const compH = parseFloat(r.horas_completadas || 0);
    const pctH = totalH > 0 ? Math.round((compH / totalH) * 100) : 0;
    const clsHpct = pctH >= 75 ? 'success' : pctH >= 40 ? 'warning' : 'danger';
    const fmtF = f => f ? new Date(f).toLocaleDateString('es-VE',{day:'2-digit',month:'short',year:'numeric'}) : '—';
    
    const desempeno = calcularIndiceDesempenoDetallado(r);
    const resumenEjecutivo = generarResumenEjecutivo(d, r, desempeno);
    const evolucion = agruparCursosPorAnio(histCursos);
    
    const aniosDisp = [...new Set(histCursos.map(c => c.fecha_inicio ? new Date(c.fecha_inicio).getFullYear() : null).filter(Boolean))].sort((a,b) => b-a);
    const ambitosDisp = [...new Set(histCursos.map(c => c.nombre_ambito || 'Sin ámbito'))];
    
    const ini = `${(d.primer_nombre||'')[0]||''}${(d.primer_apellido||'')[0]||''}`.toUpperCase();
    const clsTipo = HIST_TIPO_CLR[d.tipo_docente] || 'secondary';
    const clsEst2 = HIST_ESTADO_CLR[d.estado_docente] || 'secondary';
    
    const container = document.getElementById('histDetalleContent');
    if (!container) return;
    
    container.innerHTML = `
    <div class="card border-0 mb-4 hist-perfil-card">
        <div class="card-body p-4">
            <div class="row align-items-center gy-3">
                <div class="col-md-auto text-center text-md-start">
                    <div class="rounded-circle bg-${cat.color} text-dark d-inline-flex align-items-center justify-content-center fw-bold mx-auto"
                         style="width:80px;height:80px;font-size:1.6rem;box-shadow:0 4px 12px rgba(0,0,0,.15);">${ini}</div>
                </div>
                <div class="col-md">
                    <h3 class="fw-bold mb-1">${d.primer_nombre} ${d.segundo_nombre||''} ${d.primer_apellido} ${d.segundo_apellido||''}</h3>
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <span class="badge bg-${clsTipo} px-3 py-2">${d.tipo_docente||'—'}</span>
                        <span class="badge bg-${clsEst2} px-3 py-2">${d.estado_docente||'—'}</span>
                        <span class="text-muted small"><i class="bi bi-person-vcard me-1"></i>CI: ${d.cedula}</span>
                        <span class="text-muted small"><i class="bi bi-envelope me-1"></i>${d.correo||'Sin correo'}</span>
                        ${(d.telefonos||[]).map(t=>`<span class="text-muted small"><i class="bi bi-telephone me-1"></i>${t.numero_de_telefono} <span class="badge bg-light text-secondary border">${t.tipo_de_telefono}</span></span>`).join('')}
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Edad: ${d.edad||'—'} años</span>
                        <span class="text-muted small"><i class="bi bi-gender-ambiguous me-1"></i>${d.genero||'—'}</span>
                        ${r.primer_curso ? `<span class="text-muted small"><i class="bi bi-flag me-1"></i>Primer curso: ${fmtF(r.primer_curso)}</span>` : ''}
                    </div>
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        ${(d.ambitos||[]).map(a=>`<span class="badge bg-light text-dark border" style="font-size:.72rem;">${a}</span>`).join('')}
                        ${(d.areas||[]).map(a=>`<span class="badge bg-light text-primary border" style="font-size:.72rem;"><i class="bi bi-bookmark-fill me-1"></i>${a}</span>`).join('')}
                    </div>
                </div>
                <div class="col-md-auto">
                    <div class="row row-cols-2 g-2" style="min-width:280px;">
                        <div class="col"><div class="card border-0 bg-light text-center py-3 h-100"><h3 class="fw-bold text-${cat.color} mb-0">${parseInt(r.cursos_asignados||0)}</h3><small class="text-muted">Cursos asignados</small></div></div>
                        <div class="col"><div class="card border-0 bg-light text-center py-3 h-100"><h3 class="fw-bold text-success mb-0">${parseInt(r.cursos_completados||0)}</h3><small class="text-muted">Cursos completados</small></div></div>
                        <div class="col"><div class="card border-0 bg-light text-center py-3 h-100"><h3 class="fw-bold text-primary mb-0">${Math.round(r.horas_asignadas||0)}h</h3><small class="text-muted">Horas asignadas</small></div></div>
                        <div class="col"><div class="card border-0 bg-light text-center py-3 h-100"><h3 class="fw-bold text-info mb-0">${Math.round(r.horas_completadas||0)}h</h3><small class="text-muted">Horas completadas</small></div></div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold small">Avance global de horas</span>
                    <span class="fw-bold text-${clsHpct}">${pctH}% completado</span>
                </div>
                <div class="progress" style="height:10px;border-radius:5px;"><div class="progress-bar bg-${clsHpct}" style="width:${pctH}%;transition:width .8s ease;"></div></div>
                <div class="d-flex justify-content-between mt-1"><small class="text-muted">${Math.round(compH)}h completadas</small><small class="text-muted">${Math.round(totalH - compH)}h pendientes</small></div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="fw-bold text-muted mb-0"><i class="bi bi-trophy-fill me-1 text-warning"></i>Índice de Desempeño</h6>
                        <i class="bi bi-info-circle-fill text-muted" style="cursor:help;" 
                           title="Basado en: 40% Calidad de enseñanza, 30% Cumplimiento de cursos, 20% Cumplimiento de horas, 10% Antigüedad (5 base + experiencia)"></i>
                    </div>
                    <div class="text-center mb-3">
                        <div class="display-2 fw-bold text-${desempeno.color}" style="font-size: 3.5rem;">${desempeno.indice}</div>
                        <span class="badge bg-${desempeno.color} px-3 py-2 mt-1 fs-6"><i class="bi ${desempeno.icono} me-1"></i>${desempeno.nivel}</span>
                    </div>
                    <div class="progress mb-4" style="height: 8px; border-radius: 10px;"><div class="progress-bar bg-${desempeno.color}" style="width: ${desempeno.indice}%;"></div></div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center componente-card" title="${desempeno.componentes.participantes.descripcion}">
                                <small class="text-muted d-block"><i class="bi ${desempeno.componentes.participantes.icono} me-1 text-${desempeno.componentes.participantes.color}"></i>${desempeno.componentes.participantes.label}</small>
                                <span class="fw-bold text-${desempeno.componentes.participantes.color} fs-5">${desempeno.componentes.participantes.puntaje}</span>
                                <small class="text-muted">/${desempeno.componentes.participantes.max}</small>
                                <div class="progress mt-1" style="height: 3px;">
                                    <div class="progress-bar bg-${desempeno.componentes.participantes.color}" style="width: ${(desempeno.componentes.participantes.puntaje/desempeno.componentes.participantes.max)*100}%;"></div>
                                </div>
                                <small class="text-muted fs-10">${desempeno.componentes.participantes.valor}%</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center componente-card" title="${desempeno.componentes.cursos.descripcion}">
                                <small class="text-muted d-block"><i class="bi ${desempeno.componentes.cursos.icono} me-1 text-${desempeno.componentes.cursos.color}"></i>${desempeno.componentes.cursos.label}</small>
                                <span class="fw-bold text-${desempeno.componentes.cursos.color} fs-5">${desempeno.componentes.cursos.puntaje}</span>
                                <small class="text-muted">/${desempeno.componentes.cursos.max}</small>
                                <div class="progress mt-1" style="height: 3px;">
                                    <div class="progress-bar bg-${desempeno.componentes.cursos.color}" style="width: ${(desempeno.componentes.cursos.puntaje/desempeno.componentes.cursos.max)*100}%;"></div>
                                </div>
                                <small class="text-muted fs-10">${desempeno.componentes.cursos.valor}%</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center componente-card" title="${desempeno.componentes.horas.descripcion}">
                                <small class="text-muted d-block"><i class="bi ${desempeno.componentes.horas.icono} me-1 text-${desempeno.componentes.horas.color}"></i>${desempeno.componentes.horas.label}</small>
                                <span class="fw-bold text-${desempeno.componentes.horas.color} fs-5">${desempeno.componentes.horas.puntaje}</span>
                                <small class="text-muted">/${desempeno.componentes.horas.max}</small>
                                <div class="progress mt-1" style="height: 3px;">
                                    <div class="progress-bar bg-${desempeno.componentes.horas.color}" style="width: ${(desempeno.componentes.horas.puntaje/desempeno.componentes.horas.max)*100}%;"></div>
                                </div>
                                <small class="text-muted fs-10">${desempeno.componentes.horas.valor}%</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center componente-card" title="${desempeno.componentes.antiguedad.descripcion}">
                                <small class="text-muted d-block"><i class="bi ${desempeno.componentes.antiguedad.icono} me-1 text-${desempeno.componentes.antiguedad.color}"></i>${desempeno.componentes.antiguedad.label}</small>
                                <span class="fw-bold text-${desempeno.componentes.antiguedad.color} fs-5">${desempeno.componentes.antiguedad.puntaje}</span>
                                <small class="text-muted">/${desempeno.componentes.antiguedad.max}</small>
                                <div class="progress mt-1" style="height: 3px;">
                                    <div class="progress-bar bg-${desempeno.componentes.antiguedad.color}" style="width: ${(desempeno.componentes.antiguedad.puntaje/desempeno.componentes.antiguedad.max)*100}%;"></div>
                                </div>
                                <small class="text-muted fs-10">${desempeno.componentes.antiguedad.valor}/10</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3 pt-2 border-top">
                        <small class="text-muted">
                            <i class="bi bi-calculator-fill"></i> 
                            ${desempeno.componentes.participantes.puntaje} + ${desempeno.componentes.cursos.puntaje} + ${desempeno.componentes.horas.puntaje} + ${desempeno.componentes.antiguedad.puntaje} = ${desempeno.indice}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center bg-light">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="fw-bold text-muted">Ranking Institucional</h6>
                    <div class="display-5 fw-bold text-secondary mb-2">---</div>
                    <small class="text-muted">Requiere endpoint backend</small>
                    <div class="mt-2"><span class="badge bg-secondary">Próximamente</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-steps me-2 text-${cat.color}"></i>Especialización por ${(d.areas && d.areas.length > 0) ? 'Área' : 'Ámbito'}</h6>
                    <div style="position:relative; height:200px;"><canvas id="histGrafEspecializacion"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-${cat.color}"></i>Evolución Histórica</h6>
                    <div style="position:relative; height:220px;"><canvas id="histGrafLinea"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-file-text-fill me-2 text-${cat.color}"></i>Resumen Ejecutivo</h6>
                    <div class="alert alert-light border mb-0" style="background: #f8f9fa; max-height: 200px; overflow-y: auto;">
                        <p class="mb-0" style="font-size:0.9rem; line-height:1.5;">${resumenEjecutivo}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2 text-${cat.color}"></i>Cursos por estatus</h6>
                    <div style="position:relative;height:210px;"><canvas id="histGrafDona"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2 text-${cat.color}"></i>Horas asignadas vs completadas (últimos ${Math.min(10,histCursos.length)} cursos)</h6>
                    <div style="position:relative;height:210px;"><canvas id="histGrafBar"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 align-items-end mb-3 pb-3 border-bottom">
                <div><label class="form-label small fw-semibold mb-1">Año</label><select id="histSelAnio" class="form-select form-select-sm" style="min-width:110px;" onchange="histAplicarFiltros()"><option value="todos">Todos los años</option>${aniosDisp.map(a=>`<option value="${a}">${a}</option>`).join('')}</select></div>
                <div><label class="form-label small fw-semibold mb-1">Estatus</label><select id="histSelEstatus" class="form-select form-select-sm" style="min-width:150px;" onchange="histAplicarFiltros()"><option value="todos">Todos</option><option value="Culminado">Culminado</option><option value="En proceso">En proceso</option><option value="En pausa">En pausa</option><option value="En espera">En espera</option><option value="Cancelado">Cancelado</option></select></div>
                <div><label class="form-label small fw-semibold mb-1">Ámbito</label><select id="histSelAmbito" class="form-select form-select-sm" style="min-width:150px;" onchange="histAplicarFiltros()"><option value="todos">Todos</option>${ambitosDisp.map(a=>`<option value="${a}">${a}</option>`).join('')}</select></div>
                <div><label class="form-label small fw-semibold mb-1">Ordenar por</label><select id="histSelOrden" class="form-select form-select-sm" style="min-width:170px;" onchange="histAplicarFiltros()"><option value="fecha_desc">Fecha (más reciente)</option><option value="fecha_asc">Fecha (más antiguo)</option><option value="horas_desc">Más horas</option><option value="avance_desc">Mayor avance</option><option value="participantes_desc">Más participantes</option></select></div>
                <div class="ms-auto"><label class="form-label small d-block mb-1">&nbsp;</label>
                    <button class="btn btn-sm btn-outline-secondary" onclick="histLimpiarFiltros()"><i class="bi bi-x-circle me-1"></i>Limpiar</button>
                    <button class="btn btn-sm btn-danger ms-1" onclick="exportarPdfHistorialDocente()" id="btnExportarPdfHistorial">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="card-title mb-0">Historial de Cursos<span id="histConteo" class="badge bg-secondary ms-2" style="font-size:.75rem;">${histCursos.length} cursos</span></h5></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaHist">
                    <thead class="table-light"><tr><th>Curso</th><th>Período</th><th class="text-center">Horas</th><th style="min-width:130px;">Avance</th><th class="text-center">Participantes</th><th class="text-center">Aprobados</th><th>Ámbito</th><th>Turno</th><th>Estatus</th></tr></thead>
                    <tbody id="tablaHistBody">${histCursos.map(c=>histFilaHtml(c)).join('')}</tbody>
                </table>
            </div>
            <div id="histSinRes" class="text-center py-4 text-muted" style="display:none;"><i class="bi bi-funnel" style="font-size:2rem;opacity:.3;"></i><p class="mt-2 small">Ningún curso coincide con los filtros aplicados.</p></div>
        </div>
    </div>`;
    
    setTimeout(() => {
        histRenderGraficas(histCursos);
        histRenderGraficoEspecializacion();
        histRenderGraficoLineaEvolucion();
    }, 50);
}

function histFilaHtml(c) {
    const asig = parseFloat(c.horas_totales || 0);
    const comp = parseFloat(c.horas_completadas || 0);
    const pct = asig > 0 ? Math.round((comp / asig) * 100) : 0;
    const cls = pct >= 75 ? 'success' : pct >= 40 ? 'warning' : 'danger';
    const ins = parseInt(c.total_inscritos || 0);
    const apr = parseInt(c.aprobados || 0);
    const fmtF = f => f ? new Date(f).toLocaleDateString('es-VE',{day:'2-digit',month:'short',year:'numeric'}) : '—';
    const est = c.estatus_curso || '—';
    return `<tr><td><div class="fw-semibold">${c.nombre_curso}</div><small class="text-muted">${c.tipo_formacion||''}</small></td><td><div class="text-nowrap small">${fmtF(c.fecha_inicio)}</div><div class="text-nowrap small text-muted">${fmtF(c.fecha_fin)}</div></td><td class="text-center"><span class="badge bg-success rounded-pill">${Math.round(asig)}h</span><br><small class="text-primary">${Math.round(comp)}h</small></td><td><div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1" style="height:7px;"><div class="progress-bar bg-${cls}" style="width:${pct}%;"></div></div><small class="fw-bold text-${cls}">${pct}%</small></div></td><td class="text-center"><span class="fw-bold">${ins}</span>${c.max_participantes ? `<br><small class="text-muted">cap. ${c.max_participantes}</small>` : ''}</td><td class="text-center"><span class="badge bg-success rounded-pill">${apr}</span>${ins > 0 ? `<br><small class="text-muted">${Math.round((apr/ins)*100)}%</small>` : ''}</td><td><span class="badge bg-light text-dark border">${c.nombre_ambito||'—'}</span></td><td><small>${c.turno||'—'}</small></td><td><span class="badge bg-${clsEst(est)}">${est}</span></td></tr>`;
}

function histAplicarFiltros() {
    histFiltAnio = document.getElementById('histSelAnio')?.value || 'todos';
    histFiltEstatus = document.getElementById('histSelEstatus')?.value || 'todos';
    histFiltAmbito = document.getElementById('histSelAmbito')?.value || 'todos';
    histOrden = document.getElementById('histSelOrden')?.value || 'fecha_desc';
    
    let filtrados = histCursos.filter(c => {
        const anioC = c.fecha_inicio ? new Date(c.fecha_inicio).getFullYear().toString() : null;
        return (histFiltAnio === 'todos' || anioC === histFiltAnio) &&
               (histFiltEstatus === 'todos' || c.estatus_curso === histFiltEstatus) &&
               (histFiltAmbito === 'todos' || (c.nombre_ambito||'Sin ámbito') === histFiltAmbito);
    });
    
    filtrados.sort((a, b) => {
        if (histOrden === 'fecha_asc') return new Date(a.fecha_inicio||0) - new Date(b.fecha_inicio||0);
        if (histOrden === 'horas_desc') return parseFloat(b.horas_totales||0) - parseFloat(a.horas_totales||0);
        if (histOrden === 'avance_desc') {
            const pa = parseFloat(a.horas_totales||0) > 0 ? parseFloat(a.horas_completadas||0)/parseFloat(a.horas_totales||0) : 0;
            const pb = parseFloat(b.horas_totales||0) > 0 ? parseFloat(b.horas_completadas||0)/parseFloat(b.horas_totales||0) : 0;
            return pb - pa;
        }
        if (histOrden === 'participantes_desc') return parseInt(b.total_inscritos||0) - parseInt(a.total_inscritos||0);
        return new Date(b.fecha_inicio||0) - new Date(a.fecha_inicio||0);
    });
    
    histCursosFilt = filtrados;
    const tbody = document.getElementById('tablaHistBody');
    const sinRes = document.getElementById('histSinRes');
    const conteo = document.getElementById('histConteo');
    if (tbody) tbody.innerHTML = filtrados.map(c => histFilaHtml(c)).join('');
    if (sinRes) sinRes.style.display = filtrados.length === 0 ? 'block' : 'none';
    if (conteo) conteo.textContent = `${filtrados.length} curso${filtrados.length!==1?'s':''}`;
    
    destruirGrafsHist();
    histRenderGraficas(filtrados);
    histRenderGraficoEspecializacion();
}

function histLimpiarFiltros() {
    ['histSelAnio','histSelEstatus','histSelAmbito','histSelOrden'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = id === 'histSelOrden' ? 'fecha_desc' : 'todos';
    });
    histAplicarFiltros();
}

function histRenderGraficas(cursos) {
    const ctxDona = document.getElementById('histGrafDona')?.getContext('2d');
    if (ctxDona && cursos.length > 0) {
        const conteo = {};
        cursos.forEach(c => { const e = c.estatus_curso||'Sin estado'; conteo[e]=(conteo[e]||0)+1; });
        const labels = Object.keys(conteo);
        if (histGrafDona) histGrafDona.destroy();
        histGrafDona = new Chart(ctxDona, {
            type: 'doughnut',
            data: { labels, datasets: [{ data: labels.map(l=>conteo[l]), backgroundColor: labels.map(l=>bgEst(l)), borderColor: labels.map(l=>brEst(l)), borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position:'bottom', labels:{ boxWidth:12, font:{size:11} } } }, animation: { duration:700 } }
        });
    }
    
    const ctxBar = document.getElementById('histGrafBar')?.getContext('2d');
    if (ctxBar && cursos.length > 0) {
        const top = [...cursos].sort((a,b)=>new Date(b.fecha_inicio||0)-new Date(a.fecha_inicio||0)).slice(0,10);
        if (histGrafBar) histGrafBar.destroy();
        histGrafBar = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: top.map(c => c.nombre_curso.length > 18 ? c.nombre_curso.substring(0,16)+'…' : c.nombre_curso),
                datasets: [
                    { label: 'H. Asignadas', data: top.map(c=>parseFloat(c.horas_totales||0)), backgroundColor: 'rgba(255,193,7,.5)', borderColor:'rgba(255,193,7,1)', borderWidth:2, borderRadius:4 },
                    { label: 'H. Completadas', data: top.map(c=>parseFloat(c.horas_completadas||0)), backgroundColor: 'rgba(25,135,84,.7)', borderColor:'rgba(25,135,84,1)', borderWidth:2, borderRadius:4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position:'top' } }, scales: { y: { beginAtZero: true } }, animation: { duration:700 } }
        });
    }
}

function histRenderGraficoEspecializacion() {
    const ctx = document.getElementById('histGrafEspecializacion')?.getContext('2d');
    if (!ctx || !histDocente) return;
    const datos = agruparPorEspecializacion(histDocente);
    if (histGrafEsp) histGrafEsp.destroy();
    histGrafEsp = new Chart(ctx, {
        type: 'bar', data: { labels: datos.labels, datasets: [{ label: 'Cursos impartidos', data: datos.data, backgroundColor: 'rgba(255,193,7,0.7)', borderRadius: 4 }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }, animation: { duration:700 } }
    });
}

function histRenderGraficoLineaEvolucion() {
    const ctx = document.getElementById('histGrafLinea')?.getContext('2d');
    if (!ctx) return;
    const evolucion = agruparCursosPorAnio(histCursos);
    if (histGrafLinea) histGrafLinea.destroy();
    histGrafLinea = new Chart(ctx, {
        type: 'line',
        data: { labels: evolucion.labels, datasets: [{ label: 'Cursos impartidos', data: evolucion.data, borderColor: 'rgba(255,193,7,1)', backgroundColor: 'rgba(255,193,7,0.1)', borderWidth: 3, tension: 0.3, fill: true, pointBackgroundColor: 'rgba(255,193,7,1)', pointBorderColor: '#fff', pointRadius: 5 }] },
        options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }, animation: { duration:700 } }
    });
}

async function cargarHistorialDocente() {
    const div = document.getElementById('reportContent');
    const cat = (window.categoriasReportes||[]).find(c=>c.id===(window.currentCategoryId||3)) || {color:'warning'};
    
    destruirGrafsHist();
    histDocente = null; histCursos = []; histCursosFilt = [];
    histFiltAnio = histFiltEstatus = histFiltAmbito = 'todos';
    histOrden = 'fecha_desc';
    
    div.innerHTML = `
    <div class="card shadow-lg border-0">
        <div class="card-header bg-${cat.color} text-dark d-flex align-items-center gap-3 flex-wrap">
            <div><h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Docente</h4><p class="mb-0 small" style="opacity:.7">Seleccione un docente para ver su perfil completo e historial de cursos</p></div>
        </div>
        <div class="card-body">
            <div id="histListaSection">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2"></i>Seleccione un docente</h5>
                    <div class="position-relative" style="width: 300px;"><i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size:0.9rem;"></i><input type="text" id="histSearchInput" class="form-control ps-5" placeholder="Filtrar por nombre o cédula..." autocomplete="off" oninput="histFiltrarListaDocentes()"></div>
                </div>
                <div id="histListaDocentes" class="row g-3" style="max-height: 400px; overflow-y: auto;"><div class="text-center py-4"><div class="spinner-border text-${cat.color}" role="status"></div><p class="mt-2 text-muted">Cargando lista de docentes...</p></div></div>
            </div>
            <hr class="my-4">
            <div id="histDetalleContainer" style="display: none;">
                <div id="histLoading" style="display:none;" class="text-center py-4"><div class="spinner-border text-${cat.color}"></div><p class="mt-2 text-muted">Cargando datos del docente...</p></div>
                <div id="histError" style="display:none;" class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><span id="histErrMsg"></span></div>
                <div id="histDetalleContent"></div>
            </div>
        </div>
    </div>`;
    
    inyectarEstilosHist();
    await cargarListaDocentes();
    renderListaDocentes();
    
    window.histFiltrarListaDocentes = function() {
        const input = document.getElementById('histSearchInput');
        histFiltroNombre = input?.value || '';
        renderListaDocentes();
    };
}

function limpiarHistorialDocente() {
    destruirGrafsHist();
    histDocente = null; histCursos = []; histCursosFilt = [];
}

function inyectarEstilosHist() {
    if (document.getElementById('_estHist2')) return;
    const s = document.createElement('style');
    s.id = '_estHist2';
    s.textContent = `
        .hist-perfil-card { background: linear-gradient(135deg,#fffdf0 0%,#fff 60%); border:1px solid rgba(255,193,7,.25) !important; }
        #tablaHist tbody tr:hover { background:rgba(255,193,7,.05); }
        .docente-card { transition: all 0.2s ease; cursor: pointer; }
        .docente-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1) !important; border-left: 4px solid #ffc107 !important; }
        #histListaDocentes::-webkit-scrollbar { width: 6px; }
        #histListaDocentes::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        #histListaDocentes::-webkit-scrollbar-thumb { background: #ffc107; border-radius: 10px; }
        .componente-card { transition: all 0.2s ease; cursor: pointer; }
        .componente-card:hover { transform: translateY(-2px); background: #fff8e7 !important; }
    `;
    document.head.appendChild(s);
}

window.cargarHistorialDocente = cargarHistorialDocente;
window.limpiarHistorialDocente = limpiarHistorialDocente;
window.histSeleccionarDocente = histSeleccionarDocente;
window.histAplicarFiltros = histAplicarFiltros;
window.histLimpiarFiltros = histLimpiarFiltros;

console.log('RE-Docente-Historial.js v3 - Con puntaje base 5/10 por antigüedad');
// ─── Exportar a PDF (Historial de Docente) ───────────────────────────────────
async function exportarPdfHistorialDocente() {
    if (!histDocente) {
        alert("Debe seleccionar un docente antes de exportar.");
        return;
    }

    const btn = document.getElementById('btnExportarPdfHistorial');
    const btnTop = document.getElementById('btn-print-report');
    const oldHtml = btn ? btn.innerHTML : '';
    const oldHtmlTop = btnTop ? btnTop.innerHTML : '';
    
    try {
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generando...';
            btn.disabled = true;
        }
        if (btnTop) {
            btnTop.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generando...';
            btnTop.disabled = true;
        }

        // Obtener gráficos en base64
        const getCanvasB64 = (id) => {
            const canvas = document.getElementById(id);
            return canvas ? canvas.toDataURL('image/png', 1.0) : '';
        };

        const graficoDona = getCanvasB64('histGrafDona');
        const graficoBar = getCanvasB64('histGrafBar');
        const graficoLinea = getCanvasB64('histGrafLinea');
        const graficoEsp = getCanvasB64('histGrafEspecializacion');

        // Obtener cálculos dinámicos
        const desempeno = calcularIndiceDesempenoDetallado(histDocente.resumen);
        const resumenEjecutivo = generarResumenEjecutivo(histDocente, histDocente.resumen, desempeno);
        const evolucion = agruparCursosPorAnio(histCursosFilt);
        const especializacion = agruparPorEspecializacion(histDocente);

        const filtros = {
            anio: histFiltAnio,
            estatus: histFiltEstatus,
            ambito: histFiltAmbito,
            orden: histOrden
        };

        const payload = {
            perfil: histDocente,
            cursos: histCursosFilt,
            resumen: histDocente.resumen,
            desempeno: desempeno,
            resumenEjecutivo: resumenEjecutivo,
            evolucion: evolucion,
            especializacion: especializacion,
            filtros: filtros,
            graficoDona: graficoDona,
            graficoBar: graficoBar,
            graficoLinea: graficoLinea,
            graficoEsp: graficoEsp
        };

        const url = new URL(API_URL_HIST, window.location.href);
        url.searchParams.set('accion', 'exportar_pdf_historial_docente');

        const response = await fetch(url.toString(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`Error en el servidor: ${response.status}`);
        }

        const blob = await response.blob();
        if (blob.size === 0) {
            throw new Error("El archivo PDF generado está vacío");
        }
        
        const fileUrl = URL.createObjectURL(blob);
        window.open(fileUrl, '_blank');

    } catch (error) {
        console.error("Error al exportar PDF del historial:", error);
        alert("Ocurrió un error al generar el PDF: " + error.message);
    } finally {
        if (btn) {
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
        if (btnTop) {
            btnTop.innerHTML = oldHtmlTop;
            btnTop.disabled = false;
        }
    }
}
window.exportarPdfHistorialDocente = exportarPdfHistorialDocente;


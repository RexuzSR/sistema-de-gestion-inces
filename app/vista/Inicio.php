<?php
// ==================== SEGURIDAD DE SESIÓN ====================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php'); exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('../modelo/conexion.php');
include('../modelo/IndicadorHelper.php');
$conexion = new Conexion();
$conn = $conexion->getConexion();
$conn->exec("SET NAMES 'utf8mb4'");

// === DATOS DE USUARIO ===
$nombre        = htmlspecialchars($_SESSION['nombre']   ?? '');
$apellido      = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol           = htmlspecialchars($_SESSION['rol']      ?? '');
$imagenUsuario = $_SESSION['imagen'] ?? '';
$imagenBase    = '../../assets/img/avatar/default-user.png';
$imagen        = !empty($imagenUsuario)
    ? (strpos($imagenUsuario, 'assets/') === 0 ? '../../' . $imagenUsuario : $imagenUsuario)
    : $imagenBase;

// ── Helpers globales ──
function n($conn, $sql) { return $conn->query($sql)->fetchColumn(); }
function sem($pct, $lo, $hi, $inv = false) {
    if (!$inv) { return $pct >= $hi ? 'verde' : ($pct >= $lo ? 'amarillo' : 'rojo'); }
    else        { return $pct <= $lo ? 'verde' : ($pct <= $hi ? 'amarillo' : 'rojo'); }
}
function semL($c) { return ['verde'=>'Óptimo','amarillo'=>'Atención','rojo'=>'Crítico'][$c]; }
function jl($arr, $k) { return json_encode(array_column($arr, $k)); }
function jv($arr, $k) { return json_encode(array_map('intval', array_column($arr, $k))); }

// ── Variables compartidas entre secciones ──
$mesesES = [
    1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
    5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
    9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
];
$mesActualES     = $mesesES[(int)date('n')];
$anioActual      = date('Y');
$trimestreActual = (int)ceil(date('n') / 3);
$coloresFase2    = ['#6366f1','#0ea5e9','#28c76f','#f59e0b','#ef4444','#8b5cf6','#ec4899','#10b981'];

// ── KPIs globales para badges del nav ──
$total_participantes = n($conn, "SELECT COUNT(*) FROM tb_participante");
$total_cursos        = n($conn, "SELECT COUNT(*) FROM tb_curso WHERE id_curso != 1");
$total_docentes      = n($conn, "SELECT COUNT(*) FROM tb_docente");

// ── Incluir datos de cada seccion ──
include('../controlador/Dashboard/datos_participantes.php');
include('../controlador/Dashboard/datos_cursos.php');
include('../controlador/Dashboard/datos_docentes.php');
include('../controlador/Dashboard/datos_metas.php');

$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/home.css">
    <link rel="icon" href="../../assets/img/inces-logo.png" type="image/png">
    <title>Inicio — INCES</title>
</head>
<body>
<div class="wrapper">

<!-- ════ SIDEBAR ════ -->
<!-- SideBar -->
        <?php $activePage = 'inicio'; include __DIR__ . '/partials/sidebar.php'; ?>

<div class="main">
    <?php include 'partials/header.php'; ?>
    <div class="home-content px-3 pb-5">

        <!-- ════ NAVEGACIÓN INTERNA ════ -->
        <nav class="dashboard-nav mt-3">
            <button class="dashboard-nav-item active" data-section="resumen">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg>
                Resumen
            </button>
            <button class="dashboard-nav-item" data-section="participantes">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                Participantes <span class="nav-count"><?php echo $total_participantes; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="cursos">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                Cursos <span class="nav-count"><?php echo $total_cursos; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="docentes">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg>
                Docentes <span class="nav-count"><?php echo $total_docentes; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="metas">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                Metas <span class="nav-count"><?php echo (int)($kMetas['total_metas'] ?? 0); ?></span>
            </button>
        </nav>

        <!-- ════ SECCIONES ════ -->
        <?php include 'Dashboard/seccion_resumen.php'; ?>
        <?php include 'Dashboard/seccion_participantes.php'; ?>
        <?php include 'Dashboard/seccion_cursos.php'; ?>
        <?php include 'Dashboard/seccion_docentes.php'; ?>
        <?php include 'Dashboard/seccion_metas.php'; ?>
        <?php include 'Dashboard/Prueba.php'; ?>

    </div>
</div>
</div>

<script>
const userRole     = "<?php echo $_SESSION['rol'] ?? ''; ?>";
const MESES_LABELS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

window.HOME_CHARTS = {
    partEstado:    { labels: <?php echo jl($partEstado,'estado'); ?>,           data: <?php echo jv($partEstado,'t'); ?> },
    partGenero:    { labels: <?php echo jl($partGenero,'genero'); ?>,           data: <?php echo jv($partGenero,'t'); ?> },
    partGrado:     { labels: <?php echo jl($partGrado,'grado_institucion'); ?>, data: <?php echo jv($partGrado,'t'); ?> },
    cursosEstatus: { labels: <?php echo jl($cursosEstatus,'estatus'); ?>,       data: <?php echo jv($cursosEstatus,'t'); ?> },
    cursosModal:   { labels: <?php echo jl($cursosModalidad,'modalidad'); ?>,   data: <?php echo jv($cursosModalidad,'t'); ?> },
    cursosTurno:   { labels: <?php echo jl($cursosTurno,'turno'); ?>,          data: <?php echo jv($cursosTurno,'t'); ?> },
    docTipo:       { labels: <?php echo jl($docTipo,'tipo'); ?>,               data: <?php echo jv($docTipo,'t'); ?> },
    docEstado:     { labels: <?php echo jl($docEstado,'estado'); ?>,           data: <?php echo jv($docEstado,'t'); ?> },
    docGenero:     { labels: <?php echo jl($docGenero,'genero'); ?>,           data: <?php echo jv($docGenero,'t'); ?> },
    partMes:     { data: <?php echo json_encode($partPorMes); ?> },
    cursosMes:   { data: <?php echo json_encode($cursosPorMes); ?> },
    docentesMes: { data: <?php echo json_encode($docentesPorMes); ?> },
    metasMes:    { data: <?php echo json_encode($metasPorMes); ?> },
    cumplimientoAmbito: {
        labels: <?php echo json_encode(array_column($cumplimientoAmbito,'ambito')); ?>,
        data:   <?php echo json_encode(array_map('floatval', array_column($cumplimientoAmbito,'pct_promedio'))); ?>
    },
    ambitoSolicitado: {
        labels: <?php echo json_encode(array_column($ambitoSolicitadoMes,'ambito')); ?>,
        data:   <?php echo json_encode(array_map('intval', array_column($ambitoSolicitadoMes,'inscripciones'))); ?>
    },
    formacionSolicitada: <?php echo $jsFormacionSolicitada; ?>
};
</script>

<script>
/* ══ Sistema de tabs de graficas ══ */
(function () {
    document.querySelectorAll('.chart-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const section = btn.dataset.section;
            const chart   = btn.dataset.chart;
            const nav = btn.closest('.chart-tabs-nav');
            if (nav) nav.querySelectorAll('.chart-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const wrapper = btn.closest('.chart-tabs-wrapper');
            if (wrapper) {
                wrapper.querySelectorAll('.chart-tab-panel').forEach(p => p.classList.remove('active'));
                const targetPanel = wrapper.querySelector('#chart-panel-' + section + '-' + chart);
                if (targetPanel) targetPanel.classList.add('active');
            }
            if (typeof renderGraficasBasicas === 'function') {
                setTimeout(() => renderGraficasBasicas(section + '-' + chart), 80);
            }
        });
    });
})();

/* ══ Navegacion principal ══ */
(function () {
    const navItems = document.querySelectorAll('.dashboard-nav-item');
    const sections = document.querySelectorAll('.dashboard-section');
    const rendered = new Set(['resumen']);
    navItems.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = btn.dataset.section;
            navItems.forEach(n => n.classList.remove('active'));
            btn.classList.add('active');
            sections.forEach(s => s.classList.remove('active'));
            const targetSection = document.getElementById('section-' + target);
            if (targetSection) targetSection.classList.add('active');
            if (!rendered.has(target)) {
                rendered.add(target);
                if (typeof renderGraficasBasicas === 'function') {
                    setTimeout(() => renderGraficasBasicas(target), 80);
                }
            }
            if (target === 'resumen' && typeof renderMiniChartsMensuales === 'function') {
                setTimeout(() => renderMiniChartsMensuales(), 80);
            }
            // Gráficas especiales de la sección Cursos que no pasan por renderGraficasBasicas
            if (target === 'cursos') {
                setTimeout(() => {
                    if (typeof window.buildAmbitoPeriodo === 'function') window.buildAmbitoPeriodo();
                    if (typeof window.buildCursosMensual === 'function')  window.buildCursosMensual();
                }, 120);
            }
        });
    });
})();

function switchPeriodo(tipo) {
    document.getElementById('periodo-mes').style.display  = tipo === 'mes'  ? '' : 'none';
    document.getElementById('periodo-trim').style.display = tipo === 'trim' ? '' : 'none';
    document.getElementById('tab-mes').classList.toggle('active',  tipo === 'mes');
    document.getElementById('tab-trim').classList.toggle('active', tipo === 'trim');
}
</script>

<script src="../../assets/js/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/chart.js"></script>
<script src="../../assets/js/grafica.js"></script>
<script src="../../assets/js/home.js"></script>
<script src="../../assets/js/teclas.js"></script>
<script src="../../assets/js/notifications.js"></script>
<script src="../../assets/js/toastr/toastr.min.js"></script>
<script src="../../assets/js/toastr/toastr-config.js"></script>

<script>
/* ══ Toast de bienvenida ══ */
(function () {
    const userId     = "<?= $_SESSION['id_usuario'] ?? '' ?>";
    const claveLogin = 'bienvenida_' + userId;
    if (!sessionStorage.getItem(claveLogin)) {
        sessionStorage.setItem(claveLogin, '1');
        const nombre = "<?= htmlspecialchars($_SESSION['primer_nombre'] ?? $_SESSION['nombre'] ?? 'Usuario') ?>";
        const rol    = "<?= htmlspecialchars($_SESSION['rol'] ?? '') ?>";
        const div    = document.createElement('div');
        div.id       = 'toast-bienvenida';
        div.innerHTML = `<div style="position:fixed;top:24px;right:24px;z-index:99999;background:#1e293b;color:#f1f5f9;border-left:4px solid #22c55e;border-radius:8px;padding:16px 20px;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,0.35);font-family:sans-serif;font-size:14px;animation:slideIn .4s ease;"><strong style="color:#22c55e;display:block;margin-bottom:4px;">Bienvenido de nuevo!</strong><p style="margin:0 0 4px;font-size:15px;">Hola, <strong>${nombre}</strong></p><p style="margin:0;color:#94a3b8;font-size:12px;">${rol} · Sistema INCES</p><div style="margin-top:12px;height:3px;border-radius:2px;background:#22c55e;animation:reducir 4s linear forwards;"></div></div>`;
        document.body.appendChild(div);
        const style = document.createElement('style');
        style.textContent = `@keyframes slideIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}@keyframes reducir{from{width:100%}to{width:0%}}@keyframes slideOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(40px)}}`;
        document.head.appendChild(style);
        setTimeout(() => {
            const t = document.getElementById('toast-bienvenida');
            if (t) { t.querySelector('div').style.animation = 'slideOut .4s ease forwards'; setTimeout(() => t.remove(), 400); }
        }, 4000);
    }
})();

/* ══ Timer de inactividad ══ */
(function () {
    const INACTIVIDAD_MS = 10 * 60 * 1000;
    const AVISO_MS       =  1 * 60 * 1000;
    const LOGOUT_URL     = '../../controlador/Usuario/cerrar_sesion.php';
    const LOGIN_URL      = 'index.php?sesion=expirada';
    let timerLogout, timerAviso, avisoActivo = false;
    function resetTimer() {
        clearTimeout(timerLogout); clearTimeout(timerAviso);
        if (avisoActivo) { const a = document.getElementById('aviso-inactividad'); if (a) a.remove(); avisoActivo = false; }
        timerAviso  = setTimeout(mostrarAviso,  INACTIVIDAD_MS - AVISO_MS);
        timerLogout = setTimeout(cerrarSesion,  INACTIVIDAD_MS);
    }
    function mostrarAviso() {
        if (document.getElementById('aviso-inactividad')) return;
        avisoActivo = true; let segundos = 60;
        const div = document.createElement('div'); div.id = 'aviso-inactividad';
        div.innerHTML = `<div style="position:fixed;bottom:24px;right:24px;z-index:99999;background:#1e293b;color:#f1f5f9;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,0.35);font-family:sans-serif;font-size:14px;"><strong style="color:#f59e0b;display:block;margin-bottom:6px;">Sesion por expirar</strong><p style="margin:0 0 12px;color:#cbd5e1;">Tu sesion se cerrara en <span id="cuenta-regresiva"><strong>60</strong></span> segundos.</p><button id="btn-seguir-conectado" style="background:#3b82f6;color:#fff;border:none;border-radius:5px;padding:7px 16px;cursor:pointer;font-size:13px;">Seguir conectado</button></div>`;
        document.body.appendChild(div);
        const intervalo = setInterval(() => { segundos--; const el = document.getElementById('cuenta-regresiva'); if (el) el.innerHTML = `<strong>${segundos}</strong>`; if (segundos <= 0) clearInterval(intervalo); }, 1000);
        document.getElementById('btn-seguir-conectado').addEventListener('click', () => { clearInterval(intervalo); resetTimer(); });
    }
    function cerrarSesion() { fetch(LOGOUT_URL, { method:'POST', credentials:'same-origin' }).finally(() => { window.location.replace(LOGIN_URL); }); }
    ['mousemove','mousedown','keydown','scroll','touchstart','click'].forEach(ev => document.addEventListener(ev, resetTimer, { passive:true }));
    resetTimer();
})();
</script>

</body>
</html>

<?php
// ==================== SEGURIDAD DE SESIÓN ====================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php'); exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('../modelo/conexion.php');
$conexion = new Conexion();
$conn = $conexion->getConexion();
$conn->exec("SET NAMES 'utf8mb4'");

// === DATOS DE USUARIO ===
$nombre = htmlspecialchars($_SESSION['nombre'] ?? '');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '');
$rol = htmlspecialchars($_SESSION['rol'] ?? '');
$imagenUsuario = $_SESSION['imagen'] ?? '';
$imagenBase = '../../assets/img/avatar/default-user.png';
$imagen = !empty($imagenUsuario) ? (strpos($imagenUsuario, 'assets/') === 0 ? '../../' . $imagenUsuario : $imagenUsuario) : $imagenBase;

// KPIs básicos
function n($conn, $sql){ return $conn->query($sql)->fetchColumn(); }
$total_participantes = n($conn,"SELECT COUNT(*) FROM tb_participante");
$total_cursos        = n($conn,"SELECT COUNT(*) FROM tb_curso WHERE id_curso!=1");
$total_docentes      = n($conn,"SELECT COUNT(*) FROM tb_docente");

// Panel Participantes
$partEstado   = $conn->query("SELECT estado, COUNT(*) t FROM tb_participante GROUP BY estado ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$partGenero   = $conn->query("SELECT genero, COUNT(*) t FROM tb_participante GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
$partGrado    = $conn->query("SELECT grado_institucion, COUNT(*) t FROM tb_participante GROUP BY grado_institucion ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$ultimosPart  = $conn->query("SELECT primer_nombre, primer_apellido, cedula, estado, genero,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
    fecha_registro
    FROM tb_participante ORDER BY fecha_registro DESC, id_participante DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Panel Cursos
$cursosEstatus   = $conn->query("SELECT e.nombre_estatusCurso estatus,COUNT(*) t FROM tb_curso c JOIN tb_estatuscurso e ON c.id_estatusCurso=e.id_estatusCurso WHERE c.id_curso!=1 GROUP BY e.id_estatusCurso,e.nombre_estatusCurso ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$cursosModalidad = $conn->query("SELECT m.nombre_modalidad modalidad,COUNT(*) t FROM tb_curso c JOIN tb_modalidad m ON c.id_modalidad=m.id_modalidad WHERE c.id_curso!=1 GROUP BY m.id_modalidad,m.nombre_modalidad ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$cursosTurno     = $conn->query("SELECT turno,COUNT(*) t FROM tb_curso WHERE id_curso!=1 GROUP BY turno")->fetchAll(PDO::FETCH_ASSOC);
$topCursos       = $conn->query("SELECT c.nombre_curso,c.num_inscritos,c.max_participantes,c.horas_totales,m.nombre_modalidad,e.nombre_estatusCurso FROM tb_curso c LEFT JOIN tb_modalidad m ON c.id_modalidad=m.id_modalidad JOIN tb_estatuscurso e ON c.id_estatusCurso=e.id_estatusCurso WHERE c.id_curso!=1 ORDER BY c.num_inscritos DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Panel Docentes
$docTipo   = $conn->query("SELECT tipo_docente tipo,COUNT(*) t FROM tb_docente GROUP BY tipo_docente ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$docEstado = $conn->query("SELECT estado_docente estado,COUNT(*) t FROM tb_docente GROUP BY estado_docente ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$docGenero = $conn->query("SELECT genero,COUNT(*) t FROM tb_docente GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
$listaDoc  = $conn->query("SELECT primer_nombre, primer_apellido, cedula, tipo_docente, estado_docente, genero,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
    FROM tb_docente ORDER BY primer_apellido LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Panel En Formación
$formCurso = $conn->query("SELECT c.nombre_curso,COUNT(pc.id_participante) total,SUM(CASE WHEN p.genero='Masculino' THEN 1 ELSE 0 END) masculino,SUM(CASE WHEN p.genero='Femenino' THEN 1 ELSE 0 END) femenino FROM tb_participante_curso pc JOIN tb_curso c ON pc.id_curso=c.id_curso JOIN tb_participante p ON pc.id_participante=p.id_participante WHERE pc.estatus_participante='En formación' GROUP BY c.id_curso,c.nombre_curso ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);

// ── DATOS MENSUALES (año actual) ──
function getMensual($conn, $table, $dateCol, $whereExtra = '') {
    $where = "WHERE YEAR($dateCol) = YEAR(CURDATE())" . ($whereExtra ? " AND $whereExtra" : '');
    $rows = $conn->query("SELECT MONTH($dateCol) mes, COUNT(*) t FROM $table $where GROUP BY MONTH($dateCol) ORDER BY mes")->fetchAll(PDO::FETCH_ASSOC);
    $data = array_fill(0, 12, 0);
    foreach ($rows as $r) $data[(int)$r['mes']-1] = (int)$r['t'];
    return $data;
}
$partPorMes    = getMensual($conn, 'tb_participante', 'fecha_registro');
$cursosPorMes  = getMensual($conn, 'tb_curso', 'fecha_inicio', 'id_curso!=1');
$docentesPorMes = array_fill(0, 12, 0);
$totalDocRows = $conn->query("SELECT MONTH(fecha_nacimiento) mes, COUNT(*) t FROM tb_docente GROUP BY mes ORDER BY mes")->fetchAll(PDO::FETCH_ASSOC);
foreach ($totalDocRows as $r) $docentesPorMes[(int)$r['mes']-1] = (int)$r['t'];

// ── DATOS MENSUALES DE METAS ──
$metasPorMes = array_fill(0, 12, 0);
try {
    $mRows = $conn->query("SELECT MONTH(fecha_inicio) mes, COUNT(*) t FROM tb_metas WHERE estado='Activa' AND fecha_inicio IS NOT NULL GROUP BY MONTH(fecha_inicio) ORDER BY mes")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($mRows as $r) $metasPorMes[(int)$r['mes']-1] = (int)$r['t'];
} catch(Exception $e) {}

// KPIs avanzados
$kUtilizacion = (function($c){ $r=$c->query("SELECT COALESCE(SUM(num_inscritos),0) i,COALESCE(SUM(max_participantes),1) q FROM tb_curso WHERE id_estatusCurso IN(1,2,3,4)")->fetch(PDO::FETCH_ASSOC); $p=$r['q']>0?round($r['i']/$r['q']*100,1):0; return['inscritos'=>$r['i'],'cupos'=>$r['q'],'pct'=>$p]; })($conn);
$kDisponibles = (function($c){ $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn(); $d=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='Disponible'")->fetchColumn(); return['disponibles'=>$d,'total'=>$tot,'pct'=>$tot>0?round($d/$tot*100,1):0]; })($conn);
$kPctForm     = (function($c){ $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn(); $f=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='En formación'")->fetchColumn(); return['en_formacion'=>$f,'total'=>$tot,'pct'=>$tot>0?round($f/$tot*100,1):0]; })($conn);
$kMetas       = $conn->query("SELECT COUNT(*) total_metas,SUM(CASE WHEN estado_cumplimiento='Cumplida' THEN 1 ELSE 0 END) cumplidas,COALESCE(ROUND(AVG(porcentaje_cumplimiento),1),0) promedio_pct FROM tb_metas WHERE estado='Activa'")->fetch(PDO::FETCH_ASSOC);
$kPromedio    = $conn->query("SELECT COUNT(*) total_cursos,COALESCE(ROUND(AVG(num_inscritos),1),0) promedio FROM tb_curso WHERE id_estatusCurso IN(1,2,3,4)")->fetch(PDO::FETCH_ASSOC);
$kAsignacion  = (function($c){ $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn(); $a=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='Asignado'")->fetchColumn(); $f=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='En formación'")->fetchColumn(); $act=$a+$f; return['asignados'=>$a,'en_formacion'=>$f,'activos'=>$act,'total'=>$tot,'pct'=>$tot>0?round($act/$tot*100,1):0]; })($conn);

$cursosDetalle = $conn->query("SELECT c.nombre_curso,c.num_inscritos,c.max_participantes,(c.max_participantes - c.num_inscritos) AS cupos_disp,ROUND(c.num_inscritos/NULLIF(c.max_participantes,0)*100,1) uso_pct,e.nombre_estatusCurso FROM tb_curso c JOIN tb_estatuscurso e ON c.id_estatusCurso=e.id_estatusCurso WHERE c.id_estatusCurso IN(1,2,3,4) ORDER BY uso_pct DESC")->fetchAll(PDO::FETCH_ASSOC);
$partDisponibles = $conn->query("SELECT primer_nombre,primer_apellido,cedula,genero,grado_institucion,fecha_registro FROM tb_participante WHERE estado='Disponible' ORDER BY fecha_registro DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);

$metasDetalle = [];
try {
    $metasDetalle = $conn->query("SELECT m.*, a.nombre_ambito, MONTH(m.fecha_inicio) AS mes_meta FROM tb_metas m LEFT JOIN tb_ambito a ON m.id_ambito=a.id_ambito WHERE m.estado='Activa' ORDER BY m.porcentaje_cumplimiento DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    try { $metasDetalle = $conn->query("SELECT m.*, a.nombre_ambito, NULL AS mes_meta FROM tb_metas m LEFT JOIN tb_ambito a ON m.id_ambito=a.id_ambito WHERE m.estado='Activa' ORDER BY m.porcentaje_cumplimiento DESC")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e2) {}
}

$distEstados = $conn->query("SELECT estado,COUNT(*) total FROM tb_participante GROUP BY estado ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);

// Semáforos
function sem($pct,$lo,$hi,$inv=false){if(!$inv){return $pct>=$hi?'verde':($pct>=$lo?'amarillo':'rojo');}else{return $pct<=$lo?'verde':($pct<=$hi?'amarillo':'rojo');}}
function semL($c){return['verde'=>'Óptimo','amarillo'=>'Atención','rojo'=>'Crítico'][$c];}
$s_util=sem($kUtilizacion['pct'],70,89); $s_disp=sem($kDisponibles['pct'],20,29,true);
$s_form=sem($kPctForm['pct'],60,79); $s_meta=sem($kMetas['promedio_pct']??0,80,95);
$s_asig=sem($kAsignacion['pct'],70,84);
$promPct=$kPromedio['total_cursos']>0?round($kPromedio['promedio']/30*100,1):0; $s_prom=sem($promPct,60,84);

function jl($arr,$k){return json_encode(array_column($arr,$k));}
function jv($arr,$k){return json_encode(array_map('intval',array_column($arr,$k)));}
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
    <style>
        html { overflow-y: scroll; }
        tr.part-row-link:hover,
        tr.curso-row-link:hover,
        tr.docente-row-link:hover { outline: 2px solid rgba(99,102,241,.3); outline-offset: -1px; }
    </style>
</head>
<body>
<div class="wrapper">

<aside id="sidebar" class="expand">
    <div class="d-flex">
        <button id="toggle-btn" type="button" class="btn btn-dark d-flex align-items-center justify-content-center p-2 me-2 mt-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>
        </button>
        <div class="sidebar-logo"><img src="../../assets/img/inces-logo.png" alt="inces-logo"></div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item"><a href="inicio.php" class="sidebar-link active-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg><span>Inicio</span></a></li>
        <li class="sidebar-item"><a href="participante.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg><span>Participantes</span></a></li>
        <li class="sidebar-item"><a href="cursos.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg><span>Cursos</span></a></li>
        <li class="sidebar-item"><a href="docente.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-workspace" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg><span>Docentes</span></a></li>
        <li id="sidebar-usuario" class="sidebar-item"><a href="usuario.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-vcard-fill" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg><span>Usuarios</span></a></li>
        <li class="sidebar-item dropdown">
            <a href="#" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16"><path d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5"/></svg><span>Programacion</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill flechita" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg></a>
            <ul class="sub-menu"><li><a href="#" class="sidebar-link sub-menu-link">Curso a curso</a></li><li><a href="metas.php" class="sidebar-link sub-menu-link">Metas</a></li></ul>
        </li>
        <li class="sidebar-item"><a href="reporte.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-fill" viewBox="0 0 16 16"><path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg><span>Reportes</span></a></li>
        <li class="sidebar-item">
                    <a href="Historial.php" class="sidebar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                            <span>Historial</span>
                    </a>
            </li>
    </ul>
</aside>

<div class="main">
    <?php include 'partials/header.php'; ?>
    <div class="home-content px-3 pb-5">

        <!-- ════ 4 TARJETAS KPI BÁSICAS ════ -->
        <div class="row mt-4 g-3">
            <?php
            $basicCards = [
                ['id'=>'participantes','valor'=>$total_participantes,'label'=>'Participantes Totales','iconBg'=>'#e0f9fd','iconColor'=>'#00c8e0','chartId'=>'chartPartMes',
                 'svg'=>'<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>'],
                ['id'=>'cursos','valor'=>$total_cursos,'label'=>'Total Cursos','iconBg'=>'#e2fce8','iconColor'=>'#28c76f','chartId'=>'chartCursosMes',
                 'svg'=>'<path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>'],
                ['id'=>'docentes','valor'=>$total_docentes,'label'=>'Total Docentes','iconBg'=>'#ede8ff','iconColor'=>'#7367f0','chartId'=>'chartDocentesMes',
                 'svg'=>'<path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>'],
            ];
            foreach ($basicCards as $c): ?>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-basic shadow-sm kpi-basic-btn" role="button" tabindex="0" data-kpi-basic="<?php echo $c['id']; ?>">
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
                    <div class="kpi-basic-footer">
                        <span class="kpi-adv-toggle-badge" id="badge-basic-<?php echo $c['id']; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>
                            Ver desglose
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- TARJETA METAS -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-basic shadow-sm kpi-basic-btn" role="button" tabindex="0" data-kpi-basic="metas">
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
                        <div class="kpi-metas-barra-fondo"><div class="kpi-metas-barra" style="width:<?php echo min((float)($kMetas['promedio_pct'] ?? 0), 100); ?>%;"></div></div>
                        <div class="kpi-metas-progress-stats">
                            <span class="cumplidas-val">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#28c76f" viewBox="0 0 16 16" style="margin-right:.2rem;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                                <?php echo (int)($kMetas['cumplidas'] ?? 0); ?> cumplidas
                            </span>
                            <span class="semaforo-badge semaforo-<?php echo $s_meta; ?>"><?php echo semL($s_meta); ?></span>
                        </div>
                    </div>
                    <div class="kpi-basic-footer">
                        <span class="kpi-adv-toggle-badge" id="badge-basic-metas">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>
                            Ver desglose
                        </span>
                    </div>
                </div>
            </div>
        </div><!-- /row tarjetas -->


        <!-- ═══ PANEL: PARTICIPANTES ═══ -->
        <div class="kpi-panel-wrapper" id="panel-basic-participantes">
            <div class="kpi-panel-inner" style="border-top-color:#00c8e0;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#00afc5;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                        Análisis de Participantes
                    </h6>
                    <small class="text-muted">Datos en tiempo real · Semáforos activos</small>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#6366f1;"></span>
                        <span class="panel-section-label">Indicadores de Desempeño</span>
                        <small class="text-muted ms-auto">Semáforos en tiempo real</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_disp; ?>">
                                <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Disponibles sin asignar</span><span class="semaforo-badge semaforo-<?php echo $s_disp; ?>"><?php echo semL($s_disp); ?></span></div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_disp; ?>"><?php echo $kDisponibles['pct']; ?>%</div>
                                <div class="panel-mini-kpi-sub"><?php echo $kDisponibles['disponibles']; ?> de <?php echo $kDisponibles['total']; ?> participantes</div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_disp; ?>" style="width:<?php echo min($kDisponibles['pct'],100); ?>%"></div></div>
                                <div class="panel-mini-kpi-meta">Meta: &lt; 20%</div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_form; ?>">
                                <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">En formación actualmente</span><span class="semaforo-badge semaforo-<?php echo $s_form; ?>"><?php echo semL($s_form); ?></span></div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_form; ?>"><?php echo $kPctForm['pct']; ?>%</div>
                                <div class="panel-mini-kpi-sub"><?php echo $kPctForm['en_formacion']; ?> activos de <?php echo $kPctForm['total']; ?></div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_form; ?>" style="width:<?php echo min($kPctForm['pct'],100); ?>%"></div></div>
                                <div class="panel-mini-kpi-meta">Meta: ≥ 80%</div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_prom; ?>">
                                <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Prom. inscritos por formación</span><span class="semaforo-badge semaforo-<?php echo $s_prom; ?>"><?php echo semL($s_prom); ?></span></div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_prom; ?>"><?php echo $kPromedio['promedio']; ?></div>
                                <div class="panel-mini-kpi-sub"><?php echo $kPromedio['total_cursos']; ?> cursos activos</div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_prom; ?>" style="width:<?php echo min($promPct,100); ?>%"></div></div>
                                <div class="panel-mini-kpi-meta">Meta: ≥ 90% de ocupación</div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_asig; ?>">
                                <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Índice de asignación efectiva</span><span class="semaforo-badge semaforo-<?php echo $s_asig; ?>"><?php echo semL($s_asig); ?></span></div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_asig; ?>"><?php echo $kAsignacion['pct']; ?>%</div>
                                <div class="panel-mini-kpi-sub"><?php echo $kAsignacion['activos']; ?> activos de <?php echo $kAsignacion['total']; ?></div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_asig; ?>" style="width:<?php echo min($kAsignacion['pct'],100); ?>%"></div></div>
                                <div class="panel-mini-kpi-meta">Meta: ≥ 85%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header"><span class="panel-section-dot" style="background:#00c8e0;"></span><span class="panel-section-label">Distribución General</span></div>
                    <div class="row g-3">
                        <div class="col-md-4"><p class="kpi-chart-label">Por Estado</p><div class="kpi-chart-wrap"><canvas id="chartPartEstado"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Género</p><div class="kpi-chart-wrap"><canvas id="chartPartGenero"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Grado</p><div class="kpi-chart-wrap"><canvas id="chartPartGrado"></canvas></div></div>
                    </div>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#8b5cf6;"></span>
                        <span class="panel-section-label">Distribución por Estado</span>
                        <small class="text-muted ms-auto">Índice efectivo = Asignados + En formación / Total</small>
                    </div>
                    <div class="row g-3">
                    <?php
                    $colE2=['En formación'=>['#28c76f','#d4f4e3'],'Asignado'=>['#0ea5e9','#ddf1fb'],'Disponible'=>['#f59e0b','#fef3c7'],'Inactivo'=>['#ef4444','#fee2e2']];
                    $tG2=array_sum(array_column($distEstados,'total'));
                    foreach($distEstados as $est):
                        $ePct2=$tG2>0?round($est['total']/$tG2*100,1):0;
                        $col2=$colE2[$est['estado']]??['#6c757d','#f0f0f0'];?>
                        <div class="col-6 col-md-3">
                            <div class="kpi-estado-card" style="border-left:4px solid <?php echo $col2[0];?>;background:<?php echo $col2[1];?>;">
                                <p class="kpi-estado-valor" style="color:<?php echo $col2[0];?>;"><?php echo $est['total'];?></p>
                                <p class="kpi-estado-label"><?php echo htmlspecialchars($est['estado']);?></p>
                                <p class="kpi-estado-pct" style="color:<?php echo $col2[0];?>;"><?php echo $ePct2;?>%</p>
                                <div class="kpi-adv-barra-fondo mt-1" style="height:6px;"><div style="width:<?php echo $ePct2;?>%;height:6px;background:<?php echo $col2[0];?>;border-radius:3px;"></div></div>
                            </div>
                        </div>
                    <?php endforeach;?>
                    </div>
                </div>

                <!-- En Formación por Curso — clickeable → cursos.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#28c76f;"></span>
                        <span class="panel-section-label">En Formación por Curso</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el curso</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Curso</th><th class="text-center">Total</th><th class="text-center">Masculino</th><th class="text-center">Femenino</th><th style="min-width:160px;">Distribución</th></tr></thead>
                            <tbody>
                            <?php foreach($formCurso as $f): $pM=$f['total']>0?round($f['masculino']/$f['total']*100):0; ?>
                                <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($f['nombre_curso']); ?>" title="Ver curso en módulo de Cursos" style="cursor:pointer;">
                                    <td class="fw-semibold"><?php echo htmlspecialchars($f['nombre_curso']);?></td>
                                    <td class="text-center fw-bold"><?php echo $f['total'];?></td>
                                    <td class="text-center" style="color:#0dcaf0;"><?php echo $f['masculino'];?></td>
                                    <td class="text-center" style="color:#e83e8c;"><?php echo $f['femenino'];?></td>
                                    <td><div class="d-flex rounded overflow-hidden" style="height:12px;"><div style="width:<?php echo $pM;?>%;background:#0dcaf0;"></div><div style="width:<?php echo 100-$pM;?>%;background:#e83e8c;"></div></div></td>
                                </tr>
                            <?php endforeach; if(empty($formCurso)):?><tr><td colspan="5" class="text-center text-muted py-3">No hay participantes en formación.</td></tr><?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Disponibles sin asignación — clickeable → participante.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#f59e0b;"></span>
                        <span class="panel-section-label">Disponibles Sin Asignación</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
                    </div>
                    <?php if(empty($partDisponibles)):?>
                        <p class="text-success py-2 fw-semibold mb-0" style="font-size:.85rem;">✓ No hay participantes disponibles sin asignar.</p>
                    <?php else:?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Nombre</th><th>Cédula</th><th>Género</th><th>Grado</th><th>Fecha Registro</th></tr></thead>
                            <tbody>
                            <?php foreach($partDisponibles as $p):?>
                                <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" title="Ir al perfil de <?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?>" style="cursor:pointer;">
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']);?></td>
                                    <td><?php echo $p['cedula'];?></td>
                                    <td><?php echo $p['genero'];?></td>
                                    <td><?php echo htmlspecialchars($p['grado_institucion']);?></td>
                                    <td><?php echo $p['fecha_registro'];?></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif;?>
                </div>

                <!-- Últimos 5 registrados — clickeable → participante.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#00c8e0;"></span>
                        <span class="panel-section-label">Últimos 5 Registrados</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Nombre</th><th>Cédula</th><th>Estado</th><th>Género</th><th class="text-center">Edad</th><th>Fecha Registro</th></tr></thead>
                            <tbody>
                            <?php foreach ($ultimosPart as $p):
                                $ce=['En formación'=>'success','Disponible'=>'warning','Asignado'=>'info','Inactivo'=>'danger'][$p['estado']]??'secondary'; ?>
                                <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" title="Ir al perfil de <?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?>" style="cursor:pointer;">
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                    <td><?php echo $p['cedula']; ?></td>
                                    <td><span class="badge bg-<?php echo $ce; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $p['estado']; ?></span></td>
                                    <td><?php echo $p['genero']; ?></td>
                                    <td class="text-center fw-semibold"><?php echo isset($p['edad']) ? $p['edad'].' años' : '—'; ?></td>
                                    <td><?php echo $p['fecha_registro']; ?></td>
                                </tr>
                            <?php endforeach; if(empty($ultimosPart)):?><tr><td colspan="6" class="text-center text-muted py-3">Sin registros.</td></tr><?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ PANEL: CURSOS ═══ -->
        <div class="kpi-panel-wrapper" id="panel-basic-cursos">
            <div class="kpi-panel-inner" style="border-top-color:#28c76f;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#28a745;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                        Análisis de Cursos
                    </h6>
                    <small class="text-muted">Distribución actual del catálogo de formaciones</small>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header"><span class="panel-section-dot" style="background:#28c76f;"></span><span class="panel-section-label">Distribución General</span></div>
                    <div class="row g-3">
                        <div class="col-md-4"><p class="kpi-chart-label">Por Estatus</p><div class="kpi-chart-wrap"><canvas id="chartCursosEstatus"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Modalidad</p><div class="kpi-chart-wrap"><canvas id="chartCursosModalidad"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Turno</p><div class="kpi-chart-wrap"><canvas id="chartCursosTurno"></canvas></div></div>
                    </div>
                </div>

                <!-- Top 5 — clickeable → cursos.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#0ea5e9;"></span>
                        <span class="panel-section-label">Top 5 Cursos por Inscritos</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el curso</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Curso</th><th class="text-center">Inscritos</th><th class="text-center">Cupo</th><th class="text-center">Horas</th><th class="text-center">Modalidad</th><th style="min-width:140px;">Ocupación</th></tr></thead>
                            <tbody>
                            <?php foreach ($topCursos as $tc):
                                $ocPct=$tc['max_participantes']>0?round($tc['num_inscritos']/$tc['max_participantes']*100,1):0;
                                $ocSem=($ocPct>=90)?'verde':(($ocPct>=60)?'amarillo':'rojo'); ?>
                                <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($tc['nombre_curso']); ?>" title="Ver curso en módulo de Cursos" style="cursor:pointer;">
                                    <td class="fw-semibold" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($tc['nombre_curso']); ?>"><?php echo htmlspecialchars($tc['nombre_curso']); ?></td>
                                    <td class="text-center"><?php echo $tc['num_inscritos']; ?></td>
                                    <td class="text-center"><?php echo $tc['max_participantes']; ?></td>
                                    <td class="text-center"><?php echo $tc['horas_totales']??'—'; ?></td>
                                    <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($tc['nombre_modalidad']??'—'); ?></span></td>
                                    <td><div class="d-flex align-items-center gap-2"><div class="kpi-adv-barra-fondo flex-grow-1" style="height:9px;"><div class="kpi-adv-barra semaforo-fill-<?php echo $ocSem; ?>" style="width:<?php echo min($ocPct,100); ?>%;height:9px;"></div></div><span class="small semaforo-text-<?php echo $ocSem; ?>" style="min-width:36px;"><?php echo $ocPct; ?>%</span></div></td>
                                </tr>
                            <?php endforeach; if(empty($topCursos)):?><tr><td colspan="6" class="text-center text-muted py-3">Sin cursos.</td></tr><?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Utilización de cupos — clickeable → cursos.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#f59e0b;"></span>
                        <span class="panel-section-label">Utilización de Cupos — Formaciones Activas</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_util; ?> ms-auto" style="font-size:.72rem;">Global: <?php echo $kUtilizacion['pct']; ?>% · <?php echo semL($s_util); ?></span>
                    </div>
                    <p class="text-muted mb-2" style="font-size:.73rem;">
                        Meta ≥85% · <span class="semaforo-text-rojo fw-semibold">Rojo &lt;70%</span> · <span class="semaforo-text-amarillo fw-semibold">Amarillo 70–89%</span> · <span class="semaforo-text-verde fw-semibold">Verde ≥90%</span>
                        · <em>Haz clic en una fila para ver el curso</em>
                    </p>
                    <?php if(empty($cursosDetalle)):?>
                        <p class="text-muted py-3 text-center">No hay cursos activos.</p>
                    <?php else:?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Curso</th><th class="text-center">Inscritos</th><th class="text-center">Cupos Disp.</th><th class="text-center">Cupo Máx.</th><th style="min-width:180px;">Ocupación</th><th class="text-center">Estatus</th></tr></thead>
                            <tbody>
                            <?php foreach($cursosDetalle as $c):
                                $uPct=$c['uso_pct']??0; $uSem=($uPct>=90)?'verde':(($uPct>=70)?'amarillo':'rojo');
                                $disp=max(0,(int)$c['cupos_disp']); $dispColor=($disp<=0)?'#ef4444':(($disp<=5)?'#f59e0b':'#28a745');
                            ?>
                                <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($c['nombre_curso']); ?>" title="Ver curso en módulo de Cursos" style="cursor:pointer;">
                                    <td class="fw-semibold" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($c['nombre_curso']);?></td>
                                    <td class="text-center"><?php echo $c['num_inscritos'];?></td>
                                    <td class="text-center fw-semibold" style="color:<?php echo $dispColor;?>;"><?php echo $disp;?></td>
                                    <td class="text-center"><?php echo $c['max_participantes'];?></td>
                                    <td><div class="d-flex align-items-center gap-2"><div class="kpi-adv-barra-fondo flex-grow-1" style="height:10px;"><div class="kpi-adv-barra semaforo-fill-<?php echo $uSem;?>" style="width:<?php echo min($uPct,100);?>%;height:10px;"></div></div><span class="small fw-semibold semaforo-text-<?php echo $uSem;?>" style="min-width:42px;"><?php echo $uPct;?>%</span></div></td>
                                    <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($c['nombre_estatusCurso']);?></span></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif;?>
                </div>

            </div>
        </div>

        <!-- ═══ PANEL: DOCENTES ═══ -->
        <div class="kpi-panel-wrapper" id="panel-basic-docentes">
            <div class="kpi-panel-inner" style="border-top-color:#7367f0;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#7367f0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg>
                        Análisis de Docentes
                    </h6>
                    <small class="text-muted">Distribución del plantel docente activo</small>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header"><span class="panel-section-dot" style="background:#7367f0;"></span><span class="panel-section-label">Distribución General</span></div>
                    <div class="row g-3">
                        <div class="col-md-4"><p class="kpi-chart-label">Por Tipo</p><div class="kpi-chart-wrap"><canvas id="chartDocTipo"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Estado</p><div class="kpi-chart-wrap"><canvas id="chartDocEstado"></canvas></div></div>
                        <div class="col-md-4"><p class="kpi-chart-label">Por Género</p><div class="kpi-chart-wrap"><canvas id="chartDocGenero"></canvas></div></div>
                    </div>
                </div>

                <!-- Listado de Docentes — clickeable → docente.php -->
                <div class="panel-section">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#7367f0;"></span>
                        <span class="panel-section-label">Listado de Docentes</span>
                        <small class="text-muted ms-auto">Primeros 10 · Haz clic en una fila para ver el docente</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Nombre</th><th>Cédula</th><th class="text-center">Tipo</th><th class="text-center">Estado</th><th class="text-center">Género</th><th class="text-center">Edad</th></tr></thead>
                            <tbody>
                            <?php foreach ($listaDoc as $d):
                                $cd=['Disponible'=>'success','En formación'=>'warning','Asignado'=>'info','Inactivo'=>'danger'][$d['estado_docente']]??'secondary'; ?>
                                <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" title="Ir al perfil de <?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?>" style="cursor:pointer;">
                                    <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?></td>
                                    <td><?php echo $d['cedula']; ?></td>
                                    <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.71rem;"><?php echo $d['tipo_docente']; ?></span></td>
                                    <td class="text-center"><span class="badge bg-<?php echo $cd; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $d['estado_docente']; ?></span></td>
                                    <td class="text-center"><?php echo $d['genero']; ?></td>
                                    <td class="text-center fw-semibold"><?php echo isset($d['edad']) ? $d['edad'].' años' : '—'; ?></td>
                                </tr>
                            <?php endforeach; if(empty($listaDoc)):?><tr><td colspan="6" class="text-center text-muted py-3">Sin docentes.</td></tr><?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ PANEL: METAS ═══ -->
        <div class="kpi-panel-wrapper" id="panel-basic-metas">
            <div class="kpi-panel-inner" style="border-top-color:#ef4444;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                        Progresión de Metas Activas
                    </h6>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex gap-3">
                            <span style="font-size:.78rem;color:#64748b;"><strong style="color:#1e293b;"><?php echo (int)($kMetas['total_metas']??0); ?></strong> activas</span>
                            <span style="font-size:.78rem;color:#64748b;"><strong style="color:#28c76f;"><?php echo (int)($kMetas['cumplidas']??0); ?></strong> cumplidas</span>
                            <span class="semaforo-badge semaforo-<?php echo $s_meta; ?>"><?php echo $kMetas['promedio_pct']??0; ?>% promedio · <?php echo semL($s_meta); ?></span>
                        </div>
                        <small class="text-muted">Año <?php echo date('Y'); ?></small>
                    </div>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header"><span class="panel-section-dot" style="background:#ef4444;"></span><span class="panel-section-label">Metas por Mes</span><small class="text-muted ms-auto">Haz clic en un mes para filtrar la tabla</small></div>
                    <div class="kpi-metas-calendar-wrap"><canvas id="chartMetasMes"></canvas></div>
                </div>

                <div class="panel-section">
                    <div class="panel-section-header"><span class="panel-section-dot" style="background:#ef4444;"></span><span class="panel-section-label">Detalle de Metas</span><small class="text-muted ms-auto">Meta ≥95% verde · ≥80% atención · &lt;80% crítico</small></div>

                    <div class="metas-filter-toolbar">
                        <span id="metas-mes-activo-wrap" class="metas-mes-activo-label d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                            Mes: <strong id="metas-mes-label">—</strong>
                        </span>
                        <button id="btn-metas-ver-todos" class="btn-metas-ver-todos d-none" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
                            Ver todos los meses
                        </button>
                    </div>

                    <div id="metas-sin-datos-mes" class="metas-sin-datos-mes" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                        <p>No hay metas registradas para este mes.</p>
                    </div>

                    <?php if(empty($metasDetalle)):?>
                        <div class="text-center py-4 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#ced4da" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                            <p class="mt-2">No hay metas activas registradas.</p>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" id="tabla-metas-panel">
                            <thead class="table-light">
                                <tr><th>Ámbito</th><th class="text-center">Tipo</th><th class="text-center">Objetivo</th><th style="min-width:200px;">Progreso</th><th class="text-center">Estado</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach($metasDetalle as $m):
                                $mPct = $m['porcentaje_cumplimiento'] ?? 0;
                                $mSem = ($mPct >= 95) ? 'verde' : (($mPct >= 80) ? 'amarillo' : 'rojo');
                                $cumplida = ($m['estado_cumplimiento'] === 'Cumplida');
                                $mesMeta  = isset($m['mes_meta']) && $m['mes_meta'] !== null ? (int)$m['mes_meta'] : 0;
                            ?>
                                <tr data-mes-meta="<?php echo $mesMeta; ?>"
                                    data-id="<?php echo $m['id_meta']; ?>"
                                    class="metas-full-row meta-row-link<?php echo $cumplida ? ' metas-row-cumplida' : ''; ?>"
                                    title="Ver esta meta en el módulo de Metas"
                                    style="cursor:pointer;">
                                    <td><div class="metas-ambito-cell"><div class="metas-ambito-dot metas-dot-<?php echo $mSem; ?>"></div><span class="fw-semibold"><?php echo htmlspecialchars($m['nombre_ambito'] ?? 'General'); ?></span></div></td>
                                    <td class="text-center"><span class="metas-tipo-badge"><?php echo htmlspecialchars($m['tipo_objetivo']); ?></span></td>
                                    <td class="text-center fw-bold" style="font-size:1rem;"><?php echo $m['meta_objetivo']; ?></td>
                                    <td>
                                        <div class="metas-progress-cell">
                                            <div class="metas-progress-track"><div class="metas-progress-fill semaforo-fill-<?php echo $mSem; ?>" style="width:<?php echo min($mPct,100); ?>%;"></div></div>
                                            <span class="metas-progress-pct semaforo-text-<?php echo $mSem; ?>"><?php echo $mPct; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="semaforo-badge semaforo-<?php echo $cumplida ? 'verde' : $mSem; ?>"><?php echo $cumplida ? '✓ ' : ''; ?><?php echo $m['estado_cumplimiento']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════
                    SECCIÓN: HISTORIAL DE ACTIVIDAD + CALENDARIO
            ═══════════════════════════════════════════════════════════════ -->

<div class="row g-3 mt-2" id="section-historial-calendario">

    <!-- ── HISTORIAL DE ACTIVIDAD (col pequeña) ── -->
    <div class="col-xl-4 col-lg-5 col-md-12">
        <div class="hc-card hc-historial shadow-sm h-100">
            <div class="hc-card-header">
                <div class="hc-header-icon" style="background:#ede8ff;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#7367f0" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8"/>
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
                <div class="hc-header-text">
                    <h6 class="hc-title">Historial de Actividad</h6>
                    <span class="hc-subtitle">Acciones recientes del sistema</span>
                </div>
                <span class="hc-badge-nuevo">Próximamente</span>
            </div>

            <!-- Estado vacío — placeholder hasta implementar la lógica -->
            <div class="hc-empty-state" id="historial-empty">
                <div class="hc-empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="#c4b5fd" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8"/>
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
                <p class="hc-empty-title">Sin actividad registrada</p>
                <p class="hc-empty-desc">Aquí aparecerán las acciones<br>realizadas en el sistema.</p>
            </div>

            <!-- Lista de eventos — oculta hasta que haya datos -->
            <!-- 
            ESTRUCTURA DE DATOS A IMPLEMENTAR:
            <ul class="hc-timeline" id="historial-lista" style="display:none;">
                <li class="hc-timeline-item hc-ev-{tipo}">
                    <div class="hc-tl-dot"></div>
                    <div class="hc-tl-body">
                        <span class="hc-tl-accion">{accion}</span>
                        <span class="hc-tl-meta">{usuario} · {fecha}</span>
                    </div>
                </li>
            </ul>
            TIPOS disponibles: hc-ev-add | hc-ev-edit | hc-ev-delete | hc-ev-login
            -->

            <div class="hc-card-footer">
                <button class="hc-btn-ver-todo" type="button" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                    Ver historial completo
                </button>
            </div>
        </div>
    </div>

        <!-- ── CALENDARIO FUNCIONAL (col grande) ── -->
        <div class="col-xl-8 col-lg-7 col-md-12">
            <div class="hc-card hc-calendario shadow-sm h-100">
                <div class="hc-card-header">
                    <div class="hc-header-icon" style="background:#ddf1fb;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#0ea5e9" viewBox="0 0 16 16">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                    </div>
                    <div class="hc-header-text">
                        <h6 class="hc-title">Calendario</h6>
                        <span class="hc-subtitle" id="cal-mes-anio-label">—</span>
                    </div>
                    <div class="cal-nav-btns">
                        <button class="cal-nav-btn" id="cal-prev" type="button" title="Mes anterior">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                            </svg>
                        </button>
                        <button class="cal-nav-btn cal-btn-hoy" id="cal-hoy" type="button" title="Ir a hoy">Hoy</button>
                        <button class="cal-nav-btn" id="cal-next" type="button" title="Mes siguiente">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Grid cabecera días de semana -->
                <div class="cal-weekdays">
                    <span>Dom</span><span>Lun</span><span>Mar</span>
                    <span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span>
                </div>

                <!-- Grid de días — generado por JS -->
                <div class="cal-grid" id="cal-grid"></div>

                <!-- Leyenda / tooltip de día seleccionado -->
                <div class="cal-footer">
                    <div class="cal-leyenda">
                        <span class="cal-leyenda-item">
                            <span class="cal-leyenda-dot cal-dot-hoy"></span>Hoy
                        </span>
                        <span class="cal-leyenda-item">
                            <span class="cal-leyenda-dot cal-dot-sel"></span>Seleccionado
                        </span>
                        <span class="cal-leyenda-item">
                            <span class="cal-leyenda-dot cal-dot-fds"></span>Fin de semana
                        </span>
                    </div>
                    <span class="cal-dia-seleccionado-label" id="cal-sel-label"></span>
                </div>
            </div>
        </div>

    </div>
    <!-- /section-historial-calendario -->

    </div><!-- /home-content -->
</div><!-- /main -->
</div><!-- /wrapper -->

<script>
const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";
const MESES_LABELS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

window.HOME_CHARTS = {
    partEstado:    { labels: <?php echo jl($partEstado,'estado'); ?>,    data: <?php echo jv($partEstado,'t'); ?> },
    partGenero:    { labels: <?php echo jl($partGenero,'genero'); ?>,    data: <?php echo jv($partGenero,'t'); ?> },
    partGrado:     { labels: <?php echo jl($partGrado,'grado_institucion'); ?>, data: <?php echo jv($partGrado,'t'); ?> },
    cursosEstatus: { labels: <?php echo jl($cursosEstatus,'estatus'); ?>, data: <?php echo jv($cursosEstatus,'t'); ?> },
    cursosModal:   { labels: <?php echo jl($cursosModalidad,'modalidad'); ?>, data: <?php echo jv($cursosModalidad,'t'); ?> },
    cursosTurno:   { labels: <?php echo jl($cursosTurno,'turno'); ?>,    data: <?php echo jv($cursosTurno,'t'); ?> },
    docTipo:       { labels: <?php echo jl($docTipo,'tipo'); ?>,         data: <?php echo jv($docTipo,'t'); ?> },
    docEstado:     { labels: <?php echo jl($docEstado,'estado'); ?>,     data: <?php echo jv($docEstado,'t'); ?> },
    docGenero:     { labels: <?php echo jl($docGenero,'genero'); ?>,     data: <?php echo jv($docGenero,'t'); ?> },
    partMes:     { data: <?php echo json_encode($partPorMes); ?> },
    cursosMes:   { data: <?php echo json_encode($cursosPorMes); ?> },
    docentesMes: { data: <?php echo json_encode($docentesPorMes); ?> },
    metasMes:    { data: <?php echo json_encode($metasPorMes); ?> }
};
</script>

<script src="../../assets/js/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/chart.js"></script>
<script src="../../assets/js/grafica.js"></script>
<script src="../../assets/js/home.js"></script>
<script src="../../assets/js/notifications.js"></script>
<script src="../../assets/js/toastr/toastr.min.js"></script>
<script src="../../assets/js/toastr/toastr-config.js"></script>
</body>
</html>
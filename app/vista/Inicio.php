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
// CORRECCIÓN: "En sistema" se trata igual que "Inactivo" (participante sin curso activo)
$partEstado   = $conn->query("
    SELECT
        CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
             ELSE estado END AS estado,
        COUNT(*) t
    FROM tb_participante
    GROUP BY CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
                  ELSE estado END
    ORDER BY t DESC
")->fetchAll(PDO::FETCH_ASSOC);

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

// ── Top 5 cursos del mes actual con tendencia + columnas masculino/femenino ──
$topCursosMesActual = $conn->query("
    SELECT
        c.nombre_curso,
        c.max_participantes,
        c.horas_totales,
        m.nombre_modalidad,
        e.nombre_estatusCurso,
        c.num_inscritos,
        (c.max_participantes - c.num_inscritos) AS cupos_disp,
        ROUND(c.num_inscritos/NULLIF(c.max_participantes,0)*100,1) AS uso_pct,
        COUNT(pc.id_participante) AS inscritos_mes,
        SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
        SUM(CASE WHEN p.genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino
    FROM tb_curso c
    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
    JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
    LEFT JOIN tb_participante_curso pc
        ON pc.id_curso = c.id_curso
        AND MONTH(pc.fecha_inscripcion) = MONTH(CURDATE())
        AND YEAR(pc.fecha_inscripcion)  = YEAR(CURDATE())
    LEFT JOIN tb_participante p ON pc.id_participante = p.id_participante
    WHERE c.id_curso != 1
    GROUP BY c.id_curso, c.nombre_curso, c.max_participantes, c.horas_totales,
             m.nombre_modalidad, e.nombre_estatusCurso, c.num_inscritos
    ORDER BY inscritos_mes DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$topCursosMesAnterior = [];
if (!empty($topCursosMesActual)) {
    $nombresTop = array_column($topCursosMesActual, 'nombre_curso');
    $inParams   = implode(',', array_fill(0, count($nombresTop), '?'));
    $stmtAnterior = $conn->prepare("
        SELECT c.nombre_curso, COUNT(pc.id_participante) AS inscritos_anterior
        FROM tb_curso c
        LEFT JOIN tb_participante_curso pc
            ON pc.id_curso = c.id_curso
            AND MONTH(pc.fecha_inscripcion) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
            AND YEAR(pc.fecha_inscripcion)  = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
        WHERE c.nombre_curso IN ($inParams)
        GROUP BY c.nombre_curso
    ");
    $stmtAnterior->execute($nombresTop);
    foreach ($stmtAnterior->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $topCursosMesAnterior[$row['nombre_curso']] = (int)$row['inscritos_anterior'];
    }
}

foreach ($topCursosMesActual as &$tc) {
    $anterior = $topCursosMesAnterior[$tc['nombre_curso']] ?? 0;
    $actual   = (int)$tc['inscritos_mes'];
    if ($anterior === 0 && $actual === 0) {
        $tc['tendencia'] = 'igual'; $tc['tendencia_diff'] = 0;
    } elseif ($actual > $anterior) {
        $tc['tendencia'] = 'sube';  $tc['tendencia_diff'] = $actual - $anterior;
    } elseif ($actual < $anterior) {
        $tc['tendencia'] = 'baja';  $tc['tendencia_diff'] = $anterior - $actual;
    } else {
        $tc['tendencia'] = 'igual'; $tc['tendencia_diff'] = 0;
    }
}
unset($tc);

// Panel Docentes
$docTipo   = $conn->query("SELECT tipo_docente tipo,COUNT(*) t FROM tb_docente GROUP BY tipo_docente ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$docEstado = $conn->query("SELECT estado_docente estado,COUNT(*) t FROM tb_docente GROUP BY estado_docente ORDER BY t DESC")->fetchAll(PDO::FETCH_ASSOC);
$docGenero = $conn->query("SELECT genero,COUNT(*) t FROM tb_docente GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
$listaDoc  = $conn->query("SELECT primer_nombre, primer_apellido, cedula, tipo_docente, estado_docente, genero,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
    FROM tb_docente ORDER BY primer_apellido LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Datos mensuales (año actual)
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

$metasPorMes = array_fill(0, 12, 0);
try {
    $mRows = $conn->query("SELECT MONTH(fecha_inicio) mes, COUNT(*) t FROM tb_metas WHERE estado='Activa' AND fecha_inicio IS NOT NULL GROUP BY MONTH(fecha_inicio) ORDER BY mes")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($mRows as $r) $metasPorMes[(int)$r['mes']-1] = (int)$r['t'];
} catch(Exception $e) {}

// KPIs avanzados
// CORRECCIÓN: "En sistema" se agrupa con "Inactivo" en los KPIs relevantes
$kUtilizacion = (function($c){ $r=$c->query("SELECT COALESCE(SUM(num_inscritos),0) i,COALESCE(SUM(max_participantes),1) q FROM tb_curso WHERE id_estatusCurso IN(1,2,3,4)")->fetch(PDO::FETCH_ASSOC); $p=$r['q']>0?round($r['i']/$r['q']*100,1):0; return['inscritos'=>$r['i'],'cupos'=>$r['q'],'pct'=>$p]; })($conn);

// Disponibles = sin curso activo (excluye Asignado y En formación)
$kDisponibles = (function($c){
    $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
    // "Disponible" = explícitamente disponible en BD
    $d=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='Disponible'")->fetchColumn();
    return['disponibles'=>$d,'total'=>$tot,'pct'=>$tot>0?round($d/$tot*100,1):0];
})($conn);

$kPctForm = (function($c){
    $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
    $f=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='En formación'")->fetchColumn();
    return['en_formacion'=>$f,'total'=>$tot,'pct'=>$tot>0?round($f/$tot*100,1):0];
})($conn);

// CORRECCIÓN: "En sistema" + "Inactivo" = inactivos reales
$kInactivos = (function($c){
    $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
    $inact=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado IN ('Inactivo','En sistema')")->fetchColumn();
    return['inactivos'=>$inact,'total'=>$tot,'pct'=>$tot>0?round($inact/$tot*100,1):0];
})($conn);

$kMetas = $conn->query("SELECT COUNT(*) total_metas,SUM(CASE WHEN estado_cumplimiento='Cumplida' THEN 1 ELSE 0 END) cumplidas,COALESCE(ROUND(AVG(porcentaje_cumplimiento),1),0) promedio_pct FROM tb_metas WHERE estado='Activa'")->fetch(PDO::FETCH_ASSOC);
$kPromedio = $conn->query("SELECT COUNT(*) total_cursos,COALESCE(ROUND(AVG(num_inscritos),1),0) promedio FROM tb_curso WHERE id_estatusCurso IN(1,2,3,4)")->fetch(PDO::FETCH_ASSOC);

$kAsignacion = (function($c){
    $tot=$c->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
    $a=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='Asignado'")->fetchColumn();
    $f=$c->query("SELECT COUNT(*) FROM tb_participante WHERE estado='En formación'")->fetchColumn();
    $act=$a+$f;
    return['asignados'=>$a,'en_formacion'=>$f,'activos'=>$act,'total'=>$tot,'pct'=>$tot>0?round($act/$tot*100,1):0];
})($conn);

// Cursos con columnas masculino/femenino para la tabla principal
$cursosDetalle = $conn->query("
    SELECT
        c.nombre_curso,
        c.num_inscritos,
        c.max_participantes,
        (c.max_participantes - c.num_inscritos) AS cupos_disp,
        ROUND(c.num_inscritos/NULLIF(c.max_participantes,0)*100,1) uso_pct,
        e.nombre_estatusCurso,
        COALESCE(SUM(CASE WHEN p.genero = 'Masculino' THEN 1 ELSE 0 END),0) AS masculino,
        COALESCE(SUM(CASE WHEN p.genero = 'Femenino'  THEN 1 ELSE 0 END),0) AS femenino
    FROM tb_curso c
    JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
    LEFT JOIN tb_participante_curso pc ON pc.id_curso = c.id_curso
    LEFT JOIN tb_participante p ON pc.id_participante = p.id_participante
    WHERE c.id_estatusCurso IN(1,2,3,4)
    GROUP BY c.id_curso, c.nombre_curso, c.num_inscritos, c.max_participantes,
             e.nombre_estatusCurso
    ORDER BY uso_pct DESC
")->fetchAll(PDO::FETCH_ASSOC);

$partDisponibles = $conn->query("SELECT primer_nombre,primer_apellido,cedula,genero,grado_institucion,fecha_registro FROM tb_participante WHERE estado='Disponible' ORDER BY fecha_registro DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);

$metasDetalle = [];
try {
    $metasDetalle = $conn->query("SELECT m.*, a.nombre_ambito, MONTH(m.fecha_inicio) AS mes_meta FROM tb_metas m LEFT JOIN tb_ambito a ON m.id_ambito=a.id_ambito WHERE m.estado='Activa' ORDER BY m.porcentaje_cumplimiento DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    try { $metasDetalle = $conn->query("SELECT m.*, a.nombre_ambito, NULL AS mes_meta FROM tb_metas m LEFT JOIN tb_ambito a ON m.id_ambito=a.id_ambito WHERE m.estado='Activa' ORDER BY m.porcentaje_cumplimiento DESC")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e2) {}
}

// ── Cumplimiento por ámbito ──
$cumplimientoAmbito = [];
try {
    $cumplimientoAmbito = $conn->query("
        SELECT
            COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito,
            COUNT(*) AS total_metas,
            ROUND(AVG(m.porcentaje_cumplimiento), 1) AS pct_promedio,
            SUM(CASE WHEN m.estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END) AS cumplidas
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
        WHERE m.estado = 'Activa'
        GROUP BY a.id_ambito, a.nombre_ambito
        ORDER BY pct_promedio DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// CORRECCIÓN: distribución de estados fusionando Inactivo + En sistema
$distEstados = $conn->query("
    SELECT
        CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
             ELSE estado END AS estado,
        COUNT(*) total
    FROM tb_participante
    GROUP BY CASE WHEN estado IN ('Inactivo','En sistema') THEN 'Inactivo / Sin curso'
                  ELSE estado END
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── KPI: Cupos perdidos ──
$kCuposPerdidos = [
    'cupos_vacios'    => max(0, $kUtilizacion['cupos'] - $kUtilizacion['inscritos']),
    'total_cupos'     => $kUtilizacion['cupos'],
    'pct_desperdicio' => $kUtilizacion['cupos'] > 0
        ? round((($kUtilizacion['cupos'] - $kUtilizacion['inscritos']) / $kUtilizacion['cupos']) * 100, 1) : 0,
];

// ── Formaciones por ámbito mensual y trimestral ──
$formacionesPorAmbitoMes = [];
$formacionesPorAmbitoTrim = [];
$totalFormMes  = 0;
$totalFormTrim = 0;
try {
    $rowsMes = $conn->query("
        SELECT COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito, COUNT(*) AS total
        FROM tb_curso c LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1 AND MONTH(c.fecha_inicio) = MONTH(CURDATE()) AND YEAR(c.fecha_inicio) = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $formacionesPorAmbitoMes = $rowsMes;
    $totalFormMes = array_sum(array_column($rowsMes, 'total'));

    $rowsTrim = $conn->query("
        SELECT COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito, COUNT(*) AS total
        FROM tb_curso c LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE c.id_curso != 1 AND QUARTER(c.fecha_inicio) = QUARTER(CURDATE()) AND YEAR(c.fecha_inicio) = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $formacionesPorAmbitoTrim = $rowsTrim;
    $totalFormTrim = array_sum(array_column($rowsTrim, 'total'));
} catch(Exception $e) {}
$s_formMes  = $totalFormMes  > 15 ? 'verde' : ($totalFormMes  >= 10 ? 'amarillo' : 'rojo');
$s_formTrim = $totalFormTrim > 45 ? 'verde' : ($totalFormTrim >= 30 ? 'amarillo' : 'rojo');

// ── Ámbito más solicitado por mes ──
$ambitoSolicitadoMes = [];
try {
    $ambitoSolicitadoMes = $conn->query("
        SELECT COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito,
               COUNT(pc.id_participante) AS inscripciones,
               COUNT(DISTINCT pc.id_curso) AS cursos_activos
        FROM tb_participante_curso pc
        JOIN tb_curso c ON pc.id_curso = c.id_curso
        LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
        WHERE MONTH(pc.fecha_inscripcion) = MONTH(CURDATE()) AND YEAR(pc.fecha_inscripcion) = YEAR(CURDATE())
        GROUP BY a.id_ambito, a.nombre_ambito ORDER BY inscripciones DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}
$totalInscMes = array_sum(array_column($ambitoSolicitadoMes, 'inscripciones'));

// ── % Programas ejecutados según planificación ──
$kEjecucion = ['ejecutados' => 0, 'planificados' => 0, 'pct' => 0, 'fuente' => 'cursos'];
try {
    $metasCursos = $conn->query("
        SELECT COUNT(*) AS total,
               COALESCE(ROUND(AVG(porcentaje_cumplimiento), 1), 0) AS pct_promedio,
               SUM(CASE WHEN estado_cumplimiento = 'Cumplida' THEN 1 ELSE 0 END) AS cumplidas
        FROM tb_metas
        WHERE estado = 'Activa'
          AND QUARTER(fecha_inicio) = QUARTER(CURDATE())
          AND YEAR(fecha_inicio) = YEAR(CURDATE())
          AND (LOWER(tipo_objetivo) LIKE '%curso%' OR LOWER(tipo_objetivo) LIKE '%formac%' OR LOWER(tipo_objetivo) LIKE '%program%')
    ")->fetch(PDO::FETCH_ASSOC);
    if ((int)$metasCursos['total'] > 0) {
        $kEjecucion = ['ejecutados' => (int)$metasCursos['cumplidas'], 'planificados' => (int)$metasCursos['total'], 'pct' => (float)$metasCursos['pct_promedio'], 'fuente' => 'metas'];
    } else {
        $planif = (int)$conn->query("SELECT COUNT(*) FROM tb_curso WHERE id_curso != 1 AND QUARTER(fecha_inicio) = QUARTER(CURDATE()) AND YEAR(fecha_inicio) = YEAR(CURDATE())")->fetchColumn();
        $ejecut = (int)$conn->query("SELECT COUNT(*) FROM tb_curso c JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso WHERE c.id_curso != 1 AND QUARTER(c.fecha_inicio) = QUARTER(CURDATE()) AND YEAR(c.fecha_inicio) = YEAR(CURDATE()) AND (LOWER(e.nombre_estatusCurso) LIKE '%finaliz%' OR LOWER(e.nombre_estatusCurso) LIKE '%complet%' OR LOWER(e.nombre_estatusCurso) LIKE '%en curso%')")->fetchColumn();
        $pctEjec = $planif > 0 ? round($ejecut / $planif * 100, 1) : 0;
        $kEjecucion = ['ejecutados' => $ejecut, 'planificados' => $planif, 'pct' => $pctEjec, 'fuente' => 'cursos'];
    }
} catch(Exception $e) {}
$s_ejecucion = $kEjecucion['pct'] >= 85 ? 'verde' : ($kEjecucion['pct'] >= 75 ? 'amarillo' : 'rojo');

// Semáforos
function sem($pct,$lo,$hi,$inv=false){if(!$inv){return $pct>=$hi?'verde':($pct>=$lo?'amarillo':'rojo');}else{return $pct<=$lo?'verde':($pct<=$hi?'amarillo':'rojo');}}
function semL($c){return['verde'=>'Óptimo','amarillo'=>'Atención','rojo'=>'Crítico'][$c];}
$s_util=sem($kUtilizacion['pct'],70,89); $s_disp=sem($kDisponibles['pct'],20,29,true);
$s_form=sem($kPctForm['pct'],60,79); $s_meta=sem($kMetas['promedio_pct']??0,80,95);
$s_asig=sem($kAsignacion['pct'],70,84);
$promPct=$kPromedio['total_cursos']>0?round($kPromedio['promedio']/30*100,1):0; $s_prom=sem($promPct,60,84);
// Semáforo para inactivos (menor es mejor)
$s_inact=sem($kInactivos['pct'],20,35,true);

function jl($arr,$k){return json_encode(array_column($arr,$k));}
function jv($arr,$k){return json_encode(array_map('intval',array_column($arr,$k)));}

// Paleta de colores para ámbito
$coloresFase2 = ['#6366f1','#0ea5e9','#28c76f','#f59e0b','#ef4444','#8b5cf6','#ec4899','#10b981'];

// ── Helper: nombre de mes en español ──
$mesesES = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
    5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
    9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
];
$mesActualES = $mesesES[(int)date('n')];
$anioActual  = date('Y');
$trimestreActual = (int)ceil(date('n') / 3);

// ── Datos para cards seleccionables ──
$cardDisponibles = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante WHERE estado = 'Disponible'
    ORDER BY fecha_registro DESC
")->fetchAll(PDO::FETCH_ASSOC);

$cardFormacion = $conn->query("
    SELECT p.primer_nombre, p.primer_apellido, p.cedula, p.genero,
           p.grado_institucion,
           TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
           GROUP_CONCAT(c.nombre_curso SEPARATOR ', ') AS cursos
    FROM tb_participante p
    LEFT JOIN tb_participante_curso pc ON pc.id_participante = p.id_participante
    LEFT JOIN tb_curso c ON pc.id_curso = c.id_curso
        AND pc.estatus_participante = 'En formación'
    WHERE p.estado = 'En formación'
    GROUP BY p.id_participante
    ORDER BY p.primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

$cardAsignados = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante WHERE estado = 'Asignado'
    ORDER BY primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

// CORRECCIÓN: card de inactivos incluye "En sistema"
$cardInactivos = $conn->query("
    SELECT primer_nombre, primer_apellido, cedula, genero, grado_institucion,
           estado,
           TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
           fecha_registro
    FROM tb_participante
    WHERE estado IN ('Inactivo','En sistema')
    ORDER BY primer_apellido
")->fetchAll(PDO::FETCH_ASSOC);

$jsCardDisponibles = json_encode($cardDisponibles, JSON_UNESCAPED_UNICODE);
$jsCardFormacion   = json_encode($cardFormacion,   JSON_UNESCAPED_UNICODE);
$jsCardAsignados   = json_encode($cardAsignados,   JSON_UNESCAPED_UNICODE);
$jsCardInactivos   = json_encode($cardInactivos,   JSON_UNESCAPED_UNICODE);

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
        <li class="sidebar-item"><a href="Historial.php" class="sidebar-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/></svg><span>Historial</span></a></li>
    </ul>
</aside>

<div class="main">
    <?php include 'partials/header.php'; ?>
    <div class="home-content px-3 pb-5">

        <!-- ════ BARRA DE NAVEGACIÓN INTERNA DEL DASHBOARD ════ -->
        <nav class="dashboard-nav mt-3">
            <button class="dashboard-nav-item active" data-section="resumen">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg>
                Resumen
            </button>
            <button class="dashboard-nav-item" data-section="participantes">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                Participantes
                <span class="nav-count"><?php echo $total_participantes; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="cursos">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                Cursos
                <span class="nav-count"><?php echo $total_cursos; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="docentes">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg>
                Docentes
                <span class="nav-count"><?php echo $total_docentes; ?></span>
            </button>
            <button class="dashboard-nav-item" data-section="metas">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                Metas
                <span class="nav-count"><?php echo (int)($kMetas['total_metas'] ?? 0); ?></span>
            </button>
        </nav>

        <!-- ════ SECCIÓN 1 — RESUMEN GENERAL ════ -->
        <div class="dashboard-section active" id="section-resumen">

            <!-- Barra de cumplimiento de metas -->
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

            <!-- Tarjetas KPI de resumen -->
            <div class="card-group-container bg-light p-3 rounded-3 mt-4">
                <div class="row g-3">
                    <?php
                    $resumenCards = [
                        ['id'=>'participantes','valor'=>$total_participantes,'label'=>'Participantes Totales','iconBg'=>'#e0f9fd','iconColor'=>'#00c8e0','chartId'=>'chartPartMes',
                         'svg'=>'<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>'],
                        ['id'=>'cursos','valor'=>$total_cursos,'label'=>'Total Cursos','iconBg'=>'#e2fce8','iconColor'=>'#28c76f','chartId'=>'chartCursosMes',
                         'svg'=>'<path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 1 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>'],
                        ['id'=>'docentes','valor'=>$total_docentes,'label'=>'Total Docentes','iconBg'=>'#ede8ff','iconColor'=>'#7367f0','chartId'=>'chartDocentesMes',
                         'svg'=>'<path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>'],
                    ];
                    foreach ($resumenCards as $c): ?>
                    <div class="col-xl-3 col-md-6">
                        <div class="kpi-card-basic shadow-sm" style="cursor:default;">
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
                        <div class="kpi-card-basic shadow-sm" style="cursor:default;">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicadores semáforo rápidos -->
            <div class="card-group-container bg-light p-3 rounded-3 mt-3">
                <div class="row g-3">
                    <div class="col-6 col-xl-3">
                        <div class="panel-mini-kpi semaforo-borde-<?php echo $s_util; ?>">
                            <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Utilización de cupos</span><span class="semaforo-badge semaforo-<?php echo $s_util; ?>"><?php echo semL($s_util); ?></span></div>
                            <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_util; ?>"><?php echo $kUtilizacion['pct']; ?>%</div>
                            <div class="panel-mini-kpi-sub"><?php echo $kUtilizacion['inscritos']; ?> inscritos de <?php echo $kUtilizacion['cupos']; ?> cupos</div>
                            <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_util; ?>" style="width:<?php echo min($kUtilizacion['pct'],100); ?>%"></div></div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="panel-mini-kpi semaforo-borde-<?php echo $s_form; ?>">
                            <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">En formación</span><span class="semaforo-badge semaforo-<?php echo $s_form; ?>"><?php echo semL($s_form); ?></span></div>
                            <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_form; ?>"><?php echo $kPctForm['pct']; ?>%</div>
                            <div class="panel-mini-kpi-sub"><?php echo $kPctForm['en_formacion']; ?> activos de <?php echo $kPctForm['total']; ?></div>
                            <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_form; ?>" style="width:<?php echo min($kPctForm['pct'],100); ?>%"></div></div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="panel-mini-kpi semaforo-borde-<?php echo $s_asig; ?>">
                            <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Índice de asignación</span><span class="semaforo-badge semaforo-<?php echo $s_asig; ?>"><?php echo semL($s_asig); ?></span></div>
                            <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_asig; ?>"><?php echo $kAsignacion['pct']; ?>%</div>
                            <div class="panel-mini-kpi-sub"><?php echo $kAsignacion['activos']; ?> activos de <?php echo $kAsignacion['total']; ?></div>
                            <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_asig; ?>" style="width:<?php echo min($kAsignacion['pct'],100); ?>%"></div></div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="panel-mini-kpi semaforo-borde-<?php echo $s_ejecucion; ?>">
                            <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Programas ejecutados</span><span class="semaforo-badge semaforo-<?php echo $s_ejecucion; ?>"><?php echo semL($s_ejecucion); ?></span></div>
                            <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_ejecucion; ?>"><?php echo $kEjecucion['pct']; ?>%</div>
                            <div class="panel-mini-kpi-sub"><?php echo $kEjecucion['ejecutados']; ?> de <?php echo $kEjecucion['planificados']; ?> planificados · T<?php echo $trimestreActual; ?></div>
                            <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_ejecucion; ?>" style="width:<?php echo min($kEjecucion['pct'],100); ?>%"></div></div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /section-resumen -->

        <!-- ════ SECCIÓN 2 — PARTICIPANTES ════ -->
        <div class="dashboard-section" id="section-participantes">
            <div class="kpi-panel-inner mt-4" style="border-top-color:#00c8e0;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#00afc5;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>
                        Análisis de Participantes
                    </h6>
                    <small class="text-muted">Datos en tiempo real · Semáforos activos</small>
                </div>

                <!-- GRÁFICAS PARTICIPANTES -->
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
                        <button class="chart-tab-btn" data-section="participantes" data-chart="grado">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/></svg>
                            Por Grado
                        </button>
                    </nav>
                    <div class="chart-tabs-body">
                        <!-- TAB: Por Estado — la tabla lateral muestra últimos 5 con estado fusionado -->
                        <div class="chart-tab-panel active" id="chart-panel-participantes-estado">
                            <p class="chart-panel-title">Distribución por estado <small class="text-muted" style="font-size:.72rem;">(Inactivo y En sistema se agrupan como inactivos)</small></p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartPartEstado"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Estado</th></tr></thead>
                                        <tbody>
                                        <?php foreach($ultimosPart as $p):
                                            // Normalizar etiqueta de estado para badge
                                            $estadoLabel = in_array($p['estado'], ['Inactivo','En sistema']) ? 'Inactivo / Sin curso' : $p['estado'];
                                            $ce=['En formación'=>'#28c76f','Disponible'=>'#f59e0b','Asignado'=>'#0ea5e9','Inactivo / Sin curso'=>'#ef4444'][$estadoLabel]??'#6c757d'; ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td><?php echo $p['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $ce; ?>22;color:<?php echo $ce; ?>;"><?php echo $estadoLabel; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="chart-tab-panel" id="chart-panel-participantes-genero">
                            <p class="chart-panel-title">Distribución por género</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartPartGenero"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Género</th></tr></thead>
                                        <tbody>
                                        <?php foreach($ultimosPart as $p):
                                            $gc = $p['genero']==='Masculino' ? '#0ea5e9' : '#ec4899'; ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td><?php echo $p['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $gc; ?>22;color:<?php echo $gc; ?>;"><?php echo $p['genero']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="chart-tab-panel" id="chart-panel-participantes-grado">
                            <p class="chart-panel-title">Distribución por grado de institución</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartPartGrado"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Grado</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $coloresGrado = ['#6366f1','#f59e0b','#10b981','#ef4444','#0ea5e9','#8b5cf6'];
                                        $gradosList = array_column($partGrado, 'grado_institucion');
                                        foreach($ultimosPart as $p):
                                            $gIdx = array_search($p['grado_institucion'] ?? '', $gradosList);
                                            $gColor = $gIdx !== false ? $coloresGrado[$gIdx % count($coloresGrado)] : '#6c757d';
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                                <td><?php echo $p['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $gColor; ?>22;color:<?php echo $gColor; ?>;"><?php echo htmlspecialchars($p['grado_institucion'] ?? '—'); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /chart-tabs-wrapper participantes -->

                <!-- CARDS PARTICIPANTES — INDICADORES DE DESEMPEÑO -->
                <!-- CORRECCIÓN: "En formación" e "Índice de asignación" fusionados en una sola card.
                     "Distribución por estado" eliminada (ya está en la gráfica). -->
                <div class="card-group-container bg-light p-3 rounded-3 mt-3">
                    <div class="panel-section-header">
                        <span class="panel-section-dot" style="background:#6366f1;"></span>
                        <span class="panel-section-label">Indicadores de Desempeño</span>
                        <small class="text-muted ms-auto">Haz clic en una tarjeta para ver el detalle</small>
                    </div>
                    <div class="row g-3" id="kpi-cards-participantes">

                        <!-- Card 1: Disponibles sin asignar -->
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_disp; ?> kpi-seleccionable"
                                data-kpi="disponibles"
                                style="color:<?php echo ['verde'=>'#28c76f','amarillo'=>'#f59e0b','rojo'=>'#ef4444'][$s_disp]??'#6c757d'; ?>;">
                                <span class="kpi-selector-dot"></span>
                                <div class="panel-mini-kpi-header">
                                    <span class="panel-mini-kpi-label">Disponibles sin asignar</span>
                                    <span class="semaforo-badge semaforo-<?php echo $s_disp; ?>"><?php echo semL($s_disp); ?></span>
                                </div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_disp; ?>"><?php echo $kDisponibles['pct']; ?>%</div>
                                <div class="panel-mini-kpi-sub"><?php echo $kDisponibles['disponibles']; ?> de <?php echo $kDisponibles['total']; ?></div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_disp; ?>" style="width:<?php echo min($kDisponibles['pct'],100); ?>%"></div></div>
                            </div>
                        </div>

                        <!-- Card 2 (FUSIONADA): En formación + Asignados = Participantes Activos -->
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_asig; ?> kpi-seleccionable"
                                data-kpi="activos"
                                style="color:<?php echo ['verde'=>'#28c76f','amarillo'=>'#f59e0b','rojo'=>'#ef4444'][$s_asig]??'#6c757d'; ?>;">
                                <span class="kpi-selector-dot"></span>
                                <div class="panel-mini-kpi-header">
                                    <span class="panel-mini-kpi-label">Participantes activos</span>
                                    <span class="semaforo-badge semaforo-<?php echo $s_asig; ?>"><?php echo semL($s_asig); ?></span>
                                </div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_asig; ?>"><?php echo $kAsignacion['pct']; ?>%</div>
                                <!-- Desglose En formación + Asignados -->
                                <div class="panel-mini-kpi-sub d-flex flex-column gap-1 mt-1">
                                    <span>
                                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#28c76f;margin-right:4px;"></span>
                                        En formación: <strong><?php echo $kAsignacion['en_formacion']; ?></strong>
                                    </span>
                                    <span>
                                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0ea5e9;margin-right:4px;"></span>
                                        Asignados: <strong><?php echo $kAsignacion['asignados']; ?></strong>
                                    </span>
                                    <span style="color:#64748b;">Total activos: <?php echo $kAsignacion['activos']; ?> de <?php echo $kAsignacion['total']; ?></span>
                                </div>
                                <div class="kpi-adv-barra-fondo mt-2">
                                    <!-- Barra segmentada: En formación (verde) + Asignados (azul) -->
                                    <?php
                                    $pctForm2 = $kAsignacion['total']>0 ? round($kAsignacion['en_formacion']/$kAsignacion['total']*100,1) : 0;
                                    $pctAsig2 = $kAsignacion['total']>0 ? round($kAsignacion['asignados']/$kAsignacion['total']*100,1) : 0;
                                    ?>
                                    <div style="display:flex;height:8px;border-radius:4px;overflow:hidden;">
                                        <div style="width:<?php echo $pctForm2; ?>%;background:#28c76f;"></div>
                                        <div style="width:<?php echo $pctAsig2; ?>%;background:#0ea5e9;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Promedio inscritos por formación -->
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_prom; ?> kpi-seleccionable"
                                data-kpi="promedio"
                                style="color:<?php echo ['verde'=>'#28c76f','amarillo'=>'#f59e0b','rojo'=>'#ef4444'][$s_prom]??'#6c757d'; ?>;">
                                <span class="kpi-selector-dot"></span>
                                <div class="panel-mini-kpi-header">
                                    <span class="panel-mini-kpi-label">Prom. inscritos por formación</span>
                                    <span class="semaforo-badge semaforo-<?php echo $s_prom; ?>"><?php echo semL($s_prom); ?></span>
                                </div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_prom; ?>"><?php echo $kPromedio['promedio']; ?></div>
                                <div class="panel-mini-kpi-sub"><?php echo $kPromedio['total_cursos']; ?> cursos activos</div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_prom; ?>" style="width:<?php echo min($promPct,100); ?>%"></div></div>
                            </div>
                        </div>

                        <!-- Card 4: Inactivos / Sin curso (Inactivo + En sistema) -->
                        <div class="col-6 col-xl-3">
                            <div class="panel-mini-kpi semaforo-borde-<?php echo $s_inact; ?> kpi-seleccionable"
                                data-kpi="inactivos"
                                style="color:<?php echo ['verde'=>'#28c76f','amarillo'=>'#f59e0b','rojo'=>'#ef4444'][$s_inact]??'#6c757d'; ?>;">
                                <span class="kpi-selector-dot"></span>
                                <div class="panel-mini-kpi-header">
                                    <span class="panel-mini-kpi-label">Inactivos / Sin curso activo</span>
                                    <span class="semaforo-badge semaforo-<?php echo $s_inact; ?>"><?php echo semL($s_inact); ?></span>
                                </div>
                                <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_inact; ?>"><?php echo $kInactivos['pct']; ?>%</div>
                                <div class="panel-mini-kpi-sub"><?php echo $kInactivos['inactivos']; ?> de <?php echo $kInactivos['total']; ?> · incluye «En sistema»</div>
                                <div class="kpi-adv-barra-fondo"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_inact; ?>" style="width:<?php echo min($kInactivos['pct'],100); ?>%"></div></div>
                            </div>
                        </div>

                    </div><!-- /row kpi-cards-participantes -->

                    <!-- ══ WRAPPER DESPLEGABLE DE DETALLE ══ -->
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

                    <!-- TABLAS ADICIONALES -->
                    <div class="panel-section-header mt-3">
                        <span class="panel-section-dot" style="background:#f59e0b;"></span>
                        <span class="panel-section-label">Disponibles Sin Asignación</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
                    </div>
                    <?php if(empty($partDisponibles)):?>
                        <p class="text-success py-2 fw-semibold mb-0" style="font-size:.85rem;">✓ No hay participantes disponibles sin asignar.</p>
                    <?php else:?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <th>Nombre</th><th>Cédula</th><th>Género</th><th>Grado</th><th>Fecha Registro</th>
                            </thead>
                            <tbody>
                            <?php foreach($partDisponibles as $p):?>
                                <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
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

                    <div class="panel-section-header mt-3">
                        <span class="panel-section-dot" style="background:#00c8e0;"></span>
                        <span class="panel-section-label">Últimos 5 Registrados</span>
                        <small class="text-muted ms-auto">Haz clic en una fila para ver el participante</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <th>Nombre</th><th>Cédula</th><th>Estado</th><th>Género</th><th class="text-center">Edad</th><th>Fecha Registro</th>
                            </thead>
                            <tbody>
                            <?php foreach ($ultimosPart as $p):
                                $estadoLabel2 = in_array($p['estado'], ['Inactivo','En sistema']) ? 'Inactivo / Sin curso' : $p['estado'];
                                $ce2=['En formación'=>'success','Disponible'=>'warning','Asignado'=>'info','Inactivo / Sin curso'=>'danger'][$estadoLabel2]??'secondary'; ?>
                                <tr class="part-row-link" data-cedula="<?php echo htmlspecialchars($p['cedula']); ?>" style="cursor:pointer;">
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']); ?></td>
                                    <td><?php echo $p['cedula']; ?></td>
                                    <td><span class="badge bg-<?php echo $ce2; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $estadoLabel2; ?></span></td>
                                    <td><?php echo $p['genero']; ?></td>
                                    <td class="text-center fw-semibold"><?php echo isset($p['edad']) ? $p['edad'].' años' : '—'; ?></td>
                                    <td><?php echo $p['fecha_registro']; ?></td>
                                </tr>
                            <?php endforeach; if(empty($ultimosPart)):?>
                                <tr><td colspan="6" class="text-center text-muted py-3">Sin registros.</td></tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>

                </div><!-- /card-group-container indicadores -->
            </div>
        </div><!-- /section-participantes -->

        <!-- ════ SECCIÓN 3 — CURSOS ════ -->
        <div class="dashboard-section" id="section-cursos">
            <div class="kpi-panel-inner mt-4" style="border-top-color:#28c76f;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#28a745;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                        Análisis de Cursos
                    </h6>
                    <small class="text-muted">Distribución actual del catálogo de formaciones</small>
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
                            Ámbito Solicitado
                        </button>
                    </nav>
                    <div class="chart-tabs-body">

                        <!-- TAB: Por Estatus -->
                        <div class="chart-tab-panel active" id="chart-panel-cursos-estatus">
                            <p class="chart-panel-title">Distribución por estatus de curso</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartCursosEstatus"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Estatus</th><th class="text-center">Cantidad</th><th style="min-width:80px;">Barra</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $totalEst = array_sum(array_column($cursosEstatus,'t'));
                                        $palEst   = ['#28c76f','#0ea5e9','#f59e0b','#ef4444','#8b5cf6','#6c757d'];
                                        foreach($cursosEstatus as $i=>$e):
                                            $ep = $totalEst>0?round($e['t']/$totalEst*100):0;
                                            $ec = $palEst[$i%count($palEst)];
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($e['estatus']); ?></td>
                                                <td class="text-center fw-bold"><?php echo $e['t']; ?></td>
                                                <td><div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;"><div style="width:<?php echo $ep; ?>%;height:7px;background:<?php echo $ec; ?>;border-radius:4px;"></div></div></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Por Modalidad -->
                        <div class="chart-tab-panel" id="chart-panel-cursos-modalidad">
                            <p class="chart-panel-title">Distribución por modalidad</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartCursosModalidad"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Modalidad</th><th class="text-center">Cursos</th><th style="min-width:80px;">%</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $totalMod = array_sum(array_column($cursosModalidad,'t'));
                                        $palMod   = ['#0ea5e9','#7367f0','#28c76f'];
                                        foreach($cursosModalidad as $i=>$m):
                                            $mp = $totalMod>0?round($m['t']/$totalMod*100):0;
                                            $mc = $palMod[$i%count($palMod)];
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($m['modalidad']); ?></td>
                                                <td class="text-center fw-bold"><?php echo $m['t']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $mc; ?>22;color:<?php echo $mc; ?>;"><?php echo $mp; ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Por Turno -->
                        <div class="chart-tab-panel" id="chart-panel-cursos-turno">
                            <p class="chart-panel-title">Distribución por turno</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartCursosTurno"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Turno</th><th class="text-center">Cursos</th><th style="min-width:80px;">%</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $totalTur = array_sum(array_column($cursosTurno,'t'));
                                        $palTur   = ['#ff9f43','#0ea5e9'];
                                        foreach($cursosTurno as $i=>$t):
                                            $tp = $totalTur>0?round($t['t']/$totalTur*100):0;
                                            $tc = $palTur[$i%count($palTur)];
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($t['turno']); ?></td>
                                                <td class="text-center fw-bold"><?php echo $t['t']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $tc; ?>22;color:<?php echo $tc; ?>;"><?php echo $tp; ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Ámbito Solicitado -->
                        <div class="chart-tab-panel" id="chart-panel-cursos-ambito">
                            <p class="chart-panel-title">Ámbito más solicitado — <?php echo $mesActualES . ' ' . $anioActual; ?></p>
                            <?php if (empty($ambitoSolicitadoMes)): ?>
                                <p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin inscripciones registradas este mes.</p>
                            <?php else: ?>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartAmbitoSolicitadoTab"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>#</th><th>Ámbito</th><th class="text-center">Inscrip.</th><th class="text-center">Cursos</th><th style="min-width:100px;">% del mes</th></tr></thead>
                                        <tbody>
                                        <?php foreach($ambitoSolicitadoMes as $i=>$amb):
                                            $pctAmb = $totalInscMes > 0 ? round($amb['inscripciones'] / $totalInscMes * 100, 1) : 0;
                                            $ac = $coloresFase2[$i%count($coloresFase2)]; ?>
                                            <tr>
                                                <td style="color:#94a3b8;font-weight:600;font-size:.7rem;"><?php echo str_pad($i+1,2,'0',STR_PAD_LEFT); ?></td>
                                                <td class="fw-semibold"><?php if($i===0):?><span style="color:#f59e0b;">★</span> <?php endif; ?><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                                <td class="text-center fw-bold" style="color:<?php echo $ac;?>;"><?php echo $amb['inscripciones']; ?></td>
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

                <!-- PROGRAMAS EJECUTADOS SEGÚN PLANIFICACIÓN -->
                <div class="tables-container bg-light p-3 rounded-3 mt-3">
                    <div class="panel-section">
                        <div class="panel-section-header">
                            <span class="panel-section-dot" style="background:#10b981;"></span>
                            <span class="panel-section-label">Programas Ejecutados según Planificación</span>
                            <small class="text-muted ms-auto">T<?php echo $trimestreActual; ?> <?php echo date('Y'); ?> · Meta ≥ 85%</small>
                        </div>
                        <div class="ejecucion-resumen">
                            <div class="ejecucion-stat">
                                <div class="es-val semaforo-text-<?php echo $s_ejecucion; ?>"><?php echo $kEjecucion['pct']; ?>%</div>
                                <div class="es-lbl">% ejecutado</div>
                            </div>
                            <div class="ejecucion-stat">
                                <div class="es-val" style="color:#1e293b;"><?php echo $kEjecucion['ejecutados']; ?></div>
                                <div class="es-lbl">ejecutados</div>
                            </div>
                            <div class="ejecucion-stat">
                                <div class="es-val" style="color:#64748b;"><?php echo $kEjecucion['planificados']; ?></div>
                                <div class="es-lbl">planificados</div>
                            </div>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <div style="position:relative;margin-bottom:6px;">
                                    <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#64748b;margin-bottom:4px;">
                                        <span>0%</span>
                                        <span class="semaforo-text-<?php echo $s_ejecucion; ?> fw-semibold">Actual: <?php echo $kEjecucion['pct']; ?>%</span>
                                        <span>100%</span>
                                    </div>
                                    <div style="height:16px;border-radius:8px;background:#f1f5f9;position:relative;overflow:visible;">
                                        <div style="position:absolute;top:0;left:0;width:<?php echo min($kEjecucion['pct'],100); ?>%;height:16px;border-radius:8px;background:<?php echo $s_ejecucion==='verde'?'#28c76f':($s_ejecucion==='amarillo'?'#f59e0b':'#ef4444'); ?>;transition:width .9s ease;"></div>
                                        <div style="position:absolute;top:-4px;left:70%;transform:translateX(-50%);width:2px;height:24px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
                                        <div style="position:absolute;top:-4px;left:85%;transform:translateX(-50%);width:2px;height:24px;background:#94a3b8;border-radius:1px;z-index:2;"></div>
                                    </div>
                                    <div style="position:relative;height:18px;margin-top:3px;">
                                        <span style="position:absolute;left:70%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;">70%</span>
                                        <span style="position:absolute;left:85%;transform:translateX(-50%);font-size:.65rem;color:#94a3b8;">85%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background:#f8fafc;border-radius:8px;padding:12px;border:1px solid rgba(0,0,0,.06);font-size:.75rem;">
                                    <p style="font-weight:600;color:#1e293b;margin-bottom:8px;">Semáforo CMI</p>
                                    <div style="display:flex;flex-direction:column;gap:5px;">
                                        <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#ef4444;display:inline-block;"></span><span style="color:#64748b;">Crítico: &lt; 75%</span></div>
                                        <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#f59e0b;display:inline-block;"></span><span style="color:#64748b;">Atención: 75–84%</span></div>
                                        <div style="display:flex;align-items:center;gap:7px;"><span style="width:10px;height:10px;border-radius:2px;background:#28c76f;display:inline-block;"></span><span style="color:#64748b;">Óptimo: ≥ 85%</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA CURSOS — INSCRITOS + MASCULINO/FEMENINO + OCUPACIÓN + TENDENCIA -->
                <div class="tables-container bg-light p-3 rounded-3 mt-3">
                    <div class="panel-section">
                        <div class="panel-section-header">
                            <span class="panel-section-dot" style="background:#0ea5e9;"></span>
                            <span class="panel-section-label">Cursos — Inscritos, Género y Utilización de Cupos</span>
                            <span class="text-muted ms-auto" style="font-size:.72rem;">
                                Mes actual: <strong><?php echo $mesActualES . ' ' . $anioActual; ?></strong>
                                · Haz clic en una fila para ver el curso
                            </span>
                        </div>
                        <p class="text-muted mb-2" style="font-size:.73rem;">
                            Meta ≥85% ocupación ·
                            <span class="semaforo-text-rojo fw-semibold">Rojo &lt;70%</span> ·
                            <span class="semaforo-text-amarillo fw-semibold">Amarillo 70–89%</span> ·
                            <span class="semaforo-text-verde fw-semibold">Verde ≥90%</span> ·
                            <em>↑ Subió · ↓ Bajó · — Sin cambio vs mes anterior</em> ·
                            <span style="color:#0dcaf0;">♂ Masculino</span> · <span style="color:#e83e8c;">♀ Femenino</span>
                        </p>
                        <?php if (empty($topCursosMesActual) && empty($cursosDetalle)): ?>
                            <p class="text-muted py-3 text-center">No hay cursos activos con datos disponibles.</p>
                        <?php else:
                            $topNombres = array_column($topCursosMesActual, 'nombre_curso');
                            $filasTop   = $topCursosMesActual;
                            $filasResto = array_filter($cursosDetalle, function($c) use ($topNombres) {
                                return !in_array($c['nombre_curso'], $topNombres);
                            });
                        ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Curso</th>
                                        <th class="text-center">Inscritos</th>
                                        <th class="text-center" style="color:#0dcaf0;">♂</th>
                                        <th class="text-center" style="color:#e83e8c;">♀</th>
                                        <th class="text-center">Cupos Disp.</th>
                                        <th class="text-center">Cupo Máx.</th>
                                        <th style="min-width:140px;">Ocupación</th>
                                        <th class="text-center">Tendencia</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // ── Top 5 con badge "Top" y tendencia ──
                                foreach ($filasTop as $tc):
                                    $ocPct = $tc['max_participantes'] > 0 ? round($tc['num_inscritos'] / $tc['max_participantes'] * 100, 1) : 0;
                                    $ocSem = ($ocPct >= 90) ? 'verde' : (($ocPct >= 70) ? 'amarillo' : 'rojo');
                                    $disp  = max(0, (int)$tc['max_participantes'] - (int)$tc['num_inscritos']);
                                    $dispColor = ($disp <= 0) ? '#ef4444' : (($disp <= 5) ? '#f59e0b' : '#28a745');
                                    if ($tc['tendencia'] === 'sube')     { $tendIcon = '↑'; $tendClass = 'tendencia-sube';  $tendTitle = '+' . $tc['tendencia_diff'] . ' vs mes anterior'; }
                                    elseif ($tc['tendencia'] === 'baja') { $tendIcon = '↓'; $tendClass = 'tendencia-baja';  $tendTitle = '-' . $tc['tendencia_diff'] . ' vs mes anterior'; }
                                    else                                 { $tendIcon = '—'; $tendClass = 'tendencia-igual'; $tendTitle = 'Sin cambio vs mes anterior'; }
                                    $masc = (int)($tc['masculino'] ?? 0);
                                    $fem  = (int)($tc['femenino']  ?? 0);
                                    $pctMasc = ($masc + $fem) > 0 ? round($masc / ($masc + $fem) * 100) : 0;
                                ?>
                                    <tr class="curso-row-link" data-nombre="<?php echo htmlspecialchars($tc['nombre_curso']); ?>" style="cursor:pointer;">
                                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($tc['nombre_curso']); ?>">
                                            <span class="fw-semibold"><?php echo htmlspecialchars($tc['nombre_curso']); ?></span>
                                            <span class="badge-mini ms-1" style="background:#dbeafe;color:#1d4ed8;font-size:.62rem;">Top</span>
                                        </td>
                                        <td class="text-center fw-bold"><?php echo $tc['num_inscritos']; ?></td>
                                        <td class="text-center fw-semibold" style="color:#0dcaf0;"><?php echo $masc; ?></td>
                                        <td class="text-center fw-semibold" style="color:#e83e8c;"><?php echo $fem; ?></td>
                                        <td class="text-center fw-semibold" style="color:<?php echo $dispColor; ?>;"><?php echo $disp; ?></td>
                                        <td class="text-center"><?php echo $tc['max_participantes']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Barra género dentro de la barra de ocupación -->
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
                                        <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($tc['nombre_estatusCurso'] ?? '—'); ?></span></td>
                                    </tr>
                                <?php endforeach;

                                // ── Resto de cursos activos (sin tendencia) ──
                                foreach ($filasResto as $c):
                                    $uPct = $c['uso_pct'] ?? 0;
                                    $uSem = ($uPct >= 90) ? 'verde' : (($uPct >= 70) ? 'amarillo' : 'rojo');
                                    $disp = max(0, (int)$c['cupos_disp']);
                                    $dispColor = ($disp <= 0) ? '#ef4444' : (($disp <= 5) ? '#f59e0b' : '#28a745');
                                    $masc2 = (int)($c['masculino'] ?? 0);
                                    $fem2  = (int)($c['femenino']  ?? 0);
                                    $pctMasc2 = ($masc2 + $fem2) > 0 ? round($masc2 / ($masc2 + $fem2) * 100) : 0;
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
                                        <td class="text-center"><span class="tendencia-badge tendencia-igual" title="Sin datos de tendencia este mes">—</span></td>
                                        <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.7rem;"><?php echo htmlspecialchars($c['nombre_estatusCurso']); ?></span></td>
                                    </tr>
                                <?php endforeach;

                                if (empty($filasTop) && empty($filasResto)): ?>
                                    <tr><td colspan="9" class="text-center text-muted py-3">Sin cursos activos con datos disponibles.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen de cupos perdidos -->
                        <div class="cupos-perdidos-resumen mt-3">
                            <div class="cp-item"><span class="cp-valor"><?php echo $kCuposPerdidos['cupos_vacios']; ?></span><span class="cp-lbl">Cupos sin ocupar</span></div>
                            <div class="cp-item"><span class="cp-valor"><?php echo $kCuposPerdidos['total_cupos']; ?></span><span class="cp-lbl">Cupos totales disponibles</span></div>
                            <div class="cp-item"><span class="cp-valor semaforo-text-<?php echo $s_util; ?>"><?php echo $kCuposPerdidos['pct_desperdicio']; ?>%</span><span class="cp-lbl">% capacidad sin aprovechar</span></div>
                            <div class="cp-item" style="border-left:2px solid #fde68a;padding-left:12px;"><span style="font-size:.72rem;color:#92400e;line-height:1.5;">Cada cupo vacío representa un recurso formativo no aprovechado. Meta: ≥ 85% de ocupación.</span></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Formaciones por ámbito mensual/trimestral -->
                    <div class="panel-section mt-3">
                        <div class="panel-section-header">
                            <span class="panel-section-dot" style="background:#6366f1;"></span>
                            <span class="panel-section-label">Formaciones por Ámbito</span>
                        </div>
                        <div class="periodo-tabs">
                            <button class="periodo-tab active" type="button" id="tab-mes" onclick="switchPeriodo('mes')">Mes actual</button>
                            <button class="periodo-tab" type="button" id="tab-trim" onclick="switchPeriodo('trim')">Trimestre actual</button>
                        </div>
                        <div id="periodo-mes">
                            <div class="row g-3 align-items-start">
                                <div class="col-md-4">
                                    <div class="panel-mini-kpi semaforo-borde-<?php echo $s_formMes; ?>">
                                        <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Total formaciones este mes</span><span class="semaforo-badge semaforo-<?php echo $s_formMes; ?>"><?php echo semL($s_formMes); ?></span></div>
                                        <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_formMes; ?>"><?php echo $totalFormMes; ?></div>
                                        <div class="panel-mini-kpi-sub"><?php echo count($formacionesPorAmbitoMes); ?> ámbito<?php echo count($formacionesPorAmbitoMes) !== 1 ? 's' : ''; ?> activos en <?php echo $mesActualES . ' ' . $anioActual; ?></div>
                                        <div class="kpi-adv-barra-fondo mt-2"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_formMes; ?>" style="width:<?php echo min(round($totalFormMes/15*100), 100); ?>%"></div></div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <?php if (empty($formacionesPorAmbitoMes)): ?>
                                        <p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin formaciones iniciadas este mes.</p>
                                    <?php else:
                                        $maxMes = max(array_column($formacionesPorAmbitoMes, 'total'));
                                        foreach ($formacionesPorAmbitoMes as $i => $amb):
                                            $barW = $maxMes > 0 ? round($amb['total'] / $maxMes * 100) : 0;
                                            $color = $coloresFase2[$i % count($coloresFase2)];
                                    ?>
                                        <div class="ambito-row">
                                            <span class="ambito-row-lbl" title="<?php echo htmlspecialchars($amb['ambito']); ?>"><?php echo htmlspecialchars($amb['ambito']); ?></span>
                                            <div class="kpi-adv-barra-fondo" style="height:10px;"><div style="width:<?php echo $barW; ?>%;height:10px;background:<?php echo $color; ?>;border-radius:5px;"></div></div>
                                            <span class="ambito-row-val" style="color:<?php echo $color; ?>;"><?php echo $amb['total']; ?></span>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>
                        <div id="periodo-trim" style="display:none;">
                            <div class="row g-3 align-items-start">
                                <div class="col-md-4">
                                    <div class="panel-mini-kpi semaforo-borde-<?php echo $s_formTrim; ?>">
                                        <div class="panel-mini-kpi-header"><span class="panel-mini-kpi-label">Total formaciones este trimestre</span><span class="semaforo-badge semaforo-<?php echo $s_formTrim; ?>"><?php echo semL($s_formTrim); ?></span></div>
                                        <div class="panel-mini-kpi-valor semaforo-text-<?php echo $s_formTrim; ?>"><?php echo $totalFormTrim; ?></div>
                                        <div class="panel-mini-kpi-sub">T<?php echo $trimestreActual; ?> · <?php echo count($formacionesPorAmbitoTrim); ?> ámbito<?php echo count($formacionesPorAmbitoTrim) !== 1 ? 's' : ''; ?> activos</div>
                                        <div class="kpi-adv-barra-fondo mt-2"><div class="kpi-adv-barra semaforo-fill-<?php echo $s_formTrim; ?>" style="width:<?php echo min(round($totalFormTrim/45*100), 100); ?>%"></div></div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <?php if (empty($formacionesPorAmbitoTrim)): ?>
                                        <p class="text-muted py-3 text-center" style="font-size:.82rem;">Sin formaciones registradas en este trimestre.</p>
                                    <?php else:
                                        $maxTrim = max(array_column($formacionesPorAmbitoTrim, 'total'));
                                        foreach ($formacionesPorAmbitoTrim as $i => $amb):
                                            $barW = $maxTrim > 0 ? round($amb['total'] / $maxTrim * 100) : 0;
                                            $color = $coloresFase2[$i % count($coloresFase2)];
                                    ?>
                                        <div class="ambito-row">
                                            <span class="ambito-row-lbl" title="<?php echo htmlspecialchars($amb['ambito']); ?>"><?php echo htmlspecialchars($amb['ambito']); ?></span>
                                            <div class="kpi-adv-barra-fondo" style="height:10px;"><div style="width:<?php echo $barW; ?>%;height:10px;background:<?php echo $color; ?>;border-radius:5px;"></div></div>
                                            <span class="ambito-row-val" style="color:<?php echo $color; ?>;"><?php echo $amb['total']; ?></span>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /tables-container cursos -->
            </div>
        </div><!-- /section-cursos -->

        <!-- ════ SECCIÓN 4 — DOCENTES ════ -->
        <div class="dashboard-section" id="section-docentes">
            <div class="kpi-panel-inner mt-4" style="border-top-color:#7367f0;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#7367f0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/><path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"/></svg>
                        Análisis de Docentes
                    </h6>
                    <small class="text-muted">Distribución del plantel docente activo</small>
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
                            Por Género
                        </button>
                    </nav>
                    <div class="chart-tabs-body">
                        <div class="chart-tab-panel active" id="chart-panel-docentes-tipo">
                            <p class="chart-panel-title">Distribución por tipo de docente</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartDocTipo"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Tipo</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $palTipo = ['#7367f0','#28c76f','#ff9f43','#ea5455'];
                                        $tiposDocList = array_column($docTipo,'tipo');
                                        foreach($listaDoc as $d):
                                            $tIdx = array_search($d['tipo_docente'], $tiposDocList);
                                            $tColor = $tIdx !== false ? $palTipo[$tIdx%count($palTipo)] : '#6c757d';
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?></td>
                                                <td><?php echo $d['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $tColor; ?>22;color:<?php echo $tColor; ?>;"><?php echo htmlspecialchars($d['tipo_docente']); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="chart-tab-panel" id="chart-panel-docentes-estado">
                            <p class="chart-panel-title">Distribución por estado del docente</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartDocEstado"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Estado</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $colEstDoc=['Disponible'=>'#28c76f','Activo'=>'#0ea5e9','Inactivo'=>'#ef4444'];
                                        foreach($listaDoc as $d):
                                            $deColor=$colEstDoc[$d['estado_docente']]??'#6c757d';
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?></td>
                                                <td><?php echo $d['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $deColor; ?>22;color:<?php echo $deColor; ?>;"><?php echo htmlspecialchars($d['estado_docente']); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="chart-tab-panel" id="chart-panel-docentes-genero">
                            <p class="chart-panel-title">Distribución por género</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartDocGenero"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Género</th><th class="text-center">Edad</th></tr></thead>
                                        <tbody>
                                        <?php foreach($listaDoc as $d):
                                            $dgc = $d['genero']==='Masculino' ? '#0ea5e9' : '#ec4899'; ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?></td>
                                                <td><?php echo $d['cedula']; ?></td>
                                                <td><span class="badge-mini" style="background:<?php echo $dgc; ?>22;color:<?php echo $dgc; ?>;"><?php echo $d['genero']; ?></span></td>
                                                <td class="text-center"><?php echo isset($d['edad']) ? $d['edad'].'a' : '—'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /chart-tabs-wrapper docentes -->

                <!-- TABLAS DOCENTES -->
                <div class="tables-container bg-light p-3 rounded-3 mt-3">
                    <div class="panel-section">
                        <div class="panel-section-header">
                            <span class="panel-section-dot" style="background:#7367f0;"></span>
                            <span class="panel-section-label">Listado de Docentes</span>
                            <small class="text-muted ms-auto">Primeros 10 · Haz clic en una fila para ver el docente</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <th>Nombre</th><th>Cédula</th><th class="text-center">Tipo</th><th class="text-center">Estado</th><th class="text-center">Género</th><th class="text-center">Edad</th>
                                </thead>
                                <tbody>
                                <?php foreach ($listaDoc as $d):
                                    $cd=['Disponible'=>'success','En formación'=>'warning','Asignado'=>'info','Inactivo'=>'danger'][$d['estado_docente']]??'secondary'; ?>
                                    <tr class="docente-row-link" data-cedula="<?php echo htmlspecialchars($d['cedula']); ?>" style="cursor:pointer;">
                                        <td class="fw-semibold"><?php echo htmlspecialchars($d['primer_nombre'].' '.$d['primer_apellido']); ?></td>
                                        <td><?php echo $d['cedula']; ?></td>
                                        <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-dark" style="font-size:.71rem;"><?php echo $d['tipo_docente']; ?></span></td>
                                        <td class="text-center"><span class="badge bg-<?php echo $cd; ?> bg-opacity-15 text-dark" style="font-size:.71rem;"><?php echo $d['estado_docente']; ?></span></td>
                                        <td class="text-center"><?php echo $d['genero']; ?></td>
                                        <td class="text-center fw-semibold"><?php echo isset($d['edad']) ? $d['edad'].' años' : '—'; ?></td>
                                    </tr>
                                <?php endforeach; if(empty($listaDoc)):?>
                                    <tr><td colspan="6" class="text-center text-muted py-3">Sin docentes.</td></tr>
                                <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /section-docentes -->

        <!-- ════ SECCIÓN 5 — METAS ════ -->
        <div class="dashboard-section" id="section-metas">
            <div class="kpi-panel-inner mt-4" style="border-top-color:#ef4444;">
                <div class="kpi-panel-header">
                    <h6 class="kpi-panel-titulo" style="color:#ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>
                        Progresión de Metas Activas
                    </h6>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span style="font-size:.78rem;color:#64748b;"><strong style="color:#1e293b;"><?php echo (int)($kMetas['total_metas']??0); ?></strong> activas</span>
                        <span style="font-size:.78rem;color:#64748b;"><strong style="color:#28c76f;"><?php echo (int)($kMetas['cumplidas']??0); ?></strong> cumplidas</span>
                        <span class="semaforo-badge semaforo-<?php echo $s_meta; ?>"><?php echo $kMetas['promedio_pct']??0; ?>% promedio · <?php echo semL($s_meta); ?></span>
                        <small class="text-muted">año <?php echo $anioActual; ?></small>
                    </div>
                </div>

                <!-- GRÁFICAS METAS -->
                <div class="chart-tabs-wrapper mt-3">
                    <nav class="chart-tabs-nav" id="chart-nav-metas">
                        <button class="chart-tab-btn active" data-section="metas" data-chart="ambito">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M11 0H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm-1 7H4a.5.5 0 0 1 0-1h6a.5.5 0 0 1 0 1zm0 3H4a.5.5 0 0 1 0-1h6a.5.5 0 0 1 0 1z"/></svg>
                            Cumplimiento por Ámbito
                        </button>
                        <button class="chart-tab-btn" data-section="metas" data-chart="mes">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                            Metas por Mes
                        </button>
                    </nav>
                    <div class="chart-tabs-body">
                        <div class="chart-tab-panel active" id="chart-panel-metas-ambito">
                            <p class="chart-panel-title">Promedio de cumplimiento por ámbito</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartCumplimientoAmbitoTab"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Ámbito</th><th class="text-center">Metas</th><th class="text-center">Cumplidas</th><th class="text-center">%</th></tr></thead>
                                        <tbody>
                                        <?php foreach($cumplimientoAmbito as $amb):
                                            $aSem2 = ($amb['pct_promedio']>=95)?'verde':(($amb['pct_promedio']>=80)?'amarillo':'rojo');
                                            $aColor2=['verde'=>'#28c76f','amarillo'=>'#f59e0b','rojo'=>'#ef4444'][$aSem2];
                                        ?>
                                            <tr>
                                                <td class="fw-semibold" style="font-size:.75rem;"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                                <td class="text-center"><?php echo $amb['total_metas']; ?></td>
                                                <td class="text-center text-success fw-semibold"><?php echo $amb['cumplidas']; ?></td>
                                                <td class="text-center"><span class="badge-mini" style="background:<?php echo $aColor2; ?>22;color:<?php echo $aColor2; ?>;"><?php echo $amb['pct_promedio']; ?>%</span></td>
                                            </tr>
                                        <?php endforeach;
                                        if(empty($cumplimientoAmbito)):?>
                                            <tr><td colspan="4" class="text-center text-muted py-2">Sin metas activas.</td></tr>
                                        <?php endif;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="chart-tab-panel" id="chart-panel-metas-mes">
                            <p class="chart-panel-title">Metas activas por mes — año <?php echo $anioActual; ?> · clic para filtrar tabla</p>
                            <div class="chart-tab-duo-layout">
                                <div class="chart-tab-canvas-wrap"><canvas id="chartMetasMes"></canvas></div>
                                <div class="chart-tab-side-table">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>Mes</th><th class="text-center">Metas</th><th style="min-width:70px;">Barra</th></tr></thead>
                                        <tbody>
                                        <?php
                                        $mesesNombres=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                        $maxMetaMes = max(array_merge($metasPorMes,[1]));
                                        foreach($metasPorMes as $mi=>$mv):
                                            $mBar = $maxMetaMes>0?round($mv/$maxMetaMes*100):0;
                                            $mesActualIdx = (int)date('n')-1;
                                            $esActual = ($mi===$mesActualIdx);
                                        ?>
                                            <tr<?php echo $esActual?' style="background:#fee2e210;"':''; ?>>
                                                <td class="fw-semibold" style="<?php echo $esActual?'color:#ef4444;':''; ?>"><?php echo $mesesNombres[$mi]; ?><?php if($esActual):?> <span class="badge-mini" style="background:#fee2e2;color:#b91c1c;">hoy</span><?php endif;?></td>
                                                <td class="text-center fw-bold" style="color:<?php echo $mv>0?'#ef4444':'#94a3b8';?>;"><?php echo $mv; ?></td>
                                                <td>
                                                    <div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                                                        <div style="width:<?php echo $mBar; ?>%;height:7px;background:#ef4444;border-radius:4px;opacity:<?php echo $mv>0?'1':'.3';?>;"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /chart-tabs-wrapper metas -->

                <!-- TABLAS METAS -->
                <div class="tables-container bg-light p-3 rounded-3 mt-3">
                    <?php if (!empty($cumplimientoAmbito)): ?>
                    <div class="panel-section">
                        <div class="panel-section-header">
                            <span class="panel-section-dot" style="background:#8b5cf6;"></span>
                            <span class="panel-section-label">Cumplimiento por Ámbito</span>
                            <small class="text-muted ms-auto">Meta 100% · Verde ≥95% · Amarillo ≥80% · Rojo &lt;80%</small>
                        </div>
                        <div class="row g-3 align-items-start">
                            <div class="col-md-7">
                                <div class="ambito-chart-wrap"><canvas id="chartCumplimientoAmbito"></canvas></div>
                            </div>
                            <div class="col-md-5">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <th>Ámbito</th><th class="text-center">Metas</th><th class="text-center">Cumplidas</th><th class="text-center">Promedio</th>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($cumplimientoAmbito as $amb):
                                            $aSem = ($amb['pct_promedio'] >= 95) ? 'verde' : (($amb['pct_promedio'] >= 80) ? 'amarillo' : 'rojo');
                                        ?>
                                            <tr>
                                                <td class="fw-semibold" style="font-size:.8rem;"><?php echo htmlspecialchars($amb['ambito']); ?></td>
                                                <td class="text-center"><?php echo $amb['total_metas']; ?></td>
                                                <td class="text-center text-success fw-semibold"><?php echo $amb['cumplidas']; ?></td>
                                                <td class="text-center"><span class="semaforo-badge semaforo-<?php echo $aSem; ?>" style="font-size:.68rem;"><?php echo $amb['pct_promedio']; ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="panel-section mt-3">
                        <div class="panel-section-header"><span class="panel-section-dot" style="background:#ef4444;"></span><span class="panel-section-label">Metas por Mes</span><small class="text-muted ms-auto">Haz clic en un mes para filtrar la tabla</small></div>
                        <div class="kpi-metas-calendar-wrap"><canvas id="chartMetasMesTabla"></canvas></div>
                    </div>

                    <!-- Detalle de metas -->
                    <div class="panel-section mt-3">
                        <div class="panel-section-header"><span class="panel-section-dot" style="background:#ef4444;"></span><span class="panel-section-label">Detalle de Metas</span><small class="text-muted ms-auto">Meta ≥95% verde · ≥80% atención · &lt;80% crítico</small></div>

                        <div class="metas-filter-toolbar">
                            <span id="metas-mes-activo-wrap" class="metas-mes-activo-label d-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                                Mes: <strong id="metas-mes-label">—</strong>
                            </span>
                            <button id="btn-metas-ver-todos" class="btn-metas-ver-todos d-none" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 1 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
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
                            <table class="table table-sm table-hover table-striped align-middle mb-0" id="tabla-metas-panel">
                                <thead class="table-light">
                                    <th>Ámbito</th><th class="text-center">Tipo</th><th class="text-center">Objetivo</th><th style="min-width:200px;">Progreso</th><th class="text-center">Estado</th>
                                </thead>
                                <tbody>
                                <?php foreach($metasDetalle as $m):
                                    $mPct = $m['porcentaje_cumplimiento'] ?? 0;
                                    $mSem = ($mPct >= 95) ? 'verde' : (($mPct >= 80) ? 'amarillo' : 'rojo');
                                    $cumplida = ($m['estado_cumplimiento'] === 'Cumplida');
                                    $mesMeta  = isset($m['mes_meta']) && $m['mes_meta'] !== null ? (int)$m['mes_meta'] : 0;
                                ?>
                                    <tr data-mes-meta="<?php echo $mesMeta; ?>" data-id="<?php echo $m['id_meta']; ?>"
                                        class="metas-full-row meta-row-link<?php echo $cumplida ? ' metas-row-cumplida' : ''; ?>"
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
        </div><!-- /section-metas -->

    </div><!-- /home-content -->
</div><!-- /main -->
</div><!-- /wrapper -->

<script>
const userRole = "<?php echo $_SESSION['rol'] ?? ''; ?>";
const MESES_LABELS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

window.HOME_CHARTS = {
    // CORRECCIÓN: estados ya vienen fusionados desde PHP (Inactivo + En sistema)
    partEstado:    { labels: <?php echo jl($partEstado,'estado'); ?>,    data: <?php echo jv($partEstado,'t'); ?> },
    partGenero:    { labels: <?php echo jl($partGenero,'genero'); ?>,    data: <?php echo jv($partGenero,'t'); ?> },
    partGrado:     { labels: <?php echo jl($partGrado,'grado_institucion'); ?>, data: <?php echo jv($partGrado,'t'); ?> },
    cursosEstatus: { labels: <?php echo jl($cursosEstatus,'estatus'); ?>, data: <?php echo jv($cursosEstatus,'t'); ?> },
    cursosModal:   { labels: <?php echo jl($cursosModalidad,'modalidad'); ?>, data: <?php echo jv($cursosModalidad,'t'); ?> },
    cursosTurno:   { labels: <?php echo jl($cursosTurno,'turno'); ?>,    data: <?php echo jv($cursosTurno,'t'); ?> },
    docTipo:       { labels: <?php echo jl($docTipo,'tipo'); ?>,         data: <?php echo jv($docTipo,'t'); ?> },
    docEstado:     { labels: <?php echo jl($docEstado,'estado'); ?>,     data: <?php echo jv($docEstado,'t'); ?> },
    docGenero:     { labels: <?php echo jl($docGenero,'genero'); ?>,     data: <?php echo jv($docGenero,'t'); ?> },
    partMes:       { data: <?php echo json_encode($partPorMes); ?> },
    cursosMes:     { data: <?php echo json_encode($cursosPorMes); ?> },
    docentesMes:   { data: <?php echo json_encode($docentesPorMes); ?> },
    metasMes:      { data: <?php echo json_encode($metasPorMes); ?> },
    cumplimientoAmbito: {
        labels: <?php echo json_encode(array_column($cumplimientoAmbito, 'ambito')); ?>,
        data:   <?php echo json_encode(array_map('floatval', array_column($cumplimientoAmbito, 'pct_promedio'))); ?>
    },
    ambitoSolicitado: {
        labels: <?php echo json_encode(array_column($ambitoSolicitadoMes, 'ambito')); ?>,
        data:   <?php echo json_encode(array_map('intval', array_column($ambitoSolicitadoMes, 'inscripciones'))); ?>
    }
};
</script>

<script>
/* ══════════════════════════════════════════════════════
   SISTEMA DE TABS DE GRÁFICAS
══════════════════════════════════════════════════════ */
(function () {
    document.querySelectorAll('.chart-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const section = btn.dataset.section;
            const chart   = btn.dataset.chart;
            const nav = btn.closest('.chart-tabs-nav');
            if (nav) {
                nav.querySelectorAll('.chart-tab-btn').forEach(function (b) { b.classList.remove('active'); });
            }
            btn.classList.add('active');
            const wrapper = btn.closest('.chart-tabs-wrapper');
            if (wrapper) {
                wrapper.querySelectorAll('.chart-tab-panel').forEach(function (p) { p.classList.remove('active'); });
                const targetPanel = wrapper.querySelector('#chart-panel-' + section + '-' + chart);
                if (targetPanel) targetPanel.classList.add('active');
            }
            if (typeof renderGraficasBasicas === 'function') {
                setTimeout(function () { renderGraficasBasicas(section + '-' + chart); }, 80);
            }
        });
    });
})();

/* ══════════════════════════════════════════════════════
   SISTEMA DE NAVEGACIÓN PRINCIPAL DEL DASHBOARD
══════════════════════════════════════════════════════ */
(function () {
    const navItems   = document.querySelectorAll('.dashboard-nav-item');
    const sections   = document.querySelectorAll('.dashboard-section');
    const rendered   = new Set(['resumen']);

    navItems.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = btn.dataset.section;
            navItems.forEach(function (n) { n.classList.remove('active'); });
            btn.classList.add('active');
            sections.forEach(function (s) { s.classList.remove('active'); });
            const targetSection = document.getElementById('section-' + target);
            if (targetSection) targetSection.classList.add('active');
            if (!rendered.has(target)) {
                rendered.add(target);
                if (typeof renderGraficasBasicas === 'function') {
                    setTimeout(function () { renderGraficasBasicas(target); }, 80);
                }
            }
            if (target === 'resumen' && typeof renderMiniChartsMensuales === 'function') {
                setTimeout(function () { renderMiniChartsMensuales(); }, 80);
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
<script src="../../assets/js/notifications.js"></script>
<script src="../../assets/js/toastr/toastr.min.js"></script>
<script src="../../assets/js/toastr/toastr-config.js"></script>

<script>
/* ══════ TOAST DE BIENVENIDA ══════ */
(function () {
    const userId = "<?= $_SESSION['id_usuario'] ?? '' ?>";
    const claveLogin = 'bienvenida_' + userId;
    if (!sessionStorage.getItem(claveLogin)) {
        sessionStorage.setItem(claveLogin, '1');
        const nombre = "<?= htmlspecialchars($_SESSION['primer_nombre'] ?? $_SESSION['nombre'] ?? 'Usuario') ?>";
        const rol    = "<?= htmlspecialchars($_SESSION['rol'] ?? '') ?>";
        const div = document.createElement('div');
        div.id = 'toast-bienvenida';
        div.innerHTML = `<div style="position:fixed;top:24px;right:24px;z-index:99999;background:#1e293b;color:#f1f5f9;border-left:4px solid #22c55e;border-radius:8px;padding:16px 20px;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,0.35);font-family:sans-serif;font-size:14px;animation:slideIn .4s ease;"><strong style="color:#22c55e;display:block;margin-bottom:4px;">👋 ¡Bienvenido de nuevo!</strong><p style="margin:0 0 4px;font-size:15px;">Hola, <strong>${nombre}</strong></p><p style="margin:0;color:#94a3b8;font-size:12px;">${rol} · Sistema INCES</p><div style="margin-top:12px;height:3px;border-radius:2px;background:#22c55e;animation:reducir 4s linear forwards;"></div></div>`;
        document.body.appendChild(div);
        const style = document.createElement('style');
        style.textContent = `@keyframes slideIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}@keyframes reducir{from{width:100%}to{width:0%}}@keyframes slideOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(40px)}}`;
        document.head.appendChild(style);
        setTimeout(() => { const t=document.getElementById('toast-bienvenida'); if(t){t.querySelector('div').style.animation='slideOut .4s ease forwards';setTimeout(()=>t.remove(),400);} }, 4000);
    }
})();

/* ══════ TIMER DE INACTIVIDAD ══════ */
(function () {
    const INACTIVIDAD_MS = 10 * 60 * 1000;
    const AVISO_MS       =  1 * 60 * 1000;
    const LOGOUT_URL     = '../../controlador/Usuario/cerrar_sesion.php';
    const LOGIN_URL      = 'index.php?sesion=expirada';
    let timerLogout, timerAviso, avisoActivo = false;
    function resetTimer() {
        clearTimeout(timerLogout); clearTimeout(timerAviso);
        if (avisoActivo) { const a=document.getElementById('aviso-inactividad'); if(a)a.remove(); avisoActivo=false; }
        timerAviso = setTimeout(mostrarAviso, INACTIVIDAD_MS - AVISO_MS);
        timerLogout = setTimeout(cerrarSesion, INACTIVIDAD_MS);
    }
    function mostrarAviso() {
        if (document.getElementById('aviso-inactividad')) return;
        avisoActivo = true; let segundos = 60;
        const div = document.createElement('div'); div.id = 'aviso-inactividad';
        div.innerHTML = `<div style="position:fixed;bottom:24px;right:24px;z-index:99999;background:#1e293b;color:#f1f5f9;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,0.35);font-family:sans-serif;font-size:14px;"><strong style="color:#f59e0b;display:block;margin-bottom:6px;">⚠️ Sesión por expirar</strong><p style="margin:0 0 12px;color:#cbd5e1;">Tu sesión se cerrará en <span id="cuenta-regresiva"><strong>60</strong></span> segundos.</p><button id="btn-seguir-conectado" style="background:#3b82f6;color:#fff;border:none;border-radius:5px;padding:7px 16px;cursor:pointer;font-size:13px;">Seguir conectado</button></div>`;
        document.body.appendChild(div);
        const intervalo = setInterval(() => { segundos--; const el=document.getElementById('cuenta-regresiva'); if(el)el.innerHTML=`<strong>${segundos}</strong>`; if(segundos<=0)clearInterval(intervalo); }, 1000);
        document.getElementById('btn-seguir-conectado').addEventListener('click', () => { clearInterval(intervalo); resetTimer(); });
    }
    function cerrarSesion() { fetch(LOGOUT_URL,{method:'POST',credentials:'same-origin'}).finally(()=>{window.location.href=LOGIN_URL;}); }
    ['mousemove','mousedown','keydown','scroll','touchstart','click'].forEach(ev=>document.addEventListener(ev,resetTimer,{passive:true}));
    resetTimer();
})();
</script>

<script>
/* ══════════════════════════════════════════════════
   CARDS KPI SELECCIONABLES — Participantes
   CORRECCIÓN: card "activos" fusiona En formación + Asignados
               card "inactivos" incluye "En sistema"
══════════════════════════════════════════════════ */
(function () {
    const DATA = {
        disponibles: <?php echo $jsCardDisponibles; ?>,
        formacion:   <?php echo $jsCardFormacion;   ?>,
        asignacion:  <?php echo $jsCardAsignados;   ?>,
        inactivos:   <?php echo $jsCardInactivos;   ?>
    };

    // "activos" = formación + asignados combinados con etiqueta de sub-tipo
    DATA.activos = [
        ...DATA.formacion.map(p => ({ ...p, _subtipo: 'En formación' })),
        ...DATA.asignacion.map(p => ({ ...p, _subtipo: 'Asignado' }))
    ].sort((a, b) => (a.primer_apellido || '').localeCompare(b.primer_apellido || ''));

    // "promedio" reutiliza formación como contexto
    DATA.promedio = DATA.formacion;

    const CONFIG = {
        disponibles: {
            titulo: '👥 Participantes Disponibles sin asignar',
            color:  '#f59e0b',
            thead: ['#','Nombre','Cédula','Género','Grado','Edad','Fecha Registro'],
            row: (p, i) => `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td style="font-size:.75rem;">${esc(p.fecha_registro||'—')}</td>
                </tr>`
        },
        activos: {
            titulo: '🔗 Participantes Activos (En formación + Asignados)',
            color:  '#6366f1',
            thead: ['#','Nombre','Cédula','Género','Grado','Edad','Situación'],
            row: (p, i) => {
                const subColor = p._subtipo === 'En formación' ? '#28c76f' : '#0ea5e9';
                const extra = p._subtipo === 'En formación' ? esc(p.cursos||'—') : '—';
                return `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td><span class="badge-mini" style="background:${subColor}22;color:${subColor};">${esc(p._subtipo)}</span>${p._subtipo==='En formación'?`<br><span style="font-size:.7rem;color:#64748b;">${extra}</span>`:''}</td>
                </tr>`;
            }
        },
        promedio: {
            titulo: '📊 Participantes en formación (referencia de inscritos)',
            color:  '#0ea5e9',
            thead: ['#','Nombre','Cédula','Género','Grado','Edad','Curso(s)'],
            row: (p, i) => `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td style="font-size:.73rem;color:#475569;">${esc(p.cursos||'—')}</td>
                </tr>`
        },
        inactivos: {
            titulo: '⚠️ Inactivos / Sin curso activo (incluye "En sistema")',
            color:  '#ef4444',
            thead: ['#','Nombre','Cédula','Género','Grado','Edad','Estado BD'],
            row: (p, i) => `
                <tr class="part-row-link" data-cedula="${esc(p.cedula)}" style="cursor:pointer;">
                    <td style="color:#94a3b8;font-size:.72rem;">${i+1}</td>
                    <td class="fw-semibold">${esc(p.primer_nombre + ' ' + p.primer_apellido)}</td>
                    <td>${esc(p.cedula)}</td>
                    <td><span class="badge-mini" style="background:${p.genero==='Masculino'?'#ddf1fb':'#fce7f3'};color:${p.genero==='Masculino'?'#0ea5e9':'#ec4899'};">${esc(p.genero)}</span></td>
                    <td style="font-size:.75rem;">${esc(p.grado_institucion||'—')}</td>
                    <td class="text-center fw-semibold">${p.edad ? p.edad+'a' : '—'}</td>
                    <td><span class="badge-mini" style="background:#fee2e2;color:#ef4444;">${esc(p.estado)}</span></td>
                </tr>`
        }
    };

    let cardActiva = null;

    function esc(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function abrirDetalle(kpi, cardEl) {
        const cfg  = CONFIG[kpi];
        const data = DATA[kpi];
        if (!cfg || !data) return;

        document.querySelectorAll('.kpi-seleccionable').forEach(c => c.classList.remove('kpi-activa'));

        if (cardActiva === kpi) {
            cerrarDetalle();
            return;
        }
        cardActiva = kpi;
        cardEl.classList.add('kpi-activa');

        const tituloEl = document.getElementById('kpi-detalle-titulo');
        if (tituloEl) {
            tituloEl.innerHTML = `<span style="width:10px;height:10px;border-radius:50%;background:${cfg.color};display:inline-block;"></span> ${cfg.titulo}`;
        }

        const thead = document.getElementById('kpi-detalle-thead');
        if (thead) {
            thead.innerHTML = '<tr>' + cfg.thead.map(h => `<th style="font-size:.73rem;white-space:nowrap;">${h}</th>`).join('') + '</tr>';
        }

        const tbody = document.getElementById('kpi-detalle-tbody');
        if (tbody) {
            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="${cfg.thead.length}" class="text-center text-muted py-3">Sin registros para mostrar.</td></tr>`;
            } else {
                tbody.innerHTML = data.map((p, i) => cfg.row(p, i)).join('');
            }
        }

        const countEl = document.getElementById('kpi-detalle-count');
        if (countEl) countEl.textContent = `${data.length} registro${data.length !== 1 ? 's' : ''}`;

        const wrapper = document.getElementById('kpi-detalle-wrapper');
        if (wrapper) {
            wrapper.classList.remove('visible');
            void wrapper.offsetHeight;
            wrapper.classList.add('visible');
            setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
        }
    }

    function cerrarDetalle() {
        cardActiva = null;
        document.querySelectorAll('.kpi-seleccionable').forEach(c => c.classList.remove('kpi-activa'));
        const wrapper = document.getElementById('kpi-detalle-wrapper');
        if (wrapper) wrapper.classList.remove('visible');
    }

    document.querySelectorAll('.kpi-seleccionable[data-kpi]').forEach(function (card) {
        card.addEventListener('click', function () {
            abrirDetalle(card.dataset.kpi, card);
        });
    });

    const btnClose = document.getElementById('kpi-detalle-close');
    if (btnClose) btnClose.addEventListener('click', cerrarDetalle);

})();
</script>

</body>
</html>
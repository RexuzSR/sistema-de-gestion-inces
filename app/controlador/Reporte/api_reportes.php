<?php
// ==================== API CENTRALIZADA DE REPORTES ====================
// Ubicación: app/controlador/Reporte/api_reportes.php

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_name('SISTEMA_CONTROL_INCES');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true
) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado', 'code' => 401]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

require_once '../../modelo/conexion.php';
require_once __DIR__ . '/api_reportes_cursos_trayectoria.php';

try {
    $conexion = new Conexion();
    $conn     = $conexion->getConexion();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$accion     = trim($_GET['accion'] ?? '');
$anio       = intval($_GET['anio']   ?? date('Y'));
$anioActual = intval(date('Y'));
if ($anio > $anioActual || $anio < 2000) {
    $anio = $anioActual;
}

try {
    switch ($accion) {

        case 'participantes_total':
            echo json_encode(getParticipantesTotal($conn, $anio));
            break;

        case 'participantes_aprobados':
            echo json_encode(getParticipantesAprobados($conn, $anio));
            break;

        case 'participantes_por_ambito':
            echo json_encode(getParticipantesPorAmbito($conn, $anio));
            break;

        case 'cursos_total':
            echo json_encode(getCursosTotal($conn, $anio));
            break;

        case 'cursos_mas_participantes':
            $limite = intval($_GET['limite'] ?? 10);
            echo json_encode(getCursosMasParticipantes($conn, $anio, $limite));
            break;

        case 'curso_mas_requerido':
            echo json_encode(getCursoMasRequerido($conn, $anio));
            break;

        case 'participantes_por_curso':
            $idCurso = intval($_GET['id_curso'] ?? 0);
            echo json_encode(getParticipantesPorCurso($conn, $idCurso));
            break;

        case 'docentes_total':
            $periodo = trim($_GET['periodo'] ?? 'year');
            $periodoValor = intval($_GET['periodo_valor'] ?? 0);
            echo json_encode(getDocentesTotal($conn, $anio, $periodo, $periodoValor));
            break;

        case 'docentes_mas_solicitados':
            $limite = intval($_GET['limite'] ?? 10);
            echo json_encode(getDocentesMasSolicitados($conn, $anio, $limite));
            break;

        case 'docentes_carga_horaria':
            $limite = intval($_GET['limite'] ?? 15);
            $periodo = trim($_GET['periodo'] ?? 'year');
            $periodoValor = intval($_GET['periodo_valor'] ?? 0);
            echo json_encode(getDocentesCargaHoraria($conn, $anio, $limite, $periodo, $periodoValor));
            break;

        case 'docente_detalle_cursos':
            $idDocente = intval($_GET['id_docente'] ?? 0);
            $periodo = trim($_GET['periodo'] ?? 'year');
            $periodoValor = intval($_GET['periodo_valor'] ?? 0);
            if ($idDocente <= 0) {
                echo json_encode(['error' => 'ID de docente no válido']);
                break;
            }
            echo json_encode(getDocenteDetalleCursos($conn, $idDocente, $anio, $periodo, $periodoValor));
            break;

        case 'docentes_inactivos':
            echo json_encode(getDocentesInactivos($conn));
            break;

        case 'detalle_docente':
            $cedula = intval($_GET['cedula'] ?? 0);
            if ($cedula <= 0) { echo json_encode(['error' => 'Cédula no válida']); break; }
            echo json_encode(getDetalleDocente($conn, $cedula));
            break;

        case 'historial_cursos_docente':
            $idDocente = intval($_GET['id_docente'] ?? 0);
            if ($idDocente <= 0) { echo json_encode(['error' => 'ID no válido']); break; }
            echo json_encode(getHistorialCursosDocente($conn, $idDocente));
            break;

        case 'cursos_sin_docente':
            echo json_encode(getCursosSinDocente($conn));
            break;

        case 'metas_por_ambito':
            echo json_encode(getMetasPorAmbito($conn, $anio));
            break;
            
        case 'metas_por_anio':
            echo json_encode(getMetasPorAnio($conn));
            break;
            
        case 'metas_por_mes':
            $anioMeta = intval($_GET['anio_meta'] ?? $anio);
            echo json_encode(getMetasPorMes($conn, $anioMeta));
            break;
            
        case 'metas_por_trimestre':
            $anioMeta = intval($_GET['anio_meta'] ?? $anio);
            echo json_encode(getMetasPorTrimestre($conn, $anioMeta));
            break;
            
        case 'progreso_metas_detallado':
            echo json_encode(getProgresoMetasDetallado($conn, $anio));
            break;

        case 'detalle_participantes_meta':
            $idMeta = intval($_GET['id_meta'] ?? 0);
            if ($idMeta <= 0) {
                echo json_encode(['error' => 'ID de meta no válido']);
                break;
            }
            echo json_encode(getDetalleParticipantesPorMeta($conn, $idMeta));
            break;

        case 'busqueda_global':
            $q      = trim($_GET['q'] ?? '');
            $limite = intval($_GET['limite'] ?? 5);
            if (strlen($q) < 2) {
                echo json_encode(['participantes' => [], 'cursos' => [], 'docentes' => []]);
                break;
            }
            echo json_encode([
                'participantes' => buscarParticipantes($conn, $q, $limite),
                'cursos'        => buscarCursos($conn, $q, $limite),
                'docentes'      => buscarDocentes($conn, $q, $limite)
            ]);
            break;

        case 'lista_docentes':
            echo json_encode(getListaDocentes($conn));
            break;

        case 'anios_disponibles':
            echo json_encode(getAniosDisponibles($conn));
            break;
        
        case 'resumen_global':
        echo json_encode(getResumenGlobal($conn));
        break;

        case 'trayectoria_resumen':
            echo json_encode(getTrayectoriaResumen($conn));
            break;

        case 'trayectoria_participantes':
            echo json_encode(getTrayectoriaParticipantes($conn));
            break;

        case 'detalle_participante':
            $cedula = intval($_GET['cedula'] ?? 0);
            if ($cedula <= 0) {
                echo json_encode(['error' => 'Cédula no válida']);
                break;
            }
            echo json_encode(getDetalleParticipante($conn, $cedula));
            break;

        case 'trayectoria_cursos_resumen':
            echo json_encode(getTrayectoriaCursosResumen($conn));
            break;

        case 'trayectoria_cursos':
            echo json_encode(getTrayectoriaCursos($conn));
            break;

        case 'detalle_curso_trayectoria':
            $idCurso = intval($_GET['id_curso'] ?? 0);
            if ($idCurso <= 0) {
                echo json_encode(['error' => 'ID de curso no válido']);
                break;
            }
            echo json_encode(getDetalleCursoTrayectoria($conn, $idCurso));
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => "Acción '$accion' no reconocida"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno al procesar la consulta', 'detalle' => $e->getMessage()]);
}

$conn = null;
exit;

// ====================================================================
// ─────────────────── HELPERS ────────────────────────────────────────
// ====================================================================

function inicializarMeses(int $anio): array {
    $nombres  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $hoyAnio  = intval(date('Y'));
    $hoyMes   = intval(date('n'));
    $meses    = [];
    for ($i = 1; $i <= 12; $i++) {
        $esFuturo = ($anio > $hoyAnio) || ($anio === $hoyAnio && $i > $hoyMes);
        $meses[$i] = [
            'mes'            => $nombres[$i - 1],
            'mesNumero'      => $i,
            'nuevos'         => $esFuturo ? null : 0,
            'activos'        => $esFuturo ? null : 0,
            'retirados'      => $esFuturo ? null : 0,
            'totalAcumulado' => $esFuturo ? null : 0,
            'demo'           => $esFuturo ? null : demoVacio(),
        ];
    }
    return $meses;
}

function demoVacio(): array {
    $base = ['masculino' => 0, 'femenino' => 0, 'conDisc' => 0, 'sinDisc' => 0,
             'adolescente' => 0, 'adulto' => 0, 'adultoMayor' => 0];
    return ['nuevos' => $base, 'activos' => $base, 'retirados' => $base];
}

function mesesAArray(array $meses): array {
    $acumulado = 0;
    $resultado = [];
    foreach ($meses as $m) {
        if ($m['nuevos'] !== null) {
            $acumulado += $m['nuevos'];
            $m['totalAcumulado'] = $acumulado;
        }
        $resultado[] = $m;
    }
    return $resultado;
}

function selectDemo(string $alias = 'p'): string {
    return "
        SUM(CASE WHEN $alias.genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
        SUM(CASE WHEN $alias.genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino,
        SUM(CASE WHEN $alias.id_discapacidad IS NOT NULL AND $alias.id_discapacidad <> 1 THEN 1 ELSE 0 END) AS con_disc,
        SUM(CASE WHEN $alias.id_discapacidad IS NULL OR $alias.id_discapacidad = 1 THEN 1 ELSE 0 END) AS sin_disc,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, $alias.fecha_nacimiento, CURDATE()) BETWEEN 15 AND 17 THEN 1 ELSE 0 END) AS adolescente,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, $alias.fecha_nacimiento, CURDATE()) BETWEEN 18 AND 59 THEN 1 ELSE 0 END) AS adulto,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, $alias.fecha_nacimiento, CURDATE()) >= 60             THEN 1 ELSE 0 END) AS adulto_mayor
    ";
}

function mapDemo(array $row): array {
    return [
        'masculino'   => intval($row['masculino']    ?? 0),
        'femenino'    => intval($row['femenino']     ?? 0),
        'conDisc'     => intval($row['con_disc']     ?? 0),
        'sinDisc'     => intval($row['sin_disc']     ?? 0),
        'adolescente' => intval($row['adolescente']  ?? 0),
        'adulto'      => intval($row['adulto']       ?? 0),
        'adultoMayor' => intval($row['adulto_mayor'] ?? 0),
    ];
}

// ====================================================================
// ─────────────────── PARTICIPANTES ──────────────────────────────────
// ====================================================================

function getParticipantesTotal(PDO $conn, int $anio): array {
    $meses = inicializarMeses($anio);

    $stmt = $conn->prepare("
        SELECT MONTH(p.fecha_registro) AS mes, COUNT(*) AS total, " . selectDemo() . "
        FROM tb_participante p WHERE YEAR(p.fecha_registro) = :anio GROUP BY MONTH(p.fecha_registro)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes']);
        if (isset($meses[$m])) { $meses[$m]['nuevos'] = intval($row['total']); $meses[$m]['demo']['nuevos'] = mapDemo($row); }
    }

    $stmt = $conn->prepare("
        SELECT meses.mes_activo, COUNT(DISTINCT pc.id_participante) AS total, " . selectDemo() . "
        FROM (SELECT 1 AS mes_activo UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
              UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) meses
        JOIN tb_participante_curso pc
          ON pc.FechaInicio <= LAST_DAY(MAKEDATE(:anio1, 1) + INTERVAL (meses.mes_activo - 1) MONTH)
         AND pc.FechaFin >= DATE_FORMAT(MAKEDATE(:anio2, 1) + INTERVAL (meses.mes_activo - 1) MONTH, '%Y-%m-01')
         AND pc.estatus_participante = 'En formación'
        JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE YEAR(pc.FechaInicio) <= :anio3 AND YEAR(pc.FechaFin) >= :anio4
        GROUP BY meses.mes_activo
    ");
    $stmt->execute([':anio1'=>$anio,':anio2'=>$anio,':anio3'=>$anio,':anio4'=>$anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes_activo']);
        if (isset($meses[$m])) { $meses[$m]['activos'] = intval($row['total']); $meses[$m]['demo']['activos'] = mapDemo($row); }
    }

    $stmt = $conn->prepare("
        SELECT MONTH(pc.FechaFin) AS mes, COUNT(DISTINCT pc.id_participante) AS total, " . selectDemo() . "
        FROM tb_participante_curso pc JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE YEAR(pc.FechaFin) = :anio AND pc.estatus_participante IN ('Ausente', 'Reprobado')
        GROUP BY MONTH(pc.FechaFin)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes']);
        if (isset($meses[$m])) { $meses[$m]['retirados'] = intval($row['total']); $meses[$m]['demo']['retirados'] = mapDemo($row); }
    }
    return mesesAArray($meses);
}

function getParticipantesAprobados(PDO $conn, int $anio): array {
    $meses = inicializarMeses($anio);
    $stmt  = $conn->prepare("
        SELECT MONTH(pc.FechaFin) AS mes, COUNT(DISTINCT pc.id_participante) AS total, " . selectDemo() . "
        FROM tb_participante_curso pc JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE YEAR(pc.FechaFin) = :anio AND pc.estatus_participante = 'Aprobado' GROUP BY MONTH(pc.FechaFin)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes']);
        if (isset($meses[$m])) { $meses[$m]['nuevos'] = intval($row['total']); $meses[$m]['demo']['nuevos'] = mapDemo($row); }
    }
    return mesesAArray($meses);
}

function getParticipantesPorAmbito(PDO $conn, int $anio): array {
    $stmt = $conn->prepare("
        SELECT a.nombre_ambito AS ambito,
               COUNT(DISTINCT pc.id_participante) AS total,
               SUM(CASE WHEN pc.estatus_participante = 'Aprobado' THEN 1 ELSE 0 END) AS aprobados,
               SUM(CASE WHEN pc.estatus_participante IN ('En formación','Asignado') THEN 1 ELSE 0 END) AS en_formacion,
               SUM(CASE WHEN pc.estatus_participante IN ('Ausente','Reprobado') THEN 1 ELSE 0 END) AS retirados
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        WHERE YEAR(pc.fecha_inscripcion) = :anio
        GROUP BY a.id_ambito, a.nombre_ambito ORDER BY total DESC
    ");
    $stmt->execute([':anio' => $anio]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── CURSOS ─────────────────────────────────────────
// ====================================================================

function getCursosTotal(PDO $conn, int $anio): array {
    $meses = inicializarMeses($anio);
    $stmt  = $conn->prepare("
        SELECT MONTH(c.fecha_creacion) AS mes, COUNT(*) AS total,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En proceso' THEN 1 ELSE 0 END) AS en_proceso,
               SUM(CASE WHEN e.nombre_estatusCurso = 'Culminado'  THEN 1 ELSE 0 END) AS culminado,
               SUM(CASE WHEN e.nombre_estatusCurso = 'En espera'  THEN 1 ELSE 0 END) AS en_espera,
               SUM(CASE WHEN e.nombre_estatusCurso = 'Diferido'   THEN 1 ELSE 0 END) AS diferido,
               SUM(CASE WHEN e.nombre_estatusCurso = 'Inactivo'   THEN 1 ELSE 0 END) AS inactivo
        FROM tb_curso c
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        WHERE YEAR(c.fecha_creacion) = :anio GROUP BY MONTH(c.fecha_creacion)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes']);
        if (isset($meses[$m])) {
            $meses[$m]['nuevos']    = intval($row['total']);
            $meses[$m]['activos']   = intval($row['en_proceso']);
            $meses[$m]['retirados'] = intval($row['culminado']);
            $meses[$m]['en_espera'] = intval($row['en_espera']);
            $meses[$m]['diferido']  = intval($row['diferido']);
        }
    }
    return mesesAArray($meses);
}

function getCursosMasParticipantes(PDO $conn, int $anio, int $limite = 10): array {
    $stmt = $conn->prepare("
        SELECT
            c.id_curso,
            c.nombre_curso,
            COUNT(DISTINCT pc.id_participante) AS total_participantes,
            e.nombre_estatusCurso AS estatus,
            m.nombre_modalidad    AS modalidad,
            c.fecha_inicio,
            c.fecha_fin
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_modalidad    m ON m.id_modalidad    = c.id_modalidad
        WHERE YEAR(pc.fecha_inscripcion) = :anio
        GROUP BY c.id_curso, c.nombre_curso, e.nombre_estatusCurso, m.nombre_modalidad, c.fecha_inicio, c.fecha_fin
        ORDER BY total_participantes DESC
        LIMIT :limite
    ");
    $stmt->bindValue(':anio',   $anio,   PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCursoMasRequerido(PDO $conn, int $anio): array {
    $stmt = $conn->prepare("
        SELECT c.nombre_curso,
               COUNT(pc.id_participante_curso)  AS total_inscripciones,
               COUNT(DISTINCT pc.id_participante) AS participantes_unicos,
               e.nombre_estatusCurso AS estatus,
               a.nombre_areaFormativa AS area
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso   e ON e.id_estatusCurso   = c.id_estatusCurso
        LEFT JOIN tb_areaformativa  a ON a.id_areaFormativa  = c.id_areaFormativa
        WHERE YEAR(pc.fecha_inscripcion) = :anio
        GROUP BY c.id_curso ORDER BY total_inscripciones DESC LIMIT 10
    ");
    $stmt->execute([':anio' => $anio]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getParticipantesPorCurso(PDO $conn, int $idCurso): array {
    if ($idCurso <= 0) return [];
    $stmt = $conn->prepare("
        SELECT
            p.id_participante,
            p.primer_nombre,
            p.primer_apellido,
            p.cedula,
            pc.estatus_participante
        FROM tb_participante_curso pc
        JOIN tb_participante p ON p.id_participante = pc.id_participante
        WHERE pc.id_curso = :id_curso
        ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    $stmt->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── DOCENTES ───────────────────────────────────────
// ====================================================================

function construirFiltroPeriodo(string $campoFecha, string $periodo, int $anio, $valor): array {
    $condiciones = [];
    $params = [];
    if ($periodo === 'month') {
        $condiciones[] = "MONTH($campoFecha) = :periodo_valor";
        $params[':periodo_valor'] = intval($valor);
    } elseif ($periodo === 'week') {
        $condiciones[] = "WEEK($campoFecha, 1) = :periodo_valor";
        $params[':periodo_valor'] = intval($valor);
    }
    $condiciones[] = "YEAR($campoFecha) = :anio";
    $params[':anio'] = $anio;
    return ['clausulas' => $condiciones, 'params' => $params];
}

function getDocentesTotal(PDO $conn, int $anio, string $periodo = 'year', int $periodoValor = 0): array {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total,
               SUM(CASE WHEN estado_docente = 'Disponible'   THEN 1 ELSE 0 END) AS disponible,
               SUM(CASE WHEN estado_docente = 'En formación' THEN 1 ELSE 0 END) AS en_formacion,
               SUM(CASE WHEN estado_docente = 'Asignado'     THEN 1 ELSE 0 END) AS asignado,
               SUM(CASE WHEN estado_docente = 'Inactivo'     THEN 1 ELSE 0 END) AS inactivo,
               SUM(CASE WHEN tipo_docente   = 'Fijo'         THEN 1 ELSE 0 END) AS fijo,
               SUM(CASE WHEN tipo_docente   = 'Contratado'   THEN 1 ELSE 0 END) AS contratado,
               SUM(CASE WHEN tipo_docente   = 'PDL'          THEN 1 ELSE 0 END) AS pdl,
               SUM(CASE WHEN tipo_docente   = 'Voluntario'   THEN 1 ELSE 0 END) AS voluntario,
               SUM(CASE WHEN genero = 'Masculino' THEN 1 ELSE 0 END) AS masculino,
               SUM(CASE WHEN genero = 'Femenino'  THEN 1 ELSE 0 END) AS femenino
        FROM tb_docente
    ");
    $stmt->execute();
    $totales = $stmt->fetch(PDO::FETCH_ASSOC);

    $meses = inicializarMeses($anio);
    $stmt  = $conn->prepare("
        SELECT MONTH(dc.fecha_asignacion) AS mes, COUNT(*) AS total
        FROM tb_docente_curso dc
        WHERE YEAR(dc.fecha_asignacion) = :anio AND dc.id_docente IS NOT NULL
        GROUP BY MONTH(dc.fecha_asignacion)
    ");
    $stmt->execute([':anio' => $anio]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $m = intval($row['mes']);
        if (isset($meses[$m])) $meses[$m]['nuevos'] = intval($row['total']);
    }
    return ['totales' => $totales, 'asignacionesMes' => mesesAArray($meses)];
}

function getDocentesMasSolicitados(PDO $conn, int $anio, int $limite = 10): array {
    $stmt = $conn->prepare("
        SELECT d.primer_nombre, d.primer_apellido, d.tipo_docente, d.estado_docente,
               COUNT(dc.id_curso) AS cursos_asignados
        FROM tb_docente_curso dc
        JOIN tb_docente d ON d.id_docente = dc.id_docente
        WHERE YEAR(dc.fecha_asignacion) = :anio AND dc.id_docente IS NOT NULL
        GROUP BY d.id_docente ORDER BY cursos_asignados DESC LIMIT :limite
    ");
    $stmt->bindValue(':anio',   $anio,   PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ⭐ FUNCIÓN ACTUALIZADA con métricas de encargos vs ejecución
function getDetalleDocente(PDO $conn, int $cedula): array
{
    $stmt = $conn->prepare("
        SELECT id_docente, primer_nombre, segundo_nombre,
               primer_apellido, segundo_apellido,
               cedula, correo, fecha_nacimiento, edad,
               estado_docente, genero, tipo_docente
        FROM tb_docente WHERE cedula = :cedula LIMIT 1
    ");
    $stmt->execute([':cedula' => $cedula]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doc) return ['error' => 'Docente no encontrado'];
    $id = intval($doc['id_docente']);

    $stmt = $conn->prepare("SELECT numero_de_telefono, tipo_de_telefono FROM tb_telefono WHERE id_docente = :id ORDER BY id_telefono ASC");
    $stmt->execute([':id' => $id]);
    $doc['telefonos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ⭐ NUEVO: Estadísticas completas por ENCARGOS vs EJECUCIÓN
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT dc.id_curso) AS cursos_asignados,
            SUM(CASE WHEN c.id_estatusCurso = 5 THEN 1 ELSE 0 END) AS cursos_completados,
            SUM(c.horas_totales) AS horas_asignadas,
            SUM(c.horas_completadas) AS horas_completadas,
            SUM(CASE WHEN c.id_estatusCurso = 5 THEN 1 ELSE 0 END) AS cursos_culminados,
            SUM(CASE WHEN c.id_estatusCurso IN (3,4) THEN 1 ELSE 0 END) AS cursos_activos,
            MIN(c.fecha_inicio) AS primer_curso,
            MAX(c.fecha_inicio) AS ultimo_curso,
            COUNT(DISTINCT c.id_curso) AS total_cursos,
            -- Participantes inscritos en sus cursos
            (SELECT COUNT(DISTINCT pc.id_participante) 
             FROM tb_participante_curso pc 
             WHERE pc.id_curso IN (SELECT id_curso FROM tb_docente_curso WHERE id_docente = :id)) AS total_inscritos,
            -- Participantes aprobados en sus cursos
            (SELECT COUNT(DISTINCT pc.id_participante) 
             FROM tb_participante_curso pc 
             WHERE pc.id_curso IN (SELECT id_curso FROM tb_docente_curso WHERE id_docente = :id)
               AND pc.estatus_participante = 'Aprobado') AS total_aprobados
        FROM tb_docente_curso dc
        INNER JOIN tb_curso c ON c.id_curso = dc.id_curso
        WHERE dc.id_docente = :id
    ");
    $stmt->execute([':id' => $id]);
    $doc['resumen'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Aseguramos valores por defecto
    $doc['resumen']['cursos_asignados'] = intval($doc['resumen']['cursos_asignados'] ?? 0);
    $doc['resumen']['cursos_completados'] = intval($doc['resumen']['cursos_completados'] ?? 0);
    $doc['resumen']['total_inscritos'] = intval($doc['resumen']['total_inscritos'] ?? 0);
    $doc['resumen']['total_aprobados'] = intval($doc['resumen']['total_aprobados'] ?? 0);
    $doc['resumen']['total_cursos'] = intval($doc['resumen']['total_cursos'] ?? 0);
    $doc['resumen']['participantes_formados'] = $doc['resumen']['total_aprobados']; // Para compatibilidad

    $stmt = $conn->prepare("
        SELECT DISTINCT COALESCE(a.nombre_ambito,'Sin ámbito') AS nombre_ambito
        FROM tb_docente_curso dc
        INNER JOIN tb_curso c ON c.id_curso = dc.id_curso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        WHERE dc.id_docente = :id
    ");
    $stmt->execute([':id' => $id]);
    $doc['ambitos'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre_ambito');

    $stmt = $conn->prepare("
        SELECT DISTINCT COALESCE(af.nombre_areaFormativa,'Sin área') AS nombre_area
        FROM tb_docente_curso dc
        INNER JOIN tb_curso c ON c.id_curso = dc.id_curso
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa = c.id_areaFormativa
        WHERE dc.id_docente = :id
    ");
    $stmt->execute([':id' => $id]);
    $doc['areas'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'nombre_area');

    return $doc;
}

function getHistorialCursosDocente(PDO $conn, int $idDocente): array
{
    $stmt = $conn->prepare("
        SELECT c.id_curso, c.nombre_curso, c.fecha_inicio, c.fecha_fin,
               c.horas_totales, c.horas_completadas, c.turno,
               c.tipo_formacion, c.programa_formacion,
               c.max_participantes, c.num_inscritos,
               dc.fecha_asignacion,
               COALESCE(ec.nombre_estatusCurso,'Sin estado')  AS estatus_curso,
               COALESCE(a.nombre_ambito,'Sin ámbito')         AS nombre_ambito,
               COALESCE(af.nombre_areaFormativa,'Sin área')   AS nombre_area,
               COALESCE(m.nombre_modalidad,'Sin modalidad')   AS nombre_modalidad,
               SUM(CASE WHEN pc.estatus_participante='Aprobado'     THEN 1 ELSE 0 END) AS aprobados,
               SUM(CASE WHEN pc.estatus_participante='Reprobado'    THEN 1 ELSE 0 END) AS reprobados,
               SUM(CASE WHEN pc.estatus_participante='Ausente'      THEN 1 ELSE 0 END) AS ausentes,
               SUM(CASE WHEN pc.estatus_participante='En formación' THEN 1 ELSE 0 END) AS en_formacion,
               COUNT(pc.id_participante_curso)                                          AS total_inscritos
        FROM tb_docente_curso dc
        INNER JOIN tb_curso c ON c.id_curso = dc.id_curso
        LEFT JOIN tb_estatuscurso  ec ON ec.id_estatusCurso   = c.id_estatusCurso
        LEFT JOIN tb_ambito         a ON a.id_ambito           = c.id_ambito
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa   = c.id_areaFormativa
        LEFT JOIN tb_modalidad      m ON m.id_modalidad        = c.id_modalidad
        LEFT JOIN tb_participante_curso pc ON pc.id_curso      = c.id_curso
        WHERE dc.id_docente = :id
        GROUP BY c.id_curso, c.nombre_curso, c.fecha_inicio, c.fecha_fin,
                 c.horas_totales, c.horas_completadas, c.turno, c.tipo_formacion,
                 c.programa_formacion, c.max_participantes, c.num_inscritos,
                 dc.fecha_asignacion, ec.nombre_estatusCurso,
                 a.nombre_ambito, af.nombre_areaFormativa, m.nombre_modalidad
        ORDER BY c.fecha_inicio DESC
    ");
    $stmt->execute([':id' => $idDocente]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDocentesInactivos(PDO $conn): array {
    $stmt = $conn->prepare("
        SELECT d.id_docente, d.primer_nombre, d.primer_apellido, d.tipo_docente,
               d.estado_docente, d.correo, MAX(dc.fecha_asignacion) AS ultima_asignacion
        FROM tb_docente d
        LEFT JOIN tb_docente_curso dc ON dc.id_docente = d.id_docente
        GROUP BY d.id_docente
        HAVING ultima_asignacion IS NULL OR ultima_asignacion < DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        ORDER BY ultima_asignacion ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── LISTA DE DOCENTES ──────────────────────────────
// ====================================================================

function getListaDocentes(PDO $conn): array
{
    $stmt = $conn->prepare("
        SELECT 
            id_docente,
            primer_nombre,
            segundo_nombre,
            primer_apellido,
            segundo_apellido,
            cedula,
            correo,
            tipo_docente,
            estado_docente,
            genero
        FROM tb_docente
        ORDER BY primer_apellido ASC, primer_nombre ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── CURSOS SIN DOCENTE ─────────────────────────
// ====================================================================

function getCursosSinDocente(PDO $conn): array
{
    $stmt = $conn->query("
        SELECT
            c.id_curso,
            c.nombre_curso,
            c.fecha_inicio,
            c.horas_totales,
            c.max_participantes,
            c.turno,
            COALESCE(a.nombre_ambito, 'Sin ámbito')          AS nombre_ambito,
            COALESCE(af.nombre_areaFormativa, 'Sin área')    AS nombre_area,
            COALESCE(m.nombre_modalidad, 'Sin modalidad')    AS nombre_modalidad,
            (
                SELECT COUNT(*)
                FROM tb_participante_curso pc
                WHERE pc.id_curso = c.id_curso
            ) AS participantes_inscritos
        FROM tb_curso c
        LEFT JOIN tb_ambito        a  ON a.id_ambito        = c.id_ambito
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa = c.id_areaFormativa
        LEFT JOIN tb_modalidad     m  ON m.id_modalidad      = c.id_modalidad
        WHERE c.id_estatusCurso = 1
        AND NOT EXISTS (
            SELECT 1
            FROM tb_docente_curso dc
            WHERE dc.id_curso = c.id_curso
              AND dc.id_docente IS NOT NULL
        )
        ORDER BY c.fecha_inicio ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── CARGA HORARIA DOCENTES ─────────────────────
// ====================================================================

function getDocentesCargaHoraria(PDO $conn, int $anio, int $limite = 15, string $periodo = 'year', int $periodoValor = 0): array
{
    $filtro = construirFiltroPeriodo('COALESCE(c.fecha_inicio, dc.fecha_asignacion)', $periodo, $anio, $periodoValor);
    $filtroSolap = construirFiltroPeriodo('COALESCE(c2.fecha_inicio, dc2.fecha_asignacion)', $periodo, $anio, $periodoValor);
    $wherePeriodo = implode(' AND ', $filtro['clausulas']);
    $wherePeriodoSolap = implode(' AND ', $filtroSolap['clausulas']);

    $stmt = $conn->prepare("
        SELECT
            d.id_docente,
            d.primer_nombre,
            d.primer_apellido,
            d.tipo_docente,
            d.estado_docente,
            COALESCE(SUM(c.horas_totales), 0)      AS horas_asignadas,
            COALESCE(SUM(c.horas_completadas), 0)  AS horas_completadas,
            COUNT(DISTINCT dc.id_curso)             AS cursos_activos,
            COALESCE((
                SELECT 1
                FROM tb_docente_curso dc2
                JOIN tb_curso c2 ON c2.id_curso = dc2.id_curso
                WHERE dc2.id_docente = d.id_docente
                  AND c2.id_estatusCurso IN (3, 4, 5)
                  AND $wherePeriodoSolap
                GROUP BY dc2.id_docente
                HAVING SUM(c2.horas_por_clase) > 8
            ), 0) AS sobrecarga_diaria
        FROM tb_docente d
        INNER JOIN tb_docente_curso dc ON dc.id_docente = d.id_docente
        INNER JOIN tb_curso c          ON c.id_curso    = dc.id_curso
        WHERE
            $wherePeriodo
            AND c.id_estatusCurso IN (3, 4, 5)
        GROUP BY
            d.id_docente,
            d.primer_nombre,
            d.primer_apellido,
            d.tipo_docente,
            d.estado_docente
        HAVING horas_asignadas > 0
        ORDER BY horas_asignadas DESC
        LIMIT :limite
    ");

    foreach ($filtro['params'] as $key => $value) {
        if ($key === ':periodo_valor') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } elseif ($key === ':anio') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDocenteDetalleCursos(PDO $conn, int $idDocente, int $anio, string $periodo = 'year', int $periodoValor = 0): array
{
    $filtro = construirFiltroPeriodo('COALESCE(c.fecha_inicio, dc.fecha_asignacion)', $periodo, $anio, $periodoValor);
    $wherePeriodo = implode(' AND ', $filtro['clausulas']);

    $stmt = $conn->prepare("
        SELECT
            c.id_curso,
            c.nombre_curso,
            c.fecha_inicio,
            c.fecha_fin,
            c.horas_totales,
            c.horas_completadas,
            COALESCE(ec.nombre_estatusCurso, 'Sin estado') AS estatus_curso
        FROM tb_docente_curso dc
        INNER JOIN tb_curso c ON c.id_curso = dc.id_curso
        LEFT JOIN tb_estatuscurso ec ON ec.id_estatusCurso = c.id_estatusCurso
        WHERE
            dc.id_docente = :id_docente
            AND $wherePeriodo
        ORDER BY c.fecha_inicio ASC
    ");
    $stmt->bindValue(':id_docente', $idDocente, PDO::PARAM_INT);
    foreach ($filtro['params'] as $key => $value) {
        if ($key === ':periodo_valor') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } elseif ($key === ':anio') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ───────────────────────────── METAS ─────────────────────────────
// ====================================================================

function getMetasPorAmbito(PDO $conn, int $anio): array {
    $stmt = $conn->prepare("
        SELECT 
            m.id_meta,
            COALESCE(a.nombre_ambito, 'Sin ámbito asignado') AS nombre_ambito,
            m.tipo_objetivo,
            m.meta_objetivo,
            m.estado,
            m.fecha_inicio,
            m.fecha_fin,
            m.periodo_tiempo,
            m.id_ambito
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON a.id_ambito = m.id_ambito
        WHERE (YEAR(m.fecha_inicio) = :anio OR m.fecha_inicio IS NULL)
        ORDER BY COALESCE(a.nombre_ambito, 'ZZZ'), m.tipo_objetivo
    ");
    $stmt->execute([':anio' => $anio]);
    $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($metas as &$meta) {
        $estudiantesFormados = 0;
        $fechaInicio = $meta['fecha_inicio'];
        $fechaFin = $meta['fecha_fin'];
        
        if (empty($fechaInicio)) $fechaInicio = "{$anio}-01-01";
        if (empty($fechaFin)) $fechaFin = "{$anio}-12-31";
        
        if ($meta['tipo_objetivo'] === 'Aprobados') {
            $estudiantesFormados = contarAprobadosPorAmbitoYPeriodo($conn, $meta['id_ambito'], $fechaInicio, $fechaFin);
        } else if ($meta['tipo_objetivo'] === 'Inscritos') {
            $estudiantesFormados = contarInscritosPorAmbitoYPeriodo($conn, $meta['id_ambito'], $fechaInicio, $fechaFin);
        }
        
        $metaObjetivo = intval($meta['meta_objetivo']);
        $porcentaje = $metaObjetivo > 0 ? ($estudiantesFormados / $metaObjetivo) * 100 : 0;
        
        $estadoCumplimiento = 'En progreso';
        if ($porcentaje >= 100) $estadoCumplimiento = 'Cumplida';
        else if ($porcentaje <= 0) $estadoCumplimiento = 'Sin iniciar';
        
        $meta['estudiantes_formados'] = $estudiantesFormados;
        $meta['porcentaje_cumplimiento'] = round($porcentaje, 2);
        $meta['estado_cumplimiento'] = $estadoCumplimiento;
    }
    
    return $metas;
}

function contarAprobadosPorAmbitoYPeriodo(PDO $conn, $idAmbito, string $fechaInicio, string $fechaFin): int {
    if (empty($idAmbito)) {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin]);
    } else {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE c.id_ambito = :id_ambito
              AND pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id_ambito' => $idAmbito, ':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin]);
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return intval($result['total'] ?? 0);
}

function contarInscritosPorAmbitoYPeriodo(PDO $conn, $idAmbito, string $fechaInicio, string $fechaFin): int {
    if (empty($idAmbito)) {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin]);
    } else {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE c.id_ambito = :id_ambito
              AND pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id_ambito' => $idAmbito, ':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin]);
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return intval($result['total'] ?? 0);
}

function getDetalleParticipantesPorMeta(PDO $conn, int $idMeta): array {
    $stmt = $conn->prepare("
        SELECT m.*, a.nombre_ambito
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON a.id_ambito = m.id_ambito
        WHERE m.id_meta = :id_meta
    ");
    $stmt->execute([':id_meta' => $idMeta]);
    $meta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$meta) return [];
    
    $fechaInicio = $meta['fecha_inicio'] ?? date('Y') . '-01-01';
    $fechaFin = $meta['fecha_fin'] ?? date('Y') . '-12-31';
    
    if ($meta['tipo_objetivo'] === 'Aprobados') {
        $sql = "
            SELECT DISTINCT 
                p.id_participante,
                CONCAT(p.primer_nombre, ' ', p.primer_apellido) as nombre_completo,
                p.cedula,
                c.nombre_curso,
                pc.FechaFin as fecha_aprobacion
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE " . (empty($meta['id_ambito']) ? "" : "c.id_ambito = :id_ambito AND") . "
              pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $params = [':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin];
        if (!empty($meta['id_ambito'])) $params[':id_ambito'] = $meta['id_ambito'];
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    } else {
        $sql = "
            SELECT DISTINCT 
                p.id_participante,
                CONCAT(p.primer_nombre, ' ', p.primer_apellido) as nombre_completo,
                p.cedula,
                c.nombre_curso,
                pc.fecha_inscripcion,
                pc.estatus_participante
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE " . (empty($meta['id_ambito']) ? "" : "c.id_ambito = :id_ambito AND") . "
              pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $params = [':fecha_inicio' => $fechaInicio, ':fecha_fin' => $fechaFin];
        if (!empty($meta['id_ambito'])) $params[':id_ambito'] = $meta['id_ambito'];
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ====================================================================
// ─────────────────── UTILIDADES ─────────────────────────────────────
// ====================================================================

function getAniosDisponibles(PDO $conn): array {
    $stmt = $conn->query("
        SELECT DISTINCT anio FROM (
            SELECT YEAR(fecha_registro)  AS anio FROM tb_participante
            UNION SELECT YEAR(fecha_creacion)    AS anio FROM tb_curso
            UNION SELECT YEAR(fecha_asignacion)  AS anio FROM tb_docente_curso WHERE id_docente IS NOT NULL
            UNION SELECT YEAR(fecha_inicio)      AS anio FROM tb_metas
        ) t
        WHERE anio IS NOT NULL AND anio >= 2000 AND anio <= YEAR(CURDATE())
        ORDER BY anio DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return empty($rows) ? [intval(date('Y'))] : array_map('intval', $rows);
}

function buscarParticipantes(PDO $conn, string $q, int $limite): array {
    $stmt = $conn->prepare("
        SELECT CONCAT(primer_nombre, ' ', primer_apellido) AS nombre, cedula, estado
        FROM tb_participante
        WHERE primer_nombre LIKE :q OR primer_apellido LIKE :q OR cedula LIKE :q OR correo LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q', "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
function buscarCursos(PDO $conn, string $q, int $limite): array {
    $stmt = $conn->prepare("
        SELECT c.id_curso, c.nombre_curso AS nombre, e.nombre_estatusCurso AS estatus
        FROM tb_curso c
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        WHERE c.nombre_curso LIKE :q OR c.id_curso LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q', "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
function buscarDocentes(PDO $conn, string $q, int $limite): array {
    $stmt = $conn->prepare("
        SELECT id_docente, primer_nombre, primer_apellido, cedula, tipo_docente, estado_docente
        FROM tb_docente
        WHERE primer_nombre LIKE :q OR primer_apellido LIKE :q OR cedula LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q', "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getResumenGlobal(PDO $conn): array {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_participante");
    $participantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_curso");
    $cursos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_docente");
    $docentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmt = $conn->query("SELECT AVG(porcentaje_cumplimiento) as promedio FROM tb_metas WHERE estado = 'Activo'");
    $promedioMeta = $stmt->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 0;
    return [
        'participantes' => (int)$participantes,
        'cursos' => (int)$cursos,
        'docentes' => (int)$docentes,
        'pct_metas' => round($promedioMeta, 1)
    ];
}

function getTrayectoriaResumen(PDO $conn): array {
    $stmt = $conn->query("SELECT COUNT(*) FROM tb_participante");
    $total = intval($stmt->fetchColumn());
    $stmt = $conn->query("SELECT COUNT(DISTINCT pc.id_participante) FROM tb_participante_curso pc INNER JOIN tb_participante p ON p.id_participante = pc.id_participante");
    $activos = intval($stmt->fetchColumn());
    $sin_curso = max(0, $total - $activos);
    return ['total' => $total, 'activos' => $activos, 'sin_curso' => $sin_curso];
}
 
function getTrayectoriaParticipantes(PDO $conn): array {
    $stmt = $conn->query("
        SELECT p.cedula, p.primer_nombre, p.primer_apellido, p.genero, p.edad, p.correo,
               p.fecha_registro, p.estado, p.grado_institucion,
               (SELECT t.numero_de_telefono FROM tb_telefono t WHERE t.id_participante = p.id_participante AND t.tipo_de_telefono = 'Móvil' ORDER BY t.id_telefono ASC LIMIT 1) AS telefono,
               CASE WHEN EXISTS (SELECT 1 FROM tb_participante_curso pc WHERE pc.id_participante = p.id_participante) THEN 1 ELSE 0 END AS has_curso
        FROM tb_participante p ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDetalleParticipante(PDO $conn, int $cedula): array {
    $stmt = $conn->prepare("
        SELECT p.cedula, p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido,
               p.edad, p.fecha_nacimiento, p.fecha_registro, p.correo, p.estado, p.genero,
               p.grado_institucion, d.nombre_discapacidad
        FROM tb_participante p
        LEFT JOIN tb_discapacidad d ON d.id_discapacidad = p.id_discapacidad
        WHERE p.cedula = :cedula LIMIT 1
    ");
    $stmt->execute([':cedula' => $cedula]);
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$persona) return ['error' => 'Participante no encontrado'];
    
    $stmtId = $conn->prepare("SELECT id_participante FROM tb_participante WHERE cedula = :cedula LIMIT 1");
    $stmtId->execute([':cedula' => $cedula]);
    $idParticipante = intval($stmtId->fetchColumn());
    
    $stmt = $conn->prepare("SELECT numero_de_telefono, tipo_de_telefono FROM tb_telefono WHERE id_participante = :id ORDER BY id_telefono ASC");
    $stmt->execute([':id' => $idParticipante]);
    $persona['telefonos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("
        SELECT c.id_curso, c.nombre_curso, c.turno, pc.fecha_inscripcion,
               pc.FechaInicio AS fecha_inicio, pc.FechaFin AS fecha_fin, pc.estatus_participante,
               e.nombre_estatusCurso AS estatus_curso, a.nombre_ambito, af.nombre_areaFormativa AS area_formativa,
               m.nombre_modalidad, c.horas_totales
        FROM tb_participante_curso pc
        JOIN tb_curso c ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        LEFT JOIN tb_ambito a ON a.id_ambito = c.id_ambito
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa = c.id_areaFormativa
        LEFT JOIN tb_modalidad m ON m.id_modalidad = c.id_modalidad
        WHERE pc.id_participante = :id ORDER BY pc.FechaInicio ASC
    ");
    $stmt->execute([':id' => $idParticipante]);
    $persona['cursos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $persona;
}

// ====================================================================
// ─────────── METAS: FUNCIONES AGREGADAS ─────────────────────────────
// ====================================================================

/**
 * Resumen de metas agrupado por año (todos los años con registros).
 * Salida: anio, total_metas, objetivo_total, alcanzado_total,
 *         porcentaje_promedio, metas_cumplidas
 */
function getMetasPorAnio(PDO $conn): array {
    $stmt = $conn->query("
        SELECT DISTINCT YEAR(fecha_inicio) AS anio
        FROM tb_metas
        WHERE fecha_inicio IS NOT NULL
        ORDER BY anio ASC
    ");
    $anios = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($anios)) return [];

    $resultado = [];
    foreach ($anios as $anio) {
        $anio  = intval($anio);
        $metas = getMetasPorAmbito($conn, $anio);
        if (empty($metas)) continue;

        $totalMetas     = count($metas);
        $objetivoTotal  = array_sum(array_column($metas, 'meta_objetivo'));
        $alcanzadoTotal = array_sum(array_column($metas, 'estudiantes_formados'));
        $sumaPct        = array_sum(array_column($metas, 'porcentaje_cumplimiento'));
        $promedioPct    = $totalMetas > 0 ? round($sumaPct / $totalMetas, 2) : 0;
        $metasCumplidas = count(array_filter($metas, fn($m) => $m['porcentaje_cumplimiento'] >= 100));

        $resultado[] = [
            'anio'                => $anio,
            'total_metas'         => $totalMetas,
            'objetivo_total'      => $objetivoTotal,
            'alcanzado_total'     => $alcanzadoTotal,
            'porcentaje_promedio' => $promedioPct,
            'metas_cumplidas'     => $metasCumplidas,
        ];
    }

    return $resultado;
}

/**
 * Resumen de metas agrupado por trimestre para un año dado.
 * Salida: trimestre, total_metas, objetivo_total, alcanzado_total,
 *         porcentaje_promedio, metas_cumplidas
 */
function getMetasPorTrimestre(PDO $conn, int $anio): array {
    $metas = getMetasPorAmbito($conn, $anio);
    if (empty($metas)) return [];

    $trimestres = [1 => [], 2 => [], 3 => [], 4 => []];
    foreach ($metas as $meta) {
        $fi  = $meta['fecha_inicio'] ?? null;
        $mes = $fi ? intval(date('n', strtotime($fi))) : 1;
        $t   = intval(ceil($mes / 3));
        $trimestres[$t][] = $meta;
    }

    $resultado = [];
    foreach ($trimestres as $t => $lista) {
        if (empty($lista)) continue;

        $totalMetas     = count($lista);
        $objetivoTotal  = array_sum(array_column($lista, 'meta_objetivo'));
        $alcanzadoTotal = array_sum(array_column($lista, 'estudiantes_formados'));
        $sumaPct        = array_sum(array_column($lista, 'porcentaje_cumplimiento'));
        $promedioPct    = $totalMetas > 0 ? round($sumaPct / $totalMetas, 2) : 0;
        $metasCumplidas = count(array_filter($lista, fn($m) => $m['porcentaje_cumplimiento'] >= 100));

        $resultado[] = [
            'trimestre'           => $t,
            'total_metas'         => $totalMetas,
            'objetivo_total'      => $objetivoTotal,
            'alcanzado_total'     => $alcanzadoTotal,
            'porcentaje_promedio' => $promedioPct,
            'metas_cumplidas'     => $metasCumplidas,
        ];
    }

    return $resultado;
}

/**
 * Resumen de metas agrupado por mes para un año dado.
 * Salida: mes, nombre_mes, total_metas, objetivo_total,
 *         alcanzado_total, porcentaje_promedio, metas_cumplidas
 */
function getMetasPorMes(PDO $conn, int $anio): array {
    $nombresMeses = [
        1=>'Enero',    2=>'Febrero',   3=>'Marzo',     4=>'Abril',
        5=>'Mayo',     6=>'Junio',     7=>'Julio',     8=>'Agosto',
        9=>'Septiembre',10=>'Octubre', 11=>'Noviembre',12=>'Diciembre',
    ];

    $metas = getMetasPorAmbito($conn, $anio);
    if (empty($metas)) return [];

    $porMes = [];
    foreach ($metas as $meta) {
        $fi  = $meta['fecha_inicio'] ?? null;
        $mes = $fi ? intval(date('n', strtotime($fi))) : 1;
        $porMes[$mes][] = $meta;
    }

    $resultado = [];
    foreach ($porMes as $mes => $lista) {
        $totalMetas     = count($lista);
        $objetivoTotal  = array_sum(array_column($lista, 'meta_objetivo'));
        $alcanzadoTotal = array_sum(array_column($lista, 'estudiantes_formados'));
        $sumaPct        = array_sum(array_column($lista, 'porcentaje_cumplimiento'));
        $promedioPct    = $totalMetas > 0 ? round($sumaPct / $totalMetas, 2) : 0;
        $metasCumplidas = count(array_filter($lista, fn($m) => $m['porcentaje_cumplimiento'] >= 100));

        $resultado[] = [
            'mes'                 => $mes,
            'nombre_mes'          => $nombresMeses[$mes] ?? "Mes $mes",
            'total_metas'         => $totalMetas,
            'objetivo_total'      => $objetivoTotal,
            'alcanzado_total'     => $alcanzadoTotal,
            'porcentaje_promedio' => $promedioPct,
            'metas_cumplidas'     => $metasCumplidas,
        ];
    }

    usort($resultado, fn($a, $b) => $a['mes'] - $b['mes']);
    return $resultado;
}

/**
 * Progreso detallado de cada meta para un año dado.
 * Mismos campos que getMetasPorAmbito + brecha y excedente calculados.
 */
function getProgresoMetasDetallado(PDO $conn, int $anio): array {
    $metas = getMetasPorAmbito($conn, $anio);
    foreach ($metas as &$m) {
        $obj          = intval($m['meta_objetivo']);
        $alc          = intval($m['estudiantes_formados']);
        $m['brecha']   = max(0, $obj - $alc);
        $m['excedente']= max(0, $alc - $obj);
        $m['cumplida'] = $m['porcentaje_cumplimiento'] >= 100;
    }
    return $metas;
}
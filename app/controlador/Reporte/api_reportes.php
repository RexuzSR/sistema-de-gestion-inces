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
            echo json_encode(getDocentesTotal($conn, $anio));
            break;

        case 'docentes_mas_solicitados':
            $limite = intval($_GET['limite'] ?? 10);
            echo json_encode(getDocentesMasSolicitados($conn, $anio, $limite));
            break;

        case 'docentes_inactivos':
            echo json_encode(getDocentesInactivos($conn));
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
        SUM(CASE WHEN $alias.edad BETWEEN 15 AND 17 THEN 1 ELSE 0 END) AS adolescente,
        SUM(CASE WHEN $alias.edad BETWEEN 18 AND 59 THEN 1 ELSE 0 END) AS adulto,
        SUM(CASE WHEN $alias.edad >= 60             THEN 1 ELSE 0 END) AS adulto_mayor
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
               SUM(CASE WHEN pc.estatus_participante IN ('En formación','Pendiente') THEN 1 ELSE 0 END) AS en_formacion,
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

/**
 * ─── CORRECCIÓN CRÍTICA ─────────────────────────────────────────────────────
 * Se añade c.id_curso al SELECT para que el JS pueda identificar cada curso
 * y cargar sus participantes correctamente.
 */
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

/**
 * Devuelve todos los participantes inscritos en un curso por su id_curso.
 */
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

function getDocentesTotal(PDO $conn, int $anio): array {
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
// ───────────────────────────── METAS ─────────────────────────────
// ====================================================================

function getMetasPorAmbito(PDO $conn, int $anio): array {
    // Primero obtener todas las metas
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
    
    // Para cada meta, calcular el progreso real basado en participantes
    foreach ($metas as &$meta) {
        $estudiantesFormados = 0;
        
        // Determinar las fechas de la meta
        $fechaInicio = $meta['fecha_inicio'];
        $fechaFin = $meta['fecha_fin'];
        
        // Si no hay fechas definidas, usar el año completo
        if (empty($fechaInicio)) {
            $fechaInicio = "{$anio}-01-01";
        }
        if (empty($fechaFin)) {
            $fechaFin = "{$anio}-12-31";
        }
        
        if ($meta['tipo_objetivo'] === 'Aprobados') {
            // Para metas de APROBADOS: contar participantes con estatus 'Aprobado'
            // en cursos del mismo ámbito, dentro del período de la meta
            $estudiantesFormados = contarAprobadosPorAmbitoYPeriodo(
                $conn, 
                $meta['id_ambito'], 
                $fechaInicio, 
                $fechaFin
            );
        } 
        else if ($meta['tipo_objetivo'] === 'Inscritos') {
            // Para metas de INSCRITOS: contar participantes inscritos
            // en cursos del mismo ámbito, dentro del período de la meta
            $estudiantesFormados = contarInscritosPorAmbitoYPeriodo(
                $conn, 
                $meta['id_ambito'], 
                $fechaInicio, 
                $fechaFin
            );
        }
        
        // Calcular porcentaje de cumplimiento
        $metaObjetivo = intval($meta['meta_objetivo']);
        $porcentaje = 0;
        if ($metaObjetivo > 0) {
            $porcentaje = ($estudiantesFormados / $metaObjetivo) * 100;
        }
        
        // Determinar estado de cumplimiento
        $estadoCumplimiento = 'En progreso';
        if ($porcentaje >= 100) {
            $estadoCumplimiento = 'Cumplida';
        } else if ($porcentaje <= 0) {
            $estadoCumplimiento = 'Sin iniciar';
        }
        
        // Asignar valores calculados
        $meta['estudiantes_formados'] = $estudiantesFormados;
        $meta['porcentaje_cumplimiento'] = round($porcentaje, 2);
        $meta['estado_cumplimiento'] = $estadoCumplimiento;
    }
    
    error_log("Metas procesadas para año $anio: " . count($metas));
    return $metas;
}

/**
 * Cuenta participantes APROBADOS en cursos de un ámbito específico dentro de un período
 */
function contarAprobadosPorAmbitoYPeriodo(PDO $conn, $idAmbito, string $fechaInicio, string $fechaFin): int {
    // Si no hay ámbito asignado, buscar metas sin ámbito específico
    if (empty($idAmbito)) {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
    } else {
        // Contar aprobados en cursos del ámbito específico
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE c.id_ambito = :id_ambito
              AND pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id_ambito' => $idAmbito,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
    }
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return intval($result['total'] ?? 0);
}

/**
 * Cuenta participantes INSCRITOS en cursos de un ámbito específico dentro de un período
 * (basado en fecha_inscripcion)
 */
function contarInscritosPorAmbitoYPeriodo(PDO $conn, $idAmbito, string $fechaInicio, string $fechaFin): int {
    // Si no hay ámbito asignado, buscar metas sin ámbito específico
    if (empty($idAmbito)) {
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
    } else {
        // Contar inscritos en cursos del ámbito específico
        $sql = "
            SELECT COUNT(DISTINCT pc.id_participante) as total
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            WHERE c.id_ambito = :id_ambito
              AND pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id_ambito' => $idAmbito,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
    }
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return intval($result['total'] ?? 0);
}

/**
 * Función adicional: Obtiene el desglose de participantes por meta
 * Útil para mostrar detalles en el reporte
 */
function getDetalleParticipantesPorMeta(PDO $conn, int $idMeta): array {
    // Primero obtener la meta
    $stmt = $conn->prepare("
        SELECT m.*, a.nombre_ambito
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON a.id_ambito = m.id_ambito
        WHERE m.id_meta = :id_meta
    ");
    $stmt->execute([':id_meta' => $idMeta]);
    $meta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$meta) {
        return [];
    }
    
    $fechaInicio = $meta['fecha_inicio'] ?? date('Y') . '-01-01';
    $fechaFin = $meta['fecha_fin'] ?? date('Y') . '-12-31';
    
    if ($meta['tipo_objetivo'] === 'Aprobados') {
        // Obtener lista de participantes aprobados
        $sql = "
            SELECT DISTINCT 
                p.id_participante,
                CONCAT(p.primer_nombre, ' ', p.primer_apellido) as nombre_completo,
                p.cedula,
                c.nombre_curso,
                pc.FechaFin as fecha_aprobacion,
                pc.calificacion
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE " . (empty($meta['id_ambito']) ? "" : "c.id_ambito = :id_ambito AND") . "
              pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";
        $params = [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ];
        if (!empty($meta['id_ambito'])) {
            $params[':id_ambito'] = $meta['id_ambito'];
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
    } else {
        // Obtener lista de participantes inscritos
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
        $params = [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ];
        if (!empty($meta['id_ambito'])) {
            $params[':id_ambito'] = $meta['id_ambito'];
        }
        
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



function buscarParticipantes(PDO $conn, string $q, int $limite): array
{
    $stmt = $conn->prepare("
        SELECT
            CONCAT(primer_nombre, ' ', primer_apellido)  AS nombre,
            cedula,
            estado
        FROM tb_participante
        WHERE primer_nombre  LIKE :q
           OR primer_apellido LIKE :q
           OR cedula          LIKE :q
           OR correo          LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q',      "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite,  PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Devuelve: nombre, cedula, estado
    // El JS usa `p.cedula` como valor de ?buscar=
}
 
function buscarCursos(PDO $conn, string $q, int $limite): array
{
    $stmt = $conn->prepare("
        SELECT
            c.id_curso,
            c.nombre_curso              AS nombre,
            e.nombre_estatusCurso       AS estatus
        FROM tb_curso c
        LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso
        WHERE c.nombre_curso LIKE :q
           OR c.id_curso     LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q',      "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite,  PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Devuelve: id_curso, nombre, estatus
    // El JS usa `c.id_curso` como valor de ?buscar=
}
 
function buscarDocentes(PDO $conn, string $q, int $limite): array
{
    $stmt = $conn->prepare("
        SELECT
            CONCAT(primer_nombre, ' ', primer_apellido)  AS nombre,
            cedula,
            tipo_docente,
            estado_docente
        FROM tb_docente
        WHERE primer_nombre  LIKE :q
           OR primer_apellido LIKE :q
           OR cedula          LIKE :q
        LIMIT :limite
    ");
    $stmt->bindValue(':q',      "%{$q}%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite,  PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Devuelve: nombre, cedula, tipo_docente, estado_docente
    // El JS usa `d.cedula` como valor de ?buscar=
}

function getResumenGlobal(PDO $conn): array {
    // Total de participantes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_participante");
    $participantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total de cursos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_curso");
    $cursos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total de docentes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tb_docente");
    $docentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Progreso de metas (porcentaje promedio de cumplimiento)
    $stmt = $conn->query("
        SELECT AVG(porcentaje_cumplimiento) as promedio 
        FROM tb_metas 
        WHERE estado = 'Activo'
    ");
    $promedioMeta = $stmt->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 0;
    
    return [
        'participantes' => (int)$participantes,
        'cursos'        => (int)$cursos,
        'docentes'      => (int)$docentes,
        'pct_metas'     => round($promedioMeta, 1)
    ];
}

/**
 * Resumen de 4 KPI cards para el reporte Trayectoria de Participantes:
 *  - total:       total de participantes registrados en el sistema
 *  - activos:     participantes con estado = 'Activo'
 *  - registrados: participantes que tienen al menos una inscripción en tb_participante_curso
 *  - sin_curso:   participantes SIN ninguna inscripción en tb_participante_curso
 */
function getTrayectoriaResumen(PDO $conn): array {
 
    // Total general
    $stmt  = $conn->query("SELECT COUNT(*) FROM tb_participante");
    $total = intval($stmt->fetchColumn());
 
    // Participantes que existen en tb_participante Y tienen
    // al menos 1 inscripción en tb_participante_curso
    $stmt    = $conn->query("
        SELECT COUNT(DISTINCT pc.id_participante)
        FROM   tb_participante_curso pc
        INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
    ");
    $activos = intval($stmt->fetchColumn());
 
    // Sin ninguna inscripción válida
    $sin_curso = max(0, $total - $activos);
 
    return [
        'total'     => $total,
        'activos'   => $activos,
        'sin_curso' => $sin_curso,
    ];
}
 
/**
 * Lista completa de participantes para la tabla.
 * Incluye has_curso (1/0) para el filtro de cards.
 */
function getTrayectoriaParticipantes(PDO $conn): array {
    $stmt = $conn->query("
        SELECT
            p.cedula,
            p.primer_nombre,
            p.primer_apellido,
            p.genero,
            p.edad,
            p.correo,
            p.fecha_registro,
            p.estado,
            p.grado_institucion,
            (
                SELECT t.numero_de_telefono
                FROM   tb_telefono t
                WHERE  t.id_participante = p.id_participante
                  AND  t.tipo_de_telefono = 'Móvil'
                ORDER BY t.id_telefono ASC
                LIMIT  1
            ) AS telefono,
            CASE WHEN EXISTS (
                SELECT 1
                FROM   tb_participante_curso pc
                WHERE  pc.id_participante = p.id_participante
            ) THEN 1 ELSE 0 END AS has_curso
        FROM  tb_participante p
        ORDER BY p.primer_apellido ASC, p.primer_nombre ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDetalleParticipante(PDO $conn, int $cedula): array {
 
    // ── Datos personales + discapacidad ──────────────────────────────
    $stmt = $conn->prepare("
        SELECT
            p.cedula,
            p.primer_nombre,
            p.segundo_nombre,
            p.primer_apellido,
            p.segundo_apellido,
            p.edad,
            p.fecha_nacimiento,
            p.fecha_registro,
            p.correo,
            p.estado,
            p.genero,
            p.grado_institucion,
            d.nombre_discapacidad
        FROM tb_participante p
        LEFT JOIN tb_discapacidad d ON d.id_discapacidad = p.id_discapacidad
        WHERE p.cedula = :cedula
        LIMIT 1
    ");
    $stmt->execute([':cedula' => $cedula]);
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if (!$persona) return ['error' => 'Participante no encontrado'];
 
    // Obtener id_participante para las demás consultas
    $stmtId = $conn->prepare("SELECT id_participante FROM tb_participante WHERE cedula = :cedula LIMIT 1");
    $stmtId->execute([':cedula' => $cedula]);
    $idParticipante = intval($stmtId->fetchColumn());
 
    // ── Teléfonos ────────────────────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT numero_de_telefono, tipo_de_telefono
        FROM   tb_telefono
        WHERE  id_participante = :id
        ORDER BY id_telefono ASC
    ");
    $stmt->execute([':id' => $idParticipante]);
    $persona['telefonos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    // ── Cursos (para tabla y gráfica Gantt) ──────────────────────────
    $stmt = $conn->prepare("
        SELECT
            c.id_curso,
            c.nombre_curso,
            c.turno,
            pc.fecha_inscripcion,
            pc.FechaInicio       AS fecha_inicio,
            pc.FechaFin          AS fecha_fin,
            pc.estatus_participante,
            e.nombre_estatusCurso AS estatus_curso,
            a.nombre_ambito,
            af.nombre_areaFormativa AS area_formativa,
            m.nombre_modalidad,
            c.horas_totales
        FROM   tb_participante_curso pc
        JOIN   tb_curso c  ON c.id_curso = pc.id_curso
        LEFT JOIN tb_estatuscurso  e  ON e.id_estatusCurso  = c.id_estatusCurso
        LEFT JOIN tb_ambito        a  ON a.id_ambito        = c.id_ambito
        LEFT JOIN tb_areaformativa af ON af.id_areaFormativa = c.id_areaFormativa
        LEFT JOIN tb_modalidad     m  ON m.id_modalidad      = c.id_modalidad
        WHERE  pc.id_participante = :id
        ORDER BY pc.FechaInicio ASC
    ");
    $stmt->execute([':id' => $idParticipante]);
    $persona['cursos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    return $persona;
}
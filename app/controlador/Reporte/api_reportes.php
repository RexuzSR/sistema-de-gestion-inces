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

        // ── NUEVO: participantes de un curso específico ───────────────────────
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
// ─────────────────── METAS ──────────────────────────────────────────
// ====================================================================

function getMetasPorAmbito(PDO $conn, int $anio): array {
    $dynamicCount = "(
        CASE 
            WHEN m.tipo_objetivo = 'Aprobados' THEN (
                SELECT COUNT(DISTINCT pc2.id_participante)
                FROM tb_participante_curso pc2
                INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                WHERE pc2.estatus_participante = 'Aprobado'
                  AND pc2.FechaFin BETWEEN m.fecha_inicio AND m.fecha_fin
                  AND (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito)
            )
            WHEN m.tipo_objetivo = 'Inscritos' THEN (
                SELECT COUNT(DISTINCT pc2.id_participante)
                FROM tb_participante_curso pc2
                INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                WHERE pc2.fecha_inscripcion BETWEEN m.fecha_inicio AND m.fecha_fin
                  AND (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito)
            )
            WHEN m.tipo_objetivo = 'Ingresados' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            WHEN m.tipo_objetivo = 'Masculino' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.genero = 'Masculino'
                  AND p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            WHEN m.tipo_objetivo = 'Femenino' THEN (
                SELECT COUNT(DISTINCT p.id_participante)
                FROM tb_participante p
                WHERE p.genero = 'Femenino'
                  AND p.fecha_registro BETWEEN m.fecha_inicio AND m.fecha_fin
            )
            ELSE 0
        END
    )";

    $stmt = $conn->prepare("
        SELECT a.nombre_ambito, m.tipo_objetivo, m.meta_objetivo, 
               {$dynamicCount} AS estudiantes_formados,
               CASE WHEN m.meta_objetivo = 0 THEN 0
                    ELSE ROUND(({$dynamicCount})/m.meta_objetivo*100, 2)
               END AS porcentaje_cumplimiento,
               CASE WHEN ({$dynamicCount}) >= m.meta_objetivo THEN 'Cumplida'
                    ELSE 'En progreso'
               END AS estado_cumplimiento, 
               m.estado, m.fecha_inicio, m.fecha_fin
        FROM tb_metas m
        LEFT JOIN tb_ambito a ON a.id_ambito = m.id_ambito
        WHERE YEAR(m.fecha_inicio) = :anio
        ORDER BY a.nombre_ambito, m.tipo_objetivo
    ");
    $stmt->execute([':anio' => $anio]);
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
    $stmt = $conn->prepare("SELECT CONCAT(primer_nombre,' ',primer_apellido) AS nombre, cedula, estado AS estado FROM tb_participante WHERE primer_nombre LIKE :q OR primer_apellido LIKE :q OR cedula LIKE :q LIMIT :limite");
    $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarCursos(PDO $conn, string $q, int $limite): array {
    $stmt = $conn->prepare("SELECT c.nombre_curso AS nombre, e.nombre_estatusCurso AS estatus FROM tb_curso c LEFT JOIN tb_estatuscurso e ON e.id_estatusCurso = c.id_estatusCurso WHERE c.nombre_curso LIKE :q LIMIT :limite");
    $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscarDocentes(PDO $conn, string $q, int $limite): array {
    $stmt = $conn->prepare("SELECT CONCAT(primer_nombre,' ',primer_apellido) AS nombre, tipo_docente, estado_docente FROM tb_docente WHERE primer_nombre LIKE :q OR primer_apellido LIKE :q LIMIT :limite");
    $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
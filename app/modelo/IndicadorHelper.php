<?php
/**
 * ============================================================
 *  IndicadorHelper.php
 *  Clase Helper para el Cuadro de Mando Integral (Balanced Scorecard)
 *  de la Oficina de Informática y Telemática del INCES.
 * ============================================================
 */

class IndicadorHelper {
    
    // Configuración en memoria para los semáforos de los indicadores
    public static $CONFIG = [
        'PP_UTIL_CUPOS' => [
            'nombre' => '% de utilización de cupos por formación',
            'lo' => 70.0,
            'hi' => 85.0,
            'inv' => false,
            'meta' => '≥ 85% de uso real'
        ],
        'US_PART_DISP' => [
            'nombre' => '% Participantes disponibles sin asignación',
            'lo' => 20.0,
            'hi' => 30.0,
            'inv' => true,
            'meta' => '< 20%'
        ],
        'US_CUMP_METAS' => [
            'nombre' => '% tasa de cumplimiento de metas por ámbitos',
            'lo' => 80.0,
            'hi' => 90.0,
            'inv' => false,
            'meta' => '100% (cumplimiento exacto del plan)'
        ],
        'US_FORM_CANCEL' => [
            'nombre' => '% de formación cancelada',
            'lo' => 15.0,
            'hi' => 30.0,
            'inv' => true,
            'meta' => '< 30% anual'
        ],
        'PI_FORM_SOLICITADA' => [
            'nombre' => '% formaciones más solicitadas por mes',
            'lo' => 3, // N° de formaciones en el top 5 que representan significación
            'hi' => 5,
            'inv' => false,
            'meta' => '5 del top 5'
        ],
        'PI_ASIG_EFECTIVA' => [
            'nombre' => 'Índice de asignación efectiva',
            'lo' => 70.0,
            'hi' => 85.0,
            'inv' => false,
            'meta' => '≥ 85%'
        ],
        'PI_AMBITO_SOLICITADO' => [
            'nombre' => '% ámbito de formación más solicitado por mes',
            'lo' => 70.0,
            'hi' => 95.0,
            'inv' => false,
            'meta' => '≥ 95% de inscritos en los 3 ámbitos prioritarios'
        ],
        'PI_PROM_PARTICIPANTES' => [
            'nombre' => 'Promedio de participantes por formación',
            'lo' => 70.0,
            'hi' => 90.0,
            'inv' => false,
            'meta' => '≥ 90% de ocupación del cupo'
        ],
        'PI_PROG_PLANIFICADOS' => [
            'nombre' => '% Porcentaje de Programas Ejecutados Según Planificación',
            'lo' => 75.0,
            'hi' => 85.0,
            'inv' => false,
            'meta' => '≥ 85%'
        ],
        'PI_FORM_AMBITO_MENS' => [
            'nombre' => 'Cantidad de Formaciones por ámbito mensual',
            'lo' => 10,
            'hi' => 12,
            'inv' => false,
            'meta' => '> 10'
        ],
        'PI_FORM_AMBITO_TRIM' => [
            'nombre' => 'Cantidad de Formaciones por ámbito trimestral',
            'lo' => 30,
            'hi' => 45,
            'inv' => false,
            'meta' => '> 30'
        ],
        'AC_OBJ_PARTICIPANTES_ANUAL' => [
            'nombre' => '% tasa de cumplimiento del objetivo de participantes anuales',
            'lo' => 80.0,
            'hi' => 90.0,
            'inv' => false,
            'meta' => '≥ 90% del objetivo anual'
        ],
        'AC_PART_FORMACION' => [
            'nombre' => '% Participantes en formación',
            'lo' => 60.0,
            'hi' => 80.0,
            'inv' => false,
            'meta' => '≥ 80%'
        ]
    ];

    /**
     * Evalúa el semáforo para un indicador y devuelve su color y leyenda.
     */
    public static function evaluarSemaforo($codigo, $valor) {
        if (!isset(self::$CONFIG[$codigo])) {
            return ['alerta' => 'gris', 'leyenda' => 'No configurado', 'meta' => '—', 'nombre' => $codigo];
        }
        
        $conf = self::$CONFIG[$codigo];
        $lo = $conf['lo'];
        $hi = $conf['hi'];
        $inv = $conf['inv'];

        if (!$inv) {
            $alerta = $valor >= $hi ? 'verde' : ($valor >= $lo ? 'amarillo' : 'rojo');
        } else {
            $alerta = $valor <= $lo ? 'verde' : ($valor <= $hi ? 'amarillo' : 'rojo');
        }

        $leyendas = [
            'verde' => 'Deseable',
            'amarillo' => 'Precaución',
            'rojo' => 'Peligro',
            'gris' => 'Sin Datos'
        ];

        return [
            'alerta' => $alerta,
            'leyenda' => $leyendas[$alerta],
            'meta' => $conf['meta'],
            'nombre' => $conf['nombre']
        ];
    }

    /**
     * PERSPECTIVA: PLANIFICACIÓN Y PRESUPUESTO
     * % de utilización de cupos por formación (Mensual)
     */
    public static function getUtilizacionCupos($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');
        
        $sql = "SELECT COALESCE(SUM(num_inscritos), 0) AS i,
                       COALESCE(SUM(max_participantes), 0) AS q
                FROM tb_curso
                WHERE id_curso != 1
                  AND MONTH(fecha_inicio) = :mes
                  AND YEAR(fecha_inicio) = :anio";
        $st = $conn->prepare($sql);
        $st->execute([':mes' => $mes, ':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        
        // Fallback a los cursos activos del año si no hay datos este mes
        if ($r['q'] == 0) {
            $sqlFallback = "SELECT COALESCE(SUM(num_inscritos), 0) AS i,
                                   COALESCE(SUM(max_participantes), 0) AS q
                            FROM tb_curso
                            WHERE id_curso != 1 AND id_estatusCurso IN (1, 2, 3, 4)";
            $r = $conn->query($sqlFallback)->fetch(PDO::FETCH_ASSOC);
        }

        $pct = $r['q'] > 0 ? round($r['i'] / $r['q'] * 100, 1) : 0.0;
        return ['inscritos' => (int)$r['i'], 'cupos' => (int)$r['q'], 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: USUARIOS - INDICADOR 1
     * % Participantes disponibles sin asignación (Mensual)
     */
    public static function getParticipantesDisponiblesSinAsignacion($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sqlTotal = "SELECT COUNT(*) FROM tb_participante WHERE MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stTotal = $conn->prepare($sqlTotal);
        $stTotal->execute([':mes' => $mes, ':anio' => $anio]);
        $tot = (int)$stTotal->fetchColumn();

        $sqlDisp = "SELECT COUNT(*) FROM tb_participante WHERE estado = 'Disponible' AND MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stDisp = $conn->prepare($sqlDisp);
        $stDisp->execute([':mes' => $mes, ':anio' => $anio]);
        $d = (int)$stDisp->fetchColumn();

        // Fallback al total general histórico si no hay nuevos registrados en el mes
        if ($tot === 0) {
            $tot = (int)$conn->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
            $d = (int)$conn->query("SELECT COUNT(*) FROM tb_participante WHERE estado = 'Disponible'")->fetchColumn();
        }

        $pct = $tot > 0 ? round($d / $tot * 100, 1) : 0.0;
        return ['disponibles' => $d, 'total' => $tot, 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: USUARIOS - INDICADOR 2
     * % Tasa de cumplimiento de metas por ámbitos (Mensual)
     */
    public static function getCumplimientoMetasAmbito($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT COALESCE(ROUND(AVG(porcentaje_cumplimiento), 1), 0.0) AS pct
                FROM tb_metas
                WHERE id_ambito IS NOT NULL
                  AND YEAR(fecha_inicio) = :anio
                  AND MONTH(fecha_inicio) <= :mes
                  AND MONTH(fecha_fin) >= :mes";
        $st = $conn->prepare($sql);
        $st->execute([':mes' => $mes, ':anio' => $anio]);
        $pct = (float)$st->fetchColumn();

        return ['pct' => $pct];
    }

    /**
     * PERSPECTIVA: USUARIOS - INDICADOR 3
     * % de formación cancelada (Trimestral/Mensual)
     */
    public static function getFormacionCancelada($conn, $trimestre = null, $anio = null) {
        $trimestre = $trimestre ?? (int)ceil(date('n') / 3);
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT 
                    COUNT(CASE WHEN id_estatusCurso IN (2, 6) THEN 1 END) AS cancelados,
                    COUNT(*) AS total
                FROM tb_curso
                WHERE id_curso != 1
                  AND QUARTER(fecha_inicio) = :trimestre
                  AND YEAR(fecha_inicio) = :anio";
        $st = $conn->prepare($sql);
        $st->execute([':trimestre' => $trimestre, ':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        
        $pct = $r['total'] > 0 ? round($r['cancelados'] / $r['total'] * 100, 1) : 0.0;
        return ['cancelados' => (int)$r['cancelados'], 'total' => (int)$r['total'], 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 1
     * % formaciones más solicitadas por mes (Mensual)
     * Fórmula: (N° inscripciones en formación X / Total inscripciones del mes) × 100
     */
    public static function getFormacionesMasSolicitadas($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');
        
        $sqlTotal = "SELECT COUNT(*) FROM tb_participante_curso 
                     WHERE MONTH(fecha_inscripcion) = :mes AND YEAR(fecha_inscripcion) = :anio";
        $stTotal = $conn->prepare($sqlTotal);
        $stTotal->execute([':mes' => $mes, ':anio' => $anio]);
        $totalInsc = (int)$stTotal->fetchColumn();

        if ($totalInsc === 0) {
            return ['top5' => [], 'cumplen' => 0, 'total_inscripciones' => 0];
        }

        $sqlCursos = "SELECT c.nombre_curso, COUNT(pc.id_participante) AS inscritos
                      FROM tb_participante_curso pc
                      JOIN tb_curso c ON pc.id_curso = c.id_curso
                      WHERE MONTH(pc.fecha_inscripcion) = :mes AND YEAR(pc.fecha_inscripcion) = :anio
                      GROUP BY c.id_curso, c.nombre_curso
                      ORDER BY inscritos DESC
                      LIMIT 5";
        $stCursos = $conn->prepare($sqlCursos);
        $stCursos->execute([':mes' => $mes, ':anio' => $anio]);
        $top5 = $stCursos->fetchAll(PDO::FETCH_ASSOC);

        $cumplen = 0;
        foreach ($top5 as &$item) {
            $item['pct'] = round(($item['inscritos'] / $totalInsc) * 100, 1);
            // Si el curso acumula al menos el 5% de las solicitudes totales, lo consideramos relevante.
            // Para la regla semafórica: deseable = 5 del top 5 cumplen significación.
            if ($item['pct'] >= 5.0) {
                $cumplen++;
            }
        }
        unset($item);

        return [
            'top5' => $top5,
            'cumplen' => $cumplen,
            'total_inscripciones' => $totalInsc
        ];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 2
     * Índice de asignación efectiva (Mensual)
     */
    public static function getAsignacionEfectiva($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sqlTotal = "SELECT COUNT(*) FROM tb_participante WHERE MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stTotal = $conn->prepare($sqlTotal);
        $stTotal->execute([':mes' => $mes, ':anio' => $anio]);
        $tot = (int)$stTotal->fetchColumn();

        $sqlAsig = "SELECT COUNT(*) FROM tb_participante 
                    WHERE (estado = 'Asignado' OR LOWER(estado) LIKE '%en form%') 
                      AND MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stAsig = $conn->prepare($sqlAsig);
        $stAsig->execute([':mes' => $mes, ':anio' => $anio]);
        $asig = (int)$stAsig->fetchColumn();

        // Fallback al histórico general si no hay registros en ese mes
        if ($tot === 0) {
            $tot = (int)$conn->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
            $asig = (int)$conn->query("SELECT COUNT(*) FROM tb_participante WHERE estado = 'Asignado' OR LOWER(estado) LIKE '%en form%'")->fetchColumn();
        }

        $pct = $tot > 0 ? round($asig / $tot * 100, 1) : 0.0;
        return ['activos' => $asig, 'total' => $tot, 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 3
     * % ámbito de formación más solicitado por mes (Mensual)
     * Meta: ≥ 95% de inscritos en los 3 ámbitos prioritarios del mes
     */
    public static function getAmbitoMasSolicitado($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sqlTotal = "SELECT COUNT(*) FROM tb_participante_curso 
                     WHERE MONTH(fecha_inscripcion) = :mes AND YEAR(fecha_inscripcion) = :anio";
        $stTotal = $conn->prepare($sqlTotal);
        $stTotal->execute([':mes' => $mes, ':anio' => $anio]);
        $totalInsc = (int)$stTotal->fetchColumn();

        if ($totalInsc === 0) {
            return ['pct' => 0.0, 'total_inscritos' => 0, 'inscritos_top3' => 0];
        }

        $sqlAmbitos = "SELECT c.id_ambito, COUNT(pc.id_participante) AS inscripciones
                       FROM tb_participante_curso pc
                       JOIN tb_curso c ON pc.id_curso = c.id_curso
                       WHERE MONTH(pc.fecha_inscripcion) = :mes AND YEAR(pc.fecha_inscripcion) = :anio
                       GROUP BY c.id_ambito
                       ORDER BY inscripciones DESC
                       LIMIT 3";
        $stAmbitos = $conn->prepare($sqlAmbitos);
        $stAmbitos->execute([':mes' => $mes, ':anio' => $anio]);
        $top3 = $stAmbitos->fetchAll(PDO::FETCH_ASSOC);

        $inscritosTop3 = 0;
        foreach ($top3 as $t) {
            $inscritosTop3 += (int)$t['inscripciones'];
        }

        $pct = round(($inscritosTop3 / $totalInsc) * 100, 1);
        return ['pct' => $pct, 'total_inscritos' => $totalInsc, 'inscritos_top3' => $inscritosTop3];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 4
     * Promedio de participantes por formación
     * Meta: ≥ 90 % de ocupación del cupo
     */
    public static function getPromedioParticipantes($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT 
                    COALESCE(SUM(num_inscritos), 0) AS inscritos,
                    COALESCE(SUM(max_participantes), 0) AS cupos
                FROM tb_curso
                WHERE id_curso != 1
                  AND MONTH(fecha_inicio) = :mes
                  AND YEAR(fecha_inicio) = :anio";
        $st = $conn->prepare($sql);
        $st->execute([':mes' => $mes, ':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);

        if ($r['cupos'] == 0) {
            $sqlFallback = "SELECT 
                                COALESCE(SUM(num_inscritos), 0) AS inscritos,
                                COALESCE(SUM(max_participantes), 0) AS cupos
                            FROM tb_curso
                            WHERE id_curso != 1 AND id_estatusCurso IN (1, 2, 3, 4)";
            $r = $conn->query($sqlFallback)->fetch(PDO::FETCH_ASSOC);
        }

        $pct = $r['cupos'] > 0 ? round($r['inscritos'] / $r['cupos'] * 100, 1) : 0.0;
        return ['pct' => $pct];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 5
     * % Porcentaje de Programas Ejecutados Según Planificación (Trimestral)
     */
    public static function getProgramasEjecutadosPlanificados($conn, $trimestre = null, $anio = null) {
        $trimestre = $trimestre ?? (int)ceil(date('n') / 3);
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT 
                    COUNT(*) AS planificados,
                    COUNT(CASE WHEN id_estatusCurso IN (3, 5) THEN 1 END) AS ejecutados
                FROM tb_curso
                WHERE id_curso != 1
                  AND QUARTER(fecha_inicio) = :trimestre
                  AND YEAR(fecha_inicio) = :anio";
        $st = $conn->prepare($sql);
        $st->execute([':trimestre' => $trimestre, ':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);

        $pct = $r['planificados'] > 0 ? round($r['ejecutados'] / $r['planificados'] * 100, 1) : 0.0;
        return ['planificados' => (int)$r['planificados'], 'ejecutados' => (int)$r['ejecutados'], 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 6
     * % cantidad de Formaciones por ámbito mensual
     */
    public static function getCantidadFormacionesAmbitoMensual($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito, COUNT(*) AS total
                FROM tb_curso c
                LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                WHERE c.id_curso != 1
                  AND MONTH(c.fecha_inicio) = :mes
                  AND YEAR(c.fecha_inicio) = :anio
                GROUP BY c.id_ambito, a.nombre_ambito
                ORDER BY total DESC";
        $st = $conn->prepare($sql);
        $st->execute([':mes' => $mes, ':anio' => $anio]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $max = 0;
        foreach ($rows as $r) {
            if ($r['total'] > $max) {
                $max = $r['total'];
            }
        }
        return ['list' => $rows, 'max' => $max];
    }

    /**
     * PERSPECTIVA: PROCESOS INTERNOS - INDICADOR 7
     * % cantidad de Formaciones por ámbito trimestral
     */
    public static function getCantidadFormacionesAmbitoTrimestral($conn, $trimestre = null, $anio = null) {
        $trimestre = $trimestre ?? (int)ceil(date('n') / 3);
        $anio = $anio ?? (int)date('Y');

        $sql = "SELECT COALESCE(a.nombre_ambito, 'Sin ámbito') AS ambito, COUNT(*) AS total
                FROM tb_curso c
                LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                WHERE c.id_curso != 1
                  AND QUARTER(c.fecha_inicio) = :trimestre
                  AND YEAR(c.fecha_inicio) = :anio
                GROUP BY c.id_ambito, a.nombre_ambito
                ORDER BY total DESC";
        $st = $conn->prepare($sql);
        $st->execute([':trimestre' => $trimestre, ':anio' => $anio]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $max = 0;
        foreach ($rows as $r) {
            if ($r['total'] > $max) {
                $max = $r['total'];
            }
        }
        return ['list' => $rows, 'max' => $max];
    }

    /**
     * PERSPECTIVA: APRENDIZAJE Y CRECIMIENTO - INDICADOR 1
     * % tasa de cumplimiento del plan de capacitación anual
     */
    public static function getCumplimientoCapacitacionesAnual($conn, $anio = null) {
        $anio = $anio ?? (int)date('Y');
        
        $sql = "SELECT 
                    COUNT(*) AS planificadas,
                    COUNT(CASE WHEN id_estatusCurso IN (3, 5) THEN 1 END) AS ejecutadas
                FROM tb_curso
                WHERE id_curso != 1
                  AND YEAR(fecha_inicio) = :anio";
        $st = $conn->prepare($sql);
        $st->execute([':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);

        $pct = $r['planificadas'] > 0 ? round($r['ejecutadas'] / $r['planificadas'] * 100, 1) : 0.0;
        return ['planificadas' => (int)$r['planificadas'], 'ejecutadas' => (int)$r['ejecutadas'], 'pct' => $pct];
    }

    /**
     * PERSPECTIVA: APRENDIZAJE Y CRECIMIENTO - INDICADOR AUX
     * % tasa de cumplimiento del objetivo de participantes anuales
     */
    public static function getCumplimientoParticipantesAnual($conn, $anio = null) {
        $anio = $anio ?? (int)date('Y');
        
        $sql = "SELECT porcentaje_cumplimiento, meta_objetivo, estudiantes_formados
                FROM tb_metas
                WHERE estado = 'Activa'
                  AND YEAR(fecha_inicio) = :anio
                  AND tipo_objetivo = 'Inscritos'
                  AND id_ambito IS NULL
                ORDER BY meta_objetivo DESC
                LIMIT 1";
        $st = $conn->prepare($sql);
        $st->execute([':anio' => $anio]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        
        if ($r) {
            return [
                'pct' => (float)$r['porcentaje_cumplimiento'],
                'objetivo' => (int)$r['meta_objetivo'],
                'real' => (int)$r['estudiantes_formados']
            ];
        }
        return ['pct' => 0.0, 'objetivo' => 0, 'real' => 0];
    }

    /**
     * PROMEDIO DE PARTICIPANTES POR FORMACIÓN (Global)
     */
    public static function getPromedioParticipantesPorFormacion($conn) {
        $sql = "SELECT AVG(sub.inscritos_reales) AS prom 
                FROM (
                    SELECT c.id_curso, COUNT(pc.id_participante) as inscritos_reales
                    FROM tb_curso c
                    LEFT JOIN tb_participante_curso pc ON c.id_curso = pc.id_curso
                    WHERE c.id_curso != 1
                    GROUP BY c.id_curso
                ) sub";
        $r = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $r ? round((float)$r['prom'], 1) : 0.0;
    }

    /**
     * DETALLE DE PROMEDIO DE PARTICIPANTES POR FORMACIÓN (Lista)
     */
    public static function getDetallePromedioPorFormacion($conn) {
        $sql = "SELECT c.nombre_curso, 
                       COUNT(DISTINCT c.id_curso) as veces_dictado, 
                       ROUND(AVG(sub.inscritos_reales), 1) as prom_participantes,
                       ROUND(AVG(c.max_participantes), 1) as prom_cupos,
                       SUM(sub.inscritos_reales) as total_participantes 
                FROM tb_curso c
                LEFT JOIN (
                    SELECT id_curso, COUNT(id_participante) as inscritos_reales
                    FROM tb_participante_curso
                    GROUP BY id_curso
                ) sub ON c.id_curso = sub.id_curso
                WHERE c.id_curso != 1 
                GROUP BY c.nombre_curso 
                ORDER BY prom_participantes DESC, veces_dictado DESC";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * PERSPECTIVA: APRENDIZAJE Y CRECIMIENTO - INDICADOR 2
     * % Participantes en formación (Mensual)
     */
    public static function getParticipantesEnFormacion($conn, $mes = null, $anio = null) {
        $mes = $mes ?? (int)date('n');
        $anio = $anio ?? (int)date('Y');

        $sqlTotal = "SELECT COUNT(*) FROM tb_participante WHERE MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stTotal = $conn->prepare($sqlTotal);
        $stTotal->execute([':mes' => $mes, ':anio' => $anio]);
        $tot = (int)$stTotal->fetchColumn();

        $sqlForm = "SELECT COUNT(*) FROM tb_participante 
                    WHERE LOWER(estado) LIKE '%en form%' 
                      AND MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio";
        $stForm = $conn->prepare($sqlForm);
        $stForm->execute([':mes' => $mes, ':anio' => $anio]);
        $form = (int)$stForm->fetchColumn();

        // Fallback al histórico general si no hay registros este mes
        if ($tot === 0) {
            $tot = (int)$conn->query("SELECT COUNT(*) FROM tb_participante")->fetchColumn();
            $form = (int)$conn->query("SELECT COUNT(*) FROM tb_participante WHERE LOWER(estado) LIKE '%en form%'")->fetchColumn();
        }

        $pct = $tot > 0 ? round($form / $tot * 100, 1) : 0.0;
        return ['en_formacion' => $form, 'total' => $tot, 'pct' => $pct];
    }
}
?>

<?php
require_once __DIR__ . '/../../modelo/conexion.php';

class Metas {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    /**
     * Cierra automáticamente las metas cuya fecha_fin ya pasó y aún están Activas.
     * Se llama antes de cada consulta para mantener el estado siempre actualizado.
     */
    private function cerrarMetasVencidas() {
        try {
            // Sincronizar estados: si la fecha fin ya pasó, forzar a 'Cerrada'
            $sql = "UPDATE tb_metas
                       SET estado = 'Cerrada'
                     WHERE estado != 'Cerrada'
                       AND fecha_fin < CURDATE()";
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Error en cerrarMetasVencidas: " . $e->getMessage());
        }
    }

    public function obtenerMetas($params = []) {
        $this->cerrarMetasVencidas(); // Sincronizar estados antes de consultar
        try {
            // Si m.id_ambito IS NULL => meta Global: suma de todos los cursos.
            // Si m.id_ambito tiene valor => solo cuenta participantes de cursos de ese ámbito.
            // Soporta tipo_objetivo: 'Aprobados' (estatus=Aprobado, por FechaFin) o 'Inscritos' (por fecha_inscripcion).
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

            $sql = "SELECT m.id_meta, m.meta_objetivo, m.id_ambito, m.tipo_objetivo,
                        COALESCE(a.nombre_ambito, 'Global') AS ambito_nombre,
                        {$dynamicCount} AS estudiantes_formados,
                        CASE WHEN m.meta_objetivo = 0 THEN 0
                             ELSE ROUND(({$dynamicCount})/m.meta_objetivo*100, 2)
                        END AS porcentaje_cumplimiento,
                        CASE WHEN ({$dynamicCount}) >= m.meta_objetivo THEN 'Cumplida'
                             WHEN m.estado = 'Cerrada' OR m.fecha_fin < CURDATE() THEN 'No cumplida'
                             ELSE 'En progreso'
                        END AS estado_cumplimiento,
                        DATE_FORMAT(m.fecha_inicio, '%Y-%m-%d') as fecha_inicio,
                        DATE_FORMAT(m.fecha_fin, '%Y-%m-%d') as fecha_fin,
                        m.estado, m.created_at
                    FROM tb_metas m
                    LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
                    ORDER BY m.id_meta DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerMetas: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerPorId($id) {
        try {
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

            $sql = "SELECT m.id_meta, m.meta_objetivo, m.id_ambito, m.tipo_objetivo,
                        COALESCE(a.nombre_ambito, 'Global') AS ambito_nombre,
                        {$dynamicCount} AS estudiantes_formados,
                        CASE WHEN m.meta_objetivo = 0 THEN 0
                             ELSE ROUND(({$dynamicCount})/m.meta_objetivo*100, 2)
                        END AS porcentaje_cumplimiento,
                        CASE WHEN ({$dynamicCount}) >= m.meta_objetivo THEN 'Cumplida'
                             WHEN m.estado = 'Cerrada' OR m.fecha_fin < CURDATE() THEN 'No cumplida'
                             ELSE 'En progreso'
                        END AS estado_cumplimiento,
                        DATE_FORMAT(m.fecha_inicio, '%Y-%m-%d') as fecha_inicio,
                        DATE_FORMAT(m.fecha_fin, '%Y-%m-%d') as fecha_fin,
                        m.estado, m.created_at
                    FROM tb_metas m
                    LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito
                    WHERE m.id_meta = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            throw $e;
        }
    }

    public function registrar($data) {
        try {
            if (!empty($data['fecha_inicio']) && strtotime($data['fecha_inicio']) < strtotime(date('Y-m-d'))) {
                return ['success' => false, 'message' => 'No se puede crear una meta con fecha de inicio anterior al día de hoy.'];
            }

            // Determinamos el estado automáticamente basado en la fecha fin
            $estado = 'Activa';
            if (!empty($data['fecha_fin']) && strtotime($data['fecha_fin']) < strtotime(date('Y-m-d'))) {
                $estado = 'Cerrada';
            }

            // No guardamos estudiantes_formados aquí: se calcula dinámicamente
            $sql = "INSERT INTO tb_metas (meta_objetivo, estudiantes_formados, tipo_objetivo, fecha_inicio, fecha_fin, estado, id_ambito) VALUES (:meta_objetivo, 0, :tipo_objetivo, :fecha_inicio, :fecha_fin, :estado, :id_ambito)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':meta_objetivo' => $data['meta_objetivo'] ?? null,
                ':tipo_objetivo' => $data['tipo_objetivo'] ?? 'Aprobados',
                ':fecha_inicio' => $data['fecha_inicio'] ?? null,
                ':fecha_fin' => $data['fecha_fin'] ?? null,
                ':estado' => $estado,
                ':id_ambito' => isset($data['id_ambito']) && $data['id_ambito'] !== '' ? $data['id_ambito'] : null
            ]);

            return [
                'success' => true,
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar Meta: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function actualizar($data) {
        try {
            // Verificar estado actual
            $stmtCheck = $this->conn->prepare("SELECT estado, fecha_fin FROM tb_metas WHERE id_meta = :id");
            $stmtCheck->execute([':id' => $data['id_meta']]);
            $currentMeta = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$currentMeta) {
                return ['success' => false, 'message' => 'Meta no encontrada.'];
            }

            // Validar si la meta está vencida
            if (strtotime($currentMeta['fecha_fin']) < strtotime(date('Y-m-d'))) {
                return ['success' => false, 'message' => 'No se puede editar una meta que ya ha vencido.'];
            }

            // Si ya está cerrada, no permitir reabrirla (a menos que estemos extendiendo la fecha en el futuro, pero el usuario dijo "no reabrirse")
            // Si el estado enviado es 'Activa' pero en BD es 'Cerrada', bloqueamos.
            // Dado que quitaremos el selector de estado, el backend debe decidir.
            
            $nuevoEstado = $currentMeta['estado']; // Mantener estado actual por defecto
            
            // Si la fecha fin es hoy o futura, y está activa, se mantiene activa.
            // Si la fecha fin ya pasó, forzamos cerrada.
            if (!empty($data['fecha_fin']) && strtotime($data['fecha_fin']) < strtotime(date('Y-m-d'))) {
                $nuevoEstado = 'Cerrada';
            } else if ($currentMeta['estado'] === 'Cerrada' && strtotime($data['fecha_fin'] ?? $currentMeta['fecha_fin']) >= strtotime(date('Y-m-d'))) {
                 // Si intentan editar una cerrada dándole más tiempo... ¿se permite?
                 // El requerimiento dice "una meta ya cerrada no pueda reabrirse".
                 // Interpretamos que "Cerrada" es un estado final.
                 return ['success' => false, 'message' => 'No se puede reabrir ni editar una meta que ya ha sido cerrada.'];
            }

            $sql = "UPDATE tb_metas SET meta_objetivo = :meta_objetivo, tipo_objetivo = :tipo_objetivo, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado, id_ambito = :id_ambito WHERE id_meta = :id_meta";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':meta_objetivo' => $data['meta_objetivo'] ?? null,
                ':tipo_objetivo' => $data['tipo_objetivo'] ?? 'Aprobados',
                ':fecha_inicio' => $data['fecha_inicio'] ?? null,
                ':fecha_fin' => $data['fecha_fin'] ?? null,
                ':estado' => $nuevoEstado,
                ':id_ambito' => isset($data['id_ambito']) && $data['id_ambito'] !== '' ? $data['id_ambito'] : null,
                ':id_meta' => $data['id_meta']
            ]);

            return ['success' => true, 'rows' => $stmt->rowCount()];
        } catch (PDOException $e) {
            error_log("Error en actualizar Meta: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lista los participantes que cuentan para el cumplimiento de una meta
     * (misma lógica que el conteo dinámico de estudiantes_formados).
     */
    public function obtenerParticipantesPorMeta($id) {
        $meta = $this->obtenerPorId($id);
        if (!$meta) {
            return null;
        }

        $tipo = $meta['tipo_objetivo'] ?? 'Aprobados';
        $idAmbito = $meta['id_ambito'] ?? null;
        $fechaInicio = $meta['fecha_inicio'];
        $fechaFin = $meta['fecha_fin'];

        $select = "SELECT DISTINCT
                        p.id_participante,
                        p.cedula,
                        p.primer_nombre,
                        p.segundo_nombre,
                        p.primer_apellido,
                        p.segundo_apellido,
                        p.genero,
                        p.correo,
                        c.id_curso,
                        c.nombre_curso,
                        pc.estatus_participante,
                        pc.fecha_inscripcion,
                        pc.FechaFin AS fecha_fin_curso";

        try {
            switch ($tipo) {
                case 'Aprobados':
                    $sql = $select . "
                        FROM tb_participante_curso pc
                        INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
                        INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                        WHERE pc.estatus_participante = 'Aprobado'
                          AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
                          AND (:id_ambito IS NULL OR c.id_ambito = :id_ambito)
                        ORDER BY p.primer_apellido, p.primer_nombre";
                    break;

                case 'Inscritos':
                    $sql = $select . "
                        FROM tb_participante_curso pc
                        INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
                        INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                        WHERE pc.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin
                          AND (:id_ambito IS NULL OR c.id_ambito = :id_ambito)
                        ORDER BY p.primer_apellido, p.primer_nombre";
                    break;

                case 'Ingresados':
                    $sql = "SELECT DISTINCT
                                p.id_participante,
                                p.cedula,
                                p.primer_nombre,
                                p.segundo_nombre,
                                p.primer_apellido,
                                p.segundo_apellido,
                                p.genero,
                                p.correo,
                                NULL AS nombre_curso,
                                'Registrado' AS estatus_participante,
                                p.fecha_registro AS fecha_inscripcion,
                                NULL AS fecha_fin_curso
                            FROM tb_participante p
                            WHERE p.fecha_registro BETWEEN :fecha_inicio AND :fecha_fin
                            ORDER BY p.primer_apellido, p.primer_nombre";
                    break;

                case 'Masculino':
                case 'Femenino':
                    $sql = "SELECT DISTINCT
                                p.id_participante,
                                p.cedula,
                                p.primer_nombre,
                                p.segundo_nombre,
                                p.primer_apellido,
                                p.segundo_apellido,
                                p.genero,
                                p.correo,
                                NULL AS nombre_curso,
                                p.genero AS estatus_participante,
                                p.fecha_registro AS fecha_inscripcion,
                                NULL AS fecha_fin_curso
                            FROM tb_participante p
                            WHERE p.genero = :genero
                              AND p.fecha_registro BETWEEN :fecha_inicio AND :fecha_fin
                            ORDER BY p.primer_apellido, p.primer_nombre";
                    break;

                default:
                    return ['meta' => $meta, 'participantes' => []];
            }

            $stmt = $this->conn->prepare($sql);

            if ($tipo === 'Masculino' || $tipo === 'Femenino') {
                $stmt->execute([
                    ':fecha_inicio' => $fechaInicio,
                    ':fecha_fin' => $fechaFin,
                    ':genero' => $tipo,
                ]);
            } elseif ($tipo === 'Ingresados') {
                $stmt->execute([
                    ':fecha_inicio' => $fechaInicio,
                    ':fecha_fin' => $fechaFin,
                ]);
            } else {
                $stmt->bindValue(':fecha_inicio', $fechaInicio);
                $stmt->bindValue(':fecha_fin', $fechaFin);
                if ($idAmbito === null) {
                    $stmt->bindValue(':id_ambito', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':id_ambito', (int) $idAmbito, PDO::PARAM_INT);
                }
                $stmt->execute();
            }

            $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['meta' => $meta, 'participantes' => $participantes];
        } catch (PDOException $e) {
            error_log('Error en obtenerParticipantesPorMeta: ' . $e->getMessage());
            throw $e;
        }
    }

    public function eliminar($id) {
        try {
            // Verificar si está vencida
            $stmtCheck = $this->conn->prepare("SELECT fecha_fin FROM tb_metas WHERE id_meta = :id");
            $stmtCheck->execute([':id' => $id]);
            $meta = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($meta) {
                if (strtotime($meta['fecha_fin']) < strtotime(date('Y-m-d'))) {
                    return ['success' => false, 'message' => 'No se puede eliminar una meta que ya ha vencido.'];
                }
            }

            $sql = "DELETE FROM tb_metas WHERE id_meta = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return ['success' => true, 'rows' => $stmt->rowCount()];
        } catch (PDOException $e) {
            error_log("Error en eliminar Meta: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

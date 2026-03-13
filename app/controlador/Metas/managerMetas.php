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
            $sql = "UPDATE tb_metas
                       SET estado = 'Cerrada'
                     WHERE estado = 'Activa'
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
            $dynamicCount = "(SELECT
                CASE WHEN m.tipo_objetivo = 'Aprobados'
                    THEN COUNT(DISTINCT CASE
                        WHEN pc2.estatus_participante = 'Aprobado'
                          AND pc2.FechaFin BETWEEN m.fecha_inicio AND m.fecha_fin
                        THEN pc2.id_participante END)
                    ELSE COUNT(DISTINCT CASE
                        WHEN pc2.fecha_inscripcion BETWEEN m.fecha_inicio AND m.fecha_fin
                        THEN pc2.id_participante END)
                END
            FROM tb_participante_curso pc2
            INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
            WHERE (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito))";

            $sql = "SELECT m.id_meta, m.meta_objetivo, m.id_ambito, m.tipo_objetivo,
                        COALESCE(a.nombre_ambito, 'Global') AS ambito_nombre,
                        {$dynamicCount} AS estudiantes_formados,
                        CASE WHEN m.meta_objetivo = 0 THEN 0
                             ELSE ROUND(({$dynamicCount})/m.meta_objetivo*100, 2)
                        END AS porcentaje_cumplimiento,
                        CASE WHEN ({$dynamicCount}) >= m.meta_objetivo THEN 'Cumplida'
                             ELSE 'En progreso'
                        END AS estado_cumplimiento,
                        DATE_FORMAT(m.fecha_inicio, '%Y-%m-%d') as fecha_inicio,
                        DATE_FORMAT(m.fecha_fin, '%Y-%m-%d') as fecha_fin,
                        m.estado, m.created_at
                    FROM tb_metas m
                    LEFT JOIN tb_ambito a ON m.id_ambito = a.id_ambito";
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
            $dynamicCount = "(SELECT
                CASE WHEN m.tipo_objetivo = 'Aprobados'
                    THEN COUNT(DISTINCT CASE
                        WHEN pc2.estatus_participante = 'Aprobado'
                          AND pc2.FechaFin BETWEEN m.fecha_inicio AND m.fecha_fin
                        THEN pc2.id_participante END)
                    ELSE COUNT(DISTINCT CASE
                        WHEN pc2.fecha_inscripcion BETWEEN m.fecha_inicio AND m.fecha_fin
                        THEN pc2.id_participante END)
                END
            FROM tb_participante_curso pc2
            INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
            WHERE (m.id_ambito IS NULL OR c2.id_ambito = m.id_ambito))";

            $sql = "SELECT m.id_meta, m.meta_objetivo, m.id_ambito, m.tipo_objetivo,
                        COALESCE(a.nombre_ambito, 'Global') AS ambito_nombre,
                        {$dynamicCount} AS estudiantes_formados,
                        CASE WHEN m.meta_objetivo = 0 THEN 0
                             ELSE ROUND(({$dynamicCount})/m.meta_objetivo*100, 2)
                        END AS porcentaje_cumplimiento,
                        CASE WHEN ({$dynamicCount}) >= m.meta_objetivo THEN 'Cumplida'
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
            // No guardamos estudiantes_formados aquí: se calcula dinámicamente
            $sql = "INSERT INTO tb_metas (meta_objetivo, estudiantes_formados, tipo_objetivo, fecha_inicio, fecha_fin, estado, id_ambito) VALUES (:meta_objetivo, 0, :tipo_objetivo, :fecha_inicio, :fecha_fin, :estado, :id_ambito)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':meta_objetivo' => $data['meta_objetivo'] ?? null,
                ':tipo_objetivo' => $data['tipo_objetivo'] ?? 'Aprobados',
                ':fecha_inicio' => $data['fecha_inicio'] ?? null,
                ':fecha_fin' => $data['fecha_fin'] ?? null,
                ':estado' => $data['estado'] ?? 'Activa',
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
            // estudiantes_formados se calcula dinámicamente; no lo almacenamos manualmente
            $sql = "UPDATE tb_metas SET meta_objetivo = :meta_objetivo, tipo_objetivo = :tipo_objetivo, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, estado = :estado, id_ambito = :id_ambito WHERE id_meta = :id_meta";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':meta_objetivo' => $data['meta_objetivo'] ?? null,
                ':tipo_objetivo' => $data['tipo_objetivo'] ?? 'Aprobados',
                ':fecha_inicio' => $data['fecha_inicio'] ?? null,
                ':fecha_fin' => $data['fecha_fin'] ?? null,
                ':estado' => $data['estado'] ?? 'Activa',
                ':id_ambito' => isset($data['id_ambito']) && $data['id_ambito'] !== '' ? $data['id_ambito'] : null,
                ':id_meta' => $data['id_meta']
            ]);

            return ['success' => true, 'rows' => $stmt->rowCount()];
        } catch (PDOException $e) {
            error_log("Error en actualizar Meta: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function eliminar($id) {
        try {
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

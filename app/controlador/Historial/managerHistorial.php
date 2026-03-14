<?php
require_once __DIR__ . '/../../modelo/conexion.php';

class Historial {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function getConexion() {
        return $this->conn;
    }

    /**
     * Obtiene el historial de actividades del sistema
     * @param int $limite Número máximo de registros a retornar
     * @param int $offset Desplazamiento para paginación
     * @param string $filtro Filtro por tipo de actividad (opcional)
     * @param string $fechaDesde Fecha desde (opcional)
     * @param string $fechaHasta Fecha hasta (opcional)
     */
    public function obtenerHistorial($limite = 50, $offset = 0, $filtro = '', $fechaDesde = '', $fechaHasta = '') {
        try {
            $sql = "SELECT 
                        h.id_historial,
                        h.tipo_actividad,
                        h.descripcion,
                        h.tabla_afectada,
                        h.id_registro,
                        h.usuario_id,
                        h.fecha_hora,
                        CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.primer_apellido, '')) as nombre,
                        u.rol
                    FROM tb_historial h
                    LEFT JOIN tb_usuario u ON h.usuario_id = u.id_usuario
                    WHERE 1=1";
            
            $params = [];

            if (!empty($filtro)) {
                $sql .= " AND h.tipo_actividad = :filtro";
                $params[':filtro'] = $filtro;
            }

            if (!empty($fechaDesde)) {
                $sql .= " AND DATE(h.fecha_hora) >= :fecha_desde";
                $params[':fecha_desde'] = $fechaDesde;
            }

            if (!empty($fechaHasta)) {
                $sql .= " AND DATE(h.fecha_hora) <= :fecha_hasta";
                $params[':fecha_hasta'] = $fechaHasta;
            }

            $sql .= " ORDER BY h.fecha_hora DESC LIMIT :limite OFFSET :offset";

            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerHistorial: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el total de registros en el historial
     */
    public function obtenerTotalHistorial($filtro = '', $fechaDesde = '', $fechaHasta = '') {
        try {
            $sql = "SELECT COUNT(*) as total FROM tb_historial h WHERE 1=1";
            
            $params = [];

            if (!empty($filtro)) {
                $sql .= " AND h.tipo_actividad = :filtro";
                $params[':filtro'] = $filtro;
            }

            if (!empty($fechaDesde)) {
                $sql .= " AND DATE(h.fecha_hora) >= :fecha_desde";
                $params[':fecha_desde'] = $fechaDesde;
            }

            if (!empty($fechaHasta)) {
                $sql .= " AND DATE(h.fecha_hora) <= :fecha_hasta";
                $params[':fecha_hasta'] = $fechaHasta;
            }

            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];

        } catch (PDOException $e) {
            error_log("Error en obtenerTotalHistorial: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Registra una actividad en el historial
     */
    public function registrarActividad($tipo_actividad, $descripcion, $tabla_afectada, $id_registro, $usuario_id) {
        try {
            $sql = "INSERT INTO tb_historial 
                    (tipo_actividad, descripcion, tabla_afectada, id_registro, usuario_id, fecha_hora)
                    VALUES (:tipo, :descripcion, :tabla, :id_registro, :usuario_id, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':tipo' => $tipo_actividad,
                ':descripcion' => $descripcion,
                ':tabla' => $tabla_afectada,
                ':id_registro' => $id_registro,
                ':usuario_id' => $usuario_id
            ]);

            return [
                'success' => true,
                'message' => 'Actividad registrada correctamente'
            ];

        } catch (PDOException $e) {
            error_log("Error en registrarActividad: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al registrar la actividad'
            ];
        }
    }

    /**
     * Obtiene estadísticas del historial
     */
    public function obtenerEstadisticas($fechaDesde = '', $fechaHasta = '') {
        try {
            $sql = "SELECT 
                        tipo_actividad,
                        COUNT(*) as total,
                        tabla_afectada
                    FROM tb_historial
                    WHERE 1=1";
            
            $params = [];

            if (!empty($fechaDesde)) {
                $sql .= " AND DATE(fecha_hora) >= :fecha_desde";
                $params[':fecha_desde'] = $fechaDesde;
            }

            if (!empty($fechaHasta)) {
                $sql .= " AND DATE(fecha_hora) <= :fecha_hasta";
                $params[':fecha_hasta'] = $fechaHasta;
            }

            $sql .= " GROUP BY tipo_actividad, tabla_afectada ORDER BY total DESC";

            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el historial de un registro específico
     */
    public function obtenerHistorialPorRegistro($tabla_afectada, $id_registro) {
        try {
            $sql = "SELECT 
                        h.id_historial,
                        h.tipo_actividad,
                        h.descripcion,
                        h.fecha_hora,
                        CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.primer_apellido, '')) as nombre,
                        u.rol
                    FROM tb_historial h
                    LEFT JOIN tb_usuario u ON h.usuario_id = u.id_usuario
                    WHERE h.tabla_afectada = :tabla 
                    AND h.id_registro = :id_registro
                    ORDER BY h.fecha_hora DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':tabla' => $tabla_afectada,
                ':id_registro' => $id_registro
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerHistorialPorRegistro: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene actividades recientes (últimas 24 horas)
     */
    public function obtenerActividadesRecientes($limite = 10) {
        try {
            $sql = "SELECT 
                        h.id_historial,
                        h.tipo_actividad,
                        h.descripcion,
                        h.tabla_afectada,
                        h.fecha_hora,
                        CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.primer_apellido, '')) as nombre,
                        u.rol
                    FROM tb_historial h
                    LEFT JOIN tb_usuario u ON h.usuario_id = u.id_usuario
                    WHERE h.fecha_hora >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ORDER BY h.fecha_hora DESC
                    LIMIT :limite";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerActividadesRecientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene actividades por usuario
     */
    public function obtenerActividadesPorUsuario($usuario_id, $limite = 50) {
        try {
            $sql = "SELECT 
                        h.id_historial,
                        h.tipo_actividad,
                        h.descripcion,
                        h.tabla_afectada,
                        h.fecha_hora,
                        CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.primer_apellido, '')) as nombre
                    FROM tb_historial h
                    LEFT JOIN tb_usuario u ON h.usuario_id = u.id_usuario
                    WHERE h.usuario_id = :usuario_id
                    ORDER BY h.fecha_hora DESC
                    LIMIT :limite";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':usuario_id', (int)$usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerActividadesPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene tipos de actividades disponibles
     */
    public function obtenerTiposActividad() {
        try {
            $sql = "SELECT DISTINCT tipo_actividad FROM tb_historial ORDER BY tipo_actividad";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        } catch (PDOException $e) {
            error_log("Error en obtenerTiposActividad: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpia el historial anterior a una fecha
     */
    public function limpiarHistorialAntiguo($dias = 90) {
        try {
            $sql = "DELETE FROM tb_historial 
                    WHERE fecha_hora < DATE_SUB(NOW(), INTERVAL :dias DAY)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':dias', (int)$dias, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'success' => true,
                'message' => 'Historial limpiado correctamente',
                'registros_eliminados' => $stmt->rowCount()
            ];

        } catch (PDOException $e) {
            error_log("Error en limpiarHistorialAntiguo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al limpiar el historial'
            ];
        }
    }
}
?>

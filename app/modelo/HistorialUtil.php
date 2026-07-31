<?php
/**
 * Utilidad para registrar actividades en el historial
 * Uso: HistorialUtil::registrar('Crear', 'Se creó un nuevo participante', 'tb_participante', $id_participante, $_SESSION['id_usuario']);
 */

class HistorialUtil {
    private static $conn = null;

    /**
     * Inicializa la conexión a la base de datos
     */
    private static function inicializarConexion() {
        if (self::$conn === null) {
            require_once __DIR__ . '/conexion.php';
            $conexion = new Conexion();
            self::$conn = $conexion->getConexion();
        }
    }

    /**
     * Registra una actividad en el historial
     * 
     * @param string $tipo_actividad Tipo de actividad (Crear, Editar, Eliminar, Login, Ver, etc.)
     * @param string $descripcion Descripción detallada de la actividad
     * @param string $tabla_afectada Nombre de la tabla afectada
     * @param int $id_registro ID del registro afectado
     * @param int $usuario_id ID del usuario que realizó la acción
     * @return bool True si se registró correctamente, false en caso contrario
     */
    public static function registrar($tipo_actividad, $descripcion, $tabla_afectada, $id_registro, $usuario_id) {
        try {
            self::inicializarConexion();

            $sql = "INSERT INTO tb_historial 
                    (tipo_actividad, descripcion, tabla_afectada, id_registro, usuario_id, fecha_hora)
                    VALUES (:tipo, :descripcion, :tabla, :id_registro, :usuario_id, NOW())";
            
            $stmt = self::$conn->prepare($sql);
            $resultado = $stmt->execute([
                ':tipo' => $tipo_actividad,
                ':descripcion' => $descripcion,
                ':tabla' => $tabla_afectada,
                ':id_registro' => $id_registro,
                ':usuario_id' => $usuario_id
            ]);

            return $resultado;

        } catch (PDOException $e) {
            error_log("Error al registrar actividad en historial: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra un login de usuario
     */
    public static function registrarLogin($usuario_id, $nombre_usuario) {
        return self::registrar(
            'Login',
            "Usuario $nombre_usuario inició sesión",
            'tb_usuario',
            $usuario_id,
            $usuario_id
        );
    }

    /**
     * Registra un logout de usuario
     */
    public static function registrarLogout($usuario_id, $nombre_usuario) {
        return self::registrar(
            'Logout',
            "Usuario $nombre_usuario cerró sesión",
            'tb_usuario',
            $usuario_id,
            $usuario_id
        );
    }

    /**
     * Registra la creación de un participante
     */
    public static function registrarCrearParticipante($id_participante, $nombre_participante, $usuario_id) {
        return self::registrar(
            'Crear',
            "Se creó el participante: $nombre_participante",
            'tb_participante',
            $id_participante,
            $usuario_id
        );
    }

    /**
     * Registra la edición de un participante
     */
    public static function registrarEditarParticipante($id_participante, $nombre_participante, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se editó el participante: $nombre_participante",
            'tb_participante',
            $id_participante,
            $usuario_id
        );
    }

    /**
     * Registra la eliminación de un participante
     */
    public static function registrarEliminarParticipante($id_participante, $nombre_participante, $usuario_id) {
        return self::registrar(
            'Eliminar',
            "Se eliminó el participante: $nombre_participante",
            'tb_participante',
            $id_participante,
            $usuario_id
        );
    }

    /**
     * Registra la creación de un curso
     */
    public static function registrarCrearCurso($id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Crear',
            "Se creó el curso: $nombre_curso",
            'tb_curso',
            $id_curso,
            $usuario_id
        );
    }

    /**
     * Registra la edición de un curso
     */
    public static function registrarEditarCurso($id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se editó el curso: $nombre_curso",
            'tb_curso',
            $id_curso,
            $usuario_id
        );
    }

    /**
     * Registra la eliminación de un curso
     */
    public static function registrarEliminarCurso($id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Eliminar',
            "Se eliminó el curso: $nombre_curso",
            'tb_curso',
            $id_curso,
            $usuario_id
        );
    }

    /**
     * Registra la creación de un docente
     */
    public static function registrarCrearDocente($id_docente, $nombre_docente, $usuario_id) {
        return self::registrar(
            'Crear',
            "Se creó el docente: $nombre_docente",
            'tb_docente',
            $id_docente,
            $usuario_id
        );
    }

    /**
     * Registra la edición de un docente
     */
    public static function registrarEditarDocente($id_docente, $nombre_docente, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se editó el docente: $nombre_docente",
            'tb_docente',
            $id_docente,
            $usuario_id
        );
    }

    /**
     * Registra la eliminación de un docente
     */
    public static function registrarEliminarDocente($id_docente, $nombre_docente, $usuario_id) {
        return self::registrar(
            'Eliminar',
            "Se eliminó el docente: $nombre_docente",
            'tb_docente',
            $id_docente,
            $usuario_id
        );
    }

    /**
     * Registra la asignación de un participante a un curso
     */
    public static function registrarAsignarParticipanteCurso($id_participante, $nombre_participante, $id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se asignó el participante $nombre_participante al curso $nombre_curso",
            'tb_participante_curso',
            $id_participante,
            $usuario_id
        );
    }

    /**
     * Registra la desasignación de un participante de un curso
     */
    public static function registrarDesasignarParticipanteCurso($id_participante, $nombre_participante, $id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se desasignó el participante $nombre_participante del curso $nombre_curso",
            'tb_participante_curso',
            $id_participante,
            $usuario_id
        );
    }

    /**
     * Registra la asignación de un docente a un curso
     */
    public static function registrarAsignarDocenteCurso($id_docente, $nombre_docente, $id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se asignó el docente $nombre_docente al curso $nombre_curso",
            'tb_docente_curso',
            $id_docente,
            $usuario_id
        );
    }

    /**
     * Registra la desasignación de un docente de un curso
     */
    public static function registrarDesasignarDocenteCurso($id_docente, $nombre_docente, $id_curso, $nombre_curso, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se desasignó el docente $nombre_docente del curso $nombre_curso",
            'tb_docente_curso',
            $id_docente,
            $usuario_id
        );
    }

    /**
     * Registra la creación de una meta
     */
    public static function registrarCrearMeta($id_meta, $nombre_meta, $usuario_id) {
        return self::registrar(
            'Crear',
            "Se creó la meta: $nombre_meta",
            'tb_metas',
            $id_meta,
            $usuario_id
        );
    }

    /**
     * Registra la edición de una meta
     */
    public static function registrarEditarMeta($id_meta, $nombre_meta, $usuario_id) {
        return self::registrar(
            'Editar',
            "Se editó la meta: $nombre_meta",
            'tb_metas',
            $id_meta,
            $usuario_id
        );
    }

    /**
     * Registra la eliminación de una meta
     */
    public static function registrarEliminarMeta($id_meta, $nombre_meta, $usuario_id) {
        return self::registrar(
            'Eliminar',
            "Se eliminó la meta: $nombre_meta",
            'tb_metas',
            $id_meta,
            $usuario_id
        );
    }

    /**
     * Registra la visualización de un registro
     */
    public static function registrarVer($tabla, $id_registro, $descripcion, $usuario_id) {
        return self::registrar(
            'Ver',
            $descripcion,
            $tabla,
            $id_registro,
            $usuario_id
        );
    }

    /**
     * Registra la generación de un reporte o constancia en PDF
     */
    public static function registrarReporte($reporteName, $usuario_id, $tabla = null, $id_registro = null) {
        return self::registrar(
            'Reporte',
            "Se generó el reporte PDF de: $reporteName",
            $tabla,
            $id_registro,
            $usuario_id
        );
    }
}

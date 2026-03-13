<?php
require_once '../../modelo/conexion.php';

class DocenteCurso {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function obtenerCursosAsignados($id_docente) {
        $sql = "SELECT dc.id_docente_curso, c.id_curso, c.nombre_curso, ec.nombre_estatusCurso
                FROM tb_docente_curso dc
                INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                INNER JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                WHERE dc.id_docente = ? AND c.id_curso != 1"; // Exclude course with id 1
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_docente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCursosNoAsignados($id_docente) {
        $sql = "
            SELECT
                c.id_curso,
                c.nombre_curso,
                c.fecha_inicio,
                c.fecha_limite_inicio,
                c.fecha_fin,
                CONCAT(
                  c.nombre_curso,
                  ' (',
                  DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y'),
                  ' - ',
                  DATE_FORMAT(c.fecha_fin, '%d/%m/%Y'),
                  ')'
                ) AS display
            FROM tb_curso c
            WHERE
                c.id_curso NOT IN (
                    -- Excluye cualquier curso que ya tenga un docente asignado
                    SELECT dc.id_curso
                    FROM tb_docente_curso dc
                )
                AND c.id_estatusCurso = 1
                AND c.id_curso != 1
            ORDER BY c.fecha_inicio DESC
        ";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();  // ya no pasamos $id_docente porque la subconsulta no lo usa
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function asignarCurso($id_docente, $id_curso) {
        try {
            // Verificar que el curso esté en espera antes de asignar
            $sqlVerificar = "SELECT id_estatusCurso FROM tb_curso WHERE id_curso = ?";
            $stmtVerificar = $this->conn->prepare($sqlVerificar);
            $stmtVerificar->execute([$id_curso]);
            $estatus = $stmtVerificar->fetchColumn();
            
            if ($estatus != 1) { // 1 = "En espera"
                return ['success' => false, 'message' => 'Solo se pueden asignar cursos en estado "En espera"'];
            }

            // SQL para insertar el curso asignado (registrar fecha de asignación)
            $sql = "INSERT INTO tb_docente_curso (id_docente, id_curso, fecha_asignacion) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([$id_docente, $id_curso])) {
                // Verificar el estado actual del docente
                $estadoActual = $this->obtenerEstadoDocente($id_docente)['estado'];
                
                // Solo actualizar el estado si no está en "En formación"
                if ($estadoActual !== 'En formación') {
                    $this->actualizarEstadoDocente($id_docente, 'Asignado');
                }
                
                return ['success' => true, 'message' => 'Curso asignado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al asignar el curso'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    public function eliminarCursoDocente($id_docente_curso) {
        try {
            // Primero obtener el id_docente antes de eliminar
            $sqlDocente = "SELECT id_docente FROM tb_docente_curso WHERE id_docente_curso = ?";
            $stmtDocente = $this->conn->prepare($sqlDocente);
            $stmtDocente->execute([$id_docente_curso]);
            $id_docente = $stmtDocente->fetchColumn();
            
            if (!$id_docente) {
                return ['success' => false, 'message' => 'No se encontró el docente'];
            }

            // Verificar el estado actual del docente
            $estadoActual = $this->obtenerEstadoDocente($id_docente)['estado'];
            
            // SQL para eliminar el curso asignado
            $sql = "DELETE FROM tb_docente_curso WHERE id_docente_curso = ?";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([$id_docente_curso])) {
                // Verificar si el docente tiene más cursos asignados (excluyendo curso con id 1)
                $tieneCursos = $this->verificarCursosAsignados($id_docente);
                
                // Solo actualizar el estado si no está en "En formación"
                if ($estadoActual !== 'En formación') {
                    if ($tieneCursos) {
                        $this->actualizarEstadoDocente($id_docente, 'Asignado');
                    } else {
                        $this->actualizarEstadoDocente($id_docente, 'Disponible');
                    }
                }
                
                return ['success' => true, 'message' => 'Curso desinscrito correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al desinscribir el curso'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    public function actualizarEstadoDocente($id_docente, $estado) {
        $sql = "UPDATE tb_docente SET estado_docente = ? WHERE id_docente = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$estado, $id_docente]);
    }

    public function verificarCursosAsignados($id_docente) {
        $sql = "SELECT COUNT(*) FROM tb_docente_curso dc
                INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                WHERE dc.id_docente = ? AND c.id_curso != 1"; // Exclude course with id 1
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_docente]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerDatosDocente($id_docente) {
        $sql = "SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, correo, fecha_nacimiento, edad, estado_docente, genero, tipo_docente
                FROM tb_docente WHERE id_docente = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_docente]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $nombre = trim(($row['primer_nombre'] ?? '') . ' ' . ($row['segundo_nombre'] ?? ''));
        $apellido = trim(($row['primer_apellido'] ?? '') . ' ' . ($row['segundo_apellido'] ?? ''));

        return [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'cedula' => $row['cedula'] ?? null,
            'correo' => $row['correo'] ?? null,
            'fecha_nacimiento' => $row['fecha_nacimiento'] ?? null,
            'edad' => $row['edad'] ?? null,
            'estado_docente' => $row['estado_docente'] ?? null,
            'genero' => $row['genero'] ?? null,
            'tipo_docente' => $row['tipo_docente'] ?? null
        ];
    }

    public function obtenerEstadoDocente($id_docente) {
        $sql = "SELECT estado_docente as estado FROM tb_docente WHERE id_docente = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_docente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
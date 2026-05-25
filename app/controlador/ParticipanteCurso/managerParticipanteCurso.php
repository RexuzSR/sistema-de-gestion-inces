<?php
// Conexion a la base de datos
require_once '../../modelo/conexion.php';

// Clase Participante
class ParticipanteCurso {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function obtenerDatosParticipante($id_participante) {
        $stmt = $this->conn->prepare("SELECT CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, edad, (SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = tb_participante.id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1) AS telefono FROM tb_participante WHERE id_participante = :id_participante");
        $stmt->bindParam(':id_participante', $id_participante, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return ['error' => 'Participante no encontrado'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Error en la consulta: ' . $e->getMessage()];
        }
    }


    public function obtenerCursosEnProgreso($id_participante) {
        $sql = "SELECT c.nombre_curso, pc.estatus_participante, pc.id_participante_curso
                FROM tb_participante_curso pc
                INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                WHERE pc.id_participante = :id_participante
                AND pc.estatus_participante IN ('Asignado', 'En formación')
                ORDER BY pc.estatus_participante DESC, pc.fecha_inscripcion ASC";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_participante', $id_participante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerCursosNoInscritos($id_participante) { 
        $sql = "SELECT 
                    c.id_curso, 
                    CONCAT(c.nombre_curso, ' (', c.turno, ') - ', c.num_inscritos, '/', c.max_participantes) AS display
                FROM tb_curso c
                WHERE c.id_curso != 1
                AND c.num_inscritos < c.max_participantes
                AND c.id_estatusCurso = 1  -- Cambié el valor aquí para cursos activos (estatus_curso = 1)
                AND c.id_curso NOT IN (
                    SELECT pc.id_curso
                    FROM tb_participante_curso pc
                    WHERE pc.id_participante = :id_participante
                    AND pc.estatus_participante = 'Asignado'
                )
                ORDER BY FIELD(c.turno, 'Mañana', 'Tarde'), c.nombre_curso";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_participante', $id_participante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCursosNoInscritos: " . $e->getMessage());
            return [];
        }
    }
    
    
    
    
    public function asignarCurso($id_participante, $id_curso) {
        try {
            $this->conn->beginTransaction();
            error_log("Transaction started in asignarCurso for id_participante: $id_participante, id_curso: $id_curso.");
    
            // 1. Verificar disponibilidad de cupo en el curso
            $sqlActualizarCupo = "
                UPDATE tb_curso 
                SET num_inscritos = num_inscritos + 1 
                WHERE id_curso = :id_curso 
                  AND num_inscritos < max_participantes
            ";
            $stmtActualizarCupo = $this->conn->prepare($sqlActualizarCupo);
            $stmtActualizarCupo->execute([':id_curso' => $id_curso]);
            error_log("Cupo updated. Affected rows: " . $stmtActualizarCupo->rowCount());
    
            if ($stmtActualizarCupo->rowCount() === 0) {
                throw new Exception("El curso no tiene cupos disponibles");
            }
    
            // 2. Verificar si el participante ya tiene un registro en este curso con estatus 'Pendiente'
            $sqlVerificar = "
                SELECT id_participante_curso 
                FROM tb_participante_curso 
                WHERE id_participante = :id_p 
                  AND id_curso = :id_c
                  AND estatus_participante = 'Asignado'
            ";
            $stmtVerificar = $this->conn->prepare($sqlVerificar);
            $stmtVerificar->execute([':id_p' => $id_participante, ':id_c' => $id_curso]);
            error_log("Verification: Found " . $stmtVerificar->rowCount() . " 'Asignado' record(s) for id_participante: $id_participante in id_curso: $id_curso.");
    
            if ($stmtVerificar->rowCount() > 0) {
                throw new Exception("El participante ya está inscrito en este curso con estatus 'Asignado'");
            }

            // 2.1 Verificar si el participante ya tiene un curso en el mismo turno
            // Primero obtenemos el turno del curso al que se intenta inscribir
            $sqlTurno = "SELECT turno FROM tb_curso WHERE id_curso = :id_curso";
            $stmtTurno = $this->conn->prepare($sqlTurno);
            $stmtTurno->execute([':id_curso' => $id_curso]);
            $turnoCurso = $stmtTurno->fetchColumn();

            if ($turnoCurso) {
                // Buscamos si ya tiene cursos activos (Pendiente o En formación) en ese turno
                $sqlCheckTurno = "
                    SELECT COUNT(*) 
                    FROM tb_participante_curso pc
                    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                    WHERE pc.id_participante = :id_p
                      AND c.turno = :turno
                      AND pc.estatus_participante IN ('Asignado', 'En formación')
                      AND pc.id_curso != :id_c
                ";
                $stmtCheckTurno = $this->conn->prepare($sqlCheckTurno);
                $stmtCheckTurno->execute([
                    ':id_p' => $id_participante,
                    ':turno' => $turnoCurso,
                    ':id_c' => $id_curso
                ]);

                if ($stmtCheckTurno->fetchColumn() > 0) {
                    throw new Exception("El participante ya está inscrito en un curso del turno '$turnoCurso'. No puede inscribirse en otro del mismo turno.");
                }
            }
    
            // 3. Obtener fechas de inicio y fin del curso desde tb_curso
            $sqlFechas = "
                SELECT fecha_inicio, fecha_fin 
                FROM tb_curso 
                WHERE id_curso = :id_curso
            ";
            $stmtFechas = $this->conn->prepare($sqlFechas);
            $stmtFechas->execute([':id_curso' => $id_curso]);
            $fechasCurso = $stmtFechas->fetch(PDO::FETCH_ASSOC);
            error_log("Course dates retrieved: " . json_encode($fechasCurso));
            
            if (!$fechasCurso) {
                throw new Exception("El curso seleccionado no existe");
            }
    
            // 4. Insertar un nuevo registro en tb_participante_curso con estado 'Pendiente'
            $sqlInsertar = "
                INSERT INTO tb_participante_curso 
                    (id_participante, id_curso, fecha_inscripcion, FechaInicio, FechaFin, estatus_participante)
                VALUES 
                    (:id_p, :id_c, CURDATE(), :fecha_inicio, :fecha_fin, 'Asignado')
            ";
            $stmtInsertar = $this->conn->prepare($sqlInsertar);
            $stmtInsertar->execute([
                ':id_p'          => $id_participante,
                ':id_c'          => $id_curso,
                ':fecha_inicio'  => $fechasCurso['fecha_inicio'],
                ':fecha_fin'     => $fechasCurso['fecha_fin']
            ]);
        
            // 5. Actualizar el estado del participante en tb_participante
            $sqlActualizarParticipante = "
                UPDATE tb_participante 
                SET estado = 'Asignado' 
                WHERE id_participante = :id_p 
                  AND estado NOT IN ('En formación', 'Asignado')
            ";
            $stmtActualizarParticipante = $this->conn->prepare($sqlActualizarParticipante);
            $stmtActualizarParticipante->execute([':id_p' => $id_participante]);
            error_log("Participant state updated.");
    
            // Confirmar la transacción
            $this->conn->commit();

            return $this->conn->lastInsertId();
    
        } catch (PDOException $e) {
            $this->conn->rollBack();

            throw new Exception("Error al asignar el curso: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();

            throw $e;
        }
    }
    
    
    public function eliminarCurso($id_participante_curso, $id_participante) {
        try {
            $this->conn->beginTransaction();
    
            // 1. Obtener el curso a eliminar
            $obtenerCurso = $this->conn->prepare("
                SELECT id_curso 
                FROM tb_participante_curso 
                WHERE id_participante_curso = :id
            ");
            $obtenerCurso->execute([':id' => $id_participante_curso]);
            $curso = $obtenerCurso->fetch(PDO::FETCH_ASSOC);
    
            if (!$curso) {
                throw new Exception("Registro no encontrado");
            }
    
            // 2. Eliminar el registro
            $eliminar = $this->conn->prepare("
                DELETE FROM tb_participante_curso 
                WHERE id_participante_curso = :id
            ");
            $eliminar->execute([':id' => $id_participante_curso]);
    
            // 3. Reducir cupo del curso
            $actualizarCupo = $this->conn->prepare("
                UPDATE tb_curso 
                SET num_inscritos = num_inscritos - 1 
                WHERE id_curso = :id_curso
                AND num_inscritos > 0
            ");
            $actualizarCupo->execute([':id_curso' => $curso['id_curso']]);
    
            // 4. Consultar cursos restantes para determinar el nuevo estado
            $cursosRestantes = $this->conn->prepare("
                SELECT estatus_participante 
                FROM tb_participante_curso 
                WHERE id_participante = :id_p
            ");
            $cursosRestantes->execute([':id_p' => $id_participante]);
            
            $tieneEnFormacion = false;
            $tieneAsignado = false;
            $tieneDiferido = false;
            $tieneHistorial = false;
            
            while ($c = $cursosRestantes->fetch(PDO::FETCH_ASSOC)) {
                if ($c['estatus_participante'] === 'En formación') {
                    $tieneEnFormacion = true;
                } elseif ($c['estatus_participante'] === 'Asignado') {
                    $tieneAsignado = true;
                } elseif ($c['estatus_participante'] === 'Diferido') {
                    $tieneDiferido = true;
                } else {
                    $tieneHistorial = true;
                }
            }
            
            $nuevoEstado = 'En sistema';
            if ($tieneEnFormacion) $nuevoEstado = 'En formación';
            elseif ($tieneAsignado) $nuevoEstado = 'Asignado';
            elseif ($tieneDiferido) $nuevoEstado = 'Diferido';
            elseif ($tieneHistorial) $nuevoEstado = 'Disponible';
            
            error_log("Nuevo estado calculado: $nuevoEstado para participante ID: $id_participante");
    
            // 5. Actualizar estado del participante
            $actualizarEstado = $this->conn->prepare("
                UPDATE tb_participante 
                SET estado = :estado 
                WHERE id_participante = :id_p
            ");
            $actualizarEstado->execute([
                ':estado' => $nuevoEstado,
                ':id_p' => $id_participante
            ]);
            error_log("Estado actualizado a '$nuevoEstado' para participante $id_participante");
    
            $this->conn->commit();
            return ['success' => true];
    
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error eliminarCurso (PDO): " . $e->getMessage());
            return ['success' => false];
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error eliminarCurso (Excepción): " . $e->getMessage());
            return ['success' => false];
        }
    }
    

    public function historialParticipante($idParticipante) {
        try {
            // Consulta con JOIN para obtener el nombre del curso
            $query = "SELECT 
                        tb_curso.nombre_curso, 
                        tb_participante_curso.fecha_inscripcion, 
                        tb_participante_curso.FechaInicio AS fecha_inicio, 
                        tb_participante_curso.FechaFin AS fecha_fin, 
                        tb_participante_curso.estatus_participante 
                      FROM tb_participante_curso
                      INNER JOIN tb_curso ON tb_participante_curso.id_curso = tb_curso.id_curso
                      WHERE tb_participante_curso.id_participante = ?
                      AND tb_participante_curso.estatus_participante NOT IN ('En formación', 'Asignado')
                      ORDER BY tb_participante_curso.fecha_inscripcion DESC";
    
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idParticipante]);
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            error_log("Error en historialParticipante: " . $e->getMessage());
            return [];
        }
    }
    
}
?>

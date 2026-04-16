<?php
require_once __DIR__ . '/../../modelo/conexion.php';

class Curso {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function getConexion() {
        return $this->conn;
    }

    /**
     * Actualiza automáticamente el estado de los cursos basándose en la fecha actual y requisitos
     */
    public function actualizarEstadosPorFecha() {
        try {
            $hoy = date('Y-m-d');
            
            // 1. A 'Diferido' (2): Llegó la fecha inicio o límite, pero faltan requisitos
            $sqlDiferido = "UPDATE tb_curso c
                            SET id_estatusCurso = 2 
                            WHERE id_estatusCurso IN (1, 3) 
                            AND (fecha_inicio <= :hoy OR (fecha_limite_inicio IS NOT NULL AND fecha_limite_inicio <= :hoy))
                            AND (
                                num_inscritos < IFNULL(c.min_participantes, 12) 
                                OR NOT EXISTS (SELECT 1 FROM tb_docente_curso dc WHERE dc.id_curso = c.id_curso)
                            )";
            $stmt1 = $this->conn->prepare($sqlDiferido);
            $stmt1->execute([':hoy' => $hoy]);

            // 1. Sincronizar participantes a 'En formación' (Prioridad Máxima)
            $sqlSyncFormacion = "UPDATE tb_participante p
                                 SET p.estado = 'En formación'
                                 WHERE p.estado <> 'Inactivo'
                                 AND EXISTS (
                                     SELECT 1 FROM tb_participante_curso pc
                                     INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                                     WHERE pc.id_participante = p.id_participante
                                     AND c.id_estatusCurso = 3
                                 )";
            $this->conn->prepare($sqlSyncFormacion)->execute();

            // 2. Sincronizar participantes a 'Asignado' (Prioridad Media)
            $sqlSyncAsignado = "UPDATE tb_participante p
                                SET p.estado = 'Asignado'
                                WHERE p.estado NOT IN ('En formación', 'Inactivo')
                                AND EXISTS (
                                    SELECT 1 FROM tb_participante_curso pc
                                    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                                    WHERE pc.id_participante = p.id_participante
                                    AND c.id_estatusCurso IN (1, 4)
                                )
                                AND NOT EXISTS (
                                    SELECT 1 FROM tb_participante_curso pc2
                                    INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                                    WHERE pc2.id_participante = p.id_participante
                                    AND c2.id_estatusCurso = 3
                                )";
            $this->conn->prepare($sqlSyncAsignado)->execute();

            // 3. Sincronizar participantes a 'Diferido' (Prioridad Baja)
            $sqlSyncDiferido = "UPDATE tb_participante p
                                SET p.estado = 'Diferido'
                                WHERE p.estado NOT IN ('En formación', 'Asignado', 'Inactivo')
                                AND EXISTS (
                                    SELECT 1 FROM tb_participante_curso pc
                                    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                                    WHERE pc.id_participante = p.id_participante
                                    AND c.id_estatusCurso = 2
                                )
                                AND NOT EXISTS (
                                    SELECT 1 FROM tb_participante_curso pc2
                                    INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso
                                    WHERE pc2.id_participante = p.id_participante
                                    AND c2.id_estatusCurso IN (1, 3, 4)
                                )";
            $this->conn->prepare($sqlSyncDiferido)->execute();

            // 4. Sincronizar a 'Disponible' o 'En sistema' si no tienen cursos activos
            $sqlSyncSistema = "UPDATE tb_participante p
                               SET p.estado = 'En sistema'
                               WHERE p.estado IN ('Asignado', 'En formación', 'Diferido')
                               AND NOT EXISTS (
                                   SELECT 1 FROM tb_participante_curso pc
                                   INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                                   WHERE pc.id_participante = p.id_participante
                                   AND c.id_estatusCurso IN (1, 2, 3, 4)
                               )";
            $this->conn->prepare($sqlSyncSistema)->execute();

        } catch (PDOException $e) {
            error_log("Error en actualizarEstadosPorFecha: " . $e->getMessage());
        }
    }

    /**
     * Indica si el id_nombrecurso está asociado al PPL en tb_ppl_nombrecurso.
     */
    public function esNombrecursoAsignadoAPpl($idPpl, $idNombrecurso) {
        try {
            $idPpl = (int) $idPpl;
            $idNombrecurso = (int) $idNombrecurso;
            if ($idPpl <= 0 || $idNombrecurso <= 0) {
                return false;
            }
            $sql = 'SELECT 1 FROM tb_ppl_nombrecurso WHERE id_ppl = :p AND id_nombrecurso = :n LIMIT 1';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':p' => $idPpl, ':n' => $idNombrecurso]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('esNombrecursoAsignadoAPpl: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerNombreNombrecurso($idNombrecurso) {
        try {
            $idNombrecurso = (int) $idNombrecurso;
            if ($idNombrecurso <= 0) {
                return null;
            }
            $stmt = $this->conn->prepare('SELECT nombre_curso FROM tb_nombrecurso WHERE id_nombrecurso = :id LIMIT 1');
            $stmt->execute([':id' => $idNombrecurso]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (string) $row['nombre_curso'] : null;
        } catch (PDOException $e) {
            error_log('obtenerNombreNombrecurso: ' . $e->getMessage());
            return null;
        }
    }

    // Método para verificar si el nombre del curso ya existe (normalizado, solo por nombre)
    public function verificarNombreCurso($nombre_curso) {
        // Normalizar nombre en PHP (trim, colapsar espacios, minúsculas)
        $nombre_normalizado = mb_strtolower(preg_replace('/\s+/u', ' ', trim($nombre_curso)));
        $sql = "SELECT COUNT(*) FROM tb_curso WHERE LOWER(nombre_curso) = :nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre_normalizado);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Nuevo método: Verificar si el nombre existe y si coincide el ámbito
    public function verificarNombreYAmbito($nombre_curso, $id_ambito = null) {
        $nombre_normalizado = mb_strtolower(preg_replace('/\s+/u', ' ', trim($nombre_curso)));
        
        // Primero verificar si el nombre existe en absoluto
        $sqlNombre = "SELECT COUNT(*) FROM tb_curso WHERE LOWER(nombre_curso) = :nombre";
        $stmtNombre = $this->conn->prepare($sqlNombre);
        $stmtNombre->bindParam(':nombre', $nombre_normalizado);
        $stmtNombre->execute();
        $existeNombre = $stmtNombre->fetchColumn() > 0;

        $existeEnAmbito = false;

        // Si existe y nos pasaron un ámbito, verificar si coincide en ese ámbito
        if ($existeNombre && !empty($id_ambito)) {
            $sqlAmbito = "SELECT COUNT(*) FROM tb_curso 
                          WHERE LOWER(nombre_curso) = :nombre 
                          AND id_ambito = :ambito";
            $stmtAmbito = $this->conn->prepare($sqlAmbito);
            $stmtAmbito->bindParam(':nombre', $nombre_normalizado);
            $stmtAmbito->bindParam(':ambito', $id_ambito, PDO::PARAM_INT);
            $stmtAmbito->execute();
            $existeEnAmbito = $stmtAmbito->fetchColumn() > 0;
        }

        return [
            'existe_nombre' => $existeNombre,
            'existe_en_ambito' => $existeEnAmbito
        ];
    }

    // Verificación más estricta para creación: nombre + fecha_inicio + turno
    public function verificarDuplicadoCreacion($nombre_curso, $fecha_inicio, $turno) {
        $nombre_normalizado = mb_strtolower(preg_replace('/\s+/u', ' ', trim($nombre_curso)));
        $sql = "SELECT COUNT(*) FROM tb_curso 
                WHERE LOWER(nombre_curso) = :nombre 
                  AND fecha_inicio = :fecha_inicio 
                  AND turno = :turno";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre_normalizado);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':turno', $turno);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function verificarNombreCursoEditar($nombre, $idExcluir = 0) {
        try {
            $nombre_normalizado = mb_strtolower(preg_replace('/\s+/u', ' ', trim($nombre)));
            $sql = "SELECT COUNT(*) FROM tb_curso 
                    WHERE LOWER(nombre_curso) = :nombre 
                      AND id_curso != :idExcluir";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre_normalizado);
            $stmt->bindParam(':idExcluir', $idExcluir, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarNombreCurso: " . $e->getMessage());
            return false;
        }
    }

    public function registrar($data) {
        try {
            // Validar campos requeridos
            $requiredFields = [
                'nombre_curso', 
                'fecha_inicio', 
                'fecha_fin',
                'turno'
            ];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("El campo $field es obligatorio");
                }
            }
    
            // Validar fechas
            if (strtotime($data['fecha_inicio']) > strtotime($data['fecha_fin'])) {
                throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin");
            }
    
            $sql = "INSERT INTO tb_curso (
                        nombre_curso, 
                        fecha_inicio, 
                        fecha_limite_inicio,
                        fecha_fin, 
                        horas_totales,
                        horas_por_clase,
                        horas_completadas,
                        max_participantes,
                        min_participantes,
                        num_inscritos,
                        id_modalidad, 
                        tipo_formacion,
                        id_estatusCurso, 
                        id_sectoreconomico,
                        id_subtipo,
                        id_areaFormativa, 
                        id_ambito,
                        id_ppl,
                        id_nombre_especifico,
                        turno, 
                        programa_formacion,
                        fecha_creacion,
                        fecha_actualizacion
                    ) VALUES (
                        :nombre_curso, 
                        :fecha_inicio, 
                        :fecha_limite_inicio,
                        :fecha_fin, 
                        :horas_totales,
                        :horas_por_clase,
                        :horas_completadas,
                        :max_participantes,
                        :min_participantes,
                        :num_inscritos,
                        :id_modalidad, 
                        :tipo_formacion,
                        :id_estatusCurso, 
                        :id_sectoreconomico,
                        :id_subtipo,
                        :id_areaFormativa, 
                        :id_ambito,
                        :id_ppl,
                        :id_nombre_especifico,
                        :turno, 
                        :programa_formacion,
                        :fecha_creacion,
                        :fecha_actualizacion
                    )";
    
            $stmt = $this->conn->prepare($sql);
            
            // Valores por defecto y mapeo de nuevos campos
            $params = [
                ':nombre_curso' => $data['nombre_curso'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_limite_inicio' => $data['fecha_limite_inicio'] ?? null,
                ':fecha_fin' => $data['fecha_fin'],
                ':horas_totales' => $data['horas_totales'] ?? null,
                ':horas_por_clase' => $data['horas_por_clase'] ?? null,
                ':horas_completadas' => $data['horas_completadas'] ?? 0.00,
                ':max_participantes' => $data['max_participantes'] ?? 30,
                ':min_participantes' => $data['min_participantes'] ?? 10,
                ':num_inscritos' => 0,
                ':id_modalidad' => $data['id_modalidad'] ?? null,
                ':tipo_formacion' => $data['tipo_formacion'] ?? null,
                ':id_estatusCurso' => $data['id_estatusCurso'] ?? 1,
                ':id_sectoreconomico' => $data['id_sectoreconomico'] ?? ($data['sectoreconomico'] ?? null),
                ':id_subtipo' => $data['id_subtipo'] ?? ($data['subtipo'] ?? null),
                ':id_areaFormativa' => $data['id_areaFormativa'] ?? null,
                ':id_ambito' => $data['id_ambito'] ?? ($data['ambito'] ?? null),
                ':id_ppl' => !empty($data['id_ppl']) ? (int) $data['id_ppl'] : null,
                ':id_nombre_especifico' => !empty($data['id_nombre_especifico']) ? (int) $data['id_nombre_especifico'] : null,
                ':turno' => $data['turno'],
                ':programa_formacion' => $data['programa_formacion'] ?? null,
                ':fecha_creacion' => date('Y-m-d H:i:s'),
                ':fecha_actualizacion' => null
            ];
    
            $stmt->execute($params);
    
            return [
                "success" => true,
                "message" => "Curso registrado con éxito",
                "id_curso" => $this->conn->lastInsertId()
            ];
    
        } catch (PDOException $e) {
            error_log("Error al registrar curso: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Error al registrar el curso en la base de datos",
                "error" => $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("Error de validación: " . $e->getMessage());
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Comprueba si un docente puede ser asignado a un curso basándose en:
     * 1. No tener cursos en el mismo turno con fechas solapadas.
     * 2. No exceder las 8 horas de trabajo diarias (por clase).
     */
    public function comprobarDisponibilidadDocente($idDocente, $idCurso) {
        // 1. Obtener datos del curso al que se quiere asignar
        $cursoNuevo = $this->obtenerCursoPorId($idCurso);
        if (isset($cursoNuevo['error'])) return ["success" => false, "message" => $cursoNuevo['error']];

        $fechaInicioN = $cursoNuevo['fecha_inicio'];
        $fechaFinN = $cursoNuevo['fecha_fin'];
        $turnoN = $cursoNuevo['turno'];
        $horasN = (float)$cursoNuevo['horas_por_clase'];

        // 2. Obtener cursos activos del docente (Espera, Diferido, En proceso)
        $sql = "SELECT c.nombre_curso, c.turno, c.horas_por_clase, c.fecha_inicio, c.fecha_fin
                FROM tb_docente_curso dc
                JOIN tb_curso c ON dc.id_curso = c.id_curso
                WHERE dc.id_docente = :idDocente 
                AND c.id_estatusCurso IN (1, 2, 3) -- En espera, Diferido o En proceso
                AND (
                    -- Si alguno no tiene fechas, asumimos que puede haber solapamiento
                    (:fechaInicio IS NULL OR :fechaFin IS NULL OR c.fecha_inicio IS NULL OR c.fecha_fin IS NULL)
                    OR (c.fecha_inicio <= :fechaFin AND c.fecha_fin >= :fechaInicio)
                )";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':idDocente' => $idDocente,
            ':fechaInicio' => $fechaInicioN,
            ':fechaFin' => $fechaFinN
        ]);
        
        $cursosAsignados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $horasTotales = $horasN;
        
        foreach ($cursosAsignados as $ca) {
            // Validar turno (usando mb_strtolower para soportar 'Mañana')
            if (trim(mb_strtolower($ca['turno'], 'UTF-8')) === trim(mb_strtolower($turnoN, 'UTF-8'))) {
                return [
                    "success" => false, 
                    "message" => "Conflicto de horario: El docente ya tiene asignado el curso '{$ca['nombre_curso']}' en el turno '{$ca['turno']}' durante este período."
                ];
            }
            $horasTotales += (float)$ca['horas_por_clase'];
        }

        // Validar límite de 8 horas
        if ($horasTotales > 8) {
            return [
                "success" => false,
                "message" => "Exceso de carga horaria: Asignar este curso llevaría al docente a trabajar {$horasTotales} horas diarias (Máximo permitido: 8h). Actualmente ya tiene " . ($horasTotales - $horasN) . " horas asignadas."
            ];
        }

        return ["success" => true];
    }

    public function asignarDocenteCurso(int $idCurso, int $idDocente) {
        try {
            // Primero validamos disponibilidad del docente
            $disponibilidad = $this->comprobarDisponibilidadDocente($idDocente, $idCurso);
            if (!$disponibilidad['success']) {
                return $disponibilidad;
            }

            // Validar si el curso ya tiene un docente asignado
            $sqlCheck = "SELECT id_docente FROM tb_docente_curso WHERE id_curso = :cur LIMIT 1";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([':cur' => $idCurso]);
            if ($stmtCheck->fetch()) {
                return [
                    "success" => false,
                    "message" => "Este curso ya tiene un docente asignado. Si desea cambiarlo, primero debe desvincular al actual."
                ];
            }

            $sql = "INSERT INTO tb_docente_curso (id_docente, id_curso, fecha_asignacion) 
                    VALUES (:doc, :cur, CURDATE())";
            $stmt = $this->conn->prepare($sql);
            $res = $stmt->execute([
                ':doc' => $idDocente,
                ':cur' => $idCurso
            ]);

            if ($res) {
                // Actualizar estado del docente a 'Asignado' si es necesario
                $this->actualizarEstadoDocente($idDocente);
                return ["success" => true, "message" => "Docente asignado correctamente."];
            } else {
                return ["success" => false, "message" => "Error al ejecutar la asignación."];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error de base de datos: " . $e->getMessage()];
        }
    }

    public function obtenerCursos() {
        try {
            $this->actualizarEstadosPorFecha();
            $sql = "SELECT
                        c.id_curso,
                        c.nombre_curso,
                        c.id_modalidad,
                        m.nombre_modalidad,
                        c.programa_formacion,
                        af.nombre_areaFormativa,
                        c.fecha_inicio,
                        c.fecha_limite_inicio,
                        c.fecha_fin,
                        c.horas_totales,
                        c.horas_por_clase,
                        c.horas_completadas,
                        c.min_participantes,
                        c.turno,
                        c.num_inscritos,
                        c.max_participantes,
                        c.id_estatusCurso,
                        ec.nombre_estatusCurso AS estatus,
                        se.nombre_sectoreconomico,
                        st.nombre_subtipo,
                        a.nombre_ambito,
                        c.fecha_creacion,
                        c.fecha_actualizacion,
                        (SELECT COUNT(*) FROM tb_participante_curso pc WHERE pc.id_curso = c.id_curso) AS registros_participante_curso
                    FROM tb_curso c
                    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
                    LEFT JOIN tb_areaformativa af ON c.id_areaFormativa = af.id_areaFormativa
                    LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                    LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
                    LEFT JOIN tb_subtipo st ON c.id_subtipo = st.id_subtipo
                    LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                    ORDER BY c.id_curso DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
    
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $resultados ?: [];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al obtener los cursos: " . $e->getMessage()
            ];
        }
    }

    public function obtenerCursoFiltros($filtros) {
        try {
            $sql = "SELECT 
                        c.id_curso, 
                        c.nombre_curso, 
                        c.fecha_inicio, 
                        c.fecha_fin, 
                        c.max_participantes, 
                        c.num_inscritos, 
                        c.tipo_formacion, 
                        c.id_sectoreconomico, 
                        c.id_subtipo, 
                        c.id_ambito, 
                        c.programa_formacion, 
                        c.turno, 
                        m.nombre_modalidad, 
                        e.nombre_estatuscurso,
                        se.nombre_sectoreconomico AS sectoreconomico_nombre,
                        st.nombre_subtipo AS subtipo_nombre,
                        a.nombre_ambito AS ambito_nombre
                    FROM tb_curso c
                    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
                    LEFT JOIN tb_estatuscurso e ON c.id_estatusCurso = e.id_estatusCurso
                    LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
                    LEFT JOIN tb_subtipo st ON c.id_subtipo = st.id_subtipo
                    LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                    WHERE c.id_curso <> 1"; // Excluir curso con ID 1
    
            $this->actualizarEstadosPorFecha();
            $params = [];
    
            if (!empty($filtros['modalidad'])) {
                $sql .= " AND c.id_modalidad = :modalidad";
                $params[':modalidad'] = $filtros['modalidad'];
            }
            if (!empty($filtros['estado'])) {
                $sql .= " AND c.id_estatusCurso = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            if (!empty($filtros['turno'])) {
                $sql .= " AND c.turno = :turno";
                $params[':turno'] = $filtros['turno'];
            }
    
            $stmt = $this->conn->prepare($sql);
    
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function obtenerCursoPorId($id) {
        try {
            $sql = "SELECT 
                        c.id_curso,
                        c.nombre_curso,
                        c.fecha_inicio,
                        c.fecha_limite_inicio,
                        c.fecha_fin,
                        c.horas_totales,
                        c.horas_por_clase,
                        c.horas_completadas,
                        c.max_participantes,
                        c.min_participantes,
                        c.num_inscritos,
                        c.id_modalidad,
                        c.tipo_formacion,
                        c.id_estatusCurso,
                        c.id_sectoreconomico,
                        c.id_subtipo,
                        c.id_areaFormativa,
                        c.id_ambito,
                        c.id_ppl,
                        c.id_nombre_especifico,
                        c.turno,
                        c.programa_formacion,
                        m.nombre_modalidad,
                        af.nombre_areaFormativa,
                        se.nombre_sectoreconomico,
                        st.nombre_subtipo,
                        a.nombre_ambito,
                        ec.nombre_estatusCurso AS nombre_estatus,
                        c.fecha_creacion,
                        c.fecha_actualizacion
                    FROM tb_curso c
                    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
                    LEFT JOIN tb_areaformativa af ON c.id_areaFormativa = af.id_areaFormativa
                    LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
                    LEFT JOIN tb_subtipo st ON c.id_subtipo = st.id_subtipo
                    LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                    LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                    WHERE c.id_curso = :id";
    
            $this->actualizarEstadosPorFecha();
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$curso) {
                return ["error" => "Curso no encontrado"];
            }
    
            // Formato de fechas seguro
            $curso['fecha_inicio'] = ($curso['fecha_inicio'] && $curso['fecha_inicio'] !== '0000-00-00') 
                ? date('Y-m-d', strtotime($curso['fecha_inicio'])) 
                : null;
            $curso['fecha_fin'] = ($curso['fecha_fin'] && $curso['fecha_fin'] !== '0000-00-00') 
                ? date('Y-m-d', strtotime($curso['fecha_fin'])) 
                : null;
    
            return $curso;
    
        } catch (PDOException $e) {
            error_log("Error en obtenerCursoPorId: " . $e->getMessage());
            return [
                "error" => "Error de base de datos",
                "detalle" => $e->getMessage()
            ];
        }
    }

 public function editar($data) {
    try {
        if (isset($data['horas_por_clase-Edit']) && $data['horas_por_clase-Edit'] !== '' && $data['horas_por_clase-Edit'] !== null) {
            $hpc = (float)$data['horas_por_clase-Edit'];
            if ($hpc != 4.0 && $hpc != 5.0) {
                return [
                    'success' => false,
                    'message' => 'Horas por clase solo pueden ser 4 o 5.'
                ];
            }
        }

        $sql = "UPDATE tb_curso SET
                nombre_curso        = :nombre_curso,
                fecha_inicio        = :fecha_inicio,
                fecha_limite_inicio = :fecha_limite_inicio,
                fecha_fin           = :fecha_fin,
                horas_totales       = :horas_totales,
                horas_por_clase     = :horas_por_clase,
                max_participantes   = :max_participantes,
                min_participantes   = :min_participantes,
                id_modalidad        = :id_modalidad,
                id_areaFormativa    = :id_areaFormativa,
                id_sectoreconomico  = :id_sectoreconomico,
                id_subtipo          = :id_subtipo,
                id_ambito           = :id_ambito,
                id_ppl              = :id_ppl,
                id_nombre_especifico = :id_nombre_especifico,
                turno               = :turno,
                tipo_formacion      = :tipo_formacion,
                programa_formacion  = :programa_formacion,
                fecha_actualizacion = NOW()
                WHERE id_curso = :id_curso";

        $stmt = $this->conn->prepare($sql);

        $params = [
            ':nombre_curso'        => $data['nombre_curso-Edit']        ?? $data['nombre_curso'],
            ':fecha_inicio'        => $data['fecha_inicio-Edit']        ?? $data['fecha_inicio'],
            ':fecha_limite_inicio' => $data['fecha_limite_inicio-Edit'] ?? $data['fecha_limite_inicio'] ?? null,
            ':fecha_fin'           => $data['fecha_fin-Edit']           ?? $data['fecha_fin'],
            ':horas_totales'       => $data['horas_totales-Edit']       ?? $data['horas_totales']       ?? null,
            ':horas_por_clase'     => $data['horas_por_clase-Edit']     ?? $data['horas_por_clase']     ?? null,
            ':max_participantes'   => $data['max_participantes-Edit']   ?? $data['max_participantes'],
            ':min_participantes'   => $data['min_participantes-Edit']   ?? $data['min_participantes']   ?? null,
            ':id_modalidad'        => $data['id_modalidad-Edit']        ?? $data['id_modalidad'],
            ':id_areaFormativa'    => $data['id_areaFormativa-Edit']    ?? $data['id_areaFormativa'] ?? null,
            ':id_sectoreconomico'  => $data['id_sectoreconomico-Edit']  ?? $data['id_sectoreconomico']  ?? null,
            ':id_subtipo'          => $data['id_subtipo-Edit']          ?? $data['id_subtipo']          ?? null,
            ':id_ambito'           => $data['id_ambito-Edit']           ?? $data['id_ambito']           ?? null,
            ':id_ppl'              => !empty($data['id_ppl-Edit']) ? (int)$data['id_ppl-Edit'] : ($data['id_ppl'] ?? null),
            ':id_nombre_especifico'=> !empty($data['id_nombrecurso-Edit']) ? (int)$data['id_nombrecurso-Edit'] : ($data['id_nombre_especifico'] ?? null),
            ':turno'               => $data['turno-Edit']               ?? $data['turno'],
            ':tipo_formacion'      => $data['tipo_formacion-Edit']      ?? $data['tipo_formacion'],
            ':programa_formacion'  => $data['programa_formacion-Edit']  ?? $data['programa_formacion'],
            ':id_curso'            => $data['id_curso']
        ];

        $stmt->execute($params);

        return [
            'success'       => true,
            'message'       => 'Curso actualizado exitosamente',
            'affected_rows' => $stmt->rowCount()
        ];

    } catch (PDOException $e) {
        error_log("Error en editar curso: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ];
    }
}

    public function verCurso($id) {
        try {
            $sql = "SELECT 
                        c.id_curso,
                        c.nombre_curso,
                        c.fecha_inicio,
                        c.fecha_fin,
                        c.max_participantes,
                        c.num_inscritos,
                        c.id_modalidad,
                        c.tipo_formacion,
                        c.id_ambito,
                        c.id_sectoreconomico,
                        c.id_subtipo,
                        c.id_areaFormativa,
                        c.turno,
                        c.programa_formacion,
                        m.nombre_modalidad AS modalidad,
                        af.nombre_areaFormativa AS area_formativa,
                        ec.nombre_estatusCurso AS estado,
                        se.nombre_sectoreconomico AS sectoreconomico_nombre,
                        st.nombre_subtipo AS subtipo_nombre,
                        a.nombre_ambito AS ambito_nombre
                    FROM tb_curso c
                    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
                    LEFT JOIN tb_areaformativa af ON c.id_areaFormativa = af.id_areaFormativa
                    LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                    LEFT JOIN tb_sectoreconomico se ON c.id_sectoreconomico = se.id_sectoreconomico
                    LEFT JOIN tb_subtipo st ON c.id_subtipo = st.id_subtipo
                    LEFT JOIN tb_ambito a ON c.id_ambito = a.id_ambito
                    WHERE c.id_curso = :id";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$curso) {
                return [
                    'success' => false,
                    'message' => 'Curso no encontrado'
                ];
            }
    
            return [
                'success' => true,
                'data' => [
                    'id' => $curso['id_curso'],
                    'nombre' => $curso['nombre_curso'],
                    'fechaInicio' => $curso['fecha_inicio'],
                    'fechaCulminacion' => $curso['fecha_fin'],
                    'maxParticipantes' => $curso['max_participantes'],
                    'numInscritos' => $curso['num_inscritos'],
                    'turno' => $curso['turno'],
                    'tipoFormacion' => $curso['tipo_formacion'],
                    'ambito' => $curso['ambito_nombre'],
                    'sectorEconomico' => $curso['sectoreconomico_nombre'],
                    'modalidad' => $curso['modalidad'],
                    'areaFormativa' => $curso['area_formativa'],
                    'estado' => $curso['estado'],
                    'programa_formacion' => $curso['programa_formacion'],
                    'subtipo' => $curso['subtipo_nombre'],
                    'id_ambito' => $curso['id_ambito'],
                    'id_sectoreconomico' => $curso['id_sectoreconomico'],
                    'id_subtipo' => $curso['id_subtipo']
                ]
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener el curso: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerDocentePorCurso($idCurso) {
        try {
            $sql = "SELECT 
                        d.id_docente,
                        d.primer_nombre, 
                        d.primer_apellido, 
                        d.cedula, 
                        d.edad
                    FROM tb_docente d
                    INNER JOIN tb_docente_curso dc 
                        ON d.id_docente = dc.id_docente
                    WHERE dc.id_curso = ?
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCurso]);
            $docente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($docente) {
                // Formatear nombre completo
                $docente['nombre_completo'] = $docente['primer_nombre'] . ' ' . $docente['primer_apellido'];
            }
            
            return $docente;
        } catch (PDOException $e) {
            error_log("Error en obtenerDocentePorCurso: " . $e->getMessage());
            return null;
        }
    }

    public function eliminarCurso($id) {
        try {
            // Primero, verificar si el curso tiene participantes asociados
            $sqlVerificar = "SELECT COUNT(*) FROM tb_participante_curso WHERE id_curso = :id";
            $stmtVerificar = $this->conn->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $participantesCount = $stmtVerificar->fetchColumn();

            // Si hay participantes asociados, no se puede eliminar
            if ($participantesCount > 0) {
                return [
                    "success" => false,
                    "message" => "No se puede eliminar el curso, ya que tiene registros de participantes y/o docentes asignados"
                ];
            }

            // Eliminar asignaciones de docentes primero
            $sqlEliminarDocentes = "DELETE FROM tb_docente_curso WHERE id_curso = :id";
            $stmtDocentes = $this->conn->prepare($sqlEliminarDocentes);
            $stmtDocentes->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDocentes->execute();

            // Eliminar el curso
            $sql = "DELETE FROM tb_curso WHERE id_curso = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return [
                    "success" => true,
                    "message" => "Curso eliminado con éxito"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se encontró el Curso con ID: " . $id
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error en la consulta: " . $e->getMessage()
            ];
        }
    }

    public function obtenerParticipantesDisponibles($id_curso, $busqueda = '') {
        try {
            // Obtener el turno del curso actual
            $stmtTurno = $this->conn->prepare("SELECT turno FROM tb_curso WHERE id_curso = :id");
            $stmtTurno->execute([':id' => $id_curso]);
            $turnoActual = $stmtTurno->fetchColumn();

            $sql = "SELECT p.id_participante, 
                           CONCAT(p.primer_nombre, IFNULL(CONCAT(' ', p.segundo_nombre), '')) AS nombre, 
                           CONCAT(p.primer_apellido, IFNULL(CONCAT(' ', p.segundo_apellido), '')) AS apellido, 
                           p.cedula,
                           (SELECT c2.nombre_curso 
                            FROM tb_participante_curso pc2 
                            INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso 
                            WHERE pc2.id_participante = p.id_participante 
                              AND c2.turno = :turno 
                              AND pc2.estatus_participante IN ('Pendiente', 'En formación')
                              AND pc2.id_curso != :id_curso
                            LIMIT 1) as curso_conflicto,
                           (SELECT c2.turno 
                            FROM tb_participante_curso pc2 
                            INNER JOIN tb_curso c2 ON pc2.id_curso = c2.id_curso 
                            WHERE pc2.id_participante = p.id_participante 
                              AND c2.turno = :turno 
                              AND pc2.estatus_participante IN ('Pendiente', 'En formación')
                              AND pc2.id_curso != :id_curso
                            LIMIT 1) as turno_conflicto
                    FROM tb_participante p
                    WHERE NOT EXISTS (
                        SELECT 1 
                        FROM tb_participante_curso pc 
                        WHERE pc.id_participante = p.id_participante 
                        AND pc.id_curso = :id_curso 
                        AND pc.estatus_participante != 'Retirado'
                    )
                    AND (
                        CONCAT(p.primer_nombre, ' ', IFNULL(p.segundo_nombre, ''), ' ', p.primer_apellido, ' ', IFNULL(p.segundo_apellido, '')) LIKE :busqueda 
                        OR p.cedula LIKE :busqueda
                    )
                    ORDER BY curso_conflicto ASC, p.primer_nombre ASC";
            
            $stmt = $this->conn->prepare($sql);
            $busquedaParam = "%$busqueda%";
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->bindParam(':turno', $turnoActual, PDO::PARAM_STR);
            $stmt->bindParam(':busqueda', $busquedaParam, PDO::PARAM_STR);
            $stmt->execute();
            
            $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agregar bandera de conflicto para facilitar al frontend
            foreach ($participantes as &$p) {
                $p['tiene_conflicto'] = !empty($p['curso_conflicto']);
            }
            
            return $participantes;
            
        } catch(PDOException $e) {
            error_log("ERROR obtenerParticipantesDisponibles: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerParticipantesInscritos($id_curso) {
        try {
            $sql = "SELECT 
                        p.id_participante,
                        CONCAT(p.primer_nombre, IFNULL(CONCAT(' ', p.segundo_nombre), '')) AS nombre,
                        CONCAT(p.primer_apellido, IFNULL(CONCAT(' ', p.segundo_apellido), '')) AS apellido,
                        p.cedula,
                        p.edad,
                        p.genero,
                        pc.id_participante_curso
                    FROM tb_participante_curso pc
                    JOIN tb_participante p ON pc.id_participante = p.id_participante
                    WHERE pc.id_curso = :id_curso
                    AND pc.estatus_participante IN ('Pendiente', 'En formación')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error en obtenerParticipantesInscritos: " . $e->getMessage());
            return [];
        }
    }

    public function agregarParticipante($id_participante, $id_curso) {
        try {
            $this->conn->beginTransaction();

            // 1. Verificar si ya está inscrito
        $sqlCheck = "SELECT estatus_participante 
        FROM tb_participante_curso 
        WHERE id_participante = :id_p 
        AND id_curso = :id_c 
        AND estatus_participante != 'Retirado'";
        
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ':id_p' => $id_participante,
            ':id_c' => $id_curso
        ]);

        if ($stmtCheck->rowCount() > 0) {
                throw new Exception("El participante ya está inscrito en este curso con un estatus válido.");
            }

            // 2.1 Verificar si el participante ya tiene un curso en el mismo turno
            // Obtenemos el turno del curso actual (ya lo tenemos en $curso, pero verifiquemos si está en el select)
            // Agregamos turno al select inicial
            $sqlCursoInfo = "SELECT fecha_inicio, fecha_fin, max_participantes, num_inscritos, id_estatusCurso, turno 
                            FROM tb_curso 
                            WHERE id_curso = :id_curso";
            $stmtCursoInfo = $this->conn->prepare($sqlCursoInfo);
            $stmtCursoInfo->execute([':id_curso' => $id_curso]);
            $curso = $stmtCursoInfo->fetch(PDO::FETCH_ASSOC);

            // Re-validamos info del curso con el nuevo select
            if (!$curso || $curso['id_estatusCurso'] != 1) {
                throw new Exception("Curso no disponible para asignaciones");
            }

            if ($curso['num_inscritos'] >= $curso['max_participantes']) {
                throw new Exception("El curso ha alcanzado el máximo de participantes");
            }
            
            $turnoCurso = $curso['turno'];
            $fechaInicio = $curso['fecha_inicio'];
            $fechaFin = $curso['fecha_fin'];

            if ($turnoCurso) {
                 // Buscamos si ya tiene cursos activos (Pendiente o En formación) en ese turno
                 $sqlCheckTurno = "
                    SELECT COUNT(*) 
                    FROM tb_participante_curso pc
                    INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                    WHERE pc.id_participante = :id_p
                      AND c.turno = :turno
                      AND pc.estatus_participante IN ('Pendiente', 'En formación')
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

            // 3. Insertar nuevo registro
            $sqlInsert = "INSERT INTO tb_participante_curso 
                        (id_participante, id_curso, fecha_inscripcion, FechaInicio, FechaFin, estatus_participante)
                        VALUES (:id_p, :id_c, CURDATE(), :fecha_inicio, :fecha_fin, 'Pendiente')";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->execute([
                ':id_p' => $id_participante,
                ':id_c' => $id_curso,
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin' => $fechaFin
            ]);

            // 4. Incrementar contador de inscritos
            $sqlUpdateInscritos = "UPDATE tb_curso 
                                SET num_inscritos = num_inscritos + 1 
                                WHERE id_curso = :id_c";
            $stmtUpdate = $this->conn->prepare($sqlUpdateInscritos);
            $stmtUpdate->execute([':id_c' => $id_curso]);

            // 5. Actualizar estado del participante
            $sqlParticipante = "UPDATE tb_participante 
                                SET estado = 'Asignado' 
                                WHERE id_participante = :id_p 
                                AND estado != 'Asignado'";
            $stmtParticipante = $this->conn->prepare($sqlParticipante);
            $stmtParticipante->execute([':id_p' => $id_participante]);

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Participante agregado exitosamente'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error PDO: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error General: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function eliminarParticipante($id_participante_curso) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener id_curso e id_participante
            $sqlSelect = "SELECT id_curso, id_participante 
                        FROM tb_participante_curso 
                        WHERE id_participante_curso = :id";
            $stmtSelect = $this->conn->prepare($sqlSelect);
            $stmtSelect->execute([':id' => $id_participante_curso]);
            $info = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if (!$info) {
                throw new Exception("Registro no encontrado");
            }

            $id_participante = $info['id_participante'];
            $id_curso = $info['id_curso'];

            // 2. Eliminar registro
            $sqlDelete = "DELETE FROM tb_participante_curso 
                        WHERE id_participante_curso = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([':id' => $id_participante_curso]);
            $deleted = $stmtDelete->rowCount() > 0;

            // 3. Actualizar contador del curso
            if ($deleted) {
                $sqlUpdateCurso = "UPDATE tb_curso 
                                SET num_inscritos = num_inscritos - 1 
                                WHERE id_curso = :id_curso";
                $stmtUpdateCurso = $this->conn->prepare($sqlUpdateCurso);
                $stmtUpdateCurso->execute([':id_curso' => $id_curso]);
            }

            // 4. Verificar si el participante no tiene más cursos
            if ($deleted) {
                $sqlCheckCursos = "SELECT COUNT(*) 
                                FROM tb_participante_curso 
                                WHERE id_participante = :id_participante";
                $stmtCheck = $this->conn->prepare($sqlCheckCursos);
                $stmtCheck->execute([':id_participante' => $id_participante]);
                $totalCursos = $stmtCheck->fetchColumn();

                // Actualizar estado si no tiene más cursos
                if ($totalCursos == 0) {
                    $sqlUpdateEstado = "UPDATE tb_participante 
                                    SET estado = 'En sistema' 
                                    WHERE id_participante = :id_participante";
                    $stmtUpdateEstado = $this->conn->prepare($sqlUpdateEstado);
                    $stmtUpdateEstado->execute([':id_participante' => $id_participante]);
                }
            }

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Participante eliminado exitosamente'
            ];

        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en base de datos: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        } catch(Exception $e) {
            $this->conn->rollBack();
            error_log("Error general: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerInfoCurso($id_curso) {
        try {
            $sql = "SELECT num_inscritos, max_participantes, id_estatusCurso 
                    FROM tb_curso 
                    WHERE id_curso = :id_curso";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error en obtenerInfoCurso: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerParticipantesPorCurso($id_curso) {
        try {
            $sql = "SELECT 
                        pc.id_participante_curso,
                        pc.estatus_participante,
                        p.primer_nombre,
                        p.primer_apellido,
                        p.cedula,
                        CONCAT(p.primer_nombre, ' ', p.primer_apellido) AS nombre_completo
                    FROM tb_participante_curso pc
                    INNER JOIN tb_participante p ON pc.id_participante = p.id_participante
                    WHERE pc.id_curso = :id_curso
                    AND pc.estatus_participante IN ('Pendiente', 'En formación')";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerParticipantesPorCurso: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerIdEstatusCurso($id_curso) {
        try {
            $sql = "SELECT id_estatusCurso FROM tb_curso WHERE id_curso = :id_curso";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['id_estatusCurso'] : null;
        } catch (PDOException $e) {
            error_log("Error en obtenerIdEstatusCurso: " . $e->getMessage());
            return null;
        }
    }

    public function finalizarCurso($id_curso) {
        try {
            // 5 = Culminado
            $sql = "UPDATE tb_curso SET id_estatusCurso = 5 WHERE id_curso = :id_curso";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al finalizar curso: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerCursoBasico($id_curso) {
        try {
            $sql = "SELECT id_curso, nombre_curso, fecha_fin FROM tb_curso WHERE id_curso = :id_curso";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCursoBasico: " . $e->getMessage());
            return null;
        }
    }

    public function guardarNotas($idParticipanteCurso, $estado) {
        try {
            $sql = "UPDATE tb_participante_curso SET estatus_participante = ? WHERE id_participante_curso = ?";
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([$estado, $idParticipanteCurso]);
            
            return [
                'success' => $success,
                'message' => $success ? 'Notas guardadas exitosamente' : 'Error al guardar notas'
            ];
        } catch (PDOException $e) {
            error_log("Error en guardarNotas: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

    public function cambiarEstadoCurso($idCurso, $nuevoEstado) {
        try {
            $sql = "UPDATE tb_curso SET id_estatusCurso = ? WHERE id_curso = ?";
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([$nuevoEstado, $idCurso]);
            
            return [
                'success' => $success,
                'message' => $success ? 'Estado del curso actualizado' : 'Error al cambiar estado'
            ];
        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoCurso: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

    public function actualizarEstadoDocente(int $idDocente) {
        try {
            $sql = "UPDATE tb_docente
                    SET estado_docente = 'Asignado'
                    WHERE id_docente = :docente
                    AND estado_docente <> 'En formación'";
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([':docente' => $idDocente]);
            
            return [
                'success' => $success,
                'message' => $success ? 'Estado del docente actualizado' : 'Error al actualizar estado'
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizarEstadoDocente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

    // Método adicional para obtener todos los datos de referencia (para formularios)
    public function obtenerDatosReferencia() {
        try {
            $datos = [];
            
            // Obtener modalidades
            $stmtModalidades = $this->conn->query("SELECT * FROM tb_modalidad ORDER BY nombre_modalidad");
            $datos['modalidades'] = $stmtModalidades->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener áreas formativas
            $stmtAreas = $this->conn->query("SELECT * FROM tb_areaformativa ORDER BY nombre_areaFormativa");
            $datos['areas'] = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener sectores económicos
            $stmtSectores = $this->conn->query("SELECT * FROM tb_sectoreconomico ORDER BY nombre_sectoreconomico");
            $datos['sectores'] = $stmtSectores->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener subtipos
            $stmtSubtipos = $this->conn->query("SELECT * FROM tb_subtipo ORDER BY nombre_subtipo");
            $datos['subtipos'] = $stmtSubtipos->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener ámbitos
            $stmtAmbitos = $this->conn->query("SELECT * FROM tb_ambito ORDER BY nombre_ambito");
            $datos['ambitos'] = $stmtAmbitos->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener estados de curso
            $stmtEstados = $this->conn->query("SELECT * FROM tb_estatuscurso ORDER BY id_estatusCurso");
            $datos['estados'] = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);
            
            return $datos;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDatosReferencia: " . $e->getMessage());
            return [];
        }
    }

public function reiniciarCurso(
        $idCurso,
        $nuevaFechaInicio,
        $nuevaFechaFin,
        $modo = 'diferido',
        $mantenerParticipantes = false,
        $id_modalidad = null,
        $id_sectoreconomico = null,
        $id_subtipo = null,
        $id_ambito = null,
        $turno = null,
        $horas_por_clase = null,
        $horas_totales = null,
        $min_participantes = null,
        $max_participantes = null
    ) {
    try {
        // Validar que el curso exista y tenga estado válido
        $sqlCheck = "SELECT id_estatusCurso, id_modalidad, id_sectoreconomico, id_subtipo, id_ambito, turno, horas_por_clase, horas_totales, min_participantes, max_participantes FROM tb_curso WHERE id_curso = :id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $idCurso]);
        $current = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$current) {
            return ['success' => false, 'message' => 'El curso no existe.'];
        }

        $estadoActual = (int) $current['id_estatusCurso'];

        if (!$estadoActual) {
            return ['success' => false, 'message' => 'El curso no existe.'];
        }
        if (!in_array($estadoActual, [2, 5])) {
            return ['success' => false, 'message' => 'Solo se pueden reiniciar cursos Diferidos o Culminados.'];
        }

        // Validar fechas
        $fechaInicio = DateTime::createFromFormat('Y-m-d', $nuevaFechaInicio);
        $fechaFin    = DateTime::createFromFormat('Y-m-d', $nuevaFechaFin);
        if (!$fechaInicio || !$fechaFin) {
            return ['success' => false, 'message' => 'Formato de fechas inválido.'];
        }
        if ($fechaInicio >= $fechaFin) {
            return ['success' => false, 'message' => 'La fecha de inicio debe ser anterior a la fecha de fin.'];
        }

        // Normalizar los nuevos valores usando los del curso actual cuando no se proporcionan.
        $id_modalidad       = !empty($id_modalidad) ? (int)$id_modalidad : (int)$current['id_modalidad'];
        $id_sectoreconomico = !empty($id_sectoreconomico) ? (int)$id_sectoreconomico : (int)$current['id_sectoreconomico'];
        $id_subtipo         = !empty($id_subtipo) ? (int)$id_subtipo : (int)$current['id_subtipo'];
        $id_ambito          = !empty($id_ambito) ? (int)$id_ambito : (int)$current['id_ambito'];
        $turno              = !empty($turno) ? $turno : $current['turno'];
        $horas_por_clase    = !empty($horas_por_clase) ? (int)$horas_por_clase : (int)$current['horas_por_clase'];
        $horas_totales      = !empty($horas_totales) ? (int)$horas_totales : (int)$current['horas_totales'];
        $min_participantes  = !empty($min_participantes) ? (int)$min_participantes : (int)$current['min_participantes'];
        $max_participantes  = !empty($max_participantes) ? (int)$max_participantes : (int)$current['max_participantes'];

        if ($id_modalidad <= 0 || $id_sectoreconomico <= 0 || $id_subtipo <= 0 || $id_ambito <= 0) {
            return ['success' => false, 'message' => 'Debe seleccionar modalidad, sector económico, subtipo y ámbito válidos.'];
        }
        if (!in_array($turno, ['Mañana', 'Tarde'])) {
            return ['success' => false, 'message' => 'El turno debe ser Mañana o Tarde.' ];
        }
        if ($horas_por_clase !== 4 && $horas_por_clase !== 5) {
            return ['success' => false, 'message' => 'Las horas por clase deben ser 4 o 5.' ];
        }
        if ($horas_totales <= 0) {
            return ['success' => false, 'message' => 'Las horas totales deben ser mayores a cero.' ];
        }
        if ($min_participantes < 12 || $min_participantes > 40) {
            return ['success' => false, 'message' => 'El mínimo de participantes debe estar entre 12 y 40.' ];
        }
        if ($max_participantes < 12 || $max_participantes > 40) {
            return ['success' => false, 'message' => 'El máximo de participantes debe estar entre 12 y 40.' ];
        }
        if ($min_participantes > $max_participantes) {
            return ['success' => false, 'message' => 'El mínimo de participantes no puede ser mayor que el máximo.' ];
        }

        // Calcular fecha límite (9 días después)
        $fechaLimite = clone $fechaInicio;
        $fechaLimite->modify('+9 days');
        $nuevaFechaLimite = $fechaLimite->format('Y-m-d');

        $this->conn->beginTransaction();

        // ============================================================
        // Obtener participantes ANTES de cualquier operación
        // ============================================================
        $sqlGetPart = "SELECT DISTINCT id_participante 
                       FROM tb_participante_curso 
                       WHERE id_curso = :id_curso";
        $stmtGetPart = $this->conn->prepare($sqlGetPart);
        $stmtGetPart->execute([':id_curso' => $idCurso]);
        $participantes = $stmtGetPart->fetchAll(PDO::FETCH_COLUMN);

        if ($modo === 'culminado' || ($modo === 'diferido' && !$mantenerParticipantes)) {
            // ============================================================
            // SIN PARTICIPANTES: eliminar inscritos y poner num_inscritos=0
            // ============================================================

            // Actualizar estado de cada participante en tb_participante
            foreach ($participantes as $idParticipante) {
                // Verificar otros cursos del participante
                $sqlOtros = "SELECT 
                                SUM(CASE WHEN c.id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
                                SUM(CASE WHEN c.id_estatusCurso IN (1, 4) THEN 1 ELSE 0 END) as asignado,
                                SUM(CASE WHEN c.id_estatusCurso = 2 THEN 1 ELSE 0 END) as diferido
                             FROM tb_participante_curso pc
                             INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                             WHERE pc.id_participante = :id_p
                               AND pc.id_curso != :id_c
                               AND pc.estatus_participante IN ('Pendiente', 'En formación')";
                $stmtOtros = $this->conn->prepare($sqlOtros);
                $stmtOtros->execute([':id_p' => $idParticipante, ':id_c' => $idCurso]);
                $counts = $stmtOtros->fetch(PDO::FETCH_ASSOC);

                if ($counts['en_proceso'] > 0) {
                    $nuevoEstadoPart = 'En formación';
                } elseif ($counts['asignado'] > 0) {
                    $nuevoEstadoPart = 'Asignado';
                } elseif ($counts['diferido'] > 0) {
                    $nuevoEstadoPart = 'Diferido';
                } else {
                    $nuevoEstadoPart = 'En sistema';
                }

                $sqlUpdPart = "UPDATE tb_participante 
                               SET estado = :estado 
                               WHERE id_participante = :id_p";
                $stmtUpdPart = $this->conn->prepare($sqlUpdPart);
                $stmtUpdPart->execute([':estado' => $nuevoEstadoPart, ':id_p' => $idParticipante]);
            }

            // Eliminar participantes del curso
            $sqlDel = "DELETE FROM tb_participante_curso WHERE id_curso = :id_curso";
            $stmtDel = $this->conn->prepare($sqlDel);
            $stmtDel->execute([':id_curso' => $idCurso]);

            // Actualizar curso — num_inscritos = 0, estado = 1 (En espera)
            $sqlCurso = "UPDATE tb_curso SET
                             fecha_inicio        = :fecha_inicio,
                             fecha_limite_inicio = :fecha_limite,
                             fecha_fin           = :fecha_fin,
                             id_estatusCurso     = 1,
                             horas_por_clase     = :horas_por_clase,
                             horas_totales       = :horas_totales,
                             min_participantes   = :min_participantes,
                             max_participantes   = :max_participantes,
                             id_modalidad        = :id_modalidad,
                             id_sectoreconomico  = :id_sectoreconomico,
                             id_subtipo          = :id_subtipo,
                             id_ambito           = :id_ambito,
                             turno               = :turno,
                             horas_completadas   = 0,
                             num_inscritos       = 0,
                             fecha_actualizacion = NOW()
                         WHERE id_curso = :id";
            $stmtCurso = $this->conn->prepare($sqlCurso);
            $stmtCurso->execute([
                ':fecha_inicio' => $nuevaFechaInicio,
                ':fecha_limite' => $nuevaFechaLimite,
                ':fecha_fin'    => $nuevaFechaFin,
                ':horas_por_clase' => $horas_por_clase,
                ':horas_totales' => $horas_totales,
                ':min_participantes' => $min_participantes,
                ':max_participantes' => $max_participantes,
                ':id_modalidad' => $id_modalidad,
                ':id_sectoreconomico' => $id_sectoreconomico,
                ':id_subtipo' => $id_subtipo,
                ':id_ambito' => $id_ambito,
                ':turno' => $turno,
                ':id'           => $idCurso
            ]);

        } else {
            // ============================================================
            // CON PARTICIPANTES (diferido + mantenerParticipantes=true)
            // Conservar inscritos, actualizar fechas, poner Pendiente
            // ============================================================

            // Actualizar fechas y estatus de participantes en el curso
            $sqlPartCurso = "UPDATE tb_participante_curso
                             SET estatus_participante = 'Pendiente',
                                 FechaInicio          = :fecha_inicio,
                                 FechaFin             = :fecha_fin
                             WHERE id_curso = :id_curso";
            $stmtPartCurso = $this->conn->prepare($sqlPartCurso);
            $stmtPartCurso->execute([
                ':fecha_inicio' => $nuevaFechaInicio,
                ':fecha_fin'    => $nuevaFechaFin,
                ':id_curso'     => $idCurso
            ]);

            // Actualizar estado general de cada participante en tb_participante
            foreach ($participantes as $idParticipante) {
                // Verificar estado de todos los cursos del participante (incluyendo el actual que ahora es 'En espera' = 1)
                $sqlCheck = "SELECT 
                                SUM(CASE WHEN c.id_estatusCurso = 3 THEN 1 ELSE 0 END) as en_proceso,
                                SUM(CASE WHEN c.id_estatusCurso IN (1, 4) THEN 1 ELSE 0 END) as asignado,
                                SUM(CASE WHEN c.id_estatusCurso = 2 THEN 1 ELSE 0 END) as diferido
                             FROM tb_participante_curso pc
                             INNER JOIN tb_curso c ON pc.id_curso = c.id_curso
                             WHERE pc.id_participante = :id_p
                               AND pc.estatus_participante IN ('Pendiente', 'En formación')";
                $stmtCheck = $this->conn->prepare($sqlCheck);
                $stmtCheck->execute([':id_p' => $idParticipante]);
                $counts = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($counts['en_proceso'] > 0) {
                    $nuevoEstadoPart = 'En formación';
                } elseif ($counts['asignado'] > 0) {
                    $nuevoEstadoPart = 'Asignado';
                } elseif ($counts['diferido'] > 0) {
                    $nuevoEstadoPart = 'Diferido';
                } else {
                    $nuevoEstadoPart = 'En sistema';
                }

                $sqlUpdPart = "UPDATE tb_participante 
                               SET estado = :estado 
                               WHERE id_participante = :id_p";
                $stmtUpdPart = $this->conn->prepare($sqlUpdPart);
                $stmtUpdPart->execute([':estado' => $nuevoEstadoPart, ':id_p' => $idParticipante]);
            }

            // Actualizar curso — estado = 1 (En espera), conserva num_inscritos
            $sqlCurso = "UPDATE tb_curso SET
                             fecha_inicio        = :fecha_inicio,
                             fecha_limite_inicio = :fecha_limite,
                             fecha_fin           = :fecha_fin,
                             id_estatusCurso     = 1,
                             horas_por_clase     = :horas_por_clase,
                             horas_totales       = :horas_totales,
                             min_participantes   = :min_participantes,
                             max_participantes   = :max_participantes,
                             id_modalidad        = :id_modalidad,
                             id_sectoreconomico  = :id_sectoreconomico,
                             id_subtipo          = :id_subtipo,
                             id_ambito           = :id_ambito,
                             turno               = :turno,
                             horas_completadas   = 0,
                             fecha_actualizacion = NOW()
                         WHERE id_curso = :id";
            $stmtCurso = $this->conn->prepare($sqlCurso);
            $stmtCurso->execute([
                ':fecha_inicio' => $nuevaFechaInicio,
                ':fecha_limite' => $nuevaFechaLimite,
                ':fecha_fin'    => $nuevaFechaFin,
                ':horas_por_clase' => $horas_por_clase,
                ':horas_totales' => $horas_totales,
                ':min_participantes' => $min_participantes,
                ':max_participantes' => $max_participantes,
                ':id_modalidad' => $id_modalidad,
                ':id_sectoreconomico' => $id_sectoreconomico,
                ':id_subtipo' => $id_subtipo,
                ':id_ambito' => $id_ambito,
                ':turno' => $turno,
                ':id'           => $idCurso
            ]);
        }

        // Verificar que el UPDATE del curso fue exitoso
        if ($stmtCurso->rowCount() === 0) {
            throw new Exception("No se pudo actualizar el curso. Verifique que existe.");
        }

        // ============================================================
        // COMÚN: actualizar estado de docentes asignados
        // ============================================================
        $sqlDocentes = "SELECT id_docente FROM tb_docente_curso WHERE id_curso = :id_curso";
        $stmtDoc = $this->conn->prepare($sqlDocentes);
        $stmtDoc->execute([':id_curso' => $idCurso]);
        $docentes = $stmtDoc->fetchAll(PDO::FETCH_COLUMN);

        foreach ($docentes as $idDocente) {
            $sqlActivos = "SELECT COUNT(*)
                           FROM tb_docente_curso dc
                           INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                           WHERE dc.id_docente = :id_d
                             AND c.id_estatusCurso = 3
                             AND dc.id_curso != :id_c";
            $stmtAct = $this->conn->prepare($sqlActivos);
            $stmtAct->execute([':id_d' => $idDocente, ':id_c' => $idCurso]);
            $tieneActivos = (int)$stmtAct->fetchColumn() > 0;

            $nuevoEstadoDoc = $tieneActivos ? 'En formación' : 'Asignado';

            $sqlUpdDoc = "UPDATE tb_docente 
                          SET estado_docente = :estado 
                          WHERE id_docente = :id_d";
            $stmtUpdDoc = $this->conn->prepare($sqlUpdDoc);
            $stmtUpdDoc->execute([':estado' => $nuevoEstadoDoc, ':id_d' => $idDocente]);
        }

        $this->conn->commit();

        $mensaje = ($modo === 'culminado' || !$mantenerParticipantes)
            ? 'Curso reiniciado en estado En espera. Los participantes fueron removidos.'
            : 'Curso reiniciado en estado En espera. Los participantes se conservaron con las nuevas fechas.';

        return ['success' => true, 'message' => $mensaje];

    } catch (PDOException $e) {
        $this->conn->rollBack();
        error_log("Error PDO reiniciarCurso: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error reiniciarCurso: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

} 
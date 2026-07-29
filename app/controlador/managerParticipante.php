<?php
// Conexion a la base de datos
require_once __DIR__ . '/../modelo/conexion.php';

// Clase Participante
class Participante {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function getConexion() {
        return $this->conn;
    }


    // Método para verificar si la cédula ya está registrada
    public function verificarCedula($cedula) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_participante WHERE cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetchColumn() > 0;
    }


// Método para verificar si la cédula ya está registrada excluyendo el ID actual
public function verificarCedulaEditar($cedula, $idParticipante) {
    $stmt = $this->conn->prepare(
        "SELECT COUNT(*) 
        FROM tb_participante 
        WHERE cedula = ? 
        AND id_participante != ?"
    );
    
    $stmt->execute([$cedula, $idParticipante]);
    return $stmt->fetchColumn() > 0;
}
public function registrar($data) {
    $this->conn->beginTransaction();
    try {
        $fechaNacimiento = new DateTime($data['fecha_nacimiento']);
        $edad = (new DateTime())->diff($fechaNacimiento)->y;

        // Insertar participante con estado fijo 'En sistema'
        $sqlParticipante = "INSERT INTO tb_participante (
            primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
            cedula, nacionalidad, edad, correo, fecha_nacimiento, estado, grado_institucion, genero, id_discapacidad
        ) VALUES (
            :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
            :cedula, :nacionalidad, :edad, :correo, :fecha_nacimiento, 'En sistema', :grado_institucion, :genero, :id_discapacidad
        )";

        $stmt = $this->conn->prepare($sqlParticipante);
        $stmt->execute([
            ':primer_nombre' => $data['primer_nombre'],
            ':segundo_nombre' => $data['segundo_nombre'],
            ':primer_apellido' => $data['primer_apellido'],
            ':segundo_apellido' => $data['segundo_apellido'],
            ':cedula' => $data['cedula'],
            ':nacionalidad' => $data['nacionalidad'] ?? 'V',
            ':edad' => $edad,
            ':correo' => $data['correo'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':grado_institucion' => $data['grado_institucion'],
            ':genero' => $data['genero'],
            ':id_discapacidad' => $data['id_discapacidad'] ?? null
        ]);

        $id_participante = $this->conn->lastInsertId();

        // Registrar múltiples discapacidades
        if (isset($data['discapacidades']) && is_array($data['discapacidades'])) {
            $sqlDisc = "INSERT INTO tb_participante_discapacidad (id_participante, id_discapacidad) VALUES (:id_participante, :id_discapacidad)";
            $stmtDisc = $this->conn->prepare($sqlDisc);
            foreach ($data['discapacidades'] as $id_disc) {
                if ((int)$id_disc > 1) {
                    $stmtDisc->execute([
                        ':id_participante' => $id_participante,
                        ':id_discapacidad' => (int)$id_disc
                    ]);
                }
            }
        }

        try {
            if (isset($data['telefono']) && !empty($data['telefono'])) {
                $sqlInsertTel = "INSERT INTO tb_telefono (numero_de_telefono, tipo_de_telefono, id_participante) 
                                VALUES (:telefono, 'Móvil', :id_participante)";
                $stmtInsertTel = $this->conn->prepare($sqlInsertTel);
                $stmtInsertTel->execute([':telefono' => $data['telefono'], ':id_participante' => $id_participante]);
            }
        } catch (PDOException $e) {
            error_log("Error al guardar teléfono: " . $e->getMessage());
        }

        // Si hay curso seleccionado, asignarlo
        if (!empty($data['id_curso'])) {
            $sqlUpdateEstado = "UPDATE tb_participante SET estado = 'Asignado' WHERE id_participante = :id_participante";
            $stmtUpdateEstado = $this->conn->prepare($sqlUpdateEstado);
            $stmtUpdateEstado->execute([':id_participante' => $id_participante]);
            
            // Verificar y actualizar cupo del curso
            $sqlUpdateCurso = "UPDATE tb_curso 
                               SET num_inscritos = num_inscritos + 1 
                               WHERE id_curso = :id_curso 
                               AND num_inscritos < max_participantes";
            
            $stmtUpdate = $this->conn->prepare($sqlUpdateCurso);
            $stmtUpdate->execute([':id_curso' => $data['id_curso']]);
            $rowsUpdated = $stmtUpdate->rowCount();

            if ($rowsUpdated === 0) {
                throw new Exception("No hay cupos disponibles en el curso seleccionado.");
            }

            // Obtener fechas del curso
            $sqlFechasCurso = "SELECT fecha_inicio, fecha_fin FROM tb_curso WHERE id_curso = :id_curso";
            $stmtFechas = $this->conn->prepare($sqlFechasCurso);
            $stmtFechas->execute([':id_curso' => $data['id_curso']]);
            $fechasCurso = $stmtFechas->fetch(PDO::FETCH_ASSOC);

            if (!$fechasCurso) {
                throw new Exception("El curso seleccionado no existe.");
            }

            // Insertar en participante_curso
            $sqlParticipanteCurso = "INSERT INTO tb_participante_curso (
                id_participante, id_curso, fecha_inscripcion, FechaInicio, FechaFin, estatus_participante
            ) VALUES (
                :id_participante, :id_curso, :fecha_inscripcion, :fecha_inicio, :fecha_fin, :estatus_participante
            )";

            $stmtCurso = $this->conn->prepare($sqlParticipanteCurso);
            $stmtCurso->execute([
                ':id_participante' => $id_participante,
                ':id_curso' => $data['id_curso'],
                ':fecha_inscripcion' => date('Y-m-d'),
                ':fecha_inicio' => $fechasCurso['fecha_inicio'],
                ':fecha_fin' => $fechasCurso['fecha_fin'],
                ':estatus_participante' => 'Asignado'
            ]);
        }

        $this->conn->commit();

        return [
            "success" => true,
            "message" => "Participante registrado con éxito",
            "id" => $id_participante
        ];

    } catch (Exception $e) {
        $this->conn->rollBack();
        return [
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ];
    }
}

// Método para eliminar un participante
public function eliminar($id) {
    try {
        // Verificar estado del participante
        $sqlCheckEstado = "SELECT estado FROM tb_participante WHERE id_participante = :id";
        $stmtCheckEstado = $this->conn->prepare($sqlCheckEstado);
        $stmtCheckEstado->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheckEstado->execute();
        $participante = $stmtCheckEstado->fetch(PDO::FETCH_ASSOC);

        if (!$participante) {
            return [
                "success" => false,
                "message" => "No se encontró el participante."
            ];
        }

        if ($participante['estado'] != 'En sistema') {
            return [
                "success" => false,
                "message" => "¡No se puede eliminar un participante en curso o con registros historicos!."
            ];
        }

        // Verificar inscripciones en cursos
        $sqlCheckCursos = "SELECT COUNT(*) AS count FROM tb_participante_curso WHERE id_participante = :id";
        $stmtCheckCursos = $this->conn->prepare($sqlCheckCursos);
        $stmtCheckCursos->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheckCursos->execute();
        $resultCursos = $stmtCheckCursos->fetch(PDO::FETCH_ASSOC);

        if ($resultCursos['count'] > 0) {
            return [
                "success" => false,
                "message" => "No se puede eliminar un participante inscrito en un curso."
            ];
        }

        // Eliminar participante
        $sqlDelete = "DELETE FROM tb_participante WHERE id_participante = :id";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtDelete->execute();

        if ($stmtDelete->rowCount() > 0) {
            return [
                "success" => true,
                "message" => "Participante eliminado con éxito."
            ];
        } else {
            return [
                "success" => false,
                "message" => "No se pudo eliminar el participante."
            ];
        }
    } catch (PDOException $e) {
        return [
            "success" => false,
            "message" => "Error en la consulta: " . $e->getMessage()
        ];
    }
}

    public function actualizarParticipante($data) {
        try {
            $this->conn->beginTransaction();
            
            // Validar y calcular edad
            if (empty($data['fecha_nacimiento'])) {
                throw new Exception("Fecha de nacimiento requerida");
            }
            
            $fechaNacimiento = new DateTime($data['fecha_nacimiento']);
            $edad = (new DateTime())->diff($fechaNacimiento)->y;

            // Preparar datos para actualización de participante
            $sql = "UPDATE tb_participante SET 
                    primer_nombre = :primer_nombre,
                    segundo_nombre = :segundo_nombre,
                    primer_apellido = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    cedula = :cedula,
                    nacionalidad = :nacionalidad,
                    edad = :edad,
                    correo = :correo,
                    fecha_nacimiento = :fecha_nacimiento,
                    grado_institucion = :grado_institucion,
                    genero = :genero,
                    id_discapacidad = :id_discapacidad
                    WHERE id_participante = :id_participante";

            $stmt = $this->conn->prepare($sql);
            
            $params = [
                ':id_participante' => $data['id_participante'],
                ':primer_nombre' => $data['primer_nombre'],
                ':segundo_nombre' => $data['segundo_nombre'] ?? null,
                ':primer_apellido' => $data['primer_apellido'],
                ':segundo_apellido' => $data['segundo_apellido'] ?? null,
                ':cedula' => $data['cedula'],
                ':nacionalidad' => $data['nacionalidad'] ?? 'V',
                ':edad' => $edad,
                ':correo' => $data['correo'],
                ':fecha_nacimiento' => $data['fecha_nacimiento'],
                ':grado_institucion' => $data['grado_institucion'],
                ':genero' => $data['genero'],
                ':id_discapacidad' => $data['id_discapacidad'] ?? null
            ];

            $result = $stmt->execute($params);

            if (!$result) {
                throw new Exception("Error al actualizar el participante en la base de datos");
            }

            // Actualizar discapacidades múltiples
            if (isset($data['discapacidades']) && is_array($data['discapacidades'])) {
                $sqlDelDisc = "DELETE FROM tb_participante_discapacidad WHERE id_participante = :id_participante";
                $stmtDelDisc = $this->conn->prepare($sqlDelDisc);
                $stmtDelDisc->execute([':id_participante' => $data['id_participante']]);

                $sqlInsertDisc = "INSERT INTO tb_participante_discapacidad (id_participante, id_discapacidad) VALUES (:id_participante, :id_discapacidad)";
                $stmtInsertDisc = $this->conn->prepare($sqlInsertDisc);
                foreach ($data['discapacidades'] as $id_disc) {
                    if ((int)$id_disc > 1) {
                        $stmtInsertDisc->execute([
                            ':id_participante' => $data['id_participante'],
                            ':id_discapacidad' => (int)$id_disc
                        ]);
                    }
                }
            }

            // Actualizar o insertar teléfono (tipo 'Móvil')
            if (isset($data['telefono']) && !empty($data['telefono'])) {
                $sqlCheckTel = "SELECT id_telefono FROM tb_telefono WHERE id_participante = :id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1";
                $stmtCheckTel = $this->conn->prepare($sqlCheckTel);
                $stmtCheckTel->execute([':id_participante' => $data['id_participante']]);
                $existingTel = $stmtCheckTel->fetch(PDO::FETCH_ASSOC);

                if ($existingTel) {
                    $sqlUpdateTel = "UPDATE tb_telefono SET numero_de_telefono = :telefono WHERE id_telefono = :id_telefono";
                    $stmtUpdateTel = $this->conn->prepare($sqlUpdateTel);
                    $stmtUpdateTel->execute([
                        ':telefono' => $data['telefono'],
                        ':id_telefono' => $existingTel['id_telefono']
                    ]);
                } else {
                    $sqlInsertTel = "INSERT INTO tb_telefono (numero_de_telefono, tipo_de_telefono, id_participante) 
                                    VALUES (:telefono, 'Móvil', :id_participante)";
                    $stmtInsertTel = $this->conn->prepare($sqlInsertTel);
                    $stmtInsertTel->execute([
                        ':telefono' => $data['telefono'],
                        ':id_participante' => $data['id_participante']
                    ]);
                }
            }

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error PDO en actualizarParticipante: " . $e->getMessage());
            
            // Verificar si es error de duplicado de cédula
            if ($e->errorInfo[1] == 1062) {
                throw new PDOException("La cédula ya está registrada en otro participante", 1062);
            }
            
            throw $e;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en actualizarParticipante: " . $e->getMessage());
            throw $e;
        }
    }


      // Método para obtener un participante por ID
      public function verParticipante($id) {
        try {
            $sql = "SELECT 
                        p.id_participante,
                        CONCAT(p.primer_nombre, IFNULL(CONCAT(' ', p.segundo_nombre), '')) AS nombre,
                        CONCAT(p.primer_apellido, IFNULL(CONCAT(' ', p.segundo_apellido), '')) AS apellido,
                        p.cedula,
                        p.edad,
                        p.correo,
                        (SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = p.id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1) AS telefono,
                        DATE_FORMAT(p.fecha_nacimiento, '%d/%m/%Y') as fecha_nacimiento,
                        DATE_FORMAT(p.fecha_registro, '%d/%m/%Y') as fecha_registro,
                        p.grado_institucion,
                        p.genero,
                        COALESCE(d.nombre_discapacidad, 'Ninguna') AS nombre_discapacidad
                    FROM tb_participante p
                    LEFT JOIN tb_discapacidad d ON p.id_discapacidad = d.id_discapacidad
                    WHERE p.id_participante = :id";
     
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
     
            $participante = $stmt->fetch(PDO::FETCH_ASSOC);
     
            if ($participante) {
                return [
                    'success' => true,
                    'data' => [
                        'nombre' => $participante['nombre'],
                        'apellido' => $participante['apellido'],
                        'cedula' => $participante['cedula'],
                        'edad' => $participante['edad'],
                        'correo' => $participante['correo'],
                        'telefono' => $participante['telefono'],
                        'fecha_nacimiento' => $participante['fecha_nacimiento'],
                        'fecha_registro' => $participante['fecha_registro'],
                        'grado_institucion' => $participante['grado_institucion'],
                        'genero' => $participante['genero'],
                        'nombre_discapacidad' => $participante['nombre_discapacidad']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Participante no encontrado'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerParticipantes() {
        try {
            $sql = "SELECT id_participante, CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, edad, correo, (SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = tb_participante.id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1) AS telefono, DATE_FORMAT(fecha_registro, '%d/%m/%Y') as fecha_registro, grado_institucion, genero, estado FROM tb_participante";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerParticipantes: " . $e->getMessage());
            throw new Exception("Error al obtener participantes: " . $e->getMessage());
        }
    }

        // Obtener participante por ID
        public function obtenerPorId($id) {
            try {
                $sql = "SELECT 
                            p.id_participante, 
                            p.primer_nombre, 
                            p.segundo_nombre, 
                            p.primer_apellido, 
                            p.segundo_apellido, 
                            p.cedula, 
                            p.edad, 
                            p.correo, 
                            t.numero_de_telefono AS telefono, 
                            DATE_FORMAT(p.fecha_nacimiento, '%Y-%m-%d') as fecha_nacimiento, 
                            DATE_FORMAT(p.fecha_registro, '%d/%m/%Y') as fecha_registro, 
                            p.grado_institucion, 
                            p.genero, 
                            p.estado,
                            p.id_discapacidad
                        FROM tb_participante p
                        LEFT JOIN tb_telefono t ON p.id_participante = t.id_participante AND t.tipo_de_telefono = 'Móvil'
                        WHERE p.id_participante = :id";
                        
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                
                return $result ? $result : null;
                
            } catch (PDOException $e) {
                error_log("Error en obtenerPorId: " . $e->getMessage());
                return null;
            }
        }

        public function obtenerDiscapacidades($id_participante) {
            try {
                $sql = "SELECT id_discapacidad FROM tb_participante_discapacidad WHERE id_participante = :id_participante";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([':id_participante' => $id_participante]);
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                error_log("Error en obtenerDiscapacidades: " . $e->getMessage());
                return [];
            }
        }


        public function obtenerCursosDisponibles() {
            $sql = "SELECT 
                        id_curso, 
                        CASE 
                            WHEN id_curso = 1 THEN 'No asignado' 
                            ELSE CONCAT(
                                nombre_curso, 
                                ' (', 
                                turno, 
                                ') - ', 
                                num_inscritos, 
                                '/', 
                                max_participantes
                            )
                        END AS display
                    FROM tb_curso
                    WHERE 
                        num_inscritos < max_participantes
                        AND id_estatusCurso = 1
                    ORDER BY 
                        FIELD(turno, 'Mañana', 'Tarde'), 
                        nombre_curso";
        
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en obtenerCursosDisponibles: " . $e->getMessage());
                return [];
            }
        }
        

        public function obtenerCursosParticipante($idParticipante) {
            try {
                // Obtener cursos activos
                $sqlActivos = "SELECT 
                                    c.id_curso,
                                    c.nombre_curso, 
                                    DATE_FORMAT(pc.fecha_inscripcion, '%d/%m/%Y') as fecha_inicio,
                                    pc.estatus_participante as estado,
                                    c.id_estatusCurso as id_estado_curso
                               FROM tb_participante_curso pc
                               JOIN tb_curso c ON pc.id_curso = c.id_curso
                               WHERE pc.id_participante = :id
                               AND pc.estatus_participante IN ('Asignado', 'En formación')";
        
                $stmtActivos = $this->conn->prepare($sqlActivos);
                $stmtActivos->bindParam(':id', $idParticipante, PDO::PARAM_INT);
                $stmtActivos->execute();
                $cursosActivos = $stmtActivos->fetchAll(PDO::FETCH_ASSOC);
        
                // Obtener historial académico
                $sqlHistorial = "SELECT 
                                      c.id_curso,
                                      c.nombre_curso, 
                                      DATE_FORMAT(pc.fecha_inscripcion, '%d/%m/%Y') as fecha_inicio,
                                      pc.estatus_participante as estado
                                  FROM tb_participante_curso pc
                                  JOIN tb_curso c ON pc.id_curso = c.id_curso
                                  WHERE pc.id_participante = :id
                                  AND pc.estatus_participante IN ('Aprobado', 'Reprobado')";
        
                $stmtHistorial = $this->conn->prepare($sqlHistorial);
                $stmtHistorial->bindParam(':id', $idParticipante, PDO::PARAM_INT);
                $stmtHistorial->execute();
                $historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
        
                return [
                    'success' => true,
                    'cursosActivos' => $cursosActivos,
                    'historial' => $historial
                ];
        
            } catch (PDOException $e) {
                return [
                    'success' => false,
                    'message' => 'Error al obtener cursos: ' . $e->getMessage()
                ];
            }
        }


        public function filtrarParticipantes($filtros) {
            try {
                $sql = "SELECT id_participante, CONCAT(primer_nombre, IFNULL(CONCAT(' ', segundo_nombre), '')) AS nombre, CONCAT(primer_apellido, IFNULL(CONCAT(' ', segundo_apellido), '')) AS apellido, cedula, edad, correo, (SELECT numero_de_telefono FROM tb_telefono WHERE id_participante = tb_participante.id_participante AND tipo_de_telefono = 'Móvil' LIMIT 1) AS telefono, DATE_FORMAT(fecha_registro, '%d/%m/%Y') as fecha_registro, grado_institucion, genero, estado FROM tb_participante WHERE 1 = 1";
                $params = [];
    
                // Filtro Sexo
                if (!empty($filtros['sexo']) && in_array($filtros['sexo'], ['Masculino', 'Femenino'])) {
                    $sql .= " AND genero = :sexo";
                    $params[':sexo'] = $filtros['sexo'];
                }
    
                // Filtro Edad
                if (!empty($filtros['edad']) && preg_match('/^(\d+)-(\d+)$/', $filtros['edad'], $matches)) {
                    $sql .= " AND edad BETWEEN :edadMin AND :edadMax";
                    $params[':edadMin'] = (int)$matches[1];
                    $params[':edadMax'] = (int)$matches[2];
                }
    
                // Filtro de Búsqueda General
                if (!empty($filtros['search'])) {
                    $search = '%' . $filtros['search'] . '%';
                    $sql .= " AND (
                        CONCAT(primer_nombre, ' ', IFNULL(segundo_nombre, ''), ' ', primer_apellido, ' ', IFNULL(segundo_apellido, '')) LIKE :search
                        OR cedula LIKE :search
                        OR correo LIKE :search
                    )";
                    $params[':search'] = $search;
                }

                // Filtro Estado
                if (!empty($filtros['estado']) && in_array($filtros['estado'], ['En formación', 'Disponible', 'En sistema', 'Asignado', 'Diferido'])) {
                    $sql .= " AND estado = :estado";
                    $params[':estado'] = $filtros['estado'];
                }

                $sql .= " ORDER BY id_participante DESC";
    
                $stmt = $this->conn->prepare($sql);
                
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            } catch (PDOException $e) {
                throw new Exception("Error de base de datos: " . $e->getMessage());
            }
        }

}
?>

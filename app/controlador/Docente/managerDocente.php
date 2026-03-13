<?php
// Conexion a la base de datos
require_once __DIR__ . '/../../modelo/conexion.php';

class docente {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }

    public function getConexion() {
        return $this->conn;
    }

    public function verificarCedula($cedula) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tb_docente WHERE cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetchColumn() > 0;
    }

    public function verificarCedulaEditar($cedula, $idDocente) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) 
            FROM tb_docente 
            WHERE cedula = ? 
            AND id_docente != ?"
        );
        $stmt->execute([$cedula, $idDocente]);
        return $stmt->fetchColumn() > 0;
    }

    public function verificarCedulaEdicion($cedula, $idDocente) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as count 
                FROM tb_docente 
                WHERE cedula = :cedula 
                AND id_docente != :idDocente"
            );
            $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
            $stmt->bindParam(':idDocente', $idDocente, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarCedulaEdicion: " . $e->getMessage());
            return false;
        }
    }
    
    public function registrar($data) {
        try {
            // Depuración
            error_log("Registrando docente con datos: " . print_r($data, true));
            
            // Validar campos obligatorios
            if (empty($data['primer_nombre']) || empty($data['primer_apellido']) || empty($data['cedula'])) {
                return [
                    "success" => false,
                    "message" => "Los campos nombre, apellido y cédula son obligatorios"
                ];
            }

            // Calcular la edad
            $fechaNacimiento = new DateTime($data['fechaNacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;

            $sql = "INSERT INTO tb_docente 
                    (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
                     cedula, correo, fecha_nacimiento, edad, estado_docente, genero, tipo_docente) 
                    VALUES (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, 
                            :cedula, :correo, :fecha_nacimiento, :edad, :estado_docente, :genero, :tipo_docente)";
            
            error_log("SQL: " . $sql);
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':primer_nombre' => $data['primer_nombre'],
                ':segundo_nombre' => $data['segundo_nombre'] ?? null,
                ':primer_apellido' => $data['primer_apellido'],
                ':segundo_apellido' => $data['segundo_apellido'] ?? null,
                ':cedula' => $data['cedula'],
                ':correo' => $data['correo'],
                ':fecha_nacimiento' => $data['fechaNacimiento'],
                ':edad' => $edad,
                ':estado_docente' => 'Disponible',
                ':genero' => $data['genero'],
                ':tipo_docente' => $data['tipo_docente'] ?? 'Contratado'
            ]);

            $id_docente = $this->conn->lastInsertId();
            
            error_log("Docente registrado con ID: " . $id_docente);

            // Insertar teléfono si se proporciona
            if (!empty($data['telefono'])) {
                $sqlTelefono = "INSERT INTO tb_telefono (numero_de_telefono, tipo_de_telefono, id_docente) 
                                VALUES (:telefono, 'Móvil', :id_docente)";
                $stmtTelefono = $this->conn->prepare($sqlTelefono);
                $stmtTelefono->execute([
                    ':telefono' => $data['telefono'],
                    ':id_docente' => $id_docente
                ]);
            }

            return [
                "success" => true,
                "message" => "Docente registrado con éxito"
            ];
        } catch (PDOException $e) {
            error_log("Error PDO al registrar docente: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Error al registrar Docente: " . $e->getMessage()
            ];
        }
    }

    public function existeCedula($cedula) {
        $sql = "SELECT COUNT(*) as total FROM tb_docente WHERE cedula = :cedula";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Cambiar estado del docente (Activar/Inactivar) - VERSIÓN MEJORADA CON VALIDACIÓN DE CURSOS
    public function cambiarEstado($id_docente, $nuevo_estado) {
        try {
            // Validar que el nuevo estado sea válido
            $estados_validos = ['Disponible', 'En formación', 'Asignado', 'Inactivo'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                return [
                    'success' => false,
                    'message' => 'Estado no válido'
                ];
            }

            // Verificar si el docente existe
            $sql_verificar = "SELECT COUNT(*) FROM tb_docente WHERE id_docente = :id_docente";
            $stmt_verificar = $this->conn->prepare($sql_verificar);
            $stmt_verificar->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            $stmt_verificar->execute();
            
            if ($stmt_verificar->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ];
            }

            // VALIDACIÓN: Verificar si el docente tiene cursos asignados cuando se quiere inactivar
            if ($nuevo_estado === 'Inactivo') {
                // Consulta para verificar si el docente tiene cursos asignados
                $sql_asignaciones = "
                    SELECT COUNT(dc.id_docente_curso) as total_asignaciones
                    FROM tb_docente_curso dc
                    WHERE dc.id_docente = :id_docente
                ";
                
                $stmt_asignaciones = $this->conn->prepare($sql_asignaciones);
                $stmt_asignaciones->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                $stmt_asignaciones->execute();
                
                $resultado = $stmt_asignaciones->fetch(PDO::FETCH_ASSOC);
                
                if ($resultado['total_asignaciones'] > 0) {
                    // Obtener detalles de los cursos para el mensaje
                    $sql_cursos = "
                        SELECT c.nombre_curso, c.codigo_curso
                        FROM tb_docente_curso dc
                        INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                        WHERE dc.id_docente = :id_docente
                        LIMIT 5
                    ";
                    
                    $stmt_cursos = $this->conn->prepare($sql_cursos);
                    $stmt_cursos->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                    $stmt_cursos->execute();
                    $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Construir mensaje de error detallado
                    $mensaje = "No se puede inactivar el docente porque tiene " . 
                               $resultado['total_asignaciones'] . 
                               " curso(s) asignado(s) actualmente.";
                    
                    if (!empty($cursos)) {
                        $nombresCursos = array_map(function($curso) {
                            return $curso['nombre_curso'];
                        }, $cursos);
                        
                        $mensaje .= " Cursos asignados: " . implode(', ', $nombresCursos);
                        
                        if ($resultado['total_asignaciones'] > 5) {
                            $mensaje .= " y " . ($resultado['total_asignaciones'] - 5) . " más.";
                        }
                    }
                    
                    $mensaje .= ". Primero debe reasignar los cursos a otro docente.";
                    
                    return [
                        'success' => false,
                        'message' => $mensaje,
                        'tiene_asignaciones' => true,
                        'total_asignaciones' => $resultado['total_asignaciones'],
                        'cursos' => $cursos
                    ];
                }
            }

            // Obtener estado actual para mensaje informativo
            $estado_actual = $this->obtenerEstadoDocente($id_docente);

            // Actualizar el estado
            $sql = "UPDATE tb_docente SET estado_docente = :nuevo_estado WHERE id_docente = :id_docente";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nuevo_estado', $nuevo_estado);
            $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $accion = $nuevo_estado === 'Inactivo' ? 'inactivado' : 'activado';
                return [
                    'success' => true,
                    'message' => 'Docente ' . $accion . ' correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al cambiar el estado'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    // Método para editar un docente
    public function editar($data) {
        try {
            // Calcular la edad
            $fechaNacimiento = new DateTime($data['fechaNacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;

            // Actualizar docente
            $sql = "UPDATE tb_docente 
                    SET primer_nombre = :primer_nombre, 
                        segundo_nombre = :segundo_nombre,
                        primer_apellido = :primer_apellido, 
                        segundo_apellido = :segundo_apellido,
                        cedula = :cedula, 
                        correo = :correo, 
                        fecha_nacimiento = :fecha_nacimiento, 
                        edad = :edad, 
                        estado_docente = :estado_docente, 
                        genero = :genero,
                        tipo_docente = :tipo_docente
                    WHERE id_docente = :id_docente";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_docente' => $data['id_docente'],
                ':primer_nombre' => $data['primer_nombre'],
                ':segundo_nombre' => $data['segundo_nombre'] ?? null,
                ':primer_apellido' => $data['primer_apellido'],
                ':segundo_apellido' => $data['segundo_apellido'] ?? null,
                ':cedula' => $data['cedula'],
                ':correo' => $data['correo'],
                ':fecha_nacimiento' => $data['fechaNacimiento'],
                ':edad' => $edad,
                ':estado_docente' => $data['estado_docente'],
                ':genero' => $data['genero'],
                ':tipo_docente' => $data['tipo_docente']
            ]);

            // Actualizar teléfono
            // Primero eliminar teléfonos existentes
            $sqlDeleteTelefono = "DELETE FROM tb_telefono WHERE id_docente = :id_docente";
            $stmtDelete = $this->conn->prepare($sqlDeleteTelefono);
            $stmtDelete->execute([':id_docente' => $data['id_docente']]);

            // Insertar nuevo teléfono si se proporciona
            if (!empty($data['telefono'])) {
                $sqlTelefono = "INSERT INTO tb_telefono (numero_de_telefono, tipo_de_telefono, id_docente) 
                                VALUES (:telefono, 'Móvil', :id_docente)";
                $stmtTelefono = $this->conn->prepare($sqlTelefono);
                $stmtTelefono->execute([
                    ':telefono' => $data['telefono'],
                    ':id_docente' => $data['id_docente']
                ]);
            }

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Datos actualizados correctamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se actualizaron los datos.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar los datos: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerDocentes() {
        try {
            $sql = "SELECT d.id_docente, 
                           d.primer_nombre, 
                           d.segundo_nombre, 
                           d.primer_apellido, 
                           d.segundo_apellido, 
                           d.cedula, 
                           d.correo, 
                           d.fecha_nacimiento, 
                           d.edad, 
                           d.estado_docente, 
                           d.genero,
                           d.tipo_docente,
                           t.numero_de_telefono as telefono
                    FROM tb_docente d
                    LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
                    GROUP BY d.id_docente
                    ORDER BY d.id_docente DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error al obtener docentes: ' . $e->getMessage());
        }
    }

    public function obtenerEstadoDocente($id_docente) {
        try {
            $sql = "SELECT estado_docente FROM tb_docente WHERE id_docente = :id_docente";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_docente' => $id_docente]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['estado_docente'] ?? 'Disponible';
        } catch (PDOException $e) {
            throw new Exception('Error al obtener el estado actual del docente: ' . $e->getMessage());
        }
    }

    // Método para obtener los datos de un docente por ID
    public function obtenerDocentePorId($id) {
        try {
            $sql = "SELECT d.id_docente, 
                           d.primer_nombre, 
                           d.segundo_nombre, 
                           d.primer_apellido, 
                           d.segundo_apellido, 
                           d.cedula, 
                           d.correo, 
                           d.fecha_nacimiento, 
                           d.edad, 
                           d.estado_docente, 
                           d.genero,
                           d.tipo_docente,
                           t.numero_de_telefono as telefono
                    FROM tb_docente d
                    LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
                    WHERE d.id_docente = :id
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $docente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($docente) {
                // Asegurar que todos los campos existan
                $docente['segundo_nombre'] = $docente['segundo_nombre'] ?? '';
                $docente['segundo_apellido'] = $docente['segundo_apellido'] ?? '';
                $docente['telefono'] = $docente['telefono'] ?? '';
                
                return [
                    'success' => true,
                    'data' => $docente
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en obtenerDocentePorId: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener un docente por ID (para ver detalles)
    public function verDocente($id) {
        try {
            // Obtener datos básicos del docente
            $sql = "SELECT d.*, t.numero_de_telefono as telefono
                    FROM tb_docente d
                    LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente
                    WHERE d.id_docente = :id
                    LIMIT 1";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $docente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($docente) {
                // Obtener cursos asociados
                $sqlCursos = "
                    SELECT 
                        c.id_curso,
                        c.nombre_curso, 
                        ec.nombre_estatusCurso AS estado,
                        dc.fecha_asignacion
                    FROM tb_docente_curso dc
                    INNER JOIN tb_curso c ON dc.id_curso = c.id_curso
                    LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                    WHERE dc.id_docente = :id
                ";

                $stmtCursos = $this->conn->prepare($sqlCursos);
                $stmtCursos->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtCursos->execute();
                $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'success' => true,
                    'data' => [
                        'primer_nombre' => $docente['primer_nombre'],
                        'segundo_nombre' => $docente['segundo_nombre'],
                        'primer_apellido' => $docente['primer_apellido'],
                        'segundo_apellido' => $docente['segundo_apellido'],
                        'cedula' => $docente['cedula'],
                        'telefono' => $docente['telefono'],
                        'edad' => $docente['edad'],
                        'fecha_nacimiento' => $docente['fecha_nacimiento'],
                        'genero' => $docente['genero'],
                        'estado_docente' => $docente['estado_docente'],
                        'tipo_docente' => $docente['tipo_docente'],
                        'correo' => $docente['correo'],
                        'cursos' => $cursos
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ];
        }
    }

    // Método para eliminar un docente
    public function eliminar($id_docente) {
        try {
            // Verificar si el docente tiene cursos asignados
            $sqlVerificar = "SELECT COUNT(*) FROM tb_docente_curso WHERE id_docente = :id_docente";
            $stmtVerificar = $this->conn->prepare($sqlVerificar);
            $stmtVerificar->execute([':id_docente' => $id_docente]);
            $tieneCursos = $stmtVerificar->fetchColumn() > 0;

            if ($tieneCursos) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el docente porque tiene cursos asignados'
                ];
            }

            // Eliminar teléfonos asociados
            $sqlEliminarTelefonos = "DELETE FROM tb_telefono WHERE id_docente = :id_docente";
            $stmtTelefonos = $this->conn->prepare($sqlEliminarTelefonos);
            $stmtTelefonos->execute([':id_docente' => $id_docente]);

            // Eliminar docente
            $sql = "DELETE FROM tb_docente WHERE id_docente = :id_docente";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_docente' => $id_docente]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Docente eliminado correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se encontró el docente'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar el docente: ' . $e->getMessage()
            ];
        }
    }
}
?>
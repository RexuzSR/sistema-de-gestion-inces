<?php
// Incluir la conexión a la base de datos
require_once '../../modelo/conexion.php';

class Usuario {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->getConexion();
    }


// Método para iniciar sesión
public function iniciarSesion($nombre_usuario, $contraseña) {
    try {
        // CONSULTA CORREGIDA - Usando los nombres correctos de campos de la tabla
        $sql = "SELECT id_usuario, nombre_usuario, password, rol, estado_usuario, 
                       primer_nombre, primer_apellido, imagen 
                FROM tb_usuario 
                WHERE nombre_usuario = :nombre_usuario";
        
        error_log("Consulta SQL: " . $sql);
        error_log("Buscando usuario: " . $nombre_usuario);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            error_log("Usuario encontrado en BD: " . print_r($usuario, true));
            
            // Verificar si el usuario está activo
            if ($usuario['estado_usuario'] !== 'Activo') {
                error_log("Usuario inactivo: " . $usuario['estado_usuario']);
                return "Usuario inactivo. Contacte al administrador.";
            }

            // Verificar la contraseña
            error_log("Contraseña recibida: '$contraseña'");
            error_log("Hash en BD: " . $usuario['password']);
            
            if (password_verify($contraseña, $usuario['password'])) {
                error_log("Contraseña VERIFICADA correctamente");
                
                // Iniciar sesión - Usando los nombres correctos de campos
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                $_SESSION['rol'] = $usuario['rol'];
                // Guardar nombres separados y combinados para compatibilidad
                $_SESSION['primer_nombre'] = $usuario['primer_nombre'] ?? '';
                $_SESSION['segundo_nombre'] = $usuario['segundo_nombre'] ?? '';
                $_SESSION['primer_apellido'] = $usuario['primer_apellido'] ?? '';
                $_SESSION['segundo_apellido'] = $usuario['segundo_apellido'] ?? '';
                $_SESSION['nombre'] = trim(($_SESSION['primer_nombre'] ?? '') . ' ' . ($_SESSION['segundo_nombre'] ?? ''));
                $_SESSION['apellido'] = trim(($_SESSION['primer_apellido'] ?? '') . ' ' . ($_SESSION['segundo_apellido'] ?? ''));
                $_SESSION['imagen'] = $usuario['imagen'];
                $_SESSION['logged_in'] = true;
                
                error_log("Sesión establecida - ID Usuario: " . $_SESSION['id_usuario']);
                
                // Actualizar estado de los cursos (puede comentarse temporalmente para pruebas)
                $this->actualizarEstadoCursos();
                error_log("Estado de cursos actualizado");

                return true;
            } else {
                error_log("CONTRASEÑA INCORRECTA - No coincide el hash");
                return "Credenciales incorrectas";
            }
        } else {
            error_log("Usuario NO encontrado en BD: " . $nombre_usuario);
            return "Usuario no encontrado";
        }
    } catch (PDOException $e) {
        error_log("ERROR PDO en iniciarSesion: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        return "Error en el sistema. Intente más tarde.";
    } catch (Exception $e) {
        error_log("ERROR General en iniciarSesion: " . $e->getMessage());
        return "Error en el sistema. Intente más tarde.";
    }
}


public function actualizarEstadoCursos() {
    try {
        // Obtener la fecha actual
        $fechaActual = date("Y-m-d");

        // Obtener los cursos que han comenzado o finalizado
        $sql = "SELECT c.id_curso, c.fecha_inicio, c.fecha_fin, c.num_inscritos, c.min_participantes, c.id_estatusCurso, dc.id_docente 
                FROM tb_curso c
                LEFT JOIN tb_docente_curso dc ON c.id_curso = dc.id_curso
                WHERE (c.fecha_inicio <= :fechaActual AND c.id_estatusCurso NOT IN (3,5)) 
                   OR (c.fecha_fin <= :fechaActual AND c.id_estatusCurso NOT IN (3,5))";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fechaActual', $fechaActual);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($curso = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $idCurso = $curso['id_curso'];
                $idDocente = $curso['id_docente'];

                // Verificar si el curso ha finalizado
                if ($curso['fecha_fin'] <= $fechaActual) {
                    // 5 = Culminado
                    $updateCurso = "UPDATE tb_curso SET id_estatusCurso = 5 WHERE id_curso = :id_curso";
                    $stmtUpdateCurso = $this->conn->prepare($updateCurso);
                    $stmtUpdateCurso->bindParam(':id_curso', $idCurso);
                    $stmtUpdateCurso->execute();

                    // Cambiar el estado de los participantes en el curso a "Asignado" si corresponde
                    $updateParticipantesPendientes = "UPDATE tb_participante_curso 
                                                      SET estatus_participante = 'Asignado' 
                                                      WHERE id_curso = :id_curso 
                                                        AND estatus_participante IN ('En formación')";
                    $stmtUpdateParticipantesPendientes = $this->conn->prepare($updateParticipantesPendientes);
                    $stmtUpdateParticipantesPendientes->bindParam(':id_curso', $idCurso);
                    $stmtUpdateParticipantesPendientes->execute();
                } 
                // Verificar si el curso ha comenzado
                elseif ($curso['fecha_inicio'] <= $fechaActual) {
                    // Si cumple mínimo: 3 = En proceso; Si no cumple: 2 = Diferido
                    $nuevoEstado = ($curso['num_inscritos'] >= $curso['min_participantes']) ? 3 : 2;
                    $updateCurso = "UPDATE tb_curso SET id_estatusCurso = :estado WHERE id_curso = :id_curso";
                    $stmtUpdateCurso = $this->conn->prepare($updateCurso);
                    $stmtUpdateCurso->bindParam(':estado', $nuevoEstado, PDO::PARAM_INT);
                    $stmtUpdateCurso->bindParam(':id_curso', $idCurso);
                    $stmtUpdateCurso->execute();

                    // Cambiar el estado de los participantes según si es "En proceso" o "Diferido"
                    if ($nuevoEstado == 3) {
                        $updateParticipantesCurso = "UPDATE tb_participante_curso 
                                                     SET estatus_participante = 'En formación' 
                                                     WHERE id_curso = :id_curso AND estatus_participante = 'Asignado'";
                    } else {
                        $updateParticipantesCurso = "UPDATE tb_participante_curso 
                                                     SET estatus_participante = 'Diferido' 
                                                     WHERE id_curso = :id_curso AND estatus_participante = 'Asignado'";
                    }
                    $stmtUpdateParticipantesCurso = $this->conn->prepare($updateParticipantesCurso);
                    $stmtUpdateParticipantesCurso->bindParam(':id_curso', $idCurso);
                    $stmtUpdateParticipantesCurso->execute();
                }

                // Actualizar el estado de los docentes asociados al curso
                if (!empty($idDocente)) {
                    $sqlEstadoDocente = "SELECT COUNT(*) AS cursos_en_formacion 
                                         FROM tb_curso c
                                         INNER JOIN tb_docente_curso dc ON c.id_curso = dc.id_curso
                                         WHERE dc.id_docente = :id_docente AND c.id_estatusCurso = 3";
                    $stmtEstadoDocente = $this->conn->prepare($sqlEstadoDocente);
                    $stmtEstadoDocente->bindParam(':id_docente', $idDocente);
                    $stmtEstadoDocente->execute();
                    $resultado = $stmtEstadoDocente->fetch(PDO::FETCH_ASSOC);

                    // Determinar el estado del docente
                    $estadoDocente = $resultado['cursos_en_formacion'] > 0 ? 'En formación' : 'Asignado';
                    $updateDocente = "UPDATE tb_docente SET estado_docente = :estado_docente WHERE id_docente = :id_docente";
                    $stmtUpdateDocente = $this->conn->prepare($updateDocente);
                    $stmtUpdateDocente->bindParam(':estado_docente', $estadoDocente);
                    $stmtUpdateDocente->bindParam(':id_docente', $idDocente);
                    $stmtUpdateDocente->execute();
                }

                // Actualizar el estado de los participantes vinculados al curso
                $sqlParticipantes = "SELECT DISTINCT id_participante 
                                     FROM tb_participante_curso 
                                     WHERE id_curso = :id_curso";
                $stmtParticipantes = $this->conn->prepare($sqlParticipantes);
                $stmtParticipantes->bindParam(':id_curso', $idCurso);
                $stmtParticipantes->execute();

                while ($participante = $stmtParticipantes->fetch(PDO::FETCH_ASSOC)) {
                    $idParticipante = $participante['id_participante'];

                    // Verificar los estados de los cursos del participante
                    $sqlEstadoCursos = "SELECT estatus_participante 
                                        FROM tb_participante_curso 
                                        WHERE id_participante = :id_participante";
                    $stmtEstadoCursos = $this->conn->prepare($sqlEstadoCursos);
                    $stmtEstadoCursos->bindParam(':id_participante', $idParticipante);
                    $stmtEstadoCursos->execute();

                    $tieneEnFormacion = false;
                    $tieneAsignado = false;
                    $tieneDiferido = false;
                    $tieneHistorial = false;

                    while ($estadoCurso = $stmtEstadoCursos->fetch(PDO::FETCH_ASSOC)) {
                        if ($estadoCurso['estatus_participante'] === 'En formación') {
                            $tieneEnFormacion = true;
                        } elseif ($estadoCurso['estatus_participante'] === 'Asignado') {
                            $tieneAsignado = true;
                        } elseif ($estadoCurso['estatus_participante'] === 'Diferido') {
                            $tieneDiferido = true;
                        } else {
                            $tieneHistorial = true;
                        }
                    }

                    // Determinar el estado del participante
                    $nuevoEstadoParticipante = 'En sistema'; // Valor predeterminado
                    if ($tieneEnFormacion) {
                        $nuevoEstadoParticipante = 'En formación';
                    } elseif ($tieneAsignado) {
                        $nuevoEstadoParticipante = 'Asignado';
                    } elseif ($tieneDiferido) {
                        $nuevoEstadoParticipante = 'Diferido';
                    } elseif ($tieneHistorial) {
                        $nuevoEstadoParticipante = 'Disponible';
                    }

                    // Actualizar el estado del participante en la tabla `tb_participante`
                    $updateEstadoParticipante = "UPDATE tb_participante 
                                                 SET estado = :nuevo_estado 
                                                 WHERE id_participante = :id_participante";
                    $stmtUpdateEstadoParticipante = $this->conn->prepare($updateEstadoParticipante);
                    $stmtUpdateEstadoParticipante->bindParam(':nuevo_estado', $nuevoEstadoParticipante);
                    $stmtUpdateEstadoParticipante->bindParam(':id_participante', $idParticipante);
                    $stmtUpdateEstadoParticipante->execute();
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Error al actualizar el estado de los cursos, docentes y participantes: " . $e->getMessage());
    }
}




    public function verificarUsuario($username = null, $correo = null) {
        $selects = [];
        $params = [];
    
        // Verificar nombre de usuario
        if ($username !== null) {
            $selects[] = "SUM(CASE WHEN nombre_usuario = ? THEN 1 ELSE 0 END) as username_count";
            $params[] = $username;
        } else {
            $selects[] = "0 as username_count";
        }
    
        // Verificar correo
        if ($correo !== null) {
            $selects[] = "SUM(CASE WHEN correo = ? THEN 1 ELSE 0 END) as correo_count";
            $params[] = $correo;
        } else {
            $selects[] = "0 as correo_count";
        }
    
        $sql = "SELECT " . implode(', ', $selects) . " FROM tb_usuario";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return [
            'existsUsername' => $result['username_count'] > 0,
            'existsCorreo' => $result['correo_count'] > 0
        ];
    }

    public function verificarUsuarioEdit($nombre_usuario, $id_usuario) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM tb_usuario 
                WHERE nombre_usuario = :nombre_usuario 
                AND id_usuario != :id_usuario
            ");
            
            $stmt->execute([
                ':nombre_usuario' => $nombre_usuario,
                ':id_usuario' => $id_usuario
            ]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en VerificarNombreUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function verificarCorreoEdit($correo, $id_usuario) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM tb_usuario 
                WHERE correo = :correo 
                AND id_usuario != :id_usuario
            ");
            
            $stmt->execute([
                ':correo' => $correo,
                ':id_usuario' => $id_usuario
            ]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en VerificarCorreo: " . $e->getMessage());
            return false;
        }
    }


// Método para registrar un usuario
public function registrarUsuario($data) {
    try {
        // Verificar si el nombre de usuario ya existe
        $stmt_check = $this->conn->prepare("SELECT COUNT(*) FROM tb_usuario WHERE nombre_usuario = :nombre_usuario");
        $stmt_check->execute([':nombre_usuario' => $data['nombre_usuario']]);
        if ($stmt_check->fetchColumn() > 0) {
            return ["success" => false, "message" => "El nombre de usuario ya está en uso."];
        }

        // Verificar si el correo ya está registrado
        $stmt_check = $this->conn->prepare("SELECT COUNT(*) FROM tb_usuario WHERE correo = :correo");
        $stmt_check->execute([':correo' => $data['correo']]);
        if ($stmt_check->fetchColumn() > 0) {
            return ["success" => false, "message" => "El correo ya está en uso."];
        }

        // Imagen por defecto
        $imagen = 'assets/img/avatar/default-user.png';

        // Validación y subida de imagen si se proporcionó
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $file = $_FILES["imagen"];
            $nombreImagen = time() . "_" . basename($file["name"]);
            $tipoImagen = mime_content_type($file['tmp_name']);
            $rutaTemporal = $file["tmp_name"];
            $size = $file["size"];
            $carpeta = __DIR__ . "/../../../assets/img/avatar/";

            // Verifica tipos válidos y tamaño
            $tiposValidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($tipoImagen, $tiposValidos)) {
                return ["success" => false, "message" => "Solo se permiten imágenes JPG, PNG o GIF."];
            }

            if ($size > 2 * 1024 * 1024) {
                return ["success" => false, "message" => "La imagen no debe superar los 2MB."];
            }

            // Crear carpeta si no existe
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            // Mover archivo
            $rutaDestino = $carpeta . $nombreImagen;
            if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                $imagen = "assets/img/avatar/" . $nombreImagen;
            } else {
                error_log("No se pudo mover el archivo a la carpeta destino.");
            }
        }

        // Hashear la contraseña
        $password_hashed = password_hash($data['password'], PASSWORD_BCRYPT);

        // Preparar valores para nombres/apellidos (segundos pueden quedar vacíos)
        $primer_nombre = $data['primer_nombre'] ?? ($data['nombre'] ?? '');
        $segundo_nombre = $data['segundo_nombre'] ?? '';
        $primer_apellido = $data['primer_apellido'] ?? ($data['apellido'] ?? '');
        $segundo_apellido = $data['segundo_apellido'] ?? '';

        // Insertar usuario con nuevos campos de nombre/apellidos
        $sql = "INSERT INTO tb_usuario 
                (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, nombre_usuario, correo, password, rol, imagen, estado_usuario) 
                VALUES 
                (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :nombre_usuario, :correo, :password, :rol, :imagen, :estado_usuario)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':primer_nombre' => $primer_nombre,
            ':segundo_nombre' => $segundo_nombre,
            ':primer_apellido' => $primer_apellido,
            ':segundo_apellido' => $segundo_apellido,
            ':nombre_usuario' => $data['nombre_usuario'],
            ':correo' => $data['correo'],
            ':password' => $password_hashed,
            ':rol' => $data['rol'],
            ':imagen' => $imagen,
            ':estado_usuario' => $data['estado_usuario'] // "Activo" o "Inactivo"
        ]);

        $lastId = $this->conn->lastInsertId();

        return ["success" => true, "message" => "✅ Usuario registrado con éxito.", "id_usuario" => $lastId];

    } catch (PDOException $e) {
        return ["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()];
    }
}


    public function obtenerUsuarios() {
        try {
            // Consulta directamente a tb_usuario con estado_usuario incluido
            $sql = "SELECT id_usuario, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, nombre_usuario, correo, rol, imagen, estado_usuario
                    FROM tb_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agregar campos compatibles `nombre` y `apellido` combinados para compatibilidad con front-end
            foreach ($usuarios as &$u) {
                $u['nombre'] = trim(($u['primer_nombre'] ?? '') . ' ' . ($u['segundo_nombre'] ?? ''));
                $u['apellido'] = trim(($u['primer_apellido'] ?? '') . ' ' . ($u['segundo_apellido'] ?? ''));
            }


            return $usuarios;
        } catch (PDOException $e) {

            return ['error' => true, 'message' => 'Error al obtener los usuarios: ' . $e->getMessage()];
        }
    }

    public function editar($data) {
        try {
            // Preparar la consulta base
            $sql = "UPDATE tb_usuario 
                    SET primer_nombre = :primer_nombre, 
                        segundo_nombre = :segundo_nombre, 
                        primer_apellido = :primer_apellido, 
                        segundo_apellido = :segundo_apellido, 
                        nombre_usuario = :nombre_usuario, 
                        rol = :rol, 
                        estado_usuario = :estado_usuario, 
                        correo = :correo, 
                        imagen = :imagen";
            
            // Agregar password solo si se proporciona
            if (!empty($data['password'])) {
                $sql .= ", password = :password";
                $password_hashed = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            $sql .= " WHERE id_usuario = :id_usuario";
            
            // Preparar la sentencia
            $stmt = $this->conn->prepare($sql);
            
            // Preparar nombres/apellidos (compatibilidad con campos antiguos)
            $primer_nombre = $data['primer_nombre'] ?? ($data['nombre'] ?? '');
            $segundo_nombre = $data['segundo_nombre'] ?? '';
            $primer_apellido = $data['primer_apellido'] ?? ($data['apellido'] ?? '');
            $segundo_apellido = $data['segundo_apellido'] ?? '';

            // Bind de parámetros comunes
            $stmt->bindParam(':id_usuario', $data['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':primer_nombre', $primer_nombre);
            $stmt->bindParam(':segundo_nombre', $segundo_nombre);
            $stmt->bindParam(':primer_apellido', $primer_apellido);
            $stmt->bindParam(':segundo_apellido', $segundo_apellido);
            $stmt->bindParam(':nombre_usuario', $data['nombre_usuario']);
            $stmt->bindParam(':rol', $data['rol']);
            $stmt->bindParam(':estado_usuario', $data['estado_usuario']);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':imagen', $data['imagen']);
            
            // Bind de password si existe
            if (!empty($data['password'])) {
                $stmt->bindParam(':password', $password_hashed);
            }
            
            // Ejecutar la consulta
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => '✅ Usuario actualizado correctamente.',
                'imagen' => $data['imagen'] // Devolver la ruta de la imagen para feedback
            ];
            
        } catch (PDOException $e) {
            error_log("Error en editar usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '❌ Error al actualizar el usuario: ' . $e->getMessage()
    ];
    }
    }
    
    

    public function obtenerUsuarioPorId($id) {
        $id = intval($id);  // Sanitizar el ID

        // Consulta SQL para obtener los datos del usuario con el ID proporcionado
        $sql = "SELECT p.* 
                FROM tb_usuario p
                WHERE p.id_usuario = :id";  // Asegúrate de que este es el campo correcto

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Agregar campos `nombre` y `apellido` combinados para compatibilidad
                $usuario['nombre'] = trim(($usuario['primer_nombre'] ?? '') . ' ' . ($usuario['segundo_nombre'] ?? ''));
                $usuario['apellido'] = trim(($usuario['primer_apellido'] ?? '') . ' ' . ($usuario['segundo_apellido'] ?? ''));
                return json_encode($usuario); // Enviar los datos en formato JSON
            } else {
                return json_encode(['error' => 'Usuario no encontrado']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }

    public function obtenerImagenActual($id_usuario) {
        $sql = "SELECT imagen FROM tb_usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchColumn(); // Devuelve solo el valor de la columna "imagen"
    }
    



    public function eliminar($id) {
        try {
            // Primero, verificar si el usuario a eliminar es administrador
            $sqlCheckRol = "SELECT rol FROM tb_usuario WHERE id_usuario = :id";
            $stmtCheck = $this->conn->prepare($sqlCheckRol);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $usuario = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
            if (!$usuario) {
                return [
                    "success" => false,
                    "message" => "Usuario no encontrado con ID: " . $id
                ];
            }
    
            if ($usuario['rol'] === 'Administrador') {
                // Contar cuántos administradores hay
                $sqlCount = "SELECT COUNT(*) AS total FROM tb_usuario WHERE rol = 'Administrador'";
                $stmtCount = $this->conn->query($sqlCount);
                $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
    
                if ($result['total'] <= 1) {
                    return [
                        "success" => false,
                        "message" => "No se puede eliminar el último usuario administrador."
                    ];
                }
            }

            // --- NUEVA VALIDACIÓN: Evitar auto-borrado ---
            if (isset($_SESSION['id_usuario']) && $id == $_SESSION['id_usuario']) {
                return [
                    "success" => false,
                    "message" => "No puedes eliminar tu propia cuenta mientras estés conectado."
                ];
            }
            // ---------------------------------------------
    
            // Eliminar el usuario si no es el último administrador
            $sql = "DELETE FROM tb_usuario WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                return [
                    "success" => true,
                    "message" => "Usuario eliminado con éxito"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se encontró el Usuario con ID: " . $id
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error en la consulta: " . $e->getMessage()
            ];
        }
    }
    
    


    public function verUsuario($id) {
        try {
            $sql = "SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, nombre_usuario, rol, imagen, estado_usuario, correo 
                    FROM tb_usuario 
                    WHERE id_usuario = :id";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($usuario) {
                // Combinar nombres y apellidos
                $nombre_completo = trim(($usuario['primer_nombre'] ?? '') . ' ' . ($usuario['segundo_nombre'] ?? ''));
                $apellido_completo = trim(($usuario['primer_apellido'] ?? '') . ' ' . ($usuario['segundo_apellido'] ?? ''));

                return [
                    'success' => true,
                    'data' => [
                        'nombre' => $nombre_completo,
                        'apellido' => $apellido_completo,
                        'nombre_usuario' => $usuario['nombre_usuario'],
                        'rol' => $usuario['rol'],
                        'imagen' => $usuario['imagen'],
                        'estado' => $usuario['estado_usuario'],
                        'correo' => $usuario['correo'],
                        // Para compatibilidad, incluir campos separados
                        'primer_nombre' => $usuario['primer_nombre'] ?? '',
                        'segundo_nombre' => $usuario['segundo_nombre'] ?? '',
                        'primer_apellido' => $usuario['primer_apellido'] ?? '',
                        'segundo_apellido' => $usuario['segundo_apellido'] ?? '',
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ];
        }
    }


    public function obtenerUsuariosPorFiltro($rol = null, $estado = null) {
        // Iniciar la consulta SQL con una condición base
        $sql = "SELECT p.*
                FROM tb_usuario p
                WHERE 1=1";  // Condición base para que siempre sea verdadera
    
        // Agregar condiciones según los filtros proporcionados
        if ($rol) {
            $sql .= " AND p.rol = :rol"; // Filtro por rol
        }
        if ($estado) {
            $sql .= " AND p.estado_usuario = :estado"; // Filtro por estado
        }
    
        // Preparar la consulta
        $stmt = $this->conn->prepare($sql);
    
        // Asignar los valores de los parámetros en caso de que existan
        if ($rol) {
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        }
        if ($estado) {
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR); // Para 'Activo' o 'Inactivo'
        }
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener los resultados y devolverlos
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function existeCorreo($correo) {
        try {
            $sql = "SELECT id_usuario FROM tb_usuario WHERE correo = :correo";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Error en existeCorreo: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizarPasswordPorCorreo($correo, $nueva_password) {
        try {
            $hashed_password = password_hash($nueva_password, PASSWORD_BCRYPT);
            
            $sql = "UPDATE tb_usuario SET password = :password WHERE correo = :correo";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo actualizar la contraseña'];
            }
        } catch (PDOException $e) {
            error_log("Error en actualizarPasswordPorCorreo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar la contraseña'];
        }
    }


    // MÉTODO PARA GENERAR CÓDIGO DE RECUPERACIÓN
public function generarCodigoRecuperacion($correo, $nombre_usuario) {
    try {
        // Verificar coincidencia entre correo y usuario
        $sql = "SELECT id_usuario FROM tb_usuario 
                WHERE correo = :correo AND nombre_usuario = :nombre_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => $correo, ':nombre_usuario' => $nombre_usuario]);
        
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Correo y usuario no coinciden'];
        }

        // Generar código de 5 dígitos
        $codigo = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Actualizar base de datos
        $sqlUpdate = "UPDATE tb_usuario SET 
                     codigo_recuperacion = :codigo, 
                     codigo_expiracion = :expiracion 
                     WHERE correo = :correo";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':codigo' => $codigo,
            ':expiracion' => $expiracion,
            ':correo' => $correo
        ]);
        
        // Enviar correo (Para desarrollo, muestra el código)
        // return ['success' => true, 'codigo' => $codigo]; // Descomentar en desarrollo
        
        // En producción, usar:
        $this->enviarCorreoRecuperacion($correo, $codigo);
        return ['success' => true];
        
    } catch (PDOException $e) {
        error_log("Error en generarCodigoRecuperacion: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al generar código'];
    }
}

// MÉTODO PARA ENVIAR CORREO
private function enviarCorreoRecuperacion($correo, $codigo) {
    $asunto = "Código de Recuperación - INCES";
    $mensaje = "Tu código es: $codigo\nVálido por 15 minutos.";
    $headers = "From: no-reply@inces.com" . "\r\n";
    
    if (!mail($correo, $asunto, $mensaje, $headers)) {
        error_log("Falló el envío a: $correo");
    }
}

// MÉTODO PARA VERIFICAR CÓDIGO
public function verificarCodigoRecuperacion($correo, $codigo) {
    try {
        $sql = "SELECT id_usuario FROM tb_usuario 
                WHERE correo = :correo 
                AND codigo_recuperacion = :codigo
                AND codigo_expiracion > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => $correo, ':codigo' => $codigo]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Error en verificarCodigoRecuperacion: " . $e->getMessage());
        return false;
    }
}

// MÉTODO PARA CAMBIAR CONTRASEÑA
public function cambiarPasswordConCodigo($correo, $codigo, $nueva_password) {
    try {
        if (!$this->verificarCodigoRecuperacion($correo, $codigo)) {
            return ['success' => false, 'message' => 'Código inválido o expirado'];
        }

        $hashed_password = password_hash($nueva_password, PASSWORD_BCRYPT);
        $sql = "UPDATE tb_usuario SET 
                password = :password,
                codigo_recuperacion = NULL,
                codigo_expiracion = NULL
                WHERE correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':password' => $hashed_password, ':correo' => $correo]);
        
        return ['success' => true, 'message' => 'Contraseña actualizada'];
        
    } catch (PDOException $e) {
        error_log("Error en cambiarPasswordConCodigo: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar'];
    }
}
    
}
?>

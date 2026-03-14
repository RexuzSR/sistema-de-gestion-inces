<?php
require_once 'managerUsuario.php';
require_once '../../modelo/HistorialUtil.php';
header('Content-Type: application/json');

session_name('SISTEMA_CONTROL_INCES');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuario();
    
    // Obtener el ID del usuario actual de la sesión
    $currentLoggedId = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
    
    // Obtener el ID del usuario que se está editando
    $editingUserId = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : null;
    
    // Obtener la imagen actual primero
    $imagenActual = $usuario->obtenerImagenActual($editingUserId);
    
    // Obtener el rol actual del usuario en la base de datos para seguridad
    $datosUsuarioActual = json_decode($usuario->obtenerUsuarioPorId($editingUserId), true);
    $rolActualBD = $datosUsuarioActual['rol'] ?? null;

    $data = [
        'id_usuario' => $editingUserId,
        'primer_nombre' => $_POST['primer_nombre'] ?? null,
        'segundo_nombre' => $_POST['segundo_nombre'] ?? null,
        'primer_apellido' => $_POST['primer_apellido'] ?? null,
        'segundo_apellido' => $_POST['segundo_apellido'] ?? null,
        'nombre_usuario' => $_POST['nombre_usuario'] ?? null,
        'password' => $_POST['password'] ?? null,
        'rol' => $_POST['rol'] ?? null,
        'estado_usuario' => $_POST['estado_usuario'] ?? null,
        'correo' => $_POST['correo'] ?? null,
        'imagen' => $imagenActual, // Usamos la imagen actual por defecto
    ];

    // VALIDACIÓN DE SEGURIDAD: Prevenir cambio de rol y estado propio
    if ($editingUserId === $currentLoggedId) {
        // Forzamos el rol y el estado que ya tiene en la BD
        $data['rol'] = $rolActualBD;
        $data['estado_usuario'] = $datosUsuarioActual['estado_usuario'] ?? 'Activo';
    }

    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagen'];
        
        // Validar tipo de imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);
        
        if (!in_array($detectedType, $allowedTypes)) {
            echo json_encode(["success" => false, "message" => "Formato de imagen no permitido."]);
            exit;
        }
        
        // Validar tamaño
        if ($file['size'] > 3 * 1024 * 1024) {
            echo json_encode(["success" => false, "message" => "El tamaño de la imagen no puede exceder los 3MB."]);
            exit;
        }
        
        // Generar nombre único para la imagen
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreImagen = 'avatar_' . uniqid() . '.' . $extension;
        $carpetaDestino = __DIR__ . '/../../../assets/img/avatar/';
        
        // Crear directorio si no existe
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }
        
        // Mover la imagen nueva
        $rutaCompleta = $carpetaDestino . $nombreImagen;
        
        if (move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            // Eliminar la imagen anterior si no es la default
            if ($imagenActual && $imagenActual !== 'assets/img/avatar/default-user.png') {
                $rutaImagenAnterior = __DIR__ . '/../../../' . $imagenActual;
                if (file_exists($rutaImagenAnterior)) {
                    unlink($rutaImagenAnterior);
                }
            }
            
            // Guardar solo la ruta relativa en la base de datos
            $data['imagen'] = 'assets/img/avatar/' . $nombreImagen;
        } else {
            echo json_encode(["success" => false, "message" => "Error al subir la imagen."]);
            exit;
        }
    }

    try {
        $response = $usuario->editar($data);
        
        if ($response['success'] && isset($_SESSION['id_usuario'])) {
            $nombreTarget = $data['nombre_usuario'] ?? 'Usuario ID: ' . $data['id_usuario'];
            HistorialUtil::registrar('Editar', "Se editó el usuario: $nombreTarget", 'tb_usuario', $data['id_usuario'], $_SESSION['id_usuario']);
        }
        
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método HTTP no permitido.'
    ]);
}
?>
<?php
session_start();
require_once '../../modelo/conexion.php';
require_once 'managerUsuario.php';

$manager = new Usuario();
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'solicitar_codigo':
            if (!empty($_POST['correo']) && !empty($_POST['nombre_usuario'])) {
                $response = $manager->generarCodigoRecuperacion(
                    $_POST['correo'],
                    $_POST['nombre_usuario']
                );
            }
            break;
            
        case 'verificar_codigo':
            if (!empty($_POST['correo']) && !empty($_POST['codigo']) 
                && !empty($_POST['nueva_password']) && $_POST['nueva_password'] === $_POST['confirmar_password']) {
                
                $response = $manager->cambiarPasswordConCodigo(
                    $_POST['correo'],
                    $_POST['codigo'],
                    $_POST['nueva_password']
                );
                
                if ($response['success']) {
                    $_SESSION['exito_recuperar'] = $response['message'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Datos incompletos o contraseñas no coinciden'];
            }
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
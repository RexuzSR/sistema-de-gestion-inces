<?php
session_start();
// Limpiar campos del formulario si se envió correctamente
$form_limpio = isset($_SESSION['form_limpio']) ? true : false;
if ($form_limpio) {
    unset($_SESSION['form_limpio']);
}

// Preparar valores para los campos del formulario
$valor_correo = $form_limpio ? '' : (isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '');
$valor_nueva_pass = $form_limpio ? '' : (isset($_POST['nueva_password']) ? htmlspecialchars($_POST['nueva_password']) : '');
$valor_confirmar_pass = $form_limpio ? '' : (isset($_POST['confirmar_password']) ? htmlspecialchars($_POST['confirmar_password']) : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - INCES</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    
<section class="container">
    <div class="row justify-content-center align-items-center gap-5">
        <!-- Formulario Recuperación -->
        <div class="col-12 col-md-6 col-lg-5 rounded-3 bg-white shadow-lg container-login">
            <img class="inces__logo" src="../../assets/img/inces-logo.png" alt="Logo INCES">
            
            <h2 class="text-center pt-1">Recuperar Contraseña</h2>
            <p class="text-center pb-4 texto fs-6">INCES Industrial "Bolivariano"</p>
            
            <form action="../controlador/Usuario/recuperar_password.php" method="POST">
                <!-- Correo Electrónico -->
                <label class="pt-2 pb-1 text-color texto-input">Correo Electrónico</label>
                <div class="d-flex justify-content-center align-items-center position-relative">
                    <input class="input w-100 rounded-1" type="email" name="correo" 
                           value="<?= $valor_correo ?>" 
                           placeholder="Introduzca su correo" required>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#181818" class="bi bi-envelope-fill icon" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                    </svg>
                </div>

                <!-- Nueva Contraseña -->
                <label class="pt-4 pb-1 text-color texto-input">Nueva Contraseña</label>
                <div class="d-flex justify-content-center align-items-center position-relative">
                    <input class="input w-100 rounded-1" type="password" name="nueva_password" id="nueva_password" 
                           value="<?= $valor_nueva_pass ?>" 
                           placeholder="Nueva contraseña" required>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#181818" class="bi bi-lock-fill icon" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </div>

                <!-- Confirmar Contraseña -->
                <label class="pt-4 pb-1 text-color texto-input">Confirmar Contraseña</label>
                <div class="d-flex justify-content-center align-items-center position-relative">
                    <input class="input w-100 rounded-1" type="password" name="confirmar_password" id="confirmar_password" 
                           value="<?= $valor_confirmar_pass ?>" 
                           placeholder="Confirmar contraseña" required>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#181818" class="bi bi-lock-fill icon" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </div>

                <!-- Mensajes de error -->
                <?php if(isset($_SESSION['error_recuperar'])): ?>
                    <div class="alert alert-danger mt-3"><?= htmlspecialchars($_SESSION['error_recuperar']); ?></div>
                    <?php unset($_SESSION['error_recuperar']); ?>
                <?php endif; ?>

                <!-- Mensaje de éxito -->
                <?php if(isset($_SESSION['exito_recuperar'])): ?>
                    <div class="alert alert-success mt-3">
                        <?= htmlspecialchars($_SESSION['exito_recuperar']); ?>
                        <div class="mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            <span class="ms-2">Contraseña actualizada correctamente</span>
                        </div>
                    </div>
                    <?php unset($_SESSION['exito_recuperar']); ?>
                <?php endif; ?>


                <!-- Botón de envío -->
                <button type="submit" class="w-100 p-2 mt-4 rounded-1 boton text-white fs-5">Actualizar Contraseña</button>

                <!-- Enlace para volver -->
                <p class=" text-center texto pt-4 texto fs-6">
                <a class="text-primary text-decoration-none" href="/Sistema de Gestion inces/app/vista/index.php" class="text-color text-decoration-none">Volver al inicio de sesión</a></p>
            </form>
        </div>
        
        <!-- Imagen decorativa -->
        <div class="col-12 col-md-6 col-lg-6 img-extend rounded-4 d-none d-md-block"></div>
    </div>
</section>

<script src="../../assets/js/mostrar_password.js"></script>
</body>
</html>
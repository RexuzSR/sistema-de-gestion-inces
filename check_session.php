<?php
// check_session.php

// Configuración de sesión directa
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');

session_name('SISTEMA_CONTROL_INCES');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$valid = false;

// Solo verifica, NO destruye
if (isset($_SESSION['id_usuario']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $valid = true;
}

header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

echo json_encode(['valid' => $valid]);
?>
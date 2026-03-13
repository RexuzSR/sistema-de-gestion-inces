<?php
session_start();

// Lista de páginas que no requieren autenticación
$public_pages = ['index.php', 'reset-password.php'];

// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);

// Si no está en páginas públicas y no tiene sesión, redirigir
if (!in_array($current_page, $public_pages) && !isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

// Si está en página de login pero ya tiene sesión, redirigir al dashboard
if ($current_page === 'index.php' && isset($_SESSION['id_usuario'])) {
    header('Location: Inicio.php');
    exit;
}
?>
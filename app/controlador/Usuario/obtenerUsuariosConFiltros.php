<?php
// obtenerUsuariosConFiltros.php
require_once 'managerUsuario.php';

// Obtener los valores de los filtros
$rol = isset($_GET['rol']) ? $_GET['rol'] : null;
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;

// Crear instancia de la clase Usuario
$usuario = new Usuario();

// Llamar a la función para obtener los usuarios filtrados
$resultado = $usuario->obtenerUsuariosPorFiltro($rol, $estado);

// Devolver el resultado como JSON
echo json_encode($resultado);
?>

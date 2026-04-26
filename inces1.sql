-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-04-2026 a las 01:44:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inces1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ambito`
--

CREATE TABLE `tb_ambito` (
  `id_ambito` int(11) NOT NULL,
  `nombre_ambito` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_ambito`
--

INSERT INTO `tb_ambito` (`id_ambito`, `nombre_ambito`) VALUES
(1, 'Centro de Formacion Socialista'),
(2, 'Comunas'),
(3, 'Instalaciones Militares'),
(4, 'Unidades educativas'),
(5, 'Sistema de Misiones y Grandes Misiones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_areaformativa`
--

CREATE TABLE `tb_areaformativa` (
  `id_areaFormativa` int(11) NOT NULL,
  `nombre_areaFormativa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_areaformativa`
--

INSERT INTO `tb_areaformativa` (`id_areaFormativa`, `nombre_areaFormativa`) VALUES
(1, 'Alimentos y bebidas'),
(2, 'Confecciones'),
(3, 'Procesos industriales'),
(4, 'Forestal'),
(5, 'Seguridad pública'),
(6, 'Planificación y formulación de proyectos'),
(7, 'Instrumentación'),
(8, 'Alojamiento'),
(9, 'Tecnología electrónica'),
(10, 'Tecnología eléctrica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_curso`
--

CREATE TABLE `tb_curso` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_limite_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `horas_totales` decimal(6,2) DEFAULT NULL,
  `horas_por_clase` decimal(4,2) DEFAULT NULL,
  `horas_completadas` decimal(6,2) DEFAULT 0.00,
  `max_participantes` int(11) DEFAULT 30,
  `min_participantes` int(11) DEFAULT 10,
  `num_inscritos` int(11) DEFAULT 0,
  `id_modalidad` int(11) DEFAULT NULL,
  `tipo_formacion` enum('Unidad Curricular') DEFAULT NULL,
  `id_estatusCurso` int(11) DEFAULT NULL,
  `id_sectoreconomico` int(11) DEFAULT NULL,
  `id_subtipo` int(11) DEFAULT NULL,
  `id_areaFormativa` int(11) DEFAULT NULL,
  `id_ambito` int(11) DEFAULT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `id_nombre_especifico` int(11) DEFAULT NULL,
  `turno` enum('Mañana','Tarde') NOT NULL,
  `programa_formacion` enum('Programa de Formación Productiva (PFP)') DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_curso`
--

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `id_sectoreconomico`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `id_ppl`, `id_nombre_especifico`, `turno`, `programa_formacion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(91, 'Prostitución-Avanzada', '2026-01-08', NULL, '2026-03-06', NULL, NULL, 0.00, 25, 10, 1, 1, 'Unidad Curricular', 5, NULL, NULL, 7, NULL, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-07 08:15:22', '2026-03-07 23:13:13'),
(95, 'constructores', '2026-01-21', '2026-01-02', '2026-03-12', 60.00, 5.00, 0.00, 40, 10, 3, 2, 'Unidad Curricular', 5, 1, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-22 05:23:42', '2026-03-13 03:12:59'),
(96, 'Ingles', '2026-01-22', '0000-00-00', '2026-02-12', 5.00, 4.00, 0.00, 40, 10, 3, 1, 'Unidad Curricular', 5, 3, 2, 6, 5, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-22 05:27:19', '2026-03-07 23:13:13'),
(108, 'constructor', '2026-03-17', '2026-03-26', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 0, 2, 'Unidad Curricular', 5, 2, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 00:35:51', '2026-03-31 03:09:21'),
(109, 'EstadosComprobar', '2026-03-17', '2026-03-26', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 6, 2, 'Unidad Curricular', 5, 2, 3, 8, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 04:14:04', '2026-03-31 03:09:21'),
(110, 'constructoras', '2026-03-17', '2026-03-06', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 0, 1, 'Unidad Curricular', 5, 2, 3, 8, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 06:39:52', '2026-03-31 03:09:21'),
(111, 'constructoras', '2026-03-24', '2026-04-02', '2026-04-14', 80.00, 5.00, 0.00, 50, 12, 0, 2, 'Unidad Curricular', 5, 2, 1, 8, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-03-14 21:33:32', '2026-04-26 23:43:08'),
(113, 'constructorass', '2026-04-06', '2026-04-15', '2026-04-24', 60.00, 4.00, 0.00, 40, 12, 2, 2, 'Unidad Curricular', 5, 1, 3, 8, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-26 22:31:04', '2026-04-26 23:43:08'),
(114, 'constructoresasd', '2026-04-20', '2026-04-29', '2026-05-13', 70.00, 4.00, 0.00, 40, 12, 0, 2, 'Unidad Curricular', 2, 2, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-10 08:39:01', '2026-04-26 23:22:45'),
(400, 'Seguridad y Salud Laboral (Culminado 2024)', '2024-01-10', '2024-01-20', '2024-04-05', 80.00, 4.00, 80.00, 25, 10, 12, 1, 'Unidad Curricular', 5, 2, 3, 5, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-01-05 07:00:00', '2026-04-26 23:42:55'),
(401, 'Ofimática Básica (Culminado 2024)', '2024-06-10', '2024-06-20', '2024-09-15', 60.00, 4.00, 60.00, 30, 10, 12, 3, 'Unidad Curricular', 5, 3, 1, 9, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-05-25 09:00:00', '2026-04-26 23:42:55'),
(402, 'Atención y Servicio al Cliente (En espera)', '2024-03-01', '2024-03-10', '2024-05-20', 70.00, 5.00, 0.00, 35, 10, 13, 1, 'Unidad Curricular', 5, 1, 2, 8, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-02-15 08:30:00', '2026-04-26 23:43:08'),
(403, 'Inglés Básico – Módulo 1 (Diferido)', '2024-08-01', '2024-08-10', '2024-10-10', 40.00, 4.00, 0.00, 20, 8, 4, 2, 'Unidad Curricular', 5, 3, 3, 6, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-07-15 10:00:00', '2026-04-26 23:43:08'),
(404, 'Panadería Básica (Diferido)', '2024-09-01', '2024-09-10', '2024-11-15', 50.00, 5.00, 0.00, 22, 10, 4, 1, 'Unidad Curricular', 5, 2, 1, 1, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-08-18 09:00:00', '2026-04-26 23:43:09'),
(405, 'Técnicas de Estudio (Diferido)', '2024-10-01', '2024-10-05', '2024-12-10', 30.00, 3.00, 0.00, 25, 10, 4, 2, 'Unidad Curricular', 5, 1, 3, 6, 5, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-09-20 11:00:00', '2026-04-26 23:43:09'),
(406, 'Redacción Básica (Diferido)', '2024-04-10', '2024-04-15', '2024-06-10', 35.00, 3.50, 0.00, 20, 8, 3, 1, 'Unidad Curricular', 5, 2, 2, 10, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-03-25 14:00:00', '2026-04-26 23:43:10'),
(407, 'Historia Contemporánea (Diferido)', '2024-11-05', '2024-11-12', '2025-01-15', 45.00, 4.50, 0.00, 22, 10, 3, 2, 'Unidad Curricular', 5, 1, 3, 6, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-10-28 08:00:00', '2026-04-26 23:43:10'),
(408, 'Matemática Aplicada (Diferido)', '2024-07-01', '2024-07-10', '2024-09-05', 40.00, 4.00, 0.00, 22, 10, 3, 1, 'Unidad Curricular', 5, 2, 3, 5, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-06-15 09:30:00', '2026-04-26 23:43:10'),
(409, 'Contabilidad para Emprendedores (Diferido)', '2024-05-02', '2024-05-10', '2024-07-20', 50.00, 5.00, 0.00, 20, 8, 2, 3, 'Unidad Curricular', 5, 1, 2, 6, 1, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-04-18 14:00:00', '2026-04-26 23:43:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_discapacidad`
--

CREATE TABLE `tb_discapacidad` (
  `id_discapacidad` int(11) NOT NULL,
  `nombre_discapacidad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_discapacidad`
--

INSERT INTO `tb_discapacidad` (`id_discapacidad`, `nombre_discapacidad`) VALUES
(1, 'Ninguna'),
(2, 'Visual'),
(3, 'Auditiva'),
(4, 'Motora');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_docente`
--

CREATE TABLE `tb_docente` (
  `id_docente` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` int(11) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `estado_docente` enum('Disponible','En formación','Asignado','Inactivo') DEFAULT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `tipo_docente` enum('Fijo','Contratado','PDL','Voluntario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_docente`
--

INSERT INTO `tb_docente` (`id_docente`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `correo`, `fecha_nacimiento`, `edad`, `estado_docente`, `genero`, `tipo_docente`) VALUES
(21, 'Jorge', '', 'Santos', '', 31104273, 'gdanieldj@gmail.com', '2000-06-08', 25, 'Disponible', 'Femenino', 'Fijo'),
(22, 'maigualida ', 'del valle', 'Rengel', '', 12665258, 'examplee@gmail.com', '1975-10-15', 50, 'En formación', 'Femenino', 'Voluntario'),
(23, 'samuel ', 'jose ', 'rivas', 'rengel', 30635942, 'example@gmail.com', '2004-10-27', 21, 'Asignado', 'Masculino', 'Fijo'),
(24, 'asdf', 'asdf', 'asdff', 'qwerty', 8643258, 'example@gmail.com', '2004-10-20', 21, 'Asignado', 'Femenino', 'Voluntario'),
(25, 'Trinity', 'tarcillena', 'asdff', 'asdasf', 8643257, 'samueljosealexander@gmail.com', '2010-04-15', 15, 'Asignado', 'Femenino', 'PDL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_docente_curso`
--

CREATE TABLE `tb_docente_curso` (
  `id_docente_curso` int(11) NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha_asignacion` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_docente_curso`
--

INSERT INTO `tb_docente_curso` (`id_docente_curso`, `id_docente`, `id_curso`, `fecha_asignacion`) VALUES
(56, 11, 90, '2025-12-18'),
(57, 12, 79, '2025-12-18'),
(58, 14, 76, '2025-12-18'),
(59, NULL, 80, '2025-12-18'),
(62, 22, 96, '2026-01-21'),
(63, 23, 95, '2026-01-21'),
(89, 24, 113, '2026-03-26'),
(90, 25, 111, '2026-03-26'),
(91, 23, 110, '2026-03-26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_estatuscurso`
--

CREATE TABLE `tb_estatuscurso` (
  `id_estatusCurso` int(11) NOT NULL,
  `nombre_estatusCurso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_estatuscurso`
--

INSERT INTO `tb_estatuscurso` (`id_estatusCurso`, `nombre_estatusCurso`) VALUES
(1, 'En espera'),
(2, 'Diferido'),
(3, 'En proceso'),
(4, 'En pausa'),
(5, 'Culminado'),
(6, 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_historial`
--

CREATE TABLE `tb_historial` (
  `id_historial` int(11) NOT NULL,
  `tipo_actividad` varchar(50) NOT NULL COMMENT 'Crear, Editar, Eliminar, Login, Ver, etc.',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción detallada de la actividad',
  `tabla_afectada` varchar(100) DEFAULT NULL COMMENT 'Nombre de la tabla afectada',
  `id_registro` int(11) DEFAULT NULL COMMENT 'ID del registro afectado',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario que realizó la acción',
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_historial`
--

INSERT INTO `tb_historial` (`id_historial`, `tipo_actividad`, `descripcion`, `tabla_afectada`, `id_registro`, `usuario_id`, `fecha_hora`) VALUES
(1, 'Crear', 'Se creó el participante: Jorge Sucre', 'tb_participante', 351, 16, '2026-03-14 16:39:59'),
(2, 'Editar', 'Se editó el curso: constructoras', 'tb_curso', 110, 16, '2026-03-14 16:50:35'),
(3, 'Crear', 'Se creó la meta: Aprobados (800)', 'tb_metas', 19, 16, '2026-03-14 17:59:43'),
(4, 'Crear', 'Se creó la meta: Aprobados (400)', 'tb_metas', 20, 16, '2026-03-26 03:10:32'),
(5, 'Editar', 'Se editó la meta: Aprobados (20)', 'tb_metas', 17, 16, '2026-03-26 03:10:49'),
(6, 'Editar', 'Se editó la meta: Aprobados (20)', 'tb_metas', 17, 16, '2026-03-26 03:10:54'),
(7, 'Crear', 'Se creó el participante: Alexander Serrada', 'tb_participante', 352, 16, '2026-03-26 17:14:34'),
(8, 'Eliminar', 'Se eliminó el curso: Curso de Prueba 2', 'tb_curso', 112, 16, '2026-03-26 17:30:39'),
(9, 'Crear', 'Se creó el curso: constructorass', 'tb_curso', 113, 16, '2026-03-26 17:31:04'),
(10, 'Crear', 'Se creó el docente: Trinity asdff', 'tb_docente', NULL, 16, '2026-03-26 17:38:32'),
(11, 'Editar', 'Se asignó el docente samuel  rivas al curso constructoras', 'tb_docente_curso', 23, 16, '2026-03-26 18:12:21'),
(12, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-26 18:21:36'),
(13, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-26 18:21:59'),
(14, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-26 18:24:16'),
(15, 'Logout', 'Usuario samu cerró sesión', 'tb_usuario', 16, 16, '2026-03-26 18:24:22'),
(16, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-26 18:24:31'),
(17, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-28 01:29:43'),
(18, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-31 03:09:22'),
(19, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 14:49:12'),
(20, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 14:49:43'),
(21, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 15:39:33'),
(22, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 20:53:04'),
(23, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 21:40:01'),
(24, 'Crear', 'Se creó el usuario: Nuevo Usuario', 'tb_usuario', NULL, 16, '2026-04-08 21:41:04'),
(25, 'Crear', 'Se creó el usuario: Nuevo Usuario', 'tb_usuario', NULL, 16, '2026-04-08 21:41:20'),
(26, 'Crear', 'Se creó el usuario: Nuevo Usuario', 'tb_usuario', NULL, 16, '2026-04-08 21:41:35'),
(27, 'Eliminar', 'Se eliminó el usuario: USUARIO', 'tb_usuario', 17, 16, '2026-04-08 21:54:21'),
(28, 'Eliminar', 'Se eliminó el usuario: users', 'tb_usuario', 18, 16, '2026-04-08 21:54:23'),
(29, 'Eliminar', 'Se eliminó el usuario: samuelo', 'tb_usuario', 19, 16, '2026-04-08 21:54:27'),
(30, 'Crear', 'Se creó el usuario: usuario', 'tb_usuario', 20, 16, '2026-04-08 21:55:00'),
(31, 'Logout', 'Usuario samu cerró sesión', 'tb_usuario', 16, 16, '2026-04-08 21:55:05'),
(32, 'Login', 'Usuario usuario inició sesión', 'tb_usuario', 20, 20, '2026-04-08 21:55:17'),
(33, 'Login', 'Usuario usuario inició sesión', 'tb_usuario', 20, 20, '2026-04-08 22:39:57'),
(34, 'Logout', 'Usuario usuario cerró sesión', 'tb_usuario', 20, 20, '2026-04-08 22:55:52'),
(35, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-08 22:55:57'),
(36, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-09 01:37:03'),
(37, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-09 02:20:58'),
(38, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-09 02:31:04'),
(39, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-09 02:35:05'),
(40, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-09 02:50:31'),
(41, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-10 02:29:03'),
(42, 'Crear', 'Se creó el curso: constructoresasd', 'tb_curso', 114, 16, '2026-04-10 02:39:01'),
(43, 'Crear', 'Se creó la meta: Aprobados (100)', 'tb_metas', 21, 16, '2026-04-10 02:49:54'),
(44, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-10 03:14:06'),
(45, 'Eliminar', 'Se eliminó la meta: Aprobados (400)', 'tb_metas', 20, 16, '2026-04-10 03:22:32'),
(46, 'Eliminar', 'Se eliminó la meta: Meta (0)', 'tb_metas', 20, 16, '2026-04-10 03:22:34'),
(47, 'Eliminar', 'Se eliminó la meta: Meta (0)', 'tb_metas', 20, 16, '2026-04-10 03:22:34'),
(48, 'Crear', 'Se creó la meta: Inscritos (5000)', 'tb_metas', 22, 16, '2026-04-10 03:25:24'),
(49, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-04-12 03:59:43'),
(50, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-04-12 04:33:39'),
(51, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-04-13 05:04:56'),
(52, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-04-26 23:43:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_metas`
--

CREATE TABLE `tb_metas` (
  `id_meta` int(11) NOT NULL,
  `id_ambito` int(11) DEFAULT NULL,
  `meta_objetivo` int(11) NOT NULL,
  `estudiantes_formados` int(11) NOT NULL DEFAULT 0,
  `tipo_objetivo` enum('Aprobados','Inscritos') NOT NULL DEFAULT 'Aprobados',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Cerrada') DEFAULT 'Activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `porcentaje_cumplimiento` decimal(5,2) GENERATED ALWAYS AS (round(`estudiantes_formados` / `meta_objetivo` * 100,2)) STORED,
  `estado_cumplimiento` enum('En progreso','Cumplida') GENERATED ALWAYS AS (case when `estudiantes_formados` >= `meta_objetivo` then 'Cumplida' else 'En progreso' end) STORED,
  `ambito_id` int(11) NOT NULL,
  `periodo_tiempo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_metas`
--

INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`, `ambito_id`, `periodo_tiempo`) VALUES
(17, 1, 20, 0, 'Aprobados', '2026-01-01', '0000-00-00', 'Cerrada', '2026-01-31 14:37:58', 0, NULL),
(18, 1, 20, 0, 'Aprobados', '2026-01-01', '2026-01-31', 'Cerrada', '2026-01-31 15:00:26', 0, NULL),
(19, 1, 800, 0, 'Aprobados', '2026-03-01', '2026-03-28', 'Cerrada', '2026-03-14 17:59:43', 0, NULL),
(21, NULL, 100, 0, 'Aprobados', '2026-04-10', '2026-07-09', 'Activa', '2026-04-10 02:49:54', 0, NULL),
(22, NULL, 5000, 0, 'Inscritos', '2026-04-10', '2026-07-09', 'Activa', '2026-04-10 03:25:20', 0, NULL),
(40, 1, 500, 120, 'Aprobados', '2024-01-01', '2024-06-30', 'Activa', '2024-01-02 07:00:00', 1, '2024-S1'),
(41, 2, 300, 300, 'Inscritos', '2024-01-01', '2024-03-31', 'Cerrada', '2024-01-03 08:00:00', 2, '2024-T1'),
(42, 3, 200, 85, 'Aprobados', '2024-04-01', '2024-09-30', 'Activa', '2024-04-01 07:30:00', 3, '2024-S2'),
(43, 4, 150, 150, 'Inscritos', '2024-04-01', '2024-06-30', 'Cerrada', '2024-04-02 09:00:00', 4, '2024-T2'),
(44, 5, 400, 210, 'Aprobados', '2024-07-01', '2024-12-31', 'Activa', '2024-07-01 08:00:00', 5, '2024-S3'),
(45, 1, 600, 600, 'Inscritos', '2024-07-01', '2024-09-30', 'Cerrada', '2024-07-02 10:00:00', 1, '2024-T3'),
(46, 2, 250, 90, 'Aprobados', '2024-10-01', '2025-03-31', 'Activa', '2024-10-01 07:00:00', 2, '2024-S4'),
(47, 3, 180, 180, 'Inscritos', '2024-10-01', '2024-12-31', 'Cerrada', '2024-10-02 09:30:00', 3, '2024-T4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_modalidad`
--

CREATE TABLE `tb_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `nombre_modalidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_modalidad`
--

INSERT INTO `tb_modalidad` (`id_modalidad`, `nombre_modalidad`) VALUES
(1, 'Presencial'),
(2, 'Online'),
(3, 'Semi presencial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_nombrecurso`
--

CREATE TABLE `tb_nombrecurso` (
  `id_nombrecurso` int(11) NOT NULL,
  `nombre_curso` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_nombrecurso`
--

INSERT INTO `tb_nombrecurso` (`id_nombrecurso`, `nombre_curso`) VALUES
(1, 'INGLÉS CON PROPÓSITOS GENERALES. NIVEL BÁSICO I'),
(2, 'AMBIENTE Y PROTECCIÓN AMBIENTAL'),
(3, 'SEGURIDAD Y SALUD LABORAL'),
(4, 'ESTADÍSTICA APLICADA A LA SEGURIDAD OCUPACIONAL'),
(5, 'MATEMÁTICA BÁSICA'),
(6, 'ORTOGRAFÍA Y REDACCIÓN'),
(7, 'HISTORIA CONTEMPORÁNEA DE VENEZUELA'),
(8, 'NUEVO MODELO DE GESTIÓN PRODUCTIVA'),
(9, 'HABILIDADES SOCIOEMOCIONALES'),
(10, 'LECTURA Y ESCRITURA CRÍTICA'),
(11, 'ÉTICA Y VALORES PARA LA TRANSFORMACIÓN SOCIAL'),
(12, 'TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIÓN (TIC)'),
(13, 'PROYECTO DE INNOVACIÓN PRODUCTIVA'),
(14, 'CÁLCULO DE COSTOS Y PRESUPUESTOS'),
(15, 'MANTENIMIENTO PREVENTIVO DE EQUIPOS Y MAQUINARIAS'),
(16, 'GESTIÓN COMUNAL Y PARTICIPACIÓN CIUDADANA'),
(17, 'INTRODUCCIÓN AL DESARROLLO DE SOFTWARE'),
(18, 'LENGUAJES DE PROGRAMACIÓN BÁSICA'),
(19, 'BASES DE DATOS FUNDAMENTALES'),
(20, 'REDES Y TELECOMUNICACIONES BÁSICAS'),
(21, 'DIBUJO TÉCNICO APLICADO'),
(22, 'MATERIALES Y HERRAMIENTAS DE CONSTRUCCIÓN'),
(23, 'FUNDAMENTOS DE ALBAÑILERÍA'),
(24, 'LECTURA E INTERPRETACIÓN DE PLANOS'),
(25, 'TECNOLOGÍA DE LOS ALIMENTOS'),
(26, 'HIGIENE Y MANIPULACIÓN DE ALIMENTOS'),
(27, 'TÉCNICAS BÁSICAS de COCINA'),
(28, 'PANADERÍA Y PASTELERÍA BÁSICA'),
(29, 'ADMINISTRACIÓN Y GESTIÓN DE PEQUEÑAS EMPRESAS'),
(30, 'ATENCIÓN Y SERVICIO AL CLIENTE'),
(31, 'MERCADEO Y VENTAS DIGITALES'),
(32, 'DERECHO LABORAL Y LEGISLACIÓN VINCULANTE'),
(33, 'PRIMEROS AUXILIOS Y EMERGENCIAS'),
(34, 'RECREACIÓN Y DINÁMICA DE GRUPOS'),
(35, 'CULTURA Y MANIFESTACIONES TRADICIONALES'),
(36, 'MANEJO DE REDES SOCIALES PARA EMPRENDIMIENTOS'),
(37, 'FIBRA ÓPTICA Y NUEVAS TECNOLOGÍAS'),
(38, 'MEDICIONES DE ENLACE'),
(39, 'DISEÑO Y CONSTRUCCIÓN DE REDES DE FIBRA ÓPTICA'),
(40, 'CERTIFICACIÓN DE INSTALACIÓN.'),
(41, 'ELABORACIÓN DE PRESUPUESTO PARA FIBRA ÓPTICA'),
(42, 'DULCES A BASE DE CACAO'),
(43, 'BOMBONES Y FIGURAS DECORATIVAS CON CHOCOLATE'),
(44, 'TIPOLOGÍA DE MUEBLES DE MADERA'),
(45, 'CONSERVACIÓN DE MUEBLES DE MADERA'),
(46, 'TÉCNICAS DE LUSTRE DE PINTURA Y LAQUEADO'),
(47, 'MATERIALES Y HERRAMIENTA PARA PINTAR Y LUSTRAR'),
(48, 'TÉCNICAS DE RESTAURACIÓN DE MADERA'),
(49, 'Alfabetización Tecnológica'),
(50, 'Arquitectura del Computador'),
(51, 'Algoritmia y Programación'),
(52, 'Base de Datos'),
(53, 'Redes de Computadoras'),
(54, 'Ingeniería de Software'),
(55, 'Sistemas Operativos'),
(56, 'Proyecto Socio Tecnológico'),
(57, 'Proyecto Ético de Vida'),
(58, 'Dirección de Ventas'),
(59, 'Contabilidad Básica'),
(60, 'Ofimática'),
(61, 'Comunicación Organizacional'),
(62, 'Política Internacional'),
(63, 'Comunicación Digital'),
(64, 'Redes Sociales'),
(65, 'Producción de Contenidos'),
(66, 'Matemática Aplicada'),
(67, 'Técnicas de Construcción'),
(68, 'Seguridad Industrial'),
(69, 'Procesos Constructivos Tradicionales'),
(70, 'Uso de Materiales Naturales'),
(71, 'Seguridad en Obras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_objetivos`
--

CREATE TABLE `tb_objetivos` (
  `id_objetivo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_objetivos`
--

INSERT INTO `tb_objetivos` (`id_objetivo`, `descripcion`) VALUES
(1, 'Objetivo 1'),
(2, 'Capacitación Básica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_participante`
--

CREATE TABLE `tb_participante` (
  `id_participante` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` int(11) NOT NULL,
  `edad` int(11) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` date DEFAULT current_timestamp(),
  `estado` enum('En formaci?n','Inactivo','Disponible','Asignado','En sistema','Diferido') NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `id_discapacidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
(339, 'Jorge', 'Josmauro', 'Santos', 'Silva', 31104273, 25, 'silvajosmauro@gmail.com', '2000-02-12', '2026-01-07', 'En sistema', 'Primaria', 'Femenino', 2),
(341, 'samuel', 'jose', 'rivas', 'rengel', 30635942, 21, 'samueljosealexander@gmail.com', '2004-10-27', '2026-01-07', 'En sistema', 'Universidad', 'Masculino', 1),
(343, 'samuel', 'jose', 'rivas', 'rengel', 30635941, 21, 'example@gmail.com', '2004-10-27', '2026-01-11', 'En sistema', 'Universidad', 'Masculino', 2),
(345, 'Alexander', 'Del Valle', 'Rivas', 'Rengel', 30635932, 52, 'example@gmail.com', '1973-10-10', '2026-02-27', 'En sistema', 'Primaria', 'Masculino', 3),
(346, 'Trinity', 'Jose', 'Rivas', 'Morales', 33312864, 15, 'example@gmail.com', '2010-04-15', '2026-02-27', 'En sistema', 'Primaria', 'Femenino', 3),
(347, 'Alexander', 'Jose', 'Rivas', 'Rengel', 6803423, 49, 'example@gmail.com', '1976-05-20', '2026-03-07', 'En sistema', 'Bachillerato', 'Masculino', 2),
(348, 'Alexander', 'Jose', 'Nolazco', 'Gonzales', 30655942, 60, 'example@gmail.com', '1965-04-15', '2026-03-07', 'En sistema', 'Primaria', 'Masculino', 3),
(349, 'Alexander', 'Del Valle', 'Rivas', 'Rengel', 30635742, 45, 'example@gmail.com', '1980-04-10', '2026-03-07', 'En sistema', 'Bachillerato', 'Femenino', 2),
(350, 'Trinity', 'Angel Ys', 'Rivas', 'Diouh', 30638942, 65, 'example@gmail.com', '1960-04-15', '2026-03-07', 'En sistema', 'Primaria', 'Femenino', 2),
(351, 'Jorge', 'Del Valle', 'Sucre', 'Cumana', 7894561, 15, 'example@gmail.com', '2010-06-09', '2026-03-14', 'En sistema', 'Bachillerato', 'Masculino', 4),
(352, 'Alexander', 'Del Valle', 'Serrada', 'Martinez', 30635956, 51, 'examplee@gmail.com', '1975-03-26', '2026-03-26', 'En sistema', 'Bachillerato', 'Masculino', 3),
(500, 'Luisa', 'Fernanda', 'Martinez', 'Blanco', 20000001, 25, 'lfer01@mail.com', '1999-02-10', '2024-01-10', 'En formaci?n', 'Universidad', 'Femenino', 1),
(501, 'Pedro', 'Luis', 'Gomez', 'Vera', 20000002, 30, 'plg02@mail.com', '1994-06-15', '2024-01-10', 'En formaci?n', 'Bachillerato', 'Masculino', 2),
(502, 'Ana', 'Maria', 'Diaz', 'Castro', 20000003, 27, 'amd03@mail.com', '1997-09-20', '2024-01-10', 'En formaci?n', 'Universidad', 'Femenino', 1),
(503, 'Carlos', 'Alberto', 'Rojas', 'Mendoza', 20000004, 35, 'car04@mail.com', '1989-03-05', '2024-01-10', 'En formaci?n', 'Primaria', 'Masculino', 3),
(504, 'Sofia', 'Isabel', 'Perez', 'Lara', 20000005, 23, 'sip05@mail.com', '2001-07-12', '2024-01-10', 'En formaci?n', 'Universidad', 'Femenino', 4),
(505, 'Miguel', 'Angel', 'Torres', 'Reyes', 20000006, 42, 'mig06@mail.com', '1982-11-22', '2024-01-10', 'En formaci?n', 'Bachillerato', 'Masculino', 1),
(506, 'Carmen', 'Andrea', 'Suarez', 'Morales', 20000007, 29, 'cas07@mail.com', '1995-08-14', '2024-01-10', 'En formaci?n', 'Universidad', 'Femenino', 2),
(507, 'Jose', 'Gregorio', 'Herrera', 'Vega', 20000008, 33, 'jgh08@mail.com', '1991-05-18', '2024-01-10', 'En formaci?n', 'Bachillerato', 'Masculino', 1),
(508, 'Rosa', 'Elena', 'Flores', 'Acosta', 20000009, 38, 'ref09@mail.com', '1986-01-30', '2024-01-10', 'En formaci?n', 'Primaria', 'Femenino', 3),
(509, 'Manuel', 'Jose', 'Ortiz', 'Garcia', 20000010, 45, 'mjo10@mail.com', '1979-09-07', '2024-01-10', 'En formaci?n', 'Primaria', 'Masculino', 4),
(510, 'Gabriela', 'Cecilia', 'Ramirez', 'Molina', 20000011, 31, 'gcr11@mail.com', '1993-12-05', '2024-02-05', 'Inactivo', 'Universidad', 'Femenino', 2),
(511, 'Hector', 'Luis', 'Vargas', 'Campos', 20000012, 40, 'hlv12@mail.com', '1984-06-25', '2024-02-05', 'Inactivo', 'Bachillerato', 'Masculino', 1),
(512, 'Beatriz', 'Alejandra', 'Castro', 'Rivas', 20000013, 27, 'bac13@mail.com', '1997-01-14', '2024-02-05', 'Inactivo', 'Universidad', 'Femenino', 3),
(513, 'Mario', 'Enrique', 'Guzman', 'Pinto', 20000014, 52, 'meg14@mail.com', '1972-10-11', '2024-02-05', 'Inactivo', 'Primaria', 'Masculino', 4),
(514, 'Lorena', 'Marcela', 'Fuentes', 'Santana', 20000015, 36, 'lmf15@mail.com', '1988-03-22', '2024-02-05', 'Inactivo', 'Bachillerato', 'Femenino', 2),
(515, 'Diego', 'Mauricio', 'Parra', 'Cruz', 20000016, 29, 'dmp16@mail.com', '1995-07-07', '2024-02-05', 'Inactivo', 'Universidad', 'Masculino', 1),
(516, 'Monica', 'Ximena', 'Leon', 'Espinoza', 20000017, 44, 'mxl17@mail.com', '1980-09-19', '2024-02-05', 'Inactivo', 'Primaria', 'Femenino', 3),
(517, 'Andres', 'Felipe', 'Navarro', 'Serrano', 20000018, 33, 'afn18@mail.com', '1991-04-30', '2024-02-05', 'Inactivo', 'Bachillerato', 'Masculino', 1),
(518, 'Natalia', 'Rocio', 'Ibarra', 'Rios', 20000019, 26, 'nri19@mail.com', '1998-08-12', '2024-02-05', 'Inactivo', 'Universidad', 'Femenino', 2),
(519, 'Ernesto', 'Raul', 'Mejia', 'Giraldo', 20000020, 48, 'erm20@mail.com', '1976-12-01', '2024-02-05', 'Inactivo', 'Primaria', 'Masculino', 4),
(520, 'Paola', 'Andrea', 'Cortes', 'Urrea', 20000021, 24, 'pac21@mail.com', '2000-05-09', '2024-03-12', 'Disponible', 'Universidad', 'Femenino', 1),
(521, 'Oscar', 'Andres', 'Lozano', 'Ossa', 20000022, 37, 'oal22@mail.com', '1987-11-03', '2024-03-12', 'Disponible', 'Bachillerato', 'Masculino', 3),
(522, 'Cristina', 'Beatriz', 'Duarte', 'Velez', 20000023, 41, 'cbd23@mail.com', '1983-06-18', '2024-03-12', 'Disponible', 'Primaria', 'Femenino', 2),
(523, 'Javier', 'Ignacio', 'Zapata', 'Arango', 20000024, 28, 'jiz24@mail.com', '1996-02-25', '2024-03-12', 'Disponible', 'Universidad', 'Masculino', 1),
(524, 'Valentina', 'Carolina', 'Narvaez', 'Bermudez', 20000025, 33, 'vcn25@mail.com', '1991-10-13', '2024-03-12', 'En sistema', 'Bachillerato', 'Femenino', 4),
(525, 'Roberto', 'Carlos', 'Ayala', 'Restrepo', 20000026, 46, 'rca26@mail.com', '1978-07-29', '2024-03-12', 'En sistema', 'Primaria', 'Masculino', 2),
(526, 'Adriana', 'Lucia', 'Palacios', 'Mora', 20000027, 29, 'alp27@mail.com', '1995-12-08', '2024-03-12', 'En sistema', 'Universidad', 'Femenino', 1),
(527, 'German', 'Antonio', 'Hurtado', 'Cano', 20000028, 55, 'gah28@mail.com', '1969-03-15', '2024-03-12', 'En sistema', 'Primaria', 'Masculino', 3),
(528, 'Sandra', 'Milena', 'Rincon', 'Galvis', 20000029, 38, 'smr29@mail.com', '1986-08-20', '2024-03-12', 'En sistema', 'Bachillerato', 'Femenino', 2),
(529, 'Felipe', 'Santiago', 'Avila', 'Pabon', 20000030, 31, 'fsa30@mail.com', '1993-04-05', '2024-03-12', 'En sistema', 'Universidad', 'Masculino', 4),
(530, 'Marcela', 'Johana', 'Ruiz', 'Pineda', 20000031, 27, 'mjr31@mail.com', '1997-05-22', '2024-04-20', 'En sistema', 'Universidad', 'Femenino', 1),
(531, 'Alberto', 'Jose', 'Beltran', 'Londoño', 20000032, 42, 'ajb32@mail.com', '1982-01-17', '2024-04-20', 'En sistema', 'Bachillerato', 'Masculino', 2),
(532, 'Nelly', 'Patricia', 'Ochoa', 'Gil', 20000033, 35, 'npo33@mail.com', '1989-09-03', '2024-04-20', 'En sistema', 'Primaria', 'Femenino', 3),
(533, 'Esteban', 'David', 'Cardenas', 'Franco', 20000034, 30, 'edc34@mail.com', '1994-11-11', '2024-04-20', 'En sistema', 'Universidad', 'Masculino', 1),
(534, 'Diana', 'Carolina', 'Paz', 'Moreno', 20000035, 40, 'dcp35@mail.com', '1984-06-14', '2024-04-20', 'En sistema', 'Bachillerato', 'Femenino', 4),
(535, 'Armando', 'Rafael', 'Tovar', 'Soto', 20000036, 49, 'art36@mail.com', '1975-03-28', '2024-04-20', 'En sistema', 'Primaria', 'Masculino', 2),
(536, 'Veronica', 'Alejandra', 'Mendoza', 'Zamora', 20000037, 28, 'vam37@mail.com', '1996-07-09', '2024-04-20', 'En sistema', 'Universidad', 'Femenino', 1),
(537, 'Cesar', 'Augusto', 'Paredes', 'Osorio', 20000038, 37, 'cap38@mail.com', '1987-12-19', '2024-04-20', 'En sistema', 'Bachillerato', 'Masculino', 3),
(538, 'Gloria', 'Esperanza', 'Quintana', 'Carvajal', 20000039, 44, 'geq39@mail.com', '1980-05-02', '2024-04-20', 'En sistema', 'Primaria', 'Femenino', 2),
(539, 'Jaime', 'Alonso', 'Caicedo', 'Tamayo', 20000040, 32, 'jac40@mail.com', '1992-10-18', '2024-04-20', 'En sistema', 'Universidad', 'Masculino', 4),
(540, 'Catalina', 'Isabel', 'Rico', 'Guerra', 20000041, 26, 'cir41@mail.com', '1998-01-30', '2024-06-08', 'En sistema', 'Universidad', 'Femenino', 1),
(541, 'Ramiro', 'Ernesto', 'Bautista', 'Rueda', 20000042, 51, 'reb42@mail.com', '1973-09-14', '2024-06-08', 'En sistema', 'Primaria', 'Masculino', 3),
(542, 'Sonia', 'Maribel', 'Villamil', 'Uribe', 20000043, 34, 'smv43@mail.com', '1990-06-21', '2024-06-08', 'En sistema', 'Bachillerato', 'Femenino', 2),
(543, 'Gonzalo', 'Javier', 'Piedrahita', 'Cordoba', 20000044, 29, 'gjp44@mail.com', '1995-02-17', '2024-06-08', 'En sistema', 'Universidad', 'Masculino', 1),
(544, 'Marina', 'Teresa', 'Correa', 'Orozco', 20000045, 47, 'mtc45@mail.com', '1977-12-03', '2024-06-08', 'En sistema', 'Primaria', 'Femenino', 4),
(545, 'Humberto', 'Andres', 'Vergara', 'Acevedo', 20000046, 33, 'hav46@mail.com', '1991-08-22', '2024-06-08', 'En sistema', 'Bachillerato', 'Masculino', 2),
(546, 'Andrea', 'Liliana', 'Saenz', 'Barrera', 20000047, 28, 'als47@mail.com', '1996-05-11', '2024-06-08', 'En sistema', 'Universidad', 'Femenino', 1),
(547, 'Ivan', 'Jose', 'Andrade', 'Vidal', 20000048, 41, 'ija48@mail.com', '1983-04-06', '2024-06-08', 'En sistema', 'Bachillerato', 'Masculino', 3),
(548, 'Liliana', 'Maria', 'Diaz', 'Espino', 20000049, 36, 'lmd49@mail.com', '1988-11-02', '2024-06-08', 'En sistema', 'Universidad', 'Femenino', 2),
(549, 'Nicolas', 'Felipe', 'Cuevas', 'Mesa', 20000050, 45, 'nfc50@mail.com', '1979-07-18', '2024-06-08', 'En sistema', 'Primaria', 'Masculino', 4),
(550, 'Paula', 'Andrea', 'Benitez', 'Murillo', 20000051, 22, 'pab51@mail.com', '2002-09-09', '2024-08-01', 'En sistema', 'Universidad', 'Femenino', 1),
(551, 'Rafael', 'Eduardo', 'Mojica', 'Valencia', 20000052, 39, 'rem52@mail.com', '1985-01-25', '2024-08-01', 'En sistema', 'Bachillerato', 'Masculino', 2),
(552, 'Alejandra', 'Patricia', 'Barragan', 'Heredia', 20000053, 31, 'apb53@mail.com', '1993-03-30', '2024-08-01', 'En sistema', 'Universidad', 'Femenino', 3),
(553, 'Julio', 'Cesar', 'Fajardo', 'Segura', 20000054, 48, 'jcf54@mail.com', '1976-12-14', '2024-08-01', 'En sistema', 'Primaria', 'Masculino', 4),
(554, 'Camila', 'Fernanda', 'Olarte', 'Restrepo', 20000055, 27, 'cfo55@mail.com', '1997-04-17', '2024-08-01', 'En sistema', 'Universidad', 'Femenino', 1),
(555, 'Alfonso', 'Ricardo', 'Rueda', 'Cala', 20000056, 53, 'arr56@mail.com', '1971-08-05', '2024-08-01', 'En sistema', 'Primaria', 'Masculino', 2),
(556, 'Isabel', 'Cristina', 'Caballero', 'Bastidas', 20000057, 34, 'icc57@mail.com', '1990-10-28', '2024-08-01', 'En sistema', 'Bachillerato', 'Femenino', 3),
(557, 'Rodrigo', 'Antonio', 'Jaramillo', 'Serrano', 20000058, 41, 'raj58@mail.com', '1983-06-09', '2024-08-01', 'En sistema', 'Universidad', 'Masculino', 1),
(558, 'Victoria', 'Eugenia', 'Aguilar', 'Pardo', 20000059, 38, 'vea59@mail.com', '1986-02-14', '2024-08-01', 'En sistema', 'Bachillerato', 'Femenino', 4),
(559, 'Samuel', 'David', 'Zuluaga', 'Rengifo', 20000060, 46, 'sdz60@mail.com', '1978-11-19', '2024-08-01', 'En sistema', 'Primaria', 'Masculino', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_participante_curso`
--

CREATE TABLE `tb_participante_curso` (
  `id_participante_curso` int(11) NOT NULL,
  `id_participante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha_inscripcion` date NOT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date NOT NULL,
  `estatus_participante` enum('Pendiente','Aprobado','Reprobado','En formación','Ausente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante_curso`
--

INSERT INTO `tb_participante_curso` (`id_participante_curso`, `id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(412, 331, 76, '2024-10-18', '2024-11-04', '2024-11-18', 'Reprobado'),
(413, 332, 77, '2024-12-08', '2025-01-06', '2025-01-20', 'Aprobado'),
(414, 333, 78, '2025-01-12', '2025-02-03', '2025-02-17', 'Ausente'),
(415, 334, 79, '2025-02-18', '2025-03-10', '2025-03-24', 'Aprobado'),
(416, 335, 80, '2025-03-05', '2025-04-07', '2025-04-21', 'Reprobado'),
(447, 316, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'Pendiente'),
(448, 317, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'Pendiente'),
(449, 318, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'Pendiente'),
(450, 319, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'Pendiente'),
(451, 320, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'Pendiente'),
(452, 323, 79, '2025-05-28', '2025-03-10', '2025-03-24', 'Pendiente'),
(453, 321, 76, '2025-05-28', '2025-11-04', '2025-11-30', 'Pendiente'),
(454, 325, 76, '2025-05-28', '2025-11-04', '2025-11-30', 'Pendiente'),
(455, 331, 74, '2025-05-28', '2024-09-02', '2024-09-16', 'Pendiente'),
(456, 335, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(457, 320, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(458, 321, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(459, 322, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(460, 334, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(461, 336, 89, '2025-12-27', '2026-01-05', '2026-01-19', 'Pendiente'),
(465, 339, 91, '2026-01-07', '2026-01-08', '2026-03-06', 'En formación'),
(466, 339, 95, '2026-01-21', '2026-01-21', '2026-03-12', 'Pendiente'),
(467, 341, 95, '2026-01-21', '2026-01-21', '2026-03-12', 'Pendiente'),
(468, 343, 95, '2026-01-21', '2026-01-21', '2026-03-12', 'Pendiente'),
(469, 339, 96, '2026-01-21', '2026-01-22', '2026-02-12', 'En formación'),
(470, 341, 96, '2026-01-21', '2026-01-22', '2026-02-12', 'En formación'),
(471, 343, 96, '2026-01-21', '2026-01-22', '2026-02-12', 'En formación'),
(476, 345, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(477, 347, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(478, 348, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(479, 349, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(480, 346, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(481, 350, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'Pendiente'),
(482, 352, 113, '2026-03-27', '2026-04-06', '2026-04-24', 'Pendiente'),
(483, 351, 113, '2026-03-27', '2026-04-06', '2026-04-24', 'Pendiente'),
(600, 500, 400, '2024-01-06', '2024-01-10', '2024-04-05', 'Aprobado'),
(601, 501, 400, '2024-01-06', '2024-01-10', '2024-04-05', 'Aprobado'),
(602, 502, 400, '2024-01-06', '2024-01-10', '2024-04-05', 'Aprobado'),
(603, 503, 400, '2024-01-07', '2024-01-10', '2024-04-05', 'Aprobado'),
(604, 504, 400, '2024-01-07', '2024-01-10', '2024-04-05', 'Aprobado'),
(605, 505, 400, '2024-01-07', '2024-01-10', '2024-04-05', 'Aprobado'),
(606, 506, 400, '2024-01-08', '2024-01-10', '2024-04-05', 'Aprobado'),
(607, 507, 400, '2024-01-08', '2024-01-10', '2024-04-05', 'Aprobado'),
(608, 508, 400, '2024-01-08', '2024-01-10', '2024-04-05', 'Aprobado'),
(609, 509, 400, '2024-01-09', '2024-01-10', '2024-04-05', 'Reprobado'),
(610, 510, 400, '2024-01-09', '2024-01-10', '2024-04-05', 'Aprobado'),
(611, 511, 400, '2024-01-09', '2024-01-10', '2024-04-05', 'Aprobado'),
(612, 512, 401, '2024-05-27', '2024-06-10', '2024-09-15', 'Aprobado'),
(613, 513, 401, '2024-05-27', '2024-06-10', '2024-09-15', 'Aprobado'),
(614, 514, 401, '2024-05-28', '2024-06-10', '2024-09-15', 'Reprobado'),
(615, 515, 401, '2024-05-28', '2024-06-10', '2024-09-15', 'Aprobado'),
(616, 516, 401, '2024-05-29', '2024-06-10', '2024-09-15', 'Aprobado'),
(617, 517, 401, '2024-05-29', '2024-06-10', '2024-09-15', 'Aprobado'),
(618, 518, 401, '2024-05-30', '2024-06-10', '2024-09-15', 'Aprobado'),
(619, 519, 401, '2024-05-30', '2024-06-10', '2024-09-15', 'Aprobado'),
(620, 520, 401, '2024-05-31', '2024-06-10', '2024-09-15', 'Aprobado'),
(621, 521, 401, '2024-05-31', '2024-06-10', '2024-09-15', 'Aprobado'),
(622, 522, 401, '2024-06-01', '2024-06-10', '2024-09-15', 'Aprobado'),
(623, 523, 401, '2024-06-01', '2024-06-10', '2024-09-15', 'Reprobado'),
(624, 524, 402, '2024-02-20', '2024-03-01', '2024-05-20', 'Pendiente'),
(625, 525, 402, '2024-02-20', '2024-03-01', '2024-05-20', 'Pendiente'),
(626, 526, 402, '2024-02-21', '2024-03-01', '2024-05-20', 'Pendiente'),
(627, 527, 402, '2024-02-21', '2024-03-01', '2024-05-20', 'Pendiente'),
(628, 528, 402, '2024-02-22', '2024-03-01', '2024-05-20', 'Pendiente'),
(629, 529, 402, '2024-02-22', '2024-03-01', '2024-05-20', 'Pendiente'),
(630, 530, 402, '2024-02-23', '2024-03-01', '2024-05-20', 'Pendiente'),
(631, 531, 402, '2024-02-23', '2024-03-01', '2024-05-20', 'Pendiente'),
(632, 532, 402, '2024-02-24', '2024-03-01', '2024-05-20', 'Pendiente'),
(633, 533, 402, '2024-02-24', '2024-03-01', '2024-05-20', 'Pendiente'),
(634, 534, 402, '2024-02-25', '2024-03-01', '2024-05-20', 'Pendiente'),
(635, 535, 402, '2024-02-25', '2024-03-01', '2024-05-20', 'Pendiente'),
(636, 536, 402, '2024-02-26', '2024-03-01', '2024-05-20', 'Pendiente'),
(637, 537, 403, '2024-07-20', '2024-08-01', '2024-10-10', 'Pendiente'),
(638, 538, 403, '2024-07-20', '2024-08-01', '2024-10-10', 'Pendiente'),
(639, 539, 403, '2024-07-21', '2024-08-01', '2024-10-10', 'Pendiente'),
(640, 540, 403, '2024-07-21', '2024-08-01', '2024-10-10', 'Pendiente'),
(641, 541, 404, '2024-08-20', '2024-09-01', '2024-11-15', 'Pendiente'),
(642, 542, 404, '2024-08-20', '2024-09-01', '2024-11-15', 'Pendiente'),
(643, 543, 404, '2024-08-21', '2024-09-01', '2024-11-15', 'Pendiente'),
(644, 544, 404, '2024-08-21', '2024-09-01', '2024-11-15', 'Pendiente'),
(645, 545, 405, '2024-09-22', '2024-10-01', '2024-12-10', 'Pendiente'),
(646, 546, 405, '2024-09-22', '2024-10-01', '2024-12-10', 'Pendiente'),
(647, 547, 405, '2024-09-23', '2024-10-01', '2024-12-10', 'Pendiente'),
(648, 548, 405, '2024-09-23', '2024-10-01', '2024-12-10', 'Pendiente'),
(649, 549, 406, '2024-03-28', '2024-04-10', '2024-06-10', 'Pendiente'),
(650, 550, 406, '2024-03-28', '2024-04-10', '2024-06-10', 'Pendiente'),
(651, 551, 406, '2024-03-29', '2024-04-10', '2024-06-10', 'Pendiente'),
(652, 552, 407, '2024-10-29', '2024-11-05', '2025-01-15', 'Pendiente'),
(653, 553, 407, '2024-10-29', '2024-11-05', '2025-01-15', 'Pendiente'),
(654, 554, 407, '2024-10-30', '2024-11-05', '2025-01-15', 'Pendiente'),
(655, 555, 408, '2024-06-17', '2024-07-01', '2024-09-05', 'Pendiente'),
(656, 556, 408, '2024-06-17', '2024-07-01', '2024-09-05', 'Pendiente'),
(657, 557, 408, '2024-06-18', '2024-07-01', '2024-09-05', 'Pendiente'),
(658, 558, 409, '2024-04-20', '2024-05-02', '2024-07-20', 'Pendiente'),
(659, 559, 409, '2024-04-20', '2024-05-02', '2024-07-20', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ppl`
--

CREATE TABLE `tb_ppl` (
  `id_ppl` int(11) NOT NULL,
  `nombre_ppl` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_ppl`
--

INSERT INTO `tb_ppl` (`id_ppl`, `nombre_ppl`) VALUES
(1, 'ALBAÑIL'),
(2, 'ANALISTA DE SISTEMA'),
(3, 'ARTESANO CONSTRUCTOR DE EDIFICACIONES DE TIERRA'),
(4, 'ASISTENTE ADMINISTRATIVO'),
(5, 'ASISTENTE DE COMUNICACIÓN SOCIAL DIGITAL'),
(6, 'ASISTENTE DE CONTABILIDAD'),
(7, 'ASISTENTE DE GUIA DE TURISMO DE AVENTURA DE BAJA Y MEDIA MONTAÑA'),
(8, 'ASISTENTE DE NUTRICIÓN Y DIETÉTICA'),
(9, 'ASISTENTE DE RECREACIÓN'),
(10, 'AUXILIAR DE PROTOCOLO'),
(11, 'AUXILIAR DE RECREACIÓN'),
(12, 'AVICULTORES'),
(13, 'AYUDANTE DE ALBAÑIL'),
(14, 'AYUDANTE DE ARTESANO CONSTRUCTOR DE EDIFICACIONES DE TIERRA'),
(15, 'AYUDANTE DE COCINA'),
(16, 'AYUDANTE DE MECÁNICA AUTOMOTRIZ'),
(17, 'AYUDANTE DE SERVICIOS DE POZOS PETROLEROS'),
(18, 'BARBERO(A)'),
(19, 'BARTENDER'),
(20, 'CARPINTERO'),
(21, 'CERRAJERO'),
(22, 'COCINERO (A)'),
(23, 'CONFECCIONISTA DE ROPA INFANTIL'),
(24, 'CONFECCIONISTA DE UNIFORMES'),
(25, 'CRIADOR DE GANADO OVINO Y CAPRINO'),
(26, 'CRIADORES DE GANADO BOVINO'),
(27, 'CRIADORES DE GANADO PORCINO'),
(28, 'CULTIVADOR DE CACAO'),
(29, 'CULTOR DE CACAO'),
(30, 'CUÑERO DE SERVICIOS DE POZOS PETROLEROS'),
(31, 'DESARROLLADOR DE SITIO WEB'),
(32, 'DIAGRAMADOR(A)'),
(33, 'DISEÑADOR DE CALZADO'),
(34, 'DISEÑADOR DE SITIO WEB'),
(35, 'DISTRIBUIDOR DE GAS LICUADO DE PETRÓLEO'),
(36, 'EDITORIALISTA'),
(37, 'ELABORADOR DE BOLSOS TEJIDOS'),
(38, 'ELABORADOR DE CHINCHORROS'),
(39, 'ELABORADOR DE COJINES'),
(40, 'ELABORADOR DE HAMACAS'),
(41, 'ELABORADOR DE INSTRUMENTOS MUSICALES DE CUERDA'),
(42, 'ELECTRICISTA AUTOMOTRIZ'),
(43, 'ELECTRICISTA DE MANTENIMIENTO DEL SISTEMA DE ALUMBRADO PÚBLICO'),
(44, 'ELECTRICISTA EN REDES DE DISTRIBUCIÓN'),
(45, 'EMPRENDEDOR'),
(46, 'EMPRENDEDOR DIGITAL'),
(47, 'ENCUELLADOR DE SERVICIOS DE POZOS PETROLEROS'),
(48, 'ENSAMBLADOR DE PRODUCTOS ELECTRÓNICOS'),
(49, 'ENTRENADOR DEPORTIVO'),
(50, 'ESTETICISTA'),
(51, 'FABRICADOR DE CALZADO'),
(52, 'FOTÓGRAFO (A)'),
(53, 'GESTOR EMPRESARIAL DE TALENTO HUMANO'),
(54, 'GUIA CEREMONIAL'),
(55, 'GUIA DE TURISMO DE AVENTURA EN BAJA Y MEDIA MONTAÑA'),
(56, 'GUIA PROTOCOLO'),
(57, 'HERRERO'),
(58, 'INSPECTOR DE SEGURIDAD Y SALUD OCUPACIONAL'),
(59, 'INSTALADOR DE CONDUCTOS PARA AIRE ACONDICIONADO'),
(60, 'INSTALADOR DE EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),
(61, 'INSTALADOR DE FIBRA ÓPTICA'),
(62, 'INSTALADOR DE LÍNEAS DE ENERGÍA ELÉCTRICA AÉREAS EN REDES DE DISTRIBUCIÓN'),
(63, 'INSTALADOR DE MATERIALES DE IMPERMEABILIZACIÓN EN EDIFICACIONES'),
(64, 'INSTALADOR DE REDES Y LÍNEAS DE TELECOMUNICACIONES'),
(65, 'INSTALADOR DE REVESTIMIENTOS: PISOS, PAREDES Y TECHOS'),
(66, 'JARDINERO'),
(67, 'MAESTRO ARTESANO DE EDIFICACIONES DE TIERRA'),
(68, 'MANICURISTA/PEDICURISTA'),
(69, 'MECÁNICO DE MOTORES A GASOLINA'),
(70, 'MECÁNICO DE REFRIGERACIÓN Y CLIMATIZACIÓN'),
(71, 'MECÁNICO DE TRANSMISIÓN AUTOMOTRIZ'),
(72, 'MECÁNICO DIRECCIÓN Y FRENOS DE AUTOMÓVILES'),
(73, 'MECÁNICO MAQUINARIA TEXTIL'),
(74, 'MECÁNICO OPERADOR DE MONTACARGAS'),
(75, 'MESONERO/AZAFATA'),
(76, 'MONTADOR DE CALZADO'),
(77, 'MONTADOR PREFABRICADOS TABIQUERÍA LIGERA'),
(78, 'OBRERO DE MANTENIMIENTO DE ESPACIOS PÚBLICOS'),
(79, 'OPERADOR DE CALDERAS'),
(80, 'OPERADOR DE CARGADOR FRONTAL'),
(81, 'OPERADOR DE GRANJAS INTEGRALES'),
(82, 'OPERADOR DE MÁQUINA MOLIENDA/CEREALES'),
(83, 'OPERADOR DE MAQUINARIAS FIJAS'),
(84, 'OPERADOR DE MÁQUINAS DE CONFECCIÓN'),
(85, 'OPERADOR DE MÁQUINAS HERRAMIENTAS'),
(86, 'OPERADOR DE PLANTA DE GAS LICUADO DE PETRÓLEO'),
(87, 'ORIENTADOR(A) FAMILIAR Y GESTACIÓN'),
(88, 'PANADERO'),
(89, 'PANTRISTA'),
(90, 'PARRILLERO (A)'),
(91, 'PASTELERO'),
(92, 'PATRONISTA PRENDAS DE VESTIR'),
(93, 'PELUQUERO(A)'),
(94, 'PINTOR DE CONSTRUCCIÓN'),
(95, 'PLOMERO'),
(96, 'PROCESADOR DE CACAO'),
(97, 'PRODUCTOR AGRÍCOLA'),
(98, 'PRODUCTOR AGROPECUARIO'),
(99, 'PRODUCTOR DE CONTENIDO DIGITAL'),
(100, 'PRODUCTOR(A) AGROECOLÓGICO'),
(101, 'PROMOTOR CULTURAL'),
(102, 'PROMOTOR DE RECREACIÓN DEPORTIVA'),
(103, 'PROMOTOR DE RECREACIÓN LABORAL'),
(104, 'PROMOTOR RECREATIVO'),
(105, 'PROMOTOR(A) DE SALUD COMUNITARIA'),
(106, 'PROMOTOR(A) DE SALUD COMUNITARIA: PARTO HUMANIZADO'),
(107, 'RECEPCIONISTA DE HOTEL'),
(108, 'REDACTOR (A)'),
(109, 'REFORESTADOR DE BOSQUES'),
(110, 'REPARADOR DE MOTORES DIÉSEL'),
(111, 'REPORTERO (A) GRÁFICO'),
(112, 'REPORTERO (A) GRÁFICO DIGITAL'),
(113, 'REPOSTERO'),
(114, 'RESTAURADOR, MUEBLES DE MADERA'),
(115, 'RESTAURADOR, Y MANTENIMIENTO DE INSTRUMENTOS MUSICALES DE CUERDA'),
(116, 'SECRETARIA(O) ADMINISTRATIVA (O)'),
(117, 'SOLDADOR (A)'),
(118, 'SOLDADOR (A) ARCO ELÉCTRICO'),
(119, 'SOLDADOR (A) DE SOLDADURAS ESPECIALES'),
(120, 'SUPERVISOR DE CONTROL DE CALIDAD EN LA PRODUCCIÓN'),
(121, 'TÉCNICO EN DEMOLICIÓN Y DESACTIVACIÓN DE BOMBAS Y ARTEFACTOS EXPLOSIVOS'),
(122, 'TÉCNICO EN FABRICACIÓN DE MUEBLES'),
(123, 'TÉCNICO EN SOPORTE Y MANTENIMIENTO DE TECNOLOGÍAS DE INFORMACIÓN'),
(124, 'TÉCNICOS EN ADMINISTRACIÓN DE UNIDADES MILITARES BÁSICAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ppl_nombrecurso`
--

CREATE TABLE `tb_ppl_nombrecurso` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `id_nombrecurso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_ppl_nombrecurso`
--

INSERT INTO `tb_ppl_nombrecurso` (`id`, `id_ppl`, `id_nombrecurso`) VALUES
(21, 1, 8),
(23, 1, 24),
(22, 1, 66),
(24, 1, 67),
(25, 1, 68),
(1, 2, 5),
(2, 2, 49),
(3, 2, 50),
(4, 2, 51),
(5, 2, 52),
(6, 2, 53),
(7, 2, 54),
(8, 2, 55),
(9, 2, 56),
(26, 3, 5),
(27, 3, 6),
(28, 3, 69),
(29, 3, 70),
(30, 3, 71),
(14, 4, 30),
(10, 4, 57),
(11, 4, 58),
(12, 4, 59),
(13, 4, 60),
(15, 4, 61),
(16, 5, 6),
(17, 5, 62),
(18, 5, 63),
(19, 5, 64),
(20, 5, 65);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_sectoreconomico`
--

CREATE TABLE `tb_sectoreconomico` (
  `id_sectoreconomico` int(11) NOT NULL,
  `nombre_sectoreconomico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_sectoreconomico`
--

INSERT INTO `tb_sectoreconomico` (`id_sectoreconomico`, `nombre_sectoreconomico`) VALUES
(1, 'Comercio y Servicios'),
(2, 'Industria'),
(3, 'Investigación, Desarrollo, Innovación e Información');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_subtipo`
--

CREATE TABLE `tb_subtipo` (
  `id_subtipo` int(11) NOT NULL,
  `nombre_subtipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_subtipo`
--

INSERT INTO `tb_subtipo` (`id_subtipo`, `nombre_subtipo`) VALUES
(1, 'ESPECÍFICA/TÉCNICA'),
(2, 'TRANSVERSAL/GENÉRICA'),
(3, 'BÁSICA/COMÚN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_telefono`
--

CREATE TABLE `tb_telefono` (
  `id_telefono` int(11) NOT NULL,
  `numero_de_telefono` bigint(20) NOT NULL,
  `tipo_de_telefono` enum('Móvil','Fijo') NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_participante` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_telefono`
--

INSERT INTO `tb_telefono` (`id_telefono`, `numero_de_telefono`, `tipo_de_telefono`, `id_docente`, `id_participante`, `id_usuario`) VALUES
(1, 4121234567, 'Móvil', NULL, 316, NULL),
(2, 4149876543, 'Móvil', NULL, 317, NULL),
(3, 4241234567, 'Móvil', NULL, 318, NULL),
(4, 4161234567, 'Móvil', NULL, 319, NULL),
(5, 4121234568, 'Móvil', NULL, 320, NULL),
(6, 4261234567, 'Móvil', NULL, 321, NULL),
(7, 4131234567, 'Móvil', NULL, 322, NULL),
(8, 4171234567, 'Móvil', NULL, 323, NULL),
(9, 4241234568, 'Móvil', NULL, 324, NULL),
(10, 4159876543, 'Móvil', NULL, 325, NULL),
(11, 4121234569, 'Móvil', NULL, 326, NULL),
(12, 4261234568, 'Móvil', NULL, 327, NULL),
(13, 4141234567, 'Móvil', NULL, 328, NULL),
(14, 4161234568, 'Móvil', NULL, 329, NULL),
(15, 4241234569, 'Móvil', NULL, 330, NULL),
(16, 4171234568, 'Móvil', NULL, 331, NULL),
(17, 4131234568, 'Móvil', NULL, 332, NULL),
(18, 4261234569, 'Móvil', NULL, 333, NULL),
(19, 4121234570, 'Móvil', NULL, 334, NULL),
(20, 4241234570, 'Móvil', NULL, 335, NULL),
(32, 4121234570, 'Móvil', 11, NULL, NULL),
(33, 4149876544, 'Móvil', 12, NULL, NULL),
(34, 4241234571, 'Móvil', 13, NULL, NULL),
(35, 4161234569, 'Móvil', 14, NULL, NULL),
(36, 4261234570, 'Móvil', 15, NULL, NULL),
(37, 4131234569, 'Móvil', 16, NULL, NULL),
(38, 4171234569, 'Móvil', 17, NULL, NULL),
(39, 4241234572, 'Móvil', 18, NULL, NULL),
(40, 4121234571, 'Móvil', 19, NULL, NULL),
(41, 4159876544, 'Móvil', 20, NULL, NULL),
(42, 4248284942, 'Móvil', NULL, 336, NULL),
(43, 4162978945, 'Móvil', NULL, 339, NULL),
(45, 4162978945, 'Móvil', 21, NULL, NULL),
(46, 4248284942, 'Móvil', NULL, 341, NULL),
(47, 4248404544, 'Móvil', 22, NULL, NULL),
(48, 4248284942, 'Móvil', NULL, 343, NULL),
(49, 4248284942, 'Móvil', 23, NULL, NULL),
(50, 4121234568, 'Móvil', NULL, 345, NULL),
(51, 4121234568, 'Móvil', NULL, 346, NULL),
(52, 4147775577, 'Móvil', NULL, 347, NULL),
(53, 4121234568, 'Móvil', NULL, 348, NULL),
(54, 4121234568, 'Móvil', NULL, 349, NULL),
(55, 4121234568, 'Móvil', NULL, 350, NULL),
(56, 4121234568, 'Móvil', 24, NULL, NULL),
(57, 4127775577, 'Móvil', NULL, 351, NULL),
(58, 4164568754, 'Móvil', NULL, 352, NULL),
(59, 4167775577, 'Móvil', 25, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL,
  `primer_nombre` varchar(20) NOT NULL,
  `segundo_nombre` varchar(20) DEFAULT NULL,
  `primer_apellido` varchar(20) NOT NULL,
  `segundo_apellido` varchar(20) DEFAULT NULL,
  `nombre_usuario` varchar(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Administrador','Apoyo') NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado_usuario` enum('Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_usuario`
--

INSERT INTO `tb_usuario` (`id_usuario`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `nombre_usuario`, `correo`, `password`, `rol`, `imagen`, `estado_usuario`) VALUES
(11, 'Daniel', NULL, 'Bringtown', NULL, 'ADMIN', 'dbringtown1@gmail.com', '$2y$10$mdOsh/Pim6tsKM27z63W2e36oi4zWV42t4e7PavWmcwGR2piYs.Fi', 'Administrador', 'assets/img/avatar/avatar_680da3ed12fa2.jpg', 'Activo'),
(13, 'Jorge', 'Josmauro', 'SDDD', 'Silva', 'Gianmarino', 'dbringtown2@gmail.com', '$2y$10$1OPhWnf4z2aHIJpXO5QOAeZ6RfN0lMx/VhsWFFNL04IaUWPY5uf0W', 'Apoyo', 'assets/img/avatar/default-user.png', 'Inactivo'),
(14, 'Jose', NULL, 'Benitez', NULL, 'Jbenitez', 'dbringtown12@gmail.com', '$2y$10$1HNTuArHRCrmq9yMywGFAu8M7R65NL0PA8FnjoY8UuC.YtMKzXFHe', 'Administrador', 'assets/img/avatar/default-user.png', 'Activo'),
(15, 'Mary', NULL, 'Serrada', NULL, 'INCES2025', 'dbringtown13@gmail.com', '$2y$10$z21kNN/cWNRTBgjugD6ij.4669Z5h5PWkhEYJMMvAwOe/KerCJ2MO', 'Apoyo', 'assets/img/avatar/default-user.png', 'Activo'),
(16, 'samuel ', '', 'rivas', '', 'samu', 'example@gmail.com', '$2y$10$A/ISgZY.PQH6VFVUjTk2I.V0eiZj6zJR/3W3z1cTc9SKokSq1Wkva', 'Administrador', 'assets/img/avatar/1766934581_Gojo Satoru.jpeg', 'Activo'),
(20, 'samuel ', 'jose', 'rivas', 'rengel', 'usuario', 'examplee@gmail.com', '$2y$10$TCZMLfqr/RTDf1QvVjxgI.Bf5yWPHY0qSvfMjhbrOTyKFRfgeiFOS', 'Apoyo', 'assets/img/avatar/default-user.png', 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_ambito`
--
ALTER TABLE `tb_ambito`
  ADD PRIMARY KEY (`id_ambito`);

--
-- Indices de la tabla `tb_areaformativa`
--
ALTER TABLE `tb_areaformativa`
  ADD PRIMARY KEY (`id_areaFormativa`);

--
-- Indices de la tabla `tb_curso`
--
ALTER TABLE `tb_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_modalidad` (`id_modalidad`),
  ADD KEY `id_estatusCurso` (`id_estatusCurso`),
  ADD KEY `id_areaFormativa` (`id_areaFormativa`),
  ADD KEY `fk_curso_ambito` (`id_ambito`),
  ADD KEY `fk_curso_subtipo` (`id_subtipo`),
  ADD KEY `fk_curso_sectoreconomico` (`id_sectoreconomico`);

--
-- Indices de la tabla `tb_discapacidad`
--
ALTER TABLE `tb_discapacidad`
  ADD PRIMARY KEY (`id_discapacidad`);

--
-- Indices de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  ADD PRIMARY KEY (`id_docente_curso`),
  ADD KEY `fk_docente_curso_docente` (`id_docente`),
  ADD KEY `fk_docente_curso_curso` (`id_curso`);

--
-- Indices de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  ADD PRIMARY KEY (`id_estatusCurso`);

--
-- Indices de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `idx_tipo_actividad` (`tipo_actividad`),
  ADD KEY `idx_fecha_hora` (`fecha_hora`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_tabla_registro` (`tabla_afectada`,`id_registro`);

--
-- Indices de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  ADD PRIMARY KEY (`id_meta`),
  ADD KEY `fk_meta_objetivo` (`meta_objetivo`),
  ADD KEY `idx_metas_ambito` (`id_ambito`);

--
-- Indices de la tabla `tb_modalidad`
--
ALTER TABLE `tb_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `tb_nombrecurso`
--
ALTER TABLE `tb_nombrecurso`
  ADD PRIMARY KEY (`id_nombrecurso`);

--
-- Indices de la tabla `tb_objetivos`
--
ALTER TABLE `tb_objetivos`
  ADD PRIMARY KEY (`id_objetivo`);

--
-- Indices de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  ADD PRIMARY KEY (`id_participante`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `fk_participante_discapacidad` (`id_discapacidad`);

--
-- Indices de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  ADD PRIMARY KEY (`id_participante_curso`),
  ADD KEY `id_participante` (`id_participante`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `tb_ppl`
--
ALTER TABLE `tb_ppl`
  ADD PRIMARY KEY (`id_ppl`);

--
-- Indices de la tabla `tb_ppl_nombrecurso`
--
ALTER TABLE `tb_ppl_nombrecurso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ppl_nombrecurso` (`id_ppl`,`id_nombrecurso`),
  ADD KEY `idx_ppl` (`id_ppl`),
  ADD KEY `idx_nombrecurso` (`id_nombrecurso`);

--
-- Indices de la tabla `tb_sectoreconomico`
--
ALTER TABLE `tb_sectoreconomico`
  ADD PRIMARY KEY (`id_sectoreconomico`);

--
-- Indices de la tabla `tb_subtipo`
--
ALTER TABLE `tb_subtipo`
  ADD PRIMARY KEY (`id_subtipo`);

--
-- Indices de la tabla `tb_telefono`
--
ALTER TABLE `tb_telefono`
  ADD PRIMARY KEY (`id_telefono`),
  ADD KEY `fk_telefono_docente` (`id_docente`),
  ADD KEY `fk_telefono_participante` (`id_participante`),
  ADD KEY `fk_telefono_usuario` (`id_usuario`);

--
-- Indices de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_ambito`
--
ALTER TABLE `tb_ambito`
  MODIFY `id_ambito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_areaformativa`
--
ALTER TABLE `tb_areaformativa`
  MODIFY `id_areaFormativa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tb_curso`
--
ALTER TABLE `tb_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=410;

--
-- AUTO_INCREMENT de la tabla `tb_discapacidad`
--
ALTER TABLE `tb_discapacidad`
  MODIFY `id_discapacidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `tb_modalidad`
--
ALTER TABLE `tb_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_nombrecurso`
--
ALTER TABLE `tb_nombrecurso`
  MODIFY `id_nombrecurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `tb_objetivos`
--
ALTER TABLE `tb_objetivos`
  MODIFY `id_objetivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=560;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=660;

--
-- AUTO_INCREMENT de la tabla `tb_ppl`
--
ALTER TABLE `tb_ppl`
  MODIFY `id_ppl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT de la tabla `tb_ppl_nombrecurso`
--
ALTER TABLE `tb_ppl_nombrecurso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `tb_sectoreconomico`
--
ALTER TABLE `tb_sectoreconomico`
  MODIFY `id_sectoreconomico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_subtipo`
--
ALTER TABLE `tb_subtipo`
  MODIFY `id_subtipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_telefono`
--
ALTER TABLE `tb_telefono`
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_curso`
--
ALTER TABLE `tb_curso`
  ADD CONSTRAINT `fk_curso_ambito` FOREIGN KEY (`id_ambito`) REFERENCES `tb_ambito` (`id_ambito`),
  ADD CONSTRAINT `fk_curso_estatus` FOREIGN KEY (`id_estatusCurso`) REFERENCES `tb_estatuscurso` (`id_estatusCurso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_curso_sectoreconomico` FOREIGN KEY (`id_sectoreconomico`) REFERENCES `tb_sectoreconomico` (`id_sectoreconomico`),
  ADD CONSTRAINT `fk_curso_subtipo` FOREIGN KEY (`id_subtipo`) REFERENCES `tb_subtipo` (`id_subtipo`),
  ADD CONSTRAINT `tb_curso_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `tb_modalidad` (`id_modalidad`),
  ADD CONSTRAINT `tb_curso_ibfk_4` FOREIGN KEY (`id_areaFormativa`) REFERENCES `tb_areaformativa` (`id_areaFormativa`);

--
-- Filtros para la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  ADD CONSTRAINT `fk_metas_ambito` FOREIGN KEY (`id_ambito`) REFERENCES `tb_ambito` (`id_ambito`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  ADD CONSTRAINT `fk_participante_discapacidad` FOREIGN KEY (`id_discapacidad`) REFERENCES `tb_discapacidad` (`id_discapacidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

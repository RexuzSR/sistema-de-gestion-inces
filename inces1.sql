-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-06-2026 a las 04:18:54
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
  `preimpreso` varchar(50) DEFAULT NULL,
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

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `preimpreso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `id_sectoreconomico`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `id_ppl`, `id_nombre_especifico`, `turno`, `programa_formacion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Curso Base No Utilizable', NULL, '2025-08-01', NULL, NULL, NULL, NULL, 0.00, 30, 10, 0, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Mañana', NULL, '2026-05-25 01:46:16', '2026-05-25 01:47:50'),
(2, 'Pastelería Comercial - Grupo A', '101-2026', '2026-02-06', '2026-02-11', '2026-05-05', 111.00, 4.00, 101.00, 32, 12, 5, 2, 'Unidad Curricular', 5, 2, 2, 2, 2, 2, 2, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-02-06 13:00:00', '2026-06-21 17:08:35'),
(3, 'Mecánica Automotriz - Grupo A', '102-2026', '2026-03-01', '2026-03-06', '2026-05-08', 120.00, 4.00, 87.00, 24, 15, 4, 3, 'Unidad Curricular', 5, 3, 3, 3, 3, 3, 3, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-01 13:00:00', '2026-06-21 17:08:35'),
(4, 'Electricidad Residencial - Grupo A', '103-2026', '2026-04-08', '2026-04-13', '2026-05-14', 144.00, 4.00, 32.00, 35, 12, 5, 1, 'Unidad Curricular', 5, 1, 1, 4, 4, 4, 4, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-08 13:00:00', '2026-06-21 22:21:23'),
(5, 'Corte y Costura - Grupo A', '104-2026', '2026-05-09', '2026-05-14', '2026-07-19', 82.00, 4.00, 40.00, 30, 14, 10, 2, 'Unidad Curricular', 2, 2, 2, 5, 5, 5, 5, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-09 13:00:00', '2026-06-21 21:34:11'),
(6, 'Informática Básica - Grupo A', '105-2026', '2026-06-08', '2026-06-13', '2026-08-18', 129.00, 4.00, 0.00, 21, 14, 16, 3, 'Unidad Curricular', 3, 3, 3, 6, 1, 6, 6, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-06-08 13:00:00', '2026-06-21 21:34:11'),
(7, 'Soporte Técnico de PC - Grupo A', '106-2026', '2026-01-04', '2026-01-09', '2026-02-19', 84.00, 4.00, 90.00, 33, 13, 1, 1, 'Unidad Curricular', 5, 1, 1, 7, 2, 7, 7, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-04 13:00:00', '2026-06-21 17:08:35'),
(8, 'Contabilidad Básica - Grupo A', '107-2026', '2026-02-07', '2026-02-12', '2026-03-21', 136.00, 4.00, 111.00, 30, 11, 2, 2, 'Unidad Curricular', 5, 2, 2, 8, 3, 8, 8, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-02-07 13:00:00', '2026-06-21 17:08:35'),
(9, 'Asistente Administrativo - Grupo A', '108-2026', '2026-03-05', '2026-03-10', '2026-04-29', 145.00, 4.00, 116.00, 20, 15, 4, 3, 'Unidad Curricular', 5, 3, 3, 9, 4, 9, 9, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-05 13:00:00', '2026-06-21 17:08:35'),
(10, 'Peluquería Unisex - Grupo A', '109-2026', '2026-04-07', '2026-04-12', '2026-06-14', 124.00, 4.00, 29.00, 33, 14, 7, 1, 'Unidad Curricular', 5, 1, 1, 10, 5, 10, 10, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-07 13:00:00', '2026-06-21 22:21:23'),
(11, 'Cocina Doméstica - Grupo A', '110-2026', '2026-05-08', '2026-05-13', '2026-06-15', 115.00, 4.00, 50.00, 27, 10, 13, 2, 'Unidad Curricular', 5, 2, 2, 1, 1, 11, 11, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-08 13:00:00', '2026-06-21 21:34:11'),
(12, 'Huertos Familiares - Grupo A', '111-2026', '2026-06-09', '2026-06-14', '2026-08-20', 133.00, 4.00, 0.00, 26, 15, 20, 3, 'Unidad Curricular', 3, 3, 3, 2, 2, 12, 12, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-06-09 13:00:00', '2026-06-21 21:34:11'),
(13, 'Soldadura de Arco - Grupo A', '112-2026', '2026-01-08', '2026-01-13', '2026-02-11', 105.00, 4.00, 86.00, 24, 15, 1, 1, 'Unidad Curricular', 5, 1, 1, 3, 3, 13, 13, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-08 13:00:00', '2026-06-21 17:08:35'),
(14, 'Refrigeración Básica - Grupo A', '113-2026', '2026-02-09', '2026-02-14', '2026-04-03', 82.00, 4.00, 112.00, 25, 13, 2, 2, 'Unidad Curricular', 5, 2, 2, 4, 4, 14, 14, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-09 13:00:00', '2026-06-21 17:08:34'),
(15, 'Manicura y Pedicura - Grupo A', '114-2026', '2026-03-04', '2026-03-09', '2026-04-13', 132.00, 4.00, 106.00, 26, 13, 2, 3, 'Unidad Curricular', 5, 3, 3, 5, 5, 15, 15, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-03-04 13:00:00', '2026-06-21 17:08:35'),
(16, 'Panadería Básica I - Grupo B', '115-2026', '2026-04-02', '2026-04-07', '2026-05-19', 81.00, 4.00, 50.00, 32, 14, 12, 1, 'Unidad Curricular', 5, 1, 1, 6, 1, 16, 16, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-04-02 13:00:00', '2026-06-21 22:21:23'),
(17, 'Pastelería Comercial - Grupo B', '116-2026', '2026-05-01', '2026-05-06', '2026-06-26', 96.00, 4.00, 22.00, 33, 14, 5, 2, 'Unidad Curricular', 2, 2, 2, 7, 2, 17, 17, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-01 13:00:00', '2026-06-21 21:34:11'),
(18, 'Mecánica Automotriz - Grupo B', '117-2026', '2026-06-10', '2026-06-15', '2026-09-06', 102.00, 4.00, 0.00, 32, 13, 12, 3, 'Unidad Curricular', 2, 3, 3, 8, 3, 18, 18, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-06-10 13:00:00', '2026-06-21 21:34:11'),
(19, 'Electricidad Residencial - Grupo B', '118-2026', '2026-01-08', '2026-01-13', '2026-02-12', 88.00, 4.00, 100.00, 29, 12, 1, 1, 'Unidad Curricular', 5, 1, 1, 9, 4, 19, 19, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-08 13:00:00', '2026-06-21 17:08:35'),
(20, 'Corte y Costura - Grupo B', '119-2026', '2026-02-10', '2026-02-15', '2026-03-28', 131.00, 4.00, 80.00, 27, 14, 2, 2, 'Unidad Curricular', 5, 2, 2, 10, 5, 20, 20, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-02-10 13:00:00', '2026-06-21 17:08:35'),
(21, 'Informática Básica - Grupo B', '120-2026', '2026-03-09', '2026-03-14', '2026-04-27', 107.00, 4.00, 103.00, 21, 12, 4, 3, 'Unidad Curricular', 5, 3, 3, 1, 1, 21, 21, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-09 13:00:00', '2026-06-21 17:08:35');

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
(1, 'Manuel', '', 'Fernandez', '', 10073921, 'manuel.fernandez1@example.com', '1986-07-07', 39, 'Asignado', 'Masculino', 'Voluntario'),
(2, 'Elena', '', 'Gonzalez', '', 10147842, 'elena.gonzalez2@example.com', '1970-05-19', 56, 'Asignado', 'Femenino', 'Contratado'),
(3, 'Pedro', 'Luis', 'Ramirez', '', 10221763, 'pedro.ramirez3@example.com', '1969-09-02', 56, 'Asignado', 'Masculino', 'PDL'),
(4, 'Francisca', '', 'Ramirez', 'Flores', 10295684, 'francisca.ramirez4@example.com', '1970-04-04', 56, 'Asignado', 'Femenino', 'Fijo'),
(5, 'Miguel', '', 'Sanchez', '', 10369605, 'miguel.sanchez5@example.com', '1990-02-21', 36, 'Inactivo', 'Masculino', 'Voluntario'),
(6, 'Maria', 'Ana', 'Hernandez', '', 10443526, 'maria.hernandez6@example.com', '1965-10-05', 60, 'Disponible', 'Femenino', 'Voluntario'),
(7, 'Francisco', '', 'Sanchez', '', 10517447, 'francisco.sanchez7@example.com', '1983-08-04', 42, 'Asignado', 'Masculino', 'PDL'),
(8, 'Maria', '', 'Gonzalez', 'Hernandez', 10591368, 'maria.gonzalez8@example.com', '1973-05-08', 53, 'En formación', 'Femenino', 'PDL'),
(9, 'Francisco', 'Miguel', 'Martinez', '', 10665289, 'francisco.martinez9@example.com', '1994-04-04', 32, 'Asignado', 'Masculino', 'PDL'),
(10, 'Juana', '', 'Hernandez', '', 10739210, 'juana.hernandez10@example.com', '1987-09-01', 38, 'Asignado', 'Femenino', 'PDL'),
(11, 'Luis', '', 'Hernandez', '', 10813131, 'luis.hernandez11@example.com', '1964-11-10', 61, 'Disponible', 'Masculino', 'PDL'),
(12, 'Teresa', 'Luisa', 'Hernandez', 'Gonzalez', 10887052, 'teresa.hernandez12@example.com', '1977-05-03', 49, 'Asignado', 'Femenino', 'Contratado'),
(13, 'Miguel', '', 'Garcia', '', 10960973, 'miguel.garcia13@example.com', '1980-10-31', 45, 'Disponible', 'Masculino', 'Fijo'),
(14, 'Juana', '', 'Perez', '', 11034894, 'juana.perez14@example.com', '1981-02-08', 45, 'Disponible', 'Femenino', 'Contratado'),
(15, 'Juan', 'Francisco', 'Hernandez', '', 11108815, 'juan.hernandez15@example.com', '1992-04-06', 34, 'Asignado', 'Masculino', 'Fijo');

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
(1, 1, 2, '2026-02-06'),
(2, 2, 3, '2026-03-01'),
(3, 3, 4, '2026-04-08'),
(4, 4, 5, '2026-05-09'),
(5, 5, 6, '2026-06-08'),
(6, 6, 7, '2026-01-04'),
(7, 7, 8, '2026-02-07'),
(8, 8, 9, '2026-03-05'),
(9, 9, 10, '2026-04-07'),
(10, 10, 11, '2026-05-08'),
(11, 11, 12, '2026-06-09'),
(12, 12, 13, '2026-01-08'),
(13, 13, 14, '2026-02-09'),
(14, 14, 15, '2026-03-04'),
(15, 15, 16, '2026-04-02'),
(16, 1, 17, '2026-05-01'),
(17, 2, 18, '2026-06-10'),
(18, 3, 19, '2026-01-08'),
(19, 4, 20, '2026-02-10'),
(20, 5, 21, '2026-03-09');

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
(1, 'Crear', 'Se creó la meta: Ingresados (1200)', 'tb_metas', 7, 16, '2026-06-21 21:56:17'),
(2, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-21 22:21:23'),
(3, 'Eliminar', 'Se eliminó la meta: Ingresados (1200)', 'tb_metas', 7, 16, '2026-06-21 22:22:59'),
(4, 'Crear', 'Se creó la meta: Ingresados (212)', 'tb_metas', 8, 16, '2026-06-21 22:23:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_metas`
--

CREATE TABLE `tb_metas` (
  `id_meta` int(11) NOT NULL,
  `id_ambito` int(11) DEFAULT NULL,
  `meta_objetivo` int(11) NOT NULL,
  `estudiantes_formados` int(11) NOT NULL DEFAULT 0,
  `tipo_objetivo` enum('Aprobados','Inscritos','Ingresados','Masculino','Femenino') NOT NULL DEFAULT 'Aprobados',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Cerrada') DEFAULT 'Activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `porcentaje_cumplimiento` decimal(5,2) GENERATED ALWAYS AS (round(`estudiantes_formados` / `meta_objetivo` * 100,2)) STORED,
  `estado_cumplimiento` enum('En progreso','Cumplida') GENERATED ALWAYS AS (case when `estudiantes_formados` >= `meta_objetivo` then 'Cumplida' else 'En progreso' end) STORED,
  `ambito_id` int(11) NOT NULL DEFAULT 0,
  `periodo_tiempo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_metas`
--

INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`, `ambito_id`, `periodo_tiempo`) VALUES
(1, NULL, 60, 4, 'Aprobados', '2026-01-01', '2026-03-31', 'Cerrada', '2026-06-21 17:08:35', 0, 'Trimestre I 2026'),
(2, NULL, 80, 31, 'Inscritos', '2026-01-01', '2026-03-31', 'Cerrada', '2026-06-21 17:08:35', 0, 'Inscripciones T-I'),
(3, 1, 40, 4, 'Aprobados', '2026-04-01', '2026-06-30', 'Activa', '2026-06-21 17:08:35', 0, 'CFS Trimestre II'),
(4, 2, 30, 26, 'Inscritos', '2026-04-01', '2026-06-30', 'Activa', '2026-06-21 17:08:35', 0, 'Comunas Trimestre II'),
(5, NULL, 120, 23, 'Aprobados', '2026-01-01', '2026-06-30', 'Activa', '2026-06-21 17:08:35', 0, 'Semestre I 2026'),
(6, NULL, 200, 23, 'Aprobados', '2026-01-01', '2026-12-31', 'Activa', '2026-06-21 17:08:35', 0, 'Meta Anual 2026'),
(8, NULL, 212, 3, 'Ingresados', '2026-06-22', '2027-06-21', 'Activa', '2026-06-21 22:23:46', 0, NULL);

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
(71, 'Seguridad en Obras'),
(73, 'ARMADO DE CABILLA'),
(74, 'CÓMPUTOS MÉTRICOS'),
(75, 'ELECTRICIDAD PARA LA CONSTRUCCIÓN'),
(76, 'ENCOFRADO'),
(77, 'INSTALACIÓN DE MAMPOSTERÍA'),
(81, 'PLANOS ARQUITECTÓNICOS'),
(82, 'PLANOS DE INSTALACIONES ELÉCTRICAS'),
(83, 'PLANOS DE INSTALACIONES SANITARIAS E ISOMETRÍA'),
(84, 'PLANOS ESTRUCTURALES'),
(85, 'PLANOS TOPOGRÁFICOS'),
(86, 'PLOMERÍA'),
(87, 'PROCESO SOCIAL DE TRABAJO'),
(90, 'SISTEMAS CONSTRUCTIVOS'),
(91, 'SISTEMAS CONSTRUCTIVOS ECOLÓGICOS'),
(93, 'ANÁLISIS DE SISTEMAS'),
(94, 'ANÁLISIS ESTADÍSTICOS'),
(97, 'ESTRATEGIAS Y TÉCNICAS PARA EL ESTUDIO'),
(98, 'INTRODUCCIÓN A LA PROGRAMACIÓN'),
(99, 'LÓGICA'),
(101, 'OFIMÁTICA BAJO TECNOLOGÍAS LIBRES'),
(102, 'PENSAMIENTO CREATIVO Y PRODUCTIVO'),
(103, 'PROGRAMACIÓN ORIENTADA A OBJETOS'),
(104, 'PROGRAMACIÓN PHP'),
(105, 'SISTEMA DE INFORMACIÓN'),
(106, 'SISTEMA OPERATIVO'),
(107, 'FABRICACIÓN DE ADOBES ARTESANALES'),
(108, 'FABRICACIÓN DE LADRILLOS ARTESANALES'),
(109, 'FABRICACIÓN DE LOSETAS (BALDOSAS) ARTESANALES'),
(110, 'FABRICACIÓN DE TEJAS ARTESANALES'),
(111, 'TÉCNICAS CONSTRUCTIVAS TRADICIONALES DE EDIFICACIONES DE TIERRA'),
(112, 'ADMINISTRACIÓN DE RECURSOS HUMANOS'),
(113, 'ALMACÉN'),
(114, 'CÁLCULO MERCANTIL'),
(115, 'GESTIÓN DE EVENTOS'),
(116, 'HISTORIA CONTEMPORÁNEA DE AMÉRICA'),
(117, 'LEGISLACIÓN LABORAL'),
(118, 'MANEJO DE RECURSOS E INSTRUMENTOS FINANCIEROS'),
(119, 'MARKETING EMPRESARIAL'),
(120, 'ORATORIA'),
(121, 'ORGANIZACIÓN Y ADMINISTRACIÓN DE OFICINA'),
(122, 'FOTOGRAFÍA DIGITAL'),
(123, 'GEOGRAFÍA ECONÓMICA Y SOCIAL DE VENEZUELA'),
(124, 'MEDIOS AUDIOVISUALES (LENGUAJE Y COMUNICACIÓN)'),
(125, 'METODOLOGÍA DE LA INVESTIGACIÓN EDUCATIVA'),
(126, 'ORIENTACIÓN Y ATENCIÓN AL USUARIO'),
(127, 'POLÍTICA INTERNACIONAL CONTEMPORÁNEA'),
(128, 'SOCIOPOLÍTICA DE LA INFORMACIÓN Y COMUNICACIÓN'),
(129, 'ÉTICA Y LEGISLACIÓN PARA EL EJERCICIO DE LA CIUDADANÍA EN LA COMUNICACIÓN'),
(130, 'BALANCE DE COMPROBACIÓN CONTABLE'),
(131, 'COMUNICACIÓN ORAL Y ESCRITA'),
(132, 'CUENTAS CONTABLES'),
(133, 'CÁLCULO DE PRESTACIONES SOCIALES'),
(134, 'IMPUESTOS FISCALES Y PARAFISCALES'),
(135, 'MANEJO DE INVENTARIO'),
(136, 'MATEMÁTICA FINANCIERA'),
(137, 'PLANIFICACIÓN Y CONTROL DE COSTO'),
(138, 'PRINCIPIOS BÁSICOS DE CONTABILIDAD'),
(139, 'PROCESAMIENTO DE DOCUMENTOS MERCANTILES'),
(140, 'SOFTWARE CONTABLE'),
(141, 'CAMPAMENTO Y EXCURSIÓN'),
(142, 'DESARROLLO SUSTENTABLE Y SOSTENIBLE EN EL MOTOR TURISMO'),
(143, 'DISEÑO DE PRODUCTOS TURÍSTICOS NACIONAL E INTERNACIONAL'),
(144, 'MANEJO DE CUERDAS'),
(145, 'INGLES CON PROPÓSITOS GENERALES. NIVEL BÁSICO'),
(146, 'MANEJO DE DESECHOS Y RECURSOS APROVECHABLES'),
(147, 'NUTRICIÓN Y DIETÉTICA'),
(148, 'PROTOCOLO Y ETIQUETA'),
(149, 'SERVICIO DE COMEDOR'),
(150, 'SERVICIO DE RESTAURANTE'),
(151, 'TÉCNICAS DE GESTIÓN DE CONTROL DE ALIMENTOS Y BEBIDAS'),
(152, 'TÉCNICAS INSTRUMENTALES DE LA RECREACIÓN'),
(153, 'EXCELENCIA EN LA CALIDAD DE VENTAS'),
(154, 'RELACIONES PÚBLICAS DIGITALES'),
(155, 'SERVICIO DE BAR'),
(156, 'COMERCIALIZACIÓN DE PRODUCTOS AGRÍCOLAS'),
(157, 'CONSTITUCIÓN DE UNIDADES ECONÓMICAS'),
(158, 'CRÍA DE AVES'),
(159, 'GENERALIDADES DE LAS GRANJAS INTEGRALES'),
(160, 'ELABORACIÓN DE COMIDA RÁPIDA'),
(161, 'GESTIÓN DE SERVICIOS EN ALIMENTOS Y BEBIDAS'),
(162, 'INTRODUCCIÓN A LA COCINA'),
(163, 'SERVICIOS DE CATERING'),
(164, 'ELECTRICIDAD BÁSICA'),
(165, 'MEDIOS DE TRANSPORTE TERRESTRE'),
(166, 'REPARACIÓN DE MOTORES DE GASOLINA'),
(167, 'SISTEMA DE ENFRIAMIENTO DEL AUTOMÓVIL'),
(168, 'SISTEMA DE HIDRÁULICO DEL AUTOMÓVIL'),
(169, 'SISTEMA ELÉCTRICO DEL AUTOMÓVIL'),
(170, 'SEGURIDAD EN EL ÁREA DE LA PERFORACIÓN DE POZOS PETROLEROS'),
(171, 'SEGURIDAD'),
(172, 'HIGIENE Y PREVENCIÓN DE RIESGOS AMBIENTALES EN INSTALACIONES PETROLERAS'),
(173, 'BARBA'),
(174, 'CEJAS Y BIGOTE'),
(175, 'COLOCACIÓN DE PELUCAS'),
(176, 'POSTIZOS Y EXTENSIONES'),
(177, 'COLORIMETRIA EN LA PELUQUERÍA Y LA BARBERÍA'),
(178, 'CORTE DE CABELLO'),
(179, 'GENERALIDADES DE LA PELUQUERÍA Y LA BARBERÍA'),
(180, 'PROCESOS QUÍMICOS EN LA PELUQUERÍA Y LA BARBERÍA'),
(181, 'SECADO DE CABELLO'),
(182, 'TÉCNICA CAPILAR'),
(183, 'CARPINTERÍA BÁSICA'),
(184, 'MAQUINARIAS FIJAS EN LA CARPINTERÍA Y EBANISTERÍA'),
(185, 'METROLOGÍA DE LA CARPINTERÍA'),
(186, 'PLAN DE NEGOCIOS'),
(187, 'CORTE DE MATERIA PRIMA'),
(188, 'COCINA'),
(189, 'COCINA BÁSICA JAPONESA'),
(190, 'CORTES Y PREPARACIÓN DE CARNES'),
(191, 'ELABORACIÓN DE ENSALADAS'),
(192, 'INTRODUCCIÓN AL VINO'),
(193, 'MONTAJE Y DECORACIÓN DE BUFFET'),
(194, 'TÉCNICAS DE PREPARACIÓN DE SALSAS'),
(195, 'CONFECCIÓN DE ROPA INFANTIL'),
(196, 'CORTE DE PRENDAS DE VESTIR'),
(197, 'MECÁNICA DE MÁQUINAS DE COSER'),
(198, 'MÁQUINA RECTA'),
(199, 'PATRONAJE DE PRENDAS DE VESTIR'),
(200, 'PRODUCTOS Y MATERIALES TEXTIL'),
(201, 'REPARACIÓN DE MÁQUINAS DE COSER'),
(202, 'CONFECCIÓN DE ROPA DEPORTIVA'),
(203, 'CONFECCIÓN DE UNIFORMES'),
(204, 'CRÍA DE OVINO Y CAPRINO'),
(205, 'GANADERÍA DE DOBLE PROPÓSITO'),
(206, 'CRÍA DE PORCINO'),
(207, 'COMERCIALIZACIÓN DEL CACAO'),
(208, 'ELABORACIÓN DE BIOINSUMOS'),
(209, 'ESTABLECIMIENTO DEL VIVERO DE CACAO'),
(210, 'INTRODUCCIÓN AL MUNDO DEL CACAO'),
(211, 'MANEJO AGROECOLÓGICO DEL CACAO'),
(212, 'MANEJO INTEGRADO DE PLAGAS Y ENFERMEDADES EN CULTIVOS DE CACAO'),
(213, 'PROCESAMIENTO PRIMARIO DEL CACAO'),
(214, 'BEBIDAS A BASE DE CACAO'),
(215, 'CHOCOLATERÍA'),
(216, 'USO TERAPÉUTICO Y COSMÉTICO DEL CACAO'),
(217, 'CONTROL DE SÓLIDOS'),
(218, 'FUNDAMENTOS BÁSICOS DE LA INDUSTRIA PETROLERA'),
(219, 'FUNDAMENTOS DE CÁLCULO PARA LA PERFORACIÓN DE POZOS PETROLEROS'),
(220, 'PRINCIPIOS BÁSICOS DE QUÍMICA'),
(221, 'GUION GRÁFICO'),
(222, 'SISTEMA OPERATIVO DE REDES'),
(223, 'EDICIÓN DE PÁGINAS WEB'),
(224, 'GOBIERNO ELECTRÓNICO'),
(225, 'CORTE DE CALZADO'),
(226, 'COSTURA DE CALZADO'),
(227, 'ELABORACIÓN DE BOCETOS DE CALZADO'),
(228, 'ELABORACIÓN DE PATRÓN BASE DE CALZADO'),
(229, 'ELABORACIÓN DE PATRÓN BASE EN METAL'),
(230, 'ESCALADA DE PATRONES DE CALZADO'),
(231, 'FUNDAMENTOS BÁSICOS DEL CALZADO'),
(232, 'DIBUJO ANATÓMICO'),
(233, 'EDICIÓN DIGITAL'),
(234, 'MODELACIÓN Y ANIMACIÓN 2D/3D'),
(235, 'PROGRAMA DE EDICIÓN DE IMAGEN'),
(236, 'PROGRAMACIÓN DE VIDEOS'),
(237, 'PROGRAMACIÓN HTML'),
(238, 'RECURSOS Y MEDIOS DE INFORMACIÓN DIGITAL'),
(239, 'SONIDO PARA WEB'),
(240, 'DISTRIBUCIÓN DE GAS LICUADO DE PETRÓLEO'),
(241, 'GENERALIDADES DE GAS LICUADO DE PETRÓLEO'),
(242, 'INSTALACIÓN DE SISTEMAS DE GAS LICUADO DE PETRÓLEO'),
(243, 'LEGISLACIÓN DE GAS LICUADO DE PETRÓLEO'),
(244, 'MANIPULACIÓN Y ALMACENAMIENTO DE GAS LICUADO DE PETRÓLEO'),
(245, 'MÁQUINAS HERRAMIENTAS Y MATERIALES PARA INSTALACIÓN DE G.L.P'),
(246, 'SEGURIDAD PARA EL ÁREA DE GAS LICUADO DE PETRÓLEO'),
(247, 'ADMINISTRACIÓN DE REDES SOCIALES'),
(248, 'ADMINISTRACIÓN DE LA PEQUEÑA Y MEDIANA EMPRESA'),
(249, 'CONFECCIÓN DE BOLSOS TEJIDOS'),
(250, 'DESARROLLO HUMANO'),
(251, 'ELABORACIÓN DE CHINCHORROS'),
(252, 'CONFECCIÓN DE COJINES EN TEXTILES Y TEJIDOS'),
(253, 'ELABORACIÓN DE HAMACAS'),
(254, 'ANATOMÍA DE LA MADERA'),
(255, 'ELABORADOR DE ARPA'),
(256, 'ELABORADOR DE BANDOLA'),
(257, 'ELABORADOR DE CUATRO'),
(258, 'ELABORADOR DE GUITARRA'),
(259, 'ELABORADOR DE MANDOLINA'),
(260, 'SISTEMA ELECTRÓNICO DEL AUTOMÓVIL'),
(261, 'INSTALACIONES AÉREAS DE RED DE DISTRIBUCIÓN I'),
(262, 'INSTALACIONES AÉREAS DE RED DE DISTRIBUCIÓN II'),
(263, 'LECTURA E INTERPRETACIÓN DE PLANOS DE LA RED ELÉCTRICA DE DISTRIBUCIÓN'),
(264, 'NORMAS TÉCNICAS DE LA RED DE DISTRIBUCIÓN DEL SISTEMA ELÉCTRICO NACIONAL'),
(265, 'EQUIPOS DE MEDICIÓN DE ENERGÍA ELÉCTRICA'),
(266, 'ELABORACIÓN Y FORMULACIÓN DE PROYECTOS SOCIOPRODUCTIVOS'),
(267, 'GESTIÓN DE COSTOS'),
(268, 'PLANIFICACIÓN Y CONTROL DE PRODUCCIÓN'),
(269, 'PUBLICIDAD Y MERCADEO'),
(270, 'DISPOSITIVOS HERRAMIENTAS Y APLICACIONES DE LA COMUNICACIÓN DIGITAL'),
(271, 'GERENCIA DE PROYECTOS PARA EMPRENDEDORES'),
(272, 'LIDERAZGO'),
(273, 'MARKETING'),
(274, 'MARKETING DIGITAL'),
(275, 'FUNDAMENTOS DE LA PERFORACIÓN DE POZOS PETROLEROS'),
(276, 'MANTENIMIENTO DE FLUIDOS DE PERFORACIÓN'),
(277, 'OPERACIONES DEL ENCUELLADOR'),
(278, 'OPERACIONES EN LA PLANCHADA DEL TALADRO'),
(279, 'CIRCUITOS ELECTRÓNICOS'),
(280, 'CIRCUITOS ELÉCTRICOS'),
(281, 'ELECTRÓNICA DIGITAL'),
(282, 'FUNDAMENTOS DE NEUMÁTICA'),
(283, 'FUNDAMENTOS DEL SOFTWARE LIBRE'),
(284, 'FÍSICA BÁSICA'),
(285, 'LECTURA E INTERPRETACIÓN DE DIAGRAMA DE CABLEADOS ELECTRÓNICOS'),
(286, 'DEPILACIÓN Y EPILACIÓN'),
(287, 'DISEÑO DE CEJAS'),
(288, 'EXTENSIÓN DE PESTAÑAS'),
(289, 'GENERALIDADES DE LA ESTÉTICA'),
(290, 'HIGIENE DE LA PIEL'),
(291, 'MANEJO DE CÁMARA'),
(292, 'ESTRATEGIAS Y PRÁCTICAS GERENCIALES'),
(293, 'HERRAMIENTAS INTEGRADORAS PARA LA RESOLUCIÓN DE CONFLICTOS'),
(294, 'LIDERAZGO COMO HERRAMIENTA TRANSFORMADORA'),
(295, 'CEREMONIAL'),
(296, 'PROTOCOLO'),
(297, 'AUXILIO MÉDICO DE EMERGENCIA EN BAJA Y MEDIA MONTAÑA'),
(298, 'GESTIÓN DE RIESGO EN BAJA Y MEDIA MONTAÑA'),
(299, 'MANEJO Y GUIATURA DE GRUPO'),
(300, 'SUPERVIVENCIA EN BAJA Y MEDIA MONTAÑA'),
(301, 'TÉCNICAS DE ORIENTACIÓN Y CARTOGRAFÍA'),
(302, 'BÁSICO DE HERRERÍA'),
(303, 'ELABORACIÓN DE PUERTAS Y VENTANAS METÁLICAS'),
(304, 'PRIMEROS AUXILIOS'),
(305, 'SEGURIDAD OCUPACIONAL'),
(306, 'FUNDAMENTOS DE TUBERÍAS EN EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),
(307, 'HERRAMIENTAS E INSTRUMENTOS DE MEDICIÓN'),
(308, 'INSTALACIÓN DE TUBERÍAS EN EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),
(309, 'MANTENIMIENTO Y DIAGNÓSTICO EN REFRIGERACIÓN Y CLIMATIZACIÓN'),
(310, 'PRINCIPIOS DE REFRIGERACIÓN'),
(311, 'SOLDADURA DE REFRIGERACIÓN'),
(312, 'CABLES Y HERRAMIENTAS PARA EMPALME DE FIBRA ÓPTICA'),
(313, 'CERTIFICACIÓN DE INSTALACIÓN'),
(314, 'PRINCIPIOS BÁSICOS DE FIBRA ÓPTICA'),
(315, 'IMPERMEABILIZACIÓN DE EDIFICACIONES'),
(316, 'INSTALACIÓN DE ANTENAS SATELITALES FTA'),
(317, 'INSTALACIÓN Y CONFIGURACIÓN DE TECNOLOGÍA INALÁMBRICA WIFI'),
(318, 'REDACCIÓN DE INFORMES TÉCNICOS'),
(319, 'SISTEMA DE TELECOMUNICACIONES'),
(320, 'REVESTIMIENTOS DE PISOS PAREDES Y TECHOS'),
(321, 'JARDINERÍA'),
(322, 'JARDINERÍA PRODUCTIVA SOSTENIBLE'),
(323, 'CARPINTERÍA DE EDIFICACIONES TRADICIONALES'),
(324, 'MANICURA'),
(325, 'PEDICURA'),
(326, 'SISTEMA BÁSICO DE ACRÍLICO'),
(327, 'SISTEMA DE FRENOS DEL AUTOMÓVIL'),
(328, 'CIRCUITOS ELÉCTRICOS PARA REFRIGERACIÓN Y CLIMATIZACIÓN'),
(329, 'NOCIONES DE GEOMETRÍA'),
(330, 'SISTEMAS DE REFRIGERACIÓN Y CLIMATIZACIÓN'),
(331, 'USO DE REFRIGERANTES Y LUBRICANTES'),
(332, 'SISTEMA DE TRANSMISIÓN DEL AUTOMÓVIL'),
(333, 'SISTEMA DE DIRECCIÓN DEL AUTOMÓVIL'),
(334, 'SISTEMA DE SUSPENSIÓN AUTOMOTRIZ'),
(335, 'REPARACIÓN DE MOTORES DIÉSEL'),
(336, 'INSTALACIÓN DE TABIQUERÍA Y DRY WALL'),
(337, 'PINTURA DE OBRAS'),
(338, 'SISTEMAS DE MANTENIMIENTO DE ESPACIOS PÚBLICOS'),
(339, 'COMBUSTIÓN PARA OPERACIONES DE CALDERAS'),
(340, 'CONTROLES ELÉCTRICOS'),
(341, 'FUNDAMENTOS BÁSICOS EN OPERACIÓN DE CALDERAS'),
(342, 'INSTRUMENTACIÓN DE CALDERAS'),
(343, 'MANTENIMIENTO GENERAL DE LAS CALDERAS'),
(344, 'PROCESO DE LA COMBUSTIÓN EN CALDERAS'),
(345, 'TRATAMIENTOS QUÍMICOS DEL AGUA PARA OPERACIONES EN CALDERAS'),
(346, 'TRATAMIENTOS TÉRMICOS'),
(347, 'MANTENIMIENTO PREVENTIVO DEL CARGADOR FRONTAL'),
(348, 'TÉCNICAS DE MANEJO DEFENSIVO'),
(349, 'TÉCNICAS DE OPERACIÓN DEL CARGADOR FRONTAL'),
(350, 'CULTIVO DE HORTALIZAS EN HUERTOS'),
(351, 'CULTIVOS DE ESPECIES DE CICLOS CORTOS'),
(352, 'CONTROL NUMÉRICO COMPUTARIZADO'),
(353, 'MANTENIMIENTO DE MAQUINARIAS FIJA'),
(354, 'BÁSICO DE INSTRUMENTACIÓN PARA EL PROCESAMIENTO DE CEREALES'),
(355, 'CONTROLES Y PROCESOS EN LA RECEPCIÓN Y ALMACENAMIENTO DEL GRANO DE MAÍZ'),
(356, 'DESGERMINACIÓN DEL GRANO DE MAÍZ'),
(357, 'LAMINACIÓN DEL ENDOSPERMO'),
(358, 'MAQUINAS Y MECANISMOS DEL PROCESAMIENTO DE CEREALES'),
(359, 'MOLIENDA DE LA HOJUELA'),
(360, 'AFILADO DE HERRAMIENTAS DE CORTE'),
(361, 'AJUSTE MECÁNICO'),
(362, 'LECTURA E INTERPRETACIÓN DE PLANOS DE PIEZAS MECÁNICAS'),
(363, 'METROLOGÍA'),
(364, 'SOLDADURA BÁSICA'),
(365, 'INSTALACIÓN DE CILINDROS DE GAS LICUADO DE PETRÓLEO'),
(366, 'INSTALACIÓN DE PLANTA DE GAS LICUADO DE PETROLEO'),
(367, 'REPARACIÓN DE CILINDROS DE GAS LICUADO DE PETRÓLEO'),
(368, 'COMPROMISO COMUNAL DE LA PROMOTORA POR EL PARTO Y NACIMIENTO HUMANIZADO'),
(369, 'CRIANZA RESPETUOSA'),
(370, 'PLANIFICACIÓN FAMILIAR'),
(371, 'PROCESO DE GESTACIÓN PARTO HUMANIZADO Y NACIMIENTO PUERPERIO'),
(372, 'TRABAJO COMUNITARIO'),
(373, 'ÉTICA PARA LA VIDA'),
(374, 'AMASADOR DE PAN'),
(375, 'CONSERVACIÓN DE ALIMENTOS'),
(376, 'ELABORACIÓN DE PANES'),
(377, 'ELABORACIÓN DE PRODUCTOS ALIMENTICIOS SIN GLUTEN'),
(378, 'MASAS BÁSICAS DE LA PASTELERÍA'),
(379, 'PANADERÍA'),
(380, 'PANADERÍA ARTESANAL'),
(381, 'TÉCNICA DE PROCESAMIENTO DE LEGUMINOSAS TUBÉRCULOS Y CEREALES'),
(382, 'TÉCNICAS DE PROCESAMIENTO DE PRODUCTOS DE BOLLERÍA DE HARINAS ALTERNATIVAS'),
(383, 'INTRODUCCIÓN AL CONOCIMIENTO CIENTÍFICO'),
(384, 'PASTELERÍA Y REPOSTERÍA'),
(385, 'COLOCACIÓN DE PELUCAS POSTIZOS Y EXTENSIONES'),
(386, 'PEINADOS MOÑOS Y TRENZADOS'),
(387, 'PROCESAMIENTO SECUNDARIO DEL CACAO'),
(388, 'CULTIVADOR DE ARBOLES FRUTALES'),
(389, 'CUNICULTURA'),
(390, 'APLICACIONES MÓVILES'),
(391, 'TEORÍA DE LA COMUNICACIÓN'),
(392, 'ENTOMOLOGÍA AGRÍCOLA'),
(393, 'INTRODUCCIÓN A LA AGROECOLOGÍA'),
(394, 'MANEJO AGROECOLÓGICO DE SUELOS'),
(395, 'MANEJO DE ENFERMEDADES Y ENEMIGOS NATURALES DE LOS CULTIVOS'),
(396, 'PRODUCCIÓN AGROECOLÓGICA DE SEMILLAS'),
(397, 'PRODUCCIÓN DE BIOINSUMOS AGROECOLÓGICOS'),
(398, 'SISTEMAS AGROFORESTALES'),
(399, 'DISEÑO Y EVALUACIÓN DE PROGRAMAS RECREATIVOS'),
(400, 'ANÁLISIS DE SITUACIÓN DE SALUD'),
(401, 'CURSO DE VIDA DE LA MUJER'),
(402, 'SALUD COMUNITARIA'),
(403, 'IMAGEN PERSONAL Y PROFESIONAL DEL PRESTADOR DE SERVICIO HOTELERO'),
(404, 'INGLES TÉCNICO OCUPACIONAL ATENCIÓN AL CLIENTE'),
(405, 'INGLÉS PARA PERSONAL DE RECEPCIÓN DE HOTEL'),
(406, 'SERVICIOS DEL PERSONAL DE RECEPCIÓN DE HOTEL'),
(407, 'INSTALACIÓN DE VIVERO FORESTAL'),
(408, 'INVENTARIO FORESTAL'),
(409, 'LEGISLACIÓN Y POLÍTICAS PÚBLICAS EN REFORESTACIÓN'),
(410, 'NOCIONES DE BOTÁNICA FORESTAL'),
(411, 'REFORESTACIÓN Y PROCESOS'),
(412, 'GENERALIDADES DEL MOTOR DIÉSEL'),
(413, 'REPARACIÓN DEL SISTEMA CONJUNTO MÓVIL DEL MOTOR DIÉSEL'),
(414, 'REPARACIÓN DEL SISTEMA DE ALIMENTACIÓN DE AIRE DEL MOTOR DIÉSEL'),
(415, 'REPARACIÓN DEL SISTEMA DE COMBUSTIBLE DEL MOTOR DIÉSEL'),
(416, 'REPARACIÓN DEL SISTEMA DE DISTRIBUCIÓN DE MOTOR DIÉSEL'),
(417, 'REPARACIÓN DEL SISTEMA DE INYECCIÓN DEL MOTOR DIÉSEL'),
(418, 'REPARACIÓN DEL SISTEMA DE LUBRICACIÓN DEL MOTOR DIÉSEL'),
(419, 'REPARACIÓN DEL SISTEMA DEL ENFRIAMIENTO DEL MOTOR DIÉSEL'),
(420, 'REPARACIÓN DEL SISTEMA ELÉCTRICO DEL MOTOR DIÉSEL'),
(421, 'ILUMINACIÓN'),
(422, 'APLICACIÓN DE TINTES EN MUEBLES'),
(423, 'CREACIÓN DE UN TALLER DE RESTAURACIÓN'),
(424, 'PREPARACIÓN Y APLICACIÓN CON CERAS EN MUEBLES'),
(425, 'RECUPERACIÓN VOLUMÉTRICA CON MASILLA'),
(426, 'RESTAURAR CERRADURAS Y HERRAJES EN LA MADERA'),
(427, 'SUSTITUCIÓN DE ELEMENTOS ESTROPEADOS Y ROTOS'),
(428, 'TRATAMIENTO CONTRA LA CARCOMA'),
(429, 'TÉCNICAS DE RESTAURACIÓN'),
(430, 'GENERALIDADES CONCEPTUALES SOBRE LOS INSTRUMENTOS MUSICALES DE CUERDA'),
(431, 'HISTORIA Y EVOLUCIÓN DE LOS INSTRUMENTOS MUSICALES DE CUERDA'),
(432, 'REPARACIÓN Y MANTENIMIENTO DE LOS INSTRUMENTOS MUSICALES DE CUERDA'),
(433, 'RESTAURACIÓN DE INSTRUMENTOS MUSICALES DE CUERDA'),
(434, 'LECTURA E INTERPRETACIÓN DE PLANOS DE SOLDADURA'),
(435, 'NOCIONES DE METALURGIA'),
(436, 'SOLDADURA OXIACETILÉNICA'),
(437, 'TRAZADO DE MATERIAL METÁLICO'),
(438, 'INSPECCIONES DE SOLDADURA (PRUEBAS Y ENSAYOS)'),
(439, 'PULITURA DE METALES EN ACERO INOXIDABLE'),
(440, 'SOLDADURA ALUMINOTÉRMICA'),
(441, 'SOLDADURA MIG MAG'),
(442, 'SOLDADURA TIG'),
(443, 'MANTENIMIENTO DE COMPUTADORAS'),
(444, 'SISTEMA DE CONTROL DE CALIDAD'),
(445, 'ELABORACIÓN DE ATAÚD DE MADERA'),
(446, 'ELABORACIÓN DE CAMAS DE MADERA'),
(447, 'ELABORACIÓN DE PUERTAS Y VENTANAS DE MADERA'),
(448, 'ELABORACIÓN MESAS Y SILLA DE MADERA'),
(449, 'SOPORTE TÉCNICO'),
(450, 'LEGISLACIÓN MILITAR'),
(451, 'REDACCIÓN Y DOCUMENTACIÓN MILITAR'),
(452, 'SIMBOLOGÍA MILITAR');

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
  `estado` enum('En formacion','Inactivo','Disponible','Asignado','En sistema','Diferido') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `id_discapacidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
(1, 'Rosa', '', 'Gomez', '', 20061729, 53, 'rosa.gomez1@students.com', '1972-09-17', '2026-02-13', 'En formacion', 'Bachillerato', 'Femenino', 1),
(2, 'Juana', '', 'Martinez', '', 20123458, 51, 'juana.martinez2@students.com', '1974-07-16', '2026-03-11', 'En formacion', 'Bachillerato', 'Femenino', 1),
(3, 'Miguel', '', 'Torres', 'Fernandez', 20185187, 35, 'miguel.torres3@students.com', '1990-11-07', '2026-04-06', 'En formacion', 'Primaria', 'Masculino', 1),
(4, 'Isabel', '', 'Hernandez', '', 20246916, 52, 'isabel.hernandez4@students.com', '1973-11-25', '2026-05-20', 'En formacion', 'Universidad', 'Femenino', 1),
(5, 'Luisa', 'Ana', 'Hernandez', '', 20308645, 43, 'luisa.hernandez5@students.com', '1983-03-08', '2026-06-21', 'Disponible', 'Universidad', 'Femenino', 1),
(6, 'Miguel', '', 'Sanchez', 'Ramirez', 20370374, 48, 'miguel.sanchez6@students.com', '1978-03-14', '2026-01-28', 'Disponible', 'Primaria', 'Masculino', 1),
(7, 'Teresa', '', 'Ramirez', '', 20432103, 53, 'teresa.ramirez7@students.com', '1972-11-24', '2026-02-10', 'En formacion', 'Universidad', 'Femenino', 1),
(8, 'Elena', '', 'Perez', '', 20493832, 34, 'elena.perez8@students.com', '1991-09-30', '2026-03-11', 'En formacion', 'Universidad', 'Femenino', 1),
(9, 'Francisco', '', 'Martinez', 'Perez', 20555561, 54, 'francisco.martinez9@students.com', '1971-10-15', '2026-04-18', 'En formacion', 'Universidad', 'Masculino', 1),
(10, 'Elena', 'Carmen', 'Garcia', '', 20617290, 16, 'elena.garcia10@students.com', '2009-12-01', '2026-05-18', 'En formacion', 'Universidad', 'Femenino', 1),
(11, 'Juana', '', 'Diaz', '', 20679019, 42, 'juana.diaz11@students.com', '1983-07-09', '2026-06-20', 'En formacion', 'Primaria', 'Femenino', 1),
(12, 'Luis', '', 'Martinez', 'Hernandez', 20740748, 64, 'luis.martinez12@students.com', '1961-07-09', '2026-01-12', 'Disponible', 'Primaria', 'Masculino', 1),
(13, 'Francisca', '', 'Lopez', '', 20802477, 37, 'francisca.lopez13@students.com', '1988-12-05', '2026-02-03', 'Disponible', 'Bachillerato', 'Femenino', 1),
(14, 'Rosa', '', 'Sanchez', '', 20864206, 48, 'rosa.sanchez14@students.com', '1978-04-02', '2026-03-22', 'En formacion', 'Bachillerato', 'Femenino', 1),
(15, 'Ramón', 'Rafael', 'Flores', 'Diaz', 20925935, 41, 'ramón.flores15@students.com', '1985-01-28', '2026-04-18', 'En formacion', 'Primaria', 'Masculino', 3),
(16, 'Luisa', '', 'Torres', '', 20987664, 32, 'luisa.torres16@students.com', '1993-06-25', '2026-05-24', 'Inactivo', 'Primaria', 'Femenino', 1),
(17, 'Maria', '', 'Gonzalez', '', 21049393, 53, 'maria.gonzalez17@students.com', '1972-11-07', '2026-06-16', 'Diferido', 'Primaria', 'Femenino', 1),
(18, 'Pedro', '', 'Diaz', 'Sanchez', 21111122, 51, 'pedro.diaz18@students.com', '1974-07-23', '2026-01-23', 'Asignado', 'Primaria', 'Masculino', 1),
(19, 'Teresa', '', 'Sanchez', '', 21172851, 51, 'teresa.sanchez19@students.com', '1974-12-20', '2026-02-28', 'En formacion', 'Bachillerato', 'Femenino', 1),
(20, 'Ana', 'Luisa', 'Hernandez', '', 21234580, 15, 'ana.hernandez20@students.com', '2011-04-29', '2026-03-17', 'Asignado', 'Bachillerato', 'Femenino', 1),
(21, 'Manuel', '', 'Gomez', 'Garcia', 21296309, 36, 'manuel.gomez21@students.com', '1990-01-27', '2026-04-05', 'Asignado', 'Primaria', 'Masculino', 1),
(22, 'Elena', '', 'Lopez', '', 21358038, 37, 'elena.lopez22@students.com', '1989-01-12', '2026-05-19', 'Inactivo', 'Universidad', 'Femenino', 1),
(23, 'Elena', '', 'Fernandez', '', 21419767, 26, 'elena.fernandez23@students.com', '1999-10-29', '2026-06-03', 'En formacion', 'Universidad', 'Femenino', 1),
(24, 'Carlos', '', 'Gomez', 'Rodriguez', 21481496, 68, 'carlos.gomez24@students.com', '1958-02-28', '2026-01-18', 'Asignado', 'Bachillerato', 'Masculino', 1),
(25, 'Elena', 'Ana', 'Gonzalez', '', 21543225, 40, 'elena.gonzalez25@students.com', '1986-04-21', '2026-02-20', 'Disponible', 'Primaria', 'Femenino', 1),
(26, 'Rosa', '', 'Hernandez', '', 21604954, 24, 'rosa.hernandez26@students.com', '2002-02-16', '2026-03-16', 'En formacion', 'Universidad', 'Femenino', 1),
(27, 'Manuel', '', 'Martinez', 'Rodriguez', 21666683, 50, 'manuel.martinez27@students.com', '1976-01-25', '2026-04-09', 'En formacion', 'Primaria', 'Masculino', 1),
(28, 'Isabel', '', 'Hernandez', '', 21728412, 46, 'isabel.hernandez28@students.com', '1979-08-03', '2026-05-22', 'En formacion', 'Primaria', 'Femenino', 1),
(29, 'Maria', '', 'Garcia', '', 21790141, 52, 'maria.garcia29@students.com', '1974-05-27', '2026-06-23', 'En formacion', 'Bachillerato', 'Femenino', 1),
(30, 'Carlos', 'Ramón', 'Hernandez', 'Hernandez', 21851870, 15, 'carlos.hernandez30@students.com', '2010-09-07', '2026-01-10', 'Asignado', 'Primaria', 'Masculino', 4),
(31, 'Ana', '', 'Flores', '', 21913599, 26, 'ana.flores31@students.com', '2000-01-26', '2026-02-22', 'En formacion', 'Universidad', 'Femenino', 1),
(32, 'Francisca', '', 'Gomez', '', 21975328, 39, 'francisca.gomez32@students.com', '1986-09-30', '2026-03-10', 'En formacion', 'Universidad', 'Femenino', 1),
(33, 'Francisco', '', 'Hernandez', 'Ramirez', 22037057, 40, 'francisco.hernandez33@students.com', '1985-07-30', '2026-04-21', 'En formacion', 'Primaria', 'Masculino', 1),
(34, 'Juana', '', 'Gonzalez', '', 22098786, 51, 'juana.gonzalez34@students.com', '1974-10-03', '2026-05-23', 'En formacion', 'Primaria', 'Femenino', 1),
(35, 'Rosa', 'Francisca', 'Lopez', '', 22160515, 49, 'rosa.lopez35@students.com', '1976-08-17', '2026-06-25', 'En formacion', 'Bachillerato', 'Femenino', 1),
(36, 'Manuel', '', 'Torres', 'Sanchez', 22222244, 67, 'manuel.torres36@students.com', '1958-08-16', '2026-01-03', 'Disponible', 'Bachillerato', 'Masculino', 1),
(37, 'Carmen', '', 'Garcia', '', 22283973, 37, 'carmen.garcia37@students.com', '1988-11-09', '2026-02-04', 'En formacion', 'Primaria', 'Femenino', 1),
(38, 'Juana', '', 'Hernandez', '', 22345702, 46, 'juana.hernandez38@students.com', '1980-04-18', '2026-03-16', 'En formacion', 'Universidad', 'Femenino', 1),
(39, 'Carlos', '', 'Ramirez', 'Garcia', 22407431, 45, 'carlos.ramirez39@students.com', '1981-02-16', '2026-04-17', 'En formacion', 'Primaria', 'Masculino', 1),
(40, 'Francisca', 'Ana', 'Diaz', '', 22469160, 15, 'francisca.diaz40@students.com', '2010-08-08', '2026-05-19', 'Diferido', 'Universidad', 'Femenino', 1),
(41, 'Ana', '', 'Gomez', '', 22530889, 42, 'ana.gomez41@students.com', '1983-12-09', '2026-06-17', 'Asignado', 'Universidad', 'Femenino', 1),
(42, 'Francisco', '', 'Sanchez', 'Hernandez', 22592618, 18, 'francisco.sanchez42@students.com', '2007-11-01', '2026-01-25', 'Disponible', 'Primaria', 'Masculino', 1),
(43, 'Luisa', '', 'Gomez', '', 22654347, 26, 'luisa.gomez43@students.com', '1999-10-16', '2026-02-12', 'Asignado', 'Universidad', 'Femenino', 1),
(44, 'Elena', '', 'Hernandez', '', 22716076, 33, 'elena.hernandez44@students.com', '1992-09-19', '2026-03-14', 'En formacion', 'Bachillerato', 'Femenino', 1),
(45, 'Pedro', 'Miguel', 'Rodriguez', 'Sanchez', 22777805, 21, 'pedro.rodriguez45@students.com', '2005-03-18', '2026-04-05', 'En formacion', 'Bachillerato', 'Masculino', 4),
(46, 'Rosa', '', 'Garcia', '', 22839534, 33, 'rosa.garcia46@students.com', '1993-01-23', '2026-05-03', 'En formacion', 'Universidad', 'Femenino', 1),
(47, 'Isabel', '', 'Rodriguez', '', 22901263, 52, 'isabel.rodriguez47@students.com', '1974-01-27', '2026-06-23', 'Disponible', 'Bachillerato', 'Femenino', 1),
(48, 'Carlos', '', 'Flores', 'Ramirez', 22962992, 61, 'carlos.flores48@students.com', '1965-06-05', '2026-01-12', 'Disponible', 'Primaria', 'Masculino', 1),
(49, 'Ana', '', 'Gomez', '', 23024721, 49, 'ana.gomez49@students.com', '1977-02-03', '2026-02-24', 'En formacion', 'Universidad', 'Femenino', 1),
(50, 'Elena', 'Isabel', 'Rodriguez', '', 23086450, 17, 'elena.rodriguez50@students.com', '2008-07-30', '2026-03-21', 'Asignado', 'Bachillerato', 'Femenino', 1),
(51, 'Francisco', '', 'Perez', 'Flores', 23148179, 39, 'francisco.perez51@students.com', '1986-10-26', '2026-04-23', 'En formacion', 'Universidad', 'Masculino', 1),
(52, 'Maria', '', 'Ramirez', '', 23209908, 34, 'maria.ramirez52@students.com', '1991-10-07', '2026-05-06', 'En formacion', 'Universidad', 'Femenino', 1),
(53, 'Teresa', '', 'Gonzalez', '', 23271637, 41, 'teresa.gonzalez53@students.com', '1985-02-12', '2026-06-19', 'Inactivo', 'Bachillerato', 'Femenino', 1),
(54, 'Manuel', '', 'Sanchez', 'Gomez', 23333366, 41, 'manuel.sanchez54@students.com', '1985-06-06', '2026-01-19', 'Disponible', 'Primaria', 'Masculino', 1),
(55, 'Francisca', 'Carmen', 'Gonzalez', '', 23395095, 52, 'francisca.gonzalez55@students.com', '1973-07-17', '2026-02-24', 'Disponible', 'Bachillerato', 'Femenino', 1),
(56, 'Rosa', '', 'Rodriguez', '', 23456824, 51, 'rosa.rodriguez56@students.com', '1975-04-19', '2026-03-10', 'En formacion', 'Universidad', 'Femenino', 1),
(57, 'Miguel', '', 'Gonzalez', 'Torres', 23518553, 41, 'miguel.gonzalez57@students.com', '1985-01-20', '2026-04-10', 'En formacion', 'Universidad', 'Masculino', 1),
(58, 'Juana', '', 'Martinez', '', 23580282, 49, 'juana.martinez58@students.com', '1977-06-14', '2026-05-25', 'En formacion', 'Primaria', 'Femenino', 1),
(59, 'Francisca', '', 'Sanchez', '', 23642011, 42, 'francisca.sanchez59@students.com', '1984-06-13', '2026-06-13', 'En formacion', 'Primaria', 'Femenino', 1),
(60, 'Jose', 'Miguel', 'Gonzalez', 'Gonzalez', 23703740, 16, 'jose.gonzalez60@students.com', '2010-03-02', '2026-01-19', 'Inactivo', 'Primaria', 'Masculino', 4),
(61, 'Maria', '', 'Fernandez', '', 23765469, 42, 'maria.fernandez61@students.com', '1983-09-28', '2026-02-26', 'Asignado', 'Bachillerato', 'Femenino', 1),
(62, 'Teresa', '', 'Gonzalez', '', 23827198, 22, 'teresa.gonzalez62@students.com', '2004-01-11', '2026-03-18', 'En formacion', 'Bachillerato', 'Femenino', 1),
(63, 'Jose', '', 'Gonzalez', 'Hernandez', 23888927, 27, 'jose.gonzalez63@students.com', '1998-08-20', '2026-04-01', 'Asignado', 'Primaria', 'Masculino', 1),
(64, 'Elena', '', 'Sanchez', '', 23950656, 47, 'elena.sanchez64@students.com', '1978-12-02', '2026-05-25', 'En formacion', 'Universidad', 'Femenino', 1),
(65, 'Francisca', 'Carmen', 'Fernandez', '', 24012385, 47, 'francisca.fernandez65@students.com', '1978-10-28', '2026-06-20', 'Inactivo', 'Bachillerato', 'Femenino', 1),
(66, 'Rafael', '', 'Gonzalez', 'Hernandez', 24074114, 24, 'rafael.gonzalez66@students.com', '2002-03-04', '2026-01-15', 'Inactivo', 'Universidad', 'Masculino', 1),
(67, 'Teresa', '', 'Flores', '', 24135843, 37, 'teresa.flores67@students.com', '1988-11-10', '2026-02-21', 'Inactivo', 'Universidad', 'Femenino', 1),
(68, 'Isabel', '', 'Flores', '', 24197572, 33, 'isabel.flores68@students.com', '1992-07-07', '2026-03-27', 'En formacion', 'Bachillerato', 'Femenino', 1),
(69, 'Manuel', '', 'Diaz', 'Lopez', 24259301, 51, 'manuel.diaz69@students.com', '1975-03-10', '2026-04-01', 'Asignado', 'Bachillerato', 'Masculino', 1),
(70, 'Ana', 'Isabel', 'Gonzalez', '', 24321030, 17, 'ana.gonzalez70@students.com', '2008-09-30', '2026-05-28', 'En formacion', 'Universidad', 'Femenino', 1),
(71, 'Rosa', '', 'Lopez', '', 24382759, 27, 'rosa.lopez71@students.com', '1998-06-29', '2026-06-21', 'En formacion', 'Bachillerato', 'Femenino', 1),
(72, 'Luis', '', 'Hernandez', 'Ramirez', 24444488, 67, 'luis.hernandez72@students.com', '1958-10-12', '2026-01-04', 'En formacion', 'Primaria', 'Masculino', 1),
(73, 'Isabel', '', 'Gonzalez', '', 24506217, 44, 'isabel.gonzalez73@students.com', '1982-03-20', '2026-02-10', 'Diferido', 'Universidad', 'Femenino', 1),
(74, 'Carmen', '', 'Diaz', '', 24567946, 46, 'carmen.diaz74@students.com', '1980-05-31', '2026-03-14', 'En formacion', 'Primaria', 'Femenino', 1),
(75, 'Manuel', 'Jose', 'Martinez', 'Lopez', 24629675, 34, 'manuel.martinez75@students.com', '1992-04-21', '2026-04-24', 'En formacion', 'Primaria', 'Masculino', 3),
(76, 'Carmen', '', 'Lopez', '', 24691404, 42, 'carmen.lopez76@students.com', '1983-10-27', '2026-05-05', 'Diferido', 'Bachillerato', 'Femenino', 1),
(77, 'Juana', '', 'Martinez', '', 24753133, 52, 'juana.martinez77@students.com', '1974-03-26', '2026-06-01', 'En formacion', 'Primaria', 'Femenino', 1),
(78, 'Carlos', '', 'Martinez', 'Garcia', 24814862, 34, 'carlos.martinez78@students.com', '1991-08-31', '2026-01-24', 'Asignado', 'Universidad', 'Masculino', 1),
(79, 'Rosa', '', 'Lopez', '', 24876591, 41, 'rosa.lopez79@students.com', '1984-11-02', '2026-02-05', 'En formacion', 'Universidad', 'Femenino', 1),
(80, 'Carmen', 'Isabel', 'Diaz', '', 24938320, 16, 'carmen.diaz80@students.com', '2010-05-12', '2026-03-22', 'Asignado', 'Bachillerato', 'Femenino', 1),
(81, 'Francisco', '', 'Sanchez', 'Flores', 25000049, 51, 'francisco.sanchez81@students.com', '1974-11-08', '2026-04-15', 'En formacion', 'Bachillerato', 'Masculino', 1),
(82, 'Luisa', '', 'Rodriguez', '', 25061778, 41, 'luisa.rodriguez82@students.com', '1984-11-02', '2026-05-26', 'Diferido', 'Bachillerato', 'Femenino', 1),
(83, 'Teresa', '', 'Martinez', '', 25123507, 20, 'teresa.martinez83@students.com', '2005-11-26', '2026-06-02', 'En formacion', 'Universidad', 'Femenino', 1),
(84, 'Francisco', '', 'Garcia', 'Perez', 25185236, 67, 'francisco.garcia84@students.com', '1959-01-31', '2026-01-28', 'Disponible', 'Bachillerato', 'Masculino', 1),
(85, 'Teresa', 'Ana', 'Garcia', '', 25246965, 45, 'teresa.garcia85@students.com', '1980-12-19', '2026-02-07', 'En formacion', 'Universidad', 'Femenino', 1),
(86, 'Maria', '', 'Perez', '', 25308694, 40, 'maria.perez86@students.com', '1986-04-22', '2026-03-02', 'Disponible', 'Bachillerato', 'Femenino', 1),
(87, 'Carlos', '', 'Diaz', 'Sanchez', 25370423, 46, 'carlos.diaz87@students.com', '1980-05-04', '2026-04-07', 'En formacion', 'Primaria', 'Masculino', 1),
(88, 'Teresa', '', 'Gomez', '', 25432152, 53, 'teresa.gomez88@students.com', '1973-06-15', '2026-05-23', 'En formacion', 'Primaria', 'Femenino', 1),
(89, 'Elena', '', 'Flores', '', 25493881, 46, 'elena.flores89@students.com', '1980-05-05', '2026-06-18', 'Disponible', 'Primaria', 'Femenino', 1),
(90, 'Carlos', 'Pedro', 'Ramirez', 'Flores', 25555610, 16, 'carlos.ramirez90@students.com', '2010-06-04', '2026-01-01', 'Disponible', 'Universidad', 'Masculino', 4);

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
  `estatus_participante` enum('Asignado','Diferido','Aprobado','Reprobado','En formación','Ausente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante_curso`
--

INSERT INTO `tb_participante_curso` (`id_participante_curso`, `id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1, 1, 11, '2026-05-04', '2026-05-08', '2026-06-15', 'En formación'),
(2, 1, 16, '2026-04-01', '2026-04-02', '2026-05-19', 'Asignado'),
(3, 2, 5, '2026-05-05', '2026-05-09', '2026-07-19', 'En formación'),
(4, 3, 5, '2026-05-02', '2026-05-09', '2026-07-19', 'En formación'),
(5, 3, 18, '2026-06-08', '2026-06-10', '2026-09-06', 'Diferido'),
(6, 4, 12, '2026-05-30', '2026-06-09', '2026-08-20', 'Asignado'),
(7, 5, 14, '2026-06-21', '2026-02-09', '2026-04-03', 'Reprobado'),
(8, 6, 14, '2026-01-30', '2026-02-09', '2026-04-03', 'Aprobado'),
(9, 7, 4, '2026-04-03', '2026-04-08', '2026-05-14', 'Asignado'),
(10, 7, 5, '2026-05-02', '2026-05-09', '2026-07-19', 'En formación'),
(11, 8, 10, '2026-03-28', '2026-04-07', '2026-06-14', 'Asignado'),
(12, 8, 11, '2026-05-06', '2026-05-08', '2026-06-15', 'En formación'),
(13, 9, 12, '2026-06-02', '2026-06-09', '2026-08-20', 'Asignado'),
(14, 10, 6, '2026-06-03', '2026-06-08', '2026-08-18', 'Asignado'),
(15, 11, 2, '2026-06-20', '2026-02-06', '2026-05-05', 'Aprobado'),
(16, 11, 12, '2026-06-20', '2026-06-09', '2026-08-20', 'Asignado'),
(17, 12, 9, '2026-02-28', '2026-03-05', '2026-04-29', 'Aprobado'),
(18, 13, 3, '2026-02-24', '2026-03-01', '2026-05-08', 'Aprobado'),
(19, 13, 16, '2026-03-27', '2026-04-02', '2026-05-19', 'Asignado'),
(20, 14, 5, '2026-05-04', '2026-05-09', '2026-07-19', 'En formación'),
(21, 15, 5, '2026-05-07', '2026-05-09', '2026-07-19', 'En formación'),
(22, 15, 12, '2026-05-30', '2026-06-09', '2026-08-20', 'Asignado'),
(23, 16, 6, '2026-06-06', '2026-06-08', '2026-08-18', 'Asignado'),
(24, 17, 18, '2026-06-16', '2026-06-10', '2026-09-06', 'Diferido'),
(25, 18, 16, '2026-03-31', '2026-04-02', '2026-05-19', 'Asignado'),
(26, 19, 5, '2026-04-30', '2026-05-09', '2026-07-19', 'En formación'),
(27, 20, 16, '2026-03-23', '2026-04-02', '2026-05-19', 'Asignado'),
(28, 21, 4, '2026-04-05', '2026-04-08', '2026-05-14', 'Asignado'),
(29, 21, 10, '2026-04-05', '2026-04-07', '2026-06-14', 'Asignado'),
(30, 22, 6, '2026-06-02', '2026-06-08', '2026-08-18', 'Asignado'),
(31, 23, 6, '2026-06-03', '2026-06-08', '2026-08-18', 'Asignado'),
(32, 23, 12, '2026-06-06', '2026-06-09', '2026-08-20', 'Asignado'),
(33, 24, 10, '2026-03-30', '2026-04-07', '2026-06-14', 'Asignado'),
(34, 25, 15, '2026-02-26', '2026-03-04', '2026-04-13', 'Aprobado'),
(35, 26, 17, '2026-04-26', '2026-05-01', '2026-06-26', 'En formación'),
(36, 27, 11, '2026-05-02', '2026-05-08', '2026-06-15', 'En formación'),
(37, 27, 18, '2026-06-04', '2026-06-10', '2026-09-06', 'Diferido'),
(38, 28, 12, '2026-06-08', '2026-06-09', '2026-08-20', 'Asignado'),
(39, 29, 12, '2026-06-23', '2026-06-09', '2026-08-20', 'Asignado'),
(40, 30, 10, '2026-04-06', '2026-04-07', '2026-06-14', 'Asignado'),
(41, 30, 16, '2026-03-26', '2026-04-02', '2026-05-19', 'Asignado'),
(42, 31, 11, '2026-05-04', '2026-05-08', '2026-06-15', 'En formación'),
(43, 32, 11, '2026-05-07', '2026-05-08', '2026-06-15', 'En formación'),
(44, 32, 12, '2026-06-02', '2026-06-09', '2026-08-20', 'Asignado'),
(45, 33, 11, '2026-04-29', '2026-05-08', '2026-06-15', 'En formación'),
(46, 33, 17, '2026-04-27', '2026-05-01', '2026-06-26', 'En formación'),
(47, 34, 12, '2026-06-04', '2026-06-09', '2026-08-20', 'Asignado'),
(48, 34, 18, '2026-06-03', '2026-06-10', '2026-09-06', 'Diferido'),
(49, 35, 5, '2026-06-25', '2026-05-09', '2026-07-19', 'En formación'),
(50, 36, 9, '2026-02-26', '2026-03-05', '2026-04-29', 'Aprobado'),
(51, 36, 13, '2026-01-03', '2026-01-08', '2026-02-11', 'Aprobado'),
(52, 37, 17, '2026-04-25', '2026-05-01', '2026-06-26', 'En formación'),
(53, 38, 11, '2026-04-28', '2026-05-08', '2026-06-15', 'En formación'),
(54, 39, 11, '2026-05-04', '2026-05-08', '2026-06-15', 'En formación'),
(55, 39, 12, '2026-06-03', '2026-06-09', '2026-08-20', 'Asignado'),
(56, 40, 18, '2026-06-05', '2026-06-10', '2026-09-06', 'Diferido'),
(57, 41, 4, '2026-06-17', '2026-04-08', '2026-05-14', 'Asignado'),
(58, 41, 18, '2026-06-17', '2026-06-10', '2026-09-06', 'Diferido'),
(59, 42, 8, '2026-02-06', '2026-02-07', '2026-03-21', 'Aprobado'),
(60, 42, 21, '2026-02-28', '2026-03-09', '2026-04-27', 'Aprobado'),
(61, 43, 16, '2026-03-25', '2026-04-02', '2026-05-19', 'Asignado'),
(62, 44, 12, '2026-06-03', '2026-06-09', '2026-08-20', 'Asignado'),
(63, 45, 12, '2026-06-07', '2026-06-09', '2026-08-20', 'Asignado'),
(64, 46, 6, '2026-06-04', '2026-06-08', '2026-08-18', 'Asignado'),
(65, 47, 20, '2026-06-23', '2026-02-10', '2026-03-28', 'Aprobado'),
(66, 48, 2, '2026-02-02', '2026-02-06', '2026-05-05', 'Aprobado'),
(67, 48, 10, '2026-04-06', '2026-04-07', '2026-06-14', 'Asignado'),
(68, 49, 6, '2026-06-03', '2026-06-08', '2026-08-18', 'Asignado'),
(69, 49, 11, '2026-04-30', '2026-05-08', '2026-06-15', 'En formación'),
(70, 50, 10, '2026-04-05', '2026-04-07', '2026-06-14', 'Asignado'),
(71, 51, 11, '2026-05-07', '2026-05-08', '2026-06-15', 'En formación'),
(72, 51, 17, '2026-04-29', '2026-05-01', '2026-06-26', 'En formación'),
(73, 52, 11, '2026-05-06', '2026-05-08', '2026-06-15', 'En formación'),
(74, 52, 18, '2026-06-06', '2026-06-10', '2026-09-06', 'Diferido'),
(75, 53, 3, '2026-06-19', '2026-03-01', '2026-05-08', 'Aprobado'),
(76, 54, 20, '2026-02-09', '2026-02-10', '2026-03-28', 'Ausente'),
(77, 55, 3, '2026-02-27', '2026-03-01', '2026-05-08', 'Aprobado'),
(78, 56, 12, '2026-06-06', '2026-06-09', '2026-08-20', 'Asignado'),
(79, 56, 16, '2026-03-25', '2026-04-02', '2026-05-19', 'Asignado'),
(80, 57, 12, '2026-06-07', '2026-06-09', '2026-08-20', 'Asignado'),
(81, 57, 17, '2026-04-27', '2026-05-01', '2026-06-26', 'En formación'),
(82, 58, 6, '2026-06-06', '2026-06-08', '2026-08-18', 'Asignado'),
(83, 59, 12, '2026-06-13', '2026-06-09', '2026-08-20', 'Asignado'),
(84, 60, 2, '2026-02-05', '2026-02-06', '2026-05-05', 'Aprobado'),
(85, 61, 4, '2026-04-01', '2026-04-08', '2026-05-14', 'Asignado'),
(86, 62, 11, '2026-05-03', '2026-05-08', '2026-06-15', 'En formación'),
(87, 62, 16, '2026-03-23', '2026-04-02', '2026-05-19', 'Asignado'),
(88, 63, 16, '2026-04-01', '2026-04-02', '2026-05-19', 'Asignado'),
(89, 64, 12, '2026-06-05', '2026-06-09', '2026-08-20', 'Asignado'),
(90, 64, 18, '2026-06-05', '2026-06-10', '2026-09-06', 'Diferido'),
(91, 65, 9, '2026-06-20', '2026-03-05', '2026-04-29', 'Aprobado'),
(92, 65, 12, '2026-06-20', '2026-06-09', '2026-08-20', 'Asignado'),
(93, 66, 21, '2026-03-04', '2026-03-09', '2026-04-27', 'Aprobado'),
(94, 67, 11, '2026-05-01', '2026-05-08', '2026-06-15', 'En formación'),
(95, 68, 5, '2026-05-05', '2026-05-09', '2026-07-19', 'En formación'),
(96, 68, 18, '2026-06-06', '2026-06-10', '2026-09-06', 'Diferido'),
(97, 69, 16, '2026-04-01', '2026-04-02', '2026-05-19', 'Asignado'),
(98, 70, 6, '2026-06-04', '2026-06-08', '2026-08-18', 'Asignado'),
(99, 71, 6, '2026-06-21', '2026-06-08', '2026-08-18', 'Asignado'),
(100, 71, 19, '2026-06-21', '2026-01-08', '2026-02-12', 'Reprobado'),
(101, 72, 5, '2026-05-01', '2026-05-09', '2026-07-19', 'En formación'),
(102, 72, 6, '2026-05-31', '2026-06-08', '2026-08-18', 'Asignado'),
(103, 73, 15, '2026-03-01', '2026-03-04', '2026-04-13', 'Ausente'),
(104, 73, 18, '2026-05-31', '2026-06-10', '2026-09-06', 'Diferido'),
(105, 74, 6, '2026-05-30', '2026-06-08', '2026-08-18', 'Asignado'),
(106, 74, 16, '2026-03-27', '2026-04-02', '2026-05-19', 'Asignado'),
(107, 75, 5, '2026-04-29', '2026-05-09', '2026-07-19', 'En formación'),
(108, 76, 18, '2026-06-01', '2026-06-10', '2026-09-06', 'Diferido'),
(109, 77, 6, '2026-06-01', '2026-06-08', '2026-08-18', 'Asignado'),
(110, 78, 10, '2026-04-06', '2026-04-07', '2026-06-14', 'Asignado'),
(111, 79, 2, '2026-02-05', '2026-02-06', '2026-05-05', 'Aprobado'),
(112, 79, 6, '2026-06-03', '2026-06-08', '2026-08-18', 'Asignado'),
(113, 80, 16, '2026-03-30', '2026-04-02', '2026-05-19', 'Asignado'),
(114, 81, 6, '2026-06-01', '2026-06-08', '2026-08-18', 'Asignado'),
(115, 81, 12, '2026-06-05', '2026-06-09', '2026-08-20', 'Asignado'),
(116, 82, 18, '2026-06-01', '2026-06-10', '2026-09-06', 'Diferido'),
(117, 83, 12, '2026-06-02', '2026-06-09', '2026-08-20', 'Asignado'),
(118, 84, 3, '2026-02-25', '2026-03-01', '2026-05-08', 'Aprobado'),
(119, 85, 12, '2026-06-07', '2026-06-09', '2026-08-20', 'Asignado'),
(120, 86, 9, '2026-03-02', '2026-03-05', '2026-04-29', 'Aprobado'),
(121, 86, 21, '2026-03-07', '2026-03-09', '2026-04-27', 'Aprobado'),
(122, 87, 4, '2026-04-07', '2026-04-08', '2026-05-14', 'Asignado'),
(123, 87, 6, '2026-06-04', '2026-06-08', '2026-08-18', 'Asignado'),
(124, 88, 6, '2026-06-05', '2026-06-08', '2026-08-18', 'Asignado'),
(125, 89, 8, '2026-06-18', '2026-02-07', '2026-03-21', 'Aprobado'),
(126, 89, 21, '2026-06-18', '2026-03-09', '2026-04-27', 'Aprobado'),
(127, 90, 2, '2026-01-31', '2026-02-06', '2026-05-05', 'Aprobado'),
(128, 90, 7, '2026-01-03', '2026-01-04', '2026-02-19', 'Reprobado');

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
(124, 'TÉCNICOS EN ADMINISTRACIÓN DE UNIDADES MILITARES BÁSICAS'),
(127, 'INSTALADOR DE REVESTIMIENTOS PISOS PAREDES Y TECHOS'),
(128, 'PRODUCTOR DE CONTENIDO DIGITAL: ADMINISTRACIÓN DE REDES SOCIALES, ALFABETIZACIÓN TECNOLÓGICA, COMUNI'),
(129, 'RESTAURADOR MUEBLES DE MADERA'),
(130, 'RESTAURADOR Y MANTENIMIENTO DE INSTRUMENTOS MUSICALES DE CUERDA'),
(131, 'SECRETARIA(O) ADMINISTRATIVA(O)');

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
(31, 1, 2),
(48, 1, 3),
(37, 1, 5),
(39, 1, 6),
(21, 1, 8),
(23, 1, 24),
(47, 1, 57),
(22, 1, 66),
(24, 1, 67),
(25, 1, 68),
(32, 1, 73),
(33, 1, 74),
(34, 1, 75),
(35, 1, 76),
(36, 1, 77),
(40, 1, 81),
(41, 1, 82),
(42, 1, 83),
(43, 1, 84),
(44, 1, 85),
(45, 1, 86),
(46, 1, 87),
(49, 1, 90),
(50, 1, 91),
(64, 2, 3),
(1, 2, 5),
(2, 2, 49),
(3, 2, 50),
(4, 2, 51),
(5, 2, 52),
(6, 2, 53),
(7, 2, 54),
(8, 2, 55),
(9, 2, 56),
(52, 2, 93),
(53, 2, 94),
(56, 2, 97),
(57, 2, 98),
(58, 2, 99),
(60, 2, 101),
(61, 2, 102),
(62, 2, 103),
(63, 2, 104),
(65, 2, 105),
(66, 2, 106),
(67, 3, 2),
(75, 3, 3),
(26, 3, 5),
(27, 3, 6),
(72, 3, 8),
(74, 3, 57),
(28, 3, 69),
(29, 3, 70),
(30, 3, 71),
(73, 3, 87),
(68, 3, 107),
(69, 3, 108),
(70, 3, 109),
(71, 3, 110),
(76, 3, 111),
(79, 4, 2),
(14, 4, 30),
(10, 4, 57),
(11, 4, 58),
(12, 4, 59),
(13, 4, 60),
(15, 4, 61),
(89, 4, 87),
(86, 4, 101),
(77, 4, 112),
(78, 4, 113),
(80, 4, 114),
(81, 4, 115),
(82, 4, 116),
(83, 4, 117),
(84, 4, 118),
(85, 4, 119),
(87, 4, 120),
(88, 4, 121),
(16, 5, 6),
(17, 5, 62),
(18, 5, 63),
(19, 5, 64),
(20, 5, 65),
(95, 5, 101),
(92, 5, 116),
(90, 5, 122),
(91, 5, 123),
(93, 5, 124),
(94, 5, 125),
(96, 5, 126),
(97, 5, 127),
(98, 5, 128),
(99, 5, 129),
(109, 6, 5),
(105, 6, 9),
(100, 6, 49),
(101, 6, 130),
(102, 6, 131),
(103, 6, 132),
(104, 6, 133),
(106, 6, 134),
(107, 6, 135),
(108, 6, 136),
(110, 6, 137),
(111, 6, 138),
(112, 6, 139),
(113, 6, 140),
(114, 7, 2),
(118, 7, 123),
(115, 7, 141),
(116, 7, 142),
(117, 7, 143),
(119, 7, 144),
(127, 8, 3),
(125, 8, 6),
(123, 8, 8),
(120, 8, 131),
(121, 8, 145),
(122, 8, 146),
(124, 8, 147),
(126, 8, 148),
(128, 8, 149),
(129, 8, 150),
(130, 8, 151),
(137, 9, 3),
(134, 9, 6),
(132, 9, 9),
(136, 9, 57),
(135, 9, 102),
(131, 9, 131),
(133, 9, 145),
(138, 9, 152),
(145, 10, 3),
(142, 10, 6),
(139, 10, 131),
(141, 10, 145),
(143, 10, 148),
(147, 10, 149),
(148, 10, 150),
(140, 10, 153),
(144, 10, 154),
(146, 10, 155),
(149, 11, 2),
(156, 11, 3),
(153, 11, 6),
(151, 11, 9),
(155, 11, 57),
(154, 11, 102),
(150, 11, 131),
(152, 11, 145),
(157, 11, 152),
(158, 12, 2),
(165, 12, 3),
(163, 12, 5),
(164, 12, 8),
(159, 12, 156),
(160, 12, 157),
(161, 12, 158),
(162, 12, 159),
(166, 13, 2),
(172, 13, 3),
(168, 13, 5),
(169, 13, 6),
(171, 13, 57),
(167, 13, 76),
(170, 13, 87),
(173, 13, 90),
(174, 13, 91),
(175, 14, 2),
(180, 14, 3),
(177, 14, 6),
(176, 14, 8),
(179, 14, 57),
(178, 14, 87),
(181, 14, 90),
(182, 14, 91),
(183, 14, 111),
(193, 15, 3),
(191, 15, 5),
(188, 15, 26),
(184, 15, 49),
(192, 15, 102),
(185, 15, 131),
(190, 15, 146),
(186, 15, 160),
(187, 15, 161),
(189, 15, 162),
(194, 15, 163),
(195, 16, 2),
(201, 16, 3),
(198, 16, 5),
(197, 16, 9),
(196, 16, 164),
(199, 16, 165),
(200, 16, 166),
(202, 16, 167),
(203, 16, 168),
(204, 16, 169),
(206, 17, 5),
(207, 17, 101),
(205, 17, 145),
(208, 17, 170),
(209, 17, 171),
(210, 17, 172),
(223, 18, 3),
(221, 18, 57),
(219, 18, 126),
(216, 18, 131),
(211, 18, 173),
(212, 18, 174),
(213, 18, 175),
(214, 18, 176),
(215, 18, 177),
(217, 18, 178),
(218, 18, 179),
(220, 18, 180),
(222, 18, 181),
(224, 18, 182),
(230, 19, 3),
(228, 19, 6),
(225, 19, 131),
(227, 19, 145),
(232, 19, 149),
(226, 19, 153),
(229, 19, 154),
(231, 19, 155),
(233, 20, 2),
(241, 20, 3),
(236, 20, 5),
(238, 20, 8),
(240, 20, 87),
(234, 20, 183),
(235, 20, 184),
(237, 20, 185),
(239, 20, 186),
(242, 21, 2),
(245, 21, 3),
(244, 21, 5),
(243, 21, 187),
(261, 22, 3),
(258, 22, 5),
(254, 22, 26),
(246, 22, 49),
(260, 22, 102),
(249, 22, 131),
(257, 22, 146),
(251, 22, 160),
(253, 22, 161),
(255, 22, 162),
(262, 22, 163),
(247, 22, 188),
(248, 22, 189),
(250, 22, 190),
(252, 22, 191),
(256, 22, 192),
(259, 22, 193),
(263, 22, 194),
(273, 23, 3),
(266, 23, 5),
(270, 23, 102),
(264, 23, 195),
(265, 23, 196),
(267, 23, 197),
(268, 23, 198),
(269, 23, 199),
(271, 23, 200),
(272, 23, 201),
(284, 24, 3),
(277, 24, 5),
(281, 24, 102),
(276, 24, 196),
(278, 24, 197),
(279, 24, 198),
(280, 24, 199),
(282, 24, 200),
(283, 24, 201),
(274, 24, 202),
(275, 24, 203),
(285, 25, 2),
(292, 25, 3),
(290, 25, 5),
(291, 25, 8),
(286, 25, 156),
(287, 25, 157),
(289, 25, 159),
(288, 25, 204),
(293, 26, 2),
(300, 26, 3),
(298, 26, 5),
(299, 26, 8),
(294, 26, 156),
(295, 26, 157),
(297, 26, 159),
(296, 26, 205),
(301, 27, 2),
(308, 27, 3),
(306, 27, 5),
(307, 27, 8),
(302, 27, 156),
(303, 27, 157),
(305, 27, 159),
(304, 27, 206),
(309, 28, 2),
(318, 28, 3),
(316, 28, 5),
(310, 28, 207),
(311, 28, 208),
(312, 28, 209),
(313, 28, 210),
(314, 28, 211),
(315, 28, 212),
(317, 28, 213),
(327, 29, 3),
(326, 29, 5),
(323, 29, 26),
(322, 29, 42),
(320, 29, 43),
(325, 29, 119),
(324, 29, 210),
(319, 29, 214),
(321, 29, 215),
(328, 29, 216),
(329, 30, 217),
(330, 30, 218),
(331, 30, 219),
(332, 30, 220),
(334, 31, 2),
(338, 31, 8),
(333, 31, 49),
(339, 31, 102),
(335, 31, 131),
(337, 31, 145),
(336, 31, 221),
(340, 31, 222),
(344, 32, 1),
(346, 32, 101),
(345, 32, 124),
(347, 32, 126),
(348, 32, 129),
(343, 32, 145),
(341, 32, 223),
(342, 32, 224),
(349, 33, 2),
(358, 33, 5),
(359, 33, 8),
(350, 33, 157),
(351, 33, 225),
(352, 33, 226),
(353, 33, 227),
(354, 33, 228),
(355, 33, 229),
(356, 33, 230),
(357, 33, 231),
(361, 34, 2),
(367, 34, 8),
(360, 34, 49),
(368, 34, 102),
(362, 34, 131),
(365, 34, 145),
(363, 34, 232),
(364, 34, 233),
(366, 34, 234),
(369, 34, 235),
(370, 34, 236),
(371, 34, 237),
(372, 34, 238),
(373, 34, 239),
(374, 35, 2),
(382, 35, 6),
(383, 35, 87),
(375, 35, 131),
(376, 35, 240),
(377, 35, 241),
(378, 35, 242),
(379, 35, 243),
(380, 35, 244),
(381, 35, 245),
(384, 35, 246),
(389, 36, 1),
(391, 36, 101),
(390, 36, 124),
(392, 36, 126),
(393, 36, 129),
(388, 36, 145),
(386, 36, 223),
(387, 36, 224),
(385, 36, 247),
(398, 37, 3),
(397, 37, 5),
(396, 37, 157),
(394, 37, 248),
(395, 37, 249),
(402, 38, 3),
(401, 38, 87),
(399, 38, 250),
(400, 38, 251),
(408, 39, 3),
(406, 39, 5),
(407, 39, 87),
(405, 39, 157),
(403, 39, 248),
(404, 39, 252),
(413, 40, 3),
(411, 40, 5),
(412, 40, 87),
(409, 40, 250),
(410, 40, 253),
(414, 41, 2),
(422, 41, 3),
(421, 41, 5),
(415, 41, 254),
(416, 41, 255),
(417, 41, 256),
(418, 41, 257),
(419, 41, 258),
(420, 41, 259),
(423, 42, 2),
(428, 42, 3),
(426, 42, 5),
(425, 42, 9),
(424, 42, 164),
(427, 42, 165),
(429, 42, 167),
(431, 42, 169),
(430, 42, 260),
(436, 43, 5),
(438, 43, 87),
(432, 43, 164),
(433, 43, 261),
(434, 43, 262),
(435, 43, 263),
(437, 43, 264),
(444, 44, 5),
(446, 44, 87),
(439, 44, 164),
(441, 44, 261),
(442, 44, 262),
(443, 44, 263),
(445, 44, 264),
(440, 44, 265),
(453, 45, 57),
(451, 45, 137),
(448, 45, 157),
(447, 45, 248),
(449, 45, 266),
(450, 45, 267),
(452, 45, 268),
(454, 45, 269),
(460, 46, 9),
(455, 46, 49),
(465, 46, 57),
(456, 46, 157),
(464, 46, 186),
(458, 46, 266),
(466, 46, 269),
(457, 46, 270),
(459, 46, 271),
(461, 46, 272),
(462, 46, 273),
(463, 46, 274),
(467, 47, 217),
(468, 47, 218),
(469, 47, 219),
(474, 47, 220),
(470, 47, 275),
(471, 47, 276),
(472, 47, 277),
(473, 47, 278),
(484, 48, 3),
(482, 48, 5),
(483, 48, 87),
(475, 48, 279),
(476, 48, 280),
(477, 48, 281),
(478, 48, 282),
(479, 48, 283),
(480, 48, 284),
(481, 48, 285),
(488, 49, 1),
(489, 49, 6),
(487, 49, 9),
(485, 49, 131),
(486, 49, 266),
(498, 50, 3),
(497, 50, 57),
(496, 50, 126),
(490, 50, 131),
(491, 50, 286),
(492, 50, 287),
(493, 50, 288),
(494, 50, 289),
(495, 50, 290),
(499, 51, 2),
(505, 51, 3),
(503, 51, 5),
(504, 51, 8),
(500, 51, 157),
(501, 51, 225),
(502, 51, 226),
(509, 52, 1),
(506, 52, 49),
(511, 52, 101),
(507, 52, 122),
(512, 52, 126),
(513, 52, 128),
(514, 52, 129),
(508, 52, 145),
(510, 52, 291),
(524, 53, 6),
(519, 53, 9),
(525, 53, 87),
(515, 53, 112),
(522, 53, 120),
(523, 53, 126),
(516, 53, 131),
(517, 53, 250),
(518, 53, 292),
(520, 53, 293),
(521, 53, 294),
(526, 54, 2),
(535, 54, 57),
(529, 54, 115),
(530, 54, 117),
(531, 54, 119),
(532, 54, 120),
(534, 54, 148),
(528, 54, 250),
(527, 54, 295),
(533, 54, 296),
(536, 55, 2),
(538, 55, 123),
(537, 55, 297),
(539, 55, 298),
(540, 55, 299),
(541, 55, 300),
(542, 55, 301),
(543, 56, 2),
(551, 56, 57),
(545, 56, 115),
(546, 56, 117),
(547, 56, 119),
(548, 56, 120),
(550, 56, 148),
(544, 56, 250),
(549, 56, 296),
(552, 57, 2),
(560, 57, 3),
(556, 57, 5),
(557, 57, 6),
(559, 57, 57),
(554, 57, 74),
(558, 57, 87),
(553, 57, 302),
(555, 57, 303),
(562, 58, 1),
(569, 58, 3),
(561, 58, 4),
(563, 58, 5),
(564, 58, 6),
(567, 58, 57),
(566, 58, 87),
(565, 58, 304),
(568, 58, 305),
(570, 59, 2),
(574, 59, 3),
(572, 59, 5),
(573, 59, 87),
(571, 59, 131),
(580, 60, 5),
(582, 60, 6),
(581, 60, 8),
(585, 60, 57),
(575, 60, 157),
(583, 60, 186),
(576, 60, 306),
(577, 60, 307),
(578, 60, 308),
(579, 60, 309),
(584, 60, 310),
(586, 60, 311),
(588, 61, 2),
(598, 61, 3),
(593, 61, 5),
(595, 61, 6),
(594, 61, 38),
(591, 61, 39),
(592, 61, 41),
(587, 61, 49),
(597, 61, 57),
(589, 61, 312),
(590, 61, 313),
(596, 61, 314),
(604, 62, 5),
(606, 62, 87),
(599, 62, 164),
(601, 62, 261),
(602, 62, 262),
(603, 62, 263),
(605, 62, 264),
(600, 62, 284),
(607, 63, 2),
(614, 63, 3),
(610, 63, 5),
(611, 63, 6),
(613, 63, 57),
(608, 63, 74),
(612, 63, 87),
(609, 63, 315),
(616, 64, 2),
(623, 64, 3),
(621, 64, 6),
(615, 64, 49),
(620, 64, 126),
(617, 64, 131),
(618, 64, 316),
(619, 64, 317),
(622, 64, 318),
(624, 64, 319),
(634, 66, 2),
(640, 66, 3),
(637, 66, 6),
(639, 66, 57),
(638, 66, 87),
(635, 66, 321),
(636, 66, 322),
(641, 67, 2),
(652, 67, 3),
(647, 67, 5),
(649, 67, 6),
(648, 67, 8),
(651, 67, 57),
(650, 67, 87),
(643, 67, 107),
(644, 67, 108),
(645, 67, 109),
(646, 67, 110),
(653, 67, 111),
(642, 67, 323),
(659, 68, 3),
(658, 68, 57),
(656, 68, 126),
(654, 68, 131),
(655, 68, 324),
(657, 68, 325),
(660, 68, 326),
(661, 69, 2),
(665, 69, 3),
(664, 69, 5),
(663, 69, 9),
(662, 69, 164),
(667, 69, 168),
(666, 69, 327),
(674, 70, 5),
(678, 70, 57),
(669, 70, 97),
(676, 70, 102),
(679, 70, 269),
(670, 70, 306),
(671, 70, 307),
(672, 70, 308),
(673, 70, 309),
(677, 70, 310),
(681, 70, 311),
(668, 70, 328),
(675, 70, 329),
(680, 70, 330),
(682, 70, 331),
(683, 71, 2),
(688, 71, 3),
(686, 71, 5),
(685, 71, 9),
(684, 71, 164),
(687, 71, 165),
(690, 71, 168),
(693, 71, 169),
(692, 71, 260),
(689, 71, 327),
(691, 71, 332),
(694, 72, 2),
(699, 72, 3),
(697, 72, 5),
(696, 72, 9),
(695, 72, 164),
(698, 72, 165),
(702, 72, 168),
(701, 72, 327),
(700, 72, 333),
(703, 72, 334),
(709, 73, 3),
(704, 73, 5),
(707, 73, 102),
(705, 73, 197),
(706, 73, 198),
(708, 73, 201),
(713, 74, 3),
(710, 74, 5),
(711, 74, 87),
(712, 74, 335),
(718, 75, 3),
(717, 75, 6),
(715, 75, 145),
(716, 75, 147),
(719, 75, 149),
(720, 75, 150),
(714, 75, 153),
(721, 75, 163),
(722, 76, 2),
(726, 76, 3),
(724, 76, 5),
(725, 76, 8),
(723, 76, 157),
(727, 77, 2),
(735, 77, 3),
(730, 77, 5),
(732, 77, 6),
(731, 77, 8),
(734, 77, 57),
(728, 77, 74),
(733, 77, 87),
(729, 77, 336),
(736, 78, 2),
(746, 78, 3),
(741, 78, 6),
(744, 78, 57),
(738, 78, 77),
(743, 78, 87),
(737, 78, 302),
(745, 78, 320),
(739, 78, 321),
(740, 78, 322),
(742, 78, 337),
(747, 78, 338),
(748, 79, 2),
(755, 79, 5),
(752, 79, 284),
(749, 79, 339),
(750, 79, 340),
(751, 79, 341),
(753, 79, 342),
(754, 79, 343),
(756, 79, 344),
(757, 79, 345),
(758, 79, 346),
(759, 80, 2),
(764, 80, 3),
(762, 80, 5),
(760, 80, 9),
(763, 80, 87),
(761, 80, 347),
(765, 80, 348),
(766, 80, 349),
(767, 81, 2),
(778, 81, 3),
(776, 81, 5),
(777, 81, 8),
(768, 81, 156),
(769, 81, 157),
(775, 81, 159),
(770, 81, 204),
(774, 81, 205),
(771, 81, 206),
(772, 81, 350),
(773, 81, 351),
(796, 82, 3),
(794, 82, 87),
(790, 82, 123),
(787, 82, 131),
(795, 82, 318),
(786, 82, 354),
(788, 82, 355),
(789, 82, 356),
(791, 82, 357),
(792, 82, 358),
(793, 82, 359),
(779, 83, 2),
(785, 83, 5),
(783, 83, 284),
(781, 83, 340),
(782, 83, 341),
(780, 83, 352),
(784, 83, 353),
(802, 84, 3),
(797, 84, 5),
(800, 84, 102),
(798, 84, 197),
(799, 84, 198),
(801, 84, 200),
(811, 85, 3),
(808, 85, 5),
(810, 85, 101),
(805, 85, 187),
(806, 85, 284),
(803, 85, 360),
(804, 85, 361),
(807, 85, 362),
(809, 85, 363),
(812, 85, 364),
(813, 86, 2),
(822, 86, 6),
(823, 86, 87),
(814, 86, 131),
(816, 86, 145),
(815, 86, 241),
(819, 86, 242),
(820, 86, 243),
(821, 86, 244),
(825, 86, 246),
(817, 86, 365),
(818, 86, 366),
(824, 86, 367),
(830, 87, 6),
(829, 87, 9),
(827, 87, 131),
(832, 87, 304),
(826, 87, 368),
(828, 87, 369),
(831, 87, 370),
(833, 87, 371),
(834, 87, 372),
(835, 87, 373),
(842, 88, 8),
(846, 88, 87),
(845, 88, 102),
(837, 88, 131),
(847, 88, 269),
(850, 88, 373),
(836, 88, 374),
(838, 88, 375),
(839, 88, 376),
(840, 88, 377),
(841, 88, 378),
(843, 88, 379),
(844, 88, 380),
(848, 88, 381),
(849, 88, 382),
(858, 89, 3),
(855, 89, 5),
(854, 89, 26),
(851, 89, 49),
(857, 89, 102),
(852, 89, 131),
(859, 89, 163),
(853, 89, 191),
(856, 89, 193),
(867, 90, 3),
(866, 90, 5),
(862, 90, 26),
(860, 90, 131),
(865, 90, 146),
(863, 90, 162),
(861, 90, 190),
(868, 90, 194),
(864, 90, 383),
(873, 91, 8),
(876, 91, 87),
(875, 91, 102),
(869, 91, 131),
(877, 91, 269),
(879, 91, 373),
(870, 91, 375),
(871, 91, 377),
(872, 91, 378),
(878, 91, 381),
(874, 91, 384),
(885, 92, 3),
(881, 92, 5),
(884, 92, 102),
(880, 92, 196),
(882, 92, 198),
(883, 92, 199),
(896, 93, 3),
(894, 93, 57),
(891, 93, 126),
(888, 93, 131),
(887, 93, 177),
(889, 93, 178),
(890, 93, 179),
(893, 93, 180),
(895, 93, 181),
(897, 93, 182),
(886, 93, 385),
(892, 93, 386),
(898, 94, 2),
(905, 94, 3),
(900, 94, 5),
(901, 94, 6),
(904, 94, 57),
(899, 94, 74),
(903, 94, 87),
(902, 94, 337),
(906, 95, 2),
(915, 95, 3),
(908, 95, 5),
(910, 95, 6),
(909, 95, 8),
(914, 95, 57),
(907, 95, 74),
(911, 95, 83),
(912, 95, 86),
(913, 95, 87),
(919, 96, 5),
(917, 96, 26),
(916, 96, 207),
(918, 96, 210),
(920, 96, 213),
(921, 96, 387),
(937, 97, 2),
(946, 97, 3),
(944, 97, 5),
(945, 97, 8),
(938, 97, 156),
(939, 97, 157),
(943, 97, 159),
(941, 97, 350),
(942, 97, 351),
(940, 97, 388),
(922, 98, 2),
(936, 98, 3),
(934, 98, 5),
(935, 98, 8),
(923, 98, 156),
(924, 98, 157),
(925, 98, 158),
(933, 98, 159),
(926, 98, 204),
(932, 98, 205),
(927, 98, 206),
(929, 98, 350),
(930, 98, 351),
(928, 98, 388),
(931, 98, 389),
(950, 100, 2),
(957, 100, 5),
(958, 100, 8),
(960, 100, 87),
(953, 100, 97),
(959, 100, 102),
(951, 100, 157),
(952, 100, 392),
(954, 100, 393),
(955, 100, 394),
(956, 100, 395),
(961, 100, 396),
(962, 100, 397),
(963, 100, 398),
(969, 101, 3),
(966, 101, 6),
(968, 101, 57),
(967, 101, 102),
(964, 101, 131),
(965, 101, 157),
(975, 102, 3),
(972, 102, 6),
(974, 102, 57),
(973, 102, 102),
(970, 102, 131),
(971, 102, 157),
(982, 103, 3),
(979, 103, 6),
(977, 103, 9),
(981, 103, 57),
(980, 103, 102),
(976, 103, 131),
(978, 103, 145),
(990, 104, 3),
(987, 104, 6),
(985, 104, 9),
(989, 104, 57),
(988, 104, 102),
(983, 104, 131),
(986, 104, 145),
(984, 104, 399),
(996, 105, 6),
(995, 105, 9),
(993, 105, 131),
(992, 105, 368),
(997, 105, 370),
(999, 105, 372),
(1000, 105, 373),
(991, 105, 400),
(994, 105, 401),
(998, 105, 402),
(1007, 106, 6),
(1006, 106, 9),
(1003, 106, 131),
(1002, 106, 368),
(1004, 106, 369),
(1008, 106, 370),
(1009, 106, 371),
(1011, 106, 372),
(1012, 106, 373),
(1001, 106, 400),
(1005, 106, 401),
(1010, 106, 402),
(1019, 107, 1),
(1028, 107, 3),
(1021, 107, 5),
(1026, 107, 6),
(1015, 107, 9),
(1022, 107, 101),
(1023, 107, 120),
(1024, 107, 121),
(1025, 107, 126),
(1013, 107, 131),
(1027, 107, 138),
(1014, 107, 250),
(1016, 107, 293),
(1017, 107, 403),
(1018, 107, 404),
(1020, 107, 405),
(1029, 107, 406),
(1035, 108, 1),
(1038, 108, 6),
(1030, 108, 49),
(1036, 108, 101),
(1033, 108, 116),
(1031, 108, 123),
(1037, 108, 126),
(1039, 108, 128),
(1040, 108, 129),
(1034, 108, 145),
(1032, 108, 224),
(1041, 109, 2),
(1050, 109, 3),
(1046, 109, 5),
(1042, 109, 131),
(1048, 109, 318),
(1043, 109, 407),
(1044, 109, 408),
(1045, 109, 409),
(1047, 109, 410),
(1049, 109, 411),
(1051, 110, 2),
(1064, 110, 3),
(1053, 110, 5),
(1054, 110, 126),
(1055, 110, 335),
(1052, 110, 412),
(1056, 110, 413),
(1057, 110, 414),
(1058, 110, 415),
(1059, 110, 416),
(1060, 110, 417),
(1061, 110, 418),
(1062, 110, 419),
(1063, 110, 420),
(1069, 111, 101),
(1068, 111, 124),
(1070, 111, 126),
(1071, 111, 127),
(1072, 111, 128),
(1073, 111, 129),
(1065, 111, 223),
(1066, 111, 224),
(1067, 111, 291),
(1077, 112, 1),
(1082, 112, 6),
(1074, 112, 49),
(1080, 112, 101),
(1075, 112, 122),
(1079, 112, 125),
(1081, 112, 126),
(1083, 112, 128),
(1084, 112, 129),
(1078, 112, 291),
(1076, 112, 421),
(1088, 113, 8),
(1090, 113, 87),
(1089, 113, 102),
(1085, 113, 131),
(1091, 113, 269),
(1094, 113, 373),
(1086, 113, 375),
(1087, 113, 377),
(1092, 113, 381),
(1093, 113, 382),
(1130, 117, 2),
(1136, 117, 3),
(1133, 117, 5),
(1135, 117, 8),
(1131, 117, 284),
(1137, 117, 364),
(1132, 117, 434),
(1134, 117, 435),
(1138, 117, 436),
(1139, 117, 437),
(1147, 118, 3),
(1143, 118, 8),
(1145, 118, 87),
(1140, 118, 157),
(1144, 118, 186),
(1148, 118, 364),
(1142, 118, 434),
(1141, 118, 438),
(1146, 118, 439),
(1153, 119, 3),
(1151, 119, 8),
(1149, 119, 157),
(1152, 119, 186),
(1150, 119, 284),
(1154, 119, 440),
(1155, 119, 441),
(1156, 119, 442),
(1166, 120, 3),
(1165, 120, 87),
(1164, 120, 268),
(1157, 120, 279),
(1158, 120, 280),
(1159, 120, 281),
(1160, 120, 282),
(1161, 120, 283),
(1162, 120, 285),
(1163, 120, 443),
(1167, 120, 444),
(1168, 122, 2),
(1179, 122, 3),
(1174, 122, 5),
(1176, 122, 8),
(1178, 122, 87),
(1169, 122, 183),
(1175, 122, 185),
(1177, 122, 186),
(1170, 122, 445),
(1171, 122, 446),
(1172, 122, 447),
(1173, 122, 448),
(1183, 123, 3),
(1182, 123, 87),
(1180, 123, 283),
(1181, 123, 443),
(1184, 123, 449),
(1186, 124, 2),
(1189, 124, 6),
(1187, 124, 9),
(1185, 124, 112),
(1188, 124, 450),
(1190, 124, 451),
(1191, 124, 452),
(625, 127, 2),
(633, 127, 3),
(628, 127, 5),
(629, 127, 6),
(631, 127, 57),
(626, 127, 74),
(627, 127, 77),
(630, 127, 87),
(632, 127, 320),
(948, 128, 154),
(947, 128, 390),
(949, 128, 391),
(1095, 129, 2),
(1107, 129, 3),
(1102, 129, 5),
(1109, 129, 44),
(1097, 129, 45),
(1111, 129, 46),
(1103, 129, 47),
(1101, 129, 119),
(1100, 129, 135),
(1099, 129, 153),
(1096, 129, 422),
(1098, 129, 423),
(1104, 129, 424),
(1105, 129, 425),
(1106, 129, 426),
(1108, 129, 427),
(1110, 129, 428),
(1112, 129, 429),
(1113, 130, 2),
(1119, 130, 3),
(1116, 130, 5),
(1114, 130, 430),
(1115, 130, 431),
(1117, 130, 432),
(1118, 130, 433),
(1121, 131, 2),
(1128, 131, 6),
(1129, 131, 87),
(1125, 131, 101),
(1120, 131, 112),
(1122, 131, 115),
(1123, 131, 117),
(1124, 131, 118),
(1126, 131, 120),
(1127, 131, 121);

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
(106, 4125555555, 'Móvil', NULL, NULL, 11),
(319, 4120098765, 'Móvil', 1, NULL, NULL),
(320, 4120197530, 'Móvil', 2, NULL, NULL),
(321, 4120296295, 'Móvil', 3, NULL, NULL),
(322, 4120395060, 'Móvil', 4, NULL, NULL),
(323, 4120493825, 'Móvil', 5, NULL, NULL),
(324, 4120592590, 'Móvil', 6, NULL, NULL),
(325, 4120691355, 'Móvil', 7, NULL, NULL),
(326, 4120790120, 'Móvil', 8, NULL, NULL),
(327, 4120888885, 'Móvil', 9, NULL, NULL),
(328, 4120987650, 'Móvil', 10, NULL, NULL),
(329, 4121086415, 'Móvil', 11, NULL, NULL),
(330, 4121185180, 'Móvil', 12, NULL, NULL),
(331, 4121283945, 'Móvil', 13, NULL, NULL),
(332, 4121382710, 'Móvil', 14, NULL, NULL),
(333, 4121481475, 'Móvil', 15, NULL, NULL),
(334, 4160083719, 'Móvil', NULL, 1, NULL),
(335, 4160167438, 'Móvil', NULL, 2, NULL),
(336, 4160251157, 'Móvil', NULL, 3, NULL),
(337, 4160334876, 'Móvil', NULL, 4, NULL),
(338, 4160418595, 'Móvil', NULL, 5, NULL),
(339, 4160502314, 'Móvil', NULL, 6, NULL),
(340, 4160586033, 'Móvil', NULL, 7, NULL),
(341, 4160669752, 'Móvil', NULL, 8, NULL),
(342, 4160753471, 'Móvil', NULL, 9, NULL),
(343, 4160837190, 'Móvil', NULL, 10, NULL),
(344, 4160920909, 'Móvil', NULL, 11, NULL),
(345, 4161004628, 'Móvil', NULL, 12, NULL),
(346, 4161088347, 'Móvil', NULL, 13, NULL),
(347, 4161172066, 'Móvil', NULL, 14, NULL),
(348, 4161255785, 'Móvil', NULL, 15, NULL),
(349, 4161339504, 'Móvil', NULL, 16, NULL),
(350, 4161423223, 'Móvil', NULL, 17, NULL),
(351, 4161506942, 'Móvil', NULL, 18, NULL),
(352, 4161590661, 'Móvil', NULL, 19, NULL),
(353, 4161674380, 'Móvil', NULL, 20, NULL),
(354, 4161758099, 'Móvil', NULL, 21, NULL),
(355, 4161841818, 'Móvil', NULL, 22, NULL),
(356, 4161925537, 'Móvil', NULL, 23, NULL),
(357, 4162009256, 'Móvil', NULL, 24, NULL),
(358, 4162092975, 'Móvil', NULL, 25, NULL),
(359, 4162176694, 'Móvil', NULL, 26, NULL),
(360, 4162260413, 'Móvil', NULL, 27, NULL),
(361, 4162344132, 'Móvil', NULL, 28, NULL),
(362, 4162427851, 'Móvil', NULL, 29, NULL),
(363, 4162511570, 'Móvil', NULL, 30, NULL),
(364, 4162595289, 'Móvil', NULL, 31, NULL),
(365, 4162679008, 'Móvil', NULL, 32, NULL),
(366, 4162762727, 'Móvil', NULL, 33, NULL),
(367, 4162846446, 'Móvil', NULL, 34, NULL),
(368, 4162930165, 'Móvil', NULL, 35, NULL),
(369, 4163013884, 'Móvil', NULL, 36, NULL),
(370, 4163097603, 'Móvil', NULL, 37, NULL),
(371, 4163181322, 'Móvil', NULL, 38, NULL),
(372, 4163265041, 'Móvil', NULL, 39, NULL),
(373, 4163348760, 'Móvil', NULL, 40, NULL),
(374, 4163432479, 'Móvil', NULL, 41, NULL),
(375, 4163516198, 'Móvil', NULL, 42, NULL),
(376, 4163599917, 'Móvil', NULL, 43, NULL),
(377, 4163683636, 'Móvil', NULL, 44, NULL),
(378, 4163767355, 'Móvil', NULL, 45, NULL),
(379, 4163851074, 'Móvil', NULL, 46, NULL),
(380, 4163934793, 'Móvil', NULL, 47, NULL),
(381, 4164018512, 'Móvil', NULL, 48, NULL),
(382, 4164102231, 'Móvil', NULL, 49, NULL),
(383, 4164185950, 'Móvil', NULL, 50, NULL),
(384, 4164269669, 'Móvil', NULL, 51, NULL),
(385, 4164353388, 'Móvil', NULL, 52, NULL),
(386, 4164437107, 'Móvil', NULL, 53, NULL),
(387, 4164520826, 'Móvil', NULL, 54, NULL),
(388, 4164604545, 'Móvil', NULL, 55, NULL),
(389, 4164688264, 'Móvil', NULL, 56, NULL),
(390, 4164771983, 'Móvil', NULL, 57, NULL),
(391, 4164855702, 'Móvil', NULL, 58, NULL),
(392, 4164939421, 'Móvil', NULL, 59, NULL),
(393, 4165023140, 'Móvil', NULL, 60, NULL),
(394, 4165106859, 'Móvil', NULL, 61, NULL),
(395, 4165190578, 'Móvil', NULL, 62, NULL),
(396, 4165274297, 'Móvil', NULL, 63, NULL),
(397, 4165358016, 'Móvil', NULL, 64, NULL),
(398, 4165441735, 'Móvil', NULL, 65, NULL),
(399, 4165525454, 'Móvil', NULL, 66, NULL),
(400, 4165609173, 'Móvil', NULL, 67, NULL),
(401, 4165692892, 'Móvil', NULL, 68, NULL),
(402, 4165776611, 'Móvil', NULL, 69, NULL),
(403, 4165860330, 'Móvil', NULL, 70, NULL),
(404, 4165944049, 'Móvil', NULL, 71, NULL),
(405, 4166027768, 'Móvil', NULL, 72, NULL),
(406, 4166111487, 'Móvil', NULL, 73, NULL),
(407, 4166195206, 'Móvil', NULL, 74, NULL),
(408, 4166278925, 'Móvil', NULL, 75, NULL),
(409, 4166362644, 'Móvil', NULL, 76, NULL),
(410, 4166446363, 'Móvil', NULL, 77, NULL),
(411, 4166530082, 'Móvil', NULL, 78, NULL),
(412, 4166613801, 'Móvil', NULL, 79, NULL),
(413, 4166697520, 'Móvil', NULL, 80, NULL),
(414, 4166781239, 'Móvil', NULL, 81, NULL),
(415, 4166864958, 'Móvil', NULL, 82, NULL),
(416, 4166948677, 'Móvil', NULL, 83, NULL),
(417, 4167032396, 'Móvil', NULL, 84, NULL),
(418, 4167116115, 'Móvil', NULL, 85, NULL),
(419, 4167199834, 'Móvil', NULL, 86, NULL),
(420, 4167283553, 'Móvil', NULL, 87, NULL),
(421, 4167367272, 'Móvil', NULL, 88, NULL),
(422, 4167450991, 'Móvil', NULL, 89, NULL),
(423, 4167534710, 'Móvil', NULL, 90, NULL);

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
(11, 'Daniel', NULL, 'Bringtown', NULL, 'ADMIN', 'dbringtown1@gmail.com', '$2y$10$4pmDGNsnYghS/NDh85/64OQn/cE8WOSw1Y/vympd2TpYqP6/DNg6O', 'Administrador', 'assets/img/avatar/avatar_680da3ed12fa2.jpg', 'Activo'),
(13, 'Jorge', 'Josmauro', 'SDDD', 'Silva', 'Gianmarino', 'dbringtown2@gmail.com', '$2y$10$1OPhWnf4z2aHIJpXO5QOAeZ6RfN0lMx/VhsWFFNL04IaUWPY5uf0W', 'Apoyo', 'assets/img/avatar/default-user.png', 'Inactivo'),
(14, 'Jose', NULL, 'Benitez', NULL, 'Jbenitez', 'dbringtown12@gmail.com', '$2y$10$1HNTuArHRCrmq9yMywGFAu8M7R65NL0PA8FnjoY8UuC.YtMKzXFHe', 'Administrador', 'assets/img/avatar/default-user.png', 'Activo'),
(15, 'Mary', NULL, 'Serrada', NULL, 'INCES2025', 'dbringtown13@gmail.com', '$2y$10$z21kNN/cWNRTBgjugD6ij.4669Z5h5PWkhEYJMMvAwOe/KerCJ2MO', 'Apoyo', 'assets/img/avatar/default-user.png', 'Activo'),
(16, 'samuel ', '', 'rivas', '', 'samu', 'example@gmail.com', '$2y$10$CniBbUjywGebCBl7q4XLMO612UxtLOd8kaHwF7wLYvaUnjF.x164S', 'Administrador', 'assets/img/avatar/1766934581_Gojo Satoru.jpeg', 'Activo'),
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
  ADD UNIQUE KEY `uq_preimpreso` (`preimpreso`),
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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tb_discapacidad`
--
ALTER TABLE `tb_discapacidad`
  MODIFY `id_discapacidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tb_modalidad`
--
ALTER TABLE `tb_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_nombrecurso`
--
ALTER TABLE `tb_nombrecurso`
  MODIFY `id_nombrecurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=453;

--
-- AUTO_INCREMENT de la tabla `tb_objetivos`
--
ALTER TABLE `tb_objetivos`
  MODIFY `id_objetivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `tb_ppl`
--
ALTER TABLE `tb_ppl`
  MODIFY `id_ppl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de la tabla `tb_ppl_nombrecurso`
--
ALTER TABLE `tb_ppl_nombrecurso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1192;

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
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=424;

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

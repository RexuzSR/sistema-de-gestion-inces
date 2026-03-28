-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-03-2026 a las 04:16:21
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
  `id_motor` int(11) DEFAULT NULL,
  `id_subtipo` int(11) DEFAULT NULL,
  `id_areaFormativa` int(11) DEFAULT NULL,
  `id_ambito` int(11) DEFAULT NULL,
  `turno` enum('Mañana','Tarde') NOT NULL,
  `programa_formacion` enum('Programa de Formación Productiva (PFP)') DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_curso`
--

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `id_sectoreconomico`, `id_motor`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `turno`, `programa_formacion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(91, 'Prostitución-Avanzada', '2026-01-08', NULL, '2026-03-06', NULL, NULL, 0.00, 25, 10, 1, 1, 'Unidad Curricular', 5, NULL, 6, NULL, 7, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-07 08:15:22', '2026-03-07 23:13:13'),
(95, 'constructores', '2026-01-21', '2026-01-02', '2026-03-12', 60.00, 5.00, 0.00, 40, 10, 3, 2, 'Unidad Curricular', 5, 1, 2, 3, 1, 1, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-22 05:23:42', '2026-03-13 03:12:59'),
(96, 'Ingles', '2026-01-22', '0000-00-00', '2026-02-12', 5.00, 4.00, 0.00, 40, 10, 3, 1, 'Unidad Curricular', 5, 3, 6, 2, 6, 5, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-22 05:27:19', '2026-03-07 23:13:13'),
(108, 'constructor', '2026-03-17', '2026-03-26', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 0, 2, 'Unidad Curricular', 2, 2, 5, 3, 1, 1, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 00:35:51', '2026-03-24 01:51:41'),
(109, 'EstadosComprobar', '2026-03-17', '2026-03-26', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 6, 2, 'Unidad Curricular', 2, 2, 2, 3, 8, 2, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 04:14:04', '2026-03-24 01:51:41'),
(110, 'constructoras', '2026-03-17', '2026-03-06', '2026-03-30', 40.00, 4.00, 0.00, 40, 12, 0, 1, 'Unidad Curricular', 2, 2, 4, 3, 8, 1, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-08 06:39:52', '2026-03-14 16:50:35'),
(111, 'constructoras', '2026-03-24', '2026-04-02', '2026-04-14', 80.00, 5.00, 0.00, 50, 12, 0, 2, 'Unidad Curricular', 2, 2, 4, 1, 8, 3, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-03-14 21:33:32', '2026-03-24 01:51:41');

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
(24, 'asdf', 'asdf', 'asdff', 'qwerty', 8643258, 'example@gmail.com', '2004-10-20', 21, 'Disponible', 'Femenino', 'Voluntario');

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
(63, 23, 95, '2026-01-21');

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
(3, 'Crear', 'Se creó la meta: Aprobados (800)', 'tb_metas', 19, 16, '2026-03-14 17:59:43');

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
(17, 1, 20, 0, 'Inscritos', '2026-01-01', '2026-01-31', 'Cerrada', '2026-01-31 14:37:58', 0, NULL),
(18, 1, 20, 0, 'Aprobados', '2026-01-01', '2026-01-31', 'Cerrada', '2026-01-31 15:00:26', 0, NULL),
(19, 1, 800, 0, 'Aprobados', '2026-03-01', '2026-03-28', 'Activa', '2026-03-14 17:59:43', 0, NULL);

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
-- Estructura de tabla para la tabla `tb_motor`
--

CREATE TABLE `tb_motor` (
  `id_motor` int(11) NOT NULL,
  `nombre_motor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_motor`
--

INSERT INTO `tb_motor` (`id_motor`, `nombre_motor`) VALUES
(1, 'Turismo'),
(2, 'Industria'),
(3, 'Telecomunicaciones e Informática'),
(4, 'Industria Militar'),
(5, 'Forestal'),
(6, 'Investigación, Desarrollo, Innovación e Informació');

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
  `estado` enum('En formación','Inactivo','Disponible','Asignado','En sistema') NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `id_discapacidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
(339, 'Jorge', 'Josmauro', 'Santos', 'Silva', 31104273, 25, 'silvajosmauro@gmail.com', '2000-02-12', '2026-01-07', 'En formación', 'Primaria', 'Femenino', 2),
(341, 'samuel', 'jose', 'rivas', 'rengel', 30635942, 21, 'samueljosealexander@gmail.com', '2004-10-27', '2026-01-07', 'En formación', 'Universidad', 'Masculino', 1),
(343, 'samuel', 'jose', 'rivas', 'rengel', 30635941, 21, 'example@gmail.com', '2004-10-27', '2026-01-11', 'En formación', 'Universidad', 'Masculino', 2),
(345, 'Alexander', 'Del Valle', 'Rivas', 'Rengel', 30635932, 52, 'example@gmail.com', '1973-10-10', '2026-02-27', 'En formación', 'Primaria', 'Masculino', 3),
(346, 'Trinity', 'Jose', 'Rivas', 'Morales', 33312864, 15, 'example@gmail.com', '2010-04-15', '2026-02-27', 'En formación', 'Primaria', 'Femenino', 3),
(347, 'Alexander', 'Jose', 'Rivas', 'Rengel', 6803423, 49, 'example@gmail.com', '1976-05-20', '2026-03-07', 'En formación', 'Bachillerato', 'Masculino', 2),
(348, 'Alexander', 'Jose', 'Nolazco', 'Gonzales', 30655942, 60, 'example@gmail.com', '1965-04-15', '2026-03-07', 'En formación', 'Primaria', 'Masculino', 3),
(349, 'Alexander', 'Del Valle', 'Rivas', 'Rengel', 30635742, 45, 'example@gmail.com', '1980-04-10', '2026-03-07', 'En formación', 'Bachillerato', 'Femenino', 2),
(350, 'Trinity', 'Angel Ys', 'Rivas', 'Diouh', 30638942, 65, 'example@gmail.com', '1960-04-15', '2026-03-07', 'En formación', 'Primaria', 'Femenino', 2),
(351, 'Jorge', 'Del Valle', 'Sucre', 'Cumana', 7894561, 15, 'example@gmail.com', '2010-06-09', '2026-03-14', 'En sistema', 'Bachillerato', 'Masculino', 4);

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
(476, 345, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación'),
(477, 347, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación'),
(478, 348, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación'),
(479, 349, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación'),
(480, 346, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación'),
(481, 350, 109, '2026-03-08', '2026-03-17', '2026-03-30', 'En formación');

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
(57, 4127775577, 'Móvil', NULL, 351, NULL);

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
(16, 'samuel ', '', 'rivas', '', 'samu', 'example@gmail.com', '$2y$10$A/ISgZY.PQH6VFVUjTk2I.V0eiZj6zJR/3W3z1cTc9SKokSq1Wkva', 'Administrador', 'assets/img/avatar/1766934581_Gojo Satoru.jpeg', 'Activo');

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
  ADD KEY `id_motor` (`id_motor`),
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
-- Indices de la tabla `tb_motor`
--
ALTER TABLE `tb_motor`
  ADD PRIMARY KEY (`id_motor`);

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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `tb_discapacidad`
--
ALTER TABLE `tb_discapacidad`
  MODIFY `id_discapacidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tb_modalidad`
--
ALTER TABLE `tb_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_motor`
--
ALTER TABLE `tb_motor`
  MODIFY `id_motor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_objetivos`
--
ALTER TABLE `tb_objetivos`
  MODIFY `id_objetivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=482;

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
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `tb_curso_ibfk_3` FOREIGN KEY (`id_motor`) REFERENCES `tb_motor` (`id_motor`),
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

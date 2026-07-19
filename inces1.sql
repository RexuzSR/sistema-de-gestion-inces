-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-07-2026 a las 20:03:55
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
  `preimpreso` varchar(255) DEFAULT NULL,
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
  `fue_diferido` tinyint(1) DEFAULT 0,
  `id_sectoreconomico` int(11) DEFAULT NULL,
  `id_subtipo` int(11) DEFAULT NULL,
  `id_areaFormativa` int(11) DEFAULT NULL,
  `id_ambito` int(11) DEFAULT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `id_nombre_especifico` int(11) DEFAULT NULL,
  `turno` enum('Mañana','Tarde') NOT NULL,
  `programa_formacion` enum('Programa de Formación Productiva (PFP)') DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reiniciado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_curso`
--

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `preimpreso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `fue_diferido`, `id_sectoreconomico`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `id_ppl`, `id_nombre_especifico`, `turno`, `programa_formacion`, `fecha_creacion`, `fecha_actualizacion`, `reiniciado`) VALUES
(1, 'Técnicas de Construcción - Cohorte 2025-01-1', 'PRE-73840', '2025-01-02', NULL, '2025-02-06', 159.00, 4.00, 159.00, 41, 10, 17, 2, 'Unidad Curricular', 5, 0, 2, 3, 2, 3, 1, 67, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:46', '2026-07-19 17:38:46', 0),
(2, 'Comunicación Organizacional - Cohorte 2025-01-2', 'PRE-46200', '2025-01-11', NULL, '2025-02-17', 160.00, 5.00, 160.00, 48, 10, 26, 3, 'Unidad Curricular', 5, 0, 3, 3, 4, 1, 4, 61, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:46', '2026-07-19 17:38:47', 0),
(3, 'Uso de Materiales Naturales - Cohorte 2025-01-3', 'PRE-60858', '2025-01-06', NULL, '2025-02-09', 141.00, 5.00, 141.00, 40, 10, 26, 2, 'Unidad Curricular', 5, 0, 1, 1, 3, 1, 3, 70, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:48', '2026-07-19 17:38:49', 0),
(4, 'Procesos Constructivos Tradicionales - Cohorte 2025-02-1', 'PRE-92179', '2025-02-15', NULL, '2025-03-24', 91.00, 4.00, 91.00, 47, 10, 17, 2, 'Unidad Curricular', 5, 0, 1, 1, 3, 2, 3, 69, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:49', '2026-07-19 17:38:50', 0),
(5, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2025-02-2', 'PRE-31720', '2025-02-07', NULL, '2025-03-10', 104.00, 4.00, 104.00, 42, 10, 22, 1, 'Unidad Curricular', 5, 0, 3, 2, 7, 1, 5, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:50', '2026-07-19 17:38:52', 0),
(6, 'Redes de Computadoras - Cohorte 2025-02-3', 'PRE-94141', '2025-02-02', NULL, '2025-03-10', 148.00, 5.00, 148.00, 40, 10, 17, 1, 'Unidad Curricular', 5, 0, 3, 1, 10, 5, 2, 53, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:52', '2026-07-19 17:38:53', 0),
(7, 'Dirección de Ventas - Cohorte 2025-03-1', 'PRE-45429', '2025-03-08', NULL, '2025-04-12', 89.00, 5.00, 89.00, 49, 10, 23, 1, 'Unidad Curricular', 5, 0, 2, 1, 10, 5, 4, 58, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:53', '2026-07-19 17:38:54', 0),
(8, 'Técnicas de Construcción - Cohorte 2025-03-2', 'PRE-52916', '2025-03-04', NULL, '2025-04-03', 119.00, 4.00, 119.00, 46, 10, 27, 3, 'Unidad Curricular', 5, 0, 3, 2, 6, 2, 1, 67, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:54', '2026-07-19 17:38:55', 0),
(9, 'Política Internacional - Cohorte 2025-03-3', 'PRE-34614', '2025-03-13', NULL, '2025-04-18', 113.00, 5.00, 113.00, 43, 10, 23, 2, 'Unidad Curricular', 5, 0, 2, 2, 9, 3, 5, 62, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:56', '2026-07-19 17:38:57', 0),
(10, 'Seguridad Industrial - Cohorte 2025-04-1', 'PRE-76594', '2025-04-05', NULL, '2025-05-08', 122.00, 5.00, 122.00, 41, 10, 26, 2, 'Unidad Curricular', 5, 0, 1, 3, 9, 3, 1, 68, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:57', '2026-07-19 17:38:58', 0),
(11, 'MATEMÁTICA BÁSICA - Cohorte 2025-04-2', 'PRE-42763', '2025-04-06', NULL, '2025-05-11', 148.00, 5.00, 148.00, 48, 10, 17, 1, 'Unidad Curricular', 5, 0, 3, 3, 7, 1, 3, 5, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:58', '2026-07-19 17:38:59', 0),
(12, 'Política Internacional - Cohorte 2025-04-3', 'PRE-68577', '2025-04-11', NULL, '2025-05-13', 133.00, 4.00, 133.00, 43, 10, 17, 2, 'Unidad Curricular', 5, 0, 2, 2, 6, 4, 5, 62, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:38:59', '2026-07-19 17:39:01', 0),
(13, 'Técnicas de Construcción - Cohorte 2025-05-1', 'PRE-54146', '2025-05-07', NULL, '2025-06-13', 108.00, 4.00, 108.00, 45, 10, 26, 2, 'Unidad Curricular', 5, 0, 3, 3, 8, 1, 1, 67, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:01', '2026-07-19 17:39:03', 0),
(14, 'Sistemas Operativos - Cohorte 2025-05-2', 'PRE-99666', '2025-05-05', NULL, '2025-06-06', 83.00, 4.00, 83.00, 41, 10, 26, 1, 'Unidad Curricular', 5, 0, 2, 1, 3, 1, 2, 55, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:03', '2026-07-19 17:39:04', 0),
(15, 'Técnicas de Construcción - Cohorte 2025-05-3', 'PRE-26669', '2025-05-10', NULL, '2025-06-17', 65.00, 4.00, 65.00, 44, 10, 23, 1, 'Unidad Curricular', 5, 0, 1, 2, 10, 1, 1, 67, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:05', '2026-07-19 17:39:06', 0),
(16, 'Contabilidad Básica - Cohorte 2025-06-1', 'PRE-15204', '2025-06-07', NULL, '2025-07-11', 107.00, 5.00, 107.00, 48, 10, 23, 2, 'Unidad Curricular', 5, 0, 1, 3, 8, 1, 4, 59, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:06', '2026-07-19 17:39:07', 0),
(17, 'NUEVO MODELO DE GESTIÓN PRODUCTIVA - Cohorte 2025-06-2', 'PRE-97028', '2025-06-09', NULL, '2025-07-10', 82.00, 4.00, 82.00, 42, 10, 17, 2, 'Unidad Curricular', 5, 0, 3, 1, 10, 3, 1, 8, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:08', '2026-07-19 17:39:08', 0),
(18, 'Ingeniería de Software - Cohorte 2025-06-3', 'PRE-75148', '2025-06-15', NULL, '2025-07-23', 147.00, 5.00, 147.00, 40, 10, 24, 1, 'Unidad Curricular', 5, 0, 1, 2, 2, 5, 2, 54, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:08', '2026-07-19 17:39:09', 0),
(19, 'Producción de Contenidos - Cohorte 2025-07-1', 'PRE-92686', '2025-07-05', NULL, '2025-08-05', 85.00, 5.00, 85.00, 50, 10, 15, 1, 'Unidad Curricular', 5, 0, 2, 2, 7, 4, 5, 65, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:10', '2026-07-19 17:39:10', 0),
(20, 'ATENCIÓN Y SERVICIO AL CLIENTE - Cohorte 2025-07-2', 'PRE-63535', '2025-07-03', NULL, '2025-08-05', 69.00, 5.00, 69.00, 48, 10, 30, 3, 'Unidad Curricular', 5, 0, 1, 1, 9, 4, 4, 30, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:10', '2026-07-19 17:39:12', 0),
(21, 'MATEMÁTICA BÁSICA - Cohorte 2025-07-3', 'PRE-12173', '2025-07-13', NULL, '2025-08-20', 92.00, 4.00, 92.00, 41, 10, 21, 3, 'Unidad Curricular', 5, 0, 3, 3, 9, 5, 2, 5, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:12', '2026-07-19 17:39:13', 0),
(22, 'Producción de Contenidos - Cohorte 2025-08-1', 'PRE-59390', '2025-08-10', NULL, '2025-09-10', 128.00, 5.00, 128.00, 46, 10, 25, 2, 'Unidad Curricular', 5, 0, 1, 2, 4, 2, 5, 65, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:13', '2026-07-19 17:39:14', 0),
(23, 'Contabilidad Básica - Cohorte 2025-08-2', 'PRE-29392', '2025-08-07', NULL, '2025-09-15', 79.00, 4.00, 79.00, 47, 10, 22, 2, 'Unidad Curricular', 5, 0, 3, 1, 7, 2, 4, 59, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:14', '2026-07-19 17:39:16', 0),
(24, 'Seguridad Industrial - Cohorte 2025-08-3', 'PRE-62708', '2025-08-06', NULL, '2025-09-14', 111.00, 4.00, 111.00, 47, 10, 17, 2, 'Unidad Curricular', 5, 0, 2, 3, 3, 4, 1, 68, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:16', '2026-07-19 17:39:17', 0),
(25, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2025-09-1', 'PRE-99108', '2025-09-09', NULL, '2025-10-10', 69.00, 5.00, 69.00, 43, 10, 24, 2, 'Unidad Curricular', 5, 0, 2, 1, 9, 1, 5, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:17', '2026-07-19 17:39:19', 0),
(26, 'Proyecto Ético de Vida - Cohorte 2025-09-2', 'PRE-91470', '2025-09-15', NULL, '2025-10-20', 116.00, 4.00, 116.00, 41, 10, 24, 1, 'Unidad Curricular', 5, 0, 3, 1, 7, 5, 4, 57, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:19', '2026-07-19 17:39:20', 0),
(27, 'Matemática Aplicada - Cohorte 2025-09-3', 'PRE-57170', '2025-09-05', NULL, '2025-10-08', 141.00, 4.00, 141.00, 50, 10, 15, 3, 'Unidad Curricular', 5, 0, 3, 1, 9, 5, 1, 66, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:20', '2026-07-19 17:39:21', 0),
(28, 'Ofimática - Cohorte 2025-10-1', 'PRE-77792', '2025-10-05', NULL, '2025-11-12', 114.00, 4.00, 114.00, 40, 10, 28, 1, 'Unidad Curricular', 5, 0, 3, 2, 9, 5, 4, 60, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:21', '2026-07-19 17:39:22', 0),
(29, 'Alfabetización Tecnológica - Cohorte 2025-10-2', 'PRE-49573', '2025-10-03', NULL, '2025-11-12', 158.00, 5.00, 158.00, 40, 10, 16, 1, 'Unidad Curricular', 5, 0, 2, 3, 5, 3, 2, 49, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:22', '2026-07-19 17:39:23', 0),
(30, 'NUEVO MODELO DE GESTIÓN PRODUCTIVA - Cohorte 2025-10-3', 'PRE-59445', '2025-10-02', NULL, '2025-11-03', 108.00, 4.00, 108.00, 48, 10, 18, 3, 'Unidad Curricular', 5, 0, 2, 3, 7, 1, 1, 8, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:23', '2026-07-19 17:39:24', 0),
(31, 'Redes Sociales - Cohorte 2025-11-1', 'PRE-73235', '2025-11-13', NULL, '2025-12-18', 65.00, 4.00, 65.00, 46, 10, 19, 2, 'Unidad Curricular', 5, 0, 3, 3, 5, 4, 5, 64, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:24', '2026-07-19 17:39:25', 0),
(32, 'MATEMÁTICA BÁSICA - Cohorte 2025-11-2', 'PRE-90275', '2025-11-11', NULL, '2025-12-21', 67.00, 4.00, 67.00, 45, 10, 24, 2, 'Unidad Curricular', 5, 0, 3, 1, 1, 2, 3, 5, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:25', '2026-07-19 17:39:27', 0),
(33, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2025-11-3', 'PRE-94235', '2025-11-10', NULL, '2025-12-13', 98.00, 4.00, 98.00, 42, 10, 21, 3, 'Unidad Curricular', 5, 0, 2, 1, 5, 1, 5, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:27', '2026-07-19 17:39:28', 0),
(34, 'Redes de Computadoras - Cohorte 2025-12-1', 'PRE-86513', '2025-12-06', NULL, '2026-01-07', 123.00, 5.00, 123.00, 49, 10, 21, 1, 'Unidad Curricular', 5, 0, 2, 1, 4, 1, 2, 53, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:28', '2026-07-19 17:39:29', 0),
(35, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2025-12-2', 'PRE-59174', '2025-12-15', NULL, '2026-01-20', 127.00, 4.00, 127.00, 49, 10, 16, 2, 'Unidad Curricular', 5, 0, 2, 2, 9, 1, 3, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:29', '2026-07-19 17:39:30', 0),
(36, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2025-12-3', 'PRE-67487', '2025-12-11', NULL, '2026-01-17', 69.00, 5.00, 69.00, 45, 10, 26, 3, 'Unidad Curricular', 5, 0, 3, 2, 4, 4, 5, 6, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:30', '2026-07-19 17:39:31', 0),
(37, 'Sistemas Operativos - Cohorte 2026-01-1', 'PRE-51030', '2026-01-03', NULL, '2026-02-06', 102.00, 5.00, 102.00, 50, 10, 24, 1, 'Unidad Curricular', 5, 0, 1, 3, 3, 2, 2, 55, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:31', '2026-07-19 17:39:34', 0),
(38, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2026-01-2', 'PRE-77900', '2026-01-07', NULL, '2026-02-14', 149.00, 5.00, 149.00, 50, 10, 21, 3, 'Unidad Curricular', 5, 0, 3, 3, 2, 1, 3, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:34', '2026-07-19 17:39:35', 0),
(39, 'Matemática Aplicada - Cohorte 2026-01-3', 'PRE-41950', '2026-01-02', NULL, '2026-02-10', 109.00, 5.00, 109.00, 48, 10, 21, 2, 'Unidad Curricular', 5, 0, 1, 1, 7, 1, 1, 66, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:35', '2026-07-19 17:39:37', 0),
(40, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2026-02-1', 'PRE-85091', '2026-02-13', NULL, '2026-03-25', 75.00, 5.00, 75.00, 42, 10, 26, 3, 'Unidad Curricular', 5, 0, 3, 1, 7, 4, 3, 6, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:37', '2026-07-19 17:39:39', 0),
(41, 'Alfabetización Tecnológica - Cohorte 2026-02-2', 'PRE-31714', '2026-02-09', NULL, '2026-03-14', 155.00, 4.00, 155.00, 49, 10, 30, 2, 'Unidad Curricular', 5, 0, 1, 2, 9, 2, 2, 49, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:39', '2026-07-19 17:39:41', 0),
(42, 'Base de Datos - Cohorte 2026-02-3', 'PRE-97624', '2026-02-07', NULL, '2026-03-13', 141.00, 4.00, 141.00, 50, 10, 22, 1, 'Unidad Curricular', 5, 0, 3, 3, 6, 5, 2, 52, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:41', '2026-07-19 17:39:42', 0),
(43, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2026-03-1', 'PRE-46473', '2026-03-03', NULL, '2026-04-08', 81.00, 5.00, 81.00, 40, 10, 18, 1, 'Unidad Curricular', 5, 0, 3, 1, 5, 3, 5, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:42', '2026-07-19 17:39:43', 0),
(44, 'Matemática Aplicada - Cohorte 2026-03-2', 'PRE-65491', '2026-03-04', NULL, '2026-04-12', 109.00, 4.00, 109.00, 49, 10, 30, 2, 'Unidad Curricular', 5, 0, 3, 3, 4, 3, 1, 66, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:43', '2026-07-19 17:39:45', 0),
(45, 'Procesos Constructivos Tradicionales - Cohorte 2026-03-3', 'PRE-67351', '2026-03-12', NULL, '2026-04-14', 88.00, 4.00, 88.00, 46, 10, 29, 3, 'Unidad Curricular', 5, 0, 1, 1, 9, 5, 3, 69, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:45', '2026-07-19 17:39:47', 0),
(46, 'Comunicación Digital - Cohorte 2026-04-1', 'PRE-96944', '2026-04-10', NULL, '2026-05-10', 124.00, 5.00, 124.00, 42, 10, 17, 3, 'Unidad Curricular', 5, 0, 1, 1, 6, 2, 5, 63, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:47', '2026-07-19 17:39:47', 0),
(47, 'Arquitectura del Computador - Cohorte 2026-04-2', 'PRE-12499', '2026-04-13', NULL, '2026-05-14', 87.00, 4.00, 87.00, 46, 10, 19, 2, 'Unidad Curricular', 5, 0, 3, 3, 5, 2, 2, 50, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:47', '2026-07-19 17:39:48', 0),
(48, 'Proyecto Socio Tecnológico - Cohorte 2026-04-3', 'PRE-91236', '2026-04-10', NULL, '2026-05-16', 106.00, 4.00, 106.00, 49, 10, 15, 2, 'Unidad Curricular', 5, 0, 1, 1, 4, 3, 2, 56, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:48', '2026-07-19 17:39:49', 0),
(49, 'NUEVO MODELO DE GESTIÓN PRODUCTIVA - Cohorte 2026-05-1', 'PRE-20606', '2026-05-08', NULL, '2026-06-15', 134.00, 5.00, 134.00, 43, 10, 27, 3, 'Unidad Curricular', 5, 0, 1, 2, 9, 3, 1, 8, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:49', '2026-07-19 17:39:51', 0),
(50, 'Política Internacional - Cohorte 2026-05-2', 'PRE-35887', '2026-05-06', NULL, '2026-06-11', 141.00, 4.00, 141.00, 44, 10, 15, 3, 'Unidad Curricular', 5, 0, 2, 2, 9, 2, 5, 62, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:51', '2026-07-19 17:39:52', 0),
(51, 'Producción de Contenidos - Cohorte 2026-05-3', 'PRE-43996', '2026-05-12', NULL, '2026-06-20', 99.00, 4.00, 99.00, 41, 10, 22, 3, 'Unidad Curricular', 5, 0, 3, 1, 9, 4, 5, 65, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:52', '2026-07-19 17:39:53', 0),
(52, 'Ofimática - Cohorte 2026-06-1', 'PRE-87604', '2026-06-11', NULL, '2026-07-16', 158.00, 5.00, 158.00, 43, 10, 29, 1, 'Unidad Curricular', 5, 0, 2, 2, 8, 1, 4, 60, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:53', '2026-07-19 17:39:54', 0),
(53, 'Matemática Aplicada - Cohorte 2026-06-2', 'PRE-75707', '2026-06-06', NULL, '2026-07-10', 113.00, 4.00, 113.00, 45, 10, 24, 3, 'Unidad Curricular', 5, 0, 3, 2, 5, 2, 1, 66, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:54', '2026-07-19 17:39:57', 0),
(54, 'Redes de Computadoras - Cohorte 2026-06-3', 'PRE-18798', '2026-06-12', NULL, '2026-07-20', 96.00, 5.00, 38.40, 49, 10, 25, 1, 'Unidad Curricular', 3, 0, 1, 3, 6, 4, 2, 53, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:57', '2026-07-19 17:39:59', 0),
(55, 'Redes de Computadoras - Cohorte 2026-07-1', 'PRE-98959', '2026-07-08', NULL, '2026-08-07', 142.00, 5.00, 56.80, 44, 10, 26, 1, 'Unidad Curricular', 3, 0, 2, 3, 7, 1, 2, 53, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:39:59', '2026-07-19 17:40:00', 0),
(56, 'Procesos Constructivos Tradicionales - Cohorte 2026-07-2', 'PRE-93754', '2026-07-02', NULL, '2026-08-01', 153.00, 4.00, 61.20, 44, 10, 19, 2, 'Unidad Curricular', 3, 0, 1, 2, 8, 5, 3, 69, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:00', '2026-07-19 17:40:02', 0),
(57, 'Alfabetización Tecnológica - Cohorte 2026-07-3', 'PRE-50333', '2026-07-14', NULL, '2026-08-17', 85.00, 5.00, 34.00, 42, 10, 20, 3, 'Unidad Curricular', 3, 0, 2, 1, 10, 4, 2, 49, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:02', '2026-07-19 17:40:03', 0),
(58, 'Matemática Aplicada - Cohorte 2026-08-1', 'PRE-22994', '2026-08-12', NULL, '2026-09-17', 150.00, 4.00, 0.00, 41, 10, 16, 1, 'Unidad Curricular', 1, 0, 3, 3, 6, 5, 1, 66, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:03', '2026-07-19 17:40:04', 0),
(59, 'Procesos Constructivos Tradicionales - Cohorte 2026-08-2', 'PRE-72628', '2026-08-14', NULL, '2026-09-19', 90.00, 5.00, 0.00, 41, 10, 23, 1, 'Unidad Curricular', 1, 0, 3, 1, 10, 4, 3, 69, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:04', '2026-07-19 17:40:06', 0),
(60, 'ORTOGRAFÍA Y REDACCIÓN - Cohorte 2026-08-3', 'PRE-63886', '2026-08-02', NULL, '2026-09-02', 135.00, 5.00, 0.00, 49, 10, 15, 1, 'Unidad Curricular', 1, 0, 1, 1, 4, 2, 5, 6, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:06', '2026-07-19 17:40:07', 0),
(61, 'Comunicación Organizacional - Cohorte 2026-09-1', 'PRE-46982', '2026-09-10', NULL, '2026-10-10', 106.00, 4.00, 0.00, 46, 10, 21, 3, 'Unidad Curricular', 1, 0, 3, 3, 5, 2, 4, 61, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:07', '2026-07-19 17:40:09', 0),
(62, 'LECTURA E INTERPRETACIÓN DE PLANOS - Cohorte 2026-09-2', 'PRE-19185', '2026-09-03', NULL, '2026-10-11', 90.00, 5.00, 0.00, 46, 10, 27, 1, 'Unidad Curricular', 1, 0, 3, 1, 4, 2, 1, 24, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:09', '2026-07-19 17:40:12', 0),
(63, 'Arquitectura del Computador - Cohorte 2026-09-3', 'PRE-39140', '2026-09-13', NULL, '2026-10-20', 157.00, 5.00, 0.00, 43, 10, 26, 3, 'Unidad Curricular', 1, 0, 1, 3, 3, 3, 2, 50, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-07-19 17:40:12', '2026-07-19 17:40:15', 0);

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
(1, 'Pedro', 'Carmen', 'Ramirez', 'Martinez', 15000000, 'docente0@example.com', '1987-06-19', 39, 'Asignado', 'Femenino', 'Contratado'),
(2, 'Maria', 'Ana', 'Rodriguez', 'Garcia', 15000001, 'docente1@example.com', '1988-05-15', 38, 'Asignado', 'Femenino', 'Contratado'),
(3, 'Ana', 'Juan', 'Garcia', 'Martinez', 15000002, 'docente2@example.com', '1989-01-15', 37, 'Asignado', 'Masculino', 'Contratado'),
(4, 'Maria', 'Ana', 'Rodriguez', 'Diaz', 15000003, 'docente3@example.com', '1985-06-16', 41, 'Asignado', 'Masculino', 'Fijo'),
(5, 'Rosa', 'Juan', 'Gonzalez', 'Garcia', 15000004, 'docente4@example.com', '1983-02-11', 43, 'Asignado', 'Femenino', 'Contratado');

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
(1, 3, 1, '2024-12-28'),
(2, 1, 2, '2025-01-06'),
(3, 4, 3, '2025-01-01'),
(4, 2, 4, '2025-02-10'),
(5, 5, 5, '2025-02-02'),
(6, 1, 7, '2025-03-03'),
(7, 4, 8, '2025-02-27'),
(8, 3, 9, '2025-03-08'),
(9, 2, 10, '2025-03-31'),
(10, 4, 11, '2025-04-01'),
(11, 5, 12, '2025-04-06'),
(12, 3, 13, '2025-05-02'),
(13, 5, 14, '2025-04-30'),
(14, 3, 15, '2025-05-05'),
(15, 1, 16, '2025-06-02'),
(16, 2, 17, '2025-06-04'),
(17, 4, 18, '2025-06-10'),
(18, 5, 19, '2025-06-30'),
(19, 3, 20, '2025-06-28'),
(20, 1, 21, '2025-07-08'),
(21, 5, 22, '2025-08-05'),
(22, 4, 23, '2025-08-02'),
(23, 3, 24, '2025-08-01'),
(24, 1, 25, '2025-09-04'),
(25, 5, 26, '2025-09-10'),
(26, 2, 27, '2025-08-31'),
(27, 4, 28, '2025-09-30'),
(28, 3, 29, '2025-09-28'),
(29, 3, 31, '2025-11-08'),
(30, 1, 32, '2025-11-06'),
(31, 5, 33, '2025-11-05'),
(32, 2, 34, '2025-12-01'),
(33, 5, 35, '2025-12-10'),
(34, 4, 36, '2025-12-06'),
(35, 1, 37, '2025-12-29'),
(36, 3, 38, '2026-01-02'),
(37, 1, 40, '2026-02-08'),
(38, 5, 41, '2026-02-04'),
(39, 2, 42, '2026-02-02'),
(40, 4, 43, '2026-02-26'),
(41, 3, 44, '2026-02-27'),
(42, 3, 45, '2026-03-07'),
(43, 5, 46, '2026-04-05'),
(44, 2, 47, '2026-04-08'),
(45, 2, 48, '2026-04-05'),
(46, 3, 49, '2026-05-03'),
(47, 4, 50, '2026-05-01'),
(48, 1, 51, '2026-05-07'),
(49, 2, 52, '2026-06-06'),
(50, 5, 53, '2026-06-01'),
(51, 4, 54, '2026-06-07'),
(52, 1, 55, '2026-07-03'),
(53, 5, 56, '2026-06-27'),
(54, 3, 57, '2026-07-09'),
(55, 1, 58, '2026-08-07'),
(56, 5, 59, '2026-08-09'),
(57, 2, 60, '2026-07-28'),
(58, 3, 61, '2026-09-05'),
(59, 2, 62, '2026-08-29'),
(60, 4, 63, '2026-09-08');

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
(1, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-16 21:34:45'),
(2, 'Logout', 'Usuario samu cerró sesión', 'tb_usuario', 16, 16, '2026-07-16 21:35:03'),
(3, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-16 21:35:11'),
(4, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-16 21:47:38'),
(5, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-16 21:58:19'),
(6, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-16 23:29:14'),
(7, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 00:12:08'),
(8, 'Crear', 'Se creó el curso: Comunicación Organizacional', 'tb_curso', 31, 16, '2026-07-17 00:42:57'),
(9, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 00:56:28'),
(10, 'Eliminar', 'Se eliminó la meta:  (482)', 'tb_metas', 14, 16, '2026-07-17 02:00:09'),
(11, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 03:43:23'),
(12, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 04:34:06'),
(13, 'Logout', 'Usuario samu cerró sesión', 'tb_usuario', 16, 16, '2026-07-17 04:42:57'),
(14, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 04:43:45'),
(15, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 04:49:21'),
(16, 'Logout', 'Usuario samu cerró sesión', 'tb_usuario', 16, 16, '2026-07-17 05:07:32'),
(17, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 10:38:05'),
(18, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-17 11:07:32'),
(19, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-07-19 15:24:56');

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
(1, NULL, 20, 29, 'Aprobados', '2025-08-01', '2025-09-30', 'Cerrada', '2026-05-25 01:30:46', 0, 'Trimestre III 2025'),
(2, 1, 25, 30, 'Inscritos', '2025-10-01', '2025-12-31', 'Cerrada', '2026-05-25 01:30:46', 0, 'Inscritos CFS T-IV'),
(3, NULL, 80, 64, 'Aprobados', '2025-10-01', '2025-12-31', 'Cerrada', '2026-05-25 01:30:47', 0, 'Aprobados Globales T'),
(4, NULL, 35, 106, 'Aprobados', '2026-01-01', '2026-03-31', 'Cerrada', '2026-05-25 01:30:47', 0, 'Aprobados Globales T'),
(5, NULL, 150, 562, 'Inscritos', '2026-01-01', '2026-12-31', 'Activa', '2026-05-25 01:30:47', 0, 'Semestre I 2026'),
(6, 2, 15, 70, 'Aprobados', '2026-04-01', '2026-07-31', 'Activa', '2026-05-25 01:30:47', 0, 'Comunas Abr-Jul'),
(9, 1, 20, 27, 'Aprobados', '2026-07-01', '2026-12-31', 'Activa', '2026-07-16 09:00:00', 0, 'CFS Trimestre III - '),
(10, 2, 20, 24, 'Aprobados', '2026-07-01', '2026-12-31', 'Activa', '2026-07-16 09:00:00', 0, 'CFS Trimestre III - '),
(11, 3, 22, 0, 'Aprobados', '2026-07-01', '2026-12-31', 'Activa', '2026-07-16 09:00:00', 0, 'CFS Trimestre III - '),
(12, 4, 20, 0, 'Aprobados', '2026-07-01', '2026-12-31', 'Activa', '2026-07-16 09:00:00', 0, 'CFS Trimestre III - '),
(13, 5, 37, 0, 'Aprobados', '2026-07-01', '2026-12-31', 'Activa', '2026-07-16 09:00:00', 0, 'CFS Trimestre III - ');

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
  `estado` enum('En formación','Inactivo','Disponible','Asignado','En sistema','Diferido') NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `id_discapacidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
(1, 'Jose', 'Carlos', 'Martinez', 'Garcia', 20000000, 26, 'participante0@example.com', '2000-06-19', '2026-03-14', 'En formación', 'Universidad', 'Femenino', NULL),
(2, 'Rosa', 'Laura', 'Martinez', 'Hernandez', 20000001, 21, 'participante1@example.com', '2005-02-16', '2026-06-09', 'En formación', 'Bachillerato', 'Masculino', NULL),
(3, 'Marta', 'Rosa', 'Perez', 'Martinez', 20000002, 21, 'participante2@example.com', '2004-09-13', '2026-07-02', 'Asignado', 'Universidad', 'Masculino', NULL),
(4, 'Luis', 'Carlos', 'Suarez', 'Perez', 20000003, 22, 'participante3@example.com', '2004-06-10', '2026-02-10', 'En formación', 'Universidad', 'Masculino', NULL),
(5, 'Maria', 'Marta', 'Torres', 'Perez', 20000004, 25, 'participante4@example.com', '2000-08-16', '2026-02-21', 'Disponible', 'Primaria', 'Masculino', NULL),
(6, 'Patricia', 'Maria', 'Ramirez', 'Torres', 20000005, 20, 'participante5@example.com', '2005-09-16', '2026-05-15', 'Asignado', 'Primaria', 'Femenino', NULL),
(7, 'Maria', 'Jesus', 'Suarez', 'Hernandez', 20000006, 26, 'participante6@example.com', '2000-05-14', '2026-05-06', 'En formación', 'Bachillerato', 'Masculino', NULL),
(8, 'Carlos', 'Maria', 'Diaz', 'Suarez', 20000007, 24, 'participante7@example.com', '2001-08-19', '2026-01-27', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(9, 'Ana', 'Jose', 'Garcia', 'Garcia', 20000008, 24, 'participante8@example.com', '2002-05-16', '2026-05-29', 'En formación', 'Bachillerato', 'Femenino', NULL),
(10, 'Ana', 'Maria', 'Garcia', 'Gonzalez', 20000009, 22, 'participante9@example.com', '2004-02-17', '2026-03-15', 'En formación', 'Bachillerato', 'Femenino', NULL),
(11, 'Carmen', 'Jesus', 'Perez', 'Garcia', 20000010, 21, 'participante10@example.com', '2004-09-15', '2026-04-30', 'En formación', 'Bachillerato', 'Masculino', NULL),
(12, 'Laura', 'Ana', 'Hernandez', 'Diaz', 20000011, 23, 'participante11@example.com', '2002-07-19', '2026-06-29', 'En formación', 'Primaria', 'Femenino', NULL),
(13, 'Juan', 'Patricia', 'Lopez', 'Torres', 20000012, 22, 'participante12@example.com', '2004-03-19', '2026-05-25', 'En formación', 'Universidad', 'Femenino', NULL),
(14, 'Marta', 'Carmen', 'Sanchez', 'Suarez', 20000013, 24, 'participante13@example.com', '2001-07-17', '2026-07-05', 'Asignado', 'Universidad', 'Masculino', NULL),
(15, 'Laura', 'Carlos', 'Hernandez', 'Torres', 20000014, 26, 'participante14@example.com', '2000-03-14', '2026-04-11', 'Disponible', 'Primaria', 'Femenino', NULL),
(16, 'Laura', 'Marta', 'Torres', 'Gonzalez', 20000015, 21, 'participante15@example.com', '2005-04-16', '2026-05-22', 'En formación', 'Universidad', 'Femenino', NULL),
(17, 'Patricia', 'Laura', 'Rodriguez', 'Garcia', 20000016, 23, 'participante16@example.com', '2003-05-11', '2026-01-14', 'En formación', 'Primaria', 'Masculino', NULL),
(18, 'Carlos', 'Rosa', 'Hernandez', 'Martinez', 20000017, 22, 'participante17@example.com', '2003-08-18', '2026-02-04', 'En formación', 'Universidad', 'Masculino', NULL),
(19, 'Jose', 'Marta', 'Hernandez', 'Hernandez', 20000018, 26, 'participante18@example.com', '2000-05-14', '2026-05-12', 'En formación', 'Bachillerato', 'Masculino', NULL),
(20, 'Luis', 'Miguel', 'Perez', 'Perez', 20000019, 25, 'participante19@example.com', '2001-03-15', '2026-06-12', 'En formación', 'Primaria', 'Femenino', NULL),
(21, 'Jesus', 'Rosa', 'Ramirez', 'Diaz', 20000020, 22, 'participante20@example.com', '2004-01-15', '2026-01-23', 'En formación', 'Primaria', 'Masculino', NULL),
(22, 'Ana', 'Pedro', 'Diaz', 'Martinez', 20000021, 24, 'participante21@example.com', '2002-03-18', '2026-01-19', 'Asignado', 'Primaria', 'Masculino', NULL),
(23, 'Marta', 'Jesus', 'Torres', 'Gonzalez', 20000022, 25, 'participante22@example.com', '2000-09-12', '2026-01-24', 'En formación', 'Primaria', 'Masculino', NULL),
(24, 'Carmen', 'Carmen', 'Martinez', 'Gonzalez', 20000023, 23, 'participante23@example.com', '2003-03-15', '2026-03-07', 'En formación', 'Universidad', 'Masculino', NULL),
(25, 'Laura', 'Miguel', 'Ramirez', 'Lopez', 20000024, 23, 'participante24@example.com', '2003-05-13', '2026-02-26', 'Disponible', 'Bachillerato', 'Femenino', NULL),
(26, 'Maria', 'Marta', 'Ramirez', 'Sanchez', 20000025, 23, 'participante25@example.com', '2003-03-15', '2026-03-29', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(27, 'Ana', 'Miguel', 'Perez', 'Suarez', 20000026, 22, 'participante26@example.com', '2003-08-10', '2026-03-13', 'En formación', 'Bachillerato', 'Femenino', NULL),
(28, 'Miguel', 'Ana', 'Torres', 'Suarez', 20000027, 25, 'participante27@example.com', '2001-02-12', '2026-04-07', 'Asignado', 'Primaria', 'Masculino', NULL),
(29, 'Jesus', 'Ana', 'Garcia', 'Hernandez', 20000028, 25, 'participante28@example.com', '2001-03-10', '2026-03-10', 'Disponible', 'Universidad', 'Femenino', NULL),
(30, 'Marta', 'Luis', 'Suarez', 'Perez', 20000029, 21, 'participante29@example.com', '2004-07-19', '2026-02-23', 'Disponible', 'Primaria', 'Masculino', NULL),
(31, 'Marta', 'Carmen', 'Hernandez', 'Lopez', 20000030, 22, 'participante30@example.com', '2003-09-14', '2026-03-03', 'En formación', 'Universidad', 'Masculino', NULL),
(32, 'Pedro', 'Maria', 'Torres', 'Gonzalez', 20000031, 23, 'participante31@example.com', '2003-04-15', '2026-05-29', 'Asignado', 'Universidad', 'Femenino', NULL),
(33, 'Miguel', 'Ana', 'Garcia', 'Martinez', 20000032, 22, 'participante32@example.com', '2004-03-17', '2026-06-11', 'En formación', 'Primaria', 'Masculino', NULL),
(34, 'Ana', 'Marta', 'Diaz', 'Gonzalez', 20000033, 26, 'participante33@example.com', '2000-03-10', '2026-06-15', 'Asignado', 'Primaria', 'Masculino', NULL),
(35, 'Marta', 'Pedro', 'Gonzalez', 'Rodriguez', 20000034, 26, 'participante34@example.com', '2000-01-11', '2026-05-28', 'Asignado', 'Universidad', 'Masculino', NULL),
(36, 'Carmen', 'Miguel', 'Rodriguez', 'Diaz', 20000035, 20, 'participante35@example.com', '2005-09-11', '2026-02-12', 'Disponible', 'Primaria', 'Femenino', NULL),
(37, 'Luis', 'Jose', 'Ramirez', 'Sanchez', 20000036, 25, 'participante36@example.com', '2001-04-18', '2026-06-12', 'En formación', 'Primaria', 'Masculino', NULL),
(38, 'Laura', 'Laura', 'Gonzalez', 'Perez', 20000037, 24, 'participante37@example.com', '2002-03-13', '2026-04-07', 'En formación', 'Primaria', 'Masculino', NULL),
(39, 'Jose', 'Jesus', 'Lopez', 'Rodriguez', 20000038, 23, 'participante38@example.com', '2003-07-14', '2026-07-09', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(40, 'Luis', 'Jesus', 'Suarez', 'Torres', 20000039, 21, 'participante39@example.com', '2005-03-19', '2026-03-10', 'En formación', 'Primaria', 'Masculino', NULL),
(41, 'Pedro', 'Pedro', 'Martinez', 'Garcia', 20000040, 22, 'participante40@example.com', '2004-06-11', '2026-06-19', 'En formación', 'Universidad', 'Masculino', NULL),
(42, 'Carmen', 'Ana', 'Diaz', 'Perez', 20000041, 26, 'participante41@example.com', '2000-01-14', '2026-02-17', 'En formación', 'Primaria', 'Masculino', NULL),
(43, 'Pedro', 'Luis', 'Gonzalez', 'Suarez', 20000042, 26, 'participante42@example.com', '2000-07-12', '2026-05-07', 'Disponible', 'Bachillerato', 'Masculino', NULL),
(44, 'Pedro', 'Rosa', 'Martinez', 'Martinez', 20000043, 21, 'participante43@example.com', '2005-07-12', '2026-04-05', 'En formación', 'Bachillerato', 'Femenino', NULL),
(45, 'Juan', 'Jose', 'Martinez', 'Rodriguez', 20000044, 21, 'participante44@example.com', '2004-08-14', '2026-04-04', 'Asignado', 'Primaria', 'Femenino', NULL),
(46, 'Jesus', 'Carlos', 'Perez', 'Lopez', 20000045, 20, 'participante45@example.com', '2005-09-12', '2026-07-02', 'En formación', 'Bachillerato', 'Masculino', NULL),
(47, 'Jose', 'Rosa', 'Ramirez', 'Torres', 20000046, 25, 'participante46@example.com', '2001-06-17', '2026-02-10', 'En formación', 'Primaria', 'Masculino', NULL),
(48, 'Miguel', 'Jesus', 'Martinez', 'Ramirez', 20000047, 23, 'participante47@example.com', '2003-02-17', '2026-02-19', 'En formación', 'Primaria', 'Masculino', NULL),
(49, 'Marta', 'Pedro', 'Sanchez', 'Garcia', 20000048, 22, 'participante48@example.com', '2004-03-18', '2026-05-09', 'En formación', 'Bachillerato', 'Femenino', NULL),
(50, 'Marta', 'Maria', 'Suarez', 'Ramirez', 20000049, 21, 'participante49@example.com', '2005-04-19', '2026-04-09', 'En formación', 'Universidad', 'Masculino', NULL),
(51, 'Marta', 'Miguel', 'Hernandez', 'Perez', 20000050, 25, 'participante50@example.com', '2001-05-15', '2026-04-16', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(52, 'Jesus', 'Pedro', 'Sanchez', 'Diaz', 20000051, 25, 'participante51@example.com', '2001-01-10', '2026-02-04', 'En formación', 'Bachillerato', 'Femenino', NULL),
(53, 'Laura', 'Carmen', 'Suarez', 'Gonzalez', 20000052, 23, 'participante52@example.com', '2003-05-16', '2026-02-25', 'En formación', 'Universidad', 'Femenino', NULL),
(54, 'Carlos', 'Ana', 'Perez', 'Rodriguez', 20000053, 24, 'participante53@example.com', '2002-07-15', '2026-06-23', 'Disponible', 'Primaria', 'Masculino', NULL),
(55, 'Jesus', 'Maria', 'Lopez', 'Hernandez', 20000054, 23, 'participante54@example.com', '2003-05-13', '2026-04-18', 'En formación', 'Bachillerato', 'Femenino', NULL),
(56, 'Luis', 'Juan', 'Diaz', 'Hernandez', 20000055, 25, 'participante55@example.com', '2001-05-17', '2026-01-20', 'En formación', 'Bachillerato', 'Femenino', NULL),
(57, 'Jesus', 'Ana', 'Martinez', 'Lopez', 20000056, 22, 'participante56@example.com', '2004-04-15', '2026-06-20', 'En formación', 'Bachillerato', 'Femenino', NULL),
(58, 'Pedro', 'Laura', 'Garcia', 'Ramirez', 20000057, 23, 'participante57@example.com', '2003-05-15', '2026-01-03', 'En formación', 'Primaria', 'Femenino', NULL),
(59, 'Carlos', 'Jesus', 'Ramirez', 'Rodriguez', 20000058, 23, 'participante58@example.com', '2003-06-19', '2026-04-04', 'En formación', 'Universidad', 'Masculino', NULL),
(60, 'Carmen', 'Jesus', 'Torres', 'Perez', 20000059, 26, 'participante59@example.com', '2000-05-15', '2026-03-08', 'Asignado', 'Primaria', 'Masculino', NULL),
(61, 'Carlos', 'Miguel', 'Hernandez', 'Lopez', 20000060, 25, 'participante60@example.com', '2000-09-18', '2026-02-21', 'Asignado', 'Universidad', 'Femenino', NULL),
(62, 'Marta', 'Pedro', 'Sanchez', 'Suarez', 20000061, 24, 'participante61@example.com', '2002-04-16', '2026-02-28', 'En formación', 'Universidad', 'Femenino', NULL),
(63, 'Maria', 'Jesus', 'Lopez', 'Torres', 20000062, 23, 'participante62@example.com', '2003-04-16', '2026-05-19', 'En formación', 'Universidad', 'Femenino', NULL),
(64, 'Patricia', 'Carlos', 'Gonzalez', 'Gonzalez', 20000063, 24, 'participante63@example.com', '2001-09-16', '2026-05-02', 'En formación', 'Bachillerato', 'Masculino', NULL),
(65, 'Patricia', 'Juan', 'Perez', 'Martinez', 20000064, 24, 'participante64@example.com', '2001-08-19', '2026-07-13', 'En formación', 'Bachillerato', 'Masculino', NULL),
(66, 'Patricia', 'Juan', 'Sanchez', 'Ramirez', 20000065, 24, 'participante65@example.com', '2001-07-18', '2026-01-14', 'Disponible', 'Bachillerato', 'Femenino', NULL),
(67, 'Maria', 'Juan', 'Torres', 'Torres', 20000066, 23, 'participante66@example.com', '2003-03-12', '2026-03-18', 'Disponible', 'Bachillerato', 'Femenino', NULL),
(68, 'Jose', 'Miguel', 'Garcia', 'Lopez', 20000067, 22, 'participante67@example.com', '2004-04-13', '2026-05-25', 'En formación', 'Primaria', 'Femenino', NULL),
(69, 'Maria', 'Jose', 'Diaz', 'Suarez', 20000068, 23, 'participante68@example.com', '2003-03-17', '2026-04-11', 'En formación', 'Primaria', 'Femenino', NULL),
(70, 'Jose', 'Jose', 'Torres', 'Garcia', 20000069, 22, 'participante69@example.com', '2004-04-15', '2026-03-11', 'En formación', 'Bachillerato', 'Femenino', NULL),
(71, 'Jose', 'Maria', 'Rodriguez', 'Ramirez', 20000070, 21, 'participante70@example.com', '2005-01-18', '2026-02-14', 'En formación', 'Universidad', 'Masculino', NULL),
(72, 'Luis', 'Ana', 'Hernandez', 'Suarez', 20000071, 22, 'participante71@example.com', '2003-09-11', '2026-01-15', 'En formación', 'Primaria', 'Masculino', NULL),
(73, 'Marta', 'Marta', 'Garcia', 'Perez', 20000072, 21, 'participante72@example.com', '2004-07-18', '2026-05-18', 'En formación', 'Bachillerato', 'Femenino', NULL),
(74, 'Carlos', 'Pedro', 'Martinez', 'Suarez', 20000073, 25, 'participante73@example.com', '2001-01-11', '2026-02-22', 'En formación', 'Universidad', 'Femenino', NULL),
(75, 'Jose', 'Laura', 'Perez', 'Lopez', 20000074, 23, 'participante74@example.com', '2003-01-16', '2026-02-16', 'Asignado', 'Primaria', 'Masculino', NULL),
(76, 'Jesus', 'Rosa', 'Torres', 'Garcia', 20000075, 23, 'participante75@example.com', '2003-01-19', '2026-03-16', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(77, 'Luis', 'Juan', 'Gonzalez', 'Martinez', 20000076, 20, 'participante76@example.com', '2005-09-16', '2026-02-06', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(78, 'Carmen', 'Jesus', 'Diaz', 'Suarez', 20000077, 21, 'participante77@example.com', '2005-06-19', '2026-06-06', 'Asignado', 'Primaria', 'Masculino', NULL),
(79, 'Pedro', 'Ana', 'Sanchez', 'Ramirez', 20000078, 23, 'participante78@example.com', '2003-02-13', '2026-03-24', 'En formación', 'Primaria', 'Masculino', NULL),
(80, 'Pedro', 'Pedro', 'Torres', 'Torres', 20000079, 21, 'participante79@example.com', '2005-06-16', '2026-05-21', 'En formación', 'Primaria', 'Femenino', NULL),
(81, 'Luis', 'Carlos', 'Sanchez', 'Diaz', 20000080, 25, 'participante80@example.com', '2000-09-17', '2026-02-28', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(82, 'Pedro', 'Miguel', 'Perez', 'Hernandez', 20000081, 26, 'participante81@example.com', '2000-05-19', '2026-03-13', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(83, 'Miguel', 'Rosa', 'Perez', 'Diaz', 20000082, 25, 'participante82@example.com', '2001-03-18', '2026-06-28', 'En formación', 'Universidad', 'Masculino', NULL),
(84, 'Juan', 'Jesus', 'Torres', 'Martinez', 20000083, 23, 'participante83@example.com', '2003-07-13', '2026-03-30', 'En formación', 'Primaria', 'Masculino', NULL),
(85, 'Carmen', 'Luis', 'Torres', 'Gonzalez', 20000084, 24, 'participante84@example.com', '2002-07-13', '2026-04-16', 'En formación', 'Primaria', 'Femenino', NULL),
(86, 'Pedro', 'Luis', 'Ramirez', 'Rodriguez', 20000085, 21, 'participante85@example.com', '2005-03-17', '2026-03-04', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(87, 'Luis', 'Marta', 'Rodriguez', 'Lopez', 20000086, 22, 'participante86@example.com', '2003-09-11', '2026-07-15', 'En formación', 'Primaria', 'Femenino', NULL),
(88, 'Patricia', 'Laura', 'Hernandez', 'Hernandez', 20000087, 22, 'participante87@example.com', '2004-03-17', '2026-01-01', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(89, 'Carmen', 'Jose', 'Lopez', 'Rodriguez', 20000088, 25, 'participante88@example.com', '2001-03-18', '2026-01-09', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(90, 'Luis', 'Jose', 'Torres', 'Perez', 20000089, 24, 'participante89@example.com', '2002-03-10', '2026-02-11', 'Asignado', 'Primaria', 'Masculino', NULL),
(91, 'Laura', 'Rosa', 'Lopez', 'Gonzalez', 20000090, 23, 'participante90@example.com', '2002-09-12', '2026-07-02', 'En formación', 'Universidad', 'Masculino', NULL),
(92, 'Maria', 'Jose', 'Gonzalez', 'Gonzalez', 20000091, 23, 'participante91@example.com', '2003-04-15', '2026-07-14', 'En formación', 'Primaria', 'Femenino', NULL),
(93, 'Marta', 'Carmen', 'Lopez', 'Gonzalez', 20000092, 24, 'participante92@example.com', '2002-06-16', '2026-02-03', 'En formación', 'Primaria', 'Masculino', NULL),
(94, 'Rosa', 'Carmen', 'Martinez', 'Gonzalez', 20000093, 25, 'participante93@example.com', '2001-03-15', '2026-06-24', 'Asignado', 'Primaria', 'Femenino', NULL),
(95, 'Jesus', 'Luis', 'Sanchez', 'Lopez', 20000094, 22, 'participante94@example.com', '2004-07-13', '2026-07-02', 'En formación', 'Primaria', 'Femenino', NULL),
(96, 'Maria', 'Patricia', 'Garcia', 'Hernandez', 20000095, 23, 'participante95@example.com', '2002-08-19', '2026-07-08', 'En formación', 'Bachillerato', 'Femenino', NULL),
(97, 'Ana', 'Pedro', 'Sanchez', 'Lopez', 20000096, 24, 'participante96@example.com', '2002-05-14', '2026-01-05', 'En formación', 'Primaria', 'Masculino', NULL),
(98, 'Jose', 'Patricia', 'Suarez', 'Garcia', 20000097, 20, 'participante97@example.com', '2005-08-13', '2026-02-17', 'Disponible', 'Bachillerato', 'Masculino', NULL),
(99, 'Juan', 'Pedro', 'Hernandez', 'Gonzalez', 20000098, 26, 'participante98@example.com', '2000-02-15', '2026-01-25', 'En formación', 'Universidad', 'Masculino', NULL),
(100, 'Pedro', 'Laura', 'Perez', 'Gonzalez', 20000099, 22, 'participante99@example.com', '2004-05-10', '2026-06-29', 'Asignado', 'Universidad', 'Masculino', NULL),
(101, 'Miguel', 'Pedro', 'Diaz', 'Lopez', 20000100, 26, 'participante100@example.com', '2000-02-15', '2026-02-04', 'Asignado', 'Universidad', 'Masculino', NULL),
(102, 'Rosa', 'Patricia', 'Martinez', 'Lopez', 20000101, 25, 'participante101@example.com', '2000-09-16', '2026-01-28', 'Disponible', 'Bachillerato', 'Masculino', NULL),
(103, 'Maria', 'Maria', 'Suarez', 'Lopez', 20000102, 24, 'participante102@example.com', '2002-01-14', '2026-02-04', 'En formación', 'Universidad', 'Masculino', NULL),
(104, 'Carlos', 'Miguel', 'Rodriguez', 'Rodriguez', 20000103, 25, 'participante103@example.com', '2001-05-18', '2026-03-31', 'Asignado', 'Universidad', 'Femenino', NULL),
(105, 'Luis', 'Laura', 'Sanchez', 'Sanchez', 20000104, 22, 'participante104@example.com', '2004-05-17', '2026-05-27', 'Disponible', 'Bachillerato', 'Masculino', NULL),
(106, 'Juan', 'Pedro', 'Hernandez', 'Martinez', 20000105, 24, 'participante105@example.com', '2002-05-10', '2026-03-14', 'En formación', 'Universidad', 'Masculino', NULL),
(107, 'Juan', 'Ana', 'Torres', 'Perez', 20000106, 24, 'participante106@example.com', '2002-06-14', '2026-04-28', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(108, 'Carmen', 'Carmen', 'Perez', 'Martinez', 20000107, 22, 'participante107@example.com', '2004-06-11', '2026-06-22', 'En formación', 'Universidad', 'Masculino', NULL),
(109, 'Patricia', 'Pedro', 'Ramirez', 'Suarez', 20000108, 26, 'participante108@example.com', '2000-04-12', '2026-04-28', 'En formación', 'Universidad', 'Masculino', NULL),
(110, 'Juan', 'Carmen', 'Diaz', 'Diaz', 20000109, 23, 'participante109@example.com', '2003-02-13', '2026-03-12', 'En formación', 'Universidad', 'Femenino', NULL),
(111, 'Maria', 'Juan', 'Lopez', 'Sanchez', 20000110, 25, 'participante110@example.com', '2001-05-19', '2026-01-01', 'Disponible', 'Bachillerato', 'Femenino', NULL),
(112, 'Jesus', 'Rosa', 'Martinez', 'Suarez', 20000111, 23, 'participante111@example.com', '2003-01-14', '2026-07-02', 'En formación', 'Bachillerato', 'Masculino', NULL),
(113, 'Miguel', 'Carmen', 'Gonzalez', 'Rodriguez', 20000112, 22, 'participante112@example.com', '2003-07-17', '2026-05-05', 'En formación', 'Universidad', 'Masculino', NULL),
(114, 'Carmen', 'Miguel', 'Rodriguez', 'Torres', 20000113, 21, 'participante113@example.com', '2005-03-12', '2026-03-17', 'Disponible', 'Universidad', 'Femenino', NULL),
(115, 'Rosa', 'Rosa', 'Lopez', 'Rodriguez', 20000114, 26, 'participante114@example.com', '2000-07-15', '2026-01-03', 'En formación', 'Universidad', 'Femenino', NULL),
(116, 'Carlos', 'Carmen', 'Perez', 'Hernandez', 20000115, 21, 'participante115@example.com', '2005-07-12', '2026-06-30', 'Asignado', 'Primaria', 'Masculino', NULL),
(117, 'Pedro', 'Maria', 'Torres', 'Suarez', 20000116, 25, 'participante116@example.com', '2001-04-17', '2026-04-16', 'En formación', 'Bachillerato', 'Femenino', NULL),
(118, 'Carmen', 'Laura', 'Sanchez', 'Ramirez', 20000117, 25, 'participante117@example.com', '2001-04-19', '2026-07-05', 'Disponible', 'Bachillerato', 'Femenino', NULL),
(119, 'Carmen', 'Jose', 'Sanchez', 'Hernandez', 20000118, 23, 'participante118@example.com', '2003-02-11', '2026-01-19', 'En formación', 'Primaria', 'Masculino', NULL),
(120, 'Jose', 'Carmen', 'Rodriguez', 'Suarez', 20000119, 21, 'participante119@example.com', '2004-08-14', '2026-05-07', 'En formación', 'Bachillerato', 'Femenino', NULL),
(121, 'Maria', 'Juan', 'Martinez', 'Suarez', 20000120, 25, 'participante120@example.com', '2001-07-12', '2026-07-02', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(122, 'Pedro', 'Miguel', 'Suarez', 'Suarez', 20000121, 25, 'participante121@example.com', '2001-01-14', '2026-05-21', 'En formación', 'Bachillerato', 'Masculino', NULL),
(123, 'Jose', 'Luis', 'Hernandez', 'Hernandez', 20000122, 22, 'participante122@example.com', '2004-07-13', '2026-06-05', 'Asignado', 'Primaria', 'Masculino', NULL),
(124, 'Jesus', 'Jose', 'Suarez', 'Martinez', 20000123, 23, 'participante123@example.com', '2002-08-10', '2026-06-06', 'En formación', 'Universidad', 'Femenino', NULL),
(125, 'Luis', 'Pedro', 'Diaz', 'Martinez', 20000124, 22, 'participante124@example.com', '2004-05-19', '2026-05-01', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(126, 'Juan', 'Patricia', 'Hernandez', 'Torres', 20000125, 26, 'participante125@example.com', '2000-06-11', '2026-05-15', 'Disponible', 'Primaria', 'Femenino', NULL),
(127, 'Pedro', 'Laura', 'Martinez', 'Gonzalez', 20000126, 23, 'participante126@example.com', '2002-08-11', '2026-04-25', 'En formación', 'Primaria', 'Masculino', NULL),
(128, 'Maria', 'Miguel', 'Torres', 'Suarez', 20000127, 25, 'participante127@example.com', '2001-07-11', '2026-06-19', 'En formación', 'Bachillerato', 'Femenino', NULL),
(129, 'Maria', 'Carlos', 'Sanchez', 'Hernandez', 20000128, 24, 'participante128@example.com', '2002-01-12', '2026-04-19', 'En formación', 'Bachillerato', 'Femenino', NULL),
(130, 'Juan', 'Ana', 'Suarez', 'Rodriguez', 20000129, 22, 'participante129@example.com', '2004-01-11', '2026-02-03', 'En formación', 'Bachillerato', 'Femenino', NULL),
(131, 'Miguel', 'Patricia', 'Ramirez', 'Garcia', 20000130, 25, 'participante130@example.com', '2001-01-10', '2026-02-09', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(132, 'Miguel', 'Carlos', 'Rodriguez', 'Martinez', 20000131, 22, 'participante131@example.com', '2004-01-12', '2026-04-09', 'Asignado', 'Primaria', 'Masculino', NULL),
(133, 'Luis', 'Luis', 'Hernandez', 'Suarez', 20000132, 24, 'participante132@example.com', '2001-08-19', '2026-06-28', 'En formación', 'Primaria', 'Femenino', NULL),
(134, 'Laura', 'Patricia', 'Perez', 'Suarez', 20000133, 21, 'participante133@example.com', '2005-03-11', '2026-01-03', 'En formación', 'Primaria', 'Masculino', NULL),
(135, 'Jose', 'Rosa', 'Lopez', 'Lopez', 20000134, 21, 'participante134@example.com', '2005-06-14', '2026-03-09', 'En formación', 'Bachillerato', 'Femenino', NULL),
(136, 'Carlos', 'Carlos', 'Hernandez', 'Sanchez', 20000135, 24, 'participante135@example.com', '2002-03-19', '2026-05-14', 'En formación', 'Primaria', 'Masculino', NULL),
(137, 'Juan', 'Juan', 'Ramirez', 'Perez', 20000136, 26, 'participante136@example.com', '2000-03-12', '2026-03-15', 'En formación', 'Bachillerato', 'Femenino', NULL),
(138, 'Jesus', 'Ana', 'Gonzalez', 'Garcia', 20000137, 25, 'participante137@example.com', '2000-08-19', '2026-06-13', 'En formación', 'Primaria', 'Femenino', NULL),
(139, 'Juan', 'Carlos', 'Torres', 'Sanchez', 20000138, 22, 'participante138@example.com', '2004-07-12', '2026-01-04', 'Asignado', 'Universidad', 'Masculino', NULL),
(140, 'Miguel', 'Ana', 'Sanchez', 'Gonzalez', 20000139, 25, 'participante139@example.com', '2001-05-11', '2026-05-01', 'Asignado', 'Bachillerato', 'Femenino', NULL),
(141, 'Carlos', 'Miguel', 'Lopez', 'Perez', 20000140, 21, 'participante140@example.com', '2005-02-16', '2026-01-02', 'En formación', 'Bachillerato', 'Masculino', NULL),
(142, 'Juan', 'Juan', 'Torres', 'Diaz', 20000141, 25, 'participante141@example.com', '2000-09-10', '2026-02-10', 'Asignado', 'Bachillerato', 'Masculino', NULL),
(143, 'Jesus', 'Miguel', 'Torres', 'Sanchez', 20000142, 25, 'participante142@example.com', '2001-04-14', '2026-07-16', 'En formación', 'Bachillerato', 'Femenino', NULL),
(144, 'Miguel', 'Carlos', 'Rodriguez', 'Ramirez', 20000143, 24, 'participante143@example.com', '2002-05-10', '2026-03-15', 'En formación', 'Universidad', 'Masculino', NULL),
(145, 'Carlos', 'Rosa', 'Rodriguez', 'Ramirez', 20000144, 24, 'participante144@example.com', '2002-03-15', '2026-06-20', 'Asignado', 'Universidad', 'Femenino', NULL),
(146, 'Juan', 'Jose', 'Garcia', 'Rodriguez', 20000145, 20, 'participante145@example.com', '2005-09-15', '2026-02-11', 'Asignado', 'Universidad', 'Femenino', NULL),
(147, 'Rosa', 'Miguel', 'Rodriguez', 'Martinez', 20000146, 26, 'participante146@example.com', '2000-04-18', '2026-03-30', 'Asignado', 'Universidad', 'Masculino', NULL),
(148, 'Rosa', 'Juan', 'Lopez', 'Lopez', 20000147, 21, 'participante147@example.com', '2005-04-18', '2026-05-02', 'Disponible', 'Primaria', 'Femenino', NULL),
(149, 'Juan', 'Patricia', 'Martinez', 'Torres', 20000148, 23, 'participante148@example.com', '2002-08-15', '2026-05-28', 'En formación', 'Primaria', 'Femenino', NULL),
(150, 'Pedro', 'Carlos', 'Garcia', 'Gonzalez', 20000149, 26, 'participante149@example.com', '2000-04-18', '2026-06-23', 'En formación', 'Primaria', 'Masculino', NULL);

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
  `estatus_participante` enum('Pendiente','Aprobado','Reprobado','En formación','Ausente','Asignado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante_curso`
--

INSERT INTO `tb_participante_curso` (`id_participante_curso`, `id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1, 10, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(2, 115, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Reprobado'),
(3, 26, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(4, 64, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(5, 131, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(6, 148, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(7, 81, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(8, 36, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(9, 34, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(10, 69, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Reprobado'),
(11, 44, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(12, 61, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Reprobado'),
(13, 103, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(14, 85, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(15, 67, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(16, 12, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(17, 32, 1, '2024-12-30', '2025-01-02', '2025-02-06', 'Aprobado'),
(18, 114, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(19, 87, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(20, 13, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(21, 24, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(22, 73, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(23, 10, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(24, 4, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(25, 31, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(26, 22, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(27, 16, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(28, 66, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(29, 40, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(30, 84, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(31, 107, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(32, 104, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(33, 55, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(34, 119, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(35, 17, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(36, 3, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Reprobado'),
(37, 128, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(38, 118, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(39, 149, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(40, 122, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(41, 105, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(42, 68, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(43, 88, 2, '2025-01-08', '2025-01-11', '2025-02-17', 'Aprobado'),
(44, 9, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(45, 110, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(46, 49, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(47, 42, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Reprobado'),
(48, 86, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(49, 130, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(50, 133, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(51, 33, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(52, 137, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(53, 109, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(54, 83, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(55, 111, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(56, 139, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(57, 96, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(58, 15, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(59, 25, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(60, 47, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(61, 61, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(62, 28, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(63, 134, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(64, 131, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(65, 142, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(66, 41, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(67, 37, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(68, 74, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Aprobado'),
(69, 127, 3, '2025-01-03', '2025-01-06', '2025-02-09', 'Reprobado'),
(70, 83, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(71, 7, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(72, 88, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(73, 73, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(74, 89, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(75, 85, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(76, 1, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(77, 98, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Reprobado'),
(78, 135, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(79, 24, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(80, 4, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(81, 139, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(82, 114, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(83, 112, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(84, 40, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(85, 138, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Aprobado'),
(86, 75, 4, '2025-02-12', '2025-02-15', '2025-03-24', 'Reprobado'),
(87, 29, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(88, 9, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(89, 124, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(90, 118, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(91, 136, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Reprobado'),
(92, 102, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(93, 67, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(94, 76, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(95, 15, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(96, 53, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(97, 43, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Reprobado'),
(98, 22, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(99, 143, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(100, 55, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(101, 2, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(102, 50, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(103, 21, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(104, 145, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(105, 82, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(106, 69, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Reprobado'),
(107, 101, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(108, 144, 5, '2025-02-04', '2025-02-07', '2025-03-10', 'Aprobado'),
(109, 85, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(110, 20, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(111, 93, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(112, 56, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Reprobado'),
(113, 113, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(114, 143, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(115, 5, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(116, 18, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Reprobado'),
(117, 123, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(118, 32, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Reprobado'),
(119, 136, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(120, 35, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(121, 11, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(122, 103, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Reprobado'),
(123, 38, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(124, 129, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(125, 116, 6, '2025-01-30', '2025-02-02', '2025-03-10', 'Aprobado'),
(126, 41, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(127, 88, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(128, 57, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(129, 100, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(130, 97, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(131, 40, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(132, 135, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(133, 30, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(134, 91, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(135, 86, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(136, 24, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(137, 131, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(138, 4, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(139, 70, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(140, 43, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(141, 82, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(142, 47, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(143, 115, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(144, 90, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(145, 118, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(146, 141, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(147, 71, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Aprobado'),
(148, 28, 7, '2025-03-05', '2025-03-08', '2025-04-12', 'Reprobado'),
(149, 147, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(150, 92, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(151, 137, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(152, 66, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(153, 44, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(154, 128, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(155, 106, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(156, 113, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(157, 141, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(158, 133, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(159, 58, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(160, 8, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(161, 49, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(162, 99, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(163, 129, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(164, 42, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(165, 79, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(166, 33, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(167, 27, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(168, 78, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(169, 56, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(170, 74, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(171, 37, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(172, 68, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(173, 131, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(174, 30, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Aprobado'),
(175, 26, 8, '2025-03-01', '2025-03-04', '2025-04-03', 'Reprobado'),
(176, 136, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(177, 74, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(178, 69, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(179, 45, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(180, 81, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(181, 75, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(182, 138, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(183, 127, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(184, 137, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Reprobado'),
(185, 10, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(186, 145, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(187, 3, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(188, 101, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(189, 13, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(190, 104, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(191, 8, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(192, 121, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(193, 80, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(194, 85, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(195, 60, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(196, 98, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(197, 25, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Aprobado'),
(198, 50, 9, '2025-03-10', '2025-03-13', '2025-04-18', 'Reprobado'),
(199, 55, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(200, 112, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(201, 130, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(202, 110, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(203, 106, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(204, 93, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(205, 26, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(206, 132, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(207, 119, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(208, 66, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(209, 150, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(210, 19, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(211, 31, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(212, 146, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(213, 63, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(214, 94, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(215, 6, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(216, 78, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(217, 95, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(218, 17, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(219, 108, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(220, 122, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(221, 9, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(222, 113, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(223, 62, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(224, 20, 10, '2025-04-02', '2025-04-05', '2025-05-08', 'Aprobado'),
(225, 5, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(226, 145, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(227, 14, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(228, 97, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(229, 1, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(230, 53, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(231, 3, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(232, 101, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(233, 131, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(234, 63, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(235, 73, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(236, 113, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(237, 52, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(238, 38, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(239, 34, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(240, 132, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Aprobado'),
(241, 135, 11, '2025-04-03', '2025-04-06', '2025-05-11', 'Reprobado'),
(242, 46, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(243, 33, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(244, 67, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(245, 32, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(246, 107, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Reprobado'),
(247, 87, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(248, 133, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(249, 79, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(250, 34, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(251, 76, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(252, 139, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(253, 7, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(254, 142, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(255, 51, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(256, 114, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(257, 35, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(258, 11, 12, '2025-04-08', '2025-04-11', '2025-05-13', 'Aprobado'),
(259, 75, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(260, 69, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(261, 82, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(262, 137, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(263, 81, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(264, 73, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(265, 144, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(266, 109, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(267, 84, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(268, 23, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Reprobado'),
(269, 91, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(270, 92, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Reprobado'),
(271, 30, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(272, 96, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(273, 77, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(274, 140, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Reprobado'),
(275, 37, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(276, 39, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(277, 88, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(278, 71, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(279, 86, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(280, 90, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(281, 28, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(282, 131, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(283, 115, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(284, 99, 13, '2025-05-04', '2025-05-07', '2025-06-13', 'Aprobado'),
(285, 65, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(286, 120, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(287, 95, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(288, 67, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(289, 21, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(290, 83, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(291, 11, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(292, 108, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(293, 10, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(294, 66, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(295, 96, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(296, 81, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(297, 143, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(298, 58, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(299, 76, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(300, 64, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(301, 40, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(302, 48, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(303, 62, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(304, 93, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(305, 8, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(306, 130, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(307, 104, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(308, 78, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(309, 49, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(310, 126, 14, '2025-05-02', '2025-05-05', '2025-06-06', 'Aprobado'),
(311, 60, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(312, 119, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(313, 92, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(314, 88, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(315, 89, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(316, 134, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(317, 111, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(318, 124, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(319, 116, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(320, 90, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Reprobado'),
(321, 139, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(322, 2, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(323, 71, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(324, 15, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(325, 138, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(326, 36, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(327, 146, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Reprobado'),
(328, 20, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(329, 46, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Reprobado'),
(330, 122, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(331, 9, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Reprobado'),
(332, 68, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Aprobado'),
(333, 32, 15, '2025-05-07', '2025-05-10', '2025-06-17', 'Reprobado'),
(334, 42, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(335, 94, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(336, 64, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(337, 53, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(338, 149, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Reprobado'),
(339, 133, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(340, 54, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(341, 70, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(342, 59, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(343, 120, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(344, 52, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(345, 29, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(346, 6, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(347, 85, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(348, 78, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(349, 16, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Reprobado'),
(350, 1, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(351, 110, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(352, 26, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(353, 9, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(354, 19, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(355, 118, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(356, 76, 16, '2025-06-04', '2025-06-07', '2025-07-11', 'Aprobado'),
(357, 138, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(358, 41, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(359, 129, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(360, 148, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Reprobado'),
(361, 142, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(362, 45, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(363, 101, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(364, 114, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(365, 89, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(366, 2, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Reprobado'),
(367, 48, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(368, 104, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(369, 136, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(370, 145, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(371, 11, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Reprobado'),
(372, 38, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(373, 10, 17, '2025-06-06', '2025-06-09', '2025-07-10', 'Aprobado'),
(374, 33, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(375, 97, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(376, 141, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(377, 32, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(378, 23, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(379, 61, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(380, 17, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(381, 90, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(382, 28, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(383, 126, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(384, 50, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(385, 73, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(386, 37, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(387, 84, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(388, 14, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(389, 30, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(390, 140, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(391, 83, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(392, 13, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(393, 36, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(394, 108, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(395, 127, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(396, 8, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Reprobado'),
(397, 7, 18, '2025-06-12', '2025-06-15', '2025-07-23', 'Aprobado'),
(398, 11, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(399, 140, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(400, 116, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(401, 112, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(402, 105, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(403, 2, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(404, 27, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(405, 36, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(406, 93, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(407, 131, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(408, 102, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(409, 142, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(410, 119, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(411, 35, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(412, 83, 19, '2025-07-02', '2025-07-05', '2025-08-05', 'Aprobado'),
(413, 121, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(414, 48, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(415, 134, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(416, 33, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(417, 67, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(418, 98, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(419, 85, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(420, 90, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(421, 147, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(422, 3, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(423, 129, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(424, 12, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(425, 52, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(426, 14, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(427, 58, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(428, 59, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Reprobado'),
(429, 117, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(430, 64, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Reprobado'),
(431, 71, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(432, 19, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Reprobado'),
(433, 16, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(434, 132, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(435, 139, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(436, 42, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(437, 145, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(438, 76, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(439, 18, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(440, 125, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(441, 20, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Aprobado'),
(442, 22, 20, '2025-06-30', '2025-07-03', '2025-08-05', 'Reprobado'),
(443, 75, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(444, 113, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(445, 101, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(446, 60, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(447, 128, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(448, 8, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(449, 94, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(450, 37, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(451, 115, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(452, 65, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(453, 30, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(454, 92, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(455, 88, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(456, 34, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(457, 6, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(458, 150, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(459, 32, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(460, 23, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(461, 109, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(462, 95, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(463, 124, 21, '2025-07-10', '2025-07-13', '2025-08-20', 'Aprobado'),
(464, 104, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(465, 19, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(466, 62, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(467, 55, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(468, 129, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(469, 21, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(470, 70, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(471, 69, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(472, 117, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(473, 22, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(474, 4, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(475, 12, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(476, 68, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(477, 84, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(478, 50, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(479, 122, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(480, 120, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(481, 40, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(482, 16, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(483, 112, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(484, 140, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(485, 127, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(486, 105, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Reprobado'),
(487, 58, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(488, 131, 22, '2025-08-07', '2025-08-10', '2025-09-10', 'Aprobado'),
(489, 24, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(490, 31, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(491, 64, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(492, 97, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(493, 7, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(494, 135, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(495, 82, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(496, 102, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(497, 36, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(498, 86, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Reprobado'),
(499, 103, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(500, 79, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(501, 33, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(502, 66, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(503, 29, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(504, 49, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(505, 143, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(506, 106, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(507, 61, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(508, 100, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(509, 110, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(510, 80, 23, '2025-08-04', '2025-08-07', '2025-09-15', 'Aprobado'),
(511, 13, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(512, 83, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(513, 137, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(514, 5, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(515, 134, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(516, 57, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(517, 38, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(518, 130, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(519, 46, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Reprobado'),
(520, 3, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(521, 11, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(522, 126, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(523, 142, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(524, 149, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(525, 85, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(526, 63, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(527, 108, 24, '2025-08-03', '2025-08-06', '2025-09-14', 'Aprobado'),
(528, 123, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(529, 37, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(530, 144, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(531, 41, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(532, 89, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(533, 42, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Reprobado'),
(534, 51, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(535, 39, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(536, 96, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(537, 88, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(538, 2, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(539, 115, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Reprobado'),
(540, 35, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(541, 72, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(542, 116, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(543, 145, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(544, 95, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(545, 124, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(546, 34, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Reprobado'),
(547, 87, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(548, 98, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(549, 18, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(550, 77, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(551, 111, 25, '2025-09-06', '2025-09-09', '2025-10-10', 'Aprobado'),
(552, 133, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(553, 75, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(554, 149, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(555, 52, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(556, 112, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(557, 6, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(558, 90, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(559, 25, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(560, 126, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(561, 113, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Reprobado'),
(562, 55, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(563, 120, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(564, 10, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(565, 147, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(566, 85, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Reprobado'),
(567, 119, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(568, 107, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(569, 105, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(570, 131, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(571, 15, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(572, 20, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(573, 38, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(574, 44, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(575, 94, 26, '2025-09-12', '2025-09-15', '2025-10-20', 'Aprobado'),
(576, 121, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(577, 128, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(578, 76, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Reprobado'),
(579, 56, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(580, 1, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(581, 132, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(582, 28, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(583, 14, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Reprobado'),
(584, 146, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(585, 47, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(586, 71, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(587, 99, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(588, 43, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(589, 148, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(590, 109, 27, '2025-09-02', '2025-09-05', '2025-10-08', 'Aprobado'),
(591, 63, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(592, 58, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Reprobado'),
(593, 24, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(594, 81, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(595, 110, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(596, 27, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(597, 106, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(598, 45, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(599, 69, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(600, 73, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(601, 17, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(602, 101, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(603, 138, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(604, 136, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(605, 64, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Reprobado'),
(606, 117, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Reprobado'),
(607, 141, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(608, 93, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(609, 12, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(610, 103, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(611, 49, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(612, 80, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(613, 84, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(614, 142, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(615, 143, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(616, 61, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Reprobado'),
(617, 31, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(618, 140, 28, '2025-10-02', '2025-10-05', '2025-11-12', 'Aprobado'),
(619, 28, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(620, 100, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(621, 8, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(622, 117, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(623, 26, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(624, 91, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(625, 89, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(626, 40, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(627, 93, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(628, 22, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(629, 96, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(630, 80, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(631, 116, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(632, 94, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(633, 72, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(634, 60, 29, '2025-09-30', '2025-10-03', '2025-11-12', 'Aprobado'),
(635, 16, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(636, 54, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Reprobado'),
(637, 29, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Reprobado'),
(638, 114, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(639, 59, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(640, 92, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(641, 5, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(642, 19, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(643, 53, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(644, 32, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Reprobado'),
(645, 79, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(646, 9, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(647, 127, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(648, 48, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(649, 57, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(650, 67, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(651, 118, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Reprobado'),
(652, 139, 30, '2025-09-29', '2025-10-02', '2025-11-03', 'Aprobado'),
(653, 1, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(654, 11, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Reprobado'),
(655, 73, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(656, 141, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(657, 56, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(658, 6, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(659, 79, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(660, 123, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(661, 66, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(662, 94, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(663, 129, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(664, 117, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(665, 12, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(666, 136, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Reprobado'),
(667, 84, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(668, 81, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(669, 85, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(670, 116, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(671, 126, 31, '2025-11-10', '2025-11-13', '2025-12-18', 'Aprobado'),
(672, 144, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(673, 76, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Reprobado'),
(674, 33, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(675, 115, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(676, 96, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(677, 35, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(678, 5, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(679, 74, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(680, 120, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(681, 10, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(682, 23, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(683, 7, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(684, 133, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(685, 15, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(686, 77, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(687, 139, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(688, 107, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(689, 95, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(690, 65, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(691, 82, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(692, 109, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(693, 124, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(694, 25, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(695, 131, 32, '2025-11-08', '2025-11-11', '2025-12-21', 'Aprobado'),
(696, 148, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(697, 8, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(698, 128, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(699, 108, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(700, 9, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(701, 50, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(702, 112, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(703, 16, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(704, 52, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(705, 62, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(706, 72, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(707, 92, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(708, 29, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(709, 36, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(710, 20, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(711, 83, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(712, 97, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(713, 41, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(714, 67, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(715, 37, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Aprobado'),
(716, 39, 33, '2025-11-07', '2025-11-10', '2025-12-13', 'Reprobado'),
(717, 5, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(718, 46, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(719, 135, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(720, 30, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(721, 51, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Reprobado'),
(722, 83, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(723, 149, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(724, 108, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(725, 101, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Reprobado'),
(726, 125, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(727, 21, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(728, 10, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(729, 50, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(730, 19, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(731, 148, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(732, 112, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(733, 6, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(734, 79, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(735, 77, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(736, 62, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(737, 117, 34, '2025-12-03', '2025-12-06', '2026-01-07', 'Aprobado'),
(738, 8, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(739, 98, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(740, 97, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(741, 44, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Reprobado'),
(742, 63, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(743, 64, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(744, 132, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado');
INSERT INTO `tb_participante_curso` (`id_participante_curso`, `id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(745, 135, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(746, 54, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(747, 108, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(748, 69, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(749, 100, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(750, 51, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(751, 75, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(752, 53, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(753, 49, 35, '2025-12-12', '2025-12-15', '2026-01-20', 'Aprobado'),
(754, 121, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(755, 97, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(756, 146, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(757, 147, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Reprobado'),
(758, 129, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(759, 8, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(760, 131, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(761, 33, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(762, 100, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(763, 57, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(764, 58, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(765, 66, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(766, 91, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(767, 9, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(768, 63, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(769, 106, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(770, 48, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(771, 27, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(772, 90, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(773, 113, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(774, 143, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Reprobado'),
(775, 92, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(776, 72, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(777, 39, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(778, 55, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(779, 11, 36, '2025-12-08', '2025-12-11', '2026-01-17', 'Aprobado'),
(780, 37, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Reprobado'),
(781, 76, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(782, 145, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(783, 136, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(784, 139, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(785, 143, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Reprobado'),
(786, 89, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(787, 19, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(788, 79, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(789, 6, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(790, 29, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(791, 96, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(792, 41, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(793, 112, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(794, 2, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(795, 118, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Reprobado'),
(796, 31, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(797, 110, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Reprobado'),
(798, 92, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(799, 47, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(800, 73, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(801, 134, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(802, 93, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(803, 35, 37, '2025-12-31', '2026-01-03', '2026-02-06', 'Aprobado'),
(804, 27, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(805, 137, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(806, 126, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(807, 104, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(808, 11, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(809, 141, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(810, 71, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(811, 105, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(812, 9, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(813, 18, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Reprobado'),
(814, 16, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(815, 125, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(816, 127, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(817, 22, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(818, 147, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(819, 84, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(820, 4, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(821, 80, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(822, 94, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Aprobado'),
(823, 85, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Reprobado'),
(824, 46, 38, '2026-01-04', '2026-01-07', '2026-02-14', 'Reprobado'),
(825, 53, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(826, 98, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(827, 86, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(828, 61, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(829, 64, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(830, 145, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Reprobado'),
(831, 141, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(832, 110, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(833, 22, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(834, 3, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(835, 95, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Reprobado'),
(836, 54, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(837, 137, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(838, 25, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(839, 138, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(840, 114, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(841, 70, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(842, 140, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(843, 150, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(844, 96, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Aprobado'),
(845, 82, 39, '2025-12-30', '2026-01-02', '2026-02-10', 'Reprobado'),
(846, 123, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(847, 131, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(848, 31, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(849, 55, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(850, 32, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(851, 72, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(852, 93, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(853, 66, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(854, 43, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(855, 18, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(856, 50, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(857, 40, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(858, 110, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(859, 118, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(860, 94, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(861, 117, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(862, 39, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(863, 13, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(864, 96, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(865, 92, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(866, 126, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(867, 38, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(868, 77, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(869, 29, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(870, 111, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(871, 1, 40, '2026-02-10', '2026-02-13', '2026-03-25', 'Aprobado'),
(872, 42, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(873, 71, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(874, 81, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Reprobado'),
(875, 142, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(876, 27, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Reprobado'),
(877, 105, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(878, 127, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(879, 59, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Reprobado'),
(880, 78, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(881, 125, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Reprobado'),
(882, 76, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(883, 100, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(884, 122, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(885, 134, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(886, 36, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(887, 69, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(888, 9, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(889, 99, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(890, 7, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(891, 19, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(892, 74, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(893, 21, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(894, 26, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Reprobado'),
(895, 63, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(896, 67, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(897, 124, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(898, 44, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(899, 16, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(900, 91, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(901, 132, 41, '2026-02-06', '2026-02-09', '2026-03-14', 'Aprobado'),
(902, 95, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(903, 109, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(904, 78, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(905, 67, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(906, 17, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Reprobado'),
(907, 13, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Reprobado'),
(908, 64, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(909, 88, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(910, 82, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(911, 113, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(912, 56, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(913, 81, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(914, 130, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(915, 54, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(916, 136, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(917, 110, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(918, 39, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(919, 7, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(920, 129, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(921, 96, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Aprobado'),
(922, 24, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Reprobado'),
(923, 19, 42, '2026-02-04', '2026-02-07', '2026-03-13', 'Reprobado'),
(924, 36, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(925, 25, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(926, 53, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(927, 45, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(928, 106, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(929, 93, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(930, 92, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(931, 20, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(932, 29, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(933, 140, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(934, 31, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(935, 49, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(936, 2, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(937, 3, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(938, 124, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(939, 12, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(940, 102, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(941, 1, 43, '2026-02-28', '2026-03-03', '2026-04-08', 'Aprobado'),
(942, 137, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(943, 3, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(944, 103, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(945, 86, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(946, 12, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(947, 146, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(948, 145, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(949, 58, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(950, 90, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(951, 62, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(952, 47, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(953, 87, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(954, 60, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(955, 61, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(956, 68, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(957, 75, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Reprobado'),
(958, 73, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Reprobado'),
(959, 23, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(960, 102, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(961, 98, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(962, 25, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Reprobado'),
(963, 48, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(964, 115, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(965, 138, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(966, 53, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(967, 101, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(968, 109, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(969, 121, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(970, 141, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(971, 114, 44, '2026-03-01', '2026-03-04', '2026-04-12', 'Aprobado'),
(972, 30, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(973, 120, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(974, 144, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(975, 116, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(976, 118, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(977, 27, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(978, 60, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(979, 150, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(980, 103, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Reprobado'),
(981, 10, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(982, 137, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(983, 37, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(984, 77, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(985, 58, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(986, 101, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Reprobado'),
(987, 61, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(988, 114, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(989, 107, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(990, 141, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(991, 134, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Reprobado'),
(992, 59, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(993, 100, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(994, 104, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(995, 84, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(996, 99, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(997, 148, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(998, 121, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(999, 147, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(1000, 117, 45, '2026-03-09', '2026-03-12', '2026-04-14', 'Aprobado'),
(1001, 147, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1002, 142, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1003, 144, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1004, 69, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1005, 36, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1006, 76, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1007, 45, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1008, 27, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1009, 67, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1010, 106, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1011, 18, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1012, 132, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1013, 81, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1014, 130, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1015, 19, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1016, 2, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1017, 50, 46, '2026-04-07', '2026-04-10', '2026-05-10', 'Aprobado'),
(1018, 76, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1019, 4, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1020, 109, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1021, 55, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1022, 95, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1023, 22, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1024, 88, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Reprobado'),
(1025, 122, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1026, 74, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1027, 64, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1028, 6, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Reprobado'),
(1029, 51, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1030, 1, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1031, 20, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1032, 39, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1033, 33, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1034, 92, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1035, 12, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1036, 115, 47, '2026-04-10', '2026-04-13', '2026-05-14', 'Aprobado'),
(1037, 54, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1038, 117, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1039, 95, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1040, 28, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1041, 139, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1042, 99, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1043, 126, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1044, 34, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1045, 49, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1046, 9, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1047, 125, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1048, 108, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1049, 113, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1050, 100, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1051, 93, 48, '2026-04-07', '2026-04-10', '2026-05-16', 'Aprobado'),
(1052, 11, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1053, 49, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1054, 110, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1055, 66, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Reprobado'),
(1056, 3, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1057, 29, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Reprobado'),
(1058, 17, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1059, 24, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1060, 117, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1061, 18, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1062, 77, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Reprobado'),
(1063, 45, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1064, 70, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1065, 44, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1066, 125, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1067, 7, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1068, 89, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1069, 56, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1070, 65, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1071, 101, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Reprobado'),
(1072, 21, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1073, 13, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1074, 144, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1075, 60, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Reprobado'),
(1076, 142, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1077, 9, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1078, 82, 49, '2026-05-05', '2026-05-08', '2026-06-15', 'Aprobado'),
(1079, 120, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1080, 44, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1081, 14, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1082, 32, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1083, 128, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Reprobado'),
(1084, 80, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Reprobado'),
(1085, 61, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1086, 60, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1087, 149, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1088, 43, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1089, 66, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Reprobado'),
(1090, 89, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1091, 15, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1092, 83, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1093, 146, 50, '2026-05-03', '2026-05-06', '2026-06-11', 'Aprobado'),
(1094, 148, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1095, 112, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Reprobado'),
(1096, 73, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1097, 37, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1098, 8, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1099, 42, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Reprobado'),
(1100, 79, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1101, 87, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1102, 132, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1103, 92, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1104, 27, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1105, 10, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1106, 134, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1107, 25, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1108, 85, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1109, 67, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1110, 57, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1111, 38, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1112, 17, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1113, 96, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1114, 107, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Reprobado'),
(1115, 143, 51, '2026-05-09', '2026-05-12', '2026-06-20', 'Aprobado'),
(1116, 147, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1117, 48, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1118, 14, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1119, 124, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Reprobado'),
(1120, 131, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1121, 105, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1122, 130, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1123, 42, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1124, 54, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1125, 39, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1126, 102, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1127, 33, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1128, 127, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1129, 113, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1130, 115, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1131, 150, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Reprobado'),
(1132, 145, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1133, 8, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1134, 91, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1135, 149, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1136, 59, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1137, 90, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1138, 36, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1139, 34, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1140, 128, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1141, 25, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1142, 10, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1143, 64, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1144, 93, 52, '2026-06-08', '2026-06-11', '2026-07-16', 'Aprobado'),
(1145, 23, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1146, 73, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1147, 99, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1148, 114, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1149, 35, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1150, 47, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1151, 88, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1152, 137, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1153, 75, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1154, 86, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1155, 6, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1156, 57, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1157, 38, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1158, 136, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1159, 141, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1160, 80, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1161, 138, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1162, 106, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1163, 28, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1164, 53, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1165, 52, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1166, 100, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1167, 31, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1168, 123, 53, '2026-06-03', '2026-06-06', '2026-07-10', 'Aprobado'),
(1169, 16, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1170, 130, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1171, 110, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1172, 20, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1173, 103, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1174, 136, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1175, 19, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1176, 46, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1177, 63, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1178, 47, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1179, 95, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1180, 99, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1181, 64, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1182, 71, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1183, 58, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1184, 69, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1185, 9, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1186, 44, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1187, 133, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1188, 48, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1189, 129, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1190, 74, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1191, 65, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1192, 120, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1193, 2, 54, '2026-06-09', '2026-06-12', '2026-07-20', 'En formación'),
(1194, 41, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1195, 10, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1196, 49, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1197, 84, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1198, 96, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1199, 128, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1200, 119, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1201, 17, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1202, 109, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1203, 106, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1204, 115, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1205, 56, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1206, 72, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1207, 80, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1208, 93, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1209, 52, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1210, 13, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1211, 55, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1212, 83, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1213, 62, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1214, 11, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1215, 141, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1216, 37, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1217, 38, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1218, 73, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1219, 97, 55, '2026-07-05', '2026-07-08', '2026-08-07', 'En formación'),
(1220, 59, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1221, 79, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1222, 92, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1223, 31, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1224, 42, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1225, 127, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1226, 1, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1227, 144, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1228, 53, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1229, 85, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1230, 134, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1231, 137, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1232, 143, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1233, 21, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1234, 91, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1235, 68, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1236, 23, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1237, 70, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1238, 87, 56, '2026-06-29', '2026-07-02', '2026-08-01', 'En formación'),
(1239, 33, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1240, 27, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1241, 57, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1242, 4, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1243, 124, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1244, 122, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1245, 18, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1246, 24, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1247, 108, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1248, 149, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1249, 112, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1250, 138, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1251, 117, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1252, 113, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1253, 135, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1254, 7, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1255, 12, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1256, 150, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1257, 40, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1258, 50, 57, '2026-07-11', '2026-07-14', '2026-08-17', 'En formación'),
(1259, 68, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1260, 80, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1261, 83, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1262, 34, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1263, 87, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1264, 100, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1265, 125, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1266, 88, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1267, 72, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1268, 56, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1269, 89, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1270, 32, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1271, 71, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1272, 45, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1273, 106, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1274, 35, 58, '2026-08-09', '2026-08-12', '2026-09-17', 'Asignado'),
(1275, 96, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1276, 38, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1277, 78, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1278, 60, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1279, 133, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1280, 131, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1281, 117, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1282, 40, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1283, 149, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1284, 92, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1285, 90, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1286, 115, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1287, 14, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1288, 57, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1289, 44, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1290, 136, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1291, 27, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1292, 63, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1293, 143, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1294, 61, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1295, 75, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1296, 121, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1297, 123, 59, '2026-08-11', '2026-08-14', '2026-09-19', 'Asignado'),
(1298, 39, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1299, 72, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1300, 124, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1301, 23, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1302, 20, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1303, 139, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1304, 79, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1305, 135, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1306, 10, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1307, 8, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1308, 56, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1309, 125, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1310, 62, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1311, 70, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1312, 59, 60, '2026-07-30', '2026-08-02', '2026-09-02', 'Asignado'),
(1313, 78, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1314, 147, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1315, 26, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1316, 82, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1317, 69, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1318, 93, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1319, 22, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1320, 51, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1321, 61, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1322, 59, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1323, 138, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1324, 58, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1325, 109, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1326, 143, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1327, 142, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1328, 127, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1329, 77, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1330, 139, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1331, 120, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1332, 144, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1333, 86, 61, '2026-09-07', '2026-09-10', '2026-10-10', 'Asignado'),
(1334, 73, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1335, 83, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1336, 26, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1337, 11, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1338, 120, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1339, 146, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1340, 13, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1341, 145, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1342, 80, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1343, 24, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1344, 86, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1345, 12, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1346, 141, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1347, 93, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1348, 59, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1349, 101, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1350, 3, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1351, 122, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1352, 94, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1353, 147, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1354, 33, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1355, 76, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1356, 77, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1357, 58, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1358, 140, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1359, 79, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1360, 37, 62, '2026-08-31', '2026-09-03', '2026-10-11', 'Asignado'),
(1361, 103, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1362, 123, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1363, 107, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1364, 129, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1365, 48, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1366, 122, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1367, 135, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1368, 38, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1369, 97, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1370, 134, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1371, 2, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1372, 16, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1373, 84, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1374, 104, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1375, 52, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1376, 132, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1377, 92, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1378, 47, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1379, 124, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1380, 116, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1381, 81, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1382, 28, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1383, 73, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1384, 10, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1385, 6, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado'),
(1386, 133, 63, '2026-09-10', '2026-09-13', '2026-10-20', 'Asignado');

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
(1, 4141234560, 'Móvil', 1, NULL, NULL),
(2, 4141234561, 'Móvil', 2, NULL, NULL),
(3, 4141234562, 'Móvil', 3, NULL, NULL),
(4, 4141234563, 'Móvil', 4, NULL, NULL),
(5, 4141234564, 'Móvil', 5, NULL, NULL),
(6, 4243515712, 'Móvil', NULL, 1, NULL),
(7, 4242157567, 'Móvil', NULL, 2, NULL),
(8, 4243532694, 'Móvil', NULL, 3, NULL),
(9, 4247445377, 'Móvil', NULL, 4, NULL),
(10, 4243139274, 'Móvil', NULL, 5, NULL),
(11, 4244756298, 'Móvil', NULL, 6, NULL),
(12, 4244748310, 'Móvil', NULL, 7, NULL),
(13, 4249493956, 'Móvil', NULL, 8, NULL),
(14, 4243415660, 'Móvil', NULL, 9, NULL),
(15, 4248863782, 'Móvil', NULL, 10, NULL),
(16, 4243049398, 'Móvil', NULL, 11, NULL),
(17, 4242901100, 'Móvil', NULL, 12, NULL),
(18, 4242372690, 'Móvil', NULL, 13, NULL),
(19, 4245231801, 'Móvil', NULL, 14, NULL),
(20, 4244264701, 'Móvil', NULL, 15, NULL),
(21, 4241023558, 'Móvil', NULL, 16, NULL),
(22, 4242960840, 'Móvil', NULL, 17, NULL),
(23, 4249893574, 'Móvil', NULL, 18, NULL),
(24, 4246937625, 'Móvil', NULL, 19, NULL),
(25, 4247822590, 'Móvil', NULL, 20, NULL),
(26, 4245786217, 'Móvil', NULL, 21, NULL),
(27, 4248774982, 'Móvil', NULL, 22, NULL),
(28, 4249986085, 'Móvil', NULL, 23, NULL),
(29, 4246143847, 'Móvil', NULL, 24, NULL),
(30, 4249762537, 'Móvil', NULL, 25, NULL),
(31, 4249112141, 'Móvil', NULL, 26, NULL),
(32, 4249891284, 'Móvil', NULL, 27, NULL),
(33, 4249162573, 'Móvil', NULL, 28, NULL),
(34, 4242382036, 'Móvil', NULL, 29, NULL),
(35, 4241324777, 'Móvil', NULL, 30, NULL),
(36, 4245126835, 'Móvil', NULL, 31, NULL),
(37, 4243071856, 'Móvil', NULL, 32, NULL),
(38, 4247012541, 'Móvil', NULL, 33, NULL),
(39, 4242764571, 'Móvil', NULL, 34, NULL),
(40, 4246857545, 'Móvil', NULL, 35, NULL),
(41, 4248304770, 'Móvil', NULL, 36, NULL),
(42, 4243263029, 'Móvil', NULL, 37, NULL),
(43, 4244240878, 'Móvil', NULL, 38, NULL),
(44, 4243683936, 'Móvil', NULL, 39, NULL),
(45, 4241288331, 'Móvil', NULL, 40, NULL),
(46, 4248814266, 'Móvil', NULL, 41, NULL),
(47, 4247484042, 'Móvil', NULL, 42, NULL),
(48, 4245355552, 'Móvil', NULL, 43, NULL),
(49, 4246986592, 'Móvil', NULL, 44, NULL),
(50, 4241860132, 'Móvil', NULL, 45, NULL),
(51, 4248898358, 'Móvil', NULL, 46, NULL),
(52, 4244382753, 'Móvil', NULL, 47, NULL),
(53, 4246859629, 'Móvil', NULL, 48, NULL),
(54, 4243070375, 'Móvil', NULL, 49, NULL),
(55, 4242627162, 'Móvil', NULL, 50, NULL),
(56, 4247689770, 'Móvil', NULL, 51, NULL),
(57, 4246075015, 'Móvil', NULL, 52, NULL),
(58, 4247299916, 'Móvil', NULL, 53, NULL),
(59, 4246720143, 'Móvil', NULL, 54, NULL),
(60, 4242373761, 'Móvil', NULL, 55, NULL),
(61, 4245849198, 'Móvil', NULL, 56, NULL),
(62, 4243217396, 'Móvil', NULL, 57, NULL),
(63, 4247099089, 'Móvil', NULL, 58, NULL),
(64, 4248406862, 'Móvil', NULL, 59, NULL),
(65, 4249092438, 'Móvil', NULL, 60, NULL),
(66, 4244679598, 'Móvil', NULL, 61, NULL),
(67, 4244879249, 'Móvil', NULL, 62, NULL),
(68, 4246688182, 'Móvil', NULL, 63, NULL),
(69, 4246216865, 'Móvil', NULL, 64, NULL),
(70, 4248366723, 'Móvil', NULL, 65, NULL),
(71, 4249550575, 'Móvil', NULL, 66, NULL),
(72, 4242362388, 'Móvil', NULL, 67, NULL),
(73, 4246250328, 'Móvil', NULL, 68, NULL),
(74, 4246062306, 'Móvil', NULL, 69, NULL),
(75, 4242934726, 'Móvil', NULL, 70, NULL),
(76, 4243138299, 'Móvil', NULL, 71, NULL),
(77, 4246638870, 'Móvil', NULL, 72, NULL),
(78, 4249345702, 'Móvil', NULL, 73, NULL),
(79, 4245044677, 'Móvil', NULL, 74, NULL),
(80, 4244783260, 'Móvil', NULL, 75, NULL),
(81, 4246582719, 'Móvil', NULL, 76, NULL),
(82, 4242926753, 'Móvil', NULL, 77, NULL),
(83, 4246552108, 'Móvil', NULL, 78, NULL),
(84, 4245418286, 'Móvil', NULL, 79, NULL),
(85, 4247068869, 'Móvil', NULL, 80, NULL),
(86, 4243864215, 'Móvil', NULL, 81, NULL),
(87, 4246703940, 'Móvil', NULL, 82, NULL),
(88, 4245033421, 'Móvil', NULL, 83, NULL),
(89, 4243669246, 'Móvil', NULL, 84, NULL),
(90, 4247669995, 'Móvil', NULL, 85, NULL),
(91, 4241408562, 'Móvil', NULL, 86, NULL),
(92, 4245691382, 'Móvil', NULL, 87, NULL),
(93, 4248648630, 'Móvil', NULL, 88, NULL),
(94, 4242573329, 'Móvil', NULL, 89, NULL),
(95, 4248893157, 'Móvil', NULL, 90, NULL),
(96, 4245636595, 'Móvil', NULL, 91, NULL),
(97, 4246901315, 'Móvil', NULL, 92, NULL),
(98, 4242158726, 'Móvil', NULL, 93, NULL),
(99, 4244087082, 'Móvil', NULL, 94, NULL),
(100, 4242674670, 'Móvil', NULL, 95, NULL),
(101, 4243781645, 'Móvil', NULL, 96, NULL),
(102, 4246481101, 'Móvil', NULL, 97, NULL),
(103, 4242701522, 'Móvil', NULL, 98, NULL),
(104, 4243548061, 'Móvil', NULL, 99, NULL),
(105, 4242292574, 'Móvil', NULL, 100, NULL),
(106, 4249079954, 'Móvil', NULL, 101, NULL),
(107, 4249057917, 'Móvil', NULL, 102, NULL),
(108, 4242537879, 'Móvil', NULL, 103, NULL),
(109, 4244890474, 'Móvil', NULL, 104, NULL),
(110, 4247890711, 'Móvil', NULL, 105, NULL),
(111, 4245016724, 'Móvil', NULL, 106, NULL),
(112, 4241870814, 'Móvil', NULL, 107, NULL),
(113, 4244722080, 'Móvil', NULL, 108, NULL),
(114, 4248544004, 'Móvil', NULL, 109, NULL),
(115, 4244904777, 'Móvil', NULL, 110, NULL),
(116, 4248627946, 'Móvil', NULL, 111, NULL),
(117, 4248130180, 'Móvil', NULL, 112, NULL),
(118, 4241479883, 'Móvil', NULL, 113, NULL),
(119, 4243493149, 'Móvil', NULL, 114, NULL),
(120, 4249264591, 'Móvil', NULL, 115, NULL),
(121, 4242815167, 'Móvil', NULL, 116, NULL),
(122, 4242072499, 'Móvil', NULL, 117, NULL),
(123, 4249366563, 'Móvil', NULL, 118, NULL),
(124, 4248017272, 'Móvil', NULL, 119, NULL),
(125, 4242460877, 'Móvil', NULL, 120, NULL),
(126, 4245626274, 'Móvil', NULL, 121, NULL),
(127, 4247902961, 'Móvil', NULL, 122, NULL),
(128, 4247866740, 'Móvil', NULL, 123, NULL),
(129, 4241522006, 'Móvil', NULL, 124, NULL),
(130, 4248362401, 'Móvil', NULL, 125, NULL),
(131, 4242616880, 'Móvil', NULL, 126, NULL),
(132, 4249199188, 'Móvil', NULL, 127, NULL),
(133, 4245832836, 'Móvil', NULL, 128, NULL),
(134, 4245286416, 'Móvil', NULL, 129, NULL),
(135, 4243935048, 'Móvil', NULL, 130, NULL),
(136, 4247209401, 'Móvil', NULL, 131, NULL),
(137, 4248561623, 'Móvil', NULL, 132, NULL),
(138, 4242963315, 'Móvil', NULL, 133, NULL),
(139, 4241822080, 'Móvil', NULL, 134, NULL),
(140, 4241291778, 'Móvil', NULL, 135, NULL),
(141, 4249304013, 'Móvil', NULL, 136, NULL),
(142, 4244244140, 'Móvil', NULL, 137, NULL),
(143, 4243436973, 'Móvil', NULL, 138, NULL),
(144, 4247946306, 'Móvil', NULL, 139, NULL),
(145, 4248636330, 'Móvil', NULL, 140, NULL),
(146, 4249771967, 'Móvil', NULL, 141, NULL),
(147, 4246443026, 'Móvil', NULL, 142, NULL),
(148, 4242368161, 'Móvil', NULL, 143, NULL),
(149, 4244843556, 'Móvil', NULL, 144, NULL),
(150, 4245243557, 'Móvil', NULL, 145, NULL),
(151, 4241245177, 'Móvil', NULL, 146, NULL),
(152, 4242960226, 'Móvil', NULL, 147, NULL),
(153, 4245530078, 'Móvil', NULL, 148, NULL),
(154, 4246476382, 'Móvil', NULL, 149, NULL),
(155, 4243178879, 'Móvil', NULL, 150, NULL);

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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10001;

--
-- AUTO_INCREMENT de la tabla `tb_discapacidad`
--
ALTER TABLE `tb_discapacidad`
  MODIFY `id_discapacidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

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
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

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

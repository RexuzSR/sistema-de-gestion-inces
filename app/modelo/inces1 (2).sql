-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2025 a las 02:20:46
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
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `max_participantes` int(11) DEFAULT 30,
  `num_inscritos` int(11) DEFAULT 0,
  `id_modalidad` int(11) DEFAULT NULL,
  `tipo_formacion` enum('Unidad Curricular') DEFAULT NULL,
  `id_estatusCurso` int(11) DEFAULT NULL,
  `sectoreconomico` enum('Comercio y Servicios','Industria','Investigación, Desarrollo, Innovación e Información') DEFAULT NULL,
  `id_motor` int(11) DEFAULT NULL,
  `subtipo` enum('ESPECÍFICA/TÉCNICA','TRANSVERSAL/GENÉRICA','BÁSICA/COMÚN') DEFAULT NULL,
  `id_areaFormativa` int(11) DEFAULT NULL,
  `ambito` enum('Centro de Formacion Socialista','Comunas','Instalaciones Militares','Unidades educativas','Sistema de Misiones y Grandes Misiones') DEFAULT NULL,
  `turno` enum('Mañana','Tarde') NOT NULL,
  `programa_formacion` enum('Programa de Formación Productiva (PFP)') DEFAULT NULL,
  `fecha_creacion` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_curso`
--

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `fecha_fin`, `max_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `sectoreconomico`, `id_motor`, `subtipo`, `id_areaFormativa`, `ambito`, `turno`, `programa_formacion`, `fecha_creacion`) VALUES
(71, 'AMASADOR DE PAN', '2025-03-30', '2025-05-20', 25, 5, 1, 'Unidad Curricular', 3, 'Comercio y Servicios', 3, 'ESPECÍFICA/TÉCNICA', 2, 'Centro de Formacion Socialista', 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-05-15'),
(72, 'CONSERVACIÓN DE ALIMENTOS', '2025-05-01', '2025-07-01', 20, 5, 2, 'Unidad Curricular', 2, 'Comercio y Servicios', 2, 'ESPECÍFICA/TÉCNICA', 3, 'Centro de Formacion Socialista', 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-06-10'),
(73, 'CONFECCIÓN DE ROPA DEPORTIVA', '2024-08-05', '2024-08-19', 30, 0, 1, 'Unidad Curricular', 3, 'Industria', 4, 'ESPECÍFICA/TÉCNICA', 5, 'Comunas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-07-20'),
(74, 'SEGURIDAD Y SALUD LABORAL', '2024-09-02', '2024-09-16', 15, 1, 3, 'Unidad Curricular', 3, 'Industria', 5, 'TRANSVERSAL/GENÉRICA', 6, 'Instalaciones Militares', 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-08-10'),
(75, 'GENERALIDADES DEL MOTOR DIÉSEL', '2024-10-07', '2024-10-21', 18, 0, 2, 'Unidad Curricular', 3, 'Industria', 6, 'ESPECÍFICA/TÉCNICA', 7, 'Centro de Formacion Socialista', 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-09-15'),
(76, 'REPARACIÓN DE', '2025-11-04', '2025-11-30', 22, 2, 1, 'Unidad Curricular', 1, 'Industria', 1, 'ESPECÍFICA/TÉCNICA', 8, 'Centro de Formacion Socialista', 'Tarde', 'Programa de Formación Productiva (PFP)', '2024-10-20'),
(77, 'PROYECTO ÉTICO DE VIDA', '2025-01-06', '2025-01-20', 12, 0, 3, 'Unidad Curricular', 3, 'Comercio y Servicios', 2, 'BÁSICA/COMÚN', 9, 'Comunas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2024-12-10'),
(78, 'ADMINISTRACIÓN II', '2025-06-03', '2025-07-30', 28, 0, 2, 'Unidad Curricular', 1, 'Comercio y Servicios', 3, 'ESPECÍFICA/TÉCNICA', 10, 'Centro de Formacion Socialista', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-01-15'),
(79, 'CARPINTERÍA BÁSICA', '2025-03-10', '2025-03-24', 16, 5, 1, 'Unidad Curricular', 3, 'Industria', 4, 'ESPECÍFICA/TÉCNICA', 1, 'Unidades educativas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-02-20'),
(80, 'METROLOGÍA', '2025-04-07', '2025-04-21', 20, 0, 2, 'Unidad Curricular', 3, 'Industria', 5, 'ESPECÍFICA/TÉCNICA', 2, 'Instalaciones Militares', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-03-15'),
(81, 'FÍSICA BÁSICA', '2025-05-05', '2025-05-19', 14, 0, 3, 'Unidad Curricular', 3, 'Investigación, Desarrollo, Innovación e Información', 6, 'BÁSICA/COMÚN', 3, 'Instalaciones Militares', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-04-10'),
(82, 'PRINCIPIOS BÁSICOS DE CONTABILIDAD', '2025-06-02', '2025-06-16', 25, 0, 1, 'Unidad Curricular', 1, 'Comercio y Servicios', 1, 'ESPECÍFICA/TÉCNICA', 4, 'Sistema de Misiones y Grandes Misiones', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-05-20'),
(83, 'CONFECCIÓN DE ROPA INFANTIL', '2025-07-07', '2025-07-21', 30, 0, 2, 'Unidad Curricular', 1, 'Industria', 2, 'ESPECÍFICA/TÉCNICA', 5, 'Comunas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-06-15'),
(84, 'PATRONAJE DE PRENDAS DE VESTIR', '2025-08-04', '2025-08-18', 18, 0, 3, 'Unidad Curricular', 1, 'Industria', 3, 'ESPECÍFICA/TÉCNICA', 6, 'Comunas', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-07-20'),
(85, 'CORTE DE PRENDAS DE VESTIR', '2025-09-01', '2025-09-15', 22, 0, 1, 'Unidad Curricular', 1, 'Industria', 4, 'ESPECÍFICA/TÉCNICA', 7, 'Comunas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-08-10'),
(86, 'REPARACIÓN DE MÁQUINAS DE COSER', '2025-10-06', '2025-10-20', 15, 0, 2, 'Unidad Curricular', 1, 'Industria', 5, 'ESPECÍFICA/TÉCNICA', 8, 'Centro de Formacion Socialista', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-09-15'),
(87, 'LECTURA E INTERPRETACIÓN', '2025-11-03', '2025-11-30', 20, 0, 3, 'Unidad Curricular', 1, 'Investigación, Desarrollo, Innovación e Información', 6, 'ESPECÍFICA/TÉCNICA', 9, 'Sistema de Misiones y Grandes Misiones', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-10-20'),
(88, 'CIRCUITOS ELÉCTRICOS', '2025-12-01', '2025-12-15', 25, 0, 1, 'Unidad Curricular', 1, 'Investigación, Desarrollo, Innovación e Información', 1, 'ESPECÍFICA/TÉCNICA', 10, 'Sistema de Misiones y Grandes Misiones', 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-11-15'),
(89, 'PROYECTO ÉTICO DE VIDA', '2026-01-05', '2026-01-19', 12, 2, 2, 'Unidad Curricular', 1, 'Comercio y Servicios', 2, 'BÁSICA/COMÚN', 1, 'Comunas', 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-12-20'),
(90, 'ADMINISTRACIÓN', '2026-02-02', '2026-02-25', 28, 0, 3, 'Unidad Curricular', 1, 'Comercio y Servicios', 3, 'ESPECÍFICA/TÉCNICA', 2, 'Centro de Formacion Socialista', 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_docente`
--

CREATE TABLE `tb_docente` (
  `id_docente` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` int(11) NOT NULL,
  `telefono` bigint(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `estado_docente` enum('Disponible','En formación','Asignado') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_docente`
--

INSERT INTO `tb_docente` (`id_docente`, `nombre`, `apellido`, `cedula`, `telefono`, `correo`, `fecha_nacimiento`, `edad`, `estado_docente`, `genero`) VALUES
(11, 'Roberto', 'Jiménez', 12345679, 4121234570, 'roberto.jimenez@email.com', '1980-03-25', 44, 'Asignado', 'Masculino'),
(12, 'Carmen', 'Lara', 23456780, 4149876544, 'carmen.lara@email.com', '1975-07-12', 48, 'Asignado', 'Femenino'),
(13, 'Luis', 'Mendoza', 34567890, 4241234571, 'luis.mendoza@email.com', '1990-11-08', 33, 'Disponible', 'Masculino'),
(14, 'Patricia', 'Rivas', 45678901, 4161234569, 'patricia.rivas@email.com', '1988-05-19', 35, 'Asignado', 'Femenino'),
(15, 'Jorge', 'Paredes', 56789012, 4261234570, 'jorge.paredes@email.com', '1972-09-30', 51, 'Disponible', 'Masculino'),
(16, 'Gladys', 'Fuentes', 67890123, 4131234569, 'gladys.fuentes@email.com', '1985-02-14', 39, 'Disponible', 'Femenino'),
(17, 'Francisco', 'Briceño', 78901234, 4171234569, 'francisco.briceno@email.com', '1995-12-22', 28, 'Disponible', 'Masculino'),
(18, 'Marisela', 'Acosta', 89012345, 4241234572, 'marisela.acosta@email.com', '1978-08-07', 45, 'Disponible', 'Femenino'),
(19, 'Héctor', 'Navarro', 90123456, 4121234571, 'hector.navarro@email.com', '1968-04-18', 56, 'Disponible', 'Masculino'),
(20, 'Yolanda', 'Peña', 11223345, 4159876544, 'yolanda.pena@email.com', '1983-10-05', 40, 'Disponible', 'Femenino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_docente_curso`
--

CREATE TABLE `tb_docente_curso` (
  `id_docente_curso` int(11) NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_docente_curso`
--

INSERT INTO `tb_docente_curso` (`id_docente_curso`, `id_docente`, `id_curso`) VALUES
(56, 11, 90),
(57, 12, 79),
(58, 14, 76);

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
(2, 'En proceso'),
(3, 'Culminado'),
(4, 'Aprobado');

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
-- Estructura de tabla para la tabla `tb_participante`
--

CREATE TABLE `tb_participante` (
  `id_participante` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` int(11) NOT NULL,
  `edad` int(11) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `telefono` bigint(20) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` date DEFAULT current_timestamp(),
  `estado` enum('En formación','Inactivo','Disponible','Asignado') NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `nombre`, `apellido`, `cedula`, `edad`, `correo`, `telefono`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`) VALUES
(316, 'María', 'González', 12345678, 25, 'maria.gonzalez@email.com', 4121234567, '1998-05-15', '2024-03-10', 'En formación', 'Universidad', 'Femenino'),
(317, 'Juan', 'Pérez', 8765432, 18, 'juan.perez@email.com', 4149876543, '2005-11-22', '2024-06-18', 'En formación', 'Bachillerato', 'Masculino'),
(318, 'Ana', 'Rodríguez', 23456789, 32, 'ana.rodriguez@email.com', 4241234567, '1991-08-30', '2024-01-05', 'En formación', 'Universidad', 'Femenino'),
(319, 'Carlos', 'López', 34567812, 16, 'carlos.lopez@email.com', 4161234567, '2007-04-12', '2024-09-22', 'En formación', 'Bachillerato', 'Masculino'),
(320, 'Luisa', 'Martínez', 45678123, 45, 'luisa.martinez@email.com', 4121234568, '1978-12-05', '2024-02-14', 'En formación', 'Universidad', 'Femenino'),
(321, 'Pedro', 'Hernández', 56781234, 14, 'pedro.hernandez@email.com', 4261234567, '2009-07-19', '2025-01-30', 'Asignado', 'Primaria', 'Masculino'),
(322, 'Sofía', 'García', 67812345, 28, 'sofia.garcia@email.com', 4131234567, '1995-10-25', '2024-04-17', 'Asignado', 'Universidad', 'Femenino'),
(323, 'José', 'Fernández', 78123456, 20, 'jose.fernandez@email.com', 4171234567, '2003-09-08', '2024-11-05', 'Asignado', 'Bachillerato', 'Masculino'),
(324, 'Valentina', 'Díaz', 81234567, 22, 'valentina.diaz@email.com', 4241234568, '2001-02-28', '2024-07-12', 'Inactivo', 'Universidad', 'Femenino'),
(325, 'Miguel', 'Torres', 9876543, 17, 'miguel.torres@email.com', 4159876543, '2006-06-14', '2025-02-20', 'Asignado', 'Bachillerato', 'Masculino'),
(326, 'Isabella', 'Ramírez', 11223344, 35, 'isabella.ramirez@email.com', 4121234569, '1988-03-21', '2024-05-08', 'Inactivo', 'Universidad', 'Femenino'),
(327, 'Alejandro', 'Sánchez', 22334455, 19, 'alejandro.sanchez@email.com', 4261234568, '2004-12-07', '2024-10-15', 'Inactivo', 'Bachillerato', 'Masculino'),
(328, 'Camila', 'Rojas', 33445566, 27, 'camila.rojas@email.com', 4141234567, '1996-08-19', '2024-03-25', 'Inactivo', 'Universidad', 'Femenino'),
(329, 'Daniel', 'Morales', 44556677, 15, 'daniel.morales@email.com', 4161234568, '2008-05-30', '2025-03-10', 'Inactivo', 'Primaria', 'Masculino'),
(330, 'Gabriela', 'Ortiz', 55667788, 40, 'gabriela.ortiz@email.com', 4241234569, '1983-11-12', '2024-01-20', 'Inactivo', 'Universidad', 'Femenino'),
(331, 'Andrés', 'Vargas', 66778899, 23, 'andres.vargas@email.com', 4171234568, '2000-07-24', '2024-08-30', 'Asignado', 'Universidad', 'Masculino'),
(332, 'Natalia', 'Gómez', 77889900, 29, 'natalia.gomez@email.com', 4131234568, '1994-04-16', '2024-06-05', 'Inactivo', 'Universidad', 'Femenino'),
(333, 'Diego', 'Silva', 88990011, 21, 'diego.silva@email.com', 4261234569, '2002-09-03', '2024-12-12', 'En sistema', 'Universidad', 'Masculino'),
(334, 'Valeria', 'Mendoza', 99001122, 36, 'valeria.mendoza@email.com', 4121234570, '1987-01-28', '2024-02-28', 'Asignado', 'Universidad', 'Femenino'),
(335, 'Ricardo', 'Castro', 10293847, 75, 'ricardo.castro@email.com', 4241234570, '1948-06-15', '2024-04-10', 'Asignado', 'Universidad', 'Masculino');

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
(387, 316, 71, '2024-05-20', '2024-06-10', '2024-06-24', 'Aprobado'),
(388, 317, 72, '2024-06-05', '2024-07-01', '2024-07-15', 'Reprobado'),
(389, 318, 73, '2024-07-15', '2024-08-05', '2024-08-19', 'Aprobado'),
(390, 319, 74, '2024-07-25', '2024-09-02', '2024-09-16', 'Ausente'),
(391, 320, 75, '2024-09-01', '2024-10-07', '2024-10-21', 'Aprobado'),
(392, 321, 76, '2024-10-15', '2024-11-04', '2024-11-18', 'Reprobado'),
(393, 322, 77, '2024-12-05', '2025-01-06', '2025-01-20', 'Aprobado'),
(394, 323, 78, '2025-01-10', '2025-02-03', '2025-02-17', 'Ausente'),
(395, 324, 79, '2025-02-15', '2025-03-10', '2025-03-24', 'Aprobado'),
(396, 325, 80, '2025-03-01', '2025-04-07', '2025-04-21', 'Reprobado'),
(397, 316, 81, '2025-04-05', '2025-05-05', '2025-05-19', 'Aprobado'),
(398, 317, 82, '2025-05-15', '2025-06-02', '2025-06-16', 'Reprobado'),
(399, 318, 83, '2025-06-10', '2025-07-07', '2025-07-21', 'Aprobado'),
(400, 319, 84, '2025-07-15', '2025-08-04', '2025-08-18', 'Ausente'),
(401, 320, 85, '2025-08-05', '2025-09-01', '2025-09-15', 'Aprobado'),
(402, 321, 86, '2025-09-10', '2025-10-06', '2025-10-20', 'Reprobado'),
(403, 322, 87, '2025-10-15', '2025-11-03', '2025-11-17', 'Aprobado'),
(404, 323, 88, '2025-11-10', '2025-12-01', '2025-12-15', 'Ausente'),
(405, 324, 89, '2025-12-15', '2026-01-05', '2026-01-19', 'Aprobado'),
(406, 325, 90, '2026-01-10', '2026-02-02', '2026-02-16', 'Reprobado'),
(407, 326, 71, '2024-05-18', '2024-06-10', '2024-06-24', 'Aprobado'),
(408, 327, 72, '2024-06-08', '2024-07-01', '2024-07-15', 'Reprobado'),
(409, 328, 73, '2024-07-18', '2024-08-05', '2024-08-19', 'Aprobado'),
(410, 329, 74, '2024-07-28', '2024-09-02', '2024-09-16', 'Ausente'),
(411, 330, 75, '2024-09-05', '2024-10-07', '2024-10-21', 'Aprobado'),
(412, 331, 76, '2024-10-18', '2024-11-04', '2024-11-18', 'Reprobado'),
(413, 332, 77, '2024-12-08', '2025-01-06', '2025-01-20', 'Aprobado'),
(414, 333, 78, '2025-01-12', '2025-02-03', '2025-02-17', 'Ausente'),
(415, 334, 79, '2025-02-18', '2025-03-10', '2025-03-24', 'Aprobado'),
(416, 335, 80, '2025-03-05', '2025-04-07', '2025-04-21', 'Reprobado'),
(447, 316, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'En formación'),
(448, 317, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'En formación'),
(449, 318, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'En formación'),
(450, 319, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'En formación'),
(451, 320, 72, '2025-05-28', '2025-05-01', '2025-07-01', 'En formación'),
(452, 323, 79, '2025-05-28', '2025-03-10', '2025-03-24', 'Pendiente'),
(453, 321, 76, '2025-05-28', '2025-11-04', '2025-11-30', 'Pendiente'),
(454, 325, 76, '2025-05-28', '2025-11-04', '2025-11-30', 'Pendiente'),
(455, 331, 74, '2025-05-28', '2024-09-02', '2024-09-16', 'Pendiente'),
(456, 335, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(457, 320, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(458, 321, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(459, 322, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente'),
(460, 334, 71, '2025-05-28', '2025-07-30', '2025-10-30', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
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

INSERT INTO `tb_usuario` (`id_usuario`, `nombre`, `apellido`, `nombre_usuario`, `correo`, `password`, `rol`, `imagen`, `estado_usuario`) VALUES
(11, 'Daniel', 'Bringtown', 'ADMIN', 'dbringtown1@gmail.com', '$2y$10$mdOsh/Pim6tsKM27z63W2e36oi4zWV42t4e7PavWmcwGR2piYs.Fi', 'Administrador', 'assets/img/avatar/avatar_680da3ed12fa2.jpg', 'Activo'),
(13, 'Gianmarino', 'Di renzo', 'Gianmarino', 'dbringtown2@gmail.com', '$2y$10$1OPhWnf4z2aHIJpXO5QOAeZ6RfN0lMx/VhsWFFNL04IaUWPY5uf0W', 'Apoyo', 'assets/img/avatar/default-user.png', 'Inactivo'),
(14, 'Jose', 'Benitez', 'Jbenitez', 'dbringtown12@gmail.com', '$2y$10$1HNTuArHRCrmq9yMywGFAu8M7R65NL0PA8FnjoY8UuC.YtMKzXFHe', 'Administrador', 'assets/img/avatar/default-user.png', 'Activo'),
(15, 'Mary', 'Serrada', 'INCES2025', 'dbringtown13@gmail.com', '$2y$10$z21kNN/cWNRTBgjugD6ij.4669Z5h5PWkhEYJMMvAwOe/KerCJ2MO', 'Apoyo', 'assets/img/avatar/default-user.png', 'Activo');

--
-- Índices para tablas volcadas
--

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
  ADD KEY `id_areaFormativa` (`id_areaFormativa`);

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
  ADD KEY `id_docente` (`id_docente`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  ADD PRIMARY KEY (`id_estatusCurso`);

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
-- Indices de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  ADD PRIMARY KEY (`id_participante`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  ADD PRIMARY KEY (`id_participante_curso`),
  ADD KEY `id_participante` (`id_participante`),
  ADD KEY `id_curso` (`id_curso`);

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
-- AUTO_INCREMENT de la tabla `tb_areaformativa`
--
ALTER TABLE `tb_areaformativa`
  MODIFY `id_areaFormativa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tb_curso`
--
ALTER TABLE `tb_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `tb_docente`
--
ALTER TABLE `tb_docente`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT de la tabla `tb_participante`
--
ALTER TABLE `tb_participante`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=336;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=461;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_curso`
--
ALTER TABLE `tb_curso`
  ADD CONSTRAINT `tb_curso_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `tb_modalidad` (`id_modalidad`),
  ADD CONSTRAINT `tb_curso_ibfk_2` FOREIGN KEY (`id_estatusCurso`) REFERENCES `tb_estatuscurso` (`id_estatusCurso`),
  ADD CONSTRAINT `tb_curso_ibfk_3` FOREIGN KEY (`id_motor`) REFERENCES `tb_motor` (`id_motor`),
  ADD CONSTRAINT `tb_curso_ibfk_4` FOREIGN KEY (`id_areaFormativa`) REFERENCES `tb_areaformativa` (`id_areaFormativa`);

--
-- Filtros para la tabla `tb_docente_curso`
--
ALTER TABLE `tb_docente_curso`
  ADD CONSTRAINT `tb_docente_curso_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `tb_docente` (`id_docente`),
  ADD CONSTRAINT `tb_docente_curso_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `tb_curso` (`id_curso`);

--
-- Filtros para la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  ADD CONSTRAINT `tb_participante_curso_ibfk_1` FOREIGN KEY (`id_participante`) REFERENCES `tb_participante` (`id_participante`),
  ADD CONSTRAINT `tb_participante_curso_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `tb_curso` (`id_curso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2026 a las 02:22:04
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

INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `id_sectoreconomico`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `id_ppl`, `id_nombre_especifico`, `turno`, `programa_formacion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Curso Base No Utilizable', '2025-08-01', NULL, NULL, NULL, NULL, 0.00, 30, 10, 0, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Mañana', NULL, '2026-05-25 01:46:16', '2026-05-25 01:47:50'),
(2, 'Panadería Básica I', '2025-08-04', '2025-07-20', '2025-09-12', 60.00, 4.00, 60.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-08-01 13:00:00', '2026-05-25 01:46:16'),
(3, 'Introducción a la Ofimática', '2025-08-11', '2025-07-25', '2025-09-19', 40.00, 4.00, 40.00, 25, 10, 12, 2, 'Unidad Curricular', 5, 1, 3, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-08-05 14:30:00', '2026-05-25 01:46:16'),
(4, 'Confección de Uniformes Escolares', '2025-09-08', '2025-08-20', '2025-10-17', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 2, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-09-01 13:00:00', '2026-05-25 01:46:16'),
(5, 'Seguridad y Salud en el Trabajo', '2025-09-15', '2025-08-25', '2025-10-24', 40.00, 4.00, 40.00, 30, 10, 11, 2, 'Unidad Curricular', 5, 1, 2, 5, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-09-05 14:00:00', '2026-05-25 01:46:16'),
(6, 'Matemática Aplicada', '2025-10-06', '2025-09-20', '2025-11-14', 60.00, 3.00, 60.00, 25, 10, 10, 3, 'Unidad Curricular', 5, 1, 3, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-10-01 13:00:00', '2026-05-25 01:46:16'),
(7, 'Ortografía y Redacción Eficaz', '2025-10-13', '2025-09-25', '2025-11-21', 30.00, 3.00, 30.00, 25, 10, 12, 2, 'Unidad Curricular', 5, 1, 3, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-10-05 14:00:00', '2026-05-25 01:46:16'),
(8, 'Ética y Valores', '2025-10-20', '2025-10-05', '2025-12-12', 40.00, 4.00, 40.00, 30, 10, 11, 1, 'Unidad Curricular', 5, 1, 3, 6, 5, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-10-10 15:30:00', '2026-05-25 01:46:16'),
(9, 'Panadería y Pastelería Navideña', '2026-06-08', '2026-06-17', '2026-06-26', 60.00, 4.00, 0.00, 20, 12, 0, 1, 'Unidad Curricular', 2, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-11-01 13:00:00', '2026-06-09 21:51:36'),
(10, 'Dibujo Técnico Básico', '2025-11-10', '2025-10-25', '2025-12-19', 40.00, 3.00, 40.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 6, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-11-05 14:15:00', '2026-05-25 01:46:16'),
(11, 'Lectura e Interpretación de Planos', '2025-11-17', '2025-11-01', '2025-12-26', 60.00, 4.00, 60.00, 25, 10, 11, 3, 'Unidad Curricular', 5, 2, 1, 6, 4, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-11-12 18:00:00', '2026-05-25 01:46:16'),
(12, 'Higiene y Manipulación de Alimentos I', '2025-12-01', '2025-11-15', '2026-01-09', 30.00, 3.00, 30.00, 30, 10, 10, 1, 'Unidad Curricular', 5, 1, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-12-01 13:00:00', '2026-05-25 01:46:16'),
(13, 'Administración de Pequeñas Empresas', '2025-12-08', '2025-11-20', '2026-01-16', 60.00, 4.00, 60.00, 25, 10, 11, 2, 'Unidad Curricular', 5, 1, 2, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-12-05 14:00:00', '2026-05-25 01:46:16'),
(14, 'Cálculo de Costos y Presupuestos', '2026-01-05', '2025-12-20', '2026-02-13', 60.00, 4.00, 60.00, 25, 10, 10, 3, 'Unidad Curricular', 5, 1, 2, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-02 13:00:00', '2026-05-25 01:46:16'),
(15, 'Mecánica de Motores Básica', '2026-01-12', '2025-12-28', '2026-02-20', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 3, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-08 15:00:00', '2026-05-25 01:46:16'),
(16, 'Habilidades Socioemocionales I', '2026-01-19', '2026-01-05', '2026-03-13', 30.00, 3.00, 30.00, 30, 10, 11, 1, 'Unidad Curricular', 5, 1, 3, 6, 5, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-15 19:30:00', '2026-05-25 01:46:16'),
(17, 'Panadería y Pastelería Básica II', '2026-02-02', '2026-01-15', '2026-03-13', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-01 13:00:00', '2026-05-25 01:46:16'),
(18, 'Algoritmia y Programación Bísica', '2026-02-09', '2026-01-25', '2026-03-20', 80.00, 4.00, 80.00, 25, 10, 11, 2, 'Unidad Curricular', 5, 3, 1, 9, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-02-05 15:30:00', '2026-05-25 01:46:16'),
(19, 'Confección de Prendas de Vestir', '2026-02-16', '2026-02-01', '2026-04-10', 80.00, 4.00, 80.00, 20, 10, 12, 1, 'Unidad Curricular', 5, 2, 1, 2, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-12 18:15:00', '2026-05-25 01:46:16'),
(20, 'Dibujo Técnico Industrial', '2026-02-23', '2026-02-10', '2026-04-17', 60.00, 3.00, 0.00, 20, 10, 6, 1, 'Unidad Curricular', 5, 2, 1, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-20 19:45:00', '2026-05-25 01:47:50'),
(21, 'Mecánica de Motores a Gasolina II', '2026-03-02', '2026-02-15', '2026-04-10', 100.00, 5.00, 100.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 3, 1, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-03-01 13:00:00', '2026-05-25 01:46:16'),
(22, 'Inglés con Propósitos Generales II', '2026-03-09', '2026-02-25', '2026-04-17', 60.00, 3.00, 60.00, 30, 10, 11, 2, 'Unidad Curricular', 5, 1, 3, 6, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-05 14:15:00', '2026-05-25 01:46:16'),
(23, 'Higiene y Manipulación de Alimentos II', '2026-03-16', '2026-03-01', '2026-05-08', 40.00, 4.00, 40.00, 25, 10, 12, 1, 'Unidad Curricular', 5, 1, 3, 1, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-12 15:00:00', '2026-05-25 01:46:16'),
(24, 'Recreación y Dinámicas Infantiles', '2026-03-23', '2026-03-10', '2026-05-01', 40.00, 4.00, 20.00, 20, 10, 7, 3, 'Unidad Curricular', 5, 1, 3, 8, 4, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-20 18:00:00', '2026-05-25 01:47:50'),
(25, 'Base de Datos Relacionales', '2026-04-06', '2026-03-20', '2026-05-29', 80.00, 4.00, 60.00, 25, 10, 14, 2, 'Unidad Curricular', 5, 3, 1, 9, 1, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-04-02 13:30:00', '2026-05-30 15:13:15'),
(26, 'Confección de Uniformes Médicos', '2026-04-13', '2026-03-30', '2026-06-05', 80.00, 4.00, 48.00, 20, 10, 12, 1, 'Unidad Curricular', 5, 2, 1, 2, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-08 14:00:00', '2026-06-09 21:51:40'),
(27, 'Electricidad Básica Residencial II', '2026-04-20', '2026-04-05', '2026-06-12', 90.00, 4.00, 36.00, 20, 10, 15, 1, 'Unidad Curricular', 3, 2, 1, 10, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-15 16:00:00', '2026-05-25 01:46:16'),
(28, 'Mercadeo y Ventas Digitales II', '2026-05-04', '2026-04-20', '2026-06-26', 40.00, 4.00, 12.00, 30, 10, 10, 2, 'Unidad Curricular', 3, 1, 2, 6, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-01 13:00:00', '2026-05-25 01:46:16'),
(29, 'Diseño y Construcción de Redes II', '2026-05-11', '2026-04-25', '2026-07-03', 80.00, 4.00, 8.00, 20, 10, 8, 3, 'Unidad Curricular', 2, 3, 1, 9, 5, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-05 15:30:00', '2026-05-25 01:47:55'),
(30, 'Higiene y Manipulación de Alimentos III', '2026-05-18', '2026-05-01', '2026-07-10', 30.00, 3.00, 3.00, 30, 10, 13, 1, 'Unidad Curricular', 3, 1, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-12 18:00:00', '2026-05-25 01:46:16'),
(31, 'Panadería y Pastelería Avanzada II', '2026-06-01', '2026-05-15', '2026-07-15', 100.00, 4.00, 0.00, 20, 10, 8, 1, 'Unidad Curricular', 2, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-18 19:30:00', '2026-05-25 01:47:55'),
(32, 'Algoritmia y Programación Avanzada', '2026-06-08', '2026-05-20', '2026-07-20', 80.00, 4.00, 0.00, 25, 10, 10, 2, 'Unidad Curricular', 3, 3, 1, 9, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-22 20:45:00', '2026-05-25 01:47:55'),
(33, 'Curso Descontinuado de Prueba', '2025-10-01', '2025-09-15', '2025-11-01', 40.00, 4.00, 10.00, 20, 10, 0, 1, 'Unidad Curricular', 5, 1, 3, 5, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-09-25 14:00:00', '2026-05-25 01:47:50');

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
(1, 'Carlos', 'Eduardo', 'Rodriguez', 'Mendoza', 12345601, 'carlos.rodriguez@inces.edu.ve', '1980-01-15', 46, 'Asignado', 'Masculino', 'Fijo'),
(2, 'Maria', 'Elena', 'Gonzalez', 'Flores', 12345602, 'maria.gonzalez@inces.edu.ve', '1985-02-20', 41, 'Asignado', 'Femenino', 'Fijo'),
(3, 'Luis', 'Alberto', 'Hernandez', 'Rivas', 12345603, 'luis.hernandez@inces.edu.ve', '1978-03-25', 48, 'Asignado', 'Masculino', 'Contratado'),
(4, 'Ana', 'Gabriela', 'Lopez', 'Acosta', 12345604, 'ana.lopez@inces.edu.ve', '1990-04-30', 36, 'Asignado', 'Femenino', 'Contratado'),
(5, 'Jose', 'Manuel', 'Martinez', 'Torres', 12345605, 'jose.martinez@inces.edu.ve', '1983-05-12', 43, 'Asignado', 'Masculino', 'PDL'),
(6, 'Sofia', 'Valentina', 'Perez', 'Mendoza', 12345606, 'sofia.perez@inces.edu.ve', '1995-06-18', 31, 'Asignado', 'Femenino', 'Voluntario'),
(7, 'Juan', 'Carlos', 'Gomez', 'Castillo', 12345607, 'juan.gomez@inces.edu.ve', '1975-07-22', 50, 'Disponible', 'Masculino', 'Fijo'),
(8, 'Laura', 'Beatriz', 'Sanchez', 'Rojas', 12345608, 'laura.sanchez@inces.edu.ve', '1988-08-05', 37, 'Disponible', 'Femenino', 'Fijo'),
(9, 'Pedro', 'José', 'Alvarez', 'Guzman', 12345609, 'pedro.alvarez@inces.edu.ve', '1982-09-14', 43, 'Asignado', 'Masculino', 'Contratado'),
(10, 'Carmen', 'Julia', 'Diaz', 'Salazar', 12345610, 'carmen.diaz@inces.edu.ve', '1987-10-09', 38, 'Asignado', 'Femenino', 'PDL'),
(11, 'Francisco', 'Javier', 'Vasquez', 'Delgado', 12345611, 'francisco.vasquez@inces.edu.ve', '1970-11-03', 55, 'En formación', 'Masculino', 'Fijo'),
(12, 'Patricia', 'Alejandra', 'Castro', 'Vargas', 12345612, 'patricia.castro@inces.edu.ve', '1992-12-28', 33, 'Asignado', 'Femenino', 'Voluntario'),
(13, 'Miguel', 'Angel', 'Torres', 'Garcia', 12345613, 'miguel.torres@inces.edu.ve', '1981-04-14', 45, 'En formación', 'Masculino', 'Contratado'),
(14, 'Yolanda', 'Beatriz', 'Perez', 'Blanco', 12345614, 'yolanda.perez@inces.edu.ve', '1989-08-22', 36, 'En formación', 'Femenino', 'Voluntario'),
(15, 'Héctor', 'Jose', 'Guzman', 'Herrera', 12345615, 'hector.guzman@inces.edu.ve', '1972-10-10', 53, 'Inactivo', 'Masculino', 'Fijo');

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
(1, 1, 2, '2025-08-01'),
(2, 2, 3, '2025-08-05'),
(3, 3, 4, '2025-09-01'),
(4, 4, 5, '2025-09-05'),
(5, 5, 6, '2025-10-01'),
(6, 6, 7, '2025-10-05'),
(7, 9, 8, '2025-10-10'),
(8, 10, 9, '2025-11-01'),
(9, 11, 10, '2025-11-05'),
(10, 12, 11, '2025-11-12'),
(11, 1, 12, '2025-12-01'),
(12, 2, 13, '2025-12-05'),
(13, 3, 14, '2026-01-02'),
(14, 4, 15, '2026-01-08'),
(15, 5, 16, '2026-01-15'),
(16, 6, 17, '2026-02-01'),
(17, 9, 18, '2026-02-05'),
(18, 10, 19, '2026-02-12'),
(19, 11, 20, '2026-02-20'),
(20, 12, 21, '2026-03-01'),
(21, 1, 22, '2026-03-05'),
(22, 2, 23, '2026-03-12'),
(23, 3, 24, '2026-03-20'),
(24, 4, 25, '2026-04-02'),
(25, 5, 26, '2026-04-08'),
(26, 6, 27, '2026-04-15'),
(27, 9, 28, '2026-05-01'),
(28, 10, 29, '2026-05-05'),
(29, 11, 30, '2026-05-12'),
(30, 12, 31, '2026-05-18'),
(31, 1, 32, '2026-05-22'),
(32, 2, 33, '2025-09-25');

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
(1, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-08-01 12:00:00'),
(2, 'Crear', 'Se creó el curso: Panadería Básica I', 'tb_curso', 2, 11, '2025-08-01 13:30:00'),
(3, 'Editar', 'Se asignó el docente Carlos Rodriguez al curso Panadería Básica I', 'tb_docente_curso', 1, 11, '2025-08-01 13:45:00'),
(4, 'Crear', 'Se registró el participante: Diego Romero', 'tb_participante', 1, 11, '2025-08-05 15:20:00'),
(5, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2025-09-02 12:15:00'),
(6, 'Crear', 'Se registró el participante: Sofia Morillo', 'tb_participante', 7, 16, '2025-09-02 15:00:00'),
(7, 'Editar', 'Se completó y culminó el curso: Panadería Básica I', 'tb_curso', 2, 16, '2025-09-12 16:00:00'),
(8, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-10-02 12:00:00'),
(9, 'Crear', 'Se registró la meta: Inscritos CFS T-IV', 'tb_metas', 2, 11, '2025-10-02 13:00:00'),
(10, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2025-11-02 12:15:00'),
(11, 'Crear', 'Se creó el curso: Panadería y Pastelería Navideña', 'tb_curso', 9, 16, '2025-11-01 14:00:00'),
(12, 'Editar', 'Se completó y culminó el curso: Confección de Uniformes Escolares', 'tb_curso', 4, 16, '2025-10-17 16:00:00'),
(13, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-12-02 12:00:00'),
(14, 'Crear', 'Se registró el participante: Florencia Fuentes', 'tb_participante', 31, 11, '2025-12-02 14:30:00'),
(15, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-01-05 12:15:00'),
(16, 'Crear', 'Se creó el curso: Cálculo de Costos y Presupuestos', 'tb_curso', 14, 16, '2026-01-02 13:30:00'),
(17, 'Editar', 'Se completó y culminó el curso: Higiene y Manipulación de Alimentos I', 'tb_curso', 12, 16, '2026-01-09 16:00:00'),
(18, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-02-02 12:00:00'),
(19, 'Crear', 'Se creó el curso: Dibujo Técnico Industrial', 'tb_curso', 20, 11, '2026-02-20 15:00:00'),
(20, 'Editar', 'Se completó y culminó el curso: Cálculo de Costos y Presupuestos', 'tb_curso', 14, 11, '2026-02-13 16:00:00'),
(21, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-02 12:15:00'),
(22, 'Crear', 'Se creó el curso: Recreación y Dinámicas Infantiles', 'tb_curso', 24, 16, '2026-03-20 14:00:00'),
(23, 'Editar', 'Se completó y culminó el curso: Habilidades Socioemocionales I', 'tb_curso', 16, 16, '2026-03-13 16:00:00'),
(24, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-04-02 12:00:00'),
(25, 'Crear', 'Se creó el curso: Base de Datos Relacionales', 'tb_curso', 25, 11, '2026-04-02 13:30:00'),
(26, 'Editar', 'Se completó y culminó el curso: Confección de Prendas de Vestir', 'tb_curso', 19, 11, '2026-04-10 16:00:00'),
(27, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-02 12:15:00'),
(28, 'Crear', 'Se registró la meta: Comunas Abr-Jul', 'tb_metas', 6, 16, '2026-05-02 13:00:00'),
(29, 'Crear', 'Se creó el curso: Mercadeo y Ventas Digitales II', 'tb_curso', 28, 16, '2026-05-01 14:30:00'),
(30, 'Editar', 'Se completó y culminó el curso: Higiene y Manipulación de Alimentos II', 'tb_curso', 23, 16, '2026-05-08 16:00:00'),
(31, 'Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-05-25 01:15:00'),
(32, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-25 01:47:50'),
(33, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-25 02:19:36'),
(34, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-25 03:08:27'),
(35, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-27 00:53:53'),
(36, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-27 01:28:27'),
(37, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-27 02:32:42'),
(38, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-30 15:13:11'),
(39, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-30 16:54:27'),
(40, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-31 14:19:54'),
(41, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-31 14:34:29'),
(42, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-09 21:51:36'),
(43, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-09 21:54:44'),
(44, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-09 22:39:06'),
(45, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 03:23:40'),
(46, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 03:47:39'),
(47, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 03:55:28'),
(48, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 18:44:56'),
(49, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 19:45:46'),
(50, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 20:49:48'),
(51, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 21:19:16'),
(52, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 22:08:36'),
(53, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 22:48:48'),
(54, 'Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-06-10 23:52:58');

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
(1, NULL, 20, 29, 'Aprobados', '2025-08-01', '2025-09-30', 'Cerrada', '2026-05-25 01:46:17', 0, 'Trimestre III 2025'),
(2, 1, 25, 30, 'Inscritos', '2025-10-01', '2025-12-31', 'Cerrada', '2026-05-25 01:46:17', 0, 'Inscritos CFS T-IV'),
(3, NULL, 80, 64, 'Aprobados', '2025-10-01', '2025-12-31', 'Cerrada', '2026-05-25 01:46:17', 0, 'Aprobados Globales T'),
(4, NULL, 35, 106, 'Aprobados', '2026-01-01', '2026-03-31', 'Cerrada', '2026-05-25 01:46:17', 0, 'Aprobados Globales T'),
(5, NULL, 150, 70, 'Inscritos', '2026-01-01', '2026-06-30', 'Activa', '2026-05-25 01:46:17', 0, 'Semestre I 2026'),
(6, 2, 15, 12, 'Aprobados', '2026-04-01', '2026-07-31', 'Activa', '2026-05-25 01:46:17', 0, 'Comunas Abr-Jul');

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
  `estado` enum('En formacion','Inactivo','Disponible','Asignado','En sistema','Diferido') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `grado_institucion` enum('Primaria','Bachillerato','Universidad') NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `id_discapacidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_participante`
--

INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
(1, 'Diego', 'Alejandro', 'Romero', 'Silva', 32000001, 21, 'diego.romero@gmail.com', '2005-01-10', '2025-08-05', 'En formacion', 'Bachillerato', 'Masculino', 1),
(2, 'Camila', 'Isabella', 'Rengel', 'Morillo', 32000002, 19, 'camila.rengel@gmail.com', '2007-02-14', '2025-08-08', 'En formacion', 'Bachillerato', 'Femenino', 1),
(3, 'Mateo', 'Daniel', 'Rivas', 'Garcia', 32000003, 20, 'mateo.rivas@gmail.com', '2006-03-22', '2025-08-12', 'Asignado', 'Universidad', 'Masculino', 1),
(4, 'Valentina', 'Andrea', 'Sucre', 'Perez', 32000004, 25, 'valentina.sucre@gmail.com', '2001-04-05', '2025-08-18', 'En formacion', 'Universidad', 'Femenino', 2),
(5, 'Santiago', 'Manuel', 'Santos', 'Flores', 32000005, 23, 'santiago.santos@gmail.com', '2003-05-12', '2025-08-22', 'Asignado', 'Bachillerato', 'Masculino', 1),
(6, 'Sebastian', 'Ignacio', 'Serrada', 'Castillo', 32000006, 22, 'seb.serrada@gmail.com', '2004-06-25', '2025-08-25', 'Asignado', 'Bachillerato', 'Masculino', 1),
(7, 'Sofia', 'Alejandra', 'Morillo', 'Ortiz', 32000007, 24, 'sofia.morillo@gmail.com', '2002-07-30', '2025-09-02', 'Disponible', 'Universidad', 'Femenino', 1),
(8, 'Daniel', 'David', 'Acosta', 'López', 32000008, 27, 'daniel.acosta@gmail.com', '1999-08-14', '2025-09-05', 'Disponible', 'Primaria', 'Masculino', 3),
(9, 'Isabella', 'Rosa', 'Mendoza', 'Flores', 32000009, 21, 'isabella.mendoza@gmail.com', '2005-09-09', '2025-09-12', 'Disponible', 'Bachillerato', 'Femenino', 1),
(10, 'Samuel', 'Jose', 'Torres', 'Ramirez', 32000010, 21, 'samuel.torres@gmail.com', '2004-10-27', '2025-09-15', 'En formacion', 'Universidad', 'Masculino', 1),
(11, 'Gabriela', 'Teresa', 'Rojas', 'Medina', 32000011, 23, 'gabriela.rojas@gmail.com', '2003-11-12', '2025-09-18', 'En formacion', 'Bachillerato', 'Femenino', 1),
(12, 'Lucas', 'Benjamin', 'Cabrera', 'Vargas', 32000012, 19, 'lucas.cabrera@gmail.com', '2007-12-05', '2025-09-22', 'Disponible', 'Bachillerato', 'Masculino', 4),
(13, 'Mariana', 'Patricia', 'Castillo', 'Guzman', 32000013, 26, 'mariana.castillo@gmail.com', '2000-01-22', '2025-09-26', 'En formacion', 'Universidad', 'Femenino', 1),
(14, 'Nicolas', 'Andres', 'Salazar', 'Moreno', 32000014, 28, 'nicolas.salazar@gmail.com', '1998-02-18', '2025-10-02', 'En formacion', 'Bachillerato', 'Masculino', 1),
(15, 'Victoria', 'Beatriz', 'Delgado', 'Ortega', 32000015, 30, 'victoria.delgado@gmail.com', '1996-03-15', '2025-10-05', 'Disponible', 'Universidad', 'Femenino', 1),
(16, 'Benjamin', 'Edgar', 'Vargas', 'Guerrero', 32000016, 29, 'benjamin.vargas@gmail.com', '1997-04-20', '2025-10-08', 'En formacion', 'Primaria', 'Masculino', 2),
(17, 'Daniela', 'Elizabeth', 'Ortiz', 'Cruz', 32000017, 33, 'daniela.ortiz@gmail.com', '1993-05-30', '2025-10-12', 'En formacion', 'Universidad', 'Femenino', 1),
(18, 'Felipe', 'Julio', 'Caceres', 'Herrera', 32000018, 35, 'felipe.caceres@gmail.com', '1991-06-14', '2025-10-15', 'Diferido', 'Bachillerato', 'Masculino', 1),
(19, 'Natalia', 'Carmen', 'Benitez', 'Gil', 32000019, 22, 'natalia.benitez@gmail.com', '2004-07-08', '2025-10-18', 'En formacion', 'Bachillerato', 'Femenino', 1),
(20, 'Emmanuel', 'Edwin', 'Ferrer', 'Fuentes', 32000020, 24, 'emmanuel.ferrer@gmail.com', '2002-08-25', '2025-10-22', 'Disponible', 'Bachillerato', 'Masculino', 1),
(21, 'Lucia', 'Isabel', 'Blanco', 'Perez', 32000021, 20, 'lucia.blanco@gmail.com', '2006-09-02', '2025-10-25', 'En formacion', 'Bachillerato', 'Femenino', 1),
(22, 'Joaquin', 'Nelson', 'Ruiz', 'Alvarez', 32000022, 26, 'joaquin.ruiz@gmail.com', '2000-10-12', '2025-11-02', 'En formacion', 'Universidad', 'Masculino', 1),
(23, 'Maria', 'Jose', 'Jimenez', 'Castro', 32000023, 22, 'maria.jimenez@gmail.com', '2004-11-19', '2025-11-05', 'Disponible', 'Universidad', 'Femenino', 3),
(24, 'Tomas', 'Franklin', 'Muñoz', 'Acosta', 32000024, 30, 'tomas.munoz@gmail.com', '1996-12-11', '2025-11-10', 'En formacion', 'Bachillerato', 'Masculino', 1),
(25, 'Catalina', 'Gabriela', 'Moreno', 'Torres', 32000025, 25, 'catalina.moreno@gmail.com', '2001-01-08', '2025-11-15', 'Diferido', 'Universidad', 'Femenino', 1),
(26, 'Agustin', 'Richard', 'Ortega', 'Flores', 32000026, 27, 'agustin.ortega@gmail.com', '1999-02-28', '2025-11-18', 'En formacion', 'Primaria', 'Masculino', 1),
(27, 'Emilia', 'Sofía', 'Guerrero', 'Rivas', 32000027, 21, 'emilia.guerrero@gmail.com', '2005-03-17', '2025-11-20', 'Disponible', 'Bachillerato', 'Femenino', 1),
(28, 'Simon', 'Wilmer', 'Cruz', 'Mendoza', 32000028, 23, 'simon.cruz@gmail.com', '2003-04-15', '2025-11-22', 'En formacion', 'Bachillerato', 'Masculino', 1),
(29, 'Antonella', 'Valentina', 'Herrera', 'Castillo', 32000029, 19, 'anto.herrera@gmail.com', '2007-05-24', '2025-11-25', 'En formacion', 'Bachillerato', 'Femenino', 1),
(30, 'Maximiliano', 'Julio', 'Gil', 'Acosta', 32000030, 28, 'max.gil@gmail.com', '1998-06-03', '2025-11-28', 'Disponible', 'Bachillerato', 'Masculino', 1),
(31, 'Florencia', 'Rosa', 'Fuentes', 'Rojas', 32000031, 31, 'flor.fuentes@gmail.com', '1995-07-14', '2025-12-02', 'Disponible', 'Universidad', 'Femenino', 1),
(32, 'Gael', 'Oscar', 'Acosta', 'Mendoza', 32000032, 23, 'gael.acosta@gmail.com', '2003-08-20', '2025-12-05', 'Disponible', 'Bachillerato', 'Masculino', 2),
(33, 'Martina', 'Teresa', 'Torres', 'Medina', 32000033, 22, 'martina.torres@gmail.com', '2004-09-18', '2025-12-08', 'En formacion', 'Universidad', 'Femenino', 1),
(34, 'Bastian', 'Cesar', 'Medina', 'Rivas', 32000034, 25, 'bastian.medina@gmail.com', '2001-10-02', '2025-12-12', 'En formacion', 'Bachillerato', 'Masculino', 1),
(35, 'Isidora', 'Laura', 'Castillo', 'Silva', 32000035, 20, 'isi.castillo@gmail.com', '2006-11-12', '2025-12-15', 'En formacion', 'Bachillerato', 'Femenino', 1),
(36, 'Gaspar', 'Franklin', 'Guzman', 'Rengel', 32000036, 21, 'gaspar.guzman@gmail.com', '2005-12-05', '2025-12-18', 'Disponible', 'Bachillerato', 'Masculino', 1),
(37, 'Ignacia', 'Patricia', 'Moreno', 'Cumana', 32000037, 24, 'ignacia.moreno@gmail.com', '2002-01-30', '2025-12-22', 'Disponible', 'Universidad', 'Femenino', 1),
(38, 'Alonso', 'Manuel', 'Ortega', 'Santos', 32000038, 29, 'alonso.ortega@gmail.com', '1997-02-22', '2025-12-26', 'Diferido', 'Bachillerato', 'Masculino', 1),
(39, 'Elena', 'Beatriz', 'Perez', 'Sucre', 32000039, 27, 'elena.perez@gmail.com', '1999-03-11', '2025-12-28', 'En formacion', 'Universidad', 'Femenino', 1),
(40, 'Javier', 'Eduardo', 'Rodriguez', 'Serrada', 32000040, 32, 'javier.rodriguez@gmail.com', '1994-04-01', '2026-01-05', 'Disponible', 'Universidad', 'Masculino', 1),
(41, 'Isabel', 'Teresa', 'Martinez', 'Nolazco', 32000041, 35, 'isabel.martinez@gmail.com', '1991-05-19', '2026-01-08', 'Disponible', 'Primaria', 'Femenino', 1),
(42, 'Mateo', 'Alexander', 'Rivas', 'Gonzales', 32000042, 19, 'mateo.alex@gmail.com', '2007-06-08', '2026-01-12', 'Diferido', 'Bachillerato', 'Masculino', 1),
(43, 'Sofia', 'Del Valle', 'Sucre', 'Martinez', 32000043, 21, 'sofia.sucre@gmail.com', '2005-07-15', '2026-01-15', 'Disponible', 'Bachillerato', 'Femenino', 1),
(44, 'Diego', 'Jose', 'Santos', 'Rengel', 32000044, 25, 'diego.santos@gmail.com', '2001-08-30', '2026-01-18', 'Disponible', 'Universidad', 'Masculino', 1),
(45, 'Valentina', 'Rosa', 'Serrada', 'Martinez', 32000045, 23, 'vale.serrada@gmail.com', '2003-09-12', '2026-01-20', 'Disponible', 'Bachillerato', 'Femenino', 4),
(46, 'Lucas', 'Manuel', 'Morillo', 'Perez', 32000046, 22, 'lucas.morillo@gmail.com', '2004-10-05', '2026-01-22', 'Disponible', 'Bachillerato', 'Masculino', 1),
(47, 'Camila', 'Gabriela', 'Acosta', 'Flores', 32000047, 20, 'camila.acosta@gmail.com', '2006-11-25', '2026-01-25', 'Disponible', 'Bachillerato', 'Femenino', 1),
(48, 'Sebastian', 'Eduardo', 'Mendoza', 'Torres', 32000048, 27, 'seb.mendoza@gmail.com', '1999-01-03', '2026-01-28', 'Disponible', 'Universidad', 'Masculino', 1),
(49, 'Isabella', 'Elena', 'Torres', 'Medina', 32000049, 19, 'isabella.torres@gmail.com', '2007-02-28', '2026-02-02', 'Disponible', 'Bachillerato', 'Femenino', 1),
(50, 'Nicolas', 'Benjamín', 'Vargas', 'Vargas', 32000050, 31, 'nicolas.vargas@gmail.com', '1995-03-24', '2026-02-05', 'Disponible', 'Bachillerato', 'Masculino', 1),
(51, 'Daniela', 'Fernanda', 'Paredes', 'Acosta', 32000051, 22, 'daniela.p@gmail.com', '2004-04-12', '2026-02-08', 'Disponible', 'Bachillerato', 'Femenino', 1),
(52, 'Andrés', 'Felipe', 'Velasquez', 'Salazar', 32000052, 28, 'andres.v@gmail.com', '1998-05-18', '2026-02-12', 'Disponible', 'Universidad', 'Masculino', 1),
(53, 'Patricia', 'Juliana', 'Molina', 'Blanco', 32000053, 35, 'patricia.m@gmail.com', '1991-06-30', '2026-02-15', 'Disponible', 'Primaria', 'Femenino', 1),
(54, 'Gabriel', 'Alejandro', 'Rojas', 'Castillo', 32000054, 20, 'gabriel.r@gmail.com', '2006-07-22', '2026-02-18', 'Disponible', 'Bachillerato', 'Masculino', 2),
(55, 'Isabel', 'Teresa', 'Romero', 'Guzman', 32000055, 23, 'isabel.r@gmail.com', '2003-08-14', '2026-02-20', 'En formacion', 'Bachillerato', 'Femenino', 1),
(56, 'Julio', 'César', 'Salazar', 'Ortega', 32000056, 30, 'julio.s@gmail.com', '1996-09-05', '2026-02-22', 'En formacion', 'Universidad', 'Masculino', 1),
(57, 'Laura', 'Valentina', 'Medina', 'Moreno', 32000057, 24, 'laura.m@gmail.com', '2002-10-18', '2026-02-25', 'En formacion', 'Universidad', 'Femenino', 1),
(58, 'Oscar', 'David', 'Suarez', 'Delgado', 32000058, 29, 'oscar.s@gmail.com', '1997-11-25', '2026-02-28', 'Diferido', 'Bachillerato', 'Masculino', 1),
(59, 'Paola', 'Alejandra', 'Flores', 'Vargas', 32000059, 21, 'paola.f@gmail.com', '2005-01-30', '2026-03-02', 'Disponible', 'Bachillerato', 'Femenino', 1),
(60, 'Ricardo', 'José', 'Castro', 'Guerrero', 32000060, 27, 'ricardo.c@gmail.com', '1999-02-14', '2026-03-05', 'Disponible', 'Universidad', 'Masculino', 3),
(61, 'Silvia', 'Carolina', 'Ortega', 'Benitez', 32000061, 26, 'silvia.o@gmail.com', '2000-03-19', '2026-03-08', 'Disponible', 'Universidad', 'Femenino', 1),
(62, 'Víctor', 'Hugo', 'Rios', 'Gil', 32000062, 34, 'victor.r@gmail.com', '1992-04-25', '2026-03-12', 'Disponible', 'Bachillerato', 'Masculino', 1),
(63, 'Adriana', 'Mercedes', 'Mendoza', 'Blanco', 32000063, 23, 'adriana.m@gmail.com', '2003-05-15', '2026-03-15', 'Disponible', 'Universidad', 'Femenino', 1),
(64, 'Arturo', 'Leonel', 'Ferrer', 'Fuentes', 32000064, 25, 'arturo.f@gmail.com', '2001-06-20', '2026-03-18', 'Disponible', 'Bachillerato', 'Masculino', 1),
(65, 'Blanca', 'Nieves', 'Castillo', 'Ortega', 32000065, 30, 'blanca.c@gmail.com', '1996-07-08', '2026-03-20', 'Disponible', 'Primaria', 'Femenino', 1),
(66, 'César', 'Augusto', 'Vargas', 'Rengel', 32000066, 28, 'cesar.v@gmail.com', '1998-08-30', '2026-03-22', 'Disponible', 'Bachillerato', 'Masculino', 1),
(67, 'Diana', 'Carolina', 'Santos', 'Silva', 32000067, 19, 'diana.s@gmail.com', '2007-09-12', '2026-03-24', 'Disponible', 'Bachillerato', 'Femenino', 1),
(68, 'Eduardo', 'José', 'Sucre', 'Morillo', 32000068, 22, 'eduardo.s@gmail.com', '2004-10-05', '2026-03-26', 'Disponible', 'Bachillerato', 'Masculino', 1),
(69, 'Fabiola', 'Elena', 'Acosta', 'Rivas', 32000069, 21, 'fabiola.a@gmail.com', '2005-11-22', '2026-03-30', 'Disponible', 'Bachillerato', 'Femenino', 1),
(70, 'Gerardo', 'Antonio', 'Romero', 'Serrada', 32000070, 45, 'gerardo.r@gmail.com', '1981-05-14', '2026-04-02', 'En formacion', 'Primaria', 'Masculino', 1),
(71, 'Helena', 'Isabel', 'Serrada', 'Medina', 32000071, 38, 'helena.s@gmail.com', '1988-08-20', '2026-04-05', 'En formacion', 'Bachillerato', 'Femenino', 1),
(72, 'Iván', 'Darío', 'Morillo', 'Salazar', 32000072, 33, 'ivan.m@gmail.com', '1993-09-15', '2026-04-08', 'En formacion', 'Universidad', 'Masculino', 2),
(73, 'Julia', 'Teresa', 'Torres', 'Moreno', 32000073, 24, 'julia.t@gmail.com', '2002-02-18', '2026-04-10', 'En formacion', 'Universidad', 'Femenino', 1),
(74, 'Kevin', 'Eduardo', 'Vargas', 'Acosta', 32000074, 27, 'kevin.v@gmail.com', '1999-03-30', '2026-04-12', 'En formacion', 'Bachillerato', 'Masculino', 1),
(75, 'Lorena', 'Beatriz', 'Blanco', 'Torres', 32000075, 29, 'lorena.b@gmail.com', '1997-04-10', '2026-04-15', 'En formacion', 'Universidad', 'Femenino', 1),
(76, 'Mauricio', 'Andres', 'Gomez', 'Cruz', 32000076, 20, 'mauricio.g@gmail.com', '2006-06-12', '2026-04-18', 'En sistema', 'Bachillerato', 'Masculino', 1),
(77, 'Nora', 'Gabriela', 'Perez', 'Ruiz', 32000077, 23, 'nora.p@gmail.com', '2003-07-28', '2026-04-20', 'En sistema', 'Universidad', 'Femenino', 1),
(78, 'Omar', 'José', 'Medina', 'Castillo', 32000078, 22, 'omar.m@gmail.com', '2004-08-04', '2026-04-22', 'En sistema', 'Bachillerato', 'Masculino', 1),
(79, 'Paula', 'Isabel', 'Castro', 'Ortega', 32000079, 31, 'paula.c@gmail.com', '1995-10-14', '2026-04-25', 'En sistema', 'Universidad', 'Femenino', 1),
(80, 'Rafael', 'Jesús', 'Rodriguez', 'Moreno', 32000080, 26, 'rafael.r@gmail.com', '2000-11-09', '2026-04-28', 'En sistema', 'Bachillerato', 'Masculino', 1),
(81, 'Sonia', 'Alejandra', 'Gonzalez', 'Herrera', 32000081, 25, 'sonia.g@gmail.com', '2001-01-20', '2026-05-02', 'En sistema', 'Universidad', 'Femenino', 1),
(82, 'Tito', 'Wilmer', 'Lopez', 'Acosta', 32000082, 28, 'tito.l@gmail.com', '1998-02-15', '2026-05-05', 'En sistema', 'Bachillerato', 'Masculino', 1),
(83, 'Úrsula', 'Elena', 'Hernandez', 'Gil', 32000083, 20, 'ursula.h@gmail.com', '2006-03-12', '2026-05-08', 'En sistema', 'Bachillerato', 'Femenino', 1),
(84, 'Walter', 'César', 'Martinez', 'Fuentes', 32000084, 30, 'walter.m@gmail.com', '1996-04-24', '2026-05-12', 'En sistema', 'Universidad', 'Masculino', 1),
(85, 'Ximena', 'Rosa', 'Perez', 'Blanco', 32000085, 23, 'ximena.p@gmail.com', '2003-05-20', '2026-05-15', 'En sistema', 'Bachillerato', 'Femenino', 1),
(86, 'Yago', 'Nelson', 'Alvarez', 'Castillo', 32000086, 27, 'yago.a@gmail.com', '1999-06-30', '2026-05-18', 'En sistema', 'Universidad', 'Masculino', 1),
(87, 'Zoe', 'Sofía', 'Sanchez', 'Rojas', 32000087, 19, 'zoe.s@gmail.com', '2007-07-15', '2026-05-20', 'En sistema', 'Bachillerato', 'Femenino', 1),
(88, 'Alfredo', 'José', 'Diaz', 'Salazar', 32000088, 35, 'alfredo.d@gmail.com', '1991-08-05', '2026-05-22', 'En sistema', 'Primaria', 'Masculino', 1),
(89, 'Berta', 'Beatriz', 'Gomez', 'Castillo', 32000089, 21, 'berta.g@gmail.com', '2005-09-02', '2026-05-24', 'En sistema', 'Bachillerato', 'Femenino', 1),
(90, 'Claudio', 'José', 'Castillo', 'Vargas', 32000090, 24, 'claudio.c@gmail.com', '2002-10-10', '2026-05-24', 'En sistema', 'Bachillerato', 'Masculino', 1);

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
(1, 1, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(2, 2, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(3, 3, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(4, 4, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(5, 5, 2, '2025-08-02', '2025-08-04', '2025-09-12', 'Aprobado'),
(6, 6, 2, '2025-08-02', '2025-08-04', '2025-09-12', 'Aprobado'),
(7, 7, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Reprobado'),
(8, 8, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Ausente'),
(9, 9, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Aprobado'),
(10, 10, 2, '2025-08-04', '2025-08-04', '2025-09-12', 'Aprobado'),
(11, 11, 3, '2025-08-05', '2025-08-11', '2025-09-19', 'Aprobado'),
(12, 12, 3, '2025-08-05', '2025-08-11', '2025-09-19', 'Aprobado'),
(13, 13, 3, '2025-08-06', '2025-08-11', '2025-09-19', 'Ausente'),
(14, 14, 3, '2025-08-06', '2025-08-11', '2025-09-19', 'Aprobado'),
(15, 15, 3, '2025-08-08', '2025-08-11', '2025-09-19', 'Aprobado'),
(16, 16, 3, '2025-08-08', '2025-08-11', '2025-09-19', 'Aprobado'),
(17, 17, 3, '2025-08-09', '2025-08-11', '2025-09-19', 'Aprobado'),
(18, 18, 3, '2025-08-09', '2025-08-11', '2025-09-19', 'Reprobado'),
(19, 19, 3, '2025-08-10', '2025-08-11', '2025-09-19', 'Aprobado'),
(20, 20, 3, '2025-08-10', '2025-08-11', '2025-09-19', 'Aprobado'),
(21, 21, 3, '2025-08-11', '2025-08-11', '2025-09-19', 'Aprobado'),
(22, 22, 3, '2025-08-11', '2025-08-11', '2025-09-19', 'Aprobado'),
(23, 23, 4, '2025-09-02', '2025-09-08', '2025-10-17', 'Aprobado'),
(24, 24, 4, '2025-09-02', '2025-09-08', '2025-10-17', 'Aprobado'),
(25, 25, 4, '2025-09-03', '2025-09-08', '2025-10-17', 'Aprobado'),
(26, 26, 4, '2025-09-03', '2025-09-08', '2025-10-17', 'Reprobado'),
(27, 27, 4, '2025-09-04', '2025-09-08', '2025-10-17', 'Aprobado'),
(28, 28, 4, '2025-09-04', '2025-09-08', '2025-10-17', 'Aprobado'),
(29, 29, 4, '2025-09-05', '2025-09-08', '2025-10-17', 'Aprobado'),
(30, 30, 4, '2025-09-05', '2025-09-08', '2025-10-17', 'Aprobado'),
(31, 31, 4, '2025-09-06', '2025-09-08', '2025-10-17', 'Aprobado'),
(32, 32, 4, '2025-09-06', '2025-09-08', '2025-10-17', 'Aprobado'),
(33, 33, 5, '2025-09-08', '2025-09-15', '2025-10-24', 'Aprobado'),
(34, 34, 5, '2025-09-08', '2025-09-15', '2025-10-24', 'Aprobado'),
(35, 35, 5, '2025-09-09', '2025-09-15', '2025-10-24', 'Reprobado'),
(36, 36, 5, '2025-09-09', '2025-09-15', '2025-10-24', 'Aprobado'),
(37, 37, 5, '2025-09-10', '2025-09-15', '2025-10-24', 'Aprobado'),
(38, 38, 5, '2025-09-10', '2025-09-15', '2025-10-24', 'Aprobado'),
(39, 39, 5, '2025-09-12', '2025-09-15', '2025-10-24', 'Aprobado'),
(40, 40, 5, '2025-09-12', '2025-09-15', '2025-10-24', 'Aprobado'),
(41, 41, 5, '2025-09-14', '2025-09-15', '2025-10-24', 'Aprobado'),
(42, 42, 5, '2025-09-14', '2025-09-15', '2025-10-24', 'Aprobado'),
(43, 43, 5, '2025-09-15', '2025-09-15', '2025-10-24', 'Aprobado'),
(44, 1, 6, '2025-10-01', '2025-10-06', '2025-11-14', 'Aprobado'),
(45, 2, 6, '2025-10-01', '2025-10-06', '2025-11-14', 'Aprobado'),
(46, 3, 6, '2025-10-02', '2025-10-06', '2025-11-14', 'Aprobado'),
(47, 4, 6, '2025-10-02', '2025-10-06', '2025-11-14', 'Aprobado'),
(48, 5, 6, '2025-10-03', '2025-10-06', '2025-11-14', 'Aprobado'),
(49, 11, 6, '2025-10-03', '2025-10-06', '2025-11-14', 'Aprobado'),
(50, 12, 6, '2025-10-04', '2025-10-06', '2025-11-14', 'Aprobado'),
(51, 13, 6, '2025-10-04', '2025-10-06', '2025-11-14', 'Aprobado'),
(52, 14, 6, '2025-10-05', '2025-10-06', '2025-11-14', 'Aprobado'),
(53, 15, 6, '2025-10-05', '2025-10-06', '2025-11-14', 'Aprobado'),
(54, 16, 7, '2025-10-05', '2025-10-13', '2025-11-21', 'Aprobado'),
(55, 17, 7, '2025-10-06', '2025-10-13', '2025-11-21', 'Aprobado'),
(56, 18, 7, '2025-10-08', '2025-10-13', '2025-11-21', 'Aprobado'),
(57, 19, 7, '2025-10-08', '2025-10-13', '2025-11-21', 'Aprobado'),
(58, 20, 7, '2025-10-10', '2025-10-13', '2025-11-21', 'Aprobado'),
(59, 21, 7, '2025-10-10', '2025-10-13', '2025-11-21', 'Aprobado'),
(60, 22, 7, '2025-10-11', '2025-10-13', '2025-11-21', 'Aprobado'),
(61, 23, 7, '2025-10-11', '2025-10-13', '2025-11-21', 'Aprobado'),
(62, 24, 7, '2025-10-12', '2025-10-13', '2025-11-21', 'Aprobado'),
(63, 25, 7, '2025-10-12', '2025-10-13', '2025-11-21', 'Aprobado'),
(64, 26, 7, '2025-10-13', '2025-10-13', '2025-11-21', 'Aprobado'),
(65, 27, 7, '2025-10-13', '2025-10-13', '2025-11-21', 'Aprobado'),
(66, 28, 8, '2025-10-12', '2025-10-20', '2025-12-12', 'Aprobado'),
(67, 29, 8, '2025-10-12', '2025-10-20', '2025-12-12', 'Aprobado'),
(68, 30, 8, '2025-10-14', '2025-10-20', '2025-12-12', 'Aprobado'),
(69, 31, 8, '2025-10-15', '2025-10-20', '2025-12-12', 'Aprobado'),
(70, 32, 8, '2025-10-15', '2025-10-20', '2025-12-12', 'Aprobado'),
(71, 33, 8, '2025-10-18', '2025-10-20', '2025-12-12', 'Aprobado'),
(72, 34, 8, '2025-10-18', '2025-10-20', '2025-12-12', 'Aprobado'),
(73, 35, 8, '2025-10-19', '2025-10-20', '2025-12-12', 'Aprobado'),
(74, 36, 8, '2025-10-19', '2025-10-20', '2025-12-12', 'Aprobado'),
(75, 37, 8, '2025-10-20', '2025-10-20', '2025-12-12', 'Aprobado'),
(76, 38, 8, '2025-10-20', '2025-10-20', '2025-12-12', 'Aprobado'),
(87, 11, 10, '2025-11-02', '2025-11-10', '2025-12-19', 'Aprobado'),
(88, 12, 10, '2025-11-02', '2025-11-10', '2025-12-19', 'Aprobado'),
(89, 13, 10, '2025-11-04', '2025-11-10', '2025-12-19', 'Aprobado'),
(90, 14, 10, '2025-11-05', '2025-11-10', '2025-12-19', 'Aprobado'),
(91, 15, 10, '2025-11-05', '2025-11-10', '2025-12-19', 'Aprobado'),
(92, 22, 10, '2025-11-06', '2025-11-10', '2025-12-19', 'Aprobado'),
(93, 23, 10, '2025-11-08', '2025-11-10', '2025-12-19', 'Aprobado'),
(94, 24, 10, '2025-11-08', '2025-11-10', '2025-12-19', 'Aprobado'),
(95, 25, 10, '2025-11-10', '2025-11-10', '2025-12-19', 'Aprobado'),
(96, 26, 10, '2025-11-10', '2025-11-10', '2025-12-19', 'Aprobado'),
(97, 27, 11, '2025-11-09', '2025-11-17', '2025-12-26', 'Aprobado'),
(98, 28, 11, '2025-11-10', '2025-11-17', '2025-12-26', 'Aprobado'),
(99, 29, 11, '2025-11-12', '2025-11-17', '2025-12-26', 'Aprobado'),
(100, 30, 11, '2025-11-12', '2025-11-17', '2025-12-26', 'Aprobado'),
(101, 31, 11, '2025-11-14', '2025-11-17', '2025-12-26', 'Aprobado'),
(102, 32, 11, '2025-11-15', '2025-11-17', '2025-12-26', 'Aprobado'),
(103, 33, 11, '2025-11-15', '2025-11-17', '2025-12-26', 'Aprobado'),
(104, 34, 11, '2025-11-16', '2025-11-17', '2025-12-26', 'Aprobado'),
(105, 35, 11, '2025-11-16', '2025-11-17', '2025-12-26', 'Aprobado'),
(106, 36, 11, '2025-11-17', '2025-11-17', '2025-12-26', 'Aprobado'),
(107, 37, 11, '2025-11-17', '2025-11-17', '2025-12-26', 'Aprobado'),
(108, 38, 12, '2025-11-20', '2025-12-01', '2026-01-09', 'Aprobado'),
(109, 39, 12, '2025-11-20', '2025-12-01', '2026-01-09', 'Aprobado'),
(110, 40, 12, '2025-11-22', '2025-12-01', '2026-01-09', 'Aprobado'),
(111, 41, 12, '2025-11-25', '2025-12-01', '2026-01-09', 'Aprobado'),
(112, 42, 12, '2025-11-26', '2025-12-01', '2026-01-09', 'Aprobado'),
(113, 43, 12, '2025-11-28', '2025-12-01', '2026-01-09', 'Aprobado'),
(114, 44, 12, '2025-11-30', '2025-12-01', '2026-01-09', 'Aprobado'),
(115, 45, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),
(116, 46, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),
(117, 47, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),
(118, 14, 13, '2025-11-30', '2025-12-08', '2026-01-16', 'Aprobado'),
(119, 15, 13, '2025-12-01', '2025-12-08', '2026-01-16', 'Aprobado'),
(120, 16, 13, '2025-12-02', '2025-12-08', '2026-01-16', 'Aprobado'),
(121, 17, 13, '2025-12-04', '2025-12-08', '2026-01-16', 'Aprobado'),
(122, 18, 13, '2025-12-05', '2025-12-08', '2026-01-16', 'Aprobado'),
(123, 19, 13, '2025-12-05', '2025-12-08', '2026-01-16', 'Aprobado'),
(124, 20, 13, '2025-12-06', '2025-12-08', '2026-01-16', 'Aprobado'),
(125, 31, 13, '2025-12-06', '2025-12-08', '2026-01-16', 'Aprobado'),
(126, 32, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado'),
(127, 33, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado'),
(128, 34, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado'),
(129, 1, 14, '2026-01-02', '2026-01-05', '2026-02-13', 'Aprobado'),
(130, 2, 14, '2026-01-02', '2026-01-05', '2026-02-13', 'Aprobado'),
(131, 3, 14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(132, 4, 14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(133, 5, 14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(134, 6, 14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(135, 7, 14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(136, 8, 14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(137, 9, 14, '2026-01-05', '2026-01-05', '2026-02-13', 'Aprobado'),
(138, 10, 14, '2026-01-05', '2026-01-05', '2026-02-13', 'Aprobado'),
(139, 11, 15, '2026-01-05', '2026-01-12', '2026-02-20', 'Aprobado'),
(140, 12, 15, '2026-01-05', '2026-01-12', '2026-02-20', 'Aprobado'),
(141, 22, 15, '2026-01-06', '2026-01-12', '2026-02-20', 'Aprobado'),
(142, 23, 15, '2026-01-06', '2026-01-12', '2026-02-20', 'Aprobado'),
(143, 24, 15, '2026-01-08', '2026-01-12', '2026-02-20', 'Aprobado'),
(144, 25, 15, '2026-01-08', '2026-01-12', '2026-02-20', 'Aprobado'),
(145, 26, 15, '2026-01-10', '2026-01-12', '2026-02-20', 'Aprobado'),
(146, 27, 15, '2026-01-10', '2026-01-12', '2026-02-20', 'Aprobado'),
(147, 48, 15, '2026-01-12', '2026-01-12', '2026-02-20', 'Aprobado'),
(148, 49, 15, '2026-01-12', '2026-01-12', '2026-02-20', 'Aprobado'),
(149, 35, 16, '2026-01-12', '2026-01-19', '2026-03-13', 'Aprobado'),
(150, 36, 16, '2026-01-12', '2026-01-19', '2026-03-13', 'Aprobado'),
(151, 37, 16, '2026-01-14', '2026-01-19', '2026-03-13', 'Aprobado'),
(152, 38, 16, '2026-01-14', '2026-01-19', '2026-03-13', 'Aprobado'),
(153, 39, 16, '2026-01-15', '2026-01-19', '2026-03-13', 'Aprobado'),
(154, 43, 16, '2026-01-16', '2026-01-19', '2026-03-13', 'Aprobado'),
(155, 44, 16, '2026-01-16', '2026-01-19', '2026-03-13', 'Aprobado'),
(156, 45, 16, '2026-01-18', '2026-01-19', '2026-03-13', 'Aprobado'),
(157, 46, 16, '2026-01-18', '2026-01-19', '2026-03-13', 'Aprobado'),
(158, 47, 16, '2026-01-19', '2026-01-19', '2026-03-13', 'Aprobado'),
(159, 50, 16, '2026-01-19', '2026-01-19', '2026-03-13', 'Aprobado'),
(160, 1, 17, '2026-01-28', '2026-02-02', '2026-03-13', 'Aprobado'),
(161, 2, 17, '2026-01-28', '2026-02-02', '2026-03-13', 'Aprobado'),
(162, 3, 17, '2026-01-30', '2026-02-02', '2026-03-13', 'Aprobado'),
(163, 4, 17, '2026-01-30', '2026-02-02', '2026-03-13', 'Aprobado'),
(164, 5, 17, '2026-02-01', '2026-02-02', '2026-03-13', 'Aprobado'),
(165, 6, 17, '2026-02-01', '2026-02-02', '2026-03-13', 'Aprobado'),
(166, 7, 17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(167, 8, 17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(168, 9, 17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(169, 10, 17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(170, 51, 18, '2026-02-02', '2026-02-09', '2026-03-20', 'Aprobado'),
(171, 52, 18, '2026-02-02', '2026-02-09', '2026-03-20', 'Aprobado'),
(172, 53, 18, '2026-02-04', '2026-02-09', '2026-03-20', 'Aprobado'),
(173, 54, 18, '2026-02-05', '2026-02-09', '2026-03-20', 'Aprobado'),
(174, 55, 18, '2026-02-06', '2026-02-09', '2026-03-20', 'Aprobado'),
(175, 56, 18, '2026-02-06', '2026-02-09', '2026-03-20', 'Aprobado'),
(176, 57, 18, '2026-02-08', '2026-02-09', '2026-03-20', 'Aprobado'),
(177, 58, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(178, 11, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(179, 12, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(180, 13, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(181, 14, 19, '2026-02-10', '2026-02-16', '2026-04-10', 'Aprobado'),
(182, 15, 19, '2026-02-10', '2026-02-16', '2026-04-10', 'Aprobado'),
(183, 16, 19, '2026-02-12', '2026-02-16', '2026-04-10', 'Aprobado'),
(184, 17, 19, '2026-02-12', '2026-02-16', '2026-04-10', 'Aprobado'),
(185, 18, 19, '2026-02-14', '2026-02-16', '2026-04-10', 'Aprobado'),
(186, 19, 19, '2026-02-14', '2026-02-16', '2026-04-10', 'Aprobado'),
(187, 20, 19, '2026-02-15', '2026-02-16', '2026-04-10', 'Aprobado'),
(188, 21, 19, '2026-02-15', '2026-02-16', '2026-04-10', 'Aprobado'),
(189, 22, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(190, 23, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(191, 24, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(192, 25, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(193, 9, 20, '2026-02-18', '2026-02-23', '2026-04-17', 'Asignado'),
(194, 26, 20, '2026-02-18', '2026-02-23', '2026-04-17', 'Asignado'),
(195, 32, 20, '2026-02-20', '2026-02-23', '2026-04-17', 'Asignado'),
(196, 44, 20, '2026-02-20', '2026-02-23', '2026-04-17', 'Asignado'),
(197, 45, 20, '2026-02-22', '2026-02-23', '2026-04-17', 'Asignado'),
(198, 40, 20, '2026-02-22', '2026-02-23', '2026-04-17', 'Asignado'),
(199, 27, 21, '2026-02-25', '2026-03-02', '2026-04-10', 'Aprobado'),
(200, 28, 21, '2026-02-25', '2026-03-02', '2026-04-10', 'Aprobado'),
(201, 29, 21, '2026-02-26', '2026-03-02', '2026-04-10', 'Aprobado'),
(202, 30, 21, '2026-02-28', '2026-03-02', '2026-04-10', 'Aprobado'),
(203, 59, 21, '2026-03-01', '2026-03-02', '2026-04-10', 'Aprobado'),
(204, 60, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(205, 61, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(206, 62, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(207, 63, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(208, 64, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(209, 65, 22, '2026-03-04', '2026-03-09', '2026-04-17', 'Aprobado'),
(210, 66, 22, '2026-03-05', '2026-03-09', '2026-04-17', 'Aprobado'),
(211, 67, 22, '2026-03-06', '2026-03-09', '2026-04-17', 'Aprobado'),
(212, 68, 22, '2026-03-08', '2026-03-09', '2026-04-17', 'Aprobado'),
(213, 69, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(214, 31, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(215, 32, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(216, 33, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(217, 34, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(218, 35, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(219, 36, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(220, 37, 23, '2026-03-10', '2026-03-16', '2026-05-08', 'Aprobado'),
(221, 38, 23, '2026-03-11', '2026-03-16', '2026-05-08', 'Aprobado'),
(222, 39, 23, '2026-03-12', '2026-03-16', '2026-05-08', 'Aprobado'),
(223, 40, 23, '2026-03-12', '2026-03-16', '2026-05-08', 'Aprobado'),
(224, 41, 23, '2026-03-14', '2026-03-16', '2026-05-08', 'Aprobado'),
(225, 42, 23, '2026-03-14', '2026-03-16', '2026-05-08', 'Aprobado'),
(226, 46, 23, '2026-03-15', '2026-03-16', '2026-05-08', 'Aprobado'),
(227, 47, 23, '2026-03-15', '2026-03-16', '2026-05-08', 'Aprobado'),
(228, 48, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(229, 49, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(230, 50, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(231, 1, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(232, 2, 24, '2026-03-18', '2026-03-23', '2026-05-01', 'Asignado'),
(233, 3, 24, '2026-03-18', '2026-03-23', '2026-05-01', 'Asignado'),
(234, 4, 24, '2026-03-20', '2026-03-23', '2026-05-01', 'Asignado'),
(235, 5, 24, '2026-03-20', '2026-03-23', '2026-05-01', 'Asignado'),
(236, 6, 24, '2026-03-22', '2026-03-23', '2026-05-01', 'Asignado'),
(237, 7, 24, '2026-03-22', '2026-03-23', '2026-05-01', 'Asignado'),
(238, 8, 24, '2026-03-23', '2026-03-23', '2026-05-01', 'Asignado'),
(239, 10, 25, '2026-04-02', '2026-04-06', '2026-05-29', ''),
(240, 11, 25, '2026-04-02', '2026-04-06', '2026-05-29', ''),
(241, 16, 25, '2026-04-03', '2026-04-06', '2026-05-29', ''),
(242, 21, 25, '2026-04-03', '2026-04-06', '2026-05-29', ''),
(243, 22, 25, '2026-04-04', '2026-04-06', '2026-05-29', ''),
(244, 28, 25, '2026-04-04', '2026-04-06', '2026-05-29', ''),
(245, 29, 25, '2026-04-05', '2026-04-06', '2026-05-29', ''),
(246, 30, 25, '2026-04-05', '2026-04-06', '2026-05-29', ''),
(247, 70, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(248, 71, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(249, 72, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(250, 73, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(251, 74, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(252, 75, 25, '2026-04-06', '2026-04-06', '2026-05-29', ''),
(253, 1, 26, '2026-04-08', '2026-04-13', '2026-06-05', ''),
(254, 2, 26, '2026-04-08', '2026-04-13', '2026-06-05', ''),
(255, 4, 26, '2026-04-10', '2026-04-13', '2026-06-05', ''),
(256, 36, 26, '2026-04-10', '2026-04-13', '2026-06-05', ''),
(257, 37, 26, '2026-04-11', '2026-04-13', '2026-06-05', ''),
(258, 46, 26, '2026-04-11', '2026-04-13', '2026-06-05', ''),
(259, 47, 26, '2026-04-12', '2026-04-13', '2026-06-05', ''),
(260, 48, 26, '2026-04-12', '2026-04-13', '2026-06-05', ''),
(261, 49, 26, '2026-04-13', '2026-04-13', '2026-06-05', ''),
(262, 55, 26, '2026-04-13', '2026-04-13', '2026-06-05', ''),
(263, 56, 26, '2026-04-13', '2026-04-13', '2026-06-05', ''),
(264, 57, 26, '2026-04-13', '2026-04-13', '2026-06-05', ''),
(265, 31, 27, '2026-04-15', '2026-04-20', '2026-06-12', ''),
(266, 70, 27, '2026-04-15', '2026-04-20', '2026-06-12', ''),
(267, 71, 27, '2026-04-16', '2026-04-20', '2026-06-12', ''),
(268, 72, 27, '2026-04-16', '2026-04-20', '2026-06-12', ''),
(269, 73, 27, '2026-04-18', '2026-04-20', '2026-06-12', ''),
(270, 74, 27, '2026-04-18', '2026-04-20', '2026-06-12', ''),
(271, 75, 27, '2026-04-19', '2026-04-20', '2026-06-12', ''),
(272, 10, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(273, 11, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(274, 16, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(275, 21, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(276, 22, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(277, 28, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(278, 29, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(279, 30, 27, '2026-04-20', '2026-04-20', '2026-06-12', ''),
(280, 1, 28, '2026-04-28', '2026-05-04', '2026-06-26', ''),
(281, 2, 28, '2026-04-30', '2026-05-04', '2026-06-26', ''),
(282, 4, 28, '2026-05-01', '2026-05-04', '2026-06-26', ''),
(283, 10, 28, '2026-05-02', '2026-05-04', '2026-06-26', ''),
(284, 11, 28, '2026-05-02', '2026-05-04', '2026-06-26', ''),
(285, 16, 28, '2026-05-03', '2026-05-04', '2026-06-26', ''),
(286, 21, 28, '2026-05-04', '2026-05-04', '2026-06-26', ''),
(287, 22, 28, '2026-05-04', '2026-05-04', '2026-06-26', ''),
(288, 28, 28, '2026-05-04', '2026-05-04', '2026-06-26', ''),
(289, 29, 28, '2026-05-04', '2026-05-04', '2026-06-26', ''),
(290, 30, 29, '2026-05-02', '2026-05-11', '2026-07-03', ''),
(291, 31, 29, '2026-05-05', '2026-05-11', '2026-07-03', ''),
(292, 36, 29, '2026-05-08', '2026-05-11', '2026-07-03', ''),
(293, 37, 29, '2026-05-10', '2026-05-11', '2026-07-03', ''),
(294, 46, 29, '2026-05-10', '2026-05-11', '2026-07-03', ''),
(295, 47, 29, '2026-05-11', '2026-05-11', '2026-07-03', ''),
(296, 48, 29, '2026-05-11', '2026-05-11', '2026-07-03', ''),
(297, 49, 29, '2026-05-11', '2026-05-11', '2026-07-03', ''),
(298, 55, 30, '2026-05-10', '2026-05-18', '2026-07-10', ''),
(299, 56, 30, '2026-05-12', '2026-05-18', '2026-07-10', ''),
(300, 57, 30, '2026-05-14', '2026-05-18', '2026-07-10', ''),
(301, 70, 30, '2026-05-15', '2026-05-18', '2026-07-10', ''),
(302, 71, 30, '2026-05-16', '2026-05-18', '2026-07-10', ''),
(303, 72, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(304, 73, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(305, 74, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(306, 75, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(307, 10, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(308, 11, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(309, 16, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(310, 21, 30, '2026-05-18', '2026-05-18', '2026-07-10', ''),
(311, 5, 31, '2026-05-15', '2026-06-01', '2026-07-15', 'Diferido'),
(312, 18, 31, '2026-05-15', '2026-06-01', '2026-07-15', 'Diferido'),
(313, 25, 31, '2026-05-16', '2026-06-01', '2026-07-15', 'Diferido'),
(314, 38, 31, '2026-05-18', '2026-06-01', '2026-07-15', 'Diferido'),
(315, 42, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Diferido'),
(316, 58, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Diferido'),
(317, 3, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Diferido'),
(318, 6, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Diferido'),
(319, 13, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Asignado'),
(320, 14, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Asignado'),
(321, 17, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Asignado'),
(322, 19, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Asignado'),
(323, 24, 32, '2026-05-21', '2026-06-08', '2026-07-20', 'Asignado'),
(324, 26, 32, '2026-05-21', '2026-06-08', '2026-07-20', 'Asignado'),
(325, 33, 32, '2026-05-22', '2026-06-08', '2026-07-20', 'Asignado'),
(326, 34, 32, '2026-05-22', '2026-06-08', '2026-07-20', 'Asignado'),
(327, 35, 32, '2026-05-23', '2026-06-08', '2026-07-20', 'Asignado'),
(328, 39, 32, '2026-05-23', '2026-06-08', '2026-07-20', 'Asignado');

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
(1, 4121111101, 'Móvil', 1, NULL, NULL),
(2, 4121111102, 'Móvil', 2, NULL, NULL),
(3, 4121111103, 'Móvil', 3, NULL, NULL),
(4, 4121111104, 'Móvil', 4, NULL, NULL),
(5, 4121111105, 'Móvil', 5, NULL, NULL),
(6, 4121111106, 'Móvil', 6, NULL, NULL),
(7, 4121111107, 'Móvil', 7, NULL, NULL),
(8, 4121111108, 'Móvil', 8, NULL, NULL),
(9, 4121111109, 'Móvil', 9, NULL, NULL),
(10, 4121111110, 'Móvil', 10, NULL, NULL),
(11, 4121111111, 'Móvil', 11, NULL, NULL),
(12, 4121111112, 'Móvil', 12, NULL, NULL),
(13, 4121111113, 'Móvil', 13, NULL, NULL),
(14, 4121111114, 'Móvil', 14, NULL, NULL),
(15, 4121111115, 'Móvil', 15, NULL, NULL),
(16, 4242000001, 'Móvil', NULL, 1, NULL),
(17, 4242000002, 'Móvil', NULL, 2, NULL),
(18, 4242000003, 'Móvil', NULL, 3, NULL),
(19, 4242000004, 'Móvil', NULL, 4, NULL),
(20, 4242000005, 'Móvil', NULL, 5, NULL),
(21, 4242000006, 'Móvil', NULL, 6, NULL),
(22, 4242000007, 'Móvil', NULL, 7, NULL),
(23, 4242000008, 'Móvil', NULL, 8, NULL),
(24, 4242000009, 'Móvil', NULL, 9, NULL),
(25, 4242000010, 'Móvil', NULL, 10, NULL),
(26, 4242000011, 'Móvil', NULL, 11, NULL),
(27, 4242000012, 'Móvil', NULL, 12, NULL),
(28, 4242000013, 'Móvil', NULL, 13, NULL),
(29, 4242000014, 'Móvil', NULL, 14, NULL),
(30, 4242000015, 'Móvil', NULL, 15, NULL),
(31, 4242000016, 'Móvil', NULL, 16, NULL),
(32, 4242000017, 'Móvil', NULL, 17, NULL),
(33, 4242000018, 'Móvil', NULL, 18, NULL),
(34, 4242000019, 'Móvil', NULL, 19, NULL),
(35, 4242000020, 'Móvil', NULL, 20, NULL),
(36, 4242000021, 'Móvil', NULL, 21, NULL),
(37, 4242000022, 'Móvil', NULL, 22, NULL),
(38, 4242000023, 'Móvil', NULL, 23, NULL),
(39, 4242000024, 'Móvil', NULL, 24, NULL),
(40, 4242000025, 'Móvil', NULL, 25, NULL),
(41, 4242000026, 'Móvil', NULL, 26, NULL),
(42, 4242000027, 'Móvil', NULL, 27, NULL),
(43, 4242000028, 'Móvil', NULL, 28, NULL),
(44, 4242000029, 'Móvil', NULL, 29, NULL),
(45, 4242000030, 'Móvil', NULL, 30, NULL),
(46, 4242000031, 'Móvil', NULL, 31, NULL),
(47, 4242000032, 'Móvil', NULL, 32, NULL),
(48, 4242000033, 'Móvil', NULL, 33, NULL),
(49, 4242000034, 'Móvil', NULL, 34, NULL),
(50, 4242000035, 'Móvil', NULL, 35, NULL),
(51, 4242000036, 'Móvil', NULL, 36, NULL),
(52, 4242000037, 'Móvil', NULL, 37, NULL),
(53, 4242000038, 'Móvil', NULL, 38, NULL),
(54, 4242000039, 'Móvil', NULL, 39, NULL),
(55, 4242000040, 'Móvil', NULL, 40, NULL),
(56, 4242000041, 'Móvil', NULL, 41, NULL),
(57, 4242000042, 'Móvil', NULL, 42, NULL),
(58, 4242000043, 'Móvil', NULL, 43, NULL),
(59, 4242000044, 'Móvil', NULL, 44, NULL),
(60, 4242000045, 'Móvil', NULL, 45, NULL),
(61, 4242000046, 'Móvil', NULL, 46, NULL),
(62, 4242000047, 'Móvil', NULL, 47, NULL),
(63, 4242000048, 'Móvil', NULL, 48, NULL),
(64, 4242000049, 'Móvil', NULL, 49, NULL),
(65, 4242000050, 'Móvil', NULL, 50, NULL),
(66, 4242000051, 'Móvil', NULL, 51, NULL),
(67, 4242000052, 'Móvil', NULL, 52, NULL),
(68, 4242000053, 'Móvil', NULL, 53, NULL),
(69, 4242000054, 'Móvil', NULL, 54, NULL),
(70, 4242000055, 'Móvil', NULL, 55, NULL),
(71, 4242000056, 'Móvil', NULL, 56, NULL),
(72, 4242000057, 'Móvil', NULL, 57, NULL),
(73, 4242000058, 'Móvil', NULL, 58, NULL),
(74, 4242000059, 'Móvil', NULL, 59, NULL),
(75, 4242000060, 'Móvil', NULL, 60, NULL),
(76, 4242000061, 'Móvil', NULL, 61, NULL),
(77, 4242000062, 'Móvil', NULL, 62, NULL),
(78, 4242000063, 'Móvil', NULL, 63, NULL),
(79, 4242000064, 'Móvil', NULL, 64, NULL),
(80, 4242000065, 'Móvil', NULL, 65, NULL),
(81, 4242000066, 'Móvil', NULL, 66, NULL),
(82, 4242000067, 'Móvil', NULL, 67, NULL),
(83, 4242000068, 'Móvil', NULL, 68, NULL),
(84, 4242000069, 'Móvil', NULL, 69, NULL),
(85, 4242000070, 'Móvil', NULL, 70, NULL),
(86, 4242000071, 'Móvil', NULL, 71, NULL),
(87, 4242000072, 'Móvil', NULL, 72, NULL),
(88, 4242000073, 'Móvil', NULL, 73, NULL),
(89, 4242000074, 'Móvil', NULL, 74, NULL),
(90, 4242000075, 'Móvil', NULL, 75, NULL),
(91, 4242000076, 'Móvil', NULL, 76, NULL),
(92, 4242000077, 'Móvil', NULL, 77, NULL),
(93, 4242000078, 'Móvil', NULL, 78, NULL),
(94, 4242000079, 'Móvil', NULL, 79, NULL),
(95, 4242000080, 'Móvil', NULL, 80, NULL),
(96, 4242000081, 'Móvil', NULL, 81, NULL),
(97, 4242000082, 'Móvil', NULL, 82, NULL),
(98, 4242000083, 'Móvil', NULL, 83, NULL),
(99, 4242000084, 'Móvil', NULL, 84, NULL),
(100, 4242000085, 'Móvil', NULL, 85, NULL),
(101, 4242000086, 'Móvil', NULL, 86, NULL),
(102, 4242000087, 'Móvil', NULL, 87, NULL),
(103, 4242000088, 'Móvil', NULL, 88, NULL),
(104, 4242000089, 'Móvil', NULL, 89, NULL),
(105, 4242000090, 'Móvil', NULL, 90, NULL);

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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  MODIFY `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tb_estatuscurso`
--
ALTER TABLE `tb_estatuscurso`
  MODIFY `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_historial`
--
ALTER TABLE `tb_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `tb_metas`
--
ALTER TABLE `tb_metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `tb_participante_curso`
--
ALTER TABLE `tb_participante_curso`
  MODIFY `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

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
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

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

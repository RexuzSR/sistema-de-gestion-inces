-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: inces1
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tb_ambito`
--

DROP TABLE IF EXISTS `tb_ambito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ambito` (
  `id_ambito` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ambito` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ambito`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_ambito`
--

LOCK TABLES `tb_ambito` WRITE;
/*!40000 ALTER TABLE `tb_ambito` DISABLE KEYS */;
INSERT INTO `tb_ambito` VALUES (1,'Centro de Formacion Socialista'),(2,'Comunas'),(3,'Instalaciones Militares'),(4,'Unidades educativas'),(5,'Sistema de Misiones y Grandes Misiones');
/*!40000 ALTER TABLE `tb_ambito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_areaformativa`
--

DROP TABLE IF EXISTS `tb_areaformativa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_areaformativa` (
  `id_areaFormativa` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_areaFormativa` varchar(50) NOT NULL,
  PRIMARY KEY (`id_areaFormativa`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_areaformativa`
--

LOCK TABLES `tb_areaformativa` WRITE;
/*!40000 ALTER TABLE `tb_areaformativa` DISABLE KEYS */;
INSERT INTO `tb_areaformativa` VALUES (1,'Alimentos y bebidas'),(2,'Confecciones'),(3,'Procesos industriales'),(4,'Forestal'),(5,'Seguridad pública'),(6,'Planificación y formulación de proyectos'),(7,'Instrumentación'),(8,'Alojamiento'),(9,'Tecnología electrónica'),(10,'Tecnología eléctrica');
/*!40000 ALTER TABLE `tb_areaformativa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_curso`
--

DROP TABLE IF EXISTS `tb_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_curso` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
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
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_curso`),
  UNIQUE KEY `uq_preimpreso` (`preimpreso`),
  KEY `id_modalidad` (`id_modalidad`),
  KEY `id_estatusCurso` (`id_estatusCurso`),
  KEY `id_areaFormativa` (`id_areaFormativa`),
  KEY `fk_curso_ambito` (`id_ambito`),
  KEY `fk_curso_subtipo` (`id_subtipo`),
  KEY `fk_curso_sectoreconomico` (`id_sectoreconomico`),
  CONSTRAINT `fk_curso_ambito` FOREIGN KEY (`id_ambito`) REFERENCES `tb_ambito` (`id_ambito`),
  CONSTRAINT `fk_curso_estatus` FOREIGN KEY (`id_estatusCurso`) REFERENCES `tb_estatuscurso` (`id_estatusCurso`) ON UPDATE CASCADE,
  CONSTRAINT `fk_curso_sectoreconomico` FOREIGN KEY (`id_sectoreconomico`) REFERENCES `tb_sectoreconomico` (`id_sectoreconomico`),
  CONSTRAINT `fk_curso_subtipo` FOREIGN KEY (`id_subtipo`) REFERENCES `tb_subtipo` (`id_subtipo`),
  CONSTRAINT `tb_curso_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `tb_modalidad` (`id_modalidad`),
  CONSTRAINT `tb_curso_ibfk_4` FOREIGN KEY (`id_areaFormativa`) REFERENCES `tb_areaformativa` (`id_areaFormativa`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_curso`
--

LOCK TABLES `tb_curso` WRITE;
/*!40000 ALTER TABLE `tb_curso` DISABLE KEYS */;
INSERT INTO `tb_curso` VALUES (1,'Curso Base No Utilizable',NULL,'2025-08-01',NULL,NULL,NULL,NULL,0.00,30,10,0,NULL,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,'Mañana',NULL,'2026-05-25 01:46:16','2026-05-25 01:47:50'),(2,'Panadería Básica I',NULL,'2026-06-22','2026-07-01','2026-07-10',60.00,4.00,0.00,20,12,0,1,'Unidad Curricular',1,1,1,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-08-01 13:00:00','2026-06-15 18:21:01'),(3,'Introducción a la Ofimática',NULL,'2025-08-11','2025-07-25','2025-09-19',40.00,4.00,40.00,25,10,12,2,'Unidad Curricular',5,1,3,6,2,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2025-08-05 14:30:00','2026-05-25 01:46:16'),(4,'Confección de Uniformes Escolares',NULL,'2025-09-08','2025-08-20','2025-10-17',80.00,4.00,80.00,20,10,10,1,'Unidad Curricular',5,2,1,2,3,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-09-01 13:00:00','2026-05-25 01:46:16'),(5,'Seguridad y Salud en el Trabajo',NULL,'2025-09-15','2025-08-25','2025-10-24',40.00,4.00,40.00,30,10,11,2,'Unidad Curricular',5,1,2,5,4,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2025-09-05 14:00:00','2026-05-25 01:46:16'),(6,'Matemática Aplicada',NULL,'2025-10-06','2025-09-20','2025-11-14',60.00,3.00,60.00,25,10,10,3,'Unidad Curricular',5,1,3,6,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-10-01 13:00:00','2026-05-25 01:46:16'),(7,'Ortografía y Redacción Eficaz',NULL,'2025-10-13','2025-09-25','2025-11-21',30.00,3.00,30.00,25,10,12,2,'Unidad Curricular',5,1,3,6,2,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2025-10-05 14:00:00','2026-05-25 01:46:16'),(8,'Ética y Valores',NULL,'2025-10-20','2025-10-05','2025-12-12',40.00,4.00,40.00,30,10,11,1,'Unidad Curricular',5,1,3,6,5,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-10-10 15:30:00','2026-05-25 01:46:16'),(9,'Panadería y Pastelería Navideña',NULL,'2026-06-08','2026-06-17','2026-06-26',60.00,4.00,0.00,20,12,0,1,'Unidad Curricular',2,1,1,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-11-01 13:00:00','2026-06-09 21:51:36'),(10,'Dibujo Técnico Básico',NULL,'2025-11-10','2025-10-25','2025-12-19',40.00,3.00,40.00,20,10,10,1,'Unidad Curricular',5,2,1,6,3,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2025-11-05 14:15:00','2026-05-25 01:46:16'),(11,'Lectura e Interpretación de Planos',NULL,'2025-11-17','2025-11-01','2025-12-26',60.00,4.00,60.00,25,10,11,3,'Unidad Curricular',5,2,1,6,4,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-11-12 18:00:00','2026-05-25 01:46:16'),(12,'Higiene y Manipulación de Alimentos I',NULL,'2025-12-01','2025-11-15','2026-01-09',30.00,3.00,30.00,30,10,10,1,'Unidad Curricular',5,1,3,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-12-01 13:00:00','2026-05-25 01:46:16'),(13,'Administración de Pequeñas Empresas',NULL,'2025-12-08','2025-11-20','2026-01-16',60.00,4.00,60.00,25,10,11,2,'Unidad Curricular',5,1,2,6,2,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2025-12-05 14:00:00','2026-05-25 01:46:16'),(14,'Cálculo de Costos y Presupuestos',NULL,'2026-01-05','2025-12-20','2026-02-13',60.00,4.00,60.00,25,10,10,3,'Unidad Curricular',5,1,2,6,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-01-02 13:00:00','2026-05-25 01:46:16'),(15,'Mecánica de Motores Básica',NULL,'2026-01-12','2025-12-28','2026-02-20',80.00,4.00,80.00,20,10,10,1,'Unidad Curricular',5,2,1,3,3,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-01-08 15:00:00','2026-05-25 01:46:16'),(16,'Habilidades Socioemocionales I',NULL,'2026-01-19','2026-01-05','2026-03-13',30.00,3.00,30.00,30,10,11,1,'Unidad Curricular',5,1,3,6,5,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-01-15 19:30:00','2026-05-25 01:46:16'),(17,'Panadería y Pastelería Básica II',NULL,'2026-02-02','2026-01-15','2026-03-13',80.00,4.00,80.00,20,10,10,1,'Unidad Curricular',5,1,1,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-02-01 13:00:00','2026-05-25 01:46:16'),(18,'Algoritmia y Programación Bísica',NULL,'2026-02-09','2026-01-25','2026-03-20',80.00,4.00,80.00,25,10,11,2,'Unidad Curricular',5,3,1,9,4,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-02-05 15:30:00','2026-05-25 01:46:16'),(19,'Confección de Prendas de Vestir',NULL,'2026-02-16','2026-02-01','2026-04-10',80.00,4.00,80.00,20,10,12,1,'Unidad Curricular',5,2,1,2,2,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-02-12 18:15:00','2026-05-25 01:46:16'),(20,'Dibujo Técnico Industrial',NULL,'2026-02-23','2026-02-10','2026-04-17',60.00,3.00,0.00,20,10,6,1,'Unidad Curricular',5,2,1,6,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-02-20 19:45:00','2026-05-25 01:47:50'),(21,'Mecánica de Motores a Gasolina II',NULL,'2026-03-02','2026-02-15','2026-04-10',100.00,5.00,100.00,20,10,10,1,'Unidad Curricular',5,2,1,3,1,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-03-01 13:00:00','2026-05-25 01:46:16'),(22,'Inglés con Propósitos Generales II',NULL,'2026-03-09','2026-02-25','2026-04-17',60.00,3.00,60.00,30,10,11,2,'Unidad Curricular',5,1,3,6,3,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-03-05 14:15:00','2026-05-25 01:46:16'),(23,'Higiene y Manipulación de Alimentos II',NULL,'2026-03-16','2026-03-01','2026-05-08',40.00,4.00,40.00,25,10,12,1,'Unidad Curricular',5,1,3,1,2,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-03-12 15:00:00','2026-05-25 01:46:16'),(24,'Recreación y Dinámicas Infantiles',NULL,'2026-03-23','2026-03-10','2026-05-01',40.00,4.00,20.00,20,10,7,3,'Unidad Curricular',5,1,3,8,4,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-03-20 18:00:00','2026-05-25 01:47:50'),(25,'Base de Datos Relacionales',NULL,'2026-04-06','2026-03-20','2026-05-29',80.00,4.00,60.00,25,10,14,2,'Unidad Curricular',5,3,1,9,1,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-04-02 13:30:00','2026-05-30 15:13:15'),(26,'Confección de Uniformes Médicos',NULL,'2026-04-13','2026-03-30','2026-06-05',80.00,4.00,48.00,20,10,12,1,'Unidad Curricular',5,2,1,2,2,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-04-08 14:00:00','2026-06-09 21:51:40'),(27,'Electricidad Básica Residencial II',NULL,'2026-04-20','2026-04-05','2026-06-12',90.00,4.00,36.00,20,10,15,1,'Unidad Curricular',5,2,1,10,3,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-04-15 16:00:00','2026-06-14 19:30:33'),(28,'Mercadeo y Ventas Digitales II',NULL,'2026-05-04','2026-04-20','2026-06-26',40.00,4.00,12.00,30,10,10,2,'Unidad Curricular',3,1,2,6,4,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-05-01 13:00:00','2026-05-25 01:46:16'),(29,'Diseño y Construcción de Redes II',NULL,'2026-05-11','2026-04-25','2026-07-03',80.00,4.00,8.00,20,10,8,3,'Unidad Curricular',2,3,1,9,5,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-05-05 15:30:00','2026-05-25 01:47:55'),(30,'Higiene y Manipulación de Alimentos III',NULL,'2026-05-18','2026-05-01','2026-07-10',30.00,3.00,3.00,30,10,13,1,'Unidad Curricular',3,1,3,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-05-12 18:00:00','2026-05-25 01:46:16'),(31,'Panadería y Pastelería Avanzada II',NULL,'2026-06-01','2026-05-15','2026-07-15',100.00,4.00,0.00,20,10,8,1,'Unidad Curricular',2,1,1,1,1,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2026-05-18 19:30:00','2026-05-25 01:47:55'),(32,'Algoritmia y Programación Avanzada',NULL,'2026-06-08','2026-05-20','2026-07-20',80.00,4.00,0.00,25,10,10,2,'Unidad Curricular',3,3,1,9,2,NULL,NULL,'Tarde','Programa de Formación Productiva (PFP)','2026-05-22 20:45:00','2026-05-25 01:47:55'),(33,'Curso Descontinuado de Prueba',NULL,'2026-06-25','2026-07-04','2026-07-08',40.00,4.00,0.00,20,12,0,1,'Unidad Curricular',1,1,3,5,3,NULL,NULL,'Mañana','Programa de Formación Productiva (PFP)','2025-09-25 14:00:00','2026-06-19 01:20:51'),(34,'PENSAMIENTO CREATIVO Y PRODUCTIVO',NULL,'2026-06-29','2026-07-08','2026-07-29',90.00,4.00,0.00,40,12,23,1,'Unidad Curricular',1,2,2,NULL,2,2,102,'Tarde','Programa de Formación Productiva (PFP)','2026-06-19 07:20:12','2026-06-19 01:20:29');
/*!40000 ALTER TABLE `tb_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_discapacidad`
--

DROP TABLE IF EXISTS `tb_discapacidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_discapacidad` (
  `id_discapacidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_discapacidad` varchar(100) NOT NULL,
  PRIMARY KEY (`id_discapacidad`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_discapacidad`
--

LOCK TABLES `tb_discapacidad` WRITE;
/*!40000 ALTER TABLE `tb_discapacidad` DISABLE KEYS */;
INSERT INTO `tb_discapacidad` VALUES (1,'Ninguna'),(2,'Visual'),(3,'Auditiva'),(4,'Motora');
/*!40000 ALTER TABLE `tb_discapacidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_docente`
--

DROP TABLE IF EXISTS `tb_docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_docente` (
  `id_docente` int(11) NOT NULL AUTO_INCREMENT,
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
  `tipo_docente` enum('Fijo','Contratado','PDL','Voluntario') NOT NULL,
  PRIMARY KEY (`id_docente`),
  UNIQUE KEY `cedula` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_docente`
--

LOCK TABLES `tb_docente` WRITE;
/*!40000 ALTER TABLE `tb_docente` DISABLE KEYS */;
INSERT INTO `tb_docente` VALUES (1,'Carlos','Eduardo','Rodriguez','Mendoza',12345601,'carlos.rodriguez@inces.edu.ve','1980-01-15',46,'En formación','Masculino','Fijo'),(2,'Maria','Elena','Gonzalez','Flores',12345602,'maria.gonzalez@inces.edu.ve','1985-02-20',41,'Asignado','Femenino','Fijo'),(3,'Luis','Alberto','Hernandez','Rivas',12345603,'luis.hernandez@inces.edu.ve','1978-03-25',48,'Asignado','Masculino','Contratado'),(4,'Ana','Gabriela','Lopez','Acosta',12345604,'ana.lopez@inces.edu.ve','1990-04-30',36,'Asignado','Femenino','Contratado'),(5,'Jose','Manuel','Martinez','Torres',12345605,'jose.martinez@inces.edu.ve','1983-05-12',43,'Asignado','Masculino','PDL'),(6,'Sofia','Valentina','Perez','Mendoza',12345606,'sofia.perez@inces.edu.ve','1995-06-18',31,'Asignado','Femenino','Voluntario'),(7,'Juan','Carlos','Gomez','Castillo',12345607,'juan.gomez@inces.edu.ve','1975-07-22',50,'Disponible','Masculino','Fijo'),(8,'Laura','Beatriz','Sanchez','Rojas',12345608,'laura.sanchez@inces.edu.ve','1988-08-05',37,'Disponible','Femenino','Fijo'),(9,'Pedro','José','Alvarez','Guzman',12345609,'pedro.alvarez@inces.edu.ve','1982-09-14',43,'Asignado','Masculino','Contratado'),(10,'Carmen','Julia','Diaz','Salazar',12345610,'carmen.diaz@inces.edu.ve','1987-10-09',38,'Asignado','Femenino','PDL'),(11,'Francisco','Javier','Vasquez','Delgado',12345611,'francisco.vasquez@inces.edu.ve','1970-11-03',55,'En formación','Masculino','Fijo'),(12,'Patricia','Alejandra','Castro','Vargas',12345612,'patricia.castro@inces.edu.ve','1992-12-28',33,'Asignado','Femenino','Voluntario'),(13,'Miguel','Angel','Torres','Garcia',12345613,'miguel.torres@inces.edu.ve','1981-04-14',45,'En formación','Masculino','Contratado'),(14,'Yolanda','Beatriz','Perez','Blanco',12345614,'yolanda.perez@inces.edu.ve','1989-08-22',36,'En formación','Femenino','Voluntario'),(15,'Héctor','Jose','Guzman','Herrera',12345615,'hector.guzman@inces.edu.ve','1972-10-10',53,'Inactivo','Masculino','Fijo');
/*!40000 ALTER TABLE `tb_docente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_docente_curso`
--

DROP TABLE IF EXISTS `tb_docente_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_docente_curso` (
  `id_docente_curso` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha_asignacion` date DEFAULT curdate(),
  PRIMARY KEY (`id_docente_curso`),
  KEY `fk_docente_curso_docente` (`id_docente`),
  KEY `fk_docente_curso_curso` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_docente_curso`
--

LOCK TABLES `tb_docente_curso` WRITE;
/*!40000 ALTER TABLE `tb_docente_curso` DISABLE KEYS */;
INSERT INTO `tb_docente_curso` VALUES (1,1,2,'2025-08-01'),(2,2,3,'2025-08-05'),(3,3,4,'2025-09-01'),(4,4,5,'2025-09-05'),(5,5,6,'2025-10-01'),(6,6,7,'2025-10-05'),(7,9,8,'2025-10-10'),(8,10,9,'2025-11-01'),(9,11,10,'2025-11-05'),(10,12,11,'2025-11-12'),(11,1,12,'2025-12-01'),(12,2,13,'2025-12-05'),(13,3,14,'2026-01-02'),(14,4,15,'2026-01-08'),(15,5,16,'2026-01-15'),(16,6,17,'2026-02-01'),(17,9,18,'2026-02-05'),(18,10,19,'2026-02-12'),(19,11,20,'2026-02-20'),(20,12,21,'2026-03-01'),(21,1,22,'2026-03-05'),(22,2,23,'2026-03-12'),(23,3,24,'2026-03-20'),(24,4,25,'2026-04-02'),(25,5,26,'2026-04-08'),(26,6,27,'2026-04-15'),(27,9,28,'2026-05-01'),(28,10,29,'2026-05-05'),(29,11,30,'2026-05-12'),(30,12,31,'2026-05-18'),(31,1,32,'2026-05-22'),(32,2,33,'2025-09-25');
/*!40000 ALTER TABLE `tb_docente_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_estatuscurso`
--

DROP TABLE IF EXISTS `tb_estatuscurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_estatuscurso` (
  `id_estatusCurso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_estatusCurso` varchar(50) NOT NULL,
  PRIMARY KEY (`id_estatusCurso`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_estatuscurso`
--

LOCK TABLES `tb_estatuscurso` WRITE;
/*!40000 ALTER TABLE `tb_estatuscurso` DISABLE KEYS */;
INSERT INTO `tb_estatuscurso` VALUES (1,'En espera'),(2,'Diferido'),(3,'En proceso'),(4,'En pausa'),(5,'Culminado'),(6,'Inactivo');
/*!40000 ALTER TABLE `tb_estatuscurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_historial`
--

DROP TABLE IF EXISTS `tb_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_historial` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_actividad` varchar(50) NOT NULL COMMENT 'Crear, Editar, Eliminar, Login, Ver, etc.',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción detallada de la actividad',
  `tabla_afectada` varchar(100) DEFAULT NULL COMMENT 'Nombre de la tabla afectada',
  `id_registro` int(11) DEFAULT NULL COMMENT 'ID del registro afectado',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario que realizó la acción',
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_historial`),
  KEY `idx_tipo_actividad` (`tipo_actividad`),
  KEY `idx_fecha_hora` (`fecha_hora`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_tabla_registro` (`tabla_afectada`,`id_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_historial`
--

LOCK TABLES `tb_historial` WRITE;
/*!40000 ALTER TABLE `tb_historial` DISABLE KEYS */;
INSERT INTO `tb_historial` VALUES (1,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2025-08-01 12:00:00'),(2,'Crear','Se creó el curso: Panadería Básica I','tb_curso',2,11,'2025-08-01 13:30:00'),(3,'Editar','Se asignó el docente Carlos Rodriguez al curso Panadería Básica I','tb_docente_curso',1,11,'2025-08-01 13:45:00'),(4,'Crear','Se registró el participante: Diego Romero','tb_participante',1,11,'2025-08-05 15:20:00'),(5,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2025-09-02 12:15:00'),(6,'Crear','Se registró el participante: Sofia Morillo','tb_participante',7,16,'2025-09-02 15:00:00'),(7,'Editar','Se completó y culminó el curso: Panadería Básica I','tb_curso',2,16,'2025-09-12 16:00:00'),(8,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2025-10-02 12:00:00'),(9,'Crear','Se registró la meta: Inscritos CFS T-IV','tb_metas',2,11,'2025-10-02 13:00:00'),(10,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2025-11-02 12:15:00'),(11,'Crear','Se creó el curso: Panadería y Pastelería Navideña','tb_curso',9,16,'2025-11-01 14:00:00'),(12,'Editar','Se completó y culminó el curso: Confección de Uniformes Escolares','tb_curso',4,16,'2025-10-17 16:00:00'),(13,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2025-12-02 12:00:00'),(14,'Crear','Se registró el participante: Florencia Fuentes','tb_participante',31,11,'2025-12-02 14:30:00'),(15,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-01-05 12:15:00'),(16,'Crear','Se creó el curso: Cálculo de Costos y Presupuestos','tb_curso',14,16,'2026-01-02 13:30:00'),(17,'Editar','Se completó y culminó el curso: Higiene y Manipulación de Alimentos I','tb_curso',12,16,'2026-01-09 16:00:00'),(18,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2026-02-02 12:00:00'),(19,'Crear','Se creó el curso: Dibujo Técnico Industrial','tb_curso',20,11,'2026-02-20 15:00:00'),(20,'Editar','Se completó y culminó el curso: Cálculo de Costos y Presupuestos','tb_curso',14,11,'2026-02-13 16:00:00'),(21,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-03-02 12:15:00'),(22,'Crear','Se creó el curso: Recreación y Dinámicas Infantiles','tb_curso',24,16,'2026-03-20 14:00:00'),(23,'Editar','Se completó y culminó el curso: Habilidades Socioemocionales I','tb_curso',16,16,'2026-03-13 16:00:00'),(24,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2026-04-02 12:00:00'),(25,'Crear','Se creó el curso: Base de Datos Relacionales','tb_curso',25,11,'2026-04-02 13:30:00'),(26,'Editar','Se completó y culminó el curso: Confección de Prendas de Vestir','tb_curso',19,11,'2026-04-10 16:00:00'),(27,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-02 12:15:00'),(28,'Crear','Se registró la meta: Comunas Abr-Jul','tb_metas',6,16,'2026-05-02 13:00:00'),(29,'Crear','Se creó el curso: Mercadeo y Ventas Digitales II','tb_curso',28,16,'2026-05-01 14:30:00'),(30,'Editar','Se completó y culminó el curso: Higiene y Manipulación de Alimentos II','tb_curso',23,16,'2026-05-08 16:00:00'),(31,'Login','Usuario ADMIN inició sesión','tb_usuario',11,11,'2026-05-25 01:15:00'),(32,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-25 01:47:50'),(33,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-25 02:19:36'),(34,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-25 03:08:27'),(35,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-27 00:53:53'),(36,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-27 01:28:27'),(37,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-27 02:32:42'),(38,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-30 15:13:11'),(39,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-30 16:54:27'),(40,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-31 14:19:54'),(41,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-05-31 14:34:29'),(42,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-09 21:51:36'),(43,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-09 21:54:44'),(44,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-09 22:39:06'),(45,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 03:23:40'),(46,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 03:47:39'),(47,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 03:55:28'),(48,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 18:44:56'),(49,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 19:45:46'),(50,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 20:49:48'),(51,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 21:19:16'),(52,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 22:08:36'),(53,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 22:48:48'),(54,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-10 23:52:58'),(55,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-11 10:12:49'),(56,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-11 10:59:50'),(57,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-12 00:29:25'),(58,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-12 22:47:11'),(59,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-12 23:34:05'),(60,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-14 19:30:30'),(61,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-14 19:52:21'),(62,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 14:11:18'),(63,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 15:18:09'),(64,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 17:21:17'),(65,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 20:46:57'),(66,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 21:38:07'),(67,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-15 21:59:41'),(68,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 02:05:58'),(69,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 02:53:44'),(70,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 03:20:23'),(71,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 03:50:20'),(72,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 04:33:19'),(73,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 04:53:16'),(74,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 05:16:28'),(75,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 11:10:37'),(76,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-16 11:32:05'),(77,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-19 00:15:51'),(78,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-19 00:59:26'),(79,'Crear','Se creó el curso: PENSAMIENTO CREATIVO Y PRODUCTIVO','tb_curso',34,16,'2026-06-19 01:20:12'),(80,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-19 01:50:53'),(81,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-19 02:06:16'),(82,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-19 02:30:38'),(83,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-20 01:32:39'),(84,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-20 02:26:53'),(85,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-20 02:29:16'),(86,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-20 02:34:37'),(87,'Crear','Se creó la meta: Inscritos (1200)','tb_metas',7,16,'2026-06-20 02:56:07'),(88,'Login','Usuario samu inició sesión','tb_usuario',16,16,'2026-06-21 15:55:11');
/*!40000 ALTER TABLE `tb_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_metas`
--

DROP TABLE IF EXISTS `tb_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_metas` (
  `id_meta` int(11) NOT NULL AUTO_INCREMENT,
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
  `periodo_tiempo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_meta`),
  KEY `fk_meta_objetivo` (`meta_objetivo`),
  KEY `idx_metas_ambito` (`id_ambito`),
  CONSTRAINT `fk_metas_ambito` FOREIGN KEY (`id_ambito`) REFERENCES `tb_ambito` (`id_ambito`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_metas`
--

LOCK TABLES `tb_metas` WRITE;
/*!40000 ALTER TABLE `tb_metas` DISABLE KEYS */;
INSERT INTO `tb_metas` VALUES (1,NULL,20,29,'Aprobados','2025-08-01','2025-09-30','Cerrada','2026-05-25 01:46:17',145.00,'Cumplida',0,'Trimestre III 2025'),(2,1,25,30,'Inscritos','2025-10-01','2025-12-31','Cerrada','2026-05-25 01:46:17',120.00,'Cumplida',0,'Inscritos CFS T-IV'),(3,NULL,80,64,'Aprobados','2025-10-01','2025-12-31','Cerrada','2026-05-25 01:46:17',80.00,'En progreso',0,'Aprobados Globales T'),(4,NULL,35,106,'Aprobados','2026-01-01','2026-03-31','Cerrada','2026-05-25 01:46:17',302.86,'Cumplida',0,'Aprobados Globales T'),(5,NULL,150,70,'Inscritos','2026-01-01','2026-06-30','Activa','2026-05-25 01:46:17',46.67,'En progreso',0,'Semestre I 2026'),(6,2,15,12,'Aprobados','2026-04-01','2026-07-31','Activa','2026-05-25 01:46:17',80.00,'En progreso',0,'Comunas Abr-Jul'),(7,NULL,1200,0,'Inscritos','2026-06-20','2027-06-19','Activa','2026-06-20 02:56:07',0.00,'En progreso',0,NULL);
/*!40000 ALTER TABLE `tb_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_modalidad`
--

DROP TABLE IF EXISTS `tb_modalidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_modalidad` (
  `id_modalidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_modalidad` varchar(50) NOT NULL,
  PRIMARY KEY (`id_modalidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_modalidad`
--

LOCK TABLES `tb_modalidad` WRITE;
/*!40000 ALTER TABLE `tb_modalidad` DISABLE KEYS */;
INSERT INTO `tb_modalidad` VALUES (1,'Presencial'),(2,'Online'),(3,'Semi presencial');
/*!40000 ALTER TABLE `tb_modalidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_nombrecurso`
--

DROP TABLE IF EXISTS `tb_nombrecurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_nombrecurso` (
  `id_nombrecurso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(150) NOT NULL,
  PRIMARY KEY (`id_nombrecurso`)
) ENGINE=InnoDB AUTO_INCREMENT=453 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_nombrecurso`
--

LOCK TABLES `tb_nombrecurso` WRITE;
/*!40000 ALTER TABLE `tb_nombrecurso` DISABLE KEYS */;
INSERT INTO `tb_nombrecurso` VALUES (1,'INGLÉS CON PROPÓSITOS GENERALES. NIVEL BÁSICO I'),(2,'AMBIENTE Y PROTECCIÓN AMBIENTAL'),(3,'SEGURIDAD Y SALUD LABORAL'),(4,'ESTADÍSTICA APLICADA A LA SEGURIDAD OCUPACIONAL'),(5,'MATEMÁTICA BÁSICA'),(6,'ORTOGRAFÍA Y REDACCIÓN'),(7,'HISTORIA CONTEMPORÁNEA DE VENEZUELA'),(8,'NUEVO MODELO DE GESTIÓN PRODUCTIVA'),(9,'HABILIDADES SOCIOEMOCIONALES'),(10,'LECTURA Y ESCRITURA CRÍTICA'),(11,'ÉTICA Y VALORES PARA LA TRANSFORMACIÓN SOCIAL'),(12,'TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIÓN (TIC)'),(13,'PROYECTO DE INNOVACIÓN PRODUCTIVA'),(14,'CÁLCULO DE COSTOS Y PRESUPUESTOS'),(15,'MANTENIMIENTO PREVENTIVO DE EQUIPOS Y MAQUINARIAS'),(16,'GESTIÓN COMUNAL Y PARTICIPACIÓN CIUDADANA'),(17,'INTRODUCCIÓN AL DESARROLLO DE SOFTWARE'),(18,'LENGUAJES DE PROGRAMACIÓN BÁSICA'),(19,'BASES DE DATOS FUNDAMENTALES'),(20,'REDES Y TELECOMUNICACIONES BÁSICAS'),(21,'DIBUJO TÉCNICO APLICADO'),(22,'MATERIALES Y HERRAMIENTAS DE CONSTRUCCIÓN'),(23,'FUNDAMENTOS DE ALBAÑILERÍA'),(24,'LECTURA E INTERPRETACIÓN DE PLANOS'),(25,'TECNOLOGÍA DE LOS ALIMENTOS'),(26,'HIGIENE Y MANIPULACIÓN DE ALIMENTOS'),(27,'TÉCNICAS BÁSICAS de COCINA'),(28,'PANADERÍA Y PASTELERÍA BÁSICA'),(29,'ADMINISTRACIÓN Y GESTIÓN DE PEQUEÑAS EMPRESAS'),(30,'ATENCIÓN Y SERVICIO AL CLIENTE'),(31,'MERCADEO Y VENTAS DIGITALES'),(32,'DERECHO LABORAL Y LEGISLACIÓN VINCULANTE'),(33,'PRIMEROS AUXILIOS Y EMERGENCIAS'),(34,'RECREACIÓN Y DINÁMICA DE GRUPOS'),(35,'CULTURA Y MANIFESTACIONES TRADICIONALES'),(36,'MANEJO DE REDES SOCIALES PARA EMPRENDIMIENTOS'),(37,'FIBRA ÓPTICA Y NUEVAS TECNOLOGÍAS'),(38,'MEDICIONES DE ENLACE'),(39,'DISEÑO Y CONSTRUCCIÓN DE REDES DE FIBRA ÓPTICA'),(40,'CERTIFICACIÓN DE INSTALACIÓN.'),(41,'ELABORACIÓN DE PRESUPUESTO PARA FIBRA ÓPTICA'),(42,'DULCES A BASE DE CACAO'),(43,'BOMBONES Y FIGURAS DECORATIVAS CON CHOCOLATE'),(44,'TIPOLOGÍA DE MUEBLES DE MADERA'),(45,'CONSERVACIÓN DE MUEBLES DE MADERA'),(46,'TÉCNICAS DE LUSTRE DE PINTURA Y LAQUEADO'),(47,'MATERIALES Y HERRAMIENTA PARA PINTAR Y LUSTRAR'),(48,'TÉCNICAS DE RESTAURACIÓN DE MADERA'),(49,'Alfabetización Tecnológica'),(50,'Arquitectura del Computador'),(51,'Algoritmia y Programación'),(52,'Base de Datos'),(53,'Redes de Computadoras'),(54,'Ingeniería de Software'),(55,'Sistemas Operativos'),(56,'Proyecto Socio Tecnológico'),(57,'Proyecto Ético de Vida'),(58,'Dirección de Ventas'),(59,'Contabilidad Básica'),(60,'Ofimática'),(61,'Comunicación Organizacional'),(62,'Política Internacional'),(63,'Comunicación Digital'),(64,'Redes Sociales'),(65,'Producción de Contenidos'),(66,'Matemática Aplicada'),(67,'Técnicas de Construcción'),(68,'Seguridad Industrial'),(69,'Procesos Constructivos Tradicionales'),(70,'Uso de Materiales Naturales'),(71,'Seguridad en Obras'),(73,'ARMADO DE CABILLA'),(74,'CÓMPUTOS MÉTRICOS'),(75,'ELECTRICIDAD PARA LA CONSTRUCCIÓN'),(76,'ENCOFRADO'),(77,'INSTALACIÓN DE MAMPOSTERÍA'),(81,'PLANOS ARQUITECTÓNICOS'),(82,'PLANOS DE INSTALACIONES ELÉCTRICAS'),(83,'PLANOS DE INSTALACIONES SANITARIAS E ISOMETRÍA'),(84,'PLANOS ESTRUCTURALES'),(85,'PLANOS TOPOGRÁFICOS'),(86,'PLOMERÍA'),(87,'PROCESO SOCIAL DE TRABAJO'),(90,'SISTEMAS CONSTRUCTIVOS'),(91,'SISTEMAS CONSTRUCTIVOS ECOLÓGICOS'),(93,'ANÁLISIS DE SISTEMAS'),(94,'ANÁLISIS ESTADÍSTICOS'),(97,'ESTRATEGIAS Y TÉCNICAS PARA EL ESTUDIO'),(98,'INTRODUCCIÓN A LA PROGRAMACIÓN'),(99,'LÓGICA'),(101,'OFIMÁTICA BAJO TECNOLOGÍAS LIBRES'),(102,'PENSAMIENTO CREATIVO Y PRODUCTIVO'),(103,'PROGRAMACIÓN ORIENTADA A OBJETOS'),(104,'PROGRAMACIÓN PHP'),(105,'SISTEMA DE INFORMACIÓN'),(106,'SISTEMA OPERATIVO'),(107,'FABRICACIÓN DE ADOBES ARTESANALES'),(108,'FABRICACIÓN DE LADRILLOS ARTESANALES'),(109,'FABRICACIÓN DE LOSETAS (BALDOSAS) ARTESANALES'),(110,'FABRICACIÓN DE TEJAS ARTESANALES'),(111,'TÉCNICAS CONSTRUCTIVAS TRADICIONALES DE EDIFICACIONES DE TIERRA'),(112,'ADMINISTRACIÓN DE RECURSOS HUMANOS'),(113,'ALMACÉN'),(114,'CÁLCULO MERCANTIL'),(115,'GESTIÓN DE EVENTOS'),(116,'HISTORIA CONTEMPORÁNEA DE AMÉRICA'),(117,'LEGISLACIÓN LABORAL'),(118,'MANEJO DE RECURSOS E INSTRUMENTOS FINANCIEROS'),(119,'MARKETING EMPRESARIAL'),(120,'ORATORIA'),(121,'ORGANIZACIÓN Y ADMINISTRACIÓN DE OFICINA'),(122,'FOTOGRAFÍA DIGITAL'),(123,'GEOGRAFÍA ECONÓMICA Y SOCIAL DE VENEZUELA'),(124,'MEDIOS AUDIOVISUALES (LENGUAJE Y COMUNICACIÓN)'),(125,'METODOLOGÍA DE LA INVESTIGACIÓN EDUCATIVA'),(126,'ORIENTACIÓN Y ATENCIÓN AL USUARIO'),(127,'POLÍTICA INTERNACIONAL CONTEMPORÁNEA'),(128,'SOCIOPOLÍTICA DE LA INFORMACIÓN Y COMUNICACIÓN'),(129,'ÉTICA Y LEGISLACIÓN PARA EL EJERCICIO DE LA CIUDADANÍA EN LA COMUNICACIÓN'),(130,'BALANCE DE COMPROBACIÓN CONTABLE'),(131,'COMUNICACIÓN ORAL Y ESCRITA'),(132,'CUENTAS CONTABLES'),(133,'CÁLCULO DE PRESTACIONES SOCIALES'),(134,'IMPUESTOS FISCALES Y PARAFISCALES'),(135,'MANEJO DE INVENTARIO'),(136,'MATEMÁTICA FINANCIERA'),(137,'PLANIFICACIÓN Y CONTROL DE COSTO'),(138,'PRINCIPIOS BÁSICOS DE CONTABILIDAD'),(139,'PROCESAMIENTO DE DOCUMENTOS MERCANTILES'),(140,'SOFTWARE CONTABLE'),(141,'CAMPAMENTO Y EXCURSIÓN'),(142,'DESARROLLO SUSTENTABLE Y SOSTENIBLE EN EL MOTOR TURISMO'),(143,'DISEÑO DE PRODUCTOS TURÍSTICOS NACIONAL E INTERNACIONAL'),(144,'MANEJO DE CUERDAS'),(145,'INGLES CON PROPÓSITOS GENERALES. NIVEL BÁSICO'),(146,'MANEJO DE DESECHOS Y RECURSOS APROVECHABLES'),(147,'NUTRICIÓN Y DIETÉTICA'),(148,'PROTOCOLO Y ETIQUETA'),(149,'SERVICIO DE COMEDOR'),(150,'SERVICIO DE RESTAURANTE'),(151,'TÉCNICAS DE GESTIÓN DE CONTROL DE ALIMENTOS Y BEBIDAS'),(152,'TÉCNICAS INSTRUMENTALES DE LA RECREACIÓN'),(153,'EXCELENCIA EN LA CALIDAD DE VENTAS'),(154,'RELACIONES PÚBLICAS DIGITALES'),(155,'SERVICIO DE BAR'),(156,'COMERCIALIZACIÓN DE PRODUCTOS AGRÍCOLAS'),(157,'CONSTITUCIÓN DE UNIDADES ECONÓMICAS'),(158,'CRÍA DE AVES'),(159,'GENERALIDADES DE LAS GRANJAS INTEGRALES'),(160,'ELABORACIÓN DE COMIDA RÁPIDA'),(161,'GESTIÓN DE SERVICIOS EN ALIMENTOS Y BEBIDAS'),(162,'INTRODUCCIÓN A LA COCINA'),(163,'SERVICIOS DE CATERING'),(164,'ELECTRICIDAD BÁSICA'),(165,'MEDIOS DE TRANSPORTE TERRESTRE'),(166,'REPARACIÓN DE MOTORES DE GASOLINA'),(167,'SISTEMA DE ENFRIAMIENTO DEL AUTOMÓVIL'),(168,'SISTEMA DE HIDRÁULICO DEL AUTOMÓVIL'),(169,'SISTEMA ELÉCTRICO DEL AUTOMÓVIL'),(170,'SEGURIDAD EN EL ÁREA DE LA PERFORACIÓN DE POZOS PETROLEROS'),(171,'SEGURIDAD'),(172,'HIGIENE Y PREVENCIÓN DE RIESGOS AMBIENTALES EN INSTALACIONES PETROLERAS'),(173,'BARBA'),(174,'CEJAS Y BIGOTE'),(175,'COLOCACIÓN DE PELUCAS'),(176,'POSTIZOS Y EXTENSIONES'),(177,'COLORIMETRIA EN LA PELUQUERÍA Y LA BARBERÍA'),(178,'CORTE DE CABELLO'),(179,'GENERALIDADES DE LA PELUQUERÍA Y LA BARBERÍA'),(180,'PROCESOS QUÍMICOS EN LA PELUQUERÍA Y LA BARBERÍA'),(181,'SECADO DE CABELLO'),(182,'TÉCNICA CAPILAR'),(183,'CARPINTERÍA BÁSICA'),(184,'MAQUINARIAS FIJAS EN LA CARPINTERÍA Y EBANISTERÍA'),(185,'METROLOGÍA DE LA CARPINTERÍA'),(186,'PLAN DE NEGOCIOS'),(187,'CORTE DE MATERIA PRIMA'),(188,'COCINA'),(189,'COCINA BÁSICA JAPONESA'),(190,'CORTES Y PREPARACIÓN DE CARNES'),(191,'ELABORACIÓN DE ENSALADAS'),(192,'INTRODUCCIÓN AL VINO'),(193,'MONTAJE Y DECORACIÓN DE BUFFET'),(194,'TÉCNICAS DE PREPARACIÓN DE SALSAS'),(195,'CONFECCIÓN DE ROPA INFANTIL'),(196,'CORTE DE PRENDAS DE VESTIR'),(197,'MECÁNICA DE MÁQUINAS DE COSER'),(198,'MÁQUINA RECTA'),(199,'PATRONAJE DE PRENDAS DE VESTIR'),(200,'PRODUCTOS Y MATERIALES TEXTIL'),(201,'REPARACIÓN DE MÁQUINAS DE COSER'),(202,'CONFECCIÓN DE ROPA DEPORTIVA'),(203,'CONFECCIÓN DE UNIFORMES'),(204,'CRÍA DE OVINO Y CAPRINO'),(205,'GANADERÍA DE DOBLE PROPÓSITO'),(206,'CRÍA DE PORCINO'),(207,'COMERCIALIZACIÓN DEL CACAO'),(208,'ELABORACIÓN DE BIOINSUMOS'),(209,'ESTABLECIMIENTO DEL VIVERO DE CACAO'),(210,'INTRODUCCIÓN AL MUNDO DEL CACAO'),(211,'MANEJO AGROECOLÓGICO DEL CACAO'),(212,'MANEJO INTEGRADO DE PLAGAS Y ENFERMEDADES EN CULTIVOS DE CACAO'),(213,'PROCESAMIENTO PRIMARIO DEL CACAO'),(214,'BEBIDAS A BASE DE CACAO'),(215,'CHOCOLATERÍA'),(216,'USO TERAPÉUTICO Y COSMÉTICO DEL CACAO'),(217,'CONTROL DE SÓLIDOS'),(218,'FUNDAMENTOS BÁSICOS DE LA INDUSTRIA PETROLERA'),(219,'FUNDAMENTOS DE CÁLCULO PARA LA PERFORACIÓN DE POZOS PETROLEROS'),(220,'PRINCIPIOS BÁSICOS DE QUÍMICA'),(221,'GUION GRÁFICO'),(222,'SISTEMA OPERATIVO DE REDES'),(223,'EDICIÓN DE PÁGINAS WEB'),(224,'GOBIERNO ELECTRÓNICO'),(225,'CORTE DE CALZADO'),(226,'COSTURA DE CALZADO'),(227,'ELABORACIÓN DE BOCETOS DE CALZADO'),(228,'ELABORACIÓN DE PATRÓN BASE DE CALZADO'),(229,'ELABORACIÓN DE PATRÓN BASE EN METAL'),(230,'ESCALADA DE PATRONES DE CALZADO'),(231,'FUNDAMENTOS BÁSICOS DEL CALZADO'),(232,'DIBUJO ANATÓMICO'),(233,'EDICIÓN DIGITAL'),(234,'MODELACIÓN Y ANIMACIÓN 2D/3D'),(235,'PROGRAMA DE EDICIÓN DE IMAGEN'),(236,'PROGRAMACIÓN DE VIDEOS'),(237,'PROGRAMACIÓN HTML'),(238,'RECURSOS Y MEDIOS DE INFORMACIÓN DIGITAL'),(239,'SONIDO PARA WEB'),(240,'DISTRIBUCIÓN DE GAS LICUADO DE PETRÓLEO'),(241,'GENERALIDADES DE GAS LICUADO DE PETRÓLEO'),(242,'INSTALACIÓN DE SISTEMAS DE GAS LICUADO DE PETRÓLEO'),(243,'LEGISLACIÓN DE GAS LICUADO DE PETRÓLEO'),(244,'MANIPULACIÓN Y ALMACENAMIENTO DE GAS LICUADO DE PETRÓLEO'),(245,'MÁQUINAS HERRAMIENTAS Y MATERIALES PARA INSTALACIÓN DE G.L.P'),(246,'SEGURIDAD PARA EL ÁREA DE GAS LICUADO DE PETRÓLEO'),(247,'ADMINISTRACIÓN DE REDES SOCIALES'),(248,'ADMINISTRACIÓN DE LA PEQUEÑA Y MEDIANA EMPRESA'),(249,'CONFECCIÓN DE BOLSOS TEJIDOS'),(250,'DESARROLLO HUMANO'),(251,'ELABORACIÓN DE CHINCHORROS'),(252,'CONFECCIÓN DE COJINES EN TEXTILES Y TEJIDOS'),(253,'ELABORACIÓN DE HAMACAS'),(254,'ANATOMÍA DE LA MADERA'),(255,'ELABORADOR DE ARPA'),(256,'ELABORADOR DE BANDOLA'),(257,'ELABORADOR DE CUATRO'),(258,'ELABORADOR DE GUITARRA'),(259,'ELABORADOR DE MANDOLINA'),(260,'SISTEMA ELECTRÓNICO DEL AUTOMÓVIL'),(261,'INSTALACIONES AÉREAS DE RED DE DISTRIBUCIÓN I'),(262,'INSTALACIONES AÉREAS DE RED DE DISTRIBUCIÓN II'),(263,'LECTURA E INTERPRETACIÓN DE PLANOS DE LA RED ELÉCTRICA DE DISTRIBUCIÓN'),(264,'NORMAS TÉCNICAS DE LA RED DE DISTRIBUCIÓN DEL SISTEMA ELÉCTRICO NACIONAL'),(265,'EQUIPOS DE MEDICIÓN DE ENERGÍA ELÉCTRICA'),(266,'ELABORACIÓN Y FORMULACIÓN DE PROYECTOS SOCIOPRODUCTIVOS'),(267,'GESTIÓN DE COSTOS'),(268,'PLANIFICACIÓN Y CONTROL DE PRODUCCIÓN'),(269,'PUBLICIDAD Y MERCADEO'),(270,'DISPOSITIVOS HERRAMIENTAS Y APLICACIONES DE LA COMUNICACIÓN DIGITAL'),(271,'GERENCIA DE PROYECTOS PARA EMPRENDEDORES'),(272,'LIDERAZGO'),(273,'MARKETING'),(274,'MARKETING DIGITAL'),(275,'FUNDAMENTOS DE LA PERFORACIÓN DE POZOS PETROLEROS'),(276,'MANTENIMIENTO DE FLUIDOS DE PERFORACIÓN'),(277,'OPERACIONES DEL ENCUELLADOR'),(278,'OPERACIONES EN LA PLANCHADA DEL TALADRO'),(279,'CIRCUITOS ELECTRÓNICOS'),(280,'CIRCUITOS ELÉCTRICOS'),(281,'ELECTRÓNICA DIGITAL'),(282,'FUNDAMENTOS DE NEUMÁTICA'),(283,'FUNDAMENTOS DEL SOFTWARE LIBRE'),(284,'FÍSICA BÁSICA'),(285,'LECTURA E INTERPRETACIÓN DE DIAGRAMA DE CABLEADOS ELECTRÓNICOS'),(286,'DEPILACIÓN Y EPILACIÓN'),(287,'DISEÑO DE CEJAS'),(288,'EXTENSIÓN DE PESTAÑAS'),(289,'GENERALIDADES DE LA ESTÉTICA'),(290,'HIGIENE DE LA PIEL'),(291,'MANEJO DE CÁMARA'),(292,'ESTRATEGIAS Y PRÁCTICAS GERENCIALES'),(293,'HERRAMIENTAS INTEGRADORAS PARA LA RESOLUCIÓN DE CONFLICTOS'),(294,'LIDERAZGO COMO HERRAMIENTA TRANSFORMADORA'),(295,'CEREMONIAL'),(296,'PROTOCOLO'),(297,'AUXILIO MÉDICO DE EMERGENCIA EN BAJA Y MEDIA MONTAÑA'),(298,'GESTIÓN DE RIESGO EN BAJA Y MEDIA MONTAÑA'),(299,'MANEJO Y GUIATURA DE GRUPO'),(300,'SUPERVIVENCIA EN BAJA Y MEDIA MONTAÑA'),(301,'TÉCNICAS DE ORIENTACIÓN Y CARTOGRAFÍA'),(302,'BÁSICO DE HERRERÍA'),(303,'ELABORACIÓN DE PUERTAS Y VENTANAS METÁLICAS'),(304,'PRIMEROS AUXILIOS'),(305,'SEGURIDAD OCUPACIONAL'),(306,'FUNDAMENTOS DE TUBERÍAS EN EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),(307,'HERRAMIENTAS E INSTRUMENTOS DE MEDICIÓN'),(308,'INSTALACIÓN DE TUBERÍAS EN EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),(309,'MANTENIMIENTO Y DIAGNÓSTICO EN REFRIGERACIÓN Y CLIMATIZACIÓN'),(310,'PRINCIPIOS DE REFRIGERACIÓN'),(311,'SOLDADURA DE REFRIGERACIÓN'),(312,'CABLES Y HERRAMIENTAS PARA EMPALME DE FIBRA ÓPTICA'),(313,'CERTIFICACIÓN DE INSTALACIÓN'),(314,'PRINCIPIOS BÁSICOS DE FIBRA ÓPTICA'),(315,'IMPERMEABILIZACIÓN DE EDIFICACIONES'),(316,'INSTALACIÓN DE ANTENAS SATELITALES FTA'),(317,'INSTALACIÓN Y CONFIGURACIÓN DE TECNOLOGÍA INALÁMBRICA WIFI'),(318,'REDACCIÓN DE INFORMES TÉCNICOS'),(319,'SISTEMA DE TELECOMUNICACIONES'),(320,'REVESTIMIENTOS DE PISOS PAREDES Y TECHOS'),(321,'JARDINERÍA'),(322,'JARDINERÍA PRODUCTIVA SOSTENIBLE'),(323,'CARPINTERÍA DE EDIFICACIONES TRADICIONALES'),(324,'MANICURA'),(325,'PEDICURA'),(326,'SISTEMA BÁSICO DE ACRÍLICO'),(327,'SISTEMA DE FRENOS DEL AUTOMÓVIL'),(328,'CIRCUITOS ELÉCTRICOS PARA REFRIGERACIÓN Y CLIMATIZACIÓN'),(329,'NOCIONES DE GEOMETRÍA'),(330,'SISTEMAS DE REFRIGERACIÓN Y CLIMATIZACIÓN'),(331,'USO DE REFRIGERANTES Y LUBRICANTES'),(332,'SISTEMA DE TRANSMISIÓN DEL AUTOMÓVIL'),(333,'SISTEMA DE DIRECCIÓN DEL AUTOMÓVIL'),(334,'SISTEMA DE SUSPENSIÓN AUTOMOTRIZ'),(335,'REPARACIÓN DE MOTORES DIÉSEL'),(336,'INSTALACIÓN DE TABIQUERÍA Y DRY WALL'),(337,'PINTURA DE OBRAS'),(338,'SISTEMAS DE MANTENIMIENTO DE ESPACIOS PÚBLICOS'),(339,'COMBUSTIÓN PARA OPERACIONES DE CALDERAS'),(340,'CONTROLES ELÉCTRICOS'),(341,'FUNDAMENTOS BÁSICOS EN OPERACIÓN DE CALDERAS'),(342,'INSTRUMENTACIÓN DE CALDERAS'),(343,'MANTENIMIENTO GENERAL DE LAS CALDERAS'),(344,'PROCESO DE LA COMBUSTIÓN EN CALDERAS'),(345,'TRATAMIENTOS QUÍMICOS DEL AGUA PARA OPERACIONES EN CALDERAS'),(346,'TRATAMIENTOS TÉRMICOS'),(347,'MANTENIMIENTO PREVENTIVO DEL CARGADOR FRONTAL'),(348,'TÉCNICAS DE MANEJO DEFENSIVO'),(349,'TÉCNICAS DE OPERACIÓN DEL CARGADOR FRONTAL'),(350,'CULTIVO DE HORTALIZAS EN HUERTOS'),(351,'CULTIVOS DE ESPECIES DE CICLOS CORTOS'),(352,'CONTROL NUMÉRICO COMPUTARIZADO'),(353,'MANTENIMIENTO DE MAQUINARIAS FIJA'),(354,'BÁSICO DE INSTRUMENTACIÓN PARA EL PROCESAMIENTO DE CEREALES'),(355,'CONTROLES Y PROCESOS EN LA RECEPCIÓN Y ALMACENAMIENTO DEL GRANO DE MAÍZ'),(356,'DESGERMINACIÓN DEL GRANO DE MAÍZ'),(357,'LAMINACIÓN DEL ENDOSPERMO'),(358,'MAQUINAS Y MECANISMOS DEL PROCESAMIENTO DE CEREALES'),(359,'MOLIENDA DE LA HOJUELA'),(360,'AFILADO DE HERRAMIENTAS DE CORTE'),(361,'AJUSTE MECÁNICO'),(362,'LECTURA E INTERPRETACIÓN DE PLANOS DE PIEZAS MECÁNICAS'),(363,'METROLOGÍA'),(364,'SOLDADURA BÁSICA'),(365,'INSTALACIÓN DE CILINDROS DE GAS LICUADO DE PETRÓLEO'),(366,'INSTALACIÓN DE PLANTA DE GAS LICUADO DE PETROLEO'),(367,'REPARACIÓN DE CILINDROS DE GAS LICUADO DE PETRÓLEO'),(368,'COMPROMISO COMUNAL DE LA PROMOTORA POR EL PARTO Y NACIMIENTO HUMANIZADO'),(369,'CRIANZA RESPETUOSA'),(370,'PLANIFICACIÓN FAMILIAR'),(371,'PROCESO DE GESTACIÓN PARTO HUMANIZADO Y NACIMIENTO PUERPERIO'),(372,'TRABAJO COMUNITARIO'),(373,'ÉTICA PARA LA VIDA'),(374,'AMASADOR DE PAN'),(375,'CONSERVACIÓN DE ALIMENTOS'),(376,'ELABORACIÓN DE PANES'),(377,'ELABORACIÓN DE PRODUCTOS ALIMENTICIOS SIN GLUTEN'),(378,'MASAS BÁSICAS DE LA PASTELERÍA'),(379,'PANADERÍA'),(380,'PANADERÍA ARTESANAL'),(381,'TÉCNICA DE PROCESAMIENTO DE LEGUMINOSAS TUBÉRCULOS Y CEREALES'),(382,'TÉCNICAS DE PROCESAMIENTO DE PRODUCTOS DE BOLLERÍA DE HARINAS ALTERNATIVAS'),(383,'INTRODUCCIÓN AL CONOCIMIENTO CIENTÍFICO'),(384,'PASTELERÍA Y REPOSTERÍA'),(385,'COLOCACIÓN DE PELUCAS POSTIZOS Y EXTENSIONES'),(386,'PEINADOS MOÑOS Y TRENZADOS'),(387,'PROCESAMIENTO SECUNDARIO DEL CACAO'),(388,'CULTIVADOR DE ARBOLES FRUTALES'),(389,'CUNICULTURA'),(390,'APLICACIONES MÓVILES'),(391,'TEORÍA DE LA COMUNICACIÓN'),(392,'ENTOMOLOGÍA AGRÍCOLA'),(393,'INTRODUCCIÓN A LA AGROECOLOGÍA'),(394,'MANEJO AGROECOLÓGICO DE SUELOS'),(395,'MANEJO DE ENFERMEDADES Y ENEMIGOS NATURALES DE LOS CULTIVOS'),(396,'PRODUCCIÓN AGROECOLÓGICA DE SEMILLAS'),(397,'PRODUCCIÓN DE BIOINSUMOS AGROECOLÓGICOS'),(398,'SISTEMAS AGROFORESTALES'),(399,'DISEÑO Y EVALUACIÓN DE PROGRAMAS RECREATIVOS'),(400,'ANÁLISIS DE SITUACIÓN DE SALUD'),(401,'CURSO DE VIDA DE LA MUJER'),(402,'SALUD COMUNITARIA'),(403,'IMAGEN PERSONAL Y PROFESIONAL DEL PRESTADOR DE SERVICIO HOTELERO'),(404,'INGLES TÉCNICO OCUPACIONAL ATENCIÓN AL CLIENTE'),(405,'INGLÉS PARA PERSONAL DE RECEPCIÓN DE HOTEL'),(406,'SERVICIOS DEL PERSONAL DE RECEPCIÓN DE HOTEL'),(407,'INSTALACIÓN DE VIVERO FORESTAL'),(408,'INVENTARIO FORESTAL'),(409,'LEGISLACIÓN Y POLÍTICAS PÚBLICAS EN REFORESTACIÓN'),(410,'NOCIONES DE BOTÁNICA FORESTAL'),(411,'REFORESTACIÓN Y PROCESOS'),(412,'GENERALIDADES DEL MOTOR DIÉSEL'),(413,'REPARACIÓN DEL SISTEMA CONJUNTO MÓVIL DEL MOTOR DIÉSEL'),(414,'REPARACIÓN DEL SISTEMA DE ALIMENTACIÓN DE AIRE DEL MOTOR DIÉSEL'),(415,'REPARACIÓN DEL SISTEMA DE COMBUSTIBLE DEL MOTOR DIÉSEL'),(416,'REPARACIÓN DEL SISTEMA DE DISTRIBUCIÓN DE MOTOR DIÉSEL'),(417,'REPARACIÓN DEL SISTEMA DE INYECCIÓN DEL MOTOR DIÉSEL'),(418,'REPARACIÓN DEL SISTEMA DE LUBRICACIÓN DEL MOTOR DIÉSEL'),(419,'REPARACIÓN DEL SISTEMA DEL ENFRIAMIENTO DEL MOTOR DIÉSEL'),(420,'REPARACIÓN DEL SISTEMA ELÉCTRICO DEL MOTOR DIÉSEL'),(421,'ILUMINACIÓN'),(422,'APLICACIÓN DE TINTES EN MUEBLES'),(423,'CREACIÓN DE UN TALLER DE RESTAURACIÓN'),(424,'PREPARACIÓN Y APLICACIÓN CON CERAS EN MUEBLES'),(425,'RECUPERACIÓN VOLUMÉTRICA CON MASILLA'),(426,'RESTAURAR CERRADURAS Y HERRAJES EN LA MADERA'),(427,'SUSTITUCIÓN DE ELEMENTOS ESTROPEADOS Y ROTOS'),(428,'TRATAMIENTO CONTRA LA CARCOMA'),(429,'TÉCNICAS DE RESTAURACIÓN'),(430,'GENERALIDADES CONCEPTUALES SOBRE LOS INSTRUMENTOS MUSICALES DE CUERDA'),(431,'HISTORIA Y EVOLUCIÓN DE LOS INSTRUMENTOS MUSICALES DE CUERDA'),(432,'REPARACIÓN Y MANTENIMIENTO DE LOS INSTRUMENTOS MUSICALES DE CUERDA'),(433,'RESTAURACIÓN DE INSTRUMENTOS MUSICALES DE CUERDA'),(434,'LECTURA E INTERPRETACIÓN DE PLANOS DE SOLDADURA'),(435,'NOCIONES DE METALURGIA'),(436,'SOLDADURA OXIACETILÉNICA'),(437,'TRAZADO DE MATERIAL METÁLICO'),(438,'INSPECCIONES DE SOLDADURA (PRUEBAS Y ENSAYOS)'),(439,'PULITURA DE METALES EN ACERO INOXIDABLE'),(440,'SOLDADURA ALUMINOTÉRMICA'),(441,'SOLDADURA MIG MAG'),(442,'SOLDADURA TIG'),(443,'MANTENIMIENTO DE COMPUTADORAS'),(444,'SISTEMA DE CONTROL DE CALIDAD'),(445,'ELABORACIÓN DE ATAÚD DE MADERA'),(446,'ELABORACIÓN DE CAMAS DE MADERA'),(447,'ELABORACIÓN DE PUERTAS Y VENTANAS DE MADERA'),(448,'ELABORACIÓN MESAS Y SILLA DE MADERA'),(449,'SOPORTE TÉCNICO'),(450,'LEGISLACIÓN MILITAR'),(451,'REDACCIÓN Y DOCUMENTACIÓN MILITAR'),(452,'SIMBOLOGÍA MILITAR');
/*!40000 ALTER TABLE `tb_nombrecurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_objetivos`
--

DROP TABLE IF EXISTS `tb_objetivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_objetivos` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_objetivos`
--

LOCK TABLES `tb_objetivos` WRITE;
/*!40000 ALTER TABLE `tb_objetivos` DISABLE KEYS */;
INSERT INTO `tb_objetivos` VALUES (1,'Objetivo 1'),(2,'Capacitación Básica');
/*!40000 ALTER TABLE `tb_objetivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_participante`
--

DROP TABLE IF EXISTS `tb_participante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_participante` (
  `id_participante` int(11) NOT NULL AUTO_INCREMENT,
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
  `id_discapacidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_participante`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `fk_participante_discapacidad` (`id_discapacidad`),
  CONSTRAINT `fk_participante_discapacidad` FOREIGN KEY (`id_discapacidad`) REFERENCES `tb_discapacidad` (`id_discapacidad`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_participante`
--

LOCK TABLES `tb_participante` WRITE;
/*!40000 ALTER TABLE `tb_participante` DISABLE KEYS */;
INSERT INTO `tb_participante` VALUES (1,'Diego','Alejandro','Romero','Silva',32000001,21,'diego.romero@gmail.com','2005-01-10','2025-08-05','En formacion','Bachillerato','Masculino',1),(2,'Camila','Isabella','Rengel','Morillo',32000002,19,'camila.rengel@gmail.com','2007-02-14','2025-08-08','En formacion','Bachillerato','Femenino',1),(3,'Mateo','Daniel','Rivas','Garcia',32000003,20,'mateo.rivas@gmail.com','2006-03-22','2025-08-12','Asignado','Universidad','Masculino',1),(4,'Valentina','Andrea','Sucre','Perez',32000004,25,'valentina.sucre@gmail.com','2001-04-05','2025-08-18','En formacion','Universidad','Femenino',2),(5,'Santiago','Manuel','Santos','Flores',32000005,23,'santiago.santos@gmail.com','2003-05-12','2025-08-22','Asignado','Bachillerato','Masculino',1),(6,'Sebastian','Ignacio','Serrada','Castillo',32000006,22,'seb.serrada@gmail.com','2004-06-25','2025-08-25','Asignado','Bachillerato','Masculino',1),(7,'Sofia','Alejandra','Morillo','Ortiz',32000007,24,'sofia.morillo@gmail.com','2002-07-30','2025-09-02','Disponible','Universidad','Femenino',1),(8,'Daniel','David','Acosta','López',32000008,27,'daniel.acosta@gmail.com','1999-08-14','2025-09-05','Asignado','Primaria','Masculino',3),(9,'Isabella','Rosa','Mendoza','Flores',32000009,21,'isabella.mendoza@gmail.com','2005-09-09','2025-09-12','Disponible','Bachillerato','Femenino',1),(10,'Samuel','Jose','Torres','Ramirez',32000010,21,'samuel.torres@gmail.com','2004-10-27','2025-09-15','En formacion','Universidad','Masculino',1),(11,'Gabriela','Teresa','Rojas','Medina',32000011,23,'gabriela.rojas@gmail.com','2003-11-12','2025-09-18','En formacion','Bachillerato','Femenino',1),(12,'Lucas','Benjamin','Cabrera','Vargas',32000012,19,'lucas.cabrera@gmail.com','2007-12-05','2025-09-22','Disponible','Bachillerato','Masculino',4),(13,'Mariana','Patricia','Castillo','Guzman',32000013,26,'mariana.castillo@gmail.com','2000-01-22','2025-09-26','En formacion','Universidad','Femenino',1),(14,'Nicolas','Andres','Salazar','Moreno',32000014,28,'nicolas.salazar@gmail.com','1998-02-18','2025-10-02','En formacion','Bachillerato','Masculino',1),(15,'Victoria','Beatriz','Delgado','Ortega',32000015,30,'victoria.delgado@gmail.com','1996-03-15','2025-10-05','Disponible','Universidad','Femenino',1),(16,'Benjamin','Edgar','Vargas','Guerrero',32000016,29,'benjamin.vargas@gmail.com','1997-04-20','2025-10-08','En formacion','Primaria','Masculino',2),(17,'Daniela','Elizabeth','Ortiz','Cruz',32000017,33,'daniela.ortiz@gmail.com','1993-05-30','2025-10-12','En formacion','Universidad','Femenino',1),(18,'Felipe','Julio','Caceres','Herrera',32000018,35,'felipe.caceres@gmail.com','1991-06-14','2025-10-15','Asignado','Bachillerato','Masculino',1),(19,'Natalia','Carmen','Benitez','Gil',32000019,22,'natalia.benitez@gmail.com','2004-07-08','2025-10-18','En formacion','Bachillerato','Femenino',1),(20,'Emmanuel','Edwin','Ferrer','Fuentes',32000020,24,'emmanuel.ferrer@gmail.com','2002-08-25','2025-10-22','Asignado','Bachillerato','Masculino',1),(21,'Lucia','Isabel','Blanco','Perez',32000021,20,'lucia.blanco@gmail.com','2006-09-02','2025-10-25','En formacion','Bachillerato','Femenino',1),(22,'Joaquin','Nelson','Ruiz','Alvarez',32000022,26,'joaquin.ruiz@gmail.com','2000-10-12','2025-11-02','En formacion','Universidad','Masculino',1),(23,'Maria','Jose','Jimenez','Castro',32000023,22,'maria.jimenez@gmail.com','2004-11-19','2025-11-05','Disponible','Universidad','Femenino',3),(24,'Tomas','Franklin','Muñoz','Acosta',32000024,30,'tomas.munoz@gmail.com','1996-12-11','2025-11-10','En formacion','Bachillerato','Masculino',1),(25,'Catalina','Gabriela','Moreno','Torres',32000025,25,'catalina.moreno@gmail.com','2001-01-08','2025-11-15','Asignado','Universidad','Femenino',1),(26,'Agustin','Richard','Ortega','Flores',32000026,27,'agustin.ortega@gmail.com','1999-02-28','2025-11-18','En formacion','Primaria','Masculino',1),(27,'Emilia','Sofía','Guerrero','Rivas',32000027,21,'emilia.guerrero@gmail.com','2005-03-17','2025-11-20','Asignado','Bachillerato','Femenino',1),(28,'Simon','Wilmer','Cruz','Mendoza',32000028,23,'simon.cruz@gmail.com','2003-04-15','2025-11-22','En formacion','Bachillerato','Masculino',1),(29,'Antonella','Valentina','Herrera','Castillo',32000029,19,'anto.herrera@gmail.com','2007-05-24','2025-11-25','En formacion','Bachillerato','Femenino',1),(30,'Maximiliano','Julio','Gil','Acosta',32000030,28,'max.gil@gmail.com','1998-06-03','2025-11-28','En formacion','Bachillerato','Masculino',1),(31,'Florencia','Rosa','Fuentes','Rojas',32000031,31,'flor.fuentes@gmail.com','1995-07-14','2025-12-02','En formacion','Universidad','Femenino',1),(32,'Gael','Oscar','Acosta','Mendoza',32000032,23,'gael.acosta@gmail.com','2003-08-20','2025-12-05','Asignado','Bachillerato','Masculino',2),(33,'Martina','Teresa','Torres','Medina',32000033,22,'martina.torres@gmail.com','2004-09-18','2025-12-08','En formacion','Universidad','Femenino',1),(34,'Bastian','Cesar','Medina','Rivas',32000034,25,'bastian.medina@gmail.com','2001-10-02','2025-12-12','En formacion','Bachillerato','Masculino',1),(35,'Isidora','Laura','Castillo','Silva',32000035,20,'isi.castillo@gmail.com','2006-11-12','2025-12-15','En formacion','Bachillerato','Femenino',1),(36,'Gaspar','Franklin','Guzman','Rengel',32000036,21,'gaspar.guzman@gmail.com','2005-12-05','2025-12-18','En formacion','Bachillerato','Masculino',1),(37,'Ignacia','Patricia','Moreno','Cumana',32000037,24,'ignacia.moreno@gmail.com','2002-01-30','2025-12-22','En formacion','Universidad','Femenino',1),(38,'Alonso','Manuel','Ortega','Santos',32000038,29,'alonso.ortega@gmail.com','1997-02-22','2025-12-26','Asignado','Bachillerato','Masculino',1),(39,'Elena','Beatriz','Perez','Sucre',32000039,27,'elena.perez@gmail.com','1999-03-11','2025-12-28','En formacion','Universidad','Femenino',1),(40,'Javier','Eduardo','Rodriguez','Serrada',32000040,32,'javier.rodriguez@gmail.com','1994-04-01','2026-01-05','Disponible','Universidad','Masculino',1),(41,'Isabel','Teresa','Martinez','Nolazco',32000041,35,'isabel.martinez@gmail.com','1991-05-19','2026-01-08','Asignado','Primaria','Femenino',1),(42,'Mateo','Alexander','Rivas','Gonzales',32000042,19,'mateo.alex@gmail.com','2007-06-08','2026-01-12','Diferido','Bachillerato','Masculino',1),(43,'Sofia','Del Valle','Sucre','Martinez',32000043,21,'sofia.sucre@gmail.com','2005-07-15','2026-01-15','Disponible','Bachillerato','Femenino',1),(44,'Diego','Jose','Santos','Rengel',32000044,25,'diego.santos@gmail.com','2001-08-30','2026-01-18','Asignado','Universidad','Masculino',1),(45,'Valentina','Rosa','Serrada','Martinez',32000045,23,'vale.serrada@gmail.com','2003-09-12','2026-01-20','Disponible','Bachillerato','Femenino',4),(46,'Lucas','Manuel','Morillo','Perez',32000046,22,'lucas.morillo@gmail.com','2004-10-05','2026-01-22','En formacion','Bachillerato','Masculino',1),(47,'Camila','Gabriela','Acosta','Flores',32000047,20,'camila.acosta@gmail.com','2006-11-25','2026-01-25','En formacion','Bachillerato','Femenino',1),(48,'Sebastian','Eduardo','Mendoza','Torres',32000048,27,'seb.mendoza@gmail.com','1999-01-03','2026-01-28','En formacion','Universidad','Masculino',1),(49,'Isabella','Elena','Torres','Medina',32000049,19,'isabella.torres@gmail.com','2007-02-28','2026-02-02','En formacion','Bachillerato','Femenino',1),(50,'Nicolas','Benjamín','Vargas','Vargas',32000050,31,'nicolas.vargas@gmail.com','1995-03-24','2026-02-05','Disponible','Bachillerato','Masculino',1),(51,'Daniela','Fernanda','Paredes','Acosta',32000051,22,'daniela.p@gmail.com','2004-04-12','2026-02-08','Asignado','Bachillerato','Femenino',1),(52,'Andrés','Felipe','Velasquez','Salazar',32000052,28,'andres.v@gmail.com','1998-05-18','2026-02-12','Asignado','Universidad','Masculino',1),(53,'Patricia','Juliana','Molina','Blanco',32000053,35,'patricia.m@gmail.com','1991-06-30','2026-02-15','Disponible','Primaria','Femenino',1),(54,'Gabriel','Alejandro','Rojas','Castillo',32000054,20,'gabriel.r@gmail.com','2006-07-22','2026-02-18','Asignado','Bachillerato','Masculino',2),(55,'Isabel','Teresa','Romero','Guzman',32000055,23,'isabel.r@gmail.com','2003-08-14','2026-02-20','En formacion','Bachillerato','Femenino',1),(56,'Julio','César','Salazar','Ortega',32000056,30,'julio.s@gmail.com','1996-09-05','2026-02-22','En formacion','Universidad','Masculino',1),(57,'Laura','Valentina','Medina','Moreno',32000057,24,'laura.m@gmail.com','2002-10-18','2026-02-25','En formacion','Universidad','Femenino',1),(58,'Oscar','David','Suarez','Delgado',32000058,29,'oscar.s@gmail.com','1997-11-25','2026-02-28','Diferido','Bachillerato','Masculino',1),(59,'Paola','Alejandra','Flores','Vargas',32000059,21,'paola.f@gmail.com','2005-01-30','2026-03-02','Disponible','Bachillerato','Femenino',1),(60,'Ricardo','José','Castro','Guerrero',32000060,27,'ricardo.c@gmail.com','1999-02-14','2026-03-05','Disponible','Universidad','Masculino',3),(61,'Silvia','Carolina','Ortega','Benitez',32000061,26,'silvia.o@gmail.com','2000-03-19','2026-03-08','Disponible','Universidad','Femenino',1),(62,'Víctor','Hugo','Rios','Gil',32000062,34,'victor.r@gmail.com','1992-04-25','2026-03-12','Disponible','Bachillerato','Masculino',1),(63,'Adriana','Mercedes','Mendoza','Blanco',32000063,23,'adriana.m@gmail.com','2003-05-15','2026-03-15','Asignado','Universidad','Femenino',1),(64,'Arturo','Leonel','Ferrer','Fuentes',32000064,25,'arturo.f@gmail.com','2001-06-20','2026-03-18','Asignado','Bachillerato','Masculino',1),(65,'Blanca','Nieves','Castillo','Ortega',32000065,30,'blanca.c@gmail.com','1996-07-08','2026-03-20','Asignado','Primaria','Femenino',1),(66,'César','Augusto','Vargas','Rengel',32000066,28,'cesar.v@gmail.com','1998-08-30','2026-03-22','Asignado','Bachillerato','Masculino',1),(67,'Diana','Carolina','Santos','Silva',32000067,19,'diana.s@gmail.com','2007-09-12','2026-03-24','Asignado','Bachillerato','Femenino',1),(68,'Eduardo','José','Sucre','Morillo',32000068,22,'eduardo.s@gmail.com','2004-10-05','2026-03-26','Asignado','Bachillerato','Masculino',1),(69,'Fabiola','Elena','Acosta','Rivas',32000069,21,'fabiola.a@gmail.com','2005-11-22','2026-03-30','Asignado','Bachillerato','Femenino',1),(70,'Gerardo','Antonio','Romero','Serrada',32000070,45,'gerardo.r@gmail.com','1981-05-14','2026-04-02','En formacion','Primaria','Masculino',1),(71,'Helena','Isabel','Serrada','Medina',32000071,38,'helena.s@gmail.com','1988-08-20','2026-04-05','En formacion','Bachillerato','Femenino',1),(72,'Iván','Darío','Morillo','Salazar',32000072,33,'ivan.m@gmail.com','1993-09-15','2026-04-08','En formacion','Universidad','Masculino',2),(73,'Julia','Teresa','Torres','Moreno',32000073,24,'julia.t@gmail.com','2002-02-18','2026-04-10','En formacion','Universidad','Femenino',1),(74,'Kevin','Eduardo','Vargas','Acosta',32000074,27,'kevin.v@gmail.com','1999-03-30','2026-04-12','En formacion','Bachillerato','Masculino',1),(75,'Lorena','Beatriz','Blanco','Torres',32000075,29,'lorena.b@gmail.com','1997-04-10','2026-04-15','En formacion','Universidad','Femenino',1),(76,'Mauricio','Andres','Gomez','Cruz',32000076,20,'mauricio.g@gmail.com','2006-06-12','2026-04-18','En sistema','Bachillerato','Masculino',1),(77,'Nora','Gabriela','Perez','Ruiz',32000077,23,'nora.p@gmail.com','2003-07-28','2026-04-20','En sistema','Universidad','Femenino',1),(78,'Omar','José','Medina','Castillo',32000078,22,'omar.m@gmail.com','2004-08-04','2026-04-22','En sistema','Bachillerato','Masculino',1),(79,'Paula','Isabel','Castro','Ortega',32000079,31,'paula.c@gmail.com','1995-10-14','2026-04-25','En sistema','Universidad','Femenino',1),(80,'Rafael','Jesús','Rodriguez','Moreno',32000080,26,'rafael.r@gmail.com','2000-11-09','2026-04-28','En sistema','Bachillerato','Masculino',1),(81,'Sonia','Alejandra','Gonzalez','Herrera',32000081,25,'sonia.g@gmail.com','2001-01-20','2026-05-02','En sistema','Universidad','Femenino',1),(82,'Tito','Wilmer','Lopez','Acosta',32000082,28,'tito.l@gmail.com','1998-02-15','2026-05-05','En sistema','Bachillerato','Masculino',1),(83,'Úrsula','Elena','Hernandez','Gil',32000083,20,'ursula.h@gmail.com','2006-03-12','2026-05-08','En sistema','Bachillerato','Femenino',1),(84,'Walter','César','Martinez','Fuentes',32000084,30,'walter.m@gmail.com','1996-04-24','2026-05-12','En sistema','Universidad','Masculino',1),(85,'Ximena','Rosa','Perez','Blanco',32000085,23,'ximena.p@gmail.com','2003-05-20','2026-05-15','En sistema','Bachillerato','Femenino',1),(86,'Yago','Nelson','Alvarez','Castillo',32000086,27,'yago.a@gmail.com','1999-06-30','2026-05-18','En sistema','Universidad','Masculino',1),(87,'Zoe','Sofía','Sanchez','Rojas',32000087,19,'zoe.s@gmail.com','2007-07-15','2026-05-20','En sistema','Bachillerato','Femenino',1),(88,'Alfredo','José','Diaz','Salazar',32000088,35,'alfredo.d@gmail.com','1991-08-05','2026-05-22','Asignado','Primaria','Masculino',1),(89,'Berta','Beatriz','Gomez','Castillo',32000089,21,'berta.g@gmail.com','2005-09-02','2026-05-24','Asignado','Bachillerato','Femenino',1),(90,'Claudio','José','Castillo','Vargas',32000090,24,'claudio.c@gmail.com','2002-10-10','2026-05-24','Asignado','Bachillerato','Masculino',1);
/*!40000 ALTER TABLE `tb_participante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_participante_curso`
--

DROP TABLE IF EXISTS `tb_participante_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_participante_curso` (
  `id_participante_curso` int(11) NOT NULL AUTO_INCREMENT,
  `id_participante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha_inscripcion` date NOT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date NOT NULL,
  `estatus_participante` enum('Asignado','Diferido','Aprobado','Reprobado','En formación','Ausente') NOT NULL,
  PRIMARY KEY (`id_participante_curso`),
  KEY `id_participante` (`id_participante`),
  KEY `id_curso` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_participante_curso`
--

LOCK TABLES `tb_participante_curso` WRITE;
/*!40000 ALTER TABLE `tb_participante_curso` DISABLE KEYS */;
INSERT INTO `tb_participante_curso` VALUES (11,11,3,'2025-08-05','2025-08-11','2025-09-19','Aprobado'),(12,12,3,'2025-08-05','2025-08-11','2025-09-19','Aprobado'),(13,13,3,'2025-08-06','2025-08-11','2025-09-19','Ausente'),(14,14,3,'2025-08-06','2025-08-11','2025-09-19','Aprobado'),(15,15,3,'2025-08-08','2025-08-11','2025-09-19','Aprobado'),(16,16,3,'2025-08-08','2025-08-11','2025-09-19','Aprobado'),(17,17,3,'2025-08-09','2025-08-11','2025-09-19','Aprobado'),(18,18,3,'2025-08-09','2025-08-11','2025-09-19','Reprobado'),(19,19,3,'2025-08-10','2025-08-11','2025-09-19','Aprobado'),(20,20,3,'2025-08-10','2025-08-11','2025-09-19','Aprobado'),(21,21,3,'2025-08-11','2025-08-11','2025-09-19','Aprobado'),(22,22,3,'2025-08-11','2025-08-11','2025-09-19','Aprobado'),(23,23,4,'2025-09-02','2025-09-08','2025-10-17','Aprobado'),(24,24,4,'2025-09-02','2025-09-08','2025-10-17','Aprobado'),(25,25,4,'2025-09-03','2025-09-08','2025-10-17','Aprobado'),(26,26,4,'2025-09-03','2025-09-08','2025-10-17','Reprobado'),(27,27,4,'2025-09-04','2025-09-08','2025-10-17','Aprobado'),(28,28,4,'2025-09-04','2025-09-08','2025-10-17','Aprobado'),(29,29,4,'2025-09-05','2025-09-08','2025-10-17','Aprobado'),(30,30,4,'2025-09-05','2025-09-08','2025-10-17','Aprobado'),(31,31,4,'2025-09-06','2025-09-08','2025-10-17','Aprobado'),(32,32,4,'2025-09-06','2025-09-08','2025-10-17','Aprobado'),(33,33,5,'2025-09-08','2025-09-15','2025-10-24','Aprobado'),(34,34,5,'2025-09-08','2025-09-15','2025-10-24','Aprobado'),(35,35,5,'2025-09-09','2025-09-15','2025-10-24','Reprobado'),(36,36,5,'2025-09-09','2025-09-15','2025-10-24','Aprobado'),(37,37,5,'2025-09-10','2025-09-15','2025-10-24','Aprobado'),(38,38,5,'2025-09-10','2025-09-15','2025-10-24','Aprobado'),(39,39,5,'2025-09-12','2025-09-15','2025-10-24','Aprobado'),(40,40,5,'2025-09-12','2025-09-15','2025-10-24','Aprobado'),(41,41,5,'2025-09-14','2025-09-15','2025-10-24','Aprobado'),(42,42,5,'2025-09-14','2025-09-15','2025-10-24','Aprobado'),(43,43,5,'2025-09-15','2025-09-15','2025-10-24','Aprobado'),(44,1,6,'2025-10-01','2025-10-06','2025-11-14','Aprobado'),(45,2,6,'2025-10-01','2025-10-06','2025-11-14','Aprobado'),(46,3,6,'2025-10-02','2025-10-06','2025-11-14','Aprobado'),(47,4,6,'2025-10-02','2025-10-06','2025-11-14','Aprobado'),(48,5,6,'2025-10-03','2025-10-06','2025-11-14','Aprobado'),(49,11,6,'2025-10-03','2025-10-06','2025-11-14','Aprobado'),(50,12,6,'2025-10-04','2025-10-06','2025-11-14','Aprobado'),(51,13,6,'2025-10-04','2025-10-06','2025-11-14','Aprobado'),(52,14,6,'2025-10-05','2025-10-06','2025-11-14','Aprobado'),(53,15,6,'2025-10-05','2025-10-06','2025-11-14','Aprobado'),(54,16,7,'2025-10-05','2025-10-13','2025-11-21','Aprobado'),(55,17,7,'2025-10-06','2025-10-13','2025-11-21','Aprobado'),(56,18,7,'2025-10-08','2025-10-13','2025-11-21','Aprobado'),(57,19,7,'2025-10-08','2025-10-13','2025-11-21','Aprobado'),(58,20,7,'2025-10-10','2025-10-13','2025-11-21','Aprobado'),(59,21,7,'2025-10-10','2025-10-13','2025-11-21','Aprobado'),(60,22,7,'2025-10-11','2025-10-13','2025-11-21','Aprobado'),(61,23,7,'2025-10-11','2025-10-13','2025-11-21','Aprobado'),(62,24,7,'2025-10-12','2025-10-13','2025-11-21','Aprobado'),(63,25,7,'2025-10-12','2025-10-13','2025-11-21','Aprobado'),(64,26,7,'2025-10-13','2025-10-13','2025-11-21','Aprobado'),(65,27,7,'2025-10-13','2025-10-13','2025-11-21','Aprobado'),(66,28,8,'2025-10-12','2025-10-20','2025-12-12','Aprobado'),(67,29,8,'2025-10-12','2025-10-20','2025-12-12','Aprobado'),(68,30,8,'2025-10-14','2025-10-20','2025-12-12','Aprobado'),(69,31,8,'2025-10-15','2025-10-20','2025-12-12','Aprobado'),(70,32,8,'2025-10-15','2025-10-20','2025-12-12','Aprobado'),(71,33,8,'2025-10-18','2025-10-20','2025-12-12','Aprobado'),(72,34,8,'2025-10-18','2025-10-20','2025-12-12','Aprobado'),(73,35,8,'2025-10-19','2025-10-20','2025-12-12','Aprobado'),(74,36,8,'2025-10-19','2025-10-20','2025-12-12','Aprobado'),(75,37,8,'2025-10-20','2025-10-20','2025-12-12','Aprobado'),(76,38,8,'2025-10-20','2025-10-20','2025-12-12','Aprobado'),(87,11,10,'2025-11-02','2025-11-10','2025-12-19','Aprobado'),(88,12,10,'2025-11-02','2025-11-10','2025-12-19','Aprobado'),(89,13,10,'2025-11-04','2025-11-10','2025-12-19','Aprobado'),(90,14,10,'2025-11-05','2025-11-10','2025-12-19','Aprobado'),(91,15,10,'2025-11-05','2025-11-10','2025-12-19','Aprobado'),(92,22,10,'2025-11-06','2025-11-10','2025-12-19','Aprobado'),(93,23,10,'2025-11-08','2025-11-10','2025-12-19','Aprobado'),(94,24,10,'2025-11-08','2025-11-10','2025-12-19','Aprobado'),(95,25,10,'2025-11-10','2025-11-10','2025-12-19','Aprobado'),(96,26,10,'2025-11-10','2025-11-10','2025-12-19','Aprobado'),(97,27,11,'2025-11-09','2025-11-17','2025-12-26','Aprobado'),(98,28,11,'2025-11-10','2025-11-17','2025-12-26','Aprobado'),(99,29,11,'2025-11-12','2025-11-17','2025-12-26','Aprobado'),(100,30,11,'2025-11-12','2025-11-17','2025-12-26','Aprobado'),(101,31,11,'2025-11-14','2025-11-17','2025-12-26','Aprobado'),(102,32,11,'2025-11-15','2025-11-17','2025-12-26','Aprobado'),(103,33,11,'2025-11-15','2025-11-17','2025-12-26','Aprobado'),(104,34,11,'2025-11-16','2025-11-17','2025-12-26','Aprobado'),(105,35,11,'2025-11-16','2025-11-17','2025-12-26','Aprobado'),(106,36,11,'2025-11-17','2025-11-17','2025-12-26','Aprobado'),(107,37,11,'2025-11-17','2025-11-17','2025-12-26','Aprobado'),(108,38,12,'2025-11-20','2025-12-01','2026-01-09','Aprobado'),(109,39,12,'2025-11-20','2025-12-01','2026-01-09','Aprobado'),(110,40,12,'2025-11-22','2025-12-01','2026-01-09','Aprobado'),(111,41,12,'2025-11-25','2025-12-01','2026-01-09','Aprobado'),(112,42,12,'2025-11-26','2025-12-01','2026-01-09','Aprobado'),(113,43,12,'2025-11-28','2025-12-01','2026-01-09','Aprobado'),(114,44,12,'2025-11-30','2025-12-01','2026-01-09','Aprobado'),(115,45,12,'2025-12-01','2025-12-01','2026-01-09','Aprobado'),(116,46,12,'2025-12-01','2025-12-01','2026-01-09','Aprobado'),(117,47,12,'2025-12-01','2025-12-01','2026-01-09','Aprobado'),(118,14,13,'2025-11-30','2025-12-08','2026-01-16','Aprobado'),(119,15,13,'2025-12-01','2025-12-08','2026-01-16','Aprobado'),(120,16,13,'2025-12-02','2025-12-08','2026-01-16','Aprobado'),(121,17,13,'2025-12-04','2025-12-08','2026-01-16','Aprobado'),(122,18,13,'2025-12-05','2025-12-08','2026-01-16','Aprobado'),(123,19,13,'2025-12-05','2025-12-08','2026-01-16','Aprobado'),(124,20,13,'2025-12-06','2025-12-08','2026-01-16','Aprobado'),(125,31,13,'2025-12-06','2025-12-08','2026-01-16','Aprobado'),(126,32,13,'2025-12-08','2025-12-08','2026-01-16','Aprobado'),(127,33,13,'2025-12-08','2025-12-08','2026-01-16','Aprobado'),(128,34,13,'2025-12-08','2025-12-08','2026-01-16','Aprobado'),(129,1,14,'2026-01-02','2026-01-05','2026-02-13','Aprobado'),(130,2,14,'2026-01-02','2026-01-05','2026-02-13','Aprobado'),(131,3,14,'2026-01-03','2026-01-05','2026-02-13','Aprobado'),(132,4,14,'2026-01-03','2026-01-05','2026-02-13','Aprobado'),(133,5,14,'2026-01-03','2026-01-05','2026-02-13','Aprobado'),(134,6,14,'2026-01-04','2026-01-05','2026-02-13','Aprobado'),(135,7,14,'2026-01-04','2026-01-05','2026-02-13','Aprobado'),(136,8,14,'2026-01-04','2026-01-05','2026-02-13','Aprobado'),(137,9,14,'2026-01-05','2026-01-05','2026-02-13','Aprobado'),(138,10,14,'2026-01-05','2026-01-05','2026-02-13','Aprobado'),(139,11,15,'2026-01-05','2026-01-12','2026-02-20','Aprobado'),(140,12,15,'2026-01-05','2026-01-12','2026-02-20','Aprobado'),(141,22,15,'2026-01-06','2026-01-12','2026-02-20','Aprobado'),(142,23,15,'2026-01-06','2026-01-12','2026-02-20','Aprobado'),(143,24,15,'2026-01-08','2026-01-12','2026-02-20','Aprobado'),(144,25,15,'2026-01-08','2026-01-12','2026-02-20','Aprobado'),(145,26,15,'2026-01-10','2026-01-12','2026-02-20','Aprobado'),(146,27,15,'2026-01-10','2026-01-12','2026-02-20','Aprobado'),(147,48,15,'2026-01-12','2026-01-12','2026-02-20','Aprobado'),(148,49,15,'2026-01-12','2026-01-12','2026-02-20','Aprobado'),(149,35,16,'2026-01-12','2026-01-19','2026-03-13','Aprobado'),(150,36,16,'2026-01-12','2026-01-19','2026-03-13','Aprobado'),(151,37,16,'2026-01-14','2026-01-19','2026-03-13','Aprobado'),(152,38,16,'2026-01-14','2026-01-19','2026-03-13','Aprobado'),(153,39,16,'2026-01-15','2026-01-19','2026-03-13','Aprobado'),(154,43,16,'2026-01-16','2026-01-19','2026-03-13','Aprobado'),(155,44,16,'2026-01-16','2026-01-19','2026-03-13','Aprobado'),(156,45,16,'2026-01-18','2026-01-19','2026-03-13','Aprobado'),(157,46,16,'2026-01-18','2026-01-19','2026-03-13','Aprobado'),(158,47,16,'2026-01-19','2026-01-19','2026-03-13','Aprobado'),(159,50,16,'2026-01-19','2026-01-19','2026-03-13','Aprobado'),(160,1,17,'2026-01-28','2026-02-02','2026-03-13','Aprobado'),(161,2,17,'2026-01-28','2026-02-02','2026-03-13','Aprobado'),(162,3,17,'2026-01-30','2026-02-02','2026-03-13','Aprobado'),(163,4,17,'2026-01-30','2026-02-02','2026-03-13','Aprobado'),(164,5,17,'2026-02-01','2026-02-02','2026-03-13','Aprobado'),(165,6,17,'2026-02-01','2026-02-02','2026-03-13','Aprobado'),(166,7,17,'2026-02-02','2026-02-02','2026-03-13','Aprobado'),(167,8,17,'2026-02-02','2026-02-02','2026-03-13','Aprobado'),(168,9,17,'2026-02-02','2026-02-02','2026-03-13','Aprobado'),(169,10,17,'2026-02-02','2026-02-02','2026-03-13','Aprobado'),(170,51,18,'2026-02-02','2026-02-09','2026-03-20','Aprobado'),(171,52,18,'2026-02-02','2026-02-09','2026-03-20','Aprobado'),(172,53,18,'2026-02-04','2026-02-09','2026-03-20','Aprobado'),(173,54,18,'2026-02-05','2026-02-09','2026-03-20','Aprobado'),(174,55,18,'2026-02-06','2026-02-09','2026-03-20','Aprobado'),(175,56,18,'2026-02-06','2026-02-09','2026-03-20','Aprobado'),(176,57,18,'2026-02-08','2026-02-09','2026-03-20','Aprobado'),(177,58,18,'2026-02-09','2026-02-09','2026-03-20','Aprobado'),(178,11,18,'2026-02-09','2026-02-09','2026-03-20','Aprobado'),(179,12,18,'2026-02-09','2026-02-09','2026-03-20','Aprobado'),(180,13,18,'2026-02-09','2026-02-09','2026-03-20','Aprobado'),(181,14,19,'2026-02-10','2026-02-16','2026-04-10','Aprobado'),(182,15,19,'2026-02-10','2026-02-16','2026-04-10','Aprobado'),(183,16,19,'2026-02-12','2026-02-16','2026-04-10','Aprobado'),(184,17,19,'2026-02-12','2026-02-16','2026-04-10','Aprobado'),(185,18,19,'2026-02-14','2026-02-16','2026-04-10','Aprobado'),(186,19,19,'2026-02-14','2026-02-16','2026-04-10','Aprobado'),(187,20,19,'2026-02-15','2026-02-16','2026-04-10','Aprobado'),(188,21,19,'2026-02-15','2026-02-16','2026-04-10','Aprobado'),(189,22,19,'2026-02-16','2026-02-16','2026-04-10','Aprobado'),(190,23,19,'2026-02-16','2026-02-16','2026-04-10','Aprobado'),(191,24,19,'2026-02-16','2026-02-16','2026-04-10','Aprobado'),(192,25,19,'2026-02-16','2026-02-16','2026-04-10','Aprobado'),(193,9,20,'2026-02-18','2026-02-23','2026-04-17','Asignado'),(194,26,20,'2026-02-18','2026-02-23','2026-04-17','Asignado'),(195,32,20,'2026-02-20','2026-02-23','2026-04-17','Asignado'),(196,44,20,'2026-02-20','2026-02-23','2026-04-17','Asignado'),(197,45,20,'2026-02-22','2026-02-23','2026-04-17','Asignado'),(198,40,20,'2026-02-22','2026-02-23','2026-04-17','Asignado'),(199,27,21,'2026-02-25','2026-03-02','2026-04-10','Aprobado'),(200,28,21,'2026-02-25','2026-03-02','2026-04-10','Aprobado'),(201,29,21,'2026-02-26','2026-03-02','2026-04-10','Aprobado'),(202,30,21,'2026-02-28','2026-03-02','2026-04-10','Aprobado'),(203,59,21,'2026-03-01','2026-03-02','2026-04-10','Aprobado'),(204,60,21,'2026-03-02','2026-03-02','2026-04-10','Aprobado'),(205,61,21,'2026-03-02','2026-03-02','2026-04-10','Aprobado'),(206,62,21,'2026-03-02','2026-03-02','2026-04-10','Aprobado'),(207,63,21,'2026-03-02','2026-03-02','2026-04-10','Aprobado'),(208,64,21,'2026-03-02','2026-03-02','2026-04-10','Aprobado'),(209,65,22,'2026-03-04','2026-03-09','2026-04-17','Aprobado'),(210,66,22,'2026-03-05','2026-03-09','2026-04-17','Aprobado'),(211,67,22,'2026-03-06','2026-03-09','2026-04-17','Aprobado'),(212,68,22,'2026-03-08','2026-03-09','2026-04-17','Aprobado'),(213,69,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(214,31,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(215,32,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(216,33,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(217,34,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(218,35,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(219,36,22,'2026-03-09','2026-03-09','2026-04-17','Aprobado'),(220,37,23,'2026-03-10','2026-03-16','2026-05-08','Aprobado'),(221,38,23,'2026-03-11','2026-03-16','2026-05-08','Aprobado'),(222,39,23,'2026-03-12','2026-03-16','2026-05-08','Aprobado'),(223,40,23,'2026-03-12','2026-03-16','2026-05-08','Aprobado'),(224,41,23,'2026-03-14','2026-03-16','2026-05-08','Aprobado'),(225,42,23,'2026-03-14','2026-03-16','2026-05-08','Aprobado'),(226,46,23,'2026-03-15','2026-03-16','2026-05-08','Aprobado'),(227,47,23,'2026-03-15','2026-03-16','2026-05-08','Aprobado'),(228,48,23,'2026-03-16','2026-03-16','2026-05-08','Aprobado'),(229,49,23,'2026-03-16','2026-03-16','2026-05-08','Aprobado'),(230,50,23,'2026-03-16','2026-03-16','2026-05-08','Aprobado'),(231,1,23,'2026-03-16','2026-03-16','2026-05-08','Aprobado'),(232,2,24,'2026-03-18','2026-03-23','2026-05-01','Asignado'),(233,3,24,'2026-03-18','2026-03-23','2026-05-01','Asignado'),(234,4,24,'2026-03-20','2026-03-23','2026-05-01','Asignado'),(235,5,24,'2026-03-20','2026-03-23','2026-05-01','Asignado'),(236,6,24,'2026-03-22','2026-03-23','2026-05-01','Asignado'),(237,7,24,'2026-03-22','2026-03-23','2026-05-01','Asignado'),(238,8,24,'2026-03-23','2026-03-23','2026-05-01','Asignado'),(239,10,25,'2026-04-02','2026-04-06','2026-05-29','En formación'),(240,11,25,'2026-04-02','2026-04-06','2026-05-29','En formación'),(241,16,25,'2026-04-03','2026-04-06','2026-05-29','En formación'),(242,21,25,'2026-04-03','2026-04-06','2026-05-29','En formación'),(243,22,25,'2026-04-04','2026-04-06','2026-05-29','En formación'),(244,28,25,'2026-04-04','2026-04-06','2026-05-29','En formación'),(245,29,25,'2026-04-05','2026-04-06','2026-05-29','En formación'),(246,30,25,'2026-04-05','2026-04-06','2026-05-29','En formación'),(247,70,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(248,71,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(249,72,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(250,73,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(251,74,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(252,75,25,'2026-04-06','2026-04-06','2026-05-29','En formación'),(253,1,26,'2026-04-08','2026-04-13','2026-06-05','En formación'),(254,2,26,'2026-04-08','2026-04-13','2026-06-05','En formación'),(255,4,26,'2026-04-10','2026-04-13','2026-06-05','En formación'),(256,36,26,'2026-04-10','2026-04-13','2026-06-05','En formación'),(257,37,26,'2026-04-11','2026-04-13','2026-06-05','En formación'),(258,46,26,'2026-04-11','2026-04-13','2026-06-05','En formación'),(259,47,26,'2026-04-12','2026-04-13','2026-06-05','En formación'),(260,48,26,'2026-04-12','2026-04-13','2026-06-05','En formación'),(261,49,26,'2026-04-13','2026-04-13','2026-06-05','En formación'),(262,55,26,'2026-04-13','2026-04-13','2026-06-05','En formación'),(263,56,26,'2026-04-13','2026-04-13','2026-06-05','En formación'),(264,57,26,'2026-04-13','2026-04-13','2026-06-05','En formación'),(265,31,27,'2026-04-15','2026-04-20','2026-06-12','En formación'),(266,70,27,'2026-04-15','2026-04-20','2026-06-12','En formación'),(267,71,27,'2026-04-16','2026-04-20','2026-06-12','En formación'),(268,72,27,'2026-04-16','2026-04-20','2026-06-12','En formación'),(269,73,27,'2026-04-18','2026-04-20','2026-06-12','En formación'),(270,74,27,'2026-04-18','2026-04-20','2026-06-12','En formación'),(271,75,27,'2026-04-19','2026-04-20','2026-06-12','En formación'),(272,10,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(273,11,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(274,16,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(275,21,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(276,22,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(277,28,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(278,29,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(279,30,27,'2026-04-20','2026-04-20','2026-06-12','En formación'),(280,1,28,'2026-04-28','2026-05-04','2026-06-26','En formación'),(281,2,28,'2026-04-30','2026-05-04','2026-06-26','En formación'),(282,4,28,'2026-05-01','2026-05-04','2026-06-26','En formación'),(283,10,28,'2026-05-02','2026-05-04','2026-06-26','En formación'),(284,11,28,'2026-05-02','2026-05-04','2026-06-26','En formación'),(285,16,28,'2026-05-03','2026-05-04','2026-06-26','En formación'),(286,21,28,'2026-05-04','2026-05-04','2026-06-26','En formación'),(287,22,28,'2026-05-04','2026-05-04','2026-06-26','En formación'),(288,28,28,'2026-05-04','2026-05-04','2026-06-26','En formación'),(289,29,28,'2026-05-04','2026-05-04','2026-06-26','En formación'),(290,30,29,'2026-05-02','2026-05-11','2026-07-03','En formación'),(291,31,29,'2026-05-05','2026-05-11','2026-07-03','En formación'),(292,36,29,'2026-05-08','2026-05-11','2026-07-03','En formación'),(293,37,29,'2026-05-10','2026-05-11','2026-07-03','En formación'),(294,46,29,'2026-05-10','2026-05-11','2026-07-03','En formación'),(295,47,29,'2026-05-11','2026-05-11','2026-07-03','En formación'),(296,48,29,'2026-05-11','2026-05-11','2026-07-03','En formación'),(297,49,29,'2026-05-11','2026-05-11','2026-07-03','En formación'),(298,55,30,'2026-05-10','2026-05-18','2026-07-10','En formación'),(299,56,30,'2026-05-12','2026-05-18','2026-07-10','En formación'),(300,57,30,'2026-05-14','2026-05-18','2026-07-10','En formación'),(301,70,30,'2026-05-15','2026-05-18','2026-07-10','En formación'),(302,71,30,'2026-05-16','2026-05-18','2026-07-10','En formación'),(303,72,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(304,73,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(305,74,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(306,75,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(307,10,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(308,11,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(309,16,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(310,21,30,'2026-05-18','2026-05-18','2026-07-10','En formación'),(311,5,31,'2026-05-15','2026-06-01','2026-07-15','Diferido'),(312,18,31,'2026-05-15','2026-06-01','2026-07-15','Diferido'),(313,25,31,'2026-05-16','2026-06-01','2026-07-15','Diferido'),(314,38,31,'2026-05-18','2026-06-01','2026-07-15','Diferido'),(315,42,31,'2026-05-20','2026-06-01','2026-07-15','Diferido'),(316,58,31,'2026-05-20','2026-06-01','2026-07-15','Diferido'),(317,3,31,'2026-05-20','2026-06-01','2026-07-15','Diferido'),(318,6,31,'2026-05-20','2026-06-01','2026-07-15','Diferido'),(319,13,32,'2026-05-20','2026-06-08','2026-07-20','Asignado'),(320,14,32,'2026-05-20','2026-06-08','2026-07-20','Asignado'),(321,17,32,'2026-05-20','2026-06-08','2026-07-20','Asignado'),(322,19,32,'2026-05-20','2026-06-08','2026-07-20','Asignado'),(323,24,32,'2026-05-21','2026-06-08','2026-07-20','Asignado'),(324,26,32,'2026-05-21','2026-06-08','2026-07-20','Asignado'),(325,33,32,'2026-05-22','2026-06-08','2026-07-20','Asignado'),(326,34,32,'2026-05-22','2026-06-08','2026-07-20','Asignado'),(327,35,32,'2026-05-23','2026-06-08','2026-07-20','Asignado'),(328,39,32,'2026-05-23','2026-06-08','2026-07-20','Asignado'),(329,63,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(330,88,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(331,38,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(332,52,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(333,64,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(334,89,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(335,65,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(336,25,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(337,66,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(338,90,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(339,8,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(340,51,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(341,67,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(342,44,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(343,68,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(344,27,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(345,20,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(346,69,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(347,18,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(348,54,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(349,32,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(350,41,34,'2026-06-18','2026-06-29','2026-07-29','Asignado'),(351,55,34,'2026-06-18','2026-06-29','2026-07-29','Asignado');
/*!40000 ALTER TABLE `tb_participante_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_ppl`
--

DROP TABLE IF EXISTS `tb_ppl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ppl` (
  `id_ppl` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ppl` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ppl`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_ppl`
--

LOCK TABLES `tb_ppl` WRITE;
/*!40000 ALTER TABLE `tb_ppl` DISABLE KEYS */;
INSERT INTO `tb_ppl` VALUES (1,'ALBAÑIL'),(2,'ANALISTA DE SISTEMA'),(3,'ARTESANO CONSTRUCTOR DE EDIFICACIONES DE TIERRA'),(4,'ASISTENTE ADMINISTRATIVO'),(5,'ASISTENTE DE COMUNICACIÓN SOCIAL DIGITAL'),(6,'ASISTENTE DE CONTABILIDAD'),(7,'ASISTENTE DE GUIA DE TURISMO DE AVENTURA DE BAJA Y MEDIA MONTAÑA'),(8,'ASISTENTE DE NUTRICIÓN Y DIETÉTICA'),(9,'ASISTENTE DE RECREACIÓN'),(10,'AUXILIAR DE PROTOCOLO'),(11,'AUXILIAR DE RECREACIÓN'),(12,'AVICULTORES'),(13,'AYUDANTE DE ALBAÑIL'),(14,'AYUDANTE DE ARTESANO CONSTRUCTOR DE EDIFICACIONES DE TIERRA'),(15,'AYUDANTE DE COCINA'),(16,'AYUDANTE DE MECÁNICA AUTOMOTRIZ'),(17,'AYUDANTE DE SERVICIOS DE POZOS PETROLEROS'),(18,'BARBERO(A)'),(19,'BARTENDER'),(20,'CARPINTERO'),(21,'CERRAJERO'),(22,'COCINERO (A)'),(23,'CONFECCIONISTA DE ROPA INFANTIL'),(24,'CONFECCIONISTA DE UNIFORMES'),(25,'CRIADOR DE GANADO OVINO Y CAPRINO'),(26,'CRIADORES DE GANADO BOVINO'),(27,'CRIADORES DE GANADO PORCINO'),(28,'CULTIVADOR DE CACAO'),(29,'CULTOR DE CACAO'),(30,'CUÑERO DE SERVICIOS DE POZOS PETROLEROS'),(31,'DESARROLLADOR DE SITIO WEB'),(32,'DIAGRAMADOR(A)'),(33,'DISEÑADOR DE CALZADO'),(34,'DISEÑADOR DE SITIO WEB'),(35,'DISTRIBUIDOR DE GAS LICUADO DE PETRÓLEO'),(36,'EDITORIALISTA'),(37,'ELABORADOR DE BOLSOS TEJIDOS'),(38,'ELABORADOR DE CHINCHORROS'),(39,'ELABORADOR DE COJINES'),(40,'ELABORADOR DE HAMACAS'),(41,'ELABORADOR DE INSTRUMENTOS MUSICALES DE CUERDA'),(42,'ELECTRICISTA AUTOMOTRIZ'),(43,'ELECTRICISTA DE MANTENIMIENTO DEL SISTEMA DE ALUMBRADO PÚBLICO'),(44,'ELECTRICISTA EN REDES DE DISTRIBUCIÓN'),(45,'EMPRENDEDOR'),(46,'EMPRENDEDOR DIGITAL'),(47,'ENCUELLADOR DE SERVICIOS DE POZOS PETROLEROS'),(48,'ENSAMBLADOR DE PRODUCTOS ELECTRÓNICOS'),(49,'ENTRENADOR DEPORTIVO'),(50,'ESTETICISTA'),(51,'FABRICADOR DE CALZADO'),(52,'FOTÓGRAFO (A)'),(53,'GESTOR EMPRESARIAL DE TALENTO HUMANO'),(54,'GUIA CEREMONIAL'),(55,'GUIA DE TURISMO DE AVENTURA EN BAJA Y MEDIA MONTAÑA'),(56,'GUIA PROTOCOLO'),(57,'HERRERO'),(58,'INSPECTOR DE SEGURIDAD Y SALUD OCUPACIONAL'),(59,'INSTALADOR DE CONDUCTOS PARA AIRE ACONDICIONADO'),(60,'INSTALADOR DE EQUIPOS DE REFRIGERACIÓN Y AIRE ACONDICIONADO'),(61,'INSTALADOR DE FIBRA ÓPTICA'),(62,'INSTALADOR DE LÍNEAS DE ENERGÍA ELÉCTRICA AÉREAS EN REDES DE DISTRIBUCIÓN'),(63,'INSTALADOR DE MATERIALES DE IMPERMEABILIZACIÓN EN EDIFICACIONES'),(64,'INSTALADOR DE REDES Y LÍNEAS DE TELECOMUNICACIONES'),(65,'INSTALADOR DE REVESTIMIENTOS: PISOS, PAREDES Y TECHOS'),(66,'JARDINERO'),(67,'MAESTRO ARTESANO DE EDIFICACIONES DE TIERRA'),(68,'MANICURISTA/PEDICURISTA'),(69,'MECÁNICO DE MOTORES A GASOLINA'),(70,'MECÁNICO DE REFRIGERACIÓN Y CLIMATIZACIÓN'),(71,'MECÁNICO DE TRANSMISIÓN AUTOMOTRIZ'),(72,'MECÁNICO DIRECCIÓN Y FRENOS DE AUTOMÓVILES'),(73,'MECÁNICO MAQUINARIA TEXTIL'),(74,'MECÁNICO OPERADOR DE MONTACARGAS'),(75,'MESONERO/AZAFATA'),(76,'MONTADOR DE CALZADO'),(77,'MONTADOR PREFABRICADOS TABIQUERÍA LIGERA'),(78,'OBRERO DE MANTENIMIENTO DE ESPACIOS PÚBLICOS'),(79,'OPERADOR DE CALDERAS'),(80,'OPERADOR DE CARGADOR FRONTAL'),(81,'OPERADOR DE GRANJAS INTEGRALES'),(82,'OPERADOR DE MÁQUINA MOLIENDA/CEREALES'),(83,'OPERADOR DE MAQUINARIAS FIJAS'),(84,'OPERADOR DE MÁQUINAS DE CONFECCIÓN'),(85,'OPERADOR DE MÁQUINAS HERRAMIENTAS'),(86,'OPERADOR DE PLANTA DE GAS LICUADO DE PETRÓLEO'),(87,'ORIENTADOR(A) FAMILIAR Y GESTACIÓN'),(88,'PANADERO'),(89,'PANTRISTA'),(90,'PARRILLERO (A)'),(91,'PASTELERO'),(92,'PATRONISTA PRENDAS DE VESTIR'),(93,'PELUQUERO(A)'),(94,'PINTOR DE CONSTRUCCIÓN'),(95,'PLOMERO'),(96,'PROCESADOR DE CACAO'),(97,'PRODUCTOR AGRÍCOLA'),(98,'PRODUCTOR AGROPECUARIO'),(99,'PRODUCTOR DE CONTENIDO DIGITAL'),(100,'PRODUCTOR(A) AGROECOLÓGICO'),(101,'PROMOTOR CULTURAL'),(102,'PROMOTOR DE RECREACIÓN DEPORTIVA'),(103,'PROMOTOR DE RECREACIÓN LABORAL'),(104,'PROMOTOR RECREATIVO'),(105,'PROMOTOR(A) DE SALUD COMUNITARIA'),(106,'PROMOTOR(A) DE SALUD COMUNITARIA: PARTO HUMANIZADO'),(107,'RECEPCIONISTA DE HOTEL'),(108,'REDACTOR (A)'),(109,'REFORESTADOR DE BOSQUES'),(110,'REPARADOR DE MOTORES DIÉSEL'),(111,'REPORTERO (A) GRÁFICO'),(112,'REPORTERO (A) GRÁFICO DIGITAL'),(113,'REPOSTERO'),(114,'RESTAURADOR, MUEBLES DE MADERA'),(115,'RESTAURADOR, Y MANTENIMIENTO DE INSTRUMENTOS MUSICALES DE CUERDA'),(116,'SECRETARIA(O) ADMINISTRATIVA (O)'),(117,'SOLDADOR (A)'),(118,'SOLDADOR (A) ARCO ELÉCTRICO'),(119,'SOLDADOR (A) DE SOLDADURAS ESPECIALES'),(120,'SUPERVISOR DE CONTROL DE CALIDAD EN LA PRODUCCIÓN'),(121,'TÉCNICO EN DEMOLICIÓN Y DESACTIVACIÓN DE BOMBAS Y ARTEFACTOS EXPLOSIVOS'),(122,'TÉCNICO EN FABRICACIÓN DE MUEBLES'),(123,'TÉCNICO EN SOPORTE Y MANTENIMIENTO DE TECNOLOGÍAS DE INFORMACIÓN'),(124,'TÉCNICOS EN ADMINISTRACIÓN DE UNIDADES MILITARES BÁSICAS'),(127,'INSTALADOR DE REVESTIMIENTOS PISOS PAREDES Y TECHOS'),(128,'PRODUCTOR DE CONTENIDO DIGITAL: ADMINISTRACIÓN DE REDES SOCIALES, ALFABETIZACIÓN TECNOLÓGICA, COMUNI'),(129,'RESTAURADOR MUEBLES DE MADERA'),(130,'RESTAURADOR Y MANTENIMIENTO DE INSTRUMENTOS MUSICALES DE CUERDA'),(131,'SECRETARIA(O) ADMINISTRATIVA(O)');
/*!40000 ALTER TABLE `tb_ppl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_ppl_nombrecurso`
--

DROP TABLE IF EXISTS `tb_ppl_nombrecurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ppl_nombrecurso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ppl` int(11) NOT NULL,
  `id_nombrecurso` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ppl_nombrecurso` (`id_ppl`,`id_nombrecurso`),
  KEY `idx_ppl` (`id_ppl`),
  KEY `idx_nombrecurso` (`id_nombrecurso`)
) ENGINE=InnoDB AUTO_INCREMENT=1192 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_ppl_nombrecurso`
--

LOCK TABLES `tb_ppl_nombrecurso` WRITE;
/*!40000 ALTER TABLE `tb_ppl_nombrecurso` DISABLE KEYS */;
INSERT INTO `tb_ppl_nombrecurso` VALUES (31,1,2),(48,1,3),(37,1,5),(39,1,6),(21,1,8),(23,1,24),(47,1,57),(22,1,66),(24,1,67),(25,1,68),(32,1,73),(33,1,74),(34,1,75),(35,1,76),(36,1,77),(40,1,81),(41,1,82),(42,1,83),(43,1,84),(44,1,85),(45,1,86),(46,1,87),(49,1,90),(50,1,91),(64,2,3),(1,2,5),(2,2,49),(3,2,50),(4,2,51),(5,2,52),(6,2,53),(7,2,54),(8,2,55),(9,2,56),(52,2,93),(53,2,94),(56,2,97),(57,2,98),(58,2,99),(60,2,101),(61,2,102),(62,2,103),(63,2,104),(65,2,105),(66,2,106),(67,3,2),(75,3,3),(26,3,5),(27,3,6),(72,3,8),(74,3,57),(28,3,69),(29,3,70),(30,3,71),(73,3,87),(68,3,107),(69,3,108),(70,3,109),(71,3,110),(76,3,111),(79,4,2),(14,4,30),(10,4,57),(11,4,58),(12,4,59),(13,4,60),(15,4,61),(89,4,87),(86,4,101),(77,4,112),(78,4,113),(80,4,114),(81,4,115),(82,4,116),(83,4,117),(84,4,118),(85,4,119),(87,4,120),(88,4,121),(16,5,6),(17,5,62),(18,5,63),(19,5,64),(20,5,65),(95,5,101),(92,5,116),(90,5,122),(91,5,123),(93,5,124),(94,5,125),(96,5,126),(97,5,127),(98,5,128),(99,5,129),(109,6,5),(105,6,9),(100,6,49),(101,6,130),(102,6,131),(103,6,132),(104,6,133),(106,6,134),(107,6,135),(108,6,136),(110,6,137),(111,6,138),(112,6,139),(113,6,140),(114,7,2),(118,7,123),(115,7,141),(116,7,142),(117,7,143),(119,7,144),(127,8,3),(125,8,6),(123,8,8),(120,8,131),(121,8,145),(122,8,146),(124,8,147),(126,8,148),(128,8,149),(129,8,150),(130,8,151),(137,9,3),(134,9,6),(132,9,9),(136,9,57),(135,9,102),(131,9,131),(133,9,145),(138,9,152),(145,10,3),(142,10,6),(139,10,131),(141,10,145),(143,10,148),(147,10,149),(148,10,150),(140,10,153),(144,10,154),(146,10,155),(149,11,2),(156,11,3),(153,11,6),(151,11,9),(155,11,57),(154,11,102),(150,11,131),(152,11,145),(157,11,152),(158,12,2),(165,12,3),(163,12,5),(164,12,8),(159,12,156),(160,12,157),(161,12,158),(162,12,159),(166,13,2),(172,13,3),(168,13,5),(169,13,6),(171,13,57),(167,13,76),(170,13,87),(173,13,90),(174,13,91),(175,14,2),(180,14,3),(177,14,6),(176,14,8),(179,14,57),(178,14,87),(181,14,90),(182,14,91),(183,14,111),(193,15,3),(191,15,5),(188,15,26),(184,15,49),(192,15,102),(185,15,131),(190,15,146),(186,15,160),(187,15,161),(189,15,162),(194,15,163),(195,16,2),(201,16,3),(198,16,5),(197,16,9),(196,16,164),(199,16,165),(200,16,166),(202,16,167),(203,16,168),(204,16,169),(206,17,5),(207,17,101),(205,17,145),(208,17,170),(209,17,171),(210,17,172),(223,18,3),(221,18,57),(219,18,126),(216,18,131),(211,18,173),(212,18,174),(213,18,175),(214,18,176),(215,18,177),(217,18,178),(218,18,179),(220,18,180),(222,18,181),(224,18,182),(230,19,3),(228,19,6),(225,19,131),(227,19,145),(232,19,149),(226,19,153),(229,19,154),(231,19,155),(233,20,2),(241,20,3),(236,20,5),(238,20,8),(240,20,87),(234,20,183),(235,20,184),(237,20,185),(239,20,186),(242,21,2),(245,21,3),(244,21,5),(243,21,187),(261,22,3),(258,22,5),(254,22,26),(246,22,49),(260,22,102),(249,22,131),(257,22,146),(251,22,160),(253,22,161),(255,22,162),(262,22,163),(247,22,188),(248,22,189),(250,22,190),(252,22,191),(256,22,192),(259,22,193),(263,22,194),(273,23,3),(266,23,5),(270,23,102),(264,23,195),(265,23,196),(267,23,197),(268,23,198),(269,23,199),(271,23,200),(272,23,201),(284,24,3),(277,24,5),(281,24,102),(276,24,196),(278,24,197),(279,24,198),(280,24,199),(282,24,200),(283,24,201),(274,24,202),(275,24,203),(285,25,2),(292,25,3),(290,25,5),(291,25,8),(286,25,156),(287,25,157),(289,25,159),(288,25,204),(293,26,2),(300,26,3),(298,26,5),(299,26,8),(294,26,156),(295,26,157),(297,26,159),(296,26,205),(301,27,2),(308,27,3),(306,27,5),(307,27,8),(302,27,156),(303,27,157),(305,27,159),(304,27,206),(309,28,2),(318,28,3),(316,28,5),(310,28,207),(311,28,208),(312,28,209),(313,28,210),(314,28,211),(315,28,212),(317,28,213),(327,29,3),(326,29,5),(323,29,26),(322,29,42),(320,29,43),(325,29,119),(324,29,210),(319,29,214),(321,29,215),(328,29,216),(329,30,217),(330,30,218),(331,30,219),(332,30,220),(334,31,2),(338,31,8),(333,31,49),(339,31,102),(335,31,131),(337,31,145),(336,31,221),(340,31,222),(344,32,1),(346,32,101),(345,32,124),(347,32,126),(348,32,129),(343,32,145),(341,32,223),(342,32,224),(349,33,2),(358,33,5),(359,33,8),(350,33,157),(351,33,225),(352,33,226),(353,33,227),(354,33,228),(355,33,229),(356,33,230),(357,33,231),(361,34,2),(367,34,8),(360,34,49),(368,34,102),(362,34,131),(365,34,145),(363,34,232),(364,34,233),(366,34,234),(369,34,235),(370,34,236),(371,34,237),(372,34,238),(373,34,239),(374,35,2),(382,35,6),(383,35,87),(375,35,131),(376,35,240),(377,35,241),(378,35,242),(379,35,243),(380,35,244),(381,35,245),(384,35,246),(389,36,1),(391,36,101),(390,36,124),(392,36,126),(393,36,129),(388,36,145),(386,36,223),(387,36,224),(385,36,247),(398,37,3),(397,37,5),(396,37,157),(394,37,248),(395,37,249),(402,38,3),(401,38,87),(399,38,250),(400,38,251),(408,39,3),(406,39,5),(407,39,87),(405,39,157),(403,39,248),(404,39,252),(413,40,3),(411,40,5),(412,40,87),(409,40,250),(410,40,253),(414,41,2),(422,41,3),(421,41,5),(415,41,254),(416,41,255),(417,41,256),(418,41,257),(419,41,258),(420,41,259),(423,42,2),(428,42,3),(426,42,5),(425,42,9),(424,42,164),(427,42,165),(429,42,167),(431,42,169),(430,42,260),(436,43,5),(438,43,87),(432,43,164),(433,43,261),(434,43,262),(435,43,263),(437,43,264),(444,44,5),(446,44,87),(439,44,164),(441,44,261),(442,44,262),(443,44,263),(445,44,264),(440,44,265),(453,45,57),(451,45,137),(448,45,157),(447,45,248),(449,45,266),(450,45,267),(452,45,268),(454,45,269),(460,46,9),(455,46,49),(465,46,57),(456,46,157),(464,46,186),(458,46,266),(466,46,269),(457,46,270),(459,46,271),(461,46,272),(462,46,273),(463,46,274),(467,47,217),(468,47,218),(469,47,219),(474,47,220),(470,47,275),(471,47,276),(472,47,277),(473,47,278),(484,48,3),(482,48,5),(483,48,87),(475,48,279),(476,48,280),(477,48,281),(478,48,282),(479,48,283),(480,48,284),(481,48,285),(488,49,1),(489,49,6),(487,49,9),(485,49,131),(486,49,266),(498,50,3),(497,50,57),(496,50,126),(490,50,131),(491,50,286),(492,50,287),(493,50,288),(494,50,289),(495,50,290),(499,51,2),(505,51,3),(503,51,5),(504,51,8),(500,51,157),(501,51,225),(502,51,226),(509,52,1),(506,52,49),(511,52,101),(507,52,122),(512,52,126),(513,52,128),(514,52,129),(508,52,145),(510,52,291),(524,53,6),(519,53,9),(525,53,87),(515,53,112),(522,53,120),(523,53,126),(516,53,131),(517,53,250),(518,53,292),(520,53,293),(521,53,294),(526,54,2),(535,54,57),(529,54,115),(530,54,117),(531,54,119),(532,54,120),(534,54,148),(528,54,250),(527,54,295),(533,54,296),(536,55,2),(538,55,123),(537,55,297),(539,55,298),(540,55,299),(541,55,300),(542,55,301),(543,56,2),(551,56,57),(545,56,115),(546,56,117),(547,56,119),(548,56,120),(550,56,148),(544,56,250),(549,56,296),(552,57,2),(560,57,3),(556,57,5),(557,57,6),(559,57,57),(554,57,74),(558,57,87),(553,57,302),(555,57,303),(562,58,1),(569,58,3),(561,58,4),(563,58,5),(564,58,6),(567,58,57),(566,58,87),(565,58,304),(568,58,305),(570,59,2),(574,59,3),(572,59,5),(573,59,87),(571,59,131),(580,60,5),(582,60,6),(581,60,8),(585,60,57),(575,60,157),(583,60,186),(576,60,306),(577,60,307),(578,60,308),(579,60,309),(584,60,310),(586,60,311),(588,61,2),(598,61,3),(593,61,5),(595,61,6),(594,61,38),(591,61,39),(592,61,41),(587,61,49),(597,61,57),(589,61,312),(590,61,313),(596,61,314),(604,62,5),(606,62,87),(599,62,164),(601,62,261),(602,62,262),(603,62,263),(605,62,264),(600,62,284),(607,63,2),(614,63,3),(610,63,5),(611,63,6),(613,63,57),(608,63,74),(612,63,87),(609,63,315),(616,64,2),(623,64,3),(621,64,6),(615,64,49),(620,64,126),(617,64,131),(618,64,316),(619,64,317),(622,64,318),(624,64,319),(634,66,2),(640,66,3),(637,66,6),(639,66,57),(638,66,87),(635,66,321),(636,66,322),(641,67,2),(652,67,3),(647,67,5),(649,67,6),(648,67,8),(651,67,57),(650,67,87),(643,67,107),(644,67,108),(645,67,109),(646,67,110),(653,67,111),(642,67,323),(659,68,3),(658,68,57),(656,68,126),(654,68,131),(655,68,324),(657,68,325),(660,68,326),(661,69,2),(665,69,3),(664,69,5),(663,69,9),(662,69,164),(667,69,168),(666,69,327),(674,70,5),(678,70,57),(669,70,97),(676,70,102),(679,70,269),(670,70,306),(671,70,307),(672,70,308),(673,70,309),(677,70,310),(681,70,311),(668,70,328),(675,70,329),(680,70,330),(682,70,331),(683,71,2),(688,71,3),(686,71,5),(685,71,9),(684,71,164),(687,71,165),(690,71,168),(693,71,169),(692,71,260),(689,71,327),(691,71,332),(694,72,2),(699,72,3),(697,72,5),(696,72,9),(695,72,164),(698,72,165),(702,72,168),(701,72,327),(700,72,333),(703,72,334),(709,73,3),(704,73,5),(707,73,102),(705,73,197),(706,73,198),(708,73,201),(713,74,3),(710,74,5),(711,74,87),(712,74,335),(718,75,3),(717,75,6),(715,75,145),(716,75,147),(719,75,149),(720,75,150),(714,75,153),(721,75,163),(722,76,2),(726,76,3),(724,76,5),(725,76,8),(723,76,157),(727,77,2),(735,77,3),(730,77,5),(732,77,6),(731,77,8),(734,77,57),(728,77,74),(733,77,87),(729,77,336),(736,78,2),(746,78,3),(741,78,6),(744,78,57),(738,78,77),(743,78,87),(737,78,302),(745,78,320),(739,78,321),(740,78,322),(742,78,337),(747,78,338),(748,79,2),(755,79,5),(752,79,284),(749,79,339),(750,79,340),(751,79,341),(753,79,342),(754,79,343),(756,79,344),(757,79,345),(758,79,346),(759,80,2),(764,80,3),(762,80,5),(760,80,9),(763,80,87),(761,80,347),(765,80,348),(766,80,349),(767,81,2),(778,81,3),(776,81,5),(777,81,8),(768,81,156),(769,81,157),(775,81,159),(770,81,204),(774,81,205),(771,81,206),(772,81,350),(773,81,351),(796,82,3),(794,82,87),(790,82,123),(787,82,131),(795,82,318),(786,82,354),(788,82,355),(789,82,356),(791,82,357),(792,82,358),(793,82,359),(779,83,2),(785,83,5),(783,83,284),(781,83,340),(782,83,341),(780,83,352),(784,83,353),(802,84,3),(797,84,5),(800,84,102),(798,84,197),(799,84,198),(801,84,200),(811,85,3),(808,85,5),(810,85,101),(805,85,187),(806,85,284),(803,85,360),(804,85,361),(807,85,362),(809,85,363),(812,85,364),(813,86,2),(822,86,6),(823,86,87),(814,86,131),(816,86,145),(815,86,241),(819,86,242),(820,86,243),(821,86,244),(825,86,246),(817,86,365),(818,86,366),(824,86,367),(830,87,6),(829,87,9),(827,87,131),(832,87,304),(826,87,368),(828,87,369),(831,87,370),(833,87,371),(834,87,372),(835,87,373),(842,88,8),(846,88,87),(845,88,102),(837,88,131),(847,88,269),(850,88,373),(836,88,374),(838,88,375),(839,88,376),(840,88,377),(841,88,378),(843,88,379),(844,88,380),(848,88,381),(849,88,382),(858,89,3),(855,89,5),(854,89,26),(851,89,49),(857,89,102),(852,89,131),(859,89,163),(853,89,191),(856,89,193),(867,90,3),(866,90,5),(862,90,26),(860,90,131),(865,90,146),(863,90,162),(861,90,190),(868,90,194),(864,90,383),(873,91,8),(876,91,87),(875,91,102),(869,91,131),(877,91,269),(879,91,373),(870,91,375),(871,91,377),(872,91,378),(878,91,381),(874,91,384),(885,92,3),(881,92,5),(884,92,102),(880,92,196),(882,92,198),(883,92,199),(896,93,3),(894,93,57),(891,93,126),(888,93,131),(887,93,177),(889,93,178),(890,93,179),(893,93,180),(895,93,181),(897,93,182),(886,93,385),(892,93,386),(898,94,2),(905,94,3),(900,94,5),(901,94,6),(904,94,57),(899,94,74),(903,94,87),(902,94,337),(906,95,2),(915,95,3),(908,95,5),(910,95,6),(909,95,8),(914,95,57),(907,95,74),(911,95,83),(912,95,86),(913,95,87),(919,96,5),(917,96,26),(916,96,207),(918,96,210),(920,96,213),(921,96,387),(937,97,2),(946,97,3),(944,97,5),(945,97,8),(938,97,156),(939,97,157),(943,97,159),(941,97,350),(942,97,351),(940,97,388),(922,98,2),(936,98,3),(934,98,5),(935,98,8),(923,98,156),(924,98,157),(925,98,158),(933,98,159),(926,98,204),(932,98,205),(927,98,206),(929,98,350),(930,98,351),(928,98,388),(931,98,389),(950,100,2),(957,100,5),(958,100,8),(960,100,87),(953,100,97),(959,100,102),(951,100,157),(952,100,392),(954,100,393),(955,100,394),(956,100,395),(961,100,396),(962,100,397),(963,100,398),(969,101,3),(966,101,6),(968,101,57),(967,101,102),(964,101,131),(965,101,157),(975,102,3),(972,102,6),(974,102,57),(973,102,102),(970,102,131),(971,102,157),(982,103,3),(979,103,6),(977,103,9),(981,103,57),(980,103,102),(976,103,131),(978,103,145),(990,104,3),(987,104,6),(985,104,9),(989,104,57),(988,104,102),(983,104,131),(986,104,145),(984,104,399),(996,105,6),(995,105,9),(993,105,131),(992,105,368),(997,105,370),(999,105,372),(1000,105,373),(991,105,400),(994,105,401),(998,105,402),(1007,106,6),(1006,106,9),(1003,106,131),(1002,106,368),(1004,106,369),(1008,106,370),(1009,106,371),(1011,106,372),(1012,106,373),(1001,106,400),(1005,106,401),(1010,106,402),(1019,107,1),(1028,107,3),(1021,107,5),(1026,107,6),(1015,107,9),(1022,107,101),(1023,107,120),(1024,107,121),(1025,107,126),(1013,107,131),(1027,107,138),(1014,107,250),(1016,107,293),(1017,107,403),(1018,107,404),(1020,107,405),(1029,107,406),(1035,108,1),(1038,108,6),(1030,108,49),(1036,108,101),(1033,108,116),(1031,108,123),(1037,108,126),(1039,108,128),(1040,108,129),(1034,108,145),(1032,108,224),(1041,109,2),(1050,109,3),(1046,109,5),(1042,109,131),(1048,109,318),(1043,109,407),(1044,109,408),(1045,109,409),(1047,109,410),(1049,109,411),(1051,110,2),(1064,110,3),(1053,110,5),(1054,110,126),(1055,110,335),(1052,110,412),(1056,110,413),(1057,110,414),(1058,110,415),(1059,110,416),(1060,110,417),(1061,110,418),(1062,110,419),(1063,110,420),(1069,111,101),(1068,111,124),(1070,111,126),(1071,111,127),(1072,111,128),(1073,111,129),(1065,111,223),(1066,111,224),(1067,111,291),(1077,112,1),(1082,112,6),(1074,112,49),(1080,112,101),(1075,112,122),(1079,112,125),(1081,112,126),(1083,112,128),(1084,112,129),(1078,112,291),(1076,112,421),(1088,113,8),(1090,113,87),(1089,113,102),(1085,113,131),(1091,113,269),(1094,113,373),(1086,113,375),(1087,113,377),(1092,113,381),(1093,113,382),(1130,117,2),(1136,117,3),(1133,117,5),(1135,117,8),(1131,117,284),(1137,117,364),(1132,117,434),(1134,117,435),(1138,117,436),(1139,117,437),(1147,118,3),(1143,118,8),(1145,118,87),(1140,118,157),(1144,118,186),(1148,118,364),(1142,118,434),(1141,118,438),(1146,118,439),(1153,119,3),(1151,119,8),(1149,119,157),(1152,119,186),(1150,119,284),(1154,119,440),(1155,119,441),(1156,119,442),(1166,120,3),(1165,120,87),(1164,120,268),(1157,120,279),(1158,120,280),(1159,120,281),(1160,120,282),(1161,120,283),(1162,120,285),(1163,120,443),(1167,120,444),(1168,122,2),(1179,122,3),(1174,122,5),(1176,122,8),(1178,122,87),(1169,122,183),(1175,122,185),(1177,122,186),(1170,122,445),(1171,122,446),(1172,122,447),(1173,122,448),(1183,123,3),(1182,123,87),(1180,123,283),(1181,123,443),(1184,123,449),(1186,124,2),(1189,124,6),(1187,124,9),(1185,124,112),(1188,124,450),(1190,124,451),(1191,124,452),(625,127,2),(633,127,3),(628,127,5),(629,127,6),(631,127,57),(626,127,74),(627,127,77),(630,127,87),(632,127,320),(948,128,154),(947,128,390),(949,128,391),(1095,129,2),(1107,129,3),(1102,129,5),(1109,129,44),(1097,129,45),(1111,129,46),(1103,129,47),(1101,129,119),(1100,129,135),(1099,129,153),(1096,129,422),(1098,129,423),(1104,129,424),(1105,129,425),(1106,129,426),(1108,129,427),(1110,129,428),(1112,129,429),(1113,130,2),(1119,130,3),(1116,130,5),(1114,130,430),(1115,130,431),(1117,130,432),(1118,130,433),(1121,131,2),(1128,131,6),(1129,131,87),(1125,131,101),(1120,131,112),(1122,131,115),(1123,131,117),(1124,131,118),(1126,131,120),(1127,131,121);
/*!40000 ALTER TABLE `tb_ppl_nombrecurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_sectoreconomico`
--

DROP TABLE IF EXISTS `tb_sectoreconomico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_sectoreconomico` (
  `id_sectoreconomico` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_sectoreconomico` varchar(100) NOT NULL,
  PRIMARY KEY (`id_sectoreconomico`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_sectoreconomico`
--

LOCK TABLES `tb_sectoreconomico` WRITE;
/*!40000 ALTER TABLE `tb_sectoreconomico` DISABLE KEYS */;
INSERT INTO `tb_sectoreconomico` VALUES (1,'Comercio y Servicios'),(2,'Industria'),(3,'Investigación, Desarrollo, Innovación e Información');
/*!40000 ALTER TABLE `tb_sectoreconomico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_subtipo`
--

DROP TABLE IF EXISTS `tb_subtipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_subtipo` (
  `id_subtipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_subtipo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_subtipo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_subtipo`
--

LOCK TABLES `tb_subtipo` WRITE;
/*!40000 ALTER TABLE `tb_subtipo` DISABLE KEYS */;
INSERT INTO `tb_subtipo` VALUES (1,'ESPECÍFICA/TÉCNICA'),(2,'TRANSVERSAL/GENÉRICA'),(3,'BÁSICA/COMÚN');
/*!40000 ALTER TABLE `tb_subtipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_telefono`
--

DROP TABLE IF EXISTS `tb_telefono`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_telefono` (
  `id_telefono` int(11) NOT NULL AUTO_INCREMENT,
  `numero_de_telefono` bigint(20) NOT NULL,
  `tipo_de_telefono` enum('Móvil','Fijo') NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `id_participante` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_telefono`),
  KEY `fk_telefono_docente` (`id_docente`),
  KEY `fk_telefono_participante` (`id_participante`),
  KEY `fk_telefono_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_telefono`
--

LOCK TABLES `tb_telefono` WRITE;
/*!40000 ALTER TABLE `tb_telefono` DISABLE KEYS */;
INSERT INTO `tb_telefono` VALUES (1,4121111101,'Móvil',1,NULL,NULL),(2,4121111102,'Móvil',2,NULL,NULL),(3,4121111103,'Móvil',3,NULL,NULL),(4,4121111104,'Móvil',4,NULL,NULL),(5,4121111105,'Móvil',5,NULL,NULL),(6,4121111106,'Móvil',6,NULL,NULL),(7,4121111107,'Móvil',7,NULL,NULL),(8,4121111108,'Móvil',8,NULL,NULL),(9,4121111109,'Móvil',9,NULL,NULL),(10,4121111110,'Móvil',10,NULL,NULL),(11,4121111111,'Móvil',11,NULL,NULL),(12,4121111112,'Móvil',12,NULL,NULL),(13,4121111113,'Móvil',13,NULL,NULL),(14,4121111114,'Móvil',14,NULL,NULL),(15,4121111115,'Móvil',15,NULL,NULL),(16,4242000001,'Móvil',NULL,1,NULL),(17,4242000002,'Móvil',NULL,2,NULL),(18,4242000003,'Móvil',NULL,3,NULL),(19,4242000004,'Móvil',NULL,4,NULL),(20,4242000005,'Móvil',NULL,5,NULL),(21,4242000006,'Móvil',NULL,6,NULL),(22,4242000007,'Móvil',NULL,7,NULL),(23,4242000008,'Móvil',NULL,8,NULL),(24,4242000009,'Móvil',NULL,9,NULL),(25,4242000010,'Móvil',NULL,10,NULL),(26,4242000011,'Móvil',NULL,11,NULL),(27,4242000012,'Móvil',NULL,12,NULL),(28,4242000013,'Móvil',NULL,13,NULL),(29,4242000014,'Móvil',NULL,14,NULL),(30,4242000015,'Móvil',NULL,15,NULL),(31,4242000016,'Móvil',NULL,16,NULL),(32,4242000017,'Móvil',NULL,17,NULL),(33,4242000018,'Móvil',NULL,18,NULL),(34,4242000019,'Móvil',NULL,19,NULL),(35,4242000020,'Móvil',NULL,20,NULL),(36,4242000021,'Móvil',NULL,21,NULL),(37,4242000022,'Móvil',NULL,22,NULL),(38,4242000023,'Móvil',NULL,23,NULL),(39,4242000024,'Móvil',NULL,24,NULL),(40,4242000025,'Móvil',NULL,25,NULL),(41,4242000026,'Móvil',NULL,26,NULL),(42,4242000027,'Móvil',NULL,27,NULL),(43,4242000028,'Móvil',NULL,28,NULL),(44,4242000029,'Móvil',NULL,29,NULL),(45,4242000030,'Móvil',NULL,30,NULL),(46,4242000031,'Móvil',NULL,31,NULL),(47,4242000032,'Móvil',NULL,32,NULL),(48,4242000033,'Móvil',NULL,33,NULL),(49,4242000034,'Móvil',NULL,34,NULL),(50,4242000035,'Móvil',NULL,35,NULL),(51,4242000036,'Móvil',NULL,36,NULL),(52,4242000037,'Móvil',NULL,37,NULL),(53,4242000038,'Móvil',NULL,38,NULL),(54,4242000039,'Móvil',NULL,39,NULL),(55,4242000040,'Móvil',NULL,40,NULL),(56,4242000041,'Móvil',NULL,41,NULL),(57,4242000042,'Móvil',NULL,42,NULL),(58,4242000043,'Móvil',NULL,43,NULL),(59,4242000044,'Móvil',NULL,44,NULL),(60,4242000045,'Móvil',NULL,45,NULL),(61,4242000046,'Móvil',NULL,46,NULL),(62,4242000047,'Móvil',NULL,47,NULL),(63,4242000048,'Móvil',NULL,48,NULL),(64,4242000049,'Móvil',NULL,49,NULL),(65,4242000050,'Móvil',NULL,50,NULL),(66,4242000051,'Móvil',NULL,51,NULL),(67,4242000052,'Móvil',NULL,52,NULL),(68,4242000053,'Móvil',NULL,53,NULL),(69,4242000054,'Móvil',NULL,54,NULL),(70,4242000055,'Móvil',NULL,55,NULL),(71,4242000056,'Móvil',NULL,56,NULL),(72,4242000057,'Móvil',NULL,57,NULL),(73,4242000058,'Móvil',NULL,58,NULL),(74,4242000059,'Móvil',NULL,59,NULL),(75,4242000060,'Móvil',NULL,60,NULL),(76,4242000061,'Móvil',NULL,61,NULL),(77,4242000062,'Móvil',NULL,62,NULL),(78,4242000063,'Móvil',NULL,63,NULL),(79,4242000064,'Móvil',NULL,64,NULL),(80,4242000065,'Móvil',NULL,65,NULL),(81,4242000066,'Móvil',NULL,66,NULL),(82,4242000067,'Móvil',NULL,67,NULL),(83,4242000068,'Móvil',NULL,68,NULL),(84,4242000069,'Móvil',NULL,69,NULL),(85,4242000070,'Móvil',NULL,70,NULL),(86,4242000071,'Móvil',NULL,71,NULL),(87,4242000072,'Móvil',NULL,72,NULL),(88,4242000073,'Móvil',NULL,73,NULL),(89,4242000074,'Móvil',NULL,74,NULL),(90,4242000075,'Móvil',NULL,75,NULL),(91,4242000076,'Móvil',NULL,76,NULL),(92,4242000077,'Móvil',NULL,77,NULL),(93,4242000078,'Móvil',NULL,78,NULL),(94,4242000079,'Móvil',NULL,79,NULL),(95,4242000080,'Móvil',NULL,80,NULL),(96,4242000081,'Móvil',NULL,81,NULL),(97,4242000082,'Móvil',NULL,82,NULL),(98,4242000083,'Móvil',NULL,83,NULL),(99,4242000084,'Móvil',NULL,84,NULL),(100,4242000085,'Móvil',NULL,85,NULL),(101,4242000086,'Móvil',NULL,86,NULL),(102,4242000087,'Móvil',NULL,87,NULL),(103,4242000088,'Móvil',NULL,88,NULL),(104,4242000089,'Móvil',NULL,89,NULL),(105,4242000090,'Móvil',NULL,90,NULL);
/*!40000 ALTER TABLE `tb_telefono` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_usuario`
--

DROP TABLE IF EXISTS `tb_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(20) NOT NULL,
  `segundo_nombre` varchar(20) DEFAULT NULL,
  `primer_apellido` varchar(20) NOT NULL,
  `segundo_apellido` varchar(20) DEFAULT NULL,
  `nombre_usuario` varchar(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Administrador','Apoyo') NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado_usuario` enum('Activo','Inactivo') NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_usuario`
--

LOCK TABLES `tb_usuario` WRITE;
/*!40000 ALTER TABLE `tb_usuario` DISABLE KEYS */;
INSERT INTO `tb_usuario` VALUES (11,'Daniel',NULL,'Bringtown',NULL,'ADMIN','dbringtown1@gmail.com','$2y$10$4pmDGNsnYghS/NDh85/64OQn/cE8WOSw1Y/vympd2TpYqP6/DNg6O','Administrador','assets/img/avatar/avatar_680da3ed12fa2.jpg','Activo'),(13,'Jorge','Josmauro','SDDD','Silva','Gianmarino','dbringtown2@gmail.com','$2y$10$1OPhWnf4z2aHIJpXO5QOAeZ6RfN0lMx/VhsWFFNL04IaUWPY5uf0W','Apoyo','assets/img/avatar/default-user.png','Inactivo'),(14,'Jose',NULL,'Benitez',NULL,'Jbenitez','dbringtown12@gmail.com','$2y$10$1HNTuArHRCrmq9yMywGFAu8M7R65NL0PA8FnjoY8UuC.YtMKzXFHe','Administrador','assets/img/avatar/default-user.png','Activo'),(15,'Mary',NULL,'Serrada',NULL,'INCES2025','dbringtown13@gmail.com','$2y$10$z21kNN/cWNRTBgjugD6ij.4669Z5h5PWkhEYJMMvAwOe/KerCJ2MO','Apoyo','assets/img/avatar/default-user.png','Activo'),(16,'samuel ','','rivas','','samu','example@gmail.com','$2y$10$CniBbUjywGebCBl7q4XLMO612UxtLOd8kaHwF7wLYvaUnjF.x164S','Administrador','assets/img/avatar/1766934581_Gojo Satoru.jpeg','Activo'),(20,'samuel ','jose','rivas','rengel','usuario','examplee@gmail.com','$2y$10$TCZMLfqr/RTDf1QvVjxgI.Bf5yWPHY0qSvfMjhbrOTyKFRfgeiFOS','Apoyo','assets/img/avatar/default-user.png','Activo');
/*!40000 ALTER TABLE `tb_usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-21 12:37:51

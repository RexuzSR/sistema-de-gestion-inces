-- ============================================================
-- SCRIPT DE SEMBRADO DE DATOS MAESTRO (AGOSTO 2025 - MAYO 2026)
-- SISTEMA DE GESTIÓN INCES
-- ============================================================

-- Desactivar temporalmente la verificación de llaves foráneas para poder limpiar las tablas
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tablas de negocio para evitar duplicados y conflictos de IDs
TRUNCATE TABLE `tb_telefono`;
TRUNCATE TABLE `tb_participante_curso`;
TRUNCATE TABLE `tb_docente_curso`;
TRUNCATE TABLE `tb_historial`;
TRUNCATE TABLE `tb_metas`;
TRUNCATE TABLE `tb_curso`;
TRUNCATE TABLE `tb_docente`;
TRUNCATE TABLE `tb_participante`;

-- Activar de nuevo la verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. INSERTAR CURSO BASE (ID = 1)
-- ============================================================
INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `id_estatusCurso`) 
VALUES (1, 'Curso Base No Utilizable', '2025-08-01', 6);


-- ============================================================
-- 2. SEMBRAR DOCENTES (15 docentes realistas en los 4 estados)
-- Estados: Disponible, En formación, Asignado, Inactivo
-- Tipos: Fijo, Contratado, PDL, Voluntario
-- ============================================================
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
(11, 'Francisco', 'Javier', 'Vasquez', 'Delgado', 12345611, 'francisco.vasquez@inces.edu.ve', '1970-11-03', 55, 'Asignado', 'Masculino', 'Fijo'),
(12, 'Patricia', 'Alejandra', 'Castro', 'Vargas', 12345612, 'patricia.castro@inces.edu.ve', '1992-12-28', 33, 'Asignado', 'Femenino', 'Voluntario'),
(13, 'Miguel', 'Angel', 'Torres', 'Garcia', 12345613, 'miguel.torres@inces.edu.ve', '1981-04-14', 45, 'En formación', 'Masculino', 'Contratado'),
(14, 'Yolanda', 'Beatriz', 'Perez', 'Blanco', 12345614, 'yolanda.perez@inces.edu.ve', '1989-08-22', 36, 'En formación', 'Femenino', 'Voluntario'),
(15, 'Héctor', 'Jose', 'Guzman', 'Herrera', 12345615, 'hector.guzman@inces.edu.ve', '1972-10-10', 53, 'Inactivo', 'Masculino', 'Fijo');


-- ============================================================
-- 3. SEMBRAR PARTICIPANTES (90 participantes registrados progresivamente)
-- Progresión por mes de registro (fecha_registro):
-- 2025: Ago(6), Sep(7), Oct(8), Nov(9), Dic(9) = 39 en 2025
-- 2026: Ene(9), Feb(10), Mar(11), Abr(11), May(10) = 51 en 2026
-- Total = 90 participantes
-- ============================================================
INSERT INTO `tb_participante` (`id_participante`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `edad`, `correo`, `fecha_nacimiento`, `fecha_registro`, `estado`, `grado_institucion`, `genero`, `id_discapacidad`) VALUES
-- ── AGOSTO 2025 (6 registros) ──
(1, 'Diego', 'Alejandro', 'Romero', 'Silva', 32000001, 21, 'diego.romero@gmail.com', '2005-01-10', '2025-08-05', 'En formación', 'Bachillerato', 'Masculino', 1),
(2, 'Camila', 'Isabella', 'Rengel', 'Morillo', 32000002, 19, 'camila.rengel@gmail.com', '2007-02-14', '2025-08-08', 'En formación', 'Bachillerato', 'Femenino', 1),
(3, 'Mateo', 'Daniel', 'Rivas', 'Garcia', 32000003, 20, 'mateo.rivas@gmail.com', '2006-03-22', '2025-08-12', 'Disponible', 'Universidad', 'Masculino', 1),
(4, 'Valentina', 'Andrea', 'Sucre', 'Perez', 32000004, 25, 'valentina.sucre@gmail.com', '2001-04-05', '2025-08-18', 'En formación', 'Universidad', 'Femenino', 2),
(5, 'Santiago', 'Manuel', 'Santos', 'Flores', 32000005, 23, 'santiago.santos@gmail.com', '2003-05-12', '2025-08-22', 'Asignado', 'Bachillerato', 'Masculino', 1),
(6, 'Sebastian', 'Ignacio', 'Serrada', 'Castillo', 32000006, 22, 'seb.serrada@gmail.com', '2004-06-25', '2025-08-25', 'Disponible', 'Bachillerato', 'Masculino', 1),

-- ── SEPTIEMBRE 2025 (7 registros) ──
(7, 'Sofia', 'Alejandra', 'Morillo', 'Ortiz', 32000007, 24, 'sofia.morillo@gmail.com', '2002-07-30', '2025-09-02', 'Inactivo', 'Universidad', 'Femenino', 1),
(8, 'Daniel', 'David', 'Acosta', 'López', 32000008, 27, 'daniel.acosta@gmail.com', '1999-08-14', '2025-09-05', 'En sistema', 'Primaria', 'Masculino', 3),
(9, 'Isabella', 'Rosa', 'Mendoza', 'Flores', 32000009, 21, 'isabella.mendoza@gmail.com', '2005-09-09', '2025-09-12', 'Diferido', 'Bachillerato', 'Femenino', 1),
(10, 'Samuel', 'Jose', 'Torres', 'Ramirez', 32000010, 21, 'samuel.torres@gmail.com', '2004-10-27', '2025-09-15', 'En formación', 'Universidad', 'Masculino', 1),
(11, 'Gabriela', 'Teresa', 'Rojas', 'Medina', 32000011, 23, 'gabriela.rojas@gmail.com', '2003-11-12', '2025-09-18', 'En formación', 'Bachillerato', 'Femenino', 1),
(12, 'Lucas', 'Benjamin', 'Cabrera', 'Vargas', 32000012, 19, 'lucas.cabrera@gmail.com', '2007-12-05', '2025-09-22', 'Disponible', 'Bachillerato', 'Masculino', 4),
(13, 'Mariana', 'Patricia', 'Castillo', 'Guzman', 32000013, 26, 'mariana.castillo@gmail.com', '2000-01-22', '2025-09-26', 'Disponible', 'Universidad', 'Femenino', 1),

-- ── OCTUBRE 2025 (8 registros) ──
(14, 'Nicolas', 'Andres', 'Salazar', 'Moreno', 32000014, 28, 'nicolas.salazar@gmail.com', '1998-02-18', '2025-10-02', 'Disponible', 'Bachillerato', 'Masculino', 1),
(15, 'Victoria', 'Beatriz', 'Delgado', 'Ortega', 32000015, 30, 'victoria.delgado@gmail.com', '1996-03-15', '2025-10-05', 'Inactivo', 'Universidad', 'Femenino', 1),
(16, 'Benjamin', 'Edgar', 'Vargas', 'Guerrero', 32000016, 29, 'benjamin.vargas@gmail.com', '1997-04-20', '2025-10-08', 'En formación', 'Primaria', 'Masculino', 2),
(17, 'Daniela', 'Elizabeth', 'Ortiz', 'Cruz', 32000017, 33, 'daniela.ortiz@gmail.com', '1993-05-30', '2025-10-12', 'Disponible', 'Universidad', 'Femenino', 1),
(18, 'Felipe', 'Julio', 'Caceres', 'Herrera', 32000018, 35, 'felipe.caceres@gmail.com', '1991-06-14', '2025-10-15', 'Asignado', 'Bachillerato', 'Masculino', 1),
(19, 'Natalia', 'Carmen', 'Benitez', 'Gil', 32000019, 22, 'natalia.benitez@gmail.com', '2004-07-08', '2025-10-18', 'Disponible', 'Bachillerato', 'Femenino', 1),
(20, 'Emmanuel', 'Edwin', 'Ferrer', 'Fuentes', 32000020, 24, 'emmanuel.ferrer@gmail.com', '2002-08-25', '2025-10-22', 'En sistema', 'Bachillerato', 'Masculino', 1),
(21, 'Lucia', 'Isabel', 'Blanco', 'Perez', 32000021, 20, 'lucia.blanco@gmail.com', '2006-09-02', '2025-10-25', 'En formación', 'Bachillerato', 'Femenino', 1),

-- ── NOVIEMBRE 2025 (9 registros) ──
(22, 'Joaquin', 'Nelson', 'Ruiz', 'Alvarez', 32000022, 26, 'joaquin.ruiz@gmail.com', '2000-10-12', '2025-11-02', 'En formación', 'Universidad', 'Masculino', 1),
(23, 'Maria', 'Jose', 'Jimenez', 'Castro', 32000023, 22, 'maria.jimenez@gmail.com', '2004-11-19', '2025-11-05', 'Diferido', 'Universidad', 'Femenino', 3),
(24, 'Tomas', 'Franklin', 'Muñoz', 'Acosta', 32000024, 30, 'tomas.munoz@gmail.com', '1996-12-11', '2025-11-10', 'Disponible', 'Bachillerato', 'Masculino', 1),
(25, 'Catalina', 'Gabriela', 'Moreno', 'Torres', 32000025, 25, 'catalina.moreno@gmail.com', '2001-01-08', '2025-11-15', 'Asignado', 'Universidad', 'Femenino', 1),
(26, 'Agustin', 'Richard', 'Ortega', 'Flores', 32000026, 27, 'agustin.ortega@gmail.com', '1999-02-28', '2025-11-18', 'Disponible', 'Primaria', 'Masculino', 1),
(27, 'Emilia', 'Sofía', 'Guerrero', 'Rivas', 32000027, 21, 'emilia.guerrero@gmail.com', '2005-03-17', '2025-11-20', 'Inactivo', 'Bachillerato', 'Femenino', 1),
(28, 'Simon', 'Wilmer', 'Cruz', 'Mendoza', 32000028, 23, 'simon.cruz@gmail.com', '2003-04-15', '2025-11-22', 'En formación', 'Bachillerato', 'Masculino', 1),
(29, 'Antonella', 'Valentina', 'Herrera', 'Castillo', 32000029, 19, 'anto.herrera@gmail.com', '2007-05-24', '2025-11-25', 'En formación', 'Bachillerato', 'Femenino', 1),
(30, 'Maximiliano', 'Julio', 'Gil', 'Acosta', 32000030, 28, 'max.gil@gmail.com', '1998-06-03', '2025-11-28', 'En formación', 'Bachillerato', 'Masculino', 1),

-- ── DICIEMBRE 2025 (9 registros) ──
(31, 'Florencia', 'Rosa', 'Fuentes', 'Rojas', 32000031, 31, 'flor.fuentes@gmail.com', '1995-07-14', '2025-12-02', 'En formación', 'Universidad', 'Femenino', 1),
(32, 'Gael', 'Oscar', 'Acosta', 'Mendoza', 32000032, 23, 'gael.acosta@gmail.com', '2003-08-20', '2025-12-05', 'Diferido', 'Bachillerato', 'Masculino', 2),
(33, 'Martina', 'Teresa', 'Torres', 'Medina', 32000033, 22, 'martina.torres@gmail.com', '2004-09-18', '2025-12-08', 'Disponible', 'Universidad', 'Femenino', 1),
(34, 'Bastian', 'Cesar', 'Medina', 'Rivas', 32000034, 25, 'bastian.medina@gmail.com', '2001-10-02', '2025-12-12', 'Disponible', 'Bachillerato', 'Masculino', 1),
(35, 'Isidora', 'Laura', 'Castillo', 'Silva', 32000035, 20, 'isi.castillo@gmail.com', '2006-11-12', '2025-12-15', 'Disponible', 'Bachillerato', 'Femenino', 1),
(36, 'Gaspar', 'Franklin', 'Guzman', 'Rengel', 32000036, 21, 'gaspar.guzman@gmail.com', '2005-12-05', '2025-12-18', 'En formación', 'Bachillerato', 'Masculino', 1),
(37, 'Ignacia', 'Patricia', 'Moreno', 'Cumana', 32000037, 24, 'ignacia.moreno@gmail.com', '2002-01-30', '2025-12-22', 'En formación', 'Universidad', 'Femenino', 1),
(38, 'Alonso', 'Manuel', 'Ortega', 'Santos', 32000038, 29, 'alonso.ortega@gmail.com', '1997-02-22', '2025-12-26', 'Asignado', 'Bachillerato', 'Masculino', 1),
(39, 'Elena', 'Beatriz', 'Perez', 'Sucre', 32000039, 27, 'elena.perez@gmail.com', '1999-03-11', '2025-12-28', 'Disponible', 'Universidad', 'Femenino', 1),

-- ── ENERO 2026 (9 registros) ──
(40, 'Javier', 'Eduardo', 'Rodriguez', 'Serrada', 32000040, 32, 'javier.rodriguez@gmail.com', '1994-04-01', '2026-01-05', 'En sistema', 'Universidad', 'Masculino', 1),
(41, 'Isabel', 'Teresa', 'Martinez', 'Nolazco', 32000041, 35, 'isabel.martinez@gmail.com', '1991-05-19', '2026-01-08', 'Inactivo', 'Primaria', 'Femenino', 1),
(42, 'Mateo', 'Alexander', 'Rivas', 'Gonzales', 32000042, 19, 'mateo.alex@gmail.com', '2007-06-08', '2026-01-12', 'Asignado', 'Bachillerato', 'Masculino', 1),
(43, 'Sofia', 'Del Valle', 'Sucre', 'Martinez', 32000043, 21, 'sofia.sucre@gmail.com', '2005-07-15', '2026-01-15', 'Disponible', 'Bachillerato', 'Femenino', 1),
(44, 'Diego', 'Jose', 'Santos', 'Rengel', 32000044, 25, 'diego.santos@gmail.com', '2001-08-30', '2026-01-18', 'Diferido', 'Universidad', 'Masculino', 1),
(45, 'Valentina', 'Rosa', 'Serrada', 'Martinez', 32000045, 23, 'vale.serrada@gmail.com', '2003-09-12', '2026-01-20', 'Disponible', 'Bachillerato', 'Femenino', 4),
(46, 'Lucas', 'Manuel', 'Morillo', 'Perez', 32000046, 22, 'lucas.morillo@gmail.com', '2004-10-05', '2026-01-22', 'En formación', 'Bachillerato', 'Masculino', 1),
(47, 'Camila', 'Gabriela', 'Acosta', 'Flores', 32000047, 20, 'camila.acosta@gmail.com', '2006-11-25', '2026-01-25', 'En formación', 'Bachillerato', 'Femenino', 1),
(48, 'Sebastian', 'Eduardo', 'Mendoza', 'Torres', 32000048, 27, 'seb.mendoza@gmail.com', '1999-01-03', '2026-01-28', 'En formación', 'Universidad', 'Masculino', 1),

-- ── FEBRERO 2026 (10 registros) ──
(49, 'Isabella', 'Elena', 'Torres', 'Medina', 32000049, 19, 'isabella.torres@gmail.com', '2007-02-28', '2026-02-02', 'En formación', 'Bachillerato', 'Femenino', 1),
(50, 'Nicolas', 'Benjamín', 'Vargas', 'Vargas', 32000050, 31, 'nicolas.vargas@gmail.com', '1995-03-24', '2026-02-05', 'Inactivo', 'Bachillerato', 'Masculino', 1),
(51, 'Daniela', 'Fernanda', 'Paredes', 'Acosta', 32000051, 22, 'daniela.p@gmail.com', '2004-04-12', '2026-02-08', 'Disponible', 'Bachillerato', 'Femenino', 1),
(52, 'Andrés', 'Felipe', 'Velasquez', 'Salazar', 32000052, 28, 'andres.v@gmail.com', '1998-05-18', '2026-02-12', 'Disponible', 'Universidad', 'Masculino', 1),
(53, 'Patricia', 'Juliana', 'Molina', 'Blanco', 32000053, 35, 'patricia.m@gmail.com', '1991-06-30', '2026-02-15', 'En sistema', 'Primaria', 'Femenino', 1),
(54, 'Gabriel', 'Alejandro', 'Rojas', 'Castillo', 32000054, 20, 'gabriel.r@gmail.com', '2006-07-22', '2026-02-18', 'Disponible', 'Bachillerato', 'Masculino', 2),
(55, 'Isabel', 'Teresa', 'Romero', 'Guzman', 32000055, 23, 'isabel.r@gmail.com', '2003-08-14', '2026-02-20', 'En formación', 'Bachillerato', 'Femenino', 1),
(56, 'Julio', 'César', 'Salazar', 'Ortega', 32000056, 30, 'julio.s@gmail.com', '1996-09-05', '2026-02-22', 'En formación', 'Universidad', 'Masculino', 1),
(57, 'Laura', 'Valentina', 'Medina', 'Moreno', 32000057, 24, 'laura.m@gmail.com', '2002-10-18', '2026-02-25', 'En formación', 'Universidad', 'Femenino', 1),
(58, 'Oscar', 'David', 'Suarez', 'Delgado', 32000058, 29, 'oscar.s@gmail.com', '1997-11-25', '2026-02-28', 'Asignado', 'Bachillerato', 'Masculino', 1),

-- ── MARZO 2026 (11 registros) ──
(59, 'Paola', 'Alejandra', 'Flores', 'Vargas', 32000059, 21, 'paola.f@gmail.com', '2005-01-30', '2026-03-02', 'Disponible', 'Bachillerato', 'Femenino', 1),
(60, 'Ricardo', 'José', 'Castro', 'Guerrero', 32000060, 27, 'ricardo.c@gmail.com', '1999-02-14', '2026-03-05', 'Disponible', 'Universidad', 'Masculino', 3),
(61, 'Silvia', 'Carolina', 'Ortega', 'Benitez', 32000061, 26, 'silvia.o@gmail.com', '2000-03-19', '2026-03-08', 'Disponible', 'Universidad', 'Femenino', 1),
(62, 'Víctor', 'Hugo', 'Rios', 'Gil', 32000062, 34, 'victor.r@gmail.com', '1992-04-25', '2026-03-12', 'Disponible', 'Bachillerato', 'Masculino', 1),
(63, 'Adriana', 'Mercedes', 'Mendoza', 'Blanco', 32000063, 23, 'adriana.m@gmail.com', '2003-05-15', '2026-03-15', 'Disponible', 'Universidad', 'Femenino', 1),
(64, 'Arturo', 'Leonel', 'Ferrer', 'Fuentes', 32000064, 25, 'arturo.f@gmail.com', '2001-06-20', '2026-03-18', 'Disponible', 'Bachillerato', 'Masculino', 1),
(65, 'Blanca', 'Nieves', 'Castillo', 'Ortega', 32000065, 30, 'blanca.c@gmail.com', '1996-07-08', '2026-03-20', 'Inactivo', 'Primaria', 'Femenino', 1),
(66, 'César', 'Augusto', 'Vargas', 'Rengel', 32000066, 28, 'cesar.v@gmail.com', '1998-08-30', '2026-03-22', 'Disponible', 'Bachillerato', 'Masculino', 1),
(67, 'Diana', 'Carolina', 'Santos', 'Silva', 32000067, 19, 'diana.s@gmail.com', '2007-09-12', '2026-03-24', 'Disponible', 'Bachillerato', 'Femenino', 1),
(68, 'Eduardo', 'José', 'Sucre', 'Morillo', 32000068, 22, 'eduardo.s@gmail.com', '2004-10-05', '2026-03-26', 'Disponible', 'Bachillerato', 'Masculino', 1),
(69, 'Fabiola', 'Elena', 'Acosta', 'Rivas', 32000069, 21, 'fabiola.a@gmail.com', '2005-11-22', '2026-03-30', 'Disponible', 'Bachillerato', 'Femenino', 1),

-- ── ABRIL 2026 (11 registros) ──
(70, 'Gerardo', 'Antonio', 'Romero', 'Serrada', 32000070, 45, 'gerardo.r@gmail.com', '1981-05-14', '2026-04-02', 'En formación', 'Primaria', 'Masculino', 1),
(71, 'Helena', 'Isabel', 'Serrada', 'Medina', 32000071, 38, 'helena.s@gmail.com', '1988-08-20', '2026-04-05', 'En formación', 'Bachillerato', 'Femenino', 1),
(72, 'Iván', 'Darío', 'Morillo', 'Salazar', 32000072, 33, 'ivan.m@gmail.com', '1993-09-15', '2026-04-08', 'En formación', 'Universidad', 'Masculino', 2),
(73, 'Julia', 'Teresa', 'Torres', 'Moreno', 32000073, 24, 'julia.t@gmail.com', '2002-02-18', '2026-04-10', 'En formación', 'Universidad', 'Femenino', 1),
(74, 'Kevin', 'Eduardo', 'Vargas', 'Acosta', 32000074, 27, 'kevin.v@gmail.com', '1999-03-30', '2026-04-12', 'En formación', 'Bachillerato', 'Masculino', 1),
(75, 'Lorena', 'Beatriz', 'Blanco', 'Torres', 32000075, 29, 'lorena.b@gmail.com', '1997-04-10', '2026-04-15', 'En formación', 'Universidad', 'Femenino', 1),
(76, 'Mauricio', 'Andres', 'Gomez', 'Cruz', 32000076, 20, 'mauricio.g@gmail.com', '2006-06-12', '2026-04-18', 'Disponible', 'Bachillerato', 'Masculino', 1),
(77, 'Nora', 'Gabriela', 'Perez', 'Ruiz', 32000077, 23, 'nora.p@gmail.com', '2003-07-28', '2026-04-20', 'Disponible', 'Universidad', 'Femenino', 1),
(78, 'Omar', 'José', 'Medina', 'Castillo', 32000078, 22, 'omar.m@gmail.com', '2004-08-04', '2026-04-22', 'Disponible', 'Bachillerato', 'Masculino', 1),
(79, 'Paula', 'Isabel', 'Castro', 'Ortega', 32000079, 31, 'paula.c@gmail.com', '1995-10-14', '2026-04-25', 'Disponible', 'Universidad', 'Femenino', 1),
(80, 'Rafael', 'Jesús', 'Rodriguez', 'Moreno', 32000080, 26, 'rafael.r@gmail.com', '2000-11-09', '2026-04-28', 'Disponible', 'Bachillerato', 'Masculino', 1),

-- ── MAYO 2026 (10 registros) ──
(81, 'Sonia', 'Alejandra', 'Gonzalez', 'Herrera', 32000081, 25, 'sonia.g@gmail.com', '2001-01-20', '2026-05-02', 'Disponible', 'Universidad', 'Femenino', 1),
(82, 'Tito', 'Wilmer', 'Lopez', 'Acosta', 32000082, 28, 'tito.l@gmail.com', '1998-02-15', '2026-05-05', 'Disponible', 'Bachillerato', 'Masculino', 1),
(83, 'Úrsula', 'Elena', 'Hernandez', 'Gil', 32000083, 20, 'ursula.h@gmail.com', '2006-03-12', '2026-05-08', 'Disponible', 'Bachillerato', 'Femenino', 1),
(84, 'Walter', 'César', 'Martinez', 'Fuentes', 32000084, 30, 'walter.m@gmail.com', '1996-04-24', '2026-05-12', 'Disponible', 'Universidad', 'Masculino', 1),
(85, 'Ximena', 'Rosa', 'Perez', 'Blanco', 32000085, 23, 'ximena.p@gmail.com', '2003-05-20', '2026-05-15', 'Disponible', 'Bachillerato', 'Femenino', 1),
(86, 'Yago', 'Nelson', 'Alvarez', 'Castillo', 32000086, 27, 'yago.a@gmail.com', '1999-06-30', '2026-05-18', 'Disponible', 'Universidad', 'Masculino', 1),
(87, 'Zoe', 'Sofía', 'Sanchez', 'Rojas', 32000087, 19, 'zoe.s@gmail.com', '2007-07-15', '2026-05-20', 'Disponible', 'Bachillerato', 'Femenino', 1),
(88, 'Alfredo', 'José', 'Diaz', 'Salazar', 32000088, 35, 'alfredo.d@gmail.com', '1991-08-05', '2026-05-22', 'Disponible', 'Primaria', 'Masculino', 1),
(89, 'Berta', 'Beatriz', 'Gomez', 'Castillo', 32000089, 21, 'berta.g@gmail.com', '2005-09-02', '2026-05-24', 'Disponible', 'Bachillerato', 'Femenino', 1),
(90, 'Claudio', 'José', 'Castillo', 'Vargas', 32000090, 24, 'claudio.c@gmail.com', '2002-10-10', '2026-05-24', 'Disponible', 'Bachillerato', 'Masculino', 1);


-- ============================================================
-- 4. SEMBRAR CURSOS (33 cursos distribuidos desde Agosto 2025 hasta Mayo 2026)
-- Estatus: 1: En espera, 2: Diferido, 3: En proceso, 4: En pausa, 5: Culminado, 6: Inactivo
-- Modalidades: 1: Presencial, 2: Online, 3: Semi presencial
-- Ámbitos: 1: CFS, 2: Comunas, 3: Militares, 4: U.Educativas, 5: Misiones
-- ============================================================
INSERT INTO `tb_curso` (`id_curso`, `nombre_curso`, `fecha_inicio`, `fecha_limite_inicio`, `fecha_fin`, `horas_totales`, `horas_por_clase`, `horas_completadas`, `max_participantes`, `min_participantes`, `num_inscritos`, `id_modalidad`, `tipo_formacion`, `id_estatusCurso`, `id_sectoreconomico`, `id_subtipo`, `id_areaFormativa`, `id_ambito`, `id_ppl`, `id_nombre_especifico`, `turno`, `programa_formacion`, `fecha_creacion`) VALUES
-- ── AGOSTO 2025 (2 cursos culminados) ──
(2, 'Panadería Básica I', '2025-08-04', '2025-07-20', '2025-09-12', 60.00, 4.00, 60.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-08-01 09:00:00'),
(3, 'Introducción a la Ofimática', '2025-08-11', '2025-07-25', '2025-09-19', 40.00, 4.00, 40.00, 25, 10, 12, 2, 'Unidad Curricular', 5, 1, 3, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-08-05 10:30:00'),

-- ── SEPTIEMBRE 2025 (2 cursos culminados) ──
(4, 'Confección de Uniformes Escolares', '2025-09-08', '2025-08-20', '2025-10-17', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 2, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-09-01 09:00:00'),
(5, 'Seguridad y Salud en el Trabajo', '2025-09-15', '2025-08-25', '2025-10-24', 40.00, 4.00, 40.00, 30, 10, 11, 2, 'Unidad Curricular', 5, 1, 2, 5, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-09-05 10:00:00'),

-- ── OCTUBRE 2025 (3 cursos culminados) ──
(6, 'Matemática Aplicada', '2025-10-06', '2025-09-20', '2025-11-14', 60.00, 3.00, 60.00, 25, 10, 10, 3, 'Unidad Curricular', 5, 1, 3, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-10-01 09:00:00'),
(7, 'Ortografía y Redacción Eficaz', '2025-10-13', '2025-09-25', '2025-11-21', 30.00, 3.00, 30.00, 25, 10, 12, 2, 'Unidad Curricular', 5, 1, 3, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-10-05 10:00:00'),
(8, 'Ética y Valores', '2025-10-20', '2025-10-05', '2025-12-12', 40.00, 4.00, 40.00, 30, 10, 11, 1, 'Unidad Curricular', 5, 1, 3, 6, 5, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-10-10 11:30:00'),

-- ── NOVIEMBRE 2025 (3 cursos culminados) ──
(9, 'Panadería y Pastelería Navideña', '2025-11-03', '2025-10-20', '2025-12-12', 60.00, 4.00, 60.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-11-01 09:00:00'),
(10, 'Dibujo Técnico Básico', '2025-11-10', '2025-10-25', '2025-12-19', 40.00, 3.00, 40.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 6, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-11-05 10:15:00'),
(11, 'Lectura e Interpretación de Planos', '2025-11-17', '2025-11-01', '2025-12-26', 60.00, 4.00, 60.00, 25, 10, 11, 3, 'Unidad Curricular', 5, 2, 1, 6, 4, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-11-12 14:00:00'),

-- ── DICIEMBRE 2025 (2 cursos culminados) ──
(12, 'Higiene y Manipulación de Alimentos I', '2025-12-01', '2025-11-15', '2026-01-09', 30.00, 3.00, 30.00, 30, 10, 10, 1, 'Unidad Curricular', 5, 1, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-12-01 09:00:00'),
(13, 'Administración de Pequeñas Empresas', '2025-12-08', '2025-11-20', '2026-01-16', 60.00, 4.00, 60.00, 25, 10, 11, 2, 'Unidad Curricular', 5, 1, 2, 6, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2025-12-05 10:00:00'),

-- ── ENERO 2026 (3 cursos culminados) ──
(14, 'Cálculo de Costos y Presupuestos', '2026-01-05', '2025-12-20', '2026-02-13', 60.00, 4.00, 60.00, 25, 10, 10, 3, 'Unidad Curricular', 5, 1, 2, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-02 09:00:00'),
(15, 'Mecánica de Motores Básica', '2026-01-12', '2025-12-28', '2026-02-20', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 3, 3, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-01-08 11:00:00'),
(16, 'Habilidades Socioemocionales I', '2026-01-19', '2026-01-05', '2026-03-13', 30.00, 3.00, 30.00, 30, 10, 11, 1, 'Unidad Curricular', 5, 1, 3, 6, 5, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-01-15 15:30:00'),

-- ── FEBRERO 2026 (3 culminados + 1 diferido) ──
(17, 'Panadería y Pastelería Básica II', '2026-02-02', '2026-01-15', '2026-03-13', 80.00, 4.00, 80.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-01 09:00:00'),
(18, 'Algoritmia y Programación Bísica', '2026-02-09', '2026-01-25', '2026-03-20', 80.00, 4.00, 80.00, 25, 10, 11, 2, 'Unidad Curricular', 5, 3, 1, 9, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-02-05 11:30:00'),
(19, 'Confección de Prendas de Vestir', '2026-02-16', '2026-02-01', '2026-04-10', 80.00, 4.00, 80.00, 20, 10, 12, 1, 'Unidad Curricular', 5, 2, 1, 2, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-12 14:15:00'),
(20, 'Dibujo Técnico Industrial', '2026-02-23', '2026-02-10', '2026-04-17', 60.00, 3.00, 0.00, 20, 10, 6, 1, 'Unidad Curricular', 2, 2, 1, 6, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-02-20 15:45:00'),

-- ── MARZO 2026 (3 culminados + 1 pausado) ──
(21, 'Mecánica de Motores a Gasolina II', '2026-03-02', '2026-02-15', '2026-04-10', 100.00, 5.00, 100.00, 20, 10, 10, 1, 'Unidad Curricular', 5, 2, 1, 3, 1, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-03-01 09:00:00'),
(22, 'Inglés con Propósitos Generales II', '2026-03-09', '2026-02-25', '2026-04-17', 60.00, 3.00, 60.00, 30, 10, 11, 2, 'Unidad Curricular', 5, 1, 3, 6, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-05 10:15:00'),
(23, 'Higiene y Manipulación de Alimentos II', '2026-03-16', '2026-03-01', '2026-05-08', 40.00, 4.00, 40.00, 25, 10, 12, 1, 'Unidad Curricular', 5, 1, 3, 1, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-12 11:00:00'),
(24, 'Recreación y Dinámicas Infantiles', '2026-03-23', '2026-03-10', '2026-05-01', 40.00, 4.00, 20.00, 20, 10, 7, 3, 'Unidad Curricular', 4, 1, 3, 8, 4, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-03-20 14:00:00'),

-- ── ABRIL 2026 (3 cursos en proceso) ──
(25, 'Base de Datos Relacionales', '2026-04-06', '2026-03-20', '2026-05-29', 80.00, 4.00, 60.00, 25, 10, 14, 2, 'Unidad Curricular', 3, 3, 1, 9, 1, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-04-02 09:30:00'),
(26, 'Confección de Uniformes Médicos', '2026-04-13', '2026-03-30', '2026-06-05', 80.00, 4.00, 48.00, 20, 10, 12, 1, 'Unidad Curricular', 3, 2, 1, 2, 2, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-08 10:00:00'),
(27, 'Electricidad Básica Residencial II', '2026-04-20', '2026-04-05', '2026-06-12', 90.00, 4.00, 36.00, 20, 10, 15, 1, 'Unidad Curricular', 3, 2, 1, 10, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-04-15 12:00:00'),

-- ── MAYO 2026 (3 cursos en proceso) ──
(28, 'Mercadeo y Ventas Digitales II', '2026-05-04', '2026-04-20', '2026-06-26', 40.00, 4.00, 12.00, 30, 10, 10, 2, 'Unidad Curricular', 3, 1, 2, 6, 4, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-01 09:00:00'),
(29, 'Diseño y Construcción de Redes II', '2026-05-11', '2026-04-25', '2026-07-03', 80.00, 4.00, 8.00, 20, 10, 8, 3, 'Unidad Curricular', 3, 3, 1, 9, 5, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-05 11:30:00'),
(30, 'Higiene y Manipulación de Alimentos III', '2026-05-18', '2026-05-01', '2026-07-10', 30.00, 3.00, 3.00, 30, 10, 13, 1, 'Unidad Curricular', 3, 1, 3, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-12 14:00:00'),

-- ── EN ESPERA / FUTUROS (2 cursos creados en Mayo para Junio) ──
(31, 'Panadería y Pastelería Avanzada II', '2026-06-01', '2026-05-15', '2026-07-15', 100.00, 4.00, 0.00, 20, 10, 8, 1, 'Unidad Curricular', 1, 1, 1, 1, 1, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2026-05-18 15:30:00'),
(32, 'Algoritmia y Programación Avanzada', '2026-06-08', '2026-05-20', '2026-07-20', 80.00, 4.00, 0.00, 25, 10, 10, 2, 'Unidad Curricular', 1, 3, 1, 9, 2, NULL, NULL, 'Tarde', 'Programa de Formación Productiva (PFP)', '2026-05-22 16:45:00'),

-- ── INACTIVO (1 curso inactivo) ──
(33, 'Curso Descontinuado de Prueba', '2025-10-01', '2025-09-15', '2025-11-01', 40.00, 4.00, 10.00, 20, 10, 0, 1, 'Unidad Curricular', 6, 1, 3, 5, 3, NULL, NULL, 'Mañana', 'Programa de Formación Productiva (PFP)', '2025-09-25 10:00:00');


-- ============================================================
-- 5. ASIGNAR DOCENTES A CURSOS (`tb_docente_curso`)
-- ============================================================
INSERT INTO `tb_docente_curso` (`id_docente_curso`, `id_docente`, `id_curso`, `fecha_asignacion`) VALUES
-- Docentes asignados a cursos pasados, activos o futuros
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


-- ============================================================
-- 6. ASIGNAR CONTACTOS TELEFÓNICOS (`tb_telefono`)
-- ============================================================
-- Docentes
INSERT INTO `tb_telefono` (`numero_de_telefono`, `tipo_de_telefono`, `id_docente`, `id_participante`, `id_usuario`) VALUES
(4121111101, 'Móvil', 1, NULL, NULL),
(4121111102, 'Móvil', 2, NULL, NULL),
(4121111103, 'Móvil', 3, NULL, NULL),
(4121111104, 'Móvil', 4, NULL, NULL),
(4121111105, 'Móvil', 5, NULL, NULL),
(4121111106, 'Móvil', 6, NULL, NULL),
(4121111107, 'Móvil', 7, NULL, NULL),
(4121111108, 'Móvil', 8, NULL, NULL),
(4121111109, 'Móvil', 9, NULL, NULL),
(4121111110, 'Móvil', 10, NULL, NULL),
(4121111111, 'Móvil', 11, NULL, NULL),
(4121111112, 'Móvil', 12, NULL, NULL),
(4121111113, 'Móvil', 13, NULL, NULL),
(4121111114, 'Móvil', 14, NULL, NULL),
(4121111115, 'Móvil', 15, NULL, NULL);

-- Participantes (Muestreo rápido)
INSERT INTO `tb_telefono` (`numero_de_telefono`, `tipo_de_telefono`, `id_docente`, `id_participante`, `id_usuario`)
SELECT 4242000000 + id_participante, 'Móvil', NULL, id_participante, NULL
FROM tb_participante;


-- ============================================================
-- 7. INSCRIPCIONES Y HISTORIAL ACADÉMICO (`tb_participante_curso`)
-- Teniendo en cuenta la progresión mensual y múltiple asociación.
-- Estados inscripción: 'Pendiente', 'Aprobado', 'Reprobado', 'En formación', 'Ausente'
-- ============================================================

-- ── 2025 ──
-- Cursos Agosto 2025 (Curso 2 y 3)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(2, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(3, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(4, 2, '2025-08-01', '2025-08-04', '2025-09-12', 'Aprobado'),
(5, 2, '2025-08-02', '2025-08-04', '2025-09-12', 'Aprobado'),
(6, 2, '2025-08-02', '2025-08-04', '2025-09-12', 'Aprobado'),
(7, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Reprobado'),
(8, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Ausente'),
(9, 2, '2025-08-03', '2025-08-04', '2025-09-12', 'Aprobado'),
(10,2, '2025-08-04', '2025-08-04', '2025-09-12', 'Aprobado'),

(11, 3, '2025-08-05', '2025-08-11', '2025-09-19', 'Aprobado'),
(12, 3, '2025-08-05', '2025-08-11', '2025-09-19', 'Aprobado'),
(13, 3, '2025-08-06', '2025-08-11', '2025-09-19', 'Ausente'),
(14, 3, '2025-08-06', '2025-08-11', '2025-09-19', 'Aprobado'),
(15, 3, '2025-08-08', '2025-08-11', '2025-09-19', 'Aprobado'),
(16, 3, '2025-08-08', '2025-08-11', '2025-09-19', 'Aprobado'),
(17, 3, '2025-08-09', '2025-08-11', '2025-09-19', 'Aprobado'),
(18, 3, '2025-08-09', '2025-08-11', '2025-09-19', 'Reprobado'),
(19, 3, '2025-08-10', '2025-08-11', '2025-09-19', 'Aprobado'),
(20, 3, '2025-08-10', '2025-08-11', '2025-09-19', 'Aprobado'),
(21, 3, '2025-08-11', '2025-08-11', '2025-09-19', 'Aprobado'),
(22, 3, '2025-08-11', '2025-08-11', '2025-09-19', 'Aprobado');

-- Cursos Septiembre 2025 (Curso 4 y 5)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(23, 4, '2025-09-02', '2025-09-08', '2025-10-17', 'Aprobado'),
(24, 4, '2025-09-02', '2025-09-08', '2025-10-17', 'Aprobado'),
(25, 4, '2025-09-03', '2025-09-08', '2025-10-17', 'Aprobado'),
(26, 4, '2025-09-03', '2025-09-08', '2025-10-17', 'Reprobado'),
(27, 4, '2025-09-04', '2025-09-08', '2025-10-17', 'Aprobado'),
(28, 4, '2025-09-04', '2025-09-08', '2025-10-17', 'Aprobado'),
(29, 4, '2025-09-05', '2025-09-08', '2025-10-17', 'Aprobado'),
(30, 4, '2025-09-05', '2025-09-08', '2025-10-17', 'Aprobado'),
(31, 4, '2025-09-06', '2025-09-08', '2025-10-17', 'Aprobado'),
(32, 4, '2025-09-06', '2025-09-08', '2025-10-17', 'Aprobado'),

(33, 5, '2025-09-08', '2025-09-15', '2025-10-24', 'Aprobado'),
(34, 5, '2025-09-08', '2025-09-15', '2025-10-24', 'Aprobado'),
(35, 5, '2025-09-09', '2025-09-15', '2025-10-24', 'Reprobado'),
(36, 5, '2025-09-09', '2025-09-15', '2025-10-24', 'Aprobado'),
(37, 5, '2025-09-10', '2025-09-15', '2025-10-24', 'Aprobado'),
(38, 5, '2025-09-10', '2025-09-15', '2025-10-24', 'Aprobado'),
(39, 5, '2025-09-12', '2025-09-15', '2025-10-24', 'Aprobado'),
(40, 5, '2025-09-12', '2025-09-15', '2025-10-24', 'Aprobado'),
(41, 5, '2025-09-14', '2025-09-15', '2025-10-24', 'Aprobado'),
(42, 5, '2025-09-14', '2025-09-15', '2025-10-24', 'Aprobado'),
(43, 5, '2025-09-15', '2025-09-15', '2025-10-24', 'Aprobado');

-- Cursos Octubre 2025 (Curso 6, 7 y 8)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1,  6, '2025-10-01', '2025-10-06', '2025-11-14', 'Aprobado'),
(2,  6, '2025-10-01', '2025-10-06', '2025-11-14', 'Aprobado'),
(3,  6, '2025-10-02', '2025-10-06', '2025-11-14', 'Aprobado'),
(4,  6, '2025-10-02', '2025-10-06', '2025-11-14', 'Aprobado'),
(5,  6, '2025-10-03', '2025-10-06', '2025-11-14', 'Aprobado'),
(11, 6, '2025-10-03', '2025-10-06', '2025-11-14', 'Aprobado'),
(12, 6, '2025-10-04', '2025-10-06', '2025-11-14', 'Aprobado'),
(13, 6, '2025-10-04', '2025-10-06', '2025-11-14', 'Aprobado'),
(14, 6, '2025-10-05', '2025-10-06', '2025-11-14', 'Aprobado'),
(15, 6, '2025-10-05', '2025-10-06', '2025-11-14', 'Aprobado'),

(16, 7, '2025-10-05', '2025-10-13', '2025-11-21', 'Aprobado'),
(17, 7, '2025-10-06', '2025-10-13', '2025-11-21', 'Aprobado'),
(18, 7, '2025-10-08', '2025-10-13', '2025-11-21', 'Aprobado'),
(19, 7, '2025-10-08', '2025-10-13', '2025-11-21', 'Aprobado'),
(20, 7, '2025-10-10', '2025-10-13', '2025-11-21', 'Aprobado'),
(21, 7, '2025-10-10', '2025-10-13', '2025-11-21', 'Aprobado'),
(22, 7, '2025-10-11', '2025-10-13', '2025-11-21', 'Aprobado'),
(23, 7, '2025-10-11', '2025-10-13', '2025-11-21', 'Aprobado'),
(24, 7, '2025-10-12', '2025-10-13', '2025-11-21', 'Aprobado'),
(25, 7, '2025-10-12', '2025-10-13', '2025-11-21', 'Aprobado'),
(26, 7, '2025-10-13', '2025-10-13', '2025-11-21', 'Aprobado'),
(27, 7, '2025-10-13', '2025-10-13', '2025-11-21', 'Aprobado'),

(28, 8, '2025-10-12', '2025-10-20', '2025-12-12', 'Aprobado'),
(29, 8, '2025-10-12', '2025-10-20', '2025-12-12', 'Aprobado'),
(30, 8, '2025-10-14', '2025-10-20', '2025-12-12', 'Aprobado'),
(31, 8, '2025-10-15', '2025-10-20', '2025-12-12', 'Aprobado'),
(32, 8, '2025-10-15', '2025-10-20', '2025-12-12', 'Aprobado'),
(33, 8, '2025-10-18', '2025-10-20', '2025-12-12', 'Aprobado'),
(34, 8, '2025-10-18', '2025-10-20', '2025-12-12', 'Aprobado'),
(35, 8, '2025-10-19', '2025-10-20', '2025-12-12', 'Aprobado'),
(36, 8, '2025-10-19', '2025-10-20', '2025-12-12', 'Aprobado'),
(37, 8, '2025-10-20', '2025-10-20', '2025-12-12', 'Aprobado'),
(38, 8, '2025-10-20', '2025-10-20', '2025-12-12', 'Aprobado');

-- Cursos Noviembre 2025 (Curso 9, 10 y 11)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1,  9, '2025-10-26', '2025-11-03', '2025-12-12', 'Aprobado'),
(2,  9, '2025-10-26', '2025-11-03', '2025-12-12', 'Aprobado'),
(3,  9, '2025-10-28', '2025-11-03', '2025-12-12', 'Aprobado'),
(4,  9, '2025-10-28', '2025-11-03', '2025-12-12', 'Aprobado'),
(5,  9, '2025-10-30', '2025-11-03', '2025-12-12', 'Aprobado'),
(6,  9, '2025-10-30', '2025-11-03', '2025-12-12', 'Aprobado'),
(7,  9, '2025-11-02', '2025-11-03', '2025-12-12', 'Aprobado'),
(8,  9, '2025-11-02', '2025-11-03', '2025-12-12', 'Aprobado'),
(9,  9, '2025-11-03', '2025-11-03', '2025-12-12', 'Aprobado'),
(10, 9, '2025-11-03', '2025-11-03', '2025-12-12', 'Aprobado'),

(11, 10, '2025-11-02', '2025-11-10', '2025-12-19', 'Aprobado'),
(12, 10, '2025-11-02', '2025-11-10', '2025-12-19', 'Aprobado'),
(13, 10, '2025-11-04', '2025-11-10', '2025-12-19', 'Aprobado'),
(14, 10, '2025-11-05', '2025-11-10', '2025-12-19', 'Aprobado'),
(15, 10, '2025-11-05', '2025-11-10', '2025-12-19', 'Aprobado'),
(22, 10, '2025-11-06', '2025-11-10', '2025-12-19', 'Aprobado'),
(23, 10, '2025-11-08', '2025-11-10', '2025-12-19', 'Aprobado'),
(24, 10, '2025-11-08', '2025-11-10', '2025-12-19', 'Aprobado'),
(25, 10, '2025-11-10', '2025-11-10', '2025-12-19', 'Aprobado'),
(26, 10, '2025-11-10', '2025-11-10', '2025-12-19', 'Aprobado'),

(27, 11, '2025-11-09', '2025-11-17', '2025-12-26', 'Aprobado'),
(28, 11, '2025-11-10', '2025-11-17', '2025-12-26', 'Aprobado'),
(29, 11, '2025-11-12', '2025-11-17', '2025-12-26', 'Aprobado'),
(30, 11, '2025-11-12', '2025-11-17', '2025-12-26', 'Aprobado'),
(31, 11, '2025-11-14', '2025-11-17', '2025-12-26', 'Aprobado'),
(32, 11, '2025-11-15', '2025-11-17', '2025-12-26', 'Aprobado'),
(33, 11, '2025-11-15', '2025-11-17', '2025-12-26', 'Aprobado'),
(34, 11, '2025-11-16', '2025-11-17', '2025-12-26', 'Aprobado'),
(35, 11, '2025-11-16', '2025-11-17', '2025-12-26', 'Aprobado'),
(36, 11, '2025-11-17', '2025-11-17', '2025-12-26', 'Aprobado'),
(37, 11, '2025-11-17', '2025-11-17', '2025-12-26', 'Aprobado');

-- Cursos Diciembre 2025 (Curso 12 y 13)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(38, 12, '2025-11-20', '2025-12-01', '2026-01-09', 'Aprobado'),
(39, 12, '2025-11-20', '2025-12-01', '2026-01-09', 'Aprobado'),
(40, 12, '2025-11-22', '2025-12-01', '2026-01-09', 'Aprobado'),
(41, 12, '2025-11-25', '2025-12-01', '2026-01-09', 'Aprobado'),
(42, 12, '2025-11-26', '2025-12-01', '2026-01-09', 'Aprobado'),
(43, 12, '2025-11-28', '2025-12-01', '2026-01-09', 'Aprobado'),
(44, 12, '2025-11-30', '2025-12-01', '2026-01-09', 'Aprobado'),
(45, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),
(46, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),
(47, 12, '2025-12-01', '2025-12-01', '2026-01-09', 'Aprobado'),

(14, 13, '2025-11-30', '2025-12-08', '2026-01-16', 'Aprobado'),
(15, 13, '2025-12-01', '2025-12-08', '2026-01-16', 'Aprobado'),
(16, 13, '2025-12-02', '2025-12-08', '2026-01-16', 'Aprobado'),
(17, 13, '2025-12-04', '2025-12-08', '2026-01-16', 'Aprobado'),
(18, 13, '2025-12-05', '2025-12-08', '2026-01-16', 'Aprobado'),
(19, 13, '2025-12-05', '2025-12-08', '2026-01-16', 'Aprobado'),
(20, 13, '2025-12-06', '2025-12-08', '2026-01-16', 'Aprobado'),
(31, 13, '2025-12-06', '2025-12-08', '2026-01-16', 'Aprobado'),
(32, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado'),
(33, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado'),
(34, 13, '2025-12-08', '2025-12-08', '2026-01-16', 'Aprobado');

-- ── 2026 ──
-- Cursos Enero 2026 (Curso 14, 15 y 16)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1,  14, '2026-01-02', '2026-01-05', '2026-02-13', 'Aprobado'),
(2,  14, '2026-01-02', '2026-01-05', '2026-02-13', 'Aprobado'),
(3,  14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(4,  14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(5,  14, '2026-01-03', '2026-01-05', '2026-02-13', 'Aprobado'),
(6,  14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(7,  14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(8,  14, '2026-01-04', '2026-01-05', '2026-02-13', 'Aprobado'),
(9,  14, '2026-01-05', '2026-01-05', '2026-02-13', 'Aprobado'),
(10, 14, '2026-01-05', '2026-01-05', '2026-02-13', 'Aprobado'),

(11, 15, '2026-01-05', '2026-01-12', '2026-02-20', 'Aprobado'),
(12, 15, '2026-01-05', '2026-01-12', '2026-02-20', 'Aprobado'),
(22, 15, '2026-01-06', '2026-01-12', '2026-02-20', 'Aprobado'),
(23, 15, '2026-01-06', '2026-01-12', '2026-02-20', 'Aprobado'),
(24, 15, '2026-01-08', '2026-01-12', '2026-02-20', 'Aprobado'),
(25, 15, '2026-01-08', '2026-01-12', '2026-02-20', 'Aprobado'),
(26, 15, '2026-01-10', '2026-01-12', '2026-02-20', 'Aprobado'),
(27, 15, '2026-01-10', '2026-01-12', '2026-02-20', 'Aprobado'),
(48, 15, '2026-01-12', '2026-01-12', '2026-02-20', 'Aprobado'),
(49, 15, '2026-01-12', '2026-01-12', '2026-02-20', 'Aprobado'),

(35, 16, '2026-01-12', '2026-01-19', '2026-03-13', 'Aprobado'),
(36, 16, '2026-01-12', '2026-01-19', '2026-03-13', 'Aprobado'),
(37, 16, '2026-01-14', '2026-01-19', '2026-03-13', 'Aprobado'),
(38, 16, '2026-01-14', '2026-01-19', '2026-03-13', 'Aprobado'),
(39, 16, '2026-01-15', '2026-01-19', '2026-03-13', 'Aprobado'),
(43, 16, '2026-01-16', '2026-01-19', '2026-03-13', 'Aprobado'),
(44, 16, '2026-01-16', '2026-01-19', '2026-03-13', 'Aprobado'),
(45, 16, '2026-01-18', '2026-01-19', '2026-03-13', 'Aprobado'),
(46, 16, '2026-01-18', '2026-01-19', '2026-03-13', 'Aprobado'),
(47, 16, '2026-01-19', '2026-01-19', '2026-03-13', 'Aprobado'),
(50, 16, '2026-01-19', '2026-01-19', '2026-03-13', 'Aprobado');

-- Cursos Febrero 2026 (Curso 17, 18, 19 y 20)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1,  17, '2026-01-28', '2026-02-02', '2026-03-13', 'Aprobado'),
(2,  17, '2026-01-28', '2026-02-02', '2026-03-13', 'Aprobado'),
(3,  17, '2026-01-30', '2026-02-02', '2026-03-13', 'Aprobado'),
(4,  17, '2026-01-30', '2026-02-02', '2026-03-13', 'Aprobado'),
(5,  17, '2026-02-01', '2026-02-02', '2026-03-13', 'Aprobado'),
(6,  17, '2026-02-01', '2026-02-02', '2026-03-13', 'Aprobado'),
(7,  17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(8,  17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(9,  17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),
(10, 17, '2026-02-02', '2026-02-02', '2026-03-13', 'Aprobado'),

(51, 18, '2026-02-02', '2026-02-09', '2026-03-20', 'Aprobado'),
(52, 18, '2026-02-02', '2026-02-09', '2026-03-20', 'Aprobado'),
(53, 18, '2026-02-04', '2026-02-09', '2026-03-20', 'Aprobado'),
(54, 18, '2026-02-05', '2026-02-09', '2026-03-20', 'Aprobado'),
(55, 18, '2026-02-06', '2026-02-09', '2026-03-20', 'Aprobado'),
(56, 18, '2026-02-06', '2026-02-09', '2026-03-20', 'Aprobado'),
(57, 18, '2026-02-08', '2026-02-09', '2026-03-20', 'Aprobado'),
(58, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(11, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(12, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),
(13, 18, '2026-02-09', '2026-02-09', '2026-03-20', 'Aprobado'),

(14, 19, '2026-02-10', '2026-02-16', '2026-04-10', 'Aprobado'),
(15, 19, '2026-02-10', '2026-02-16', '2026-04-10', 'Aprobado'),
(16, 19, '2026-02-12', '2026-02-16', '2026-04-10', 'Aprobado'),
(17, 19, '2026-02-12', '2026-02-16', '2026-04-10', 'Aprobado'),
(18, 19, '2026-02-14', '2026-02-16', '2026-04-10', 'Aprobado'),
(19, 19, '2026-02-14', '2026-02-16', '2026-04-10', 'Aprobado'),
(20, 19, '2026-02-15', '2026-02-16', '2026-04-10', 'Aprobado'),
(21, 19, '2026-02-15', '2026-02-16', '2026-04-10', 'Aprobado'),
(22, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(23, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(24, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),
(25, 19, '2026-02-16', '2026-02-16', '2026-04-10', 'Aprobado'),

(9,  20, '2026-02-18', '2026-02-23', '2026-04-17', 'Pendiente'),
(26, 20, '2026-02-18', '2026-02-23', '2026-04-17', 'Pendiente'),
(32, 20, '2026-02-20', '2026-02-23', '2026-04-17', 'Pendiente'),
(44, 20, '2026-02-20', '2026-02-23', '2026-04-17', 'Pendiente'),
(45, 20, '2026-02-22', '2026-02-23', '2026-04-17', 'Pendiente'),
(40, 20, '2026-02-22', '2026-02-23', '2026-04-17', 'Pendiente');

-- Cursos Marzo 2026 (Curso 21, 22, 23 y 24)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(27, 21, '2026-02-25', '2026-03-02', '2026-04-10', 'Aprobado'),
(28, 21, '2026-02-25', '2026-03-02', '2026-04-10', 'Aprobado'),
(29, 21, '2026-02-26', '2026-03-02', '2026-04-10', 'Aprobado'),
(30, 21, '2026-02-28', '2026-03-02', '2026-04-10', 'Aprobado'),
(59, 21, '2026-03-01', '2026-03-02', '2026-04-10', 'Aprobado'),
(60, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(61, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(62, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(63, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),
(64, 21, '2026-03-02', '2026-03-02', '2026-04-10', 'Aprobado'),

(65, 22, '2026-03-04', '2026-03-09', '2026-04-17', 'Aprobado'),
(66, 22, '2026-03-05', '2026-03-09', '2026-04-17', 'Aprobado'),
(67, 22, '2026-03-06', '2026-03-09', '2026-04-17', 'Aprobado'),
(68, 22, '2026-03-08', '2026-03-09', '2026-04-17', 'Aprobado'),
(69, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(31, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(32, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(33, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(34, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(35, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),
(36, 22, '2026-03-09', '2026-03-09', '2026-04-17', 'Aprobado'),

(37, 23, '2026-03-10', '2026-03-16', '2026-05-08', 'Aprobado'),
(38, 23, '2026-03-11', '2026-03-16', '2026-05-08', 'Aprobado'),
(39, 23, '2026-03-12', '2026-03-16', '2026-05-08', 'Aprobado'),
(40, 23, '2026-03-12', '2026-03-16', '2026-05-08', 'Aprobado'),
(41, 23, '2026-03-14', '2026-03-16', '2026-05-08', 'Aprobado'),
(42, 23, '2026-03-14', '2026-03-16', '2026-05-08', 'Aprobado'),
(46, 23, '2026-03-15', '2026-03-16', '2026-05-08', 'Aprobado'),
(47, 23, '2026-03-15', '2026-03-16', '2026-05-08', 'Aprobado'),
(48, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(49, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(50, 23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),
(1,  23, '2026-03-16', '2026-03-16', '2026-05-08', 'Aprobado'),

(2,  24, '2026-03-18', '2026-03-23', '2026-05-01', 'En formación'),
(3,  24, '2026-03-18', '2026-03-23', '2026-05-01', 'En formación'),
(4,  24, '2026-03-20', '2026-03-23', '2026-05-01', 'En formación'),
(5,  24, '2026-03-20', '2026-03-23', '2026-05-01', 'En formación'),
(6,  24, '2026-03-22', '2026-03-23', '2026-05-01', 'En formación'),
(7,  24, '2026-03-22', '2026-03-23', '2026-05-01', 'En formación'),
(8,  24, '2026-03-23', '2026-03-23', '2026-05-01', 'En formación');

-- Cursos Abril 2026 (Curso 25, 26 y 27 en proceso)
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(10, 25, '2026-04-02', '2026-04-06', '2026-05-29', 'En formación'),
(11, 25, '2026-04-02', '2026-04-06', '2026-05-29', 'En formación'),
(16, 25, '2026-04-03', '2026-04-06', '2026-05-29', 'En formación'),
(21, 25, '2026-04-03', '2026-04-06', '2026-05-29', 'En formación'),
(22, 25, '2026-04-04', '2026-04-06', '2026-05-29', 'En formación'),
(28, 25, '2026-04-04', '2026-04-06', '2026-05-29', 'En formación'),
(29, 25, '2026-04-05', '2026-04-06', '2026-05-29', 'En formación'),
(30, 25, '2026-04-05', '2026-04-06', '2026-05-29', 'En formación'),
(70, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),
(71, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),
(72, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),
(73, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),
(74, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),
(75, 25, '2026-04-06', '2026-04-06', '2026-05-29', 'En formación'),

(1,  26, '2026-04-08', '2026-04-13', '2026-06-05', 'En formación'),
(2,  26, '2026-04-08', '2026-04-13', '2026-06-05', 'En formación'),
(4,  26, '2026-04-10', '2026-04-13', '2026-06-05', 'En formación'),
(36, 26, '2026-04-10', '2026-04-13', '2026-06-05', 'En formación'),
(37, 26, '2026-04-11', '2026-04-13', '2026-06-05', 'En formación'),
(46, 26, '2026-04-11', '2026-04-13', '2026-06-05', 'En formación'),
(47, 26, '2026-04-12', '2026-04-13', '2026-06-05', 'En formación'),
(48, 26, '2026-04-12', '2026-04-13', '2026-06-05', 'En formación'),
(49, 26, '2026-04-13', '2026-04-13', '2026-06-05', 'En formación'),
(55, 26, '2026-04-13', '2026-04-13', '2026-06-05', 'En formación'),
(56, 26, '2026-04-13', '2026-04-13', '2026-06-05', 'En formación'),
(57, 26, '2026-04-13', '2026-04-13', '2026-06-05', 'En formación'),

(31, 27, '2026-04-15', '2026-04-20', '2026-06-12', 'En formación'),
(70, 27, '2026-04-15', '2026-04-20', '2026-06-12', 'En formación'),
(71, 27, '2026-04-16', '2026-04-20', '2026-06-12', 'En formación'),
(72, 27, '2026-04-16', '2026-04-20', '2026-06-12', 'En formación'),
(73, 27, '2026-04-18', '2026-04-20', '2026-06-12', 'En formación'),
(74, 27, '2026-04-18', '2026-04-20', '2026-06-12', 'En formación'),
(75, 27, '2026-04-19', '2026-04-20', '2026-06-12', 'En formación'),
(10, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(11, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(16, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(21, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(22, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(28, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(29, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación'),
(30, 27, '2026-04-20', '2026-04-20', '2026-06-12', 'En formación');

-- ── MAYO: Curso 28, 29 y 30 en proceso, más 31 y 32 futuros
INSERT INTO `tb_participante_curso` (`id_participante`, `id_curso`, `fecha_inscripcion`, `FechaInicio`, `FechaFin`, `estatus_participante`) VALUES
(1,  28, '2026-04-28', '2026-05-04', '2026-06-26', 'En formación'),
(2,  28, '2026-04-30', '2026-05-04', '2026-06-26', 'En formación'),
(4,  28, '2026-05-01', '2026-05-04', '2026-06-26', 'En formación'),
(10, 28, '2026-05-02', '2026-05-04', '2026-06-26', 'En formación'),
(11, 28, '2026-05-02', '2026-05-04', '2026-06-26', 'En formación'),
(16, 28, '2026-05-03', '2026-05-04', '2026-06-26', 'En formación'),
(21, 28, '2026-05-04', '2026-05-04', '2026-06-26', 'En formación'),
(22, 28, '2026-05-04', '2026-05-04', '2026-06-26', 'En formación'),
(28, 28, '2026-05-04', '2026-05-04', '2026-06-26', 'En formación'),
(29, 28, '2026-05-04', '2026-05-04', '2026-06-26', 'En formación'),

(30, 29, '2026-05-02', '2026-05-11', '2026-07-03', 'En formación'),
(31, 29, '2026-05-05', '2026-05-11', '2026-07-03', 'En formación'),
(36, 29, '2026-05-08', '2026-05-11', '2026-07-03', 'En formación'),
(37, 29, '2026-05-10', '2026-05-11', '2026-07-03', 'En formación'),
(46, 29, '2026-05-10', '2026-05-11', '2026-07-03', 'En formación'),
(47, 29, '2026-05-11', '2026-05-11', '2026-07-03', 'En formación'),
(48, 29, '2026-05-11', '2026-05-11', '2026-07-03', 'En formación'),
(49, 29, '2026-05-11', '2026-05-11', '2026-07-03', 'En formación'),

(55, 30, '2026-05-10', '2026-05-18', '2026-07-10', 'En formación'),
(56, 30, '2026-05-12', '2026-05-18', '2026-07-10', 'En formación'),
(57, 30, '2026-05-14', '2026-05-18', '2026-07-10', 'En formación'),
(70, 30, '2026-05-15', '2026-05-18', '2026-07-10', 'En formación'),
(71, 30, '2026-05-16', '2026-05-18', '2026-07-10', 'En formación'),
(72, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(73, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(74, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(75, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(10, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(11, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(16, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),
(21, 30, '2026-05-18', '2026-05-18', '2026-07-10', 'En formación'),

(5,  31, '2026-05-15', '2026-06-01', '2026-07-15', 'Pendiente'),
(18, 31, '2026-05-15', '2026-06-01', '2026-07-15', 'Pendiente'),
(25, 31, '2026-05-16', '2026-06-01', '2026-07-15', 'Pendiente'),
(38, 31, '2026-05-18', '2026-06-01', '2026-07-15', 'Pendiente'),
(42, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Pendiente'),
(58, 31, '2026-05-20', '2026-06-01', '2026-07-15', 'Pendiente'),
(3,  31, '2026-05-20', '2026-06-01', '2026-07-15', 'Pendiente'),
(6,  31, '2026-05-20', '2026-06-01', '2026-07-15', 'Pendiente'),

(13, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Pendiente'),
(14, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Pendiente'),
(17, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Pendiente'),
(19, 32, '2026-05-20', '2026-06-08', '2026-07-20', 'Pendiente'),
(24, 32, '2026-05-21', '2026-06-08', '2026-07-20', 'Pendiente'),
(26, 32, '2026-05-21', '2026-06-08', '2026-07-20', 'Pendiente'),
(33, 32, '2026-05-22', '2026-06-08', '2026-07-20', 'Pendiente'),
(34, 32, '2026-05-22', '2026-06-08', '2026-07-20', 'Pendiente'),
(35, 32, '2026-05-23', '2026-06-08', '2026-07-20', 'Pendiente'),
(39, 32, '2026-05-23', '2026-06-08', '2026-07-20', 'Pendiente');


-- ============================================================
-- 8. SEMBRAR METAS (`tb_metas`)
-- ============================================================
-- Meta 1: Aprobados Globales Trimestre III 2025 (Cerrada y Alcanzada)
-- Objetivo: 20. Aprobados reales: Curso 2 (9), Curso 3 (10) = 19. Espera, pongamos 22 aprobados reales: Curso 2 (9), Curso 3 (10), Curso 4 (10 aprobados en Oct) = 29 aprobados. 
-- Registraremos Meta 1: Objetivo 20. Reales 29.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (1, NULL, 20, 29, 'Aprobados', '2025-08-01', '2025-09-30', 'Cerrada', 0, 'Trimestre III 2025');

-- Meta 2: Inscritos en CFS Trimestre IV 2025 (Cerrada y Alcanzada)
-- Objetivo: 25. CFS (id_ambito = 1) inscritos reales: Curso 6 (10), Curso 9 (10), Curso 12 (10) = 30.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (2, 1, 25, 30, 'Inscritos', '2025-10-01', '2025-12-31', 'Cerrada', 0, 'Inscritos CFS T-IV');

-- Meta 3: Aprobados Globales Trimestre IV 2025 (Cerrada y No Alcanzada)
-- Objetivo: 45. Aprobados reales en Oct-Dic: Curso 6 (10), Curso 7 (12), Curso 8 (11), Curso 9 (10), Curso 10 (10), Curso 11 (11) = 64. 
-- Para que sea "No Alcanzada" (No cumplida), pongamos un objetivo de 80.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (3, NULL, 80, 64, 'Aprobados', '2025-10-01', '2025-12-31', 'Cerrada', 0, 'Aprobados Globales T-IV');

-- Meta 4: Aprobados Globales Trimestre I 2026 (Cerrada y Alcanzada)
-- Objetivo: 35. Aprobados reales en Ene-Mar: Curso 12 (10), Curso 13 (11), Curso 14 (10), Curso 15 (10), Curso 16 (11), Curso 17 (10), Curso 18 (11), Curso 19 (12), Curso 21 (10), Curso 22 (11) = 106.
-- Con objetivo 35, reales 106, queda Alcanzada.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (4, NULL, 35, 106, 'Aprobados', '2026-01-01', '2026-03-31', 'Cerrada', 0, 'Aprobados Globales T-I 2026');

-- Meta 5: Inscripciones Globales Semestre I 2026 (Activa y En proceso)
-- Objetivo: 150. Inscripciones en Ene-May 2026 (en total registramos 51 participantes nuevos más inscripciones de alumnos anteriores). Supongamos 70 alumnos únicos.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (5, NULL, 150, 70, 'Inscritos', '2026-01-01', '2026-06-30', 'Activa', 0, 'Semestre I 2026');

-- Meta 6: Aprobados en Comunas (Activa y En proceso)
-- Objetivo: 15. Aprobados en id_ambito = 2 entre Abr y Jul 2026. Aprobados reales: Curso 23 (12). Total = 12.
INSERT INTO `tb_metas` (`id_meta`, `id_ambito`, `meta_objetivo`, `estudiantes_formados`, `tipo_objetivo`, `fecha_inicio`, `fecha_fin`, `estado`, `ambito_id`, `periodo_tiempo`) 
VALUES (6, 2, 15, 12, 'Aprobados', '2026-04-01', '2026-07-31', 'Activa', 0, 'Comunas Abr-Jul');


-- ============================================================
-- 9. SEMBRAR HISTORIAL DE AUDITORÍA Y TRAZABILIDAD PROGRESIVO (`tb_historial`)
-- ============================================================
INSERT INTO `tb_historial` (`tipo_actividad`, `descripcion`, `tabla_afectada`, `id_registro`, `usuario_id`, `fecha_hora`) VALUES
-- 2025
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-08-01 08:00:00'),
('Crear', 'Se creó el curso: Panadería Básica I', 'tb_curso', 2, 11, '2025-08-01 09:30:00'),
('Editar', 'Se asignó el docente Carlos Rodriguez al curso Panadería Básica I', 'tb_docente_curso', 1, 11, '2025-08-01 09:45:00'),
('Crear', 'Se registró el participante: Diego Romero', 'tb_participante', 1, 11, '2025-08-05 11:20:00'),
('Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2025-09-02 08:15:00'),
('Crear', 'Se registró el participante: Sofia Morillo', 'tb_participante', 7, 16, '2025-09-02 11:00:00'),
('Editar', 'Se completó y culminó el curso: Panadería Básica I', 'tb_curso', 2, 16, '2025-09-12 12:00:00'),
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-10-02 08:00:00'),
('Crear', 'Se registró la meta: Inscritos CFS T-IV', 'tb_metas', 2, 11, '2025-10-02 09:00:00'),
('Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2025-11-02 08:15:00'),
('Crear', 'Se creó el curso: Panadería y Pastelería Navideña', 'tb_curso', 9, 16, '2025-11-01 10:00:00'),
('Editar', 'Se completó y culminó el curso: Confección de Uniformes Escolares', 'tb_curso', 4, 16, '2025-10-17 12:00:00'),
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2025-12-02 08:00:00'),
('Crear', 'Se registró el participante: Florencia Fuentes', 'tb_participante', 31, 11, '2025-12-02 10:30:00'),
-- 2026
('Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-01-05 08:15:00'),
('Crear', 'Se creó el curso: Cálculo de Costos y Presupuestos', 'tb_curso', 14, 16, '2026-01-02 09:30:00'),
('Editar', 'Se completó y culminó el curso: Higiene y Manipulación de Alimentos I', 'tb_curso', 12, 16, '2026-01-09 12:00:00'),
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-02-02 08:00:00'),
('Crear', 'Se creó el curso: Dibujo Técnico Industrial', 'tb_curso', 20, 11, '2026-02-20 11:00:00'),
('Editar', 'Se completó y culminó el curso: Cálculo de Costos y Presupuestos', 'tb_curso', 14, 11, '2026-02-13 12:00:00'),
('Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-03-02 08:15:00'),
('Crear', 'Se creó el curso: Recreación y Dinámicas Infantiles', 'tb_curso', 24, 16, '2026-03-20 10:00:00'),
('Editar', 'Se completó y culminó el curso: Habilidades Socioemocionales I', 'tb_curso', 16, 16, '2026-03-13 12:00:00'),
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-04-02 08:00:00'),
('Crear', 'Se creó el curso: Base de Datos Relacionales', 'tb_curso', 25, 11, '2026-04-02 09:30:00'),
('Editar', 'Se completó y culminó el curso: Confección de Prendas de Vestir', 'tb_curso', 19, 11, '2026-04-10 12:00:00'),
('Login', 'Usuario samu inició sesión', 'tb_usuario', 16, 16, '2026-05-02 08:15:00'),
('Crear', 'Se registró la meta: Comunas Abr-Jul', 'tb_metas', 6, 16, '2026-05-02 09:00:00'),
('Crear', 'Se creó el curso: Mercadeo y Ventas Digitales II', 'tb_curso', 28, 16, '2026-05-01 10:30:00'),
('Editar', 'Se completó y culminó el curso: Higiene y Manipulación de Alimentos II', 'tb_curso', 23, 16, '2026-05-08 12:00:00'),
('Login', 'Usuario ADMIN inició sesión', 'tb_usuario', 11, 11, '2026-05-24 21:15:00');

COMMIT;

-- ============================================================
-- CAPA 2: TRIGGERs DE PROTECCIÓN PARA tb_usuario
-- ============================================================
-- Estos triggers impiden INSERT, UPDATE y DELETE en tb_usuario
-- a menos que se establezca la variable de sesión MySQL:
--   SET @ALLOW_USER_CHANGES = 'INCES_TESIS_2026_CLAVE_PROTEGIDA';
--
-- La variable solo vive durante la conexión MySQL actual.
-- El código PHP la establece automáticamente cuando la
-- contraseña maestra es correcta.
-- ============================================================

-- Eliminar triggers existentes si los hay
DROP TRIGGER IF EXISTS proteger_usuario_insert;
DROP TRIGGER IF EXISTS proteger_usuario_update;
DROP TRIGGER IF EXISTS proteger_usuario_delete;

-- TRIGGER: Bloquear INSERT (crear usuarios)
DELIMITER //
CREATE TRIGGER proteger_usuario_insert
BEFORE INSERT ON tb_usuario
FOR EACH ROW
BEGIN
    IF @ALLOW_USER_CHANGES IS NULL OR @ALLOW_USER_CHANGES != 'INCES_TESIS_2026_CLAVE_PROTEGIDA' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'PROTEGIDO: No se permite crear usuarios. Sistema protegido para entrega de tesis.';
    END IF;
END //
DELIMITER ;

-- TRIGGER: Bloquear UPDATE (editar usuarios)
-- EXCEPCIÓN: Permite cambios SOLO en password, codigo_recuperacion y codigo_expiracion
-- (para que la recuperación de contraseña siga funcionando)
DELIMITER //
CREATE TRIGGER proteger_usuario_update
BEFORE UPDATE ON tb_usuario
FOR EACH ROW
BEGIN
    IF @ALLOW_USER_CHANGES IS NULL OR @ALLOW_USER_CHANGES != 'INCES_TESIS_2026_CLAVE_PROTEGIDA' THEN
        -- Verificar si solo se están cambiando campos permitidos (password, códigos de recuperación)
        IF NOT (
            OLD.primer_nombre <=> NEW.primer_nombre
            AND OLD.segundo_nombre <=> NEW.segundo_nombre
            AND OLD.primer_apellido <=> NEW.primer_apellido
            AND OLD.segundo_apellido <=> NEW.segundo_apellido
            AND OLD.nombre_usuario <=> NEW.nombre_usuario
            AND OLD.rol <=> NEW.rol
            AND OLD.estado_usuario <=> NEW.estado_usuario
            AND OLD.correo <=> NEW.correo
            AND OLD.imagen <=> NEW.imagen
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'PROTEGIDO: No se permite editar usuarios. Sistema protegido para entrega de tesis.';
        END IF;
        -- Si solo cambia password, codigo_recuperacion o codigo_expiracion → se permite sin clave
    END IF;
END //
DELIMITER ;

-- TRIGGER: Bloquear DELETE (eliminar usuarios)
DELIMITER //
CREATE TRIGGER proteger_usuario_delete
BEFORE DELETE ON tb_usuario
FOR EACH ROW
BEGIN
    IF @ALLOW_USER_CHANGES IS NULL OR @ALLOW_USER_CHANGES != 'INCES_TESIS_2026_CLAVE_PROTEGIDA' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'PROTEGIDO: No se permite eliminar usuarios. Sistema protegido para entrega de tesis.';
    END IF;
END //
DELIMITER ;

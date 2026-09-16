<?php
/**
 * ============================================================
 * SISTEMA DE PROTECCIÓN CON CONTRASEÑA MAESTRA
 * ============================================================
 * 
 * Este archivo protege las operaciones de gestión de usuarios
 * (crear, editar, eliminar) mediante una contraseña maestra
 * hasheada con bcrypt.
 * 
 * La contraseña NO se almacena en texto plano.
 * El hash bcrypt es IRREVERSIBLE: no se puede obtener la
 * contraseña original a partir del hash.
 * 
 * CAPA 1 de protección (PHP).
 * CAPA 2 de protección (MySQL TRIGGER) se desbloquea desde aquí.
 * 
 * @author Sistema de Gestión INCES
 * @version 1.0
 */

// ==================== CONFIGURACIÓN ====================

/**
 * Hash bcrypt de la contraseña maestra.
 * Generado con: password_hash('...', PASSWORD_BCRYPT, ['cost' => 12])
 * 
 * ⚠️ NO se puede revertir este hash a la contraseña original.
 * ⚠️ NO modificar este valor sin conocer la contraseña original.
 */
define('MASTER_PASSWORD_HASH', '$2y$12$JM5aWw2IuoiE/5X9VaWVuOiYLw2NX6wv0pi8kzDAkaPiqg2A5Pzye');

/**
 * Modo protegido: si es true, se requiere contraseña maestra
 * para crear, editar o eliminar usuarios.
 * 
 * Cambiar a false SOLO para deshabilitar temporalmente la protección.
 */
define('MODO_PROTEGIDO', true);

/**
 * Clave secreta para desbloquear los TRIGGERs de MySQL.
 * Esta clave se establece como variable de sesión MySQL
 * (@ALLOW_USER_CHANGES) antes de ejecutar operaciones protegidas.
 * 
 * ⚠️ Debe coincidir EXACTAMENTE con la clave definida en los TRIGGERs.
 */
define('MYSQL_TRIGGER_KEY', 'INCES_TESIS_2026_CLAVE_PROTEGIDA');

// ==================== FUNCIONES ====================

/**
 * Verifica si el modo protegido está activo.
 * 
 * @return bool True si la protección está activa
 */
function esModoProtegido() {
    return MODO_PROTEGIDO === true;
}

/**
 * Verifica la contraseña maestra contra el hash almacenado.
 * 
 * @param string $input La contraseña proporcionada por el usuario
 * @return bool True si la contraseña es correcta
 */
function verificarContraseñaMaestra($input) {
    if (empty($input)) {
        return false;
    }
    return password_verify($input, MASTER_PASSWORD_HASH);
}

/**
 * Valida la contraseña maestra y retorna un error JSON si falla.
 * Uso: llamar al inicio de cada endpoint protegido.
 * 
 * @param string|null $masterPassword La contraseña maestra enviada en POST
 * @return bool True si la validación pasó, false si falló (y ya envió respuesta JSON)
 */
function validarAccesoMaestro($masterPassword) {
    if (!esModoProtegido()) {
        return true; // Protección desactivada, permitir
    }
    
    if (empty($masterPassword)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '🔒 Contraseña maestra requerida. El sistema está protegido.'
        ]);
        return false;
    }
    
    if (!verificarContraseñaMaestra($masterPassword)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '🔒 Contraseña maestra incorrecta. Acceso denegado.'
        ]);
        return false;
    }
    
    return true;
}

/**
 * Desbloquea los TRIGGERs de MySQL estableciendo la variable de sesión.
 * Debe llamarse ANTES de ejecutar INSERT/UPDATE/DELETE en tb_usuario.
 * La variable solo vive durante la conexión actual de MySQL.
 * 
 * @param PDO $conn La conexión PDO activa
 * @return void
 */
function desbloquearTriggerMySQL($conn) {
    $key = MYSQL_TRIGGER_KEY;
    $conn->exec("SET @ALLOW_USER_CHANGES = '$key'");
}
?>

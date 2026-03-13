<?php
require_once '../../modelo/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_docente_curso = $_POST['id_docente_curso'] ?? null;
        
        if (!$id_docente_curso) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de asignación no proporcionado'
            ]);
            exit;
        }
        
        $conexion = new Conexion();
        $conn = $conexion->getConexion();
        
        // Obtener id_docente antes de eliminar
        $sqlObtener = "SELECT id_docente FROM tb_docente_curso WHERE id_docente_curso = :id";
        $stmtObtener = $conn->prepare($sqlObtener);
        $stmtObtener->execute([':id' => $id_docente_curso]);
        $asignacion = $stmtObtener->fetch(PDO::FETCH_ASSOC);
        
        if (!$asignacion) {
            echo json_encode([
                'success' => false,
                'message' => 'Asignación no encontrada'
            ]);
            exit;
        }
        
        // Eliminar asignación
        $sql = "DELETE FROM tb_docente_curso WHERE id_docente_curso = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_docente_curso]);
        
        // Verificar si el docente aún tiene cursos asignados
        $sqlVerificar = "SELECT COUNT(*) as total FROM tb_docente_curso WHERE id_docente = :id_docente";
        $stmtVerificar = $conn->prepare($sqlVerificar);
        $stmtVerificar->execute([':id_docente' => $asignacion['id_docente']]);
        $cursosRestantes = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
        
        // Actualizar estado del docente
        $nuevoEstado = ($cursosRestantes['total'] > 0) ? 'Asignado' : 'Disponible';
        $sqlActualizar = "UPDATE tb_docente SET estado_docente = :estado WHERE id_docente = :id_docente";
        $stmtActualizar = $conn->prepare($sqlActualizar);
        $stmtActualizar->execute([
            ':estado' => $nuevoEstado,
            ':id_docente' => $asignacion['id_docente']
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Asignación eliminada correctamente'
        ]);
        
    } catch (Exception $e) {
        error_log("Error en eliminarAsignacionCurso.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la asignación'
        ]);
    }
}
?>
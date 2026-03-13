<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../modelo/conexion.php';

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    // Actualizamos dinámicamente el estado igual que managerCurso para garantizar precisión
    // 1 o 3 -> 2 (Diferido): Llegó la fecha límite o de inicio, y faltan inscritos o docentes (Si estaba 'En proceso' por error, regresa a Diferido)
    $sqlDiferido = "UPDATE tb_curso c SET id_estatusCurso = 2 
                    WHERE id_estatusCurso IN (1, 3) 
                    AND (fecha_inicio <= CURDATE() OR (fecha_limite_inicio IS NOT NULL AND fecha_limite_inicio <= CURDATE()))
                    AND (num_inscritos < IFNULL(c.min_participantes, 12) OR NOT EXISTS (SELECT 1 FROM tb_docente_curso dc WHERE dc.id_curso = c.id_curso))";
    
    // 1,2 -> 3 (En Proceso): Todo listo, inscritos completos, fecha alcanzada
    $sqlEnProceso = "UPDATE tb_curso c SET id_estatusCurso = 3 
                     WHERE id_estatusCurso IN (1, 2) 
                     AND (fecha_inicio <= CURDATE() OR (fecha_limite_inicio IS NOT NULL AND fecha_limite_inicio <= CURDATE()))
                     AND num_inscritos >= IFNULL(c.min_participantes, 12) 
                     AND EXISTS (SELECT 1 FROM tb_docente_curso dc WHERE dc.id_curso = c.id_curso)";

    // 3 -> 5 (Culminado): Pasó la fecha fin
    $sqlCulminado = "UPDATE tb_curso SET id_estatusCurso = 5 
                     WHERE id_estatusCurso = 3 AND fecha_fin < CURDATE()";

    $conn->exec($sqlDiferido);
    $conn->exec($sqlEnProceso);
    $conn->exec($sqlCulminado);


    // Obtener los cursos en Estado 2(Diferido), 3(En Proceso), 5(Culminado)
    $sql = "SELECT id_curso, nombre_curso, id_estatusCurso, fecha_inicio, fecha_fin, fecha_actualizacion, fecha_creacion
            FROM tb_curso 
            WHERE id_estatusCurso IN (2, 3, 5)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $notificaciones = [];
    $hoy = date('Y-m-d');

    foreach ($cursos as $curso) {
        $evento = null;

        // Determinamos el título, tipo y fecha que originó este evento basado en el estado
        if ($curso['id_estatusCurso'] == 3) {
            // EN PROCESO (Color Primary/Azul)
            $evento = [
                'id' => $curso['id_curso'],
                'tipo' => 'en_proceso',
                'titulo' => 'Curso en Proceso',
                'mensaje' => "El curso: " . $curso['nombre_curso'] . " ha iniciado exitosamente.",
                'fecha_evento' => $curso['fecha_inicio'],
                'color' => '#0ea5e9', // Azul
                'icono' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/></svg>'
            ];
        } elseif ($curso['id_estatusCurso'] == 2) {
            // DIFERIDO (Color Warning/Naranja)
            $evento = [
                'id' => $curso['id_curso'],
                'tipo' => 'diferido',
                'titulo' => 'Curso Diferido',
                'mensaje' => "El curso: " . $curso['nombre_curso'] . " fue diferido. Faltan requisitos o docentes.",
                'fecha_evento' => $curso['fecha_inicio'],
                'color' => '#f59e0b', // Naranja/Amarillo
                'icono' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.25 5C5.56 5 5 5.56 5 6.25v3.5a1.25 1.25 0 1 0 2.5 0v-3.5C7.5 5.56 6.94 5 6.25 5zm3.5 0c-.69 0-1.25.56-1.25 1.25v3.5a1.25 1.25 0 1 0 2.5 0v-3.5C11 5.56 10.44 5 9.75 5z"/></svg>'
            ];
        } elseif ($curso['id_estatusCurso'] == 5) {
            // CULMINADO (Color Rojo)
            $evento = [
                'id' => $curso['id_curso'],
                'tipo' => 'culminado',
                'titulo' => 'Curso Culminado',
                'mensaje' => "¡El curso: " . $curso['nombre_curso'] . " ha finalizado exitosamente!",
                'fecha_evento' => $curso['fecha_fin'],
                'color' => '#ef4444', // Rojo
                'icono' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>'
            ];
        }

        // Si tenemos un evento, verificamos si es medianamente relevante
        // En un caso real mostraríamos solo del último mes o semana, o manejar una tabla de "vistos"
        // Para este proyecto validaremos notificaciones que hayan ocurrido en el último par de años(simplificado) o simplemente la mandamos.
        // Nos basaremos en `fecha_actualizacion` o si no, su `fecha_evento`
        if ($evento) {
             // Agregamos un timestamp universal para el ordenamiento
             $fechaOrd = $curso['fecha_actualizacion'] ?? $curso['fecha_creacion'] ?? $evento['fecha_evento'] . " 00:00:00";
             $evento['timestamp'] = strtotime($fechaOrd);
             
             // Identificador único (id de la "notificación" generada sobre la marcha)
             // Formato tipo: curso-12-estado-3
             $evento['notif_id'] = "curso-" . $curso['id_curso'] . "-est-" . $curso['id_estatusCurso'];

             $notificaciones[] = $evento;
        }
    }

    // Ordenamos las notificaciones: Las más recientes de primero (DESC)
    usort($notificaciones, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    // Enviar las últimas 15 notificaciones
    $notificaciones = array_slice($notificaciones, 0, 15);

    echo json_encode([
        'success' => true,
        'data' => $notificaciones
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'data' => [],
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>

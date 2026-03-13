<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'c:/xampp/htdocs/Sistema de Gestion Inces/app/controlador/Curso/managerCurso.php';

try {
    $curso = new Curso();
    $id_curso = 1; // Assuming course 1 exists and is the one we are testing.
    
    echo "=== ATTEMPTING TO ADD PARTICIPANT ===\n";
    
    // Get a available participant
    $disponibles = $curso->obtenerParticipantesDisponibles($id_curso, '');
    
    if (count($disponibles) > 0) {
        $p = $disponibles[0];
        $id_part = $p['id_participante'];
        echo "Adding Participant ID: $id_part to Course $id_curso\n";
        
        $result = $curso->agregarParticipante($id_part, $id_curso);
        print_r($result);
        
        if ($result['success']) {
            echo "Successfully added. Now verifying in enrolled list...\n";
            $inscritos = $curso->obtenerParticipantesInscritos($id_curso);
            $found = false;
            foreach ($inscritos as $i) {
                if ($i['id_participante'] == $id_part) {
                    $found = true;
                    break;
                }
            }
            echo $found ? "Participant found in enrolled list.\n" : "Participant NOT found in enrolled list!\n";
            
            // Cleanup
            echo "Removing participant to clean up...\n";
            // Assuming there is a remove function, but for now we just want to see if add works.
            // checking managerCurso.php for remove function would be good.
            // But let's just see if ADD fails.
        }
    } else {
        echo "No participants available to test addition.\n";
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>

<?php
require_once __DIR__ . '/managerParticipante.php';
require_once __DIR__ . '/Docente/managerDocente.php';

header('Content-Type: application/json');

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    $response = ['exists' => false, 'table' => null, 'data' => null];

    // Verificar en Participantes
    $participanteManager = new Participante();
    $sqlP = "SELECT p.*, t.numero_de_telefono as telefono 
             FROM tb_participante p 
             LEFT JOIN tb_telefono t ON p.id_participante = t.id_participante AND t.tipo_de_telefono = 'Móvil'
             WHERE p.cedula = :cedula LIMIT 1";
    $stmtP = $participanteManager->getConexion()->prepare($sqlP);
    $stmtP->execute([':cedula' => $cedula]);
    $participante = $stmtP->fetch(PDO::FETCH_ASSOC);

    if ($participante) {
        $response['exists'] = true;
        $response['table'] = 'participantes';
        $response['data'] = $participante;
    } else {
        // Verificar en Docentes
        $docenteManager = new docente();
        $sqlD = "SELECT d.*, t.numero_de_telefono as telefono 
                 FROM tb_docente d 
                 LEFT JOIN tb_telefono t ON d.id_docente = t.id_docente AND t.tipo_de_telefono = 'Móvil'
                 WHERE d.cedula = :cedula LIMIT 1";
        $stmtD = $docenteManager->getConexion()->prepare($sqlD);
        $stmtD->execute([':cedula' => $cedula]);
        $docente = $stmtD->fetch(PDO::FETCH_ASSOC);

        if ($docente) {
            $response['exists'] = true;
            $response['table'] = 'docentes';
            $response['data'] = $docente;
        }
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Cédula no proporcionada']);
}

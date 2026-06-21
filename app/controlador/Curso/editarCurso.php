<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once 'managerCurso.php';
require_once '../../modelo/HistorialUtil.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $data = [
        'id_curso'                  => $_POST['id_curso']                  ?? null,
        'nombre_curso-Edit'         => $_POST['nombre_curso-Edit']         ?? null,
        'preimpreso-Edit'           => $_POST['preimpreso-Edit']           ?? null,
        'id_ppl-Edit'               => $_POST['id_ppl-Edit']               ?? null,
        'id_nombrecurso-Edit'       => $_POST['id_nombrecurso-Edit']       ?? null,
        'fecha_inicio-Edit'         => $_POST['fecha_inicio-Edit']         ?? null,
        'fecha_fin-Edit'            => $_POST['fecha_fin-Edit']            ?? null,
        'fecha_limite_inicio-Edit'  => $_POST['fecha_limite_inicio-Edit']  ?? null,
        'max_participantes-Edit'    => $_POST['max_participantes-Edit']    ?? null,
        'min_participantes-Edit'    => $_POST['min_participantes-Edit']    ?? 10,
        'horas_totales-Edit'        => $_POST['horas_totales-Edit']        ?? null,
        'horas_por_clase-Edit'      => $_POST['horas_por_clase-Edit']      ?? null,
        'id_modalidad-Edit'         => $_POST['id_modalidad-Edit']         ?? null,
        'tipo_formacion-Edit'       => $_POST['tipo_formacion-Edit']       ?? null,
        'id_estatusCurso-Edit'      => $_POST['id_estatusCurso-Edit']      ?? null,
        'id_sectoreconomico-Edit'   => $_POST['id_sectoreconomico-Edit']   ?? null,
        'id_subtipo-Edit'           => $_POST['id_subtipo-Edit']           ?? null,
        'id_ambito-Edit'            => $_POST['id_ambito-Edit']            ?? null,
        'turno-Edit'                => $_POST['turno-Edit']                ?? null,
        'programa_formacion-Edit'   => $_POST['programa_formacion-Edit']   ?? null
    ];

    $required = [
        'id_curso'          => 'ID de curso',
        'nombre_curso-Edit' => 'Nombre del curso',
        'id_ppl-Edit'       => 'Perfil productivo laboral',
        'fecha_inicio-Edit' => 'Fecha de inicio',
        'fecha_fin-Edit'    => 'Fecha de fin',
        'turno-Edit'        => 'Turno'
    ];

    foreach ($required as $field => $label) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "El campo '$label' es obligatorio"]);
            exit;
        }
    }

    $curso = new Curso();
    $resultado = $curso->editar($data);

    if ($resultado['success']) {
        // Registrar en historial
        if (isset($_SESSION['id_usuario'])) {
            $nombreCurso = $data['nombre_curso-Edit'] ?? 'Curso ID: ' . $data['id_curso'];
            HistorialUtil::registrarEditarCurso($data['id_curso'], $nombreCurso, $_SESSION['id_usuario']);
        }
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode($resultado);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
}
?>
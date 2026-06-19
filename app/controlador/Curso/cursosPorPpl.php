<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado', 'cursos' => []]);
    exit;
}

$idPpl = isset($_GET['id_ppl']) ? (int) $_GET['id_ppl'] : 0;
if ($idPpl <= 0) {
    echo json_encode(['success' => true, 'cursos' => []]);
    exit;
}

require_once __DIR__ . '/../../modelo/conexion.php';

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    $sql = "SELECT nc.id_nombrecurso, nc.nombre_curso
            FROM tb_ppl_nombrecurso pnc
            INNER JOIN tb_nombrecurso nc ON nc.id_nombrecurso = pnc.id_nombrecurso
            WHERE pnc.id_ppl = :id_ppl
            ORDER BY nc.nombre_curso ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_ppl' => $idPpl]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'cursos' => $cursos]);
} catch (Throwable $e) {
    error_log('cursosPorPpl: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudieron cargar los cursos del perfil. Verifique que exista la tabla tb_ppl_nombrecurso (ejecute sql/tb_ppl_nombrecurso.sql).',
        'cursos' => [],
    ]);
}

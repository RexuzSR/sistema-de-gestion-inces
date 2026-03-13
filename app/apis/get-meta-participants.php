<?php
// api/get-meta-participants.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuración XAMPP
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'inces1';

// Conexión
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Conexión fallida']));
}

// Parámetros
$id_meta = $_GET['id_meta'] ?? $_POST['id_meta'] ?? 0;
$tipo_objetivo = $_GET['tipo_objetivo'] ?? $_POST['tipo_objetivo'] ?? 'Inscritos';

if (!$id_meta) {
    echo json_encode(['success' => false, 'error' => 'ID de meta requerido']);
    exit;
}

// Obtener información de la meta
$query_meta = "SELECT * FROM tb_metas WHERE id_meta = ?";
$stmt_meta = $conn->prepare($query_meta);
$stmt_meta->bind_param("i", $id_meta);
$stmt_meta->execute();
$meta = $stmt_meta->get_result()->fetch_assoc();

if (!$meta) {
    echo json_encode(['success' => false, 'error' => 'Meta no encontrada']);
    exit;
}

// Obtener participantes según el ámbito
$id_ambito = $meta['id_ambito'];
$query_participantes = "
    SELECT 
        p.*, 
        d.nombre_discapacidad,
        t.numero_de_telefono,
        c.id_curso,
        c.nombre_curso,
        c.horas_totales,
        c.turno,
        m.nombre_modalidad,
        a.nombre_areaFormativa,
        pc.estatus_participante,
        pc.fecha_inscripcion,
        pc.FechaInicio as fecha_inicio_curso,
        pc.FechaFin as fecha_fin_curso
    FROM tb_participante p
    LEFT JOIN tb_discapacidad d ON p.id_discapacidad = d.id_discapacidad
    LEFT JOIN tb_telefono t ON p.id_participante = t.id_participante
    LEFT JOIN tb_participante_curso pc ON p.id_participante = pc.id_participante
    LEFT JOIN tb_curso c ON pc.id_curso = c.id_curso
    LEFT JOIN tb_modalidad m ON c.id_modalidad = m.id_modalidad
    LEFT JOIN tb_areaformativa a ON c.id_areaFormativa = a.id_areaFormativa
    WHERE c.id_ambito = ?
    " . ($tipo_objetivo === 'Aprobados' ? "AND pc.estatus_participante = 'Aprobado'" : "") . "
    ORDER BY p.primer_apellido, p.primer_nombre
";

$stmt = $conn->prepare($query_participantes);
$stmt->bind_param("i", $id_ambito);
$stmt->execute();
$result = $stmt->get_result();

$participantes = [];
while ($row = $result->fetch_assoc()) {
    $participantes[] = $row;
}

echo json_encode([
    'success' => true,
    'meta' => $meta,
    'participantes' => $participantes,
    'total' => count($participantes)
]);

$conn->close();
?>
<?php
require_once '../../modelo/conexion.php';

if (isset($_GET['id_docente'])) {
    $id_docente = $_GET['id_docente'];

    try {
        $conexion = new Conexion();
        $conn = $conexion->getConexion();

        // Consultar datos del docente por ID
        $stmt = $conn->prepare("SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, correo, fecha_nacimiento, edad, estado_docente, genero, tipo_docente FROM tb_docente WHERE id_docente = ?");
        $stmt->execute([$id_docente]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $docente = [
                'nombre' => trim(($row['primer_nombre'] ?? '') . ' ' . ($row['segundo_nombre'] ?? '')),
                'apellido' => trim(($row['primer_apellido'] ?? '') . ' ' . ($row['segundo_apellido'] ?? '')),
                'cedula' => $row['cedula'] ?? null,
                'correo' => $row['correo'] ?? null,
                'fecha_nacimiento' => $row['fecha_nacimiento'] ?? null,
                'edad' => $row['edad'] ?? null,
                'estado_docente' => $row['estado_docente'] ?? null,
                'genero' => $row['genero'] ?? null,
                'tipo_docente' => $row['tipo_docente'] ?? null
            ];

            echo json_encode($docente);
        } else {
            echo json_encode(['error' => 'Docente no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener docente: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID de docente no proporcionado']);
}

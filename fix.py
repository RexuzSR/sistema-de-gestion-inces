import re

filename = r'c:\xampp\htdocs\Sistema de Gestion Inces\app\controlador\Reporte\api_reportes.php'

with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

# Pattern to match the corrupted SQL block
pattern = r"SELECT DISTINCT\s+p\.id_participante,\s+':fecha_inicio' => \$fechaInicio,"

replacement = """SELECT DISTINCT 
                p.id_participante,
                CONCAT(p.primer_nombre, ' ', p.primer_apellido) as nombre_completo,
                p.cedula,
                c.nombre_curso,
                pc.FechaFin as fecha_aprobacion
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE " . (empty($meta['id_ambito']) ? "" : "c.id_ambito = :id_ambito AND") . "
              pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        ";

        $params = [
            ':fecha_inicio' => $fechaInicio,"""

content_new = re.sub(pattern, replacement, content)

with open(filename, 'w', encoding='utf-8') as f:
    f.write(content_new)

print("Replaced!")

<?php
$file = 'app/controlador/Reporte/api_reportes.php';
$content = file_get_contents($file);

$pattern = "/SELECT DISTINCT\s+p\.id_participante,\s+':fecha_inicio' => \\\$fechaInicio,/s";

$replacement = "SELECT DISTINCT 
                p.id_participante,
                CONCAT(p.primer_nombre, ' ', p.primer_apellido) as nombre_completo,
                p.cedula,
                c.nombre_curso,
                pc.FechaFin as fecha_aprobacion
            FROM tb_participante_curso pc
            INNER JOIN tb_curso c ON c.id_curso = pc.id_curso
            INNER JOIN tb_participante p ON p.id_participante = pc.id_participante
            WHERE \" . (empty(\$meta['id_ambito']) ? \"\" : \"c.id_ambito = :id_ambito AND\") . \"
              pc.estatus_participante = 'Aprobado'
              AND pc.FechaFin BETWEEN :fecha_inicio AND :fecha_fin
        \";

        \$params = [
            ':fecha_inicio' => \$fechaInicio,";

$content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $content);
echo "Regex Replaced!";

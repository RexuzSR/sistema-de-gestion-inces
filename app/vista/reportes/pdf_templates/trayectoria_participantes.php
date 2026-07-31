<?php
// Variables disponibles:
// $usuarioFull, $txtAnio, $txtMeses, $txtGenero, $txtEstado, $txtEdad, $txtConCurso, $txtConAprobado
// $kpiData (total, activos, sin_curso), $estadoEnSistema, $estadoDisponible, $estadoEnFormacion
// $filtrados (array), $totalEncontrados, $conAprobado, $detalleHtml
// $logoGobierno, $logoInces, $fechaGeneracion
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Trayectoria de Participantes</title>
    <style>
        /* DOMPDF Compatible CSS 2.1 - Portrait */
        @page { margin: 120px 50px 70px 50px; }
        
        body { 
            font-family: "Helvetica", "Arial", sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.4; 
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -90px; 
            left: 0px; 
            right: 0px; 
            height: 70px; 
            border-bottom: 3px solid #0056b3; 
            padding-bottom: 10px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 55px; }
        .header-title { text-align: center; }
        .header-title h1 { margin: 0; font-size: 18px; color: #0056b3; text-transform: uppercase; letter-spacing: 1px; }
        .header-title p { margin: 3px 0 0 0; font-size: 11px; color: #666; }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            border-top: 1px solid #ccc; 
            font-size: 9px; 
            color: #777; 
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; }
        .page-number:after { content: counter(page); }

        /* Utilidades */
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
        
        /* Títulos de Sección */
        .section-title {
            background-color: #343a40;
            color: #fff;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0 15px 0;
            border-radius: 3px;
        }

        /* KPIs Globales */
        .kpi-row { width: 100%; margin-bottom: 20px; text-align: center; }
        .kpi-row table { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 8px 0; }
        .kpi-card { 
            background: #f8f9fa; 
            border: 1px solid #e9ecef; 
            border-radius: 4px; 
            padding: 10px 2px; 
            text-align: center;
        }
        .kpi-card h3 { font-size: 16px; margin: 0 0 5px 0; color: #333; }
        .kpi-card p { font-size: 9px; margin: 0; color: #666; text-transform: uppercase; font-weight: bold; line-height: 1.1; }

        .filter-box { background: #e9ecef; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 10px; }
        .filter-box strong { color: #0056b3; }

        /* Tablas de Datos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 10px; 
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 6px 4px; 
        }
        table.data-table th { 
            background-color: #f1f3f5; 
            color: #495057; 
            font-weight: bold; 
            text-align: center; 
        }
        table.data-table tbody tr:nth-child(even) td { background-color: #f8f9fa; }

        /* Detalle Participante */
        .detalle-box { background:#f8f9fa; padding:15px; border-radius:5px; border-left:4px solid #0056b3; margin-bottom:20px; }
        .detalle-box h4 { color:#0056b3; margin-top:0; font-size: 12px; margin-bottom: 5px;}
        .detalle-box p { margin: 0 0 4px 0; font-size: 11px; }
    </style>
</head>
<body>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 25%;"><img src="<?php echo $logoGobierno; ?>" class="logo" alt="Gobierno"></td>
                <td style="width: 50%;" class="header-title">
                    <h1>Informe Gerencial</h1>
                    <p>Trayectoria de Participantes</p>
                </td>
                <td style="width: 25%; text-align: right;"><img src="<?php echo $logoInces; ?>" class="logo" alt="INCES"></td>
            </tr>
        </table>
    </header>

    <!-- Footer Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 35%;">Generado por: <?php echo htmlspecialchars($usuarioFull); ?></td>
                <td style="width: 30%; text-align: center;">Página <span class="page-number"></span></td>
                <td style="width: 35%; text-align: right;">Fecha: <?php echo $fechaGeneracion; ?></td>
            </tr>
        </table>
    </footer>

    <!-- SECCIÓN 1: RESUMEN GENERAL (KPIs) -->
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="font-size: 16px; color: #343a40; margin-bottom: 5px;">Análisis de Trayectoria Formativa</h2>
    </div>

    <div class="kpi-row">
        <table>
            <tr>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #0d6efd;">
                        <h3><?php echo number_format($kpiData['total'], 0, '', '.'); ?></h3>
                        <p>Totales</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #198754;">
                        <h3><?php echo number_format($kpiData['activos'], 0, '', '.'); ?></h3>
                        <p>Con Cursos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #ffc107;">
                        <h3><?php echo number_format($kpiData['sin_curso'], 0, '', '.'); ?></h3>
                        <p>Sin Cursos</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #0dcaf0;">
                        <h3><?php echo number_format($estadoEnSistema, 0, '', '.'); ?></h3>
                        <p>En Sistema</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #20c997;">
                        <h3><?php echo number_format($estadoDisponible, 0, '', '.'); ?></h3>
                        <p>Disponibles</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-card" style="border-top: 3px solid #6f42c1;">
                        <h3><?php echo number_format($estadoEnFormacion, 0, '', '.'); ?></h3>
                        <p>En Formación</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- SECCIÓN 2: FILTROS APLICADOS -->
    <div class="filter-box">
        <strong>Filtros Aplicados:</strong><br>
        Participación: <?php echo htmlspecialchars($txtConCurso); ?> &nbsp;|&nbsp;
        Certificados: <?php echo htmlspecialchars($txtConAprobado); ?> &nbsp;|&nbsp;
        Año: <?php echo htmlspecialchars($txtAnio); ?> &nbsp;|&nbsp;
        Mes: <?php echo htmlspecialchars($txtMeses); ?> &nbsp;|&nbsp;
        Género: <?php echo htmlspecialchars($txtGenero); ?> &nbsp;|&nbsp;
        Estado: <?php echo htmlspecialchars($txtEstado); ?> &nbsp;|&nbsp;
        Edad: <?php echo htmlspecialchars($txtEdad); ?>
    </div>

    <!-- SECCIÓN 3: TABLA DE PARTICIPANTES -->
    <div class="section-title">Listado de Participantes (<?php echo $totalEncontrados; ?> registros)</div>
    <table class="data-table">
        <thead>
            <?php if ($conAprobado == '1'): ?>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:10%;">Cédula</th>
                <th style="width:14%;">Nombre</th>
                <th style="width:14%;">Apellido</th>
                <th style="width:11%;">Teléfono</th>
                <th style="width:18%;">Correo</th>
                <th style="width:30%;">Cursos Aprob.</th>
            </tr>
            <?php else: ?>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:11%;">Cédula</th>
                <th style="width:13%;">Nombre</th>
                <th style="width:13%;">Apellido</th>
                <th style="width:8%;">Género</th>
                <th style="width:5%;">Edad</th>
                <th style="width:11%;">Teléfono</th>
                <th style="width:15%;">Correo</th>
                <th style="width:10%;">Registro</th>
                <th style="width:10%;">Estado</th>
            </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php 
            $countP = 0;
            foreach ($filtrados as $p): 
                $countP++;
                if ($conAprobado == '1') {
                    $cursosArray = !empty($p['cursos_aprobados']) ? explode(', ', $p['cursos_aprobados']) : [];
                    if (!empty($cursosArray)) {
                        $escapedCursos = array_map('htmlspecialchars', $cursosArray);
                        $cursosHtml = '<div style="font-size: 7.5px; line-height: 1.15; color: #333; text-align: left;">';
                        $cursosHtml .= implode(' &bull; ', $escapedCursos);
                        $cursosHtml .= '</div>';
                    } else {
                        $cursosHtml = '—';
                    }
            ?>
                <tr>
                    <td class="text-center"><?php echo $countP; ?></td>
                    <td><?php echo htmlspecialchars($p['cedula']); ?></td>
                    <td><?php echo htmlspecialchars($p['primer_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['primer_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($p['correo']); ?></td>
                    <td><?php echo $cursosHtml; ?></td>
                </tr>
            <?php 
                } else {
                    $est = $p['estado'];
                    if (strpos($est, 'formaci') !== false && (strpos($est, '?') !== false || strpos($est, 'n') !== false)) {
                        $est = 'En formación';
                    }
                    $fechaR = (!empty($p['fecha_registro']) && $p['fecha_registro'] !== '0000-00-00') ? date('d/m/Y', strtotime($p['fecha_registro'])) : '—';
            ?>
                <tr>
                    <td class="text-center"><?php echo $countP; ?></td>
                    <td><?php echo htmlspecialchars($p['cedula']); ?></td>
                    <td><?php echo htmlspecialchars($p['primer_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['primer_apellido']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($p['genero']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($p['edad']); ?></td>
                    <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($p['correo']); ?></td>
                    <td class="text-center"><?php echo $fechaR; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($est); ?></td>
                </tr>
            <?php 
                }
            endforeach; 
            if (empty($filtrados)):
            ?>
                <tr><td colspan="<?php echo ($conAprobado == '1') ? 7 : 10; ?>" class="text-center py-3">No se encontraron participantes.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- SECCIÓN DETALLE (Si aplica) -->
    <?php echo $detalleHtml; ?>

</body>
</html>

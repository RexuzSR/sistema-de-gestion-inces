<?php
// Variables disponibles:
// $usuarioFull, $fechaGeneracion, $logoGobierno, $logoInces
// $participante, $cursos, $kpis
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Académico - <?php echo htmlspecialchars(($participante['primer_nombre'] ?? '') . ' ' . ($participante['primer_apellido'] ?? '')); ?></title>
    <style>
        /* CSS Compatible con DOMPDF 2.0+ */
        @page { margin: 120px 40px 60px 40px; }
        
        body { 
            font-family: "Helvetica", "Arial", sans-serif; 
            font-size: 10px; 
            color: #333; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }

        /* Encabezado Fijo */
        header { 
            position: fixed; 
            top: -90px; 
            left: 0px; 
            right: 0px; 
            height: 70px; 
            border-bottom: 2.5px solid #004d99; 
            padding-bottom: 8px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-gobierno { max-height: 50px; }
        .logo-inces { max-height: 48px; text-align: right; }
        .header-title { text-align: center; }
        .header-title h1 { margin: 0; font-size: 15px; color: #004d99; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-title p { margin: 3px 0 0 0; font-size: 10px; color: #555; font-weight: bold; }
        .header-meta { font-size: 8.5px; color: #666; text-align: right; line-height: 1.2; }

        /* Pie de Página Fijo */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            border-top: 1px solid #ccc; 
            padding-top: 5px;
            font-size: 8.5px; 
            color: #666; 
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; }
        .page-number:after { content: counter(page); }

        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        /* Títulos de Sección */
        .section-title {
            background-color: #004d99;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 18px 0 10px 0;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }

        /* Perfil del Participante */
        .profile-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-left: 4px solid #004d99;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 15px;
        }
        .profile-table { width: 100%; border-collapse: collapse; }
        .profile-table td { padding: 4px 6px; vertical-align: top; }
        .profile-label { font-weight: bold; color: #495057; width: 130px; }
        .profile-val { color: #212529; }

        /* KPIs */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 15px; }
        .kpi-card { 
            background: #ffffff; 
            border: 1px solid #dee2e6; 
            border-top: 3px solid #004d99;
            border-radius: 4px; 
            padding: 8px 4px; 
            text-align: center; 
        }
        .kpi-value { font-size: 16px; font-weight: bold; color: #004d99; margin-bottom: 2px; }
        .kpi-label { font-size: 8.5px; color: #6c757d; text-transform: uppercase; font-weight: bold; }

        /* Tablas de Cursos */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 9.5px; 
            margin-bottom: 15px;
        }
        table.data-table th { 
            background-color: #343a40; 
            color: #ffffff; 
            font-weight: bold; 
            text-align: left; 
            padding: 7px 6px;
            border: 1px solid #343a40;
        }
        table.data-table td { 
            border: 1px solid #dee2e6; 
            padding: 6px; 
            vertical-align: middle;
        }
        table.data-table tbody tr:nth-child(even) td { background-color: #f8f9fa; }

        /* Badges de Estado */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
            text-align: center;
        }
        .badge-success { background-color: #198754; }
        .badge-primary { background-color: #0d6efd; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger  { background-color: #dc3545; }
        .badge-secondary{ background-color: #6c757d; }
        .badge-info    { background-color: #0dcaf0; color: #000; }

        /* Firma */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            text-align: center;
            page-break-inside: avoid;
        }
        .signature-line {
            width: 220px;
            margin: 0 auto 5px auto;
            border-top: 1px solid #333;
        }
        .signature-name { font-weight: bold; font-size: 11px; color: #212529; }
        .signature-title { font-size: 9.5px; color: #6c757d; }
    </style>
</head>
<body>

    <!-- Header Fijo -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 25%;">
                    <?php if ($logoGobierno): ?>
                        <img src="<?php echo $logoGobierno; ?>" class="logo-gobierno" alt="Gobierno Bolivariano">
                    <?php endif; ?>
                </td>
                <td style="width: 50%;" class="header-title">
                    <h1>INSTITUTO NACIONAL DE CAPACITACIÓN Y EDUCACIÓN SOCIALISTA</h1>
                    <p>HISTORIAL ACADÉMICO DEL PARTICIPANTE</p>
                </td>
                <td style="width: 25%;" class="header-meta">
                    <?php if ($logoInces): ?>
                        <img src="<?php echo $logoInces; ?>" class="logo-inces" alt="INCES"><br>
                    <?php endif; ?>
                    <strong>Fecha:</strong> <?php echo $fechaGeneracion; ?><br>
                    <strong>Emitido por:</strong> <?php echo htmlspecialchars($usuarioFull); ?>
                </td>
            </tr>
        </table>
    </header>

    <!-- Pie de Página Fijo -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 70%;">
                    INCES Industrial - CFSB/N°570021211/0020 &bull; Av. Principal Bolivariano, Cumaná, Estado Sucre
                </td>
                <td style="width: 30%; text-align: right;">
                    Página <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </footer>

    <!-- Contenido Principal -->
    <main>

        <!-- 1. Perfil del Participante -->
        <div class="section-title">Información del Participante</div>
        <div class="profile-card">
            <?php
            $nombreComp = trim(implode(' ', array_filter([
                $participante['primer_nombre'] ?? '',
                $participante['segundo_nombre'] ?? '',
                $participante['primer_apellido'] ?? '',
                $participante['segundo_apellido'] ?? ''
            ])));
            ?>
            <table class="profile-table">
                <tr>
                    <td class="profile-label">Nombres y Apellidos:</td>
                    <td class="profile-val fw-bold"><?php echo htmlspecialchars($nombreComp ?: '—'); ?></td>
                    <td class="profile-label">Cédula de Identidad:</td>
                    <td class="profile-val fw-bold"><?php echo htmlspecialchars($participante['cedula'] ?? '—'); ?></td>
                </tr>
                <tr>
                    <td class="profile-label">Estado en Sistema:</td>
                    <td class="profile-val">
                        <?php 
                        $est = $participante['estado'] ?? 'Disponible';
                        $bgEst = 'badge-secondary';
                        if ($est === 'En formacion') $bgEst = 'badge-primary';
                        elseif ($est === 'Disponible') $bgEst = 'badge-success';
                        elseif ($est === 'Asignado') $bgEst = 'badge-info';
                        elseif ($est === 'Egresado') $bgEst = 'badge-warning';
                        ?>
                        <span class="badge <?php echo $bgEst; ?>"><?php echo htmlspecialchars($est); ?></span>
                    </td>
                    <td class="profile-label">Grado de Instrucción:</td>
                    <td class="profile-val"><?php echo htmlspecialchars($participante['grado_institucion'] ?? 'No especificado'); ?></td>
                </tr>
                <tr>
                    <td class="profile-label">Correo Electrónico:</td>
                    <td class="profile-val"><?php echo htmlspecialchars($participante['correo'] ?? '—'); ?></td>
                    <td class="profile-label">Teléfono de Contacto:</td>
                    <td class="profile-val"><?php echo htmlspecialchars($participante['telefono'] ?? '—'); ?></td>
                </tr>
                <tr>
                    <td class="profile-label">Género / Edad:</td>
                    <td class="profile-val">
                        <?php echo htmlspecialchars(($participante['genero'] ?? '—') . ' (' . ($participante['edad'] ?? '—') . ' años)'); ?>
                    </td>
                    <td class="profile-label">Discapacidad:</td>
                    <td class="profile-val"><?php echo htmlspecialchars($participante['nombre_discapacidad'] ?? 'Ninguna'); ?></td>
                </tr>
            </table>
        </div>

        <!-- 2. KPIs Académicos -->
        <table class="kpi-table">
            <tr>
                <td style="width: 25%;">
                    <div class="kpi-card">
                        <div class="kpi-value"><?php echo $kpis['total_cursos']; ?></div>
                        <div class="kpi-label">Formaciones Totales</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card" style="border-top-color: #198754;">
                        <div class="kpi-value" style="color: #198754;"><?php echo $kpis['aprobados']; ?></div>
                        <div class="kpi-label">Cursos Aprobados</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card" style="border-top-color: #0d6efd;">
                        <div class="kpi-value" style="color: #0d6efd;"><?php echo $kpis['en_curso']; ?></div>
                        <div class="kpi-label">En Formación</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card" style="border-top-color: #ffc107;">
                        <div class="kpi-value" style="color: #d39e00;"><?php echo $kpis['total_horas']; ?> hrs</div>
                        <div class="kpi-label">Horas Certificadas</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 3. Registro Histórico de Cursos -->
        <div class="section-title">Historial de Cursos y Formaciones</div>
        
        <?php if (!empty($cursos)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%; text-align: center;">#</th>
                        <th style="width: 32%;">Nombre del Curso / Formación</th>
                        <th style="width: 14%; text-align: center;">Preimpreso</th>
                        <th style="width: 18%;">Ámbito / Matriz</th>
                        <th style="width: 16%; text-align: center;">Período</th>
                        <th style="width: 6%; text-align: center;">Horas</th>
                        <th style="width: 10%; text-align: center;">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($cursos as $c): 
                        $st = $c['estatus_participante'] ?? 'Registrado';
                        $badgeClass = 'badge-secondary';
                        if ($st === 'Aprobado' || $st === 'Culminado') $badgeClass = 'badge-success';
                        elseif ($st === 'En formación' || $st === 'En proceso') $badgeClass = 'badge-primary';
                        elseif ($st === 'Reprobado' || $st === 'Cancelado') $badgeClass = 'badge-danger';
                        elseif ($st === 'Retirado') $badgeClass = 'badge-warning';
                    ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold; color: #6c757d;"><?php echo $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($c['nombre_curso']); ?></strong>
                                <?php if (!empty($c['modalidad'])): ?>
                                    <br><span style="font-size: 8px; color: #6c757d;">Modalidad: <?php echo htmlspecialchars($c['modalidad']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-family: monospace; font-size: 9px;">
                                <?php echo htmlspecialchars($c['preimpreso'] ?? '—'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($c['nombre_ambito'] ?? $c['nombre_matriz'] ?? '—'); ?>
                            </td>
                            <td style="text-align: center; font-size: 8.5px;">
                                <?php echo htmlspecialchars($c['fecha_inicio'] ?? '—'); ?>
                                <?php if (!empty($c['fecha_fin']) && $c['fecha_fin'] !== $c['fecha_inicio']): ?>
                                    <br>al <?php echo htmlspecialchars($c['fecha_fin']); ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <?php echo floatval($c['horas_totales'] ?? 0); ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($st); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background: #f8f9fa; border: 1px dashed #ced4da; padding: 20px; text-align: center; color: #6c757d; border-radius: 4px; margin-bottom: 20px;">
                <em>No se encontraron registros de cursos o formaciones para este participante.</em>
            </div>
        <?php endif; ?>

        <!-- 4. Sección de Firma Institucional -->
        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-name">Abraham Rodríguez</div>
            <div class="signature-title">Jefe de Centro INCES Bolivariano</div>
        </div>

    </main>

</body>
</html>

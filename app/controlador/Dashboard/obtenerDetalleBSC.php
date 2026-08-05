<?php
require_once __DIR__ . '/../../modelo/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

$indicador = $_POST['indicador'] ?? '';
$periodo = $_POST['periodo'] ?? ''; // Ej: "Feb 26", "2026", "T1-26"

if (empty($indicador) || empty($periodo)) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros']);
    exit;
}

// Analizar el periodo o la etiqueta seleccionada
$mes = null;
$anio = null;
$trimestre = null;
$etiquetaFiltro = null;

$mesesES = ['Ene'=>1, 'Feb'=>2, 'Mar'=>3, 'Abr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Ago'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dic'=>12];

if (preg_match('/^([A-Z][a-z]{2}) (\d{2})$/', $periodo, $matches)) {
    // Es un mes (Ej: "Feb 26")
    $mes = $mesesES[$matches[1]] ?? null;
    $anio = 2000 + (int)$matches[2];
} elseif (preg_match('/^T(\d)-(\d{2})$/', $periodo, $matches)) {
    // Es un trimestre (Ej: "T1-26")
    $trimestre = (int)$matches[1];
    $anio = 2000 + (int)$matches[2];
} elseif (preg_match('/^(\d{4})$/', $periodo, $matches)) {
    // Es un año (Ej: "2026")
    $anio = (int)$matches[1];
} else {
    // Es una etiqueta textual de categoría, ámbito o curso (Ej: "Instalaciones Militares")
    $etiquetaFiltro = trim($periodo);
}

if (!$anio && !$etiquetaFiltro) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de periodo o etiqueta no reconocido']);
    exit;
}

$conexion = new Conexion();
$conn = $conexion->getConexion();
$html = '';

try {
    switch ($indicador) {
        case '% de utilización de cupos por formación':
            if ($mes) {
                $sql = "SELECT nombre_curso, num_inscritos, max_participantes 
                        FROM tb_curso 
                        WHERE id_curso != 1 AND MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio
                        ORDER BY num_inscritos DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="table-responsive mt-3 mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Formación</th><th class="text-center py-2">Inscritos</th><th class="text-center py-2">Cupos</th><th class="text-center py-2">Uso (%)</th></tr></thead><tbody>';
                    foreach ($cursos as $c) {
                        $pct = $c['max_participantes'] > 0 ? round(($c['num_inscritos'] / $c['max_participantes']) * 100, 1) : 0;
                        $badge = $pct >= 85 ? 'bg-success' : ($pct >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                        $html .= "<tr>
                                    <td class='align-middle text-truncate' style='max-width: 150px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">".htmlspecialchars($c['nombre_curso'])."</td>
                                    <td class='text-center align-middle'>{$c['num_inscritos']}</td>
                                    <td class='text-center align-middle'>{$c['max_participantes']}</td>
                                    <td class='text-center align-middle'><span class='badge {$badge}'>{$pct}%</span></td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay formaciones registradas en este mes.</div>';
                }
            }
            break;

        case '% Participantes disponibles sin asignación':
            if ($mes) {
                $sql = "SELECT primer_nombre, primer_apellido, cedula, estado 
                        FROM tb_participante 
                        WHERE estado = 'Disponible' AND MONTH(fecha_registro) = :mes AND YEAR(fecha_registro) = :anio
                        ORDER BY primer_nombre ASC LIMIT 50"; // Limitado a 50 para no desbordar el modal
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($parts) > 0) {
                    $html .= '<div class="table-responsive mt-3 mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Participante</th><th class="py-2">Cédula</th><th class="py-2">Estado</th></tr></thead><tbody>';
                    foreach ($parts as $p) {
                        $nombreCompleto = $p['primer_nombre'] . ' ' . $p['primer_apellido'];
                        $html .= "<tr>
                                    <td class='align-middle text-truncate' style='max-width: 130px;'>".htmlspecialchars($nombreCompleto)."</td>
                                    <td class='align-middle'>{$p['cedula']}</td>
                                    <td class='align-middle'><span class='badge bg-info text-dark'>{$p['estado']}</span></td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                    if (count($parts) == 50) {
                        $html .= '<div class="text-muted text-center mt-1" style="font-size: 0.7rem;">Mostrando primeros 50 registros...</div>';
                    }
                } else {
                    $html = '<div class="alert alert-success text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-check-circle me-2"></i>Todos los participantes del mes fueron asignados a formaciones.</div>';
                }
            }
            break;
            
        case '% Porcentaje de Programas Ejecutados Según Planificación':
            if ($trimestre) {
                $mesInicio = ($trimestre - 1) * 3 + 1;
                $mesFin = $trimestre * 3;
                
                $sql = "SELECT nombre_curso, fecha_inicio, id_estatusCurso 
                        FROM tb_curso 
                        WHERE id_curso != 1 
                          AND MONTH(fecha_inicio) BETWEEN :m1 AND :m2 
                          AND YEAR(fecha_inicio) = :anio
                        ORDER BY fecha_inicio ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':m1' => $mesInicio, ':m2' => $mesFin, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="table-responsive mt-3 mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Programa Formativo</th><th class="text-center py-2">Estatus</th></tr></thead><tbody>';
                    foreach ($cursos as $c) {
                        // 3: En proceso, 5: Culminado (Ambos cuentan como ejecutados según IndicadorHelper)
                        $ejecutado = in_array($c['id_estatusCurso'], [3, 5]);
                        
                        if ($ejecutado) {
                            $badge = '<span class="badge bg-success" style="font-size:0.7rem; padding: 4px 8px;"><i class="fas fa-check me-1"></i>Ejecutado</span>';
                        } else {
                            $badge = '<span class="badge bg-secondary" style="font-size:0.7rem; padding: 4px 8px;"><i class="fas fa-clock me-1"></i>No Ejecutado</span>';
                        }

                        $html .= "<tr>
                                    <td class='align-middle text-truncate' style='max-width: 200px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">".htmlspecialchars($c['nombre_curso'])."</td>
                                    <td class='text-center align-middle'>{$badge}</td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay formaciones planificadas en este trimestre.</div>';
                }
            }
            break;
            
        case '% de formación cancelada':
            // Este es trimestral
            if ($trimestre) {
                $mesInicio = ($trimestre - 1) * 3 + 1;
                $mesFin = $trimestre * 3;
                
                $sql = "SELECT nombre_curso, fecha_inicio, id_estatusCurso 
                        FROM tb_curso 
                        WHERE id_curso != 1 AND id_estatusCurso IN (2, 6) 
                          AND MONTH(fecha_inicio) BETWEEN :m1 AND :m2 
                          AND YEAR(fecha_inicio) = :anio";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':m1' => $mesInicio, ':m2' => $mesFin, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="table-responsive mt-3 mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Formación Diferida/Cancelada</th><th class="text-end py-2">F. Programada</th></tr></thead><tbody>';
                    foreach ($cursos as $c) {
                        $fecha = date('d/m/Y', strtotime($c['fecha_inicio']));
                        $html .= "<tr>
                                    <td class='align-middle'>".htmlspecialchars($c['nombre_curso'])."</td>
                                    <td class='text-end align-middle'>{$fecha}</td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-success text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-check-circle me-2"></i>No hubo formaciones diferidas ni canceladas en este trimestre.</div>';
                }
            }
            break;
            
        case '% ambito de formacion mas solicitado por mes':
        case '% ámbito de formación más solicitado por mes':
        case 'Ámbito de formación más solicitado':
        case 'Ámbito de formación más solicitado por mes':
            if ($etiquetaFiltro) {
                // El usuario hizo clic en la barra del ámbito específico (Ej: "Instalaciones Militares")
                $sql = "SELECT c.id_curso, c.nombre_curso, c.num_inscritos, c.max_participantes, am.nombre_ambito, ec.nombre_estatusCurso
                        FROM tb_curso c
                        LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
                        LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                        WHERE c.id_curso != 1 AND (am.nombre_ambito LIKE :etiqueta OR c.nombre_curso LIKE :etiqueta)
                        ORDER BY c.num_inscritos DESC LIMIT 50";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':etiqueta' => '%' . $etiquetaFiltro . '%']);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-building me-1"></i> Formaciones en el ámbito: <u>'.htmlspecialchars($etiquetaFiltro).'</u> (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">#</th><th class="py-2">Formación</th><th class="text-center py-2">Inscritos</th><th class="text-center py-2">Estatus</th></tr></thead><tbody>';
                    $rank = 1;
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $est = htmlspecialchars($c['nombre_estatusCurso'] ?? '—');
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle del curso'>
                                    <td class='align-middle fw-bold text-muted'>{$rank}.</td>
                                    <td class='align-middle text-truncate' style='max-width: 180px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                    <td class='text-center align-middle'><span class='badge bg-info text-dark'>{$est}</span></td>
                                  </tr>";
                        $rank++;
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No se encontraron formaciones asociadas al ámbito '.htmlspecialchars($etiquetaFiltro).'.</div>';
                }
            } elseif ($mes) {
                // El usuario consultó por mes
                $sql = "SELECT c.id_curso, c.nombre_curso, c.num_inscritos, am.nombre_ambito
                        FROM tb_curso c
                        LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
                        WHERE c.id_curso != 1 AND MONTH(c.fecha_inicio) = :mes AND YEAR(c.fecha_inicio) = :anio
                        ORDER BY c.num_inscritos DESC LIMIT 50";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-layer-group me-1"></i> Formaciones por Ámbito del Mes (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Formación</th><th class="py-2">Ámbito</th><th class="text-center py-2">Inscritos</th></tr></thead><tbody>';
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $amb = htmlspecialchars($c['nombre_ambito'] ?? '—');
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle del curso'>
                                    <td class='align-middle text-truncate' style='max-width: 170px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='align-middle text-muted' style='font-size:0.75rem;'>{$amb}</td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay formaciones registradas para este mes.</div>';
                }
            }
        case '% cantidad de Formaciones por ambito mensual':
        case '% cantidad de Formaciones por ámbito mensual':
        case '% cantidad de Formaciones por ambito':
        case '% cantidad de Formaciones por ámbito':
        case 'Cantidad de formaciones por ámbito mensual':
        case 'Cursos (Nro absoluto)':
            if ($mes) {
                $sql = "SELECT c.id_curso, c.nombre_curso, c.num_inscritos, c.max_participantes, am.nombre_ambito, ec.nombre_estatusCurso
                        FROM tb_curso c
                        LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
                        LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                        WHERE c.id_curso != 1 AND MONTH(c.fecha_inicio) = :mes AND YEAR(c.fecha_inicio) = :anio
                        ORDER BY am.nombre_ambito ASC, c.num_inscritos DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-layer-group me-1"></i> Formaciones por Ámbito del Mes (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 260px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">#</th><th class="py-2">Formación</th><th class="py-2">Ámbito</th><th class="text-center py-2">Inscritos</th><th class="text-center py-2">Estatus</th></tr></thead><tbody>';
                    $rank = 1;
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $amb = htmlspecialchars($c['nombre_ambito'] ?? '—');
                        $est = htmlspecialchars($c['nombre_estatusCurso'] ?? '—');
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle del curso'>
                                    <td class='align-middle fw-bold text-muted'>{$rank}.</td>
                                    <td class='align-middle text-truncate' style='max-width: 160px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='align-middle text-muted' style='font-size:0.75rem;'><span class='badge bg-light text-dark border'>{$amb}</span></td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                    <td class='text-center align-middle'><span class='badge bg-info text-dark' style='font-size:0.7rem;'>{$est}</span></td>
                                  </tr>";
                        $rank++;
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay formaciones registradas en este mes.</div>';
                }
            } elseif ($trimestre) {
                $mesInicio = ($trimestre - 1) * 3 + 1;
                $mesFin = $trimestre * 3;
                $sql = "SELECT c.id_curso, c.nombre_curso, c.num_inscritos, am.nombre_ambito, ec.nombre_estatusCurso
                        FROM tb_curso c
                        LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
                        LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                        WHERE c.id_curso != 1 AND MONTH(c.fecha_inicio) BETWEEN :m1 AND :m2 AND YEAR(c.fecha_inicio) = :anio
                        ORDER BY am.nombre_ambito ASC, c.num_inscritos DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':m1' => $mesInicio, ':m2' => $mesFin, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-layer-group me-1"></i> Formaciones por Ámbito del Trimestre (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 260px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">#</th><th class="py-2">Formación</th><th class="py-2">Ámbito</th><th class="text-center py-2">Inscritos</th><th class="text-center py-2">Estatus</th></tr></thead><tbody>';
                    $rank = 1;
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $amb = htmlspecialchars($c['nombre_ambito'] ?? '—');
                        $est = htmlspecialchars($c['nombre_estatusCurso'] ?? '—');
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle del curso'>
                                    <td class='align-middle fw-bold text-muted'>{$rank}.</td>
                                    <td class='align-middle text-truncate' style='max-width: 160px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='align-middle text-muted' style='font-size:0.75rem;'><span class='badge bg-light text-dark border'>{$amb}</span></td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                    <td class='text-center align-middle'><span class='badge bg-info text-dark' style='font-size:0.7rem;'>{$est}</span></td>
                                  </tr>";
                        $rank++;
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay formaciones registradas en este trimestre.</div>';
                }
            }
            break;

        case '% formacion es mas solicitada por mes':
        case '% formación es más solicitada por mes':
        case 'Formación más solicitada por mes':
            if ($mes) {
                // Obtener total de inscritos en el mes
                $stmtTot = $conn->prepare("SELECT SUM(num_inscritos) AS tot FROM tb_curso WHERE id_curso != 1 AND MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio");
                $stmtTot->execute([':mes' => $mes, ':anio' => $anio]);
                $totInscritos = (int)($stmtTot->fetchColumn() ?: 0);

                // Obtener Top 5 cursos del mes
                $sql = "SELECT id_curso, nombre_curso, num_inscritos 
                        FROM tb_curso 
                        WHERE id_curso != 1 AND MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio
                        ORDER BY num_inscritos DESC LIMIT 5";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-trophy me-1"></i> Top 5 Formaciones del Mes (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">#</th><th class="py-2">Formación</th><th class="text-center py-2">Inscritos</th><th class="text-end py-2">Participación</th></tr></thead><tbody>';
                    $rank = 1;
                    foreach ($cursos as $c) {
                        $insc = (int)$c['num_inscritos'];
                        $pct = $totInscritos > 0 ? round(($insc / $totInscritos) * 100, 1) : 0;
                        $idCurso = (int)$c['id_curso'];
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle de ".htmlspecialchars($c['nombre_curso'], ENT_QUOTES)."'>
                                    <td class='align-middle fw-bold text-muted'>{$rank}.</td>
                                    <td class='align-middle text-truncate' style='max-width: 170px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='text-center align-middle fw-bold'>{$insc}</td>
                                    <td class='text-end align-middle'><span class='badge bg-primary'>{$pct}%</span></td>
                                  </tr>";
                        $rank++;
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay registros de formaciones para este periodo.</div>';
                }
            }
            break;

        case 'Promedio de participantes por formación':
        case 'Índice de asignación efectiva':
            // Para mantener la consistencia con métricas de cursos mensuales
            if ($mes) {
                $sql = "SELECT id_curso, nombre_curso, num_inscritos 
                        FROM tb_curso 
                        WHERE id_curso != 1 AND MONTH(fecha_inicio) = :mes AND YEAR(fecha_inicio) = :anio
                        ORDER BY num_inscritos DESC LIMIT 50";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':mes' => $mes, ':anio' => $anio]);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="table-responsive mt-3 mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">Formación</th><th class="text-center py-2">Inscritos</th></tr></thead><tbody>';
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle de ".htmlspecialchars($c['nombre_curso'], ENT_QUOTES)."'>
                                    <td class='align-middle text-truncate' style='max-width: 170px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                  </tr>";
                    }
                    $html .= '</tbody></table></div>';
                } else {
                    $html = '<div class="alert alert-info text-center py-2 mt-3" style="font-size: 0.85rem;"><i class="fas fa-info-circle me-2"></i>No hay registros para este periodo.</div>';
                }
            }
            break;

        default:
            if ($etiquetaFiltro) {
                // Si viene una etiqueta textual y no cayó en casos anteriores, buscar por coincidencia en cursos/ámbitos
                $sql = "SELECT c.id_curso, c.nombre_curso, c.num_inscritos, am.nombre_ambito, ec.nombre_estatusCurso
                        FROM tb_curso c
                        LEFT JOIN tb_ambito am ON c.id_ambito = am.id_ambito
                        LEFT JOIN tb_estatuscurso ec ON c.id_estatusCurso = ec.id_estatusCurso
                        WHERE c.id_curso != 1 AND (c.nombre_curso LIKE :etiqueta OR am.nombre_ambito LIKE :etiqueta)
                        ORDER BY c.num_inscritos DESC LIMIT 50";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':etiqueta' => '%' . $etiquetaFiltro . '%']);
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($cursos) > 0) {
                    $html .= '<div class="card border-0 bg-light p-2 mb-2"><strong class="text-primary" style="font-size:0.82rem;"><i class="fas fa-search me-1"></i> Formaciones asociadas a: <u>'.htmlspecialchars($etiquetaFiltro).'</u> (Haz clic para ver detalle):</strong></div>';
                    $html .= '<div class="table-responsive mb-2" style="max-height: 250px;"><table class="table table-sm table-hover mb-0" style="font-size: 0.8rem;">';
                    $html .= '<thead class="table-light" style="position: sticky; top: 0; z-index: 1;"><tr><th class="py-2">#</th><th class="py-2">Formación</th><th class="text-center py-2">Inscritos</th><th class="text-center py-2">Estatus</th></tr></thead><tbody>';
                    $rank = 1;
                    foreach ($cursos as $c) {
                        $idCurso = (int)$c['id_curso'];
                        $est = htmlspecialchars($c['nombre_estatusCurso'] ?? '—');
                        $html .= "<tr class='view-curso align-middle' data-id='{$idCurso}' data-bs-toggle='modal' data-bs-target='#modal-viewCurso' style='cursor: pointer;' title='Haz clic para ver el detalle del curso'>
                                    <td class='align-middle fw-bold text-muted'>{$rank}.</td>
                                    <td class='align-middle text-truncate' style='max-width: 180px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">
                                        <a href='javascript:void(0)' class='text-primary text-decoration-none fw-bold me-1'><i class='fas fa-external-link-alt me-1' style='font-size: 0.72rem;'></i>".htmlspecialchars($c['nombre_curso'])."</a>
                                    </td>
                                    <td class='text-center align-middle fw-bold'>{$c['num_inscritos']}</td>
                                    <td class='text-center align-middle'><span class='badge bg-info text-dark'>{$est}</span></td>
                                  </tr>";
                        $rank++;
                    }
                    $html .= '</tbody></table></div>';
                }
            }
            if (empty($html)) {
                $html = '<div class="alert alert-secondary text-center py-2 mt-3 mb-0" style="font-size: 0.8rem;"><i class="fas fa-code me-2"></i>Desglose tabular no disponible para esta selección.</div>';
            }
            break;
    }
    
    if (empty($html)) {
        $html = '<div class="alert alert-secondary text-center py-2 mt-3 mb-0" style="font-size: 0.8rem;"><i class="fas fa-info-circle me-2"></i>Datos granulares no disponibles para la resolución del periodo actual.</div>';
    }

    echo json_encode(['status' => 'success', 'html' => $html]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}

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

// Analizar el periodo
$mes = null;
$anio = null;
$trimestre = null;

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
}

if (!$anio) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de periodo no reconocido']);
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
            
        case 'Promedio de participantes por formación':
        case '% ámbito de formación más solicitado por mes':
        case 'Índice de asignación efectiva':
            // Para mantener la consistencia con métricas de cursos mensuales
            if ($mes) {
                $sql = "SELECT nombre_curso, num_inscritos 
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
                        $html .= "<tr>
                                    <td class='align-middle text-truncate' style='max-width: 170px;' title=\"".htmlspecialchars($c['nombre_curso'])."\">".htmlspecialchars($c['nombre_curso'])."</td>
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
            $html = '<div class="alert alert-secondary text-center py-2 mt-3 mb-0" style="font-size: 0.8rem;"><i class="fas fa-code me-2"></i>Desglose tabular no habilitado para este indicador.</div>';
            break;
    }
    
    if (empty($html)) {
        $html = '<div class="alert alert-secondary text-center py-2 mt-3 mb-0" style="font-size: 0.8rem;"><i class="fas fa-info-circle me-2"></i>Datos granulares no disponibles para la resolución del periodo actual.</div>';
    }

    echo json_encode(['status' => 'success', 'html' => $html]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}

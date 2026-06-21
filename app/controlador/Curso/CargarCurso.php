<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('SISTEMA_CONTROL_INCES');
    session_start();
}
require_once '../../modelo/conexion.php';
require_once '../../modelo/HistorialUtil.php';
require_once 'managerCurso.php';

header('Content-Type: application/json');

// Sanitización
function sanitize($v) {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success'=>false,'message'=>'Método no permitido']));
}

try {
    // Recoger y sanear
    $post = array_map('sanitize', $_POST);
    
    // Validar requeridos
    $requiredFields = ['nombre_curso', 'fecha_inicio', 'fecha_fin', 'turno', 'id_ppl', 'preimpreso'];
    foreach ($requiredFields as $field) {
        if (empty($post[$field])) {
            throw new Exception("El campo $field es obligatorio");
        }
    }

    $idPpl = (int) $post['id_ppl'];
    if ($idPpl <= 0) {
        throw new Exception('Debe seleccionar un perfil productivo laboral válido.');
    }

    $idNombrecursoPost = isset($post['id_nombrecurso']) ? trim((string) $post['id_nombrecurso']) : '';
    $idNombrecurso = $idNombrecursoPost !== '' ? (int) $idNombrecursoPost : 0;
    $confirmarNuevo = isset($post['confirmar_nuevo_curso']) && $post['confirmar_nuevo_curso'] === '1';

    $cursoMgr = new Curso();

    if ($idNombrecurso > 0) {
        if (!$cursoMgr->esNombrecursoAsignadoAPpl($idPpl, $idNombrecurso)) {
            throw new Exception('El nombre de curso seleccionado no corresponde al perfil productivo laboral.');
        }
        $nombreOficial = $cursoMgr->obtenerNombreNombrecurso($idNombrecurso);
        if ($nombreOficial !== null) {
            $post['nombre_curso'] = $nombreOficial;
        }
    } else {
        if (!$confirmarNuevo) {
            throw new Exception('Debe confirmar la creación cuando el curso no está en el catálogo del perfil.');
        }
    }
    
    // Fechas
    if (strtotime($post['fecha_inicio']) > strtotime($post['fecha_fin'])) {
        throw new Exception("Fecha inicio no puede ser posterior a fecha fin");
    }

    // Validar horas_totales (rango 40-1200)
    if (!empty($post['horas_totales'])) {
        $horas = intval($post['horas_totales']);
        if ($horas < 40 || $horas > 1200) {
            throw new Exception("Las horas totales deben estar entre 40 y 1200.");
        }
    }

    // Validar max_participantes (solo 40, 50 o 60)
    if (!empty($post['max_participantes'])) {
        $maxPart = intval($post['max_participantes']);
        if (!in_array($maxPart, [40, 50])) {
            throw new Exception("El máximo de participantes debe ser 40 o 50.");
        }
    }

    // Validación: no permitir cursos con el mismo nombre (normalizado)
    if ($cursoMgr->verificarNombreCurso($post['nombre_curso'])) {
        throw new Exception('Ya existe un curso con ese nombre. Cambia el nombre.');
    }

    $resCurso = $cursoMgr->registrar([
        'nombre_curso'      => $post['nombre_curso'],
        'preimpreso'        => $post['preimpreso'] ?? null,
        'fecha_inicio'      => $post['fecha_inicio'],
        'fecha_fin'         => $post['fecha_fin'],
        'max_participantes' => $post['max_participantes'] ?? 30,
        'min_participantes' => $post['min_participantes'] ?? 10,  // ¡NUEVO!
        'horas_totales'     => $post['horas_totales'] ?? null,    // ¡NUEVO!
        'horas_por_clase'   => $post['horas_por_clase'] ?? null,  // ¡NUEVO!
        'fecha_limite_inicio' => $post['fecha_limite_inicio'] ?? null, // ¡NUEVO!
        'id_modalidad'      => $post['id_modalidad'] ?? null,
        'tipo_formacion'    => $post['tipo_formacion'] ?? null,
        'id_estatusCurso'   => $post['id_estatusCurso'] ?? 1,
        'id_sectoreconomico'=> $post['id_sectoreconomico'] ?? null,
        'id_subtipo'        => $post['id_subtipo'] ?? null,
        'id_areaFormativa'  => $post['id_areaFormativa'] ?? null,
        'id_ambito'         => $post['id_ambito'] ?? null,
        'id_ppl'            => $idPpl,
        'id_nombre_especifico' => $idNombrecurso > 0 ? $idNombrecurso : null,
        'turno'             => $post['turno'],
        'programa_formacion'=> $post['programa_formacion'] ?? null
    ]);

    if (!$resCurso['success']) {
        throw new Exception($resCurso['message']);
    }

    if ($resCurso['success']) {
        $nuevoIdCurso = $resCurso['id_curso'];
        // Registrar en historial
        if (isset($_SESSION['id_usuario'])) {
            HistorialUtil::registrarCrearCurso($nuevoIdCurso, $post['nombre_curso'], $_SESSION['id_usuario']);
        }
    }
    
    // Asignar docente si se proporcionó (manejo de errores y reversión)
    if (!empty($post['id_docente'])) {
        $idDocente = intval($post['id_docente']);
        try {
            $cursoMgr->asignarDocenteCurso($nuevoIdCurso, $idDocente);
            $estadoRes = $cursoMgr->actualizarEstadoDocente($idDocente);
            // Si actualizarEstadoDocente devolviera success=false podemos loguearlo pero no bloquear
            if (isset($estadoRes['success']) && !$estadoRes['success']) {
                error_log("Advertencia: actualizarEstadoDocente devolvió false para docente $idDocente");
            }
            $message = 'Curso y docente registrados correctamente';
        } catch (Exception $e) {
            // Intentar revertir la creación del curso si la asignación de docente falla
            error_log('Error al asignar docente: ' . $e->getMessage());
            // Intentar eliminar el curso recién creado
            try {
                $borrado = $cursoMgr->eliminarCurso($nuevoIdCurso);
                if (!(isset($borrado['success']) && $borrado['success'])) {
                    error_log('Fallo al revertir curso ID '.$nuevoIdCurso.': '.($borrado['message'] ?? json_encode($borrado)));
                }
            } catch (Exception $ex) {
                error_log('Error al intentar eliminar curso tras fallo de asignación: ' . $ex->getMessage());
            }
            // Lanzar excepción para que el flujo principal devuelva error
            throw new Exception('Error al asignar docente: ' . $e->getMessage());
        }
    } else {
        $message = 'Curso registrado correctamente';
    }

    echo json_encode([
        'success'   => true,
        'message'   => $message,
        'id_curso'  => $nuevoIdCurso
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
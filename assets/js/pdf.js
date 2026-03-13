// pdf.js - Control centralizado para generación de PDFs
// Última actualización: [Fecha]

$(document).ready(function() {
    // =================================================================
    // SECCIÓN PARTICIPANTES (Modal específico)
    // =================================================================

    // 1. Constancia de Estado del Participante
    $(document).on('click', '#btn-constancia-participante', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        // Validación robusta
        if (!id || isNaN(id) || id < 1) {
            toastr.error('Error: Seleccione un participante válido', 'PDF no generado');
            return;
        }
        
        // Generar URL dinámica
        const url = new URL('../../app/controlador/fpdf/constanciaParticipante.php', window.location.href);
        url.searchParams.append('id', id);
        
        window.open(url.href, '_blank').focus();
    });

    // 2. Historial Académico del Participante
    $(document).on('click', '#btn-historial-participante', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id || isNaN(id) || id < 1) {
            toastr.error('Error: Seleccione un participante válido', 'PDF no generado');
            return;
        }
        
        // Cache buster para forzar actualización
        const timestamp = new Date().getTime();
        const url = `../../app/controlador/fpdf/historialParticipante.php?id=${id}&_=${timestamp}`;
        
        window.open(url, '_blank').focus();
    });

    // =================================================================
    // SECCIÓN GENERAL (Otros PDFs del sistema)
    // =================================================================

    // 3. Matrícula de Cursos
    $(document).on('click', '[id^="btn-matricula-"]', function(e) { // Selector dinámico
        e.preventDefault();
        const idCurso = $(this).data('id');
        
        if (idCurso && !isNaN(idCurso)) {
            window.open(`../../app/controlador/fpdf/CursoMatricula.php?id=${idCurso}`, '_blank');
        }
    });

    // 4. Constancias para Docentes
    $(document).on('click', '#btn-constancia-docente', function(e) {
        e.preventDefault();
        const idDocente = $(this).data('id');
        
        if (idDocente && !isNaN(idDocente)) {
            const url = new URL('../../app/controlador/fpdf/constanciaDocente.php', window.location.href);
            url.searchParams.append('id', idDocente);
            
            window.open(url.href, '_blank').focus();
        }
    });

    // =================================================================
    // Helper para debug
    // =================================================================
    if (window.location.href.indexOf('debug') > -1) {
        console.log('PDF.js cargado correctamente');
        console.log('Manejadores activos:', {
            participantes: ['#btn-constancia-participante', '#btn-historial-participante'],
            general: ['[id^="btn-matricula-"]', '#btn-constancia-docente']
        });
    }
});
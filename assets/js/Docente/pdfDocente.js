$(document).ready(function () {
    // Capturar clic en el botón "ver" para establecer el ID en el enlace
    $(document).on('click', '.button.view', function () {
        const idDocente = $(this).data('id'); // Obtener el ID del docente
        if (idDocente) {
            const pdfUrl = `../../app/controlador/fpdf/constanciaDocente.php?id_docente=${idDocente}`; // Generar URL dinámica

            // Actualizar el href del enlace para el botón de descarga
            $('#btn-pdf').attr('href', pdfUrl);
        } else {
            alert("ID del docente no encontrado.");
        }
    });
});

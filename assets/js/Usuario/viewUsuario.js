$(document).ready(function () {
    $(document).on('click', '.button.view', function () {
        const idUsuario = $(this).data('id');

        fetch(`../../app/controlador/Usuario/verUsuario.php?id=${idUsuario}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const usuario = data.data;

                    $("#nombre-view").text(usuario.nombre);
                    $("#apellido-view").text(usuario.apellido);
                    $("#nombre_usuario-view").text(usuario.nombre_usuario);
                    $("#correo-view").text(usuario.correo);
                    $("#rol-view").text(usuario.rol);
                    $("#estado-view").text(usuario.estado);

                    // Imagen
                    $("#avatar").attr("src", `../../${usuario.imagen}`);

                    // Estado visual
                    const estadoEl = $("#estado-view");
                    estadoEl.removeClass("badge bg-success bg-danger");

                    if (usuario.estado === "Activo") {
                        estadoEl.addClass("badge bg-success");
                    } else if (usuario.estado === "Inactivo") {
                        estadoEl.addClass("badge bg-danger");
                    }

                    // Rol visual
                    const rolEl = $("#rol-view");
                    rolEl.removeClass("badge bg-primary bg-secondary");

                    if (usuario.rol === "Administrador") {
                        rolEl.addClass("badge bg-primary");
                    } else if (usuario.rol === "Apoyo") {
                        rolEl.addClass("badge bg-secondary");
                    }

                    // Mostrar el modal
                    $("#modal-viewUsuario").modal("show");

                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error("Error al obtener los datos:", error);
                alert("No se pudo obtener la información del usuario.");
            });
    });
});

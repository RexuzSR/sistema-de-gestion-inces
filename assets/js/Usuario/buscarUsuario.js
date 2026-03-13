//Función para activar la barra de busqueda
function buscarUsuario() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase();
    const filas = document.querySelectorAll('#tabla-usuarios tbody tr');

    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
}
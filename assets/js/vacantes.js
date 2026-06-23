document.addEventListener("DOMContentLoaded", function () {
    const botonesEliminar = document.querySelectorAll(".btnEliminar");

    botonesEliminar.forEach(function (boton) {
        boton.addEventListener("click", function (e) {
            const confirmar = confirm("¿Deseas eliminar esta vacante?");
            if (!confirmar) {
                e.preventDefault();
            }
        });
    });
});

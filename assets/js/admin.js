document.addEventListener("DOMContentLoaded", function () {
    const btnLogout = document.getElementById("btnLogout");

    if (btnLogout) {
        btnLogout.addEventListener("click", function (e) {
            const confirmar = confirm("¿Deseas cerrar sesión?");
            if (!confirmar) {
                e.preventDefault();
            }
        });
    }

    const botonesEliminar = document.querySelectorAll(".btnEliminar");

    botonesEliminar.forEach(function (boton) {
        boton.addEventListener("click", function (e) {
            const confirmar = confirm("¿Deseas eliminar este registro?");
            if (!confirmar) {
                e.preventDefault();
            }
        });
    });
});

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
        boton.addEventListener("click", function () {

            const id = boton.dataset.id;
            const confirmar = confirm("¿Deseas eliminar este administrador?");

            if (!confirmar) return;

            fetch("actions/delete_administrador.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + id
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);

                if (data.success) {
                    boton.closest("tr").remove();
                }
            })
            .catch(error => {
                console.error(error);
                alert("Error de conexión.");
            });

        });
    });

});
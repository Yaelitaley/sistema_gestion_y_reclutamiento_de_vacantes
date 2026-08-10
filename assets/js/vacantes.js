document.addEventListener("DOMContentLoaded", function () {

    /*==========================================
    =            CERRAR SESIÓN (MODAL BOOTSTRAP)
    ==========================================*/

    const logoutTriggers = document.querySelectorAll(".btn-logout-trigger");
    const modalLogoutEl = document.getElementById("modalConfirmarLogout");
    const btnConfirmarLogout = document.getElementById("btnConfirmarLogout");
    let logoutUrl = null;

    if (logoutTriggers.length && modalLogoutEl && btnConfirmarLogout) {

        const modalLogout = new bootstrap.Modal(modalLogoutEl);

        logoutTriggers.forEach(function (trigger) {

            trigger.addEventListener("click", function (e) {

                e.preventDefault();
                logoutUrl = trigger.getAttribute("href");
                modalLogout.show();

            });

        });

        btnConfirmarLogout.addEventListener("click", function () {

            if (logoutUrl) {
                window.location.href = logoutUrl;
            }

        });

    }

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
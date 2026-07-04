document.addEventListener("DOMContentLoaded", function () {

    /*=========================================
    =            BOTÓN MENÚ SIDEBAR
    =========================================*/

    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    if (menuToggle && sidebar && content) {

        menuToggle.addEventListener("click", function (e) {

            e.stopPropagation();

            sidebar.classList.toggle("active");
            content.classList.toggle("sidebar-open");

        });

    }

    /*=========================================
    =            CERRAR SIDEBAR
    =========================================*/

    document.addEventListener("click", function (e) {

        if (
            sidebar &&
            content &&
            sidebar.classList.contains("active") &&
            !sidebar.contains(e.target) &&
            !menuToggle.contains(e.target)
        ) {

            sidebar.classList.remove("active");
            content.classList.remove("sidebar-open");

        }

    });

    /*=========================================
    =            CONFIRMAR LOGOUT
    =========================================*/

    const btnLogout = document.getElementById("btnLogout");

    if (btnLogout) {

        btnLogout.addEventListener("click", function (e) {

            const confirmar = confirm("¿Deseas cerrar sesión?");

            if (!confirmar) {

                e.preventDefault();

            }

        });

    }

    /*=========================================
    =            ELIMINAR RECLUTADOR
    =========================================*/

    const botonesEliminar = document.querySelectorAll(".btnEliminar");

    botonesEliminar.forEach(function (boton) {

        boton.addEventListener("click", function (e) {

            e.preventDefault();

            const confirmar = confirm("¿Deseas eliminar este reclutador?");

            if (confirmar) {

                alert("Reclutador eliminado correctamente.");

                // Aquí posteriormente irá el fetch o AJAX para eliminar.

            }

        });

    });

    /*=========================================
    =            EFECTO HOVER CARDS
    =========================================*/

    const cards = document.querySelectorAll(".dashboard-card");

    cards.forEach(function(card){

        card.addEventListener("mouseenter", function(){

            card.style.transform = "translateY(-6px)";

        });

        card.addEventListener("mouseleave", function(){

            card.style.transform = "translateY(0px)";

        });

    });

});
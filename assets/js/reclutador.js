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
    =            REGISTRAR RECLUTADOR
    =========================================*/

    const registerForm = document.getElementById("registerForm");
    const mensaje = document.getElementById("mensaje");

    if (registerForm) {

        registerForm.addEventListener("submit", function (e) {

            e.preventDefault();

            const nombre = document.getElementById("nombre").value.trim();
            const empresa = document.getElementById("empresa") ? document.getElementById("empresa").value : "";
            const telefono = document.getElementById("telefono") ? document.getElementById("telefono").value.trim() : "";
            const correo = document.getElementById("correo").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const claveSeguridad = document.getElementById("claveSeguridad").value.trim();

            mensaje.classList.remove("d-none", "alert-danger", "alert-success");

            if (!nombre || !empresa || !correo || !password || !confirmPassword || !claveSeguridad) {
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "Rellena todos los campos obligatorios.";
                return;
            }

            if (!correo.includes("@")) {
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "Correo no válido.";
                return;
            }

            if (password.length < 6) {
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "La contraseña debe tener mínimo 6 caracteres.";
                return;
            }

            if (password !== confirmPassword) {
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "Las contraseñas no coinciden.";
                return;
            }

            if (claveSeguridad.length < 4) {
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "La clave de seguridad debe tener mínimo 4 caracteres.";
                return;
            }

            const datos = new FormData();
            datos.append("nombre", nombre);
            datos.append("correo", correo);
            datos.append("empresa", empresa);
            datos.append("telefono", telefono);
            datos.append("password", password);
            datos.append("confirmPassword", confirmPassword);
            datos.append("claveSeguridad", claveSeguridad);

            const btnSubmit = registerForm.querySelector("button[type='submit']");
            if (btnSubmit) {
                btnSubmit.disabled = true;
            }

            fetch("actions/register_reclutador.php", {
                method: "POST",
                body: datos
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {

                mensaje.classList.remove("alert-danger", "alert-success");
                mensaje.classList.add(data.success ? "alert-success" : "alert-danger");
                mensaje.innerHTML = data.message;

                if (data.success) {
                    registerForm.reset();
                    setTimeout(function () {
                        window.location.href = "../admin/index_reclutador.php";
                    }, 1500);
                } else if (btnSubmit) {
                    btnSubmit.disabled = false;
                }

            })
            .catch(function () {
                mensaje.classList.remove("alert-success");
                mensaje.classList.add("alert-danger");
                mensaje.innerHTML = "Ocurrió un error al conectar con el servidor.";
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                }
            });

        });

    }

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
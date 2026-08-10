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
    =            CONFIRMAR LOGOUT (MODAL BOOTSTRAP)
    =========================================*/

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

    /*=========================================
    =            BUSCADOR DE CANDIDATOS
    =========================================*/

    const buscador = document.getElementById("buscarCandidato");
    const filtro = document.getElementById("filtroEstado");

    if (buscador && filtro) {
        const filas = document.querySelectorAll("#tablaCandidatos tbody tr");

        function filtrarTabla() {
            const texto = buscador.value.toLowerCase();
            const estado = filtro.value.toLowerCase();

            filas.forEach(fila => {
                if (!fila.cells[1] || !fila.cells[2] || !fila.cells[3]) return;

                const nombre = fila.cells[1].textContent.toLowerCase();
                const vacante = fila.cells[2].textContent.toLowerCase();
                const estadoFila = fila.cells[3].textContent.toLowerCase();

                const coincideTexto = nombre.includes(texto) || vacante.includes(texto);
                const coincideEstado = estado === "todos" || estadoFila.includes(estado);

                fila.style.display = (coincideTexto && coincideEstado) ? "" : "none";
            });
        }

        buscador.addEventListener("keyup", filtrarTabla);
        filtro.addEventListener("change", filtrarTabla);
    }

    /*=========================================
    =            ENVIAR MENSAJE (MODAL)
    =========================================*/

    const btnEnviarMensaje = document.getElementById("btnEnviarMensaje");

    if (btnEnviarMensaje) {

        btnEnviarMensaje.addEventListener("click", () => {

            const asuntoInput = document.getElementById("asuntoMensaje");
            const mensajeInput = document.getElementById("contenidoMensaje");
            const modalElement = document.getElementById("modalMensaje");

            if (!asuntoInput || !mensajeInput || !modalElement) return;

            const asunto = asuntoInput.value.trim();
            const mensajeValor = mensajeInput.value.trim();

            if (asunto === "" || mensajeValor === "") {
                alert("Complete todos los campos.");
                return;
            }

            alert("Mensaje enviado correctamente.");

            asuntoInput.value = "";
            mensajeInput.value = "";

            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();

        });
    }

});


document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById("buscarVacante");
    const filtro = document.getElementById("filtroEstado");
    const filas = document.querySelectorAll("#tablaVacantes tbody tr");

    function filtrarVacantes() {

        const texto = buscador.value.toLowerCase();
        const estado = filtro.value.toLowerCase();

        let visibles = 0;

        filas.forEach(fila => {

            const puesto = fila.cells[0].textContent.toLowerCase();
            const categoria = fila.cells[1].textContent.toLowerCase();
            const ubicacion = fila.cells[2].textContent.toLowerCase();
            const estadoFila = fila.cells[4].textContent.toLowerCase();

            const coincideTexto =
                puesto.includes(texto) ||
                categoria.includes(texto) ||
                ubicacion.includes(texto);

            const coincideEstado =
                estado === "" ||
                estadoFila.includes(estado);

            if (coincideTexto && coincideEstado) {
                fila.style.display = "";
                visibles++;
            } else {
                fila.style.display = "none";
            }

        });

    }

    buscador.addEventListener("keyup", filtrarVacantes);
    filtro.addEventListener("change", filtrarVacantes);

});

document.addEventListener("DOMContentLoaded", () => {

    // ==========================
    // Banner de imágenes
    // ==========================

    const banner = document.getElementById("bannerReclutador");

    if (banner) {
        new bootstrap.Carousel(banner, {
            interval: 4000,
            ride: true,
            pause: false
        });
    }

    // ==========================
    // Login
    // ==========================

    const loginForm = document.getElementById("loginForm");

    if (!loginForm) return;

    loginForm.addEventListener("submit", function (e) {

        e.preventDefault();

        const btn = document.getElementById("btnLogin");
        const mensaje = document.getElementById("mensaje");

        mensaje.className = "alert mt-3 d-none";
        mensaje.textContent = "";

        btn.disabled = true;
        btn.textContent = "Verificando...";

        const formData = new FormData();

        formData.append("correo", document.getElementById("correo").value.trim());
        formData.append("password", document.getElementById("password").value);

        fetch("actions/login_reclutador.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            if (data.success) {

                window.location.href = "dashboard.php";

            } else {

                mensaje.classList.remove("d-none");
                mensaje.classList.add("alert-danger");
                mensaje.textContent = data.message;

                btn.disabled = false;
                btn.textContent = "Iniciar sesión";
            }

        })
        .catch(() => {

            mensaje.classList.remove("d-none");
            mensaje.classList.add("alert-danger");
            mensaje.textContent = "Error de conexión. Intenta de nuevo.";

            btn.disabled = false;
            btn.textContent = "Iniciar sesión";

        });

    });

});
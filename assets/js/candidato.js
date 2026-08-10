console.log("JavaScript cargado correctamente");

/*=========================================
=            CONFIRMAR LOGOUT (MODAL BOOTSTRAP)
=========================================*/
document.addEventListener("DOMContentLoaded", function () {

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

});


document.addEventListener("DOMContentLoaded", function(){
//regstro
    const registerForm = document.getElementById("registerForm");

    const mensaje = document.getElementById("mensaje");


// aqui se valida el formulario de registro
    if(registerForm){

        registerForm.addEventListener("submit", function(e){

            e.preventDefault();

            // aqui se obtienen los valores de los inputs
            const nombre = document.getElementById("nombre").value;

            const correo = document.getElementById("correo").value;

            const password = document.getElementById("password").value;

            const confirmPassword = document.getElementById("confirmPassword").value;

            const claveSeguridad = document.getElementById("claveSeguridad").value;



            // se muestra un mensaje de alerta si el formulario no es valido
            mensaje.classList.remove("d-none");



            // se limpian las clases de alerta para evitar que se acumulen
            mensaje.classList.remove("alert-danger");

            mensaje.classList.remove("alert-success");



            // se revisa si los campos estan vacios
            if(nombre === "" || correo === "" || password === "" || confirmPassword === "" || claveSeguridad === ""){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Rellena todos los campos.";

            }



            // solo letras en el nombre
            else if(!isNaN(nombre)){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "El nombre debe contener letras.";

            }



            // validamos que el correo tenga un formato valido
            else if(!correo.includes("@")){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Correo no válido.";

            }



            // validamos que la contraseña tenga al menos 6 caracteres
            else if(password.length < 6){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "La contraseña debe tener mínimo 6 caracteres.";

            }



            // aqui se valida que las contraseñas coincidan
            else if(password !== confirmPassword){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Las contraseñas no coinciden.";

            }



            // validamos que la clave de seguridad tenga al menos 4 caracteres
            else if(claveSeguridad.length < 4){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "La clave de seguridad debe tener mínimo 4 caracteres.";

            }



            // si todo es correcto, se envian los datos al servidor
            else{

                const datos = new FormData();
                datos.append("nombre", nombre);
                datos.append("correo", correo);
                datos.append("password", password);
                datos.append("confirmPassword", confirmPassword);
                datos.append("claveSeguridad", claveSeguridad);

                const btnSubmit = registerForm.querySelector("button[type='submit']");
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                }

                fetch("actions/register_candidato.php", {
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
                            window.location.href = "login.php";
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

            }

        });

    }

});

//aqui se valida el formulario de inicio de sesion

const loginForm = document.getElementById("loginForm");

const mensajeLogin = document.getElementById("mensajeLogin");


// se agrega un evento de submit al formulario de inicio de sesion
if(loginForm){

    loginForm.addEventListener("submit", function(e){

        e.preventDefault();

        // aqui se obtienen los valores de los inputs de correo y contraseña
        const correo = document.getElementById("correo").value;

        const password = document.getElementById("password").value;



        // se muestra el mensaje de alerta si el formulario no es valido
        mensajeLogin.classList.remove("d-none");



        // se limpian las clases de alerta para evitar que se acumulen
        mensajeLogin.classList.remove("alert-danger");

        mensajeLogin.classList.remove("alert-success");



        // se valida que los campos de correo y contraseña no esten vacios
        if(correo === "" || password === ""){

            mensajeLogin.classList.add("alert-danger");

            mensajeLogin.innerHTML = "Rellena todos los campos.";

        }



        // se valida que el correo tenga un formato valido
        else if(!correo.includes("@")){

            mensajeLogin.classList.add("alert-danger");

            mensajeLogin.innerHTML = "Correo no válido.";

        }



        // se valida que la contraseña tenga al menos 6 caracteres
        else if(password.length < 6){

            mensajeLogin.classList.add("alert-danger");

            mensajeLogin.innerHTML = "La contraseña debe tener mínimo 6 caracteres.";

        }



        // Lnaza un mensaje de exito si el correo y la contraseña son validos
        else{

            mensajeLogin.classList.add("alert-success");

            mensajeLogin.innerHTML = "Inicio de sesión correcto.";

        }

    });

}

document.addEventListener("DOMContentLoaded", function(){

    // en este caso, se seleccionan todos los botones con la clase "btnEliminar" para agregarles un evento de clic que muestre una alerta de confirmación antes de eliminar un reclutador. Si el usuario confirma la eliminación, se muestra una alerta indicando que el reclutador ha sido eliminado correctamente.
    const botonesEliminar = document.querySelectorAll(".btnEliminar");



    botonesEliminar.forEach(function(boton){

        boton.addEventListener("click", function(){

            // mensaje de confirmacion
            const confirmar = confirm("¿Deseas eliminar este reclutador?");



            // si elige si manda un alerta de eliminado correctamente
            if(confirmar){

                alert("Reclutador eliminado correctamente.");

            }

            });

    });

});

/*==================================================
=            EXPLORAR EMPLEOS
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    // ==========================
    // BUSCADOR DE EMPLEOS
    // ==========================

    const buscador = document.getElementById("buscarEmpleo");

    if(buscador){

        buscador.addEventListener("keyup", function(){

            const texto = this.value.toLowerCase();

            const vacantes = document.querySelectorAll(".job-card");

            vacantes.forEach(function(vacante){

                const contenido = vacante.textContent.toLowerCase();

                if(contenido.includes(texto)){

                    vacante.style.display = "block";

                }

                else{

                    vacante.style.display = "none";

                }

            });

        });

    }






    // ==========================
    // BOTÓN POSTULARME (conectado a la API REST assets/api/api-postulaciones.php)
    // Se usa delegación de eventos porque algunas páginas (explorar-empleos)
    // generan las tarjetas de vacante dinámicamente después de cargar la página.
    // ==========================

    document.addEventListener("click", async function (e) {

        const boton = e.target.closest(".btnPostular");

        if (!boton || typeof Api === "undefined") return;

        const vacanteId = boton.dataset.vacanteId;

        if (!vacanteId) return;

        boton.disabled = true;
        const textoOriginal = boton.innerHTML;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';

        const { ok, message } = await Api.post("postulaciones", { vacante_id: Number(vacanteId) });

        if (!ok) {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
            alert(message || "No se pudo enviar tu postulación.");
            return;
        }

        boton.classList.remove("btn-candidato");
        boton.classList.add("btn-secondary");
        boton.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Postulado';
        alert("¡Tu postulación fue enviada correctamente!");

        document.dispatchEvent(new CustomEvent("postulacion:creada", { detail: { vacanteId: Number(vacanteId) } }));

    });






    // ==========================
    // GUARDAR EMPLEO (llamada real a actions/guardar_vacante.php)
    // ==========================

    document.addEventListener("click", function(e){

        const boton = e.target.closest(".btnGuardar");

        if(!boton) return;

        const vacanteId = boton.dataset.vacanteId;

        if(!vacanteId) return;

        const yaGuardado = boton.classList.contains("btn-success");
        const accion = yaGuardado ? "quitar" : "guardar";

        boton.disabled = true;

        const datos = new FormData();
        datos.append("vacante_id", vacanteId);
        datos.append("accion", accion);

        fetch("actions/guardar_vacante.php", {
            method: "POST",
            body: datos
        })
        .then(function(res){ return res.json(); })
        .then(function(data){

            boton.disabled = false;

            if(!data.success){
                alert(data.message);
                return;
            }

            if(data.guardado){
                boton.classList.remove("btn-outline-success");
                boton.classList.add("btn-success");
                boton.innerHTML = '<i class="bi bi-heart-fill me-2"></i>Guardado';
            } else {
                boton.classList.remove("btn-success");
                boton.classList.add("btn-outline-success");
                boton.innerHTML = '<i class="bi bi-heart me-2"></i>Guardar Empleo';
            }

        })
        .catch(function(){
            alert("Ocurrió un error al conectar con el servidor.");
            boton.disabled = false;
        });

    });






    // ==========================
    // COMPARTIR EMPLEO
    // ==========================

    const botonesCompartir = document.querySelectorAll(".btnCompartir");

    botonesCompartir.forEach(function(boton){

        boton.addEventListener("click", function(){

            navigator.clipboard.writeText(window.location.href);

            alert("Enlace copiado al portapapeles.");

        });

    });






    // ==========================
    // LIMPIAR FILTROS
    // ==========================

    const limpiar = document.getElementById("btnLimpiar");

    if(limpiar){

        limpiar.addEventListener("click", function(){

            document.querySelectorAll("select").forEach(function(select){

                select.selectedIndex = 0;

            });

            if(buscador){

                buscador.value = "";

            }

        });

    }

});

/*==================================================
=            CANCELAR POSTULACIÓN
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    // El botón "Cancelar postulación" se maneja directamente en
    // candidatos/postulaciones.php (llama a la API real de postulaciones).

});

/*==================================================
=                CURRÍCULUM
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    // ==========================
    // DESCARGAR CV
    // ==========================
    // El botón #btnDescargarCV ahora es un <a href download> real
    // que apunta directo al PDF (o un botón deshabilitado si no hay CV).
    // No requiere JS: el navegador maneja la descarga de forma nativa.





    // ==========================
    // RESTABLECER FORMULARIO
    // ==========================

    const btnRestablecerCV = document.getElementById("btnRestablecerCV");

    if(btnRestablecerCV){

        btnRestablecerCV.addEventListener("click", function(){

            alert("Los datos del formulario han sido restablecidos.");

        });

    }





    // ==========================
    // GUARDAR CAMBIOS DEL CURRÍCULUM
    // ==========================

    const formularioCV = document.getElementById("formCV");

    if(formularioCV){

        formularioCV.addEventListener("submit", function(e){

            e.preventDefault();

            const btnGuardar = formularioCV.querySelector('button[type="submit"]');
            const textoOriginal = btnGuardar ? btnGuardar.innerHTML : null;

            if(btnGuardar){
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = 'Guardando...';
            }

            const datosFormulario = new FormData(formularioCV);

            fetch(formularioCV.action, {
                method: "POST",
                body: datosFormulario
            })
            .then(function(respuesta){
                return respuesta.json();
            })
            .then(function(data){

                alert(data.message || (data.success
                    ? "Los cambios del currículum se guardaron correctamente."
                    : "Ocurrió un error al guardar los cambios."));

                if(data.success){
                    window.location.href = "cv.php";
                }

            })
            .catch(function(){

                alert("No se pudo conectar con el servidor. Intenta nuevamente.");

            })
            .finally(function(){

                if(btnGuardar){
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = textoOriginal;
                }

            });

        });

    }

});

/*==========================================
=            SUBIR CURRÍCULUM
==========================================*/

document.addEventListener("DOMContentLoaded", function(){

    const botonSubir = document.getElementById("btnSubirCV");
    const archivo = document.getElementById("archivoCV");

    if(botonSubir && archivo){

        botonSubir.addEventListener("click", function(){

            archivo.click();

        });

        archivo.addEventListener("change", function(){

            if(this.files.length === 0){
                return;
            }

            const archivoSeleccionado = this.files[0];

            if(archivoSeleccionado.type !== "application/pdf"){
                alert("Solo se permiten archivos PDF.");
                archivo.value = "";
                return;
            }

            const textoOriginal = botonSubir.innerHTML;
            botonSubir.disabled = true;
            botonSubir.innerHTML = "Subiendo...";

            const datosArchivo = new FormData();
            datosArchivo.append("archivo_cv", archivoSeleccionado);

            fetch("actions/subir_cv.php", {
                method: "POST",
                body: datosArchivo
            })
            .then(function(respuesta){
                return respuesta.json();
            })
            .then(function(data){

                alert(data.message || (data.success
                    ? "Currículum cargado correctamente."
                    : "Ocurrió un error al subir el currículum."));

                if(data.success){
                    window.location.reload();
                }

            })
            .catch(function(){

                alert("No se pudo conectar con el servidor. Intenta nuevamente.");

            })
            .finally(function(){

                botonSubir.disabled = false;
                botonSubir.innerHTML = textoOriginal;
                archivo.value = "";

            });

        });

    }

});

/*==================================================
=            SUBIR FOTO DE PERFIL
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    const inputFotoPerfil = document.getElementById("inputFotoPerfil");
    const previewFotoPerfil = document.getElementById("previewFotoPerfil");
    const mensajeFotoPerfil = document.getElementById("mensajeFotoPerfil");

    if (inputFotoPerfil && previewFotoPerfil) {

        inputFotoPerfil.addEventListener("change", function () {

            if (this.files.length === 0) {
                return;
            }

            const archivo = this.files[0];

            const tiposPermitidos = ["image/jpeg", "image/png"];
            if (!tiposPermitidos.includes(archivo.type)) {
                if (mensajeFotoPerfil) {
                    mensajeFotoPerfil.className = "mt-2 text-danger";
                    mensajeFotoPerfil.innerHTML = "Solo se permiten imágenes JPG, JPEG o PNG.";
                }
                inputFotoPerfil.value = "";
                return;
            }

            if (archivo.size > 3 * 1024 * 1024) {
                if (mensajeFotoPerfil) {
                    mensajeFotoPerfil.className = "mt-2 text-danger";
                    mensajeFotoPerfil.innerHTML = "La imagen no debe superar 3 MB.";
                }
                inputFotoPerfil.value = "";
                return;
            }

            // Vista previa inmediata (antes de subir)
            const lector = new FileReader();
            lector.onload = function (e) {
                previewFotoPerfil.src = e.target.result;
            };
            lector.readAsDataURL(archivo);

            // Subida al servidor
            const datos = new FormData();
            datos.append("foto", archivo);

            if (mensajeFotoPerfil) {
                mensajeFotoPerfil.className = "mt-2 text-muted";
                mensajeFotoPerfil.innerHTML = "Subiendo foto...";
            }
            inputFotoPerfil.disabled = true;

            fetch("actions/subir_foto.php", {
                method: "POST",
                body: datos
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {

                if (mensajeFotoPerfil) {
                    mensajeFotoPerfil.className = "mt-2 " + (data.success ? "text-success" : "text-danger");
                    mensajeFotoPerfil.innerHTML = data.message;
                }

                if (data.success && data.foto_perfil) {
                    previewFotoPerfil.src = "../" + data.foto_perfil + "?v=" + Date.now();
                }

                inputFotoPerfil.disabled = false;

            })
            .catch(function () {
                if (mensajeFotoPerfil) {
                    mensajeFotoPerfil.className = "mt-2 text-danger";
                    mensajeFotoPerfil.innerHTML = "Ocurrió un error al conectar con el servidor.";
                }
                inputFotoPerfil.disabled = false;
            });

        });

    }

});

/*==================================================
=            EDITAR PERFIL
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    // FORMULARIO
    const formPerfil = document.getElementById("formPerfil");

    // BOTÓN RESTABLECER
    const btnRestablecerPerfil = document.getElementById("btnRestablecerPerfil");



    // GUARDAR CAMBIOS
    if(formPerfil){

        formPerfil.addEventListener("submit", function(e){

            e.preventDefault();

            alert("Los cambios del perfil se guardaron correctamente.");

        });

    }



    // RESTABLECER
    if(btnRestablecerPerfil){

        btnRestablecerPerfil.addEventListener("click", function(){

            const confirmar = confirm("¿Deseas restablecer el formulario?");

            if(!confirmar){

                event.preventDefault();

            }else{

                alert("El formulario fue restablecido correctamente.");

            }

        });

    }

});

/*==================================================
=            SIDEBAR
==================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    // 1. Creamos el fondo oscuro (Overlay) dinámicamente
    let overlay = document.querySelector(".overlay");
    if (!overlay) {
        overlay = document.createElement("div");
        overlay.className = "overlay";
        document.body.appendChild(overlay);
    }

    // 2. Función para ABRIR el menú
    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            sidebar.classList.add("active");
            overlay.classList.add("active");
            if (content) content.classList.add("sidebar-open");
        });
    }

    // 3. Función para CERRAR el menú (Clic en el fondo oscuro)
    if (overlay) {
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
            if (content) content.classList.remove("sidebar-open");
        });
    }

    // 4. Función para CERRAR el menú (Clic fuera, a prueba de errores)
    document.addEventListener("click", function (e) {
        // Si el sidebar no existe o no está activo, no hacemos nada
        if (!sidebar || !sidebar.classList.contains("active")) return;

        // Si el clic fue dentro del sidebar, no hacemos nada
        if (sidebar.contains(e.target)) return;

        // Si el clic fue en el botón de menú, no hacemos nada
        if (menuToggle && menuToggle.contains(e.target)) return;

        // Si pasó todas las validaciones, cerramos el menú
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
        if (content) content.classList.remove("sidebar-open");
    });

});
console.log("JavaScript cargado correctamente");


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
    // BOTÓN POSTULARME
    // ==========================

    const botonesPostular = document.querySelectorAll(".btnPostular");

    botonesPostular.forEach(function(boton){

        boton.addEventListener("click", function(){

            alert("¡Tu postulación fue enviada correctamente!");

        });

    });






    // ==========================
    // GUARDAR EMPLEO
    // ==========================

    const botonesGuardar = document.querySelectorAll(".btnGuardar");

    botonesGuardar.forEach(function(boton){

        boton.addEventListener("click", function(){

            boton.classList.remove("btn-outline-success");

            boton.classList.add("btn-success");

            boton.innerHTML = '<i class="bi bi-heart-fill me-2"></i>Guardado';

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

    const botonesCancelar = document.querySelectorAll(".btnCancelarPostulacion");

    botonesCancelar.forEach(function(boton){

        boton.addEventListener("click", function(){

            const confirmar = confirm("¿Estás seguro de cancelar esta postulación?");

            if(confirmar){

                alert("La postulación ha sido cancelada correctamente.");

                this.closest("tr").remove();

            }

        });

    });

});

/*==================================================
=                CURRÍCULUM
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    // ==========================
    // DESCARGAR CV
    // ==========================

    const btnDescargarCV = document.getElementById("btnDescargarCV");

    if(btnDescargarCV){

        btnDescargarCV.addEventListener("click", function(){

            alert("Descargando currículum...");

        });

    }





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
    // GUARDAR CAMBIOS
    // ==========================

    const formularioCV = document.getElementById("formCV");

    if(formularioCV){

        formularioCV.addEventListener("submit", function(e){

            e.preventDefault();

            alert("Los cambios del currículum se guardaron correctamente.");

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

            if(this.files.length > 0){

                alert("Currículum cargado correctamente.");

            }

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
=            CERRAR SESIÓN
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    const botonesCerrarSesion = document.querySelectorAll(".btnCerrarSesion");

    botonesCerrarSesion.forEach(function(boton){

        boton.addEventListener("click", function(e){

            e.preventDefault();

            const confirmar = confirm("¿Estás seguro de que deseas cerrar sesión?");

            if(confirmar){

                window.location.href = this.href;

            }

        });

    });

});



/*==================================================
=            SIDEBAR
==================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    if(menuToggle && sidebar && content){

        menuToggle.addEventListener("click", function(){

            sidebar.classList.toggle("active");
            content.classList.toggle("sidebar-open");

        });

    }

});
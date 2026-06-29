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



            // se muestra un mensaje de alerta si el formulario no es valido
            mensaje.classList.remove("d-none");



            // se limpian las clases de alerta para evitar que se acumulen
            mensaje.classList.remove("alert-danger");

            mensaje.classList.remove("alert-success");



            // se revisa si los campos estan vacios
            if(nombre === "" || correo === "" || password === "" || confirmPassword === ""){

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



            // arroja un mensaje de exito si todo es correcto
            else{

                mensaje.classList.add("alert-success");

                mensaje.innerHTML = "Solicitud enviada correctamente.";

            }

        });

    }


// aqui se valida el formulario de recuperacion de contraseña

    const recoveryForm = document.getElementById("recoveryForm");

    const mensajeRecovery = document.getElementById("mensajeRecovery");


    if(recoveryForm){

        recoveryForm.addEventListener("submit", function(e){

            e.preventDefault();

            // aqui se obtiene el valor del input de correo
            const correoRecovery = document.getElementById("correoRecovery").value;



            //se muestra el mensaje si el formulario no es valido
            mensajeRecovery.classList.remove("d-none");



            // se limpian las clases de alerta para evitar que se acumulen
            mensajeRecovery.classList.remove("alert-danger");

            mensajeRecovery.classList.remove("alert-success");



            // aqui se valida que el campo de correo no este vacio
            if(correoRecovery === ""){

                mensajeRecovery.classList.add("alert-danger");

                mensajeRecovery.innerHTML = "Ingresa tu correo.";

            }



            // si no incluye el simbolo de arroba, no es un correo valido
            else if(!correoRecovery.includes("@")){

                mensajeRecovery.classList.add("alert-danger");

                mensajeRecovery.innerHTML = "Correo no válido.";

            }



            // lanza un mensaje de exito si el correo es valido
            else{

                mensajeRecovery.classList.add("alert-success");

                mensajeRecovery.innerHTML = "Correo enviado. Favor de verificar su bandeja.";

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
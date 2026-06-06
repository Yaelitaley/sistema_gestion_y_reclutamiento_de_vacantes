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
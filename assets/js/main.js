document.addEventListener("DOMContentLoaded", function(){

    // formulario de registro
    const registerForm = document.getElementById("registerForm");

    // Mmensaje de alerta
    const mensaje = document.getElementById("mensaje");



    // validar formulario de registro
    if(registerForm){

        registerForm.addEventListener("submit", function(e){

            e.preventDefault();

            // aqui obtenemos los valores de los campos del formulario
            const nombre = document.getElementById("nombre").value;
            const correo = document.getElementById("correo").value;
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            // mostramos el mensaje de alerta
            mensaje.classList.remove("d-none");



            // validamos los campos del formulario
            if(nombre === "" || correo === "" || password === "" || confirmPassword === ""){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Rellena todos los campos.";

            }



            // se realiza una validacion para que el nombre solo contenga letras, no numeros
            else if(!isNaN(nombre)){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "El nombre debe contener letras.";

            }



            // validamos que el correo contenga un @ para ser considerado valido
            else if(!correo.includes("@")){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Correo no válido.";

            }



            // la contraseña debe tener minimo 6 caracteres, si no cumple con esta condicion se muestra un mensaje de error
            else if(password.length < 6){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "La contraseña debe tener mínimo 6 caracteres.";

            }



            // deben ser las password iguales, si no lo son se muestra un mensaje de error
            else if(password !== confirmPassword){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Las contraseñas no coinciden.";

            }



            // si todo es correcto se muestra un mensaje de exito
            else{

                mensaje.classList.remove("alert-danger");

                mensaje.classList.add("alert-success");

                mensaje.innerHTML = "Reclutador registrado correctamente.";

            }

        });

    }

});
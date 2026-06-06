document.addEventListener("DOMContentLoaded", function(){

    // Iniciacion de formulario
    const registerForm = document.getElementById("registerForm");

    // mensaje de alerta 
    const mensaje = document.getElementById("mensaje");



    // comienzo de validaciones
    if(registerForm){

        registerForm.addEventListener("submit", function(e){

            e.preventDefault();

            // se hace trim para eliminar espacios al inicio y al final y la obtencion de valores
            const nombre = document.getElementById("nombre").value.trim();

            const correo = document.getElementById("correo").value.trim();

            const password = document.getElementById("password").value.trim();

            const confirmPassword = document.getElementById("confirmPassword").value.trim();



            // mostrar el alert
            mensaje.classList.remove("d-none");



            // usamos remove para eliminar clases de alertas anteriores
            mensaje.classList.remove("alert-danger");

            mensaje.classList.remove("alert-success");



            // aqui validamos el nombre que sean solo letras, incluyendo acentos
            const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/;



            // manejo de if por si maneja espacios vacios mandar un mensaje de rellena todos los campos
            if(nombre === "" || correo === "" || password === "" || confirmPassword === ""){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "Rellena todos los campos.";

            }



            // en este caso se valida el nombre, si no cumple con la expresion regular de solo letras, se muestra un mensaje de error
            else if(!soloLetras.test(nombre)){

                mensaje.classList.add("alert-danger");

                mensaje.innerHTML = "El nombre solo debe contener letras.";

            }



            // validacion del correo que contenga un @, si no lo tiene se muestra un mensaje de error
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



            // Eeste mensaje se muestra si todas las validaciones anteriores se cumplen correctamente, indicando que el administrador se ha registrado correctamente
            else{

                mensaje.classList.add("alert-success");

                mensaje.innerHTML = "Administrador registrado correctamente.";

            }

        });

    }

});


document.addEventListener("DOMContentLoaded", function(){

    // aqui obtenemos el boton de logout para agregarle un evento de click, que al hacer click se muestre una confirmacion para cerrar sesion, si el usuario confirma se muestra un mensaje de sesion cerrada correctamente y se redirecciona a la pagina de login.php
    const btnLogout = document.getElementById("btnLogout");



    if(btnLogout){

        btnLogout.addEventListener("click", function(e){

            e.preventDefault();



            // se realiza la confirmacion para cerrar la sesion
            const confirmar = confirm("¿Deseas cerrar sesión?");



            // si el usuario confirma que desea cerrar sesion, se muestra un mensaje de sesion cerrada correctamente y se redirecciona a la pagina de login.php
            if(confirmar){

                alert("Sesión cerrada correctamente.");



                // se redirecciona
                window.location.href = "login.php";

            }

        });

    }

});
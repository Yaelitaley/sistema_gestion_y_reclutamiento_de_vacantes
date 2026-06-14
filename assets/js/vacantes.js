
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

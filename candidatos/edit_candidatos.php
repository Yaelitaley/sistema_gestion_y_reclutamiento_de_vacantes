
<?php include "includes/header.php"; ?>

<div class="d-flex">



    <!-- CONTENT -->
    <div class="content w-100 p-4">

        <!-- TOP -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold">
                    Editar Candidato
                </h3>

                <p class="text-muted">
                    Modifica la información del candidato.
                </p>

            </div>

        </div>





        <!-- FORM BOX -->
        <div class="table-box">

            <form id="registerForm">

                <!-- NOMBRE -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Nombre completo
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-person-fill"></i>

                        </span>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            value="Juan Pérez">

                    </div>

                </div>





                <!-- CORREO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Correo electrónico
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-envelope-fill"></i>

                        </span>

                        <input
                            type="email"
                            id="correo"
                            class="form-control"
                            value="juan@gmail.com">

                    </div>

                </div>





                <!-- PASSWORD -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Nueva contraseña
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-lock-fill"></i>

                        </span>

                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            placeholder="Nueva contraseña">

                    </div>

                </div>





                <!-- CONFIRM PASSWORD -->
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Confirmar contraseña
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-lock-fill"></i>

                        </span>

                        <input
                            type="password"
                            id="confirmPassword"
                            class="form-control"
                            placeholder="Confirmar contraseña">

                    </div>

                </div>





                <!-- MENSAJE -->
                <div
                    id="mensaje"
                    class="alert mt-3 d-none">

                </div>





                <!-- BOTONES -->
                <div class="d-flex gap-3">

                    <!-- GUARDAR -->
                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-floppy-fill me-2"></i>

                        Guardar cambios

                    </button>





                    <!-- CANCELAR -->
                    <a href="javascript:history.back()"
                    class="cancel-link">

                        Regresar

                </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>

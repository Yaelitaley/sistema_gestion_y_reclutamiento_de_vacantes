<?php include "includes/header.php"; ?>

<main class="register-container">

    <!-- TOP BAR -->
    <div class="top-bar">

        <i class="bi bi-person-plus-fill"></i>

        <span>
            Registro de Candidato
        </span>

    </div>





    <div class="container-fluid py-5 d-flex justify-content-center">

        <div class="register-box">





            <!-- TITULO -->
            <div class="text-center mb-4">

                <div class="login-icon bg-candidato">

                    <i class="bi bi-person-fill"></i>

                </div>

                <p class="mb-1">
                    Registro de cuenta
                </p>

                <h2 class="fw-bold text-candidato">
                    Candidato
                </h2>

            </div>





            <!-- FORMULARIO -->
            <form id="registerForm">

                <!-- NOMBRE -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Nombre Completo
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            placeholder="Nombre Completo">

                    </div>

                </div>





                <!-- CORREO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Correo Electrónico
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>

                        <input
                            type="email"
                            id="correo"
                            class="form-control"
                            placeholder="Correo Electrónico">

                    </div>

                </div>





                <!-- PASSWORD -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Contraseña
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>

                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            placeholder="********">

                    </div>

                </div>





                <!-- CONFIRM PASSWORD -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Confirmar Contraseña
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>

                        <input
                            type="password"
                            id="confirmPassword"
                            class="form-control"
                            placeholder="********">

                    </div>

                </div>





                <!-- MENSAJE -->
                <div
                    id="mensaje"
                    class="alert mt-3 d-none">

                </div>





                <!-- BOTON -->
                <button
                    type="submit"
                    class="btn btn-candidato w-100">

                    Registrarse

                </button>





                <!-- REGRESAR -->
                <div class="text-center mt-3">

                    <a
                        href="login.php"
                        class="cancel-link">

                        Regresar

                    </a>

                </div>

            </form>





            <!-- ALERTA -->
            <div class="alert-box mt-4">

                <i class="bi bi-info-circle-fill"></i>

                <div>

                    <strong>
                        Información
                    </strong>

                    <p class="mb-0">
                        Una vez registrado podrás postularte a las vacantes disponibles.
                    </p>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>

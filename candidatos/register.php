<?php include "includes/header.php"; ?>

<main class="register-container">

    <div class="container-fluid">

        <div class="row min-vh-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 left-panel d-flex flex-column justify-content-center align-items-center">

                <div class="text-center">

                    <h1 class="fw-bold">
                        Únete como
                    </h1>

                    <h1 class="fw-bold text-candidato">
                        Candidato
                    </h1>

                    <p class="mt-3 fw-bold">
                        Crea tu perfil profesional y encuentra nuevas oportunidades laborales.
                    </p>

                    <img
                        src="../assets/img/candidato-register.png"
                        class="img-fluid mt-4 login-image"
                        alt="Candidato">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-person-check-fill"></i>

                    <span>
                        Tu información será utilizada únicamente para procesos de reclutamiento.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- REGISTER BOX -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-candidato">

                            <i class="bi bi-person-plus-fill"></i>

                        </div>

                        <p class="mb-1">
                            Registro de cuenta
                        </p>

                        <h2 class="fw-bold text-candidato">
                            Candidato
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="registerForm">

                        <!-- NOMBRE -->
                        <div class="mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person-fill"></i>
                                </span>

                                <input
                                    type="text"
                                    id="nombre"
                                    class="form-control"
                                    placeholder="Nombre completo">

                            </div>

                        </div>

                        <!-- CORREO -->
                        <div class="mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>

                                <input
                                    type="email"
                                    id="correo"
                                    class="form-control"
                                    placeholder="Correo electrónico">

                            </div>

                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Contraseña">

                            </div>

                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="mb-3">

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

                        <!-- BOTON -->
                        <button type="submit"
                                class="btn btn-candidato w-100">

                            Crear cuenta

                        </button>

                        <!-- MENSAJE -->
                        <div
                            id="mensaje"
                            class="alert mt-3 d-none">

                        </div>

                    </form>

                    <!-- LOGIN -->
                    <div class="text-center mt-4">

                        <a href="login.php"
                           class="forgot-password">

                            ¿Ya tienes cuenta? Inicia sesión

                        </a>

                    </div>

                    <!-- ALERT -->
                    <div class="alert-box mt-4">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <strong>
                                Registro seguro
                            </strong>

                            <p class="mb-0">
                                Completa tus datos correctamente para acceder al sistema.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>
<?php include "includes/header.php"; ?>

<main class="login-container">

    <div class="container-fluid">

        <div class="row min-vh-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 left-panel d-flex flex-column justify-content-center align-items-center">

                <div class="text-center">

                    <h1 class="fw-bold">
                        Bienvenido
                    </h1>

                    <h1 class="fw-bold text-candidato">
                        Candidato
                    </h1>

                    <p class="mt-3 fw-bold">
                        Publica tu currículum y encuentra las mejores ofertas laborales.
                    </p>

                    <img
                        src="../assets/img/candidato02.png"
                        class="img-fluid mt-4 login-image"
                        alt="Candidato">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>
                        Acceso exclusivo para los candidatos registrados.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- LOGIN -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-candidato">

                            <i class="bi bi-person-fill"></i>

                        </div>

                        <p class="mb-1">
                            Iniciar sesión como
                        </p>

                        <h2 class="fw-bold text-candidato">
                            Candidato
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="loginForm">

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
                        <div class="mb-2">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Contraseña">

                                <span class="input-group-text">
                                    <i class="bi bi-eye"></i>
                                </span>

                            </div>

                        </div>

                        <!-- RECOVERY -->
                        <div class="text-end mb-4">

                            <a href="recovery.php"
                               class="forgot-password">

                                ¿Olvidaste tu contraseña?

                            </a>

                        </div>

                        <!-- BOTON -->
                        <button type="submit"
                                class="btn btn-candidato w-100">

                            Iniciar sesión

                        </button>

                        <!-- MENSAJE -->
                        <div
                            id="mensajeLogin"
                            class="alert mt-3 d-none">

                        </div>

                    </form>

                    <!-- REGISTER -->
                    <div class="text-center mt-4">

                        <a href="register.php"
                           class="forgot-password">

                            ¿No tienes cuenta? Regístrate

                        </a>

                    </div>

                    <!-- ALERT -->
                    <div class="alert-box mt-4">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <strong>
                                Acceso seguro
                            </strong>

                            <p class="mb-0">
                                Solo los candidatos registrados pueden acceder al sistema.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>
<?php include "includes/header.php"; ?>

<main class="register-container">

    <div class="container-fluid">

        <div class="row min-vh-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 left-panel d-flex flex-column justify-content-center align-items-center">

                <div class="text-center">

                    <h1 class="fw-bold">
                        Solicitud de
                    </h1>

                    <h1 class="fw-bold text-reclutador">
                        Reclutador
                    </h1>

                    <p class="mt-3 fw-bold">

                        Envía tu información para solicitar acceso
                        como reclutador dentro de la plataforma.

                    </p>

                    <img
                        src="../assets/img/reclutador02.png"
                        class="img-fluid mt-4 login-image"
                        alt="Reclutador">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>

                        El administrador revisará tu solicitud
                        antes de aprobar el acceso.

                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">

                <!-- BOX -->
                <div class="register-box">

                    <!-- TITULO -->
                    <div class="text-center mb-4">

                        <div class="login-icon bg-reclutador">

                            <i class="bi bi-person-plus-fill"></i>

                        </div>

                        <h2 class="fw-bold text-reclutador">

                            Registro de Reclutador

                        </h2>

                    </div>

                    <!-- FORM -->
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
                                    placeholder="Nombre completo">

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
                                    placeholder="Correo electrónico">

                            </div>

                        </div>

                        <!-- EMPRESA -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Empresa
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-buildings-fill"></i>

                                </span>

                                <input
                                    type="text"
                                    id="empresa"
                                    class="form-control"
                                    placeholder="Empresa">

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
                                    placeholder="Contraseña">

                            </div>

                        </div>

                        <!-- CONFIRM -->
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

                        <!-- BOTON -->
                        <button
                            type="submit"
                            class="btn btn-reclutador w-100">

                            Enviar Solicitud

                        </button>

                        <!-- REGRESAR -->
                        <div class="text-center mt-3">

                            <a href="javascript:history.back()"
                               class="cancel-link">

                                Regresar

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>

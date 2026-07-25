<?php include "includes/header.php"; ?>

<main class="register-container ">

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">

        <div class="register-box p-4">

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





                <!-- CLAVE DE SEGURIDAD -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Clave de Seguridad
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="claveSeguridad"
                            class="form-control"
                            placeholder="Clave de seguridad">

                    </div>

                    <small class="text-muted">
                        La necesitarás si olvidas tu contraseña.
                    </small>

                </div>





                <!-- MENSAJE -->
                <div
                    id="mensaje"
                    class="alert mt-3 d-none">

                </div>





                <!-- BOTON REGISTRARSE -->
                <button
                    type="submit"
                    class="btn btn-candidato w-100">
                    Registrarse
                </button>

            </form> <!-- SE CIERRA EL FORMULARIO AQUÍ -->

            <!-- REGRESAR (FUERA DEL FORMULARIO) -->
            <div class="mt-3 d-grid">
                <a href="login.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Regresar
                </a>
            </div>






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


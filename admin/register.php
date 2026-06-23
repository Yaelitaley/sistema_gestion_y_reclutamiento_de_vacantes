<?php include "includes/header.php"; ?>

<main class="register-container">

    <div class="top-bar">

        <i class="bi bi-gear-fill"></i>

        <span>
            Registrar Administrador
        </span>

    </div>

<div class="container-fluid py-5 d-flex justify-content-center">
            <div class="register-box">

            <!-- IMAGEN -->
            <div class="text-center mb-4">

                <img
                    src="../assets/img/imagenadministrador.png"
                    class="img-fluid register-image"
                    alt="Administrador">

            </div>

            <!-- TITULO -->
            <div class="text-center mb-4">

                <p class="fw-bold">
                    Completa la información para registrar al nuevo administrador en el sistema
                </p>

            </div>

            <!-- FORM -->
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

                <!-- PASSWORDS -->
                <div class="row">

                    <!-- PASSWORD -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Contraseña Temporal
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

                    <!-- CONFIRM -->
                    <div class="col-md-6 mb-3">

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

                </div>

                <!-- INFO -->
                <div class="text-center mb-4">

                    <small class="text-muted">

                        Proporciona una contraseña temporal para el administrador.
                        Podrá cambiarla al iniciar sesión.

                    </small>

                </div>

                <!-- BOTON -->
                <div class="text-center">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Registrar Administrador

                    </button>

                    <div
                         id="mensaje"
                      class="alert mt-3 d-none">

                     </div>

                </div>

                <!-- CANCELAR -->
                <a href="javascript:history.back()"
                    class="cancel-link">

                        Regresar

                </a>

                </div>

            </form>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>
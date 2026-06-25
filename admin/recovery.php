<?php include "includes/header.php"; ?>

<main class="login-container">

    <div class="container-fluid">

        <div class="row min-vh-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 left-panel d-flex flex-column justify-content-center align-items-center">

                <div class="text-center">

                    <h1 class="fw-bold">
                        Cambiar
                    </h1>

                    <h1 class="fw-bold ">
                        Contraseña
                    </h1>

                    <p class="mt-3 fw-bold">
                        Recupera el acceso al panel de administración
                    </p>

                    <img
                        src="../assets/img/admin-recovery.png"
                        class="img-fluid mt-4 login-image"
                        alt="Recovery">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>
                        Ingresa el correo de la cuenta y 
                        establece una nueva contraseña.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- RECOVERY BOX -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-primary">

                            <i class="bi bi-person-gear"></i>

                        </div>

                        <p class="mb-1">
                            Cambio de Contraseña
                        </p>

                        <h2 class="fw-bold text-primary">
                            Restablecer contraseña
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="changePasswordForm">

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
            placeholder="Ingresa el correo">

    </div>

</div>

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

<div class="mb-4">

    <label class="form-label fw-bold">
        Confirmar contraseña
    </label>

    <div class="input-group">

        <span class="input-group-text">
            <i class="bi bi-shield-lock-fill"></i>
        </span>

        <input
            type="password"
            id="confirm_password"
            class="form-control"
            placeholder="Confirmar contraseña">

    </div>

</div>

<button
    type="submit"
    id="btnRecovery"
    class="btn btn-primary w-100">

    Actualizar contraseña

</button>

<div id="mensaje" class="alert mt-3 d-none"></div>
                    </form>

                    <!-- VOLVER -->
                    <div class="text-center mt-4">

                        <a href="login.php" class="forgot-password">
                            ← Volver al inicio de sesión
                        </a>

                    </div>

                    <!-- ALERT -->
                    <div class="alert-box mt-4">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <strong>
                                Recuperación segura
                            </strong>

                            <p class="mb-0">
                                Revisa tu correo electrónico para continuar con el proceso.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php include "includes/footer.php"; ?>
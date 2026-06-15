<?php include "includes/header.php"; ?>
<main class="login-container">

    <div class="container-fluid">

        <div class="row min-vh-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 left-panel d-flex flex-column justify-content-center align-items-center">

                <div class="text-center">

                    <h1 class="fw-bold">
                        Recuperar
                    </h1>

                    <h1 class="fw-bold text-reclutador">
                        Contraseña
                    </h1>

                    <p class="mt-3 fw-bold">
                        Recupera el acceso al panel de reclutador
                    </p>

                    <img
                        src="../assets/img/reclutador-recovery.png"
                        class="img-fluid mt-4 login-image"
                        alt="Recovery">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>
                        El sistema enviará un enlace seguro
                        al correo registrado.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- RECOVERY BOX -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-reclutador">

                            <i class="bi bi-envelope-lock-fill"></i>

                        </div>

                        <p class="mb-1">
                            Recuperación de acceso
                        </p>

                        <h2 class="fw-bold text-reclutador">
                            Reclutador
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="recoveryForm">

                        <!-- CORREO -->
                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Correo electrónico
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>

                                <input
                                    type="email"
                                    id="correoRecovery"
                                    name="correo"
                                    class="form-control"
                                    placeholder="Ingresa tu correo">

                            </div>

                        </div>

                        <!-- BOTON -->
                        <button type="submit"
                                id="btnRecovery"
                                class="btn btn-reclutador w-100">
                            Enviar enlace de recuperación
                        </button>

                        <!-- MENSAJE -->
                        <div id="mensajeRecovery" class="alert mt-3 d-none"></div>

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

<script>
document.getElementById('recoveryForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn     = document.getElementById('btnRecovery');
    const mensaje = document.getElementById('mensajeRecovery');

    mensaje.className   = 'alert mt-3 d-none';
    mensaje.textContent = '';

    btn.disabled    = true;
    btn.textContent = 'Enviando...';

    const formData = new FormData();
    formData.append('correo', document.getElementById('correoRecovery').value.trim());

    fetch('actions/recovery_reclutador.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        mensaje.classList.remove('d-none');
        if (data.success) {
            mensaje.classList.add('alert-success');
        } else {
            mensaje.classList.add('alert-danger');
        }
        mensaje.textContent = data.message;
        btn.disabled    = false;
        btn.textContent = 'Enviar enlace de recuperación';
    })
    .catch(() => {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Error de conexión. Intenta de nuevo.';
        btn.disabled    = false;
        btn.textContent = 'Enviar enlace de recuperación';
    });
});
</script>

<?php include "includes/footer.php"; ?>
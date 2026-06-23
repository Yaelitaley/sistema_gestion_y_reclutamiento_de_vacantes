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

<script>
document.getElementById('recoveryForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn     = document.getElementById('btnRecovery');
    const mensaje = document.getElementById('mensaje');

    mensaje.className   = 'alert mt-3 d-none';
    mensaje.textContent = '';

    btn.disabled    = true;
    btn.textContent = 'Enviando...';

    const formData = new FormData();

formData.append(
    'correo',
    document.getElementById('correo').value.trim()
);

formData.append(
    'password',
    document.getElementById('password').value
);

formData.append(
    'confirm_password',
    document.getElementById('confirm_password').value
);

    fetch('actions/recovery_admin.php', {
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
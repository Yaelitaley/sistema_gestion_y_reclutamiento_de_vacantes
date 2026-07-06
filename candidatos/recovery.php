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

                    <h1 class="fw-bold text-candidato">
                        Contraseña
                    </h1>

                    <p class="mt-3 fw-bold">
                        Recupera el acceso a tu cuenta de candidato
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
                        Usa tu clave de seguridad para
                        restablecer tu contraseña.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- RECOVERY BOX -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-candidato">

                            <i class="bi bi-shield-lock-fill"></i>

                        </div>

                        <p class="mb-1">
                            Recuperación de acceso
                        </p>

                        <h2 class="fw-bold text-candidato">
                            Candidato
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="recoveryForm">

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
                                    id="correoRecovery"
                                    class="form-control"
                                    placeholder="Ingresa tu correo">

                            </div>

                        </div>

                        <!-- CLAVE DE SEGURIDAD -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Clave de seguridad
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </span>

                                <input
                                    type="text"
                                    id="claveSeguridadRecovery"
                                    class="form-control"
                                    placeholder="La que registraste">

                            </div>

                        </div>

                        <!-- NUEVA CONTRASEÑA -->
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
                                    id="nuevaPassword"
                                    class="form-control"
                                    placeholder="********">

                            </div>

                        </div>

                        <!-- CONFIRMAR NUEVA CONTRASEÑA -->
                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Confirmar nueva contraseña
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>

                                <input
                                    type="password"
                                    id="confirmNuevaPassword"
                                    class="form-control"
                                    placeholder="********">

                            </div>

                        </div>

                        <!-- BOTON -->
                        <button type="submit"
                                id="btnRecovery"
                                class="btn btn-candidato w-100">
                            Restablecer contraseña
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

    const correo          = document.getElementById('correoRecovery').value.trim();
    const claveSeguridad  = document.getElementById('claveSeguridadRecovery').value.trim();
    const nuevaPassword   = document.getElementById('nuevaPassword').value;
    const confirmPassword = document.getElementById('confirmNuevaPassword').value;

    mensaje.className   = 'alert mt-3 d-none';
    mensaje.textContent = '';

    if (!correo || !claveSeguridad || !nuevaPassword || !confirmPassword) {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Rellena todos los campos.';
        return;
    }

    if (nuevaPassword.length < 6) {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'La nueva contraseña debe tener mínimo 6 caracteres.';
        return;
    }

    if (nuevaPassword !== confirmPassword) {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Las contraseñas no coinciden.';
        return;
    }

    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    const formData = new FormData();
    formData.append('correo', correo);
    formData.append('claveSeguridad', claveSeguridad);
    formData.append('nuevaPassword', nuevaPassword);
    formData.append('confirmPassword', confirmPassword);

    fetch('actions/recovery_candidato.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        mensaje.classList.remove('d-none');
        if (data.success) {
            mensaje.classList.add('alert-success');
            mensaje.textContent = data.message;
            setTimeout(function () {
                window.location.href = 'login.php';
            }, 1500);
        } else {
            mensaje.classList.add('alert-danger');
            mensaje.textContent = data.message;
            btn.disabled    = false;
            btn.textContent = 'Restablecer contraseña';
        }
    })
    .catch(() => {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Error de conexión. Intenta de nuevo.';
        btn.disabled    = false;
        btn.textContent = 'Restablecer contraseña';
    });
});
</script>

<?php include "includes/footer.php"; ?>
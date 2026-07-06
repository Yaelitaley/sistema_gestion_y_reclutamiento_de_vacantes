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
                        Ingresa el correo de la cuenta, tu clave de
                        seguridad personal y una nueva contraseña.
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
                                Clave de seguridad
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-key-fill"></i>
                                </span>

                                <input
                                    type="password"
                                    id="clave_seguridad"
                                    class="form-control"
                                    placeholder="Tu clave de seguridad personal">

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
                                Tu clave de seguridad es personal: solo tú la conoces.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<script>
(function () {

    var form = document.getElementById('changePasswordForm');

    if (!form) {
        console.error('No se encontró #changePasswordForm en la página.');
        return;
    }

    form.addEventListener('submit', function (e) {

        e.preventDefault();

        var btn     = document.getElementById('btnRecovery');
        var mensaje = document.getElementById('mensaje');

        mensaje.className   = 'alert mt-3 d-none';
        mensaje.textContent = '';

        var correo          = document.getElementById('correo').value.trim();
        var claveSeguridad  = document.getElementById('clave_seguridad').value;
        var password        = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirm_password').value;

        if (!correo || !claveSeguridad || !password || !confirmPassword) {
            mensaje.classList.remove('d-none');
            mensaje.classList.add('alert-danger');
            mensaje.textContent = 'Todos los campos son obligatorios.';
            return;
        }

        if (password !== confirmPassword) {
            mensaje.classList.remove('d-none');
            mensaje.classList.add('alert-danger');
            mensaje.textContent = 'Las contraseñas no coinciden.';
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Actualizando...';

        var formData = new FormData();
        formData.append('correo', correo);
        formData.append('clave_seguridad', claveSeguridad);
        formData.append('password', password);
        formData.append('confirm_password', confirmPassword);

        fetch('actions/change_password_admin.php', {
            method: 'POST',
            body: formData
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            mensaje.classList.remove('d-none');
            if (data.success) {
                mensaje.classList.add('alert-success');
                mensaje.textContent = data.message + ' Redirigiendo al inicio de sesión...';
                setTimeout(function () {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                mensaje.classList.add('alert-danger');
                mensaje.textContent = data.message;
                btn.disabled    = false;
                btn.textContent = 'Actualizar contraseña';
            }
        })
        .catch(function () {
            mensaje.classList.remove('d-none');
            mensaje.classList.add('alert-danger');
            mensaje.textContent = 'Error de conexión. Intenta de nuevo.';
            btn.disabled    = false;
            btn.textContent = 'Actualizar contraseña';
        });

    });

})();
</script>

<?php include "includes/footer.php"; ?>
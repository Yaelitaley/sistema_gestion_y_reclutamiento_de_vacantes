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

                    <h1 class="fw-bold ">
                        Administrador Principal
                    </h1>

                    <p class="mt-3 fw-bold">
                        Controla y administra todo el sistema
                    </p>

                    <img
                        src="../assets/img/imagenadministrador.png"
                        class="img-fluid mt-4 login-image"
                        alt="Administrador">

                </div>

                <!-- INFO -->
                <div class="security-box mt-5">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>
                        Acceso exclusivo para el administrador principal del sistema.
                    </span>

                </div>

            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">

                <!-- LOGIN -->
                <div class="login-box">

                    <div class="text-center mb-4">

                        <div class="login-icon bg-primary">

                            <i class="bi bi-shield-lock-fill"></i>

                        </div>

                        <p class="mb-1">
                            Iniciar sesión como
                        </p>

                        <h2 class="fw-bold text-primary">
                            Administrador
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
                                    name="correo"
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
                                    name="password"
                                    class="form-control"
                                    placeholder="Contraseña">

                                <span class="input-group-text">
                                    <i class="bi bi-eye"></i>
                                </span>

                            </div>

                        </div>

                        <!-- RECOVERY -->
                        <div class="text-end mb-4">

                            <a href="recovery.php" class="forgot-password">
                                ¿Olvidaste tu contraseña?
                            </a>

                        </div>

                        <button type="submit"
                                id="btnLogin"
                                class="btn btn-primary w-100">
                            Iniciar sesión
                        </button>

                        <div id="mensaje" class="alert mt-3 d-none"></div>

                    </form>

                    <!-- ALERT -->
                    <div class="alert-box mt-4">

                        <i class="bi bi-exclamation-triangle-fill"></i>

                        <div>

                            <strong>
                                Acceso restringido
                            </strong>

                            <p class="mb-0">
                                Solo el admin puede administrar administradores y reclutadores.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<script>
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn     = document.getElementById('btnLogin');
    const mensaje = document.getElementById('mensaje');

    mensaje.className   = 'alert mt-3 d-none';
    mensaje.textContent = '';

    btn.disabled    = true;
    btn.textContent = 'Verificando...';

    const formData = new FormData();
    formData.append('correo',   document.getElementById('correo').value.trim());
    formData.append('password', document.getElementById('password').value);

    fetch('actions/login_admin.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.php';
        } else {
            mensaje.classList.remove('d-none');
            mensaje.classList.add('alert-danger');
            mensaje.textContent = data.message;
            btn.disabled    = false;
            btn.textContent = 'Iniciar sesión';
        }
    })
    .catch(() => {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Error de conexión. Intenta de nuevo.';
        btn.disabled    = false;
        btn.textContent = 'Iniciar sesión';
    });
});
</script>

<?php include "includes/footer.php"; ?>
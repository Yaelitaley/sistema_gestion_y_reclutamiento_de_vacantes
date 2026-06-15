<?php include "includes/header.php"; ?>

<main class="register-container">

    <div class="top-bar">

        <i class="bi bi-gear-fill"></i>

        <span>
            Registrar Candidato
        </span>

    </div>

    <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">

        <div class="register-box">

            <!-- IMAGEN -->
            <div class="text-center mb-4">

                <img
                    src="../assets/img/admin-register.png"
                    class="img-fluid register-image"
                    alt="Candidato">

            </div>

            <!-- TITULO -->
            <div class="text-center mb-4">

                <p class="fw-bold">
                    Completa la información para registrar al nuevo Candidato en el sistema
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

                        Proporciona una contraseña temporal para el Candidato.
                        Podrá cambiarla al iniciar sesión.

                    </small>

                </div>

                <!-- BOTON -->
                <div class="text-center">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Registrar Candidato

                    </button>

                    <div
                         id="mensaje"
                      class="alert mt-3 d-none">

                            <i class="bi bi-person-plus-fill"></i>

                        </div>

                        <p class="mb-1">
                            Registro de cuenta
                        </p>

                        <h2 class="fw-bold text-candidato">
                            Candidato
                        </h2>

                    </div>

                    <!-- FORM -->
                    <form id="registerForm">

                        <!-- NOMBRE -->
                        <div class="mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person-fill"></i>
                                </span>

                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    class="form-control"
                                    placeholder="Nombre completo">

                            </div>

                        </div>

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
                        <div class="mb-3">

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

                            </div>

                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>

                                <input
                                    type="password"
                                    id="confirmPassword"
                                    name="confirmPassword"
                                    class="form-control"
                                    placeholder="Confirmar contraseña">

                            </div>

                        </div>

                        <!-- BOTON -->
                        <button type="submit"
                                id="btnRegistrar"
                                class="btn btn-candidato w-100">
                            Crear cuenta
                        </button>

                        <!-- MENSAJE -->
                        <div id="mensaje" class="alert mt-3 d-none"></div>

                    </form>

                    <!-- LOGIN -->
                    <div class="text-center mt-4">

                        <a href="login.php" class="forgot-password">
                            ¿Ya tienes cuenta? Inicia sesión
                        </a>

                    </div>

                    <!-- ALERT -->
                    <div class="alert-box mt-4">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <strong>
                                Registro seguro
                            </strong>

                            <p class="mb-0">
                                Completa tus datos correctamente para acceder al sistema.
                            </p>

                        </div>

                    </div>

                     </div>


                </div>

                <a href="javascript:history.back()"
                    class="cancel-link">

                        Regresar

                </a>

            </form>

        </div>

    </div>

</main>

<script>
document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn     = document.getElementById('btnRegistrar');
    const mensaje = document.getElementById('mensaje');

    mensaje.className   = 'alert mt-3 d-none';
    mensaje.textContent = '';

    btn.disabled    = true;
    btn.textContent = 'Registrando...';

    const formData = new FormData();
    formData.append('nombre',          document.getElementById('nombre').value.trim());
    formData.append('correo',          document.getElementById('correo').value.trim());
    formData.append('password',        document.getElementById('password').value);
    formData.append('confirmPassword', document.getElementById('confirmPassword').value);

    fetch('actions/register_candidato.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        mensaje.classList.remove('d-none');
        if (data.success) {
            mensaje.classList.add('alert-success');
            mensaje.textContent = data.message;
            document.getElementById('registerForm').reset();
        } else {
            mensaje.classList.add('alert-danger');
            mensaje.textContent = data.message;
        }
        btn.disabled    = false;
        btn.textContent = 'Crear cuenta';
    })
    .catch(() => {
        mensaje.classList.remove('d-none');
        mensaje.classList.add('alert-danger');
        mensaje.textContent = 'Error de conexión. Intenta de nuevo.';
        btn.disabled    = false;
        btn.textContent = 'Crear cuenta';
    });
});
</script>

<?php include "includes/footer.php"; ?>
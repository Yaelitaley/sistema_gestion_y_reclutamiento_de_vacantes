document.addEventListener("DOMContentLoaded", function () {

    const btnLogout = document.getElementById("btnLogout");

    if (btnLogout) {
        btnLogout.addEventListener("click", function (e) {
            const confirmar = confirm("¿Deseas cerrar sesión?");
            if (!confirmar) {
                e.preventDefault();
            }
        });
    }

    const botonesEliminar = document.querySelectorAll(".btnEliminar");

    botonesEliminar.forEach(function (boton) {
        boton.addEventListener("click", function () {

            const id = boton.dataset.id;
            const confirmar = confirm("¿Deseas eliminar este administrador?");

            if (!confirmar) return;

            fetch("actions/delete_administrador.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + id
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);

                if (data.success) {
                    boton.closest("tr").remove();
                }
            })
            .catch(error => {
                console.error(error);
                alert("Error de conexión.");
            });

        });
    });

});

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


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editForm");
    const mensaje = document.getElementById("mensaje");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("adminId").value;
        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const password = document.getElementById("password").value.trim();
        const confirmPassword = document.getElementById("confirmPassword").value.trim();

        mensaje.classList.remove("d-none", "alert-danger", "alert-success");

        if (nombre === "" || correo === "") {
            mensaje.classList.add("alert-danger");
            mensaje.innerHTML = "Nombre y correo son obligatorios.";
            return;
        }

        if (password !== confirmPassword) {
            mensaje.classList.add("alert-danger");
            mensaje.innerHTML = "Las contraseñas no coinciden.";
            return;
        }

        const datos = new URLSearchParams({ id, nombre, correo, password });

        fetch("actions/update_administrador.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: datos
        })
        .then(res => res.json())
        .then(data => {
            mensaje.classList.add(data.success ? "alert-success" : "alert-danger");
            mensaje.innerHTML = data.message;
        })
        .catch(() => {
            mensaje.classList.add("alert-danger");
            mensaje.innerHTML = "Error de conexión.";
        });
    });
});

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
<?php
require_once '../config/config.php';
require_once '../config/connection.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index_administrador.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, nombre_completo, correo FROM usuarios WHERE id = ? AND rol_id IN (1,2)");
$stmt->bind_param('i', $id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
    header("Location: index_administrador.php");
    exit;
}

include "includes/header.php";
?>

<div class="d-flex">

    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Editar Administrador</h3>
                <p class="text-muted">Modifica la información del Administrador.</p>
            </div>
        </div>

        <div class="table-box">

            <form id="editForm">

                <input type="hidden" id="adminId" value="<?php echo htmlspecialchars($admin['id']); ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            value="<?php echo htmlspecialchars($admin['nombre_completo'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input
                            type="email"
                            id="correo"
                            class="form-control"
                            value="<?php echo htmlspecialchars($admin['correo']); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nueva contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            placeholder="Déjalo vacío si no la quieres cambiar">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Confirmar contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            id="confirmPassword"
                            class="form-control"
                            placeholder="Confirmar contraseña">
                    </div>
                </div>

                <div id="mensaje" class="alert mt-3 d-none"></div>

                <div class="d-flex gap-3">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar cambios
                    </button>

                    <a href="javascript:history.back()" class="cancel-link">
                        Regresar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
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
</script>

<?php include "includes/footer.php"; ?>
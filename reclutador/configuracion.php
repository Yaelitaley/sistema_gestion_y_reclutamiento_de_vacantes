<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];
$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST['form'] ?? '';

    if ($form === 'cuenta') {
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if ($nombre === '') {
            redirect_to('configuracion.php?type=danger&msg=' . urlencode('El nombre no puede estar vacío.'));
        }

        $stmt = $conn->prepare("UPDATE reclutadores SET nombre_completo = ?, telefono = ? WHERE usuario_id = ?");
        $stmt->bind_param('ssi', $nombre, $telefono, $usuarioId);
        $stmt->execute();
        $stmt->close();

        redirect_to('configuracion.php?type=success&msg=' . urlencode('Datos de cuenta actualizados.'));
    }

    if ($form === 'seguridad') {
        $passwordActual = trim($_POST['password_actual'] ?? '');
        $passwordNueva  = trim($_POST['password_nueva'] ?? '');
        $passwordConfirmar = trim($_POST['password_confirmar'] ?? '');

        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();
        $hashActual = $stmt->get_result()->fetch_assoc()['password'] ?? '';
        $stmt->close();

        if (!password_verify($passwordActual, $hashActual)) {
            redirect_to('configuracion.php?type=danger&msg=' . urlencode('La contraseña actual no es correcta.'));
        }

        if (strlen($passwordNueva) < 6) {
            redirect_to('configuracion.php?type=danger&msg=' . urlencode('La nueva contraseña debe tener al menos 6 caracteres.'));
        }

        if ($passwordNueva !== $passwordConfirmar) {
            redirect_to('configuracion.php?type=danger&msg=' . urlencode('Las contraseñas no coinciden.'));
        }

        $hash = password_hash($passwordNueva, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hash, $usuarioId);
        $stmt->execute();
        $stmt->close();

        redirect_to('configuracion.php?type=success&msg=' . urlencode('Contraseña actualizada correctamente.'));
    }
}

$stmt = $conn->prepare("SELECT r.*, u.correo, e.nombre AS empresa
                         FROM reclutadores r
                         INNER JOIN usuarios u ON r.usuario_id = u.id
                         LEFT JOIN empresas e ON r.empresa_id = e.id
                         WHERE r.usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();
$stmt->close();

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div class="mb-4">
            <h2 class="fw-bold">Configuración</h2>
            <p class="text-muted">Administra los datos y la seguridad de tu cuenta.</p>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Datos de la Cuenta</h5>
                    <form method="POST">
                        <input type="hidden" name="form" value="cuenta">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre_completo" class="form-control" value="<?= e($datos['nombre_completo'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo</label>
                            <input type="email" class="form-control" value="<?= e($datos['correo'] ?? '') ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= e($datos['telefono'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Empresa</label>
                            <input type="text" class="form-control" value="<?= e($datos['empresa'] ?? 'Sin empresa') ?>" disabled>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-save-fill me-2"></i>Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Seguridad</h5>
                    <form method="POST">
                        <input type="hidden" name="form" value="seguridad">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña actual</label>
                            <input type="password" name="password_actual" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva contraseña</label>
                            <input type="password" name="password_nueva" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmar" class="form-control" required minlength="6">
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-shield-lock-fill me-2"></i>Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
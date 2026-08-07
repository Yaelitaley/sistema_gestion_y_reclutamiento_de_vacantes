<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 4) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT c.id, c.nombre_completo, u.email, u.clave_seguridad
                         FROM candidatos c
                         INNER JOIN usuarios u ON u.id = c.usuario_id
                         WHERE c.usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$cuenta = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cuenta) {
    die('No se encontró la cuenta asociada a este usuario.');
}

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_password') {
    $actual = trim($_POST['password_actual'] ?? '');
    $nueva = trim($_POST['password_nueva'] ?? '');
    $confirmar = trim($_POST['password_confirmar'] ?? '');

    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $hashActual = $stmt->get_result()->fetch_assoc()['password'] ?? '';
    $stmt->close();

    if (!password_verify($actual, $hashActual)) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('La contraseña actual no es correcta.'));
    } elseif (strlen($nueva) < 6) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('La nueva contraseña debe tener al menos 6 caracteres.'));
    } elseif ($nueva !== $confirmar) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('Las contraseñas nuevas no coinciden.'));
    } else {
        $hash = password_hash($nueva, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hash, $usuarioId);
        $stmt->execute();
        $stmt->close();
        redirect_to('configuracion.php?type=success&msg=' . urlencode('Contraseña actualizada correctamente.'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_clave_seguridad') {
    $nuevaClave = trim($_POST['clave_seguridad'] ?? '');
    $confirmarClave = trim($_POST['confirmar_clave_seguridad'] ?? '');

    if (strlen($nuevaClave) < 4) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('La clave de seguridad debe tener al menos 4 caracteres.'));
    } elseif ($nuevaClave !== $confirmarClave) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('Las claves de seguridad no coinciden.'));
    } else {
        $hashClave = password_hash($nuevaClave, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET clave_seguridad = ? WHERE id = ?");
        $stmt->bind_param('si', $hashClave, $usuarioId);
        $stmt->execute();
        $stmt->close();
        redirect_to('configuracion.php?type=success&msg=' . urlencode('Clave de seguridad actualizada correctamente.'));
    }
}

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Configuración de la cuenta</h3>
                <p class="text-muted">Administra la seguridad de tu cuenta, <?= e($cuenta['nombre_completo']) ?>.</p>
            </div>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- CAMBIAR CONTRASEÑA -->
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        Cambiar contraseña
                    </h5>
                    <form method="POST">
                        <input type="hidden" name="accion" value="cambiar_password">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña actual</label>
                            <input type="password" name="password_actual" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva contraseña</label>
                            <input type="password" name="password_nueva" class="form-control" minlength="6" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmar" class="form-control" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-candidato">
                            <i class="bi bi-floppy-fill me-2"></i>
                            Actualizar contraseña
                        </button>
                    </form>
                </div>
            </div>
            <!-- CLAVE DE SEGURIDAD -->
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-key-fill me-2"></i>
                        Clave de seguridad
                    </h5>
                    <p class="text-muted">
                        Se usa junto con tu correo para recuperar tu contraseña si la olvidas.
                    </p>
                    <form method="POST">
                        <input type="hidden" name="accion" value="cambiar_clave_seguridad">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva clave de seguridad</label>
                            <input type="password" name="clave_seguridad" class="form-control" minlength="4" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirmar clave de seguridad</label>
                            <input type="password" name="confirmar_clave_seguridad" class="form-control" minlength="4" required>
                        </div>
                        <button type="submit" class="btn btn-candidato">
                            <i class="bi bi-floppy-fill me-2"></i>
                            Actualizar clave de seguridad
                        </button>
                    </form>
                </div>
            </div>
            <!-- INFO DE LA CUENTA -->
            <div class="col-lg-12">
                <div class="table-box">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-person-badge-fill me-2"></i>
                        Datos de acceso
                    </h5>
                    <p><strong>Correo de acceso:</strong> <?= e($cuenta['email']) ?></p>
                    <p class="text-muted mb-0">
                        Para cambiar tu correo, nombre u otros datos personales ve a
                        <a href="editar_perfil.php">Editar Perfil</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

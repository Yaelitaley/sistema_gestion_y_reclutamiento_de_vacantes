<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    redirect_to('index_reclutador.php');
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $empresaId = (int) ($_POST['empresa_id'] ?? 0);
    $estado = trim($_POST['estado'] ?? '');

    if ($nombreCompleto === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $empresaId <= 0
        || !in_array($estado, ['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'], true)) {
        $mensaje = 'Completa todos los campos correctamente.';
        $tipoMensaje = 'danger';
    } else {
        $stmt = $conn->prepare("SELECT usuario_id FROM reclutadores WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            redirect_to('index_reclutador.php');
        }
        $usuarioId = (int) $row['usuario_id'];

        $stmt = $conn->prepare("UPDATE reclutadores SET nombre_completo = ?, telefono = ?, empresa_id = ?, estado = ? WHERE id = ?");
        $stmt->bind_param('ssisi', $nombreCompleto, $telefono, $empresaId, $estado, $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE usuarios SET email = ?, correo = ?, nombre_completo = ? WHERE id = ?");
        $stmt->bind_param('sssi', $correo, $correo, $nombreCompleto, $usuarioId);
        $stmt->execute();
        $stmt->close();

        redirect_to('index_reclutador.php?type=success&msg=' . urlencode('Reclutador actualizado correctamente.'));
    }
}

$stmt = $conn->prepare("SELECT r.id, r.nombre_completo, r.telefono, r.empresa_id, r.estado, u.correo
                         FROM reclutadores r
                         INNER JOIN usuarios u ON r.usuario_id = u.id
                         WHERE r.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$reclutador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reclutador) {
    redirect_to('index_reclutador.php');
}

$empresas = $conn->query("SELECT id, nombre FROM empresas ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>
        <div class="content w-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold">Editar Reclutador</h3>
                    <p class="text-muted">Actualiza los datos del reclutador.</p>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <div class="table-box">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= (int) $reclutador['id'] ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre_completo" class="form-control" value="<?= e($reclutador['nombre_completo']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Correo</label>
                            <input type="email" name="correo" class="form-control" value="<?= e($reclutador['correo']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" value="<?= e($reclutador['telefono']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Empresa</label>
                            <select name="empresa_id" class="form-select" required>
                                <?php foreach ($empresas as $emp): ?>
                                <option value="<?= (int) $emp['id'] ?>" <?= (int) $reclutador['empresa_id'] === (int) $emp['id'] ? 'selected' : '' ?>>
                                    <?= e($emp['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select" required>
                                <?php foreach (['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'] as $est): ?>
                                <option value="<?= e($est) ?>" <?= $reclutador['estado'] === $est ? 'selected' : '' ?>><?= e($est) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end gap-3">
                        <a href="index_reclutador.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy-fill me-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

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

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$tablasOk = admin_required_tables_ok($conn, ['usuarios', 'administradores']);
$mensaje = '';
$tipoMensaje = '';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$tablasOk) {
    $admin = null;
} elseif ($id <= 0) {
    redirect_to('index_administrador.php?type=danger&msg=' . urlencode('Administrador no válido.'));
} else {
    $stmt = $conn->prepare('SELECT a.id, a.usuario_id, a.nombre_completo, a.correo, a.empresa, a.estado, u.email
                            FROM administradores a
                            INNER JOIN usuarios u ON u.id = a.usuario_id
                            WHERE a.id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$admin) {
        redirect_to('index_administrador.php?type=danger&msg=' . urlencode('Administrador no encontrado.'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk && $admin) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $empresa = trim($_POST['empresa'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Activo');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if ($nombre === '' || $correo === '' || $empresa === '') {
        $mensaje = 'Nombre, correo y empresa son obligatorios.';
        $tipoMensaje = 'danger';
    } elseif (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u', $nombre)) {
        $mensaje = 'El nombre solo debe contener letras y espacios.';
        $tipoMensaje = 'danger';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
        $tipoMensaje = 'danger';
    } elseif ($password !== '' && strlen($password) < 6) {
        $mensaje = 'La nueva contraseña debe tener al menos 6 caracteres.';
        $tipoMensaje = 'danger';
    } elseif ($password !== $confirmPassword) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($estado, ['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } else {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('si', $correo, $admin['usuario_id']);
        $stmt->execute();
        $stmt->store_result();
        $correoExiste = $stmt->num_rows > 0;
        $stmt->close();

        if ($correoExiste) {
            $mensaje = 'Ese correo ya pertenece a otro usuario.';
            $tipoMensaje = 'danger';
        } else {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare('UPDATE administradores SET nombre_completo = ?, correo = ?, empresa = ?, estado = ? WHERE id = ?');
                $stmt->bind_param('ssssi', $nombre, $correo, $empresa, $estado, $id);
                $stmt->execute();
                $stmt->close();

                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, password = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $correo, $correo, $hash, $admin['usuario_id']);
                } else {
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ? WHERE id = ?');
                    $stmt->bind_param('ssi', $correo, $correo, $admin['usuario_id']);
                }
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                redirect_to('index_administrador.php?type=success&msg=' . urlencode('Administrador actualizado correctamente.'));
            } catch (Throwable $e) {
                $conn->rollback();
                $mensaje = 'Error al actualizar: ' . $e->getMessage();
                $tipoMensaje = 'danger';
            }
        }
    }

    $admin['nombre_completo'] = $nombre;
    $admin['correo'] = $correo;
    $admin['empresa'] = $empresa;
    $admin['estado'] = $estado;
}

include 'includes/header.php';

?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

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


        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Editar Administrador</h3>
                <p class="text-muted">Modifica la información del administrador.</p>
            </div>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Faltan tablas para esta pantalla. Importa <strong>database_chris.sql</strong>.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($admin): ?>
            <div class="table-box">
                <form id="adminEditForm" method="POST" autocomplete="off">
                    <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= e($admin['nombre_completo']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="correo" id="correo" class="form-control" value="<?= e($admin['correo']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Empresa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" name="empresa" class="form-control" value="<?= e($admin['empresa']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-select" required>
                            <?php foreach (['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'] as $estado): ?>
                                <option value="<?= e($estado) ?>" <?= $admin['estado'] === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Dejar vacío para no cambiar">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Confirmar nueva contraseña">
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-2"></i>Guardar cambios</button>
                        <a href="index_administrador.php" class="cancel-link">Regresar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>


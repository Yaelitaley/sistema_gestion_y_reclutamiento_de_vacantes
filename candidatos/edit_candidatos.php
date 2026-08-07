<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    redirect_to('index_candidatos.php?type=danger&msg=' . urlencode('Candidato no válido.'));
}

$stmt = $conn->prepare('SELECT c.id, c.usuario_id, c.nombre_completo, c.correo, c.estado, u.email
                         FROM candidatos c
                         INNER JOIN usuarios u ON u.id = c.usuario_id
                         WHERE c.id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$candidato = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$candidato) {
    redirect_to('index_candidatos.php?type=danger&msg=' . urlencode('Candidato no encontrado.'));
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if ($nombre === '' || $correo === '') {
        $mensaje = 'Nombre y correo son obligatorios.';
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
    } else {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('si', $correo, $candidato['usuario_id']);
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
                $stmt = $conn->prepare('UPDATE candidatos SET nombre_completo = ?, correo = ? WHERE id = ?');
                $stmt->bind_param('ssi', $nombre, $correo, $id);
                $stmt->execute();
                $stmt->close();

                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, nombre_completo = ?, password = ? WHERE id = ?');
                    $stmt->bind_param('ssssi', $correo, $correo, $nombre, $hash, $candidato['usuario_id']);
                } else {
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, nombre_completo = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $correo, $correo, $nombre, $candidato['usuario_id']);
                }
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                redirect_to('index_candidatos.php?type=success&msg=' . urlencode('Candidato actualizado correctamente.'));
            } catch (Throwable $e) {
                $conn->rollback();
                $mensaje = 'Error al actualizar: ' . $e->getMessage();
                $tipoMensaje = 'danger';
            }
        }
    }

    $candidato['nombre_completo'] = $nombre;
    $candidato['correo'] = $correo;
}

include "includes/header.php";
?>
<div class="d-flex">
    <!-- CONTENT -->
    <div class="content w-100 p-4">
        <!-- TOP -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">
                    Editar Candidato
                </h3>
                <p class="text-muted">
                    Modifica la información de la cuenta del candidato.
                </p>
            </div>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <!-- FORM BOX -->
        <div class="table-box">
            <form method="POST" autocomplete="off">
                <input type="hidden" name="id" value="<?= (int) $candidato['id'] ?>">
                <!-- NOMBRE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Nombre completo
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            value="<?= e($candidato['nombre_completo']) ?>"
                            required>
                    </div>
                </div>
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
                            name="correo"
                            id="correo"
                            class="form-control"
                            value="<?= e($candidato['correo']) ?>"
                            required>
                    </div>
                </div>
                <!-- PASSWORD -->
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
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Dejar vacío para no cambiar">
                    </div>
                </div>
                <!-- CONFIRM PASSWORD -->
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        Confirmar contraseña
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input
                            type="password"
                            name="confirmPassword"
                            id="confirmPassword"
                            class="form-control"
                            placeholder="Confirmar contraseña">
                    </div>
                </div>
                <!-- BOTONES -->
                <div class="d-flex gap-3">
                    <!-- GUARDAR -->
                    <button
                        type="submit"
                        class="btn btn-candidato">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar cambios
                    </button>
                    <a href="index_candidatos.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Regresar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$tablasOk = admin_required_tables_ok($conn, ['usuarios', 'administradores']);
$mensaje = '';
$tipoMensaje = '';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$tablasOk) {
    $admin = null;
} elseif ($id <= 0) {
    redirect_to('index_administrador.php?type=danger&msg=' . urlencode('Administrador no válido.'));
} else {
    $stmt = $conn->prepare('SELECT a.id, a.usuario_id, a.nombre_completo, a.correo, a.empresa, a.estado, a.foto_perfil, u.email
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
    $claveSeguridad = trim($_POST['clave_seguridad'] ?? '');
    $confirmClaveSeguridad = trim($_POST['confirm_clave_seguridad'] ?? '');

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
    } elseif ($claveSeguridad !== '' && strlen($claveSeguridad) < 4) {
        $mensaje = 'La clave de seguridad debe tener al menos 4 caracteres.';
        $tipoMensaje = 'danger';
    } elseif ($claveSeguridad !== $confirmClaveSeguridad) {
        $mensaje = 'La clave de seguridad no coincide con su confirmación.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($estado, ['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && !in_array(strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true)) {
        $mensaje = 'La foto debe ser JPG, JPEG o PNG.';
        $tipoMensaje = 'danger';
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && $_FILES['foto']['size'] > 3 * 1024 * 1024) {
        $mensaje = 'La foto no debe superar 3 MB.';
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
                // ----- Foto de perfil (opcional) -----
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $archivoFoto = $_FILES['foto'];
                    $extensionFoto = strtolower(pathinfo($archivoFoto['name'], PATHINFO_EXTENSION));
                    $finfoFoto = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeFoto = finfo_file($finfoFoto, $archivoFoto['tmp_name']);
                    finfo_close($finfoFoto);

                    if (in_array($mimeFoto, ['image/jpeg', 'image/png'], true) && @getimagesize($archivoFoto['tmp_name']) !== false) {
                        $carpetaDestinoFoto = __DIR__ . '/../assets/uploads/perfil/';
                        if (!is_dir($carpetaDestinoFoto)) {
                            mkdir($carpetaDestinoFoto, 0755, true);
                        }

                        $nombreArchivoFoto = 'admin_' . $id . '_' . time() . '.' . $extensionFoto;
                        $rutaDestinoFoto = $carpetaDestinoFoto . $nombreArchivoFoto;
                        $rutaRelativaFoto = 'assets/uploads/perfil/' . $nombreArchivoFoto;

                        if (move_uploaded_file($archivoFoto['tmp_name'], $rutaDestinoFoto)) {
                            $stmtFoto = $conn->prepare('UPDATE administradores SET foto_perfil = ? WHERE id = ?');
                            $stmtFoto->bind_param('si', $rutaRelativaFoto, $id);
                            $stmtFoto->execute();
                            $stmtFoto->close();

                            if (!empty($admin['foto_perfil'])) {
                                $rutaAnteriorFoto = __DIR__ . '/../' . $admin['foto_perfil'];
                                if (is_file($rutaAnteriorFoto) && $rutaAnteriorFoto !== $rutaDestinoFoto) {
                                    @unlink($rutaAnteriorFoto);
                                }
                            }
                        }
                    }
                }

                $stmt = $conn->prepare('UPDATE administradores SET nombre_completo = ?, correo = ?, empresa = ?, estado = ? WHERE id = ?');
                $stmt->bind_param('ssssi', $nombre, $correo, $empresa, $estado, $id);
                $stmt->execute();
                $stmt->close();

                if ($password !== '' && $claveSeguridad !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $hashClave = password_hash($claveSeguridad, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, password = ?, clave_seguridad = ? WHERE id = ?');
                    $stmt->bind_param('ssssi', $correo, $correo, $hash, $hashClave, $admin['usuario_id']);
                } elseif ($password !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, password = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $correo, $correo, $hash, $admin['usuario_id']);
                } elseif ($claveSeguridad !== '') {
                    $hashClave = password_hash($claveSeguridad, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('UPDATE usuarios SET email = ?, correo = ?, clave_seguridad = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $correo, $correo, $hashClave, $admin['usuario_id']);
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
                <form id="adminEditForm" method="POST" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">

                    <div class="text-center mb-4">
                        <img
                            src="<?= !empty($admin['foto_perfil']) ? '../' . e($admin['foto_perfil']) . '?v=' . time() : '../assets/img/imagenadministrador.png' ?>"
                            id="previewFotoPerfil"
                            class="rounded-circle shadow mb-3"
                            width="150"
                            height="150"
                            style="object-fit:cover;"
                            alt="Foto">
                        <div class="col-md-6 mx-auto text-start">
                            <label class="form-label fw-bold">Cambiar foto de perfil</label>
                            <input type="file" name="foto" id="inputFotoPerfil" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                            <small class="text-muted">Formatos permitidos: JPG, JPEG o PNG. Tamaño máximo 3 MB.</small>
                        </div>
                    </div>

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

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Clave de seguridad personal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="clave_seguridad" id="clave_seguridad" class="form-control" placeholder="Dejar vacío para no cambiarla" minlength="4">
                        </div>
                        <small class="text-muted">Se usa junto con tu correo para recuperar tu contraseña si la olvidas. Solo tú debes conocerla.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirmar clave de seguridad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="confirm_clave_seguridad" id="confirm_clave_seguridad" class="form-control" placeholder="Confirmar clave de seguridad" minlength="4">
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    var inputFoto = document.getElementById("inputFotoPerfil");
    var preview = document.getElementById("previewFotoPerfil");
    if (inputFoto && preview) {
        inputFoto.addEventListener("change", function () {
            if (this.files && this.files[0]) {
                var lector = new FileReader();
                lector.onload = function (e) { preview.src = e.target.result; };
                lector.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
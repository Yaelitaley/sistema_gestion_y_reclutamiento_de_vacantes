<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT r.id, r.nombre_completo, r.telefono, r.empresa_id, r.foto_perfil, u.email
                         FROM reclutadores r
                         INNER JOIN usuarios u ON r.usuario_id = u.id
                         WHERE r.usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$reclutador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reclutador) {
    die('No se encontró el perfil de reclutador asociado a este usuario.');
}
$reclutadorId = (int) $reclutador['id'];

$empresas = $conn->query("SELECT id, nombre FROM empresas ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

// ----- Foto de perfil (con archivo real si existe, o imagen por defecto) -----
$fotoPerfilSrc = !empty($reclutador['foto_perfil'])
    ? '../' . htmlspecialchars($reclutador['foto_perfil']) . '?v=' . time()
    : '../assets/img/reclutador02.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $empresaId = (int) ($_POST['empresa_id'] ?? 0);
    $nuevaPassword = trim($_POST['nueva_password'] ?? '');
    $confirmPassword = trim($_POST['confirmar_password'] ?? '');

    if ($nombreCompleto === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $empresaId <= 0) {
        redirect_to('editar_perfil.php?type=danger&msg=' . urlencode('Completa el nombre, correo y empresa correctamente.'));
    }

    if ($nuevaPassword !== '' && $nuevaPassword !== $confirmPassword) {
        redirect_to('editar_perfil.php?type=danger&msg=' . urlencode('Las contraseñas no coinciden.'));
    }

    // ----- Foto de perfil (opcional) -----
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $archivoFoto = $_FILES['foto'];
        $extensionFoto = strtolower(pathinfo($archivoFoto['name'], PATHINFO_EXTENSION));
        $finfoFoto = finfo_open(FILEINFO_MIME_TYPE);
        $mimeFoto = finfo_file($finfoFoto, $archivoFoto['tmp_name']);
        finfo_close($finfoFoto);

        if (!in_array($extensionFoto, ['jpg', 'jpeg', 'png'], true) || !in_array($mimeFoto, ['image/jpeg', 'image/png'], true)) {
            redirect_to('editar_perfil.php?type=danger&msg=' . urlencode('La foto debe ser JPG, JPEG o PNG.'));
        }

        if ($archivoFoto['size'] > 3 * 1024 * 1024) {
            redirect_to('editar_perfil.php?type=danger&msg=' . urlencode('La foto no debe superar 3 MB.'));
        }

        if (@getimagesize($archivoFoto['tmp_name']) === false) {
            redirect_to('editar_perfil.php?type=danger&msg=' . urlencode('El archivo no es una imagen válida.'));
        }

        $carpetaDestinoFoto = __DIR__ . '/../assets/uploads/perfil/';
        if (!is_dir($carpetaDestinoFoto)) {
            mkdir($carpetaDestinoFoto, 0755, true);
        }

        $nombreArchivoFoto = 'reclutador_' . $reclutadorId . '_' . time() . '.' . $extensionFoto;
        $rutaDestinoFoto = $carpetaDestinoFoto . $nombreArchivoFoto;
        $rutaRelativaFoto = 'assets/uploads/perfil/' . $nombreArchivoFoto;

        if (move_uploaded_file($archivoFoto['tmp_name'], $rutaDestinoFoto)) {
            $stmt = $conn->prepare("UPDATE reclutadores SET foto_perfil = ? WHERE id = ?");
            $stmt->bind_param('si', $rutaRelativaFoto, $reclutadorId);
            $stmt->execute();
            $stmt->close();

            if (!empty($reclutador['foto_perfil'])) {
                $rutaAnteriorFoto = __DIR__ . '/../' . $reclutador['foto_perfil'];
                if (is_file($rutaAnteriorFoto) && $rutaAnteriorFoto !== $rutaDestinoFoto) {
                    @unlink($rutaAnteriorFoto);
                }
            }
        }
    }

    $stmt = $conn->prepare("UPDATE reclutadores SET nombre_completo = ?, telefono = ?, empresa_id = ? WHERE id = ?");
    $stmt->bind_param('ssii', $nombreCompleto, $telefono, $empresaId, $reclutadorId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE usuarios SET email = ?, correo = ?, nombre_completo = ? WHERE id = ?");
    $stmt->bind_param('sssi', $correo, $correo, $nombreCompleto, $usuarioId);
    $stmt->execute();
    $stmt->close();

    if ($nuevaPassword !== '') {
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hash, $usuarioId);
        $stmt->execute();
        $stmt->close();
    }

    redirect_to('editar_perfil.php?type=success&msg=' . urlencode('Perfil actualizado correctamente.'));
}

include "includes/header.php";
?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Editar Perfil</h2>
                <p class="text-muted">Actualiza la información de tu perfil de reclutador.</p>
            </div>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="table-box">
            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- FOTO -->
                    <div class="col-md-12 text-center mb-5">
                        <img
                            src="<?= $fotoPerfilSrc ?>"
                            id="previewFotoPerfil"
                            class="rounded-circle shadow mb-3"
                            width="170"
                            height="170"
                            style="object-fit:cover;"
                            alt="Foto">
                        <div class="col-md-6 mx-auto text-start">
                            <label class="form-label fw-bold">Cambiar foto de perfil</label>
                            <input type="file" name="foto" id="inputFotoPerfil" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                            <small class="text-muted">Formatos permitidos: JPG, JPEG o PNG. Tamaño máximo 3 MB.</small>
                        </div>
                    </div>

                    <!-- NOMBRE COMPLETO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control" value="<?= e($reclutador['nombre_completo']) ?>" required>
                    </div>

                    <!-- CORREO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= e($reclutador['email']) ?>" required>
                    </div>

                    <!-- TELEFONO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="tel" name="telefono" class="form-control" value="<?= e($reclutador['telefono']) ?>">
                    </div>

                    <!-- EMPRESA -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Empresa</label>
                        <select name="empresa_id" class="form-select" required>
                            <option value="">Selecciona una empresa</option>
                            <?php foreach ($empresas as $emp): ?>
                            <option value="<?= (int) $emp['id'] ?>" <?= (int) $reclutador['empresa_id'] === (int) $emp['id'] ? 'selected' : '' ?>>
                                <?= e($emp['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- CONTRASEÑA -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" name="nueva_password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    </div>

                    <!-- CONFIRMAR -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Confirmar Contraseña</label>
                        <input type="password" name="confirmar_password" class="form-control" placeholder="********">
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end gap-3">
                    <a href="javascript:history.back();" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Regresar
                    </a>
                    <button type="reset" class="btn btn-warning">Limpiar</button>
                    <button type="submit" class="btn btn-reclutador">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
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
<?php include "includes/footer.php"; ?>
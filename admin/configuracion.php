<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$tablasOk = admin_required_tables_ok($conn, ['configuracion', 'usuarios']);
$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    $form = $_POST['form'] ?? '';

    try {
        if ($form === 'general') {
            $nombreSistema = trim($_POST['nombre_sistema'] ?? '');
            $correoSoporte = trim($_POST['correo_soporte'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            if ($nombreSistema === '' || $correoSoporte === '' || $telefono === '') {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('Completa todos los datos generales.'));
            }

            if (!filter_var($correoSoporte, FILTER_VALIDATE_EMAIL)) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('El correo de soporte no es válido.'));
            }

            set_config_value($conn, 'nombre_sistema', $nombreSistema);
            set_config_value($conn, 'correo_soporte', $correoSoporte);
            set_config_value($conn, 'telefono', $telefono);
            redirect_to('configuracion.php?type=success&msg=' . urlencode('Información del sistema actualizada.'));
        }

        if ($form === 'seguridad') {
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmPassword'] ?? '');
            $mantenerSesion = isset($_POST['mantener_sesion']) ? '1' : '0';

            set_config_value($conn, 'mantener_sesion', $mantenerSesion);

            if ($password !== '' || $confirmPassword !== '') {
                if (strlen($password) < 6) {
                    redirect_to('configuracion.php?type=danger&msg=' . urlencode('La contraseña debe tener al menos 6 caracteres.'));
                }

                if ($password !== $confirmPassword) {
                    redirect_to('configuracion.php?type=danger&msg=' . urlencode('Las contraseñas no coinciden.'));
                }

                $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

                if ($usuarioId <= 0) {
                    $result = $conn->query('SELECT id FROM usuarios WHERE rol_id IN (1, 2) ORDER BY id ASC LIMIT 1');
                    $row = $result ? $result->fetch_assoc() : null;
                    $usuarioId = $row ? (int) $row['id'] : 0;
                }

                if ($usuarioId <= 0) {
                    redirect_to('configuracion.php?type=danger&msg=' . urlencode('No se encontró un administrador para actualizar contraseña.'));
                }

                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                $stmt->bind_param('si', $hash, $usuarioId);
                $stmt->execute();
                $stmt->close();
            }

            redirect_to('configuracion.php?type=success&msg=' . urlencode('Configuración de seguridad actualizada.'));
        }

        if ($form === 'apariencia') {
            $color = trim($_POST['color_principal'] ?? 'Azul');
            $tema = trim($_POST['tema'] ?? 'Claro');
            $idioma = trim($_POST['idioma'] ?? 'Español');

            if (!in_array($color, ['Azul', 'Morado', 'Verde', 'Rojo'], true)) {
                $color = 'Azul';
            }
            if (!in_array($tema, ['Claro', 'Oscuro'], true)) {
                $tema = 'Claro';
            }
            if (!in_array($idioma, ['Español', 'Inglés'], true)) {
                $idioma = 'Español';
            }

            set_config_value($conn, 'color_principal', $color);
            set_config_value($conn, 'tema', $tema);
            set_config_value($conn, 'idioma', $idioma);
            redirect_to('configuracion.php?type=success&msg=' . urlencode('Preferencias visuales actualizadas.'));
        }
    } catch (Throwable $e) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('Error al guardar configuración: ' . $e->getMessage()));
    }
}

$cfg = [
    'nombre_sistema' => get_config_value($conn, 'nombre_sistema', 'Portal de Empleos'),
    'correo_soporte' => get_config_value($conn, 'correo_soporte', 'soporte@portal.com'),
    'telefono' => get_config_value($conn, 'telefono', '9811234567'),
    'mantener_sesion' => get_config_value($conn, 'mantener_sesion', '1'),
    'color_principal' => get_config_value($conn, 'color_principal', 'Azul'),
    'tema' => get_config_value($conn, 'tema', 'Claro'),
    'idioma' => get_config_value($conn, 'idioma', 'Español')
];

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Configuración</h2>
                <p class="text-muted">Administra las configuraciones generales del sistema.</p>
            </div>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Faltan las tablas <strong>configuracion</strong> o <strong>usuarios</strong>. Importa <strong>database_chris.sql</strong>.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Información del sistema</h5>

                    <form method="POST">
                        <input type="hidden" name="form" value="general">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del sistema</label>
                            <input type="text" name="nombre_sistema" class="form-control" value="<?= e($cfg['nombre_sistema']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo de soporte</label>
                            <input type="email" name="correo_soporte" class="form-control" value="<?= e($cfg['correo_soporte']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= e($cfg['telefono']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" <?= !$tablasOk ? 'disabled' : '' ?>><i class="bi bi-save-fill me-2"></i>Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Seguridad</h5>

                    <form method="POST" autocomplete="off">
                        <input type="hidden" name="form" value="seguridad">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña">
                            <small class="text-muted">Déjala vacía si no quieres cambiarla.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmar contraseña</label>
                            <input type="password" name="confirmPassword" class="form-control" placeholder="Confirmar contraseña">
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="mantener_sesion" id="mantenerSesion" <?= $cfg['mantener_sesion'] === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="mantenerSesion">Mantener sesión iniciada</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100" <?= !$tablasOk ? 'disabled' : '' ?>><i class="bi bi-shield-lock-fill me-2"></i>Actualizar Seguridad</button>
                    </form>
                </div>
            </div>

            <div class="col-md-12">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Preferencias visuales</h5>

                    <form method="POST">
                        <input type="hidden" name="form" value="apariencia">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Color principal</label>
                                <select name="color_principal" class="form-select">
                                    <?php foreach (['Azul', 'Morado', 'Verde', 'Rojo'] as $color): ?>
                                        <option value="<?= e($color) ?>" <?= $cfg['color_principal'] === $color ? 'selected' : '' ?>><?= e($color) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Tema</label>
                                <select name="tema" class="form-select">
                                    <?php foreach (['Claro', 'Oscuro'] as $tema): ?>
                                        <option value="<?= e($tema) ?>" <?= $cfg['tema'] === $tema ? 'selected' : '' ?>><?= e($tema) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Idioma</label>
                                <select name="idioma" class="form-select">
                                    <?php foreach (['Español', 'Inglés'] as $idioma): ?>
                                        <option value="<?= e($idioma) ?>" <?= $cfg['idioma'] === $idioma ? 'selected' : '' ?>><?= e($idioma) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" <?= !$tablasOk ? 'disabled' : '' ?>><i class="bi bi-palette-fill me-2"></i>Guardar Preferencias</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

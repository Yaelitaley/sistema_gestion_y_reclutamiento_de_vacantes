<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = '';
$tipoMensaje = '';
$valores = [
    'nombre' => '',
    'correo' => '',
    'empresa' => 'Portal de Empleos',
    'estado' => 'Activo'
];

$tablasOk = admin_required_tables_ok($conn, ['usuarios', 'administradores']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    $valores['nombre'] = trim($_POST['nombre'] ?? '');
    $valores['correo'] = trim($_POST['correo'] ?? '');
    $valores['empresa'] = trim($_POST['empresa'] ?? '');
    $valores['estado'] = trim($_POST['estado'] ?? 'Activo');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');
    $claveSeguridad = trim($_POST['clave_seguridad'] ?? '');
    $confirmClaveSeguridad = trim($_POST['confirm_clave_seguridad'] ?? '');

    if ($valores['nombre'] === '' || $valores['correo'] === '' || $valores['empresa'] === '' || $password === '' || $confirmPassword === '' || $claveSeguridad === '' || $confirmClaveSeguridad === '') {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipoMensaje = 'danger';
    } elseif (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u', $valores['nombre'])) {
        $mensaje = 'El nombre solo debe contener letras y espacios.';
        $tipoMensaje = 'danger';
    } elseif (!filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo electrónico no es válido.';
        $tipoMensaje = 'danger';
    } elseif (strlen($password) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
        $tipoMensaje = 'danger';
    } elseif ($password !== $confirmPassword) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipoMensaje = 'danger';
    } elseif (strlen($claveSeguridad) < 4) {
        $mensaje = 'La clave de seguridad debe tener al menos 4 caracteres.';
        $tipoMensaje = 'danger';
    } elseif ($claveSeguridad !== $confirmClaveSeguridad) {
        $mensaje = 'La clave de seguridad no coincide con su confirmación.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($valores['estado'], ['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } else {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $valores['correo']);
        $stmt->execute();
        $stmt->store_result();
        $correoExiste = $stmt->num_rows > 0;
        $stmt->close();

        if ($correoExiste) {
            $mensaje = 'El correo ya está registrado.';
            $tipoMensaje = 'danger';
        } else {
            $conn->begin_transaction();

            try {
                $rolId = 2;
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $hashClaveSeguridad = password_hash($claveSeguridad, PASSWORD_BCRYPT);

                $stmt = $conn->prepare('INSERT INTO usuarios (rol_id, email, password, correo, clave_seguridad) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('issss', $rolId, $valores['correo'], $hash, $valores['correo'], $hashClaveSeguridad);
                $stmt->execute();
                $usuarioId = $conn->insert_id;
                $stmt->close();

                $stmt = $conn->prepare('INSERT INTO administradores (usuario_id, nombre_completo, correo, empresa, estado) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('issss', $usuarioId, $valores['nombre'], $valores['correo'], $valores['empresa'], $valores['estado']);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                $mensaje = 'Administrador registrado correctamente.';
                $tipoMensaje = 'success';
                $valores = ['nombre' => '', 'correo' => '', 'empresa' => 'Portal de Empleos', 'estado' => 'Activo'];
            } catch (Throwable $e) {
                $conn->rollback();
                $mensaje = 'Error al registrar administrador: ' . $e->getMessage();
                $tipoMensaje = 'danger';
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="register-container">
    <div class="top-bar">
        <i class="bi bi-gear-fill"></i>
        <span>Registrar Administrador</span>
    </div>

    <div class="container-fluid py-5 d-flex justify-content-center">
        <div class="register-box">
            <div class="text-center mb-4">
                <img src="../assets/img/imagenadministrador.png" class="img-fluid register-image" alt="Administrador">
            </div>

            <div class="text-center mb-4">
                <p class="fw-bold">Completa la información para registrar al nuevo administrador en el sistema</p>
            </div>

            <?php if (!$tablasOk): ?>
                <div class="alert alert-warning">
                    Faltan las tablas <strong>usuarios</strong> o <strong>administradores</strong>. Importa primero el archivo <strong>database_chris.sql</strong>.
                </div>
            <?php endif; ?>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <form id="adminCreateForm" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre Completo" value="<?= e($valores['nombre']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo Electrónico" value="<?= e($valores['correo']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Empresa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <input type="text" name="empresa" class="form-control" placeholder="Empresa" value="<?= e($valores['empresa']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <select name="estado" class="form-select" required>
                        <?php foreach (['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'] as $estado): ?>
                            <option value="<?= e($estado) ?>" <?= $valores['estado'] === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contraseña Temporal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="********" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Clave de Seguridad Personal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="clave_seguridad" id="clave_seguridad" class="form-control" placeholder="Clave de seguridad" required minlength="4">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Confirmar Clave de Seguridad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="confirm_clave_seguridad" id="confirm_clave_seguridad" class="form-control" placeholder="Confirmar clave de seguridad" required minlength="4">
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <small class="text-muted">
                        La contraseña es temporal, el administrador podrá cambiarla después.
                        La <strong>clave de seguridad</strong> es personal e intransferible: solo el propio administrador
                        debe conocerla, ya que se usará para recuperar su contraseña si la olvida.
                    </small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100" <?= !$tablasOk ? 'disabled' : '' ?>>Registrar Administrador</button>
                </div>

                <div class="text-center mt-3">
                    <a href="index_administrador.php" class="cancel-link">Regresar</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
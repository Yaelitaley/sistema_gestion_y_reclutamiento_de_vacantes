<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

// Verificamos exactamente qué tablas hacen falta (para poder avisar bien si algo no cuadra)
$faltantes = [];
foreach (['configuracion', 'usuarios'] as $tabla) {
    if (!table_exists($conn, $tabla)) {
        $faltantes[] = $tabla;
    }
}
$tablasOk = empty($faltantes);

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST['form'] ?? '';

    try {
        // ---- Formulario: datos generales (nombre del admin logueado + correo de contacto del sitio) ----
        if ($form === 'general') {
            $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
            $correoContacto = trim($_POST['correo_contacto'] ?? '');

            if ($nombreCompleto === '' || $correoContacto === '') {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('Completa tu nombre y el correo de contacto.'));
            }

            if (!filter_var($correoContacto, FILTER_VALIDATE_EMAIL)) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('El correo de contacto no es válido.'));
            }

            $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($usuarioId <= 0) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('No se encontró la sesión del administrador.'));
            }

            // Actualiza el nombre del admin que tiene la sesión activa
            $stmt = $conn->prepare('UPDATE usuarios SET nombre_completo = ? WHERE id = ? AND rol_id IN (1, 2)');
            $stmt->bind_param('si', $nombreCompleto, $usuarioId);
            $stmt->execute();
            $stmt->close();

            // Refleja el cambio inmediatamente en la sesión (topbar, saludo, etc.)
            $_SESSION['nombre'] = $nombreCompleto;

            set_config_value($conn, 'correo_contacto', $correoContacto);
            redirect_to('configuracion.php?type=success&msg=' . urlencode('Datos actualizados correctamente.'));
        }

        // ---- Formulario: reclutamiento (clave: max_postulaciones_dia, mantenimiento) ----
        if ($form === 'reclutamiento') {
            $maxPostulaciones = (int) ($_POST['max_postulaciones_dia'] ?? 0);
            $mantenimiento    = isset($_POST['mantenimiento']) ? '1' : '0';

            if ($maxPostulaciones < 1) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('El máximo de postulaciones por día debe ser al menos 1.'));
            }

            set_config_value($conn, 'max_postulaciones_dia', (string) $maxPostulaciones);
            set_config_value($conn, 'mantenimiento', $mantenimiento);
            redirect_to('configuracion.php?type=success&msg=' . urlencode('Configuración de reclutamiento actualizada.'));
        }

        // ---- Formulario: seguridad (tabla usuarios, rol_id 1 o 2) ----
        if ($form === 'seguridad') {
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmPassword'] ?? '');

            if ($password === '' && $confirmPassword === '') {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('Escribe una contraseña para actualizarla.'));
            }

            if (strlen($password) < 6) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('La contraseña debe tener al menos 6 caracteres.'));
            }

            if ($password !== $confirmPassword) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('Las contraseñas no coinciden.'));
            }

            $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($usuarioId <= 0) {
                redirect_to('configuracion.php?type=danger&msg=' . urlencode('No se encontró la sesión del administrador.'));
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare('UPDATE usuarios SET password = ? WHERE id = ? AND rol_id IN (1, 2)');
            $stmt->bind_param('si', $hash, $usuarioId);
            $stmt->execute();
            $stmt->close();

            redirect_to('configuracion.php?type=success&msg=' . urlencode('Contraseña actualizada correctamente.'));
        }
    } catch (Throwable $e) {
        redirect_to('configuracion.php?type=danger&msg=' . urlencode('Error al guardar configuración: ' . $e->getMessage()));
    }
}

// Nombre y correo actuales del admin logueado (siempre frescos desde la BD, no solo de la sesión)
$nombreActual = '';
$correoActual = $_SESSION['correo'] ?? '';
$usuarioIdActual = (int) ($_SESSION['usuario_id'] ?? 0);

if ($usuarioIdActual > 0 && $tablasOk) {
    $stmt = $conn->prepare('SELECT nombre_completo, correo FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $usuarioIdActual);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $nombreActual = $row['nombre_completo'] ?? '';
        $correoActual = $row['correo'] ?? $correoActual;
    }
}

// Valores actuales, tal cual están en tu tabla `configuracion`
$cfg = [
    'correo_contacto'       => get_config_value($conn, 'correo_contacto', 'contacto@portalempleos.com'),
    'max_postulaciones_dia' => get_config_value($conn, 'max_postulaciones_dia', '5'),
    'mantenimiento'         => get_config_value($conn, 'mantenimiento', '0')
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
            <div class="alert alert-warning">
                Faltan estas tablas en tu base de datos: <strong><?= e(implode(', ', $faltantes)) ?></strong>.
                Importa tu archivo <strong>reclutamiento_vacantes.sql</strong> y revisa <code>config/connection.php</code>.
            </div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Nombre: tabla usuarios.nombre_completo (admin logueado) | Correo: clave correo_contacto -->
            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Información general</h5>

                    <form method="POST">
                        <input type="hidden" name="form" value="general">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de usuario</label>
                            <input type="text" name="nombre_completo" class="form-control" value="<?= e($nombreActual) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo de contacto</label>
                            <input type="email" name="correo_contacto" class="form-control" value="<?= e($cfg['correo_contacto']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save-fill me-2"></i>Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <!-- Corresponde a: clave = max_postulaciones_dia, mantenimiento -->
            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Reclutamiento y mantenimiento</h5>

                    <form method="POST">
                        <input type="hidden" name="form" value="reclutamiento">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Máximo de postulaciones por día</label>
                            <input type="number" min="1" name="max_postulaciones_dia" class="form-control" value="<?= e($cfg['max_postulaciones_dia']) ?>" required>
                            <small class="text-muted">Límite de vacantes a las que un candidato puede postularse por día.</small>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="mantenimiento" id="mantenimiento" <?= $cfg['mantenimiento'] === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="mantenimiento">Activar modo mantenimiento</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-gear-fill me-2"></i>Guardar Configuración</button>
                    </form>
                </div>
            </div>

            <!-- Corresponde a: tabla usuarios, admin logueado -->
            <div class="col-md-12">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Seguridad de mi cuenta</h5>
                    <p class="text-muted mb-3">Sesión actual: <strong><?= e($nombreActual ?: 'Administrador') ?></strong> (<?= e($correoActual) ?>)</p>

                    <form method="POST" autocomplete="off" class="row g-3">
                        <input type="hidden" name="form" value="seguridad">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña" minlength="6">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirmar contraseña</label>
                            <input type="password" name="confirmPassword" class="form-control" placeholder="Confirmar contraseña" minlength="6">
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-dark"><i class="bi bi-shield-lock-fill me-2"></i>Actualizar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
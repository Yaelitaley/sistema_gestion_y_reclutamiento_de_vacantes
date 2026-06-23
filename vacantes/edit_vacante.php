<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = '';
$tipoMensaje = '';
$tablasOk = admin_required_tables_ok($conn, ['vacantes']);
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$categoriasBase = ['Tecnología', 'Diseño', 'Marketing', 'Administración', 'Ventas', 'Recursos Humanos', 'Soporte', 'Otro'];

if (!$tablasOk) {
    $vacante = null;
} elseif ($id <= 0) {
    redirect_to('vacantes.php?type=danger&msg=' . urlencode('Vacante no válida.'));
} else {
    $stmt = $conn->prepare('SELECT * FROM vacantes WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $vacante = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$vacante) {
        redirect_to('vacantes.php?type=danger&msg=' . urlencode('Vacante no encontrada.'));
    }

    if (!in_array($vacante['categoria'], $categoriasBase, true)) {
        $categoriasBase[] = $vacante['categoria'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk && $vacante) {
    $titulo = trim($_POST['titulo'] ?? '');
    $empresa = trim($_POST['empresa'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $salarioInput = trim($_POST['salario'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Activo');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($titulo === '' || $empresa === '' || $ubicacion === '' || $categoria === '' || $estado === '' || $descripcion === '') {
        $mensaje = 'Todos los campos son obligatorios, excepto salario.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($estado, ['Activo', 'Inactivo'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } elseif ($salarioInput !== '' && !is_numeric($salarioInput)) {
        $mensaje = 'El salario debe ser numérico.';
        $tipoMensaje = 'danger';
    } else {
        $salario = $salarioInput === '' ? null : (float) $salarioInput;

        try {
            $stmt = $conn->prepare('UPDATE vacantes SET titulo = ?, empresa = ?, ubicacion = ?, salario = ?, categoria = ?, estado = ?, descripcion = ? WHERE id = ?');
            $stmt->bind_param('sssdsssi', $titulo, $empresa, $ubicacion, $salario, $categoria, $estado, $descripcion, $id);
            $stmt->execute();
            $stmt->close();

            redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante actualizada correctamente.'));
        } catch (Throwable $e) {
            $mensaje = 'Error al actualizar vacante: ' . $e->getMessage();
            $tipoMensaje = 'danger';
        }
    }

    $vacante = [
        'id' => $id,
        'titulo' => $titulo,
        'empresa' => $empresa,
        'ubicacion' => $ubicacion,
        'salario' => $salarioInput,
        'categoria' => $categoria,
        'estado' => $estado,
        'descripcion' => $descripcion
    ];
}

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Editar Vacante</h2>
                <p class="text-muted">Modifica la información de la vacante.</p>
            </div>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Falta la tabla <strong>vacantes</strong>. Importa <strong>database_chris.sql</strong>.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($vacante): ?>
            <div class="table-box">
                <form id="vacanteEditForm" method="POST">
                    <input type="hidden" name="id" value="<?= (int) $vacante['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Título de la vacante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                            <input type="text" name="titulo" class="form-control" value="<?= e($vacante['titulo']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Empresa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" name="empresa" class="form-control" value="<?= e($vacante['empresa']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ubicación</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" name="ubicacion" class="form-control" value="<?= e($vacante['ubicacion']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Salario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                            <input type="number" step="0.01" min="0" name="salario" class="form-control" value="<?= e($vacante['salario']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="categoria" class="form-select" required>
                            <?php foreach ($categoriasBase as $categoria): ?>
                                <option value="<?= e($categoria) ?>" <?= $vacante['categoria'] === $categoria ? 'selected' : '' ?>><?= e($categoria) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-select" required>
                            <?php foreach (['Activo', 'Inactivo'] as $estado): ?>
                                <option value="<?= e($estado) ?>" <?= $vacante['estado'] === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="5" required><?= e($vacante['descripcion']) ?></textarea>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-2"></i>Guardar cambios</button>
                        <a href="vacantes.php" class="cancel-link">Regresar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

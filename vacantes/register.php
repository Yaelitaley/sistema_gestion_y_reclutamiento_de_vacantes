<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = '';
$tipoMensaje = '';
$tablasOk = admin_required_tables_ok($conn, ['vacantes']);
$valores = [
    'titulo' => '',
    'empresa' => '',
    'ubicacion' => '',
    'salario' => '',
    'categoria' => 'Tecnología',
    'estado' => 'Activo',
    'descripcion' => ''
];

$categoriasBase = ['Tecnología', 'Diseño', 'Marketing', 'Administración', 'Ventas', 'Recursos Humanos', 'Soporte', 'Otro'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    foreach ($valores as $key => $value) {
        $valores[$key] = trim($_POST[$key] ?? '');
    }

    if ($valores['titulo'] === '' || $valores['empresa'] === '' || $valores['ubicacion'] === '' || $valores['categoria'] === '' || $valores['estado'] === '' || $valores['descripcion'] === '') {
        $mensaje = 'Todos los campos son obligatorios, excepto salario.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($valores['estado'], ['Activo', 'Inactivo'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } elseif ($valores['salario'] !== '' && !is_numeric($valores['salario'])) {
        $mensaje = 'El salario debe ser numérico.';
        $tipoMensaje = 'danger';
    } else {
        $salario = $valores['salario'] === '' ? null : (float) $valores['salario'];

        try {
            $stmt = $conn->prepare('INSERT INTO vacantes (titulo, empresa, ubicacion, salario, categoria, estado, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssdsss', $valores['titulo'], $valores['empresa'], $valores['ubicacion'], $salario, $valores['categoria'], $valores['estado'], $valores['descripcion']);
            $stmt->execute();
            $stmt->close();

            redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante registrada correctamente.'));
        } catch (Throwable $e) {
            $mensaje = 'Error al registrar vacante: ' . $e->getMessage();
            $tipoMensaje = 'danger';
        }
    }
}

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Nueva Vacante</h2>
                <p class="text-muted">Registra una nueva vacante para mostrarla en el sistema.</p>
            </div>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Falta la tabla <strong>vacantes</strong>. Importa <strong>database_chris.sql</strong>.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="table-box">
            <form id="vacanteCreateForm" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Título de la vacante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                            <input type="text" name="titulo" class="form-control" placeholder="Ej. Desarrollador Backend" value="<?= e($valores['titulo']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Empresa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" name="empresa" class="form-control" placeholder="Nombre de empresa" value="<?= e($valores['empresa']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ubicación</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" name="ubicacion" class="form-control" placeholder="Ej. Mérida, Yucatán" value="<?= e($valores['ubicacion']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Salario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                            <input type="number" step="0.01" min="0" name="salario" class="form-control" placeholder="Ej. 15000" value="<?= e($valores['salario']) ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="categoria" class="form-select" required>
                            <?php foreach ($categoriasBase as $categoria): ?>
                                <option value="<?= e($categoria) ?>" <?= $valores['categoria'] === $categoria ? 'selected' : '' ?>><?= e($categoria) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-select" required>
                            <?php foreach (['Activo', 'Inactivo'] as $estado): ?>
                                <option value="<?= e($estado) ?>" <?= $valores['estado'] === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="5" placeholder="Describe responsabilidades, requisitos y beneficios..." required><?= e($valores['descripcion']) ?></textarea>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary" <?= !$tablasOk ? 'disabled' : '' ?>><i class="bi bi-save-fill me-2"></i>Guardar Vacante</button>
                    <a href="vacantes.php" class="cancel-link">Regresar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

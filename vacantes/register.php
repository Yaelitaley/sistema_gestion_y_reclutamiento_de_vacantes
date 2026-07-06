<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = '';
$tipoMensaje = '';
$tablasOk = admin_required_tables_ok($conn, ['vacantes', 'reclutadores']);
$valores = [
    'trabajo' => '',
    'reclutador_id' => '',
    'ubicacion' => '',
    'salario' => '',
    'categoria' => 'Tecnología',
    'nivel_experiencia' => 'Sin experiencia',
    'activa' => '1',
    'requisitos' => '',
    'descripcion' => ''
];

$categoriasBase = ['Tecnología', 'Diseño', 'Marketing', 'Administración', 'Ventas', 'Recursos Humanos', 'Soporte', 'Otro'];
$nivelesBase = ['Sin experiencia', 'Junior', 'Intermedio', 'Senior'];

$reclutadores = [];
if ($tablasOk) {
    $resReclutadores = $conn->query(
        "SELECT r.id, r.nombre_completo, COALESCE(e.nombre, 'Sin empresa') AS empresa
         FROM reclutadores r
         LEFT JOIN empresas e ON r.empresa_id = e.id
         ORDER BY r.nombre_completo ASC"
    );
    if ($resReclutadores) {
        while ($fila = $resReclutadores->fetch_assoc()) {
            $reclutadores[] = $fila;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    foreach ($valores as $key => $value) {
        $valores[$key] = trim($_POST[$key] ?? '');
    }

    $reclutadorId = (int) $valores['reclutador_id'];

    if ($valores['trabajo'] === '' || $reclutadorId <= 0 || $valores['ubicacion'] === '' || $valores['categoria'] === ''
        || $valores['nivel_experiencia'] === '' || $valores['requisitos'] === '' || $valores['descripcion'] === '') {
        $mensaje = 'Todos los campos son obligatorios, excepto salario.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($valores['activa'], ['0', '1'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } elseif ($valores['salario'] !== '' && !is_numeric($valores['salario'])) {
        $mensaje = 'El salario debe ser numérico.';
        $tipoMensaje = 'danger';
    } else {
        $salario = $valores['salario'] === '' ? null : (float) $valores['salario'];
        $activa = (int) $valores['activa'];

        try {
            $stmt = $conn->prepare(
                'INSERT INTO vacantes (reclutador_id, trabajo, descripcion, categoria, requisitos, salario, ubicacion, nivel_experiencia, activa)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'isssdsssi',
                $reclutadorId,
                $valores['trabajo'],
                $valores['descripcion'],
                $valores['categoria'],
                $valores['requisitos'],
                $salario,
                $valores['ubicacion'],
                $valores['nivel_experiencia'],
                $activa
            );
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
            <div class="alert alert-warning">Faltan las tablas <strong>vacantes</strong> o <strong>reclutadores</strong>. Importa tu script SQL.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($tablasOk && empty($reclutadores)): ?>
            <div class="alert alert-warning">No hay reclutadores registrados todavía. Registra un reclutador antes de crear una vacante.</div>
        <?php endif; ?>

        <div class="table-box">
            <form id="vacanteCreateForm" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Título de la vacante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                            <input type="text" name="trabajo" class="form-control" placeholder="Ej. Desarrollador Backend" value="<?= e($valores['trabajo']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Reclutador / Empresa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select name="reclutador_id" class="form-select" required <?= empty($reclutadores) ? 'disabled' : '' ?>>
                                <option value="">Selecciona un reclutador</option>
                                <?php foreach ($reclutadores as $reclutador): ?>
                                    <option value="<?= (int) $reclutador['id'] ?>" <?= (string) $valores['reclutador_id'] === (string) $reclutador['id'] ? 'selected' : '' ?>>
                                        <?= e($reclutador['nombre_completo']) ?> — <?= e($reclutador['empresa']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="categoria" class="form-select" required>
                            <?php foreach ($categoriasBase as $categoria): ?>
                                <option value="<?= e($categoria) ?>" <?= $valores['categoria'] === $categoria ? 'selected' : '' ?>><?= e($categoria) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Nivel de experiencia</label>
                        <select name="nivel_experiencia" class="form-select" required>
                            <?php foreach ($nivelesBase as $nivel): ?>
                                <option value="<?= e($nivel) ?>" <?= $valores['nivel_experiencia'] === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="activa" class="form-select" required>
                            <option value="1" <?= $valores['activa'] === '1' ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= $valores['activa'] === '0' ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Requisitos</label>
                    <textarea name="requisitos" class="form-control" rows="3" placeholder="Ej. Conocimiento en PHP, MySQL, Git. Mínimo 1 año de experiencia." required><?= e($valores['requisitos']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="5" placeholder="Describe responsabilidades, requisitos y beneficios..." required><?= e($valores['descripcion']) ?></textarea>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary" <?= (!$tablasOk || empty($reclutadores)) ? 'disabled' : '' ?>><i class="bi bi-save-fill me-2"></i>Guardar Vacante</button>
                    <a href="vacantes.php" class="cancel-link">Regresar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
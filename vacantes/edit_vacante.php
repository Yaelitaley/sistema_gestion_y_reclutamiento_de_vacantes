<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = '';
$tipoMensaje = '';
$tablasOk = admin_required_tables_ok($conn, ['vacantes', 'reclutadores']);
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
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

    if (!in_array($vacante['nivel_experiencia'], $nivelesBase, true)) {
        $nivelesBase[] = $vacante['nivel_experiencia'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk && $vacante) {
    $trabajo = trim($_POST['trabajo'] ?? '');
    $reclutadorId = (int) ($_POST['reclutador_id'] ?? 0);
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $salarioInput = trim($_POST['salario'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $nivelExperiencia = trim($_POST['nivel_experiencia'] ?? '');
    $activaInput = trim($_POST['activa'] ?? '1');
    $requisitos = trim($_POST['requisitos'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($trabajo === '' || $reclutadorId <= 0 || $ubicacion === '' || $categoria === '' || $nivelExperiencia === '' || $requisitos === '' || $descripcion === '') {
        $mensaje = 'Todos los campos son obligatorios, excepto salario.';
        $tipoMensaje = 'danger';
    } elseif (!in_array($activaInput, ['0', '1'], true)) {
        $mensaje = 'El estado seleccionado no es válido.';
        $tipoMensaje = 'danger';
    } elseif ($salarioInput !== '' && !is_numeric($salarioInput)) {
        $mensaje = 'El salario debe ser numérico.';
        $tipoMensaje = 'danger';
    } else {
        $salario = $salarioInput === '' ? null : (float) $salarioInput;
        $activa = (int) $activaInput;

        try {
            $stmt = $conn->prepare(
                'UPDATE vacantes SET reclutador_id = ?, trabajo = ?, descripcion = ?, categoria = ?, requisitos = ?, salario = ?, ubicacion = ?, nivel_experiencia = ?, activa = ? WHERE id = ?'
            );
            $stmt->bind_param(
                'isssdsssii',
                $reclutadorId,
                $trabajo,
                $descripcion,
                $categoria,
                $requisitos,
                $salario,
                $ubicacion,
                $nivelExperiencia,
                $activa,
                $id
            );
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
        'trabajo' => $trabajo,
        'reclutador_id' => $reclutadorId,
        'ubicacion' => $ubicacion,
        'salario' => $salarioInput,
        'categoria' => $categoria,
        'nivel_experiencia' => $nivelExperiencia,
        'activa' => $activaInput,
        'requisitos' => $requisitos,
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
            <div class="alert alert-warning">Faltan las tablas <strong>vacantes</strong> o <strong>reclutadores</strong>. Importa tu script SQL.</div>
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
                            <input type="text" name="trabajo" class="form-control" value="<?= e($vacante['trabajo']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reclutador / Empresa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select name="reclutador_id" class="form-select" required>
                                <option value="">Selecciona un reclutador</option>
                                <?php foreach ($reclutadores as $reclutador): ?>
                                    <option value="<?= (int) $reclutador['id'] ?>" <?= (string) $vacante['reclutador_id'] === (string) $reclutador['id'] ? 'selected' : '' ?>>
                                        <?= e($reclutador['nombre_completo']) ?> — <?= e($reclutador['empresa']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                        <label class="form-label fw-bold">Nivel de experiencia</label>
                        <select name="nivel_experiencia" class="form-select" required>
                            <?php foreach ($nivelesBase as $nivel): ?>
                                <option value="<?= e($nivel) ?>" <?= $vacante['nivel_experiencia'] === $nivel ? 'selected' : '' ?>><?= e($nivel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="activa" class="form-select" required>
                            <option value="1" <?= (string) $vacante['activa'] === '1' ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= (string) $vacante['activa'] === '0' ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos</label>
                        <textarea name="requisitos" class="form-control" rows="3" required><?= e($vacante['requisitos']) ?></textarea>
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
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';
$tablasOk = admin_required_tables_ok($conn, ['vacantes']);
$tienePostulaciones = table_exists($conn, 'postulaciones');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'eliminar') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $conn->begin_transaction();
            try {
                if ($tienePostulaciones) {
                    $stmt = $conn->prepare('DELETE FROM postulaciones WHERE vacante_id = ?');
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();
                }

                $stmt = $conn->prepare('DELETE FROM vacantes WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $eliminadas = $stmt->affected_rows;
                $stmt->close();

                $conn->commit();

                if ($eliminadas > 0) {
                    redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante eliminada correctamente.'));
                }

                redirect_to('vacantes.php?type=danger&msg=' . urlencode('Vacante no encontrada.'));
            } catch (Throwable $e) {
                $conn->rollback();
                redirect_to('vacantes.php?type=danger&msg=' . urlencode('No se pudo eliminar: ' . $e->getMessage()));
            }
        }
    }
}

$buscar = trim($_GET['buscar'] ?? '');
$estadoFiltro = trim($_GET['estado'] ?? '');
$categoriaFiltro = trim($_GET['categoria'] ?? '');
$vacantes = [];
$categorias = [];
$totales = ['activas' => 0, 'inactivas' => 0, 'postulaciones' => 0, 'categorias' => 0];

if ($tablasOk) {
    $totales['activas'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM vacantes WHERE activa = 1")->fetch_assoc()['total'] ?? 0);
    $totales['inactivas'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM vacantes WHERE activa = 0")->fetch_assoc()['total'] ?? 0);
    $totales['categorias'] = (int) ($conn->query("SELECT COUNT(DISTINCT categoria) AS total FROM vacantes WHERE categoria <> ''")->fetch_assoc()['total'] ?? 0);

    if ($tienePostulaciones) {
        $totales['postulaciones'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM postulaciones')->fetch_assoc()['total'] ?? 0);
    }

    $catResult = $conn->query("SELECT DISTINCT categoria FROM vacantes WHERE categoria <> '' ORDER BY categoria ASC");
    if ($catResult) {
        while ($row = $catResult->fetch_assoc()) {
            $categorias[] = $row['categoria'];
        }
    }

    $where = [];
    $types = '';
    $params = [];

    if ($buscar !== '') {
        $where[] = '(v.trabajo LIKE ? OR emp.nombre LIKE ? OR v.ubicacion LIKE ? OR v.descripcion LIKE ?)';
        $like = '%' . $buscar . '%';
        array_push($params, $like, $like, $like, $like);
        $types .= 'ssss';
    }

    if ($estadoFiltro !== '' && in_array($estadoFiltro, ['Activo', 'Inactivo'], true)) {
        $where[] = 'v.activa = ?';
        $params[] = ($estadoFiltro === 'Activo') ? 1 : 0;
        $types .= 'i';
    }

    if ($categoriaFiltro !== '') {
        $where[] = 'v.categoria = ?';
        $params[] = $categoriaFiltro;
        $types .= 's';
    }

    $postulacionesSql = $tienePostulaciones ? '(SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id = v.id)' : '0';
    
    // Vinculamos con reclutadores y empresas para obtener el nombre real de la organización
    $sql = "SELECT v.id, v.trabajo AS titulo, COALESCE(emp.nombre, 'Sin Empresa') AS empresa, v.ubicacion, v.salario, v.categoria, v.activa, v.descripcion, {$postulacionesSql} AS postulaciones
            FROM vacantes v
            LEFT JOIN reclutadores r ON v.reclutador_id = r.id
            LEFT JOIN empresas emp ON r.empresa_id = emp.id";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY v.id DESC';
    $stmt = $conn->prepare($sql);

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $vacantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Gestión de Vacantes</h2>
                <p class="text-muted">Administra, filtra, edita y elimina vacantes del sistema.</p>
            </div>

            <a href="../vacantes/register.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nueva Vacante
            </a>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Falta la tabla <strong>vacantes</strong>. Importa tu script SQL.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold"><?= $totales['activas'] ?></h3><p class="text-muted mb-0">Vacantes Activas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-secondary-subtle"><i class="bi bi-file-earmark-fill text-secondary"></i></div><div><h3 class="fw-bold"><?= $totales['inactivas'] ?></h3><p class="text-muted mb-0">Vacantes Inactivas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-people-fill text-success"></i></div><div><h3 class="fw-bold"><?= $totales['postulaciones'] ?></h3><p class="text-muted mb-0">Total Postulaciones</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-bar-chart-fill text-warning"></i></div><div><h3 class="fw-bold"><?= $totales['categorias'] ?></h3><p class="text-muted mb-0">Áreas o Categorías</p></div></div>
            </div>
        </div>

        <div class="table-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold">Lista de Vacantes</h5>
            </div>

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar vacante..." value="<?= e($buscar) ?>">
                </div>

                <div class="col-md-3">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <?php foreach (['Activo', 'Inactivo'] as $estado): ?>
                            <option value="<?= e($estado) ?>" <?= $estadoFiltro === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= e($categoria) ?>" <?= $categoriaFiltro === $categoria ? 'selected' : '' ?>><?= e($categoria) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-2"></i>Aplicar filtros</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Vacante</th>
                            <th>Empresa</th>
                            <th>Ubicación</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Postulaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$vacantes): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay vacantes registradas.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($vacantes as $vacante): ?>
                            <tr>
                                <td>
                                    <strong><?= e($vacante['titulo']) ?></strong><br>
                                    <small class="text-muted"><?= e(texto_corto($vacante['descripcion'], 65)) ?></small>
                                </td>
                                <td><?= e($vacante['empresa']) ?></td>
                                <td><?= e($vacante['ubicacion']) ?></td>
                                <td><span class="badge bg-primary"><?= e($vacante['categoria']) ?></span></td>
                                <td>
                                    <?php if (function_exists('badge_estado')): ?>
                                        <?= badge_estado($vacante['activa'] ? 'Activo' : 'Inactivo') ?>
                                    <?php else: ?>
                                        <span class="badge <?= $vacante['activa'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $vacante['activa'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) $vacante['postulaciones'] ?></td>
                                <td>
                                    <a href="edit_vacante.php?id=<?= (int) $vacante['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta vacante?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $vacante['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';
$tablasOk = admin_required_tables_ok($conn, ['usuarios', 'administradores']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablasOk) {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'eliminar') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare('SELECT usuario_id FROM administradores WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($admin) {
                $usuarioId = (int) $admin['usuario_id'];
                $usuarioSesion = (int) ($_SESSION['usuario_id'] ?? 0);

                if ($usuarioSesion > 0 && $usuarioId === $usuarioSesion) {
                    redirect_to('index_administrador.php?type=danger&msg=' . urlencode('No puedes eliminar tu propio usuario.'));
                }

                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare('DELETE FROM administradores WHERE id = ?');
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
                    $stmt->bind_param('i', $usuarioId);
                    $stmt->execute();
                    $stmt->close();

                    $conn->commit();
                    redirect_to('index_administrador.php?type=success&msg=' . urlencode('Administrador eliminado correctamente.'));
                } catch (Throwable $e) {
                    $conn->rollback();
                    redirect_to('index_administrador.php?type=danger&msg=' . urlencode('No se pudo eliminar: ' . $e->getMessage()));
                }
            }
        }

        redirect_to('index_administrador.php?type=danger&msg=' . urlencode('Administrador no encontrado.'));
    }
}

$buscar = trim($_GET['buscar'] ?? '');
$estadoFiltro = trim($_GET['estado'] ?? '');
$administradores = [];
$totales = ['total' => 0, 'activos' => 0, 'pendientes' => 0, 'bloqueados' => 0];

if ($tablasOk) {
    $totales['total'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM administradores')->fetch_assoc()['total'] ?? 0);
    $totales['activos'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM administradores WHERE estado = 'Activo'")->fetch_assoc()['total'] ?? 0);
    $totales['pendientes'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM administradores WHERE estado = 'Pendiente'")->fetch_assoc()['total'] ?? 0);
    $totales['bloqueados'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM administradores WHERE estado = 'Bloqueado'")->fetch_assoc()['total'] ?? 0);

    $where = [];
    $types = '';
    $params = [];

    if ($buscar !== '') {
        $where[] = '(a.nombre_completo LIKE ? OR a.correo LIKE ? OR a.empresa LIKE ?)';
        $like = '%' . $buscar . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= 'sss';
    }

    if ($estadoFiltro !== '' && in_array($estadoFiltro, ['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'], true)) {
        $where[] = 'a.estado = ?';
        $params[] = $estadoFiltro;
        $types .= 's';
    }

    $sql = 'SELECT a.id, a.usuario_id, a.nombre_completo, a.correo, a.empresa, a.estado, u.rol_id
            FROM administradores a
            INNER JOIN usuarios u ON u.id = a.usuario_id';

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY a.id DESC';
    $stmt = $conn->prepare($sql);

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $administradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Administradores</h3>
                <p class="text-muted">Administra los demás administradores registrados en el sistema.</p>
            </div>

            <a href="../admin/register.php" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Administrador
            </a>
        </div>

        <?php if (!$tablasOk): ?>
            <div class="alert alert-warning">Faltan tablas para esta pantalla. Importa tu base de datos en MySQL.</div>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-shield-lock-fill text-primary"></i></div><div><h3 class="fw-bold"><?= $totales['total'] ?></h3><p class="text-muted mb-0">Total admins</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold"><?= $totales['activos'] ?></h3><p class="text-muted mb-0">Activos</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-clock-fill text-warning"></i></div><div><h3 class="fw-bold"><?= $totales['pendientes'] ?></h3><p class="text-muted mb-0">Pendientes</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-lock-fill text-danger"></i></div><div><h3 class="fw-bold"><?= $totales['bloqueados'] ?></h3><p class="text-muted mb-0">Bloqueados</p></div></div>
            </div>
        </div>

        <div class="table-box">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, correo o empresa..." value="<?= e($buscar) ?>">
                </div>
                <div class="col-md-4">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <?php foreach (['Activo', 'Pendiente', 'Bloqueado', 'Inactivo'] as $estado): ?>
                            <option value="<?= e($estado) ?>" <?= $estadoFiltro === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-2"></i>Filtrar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Empresa</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$administradores): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay administradores registrados.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($administradores as $admin): ?>
                            <tr>
                                <td><?= (int) $admin['id'] ?></td>
                                <td><?= e($admin['nombre_completo']) ?></td>
                                <td><?= e($admin['correo']) ?></td>
                                <td><?= e($admin['empresa']) ?></td>
                                <td><?= ((int) $admin['rol_id'] === 1) ? 'Principal' : 'Administrador' ?></td>
                                <td>
                                    <?php if (function_exists('badge_estado')): ?>
                                        <?= badge_estado($admin['estado']) ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($admin['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="../admin/edit_administrador.php?id=<?= (int) $admin['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este administrador?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="javascript:history.back()" class="cancel-link">Regresar</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
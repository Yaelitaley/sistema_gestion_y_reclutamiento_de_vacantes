<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$buscar = trim($_GET['buscar'] ?? '');
$filtroRol = (int) ($_GET['rol'] ?? 0);

$sql = "SELECT u.id, u.nombre_completo, u.email, u.estado, u.created_at, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE 1 = 1";
$params = [];
$types = '';

if ($buscar !== '') {
    $sql .= " AND (u.nombre_completo LIKE ? OR u.email LIKE ?)";
    $like = '%' . $buscar . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
if ($filtroRol > 0) {
    $sql .= " AND u.rol_id = ?";
    $params[] = $filtroRol;
    $types .= 'i';
}
$sql .= " ORDER BY u.created_at DESC";

if ($types !== '') {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $usuarios = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$roles = $conn->query("SELECT id, nombre FROM roles ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

$totales = $conn->query("SELECT r.nombre, COUNT(*) AS total FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id GROUP BY r.nombre")->fetch_all(MYSQLI_ASSOC);

function badge_estado_usuario(string $estado): string
{
    $map = [
        'Activo' => 'success',
        'Pendiente' => 'warning text-dark',
        'Bloqueado' => 'danger',
        'Inactivo' => 'secondary',
    ];
    $clase = $map[$estado] ?? 'secondary';
    return '<span class="badge bg-' . $clase . '">' . htmlspecialchars($estado) . '</span>';
}

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Todos los Usuarios</h3>
                <p class="text-muted">Vista global de administradores, reclutadores y candidatos registrados.</p>
            </div>
        </div>

        <!-- TARJETAS POR ROL -->
        <div class="row g-4 mb-4">
            <?php foreach ($totales as $t): ?>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-subtle">
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= (int) $t['total'] ?></h3>
                        <p class="mb-0 text-muted"><?= e($t['nombre']) ?>s</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="table-responsive">
            <form method="GET" class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0">Lista de Usuarios</h4>
                <div class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control" value="<?= e($buscar) ?>" placeholder="Buscar por nombre o correo...">
                    </div>
                    <select name="rol" class="form-select" onchange="this.form.submit()">
                        <option value="0">Todos los roles</option>
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['id'] ?>" <?= $filtroRol === (int) $r['id'] ? 'selected' : '' ?>><?= e($r['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registrado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No se encontraron usuarios.</td>
                    </tr>
                    <?php else: foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= e($u['nombre_completo']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= e($u['rol']) ?></span></td>
                        <td><?= badge_estado_usuario($u['estado']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

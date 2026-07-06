<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT id FROM reclutadores WHERE usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$reclutador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reclutador) {
    die('No se encontró el perfil de reclutador asociado a este usuario.');
}
$reclutadorId = (int) $reclutador['id'];

$buscar = trim($_GET['buscar'] ?? '');

// ---------- ACCIONES: cambiar estado de la entrevista ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_estado') {
    $id = (int) ($_POST['id'] ?? 0);
    $nuevoEstado = $_POST['estado'] ?? '';
    if (in_array($nuevoEstado, ['Programada', 'Realizada', 'Cancelada'], true) && $id > 0) {
        $stmt = $conn->prepare("UPDATE entrevistas e
                                 INNER JOIN postulaciones p ON e.postulacion_id = p.id
                                 INNER JOIN vacantes v ON p.vacante_id = v.id
                                 SET e.estado = ?
                                 WHERE e.id = ? AND v.reclutador_id = ?");
        $stmt->bind_param('sii', $nuevoEstado, $id, $reclutadorId);
        $stmt->execute();
        $stmt->close();
        redirect_to('entrevistas.php?type=success&msg=' . urlencode('Estado de la entrevista actualizado.'));
    }
}

// ---------- CONTADORES ----------
$sqlBase = "FROM entrevistas e
            INNER JOIN postulaciones p ON e.postulacion_id = p.id
            INNER JOIN vacantes v ON p.vacante_id = v.id
            WHERE v.reclutador_id = ?";

$stmt = $conn->prepare("SELECT
        SUM(e.estado='Programada') AS programadas,
        SUM(e.estado='Realizada') AS realizadas,
        SUM(e.estado='Cancelada') AS canceladas
        $sqlBase");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$conteo = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ---------- LISTADO ----------
$sqlListado = "SELECT e.id, e.fecha, e.estado,
                      c.nombre_completo AS candidato,
                      v.trabajo AS vacante
               FROM entrevistas e
               INNER JOIN postulaciones p ON e.postulacion_id = p.id
               INNER JOIN vacantes v ON p.vacante_id = v.id
               INNER JOIN candidatos c ON p.candidato_id = c.id
               WHERE v.reclutador_id = ?";

$params = [$reclutadorId];
$types = 'i';

if ($buscar !== '') {
    $sqlListado .= " AND (c.nombre_completo LIKE ? OR v.trabajo LIKE ?)";
    $like = '%' . $buscar . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
$sqlListado .= " ORDER BY e.fecha DESC";

$stmt = $conn->prepare($sqlListado);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$entrevistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

$badges = [
    'Programada' => 'bg-warning text-dark',
    'Realizada'  => 'bg-success',
    'Cancelada'  => 'bg-danger',
];

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Lista de Entrevistas</h4>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar entrevista..." value="<?= e($buscar) ?>">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </form>
                <a href="crear_entrevista.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Nueva Entrevista</a>
            </div>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-clock-fill text-warning"></i></div><div><h3 class="fw-bold"><?= (int)($conteo['programadas'] ?? 0) ?></h3><p class="mb-0">Programadas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold"><?= (int)($conteo['realizadas'] ?? 0) ?></h3><p class="mb-0">Realizadas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-x-circle-fill text-danger"></i></div><div><h3 class="fw-bold"><?= (int)($conteo['canceladas'] ?? 0) ?></h3><p class="mb-0">Canceladas</p></div></div>
            </div>
        </div>

        <div class="table-box">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Candidato</th>
                        <th>Vacante</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$entrevistas): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay entrevistas registradas.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($entrevistas as $ent): ?>
                        <tr>
                            <td><?= e($ent['candidato']) ?></td>
                            <td><?= e($ent['vacante']) ?></td>
                            <td><?= date('d/m/Y', strtotime($ent['fecha'])) ?></td>
                            <td><?= date('h:i A', strtotime($ent['fecha'])) ?></td>
                            <td><span class="badge <?= $badges[$ent['estado']] ?? 'bg-secondary' ?>"><?= e($ent['estado']) ?></span></td>
                            <td class="text-center">
                                <a href="ver_entrevista.php?id=<?= (int) $ent['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i> Ver</a>
                                <?php if ($ent['estado'] === 'Programada'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id" value="<?= (int) $ent['id'] ?>">
                                    <input type="hidden" name="estado" value="Realizada">
                                    <button class="btn btn-success btn-sm" title="Marcar como realizada"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id" value="<?= (int) $ent['id'] ?>">
                                    <input type="hidden" name="estado" value="Cancelada">
                                    <button class="btn btn-danger btn-sm" title="Cancelar" onclick="return confirm('¿Cancelar esta entrevista?');"><i class="bi bi-x-lg"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
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

// Totales generales
$stmt = $conn->prepare("SELECT COUNT(*) AS total, SUM(activa=1) AS activas FROM vacantes WHERE reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$resVac = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM postulaciones p INNER JOIN vacantes v ON p.vacante_id = v.id WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$totalPostulaciones = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM entrevistas e
                         INNER JOIN postulaciones p ON e.postulacion_id = p.id
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$totalEntrevistas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

// Postulaciones agrupadas por estado
$stmt = $conn->prepare("SELECT ep.nombre AS estado, COUNT(p.id) AS total
                         FROM estados_postulacion ep
                         LEFT JOIN postulaciones p ON p.estado_id = ep.id
                            AND p.vacante_id IN (SELECT id FROM vacantes WHERE reclutador_id = ?)
                         GROUP BY ep.id, ep.nombre");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$porEstado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$maxEstado = max(1, max(array_column($porEstado, 'total') ?: [0]));

// Vacantes con más postulaciones
$stmt = $conn->prepare("SELECT v.trabajo, COUNT(p.id) AS total
                         FROM vacantes v
                         LEFT JOIN postulaciones p ON p.vacante_id = v.id
                         WHERE v.reclutador_id = ?
                         GROUP BY v.id, v.trabajo
                         ORDER BY total DESC LIMIT 5");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$topVacantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$maxVacante = max(1, max(array_column($topVacantes, 'total') ?: [0]));

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div class="mb-4">
            <h2 class="fw-bold">Mis Reportes</h2>
            <p class="text-muted">Resumen del desempeño de tus vacantes y procesos.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold"><?= (int)($resVac['total'] ?? 0) ?></h3><p class="text-muted mb-0">Vacantes Totales</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold"><?= (int)($resVac['activas'] ?? 0) ?></h3><p class="text-muted mb-0">Vacantes Activas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-people-fill text-info"></i></div><div><h3 class="fw-bold"><?= $totalPostulaciones ?></h3><p class="text-muted mb-0">Postulaciones</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-calendar-event-fill text-warning"></i></div><div><h3 class="fw-bold"><?= $totalEntrevistas ?></h3><p class="text-muted mb-0">Entrevistas</p></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Postulaciones por Estado</h5>
                    <?php foreach ($porEstado as $row): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><?= e($row['estado']) ?></span>
                                <strong><?= (int) $row['total'] ?></strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: <?= ((int)$row['total'] / $maxEstado) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes con más Postulaciones</h5>
                    <?php if (!$topVacantes): ?>
                        <p class="text-muted">Aún no tienes vacantes registradas.</p>
                    <?php endif; ?>
                    <?php foreach ($topVacantes as $row): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><?= e($row['trabajo']) ?></span>
                                <strong><?= (int) $row['total'] ?></strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?= ((int)$row['total'] / $maxVacante) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
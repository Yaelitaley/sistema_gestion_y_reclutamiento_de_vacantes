<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

$tieneVacantes = table_exists($conn, 'vacantes');
$tienePostulaciones = table_exists($conn, 'postulaciones');
$tieneEntrevistas = table_exists($conn, 'entrevistas');
$tieneCandidatos = table_exists($conn, 'candidatos');
$tieneReclutadores = table_exists($conn, 'reclutadores');

$stats = [
    'vacantes' => 0,
    'entrevistas' => 0,
    'contrataciones' => 0,
    'rechazados' => 0,
    'candidatos' => 0,
    'reclutadores' => 0,
    'postulaciones' => 0
];

$estadosPostulaciones = [
    'Postulado' => 0,
    'En revisión' => 0,
    'Entrevista' => 0,
    'Contratado' => 0,
    'Rechazado' => 0
];

$vacantesPorCategoria = [];
$vacantesPorEstado = ['Activo' => 0, 'Inactivo' => 0];
$recientes = [];

if ($tieneVacantes) {
    $stats['vacantes'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM vacantes')->fetch_assoc()['total'] ?? 0);

    $result = $conn->query('SELECT categoria, COUNT(*) AS total FROM vacantes GROUP BY categoria ORDER BY total DESC');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $vacantesPorCategoria[$row['categoria']] = (int) $row['total'];
        }
    }

    // Se corrige: Agrupación por el campo real 'activa' (1 o 0)
    $result = $conn->query('SELECT activa, COUNT(*) AS total FROM vacantes GROUP BY activa');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $estadoTxt = ((int)$row['activa'] === 1) ? 'Activo' : 'Inactivo';
            $vacantesPorEstado[$estadoTxt] = (int) $row['total'];
        }
    }

    $postSql = $tienePostulaciones ? '(SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id = v.id)' : '0';
    // Se corrige: v.trabajo en lugar de v.titulo, y se evalúa el estado textual a partir de v.activa
    $result = $conn->query("SELECT v.trabajo AS titulo, v.categoria, IF(v.activa = 1, 'Activo', 'Inactivo') AS estado, {$postSql} AS postulaciones FROM vacantes v ORDER BY v.id DESC LIMIT 5");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $recientes[] = $row;
        }
    }
}

if ($tienePostulaciones) {
    $stats['postulaciones'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM postulaciones')->fetch_assoc()['total'] ?? 0);
    
    // Se corrige: Las postulaciones se vinculan a estados_postulacion para filtrar por nombre de estado
    $stats['contrataciones'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM postulaciones p INNER JOIN estados_postulacion ep ON p.estado_id = ep.id WHERE ep.nombre = 'Contratado'")->fetch_assoc()['total'] ?? 0);
    $stats['rechazados'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM postulaciones p INNER JOIN estados_postulacion ep ON p.estado_id = ep.id WHERE ep.nombre = 'Rechazado'")->fetch_assoc()['total'] ?? 0);

    // Se corrige: Agrupación por el nombre real del estado usando un JOIN
    $result = $conn->query('SELECT ep.nombre AS estado, COUNT(p.id) AS total FROM estados_postulacion ep LEFT JOIN postulaciones p ON ep.id = p.estado_id GROUP BY ep.id, ep.nombre');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (array_key_exists($row['estado'], $estadosPostulaciones)) {
                $estadosPostulaciones[$row['estado']] = (int) $row['total'];
            }
        }
    }
}

if ($tieneEntrevistas) {
    // Nota: Si tu tabla entrevistas usa un campo numérico o diferente para el estado, puedes ajustarlo aquí
    $stats['entrevistas'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM entrevistas WHERE estado = 'Realizada'")->fetch_assoc()['total'] ?? 0);
}

if ($tieneCandidatos) {
    $stats['candidatos'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM candidatos')->fetch_assoc()['total'] ?? 0);
}

if ($tieneReclutadores) {
    $stats['reclutadores'] = (int) ($conn->query('SELECT COUNT(*) AS total FROM reclutadores')->fetch_assoc()['total'] ?? 0);
}

$maxPostulaciones = max(1, max($estadosPostulaciones));
$maxCategorias = max(1, $vacantesPorCategoria ? max($vacantesPorCategoria) : 1);

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Reportes</h2>
                <p class="text-muted">Estadísticas y análisis del sistema con datos de la base de datos.</p>
            </div>
        </div>

        <?php if (!$tieneVacantes || !$tienePostulaciones): ?>
            <div class="alert alert-warning">
                Algunas estadísticas pueden aparecer en cero porque faltan tablas. Para tener el reporte completo importa tus scripts SQL.
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold"><?= $stats['vacantes'] ?></h3><p class="text-muted mb-0">Vacantes Publicadas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-person-video3 text-warning"></i></div><div><h3 class="fw-bold"><?= $stats['entrevistas'] ?></h3><p class="text-muted mb-0">Entrevistas Realizadas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-bar-chart-fill text-success"></i></div><div><h3 class="fw-bold"><?= $stats['contrataciones'] ?></h3><p class="text-muted mb-0">Contrataciones</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-x-circle-fill text-danger"></i></div><div><h3 class="fw-bold"><?= $stats['rechazados'] ?></h3><p class="text-muted mb-0">Candidatos Rechazados</p></div></div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-people-fill text-info"></i></div><div><h3 class="fw-bold"><?= $stats['candidatos'] ?></h3><p class="text-muted mb-0">Candidatos registrados</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-secondary-subtle"><i class="bi bi-person-badge-fill text-secondary"></i></div><div><h3 class="fw-bold"><?= $stats['reclutadores'] ?></h3><p class="text-muted mb-0">Reclutadores registrados</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-send-fill text-success"></i></div><div><h3 class="fw-bold"><?= $stats['postulaciones'] ?></h3><p class="text-muted mb-0">Total postulaciones</p></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Candidatos por estado de postulación</h5>

                    <?php foreach ($estadosPostulaciones as $estado => $total): ?>
                        <?php $porcentaje = (int) round(($total / $maxPostulaciones) * 100); ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>
                                    <?php if (function_exists('badge_estado')): ?>
                                        <?= badge_estado($estado) ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($estado) ?></span>
                                    <?php endif; ?>
                                </span>
                                <strong><?= (int) $total ?></strong>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes por estado</h5>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>
                            <?php if (function_exists('badge_estado')): ?>
                                <?= badge_estado('Activo') ?>
                            <?php else: ?>
                                <span class="badge bg-success">Activo</span>
                            <?php endif; ?>
                        </span>
                        <strong><?= (int) ($vacantesPorEstado['Activo'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <?php if (function_exists('badge_estado')): ?>
                                <?= badge_estado('Inactivo') ?>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </span>
                        <strong><?= (int) ($vacantesPorEstado['Inactivo'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes por categoría</h5>
                    <?php if (!$vacantesPorCategoria): ?>
                        <p class="text-muted mb-0">No hay vacantes registradas.</p>
                    <?php endif; ?>

                    <?php foreach ($vacantesPorCategoria as $categoria => $total): ?>
                        <?php $porcentaje = (int) round(($total / $maxCategorias) * 100); ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= e($categoria) ?></span>
                                <strong><?= (int) $total ?></strong>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Últimas vacantes</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Vacante</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Postulaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$recientes): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Sin información.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($recientes as $vacante): ?>
                                    <tr>
                                        <td><?= e($vacante['titulo']) ?></td>
                                        <td><?= e($vacante['categoria']) ?></td>
                                        <td>
                                            <?php if (function_exists('badge_estado')): ?>
                                                <?= badge_estado($vacante['estado']) ?>
                                            <?php else: ?>
                                                <span class="badge <?= $vacante['estado'] === 'Activo' ? 'bg-success' : 'bg-danger' ?>"><?= e($vacante['estado']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int) $vacante['postulaciones'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare("SELECT id FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->bind_result($candidato_id);
$stmt->fetch();
$stmt->close();

if (!$candidato_id) {
    header('Location: login.php');
    exit;
}

$busqueda = trim($_GET['busqueda'] ?? '');
$estado   = trim($_GET['estado'] ?? '');

$mapaEstados = [
    'postulado'   => 1,
    'revision'    => 2,
    'entrevista'  => 3,
    'rechazado'   => 4,
    'contratado'  => 5,
];

// ----- Tarjetas de resumen (siempre sobre el total real, sin filtros) -----
$stmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(estado_id = 2) AS en_revision,
        SUM(estado_id = 3) AS entrevistas,
        SUM(estado_id = 5) AS contratado
     FROM postulaciones
     WHERE candidato_id = ?"
);
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$stmt->bind_result($total, $enRevision, $entrevistas, $contratado);
$stmt->fetch();
$stmt->close();

$total       = $total ?? 0;
$enRevision  = $enRevision ?? 0;
$entrevistas = $entrevistas ?? 0;
$contratado  = $contratado ?? 0;

// ----- Listado con filtros -----
$sql = "SELECT p.id, v.trabajo, e.nombre AS empresa, v.ubicacion, p.fecha_postulacion, ep.id AS estado_id, ep.nombre AS estado
        FROM postulaciones p
        INNER JOIN vacantes v ON p.vacante_id = v.id
        INNER JOIN reclutadores r ON v.reclutador_id = r.id
        INNER JOIN empresas e ON r.empresa_id = e.id
        INNER JOIN estados_postulacion ep ON p.estado_id = ep.id
        WHERE p.candidato_id = ?";

$params = [$candidato_id];
$types  = 'i';

if ($busqueda !== '') {
    $sql .= " AND (v.trabajo LIKE ? OR e.nombre LIKE ?)";
    $like = '%' . $busqueda . '%';
    $params[] = $like; $params[] = $like;
    $types .= 'ss';
}
if ($estado !== '' && isset($mapaEstados[$estado])) {
    $sql .= " AND p.estado_id = ?";
    $params[] = $mapaEstados[$estado];
    $types .= 'i';
}

$sql .= " ORDER BY p.fecha_postulacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$postulaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function badgeEstadoPostulacion($estado) {
    $map = [
        'Postulado'    => 'bg-secondary',
        'En revisión'  => 'bg-warning text-dark',
        'Entrevista'   => 'bg-info',
        'Rechazado'    => 'bg-danger',
        'Contratado'   => 'bg-success',
    ];
    $clase = $map[$estado] ?? 'bg-secondary';
    return '<span class="badge ' . $clase . '">' . htmlspecialchars($estado) . '</span>';
}
?>
<?php include "includes/header.php"; ?>

<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>

        <!-- TÍTULO -->
        <div class="mb-4">
            <h2 class="fw-bold">Mis Postulaciones</h2>
            <p class="text-muted">Consulta el estado de todas las vacantes a las que te has postulado.</p>
        </div>

       <!-- TARJETAS -->
<div class="row g-4 mb-5 p-4">

    <div class="col-lg-3 col-md-6">
        <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
            <div class="card-icon bg-primary-subtle">
                <i class="bi bi-send-check-fill text-primary"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0"><?= (int)$total ?></h3>
                <p class="text-muted mb-0">Postulaciones</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
            <div class="card-icon bg-warning-subtle">
                <i class="bi bi-clock-history text-warning"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0"><?= (int)$enRevision ?></h3>
                <p class="text-muted mb-0">En Revisión</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
            <div class="card-icon bg-info-subtle">
                <i class="bi bi-calendar-event-fill text-info"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0"><?= (int)$entrevistas ?></h3>
                <p class="text-muted mb-0">Entrevistas</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
            <div class="card-icon bg-success-subtle">
                <i class="bi bi-check-circle-fill text-success"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0"><?= (int)$contratado ?></h3>
                <p class="text-muted mb-0">Contratado</p>
            </div>
        </div>
    </div>

</div>

        <!-- BUSCADOR Y FILTROS -->
        <form action="postulaciones.php" method="GET">
            <div class="table-responsive mb-4">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="busqueda" class="form-control" placeholder="Buscar empresa o puesto..." value="<?= htmlspecialchars($busqueda) ?>">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" name="estado">
                            <option value="">Todas</option>
                            <option value="postulado" <?= $estado === 'postulado' ? 'selected' : '' ?>>Postulado</option>
                            <option value="revision" <?= $estado === 'revision' ? 'selected' : '' ?>>En revisión</option>
                            <option value="entrevista" <?= $estado === 'entrevista' ? 'selected' : '' ?>>Entrevista</option>
                            <option value="contratado" <?= $estado === 'contratado' ? 'selected' : '' ?>>Contratado</option>
                            <option value="rechazado" <?= $estado === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-candidato w-100">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- TABLA DE POSTULACIONES -->
        <div class="table-box">
            <h4 class="fw-bold mb-4">Historial de Postulaciones</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Puesto</th>
                            <th>Empresa</th>
                            <th>Ubicación</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($postulaciones)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No se encontraron postulaciones.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($postulaciones as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['trabajo']) ?></td>
                                    <td><?= htmlspecialchars($p['empresa']) ?></td>
                                    <td><?= htmlspecialchars($p['ubicacion']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_postulacion'])) ?></td>
                                    <td><?= badgeEstadoPostulacion($p['estado']) ?></td>
                                    <td class="text-center">
                                        <a href="ver-empleo.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye-fill"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btnCancelarPostulacion" data-postulacion-id="<?= (int)$p['id'] ?>"><i class="bi bi-x-circle-fill"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RESUMEN -->
        <div class="row mt-5">
            <div class="col-lg-6">
                <div class="dashboard-card p-4">
                    <div class="card-icono bg-success-subtle"><i class="bi bi-graph-up-arrow text-success"></i></div>
                    <div>
                        <h5 class="fw-bold">Resumen</h5>
                        <p class="mb-0 text-muted">
                            Has enviado <strong><?= (int)$total ?> postulaciones</strong>, de las cuales <strong><?= (int)$enRevision ?></strong> continúan en revisión y <strong><?= (int)$entrevistas ?></strong> ya avanzaron a entrevista.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-card p-4">
                    <div class="card-icono bg-primary-subtle"><i class="bi bi-lightbulb-fill text-primary"></i></div>
                    <div>
                        <h5 class="fw-bold">Recomendación</h5>
                        <p class="mb-0 text-muted">
                            Mantén actualizado tu perfil y tu currículum para aumentar tus posibilidades de ser seleccionado.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="table-box mt-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="fw-bold">¿Deseas buscar nuevas oportunidades?</h4>
                    <p class="text-muted mb-0">Explora nuevas vacantes disponibles y continúa creciendo profesionalmente.</p>
                </div>
                <div class="mt-3 mt-lg-0">
                    <a href="explorar-empleos.php" class="btn btn-candidato">
                        <i class="bi bi-search me-2"></i>
                        Explorar Empleos
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
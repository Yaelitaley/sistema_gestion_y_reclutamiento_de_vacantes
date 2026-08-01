<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

$vacante_id = intval($_GET['id'] ?? 0);

if ($vacante_id <= 0) {
    header('Location: explorar-empleos.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare("SELECT id FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->bind_result($candidato_id);
$stmt->fetch();
$stmt->close();

// Datos de la vacante
$stmt = $conn->prepare(
    "SELECT v.id, v.trabajo, v.descripcion, v.categoria, v.requisitos, v.salario, v.ubicacion,
            v.nivel_experiencia, v.modalidad, v.created_at, e.nombre AS empresa, e.logo_path
     FROM vacantes v
     INNER JOIN reclutadores r ON v.reclutador_id = r.id
     INNER JOIN empresas e ON r.empresa_id = e.id
     WHERE v.id = ? AND v.activa = 1"
);
$stmt->bind_param('i', $vacante_id);
$stmt->execute();
$vacante = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$vacante) {
    header('Location: explorar-empleos.php');
    exit;
}

// ¿El candidato ya se postuló a esta vacante?
$yaPostulado = false;
if ($candidato_id) {
    $stmt = $conn->prepare("SELECT id FROM postulaciones WHERE candidato_id = ? AND vacante_id = ?");
    $stmt->bind_param('ii', $candidato_id, $vacante_id);
    $stmt->execute();
    $stmt->store_result();
    $yaPostulado = $stmt->num_rows > 0;
    $stmt->close();
}

// Requisitos en líneas (para mostrarlos como lista)
$listaRequisitos = array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $vacante['requisitos'])));

// Vacantes relacionadas (misma categoría, distinta a la actual)
$stmt = $conn->prepare(
    "SELECT v.id, v.trabajo, v.salario, v.modalidad, e.nombre AS empresa
     FROM vacantes v
     INNER JOIN reclutadores r ON v.reclutador_id = r.id
     INNER JOIN empresas e ON r.empresa_id = e.id
     WHERE v.activa = 1 AND v.categoria = ? AND v.id != ?
     ORDER BY v.created_at DESC
     LIMIT 3"
);
$stmt->bind_param('si', $vacante['categoria'], $vacante_id);
$stmt->execute();
$relacionadas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function tiempoPublicado($fecha) {
    $dias = floor((time() - strtotime($fecha)) / 86400);
    if ($dias <= 0) return 'Publicado hoy';
    if ($dias === 1) return 'Publicado hace 1 día';
    return 'Publicado hace ' . $dias . ' días';
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

        <!-- INFORMACIÓN PRINCIPAL -->
        <div class="table-box mb-5">
            <div class="row align-items-center">
                <!-- LOGO -->
                <div class="col-lg-2 text-center">
                    <img src="../<?= htmlspecialchars($vacante['logo_path'] ?: 'assets/img/imagen1.png') ?>" class="img-fluid rounded" style="max-width:120px;" alt="Empresa">
                </div>
                <!-- DATOS -->
                <div class="col-lg-7">
                    <h2 class="fw-bold"><?= htmlspecialchars($vacante['trabajo']) ?></h2>
                    <h5 class="text-success"><?= htmlspecialchars($vacante['empresa']) ?></h5>
                    <div class="mt-3">
                        <?php if (!empty($vacante['modalidad'])): ?>
                            <span class="badge bg-primary me-2"><?= htmlspecialchars($vacante['modalidad']) ?></span>
                        <?php endif; ?>
                        <span class="badge bg-success me-2"><?= htmlspecialchars($vacante['nivel_experiencia']) ?></span>
                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($vacante['categoria']) ?></span>
                    </div>
                    <div class="mt-4">
                        <p><i class="bi bi-geo-alt-fill text-danger me-2"></i> <?= htmlspecialchars($vacante['ubicacion']) ?></p>
                        <p><i class="bi bi-cash-stack text-success me-2"></i> <?= $vacante['salario'] !== null ? '$' . number_format($vacante['salario'], 2) . ' MXN mensuales' : 'Salario a convenir' ?></p>
                        <p><i class="bi bi-calendar-event-fill text-primary me-2"></i> <?= tiempoPublicado($vacante['created_at']) ?></p>
                    </div>
                </div>
                <!-- BOTONES -->
                <div class="col-lg-3">
                    <div class="d-grid gap-3">
                        <?php if ($yaPostulado): ?>
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Ya te postulaste
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-candidato btn-lg btnPostular" data-vacante-id="<?= (int)$vacante['id'] ?>">
                                <i class="bi bi-send-fill me-2"></i>
                                Postularme
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-success btnGuardar">
                            <i class="bi bi-heart me-2"></i>
                            Guardar Empleo
                        </button>
                        <button type="button" class="btn btn-outline-primary btnCompartir">
                            <i class="bi bi-share-fill me-2"></i>
                            Compartir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESCRIPCIÓN -->
        <div class="table-box mb-4">
            <h3 class="fw-bold mb-4">Descripción del Puesto</h3>
            <p class="text-muted"><?= nl2br(htmlspecialchars($vacante['descripcion'])) ?></p>
        </div>

        <!-- REQUISITOS -->
        <div class="table-box mb-4">
            <h3 class="fw-bold mb-4">Requisitos</h3>
            <?php if (empty($listaRequisitos)): ?>
                <p class="text-muted mb-0">No se especificaron requisitos.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($listaRequisitos as $req): ?>
                        <li class="list-group-item">✔ <?= htmlspecialchars($req) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- INFORMACIÓN DE LA EMPRESA -->
        <div class="table-box mb-5">
            <h3 class="fw-bold mb-4">Acerca de la Empresa</h3>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Información</h5>
                            <p><i class="bi bi-building me-2 text-primary"></i> <?= htmlspecialchars($vacante['empresa']) ?></p>
                            <p><i class="bi bi-geo-alt-fill me-2 text-danger"></i> <?= htmlspecialchars($vacante['ubicacion']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VACANTES RELACIONADAS -->
        <div class="table-box mb-5 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 p-4">
                <h3 class="fw-bold">Vacantes Relacionadas</h3>
                <a href="explorar-empleos.php" class="btn btn-outline-success">Ver Todas</a>
            </div>
            <div class="row g-4">
                <?php if (empty($relacionadas)): ?>
                    <div class="col-12">
                        <p class="text-muted mb-0">No hay vacantes relacionadas por el momento.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($relacionadas as $r): ?>
                        <div class="col-lg-4">
                            <div class="job-card p-4">
                                <h5 class="fw-bold"><?= htmlspecialchars($r['trabajo']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($r['empresa']) ?></p>
                                <?php if (!empty($r['modalidad'])): ?>
                                    <span class="badge bg-primary mb-3"><?= htmlspecialchars($r['modalidad']) ?></span>
                                <?php endif; ?>
                                <p><i class="bi bi-cash-stack me-2"></i> <?= $r['salario'] !== null ? '$' . number_format($r['salario'], 2) . ' MXN' : 'A convenir' ?></p>
                                <a href="ver-empleo.php?id=<?= (int)$r['id'] ?>" class="btn btn-outline-success w-100">Ver Vacante</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="explorar-empleos.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
            <div>
                <button type="button" class="btn btn-outline-success me-2 btnGuardar">
                    <i class="bi bi-heart-fill me-2"></i>
                    Guardar Empleo
                </button>
                <?php if (!$yaPostulado): ?>
                    <button type="button" class="btn btn-candidato btnPostular" data-vacante-id="<?= (int)$vacante['id'] ?>">
                        <i class="bi bi-send-fill me-2"></i>
                        Postularme
                    </button>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
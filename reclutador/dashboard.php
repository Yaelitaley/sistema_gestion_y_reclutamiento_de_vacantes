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

// ---------- TARJETAS ----------
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM vacantes WHERE reclutador_id = ? AND activa = 1");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$vacantesActivas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM postulaciones p INNER JOIN vacantes v ON p.vacante_id = v.id WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$totalPostulantes = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM entrevistas e
                         INNER JOIN postulaciones p ON e.postulacion_id = p.id
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$totalEntrevistas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM postulaciones p
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         WHERE v.reclutador_id = ? AND p.estado_id = 5");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$totalContratados = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

// ---------- MIS PROCESOS ACTIVOS (últimas vacantes con conteo de postulantes) ----------
$stmt = $conn->prepare("SELECT v.id, v.trabajo, v.activa, v.updated_at,
                                (SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id = v.id) AS postulados
                         FROM vacantes v
                         WHERE v.reclutador_id = ?
                         ORDER BY v.updated_at DESC
                         LIMIT 5");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$procesos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- PRÓXIMAS ENTREVISTAS ----------
$stmt = $conn->prepare("SELECT e.fecha, c.nombre_completo AS candidato, v.trabajo
                         FROM entrevistas e
                         INNER JOIN postulaciones p ON e.postulacion_id = p.id
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         INNER JOIN candidatos c ON p.candidato_id = c.id
                         WHERE v.reclutador_id = ? AND e.estado = 'Programada' AND e.fecha >= NOW()
                         ORDER BY e.fecha ASC
                         LIMIT 4");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$proximasEntrevistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- ETAPAS DE RECLUTAMIENTO ----------
$stmt = $conn->prepare("SELECT
        SUM(p.estado_id = 1) AS postulados,
        SUM(p.estado_id = 2) AS revision,
        SUM(p.estado_id = 3) AS entrevistas,
        SUM(p.estado_id = 5) AS contratados
        FROM postulaciones p
        INNER JOIN vacantes v ON p.vacante_id = v.id
        WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$etapas = $stmt->get_result()->fetch_assoc();
$stmt->close();
$etapas = $etapas ?: [];
foreach (['postulados', 'revision', 'entrevistas', 'contratados'] as $k) {
    $etapas[$k] = (int) ($etapas[$k] ?? 0);
}

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-subtle">
                        <i class="bi bi-building-check text-primary"></i>
                    </div>
                    <div>
                        <h3 class="texto fw-bold"><?= $vacantesActivas ?></h3>
                        <p class="texto mb-0">
                            Vacantes Activas
                        </p>
                    </div>
                </div>
            </div>
            <div class="table-responsive col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-success-subtle">
                        <i class="bi bi-person-vcard-fill text-success"></i>
                    </div>
                    <div>
                        <h3 class="texto fw-bold"><?= $totalPostulantes ?></h3>
                        <p class="texto  mb-0">
                            Postulantes
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-warning-subtle">
                        <i class="bi bi-calendar2-check-fill text-warning"></i>
                    </div>
                    <div>
                        <h3 class="texto fw-bold"><?= $totalEntrevistas ?></h3>
                        <p class="texto  mb-0">
                            Entrevistas
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-info-subtle">
                        <i class="bi bi-envelope-check-fill text-info"></i>
                    </div>
                    <div>
                        <h3 class="texto fw-bold"><?= $totalContratados ?></h3>
                        <p class="texto mb-0">
                            Contratados
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- TABLAS -->
        <div class="row g-4">
            <!-- VACANTES -->
            <div class="col-lg-8">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="texto fw-bold">
                            Mis Procesos Activos
                        </h5>
                        <a href="vacantes.php"
                           class="btn btn-sm btn-reclutador">
                            Ver Vacantes
                        </a>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Puesto</th>
                                <th>Postulados</th>
                                <th>Estado</th>
                                <th>Actualizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($procesos)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Aún no tienes vacantes publicadas.</td>
                            </tr>
                            <?php else: foreach ($procesos as $proc): ?>
                            <tr>
                                <td><?= e($proc['trabajo']) ?></td>
                                <td><?= (int) $proc['postulados'] ?></td>
                                <td>
                                    <?= $proc['activa']
                                        ? '<span class="badge bg-success">Activa</span>'
                                        : '<span class="badge bg-secondary">Inactiva</span>' ?>
                                </td>
                                <td><?= e(date('d/m/Y', strtotime($proc['updated_at']))) ?></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ENTREVISTAS -->
            <div class="col-lg-4">
                <div class="action-box">
                    <h5 class="texto fw-bold mb-4">
                        Próximas Entrevistas
                    </h5>
                    <?php if (empty($proximasEntrevistas)): ?>
                    <p class="text-muted">No tienes entrevistas programadas.</p>
                    <?php else: foreach ($proximasEntrevistas as $ent): ?>
                    <div class="mb-4">
                        <h6 class="texto fw-bold">
                            <?= e($ent['candidato']) ?>
                        </h6>
                        <small class="texto text-muted">
                            <?= e($ent['trabajo']) ?>
                        </small>
                        <br>
                        <small>
                            <?= e(date('d/m/Y g:i A', strtotime($ent['fecha']))) ?>
                        </small>
                    </div>
                    <?php endforeach; endif; ?>
                    <a href="entrevistas.php"
                       class="btn btn-reclutador w-100">
                        Ver Entrevistas
                    </a>
                </div>
            </div>
        </div>
        <!-- PROCESO -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-box">
                    <h5 class="texto fw-bold mb-4">
                        Etapas de Reclutamiento
                    </h5>
                    <div class="row text-center">
                        <div class="col">
                            <h2><?= $etapas['postulados'] ?></h2>
                            <p>Postulados</p>
                        </div>
                        <div class="col">
                            <h2><?= $etapas['revision'] ?></h2>
                            <p>Revisión</p>
                        </div>
                        <div class="col">
                            <h2><?= $etapas['entrevistas'] ?></h2>
                            <p>Entrevistas</p>
                        </div>
                        <div class="col">
                            <h2><?= $etapas['contratados'] ?></h2>
                            <p>Contratados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

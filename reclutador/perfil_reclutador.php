<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT r.*, u.correo, u.created_at, e.nombre AS empresa
                         FROM reclutadores r
                         INNER JOIN usuarios u ON r.usuario_id = u.id
                         LEFT JOIN empresas e ON r.empresa_id = e.id
                         WHERE r.usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$perfil = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$perfil) {
    die('No se encontró el perfil de reclutador asociado a este usuario.');
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM vacantes WHERE reclutador_id = ?");
$stmt->bind_param('i', $perfil['id']);
$stmt->execute();
$vacantesPublicadas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(DISTINCT p.candidato_id) AS total FROM postulaciones p
                         INNER JOIN vacantes v ON p.vacante_id = v.id WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $perfil['id']);
$stmt->execute();
$candidatosGestionados = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM entrevistas e
                         INNER JOIN postulaciones p ON e.postulacion_id = p.id
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         WHERE v.reclutador_id = ? AND e.estado = 'Realizada'");
$stmt->bind_param('i', $perfil['id']);
$stmt->execute();
$entrevistasRealizadas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Mi Perfil</h2>
                <p class="text-muted">Consulta la información de tu perfil como reclutador.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="table-box text-center">
                    <img src="../assets/img/reclutador-avatar.png" class="rounded-circle shadow mb-3" width="180" height="180" alt="Reclutador">
                    <h3 class="fw-bold"><?= e($perfil['nombre_completo']) ?></h3>
                    <p class="text-muted">Reclutador</p>
                    <span class="badge <?= $perfil['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>"><?= e(ucfirst($perfil['estado'])) ?></span>
                    <hr>
                    <a href="editar_perfil.php" class="btn btn-primary"><i class="bi bi-pencil-fill me-2"></i>Editar Perfil</a>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="table-box">
                    <h4 class="fw-bold mb-4">Información Personal</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr><th width="35%">Nombre Completo</th><td><?= e($perfil['nombre_completo']) ?></td></tr>
                            <tr><th>Correo Electrónico</th><td><?= e($perfil['correo']) ?></td></tr>
                            <tr><th>Teléfono</th><td><?= e($perfil['telefono'] ?: 'No registrado') ?></td></tr>
                            <tr><th>Empresa</th><td><?= e($perfil['empresa'] ?: 'Sin empresa') ?></td></tr>
                            <tr><th>Fecha de Registro</th><td><?= date('d F Y', strtotime($perfil['created_at'])) ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold"><?= $vacantesPublicadas ?></h3><p class="mb-0">Vacantes Publicadas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-people-fill text-success"></i></div><div><h3 class="fw-bold"><?= $candidatosGestionados ?></h3><p class="mb-0">Candidatos Gestionados</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-calendar-check-fill text-warning"></i></div><div><h3 class="fw-bold"><?= $entrevistasRealizadas ?></h3><p class="mb-0">Entrevistas Realizadas</p></div></div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
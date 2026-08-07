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
$filtroEstado = trim($_GET['estado'] ?? 'Todos');

// ---------- TARJETAS DE RESUMEN ----------
$stmt = $conn->prepare("SELECT
        COUNT(*) AS total,
        SUM(ep.nombre = 'En revisión') AS revision,
        SUM(ep.nombre = 'Entrevista') AS entrevista,
        SUM(ep.nombre = 'Contratado') AS contratados
        FROM postulaciones p
        INNER JOIN vacantes v ON p.vacante_id = v.id
        INNER JOIN estados_postulacion ep ON p.estado_id = ep.id
        WHERE v.reclutador_id = ?");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$resumen = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();
foreach (['total', 'revision', 'entrevista', 'contratados'] as $k) {
    $resumen[$k] = (int) ($resumen[$k] ?? 0);
}

// ---------- LISTADO ----------
$sql = "SELECT p.id AS postulacion_id, c.id AS candidato_id, c.nombre_completo, c.puesto_deseado,
               v.trabajo AS vacante, ep.nombre AS estado, p.fecha_postulacion
        FROM postulaciones p
        INNER JOIN vacantes v ON p.vacante_id = v.id
        INNER JOIN candidatos c ON p.candidato_id = c.id
        INNER JOIN estados_postulacion ep ON p.estado_id = ep.id
        WHERE v.reclutador_id = ?";
$params = [$reclutadorId];
$types = 'i';

if ($buscar !== '') {
    $sql .= " AND (c.nombre_completo LIKE ? OR v.trabajo LIKE ?)";
    $like = '%' . $buscar . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
if ($filtroEstado !== '' && $filtroEstado !== 'Todos') {
    $sql .= " AND ep.nombre = ?";
    $params[] = $filtroEstado;
    $types .= 's';
}
$sql .= " ORDER BY p.fecha_postulacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$candidatos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">
                    Gestión de Candidatos
                </h2>
                <p class="text-muted">
                    Consulta los candidatos postulados a tus vacantes.
                </p>
            </div>
        </div>
        <!-- CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-subtle">
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= $resumen['total'] ?></h3>
                        <p class="mb-0 text-muted">
                            Total Candidatos
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-warning-subtle">
                        <i class="bi bi-search text-warning"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= $resumen['revision'] ?></h3>
                        <p class="mb-0 text-muted">
                            En Revisión
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-info-subtle">
                        <i class="bi bi-calendar-event-fill text-info"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= $resumen['entrevista'] ?></h3>
                        <p class="mb-0 text-muted">
                            Entrevistas
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="card-icon bg-success-subtle">
                        <i class="bi bi-person-check-fill text-success"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= $resumen['contratados'] ?></h3>
                        <p class="mb-0 text-muted">
                            Contratados
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- TABLA -->
        <div class="table-responsive">
           <form method="GET" class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        Lista de Candidatos
    </h4>
    <div class="d-flex gap-2">
        <!-- Buscador -->
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input
                type="text"
                name="buscar"
                id="buscarCandidato"
                class="form-control"
                value="<?= e($buscar) ?>"
                placeholder="Buscar candidato...">
        </div>
        <!-- Filtro -->
        <select
            id="filtroEstado"
            name="estado"
            class="form-select"
            onchange="this.form.submit()">
            <option value="Todos" <?= $filtroEstado === 'Todos' ? 'selected' : '' ?>>Todos</option>
            <option value="En revisión" <?= $filtroEstado === 'En revisión' ? 'selected' : '' ?>>En revisión</option>
            <option value="Entrevista" <?= $filtroEstado === 'Entrevista' ? 'selected' : '' ?>>Entrevista</option>
            <option value="Contratado" <?= $filtroEstado === 'Contratado' ? 'selected' : '' ?>>Contratado</option>
            <option value="Rechazado" <?= $filtroEstado === 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
        </select>
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </div>
</form>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Vacante</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($candidatos)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Todavía no hay candidatos postulados a tus vacantes.</td>
                    </tr>
                    <?php else: foreach ($candidatos as $cand): ?>
                    <tr>
                        <td>
                            <img
                                src="../assets/img/candidato02.png"
                                width="50"
                                class="rounded-circle">
                        </td>
                        <td>
                            <?= e($cand['nombre_completo']) ?>
                        </td>
                        <td>
                            <?= e($cand['vacante']) ?>
                        </td>
                        <td>
                            <?= badge_estado($cand['estado']) ?>
                        </td>
                        <td>
                            <?= e(date('d/m/Y', strtotime($cand['fecha_postulacion']))) ?>
                        </td>
                        <td class="text-center">
                            <a href="ver_candidatos.php?id=<?= (int) $cand['postulacion_id'] ?>"
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-eye-fill"></i>
                                Ver
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare(
    "SELECT id, nombre_completo, correo, telefono, ubicacion, genero, estado,
            puesto_deseado, disponibilidad, modalidad, salario_esperado,
            linkedin, github, portafolio, resumen, objetivos,
            ofertas_empleo, notificaciones_sistema, perfil_publico,
            fecha_nacimiento, nacionalidad, cv_path
     FROM candidatos WHERE usuario_id = ?"
);
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$candidato = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$candidato) {
    header('Location: login.php');
    exit;
}

$candidato_id = $candidato['id'];

// ----- Habilidades -----
$stmt = $conn->prepare("SELECT habilidad, nivel FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$habilidades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Estadísticas de postulaciones -----
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
$stmt->bind_result($totalPostulaciones, $enRevision, $entrevistas, $contratado);
$stmt->fetch();
$stmt->close();

$totalPostulaciones = $totalPostulaciones ?? 0;
$enRevision         = $enRevision ?? 0;
$entrevistas        = $entrevistas ?? 0;
$contratado         = $contratado ?? 0;

// ----- Objetivos como lista (una línea por objetivo) -----
$listaObjetivos = [];
if (!empty($candidato['objetivos'])) {
    $listaObjetivos = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $candidato['objetivos'])));
}

// ----- Porcentaje de perfil completo (real, basado en campos rellenados) -----
$camposPerfil = [
    $candidato['telefono'], $candidato['ubicacion'], $candidato['genero'],
    $candidato['puesto_deseado'], $candidato['disponibilidad'], $candidato['modalidad'],
    $candidato['salario_esperado'], $candidato['resumen'], $candidato['objetivos'],
    $candidato['cv_path'], !empty($habilidades) ? 'ok' : '',
];
$camposLlenos = count(array_filter($camposPerfil, function ($v) { return !empty($v); }));
$porcentajePerfil = (int) round(($camposLlenos / count($camposPerfil)) * 100);
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">
                    Mi Perfil
                </h2>
                <p class="text-muted">
                    Consulta y administra la información de tu cuenta.
                </p>
            </div>
           
        </div>

        <!-- PERFIL -->
        <div class="table-box mb-4">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center">
                    <img
                        src="../assets/img/candidato.png"
                        class="rounded-circle img-fluid mb-3"
                        style="width:180px; height:180px; object-fit:cover;"
                        alt="Foto de perfil">
                    <button class="btn btn-outline-primary" disabled title="Próximamente">
                        <i class="bi bi-camera-fill me-2"></i>
                        Cambiar Foto
                    </button>
                </div>
                <div class="col-lg-9">
                    <h2 class="fw-bold">
                        <?= htmlspecialchars($candidato['nombre_completo']) ?>
                    </h2>
                    <h5 class="text-success">
                        <?= htmlspecialchars($candidato['puesto_deseado'] ?: 'Puesto deseado no definido') ?>
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-envelope-fill text-primary me-2"></i>
                                <?= htmlspecialchars($candidato['correo']) ?>
                            </p>
                            <p>
                                <i class="bi bi-telephone-fill text-success me-2"></i>
                                <?= htmlspecialchars($candidato['telefono'] ?: 'Sin teléfono registrado') ?>
                            </p>
                            <p>
                                <i class="bi bi-person-fill text-warning me-2"></i>
                                <?= htmlspecialchars($candidato['genero'] ?: 'Género no especificado') ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                <?= htmlspecialchars($candidato['ubicacion'] ?: 'Ubicación no registrada') ?>
                            </p>
                            <p>
                                <i class="bi bi-person-badge-fill text-info me-2"></i>
                                Candidato
                            </p>
                            <p>
                                <i class="bi bi-<?= strtolower($candidato['estado']) === 'activo' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-warning' ?> me-2"></i>
                                <?= htmlspecialchars(ucfirst($candidato['estado'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN PROFESIONAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Información Profesional
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Puesto Deseado:</strong>
                        <?= htmlspecialchars($candidato['puesto_deseado'] ?: 'No especificado') ?>
                    </p>
                    <p>
                        <strong>Disponibilidad:</strong>
                        <?= htmlspecialchars($candidato['disponibilidad'] ?: 'No especificada') ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Modalidad Preferida:</strong>
                        <?= htmlspecialchars($candidato['modalidad'] ?: 'No especificada') ?>
                    </p>
                    <p>
                        <strong>Salario Esperado:</strong>
                        <?= htmlspecialchars($candidato['salario_esperado'] ?: 'No especificado') ?>
                    </p>
                </div>
            </div>
            <?php if (!empty($habilidades)): ?>
                <hr>
                <strong>Habilidades:</strong>
                <div class="mt-2">
                    <?php foreach ($habilidades as $h): ?>
                        <span class="badge bg-primary me-2 mb-2">
                            <?= htmlspecialchars($h['habilidad']) ?><?= !empty($h['nivel']) ? ' — ' . htmlspecialchars($h['nivel']) : '' ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icono bg-primary-subtle mt-2">
                        <i class="bi bi-send-check-fill text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= (int) $totalPostulaciones ?></h3>
                        <p class="text-muted mb-0">Postulaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icono bg-warning-subtle mt-2">
                        <i class="bi bi-clock-history text-warning"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= (int) $enRevision ?></h3>
                        <p class="text-muted mb-0">En Revisión</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icono bg-info-subtle mt-2">
                        <i class="bi bi-calendar-event-fill text-info"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= (int) $entrevistas ?></h3>
                        <p class="text-muted mb-0">Entrevistas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icono bg-success-subtle mt-2">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold"><?= (int) $contratado ?></h3>
                        <p class="text-muted mb-0">Contratado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACCESOS RÁPIDOS -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Accesos Rápidos
            </h4>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <a href="cv.php" class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-file-earmark-person-fill fs-4 d-block mb-2"></i>
                        Mi Currículum
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="postulaciones.php" class="btn btn-outline-success w-100 py-3">
                        <i class="bi bi-send-check-fill fs-4 d-block mb-2"></i>
                        Mis Postulaciones
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="explorar-empleos.php" class="btn btn-outline-warning w-100 py-3">
                        <i class="bi bi-search fs-4 d-block mb-2"></i>
                        Explorar Empleos
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="configuracion.php" class="btn btn-outline-secondary w-100 py-3">
                        <i class="bi bi-gear-fill fs-4 d-block mb-2"></i>
                        Configuración
                    </a>
                </div>
            </div>
        </div>

        <!-- REDES PROFESIONALES -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Redes Profesionales
            </h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">LinkedIn</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($candidato['linkedin'] ?: 'No registrado') ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">GitHub</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($candidato['github'] ?: 'No registrado') ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Portafolio</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($candidato['portafolio'] ?: 'No registrado') ?>" readonly>
                </div>
            </div>
        </div>

        <!-- RESUMEN DEL PERFIL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">
                Resumen del Perfil
            </h4>
            <p class="text-muted mb-0">
                <?= !empty($candidato['resumen']) ? nl2br(htmlspecialchars($candidato['resumen'])) : 'Aún no has escrito un resumen de tu perfil. Agrégalo desde "Editar Perfil".' ?>
            </p>
        </div>

        <!-- OBJETIVOS PROFESIONALES -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">
                Objetivos Profesionales
            </h4>
            <?php if (empty($listaObjetivos)): ?>
                <p class="text-muted mb-0">Aún no has definido tus objetivos profesionales.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($listaObjetivos as $obj): ?>
                        <li class="list-group-item">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?= htmlspecialchars($obj) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- PREFERENCIAS DE LA CUENTA -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Preferencias de la Cuenta
            </h4>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" <?= $candidato['ofertas_empleo'] ? 'checked' : '' ?> disabled>
                <label class="form-check-label">
                    Recibir ofertas de empleo por correo.
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" <?= $candidato['notificaciones_sistema'] ? 'checked' : '' ?> disabled>
                <label class="form-check-label">
                    Recibir notificaciones del sistema.
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" <?= $candidato['perfil_publico'] ? 'checked' : '' ?> disabled>
                <label class="form-check-label">
                    Mostrar mi perfil públicamente para reclutadores.
                </label>
            </div>
        </div>

        <!-- ESTADO DE LA CUENTA -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-4">
                Estado de la Cuenta
            </h4>
            <div class="alert alert-info d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    Tu perfil está completo en un <strong><?= $porcentajePerfil ?>%</strong>.
                    Mantén tu información actualizada para aumentar tus
                    oportunidades de ser contactado por los reclutadores.
                </div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
            <div>
                <a href="cv.php" class="btn btn-outline-primary me-2">
                    <i class="bi bi-file-earmark-person-fill me-2"></i>
                    Ver CV
                </a>
                <a href="editar_perfil.php" class="btn btn-candidato">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar Perfil
                </a>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
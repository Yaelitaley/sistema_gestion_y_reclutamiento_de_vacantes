<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 4) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT c.id, c.nombre_completo, c.puesto_deseado, c.cv_path
                         FROM candidatos c WHERE c.usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$candidato = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$candidato) {
    die('No se encontró el perfil del candidato asociado a este usuario.');
}
$candidatoId = (int) $candidato['id'];

// ---------- CATEGORÍAS CON VACANTES ACTIVAS ----------
$categorias = $conn->query("SELECT categoria, COUNT(*) AS total FROM vacantes WHERE activa = 1 AND categoria IS NOT NULL AND categoria <> '' GROUP BY categoria ORDER BY total DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// ---------- EMPLEOS RECOMENDADOS (últimas vacantes activas) ----------
$stmt = $conn->prepare("SELECT v.id, v.trabajo, v.ubicacion, v.salario, v.modalidad, e.nombre AS empresa
                         FROM vacantes v
                         INNER JOIN reclutadores r ON v.reclutador_id = r.id
                         INNER JOIN empresas e ON r.empresa_id = e.id
                         WHERE v.activa = 1
                         AND v.id NOT IN (SELECT vacante_id FROM postulaciones WHERE candidato_id = ?)
                         ORDER BY v.created_at DESC
                         LIMIT 4");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$recomendados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- ESTADÍSTICAS ----------
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM postulaciones WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$totalPostulaciones = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM entrevistas e
                         INNER JOIN postulaciones p ON e.postulacion_id = p.id
                         WHERE p.candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$totalEntrevistas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM vacantes_guardadas WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$totalGuardadas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM empresas_seguidas WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$totalSeguidas = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$iconos = ['bi-code-slash text-primary', 'bi-palette-fill text-danger', 'bi-megaphone-fill text-warning', 'bi-graph-up-arrow text-success', 'bi-bank text-info', 'bi-grid-fill text-secondary'];

include "includes/header.php";
?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>

        <!-- ALERTA: NO TIENE CV CARGADO -->
        <?php if (empty($candidato['cv_path'])): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                No tienes un CV ingresado. Sube tu currículum en formato PDF para que los reclutadores puedan revisarlo.
                <a href="editar_cv.php" class="alert-link">Subir CV ahora</a>.
            </div>
        </div>
        <?php endif; ?>

        <!-- BANNER PRINCIPAL -->
        <div class="dashboard-carde hero-banner mb-5">
            <div class="row g-0 align-items-center">
                <div class="col-lg-7">
                    <div class="p-5">
                        <span class="badge bg-success mb-3">
                            Nuevas Vacantes
                        </span>
                        <h2 class="fw-bold mb-3">
                            Tu próximo empleo te está esperando.
                        </h2>
                        <p class="texto opacity-75 mb-4">
                            Descubre cientos de oportunidades laborales
                            creadas especialmente para tu perfil profesional.
                        </p>
                        <a
                            href="../candidatos/explorar-empleos.php"
                            class="btn btn-candidato btn-lg">
                            <i class="bi bi-arrow-right-circle-fill"></i>
                            Explorar Empleos
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <img
                        src="../assets/img/imagencandidato.png"
                        class="img-fluid"
                        style="max-height:320px;"
                        alt="Banner">
                </div>
            </div>
        </div>
        <!-- CATEGORÍAS POPULARES -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">
                Categorías Populares
            </h4>
            <a
                href="../candidatos/explorar-empleos.php"
                class="btn btn-candidato">
                Ver todas
            </a>
        </div>
        <div class="row g-3 mb-5">
            <?php if (empty($categorias)): ?>
            <p class="text-muted">Aún no hay vacantes con categoría asignada.</p>
            <?php else: foreach ($categorias as $i => $cat): ?>
            <div class="col-lg-2 col-md-4">
                <a href="explorar-empleos.php?categoria=<?= urlencode($cat['categoria']) ?>" class="text-decoration-none">
                    <div class="dashboard-card text-center">
                        <i class="bi <?= e($iconos[$i % count($iconos)]) ?> fs-1"></i>
                        <h6 class="mt-3 texto">
                            <?= e($cat['categoria']) ?>
                        </h6>
                        <small class="text-muted"><?= (int) $cat['total'] ?> vacantes</small>
                    </div>
                </a>
            </div>
            <?php endforeach; endif; ?>
        </div>
        <!-- EMPLEOS RECOMENDADOS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">
                Empleos recomendados
            </h4>
            <a
                href="explorar-empleos.php"
                class="btn btn-candidato">
                Ver todos
            </a>
        </div>
        <div class="row g-4">
            <?php if (empty($recomendados)): ?>
            <p class="text-muted">No hay vacantes nuevas por ahora. Vuelve pronto.</p>
            <?php else: foreach ($recomendados as $v): ?>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card h-100 p-4">
                    <h5 class="fw-bold">
                        <?= e($v['trabajo']) ?>
                    </h5>
                    <p class="texto opacity-75">
                        <?= e($v['empresa']) ?>
                    </p>
                    <span class="badge bg-primary mb-3">
                        <?= e($v['modalidad'] ?: 'No especificada') ?>
                    </span>
                    <p>
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <?= e($v['ubicacion'] ?: 'No especificada') ?>
                    </p>
                    <p>
                        <i class="bi bi-cash-stack text-success"></i>
                        <?= $v['salario'] ? '$' . number_format((float) $v['salario'], 2) : 'A convenir' ?>
                    </p>
                    <a
                        href="ver-empleo.php?id=<?= (int) $v['id'] ?>"
                        class="btn btn-candidato w-100">
                        Ver Vacante
                    </a>
                </div>
            </div>
            <?php endforeach; endif; ?>
            <!-- PANEL LATERAL -->
            <div class="row mt-5 ">
                <!-- PERFIL -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-carde flex-column text-center p-4">
                        <i class="bi bi-person-fill me-3 fs-4 "></i>
                        <h4 class="fw-bold">
                            <?= e($candidato['nombre_completo']) ?>
                        </h4>
                        <p class="texto opacity-75">
                            <?= e($candidato['puesto_deseado'] ?: 'Sin puesto deseado definido') ?>
                        </p>
                        <span class="badge <?= $candidato['cv_path'] ? 'bg-success' : 'bg-warning text-dark' ?> mb-3">
                            <?= $candidato['cv_path'] ? 'Perfil con CV' : 'Falta subir CV' ?>
                        </span>
                        <a
                            href="perfil.php"
                            class="btn btn-candidato w-100">
                            <i class="bi bi-person-fill me-2"></i>
                            Ver Perfil
                        </a>
                    </div>
                </div>
                <!-- CURRICULUM -->
                <div class="col-lg-4 mb-4 ">
                    <div class="dashboard-carde flex-column p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-file-earmark-person-fill me-2"></i>
                            Mi Currículum
                        </h5>
                        <p class="texto opacity-75">
                            Mantén actualizado tu CV para aumentar tus oportunidades laborales.
                        </p>
                        <div class="d-grid gap-2 mt-3">
                            <input
    type="file"
    id="archivoCV"
    accept=".pdf"
    hidden>
<button
    class="btn btn-candidato"
    id="btnSubirCV">
    <i class="bi bi-upload me-2"></i>
    Subir CV
</button>
                                <a href="cv.php" class="btn btn-candidato">
    <i class="bi bi-file-earmark-person-fill me-2"></i>
    Visualizar CV
</a>
                        </div>
                    </div>
                </div>
                <!-- ESTADISTICAS -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-carde flex-column p-4">
                        <h5 class="fw-bold mb-4">
                            Mis Estadísticas
                        </h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                Postulaciones
                            </span>
                            <strong>
                                <?= $totalPostulaciones ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                Entrevistas
                            </span>
                            <strong>
                                <?= $totalEntrevistas ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                Vacantes Guardadas
                            </span>
                            <strong>
                                <?= $totalGuardadas ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>
                                Empresas Seguidas
                            </span>
                            <strong>
                                <?= $totalSeguidas ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ACCIONES RAPIDAS -->
            <div class="table-box mt-4">
                <h4 class="fw-bold mb-4">
                    Acciones Rápidas
                </h4>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a
                            href="explorar-empleos.php"
                            class="btn btn-candidato w-100">
                            <i class="bi bi-briefcase-fill me-2"></i>
                            Buscar Empleos
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a
                            href="perfil.php"
                            class="btn btn-candidato w-100">
                            <i class="bi bi-person-fill me-2"></i>
                            Mi Perfil
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a
                            href="postulaciones.php"
                            class="btn btn-candidato w-100">
                            <i class="bi bi-send-check-fill me-2"></i>
                            Mis Postulaciones
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a
                            href="configuracion.php"
                            class="btn btn-candidato w-100">
                            <i class="bi bi-gear-fill me-2"></i>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
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

$postulacionId = (int) ($_GET['id'] ?? 0);
if ($postulacionId <= 0) {
    redirect_to('candidatos.php');
}

// ---------- CAMBIAR ESTADO (Entrevista / Contratado / Rechazado) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_estado') {
    $nuevoEstadoId = (int) ($_POST['estado_id'] ?? 0);
    if (in_array($nuevoEstadoId, [1, 2, 3, 4, 5], true)) {
        $stmt = $conn->prepare("UPDATE postulaciones p
                                 INNER JOIN vacantes v ON p.vacante_id = v.id
                                 SET p.estado_id = ?
                                 WHERE p.id = ? AND v.reclutador_id = ?");
        $stmt->bind_param('iii', $nuevoEstadoId, $postulacionId, $reclutadorId);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO historial_estados_postulacion (postulacion_id, estado_id, fecha_cambio) VALUES (?, ?, NOW())");
        $stmt->bind_param('ii', $postulacionId, $nuevoEstadoId);
        $stmt->execute();
        $stmt->close();

        redirect_to('ver_candidatos.php?id=' . $postulacionId . '&type=success&msg=' . urlencode('Estado actualizado correctamente.'));
    }
}

// ---------- DATOS DEL CANDIDATO Y LA POSTULACIÓN ----------
$stmt = $conn->prepare("SELECT p.id AS postulacion_id, p.estado_id, p.fecha_postulacion,
                                c.id AS candidato_id, c.nombre_completo, c.correo, c.telefono, c.ubicacion,
                                c.fecha_nacimiento, c.nacionalidad, c.puesto_deseado, c.salario_esperado,
                                c.disponibilidad, c.modalidad, c.cv_path, c.resumen,
                                v.trabajo AS vacante, ep.nombre AS estado
                         FROM postulaciones p
                         INNER JOIN vacantes v ON p.vacante_id = v.id
                         INNER JOIN candidatos c ON p.candidato_id = c.id
                         INNER JOIN estados_postulacion ep ON p.estado_id = ep.id
                         WHERE p.id = ? AND v.reclutador_id = ?");
$stmt->bind_param('ii', $postulacionId, $reclutadorId);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$datos) {
    die('No se encontró la postulación solicitada.');
}
$candidatoId = (int) $datos['candidato_id'];

// ---------- EXPERIENCIA ----------
$stmt = $conn->prepare("SELECT empresa, puesto, fecha_inicio, fecha_fin, descripcion FROM candidato_experiencia WHERE candidato_id = ? ORDER BY fecha_inicio DESC");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$experiencia = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- FORMACIÓN ----------
$stmt = $conn->prepare("SELECT institucion, carrera, fecha_inicio, fecha_fin FROM candidato_formacion WHERE candidato_id = ? ORDER BY fecha_inicio DESC");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$formacion = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- HABILIDADES ----------
$stmt = $conn->prepare("SELECT habilidad, nivel FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$habilidades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- IDIOMAS ----------
$stmt = $conn->prepare("SELECT idioma, nivel FROM candidato_idiomas WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$idiomas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- CERTIFICACIONES ----------
$stmt = $conn->prepare("SELECT descripcion FROM candidato_certificaciones WHERE candidato_id = ?");
$stmt->bind_param('i', $candidatoId);
$stmt->execute();
$certificaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- LÍNEA DE TIEMPO DEL PROCESO ----------
$stmt = $conn->prepare("SELECT ep.nombre, h.fecha_cambio FROM historial_estados_postulacion h
                         INNER JOIN estados_postulacion ep ON h.estado_id = ep.id
                         WHERE h.postulacion_id = ? ORDER BY h.fecha_cambio ASC");
$stmt->bind_param('i', $postulacionId);
$stmt->execute();
$historial = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$edad = $datos['fecha_nacimiento'] ? floor((time() - strtotime($datos['fecha_nacimiento'])) / 31557600) : null;

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

$pasos = ['Postulado', 'En revisión', 'Entrevista', 'Contratado'];

include "includes/header.php";
?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>
        <!-- TITULO -->
        <div class="mb-4">
            <h2 class="fw-bold">
                Perfil del Candidato
            </h2>
            <p class="text-muted">
                Consulta la información completa del candidato y administra
                su proceso de selección.
            </p>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <!-- PERFIL -->
        <div class="candidate-profile mb-4">
            <div class="row align-items-center">
                <!-- FOTO -->
                <div class="col-lg-3 text-center">
                    <img
                        src="../assets/img/candidato02.png"
                        class="candidate-photo img-fluid rounded-circle"
                        alt="Candidato">
                </div>
                <!-- INFORMACION -->
                <div class="col-lg-6">
                    <h2 class="fw-bold">
                        <?= e($datos['nombre_completo']) ?>
                    </h2>
                    <h5 class="text-primary mb-4">
                        <?= e($datos['puesto_deseado'] ?: 'Puesto deseado no especificado') ?>
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            <?= e($datos['correo']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-telephone-fill text-success me-2"></i>
                            <?= e($datos['telefono'] ?: 'No registrado') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <?= e($datos['ubicacion'] ?: 'No registrada') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-calendar-check-fill text-warning me-2"></i>
                            <?= e($datos['disponibilidad'] ?: 'No especificada') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-cash-stack text-success me-2"></i>
                            <?= $datos['salario_esperado'] ? '$' . number_format((float) $datos['salario_esperado'], 2) : 'No especificado' ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="bi bi-briefcase-fill text-secondary me-2"></i>
                            <?= e($datos['modalidad'] ?: 'No especificada') ?>
                        </div>
                    </div>
                </div>
                <!-- ESTADO -->
                <div class="col-lg-3">
                    <div class="status-card">
                        <h5 class="fw-bold mb-4">
                            Estado del proceso
                        </h5>
                        <div class="mb-3">
                            <?= badge_estado($datos['estado']) ?>
                        </div>
                        <hr>
                        <div class="timeline">
                            <?php foreach ($pasos as $paso):
                                $completado = in_array($paso, array_column($historial, 'nombre'), true);
                                $activo = $datos['estado'] === $paso;
                                $clase = $completado ? 'completed' : ($activo ? 'active' : '');
                                $icono = $completado ? 'bi-check-circle-fill' : ($activo ? 'bi-clock-fill' : 'bi-circle');
                            ?>
                            <div class="timeline-item <?= e($clase) ?>">
                                <i class="bi <?= e($icono) ?>"></i>
                                <?= e($paso) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <form method="POST" class="d-grid gap-2">
                            <input type="hidden" name="accion" value="cambiar_estado">
                            <button type="submit" name="estado_id" value="3" class="btn btn-success">
                                <i class="bi bi-calendar-check-fill me-2"></i>
                                Pasar a Entrevista
                            </button>
                            <button type="submit" name="estado_id" value="5" class="btn btn-primary">
                                <i class="bi bi-person-check-fill me-2"></i>
                                Contratar
                            </button>
                            <div class="d-flex gap-2">
                                <a href="candidatos.php" class="btn btn-secondary flex-fill">
                                    <i class="bi bi-arrow-left-circle-fill me-2"></i>
                                    Regresar
                                </a>
                                <button type="submit" name="estado_id" value="4" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Rechazar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- INFORMACION -->
        <div class="row g-4">
            <!-- INFORMACION PERSONAL -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-person-fill me-2"></i>
                        Información Personal
                    </h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%">Nombre</th>
                                <td><?= e($datos['nombre_completo']) ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de nacimiento</th>
                                <td><?= $datos['fecha_nacimiento'] ? e(date('d/m/Y', strtotime($datos['fecha_nacimiento']))) : 'No registrada' ?></td>
                            </tr>
                            <tr>
                                <th>Edad</th>
                                <td><?= $edad !== null ? (int) $edad . ' años' : 'No disponible' ?></td>
                            </tr>
                            <tr>
                                <th>Nacionalidad</th>
                                <td><?= e($datos['nacionalidad'] ?: 'No registrada') ?></td>
                            </tr>
                            <tr>
                                <th>Ubicación</th>
                                <td><?= e($datos['ubicacion'] ?: 'No registrada') ?></td>
                            </tr>
                            <tr>
                                <th>Resumen</th>
                                <td><?= nl2br(e($datos['resumen'] ?: 'Sin resumen registrado')) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- EXPERIENCIA -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-briefcase-fill me-2"></i>
                        Experiencia Laboral
                    </h4>
                    <?php if (empty($experiencia)): ?>
                        <p class="text-muted">Sin experiencia registrada.</p>
                    <?php else: foreach ($experiencia as $exp): ?>
                        <h5 class="mt-2"><?= e($exp['puesto']) ?></h5>
                        <p class="text-primary mb-1"><?= e($exp['empresa']) ?></p>
                        <small class="text-muted">
                            <?= e($exp['fecha_inicio'] ? date('M Y', strtotime($exp['fecha_inicio'])) : '') ?>
                            -
                            <?= e($exp['fecha_fin'] ? date('M Y', strtotime($exp['fecha_fin'])) : 'Actualidad') ?>
                        </small>
                        <p class="mt-2"><?= nl2br(e($exp['descripcion'])) ?></p>
                        <hr>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <!-- FORMACIÓN -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-mortarboard-fill me-2"></i>
                        Formación Académica
                    </h4>
                    <?php if (empty($formacion)): ?>
                        <p class="text-muted">Sin formación registrada.</p>
                    <?php else: foreach ($formacion as $f): ?>
                        <h6 class="fw-bold mt-2"><?= e($f['carrera']) ?></h6>
                        <p class="text-primary mb-1"><?= e($f['institucion']) ?></p>
                        <small class="text-muted">
                            <?= e($f['fecha_inicio'] ? date('Y', strtotime($f['fecha_inicio'])) : '') ?>
                            -
                            <?= e($f['fecha_fin'] ? date('Y', strtotime($f['fecha_fin'])) : 'Actualidad') ?>
                        </small>
                        <hr>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <!-- CERTIFICACIONES -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-patch-check-fill me-2"></i>
                        Certificaciones
                    </h4>
                    <?php if (empty($certificaciones)): ?>
                        <p class="text-muted">Sin certificaciones registradas.</p>
                    <?php else: ?>
                        <ul>
                        <?php foreach ($certificaciones as $c): ?>
                            <li><?= e($c['descripcion']) ?></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            <!-- HABILIDADES -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-stars me-2"></i>
                        Habilidades
                    </h4>
                    <?php if (empty($habilidades)): ?>
                        <p class="text-muted">Sin habilidades registradas.</p>
                    <?php else: foreach ($habilidades as $h): ?>
                        <span class="badge bg-primary m-1 p-2"><?= e($h['habilidad']) ?><?= $h['nivel'] ? ' · ' . e($h['nivel']) : '' ?></span>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <!-- IDIOMAS -->
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bi bi-translate me-2"></i>
                        Idiomas
                    </h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Idioma</th>
                                <th>Nivel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($idiomas)): ?>
                            <tr><td colspan="2" class="text-muted">Sin idiomas registrados.</td></tr>
                            <?php else: foreach ($idiomas as $idm): ?>
                            <tr>
                                <td><?= e($idm['idioma']) ?></td>
                                <td><?= e($idm['nivel']) ?></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- CV -->
            <div class="col-lg-12">
                <div class="candidate-card ">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>
                            Currículum Vitae
                        </h4>
                        <?php if (!empty($datos['cv_path'])): ?>
                        <a href="../<?= e($datos['cv_path']) ?>" download class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i>
                            Descargar PDF
                        </a>
                        <a href="../<?= e($datos['cv_path']) ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>
                            Ver CV
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="cv-preview col-lg-12">
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-pdf-fill text-danger"
                               style="font-size:90px;"></i>
                            <h5 class="mt-4">
                                <?= !empty($datos['cv_path']) ? e(basename($datos['cv_path'])) : 'El candidato aún no ha subido su CV' ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

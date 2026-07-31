<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare(
    "SELECT id, nombre_completo, correo, telefono, ubicacion, fecha_nacimiento, cv_path,
            perfil_profesional, objetivo_profesional, aptitudes, disponibilidad, modalidad,
            puesto_deseado
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

// ----- Formación académica -----
$stmt = $conn->prepare("SELECT institucion, carrera, fecha_inicio, fecha_fin FROM candidato_formacion WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$formacion = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Experiencia laboral -----
$stmt = $conn->prepare("SELECT empresa, puesto, fecha_inicio, fecha_fin, descripcion FROM candidato_experiencia WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$experiencia = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Habilidades técnicas -----
$stmt = $conn->prepare("SELECT habilidad, nivel FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$habilidades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Idiomas -----
$stmt = $conn->prepare("SELECT idioma, nivel FROM candidato_idiomas WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$idiomas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Certificaciones -----
$stmt = $conn->prepare("SELECT descripcion FROM candidato_certificaciones WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$certificaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Edad (si hay fecha de nacimiento) -----
$edad = null;
if (!empty($candidato['fecha_nacimiento'])) {
    $nacimiento = new DateTime($candidato['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;
}

// ----- Aptitudes como lista (una por línea o separadas por coma) -----
$listaAptitudes = [];
if (!empty($candidato['aptitudes'])) {
    $listaAptitudes = array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $candidato['aptitudes'])));
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">
                    Mi Currículum
                </h2>
                <p class="text-muted">
                    Visualiza y administra la información de tu currículum.
                </p>
            </div>
        </div>

        <!-- INFORMACIÓN PERSONAL -->
        <div class="table-box mb-4">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center">
                    <img src="../assets/img/candidato.png" class="rounded-circle img-fluid" style="width:140px; height:140px; object-fit:cover;">
                </div>
                <div class="col-lg-10">
                    <h3 class="fw-bold">
                        <?= htmlspecialchars($candidato['nombre_completo']) ?>
                    </h3>
                    <h5 class="text-success">
                        <?= htmlspecialchars($candidato['puesto_deseado'] ?: 'Puesto deseado no definido') ?>
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-envelope-fill me-2 text-primary"></i>
                                <?= htmlspecialchars($candidato['correo']) ?>
                            </p>
                            <p>
                                <i class="bi bi-telephone-fill me-2 text-success"></i>
                                <?= htmlspecialchars($candidato['telefono'] ?: 'Sin teléfono registrado') ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                <?= htmlspecialchars($candidato['ubicacion'] ?: 'Sin ubicación registrada') ?>
                            </p>
                            <p>
                                <i class="bi bi-calendar-fill me-2 text-warning"></i>
                                <?= $edad !== null ? $edad . ' años' : 'Fecha de nacimiento no registrada' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERFIL PROFESIONAL -->
        <?php if (!empty($candidato['perfil_profesional'])): ?>
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">
                Perfil Profesional
            </h4>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($candidato['perfil_profesional'])) ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- FORMACIÓN ACADÉMICA -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Formación Académica
            </h4>
            <?php if (empty($formacion)): ?>
                <p class="text-muted mb-0">Aún no has agregado tu formación académica.</p>
            <?php else: ?>
                <?php foreach ($formacion as $i => $f): ?>
                    <div class="border-start border-4 border-success ps-3 <?= $i < count($formacion) - 1 ? 'mb-4' : '' ?>">
                        <h5 class="fw-bold">
                            <?= htmlspecialchars($f['carrera']) ?>
                        </h5>
                        <p class="text-muted mb-1">
                            <?= htmlspecialchars($f['institucion']) ?>
                        </p>
                        <small class="text-secondary">
                            <?= htmlspecialchars($f['fecha_inicio']) ?> - <?= htmlspecialchars($f['fecha_fin']) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- EXPERIENCIA LABORAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Experiencia Laboral
            </h4>
            <?php if (empty($experiencia)): ?>
                <p class="text-muted mb-0">Aún no has agregado experiencia laboral.</p>
            <?php else: ?>
                <?php foreach ($experiencia as $i => $ex): ?>
                    <div class="border-start border-4 border-warning ps-3 <?= $i < count($experiencia) - 1 ? 'mb-4' : '' ?>">
                        <h5 class="fw-bold">
                            <?= htmlspecialchars($ex['puesto']) ?>
                        </h5>
                        <p class="text-muted mb-1">
                            <?= htmlspecialchars($ex['empresa']) ?>
                        </p>
                        <small class="text-secondary">
                            <?= htmlspecialchars($ex['fecha_inicio']) ?> - <?= htmlspecialchars($ex['fecha_fin']) ?>
                        </small>
                        <?php if (!empty($ex['descripcion'])): ?>
                            <p class="mt-3">
                                <?= nl2br(htmlspecialchars($ex['descripcion'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- HABILIDADES -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Habilidades Técnicas
            </h4>
            <?php if (empty($habilidades)): ?>
                <p class="text-muted mb-0">Aún no has agregado habilidades técnicas.</p>
            <?php else: ?>
                <?php foreach ($habilidades as $h): ?>
                    <span class="badge bg-primary me-2 mb-2">
                        <?= htmlspecialchars($h['habilidad']) ?><?= !empty($h['nivel']) ? ' — ' . htmlspecialchars($h['nivel']) : '' ?>
                    </span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- IDIOMAS -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Idiomas
            </h4>
            <?php if (empty($idiomas)): ?>
                <p class="text-muted mb-0">Aún no has agregado idiomas.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Idioma</th>
                            <th>Nivel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($idiomas as $idi): ?>
                            <tr>
                                <td><?= htmlspecialchars($idi['idioma']) ?></td>
                                <td><?= htmlspecialchars($idi['nivel'] ?: 'No especificado') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- CERTIFICACIONES -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-4">
                Cursos y Certificaciones
            </h4>
            <?php if (empty($certificaciones)): ?>
                <p class="text-muted mb-0">Aún no has agregado cursos ni certificaciones.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($certificaciones as $c): ?>
                        <li class="list-group-item">
                            <i class="bi bi-patch-check-fill text-success me-2"></i>
                            <?= htmlspecialchars($c['descripcion']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- APTITUDES -->
        <?php if (!empty($listaAptitudes)): ?>
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Aptitudes Profesionales
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($listaAptitudes as $i => $apt): ?>
                            <?php if ($i % 2 === 0): ?>
                                <li class="list-group-item">✔ <?= htmlspecialchars($apt) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($listaAptitudes as $i => $apt): ?>
                            <?php if ($i % 2 === 1): ?>
                                <li class="list-group-item">✔ <?= htmlspecialchars($apt) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Información Adicional
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Disponibilidad:</strong>
                        <?= htmlspecialchars($candidato['disponibilidad'] ?: 'No especificada') ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Modalidad preferida:</strong>
                        <?= htmlspecialchars($candidato['modalidad'] ?: 'No especificada') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- OBJETIVO PROFESIONAL -->
        <?php if (!empty($candidato['objetivo_profesional'])): ?>
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-3">
                Objetivo Profesional
            </h4>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($candidato['objetivo_profesional'])) ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- BOTONES -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
            <div>
                <a href="editar_cv.php" class="btn btn-candidato me-2">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar CV
                </a>
                <?php if (!empty($candidato['cv_path'])): ?>
                    <a href="../<?= htmlspecialchars($candidato['cv_path']) ?>" id="btnDescargarCV" class="btn btn-outline-primary" target="_blank" download>
                        <i class="bi bi-download me-2"></i>
                        Descargar PDF
                    </a>
                <?php else: ?>
                    <button type="button" id="btnDescargarCV" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i>
                        Descargar PDF
                    </button>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
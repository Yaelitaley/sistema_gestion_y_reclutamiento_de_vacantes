<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare(
    "SELECT id, nombre_completo, correo, telefono, ubicacion, cv_path,
            perfil_profesional, objetivo_profesional, aptitudes,
            disponibilidad, modalidad, linkedin, portafolio
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

// ----- Habilidades técnicas (por nombre exacto, para precargar los 6 campos fijos) -----
$stmt = $conn->prepare("SELECT habilidad, nivel FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$habilidadesRes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$nivelesHabilidad = [
    'HTML' => '', 'CSS' => '', 'Bootstrap' => '', 'JavaScript' => '', 'PHP' => '', 'MySQL' => '',
];
foreach ($habilidadesRes as $h) {
    if (array_key_exists($h['habilidad'], $nivelesHabilidad)) {
        $nivelesHabilidad[$h['habilidad']] = $h['nivel'] ?? '';
    }
}

// ----- Formación académica (se edita solo el primer registro, igual que guarda la acción) -----
$stmt = $conn->prepare("SELECT institucion, carrera, fecha_inicio, fecha_fin FROM candidato_formacion WHERE candidato_id = ? ORDER BY id ASC LIMIT 1");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$formacion = $stmt->get_result()->fetch_assoc() ?: ['institucion' => '', 'carrera' => '', 'fecha_inicio' => '', 'fecha_fin' => ''];
$stmt->close();

// ----- Experiencia laboral (primer registro) -----
$stmt = $conn->prepare("SELECT empresa, puesto, fecha_inicio, fecha_fin, descripcion FROM candidato_experiencia WHERE candidato_id = ? ORDER BY id ASC LIMIT 1");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$experiencia = $stmt->get_result()->fetch_assoc() ?: ['empresa' => '', 'puesto' => '', 'fecha_inicio' => '', 'fecha_fin' => '', 'descripcion' => ''];
$stmt->close();

// ----- Idiomas (hasta 2, igual que guarda la acción) -----
$stmt = $conn->prepare("SELECT idioma, nivel FROM candidato_idiomas WHERE candidato_id = ? ORDER BY id ASC LIMIT 2");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$idiomasRes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$idioma1 = $idiomasRes[0] ?? ['idioma' => '', 'nivel' => ''];
$idioma2 = $idiomasRes[1] ?? ['idioma' => '', 'nivel' => ''];

// ----- Certificaciones (una por línea) -----
$stmt = $conn->prepare("SELECT descripcion FROM candidato_certificaciones WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$certRes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$certificacionesTexto = implode("\n", array_column($certRes, 'descripcion'));

$nivelesIdioma = ['Nativo', 'Avanzado', 'Intermedio', 'Básico'];
?>
<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">
                    Editar Currículum
                </h2>
                <p class="text-muted">
                    Actualiza la información de tu perfil profesional.
                </p>
            </div>
        </div>

        <!-- Formulario conectado a actions/guardar_cv.php vía fetch en candidato.js -->
        <form id="formCV" action="actions/guardar_cv.php" method="POST" enctype="multipart/form-data">

            <!-- DATOS PERSONALES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Datos Personales
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre_completo" value="<?= htmlspecialchars($candidato['nombre_completo']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($candidato['correo']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($candidato['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="ciudad" value="<?= htmlspecialchars($candidato['ubicacion'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- PERFIL PROFESIONAL -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Perfil Profesional
                </h4>
                <textarea class="form-control" name="perfil_profesional" rows="6"><?= htmlspecialchars($candidato['perfil_profesional'] ?? '') ?></textarea>
            </div>

            <!-- FORMACIÓN ACADÉMICA -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Formación Académica
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Institución</label>
                        <input type="text" class="form-control" name="institucion" value="<?= htmlspecialchars($formacion['institucion']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Carrera</label>
                        <input type="text" class="form-control" name="carrera" value="<?= htmlspecialchars($formacion['carrera']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="text" class="form-control" name="inicio_formacion" value="<?= htmlspecialchars($formacion['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="text" class="form-control" name="fin_formacion" value="<?= htmlspecialchars($formacion['fecha_fin'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- EXPERIENCIA LABORAL -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Experiencia Laboral
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Empresa / Proyecto</label>
                        <input type="text" class="form-control" name="empresa" value="<?= htmlspecialchars($experiencia['empresa']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Puesto</label>
                        <input type="text" class="form-control" name="puesto" value="<?= htmlspecialchars($experiencia['puesto']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="text" class="form-control" name="inicio_experiencia" value="<?= htmlspecialchars($experiencia['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="text" class="form-control" name="fin_experiencia" value="<?= htmlspecialchars($experiencia['fecha_fin'] ?? '') ?>">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion_experiencia" rows="5"><?= htmlspecialchars($experiencia['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- HABILIDADES TÉCNICAS -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Habilidades Técnicas
                </h4>
                <p class="text-muted small">Escribe el nivel como texto libre, por ejemplo "Avanzado (95%)" o simplemente "Intermedio". Deja vacío si no aplica.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">HTML</label>
                        <input type="text" class="form-control" name="html_nivel" value="<?= htmlspecialchars($nivelesHabilidad['HTML']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CSS</label>
                        <input type="text" class="form-control" name="css_nivel" value="<?= htmlspecialchars($nivelesHabilidad['CSS']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bootstrap</label>
                        <input type="text" class="form-control" name="bootstrap_nivel" value="<?= htmlspecialchars($nivelesHabilidad['Bootstrap']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">JavaScript</label>
                        <input type="text" class="form-control" name="js_nivel" value="<?= htmlspecialchars($nivelesHabilidad['JavaScript']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">PHP</label>
                        <input type="text" class="form-control" name="php_nivel" value="<?= htmlspecialchars($nivelesHabilidad['PHP']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">MySQL</label>
                        <input type="text" class="form-control" name="mysql_nivel" value="<?= htmlspecialchars($nivelesHabilidad['MySQL']) ?>">
                    </div>
                </div>
            </div>

            <!-- APTITUDES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Aptitudes Profesionales
                </h4>
                <label class="form-label">Escribe tus aptitudes (separadas por comas o una por línea)</label>
                <textarea class="form-control" name="aptitudes" rows="4"><?= htmlspecialchars($candidato['aptitudes'] ?? '') ?></textarea>
            </div>

            <!-- IDIOMAS -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Idiomas
                </h4>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Idioma 1</label>
                        <input type="text" class="form-control" name="idioma1" value="<?= htmlspecialchars($idioma1['idioma']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nivel</label>
                        <select class="form-select" name="nivel1">
                            <option value="">Selecciona un nivel</option>
                            <?php foreach ($nivelesIdioma as $niv): ?>
                                <option value="<?= $niv ?>" <?= $idioma1['nivel'] === $niv ? 'selected' : '' ?>><?= $niv ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Idioma 2</label>
                        <input type="text" class="form-control" name="idioma2" value="<?= htmlspecialchars($idioma2['idioma']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nivel</label>
                        <select class="form-select" name="nivel2">
                            <option value="">Selecciona un nivel</option>
                            <?php foreach ($nivelesIdioma as $niv): ?>
                                <option value="<?= $niv ?>" <?= $idioma2['nivel'] === $niv ? 'selected' : '' ?>><?= $niv ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CERTIFICACIONES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Certificaciones
                </h4>
                <label class="form-label">Una certificación por línea</label>
                <textarea class="form-control" name="certificaciones" rows="4"><?= htmlspecialchars($certificacionesTexto) ?></textarea>
            </div>

            <!-- OBJETIVO PROFESIONAL -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Objetivo Profesional
                </h4>
                <textarea class="form-control" name="objetivo_profesional" rows="5"><?= htmlspecialchars($candidato['objetivo_profesional'] ?? '') ?></textarea>
            </div>

            <!-- INFORMACIÓN ADICIONAL -->
            <div class="table-box mb-5">
                <h4 class="fw-bold mb-4">
                    Información Adicional
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Disponibilidad</label>
                        <select class="form-select" name="disponibilidad">
                            <option value="">Selecciona una opción</option>
                            <?php foreach (['Tiempo Completo', 'Medio Tiempo', 'Freelance'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $candidato['disponibilidad'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Modalidad Preferida</label>
                        <select class="form-select" name="modalidad">
                            <option value="">Selecciona una opción</option>
                            <?php foreach (['Híbrido', 'Presencial', 'Remoto'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $candidato['modalidad'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin" value="<?= htmlspecialchars($candidato['linkedin'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Portafolio</label>
                        <input type="url" class="form-control" name="portafolio" value="<?= htmlspecialchars($candidato['portafolio'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- SUBIR CV -->
            <div class="table-box mb-5">
                <h4 class="fw-bold mb-4">
                    Archivo del Currículum
                </h4>
                <?php if (!empty($candidato['cv_path'])): ?>
                    <p class="mb-2">
                        <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>
                        Ya tienes un CV cargado. Puedes <a href="../<?= htmlspecialchars($candidato['cv_path']) ?>" target="_blank">verlo aquí</a>.
                        Sube otro archivo abajo solo si quieres reemplazarlo.
                    </p>
                <?php endif; ?>
                <label class="form-label">
                    Selecciona tu CV en formato PDF
                </label>
                <input type="file" class="form-control" name="archivo_cv" accept=".pdf">
                <small class="text-muted">
                    Formato permitido: PDF (Máximo 5 MB).
                </small>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
                <a href="cv.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </a>
                <div>
                    <button type="reset" id="btnRestablecerCV" class="btn btn-outline-danger me-2">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        Restablecer
                    </button>
                    <button type="submit" id="btnGuardarCV" class="btn btn-success">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
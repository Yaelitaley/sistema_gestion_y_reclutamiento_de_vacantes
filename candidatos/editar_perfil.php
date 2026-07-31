<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

// Candidato en sesión
$stmt = $conn->prepare(
    "SELECT id, nombre_completo, correo, telefono, fecha_nacimiento, nacionalidad, ubicacion, genero,
            puesto_deseado, salario_esperado, disponibilidad, modalidad,
            linkedin, github, portafolio, resumen, objetivos,
            ofertas_empleo, notificaciones_sistema, perfil_publico
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

// ----- Habilidades (como texto separado por comas, para el campo de edición rápida) -----
$stmt = $conn->prepare("SELECT habilidad FROM candidato_habilidades WHERE candidato_id = ?");
$stmt->bind_param('i', $candidato_id);
$stmt->execute();
$habilidadesRes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$habilidadesTexto = implode(', ', array_column($habilidadesRes, 'habilidad'));

// ----- Separar nombre_completo en nombre / apellidos para los dos campos del formulario -----
$partesNombre = explode(' ', trim($candidato['nombre_completo']), 2);
$nombre    = $partesNombre[0] ?? '';
$apellidos = $partesNombre[1] ?? '';

$fechaNacValue = !empty($candidato['fecha_nacimiento']) ? date('Y-m-d', strtotime($candidato['fecha_nacimiento'])) : '';
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
                    Editar Perfil
                </h2>
                <p class="text-muted">
                    Actualiza la información de tu cuenta y tus datos personales.
                </p>
            </div>
        </div>

        <!-- Formulario conectado a actions/actualizar_perfil.php vía fetch en candidato.js -->
        <form id="formPerfil" action="actions/actualizar_perfil.php" method="POST" enctype="multipart/form-data">

            <!-- FOTO -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Foto de Perfil
                </h4>
                <div class="row align-items-center">
                    <div class="col-lg-3 text-center">
                        <img src="../assets/img/candidato.png" class="rounded-circle img-fluid mb-3" style="width:160px;height:160px;object-fit:cover;">
                    </div>
                    <div class="col-lg-9">
                        <label class="form-label">
                            Selecciona una nueva fotografía
                        </label>
                        <input type="file" class="form-control" name="foto" accept="image/*" disabled>
                        <small class="text-muted">
                            Próximamente disponible. Formatos permitidos: JPG, PNG o JPEG.
                        </small>
                    </div>
                </div>
            </div>

            <!-- DATOS PERSONALES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Datos Personales
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" class="form-control" name="apellidos" value="<?= htmlspecialchars($apellidos) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($candidato['correo']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($candidato['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" value="<?= htmlspecialchars($fechaNacValue) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Género</label>
                        <select class="form-select" name="genero">
                            <option value="">Selecciona una opción</option>
                            <?php foreach (['Masculino', 'Femenino', 'Otro'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $candidato['genero'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nacionalidad</label>
                        <input type="text" class="form-control" name="nacionalidad" value="<?= htmlspecialchars($candidato['nacionalidad'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="ciudad" value="<?= htmlspecialchars($candidato['ubicacion'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN PROFESIONAL -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Información Profesional
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Puesto Deseado</label>
                        <input type="text" class="form-control" name="puesto_deseado" value="<?= htmlspecialchars($candidato['puesto_deseado'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salario Esperado</label>
                        <input type="text" class="form-control" name="salario_esperado" value="<?= htmlspecialchars($candidato['salario_esperado'] ?? '') ?>">
                    </div>
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
                            <?php foreach (['Híbrido', 'Remoto', 'Presencial'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $candidato['modalidad'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Habilidades (separadas por comas)</label>
                        <input type="text" class="form-control" name="habilidades" value="<?= htmlspecialchars($habilidadesTexto) ?>" placeholder="Ej. PHP, MySQL, JavaScript">
                        <small class="text-muted">Reemplaza por completo tu lista de habilidades actual al guardar.</small>
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
                        <input type="url" class="form-control" name="linkedin" value="<?= htmlspecialchars($candidato['linkedin'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">GitHub</label>
                        <input type="url" class="form-control" name="github" value="<?= htmlspecialchars($candidato['github'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Portafolio</label>
                        <input type="url" class="form-control" name="portafolio" value="<?= htmlspecialchars($candidato['portafolio'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- RESUMEN DEL PERFIL -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Resumen del Perfil
                </h4>
                <div class="mb-3">
                    <label class="form-label">Escribe un breve resumen sobre ti</label>
                    <textarea class="form-control" name="resumen" rows="4"><?= htmlspecialchars($candidato['resumen'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- OBJETIVOS PROFESIONALES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Objetivos Profesionales
                </h4>
                <div class="mb-3">
                    <label class="form-label">Tus objetivos (uno por línea)</label>
                    <textarea class="form-control" name="objetivos" rows="4"><?= htmlspecialchars($candidato['objetivos'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- PREFERENCIAS -->
            <div class="table-box mb-5">
                <h4 class="fw-bold mb-4">
                    Preferencias de la Cuenta
                </h4>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="ofertas_empleo" value="1" <?= $candidato['ofertas_empleo'] ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Recibir ofertas de empleo por correo.
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="notificaciones_sistema" value="1" <?= $candidato['notificaciones_sistema'] ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Recibir notificaciones del sistema.
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="perfil_publico" value="1" <?= $candidato['perfil_publico'] ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Mostrar mi perfil públicamente para reclutadores.
                    </label>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
                <!-- REGRESAR -->
                <a href="perfil.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </a>

                <div>
                    <!-- RESTABLECER -->
                    <button type="reset" id="btnRestablecerPerfil" class="btn btn-outline-danger me-2">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        Restablecer
                    </button>

                    <!-- GUARDAR -->
                    <button type="submit" id="btnGuardarPerfil" class="btn btn-success">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
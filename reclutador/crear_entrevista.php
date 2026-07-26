<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';
/*
|--------------------------------------------------------------------------
| Validar sesión
|--------------------------------------------------------------------------
*/
if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}
$usuarioId = (int)$_SESSION['usuario_id'];
/*
|--------------------------------------------------------------------------
| Obtener reclutador
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM reclutadores
    WHERE usuario_id = ?
");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$reclutador = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$reclutador) {
    die("No se encontró el perfil del reclutador.");
}
$reclutadorId = (int)$reclutador['id'];
/*
|--------------------------------------------------------------------------
| Obtener postulaciones del reclutador
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        p.id,
        c.nombre_completo,
        c.correo,
        c.telefono,
        v.trabajo
    FROM postulaciones p
    INNER JOIN candidatos c
        ON c.id = p.candidato_id
    INNER JOIN vacantes v
        ON v.id = p.vacante_id
    WHERE v.reclutador_id = ?
    ORDER BY c.nombre_completo ASC
");
$stmt->bind_param("i", $reclutadorId);
$stmt->execute();
$postulaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
/*
|--------------------------------------------------------------------------
| Guardar entrevista
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postulacionId = (int)($_POST['postulacion_id'] ?? 0);
    $fecha         = trim($_POST['fecha'] ?? '');
    $hora          = trim($_POST['hora'] ?? '');
    $modalidad     = trim($_POST['modalidad'] ?? '');
    $lugar         = trim($_POST['lugar'] ?? '');
    $notas         = trim($_POST['notas'] ?? '');
    $estado        = trim($_POST['estado'] ?? 'Programada');
    if (
        $postulacionId <= 0 ||
        $fecha === '' ||
        $hora === '' ||
        $modalidad === '' ||
        $lugar === ''
    ) {
        redirect_to(
            'crear_entrevista.php?type=danger&msg=' .
            urlencode('Complete todos los campos obligatorios.')
        );
    }
    $fechaCompleta = $fecha . ' ' . $hora . ':00';
    /*
    |--------------------------------------------------------------------------
    | Verificar que la postulación pertenezca al reclutador
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        SELECT p.id
        FROM postulaciones p
        INNER JOIN vacantes v
            ON v.id = p.vacante_id
        WHERE
            p.id = ?
            AND v.reclutador_id = ?
    ");
    $stmt->bind_param("ii", $postulacionId, $reclutadorId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        redirect_to(
            'entrevistas.php?type=danger&msg=' .
            urlencode('La postulación seleccionada no es válida.')
        );
    }
    $stmt->close();
    /*
    |--------------------------------------------------------------------------
    | Insertar entrevista
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO entrevistas
        (
            postulacion_id,
            fecha,
            modalidad,
            lugar,
            notas,
            estado
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?
        )
    ");
    $stmt->bind_param(
        "isssss",
        $postulacionId,
        $fechaCompleta,
        $modalidad,
        $lugar,
        $notas,
        $estado
    );
    if ($stmt->execute()) {
        $stmt->close();
        redirect_to(
            'entrevistas.php?type=success&msg=' .
            urlencode('Entrevista programada correctamente.')
        );
    }
    $stmt->close();
    redirect_to(
        'crear_entrevista.php?type=danger&msg=' .
        urlencode('No fue posible programar la entrevista.')
    );
}
/*
|--------------------------------------------------------------------------
| Mensajes
|--------------------------------------------------------------------------
*/
$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';
?>
<?php include "includes/header.php"; ?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">
                    Programar Entrevista
                </h2>
                <p class="text-muted">
                    Complete la información para agendar una entrevista con un candidato.
                </p>
            </div>
            <a href="entrevistas.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
        </div>
        <!-- MENSAJES -->
        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>">
                <?= e($mensaje) ?>
            </div>
        <?php endif; ?>
        <!-- FORMULARIO -->
        <div class="table-box">
            <form method="POST" id="formEntrevista">
                <div class="row">
                    <!-- POSTULACIÓN -->
                    <div class="col-12 mb-4">
                        <label
                            for="postulacion_id"
                            class="form-label fw-bold">
                            Postulación
                        </label>
                        <select
                            id="postulacion_id"
                            name="postulacion_id"
                            class="form-select"
                            required>
                            <option value="">
                                Seleccione una postulación
                            </option>
                            <?php foreach ($postulaciones as $p): ?>
                                <option
                                    value="<?= (int)$p['id'] ?>"
                                    data-candidato="<?= e($p['nombre_completo']) ?>"
                                    data-vacante="<?= e($p['trabajo']) ?>"
                                    data-correo="<?= e($p['correo']) ?>"
                                    data-telefono="<?= e($p['telefono']) ?>">
                                    <?= e($p['nombre_completo']) ?>
                                    — 
                                    <?= e($p['trabajo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- CANDIDATO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">
                            Candidato
                        </label>
                        <input
                            type="text"
                            id="nombre_candidato"
                            class="form-control"
                            readonly>
                    </div>
                    <!-- VACANTE -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">
                            Vacante
                        </label>
                        <input
                            type="text"
                            id="nombre_vacante"
                            class="form-control"
                            readonly>
                    </div>
                    <!-- CORREO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            id="correo"
                            class="form-control"
                            readonly>
                    </div>
                    <!-- TELÉFONO -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">
                            Teléfono
                        </label>
                        <input
                            type="text"
                            id="telefono"
                            class="form-control"
                            readonly>
                    </div>
                    <!-- FECHA -->
                    <div class="col-md-6 mb-4">
                        <label
                            for="fecha"
                            class="form-label fw-bold">
                            Fecha
                        </label>
                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            class="form-control"
                            required>
                    </div>
                    <!-- HORA -->
                    <div class="col-md-6 mb-4">
                        <label
                            for="hora"
                            class="form-label fw-bold">
                            Hora
                        </label>
                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            class="form-control"
                            required>
                    </div>
                                        <!-- MODALIDAD -->
                    <div class="col-md-6 mb-4">
                        <label
                            for="modalidad"
                            class="form-label fw-bold">
                            Modalidad
                        </label>
                        <select
                            id="modalidad"
                            name="modalidad"
                            class="form-select"
                            required>
                            <option value="">
                                Seleccione una modalidad
                            </option>
                            <option value="Presencial">
                                Presencial
                            </option>
                            <option value="Virtual">
                                Virtual
                            </option>
                            <option value="Telefónica">
                                Telefónica
                            </option>
                        </select>
                    </div>
                    <!-- ESTADO -->
                    <div class="col-md-6 mb-4">
                        <label
                            for="estado"
                            class="form-label fw-bold">
                            Estado
                        </label>
                        <select
                            id="estado"
                            name="estado"
                            class="form-select"
                            required>
                            <option value="Programada" selected>
                                Programada
                            </option>
                            <option value="Realizada">
                                Realizada
                            </option>
                            <option value="Cancelada">
                                Cancelada
                            </option>
                        </select>
                    </div>
                    <!-- LUGAR -->
                    <div class="col-12 mb-4">
                        <label
                            for="lugar"
                            class="form-label fw-bold">
                            Lugar o enlace de la entrevista
                        </label>
                        <input
                            type="text"
                            id="lugar"
                            name="lugar"
                            class="form-control"
                            placeholder="Ej. Sala de juntas, Google Meet, Zoom..."
                            required>
                    </div>
                    <!-- NOTAS -->
                    <div class="col-12 mb-4">
                        <label
                            for="notas"
                            class="form-label fw-bold">
                            Notas
                        </label>
                        <textarea
                            id="notas"
                            name="notas"
                            rows="5"
                            class="form-control"
                            placeholder="Agregue observaciones para la entrevista..."></textarea>
                    </div>
                </div>
                <hr>
                <!-- BOTONES -->
                <div class="d-flex justify-content-end gap-2">
                    <a
                        href="entrevistas.php"
                        class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Cancelar
                    </a>
                    <button
                        type="reset"
                        class="btn btn-warning">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Limpiar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-reclutador">
                        <i class="bi bi-calendar-plus-fill me-2"></i>
                        Programar Entrevista
                    </button>
                </div>
                    </form>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const postulacion = document.getElementById("postulacion_id");
    const candidato = document.getElementById("nombre_candidato");
    const vacante   = document.getElementById("nombre_vacante");
    const correo    = document.getElementById("correo");
    const telefono  = document.getElementById("telefono");
    postulacion.addEventListener("change", function () {
        const opcion = this.options[this.selectedIndex];
        if (this.value === "") {
            candidato.value = "";
            vacante.value = "";
            correo.value = "";
            telefono.value = "";
            return;
        }
        candidato.value = opcion.dataset.candidato;
        vacante.value   = opcion.dataset.vacante;
        correo.value    = opcion.dataset.correo;
        telefono.value  = opcion.dataset.telefono;
    });
});
</script>
<?php include "includes/footer.php"; ?>
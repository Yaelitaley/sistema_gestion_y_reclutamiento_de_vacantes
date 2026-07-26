<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';
if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}
$usuarioId = (int) $_SESSION['usuario_id'];
$vacanteId = (int) ($_GET['id'] ?? 0);
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
| Obtener vacante
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    v.*,
    (
        SELECT COUNT(*)
        FROM postulaciones p
        WHERE p.vacante_id = v.id
    ) AS total_postulaciones
FROM vacantes v
WHERE
    v.id = ?
AND
    v.reclutador_id = ?
LIMIT 1
");
$stmt->bind_param("ii", $vacanteId, $reclutadorId);
$stmt->execute();
$vacante = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$vacante) {
    redirect_to(
        'vacantes.php?type=danger&msg=' .
        urlencode('La vacante no existe o no tienes permisos para visualizarla.')
    );
}
include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= e($vacante['trabajo']) ?>
                </h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                    <?= e($vacante['ubicacion']) ?>
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a
                    href="vacantes.php"
                    class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i>
                    Regresar
                </a>
                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalVacante"
                    onclick='editarVacante(<?= json_encode($vacante) ?>)'>
                    <i class="bi bi-pencil-fill me-2"></i>
                    Editar Vacante
                </button>
            </div>
        </div>
        <!-- Tarjetas -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-success-subtle">
                        <i class="bi bi-cash-stack text-success"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold">
                            <?= $vacante['salario'] !== null
                                ? '$' . number_format((float)$vacante['salario'],2)
                                : 'No especificado'; ?>
                        </h4>
                        <p class="mb-0">
                            Salario
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-warning-subtle">
                        <i class="bi bi-award-fill text-warning"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold">
                            <?= e($vacante['nivel_experiencia']) ?>
                        </h4>
                        <p class="mb-0">
                            Experiencia
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-subtle">
                        <i class="bi bi-tags-fill text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold">
                            <?= e($vacante['categoria']) ?>
                        </h4>
                        <p class="mb-0">
                            Categoría
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-info-subtle">
                        <i class="bi bi-people-fill text-info"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold">
                            <?= (int)$vacante['total_postulaciones'] ?>
                        </h4>
                        <p class="mb-0">
                            Postulaciones
                        </p>
                    </div>
                </div>
            </div>
        </div>

                <!-- ===========================
             DESCRIPCIÓN Y DETALLES
        ============================ -->
        <div class="row g-4">
            <!-- Columna izquierda -->
            <div class="col-lg-8">
                <div class="table-box mb-4">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                        Descripción del Puesto
                    </h4>
                    <hr>
                    <p
                        class="text-muted mb-0"
                        style="white-space: pre-line; text-align: justify;">
                        <?= e($vacante['descripcion']) ?>
                    </p>
                </div>
                <div class="table-box">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-card-checklist text-success me-2"></i>
                        Requisitos
                    </h4>
                    <hr>
                    <?php if (trim($vacante['requisitos']) != ""): ?>
                        <p
                            class="text-muted mb-0"
                            style="white-space: pre-line; text-align: justify;">
                            <?= e($vacante['requisitos']) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted fst-italic">
                            No se especificaron requisitos.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Columna derecha -->
            <div class="col-lg-4">
                <div class="table-box">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        Información de la Vacante
                    </h4>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="45%">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                Ubicación
                            </th>
                            <td>
                                <?= e($vacante['ubicacion']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="bi bi-tags-fill text-primary me-2"></i>
                                Categoría
                            </th>
                            <td>
                                <?= e($vacante['categoria']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="bi bi-award-fill text-warning me-2"></i>
                                Experiencia
                            </th>
                            <td>
                                <?= e($vacante['nivel_experiencia']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Estado
                            </th>
                            <td>
                                <?= badge_estado($vacante['activa'] ? 'Activo' : 'Inactivo') ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="bi bi-calendar-event-fill text-secondary me-2"></i>
                                Publicada
                            </th>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($vacante['created_at'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <i class="bi bi-clock-history text-secondary me-2"></i>
                                Última actualización
                            </th>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($vacante['updated_at'])) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- Botones inferiores -->
        <div class="d-flex justify-content-end mt-4">
            <a
                href="vacantes.php"
                class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Regresar
            </a>
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalVacante"
                onclick='editarVacante(<?= json_encode($vacante) ?>)'>
                <i class="bi bi-pencil-fill me-2"></i>
                Editar Vacante
            </button>
        </div>
    </div>
</div>

<!-- ===========================
     MODAL EDITAR VACANTE
=========================== -->

<div class="modal fade" id="modalVacante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="vacantes.php">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalVacanteTitulo">
                        Editar Vacante
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="accion" value="guardar">
                    <input type="hidden" name="id" id="f_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-bold">
                                Puesto
                            </label>

                            <input
                                type="text"
                                name="trabajo"
                                id="f_trabajo"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-bold">
                                Categoría
                            </label>

                            <input
                                type="text"
                                name="categoria"
                                id="f_categoria"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Ubicación
                            </label>

                            <input
                                type="text"
                                name="ubicacion"
                                id="f_ubicacion"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Salario
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="salario"
                                id="f_salario"
                                class="form-control">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Nivel de experiencia
                            </label>

                            <input
                                type="text"
                                name="nivel_experiencia"
                                id="f_nivel"
                                class="form-control"
                                required>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Descripción
                        </label>

                        <textarea
                            name="descripcion"
                            id="f_descripcion"
                            rows="4"
                            class="form-control"
                            required></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Requisitos
                        </label>

                        <textarea
                            name="requisitos"
                            id="f_requisitos"
                            rows="4"
                            class="form-control"></textarea>

                    </div>

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="activa"
                            id="f_activa">

                        <label
                            class="form-check-label"
                            for="f_activa">

                            Vacante activa

                        </label>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Guardar Cambios

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

function editarVacante(v){

    document.getElementById("modalVacanteTitulo").innerText="Editar Vacante";

    document.getElementById("f_id").value=v.id;
    document.getElementById("f_trabajo").value=v.trabajo;
    document.getElementById("f_categoria").value=v.categoria;
    document.getElementById("f_ubicacion").value=v.ubicacion;
    document.getElementById("f_salario").value=v.salario;
    document.getElementById("f_nivel").value=v.nivel_experiencia;
    document.getElementById("f_descripcion").value=v.descripcion;
    document.getElementById("f_requisitos").value=v.requisitos;
    document.getElementById("f_activa").checked=(v.activa==1);

}

</script>

<?php include "includes/footer.php"; ?>
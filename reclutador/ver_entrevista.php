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

$usuarioId = (int) $_SESSION['usuario_id'];
$entrevistaId = (int) ($_GET['id'] ?? 0);

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

$reclutadorId = (int) $reclutador['id'];

/*
|--------------------------------------------------------------------------
| Validar ID
|--------------------------------------------------------------------------
*/

if ($entrevistaId <= 0) {

    redirect_to(
        'entrevistas.php?type=danger&msg=' .
        urlencode('La entrevista solicitada no es válida.')
    );

}

/*
|--------------------------------------------------------------------------
| Obtener entrevista
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("

SELECT

    e.*,

    c.nombre_completo,
    c.correo,
    c.telefono,

    v.id AS vacante_id,
    v.trabajo,
    v.ubicacion,

    p.id AS postulacion_id

FROM entrevistas e

INNER JOIN postulaciones p
    ON p.id = e.postulacion_id

INNER JOIN candidatos c
    ON c.id = p.candidato_id

INNER JOIN vacantes v
    ON v.id = p.vacante_id

WHERE

    e.id = ?
    AND v.reclutador_id = ?

LIMIT 1

");

$stmt->bind_param(
    "ii",
    $entrevistaId,
    $reclutadorId
);

$stmt->execute();

$entrevista = $stmt->get_result()->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Validar entrevista
|--------------------------------------------------------------------------
*/

if (!$entrevista) {

    redirect_to(
        'entrevistas.php?type=danger&msg=' .
        urlencode('La entrevista no existe o no tienes permisos para visualizarla.')
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
                    Ver Entrevista
                </h2>

                <p class="text-muted mb-0">
                    Información completa de la entrevista programada.
                </p>

            </div>

            <div class="mt-3 mt-md-0">

                <a
                    href="entrevistas.php"
                    class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Regresar

                </a>

            </div>

        </div>

        <!-- Tarjetas superiores -->

        <div class="row g-4 mb-4">

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-calendar-event-fill text-primary"></i>

                    </div>

                    <div>

                        <h4 class="fw-bold">

                            <?= date('d/m/Y', strtotime($entrevista['fecha'])) ?>

                        </h4>

                        <p class="mb-0">

                            Fecha

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-clock-fill text-success"></i>

                    </div>

                    <div>

                        <h4 class="fw-bold">

                            <?= date('H:i', strtotime($entrevista['fecha'])) ?>

                        </h4>

                        <p class="mb-0">

                            Hora

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-camera-video-fill text-warning"></i>

                    </div>

                    <div>

                        <h4 class="fw-bold">

                            <?= e($entrevista['modalidad']) ?>

                        </h4>

                        <p class="mb-0">

                            Modalidad

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-info-subtle">

                        <i class="bi bi-check-circle-fill text-info"></i>

                    </div>

                    <div>

                        <h4 class="fw-bold">

                            <?= e($entrevista['estado']) ?>

                        </h4>

                        <p class="mb-0">

                            Estado

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- Información -->

        <div class="row g-4">

            <!-- Información del candidato -->

            <div class="col-lg-6">

                <div class="table-box">

                    <h4 class="fw-bold mb-3">

                        <i class="bi bi-person-fill text-primary me-2"></i>

                        Información del Candidato

                    </h4>

                    <hr>

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th width="35%">

                                Nombre

                            </th>

                            <td>

                                <?= e($entrevista['nombre_completo']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Correo

                            </th>

                            <td>

                                <?= e($entrevista['correo']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Teléfono

                            </th>

                            <td>

                                <?= e($entrevista['telefono']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Vacante

                            </th>

                            <td>

                                <?= e($entrevista['trabajo']) ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            <!-- Información de la entrevista -->

            <div class="col-lg-6">

                <div class="table-box">

                    <h4 class="fw-bold mb-3">

                        <i class="bi bi-calendar-check-fill text-success me-2"></i>

                        Información de la Entrevista

                    </h4>

                    <hr>

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th width="35%">

                                Fecha

                            </th>

                            <td>

                                <?= date('d/m/Y', strtotime($entrevista['fecha'])) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Hora

                            </th>

                            <td>

                                <?= date('H:i', strtotime($entrevista['fecha'])) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Modalidad

                            </th>

                            <td>

                                <?= e($entrevista['modalidad']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Lugar

                            </th>

                            <td>

                                <?= e($entrevista['lugar']) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Estado

                            </th>

                            <td>

                                <?= badge_estado($entrevista['estado']) ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            

        </div>

        <!-- Notas -->

        <div class="row mt-4">

            <div class="col-12">

                <div class="table-box">

                    <h4 class="fw-bold mb-3">

                        <i class="bi bi-journal-text text-warning me-2"></i>

                        Notas de la Entrevista

                    </h4>

                    <hr>

                    <?php if (!empty(trim($entrevista['notas'] ?? ''))): ?>

                        <p
                            class="text-muted mb-0"
                            style="white-space: pre-line; text-align: justify;">

                            <?= e($entrevista['notas']) ?>

                        </p>

                    <?php else: ?>

                        <p class="text-muted fst-italic">

                            No se agregaron notas para esta entrevista.

                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <!-- Información adicional -->

        <div class="row mt-4">

            <div class="col-12">

                <div class="table-box">

                    <h4 class="fw-bold mb-3">

                        <i class="bi bi-info-circle-fill text-info me-2"></i>

                        Información del Registro

                    </h4>

                    <hr>

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th width="30%">

                                <i class="bi bi-calendar-plus-fill text-success me-2"></i>

                                Fecha de creación

                            </th>

                            <td>

                                <?= date('d/m/Y H:i', strtotime($entrevista['created_at'])) ?>

                            </td>

                        </tr>

                        <tr>

                            <th>

                                <i class="bi bi-clock-history text-secondary me-2"></i>

                                Última actualización

                            </th>

                            <td>

                                <?= date('d/m/Y H:i', strtotime($entrevista['updated_at'])) ?>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        <!-- Botones inferiores -->

        <div class="d-flex justify-content-end mt-4">

            <a
                href="entrevistas.php"
                class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>

                Regresar

            </a>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
    
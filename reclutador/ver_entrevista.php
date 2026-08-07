<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';


if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];
$entrevistaId = (int) ($_GET['id'] ?? 0);

if ($entrevistaId <= 0) {
    redirect_to('entrevistas.php?type=danger&msg=' . urlencode('La entrevista solicitada no es válida.'));
}

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div id="alertaEntrevista"></div>

        <div id="contenidoEntrevista">
            <div class="table-box text-center text-muted py-5">Cargando información de la entrevista...</div>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
const ENTREVISTA_ID = <?= json_encode($entrevistaId) ?>;

const BADGES = {
    'Programada': 'bg-warning text-dark',
    'Realizada':  'bg-success',
    'Cancelada':  'bg-danger',
};

function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function badgeEstado(estado) {
    return `<span class="badge ${BADGES[estado] || 'bg-secondary'}">${estado}</span>`;
}

function fechaHora(fecha, opts) {
    return fecha ? new Date(fecha).toLocaleString('es-MX', opts) : '-';
}

function render(ent) {
    const fecha = fechaHora(ent.fecha, { day: '2-digit', month: '2-digit', year: 'numeric' });
    const hora = fechaHora(ent.fecha, { hour: '2-digit', minute: '2-digit' });

    document.getElementById('contenidoEntrevista').innerHTML = `
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1">Ver Entrevista</h2>
                <p class="text-muted mb-0">Información completa de la entrevista programada.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="entrevistas.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Regresar</a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-calendar-event-fill text-primary"></i></div><div><h4 class="fw-bold">${fecha}</h4><p class="mb-0">Fecha</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-clock-fill text-success"></i></div><div><h4 class="fw-bold">${hora}</h4><p class="mb-0">Hora</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-camera-video-fill text-info"></i></div><div><h4 class="fw-bold">${esc(ent.modalidad)}</h4><p class="mb-0">Modalidad</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-flag-fill text-warning"></i></div><div><h4 class="fw-bold">${esc(ent.estado)}</h4><p class="mb-0">Estado</p></div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-person-fill text-primary me-2"></i>Información del Candidato</h4>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tr><th width="35%">Nombre</th><td>${esc(ent.candidato_nombre)}</td></tr>
                        <tr><th>Vacante</th><td>${esc(ent.vacante_trabajo)}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-calendar-check-fill text-success me-2"></i>Información de la Entrevista</h4>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tr><th width="35%">Fecha</th><td>${fecha}</td></tr>
                        <tr><th>Hora</th><td>${hora}</td></tr>
                        <tr><th>Modalidad</th><td>${esc(ent.modalidad)}</td></tr>
                        <tr><th>Lugar</th><td>${esc(ent.lugar)}</td></tr>
                        <tr><th>Estado</th><td>${badgeEstado(ent.estado)}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-journal-text text-warning me-2"></i>Notas de la Entrevista</h4>
                    <hr>
                    ${(ent.notas || '').trim() !== ''
                        ? `<p class="text-muted mb-0" style="white-space: pre-line; text-align: justify;">${esc(ent.notas)}</p>`
                        : '<p class="text-muted fst-italic">No se agregaron notas para esta entrevista.</p>'}
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-info me-2"></i>Información del Registro</h4>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tr><th width="30%"><i class="bi bi-calendar-plus-fill text-success me-2"></i>Fecha de creación</th><td>${fechaHora(ent.created_at, { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td></tr>
                        <tr><th><i class="bi bi-clock-history text-secondary me-2"></i>Última actualización</th><td>${fechaHora(ent.updated_at, { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="entrevistas.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Regresar</a>
        </div>
    `;
}

async function cargar() {
    const recRes = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });
    if (!recRes.ok || !recRes.data) {
        window.location.href = 'entrevistas.php?type=danger&msg=' + encodeURIComponent('No se encontró el perfil del reclutador.');
        return;
    }

    const { ok, data } = await Api.get('entrevistas', { reclutador_id: recRes.data.id });
    const entrevista = ok ? data.find(e => Number(e.id) === ENTREVISTA_ID) : null;

    if (!entrevista) {
        window.location.href = 'entrevistas.php?type=danger&msg=' + encodeURIComponent('La entrevista no existe o no tienes permisos para visualizarla.');
        return;
    }

    render(entrevista);
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

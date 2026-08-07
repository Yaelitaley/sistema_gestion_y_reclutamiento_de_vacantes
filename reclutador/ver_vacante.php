<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';


if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];
$vacanteId = (int) ($_GET['id'] ?? 0);

if ($vacanteId <= 0) {
    redirect_to('vacantes.php');
}

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div id="alertaVacante"></div>

        <div id="contenidoVacante">
            <div class="table-box text-center text-muted py-5">Cargando información de la vacante...</div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR VACANTE -->
<div class="modal fade" id="modalVacante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formVacante">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Vacante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="f_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Puesto</label>
                            <input type="text" id="f_trabajo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <input type="text" id="f_categoria" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" id="f_ubicacion" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Salario</label>
                            <input type="number" step="0.01" id="f_salario" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nivel de experiencia</label>
                            <input type="text" id="f_nivel" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea id="f_descripcion" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos</label>
                        <textarea id="f_requisitos" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="f_activa">
                        <label class="form-check-label" for="f_activa">Vacante activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const VACANTE_ID = <?= json_encode($vacanteId) ?>;
let vacanteActual = null;
let modalVacanteEl = null;

function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaVacante').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function badgeEstado(activa) {
    return activa == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
}

function formatoFechaHora(fecha) {
    return fecha ? new Date(fecha).toLocaleString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
}

function render(v) {
    document.getElementById('contenidoVacante').innerHTML = `
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1">${esc(v.trabajo)}</h2>
                <p class="text-muted mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>${esc(v.ubicacion)}</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="vacantes.php" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i> Regresar</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVacante" onclick="abrirEdicion()"><i class="bi bi-pencil-fill me-2"></i>Editar Vacante</button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-cash-stack text-success"></i></div><div><h4 class="fw-bold">${v.salario !== null ? '$' + Number(v.salario).toLocaleString('es-MX', { minimumFractionDigits: 2 }) : 'No especificado'}</h4><p class="mb-0">Salario</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-award-fill text-warning"></i></div><div><h4 class="fw-bold">${esc(v.nivel_experiencia)}</h4><p class="mb-0">Experiencia</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-tags-fill text-primary"></i></div><div><h4 class="fw-bold">${esc(v.categoria)}</h4><p class="mb-0">Categoría</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-people-fill text-info"></i></div><div><h4 class="fw-bold">${v.total_postulaciones ?? 0}</h4><p class="mb-0">Postulaciones</p></div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-box mb-4">
                    <h4 class="fw-bold mb-3"><i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Descripción del Puesto</h4>
                    <hr>
                    <p class="text-muted mb-0" style="white-space: pre-line; text-align: justify;">${esc(v.descripcion)}</p>
                </div>
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-card-checklist text-success me-2"></i>Requisitos</h4>
                    <hr>
                    ${(v.requisitos || '').trim() !== ''
                        ? `<p class="text-muted mb-0" style="white-space: pre-line; text-align: justify;">${esc(v.requisitos)}</p>`
                        : '<p class="text-muted fst-italic">No se especificaron requisitos.</p>'}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="table-box">
                    <h4 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-info me-2"></i>Información de la Vacante</h4>
                    <hr>
                    <table class="table table-borderless mb-0">
                        <tr><th width="45%"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Ubicación</th><td>${esc(v.ubicacion)}</td></tr>
                        <tr><th><i class="bi bi-tags-fill text-primary me-2"></i>Categoría</th><td>${esc(v.categoria)}</td></tr>
                        <tr><th><i class="bi bi-award-fill text-warning me-2"></i>Experiencia</th><td>${esc(v.nivel_experiencia)}</td></tr>
                        <tr><th><i class="bi bi-check-circle-fill text-success me-2"></i>Estado</th><td>${badgeEstado(v.activa)}</td></tr>
                        <tr><th><i class="bi bi-calendar-event-fill text-secondary me-2"></i>Publicada</th><td>${formatoFechaHora(v.created_at)}</td></tr>
                        <tr><th><i class="bi bi-clock-history text-secondary me-2"></i>Última actualización</th><td>${formatoFechaHora(v.updated_at)}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="vacantes.php" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i> Regresar</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVacante" onclick="abrirEdicion()"><i class="bi bi-pencil-fill me-2"></i>Editar Vacante</button>
        </div>
    `;
}

function abrirEdicion() {
    if (!vacanteActual) return;
    const v = vacanteActual;
    document.getElementById('f_id').value = v.id;
    document.getElementById('f_trabajo').value = v.trabajo;
    document.getElementById('f_categoria').value = v.categoria;
    document.getElementById('f_ubicacion').value = v.ubicacion;
    document.getElementById('f_salario').value = v.salario ?? '';
    document.getElementById('f_nivel').value = v.nivel_experiencia;
    document.getElementById('f_descripcion').value = v.descripcion;
    document.getElementById('f_requisitos').value = v.requisitos ?? '';
    document.getElementById('f_activa').checked = v.activa == 1;
}

async function cargar() {
    const { ok, data, message } = await Api.getOne('vacantes', VACANTE_ID);

    if (!ok || !data) {
        window.location.href = 'vacantes.php?type=danger&msg=' + encodeURIComponent(message || 'La vacante no existe o no tienes permisos para visualizarla.');
        return;
    }

    vacanteActual = data;
    render(data);
}

document.getElementById('formVacante').addEventListener('submit', async function (e) {
    e.preventDefault();

    const payload = {
        trabajo: document.getElementById('f_trabajo').value.trim(),
        categoria: document.getElementById('f_categoria').value.trim(),
        ubicacion: document.getElementById('f_ubicacion').value.trim(),
        salario: document.getElementById('f_salario').value === '' ? null : Number(document.getElementById('f_salario').value),
        nivel_experiencia: document.getElementById('f_nivel').value.trim(),
        descripcion: document.getElementById('f_descripcion').value.trim(),
        requisitos: document.getElementById('f_requisitos').value.trim(),
        activa: document.getElementById('f_activa').checked ? 1 : 0,
    };

    const { ok, message } = await Api.patch('vacantes', VACANTE_ID, payload);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo guardar la vacante.', 'danger');
        return;
    }

    modalVacanteEl.hide();
    mostrarAlerta('Vacante actualizada correctamente.', 'success');
    await cargar();
});

document.addEventListener('DOMContentLoaded', function () {
    modalVacanteEl = new bootstrap.Modal(document.getElementById('modalVacante'));
    cargar();
});
</script>

<?php include "includes/footer.php"; ?>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';


if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div id="alertaDashboard"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-building-check text-primary"></i></div><div><h3 class="texto fw-bold" id="statVacantes">0</h3><p class="texto mb-0">Vacantes Activas</p></div></div>
            </div>
            <div class="table-responsive col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-person-vcard-fill text-success"></i></div><div><h3 class="texto fw-bold" id="statPostulantes">0</h3><p class="texto mb-0">Postulantes</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-calendar2-check-fill text-warning"></i></div><div><h3 class="texto fw-bold" id="statEntrevistas">0</h3><p class="texto mb-0">Entrevistas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-envelope-check-fill text-info"></i></div><div><h3 class="texto fw-bold" id="statContratados">0</h3><p class="texto mb-0">Contratados</p></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="texto fw-bold">Mis Procesos Activos</h5>
                        <a href="vacantes.php" class="btn btn-sm btn-reclutador">Ver Vacantes</a>
                    </div>
                    <table class="table table-hover">
                        <thead><tr><th>Puesto</th><th>Postulados</th><th>Estado</th><th>Actualizado</th></tr></thead>
                        <tbody id="tbodyProcesos"><tr><td colspan="4" class="text-center text-muted py-3">Cargando...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="action-box">
                    <h5 class="texto fw-bold mb-4">Próximas Entrevistas</h5>
                    <div id="listaProximasEntrevistas"><p class="text-muted">Cargando...</p></div>
                    <a href="entrevistas.php" class="btn btn-reclutador w-100">Ver Entrevistas</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="table-box">
                    <h5 class="texto fw-bold mb-4">Etapas de Reclutamiento</h5>
                    <div class="row text-center">
                        <div class="col"><h2 id="etapaPostulados">0</h2><p>Postulados</p></div>
                        <div class="col"><h2 id="etapaRevision">0</h2><p>Revisión</p></div>
                        <div class="col"><h2 id="etapaEntrevistas">0</h2><p>Entrevistas</p></div>
                        <div class="col"><h2 id="etapaContratados">0</h2><p>Contratados</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaDashboard').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function renderProcesos(vacantes) {
    const tbody = document.getElementById('tbodyProcesos');
    const ultimos5 = [...vacantes].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)).slice(0, 5);

    if (!ultimos5.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Aún no tienes vacantes publicadas.</td></tr>';
        return;
    }

    tbody.innerHTML = ultimos5.map(v => `
        <tr>
            <td>${v.trabajo}</td>
            <td>${v.total_postulaciones ?? 0}</td>
            <td>${v.activa == 1 ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>'}</td>
            <td>${new Date(v.updated_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
        </tr>
    `).join('');
}

function renderProximasEntrevistas(entrevistas) {
    const cont = document.getElementById('listaProximasEntrevistas');
    const proximas = entrevistas.slice(0, 4);

    if (!proximas.length) {
        cont.innerHTML = '<p class="text-muted">No tienes entrevistas programadas.</p>';
        return;
    }

    cont.innerHTML = proximas.map(e => `
        <div class="mb-4">
            <h6 class="texto fw-bold">${e.candidato_nombre}</h6>
            <small class="texto text-muted">${e.vacante_trabajo}</small><br>
            <small>${new Date(e.fecha).toLocaleString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit' })}</small>
        </div>
    `).join('');
}

async function cargar() {
    const recRes = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });
    if (!recRes.ok || !recRes.data) {
        mostrarAlerta(recRes.message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }
    const reclutadorId = recRes.data.id;

    const [vacRes, postRes, entRes] = await Promise.all([
        Api.get('vacantes', { reclutador_id: reclutadorId, limit: 200 }),
        Api.get('postulaciones', { reclutador_id: reclutadorId, limit: 200 }),
        Api.get('entrevistas', { reclutador_id: reclutadorId, proximas: 1 }),
    ]);

    const vacantes = vacRes.ok ? vacRes.data : [];
    const postulaciones = postRes.ok ? postRes.data : [];
    const entrevistas = entRes.ok ? entRes.data : [];

    document.getElementById('statVacantes').textContent = vacantes.filter(v => v.activa == 1).length;
    document.getElementById('statPostulantes').textContent = postulaciones.length;
    document.getElementById('statEntrevistas').textContent = entrevistas.length;
    document.getElementById('statContratados').textContent = postulaciones.filter(p => Number(p.estado_id) === 5).length;

    document.getElementById('etapaPostulados').textContent = postulaciones.filter(p => Number(p.estado_id) === 1).length;
    document.getElementById('etapaRevision').textContent = postulaciones.filter(p => Number(p.estado_id) === 2).length;
    document.getElementById('etapaEntrevistas').textContent = postulaciones.filter(p => Number(p.estado_id) === 3).length;
    document.getElementById('etapaContratados').textContent = postulaciones.filter(p => Number(p.estado_id) === 5).length;

    renderProcesos(vacantes);
    renderProximasEntrevistas(entrevistas);
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

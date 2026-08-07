<?php
require_once '../config/config.php';
require_once '../config/app_helpers.php';
require_admin_login();

include "includes/header.php";
?>
    <?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>

        <div id="alertaDashboard"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon icon-blue"><i class="bi bi-person-workspace text-primary"></i></div><div><h3 class="texto fw-bold" id="statReclutadores">0</h3><p class="texto mb-0">Reclutadores</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon icon-green"><i class="bi bi-person-vcard-fill text-success"></i></div><div><h3 class="texto fw-bold" id="statCandidatos">0</h3><p class="texto mb-0">Candidatos</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon icon-orange"><i class="bi bi-building-fill-check text-warning"></i></div><div><h3 class="texto fw-bold" id="statVacantes">0</h3><p class="texto mb-0">Vacantes</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon icon-purple"><i class="bi bi-clipboard2-check-fill text-danger"></i></div><div><h3 class="texto fw-bold" id="statPostulaciones">0</h3><p class="texto mb-0">Postulaciones</p></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="texto fw-bold">Reclutadores recientes</h5>
                        <a href="../reclutador/register.php" class="btn btn-dashboard btn-purple w-100 mb-3">Ver todos</a>
                    </div>
                    <table class="table align-middle">
                        <thead><tr><th>Nombre</th><th>Correo</th><th>Empresa</th><th>Estado</th></tr></thead>
                        <tbody id="tbodyRecientes"><tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-box">
                    <h5 class="fw-bold mb-4">Acciones rápidas</h5>
                    <a href="../reclutador/register.php" class="btn btn-dashboard btn-blue"><i class="bi bi-person-workspace me-2"></i>Agregar Reclutador</a>
                    <br><br>
                    <a href="../candidatos/register.php" class="btn btn-dashboard btn-green"><i class="bi bi-person-vcard-fill me-2"></i>Agregar Candidato</a>
                    <br><br>
                    <a href="../admin/register.php" class="btn btn-dashboard btn-purple"><i class="bi bi-shield-lock-fill me-2"></i>Agregar Administrador</a>
                    <br><br>
                    <a href="vacantes.php" class="btn btn-dashboard btn-orange"><i class="bi bi-briefcase-fill me-2"></i>Gestionar Vacantes</a>
                    <br><br>
                    <a href="reportes.php" class="btn btn-dashboard btn-red"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Ver Reporte</a>
                </div>
            </div>
        </div>
    </div>

<script src="../assets/js/api-client.js"></script>
<script>
function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaDashboard').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

const BADGES = { activo: 'bg-success', pendiente: 'bg-warning text-dark', bloqueado: 'bg-danger' };

function renderRecientes(reclutadores) {
    const tbody = document.getElementById('tbodyRecientes');
    const ultimos3 = [...reclutadores].sort((a, b) => b.id - a.id).slice(0, 3);

    if (!ultimos3.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin reclutadores registrados.</td></tr>';
        return;
    }

    tbody.innerHTML = ultimos3.map(r => {
        const estado = (r.estado || '').toLowerCase();
        const badge = BADGES[estado] || 'bg-secondary';
        return `
            <tr>
                <td>${r.nombre_completo}</td>
                <td>${r.correo ?? ''}</td>
                <td>${r.empresa_nombre ?? 'Sin empresa'}</td>
                <td><span class="badge ${badge}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span></td>
            </tr>`;
    }).join('');
}

async function cargar() {
    const [recRes, candRes, vacRes, postRes] = await Promise.all([
        Api.get('reclutadores', { limit: 200 }),
        Api.get('candidatos', { limit: 1 }),
        Api.get('vacantes', { limit: 1 }),
        Api.get('postulaciones', { limit: 1 }),
    ]);

    if (recRes.ok) {
        document.getElementById('statReclutadores').textContent = recRes.meta ? recRes.meta.total : recRes.data.length;
        renderRecientes(recRes.data);
    } else {
        mostrarAlerta(recRes.message || 'No se pudieron cargar los reclutadores.', 'danger');
    }

    if (candRes.ok && candRes.meta) document.getElementById('statCandidatos').textContent = candRes.meta.total;
    if (vacRes.ok && vacRes.meta) document.getElementById('statVacantes').textContent = vacRes.meta.total;
    if (postRes.ok && postRes.meta) document.getElementById('statPostulaciones').textContent = postRes.meta.total.toLocaleString('es-MX');
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

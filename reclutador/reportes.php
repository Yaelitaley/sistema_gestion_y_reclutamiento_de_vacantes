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

        <div class="mb-4">
            <h2 class="fw-bold">Mis Reportes</h2>
            <p class="text-muted">Resumen del desempeño de tus vacantes y procesos.</p>
        </div>

        <div id="alertaReportes"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold" id="statVacTotal">0</h3><p class="text-muted mb-0">Vacantes Totales</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold" id="statVacActivas">0</h3><p class="text-muted mb-0">Vacantes Activas</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-people-fill text-info"></i></div><div><h3 class="fw-bold" id="statPostulaciones">0</h3><p class="text-muted mb-0">Postulaciones</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-calendar-event-fill text-warning"></i></div><div><h3 class="fw-bold" id="statEntrevistas">0</h3><p class="text-muted mb-0">Entrevistas</p></div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Postulaciones por Estado</h5>
                    <div id="porEstado"><p class="text-muted">Cargando...</p></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes con más Postulaciones</h5>
                    <div id="topVacantes"><p class="text-muted">Cargando...</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
const ESTADOS_ORDEN = ['Postulado', 'En revisión', 'Entrevista', 'Contratado', 'Rechazado'];

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaReportes').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function barra(label, total, max, color = '') {
    const pct = max > 0 ? (total / max) * 100 : 0;
    return `
        <div class="mb-3">
            <div class="d-flex justify-content-between"><span>${label}</span><strong>${total}</strong></div>
            <div class="progress" style="height: 8px;"><div class="progress-bar ${color}" style="width: ${pct}%"></div></div>
        </div>`;
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
        Api.get('entrevistas', { reclutador_id: reclutadorId }),
    ]);

    const vacantes = vacRes.ok ? vacRes.data : [];
    const postulaciones = postRes.ok ? postRes.data : [];
    const entrevistas = entRes.ok ? entRes.data : [];

    document.getElementById('statVacTotal').textContent = vacantes.length;
    document.getElementById('statVacActivas').textContent = vacantes.filter(v => v.activa == 1).length;
    document.getElementById('statPostulaciones').textContent = postulaciones.length;
    document.getElementById('statEntrevistas').textContent = entrevistas.length;

    // Postulaciones por estado
    const conteoEstados = {};
    ESTADOS_ORDEN.forEach(e => conteoEstados[e] = 0);
    postulaciones.forEach(p => {
        conteoEstados[p.estado_nombre] = (conteoEstados[p.estado_nombre] || 0) + 1;
    });
    const maxEstado = Math.max(1, ...Object.values(conteoEstados));
    document.getElementById('porEstado').innerHTML = ESTADOS_ORDEN.map(e => barra(e, conteoEstados[e], maxEstado)).join('');

    // Vacantes con más postulaciones
    const topVacantes = [...vacantes]
        .sort((a, b) => Number(b.total_postulaciones || 0) - Number(a.total_postulaciones || 0))
        .slice(0, 5);
    const maxVacante = Math.max(1, ...topVacantes.map(v => Number(v.total_postulaciones || 0)));

    document.getElementById('topVacantes').innerHTML = topVacantes.length
        ? topVacantes.map(v => barra(v.trabajo, Number(v.total_postulaciones || 0), maxVacante, 'bg-success')).join('')
        : '<p class="text-muted">Aún no tienes vacantes registradas.</p>';
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

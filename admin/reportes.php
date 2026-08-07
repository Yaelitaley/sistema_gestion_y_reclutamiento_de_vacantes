<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>
    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Reportes</h2>
                <p class="text-muted">Estadísticas y análisis del sistema con datos de la base de datos.</p>
            </div>
        </div>

        <div id="alertaReportes"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold" id="statVacantes">0</h3><p class="text-muted mb-0">Vacantes Publicadas</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-person-video3 text-warning"></i></div><div><h3 class="fw-bold" id="statEntrevistas">0</h3><p class="text-muted mb-0">Entrevistas Realizadas</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-bar-chart-fill text-success"></i></div><div><h3 class="fw-bold" id="statContrataciones">0</h3><p class="text-muted mb-0">Contrataciones</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-x-circle-fill text-danger"></i></div><div><h3 class="fw-bold" id="statRechazados">0</h3><p class="text-muted mb-0">Candidatos Rechazados</p></div></div></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-people-fill text-info"></i></div><div><h3 class="fw-bold" id="statCandidatos">0</h3><p class="text-muted mb-0">Candidatos registrados</p></div></div></div>
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-secondary-subtle"><i class="bi bi-person-badge-fill text-secondary"></i></div><div><h3 class="fw-bold" id="statReclutadores">0</h3><p class="text-muted mb-0">Reclutadores registrados</p></div></div></div>
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-send-fill text-success"></i></div><div><h3 class="fw-bold" id="statPostulaciones">0</h3><p class="text-muted mb-0">Total postulaciones</p></div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Candidatos por estado de postulación</h5>
                    <div id="porEstadoPostulacion"></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes por estado</h5>
                    <div id="vacantesPorEstado"></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Vacantes por categoría</h5>
                    <div id="vacantesPorCategoria"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-box">
                    <h5 class="fw-bold mb-4">Últimas vacantes</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr><th>Vacante</th><th>Categoría</th><th>Estado</th><th>Postulaciones</th></tr>
                            </thead>
                            <tbody id="ultimasVacantes">
                                <tr><td colspan="4" class="text-center text-muted py-4">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const ESTADOS_ORDEN = ['Postulado', 'En revisión', 'Entrevista', 'Contratado', 'Rechazado'];
const BADGES_POST = { Postulado: 'bg-secondary', 'En revisión': 'bg-warning text-dark', Entrevista: 'bg-info', Contratado: 'bg-success', Rechazado: 'bg-danger' };

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaReportes').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function barra(labelHtml, total, max) {
    const pct = max > 0 ? Math.round((total / max) * 100) : 0;
    return `
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1"><span>${labelHtml}</span><strong>${total}</strong></div>
            <div class="progress" role="progressbar" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: ${pct}%"></div>
            </div>
        </div>`;
}

async function cargar() {
    const [vacRes, postRes, entRes, candRes, recRes] = await Promise.all([
        Api.get('vacantes', { limit: 200 }),
        Api.get('postulaciones', { limit: 200 }),
        Api.get('entrevistas', { estado: 'Realizada' }),
        Api.get('candidatos', { limit: 1 }),
        Api.get('reclutadores', { limit: 200 }),
    ]);

    if (!vacRes.ok || !postRes.ok) {
        mostrarAlerta('No se pudieron cargar todas las estadísticas del sistema.', 'warning');
    }

    const vacantes = vacRes.ok ? vacRes.data : [];
    const postulaciones = postRes.ok ? postRes.data : [];
    const entrevistas = entRes.ok ? entRes.data : [];
    const reclutadores = recRes.ok ? recRes.data : [];

    document.getElementById('statVacantes').textContent = vacRes.ok ? (vacRes.meta ? vacRes.meta.total : vacantes.length) : 0;
    document.getElementById('statEntrevistas').textContent = entrevistas.length;
    document.getElementById('statContrataciones').textContent = postulaciones.filter(p => p.estado_nombre === 'Contratado').length;
    document.getElementById('statRechazados').textContent = postulaciones.filter(p => p.estado_nombre === 'Rechazado').length;
    document.getElementById('statCandidatos').textContent = candRes.ok && candRes.meta ? candRes.meta.total : 0;
    document.getElementById('statReclutadores').textContent = reclutadores.length;
    document.getElementById('statPostulaciones').textContent = postRes.ok ? (postRes.meta ? postRes.meta.total : postulaciones.length) : 0;

    // Postulaciones por estado
    const conteoEstados = {};
    ESTADOS_ORDEN.forEach(e => conteoEstados[e] = 0);
    postulaciones.forEach(p => { conteoEstados[p.estado_nombre] = (conteoEstados[p.estado_nombre] || 0) + 1; });
    const maxEstado = Math.max(1, ...Object.values(conteoEstados));
    document.getElementById('porEstadoPostulacion').innerHTML = ESTADOS_ORDEN.map(e =>
        barra(`<span class="badge ${BADGES_POST[e] || 'bg-secondary'}">${e}</span>`, conteoEstados[e], maxEstado)
    ).join('');

    // Vacantes por estado (activa/inactiva)
    const activas = vacantes.filter(v => v.activa == 1).length;
    const inactivas = vacantes.filter(v => v.activa == 0).length;
    document.getElementById('vacantesPorEstado').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3"><span><span class="badge bg-success">Activo</span></span><strong>${activas}</strong></div>
        <div class="d-flex justify-content-between align-items-center"><span><span class="badge bg-danger">Inactivo</span></span><strong>${inactivas}</strong></div>`;

    // Vacantes por categoría
    const conteoCategorias = {};
    vacantes.forEach(v => { conteoCategorias[v.categoria] = (conteoCategorias[v.categoria] || 0) + 1; });
    const entradasCategorias = Object.entries(conteoCategorias).sort((a, b) => b[1] - a[1]);
    const maxCategoria = Math.max(1, ...entradasCategorias.map(e => e[1]));
    document.getElementById('vacantesPorCategoria').innerHTML = entradasCategorias.length
        ? entradasCategorias.map(([cat, total]) => barra(cat, total, maxCategoria)).join('')
        : '<p class="text-muted mb-0">No hay vacantes registradas.</p>';

    // Últimas vacantes
    const ultimas5 = [...vacantes].sort((a, b) => b.id - a.id).slice(0, 5);
    document.getElementById('ultimasVacantes').innerHTML = ultimas5.length
        ? ultimas5.map(v => `
            <tr>
                <td>${v.trabajo}</td>
                <td>${v.categoria}</td>
                <td><span class="badge ${v.activa == 1 ? 'bg-success' : 'bg-danger'}">${v.activa == 1 ? 'Activo' : 'Inactivo'}</span></td>
                <td>${v.total_postulaciones ?? 0}</td>
            </tr>`).join('')
        : '<tr><td colspan="4" class="text-center text-muted py-4">Sin información.</td></tr>';
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include 'includes/footer.php'; ?>

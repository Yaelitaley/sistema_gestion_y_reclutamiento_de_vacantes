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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Mi Perfil</h2>
                <p class="text-muted">Consulta la información de tu perfil como reclutador.</p>
            </div>
        </div>

        <div id="alertaPerfil"></div>

        <div id="contenidoPerfil">
            <div class="table-box text-center text-muted py-5">Cargando tu perfil...</div>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaPerfil').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

async function cargar() {
    const recRes = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });
    if (!recRes.ok || !recRes.data) {
        mostrarAlerta(recRes.message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }
    const r = recRes.data;

    const [userRes, empresaRes, vacRes, postRes, entRes] = await Promise.all([
        Api.getOne('usuarios', r.usuario_id),
        r.empresa_id ? Api.getOne('empresas', r.empresa_id) : Promise.resolve({ ok: false }),
        Api.get('vacantes', { reclutador_id: r.id, limit: 200 }),
        Api.get('postulaciones', { reclutador_id: r.id, limit: 200 }),
        Api.get('entrevistas', { reclutador_id: r.id, estado: 'Realizada' }),
    ]);

    const correo = userRes.ok && userRes.data ? userRes.data.correo : '';
    const creadoEl = userRes.ok && userRes.data ? new Date(userRes.data.created_at) : null;
    const empresaNombre = empresaRes.ok && empresaRes.data ? empresaRes.data.nombre : '';

    const vacantesPublicadas = vacRes.ok ? vacRes.data.length : 0;
    const candidatosGestionados = postRes.ok ? new Set(postRes.data.map(p => p.candidato_id)).size : 0;
    const entrevistasRealizadas = entRes.ok ? entRes.data.length : 0;

    const fotoSrc = r.foto_perfil ? `../${r.foto_perfil}?v=${Date.now()}` : '../assets/img/reclutador-avatar.png';
    const estadoTexto = (r.estado || '').charAt(0).toUpperCase() + (r.estado || '').slice(1);
    const estadoBadge = (r.estado || '').toLowerCase() === 'activo' ? 'bg-success' : 'bg-secondary';

    document.getElementById('contenidoPerfil').innerHTML = `
        <div class="row">
            <div class="col-lg-4">
                <div class="table-box text-center">
                    <img src="${fotoSrc}" class="rounded-circle shadow mb-3" width="180" height="180" style="object-fit:cover;" alt="Reclutador">
                    <h3 class="fw-bold">${esc(r.nombre_completo)}</h3>
                    <p class="text-muted">Reclutador</p>
                    <span class="badge ${estadoBadge}">${esc(estadoTexto)}</span>
                    <hr>
                    <a href="editar_perfil.php" class="btn btn-reclutador"><i class="bi bi-pencil-fill me-2"></i>Editar Perfil</a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="table-box">
                    <h4 class="fw-bold mb-4">Información Personal</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr><th width="35%">Nombre Completo</th><td>${esc(r.nombre_completo)}</td></tr>
                            <tr><th>Correo Electrónico</th><td>${esc(correo)}</td></tr>
                            <tr><th>Teléfono</th><td>${esc(r.telefono) || 'No registrado'}</td></tr>
                            <tr><th>Empresa</th><td>${esc(empresaNombre) || 'Sin empresa'}</td></tr>
                            <tr><th>Fecha de Registro</th><td>${creadoEl ? creadoEl.toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' }) : 'No disponible'}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold">${vacantesPublicadas}</h3><p class="mb-0">Vacantes Publicadas</p></div></div></div>
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-people-fill text-success"></i></div><div><h3 class="fw-bold">${candidatosGestionados}</h3><p class="mb-0">Candidatos Gestionados</p></div></div></div>
            <div class="col-md-4"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-calendar-check-fill text-warning"></i></div><div><h3 class="fw-bold">${entrevistasRealizadas}</h3><p class="mb-0">Entrevistas Realizadas</p></div></div></div>
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

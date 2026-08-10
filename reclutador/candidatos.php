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
                <h2 class="fw-bold">Gestión de Candidatos</h2>
                <p class="text-muted">Consulta los candidatos postulados a tus vacantes.</p>
            </div>
        </div>

        <div id="alertaCandidatos"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-people-fill text-primary"></i></div><div><h3 class="fw-bold" id="statTotal">0</h3><p class="mb-0 text-muted">Total Candidatos</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-search text-warning"></i></div><div><h3 class="fw-bold" id="statRevision">0</h3><p class="mb-0 text-muted">En Revisión</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-info-subtle"><i class="bi bi-calendar-event-fill text-info"></i></div><div><h3 class="fw-bold" id="statEntrevista">0</h3><p class="mb-0 text-muted">Entrevistas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-person-check-fill text-success"></i></div><div><h3 class="fw-bold" id="statContratados">0</h3><p class="mb-0 text-muted">Contratados</p></div></div>
            </div>
        </div>

        <div class="table-responsive">
            <form id="formFiltrosCandidatos" class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Lista de Candidatos</h4>
                <div class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscarCandidato" class="form-control" placeholder="Buscar candidato...">
                    </div>
                    <select id="filtroEstado" class="form-select">
                        <option value="Todos">Todos</option>
                        <option value="En revisión">En revisión</option>
                        <option value="Entrevista">Entrevista</option>
                        <option value="Contratado">Contratado</option>
                        <option value="Rechazado">Rechazado</option>
                    </select>
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Vacante</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyCandidatos">
                    <tr><td colspan="6" class="text-center text-muted py-4">Cargando candidatos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
let reclutadorId = null;
let todasLasPostulaciones = [];

const MAPA_BADGE = {
    'Postulado':   'bg-secondary',
    'En revisión': 'bg-warning text-dark',
    'Entrevista':  'bg-info',
    'Rechazado':   'bg-danger',
    'Contratado':  'bg-success',
};

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaCandidatos').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function badgeEstado(nombre) {
    const clase = MAPA_BADGE[nombre] || 'bg-secondary';
    return `<span class="badge ${clase}">${nombre}</span>`;
}

function formatoFecha(fecha) {
    if (!fecha) return '-';
    return new Date(fecha).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function fotoCandidatoSrc(fotoPerfil) {
    return fotoPerfil ? `../${fotoPerfil}` : '../assets/img/candidato02.png';
}

function renderTabla(postulaciones) {
    const tbody = document.getElementById('tbodyCandidatos');

    if (!postulaciones.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Todavía no hay candidatos postulados a tus vacantes.</td></tr>';
        return;
    }

    tbody.innerHTML = postulaciones.map(p => `
        <tr>
            <td><img src="${fotoCandidatoSrc(p.candidato_foto)}" width="50" height="50" class="rounded-circle" style="object-fit:cover;" onerror="this.onerror=null;this.src='../assets/img/candidato02.png';"></td>
            <td>${p.candidato_nombre ?? '-'}</td>
            <td>${p.trabajo ?? '-'}</td>
            <td>${badgeEstado(p.estado_nombre)}</td>
            <td>${formatoFecha(p.fecha_postulacion || p.created_at)}</td>
            <td class="text-center">
                <a href="ver_candidatos.php?id=${p.id}" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i> Ver</a>
            </td>
        </tr>
    `).join('');
}

function actualizarStats(postulaciones) {
    document.getElementById('statTotal').textContent = postulaciones.length;
    document.getElementById('statRevision').textContent = postulaciones.filter(p => p.estado_nombre === 'En revisión').length;
    document.getElementById('statEntrevista').textContent = postulaciones.filter(p => p.estado_nombre === 'Entrevista').length;
    document.getElementById('statContratados').textContent = postulaciones.filter(p => p.estado_nombre === 'Contratado').length;
}

function aplicarFiltros() {
    const texto = document.getElementById('buscarCandidato').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;

    const filtradas = todasLasPostulaciones.filter(p => {
        const coincideTexto = !texto || [p.candidato_nombre, p.trabajo].join(' ').toLowerCase().includes(texto);
        const coincideEstado = estado === 'Todos' || p.estado_nombre === estado;
        return coincideTexto && coincideEstado;
    });

    renderTabla(filtradas);
}

async function cargarCandidatos() {
    const { ok, data, message } = await Api.get('postulaciones', { reclutador_id: reclutadorId, limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar los candidatos.', 'danger');
        return;
    }

    todasLasPostulaciones = data;
    actualizarStats(data);
    aplicarFiltros();
}

async function inicializar() {
    const { ok, data, message } = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });

    if (!ok || !data) {
        mostrarAlerta(message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }

    reclutadorId = data.id;
    await cargarCandidatos();
}

document.getElementById('formFiltrosCandidatos').addEventListener('submit', function (e) {
    e.preventDefault();
    aplicarFiltros();
});
document.getElementById('buscarCandidato').addEventListener('input', aplicarFiltros);
document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);

document.addEventListener('DOMContentLoaded', inicializar);
</script>

<?php include "includes/footer.php"; ?>
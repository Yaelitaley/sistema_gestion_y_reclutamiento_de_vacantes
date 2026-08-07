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
            <h4 class="fw-bold">Lista de Entrevistas</h4>
            <div class="d-flex gap-2">
                <form id="formBuscar" class="d-flex gap-2">
                    <input type="text" id="buscarEntrevista" class="form-control" placeholder="Buscar entrevista...">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </form>
                <a href="crear_entrevista.php" class="btn btn-reclutador"><i class="bi bi-plus-circle-fill me-2"></i>Nueva Entrevista</a>
            </div>
        </div>

        <div id="alertaEntrevistas"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-clock-fill text-warning"></i></div><div><h3 class="fw-bold" id="statProgramadas">0</h3><p class="mb-0">Programadas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold" id="statRealizadas">0</h3><p class="mb-0">Realizadas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-x-circle-fill text-danger"></i></div><div><h3 class="fw-bold" id="statCanceladas">0</h3><p class="mb-0">Canceladas</p></div></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Candidato</th>
                        <th>Vacante</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyEntrevistas">
                    <tr><td colspan="6" class="text-center text-muted py-4">Cargando entrevistas...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
let reclutadorId = null;
let todasLasEntrevistas = [];

const BADGES = {
    'Programada': 'bg-warning text-dark',
    'Realizada':  'bg-success',
    'Cancelada':  'bg-danger',
};

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaEntrevistas').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function renderTabla(entrevistas) {
    const tbody = document.getElementById('tbodyEntrevistas');

    if (!entrevistas.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay entrevistas registradas.</td></tr>';
        return;
    }

    tbody.innerHTML = entrevistas.map(ent => {
        const fecha = new Date(ent.fecha);
        const acciones = ent.estado === 'Programada'
            ? `<button class="btn btn-success btn-sm" title="Marcar como realizada" onclick="cambiarEstado(${ent.id}, 'Realizada')"><i class="bi bi-check-lg"></i></button>
               <button class="btn btn-danger btn-sm" title="Cancelar" onclick="cambiarEstado(${ent.id}, 'Cancelada')"><i class="bi bi-x-lg"></i></button>`
            : '';

        return `
            <tr>
                <td>${ent.candidato_nombre}</td>
                <td>${ent.vacante_trabajo}</td>
                <td>${fecha.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
                <td>${fecha.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })}</td>
                <td><span class="badge ${BADGES[ent.estado] || 'bg-secondary'}">${ent.estado}</span></td>
                <td class="text-center">
                    <a href="ver_entrevista.php?id=${ent.id}" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i> Ver</a>
                    ${acciones}
                </td>
            </tr>`;
    }).join('');
}

function actualizarStats(entrevistas) {
    document.getElementById('statProgramadas').textContent = entrevistas.filter(e => e.estado === 'Programada').length;
    document.getElementById('statRealizadas').textContent = entrevistas.filter(e => e.estado === 'Realizada').length;
    document.getElementById('statCanceladas').textContent = entrevistas.filter(e => e.estado === 'Cancelada').length;
}

function aplicarFiltro() {
    const texto = document.getElementById('buscarEntrevista').value.toLowerCase().trim();
    const filtradas = !texto
        ? todasLasEntrevistas
        : todasLasEntrevistas.filter(e => [e.candidato_nombre, e.vacante_trabajo].join(' ').toLowerCase().includes(texto));
    renderTabla(filtradas);
}

async function cargarEntrevistas() {
    const { ok, data, message } = await Api.get('entrevistas', { reclutador_id: reclutadorId });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar las entrevistas.', 'danger');
        return;
    }

    todasLasEntrevistas = data;
    actualizarStats(data);
    aplicarFiltro();
}

async function cambiarEstado(id, estado) {
    if (estado === 'Cancelada' && !confirm('¿Cancelar esta entrevista?')) return;

    const { ok, message } = await Api.patch('entrevistas', id, { estado });

    if (!ok) {
        mostrarAlerta(message || 'No se pudo actualizar la entrevista.', 'danger');
        return;
    }

    mostrarAlerta('Estado de la entrevista actualizado.', 'success');
    await cargarEntrevistas();
}

async function inicializar() {
    const { ok, data, message } = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });

    if (!ok || !data) {
        mostrarAlerta(message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }

    reclutadorId = data.id;
    await cargarEntrevistas();
}

document.getElementById('formBuscar').addEventListener('submit', function (e) {
    e.preventDefault();
    aplicarFiltro();
});
document.getElementById('buscarEntrevista').addEventListener('input', aplicarFiltro);

document.addEventListener('DOMContentLoaded', inicializar);
</script>

<?php include "includes/footer.php"; ?>

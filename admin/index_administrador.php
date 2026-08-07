<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

include 'includes/header.php';
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>
    <div class="content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Administradores</h3>
                <p class="text-muted">Administra los demás administradores registrados en el sistema.</p>
            </div>
            <a href="../admin/register.php" class="btn btn-reclutador">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Administrador
            </a>
        </div>

        <div id="alertaAdmins"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-shield-lock-fill text-primary"></i></div><div><h3 class="fw-bold" id="statTotal">0</h3><p class="text-muted mb-0">Total admins</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold" id="statActivos">0</h3><p class="text-muted mb-0">Activos</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-clock-fill text-warning"></i></div><div><h3 class="fw-bold" id="statPendientes">0</h3><p class="text-muted mb-0">Pendientes</p></div></div></div>
            <div class="col-md-3"><div class="dashboard-card"><div class="card-icon bg-danger-subtle"><i class="bi bi-lock-fill text-danger"></i></div><div><h3 class="fw-bold" id="statBloqueados">0</h3><p class="text-muted mb-0">Bloqueados</p></div></div></div>
        </div>

        <div class="table-box">
            <form id="formFiltrosAdmins" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="text" id="buscarAdmin" class="form-control" placeholder="Buscar por nombre, correo o empresa...">
                </div>
                <div class="col-md-4">
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Bloqueado">Bloqueado</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-reclutador w-100" type="submit"><i class="bi bi-search me-2"></i>Filtrar</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nombre</th><th>Correo</th><th>Empresa</th><th>Rol</th><th>Estado</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAdmins">
                        <tr><td colspan="7" class="text-center text-muted py-4">Cargando administradores...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="javascript:history.back()" class="cancel-link">Regresar</a>
        </div>
    </div>
</div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

const BADGES = { Activo: 'bg-success', Pendiente: 'bg-warning text-dark', Bloqueado: 'bg-danger', Inactivo: 'bg-secondary' };

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaAdmins').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function renderTarjetas(admins) {
    document.getElementById('statTotal').textContent = admins.length;
    document.getElementById('statActivos').textContent = admins.filter(a => a.estado === 'Activo').length;
    document.getElementById('statPendientes').textContent = admins.filter(a => a.estado === 'Pendiente').length;
    document.getElementById('statBloqueados').textContent = admins.filter(a => a.estado === 'Bloqueado').length;
}

function renderTabla(admins) {
    const tbody = document.getElementById('tbodyAdmins');

    if (!admins.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay administradores registrados.</td></tr>';
        return;
    }

    tbody.innerHTML = admins.map(a => {
        const puedeEliminar = Number(a.usuario_id) !== SESSION_USUARIO_ID;
        return `
            <tr>
                <td>${a.id}</td>
                <td>${a.nombre_completo}</td>
                <td>${a.correo}</td>
                <td>${a.empresa ?? ''}</td>
                <td>${Number(a.rol_id) === 1 ? 'Principal' : 'Administrador'}</td>
                <td><span class="badge ${BADGES[a.estado] || 'bg-secondary'}">${a.estado}</span></td>
                <td>
                    <a href="../admin/edit_administrador.php?id=${a.id}" class="btn btn-warning btn-sm" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                    ${puedeEliminar
                        ? `<button class="btn btn-danger btn-sm" title="Eliminar" onclick="eliminarAdmin(${a.id})"><i class="bi bi-trash-fill"></i></button>`
                        : ''}
                </td>
            </tr>`;
    }).join('');
}

let todosLosAdmins = [];

function aplicarFiltros() {
    const texto = document.getElementById('buscarAdmin').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;

    const filtrados = todosLosAdmins.filter(a => {
        const coincideTexto = !texto || [a.nombre_completo, a.correo, a.empresa].join(' ').toLowerCase().includes(texto);
        const coincideEstado = !estado || a.estado === estado;
        return coincideTexto && coincideEstado;
    });

    renderTabla(filtrados);
}

async function cargar() {
    const { ok, data, message } = await Api.get('administradores');

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar los administradores.', 'danger');
        return;
    }

    todosLosAdmins = data;
    renderTarjetas(data);
    aplicarFiltros();
}

async function eliminarAdmin(id) {
    if (!confirm('¿Deseas eliminar este administrador?')) return;

    const { ok, message } = await Api.del('administradores', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo eliminar.', 'danger');
        return;
    }

    mostrarAlerta('Administrador eliminado correctamente.', 'success');
    await cargar();
}

document.getElementById('formFiltrosAdmins').addEventListener('submit', function (e) {
    e.preventDefault();
    aplicarFiltros();
});
document.getElementById('buscarAdmin').addEventListener('input', aplicarFiltros);
document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include 'includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';

require_admin_login();

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Todos los Usuarios</h3>
                <p class="text-muted">Vista global de administradores, reclutadores y candidatos registrados.</p>
            </div>
        </div>

        <div id="alertaUsuarios"></div>

        <div class="row g-4 mb-4" id="tarjetasRoles"></div>

        <div class="table-responsive">
            <form id="formFiltrosUsuarios" class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="fw-bold mb-0">Lista de Usuarios</h4>
                <div class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar por nombre o correo...">
                    </div>
                    <select id="filtroRol" class="form-select">
                        <option value="0">Todos los roles</option>
                    </select>
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Registrado</th></tr>
                </thead>
                <tbody id="tbodyUsuarios">
                    <tr><td colspan="5" class="text-center text-muted py-4">Cargando usuarios...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
let roles = [];
let todosLosUsuarios = [];

const BADGES_ESTADO = {
    'Activo': 'bg-success', 'Pendiente': 'bg-warning text-dark',
    'Bloqueado': 'bg-danger', 'Inactivo': 'bg-secondary',
};

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaUsuarios').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function nombreRol(rolId) {
    const r = roles.find(r => Number(r.id) === Number(rolId));
    return r ? r.nombre : 'Sin rol';
}

function renderTarjetas(usuarios) {
    const conteo = {};
    usuarios.forEach(u => {
        const nombre = nombreRol(u.rol_id);
        conteo[nombre] = (conteo[nombre] || 0) + 1;
    });

    document.getElementById('tarjetasRoles').innerHTML = Object.entries(conteo).map(([nombre, total]) => `
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="card-icon bg-primary-subtle"><i class="bi bi-people-fill text-primary"></i></div>
                <div><h3 class="fw-bold">${total}</h3><p class="mb-0 text-muted">${nombre}s</p></div>
            </div>
        </div>
    `).join('');
}

function renderTabla(usuarios) {
    const tbody = document.getElementById('tbodyUsuarios');

    if (!usuarios.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron usuarios.</td></tr>';
        return;
    }

    tbody.innerHTML = usuarios.map(u => `
        <tr>
            <td>${u.nombre_completo ?? '-'}</td>
            <td>${u.email ?? u.correo ?? '-'}</td>
            <td><span class="badge bg-info text-dark">${nombreRol(u.rol_id)}</span></td>
            <td><span class="badge ${BADGES_ESTADO[u.estado] || 'bg-secondary'}">${u.estado ?? '-'}</span></td>
            <td>${u.created_at ? new Date(u.created_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-'}</td>
        </tr>
    `).join('');
}

function aplicarFiltros() {
    const texto = document.getElementById('buscarUsuario').value.toLowerCase().trim();
    const rol = Number(document.getElementById('filtroRol').value);

    const filtrados = todosLosUsuarios.filter(u => {
        const coincideTexto = !texto || [u.nombre_completo, u.email, u.correo].join(' ').toLowerCase().includes(texto);
        const coincideRol = rol === 0 || Number(u.rol_id) === rol;
        return coincideTexto && coincideRol;
    });

    renderTabla(filtrados);
}

async function cargar() {
    const [rolesRes, usuariosRes] = await Promise.all([
        Api.get('roles', { limit: 50 }),
        Api.get('usuarios', { limit: 200 }),
    ]);

    if (rolesRes.ok) {
        roles = rolesRes.data;
        const select = document.getElementById('filtroRol');
        roles.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.nombre;
            select.appendChild(opt);
        });
    }

    if (!usuariosRes.ok) {
        mostrarAlerta(usuariosRes.message || 'No se pudieron cargar los usuarios.', 'danger');
        return;
    }

    todosLosUsuarios = usuariosRes.data;
    renderTarjetas(todosLosUsuarios);
    aplicarFiltros();
}

document.getElementById('formFiltrosUsuarios').addEventListener('submit', function (e) {
    e.preventDefault();
    aplicarFiltros();
});
document.getElementById('buscarUsuario').addEventListener('input', aplicarFiltros);
document.getElementById('filtroRol').addEventListener('change', aplicarFiltros);

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

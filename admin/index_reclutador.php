<?php
require_once '../config/config.php';
require_once '../config/app_helpers.php';

require_admin_login();

include "includes/header.php";
?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>
    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Reclutadores</h3>
                <p class="text-muted">Administra los reclutadores registrados en el sistema.</p>
            </div>

            <a href="../reclutador/register.php" class="btn btn-reclutador">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Reclutador
            </a>
        </div>

        <div id="alertaReclutadores"></div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyReclutadores">
                    <tr><td colspan="6" class="text-center text-muted py-4">Cargando reclutadores...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


</div>

<script src="../assets/js/api-client.js"></script>
<script>
const BADGES = { activo: 'bg-success', pendiente: 'bg-warning text-dark', bloqueado: 'bg-danger' };

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaReclutadores').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function renderTabla(reclutadores) {
    const tbody = document.getElementById('tbodyReclutadores');

    if (!reclutadores.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay reclutadores registrados.</td></tr>';
        return;
    }

    tbody.innerHTML = reclutadores.map(r => {
        const estado = (r.estado || '').toLowerCase();
        const badge = BADGES[estado] || 'bg-secondary';
        return `
            <tr>
                <td>${r.id}</td>
                <td>${r.nombre_completo}</td>
                <td>${r.correo ?? ''}</td>
                <td>${r.empresa_nombre ?? 'Sin empresa'}</td>
                <td><span class="badge ${badge}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span></td>
                <td>
                    <a href="edit_reclutador.php?id=${r.id}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill"></i></a>
                    <button class="btn btn-danger btn-sm" onclick="eliminarReclutador(${r.id})"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>`;
    }).join('');
}

async function cargarReclutadores() {
    const { ok, data, message } = await Api.get('reclutadores', { limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar los reclutadores.', 'danger');
        return;
    }

    renderTabla(data);
}

async function eliminarReclutador(id) {
    if (!confirm('¿Deseas eliminar este reclutador?')) return;

    const { ok, message } = await Api.del('reclutadores', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo eliminar el reclutador.', 'danger');
        return;
    }

    mostrarAlerta('Reclutador eliminado correctamente.', 'success');
    await cargarReclutadores();
}

document.addEventListener('DOMContentLoaded', cargarReclutadores);
</script>

<?php include "includes/footer.php"; ?>


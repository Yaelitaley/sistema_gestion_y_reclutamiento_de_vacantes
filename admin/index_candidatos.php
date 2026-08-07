<?php
require_once '../config/config.php';
require_once '../config/app_helpers.php';

require_admin_login();

include "includes/header.php";
?>

<?php include "includes/sidebar.php"; ?>
    <div class="content">
        <?php include "includes/topbar.php"; ?>

<div class="d-flex">

    <div class="content w-100 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Candidatos</h3>
                <p class="text-muted">Administra los Candidatos registrados en el sistema.</p>
            </div>

            <a href="../candidatos/register.php" class="btn btn-reclutador">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Agregar Candidato
            </a>
        </div>

        <div id="alertaCandidatos"></div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyCandidatos">
                    <tr><td colspan="6" class="text-center text-muted py-4">Cargando candidatos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="javascript:history.back()" class="cancel-link">Regresar</a>
</div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const BADGES = { activo: 'bg-success', pendiente: 'bg-warning text-dark', bloqueado: 'bg-danger' };

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaCandidatos').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function renderTabla(candidatos) {
    const tbody = document.getElementById('tbodyCandidatos');

    if (!candidatos.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay candidatos registrados.</td></tr>';
        return;
    }

    tbody.innerHTML = candidatos.map(c => {
        const estado = (c.estado || '').toLowerCase();
        const badge = BADGES[estado] || 'bg-secondary';
        return `
            <tr>
                <td>${c.id}</td>
                <td>${c.nombre_completo}</td>
                <td>${c.correo ?? ''}</td>
                <td>${c.ubicacion ?? 'Sin definir'}</td>
                <td><span class="badge ${badge}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span></td>
                <td>
                    <a href="../candidatos/edit_candidatos.php?id=${c.id}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill"></i></a>
                    <button class="btn btn-danger btn-sm" onclick="eliminarCandidato(${c.id})"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>`;
    }).join('');
}

async function cargarCandidatos() {
    const { ok, data, message } = await Api.get('candidatos', { limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar los candidatos.', 'danger');
        return;
    }

    renderTabla([...data].sort((a, b) => b.id - a.id));
}

async function eliminarCandidato(id) {
    if (!confirm('¿Deseas eliminar este candidato?')) return;

    const { ok, message } = await Api.del('candidatos', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo eliminar el candidato.', 'danger');
        return;
    }

    mostrarAlerta('Candidato eliminado correctamente.', 'success');
    await cargarCandidatos();
}

document.addEventListener('DOMContentLoaded', cargarCandidatos);
</script>

<?php include "includes/footer.php"; ?>

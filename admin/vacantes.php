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
                <h2 class="fw-bold">Gestión de Vacantes</h2>
                <p class="text-muted">Administra, filtra, edita y elimina vacantes del sistema.</p>
            </div>

            <a href="../vacantes/register.php" class="btn btn-reclutador">
                <i class="bi bi-plus-circle me-2"></i>Nueva Vacante
            </a>
        </div>

        <div id="alertaVacantes"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold" id="statActivas">0</h3><p class="text-muted mb-0">Vacantes Activas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-secondary-subtle"><i class="bi bi-file-earmark-fill text-secondary"></i></div><div><h3 class="fw-bold" id="statInactivas">0</h3><p class="text-muted mb-0">Vacantes Inactivas</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-people-fill text-success"></i></div><div><h3 class="fw-bold" id="statPostulaciones">0</h3><p class="text-muted mb-0">Total Postulaciones</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-bar-chart-fill text-warning"></i></div><div><h3 class="fw-bold" id="statCategorias">0</h3><p class="text-muted mb-0">Áreas o Categorías</p></div></div>
            </div>
        </div>

        <div class="table-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold">Lista de Vacantes</h5>
            </div>

            <form id="formFiltrosVacantes" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar vacante...">
                </div>

                <div class="col-md-3">
                    <select id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-reclutador w-100" type="submit"><i class="bi bi-search me-2"></i>Aplicar filtros</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Vacante</th>
                            <th>Empresa</th>
                            <th>Ubicación</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Postulaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyVacantes">
                        <tr><td colspan="7" class="text-center text-muted py-4">Cargando vacantes...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
let todasLasVacantes = []; // usada para poblar el select de categorías

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaVacantes').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function textoCorto(texto, limite = 65) {
    texto = (texto || '').toString();
    return texto.length > limite ? texto.slice(0, limite - 3) + '...' : texto;
}

function badgeEstado(activa) {
    return activa == 1
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-danger">Inactivo</span>';
}

function renderTabla(vacantes) {
    const tbody = document.getElementById('tbodyVacantes');

    if (!vacantes.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay vacantes registradas.</td></tr>';
        return;
    }

    tbody.innerHTML = vacantes.map(v => `
        <tr>
            <td>
                <strong>${v.trabajo}</strong><br>
                <small class="text-muted">${textoCorto(v.descripcion)}</small>
            </td>
            <td>${v.empresa_nombre ?? 'Sin Empresa'}</td>
            <td>${v.ubicacion}</td>
            <td><span class="badge bg-primary">${v.categoria}</span></td>
            <td>${badgeEstado(v.activa)}</td>
            <td>${v.total_postulaciones ?? 0}</td>
            <td>
                <a href="../vacantes/edit_vacante.php?id=${v.id}" class="btn btn-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-danger btn-sm" title="Eliminar" onclick="eliminarVacante(${v.id})">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function actualizarEstadisticas(vacantes) {
    document.getElementById('statActivas').textContent = vacantes.filter(v => v.activa == 1).length;
    document.getElementById('statInactivas').textContent = vacantes.filter(v => v.activa == 0).length;
    document.getElementById('statPostulaciones').textContent = vacantes.reduce((acc, v) => acc + Number(v.total_postulaciones || 0), 0);
    document.getElementById('statCategorias').textContent = new Set(vacantes.map(v => v.categoria).filter(Boolean)).size;
}

function poblarCategorias(vacantes) {
    const select = document.getElementById('categoria');
    const actuales = new Set(Array.from(select.options).map(o => o.value));
    const valorActual = select.value;

    [...new Set(vacantes.map(v => v.categoria).filter(Boolean))].sort().forEach(cat => {
        if (!actuales.has(cat)) {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            select.appendChild(opt);
        }
    });

    select.value = valorActual;
}

async function cargarVacantes() {
    // Traemos todas (sin filtro de texto) para estadísticas/categorías reales,
    // y aplicamos el filtro de búsqueda/estado/categoría vía la API.
    const baseRes = await Api.get('vacantes', { limit: 200 });
    if (baseRes.ok) {
        todasLasVacantes = baseRes.data;
        actualizarEstadisticas(baseRes.data);
        poblarCategorias(baseRes.data);
    }

    const estado = document.getElementById('estado').value;
    const filtros = {
        limit: 200,
        buscar: document.getElementById('buscar').value.trim(),
        categoria: document.getElementById('categoria').value,
    };
    if (estado === 'Activo') filtros.activa = 1;
    if (estado === 'Inactivo') filtros.activa = 0;

    const { ok, data, message } = await Api.get('vacantes', filtros);

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar las vacantes.', 'danger');
        return;
    }

    renderTabla(data);
}

async function eliminarVacante(id) {
    if (!confirm('¿Deseas eliminar esta vacante?')) return;

    const { ok, message } = await Api.del('vacantes', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo eliminar la vacante.', 'danger');
        return;
    }

    mostrarAlerta('Vacante eliminada correctamente.', 'success');
    await cargarVacantes();
}

document.getElementById('formFiltrosVacantes').addEventListener('submit', function (e) {
    e.preventDefault();
    cargarVacantes();
});

document.addEventListener('DOMContentLoaded', cargarVacantes);
</script>

<?php include 'includes/footer.php'; ?>

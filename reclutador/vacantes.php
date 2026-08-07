<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';


if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

include 'includes/header.php';
?>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="content w-100 p-4">
        <?php include 'includes/topbar.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Mis Vacantes</h2>
                <p class="text-muted">Administra las vacantes que has publicado.</p>
            </div>
            <button type="button" class="btn btn-reclutador" data-bs-toggle="modal" data-bs-target="#modalVacante" onclick="nuevaVacante()">
                <i class="bi bi-plus-circle-fill me-2"></i>Nueva Vacante
            </button>
        </div>

        <div id="alertaVacantes"></div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold" id="statTotal">0</h3><p class="mb-0">Total de Vacantes</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold" id="statActivas">0</h3><p class="mb-0">Activas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-people-fill text-warning"></i></div><div><h3 class="fw-bold" id="statPostulaciones">0</h3><p class="mb-0">Postulaciones Recibidas</p></div></div>
            </div>
        </div>

        <div class="table-box">

            <!-- Buscador y filtro -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input
                        type="text"
                        id="buscarVacante"
                        class="form-control"
                        autocomplete="off"
                        placeholder="🔍 Buscar por puesto, categoría o ubicación...">
                </div>

                <div class="col-md-4">
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todas las vacantes</option>
                        <option value="Activo">Activas</option>
                        <option value="Inactivo">Inactivas</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="tablaVacantes">
                    <thead>
                        <tr>
                            <th>Puesto</th>
                            <th>Categoría</th>
                            <th>Ubicación</th>
                            <th>Salario</th>
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

<!-- MODAL CREAR / EDITAR -->
<div class="modal fade" id="modalVacante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formVacante">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVacanteTitulo">Nueva Vacante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="f_id" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Puesto</label>
                            <input type="text" id="f_trabajo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <input type="text" id="f_categoria" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" id="f_ubicacion" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Salario</label>
                            <input type="number" step="0.01" id="f_salario" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nivel de experiencia</label>
                            <input type="text" id="f_nivel" class="form-control" placeholder="Ej. Junior, Senior" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea id="f_descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos</label>
                        <textarea id="f_requisitos" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="f_activa" checked>
                        <label class="form-check-label" for="f_activa">Vacante activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-reclutador" id="btnGuardarVacante">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

let reclutadorId = null;
let todasLasVacantes = [];
let modalVacanteEl = null;

function mostrarAlerta(mensaje, tipo) {
    const cont = document.getElementById('alertaVacantes');
    cont.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function formatoSalario(salario) {
    return (salario !== null && salario !== '' && salario !== undefined)
        ? '$' + Number(salario).toLocaleString('es-MX', { minimumFractionDigits: 2 })
        : 'No especificado';
}

function badgeEstado(activa) {
    return activa == 1
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-secondary">Inactivo</span>';
}

function textoCorto(texto, limite = 60) {
    texto = (texto || '').toString();
    return texto.length > limite ? texto.slice(0, limite - 3) + '...' : texto;
}

function renderVacantes(vacantes) {
    const tbody = document.getElementById('tbodyVacantes');

    if (!vacantes.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Aún no has publicado vacantes.</td></tr>';
    } else {
        tbody.innerHTML = vacantes.map(v => `
            <tr>
                <td><strong>${v.trabajo}</strong><br><small class="text-muted">${textoCorto(v.descripcion)}</small></td>
                <td><span class="badge bg-primary">${v.categoria}</span></td>
                <td>${v.ubicacion}</td>
                <td>${formatoSalario(v.salario)}</td>
                <td>${badgeEstado(v.activa)}</td>
                <td>${v.total_postulaciones ?? 0}</td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm" onclick="editarVacante(${v.id})" data-bs-toggle="modal" data-bs-target="#modalVacante">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarVacante(${v.id})">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                    <a href="ver_vacante.php?id=${v.id}" class="btn btn-info btn-sm" title="Ver vacante">
                        <i class="bi bi-eye-fill"></i>
                    </a>
                </td>
            </tr>
        `).join('');
    }

    document.getElementById('statTotal').textContent = vacantes.length;
    document.getElementById('statActivas').textContent = vacantes.filter(v => v.activa == 1).length;
    document.getElementById('statPostulaciones').textContent = vacantes.reduce((acc, v) => acc + Number(v.total_postulaciones || 0), 0);
}

function aplicarFiltros() {
    const texto = (document.getElementById('buscarVacante').value || '').toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;

    const filtradas = todasLasVacantes.filter(v => {
        const coincideTexto = !texto || [v.trabajo, v.categoria, v.ubicacion].join(' ').toLowerCase().includes(texto);
        const coincideEstado = !estado || (estado === 'Activo' ? v.activa == 1 : v.activa == 0);
        return coincideTexto && coincideEstado;
    });

    renderVacantes(filtradas);
}

async function cargarVacantes() {
    const { ok, data, message } = await Api.get('vacantes', { reclutador_id: reclutadorId, limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar las vacantes.', 'danger');
        return;
    }

    todasLasVacantes = data;
    aplicarFiltros();
}

async function inicializar() {
    const { ok, data, message } = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });

    if (!ok || !data) {
        mostrarAlerta(message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }

    reclutadorId = data.id;
    modalVacanteEl = new bootstrap.Modal(document.getElementById('modalVacante'));
    await cargarVacantes();
}

function nuevaVacante() {
    document.getElementById('modalVacanteTitulo').innerText = 'Nueva Vacante';
    document.getElementById('f_id').value = 0;
    document.getElementById('f_trabajo').value = '';
    document.getElementById('f_categoria').value = '';
    document.getElementById('f_ubicacion').value = '';
    document.getElementById('f_salario').value = '';
    document.getElementById('f_nivel').value = '';
    document.getElementById('f_descripcion').value = '';
    document.getElementById('f_requisitos').value = '';
    document.getElementById('f_activa').checked = true;
}

function editarVacante(id) {
    const v = todasLasVacantes.find(item => item.id == id);
    if (!v) return;

    document.getElementById('modalVacanteTitulo').innerText = 'Editar Vacante';
    document.getElementById('f_id').value = v.id;
    document.getElementById('f_trabajo').value = v.trabajo;
    document.getElementById('f_categoria').value = v.categoria;
    document.getElementById('f_ubicacion').value = v.ubicacion;
    document.getElementById('f_salario').value = v.salario ?? '';
    document.getElementById('f_nivel').value = v.nivel_experiencia;
    document.getElementById('f_descripcion').value = v.descripcion;
    document.getElementById('f_requisitos').value = v.requisitos ?? '';
    document.getElementById('f_activa').checked = v.activa == 1;
}

async function eliminarVacante(id) {
    if (!confirm('¿Eliminar esta vacante?')) return;

    const { ok, message } = await Api.del('vacantes', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo eliminar la vacante.', 'danger');
        return;
    }

    mostrarAlerta('Vacante eliminada correctamente.', 'success');
    await cargarVacantes();
}

document.getElementById('formVacante').addEventListener('submit', async function (e) {
    e.preventDefault();

    const id = parseInt(document.getElementById('f_id').value, 10);
    const salarioValor = document.getElementById('f_salario').value;

    const payload = {
        trabajo: document.getElementById('f_trabajo').value.trim(),
        categoria: document.getElementById('f_categoria').value.trim(),
        ubicacion: document.getElementById('f_ubicacion').value.trim(),
        salario: salarioValor === '' ? null : Number(salarioValor),
        nivel_experiencia: document.getElementById('f_nivel').value.trim(),
        descripcion: document.getElementById('f_descripcion').value.trim(),
        requisitos: document.getElementById('f_requisitos').value.trim(),
        activa: document.getElementById('f_activa').checked ? 1 : 0,
    };

    if (!payload.trabajo || !payload.descripcion || !payload.categoria || !payload.ubicacion || !payload.nivel_experiencia) {
        mostrarAlerta('Completa todos los campos obligatorios.', 'danger');
        return;
    }

    const btn = document.getElementById('btnGuardarVacante');
    btn.disabled = true;

    const resultado = (id > 0)
        ? await Api.patch('vacantes', id, payload)
        : await Api.post('vacantes', { ...payload, reclutador_id: reclutadorId });

    btn.disabled = false;

    if (!resultado.ok) {
        mostrarAlerta(resultado.message || 'No se pudo guardar la vacante.', 'danger');
        return;
    }

    modalVacanteEl.hide();
    mostrarAlerta(id > 0 ? 'Vacante actualizada correctamente.' : 'Vacante creada correctamente.', 'success');
    await cargarVacantes();
});

document.getElementById('buscarVacante').addEventListener('input', aplicarFiltros);
document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);

document.addEventListener('DOMContentLoaded', inicializar);
</script>

<?php include 'includes/footer.php'; ?>

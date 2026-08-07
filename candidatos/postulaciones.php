<?php
require_once '../config/config.php';

// Esta página ya no consulta "postulaciones" directamente: el listado, las
// tarjetas de resumen, los filtros y la cancelación se hacen desde el
// navegador contra la API REST (assets/api/api-postulaciones.php).
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
?>
<?php include "includes/header.php"; ?>

<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>

        <!-- TÍTULO -->
        <div class="mb-4">
            <h2 class="fw-bold">Mis Postulaciones</h2>
            <p class="text-muted">Consulta el estado de todas las vacantes a las que te has postulado.</p>
        </div>

        <div id="alertaPostulaciones"></div>

        <!-- TARJETAS -->
        <div class="row g-4 mb-5 p-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
                    <div class="card-icon bg-primary-subtle"><i class="bi bi-send-check-fill text-primary"></i></div>
                    <div><h3 class="fw-bold mb-0" id="statTotal">0</h3><p class="text-muted mb-0">Postulaciones</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
                    <div class="card-icon bg-warning-subtle"><i class="bi bi-clock-history text-warning"></i></div>
                    <div><h3 class="fw-bold mb-0" id="statRevision">0</h3><p class="text-muted mb-0">En Revisión</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
                    <div class="card-icon bg-info-subtle"><i class="bi bi-calendar-event-fill text-info"></i></div>
                    <div><h3 class="fw-bold mb-0" id="statEntrevistas">0</h3><p class="text-muted mb-0">Entrevistas</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-carde p-4 d-flex align-items-center gap-3">
                    <div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div>
                    <div><h3 class="fw-bold mb-0" id="statContratado">0</h3><p class="text-muted mb-0">Contratado</p></div>
                </div>
            </div>
        </div>

        <!-- BUSCADOR Y FILTROS -->
        <form id="formFiltrosPostulaciones">
            <div class="table-responsive mb-4">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarPostulacion" class="form-control" placeholder="Buscar empresa o puesto...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todas</option>
                            <option value="1">Postulado</option>
                            <option value="2">En revisión</option>
                            <option value="3">Entrevista</option>
                            <option value="5">Contratado</option>
                            <option value="4">Rechazado</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-candidato w-100">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- TABLA DE POSTULACIONES -->
        <div class="table-box">
            <h4 class="fw-bold mb-4">Historial de Postulaciones</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Puesto</th>
                            <th>Empresa</th>
                            <th>Ubicación</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPostulaciones">
                        <tr><td colspan="6" class="text-center text-muted py-4">Cargando postulaciones...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RESUMEN -->
        <div class="row mt-5">
            <div class="col-lg-6">
                <div class="dashboard-card p-4">
                    <div class="card-icono bg-success-subtle"><i class="bi bi-graph-up-arrow text-success"></i></div>
                    <div>
                        <h5 class="fw-bold">Resumen</h5>
                        <p class="mb-0 text-muted" id="resumenTexto">Cargando resumen...</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-card p-4">
                    <div class="card-icono bg-primary-subtle"><i class="bi bi-lightbulb-fill text-primary"></i></div>
                    <div>
                        <h5 class="fw-bold">Recomendación</h5>
                        <p class="mb-0 text-muted">
                            Mantén actualizado tu perfil y tu currículum para aumentar tus posibilidades de ser seleccionado.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="table-box mt-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="fw-bold">¿Deseas buscar nuevas oportunidades?</h4>
                    <p class="text-muted mb-0">Explora nuevas vacantes disponibles y continúa creciendo profesionalmente.</p>
                </div>
                <div class="mt-3 mt-lg-0">
                    <a href="explorar-empleos.php" class="btn btn-candidato">
                        <i class="bi bi-search me-2"></i>
                        Explorar Empleos
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

let candidatoId = null;
let todasLasPostulaciones = [];

const MAPA_BADGE = {
    'Postulado':   'bg-secondary',
    'En revisión': 'bg-warning text-dark',
    'Entrevista':  'bg-info',
    'Rechazado':   'bg-danger',
    'Contratado':  'bg-success',
};

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaPostulaciones').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function badgeEstado(nombre) {
    const clase = MAPA_BADGE[nombre] || 'bg-secondary';
    return `<span class="badge ${clase}">${nombre}</span>`;
}

function formatoFecha(fecha) {
    if (!fecha) return '-';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function renderTabla(postulaciones) {
    const tbody = document.getElementById('tbodyPostulaciones');

    if (!postulaciones.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se encontraron postulaciones.</td></tr>';
        return;
    }

    tbody.innerHTML = postulaciones.map(p => `
        <tr>
            <td>${p.trabajo}</td>
            <td>${p.empresa_nombre ?? 'Sin Empresa'}</td>
            <td>${p.ubicacion ?? '-'}</td>
            <td>${formatoFecha(p.fecha_postulacion || p.created_at)}</td>
            <td>${badgeEstado(p.estado_nombre)}</td>
            <td class="text-center">
                <a href="ver-empleo.php?id=${p.vacante_id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye-fill"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger btnCancelarPostulacion" data-postulacion-id="${p.id}">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function actualizarResumen(postulaciones) {
    const total       = postulaciones.length;
    const enRevision  = postulaciones.filter(p => Number(p.estado_id) === 2).length;
    const entrevistas = postulaciones.filter(p => Number(p.estado_id) === 3).length;
    const contratado  = postulaciones.filter(p => Number(p.estado_id) === 5).length;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statRevision').textContent = enRevision;
    document.getElementById('statEntrevistas').textContent = entrevistas;
    document.getElementById('statContratado').textContent = contratado;

    document.getElementById('resumenTexto').innerHTML =
        `Has enviado <strong>${total} postulaciones</strong>, de las cuales <strong>${enRevision}</strong> continúan en revisión y <strong>${entrevistas}</strong> ya avanzaron a entrevista.`;
}

function aplicarFiltros() {
    const texto = document.getElementById('buscarPostulacion').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;

    const filtradas = todasLasPostulaciones.filter(p => {
        const coincideTexto = !texto || [p.trabajo, p.empresa_nombre].join(' ').toLowerCase().includes(texto);
        const coincideEstado = !estado || Number(p.estado_id) === Number(estado);
        return coincideTexto && coincideEstado;
    });

    renderTabla(filtradas);
}

async function cargarPostulaciones() {
    const { ok, data, message } = await Api.get('postulaciones', { candidato_id: candidatoId, limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar tus postulaciones.', 'danger');
        return;
    }

    todasLasPostulaciones = data;
    actualizarResumen(data);
    aplicarFiltros();
}

async function inicializar() {
    const { ok, data, message } = await Api.get('candidatos', { usuario_id: SESSION_USUARIO_ID });

    if (!ok || !data) {
        mostrarAlerta(message || 'No se encontró tu perfil de candidato.', 'danger');
        return;
    }

    candidatoId = data.id;
    await cargarPostulaciones();
}

document.getElementById('formFiltrosPostulaciones').addEventListener('submit', function (e) {
    e.preventDefault();
    aplicarFiltros();
});
document.getElementById('buscarPostulacion').addEventListener('input', aplicarFiltros);
document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);

// Delegado: las filas se regeneran dinámicamente al cargar/filtrar.
document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.btnCancelarPostulacion');
    if (!boton) return;

    if (!confirm('¿Estás seguro de cancelar esta postulación?')) return;

    const id = boton.dataset.postulacionId;
    const { ok, message } = await Api.del('postulaciones', id);

    if (!ok) {
        mostrarAlerta(message || 'No se pudo cancelar la postulación.', 'danger');
        return;
    }

    mostrarAlerta('La postulación ha sido cancelada correctamente.', 'success');
    await cargarPostulaciones();
});

document.addEventListener('DOMContentLoaded', inicializar);
</script>

<?php include "includes/footer.php"; ?>

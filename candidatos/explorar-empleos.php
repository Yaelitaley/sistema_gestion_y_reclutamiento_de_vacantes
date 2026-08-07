<?php
require_once '../config/config.php';

// Esta página ya no consulta "vacantes" directamente: el listado, los
// filtros y las estadísticas se obtienen desde el navegador a través de
// la API REST (assets/api/api-vacantes.php y api-postulaciones.php).
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Explorar Empleos</h2>
                <p class="text-muted">Descubre nuevas oportunidades laborales y encuentra el empleo ideal para ti.</p>
            </div>
        </div>

        <div id="alertaEmpleos"></div>

        <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-briefcase-fill"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0" id="statTotalVacantes">0</h3>
                        <p class="text-muted mb-0">Vacantes activas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0" id="statTotalEmpresas">0</h3>
                        <p class="text-muted mb-0">Empresas contratando</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-house-door-fill"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0" id="statTotalRemotas">0</h3>
                        <p class="text-muted mb-0">Vacantes remotas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-stars"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0" id="statNuevasHoy">0</h3>
                        <p class="text-muted mb-0">Nuevas hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BUSCADOR Y FILTROS -->
        <form id="formFiltros">
            <div class="search-job mb-4">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input
                                type="text"
                                id="buscarEmpleo"
                                name="busqueda"
                                class="form-control"
                                placeholder="Puesto, empresa o palabra clave...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" id="filtroCategoria" name="categoria">
                            <option value="">Categoría</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" id="filtroUbicacion" name="ubicacion">
                            <option value="">Ubicación</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" id="filtroNivel" name="nivel_experiencia">
                            <option value="">Nivel</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" id="filtroModalidad" name="modalidad">
                            <option value="">Modalidad</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-lg-8"></div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-candidato w-100">
                            <i class="bi bi-search me-2"></i>
                            Buscar
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="button" id="btnLimpiar" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-2"></i>
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- RESULTADOS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0" id="contadorResultados">Cargando vacantes...</h4>
        </div>

        <div class="row g-4 mb-5" id="listaVacantes"></div>

    </div>
</div>

<script>
// api-client.js ya se carga globalmente en includes/footer.php del módulo candidato.
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

let candidatoId = null;
let postuladasIds = [];
let todasLasVacantesActivas = []; // usado para poblar los filtros y las estadísticas

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaEmpleos').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function badgeModalidad(modalidad) {
    const map = { Presencial: 'bg-danger', 'Híbrido': 'bg-primary', Remoto: 'bg-warning text-dark' };
    if (!modalidad) return '';
    return `<span class="badge ${map[modalidad] || 'bg-secondary'}">${modalidad}</span>`;
}

function textoCorto(texto, limite = 110) {
    texto = (texto || '').toString();
    return texto.length > limite ? texto.slice(0, limite - 3) + '...' : texto;
}

function llenarSelect(select, valores, valorActual) {
    const actuales = new Set(Array.from(select.options).map(o => o.value));
    valores.forEach(v => {
        if (v && !actuales.has(v)) {
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            select.appendChild(opt);
        }
    });
    if (valorActual) select.value = valorActual;
}

function poblarFiltrosYEstadisticas(vacantes) {
    llenarSelect(document.getElementById('filtroCategoria'), [...new Set(vacantes.map(v => v.categoria))].sort());
    llenarSelect(document.getElementById('filtroUbicacion'), [...new Set(vacantes.map(v => v.ubicacion))].sort());
    llenarSelect(document.getElementById('filtroNivel'), [...new Set(vacantes.map(v => v.nivel_experiencia))].sort());
    llenarSelect(document.getElementById('filtroModalidad'), [...new Set(vacantes.map(v => v.modalidad).filter(Boolean))].sort());

    const hoy = new Date().toISOString().slice(0, 10);
    document.getElementById('statTotalVacantes').textContent = vacantes.length;
    document.getElementById('statTotalEmpresas').textContent = new Set(vacantes.map(v => v.empresa_nombre)).size;
    document.getElementById('statTotalRemotas').textContent = vacantes.filter(v => v.modalidad === 'Remoto').length;
    document.getElementById('statNuevasHoy').textContent = vacantes.filter(v => (v.created_at || '').slice(0, 10) === hoy).length;
}

function renderVacantes(vacantes) {
    const cont = document.getElementById('listaVacantes');
    document.getElementById('contadorResultados').textContent =
        `${vacantes.length} ${vacantes.length === 1 ? 'vacante encontrada' : 'vacantes encontradas'}`;

    if (!vacantes.length) {
        cont.innerHTML = `
            <div class="col-12">
                <div class="table-box text-center text-muted py-5">
                    <i class="bi bi-search fs-1 d-block mb-3"></i>
                    No se encontraron vacantes con esos filtros. Intenta con otra búsqueda.
                </div>
            </div>`;
        return;
    }

    cont.innerHTML = vacantes.map(v => {
        const yaPostulado = postuladasIds.includes(Number(v.id));
        const salario = v.salario !== null ? '$' + Number(v.salario).toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN' : 'Salario a convenir';
        const botonPostular = yaPostulado
            ? `<button type="button" class="btn btn-secondary flex-grow-1" disabled><i class="bi bi-check-circle-fill me-1"></i> Postulado</button>`
            : `<button type="button" class="btn btn-candidato flex-grow-1 btnPostular" data-vacante-id="${v.id}"><i class="bi bi-send-fill me-1"></i> Postularme</button>`;

        return `
            <div class="col-lg-4 col-md-6">
                <div class="job-card h-100 d-flex flex-column">
                    <h5>${v.trabajo}</h5>
                    <p>${v.empresa_nombre ?? 'Sin Empresa'}</p>
                    <div>
                        ${badgeModalidad(v.modalidad)}
                        <span class="badge bg-success">${v.nivel_experiencia}</span>
                        <span class="badge bg-secondary">${v.categoria}</span>
                    </div>
                    <p><i class="bi bi-geo-alt-fill"></i> ${v.ubicacion}</p>
                    <p><i class="bi bi-cash-stack"></i> ${salario}</p>
                    <p class="flex-grow-1">${textoCorto(v.descripcion)}</p>
                    <div class="d-grid gap-2 mt-2">
                        <a href="ver-empleo.php?id=${v.id}" class="btn btn-outline-success">Ver Vacante</a>
                        <div class="d-flex gap-2">
                            ${botonPostular}
                            <button type="button" class="btn btn-outline-success btnGuardar" data-vacante-id="${v.id}">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
    }).join('');
}

async function cargarVacantes() {
    const filtros = {
        activa: 1,
        limit: 100,
        buscar: document.getElementById('buscarEmpleo').value.trim(),
        categoria: document.getElementById('filtroCategoria').value,
        ubicacion: document.getElementById('filtroUbicacion').value,
        nivel_experiencia: document.getElementById('filtroNivel').value,
        modalidad: document.getElementById('filtroModalidad').value,
    };

    const { ok, data, message } = await Api.get('vacantes', filtros);

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar las vacantes.', 'danger');
        return;
    }

    renderVacantes(data);
}

async function cargarBase() {
    // Trae todas las activas (sin filtros) una sola vez para poblar
    // los selects de filtro y las tarjetas de estadísticas.
    const { ok, data } = await Api.get('vacantes', { activa: 1, limit: 200 });
    if (ok) {
        todasLasVacantesActivas = data;
        poblarFiltrosYEstadisticas(data);
    }

    if (SESSION_USUARIO_ID) {
        const cand = await Api.get('candidatos', { usuario_id: SESSION_USUARIO_ID });
        if (cand.ok && cand.data) {
            candidatoId = cand.data.id;
            const post = await Api.get('postulaciones', { candidato_id: candidatoId, limit: 200 });
            if (post.ok) {
                postuladasIds = post.data.map(p => Number(p.vacante_id));
            }
        }
    }

    await cargarVacantes();
}

document.getElementById('formFiltros').addEventListener('submit', function (e) {
    e.preventDefault();
    cargarVacantes();
});

document.getElementById('btnLimpiar').addEventListener('click', function () {
    document.getElementById('formFiltros').reset();
    cargarVacantes();
});

// El botón "Postularme" se maneja de forma centralizada en candidato.js
// (usa delegación de eventos, así que funciona también con las tarjetas
// que esta página genera dinámicamente). Aquí solo refrescamos el estado
// local para que, tras postularte, la tarjeta ya no muestre el botón.
document.addEventListener('postulacion:creada', function (e) {
    postuladasIds.push(Number(e.detail.vacanteId));
    cargarVacantes();
});

document.addEventListener('DOMContentLoaded', cargarBase);
</script>

<?php include "includes/footer.php"; ?>

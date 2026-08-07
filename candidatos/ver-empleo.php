<?php
require_once '../config/config.php';

// Esta página ya no consulta la base de datos directamente: el detalle de
// la vacante, las vacantes relacionadas y el estado de "ya postulado" se
// obtienen desde el navegador a través de la API REST.
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

$vacanteId = (int) ($_GET['id'] ?? 0);

if ($vacanteId <= 0) {
    header('Location: explorar-empleos.php');
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

        <div id="alertaEmpleo"></div>

        <div id="contenidoVacante">
            <div class="table-box text-center text-muted py-5">Cargando información de la vacante...</div>
        </div>

    </div>
</div>

<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
const VACANTE_ID = <?= json_encode($vacanteId) ?>;

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaEmpleo').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function tiempoPublicado(fecha) {
    const dias = Math.floor((Date.now() - new Date(fecha).getTime()) / 86400000);
    if (dias <= 0) return 'Publicado hoy';
    if (dias === 1) return 'Publicado hace 1 día';
    return `Publicado hace ${dias} días`;
}

function renderRelacionada(r) {
    const salario = r.salario !== null ? '$' + Number(r.salario).toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN' : 'A convenir';
    const modalidad = r.modalidad ? `<span class="badge bg-primary mb-3">${r.modalidad}</span>` : '';
    return `
        <div class="col-lg-4">
            <div class="job-card p-4">
                <h5 class="fw-bold">${r.trabajo}</h5>
                <p class="text-muted">${r.empresa_nombre ?? 'Sin Empresa'}</p>
                ${modalidad}
                <p><i class="bi bi-cash-stack me-2"></i> ${salario}</p>
                <a href="ver-empleo.php?id=${r.id}" class="btn btn-outline-success w-100">Ver Vacante</a>
            </div>
        </div>`;
}

function renderVacante(v, yaPostulado, relacionadas) {
    const listaRequisitos = (v.requisitos || '')
        .split(/\r\n|\r|\n|,/)
        .map(t => t.trim())
        .filter(Boolean);

    const requisitosHtml = listaRequisitos.length
        ? `<ul class="list-group list-group-flush">${listaRequisitos.map(r => `<li class="list-group-item">✔ ${r}</li>`).join('')}</ul>`
        : `<p class="text-muted mb-0">No se especificaron requisitos.</p>`;

    const botonPostularGrande = yaPostulado
        ? `<button type="button" class="btn btn-secondary btn-lg" disabled><i class="bi bi-check-circle-fill me-2"></i>Ya te postulaste</button>`
        : `<button type="button" class="btn btn-candidato btn-lg btnPostular" data-vacante-id="${v.id}"><i class="bi bi-send-fill me-2"></i>Postularme</button>`;

    const botonPostularChico = yaPostulado ? '' : `
        <button type="button" class="btn btn-candidato btnPostular" data-vacante-id="${v.id}">
            <i class="bi bi-send-fill me-2"></i>Postularme
        </button>`;

    const salario = v.salario !== null ? '$' + Number(v.salario).toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN mensuales' : 'Salario a convenir';
    const logo = v.empresa_logo ? `../${v.empresa_logo}` : '../assets/img/imagen1.png';

    const relacionadasHtml = relacionadas.length
        ? relacionadas.map(renderRelacionada).join('')
        : `<div class="col-12"><p class="text-muted mb-0">No hay vacantes relacionadas por el momento.</p></div>`;

    document.getElementById('contenidoVacante').innerHTML = `
        <div class="table-box mb-5">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center">
                    <img src="${logo}" class="img-fluid rounded" style="max-width:120px;" alt="Empresa">
                </div>
                <div class="col-lg-7">
                    <h2 class="fw-bold">${v.trabajo}</h2>
                    <h5 class="text-success">${v.empresa_nombre ?? 'Sin Empresa'}</h5>
                    <div class="mt-3">
                        ${v.modalidad ? `<span class="badge bg-primary me-2">${v.modalidad}</span>` : ''}
                        <span class="badge bg-success me-2">${v.nivel_experiencia}</span>
                        <span class="badge bg-warning text-dark">${v.categoria}</span>
                    </div>
                    <div class="mt-4">
                        <p><i class="bi bi-geo-alt-fill text-danger me-2"></i> ${v.ubicacion}</p>
                        <p><i class="bi bi-cash-stack text-success me-2"></i> ${salario}</p>
                        <p><i class="bi bi-calendar-event-fill text-primary me-2"></i> ${tiempoPublicado(v.created_at)}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-grid gap-3">
                        ${botonPostularGrande}
                        <button type="button" class="btn btn-outline-success btnGuardar" data-vacante-id="${v.id}">
                            <i class="bi bi-heart me-2"></i>Guardar Empleo
                        </button>
                        <button type="button" class="btn btn-outline-primary btnCompartir">
                            <i class="bi bi-share-fill me-2"></i>Compartir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-box mb-4">
            <h3 class="fw-bold mb-4">Descripción del Puesto</h3>
            <p class="text-muted">${(v.descripcion || '').replace(/\n/g, '<br>')}</p>
        </div>

        <div class="table-box mb-4">
            <h3 class="fw-bold mb-4">Requisitos</h3>
            ${requisitosHtml}
        </div>

        <div class="table-box mb-5">
            <h3 class="fw-bold mb-4">Acerca de la Empresa</h3>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Información</h5>
                            <p><i class="bi bi-building me-2 text-primary"></i> ${v.empresa_nombre ?? 'Sin Empresa'}</p>
                            <p><i class="bi bi-geo-alt-fill me-2 text-danger"></i> ${v.ubicacion}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-box mb-5 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 p-4">
                <h3 class="fw-bold">Vacantes Relacionadas</h3>
                <a href="explorar-empleos.php" class="btn btn-outline-success">Ver Todas</a>
            </div>
            <div class="row g-4">${relacionadasHtml}</div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="explorar-empleos.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Regresar
            </a>
            <div>
                <button type="button" class="btn btn-outline-success me-2 btnGuardar" data-vacante-id="${v.id}">
                    <i class="bi bi-heart-fill me-2"></i>Guardar Empleo
                </button>
                ${botonPostularChico}
            </div>
        </div>
    `;
}

async function cargar() {
    const vacanteRes = await Api.getOne('vacantes', VACANTE_ID);

    if (!vacanteRes.ok || !vacanteRes.data || vacanteRes.data.activa != 1) {
        window.location.href = 'explorar-empleos.php';
        return;
    }

    const v = vacanteRes.data;

    let yaPostulado = false;
    if (SESSION_USUARIO_ID) {
        const cand = await Api.get('candidatos', { usuario_id: SESSION_USUARIO_ID });
        if (cand.ok && cand.data) {
            const post = await Api.get('postulaciones', { candidato_id: cand.data.id, vacante_id: VACANTE_ID });
            yaPostulado = post.ok && post.data.length > 0;
        }
    }

    const relRes = await Api.get('vacantes', { categoria: v.categoria, activa: 1, excluir_id: VACANTE_ID, limit: 3 });
    const relacionadas = relRes.ok ? relRes.data : [];

    renderVacante(v, yaPostulado, relacionadas);
}

document.addEventListener('click', function (e) {
    if (e.target.closest('.btnCompartir')) {
        navigator.clipboard.writeText(window.location.href);
        alert('Enlace copiado al portapapeles.');
    }
});

document.addEventListener('postulacion:creada', function () {
    // Vuelve a pintar la vista con el nuevo estado "ya postulado".
    cargar();
});

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

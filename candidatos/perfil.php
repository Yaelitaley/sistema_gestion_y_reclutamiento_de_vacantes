<?php
require_once '../config/config.php';

// Esta página ya no consulta la base de datos directamente: el perfil, las
// habilidades y las estadísticas de postulaciones se obtienen desde el
// navegador a través de la API REST.
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
                <h2 class="fw-bold">Mi Perfil</h2>
                <p class="text-muted">Consulta y administra la información de tu cuenta.</p>
            </div>
        </div>

        <div id="alertaPerfil"></div>

        <div id="contenidoPerfil">
            <div class="table-box text-center text-muted py-5">Cargando tu perfil...</div>
        </div>

    </div>
</div>

<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaPerfil').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function nl2br(texto) {
    return esc(texto).replace(/\n/g, '<br>');
}

function calcularPorcentajePerfil(c, habilidades) {
    const campos = [
        c.telefono, c.ubicacion, c.genero, c.puesto_deseado, c.disponibilidad,
        c.modalidad, c.salario_esperado, c.resumen, c.objetivos, c.cv_path,
        habilidades.length ? 'ok' : '',
    ];
    const llenos = campos.filter(v => v !== null && v !== undefined && v !== '').length;
    return Math.round((llenos / campos.length) * 100);
}

function render(c, habilidades, stats) {
    const fotoSrc = c.foto_perfil ? `../${c.foto_perfil}?v=${Date.now()}` : '../assets/img/candidato.png';

    const objetivos = (c.objetivos || '')
        .split(/\r\n|\r|\n/)
        .map(t => t.trim())
        .filter(Boolean);

    const habilidadesHtml = habilidades.length
        ? `<hr><strong>Habilidades:</strong><div class="mt-2">${habilidades.map(h =>
            `<span class="badge bg-primary me-2 mb-2">${esc(h.habilidad)}${h.nivel ? ' — ' + esc(h.nivel) : ''}</span>`
          ).join('')}</div>`
        : '';

    const objetivosHtml = objetivos.length
        ? `<ul class="list-group list-group-flush">${objetivos.map(o =>
            `<li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i>${esc(o)}</li>`
          ).join('')}</ul>`
        : '<p class="text-muted mb-0">Aún no has definido tus objetivos profesionales.</p>';

    const porcentaje = calcularPorcentajePerfil(c, habilidades);
    const estadoIcono = (c.estado || '').toLowerCase() === 'activo' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-warning';

    document.getElementById('contenidoPerfil').innerHTML = `
        <div class="table-box mb-4">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center">
                    <img src="${fotoSrc}" class="rounded-circle img-fluid mb-3" style="width:180px; height:180px; object-fit:cover;" alt="Foto de perfil">
                    <a href="editar_perfil.php" class="btn btn-outline-primary"><i class="bi bi-camera-fill me-2"></i>Cambiar Foto</a>
                </div>
                <div class="col-lg-9">
                    <h2 class="fw-bold">${esc(c.nombre_completo)}</h2>
                    <h5 class="text-success">${esc(c.puesto_deseado) || 'Puesto deseado no definido'}</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="bi bi-envelope-fill text-primary me-2"></i>${esc(c.correo)}</p>
                            <p><i class="bi bi-telephone-fill text-success me-2"></i>${esc(c.telefono) || 'Sin teléfono registrado'}</p>
                            <p><i class="bi bi-person-fill text-warning me-2"></i>${esc(c.genero) || 'Género no especificado'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="bi bi-geo-alt-fill text-danger me-2"></i>${esc(c.ubicacion) || 'Ubicación no registrada'}</p>
                            <p><i class="bi bi-person-badge-fill text-info me-2"></i>Candidato</p>
                            <p><i class="bi bi-${estadoIcono} me-2"></i>${esc((c.estado || '').charAt(0).toUpperCase() + (c.estado || '').slice(1))}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">Información Profesional</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Puesto Deseado:</strong> ${esc(c.puesto_deseado) || 'No especificado'}</p>
                    <p><strong>Disponibilidad:</strong> ${esc(c.disponibilidad) || 'No especificada'}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Modalidad Preferida:</strong> ${esc(c.modalidad) || 'No especificada'}</p>
                    <p><strong>Salario Esperado:</strong> ${esc(c.salario_esperado) || 'No especificado'}</p>
                </div>
            </div>
            ${habilidadesHtml}
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icono bg-primary-subtle mt-2"><i class="bi bi-send-check-fill text-primary"></i></div><div><h3 class="fw-bold">${stats.total}</h3><p class="text-muted mb-0">Postulaciones</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icono bg-warning-subtle mt-2"><i class="bi bi-clock-history text-warning"></i></div><div><h3 class="fw-bold">${stats.enRevision}</h3><p class="text-muted mb-0">En Revisión</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icono bg-info-subtle mt-2"><i class="bi bi-calendar-event-fill text-info"></i></div><div><h3 class="fw-bold">${stats.entrevistas}</h3><p class="text-muted mb-0">Entrevistas</p></div></div></div>
            <div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-icono bg-success-subtle mt-2"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold">${stats.contratado}</h3><p class="text-muted mb-0">Contratado</p></div></div></div>
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">Accesos Rápidos</h4>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6"><a href="cv.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-file-earmark-person-fill fs-4 d-block mb-2"></i>Mi Currículum</a></div>
                <div class="col-lg-3 col-md-6"><a href="postulaciones.php" class="btn btn-outline-success w-100 py-3"><i class="bi bi-send-check-fill fs-4 d-block mb-2"></i>Mis Postulaciones</a></div>
                <div class="col-lg-3 col-md-6"><a href="explorar-empleos.php" class="btn btn-outline-warning w-100 py-3"><i class="bi bi-search fs-4 d-block mb-2"></i>Explorar Empleos</a></div>
                <div class="col-lg-3 col-md-6"><a href="configuracion.php" class="btn btn-outline-secondary w-100 py-3"><i class="bi bi-gear-fill fs-4 d-block mb-2"></i>Configuración</a></div>
            </div>
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">Redes Profesionales</h4>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">LinkedIn</label><input type="text" class="form-control" value="${esc(c.linkedin) || 'No registrado'}" readonly></div>
                <div class="col-md-4 mb-3"><label class="form-label">GitHub</label><input type="text" class="form-control" value="${esc(c.github) || 'No registrado'}" readonly></div>
                <div class="col-md-4 mb-3"><label class="form-label">Portafolio</label><input type="text" class="form-control" value="${esc(c.portafolio) || 'No registrado'}" readonly></div>
            </div>
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">Resumen del Perfil</h4>
            <p class="text-muted mb-0">${c.resumen ? nl2br(c.resumen) : 'Aún no has escrito un resumen de tu perfil. Agrégalo desde "Editar Perfil".'}</p>
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">Objetivos Profesionales</h4>
            ${objetivosHtml}
        </div>

        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">Preferencias de la Cuenta</h4>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" ${c.ofertas_empleo ? 'checked' : ''} disabled><label class="form-check-label">Recibir ofertas de empleo por correo.</label></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" ${c.notificaciones_sistema ? 'checked' : ''} disabled><label class="form-check-label">Recibir notificaciones del sistema.</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" ${c.perfil_publico ? 'checked' : ''} disabled><label class="form-check-label">Mostrar mi perfil públicamente para reclutadores.</label></div>
        </div>

        <div class="table-box mb-5">
            <h4 class="fw-bold mb-4">Estado de la Cuenta</h4>
            <div class="alert alert-info d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>Tu perfil está completo en un <strong>${porcentaje}%</strong>. Mantén tu información actualizada para aumentar tus oportunidades de ser contactado por los reclutadores.</div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
            <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Regresar</a>
            <div>
                <a href="cv.php" class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i>Ver CV</a>
                <a href="editar_perfil.php" class="btn btn-candidato"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</a>
            </div>
        </div>
    `;
}

async function cargar() {
    const candRes = await Api.get('candidatos', { usuario_id: SESSION_USUARIO_ID });

    if (!candRes.ok || !candRes.data) {
        mostrarAlerta(candRes.message || 'No se encontró tu perfil.', 'danger');
        return;
    }

    const c = candRes.data;

    const [habRes, postRes] = await Promise.all([
        Api.get('candidato_habilidades', { candidato_id: c.id }),
        Api.get('postulaciones', { candidato_id: c.id, limit: 200 }),
    ]);

    const habilidades = habRes.ok ? habRes.data : [];
    const postulaciones = postRes.ok ? postRes.data : [];

    const stats = {
        total: postulaciones.length,
        enRevision: postulaciones.filter(p => Number(p.estado_id) === 2).length,
        entrevistas: postulaciones.filter(p => Number(p.estado_id) === 3).length,
        contratado: postulaciones.filter(p => Number(p.estado_id) === 5).length,
    };

    render(c, habilidades, stats);
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>

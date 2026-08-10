<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';


if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

$postulacionId = (int) ($_GET['id'] ?? 0);
if ($postulacionId <= 0) {
    redirect_to('candidatos.php');
}

include "includes/header.php";
?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>
        <!-- TITULO -->
        <div class="mb-4">
            <h2 class="fw-bold">Perfil del Candidato</h2>
            <p class="text-muted">Consulta la información completa del candidato y administra su proceso de selección.</p>
        </div>

        <div id="alertaCandidato"></div>

        <div id="contenidoCandidato">
            <div class="table-box text-center text-muted py-5">Cargando información del candidato...</div>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
const POSTULACION_ID = <?= json_encode($postulacionId) ?>;
const PASOS = ['Postulado', 'En revisión', 'Entrevista', 'Contratado'];

const MAPA_BADGE = {
    'Postulado':   'bg-secondary',
    'En revisión': 'bg-warning text-dark',
    'Entrevista':  'bg-info',
    'Rechazado':   'bg-danger',
    'Contratado':  'bg-success',
};

function esc(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaCandidato').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function badgeEstado(nombre) {
    return `<span class="badge ${MAPA_BADGE[nombre] || 'bg-secondary'}">${nombre}</span>`;
}

function formatoFecha(fecha, formato = { day: '2-digit', month: '2-digit', year: 'numeric' }) {
    if (!fecha) return null;
    return new Date(fecha).toLocaleDateString('es-MX', formato);
}

function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) return null;
    return Math.floor((Date.now() - new Date(fechaNacimiento).getTime()) / 31557600000);
}

function render(postulacion, candidato, experiencia, formacion, idiomas, certificaciones, habilidades, historial) {
    const edad = calcularEdad(candidato.fecha_nacimiento);
    const nombresHistorial = historial.map(h => h.estado_nombre);

    const timelineHtml = PASOS.map(paso => {
        const completado = nombresHistorial.includes(paso);
        const activo = postulacion.estado_nombre === paso;
        const clase = completado ? 'completed' : (activo ? 'active' : '');
        const icono = completado ? 'bi-check-circle-fill' : (activo ? 'bi-clock-fill' : 'bi-circle');
        return `<div class="timeline-item ${clase}"><i class="bi ${icono}"></i> ${paso}</div>`;
    }).join('');

    const experienciaHtml = experiencia.length
        ? experiencia.map(exp => `
            <h5 class="mt-2">${esc(exp.puesto)}</h5>
            <p class="text-primary mb-1">${esc(exp.empresa)}</p>
            <small class="text-muted">${formatoFecha(exp.fecha_inicio, { month: 'short', year: 'numeric' }) || ''} - ${exp.fecha_fin ? formatoFecha(exp.fecha_fin, { month: 'short', year: 'numeric' }) : 'Actualidad'}</small>
            <p class="mt-2">${esc(exp.descripcion).replace(/\n/g, '<br>')}</p><hr>
        `).join('')
        : '<p class="text-muted">Sin experiencia registrada.</p>';

    const formacionHtml = formacion.length
        ? formacion.map(f => `
            <h6 class="fw-bold mt-2">${esc(f.carrera)}</h6>
            <p class="text-primary mb-1">${esc(f.institucion)}</p>
            <small class="text-muted">${formatoFecha(f.fecha_inicio, { year: 'numeric' }) || ''} - ${f.fecha_fin ? formatoFecha(f.fecha_fin, { year: 'numeric' }) : 'Actualidad'}</small><hr>
        `).join('')
        : '<p class="text-muted">Sin formación registrada.</p>';

    const certificacionesHtml = certificaciones.length
        ? `<ul>${certificaciones.map(c => `<li>${esc(c.descripcion)}</li>`).join('')}</ul>`
        : '<p class="text-muted">Sin certificaciones registradas.</p>';

    const habilidadesHtml = habilidades.length
        ? habilidades.map(h => `<span class="badge bg-primary m-1 p-2">${esc(h.habilidad)}${h.nivel ? ' · ' + esc(h.nivel) : ''}</span>`).join('')
        : '<p class="text-muted">Sin habilidades registradas.</p>';

    const idiomasHtml = idiomas.length
        ? idiomas.map(i => `<tr><td>${esc(i.idioma)}</td><td>${esc(i.nivel)}</td></tr>`).join('')
        : '<tr><td colspan="2" class="text-muted">Sin idiomas registrados.</td></tr>';

    const cvHtml = candidato.cv_path
        ? `<a href="../${esc(candidato.cv_path)}" download class="btn btn-outline-primary"><i class="bi bi-download me-2"></i>Descargar PDF</a>
           <a href="../${esc(candidato.cv_path)}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i>Ver CV</a>`
        : '';

    document.getElementById('contenidoCandidato').innerHTML = `
        <div class="candidate-profile mb-4">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center">
                    <img src="${candidato.foto_perfil ? '../' + esc(candidato.foto_perfil) + '?v=' + Date.now() : '../assets/img/candidato02.png'}" class="candidate-photo img-fluid rounded-circle" alt="Candidato" onerror="this.onerror=null;this.src='../assets/img/candidato02.png';">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold">${esc(candidato.nombre_completo)}</h2>
                    <h5 class="text-primary mb-4">${esc(candidato.puesto_deseado) || 'Puesto deseado no especificado'}</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3"><i class="bi bi-envelope-fill text-primary me-2"></i>${esc(candidato.correo)}</div>
                        <div class="col-md-6 mb-3"><i class="bi bi-telephone-fill text-success me-2"></i>${esc(candidato.telefono) || 'No registrado'}</div>
                        <div class="col-md-6 mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i>${esc(candidato.ubicacion) || 'No registrada'}</div>
                        <div class="col-md-6 mb-3"><i class="bi bi-calendar-check-fill text-warning me-2"></i>${esc(candidato.disponibilidad) || 'No especificada'}</div>
                        <div class="col-md-6 mb-3"><i class="bi bi-cash-stack text-success me-2"></i>${candidato.salario_esperado ? '$' + Number(candidato.salario_esperado).toLocaleString('es-MX', { minimumFractionDigits: 2 }) : 'No especificado'}</div>
                        <div class="col-md-6 mb-3"><i class="bi bi-briefcase-fill text-secondary me-2"></i>${esc(candidato.modalidad) || 'No especificada'}</div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="status-card">
                        <h5 class="fw-bold mb-4">Estado del proceso</h5>
                        <div class="mb-3">${badgeEstado(postulacion.estado_nombre)}</div>
                        <hr>
                        <div class="timeline">${timelineHtml}</div>
                        <hr>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="cambiarEstado(3)"><i class="bi bi-calendar-check-fill me-2"></i>Pasar a Entrevista</button>
                            <button type="button" class="btn btn-primary" onclick="cambiarEstado(5)"><i class="bi bi-person-check-fill me-2"></i>Contratar</button>
                            <div class="d-flex gap-2">
                                <a href="candidatos.php" class="btn btn-secondary flex-fill"><i class="bi bi-arrow-left-circle-fill me-2"></i>Regresar</a>
                                <button type="button" class="btn btn-danger" onclick="cambiarEstado(4)"><i class="bi bi-x-circle me-2"></i>Rechazar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-person-fill me-2"></i>Información Personal</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr><th width="40%">Nombre</th><td>${esc(candidato.nombre_completo)}</td></tr>
                            <tr><th>Fecha de nacimiento</th><td>${formatoFecha(candidato.fecha_nacimiento) || 'No registrada'}</td></tr>
                            <tr><th>Edad</th><td>${edad !== null ? edad + ' años' : 'No disponible'}</td></tr>
                            <tr><th>Nacionalidad</th><td>${esc(candidato.nacionalidad) || 'No registrada'}</td></tr>
                            <tr><th>Ubicación</th><td>${esc(candidato.ubicacion) || 'No registrada'}</td></tr>
                            <tr><th>Resumen</th><td>${(esc(candidato.resumen) || 'Sin resumen registrado').replace(/\n/g, '<br>')}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-briefcase-fill me-2"></i>Experiencia Laboral</h4>
                    ${experienciaHtml}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-mortarboard-fill me-2"></i>Formación Académica</h4>
                    ${formacionHtml}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-patch-check-fill me-2"></i>Certificaciones</h4>
                    ${certificacionesHtml}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-stars me-2"></i>Habilidades</h4>
                    ${habilidadesHtml}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="candidate-card">
                    <h4 class="fw-bold mb-4"><i class="bi bi-translate me-2"></i>Idiomas</h4>
                    <table class="table"><thead><tr><th>Idioma</th><th>Nivel</th></tr></thead><tbody>${idiomasHtml}</tbody></table>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="candidate-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Currículum Vitae</h4>
                        ${cvHtml}
                    </div>
                    <div class="cv-preview col-lg-12">
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size:90px;"></i>
                            <h5 class="mt-4">${candidato.cv_path ? esc(candidato.cv_path.split('/').pop()) : 'El candidato aún no ha subido su CV'}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

async function cargar() {
    const recRes = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });
    if (!recRes.ok || !recRes.data) {
        mostrarAlerta('No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }

    const postsRes = await Api.get('postulaciones', { reclutador_id: recRes.data.id, limit: 200 });
    const postulacion = postsRes.ok ? postsRes.data.find(p => Number(p.id) === POSTULACION_ID) : null;

    if (!postulacion) {
        mostrarAlerta('No se encontró la postulación solicitada.', 'danger');
        return;
    }

    const [candRes, expRes, formRes, idiRes, certRes, habRes, histRes] = await Promise.all([
        Api.getOne('candidatos', postulacion.candidato_id),
        Api.get('candidato_experiencia', { candidato_id: postulacion.candidato_id }),
        Api.get('candidato_formacion', { candidato_id: postulacion.candidato_id }),
        Api.get('candidato_idiomas', { candidato_id: postulacion.candidato_id }),
        Api.get('candidato_certificaciones', { candidato_id: postulacion.candidato_id }),
        Api.get('candidato_habilidades', { candidato_id: postulacion.candidato_id }),
        Api.get('historial_estados_postulacion', { postulacion_id: POSTULACION_ID }),
    ]);

    if (!candRes.ok || !candRes.data) {
        mostrarAlerta('No se pudo cargar la información del candidato.', 'danger');
        return;
    }

    render(
        postulacion,
        candRes.data,
        expRes.ok ? expRes.data : [],
        formRes.ok ? formRes.data : [],
        idiRes.ok ? idiRes.data : [],
        certRes.ok ? certRes.data : [],
        habRes.ok ? habRes.data : [],
        histRes.ok ? histRes.data : []
    );
}

async function cambiarEstado(estadoId) {
    const { ok, message } = await Api.patch('postulaciones', POSTULACION_ID, { estado_id: estadoId });

    if (!ok) {
        mostrarAlerta(message || 'No se pudo actualizar el estado.', 'danger');
        return;
    }

    mostrarAlerta('Estado actualizado correctamente.', 'success');
    await cargar();
}

document.addEventListener('DOMContentLoaded', cargar);
</script>

<?php include "includes/footer.php"; ?>
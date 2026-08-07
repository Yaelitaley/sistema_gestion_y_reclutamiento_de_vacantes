<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];
?>
<?php include "includes/header.php"; ?>
<div class="d-flex">
    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>
        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Programar Entrevista</h2>
                <p class="text-muted">Complete la información para agendar una entrevista con un candidato.</p>
            </div>
            <a href="entrevistas.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
        </div>

        <div id="alertaEntrevista"></div>

        <!-- FORMULARIO -->
        <div class="table-box">
            <form id="formEntrevista">
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="postulacion_id" class="form-label fw-bold">Postulación</label>
                        <select id="postulacion_id" class="form-select" required>
                            <option value="">Cargando postulaciones...</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Candidato</label>
                        <input type="text" id="nombre_candidato" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Vacante</label>
                        <input type="text" id="nombre_vacante" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Correo electrónico</label>
                        <input type="email" id="correo" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" id="telefono" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="fecha" class="form-label fw-bold">Fecha</label>
                        <input type="date" id="fecha" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="hora" class="form-label fw-bold">Hora</label>
                        <input type="time" id="hora" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="modalidad" class="form-label fw-bold">Modalidad</label>
                        <select id="modalidad" class="form-select" required>
                            <option value="">Seleccione una modalidad</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                            <option value="Telefónica">Telefónica</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="estado" class="form-label fw-bold">Estado</label>
                        <select id="estado" class="form-select" required>
                            <option value="Programada" selected>Programada</option>
                            <option value="Realizada">Realizada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="lugar" class="form-label fw-bold">Lugar o enlace de la entrevista</label>
                        <input type="text" id="lugar" class="form-control" placeholder="Ej. Sala de juntas, Google Meet, Zoom..." required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="notas" class="form-label fw-bold">Notas</label>
                        <textarea id="notas" rows="5" class="form-control" placeholder="Agregue observaciones para la entrevista..."></textarea>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="entrevistas.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Cancelar</a>
                    <button type="reset" class="btn btn-warning"><i class="bi bi-arrow-clockwise me-2"></i>Limpiar</button>
                    <button type="submit" class="btn btn-reclutador"><i class="bi bi-calendar-plus-fill me-2"></i>Programar Entrevista</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/api-client.js"></script>
<script>
const SESSION_USUARIO_ID = <?= json_encode($usuarioId) ?>;
let reclutadorId = null;
let postulacionesDisponibles = [];

function mostrarAlerta(mensaje, tipo) {
    document.getElementById('alertaEntrevista').innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
}

function poblarSelect(postulaciones) {
    const select = document.getElementById('postulacion_id');
    select.innerHTML = '<option value="">Seleccione una postulación</option>' + postulaciones.map(p =>
        `<option value="${p.id}">${p.candidato_nombre} — ${p.trabajo}</option>`
    ).join('');
}

document.getElementById('postulacion_id').addEventListener('change', function () {
    const p = postulacionesDisponibles.find(item => String(item.id) === this.value);
    document.getElementById('nombre_candidato').value = p ? p.candidato_nombre : '';
    document.getElementById('nombre_vacante').value = p ? p.trabajo : '';
    document.getElementById('correo').value = p ? (p.candidato_correo || '') : '';
    document.getElementById('telefono').value = p ? (p.candidato_telefono || '') : '';
});

async function cargarPostulaciones() {
    const { ok, data, message } = await Api.get('postulaciones', { reclutador_id: reclutadorId, limit: 200 });

    if (!ok) {
        mostrarAlerta(message || 'No se pudieron cargar las postulaciones.', 'danger');
        return;
    }

    postulacionesDisponibles = data.sort((a, b) => (a.candidato_nombre || '').localeCompare(b.candidato_nombre || ''));
    poblarSelect(postulacionesDisponibles);
}

async function inicializar() {
    const { ok, data, message } = await Api.get('reclutadores', { usuario_id: SESSION_USUARIO_ID });

    if (!ok || !data) {
        mostrarAlerta(message || 'No se encontró el perfil de reclutador asociado a este usuario.', 'danger');
        return;
    }

    reclutadorId = data.id;
    await cargarPostulaciones();
}

document.getElementById('formEntrevista').addEventListener('submit', async function (e) {
    e.preventDefault();

    const postulacionId = document.getElementById('postulacion_id').value;
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;

    if (!postulacionId || !fecha || !hora || !document.getElementById('modalidad').value || !document.getElementById('lugar').value.trim()) {
        mostrarAlerta('Complete todos los campos obligatorios.', 'danger');
        return;
    }

    const payload = {
        postulacion_id: Number(postulacionId),
        fecha: `${fecha} ${hora}:00`,
        modalidad: document.getElementById('modalidad').value,
        estado: document.getElementById('estado').value,
        lugar: document.getElementById('lugar').value.trim(),
        notas: document.getElementById('notas').value.trim(),
    };

    const { ok, message } = await Api.post('entrevistas', payload);

    if (!ok) {
        mostrarAlerta(message || 'No fue posible programar la entrevista.', 'danger');
        return;
    }

    window.location.href = 'entrevistas.php?type=success&msg=' + encodeURIComponent('Entrevista programada correctamente.');
});

document.addEventListener('DOMContentLoaded', inicializar);
</script>

<?php include "includes/footer.php"; ?>

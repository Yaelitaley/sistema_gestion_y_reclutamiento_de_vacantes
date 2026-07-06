<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/app_helpers.php';

if (($_SESSION['rol_id'] ?? 0) != 3) {
    redirect_to('login.php');
}

$usuarioId = (int) $_SESSION['usuario_id'];

// Obtener el id del reclutador a partir del usuario logueado
$stmt = $conn->prepare("SELECT id, empresa_id FROM reclutadores WHERE usuario_id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$reclutador = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reclutador) {
    die('No se encontró el perfil de reclutador asociado a este usuario.');
}
$reclutadorId = (int) $reclutador['id'];

$mensaje = $_GET['msg'] ?? '';
$tipoMensaje = $_GET['type'] ?? 'success';

// crear, editar y eliminar 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'guardar') {
        $id          = (int) ($_POST['id'] ?? 0);
        $trabajo     = trim($_POST['trabajo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria   = trim($_POST['categoria'] ?? '');
        $requisitos  = trim($_POST['requisitos'] ?? '');
        $salario     = trim($_POST['salario'] ?? '');
        $ubicacion   = trim($_POST['ubicacion'] ?? '');
        $nivel       = trim($_POST['nivel_experiencia'] ?? '');
        $activa      = isset($_POST['activa']) ? 1 : 0;

        if ($trabajo === '' || $descripcion === '' || $categoria === '' || $ubicacion === '' || $nivel === '') {
            redirect_to('vacantes.php?type=danger&msg=' . urlencode('Completa todos los campos obligatorios.'));
        }

        $salarioVal = $salario === '' ? null : (float) $salario;

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE vacantes SET trabajo=?, descripcion=?, categoria=?, requisitos=?, salario=?, ubicacion=?, nivel_experiencia=?, activa=? WHERE id=? AND reclutador_id=?");
            $stmt->bind_param('ssssdssiii', $trabajo, $descripcion, $categoria, $requisitos, $salarioVal, $ubicacion, $nivel, $activa, $id, $reclutadorId);
            $stmt->execute();
            $stmt->close();
            redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante actualizada correctamente.'));
        } else {
            $stmt = $conn->prepare("INSERT INTO vacantes (reclutador_id, trabajo, descripcion, categoria, requisitos, salario, ubicacion, nivel_experiencia, activa) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('issssdssi', $reclutadorId, $trabajo, $descripcion, $categoria, $requisitos, $salarioVal, $ubicacion, $nivel, $activa);
            $stmt->execute();
            $stmt->close();
            redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante creada correctamente.'));
        }
    }

    if ($accion === 'eliminar') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM postulaciones WHERE vacante_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("DELETE FROM vacantes WHERE id = ? AND reclutador_id = ?");
                $stmt->bind_param('ii', $id, $reclutadorId);
                $stmt->execute();
                $stmt->close();
                $conn->commit();
                redirect_to('vacantes.php?type=success&msg=' . urlencode('Vacante eliminada correctamente.'));
            } catch (Throwable $e) {
                $conn->rollback();
                redirect_to('vacantes.php?type=danger&msg=' . urlencode('No se pudo eliminar: ' . $e->getMessage()));
            }
        }
    }
}

// ---------- LISTADO ----------
$stmt = $conn->prepare("SELECT v.*, (SELECT COUNT(*) FROM postulaciones p WHERE p.vacante_id = v.id) AS total_postulaciones
                         FROM vacantes v WHERE v.reclutador_id = ? ORDER BY v.id DESC");
$stmt->bind_param('i', $reclutadorId);
$stmt->execute();
$vacantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalActivas = 0;
foreach ($vacantes as $v) { if ((int)$v['activa'] === 1) $totalActivas++; }

include "includes/header.php";
?>
<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>

    <div class="content w-100 p-4">
        <?php include "includes/topbar.php"; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Mis Vacantes</h2>
                <p class="text-muted">Administra las vacantes que has publicado.</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVacante" onclick="nuevaVacante()">
                <i class="bi bi-plus-circle-fill me-2"></i>Nueva Vacante
            </button>
        </div>

        <?php if ($mensaje !== ''): ?>
            <div class="alert alert-<?= e($tipoMensaje) ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div><div><h3 class="fw-bold"><?= count($vacantes) ?></h3><p class="mb-0">Total de Vacantes</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div><div><h3 class="fw-bold"><?= $totalActivas ?></h3><p class="mb-0">Activas</p></div></div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card"><div class="card-icon bg-warning-subtle"><i class="bi bi-people-fill text-warning"></i></div><div><h3 class="fw-bold"><?= array_sum(array_column($vacantes, 'total_postulaciones')) ?></h3><p class="mb-0">Postulaciones Recibidas</p></div></div>
            </div>
        </div>

        <div class="table-box">
            <div class="table-responsive">
                <table class="table align-middle">
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
                    <tbody>
                        <?php if (!$vacantes): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aún no has publicado vacantes.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($vacantes as $v): ?>
                            <tr>
                                <td><strong><?= e($v['trabajo']) ?></strong><br><small class="text-muted"><?= e(texto_corto($v['descripcion'], 60)) ?></small></td>
                                <td><span class="badge bg-primary"><?= e($v['categoria']) ?></span></td>
                                <td><?= e($v['ubicacion']) ?></td>
                                <td><?= $v['salario'] !== null ? '$' . number_format((float)$v['salario'], 2) : 'No especificado' ?></td>
                                <td><?= badge_estado($v['activa'] ? 'Activo' : 'Inactivo') ?></td>
                                <td><?= (int) $v['total_postulaciones'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        onclick='editarVacante(<?= json_encode($v) ?>)'
                                        data-bs-toggle="modal" data-bs-target="#modalVacante">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta vacante?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $v['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVacanteTitulo">Nueva Vacante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" value="guardar">
                    <input type="hidden" name="id" id="f_id" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Puesto</label>
                            <input type="text" name="trabajo" id="f_trabajo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <input type="text" name="categoria" id="f_categoria" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <input type="text" name="ubicacion" id="f_ubicacion" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Salario</label>
                            <input type="number" step="0.01" name="salario" id="f_salario" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nivel de experiencia</label>
                            <input type="text" name="nivel_experiencia" id="f_nivel" class="form-control" placeholder="Ej. Junior, Senior" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" id="f_descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos</label>
                        <textarea name="requisitos" id="f_requisitos" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="activa" id="f_activa" checked>
                        <label class="form-check-label" for="f_activa">Vacante activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

function editarVacante(v) {
    document.getElementById('modalVacanteTitulo').innerText = 'Editar Vacante';
    document.getElementById('f_id').value = v.id;
    document.getElementById('f_trabajo').value = v.trabajo;
    document.getElementById('f_categoria').value = v.categoria;
    document.getElementById('f_ubicacion').value = v.ubicacion;
    document.getElementById('f_salario').value = v.salario;
    document.getElementById('f_nivel').value = v.nivel_experiencia;
    document.getElementById('f_descripcion').value = v.descripcion;
    document.getElementById('f_requisitos').value = v.requisitos;
    document.getElementById('f_activa').checked = v.activa == 1;
}
</script>

<?php include "includes/footer.php"; ?>
<?php
require_once '../config/config.php';
require_once '../config/connection.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: login.php');
    exit;
}

$busqueda  = trim($_GET['busqueda'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$ubicacion = trim($_GET['ubicacion'] ?? '');
$nivel     = trim($_GET['nivel_experiencia'] ?? '');
$modalidad = trim($_GET['modalidad'] ?? '');

// ----- Listado de vacantes activas con filtros -----
$sql = "SELECT v.id, v.trabajo, v.descripcion, v.categoria, v.ubicacion, v.salario, v.nivel_experiencia, v.modalidad,
               e.nombre AS empresa
        FROM vacantes v
        INNER JOIN reclutadores r ON v.reclutador_id = r.id
        INNER JOIN empresas e ON r.empresa_id = e.id
        WHERE v.activa = 1";

$params = [];
$types  = '';

if ($busqueda !== '') {
    $sql .= " AND (v.trabajo LIKE ? OR v.descripcion LIKE ? OR e.nombre LIKE ?)";
    $like = '%' . $busqueda . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}
if ($categoria !== '') {
    $sql .= " AND v.categoria = ?";
    $params[] = $categoria;
    $types .= 's';
}
if ($ubicacion !== '') {
    $sql .= " AND v.ubicacion = ?";
    $params[] = $ubicacion;
    $types .= 's';
}
if ($nivel !== '') {
    $sql .= " AND v.nivel_experiencia = ?";
    $params[] = $nivel;
    $types .= 's';
}
if ($modalidad !== '') {
    $sql .= " AND v.modalidad = ?";
    $params[] = $modalidad;
    $types .= 's';
}

$sql .= " ORDER BY v.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$vacantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----- Opciones para los selects de filtro -----
$categorias = $conn->query("SELECT DISTINCT categoria FROM vacantes WHERE activa = 1 ORDER BY categoria")->fetch_all(MYSQLI_ASSOC);
$ubicaciones = $conn->query("SELECT DISTINCT ubicacion FROM vacantes WHERE activa = 1 ORDER BY ubicacion")->fetch_all(MYSQLI_ASSOC);
$niveles = $conn->query("SELECT DISTINCT nivel_experiencia FROM vacantes WHERE activa = 1 ORDER BY nivel_experiencia")->fetch_all(MYSQLI_ASSOC);
$modalidades = $conn->query("SELECT DISTINCT modalidad FROM vacantes WHERE activa = 1 AND modalidad IS NOT NULL ORDER BY modalidad")->fetch_all(MYSQLI_ASSOC);

// ----- Estadísticas -----
$totalVacantes = $conn->query("SELECT COUNT(*) AS c FROM vacantes WHERE activa = 1")->fetch_assoc()['c'];
$totalEmpresas = $conn->query(
    "SELECT COUNT(DISTINCT e.id) AS c
     FROM empresas e
     INNER JOIN reclutadores r ON r.empresa_id = e.id
     INNER JOIN vacantes v ON v.reclutador_id = r.id
     WHERE v.activa = 1"
)->fetch_assoc()['c'];
$totalRemotas = $conn->query("SELECT COUNT(*) AS c FROM vacantes WHERE activa = 1 AND modalidad = 'Remoto'")->fetch_assoc()['c'];
$nuevasHoy = $conn->query("SELECT COUNT(*) AS c FROM vacantes WHERE activa = 1 AND DATE(created_at) = CURDATE()")->fetch_assoc()['c'];

function badgeModalidad($modalidad) {
    $map = [
        'Presencial' => 'bg-danger',
        'Híbrido'    => 'bg-primary',
        'Remoto'     => 'bg-warning text-dark',
    ];
    if (empty($modalidad)) return '';
    $clase = $map[$modalidad] ?? 'bg-secondary';
    return '<span class="badge ' . $clase . '">' . htmlspecialchars($modalidad) . '</span>';
}

// ----- Candidato en sesión: para saber a qué vacantes ya se postuló / cuáles guardó -----
$stmt = $conn->prepare("SELECT id FROM candidatos WHERE usuario_id = ?");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$stmt->bind_result($candidato_id);
$stmt->fetch();
$stmt->close();

$postuladasIds = [];
$guardadasIds  = [];
if ($candidato_id) {
    $res = $conn->prepare("SELECT vacante_id FROM postulaciones WHERE candidato_id = ?");
    $res->bind_param('i', $candidato_id);
    $res->execute();
    $r = $res->get_result();
    while ($row = $r->fetch_assoc()) { $postuladasIds[] = (int) $row['vacante_id']; }
    $res->close();

    $res = $conn->prepare("SELECT vacante_id FROM vacantes_guardadas WHERE candidato_id = ?");
    $res->bind_param('i', $candidato_id);
    $res->execute();
    $r = $res->get_result();
    while ($row = $r->fetch_assoc()) { $guardadasIds[] = (int) $row['vacante_id']; }
    $res->close();
}
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

        <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-briefcase-fill"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= (int) $totalVacantes ?></h3>
                        <p class="text-muted mb-0">Vacantes activas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= (int) $totalEmpresas ?></h3>
                        <p class="text-muted mb-0">Empresas contratando</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-house-door-fill"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= (int) $totalRemotas ?></h3>
                        <p class="text-muted mb-0">Vacantes remotas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card d-flex align-items-center gap-3">
                    <div class="card-icon"><i class="bi bi-stars"></i></div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= (int) $nuevasHoy ?></h3>
                        <p class="text-muted mb-0">Nuevas hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BUSCADOR Y FILTROS -->
        <form action="explorar-empleos.php" method="GET">
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
                                placeholder="Puesto, empresa o palabra clave..."
                                value="<?= htmlspecialchars($busqueda) ?>">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" name="categoria">
                            <option value="">Categoría</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= htmlspecialchars($c['categoria']) ?>" <?= $categoria === $c['categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['categoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" name="ubicacion">
                            <option value="">Ubicación</option>
                            <?php foreach ($ubicaciones as $u): ?>
                                <option value="<?= htmlspecialchars($u['ubicacion']) ?>" <?= $ubicacion === $u['ubicacion'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['ubicacion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" name="nivel_experiencia">
                            <option value="">Nivel</option>
                            <?php foreach ($niveles as $n): ?>
                                <option value="<?= htmlspecialchars($n['nivel_experiencia']) ?>" <?= $nivel === $n['nivel_experiencia'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($n['nivel_experiencia']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select class="form-select" name="modalidad">
                            <option value="">Modalidad</option>
                            <?php foreach ($modalidades as $m): ?>
                                <option value="<?= htmlspecialchars($m['modalidad']) ?>" <?= $modalidad === $m['modalidad'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['modalidad']) ?>
                                </option>
                            <?php endforeach; ?>
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
            <h4 class="fw-bold mb-0">
                <?= count($vacantes) ?> <?= count($vacantes) === 1 ? 'vacante encontrada' : 'vacantes encontradas' ?>
            </h4>
        </div>

        <div class="row g-4 mb-5">
            <?php if (empty($vacantes)): ?>
                <div class="col-12">
                    <div class="table-box text-center text-muted py-5">
                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                        No se encontraron vacantes con esos filtros. Intenta con otra búsqueda.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($vacantes as $v): ?>
                    <?php
                        $yaPostulado = in_array((int) $v['id'], $postuladasIds, true);
                        $yaGuardado  = in_array((int) $v['id'], $guardadasIds, true);
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="job-card h-100 d-flex flex-column">
                            <h5><?= htmlspecialchars($v['trabajo']) ?></h5>
                            <p><?= htmlspecialchars($v['empresa']) ?></p>
                            <div>
                                <?= badgeModalidad($v['modalidad']) ?>
                                <span class="badge bg-success"><?= htmlspecialchars($v['nivel_experiencia']) ?></span>
                                <span class="badge bg-secondary"><?= htmlspecialchars($v['categoria']) ?></span>
                            </div>
                            <p><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($v['ubicacion']) ?></p>
                            <p><i class="bi bi-cash-stack"></i> <?= $v['salario'] !== null ? '$' . number_format($v['salario'], 2) . ' MXN' : 'Salario a convenir' ?></p>
                            <p class="flex-grow-1"><?= htmlspecialchars(mb_strimwidth($v['descripcion'], 0, 110, '...')) ?></p>
                            <div class="d-grid gap-2 mt-2">
                                <a href="ver-empleo.php?id=<?= (int) $v['id'] ?>" class="btn btn-outline-success">
                                    Ver Vacante
                                </a>
                                <div class="d-flex gap-2">
                                    <?php if ($yaPostulado): ?>
                                        <button type="button" class="btn btn-secondary flex-grow-1" disabled>
                                            <i class="bi bi-check-circle-fill me-1"></i> Postulado
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-candidato flex-grow-1 btnPostular" data-vacante-id="<?= (int) $v['id'] ?>">
                                            <i class="bi bi-send-fill me-1"></i> Postularme
                                        </button>
                                    <?php endif; ?>
                                    <button
                                        type="button"
                                        class="btn <?= $yaGuardado ? 'btn-success' : 'btn-outline-success' ?> btnGuardar"
                                        data-vacante-id="<?= (int) $v['id'] ?>">
                                        <i class="bi <?= $yaGuardado ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
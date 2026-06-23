<?php
require_once '../config/config.php';
require_once '../config/connection.php';

$totalReclutadores  = $conn->query("SELECT COUNT(*) AS total FROM reclutadores")->fetch_assoc()['total'];
$totalCandidatos     = $conn->query("SELECT COUNT(*) AS total FROM candidatos")->fetch_assoc()['total'];
$totalVacantes       = $conn->query("SELECT COUNT(*) AS total FROM vacantes")->fetch_assoc()['total'];
$totalPostulaciones  = $conn->query("SELECT COUNT(*) AS total FROM postulaciones")->fetch_assoc()['total'];

$recientes = $conn->query("SELECT r.nombre_completo, u.correo, e.nombre AS empresa, r.estado
                            FROM reclutadores r
                            INNER JOIN usuarios u ON r.usuario_id = u.id
                            LEFT JOIN empresas e ON r.empresa_id = e.id
                            ORDER BY r.id DESC
                            LIMIT 3");

include "includes/header.php";
?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>



    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TOPBAR -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold">
                    ¡Bienvenido, Administrador!
                </h3>

                <p class="text-muted">
                    Gestiona reclutadores, candidatos y vacantes del sistema.
                </p>

            </div>

            <!-- PERFIL -->
            <div class="admin-profile d-flex align-items-center">

                <i class="bi bi-bell-fill me-4 fs-5"></i>

                <div class="d-flex align-items-center">

                    <img
                        src="../assets/img/admin-avatar.png"
                        class="rounded-circle me-2"
                        width="40"
                        height="40"
                        alt="Admin">

                    <span class="fw-semibold">
                        Admin
                    </span>

                </div>

            </div>

        </div>





        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-person-badge-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            <?php echo $totalReclutadores; ?>
                        </h3>

                        <p class="text-muted mb-0">
                            Reclutadores
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-people-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            <?php echo $totalCandidatos; ?>
                        </h3>

                        <p class="text-muted mb-0">
                            Candidatos
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-briefcase-fill text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            <?php echo $totalVacantes; ?>
                        </h3>

                        <p class="text-muted mb-0">
                            Vacantes
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-danger-subtle">

                        <i class="bi bi-bar-chart-fill text-danger"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            <?php echo number_format($totalPostulaciones); ?>
                        </h3>

                        <p class="text-muted mb-0">
                            Postulaciones
                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- TABLAS -->
        <div class="row g-4">

            <!-- RECLUTADORES -->
            <div class="col-md-8">

                <div class="table-box">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="fw-bold">
                            Reclutadores recientes
                        </h5>

                        <a href="reclutadores.php"
                           class="btn btn-primary btn-sm">

                            Ver todos

                        </a>

                    </div>

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Empresa</th>
                                <th>Estado</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            if ($recientes && $recientes->num_rows > 0) {
                                while ($row = $recientes->fetch_assoc()) {
                                    $estado = strtolower($row['estado']);

                                    if ($estado === 'activo') {
                                        $badge = 'bg-success';
                                    } elseif ($estado === 'pendiente') {
                                        $badge = 'bg-warning text-dark';
                                    } elseif ($estado === 'bloqueado') {
                                        $badge = 'bg-danger';
                                    } else {
                                        $badge = 'bg-secondary';
                                    }

                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['nombre_completo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['empresa'] ?? 'Sin empresa') . '</td>';
                                    echo '<td><span class="badge ' . $badge . '">' . htmlspecialchars(ucfirst($estado)) . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center text-muted">Sin reclutadores registrados.</td></tr>';
                            }
                            ?>

                        </tbody>

                    </table>

                </div>

            </div>





            <!-- ACCIONES -->
                    <div class="col-md-4">

                     <div class="action-box">

                         <h5 class="fw-bold mb-4">
                                Acciones rápidas
                         </h5>





                         <!-- RECLUTADOR -->
                            <a href="../reclutador/register.php"
                               class="btn btn-primary w-100 mb-3">

                              <i class="bi bi-person-badge-fill me-2"></i>

                              Agregar Reclutador

                         </a>





                          <!-- CANDIDATO -->
                         <a href="../candidatos/register.php"
           class="btn btn-success w-100 mb-3">

                              <i class="bi bi-people-fill me-2"></i>

                              Agregar Candidato

                            </a>





                         <!-- ADMIN -->
                            <a href="../admin/register.php"
                              class="btn btn-dark w-100 mb-3">

                             <i class="bi bi-shield-lock-fill me-2"></i>

                             Agregar Administrador

                         </a>





                         <!-- VACANTES -->
                         <a href="../vacantes/vacantes.php"
                              class="btn btn-warning w-100 mb-3">

                              <i class="bi bi-briefcase-fill me-2"></i>

                              Gestionar Vacantes

                         </a>





                         <!-- REPORTES -->
                         <a href="../reportes/reportes.php"
                               class="btn btn-danger w-100">

                             <i class="bi bi-bar-chart-fill me-2"></i>

                             Generar Reporte

                         </a>

                     </div>

                </div>
        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
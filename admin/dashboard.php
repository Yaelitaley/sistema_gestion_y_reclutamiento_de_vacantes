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


    <?php include "includes/sidebar.php"; ?>

    <div class="content">

        <?php include "includes/topbar.php"; ?>


        <!-- CARDS -->
        <div class=" row g-4 mb-4">

            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon icon-blue">

                        <i class="bi bi-person-workspace text-primary"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">
                            <?php echo $totalReclutadores; ?>
                        </h3>

                        <p class="texto mb-0">
                            Reclutadores
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                   <div class="card-icon icon-green">

                        <i class="bi bi-person-vcard-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">
                            <?php echo $totalCandidatos; ?>
                        </h3>

                        <p class="texto mb-0">
                            Candidatos
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon icon-orange">

                        <i class="bi bi-building-fill-check text-warning"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">
                            <?php echo $totalVacantes; ?>
                        </h3>

                        <p class="texto mb-0">
                            Vacantes
                        </p>

                    </div>

                </div>

            </div>





            <!-- CARD -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon icon-purple">

                        <i class="bi bi-clipboard2-check-fill text-danger"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">
                            <?php echo number_format($totalPostulaciones); ?>
                        </h3>

                        <p class="texto mb-0">
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

                        <h5 class="texto fw-bold">
                            Reclutadores recientes
                        </h5>

                        <a href="../reclutador/register.php"
   class="btn btn-dashboard btn-purple w-100 mb-3">

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
                               class="btn btn-dashboard btn-blue">

                              <i class="bi bi-person-workspace me-2"></i>

                              Agregar Reclutador

                         </a>



<br><br>

                          <!-- CANDIDATO -->
                         <a href="../candidatos/register.php"
                           class="btn btn-dashboard btn-green">

                              <i class="bi bi-person-vcard-fill me-2"></i>

                              Agregar Candidato

                            </a>


<br><br>


                         <!-- ADMIN -->
                            <a href="../admin/register.php"
                              class="btn btn-dashboard btn-purple">

                             <i class="bi bi-shield-lock-fill me-2"></i>

                             Agregar Administrador

                         </a>


<br><br>


                         <!-- VACANTES -->
                         <a href="../vacantes/vacantes.php"
                              class="btn btn-dashboard btn-orange">

                              <i class="bi bi-briefcase-fill me-2"></i>

                              Gestionar Vacantes

                         </a>

<br>
<br>


                         <!-- REPORTES -->
                         <a href="../reportes/reportes.php"
                             class="btn btn-dashboard btn-red">

                             <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>

                             Ver Reporte

                         </a>

                     </div>

                </div>
        </div>

    </div>


<?php include "includes/footer.php"; ?>
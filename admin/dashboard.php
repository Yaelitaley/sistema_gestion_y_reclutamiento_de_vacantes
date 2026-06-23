
<?php include "includes/header.php"; ?>

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
                        src="../assets/img/imagenadministrador.png"
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
                            12
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
                            356
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
                            28
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
                            1,248
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

                            <tr>

                                <td>Juan Pérez</td>
                                <td>juan@gmail.com</td>
                                <td>Tech Solutions</td>

                                <td>
                                    <span class="badge bg-success">
                                        Activo
                                    </span>
                                </td>

                            </tr>





                            <tr>

                                <td>Ana López</td>
                                <td>ana@gmail.com</td>
                                <td>Global Corp</td>

                                <td>
                                    <span class="badge bg-warning text-dark">
                                        Pendiente
                                    </span>
                                </td>

                            </tr>





                            <tr>

                                <td>Carlos Ruiz</td>
                                <td>carlos@gmail.com</td>
                                <td>Dev Company</td>

                                <td>
                                    <span class="badge bg-danger">
                                        Bloqueado
                                    </span>
                                </td>

                            </tr>

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
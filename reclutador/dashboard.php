<?php include "includes/header.php"; ?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>

    <div class="content w-100 p-4">

        <?php include "includes/topbar.php"; ?>


        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-building-check text-primary"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">8</h3>

                        <p class="texto mb-0">

                            Vacantes Activas

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-person-vcard-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">134</h3>

                        <p class="texto  mb-0">

                            Postulantes

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-calendar2-check-fill text-warning"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">15</h3>

                        <p class="texto  mb-0">

                            Entrevistas

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-info-subtle">

                        <i class="bi bi-envelope-check-fill text-info"></i>

                    </div>

                    <div>

                        <h3 class="texto fw-bold">5</h3>

                        <p class="texto mb-0">

                            Ofertas Enviadas

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- TABLAS -->
        <div class="row g-4">

            <!-- VACANTES -->
            <div class="col-lg-8">

                <div class="table-box">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="texto fw-bold">

                            Mis Procesos Activos

                        </h5>

                        <a href="vacantes.php"
                           class="btn btn-sm btn-primary">

                            Ver Vacantes

                        </a>

                    </div>

                    <table class="table table-hover">

                        <thead>

                            <tr>

                                <th>Puesto</th>

                                <th>Postulados</th>

                                <th>Estado</th>

                                <th>Actualizado</th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>Desarrollador Backend</td>

                                <td>22</td>

                                <td>

                                    <span class="badge bg-success">

                                        En proceso

                                    </span>

                                </td>

                                <td>Hoy</td>

                            </tr>

                            <tr>

                                <td>Diseñador UI/UX</td>

                                <td>15</td>

                                <td>

                                    <span class="badge bg-warning text-dark">

                                        Entrevistas

                                    </span>

                                </td>

                                <td>Ayer</td>

                            </tr>

                            <tr>

                                <td>Marketing Digital</td>

                                <td>10</td>

                                <td>

                                    <span class="badge bg-primary">

                                        Publicada

                                    </span>

                                </td>

                                <td>Hace 2 días</td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- ENTREVISTAS -->
            <div class="col-lg-4">

                <div class="action-box">

                    <h5 class="texto fw-bold mb-4">

                        Próximas Entrevistas

                    </h5>

                    <div class="mb-4">

                        <h6 class="texto fw-bold">

                            Ana López

                        </h6>

                        <small class="texto text-muted">

                            Desarrollador Backend

                        </small>

                        <br>

                        <small>

                            10:00 AM

                        </small>

                    </div>

                    <div class="mb-4">

                        <h6 class="texto fw-bold">

                            Diego Martínez

                        </h6>

                        <small class="texto text-muted">

                            Diseñador UI

                        </small>

                        <br>

                        <small>

                            12:00 PM

                        </small>

                    </div>

                    <a href="entrevistas.php"
                       class="btn btn-reclutador w-100">

                        Ver Entrevistas

                    </a>

                </div>

            </div>

        </div>

        <!-- PROCESO -->
        <div class="row mt-4">

            <div class="col-12">

                <div class="table-box">

                    <h5 class="texto fw-bold mb-4">

                        Etapas de Reclutamiento

                    </h5>

                    <div class="row text-center">

                        <div class="col">

                            <h2>32</h2>

                            <p>Postulados</p>

                        </div>

                        <div class="col">

                            <h2>27</h2>

                            <p>Revisión</p>

                        </div>

                        <div class="col">

                            <h2>18</h2>

                            <p>Entrevistas</p>

                        </div>

                        <div class="col">

                            <h2>9</h2>

                            <p>Contratados</p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
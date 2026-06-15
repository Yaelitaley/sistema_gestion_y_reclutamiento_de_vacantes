<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>



    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TOP -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">
                    Reportes
                </h2>

                <p class="text-muted">
                    Estadísticas y análisis del sistema
                </p>

            </div>

        </div>





        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <!-- VACANTES -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-briefcase-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            56
                        </h3>

                        <p class="text-muted mb-0">
                            Vacantes Publicadas
                        </p>

                    </div>

                </div>

            </div>





            <!-- ENTREVISTAS -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-person-video3 text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            28
                        </h3>

                        <p class="text-muted mb-0">
                            Entrevistas Realizadas
                        </p>

                    </div>

                </div>

            </div>





            <!-- CONTRATACIONES -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-bar-chart-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            15
                        </h3>

                        <p class="text-muted mb-0">
                            Contrataciones
                        </p>

                    </div>

                </div>

            </div>





            <!-- RECHAZADOS -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-danger-subtle">

                        <i class="bi bi-x-circle-fill text-danger"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            48
                        </h3>

                        <p class="text-muted mb-0">
                            Candidatos Rechazados
                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- GRAFICAS -->
        <div class="row g-4">

            <!-- GRAFICA 1 -->
            <div class="col-md-8">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Candidatos por estado
                    </h5>

                    <!-- GRAFICA FALSA -->
                    <div class="d-flex justify-content-center align-items-center flex-column">

                        <div class="fake-chart-pie mb-4">

                            <div class="pie-circle"></div>

                        </div>





                        <!-- ESTADOS -->
                        <div class="row w-100 text-center">

                            <div class="col-md-3">

                                <span class="badge bg-primary">
                                    Postulado
                                </span>

                                <p class="mt-2">
                                    98
                                </p>

                            </div>





                            <div class="col-md-3">

                                <span class="badge bg-success">
                                    En revisión
                                </span>

                                <p class="mt-2">
                                    64
                                </p>

                            </div>





                            <div class="col-md-3">

                                <span class="badge bg-warning text-dark">
                                    Entrevista
                                </span>

                                <p class="mt-2">
                                    26
                                </p>

                            </div>





                            <div class="col-md-3">

                                <span class="badge bg-danger">
                                    Rechazado
                                </span>

                                <p class="mt-2">
                                    42
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>





            <!-- GRAFICA 2 -->
            <div class="col-md-4">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Candidatos por mes
                    </h5>

                    <!-- BARRAS -->
                    <div class="fake-bars d-flex align-items-end justify-content-around">

                        <div class="bar" style="height: 80px;"></div>
                        <div class="bar" style="height: 120px;"></div>
                        <div class="bar" style="height: 60px;"></div>
                        <div class="bar" style="height: 150px;"></div>
                        <div class="bar" style="height: 100px;"></div>
                        <div class="bar" style="height: 170px;"></div>

                    </div>

                </div>

            </div>





            <!-- GRAFICA 3 -->
            <div class="col-md-6">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Vacantes publicadas vs entrevistas
                    </h5>

                    <div class="fake-line-chart">

                        <div class="line"></div>

                    </div>

                </div>

            </div>





            <!-- GRAFICA 4 -->
            <div class="col-md-6">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Contrataciones por mes
                    </h5>

                    <div class="fake-bars d-flex align-items-end justify-content-around">

                        <div class="bar bg-success" style="height: 40px;"></div>
                        <div class="bar bg-success" style="height: 60px;"></div>
                        <div class="bar bg-success" style="height: 100px;"></div>
                        <div class="bar bg-success" style="height: 140px;"></div>
                        <div class="bar bg-success" style="height: 170px;"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
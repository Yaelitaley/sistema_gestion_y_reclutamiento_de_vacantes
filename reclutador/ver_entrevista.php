<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <?php include "includes/topbar.php"; ?>

        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">

                    Detalle de la Entrevista

                </h2>

                <p class="text-muted">

                    Consulta la información de la entrevista y la evaluación del candidato.

                </p>

            </div>

            <a href="entrevistas.php"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left me-2"></i>

                Regresar

            </a>

        </div>





        <div class="row g-4">

            <!-- INFORMACION -->
            <div class="col-lg-8">

                <div class="table-box">

                    <h4 class="fw-bold mb-4">

                        Información General

                    </h4>

                    <table class="table table-borderless">

                        <tbody>

                            <tr>

                                <th width="35%">Candidato</th>

                                <td>Juan Pérez Hernández</td>

                            </tr>

                            <tr>

                                <th>Vacante</th>

                                <td>Desarrollador Backend</td>

                            </tr>

                            <tr>

                                <th>Empresa</th>

                                <td>Tech Solutions</td>

                            </tr>

                            <tr>

                                <th>Entrevistador</th>

                                <td>María González</td>

                            </tr>

                            <tr>

                                <th>Fecha</th>

                                <td>25 Junio 2026</td>

                            </tr>

                            <tr>

                                <th>Hora</th>

                                <td>10:00 AM</td>

                            </tr>

                            <tr>

                                <th>Lugar</th>

                                <td>Sala de Juntas A</td>

                            </tr>

                            <tr>

                                <th>Modalidad</th>

                                <td>Presencial</td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>





            <!-- ESTADO -->
            <div class="col-lg-4">

                <div class="action-box">

                    <h4 class="fw-bold mb-4">

                        Estado

                    </h4>

                    <div class="text-center mb-4">

                        <span class="badge bg-warning text-dark fs-6 p-3">

                            Pendiente

                        </span>

                    </div>

                    <div class="d-grid gap-3">

                        <button class="btn btn-success">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            Marcar Completada

                        </button>

                        <button class="btn btn-danger">

                            <i class="bi bi-x-circle-fill me-2"></i>

                            Denegar

                        </button>

                    </div>

                </div>

            </div>





            <!-- OBSERVACIONES -->
            <div class="col-lg-12">

                <div class="table-box">

                    <h4 class="fw-bold mb-4">

                        Observaciones

                    </h4>

                    <textarea
                        class="form-control"
                        rows="6"
                        placeholder="Escriba aquí las observaciones de la entrevista..."></textarea>

                </div>

            </div>





            <!-- EVALUACION -->
            <div class="col-lg-12">

                <div class="table-box">

                    <h4 class="fw-bold mb-4">

                        Evaluación del Candidato

                    </h4>

                    <div class="row text-center">

                        <div class="col-md-3">

                            <div class="dashboard-card">

                                <h2 class="text-success">

                                    95%

                                </h2>

                                <p>

                                    Comunicación

                                </p>

                            </div>

                        </div>





                        <div class="col-md-3">

                            <div class="dashboard-card">

                                <h2 class="text-primary">

                                    90%

                                </h2>

                                <p>

                                    Conocimientos

                                </p>

                            </div>

                        </div>





                        <div class="col-md-3">

                            <div class="dashboard-card">

                                <h2 class="text-warning">

                                    88%

                                </h2>

                                <p>

                                    Trabajo en Equipo

                                </p>

                            </div>

                        </div>





                        <div class="col-md-3">

                            <div class="dashboard-card">

                                <h2 class="text-danger">

                                    92%

                                </h2>

                                <p>

                                    Desempeño General

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>





            <!-- COMENTARIOS -->
            <div class="col-lg-12">

                <div class="table-box">

                    <h4 class="fw-bold mb-4">

                        Comentarios Finales

                    </h4>

                    <p class="text-muted">

                        El candidato demuestra conocimientos sólidos en desarrollo web,
                        buena comunicación y experiencia en proyectos utilizando PHP,
                        Bootstrap y MySQL. Se recomienda continuar con el proceso de
                        contratación.

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
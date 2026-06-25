<?php include "includes/header.php"; ?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>

    <div class="content w-100 p-4">

        <?php include "includes/topbar.php"; ?>

       <div class="d-flex justify-content-between align-items-center mb-4">

    <h4 class="fw-bold">

        Lista de Entrevistas

    </h4>

    <div class="d-flex gap-2">

        <input
            type="text"
            class="form-control"
            placeholder="Buscar entrevista...">

        <a href="crear_entrevista.php"
   class="btn btn-primary">

    <i class="bi bi-plus-circle-fill me-2"></i>

    Nueva Entrevista

</a>

    </div>

</div>





        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-clock-fill text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            12

                        </h3>

                        <p class="mb-0">

                            Pendientes

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-check-circle-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            26

                        </h3>

                        <p class="mb-0">

                            Completadas

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-danger-subtle">

                        <i class="bi bi-x-circle-fill text-danger"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            5

                        </h3>

                        <p class="mb-0">

                            Denegadas

                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- TABLA -->
        <div class="table-box">

            

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Candidato</th>

                        <th>Vacante</th>

                        <th>Fecha</th>

                        <th>Hora</th>

                        <th>Estado</th>

                        <th class="text-center">

                            Acciones

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Juan Pérez</td>

                        <td>Desarrollador Backend</td>

                        <td>25/06/2026</td>

                        <td>10:00 AM</td>

                        <td>

                            <span class="badge bg-warning text-dark">

                                Pendiente

                            </span>

                        </td>

                        <td class="text-center">

                            <a href="ver_entrevista.php"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>

                                Ver

                            </a>

                        </td>

                    </tr>





                    <tr>

                        <td>Ana López</td>

                        <td>Diseñadora UI/UX</td>

                        <td>24/06/2026</td>

                        <td>12:00 PM</td>

                        <td>

                            <span class="badge bg-success">

                                Completada

                            </span>

                        </td>

                        <td class="text-center">

                            <a href="ver_entrevista.php"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>

                                Ver

                            </a>

                        </td>

                    </tr>





                    <tr>

                        <td>Carlos Ruiz</td>

                        <td>Marketing Digital</td>

                        <td>23/06/2026</td>

                        <td>09:00 AM</td>

                        <td>

                            <span class="badge bg-danger">

                                Denegada

                            </span>

                        </td>

                        <td class="text-center">

                            <a href="ver_entrevista.php"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>

                                Ver

                            </a>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
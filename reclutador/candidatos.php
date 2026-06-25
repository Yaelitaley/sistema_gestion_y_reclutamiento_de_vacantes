<?php include "includes/header.php"; ?>

<div class="d-flex">

    <?php include "includes/sidebar.php"; ?>

    <div class="content w-100 p-4">

        <?php include "includes/topbar.php"; ?>

        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">

                    Gestión de Candidatos

                </h2>

                <p class="text-muted">

                    Consulta los candidatos postulados a tus vacantes.

                </p>

            </div>

        </div>





        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-people-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            145

                        </h3>

                        <p class="mb-0 text-muted">

                            Total Candidatos

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-search text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            36

                        </h3>

                        <p class="mb-0 text-muted">

                            En Revisión

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-info-subtle">

                        <i class="bi bi-calendar-event-fill text-info"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            18

                        </h3>

                        <p class="mb-0 text-muted">

                            Entrevistas

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-person-check-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            9

                        </h3>

                        <p class="mb-0 text-muted">

                            Contratados

                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- TABLA -->
        <div class="table-box">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h4 class="fw-bold">

                    Lista de Candidatos

                </h4>

                <input
                    type="text"
                    class="form-control w-25"
                    placeholder="Buscar candidato...">

            </div>

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Foto</th>

                        <th>Nombre</th>

                        <th>Vacante</th>

                        <th>Estado</th>

                        <th>Fecha</th>

                        <th class="text-center">

                            Acciones

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>

                            <img
                                src="../assets/img/avatar.png"
                                width="50"
                                class="rounded-circle">

                        </td>

                        <td>

                            Juan Pérez Hernández

                        </td>

                        <td>

                            Desarrollador Backend

                        </td>

                        <td>

                            <span class="badge bg-warning">

                                En revisión

                            </span>

                        </td>

                        <td>

                            Hoy

                        </td>

                        <td class="text-center">

                            <a href="ver_candidatos.php"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>

                                Ver

                            </a>

                        </td>

                    </tr>





                    <tr>

                        <td>

                            <img
                                src="../assets/img/avatar.png"
                                width="50"
                                class="rounded-circle">

                        </td>

                        <td>

                            Ana López

                        </td>

                        <td>

                            Diseñadora UI/UX

                        </td>

                        <td>

                            <span class="badge bg-info">

                                Entrevista

                            </span>

                        </td>

                        <td>

                            Ayer

                        </td>

                        <td class="text-center">

                            <a href="ver_candidatos.php"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>

                                Ver

                            </a>

                        </td>

                    </tr>





                    <tr>

                        <td>

                            <img
                                src="../assets/img/avatar.png"
                                width="50"
                                class="rounded-circle">

                        </td>

                        <td>

                            Carlos Ruiz

                        </td>

                        <td>

                            Marketing Digital

                        </td>

                        <td>

                            <span class="badge bg-success">

                                Contratado

                            </span>

                        </td>

                        <td>

                            18/06/2026

                        </td>

                        <td class="text-center">

                            <a href="ver_candidatos.php"
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


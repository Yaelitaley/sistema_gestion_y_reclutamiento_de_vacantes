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

                    Mi Perfil

                </h2>

                <p class="text-muted">

                    Consulta la información de tu perfil como reclutador.

                </p>

            </div>

        </div>





        <div class="row">

            <!-- FOTO -->
            <div class="col-lg-4">

                <div class="table-box text-center">

                    <img
                        src="../assets/img/reclutador-avatar.png"
                        class="rounded-circle shadow mb-3"
                        width="180"
                        height="180"
                        alt="Reclutador">

                    <h3 class="fw-bold">

                        María González

                    </h3>

                    <p class="text-muted">

                        Reclutadora Senior

                    </p>

                    <span class="badge bg-success">

                        Activo

                    </span>

                    <hr>

                    <a
    href="editar_perfil.php"
    class="btn btn-primary">

    <i class="bi bi-pencil-fill me-2"></i>

    Editar Perfil

</a>

                </div>

            </div>





            <!-- INFORMACION -->
            <div class="col-lg-8">

                <div class="table-box">

                    <h4 class="fw-bold mb-4">

                        Información Personal

                    </h4>

                    <table class="table table-borderless">

                        <tbody>

                            <tr>

                                <th width="35%">

                                    Nombre Completo

                                </th>

                                <td>

                                    María González Pérez

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Correo Electrónico

                                </th>

                                <td>

                                    maria@empresa.com

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Teléfono

                                </th>

                                <td>

                                    981 123 4567

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Empresa

                                </th>

                                <td>

                                    Tech Solutions

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Cargo

                                </th>

                                <td>

                                    Reclutadora Senior

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Departamento

                                </th>

                                <td>

                                    Recursos Humanos

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Ciudad

                                </th>

                                <td>

                                    Campeche, México

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Fecha de Registro

                                </th>

                                <td>

                                    15 Enero 2026

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>





        <!-- ESTADISTICAS -->
        <div class="row mt-4">

            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-briefcase-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            18

                        </h3>

                        <p class="mb-0">

                            Vacantes Publicadas

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-people-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            96

                        </h3>

                        <p class="mb-0">

                            Candidatos Gestionados

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-md-4">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-calendar-check-fill text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            28

                        </h3>

                        <p class="mb-0">

                            Entrevistas Realizadas

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
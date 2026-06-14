<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>



    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">
                    Gestión de Vacantes
                </h2>

                <p class="text-muted">
                    Administra y elimina vacantes del sistema
                </p>

            </div>

            <!-- BOTON -->
            <a href="../vacantes/register.php"
               class="btn btn-primary">

                <i class="bi bi-plus-circle me-2"></i>
                Nueva Vacante

            </a>

        </div>





        <!-- CARDS -->
        <div class="row g-4 mb-4">

            <!-- ACTIVAS -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-briefcase-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            18
                        </h3>

                        <p class="text-muted mb-0">
                            Vacantes Activas
                        </p>

                    </div>

                </div>

            </div>





            <!-- INACTIVAS -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-secondary-subtle">

                        <i class="bi bi-file-earmark-fill text-secondary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            7
                        </h3>

                        <p class="text-muted mb-0">
                            Vacantes Inactivas
                        </p>

                    </div>

                </div>

            </div>





            <!-- POSTULACIONES -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-people-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            324
                        </h3>

                        <p class="text-muted mb-0">
                            Total Postulaciones
                        </p>

                    </div>

                </div>

            </div>





            <!-- CATEGORIAS -->
            <div class="col-md-3">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-bar-chart-fill text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">
                            5
                        </h3>

                        <p class="text-muted mb-0">
                            Áreas o Categorías
                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- TABLA -->
        <div class="table-box">

            <!-- TOP -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="fw-bold">
                    Lista de Vacantes
                </h5>

            </div>





            <!-- BUSQUEDA -->
            <div class="row g-3 mb-4">

                <!-- SEARCH -->
                <div class="col-md-3">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Buscar vacante...">

                </div>





                <!-- ESTADO -->
                <div class="col-md-3">

                    <select class="form-select">

                        <option>
                            Todos los estados
                        </option>

                        <option>
                            Activo
                        </option>

                        <option>
                            Inactivo
                        </option>

                    </select>

                </div>





                <!-- CATEGORIA -->
                <div class="col-md-3">

                    <select class="form-select">

                        <option>
                            Todas las categorías
                        </option>

                        <option>
                            Tecnología
                        </option>

                        <option>
                            Diseño
                        </option>

                        <option>
                            Marketing
                        </option>

                    </select>

                </div>





                <!-- BOTON -->
                <div class="col-md-3">

                    <button class="btn btn-primary w-100">

                        <i class="bi bi-search me-2"></i>
                        Aplicar filtros

                    </button>

                </div>

            </div>





            <!-- TABLA -->
            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Vacante</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Postulaciones</th>
                        <th>Acciones</th>

                    </tr>

                </thead>





                <tbody>

                    <!-- FILA -->
                    <tr>

                        <td>Desarrollador Backend</td>

                        <td>
                            <span class="badge bg-primary">
                                Tecnología
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-success">
                                Activo
                            </span>
                        </td>

                        <td>35</td>

                        <td>

                            <!-- EDITAR -->
                            <a href="edit_vacante.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                             <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>


                        </td>

                    </tr>





                    <!-- FILA -->
                    <tr>

                        <td>Diseñador UI/UX</td>

                        <td>
                            <span class="badge bg-danger">
                                Diseño
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-success">
                                Activo
                            </span>
                        </td>

                        <td>20</td>

                        <td>

                            <a href="edit_vacante.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                             <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>


                        </td>

                    </tr>





                    <!-- FILA -->
                    <tr>

                        <td>Marketing Digital</td>

                        <td>
                            <span class="badge bg-warning text-dark">
                                Marketing
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-danger">
                                Inactivo
                            </span>
                        </td>

                        <td>10</td>

                        <td>

                            <a href="edit_vacante.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                             <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>


                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
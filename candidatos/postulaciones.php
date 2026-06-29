<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>



        <!-- TÍTULO -->
        <div class="mb-4">

            <h2 class="fw-bold">

                Mis Postulaciones

            </h2>

            <p class="text-muted">

                Consulta el estado de todas las vacantes a las que te has postulado.

            </p>

        </div>



        <!-- TARJETAS -->
        <div class="row g-4 mb-5">

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-send-check-fill text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            12

                        </h3>

                        <p class="text-muted mb-0">

                            Postulaciones

                        </p>

                    </div>

                </div>

            </div>



            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-clock-history text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            4

                        </h3>

                        <p class="text-muted mb-0">

                            En Revisión

                        </p>

                    </div>

                </div>

            </div>



            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-info-subtle">

                        <i class="bi bi-calendar-event-fill text-info"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            3

                        </h3>

                        <p class="text-muted mb-0">

                            Entrevistas

                        </p>

                    </div>

                </div>

            </div>



            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-check-circle-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            1

                        </h3>

                        <p class="text-muted mb-0">

                            Contratado

                        </p>

                    </div>

                </div>

            </div>

        </div>



        <!-- BUSCADOR -->
        <div class="table-box mb-4">

            <div class="row g-3">

                <div class="col-lg-8">

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-search"></i>

                        </span>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Buscar empresa o puesto...">

                    </div>

                </div>



                <div class="col-lg-2">

                    <select class="form-select">

                        <option>Todas</option>

                        <option>En revisión</option>

                        <option>Entrevista</option>

                        <option>Contratado</option>

                        <option>Rechazado</option>

                    </select>

                </div>



                <div class="col-lg-2">

                    <button class="btn btn-success w-100">

                        Buscar

                    </button>

                </div>

            </div>

        </div>

                <!-- TABLA DE POSTULACIONES -->
        <div class="table-box">

            <h4 class="fw-bold mb-4">

                Historial de Postulaciones

            </h4>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Puesto</th>

                            <th>Empresa</th>

                            <th>Ubicación</th>

                            <th>Fecha</th>

                            <th>Estado</th>

                            <th class="text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>Frontend Developer</td>

                            <td>Google México</td>

                            <td>Ciudad de México</td>

                            <td>15/06/2026</td>

                            <td>

                                <span class="badge bg-warning text-dark">

                                    En revisión

                                </span>

                            </td>

                            <td class="text-center">

                                <a
                                    href="ver-empleo.php"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <button
    class="btn btn-sm btn-outline-danger btnCancelarPostulacion">

    <i class="bi bi-x-circle-fill"></i>

</button>

                            </td>

                        </tr>





                        <tr>

                            <td>UI / UX Designer</td>

                            <td>Microsoft</td>

                            <td>Guadalajara</td>

                            <td>13/06/2026</td>

                            <td>

                                <span class="badge bg-info">

                                    Entrevista

                                </span>

                            </td>

                            <td class="text-center">

                                <a
                                    href="ver-empleo.php"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <button
    class="btn btn-sm btn-outline-danger btnCancelarPostulacion">

    <i class="bi bi-x-circle-fill"></i>

</button>

                            </td>

                        </tr>





                        <tr>

                            <td>Backend Developer</td>

                            <td>Oracle</td>

                            <td>Querétaro</td>

                            <td>10/06/2026</td>

                            <td>

                                <span class="badge bg-success">

                                    Contratado

                                </span>

                            </td>

                            <td class="text-center">

                                <a
                                    href="ver-empleo.php"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <button
    class="btn btn-sm btn-outline-danger btnCancelarPostulacion">

    <i class="bi bi-x-circle-fill"></i>

</button>

                            </td>

                        </tr>





                        <tr>

                            <td>Analista de Datos</td>

                            <td>Amazon</td>

                            <td>Monterrey</td>

                            <td>08/06/2026</td>

                            <td>

                                <span class="badge bg-danger">

                                    Rechazado

                                </span>

                            </td>

                            <td class="text-center">

                                <a
                                    href="ver-empleo.php"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <button
    class="btn btn-sm btn-outline-danger btnCancelarPostulacion">

    <i class="bi bi-x-circle-fill"></i>

</button>

                            </td>

                        </tr>





                        <tr>

                            <td>Marketing Digital</td>

                            <td>Mercado Libre</td>

                            <td>Ciudad de México</td>

                            <td>06/06/2026</td>

                            <td>

                                <span class="badge bg-warning text-dark">

                                    En revisión

                                </span>

                            </td>

                            <td class="text-center">

                                <a
                                    href="ver-empleo.php"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <button
    class="btn btn-sm btn-outline-danger btnCancelarPostulacion">

    <i class="bi bi-x-circle-fill"></i>

</button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

                <!-- PAGINACIÓN -->
        <nav class="mt-4">

            <ul class="pagination justify-content-center">

                <li class="page-item disabled">

                    <a class="page-link">

                        Anterior

                    </a>

                </li>

                <li class="page-item active">

                    <a class="page-link">

                        1

                    </a>

                </li>

                <li class="page-item">

                    <a class="page-link">

                        2

                    </a>

                </li>

                <li class="page-item">

                    <a class="page-link">

                        3

                    </a>

                </li>

                <li class="page-item">

                    <a class="page-link">

                        Siguiente

                    </a>

                </li>

            </ul>

        </nav>





        <!-- RESUMEN -->
        <div class="row mt-5">

            <div class="col-lg-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-graph-up-arrow text-success"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold">

                            Resumen

                        </h5>

                        <p class="mb-0 text-muted">

                            Has enviado <strong>12 postulaciones</strong>,
                            de las cuales <strong>4</strong> continúan en revisión
                            y <strong>3</strong> ya avanzaron a entrevista.

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-lg-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-lightbulb-fill text-primary"></i>

                    </div>

                    <div>

                        <h5 class="fw-bold">

                            Recomendación

                        </h5>

                        <p class="mb-0 text-muted">

                            Mantén actualizado tu perfil y tu currículum para
                            aumentar tus posibilidades de ser seleccionado.

                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- ACCIONES -->
        <div class="table-box mt-5">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h4 class="fw-bold">

                        ¿Deseas buscar nuevas oportunidades?

                    </h4>

                    <p class="text-muted mb-0">

                        Explora nuevas vacantes disponibles y continúa creciendo profesionalmente.

                    </p>

                </div>





                <div class="mt-3 mt-lg-0">

                    <a
                        href="explorar-empleos.php"
                        class="btn btn-success">

                        <i class="bi bi-search me-2"></i>

                        Explorar Empleos

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
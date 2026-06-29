<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>





        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">

                    Explorar Empleos

                </h2>

                <p class="text-muted">

                    Descubre nuevas oportunidades laborales y encuentra el empleo ideal para ti.

                </p>

            </div>

        </div>





        <!-- BUSCADOR -->
        <div class="table-box mb-4">

            <div class="row g-3">

                <div class="col-lg-10">

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-search"></i>

                        </span>

                        <input
                            type="text"
                            id="buscarEmpleo"
                            class="form-control"
                            placeholder="Buscar puesto, empresa o palabra clave...">

                    </div>

                </div>





                <div class="col-lg-2">

                    <button class="btn btn-success w-100">

                        Buscar

                    </button>

                </div>

            </div>

        </div>





        <!-- FILTROS -->
        <div class="table-box mb-5">

            <h5 class="fw-bold mb-4">

                Filtrar Vacantes

            </h5>

            <div class="row g-3">

                <!-- CATEGORÍA -->
                <div class="col-lg-3">

                    <label class="form-label">

                        Categoría

                    </label>

                    <select class="form-select">

                        <option selected>

                            Todas

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

                        <option>

                            Recursos Humanos

                        </option>

                        <option>

                            Administración

                        </option>

                    </select>

                </div>





                <!-- MODALIDAD -->
                <div class="col-lg-3">

                    <label class="form-label">

                        Modalidad

                    </label>

                    <select class="form-select">

                        <option selected>

                            Todas

                        </option>

                        <option>

                            Presencial

                        </option>

                        <option>

                            Remoto

                        </option>

                        <option>

                            Híbrido

                        </option>

                    </select>

                </div>





                <!-- UBICACIÓN -->
                <div class="col-lg-3">

                    <label class="form-label">

                        Ubicación

                    </label>

                    <select class="form-select">

                        <option selected>

                            Todo México

                        </option>

                        <option>

                            Ciudad de México

                        </option>

                        <option>

                            Guadalajara

                        </option>

                        <option>

                            Monterrey

                        </option>

                        <option>

                            Campeche

                        </option>

                    </select>

                </div>





                <!-- SALARIO -->
                <div class="col-lg-3">

                    <label class="form-label">

                        Salario

                    </label>

                    <select class="form-select">

                        <option selected>

                            Cualquiera

                        </option>

                        <option>

                            $10,000 - $20,000

                        </option>

                        <option>

                            $20,000 - $30,000

                        </option>

                        <option>

                            Más de $30,000

                        </option>

                    </select>

                </div>

            </div>





            <div class="mt-4 d-flex justify-content-end gap-2">

                <button 
                id="btnLimpiar"
                class="btn btn-outline-secondary">

                    Limpiar Filtros

                </button>

                <button class="btn btn-success">

                    Aplicar Filtros

                </button>

            </div>

        </div>

                <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-5">

            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-success-subtle">

                        <i class="bi bi-briefcase-fill text-success"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            125

                        </h3>

                        <p class="text-muted mb-0">

                            Vacantes Disponibles

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-primary-subtle">

                        <i class="bi bi-building text-primary"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            48

                        </h3>

                        <p class="text-muted mb-0">

                            Empresas

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-warning-subtle">

                        <i class="bi bi-laptop text-warning"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            36

                        </h3>

                        <p class="text-muted mb-0">

                            Remotas

                        </p>

                    </div>

                </div>

            </div>





            <div class="col-lg-3 col-md-6">

                <div class="dashboard-card">

                    <div class="card-icon bg-danger-subtle">

                        <i class="bi bi-stars text-danger"></i>

                    </div>

                    <div>

                        <h3 class="fw-bold">

                            18

                        </h3>

                        <p class="text-muted mb-0">

                            Nuevas Hoy

                        </p>

                    </div>

                </div>

            </div>

        </div>





        <!-- LISTADO DE VACANTES -->

        <h4 class="fw-bold mb-4">

            Vacantes Disponibles

        </h4>





        <div class="row g-4">

            <!-- VACANTE 1 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Frontend Developer

                    </h4>

                    <p class="text-muted">

                        Google México

                    </p>

                    <span class="badge bg-success">

                        Tiempo Completo

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Ciudad de México

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $28,000 MXN

                    </p>

                    <p>

                        Desarrollo con HTML, CSS, Bootstrap, JavaScript y React.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                         class="btn btn-success btnPostular">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 2 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Diseñador UI / UX

                    </h4>

                    <p class="text-muted">

                        Microsoft

                    </p>

                    <span class="badge bg-primary">

                        Híbrido

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Guadalajara

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $24,000 MXN

                    </p>

                    <p>

                        Diseño de interfaces para aplicaciones web y móviles.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 3 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Backend Developer

                    </h4>

                    <p class="text-muted">

                        Oracle

                    </p>

                    <span class="badge bg-danger">

                        Presencial

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Querétaro

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $32,000 MXN

                    </p>

                    <p>

                        Desarrollo de APIs con PHP, Laravel y MySQL.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 4 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Analista de Datos

                    </h4>

                    <p class="text-muted">

                        Amazon

                    </p>

                    <span class="badge bg-warning text-dark">

                        Remoto

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Monterrey

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $30,000 MXN

                    </p>

                    <p>

                        Análisis de datos utilizando SQL, Python y Power BI.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>

                        <!-- VACANTE 5 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Especialista en Marketing Digital

                    </h4>

                    <p class="text-muted">

                        Mercado Libre

                    </p>

                    <span class="badge bg-info">

                        Híbrido

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Ciudad de México

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $22,000 MXN

                    </p>

                    <p>

                        Gestión de campañas digitales, SEO y redes sociales.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 6 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Analista Financiero

                    </h4>

                    <p class="text-muted">

                        BBVA

                    </p>

                    <span class="badge bg-primary">

                        Tiempo Completo

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Monterrey

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $27,000 MXN

                    </p>

                    <p>

                        Elaboración de reportes financieros y análisis de indicadores.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 7 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Ingeniero DevOps

                    </h4>

                    <p class="text-muted">

                        IBM

                    </p>

                    <span class="badge bg-warning text-dark">

                        Remoto

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Guadalajara

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $38,000 MXN

                    </p>

                    <p>

                        Administración de servidores, Docker, Kubernetes y CI/CD.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>





            <!-- VACANTE 8 -->
            <div class="col-lg-6">

                <div class="job-card">

                    <h4 class="fw-bold">

                        Auxiliar Administrativo

                    </h4>

                    <p class="text-muted">

                        Grupo Bimbo

                    </p>

                    <span class="badge bg-secondary">

                        Presencial

                    </span>

                    <hr>

                    <p>

                        <i class="bi bi-geo-alt-fill text-danger"></i>

                        Campeche

                    </p>

                    <p>

                        <i class="bi bi-cash-stack text-success"></i>

                        $16,000 MXN

                    </p>

                    <p>

                        Apoyo en actividades administrativas y control documental.

                    </p>

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="../candidatos/ver-empleo.php"
                            class="btn btn-outline-success">

                            Ver Detalles

                        </a>

                        <button
                            class="btn btn-success">

                            Postularme

                        </button>

                    </div>

                </div>

            </div>

        </div>





        <!-- PAGINACIÓN -->
        <nav class="mt-5">

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





        <!-- BANNER FINAL -->
        <div class="table-box mt-5 text-center">

            <h3 class="fw-bold">

                ¿No encontraste el empleo ideal?

            </h3>

            <p class="text-muted">

                Mantén actualizado tu perfil y tu currículum para recibir nuevas recomendaciones de empleo.

            </p>

            <a
                href="perfil.php"
                class="btn btn-success">

                Actualizar Perfil

            </a>

        </div>

    </div>

</div>

<?php include "../candidatos/includes/footer.php"; ?>
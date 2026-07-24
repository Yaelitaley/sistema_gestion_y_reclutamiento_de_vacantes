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
                <h2 class="fw-bold">Explorar Empleos</h2>
                <p class="text-muted">Descubre nuevas oportunidades laborales y encuentra el empleo ideal para ti.</p>
            </div>
        </div>

        <!-- BUSCADOR Y FILTROS (Unificados en un formulario GET) -->
        <form action="explorar-empleos.php" method="GET">
            <!-- BUSCADOR -->
            <div class="tabla mb-4">
                <div class="row g-3">
                    <div class="col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="busqueda" id="buscarEmpleo" class="form-control" placeholder="Buscar puesto, empresa o palabra clave...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-success w-100">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-success-subtle"><i class="bi bi-briefcase-fill text-success"></i></div>
                    <div>
                        <h3 class="fw-bold">125</h3>
                        <p class="text-muted mb-0">Vacantes Disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-primary-subtle"><i class="bi bi-building text-primary"></i></div>
                    <div>
                        <h3 class="fw-bold">48</h3>
                        <p class="text-muted mb-0">Empresas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-warning-subtle"><i class="bi bi-laptop text-warning"></i></div>
                    <div>
                        <h3 class="fw-bold">36</h3>
                        <p class="text-muted mb-0">Remotas</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card">
                    <div class="card-icon bg-danger-subtle"><i class="bi bi-stars text-danger"></i></div>
                    <div>
                        <h3 class="fw-bold">18</h3>
                        <p class="text-muted mb-0">Nuevas Hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTADO DE VACANTES -->
        <h4 class="fw-bold mb-4">Vacantes Disponibles</h4>

        <div class="row g-4">
            <!-- VACANTE 1 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Frontend Developer</h4>
                    <p class="text-muted">Google México</p>
                    <span class="badge bg-success">Tiempo Completo</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Ciudad de México</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $28,000 MXN</p>
                    <p>Desarrollo con HTML, CSS, Bootstrap, JavaScript y React.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 2 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Diseñador UI / UX</h4>
                    <p class="text-muted">Microsoft</p>
                    <span class="badge bg-primary">Híbrido</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Guadalajara</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $24,000 MXN</p>
                    <p>Diseño de interfaces para aplicaciones web y móviles.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 3 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Backend Developer</h4>
                    <p class="text-muted">Oracle</p>
                    <span class="badge bg-danger">Presencial</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Querétaro</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $32,000 MXN</p>
                    <p>Desarrollo de APIs con PHP, Laravel y MySQL.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 4 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Analista de Datos</h4>
                    <p class="text-muted">Amazon</p>
                    <span class="badge bg-warning text-dark">Remoto</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Monterrey</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $30,000 MXN</p>
                    <p>Análisis de datos utilizando SQL, Python y Power BI.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 5 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Especialista en Marketing Digital</h4>
                    <p class="text-muted">Mercado Libre</p>
                    <span class="badge bg-info">Híbrido</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Ciudad de México</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $22,000 MXN</p>
                    <p>Gestión de campañas digitales, SEO y redes sociales.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 6 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Analista Financiero</h4>
                    <p class="text-muted">BBVA</p>
                    <span class="badge bg-primary">Tiempo Completo</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Monterrey</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $27,000 MXN</p>
                    <p>Elaboración de reportes financieros y análisis de indicadores.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 7 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Ingeniero DevOps</h4>
                    <p class="text-muted">IBM</p>
                    <span class="badge bg-warning text-dark">Remoto</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Guadalajara</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $38,000 MXN</p>
                    <p>Administración de servidores, Docker, Kubernetes y CI/CD.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>

            <!-- VACANTE 8 -->
            <div class="col-lg-6">
                <div class="job-card">
                    <h4 class="fw-bold">Auxiliar Administrativo</h4>
                    <p class="text-muted">Grupo Bimbo</p>
                    <span class="badge bg-secondary">Presencial</span>
                    <hr>
                    <p><i class="bi bi-geo-alt-fill text-danger"></i> Campeche</p>
                    <p><i class="bi bi-cash-stack text-success"></i> $16,000 MXN</p>
                    <p>Apoyo en actividades administrativas y control documental.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="ver-empleo.php" class="btn btn-outline-success">Ver Detalles</a>
                        <button class="btn btn-success btnPostular">Postularme</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAGINACIÓN -->
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
            </ul>
        </nav>

        <!-- BANNER FINAL -->
        <div class="table-box mt-5 text-center">
            <h3 class="fw-bold">¿No encontraste el empleo ideal?</h3>
            <p class="text-muted">Mantén actualizado tu perfil y tu currículum para recibir nuevas recomendaciones de empleo.</p>
            <!-- Corregido el enlace a editar-perfil.php -->
            <a href="editar-perfil.php" class="btn btn-success">Actualizar Perfil</a>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
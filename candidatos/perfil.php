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
                    Mi Perfil
                </h2>
                <p class="text-muted">
                    Consulta y administra la información de tu cuenta.
                </p>
            </div>
            <a
                href="editar_perfil.php"
                class="btn btn-success">
                <i class="bi bi-pencil-square me-2"></i>
                Editar Perfil
            </a>
        </div>
        <!-- PERFIL -->
        <div class="table-box mb-4">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center">
                    <img
                        src="../assets/img/candidato.png"
                        class="rounded-circle img-fluid mb-3"
                        style="width:180px; height:180px; object-fit:cover;"
                        alt="Foto de perfil">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-camera-fill me-2"></i>
                        Cambiar Foto
                    </button>
                </div>
                <div class="col-lg-9">
                    <h2 class="fw-bold">
                        Gabriel Montero
                    </h2>
                    <h5 class="text-success">
                        Desarrollador Frontend
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-envelope-fill text-primary me-2"></i>
                                gabriel@email.com
                            </p>
                            <p>
                                <i class="bi bi-telephone-fill text-success me-2"></i>
                                +52 981 000 0000
                            </p>
                            <p>
                                <i class="bi bi-calendar-fill text-warning me-2"></i>
                                21 años
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                Campeche, México
                            </p>
                            <p>
                                <i class="bi bi-person-badge-fill text-info me-2"></i>
                                Candidato
                            </p>
                            <p>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Perfil Verificado
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- INFORMACIÓN PROFESIONAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Información Profesional
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Puesto Deseado:</strong>
                        Desarrollador Frontend
                    </p>
                    <p>
                        <strong>Disponibilidad:</strong>
                        Tiempo Completo
                    </p>
                    <p>
                        <strong>Modalidad:</strong>
                        Remoto / Híbrido
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Experiencia:</strong>
                        1 año
                    </p>
                    <p>
                        <strong>Salario Esperado:</strong>
                        $20,000 MXN
                    </p>
                    <p>
                        <strong>Estado:</strong>
                        Activo
                    </p>
                </div>
            </div>
        </div>
                <!-- ESTADO DEL PERFIL -->
        <div class="table-box mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">
                    Estado del Perfil
                </h4>
                <span class="badge bg-success">
                    85% Completo
                </span>
            </div>
            <p class="text-muted">
                Completa toda la información de tu perfil para aumentar tus oportunidades laborales.
            </p>
            <div class="progress mb-4" style="height: 12px;">
                <div
                    class="progress-bar bg-success"
                    style="width:85%;">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <p>
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Datos Personales
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Currículum
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Experiencia
                    </p>
                </div>
                <div class="col-md-3">
                    <p>
                        <i class="bi bi-x-circle-fill text-danger me-2"></i>
                        Portafolio
                    </p>
                </div>
            </div>
        </div>
        <!-- ESTADÍSTICAS -->
        <div class="row g-4 mb-4">
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
        <!-- ACCESOS RÁPIDOS -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Accesos Rápidos
            </h4>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <a
                        href="cv.php"
                        class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-file-earmark-person-fill fs-4 d-block mb-2"></i>
                        Mi Currículum
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a
                        href="postulaciones.php"
                        class="btn btn-outline-success w-100 py-3">
                        <i class="bi bi-send-check-fill fs-4 d-block mb-2"></i>
                        Mis Postulaciones
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a
                        href="explorar_empleos.php"
                        class="btn btn-outline-warning w-100 py-3">
                        <i class="bi bi-search fs-4 d-block mb-2"></i>
                        Explorar Empleos
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a
                        href="configuracion.php"
                        class="btn btn-outline-secondary w-100 py-3">
                        <i class="bi bi-gear-fill fs-4 d-block mb-2"></i>
                        Configuración
                    </a>
                </div>
            </div>
        </div>
        <!-- REDES PROFESIONALES -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-4">
                Redes Profesionales
            </h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        LinkedIn
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        value="linkedin.com/in/gabrielmontero"
                        readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        GitHub
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        value="github.com/gabrielmontero"
                        readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        Portafolio
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        value="www.gabrielmontero.dev"
                        readonly>
                </div>
            </div>
        </div>
                <!-- RESUMEN DEL PERFIL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">
                Resumen del Perfil
            </h4>
            <p class="text-muted">
                Actualmente soy estudiante de Ingeniería en Programación y Web,
                interesado en desarrollarme como desarrollador Frontend.
                Me apasiona crear interfaces modernas, intuitivas y responsivas,
                utilizando tecnologías como HTML, CSS, Bootstrap, JavaScript,
                PHP y MySQL.
            </p>
        </div>
        <!-- OBJETIVOS PROFESIONALES -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-3">
                Objetivos Profesionales
            </h4>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    Obtener experiencia profesional en desarrollo web.
                </li>
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    Participar en proyectos innovadores.
                </li>
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    Continuar aprendiendo nuevas tecnologías.
                </li>
                <li class="list-group-item">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    Crecer profesionalmente dentro del área de desarrollo de software.
                </li>
            </ul>
        </div>
        <!-- BOTONES -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
            <a
                href="dashboard.php"
                class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
            <div>
                <a
                    href="cv.php"
                    class="btn btn-outline-primary me-2">
                    <i class="bi bi-file-earmark-person-fill me-2"></i>
                    Ver CV
                </a>
                <a
                    href="editar_perfil.php"
                    class="btn btn-success">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar Perfil
                </a>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
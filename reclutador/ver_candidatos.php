<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>
    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TOPBAR -->
        <?php include "includes/topbar.php"; ?>
        <!-- TITULO -->
        <div class="mb-4">
            <h2 class="fw-bold">

                Perfil del Candidato

            </h2>

            <p class="text-muted">

                Consulta la información completa del candidato y administra
                su proceso de selección.

            </p>

        </div>
        <!-- PERFIL -->
        <div class="candidate-profile mb-4">

            <div class="row align-items-center">

                <!-- FOTO -->
                <div class="col-lg-3 text-center">

                    <img
                        src="../assets/img/avatar.png"
                        class="candidate-photo img-fluid rounded-circle"
                        alt="Candidato">

                </div>
                <!-- INFORMACION -->
                <div class="col-lg-6">

                    <h2 class="fw-bold">

                        Juan Pérez Hernández

                    </h2>

                    <h5 class="text-primary mb-4">

                        Desarrollador Web Full Stack

                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-envelope-fill text-primary me-2"></i>

                            juan@gmail.com

                        </div>

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-telephone-fill text-success me-2"></i>

                            981 123 4567

                        </div>

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>

                            Campeche, México

                        </div>

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-calendar-check-fill text-warning me-2"></i>

                            Disponible inmediatamente

                        </div>

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-cash-stack text-success me-2"></i>

                            $18,000 MXN

                        </div>

                        <div class="col-md-6 mb-3">

                            <i class="bi bi-briefcase-fill text-secondary me-2"></i>

                            Tiempo Completo

                        </div>

                    </div>

                </div>

                <!-- ESTADO -->
                <div class="col-lg-3">

                    <div class="status-card">

                        <h5 class="fw-bold mb-4">

                            Estado del proceso

                        </h5>

                        <div class="mb-3">

                            <span class="badge bg-success fs-6">

                                En Entrevista

                            </span>

                        </div>

                        <hr>

                        <div class="timeline">

                            <div class="timeline-item completed">

                                <i class="bi bi-check-circle-fill"></i>

                                Postulación recibida

                            </div>

                            <div class="timeline-item completed">

                                <i class="bi bi-check-circle-fill"></i>

                                CV Revisado

                            </div>

                            <div class="timeline-item active">

                                <i class="bi bi-clock-fill"></i>

                                Entrevista Programada

                            </div>

                            <div class="timeline-item">

                                <i class="bi bi-circle"></i>

                                Contratación

                            </div>

                        </div>

                        <hr>

                        <div class="d-grid gap-2">

                            <button class="btn btn-success">

                                <i class="bi bi-calendar-check-fill me-2"></i>

                                Programar Entrevista

                            </button>

                            <button class="btn btn-primary">

                                <i class="bi bi-chat-dots-fill me-2"></i>

                                Enviar Mensaje

                            </button>

                            <button class="btn btn-danger">

                                <i class="bi bi-x-circle-fill me-2"></i>

                                Rechazar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- INFORMACION -->
        <div class="row g-4">

            <!-- INFORMACION PERSONAL -->
            <div class="col-lg-6">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-person-fill me-2"></i>

                        Información Personal

                    </h4>

                    <table class="table table-borderless">

                        <tbody>

                            <tr>

                                <th width="40%">

                                    Nombre

                                </th>

                                <td>

                                    Juan Pérez Hernández

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Fecha de nacimiento

                                </th>

                                <td>

                                    15 Marzo 2002

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Edad

                                </th>

                                <td>

                                    24 años

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Nacionalidad

                                </th>

                                <td>

                                    Mexicana

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Estado Civil

                                </th>

                                <td>

                                    Soltero

                                </td>

                            </tr>
                                                        <tr>

                                <th>

                                    Dirección

                                </th>

                                <td>

                                    Colonia Centro, Campeche, Campeche

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Código Postal

                                </th>

                                <td>

                                    24000

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Correo Electrónico

                                </th>

                                <td>

                                    juan@gmail.com

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

                        </tbody>

                    </table>

                </div>

            </div>





            <!-- EDUCACION -->
            <div class="col-lg-6">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-mortarboard-fill me-2"></i>

                        Educación

                    </h4>

                    <div class="education-item">

                        <h5 class="fw-bold">

                            Ingeniería en Programación y Web Master

                        </h5>

                        <p class="mb-1">

                            ITES René Descartes

                        </p>

                        <small class="text-muted">

                            2022 - 2025

                        </small>

                    </div>

                    <hr>

                    <div class="education-item">

                        <h5 class="fw-bold">

                            Bachillerato General

                        </h5>

                        <p class="mb-1">

                            COBACH Campeche

                        </p>

                        <small class="text-muted">

                            2019 - 2022

                        </small>

                    </div>

                </div>

            </div>





            <!-- EXPERIENCIA -->
            <div class="col-lg-12">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-briefcase-fill me-2"></i>

                        Experiencia Laboral

                    </h4>

                    <div class="experience-item">

                        <h5 class="fw-bold">

                            Desarrollador Web Full Stack

                        </h5>

                        <p class="text-primary">

                            Tech Solutions S.A.

                        </p>

                        <small class="text-muted">

                            Enero 2025 - Actualidad

                        </small>

                        <p class="mt-3">

                            Desarrollo de aplicaciones web utilizando PHP,
                            Bootstrap, JavaScript y MySQL.
                            Participación en el diseño de interfaces,
                            integración con bases de datos y mantenimiento
                            de sistemas empresariales.

                        </p>

                    </div>

                    <hr>

                    <div class="experience-item">

                        <h5 class="fw-bold">

                            Desarrollador Frontend

                        </h5>

                        <p class="text-primary">

                            Creative Software

                        </p>

                        <small class="text-muted">

                            Enero 2024 - Diciembre 2024

                        </small>

                        <p class="mt-3">

                            Desarrollo de interfaces web responsivas utilizando
                            HTML5, CSS3, Bootstrap y JavaScript.

                        </p>

                    </div>

                </div>

            </div>





            <!-- HABILIDADES -->
            <div class="col-lg-6">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-stars me-2"></i>

                        Habilidades

                    </h4>

                    <span class="badge bg-primary m-1 p-2">

                        HTML5

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        CSS3

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        Bootstrap

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        JavaScript

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        PHP

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        MySQL

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        Git

                    </span>

                    <span class="badge bg-primary m-1 p-2">

                        GitHub

                    </span>

                </div>

            </div>





            <!-- IDIOMAS -->
            <div class="col-lg-6">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-translate me-2"></i>

                        Idiomas

                    </h4>

                    <table class="table">

                        <thead>

                            <tr>

                                <th>

                                    Idioma

                                </th>

                                <th>

                                    Nivel

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>

                                    Español

                                </td>

                                <td>

                                    Nativo

                                </td>

                            </tr>

                            <tr>

                                <td>

                                    Inglés

                                </td>

                                <td>

                                    Intermedio

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

                        <!-- CV -->
            <div class="col-lg-8">

                <div class="candidate-card">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h4 class="fw-bold">

                            <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>

                            Currículum Vitae

                        </h4>

                        <button class="btn btn-danger">

                            <i class="bi bi-download me-2"></i>

                            Descargar CV

                        </button>

                    </div>

                    <div class="cv-preview">

                        <div class="text-center py-5">

                            <i class="bi bi-file-earmark-pdf-fill text-danger"
                               style="font-size:90px;"></i>

                            <h5 class="mt-4">

                                Juan_Perez_CV.pdf

                            </h5>

                            <p class="text-muted">

                                Vista previa del currículum del candidato.

                            </p>

                        </div>

                    </div>

                </div>

            </div>





            <!-- DOCUMENTOS -->
            <div class="col-lg-4">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-folder-fill me-2"></i>

                        Documentos

                    </h4>

                    <div class="list-group">

                        <a href="#"
                           class="list-group-item list-group-item-action">

                            <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>

                            Currículum.pdf

                        </a>

                        <a href="#"
                           class="list-group-item list-group-item-action">

                            <i class="bi bi-file-earmark-person-fill text-primary me-2"></i>

                            INE.pdf

                        </a>

                        <a href="#"
                           class="list-group-item list-group-item-action">

                            <i class="bi bi-file-earmark-medical-fill text-success me-2"></i>

                            Constancia.pdf

                        </a>

                        <a href="#"
                           class="list-group-item list-group-item-action">

                            <i class="bi bi-file-earmark-text-fill text-warning me-2"></i>

                            Carta de Presentación.pdf

                        </a>

                    </div>

                </div>

            </div>





            <!-- OBSERVACIONES -->
            <div class="col-lg-12">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-chat-left-text-fill me-2"></i>

                        Observaciones del Reclutador

                    </h4>

                    <textarea
                        class="form-control"
                        rows="6"
                        placeholder="Escriba aquí las observaciones del candidato..."></textarea>

                </div>

            </div>





            <!-- EVALUACION -->
            <div class="col-lg-12">

                <div class="candidate-card">

                    <h4 class="fw-bold mb-4">

                        <i class="bi bi-star-fill text-warning me-2"></i>

                        Evaluación General

                    </h4>

                    <div class="row text-center">

                        <div class="col-md-3">

                            <div class="p-3">

                                <h2 class="text-success">

                                    95%

                                </h2>

                                <p class="mb-0">

                                    Conocimientos

                                </p>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="p-3">

                                <h2 class="text-primary">

                                    90%

                                </h2>

                                <p class="mb-0">

                                    Comunicación

                                </p>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="p-3">

                                <h2 class="text-warning">

                                    88%

                                </h2>

                                <p class="mb-0">

                                    Trabajo en Equipo

                                </p>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="p-3">

                                <h2 class="text-danger">

                                    92%

                                </h2>

                                <p class="mb-0">

                                    Desempeño General

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
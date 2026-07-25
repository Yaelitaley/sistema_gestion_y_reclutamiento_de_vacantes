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
                    Mi Currículum
                </h2>
                <p class="text-muted">
                    Visualiza y administra la información de tu currículum.
                </p>
            </div>
        </div>

        <!-- INFORMACIÓN PERSONAL -->
        <div class="table-box mb-4">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center">
                    <!-- Corregido el nombre de la imagen para que coincida con perfil.php -->
                    <img src="../assets/img/candidato.png" class="rounded-circle img-fluid" style="width:140px; height:140px; object-fit:cover;">
                </div>
                <div class="col-lg-10">
                    <h3 class="fw-bold">
                        Gabriel Montero
                    </h3>
                    <h5 class="text-success">
                        Desarrollador Frontend
                    </h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-envelope-fill me-2 text-primary"></i>
                                gabriel@email.com
                            </p>
                            <p>
                                <i class="bi bi-telephone-fill me-2 text-success"></i>
                                +52 981 000 0000
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                Campeche, México
                            </p>
                            <p>
                                <i class="bi bi-calendar-fill me-2 text-warning"></i>
                                21 años
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERFIL PROFESIONAL (Texto sincronizado con perfil.php) -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-3">
                Perfil Profesional
            </h4>
            <p class="text-muted">
                Actualmente soy estudiante de Ingeniería en Programación y Web, 
                interesado en desarrollarme como desarrollador Frontend. Me apasiona 
                crear interfaces modernas, intuitivas y responsivas, utilizando 
                tecnologías como HTML, CSS, Bootstrap, JavaScript, PHP y MySQL.
            </p>
        </div>

        <!-- FORMACIÓN ACADÉMICA -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Formación Académica
            </h4>
            <div class="border-start border-4 border-success ps-3 mb-4">
                <h5 class="fw-bold">
                    Ingeniería en Programación y Web
                </h5>
                <p class="text-muted mb-1">
                    ITES René Descartes
                </p>
                <small class="text-secondary">
                    2024 - Actualidad
                </small>
            </div>
            <div class="border-start border-4 border-primary ps-3">
                <h5 class="fw-bold">
                    Bachillerato
                </h5>
                <p class="text-muted mb-1">
                    Educación Media Superior
                </p>
                <small class="text-secondary">
                    2021 - 2024
                </small>
            </div>
        </div>

        <!-- EXPERIENCIA LABORAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Experiencia Laboral
            </h4>
            <div class="border-start border-4 border-warning ps-3">
                <h5 class="fw-bold">
                    Desarrollador Frontend (Proyecto Académico)
                </h5>
                <p class="text-muted mb-1">
                    Sistema INERTIA
                </p>
                <small class="text-secondary">
                    2024 - Actualidad <!-- Corregido el año -->
                </small>
                <p class="mt-3">
                    Desarrollo de interfaces web modernas utilizando HTML,
                    CSS, Bootstrap, JavaScript y PHP, implementando diseño
                    responsive y experiencia de usuario.
                </p>
            </div>
        </div>

        <!-- HABILIDADES -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Habilidades Técnicas
            </h4>
            <span class="badge bg-primary me-2 mb-2">HTML5</span>
            <span class="badge bg-info me-2 mb-2">CSS3</span>
            <span class="badge bg-success me-2 mb-2">Bootstrap</span>
            <span class="badge bg-warning text-dark me-2 mb-2">JavaScript</span>
            <span class="badge bg-danger me-2 mb-2">PHP</span>
            <span class="badge bg-secondary me-2 mb-2">MySQL</span>
            <span class="badge bg-dark me-2 mb-2">Git</span>
            <span class="badge bg-success me-2 mb-2">GitHub</span>
        </div>

        <!-- IDIOMAS -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Idiomas
            </h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Idioma</th>
                        <th>Nivel</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Español</td>
                        <td>Nativo</td>
                    </tr>
                    <tr>
                        <td>Inglés</td>
                        <td>Intermedio</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- CERTIFICACIONES -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-4">
                Cursos y Certificaciones
            </h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="bi bi-patch-check-fill text-success me-2"></i>
                    Curso de Desarrollo Web con HTML y CSS
                </li>
                <li class="list-group-item">
                    <i class="bi bi-patch-check-fill text-success me-2"></i>
                    Introducción a JavaScript
                </li>
                <li class="list-group-item">
                    <i class="bi bi-patch-check-fill text-success me-2"></i>
                    Bootstrap 5 Responsive Design
                </li>
                <li class="list-group-item">
                    <i class="bi bi-patch-check-fill text-success me-2"></i>
                    Fundamentos de Git y GitHub
                </li>
            </ul>
        </div>

        <!-- APTITUDES -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Aptitudes Profesionales
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✔ Trabajo en equipo</li>
                        <li class="list-group-item">✔ Comunicación efectiva</li>
                        <li class="list-group-item">✔ Resolución de problemas</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✔ Aprendizaje continuo</li>
                        <li class="list-group-item">✔ Organización</li>
                        <li class="list-group-item">✔ Adaptabilidad</li>
                    </ul>
                </div>
            </div>
        </div>


        <!-- INFORMACIÓN ADICIONAL -->
        <div class="table-box mb-4">
            <h4 class="fw-bold mb-4">
                Información Adicional
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Disponibilidad:</strong>
                        Tiempo completo.
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Modalidad preferida:</strong>
                        Híbrido. <!-- Sincronizado con editar-perfil.php -->
                    </p>
                </div>
            </div>
        </div>

        <!-- OBJETIVO PROFESIONAL -->
        <div class="table-box mb-5">
            <h4 class="fw-bold mb-3">
                Objetivo Profesional
            </h4>
            <p class="text-muted">
                Obtener experiencia profesional en desarrollo web, participar en 
                proyectos innovadores, continuar aprendiendo nuevas tecnologías y 
                crecer profesionalmente dentro del área de desarrollo de software.
            </p>
        </div>

        <!-- BOTONES -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
            <div>
                <a href="editar_cv.php" class="btn btn-success me-2">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar CV
                </a>
                <button type="button" id="btnDescargarCV" class="btn btn-outline-primary">
                    <i class="bi bi-download me-2"></i>
                    Descargar PDF
                </button>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
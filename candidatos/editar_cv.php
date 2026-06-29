<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

    





        <!-- TÍTULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">

                    Editar Currículum

                </h2>

                <p class="text-muted">

                    Actualiza la información de tu perfil profesional.

                </p>

            </div>

        </div>





        <form id="formCV">

            <!-- DATOS PERSONALES -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Datos Personales

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nombre Completo

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Gabriel Montero">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Correo Electrónico

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            value="gabriel@email.com">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Teléfono

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="+52 981 000 0000">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Ciudad

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Campeche">

                    </div>

                </div>

            </div>





            <!-- PERFIL -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Perfil Profesional

                </h4>

                <textarea
                    class="form-control"
                    rows="6">Estudiante de Ingeniería en Programación y Web con conocimientos en HTML, CSS, Bootstrap, JavaScript, PHP y MySQL.</textarea>

            </div>





            <!-- FORMACIÓN -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Formación Académica

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Institución

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="ITES René Descartes">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Carrera

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Ingeniería en Programación y Web">

                    </div>

                </div>

            </div>

                        <!-- EXPERIENCIA LABORAL -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Experiencia Laboral

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Empresa

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Proyecto INERTIA">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Puesto

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Desarrollador Frontend">

                    </div>





                    <div class="col-12 mb-3">

                        <label class="form-label">

                            Descripción

                        </label>

                        <textarea
                            class="form-control"
                            rows="5">Desarrollo de interfaces web utilizando HTML5, CSS3, Bootstrap, JavaScript y PHP para un sistema de reclutamiento.</textarea>

                    </div>

                </div>

            </div>





            <!-- HABILIDADES -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Habilidades Técnicas

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            HTML

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Avanzado">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            CSS

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Avanzado">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Bootstrap

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Intermedio">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            JavaScript

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Intermedio">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            PHP

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Básico">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            MySQL

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Básico">

                    </div>

                </div>

            </div>





            <!-- IDIOMAS -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Idiomas

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Idioma

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Español">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nivel

                        </label>

                        <select class="form-select">

                            <option selected>

                                Nativo

                            </option>

                            <option>

                                Avanzado

                            </option>

                            <option>

                                Intermedio

                            </option>

                            <option>

                                Básico

                            </option>

                        </select>

                    </div>

                </div>

            </div>





            <!-- CERTIFICACIONES -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Certificaciones

                </h4>

                <textarea
                    class="form-control"
                    rows="4"
                    placeholder="Escribe tus cursos o certificaciones...">Curso de Desarrollo Web.
Curso de Bootstrap 5.
Curso de JavaScript.</textarea>

            </div>





            <!-- SUBIR CV -->
            <div class="table-box mb-5">

                <h4 class="fw-bold mb-4">

                    Archivo del Currículum

                </h4>

                <label class="form-label">

                    Selecciona tu CV en formato PDF

                </label>

                <input
                    type="file"
                    class="form-control"
                    accept=".pdf">

                <small class="text-muted">

                    Formato permitido: PDF (Máximo 5 MB).

                </small>

            </div>

                        <!-- OBJETIVO PROFESIONAL -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Objetivo Profesional

                </h4>

                <textarea
                    class="form-control"
                    rows="5"
                    placeholder="Describe tu objetivo profesional...">Desarrollarme profesionalmente como desarrollador web, participando en proyectos innovadores donde pueda aplicar mis conocimientos en tecnologías frontend y backend, así como adquirir nuevas habilidades.</textarea>

            </div>





            <!-- INFORMACIÓN ADICIONAL -->
            <div class="table-box mb-5">

                <h4 class="fw-bold mb-4">

                    Información Adicional

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Disponibilidad

                        </label>

                        <select class="form-select">

                            <option selected>

                                Tiempo Completo

                            </option>

                            <option>

                                Medio Tiempo

                            </option>

                            <option>

                                Freelance

                            </option>

                        </select>

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Modalidad Preferida

                        </label>

                        <select class="form-select">

                            <option selected>

                                Híbrido

                            </option>

                            <option>

                                Presencial

                            </option>

                            <option>

                                Remoto

                            </option>

                        </select>

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            LinkedIn

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            placeholder="https://linkedin.com/in/usuario">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Portafolio

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            placeholder="https://miportafolio.com">

                    </div>

                </div>

            </div>





            <!-- BOTONES -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">

                <a
                    href="cv.php"
                    class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-2"></i>

                    Regresar

                </a>





                <div>

                    <button
                        type="reset"
                        id="btnRestablecerCV"
                        class="btn btn-outline-danger me-2">

                        <i class="bi bi-arrow-counterclockwise me-2"></i>

                        Restablecer

                    </button>





                    <button
                        type="submit"
                        id="btnGuardarCV"
                        class="btn btn-success">

                        <i class="bi bi-floppy-fill me-2"></i>

                        Guardar Cambios

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<?php include "includes/footer.php"; ?>
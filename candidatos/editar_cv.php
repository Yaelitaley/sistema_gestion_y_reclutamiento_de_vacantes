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

        <!-- Formulario preparado para PHP -->
        <form id="formCV" action="guardar-cv.php" method="POST">

            <!-- DATOS PERSONALES -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Datos Personales
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre_completo" value="Gabriel Montero">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="gabriel@email.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="+52 981 000 0000">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="ciudad" value="Campeche, México">
                    </div>
                </div>
            </div>

            <!-- PERFIL PROFESIONAL (Texto sincronizado) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Perfil Profesional
                </h4>
                <textarea class="form-control" name="perfil_profesional" rows="6">Actualmente soy estudiante de Ingeniería en Programación y Web, interesado en desarrollarme como desarrollador Frontend. Me apasiona crear interfaces modernas, intuitivas y responsivas, utilizando tecnologías como HTML, CSS, Bootstrap, JavaScript, PHP y MySQL.</textarea>
            </div>

            <!-- FORMACIÓN ACADÉMICA (Con fechas agregadas) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Formación Académica
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Institución</label>
                        <input type="text" class="form-control" name="institucion" value="ITES René Descartes">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Carrera</label>
                        <input type="text" class="form-control" name="carrera" value="Ingeniería en Programación y Web">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="text" class="form-control" name="inicio_formacion" value="2024">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="text" class="form-control" name="fin_formacion" value="Actualidad">
                    </div>
                </div>
            </div>

            <!-- EXPERIENCIA LABORAL (Con fechas y textos sincronizados) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Experiencia Laboral
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Empresa / Proyecto</label>
                        <input type="text" class="form-control" name="empresa" value="Sistema INERTIA">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Puesto</label>
                        <input type="text" class="form-control" name="puesto" value="Desarrollador Frontend (Proyecto Académico)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="text" class="form-control" name="inicio_experiencia" value="2024">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="text" class="form-control" name="fin_experiencia" value="Actualidad">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion_experiencia" rows="5">Desarrollo de interfaces web modernas utilizando HTML, CSS, Bootstrap, JavaScript y PHP, implementando diseño responsive y experiencia de usuario.</textarea>
                    </div>
                </div>
            </div>

            <!-- HABILIDADES TÉCNICAS -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Habilidades Técnicas
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">HTML</label>
                        <input type="text" class="form-control" name="html_nivel" value="Avanzado (95%)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CSS</label>
                        <input type="text" class="form-control" name="css_nivel" value="Avanzado (90%)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bootstrap</label>
                        <input type="text" class="form-control" name="bootstrap_nivel" value="Intermedio (88%)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">JavaScript</label>
                        <input type="text" class="form-control" name="js_nivel" value="Intermedio (80%)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">PHP</label>
                        <input type="text" class="form-control" name="php_nivel" value="Básico (75%)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">MySQL</label>
                        <input type="text" class="form-control" name="mysql_nivel" value="Básico">
                    </div>
                </div>
            </div>

            <!-- APTITUDES (Nueva sección agregada para coincidir con cv.php) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Aptitudes Profesionales
                </h4>
                <label class="form-label">Escribe tus aptitudes (separadas por comas o una por línea)</label>
                <textarea class="form-control" name="aptitudes" rows="4">Trabajo en equipo
Comunicación efectiva
Resolución de problemas
Aprendizaje continuo
Organización
Adaptabilidad</textarea>
            </div>

            <!-- IDIOMAS (Segundo idioma agregado) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Idiomas
                </h4>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Idioma 1</label>
                        <input type="text" class="form-control" name="idioma1" value="Español">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nivel</label>
                        <select class="form-select" name="nivel1">
                            <option selected>Nativo</option>
                            <option>Avanzado</option>
                            <option>Intermedio</option>
                            <option>Básico</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Idioma 2</label>
                        <input type="text" class="form-control" name="idioma2" value="Inglés">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nivel</label>
                        <select class="form-select" name="nivel2">
                            <option>Nativo</option>
                            <option>Avanzado</option>
                            <option selected>Intermedio</option>
                            <option>Básico</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CERTIFICACIONES (Texto sincronizado) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Certificaciones
                </h4>
                <textarea class="form-control" name="certificaciones" rows="4">Curso de Desarrollo Web con HTML y CSS
Introducción a JavaScript
Bootstrap 5 Responsive Design
Fundamentos de Git y GitHub</textarea>
            </div>

            <!-- OBJETIVO PROFESIONAL (Texto sincronizado) -->
            <div class="table-box mb-4">
                <h4 class="fw-bold mb-4">
                    Objetivo Profesional
                </h4>
                <textarea class="form-control" name="objetivo_profesional" rows="5">Obtener experiencia profesional en desarrollo web, participar en proyectos innovadores, continuar aprendiendo nuevas tecnologías y crecer profesionalmente dentro del área de desarrollo de software.</textarea>
            </div>

            <!-- INFORMACIÓN ADICIONAL (Redes sincronizadas) -->
            <div class="table-box mb-5">
                <h4 class="fw-bold mb-4">
                    Información Adicional
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Disponibilidad</label>
                        <select class="form-select" name="disponibilidad">
                            <option selected>Tiempo Completo</option>
                            <option>Medio Tiempo</option>
                            <option>Freelance</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Modalidad Preferida</label>
                        <select class="form-select" name="modalidad">
                            <option selected>Híbrido</option>
                            <option>Presencial</option>
                            <option>Remoto</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin" value="linkedin.com/in/gabrielmontero">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Portafolio</label>
                        <input type="url" class="form-control" name="portafolio" value="www.gabrielmontero.dev">
                    </div>
                </div>
            </div>

            <!-- SUBIR CV -->
            <div class="table-box mb-5">
                <h4 class="fw-bold mb-4">
                    Archivo del Currículum
                </h4>
                <label class="form-label">
                    Selecciona tu CV en formato PDF
                </label>
                <input type="file" class="form-control" name="archivo_cv" accept=".pdf">
                <small class="text-muted">
                    Formato permitido: PDF (Máximo 5 MB).
                </small>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">
                <a href="cv.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </a>
                <div>
                    <button type="reset" id="btnRestablecerCV" class="btn btn-outline-danger me-2">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        Restablecer
                    </button>
                    <button type="submit" id="btnGuardarCV" class="btn btn-success">
                        <i class="bi bi-floppy-fill me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
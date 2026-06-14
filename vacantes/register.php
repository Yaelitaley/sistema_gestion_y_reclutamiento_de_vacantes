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
                    Registrar Vacante
                </h2>

                <p class="text-muted">
                    Completa la información para crear una nueva vacante
                </p>

            </div>

        </div>





        <!-- FORM -->
        <div class="register-box mx-auto">

            <form id="registerVacanteForm">

                <!-- TITULO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Título de la Vacante
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-briefcase-fill"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="titulo"
                            placeholder="Ejemplo: Desarrollador Backend">

                    </div>

                </div>





                <!-- EMPRESA -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Empresa
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-building"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="empresa"
                            placeholder="Nombre de la empresa">

                    </div>

                </div>





                <!-- UBICACION -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Ubicación
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="ubicacion"
                            placeholder="Ciudad o Estado">

                    </div>

                </div>





                <!-- SALARIO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Salario
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-cash-stack"></i>
                        </span>

                        <input
                            type="number"
                            class="form-control"
                            id="salario"
                            placeholder="Ejemplo: 15000">

                    </div>

                </div>





                <!-- CATEGORIA -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Categoría
                    </label>

                    <select
                        class="form-select"
                        id="categoria">

                        <option selected disabled>
                            Selecciona una categoría
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
                            Administración
                        </option>

                    </select>

                </div>





                <!-- TIPO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Tipo de empleo
                    </label>

                    <select
                        class="form-select"
                        id="tipo">

                        <option selected disabled>
                            Selecciona un tipo
                        </option>

                        <option>
                            Tiempo completo
                        </option>

                        <option>
                            Medio tiempo
                        </option>

                        <option>
                            Freelance
                        </option>

                        <option>
                            Remoto
                        </option>

                    </select>

                </div>





                <!-- ESTADO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Estado
                    </label>

                    <select
                        class="form-select"
                        id="estado">

                        <option selected>
                            Activo
                        </option>

                        <option>
                            Inactivo
                        </option>

                    </select>

                </div>





                <!-- DESCRIPCION -->
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Descripción
                    </label>

                    <textarea
                        class="form-control"
                        rows="5"
                        id="descripcion"
                        placeholder="Describe las responsabilidades y requisitos de la vacante"></textarea>

                </div>





                <!-- MENSAJE -->
                <div
                    id="mensajeVacante"
                    class="alert d-none mb-3">

                </div>





                <!-- BOTONES -->
                <div class="d-flex gap-3">

                    <!-- REGISTRAR -->
                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        Registrar Vacante

                    </button>





                    <!-- CANCELAR -->
                    <a href="vacantes.php"
                       class="btn btn-secondary w-100">

                        <i class="bi bi-x-circle-fill me-2"></i>

                        Cancelar

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
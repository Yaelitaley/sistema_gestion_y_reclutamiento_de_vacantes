
<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "../admin/includes/sidebar.php"; ?>



    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">
                    Editar Vacante
                </h2>

                <p class="text-muted">
                    Modifica la información de la vacante
                </p>

            </div>

        </div>





        <!-- FORM -->
        <div class="register-box mx-auto">

            <form id="editVacanteForm">

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
                            value="Desarrollador Backend">

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
                            value="Tech Solutions">

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
                            value="Campeche, México">

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
                            value="15000">

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

                        <option selected>
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
                        id="descripcion">Vacante para desarrollador backend con experiencia en PHP y MySQL.</textarea>

                </div>





                <!-- MENSAJE -->
                <div
                    id="mensajeVacante"
                    class="alert d-none mb-3">

                </div>





                <!-- BOTONES -->
                <div class="d-flex gap-3">

                    <!-- GUARDAR -->
                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        Guardar Cambios

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
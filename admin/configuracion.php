
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
                    Configuración
                </h2>

                <p class="text-muted">
                    Administra las configuraciones generales del sistema
                </p>

            </div>

        </div>





        <div class="row g-4">

            <!-- CONFIGURACION GENERAL -->
            <div class="col-md-6">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Información del sistema
                    </h5>

                    <form>

                        <!-- NOMBRE -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Nombre del sistema
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Portal de Empleos">

                        </div>





                        <!-- CORREO -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Correo de soporte
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                value="soporte@portal.com">

                        </div>





                        <!-- TELEFONO -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="9811234567">

                        </div>





                        <!-- BOTON -->
                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="bi bi-save-fill me-2"></i>

                            Guardar Cambios

                        </button>

                    </form>

                </div>

            </div>





            <!-- SEGURIDAD -->
            <div class="col-md-6">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Seguridad
                    </h5>

                    <form>

                        <!-- PASSWORD -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                placeholder="Nueva contraseña">

                        </div>





                        <!-- CONFIRM -->
                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                placeholder="Confirmar contraseña">

                        </div>





                        <!-- SESSION -->
                        <div class="form-check form-switch mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                checked>

                            <label class="form-check-label">

                                Mantener sesión iniciada

                            </label>

                        </div>





                        <!-- BOTON -->
                        <button
                            type="submit"
                            class="btn btn-dark w-100">

                            <i class="bi bi-shield-lock-fill me-2"></i>

                            Actualizar Seguridad

                        </button>

                    </form>

                </div>

            </div>





            <!-- APARIENCIA -->
            <div class="col-md-12">

                <div class="table-box">

                    <h5 class="fw-bold mb-4">
                        Preferencias visuales
                    </h5>

                    <div class="row">

                        <!-- COLOR -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Color principal
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Azul
                                </option>

                                <option>
                                    Morado
                                </option>

                                <option>
                                    Verde
                                </option>

                                <option>
                                    Rojo
                                </option>

                            </select>

                        </div>





                        <!-- MODO -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Tema
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Claro
                                </option>

                                <option>
                                    Oscuro
                                </option>

                            </select>

                        </div>





                        <!-- IDIOMA -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Idioma
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Español
                                </option>

                                <option>
                                    Inglés
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
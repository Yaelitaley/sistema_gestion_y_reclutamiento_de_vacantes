<?php include "includes/header.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- CONTENIDO -->
    <div class="content w-100 p-4">

        <?php include "includes/topbar.php"; ?>

        <!-- TITULO -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">

                    Editar Perfil

                </h2>

                <p class="text-muted">

                    Actualiza la información de tu perfil de reclutador.

                </p>

            </div>

        </div>





        <div class="table-box">

            <form
                action=""
                method="POST"
                enctype="multipart/form-data">

                <div class="row">

                    <!-- FOTO -->
                    <div class="col-md-12 text-center mb-5">

                        <img
                            src="../assets/img/reclutador-avatar.png"
                            class="rounded-circle shadow mb-3"
                            width="170"
                            height="170"
                            alt="Foto">

                        <br>

                        <label class="form-label fw-bold">

                            Cambiar fotografía

                        </label>

                        <input
                            type="file"
                            class="form-control">

                    </div>





                    <!-- NOMBRE -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Nombre

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="María">

                    </div>





                    <!-- APELLIDOS -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Apellidos

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="González Pérez">

                    </div>





                    <!-- CORREO -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Correo Electrónico

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            value="maria@empresa.com">

                    </div>





                    <!-- TELEFONO -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Teléfono

                        </label>

                        <input
                            type="tel"
                            class="form-control"
                            value="9811234567">

                    </div>





                    <!-- EMPRESA -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Empresa

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Tech Solutions">

                    </div>





                    <!-- CARGO -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Cargo

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Reclutadora Senior">

                    </div>





                    <!-- CIUDAD -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Ciudad

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Campeche">

                    </div>





                    <!-- ESTADO -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Estado

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Campeche">

                    </div>





                    <!-- CONTRASEÑA -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Nueva Contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            placeholder="********">

                    </div>





                    <!-- CONFIRMAR -->
                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">

                            Confirmar Contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            placeholder="********">

                    </div>





                    <!-- BIOGRAFIA -->
                    <div class="col-md-12 mb-4">

                        <label class="form-label fw-bold">

                            Descripción

                        </label>

                        <textarea
                            class="form-control"
                            rows="5">Especialista en reclutamiento de talento tecnológico con experiencia en procesos de selección y entrevistas.</textarea>

                    </div>

                </div>





                <hr>





                <div class="d-flex justify-content-end gap-3">

                    <a href="javascript:history.back();" class="cancel-link">

        Regresar

    </a>

                    <button
                        type="reset"
                        class="btn btn-warning">

                        Limpiar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-floppy-fill me-2"></i>

                        Guardar Cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
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

                    Editar Perfil

                </h2>

                <p class="text-muted">

                    Actualiza la información de tu cuenta y tus datos personales.

                </p>

            </div>

        </div>





        <form id="formPerfil">

            <!-- FOTO -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Foto de Perfil

                </h4>

                <div class="row align-items-center">

                    <div class="col-lg-3 text-center">

                        <img
                            src="../assets/img/candidato.png"
                            class="rounded-circle img-fluid mb-3"
                            style="width:160px;height:160px;object-fit:cover;">

                    </div>





                    <div class="col-lg-9">

                        <label class="form-label">

                            Selecciona una nueva fotografía

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            accept="image/*">

                        <small class="text-muted">

                            Formatos permitidos: JPG, PNG o JPEG.

                        </small>

                    </div>

                </div>

            </div>





            <!-- DATOS PERSONALES -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Datos Personales

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nombre

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Gabriel">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Apellidos

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Montero">

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





                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Fecha de Nacimiento

                        </label>

                        <input
                            type="date"
                            class="form-control">

                    </div>





                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Género

                        </label>

                        <select class="form-select">

                            <option selected>

                                Masculino

                            </option>

                            <option>

                                Femenino

                            </option>

                            <option>

                                Otro

                            </option>

                        </select>

                    </div>





                    <div class="col-md-4 mb-3">

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
                        <!-- INFORMACIÓN PROFESIONAL -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Información Profesional

                </h4>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Puesto Deseado

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Desarrollador Frontend">

                    </div>





                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Salario Esperado

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="$20,000 MXN">

                    </div>





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

                                Remoto

                            </option>

                            <option>

                                Presencial

                            </option>

                        </select>

                    </div>

                </div>

            </div>





            <!-- REDES PROFESIONALES -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Redes Profesionales

                </h4>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            LinkedIn

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            placeholder="https://linkedin.com/in/usuario">

                    </div>





                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            GitHub

                        </label>

                        <input
                            type="url"
                            class="form-control"
                            placeholder="https://github.com/usuario">

                    </div>





                    <div class="col-md-4 mb-3">

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





            <!-- CAMBIAR CONTRASEÑA -->
            <div class="table-box mb-4">

                <h4 class="fw-bold mb-4">

                    Cambiar Contraseña

                </h4>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Contraseña Actual

                        </label>

                        <input
                            type="password"
                            class="form-control">

                    </div>





                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Nueva Contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control">

                    </div>





                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Confirmar Contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control">

                    </div>

                </div>

            </div>





            <!-- PREFERENCIAS -->
            <div class="table-box mb-5">

                <h4 class="fw-bold mb-4">

                    Preferencias de la Cuenta

                </h4>

                <div class="form-check mb-3">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        checked>

                    <label class="form-check-label">

                        Recibir ofertas de empleo por correo.

                    </label>

                </div>





                <div class="form-check mb-3">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        checked>

                    <label class="form-check-label">

                        Recibir notificaciones del sistema.

                    </label>

                </div>





                <div class="form-check">

                    <input
                        class="form-check-input"
                        type="checkbox">

                    <label class="form-check-label">

                        Mostrar mi perfil públicamente para reclutadores.

                    </label>

                </div>

            </div>
                        <!-- RESUMEN DE LA CUENTA -->
            <div class="table-box mb-5">

                <h4 class="fw-bold mb-4">

                    Estado de la Cuenta

                </h4>

                <div class="alert alert-info d-flex align-items-center">

                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>

                    <div>

                        Tu perfil está completo en un <strong>85%</strong>.
                        Mantén tu información actualizada para aumentar tus
                        oportunidades de ser contactado por los reclutadores.

                    </div>

                </div>

            </div>





            <!-- BOTONES -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-5">

                <!-- REGRESAR -->
                <a
                    href="perfil.php"
                    class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-2"></i>

                    Regresar

                </a>





                <div>

                    <!-- RESTABLECER -->
                    <button
                        type="reset"
                        id="btnRestablecerPerfil"
                        class="btn btn-outline-danger me-2">

                        <i class="bi bi-arrow-counterclockwise me-2"></i>

                        Restablecer

                    </button>





                    <!-- GUARDAR -->
                    <button
                        type="submit"
                        id="btnGuardarPerfil"
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
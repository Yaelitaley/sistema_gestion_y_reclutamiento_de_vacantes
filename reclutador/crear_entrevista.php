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

                    Programar Entrevista

                </h2>

                <p class="text-muted">

                    Complete la información para agendar una entrevista con un candidato.

                </p>

            </div>

            <a href="entrevistas.php"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left me-2"></i>

                Regresar

            </a>

        </div>





        <!-- FORMULARIO -->
        <div class="table-box">

            <form
                action=""
                method="POST"
                id="formEntrevista">

                <div class="row">

                    <!-- CANDIDATO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="candidato"
                            class="form-label fw-bold">

                            Candidato

                        </label>

                        <select
                            id="candidato"
                            name="candidato"
                            class="form-select"
                            required>

                            <option selected disabled>

                                Seleccione un candidato

                            </option>

                            <option>

                                Juan Pérez Hernández

                            </option>

                            <option>

                                Ana López

                            </option>

                            <option>

                                Carlos Ruiz

                            </option>

                        </select>

                    </div>





                    <!-- VACANTE -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="vacante"
                            class="form-label fw-bold">

                            Vacante

                        </label>

                        <select
                            id="vacante"
                            name="vacante"
                            class="form-select"
                            required>

                            <option selected disabled>

                                Seleccione una vacante

                            </option>

                            <option>

                                Desarrollador Backend

                            </option>

                            <option>

                                Diseñador UI / UX

                            </option>

                            <option>

                                Marketing Digital

                            </option>

                        </select>

                    </div>





                    <!-- ENTREVISTADOR -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="entrevistador"
                            class="form-label fw-bold">

                            Entrevistador

                        </label>

                        <input
                            type="text"
                            id="entrevistador"
                            name="entrevistador"
                            class="form-control"
                            placeholder="Nombre del entrevistador"
                            required>

                    </div>





                    <!-- MODALIDAD -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="modalidad"
                            class="form-label fw-bold">

                            Modalidad

                        </label>

                        <select
                            id="modalidad"
                            name="modalidad"
                            class="form-select">

                            <option>

                                Presencial

                            </option>

                            <option>

                                Virtual

                            </option>

                            <option>

                                Telefónica

                            </option>

                        </select>

                    </div>





                    <!-- FECHA -->
                    <div class="col-md-4 mb-4">

                        <label
                            for="fecha"
                            class="form-label fw-bold">

                            Fecha

                        </label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            class="form-control"
                            required>

                    </div>





                    <!-- HORA -->
                    <div class="col-md-4 mb-4">

                        <label
                            for="hora"
                            class="form-label fw-bold">

                            Hora

                        </label>

                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            class="form-control"
                            required>

                    </div>





                    <!-- DURACION -->
                    <div class="col-md-4 mb-4">

                        <label
                            for="duracion"
                            class="form-label fw-bold">

                            Duración

                        </label>

                        <select
                            id="duracion"
                            name="duracion"
                            class="form-select">

                            <option>

                                30 minutos

                            </option>

                            <option>

                                45 minutos

                            </option>

                            <option>

                                1 hora

                            </option>

                            <option>

                                1 hora 30 minutos

                            </option>

                        </select>

                    </div>





                    <!-- LUGAR -->
                    <div class="col-md-12 mb-4">

                        <label
                            for="lugar"
                            class="form-label fw-bold">

                            Lugar o Enlace

                        </label>

                        <input
                            type="text"
                            id="lugar"
                            name="lugar"
                            class="form-control"
                            placeholder="Sala de juntas, Google Meet, Zoom..."
                            required>

                    </div>





                    <!-- CORREO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="correo"
                            class="form-label fw-bold">

                            Correo del candidato

                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            class="form-control"
                            placeholder="correo@ejemplo.com">

                    </div>





                    <!-- TELEFONO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="telefono"
                            class="form-label fw-bold">

                            Teléfono del candidato

                        </label>

                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            class="form-control"
                            placeholder="9811234567">

                    </div>

                                        <!-- OBSERVACIONES -->
                    <div class="col-md-12 mb-4">

                        <label
                            for="observaciones"
                            class="form-label fw-bold">

                            Observaciones

                        </label>

                        <textarea
                            id="observaciones"
                            name="observaciones"
                            class="form-control"
                            rows="5"
                            placeholder="Escriba observaciones importantes para la entrevista..."></textarea>

                    </div>





                    <!-- ESTADO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="estado"
                            class="form-label fw-bold">

                            Estado de la Entrevista

                        </label>

                        <select
                            id="estado"
                            name="estado"
                            class="form-select">

                            <option selected>

                                Pendiente

                            </option>

                            <option>

                                Completada

                            </option>

                            <option>

                                Denegada

                            </option>

                        </select>

                    </div>





                    <!-- TIPO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="tipo"
                            class="form-label fw-bold">

                            Tipo de Entrevista

                        </label>

                        <select
                            id="tipo"
                            name="tipo"
                            class="form-select">

                            <option>

                                Primera Entrevista

                            </option>

                            <option>

                                Segunda Entrevista

                            </option>

                            <option>

                                Entrevista Técnica

                            </option>

                            <option>

                                Entrevista Final

                            </option>

                        </select>

                    </div>





                    <!-- RECORDATORIO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="recordatorio"
                            class="form-label fw-bold">

                            Enviar Recordatorio

                        </label>

                        <select
                            id="recordatorio"
                            name="recordatorio"
                            class="form-select">

                            <option>

                                Sí

                            </option>

                            <option>

                                No

                            </option>

                        </select>

                    </div>





                    <!-- CODIGO -->
                    <div class="col-md-6 mb-4">

                        <label
                            for="codigo"
                            class="form-label fw-bold">

                            Código de Entrevista

                        </label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            class="form-control"
                            placeholder="ENT-2026-001">

                    </div>

                </div>

                <hr>

                <!-- BOTONES -->
                <div class="d-flex justify-content-end gap-3">

                    <a
                        href="entrevistas.php"
                        class="btn btn-secondary">

                        <i class="bi bi-arrow-left me-2"></i>

                        Cancelar

                    </a>

                    <button
                        type="reset"
                        class="btn btn-warning">

                        <i class="bi bi-arrow-clockwise me-2"></i>

                        Limpiar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-calendar-plus-fill me-2"></i>

                        Programar Entrevista

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>
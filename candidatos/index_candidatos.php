
<?php include "includes/header.php"; ?>

<div class="d-flex">



    <!-- CONTENT -->
    <div class="content w-100 p-4">

        <!-- TOP -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold">
                    Candidatos
                </h3>

                <p class="text-muted">
                    Administra los Candidatos registrados en el sistema.
                </p>

            </div>





            <!-- BOTON -->
                <a href="../candidatos/register.php"
               class="btn btn-primary">

                <i class="bi bi-plus-circle-fill me-2"></i>

                Agregar Candidato

            </a>

        </div>





        <!-- TABLA -->
        <div class="table-box">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>





                <tbody>

                    <!-- FILA -->
                    <tr>

                        <td>1</td>

                        <td>Juan Pérez</td>

                        <td>juan@gmail.com</td>

                        <td>Tech Solutions</td>

                        <td>

                            <span class="badge bg-success">
                                Activo
                            </span>

                        </td>

                        <td>

                            <!-- EDITAR -->
                            <a href="../candidatos/edit_candidatos.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>

                        </td>

                    </tr>





                    <!-- FILA -->
                    <tr>

                        <td>2</td>

                        <td>Ana López</td>

                        <td>ana@gmail.com</td>

                        <td>Global Corp</td>

                        <td>

                            <span class="badge bg-warning text-dark">
                                Pendiente
                            </span>

                        </td>

                        <td>

                            <a href="../candidatos/edit_candidatos.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>

                        </td>

                    </tr>





                    <!-- FILA -->
                    <tr>

                        <td>3</td>

                        <td>Carlos Ruiz</td>

                        <td>carlos@gmail.com</td>

                        <td>Dev Company</td>

                        <td>

                            <span class="badge bg-danger">
                                Bloqueado
                            </span>

                        </td>

                        <td>

                            <a href="../candidatos/edit_candidatos.php"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <!-- ELIMINAR -->
                            <button
                                class="btn btn-danger btn-sm btnEliminar">

                                    <i class="bi bi-trash-fill"></i>

                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>
    

</div>


<!-- CANCELAR -->
                <div class="text-center mt-3">

                   <!-- CANCELAR -->
                    <a href="javascript:history.back()"
                    class="cancel-link">

                        Regresar

                </a>

                </div>

<?php include "includes/footer.php"; ?>

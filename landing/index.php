<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>

<main>

    <!-- HERO -->
    <section class="imagen1 py-5">

        <div class="container">

            <div class="row align-items-center">

                <!-- TEXTO -->
                <div class="col-md-6">

                    <h1 class="fw-bold">
                        CONECTAMOS TALENTO
                    </h1>

                    <h1 class="text-primary fw-bold">
                        CON OPORTUNIDADES
                    </h1>

                    <p class="mt-4">
                        Nuestra plataforma facilita el reclutamiento para
                        empresas y postulantes, de forma rápida,
                        segura y eficiente.
                    </p>

                    <div class="mt-4">

                        <a href="../reclutador/solicitud.php" class="btn btn-primary me-2">
                            Soy empresa
                        </a>

                        <a href="../candidatos/login.php" class="btn btn-outline-primary">
                            Soy candidato
                        </a>

                    </div>

                </div>

                <!-- IMAGEN -->
                <div class="col-md-6 text-center">

                    <img
                        src="../assets/img/imagen1.png"
                        class="img-fluid"
                        alt="Imagen principal">

                </div>

            </div>

        </div>

    </section>

    <!-- CARDS -->
    <section class="py-5">

        <div class="container">

            <div class="row g-4">

                <!-- CARD CANDIDATOS -->
                <div class="col-md-6">

                    <div class="card h-100 border-0 shadow-sm p-4">

                        <div class="row align-items-center">

                            <!-- TEXTO -->
                            <div class="col-8">
                                

                                <h5 class="text-purple">
                                    <i class="fa-regular fa-user"></i>
                                    Para reclutadores
                                </h5>

                                <h4 class="fw-bold">
                                    Encuentra el talento ideal
                                </h4>

                                <p>
                                    Publica vacantes, gestiona candidatos
                                    y optimiza tu proceso de selección.
                                </p>

                                <a href="../reclutador/login.php"
                                   class="text-decoration-none fw-bold">

                                    Iniciar sesión →

                                </a>

                            </div>

                            <!-- IMAGEN -->
                            <div class="col-4 text-end">

                                <img
                                    src="../assets/img/imagenreclutador.png"
                                    class="img-fluid"
                                    alt="Reclutador">

                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD CANDIDATOS -->
                <div class="col-md-6">

                    <div class="card h-100 border-0 shadow-sm p-4">

                        <div class="row align-items-center">

                            <!-- TEXTO -->
                            <div class="col-8">

                                <h5 class="text-success">
                                <i class="fa-regular fa-user"></i>

                                    Para candidatos
                                </h5>

                                <h4 class="fw-bold">
                                    Encuentra tu próxima oportunidad
                                </h4>

                                <p>
                                    Explora vacantes y postúlate
                                    fácilmente desde un solo lugar.
                                </p>

                                <a href="../candidatos/login.php"
                                   class="text-decoration-none fw-bold">

                                    Iniciar sesión →

                                </a>

                            </div>

                            <!-- IMAGEN -->
                            <div class="col-4 text-end">

                                <img
                                    src="../assets/img/imagencandidato.png"
                                    class="img-fluid"
                                    alt="Candidato">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?php include "includes/footer.php"; ?>
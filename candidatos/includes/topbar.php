<div class="top-bar mb-4">

    <!-- IZQUIERDA -->
    <div class="d-flex align-items-center">

        <!-- BOTÓN MENÚ -->
        <button
            id="menuToggle"
            class="btn btn-candidato me-3">

            <i class="bi bi-list fs-4"></i>

        </button>

        <!-- TÍTULO -->
        <div>

            <h3 class="texto fw-bold mb-0">

                ¡Bienvenido, Candidato!

            </h3>

            <p class="texto mb-0">

                Encuentra el empleo ideal para impulsar tu carrera profesional.

            </p>

        </div>

    </div>

    <!-- DERECHA -->
    <div class="candidate-profile">

        <!-- NOTIFICACIONES -->
        <button
            class="btn position-relative me-4">

            <i class="bi bi-bell-fill fs-5 text-warning"></i>

            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                3

            </span>

        </button>

        <!-- PERFIL -->
        <div class="dropdown">

            <button
                class="btn dropdown-toggle d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false">

                <img
                    src="../assets/img/imagencandidato.png"
                    class="rounded-circle me-3"
                    width="42"
                    height="42"
                    alt="Candidato">

                <div class="text-start">

                    <div class="texto fw-semibold">

                        Gabriel Montero

                    </div>

                    <small class="texto">

                        Candidato

                    </small>

                </div>

            </button>

            <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">

                <li>

                    <a
                        class="dropdown-item"
                        href="perfil.php">

                        <i class="bi bi-person me-2"></i>

                        Mi Perfil

                    </a>

                </li>

                <li>

                    <a
                        class="dropdown-item"
                        href="cv.php">

                        <i class="bi bi-file-person-fill me-2"></i>

                        Mi CV

                    </a>

                </li>

                <li>

                    <a
                        class="dropdown-item"
                        href="postulaciones.php">

                        <i class="bi bi-send-check-fill me-2"></i>

                        Mis Postulaciones

                    </a>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <a
                        class="dropdown-item"
                        href="../candidatos/logout.php"
                        id="btnLogout">

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Cerrar sesión

                    </a>

                </li>

            </ul>

        </div>

    </div>

</div>
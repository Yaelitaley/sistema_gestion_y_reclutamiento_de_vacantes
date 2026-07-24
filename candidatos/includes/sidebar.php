<?php
$pagina = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <!-- LOGO -->
    <div>
        <div class="logo">
            <i class="bi bi-briefcase-fill"></i>
            <span>
                Portal de Empleos
            </span>
        </div>
        <!-- MENÚ -->
        <ul class="menu">
            <li>
                <a href="dashboard.php"
                   class="<?= $pagina == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door-fill"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="explorar-empleos.php"
                   class="<?= $pagina == 'explorar-empleos.php' ? 'active' : '' ?>">
                    <i class="bi bi-briefcase-fill"></i>
                    Explorar Empleos
                </a>
            </li>
            <li>
                <a href="postulaciones.php"
                   class="<?= $pagina == 'postulaciones.php' ? 'active' : '' ?>">
                    <i class="bi bi-send-check-fill"></i>
                    Mis Postulaciones
                </a>
            </li>
            <li>
                <a href="cv.php"
                   class="<?= $pagina == 'cv.php' ? 'active' : '' ?>">
                    <i class="bi bi-file-person-fill"></i>
                    Mi CV
                </a>
            </li>
            <li>
                <a href="perfil.php"
                   class="<?= $pagina == 'perfil.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i>
                    Mi Perfil
                </a>
            </li>
            <li>
                <a href="configuracion.php"
                   class="<?= $pagina == 'configuracion.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill"></i>
                    Configuración
                </a>
            </li>
        </ul>
    </div>
    <!-- CERRAR SESIÓN -->
    <div class="logout">
        <a href="../candidatos/logout.php"
           id="btnLogout">
            <i class="bi bi-box-arrow-right me-2"></i>
            Cerrar sesión
        </a>
    </div>
</div>
<?php
$pagina = basename($_SERVER['PHP_SELF']);

// Cada sección cubre su página principal y sus páginas de detalle/creación,
// para que el resaltado no se mueva a "Inicio" al entrar a un detalle.
$secciones = [
    'dashboard'         => ['dashboard.php'],
    'vacantes'          => ['vacantes.php', 'ver_vacante.php'],
    'candidatos'        => ['candidatos.php', 'ver_candidatos.php'],
    'entrevistas'       => ['entrevistas.php', 'crear_entrevista.php', 'ver_entrevista.php'],
    'perfil_reclutador' => ['perfil_reclutador.php', 'editar_perfil.php'],
    'configuracion'     => ['configuracion.php'],
];

function seccion_activa($clave, $pagina, $secciones)
{
    return in_array($pagina, $secciones[$clave], true) ? 'active' : '';
}
?>
<div class="sidebar">

    <!-- LOGO -->
    <div>

        <div class="logo">

            <i class="bi bi-briefcase-fill"></i>

            Portal de Empleos

        </div>

        <!-- MENU -->
        <ul class="menu">

            <li>

                <a href="dashboard.php" class="<?= seccion_activa('dashboard', $pagina, $secciones) ?>">

                    <i class="bi bi-house-fill"></i>

                    Inicio

                </a>

            </li>

            <li>

                <a href="vacantes.php" class="<?= seccion_activa('vacantes', $pagina, $secciones) ?>">

                    <i class="bi bi-building-fill-check"></i>

                    Vacantes

                </a>

            </li>

            <li>

                <a href="../reclutador/candidatos.php" class="<?= seccion_activa('candidatos', $pagina, $secciones) ?>">

                    <i class="bi bi-people-fill"></i>

                    Candidatos

                </a>

            </li>

            <li>

                <a href="entrevistas.php" class="<?= seccion_activa('entrevistas', $pagina, $secciones) ?>">

                    <i class="bi bi-calendar-event-fill"></i>

                    Entrevistas

                </a>

            </li>

            <li>

                <a href="perfil_reclutador.php" class="<?= seccion_activa('perfil_reclutador', $pagina, $secciones) ?>">

                    <i class="bi bi-person-circle"></i>

                    Mi Perfil

                </a>

            </li>

            <li>

                <a href="configuracion.php" class="<?= seccion_activa('configuracion', $pagina, $secciones) ?>">

                    <i class="bi bi-gear-fill"></i>

                    Configuración

                </a>

            </li>

        </ul>

    </div>

    <!-- LOGOUT -->
    <div class="logout">

        <a href="../reclutador/logout.php" class="btn-logout-trigger" data-bs-toggle="modal" data-bs-target="#modalConfirmarLogout">

    <i class="bi bi-box-arrow-left"></i>

    Cerrar sesión

</a>

    </div>

</div>
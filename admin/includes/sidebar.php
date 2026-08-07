<?php
$pagina = basename($_SERVER['PHP_SELF']);

$secciones = [
    'dashboard'       => ['dashboard.php'],
    'reclutadores'    => ['index_reclutador.php', 'edit_reclutador.php'],
    'candidatos'      => ['index_candidatos.php'],
    'administradores' => ['index_administrador.php', 'edit_administrador.php'],
    'vacantes'        => ['vacantes.php'],
    'usuarios'        => ['usuarios.php'],
    'reportes'        => ['reportes.php'],
    'configuracion'   => ['configuracion.php'],
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

            <span>
                Portal de Empleos
            </span>

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
                <a href="index_reclutador.php" class="<?= seccion_activa('reclutadores', $pagina, $secciones) ?>">
                    <i class="bi bi-person-badge-fill"></i>
                    Reclutadores
                </a>
            </li>

            <li>
                <a href="index_candidatos.php" class="<?= seccion_activa('candidatos', $pagina, $secciones) ?>">
                    <i class="bi bi-people-fill"></i>
                    Candidatos
                </a>
            </li>

            <li>
                <a href="index_administrador.php" class="<?= seccion_activa('administradores', $pagina, $secciones) ?>">
                    <i class="bi bi-people-fill"></i>
                    Administradores
                </a>
            </li>

            <li>
                <a href="vacantes.php" class="<?= seccion_activa('vacantes', $pagina, $secciones) ?>">
                    <i class="bi bi-briefcase-fill"></i>
                    Vacantes
                </a>
            </li>

            <li>
                <a href="usuarios.php" class="<?= seccion_activa('usuarios', $pagina, $secciones) ?>">
                    <i class="bi bi-people-fill"></i>
                    Usuarios
                </a>
            </li>

            <li>
                <a href="reportes.php" class="<?= seccion_activa('reportes', $pagina, $secciones) ?>">
                    <i class="bi bi-bar-chart-fill"></i>
                    Reportes
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
        <a href="../admin/logout.php" id="btnLogout">
            <i class="bi bi-box-arrow-left"></i>
            Cerrar sesión
        </a>
    </div>

</div>

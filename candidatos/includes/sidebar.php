<?php
$pagina = basename($_SERVER['PHP_SELF']);

// Cada sección cubre su página principal y sus páginas de detalle/edición,
// para que el resaltado no se mueva a "Inicio" al entrar a un detalle.
$secciones = [
    'dashboard'   => ['dashboard.php'],
    'empleos'     => ['explorar-empleos.php', 'ver-empleo.php'],
    'postulaciones' => ['postulaciones.php'],
    'cv'          => ['cv.php', 'editar_cv.php'],
    'perfil'      => ['perfil.php', 'editar_perfil.php', 'edit_candidatos.php'],
    'configuracion' => ['configuracion.php'],
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
        <!-- MENÚ -->
        <ul class="menu">
            <li>
                <a href="dashboard.php"
                   class="<?= seccion_activa('dashboard', $pagina, $secciones) ?>">
                    <i class="bi bi-house-door-fill"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="explorar-empleos.php"
                   class="<?= seccion_activa('empleos', $pagina, $secciones) ?>">
                    <i class="bi bi-briefcase-fill"></i>
                    Explorar Empleos
                </a>
            </li>
            <li>
                <a href="postulaciones.php"
                   class="<?= seccion_activa('postulaciones', $pagina, $secciones) ?>">
                    <i class="bi bi-send-check-fill"></i>
                    Mis Postulaciones
                </a>
            </li>
            <li>
                <a href="cv.php"
                   class="<?= seccion_activa('cv', $pagina, $secciones) ?>">
                    <i class="bi bi-file-person-fill"></i>
                    Mi CV
                </a>
            </li>
            <li>
                <a href="perfil.php"
                   class="<?= seccion_activa('perfil', $pagina, $secciones) ?>">
                    <i class="bi bi-person-circle"></i>
                    Mi Perfil
                </a>
            </li>
            <li>
                <a href="configuracion.php"
                   class="<?= seccion_activa('configuracion', $pagina, $secciones) ?>">
                    <i class="bi bi-gear-fill"></i>
                    Configuración
                </a>
            </li>
        </ul>
    </div>
    <!-- CERRAR SESIÓN -->
    <div class="logout">
        <a href="../candidatos/logout.php"
           class="btn-logout-trigger"
           data-bs-toggle="modal"
           data-bs-target="#modalConfirmarLogout">
            <i class="bi bi-box-arrow-right me-2"></i>
            Cerrar sesión
        </a>
    </div>
</div>
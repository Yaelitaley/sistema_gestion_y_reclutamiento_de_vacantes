<?php
// ----- Datos del administrador para el topbar (nombre y foto), independiente de lo que ya haya cargado la página -----
$fotoPerfilTopbar = '../assets/img/imagenadministrador.png';
$nombreTopbar = 'Admin';

if (isset($_SESSION['usuario_id']) && isset($conn)) {
    $stmtTopbar = $conn->prepare("SELECT nombre_completo, foto_perfil FROM administradores WHERE usuario_id = ?");
    $stmtTopbar->bind_param('i', $_SESSION['usuario_id']);
    $stmtTopbar->execute();
    $datosTopbar = $stmtTopbar->get_result()->fetch_assoc();
    $stmtTopbar->close();

    if ($datosTopbar) {
        $nombreTopbar = $datosTopbar['nombre_completo'];
        if (!empty($datosTopbar['foto_perfil'])) {
            $fotoPerfilTopbar = '../' . $datosTopbar['foto_perfil'] . '?v=' . time();
        }
    }
}
?>
<div class="top-bar mb-4">
    <!-- IZQUIERDA -->
    <div class="d-flex align-items-center">
        <!-- BOTÓN DEL MENÚ -->
        <button
            class="btn btn-dashboard btn-purple me-3"
            id="menuToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <!-- TÍTULO -->
        <div>
            <h3 class="texto fw-bold mb-0">
                ¡Bienvenido, Administrador!
            </h3>
            <p class="texto mb-0">
                Gestiona reclutadores, candidatos y vacantes del sistema.
            </p>
        </div>
    </div>
    <!-- DERECHA -->
    <div class="admin-profile">
        <i class="bi bi-bell-fill me-4 fs-5 text-warning"></i>
        <div class="d-flex align-items-center">
            <img
                src="<?= htmlspecialchars($fotoPerfilTopbar) ?>"
                class="rounded-circle me-2"
                width="40"
                height="40"
                style="object-fit:cover;"
                alt="Administrador">
            <span class="texto fw-semibold">
                <?= htmlspecialchars($nombreTopbar) ?>
            </span>
        </div>
    </div>
</div>
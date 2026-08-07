<?php
// ----- Datos del reclutador para el topbar (nombre y foto), independiente de lo que ya haya cargado la página -----
$fotoPerfilTopbar = '../assets/img/imagenreclutador.png';
$nombreTopbar = 'Reclutador';

if (isset($_SESSION['usuario_id']) && isset($conn)) {
    $stmtTopbar = $conn->prepare("SELECT nombre_completo, foto_perfil FROM reclutadores WHERE usuario_id = ?");
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

        <!-- BOTÓN MENÚ -->
        <button
    id="menuToggle"
    class="btn btn-reclutador me-3">

    <i class="bi bi-list fs-4"></i>

</button>

        <!-- TÍTULO -->
        <div>

            <h3 class="texto fw-bold mb-0">

                ¡Bienvenido, Reclutador!

            </h3>

            <p class="texto mb-0">

                Encuentra y administra candidatos para tus vacantes.

            </p>

        </div>

        

    </div>

    <!-- PERFIL -->
    <div class="recruiter-profile">

        <i class="bi bi-bell-fill me-4 fs-5 text-warning"></i>

        <div class="d-flex align-items-center">

            <img
                src="<?= htmlspecialchars($fotoPerfilTopbar) ?>"
                class="rounded-circle me-2"
                width="50"
                height="50"
                style="object-fit:cover;"
                alt="Reclutador">
                

            <span class="texto fw-semibold">

                <?= htmlspecialchars($nombreTopbar) ?>

            </span>

        </div>

    </div>

</div>
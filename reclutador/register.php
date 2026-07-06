<?php
require_once '../config/config.php';
require_once '../config/connection.php';
include "includes/header.php";

$empresas = [];
$resEmpresas = $conn->query("SELECT id, nombre FROM empresas ORDER BY nombre ASC");
if ($resEmpresas) {
    while ($fila = $resEmpresas->fetch_assoc()) {
        $empresas[] = $fila;
    }
}
?>

<main class="register-container">

    <div class="top-bar">

        <i class="bi bi-gear-fill"></i>

        <span>
            Registrar Reclutador
        </span>

    </div>

<div class="container-fluid py-5 d-flex justify-content-center">
            <div class="register-box">

            <!-- IMAGEN -->
            <div class="text-center mb-4">

                <img
                    src="../assets/img/imagenreclutador.png"
                    class="img-fluid register-image"
                    alt="Reclutador">

            </div>

            <!-- TITULO -->
            <div class="text-center mb-4">

                <p class="fw-bold">
                    Completa la información para registrar al nuevo Reclutador en el sistema.
                </p>

            </div>

            <!-- FORM -->
            <form id="registerForm">

                <!-- NOMBRE -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Nombre Completo
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            placeholder="Nombre Completo">
                        </div>
                </div>

                <!-- EMPRESA -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Empresa
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-building"></i>
                        </span>

                        <select id="empresa" class="form-select">
                            <option value="">Selecciona una empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= (int) $empresa['id'] ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                </div>

                <!-- TELEFONO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Teléfono
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-telephone-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="telefono"
                            class="form-control"
                            placeholder="Teléfono (opcional)">
                        </div>

                </div>

                <!-- CORREO -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Correo Electrónico
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>

                        <input
                            type="email"
                            id="correo"
                            class="form-control"
                            placeholder="Correo Electrónico">

                    </div>

                </div>

                <!-- PASSWORDS -->
                <div class="row">

                    <!-- PASSWORD -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Contraseña Temporal
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>

                           <input
                                 type="password"
                                 id="password"
                                 class="form-control"
                                 placeholder="********">
                        </div>

                    </div>

                    <!-- CONFIRM -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Confirmar Contraseña
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>

                            <input
                                type="password"
                                id="confirmPassword"
                                class="form-control"
                                placeholder="********">
                            </div>

                    </div>

                </div>

                <!-- CLAVE DE SEGURIDAD -->
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Clave de Seguridad
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="claveSeguridad"
                            class="form-control"
                            placeholder="Clave de seguridad">

                    </div>

                    <small class="text-muted">
                        El reclutador la usará si olvida su contraseña.
                    </small>

                </div>

                <!-- INFO -->
                <div class="text-center mb-4">

                    <small class="text-muted">

                        Proporciona una contraseña temporal para el reclutador.
                        Podrá cambiarla al iniciar sesión.

                    </small>

                </div>

                <!-- BOTON -->
                <div class="text-center">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Registrar Reclutador

                    </button>

                    <div
                         id="mensaje"
                      class="alert mt-3 d-none">

                     </div>

                </div>

                <!-- CANCELAR -->
                <a href="javascript:history.back()"
                    class="cancel-link">

                        Regresar

                </a>

                </div>

            </form>

        </div>

    </div>

</main>

<?php include "includes/footer.php"; ?>
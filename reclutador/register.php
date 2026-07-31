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
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="register-box">
            
            <!-- IMAGEN -->
            <div class="text-center mb-4">
               <i class=" bi-person-badge-fill  icon-reclutador"></i>
                <h2 class="fw-bold text-reclutador">
                    Reclutador
                </h2>
            </div>

            <!-- TITULO -->
            <div class="text-center mb-4">
                <p class="fw-bold">
                    Completa la información para registrar al nuevo Reclutador en el sistema.
                </p>
            </div>

            <!-- FORM -->
            <form id="registerForm" action="actions/register_reclutador.php" method="POST">
                
                <!-- NOMBRE -->
                <div class="mb-3">
                    <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre Completo" required>
                    </div>
                </div>

                <!-- EMPRESA -->
                <div class="mb-3">
                    <label for="empresa" class="form-label fw-bold">Empresa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <select id="empresa" name="empresa" class="form-select" required>
                            <option value="">Selecciona una empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= (int) $empresa['id'] ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- TELEFONO -->
                <div class="mb-3">
                    <label for="telefono" class="form-label fw-bold">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Teléfono (opcional)">
                    </div>
                </div>

                <!-- CORREO -->
                <div class="mb-3">
                    <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="Correo Electrónico" required>
                    </div>
                </div>

                <!-- PASSWORDS -->
                <div class="row">
                    <!-- PASSWORD -->
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Contraseña Temporal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="********" required>
                        </div>
                    </div>

                    <!-- CONFIRM -->
                    <div class="col-md-6 mb-3">
                        <label for="confirmPassword" class="form-label fw-bold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="********" required>
                        </div>
                    </div>
                </div>

                <!-- CLAVE DE SEGURIDAD -->
                <div class="mb-3">
                    <label for="claveSeguridad" class="form-label fw-bold">Clave de Seguridad</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="text" id="claveSeguridad" name="claveSeguridad" class="form-control" placeholder="Clave de seguridad" required>
                    </div>
                    <small class="text-muted">El reclutador la usará si olvida su contraseña.</small>
                </div>

                <!-- INFO -->
                <div class="text-center mb-4">
                    <small class="text-muted">
                        Proporciona una contraseña temporal para el reclutador. Podrá cambiarla al iniciar sesión.
                    </small>
                </div>

                <!-- BOTON -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">Registrar Reclutador</button>
                    <div id="mensaje" class="alert mt-3 d-none"></div>
                </div>

                <!-- CANCELAR -->
                <div class="text-center mt-4">
                    <a href="../admin/index_reclutador.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Regresar
            </a>
                </div>

            </form>
        </div>
    </div>
</main>

<?php include "includes/footer.php"; ?>
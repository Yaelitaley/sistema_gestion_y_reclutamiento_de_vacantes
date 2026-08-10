<footer class="bg-pink mt-1">
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <small class="texto">
                © 2026 INERTIA - Plataforma de Reclutamiento.
            </small>
            <small class="texto">
                Versión 1.0
            </small>
        </div>
    </div>
</footer>
<!-- Modal Confirmar Cerrar Sesión -->
<div class="modal fade" id="modalConfirmarLogout" tabindex="-1" aria-labelledby="modalConfirmarLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarLogoutLabel">Cerrar sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Estás seguro de que deseas cerrar sesión?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarLogout" class="btn btn-danger">
                    <i class="bi bi-box-arrow-left me-1"></i>
                    Sí, cerrar sesión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap -->
<script src="../assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Cliente de la API REST -->
<script src="../assets/js/api-client.js"></script>
<!-- JavaScript -->
<script src="../assets/js/candidato.js"></script>
</body>
</html>
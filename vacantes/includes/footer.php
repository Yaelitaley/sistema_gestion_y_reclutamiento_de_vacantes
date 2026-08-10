<footer class="bg-light py-3 border-top">

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

<script src="../assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/vacantes.js"></script>

</body>
</html>
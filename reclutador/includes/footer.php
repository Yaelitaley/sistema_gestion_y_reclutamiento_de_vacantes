<footer class="bg-light py-0 border-top ">

</footer>

<!-- Bootstrap -->
<script src="../assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- JS -->
<script src="../assets/js/reclutador.js"></script>


<!-- Modal Rechazar Candidato -->
<div
    class="modal fade"
    id="modalRechazar"
    tabindex="-1"
    aria-labelledby="modalRechazarLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5
                    class="modal-title"
                    id="modalRechazarLabel">

                    Confirmar rechazo

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <p class="mb-0">

                    ¿Está seguro de que desea rechazar a este candidato?

                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    id="btnConfirmarRechazo"
                    class="btn btn-danger">

                    Sí, rechazar

                </button>

            </div>

        </div>

    </div>

</div>


<!--MODAL DE ENVIAR MENSAJEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE-->

<!-- Modal Enviar Mensaje -->
<div
    class="modal fade"
    id="modalMensaje"
    tabindex="-1"
    aria-labelledby="modalMensajeLabel"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5
                    class="modal-title"
                    id="modalMensajeLabel">

                    Enviar Mensaje

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Asunto
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="asuntoMensaje"
                        placeholder="Ingrese el asunto">

                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Mensaje
                    </label>

                    <textarea
                        class="form-control"
                        id="contenidoMensaje"
                        rows="5"
                        placeholder="Escriba el mensaje para el candidato..."></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    id="btnEnviarMensaje"
                    class="btn btn-primary">

                    <i class="bi bi-send-fill me-2"></i>
                    Enviar

                </button>

            </div>

        </div>

    </div>

</div>
</body>
</html>
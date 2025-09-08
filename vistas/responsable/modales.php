<?php
// vistas/responsable/modales.php
?>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-text me-2"></i>
                    Detalle de Denuncia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalle">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Cambiar Estado de Denuncia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCambiarEstado">
                <div class="modal-body">
                    <input type="hidden" id="cambio_id_denuncia" name="id_denuncia">
                    
                    <div class="mb-3">
                        <label for="cambio_nuevo_estado" class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="cambio_nuevo_estado" name="nuevo_estado" required>
                            <option value="">Seleccionar estado...</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado['id_estado_denuncia'] ?>" 
                                        data-color="<?= htmlspecialchars($estado['color']) ?>">
                                    <?= htmlspecialchars($estado['nombre_estado']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cambio_comentario" class="form-label">Comentario</label>
                        <textarea class="form-control" id="cambio_comentario" name="comentario" rows="3"
                                  placeholder="Describe el motivo del cambio de estado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Marcar Resuelto -->
<div class="modal fade" id="modalResuelto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Marcar como Resuelto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMarcarResuelto">
                <div class="modal-body">
                    <input type="hidden" id="resuelto_id_denuncia" name="id_denuncia">
                    <input type="hidden" name="nuevo_estado" value="4">
                    
                    <div class="alert alert-success">
                        <i class="bi bi-info-circle me-2"></i>
                        La denuncia será marcada como <strong>RESUELTA</strong> y se notificará al denunciante.
                    </div>
                    
                    <div class="mb-3">
                        <label for="resuelto_comentario" class="form-label">Comentario de Resolución *</label>
                        <textarea class="form-control" id="resuelto_comentario" name="comentario" rows="4" required
                                  placeholder="Describe cómo se resolvió la denuncia..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Marcar como Resuelto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
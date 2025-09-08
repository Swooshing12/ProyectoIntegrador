<?php
// vistas/admin/usuarios_instituciones/modales.php
?>

<!-- Modal Crear Asignación -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCrear">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCrearLabel">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nueva Asignación Usuario-Institución
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Usuario -->
                        <div class="col-12">
                            <label for="id_usuario" class="form-label">
                                <i class="bi bi-person me-1"></i>
                                Usuario Responsable *
                            </label>
                            <select class="form-select" id="id_usuario" name="id_usuario" required>
                                <option value="">Seleccionar usuario...</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id_usuario'] ?>">
                                        <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?> 
                                        (@<?= htmlspecialchars($usuario['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Institución -->
                        <div class="col-12">
                            <label for="id_institucion" class="form-label">
                                <i class="bi bi-building me-1"></i>
                                Institución *
                            </label>
                            <select class="form-select" id="id_institucion" name="id_institucion" required>
                                <option value="">Seleccionar institución...</option>
                                <?php foreach ($instituciones as $institucion): ?>
                                    <option value="<?= $institucion['id_institucion'] ?>">
                                        <?= htmlspecialchars($institucion['nombre_institucion']) ?> 
                                        (<?= htmlspecialchars($institucion['siglas']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Tipo de responsable -->
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="es_responsable_principal" name="es_responsable_principal">
                                <label class="form-check-label" for="es_responsable_principal">
                                    <strong>Responsable Principal</strong>
                                    <small class="text-muted d-block">El responsable principal tiene máxima autoridad sobre las denuncias de esta institución</small>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Comentarios -->
                        <div class="col-12">
                            <label for="comentarios" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>
                                Comentarios
                            </label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3" 
                                      placeholder="Comentarios adicionales sobre esta asignación..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Información Importante
                        </h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li><i class="bi bi-check me-1"></i> Solo usuarios con rol "Responsable Institucional" pueden ser asignados</li>
                            <li><i class="bi bi-check me-1"></i> Un usuario puede ser asignado a múltiples instituciones</li>
                            <li><i class="bi bi-check me-1"></i> Solo puede haber un responsable principal por institución</li>
                            <li><i class="bi bi-check me-1"></i> Los responsables principales tienen acceso completo a las denuncias</li>
                        </ul>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Crear Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Asignación -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditar">
                <input type="hidden" id="edit_id_usuario_institucion" name="id_usuario_institucion">
                
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditarLabel">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Asignación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Info actual -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading mb-2">Asignación Actual</h6>
                        <div id="info_asignacion_actual"></div>
                    </div>
                    
                    <div class="row g-3">
                        <!-- Tipo de responsable -->
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-star me-1"></i>
                                Tipo de Responsable
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_es_responsable_principal" name="es_responsable_principal">
                                <label class="form-check-label" for="edit_es_responsable_principal">
                                    <strong>Responsable Principal</strong>
                                    <small class="text-muted d-block">Máxima autoridad sobre las denuncias de esta institución</small>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-12">
                            <label for="edit_estado_asignacion" class="form-label">
                                <i class="bi bi-toggle-on me-1"></i>
                                Estado de la Asignación
                            </label>
                            <select class="form-select" id="edit_estado_asignacion" name="estado_asignacion" required>
                                <option value="ACTIVO">✅ Activo - El usuario puede gestionar denuncias</option>
                                <option value="INACTIVO">❌ Inactivo - El usuario no puede gestionar denuncias</option>
                            </select>
                        </div>
                        
                        <!-- Comentarios -->
                        <div class="col-12">
                            <label for="edit_comentarios" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>
                                Comentarios
                            </label>
                            <textarea class="form-control" id="edit_comentarios" name="comentarios" rows="3" 
                                      placeholder="Comentarios sobre esta asignación..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i>
                        Actualizar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
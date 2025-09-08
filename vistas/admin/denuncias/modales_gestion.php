<?php
// vistas/admin/denuncias/modales_gestion.php
?>

<!-- Modal: Cambiar Estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCambiarEstado">
                <input type="hidden" id="estado_id_denuncia" name="id_denuncia">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCambiarEstadoLabel">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Cambiar Estado de Denuncia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Info de la denuncia -->
                    <div class="denuncia-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Número:</strong> <span id="estado_numero_denuncia"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Estado Actual:</strong> 
                                <span id="estado_actual_badge"></span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>Título:</strong> <span id="estado_titulo_denuncia"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Nuevo Estado -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nuevo_estado" class="form-label">
                                <i class="bi bi-flag me-1"></i>
                                Nuevo Estado *
                            </label>
                            <select class="form-select" id="nuevo_estado" name="nuevo_estado" required>
                                <option value="">Seleccionar estado...</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= $estado['id_estado_denuncia'] ?>" 
                                            data-color="<?= $estado['color'] ?>">
                                        <?= htmlspecialchars($estado['nombre_estado']) ?> 
                                        - <?= htmlspecialchars($estado['descripcion']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vista Previa</label>
                            <div id="preview_nuevo_estado" class="state-preview">
                                <span class="badge bg-secondary">Selecciona un estado</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comentario -->
                    <div class="mt-3">
                        <label for="comentario_estado" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>
                            Comentario del Cambio
                        </label>
                        <textarea class="form-control" id="comentario_estado" name="comentario" 
                                  rows="3" placeholder="Describe el motivo del cambio de estado..."></textarea>
                        <div class="form-text">
                            Este comentario será visible para el denunciante en el seguimiento.
                        </div>
                    </div>
                    
                    <!-- Reglas de estados -->
                    <div class="estado-rules mt-4">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Reglas de Estados:
                        </h6>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="bi bi-arrow-right me-1"></i> <strong>Pendiente → En Revisión:</strong> Cuando se asigna a una institución</li>
                            <li><i class="bi bi-arrow-right me-1"></i> <strong>En Revisión → En Proceso:</strong> Cuando se inicia la investigación</li>
                            <li><i class="bi bi-arrow-right me-1"></i> <strong>En Proceso → Resuelto:</strong> Cuando se soluciona el problema</li>
                            <li><i class="bi bi-arrow-right me-1"></i> <strong>Cualquier → Rechazado:</strong> Si no procede la denuncia</li>
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
                        Cambiar Estado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Asignar Institución -->
<div class="modal fade" id="modalAsignarInstitucion" tabindex="-1" aria-labelledby="modalAsignarInstitucionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formAsignarInstitucion">
                <input type="hidden" id="asignar_id_denuncia" name="id_denuncia">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAsignarInstitucionLabel">
                        <i class="bi bi-building me-2"></i>
                        Asignar Institución Responsable
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Info de la denuncia -->
                    <div class="denuncia-info mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Número:</strong> <span id="asignar_numero_denuncia"></span><br>
                                <strong>Categoría:</strong> <span id="asignar_categoria_denuncia"></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Estado:</strong> <span id="asignar_estado_denuncia"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selección de institución -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="institucion_asignada" class="form-label">
                                <i class="bi bi-building me-1"></i>
                                Institución Responsable *
                            </label>
                            <select class="form-select" id="institucion_asignada" name="id_institucion" required>
                                <option value="">Seleccionar institución...</option>
                                <?php foreach ($instituciones as $institucion): ?>
                                    <option value="<?= $institucion['id_institucion'] ?>" 
                                            data-tipo="<?= $institucion['tipo_institucion'] ?>">
                                        <?= htmlspecialchars($institucion['nombre_institucion']) ?> 
                                        (<?= htmlspecialchars($institucion['siglas']) ?>)
                                        - <?= htmlspecialchars($institucion['tipo_institucion']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Prioridad -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="prioridad_asignacion" class="form-label">
                                <i class="bi bi-speedometer me-1"></i>
                                Prioridad
                            </label>
                            <select class="form-select" id="prioridad_asignacion" name="prioridad">
                                <option value="BAJA">🟢 Baja - Rutinaria</option>
                                <option value="MEDIA" selected>🟡 Media - Normal</option>
                                <option value="ALTA">🟠 Alta - Urgente</option>
                                <option value="URGENTE">🔴 Urgente - Crítica</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado Resultante</label>
                            <div class="mt-2">
                                <span class="badge bg-info">En Revisión</span>
                                <small class="text-muted d-block">Se cambiará automáticamente</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comentario -->
                    <div class="mt-3">
                        <label for="comentario_asignacion" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>
                            Instrucciones para la Institución
                        </label>
                        <textarea class="form-control" id="comentario_asignacion" name="comentario" 
                                  rows="3" placeholder="Instrucciones específicas o comentarios para la institución asignada..."></textarea>
                    </div>
                    
                    <!-- Info de contacto -->
                    <div class="institucion-info mt-4" id="institucion_contacto" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Información de Contacto</h6>
                                <div id="contacto_detalles"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Asignar Institución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Ver Detalles MEJORADO -->
<div class="modal fade" id="modalVerDetalles" tabindex="-1" aria-labelledby="modalVerDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVerDetallesLabel">
                    <i class="bi bi-eye me-2"></i>
                    Detalles Completos de la Denuncia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Tabs de navegación -->
                <ul class="nav nav-tabs nav-justified" id="detallesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-panel" type="button" role="tab">
                            <i class="bi bi-info-circle me-2"></i>General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seguimiento-tab" data-bs-toggle="tab" data-bs-target="#seguimiento-panel" type="button" role="tab">
                            <i class="bi bi-clock-history me-2"></i>Seguimiento
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="evidencias-tab" data-bs-toggle="tab" data-bs-target="#evidencias-panel" type="button" role="tab">
                            <i class="bi bi-paperclip me-2"></i>Evidencias
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="asignacion-tab" data-bs-toggle="tab" data-bs-target="#asignacion-panel" type="button" role="tab">
                            <i class="bi bi-building me-2"></i>Asignación
                        </button>
                    </li>
                </ul>
                
                <!-- Contenido de los tabs -->
                <div class="tab-content" id="detallesTabContent">
                    <!-- Tab General -->
                    <div class="tab-pane fade show active" id="general-panel" role="tabpanel">
                        <div class="p-4">
                            <div id="detalles_general">
                                <!-- Contenido se carga dinámicamente -->
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando información general...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Seguimiento -->
                    <div class="tab-pane fade" id="seguimiento-panel" role="tabpanel">
                        <div class="p-4">
                            <div id="detalles_seguimiento">
                                <!-- Contenido se carga dinámicamente -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Evidencias -->
                    <div class="tab-pane fade" id="evidencias-panel" role="tabpanel">
                        <div class="p-4">
                            <div id="detalles_evidencias">
                                <!-- Contenido se carga dinámicamente -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Asignación -->
                    <div class="tab-pane fade" id="asignacion-panel" role="tabpanel">
                        <div class="p-4">
                            <div id="detalles_asignacion">
                                <!-- Contenido se carga dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between w-100">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" onclick="gestionDenuncias.imprimirDetalles()">
                            <i class="bi bi-printer me-1"></i>Imprimir
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="gestionDenuncias.exportarDetallesPDF()">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-info" onclick="gestionDenuncias.abrirConsultaPublica(gestionDenuncias.denunciaSeleccionada?.numero_denuncia)">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Consulta Pública
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para previsualizar evidencias -->
<div class="modal fade" id="modalPrevisualizarEvidencia" tabindex="-1" aria-labelledby="modalPrevisualizarEvidenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalPrevisualizarEvidenciaLabel">
                    <i class="bi bi-eye me-2"></i>
                    Previsualización de Evidencia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div id="evidencia_preview_content">
                    <!-- Contenido de la evidencia -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="evidencia-info w-100">
                    <div class="row text-start">
                        <div class="col-md-3">
                            <strong>Archivo:</strong> <span id="preview_nombre"></span>
                        </div>
                        <div class="col-md-2">
                            <strong>Tipo:</strong> <span id="preview_tipo"></span>
                        </div>
                        <div class="col-md-2">
                            <strong>Tamaño:</strong> <span id="preview_tamaño"></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha:</strong> <span id="preview_fecha"></span>
                        </div>
                        <div class="col-md-2">
                            <a href="#" id="preview_descargar" class="btn btn-success btn-sm" target="_blank">
                                <i class="bi bi-download me-1"></i>Descargar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmar Acción -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalConfirmarLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmar Acción
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="confirmar_mensaje">
                    ¿Estás seguro de que deseas realizar esta acción?
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmar_accion">
                    <i class="bi bi-check-circle me-1"></i>
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
<?php
// vistas/admin/usuarios_instituciones/index.php

// Debug inicial
error_log("=== VISTA usuarios_instituciones/index.php ===");
error_log("Variables disponibles: " . implode(', ', array_keys(get_defined_vars())));

// Verificar si tenemos los datos necesarios
$datosCompletos = isset($usuarios) && isset($instituciones) && isset($asignaciones) && isset($permisos);

if (!$datosCompletos) {
    error_log("Datos faltantes - Cargando controlador");
    error_log("usuarios: " . (isset($usuarios) ? 'SI' : 'NO'));
    error_log("instituciones: " . (isset($instituciones) ? 'SI' : 'NO'));
    error_log("asignaciones: " . (isset($asignaciones) ? 'SI' : 'NO'));
    error_log("permisos: " . (isset($permisos) ? 'SI' : 'NO'));
    
    // Cargar el controlador si faltan datos
    require_once __DIR__ . '/../../../controladores/UsuariosInstitucionesControlador/UsuariosInstitucionesController.php';
    $controller = new UsuariosInstitucionesController();
    $controller->index();
    return;
}

// Si llegamos aquí, tenemos todos los datos
error_log("✅ Todos los datos disponibles - Renderizando vista");

// Validar que los datos sean arrays válidos
if (!is_array($usuarios)) $usuarios = [];
if (!is_array($instituciones)) $instituciones = [];
if (!is_array($asignaciones)) $asignaciones = [];
if (!is_array($permisos)) $permisos = ['puede_crear' => false, 'puede_editar' => false, 'puede_eliminar' => false];

// Variables adicionales para la vista
$titulo = "EcoReport - Gestión Usuarios-Instituciones";
$totalResponsables = count($usuarios);
$totalInstituciones = count($instituciones);
$totalAsignaciones = count($asignaciones);
$totalPrincipales = count(array_filter($asignaciones, fn($a) => $a['es_responsable_principal']));

error_log("📊 Estadísticas: $totalResponsables usuarios, $totalInstituciones instituciones, $totalAsignaciones asignaciones");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    
    <!-- Bootstrap CSS -->
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        @media (max-width: 991.98px) {
            .main-content { 
                margin-left: 0; 
            }
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            border-left: 4px solid;
        }
        
        .stats-card.primary { border-left-color: #0d6efd; }
        .stats-card.success { border-left-color: #198754; }
        .stats-card.info { border-left-color: #0dcaf0; }
        .stats-card.warning { border-left-color: #ffc107; }
        
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .debug-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 11px;
            z-index: 9999;
            display: none;
        }
        
        .debug-panel.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Debug Panel (oculto por defecto) -->
    <div id="debugPanel" class="debug-panel">
        <div><strong>DEBUG USUARIOS-INSTITUCIONES</strong></div>
        <div>Usuarios: <?= $totalResponsables ?></div>
        <div>Instituciones: <?= $totalInstituciones ?></div>
        <div>Asignaciones: <?= $totalAsignaciones ?></div>
        <div>Principales: <?= $totalPrincipales ?></div>
        <div>Permisos: <?= json_encode($permisos) ?></div>
        <div class="mt-2">
            <button onclick="document.getElementById('debugPanel').classList.remove('show')" class="btn btn-sm btn-danger">Cerrar</button>
        </div>
    </div>

    <?php 
    // Incluir navbars con manejo de errores
    $headerPath = __DIR__ . "/../../../navbars/header.php";
    $sidebarPath = __DIR__ . "/../../../navbars/sidebar.php";
    
    if (file_exists($headerPath)) {
        include $headerPath;
    } else {
        error_log("⚠️ Header no encontrado en: $headerPath");
    }
    
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    } else {
        error_log("⚠️ Sidebar no encontrado en: $sidebarPath");
    }
    ?>

    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1">
                                <i class="bi bi-people-fill text-primary me-2"></i>
                                Gestión Usuarios-Instituciones
                            </h1>
                            <p class="text-muted mb-0">Asignar responsables institucionales a sus respectivas instituciones</p>
                        </div>
                        <div>
                            <?php if ($permisos['puede_crear'] ?? false): ?>
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalCrear">
                                <i class="bi bi-plus-circle me-1"></i>
                                Nueva Asignación
                            </button>
                            <?php endif; ?>
                            
                            <!-- Botón debug (temporal) -->
                            <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('debugPanel').classList.add('show')" title="Debug">
                                <i class="bi bi-bug"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 stats-card primary">
                        <div class="card-body text-center">
                            <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0"><?= $totalResponsables ?></h3>
                            <p class="text-muted mb-0">Responsables</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 stats-card success">
                        <div class="card-body text-center">
                            <i class="bi bi-building text-success" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0"><?= $totalInstituciones ?></h3>
                            <p class="text-muted mb-0">Instituciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 stats-card info">
                        <div class="card-body text-center">
                            <i class="bi bi-link text-info" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0"><?= $totalAsignaciones ?></h3>
                            <p class="text-muted mb-0">Asignaciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 stats-card warning">
                        <div class="card-body text-center">
                            <i class="bi bi-star text-warning" style="font-size: 2.5rem;"></i>
                            <h3 class="mt-2 mb-0"><?= $totalPrincipales ?></h3>
                            <p class="text-muted mb-0">Principales</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Asignaciones -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Asignaciones Actuales
                        <span class="badge bg-primary ms-2"><?= $totalAsignaciones ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($asignaciones)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No hay asignaciones registradas</h5>
                            <p class="text-muted">Comienza creando una nueva asignación</p>
                            <?php if ($permisos['puede_crear'] ?? false): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                                <i class="bi bi-plus-circle me-1"></i>
                                Crear Primera Asignación
                            </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="bi bi-person me-1"></i>Usuario</th>
                                        <th><i class="bi bi-building me-1"></i>Institución</th>
                                        <th class="text-center"><i class="bi bi-award me-1"></i>Tipo</th>
                                        <th class="text-center"><i class="bi bi-toggle-on me-1"></i>Estado</th>
                                        <th><i class="bi bi-calendar me-1"></i>Fecha</th>
                                        <th class="text-center"><i class="bi bi-gear me-1"></i>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($asignaciones as $index => $asignacion): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                    <?= strtoupper(substr($asignacion['nombres'], 0, 1) . substr($asignacion['apellidos'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($asignacion['nombres'] . ' ' . $asignacion['apellidos']) ?></strong>
                                                    <br><small class="text-muted">@<?= htmlspecialchars($asignacion['username']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($asignacion['nombre_institucion']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($asignacion['siglas']) ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($asignacion['es_responsable_principal']): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-star-fill me-1"></i>Principal
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-person-check me-1"></i>Apoyo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($asignacion['estado_asignacion'] === 'ACTIVO'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Activo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-pause-circle me-1"></i>Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($asignacion['fecha_asignacion'])) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php if ($permisos['puede_editar'] ?? false): ?>
                                                <button class="btn btn-outline-warning" 
                                                        onclick="editarAsignacion(<?= $asignacion['id_usuario_institucion'] ?>)"
                                                        title="Editar asignación">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($permisos['puede_eliminar'] ?? false): ?>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="eliminarAsignacion(<?= $asignacion['id_usuario_institucion'] ?>)"
                                                        title="Eliminar asignación">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Asignación -->
    <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nueva Asignación Usuario-Institución
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_usuario" class="form-label">
                                        <i class="bi bi-person me-1"></i>Usuario Responsable
                                    </label>
                                    <select class="form-select" id="id_usuario" name="id_usuario" required>
                                        <option value="">Seleccionar usuario...</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= $usuario['id_usuario'] ?>">
                                                <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos'] . ' (@' . $usuario['username'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_institucion" class="form-label">
                                        <i class="bi bi-building me-1"></i>Institución
                                    </label>
                                    <select class="form-select" id="id_institucion" name="id_institucion" required>
                                        <option value="">Seleccionar institución...</option>
                                        <?php foreach ($instituciones as $institucion): ?>
                                            <option value="<?= $institucion['id_institucion'] ?>" 
                                                    data-tipo="<?= htmlspecialchars($institucion['tipo_institucion']) ?>">
                                                <?= htmlspecialchars($institucion['nombre_institucion'] . ' (' . $institucion['siglas'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="es_responsable_principal" name="es_responsable_principal">
                                <label class="form-check-label" for="es_responsable_principal">
                                    <i class="bi bi-star me-1"></i>
                                    <strong>Es responsable principal de la institución</strong>
                                    <br><small class="text-muted">El responsable principal tiene autoridad completa sobre la institución</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>Comentarios
                            </label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3" 
                                      placeholder="Observaciones, notas especiales o información adicional..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Crear Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

    <!-- Al final de la vista index.php, antes de cerrar el div main-content -->
<?php include __DIR__ . '/modales.php'; ?>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="../../../js/usuarios_instituciones.js"></script>
    
    <script>
        // Debug y configuración inicial
        console.log('🔧 Vista usuarios_instituciones cargada');
        console.log('📊 Datos cargados:', {
            usuarios: <?= $totalResponsables ?>,
            instituciones: <?= $totalInstituciones ?>,
            asignaciones: <?= $totalAsignaciones ?>,
            principales: <?= $totalPrincipales ?>
        });
        
        // Verificar dependencias
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap 5 disponible');
        } else {
            console.warn('⚠️ Bootstrap no encontrado');
        }
        
        if (typeof Swal !== 'undefined') {
            console.log('✅ SweetAlert2 disponible');
        } else {
            console.warn('⚠️ SweetAlert2 no encontrado');
        }
        
        // Evento para cambio de institución
        document.getElementById('id_institucion')?.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const tipo = option.getAttribute('data-tipo');
            if (tipo) {
                console.log('🏢 Institución seleccionada:', option.text, '- Tipo:', tipo);
            }
        });
    </script>
</body>
</html>
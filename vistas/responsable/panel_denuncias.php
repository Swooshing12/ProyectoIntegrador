<?php

if (!isset($denuncias) || !isset($estados) || !isset($estadisticas)) {
    require_once __DIR__ . '/../../controladores/ResponsableControlador/ResponsableController.php';
    $controller = new ResponsableController();
    $controller->index();
    return;
}

// Validar arrays
if (!is_array($denuncias)) $denuncias = [];
if (!is_array($estados)) $estados = [];
if (!is_array($estadisticas)) $estadisticas = [];
if (!is_array($filtros)) $filtros = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Panel de Responsable</title>
    
    <!-- Bootstrap CSS -->
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        @media (max-width: 991.98px) {
            .main-content { margin-left: 0; }
        }
        
        .stats-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .priority-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .priority-URGENTE { background: #dc3545; }
        .priority-ALTA { background: #fd7e14; }
        .priority-MEDIA { background: #ffc107; color: #000; }
        .priority-BAJA { background: #28a745; }
        
        .denuncia-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .denuncia-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .estado-badge {
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .filters-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .btn-estado {
            border-radius: 20px;
            padding: 0.4rem 1rem;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        /* css/panel_denuncias.css */

.priority-URGENTE { 
    background: #dc3545 !important; 
    color: white !important;
}

.priority-ALTA { 
    background: #fd7e14 !important; 
    color: white !important;
}

.priority-MEDIA { 
    background: #ffc107 !important; 
    color: #000 !important;
}

.priority-BAJA { 
    background: #28a745 !important; 
    color: white !important;
}

.denuncia-card:hover {
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.timeline-item {
    border-left: 3px solid #dee2e6;
    padding-left: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}

@media print {
    .main-content {
        margin-left: 0 !important;
    }
    
    .btn, .filters-card {
        display: none !important;
    }
}
    </style>
</head>
<body>
    <?php 
    if (file_exists(__DIR__ . "/../../navbars/header.php")) {
        include __DIR__ . "/../../navbars/header.php"; 
    }
    
    if (file_exists(__DIR__ . "/../../navbars/sidebar.php")) {
        include __DIR__ . "/../../navbars/sidebar.php"; 
    }
    ?>

    <div class="main-content">
        <div class="container-fluid p-4">
            
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-kanban text-primary me-2"></i>
                        Panel de Gestión de Denuncias
                    </h1>
                    <p class="text-muted mb-0">Gestiona las denuncias asignadas a tu institución</p>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="text-primary mb-0"><?= $estadisticas['total_denuncias'] ?? 0 ?></h4>
                                    <p class="text-muted mb-0">Total Denuncias</p>
                                </div>
                                <div class="text-primary">
                                    <i class="bi bi-inbox-fill" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stats-card border-start border-warning border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="text-warning mb-0"><?= ($estadisticas['pendientes'] ?? 0) + ($estadisticas['en_revision'] ?? 0) ?></h4>
                                    <p class="text-muted mb-0">Pendientes</p>
                                </div>
                                <div class="text-warning">
                                    <i class="bi bi-clock-fill" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stats-card border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="text-info mb-0"><?= $estadisticas['en_proceso'] ?? 0 ?></h4>
                                    <p class="text-muted mb-0">En Proceso</p>
                                </div>
                                <div class="text-info">
                                    <i class="bi bi-gear-fill" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card stats-card border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="text-success mb-0"><?= ($estadisticas['resueltas'] ?? 0) + ($estadisticas['cerradas'] ?? 0) ?></h4>
                                    <p class="text-muted mb-0">Resueltas</p>
                                </div>
                                <div class="text-success">
                                    <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="filters-card">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" class="form-control" name="busqueda" 
                               value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                               placeholder="Número, título o descripción...">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado['id_estado_denuncia'] ?>" 
                                        <?= ($filtros['estado'] == $estado['id_estado_denuncia']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($estado['nombre_estado']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Prioridad</label>
                        <select class="form-select" name="prioridad">
                            <option value="">Todas</option>
                            <option value="URGENTE" <?= ($filtros['prioridad'] == 'URGENTE') ? 'selected' : '' ?>>Urgente</option>
                            <option value="ALTA" <?= ($filtros['prioridad'] == 'ALTA') ? 'selected' : '' ?>>Alta</option>
                            <option value="MEDIA" <?= ($filtros['prioridad'] == 'MEDIA') ? 'selected' : '' ?>>Media</option>
                            <option value="BAJA" <?= ($filtros['prioridad'] == 'BAJA') ? 'selected' : '' ?>>Baja</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="tipo">
                            <option value="">Todos</option>
                            <option value="AMBIENTAL" <?= ($filtros['tipo'] == 'AMBIENTAL') ? 'selected' : '' ?>>Ambiental</option>
                            <option value="OBRAS_PUBLICAS" <?= ($filtros['tipo'] == 'OBRAS_PUBLICAS') ? 'selected' : '' ?>>Obras Públicas</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="?" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Lista de Denuncias -->
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">
                        Denuncias Asignadas 
                        <span class="badge bg-primary"><?= count($denuncias) ?></span>
                    </h5>
                    
                    <?php if (empty($denuncias)): ?>
                        <div class="card denuncia-card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No hay denuncias asignadas</h5>
                                <p class="text-muted">No se encontraron denuncias con los filtros aplicados</p>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <?php foreach ($denuncias as $denuncia): ?>
                        <div class="card denuncia-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Info principal -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="<?= htmlspecialchars($denuncia['icono']) ?> text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <strong><?= htmlspecialchars($denuncia['numero_denuncia']) ?></strong>
                                                </h6>
                                                <h6 class="text-primary mb-2"><?= htmlspecialchars($denuncia['titulo']) ?></h6>
                                                <p class="text-muted mb-2 small">
                                                    <?= htmlspecialchars(substr($denuncia['descripcion'], 0, 100)) ?>...
                                                </p>
                                                <div class="d-flex gap-2 mb-2">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="<?= htmlspecialchars($denuncia['icono']) ?> me-1"></i>
                                                        <?= htmlspecialchars($denuncia['nombre_categoria']) ?>
                                                    </span>
                                                    <?php if ($denuncia['total_evidencias'] > 0): ?>
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-paperclip me-1"></i>
                                                        <?= $denuncia['total_evidencias'] ?> evidencia(s)
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>
                                                    <?= htmlspecialchars($denuncia['denunciante_nombres'] . ' ' . $denuncia['denunciante_apellidos']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Estado y prioridad -->
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <span class="estado-badge" style="background-color: <?= htmlspecialchars($denuncia['color']) ?>; color: white;">
                                                <?= htmlspecialchars($denuncia['nombre_estado']) ?>
                                            </span>
                                            <br><br>
                                            <span class="badge priority-<?= $denuncia['prioridad'] ?> priority-badge">
                                                <?= htmlspecialchars($denuncia['prioridad']) ?>
                                            </span>
                                            <br><br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($denuncia['fecha_creacion'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Acciones -->
                                    <div class="col-md-3">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary btn-sm" onclick="verDetalle(<?= $denuncia['id_denuncia'] ?>)">
                                                <i class="bi bi-eye"></i> Ver Detalle
                                            </button>
                                            
                                            <div class="btn-group">
                                                <button class="btn btn-success btn-sm" onclick="cambiarEstadoRapido(<?= $denuncia['id_denuncia'] ?>, 3)">
                                                    <i class="bi bi-play-circle"></i> En Proceso
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="mostrarCambiarEstado(<?= $denuncia['id_denuncia'] ?>)">
                                                    <i class="bi bi-gear"></i> Cambiar Estado
                                                </button>
                                            </div>
                                            
                                            <?php if (in_array($denuncia['id_estado_denuncia'], [3])): // Solo si está en proceso ?>
                                            <button class="btn btn-success btn-sm" onclick="marcarResuelto(<?= $denuncia['id_denuncia'] ?>)">
                                                <i class="bi bi-check-circle"></i> Marcar Resuelto
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php include __DIR__ . '/modales.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/panel_denuncias.js"></script>
</body>
</html>
<?php
// vistas/admin/usuarios_instituciones/index.php
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    
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
    </style>
</head>
<body>
    <?php include __DIR__ . "/../../../navbars/header.php"; ?>
    <?php include __DIR__ . "/../../../navbars/sidebar.php"; ?>

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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nueva Asignación
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                            <h3 class="mt-2"><?= count($usuarios) ?></h3>
                            <p class="text-muted mb-0">Responsables</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-building text-success" style="font-size: 2rem;"></i>
                            <h3 class="mt-2"><?= count($instituciones) ?></h3>
                            <p class="text-muted mb-0">Instituciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-link text-info" style="font-size: 2rem;"></i>
                            <h3 class="mt-2"><?= count($asignaciones) ?></h3>
                            <p class="text-muted mb-0">Asignaciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-star text-warning" style="font-size: 2rem;"></i>
                            <h3 class="mt-2"><?= count(array_filter($asignaciones, fn($a) => $a['es_responsable_principal'])) ?></h3>
                            <p class="text-muted mb-0">Principales</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Asignaciones -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Asignaciones Actuales
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Institución</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Estado</th>
                                    <th>Fecha Asignación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignaciones as $asignacion): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($asignacion['nombres'] . ' ' . $asignacion['apellidos']) ?></strong>
                                            <br><small class="text-muted">@<?= htmlspecialchars($asignacion['username']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($asignacion['nombre_institucion']) ?></strong>
                                            <br><small class="text-muted">(<?= htmlspecialchars($asignacion['siglas']) ?>)</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($asignacion['es_responsable_principal']): ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-star-fill me-1"></i>Principal
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Apoyo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($asignacion['estado_asignacion'] === 'ACTIVO'): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-warning" 
                                                    onclick="editarAsignacion(<?= $asignacion['id_usuario_institucion'] ?>)"
                                                    title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="eliminarAsignacion(<?= $asignacion['id_usuario_institucion'] ?>)"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php include __DIR__ . '/modales.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="../../../js/usuarios_instituciones.js"></script>
</body>
</html>
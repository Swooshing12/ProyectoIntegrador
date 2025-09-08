<?php
// ✅ CAMBIO: Solo cargar controlador si NO hay datos
if (!isset($data)) {
    require_once __DIR__ . '/../../../controladores/GestionDenuncias/GestionDenunciasController.php';
    $controller = new GestionDenunciasController();
    $controller->index();
    return; // ✅ IMPORTANTE: Salir después de cargar
}

// Extraer datos
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../estilos/gestion_denuncias.css">
</head>
<body>
    <!-- Incluir navbars -->
    <?php include __DIR__ . "/../../../navbars/header.php"; ?>
    <?php include __DIR__ . "/../../../navbars/sidebar.php"; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="bi bi-kanban text-primary me-2"></i>
                            Gestión de Denuncias
                        </h1>
                        <p class="page-subtitle text-muted mb-0">
                            Panel de control para supervisar y gestionar denuncias ambientales y obras públicas
                        </p>
                    </div>
                    <div class="page-actions">
                        <button class="btn btn-outline-primary" onclick="window.open('../../consulta/consultar_estado.php', '_blank')">
                            <i class="bi bi-search me-1"></i>
                            Consulta Pública
                        </button>
                        <button class="btn btn-success" onclick="gestionDenuncias.refrescarDatos()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="stats-container mb-4">
                <div class="row g-3">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card total">
                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['total']) ?></div>
                                <div class="stat-label">Total</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card pendientes">
                            <div class="stat-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['pendientes']) ?></div>
                                <div class="stat-label">Pendientes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card revision">
                            <div class="stat-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['en_revision']) ?></div>
                                <div class="stat-label">En Revisión</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card proceso">
                            <div class="stat-icon">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['en_proceso']) ?></div>
                                <div class="stat-label">En Proceso</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card resueltas">
                            <div class="stat-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['resueltas']) ?></div>
                                <div class="stat-label">Resueltas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="stat-card cerradas">
                            <div class="stat-icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?= number_format($estadisticas['cerradas'] + $estadisticas['rechazadas']) ?></div>
                                <div class="stat-label">Cerradas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filters-container mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-funnel me-2"></i>
                            Filtros de Búsqueda
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filtrosForm" class="row g-3">
                            <div class="col-md-2">
                                <label for="filtro_estado" class="form-label">Estado</label>
                                <select class="form-select" id="filtro_estado" name="estado">
                                    <option value="">Todos los estados</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id_estado_denuncia'] ?>"
                                                data-color="<?= $estado['color'] ?>">
                                            <?= htmlspecialchars($estado['nombre_estado']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filtro_tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="filtro_tipo" name="tipo">
                                    <option value="">Todos los tipos</option>
                                    <option value="AMBIENTAL">Ambiental</option>
                                    <option value="OBRAS_PUBLICAS">Obras Públicas</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filtro_categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="filtro_categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id_categoria'] ?>">
                                            <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filtro_gravedad" class="form-label">Gravedad</label>
                                <select class="form-select" id="filtro_gravedad" name="gravedad">
                                    <option value="">Todas</option>
                                    <option value="BAJA">Baja</option>
                                    <option value="MEDIA">Media</option>
                                    <option value="ALTA">Alta</option>
                                    <option value="CRITICA">Crítica</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filtro_fecha_desde" class="form-label">Desde</label>
                                <input type="date" class="form-control" id="filtro_fecha_desde" name="fecha_desde">
                            </div>
                            <div class="col-md-2">
                                <label for="filtro_fecha_hasta" class="form-label">Hasta</label>
                                <input type="date" class="form-control" id="filtro_fecha_hasta" name="fecha_hasta">
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Filtrar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="gestionDenuncias.limpiarFiltros()">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabla de Denuncias -->
            <div class="table-container">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-table me-2"></i>
                                Lista de Denuncias
                                <span class="badge bg-primary ms-2" id="totalDenuncias"><?= count($denuncias) ?></span>
                            </h5>
                            <div class="table-actions">
                                <button class="btn btn-outline-success btn-sm" onclick="gestionDenuncias.exportarExcel()">
                                    <i class="bi bi-file-earmark-excel me-1"></i>
                                    Excel
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="gestionDenuncias.exportarPDF()">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="tablaDenuncias" class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="150">Número</th>
                                        <th width="200">Título</th>
                                        <th width="120">Estado</th>
                                        <th width="150">Categoría</th>
                                        <th width="100">Gravedad</th>
                                        <th width="150">Ubicación</th>
                                        <th width="150">Institución</th>
                                        <th width="120">Fecha</th>
                                        <th width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php include __DIR__ . '/modales_gestion.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <!-- Datos iniciales para JavaScript -->
    <script>
        window.gestionData = {
            denuncias: <?= json_encode($denuncias) ?>,
            estados: <?= json_encode($estados) ?>,
            categorias: <?= json_encode($categorias) ?>,
            instituciones: <?= json_encode($instituciones) ?>,
            permisos: <?= json_encode($permisos) ?>,
            id_usuario: <?= $id_usuario ?? 0 ?>,
            id_rol: <?= $id_rol ?? 0 ?>
        };
    </script>
    
    <!-- Custom JavaScript -->
    <script src="../../../js/gestion_denuncias.js"></script>
</body>
</html>
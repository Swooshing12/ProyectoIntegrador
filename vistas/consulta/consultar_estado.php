<?php
// Verificar datos básicos
if (!isset($data)) {
    $data = ['titulo' => 'Consultar Estado de Denuncia - EcoReport'];
}

// Verificar si hay sesión iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esUsuarioLogueado = isset($_SESSION['id_usuario']);
$nombreUsuario = $_SESSION['username'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['titulo'] ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../estilos/consulta_estado.css">
</head>
<body>
    <?php if ($esUsuarioLogueado): ?>
        <!-- Usuario logueado: mostrar navbars completos -->
        <?php include __DIR__ . "/../../navbars/header.php"; ?>
        <?php include __DIR__ . "/../../navbars/sidebar.php"; ?>
        
        <!-- Main Content con sidebar -->
        <div class="main-content">
    <?php else: ?>
        <!-- Usuario no logueado: solo header público -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="../../vistas/index.php">
                    <i class="bi bi-heart-pulse-fill me-2"></i>
                    <span>EcoReport</span>
                </a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="../../vistas/index.php">
                        <i class="bi bi-house-fill me-1"></i>Inicio
                    </a>
                    <a class="nav-link" href="../../login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Main Content sin sidebar -->
        <div class="public-content">
    <?php endif; ?>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Header Section -->
                    <div class="search-header text-center mb-4">
                        <div class="header-icon mb-3">
                            <i class="bi bi-search-heart"></i>
                        </div>
                        <h1 class="display-6 mb-3">
                            <span class="text-success">Consultar Estado</span>
                            <span class="text-muted">de Denuncia</span>
                        </h1>
                        <p class="lead text-muted mb-4">
                            Ingresa el número de tu denuncia para conocer su estado actual y seguimiento detallado
                        </p>
                        
                        <?php if ($esUsuarioLogueado): ?>
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-person-check-fill me-2"></i>
                                <span>Bienvenido/a <strong><?= htmlspecialchars($nombreUsuario) ?></strong>, tienes acceso a información detallada</span>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <span>Consulta pública - Para ver información completa, 
                                <a href="../../login.php" class="alert-link">inicia sesión</a></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Search Form -->
                    <div class="search-container mb-4">
                        <form id="formConsulta" class="needs-validation" novalidate>
                            <div class="search-form-group">
                                <label for="numero_denuncia" class="form-label h5">
                                    <i class="bi bi-ticket-detailed text-success me-2"></i>
                                    Número de Denuncia
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="numero_denuncia" 
                                           name="numero_denuncia"
                                           placeholder="Ej: ECO-2025-07-000010" 
                                           pattern="^ECO-\d{4}-\d{2}-\d{6}$"
                                           maxlength="20"
                                           required>
                                    <button class="btn btn-success btn-lg" type="submit" id="btnBuscar">
                                        <i class="bi bi-search me-2"></i>
                                        <span class="btn-text">Consultar Estado</span>
                                    </button>
                                </div>
                                <div class="invalid-feedback text-center mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Por favor ingresa un número de denuncia válido (formato: ECO-YYYY-MM-XXXXXX)
                                </div>
                            </div>
                            
                            <div class="search-help text-center mt-3">
                                <div class="help-items">
                                    <div class="help-item">
                                        <i class="bi bi-envelope-check text-success"></i>
                                        <span>Enviado por correo al crear la denuncia</span>
                                    </div>
                                    <div class="help-item">
                                        <i class="bi bi-shield-check text-info"></i>
                                        <span>Información actualizada en tiempo real</span>
                                    </div>
                                    <div class="help-item">
                                        <i class="bi bi-clock-history text-warning"></i>
                                        <span>Historial completo de seguimiento</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="loading-container d-none">
                        <div class="loading-content">
                            <div class="spinner-grow text-success" role="status">
                                <span class="visually-hidden">Buscando...</span>
                            </div>
                            <div class="loading-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <p class="loading-text">Buscando tu denuncia en el sistema...</p>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="resultados" class="d-none">
                        <!-- Los resultados se cargarán aquí dinámicamente -->
                    </div>

                    <!-- Error Container -->
                    <div id="error" class="alert alert-danger d-none animate__animated animate__shakeX" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Error en la búsqueda</strong>
                                <div id="errorMessage" class="mt-1"></div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" aria-label="Close" onclick="document.getElementById('error').classList.add('d-none')"></button>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Cierre del main-content o public-content -->

    <!-- Modal para previsualizar evidencias -->
    <div class="modal fade" id="evidenciaModal" tabindex="-1" aria-labelledby="evidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidenciaModalLabel">
                        <i class="bi bi-eye me-2"></i>
                        Previsualización de Evidencia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="evidenciaContent">
                        <!-- Contenido de la evidencia se cargará aquí -->
                    </div>
                    <div class="evidencia-info mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Archivo:</strong> <span id="evidenciaNombre"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipo:</strong> <span id="evidenciaTipo"></span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Tamaño:</strong> <span id="evidenciaTamaño"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha:</strong> <span id="evidenciaFecha"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                    <a href="#" id="evidenciaDescargar" class="btn btn-primary" target="_blank">
                        <i class="bi bi-download me-1"></i>Descargar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    
    <!-- Custom JS -->
    <script src="../../js/consulta_estado.js"></script>
</body>
</html>
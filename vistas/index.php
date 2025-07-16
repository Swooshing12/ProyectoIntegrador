<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Sistema de Denuncias Ambientales</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Sistema de gestión de denuncias ambientales y obras públicas. Reporta incidencias de manera fácil y segura.">
    <meta name="keywords" content="denuncias, ambiente, obras públicas, Ecuador, ciudadanía">
    <meta name="author" content="EcoReport Team">
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../estilos/index.css">
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="loading-content">
            <div class="eco-logo">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="loading-text">Cargando EcoReport...</div>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg fixed-top eco-navbar" id="mainNavbar">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="#inicio">
                <div class="brand-container">
                    <div class="brand-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">EcoReport</span>
                        <span class="brand-subtitle">Sistema de Denuncias</span>
                    </div>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#estadisticas">Estadísticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-login" href="login.php">
                            <i class="bi bi-person-circle me-2"></i>Iniciar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="hero-background">
            <div class="hero-particles"></div>
            <div class="hero-overlay"></div>
        </div>
        
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="bi bi-award me-2"></i>
                            Sistema Oficial de Denuncias
                        </div>
                        
                        <h1 class="hero-title">
                            Protege Nuestro 
                            <span class="text-gradient">Entorno</span> 
                            con Tu Voz
                        </h1>
                        
                        <p class="hero-description">
                            Únete a miles de ciudadanos comprometidos en reportar incidencias ambientales 
                            y obras públicas. Tu denuncia puede generar el cambio que nuestra comunidad necesita.
                        </p>
                        
                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">1,247</div>
                                <div class="stat-label">Denuncias Resueltas</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">89%</div>
                                <div class="stat-label">Índice de Respuesta</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">24h</div>
                                <div class="stat-label">Tiempo Promedio</div>
                            </div>
                        </div>
                        
                        <div class="hero-actions">
                            <a href="denuncias/formatodenuncias.php" class="btn btn-primary btn-report">
                                <i class="bi bi-plus-circle-fill me-2"></i>
                                ¡Reportar Ahora!
                                <span class="btn-glow"></span>
                            </a>
                            
                            <a href="#servicios" class="btn btn-outline-light btn-learn">
                                <i class="bi bi-info-circle me-2"></i>
                                Conocer Más
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="visual-container">
                            <div class="floating-card card-1">
                                <i class="bi bi-droplet-fill"></i>
                                <span>Contaminación del Agua</span>
                            </div>
                            <div class="floating-card card-2">
                                <i class="bi bi-tree-fill"></i>
                                <span>Deforestación</span>
                            </div>
                            <div class="floating-card card-3">
                                <i class="bi bi-building"></i>
                                <span>Obras Públicas</span>
                            </div>
                            <div class="central-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-text">Desliza para explorar</div>
            <div class="scroll-arrow">
                <i class="bi bi-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="servicios" class="services-section">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-badge">
                    <i class="bi bi-gear-fill me-2"></i>
                    Nuestros Servicios
                </div>
                <h2 class="section-title">¿Cómo Funciona EcoReport?</h2>
                <p class="section-description">
                    Un proceso simple y transparente para que tu denuncia genere resultados reales
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Service Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon environmental">
                            <i class="bi bi-tree-fill"></i>
                        </div>
                        <h3 class="service-title">Denuncias Ambientales</h3>
                        <p class="service-description">
                            Reporta contaminación del agua, aire, deforestación, 
                            gestión inadecuada de residuos y daños a especies protegidas.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Evidencia fotográfica</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Geolocalización precisa</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Seguimiento en tiempo real</span>
                            </div>
                        </div>
                        <a href="denuncias/formatodenuncias.php" class="service-btn">
                            Reportar Incidencia Ambiental
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon infrastructure">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="service-title">Obras Públicas</h3>
                        <p class="service-description">
                            Denuncia problemas en vías, infraestructura educativa, de salud, 
                            servicios públicos y espacios recreativos.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Obras inconclusas</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Mal estado de infraestructura</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Servicios deficientes</span>
                            </div>
                        </div>
                        <a href="denuncias/formatodenuncias.php" class="service-btn">
                            Reportar Obra Pública
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service Card 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon tracking">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="service-title">Seguimiento Transparente</h3>
                        <p class="service-description">
                            Monitorea el progreso de tu denuncia desde el reporte inicial 
                            hasta la resolución final con total transparencia.
                        </p>
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Notificaciones automáticas</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Estado en tiempo real</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Comunicación directa</span>
                            </div>
                        </div>
                        <a href="login.php" class="service-btn">
                            Acceder al Panel
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="estadisticas" class="stats-section">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-badge">
                    <i class="bi bi-graph-up-arrow me-2"></i>
                    Impacto Real
                </div>
                <h2 class="section-title">Resultados que Hablan por Sí Solos</h2>
                <p class="section-description">
                    Gracias a la participación ciudadana, hemos logrado generar cambios 
                    significativos en nuestra comunidad y medio ambiente.
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="stat-number" data-count="1247">0</div>
                        <div class="stat-label">Denuncias Resueltas</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number" data-count="5820">0</div>
                        <div class="stat-label">Ciudadanos Activos</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <div class="stat-number" data-count="23">0</div>
                        <div class="stat-label">Instituciones Vinculadas</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div class="stat-number" data-count="24">0</div>
                        <div class="stat-label">Horas Promedio de Respuesta</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contacto" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-content">
                        <div class="section-badge">
                            <i class="bi bi-envelope-fill me-2"></i>
                            Contáctanos
                        </div>
                        <h2 class="section-title">¿Necesitas Ayuda o Tienes Dudas?</h2>
                        <p class="section-description">
                            Nuestro equipo está aquí para ayudarte con cualquier consulta 
                            sobre el proceso de denuncias o el seguimiento de casos.
                        </p>
                        
                        <div class="contact-info">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="contact-details">
                                    <h4>Correo Electrónico</h4>
                                    <p>soporte@ecoreport.gob.ec</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="contact-details">
                                    <h4>Teléfono de Soporte</h4>
                                    <p>1800-ECOPORT (326-7678)</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                                <div class="contact-details">
                                    <h4>Horario de Atención</h4>
                                    <p>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="quick-actions">
                        <h3>Acciones Rápidas</h3>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-plus-circle-fill"></i>
                            </div>
                            <div class="action-content">
                                <h4>Nueva Denuncia</h4>
                                <p>Reporta una nueva incidencia ambiental o de obra pública</p>
                                <a href="denuncias/formatodenuncias.php" class="action-btn">
                                    Reportar Ahora
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-search"></i>
                            </div>
                            <div class="action-content">
                                <h4>Consultar Estado</h4>
                                <p>Verifica el progreso de tus denuncias existentes</p>
                                <a href="login.php" class="action-btn">
                                    Iniciar Sesión
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-question-circle-fill"></i>
                            </div>
                            <div class="action-content">
                                <h4>Centro de Ayuda</h4>
                                <p>Encuentra respuestas a preguntas frecuentes</p>
                                <a href="#" class="action-btn">
                                    Ver FAQ
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="brand-container">
                            <div class="brand-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="brand-text">
                                <span class="brand-name">EcoReport</span>
                                <span class="brand-subtitle">Sistema de Denuncias</span>
                            </div>
                        </div>
                        <p class="footer-description">
                            Protegiendo nuestro entorno a través de la participación ciudadana 
                            y la transparencia institucional.
                        </p>
                        <div class="social-links">
                            <a href="#" class="social-link">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-links">
                        <h5>Servicios</h5>
                        <ul>
                            <li><a href="denuncias/formatodenuncias.php">Nueva Denuncia</a></li>
                            <li><a href="login.php">Seguimiento</a></li>
                            <li><a href="#estadisticas">Estadísticas</a></li>
                            <li><a href="#contacto">Soporte</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-links">
                        <h5>Información</h5>
                        <ul>
                            <li><a href="#">Acerca de</a></li>
                            <li><a href="#">Cómo Funciona</a></li>
                            <li><a href="#">Instituciones</a></li>
                            <li><a href="#">Transparencia</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-links">
                        <h5>Legal</h5>
                        <ul>
                            <li><a href="#">Política de Privacidad</a></li>
                            <li><a href="#">Términos de Servicio</a></li>
                            <li><a href="#">Cookies</a></li>
                            <li><a href="#">Transparencia</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <div class="footer-links">
                        <h5>Contacto</h5>
                        <ul>
                            <li><a href="mailto:soporte@ecoreport.gob.ec">soporte@ecoreport.gob.ec</a></li>
                            <li><a href="tel:1800326767">1800-ECOPORT</a></li>
                            <li><a href="#">Chat en Vivo</a></li>
                            <li><a href="#">Centro de Ayuda</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="footer-copyright">
                            © 2025 EcoReport. Todos los derechos reservados.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="footer-credits">
                            Desarrollado con 💚 para el cuidado del medio ambiente
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- External JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="../js/index.js"></script>
</body>
</html>
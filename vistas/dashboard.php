<?php
require_once "../helpers/permisos.php"; // Protección de sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport Dashboard | Sistema Denuncias</title>
    
    <!-- Bootstrap CSS -->
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../estilos/dashboard.css">
</head>
<body>
    <!-- Incluir la barra de navegación -->
    <?php include "../navbars/header.php"; ?>
    
    <!-- Incluir el sidebar -->
    <?php include "../navbars/sidebar.php"; ?>

    <!-- Contenido principal -->
<main class="dashboard-container">
    <div class="container-fluid p-4">
        <!-- Header Hero Section Rediseñado -->
        <div class="hero-header-clinic" data-aos="fade-down">
            <div class="hero-overlay"></div>
            <div class="hero-content-clinic">
                <div class="welcome-section">
                    <div class="welcome-icon">
                        <i class="bi bi-hospital-fill"></i>
                    </div>
                    <div class="welcome-text">
                        <h1 class="clinic-title">
                            <span class="clinic-name">EcoReport</span>
                            <span class="clinic-subtitle">Centro Denuncias</span>
                        </h1>
                        <p class="welcome-message">
                            Bienvenido/a, <span class="user-name"><?php echo $_SESSION["username"]; ?></span>
                        </p>

                    </div>
                </div>
                
              
                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <h4 class="contact-info-title">
                            <i class="bi bi-telephone-fill me-2"></i>
                            Contáctanos
                        </h4>
                        
                        <div class="contact-methods">
                            <div class="contact-method">
                                <div class="method-icon emergency-contact">
                                    <i class="bi bi-phone-vibrate"></i>
                                </div>
                                <div class="method-info">
                                    <h6>Emergencias</h6>
                                    <p>911 / (02) 2-911-911</p>
                                    <span class="availability">24 horas</span>
                                </div>
                            </div>
                            
                            <div class="contact-method">
                                <div class="method-icon general-contact">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div class="method-info">
                                    <h6>Citas y Consultas</h6>
                                    <p>(02) 2-456-789</p>
                                    <span class="availability">Lun - Dom: 6:00 - 22:00</span>
                                </div>
                            </div>
                            
                            <div class="contact-method">
                                <div class="method-icon email-contact">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div class="method-info">
                                    <h6>Correo Electrónico</h6>
                                    <p>info@ecoreport.com</p>
                                    <span class="availability">Respuesta en 2 horas</span>
                                </div>
                            </div>
                            
                            <div class="contact-method">
                                <div class="method-icon whatsapp-contact">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                                <div class="method-info">
                                    <h6>WhatsApp</h6>
                                    <p>+593 99 123 4567</p>
                                    <span class="availability">Chat directo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
$(document).ready(function() {
    // Inicializar AOS (animaciones)
    AOS.init({
        duration: 800,
        easing: 'ease-out',
        once: true,
        offset: 100
    });

    // Inicializar efectos del dashboard
    initializeClinicDashboard();
    
    // Animaciones de entrada
    setTimeout(() => {
        $('.content-loading').addClass('content-loaded');
    }, 300);
    
    // Efectos de parallax suave
    initializeParallaxEffects();
    
    // Efectos hover mejorados
    initializeHoverEffects();
});

function initializeClinicDashboard() {
    console.log('🏥 Dashboard Clínica MediSys inicializado');
    
    // Animación de contador en stats hero
    animateHeroCounters();
    
    // Efectos de typing en el tagline
    initializeTypingEffect();
}

function animateHeroCounters() {
    $('.stat-hero-number').each(function() {
        const $this = $(this);
        const finalText = $this.text();
        const isPercentage = finalText.includes('%');
        const isPlus = finalText.includes('+');
        const numericValue = parseInt(finalText.replace(/[^0-9]/g, ''));
        
        $this.text('0');
        
        $({ countNum: 0 }).animate({
            countNum: numericValue
        }, {
            duration: 2500,
            easing: 'swing',
            step: function() {
                const current = Math.floor(this.countNum);
                let displayText = current.toLocaleString();
                
                if (isPercentage) displayText += '%';
                if (isPlus && current > 0) displayText += '+';
                
                $this.text(displayText);
            },
            complete: function() {
                $this.text(finalText);
            }
        });
    });
}

function initializeTypingEffect() {
    const tagline = $('.clinic-tagline');
    const text = tagline.text();
    tagline.text('');
    
    setTimeout(() => {
        let i = 0;
        const typeInterval = setInterval(() => {
            tagline.text(text.slice(0, i));
            i++;
            if (i > text.length) {
                clearInterval(typeInterval);
            }
        }, 50);
    }, 1000);
}

function initializeParallaxEffects() {
    $(window).scroll(function() {
        const scrollTop = $(this).scrollTop();
        const windowHeight = $(this).height();
        
        // Parallax suave en el hero
        $('.hero-overlay').css({
            'transform': `translateY(${scrollTop * 0.3}px)`
        });
        
        // Fade out del hero
        const heroOpacity = Math.max(0, 1 - scrollTop / windowHeight);
        $('.hero-header-clinic').css('opacity', heroOpacity);
    });
}

function initializeHoverEffects() {
    // Efecto hover en service cards
    $('.service-card').hover(
        function() {
            $(this).find('.service-icon').addClass('animate__animated animate__pulse');
        },
        function() {
            $(this).find('.service-icon').removeClass('animate__animated animate__pulse');
        }
    );
    
    // Efecto hover en medical cards
    $('.medical-card').hover(
        function() {
            $(this).siblings().css('opacity', '0.7');
        },
        function() {
            $(this).siblings().css('opacity', '1');
        }
    );
    
    // Efecto click en contact methods
    $('.contact-method').click(function() {
        const method = $(this);
        method.addClass('animate__animated animate__heartBeat');
        
        setTimeout(() => {
            method.removeClass('animate__animated animate__heartBeat');
        }, 1000);
    });
}

// Funciones adicionales para interactividad
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="notification notification-${type}">
            <i class="bi bi-info-circle me-2"></i>
            ${message}
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Smooth scroll para navegación interna
$('a[href^="#"]').click(function(e) {
    e.preventDefault();
    const target = $($(this).attr('href'));
    if (target.length) {
        $('html, body').animate({
            scrollTop: target.offset().top - 80
        }, 800);
    }
});

// Lazy loading para imágenes (si las agregas después)
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}
</script>
</body>
</html>
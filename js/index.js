// js/index.js

/**
 * ECOREPORT - INDEX JAVASCRIPT PREMIUM
 * Sistema de Denuncias Ambientales y Obras Públicas
 * Funcionalidades interactivas y animaciones avanzadas
 */

// ===================================================
// CONFIGURACIÓN GLOBAL Y VARIABLES
// ===================================================

const EcoReport = {
    // Configuración
    config: {
        loadingDuration: 2000,
        scrollThreshold: 100,
        animationDelay: 150,
        countAnimationDuration: 2000,
        particleCount: 50,
        debugMode: false
    },
    
    // Estado de la aplicación
    state: {
        isLoaded: false,
        isScrolled: false,
        currentSection: 'inicio',
        animatedElements: new Set(),
        particles: []
    },
    
    // Selectores DOM
    selectors: {
        loadingScreen: '#loadingScreen',
        navbar: '#mainNavbar',
        navLinks: '.nav-link',
        heroSection: '#inicio',
        sections: 'section[id]',
        statNumbers: '.stat-number[data-count]',
        revealElements: '.fade-in-up, .fade-in-left, .fade-in-right',
        serviceCards: '.service-card',
        contactItems: '.contact-item',
        actionCards: '.action-card'
    }
};

// ===================================================
// INICIALIZACIÓN Y CARGA
// ===================================================

class AppInitializer {
    constructor() {
        this.init();
    }
    
    init() {
        if (EcoReport.config.debugMode) {
            console.log('🌱 EcoReport iniciando...');
        }
        
        // Detectar si el usuario usa mouse o teclado
        this.setupAccessibility();
        
        // Configurar loading screen
        this.setupLoadingScreen();
        
        // Configurar navegación
        this.setupNavigation();
        
        // Configurar animaciones de scroll
        this.setupScrollAnimations();
        
        // Configurar contadores animados
        this.setupCounters();
        
        // Configurar efectos de hover
        this.setupInteractiveEffects();
        
        // Configurar smooth scrolling
        this.setupSmoothScrolling();
        
        // Configurar partículas (opcional)
        this.setupParticleEffects();
        
        // Configurar eventos de redimensionado
        this.setupResizeHandlers();
        
        // Marcar como inicializado
        document.body.classList.add('js-loaded');
        
        if (EcoReport.config.debugMode) {
            console.log('✅ EcoReport inicializado correctamente');
        }
    }
    
    setupAccessibility() {
        // Detectar uso de mouse vs teclado para focus styles
        let isMouseUser = false;
        
        document.addEventListener('mousedown', () => {
            if (!isMouseUser) {
                isMouseUser = true;
                document.body.classList.add('mouse-user');
            }
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab' && isMouseUser) {
                isMouseUser = false;
                document.body.classList.remove('mouse-user');
            }
        });
    }
    
    setupLoadingScreen() {
        const loadingScreen = document.querySelector(EcoReport.selectors.loadingScreen);
        
        if (!loadingScreen) return;
        
        // Simular carga progresiva
        const progressBar = loadingScreen.querySelector('.loading-progress');
        let progress = 0;
        
        const loadingInterval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 100) progress = 100;
            
            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }
            
            if (progress >= 100) {
                clearInterval(loadingInterval);
                setTimeout(() => {
                    this.hideLoadingScreen(loadingScreen);
                }, 500);
            }
        }, 200);
        
        // Fallback para ocultar loading después de tiempo máximo
        setTimeout(() => {
            if (!EcoReport.state.isLoaded) {
                this.hideLoadingScreen(loadingScreen);
            }
        }, EcoReport.config.loadingDuration);
    }
    
    hideLoadingScreen(loadingScreen) {
        loadingScreen.classList.add('hidden');
        EcoReport.state.isLoaded = true;
        
        // Iniciar animaciones de entrada después de ocultar loading
        setTimeout(() => {
            this.startEntryAnimations();
        }, 300);
        
        if (EcoReport.config.debugMode) {
            console.log('🎬 Loading screen ocultado');
        }
    }
    
    startEntryAnimations() {
        // Animar elementos del hero
        const heroElements = document.querySelectorAll('.hero-badge, .hero-title, .hero-description, .hero-stats, .hero-actions');
        
        heroElements.forEach((element, index) => {
            setTimeout(() => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 50);
            }, index * EcoReport.config.animationDelay);
        });
        
        // Animar tarjetas flotantes
        const floatingCards = document.querySelectorAll('.floating-card');
        floatingCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8) translateY(20px)';
                card.style.transition = 'all 1s cubic-bezier(0.34, 1.56, 0.64, 1)';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1) translateY(0)';
                }, 50);
            }, (index + 2) * EcoReport.config.animationDelay);
        });
    }
    
    setupNavigation() {
        const navbar = document.querySelector(EcoReport.selectors.navbar);
        const navLinks = document.querySelectorAll(EcoReport.selectors.navLinks);
        
        if (!navbar) return;
        
        // Scroll spy para navegación
        this.setupScrollSpy(navLinks);
        
        // Efecto de scroll en navbar
        window.addEventListener('scroll', this.throttle(() => {
            const scrolled = window.scrollY > EcoReport.config.scrollThreshold;
            
            if (scrolled !== EcoReport.state.isScrolled) {
                EcoReport.state.isScrolled = scrolled;
                navbar.classList.toggle('scrolled', scrolled);
                document.body.classList.toggle('js-scrolled', scrolled);
            }
        }, 16));
        
        // Cerrar menú móvil al hacer clic en enlaces
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
    }
    
    setupScrollSpy(navLinks) {
        const sections = document.querySelectorAll(EcoReport.selectors.sections);
        
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '-20% 0px -20% 0px'
        };
        
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const currentSection = entry.target.id;
                    
                    if (currentSection !== EcoReport.state.currentSection) {
                        EcoReport.state.currentSection = currentSection;
                        
                        // Actualizar navegación activa
                        navLinks.forEach(link => {
                            const href = link.getAttribute('href');
                            const isActive = href === `#${currentSection}`;
                            link.classList.toggle('active', isActive);
                        });
                        
                        if (EcoReport.config.debugMode) {
                            console.log(`📍 Sección activa: ${currentSection}`);
                        }
                    }
                }
            });
        }, observerOptions);
        
        sections.forEach(section => {
            sectionObserver.observe(section);
        });
    }
    
    setupScrollAnimations() {
        const revealElements = document.querySelectorAll(EcoReport.selectors.revealElements);
        
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !EcoReport.state.animatedElements.has(entry.target)) {
                    entry.target.classList.add('visible');
                    EcoReport.state.animatedElements.add(entry.target);
                    
                    if (EcoReport.config.debugMode) {
                        console.log('🎭 Elemento animado:', entry.target);
                    }
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        revealElements.forEach(element => {
            revealObserver.observe(element);
        });
    }
    
    setupCounters() {
        const statNumbers = document.querySelectorAll(EcoReport.selectors.statNumbers);
        
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    this.animateCounter(entry.target);
                    entry.target.classList.add('counted');
                }
            });
        }, {
            threshold: 0.5
        });
        
        statNumbers.forEach(counter => {
            counterObserver.observe(counter);
        });
    }
    
    animateCounter(element) {
        const target = parseInt(element.dataset.count);
        const duration = EcoReport.config.countAnimationDuration;
        const startTime = performance.now();
        
        const updateCounter = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function para suavizar la animación
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(target * easeOutQuart);
            
            element.textContent = this.formatNumber(current);
            element.classList.add('animate');
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = this.formatNumber(target);
                
                if (EcoReport.config.debugMode) {
                    console.log(`🔢 Contador completado: ${target}`);
                }
            }
        };
        
        requestAnimationFrame(updateCounter);
    }
    
    formatNumber(num) {
        // Formatear números grandes con comas
        if (num >= 1000) {
            return num.toLocaleString();
        }
        return num.toString();
    }
    
    setupInteractiveEffects() {
        // Efectos para tarjetas de servicio
        const serviceCards = document.querySelectorAll(EcoReport.selectors.serviceCards);
        serviceCards.forEach((card, index) => {
            this.addCardEffects(card, index);
        });
        
        // Efectos para elementos de contacto
        const contactItems = document.querySelectorAll(EcoReport.selectors.contactItems);
        contactItems.forEach(item => {
            this.addHoverEffects(item);
        });
        
        // Efectos para tarjetas de acción
        const actionCards = document.querySelectorAll(EcoReport.selectors.actionCards);
        actionCards.forEach(card => {
            this.addHoverEffects(card);
        });
        
        // Efecto parallax sutil en hero
        this.setupParallaxEffect();
    }
    
    addCardEffects(card, index) {
        // Efecto de entrada escalonada
        setTimeout(() => {
            card.classList.add('fade-in-up');
        }, index * 100);
        
        // Efecto de hover con transformación 3D
        card.addEventListener('mouseenter', (e) => {
            card.style.transform = 'translateY(-8px) rotateX(5deg)';
            card.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
        });
        
        card.addEventListener('mouseleave', (e) => {
            card.style.transform = 'translateY(0) rotateX(0deg)';
        });
        
        // Efecto de movimiento del mouse
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            card.style.transform = `translateY(-8px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
    }
    
    addHoverEffects(element) {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'translateX(5px) scale(1.02)';
            element.style.transition = 'all 0.3s ease-out';
        });
        
        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translateX(0) scale(1)';
        });
    }
    
    setupParallaxEffect() {
        const heroSection = document.querySelector(EcoReport.selectors.heroSection);
        const heroParticles = document.querySelector('.hero-particles');
        
        if (!heroSection || !heroParticles) return;
        
        window.addEventListener('scroll', this.throttle(() => {
            const scrolled = window.scrollY;
            const rate = scrolled * -0.5;
            
            heroParticles.style.transform = `translateY(${rate}px)`;
        }, 16));
    }
    
    setupSmoothScrolling() {
        // Smooth scrolling para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const href = anchor.getAttribute('href');
                
                if (href === '#') return;
                
                e.preventDefault();
                
                const target = document.querySelector(href);
                if (target) {
                    const navbarHeight = document.querySelector(EcoReport.selectors.navbar)?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - navbarHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Añadir hash a la URL después del scroll
                    setTimeout(() => {
                        history.pushState(null, null, href);
                    }, 100);
                }
            });
        });
    }
    
    setupParticleEffects() {
        if (!window.requestAnimationFrame) return;
        
        const particlesContainer = document.querySelector('.hero-particles');
        if (!particlesContainer) return;
        
        // Crear partículas dinámicas (opcional, para mejor rendimiento)
        this.createFloatingParticles(particlesContainer);
    }
    
    createFloatingParticles(container) {
        const particleCount = Math.min(EcoReport.config.particleCount, 30); // Limitar para rendimiento
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'floating-particle';
            
            // Estilos CSS dinámicos
            Object.assign(particle.style, {
                position: 'absolute',
                width: `${Math.random() * 4 + 2}px`,
                height: `${Math.random() * 4 + 2}px`,
                background: 'rgba(255, 255, 255, 0.1)',
                borderRadius: '50%',
                left: `${Math.random() * 100}%`,
                top: `${Math.random() * 100}%`,
                animation: `particleFloat ${5 + Math.random() * 10}s linear infinite`,
                animationDelay: `${Math.random() * 5}s`
            });
            
            container.appendChild(particle);
            EcoReport.state.particles.push(particle);
        }
    }
    
    setupResizeHandlers() {
        let resizeTimeout;
        
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });
    }
    
    handleResize() {
        // Recalcular posiciones y animaciones si es necesario
        const isMobile = window.innerWidth < 768;
        
        if (isMobile !== EcoReport.state.isMobile) {
            EcoReport.state.isMobile = isMobile;
            
            // Ajustar efectos para móvil
            if (isMobile) {
                this.disableMobileEffects();
            } else {
                this.enableDesktopEffects();
            }
        }
        
        if (EcoReport.config.debugMode) {
            console.log(`📱 Resize detectado: ${window.innerWidth}x${window.innerHeight}`);
        }
    }
    
    disableMobileEffects() {
        // Deshabilitar efectos costosos en móvil
        const serviceCards = document.querySelectorAll(EcoReport.selectors.serviceCards);
        serviceCards.forEach(card => {
            card.style.transform = '';
            card.removeEventListener('mousemove', () => {});
        });
    }
    
    enableDesktopEffects() {
        // Rehabilitar efectos para desktop
        const serviceCards = document.querySelectorAll(EcoReport.selectors.serviceCards);
        serviceCards.forEach((card, index) => {
            this.addCardEffects(card, index);
        });
    }
    
    // Utility: Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

// ===================================================
// FUNCIONALIDADES ADICIONALES
// ===================================================

class InteractionManager {
    constructor() {
        this.setupButtonEffects();
        this.setupFormEnhancements();
        this.setupNotifications();
        this.setupKeyboardNavigation();
    }
    
    setupButtonEffects() {
        // Efecto ripple para botones
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', this.createRippleEffect.bind(this));
        });
    }
    
    createRippleEffect(e) {
        const button = e.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        // CSS para el ripple
        if (!document.querySelector('#ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: scale(0);
                    animation: rippleAnimation 0.6s linear;
                    pointer-events: none;
                    z-index: 1;
                }
                @keyframes rippleAnimation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                .btn {
                    position: relative;
                    overflow: hidden;
                }
            `;
            document.head.appendChild(style);
        }
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 600);
    }
    
    setupFormEnhancements() {
        // Mejorar experiencia de formularios (si los hay)
        document.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('focus', (e) => {
                e.target.parentElement?.classList.add('focused');
            });
            
            field.addEventListener('blur', (e) => {
                e.target.parentElement?.classList.remove('focused');
            });
        });
    }
    
    setupNotifications() {
        // Sistema de notificaciones toast (básico)
        this.createNotificationContainer();
    }
    
    createNotificationContainer() {
        if (document.querySelector('#toast-container')) return;
        
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    
    showNotification(message, type = 'info', duration = 3000) {
        const container = document.querySelector('#toast-container');
        if (!container) return;
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: var(--eco-green);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
            pointer-events: auto;
            cursor: pointer;
        `;
        toast.textContent = message;
        
        container.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 50);
        
        // Auto-remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, duration);
        
        // Remove on click
        toast.addEventListener('click', () => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        });
    }
    
    setupKeyboardNavigation() {
        // Mejorar navegación por teclado
        document.addEventListener('keydown', (e) => {
            // Esc para cerrar modales
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(modal => {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                });
            }
            
            // Ctrl/Cmd + K para enfoque en búsqueda (si existe)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('#globalSearch');
                if (searchInput) searchInput.focus();
            }
        });
    }
}

// ===================================================
// INICIALIZACIÓN AUTOMÁTICA
// ===================================================

document.addEventListener('DOMContentLoaded', () => {
    // Verificar que Bootstrap esté cargado
    if (typeof bootstrap === 'undefined') {
        console.warn('⚠️ Bootstrap no está cargado. Algunas funcionalidades pueden no funcionar.');
    }
    
    // Inicializar aplicación
    const app = new AppInitializer();
    const interactions = new InteractionManager();
    
    // Exponer funciones globales para uso externo
    window.EcoReport = {
        ...EcoReport,
        showNotification: interactions.showNotification.bind(interactions),
        scrollToSection: (sectionId) => {
            const element = document.querySelector(`#${sectionId}`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }
    };
    
    // Debug info
    if (EcoReport.config.debugMode) {
        console.log('🌱 EcoReport cargado completamente');
        console.log('📊 Estado:', EcoReport.state);
        console.log('⚙️ Configuración:', EcoReport.config);
    }
});

// ===================================================
// PERFORMANCE MONITORING (OPCIONAL)
// ===================================================

if ('performance' in window && EcoReport.config.debugMode) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.timing;
            const loadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log(`⚡ Tiempo de carga total: ${loadTime}ms`);
        }, 0);
    });
}

// ===================================================
// SERVICE WORKER REGISTRATION (FUTURO)
// ===================================================

if ('serviceWorker' in navigator && location.protocol === 'https:') {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                if (EcoReport.config.debugMode) {
                    console.log('📡 Service Worker registrado:', registration);
                }
            })
            .catch(error => {
                if (EcoReport.config.debugMode) {
                    console.log('❌ Error registrando Service Worker:', error);
                }
            });
    });
}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Denuncia y Protege Nuestro Entorno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#16a34a',
                        'eco-dark': '#14532d',
                        'eco-light': '#dcfce7',
                        'danger-red': '#dc2626'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0f766e 0%, #059669 50%, #10b981 100%);
        }
        
        .hero-pattern {
            background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%),
                              radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-eco-green to-emerald-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">EcoReport</span>
                </div>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="#inicio" class="text-gray-700 hover:text-eco-green font-medium transition-colors">Inicio</a>
                    <a href="#como-funciona" class="text-gray-700 hover:text-eco-green font-medium transition-colors">¿Cómo funciona?</a>
                    <a href="#reportar" class="text-gray-700 hover:text-eco-green font-medium transition-colors">Reportar</a>
                    <a href="#contacto" class="text-gray-700 hover:text-eco-green font-medium transition-colors">Contacto</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <button class="bg-eco-green text-white px-6 py-2 rounded-lg font-medium hover:bg-eco-dark transition-all duration-300 transform hover:scale-105">
                        Iniciar Sesión
                    </button>
                    <button class="md:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="gradient-bg bg-green-800 hero-pattern min-h-screen flex items-center relative overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Denuncia y <span class="text-green-300">Protege</span><br>
                        Nuestro Entorno Hoy
                    </h1>
                    <p class="text-xl mb-8 text-green-100 leading-relaxed">
                        Únete a la comunidad ciudadana para reportar incidencias ambientales y de obras 
                        de forma fácil, segura y con seguimiento en tiempo real.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="bg-white text-eco-green px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 pulse-animation">
                            🚨 ¡Reportar Ahora!
                        </button>
                        <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-eco-green transition-all duration-300">
                            Ver Reportes
                        </button>
                    </div>
                    <div class="mt-12 flex items-center space-x-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-300">2,548</div>
                            <div class="text-sm text-green-100">Reportes Realizados</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-300">1,234</div>
                            <div class="text-sm text-green-100">Casos Resueltos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-300">89%</div>
                            <div class="text-sm text-green-100">Tasa de Éxito</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="relative z-10">
                        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md mx-auto">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-eco-green to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">¡Denuncia Incidencias!</h3>
                                <p class="text-gray-600 mt-2">Ambientales y de obras</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
                                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-red-800">Contaminación</div>
                                        <div class="text-sm text-red-600">Agua, aire, suelo</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg">
                                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-orange-800">Obras Irregulares</div>
                                        <div class="text-sm text-orange-600">Sin permisos</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-green-800">Deforestación</div>
                                        <div class="text-sm text-green-600">Tala ilegal</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-green-300 rounded-full opacity-20"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-emerald-300 rounded-full opacity-20"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section id="como-funciona" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Cómo Funciona EcoReport?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Reportar incidencias ambientales nunca fue tan fácil. Sigue estos simples pasos 
                    y contribuye a proteger nuestro medio ambiente.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center card-hover bg-gray-50 p-8 rounded-2xl">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detecta el Problema</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Identifica incidencias ambientales o de obras irregulares en tu comunidad. 
                        Cada reporte cuenta para proteger nuestro entorno.
                    </p>
                </div>
                
                <div class="text-center card-hover bg-gray-50 p-8 rounded-2xl">
                    <div class="w-16 h-16 bg-gradient-to-r from-eco-green to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Reporta Fácilmente</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Completa nuestro formulario intuitivo, adjunta fotos y describe la situación. 
                        Tu reporte será procesado inmediatamente.
                    </p>
                </div>
                
                <div class="text-center card-hover bg-gray-50 p-8 rounded-2xl">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Seguimiento en Tiempo Real</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Recibe actualizaciones del progreso de tu denuncia y mantente informado 
                        sobre las acciones tomadas por las autoridades.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="reportar" class="py-20 bg-gradient-to-r from-red-500 to-red-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-4xl font-bold text-white mb-6">
                    ¡Actúa Ahora! Cada Reporte Marca la Diferencia
                </h2>
                <p class="text-xl text-red-100 mb-8 leading-relaxed">
                    No esperes más. Si has visto algo que afecta al medio ambiente o una obra irregular, 
                    reporta ahora y ayuda a construir un futuro más sostenible para todos.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="bg-white text-red-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105">
                        📱 Reportar Incidencia
                    </button>
                    <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-red-600 transition-all duration-300">
                        📋 Ver Todos los Reportes
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 py-12">
                <div class="col-span-1">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-eco-green to-emerald-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">EcoReport</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Plataforma ciudadana para reportar incidencias ambientales y construir un futuro sostenible.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.748.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.754-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Plataforma</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Cómo funciona</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Reportar incidencia</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Ver reportes</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Estadísticas</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Soporte</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Centro de ayuda</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Preguntas frecuentes</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Términos de uso</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            info@ecoreport.com
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            +1 (555) 123-4567
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Quito, Ecuador
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 pb-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © 2025 EcoReport. Todos los derechos reservados.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Política de Privacidad</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Términos de Servicio</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.md\\:hidden button');
        const mobileMenu = document.createElement('div');
        mobileMenu.className = 'md:hidden bg-white shadow-lg';
        mobileMenu.innerHTML = `
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#inicio" class="block px-3 py-2 text-gray-700 hover:text-eco-green font-medium">Inicio</a>
                <a href="#como-funciona" class="block px-3 py-2 text-gray-700 hover:text-eco-green font-medium">¿Cómo funciona?</a>
                <a href="#reportar" class="block px-3 py-2 text-gray-700 hover:text-eco-green font-medium">Reportar</a>
                <a href="#contacto" class="block px-3 py-2 text-gray-700 hover:text-eco-green font-medium">Contacto</a>
            </div>
        `;
        mobileMenu.style.display = 'none';
        
        mobileMenuButton.addEventListener('click', function() {
            if (mobileMenu.style.display === 'none') {
                mobileMenu.style.display = 'block';
                mobileMenuButton.querySelector('path').setAttribute('d', 'M6 18L18 6M6 6l12 12');
            } else {
                mobileMenu.style.display = 'none';
                mobileMenuButton.querySelector('path').setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            }
        });
        
        document.querySelector('header').appendChild(mobileMenu);

        // Counter animation on scroll
        function animateCounters() {
            const counters = document.querySelectorAll('.text-3xl.font-bold.text-green-300');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.textContent.replace(/[^0-9]/g, ''));
                        let current = 0;
                        const increment = target / 100;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                entry.target.textContent = target.toLocaleString();
                                clearInterval(timer);
                            } else {
                                entry.target.textContent = Math.floor(current).toLocaleString();
                            }
                        }, 20);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            counters.forEach(counter => observer.observe(counter));
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            animateCounters();
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.classList.add('bg-white/95', 'backdrop-blur-sm');
            } else {
                header.classList.remove('bg-white/95', 'backdrop-blur-sm');
            }
        });

        // Button click handlers
        document.querySelectorAll('button').forEach(button => {
            if (button.textContent.includes('Reportar')) {
                button.addEventListener('click', function() {
                    alert('¡Funcionalidad de reporte próximamente! Gracias por tu interés en proteger el medio ambiente.');
                });
            }
        });
    </script>
</body>
</html>
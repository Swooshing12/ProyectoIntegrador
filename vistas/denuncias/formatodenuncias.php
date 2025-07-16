<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Formulario de Denuncias</title>
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
    </style>
</head>
<body class="bg-gray-200">
    <!-- Header -->
    <header class="bg-white shadow-lg">
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
                    <a href="#" class="text-gray-700 hover:text-eco-green font-medium transition-colors">Volver al Inicio</a>
                   
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Formulario de Denuncias</h1>
                <p class="text-gray-600">FOR - ECO - GDI - GDD – 001 – 001</p>
                <p class="text-sm text-gray-500 mt-4">
                    Completa todos los campos marcados con asterisco (*) para procesar tu denuncia ambiental
                </p>
            </div>
        </div>

        <form id="reportForm" class="space-y-6">
            <!-- 1. Datos del Denunciante -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3">1</span>
                    Datos del Denunciante
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Identificación *
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                            <option value="">Seleccione...</option>
                            <option value="cedula">Cédula de Ciudadanía</option>
                            <option value="pasaporte">Pasaporte</option>
                            <option value="extranjeria">Cédula de Extranjería</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Identificación *
                        </label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Emisión *
                        </label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Número Telefónico *
                        </label>
                        <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico *
                        </label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección
                        </label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green">
                    </div>
                </div>
            </div>

            <!-- 2. Datos Adicionales -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3">2</span>
                    Datos Adicionales
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Género *
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                            <option value="">Seleccione...</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                            <option value="otro">Otro</option>
                            <option value="no-especifica">Prefiero no especificar</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Etnia *
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                            <option value="">Seleccione...</option>
                            <option value="mestizo">Mestizo</option>
                            <option value="indigena">Indígena</option>
                            <option value="afroecuatoriano">Afroecuatoriano</option>
                            <option value="montubio">Montubio</option>
                            <option value="blanco">Blanco</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Edad *
                        </label>
                        <input type="number" min="18" max="120" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ¿Pertenece a algún grupo de atención prioritaria? *
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="atencion_prioritaria" value="si" class="text-eco-green focus:ring-eco-green border-gray-300" required>
                                <span class="ml-2">SÍ</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="atencion_prioritaria" value="no" class="text-eco-green focus:ring-eco-green border-gray-300" required>
                                <span class="ml-2">NO</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Denuncia -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3">3</span>
                    Denuncia
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Narración de los hechos *
                    </label>
                    <textarea rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                              placeholder="Describa detalladamente los hechos que desea denunciar, incluyendo fecha, hora, lugar y cualquier información relevante sobre la incidencia ambiental..." required></textarea>
                    <p class="text-sm text-gray-500 mt-1">Mínimo 50 caracteres</p>
                </div>
            </div>

            <!-- 4. Datos del Denunciado -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3">4</span>
                    Datos del Denunciado
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Servidor Municipal
                        </label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Nombre del servidor municipal involucrado (si aplica)">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Entidad Municipal
                        </label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Nombre de la entidad municipal involucrada (si aplica)">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Información Adicional
                        </label>
                        <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                  placeholder="Información adicional sobre personas, empresas o entidades involucradas en la incidencia ambiental..."></textarea>
                    </div>
                </div>
            </div>

            <!-- 5. Documentación Adjunta -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3">5</span>
                    Documentación Adjunta
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cargar archivo (documento, foto, video o archivo comprimido)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="mt-4">
                                <label class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Haz clic para subir o arrastra y suelta
                                    </span>
                                    <input type="file" class="hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.avi,.zip,.rar">
                                </label>
                                <p class="mt-1 text-sm text-gray-500">
                                    PDF, DOC, JPG, PNG, MP4, ZIP hasta 10MB
                                </p>
                            </div>
                        </div>
                        <div id="fileList" class="mt-2 space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Privacidad y Consentimiento -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Privacidad y Consentimiento</h2>
                
                <div class="space-y-4">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" class="mt-1 text-eco-green focus:ring-eco-green border-gray-300 rounded">
                        <span class="text-sm text-gray-700">
                            Deseo mantener la reserva de mi identidad.
                        </span>
                    </label>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Política de Privacidad</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <strong>EcoReport</strong> garantiza que sus datos personales serán utilizados exclusivamente para la gestión de su denuncia, 
                            bajo estrictas medidas de seguridad y confidencialidad, sus derechos respecto al manejo de su información serán respetados 
                            conforme a la normativa vigente en materia de protección de datos personales. La información proporcionada no será compartida 
                            con terceros sin su consentimiento, salvo en los casos previstos por la Ley. Al aceptar, usted autoriza el uso de sus datos 
                            únicamente para fines relacionados con la investigación de su denuncia. Para mayor información, puede contactarnos al correo 
                            electrónico <a href="mailto:protecciondedatos@ecoreport.com" class="text-eco-green hover:underline">protecciondedatos@ecoreport.com</a> 
                            o acudir a nuestras oficinas.
                        </p>
                    </div>
                    
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" class="mt-1 text-eco-green focus:ring-eco-green border-gray-300 rounded" required>
                        <span class="text-sm text-gray-700">
                            Acepto la política de privacidad y autorizo el tratamiento de mis datos personales. *
                        </span>
                    </label>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <button type="button" class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                        Guardar Borrador
                    </button>
                    <button type="submit" class="px-6 py-3 bg-eco-green text-white rounded-lg font-medium hover:bg-eco-dark transition-colors">
                        Enviar Denuncia
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        // File upload handling
        const fileInput = document.querySelector('input[type="file"]');
        const fileList = document.getElementById('fileList');
        
        fileInput.addEventListener('change', function(e) {
            fileList.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center justify-between bg-gray-50 p-2 rounded';
                fileDiv.innerHTML = `
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm">Eliminar</button>
                `;
                fileList.appendChild(fileDiv);
            });
        });

        // Form submission
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const narration = document.querySelector('textarea[placeholder*="Describa detalladamente"]').value;
            if (narration.length < 50) {
                alert('La narración de los hechos debe tener al menos 50 caracteres.');
                return;
            }
            
            // Success message
            alert('¡Denuncia enviada exitosamente! Se le enviará un correo de confirmación con el número de seguimiento.');
            
            // Reset form
            this.reset();
            fileList.innerHTML = '';
        });

        // Save draft functionality
        document.querySelector('button[type="button"]:nth-child(2)').addEventListener('click', function() {
            alert('Borrador guardado exitosamente. Puede continuar editando más tarde.');
        });
    </script>
</body>
</html>
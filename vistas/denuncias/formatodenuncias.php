<?php
// Si se accede desde el controlador, mostrar la vista directamente
// Si no, redirigir al controlador
if (!isset($categorias)) {
    require_once __DIR__ . '/../../controladores/FormatoDenunciasControlador/FormatoDenunciasController.php';
    $controller = new FormatoDenunciasController();
    $controller->index();
    return;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoReport - Formulario de Denuncias</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Formulario oficial para reportar denuncias ambientales y obras públicas">
    <meta name="keywords" content="denuncia, ambiente, obras públicas, Ecuador, formulario">
    
    <!-- External Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind Config -->
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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../estilos/denuncias/formatodenuncias.css">
</head>
<body class="bg-gray-50">
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen hidden">
        <div class="loading-content">
            <div class="eco-spinner">🌿</div>
            <div class="loading-text">Procesando tu denuncia...</div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-eco-green to-emerald-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-2xl font-bold text-gray-900">EcoReport</span>
                        <p class="text-xs text-gray-500">Sistema de Denuncias Ambientales</p>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <a href="../../index.php" class="text-gray-700 hover:text-eco-green font-medium transition-colors">
                        <i class="bi bi-house-fill mr-1"></i> Inicio
                    </a>
                    <a href="#ayuda" class="text-gray-700 hover:text-eco-green font-medium transition-colors">
                        <i class="bi bi-question-circle-fill mr-1"></i> Ayuda
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Progress Bar -->
    <div class="bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progreso del formulario</span>
                <span class="text-sm text-gray-500" id="progressPercent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-eco-green h-2 rounded-full transition-all duration-300" id="progressBar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Formulario de Denuncias Ambientales</h1>
                <p class="text-gray-600 font-medium">FOR - ECO - GDI - GDD – 001 – 001</p>
                <div class="mt-4 p-4 bg-eco-light rounded-lg">
                    <p class="text-sm text-eco-dark">
                        <i class="bi bi-info-circle-fill mr-2"></i>
                        Completa todos los campos marcados con asterisco (*) para procesar tu denuncia ambiental.
                        El proceso toma aproximadamente 5-10 minutos.
                    </p>
                </div>
            </div>
        </div>

        <!-- Error Display -->
        <div id="errorContainer" class="hidden mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Errores en el formulario</h3>
                        <div class="mt-2 text-sm text-red-700" id="errorList"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form id="denunciaForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="crear">
            
            <!-- 1. Datos del Denunciante -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="1">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                    Datos del Denunciante
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-card-text text-eco-green mr-1"></i> Cédula de Identidad *
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" name="cedula_denunciante" id="cedula_denunciante" 
                                class="form-input flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                placeholder="Ej: 1234567890" maxlength="10" pattern="[0-9]{10}" required>
                            <button type="button" id="btnBuscarCedula" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors font-medium whitespace-nowrap"
                                    title="Buscar datos en Registro Civil">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">10 dígitos numéricos - El botón "Buscar" completa automáticamente los datos</p>
                    </div>
                                        
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-person-fill text-eco-green mr-1"></i> Nombres *
                        </label>
                        <input type="text" name="nombres_denunciante" id="nombres_denunciante"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Nombres completos" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-person-fill text-eco-green mr-1"></i> Apellidos *
                        </label>
                        <input type="text" name="apellidos_denunciante" id="apellidos_denunciante"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Apellidos completos" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-telephone-fill text-eco-green mr-1"></i> Teléfono *
                        </label>
                        <input type="tel" name="telefono_denunciante" id="telefono_denunciante"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Ej: 0987654321" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-envelope-fill text-eco-green mr-1"></i> Correo Electrónico *
                        </label>
                        <input type="email" name="correo_denunciante" id="correo_denunciante"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="correo@ejemplo.com" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-geo-alt-fill text-eco-green mr-1"></i> Dirección de Domicilio
                        </label>
                        <input type="text" name="direccion_denunciante" id="direccion_denunciante"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Dirección completa (opcional)">
                    </div>
                </div>
            </div>

            <!-- 2. Ubicación del Incidente -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="2">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                    Ubicación del Incidente
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-map-fill text-eco-green mr-1"></i> Provincia *
                        </label>
                        <select name="provincia" id="provincia" class="form-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                            <option value="">Seleccione una provincia</option>
                            <option value="Pichincha">Pichincha</option>
                            <option value="Guayas">Guayas</option>
                            <option value="Azuay">Azuay</option>
                            <option value="Manabí">Manabí</option>
                            <!-- Agregar más provincias según sea necesario -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-building text-eco-green mr-1"></i> Cantón *
                        </label>
                        <select name="canton" id="canton" class="form-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                            <option value="">Seleccione un cantón</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-house text-eco-green mr-1"></i> Parroquia
                        </label>
                        <input type="text" name="parroquia" id="parroquia"
                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                               placeholder="Nombre de la parroquia">
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-geo text-eco-green mr-1"></i> Dirección Específica
                        </label>
                        <textarea name="direccion_especifica" id="direccion_especifica" rows="3"
                                  class="form-textarea w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                  placeholder="Describa la ubicación específica del incidente (calles, referencias, coordenadas, etc.)"></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Detalles de la Denuncia -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="3">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-eco-green text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                    Detalles de la Denuncia
                </h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-tags-fill text-eco-green mr-1"></i> Categoría de la Denuncia *
                            </label>
                            <select name="id_categoria" id="id_categoria" class="form-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>" data-tipo="<?= $categoria['tipo_principal'] ?>">
                                        <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-exclamation-triangle text-eco-green mr-1"></i> Nivel de Gravedad *
                            </label>
                            <select name="gravedad" id="gravedad" class="form-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" required>
                                <option value="">Seleccione el nivel</option>
                                <option value="BAJA">🟢 Baja - Impacto mínimo</option>
                                <option value="MEDIA">🟡 Media - Impacto moderado</option>
                                <option value="ALTA">🟠 Alta - Impacto significativo</option>
                                <option value="CRITICA">🔴 Crítica - Impacto severo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-calendar-event text-eco-green mr-1"></i> Fecha de Ocurrencia
                            </label>
                            <input type="date" name="fecha_ocurrencia" id="fecha_ocurrencia"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                   max="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-clock text-eco-green mr-1"></i> Requiere Atención Prioritaria? *
                            </label>
                            <div class="flex space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input type="radio" name="atencion_prioritaria" value="si" class="text-eco-green focus:ring-eco-green border-gray-300" required>
                                    <span class="ml-2">🚨 SÍ</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="atencion_prioritaria" value="no" class="text-eco-green focus:ring-eco-green border-gray-300" required>
                                    <span class="ml-2">⏳ NO</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-file-text text-eco-green mr-1"></i> Narración de los Hechos *
                        </label>
                        <textarea name="narracion_hechos" id="narracion_hechos" rows="6"
                                  class="form-textarea w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                  placeholder="Describa detalladamente los hechos que desea denunciar, incluyendo fecha, hora, lugar y cualquier información relevante sobre la incidencia ambiental..." 
                                  minlength="50" required></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-xs text-gray-500">Mínimo 50 caracteres</p>
                            <span id="charCount" class="text-xs text-gray-400">0/50</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Datos del Denunciado (Opcional) -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-gray-400 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">4</span>
                    Datos del Denunciado <span class="text-sm font-normal text-gray-500">(Opcional)</span>
                </h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-person-badge text-gray-600 mr-1"></i> Servidor Municipal
                            </label>
                            <input type="text" name="servidor_municipal" id="servidor_municipal"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                   placeholder="Nombre del servidor municipal involucrado">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-building text-gray-600 mr-1"></i> Entidad Municipal
                            </label>
                            <input type="text" name="entidad_municipal" id="entidad_municipal"
                                   class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                   placeholder="Nombre de la entidad municipal">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-info-circle text-gray-600 mr-1"></i> Información Adicional del Denunciado
                        </label>
                        <textarea name="informacion_adicional" id="informacion_adicional" rows="4"
                                  class="form-textarea w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-eco-green focus:border-eco-green" 
                                  placeholder="Información adicional sobre personas, empresas o entidades involucradas..."></textarea>
                    </div>
                </div>
            </div>

            <!-- 5. Documentación Adjunta -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="5">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">5</span>
                    Documentación Adjunta <span class="text-sm font-normal text-gray-500">(Opcional)</span>
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-cloud-upload text-blue-500 mr-1"></i> Cargar Evidencias
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-eco-green transition-colors" id="dropZone">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="mt-4">
                            <label class="cursor-pointer">
                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                    🔽 Haz clic para subir o arrastra archivos aquí
                                </span>
                                <input type="file" name="evidencias[]" id="evidencias" class="hidden" multiple 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.avi,.zip,.rar">
                            </label>
                            <p class="mt-1 text-xs text-gray-500">
                                📁 Formatos: PDF, DOC, JPG, PNG, MP4, ZIP | 📏 Máximo: 10MB por archivo
                            </p>
                        </div>
                    </div>
                    <div id="fileList" class="mt-4 space-y-2"></div>
                </div>
            </div>

            <!-- 6. Privacidad y Consentimiento -->
            <div class="bg-white rounded-lg shadow-sm p-6 form-section" data-section="6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">6</span>
                    Privacidad y Consentimiento
                </h2>
                
                <div class="space-y-4">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" name="reserva_identidad" id="reserva_identidad" 
                               class="mt-1 text-eco-green focus:ring-eco-green border-gray-300 rounded">
                        <span class="text-sm text-gray-700">
                            🔒 Deseo mantener la reserva de mi identidad.
                        </span>
                    </label>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">📋 Política de Privacidad</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            <strong>EcoReport</strong> garantiza que sus datos personales serán utilizados exclusivamente para la gestión de su denuncia, 
                            bajo estrictas medidas de seguridad y confidencialidad. Sus derechos respecto al manejo de su información serán respetados 
                            conforme a la normativa vigente en materia de protección de datos personales.
                        </p>
                    </div>
                    
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" name="acepta_politica" id="acepta_politica" 
                               class="mt-1 text-eco-green focus:ring-eco-green border-gray-300 rounded" required>
                        <span class="text-sm text-gray-700">
                            ✅ Acepto la política de privacidad y autorizo el tratamiento de mis datos personales. *
                        </span>
                    </label>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="bg-white rounded-lg shadow-sm p-6 sticky bottom-0">
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <button type="button" id="btnCancelar" class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="bi bi-x-circle mr-1"></i> Cancelar
                    </button>
                    <button type="button" id="btnBorrador" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                        <i class="bi bi-save mr-1"></i> Guardar Borrador
                    </button>
                    <button type="submit" id="btnEnviar" class="px-6 py-3 bg-eco-green text-white rounded-lg font-medium hover:bg-eco-dark transition-colors">
                        <i class="bi bi-send mr-1"></i> Enviar Denuncia
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <p class="text-gray-300">
               <strong>EcoReport</strong> - Sistema de Denuncias Ambientales y Obras Públicas
           </p>
           <p class="text-gray-400 text-sm mt-2">
               © <?= date('Y') ?> Todos los derechos reservados | 
               <a href="#" class="text-eco-green hover:underline">Términos de Uso</a> | 
               <a href="#" class="text-eco-green hover:underline">Política de Privacidad</a>
           </p>
       </div>
   </footer>

   <!-- Scripts -->
   <script src="../../js/formatodenuncias.js"></script>
   <script>
       // Configuración global para el formulario
       window.EcoReportConfig = {
           maxFileSize: 10 * 1024 * 1024, // 10MB
           allowedFileTypes: ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'mp4', 'avi', 'zip', 'rar'],
           minDescriptionLength: 50,
           controllerUrl: '../../controladores/FormatoDenunciasControlador/FormatoDenunciasController.php'
       };
   </script>
</body>
</html>
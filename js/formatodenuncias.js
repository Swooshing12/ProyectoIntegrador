// ===================================================
// ECOREPORT - FORMATODENUNCIAS JS
// Sistema de Denuncias Ambientales
// ===================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🌿 EcoReport - Formulario de Denuncias iniciado');
    
    // Configuración
    const config = window.EcoReportConfig || {};
    const maxFileSize = config.maxFileSize || 10 * 1024 * 1024; // 10MB
    const allowedTypes = config.allowedFileTypes || ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'mp4', 'avi', 'zip', 'rar'];
    const minDescLength = config.minDescriptionLength || 50;
    
    // Elementos del DOM
    const form = document.getElementById('denunciaForm');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const errorContainer = document.getElementById('errorContainer');
    const loadingScreen = document.getElementById('loadingScreen');
    
    // Archivos subidos
    let uploadedFiles = [];
    
    // ===== INICIALIZACIÓN ===== 
    init();
    
    function init() {
        setupFormValidation();
        setupFileUpload();
        setupProgressTracking();
        setupEventListeners();
        setupLocationDependencies();
        setupCharacterCounter();
        setupCedulaSearch();  // ✅ AÑADIR ESTA LÍNEA
        loadSavedDraft();
    }
    
    // ===== VALIDACIÓN DEL FORMULARIO =====
    function setupFormValidation() {
        // Validación de cédula
        const cedulaInput = document.getElementById('cedula_denunciante');
        cedulaInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            validateCedula(this.value);
        });
        
        // Validación de email
        const emailInput = document.getElementById('correo_denunciante');
        emailInput.addEventListener('blur', function() {
            validateEmail(this.value);
        });
        
        // Validación en tiempo real
        const requiredInputs = form.querySelectorAll('[required]');
        requiredInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    }
    function showAlert(type, message) {
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };
        
        Swal.fire({
            icon: icons[type],
            title: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    function showErrors(errors) {
        errorContainer.classList.remove('hidden');
        document.getElementById('errorList').innerHTML = errors.map(error => `<div>• ${error}</div>`).join('');
        errorContainer.scrollIntoView({ behavior: 'smooth' });
    }
    
    function hideErrors() {
        errorContainer.classList.add('hidden');
    }
    
    function validateCedula(cedula) {
        const cedulaInput = document.getElementById('cedula_denunciante');
        
        if (cedula.length !== 10) {
            showFieldError(cedulaInput, 'La cédula debe tener exactamente 10 dígitos');
            return false;
        }
        
        // Validación algoritmo de cédula ecuatoriana
        if (!isValidEcuadorianCedula(cedula)) {
            showFieldError(cedulaInput, 'Número de cédula no válido');
            return false;
        }
        
        showFieldSuccess(cedulaInput);
        return true;
    }
    
    function isValidEcuadorianCedula(cedula) {
        if (cedula.length !== 10) return false;
        
        const province = parseInt(cedula.substring(0, 2));
        if (province < 1 || province > 24) return false;
        
        const digits = cedula.split('').map(Number);
        const verifier = digits[9];
        
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            let digit = digits[i];
            if (i % 2 === 0) {
                digit *= 2;
                if (digit > 9) digit -= 9;
            }
            sum += digit;
        }
        
        const calculatedVerifier = sum % 10 === 0 ? 0 : 10 - (sum % 10);
        return verifier === calculatedVerifier;
    }
    
    function validateEmail(email) {
        const emailInput = document.getElementById('correo_denunciante');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            showFieldError(emailInput, 'Formato de correo electrónico no válido');
            return false;
        }
        
        showFieldSuccess(emailInput);
        return true;
    }
    
    function validateField(field) {
        const value = field.value.trim();
        
        if (field.hasAttribute('required') && !value) {
            showFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        
        if (field.type === 'email' && value) {
            return validateEmail(value);
        }
        
        if (field.name === 'cedula_denunciante' && value) {
            return validateCedula(value);
        }
        
        if (field.name === 'narracion_hechos' && value.length < minDescLength) {
            showFieldError(field, `Mínimo ${minDescLength} caracteres requeridos`);
            return false;
        }
        
        showFieldSuccess(field);
        return true;
    }
    
    function showFieldError(field, message) {
        field.classList.remove('border-green-300');
        field.classList.add('border-red-300');
        
        // Remover mensaje anterior
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        // Agregar nuevo mensaje
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    
    function showFieldSuccess(field) {
        field.classList.remove('border-red-300');
        field.classList.add('border-green-300');
        
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) existingError.remove();
    }
    
    // ===== SUBIDA DE ARCHIVOS =====
    function setupFileUpload() {
        const fileInput = document.getElementById('evidencias');
        const dropZone = document.getElementById('dropZone');
        const fileList = document.getElementById('fileList');
        
        // Click para seleccionar archivos
        dropZone.addEventListener('click', function(e) {
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });
        
        // Drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = Array.from(e.dataTransfer.files);
            processFiles(files);
        });
        
        // Selección de archivos
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            processFiles(files);
        });
        
        function processFiles(files) {
            files.forEach(file => {
                if (validateFile(file)) {
                    addFileToList(file);
                }
            });
        }
        
        function validateFile(file) {
            // Validar tamaño
            if (file.size > maxFileSize) {
                showAlert('error', `El archivo "${file.name}" excede el tamaño máximo de 10MB`);
                return false;
            }
            
            // Validar tipo
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(extension)) {
                showAlert('error', `Tipo de archivo no permitido: ${extension}`);
                return false;
            }
            
            // Validar duplicados
            if (uploadedFiles.some(f => f.name === file.name && f.size === file.size)) {
                showAlert('warning', `El archivo "${file.name}" ya está agregado`);
                return false;
            }
            
            return true;
        }
        
                function addFileToList(file) {
                // ✅ VERIFICAR que no esté duplicado
                const isDuplicate = uploadedFiles.some(f => f.name === file.name && f.size === file.size);
                if (isDuplicate) {
                    showAlert('warning', `El archivo "${file.name}" ya está agregado`);
                    return;
                }
                
                uploadedFiles.push(file);
                
                // ✅ DEBUG
                console.log('📁 Archivo agregado:', file.name, 'Total archivos:', uploadedFiles.length);
                
                const fileDiv = document.createElement('div');
                fileDiv.className = 'file-item fade-in';
                fileDiv.innerHTML = `
                    <div class="file-icon">${getFileIcon(file.name)}</div>
                    <div class="file-info">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                    <button type="button" class="file-remove" onclick="removeFile('${file.name}', this)">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                `;
                
                document.getElementById('fileList').appendChild(fileDiv);
                updateProgress();
            }
    }
    
    window.removeFile = function(fileName, button) {
        // Remover del array
        uploadedFiles = uploadedFiles.filter(f => f.name !== fileName);
        
        // Remover del DOM
        button.parentElement.remove();
        
        updateProgress();
        showAlert('success', 'Archivo eliminado');
    };
    
    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        const icons = {
            pdf: '📄', doc: '📄', docx: '📄',
            jpg: '🖼️', jpeg: '🖼️', png: '🖼️',
            mp4: '🎥', avi: '🎥',
            zip: '📦', rar: '📦'
        };
        return icons[extension] || '📎';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // ===== SEGUIMIENTO DE PROGRESO =====
    function setupProgressTracking() {
        const sections = document.querySelectorAll('.form-section');
        
        sections.forEach((section, index) => {
            const inputs = section.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('input', updateProgress);
                input.addEventListener('change', updateProgress);
            });
        });
    }
    
    function updateProgress() {
        const totalFields = form.querySelectorAll('input[required], select[required], textarea[required]').length;
        const filledFields = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'))
            .filter(field => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    return field.checked;
                }
                return field.value.trim() !== '';
            }).length;
        
        const percentage = Math.round((filledFields / totalFields) * 100);
        
        progressBar.style.width = percentage + '%';
        progressPercent.textContent = percentage + '%';
        
        // Actualizar color del progreso
        if (percentage < 30) {
            progressBar.className = 'bg-red-500 h-2 rounded-full transition-all duration-300';
        } else if (percentage < 70) {
            progressBar.className = 'bg-yellow-500 h-2 rounded-full transition-all duration-300';
        } else {
            progressBar.className = 'bg-eco-green h-2 rounded-full transition-all duration-300';
        }
    }
    
    // ===== EVENTOS =====
    function setupEventListeners() {
        // Envío del formulario
        form.addEventListener('submit', handleFormSubmit);
        
        // Botones
        document.getElementById('btnCancelar').addEventListener('click', handleCancel);
        document.getElementById('btnBorrador').addEventListener('click', handleSaveDraft);
        
        // Validación en tiempo real
        form.addEventListener('input', function(e) {
            if (e.target.hasAttribute('required')) {
                validateField(e.target);
            }
        });
    }
    
    async function handleFormSubmit(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        showAlert('error', 'Por favor corrige los errores en el formulario');
        return;
    }
    
    try {
        showLoading(true);
        
        const formData = new FormData(form);
        
        // ✅ DEFINIR URL CORRECTA
        const controllerUrl = '../../controladores/FormatoDenunciasControlador/FormatoDenunciasController.php';
        
        // Agregar archivos
        uploadedFiles.forEach((file, index) => {
            formData.append(`evidencias[${index}]`, file);
        });
        
        // ✅ DEBUG: Mostrar datos que se envían
        console.log('📤 Enviando denuncia a:', controllerUrl);
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        
        const response = await fetch(controllerUrl, {
            method: 'POST',
            body: formData
        });
        
        // ✅ VERIFICAR SI LA RESPUESTA ES HTML (ERROR 404/500)
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const htmlText = await response.text();
            console.error('❌ Respuesta no es JSON:', htmlText.substring(0, 200));
            throw new Error('El servidor no está respondiendo correctamente. Verifica que el archivo del controlador existe.');
        }
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessMessage(result);
            clearSavedDraft();
            form.reset();
            uploadedFiles = [];
            document.getElementById('fileList').innerHTML = '';
        } else {
            showAlert('error', result.message || 'Error al enviar la denuncia');
        }
        
    } catch (error) {
        console.error('❌ Error completo:', error);
        showAlert('error', `Error: ${error.message}`);
    } finally {
        showLoading(false);
    }
}
    
    function validateForm() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        const errors = [];
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
                errors.push(`${field.name}: ${field.validationMessage || 'Campo requerido'}`);
            }
        });
        
        if (errors.length > 0) {
            showErrors(errors);
        } else {
            hideErrors();
        }
        
        return isValid;
    }
    
    function showSuccessMessage(result) {
        Swal.fire({
            icon: 'success',
            title: '¡Denuncia Enviada Exitosamente!',
            html: `
                <div class="text-center">
                    <div class="text-6xl mb-4">🌿</div>
                    <p class="text-lg mb-3">Tu denuncia ha sido registrada correctamente</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p class="font-bold text-green-800">Número de seguimiento:</p>
                        <p class="text-2xl font-mono text-green-600">${result.numero_denuncia}</p>
                    </div>
                    <p class="text-sm text-gray-600">
                        ${result.email_enviado ? 
                            '📧 Se ha enviado una confirmación a tu correo electrónico' : 
                            '⚠️ No se pudo enviar el correo de confirmación'
                        }
                    </p>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#16a34a',
            allowOutsideClick: false
        }).then(() => {
            window.location.href = '../../vistas/index.php';
        });
    }
    
    // ===== UTILIDADES =====
    function showLoading(show) {
        if (show) {
            loadingScreen.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            loadingScreen.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    
    
    // ===== DEPENDENCIAS DE UBICACIÓN =====
    function setupLocationDependencies() {
        const provinciaSelect = document.getElementById('provincia');
        const cantonSelect = document.getElementById('canton');
        
        const cantonesPorProvincia = {
            'Pichincha': ['Quito', 'Cayambe', 'Mejía', 'Pedro Moncayo', 'Rumiñahui', 'San Miguel de los Bancos'],
            'Guayas': ['Guayaquil', 'Durán', 'Samborondón', 'Daule', 'Milagro', 'Playas'],
            'Azuay': ['Cuenca', 'Gualaceo', 'Paute', 'Santa Isabel', 'Sigsig'],
            'Manabí': ['Manta', 'Portoviejo', 'Chone', 'Montecristi', 'Jipijapa']
        };
        
        provinciaSelect.addEventListener('change', function() {
            const provincia = this.value;
            cantonSelect.innerHTML = '<option value="">Seleccione un cantón</option>';
            
            if (provincia && cantonesPorProvincia[provincia]) {
                cantonesPorProvincia[provincia].forEach(canton => {
                    const option = document.createElement('option');
                    option.value = canton;
                    option.textContent = canton;
                    cantonSelect.appendChild(option);
                });
            }
        });
    }
    
    // ===== CONTADOR DE CARACTERES =====
    function setupCharacterCounter() {
        const narracionTextarea = document.getElementById('narracion_hechos');
        const charCount = document.getElementById('charCount');
        
        narracionTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/${minDescLength}`;
            
            if (length < minDescLength) {
                charCount.className = 'text-xs text-red-500';
            } else {
                charCount.className = 'text-xs text-green-500';
            }
        });
    }
    
    // ===== BORRADOR =====
    function handleSaveDraft() {
        const formData = new FormData(form);
        const draftData = {};
        
        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }
        
        localStorage.setItem('ecoreport_draft', JSON.stringify(draftData));
        showAlert('success', 'Borrador guardado exitosamente');
    }
    
    function loadSavedDraft() {
        const savedDraft = localStorage.getItem('ecoreport_draft');
        if (savedDraft) {
            try {
                const draftData = JSON.parse(savedDraft);
                
                Swal.fire({
                    title: 'Borrador encontrado',
                    text: '¿Deseas cargar el borrador guardado?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cargar',
                    cancelButtonText: 'No, empezar nuevo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadDraftData(draftData);
                        showAlert('success', 'Borrador cargado');
                    }
                });
            } catch (error) {
               console.error('Error cargando borrador:', error);
               localStorage.removeItem('ecoreport_draft');
           }
       }
   }
   
   function loadDraftData(draftData) {
       Object.keys(draftData).forEach(key => {
           const field = form.querySelector(`[name="${key}"]`);
           if (field) {
               if (field.type === 'checkbox' || field.type === 'radio') {
                   field.checked = draftData[key] === field.value;
               } else {
                   field.value = draftData[key];
               }
               
               // Trigger events para validación
               field.dispatchEvent(new Event('input', { bubbles: true }));
               field.dispatchEvent(new Event('change', { bubbles: true }));
           }
       });
       
       updateProgress();
   }
   
   function clearSavedDraft() {
       localStorage.removeItem('ecoreport_draft');
   }
   
   function handleCancel() {
       Swal.fire({
           title: '¿Estás seguro?',
           text: 'Se perderán todos los datos ingresados',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Sí, cancelar',
           cancelButtonText: 'Continuar editando',
           confirmButtonColor: '#dc2626'
       }).then((result) => {
           if (result.isConfirmed) {
               clearSavedDraft();
               window.location.href = '../../vistas/index.php';
           }
       });
   }
   
   // ===== SCROLL SUAVE ENTRE SECCIONES =====
   function scrollToSection(sectionNumber) {
       const section = document.querySelector(`[data-section="${sectionNumber}"]`);
       if (section) {
           section.scrollIntoView({ 
               behavior: 'smooth', 
               block: 'start' 
           });
       }
   }
   
   // ===== VALIDACIÓN AUTOMÁTICA AL SALIR DE SECCIONES =====
   function setupSectionValidation() {
       const sections = document.querySelectorAll('.form-section');
       
       sections.forEach(section => {
           const inputs = section.querySelectorAll('input, select, textarea');
           
           inputs.forEach(input => {
               input.addEventListener('blur', function() {
                   // Validar toda la sección cuando se sale de un campo
                   validateSection(section);
               });
           });
       });
   }
   
   function validateSection(section) {
       const sectionInputs = section.querySelectorAll('input, select, textarea');
       let sectionValid = true;
       
       sectionInputs.forEach(input => {
           if (!validateField(input)) {
               sectionValid = false;
           }
       });
       
       // Actualizar estado visual de la sección
       if (sectionValid) {
           section.classList.add('completed');
           section.classList.remove('error');
       } else {
           section.classList.add('error');
           section.classList.remove('completed');
       }
       
       return sectionValid;
   }
   
   // ===== AUTO-GUARDADO =====
   function setupAutoSave() {
       let autoSaveTimer;
       
       form.addEventListener('input', function() {
           clearTimeout(autoSaveTimer);
           autoSaveTimer = setTimeout(() => {
               handleSaveDraft();
               console.log('📝 Auto-guardado realizado');
           }, 30000); // Auto-guardar cada 30 segundos
       });
   }
   
   // ===== DETECCIÓN DE CAMBIOS NO GUARDADOS =====
   function setupUnsavedChangesWarning() {
       let hasUnsavedChanges = false;
       
       form.addEventListener('input', function() {
           hasUnsavedChanges = true;
       });
       
       form.addEventListener('submit', function() {
           hasUnsavedChanges = false;
       });
       
       window.addEventListener('beforeunload', function(e) {
           if (hasUnsavedChanges) {
               const message = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
               e.returnValue = message;
               return message;
           }
       });
   }
   
   // ===== MEJORAS DE ACCESIBILIDAD =====
   function setupAccessibility() {
       // Navegación con teclado
       form.addEventListener('keydown', function(e) {
           // Alt + S para guardar borrador
           if (e.altKey && e.key === 's') {
               e.preventDefault();
               handleSaveDraft();
           }
           
           // Alt + Enter para enviar (desde textarea)
           if (e.altKey && e.key === 'Enter' && e.target.tagName === 'TEXTAREA') {
               e.preventDefault();
               form.requestSubmit();
           }
       });
       
       // ARIA labels dinámicos
       const requiredFields = form.querySelectorAll('[required]');
       requiredFields.forEach(field => {
           field.setAttribute('aria-required', 'true');
           
           field.addEventListener('invalid', function() {
               this.setAttribute('aria-invalid', 'true');
           });
           
           field.addEventListener('input', function() {
               if (this.validity.valid) {
                   this.setAttribute('aria-invalid', 'false');
               }
           });
       });
   }
   
   // ===== TOOLTIPS INFORMATIVOS =====
   function setupTooltips() {
       const tooltipElements = [
           {
               selector: '#cedula_denunciante',
               message: 'Ingresa tu número de cédula ecuatoriana de 10 dígitos'
           },
           {
               selector: '#narracion_hechos',
               message: 'Describe los hechos de manera clara y detallada. Incluye fecha, hora y ubicación específica'
           },
           {
               selector: '#evidencias',
               message: 'Puedes subir fotos, videos, documentos o archivos comprimidos como evidencia'
           }
       ];
       
       tooltipElements.forEach(item => {
           const element = document.querySelector(item.selector);
           if (element) {
               element.setAttribute('title', item.message);
               element.classList.add('tooltip');
               element.setAttribute('data-tooltip', item.message);
           }
       });
   }
   
   // ===== ANÁLISIS DE FORMULARIO =====
   function trackFormAnalytics() {
       const startTime = Date.now();
       
       // Tracking de campos completados
       const fieldCompletionTimes = {};
       
       form.querySelectorAll('input, select, textarea').forEach(field => {
           field.addEventListener('focus', function() {
               fieldCompletionTimes[this.name] = Date.now();
           });
           
           field.addEventListener('blur', function() {
               if (fieldCompletionTimes[this.name]) {
                   const timeSpent = Date.now() - fieldCompletionTimes[this.name];
                   console.log(`⏱️ Campo ${this.name}: ${timeSpent}ms`);
               }
           });
       });
       
       // Tracking de envío
       form.addEventListener('submit', function() {
           const totalTime = Date.now() - startTime;
           console.log(`📊 Tiempo total de formulario: ${Math.round(totalTime / 1000)}s`);
           console.log(`📁 Archivos subidos: ${uploadedFiles.length}`);
       });
   }
   
   // ===== INICIALIZACIÓN COMPLETA =====
   function initializeAdvancedFeatures() {
       setupSectionValidation();
       setupAutoSave();
       setupUnsavedChangesWarning();
       setupAccessibility();
       setupTooltips();
       
       if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
           trackFormAnalytics();
       }
       
       console.log('✅ EcoReport - Formulario completamente inicializado');
   }
   
   // Inicializar características avanzadas después de un breve delay
   setTimeout(initializeAdvancedFeatures, 100);
   
   // ===== UTILIDADES GLOBALES =====
   window.EcoReportForm = {
       validateForm,
       showAlert,
       scrollToSection,
       updateProgress,
       handleSaveDraft: () => handleSaveDraft(),
       clearDraft: () => clearSavedDraft()
   };
});

// ===== FUNCIONES HELPER GLOBALES =====
function formatDate(date) {
   return new Intl.DateTimeFormat('es-EC', {
       year: 'numeric',
       month: '2-digit',
       day: '2-digit'
   }).format(new Date(date));
}

function sanitizeInput(input) {
   const temp = document.createElement('div');
   temp.textContent = input;
   return temp.innerHTML;
}

function debounce(func, wait) {
   let timeout;
   return function executedFunction(...args) {
       const later = () => {
           clearTimeout(timeout);
           func(...args);
       };
       clearTimeout(timeout);
       timeout = setTimeout(later, wait);
   };
}

// ===== SERVICE WORKER PARA FUNCIONALIDAD OFFLINE =====
if ('serviceWorker' in navigator) {
   window.addEventListener('load', function() {
       navigator.serviceWorker.register('/sw.js')
           .then(function(registration) {
               console.log('SW registrado exitosamente:', registration.scope);
           })
           .catch(function(registrationError) {
               console.log('SW falló al registrarse:', registrationError);
           });
   });

}

// ===== BÚSQUEDA POR CÉDULA =====
function setupCedulaSearch() {
    const btnBuscar = document.getElementById('btnBuscarCedula');
    const cedulaInput = document.getElementById('cedula_denunciante');
    
    if (btnBuscar && cedulaInput) {
        btnBuscar.addEventListener('click', buscarPorCedula);
        
        // También buscar al presionar Enter en el campo de cédula
        cedulaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 10) {
                e.preventDefault();
                buscarPorCedula();
            }
        });
    }
}

function buscarPorCedula() {
    const cedulaInput = document.getElementById('cedula_denunciante');
    const cedula = cedulaInput.value.trim();
    
    if (!cedula) {
        showAlert('error', 'Por favor, ingresa una cédula');
        return;
    }
    
    if (cedula.length !== 10) {
        showAlert('error', 'La cédula debe tener exactamente 10 dígitos');
        return;
    }
    
    // Mostrar loading en el botón
    const btnBuscar = document.getElementById('btnBuscarCedula');
    const textoOriginal = btnBuscar.innerHTML;
    btnBuscar.innerHTML = '<i class="bi bi-arrow-clockwise animate-spin"></i> Buscando...';
    btnBuscar.disabled = true;
    
    // Realizar búsqueda
    fetch(`../../controladores/obtenerDatos.php?cedula=${cedula}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Restaurar botón
            btnBuscar.innerHTML = textoOriginal;
            btnBuscar.disabled = false;
            
            if (data.estado !== 'OK' || !data.resultado?.length) {
                showAlert('error', 'No se encontraron datos para la cédula ingresada');
                return;
            }
            
            const persona = data.resultado[0];
            const palabras = persona.nombre.split(' ');
            
            // ✅ LLENAR Y BLOQUEAR CAMPOS
            const nombresInput = document.getElementById('nombres_denunciante');
            const apellidosInput = document.getElementById('apellidos_denunciante');
            
            // Apellidos (primeras 2 palabras)
            if (apellidosInput) {
                apellidosInput.value = palabras.slice(0, 2).join(' ');
                apellidosInput.readOnly = true;
                apellidosInput.classList.add('bg-gray-100', 'text-gray-600');
            }
            
            // Nombres (palabras restantes)
            if (nombresInput) {
                nombresInput.value = palabras.slice(2).join(' ');
                nombresInput.readOnly = true;
                nombresInput.classList.add('bg-gray-100', 'text-gray-600');
            }
            
            // Cédula (bloquear para que no se modifique)
            cedulaInput.readOnly = true;
            cedulaInput.classList.add('bg-gray-100', 'text-gray-600');
            
            // ✅ NACIONALIDAD: Si es ciudadano ecuatoriano
            if (persona.condicionCiudadano && persona.condicionCiudadano.toUpperCase() === 'CIUDADANO') {
                // Crear un campo hidden para la nacionalidad
                let nacionalidadHidden = document.getElementById('nacionalidad_hidden');
                if (!nacionalidadHidden) {
                    nacionalidadHidden = document.createElement('input');
                    nacionalidadHidden.type = 'hidden';
                    nacionalidadHidden.id = 'nacionalidad_hidden';
                    nacionalidadHidden.name = 'nacionalidad_denunciante';
                    nacionalidadHidden.value = 'Ecuatoriana';
                    cedulaInput.parentNode.appendChild(nacionalidadHidden);
                }
                
                // Mostrar indicador visual de nacionalidad
                mostrarNacionalidadEcuatoriana();
            }
            
            // ✅ AGREGAR BOTÓN PARA RESETEAR
            agregarBotonResetear();
            
            // ✅ MOSTRAR MENSAJE DE ÉXITO
            showAlert('success', 'Datos completados automáticamente desde el Registro Civil');
            
            // Trigger validación y actualización de progreso
            [nombresInput, apellidosInput, cedulaInput].forEach(input => {
                if (input) {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            updateProgress();
            
        })
        .catch(error => {
            // Restaurar botón en caso de error
            btnBuscar.innerHTML = textoOriginal;
            btnBuscar.disabled = false;
            
            console.error('Error buscando cédula:', error);
            showAlert('error', 'No se pudieron obtener los datos. Intente nuevamente');
        });
}

// Función para el botón buscar cédula
document.getElementById('btnBuscarCedula').addEventListener('click', function() {
    const cedula = document.getElementById('cedula_denunciante').value.trim();
    
    if (!cedula || cedula.length !== 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Cédula Inválida',
            text: 'Ingresa una cédula válida de 10 dígitos',
            confirmButtonColor: '#16a34a'
        });
        return;
    }
    
    // Mostrar loading
    this.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Buscando...';
    this.disabled = true;
    
    // Simular búsqueda (aquí puedes implementar la validación real)
    setTimeout(() => {
        // Restaurar botón
        this.innerHTML = '<i class="bi bi-search"></i> Buscar';
        this.disabled = false;
        
        // Mostrar advertencia
        Swal.fire({
            icon: 'info',
            title: 'Importante',
            html: `
                <div style="text-align: left;">
                    <p><strong>Si ya tienes una cuenta registrada:</strong></p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>No podrás crear una nueva denuncia desde este formulario</li>
                        <li>Debes iniciar sesión con tu cuenta existente</li>
                        <li>Desde tu cuenta podrás crear nuevas denuncias</li>
                    </ul>
                    <p><strong>Si es tu primera denuncia:</strong></p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Se creará automáticamente tu cuenta</li>
                        <li>Recibirás credenciales por correo</li>
                    </ul>
                </div>
            `,
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Entendido'
        });
    }, 1000);
});

function mostrarNacionalidadEcuatoriana() {
    // Crear o actualizar indicador de nacionalidad
    let nacionalidadIndicator = document.getElementById('nacionalidad_indicator');
    if (!nacionalidadIndicator) {
        nacionalidadIndicator = document.createElement('div');
        nacionalidadIndicator.id = 'nacionalidad_indicator';
        nacionalidadIndicator.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded-md';
        nacionalidadIndicator.innerHTML = `
            <div class="flex items-center text-green-700">
                <i class="bi bi-flag-fill mr-2"></i>
                <span class="text-sm font-medium">Nacionalidad: Ecuatoriana (detectada automáticamente)</span>
            </div>
        `;
        
        // Insertar después del campo de cédula
        const cedulaContainer = document.getElementById('cedula_denunciante').parentNode;
        cedulaContainer.appendChild(nacionalidadIndicator);
    }
}

function agregarBotonResetear() {
    // Verificar si ya existe el botón
    if (document.getElementById('btnResetearDatos')) return;
    
    const btnBuscar = document.getElementById('btnBuscarCedula');
    const btnResetear = document.createElement('button');
    btnResetear.type = 'button';
    btnResetear.id = 'btnResetearDatos';
    btnResetear.className = 'px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors';
    btnResetear.title = 'Limpiar datos y permitir edición manual';
    btnResetear.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
    
    // Insertar después del botón de búsqueda
    btnBuscar.parentNode.insertBefore(btnResetear, btnBuscar.nextSibling);
    
    // Agregar evento click
    btnResetear.addEventListener('click', resetearCamposBusqueda);
}

function resetearCamposBusqueda() {
    // Desbloquear y limpiar campos
    const campos = ['cedula_denunciante', 'nombres_denunciante', 'apellidos_denunciante'];
    
    campos.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.readOnly = false;
            field.classList.remove('bg-gray-100', 'text-gray-600');
            field.value = '';
            
            // Trigger eventos para validación
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
    
    // Remover campo hidden de nacionalidad
    const nacionalidadHidden = document.getElementById('nacionalidad_hidden');
    if (nacionalidadHidden) {
        nacionalidadHidden.remove();
    }
    
    // Remover indicador de nacionalidad
    const nacionalidadIndicator = document.getElementById('nacionalidad_indicator');
    if (nacionalidadIndicator) {
        nacionalidadIndicator.remove();
    }
    
    // Remover botón de reseteo
    const btnResetear = document.getElementById('btnResetearDatos');
    if (btnResetear) {
        btnResetear.remove();
    }
    
    // Mensaje de confirmación
    showAlert('info', 'Campos desbloqueados. Ahora puedes ingresar los datos manualmente');
    
    // Enfocar en el campo de cédula
    document.getElementById('cedula_denunciante').focus();
    
    updateProgress();
}

// ===== VALIDACIÓN ESPECÍFICA PARA CÉDULA ECUATORIANA =====
function isValidEcuadorianCedula(cedula) {
    if (cedula.length !== 10) return false;
    
    const province = parseInt(cedula.substring(0, 2));
    if (province < 1 || province > 24) return false;
    
    const digits = cedula.split('').map(Number);
    const verifier = digits[9];
    
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        let digit = digits[i];
        if (i % 2 === 0) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
    }
    
    const calculatedVerifier = sum % 10 === 0 ? 0 : 10 - (sum % 10);
    return verifier === calculatedVerifier;
}

// ===== DETECCIÓN DE CONEXIÓN =====
window.addEventListener('online', function() {
   console.log('🌐 Conexión restaurada');
   document.body.classList.remove('offline');
});

window.addEventListener('offline', function() {
   console.log('📴 Sin conexión');
   document.body.classList.add('offline');
   
   // Mostrar mensaje de offline
   const offlineMessage = document.createElement('div');
   offlineMessage.id = 'offline-message';
   offlineMessage.className = 'fixed top-0 left-0 right-0 bg-red-500 text-white text-center py-2 z-50';
   offlineMessage.innerHTML = '📴 Sin conexión a internet. Los datos se guardarán localmente.';
   document.body.prepend(offlineMessage);
});

console.log('🌿 EcoReport FormFormato Denuncias - JavaScript cargado completamente');
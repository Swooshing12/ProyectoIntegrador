// js/consulta_estado.js

/**
 * CONSULTA ESTADO DENUNCIA - JAVASCRIPT MEJORADO
 * Sistema de consulta de estado de denuncias EcoReport
 * Con previsualización de evidencias y interfaz mejorada
 */

class ConsultaEstado {
    constructor() {
        this.form = document.getElementById('formConsulta');
        this.inputNumero = document.getElementById('numero_denuncia');
        this.btnBuscar = document.getElementById('btnBuscar');
        this.loading = document.getElementById('loading');
        this.resultados = document.getElementById('resultados');
        this.error = document.getElementById('error');
        this.errorMessage = document.getElementById('errorMessage');
        
        // Modal para evidencias
        this.evidenciaModal = new bootstrap.Modal(document.getElementById('evidenciaModal'));
        
        this.init();
    }
    
    init() {
        // Event listeners
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.inputNumero.addEventListener('input', (e) => this.formatearNumero(e));
        this.inputNumero.addEventListener('keypress', (e) => this.validarTecla(e));
        
        // Focus inicial
        // Focus inicial
       this.inputNumero.focus();
       
       // Auto-limpiar errores cuando el usuario empiece a escribir
       this.inputNumero.addEventListener('input', () => {
           this.ocultarError();
       });
       
       console.log('💡 ConsultaEstado inicializado con previsualización');
   }
   
   /**
    * Manejar envío del formulario
    */
   async handleSubmit(e) {
       e.preventDefault();
       
       const numero = this.inputNumero.value.trim();
       
       // Validar formato
       if (!this.validarFormato(numero)) {
           this.mostrarError('El formato del número de denuncia debe ser: ECO-YYYY-MM-XXXXXX');
           this.inputNumero.focus();
           return;
       }
       
       await this.buscarDenuncia(numero);
   }
   
   /**
    * Buscar denuncia
    */
   async buscarDenuncia(numero) {
       try {
           this.mostrarLoading(true);
           this.ocultarError();
           this.ocultarResultados();
           
           const formData = new FormData();
           formData.append('numero_denuncia', numero);
           
           const response = await fetch('../../controladores/ConsultaEstado/ConsultaEstadoController.php?action=buscar', {
               method: 'POST',
               body: formData
           });
           
           if (!response.ok) {
               throw new Error(`HTTP ${response.status}: ${response.statusText}`);
           }
           
           const data = await response.json();
           
           if (data.success) {
               this.mostrarResultados(data.data);
               this.animarResultados();
           } else {
               this.mostrarError(data.message);
           }
           
       } catch (error) {
           console.error('Error buscando denuncia:', error);
           this.mostrarError('Error de conexión. Por favor verifica tu conexión a internet e intenta nuevamente.');
       } finally {
           this.mostrarLoading(false);
       }
   }
   
   /**
    * Mostrar resultados con animaciones
    */
   mostrarResultados(data) {
       const { denuncia, seguimiento, evidencias, permisos } = data;
       
       const html = `
           <div class="result-card fade-in-up">
               ${this.generarHeaderDenuncia(denuncia)}
               ${this.generarInfoGeneral(denuncia)}
               ${this.generarSeguimiento(seguimiento)}
               ${this.generarEvidencias(evidencias, permisos)}
               ${this.generarAcciones(denuncia)}
           </div>
       `;
       
       this.resultados.innerHTML = html;
       this.resultados.classList.remove('d-none');
       
       // Configurar eventos para evidencias
       this.configurarEventosEvidencias();
       
       // Scroll suave a resultados
       setTimeout(() => {
           this.resultados.scrollIntoView({ 
               behavior: 'smooth', 
               block: 'start',
               inline: 'nearest'
           });
       }, 100);
   }
   
   /**
    * Generar header de denuncia
    */
   generarHeaderDenuncia(denuncia) {
       const estadoClass = this.obtenerClaseEstado(denuncia.nombre_estado);
       const iconoGravedad = this.generarIconoGravedad(denuncia.gravedad);
       
       return `
           <div class="result-header">
               <div class="d-flex justify-content-between align-items-start flex-wrap">
                   <div class="flex-grow-1">
                       <div class="d-flex align-items-center mb-3">
                           <i class="bi bi-file-earmark-text me-3 fs-3"></i>
                           <div>
                               <h3 class="mb-1 text-white">${denuncia.numero_denuncia}</h3>
                               <p class="mb-0 opacity-75">${denuncia.titulo}</p>
                           </div>
                       </div>
                       <div class="denuncia-meta d-flex flex-wrap gap-3">
                           <div class="meta-item">
                               <i class="bi bi-calendar3 me-1"></i>
                               <strong>Creada:</strong> ${this.formatearFecha(denuncia.fecha_creacion)}
                           </div>
                           <div class="meta-item">
                               <i class="bi bi-geo-alt me-1"></i>
                               <strong>Ubicación:</strong> ${denuncia.provincia}, ${denuncia.canton}
                           </div>
                       </div>
                   </div>
                   <div class="text-end">
                       <div class="status-badge ${estadoClass} mb-3">
                           <i class="bi bi-circle-fill me-2" style="color: ${denuncia.estado_color}; font-size: 0.8rem;"></i>
                           ${denuncia.nombre_estado}
                       </div>
                       <div class="gravedad-badge d-flex align-items-center text-white-50">
                           ${iconoGravedad}
                           <span class="ms-2">Gravedad: ${denuncia.gravedad}</span>
                       </div>
                   </div>
               </div>
           </div>
       `;
   }
   
   /**
    * Generar información general
    */
   generarInfoGeneral(denuncia) {
       return `
           <div class="result-body">
               <h5 class="section-title mb-4">
                   <i class="bi bi-info-circle text-primary me-2"></i>
                   Información General
               </h5>
               
               <div class="info-grid">
                   <div class="info-item slide-in-right" style="animation-delay: 0.1s">
                       <div class="info-label">
                           <i class="bi ${denuncia.categoria_icono} me-1"></i>
                           Categoría
                       </div>
                       <div class="info-value">${denuncia.nombre_categoria}</div>
                   </div>
                   
                   <div class="info-item slide-in-right" style="animation-delay: 0.2s">
                       <div class="info-label">
                           <i class="bi bi-tag me-1"></i>
                           Tipo
                       </div>
                       <div class="info-value">
                           <span class="badge ${denuncia.tipo_principal === 'AMBIENTAL' ? 'bg-success' : 'bg-info'}">${denuncia.tipo_principal}</span>
                       </div>
                   </div>
                   
                   <div class="info-item slide-in-right" style="animation-delay: 0.3s">
                       <div class="info-label">
                           <i class="bi bi-exclamation-triangle me-1"></i>
                           Nivel de Gravedad
                       </div>
                       <div class="info-value">
                           ${this.generarIconoGravedad(denuncia.gravedad)}
                           ${denuncia.gravedad}
                       </div>
                   </div>
                   
                   <div class="info-item slide-in-right" style="animation-delay: 0.4s">
                       <div class="info-label">
                           <i class="bi bi-geo-alt me-1"></i>
                           Ubicación Específica
                       </div>
                       <div class="info-value">
                           ${denuncia.provincia}, ${denuncia.canton}
                           ${denuncia.parroquia ? `<br><small class="text-muted">Parroquia: ${denuncia.parroquia}</small>` : ''}
                           ${denuncia.direccion_especifica ? `<br><small class="text-muted">${denuncia.direccion_especifica}</small>` : ''}
                       </div>
                   </div>
                   
                   ${denuncia.fecha_ocurrencia ? `
                       <div class="info-item slide-in-right" style="animation-delay: 0.5s">
                           <div class="info-label">
                               <i class="bi bi-clock me-1"></i>
                               Fecha de Ocurrencia
                           </div>
                           <div class="info-value">${this.formatearFecha(denuncia.fecha_ocurrencia)}</div>
                       </div>
                   ` : ''}
                   
                   ${denuncia.nombre_institucion ? `
                       <div class="info-item slide-in-right" style="animation-delay: 0.6s">
                           <div class="info-label">
                               <i class="bi bi-building me-1"></i>
                               Institución Asignada
                           </div>
                           <div class="info-value">
                               <strong>${denuncia.nombre_institucion}</strong>
                               ${denuncia.institucion_siglas ? `<br><small class="text-muted">(${denuncia.institucion_siglas})</small>` : ''}
                           </div>
                       </div>
                   ` : ''}
               </div>
               
               <div class="descripcion-section mt-4">
                   <h6 class="mb-3">
                       <i class="bi bi-chat-text text-secondary me-2"></i>
                       Descripción de la Denuncia
                   </h6>
                   <div class="descripcion-content">
                       <p class="text-muted lh-lg">${this.formatearDescripcion(denuncia.descripcion)}</p>
                   </div>
               </div>
           </div>
       `;
   }
   
   /**
    * Generar seguimiento
    */
   generarSeguimiento(seguimiento) {
       if (!seguimiento || seguimiento.length === 0) {
           return `
               <div class="result-body border-top">
                   <h5 class="section-title mb-4">
                       <i class="bi bi-clock-history text-info me-2"></i>
                       Seguimiento de Estados
                   </h5>
                   <div class="empty-state text-center py-4">
                       <i class="bi bi-hourglass-split text-muted" style="font-size: 3rem;"></i>
                       <p class="text-muted mt-3 mb-0">No hay actualizaciones de seguimiento disponibles.</p>
                       <small class="text-muted">Las actualizaciones aparecerán aquí conforme se procese tu denuncia.</small>
                   </div>
               </div>
           `;
       }
       
       const timelineItems = seguimiento.map((item, index) => `
           <div class="timeline-item slide-in-right" style="animation-delay: ${0.1 * (index + 1)}s">
               <div class="d-flex justify-content-between align-items-start mb-3">
                   <div class="timeline-date">
                       <i class="bi bi-calendar-check me-2"></i>
                       ${this.formatearFecha(item.fecha_actualizacion)}
                   </div>
                   <span class="badge rounded-pill" style="background-color: ${item.estado_nuevo_color}; color: white;">
                       ${item.estado_nuevo_nombre}
                   </span>
               </div>
               <div class="timeline-content">
                   <h6 class="timeline-title mb-2">
                       ${item.estado_anterior_nombre ? 
                           `<i class="bi bi-arrow-right-circle me-2 text-primary"></i>Cambio de "${item.estado_anterior_nombre}" a "${item.estado_nuevo_nombre}"` : 
                           `<i class="bi bi-play-circle me-2 text-success"></i>Estado inicial: "${item.estado_nuevo_nombre}"`
                       }
                   </h6>
                   ${item.comentario ? `
                       <div class="timeline-comment">
                           <i class="bi bi-chat-quote me-2 text-muted"></i>
                           <em>"${item.comentario}"</em>
                       </div>
                   ` : ''}
                   ${item.responsable_nombre ? `
                       <div class="timeline-responsible mt-2">
                           <i class="bi bi-person-badge me-2 text-secondary"></i>
                           <small class="text-muted">
                               Actualizado por: <strong>${item.responsable_nombre}</strong> 
                               (${item.responsable_rol})
                           </small>
                       </div>
                   ` : ''}
               </div>
           </div>
       `).join('');
       
       return `
           <div class="result-body border-top">
               <h5 class="section-title mb-4">
                   <i class="bi bi-clock-history text-info me-2"></i>
                   Seguimiento de Estados
                   <span class="badge bg-info ms-2">${seguimiento.length} actualizaciones</span>
               </h5>
               <div class="timeline">
                   ${timelineItems}
               </div>
           </div>
       `;
   }
   
   /**
    * Generar evidencias con previsualización
    */
   /**
 * Generar evidencias con previsualización (SIN RESTRICCIONES)
 */
generarEvidencias(evidencias, permisos) {
    if (!evidencias || evidencias.length === 0) {
        return `
            <div class="result-body border-top">
                <h5 class="section-title mb-4">
                    <i class="bi bi-paperclip text-secondary me-2"></i>
                    Evidencias Adjuntas
                </h5>
                <div class="empty-state text-center py-4">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No hay evidencias adjuntas a esta denuncia.</p>
                </div>
            </div>
        `;
    }
    
    const evidenciaItems = evidencias.map((evidencia, index) => {
        const esImagen = this.esArchivoImagen(evidencia.tipo_evidencia, evidencia.nombre_archivo);
        
        return `
            <div class="evidence-item slide-in-right" 
                 style="animation-delay: ${0.1 * (index + 1)}s"
                 data-evidencia='${JSON.stringify(evidencia)}'
                 onclick="consultaEstado.mostrarEvidencia(this)">
                
                ${esImagen ? `
                    <div class="evidence-preview-container">
                        <img src="../../${evidencia.ruta_archivo}" 
                             alt="${evidencia.nombre_archivo}"
                             class="evidence-preview"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="evidence-icon" style="display: none;">
                            ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
                        </div>
                    </div>
                ` : `
                    <div class="evidence-icon">
                        ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
                    </div>
                `}
                
                <div class="evidence-info">
                    <h6 class="evidence-title">${this.truncarTexto(evidencia.nombre_archivo, 25)}</h6>
                    <div class="evidence-meta">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-file-earmark me-1"></i>
                            ${evidencia.tipo_evidencia} • ${this.formatearTamaño(evidencia.tamaño_archivo)}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            ${this.formatearFecha(evidencia.fecha_subida)}
                        </small>
                    </div>
                </div>
                
                <div class="evidence-overlay">
                    <i class="bi bi-eye-fill"></i>
                    <span>Ver evidencia</span>
                </div>
            </div>
        `;
    }).join('');
    
    return `
        <div class="result-body border-top">
            <h5 class="section-title mb-4">
                <i class="bi bi-paperclip text-secondary me-2"></i>
                Evidencias Adjuntas
                <span class="badge bg-secondary ms-2">${evidencias.length} archivos</span>
            </h5>
            <div class="evidence-grid">
                ${evidenciaItems}
            </div>
        </div>
    `;
}
   
   /**
    * Generar sección de acciones
    */
   generarAcciones(denuncia) {
       return `
           <div class="result-body border-top">
               <div class="actions-section text-center">
                   <h6 class="mb-3 text-muted">Acciones Disponibles</h6>
                   <div class="d-flex justify-content-center gap-3 flex-wrap">
                       <button class="btn btn-outline-primary" onclick="consultaEstado.imprimirResultados()">
                           <i class="bi bi-printer me-2"></i>Imprimir
                       </button>
                       <button class="btn btn-outline-secondary" onclick="consultaEstado.limpiarFormulario()">
                           <i class="bi bi-arrow-clockwise me-2"></i>Nueva Consulta
                       </button>
                       <button class="btn btn-outline-success" onclick="consultaEstado.compartirResultado('${denuncia.numero_denuncia}')">
                           <i class="bi bi-share me-2"></i>Compartir
                       </button>
                   </div>
               </div>
           </div>
       `;
   }
   
   /**
    * Configurar eventos para evidencias
    */
   configurarEventosEvidencias() {
       const evidenceItems = document.querySelectorAll('.evidence-item');
       evidenceItems.forEach(item => {
           item.addEventListener('mouseenter', function() {
               this.style.transform = 'translateY(-8px) scale(1.02)';
           });
           
           item.addEventListener('mouseleave', function() {
               this.style.transform = 'translateY(0) scale(1)';
           });
       });
   }
   
   /**
    * Mostrar evidencia en modal
    */
   mostrarEvidencia(elemento) {
       const evidencia = JSON.parse(elemento.dataset.evidencia);
       const esImagen = this.esArchivoImagen(evidencia.tipo_evidencia, evidencia.nombre_archivo);
       
       // Actualizar contenido del modal
       document.getElementById('evidenciaModalLabel').innerHTML = `
           <i class="bi bi-eye me-2"></i>
           ${evidencia.nombre_archivo}
       `;
       
       const evidenciaContent = document.getElementById('evidenciaContent');
       
       if (esImagen) {
           evidenciaContent.innerHTML = `
               <img src="../../${evidencia.ruta_archivo}" 
                    alt="${evidencia.nombre_archivo}"
                    class="evidencia-preview-large img-fluid"
                    style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
           `;
       } else {
           evidenciaContent.innerHTML = `
               <div class="text-center p-4">
                   <div class="evidencia-icon-large mb-3" style="font-size: 5rem; color: var(--primary-color);">
                       ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
                   </div>
                   <h5>${evidencia.nombre_archivo}</h5>
                   <p class="text-muted">
                       Este tipo de archivo no se puede previsualizar. 
                       Usa el botón "Descargar" para ver el contenido.
                   </p>
               </div>
           `;
       }
       
       // Actualizar información de la evidencia
       document.getElementById('evidenciaNombre').textContent = evidencia.nombre_archivo;
       document.getElementById('evidenciaTipo').textContent = evidencia.tipo_evidencia;
       document.getElementById('evidenciaTamaño').textContent = this.formatearTamaño(evidencia.tamaño_archivo);
       document.getElementById('evidenciaFecha').textContent = this.formatearFecha(evidencia.fecha_subida);
       
       // Configurar enlace de descarga
       document.getElementById('evidenciaDescargar').href = `../../${evidencia.ruta_archivo}`;
       document.getElementById('evidenciaDescargar').download = evidencia.nombre_archivo;
       
       // Mostrar modal
       this.evidenciaModal.show();
   }
   
   /**
    * Formatear número de denuncia mientras se escribe
    */
   formatearNumero(e) {
       let valor = e.target.value.replace(/[^0-9]/g, '');
       
       if (valor.length > 0) {
           if (valor.length <= 4) {
               valor = `ECO-${valor}`;
           } else if (valor.length <= 6) {
               valor = `ECO-${valor.slice(0, 4)}-${valor.slice(4)}`;
           } else if (valor.length <= 12) {
               valor = `ECO-${valor.slice(0, 4)}-${valor.slice(4, 6)}-${valor.slice(6)}`;
           } else {
               valor = `ECO-${valor.slice(0, 4)}-${valor.slice(4, 6)}-${valor.slice(6, 12)}`;
           }
       }
       
       e.target.value = valor.toUpperCase();
   }
   
   /**
    * Validar teclas permitidas
    */
   validarTecla(e) {
       const teclaPermitida = /[0-9]/.test(e.key) || 
                             ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key);
       
       if (!teclaPermitida) {
           e.preventDefault();
       }
   }
   
   /**
    * Validar formato del número
    */
   validarFormato(numero) {
       return /^ECO-\d{4}-\d{2}-\d{6}$/.test(numero);
   }
   
   /**
    * Verificar si es archivo de imagen
    */
   esArchivoImagen(tipo, nombre) {
       const extensionesImagen = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp'];
       const extension = nombre.toLowerCase().substring(nombre.lastIndexOf('.'));
       return tipo === 'FOTO' || extensionesImagen.includes(extension);
   }
   
   /**
    * Mostrar/ocultar loading
    */
   mostrarLoading(mostrar) {
       if (mostrar) {
           this.loading.classList.remove('d-none');
           this.btnBuscar.disabled = true;
           this.btnBuscar.innerHTML = `
               <div class="spinner-border spinner-border-sm me-2" role="status">
                   <span class="visually-hidden">Cargando...</span>
               </div>
               Buscando...
           `;
       } else {
           this.loading.classList.add('d-none');
           this.btnBuscar.disabled = false;
           this.btnBuscar.innerHTML = `
               <i class="bi bi-search me-2"></i>
               <span class="btn-text">Consultar Estado</span>
           `;
       }
   }
   
   /**
    * Mostrar error con animación
    */
   mostrarError(mensaje) {
       this.errorMessage.textContent = mensaje;
       this.error.classList.remove('d-none');
       
       // Scroll suave al error
       setTimeout(() => {
           this.error.scrollIntoView({ 
               behavior: 'smooth',
               block: 'center'
           });
       }, 100);
       
       // Auto-ocultar después de 8 segundos
       setTimeout(() => {
           this.ocultarError();
       }, 8000);
   }
   
   /**
    * Ocultar error
    */
   ocultarError() {
       this.error.classList.add('d-none');
   }
   
   /**
    * Ocultar resultados
    */
   ocultarResultados() {
       this.resultados.classList.add('d-none');
   }
   
   /**
    * Animar resultados
    */
   animarResultados() {
       // Agregar clases de animación escalonada
       const elementos = this.resultados.querySelectorAll('.slide-in-right');
       elementos.forEach((el, index) => {
           el.style.animationDelay = `${0.1 * index}s`;
       });
   }
   
   /**
    * Utilidades de formato
    */
   obtenerClaseEstado(estado) {
       const estados = {
           'Pendiente': 'status-pendiente',
           'En Revisión': 'status-revision', 
           'En Proceso': 'status-proceso',
           'Resuelto': 'status-resuelto',
           'Cerrado': 'status-cerrado',
           'Rechazado': 'status-rechazado'
       };
       return estados[estado] || 'status-pendiente';
   }
   
   generarIconoGravedad(gravedad) {
       const iconos = {
           'BAJA': '<i class="bi bi-circle-fill text-success" title="Gravedad Baja"></i>',
           'MEDIA': '<i class="bi bi-circle-fill text-warning" title="Gravedad Media"></i>',
           'ALTA': '<i class="bi bi-exclamation-circle-fill text-danger" title="Gravedad Alta"></i>',
           'CRITICA': '<i class="bi bi-exclamation-triangle-fill text-danger" title="Gravedad Crítica"></i>'
       };
       return iconos[gravedad] || '<i class="bi bi-circle-fill text-secondary"></i>';
   }
   
   generarIconoEvidencia(tipo) {
       const iconos = {
           'FOTO': '<i class="bi bi-image text-primary"></i>',
           'VIDEO': '<i class="bi bi-camera-video text-info"></i>',
           'DOCUMENTO': '<i class="bi bi-file-earmark-text text-success"></i>',
           'AUDIO': '<i class="bi bi-mic text-warning"></i>'
       };
       return iconos[tipo] || '<i class="bi bi-file text-secondary"></i>';
   }
   
   formatearFecha(fecha) {
       if (!fecha) return 'No disponible';
       
       const opciones = {
           year: 'numeric',
           month: 'long',
           day: 'numeric',
           hour: '2-digit',
           minute: '2-digit',
           timeZone: 'America/Guayaquil'
       };
       
       return new Date(fecha).toLocaleDateString('es-EC', opciones);
   }
   
   formatearTamaño(bytes) {
       if (!bytes || bytes === 0) return '0 B';
       
       const unidades = ['B', 'KB', 'MB', 'GB'];
       let indice = 0;
       let tamaño = parseFloat(bytes);
       
       while (tamaño >= 1024 && indice < unidades.length - 1) {
           tamaño /= 1024;
           indice++;
       }
       
       return `${tamaño.toFixed(1)} ${unidades[indice]}`;
   }
   
   formatearDescripcion(descripcion) {
       if (!descripcion) return 'Sin descripción disponible';
       
       // Limitar a 500 caracteres y agregar "leer más" si es necesario
       if (descripcion.length > 500) {
           return `${descripcion.substring(0, 500)}... 
                   <button class="btn btn-link p-0 text-primary" onclick="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                       <small>leer más</small>
                   </button>
                   <span style="display: none;">${descripcion.substring(500)}</span>`;
       }
       
       return descripcion;
   }
   
   truncarTexto(texto, limite) {
       if (!texto) return '';
       return texto.length > limite ? `${texto.substring(0, limite)}...` : texto;
   }
   
   /**
    * Funciones de acción
    */
   limpiarFormulario() {
       this.inputNumero.value = '';
       this.ocultarResultados();
       this.ocultarError();
       this.inputNumero.focus();
   }
   
   imprimirResultados() {
       // Ocultar elementos no necesarios para impresión
       const elementosOcultar = document.querySelectorAll('.btn, .navbar, .sidebar, .modal');
       elementosOcultar.forEach(el => el.style.display = 'none');
       
       window.print();
       
       // Restaurar elementos
       setTimeout(() => {
           elementosOcultar.forEach(el => el.style.display = '');
       }, 1000);
   }
   
   compartirResultado(numeroDenuncia) {
       if (navigator.share) {
           navigator.share({
               title: 'Estado de Denuncia EcoReport',
               text: `Consulta el estado de la denuncia ${numeroDenuncia}`,
               url: window.location.href
           });
       } else {
           // Fallback: copiar al portapapeles
           const url = `${window.location.origin}${window.location.pathname}`;
           navigator.clipboard.writeText(url).then(() => {
               alert('Enlace copiado al portapapeles');
           });
       }
   }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
   window.consultaEstado = new ConsultaEstado();
});

// Funciones globales de utilidad
window.ConsultaEstadoUtils = {
   /**
    * Limpiar formulario
    */
   limpiarFormulario() {
       if (window.consultaEstado) {
           window.consultaEstado.limpiarFormulario();
       }
   },
   
   /**
    * Imprimir resultados
    */
   imprimirResultados() {
       if (window.consultaEstado) {
           window.consultaEstado.imprimirResultados();
       }
   },
   
   /**
    * Recargar página
    */
   recargarPagina() {
       window.location.reload();
   },
   
   /**
    * Volver al inicio
    */
   volverInicio() {
       window.location.href = '../../vistas/index.php';
   }
};

// Event listeners adicionales para mejorar UX
document.addEventListener('DOMContentLoaded', () => {
   // Animaciones de entrada
   const observerOptions = {
       threshold: 0.1,
       rootMargin: '0px 0px -50px 0px'
   };
   
   const observer = new IntersectionObserver((entries) => {
       entries.forEach(entry => {
           if (entry.isIntersecting) {
               entry.target.classList.add('animate-in');
           }
       });
   }, observerOptions);
   
   // Observar elementos que deben animarse
   document.querySelectorAll('.search-container, .result-card').forEach(el => {
       observer.observe(el);
   });
   
   // Agregar efectos de teclado
   document.addEventListener('keydown', (e) => {
       // ESC para cerrar modales o limpiar errores
       if (e.key === 'Escape') {
           const errorElement = document.getElementById('error');
           if (errorElement && !errorElement.classList.contains('d-none')) {
               errorElement.classList.add('d-none');
           }
       }
       
       // Ctrl/Cmd + K para enfocar búsqueda
       if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
           e.preventDefault();
           document.getElementById('numero_denuncia')?.focus();
       }
   });
   
   // Mejorar accesibilidad
   const inputNumero = document.getElementById('numero_denuncia');
   if (inputNumero) {
       inputNumero.setAttribute('aria-label', 'Ingrese el número de denuncia');
       inputNumero.setAttribute('aria-describedby', 'help-text');
   }
});

// Performance monitoring (opcional)
if ('performance' in window) {
   window.addEventListener('load', () => {
       setTimeout(() => {
           const perfData = performance.timing;
           const loadTime = perfData.loadEventEnd - perfData.navigationStart;
           console.log(`⚡ Tiempo de carga: ${loadTime}ms`);
       }, 0);
   });
}

// Service Worker para cache (futuro)
if ('serviceWorker' in navigator && location.protocol === 'https:') {
   window.addEventListener('load', () => {
       navigator.serviceWorker.register('/sw.js')
           .then(registration => {
               console.log('📡 Service Worker registrado');
           })
           .catch(error => {
               console.log('❌ Error registrando Service Worker:', error);
           });
   });
}
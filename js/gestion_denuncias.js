// js/gestion_denuncias.js

/**
 * GESTIÓN DE DENUNCIAS - JAVASCRIPT COMPLETO
 * Sistema completo para supervisores y responsables institucionales
 * Manejo de estados, asignaciones, filtros y todas las interacciones
 */

class GestionDenuncias {
    constructor() {
        // Verificar que los datos estén disponibles
        if (typeof window.gestionData === 'undefined') {
            console.error('❌ Datos de gestión no disponibles');
            return;
        }
        
        this.data = window.gestionData;
        this.tabla = null;
        this.denunciaSeleccionada = null;
        
        // Referencias a modales
        this.modalCambiarEstado = new bootstrap.Modal(document.getElementById('modalCambiarEstado'));
        this.modalAsignarInstitucion = new bootstrap.Modal(document.getElementById('modalAsignarInstitucion'));
        this.modalVerDetalles = new bootstrap.Modal(document.getElementById('modalVerDetalles'));
        this.modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmar'));
        
        this.init();
    }
    
    init() {
        console.log('🚀 Inicializando Gestión de Denuncias...');
        
        // Inicializar tabla
        this.inicializarTabla();
        
        // Configurar eventos
        this.configurarEventos();
        
        // Configurar formularios
        this.configurarFormularios();
        
        // Cargar datos iniciales
        this.cargarDenunciasEnTabla(this.data.denuncias);
        
        // Configurar filtros automáticos
        this.configurarFiltrosAutomaticos();
        
        console.log('✅ Gestión de Denuncias inicializada correctamente');
    }
    
    /**
     * Inicializar DataTable
     */
    inicializarTabla() {
        this.tabla = $('#tablaDenuncias').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[7, 'desc']], // Ordenar por fecha (columna 7)
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            columnDefs: [
                {
                    targets: [8], // Columna de acciones
                    orderable: false,
                    searchable: false
                },
                {
                    targets: [2], // Columna de estado
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data; // Ya viene formateado desde PHP
                        }
                        return data;
                    }
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                // Reconfigurar tooltips después de cada redibujado
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
    
    /**
     * Configurar eventos
     */
    configurarEventos() {
        // Evento para filtros
        $('#filtrosForm').on('submit', (e) => {
            e.preventDefault();
            this.aplicarFiltros();
        });
        
        // Eventos para cambios en select de estados en modal
        $('#nuevo_estado').on('change', (e) => {
            this.previsualizarEstado(e.target.value);
        });
        
        // Eventos para cambios en select de instituciones
        $('#institucion_asignada').on('change', (e) => {
            this.mostrarInfoInstitucion(e.target.value);
        });
        
        // Eventos para estadísticas clickeables
        $('.stat-card').on('click', (e) => {
            this.filtrarPorEstadistica(e.currentTarget);
        });
        
        // Evento para refrescar datos
        $(document).on('click', '[data-action="refresh"]', () => {
            this.refrescarDatos();
        });
    }
    
    /**
     * Configurar formularios
     */
    configurarFormularios() {
        // Formulario cambiar estado
        $('#formCambiarEstado').on('submit', (e) => {
            e.preventDefault();
            this.procesarCambioEstado();
        });
        
        // Formulario asignar institución
        $('#formAsignarInstitucion').on('submit', (e) => {
            e.preventDefault();
            this.procesarAsignacionInstitucion();
        });
    }
    
    /**
     * Cargar denuncias en la tabla
     */
    cargarDenunciasEnTabla(denuncias) {
        if (!this.tabla) {
            console.error('❌ Tabla no inicializada');
            return;
        }
        
        // Limpiar tabla
        this.tabla.clear();
        
        // Agregar filas
        denuncias.forEach(denuncia => {
            const fila = this.generarFilaDenuncia(denuncia);
            this.tabla.row.add(fila);
        });
        
        // Redibujar tabla
        this.tabla.draw();
        
        // Actualizar contador
        $('#totalDenuncias').text(denuncias.length);
        
        console.log(`📊 Cargadas ${denuncias.length} denuncias en la tabla`);
    }
    
    /**
     * Generar fila de denuncia para la tabla
     */
    generarFilaDenuncia(denuncia) {
        const estadoClass = this.obtenerClaseEstado(denuncia.nombre_estado);
        const gravedadClass = this.obtenerClaseGravedad(denuncia.gravedad);
        
        return [
            // Número
            `<span class="fw-bold text-primary">${denuncia.numero_denuncia}</span>`,
            
            // Título
            `<div class="titulo-cell">
                <span class="titulo-principal" title="${denuncia.titulo}">
                    ${this.truncarTexto(denuncia.titulo, 30)}
                </span>
                <small class="d-block text-muted">
                    <i class="bi bi-person me-1"></i>${this.truncarTexto(denuncia.denunciante_nombre, 20)}
                </small>
            </div>`,
            
            // Estado
            `<span class="status-badge ${estadoClass}">
                <i class="bi bi-circle-fill me-1" style="color: ${denuncia.estado_color}"></i>
                ${denuncia.nombre_estado}
            </span>`,
            
            // Categoría
            `<div class="categoria-cell">
                <i class="bi ${denuncia.categoria_icono} me-1 text-primary"></i>
                <span>${this.truncarTexto(denuncia.nombre_categoria, 20)}</span>
                <br><small class="text-muted">${denuncia.tipo_principal}</small>
            </div>`,
            
            // Gravedad
            `<span class="gravity-badge ${gravedadClass}">
                ${this.obtenerIconoGravedad(denuncia.gravedad)} ${denuncia.gravedad}
            </span>`,
            
            // Ubicación
            `<div class="ubicacion-cell">
                <i class="bi bi-geo-alt me-1 text-secondary"></i>
                <span>${denuncia.provincia}</span>
                <br><small class="text-muted">${denuncia.canton}</small>
            </div>`,
            
            // Institución
            denuncia.nombre_institucion ? 
                `<div class="institucion-cell">
                    <i class="bi bi-building me-1 text-success"></i>
                    <span>${this.truncarTexto(denuncia.nombre_institucion, 15)}</span>
                    <br><small class="text-muted">(${denuncia.institucion_siglas})</small>
                </div>` : 
                `<span class="text-muted">
                    <i class="bi bi-dash-circle me-1"></i>Sin asignar
                </span>`,
            
            // Fecha
            `<div class="fecha-cell">
                <span>${this.formatearFecha(denuncia.fecha_creacion)}</span>
                <br><small class="text-muted">${this.tiempoTranscurrido(denuncia.fecha_creacion)}</small>
            </div>`,
            
            // Acciones
            this.generarBotonesAccion(denuncia)
        ];
    }
    
    /**
     * Generar botones de acción
     */
    generarBotonesAccion(denuncia) {
        const permisos = this.data.permisos;
        let botones = `<div class="action-buttons">`;
        
        // Botón ver detalles (siempre disponible)
        botones += `
            <button class="action-btn btn-view" 
                    onclick="gestionDenuncias.verDetalles(${denuncia.id_denuncia})"
                    data-bs-toggle="tooltip" 
                    title="Ver detalles">
                <i class="bi bi-eye"></i>
            </button>`;
        
        // Botón cambiar estado (si tiene permisos)
        if (permisos.puede_editar) {
            botones += `
                <button class="action-btn btn-edit" 
                        onclick="gestionDenuncias.abrirCambiarEstado(${denuncia.id_denuncia})"
                        data-bs-toggle="tooltip" 
                        title="Cambiar estado">
                    <i class="bi bi-arrow-repeat"></i>
                </button>`;
        }
        
        // Botón asignar institución (solo supervisores y si no está asignada)
        if ((this.data.id_rol === 76 || this.data.id_rol === 1) && !denuncia.id_institucion_asignada) {
            botones += `
                <button class="action-btn btn-assign" 
                        onclick="gestionDenuncias.abrirAsignarInstitucion(${denuncia.id_denuncia})"
                        data-bs-toggle="tooltip" 
                        title="Asignar institución">
                    <i class="bi bi-building-add"></i>
                </button>`;
        }
        
        // Botón consulta pública
        botones += `
            <button class="action-btn btn-view" 
                    onclick="gestionDenuncias.abrirConsultaPublica('${denuncia.numero_denuncia}')"
                    data-bs-toggle="tooltip" 
                    title="Ver en consulta pública">
                <i class="bi bi-box-arrow-up-right"></i>
            </button>`;
        
        botones += `</div>`;
        return botones;
    }
    
    /**
     * Aplicar filtros
     */
    async aplicarFiltros() {
        this.mostrarLoading('Aplicando filtros...');
        
        try {
            const formData = new FormData(document.getElementById('filtrosForm'));
            
            const response = await fetch('../../../controladores/GestionDenuncias/GestionDenunciasController.php?action=filtrar', {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.cargarDenunciasEnTabla(resultado.data);
                this.mostrarNotificacion('success', `Filtros aplicados. ${resultado.total} denuncias encontradas.`);
            } else {
                this.mostrarNotificacion('error', resultado.message || 'Error al aplicar filtros');
            }
            
        } catch (error) {
            console.error('Error aplicando filtros:', error);
            this.mostrarNotificacion('error', 'Error de conexión al aplicar filtros');
        } finally {
            this.ocultarLoading();
        }
    }
    
    /**
     * Limpiar filtros
     */
    limpiarFiltros() {
        document.getElementById('filtrosForm').reset();
        this.cargarDenunciasEnTabla(this.data.denuncias);
        this.mostrarNotificacion('info', 'Filtros limpiados');
    }
    
    /**
     * Abrir modal cambiar estado
     */
    async abrirCambiarEstado(idDenuncia) {
        const denuncia = this.buscarDenuncia(idDenuncia);
        if (!denuncia) {
            this.mostrarNotificacion('error', 'Denuncia no encontrada');
            return;
        }
        
        this.denunciaSeleccionada = denuncia;
        
        // Llenar datos del modal
        document.getElementById('estado_id_denuncia').value = denuncia.id_denuncia;
        document.getElementById('estado_numero_denuncia').textContent = denuncia.numero_denuncia;
        document.getElementById('estado_titulo_denuncia').textContent = denuncia.titulo;
        
        // Estado actual
        const estadoActual = `<span class="status-badge ${this.obtenerClaseEstado(denuncia.nombre_estado)}">
            <i class="bi bi-circle-fill me-1" style="color: ${denuncia.estado_color}"></i>
            ${denuncia.nombre_estado}
        </span>`;
        document.getElementById('estado_actual_badge').innerHTML = estadoActual;
        
        // Limpiar formulario
        document.getElementById('formCambiarEstado').reset();
        document.getElementById('estado_id_denuncia').value = denuncia.id_denuncia;
        
        // Mostrar modal
        this.modalCambiarEstado.show();
    }
    
    /**
     * Abrir modal asignar institución
     */
    async abrirAsignarInstitucion(idDenuncia) {
        const denuncia = this.buscarDenuncia(idDenuncia);
        if (!denuncia) {
            this.mostrarNotificacion('error', 'Denuncia no encontrada');
            return;
        }
        
        this.denunciaSeleccionada = denuncia;
        
        // Llenar datos del modal
        document.getElementById('asignar_id_denuncia').value = denuncia.id_denuncia;
        document.getElementById('asignar_numero_denuncia').textContent = denuncia.numero_denuncia;
        document.getElementById('asignar_categoria_denuncia').textContent = denuncia.nombre_categoria;
        
        const estadoActual = `<span class="status-badge ${this.obtenerClaseEstado(denuncia.nombre_estado)}">
            ${denuncia.nombre_estado}
        </span>`;
        document.getElementById('asignar_estado_denuncia').innerHTML = estadoActual;
        
        // Limpiar formulario
        document.getElementById('formAsignarInstitucion').reset();
        document.getElementById('asignar_id_denuncia').value = denuncia.id_denuncia;
        
        // Ocultar info de contacto
        document.getElementById('institucion_contacto').style.display = 'none';
        
        // Mostrar modal
        this.modalAsignarInstitucion.show();
    }
    
    /**
     * Ver detalles de denuncia
     */
    /**
 * Ver detalles de denuncia (MEJORADO)
 */
async verDetalles(idDenuncia) {
    this.mostrarLoading('Cargando detalles completos...');
    
    try {
        // Obtener datos básicos
        const response = await fetch(`../../../controladores/GestionDenuncias/GestionDenunciasController.php?action=obtener_detalle&id=${idDenuncia}`);
        const resultado = await response.json();
        
        if (resultado.success) {
            this.denunciaSeleccionada = resultado.data;
            
            // Cargar información general
            this.cargarDetallesGeneral(resultado.data);
            
            // Cargar seguimiento
            await this.cargarDetallesSeguimiento(idDenuncia);
            
            // Cargar evidencias
            await this.cargarDetallesEvidencias(idDenuncia);
            
            // Cargar asignación
            await this.cargarDetallesAsignacion(idDenuncia);
            
            this.modalVerDetalles.show();
        } else {
            this.mostrarNotificacion('error', resultado.message || 'Error al obtener detalles');
        }
        
    } catch (error) {
        console.error('Error obteniendo detalles:', error);
        this.mostrarNotificacion('error', 'Error de conexión');
    } finally {
        this.ocultarLoading();
    }
}

/**
 * Cargar detalles generales
 */
cargarDetallesGeneral(denuncia) {
    const estadoClass = this.obtenerClaseEstado(denuncia.nombre_estado);
    const gravedadClass = this.obtenerClaseGravedad(denuncia.gravedad);
    
    const html = `
        <!-- Header principal -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <h3 class="text-primary mb-2">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    ${denuncia.numero_denuncia}
                </h3>
                <h4 class="mb-3">${denuncia.titulo}</h4>
                <p class="text-muted lead">${denuncia.descripcion}</p>
            </div>
            <div class="col-lg-4">
                <div class="status-summary p-3 bg-light rounded">
                    <div class="mb-3">
                        <span class="status-badge ${estadoClass}">
                            <i class="bi bi-circle-fill me-1" style="color: ${denuncia.estado_color}"></i>
                            ${denuncia.nombre_estado}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="gravity-badge ${gravedadClass}">
                            ${this.obtenerIconoGravedad(denuncia.gravedad)} ${denuncia.gravedad}
                        </span>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        ${this.tiempoTranscurrido(denuncia.fecha_creacion)}
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Información detallada en cards -->
        <div class="row g-4">
            <!-- Card: Información Básica -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Información Básica
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Categoría:</td>
                                <td>
                                    <i class="bi ${denuncia.categoria_icono} me-2 text-primary"></i>
                                    ${denuncia.nombre_categoria}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tipo:</td>
                                <td>
                                    <span class="badge ${denuncia.tipo_principal === 'AMBIENTAL' ? 'bg-success' : 'bg-info'}">
                                        ${denuncia.tipo_principal}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Fecha Creación:</td>
                                <td>${this.formatearFecha(denuncia.fecha_creacion)}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Última Actualización:</td>
                                <td>${this.formatearFecha(denuncia.fecha_actualizacion)}</td>
                            </tr>
                            ${denuncia.fecha_ocurrencia ? `
                            <tr>
                                <td class="fw-bold">Fecha Ocurrencia:</td>
                                <td>${this.formatearFecha(denuncia.fecha_ocurrencia)}</td>
                            </tr>` : ''}
                            <tr>
                                <td class="fw-bold">Gravedad:</td>
                                <td>
                                    <span class="gravity-badge ${gravedadClass}">
                                        ${this.obtenerIconoGravedad(denuncia.gravedad)} ${denuncia.gravedad}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Card: Ubicación -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt me-2"></i>
                            Ubicación
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Provincia:</td>
                                <td>${denuncia.provincia}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cantón:</td>
                                <td>${denuncia.canton}</td>
                            </tr>
                            ${denuncia.parroquia ? `
                            <tr>
                                <td class="fw-bold">Parroquia:</td>
                                <td>${denuncia.parroquia}</td>
                            </tr>` : ''}
                            ${denuncia.direccion_especifica ? `
                            <tr>
                                <td class="fw-bold">Dirección:</td>
                                <td>${denuncia.direccion_especifica}</td>
                            </tr>` : ''}
                        </table>
                        
                        <!-- Mapa placeholder -->
                        <div class="mt-3">
                            <div class="bg-light p-3 rounded text-center">
                                <i class="bi bi-map text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0 small">Ubicación: ${denuncia.provincia}, ${denuncia.canton}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card: Denunciante -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            Denunciante
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Nombre:</td>
                                <td>${denuncia.denunciante_nombre}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Correo:</td>
                                <td>
                                    <a href="mailto:${denuncia.denunciante_correo}" class="text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>
                                        ${denuncia.denunciante_correo}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Card: Estadísticas -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>
                            Estadísticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-item">
                                    <h4 class="text-primary mb-1">${denuncia.total_evidencias || 0}</h4>
                                    <small class="text-muted">Evidencias</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h4 class="text-success mb-1">${denuncia.total_seguimientos || 0}</h4>
                                    <small class="text-muted">Seguimientos</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h4 class="text-info mb-1">${this.calcularDiasTranscurridos(denuncia.fecha_creacion)}</h4>
                                    <small class="text-muted">Días</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información adicional si existe -->
        ${denuncia.informacion_adicional_denunciado ? `
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-chat-text me-2"></i>
                                Información Adicional
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${denuncia.informacion_adicional_denunciado}</p>
                        </div>
                    </div>
                </div>
            </div>
        ` : ''}
    `;
    
    document.getElementById('detalles_general').innerHTML = html;
}

/**
 * Cargar seguimiento completo
 */
async cargarDetallesSeguimiento(idDenuncia) {
    try {
        // Usar el modelo ConsultaDenuncia para obtener seguimiento
        const response = await fetch('../../consulta/consultar_estado.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_seguimiento&id_denuncia=${idDenuncia}`
        });
        
        // Por ahora, generar contenido básico
        const seguimientoHtml = `
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-success">
                        <i class="bi bi-play-fill text-white"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Denuncia Creada</h6>
                        <p class="text-muted">La denuncia fue registrada en el sistema</p>
                        <small class="text-muted">${this.formatearFecha(this.denunciaSeleccionada.fecha_creacion)}</small>
                    </div>
                </div>
                
                ${this.denunciaSeleccionada.nombre_institucion ? `
                <div class="timeline-item">
                    <div class="timeline-marker bg-info">
                        <i class="bi bi-building text-white"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Asignada a Institución</h6>
                        <p class="text-muted">Asignada a: ${this.denunciaSeleccionada.nombre_institucion}</p>
                        <small class="text-muted">${this.formatearFecha(this.denunciaSeleccionada.fecha_actualizacion)}</small>
                    </div>
                </div>` : ''}
                
                <div class="timeline-item">
                    <div class="timeline-marker" style="background-color: ${this.denunciaSeleccionada.estado_color}">
                        <i class="bi bi-flag-fill text-white"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Estado Actual: ${this.denunciaSeleccionada.nombre_estado}</h6>
                        <p class="text-muted">${this.denunciaSeleccionada.estado_descripcion}</p>
                        <small class="text-muted">${this.formatearFecha(this.denunciaSeleccionada.fecha_actualizacion)}</small>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('detalles_seguimiento').innerHTML = seguimientoHtml;
        
    } catch (error) {
        document.getElementById('detalles_seguimiento').innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error cargando seguimiento. Intenta nuevamente.
            </div>
        `;
    }
}

/**
 * Utilidades para evidencias
 */
/**
 * Utilidades para evidencias
 */
esArchivoImagen(tipoEvidencia, nombreArchivo) {
    return tipoEvidencia === 'IMAGEN' || nombreArchivo.match(/\.(jpg|jpeg|png|gif|bmp|webp)$/i);
}

generarIconoEvidencia(tipoEvidencia) {
    const iconos = {
        'IMAGEN': '<i class="bi bi-image-fill text-primary" style="font-size: 3rem;"></i>',
        'VIDEO': '<i class="bi bi-camera-video-fill text-danger" style="font-size: 3rem;"></i>',
        'DOCUMENTO': '<i class="bi bi-file-earmark-pdf-fill text-info" style="font-size: 3rem;"></i>',
        'AUDIO': '<i class="bi bi-music-note-beamed text-warning" style="font-size: 3rem;"></i>',
        'OTRO': '<i class="bi bi-file-earmark text-secondary" style="font-size: 3rem;"></i>'
    };
    return iconos[tipoEvidencia] || iconos['OTRO'];
}

formatearTamaño(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Cargar evidencias REALES con previsualización
 */
async cargarDetallesEvidencias(idDenuncia) {
    try {
        // Mostrar loading
        document.getElementById('detalles_evidencias').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando evidencias...</p>
            </div>
        `;
        
        // Obtener evidencias reales
        const response = await fetch(`../../../controladores/GestionDenuncias/GestionDenunciasController.php?action=obtener_evidencias&id=${idDenuncia}`);
        const resultado = await response.json();
        
        if (resultado.success && resultado.data) {
            const evidencias = resultado.data;
            
            if (evidencias.length === 0) {
                document.getElementById('detalles_evidencias').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">Sin Evidencias</h5>
                        <p class="text-muted">Esta denuncia no tiene evidencias adjuntas.</p>
                        
                        <button class="btn btn-outline-primary mt-3" onclick="gestionDenuncias.mostrarSubirEvidencia(${idDenuncia})">
                            <i class="bi bi-plus-circle me-2"></i>
                            Agregar Primera Evidencia
                        </button>
                    </div>
                `;
                return;
            }
            
            // Generar grid de evidencias
            const evidenciasHtml = `
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0">
                        <i class="bi bi-paperclip me-2 text-primary"></i>
                        Evidencias Adjuntas (${evidencias.length})
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="gestionDenuncias.mostrarSubirEvidencia(${idDenuncia})">
                        <i class="bi bi-plus-circle me-1"></i>
                        Agregar
                    </button>
                </div>
                
                <div class="evidencias-grid">
                    ${evidencias.map(evidencia => this.generarTarjetaEvidencia(evidencia)).join('')}
                </div>
                
                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Tipos soportados:</strong> Imágenes (JPG, PNG, GIF), Videos (MP4, AVI), Documentos (PDF, DOC), Audio (MP3, WAV)
                        <br>
                        <strong>Tamaño máximo:</strong> 10MB por archivo
                    </small>
                </div>
            `;
            
            document.getElementById('detalles_evidencias').innerHTML = evidenciasHtml;
            
        } else {
            throw new Error(resultado.message || 'Error obteniendo evidencias');
        }
        
    } catch (error) {
        console.error('Error cargando evidencias:', error);
        document.getElementById('detalles_evidencias').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error cargando evidencias: ${error.message}
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="gestionDenuncias.cargarDetallesEvidencias(${idDenuncia})">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                </button>
            </div>
        `;
    }
}

/**
 * Generar tarjeta de evidencia
 */
generarTarjetaEvidencia(evidencia) {
    const esImagen = this.esArchivoImagen(evidencia.tipo_evidencia, evidencia.nombre_archivo);
    const esVideo = evidencia.tipo_evidencia === 'VIDEO' || evidencia.nombre_archivo.match(/\.(mp4|avi|mov|wmv|flv|webm)$/i);
    
    return `
        <div class="evidencia-card" onclick="gestionDenuncias.previsualizarEvidencia(${JSON.stringify(evidencia).replace(/"/g, '&quot;')})">
            <div class="evidencia-preview">
                ${esImagen ? `
                    <img src="../../${evidencia.ruta_archivo}" 
                         alt="${evidencia.nombre_archivo}"
                         class="evidencia-preview-thumb"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="evidencia-icon-fallback" style="display: none;">
                        ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
                    </div>
                ` : `
                    <div class="evidencia-icon-container">
                        ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
                        ${esVideo ? '<div class="play-overlay"><i class="bi bi-play-circle-fill"></i></div>' : ''}
                    </div>
                `}
            </div>
            
            <div class="evidencia-info">
                <h6 class="evidencia-title" title="${evidencia.nombre_archivo}">
                    ${this.truncarTexto(evidencia.nombre_archivo, 18)}
                </h6>
                <div class="evidencia-meta">
                    <small class="text-muted d-block">
                        <i class="bi bi-file-earmark me-1"></i>
                        ${evidencia.tipo_evidencia}
                    </small>
                    <small class="text-muted d-block">
                        <i class="bi bi-hdd me-1"></i>
                        ${this.formatearTamaño(evidencia.tamaño_archivo)}
                    </small>
                    <small class="text-muted d-block">
                        <i class="bi bi-calendar3 me-1"></i>
                        ${this.formatearFecha(evidencia.fecha_subida)}
                    </small>
                </div>
            </div>
            
            <div class="evidencia-overlay">
                <i class="bi bi-eye-fill"></i>
                <span>Ver</span>
            </div>
        </div>
    `;
}

/**
 * Cargar información de asignación
 */
async cargarDetallesAsignacion(idDenuncia) {
    const denuncia = this.denunciaSeleccionada;
    
    const asignacionHtml = `
        <div class="row g-4">
            <div class="col-lg-8">
                ${denuncia.nombre_institucion ? `
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-building-check me-2"></i>
                                Institución Asignada
                            </h6>
                        </div>
                        <div class="card-body">
                            <h5>${denuncia.nombre_institucion}</h5>
                            <p class="text-muted mb-1">
                                <strong>Siglas:</strong> ${denuncia.institucion_siglas}<br>
                                <strong>Tipo:</strong> Institución responsable
                            </p>
                        </div>
                    </div>
                ` : `
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Sin Asignación
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Esta denuncia aún no ha sido asignada a ninguna institución responsable.</p>
                            ${(this.data.id_rol === 76 || this.data.id_rol === 1) ? `
                                <button class="btn btn-primary" onclick="gestionDenuncias.abrirAsignarInstitucion(${denuncia.id_denuncia})">
                                    <i class="bi bi-building-add me-2"></i>
                                    Asignar Institución
                                </button>
                                ` : ''}
                       </div>
                   </div>
               `}
           </div>
           
           <div class="col-lg-4">
               <div class="card">
                   <div class="card-header bg-info text-white">
                       <h6 class="mb-0">
                           <i class="bi bi-gear me-2"></i>
                           Acciones Rápidas
                       </h6>
                   </div>
                   <div class="card-body">
                       <div class="d-grid gap-2">
                           ${this.data.permisos.puede_editar ? `
                               <button class="btn btn-outline-primary btn-sm" onclick="gestionDenuncias.abrirCambiarEstado(${denuncia.id_denuncia})">
                                   <i class="bi bi-arrow-repeat me-1"></i>
                                   Cambiar Estado
                               </button>
                           ` : ''}
                           
                           ${(this.data.id_rol === 76 || this.data.id_rol === 1) && !denuncia.id_institucion_asignada ? `
                               <button class="btn btn-outline-success btn-sm" onclick="gestionDenuncias.abrirAsignarInstitucion(${denuncia.id_denuncia})">
                                   <i class="bi bi-building-add me-1"></i>
                                   Asignar Institución
                               </button>
                           ` : ''}
                           
                           <button class="btn btn-outline-info btn-sm" onclick="gestionDenuncias.abrirConsultaPublica('${denuncia.numero_denuncia}')">
                               <i class="bi bi-box-arrow-up-right me-1"></i>
                               Consulta Pública
                           </button>
                           
                           <button class="btn btn-outline-secondary btn-sm" onclick="gestionDenuncias.copiarEnlaceDenuncia('${denuncia.numero_denuncia}')">
                               <i class="bi bi-link-45deg me-1"></i>
                               Copiar Enlace
                           </button>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       
       <!-- Historial de asignaciones si existe -->
       <div class="row mt-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header bg-light">
                       <h6 class="mb-0">
                           <i class="bi bi-clock-history me-2"></i>
                           Historial de Asignaciones
                       </h6>
                   </div>
                   <div class="card-body">
                       <div class="timeline-simple">
                           <div class="timeline-item-simple">
                               <div class="timeline-marker-simple bg-primary"></div>
                               <div class="timeline-content-simple">
                                   <strong>Denuncia Creada</strong>
                                   <br><small class="text-muted">${this.formatearFecha(denuncia.fecha_creacion)}</small>
                               </div>
                           </div>
                           
                           ${denuncia.nombre_institucion ? `
                               <div class="timeline-item-simple">
                                   <div class="timeline-marker-simple bg-success"></div>
                                   <div class="timeline-content-simple">
                                       <strong>Asignada a ${denuncia.nombre_institucion}</strong>
                                       <br><small class="text-muted">${this.formatearFecha(denuncia.fecha_actualizacion)}</small>
                                   </div>
                               </div>
                           ` : `
                               <div class="timeline-item-simple">
                                   <div class="timeline-marker-simple bg-warning"></div>
                                   <div class="timeline-content-simple">
                                       <strong>Pendiente de Asignación</strong>
                                       <br><small class="text-muted">Esperando asignación a institución responsable</small>
                                   </div>
                               </div>
                           `}
                       </div>
                   </div>
               </div>
           </div>
       </div>
   `;
   
   document.getElementById('detalles_asignacion').innerHTML = asignacionHtml;
}

/**
* Previsualizar evidencia individual
*/
previsualizarEvidencia(evidencia) {
   const esImagen = this.esArchivoImagen(evidencia.tipo_evidencia, evidencia.nombre_archivo);
   const esVideo = evidencia.tipo_evidencia === 'VIDEO' || evidencia.nombre_archivo.match(/\.(mp4|avi|mov|wmv|flv|webm)$/i);
   
   let contenidoPreview = '';
   
   if (esImagen) {
       contenidoPreview = `
           <img src="../../${evidencia.ruta_archivo}" 
                alt="${evidencia.nombre_archivo}"
                class="img-fluid"
                style="max-height: 70vh; object-fit: contain;">
       `;
   } else if (esVideo) {
       contenidoPreview = `
           <video controls class="img-fluid" style="max-height: 70vh;">
               <source src="../../${evidencia.ruta_archivo}" type="video/mp4">
               <source src="../../${evidencia.ruta_archivo}" type="video/webm">
               Tu navegador no soporta el elemento de video.
           </video>
       `;
   } else {
       contenidoPreview = `
           <div class="text-center p-5">
               <div class="evidencia-icon-large mb-4" style="font-size: 6rem; color: var(--primary-color);">
                   ${this.generarIconoEvidencia(evidencia.tipo_evidencia)}
               </div>
               <h4>${evidencia.nombre_archivo}</h4>
               <p class="text-muted">
                   Este tipo de archivo no se puede previsualizar en el navegador.
                   <br>Usa el botón "Descargar" para ver el contenido.
               </p>
               <a href="../../${evidencia.ruta_archivo}" 
                  download="${evidencia.nombre_archivo}"
                  class="btn btn-success btn-lg">
                   <i class="bi bi-download me-2"></i>Descargar Archivo
               </a>
           </div>
       `;
   }
   
   // Actualizar modal de previsualización
   document.getElementById('evidencia_preview_content').innerHTML = contenidoPreview;
   document.getElementById('preview_nombre').textContent = evidencia.nombre_archivo;
   document.getElementById('preview_tipo').textContent = evidencia.tipo_evidencia;
   document.getElementById('preview_tamaño').textContent = this.formatearTamaño(evidencia.tamaño_archivo);
   document.getElementById('preview_fecha').textContent = this.formatearFecha(evidencia.fecha_subida);
   
   const enlaceDescargar = document.getElementById('preview_descargar');
   enlaceDescargar.href = `../../${evidencia.ruta_archivo}`;
   enlaceDescargar.download = evidencia.nombre_archivo;
   
   // Mostrar modal
   const modalPreview = new bootstrap.Modal(document.getElementById('modalPrevisualizarEvidencia'));
   modalPreview.show();
}

/**
* Utilidades adicionales
*/
calcularDiasTranscurridos(fecha) {
   const ahora = new Date();
   const fechaDenuncia = new Date(fecha);
   const diferencia = ahora - fechaDenuncia;
   return Math.floor(diferencia / (1000 * 60 * 60 * 24));
}

copiarEnlaceDenuncia(numeroDenuncia) {
   const enlace = `${window.location.origin}/ProyectoIntegrador/vistas/consulta/consultar_estado.php?numero=${numeroDenuncia}`;
   
   if (navigator.clipboard) {
       navigator.clipboard.writeText(enlace).then(() => {
           this.mostrarNotificacion('success', 'Enlace copiado al portapapeles');
       });
   } else {
       // Fallback para navegadores antiguos
       const textArea = document.createElement('textarea');
       textArea.value = enlace;
       document.body.appendChild(textArea);
       textArea.select();
       document.execCommand('copy');
       document.body.removeChild(textArea);
       this.mostrarNotificacion('success', 'Enlace copiado al portapapeles');
   }
}

imprimirDetalles() {
   // Ocultar elementos no imprimibles
   const elementosOcultar = document.querySelectorAll('.btn, .navbar, .sidebar, .modal-footer');
   elementosOcultar.forEach(el => el.style.display = 'none');
   
   // Configurar para impresión
   const modalBody = document.querySelector('#modalVerDetalles .modal-body');
   const contenidoOriginal = document.body.innerHTML;
   
   document.body.innerHTML = modalBody.innerHTML;
   document.title = `Denuncia ${this.denunciaSeleccionada?.numero_denuncia || 'Detalles'}`;
   
   window.print();
   
   // Restaurar
   document.body.innerHTML = contenidoOriginal;
   elementosOcultar.forEach(el => el.style.display = '');
   
   // Reinicializar eventos
   this.init();
}

exportarDetallesPDF() {
   this.mostrarNotificacion('info', 'Función de exportación PDF en desarrollo');
   // TODO: Implementar exportación a PDF
}

mostrarSubirEvidencia(idDenuncia) {
   this.mostrarNotificacion('info', 'Función para subir evidencias en desarrollo');
   // TODO: Implementar modal para subir evidencias adicionales
}
    
    /**
     * Procesar cambio de estado
     */
    async procesarCambioEstado() {
        const formData = new FormData(document.getElementById('formCambiarEstado'));
        
        // Validaciones
        if (!formData.get('nuevo_estado')) {
            this.mostrarNotificacion('warning', 'Debe seleccionar un nuevo estado');
            return;
        }
        
        this.mostrarLoading('Cambiando estado...');
        
        try {
            const response = await fetch('../../../controladores/GestionDenuncias/GestionDenunciasController.php?action=cambiar_estado', {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.modalCambiarEstado.hide();
                this.mostrarNotificacion('success', resultado.message);
                this.refrescarDatos();
            } else {
                this.mostrarNotificacion('error', resultado.message);
            }
            
        } catch (error) {
            console.error('Error cambiando estado:', error);
            this.mostrarNotificacion('error', 'Error de conexión');
        } finally {
            this.ocultarLoading();
        }
    }
    
    /**
     * Procesar asignación de institución
     */
    async procesarAsignacionInstitucion() {
        const formData = new FormData(document.getElementById('formAsignarInstitucion'));
        
        // Validaciones
        if (!formData.get('id_institucion')) {
            this.mostrarNotificacion('warning', 'Debe seleccionar una institución');
            return;
        }
        
        this.mostrarLoading('Asignando institución...');
        
        try {
            const response = await fetch('../../../controladores/GestionDenuncias/GestionDenunciasController.php?action=asignar_institucion', {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.modalAsignarInstitucion.hide();
                this.mostrarNotificacion('success', resultado.message);
                this.refrescarDatos();
            } else {
                this.mostrarNotificacion('error', resultado.message);
            }
            
        } catch (error) {
            console.error('Error asignando institución:', error);
            this.mostrarNotificacion('error', 'Error de conexión');
        } finally {
            this.ocultarLoading();
        }
    }
    
    /**
     * Previsualizar estado
     */
    previsualizarEstado(idEstado) {
        const estado = this.data.estados.find(e => e.id_estado_denuncia == idEstado);
        const preview = document.getElementById('preview_nuevo_estado');
        
        if (estado) {
            preview.innerHTML = `
                <span class="badge" style="background-color: ${estado.color}; color: white;">
                    <i class="bi bi-circle-fill me-1"></i>
                    ${estado.nombre_estado}
                </span>
                <small class="d-block text-muted mt-1">${estado.descripcion}</small>
            `;
        } else {
            preview.innerHTML = '<span class="badge bg-secondary">Selecciona un estado</span>';
        }
    }
    
    /**
     * Mostrar información de institución
     */
    mostrarInfoInstitucion(idInstitucion) {
        const institucion = this.data.instituciones.find(i => i.id_institucion == idInstitucion);
        const container = document.getElementById('institucion_contacto');
        const detalles = document.getElementById('contacto_detalles');
        
        if (institucion) {
            detalles.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Tipo:</strong> ${institucion.tipo_institucion}<br>
                        <strong>Responsable:</strong> ${institucion.responsable_nombre || 'No especificado'}
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> ${institucion.contacto_email || 'No disponible'}<br>
                        <strong>Teléfono:</strong> ${institucion.contacto_telefono || 'No disponible'}
                    </div>
                </div>
            `;
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
    
    /**
     * Configurar filtros automáticos
     */
    configurarFiltrosAutomaticos() {
        // Auto-aplicar filtros cuando cambien
        $('#filtrosForm select, #filtrosForm input').on('change', () => {
            // Debounce para evitar muchas peticiones
            clearTimeout(this.filtroTimeout);
            this.filtroTimeout = setTimeout(() => {
                this.aplicarFiltros();
            }, 500);
        });
    }
    
    /**
     * Filtrar por estadística
     */
    filtrarPorEstadistica(elemento) {
        const classes = elemento.className;
        
        // Limpiar filtros
        document.getElementById('filtrosForm').reset();
        
        // Aplicar filtro según la estadística
        if (classes.includes('pendientes')) {
            document.getElementById('filtro_estado').value = '1';
        } else if (classes.includes('revision')) {
            document.getElementById('filtro_estado').value = '2';
        } else if (classes.includes('proceso')) {
            document.getElementById('filtro_estado').value = '3';
        } else if (classes.includes('resueltas')) {
            document.getElementById('filtro_estado').value = '4';
        } else if (classes.includes('cerradas')) {
            // Cerradas incluye estados 5 y 6
            document.getElementById('filtro_estado').value = '5';
        }
        
        // Aplicar filtro
        this.aplicarFiltros();
    }
    
    /**
     * Refrescar datos
     */
    async refrescarDatos() {
        this.mostrarLoading('Actualizando datos...');
        
        try {
            // Recargar la página para obtener datos frescos
            window.location.reload();
        } catch (error) {
            console.error('Error refrescando datos:', error);
            this.mostrarNotificacion('error', 'Error al actualizar datos');
            this.ocultarLoading();
        }
    }
    
    /**
     * Abrir consulta pública
     */
    abrirConsultaPublica(numeroDenuncia) {
        const url = `../../consulta/consultar_estado.php`;
        const ventana = window.open(url, '_blank');
        
        // Opcional: rellenar automáticamente el número
        if (ventana) {
            ventana.addEventListener('load', () => {
                const input = ventana.document.getElementById('numero_denuncia');
                if (input) {
                    input.value = numeroDenuncia;
                    input.focus();
                }
            });
        }
    }
    
    /**
     * Exportar a Excel
     */
    exportarExcel() {
        this.mostrarNotificacion('info', 'Función de exportación Excel en desarrollo');
        // TODO: Implementar exportación Excel
    }
    
    /**
     * Exportar a PDF
     */
    exportarPDF() {
        this.mostrarNotificacion('info', 'Función de exportación PDF en desarrollo');
        // TODO: Implementar exportación PDF
    }
    
    /**
     * Utilidades
     */
    buscarDenuncia(id) {
        return this.data.denuncias.find(d => d.id_denuncia == id);
    }
    
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
   
   obtenerClaseGravedad(gravedad) {
       const gravedades = {
           'BAJA': 'gravity-baja',
           'MEDIA': 'gravity-media',
           'ALTA': 'gravity-alta',
           'CRITICA': 'gravity-critica'
       };
       return gravedades[gravedad] || 'gravity-media';
   }
   
   obtenerIconoGravedad(gravedad) {
       const iconos = {
           'BAJA': '<i class="bi bi-circle-fill text-success"></i>',
           'MEDIA': '<i class="bi bi-circle-fill text-warning"></i>',
           'ALTA': '<i class="bi bi-exclamation-circle-fill text-danger"></i>',
           'CRITICA': '<i class="bi bi-exclamation-triangle-fill text-danger"></i>'
       };
       return iconos[gravedad] || '<i class="bi bi-circle-fill text-secondary"></i>';
   }
   
   truncarTexto(texto, limite) {
       if (!texto) return '';
       return texto.length > limite ? `${texto.substring(0, limite)}...` : texto;
   }
   
   formatearFecha(fecha) {
       if (!fecha) return 'No disponible';
       
       const opciones = {
           year: 'numeric',
           month: 'short',
           day: 'numeric',
           hour: '2-digit',
           minute: '2-digit'
       };
       
       return new Date(fecha).toLocaleDateString('es-EC', opciones);
   }
   
   tiempoTranscurrido(fecha) {
       if (!fecha) return '';
       
       const ahora = new Date();
       const fechaDenuncia = new Date(fecha);
       const diferencia = ahora - fechaDenuncia;
       
       const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
       const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
       
       if (dias > 0) {
           return `hace ${dias} día${dias > 1 ? 's' : ''}`;
       } else if (horas > 0) {
           return `hace ${horas} hora${horas > 1 ? 's' : ''}`;
       } else {
           return 'hace menos de 1 hora';
       }
   }
   
   /**
    * Sistema de notificaciones
    */
   mostrarNotificacion(tipo, mensaje) {
       // Crear elemento de notificación
       const notificacion = document.createElement('div');
       notificacion.className = `alert alert-${this.obtenerClaseNotificacion(tipo)} alert-dismissible fade show notification-toast`;
       notificacion.style.cssText = `
           position: fixed;
           top: 20px;
           right: 20px;
           z-index: 9999;
           min-width: 300px;
           box-shadow: 0 4px 12px rgba(0,0,0,0.15);
       `;
       
       notificacion.innerHTML = `
           <div class="d-flex align-items-center">
               <i class="bi ${this.obtenerIconoNotificacion(tipo)} me-2"></i>
               <span>${mensaje}</span>
           </div>
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       `;
       
       // Agregar al DOM
       document.body.appendChild(notificacion);
       
       // Auto-remover después de 5 segundos
       setTimeout(() => {
           if (notificacion.parentNode) {
               notificacion.remove();
           }
       }, 5000);
       
       console.log(`📢 ${tipo.toUpperCase()}: ${mensaje}`);
   }
   
   obtenerClaseNotificacion(tipo) {
       const clases = {
           'success': 'success',
           'error': 'danger',
           'warning': 'warning',
           'info': 'info'
       };
       return clases[tipo] || 'info';
   }
   
   obtenerIconoNotificacion(tipo) {
       const iconos = {
           'success': 'bi-check-circle-fill',
           'error': 'bi-exclamation-triangle-fill',
           'warning': 'bi-exclamation-circle-fill',
           'info': 'bi-info-circle-fill'
       };
       return iconos[tipo] || 'bi-info-circle-fill';
   }
   
   /**
    * Sistema de loading
    */
   mostrarLoading(mensaje = 'Cargando...') {
       // Remover loading anterior si existe
       this.ocultarLoading();
       
       const loadingOverlay = document.createElement('div');
       loadingOverlay.id = 'loadingOverlay';
       loadingOverlay.className = 'loading-overlay';
       loadingOverlay.innerHTML = `
           <div class="loading-content">
               <div class="spinner-border text-primary mb-3" role="status">
                   <span class="visually-hidden">Cargando...</span>
               </div>
               <h5>${mensaje}</h5>
               <p class="text-muted mb-0">Por favor espera...</p>
           </div>
       `;
       
       document.body.appendChild(loadingOverlay);
       document.body.style.overflow = 'hidden';
   }
   
   ocultarLoading() {
       const loadingOverlay = document.getElementById('loadingOverlay');
       if (loadingOverlay) {
           loadingOverlay.remove();
           document.body.style.overflow = '';
       }
   }
   
   /**
    * Funciones de validación
    */
   validarFormularioEstado() {
       const nuevoEstado = document.getElementById('nuevo_estado').value;
       
       if (!nuevoEstado) {
           this.mostrarNotificacion('warning', 'Debe seleccionar un nuevo estado');
           return false;
       }
       
       // Validar transiciones de estado permitidas
       const estadoActual = this.denunciaSeleccionada?.id_estado_denuncia;
       
       if (!this.esTransicionValida(estadoActual, nuevoEstado)) {
           this.mostrarNotificacion('warning', 'Esta transición de estado no está permitida');
           return false;
       }
       
       return true;
   }
   
   esTransicionValida(estadoActual, nuevoEstado) {
       // Definir transiciones válidas
       const transicionesValidas = {
           1: [2, 6], // Pendiente -> En Revisión, Rechazado
           2: [3, 4, 5, 6], // En Revisión -> En Proceso, Resuelto, Cerrado, Rechazado
           3: [4, 5, 6], // En Proceso -> Resuelto, Cerrado, Rechazado
           4: [5], // Resuelto -> Cerrado
           5: [], // Cerrado (final)
           6: [] // Rechazado (final)
       };
       
       const estadoActualNum = parseInt(estadoActual);
       const nuevoEstadoNum = parseInt(nuevoEstado);
       
       // Administradores pueden hacer cualquier transición
       if (this.data.id_rol === 1) {
           return true;
       }
       
       return transicionesValidas[estadoActualNum]?.includes(nuevoEstadoNum) || false;
   }
   
   /**
    * Funciones de teclado
    */
   configurarShortcuts() {
       document.addEventListener('keydown', (e) => {
           // Ctrl/Cmd + R para refrescar
           if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
               e.preventDefault();
               this.refrescarDatos();
           }
           
           // ESC para cerrar modales
           if (e.key === 'Escape') {
               $('.modal.show').modal('hide');
           }
           
           // F para enfocar filtros
           if (e.key === 'f' && !e.ctrlKey && !e.metaKey) {
               const target = e.target;
               if (target.tagName !== 'INPUT' && target.tagName !== 'TEXTAREA') {
                   e.preventDefault();
                   document.getElementById('filtro_estado')?.focus();
               }
           }
       });
   }
   
   /**
    * Funciones de accesibilidad
    */
   configurarAccesibilidad() {
       // Agregar ARIA labels
       $('[data-bs-toggle="tooltip"]').tooltip();
       
       // Mejorar navegación por teclado
       $('.action-btn').attr('tabindex', '0');
       $('.action-btn').on('keypress', function(e) {
           if (e.which === 13) { // Enter
               $(this).click();
           }
       });
       
       // Anunciar cambios importantes
       this.anunciarCambios();
   }
   
   anunciarCambios() {
       // Crear región live para screen readers
       if (!document.getElementById('live-region')) {
           const liveRegion = document.createElement('div');
           liveRegion.id = 'live-region';
           liveRegion.setAttribute('aria-live', 'polite');
           liveRegion.setAttribute('aria-atomic', 'true');
           liveRegion.style.cssText = 'position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden;';
           document.body.appendChild(liveRegion);
       }
   }
   
   anunciar(mensaje) {
       const liveRegion = document.getElementById('live-region');
       if (liveRegion) {
           liveRegion.textContent = mensaje;
       }
   }
}

/**
* Funciones globales de utilidad
*/
window.GestionDenunciasUtils = {
   /**
    * Formatear número con separadores de miles
    */
   formatearNumero(numero) {
       return new Intl.NumberFormat('es-EC').format(numero);
   },
   
   /**
    * Copiar texto al portapapeles
    */
   async copiarAlPortapapeles(texto) {
       try {
           await navigator.clipboard.writeText(texto);
           gestionDenuncias.mostrarNotificacion('success', 'Copiado al portapapeles');
       } catch (error) {
           console.error('Error copiando al portapapeles:', error);
           gestionDenuncias.mostrarNotificacion('error', 'Error al copiar');
       }
   },
   
   /**
    * Descargar datos como JSON
    */
   descargarJSON(datos, nombreArchivo) {
       const blob = new Blob([JSON.stringify(datos, null, 2)], { type: 'application/json' });
       const url = URL.createObjectURL(blob);
       const a = document.createElement('a');
       a.href = url;
       a.download = nombreArchivo;
       document.body.appendChild(a);
       a.click();
       document.body.removeChild(a);
       URL.revokeObjectURL(url);
   },
   
   /**
    * Validar email
    */
   validarEmail(email) {
       const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
       return regex.test(email);
   },
   
   /**
    * Generar ID único
    */
   generarId() {
       return '_' + Math.random().toString(36).substr(2, 9);
   }
};

/**
* Inicialización cuando el DOM esté listo
*/
document.addEventListener('DOMContentLoaded', () => {
   // Verificar que jQuery y Bootstrap estén cargados
   if (typeof $ === 'undefined') {
       console.error('❌ jQuery no está cargado');
       return;
   }
   
   if (typeof bootstrap === 'undefined') {
       console.error('❌ Bootstrap no está cargado');
       return;
   }
   
   // Inicializar gestión de denuncias
   window.gestionDenuncias = new GestionDenuncias();
   
   // Configurar shortcuts y accesibilidad
   if (window.gestionDenuncias) {
       window.gestionDenuncias.configurarShortcuts();
       window.gestionDenuncias.configurarAccesibilidad();
   }
});

/**
* Manejo de errores globales
*/
window.addEventListener('error', (e) => {
   console.error('Error global:', e);
   if (window.gestionDenuncias) {
       window.gestionDenuncias.mostrarNotificacion('error', 'Se produjo un error inesperado');
   }
});

/**
* Manejo de errores de promesas no capturadas
*/
window.addEventListener('unhandledrejection', (e) => {
   console.error('Promise rechazada:', e);
   if (window.gestionDenuncias) {
       window.gestionDenuncias.mostrarNotificacion('error', 'Error de conexión o servidor');
   }
});

/**
* Performance monitoring
*/
if ('performance' in window) {
   window.addEventListener('load', () => {
       setTimeout(() => {
           const perfData = performance.timing;
           const loadTime = perfData.loadEventEnd - perfData.navigationStart;
           console.log(`⚡ Tiempo de carga total: ${loadTime}ms`);
           
           if (loadTime > 5000) {
               console.warn('⚠️ Tiempo de carga lento detectado');
           }
       }, 0);
   });
}

/**
* Exportar para uso global
*/
if (typeof module !== 'undefined' && module.exports) {
   module.exports = GestionDenuncias;
}
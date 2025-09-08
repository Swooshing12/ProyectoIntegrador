// js/panel_denuncias.js

class PanelDenunciasManager {
    constructor() {
        this.baseUrl = '/ProyectoIntegrador/controladores/ResponsableControlador/ResponsableController.php';
        this.init();
    }
    
    init() {
        this.configurarEventos();
        console.log('Panel de Denuncias inicializado');
    }
    
    configurarEventos() {
        // Formulario cambiar estado
        const formCambiarEstado = document.getElementById('formCambiarEstado');
        if (formCambiarEstado) {
            formCambiarEstado.addEventListener('submit', (e) => {
                e.preventDefault();
                this.procesarCambioEstado();
            });
        }
        
        // Formulario marcar resuelto
        const formMarcarResuelto = document.getElementById('formMarcarResuelto');
        if (formMarcarResuelto) {
            formMarcarResuelto.addEventListener('submit', (e) => {
                e.preventDefault();
                this.procesarCambioEstado();
            });
        }
    }
    
    async verDetalle(id_denuncia) {
        try {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
            
            const response = await fetch(`${this.baseUrl}?action=verDetalle&id=${id_denuncia}`);
            const resultado = await response.json();
            
            if (resultado.success) {
                await this.cargarDetalleCompleto(resultado.data);
            } else {
                document.getElementById('contenidoDetalle').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${resultado.message}
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error al cargar el detalle');
        }
    }
    
    async cargarDetalleCompleto(denuncia) {
        try {
            // Cargar evidencias y seguimiento en paralelo
            const [evidenciasResponse, seguimientoResponse] = await Promise.all([
                fetch(`${this.baseUrl}?action=obtenerEvidencias&id=${denuncia.id_denuncia}`),
                fetch(`${this.baseUrl}?action=obtenerSeguimiento&id=${denuncia.id_denuncia}`)
            ]);
            
            const evidenciasData = await evidenciasResponse.json();
            const seguimientoData = await seguimientoResponse.json();
            
            const evidencias = evidenciasData.success ? evidenciasData.data : [];
            const seguimiento = seguimientoData.success ? seguimientoData.data : [];
            
            // Generar HTML del detalle
            const html = this.generarHTMLDetalle(denuncia, evidencias, seguimiento);
            document.getElementById('contenidoDetalle').innerHTML = html;
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('contenidoDetalle').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar los detalles completos
                </div>
            `;
        }
    }
    
    generarHTMLDetalle(denuncia, evidencias, seguimiento) {
        return `
            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-file-text me-2"></i>
                                Información de la Denuncia
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Número:</strong> ${denuncia.numero_denuncia}<br>
                                    <strong>Título:</strong> ${denuncia.titulo}<br>
                                    <strong>Categoría:</strong> ${denuncia.nombre_categoria}<br>
                                    <strong>Tipo:</strong> ${denuncia.tipo_principal}<br>
                                    <strong>Gravedad:</strong> <span class="badge bg-warning">${denuncia.gravedad}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Estado:</strong> 
                                    <span class="badge" style="background-color: ${denuncia.color}; color: white;">
                                        ${denuncia.nombre_estado}
                                    </span><br>
                                    <strong>Prioridad:</strong> <span class="badge priority-${denuncia.prioridad}">${denuncia.prioridad}</span><br>
                                    <strong>Fecha Creación:</strong> ${new Date(denuncia.fecha_creacion).toLocaleString()}<br>
                                    <strong>Última Actualización:</strong> ${new Date(denuncia.fecha_actualizacion).toLocaleString()}
                                </div>
                            </div>
                            <hr>
                            <strong>Descripción:</strong>
                            <p class="mt-2">${denuncia.descripcion}</p>
                            
                            ${denuncia.informacion_adicional_denunciado ? `
                                <strong>Información Adicional:</strong>
                                <p class="mt-2">${denuncia.informacion_adicional_denunciado}</p>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Ubicación -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt me-2"></i>
                                Ubicación
                            </h6>
                        </div>
                        <div class="card-body">
                            <strong>Provincia:</strong> ${denuncia.provincia || 'No especificada'}<br>
                            <strong>Cantón:</strong> ${denuncia.canton || 'No especificado'}<br>
                            <strong>Parroquia:</strong> ${denuncia.parroquia || 'No especificada'}<br>
                            ${denuncia.direccion_especifica ? `<strong>Dirección:</strong> ${denuncia.direccion_especifica}<br>` : ''}
                        </div>
                    </div>
                    
                    <!-- Denunciante -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-person me-2"></i>
                                Información del Denunciante
                            </h6>
                        </div>
                        <div class="card-body">
                            <strong>Nombre:</strong> ${denuncia.denunciante_nombres} ${denuncia.denunciante_apellidos}<br>
                            <strong>Correo:</strong> ${denuncia.denunciante_correo}<br>
                            ${denuncia.denunciante_telefono ? `<strong>Teléfono:</strong> ${denuncia.denunciante_telefono}<br>` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- Panel Lateral -->
                <div class="col-md-4">
                    <!-- Acciones Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>
                                Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <button class="btn btn-warning btn-sm" onclick="mostrarCambiarEstado(${denuncia.id_denuncia})">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Cambiar Estado
                            </button>
                            
                            ${denuncia.id_estado_denuncia == 3 ? `
                                <button class="btn btn-success btn-sm" onclick="marcarResuelto(${denuncia.id_denuncia})">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Marcar Resuelto
                                </button>
                            ` : ''}
                            
                            <button class="btn btn-info btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                    
                    <!-- Evidencias -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-paperclip me-2"></i>
                                Evidencias (${evidencias.length})
                            </h6>
                        </div>
                        <div class="card-body">
                            ${evidencias.length > 0 ? evidencias.map(evidencia => `
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-${this.getIconoEvidencia(evidencia.tipo_evidencia)} me-2"></i>
                                    <div class="flex-grow-1">
                                        <small class="fw-bold">${evidencia.nombre_archivo}</small><br>
                                        <small class="text-muted">${evidencia.tipo_evidencia}</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="window.open('../../${evidencia.ruta_archivo}', '_blank')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            `).join('') : '<p class="text-muted small">No hay evidencias adjuntas</p>'}
                        </div>
                    </div>
                    
                    <!-- Seguimiento -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Historial de Seguimiento
                            </h6>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            ${seguimiento.length > 0 ? seguimiento.map(item => `
                                <div class="border-start border-3 ps-3 mb-3" style="border-color: ${item.estado_nuevo_color} !important;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="small">${item.estado_nuevo_nombre}</strong>
                                        <small class="text-muted">${new Date(item.fecha_actualizacion).toLocaleDateString()}</small>
                                    </div>
                                    <small class="text-muted">Por: ${item.nombres} ${item.apellidos}</small>
                                    ${item.comentario ? `<p class="small mt-1 mb-0">${item.comentario}</p>` : ''}
                                </div>
                            `).join('') : '<p class="text-muted small">No hay seguimiento registrado</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    getIconoEvidencia(tipo) {
        const iconos = {
            'FOTO': 'image',
            'VIDEO': 'camera-video',
            'DOCUMENTO': 'file-earmark-text',
            'AUDIO': 'file-earmark-music'
        };
        return iconos[tipo] || 'file-earmark';
    }
    
    mostrarCambiarEstado(id_denuncia) {
        document.getElementById('cambio_id_denuncia').value = id_denuncia;
        document.getElementById('cambio_nuevo_estado').value = '';
        document.getElementById('cambio_comentario').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalCambiarEstado'));
        modal.show();
    }
    
    marcarResuelto(id_denuncia) {
        document.getElementById('resuelto_id_denuncia').value = id_denuncia;
        document.getElementById('resuelto_comentario').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalResuelto'));
        modal.show();
    }
    
    async cambiarEstadoRapido(id_denuncia, nuevo_estado) {
        const confirmacion = await Swal.fire({
            title: '¿Cambiar estado?',
            text: 'La denuncia pasará a "En Proceso"',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        });
        
        if (!confirmacion.isConfirmed) return;
        
        try {
            const formData = new FormData();
            formData.append('action', 'cambiarEstado');
            formData.append('id_denuncia', id_denuncia);
            formData.append('nuevo_estado', nuevo_estado);
            formData.append('comentario', 'Cambio rápido a En Proceso');
            
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
        }
    }
    
    async procesarCambioEstado() {
        const form = event.target;
        const formData = new FormData(form);
        formData.append('action', 'cambiarEstado');
        
        // Validaciones
        if (!formData.get('nuevo_estado')) {
            this.mostrarAlerta('warning', 'Debe seleccionar un estado');
            return;
        }
        
        try {
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                // Cerrar modal activo
                const modales = ['modalCambiarEstado', 'modalResuelto', 'modalDetalle'];
                modales.forEach(modalId => {
                    const modalElement = document.getElementById(modalId);
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                });
                
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
        }
    }
    
    mostrarAlerta(tipo, mensaje) {
        const iconos = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };
        
        Swal.fire({
            icon: iconos[tipo] || 'info',
            title: mensaje,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end'
        });
    }
}

// Funciones globales
function verDetalle(id_denuncia) {
    if (window.panelManager) {
        window.panelManager.verDetalle(id_denuncia);
    }
}

function mostrarCambiarEstado(id_denuncia) {
    if (window.panelManager) {
        window.panelManager.mostrarCambiarEstado(id_denuncia);
    }
}

function marcarResuelto(id_denuncia) {
    if (window.panelManager) {
        window.panelManager.marcarResuelto(id_denuncia);
    }
}

function cambiarEstadoRapido(id_denuncia, nuevo_estado) {
    if (window.panelManager) {
        window.panelManager.cambiarEstadoRapido(id_denuncia, nuevo_estado);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.panelManager = new PanelDenunciasManager();
});
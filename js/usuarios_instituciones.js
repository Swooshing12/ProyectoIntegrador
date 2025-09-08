// js/usuarios_instituciones.js

class UsuariosInstitucionesManager {
    constructor() {
        // ✅ RUTA CORREGIDA - Desde js/ hasta controladores/
        this.baseUrl = '../../../controladores/UsuariosInstitucionesControlador/UsuariosInstitucionesController.php';
        this.init();
    }
    
    init() {
        this.configurarEventos();
        console.log('✅ Gestión Usuarios-Instituciones inicializada');
        console.log('🔗 Base URL:', this.baseUrl);
    }
    
    configurarEventos() {
        // Formulario crear
        const formCrear = document.getElementById('formCrear');
        if (formCrear) {
            formCrear.addEventListener('submit', (e) => {
                e.preventDefault();
                this.crearAsignacion();
            });
        }
        
        // Formulario editar  
        const formEditar = document.getElementById('formEditar');
        if (formEditar) {
            formEditar.addEventListener('submit', (e) => {
                e.preventDefault();
                this.editarAsignacion();
            });
        }
        
        // Formulario de la vista principal (formAsignar)
        const formAsignar = document.getElementById('formAsignar');
        if (formAsignar) {
            formAsignar.addEventListener('submit', (e) => {
                e.preventDefault();
                this.crearAsignacion();
            });
        }
        
        console.log('✅ Eventos configurados');
    }
    
    async crearAsignacion() {
        // Buscar el formulario activo
        const form = document.getElementById('formAsignar') || document.getElementById('formCrear');
        
        if (!form) {
            console.error('❌ No se encontró el formulario');
            this.mostrarAlerta('error', 'Error: Formulario no encontrado');
            return;
        }
        
        const formData = new FormData(form);
        formData.append('action', 'crear');
        
        // Debug
        console.log('📝 Datos del formulario:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
        // Validaciones
        if (!formData.get('id_usuario')) {
            this.mostrarAlerta('warning', 'Debe seleccionar un usuario');
            return;
        }
        
        if (!formData.get('id_institucion')) {
            this.mostrarAlerta('warning', 'Debe seleccionar una institución');
            return;
        }
        
        this.mostrarLoading('Creando asignación...');
        
        try {
            console.log('🚀 Enviando a:', this.baseUrl);
            
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                body: formData
            });
            
            console.log('📡 Respuesta del servidor:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const resultado = await response.json();
            console.log('📦 Resultado:', resultado);
            
            if (resultado.success) {
                // Cerrar modal si existe
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAsignar')) ||
                              bootstrap.Modal.getInstance(document.getElementById('modalCrear'));
                if (modal) {
                    modal.hide();
                }
                
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('❌ Error:', error);
            this.mostrarAlerta('error', `Error de conexión: ${error.message}`);
        } finally {
            Swal.close();
        }
    }
    
    async editarAsignacion() {
        const form = document.getElementById('formEditar');
        
        if (!form) {
            this.mostrarAlerta('error', 'Error: Formulario de edición no encontrado');
            return;
        }
        
        const formData = new FormData(form);
        formData.append('action', 'actualizar');
        
        this.mostrarLoading('Actualizando asignación...');
        
        try {
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const resultado = await response.json();
            
            if (resultado.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
                if (modal) {
                    modal.hide();
                }
                
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('❌ Error:', error);
            this.mostrarAlerta('error', `Error de conexión: ${error.message}`);
        } finally {
            Swal.close();
        }
    }
    
    async cargarDatosEdicion(id) {
        this.mostrarLoading('Cargando datos...');
        
        try {
            const response = await fetch(`${this.baseUrl}?action=obtenerPorId&id=${id}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const resultado = await response.json();
            
            if (resultado.success && resultado.data) {
                const datos = resultado.data;
                
                // Llenar formulario
                const editForm = document.getElementById('formEditar');
                if (editForm) {
                    editForm.querySelector('#edit_id_usuario_institucion').value = datos.id_usuario_institucion;
                    editForm.querySelector('#edit_es_responsable_principal').checked = datos.es_responsable_principal == 1;
                    editForm.querySelector('#edit_estado_asignacion').value = datos.estado_asignacion;
                    editForm.querySelector('#edit_comentarios').value = datos.comentarios || '';
                }
                
                // Mostrar info actual
                const infoDiv = document.getElementById('info_asignacion_actual');
                if (infoDiv) {
                    infoDiv.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Usuario:</strong> ${datos.nombres} ${datos.apellidos}<br>
                                <strong>Username:</strong> @${datos.username}
                            </div>
                            <div class="col-md-6">
                                <strong>Institución:</strong> ${datos.nombre_institucion}<br>
                                <strong>Siglas:</strong> ${datos.siglas}
                            </div>
                        </div>
                    `;
                }
                
                // Mostrar modal
                const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
                modalEditar.show();
            } else {
                this.mostrarAlerta('error', 'Error obteniendo datos');
            }
            
        } catch (error) {
            console.error('❌ Error:', error);
            this.mostrarAlerta('error', `Error de conexión: ${error.message}`);
        } finally {
            Swal.close();
        }
    }
    
    async eliminarAsignacion(id) {
        const confirmacion = await Swal.fire({
            title: '¿Eliminar Asignación?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        });
        
        if (!confirmacion.isConfirmed) return;
        
        this.mostrarLoading('Eliminando asignación...');
        
        try {
            const formData = new FormData();
            formData.append('action', 'eliminar');
            formData.append('id', id);
            
            console.log('🗑️ Eliminando ID:', id);
            
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const resultado = await response.json();
            console.log('📦 Resultado eliminación:', resultado);
            
            if (resultado.success) {
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('❌ Error:', error);
            this.mostrarAlerta('error', `Error de conexión: ${error.message}`);
        } finally {
            Swal.close();
        }
    }
    
    mostrarAlerta(tipo, mensaje) {
        const iconos = {
            'success': 'success',
            'error': 'error', 
            'warning': 'warning',
            'info': 'info'
        };
        
        const colores = {
            'success': '#28a745',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#17a2b8'
        };
        
        Swal.fire({
            icon: iconos[tipo] || 'info',
            title: mensaje,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            background: '#fff',
            color: colores[tipo] || '#333',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
    
    mostrarLoading(mensaje) {
        Swal.fire({
            title: mensaje,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            background: '#fff',
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // ✅ NUEVO: Método para verificar conectividad
    async verificarConectividad() {
        try {
            console.log('🔍 Verificando conectividad con:', this.baseUrl);
            
            const response = await fetch(`${this.baseUrl}?action=ping`);
            
            if (response.ok) {
                console.log('✅ Conexión exitosa con el controlador');
                return true;
            } else {
                console.warn('⚠️ Problema de conectividad:', response.status);
                return false;
            }
        } catch (error) {
            console.error('❌ Error de conectividad:', error);
            return false;
        }
    }
    
}


// Funciones globales para los botones (mantenemos compatibilidad)
function editarAsignacion(id) {
    if (window.usuariosInstitucionesManager) {
        window.usuariosInstitucionesManager.cargarDatosEdicion(id);
    } else {
        console.error('❌ Manager no inicializado');
    }
}

function eliminarAsignacion(id) {
    if (window.usuariosInstitucionesManager) {
        window.usuariosInstitucionesManager.eliminarAsignacion(id);
    } else {
        console.error('❌ Manager no inicializado');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Inicializando UsuariosInstitucionesManager...');
    
    try {
        window.usuariosInstitucionesManager = new UsuariosInstitucionesManager();
        
        // Verificar conectividad inicial
        setTimeout(() => {
            if (window.usuariosInstitucionesManager) {
                window.usuariosInstitucionesManager.verificarConectividad();
            }
        }, 1000);
        
    } catch (error) {
        console.error('❌ Error inicializando manager:', error);
    }
});

// ✅ NUEVO: Debug global
window.debugUsuariosInstituciones = () => {
    console.log('=== DEBUG USUARIOS INSTITUCIONES ===');
    console.log('Manager:', window.usuariosInstitucionesManager);
    console.log('Base URL:', window.usuariosInstitucionesManager?.baseUrl);
    console.log('Formularios encontrados:');
    console.log('  - formAsignar:', !!document.getElementById('formAsignar'));
    console.log('  - formCrear:', !!document.getElementById('formCrear'));
    console.log('  - formEditar:', !!document.getElementById('formEditar'));
    console.log('=======================================');
};
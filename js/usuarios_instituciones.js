// js/usuarios_instituciones.js

class UsuariosInstitucionesManager {
    constructor() {
        this.baseUrl = '../../../controladores/UsuariosInstitucionesController.php';
        this.init();
    }
    
    init() {
        this.configurarEventos();
        console.log('✅ Gestión Usuarios-Instituciones inicializada');
    }
    
    configurarEventos() {
        // Formulario crear
        $('#formCrear').on('submit', (e) => {
            e.preventDefault();
            this.crearAsignacion();
        });
        
        // Formulario editar
        $('#formEditar').on('submit', (e) => {
            e.preventDefault();
            this.editarAsignacion();
        });
    }
    
    async crearAsignacion() {
        const formData = new FormData(document.getElementById('formCrear'));
        
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
            const response = await fetch(`${this.baseUrl}?action=crear`, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                $('#modalCrear').modal('hide');
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
        } finally {
            Swal.close();
        }
    }
    
    async editarAsignacion() {
        const formData = new FormData(document.getElementById('formEditar'));
        
        this.mostrarLoading('Actualizando asignación...');
        
        try {
            const response = await fetch(`${this.baseUrl}?action=editar`, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                $('#modalEditar').modal('hide');
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
        } finally {
            Swal.close();
        }
    }
    
    async cargarDatosEdicion(id) {
        this.mostrarLoading('Cargando datos...');
        
        try {
            const response = await fetch(`${this.baseUrl}?action=obtener&id=${id}`);
            const resultado = await response.json();
            
            if (resultado.success && resultado.data) {
                const datos = resultado.data;
                
                // Llenar formulario
                document.getElementById('edit_id_usuario_institucion').value = datos.id_usuario_institucion;
                document.getElementById('edit_es_responsable_principal').checked = datos.es_responsable_principal == 1;
                document.getElementById('edit_estado_asignacion').value = datos.estado_asignacion;
                document.getElementById('edit_comentarios').value = datos.comentarios || '';
                
                // Mostrar info actual
                document.getElementById('info_asignacion_actual').innerHTML = `
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
                
                $('#modalEditar').modal('show');
            } else {
                this.mostrarAlerta('error', 'Error obteniendo datos');
            }
            
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
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
            cancelButtonText: 'Cancelar'
        });
        
        if (!confirmacion.isConfirmed) return;
        
        this.mostrarLoading('Eliminando asignación...');
        
        try {
            const formData = new FormData();
            formData.append('id', id);
            
            const response = await fetch(`${this.baseUrl}?action=eliminar`, {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                this.mostrarAlerta('success', resultado.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.mostrarAlerta('error', resultado.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión');
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
    
    mostrarLoading(mensaje) {
        Swal.fire({
            title: mensaje,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
}

// Funciones globales para los botones
function editarAsignacion(id) {
    usuariosInstitucionesManager.cargarDatosEdicion(id);
}

function eliminarAsignacion(id) {
    usuariosInstitucionesManager.eliminarAsignacion(id);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.usuariosInstitucionesManager = new UsuariosInstitucionesManager();
});
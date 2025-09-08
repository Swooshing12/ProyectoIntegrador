<?php
// controladores/GestionDenuncias/GestionDenunciasController.php

require_once __DIR__ . "/../../modelos/GestionDenuncias.php";
require_once __DIR__ . "/../../modelos/Permisos.php";

class GestionDenunciasController {
    private $gestionModel;
    private $permisosModel;
    private $id_usuario;
    private $id_rol;
    
    public function __construct() {
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: ../../login.php');
            exit;
        }
        
        $this->gestionModel = new GestionDenuncias();
        $this->permisosModel = new Permisos();
        $this->id_usuario = $_SESSION['id_usuario'];
        $this->id_rol = $_SESSION['id_rol'];
    }
    
    /**
     * Mostrar página principal
     */
    public function index() {
        try {
            // Verificar permisos básicos (solo para roles autorizados)
            if (!in_array($this->id_rol, [1, 76, 77])) {
                header('Location: ../../vistas/sin_permisos.php');
                exit;
            }
            
            // Obtener datos para la vista
            $estadisticas = $this->gestionModel->obtenerEstadisticas();
            $categorias = $this->gestionModel->obtenerCategorias();
            $estados = $this->gestionModel->obtenerEstados();
            $instituciones = $this->gestionModel->obtenerInstituciones();
            
            // Filtros iniciales según el rol
            $filtros = $this->obtenerFiltrosSegunRol();
            $denuncias = $this->gestionModel->obtenerDenunciasConFiltros($filtros);
            
            // Permisos simulados (puedes ajustar según tu sistema)
            $permisos = [
                'puede_leer' => true,
                'puede_crear' => in_array($this->id_rol, [1, 76]),
                'puede_editar' => in_array($this->id_rol, [1, 76, 77]),
                'puede_eliminar' => in_array($this->id_rol, [1])
            ];
            
            // Datos para la vista
            $data = [
                'titulo' => 'Gestión de Denuncias - EcoReport',
                'estadisticas' => $estadisticas,
                'denuncias' => $denuncias,
                'categorias' => $categorias,
                'estados' => $estados,
                'instituciones' => $instituciones,
                'permisos' => $permisos,
                'filtros_activos' => $filtros,
                'id_usuario' => $this->id_usuario,
                'id_rol' => $this->id_rol,
                'nombre_usuario' => $_SESSION['username'],
                'rol_usuario' => $_SESSION['nombre_rol'] ?? 'Usuario'
            ];
            
            // Incluir la vista
            include __DIR__ . '/../../vistas/admin/denuncias/gestion_denuncias.php';
            
        } catch (Exception $e) {
            die("Error al cargar la página: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener filtros según el rol del usuario
     */
    private function obtenerFiltrosSegunRol() {
        $filtros = [];
        
        switch ($this->id_rol) {
            case 76: // Supervisor Denuncias
                // Ve todas las denuncias
                break;
                
            case 77: // Responsable Institucional
            // Solo ve denuncias de su(s) institución(es)
            $instituciones = $this->gestionModel->obtenerInstitucionesUsuario($this->id_usuario);
            if (!empty($instituciones)) {
                $ids_instituciones = array_column($instituciones, 'id_institucion');
                $filtros['instituciones'] = $ids_instituciones;
            } else {
                // Si no tiene instituciones asignadas, no ve nada
                $filtros['sin_datos'] = true;
            }
            break;
            case 1: // Administrador
                // Ve todas las denuncias
                break;
                
            default:
                // Otros roles: solo denuncias pendientes
                $filtros['estado'] = 1;
                break;
        }
        
        return $filtros;
    }
    
    /**
     * Filtrar denuncias por AJAX
     */
    public function filtrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            // Construir filtros desde el POST
            $filtros = [];
            
            if (!empty($_POST['estado'])) {
                $filtros['estado'] = (int)$_POST['estado'];
            }
            
            if (!empty($_POST['categoria'])) {
                $filtros['categoria'] = (int)$_POST['categoria'];
            }
            
            if (!empty($_POST['tipo'])) {
                $filtros['tipo'] = $_POST['tipo'];
            }
            
            if (!empty($_POST['fecha_desde'])) {
                $filtros['fecha_desde'] = $_POST['fecha_desde'];
            }
            
            if (!empty($_POST['fecha_hasta'])) {
                $filtros['fecha_hasta'] = $_POST['fecha_hasta'];
            }
            
            if (!empty($_POST['gravedad'])) {
                $filtros['gravedad'] = $_POST['gravedad'];
            }
            
            // Aplicar filtros según rol
            $filtros = array_merge($filtros, $this->obtenerFiltrosSegunRol());
            
            // Obtener denuncias filtradas
            $denuncias = $this->gestionModel->obtenerDenunciasConFiltros($filtros);
            
            $this->responderJSON([
                'success' => true,
                'data' => $denuncias,
                'total' => count($denuncias)
            ]);
            
        } catch (Exception $e) {
            error_log("Error filtrando denuncias: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
    
    /**
     * Cambiar estado de denuncia
     */
    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            // Validar datos requeridos
            $id_denuncia = (int)($_POST['id_denuncia'] ?? 0);
            $nuevo_estado = (int)($_POST['nuevo_estado'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');
            
            if ($id_denuncia <= 0 || $nuevo_estado <= 0) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ]);
                return;
            }
            
            // Verificar permisos
            if (!$this->puedeModificarDenuncia($id_denuncia)) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'No tienes permisos para modificar esta denuncia'
                ]);
                return;
            }
            
            // Cambiar estado
            $resultado = $this->gestionModel->cambiarEstado(
                $id_denuncia,
                $nuevo_estado,
                $comentario,
                $this->id_usuario
            );
            
            if ($resultado) {
                $this->responderJSON([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente'
                ]);
            } else {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error cambiando estado: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
    
    /**
     * Asignar institución a denuncia
     */
    public function asignarInstitucion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            // Validar datos requeridos
            $id_denuncia = (int)($_POST['id_denuncia'] ?? 0);
            $id_institucion = (int)($_POST['id_institucion'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');
            $prioridad = $_POST['prioridad'] ?? 'MEDIA';
            
            if ($id_denuncia <= 0 || $id_institucion <= 0) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ]);
                return;
            }
            
            // Solo supervisores pueden asignar
            if ($this->id_rol != 76 && $this->id_rol != 1) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'No tienes permisos para asignar denuncias'
                ]);
                return;
            }
            
            // Asignar institución
            $resultado = $this->gestionModel->asignarInstitucion(
                $id_denuncia,
                $id_institucion,
                $this->id_usuario,
                $comentario,
                $prioridad
            );
            
            if ($resultado) {
                $this->responderJSON([
                    'success' => true,
                    'message' => 'Denuncia asignada correctamente'
                ]);
            } else {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Error al asignar la denuncia'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error asignando institución: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
    
    /**
     * Obtener detalles de una denuncia
     */
    public function obtenerDetalle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $id_denuncia = (int)($_GET['id'] ?? 0);
            
            if ($id_denuncia <= 0) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'ID de denuncia inválido'
                ]);
                return;
            }
            
            // Obtener denuncia con filtros (incluye verificación de permisos)
            $filtros = $this->obtenerFiltrosSegunRol();
            $denuncias = $this->gestionModel->obtenerDenunciasConFiltros($filtros);
            
            // Buscar la denuncia específica
            $denuncia = null;
            foreach ($denuncias as $d) {
                if ($d['id_denuncia'] == $id_denuncia) {
                    $denuncia = $d;
                    break;
                }
            }
            
            if (!$denuncia) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Denuncia no encontrada o sin permisos'
                ]);
                return;
            }
            
            $this->responderJSON([
                'success' => true,
                'data' => $denuncia
            ]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo detalle: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
 * Obtener evidencias de una denuncia
 */
public function obtenerEvidencias() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
        return;
    }
    
    try {
        $id_denuncia = (int)($_GET['id'] ?? 0);
        
        if ($id_denuncia <= 0) {
            $this->responderJSON([
                'success' => false,
                'message' => 'ID de denuncia inválido'
            ]);
            return;
        }
        
        // Obtener evidencias usando ConsultaDenuncia
        require_once __DIR__ . "/../../modelos/ConsultaDenuncia.php";
        $consultaModel = new ConsultaDenuncia();
        $evidencias = $consultaModel->obtenerEvidencias($id_denuncia);
        
        $this->responderJSON([
            'success' => true,
            'data' => $evidencias
        ]);
        
    } catch (Exception $e) {
        error_log("Error obteniendo evidencias: " . $e->getMessage());
        $this->responderJSON([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    }
}

/**
 * Obtener seguimiento de una denuncia
 */
public function obtenerSeguimiento() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
        return;
    }
    
    try {
        $id_denuncia = (int)($_GET['id'] ?? 0);
        
        if ($id_denuncia <= 0) {
            $this->responderJSON([
                'success' => false,
                'message' => 'ID de denuncia inválido'
            ]);
            return;
        }
        
        // Obtener seguimiento usando ConsultaDenuncia
        require_once __DIR__ . "/../../modelos/ConsultaDenuncia.php";
        $consultaModel = new ConsultaDenuncia();
        $seguimiento = $consultaModel->obtenerSeguimiento($id_denuncia);
        
        $this->responderJSON([
            'success' => true,
            'data' => $seguimiento
        ]);
        
    } catch (Exception $e) {
        error_log("Error obteniendo seguimiento: " . $e->getMessage());
        $this->responderJSON([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    }
}
    
    /**
     * Verificar si puede modificar una denuncia
     */
    private function puedeModificarDenuncia($id_denuncia) {
        // Administradores y supervisores pueden modificar cualquier denuncia
        if (in_array($this->id_rol, [1, 76])) {
            return true;
        }
        
        // Responsables institucionales solo pueden modificar denuncias de su institución
        if ($this->id_rol == 77) {
            // TODO: Verificar que la denuncia pertenece a su institución
            return true;
        }
        
        return false;
    }
    
    /**
     * Responder con JSON
     */
    private function responderJSON($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// ✅ SOLO MANEJAR RUTAS SI SE LLAMA DIRECTAMENTE
// ✅ SOLO MANEJAR RUTAS SI SE LLAMA DIRECTAMENTE
if (basename($_SERVER['PHP_SELF']) === 'GestionDenunciasController.php') {
    $controller = new GestionDenunciasController();
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'filtrar':
                $controller->filtrar();
                break;
            case 'cambiar_estado':
                $controller->cambiarEstado();
                break;
            case 'asignar_institucion':
                $controller->asignarInstitucion();
                break;
            case 'obtener_detalle':
                $controller->obtenerDetalle();
                break;
            case 'obtener_evidencias':
                $controller->obtenerEvidencias();
                break;
            case 'obtener_seguimiento':
                $controller->obtenerSeguimiento();
                break;
            default:
                $controller->index();
                break;
        }
    } else {
        $controller->index();
    }
}
?>
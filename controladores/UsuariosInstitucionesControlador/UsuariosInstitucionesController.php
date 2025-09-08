<?php
// controladores/UsuariosInstitucionesControlador/UsuariosInstitucionesController.php

require_once __DIR__ . "/../../modelos/UsuariosInstituciones.php";
require_once __DIR__ . "/../../modelos/Permisos.php";

class UsuariosInstitucionesController {
    private $usuariosInstitucionesModel;
    private $permisosModel;
    private $debug = true; // ← ACTIVAR DEBUG
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // ✅ LOGS DE DEBUG
        error_log("=== UsuariosInstitucionesController::__construct() ===");
        error_log("SESSION ID_ROL: " . ($_SESSION['id_rol'] ?? 'NO_EXISTE'));
        
        $this->usuariosInstitucionesModel = new UsuariosInstituciones();
        $this->permisosModel = new Permisos();
        
        error_log("✅ Modelos inicializados correctamente");
    }
    
    public function manejarSolicitud() {
        error_log("=== manejarSolicitud() ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("GET: " . json_encode($_GET));
        error_log("POST: " . json_encode($_POST));
        
        if (!isset($_SESSION['id_rol'])) {
            error_log("❌ Sin sesión - Redirigiendo a login");
            $this->redirigir('../../login.php');
            exit();
        }
        
        $action = $_GET['action'] ?? $_POST['action'] ?? 'index';
        error_log("ACTION: $action");
        
        try {
            switch ($action) {
                case 'crear':
                    error_log("🔄 Ejecutando crear()");
                    $this->crear();
                    break;
                case 'actualizar':
                    error_log("🔄 Ejecutando actualizar()");
                    $this->actualizar();
                    break;
                case 'eliminar':
                    error_log("🔄 Ejecutando eliminar()");
                    $this->eliminar();
                    break;
                case 'obtenerAsignaciones':
                    error_log("🔄 Ejecutando obtenerAsignaciones()");
                    $this->obtenerAsignaciones();
                    break;
                case 'obtenerPorId':
                    error_log("🔄 Ejecutando obtenerPorId()");
                    $this->obtenerPorId();
                    break;
                case 'ping':
                    error_log("🔄 Ejecutando ping()");
                    $this->ping();
                    break;
                case 'index':
                default:
                    error_log("🔄 Ejecutando index()");
                    $this->index();
                    break;
            }
        } catch (Exception $e) {
            error_log("❌ ERROR en manejarSolicitud: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'debug' => $this->debug ? $e->getTraceAsString() : null
            ]);
        }
    }
    
    public function index() {
        try {
            error_log("=== index() INICIO ===");
            
            // Obtener ID del submenú
            $id_submenu = $_GET['submenu_id'] ?? 42;
            error_log("ID_SUBMENU: $id_submenu");
            
            // Verificar permisos
            $permisos = $this->permisosModel->obtenerPermisos($_SESSION['id_rol'], $id_submenu);
            error_log("PERMISOS: " . json_encode($permisos));
            
            if (!$permisos) {
                error_log("❌ Sin permisos para submenu_id: $id_submenu, rol: " . $_SESSION['id_rol']);
                die("Error: No tienes permisos para acceder a este módulo");
            }
            
            // ✅ OBTENER DATOS CON VERIFICACIÓN
            error_log("🔄 Obteniendo usuarios...");
            $usuarios = $this->usuariosInstitucionesModel->obtenerUsuariosResponsables();
            error_log("USUARIOS OBTENIDOS: " . count($usuarios));
            
            error_log("🔄 Obteniendo instituciones...");
            $instituciones = $this->usuariosInstitucionesModel->obtenerInstituciones();
            error_log("INSTITUCIONES OBTENIDAS: " . count($instituciones));
            
            error_log("🔄 Obteniendo asignaciones...");
            $asignaciones = $this->usuariosInstitucionesModel->obtenerAsignaciones();
            error_log("ASIGNACIONES OBTENIDAS: " . count($asignaciones));
            
            // Preparar datos para la vista
            $datosVista = [
                'usuarios' => $usuarios,
                'instituciones' => $instituciones,
                'asignaciones' => $asignaciones,
                'permisos' => $permisos,
                'id_submenu' => $id_submenu
            ];
            
            error_log("✅ Datos preparados - Enviando a vista");
            
            // Pasar datos a la vista
            extract($datosVista);
            
            // Incluir la vista
            include __DIR__ . '/../../vistas/admin/usuarios_instituciones/index.php';
            
        } catch (Exception $e) {
            error_log("❌ ERROR en index(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            die("Error al cargar la página: " . $e->getMessage());
        }
    }
    
    private function crear() {
        error_log("=== crear() INICIO ===");
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("❌ Método no es POST");
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_institucion = $_POST['id_institucion'] ?? null;
        $es_responsable_principal = isset($_POST['es_responsable_principal']) ? 1 : 0;
        $comentarios = $_POST['comentarios'] ?? '';
        
        error_log("DATOS RECIBIDOS:");
        error_log("  id_usuario: $id_usuario");
        error_log("  id_institucion: $id_institucion");
        error_log("  es_responsable_principal: $es_responsable_principal");
        error_log("  comentarios: $comentarios");
        
        if (!$id_usuario || !$id_institucion) {
            error_log("❌ Faltan datos requeridos");
            $this->responderJSON(['success' => false, 'message' => 'Faltan datos requeridos: usuario e institución']);
            return;
        }
        
        try {
            error_log("🔄 Llamando al modelo para crear asignación...");
            $resultado = $this->usuariosInstitucionesModel->crearAsignacion(
                $id_usuario, 
                $id_institucion, 
                $es_responsable_principal, 
                $comentarios
            );
            
            error_log("RESULTADO DEL MODELO: " . json_encode($resultado));
            $this->responderJSON($resultado);
            
        } catch (Exception $e) {
            error_log("❌ ERROR en crear(): " . $e->getMessage());
            $this->responderJSON([
                'success' => false, 
                'message' => 'Error al crear asignación: ' . $e->getMessage()
            ]);
        }
    }
    
    // ✅ MÉTODO PING PARA TESTING
    private function ping() {
        error_log("=== ping() ===");
        $this->responderJSON([
            'success' => true,
            'message' => 'Controlador funcionando correctamente',
            'timestamp' => date('Y-m-d H:i:s'),
            'session_rol' => $_SESSION['id_rol'] ?? 'NO_SESSION'
        ]);
    }
    
    private function obtenerPorId() {
    error_log("=== obtenerPorId() INICIO ===");
    
    // Intentar obtener ID de GET y POST
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    error_log("ID recibido: " . var_export($id, true));
    error_log("GET: " . json_encode($_GET));
    error_log("POST: " . json_encode($_POST));
    
    if (!$id || !is_numeric($id)) {
        error_log("❌ ID inválido o faltante");
        $this->responderJSON(['success' => false, 'message' => 'ID requerido y debe ser numérico']);
        return;
    }
    
    try {
        $asignacion = $this->usuariosInstitucionesModel->obtenerAsignacionPorId($id);
        
        if ($asignacion) {
            error_log("✅ Asignación encontrada: " . json_encode($asignacion));
            $this->responderJSON(['success' => true, 'data' => $asignacion]);
        } else {
            error_log("⚠️ Asignación no encontrada para ID: $id");
            $this->responderJSON(['success' => false, 'message' => 'Asignación no encontrada']);
        }
    } catch (Exception $e) {
        error_log("❌ Error en obtenerPorId: " . $e->getMessage());
        $this->responderJSON(['success' => false, 'message' => $e->getMessage()]);
    }
}
    
    private function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $es_responsable_principal = isset($_POST['es_responsable_principal']) ? 1 : 0;
        $comentarios = $_POST['comentarios'] ?? '';
        $estado_asignacion = $_POST['estado_asignacion'] ?? 'ACTIVO';
        
        if (!$id) {
            $this->responderJSON(['success' => false, 'message' => 'ID requerido']);
            return;
        }
        
        $resultado = $this->usuariosInstitucionesModel->actualizarAsignacion(
            $id, 
            $es_responsable_principal, 
            $comentarios, 
            $estado_asignacion
        );
        
        $this->responderJSON($resultado);
    }
    
    private function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->responderJSON(['success' => false, 'message' => 'ID requerido']);
            return;
        }
        
        $resultado = $this->usuariosInstitucionesModel->eliminarAsignacion($id);
        $this->responderJSON($resultado);
    }
    
    private function obtenerAsignaciones() {
        try {
            $asignaciones = $this->usuariosInstitucionesModel->obtenerAsignaciones();
            $this->responderJSON(['success' => true, 'data' => $asignaciones]);
        } catch (Exception $e) {
            $this->responderJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    private function responderJSON($data) {
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) ob_clean();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
    
    private function redirigir($url) {
        if (ob_get_length()) ob_clean();
        header("Location: {$url}");
        exit();
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'UsuariosInstitucionesController.php') {
    try {
        error_log("=== ACCESO DIRECTO AL CONTROLADOR ===");
        $controller = new UsuariosInstitucionesController();
        $controller->manejarSolicitud();
    } catch (Throwable $e) {
        error_log("❌ ERROR FATAL: " . $e->getMessage());
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
}
?>
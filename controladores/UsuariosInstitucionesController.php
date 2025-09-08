<?php
// controladores/UsuariosInstitucionesController.php

require_once __DIR__ . "/../modelos/UsuariosInstituciones.php";

class UsuariosInstitucionesController {
    private $model;
    
    public function __construct() {
        session_start();
        
        // Verificar acceso
        if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
            header('Location: ../../login.php');
            exit;
        }
        
        $this->model = new UsuariosInstituciones();
    }
    
    public function index() {
        $usuarios = $this->model->obtenerUsuariosResponsables();
        $instituciones = $this->model->obtenerInstituciones();
        $asignaciones = $this->model->obtenerAsignaciones();
        
        $data = [
            'titulo' => 'Gestión Usuarios-Instituciones',
            'usuarios' => $usuarios,
            'instituciones' => $instituciones,
            'asignaciones' => $asignaciones
        ];
        
        include __DIR__ . '/../vistas/admin/usuarios_instituciones/index.php';    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = (int)$_POST['id_usuario'];
            $id_institucion = (int)$_POST['id_institucion'];
            $es_responsable_principal = isset($_POST['es_responsable_principal']) ? 1 : 0;
            $comentarios = trim($_POST['comentarios'] ?? '');
            
            $resultado = $this->model->crearAsignacion($id_usuario, $id_institucion, $es_responsable_principal, $comentarios);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
        }
    }
    
    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id_usuario_institucion'];
            $es_responsable_principal = isset($_POST['es_responsable_principal']) ? 1 : 0;
            $comentarios = trim($_POST['comentarios'] ?? '');
            $estado_asignacion = $_POST['estado_asignacion'];
            
            $resultado = $this->model->actualizarAsignacion($id, $es_responsable_principal, $comentarios, $estado_asignacion);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
        }
    }
    
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $resultado = $this->model->eliminarAsignacion($id);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
        }
    }
    
    public function obtener() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = (int)$_GET['id'];
            $asignacion = $this->model->obtenerAsignacionPorId($id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $asignacion]);
            exit;
        }
    }
}

// Manejo de rutas
$action = $_GET['action'] ?? 'index';
$controller = new UsuariosInstitucionesController();

switch ($action) {
    case 'crear':
        $controller->crear();
        break;
    case 'editar':
        $controller->editar();
        break;
    case 'eliminar':
        $controller->eliminar();
        break;
    case 'obtener':
        $controller->obtener();
        break;
    default:
        $controller->index();
        break;
}
?>
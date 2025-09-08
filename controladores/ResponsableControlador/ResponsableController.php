<?php
// controladores/ResponsableControlador/ResponsableController.php

require_once __DIR__ . "/../../modelos/PanelDenuncias.php";
require_once __DIR__ . "/../../modelos/Permisos.php";

class ResponsableController {
    private $panelModel;
    private $permisosModel;
    private $debug = false;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->panelModel = new PanelDenuncias();
        $this->permisosModel = new Permisos();
    }
    
    public function manejarSolicitud() {
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 77) {
            $this->redirigir('../../login.php');
            exit();
        }
        
        $action = $_GET['action'] ?? $_POST['action'] ?? 'index';
        
        try {
            switch ($action) {
                case 'cambiarEstado':
                    $this->cambiarEstado();
                    break;
                case 'verDetalle':
                    $this->verDetalle();
                    break;
                case 'obtenerEvidencias':
                    $this->obtenerEvidencias();
                    break;
                case 'obtenerSeguimiento':
                    $this->obtenerSeguimiento();
                    break;
                case 'index':
                default:
                    $this->index();
                    break;
            }
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }
    
    public function index() {
        try {
            $id_usuario = $_SESSION['id_usuario'];
            
            // Obtener filtros
            $filtros = [
                'estado' => $_GET['estado'] ?? '',
                'prioridad' => $_GET['prioridad'] ?? '',
                'tipo' => $_GET['tipo'] ?? '',
                'busqueda' => $_GET['busqueda'] ?? ''
            ];
            
            // Obtener datos
            $denuncias = $this->panelModel->obtenerDenunciasUsuario($id_usuario, $filtros);
            $estados = $this->panelModel->obtenerEstados();
            $estadisticas = $this->panelModel->obtenerEstadisticas($id_usuario);
            
            // Preparar datos para la vista
            $datosVista = [
                'denuncias' => $denuncias,
                'estados' => $estados,
                'estadisticas' => $estadisticas,
                'filtros' => $filtros
            ];
            
            extract($datosVista);
            include __DIR__ . '/../../vistas/responsable/panel_denuncias.php';
            
        } catch (Exception $e) {
            die("Error al cargar la página: " . $e->getMessage());
        }
    }
    
    private function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $id_denuncia = $_POST['id_denuncia'] ?? null;
        $nuevo_estado = $_POST['nuevo_estado'] ?? null;
        $comentario = $_POST['comentario'] ?? '';
        $id_usuario = $_SESSION['id_usuario'];
        
        if (!$id_denuncia || !$nuevo_estado) {
            $this->responderJSON(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        $resultado = $this->panelModel->cambiarEstadoDenuncia($id_denuncia, $nuevo_estado, $id_usuario, $comentario);
        $this->responderJSON($resultado);
    }
    
    private function verDetalle() {
        $id_denuncia = $_GET['id'] ?? null;
        $id_usuario = $_SESSION['id_usuario'];
        
        if (!$id_denuncia) {
            $this->responderJSON(['success' => false, 'message' => 'ID requerido']);
            return;
        }
        
        $detalle = $this->panelModel->obtenerDetalleDenuncia($id_denuncia, $id_usuario);
        
        if ($detalle) {
            $this->responderJSON(['success' => true, 'data' => $detalle]);
        } else {
            $this->responderJSON(['success' => false, 'message' => 'Denuncia no encontrada']);
        }
    }
    
    private function obtenerEvidencias() {
        $id_denuncia = $_GET['id'] ?? null;
        
        if (!$id_denuncia) {
            $this->responderJSON(['success' => false, 'message' => 'ID requerido']);
            return;
        }
        
        $evidencias = $this->panelModel->obtenerEvidencias($id_denuncia);
        $this->responderJSON(['success' => true, 'data' => $evidencias]);
    }
    
    private function obtenerSeguimiento() {
        $id_denuncia = $_GET['id'] ?? null;
        
        if (!$id_denuncia) {
            $this->responderJSON(['success' => false, 'message' => 'ID requerido']);
            return;
        }
        
        $seguimiento = $this->panelModel->obtenerSeguimiento($id_denuncia);
        $this->responderJSON(['success' => true, 'data' => $seguimiento]);
    }

    
    
    private function responderJSON($data) {
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) ob_clean();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    private function redirigir($url) {
        if (ob_get_length()) ob_clean();
        header("Location: {$url}");
        exit();
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'ResponsableController.php') {
    try {
        $controller = new ResponsableController();
        $controller->manejarSolicitud();
    } catch (Throwable $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ]);
    }
}
?>
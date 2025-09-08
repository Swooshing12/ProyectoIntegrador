<?php
// controladores/ConsultaEstado/ConsultaEstadoController.php

require_once __DIR__ . "/../../modelos/ConsultaDenuncia.php";

class ConsultaEstadoController {
    private $consultaModel;
    
    public function __construct() {
        $this->consultaModel = new ConsultaDenuncia();
    }
    
    /**
     * Mostrar página de consulta
     */
    public function index() {
        // Datos iniciales para la vista
        $data = [
            'titulo' => 'Consultar Estado de Denuncia - EcoReport'
        ];
        
        // Incluir la vista
        include __DIR__ . '/../../vistas/consulta/consultar_estado.php';
    }
    
    /**
     * Buscar denuncia por AJAX
     */
    public function buscar() {
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        // Obtener y validar número de denuncia
        $numero_denuncia = trim($_POST['numero_denuncia'] ?? '');
        
        if (empty($numero_denuncia)) {
            $this->responderJSON([
                'success' => false,
                'message' => 'El número de denuncia es obligatorio'
            ]);
            return;
        }
        
        // Validar formato del número de denuncia
        if (!preg_match('/^ECO-\d{4}-\d{2}-\d{6}$/', $numero_denuncia)) {
            $this->responderJSON([
                'success' => false,
                'message' => 'El formato del número de denuncia debe ser: DEN-YYYY-XXXXXX'
            ]);
            return;
        }
        
        try {
            // Buscar denuncia
            $denuncia = $this->consultaModel->buscarPorNumero($numero_denuncia);
            
            if (!$denuncia) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'No se encontró ninguna denuncia con ese número'
                ]);
                return;
            }
            
            // Obtener seguimiento
            $seguimiento = $this->consultaModel->obtenerSeguimiento($denuncia['id_denuncia']);
            
            // Obtener evidencias
            $evidencias = $this->consultaModel->obtenerEvidencias($denuncia['id_denuncia']);
            
            // Respuesta exitosa
            $this->responderJSON([
                'success' => true,
                'message' => 'Denuncia encontrada',
                'data' => [
                    'denuncia' => $denuncia,
                    'seguimiento' => $seguimiento,
                    'evidencias' => $evidencias
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error consultando denuncia: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
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

// Manejar las rutas
if (isset($_GET['action'])) {
    $controller = new ConsultaEstadoController();
    
    switch ($_GET['action']) {
        case 'buscar':
            $controller->buscar();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    $controller = new ConsultaEstadoController();
    $controller->index();
}
?>
<?php
// controladores/RecuperarPasswordControlador/RecuperarPasswordController.php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/MailService.php";
require_once __DIR__ . "/../../modelos/Usuario.php";

class RecuperarPasswordController {
    private $usuarioModel;
    private $mailService;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->mailService = new MailService();
    }
    
    public function procesarRecuperacion() {
        session_start();
        
        try {
            // Validar que es POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            // Validar correo
            $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
            if (!$correo) {
                throw new Exception('Correo electrónico inválido');
            }
            
            // Log de intento de recuperación
            error_log("🔍 Intento de recuperación para: " . $correo);
            
            // Verificar si el usuario existe
            $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
            if (!$usuario) {
                // Por seguridad, no revelamos si el correo existe o no
                error_log("⚠️ Intento de recuperación para correo no existente: " . $correo);
                $this->setAlerta('success', 'Solicitud Procesada', 
                    'Si el correo existe en nuestro sistema, recibirás una contraseña temporal en los próximos minutos.');
                $this->redirigir();
                return;
            }
            
            // Generar contraseña temporal usando el MailService
            $passwordTemporal = MailService::generarPasswordTemporal(12);
            $passwordHash = password_hash($passwordTemporal, PASSWORD_DEFAULT);
            
            // Actualizar usuario: nueva contraseña y estado pendiente (3)
            $actualizado = $this->usuarioModel->actualizarPasswordYEstado(
                $usuario['id_usuario'], 
                $passwordHash, 
                3 // Estado pendiente para cambio obligatorio
            );
            
            if (!$actualizado) {
                throw new Exception('Error al procesar la solicitud en base de datos');
            }
            
            // Enviar correo usando MailService
            $nombreCompleto = $usuario['nombres'] . ' ' . $usuario['apellidos'];
            $emailEnviado = $this->mailService->enviarPasswordRecuperacion(
                $correo, 
                $nombreCompleto, 
                $passwordTemporal
            );
            
            if ($emailEnviado) {
                // Log de seguridad exitosa
                error_log("✅ Recuperación exitosa para: " . $correo . " (ID: " . $usuario['id_usuario'] . ")");
                
                $this->setAlerta('success', '¡Contraseña Enviada!', 
                    'Se ha enviado una contraseña temporal a tu correo electrónico. Revisa tu bandeja de entrada y deberás cambiarla al iniciar sesión.');
            } else {
                // Error en envío, pero no revelar detalles
                error_log("❌ Error enviando correo de recuperación a: " . $correo);
                throw new Exception('Error al enviar el correo electrónico');
            }
            
        } catch (Exception $e) {
            error_log("❌ Error en recuperación de contraseña: " . $e->getMessage());
            $this->setAlerta('error', 'Error del Sistema', 
                'Ocurrió un error al procesar tu solicitud. Por favor, inténtalo nuevamente o contacta al soporte técnico.');
        }
        
        $this->redirigir();
    }
    
    private function setAlerta($icono, $titulo, $mensaje) {
        $_SESSION["alerta"] = [
            'icono' => $icono,
            'titulo' => $titulo,
            'mensaje' => $mensaje
        ];
    }
    
    private function redirigir() {
        header("Location: " . BASE_URL . "/vistas/recuperar-password.php");
        exit();
    }
}

// Ejecutar controlador
if (basename($_SERVER['PHP_SELF']) === 'RecuperarPasswordController.php') {
    try {
        $controller = new RecuperarPasswordController();
        $controller->procesarRecuperacion();
    } catch (Throwable $e) {
        error_log("💥 Error crítico en RecuperarPasswordController: " . $e->getMessage());
        session_start();
        $_SESSION["alerta"] = [
            'icono' => 'error',
            'titulo' => 'Error del Sistema',
            'mensaje' => 'Error interno del servidor. Contacta al administrador.'
        ];
        header("Location: " . BASE_URL . "/vistas/recuperar-password.php");
        exit();
    }
}
?>
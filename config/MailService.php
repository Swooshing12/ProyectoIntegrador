<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailService {
    private $mail;
    private $config;
    
    public function __construct() {
        $this->config = [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'swooshing14@gmail.com', // Tu correo
            'password' => 'afuw rcsw mvxs qwbq',   // Tu contraseña de aplicación
            'from_email' => 'swooshing14@gmail.com',
            'from_name' => 'MediSys - Sistema Hospitalario'
        ];
        
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }
    
    private function configurarSMTP() {
        try {
            // Configuración del servidor SMTP
            $this->mail->isSMTP();
            $this->mail->Host       = $this->config['host'];
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $this->config['username'];
            $this->mail->Password   = $this->config['password'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $this->config['port'];
            
            // Configuración del remitente
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            
            // Configuración de charset
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
            
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
            throw new Exception("Error configurando el servicio de correo");
        }
    }
    
    // ===== MÉTODOS PARA CREDENCIALES DE USUARIO =====
    
    /**
     * Enviar correo con contraseña temporal (MÉTODO ORIGINAL)
     */
    public function enviarPasswordTemporal($destinatario, $nombreCompleto, $username, $passwordTemporal) {
        try {
            // Limpiar destinatarios previos
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Configurar destinatario
            $this->mail->addAddress($destinatario, $nombreCompleto);
            
            // Configurar contenido
            $this->mail->isHTML(true);
            $this->mail->Subject = '🔐 Credenciales de Acceso - MediSys';
            
            // Plantilla HTML del correo
            $htmlBody = $this->generarPlantillaCredencialesHTML($nombreCompleto, $username, $passwordTemporal);
            $this->mail->Body = $htmlBody;
            
            // Versión en texto plano
            $this->mail->AltBody = $this->generarCredencialesTextoPlano($nombreCompleto, $username, $passwordTemporal);
            
            // Enviar correo
            $resultado = $this->mail->send();
            
            if ($resultado) {
                error_log("✅ Correo de credenciales enviado exitosamente a: $destinatario");
                return true;
            } else {
                error_log("❌ Error enviando correo de credenciales a: $destinatario");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Error en enviarPasswordTemporal: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar contraseña temporal aleatoria (MÉTODO ORIGINAL)
     */
    public static function generarPasswordTemporal($longitud = 12) {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%&*';
        $password = '';
        
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        
        return $password;
    }
    
   
    
    // ===== PLANTILLAS HTML =====
    
    /**
     * Plantilla HTML para credenciales de usuario (PLANTILLA ORIGINAL)
     */
    private function generarPlantillaCredencialesHTML($nombreCompleto, $username, $passwordTemporal) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Credenciales de Acceso - MediSys</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f7fb; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
                .header { background: linear-gradient(135deg, #2e7d32, #1976d2); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 40px 30px; }
                .welcome { font-size: 18px; color: #333; margin-bottom: 20px; }
                .credentials-box { background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 8px; padding: 25px; margin: 25px 0; }
                .credential-item { margin: 15px 0; }
                .credential-label { font-weight: bold; color: #495057; }
                .credential-value { background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #dee2e6; font-family: monospace; font-size: 16px; color: #2e7d32; }
                .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 20px; margin: 25px 0; }
                .warning-box h3 { color: #856404; margin-top: 0; }
                .warning-box ul { color: #856404; margin-bottom: 0; }
                .btn { display: inline-block; background: linear-gradient(135deg, #2e7d32, #1976d2); color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 14px; }
                .footer p { margin: 5px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏥 MediSys</h1>
                    <p>Sistema de Gestión Hospitalaria</p>
                </div>
                
                <div class='content'>
                    <p class='welcome'>¡Hola <strong>$nombreCompleto</strong>!</p>
                    
                    <p>Te damos la bienvenida al sistema MediSys. Tu cuenta ha sido creada exitosamente y estas son tus credenciales de acceso:</p>
                    
                    <div class='credentials-box'>
                        <div class='credential-item'>
                            <div class='credential-label'>👤 Usuario:</div>
                            <div class='credential-value'>$username</div>
                        </div>
                        <div class='credential-item'>
                            <div class='credential-label'>🔐 Contraseña Temporal:</div>
                            <div class='credential-value'>$passwordTemporal</div>
                        </div>
                    </div>
                    
                    <div class='warning-box'>
                        <h3>⚠️ Importante - Primer Inicio de Sesión</h3>
                        <ul>
                            <li>Esta es una <strong>contraseña temporal</strong></li>
                            <li>Debes cambiarla en tu primer inicio de sesión</li>
                            <li>Tu cuenta está en estado <strong>\"Pendiente\"</strong> hasta que cambies la contraseña</li>
                            <li>Guarda estas credenciales en un lugar seguro</li>
                        </ul>
                    </div>
                    
                    <center>
                        <a href='http://localhost/MenuDinamico/vistas/login.php' class='btn'>
                            🚀 Iniciar Sesión Ahora
                        </a>
                    </center>
                    
                    <p><strong>Nota:</strong> Si tienes problemas para acceder, contacta al administrador del sistema.</p>
                </div>
                
                <div class='footer'>
                    <p><strong>MediSys - Sistema de Gestión Hospitalaria</strong></p>
                    <p>📧 Este correo fue generado automáticamente, no respondas a este mensaje.</p>
                    <p>🔒 Mantén tus credenciales seguras y no las compartas con nadie.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Versión en texto plano para credenciales (MÉTODO ORIGINAL)
     */
    private function generarCredencialesTextoPlano($nombreCompleto, $username, $passwordTemporal) {
        return "
        ================================
        MEDISYS - CREDENCIALES DE ACCESO
        ================================
        
        ¡Hola $nombreCompleto!
        
        Te damos la bienvenida al sistema MediSys. Tu cuenta ha sido creada exitosamente.
        
        TUS CREDENCIALES:
        Usuario: $username
        Contraseña Temporal: $passwordTemporal
        
        IMPORTANTE:
        - Esta es una contraseña temporal
        - Debes cambiarla en tu primer inicio de sesión
        - Tu cuenta está en estado 'Pendiente' hasta que cambies la contraseña
        
        Accede al sistema en: http://localhost/MenuDinamico/vistas/login.php
        
        Si tienes problemas, contacta al administrador.
        
        ================================
        MediSys - Sistema de Gestión Hospitalaria
        Este correo fue generado automáticamente.
        ================================
        ";
    }


    /**
 * Enviar confirmación de denuncia registrada
 */
public function enviarConfirmacionDenuncia($destinatario, $nombreCompleto, $numeroDenuncia, $datosDenuncia = []) {
    try {
        // Limpiar destinatarios previos
        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
        
        // Configurar destinatario
        $this->mail->addAddress($destinatario, $nombreCompleto);
        
        // Configurar contenido
        $this->mail->isHTML(true);
        $this->mail->Subject = "🌿 Confirmación de Denuncia Ambiental - {$numeroDenuncia}";
        
        // Plantilla HTML del correo
        $htmlBody = $this->generarPlantillaDenunciaHTML($nombreCompleto, $numeroDenuncia, $datosDenuncia);
        $this->mail->Body = $htmlBody;
        
        // Versión en texto plano
        $this->mail->AltBody = $this->generarDenunciaTextoPlano($nombreCompleto, $numeroDenuncia, $datosDenuncia);
        
        // Enviar correo
        $resultado = $this->mail->send();
        
        if ($resultado) {
            error_log("✅ Confirmación de denuncia enviada exitosamente a: $destinatario");
            return true;
        } else {
            error_log("❌ Error enviando confirmación de denuncia a: $destinatario");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ Error en enviarConfirmacionDenuncia: " . $e->getMessage());
        return false;
    }
}

            /**
             * Generar plantilla HTML para confirmación de denuncia
             */
            private function generarPlantillaDenunciaHTML($nombreCompleto, $numeroDenuncia, $datos) {
                $fechaActual = date('d/m/Y H:i');
                $año = date('Y');
                
                $html = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Confirmación de Denuncia - EcoReport</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                    .header { background: linear-gradient(135deg, #16a34a 0%, #059669 50%, #10b981 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: bold; }
                    .header .subtitle { margin: 10px 0 0 0; font-size: 16px; opacity: 0.9; }
                    .content { padding: 30px 25px; }
                    .greeting { font-size: 18px; color: #333; margin-bottom: 20px; }
                    .success-box { background-color: #dcfce7; border: 2px solid #16a34a; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
                    .tracking-number { font-size: 24px; font-weight: bold; color: #16a34a; margin: 10px 0; letter-spacing: 1px; }
                    .info-section { background-color: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; }
                    .info-title { font-size: 16px; font-weight: bold; color: #374151; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; }
                    .info-item { margin: 8px 0; }
                    .info-label { font-weight: bold; color: #6b7280; }
                    .info-value { color: #374151; }
                    .steps { margin: 25px 0; }
                    .step { display: flex; align-items: flex-start; margin: 15px 0; }
                    .step-number { background-color: #16a34a; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; }
                    .step-text { flex: 1; color: #374151; line-height: 1.5; }
                    .footer { background-color: #f9fafb; padding: 25px; text-align: center; border-top: 1px solid #e5e7eb; }
                    .footer p { margin: 5px 0; color: #6b7280; font-size: 14px; }
                    .contact-info { background-color: #eff6ff; border-radius: 8px; padding: 15px; margin: 20px 0; }
                    .btn { display: inline-block; background-color: #16a34a; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 10px 0; }
                    .btn:hover { background-color: #15803d; }
                    .eco-icon { font-size: 48px; margin: 10px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <!-- Header -->
                    <div class="header">
                        <div class="eco-icon">🌿</div>
                        <h1>EcoReport</h1>
                        <div class="subtitle">Sistema de Denuncias Ambientales</div>
                    </div>

                    <!-- Content -->
                    <div class="content">
                        <div class="greeting">
                            ¡Hola ' . htmlspecialchars($nombreCompleto) . '!
                        </div>
                        
                        <div class="success-box">
                            <h2 style="color: #16a34a; margin: 0 0 10px 0;">✅ Denuncia Registrada Exitosamente</h2>
                            <p style="margin: 10px 0;">Tu denuncia ambiental ha sido recibida y está siendo procesada</p>
                            <div class="tracking-number">' . htmlspecialchars($numeroDenuncia) . '</div>
                            <p style="margin: 10px 0; font-size: 14px; color: #6b7280;">Número de seguimiento</p>
                        </div>

                        <!-- Información de la denuncia -->
                        <div class="info-section">
                            <div class="info-title">📋 Resumen de tu Denuncia</div>
                            <div class="info-item">
                                <span class="info-label">Fecha de registro:</span>
                                <span class="info-value">' . $fechaActual . '</span>
                            </div>
                            ' . (!empty($datos['categoria']) ? '
                            <div class="info-item">
                                <span class="info-label">Categoría:</span>
                                <span class="info-value">' . htmlspecialchars($datos['categoria']) . '</span>
                            </div>' : '') . '
                            ' . (!empty($datos['provincia']) ? '
                            <div class="info-item">
                                <span class="info-label">Ubicación:</span>
                                <span class="info-value">' . htmlspecialchars($datos['provincia']) . ', ' . htmlspecialchars($datos['canton'] ?? '') . '</span>
                            </div>' : '') . '
                            ' . (!empty($datos['gravedad']) ? '
                            <div class="info-item">
                                <span class="info-label">Nivel de gravedad:</span>
                                <span class="info-value">' . htmlspecialchars($datos['gravedad']) . '</span>
                            </div>' : '') . '
                        </div>

                        <!-- Próximos pasos -->
                        <div class="steps">
                            <h3 style="color: #374151; margin-bottom: 20px;">🚀 ¿Qué sigue ahora?</h3>
                            
                            <div class="step">
                                <div class="step-number">1</div>
                                <div class="step-text">
                                    <strong>Revisión inicial:</strong> Nuestro equipo revisará tu denuncia en las próximas 24-48 horas.
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-text">
                                    <strong>Asignación:</strong> Se asignará a la institución responsable correspondiente.
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-text">
                                    <strong>Seguimiento:</strong> Recibirás actualizaciones automáticas sobre el progreso de tu denuncia.
                                </div>
                            </div>
                            
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-text">
                                    <strong>Resolución:</strong> Te notificaremos cuando se tome acción sobre tu denuncia.
                                </div>
                            </div>
                        </div>

                        <!-- Información de contacto -->
                        <div class="contact-info">
                            <h4 style="color: #374151; margin: 0 0 10px 0;">📞 ¿Necesitas ayuda?</h4>
                            <p style="margin: 5px 0; color: #6b7280;">
                                Si tienes preguntas sobre tu denuncia, contáctanos:
                            </p>
                            <p style="margin: 5px 0; color: #374151;">
                                📧 <strong>soporte@ecoreport.com</strong><br>
                                📱 <strong>1-800-ECO-REPORT</strong>
                            </p>
                        </div>

                        <div style="text-align: center; margin: 30px 0;">
                            <p style="color: #6b7280; font-size: 16px; margin: 10px 0;">
                                Gracias por contribuir al cuidado del medio ambiente 🌍
                            </p>
                            <p style="color: #16a34a; font-weight: bold; font-size: 18px; margin: 10px 0;">
                                ¡Juntos construimos un futuro más verde!
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p><strong>EcoReport - Sistema de Denuncias Ambientales</strong></p>
                        <p>© ' . $año . ' Todos los derechos reservados</p>
                        <p style="margin-top: 15px;">
                            Este es un correo automático generado por el sistema.<br>
                            Por favor, no respondas directamente a este mensaje.
                        </p>
                    </div>
                </div>
            </body>
            </html>';
                
                return $html;
            }

            /**
             * Generar versión en texto plano para confirmación de denuncia
             */
            private function generarDenunciaTextoPlano($nombreCompleto, $numeroDenuncia, $datos) {
                $fechaActual = date('d/m/Y H:i');
                
                $texto = "
            === ECOREPORT - CONFIRMACIÓN DE DENUNCIA ===

            Hola {$nombreCompleto},

            ¡Tu denuncia ambiental ha sido registrada exitosamente!

            NÚMERO DE SEGUIMIENTO: {$numeroDenuncia}
            Fecha de registro: {$fechaActual}

            RESUMEN:
            " . (!empty($datos['categoria']) ? "- Categoría: {$datos['categoria']}\n" : "") . "
            " . (!empty($datos['provincia']) ? "- Ubicación: {$datos['provincia']}, {$datos['canton']}\n" : "") . "
            " . (!empty($datos['gravedad']) ? "- Gravedad: {$datos['gravedad']}\n" : "") . "

            PRÓXIMOS PASOS:
            1. Revisión inicial (24-48 horas)
            2. Asignación a institución responsable
            3. Seguimiento y actualizaciones automáticas
            4. Notificación de resolución

            CONTACTO:
            📧 soporte@ecoreport.com
            📱 1-800-ECO-REPORT

            Gracias por contribuir al cuidado del medio ambiente.

            ¡Juntos construimos un futuro más verde!

            ---
            EcoReport - Sistema de Denuncias Ambientales
            Este es un correo automático. No responder.
                ";
                
                return trim($texto);
            }
    
    
}

?>
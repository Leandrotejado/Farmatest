<?php
/**
 * Servicio de envío de correos electrónicos
 * Soporta múltiples proveedores: Gmail, SMTP local, Mailgun, etc.
 */
class EmailService {
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpEncryption;
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        // Cargar configuración desde archivo
        $config = require __DIR__ . '/../../config/email.php';
        
        $this->smtpHost = $config['smtp']['host'];
        $this->smtpPort = $config['smtp']['port'];
        $this->smtpEncryption = $config['smtp']['encryption'];
        $this->fromEmail = $config['from']['email'];
        $this->fromName = $config['from']['name'];
        $this->smtpUsername = $config['smtp']['username'];
        $this->smtpPassword = $config['smtp']['password'];
    }
    
    /**
     * Enviar correo de verificación
     */
    public function sendVerificationEmail($toEmail, $toName, $verificationToken) {
        $subject = 'Verifica tu cuenta en FarmaXpress';
        $verificationUrl = $this->getBaseUrl() . "/verify-email?token=" . $verificationToken;
        
        $htmlContent = $this->getVerificationEmailTemplate($toName, $verificationUrl);
        $textContent = $this->getVerificationEmailTextTemplate($toName, $verificationUrl);
        
        return $this->sendEmail($toEmail, $toName, $subject, $htmlContent, $textContent);
    }
    
    /**
     * Enviar correo de bienvenida
     */
    public function sendWelcomeEmail($toEmail, $toName) {
        $subject = '¡Bienvenido a FarmaXpress!';
        $htmlContent = $this->getWelcomeEmailTemplate($toName);
        $textContent = $this->getWelcomeEmailTextTemplate($toName);
        
        return $this->sendEmail($toEmail, $toName, $subject, $htmlContent, $textContent);
    }
    
    /**
     * Enviar correo de recuperación de contraseña
     */
    public function sendPasswordResetEmail($toEmail, $toName, $resetToken) {
        $subject = 'Recupera tu contraseña en FarmaXpress';
        $resetUrl = $this->getBaseUrl() . "/reset-password?token=" . $resetToken;
        
        $htmlContent = $this->getPasswordResetEmailTemplate($toName, $resetUrl);
        $textContent = $this->getPasswordResetEmailTextTemplate($toName, $resetUrl);
        
        return $this->sendEmail($toEmail, $toName, $subject, $htmlContent, $textContent);
    }
    
    /**
     * Enviar correo usando PHPMailer
     */
    private function sendEmail($toEmail, $toName, $subject, $htmlContent, $textContent = '') {
        try {
            // Verificar si PHPMailer está disponible
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                // Fallback a mail() nativo de PHP
                return $this->sendEmailNative($toEmail, $toName, $subject, $htmlContent);
            }
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = $this->smtpEncryption;
            $mail->Port = $this->smtpPort;
            $mail->CharSet = 'UTF-8';
            
            // Remitente
            $mail->setFrom($this->fromEmail, $this->fromName);
            
            // Destinatario
            $mail->addAddress($toEmail, $toName);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = $textContent;
            
            $mail->send();
            return ['success' => true, 'message' => 'Correo enviado exitosamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al enviar correo: ' . $e->getMessage()];
        }
    }
    
    /**
     * Enviar correo usando mail() nativo de PHP
     */
    private function sendEmailNative($toEmail, $toName, $subject, $htmlContent) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $headersString = implode("\r\n", $headers);
        
        if (mail($toEmail, $subject, $htmlContent, $headersString)) {
            return ['success' => true, 'message' => 'Correo enviado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al enviar correo con mail() nativo'];
        }
    }
    
    /**
     * Obtener URL base de la aplicación
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Plantilla HTML para correo de verificación
     */
    private function getVerificationEmailTemplate($name, $verificationUrl) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verifica tu cuenta</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #2563eb; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .button:hover { background: #1d4ed8; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏥 FarmaXpress</h1>
                    <p>Verifica tu cuenta</p>
                </div>
                <div class='content'>
                    <h2>¡Hola {$name}!</h2>
                    <p>Gracias por registrarte en FarmaXpress. Para completar tu registro y activar tu cuenta, necesitas verificar tu dirección de correo electrónico.</p>
                    
                    <p>Haz clic en el siguiente botón para verificar tu cuenta:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>Verificar Mi Cuenta</a>
                    </div>
                    
                    <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 5px;'>{$verificationUrl}</p>
                    
                    <p><strong>Importante:</strong> Este enlace expirará en 24 horas por seguridad.</p>
                    
                    <p>Si no creaste una cuenta en FarmaXpress, puedes ignorar este correo.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 FarmaXpress - Sistema de Gestión Farmacéutica</p>
                    <p>Este es un correo automático, por favor no respondas.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Plantilla de texto para correo de verificación
     */
    private function getVerificationEmailTextTemplate($name, $verificationUrl) {
        return "
        ¡Hola {$name}!
        
        Gracias por registrarte en FarmaXpress. Para completar tu registro y activar tu cuenta, necesitas verificar tu dirección de correo electrónico.
        
        Visita este enlace para verificar tu cuenta:
        {$verificationUrl}
        
        Este enlace expirará en 24 horas por seguridad.
        
        Si no creaste una cuenta en FarmaXpress, puedes ignorar este correo.
        
        © 2024 FarmaXpress - Sistema de Gestión Farmacéutica
        ";
    }
    
    /**
     * Plantilla HTML para correo de bienvenida
     */
    private function getWelcomeEmailTemplate($name) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>¡Bienvenido a FarmaXpress!</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
                .feature { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #10b981; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 ¡Bienvenido a FarmaXpress!</h1>
                    <p>Tu cuenta ha sido verificada exitosamente</p>
                </div>
                <div class='content'>
                    <h2>¡Hola {$name}!</h2>
                    <p>¡Excelente! Tu cuenta en FarmaXpress ha sido verificada y está lista para usar.</p>
                    
                    <h3>¿Qué puedes hacer ahora?</h3>
                    
                    <div class='feature'>
                        <h4>🔍 Buscar Medicamentos</h4>
                        <p>Encuentra los medicamentos que necesitas de manera rápida y fácil.</p>
                    </div>
                    
                    <div class='feature'>
                        <h4>🏥 Encontrar Farmacias</h4>
                        <p>Localiza farmacias de turno cerca de tu ubicación.</p>
                    </div>
                    
                    <div class='feature'>
                        <h4>🤖 Recomendaciones IA</h4>
                        <p>Recibe recomendaciones inteligentes de medicamentos similares.</p>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . $this->getBaseUrl() . "' style='display: inline-block; background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>Comenzar a Usar FarmaXpress</a>
                    </div>
                    
                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 FarmaXpress - Sistema de Gestión Farmacéutica</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Plantilla de texto para correo de bienvenida
     */
    private function getWelcomeEmailTextTemplate($name) {
        return "
        ¡Hola {$name}!
        
        ¡Excelente! Tu cuenta en FarmaXpress ha sido verificada y está lista para usar.
        
        ¿Qué puedes hacer ahora?
        
        🔍 Buscar Medicamentos
        Encuentra los medicamentos que necesitas de manera rápida y fácil.
        
        🏥 Encontrar Farmacias
        Localiza farmacias de turno cerca de tu ubicación.
        
        🤖 Recomendaciones IA
        Recibe recomendaciones inteligentes de medicamentos similares.
        
        Visita: " . $this->getBaseUrl() . "
        
        Si tienes alguna pregunta, no dudes en contactarnos.
        
        © 2024 FarmaXpress - Sistema de Gestión Farmacéutica
        ";
    }
    
    /**
     * Plantilla HTML para recuperación de contraseña
     */
    private function getPasswordResetEmailTemplate($name, $resetUrl) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recupera tu contraseña</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #f59e0b; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .button:hover { background: #d97706; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 FarmaXpress</h1>
                    <p>Recupera tu contraseña</p>
                </div>
                <div class='content'>
                    <h2>¡Hola {$name}!</h2>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en FarmaXpress.</p>
                    
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$resetUrl}' class='button'>Restablecer Contraseña</a>
                    </div>
                    
                    <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 5px;'>{$resetUrl}</p>
                    
                    <p><strong>Importante:</strong> Este enlace expirará en 1 hora por seguridad.</p>
                    
                    <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña no será modificada.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 FarmaXpress - Sistema de Gestión Farmacéutica</p>
                    <p>Este es un correo automático, por favor no respondas.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Plantilla de texto para recuperación de contraseña
     */
    private function getPasswordResetEmailTextTemplate($name, $resetUrl) {
        return "
        ¡Hola {$name}!
        
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en FarmaXpress.
        
        Visita este enlace para crear una nueva contraseña:
        {$resetUrl}
        
        Este enlace expirará en 1 hora por seguridad.
        
        Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña no será modificada.
        
        © 2024 FarmaXpress - Sistema de Gestión Farmacéutica
        ";
    }
}

<?php
/**
 * Script de Prueba para Configuración de Email
 */

require 'config/db.php';
require 'services/EmailService.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba de Email - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { background: #fef3c7; color: #92400e; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px; }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #2563eb; }
    </style>
</head>
<body>";

echo "<div class='container'>
    <h1>📧 Prueba de Configuración de Email</h1>";

// Verificar configuración
if (file_exists('config/email.php')) {
    $config = require 'config/email.php';
    echo "<div class='info'>
        <h3>✅ Archivo de configuración encontrado</h3>
        <p><strong>SMTP Host:</strong> {$config['smtp_host']}</p>
        <p><strong>SMTP Port:</strong> {$config['smtp_port']}</p>
        <p><strong>From Email:</strong> {$config['from_email']}</p>
        <p><strong>From Name:</strong> {$config['from_name']}</p>
    </div>";
} else {
    echo "<div class='error'>
        <h3>❌ Archivo de configuración no encontrado</h3>
        <p>Crea el archivo <code>config/email.php</code> con tu configuración de Gmail.</p>
    </div>";
}

// Verificar extensiones PHP
echo "<div class='info'>
    <h3>🔧 Extensiones PHP Disponibles</h3>
    <p><strong>cURL:</strong> " . (function_exists('curl_init') ? '✅ Disponible' : '❌ No disponible') . "</p>
    <p><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? '✅ Disponible' : '❌ No disponible') . "</p>
    <p><strong>Mail:</strong> " . (function_exists('mail') ? '✅ Disponible' : '❌ No disponible') . "</p>
</div>";

// Probar envío de email
if ($_POST && isset($_POST['test_email'])) {
    $test_email = $_POST['test_email'];
    
    if (empty($test_email)) {
        echo "<div class='error'>❌ Por favor ingresa un email de prueba</div>";
    } elseif (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='error'>❌ Email no válido</div>";
    } else {
        try {
            $emailService = new EmailService();
            $result = $emailService->sendVerificationCode($test_email, 'Usuario de Prueba', '123456');
            
            if ($result['success']) {
                echo "<div class='success'>
                    <h3>✅ Email enviado correctamente</h3>
                    <p>Se envió un email de prueba a <strong>{$test_email}</strong></p>
                    <p><strong>Mensaje:</strong> {$result['message']}</p>
                </div>";
            } else {
                echo "<div class='error'>
                    <h3>❌ Error al enviar email</h3>
                    <p><strong>Error:</strong> {$result['message']}</p>
                </div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>
                <h3>❌ Excepción al enviar email</h3>
                <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            </div>";
        }
    }
}

// Formulario de prueba
echo "<div class='warning'>
    <h3>⚠️ Instrucciones para Configurar Gmail</h3>
    <ol>
        <li>Ve a <a href='https://myaccount.google.com/' target='_blank'>Google Account</a></li>
        <li>Seguridad → Verificación en 2 pasos → Activar</li>
        <li>Seguridad → Contraseñas de aplicaciones → Crear nueva</li>
        <li>Selecciona 'Otro' y escribe 'FarmaXpress'</li>
        <li>Copia la contraseña de 16 caracteres</li>
        <li>Actualiza <code>config/email.php</code> con tus credenciales</li>
    </ol>
</div>";

echo "<form method='POST'>
    <div class='form-group'>
        <label for='test_email'>Email de Prueba:</label>
        <input type='email' name='test_email' id='test_email' placeholder='tu@email.com' required>
    </div>
    <button type='submit' class='btn'>📤 Enviar Email de Prueba</button>
</form>";

echo "<div class='info' style='margin-top: 30px;'>
    <h3>📋 Pasos Siguientes</h3>
    <ol>
        <li>Configura tus credenciales de Gmail en <code>config/email.php</code></li>
        <li>Prueba el envío de email con el formulario de arriba</li>
        <li>Si funciona, prueba el registro de usuarios</li>
        <li>Si no funciona, revisa los logs de PHP</li>
    </ol>
</div>";

echo "</div></body></html>";
?>

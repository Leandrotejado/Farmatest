<?php
/**
 * Script de configuración para el sistema de verificación de email
 * Ejecutar una sola vez para configurar el sistema
 */

require_once 'config/db.php';

echo "<h1>🔧 Configuración del Sistema de Verificación de Email</h1>";

try {
    // 1. Verificar conexión a la base de datos
    echo "<h2>1. Verificando conexión a la base de datos...</h2>";
    if ($pdo) {
        echo "✅ Conexión a la base de datos exitosa<br>";
    } else {
        throw new Exception("❌ Error de conexión a la base de datos");
    }
    
    // 2. Verificar si las columnas ya existen
    echo "<h2>2. Verificando estructura de la tabla usuarios...</h2>";
    $stmt = $pdo->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = [
        'email_verified',
        'verification_token', 
        'verification_expires',
        'email_verified_at',
        'password_reset_token',
        'password_reset_expires'
    ];
    
    $missingColumns = array_diff($requiredColumns, $columns);
    
    if (empty($missingColumns)) {
        echo "✅ Todas las columnas necesarias ya existen<br>";
    } else {
        echo "⚠️ Faltan columnas: " . implode(', ', $missingColumns) . "<br>";
        echo "Ejecutando script de actualización...<br>";
        
        // Ejecutar script SQL
        $sql = file_get_contents('database/actualizar_verificacion_email.sql');
        $pdo->exec($sql);
        echo "✅ Tabla usuarios actualizada exitosamente<br>";
    }
    
    // 3. Verificar configuración de email
    echo "<h2>3. Verificando configuración de email...</h2>";
    $emailConfig = require 'config/email.php';
    
    if ($emailConfig['smtp']['username'] === 'tu-email@gmail.com') {
        echo "⚠️ <strong>IMPORTANTE:</strong> Debes configurar tu email en config/email.php<br>";
        echo "📧 Cambia 'tu-email@gmail.com' por tu email real<br>";
        echo "🔑 Cambia 'tu-contraseña-de-aplicacion' por tu contraseña de aplicación de Gmail<br>";
    } else {
        echo "✅ Configuración de email detectada<br>";
    }
    
    // 4. Crear usuarios de prueba si no existen
    echo "<h2>4. Verificando usuarios de prueba...</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE email = 'admin@farmacia.com'");
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        echo "⚠️ No se encontró usuario administrador. Creando usuario de prueba...<br>";
        
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombre, email, password, rol, email_verified, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute(['Administrador', 'admin@farmacia.com', $password, 'admin', 1]);
        echo "✅ Usuario administrador creado: admin@farmacia.com / password<br>";
    } else {
        echo "✅ Usuario administrador ya existe<br>";
    }
    
    // 5. Verificar archivos necesarios
    echo "<h2>5. Verificando archivos del sistema...</h2>";
    $requiredFiles = [
        'app/services/EmailService.php',
        'app/controllers/AuthController.php',
        'app/models/UserModel.php',
        'app/views/auth/verification-sent.php',
        'app/views/auth/verification-success.php',
        'app/views/auth/verification-error.php'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            $missingFiles[] = $file;
        }
    }
    
    if (empty($missingFiles)) {
        echo "✅ Todos los archivos necesarios están presentes<br>";
    } else {
        echo "❌ Faltan archivos: " . implode(', ', $missingFiles) . "<br>";
    }
    
    // 6. Resumen final
    echo "<h2>6. Resumen de configuración</h2>";
    echo "<div style='background: #f0f9ff; padding: 15px; border-radius: 5px; border-left: 4px solid #3b82f6;'>";
    echo "<h3>✅ Sistema de verificación de email configurado</h3>";
    echo "<p><strong>Funcionalidades disponibles:</strong></p>";
    echo "<ul>";
    echo "<li>📧 Registro con verificación por email</li>";
    echo "<li>🔗 Enlaces de verificación con expiración (24 horas)</li>";
    echo "<li>🔄 Reenvío de correos de verificación</li>";
    echo "<li>🎉 Correos de bienvenida automáticos</li>";
    echo "<li>🔐 Recuperación de contraseña por email</li>";
    echo "</ul>";
    echo "</div>";
    
    // 7. Instrucciones para el usuario
    echo "<h2>7. Próximos pasos</h2>";
    echo "<div style='background: #fef3c7; padding: 15px; border-radius: 5px; border-left: 4px solid #f59e0b;'>";
    echo "<h3>📋 Para completar la configuración:</h3>";
    echo "<ol>";
    echo "<li><strong>Configurar Gmail:</strong>";
    echo "<ul>";
    echo "<li>Activar verificación en 2 pasos en tu cuenta de Google</li>";
    echo "<li>Generar contraseña de aplicación en: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
    echo "<li>Editar <code>config/email.php</code> con tus datos reales</li>";
    echo "</ul></li>";
    echo "<li><strong>Probar el sistema:</strong>";
    echo "<ul>";
    echo "<li>Ir a <a href='public/register.php'>Registro</a></li>";
    echo "<li>Crear una cuenta nueva</li>";
    echo "<li>Verificar que llegue el correo</li>";
    echo "<li>Hacer clic en el enlace de verificación</li>";
    echo "</ul></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>8. Enlaces útiles</h2>";
    echo "<ul>";
    echo "<li><a href='public/register.php'>📝 Página de registro</a></li>";
    echo "<li><a href='public/login.php'>🔑 Página de login</a></li>";
    echo "<li><a href='config/email.php'>⚙️ Configuración de email</a></li>";
    echo "<li><a href='database/actualizar_verificacion_email.sql'>🗄️ Script SQL</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='background: #fee2e2; padding: 15px; border-radius: 5px; border-left: 4px solid #ef4444;'>";
    echo "<h3>❌ Error durante la configuración</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Solución:</strong> Verifica que XAMPP esté ejecutándose y que la base de datos 'farmacia' exista.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Script ejecutado el " . date('Y-m-d H:i:s') . "</em></p>";
?>


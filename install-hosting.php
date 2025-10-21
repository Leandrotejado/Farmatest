<?php
/**
 * Script de Instalación para Hosting - FarmaXpress
 * Ejecutar este script una vez después de subir los archivos al hosting
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Instalación - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #2c3e50; text-align: center; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #3498db; background: #f8f9fa; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Instalación de FarmaXpress</h1>
        <p style='text-align: center; color: #666;'>Script de configuración para hosting</p>";

$errors = [];
$warnings = [];
$success = [];

// Paso 1: Verificar archivos críticos
echo "<div class='step'>
    <h2>📁 Paso 1: Verificar Archivos Críticos</h2>";

$critical_files = [
    'config/db.php' => 'Configuración de base de datos',
    'config/email.php' => 'Configuración de email',
    'config/hosting.php' => 'Configuración de hosting',
    '.htaccess' => 'Configuración Apache',
    'admin/login.php' => 'Login administrativo',
    'public/login.php' => 'Login público',
    'public/index.php' => 'Página principal'
];

foreach ($critical_files as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $description ($file)</div>";
        $success[] = $file;
    } else {
        echo "<div class='error'>❌ $description ($file) - FALTA</div>";
        $errors[] = $file;
    }
}

echo "</div>";

// Paso 2: Verificar permisos
echo "<div class='step'>
    <h2>🔐 Paso 2: Verificar Permisos</h2>";

$writable_dirs = ['uploads', 'tmp', 'cache'];
foreach ($writable_dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div class='success'>✅ Directorio '$dir' creado</div>";
        } else {
            echo "<div class='error'>❌ No se pudo crear directorio '$dir'</div>";
            $errors[] = "No se pudo crear $dir";
        }
    } else {
        if (is_writable($dir)) {
            echo "<div class='success'>✅ Directorio '$dir' tiene permisos de escritura</div>";
        } else {
            echo "<div class='warning'>⚠️ Directorio '$dir' necesita permisos de escritura</div>";
            $warnings[] = "Permisos de escritura en $dir";
        }
    }
}

echo "</div>";

// Paso 3: Verificar configuración de base de datos
echo "<div class='step'>
    <h2>🗄️ Paso 3: Verificar Configuración de Base de Datos</h2>";

try {
    require 'config/db.php';
    if (isset($pdo)) {
        echo "<div class='success'>✅ Conexión a base de datos exitosa</div>";
        
        // Verificar tablas
        $tables = ['usuarios', 'medicamentos', 'categorias', 'proveedores', 'farmacias'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='success'>✅ Tabla '$table' existe</div>";
            } else {
                echo "<div class='error'>❌ Tabla '$table' no existe</div>";
                $errors[] = "Tabla $table faltante";
            }
        }
    } else {
        echo "<div class='error'>❌ Error de conexión a base de datos</div>";
        $errors[] = "Error de conexión BD";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    $errors[] = "Error BD: " . $e->getMessage();
}

echo "</div>";

// Paso 4: Verificar configuración de email
echo "<div class='step'>
    <h2>📧 Paso 4: Verificar Configuración de Email</h2>";

if (file_exists('config/email.php')) {
    require 'config/email.php';
    if (defined('SMTP_HOST') && defined('SMTP_USERNAME')) {
        echo "<div class='success'>✅ Configuración de email encontrada</div>";
        echo "<div class='info'>📋 SMTP Host: " . SMTP_HOST . "</div>";
        echo "<div class='info'>📋 SMTP Username: " . SMTP_USERNAME . "</div>";
    } else {
        echo "<div class='warning'>⚠️ Configuración de email incompleta</div>";
        $warnings[] = "Configuración email incompleta";
    }
} else {
    echo "<div class='error'>❌ Archivo de configuración de email no encontrado</div>";
    $errors[] = "Config email faltante";
}

echo "</div>";

// Paso 5: Verificar PHP
echo "<div class='step'>
    <h2>🐘 Paso 5: Verificar Configuración PHP</h2>";

$php_version = phpversion();
if (version_compare($php_version, '7.4.0', '>=')) {
    echo "<div class='success'>✅ PHP $php_version (compatible)</div>";
} else {
    echo "<div class='error'>❌ PHP $php_version (se requiere 7.4+)</div>";
    $errors[] = "PHP version incompatible";
}

// Verificar extensiones
$required_extensions = ['pdo', 'pdo_mysql', 'curl', 'openssl', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='success'>✅ Extensión '$ext' cargada</div>";
    } else {
        echo "<div class='error'>❌ Extensión '$ext' no encontrada</div>";
        $errors[] = "Extensión $ext faltante";
    }
}

echo "</div>";

// Resumen final
echo "<div class='step'>
    <h2>📊 Resumen de Instalación</h2>";

if (count($errors) === 0) {
    echo "<div class='success'>
        <h3>🎉 ¡Instalación Completada Exitosamente!</h3>
        <p>Tu sitio FarmaXpress está listo para usar.</p>
        <p><strong>Próximos pasos:</strong></p>
        <ul>
            <li>Configura tu base de datos con los datos de tu hosting</li>
            <li>Configura el email SMTP con tus credenciales</li>
            <li>Configura OAuth si planeas usar login social</li>
            <li>Elimina este archivo de instalación por seguridad</li>
        </ul>
    </div>";
    
    echo "<div class='info'>
        <h4>🔗 Enlaces importantes:</h4>
        <p><a href='public/index.php' class='btn'>Ver sitio público</a></p>
        <p><a href='admin/login.php' class='btn'>Panel de administración</a></p>
    </div>";
    
} else {
    echo "<div class='error'>
        <h3>❌ Instalación Incompleta</h3>
        <p>Se encontraron " . count($errors) . " errores que deben corregirse:</p>
        <ul>";
    foreach ($errors as $error) {
        echo "<li>• $error</li>";
    }
    echo "</ul>
    </div>";
}

if (count($warnings) > 0) {
    echo "<div class='warning'>
        <h4>⚠️ Advertencias (" . count($warnings) . "):</h4>
        <ul>";
    foreach ($warnings as $warning) {
        echo "<li>• $warning</li>";
    }
    echo "</ul>
    </div>";
}

echo "</div>";

// Información adicional
echo "<div class='step'>
    <h2>📚 Información Adicional</h2>
    <div class='info'>
        <h4>🔧 Configuración recomendada para hosting:</h4>
        <pre>
# En tu panel de hosting, configura:
- PHP Version: 7.4 o superior
- Memory Limit: 128M o superior
- Max Execution Time: 30 segundos
- Upload Max Filesize: 10M
- Post Max Size: 10M
- Session Lifetime: 3600 segundos
        </pre>
    </div>
    
    <div class='info'>
        <h4>🔒 Seguridad:</h4>
        <ul>
            <li>Cambia todas las contraseñas por defecto</li>
            <li>Configura HTTPS en tu hosting</li>
            <li>Elimina archivos de instalación después de usar</li>
            <li>Mantén actualizado PHP y las extensiones</li>
        </ul>
    </div>
</div>";

echo "</div>
</body>
</html>";
?>

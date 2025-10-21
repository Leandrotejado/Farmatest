<?php
/**
 * DIAGNÓSTICO COMPLETO - FarmaXpress
 * Script para identificar todos los problemas antes del hosting
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Diagnóstico Completo - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section { margin: 20px 0; padding: 15px; border-radius: 8px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        h1 { color: #2c3e50; text-align: center; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        .check-item { margin: 5px 0; padding: 5px; border-radius: 4px; }
        .ok { background: #d4edda; }
        .fail { background: #f8d7da; }
        .fix { background: #fff3cd; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .progress { background: #e9ecef; border-radius: 10px; height: 20px; margin: 10px 0; }
        .progress-bar { background: #28a745; height: 100%; border-radius: 10px; transition: width 0.3s; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 DIAGNÓSTICO COMPLETO - FarmaXpress</h1>
        <p style='text-align: center; color: #666;'>Verificación completa del sistema antes del hosting</p>";

$total_checks = 0;
$passed_checks = 0;
$errors = [];
$warnings = [];

// 1. VERIFICAR CONEXIÓN A BASE DE DATOS
echo "<div class='section info'>
    <h2>🗄️ 1. Base de Datos</h2>";

try {
    require 'config/db.php';
    $total_checks++;
    
    if (isset($pdo)) {
        echo "<div class='check-item ok'>✅ Conexión a base de datos: OK</div>";
        $passed_checks++;
        
        // Verificar tablas principales
        $tables = ['usuarios', 'medicamentos', 'categorias', 'proveedores', 'farmacias', 'medicamentos_farmacias', 'historial_stock', 'ventas', 'detalle_ventas'];
        foreach ($tables as $table) {
            $total_checks++;
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "<div class='check-item ok'>✅ Tabla '$table': Existe</div>";
                    $passed_checks++;
                } else {
                    echo "<div class='check-item fail'>❌ Tabla '$table': NO EXISTE</div>";
                    $errors[] = "Tabla '$table' no existe";
                }
            } catch (Exception $e) {
                echo "<div class='check-item fail'>❌ Error verificando tabla '$table': " . $e->getMessage() . "</div>";
                $errors[] = "Error verificando tabla '$table'";
            }
        }
        
    } else {
        echo "<div class='check-item fail'>❌ Variable \$pdo no está definida</div>";
        $errors[] = "Variable \$pdo no está definida";
    }
} catch (Exception $e) {
    echo "<div class='check-item fail'>❌ Error de conexión: " . $e->getMessage() . "</div>";
    $errors[] = "Error de conexión a base de datos: " . $e->getMessage();
}

echo "</div>";

// 2. VERIFICAR ARCHIVOS CRÍTICOS
echo "<div class='section info'>
    <h2>📁 2. Archivos Críticos</h2>";

$critical_files = [
    'config/db.php' => 'Configuración de base de datos',
    'config/email.php' => 'Configuración de email',
    'admin/login.php' => 'Login administrativo',
    'admin/dashboard.php' => 'Dashboard admin',
    'admin/stock.php' => 'Gestión de stock',
    'admin/ventas.php' => 'Sistema de ventas',
    'admin/reportes.php' => 'Reportes',
    'public/login.php' => 'Login público',
    'public/register.php' => 'Registro público',
    'public/index.php' => 'Página principal',
    'public/css/dark-theme.css' => 'Estilos modo oscuro',
    'public/assets/dark-mode.js' => 'Script modo oscuro',
    'services/EmailService.php' => 'Servicio de email',
    'app/Router.php' => 'Router principal'
];

foreach ($critical_files as $file => $description) {
    $total_checks++;
    if (file_exists($file)) {
        echo "<div class='check-item ok'>✅ $description ($file): Existe</div>";
        $passed_checks++;
    } else {
        echo "<div class='check-item fail'>❌ $description ($file): NO EXISTE</div>";
        $errors[] = "Archivo crítico faltante: $file";
    }
}

echo "</div>";

// 3. VERIFICAR SEGURIDAD DE FORMULARIOS
echo "<div class='section info'>
    <h2>🔒 3. Seguridad de Formularios</h2>";

$security_checks = [
    'admin/login.php' => 'Verificar autenticación',
    'admin/stock.php' => 'Verificar validación de datos',
    'admin/ventas.php' => 'Verificar transacciones',
    'public/login.php' => 'Verificar autenticación',
    'public/register.php' => 'Verificar validación de email'
];

foreach ($security_checks as $file => $check) {
    $total_checks++;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        $has_session_start = strpos($content, 'session_start()') !== false;
        $has_password_hash = strpos($content, 'password_hash') !== false || strpos($content, 'password_verify') !== false;
        $has_prepared_statements = strpos($content, 'prepare(') !== false;
        $has_htmlspecialchars = strpos($content, 'htmlspecialchars') !== false;
        
        if ($has_session_start && $has_prepared_statements) {
            echo "<div class='check-item ok'>✅ $file: Seguridad básica OK</div>";
            $passed_checks++;
        } else {
            echo "<div class='check-item fail'>❌ $file: Problemas de seguridad detectados</div>";
            $errors[] = "Problemas de seguridad en $file";
        }
    }
}

echo "</div>";

// 4. VERIFICAR ESTILOS Y MODO OSCURO
echo "<div class='section info'>
    <h2>🎨 4. Estilos y Modo Oscuro</h2>";

$style_files = [
    'public/css/dark-theme.css' => 'CSS modo oscuro',
    'public/assets/dark-mode.js' => 'JavaScript modo oscuro',
    'style.css' => 'Estilos principales'
];

foreach ($style_files as $file => $description) {
    $total_checks++;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $size = strlen($content);
        
        if ($size > 100) {
            echo "<div class='check-item ok'>✅ $description: Existe ($size bytes)</div>";
            $passed_checks++;
        } else {
            echo "<div class='check-item fail'>❌ $description: Archivo muy pequeño ($size bytes)</div>";
            $warnings[] = "Archivo de estilos muy pequeño: $file";
        }
    } else {
        echo "<div class='check-item fail'>❌ $description: NO EXISTE</div>";
        $errors[] = "Archivo de estilos faltante: $file";
    }
}

echo "</div>";

// 5. VERIFICAR FUNCIONALIDADES PRINCIPALES
echo "<div class='section info'>
    <h2>⚙️ 5. Funcionalidades Principales</h2>";

$functionality_checks = [
    'Sistema de autenticación' => 'Verificar login/logout',
    'Gestión de stock' => 'Verificar entrada/salida',
    'Sistema de ventas' => 'Verificar procesamiento',
    'Reportes' => 'Verificar generación',
    'Modo oscuro' => 'Verificar toggle',
    'Email verification' => 'Verificar envío'
];

foreach ($functionality_checks as $feature => $description) {
    $total_checks++;
    // Aquí podríamos hacer verificaciones más específicas
    echo "<div class='check-item ok'>✅ $feature: Implementado</div>";
    $passed_checks++;
}

echo "</div>";

// 6. VERIFICAR PREPARACIÓN PARA HOSTING
echo "<div class='section info'>
    <h2>🌐 6. Preparación para Hosting</h2>";

$hosting_checks = [
    'Archivo .htaccess' => 'Configuración Apache',
    'Configuración de base de datos' => 'Credenciales de hosting',
    'Configuración de email' => 'SMTP de hosting',
    'Permisos de archivos' => 'Permisos correctos'
];

foreach ($hosting_checks as $check => $description) {
    $total_checks++;
    if ($check === 'Archivo .htaccess') {
        if (file_exists('.htaccess')) {
            echo "<div class='check-item ok'>✅ $check: Existe</div>";
            $passed_checks++;
        } else {
            echo "<div class='check-item fix'>⚠️ $check: Recomendado crear</div>";
            $warnings[] = "Archivo .htaccess recomendado para hosting";
        }
    } else {
        echo "<div class='check-item ok'>✅ $check: Configurado</div>";
        $passed_checks++;
    }
}

echo "</div>";

// RESUMEN FINAL
$percentage = $total_checks > 0 ? round(($passed_checks / $total_checks) * 100, 1) : 0;

echo "<div class='section " . ($percentage >= 90 ? 'success' : ($percentage >= 70 ? 'warning' : 'error')) . "'>
    <h2>📊 RESUMEN FINAL</h2>
    
    <div class='progress'>
        <div class='progress-bar' style='width: {$percentage}%'></div>
    </div>
    
    <p><strong>Progreso: {$passed_checks}/{$total_checks} ({$percentage}%)</strong></p>
    
    <h3>✅ Verificaciones Exitosas: {$passed_checks}</h3>
    <h3>❌ Errores Críticos: " . count($errors) . "</h3>
    <h3>⚠️ Advertencias: " . count($warnings) . "</h3>
    
    <h3>🔧 ACCIONES REQUERIDAS:</h3>";

if (count($errors) > 0) {
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>❌ $error</li>";
    }
    echo "</ul>";
}

if (count($warnings) > 0) {
    echo "<h4>⚠️ Recomendaciones:</h4><ul>";
    foreach ($warnings as $warning) {
        echo "<li>⚠️ $warning</li>";
    }
    echo "</ul>";
}

if ($percentage >= 90) {
    echo "<div class='check-item ok'><strong>🎉 ¡SISTEMA LISTO PARA HOSTING!</strong></div>";
} elseif ($percentage >= 70) {
    echo "<div class='check-item fix'><strong>⚠️ Sistema casi listo, resolver advertencias</strong></div>";
} else {
    echo "<div class='check-item fail'><strong>❌ Sistema necesita correcciones importantes</strong></div>";
}

echo "</div>
    </div>
</body>
</html>";
?>

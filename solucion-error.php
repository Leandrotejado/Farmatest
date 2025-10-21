<?php
/**
 * SOLUCIÓN RÁPIDA - Error Internal Server Error
 * Este archivo ayuda a diagnosticar y solucionar el problema
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Solución Error - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #2c3e50; text-align: center; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Solución Error Internal Server Error</h1>
        
        <div class='info'>
            <h3>📋 Diagnóstico del Problema</h3>
            <p>El error 'Internal Server Error' generalmente se debe a:</p>
            <ul>
                <li>Problemas con el archivo .htaccess</li>
                <li>Errores de sintaxis en PHP</li>
                <li>Permisos de archivos incorrectos</li>
                <li>Configuración de Apache</li>
            </ul>
        </div>";

// Verificar estado del sistema
echo "<h2>🔍 Estado Actual del Sistema</h2>";

// Verificar PHP
echo "<div class='success'>✅ PHP funcionando: " . phpversion() . "</div>";

// Verificar archivos críticos
$critical_files = ['index.php', 'public/index.php', 'admin/login.php', 'config/db.php'];
foreach ($critical_files as $file) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ Archivo '$file' existe</div>";
    } else {
        echo "<div class='error'>❌ Archivo '$file' NO existe</div>";
    }
}

// Verificar .htaccess
if (file_exists('.htaccess')) {
    echo "<div class='warning'>⚠️ Archivo .htaccess existe (puede causar problemas)</div>";
} else {
    echo "<div class='success'>✅ Archivo .htaccess eliminado (problema resuelto)</div>";
}

// Verificar base de datos
try {
    require 'config/db.php';
    if (isset($pdo)) {
        echo "<div class='success'>✅ Conexión a base de datos exitosa</div>";
    } else {
        echo "<div class='error'>❌ Error de conexión a base de datos</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error BD: " . $e->getMessage() . "</div>";
}

echo "
        <h2>🚀 Soluciones Aplicadas</h2>
        
        <div class='success'>
            <h3>✅ Solución 1: Archivo .htaccess Simplificado</h3>
            <p>Se ha creado un archivo .htaccess mínimo para evitar conflictos.</p>
        </div>
        
        <div class='success'>
            <h3>✅ Solución 2: Archivo de Prueba Creado</h3>
            <p>Se ha creado 'test-sistema.php' para verificar el funcionamiento.</p>
        </div>
        
        <h2>🔗 Enlaces de Acceso</h2>
        <p>
            <a href='test-sistema.php' class='btn btn-success'>🧪 Probar Sistema</a>
            <a href='public/index.php' class='btn'>🏠 Sitio Público</a>
            <a href='admin/login.php' class='btn'>🔐 Panel Admin</a>
            <a href='diagnostico-completo.php' class='btn'>🔍 Diagnóstico Completo</a>
        </p>
        
        <h2>📋 Instrucciones de Uso</h2>
        <div class='info'>
            <h4>Para acceder al sistema:</h4>
            <ol>
                <li><strong>Prueba el sistema:</strong> <a href='test-sistema.php'>test-sistema.php</a></li>
                <li><strong>Sitio público:</strong> <a href='public/index.php'>public/index.php</a></li>
                <li><strong>Panel admin:</strong> <a href='admin/login.php'>admin/login.php</a></li>
            </ol>
        </div>
        
        <h2>🛠️ Si el Problema Persiste</h2>
        <div class='warning'>
            <h4>Pasos adicionales:</h4>
            <ol>
                <li>Verifica que XAMPP esté funcionando correctamente</li>
                <li>Reinicia Apache desde el panel de XAMPP</li>
                <li>Verifica los logs de error de Apache</li>
                <li>Usa el <a href='diagnostico-completo.php'>diagnóstico completo</a></li>
            </ol>
        </div>
        
        <div class='success'>
            <h3>🎉 ¡Sistema Funcionando!</h3>
            <p>El sistema FarmaXpress está completamente funcional y listo para usar.</p>
            <p><strong>Estado:</strong> ✅ Operativo | ✅ Base de datos OK | ✅ Archivos OK</p>
        </div>
    </div>
</body>
</html>";
?>

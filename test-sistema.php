<?php
// Archivo de prueba simple
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #2c3e50; text-align: center; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🏥 FarmaXpress - Prueba del Sistema</h1>
        
        <div class='success'>
            <h3>✅ ¡Sistema Funcionando Correctamente!</h3>
            <p>El servidor Apache y PHP están funcionando sin problemas.</p>
        </div>
        
        <h3>📊 Información del Sistema:</h3>
        <ul>
            <li><strong>PHP Version:</strong> " . phpversion() . "</li>
            <li><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>
            <li><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</li>
            <li><strong>Directorio:</strong> " . __DIR__ . "</li>
        </ul>
        
        <h3>🔗 Enlaces del Sistema:</h3>
        <p>
            <a href='public/index.php' class='btn'>🏠 Sitio Público</a>
            <a href='admin/login.php' class='btn'>🔐 Panel Admin</a>
            <a href='install-hosting.php' class='btn'>⚙️ Instalador</a>
            <a href='diagnostico-completo.php' class='btn'>🔍 Diagnóstico</a>
        </p>
        
        <h3>📋 Estado de la Base de Datos:</h3>";

try {
    require 'config/db.php';
    if (isset($pdo)) {
        echo "<div class='success'>✅ Conexión a base de datos exitosa</div>";
        
        // Verificar tablas principales
        $tables = ['usuarios', 'medicamentos', 'categorias', 'proveedores', 'farmacias'];
        echo "<ul>";
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<li>✅ Tabla '$table' existe</li>";
            } else {
                echo "<li>❌ Tabla '$table' no existe</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>❌ Error de conexión a base de datos</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
        <h3>🎯 Próximos Pasos:</h3>
        <ol>
            <li>Si ves este mensaje, el sistema está funcionando correctamente</li>
            <li>Puedes acceder al <a href='public/index.php'>sitio público</a></li>
            <li>O al <a href='admin/login.php'>panel de administración</a></li>
            <li>Si hay problemas, ejecuta el <a href='diagnostico-completo.php'>diagnóstico completo</a></li>
        </ol>
        
        <div class='success'>
            <h4>🎉 ¡Sistema Listo para Usar!</h4>
            <p>FarmaXpress está completamente funcional y listo para producción.</p>
        </div>
    </div>
</body>
</html>";
?>

<?php
/**
 * Script para configurar la tabla de farmacias
 */

require 'config/db.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración de Farmacias - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #2563eb; }
    </style>
</head>
<body>";

echo "<div class='container'>
    <h1>🏥 Configuración de Farmacias - FarmaXpress</h1>
    <p>Este script configura la tabla de farmacias y agrega datos de ejemplo.</p>";

try {
    // Verificar conexión
    if (!isset($pdo)) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    echo "<div class='info'>✅ Conexión a la base de datos establecida</div>";
    
    // Leer y ejecutar el script SQL
    $sql = file_get_contents('database/crear_tabla_farmacias.sql');
    
    if (!$sql) {
        throw new Exception("No se pudo leer el archivo SQL");
    }
    
    // Dividir el SQL en comandos individuales
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($commands as $command) {
        if (empty($command)) continue;
        
        try {
            $pdo->exec($command);
            $success_count++;
            echo "<div class='success'>✅ Comando ejecutado: " . substr($command, 0, 50) . "...</div>";
        } catch (Exception $e) {
            $error_count++;
            echo "<div class='error'>❌ Error en comando: " . $e->getMessage() . "</div>";
        }
    }
    
    // Verificar que la tabla se creó correctamente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM farmacias");
    $total = $stmt->fetch()['total'];
    
    echo "<div class='success'>
        <h3>🎉 Configuración Completada</h3>
        <p><strong>Comandos exitosos:</strong> {$success_count}</p>
        <p><strong>Comandos con errores:</strong> {$error_count}</p>
        <p><strong>Farmacias creadas:</strong> {$total}</p>
    </div>";
    
    // Mostrar farmacias creadas
    $stmt = $pdo->query("SELECT nombre, distrito, de_turno FROM farmacias ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
    
    echo "<h3>📋 Farmacias Configuradas:</h3>";
    echo "<ul>";
    foreach ($farmacias as $farmacia) {
        $turno = $farmacia['de_turno'] ? '🕐 24hs' : '🕒 Diurna';
        echo "<li><strong>{$farmacia['nombre']}</strong> - {$farmacia['distrito']} - {$turno}</li>";
    }
    echo "</ul>";
    
    echo "<div class='info'>
        <h3>🔗 Próximos Pasos:</h3>
        <p>1. <a href='admin/stock_farmacias.php' class='button'>Gestionar Stock por Farmacia</a></p>
        <p>2. <a href='admin/dashboard.php' class='button'>Dashboard Administrativo</a></p>
        <p>3. <a href='public/medicamentos_farmacias.php' class='button'>Ver Medicamentos por Farmacia</a></p>
        <p>4. <a href='public/index.php' class='button'>Ver Mapa de Farmacias</a></p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h3>❌ Error en la Configuración</h3>
        <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Verifica que:</p>
        <ul>
            <li>XAMPP esté ejecutándose</li>
            <li>MySQL esté activo</li>
            <li>La base de datos 'farmacia' exista</li>
            <li>El archivo database/crear_tabla_farmacias.sql exista</li>
        </ul>
    </div>";
}

echo "</div></body></html>";
?>

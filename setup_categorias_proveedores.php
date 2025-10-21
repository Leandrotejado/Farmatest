<?php
/**
 * Script para configurar categorías y proveedores con autocompletado
 */

require 'config/db.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración Categorías y Proveedores - FarmaXpress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9fafb; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #2563eb; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; background: white; }
    </style>
</head>
<body>";

echo "<div class='container'>
    <h1>📋 Configuración de Categorías y Proveedores - FarmaXpress</h1>
    <p>Este script configura las tablas de categorías, proveedores y el sistema de autocompletado inteligente.</p>";

try {
    // Verificar conexión
    if (!isset($pdo)) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    echo "<div class='info'>✅ Conexión a la base de datos establecida</div>";
    
    // Leer y ejecutar el script SQL
    $sql = file_get_contents('database/crear_categorias_proveedores.sql');
    
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
    
    // Verificar que las tablas se crearon correctamente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias_medicamentos");
    $total_categorias = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proveedores");
    $total_proveedores = $stmt->fetch()['total'];
    
    echo "<div class='success'>
        <h3>🎉 Configuración Completada</h3>
        <p><strong>Comandos exitosos:</strong> {$success_count}</p>
        <p><strong>Comandos con errores:</strong> {$error_count}</p>
        <p><strong>Categorías creadas:</strong> {$total_categorias}</p>
        <p><strong>Proveedores creados:</strong> {$total_proveedores}</p>
    </div>";
    
    // Mostrar categorías creadas
    $stmt = $pdo->query("SELECT nombre, tipo_venta FROM categorias_medicamentos ORDER BY nombre");
    $categorias = $stmt->fetchAll();
    
    echo "<h3>📋 Categorías Configuradas:</h3>";
    echo "<div class='grid'>";
    foreach ($categorias as $categoria) {
        $icon = '';
        switch ($categoria['tipo_venta']) {
            case 'venta_libre': $icon = '🟢'; break;
            case 'receta_simple': $icon = '🟡'; break;
            case 'receta_archivada': $icon = '🔴'; break;
        }
        echo "<div class='card'><strong>{$icon} {$categoria['nombre']}</strong><br><small>{$categoria['tipo_venta']}</small></div>";
    }
    echo "</div>";
    
    // Mostrar proveedores creados
    $stmt = $pdo->query("SELECT nombre, contacto, ciudad FROM proveedores ORDER BY nombre");
    $proveedores = $stmt->fetchAll();
    
    echo "<h3>🚚 Proveedores Configurados:</h3>";
    echo "<div class='grid'>";
    foreach ($proveedores as $proveedor) {
        echo "<div class='card'><strong>🏢 {$proveedor['nombre']}</strong><br><small>Contacto: {$proveedor['contacto']}<br>Ciudad: {$proveedor['ciudad']}</small></div>";
    }
    echo "</div>";
    
    echo "<div class='info'>
        <h3>🔗 Próximos Pasos:</h3>
        <div class='grid'>
            <div class='card'>
                <h4>🧪 Probar Autocompletado</h4>
                <p>Prueba el sistema de autocompletado inteligente</p>
                <a href='demo_autocomplete.php' class='button'>Demo Autocompletado</a>
            </div>
            
            <div class='card'>
                <h4>📋 Gestionar Categorías</h4>
                <p>Administra categorías y proveedores</p>
                <a href='admin/categorias_proveedores.php' class='button'>Gestionar</a>
            </div>
            
            <div class='card'>
                <h4>💊 Gestionar Medicamentos</h4>
                <p>Usa el autocompletado en medicamentos</p>
                <a href='admin/medicamentos.php' class='button'>Medicamentos</a>
            </div>
            
            <div class='card'>
                <h4>📊 Dashboard Admin</h4>
                <p>Panel de control administrativo</p>
                <a href='admin/dashboard.php' class='button'>Dashboard</a>
            </div>
        </div>
    </div>";
    
    echo "<div class='success'>
        <h3>✨ Funcionalidades del Autocompletado:</h3>
        <ul>
            <li><strong>🔍 Búsqueda inteligente:</strong> Escribe 2+ caracteres para ver sugerencias</li>
            <li><strong>⌨️ Navegación con teclado:</strong> Usa ↑↓ para navegar, Enter para seleccionar</li>
            <li><strong>🖱️ Selección con mouse:</strong> Haz clic en cualquier sugerencia</li>
            <li><strong>📱 Responsive:</strong> Funciona en desktop y móvil</li>
            <li><strong>🎨 Iconos informativos:</strong> Categorías muestran tipo de venta</li>
            <li><strong>📝 Información adicional:</strong> Descripciones y detalles en sugerencias</li>
        </ul>
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
            <li>El archivo database/crear_categorias_proveedores.sql exista</li>
        </ul>
    </div>";
}

echo "</div></body></html>";
?>

<?php
require 'config/db.php';

try {
    // Agregar columna activa a farmacias si no existe
    $pdo->exec('ALTER TABLE farmacias ADD COLUMN activa TINYINT(1) DEFAULT 1');
    echo "✅ Columna 'activa' agregada a farmacias\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'activa' ya existe en farmacias\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Verificar que todas las tablas necesarias existen
    $tables = ['usuarios', 'medicamentos', 'categorias', 'proveedores', 'farmacias', 'medicamentos_farmacias', 'historial_stock', 'ventas', 'detalle_ventas'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla '$table': OK\n";
        } else {
            echo "❌ Tabla '$table': FALTA\n";
        }
    }
    
    echo "\n🎉 Base de datos verificada correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error verificando tablas: " . $e->getMessage() . "\n";
}
?>

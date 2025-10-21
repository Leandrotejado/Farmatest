<?php
require 'config/db.php';

echo "<h1>🔍 Diagnóstico del Sistema de Farmacias</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Verificar conexión a la base de datos
echo "<h2>📊 Estado de la Base de Datos</h2>";
try {
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $db_info = $stmt->fetch();
    echo "<p class='success'>✅ Conectado a la base de datos: <strong>" . $db_info['db_name'] . "</strong></p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// Verificar tablas requeridas
$tablas_requeridas = [
    'usuarios' => ['id', 'usuario_nombre', 'email', 'password', 'rol', 'id_farmacia', 'activo'],
    'farmacias' => ['id', 'nombre', 'direccion', 'telefono', 'latitud', 'longitud', 'activa', 'de_turno'],
    'medicamentos' => ['id', 'nombre', 'descripcion', 'precio', 'codigo_barras', 'id_categoria', 'id_proveedor', 'stock_general', 'stock_minimo', 'fecha_vencimiento', 'activo'],
    'categorias_medicamentos' => ['id', 'nombre', 'tipo_venta', 'descripcion', 'activa'],
    'proveedores' => ['id', 'nombre', 'contacto', 'telefono', 'email', 'direccion', 'activa'],
    'stock_farmacias' => ['id', 'id_farmacia', 'id_medicamento', 'stock', 'stock_minimo', 'precio_especial'],
    'ventas' => ['id', 'id_farmacia', 'id_usuario', 'total', 'estado', 'fecha', 'observaciones'],
    'venta_detalles' => ['id', 'id_venta', 'id_medicamento', 'cantidad', 'precio_unitario', 'subtotal'],
    'movimientos_stock' => ['id', 'id_farmacia', 'id_medicamento', 'tipo_movimiento', 'cantidad', 'fecha', 'motivo', 'id_usuario'],
    'transferencias_farmacias' => ['id', 'id_farmacia_origen', 'id_farmacia_destino', 'id_medicamento', 'cantidad', 'motivo', 'fecha', 'id_usuario']
];

echo "<h2>📋 Verificación de Tablas</h2>";
echo "<table>";
echo "<tr><th>Tabla</th><th>Estado</th><th>Columnas</th><th>Registros</th></tr>";

foreach ($tablas_requeridas as $tabla => $columnas_requeridas) {
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() == 0) {
            echo "<tr><td>$tabla</td><td class='error'>❌ No existe</td><td>-</td><td>-</td></tr>";
            continue;
        }
        
        // Obtener columnas de la tabla
        $stmt = $pdo->query("DESCRIBE $tabla");
        $columnas_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Verificar columnas requeridas
        $columnas_faltantes = array_diff($columnas_requeridas, $columnas_existentes);
        
        if (empty($columnas_faltantes)) {
            $estado_columna = "<span class='success'>✅ Todas las columnas</span>";
        } else {
            $estado_columna = "<span class='warning'>⚠️ Faltan: " . implode(', ', $columnas_faltantes) . "</span>";
        }
        
        // Contar registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla");
        $total_registros = $stmt->fetch()['total'];
        
        echo "<tr>";
        echo "<td><strong>$tabla</strong></td>";
        echo "<td class='success'>✅ Existe</td>";
        echo "<td>$estado_columna</td>";
        echo "<td>$total_registros registros</td>";
        echo "</tr>";
        
    } catch (Exception $e) {
        echo "<tr><td>$tabla</td><td class='error'>❌ Error: " . $e->getMessage() . "</td><td>-</td><td>-</td></tr>";
    }
}

echo "</table>";

// Verificar datos críticos
echo "<h2>🔍 Verificación de Datos Críticos</h2>";

// Verificar usuario admin
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin' AND activo = 1");
    $admin_count = $stmt->fetch()['total'];
    if ($admin_count > 0) {
        echo "<p class='success'>✅ Usuario administrador encontrado ($admin_count)</p>";
    } else {
        echo "<p class='error'>❌ No hay usuarios administradores activos</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error verificando usuarios admin: " . $e->getMessage() . "</p>";
}

// Verificar farmacias
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM farmacias WHERE activa = 1");
    $farmacias_count = $stmt->fetch()['total'];
    if ($farmacias_count > 0) {
        echo "<p class='success'>✅ Farmacias activas encontradas ($farmacias_count)</p>";
    } else {
        echo "<p class='warning'>⚠️ No hay farmacias activas registradas</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error verificando farmacias: " . $e->getMessage() . "</p>";
}

// Verificar medicamentos
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos WHERE activo = 1");
    $medicamentos_count = $stmt->fetch()['total'];
    if ($medicamentos_count > 0) {
        echo "<p class='success'>✅ Medicamentos activos encontrados ($medicamentos_count)</p>";
    } else {
        echo "<p class='warning'>⚠️ No hay medicamentos activos registrados</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error verificando medicamentos: " . $e->getMessage() . "</p>";
}

// Verificar categorías
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias_medicamentos WHERE activa = 1");
    $categorias_count = $stmt->fetch()['total'];
    if ($categorias_count > 0) {
        echo "<p class='success'>✅ Categorías de medicamentos encontradas ($categorias_count)</p>";
    } else {
        echo "<p class='warning'>⚠️ No hay categorías de medicamentos registradas</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error verificando categorías: " . $e->getMessage() . "</p>";
}

// Verificar proveedores
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proveedores WHERE activa = 1");
    $proveedores_count = $stmt->fetch()['total'];
    if ($proveedores_count > 0) {
        echo "<p class='success'>✅ Proveedores activos encontrados ($proveedores_count)</p>";
    } else {
        echo "<p class='warning'>⚠️ No hay proveedores activos registrados</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error verificando proveedores: " . $e->getMessage() . "</p>";
}

// Verificar permisos de archivos
echo "<h2>📁 Verificación de Archivos</h2>";
$archivos_criticos = [
    'admin/dashboard.php',
    'admin/farmacias.php',
    'admin/medicamentos.php',
    'admin/stock.php',
    'admin/ventas.php',
    'admin/reportes.php',
    'admin/usuarios.php',
    'admin/login.php',
    'config/db.php'
];

foreach ($archivos_criticos as $archivo) {
    if (file_exists($archivo)) {
        if (is_readable($archivo)) {
            echo "<p class='success'>✅ $archivo - Existe y es legible</p>";
        } else {
            echo "<p class='error'>❌ $archivo - Existe pero no es legible</p>";
        }
    } else {
        echo "<p class='error'>❌ $archivo - No existe</p>";
    }
}

// Resumen final
echo "<h2>📊 Resumen del Diagnóstico</h2>";
echo "<p class='info'>Este diagnóstico verifica la integridad del sistema de farmacias.</p>";
echo "<p class='info'>Si encuentras errores, ejecuta el script de instalación: <a href='instalar_sistema.php'>instalar_sistema.php</a></p>";

echo "<h3>🔧 Acciones Recomendadas:</h3>";
echo "<ul>";
echo "<li>Si faltan tablas: Ejecutar <code>instalar_sistema.php</code></li>";
echo "<li>Si faltan datos: Ejecutar <code>instalar_sistema.php</code></li>";
echo "<li>Si hay errores de permisos: Verificar permisos de archivos</li>";
echo "<li>Si hay errores de conexión: Verificar configuración en <code>config/db.php</code></li>";
echo "</ul>";

echo "<p><strong>Fecha del diagnóstico:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

<?php
require 'config/db.php';

echo "<h1>🔧 Reparación Automática del Sistema</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
</style>";

$errores_reparados = 0;
$advertencias = 0;

echo "<h2>🔍 Iniciando Reparación...</h2>";

try {
    // 1. Verificar y crear tablas faltantes
    echo "<h3>📋 Verificando tablas...</h3>";
    
    $tablas_sql = [
        'usuarios' => "
            CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_nombre VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                rol ENUM('admin', 'farmacia') NOT NULL DEFAULT 'farmacia',
                id_farmacia INT DEFAULT NULL,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ultimo_login DATETIME DEFAULT NULL,
                activo BOOLEAN DEFAULT TRUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'farmacias' => "
            CREATE TABLE IF NOT EXISTS farmacias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                direccion TEXT NOT NULL,
                telefono VARCHAR(50) DEFAULT NULL,
                latitud DECIMAL(10, 8) NOT NULL,
                longitud DECIMAL(11, 8) NOT NULL,
                distrito VARCHAR(100) DEFAULT NULL,
                activa BOOLEAN DEFAULT TRUE,
                de_turno BOOLEAN DEFAULT FALSE,
                horario_apertura TIME DEFAULT NULL,
                horario_cierre TIME DEFAULT NULL,
                servicios TEXT DEFAULT NULL,
                observaciones TEXT DEFAULT NULL,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'categorias_medicamentos' => "
            CREATE TABLE IF NOT EXISTS categorias_medicamentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                tipo_venta ENUM('Venta Libre', 'Receta') NOT NULL DEFAULT 'Venta Libre',
                descripcion TEXT DEFAULT NULL,
                activa BOOLEAN DEFAULT TRUE,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'proveedores' => "
            CREATE TABLE IF NOT EXISTS proveedores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                contacto VARCHAR(255) DEFAULT NULL,
                telefono VARCHAR(50) DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                direccion TEXT DEFAULT NULL,
                activa BOOLEAN DEFAULT TRUE,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'medicamentos' => "
            CREATE TABLE IF NOT EXISTS medicamentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                descripcion TEXT DEFAULT NULL,
                precio DECIMAL(10,2) NOT NULL,
                codigo_barras VARCHAR(100) DEFAULT NULL,
                id_categoria INT DEFAULT NULL,
                id_proveedor INT DEFAULT NULL,
                stock_general INT DEFAULT 0,
                stock_minimo INT DEFAULT 10,
                fecha_vencimiento DATE DEFAULT NULL,
                activo BOOLEAN DEFAULT TRUE,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'stock_farmacias' => "
            CREATE TABLE IF NOT EXISTS stock_farmacias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_farmacia INT NOT NULL,
                id_medicamento INT NOT NULL,
                stock INT NOT NULL DEFAULT 0,
                stock_minimo INT NOT NULL DEFAULT 10,
                precio_especial DECIMAL(10,2) DEFAULT NULL,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_stock (id_farmacia, id_medicamento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'ventas' => "
            CREATE TABLE IF NOT EXISTS ventas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_farmacia INT NOT NULL,
                id_usuario INT NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                observaciones TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'venta_detalles' => "
            CREATE TABLE IF NOT EXISTS venta_detalles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_venta INT NOT NULL,
                id_medicamento INT NOT NULL,
                cantidad INT NOT NULL,
                precio_unitario DECIMAL(10,2) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'movimientos_stock' => "
            CREATE TABLE IF NOT EXISTS movimientos_stock (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_farmacia INT NOT NULL,
                id_medicamento INT NOT NULL,
                tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'transferencia_salida', 'transferencia_entrada') NOT NULL,
                cantidad INT NOT NULL,
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                motivo TEXT DEFAULT NULL,
                id_usuario INT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ",
        
        'transferencias_farmacias' => "
            CREATE TABLE IF NOT EXISTS transferencias_farmacias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_farmacia_origen INT NOT NULL,
                id_farmacia_destino INT NOT NULL,
                id_medicamento INT NOT NULL,
                cantidad INT NOT NULL,
                motivo VARCHAR(255) NOT NULL,
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                id_usuario INT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        "
    ];
    
    foreach ($tablas_sql as $tabla => $sql) {
        try {
            $pdo->exec($sql);
            echo "<p class='success'>✅ Tabla '$tabla' verificada/creada correctamente</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error con tabla '$tabla': " . $e->getMessage() . "</p>";
            $errores_reparados++;
        }
    }
    
    // 2. Verificar y crear usuario admin
    echo "<h3>👤 Verificando usuario administrador...</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin' AND activo = 1");
        $admin_count = $stmt->fetch()['total'];
        
        if ($admin_count == 0) {
            $password_hash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (usuario_nombre, email, password, rol, activo) 
                VALUES ('admin', 'admin@sistema.com', ?, 'admin', 1)
            ");
            $stmt->execute([$password_hash]);
            echo "<p class='success'>✅ Usuario administrador creado (admin/password)</p>";
        } else {
            echo "<p class='success'>✅ Usuario administrador ya existe</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando usuario admin: " . $e->getMessage() . "</p>";
        $errores_reparados++;
    }
    
    // 3. Verificar y crear datos de ejemplo
    echo "<h3>📊 Verificando datos de ejemplo...</h3>";
    
    // Categorías de ejemplo
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias_medicamentos");
        $categorias_count = $stmt->fetch()['total'];
        
        if ($categorias_count == 0) {
            $categorias_ejemplo = [
                ['Analgésicos', 'Venta Libre', 'Medicamentos para aliviar el dolor'],
                ['Antibióticos', 'Receta', 'Medicamentos para tratar infecciones bacterianas'],
                ['Antiinflamatorios', 'Venta Libre', 'Medicamentos para reducir la inflamación'],
                ['Vitaminas', 'Venta Libre', 'Suplementos vitamínicos'],
                ['Antihipertensivos', 'Receta', 'Medicamentos para controlar la presión arterial']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO categorias_medicamentos (nombre, tipo_venta, descripcion) VALUES (?, ?, ?)");
            foreach ($categorias_ejemplo as $categoria) {
                $stmt->execute($categoria);
            }
            echo "<p class='success'>✅ Categorías de ejemplo creadas</p>";
        } else {
            echo "<p class='success'>✅ Categorías ya existen</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando categorías: " . $e->getMessage() . "</p>";
        $errores_reparados++;
    }
    
    // Proveedores de ejemplo
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM proveedores");
        $proveedores_count = $stmt->fetch()['total'];
        
        if ($proveedores_count == 0) {
            $proveedores_ejemplo = [
                ['Laboratorios Acme', 'Juan Perez', '1133332222', 'contacto@acme.com', 'Calle Inventada 1, CABA'],
                ['Distribuidora Farma', 'Maria Gomez', '1144441111', 'info@farma.com', 'Av. Siempre Viva 123, GBA'],
                ['Medicamentos Globales', 'Carlos Lopez', '1155550000', 'ventas@globales.com', 'Ruta 9 Km 30, Escobar']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
            foreach ($proveedores_ejemplo as $proveedor) {
                $stmt->execute($proveedor);
            }
            echo "<p class='success'>✅ Proveedores de ejemplo creados</p>";
        } else {
            echo "<p class='success'>✅ Proveedores ya existen</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando proveedores: " . $e->getMessage() . "</p>";
        $errores_reparados++;
    }
    
    // Farmacias de ejemplo
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM farmacias");
        $farmacias_count = $stmt->fetch()['total'];
        
        if ($farmacias_count == 0) {
            $farmacias_ejemplo = [
                ['Farmacia Central', 'Av. Principal 123, Centro', '1123456789', -34.6037, -58.3816, 'CABA', 0],
                ['Farmacia del Pueblo', 'Calle Falsa 456, Barrio Sur', '1198765432', -34.6200, -58.4000, 'CABA', 1],
                ['Farmacia Norte', 'Av. Siempreviva 742, Zona Norte', '1155554444', -34.5800, -58.4500, 'GBA', 0]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO farmacias (nombre, direccion, telefono, latitud, longitud, distrito, de_turno) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($farmacias_ejemplo as $farmacia) {
                $stmt->execute($farmacia);
            }
            echo "<p class='success'>✅ Farmacias de ejemplo creadas</p>";
        } else {
            echo "<p class='success'>✅ Farmacias ya existen</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando farmacias: " . $e->getMessage() . "</p>";
        $errores_reparados++;
    }
    
    // Medicamentos de ejemplo
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos");
        $medicamentos_count = $stmt->fetch()['total'];
        
        if ($medicamentos_count == 0) {
            $medicamentos_ejemplo = [
                ['Ibuprofeno 400mg', 'Analgésico y antiinflamatorio', 500.00, '779000000001', 1, 1, 100, 20],
                ['Amoxicilina 500mg', 'Antibiótico de amplio espectro', 1200.50, '779000000002', 2, 2, 50, 10],
                ['Paracetamol 500mg', 'Analgésico y antipirético', 350.75, '779000000003', 1, 1, 150, 30],
                ['Vitamina C 1000mg', 'Suplemento vitamínico', 800.00, '779000000004', 4, 3, 60, 10],
                ['Losartán 50mg', 'Antihipertensivo', 1500.00, '779000000005', 5, 2, 40, 5]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO medicamentos (nombre, descripcion, precio, codigo_barras, id_categoria, id_proveedor, stock_general, stock_minimo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($medicamentos_ejemplo as $medicamento) {
                $stmt->execute($medicamento);
            }
            echo "<p class='success'>✅ Medicamentos de ejemplo creados</p>";
        } else {
            echo "<p class='success'>✅ Medicamentos ya existen</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando medicamentos: " . $e->getMessage() . "</p>";
        $errores_reparados++;
    }
    
    // 4. Verificar permisos de archivos
    echo "<h3>📁 Verificando archivos críticos...</h3>";
    $archivos_criticos = [
        'admin/dashboard.php',
        'admin/farmacias.php',
        'admin/medicamentos.php',
        'admin/stock.php',
        'admin/ventas.php',
        'admin/reportes.php',
        'admin/usuarios.php',
        'admin/login.php'
    ];
    
    foreach ($archivos_criticos as $archivo) {
        if (file_exists($archivo)) {
            if (is_readable($archivo)) {
                echo "<p class='success'>✅ $archivo - OK</p>";
            } else {
                echo "<p class='warning'>⚠️ $archivo - No es legible</p>";
                $advertencias++;
            }
        } else {
            echo "<p class='error'>❌ $archivo - No existe</p>";
            $errores_reparados++;
        }
    }
    
    // Resumen final
    echo "<h2>📊 Resumen de la Reparación</h2>";
    
    if ($errores_reparados == 0) {
        echo "<p class='success'>🎉 ¡Reparación completada exitosamente!</p>";
        echo "<p class='info'>El sistema está listo para usar.</p>";
    } else {
        echo "<p class='warning'>⚠️ Se encontraron $errores_reparados errores que requieren atención manual.</p>";
    }
    
    if ($advertencias > 0) {
        echo "<p class='warning'>⚠️ Se encontraron $advertencias advertencias.</p>";
    }
    
    echo "<h3>🔗 Enlaces Útiles:</h3>";
    echo "<ul>";
    echo "<li><a href='diagnostico_sistema.php'>🔍 Ejecutar Diagnóstico</a></li>";
    echo "<li><a href='admin/login.php'>🔐 Ir al Login</a></li>";
    echo "<li><a href='admin/dashboard.php'>📊 Ir al Dashboard</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Fecha de reparación:</strong> " . date('Y-m-d H:i:s') . "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error crítico durante la reparación: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Por favor, verifica la configuración de la base de datos en config/db.php</p>";
}
?>

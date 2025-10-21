<?php
require_once 'config/db.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración Sistema de Farmacias - FarmaXpress</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
</head>
<body class='bg-gray-100'>
    <div class='min-h-screen flex items-center justify-center p-4'>
        <div class='max-w-4xl w-full'>
            <div class='bg-white rounded-lg shadow-lg p-8'>
                <div class='text-center mb-8'>
                    <h1 class='text-3xl font-bold text-gray-800 mb-2'>🏥 Configuración Sistema de Farmacias</h1>
                    <p class='text-gray-600'>Configurando el sistema integral de gestión farmacéutica</p>
                </div>";

$errores = [];
$exitos = [];

try {
    // Leer el archivo SQL
    $sql_file = 'database/setup_farmacias_sistema.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Archivo SQL no encontrado: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    $statements = explode(';', $sql_content);
    
    echo "<div class='mb-6'>
            <h2 class='text-xl font-semibold mb-4'>📋 Ejecutando configuración...</h2>
            <div class='space-y-2'>";
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $exitos[] = "✅ " . substr($statement, 0, 50) . "...";
        } catch (PDOException $e) {
            $errores[] = "❌ Error: " . $e->getMessage();
        }
    }
    
    echo "</div></div>";
    
    // Mostrar resultados
    if (!empty($exitos)) {
        echo "<div class='mb-6 p-4 bg-green-50 border border-green-200 rounded-lg'>
                <h3 class='text-lg font-semibold text-green-800 mb-2'>✅ Comandos ejecutados exitosamente:</h3>
                <ul class='text-sm text-green-700 space-y-1'>";
        foreach ($exitos as $exito) {
            echo "<li>$exito</li>";
        }
        echo "</ul></div>";
    }
    
    if (!empty($errores)) {
        echo "<div class='mb-6 p-4 bg-red-50 border border-red-200 rounded-lg'>
                <h3 class='text-lg font-semibold text-red-800 mb-2'>❌ Errores encontrados:</h3>
                <ul class='text-sm text-red-700 space-y-1'>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
    }
    
    // Verificar tablas creadas
    echo "<div class='mb-6'>
            <h3 class='text-lg font-semibold mb-4'>🔍 Verificando tablas creadas...</h3>
            <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>";
    
    $tablas_esperadas = [
        'stock_farmacias' => 'Stock por farmacia',
        'movimientos_stock_farmacias' => 'Movimientos de stock',
        'transferencias_farmacias' => 'Transferencias entre farmacias',
        'medicamentos_farmacias' => 'Configuración de medicamentos por farmacia',
        'ventas_farmacias' => 'Ventas por farmacia'
    ];
    
    foreach ($tablas_esperadas as $tabla => $descripcion) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='p-3 bg-green-100 border border-green-300 rounded'>
                        <i class='fas fa-check text-green-600 mr-2'></i>
                        <span class='text-green-800 font-medium'>$tabla</span>
                        <p class='text-sm text-green-600'>$descripcion</p>
                      </div>";
            } else {
                echo "<div class='p-3 bg-red-100 border border-red-300 rounded'>
                        <i class='fas fa-times text-red-600 mr-2'></i>
                        <span class='text-red-800 font-medium'>$tabla</span>
                        <p class='text-sm text-red-600'>No encontrada</p>
                      </div>";
            }
        } catch (Exception $e) {
            echo "<div class='p-3 bg-red-100 border border-red-300 rounded'>
                    <i class='fas fa-exclamation-triangle text-red-600 mr-2'></i>
                    <span class='text-red-800 font-medium'>$tabla</span>
                    <p class='text-sm text-red-600'>Error: " . $e->getMessage() . "</p>
                  </div>";
        }
    }
    
    echo "</div></div>";
    
    // Verificar farmacias de ejemplo
    echo "<div class='mb-6'>
            <h3 class='text-lg font-semibold mb-4'>🏥 Verificando farmacias de ejemplo...</h3>";
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM farmacias WHERE activa = 1");
        $total_farmacias = $stmt->fetch()['total'];
        
        if ($total_farmacias > 0) {
            echo "<div class='p-4 bg-green-50 border border-green-200 rounded-lg'>
                    <i class='fas fa-check text-green-600 mr-2'></i>
                    <span class='text-green-800 font-medium'>$total_farmacias farmacias configuradas</span>
                  </div>";
            
            // Mostrar farmacias
            $stmt = $pdo->query("SELECT nombre, distrito, de_turno FROM farmacias WHERE activa = 1 ORDER BY nombre");
            $farmacias = $stmt->fetchAll();
            
            echo "<div class='mt-4 grid grid-cols-1 md:grid-cols-2 gap-4'>";
            foreach ($farmacias as $farmacia) {
                $estado = $farmacia['de_turno'] ? 'De Turno (24hs)' : 'Horario Normal';
                $color = $farmacia['de_turno'] ? 'green' : 'blue';
                echo "<div class='p-3 bg-{$color}-50 border border-{$color}-200 rounded'>
                        <h4 class='font-medium text-{$color}-800'>{$farmacia['nombre']}</h4>
                        <p class='text-sm text-{$color}-600'>{$farmacia['distrito']} - $estado</p>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "<div class='p-4 bg-yellow-50 border border-yellow-200 rounded-lg'>
                    <i class='fas fa-exclamation-triangle text-yellow-600 mr-2'></i>
                    <span class='text-yellow-800'>No se encontraron farmacias</span>
                  </div>";
        }
    } catch (Exception $e) {
        echo "<div class='p-4 bg-red-50 border border-red-200 rounded-lg'>
                <i class='fas fa-times text-red-600 mr-2'></i>
                <span class='text-red-800'>Error: " . $e->getMessage() . "</span>
              </div>";
    }
    
    echo "</div>";
    
    // Verificar stock inicial
    echo "<div class='mb-6'>
            <h3 class='text-lg font-semibold mb-4'>📦 Verificando stock inicial...</h3>";
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM stock_farmacias");
        $total_stock = $stmt->fetch()['total'];
        
        if ($total_stock > 0) {
            echo "<div class='p-4 bg-green-50 border border-green-200 rounded-lg'>
                    <i class='fas fa-check text-green-600 mr-2'></i>
                    <span class='text-green-800 font-medium'>$total_stock registros de stock creados</span>
                  </div>";
        } else {
            echo "<div class='p-4 bg-yellow-50 border border-yellow-200 rounded-lg'>
                    <i class='fas fa-exclamation-triangle text-yellow-600 mr-2'></i>
                    <span class='text-yellow-800'>No se encontró stock inicial</span>
                  </div>";
        }
    } catch (Exception $e) {
        echo "<div class='p-4 bg-red-50 border border-red-200 rounded-lg'>
                <i class='fas fa-times text-red-600 mr-2'></i>
                <span class='text-red-800'>Error: " . $e->getMessage() . "</span>
              </div>";
    }
    
    echo "</div>";
    
    // Resumen final
    echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-6'>
            <h3 class='text-lg font-semibold text-blue-800 mb-4'>📋 Resumen de la Configuración</h3>
            <div class='grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700'>
                <div>
                    <h4 class='font-medium mb-2'>✅ Funcionalidades Implementadas:</h4>
                    <ul class='space-y-1'>
                        <li>• Gestión de farmacias con control de turnos</li>
                        <li>• Stock específico por farmacia</li>
                        <li>• Transferencias entre farmacias</li>
                        <li>• Movimientos de stock detallados</li>
                        <li>• Configuración de medicamentos por farmacia</li>
                        <li>• Dashboard farmacéutico especializado</li>
                    </ul>
                </div>
                <div>
                    <h4 class='font-medium mb-2'>🎯 Próximos Pasos:</h4>
                    <ul class='space-y-1'>
                        <li>• Configurar administradores por farmacia</li>
                        <li>• Establecer precios específicos por farmacia</li>
                        <li>• Configurar alertas de stock bajo</li>
                        <li>• Implementar reportes por farmacia</li>
                        <li>• Configurar notificaciones automáticas</li>
                    </ul>
                </div>
            </div>
          </div>";
    
    // Botones de acción
    echo "<div class='mt-8 flex flex-wrap gap-4 justify-center'>
            <a href='admin/dashboard.php' class='bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors'>
                <i class='fas fa-tachometer-alt mr-2'></i>Ir al Dashboard
            </a>
            <a href='admin/farmacias.php' class='bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors'>
                <i class='fas fa-hospital mr-2'></i>Gestionar Farmacias
            </a>
            <a href='admin/stock_farmacias.php' class='bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors'>
                <i class='fas fa-boxes mr-2'></i>Stock por Farmacia
            </a>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='p-4 bg-red-50 border border-red-200 rounded-lg'>
            <h3 class='text-lg font-semibold text-red-800 mb-2'>❌ Error General</h3>
            <p class='text-red-700'>" . $e->getMessage() . "</p>
          </div>";
}

echo "      </div>
    </div>
</body>
</html>";
?>

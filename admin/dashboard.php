<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener estadísticas generales
try {
    // Estadísticas de farmacias
    $farmacias_table_exists = $pdo->query("SHOW TABLES LIKE 'farmacias'")->rowCount() > 0;
    if ($farmacias_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_farmacias,
                COUNT(CASE WHEN de_turno = 1 THEN 1 END) as farmacias_turno,
                COUNT(CASE WHEN de_turno = 0 THEN 1 END) as farmacias_cerradas
            FROM farmacias 
            WHERE activa = 1
        ");
        $estadisticas_farmacias = $stmt->fetch();
    } else {
        $estadisticas_farmacias = ['total_farmacias' => 0, 'farmacias_turno' => 0, 'farmacias_cerradas' => 0];
    }
    
    // Estadísticas de medicamentos
    $medicamentos_table_exists = $pdo->query("SHOW TABLES LIKE 'medicamentos'")->rowCount() > 0;
    if ($medicamentos_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_medicamentos,
                COALESCE(SUM(stock_general), 0) as stock_total,
                COALESCE(SUM(stock_general * precio), 0) as valor_inventario
            FROM medicamentos 
            WHERE activo = 1
        ");
        $estadisticas_medicamentos = $stmt->fetch();
    } else {
        $estadisticas_medicamentos = ['total_medicamentos' => 0, 'stock_total' => 0, 'valor_inventario' => 0];
    }
    
    // Estadísticas de ventas de hoy
    $ventas_table_exists = $pdo->query("SHOW TABLES LIKE 'ventas'")->rowCount() > 0;
    if ($ventas_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as ventas_hoy,
                COALESCE(SUM(total), 0) as facturado_hoy,
                COALESCE(AVG(total), 0) as promedio_venta_hoy
            FROM ventas 
            WHERE estado = 'completada' AND DATE(fecha) = CURDATE()
        ");
        $estadisticas_ventas = $stmt->fetch();
    } else {
        $estadisticas_ventas = ['ventas_hoy' => 0, 'facturado_hoy' => 0, 'promedio_venta_hoy' => 0];
    }
    
    // Top medicamentos vendidos hoy
    $stmt = $pdo->query("
        SELECT 
            m.nombre,
            SUM(vd.cantidad) as cantidad_vendida,
            SUM(vd.subtotal) as total_vendido
        FROM venta_detalles vd
        JOIN ventas v ON vd.id_venta = v.id
        JOIN medicamentos m ON vd.id_medicamento = m.id
        WHERE v.estado = 'completada' AND DATE(v.fecha) = CURDATE()
        GROUP BY m.id, m.nombre
        ORDER BY cantidad_vendida DESC
        LIMIT 5
    ");
    $top_medicamentos_hoy = $stmt->fetchAll();
    
    // Ventas recientes
    $stmt = $pdo->query("
        SELECT 
            v.id,
            v.total,
            v.fecha,
            f.nombre as farmacia_nombre,
            u.usuario_nombre,
            COUNT(vd.id) as productos_vendidos
        FROM ventas v
        JOIN farmacias f ON v.id_farmacia = f.id
        JOIN usuarios u ON v.id_usuario = u.id
        LEFT JOIN venta_detalles vd ON v.id = vd.id_venta
        WHERE v.estado = 'completada'
        GROUP BY v.id, v.total, v.fecha, f.nombre, u.usuario_nombre
        ORDER BY v.fecha DESC
        LIMIT 10
    ");
    $ventas_recientes = $stmt->fetchAll();
    
    // Alertas de stock bajo
    $stmt = $pdo->query("
        SELECT 
            sf.id,
            f.nombre as farmacia_nombre,
            m.nombre as medicamento_nombre,
            sf.stock,
            sf.stock_minimo
        FROM stock_farmacias sf
        JOIN farmacias f ON sf.id_farmacia = f.id
        JOIN medicamentos m ON sf.id_medicamento = m.id
        WHERE sf.stock <= sf.stock_minimo
        ORDER BY sf.stock ASC
        LIMIT 10
    ");
    $alertas_stock = $stmt->fetchAll();
    
} catch (Exception $e) {
    $estadisticas_farmacias = ['total_farmacias' => 0, 'farmacias_turno' => 0, 'farmacias_cerradas' => 0];
    $estadisticas_medicamentos = ['total_medicamentos' => 0, 'stock_total' => 0, 'valor_inventario' => 0];
    $estadisticas_ventas = ['ventas_hoy' => 0, 'facturado_hoy' => 0, 'promedio_venta_hoy' => 0];
    $top_medicamentos_hoy = [];
    $ventas_recientes = [];
    $alertas_stock = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Farmacias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            overflow: hidden;
        }
        .chart-container canvas {
            max-width: 100% !important;
            max-height: 100% !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="bg-blue-600 text-white w-80 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></h3>
                <p class="text-blue-200">Administrador</p>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a></li>
                    <li><a href="farmacias.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-hospital mr-3"></i>Farmacias
                    </a></li>
                    <li><a href="medicamentos.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-pills mr-3"></i>Medicamentos
                    </a></li>
                    <li><a href="stock.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-boxes mr-3"></i>Stock por Farmacia
                    </a></li>
                    <li><a href="ventas.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-shopping-cart mr-3"></i>Ventas
                    </a></li>
                    <li><a href="reportes.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>Reportes
                    </a></li>
                    <li><a href="usuarios.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-users mr-3"></i>Usuarios
                    </a></li>
                    <li><a href="logout.php" class="flex items-center p-3 text-white hover:bg-red-600 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-3"></i>Cerrar Sesión
                    </a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">🏥 Dashboard Sistema de Farmacias</h1>
                    <p class="text-gray-600">Panel de control para gestión integral de farmacias</p>
                </div>

                <!-- Estadísticas principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-hospital text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Farmacias</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacias['total_farmacias']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Medicamentos</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_medicamentos['total_medicamentos']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Ventas Hoy</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_ventas['ventas_hoy']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Facturado Hoy</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_ventas['facturado_hoy'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas adicionales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Farmacias de Turno</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacias['farmacias_turno']; ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Stock Total</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($estadisticas_medicamentos['stock_total']); ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Valor Inventario</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_medicamentos['valor_inventario'], 2); ?></p>
                            </div>
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <i class="fas fa-warehouse text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos y datos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Gráfico de ventas -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Ventas de los Últimos 7 Días</h2>
                        <div class="chart-container">
                            <canvas id="chartVentas"></canvas>
                        </div>
                    </div>

                    <!-- Top medicamentos -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Medicamentos Vendidos Hoy</h2>
                        <?php if (empty($top_medicamentos_hoy)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-pills text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">No hay ventas registradas hoy</p>
                                <p class="text-gray-400 text-sm">Los medicamentos más vendidos aparecerán aquí</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($top_medicamentos_hoy as $medicamento): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($medicamento['nombre']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $medicamento['cantidad_vendida']; ?> unidades</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-green-600">$<?php echo number_format($medicamento['total_vendido'], 2); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alertas y ventas recientes -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Alertas de stock bajo -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">⚠️ Alertas de Stock Bajo</h2>
                        <?php if (empty($alertas_stock)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-6xl text-green-300 mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">¡Todo en orden!</p>
                                <p class="text-gray-400 text-sm">No hay alertas de stock bajo</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($alertas_stock as $alerta): ?>
                                <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-red-900"><?php echo htmlspecialchars($alerta['medicamento_nombre']); ?></p>
                                        <p class="text-sm text-red-600"><?php echo htmlspecialchars($alerta['farmacia_nombre']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-red-600"><?php echo $alerta['stock']; ?>/<?php echo $alerta['stock_minimo']; ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Ventas recientes -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">💰 Ventas Recientes</h2>
                        <?php if (empty($ventas_recientes)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">No hay ventas registradas</p>
                                <p class="text-gray-400 text-sm">Las ventas aparecerán aquí una vez que se registren</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($ventas_recientes as $venta): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">Venta #<?php echo $venta['id']; ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($venta['farmacia_nombre']); ?> - <?php echo htmlspecialchars($venta['usuario_nombre']); ?></p>
                                        <p class="text-xs text-gray-400"><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-green-600">$<?php echo number_format($venta['total'], 2); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $venta['productos_vendidos']; ?> productos</p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Gráfico de ventas de los últimos 7 días
        const ctxVentas = document.getElementById('chartVentas').getContext('2d');
        
        const datosVentas = {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas Diarias',
                data: [<?php echo $estadisticas_ventas['facturado_hoy']; ?>, 0, 0, 0, 0, 0, 0],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        };
        
        new Chart(ctxVentas, {
            type: 'line',
            data: datosVentas,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(59, 130, 246, 0.5)',
                        borderWidth: 1
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 20,
                        right: 20
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    },
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });
        
        // Auto-refresh cada 5 minutos
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
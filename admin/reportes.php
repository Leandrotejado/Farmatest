<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Obtener parámetros de filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$farmacia_filtro = $_GET['farmacia'] ?? '';

try {
    // Obtener farmacias para filtro
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
    
    // Reporte de ventas por período
    $sql_ventas = "
        SELECT 
            DATE(v.fecha) as fecha,
            COUNT(*) as total_ventas,
            SUM(v.total) as total_facturado,
            AVG(v.total) as promedio_venta
        FROM ventas v
        WHERE v.estado = 'completada' 
        AND DATE(v.fecha) BETWEEN ? AND ?
    ";
    $params_ventas = [$fecha_inicio, $fecha_fin];
    
    if ($farmacia_filtro) {
        $sql_ventas .= " AND v.id_farmacia = ?";
        $params_ventas[] = $farmacia_filtro;
    }
    
    $sql_ventas .= " GROUP BY DATE(v.fecha) ORDER BY fecha DESC";
    
    $stmt = $pdo->prepare($sql_ventas);
    $stmt->execute($params_ventas);
    $reporte_ventas = $stmt->fetchAll();
    
    // Reporte de medicamentos más vendidos
    $sql_medicamentos = "
        SELECT 
            m.nombre,
            SUM(vd.cantidad) as cantidad_vendida,
            SUM(vd.subtotal) as total_vendido,
            COUNT(DISTINCT v.id) as veces_vendido
        FROM venta_detalles vd
        JOIN ventas v ON vd.id_venta = v.id
        JOIN medicamentos m ON vd.id_medicamento = m.id
        WHERE v.estado = 'completada'
        AND DATE(v.fecha) BETWEEN ? AND ?
    ";
    $params_medicamentos = [$fecha_inicio, $fecha_fin];
    
    if ($farmacia_filtro) {
        $sql_medicamentos .= " AND v.id_farmacia = ?";
        $params_medicamentos[] = $farmacia_filtro;
    }
    
    $sql_medicamentos .= " GROUP BY m.id, m.nombre ORDER BY cantidad_vendida DESC LIMIT 10";
    
    $stmt = $pdo->prepare($sql_medicamentos);
    $stmt->execute($params_medicamentos);
    $medicamentos_vendidos = $stmt->fetchAll();
    
    // Reporte de ventas por farmacia
    $sql_farmacias = "
        SELECT 
            f.nombre as farmacia_nombre,
            COUNT(v.id) as total_ventas,
            SUM(v.total) as total_facturado,
            AVG(v.total) as promedio_venta
        FROM ventas v
        JOIN farmacias f ON v.id_farmacia = f.id
        WHERE v.estado = 'completada'
        AND DATE(v.fecha) BETWEEN ? AND ?
    ";
    $params_farmacias = [$fecha_inicio, $fecha_fin];
    
    if ($farmacia_filtro) {
        $sql_farmacias .= " AND v.id_farmacia = ?";
        $params_farmacias[] = $farmacia_filtro;
    }
    
    $sql_farmacias .= " GROUP BY f.id, f.nombre ORDER BY total_facturado DESC";
    
    $stmt = $pdo->prepare($sql_farmacias);
    $stmt->execute($params_farmacias);
    $ventas_por_farmacia = $stmt->fetchAll();
    
    // Reporte de stock bajo
    $stmt = $pdo->query("
        SELECT 
            f.nombre as farmacia_nombre,
            m.nombre as medicamento_nombre,
            sf.stock,
            sf.stock_minimo,
            sf.stock - sf.stock_minimo as diferencia
        FROM stock_farmacias sf
        JOIN farmacias f ON sf.id_farmacia = f.id
        JOIN medicamentos m ON sf.id_medicamento = m.id
        WHERE sf.stock <= sf.stock_minimo
        ORDER BY diferencia ASC
    ");
    $stock_bajo = $stmt->fetchAll();
    
    // Estadísticas generales
    $ventas_table_exists = $pdo->query("SHOW TABLES LIKE 'ventas'")->rowCount() > 0;
    if ($ventas_table_exists) {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_ventas_periodo,
                COALESCE(SUM(total), 0) as total_facturado_periodo,
                COALESCE(AVG(total), 0) as promedio_venta_periodo,
                COUNT(DISTINCT id_farmacia) as farmacias_activas
            FROM ventas 
            WHERE estado = 'completada' 
            AND DATE(fecha) BETWEEN ? AND ?
        ");
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $estadisticas_generales = $stmt->fetch();
    } else {
        $estadisticas_generales = ['total_ventas_periodo' => 0, 'total_facturado_periodo' => 0, 'promedio_venta_periodo' => 0, 'farmacias_activas' => 0];
    }
    
} catch (Exception $e) {
    $reporte_ventas = [];
    $medicamentos_vendidos = [];
    $ventas_por_farmacia = [];
    $stock_bajo = [];
    $estadisticas_generales = ['total_ventas_periodo' => 0, 'total_facturado_periodo' => 0, 'promedio_venta_periodo' => 0, 'farmacias_activas' => 0];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Farmacias</title>
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
                    <li><a href="dashboard.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
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
                    <li><a href="reportes.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">📊 Reportes y Analytics</h1>
                    <p class="text-gray-600">Análisis detallado del rendimiento del sistema</p>
                </div>

                <!-- Filtros -->
                <div class="bg-white p-6 rounded-lg shadow mb-8">
                    <h2 class="text-lg font-semibold mb-4">🔍 Filtros de Reporte</h2>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" 
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                            <input type="date" name="fecha_fin" value="<?php echo $fecha_fin; ?>" 
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia</label>
                            <select name="farmacia" class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las farmacias</option>
                                <?php foreach ($farmacias as $farmacia): ?>
                                    <option value="<?php echo $farmacia['id']; ?>" 
                                            <?php echo $farmacia_filtro == $farmacia['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($farmacia['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                                <i class="fas fa-search"></i> Generar Reporte
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas del período -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Ventas del Período</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_generales['total_ventas_periodo']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Facturado del Período</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_generales['total_facturado_periodo'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-calculator text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Promedio por Venta</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_generales['promedio_venta_periodo'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-hospital text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Farmacias Activas</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_generales['farmacias_activas']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Gráfico de ventas diarias -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📈 Ventas Diarias</h2>
                        <div class="chart-container">
                            <canvas id="chartVentasDiarias"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de ventas por farmacia -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">🏥 Ventas por Farmacia</h2>
                        <div class="chart-container">
                            <canvas id="chartVentasFarmacias"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Reportes detallados -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Top medicamentos vendidos -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">💊 Top Medicamentos Vendidos</h2>
                        <div class="space-y-3">
                            <?php foreach ($medicamentos_vendidos as $medicamento): ?>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($medicamento['nombre']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $medicamento['cantidad_vendida']; ?> unidades vendidas</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">$<?php echo number_format($medicamento['total_vendido'], 2); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $medicamento['veces_vendido']; ?> ventas</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ventas por farmacia -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">🏥 Rendimiento por Farmacia</h2>
                        <div class="space-y-3">
                            <?php foreach ($ventas_por_farmacia as $farmacia): ?>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($farmacia['farmacia_nombre']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $farmacia['total_ventas']; ?> ventas</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-blue-600">$<?php echo number_format($farmacia['total_facturado'], 2); ?></p>
                                    <p class="text-sm text-gray-500">Prom: $<?php echo number_format($farmacia['promedio_venta'], 2); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Reporte de stock bajo -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">⚠️ Alertas de Stock Bajo</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmacia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diferencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($stock_bajo as $stock): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($stock['farmacia_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($stock['medicamento_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stock['stock']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stock['stock_minimo']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <?php echo $stock['diferencia']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Gráfico de ventas diarias
        const ctxVentasDiarias = document.getElementById('chartVentasDiarias').getContext('2d');
        const datosVentasDiarias = {
            labels: [<?php echo "'" . implode("','", array_column($reporte_ventas, 'fecha')) . "'"; ?>],
            datasets: [{
                label: 'Ventas Diarias',
                data: [<?php echo implode(',', array_column($reporte_ventas, 'total_facturado')); ?>],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        };
        
        new Chart(ctxVentasDiarias, {
            type: 'line',
            data: datosVentasDiarias,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Gráfico de ventas por farmacia
        const ctxVentasFarmacias = document.getElementById('chartVentasFarmacias').getContext('2d');
        const datosVentasFarmacias = {
            labels: [<?php echo "'" . implode("','", array_column($ventas_por_farmacia, 'farmacia_nombre')) . "'"; ?>],
            datasets: [{
                label: 'Ventas por Farmacia',
                data: [<?php echo implode(',', array_column($ventas_por_farmacia, 'total_facturado')); ?>],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)'
                ],
                borderWidth: 1
            }]
        };
        
        new Chart(ctxVentasFarmacias, {
            type: 'bar',
            data: datosVentasFarmacias,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$farmacia_seleccionada = $_GET['farmacia'] ?? null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'ajustar_stock':
                    // Verificar si ya existe stock para este medicamento en esta farmacia
                    $stmt = $pdo->prepare("SELECT id FROM stock_farmacias WHERE id_farmacia = ? AND id_medicamento = ?");
                    $stmt->execute([$_POST['id_farmacia'], $_POST['id_medicamento']]);
                    $stock_existente = $stmt->fetch();
                    
                    if ($stock_existente) {
                        // Actualizar stock existente
                        $stmt = $pdo->prepare("
                            UPDATE stock_farmacias SET 
                                stock = stock + ?, 
                                stock_minimo = ?,
                                fecha_actualizacion = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$_POST['cantidad'], $_POST['stock_minimo'], $stock_existente['id']]);
                    } else {
                        // Crear nuevo registro de stock
                        $stmt = $pdo->prepare("
                            INSERT INTO stock_farmacias (id_farmacia, id_medicamento, stock, stock_minimo, fecha_actualizacion)
                            VALUES (?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$_POST['id_farmacia'], $_POST['id_medicamento'], $_POST['cantidad'], $_POST['stock_minimo']]);
                    }
                    
                    // Registrar movimiento
                    $stmt = $pdo->prepare("
                        INSERT INTO movimientos_stock (id_farmacia, id_medicamento, tipo_movimiento, cantidad, motivo, fecha, id_usuario)
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)
                    ");
                    $tipo_movimiento = $_POST['cantidad'] > 0 ? 'entrada' : 'salida';
                    $stmt->execute([
                        $_POST['id_farmacia'], 
                        $_POST['id_medicamento'], 
                        $tipo_movimiento, 
                        abs($_POST['cantidad']), 
                        $_POST['motivo'], 
                        $_SESSION['user_id']
                    ]);
                    
                    $mensaje = 'Stock ajustado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'transferir_stock':
                    // Reducir stock del origen
                    $stmt = $pdo->prepare("
                        UPDATE stock_farmacias SET stock = stock - ? WHERE id_farmacia = ? AND id_medicamento = ?
                    ");
                    $stmt->execute([$_POST['cantidad'], $_POST['id_farmacia_origen'], $_POST['id_medicamento']]);
                    
                    // Aumentar stock del destino
                    $stmt = $pdo->prepare("
                        INSERT INTO stock_farmacias (id_farmacia, id_medicamento, stock, stock_minimo, fecha_actualizacion)
                        VALUES (?, ?, ?, 0, NOW())
                        ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)
                    ");
                    $stmt->execute([$_POST['id_farmacia_destino'], $_POST['id_medicamento'], $_POST['cantidad']]);
                    
                    // Registrar transferencia
                    $stmt = $pdo->prepare("
                        INSERT INTO transferencias_farmacias (id_farmacia_origen, id_farmacia_destino, id_medicamento, cantidad, motivo, fecha, id_usuario)
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)
                    ");
                    $stmt->execute([
                        $_POST['id_farmacia_origen'],
                        $_POST['id_farmacia_destino'],
                        $_POST['id_medicamento'],
                        $_POST['cantidad'],
                        $_POST['motivo'],
                        $_SESSION['user_id']
                    ]);
                    
                    $mensaje = 'Transferencia realizada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener farmacias
try {
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
    
    // Obtener medicamentos
    $stmt = $pdo->query("SELECT * FROM medicamentos WHERE activo = 1 ORDER BY nombre");
    $medicamentos = $stmt->fetchAll();
    
    // Obtener stock por farmacia si hay una seleccionada
    $stock_farmacia = [];
    $estadisticas_farmacia = [];
    if ($farmacia_seleccionada) {
        $stmt = $pdo->prepare("
            SELECT 
                sf.*,
                m.nombre as medicamento_nombre,
                m.precio,
                m.codigo_barras,
                c.nombre as categoria_nombre,
                CASE 
                    WHEN sf.stock <= sf.stock_minimo THEN 'bajo'
                    WHEN sf.stock <= sf.stock_minimo * 2 THEN 'medio'
                    ELSE 'normal'
                END as estado_stock
            FROM stock_farmacias sf
            JOIN medicamentos m ON sf.id_medicamento = m.id
            LEFT JOIN categorias_medicamentos c ON m.id_categoria = c.id
            WHERE sf.id_farmacia = ?
            ORDER BY m.nombre
        ");
        $stmt->execute([$farmacia_seleccionada]);
        $stock_farmacia = $stmt->fetchAll();
        
        // Estadísticas de la farmacia seleccionada
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_medicamentos,
                SUM(sf.stock) as stock_total,
                SUM(sf.stock * m.precio) as valor_stock,
                COUNT(CASE WHEN sf.stock <= sf.stock_minimo THEN 1 END) as stock_bajo,
                f.nombre as farmacia_nombre
            FROM stock_farmacias sf
            JOIN medicamentos m ON sf.id_medicamento = m.id
            JOIN farmacias f ON sf.id_farmacia = f.id
            WHERE sf.id_farmacia = ?
        ");
        $stmt->execute([$farmacia_seleccionada]);
        $estadisticas_farmacia = $stmt->fetch();
    }
    
    // Estadísticas generales - verificar si las tablas existen
    $estadisticas_generales = ['farmacias_con_stock' => 0, 'total_registros_stock' => 0, 'stock_total_general' => 0, 'valor_total_general' => 0];
    
    $stock_table_exists = $pdo->query("SHOW TABLES LIKE 'stock_farmacias'")->rowCount() > 0;
    $medicamentos_table_exists = $pdo->query("SHOW TABLES LIKE 'medicamentos'")->rowCount() > 0;
    
    if ($stock_table_exists && $medicamentos_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(DISTINCT sf.id_farmacia) as farmacias_con_stock,
                COUNT(sf.id) as total_registros_stock,
                COALESCE(SUM(sf.stock), 0) as stock_total_general,
                COALESCE(SUM(sf.stock * m.precio), 0) as valor_total_general
            FROM stock_farmacias sf
            JOIN medicamentos m ON sf.id_medicamento = m.id
        ");
        $resultado = $stmt->fetch();
        if ($resultado) {
            $estadisticas_generales = $resultado;
        }
    }
    
} catch (Exception $e) {
    $farmacias = [];
    $medicamentos = [];
    $stock_farmacia = [];
    $estadisticas_farmacia = [];
    $estadisticas_generales = [];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock por Farmacia - Sistema de Farmacias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <li><a href="stock.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">📦 Stock por Farmacia</h1>
                    <p class="text-gray-600">Control de inventario específico por cada farmacia</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas generales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-hospital text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Farmacias con Stock</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_generales['farmacias_con_stock']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Registros de Stock</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_generales['total_registros_stock']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-cubes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Stock Total</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($estadisticas_generales['stock_total_general']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Valor Total</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_generales['valor_total_general'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selector de farmacia -->
                <div class="bg-white p-6 rounded-lg shadow mb-8">
                    <h2 class="text-lg font-semibold mb-4">Seleccionar Farmacia</h2>
                    <form method="GET" class="flex gap-4">
                        <select name="farmacia" class="flex-1 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar farmacia para ver su stock</option>
                            <?php foreach ($farmacias as $farmacia): ?>
                                <option value="<?php echo $farmacia['id']; ?>" <?php echo $farmacia_seleccionada == $farmacia['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($farmacia['nombre']); ?> - <?php echo htmlspecialchars($farmacia['distrito']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-search"></i> Ver Stock
                        </button>
                    </form>
                </div>

                <?php if ($farmacia_seleccionada && $estadisticas_farmacia): ?>
                <!-- Estadísticas de la farmacia seleccionada -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Medicamentos</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacia['total_medicamentos']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Stock Total</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($estadisticas_farmacia['stock_total']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Valor Stock</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_farmacia['valor_stock'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Stock Bajo</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacia['stock_bajo']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <button onclick="mostrarModalAjuste()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Ajustar Stock
                    </button>
                    <button onclick="mostrarModalTransferencia()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-exchange-alt"></i> Transferir Stock
                    </button>
                </div>

                <!-- Tabla de stock de la farmacia -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">
                            Stock de <?php echo htmlspecialchars($estadisticas_farmacia['farmacia_nombre']); ?>
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actualización</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($stock_farmacia as $stock): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($stock['medicamento_nombre']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($stock['codigo_barras']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($stock['categoria_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stock['stock']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $stock['stock_minimo']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($stock['estado_stock'] === 'bajo'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Bajo
                                            </span>
                                        <?php elseif ($stock['estado_stock'] === 'medio'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>Medio
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Normal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($stock['stock'] * $stock['precio'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y H:i', strtotime($stock['fecha_actualizacion'])); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal para ajustar stock -->
    <div id="modal-ajuste" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Ajustar Stock</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="ajustar_stock">
                    <input type="hidden" name="id_farmacia" value="<?php echo $farmacia_seleccionada; ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento</label>
                        <select name="id_medicamento" required 
                                class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar medicamento</option>
                            <?php foreach ($medicamentos as $medicamento): ?>
                                <option value="<?php echo $medicamento['id']; ?>">
                                    <?php echo htmlspecialchars($medicamento['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                        <input type="number" name="cantidad" required 
                               placeholder="Positivo para agregar, negativo para quitar"
                               class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" required min="0"
                               class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <select name="motivo" required class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar motivo</option>
                            <option value="Compra">Compra</option>
                            <option value="Venta">Venta</option>
                            <option value="Ajuste de inventario">Ajuste de inventario</option>
                            <option value="Pérdida">Pérdida</option>
                            <option value="Devolución">Devolución</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-save"></i> Ajustar
                        </button>
                        <button type="button" onclick="cerrarModalAjuste()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para transferir stock -->
    <div id="modal-transferencia" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Transferir Stock</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="transferir_stock">
                    <input type="hidden" name="id_farmacia_origen" value="<?php echo $farmacia_seleccionada; ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia Destino</label>
                        <select name="id_farmacia_destino" required 
                                class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar farmacia destino</option>
                            <?php foreach ($farmacias as $farmacia): ?>
                                <?php if ($farmacia['id'] != $farmacia_seleccionada): ?>
                                <option value="<?php echo $farmacia['id']; ?>">
                                    <?php echo htmlspecialchars($farmacia['nombre']); ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento</label>
                        <select name="id_medicamento" required 
                                class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar medicamento</option>
                            <?php foreach ($medicamentos as $medicamento): ?>
                                <option value="<?php echo $medicamento['id']; ?>">
                                    <?php echo htmlspecialchars($medicamento['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad a Transferir</label>
                        <input type="number" name="cantidad" required min="1"
                               class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <input type="text" name="motivo" required 
                               placeholder="Ej: Transferencia entre sucursales"
                               class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-exchange-alt"></i> Transferir
                        </button>
                        <button type="button" onclick="cerrarModalTransferencia()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarModalAjuste() {
            document.getElementById('modal-ajuste').classList.remove('hidden');
        }
        
        function cerrarModalAjuste() {
            document.getElementById('modal-ajuste').classList.add('hidden');
        }
        
        function mostrarModalTransferencia() {
            document.getElementById('modal-transferencia').classList.remove('hidden');
        }
        
        function cerrarModalTransferencia() {
            document.getElementById('modal-transferencia').classList.add('hidden');
        }
    </script>
</body>
</html>

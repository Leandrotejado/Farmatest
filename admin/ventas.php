<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$venta_editar = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'crear_venta':
                    // Crear la venta
                    $stmt = $pdo->prepare("
                        INSERT INTO ventas (id_farmacia, id_usuario, total, estado, observaciones)
                        VALUES (?, ?, ?, 'completada', ?)
                    ");
                    $stmt->execute([
                        $_POST['id_farmacia'],
                        $_SESSION['user_id'],
                        $_POST['total'],
                        $_POST['observaciones']
                    ]);
                    $venta_id = $pdo->lastInsertId();
                    
                    // Crear los detalles de la venta
                    $medicamentos = json_decode($_POST['medicamentos'], true);
                    foreach ($medicamentos as $med) {
                        $stmt = $pdo->prepare("
                            INSERT INTO venta_detalles (id_venta, id_medicamento, cantidad, precio_unitario, subtotal)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $venta_id,
                            $med['id'],
                            $med['cantidad'],
                            $med['precio'],
                            $med['subtotal']
                        ]);
                        
                        // Actualizar stock en la farmacia
                        $stmt = $pdo->prepare("
                            UPDATE stock_farmacias SET stock = stock - ? 
                            WHERE id_farmacia = ? AND id_medicamento = ?
                        ");
                        $stmt->execute([$med['cantidad'], $_POST['id_farmacia'], $med['id']]);
                        
                        // Registrar movimiento
                        $stmt = $pdo->prepare("
                            INSERT INTO movimientos_stock (id_farmacia, id_medicamento, tipo_movimiento, cantidad, motivo, fecha, id_usuario)
                            VALUES (?, ?, 'salida', ?, 'Venta', NOW(), ?)
                        ");
                        $stmt->execute([$_POST['id_farmacia'], $med['id'], $med['cantidad'], $_SESSION['user_id']]);
                    }
                    
                    $mensaje = 'Venta registrada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'cancelar_venta':
                    $stmt = $pdo->prepare("UPDATE ventas SET estado = 'cancelada' WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $mensaje = 'Venta cancelada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener ventas
try {
    $stmt = $pdo->query("
        SELECT 
            v.*,
            f.nombre as farmacia_nombre,
            u.usuario_nombre,
            COUNT(vd.id) as productos_vendidos
        FROM ventas v
        JOIN farmacias f ON v.id_farmacia = f.id
        JOIN usuarios u ON v.id_usuario = u.id
        LEFT JOIN venta_detalles vd ON v.id = vd.id_venta
        GROUP BY v.id, v.total, v.estado, v.fecha, f.nombre, u.usuario_nombre, v.observaciones
        ORDER BY v.fecha DESC
        LIMIT 50
    ");
    $ventas = $stmt->fetchAll();
    
    // Obtener farmacias
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
    
    // Obtener medicamentos
    $stmt = $pdo->query("SELECT * FROM medicamentos WHERE activo = 1 ORDER BY nombre");
    $medicamentos = $stmt->fetchAll();
    
    // Estadísticas de ventas
    $ventas_table_exists = $pdo->query("SHOW TABLES LIKE 'ventas'")->rowCount() > 0;
    if ($ventas_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_ventas,
                COALESCE(SUM(total), 0) as total_facturado,
                COUNT(CASE WHEN estado = 'completada' THEN 1 END) as ventas_completadas,
                COUNT(CASE WHEN estado = 'cancelada' THEN 1 END) as ventas_canceladas,
                COALESCE(AVG(total), 0) as promedio_venta
            FROM ventas
        ");
        $estadisticas_ventas = $stmt->fetch();
        
        // Ventas de hoy
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as ventas_hoy,
                COALESCE(SUM(total), 0) as facturado_hoy
            FROM ventas 
            WHERE estado = 'completada' AND DATE(fecha) = CURDATE()
        ");
        $ventas_hoy = $stmt->fetch();
    } else {
        $estadisticas_ventas = ['total_ventas' => 0, 'total_facturado' => 0, 'ventas_completadas' => 0, 'ventas_canceladas' => 0, 'promedio_venta' => 0];
        $ventas_hoy = ['ventas_hoy' => 0, 'facturado_hoy' => 0];
    }
    
} catch (Exception $e) {
    $ventas = [];
    $farmacias = [];
    $medicamentos = [];
    $estadisticas_ventas = ['total_ventas' => 0, 'total_facturado' => 0, 'ventas_completadas' => 0, 'ventas_canceladas' => 0, 'promedio_venta' => 0];
    $ventas_hoy = ['ventas_hoy' => 0, 'facturado_hoy' => 0];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Sistema de Farmacias</title>
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
                    <li><a href="stock.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-boxes mr-3"></i>Stock por Farmacia
                    </a></li>
                    <li><a href="ventas.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">💰 Sistema de Ventas</h1>
                    <p class="text-gray-600">Punto de venta y gestión de transacciones</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas de ventas -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_ventas['total_ventas']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Facturado</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_ventas['total_facturado'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Ventas Hoy</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $ventas_hoy['ventas_hoy']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-calculator text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Facturado Hoy</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($ventas_hoy['facturado_hoy'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Promedio Venta</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_ventas['promedio_venta'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Punto de Venta -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Formulario de venta -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">🛒 Punto de Venta</h2>
                        
                        <form id="form-venta" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia</label>
                                <select id="farmacia-venta" required 
                                        class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccionar farmacia</option>
                                    <?php foreach ($farmacias as $farmacia): ?>
                                        <option value="<?php echo $farmacia['id']; ?>">
                                            <?php echo htmlspecialchars($farmacia['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento</label>
                                <select id="medicamento-venta" required 
                                        class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccionar medicamento</option>
                                    <?php foreach ($medicamentos as $medicamento): ?>
                                        <option value="<?php echo $medicamento['id']; ?>" 
                                                data-precio="<?php echo $medicamento['precio']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($medicamento['nombre']); ?>">
                                            <?php echo htmlspecialchars($medicamento['nombre']); ?> - $<?php echo number_format($medicamento['precio'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                    <input type="number" id="cantidad-venta" min="1" value="1" required 
                                           class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario</label>
                                    <input type="number" id="precio-venta" step="0.01" readonly 
                                           class="w-full p-2 border border-gray-300 rounded bg-gray-50">
                                </div>
                            </div>
                            
                            <button type="button" onclick="agregarAlCarrito()" 
                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                                <i class="fas fa-plus"></i> Agregar al Carrito
                            </button>
                        </form>
                    </div>

                    <!-- Carrito de compras -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">🛍️ Carrito de Compras</h2>
                        
                        <div id="carrito-items" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                            <p class="text-gray-500 text-center py-4">El carrito está vacío</p>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold">Total:</span>
                                <span id="total-carrito" class="text-xl font-bold text-green-600">$0.00</span>
                            </div>
                            
                            <div class="space-y-2">
                                <textarea id="observaciones-venta" rows="2" 
                                          placeholder="Observaciones de la venta..."
                                          class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"></textarea>
                                
                                <button onclick="procesarVenta()" 
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                                    <i class="fas fa-credit-card"></i> Procesar Venta
                                </button>
                                
                                <button onclick="limpiarCarrito()" 
                                        class="w-full bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                                    <i class="fas fa-trash"></i> Limpiar Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de ventas -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">📋 Historial de Ventas</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmacia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#<?php echo $venta['id']; ?></div>
                                        <?php if ($venta['observaciones']): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($venta['observaciones']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($venta['farmacia_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($venta['usuario_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($venta['total'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $venta['productos_vendidos']; ?> productos
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($venta['estado'] === 'completada'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Completada
                                            </span>
                                        <?php elseif ($venta['estado'] === 'cancelada'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>Cancelada
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>Pendiente
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="verDetalleVenta(<?php echo $venta['id']; ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 px-2 py-1 rounded">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            <?php if ($venta['estado'] === 'completada'): ?>
                                            <button onclick="cancelarVenta(<?php echo $venta['id']; ?>)" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 px-2 py-1 rounded">
                                                <i class="fas fa-ban"></i> Cancelar
                                            </button>
                                            <?php endif; ?>
                                        </div>
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
        let carrito = [];
        let totalCarrito = 0;

        // Actualizar precio cuando se selecciona medicamento
        document.getElementById('medicamento-venta').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const precio = selectedOption.getAttribute('data-precio');
            document.getElementById('precio-venta').value = precio || '';
        });

        function agregarAlCarrito() {
            const farmacia = document.getElementById('farmacia-venta').value;
            const medicamento = document.getElementById('medicamento-venta');
            const cantidad = parseInt(document.getElementById('cantidad-venta').value);
            const precio = parseFloat(document.getElementById('precio-venta').value);

            if (!farmacia || !medicamento.value || !cantidad || !precio) {
                alert('Por favor complete todos los campos');
                return;
            }

            const selectedOption = medicamento.options[medicamento.selectedIndex];
            const nombre = selectedOption.getAttribute('data-nombre');
            const subtotal = cantidad * precio;

            const item = {
                id: medicamento.value,
                nombre: nombre,
                cantidad: cantidad,
                precio: precio,
                subtotal: subtotal
            };

            carrito.push(item);
            totalCarrito += subtotal;

            actualizarCarrito();
            limpiarFormulario();
        }

        function actualizarCarrito() {
            const carritoItems = document.getElementById('carrito-items');
            const totalElement = document.getElementById('total-carrito');

            if (carrito.length === 0) {
                carritoItems.innerHTML = '<p class="text-gray-500 text-center py-4">El carrito está vacío</p>';
                totalElement.textContent = '$0.00';
                return;
            }

            let html = '';
            carrito.forEach((item, index) => {
                html += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium">${item.nombre}</p>
                            <p class="text-sm text-gray-500">${item.cantidad} x $${item.precio.toFixed(2)}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold">$${item.subtotal.toFixed(2)}</span>
                            <button onclick="eliminarDelCarrito(${index})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            carritoItems.innerHTML = html;
            totalElement.textContent = `$${totalCarrito.toFixed(2)}`;
        }

        function eliminarDelCarrito(index) {
            totalCarrito -= carrito[index].subtotal;
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        function limpiarCarrito() {
            carrito = [];
            totalCarrito = 0;
            actualizarCarrito();
        }

        function limpiarFormulario() {
            document.getElementById('medicamento-venta').value = '';
            document.getElementById('cantidad-venta').value = '1';
            document.getElementById('precio-venta').value = '';
        }

        function procesarVenta() {
            if (carrito.length === 0) {
                alert('El carrito está vacío');
                return;
            }

            const farmacia = document.getElementById('farmacia-venta').value;
            const observaciones = document.getElementById('observaciones-venta').value;

            if (!farmacia) {
                alert('Por favor seleccione una farmacia');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="accion" value="crear_venta">
                <input type="hidden" name="id_farmacia" value="${farmacia}">
                <input type="hidden" name="total" value="${totalCarrito}">
                <input type="hidden" name="medicamentos" value='${JSON.stringify(carrito)}'>
                <input type="hidden" name="observaciones" value="${observaciones}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function verDetalleVenta(id) {
            alert('Función de detalle en desarrollo');
        }

        function cancelarVenta(id) {
            if (confirm('¿Estás seguro de que quieres cancelar esta venta?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="cancelar_venta">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

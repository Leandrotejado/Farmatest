<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$medicamento_editar = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'crear_medicamento':
                    $stmt = $pdo->prepare("
                        INSERT INTO medicamentos (nombre, descripcion, codigo_barras, precio, id_categoria, id_proveedor, stock_general, stock_minimo, fecha_vencimiento, activo)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ");
                    $stmt->execute([
                        $_POST['nombre'],
                        $_POST['descripcion'],
                        $_POST['codigo_barras'],
                        $_POST['precio'],
                        $_POST['id_categoria'],
                        $_POST['id_proveedor'],
                        $_POST['stock_general'],
                        $_POST['stock_minimo'],
                        $_POST['fecha_vencimiento']
                    ]);
                    $mensaje = 'Medicamento creado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'actualizar_medicamento':
                    $stmt = $pdo->prepare("
                        UPDATE medicamentos SET 
                            nombre = ?, descripcion = ?, codigo_barras = ?, precio = ?, 
                            id_categoria = ?, id_proveedor = ?, stock_general = ?, stock_minimo = ?, 
                            fecha_vencimiento = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['nombre'],
                        $_POST['descripcion'],
                        $_POST['codigo_barras'],
                        $_POST['precio'],
                        $_POST['id_categoria'],
                        $_POST['id_proveedor'],
                        $_POST['stock_general'],
                        $_POST['stock_minimo'],
                        $_POST['fecha_vencimiento'],
                        $_POST['id']
                    ]);
                    $mensaje = 'Medicamento actualizado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'eliminar_medicamento':
                    $stmt = $pdo->prepare("UPDATE medicamentos SET activo = 0 WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $mensaje = 'Medicamento eliminado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener medicamentos
try {
    $stmt = $pdo->query("
        SELECT 
            m.*,
            c.nombre as categoria_nombre,
            c.tipo_venta,
            p.nombre as proveedor_nombre,
            CASE 
                WHEN m.stock_general <= m.stock_minimo THEN 'bajo'
                WHEN m.stock_general <= m.stock_minimo * 2 THEN 'medio'
                ELSE 'normal'
            END as estado_stock
        FROM medicamentos m
        LEFT JOIN categorias_medicamentos c ON m.id_categoria = c.id
        LEFT JOIN proveedores p ON m.id_proveedor = p.id
        WHERE m.activo = 1
        ORDER BY m.nombre
    ");
    $medicamentos = $stmt->fetchAll();
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT * FROM categorias_medicamentos WHERE activa = 1 ORDER BY nombre");
    $categorias = $stmt->fetchAll();
    
    // Obtener proveedores
    $stmt = $pdo->query("SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre");
    $proveedores = $stmt->fetchAll();
    
    // Estadísticas de medicamentos
    $medicamentos_table_exists = $pdo->query("SHOW TABLES LIKE 'medicamentos'")->rowCount() > 0;
    if ($medicamentos_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_medicamentos,
                COALESCE(SUM(stock_general), 0) as stock_total,
                COALESCE(SUM(stock_general * precio), 0) as valor_inventario,
                COUNT(CASE WHEN stock_general <= stock_minimo THEN 1 END) as stock_bajo,
                COUNT(CASE WHEN fecha_vencimiento <= DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 END) as proximos_vencer
            FROM medicamentos 
            WHERE activo = 1
        ");
        $estadisticas_medicamentos = $stmt->fetch();
    } else {
        $estadisticas_medicamentos = ['total_medicamentos' => 0, 'stock_total' => 0, 'valor_inventario' => 0, 'stock_bajo' => 0, 'proximos_vencer' => 0];
    }
    
} catch (Exception $e) {
    $medicamentos = [];
    $categorias = [];
    $proveedores = [];
    $estadisticas_medicamentos = ['total_medicamentos' => 0, 'stock_total' => 0, 'valor_inventario' => 0, 'stock_bajo' => 0, 'proximos_vencer' => 0];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}

// Obtener medicamento para editar
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $medicamento_editar = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Medicamentos - Sistema de Farmacias</title>
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
                    <li><a href="medicamentos.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">💊 Gestión de Medicamentos</h1>
                    <p class="text-gray-600">Administra el catálogo completo de medicamentos del sistema</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas de medicamentos -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Medicamentos</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_medicamentos['total_medicamentos']; ?></p>
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
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($estadisticas_medicamentos['stock_total']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Valor Inventario</p>
                                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($estadisticas_medicamentos['valor_inventario'], 2); ?></p>
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
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_medicamentos['stock_bajo']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Próximos a Vencer</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_medicamentos['proximos_vencer']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <button onclick="mostrarFormulario()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Nuevo Medicamento
                    </button>
                    <button onclick="exportarMedicamentos()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-file-excel"></i> Exportar Lista
                    </button>
                    <button onclick="verStockBajo()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        <i class="fas fa-exclamation-triangle"></i> Ver Stock Bajo
                    </button>
                </div>

                <!-- Formulario de medicamento -->
                <div id="formulario-medicamento" class="mb-8 bg-white p-6 rounded-lg shadow <?php echo $medicamento_editar ? '' : 'hidden'; ?>">
                    <h2 class="text-xl font-semibold mb-4">
                        <?php echo $medicamento_editar ? 'Editar Medicamento' : 'Nuevo Medicamento'; ?>
                    </h2>
                    
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="accion" value="<?php echo $medicamento_editar ? 'actualizar_medicamento' : 'crear_medicamento'; ?>">
                        <?php if ($medicamento_editar): ?>
                            <input type="hidden" name="id" value="<?php echo $medicamento_editar['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Medicamento</label>
                            <input type="text" name="nombre" required 
                                   value="<?php echo htmlspecialchars($medicamento_editar['nombre'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label>
                            <input type="text" name="codigo_barras" 
                                   value="<?php echo htmlspecialchars($medicamento_editar['codigo_barras'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea name="descripcion" rows="2" 
                                      class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($medicamento_editar['descripcion'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                            <input type="number" name="precio" step="0.01" required 
                                   value="<?php echo $medicamento_editar['precio'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select name="id_categoria" required 
                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" 
                                            <?php echo ($medicamento_editar['id_categoria'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nombre']); ?> (<?php echo $categoria['tipo_venta']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                            <select name="id_proveedor" required 
                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar proveedor</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?php echo $proveedor['id']; ?>" 
                                            <?php echo ($medicamento_editar['id_proveedor'] ?? '') == $proveedor['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock General</label>
                            <input type="number" name="stock_general" min="0" required 
                                   value="<?php echo $medicamento_editar['stock_general'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" min="0" required 
                                   value="<?php echo $medicamento_editar['stock_minimo'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" 
                                   value="<?php echo $medicamento_editar['fecha_vencimiento'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class="fas fa-save"></i> <?php echo $medicamento_editar ? 'Actualizar' : 'Crear'; ?>
                            </button>
                            <button type="button" onclick="ocultarFormulario()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de medicamentos -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Catálogo de Medicamentos</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($medicamentos as $medicamento): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($medicamento['nombre']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($medicamento['codigo_barras']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div><?php echo htmlspecialchars($medicamento['categoria_nombre']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo $medicamento['tipo_venta']; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($medicamento['proveedor_nombre']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($medicamento['precio'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div><?php echo $medicamento['stock_general']; ?></div>
                                            <div class="text-xs text-gray-500">Mín: <?php echo $medicamento['stock_minimo']; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($medicamento['estado_stock'] === 'bajo'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Bajo
                                            </span>
                                        <?php elseif ($medicamento['estado_stock'] === 'medio'): ?>
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
                                        <?php if ($medicamento['fecha_vencimiento']): ?>
                                            <?php 
                                            $fecha_vencimiento = new DateTime($medicamento['fecha_vencimiento']);
                                            $hoy = new DateTime();
                                            $diferencia = $hoy->diff($fecha_vencimiento);
                                            $dias_restantes = $diferencia->days;
                                            
                                            if ($fecha_vencimiento < $hoy) {
                                                echo '<span class="text-red-600 font-semibold">Vencido</span>';
                                            } elseif ($dias_restantes <= 30) {
                                                echo '<span class="text-yellow-600 font-semibold">' . $dias_restantes . ' días</span>';
                                            } else {
                                                echo '<span class="text-green-600">' . $dias_restantes . ' días</span>';
                                            }
                                            ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="?editar=<?php echo $medicamento['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 px-2 py-1 rounded">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button onclick="eliminarMedicamento(<?php echo $medicamento['id']; ?>, '<?php echo htmlspecialchars($medicamento['nombre']); ?>')" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 px-2 py-1 rounded">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
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
        function mostrarFormulario() {
            document.getElementById('formulario-medicamento').classList.remove('hidden');
        }
        
        function ocultarFormulario() {
            document.getElementById('formulario-medicamento').classList.add('hidden');
            window.location.href = 'medicamentos.php';
        }
        
        function eliminarMedicamento(id, nombre) {
            if (confirm(`¿Estás seguro de que quieres eliminar el medicamento "${nombre}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="eliminar_medicamento">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function exportarMedicamentos() {
            alert('Función de exportación en desarrollo');
        }
        
        function verStockBajo() {
            alert('Función de stock bajo en desarrollo');
        }
    </script>
</body>
</html>

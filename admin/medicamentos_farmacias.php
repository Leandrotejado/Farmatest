<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Procesar formularios
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'asignar') {
        $id_medicamento = $_POST['id_medicamento'];
        $id_farmacia = $_POST['id_farmacia'];
        $stock = $_POST['stock'];
        $precio_especial = $_POST['precio_especial'];
        $descuento_obra_social = $_POST['descuento_obra_social'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO medicamentos_farmacias (id_medicamento, id_farmacia, stock_disponible, precio_especial, descuento_obra_social) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                stock_disponible = VALUES(stock_disponible),
                precio_especial = VALUES(precio_especial),
                descuento_obra_social = VALUES(descuento_obra_social)
            ");
            $stmt->execute([$id_medicamento, $id_farmacia, $stock, $precio_especial, $descuento_obra_social]);
            $success = 'Medicamento asignado correctamente a la farmacia';
        } catch (Exception $e) {
            $error = 'Error al asignar medicamento: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'crear_medicamento') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $stock = (int)$_POST['stock'];
        $precio = (float)$_POST['precio'];
        $id_categoria = (int)$_POST['id_categoria'];
        $id_proveedor = (int)$_POST['id_proveedor'];
        $fecha_vencimiento = $_POST['fecha_vencimiento'];
        
        if (empty($nombre) || $stock < 0 || $precio <= 0) {
            $error = 'Por favor, completa todos los campos correctamente';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO medicamentos (nombre, descripcion, stock, precio, id_categoria, id_proveedor, fecha_vencimiento) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$nombre, $descripcion, $stock, $precio, $id_categoria, $id_proveedor, $fecha_vencimiento]);
                $success = 'Medicamento creado correctamente. Ahora puedes asignarlo a una farmacia.';
            } catch (Exception $e) {
                $error = 'Error al crear medicamento: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'actualizar') {
        $id = $_POST['id'];
        $stock = $_POST['stock'];
        $precio_especial = $_POST['precio_especial'];
        $descuento_obra_social = $_POST['descuento_obra_social'];
        
        try {
            $stmt = $pdo->prepare("
                UPDATE medicamentos_farmacias 
                SET stock_disponible = ?, precio_especial = ?, descuento_obra_social = ?
                WHERE id = ?
            ");
            $stmt->execute([$stock, $precio_especial, $descuento_obra_social, $id]);
            $success = 'Medicamento actualizado correctamente';
        } catch (Exception $e) {
            $error = 'Error al actualizar medicamento: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'eliminar') {
        $id = $_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM medicamentos_farmacias WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Asignación eliminada correctamente';
        } catch (Exception $e) {
            $error = 'Error al eliminar asignación: ' . $e->getMessage();
        }
    }
}

// Obtener medicamentos con información de categoría y proveedor
try {
    $stmt = $pdo->query("
        SELECT m.*, c.nombre as categoria_nombre, p.nombre as proveedor_nombre
        FROM medicamentos m
        LEFT JOIN categorias c ON m.id_categoria = c.id
        LEFT JOIN proveedores p ON m.id_proveedor = p.id
        ORDER BY m.nombre
    ");
    $medicamentos = $stmt->fetchAll();
} catch (Exception $e) {
    $medicamentos = [];
}

// Obtener categorías
try {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
    $categorias = $stmt->fetchAll();
} catch (Exception $e) {
    $categorias = [];
}

// Obtener proveedores
try {
    $stmt = $pdo->query("SELECT * FROM proveedores ORDER BY nombre");
    $proveedores = $stmt->fetchAll();
} catch (Exception $e) {
    $proveedores = [];
}

// Obtener farmacias con manejo de errores
try {
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
} catch (Exception $e) {
    $farmacias = [];
}

// Obtener asignaciones existentes con información completa
try {
    $stmt = $pdo->query("
        SELECT mf.*, m.nombre as medicamento_nombre, m.precio as precio_base, 
               c.nombre as categoria_nombre, p.nombre as proveedor_nombre,
               f.nombre as farmacia_nombre, f.direccion
        FROM medicamentos_farmacias mf
        JOIN medicamentos m ON mf.id_medicamento = m.id
        LEFT JOIN categorias c ON m.id_categoria = c.id
        LEFT JOIN proveedores p ON m.id_proveedor = p.id
        JOIN farmacias f ON mf.id_farmacia = f.id
        WHERE mf.activo = 1
        ORDER BY f.nombre, m.nombre
    ");
    $asignaciones = $stmt->fetchAll();
} catch (Exception $e) {
    $asignaciones = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos por Farmacia - Panel Administrativo</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark Mode Script -->
    <script src="../public/assets/dark-mode.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <aside class="bg-blue-600 text-white w-64 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold"><?php echo $_SESSION['usuario_nombre']; ?></h3>
                <p><?php echo $_SESSION['rol'] == 'admin' ? 'Administrador' : 'Usuario'; ?></p>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="block p-2 hover:bg-blue-700 rounded">Dashboard</a></li>
                    <li><a href="stock.php" class="block p-2 hover:bg-blue-700 rounded">Inventario</a></li>
                    <li><a href="medicamentos_farmacias.php" class="block p-2 bg-blue-700 rounded">Medicamentos por Farmacia</a></li>
                    <li><a href="ventas.php" class="block p-2 hover:bg-blue-700 rounded">Ventas</a></li>
                    <li><a href="empleados.php" class="block p-2 hover:bg-blue-700 rounded">Empleados</a></li>
                    <li><a href="logout.php" class="block p-2 hover:bg-red-600 rounded">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Gestión de Medicamentos por Farmacia</h1>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario para crear nuevo medicamento -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">💊 Crear Nuevo Medicamento</h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <input type="hidden" name="action" value="crear_medicamento">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Medicamento:</label>
                            <input type="text" name="nombre" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: Paracetamol 500mg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría:</label>
                            <select name="id_categoria" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor:</label>
                            <select name="id_proveedor" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar proveedor</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?php echo $prov['id']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Base:</label>
                            <input type="number" name="precio" step="0.01" min="0" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Inicial:</label>
                            <input type="number" name="stock" min="0" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento:</label>
                            <input type="date" name="fecha_vencimiento" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                            <input type="text" name="descripcion" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Descripción del medicamento">
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-4">
                            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                ➕ Crear Medicamento
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Formulario para asignar medicamento a farmacia -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">🏥 Asignar Medicamento a Farmacia</h2>
                    
                    <!-- Filtro por categoría -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por categoría:</label>
                        <select id="filtroCategoria" class="w-full md:w-1/3 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="filtrarMedicamentos()">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <input type="hidden" name="action" value="asignar">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento:</label>
                            <select name="id_medicamento" id="selectMedicamento" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar medicamento</option>
                                <?php foreach ($medicamentos as $med): ?>
                                    <option value="<?php echo $med['id']; ?>" data-categoria="<?php echo $med['id_categoria']; ?>" data-precio="<?php echo $med['precio']; ?>">
                                        <?php echo htmlspecialchars($med['nombre']); ?>
                                        <?php if ($med['categoria_nombre']): ?>
                                            (<?php echo htmlspecialchars($med['categoria_nombre']); ?>)
                                        <?php endif; ?>
                                        - $<?php echo number_format($med['precio'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia:</label>
                            <select name="id_farmacia" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar farmacia</option>
                                <?php foreach ($farmacias as $farm): ?>
                                    <option value="<?php echo $farm['id']; ?>"><?php echo htmlspecialchars($farm['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock en Farmacia:</label>
                            <input type="number" name="stock" min="0" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Especial:</label>
                            <input type="number" name="precio_especial" id="precioEspecial" step="0.01" min="0" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            <small class="text-gray-500">Dejar vacío para usar precio base</small>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descuento Obra Social (%):</label>
                            <input type="number" name="descuento_obra_social" step="0.01" min="0" max="100" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-5">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                🏥 Asignar a Farmacia
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de asignaciones existentes -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Medicamentos Asignados por Farmacia</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmacia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Base</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Especial</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento OS (%)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($asignaciones as $asig): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asig['farmacia_nombre']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($asig['direccion']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asig['medicamento_nombre']); ?></div>
                                            <?php if ($asig['proveedor_nombre']): ?>
                                                <div class="text-sm text-gray-500">Proveedor: <?php echo htmlspecialchars($asig['proveedor_nombre']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($asig['categoria_nombre']): ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($asig['categoria_nombre']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">Sin categoría</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $asig['stock_disponible'] > 10 ? 'bg-green-100 text-green-800' : ($asig['stock_disponible'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo $asig['stock_disponible']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($asig['precio_base'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($asig['precio_especial'] && $asig['precio_especial'] != $asig['precio_base']): ?>
                                            <span class="text-green-600 font-semibold">$<?php echo number_format($asig['precio_especial'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-500">Precio base</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($asig['descuento_obra_social'] > 0): ?>
                                            <span class="text-green-600 font-semibold"><?php echo number_format($asig['descuento_obra_social'], 1); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-gray-500">0%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editarAsignacion(<?php echo $asig['id']; ?>, <?php echo $asig['stock_disponible']; ?>, <?php echo $asig['precio_especial']; ?>, <?php echo $asig['descuento_obra_social']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                                        <button onclick="eliminarAsignacion(<?php echo $asig['id']; ?>)" 
                                                class="text-red-600 hover:text-red-900">Eliminar</button>
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

    <!-- Modal para editar asignación -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Editar Asignación</h3>
                </div>
                <form id="editForm" method="POST" class="p-6">
                    <input type="hidden" name="action" value="actualizar">
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock:</label>
                            <input type="number" name="stock" id="editStock" min="0" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Especial:</label>
                            <input type="number" name="precio_especial" id="editPrecio" step="0.01" min="0" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descuento Obra Social (%):</label>
                            <input type="number" name="descuento_obra_social" id="editDescuento" step="0.01" min="0" max="100" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="cerrarModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editarAsignacion(id, stock, precio, descuento) {
            document.getElementById('editId').value = id;
            document.getElementById('editStock').value = stock;
            document.getElementById('editPrecio').value = precio;
            document.getElementById('editDescuento').value = descuento;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function eliminarAsignacion(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta asignación?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Filtrar medicamentos por categoría
        function filtrarMedicamentos() {
            const filtroCategoria = document.getElementById('filtroCategoria').value;
            const selectMedicamento = document.getElementById('selectMedicamento');
            const opciones = selectMedicamento.querySelectorAll('option');
            
            // Mostrar/ocultar opciones según la categoría seleccionada
            opciones.forEach(opcion => {
                if (opcion.value === '') {
                    opcion.style.display = 'block'; // Siempre mostrar la opción vacía
                } else {
                    const categoriaMedicamento = opcion.getAttribute('data-categoria');
                    if (filtroCategoria === '' || categoriaMedicamento === filtroCategoria) {
                        opcion.style.display = 'block';
                    } else {
                        opcion.style.display = 'none';
                    }
                }
            });
            
            // Resetear la selección si el medicamento actual no coincide con el filtro
            if (filtroCategoria !== '' && selectMedicamento.value !== '') {
                const opcionSeleccionada = selectMedicamento.querySelector(`option[value="${selectMedicamento.value}"]`);
                if (opcionSeleccionada && opcionSeleccionada.getAttribute('data-categoria') !== filtroCategoria) {
                    selectMedicamento.value = '';
                    document.getElementById('precioEspecial').value = '';
                }
            }
        }

        // Auto-completar precio especial con el precio base del medicamento
        document.getElementById('selectMedicamento').addEventListener('change', function() {
            const opcionSeleccionada = this.options[this.selectedIndex];
            const precioBase = opcionSeleccionada.getAttribute('data-precio');
            const precioEspecial = document.getElementById('precioEspecial');
            
            if (precioBase && !precioEspecial.value) {
                precioEspecial.value = precioBase;
            }
        });

        // Inicializar filtro al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            filtrarMedicamentos();
        });
    </script>
</body>
</html>

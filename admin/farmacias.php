<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$farmacia_editar = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'crear_farmacia':
                    $stmt = $pdo->prepare("
                        INSERT INTO farmacias (nombre, direccion, telefono, latitud, longitud, distrito, activa, de_turno, horario_apertura, horario_cierre, servicios, observaciones)
                        VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['nombre'],
                        $_POST['direccion'],
                        $_POST['telefono'],
                        $_POST['latitud'],
                        $_POST['longitud'],
                        $_POST['distrito'],
                        isset($_POST['de_turno']) ? 1 : 0,
                        $_POST['horario_apertura'],
                        $_POST['horario_cierre'],
                        $_POST['servicios'],
                        $_POST['observaciones']
                    ]);
                    $mensaje = 'Farmacia creada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'actualizar_farmacia':
                    $stmt = $pdo->prepare("
                        UPDATE farmacias SET 
                            nombre = ?, direccion = ?, telefono = ?, latitud = ?, longitud = ?, 
                            distrito = ?, de_turno = ?, horario_apertura = ?, horario_cierre = ?, 
                            servicios = ?, observaciones = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['nombre'],
                        $_POST['direccion'],
                        $_POST['telefono'],
                        $_POST['latitud'],
                        $_POST['longitud'],
                        $_POST['distrito'],
                        isset($_POST['de_turno']) ? 1 : 0,
                        $_POST['horario_apertura'],
                        $_POST['horario_cierre'],
                        $_POST['servicios'],
                        $_POST['observaciones'],
                        $_POST['id']
                    ]);
                    $mensaje = 'Farmacia actualizada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'eliminar_farmacia':
                    $stmt = $pdo->prepare("UPDATE farmacias SET activa = 0 WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $mensaje = 'Farmacia eliminada exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'cambiar_turno':
                    $stmt = $pdo->prepare("UPDATE farmacias SET de_turno = ? WHERE id = ?");
                    $stmt->execute([$_POST['de_turno'], $_POST['id']]);
                    $mensaje = 'Estado de turno actualizado exitosamente';
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
    $stmt = $pdo->query("
        SELECT f.*, 
               COUNT(u.id) as total_usuarios
        FROM farmacias f
        LEFT JOIN usuarios u ON f.id = u.id_farmacia AND u.activo = 1
        WHERE f.activa = 1
        GROUP BY f.id
        ORDER BY f.nombre
    ");
    $farmacias = $stmt->fetchAll();
    
    // Estadísticas de farmacias
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_farmacias,
            COUNT(CASE WHEN de_turno = 1 THEN 1 END) as farmacias_turno,
            COUNT(CASE WHEN de_turno = 0 THEN 1 END) as farmacias_cerradas
        FROM farmacias 
        WHERE activa = 1
    ");
    $estadisticas_farmacias = $stmt->fetch();
    
} catch (Exception $e) {
    $farmacias = [];
    $estadisticas_farmacias = ['total_farmacias' => 0, 'farmacias_turno' => 0, 'farmacias_cerradas' => 0];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}

// Obtener farmacia para editar
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM farmacias WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $farmacia_editar = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Farmacias - Sistema de Farmacias</title>
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
                    <li><a href="farmacias.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">🏥 Gestión de Farmacias</h1>
                    <p class="text-gray-600">Administra todas las farmacias del sistema y controla los turnos</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas de farmacias -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">De Turno (24hs)</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacias['farmacias_turno']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                                <i class="fas fa-door-closed text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Cerradas</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_farmacias['farmacias_cerradas']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <button onclick="mostrarFormulario()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Nueva Farmacia
                    </button>
                    <button onclick="verMapa()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-map"></i> Ver Mapa
                    </button>
                </div>

                <!-- Formulario de farmacia -->
                <div id="formulario-farmacia" class="mb-8 bg-white p-6 rounded-lg shadow <?php echo $farmacia_editar ? '' : 'hidden'; ?>">
                    <h2 class="text-xl font-semibold mb-4">
                        <?php echo $farmacia_editar ? 'Editar Farmacia' : 'Nueva Farmacia'; ?>
                    </h2>
                    
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="accion" value="<?php echo $farmacia_editar ? 'actualizar_farmacia' : 'crear_farmacia'; ?>">
                        <?php if ($farmacia_editar): ?>
                            <input type="hidden" name="id" value="<?php echo $farmacia_editar['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Farmacia</label>
                            <input type="text" name="nombre" required 
                                   value="<?php echo htmlspecialchars($farmacia_editar['nombre'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="tel" name="telefono" 
                                   value="<?php echo htmlspecialchars($farmacia_editar['telefono'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <textarea name="direccion" rows="2" required 
                                      class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($farmacia_editar['direccion'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Distrito</label>
                            <input type="text" name="distrito" 
                                   value="<?php echo htmlspecialchars($farmacia_editar['distrito'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                            <input type="number" name="latitud" step="any" 
                                   value="<?php echo $farmacia_editar['latitud'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                            <input type="number" name="longitud" step="any" 
                                   value="<?php echo $farmacia_editar['longitud'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horario de Apertura</label>
                            <input type="time" name="horario_apertura" 
                                   value="<?php echo $farmacia_editar['horario_apertura'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horario de Cierre</label>
                            <input type="time" name="horario_cierre" 
                                   value="<?php echo $farmacia_editar['horario_cierre'] ?? ''; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Servicios</label>
                            <textarea name="servicios" rows="2" 
                                      placeholder="Ej: Delivery, Vacunas, Análisis clínicos..."
                                      class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($farmacia_editar['servicios'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones" rows="2" 
                                      class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($farmacia_editar['observaciones'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="de_turno" <?php echo ($farmacia_editar['de_turno'] ?? 0) ? 'checked' : ''; ?>
                                       class="mr-2">
                                <span class="text-sm text-gray-700">Farmacia de turno (24 horas)</span>
                            </label>
                        </div>
                        
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class="fas fa-save"></i> <?php echo $farmacia_editar ? 'Actualizar' : 'Crear'; ?>
                            </button>
                            <button type="button" onclick="ocultarFormulario()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de farmacias -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Lista de Farmacias</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmacia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horarios</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($farmacias as $farmacia): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($farmacia['nombre']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($farmacia['telefono']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div><?php echo htmlspecialchars($farmacia['direccion']); ?></div>
                                            <div class="text-gray-500"><?php echo htmlspecialchars($farmacia['distrito']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($farmacia['de_turno']): ?>
                                            <span class="text-green-600 font-semibold">24 horas</span>
                                        <?php else: ?>
                                            <div>
                                                <div><?php echo $farmacia['horario_apertura'] ?: 'N/A'; ?> - <?php echo $farmacia['horario_cierre'] ?: 'N/A'; ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($farmacia['de_turno']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-clock mr-1"></i>De Turno
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-door-closed mr-1"></i>Cerrada
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $farmacia['total_usuarios']; ?> usuario(s)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="?editar=<?php echo $farmacia['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 px-2 py-1 rounded">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button onclick="cambiarTurno(<?php echo $farmacia['id']; ?>, <?php echo $farmacia['de_turno']; ?>, '<?php echo htmlspecialchars($farmacia['nombre']); ?>')" 
                                                    class="text-green-600 hover:text-green-900 bg-green-100 px-2 py-1 rounded">
                                                <i class="fas fa-clock"></i> Turno
                                            </button>
                                            <button onclick="eliminarFarmacia(<?php echo $farmacia['id']; ?>, '<?php echo htmlspecialchars($farmacia['nombre']); ?>')" 
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

    <!-- Modal para cambiar turno -->
    <div id="modal-turno" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Cambiar Estado de Turno</h3>
                <form id="form-turno" method="POST">
                    <input type="hidden" name="accion" value="cambiar_turno">
                    <input type="hidden" name="id" id="turno-farmacia-id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia</label>
                        <input type="text" id="turno-farmacia-nombre" readonly 
                               class="w-full p-2 border border-gray-300 rounded bg-gray-50">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="de_turno" required class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="0">Cerrada</option>
                            <option value="1">De Turno (24hs)</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                        <button type="button" onclick="cerrarModalTurno()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarFormulario() {
            document.getElementById('formulario-farmacia').classList.remove('hidden');
        }
        
        function ocultarFormulario() {
            document.getElementById('formulario-farmacia').classList.add('hidden');
            window.location.href = 'farmacias.php';
        }
        
        function cambiarTurno(id, estadoActual, nombre) {
            document.getElementById('turno-farmacia-id').value = id;
            document.getElementById('turno-farmacia-nombre').value = nombre;
            document.querySelector('select[name="de_turno"]').value = estadoActual;
            document.getElementById('modal-turno').classList.remove('hidden');
        }
        
        function cerrarModalTurno() {
            document.getElementById('modal-turno').classList.add('hidden');
        }
        
        function eliminarFarmacia(id, nombre) {
            if (confirm(`¿Estás seguro de que quieres eliminar la farmacia "${nombre}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="eliminar_farmacia">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function verMapa() {
            alert('Función de mapa en desarrollo');
        }
    </script>
</body>
</html>
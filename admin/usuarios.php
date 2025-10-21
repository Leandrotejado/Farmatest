<?php
require '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$usuario_editar = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'crear_usuario':
                    // Verificar si el usuario ya existe
                    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario_nombre = ? OR email = ?");
                    $stmt->execute([$_POST['usuario_nombre'], $_POST['email']]);
                    if ($stmt->fetch()) {
                        $mensaje = 'El usuario o email ya existe';
                        $tipo_mensaje = 'error';
                        break;
                    }
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios (usuario_nombre, email, password, rol, id_farmacia, activo)
                        VALUES (?, ?, ?, ?, ?, 1)
                    ");
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->execute([
                        $_POST['usuario_nombre'],
                        $_POST['email'],
                        $password_hash,
                        $_POST['rol'],
                        $_POST['id_farmacia'] ?: null
                    ]);
                    $mensaje = 'Usuario creado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'actualizar_usuario':
                    $stmt = $pdo->prepare("
                        UPDATE usuarios SET 
                            usuario_nombre = ?, email = ?, rol = ?, id_farmacia = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['usuario_nombre'],
                        $_POST['email'],
                        $_POST['rol'],
                        $_POST['id_farmacia'] ?: null,
                        $_POST['id']
                    ]);
                    
                    // Actualizar contraseña si se proporcionó
                    if (!empty($_POST['password'])) {
                        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt->execute([$password_hash, $_POST['id']]);
                    }
                    
                    $mensaje = 'Usuario actualizado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'eliminar_usuario':
                    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $mensaje = 'Usuario eliminado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
                    
                case 'cambiar_estado':
                    $stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
                    $stmt->execute([$_POST['activo'], $_POST['id']]);
                    $mensaje = 'Estado del usuario actualizado exitosamente';
                    $tipo_mensaje = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener usuarios
try {
    $stmt = $pdo->query("
        SELECT 
            u.*,
            f.nombre as farmacia_nombre
        FROM usuarios u
        LEFT JOIN farmacias f ON u.id_farmacia = f.id
        WHERE u.activo = 1
        ORDER BY u.usuario_nombre
    ");
    $usuarios = $stmt->fetchAll();
    
    // Obtener farmacias
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
    
    // Estadísticas de usuarios
    $usuarios_table_exists = $pdo->query("SHOW TABLES LIKE 'usuarios'")->rowCount() > 0;
    if ($usuarios_table_exists) {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_usuarios,
                COUNT(CASE WHEN rol = 'admin' THEN 1 END) as administradores,
                COUNT(CASE WHEN rol = 'farmacia' THEN 1 END) as usuarios_farmacia,
                COUNT(CASE WHEN id_farmacia IS NOT NULL THEN 1 END) as usuarios_asignados,
                COUNT(CASE WHEN id_farmacia IS NULL THEN 1 END) as usuarios_sin_asignar
            FROM usuarios 
            WHERE activo = 1
        ");
        $estadisticas_usuarios = $stmt->fetch();
    } else {
        $estadisticas_usuarios = ['total_usuarios' => 0, 'administradores' => 0, 'usuarios_farmacia' => 0, 'usuarios_asignados' => 0, 'usuarios_sin_asignar' => 0];
    }
    
} catch (Exception $e) {
    $usuarios = [];
    $farmacias = [];
    $estadisticas_usuarios = ['total_usuarios' => 0, 'administradores' => 0, 'usuarios_farmacia' => 0, 'usuarios_asignados' => 0, 'usuarios_sin_asignar' => 0];
    $mensaje = 'Error al cargar datos: ' . $e->getMessage();
    $tipo_mensaje = 'error';
}

// Obtener usuario para editar
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $usuario_editar = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Farmacias</title>
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
                    <li><a href="ventas.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-shopping-cart mr-3"></i>Ventas
                    </a></li>
                    <li><a href="reportes.php" class="flex items-center p-3 text-white hover:bg-blue-700 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>Reportes
                    </a></li>
                    <li><a href="usuarios.php" class="flex items-center p-3 text-white bg-blue-700 rounded-lg">
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
                    <h1 class="text-3xl font-bold text-gray-800">👥 Gestión de Usuarios</h1>
                    <p class="text-gray-600">Administra usuarios y permisos del sistema</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded <?php echo $tipo_mensaje === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas de usuarios -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_usuarios['total_usuarios']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-user-shield text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Administradores</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_usuarios['administradores']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-user-md text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Usuarios Farmacia</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_usuarios['usuarios_farmacia']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-link text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Asignados</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_usuarios['usuarios_asignados']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-unlink text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Sin Asignar</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas_usuarios['usuarios_sin_asignar']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <button onclick="mostrarFormulario()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                    <button onclick="exportarUsuarios()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-file-excel"></i> Exportar Lista
                    </button>
                </div>

                <!-- Formulario de usuario -->
                <div id="formulario-usuario" class="mb-8 bg-white p-6 rounded-lg shadow <?php echo $usuario_editar ? '' : 'hidden'; ?>">
                    <h2 class="text-xl font-semibold mb-4">
                        <?php echo $usuario_editar ? 'Editar Usuario' : 'Nuevo Usuario'; ?>
                    </h2>
                    
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="accion" value="<?php echo $usuario_editar ? 'actualizar_usuario' : 'crear_usuario'; ?>">
                        <?php if ($usuario_editar): ?>
                            <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario</label>
                            <input type="text" name="usuario_nombre" required 
                                   value="<?php echo htmlspecialchars($usuario_editar['usuario_nombre'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($usuario_editar['email'] ?? ''); ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                            <input type="password" name="password" <?php echo $usuario_editar ? '' : 'required'; ?>
                                   placeholder="<?php echo $usuario_editar ? 'Dejar vacío para mantener la actual' : 'Ingrese contraseña'; ?>"
                                   class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                            <select name="rol" required 
                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar rol</option>
                                <option value="admin" <?php echo ($usuario_editar['rol'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                <option value="farmacia" <?php echo ($usuario_editar['rol'] ?? '') === 'farmacia' ? 'selected' : ''; ?>>Farmacia</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Farmacia Asignada</label>
                            <select name="id_farmacia" 
                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Sin asignar</option>
                                <?php foreach ($farmacias as $farmacia): ?>
                                    <option value="<?php echo $farmacia['id']; ?>" 
                                            <?php echo ($usuario_editar['id_farmacia'] ?? '') == $farmacia['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($farmacia['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class="fas fa-save"></i> <?php echo $usuario_editar ? 'Actualizar' : 'Crear'; ?>
                            </button>
                            <button type="button" onclick="ocultarFormulario()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de usuarios -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Lista de Usuarios</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Farmacia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($usuario['usuario_nombre']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($usuario['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($usuario['rol'] === 'admin'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-user-shield mr-1"></i>Administrador
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-user-md mr-1"></i>Farmacia
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $usuario['farmacia_nombre'] ?: 'Sin asignar'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($usuario['activo']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="?editar=<?php echo $usuario['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 px-2 py-1 rounded">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button onclick="cambiarEstado(<?php echo $usuario['id']; ?>, <?php echo $usuario['activo']; ?>, '<?php echo htmlspecialchars($usuario['usuario_nombre']); ?>')" 
                                                    class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 px-2 py-1 rounded">
                                                <i class="fas fa-toggle-on"></i> Estado
                                            </button>
                                            <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['usuario_nombre']); ?>')" 
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

    <!-- Modal para cambiar estado -->
    <div id="modal-estado" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Cambiar Estado del Usuario</h3>
                <form id="form-estado" method="POST">
                    <input type="hidden" name="accion" value="cambiar_estado">
                    <input type="hidden" name="id" id="estado-usuario-id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                        <input type="text" id="estado-usuario-nombre" readonly 
                               class="w-full p-2 border border-gray-300 rounded bg-gray-50">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="activo" required class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                        <button type="button" onclick="cerrarModalEstado()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarFormulario() {
            document.getElementById('formulario-usuario').classList.remove('hidden');
        }
        
        function ocultarFormulario() {
            document.getElementById('formulario-usuario').classList.add('hidden');
            window.location.href = 'usuarios.php';
        }
        
        function cambiarEstado(id, estadoActual, nombre) {
            document.getElementById('estado-usuario-id').value = id;
            document.getElementById('estado-usuario-nombre').value = nombre;
            document.querySelector('select[name="activo"]').value = estadoActual;
            document.getElementById('modal-estado').classList.remove('hidden');
        }
        
        function cerrarModalEstado() {
            document.getElementById('modal-estado').classList.add('hidden');
        }
        
        function eliminarUsuario(id, nombre) {
            if (confirm(`¿Estás seguro de que quieres eliminar el usuario "${nombre}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="eliminar_usuario">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function exportarUsuarios() {
            alert('Función de exportación en desarrollo');
        }
    </script>
</body>
</html>

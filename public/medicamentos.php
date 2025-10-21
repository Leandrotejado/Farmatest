<?php
require '../config/db.php';

// Obtener parámetros de búsqueda
$farmacia_id = isset($_GET['farmacia']) ? (int)$_GET['farmacia'] : 0;
$medicamento_busqueda = isset($_GET['medicamento']) ? trim($_GET['medicamento']) : '';

// Obtener farmacias para el filtro con manejo de errores
try {
    $stmt = $pdo->query("SELECT * FROM farmacias WHERE activa = 1 ORDER BY nombre");
    $farmacias = $stmt->fetchAll();
} catch (Exception $e) {
    $farmacias = [];
}

// Construir consulta de medicamentos
$where_conditions = ["mf.activo = 1"];
$params = [];

if ($farmacia_id > 0) {
    $where_conditions[] = "mf.id_farmacia = ?";
    $params[] = $farmacia_id;
}

if (!empty($medicamento_busqueda)) {
    $where_conditions[] = "m.nombre LIKE ?";
    $params[] = "%$medicamento_busqueda%";
}

$where_clause = implode(" AND ", $where_conditions);

try {
    $stmt = $pdo->prepare("
        SELECT mf.*, m.nombre as medicamento_nombre, m.descripcion, m.precio as precio_base,
               f.nombre as farmacia_nombre, f.direccion, f.telefono, f.obras_sociales,
               c.nombre as categoria_nombre
        FROM medicamentos_farmacias mf
        JOIN medicamentos m ON mf.id_medicamento = m.id
        JOIN farmacias f ON mf.id_farmacia = f.id
        LEFT JOIN categorias c ON m.id_categoria = c.id
        WHERE $where_clause
        ORDER BY f.nombre, m.nombre
    ");

    $stmt->execute($params);
    $medicamentos = $stmt->fetchAll();
} catch (Exception $e) {
    $medicamentos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos Disponibles - FarmaXpress</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark Mode Script -->
    <script src="assets/dark-mode.js"></script>
    <style>
        /* Eliminar subrayados de todos los enlaces del menú */
        nav a {
            text-decoration: none !important;
        }
        
        nav a:hover {
            text-decoration: none !important;
        }
        
        nav a:focus {
            text-decoration: none !important;
        }
        
        nav a:visited {
            text-decoration: none !important;
        }
        
        /* Estilos para el menú desplegable */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #1e40af;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 8px;
            top: 100%;
            right: 0;
            margin-top: 5px;
        }
        
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
            border-radius: 8px;
            margin: 4px;
        }
        
        .dropdown-content a:hover {
            background-color: #3b82f6;
            text-decoration: none !important;
        }
        
        .dropdown.active .dropdown-content {
            display: block;
        }
        
        .dropdown.active .dropdown-trigger {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .dropdown-trigger {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .dropdown-trigger:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        /* Responsive para header */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            nav ul {
                font-size: 0.875rem !important;
            }
            
            nav ul li {
                margin-right: 0.5rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 shadow-lg z-10">
        <div class="container mx-auto px-4 py-2">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center">
                        <img src="../logo.png" alt="FarmaXpress Logo" class="h-16 w-auto logo-header" style="max-width: 20%; height: auto;">
                    </a>
                </div>
                <nav id="main-nav" class="hidden md:block">
                    <ul class="flex items-center space-x-4 text-base font-medium">
                        <li><a href="index.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Inicio</a></li>
                        <li><a href="index.php#map" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Mapa</a></li>
                        <li><a href="index.php#pharmacies" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Farmacias</a></li>
                        <li><a href="medicamentos.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap bg-blue-700 px-3 py-1 rounded">Medicamentos</a></li>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Usuario logueado -->
                            <li class="dropdown">
                                <div class="dropdown-trigger flex items-center space-x-1 cursor-pointer px-3 py-2 transition duration-200" onclick="toggleDropdown(this)">
                                    <span class="text-green-300 text-base whitespace-nowrap">👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span>
                                    <span class="text-white dropdown-arrow">⚙️</span>
                                </div>
                                <div class="dropdown-content">
                                    <a href="dashboard.php">Mi Cuenta</a>
                                    <a href="logout.php">Cerrar Sesión</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <!-- Usuario no logueado -->
                            <li class="flex items-center space-x-2">
                                <span class="text-yellow-300 text-base">🔒 Acceso limitado</span>
                            </li>
                            <li><a href="register.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Registrarse</a></li>
                            <li><a href="login.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Iniciar Sesión</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <button id="mobile-menu-btn" class="md:hidden text-white text-xl">
                    ☰
                </button>
            </div>
        </div>
    </header>

    <!-- Menú móvil -->
    <div id="mobile-menu" class="md:hidden bg-blue-800 text-white hidden">
        <div class="container mx-auto px-4 py-4">
            <ul class="space-y-2">
                <li><a href="index.php" class="block py-2 hover:text-blue-200 transition duration-200">Inicio</a></li>
                <li><a href="index.php#map" class="block py-2 hover:text-blue-200 transition duration-200">Mapa</a></li>
                <li><a href="index.php#pharmacies" class="block py-2 hover:text-blue-200 transition duration-200">Farmacias</a></li>
                <li><a href="medicamentos.php" class="block py-2 hover:text-blue-200 transition duration-200">Medicamentos</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Usuario logueado -->
                    <li class="border-t border-blue-600 pt-2 mt-2">
                        <div class="text-green-300 py-2">👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></div>
                        <ul class="ml-4 space-y-1">
                            <li><a href="dashboard.php" class="block py-1 hover:text-blue-200 transition duration-200">Mi Cuenta</a></li>
                            <li><a href="logout.php" class="block py-1 hover:text-red-300 transition duration-200">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Usuario no logueado -->
                    <li class="border-t border-blue-600 pt-2 mt-2">
                        <div class="text-yellow-300 py-2">🔒 Acceso limitado</div>
                        <ul class="ml-4 space-y-1">
                            <li><a href="register.php" class="block py-1 hover:text-blue-200 transition duration-200">Registrarse</a></li>
                            <li><a href="login.php" class="block py-1 hover:text-blue-200 transition duration-200">Iniciar Sesión</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Título y filtros -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">💊 Medicamentos Disponibles</h1>
                <p class="text-gray-600 mb-6">Encuentra medicamentos disponibles en farmacias de turno de la provincia de Buenos Aires</p>
                
                <!-- Filtros -->
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Farmacia:</label>
                        <select name="farmacia" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las farmacias</option>
                            <?php foreach ($farmacias as $farm): ?>
                                <option value="<?php echo $farm['id']; ?>" <?php echo $farmacia_id == $farm['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($farm['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Medicamento:</label>
                        <input type="text" name="medicamento" value="<?php echo htmlspecialchars($medicamento_busqueda); ?>" 
                               placeholder="Nombre del medicamento..." 
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            🔍 Buscar
                        </button>
                    </div>
                </form>
                
                <?php if ($farmacia_id > 0 || !empty($medicamento_busqueda)): ?>
                    <div class="mt-4">
                        <a href="medicamentos.php" class="text-blue-600 hover:underline">← Limpiar filtros</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Resultados -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Resultados (<?php echo count($medicamentos); ?> medicamentos encontrados)
                    </h2>
                </div>
                
                <?php if (empty($medicamentos)): ?>
                    <div class="p-8 text-center">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No se encontraron medicamentos</h3>
                        <p class="text-gray-500">Intenta ajustar los filtros de búsqueda o verifica que la farmacia tenga medicamentos disponibles.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-200">
                        <?php 
                        $farmacia_actual = '';
                        foreach ($medicamentos as $med): 
                            if ($farmacia_actual !== $med['farmacia_nombre']):
                                $farmacia_actual = $med['farmacia_nombre'];
                        ?>
                            <div class="p-6 bg-blue-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-blue-800">🏥 <?php echo htmlspecialchars($med['farmacia_nombre']); ?></h3>
                                        <p class="text-blue-600">📍 <?php echo htmlspecialchars($med['direccion']); ?></p>
                                        <p class="text-blue-600">📞 <?php echo htmlspecialchars($med['telefono']); ?></p>
                                        <?php if ($med['obras_sociales']): ?>
                                            <p class="text-blue-600 text-sm mt-1">💳 Obras sociales: <?php echo htmlspecialchars($med['obras_sociales']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Disponible
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($med['medicamento_nombre']); ?></h4>
                                    <?php if ($med['categoria_nombre']): ?>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($med['categoria_nombre']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600">Stock disponible:</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $med['stock_disponible'] > 10 ? 'bg-green-100 text-green-800' : ($med['stock_disponible'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo $med['stock_disponible']; ?> unidades
                                    </span>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600">Precios:</p>
                                    <div class="text-sm">
                                        <?php if ($med['precio_especial'] && $med['precio_especial'] < $med['precio_base']): ?>
                                            <p class="text-green-600 font-semibold">$<?php echo number_format($med['precio_especial'], 2); ?></p>
                                            <p class="text-gray-400 line-through">$<?php echo number_format($med['precio_base'], 2); ?></p>
                                        <?php else: ?>
                                            <p class="text-gray-800">$<?php echo number_format($med['precio_base'], 2); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div>
                                    <?php if ($med['descuento_obra_social'] > 0): ?>
                                        <p class="text-sm text-gray-600">Descuento obra social:</p>
                                        <p class="text-green-600 font-semibold"><?php echo number_format($med['descuento_obra_social'], 1); ?>%</p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($med['direccion']); ?>&travelmode=driving" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200 transition duration-200">
                                            🗺️ Cómo llegar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($med['descripcion']): ?>
                                <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($med['descripcion']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 bg-yellow-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">💡 Información Importante</h3>
                <ul class="text-yellow-700 space-y-2">
                    <li>• Los precios pueden variar según la farmacia y la obra social</li>
                    <li>• El stock se actualiza en tiempo real</li>
                    <li>• Siempre verifica la disponibilidad antes de desplazarte</li>
                    <li>• Los descuentos de obra social se aplican al momento de la compra</li>
                    <li>• En caso de emergencia, llama al 107 (SAME)</li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-300">© 2025 FarmaXpress. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });

        // Función para toggle del dropdown
        function toggleDropdown(trigger) {
            const dropdown = trigger.closest('.dropdown');
            const isActive = dropdown.classList.contains('active');
            
            // Cerrar todos los dropdowns abiertos
            document.querySelectorAll('.dropdown.active').forEach(function(openDropdown) {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('active');
                }
            });
            
            // Toggle del dropdown actual
            if (isActive) {
                dropdown.classList.remove('active');
            } else {
                dropdown.classList.add('active');
            }
        }

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.active').forEach(function(dropdown) {
                    dropdown.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>

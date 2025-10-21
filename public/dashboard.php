<?php
require '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - FarmaXpress</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <li><a href="medicamentos.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap">Medicamentos</a></li>
                        
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
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</h1>
                    <p class="text-gray-600">Gestiona tu cuenta y accede a servicios exclusivos</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Información Personal -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-blue-800 mb-4">👤 Información Personal</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre:</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($user['nombre']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email:</label>
                                <p class="text-gray-900"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de cuenta:</label>
                                <p class="text-gray-900"><?php echo $user['rol'] == 'admin' ? 'Administrador' : 'Usuario Cliente'; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Servicios Disponibles -->
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-green-800 mb-4">🎯 Servicios Disponibles</h2>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                <span>Buscar farmacias de turno</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                <span>Consultar medicamentos</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                <span>Recibir notificaciones</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                <span>Historial de búsquedas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">⚡ Acciones Rápidas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="index.php#map" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition duration-200">
                            <div class="text-2xl mb-2">🗺️</div>
                            <div class="font-semibold">Buscar Farmacias</div>
                        </a>
                        <a href="index.php#pharmacies" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition duration-200">
                            <div class="text-2xl mb-2">🏥</div>
                            <div class="font-semibold">Ver Farmacias de Turno</div>
                        </a>
                        <a href="index.php#medicines" class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition duration-200">
                            <div class="text-2xl mb-2">💊</div>
                            <div class="font-semibold">Consultar Medicamentos</div>
                        </a>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="mt-8 bg-yellow-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-3">💡 Información Importante</h3>
                    <ul class="text-yellow-700 space-y-2">
                        <li>• Tu cuenta te permite acceder a servicios personalizados</li>
                        <li>• Puedes guardar tus farmacias favoritas</li>
                        <li>• Recibirás notificaciones sobre farmacias de turno cerca de ti</li>
                        <li>• En caso de emergencia, siempre llama al 107 (SAME)</li>
                    </ul>
                </div>
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

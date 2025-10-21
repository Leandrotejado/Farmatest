<?php
require '../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmaXpress - Encuentra Farmacias de Turno</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="test-header.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="css/dark-theme.css">
    <!-- Dark Mode Script -->
    <script src="assets/dark-mode.js"></script>
    <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            
            header {
                background: linear-gradient(to right, #2563eb, #1d4ed8);
                color: white;
            }
            
            header a {
                color: white;
                text-decoration: none;
            }
            
            header a:hover {
                color: #dbeafe;
            }
            
            nav a {
                text-decoration: none;
            }
            
            /* Eliminar líneas de los elementos del header */
            header li,
            header ul,
            header nav,
            header div {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
            }
            
            /* Asegurar que el dropdown mantenga su fondo azul */
            .dropdown-content {
                background: linear-gradient(to right, #2563eb, #1d4ed8) !important;
                background-color: #2563eb !important;
            }
            
            .dropdown-content a {
                color: white !important;
                background: transparent !important;
            }
            
            .dropdown-content a:hover {
                background-color: #3b82f6 !important;
                color: white !important;
            }
            
            nav a:hover {
                text-decoration: none !important;
            }
            
            /* Eliminar fondo azul de botones de cierre */
            button[title="Cerrar"],
            button.close,
            .close-button {
                background: none !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
                outline: none !important;
            }
            
            /* Estilos específicos para botones de cierre con × */
            button:contains("×"),
            button[onclick*="close"] {
                background: none !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
                outline: none !important;
            }
            
            /* Estilos específicos para botones de cierre de notificaciones */
            .notification-stack button,
            .notification-stack button[onclick*="remove"] {
                background: none !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
                outline: none !important;
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
                background: linear-gradient(to right, #2563eb, #1d4ed8) !important;
                background-color: #2563eb !important;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                z-index: 1000;
                border-radius: 8px;
                top: 100%;
                right: 0;
                margin-top: 5px;
                border: none !important;
            }
            
            .dropdown-content a {
                color: white !important;
                padding: 12px 16px;
                text-decoration: none !important;
                display: block;
                transition: background-color 0.3s;
                border-radius: 8px;
                margin: 4px;
                background: transparent !important;
                border: none !important;
            }
            
            .dropdown-content a:hover {
                background-color: #3b82f6 !important;
                text-decoration: none !important;
                color: white !important;
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
            
            /* Asegurar que la información del mapa se mantenga visible */
            #map-info {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            #map-info.hidden {
                display: none !important;
            }
        
        .map-loading {
            position: relative;
        }
        .map-loading::after {
            content: "Cargando mapa...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            font-weight: bold;
            color: #3b82f6;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .custom-div-icon {
            background: transparent !important;
            border: none !important;
        }
        .user-location-icon {
            background: transparent !important;
            border: none !important;
        }
        #map {
            height: 500px !important;
            width: 100% !important;
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
        }
        .leaflet-container {
            height: 500px !important;
            width: 100% !important;
        }
        .map-container {
            width: 100% !important;
        }
        
        /* Estilos para el encabezado y leyenda */
        .text-center.mb-8 {
            position: relative !important;
            z-index: 50 !important;
            background: #ffffff !important;
            padding: 2rem !important;
            border-radius: 12px !important;
            margin-bottom: 2rem !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e5e7eb !important;
            clear: both !important;
        }
        
        header {
            z-index: 1000 !important;
        }
        .flex.justify-center.space-x-4 {
            z-index: 50 !important;
            position: relative !important;
        }
        
        /* Estilos específicos para el título de la sección del mapa */
        section#map .text-center.mb-8 h2 {
            position: relative !important;
            z-index: 50 !important;
            background: #ffffff !important;
            padding: 1rem 0 !important;
            margin: 0 !important;
        }
        
        /* Asegurar que toda la sección del mapa tenga el z-index correcto */
        section#map {
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Estilos simples para el mapa */
        #map {
            height: 500px !important;
            width: 100% !important;
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
        }
        
        /* Estilos simples para el mapa */
        .leaflet-control-container {
            z-index: 1000 !important;
        }
        
        /* Animaciones y efectos para el hero */
        .scale-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .scale-hover:hover {
            transform: scale(1.05);
        }
        
        /* Animación de entrada */
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Efectos de hover mejorados */
        .group:hover .group-hover\:scale-105 {
            transform: scale(1.05);
        }
        
        /* Gradientes de texto */
        .bg-clip-text {
            -webkit-background-clip: text;
            background-clip: text;
        }
        
        /* Efectos de backdrop blur */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        
        /* Estilos para el logo */
        .logo-header {
            transition: opacity 0.3s ease;
        }
        
        .logo-header:hover {
            opacity: 0.8;
        }
        
        .logo-hero {
            transition: opacity 0.3s ease;
        }
        
        
        
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            header {
                position: relative !important;
            }
            
            #main-nav {
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                background: rgba(37, 99, 235, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            
            #main-nav ul {
                padding: 1rem !important;
            }
            
            .text-3xl {
                font-size: 1.5rem !important;
            }
            
            .text-5xl {
                font-size: 2.5rem !important;
            }
            
            .text-xl {
                font-size: 1.125rem !important;
            }
            
            .text-lg {
                font-size: 1rem !important;
            }
            
            .text-sm {
                font-size: 0.875rem !important;
            }
            
            .text-gray-800 {
                color: #1f2937 !important;
            }
            
            .text-gray-600 {
                color: #4b5563 !important;
            }
            
            .text-gray-500 {
                color: #6b7280 !important;
            }
            
            .text-center.mb-8 {
                padding: 1rem !important;
                margin-bottom: 1.5rem !important;
                z-index: 50 !important;
                position: relative !important;
            }
            
            .flex.flex-col.sm\\:flex-row.justify-center.items-center.space-y-2.sm\\:space-y-0.sm\\:space-x-4.text-sm.mb-8 {
                padding: 1rem !important;
                margin-bottom: 1.5rem !important;
                z-index: 50 !important;
                position: relative !important;
            }
            
            /* Asegurar que el título del mapa no se superponga en móviles */
            section#map .text-center.mb-8 h2 {
                z-index: 50 !important;
                position: relative !important;
                background: #ffffff !important;
                padding: 0.5rem 0 !important;
            }
            
            /* Estilos simples para móviles */
            section#map .text-center.mb-8 {
                padding: 1.5rem !important;
                margin: 0 auto 1.5rem auto !important;
                max-width: 95% !important;
            }
            
            .flex.flex-col.sm\\:flex-row.justify-center.items-center.space-y-2.sm\\:space-y-0.sm\\:space-x-4.text-sm.mb-8 {
                padding: 1rem 1.5rem !important;
                margin: 0 auto 1.5rem auto !important;
                max-width: 95% !important;
            }
            
            /* Contador de farmacias en móviles */
            .bg-gradient-to-r.from-green-100.to-blue-100 {
                padding: 0.75rem !important;
                margin: 0.5rem 0 !important;
            }
            
            .bg-gradient-to-r.from-green-100.to-blue-100 .flex {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            
            .bg-gradient-to-r.from-green-100.to-blue-100 .text-gray-400 {
                display: none !important;
            }
            
            .py-20 {
                padding-top: 3rem !important;
                padding-bottom: 3rem !important;
            }
            
            .flex.space-x-6 {
                flex-direction: column !important;
                gap: 1rem !important;
            }
            
            .flex.space-x-4 {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
            
            .px-8 {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
            
            .py-3 {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            #map {
                height: 400px !important;
            }
            
            .leaflet-container {
                height: 400px !important;
            }
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
            
            .flex.justify-center.space-x-4.mt-6 {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
            
            .flex.justify-center.space-x-4.mt-6 button {
                width: 100% !important;
                margin: 0 !important;
            }
        }
        
        @media (max-width: 480px) {
            .text-5xl {
                font-size: 2rem !important;
            }
            
            .text-3xl {
                font-size: 1.25rem !important;
            }
            
            .px-8 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            #map {
                height: 350px !important;
            }
            
            .leaflet-container {
                height: 350px !important;
            }
        }
        
        /* Mejoras para tablets */
        @media (min-width: 769px) and (max-width: 1024px) {
            #map {
                height: 450px !important;
            }
            
            .leaflet-container {
                height: 450px !important;
            }
        }

        /* Estilos para la barra de progreso de las notificaciones */
        .progress-bar-container {
            position: relative;
            overflow: hidden;
        }
        
        .progress-bar {
            transform-origin: right center;
            will-change: transform;
        }
        
        .notification-stack .progress-bar {
            animation: progressShrinkRight 5s linear forwards;
        }
        
        .notification-stack .progress-bar.long-duration {
            animation: progressShrinkRight 10s linear forwards;
        }
        
        @keyframes progressShrinkRight {
            from {
                transform: scaleX(1);
            }
            to {
                transform: scaleX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 shadow-lg z-10" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; color: white !important; border: none !important; box-shadow: none !important; outline: none !important;">
        <div class="container mx-auto px-4 py-2" style="background: transparent !important;">
            <div class="flex justify-between items-center" style="background: transparent !important;">
                <div class="flex items-center space-x-4" style="background: transparent !important;">
                    <a href="index.php" class="flex items-center" style="background: transparent !important;">
                        <img src="../logo.png" alt="FarmaXpress Logo" class="h-16 w-auto logo-header" style="max-width: 20%; height: auto;">
                    </a>
                </div>
            <nav id="main-nav" class="hidden md:block" style="background: transparent !important;">
                    <ul class="flex items-center space-x-4 text-base font-medium" style="background: transparent !important;">
                        <li style="background: transparent !important;"><a href="index.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap" style="color: white !important; background: transparent !important; text-decoration: none !important; border: none !important;">Inicio</a></li>
                        <li style="background: transparent !important;"><a href="#map" class="hover:text-blue-200 transition duration-200 whitespace-nowrap" style="color: white !important; background: transparent !important; text-decoration: none !important; border: none !important;">Mapa</a></li>
                        <li style="background: transparent !important;"><a href="#pharmacies" class="hover:text-blue-200 transition duration-200 whitespace-nowrap" style="color: white !important; background: transparent !important; text-decoration: none !important; border: none !important;">Farmacias</a></li>
                        <li style="background: transparent !important;"><a href="medicamentos.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap" style="color: white !important; background: transparent !important; text-decoration: none !important; border: none !important;">Medicamentos</a></li>
                        <li style="background: transparent !important;"><a href="medicamentos_farmacias.php" class="hover:text-blue-200 transition duration-200 whitespace-nowrap" style="color: white !important; background: transparent !important; text-decoration: none !important; border: none !important;">Stock por Farmacia</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Usuario logueado -->
                        <li class="dropdown" style="background: transparent !important;">
                            <div class="dropdown-trigger flex items-center space-x-1 cursor-pointer px-3 py-2 transition duration-200" onclick="toggleDropdown(this)" style="background: transparent !important;">
                                <span class="text-blue-200 text-base whitespace-nowrap" style="color: #dbeafe !important; background: transparent !important;">👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span>
                                
                            </div>
                            <div class="dropdown-content" style="background: linear-gradient(to right, #2563eb, #1d4ed8) !important; background-color: #2563eb !important;">
                                <a href="dashboard.php" style="color: white !important; background: transparent !important; text-decoration: none !important;">Mi Cuenta</a>
                                <a href="logout.php" style="color: white !important; background: transparent !important; text-decoration: none !important;">Cerrar Sesión</a>
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
                <li><a href="#map" class="block py-2 hover:text-blue-200 transition duration-200">Mapa</a></li>
                <li><a href="#pharmacies" class="block py-2 hover:text-blue-200 transition duration-200">Farmacias</a></li>
                <li><a href="medicamentos.php" class="block py-2 hover:text-blue-200 transition duration-200">Medicamentos</a></li>
                <li><a href="medicamentos_farmacias.php" class="block py-2 hover:text-blue-200 transition duration-200">Stock por Farmacia</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Usuario logueado -->
                    <li class="border-t border-blue-600 pt-2 mt-2">
                        <div class="text-blue-200 py-2">👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></div>
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

    <!-- Hero Section Mejorada -->
    <section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-20 left-10 w-20 h-20 bg-white opacity-5 rounded-full"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-white opacity-5 rounded-full"></div>
            <div class="absolute bottom-40 right-1/3 w-24 h-24 bg-white opacity-5 rounded-full"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-24">
            <div class="text-center max-w-7xl mx-auto">
                <!-- Logo principal -->
                <div class="mb-8 flex justify-center">
                    <img src="../logo.png" alt="FarmaXpress Logo" class="h-40 w-auto logo-hero" style="max-width: 50%; height: auto;">
                </div>
                
                
                
                <!-- Subtítulo -->
                <h2 class="text-2xl md:text-3xl font-bold mb-4 text-blue-100">
                    Encuentra Farmacias de Turno Cerca de Ti
                </h2>
                
                <!-- Descripción -->
                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto leading-relaxed">
                    Descubre farmacias de guardia abiertas las 24 horas, consulta medicamentos disponibles y aprovecha descuentos con tu obra social.
                </p>
                
                <!-- Estado del usuario -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 border border-yellow-300 text-white px-10 py-8 rounded-2xl mb-8 max-w-4xl mx-auto shadow-2xl">
                        <div class="flex items-center justify-center space-x-4">
                            <span class="text-4xl">🔒</span>
                            <div class="text-left">
                                <p class="font-bold text-xl">Acceso Limitado</p>
                                <p class="text-base opacity-90">Para usar funciones interactivas como ubicación, medicamentos y mapas, necesitas registrarte o iniciar sesión.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 border border-green-300 text-white px-8 py-6 rounded-2xl mb-8 max-w-2xl mx-auto shadow-2xl">
                        <div class="flex items-center justify-center space-x-3">
                            <span class="text-3xl">✅</span>
                            <div class="text-left">
                                <p class="font-bold text-lg">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>!</p>
                                <p class="text-sm opacity-90">Tienes acceso completo a todas las funciones del sistema.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
                <!-- Botones principales -->
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 mb-10">
                    <button onclick="checkAuthAndGetLocation()" class="group bg-white text-blue-600 px-12 py-5 rounded-full font-bold text-lg hover:bg-blue-50 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl border-2 border-transparent hover:border-blue-200 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-100 to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="flex items-center space-x-3 relative z-10">
                            <span class="text-xl">📍</span>
                            <span>Usar mi ubicación</span>
                        </span>
                    </button>
                    <a href="#pharmacies" class="group bg-gradient-to-r from-blue-500 to-blue-600 text-white px-12 py-5 rounded-full font-bold text-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl border-2 border-transparent hover:border-blue-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="flex items-center space-x-3 relative z-10">
                            <span class="text-xl">🏥</span>
                            <span>Ver todas las farmacias</span>
                        </span>
                    </a>
            </div>
            
                <!-- Botones de registro (solo para usuarios no logueados) -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <a href="register.php" class="group bg-gradient-to-r from-green-500 to-emerald-600 text-white px-10 py-4 rounded-full font-semibold hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border-2 border-transparent hover:border-green-300 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="flex items-center space-x-3 relative z-10">
                                <span class="text-lg">👤</span>
                                <span>Registrarse Gratis</span>
                            </span>
                        </a>
                        <a href="login.php" class="group bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-10 py-4 rounded-full font-semibold hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border-2 border-transparent hover:border-indigo-300 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="flex items-center space-x-3 relative z-10">
                                <span class="text-lg">🔐</span>
                                <span>Iniciar Sesión</span>
                            </span>
                    </a>
                </div>
                
                    <div class="mt-8 text-center">
                        <p class="text-blue-200 text-lg font-medium">Regístrate gratis y accede a todas las funciones</p>
                </div>
            <?php endif; ?>
        </div>
        </div>
        
        <!-- Transición elegante con gradiente suave -->
        <div class="absolute bottom-0 left-0 w-full h-10 bg-gradient-to-t from-white via-blue-10 to-blue-30 shadow-lg"></div>
        
    </section>

    <!-- Sección de texto separada -->
    <section class="container mx-auto px-4 py-6 bg-gradient-to-b from-gray-50 to-white">
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold mb-6 text-gray-800 bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Farmacias de Turno - Provincia de Buenos Aires</h2>
            <p class="text-xl text-gray-600 mb-4 font-medium">Encuentra farmacias de guardia abiertas en TODOS los distritos de la provincia</p>
            <p class="text-base text-gray-500 mb-6">Conurbano, La Plata, Mar del Plata, Bahía Blanca, Tandil, Pergamino, Junín y más de 40 ciudades</p>
            
            <!-- Contador de farmacias mejorado -->
            <div class="bg-gradient-to-r from-green-100 via-blue-50 to-indigo-100 border-2 border-green-200 rounded-2xl p-6 mb-6 inline-block shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-center space-x-6 text-base">
                    <div class="flex items-center space-x-2">
                        <span class="text-green-600 font-bold text-xl" id="farmacias-abiertas">79</span>
                        <span class="text-gray-700 font-medium">farmacias de turno</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="text-red-600 font-bold text-xl" id="farmacias-cerradas">1</span>
                        <span class="text-gray-700 font-medium">farmacia cerrada</span>
                    </div>
                </div>
        </div>
        
        <!-- Leyenda de farmacias -->
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-2 sm:space-y-0 sm:space-x-4 text-sm">
            <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2 shadow-sm"></div>
                    <span class="text-gray-700 font-medium">Farmacia de turno</span>
            </div>
            <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-2 shadow-sm"></div>
                    <span class="text-gray-700 font-medium">Farmacia cerrada</span>
            </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-2 shadow-sm"></div>
                    <span class="text-gray-700 font-medium">Tu ubicación</span>
        </div>
        </div>
        </div>
    </section>

    <!-- Sección del mapa completamente separada -->
    <section id="map" class="container mx-auto px-0">
        <!-- Mensaje inicial del mapa -->
        <div id="map-placeholder" class="w-full h-[500px] rounded-xl shadow-xl border border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center mb-8">
            <div class="text-center text-gray-700">
                <div class="text-8xl mb-6">🗺️</div>
                <h3 class="text-2xl font-bold mb-4 text-gray-800">Mapa de Farmacias de Turno</h3>
                <p class="text-lg text-gray-600 mb-6">Encuentra farmacias de guardia abiertas las 24 horas</p>
                
                <!-- Botones de carga inicial -->
                <div id="load-buttons" class="flex flex-col sm:flex-row justify-center items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <button onclick="loadMapWithLocation()" class="bg-blue-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                        🗺️ Cargar Mapa
            </button>
                    <button onclick="loadMapOnly()" class="bg-gray-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-gray-700 transition duration-200 shadow-lg">
                        📍 Solo Mapa
            </button>
                </div>
            </div>
        </div>
        
        <!-- Mapa real (oculto inicialmente) -->
        <div id="map-container" class="w-full h-[500px] rounded-xl shadow-xl border border-gray-200 hidden relative mb-8">
            <div id="map" class="w-full h-full rounded-xl relative">
                <!-- Información importante (dentro del mapa, esquina superior derecha) -->
                <div id="map-info" class="absolute top-4 right-4 bg-white bg-opacity-95 p-4 rounded-lg shadow-lg max-w-xs hidden" style="z-index: 9999 !important;">
                    <!-- Botón de cerrar -->
                    <button onclick="closeMapInfo()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold leading-none" title="Cerrar" style="background: none !important; border: none !important; padding: 0 !important; box-shadow: none !important;">
                        ×
                    </button>
                    <h3 class="text-lg font-semibold text-blue-800 mb-2 pr-6">💡 Información Importante</h3>
                    <ul class="text-blue-700 space-y-1 text-sm">
                        <li>• Las farmacias de turno están abiertas las 24 horas</li>
                        <li>• Los horarios pueden variar en días feriados</li>
                        <li>• Siempre verifica la disponibilidad antes de desplazarte</li>
                        <li>• En caso de emergencia, llama al 107 (SAME)</li>
                    </ul>
                </div>
            </div>
        </div>
        
        
    </section>

    <!-- Sección de controles del mapa entre mapa y farmacias (oculta inicialmente) -->
    <section id="map-controls-section" class="container mx-auto px-4 py-8 hidden">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Controles del Mapa</h3>
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <button onclick="refreshMap()" class="bg-green-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-green-700 transition duration-200 shadow-lg">
                    🔄 Actualizar mapa
                </button>
                <button onclick="centerOnUserLocation()" class="bg-blue-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                    📍 Centrar en mi ubicación
                </button>
                <button onclick="resetMapView()" class="bg-purple-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-purple-700 transition duration-200 shadow-lg">
                    🏠 Vista general
                </button>
            </div>
        </div>
    </section>

    <section id="pharmacies" class="container mx-auto px-4 py-8 fade-in" style="margin-top: 2rem;">
        <div class="flex justify-between items-center mb-8">
            <h2 id="pharmacies-title" class="text-3xl font-bold text-gray-800">Farmacias de Turno Cercanas</h2>
            <div id="location-indicator" class="hidden flex items-center text-sm text-blue-600">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                <span>Actualizando ubicación...</span>
            </div>
        </div>
        
        <div id="pharmacies-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia Central</h3>
                <p class="text-gray-600 mb-2">📍 Av. Corrientes 1234, CABA</p>
                <p class="text-gray-600 mb-2">📞 (011) 1234-5678</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(1, 'Farmacia Central')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(1, 'Farmacia Central')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia del Pueblo</h3>
                <p class="text-gray-600 mb-2">📍 Av. Rivadavia 5678, La Plata</p>
                <p class="text-gray-600 mb-2">📞 (0221) 987-6543</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(2, 'Farmacia del Pueblo')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(2, 'Farmacia del Pueblo')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia Centro MDP</h3>
                <p class="text-gray-600 mb-2">📍 Av. Luro 2500, Mar del Plata</p>
                <p class="text-gray-600 mb-2">📞 (0223) 567-8901</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(8, 'Farmacia Centro MDP')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(8, 'Farmacia Centro MDP')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia Bahía</h3>
                <p class="text-gray-600 mb-2">📍 Av. Colón 200, Bahía Blanca</p>
                <p class="text-gray-600 mb-2">📞 (0291) 456-7890</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(10, 'Farmacia Bahía')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(10, 'Farmacia Bahía')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia Tandil</h3>
                <p class="text-gray-600 mb-2">📍 Av. Colón 800, Tandil</p>
                <p class="text-gray-600 mb-2">📞 (0249) 456-7890</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(12, 'Farmacia Tandil')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(12, 'Farmacia Tandil')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Farmacia Pilar</h3>
                <p class="text-gray-600 mb-2">📍 Av. Tratado del Pilar 2000, Pilar</p>
                <p class="text-gray-600 mb-2">📞 (0230) 456-7890</p>
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Abierta 24hs</span>
                </div>
                <button onclick="verMedicamentos(24, 'Farmacia Pilar')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                    💊 Ver medicamentos
                </button>
                <button onclick="irAFarmacia(24, 'Farmacia Pilar')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                    🏥 Ir a esta farmacia
                </button>
            </div>
        </div>
        
        <!-- Botones para actualizar ubicación y reordenar -->
        <div class="text-center mt-8 space-x-4">
            <button onclick="updatePharmaciesByLocation()" class="bg-blue-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                📍 Actualizar por Ubicación
            </button>
            <button id="stop-tracking-btn" onclick="deactivatePharmacyLocationTracking()" class="bg-red-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-red-700 transition duration-200 shadow-lg hidden">
                🛑 Detener Seguimiento
            </button>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-gray-600 mb-4">📍 <strong>80+ farmacias</strong> distribuidas en toda la Provincia de Buenos Aires</p>
            <p class="text-sm text-gray-500">Incluyendo CABA, La Plata, Mar del Plata, Bahía Blanca, Tandil, Pergamino, Junín, San Nicolás, Zárate, Campana, Pilar, Escobar, Tigre, San Isidro, Vicente López, San Fernando, Luján, Mercedes, Lobos, Chascomús, Dolores, Ayacucho, Balcarce, Necochea, Tres Arroyos, Coronel Suárez, Olavarría, Azul, Bolívar, Pehuajó, 9 de Julio, Bragado, Chivilcoy, San Antonio de Areco, Exaltación de la Cruz, General Rodríguez, Marcos Paz, General Las Heras, Navarro, Saladillo, Roque Pérez, General Belgrano y muchos más distritos.</p>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="mb-4">
                <h3 class="text-2xl font-bold mb-2">FarmaXpress</h3>
                <p class="text-gray-300">Encuentra farmacias de turno cerca de ti</p>
            </div>
            <div class="flex justify-center space-x-6 mb-4">
                <a href="index.php" class="hover:text-blue-300 transition duration-200">Inicio</a>
                <a href="#map" class="hover:text-blue-300 transition duration-200">Mapa</a>
                <a href="#pharmacies" class="hover:text-blue-300 transition duration-200">Farmacias</a>
                <a href="medicamentos.php" class="hover:text-blue-300 transition duration-200">Medicamentos</a>
                <a href="medicamentos_farmacias.php" class="hover:text-blue-300 transition duration-200">Stock por Farmacia</a>
            </div>
            <p class="text-gray-400 text-sm">&copy; 2024 FarmaXpress. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        let map;
        let userLocationMarker;

        // Datos de farmacias de toda la Provincia de Buenos Aires
        const farmacias = [
            // CABA
            { id: 1, name: "Farmacia Central", lat: -34.6037, lng: -58.3816, address: "Av. Corrientes 1234, CABA", phone: "(011) 1234-5678", status: "abierta", distrito: "CABA" },
            { id: 2, name: "Farmacia San Telmo", lat: -34.6208, lng: -58.3731, address: "Defensa 1200, San Telmo, CABA", phone: "(011) 2345-6789", status: "abierta", distrito: "CABA" },
            { id: 3, name: "Farmacia Palermo", lat: -34.5889, lng: -58.3974, address: "Av. Santa Fe 2000, Palermo, CABA", phone: "(011) 3456-7890", status: "abierta", distrito: "CABA" },
            
            // La Plata
            { id: 4, name: "Farmacia del Pueblo", lat: -34.9214, lng: -57.9544, address: "Av. Rivadavia 5678, La Plata", phone: "(0221) 987-6543", status: "abierta", distrito: "La Plata" },
            { id: 5, name: "Farmacia Centro La Plata", lat: -34.9205, lng: -57.9536, address: "Calle 7 y 50, La Plata", phone: "(0221) 456-7890", status: "abierta", distrito: "La Plata" },
            { id: 6, name: "Farmacia Tolosa", lat: -34.9056, lng: -57.9567, address: "Av. 7 y 528, Tolosa, La Plata", phone: "(0221) 567-8901", status: "abierta", distrito: "La Plata" },
            
            // Mar del Plata
            { id: 7, name: "Farmacia San Martín", lat: -38.0023, lng: -57.5575, address: "Av. San Martín 9012, Mar del Plata", phone: "(0223) 456-7890", status: "cerrada", distrito: "Mar del Plata" },
            { id: 8, name: "Farmacia Centro MDP", lat: -37.9980, lng: -57.5500, address: "Av. Luro 2500, Mar del Plata", phone: "(0223) 567-8901", status: "abierta", distrito: "Mar del Plata" },
            { id: 9, name: "Farmacia Puerto", lat: -38.0080, lng: -57.5400, address: "Av. Colón 1800, Mar del Plata", phone: "(0223) 678-9012", status: "abierta", distrito: "Mar del Plata" },
            
            // Bahía Blanca
            { id: 10, name: "Farmacia Bahía", lat: -38.7183, lng: -62.2663, address: "Av. Colón 200, Bahía Blanca", phone: "(0291) 456-7890", status: "abierta", distrito: "Bahía Blanca" },
            { id: 11, name: "Farmacia Centro BB", lat: -38.7200, lng: -62.2600, address: "Zeballos 150, Bahía Blanca", phone: "(0291) 567-8901", status: "abierta", distrito: "Bahía Blanca" },
            
            // Tandil
            { id: 12, name: "Farmacia Tandil", lat: -37.3282, lng: -59.1369, address: "Av. Colón 800, Tandil", phone: "(0249) 456-7890", status: "abierta", distrito: "Tandil" },
            { id: 13, name: "Farmacia Centro Tandil", lat: -37.3300, lng: -59.1300, address: "9 de Julio 500, Tandil", phone: "(0249) 567-8901", status: "abierta", distrito: "Tandil" },
            
            // Pergamino
            { id: 14, name: "Farmacia Pergamino", lat: -33.8900, lng: -60.5700, address: "Av. de Mayo 1000, Pergamino", phone: "(02477) 456-789", status: "abierta", distrito: "Pergamino" },
            { id: 15, name: "Farmacia Centro Pergamino", lat: -33.8850, lng: -60.5650, address: "San Martín 500, Pergamino", phone: "(02477) 567-890", status: "abierta", distrito: "Pergamino" },
            
            // Junín
            { id: 16, name: "Farmacia Junín", lat: -34.5833, lng: -60.9500, address: "Av. San Martín 800, Junín", phone: "(0236) 456-7890", status: "abierta", distrito: "Junín" },
            { id: 17, name: "Farmacia Centro Junín", lat: -34.5800, lng: -60.9450, address: "Rivadavia 600, Junín", phone: "(0236) 567-8901", status: "abierta", distrito: "Junín" },
            
            // San Nicolás
            { id: 18, name: "Farmacia San Nicolás", lat: -33.3333, lng: -60.2167, address: "Av. Savio 1200, San Nicolás", phone: "(0336) 456-7890", status: "abierta", distrito: "San Nicolás" },
            { id: 19, name: "Farmacia Centro SN", lat: -33.3300, lng: -60.2100, address: "Mitre 800, San Nicolás", phone: "(0336) 567-8901", status: "abierta", distrito: "San Nicolás" },
            
            // Zárate
            { id: 20, name: "Farmacia Zárate", lat: -34.1000, lng: -59.0167, address: "Av. Mitre 1500, Zárate", phone: "(03487) 456-789", status: "abierta", distrito: "Zárate" },
            { id: 21, name: "Farmacia Centro Zárate", lat: -34.0950, lng: -59.0100, address: "San Martín 900, Zárate", phone: "(03487) 567-890", status: "abierta", distrito: "Zárate" },
            
            // Campana
            { id: 22, name: "Farmacia Campana", lat: -34.1667, lng: -58.9500, address: "Av. San Martín 1000, Campana", phone: "(03489) 456-789", status: "abierta", distrito: "Campana" },
            { id: 23, name: "Farmacia Centro Campana", lat: -34.1600, lng: -58.9450, address: "Rivadavia 700, Campana", phone: "(03489) 567-890", status: "abierta", distrito: "Campana" },
            
            // Pilar
            { id: 24, name: "Farmacia Pilar", lat: -34.4667, lng: -58.9000, address: "Av. Tratado del Pilar 2000, Pilar", phone: "(0230) 456-7890", status: "abierta", distrito: "Pilar" },
            { id: 25, name: "Farmacia Centro Pilar", lat: -34.4600, lng: -58.8950, address: "San Martín 800, Pilar", phone: "(0230) 567-8901", status: "abierta", distrito: "Pilar" },
            
            // Escobar
            { id: 26, name: "Farmacia Escobar", lat: -34.3500, lng: -58.7833, address: "Av. Tapia de Cruz 1500, Escobar", phone: "(03488) 456-789", status: "abierta", distrito: "Escobar" },
            { id: 27, name: "Farmacia Centro Escobar", lat: -34.3450, lng: -58.7800, address: "Belgrano 600, Escobar", phone: "(03488) 567-890", status: "abierta", distrito: "Escobar" },
            
            // Tigre
            { id: 28, name: "Farmacia Tigre", lat: -34.4167, lng: -58.5833, address: "Av. Liniers 1000, Tigre", phone: "(011) 4749-0000", status: "abierta", distrito: "Tigre" },
            { id: 29, name: "Farmacia Centro Tigre", lat: -34.4100, lng: -58.5800, address: "Av. Cazón 800, Tigre", phone: "(011) 4749-1111", status: "abierta", distrito: "Tigre" },
            
            // San Isidro
            { id: 30, name: "Farmacia San Isidro", lat: -34.4667, lng: -58.5167, address: "Av. del Libertador 2000, San Isidro", phone: "(011) 4743-0000", status: "abierta", distrito: "San Isidro" },
            { id: 31, name: "Farmacia Centro SI", lat: -34.4600, lng: -58.5100, address: "Av. Centenario 1500, San Isidro", phone: "(011) 4743-1111", status: "abierta", distrito: "San Isidro" },
            
            // Vicente López
            { id: 32, name: "Farmacia Vicente López", lat: -34.5167, lng: -58.4667, address: "Av. Maipú 3000, Vicente López", phone: "(011) 4795-0000", status: "abierta", distrito: "Vicente López" },
            { id: 33, name: "Farmacia Centro VL", lat: -34.5100, lng: -58.4600, address: "Av. Santa Fe 2500, Vicente López", phone: "(011) 4795-1111", status: "abierta", distrito: "Vicente López" },
            
            // San Fernando
            { id: 34, name: "Farmacia San Fernando", lat: -34.4500, lng: -58.5500, address: "Av. del Libertador 1500, San Fernando", phone: "(011) 4744-0000", status: "abierta", distrito: "San Fernando" },
            { id: 35, name: "Farmacia Centro SF", lat: -34.4450, lng: -58.5450, address: "Av. Constitución 1000, San Fernando", phone: "(011) 4744-1111", status: "abierta", distrito: "San Fernando" },
            
            // Luján
            { id: 36, name: "Farmacia Luján", lat: -34.5667, lng: -59.1167, address: "Av. Nuestra Señora de Luján 800, Luján", phone: "(02323) 456-789", status: "abierta", distrito: "Luján" },
            { id: 37, name: "Farmacia Centro Luján", lat: -34.5600, lng: -59.1100, address: "San Martín 600, Luján", phone: "(02323) 567-890", status: "abierta", distrito: "Luján" },
            
            // Mercedes
            { id: 38, name: "Farmacia Mercedes", lat: -34.6500, lng: -59.4333, address: "Av. Mitre 1000, Mercedes", phone: "(02324) 456-789", status: "abierta", distrito: "Mercedes" },
            { id: 39, name: "Farmacia Centro Mercedes", lat: -34.6450, lng: -59.4300, address: "Rivadavia 800, Mercedes", phone: "(02324) 567-890", status: "abierta", distrito: "Mercedes" },
            
            // Lobos
            { id: 40, name: "Farmacia Lobos", lat: -35.1833, lng: -59.1000, address: "Av. San Martín 600, Lobos", phone: "(02227) 456-789", status: "abierta", distrito: "Lobos" },
            { id: 41, name: "Farmacia Centro Lobos", lat: -35.1800, lng: -59.0950, address: "Rivadavia 400, Lobos", phone: "(02227) 567-890", status: "abierta", distrito: "Lobos" },
            
            // Chascomús
            { id: 42, name: "Farmacia Chascomús", lat: -35.5667, lng: -58.0167, address: "Av. Lastra 800, Chascomús", phone: "(02241) 456-789", status: "abierta", distrito: "Chascomús" },
            { id: 43, name: "Farmacia Centro Chascomús", lat: -35.5600, lng: -58.0100, address: "San Martín 600, Chascomús", phone: "(02241) 567-890", status: "abierta", distrito: "Chascomús" },
            
            // Dolores
            { id: 44, name: "Farmacia Dolores", lat: -36.3167, lng: -57.6833, address: "Av. San Martín 700, Dolores", phone: "(02245) 456-789", status: "abierta", distrito: "Dolores" },
            { id: 45, name: "Farmacia Centro Dolores", lat: -36.3100, lng: -57.6800, address: "Rivadavia 500, Dolores", phone: "(02245) 567-890", status: "abierta", distrito: "Dolores" },
            
            // Ayacucho
            { id: 46, name: "Farmacia Ayacucho", lat: -37.1500, lng: -58.4833, address: "Av. San Martín 600, Ayacucho", phone: "(02296) 456-789", status: "abierta", distrito: "Ayacucho" },
            { id: 47, name: "Farmacia Centro Ayacucho", lat: -37.1450, lng: -58.4800, address: "Rivadavia 400, Ayacucho", phone: "(02296) 567-890", status: "abierta", distrito: "Ayacucho" },
            
            // Balcarce
            { id: 48, name: "Farmacia Balcarce", lat: -37.8500, lng: -58.2500, address: "Av. San Martín 800, Balcarce", phone: "(02266) 456-789", status: "abierta", distrito: "Balcarce" },
            { id: 49, name: "Farmacia Centro Balcarce", lat: -37.8450, lng: -58.2450, address: "Rivadavia 600, Balcarce", phone: "(02266) 567-890", status: "abierta", distrito: "Balcarce" },
            
            // Necochea
            { id: 50, name: "Farmacia Necochea", lat: -38.5500, lng: -58.7333, address: "Av. 2 y 79, Necochea", phone: "(02262) 456-789", status: "abierta", distrito: "Necochea" },
            { id: 51, name: "Farmacia Centro Necochea", lat: -38.5450, lng: -58.7300, address: "Av. 2 y 83, Necochea", phone: "(02262) 567-890", status: "abierta", distrito: "Necochea" },
            
            // Tres Arroyos
            { id: 52, name: "Farmacia Tres Arroyos", lat: -38.3667, lng: -60.2833, address: "Av. San Martín 1000, Tres Arroyos", phone: "(02983) 456-789", status: "abierta", distrito: "Tres Arroyos" },
            { id: 53, name: "Farmacia Centro TA", lat: -38.3600, lng: -60.2800, address: "Rivadavia 800, Tres Arroyos", phone: "(02983) 567-890", status: "abierta", distrito: "Tres Arroyos" },
            
            // Coronel Suárez
            { id: 54, name: "Farmacia Coronel Suárez", lat: -37.4500, lng: -61.9167, address: "Av. San Martín 800, Coronel Suárez", phone: "(02926) 456-789", status: "abierta", distrito: "Coronel Suárez" },
            { id: 55, name: "Farmacia Centro CS", lat: -37.4450, lng: -61.9100, address: "Rivadavia 600, Coronel Suárez", phone: "(02926) 567-890", status: "abierta", distrito: "Coronel Suárez" },
            
            // Olavarría
            { id: 56, name: "Farmacia Olavarría", lat: -36.9000, lng: -60.3167, address: "Av. San Martín 1200, Olavarría", phone: "(02284) 456-789", status: "abierta", distrito: "Olavarría" },
            { id: 57, name: "Farmacia Centro Olavarría", lat: -36.8950, lng: -60.3100, address: "Rivadavia 900, Olavarría", phone: "(02284) 567-890", status: "abierta", distrito: "Olavarría" },
            
            // Azul
            { id: 58, name: "Farmacia Azul", lat: -36.7833, lng: -59.8667, address: "Av. San Martín 1000, Azul", phone: "(02281) 456-789", status: "abierta", distrito: "Azul" },
            { id: 59, name: "Farmacia Centro Azul", lat: -36.7800, lng: -59.8600, address: "Rivadavia 800, Azul", phone: "(02281) 567-890", status: "abierta", distrito: "Azul" },
            
            // Bolívar
            { id: 60, name: "Farmacia Bolívar", lat: -36.2333, lng: -61.1167, address: "Av. San Martín 800, Bolívar", phone: "(02314) 456-789", status: "abierta", distrito: "Bolívar" },
            { id: 61, name: "Farmacia Centro Bolívar", lat: -36.2300, lng: -61.1100, address: "Rivadavia 600, Bolívar", phone: "(02314) 567-890", status: "abierta", distrito: "Bolívar" },
            
            // Pehuajó
            { id: 62, name: "Farmacia Pehuajó", lat: -35.8167, lng: -61.9000, address: "Av. San Martín 700, Pehuajó", phone: "(02396) 456-789", status: "abierta", distrito: "Pehuajó" },
            { id: 63, name: "Farmacia Centro Pehuajó", lat: -35.8100, lng: -61.8950, address: "Rivadavia 500, Pehuajó", phone: "(02396) 567-890", status: "abierta", distrito: "Pehuajó" },
            
            // 9 de Julio
            { id: 64, name: "Farmacia 9 de Julio", lat: -35.4500, lng: -60.8833, address: "Av. San Martín 900, 9 de Julio", phone: "(02317) 456-789", status: "abierta", distrito: "9 de Julio" },
            { id: 65, name: "Farmacia Centro 9J", lat: -35.4450, lng: -60.8800, address: "Rivadavia 700, 9 de Julio", phone: "(02317) 567-890", status: "abierta", distrito: "9 de Julio" },
            
            // Bragado
            { id: 66, name: "Farmacia Bragado", lat: -35.1167, lng: -60.4833, address: "Av. San Martín 600, Bragado", phone: "(02342) 456-789", status: "abierta", distrito: "Bragado" },
            { id: 67, name: "Farmacia Centro Bragado", lat: -35.1100, lng: -60.4800, address: "Rivadavia 400, Bragado", phone: "(02342) 567-890", status: "abierta", distrito: "Bragado" },
            
            // Chivilcoy
            { id: 68, name: "Farmacia Chivilcoy", lat: -34.9000, lng: -60.0167, address: "Av. San Martín 800, Chivilcoy", phone: "(02346) 456-789", status: "abierta", distrito: "Chivilcoy" },
            { id: 69, name: "Farmacia Centro Chivilcoy", lat: -34.8950, lng: -60.0100, address: "Rivadavia 600, Chivilcoy", phone: "(02346) 567-890", status: "abierta", distrito: "Chivilcoy" },
            
            // Mercedes (Buenos Aires)
            { id: 70, name: "Farmacia Mercedes BA", lat: -34.6500, lng: -59.4333, address: "Av. Mitre 1000, Mercedes", phone: "(02324) 456-789", status: "abierta", distrito: "Mercedes" },
            
            // Luján (adicional)
            { id: 71, name: "Farmacia Luján Norte", lat: -34.5500, lng: -59.1000, address: "Ruta 5 Km 67, Luján", phone: "(02323) 567-890", status: "abierta", distrito: "Luján" },
            
            // San Antonio de Areco
            { id: 72, name: "Farmacia San Antonio", lat: -34.2500, lng: -59.4667, address: "Av. Arellano 800, San Antonio de Areco", phone: "(02326) 456-789", status: "abierta", distrito: "San Antonio de Areco" },
            
            // Exaltación de la Cruz
            { id: 73, name: "Farmacia Capilla del Señor", lat: -34.3000, lng: -59.1000, address: "Av. San Martín 600, Capilla del Señor", phone: "(02326) 567-890", status: "abierta", distrito: "Exaltación de la Cruz" },
            
            // General Rodríguez
            { id: 74, name: "Farmacia Gral. Rodríguez", lat: -34.6167, lng: -58.9500, address: "Av. San Martín 1000, General Rodríguez", phone: "(0237) 456-7890", status: "abierta", distrito: "General Rodríguez" },
            
            // Marcos Paz
            { id: 75, name: "Farmacia Marcos Paz", lat: -34.7833, lng: -58.8333, address: "Av. San Martín 800, Marcos Paz", phone: "(0220) 456-7890", status: "abierta", distrito: "Marcos Paz" },
            
            // General Las Heras
            { id: 76, name: "Farmacia Las Heras", lat: -34.9167, lng: -58.9500, address: "Av. San Martín 600, General Las Heras", phone: "(0220) 567-8901", status: "abierta", distrito: "General Las Heras" },
            
            // Navarro
            { id: 77, name: "Farmacia Navarro", lat: -35.0000, lng: -59.2667, address: "Av. San Martín 700, Navarro", phone: "(0227) 456-7890", status: "abierta", distrito: "Navarro" },
            
            // Saladillo
            { id: 78, name: "Farmacia Saladillo", lat: -35.6333, lng: -59.7667, address: "Av. San Martín 800, Saladillo", phone: "(02344) 456-789", status: "abierta", distrito: "Saladillo" },
            
            // Roque Pérez
            { id: 79, name: "Farmacia Roque Pérez", lat: -35.4000, lng: -59.3333, address: "Av. San Martín 600, Roque Pérez", phone: "(02227) 567-890", status: "abierta", distrito: "Roque Pérez" },
            
            // General Belgrano
            { id: 80, name: "Farmacia Gral. Belgrano", lat: -35.7667, lng: -58.5000, address: "Av. San Martín 700, General Belgrano", phone: "(02241) 567-890", status: "abierta", distrito: "General Belgrano" }
        ];

        function initializeLeafletMap() {
            if (map) {
                map.remove();
            }

            const mapElement = document.getElementById("map");
            if (mapElement) {
            mapElement.classList.add('map-loading');
            }

            // Pequeño delay para asegurar que el DOM esté listo
            setTimeout(() => {
                try {
                map = L.map('map').setView([-34.9214, -57.9544], 8);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Agregar marcadores de farmacias
                farmacias.forEach(farmacia => {
                    const color = farmacia.status === 'abierta' ? '#22c55e' : '#ef4444';
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });

                    const marker = L.marker([farmacia.lat, farmacia.lng], { icon: icon }).addTo(map);
                    
                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <h3 style="margin: 0 0 10px 0; color: #1f2937;">${farmacia.name}</h3>
                            <p style="margin: 5px 0; color: #6b7280;">📍 ${farmacia.address}</p>
                            <p style="margin: 5px 0; color: #6b7280;">📞 ${farmacia.phone}</p>
                            <p style="margin: 5px 0; color: #4b5563; font-size: 0.9rem;">
                                <strong>Distrito:</strong> ${farmacia.distrito}
                            </p>
                            <p style="margin: 5px 0;">
                                <span style="background: ${color}; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    ${farmacia.status === 'abierta' ? 'Abierta 24hs' : 'Cerrada'}
                                </span>
                            </p>
                            <div style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                                <button onclick="getDirections(${farmacia.lat}, ${farmacia.lng}, '${farmacia.name}')" 
                                        style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);"
                                        onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(59, 130, 246, 0.4)'"
                                        onmouseout="this.style.background='#3b82f6'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(59, 130, 246, 0.3)'">
                                    🗺️ Ir a esta Farmacia
                                </button>
                                <button onclick="verMedicamentos(${farmacia.id}, '${encodeURIComponent(farmacia.name)}')" 
                                        style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);"
                                        onmouseover="this.style.background='#059669'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(16, 185, 129, 0.4)'"
                                        onmouseout="this.style.background='#10b981'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(16, 185, 129, 0.3)'">
                                    💊 Ver medicamentos
                                </button>
                            </div>
                        </div>
                    `);
                });

                    if (mapElement) {
                mapElement.classList.remove('map-loading');
                    }
                map.invalidateSize();
                } catch (error) {
                    console.error('Error inicializando el mapa:', error);
                    if (mapElement) {
                        mapElement.classList.remove('map-loading');
                    }
                    alert('Error al cargar el mapa. Por favor, recarga la página.');
                }
            }, 1000);
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        
                        console.log('Ubicación obtenida desde "Usar mi ubicación":', userLat, userLng);
                        
                        if (userLocationMarker) {
                            map.removeLayer(userLocationMarker);
                        }
                        
                        const userIcon = L.divIcon({
                            className: 'user-location-icon',
                            html: `<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        });
                        
                        userLocationMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                        userLocationMarker.bindPopup('<b>📍 Tu ubicación actual</b>').openPopup();
                        
                        map.setView([userLat, userLng], 15);
                        
                        // Activar detección automática de farmacias después de dar permiso
                        activatePharmacyLocationTracking();
                        
                        // Mostrar notificación
                        showLocationNotification('📍 Ubicación detectada. Seguimiento de farmacias activado. Puedes detenerlo con el botón "🛑 Detener Seguimiento".');
                    },
                    error => {
                        console.error('Error obteniendo ubicación:', error);
                        let errorMessage = 'No se pudo obtener tu ubicación. ';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Permisos de ubicación denegados.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Ubicación no disponible.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Tiempo de espera agotado.';
                                break;
                            default:
                                errorMessage += 'Error desconocido.';
                                break;
                        }
                        
                        alert(errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 30000
                    }
                );
            } else {
                alert("Geolocalización no soportada por tu navegador.");
            }
        }

        function centerMap() {
            if (userLocationMarker) {
                const latLng = userLocationMarker.getLatLng();
                map.setView([latLng.lat, latLng.lng], 12);
            } else {
                getLocation();
            }
        }

        // Función segura que busca la farmacia por ID
        function getDirectionsSafe(farmaciaId) {
            console.log('getDirectionsSafe llamada con ID:', farmaciaId);
            console.log('Array farmacias disponible:', farmacias ? 'Sí' : 'No');
            console.log('Total de farmacias:', farmacias ? farmacias.length : 0);
            
            // Verificar que el array esté disponible
            if (!farmacias || farmacias.length === 0) {
                console.error('Array de farmacias no disponible');
                alert('Error: No se pudieron cargar las farmacias. Por favor, recarga la página.');
                return;
            }
            
            // Buscar la farmacia en el array
            const farmacia = farmacias.find(f => f.id === farmaciaId);
            
            if (!farmacia) {
                console.error('Farmacia no encontrada con ID:', farmaciaId);
                console.log('Farmacias disponibles:', farmacias.map(f => ({ id: f.id, name: f.name })));
                alert(`Error: No se pudo encontrar la farmacia con ID ${farmaciaId}\n\nFarmacias disponibles: ${farmacias.length}`);
                return;
            }
            
            console.log('Farmacia encontrada:', farmacia);
            
            // Llamar a la función original con los datos correctos
            getDirections(farmacia.lat, farmacia.lng, farmacia.name);
        }
        
        function getDirections(lat, lon, name) {
            // Verificar que tengamos los parámetros básicos
            if (!lat || !lon || !name) {
                alert('Error: Faltan datos de la farmacia');
                return;
            }
            
            // Convertir a números si vienen como strings
            const latNum = parseFloat(lat);
            const lonNum = parseFloat(lon);
            
            // Verificar que sean números válidos
            if (isNaN(latNum) || isNaN(lonNum)) {
                alert(`Error: Coordenadas inválidas\nLat: ${lat}\nLon: ${lon}`);
                return;
            }
            
            // Crear URL de Google Maps
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latNum},${lonNum}&travelmode=driving`;
            
            // Intentar abrir Google Maps
            try {
                const newWindow = window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
                
                if (!newWindow) {
                    // Si no se puede abrir, mostrar opciones alternativas
                    const choice = confirm(`No se pudo abrir Google Maps automáticamente.\n\n¿Quieres abrir Google Maps manualmente?\n\nCoordenadas: ${latNum}, ${lonNum}`);
                    
                    if (choice) {
                        window.open(`https://maps.google.com/maps?q=${latNum},${lonNum}`, '_blank');
                    }
                }
            } catch (error) {
                // Si hay error, mostrar coordenadas para copiar
                alert(`Error al abrir Google Maps.\n\nCoordenadas de ${name}:\n${latNum}, ${lonNum}\n\nPuedes copiar estas coordenadas y pegarlas en Google Maps.`);
            }
        }
        
        function showFallbackOptions(lat, lon, name) {
            const options = `
                🗺️ Opciones para llegar a ${name}:
                
                1. 📱 Google Maps: https://maps.google.com/maps?q=${lat},${lon}
                2. 🍎 Apple Maps: https://maps.apple.com/?q=${lat},${lon}
                3. 📋 Coordenadas: ${lat}, ${lon}
                
                ¿Qué opción prefieres?
            `;
            
            const choice = confirm(options + '\n\n¿Quieres abrir Google Maps en una nueva pestaña?');
            
            if (choice) {
                // Intentar abrir Google Maps directamente
                window.open(`https://maps.google.com/maps?q=${lat},${lon}`, '_blank');
            } else {
                // Mostrar coordenadas para copiar
                if (confirm('¿Quieres copiar las coordenadas al portapapeles?')) {
                    copyToClipboard(`${lat}, ${lon}`);
                }
            }
        }
        
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                // Usar la API moderna de clipboard
                navigator.clipboard.writeText(text).then(() => {
                    alert('✅ Coordenadas copiadas al portapapeles: ' + text);
                }).catch(() => {
                    fallbackCopyToClipboard(text);
                });
            } else {
                // Fallback para navegadores más antiguos
                fallbackCopyToClipboard(text);
            }
        }
        
        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                alert('✅ Coordenadas copiadas al portapapeles: ' + text);
            } catch (err) {
                alert('❌ No se pudo copiar automáticamente. Coordenadas: ' + text);
            }
            
            document.body.removeChild(textArea);
        }

        function verMedicamentos(farmaciaId, farmaciaName) {
            <?php if (!isset($_SESSION['user_id'])): ?>
                if (confirm('Para ver los medicamentos disponibles necesitas iniciar sesión.\n\n¿Quieres registrarte o iniciar sesión?')) {
                    const opcion = confirm('¿Ya tienes cuenta?\n\n- OK = Iniciar Sesión\n- Cancelar = Registrarse');
                    if (opcion) {
                        window.location.href = 'login.php';
                    } else {
                        window.location.href = 'register.php';
                    }
                }
            <?php else: ?>
                window.location.href = `medicamentos.php?farmacia=${farmaciaId}`;
            <?php endif; ?>
        }

        function checkAuthAndGetLocation() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                if (confirm('Para usar tu ubicación y encontrar farmacias cercanas necesitas iniciar sesión.\n\n¿Quieres registrarte o iniciar sesión?')) {
                    const opcion = confirm('¿Ya tienes cuenta?\n\n- OK = Iniciar Sesión\n- Cancelar = Registrarse');
                    if (opcion) {
                        window.location.href = 'login.php';
                    } else {
                        window.location.href = 'register.php';
                    }
                }
            <?php else: ?>
                getLocation();
            <?php endif; ?>
        }

        function checkAuthAndCenterMap() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                if (confirm('Para centrar el mapa en tu ubicación necesitas iniciar sesión.\n\n¿Quieres registrarte o iniciar sesión?')) {
                    const opcion = confirm('¿Ya tienes cuenta?\n\n- OK = Iniciar Sesión\n- Cancelar = Registrarse');
                    if (opcion) {
                        window.location.href = 'login.php';
                    } else {
                        window.location.href = 'register.php';
                    }
                }
            <?php else: ?>
                centerMap();
            <?php endif; ?>
        }

        // Funciones para manejar el mapa
        function loadMapWithLocation() {
            console.log('Cargando mapa con ubicación...');
            
            // Ocultar placeholder y mostrar mapa
            document.getElementById('map-placeholder').classList.add('hidden');
            document.getElementById('map-container').classList.remove('hidden');
            document.getElementById('load-buttons').classList.add('hidden');
            
            // Mostrar información del mapa inmediatamente
            const mapInfo = document.getElementById('map-info');
            if (mapInfo) {
                mapInfo.classList.remove('hidden');
                console.log('Información del mapa mostrada');
                
                // Asegurar que se mantenga visible después de que Leaflet se inicialice
                setTimeout(() => {
                    mapInfo.classList.remove('hidden');
                    mapInfo.style.display = 'block';
                    mapInfo.style.visibility = 'visible';
                    mapInfo.style.opacity = '1';
                    console.log('Información del mapa forzada a ser visible');
                }, 2000);
            } else {
                console.log('No se encontró el elemento map-info');
            }
            
            document.getElementById('map-controls-section').classList.remove('hidden');
            
            // Ajustar espaciado de farmacias cuando aparecen los controles
            document.getElementById('pharmacies').style.marginTop = '0rem';
            
            // Inicializar mapa
            initializeLeafletMap();
            
            // Solicitar ubicación del usuario
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        
                        console.log('Ubicación obtenida al cargar mapa:', userLat, userLng);
                        
                        // Esperar a que el mapa se inicialice completamente
                        setTimeout(() => {
                            if (userLocationMarker) {
                                map.removeLayer(userLocationMarker);
                            }
                            
                            const userIcon = L.divIcon({
                                className: 'user-location-icon',
                                html: `<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            });
                            
                            userLocationMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                            userLocationMarker.bindPopup('<b>📍 Tu ubicación actual</b>').openPopup();
                            
                            map.setView([userLat, userLng], 15);
                            
                        // Mostrar notificación
                        showMapLoadedNotification('📍 Mapa cargado y centrado en tu ubicación');
                        
                        // Activar detección automática de farmacias después de dar permiso
                        activatePharmacyLocationTracking(true);
                        
                        console.log('Mapa cargado y centrado en ubicación del usuario');
                        }, 1500); // Esperar 1.5 segundos para que el mapa se inicialice
                    },
                    error => {
                        console.error('Error obteniendo ubicación:', error);
                        let errorMessage = 'No se pudo obtener tu ubicación. ';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Permisos de ubicación denegados.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Ubicación no disponible.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Tiempo de espera agotado.';
                                break;
                            default:
                                errorMessage += 'Error desconocido.';
                                break;
                        }
                        
                        alert(errorMessage + ' El mapa se cargará en la vista general.');
                        setTimeout(() => {
                            map.setView([-34.9214, -57.9544], 8);
                            showMapLoadedNotification('🗺️ Mapa cargado en vista general');
                        }, 1500);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 30000
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización. El mapa se cargará en la vista general.');
                setTimeout(() => {
                    map.setView([-34.9214, -57.9544], 8);
                    showMapLoadedNotification('🗺️ Mapa cargado en vista general');
                }, 1500);
            }
        }
        
        function loadMapOnly() {
            // Ocultar placeholder y mostrar mapa
            document.getElementById('map-placeholder').classList.add('hidden');
            document.getElementById('map-container').classList.remove('hidden');
            document.getElementById('load-buttons').classList.add('hidden');
            // Mostrar información del mapa inmediatamente
            const mapInfo = document.getElementById('map-info');
            if (mapInfo) {
                mapInfo.classList.remove('hidden');
                console.log('Información del mapa mostrada');
                
                // Asegurar que se mantenga visible después de que Leaflet se inicialice
                setTimeout(() => {
                    mapInfo.classList.remove('hidden');
                    mapInfo.style.display = 'block';
                    mapInfo.style.visibility = 'visible';
                    mapInfo.style.opacity = '1';
                    console.log('Información del mapa forzada a ser visible');
                }, 2000);
            } else {
                console.log('No se encontró el elemento map-info');
            }
            
            document.getElementById('map-controls-section').classList.remove('hidden');
            
            // Ajustar espaciado de farmacias cuando aparecen los controles
            document.getElementById('pharmacies').style.marginTop = '0rem';
            
            // Inicializar mapa sin ubicación
            initializeLeafletMap();
            map.setView([-34.9214, -57.9544], 8);
        }
        
        function centerOnUserLocation() {
            if (!map) {
                alert('Primero carga el mapa haciendo click en "Cargar Mapa"');
                return;
            }
            
            console.log('Centrando en ubicación del usuario...');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        
                        console.log('Ubicación obtenida:', userLat, userLng);
                        
                        // Remover marcador anterior si existe
                        if (userLocationMarker) {
                            map.removeLayer(userLocationMarker);
                        }
                        
                        // Crear icono para la ubicación del usuario
                        const userIcon = L.divIcon({
                            className: 'user-location-icon',
                            html: `<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        });
                        
                        // Agregar marcador de ubicación del usuario
                        userLocationMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                        userLocationMarker.bindPopup('<b>📍 Tu ubicación actual</b>').openPopup();
                        
                        // Centrar el mapa en la ubicación del usuario
                        map.setView([userLat, userLng], 15);
                        
                        // Activar detección automática de farmacias después de dar permiso
                        activatePharmacyLocationTracking();
                        
                        console.log('Mapa centrado en ubicación del usuario');
                    },
                    error => {
                        console.error('Error obteniendo ubicación:', error);
                        let errorMessage = 'No se pudo obtener tu ubicación. ';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Permisos de ubicación denegados.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Ubicación no disponible.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Tiempo de espera agotado.';
                                break;
                            default:
                                errorMessage += 'Error desconocido.';
                                break;
                        }
                        
                        alert(errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 30000
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        }
        
        function refreshMap() {
            if (!map) {
                alert('Primero carga el mapa haciendo click en "Cargar Mapa"');
                return;
            }
            
            console.log('Actualizando mapa...');
            
            // Primero obtener la ubicación del usuario
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        
                        console.log('Ubicación obtenida para actualizar mapa:', userLat, userLng);
                        
                        // Actualizar el mapa
                        initializeLeafletMap();
                        
                        // Esperar a que el mapa se cargue completamente y luego centrar
                        setTimeout(() => {
                            if (userLocationMarker) {
                                map.removeLayer(userLocationMarker);
                            }
                            
                            const userIcon = L.divIcon({
                                className: 'user-location-icon',
                                html: `<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            });
                            
                            userLocationMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                            userLocationMarker.bindPopup('<b>📍 Tu ubicación actual</b>').openPopup();
                            
                            map.setView([userLat, userLng], 15);
                            
                            // Activar detección automática de farmacias después de dar permiso
                            activatePharmacyLocationTracking(true);
                            
                            console.log('Mapa actualizado y centrado en ubicación del usuario');
                        }, 1500); // Esperar 1.5 segundos para que el mapa se cargue
                    },
                    error => {
                        console.error('Error obteniendo ubicación:', error);
                        let errorMessage = 'No se pudo obtener tu ubicación. ';
                        
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Permisos de ubicación denegados.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Ubicación no disponible.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Tiempo de espera agotado.';
                                break;
                            default:
                                errorMessage += 'Error desconocido.';
                                break;
                        }
                        
                        alert(errorMessage);
                        
                        // Si no se puede obtener ubicación, solo actualizar el mapa
                        initializeLeafletMap();
                        setTimeout(() => {
                            map.setView([-34.9214, -57.9544], 8);
                        }, 1500);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 30000
                    }
                );
            } else {
                // Si no hay geolocalización, solo actualizar el mapa
                initializeLeafletMap();
                setTimeout(() => {
                    map.setView([-34.9214, -57.9544], 8);
                }, 1500);
            }
        }
        
        function resetMapView() {
            if (!map) {
                alert('Primero carga el mapa');
                return;
            }
            map.setView([-34.9214, -57.9544], 8);
        }
        
        function showMapLoadedNotification(message = '¡Mapa Cargado!') {
            // Contar notificaciones existentes para apilarlas
            const existingNotifications = document.querySelectorAll('.notification-stack');
            const stackCount = existingNotifications.length;
            
            // Crear notificación bonita
            const notification = document.createElement('div');
            notification.className = 'notification-stack fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-500';
            
            // Calcular la posición vertical basada en el número de notificaciones existentes
            const bottomOffset = 20 + (stackCount * 100); // 20px base + 100px por cada notificación
            notification.style.bottom = `${bottomOffset}px`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">🗺️</div>
                    <div>
                        <div class="font-bold">${message}</div>
                        <div class="text-sm opacity-90">Farmacias de turno disponibles</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200" style="background: none !important; border: none !important; padding: 0 !important; box-shadow: none !important; outline: none !important;">✕</button>
                </div>
                <div class="progress-bar-container mt-2 h-1 bg-white bg-opacity-30 rounded-full overflow-hidden">
                    <div class="progress-bar long-duration h-full bg-white rounded-full" style="width: 100%;"></div>
                </div>
            `;
            
            // Agregar al body
            document.body.appendChild(notification);
            
            // Animar entrada
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-ocultar después de 10 segundos
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
            }, 500);
            }, 10000);
        }

        // Manejar redimensionamiento de ventana
        window.addEventListener('resize', () => {
            if (map) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            }
        });

        // Menú móvil y detección automática de ubicación
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // NO detectar ubicación automáticamente al cargar la página
            // Solo se detectará cuando el usuario haga click en uno de los botones específicos
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

        // Función para cerrar la información del mapa
        function closeMapInfo() {
            const mapInfo = document.getElementById('map-info');
            if (mapInfo) {
                mapInfo.classList.add('hidden');
                console.log('Información del mapa cerrada');
            }
        }

        // Función para ir a una farmacia específica
        function irAFarmacia(farmaciaId, farmaciaName) {
            // Coordenadas de ejemplo para cada farmacia (en un sistema real, esto vendría de una base de datos)
            const farmaciasCoords = {
                1: { lat: -34.6037, lng: -58.3816, address: "Av. Corrientes 1234, CABA" }, // Farmacia Central
                2: { lat: -34.9214, lng: -57.9544, address: "Av. Rivadavia 5678, La Plata" }, // Farmacia del Pueblo
                8: { lat: -38.0023, lng: -57.5575, address: "Av. Luro 2500, Mar del Plata" }, // Farmacia Centro MDP
                10: { lat: -38.7183, lng: -62.2663, address: "Av. Colón 200, Bahía Blanca" }, // Farmacia Bahía
                12: { lat: -37.3217, lng: -59.1332, address: "Av. Colón 800, Tandil" }, // Farmacia Tandil
                24: { lat: -34.4734, lng: -58.9114, address: "Av. Tratado del Pilar 2000, Pilar" } // Farmacia Pilar
            };

            const farmacia = farmaciasCoords[farmaciaId];
            if (!farmacia) {
                alert(`No se encontraron coordenadas para ${farmaciaName}`);
                return;
            }

            // Verificar si la geolocalización está disponible
            if (!navigator.geolocation) {
                // Si no hay geolocalización, abrir con destino solamente
                const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${farmacia.lat},${farmacia.lng}&travelmode=driving`;
                window.open(googleMapsUrl, '_blank');
                showNotification(`🗺️ Abriendo ruta a ${farmaciaName} en Google Maps`);
                return;
            }

            // Mostrar notificación de carga
            showNotification(`📍 Obteniendo tu ubicación para calcular la ruta a ${farmaciaName}...`);

            // Obtener ubicación actual del usuario
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    // Crear URL de Google Maps con origen (ubicación del usuario) y destino (farmacia)
                    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${farmacia.lat},${farmacia.lng}&travelmode=driving`;
                    
                    // Abrir en nueva pestaña
                    window.open(googleMapsUrl, '_blank');
                    
                    // Mostrar notificación de éxito
                    showNotification(`🗺️ Ruta calculada desde tu ubicación a ${farmaciaName}`);
                },
                function(error) {
                    console.error('Error al obtener ubicación:', error);
                    
                    // Si no se puede obtener la ubicación, abrir con destino solamente
                    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${farmacia.lat},${farmacia.lng}&travelmode=driving`;
                    window.open(googleMapsUrl, '_blank');
                    
                    // Mostrar notificación informativa
                    showNotification(`🗺️ Abriendo ruta a ${farmaciaName} (permite acceso a ubicación para rutas personalizadas)`);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000 // 5 minutos
                }
            );
        }

        // Función para calcular distancia entre dos puntos (fórmula de Haversine)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radio de la Tierra en kilómetros
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Variable para controlar si el tracking está activo
        let pharmacyLocationTrackingActive = false;
        let locationWatchId = null;
        let locationIntervalId = null;

        // Función para activar el tracking de farmacias (solo después de dar permiso)
        function activatePharmacyLocationTracking(showNotification = false) {
            if (pharmacyLocationTrackingActive) {
                return; // Ya está activo
            }
            
            pharmacyLocationTrackingActive = true;
            console.log('Tracking de farmacias activado');
            
            // Mostrar botón de detener seguimiento
            const stopBtn = document.getElementById('stop-tracking-btn');
            if (stopBtn) {
                stopBtn.classList.remove('hidden');
            }
            
            // Detectar ubicación inmediatamente
            detectUserLocationAndUpdatePharmacies(showNotification);
            
            // Actualizar ubicación cada 30 segundos
            locationIntervalId = setInterval(() => detectUserLocationAndUpdatePharmacies(false), 30000);
            
            // Detectar cambios de ubicación cuando el usuario se mueva
            if (navigator.geolocation) {
                locationWatchId = navigator.geolocation.watchPosition(
                    function(position) {
                        // Solo actualizar si la ubicación cambió significativamente (más de 100 metros)
                        if (window.lastKnownPosition) {
                            const distance = calculateDistance(
                                window.lastKnownPosition.coords.latitude,
                                window.lastKnownPosition.coords.longitude,
                                position.coords.latitude,
                                position.coords.longitude
                            );
                            
                            if (distance > 0.1) { // 100 metros
                                console.log('Ubicación cambió significativamente, actualizando farmacias...');
                                detectUserLocationAndUpdatePharmacies(false);
                            }
                        }
                        window.lastKnownPosition = position;
                    },
                    function(error) {
                        console.log('Error en watchPosition:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 30000
                    }
                );
            }
        }

        // Función para desactivar el tracking de farmacias
        function deactivatePharmacyLocationTracking() {
            if (!pharmacyLocationTrackingActive) {
                return; // Ya está desactivado
            }
            
            pharmacyLocationTrackingActive = false;
            console.log('Tracking de farmacias desactivado');
            
            // Ocultar botón de detener seguimiento
            const stopBtn = document.getElementById('stop-tracking-btn');
            if (stopBtn) {
                stopBtn.classList.add('hidden');
            }
            
            // Limpiar intervalos y watchers
            if (locationIntervalId) {
                clearInterval(locationIntervalId);
                locationIntervalId = null;
            }
            
            if (locationWatchId && navigator.geolocation) {
                navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = null;
            }
            
            // Mostrar notificación
            showLocationNotification('🛑 Seguimiento de ubicación detenido');
        }

        // Función para detectar ubicación automáticamente al cargar la página
        function detectUserLocationAndUpdatePharmacies(showNotification = false) {
            if (!navigator.geolocation) {
                console.log('La geolocalización no está disponible en este navegador.');
                return;
            }

            // Mostrar indicador de carga
            const title = document.getElementById('pharmacies-title');
            const indicator = document.getElementById('location-indicator');
            
            if (title) {
                title.innerHTML = '🔄 Detectando ubicación...';
            }
            if (indicator) {
                indicator.classList.remove('hidden');
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    // Calcular distancias y ordenar farmacias
                    const farmaciasConDistancia = farmacias.map(farmacia => ({
                        ...farmacia,
                        distance: calculateDistance(userLat, userLng, farmacia.lat, farmacia.lng)
                    }));

                    // Ordenar por distancia (más cercanas primero)
                    farmaciasConDistancia.sort((a, b) => a.distance - b.distance);

                    // Mostrar solo las 6 más cercanas
                    const farmaciasCercanas = farmaciasConDistancia.slice(0, 6);

                    // Actualizar la interfaz
                    updatePharmaciesGrid(farmaciasCercanas);

                    // Actualizar título y ocultar indicador
                    if (title) {
                        title.innerHTML = '📍 Farmacias de Turno Cercanas';
                    }
                    if (indicator) {
                        indicator.classList.add('hidden');
                    }

                    // Mostrar notificación solo si se solicita
                    if (showNotification) {
                        showLocationNotification(`📍 Ubicación detectada. ${farmaciasCercanas.length} farmacias cercanas. Seguimiento activado.`);
                    }
                    
                    // Activar detección automática de farmacias después de dar permiso
                    activatePharmacyLocationTracking();
                },
                function(error) {
                    console.error('Error al obtener ubicación:', error);
                    
                    // Restaurar título original y ocultar indicador
                    if (title) {
                        title.innerHTML = 'Farmacias de Turno Cercanas';
                    }
                    if (indicator) {
                        indicator.classList.add('hidden');
                    }
                    
                    // Mostrar mensaje informativo
                    showLocationNotification('⚠️ No se pudo detectar tu ubicación. Usa el botón "Actualizar por Ubicación" para intentar nuevamente.');
                }
            );
        }

        // Función para actualizar farmacias por ubicación (botón manual)
        function updatePharmaciesByLocation() {
            if (!navigator.geolocation) {
                alert('La geolocalización no está disponible en este navegador.');
                return;
            }

            // Mostrar indicador de carga
            const button = document.querySelector('button[onclick="updatePharmaciesByLocation()"]');
            if (!button) {
                console.error('No se encontró el botón de actualizar ubicación');
                return;
            }
            const originalText = button.innerHTML;
            button.innerHTML = '🔄 Detectando ubicación...';
            button.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    // Calcular distancias y ordenar farmacias
                    const farmaciasConDistancia = farmacias.map(farmacia => ({
                        ...farmacia,
                        distance: calculateDistance(userLat, userLng, farmacia.lat, farmacia.lng)
                    }));

                    // Ordenar por distancia (más cercanas primero)
                    farmaciasConDistancia.sort((a, b) => a.distance - b.distance);

                    // Mostrar solo las 6 más cercanas
                    const farmaciasCercanas = farmaciasConDistancia.slice(0, 6);

                    // Actualizar la interfaz
                    updatePharmaciesGrid(farmaciasCercanas);

                    // Restaurar botón
                    button.innerHTML = originalText;
                    button.disabled = false;

                    // Mostrar notificación específica para este botón
                    showLocationNotification(`📍 Ubicación detectada. ${farmaciasCercanas.length} farmacias cercanas. Seguimiento activado.`);
                },
                function(error) {
                    console.error('Error al obtener ubicación:', error);
                    alert('No se pudo obtener tu ubicación. Por favor, permite el acceso a la ubicación o usa el mapa para seleccionar manualmente.');
                    
                    // Restaurar botón
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            );
        }

        // Función para actualizar la grilla de farmacias
        function updatePharmaciesGrid(farmaciasList) {
            const grid = document.getElementById('pharmacies-grid');
            if (!grid) return;

            grid.innerHTML = '';

            farmaciasList.forEach(farmacia => {
                const farmaciaCard = document.createElement('div');
                farmaciaCard.className = 'bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200';
                
                const statusColor = farmacia.status === 'abierta' ? 'green' : 'red';
                const statusText = farmacia.status === 'abierta' ? 'Abierta 24hs' : 'Cerrada';
                const statusIcon = farmacia.status === 'abierta' ? '✅' : '❌';
                
                farmaciaCard.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-semibold text-gray-800">${farmacia.name}</h3>
                        <span class="text-sm text-blue-600 font-medium">${farmacia.distance.toFixed(1)} km</span>
                    </div>
                    <p class="text-gray-600 mb-2">📍 ${farmacia.address}</p>
                    <p class="text-gray-600 mb-2">📞 ${farmacia.phone}</p>
                    <div class="flex items-center mb-4">
                        <div class="w-3 h-3 bg-${statusColor}-500 rounded-full mr-2"></div>
                        <span class="text-${statusColor}-600 font-semibold">${statusIcon} ${statusText}</span>
                    </div>
                    <button onclick="verMedicamentos(${farmacia.id}, '${farmacia.name}')" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 mb-2">
                        💊 Ver medicamentos
                    </button>
                    <button onclick="irAFarmacia(${farmacia.id}, '${farmacia.name}')" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">
                        🏥 Ir a esta farmacia
                    </button>
                `;
                
                grid.appendChild(farmaciaCard);
            });
        }

        // Función para mostrar notificación de ubicación
        function showLocationNotification(message) {
            // Contar notificaciones existentes para apilarlas
            const existingNotifications = document.querySelectorAll('.notification-stack');
            const stackCount = existingNotifications.length;
            
            const notification = document.createElement('div');
            notification.className = 'notification-stack fixed right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-500';
            
            // Calcular la posición vertical basada en el número de notificaciones existentes
            const bottomOffset = 20 + (stackCount * 100); // 20px base + 100px por cada notificación
            notification.style.bottom = `${bottomOffset}px`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200" style="background: none !important; border: none !important; padding: 0 !important; box-shadow: none !important; outline: none !important;">✕</button>
                </div>
                <div class="progress-bar-container mt-2 h-1 bg-white bg-opacity-30 rounded-full overflow-hidden">
                    <div class="progress-bar h-full bg-white rounded-full" style="width: 100%;"></div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>
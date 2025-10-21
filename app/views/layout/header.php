<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'FarmaXpress' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- JavaScript -->
    <script src="/js/app.js"></script>
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50">
    <header class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-xl font-bold">FarmaXpress</a>
                    <nav class="hidden md:flex space-x-4">
                        <a href="/" class="hover:text-blue-200">Inicio</a>
                        <a href="/medicamentos" class="hover:text-blue-200">Medicamentos</a>
                        <a href="/farmacias" class="hover:text-blue-200">Farmacias</a>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="text-blue-200">Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="/dashboard" class="bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded">Dashboard</a>
                        <a href="/logout" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="/login" class="bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded">Iniciar Sesión</a>
                        <a href="/register" class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main class="container mx-auto px-4 py-6">


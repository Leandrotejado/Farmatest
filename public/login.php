<?php
session_start();
require '../config/db.php';
require '../app/SecurityHelper.php';

// Función para obtener URL de OAuth
function getOAuthUrl($provider) {
    try {
        require_once '../services/MultiOAuthService.php';
        $oauthService = new MultiOAuthService();
        $state = $oauthService->generateState();
        $_SESSION['oauth_state'] = $state;
        return $oauthService->getAuthUrl($provider, $state);
    } catch (Exception $e) {
        return '#';
    }
}

$error = '';

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

if ($_POST && isset($_POST['login'])) {
    // Verificar rate limiting
    if (!SecurityHelper::checkRateLimit('login', 5, 300)) {
        $error = 'Demasiados intentos de login. Intenta nuevamente en 5 minutos.';
        SecurityHelper::logSecurity('LOGIN_RATE_LIMIT', 'Too many attempts');
    } else {
        $email = SecurityHelper::sanitize($_POST['email']);
        $password = $_POST['password'];
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            $error = 'Email y contraseña son obligatorios';
        } elseif (!SecurityHelper::validateEmail($email)) {
            $error = 'Email inválido';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, nombre, email, password, rol, email_verified FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Verificar si el email está verificado
                    if ($user['email_verified'] == 0) {
                        $error = 'Tu cuenta no está verificada. Revisa tu email para obtener el código de verificación o <a href="resend-verification.php" class="text-blue-600 hover:underline">solicita uno nuevo</a>.';
                    } else {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['usuario_nombre'] = $user['nombre'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['rol'] = $user['rol'];
                        $_SESSION['email_verified'] = $user['email_verified'];
                        
                        SecurityHelper::logSecurity('LOGIN_SUCCESS', 'User: ' . $email);
                        
                        // Redirigir según el rol
                        if ($user['rol'] == 'admin') {
                            header('Location: ../admin/dashboard.php');
                        } else {
                            header('Location: dashboard.php');
                        }
                        exit();
                    }
                } else {
                    $error = 'Email o contraseña incorrectos';
                    SecurityHelper::logSecurity('LOGIN_FAILED', 'Invalid credentials for: ' . $email);
                }
            } catch (Exception $e) {
                $error = 'Error del sistema. Intenta nuevamente.';
                SecurityHelper::logSecurity('LOGIN_ERROR', 'Database error: ' . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FarmaXpress</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="css/dark-theme.css">
    <!-- Dark Mode Script -->
    <script src="assets/dark-mode.js"></script>
    <style>
        /* Eliminar subrayados de todos los enlaces */
        a {
            text-decoration: none !important;
        }
        
        a:hover {
            text-decoration: none !important;
        }
        
        a:focus {
            text-decoration: none !important;
        }
        
        a:visited {
            text-decoration: none !important;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">FarmaXpress</h1>
            <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Iniciar Sesión</h2>
            <p class="text-center text-gray-600 mb-4">Accede a tu cuenta para servicios personalizados</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Botones de Login Social -->
        <div class="mb-6">
            <div class="text-center mb-4">
                <span class="text-gray-500 text-sm">O inicia sesión con:</span>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                <!-- Google -->
                <a href="<?php echo getOAuthUrl('google'); ?>" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continuar con Google
                </a>
                
                <!-- Apple -->
                <a href="<?php echo getOAuthUrl('apple'); ?>" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-black text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    Continuar con Apple
                </a>
                
                <!-- Microsoft -->
                <a href="<?php echo getOAuthUrl('microsoft'); ?>" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#F25022" d="M1 1h10v10H1z"/>
                        <path fill="#00A4EF" d="M13 1h10v10H13z"/>
                        <path fill="#7FBA00" d="M1 13h10v10H1z"/>
                        <path fill="#FFB900" d="M13 13h10v10H13z"/>
                    </svg>
                    Continuar con Microsoft
                </a>
            </div>
            
            <div class="mt-4 text-center">
                <span class="text-gray-500 text-sm">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</span>
            </div>
        </div>
        
        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="mt-1 w-full p-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="togglePassword('password')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 border-none bg-transparent">
                        <span id="password-icon" style="font-size: 18px;">👁️</span>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-600">¿No tienes cuenta? <a href="register.php" class="text-blue-600 hover:underline">Registrarse</a></p>
            <p class="text-gray-600 mt-2"><a href="index.php" class="text-blue-600 hover:underline">← Volver al inicio</a></p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>

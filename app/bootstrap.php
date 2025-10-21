<?php
/**
 * Bootstrap de la aplicación
 * Configuración inicial y autoloader
 */

// Configurar manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuración de base de datos
require_once __DIR__ . '/../config/db.php';

// Autoloader simple para modelos y controladores
spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
        __DIR__ . '/middleware/',
        __DIR__ . '/services/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir Router
require_once __DIR__ . '/Router.php';

// Configurar rutas
$router = new Router();

// Rutas de autenticación
$router->get('/login', 'Auth@showLogin');
$router->post('/auth/login', 'Auth@login');
$router->get('/register', 'Auth@showRegister');
$router->post('/auth/register', 'Auth@register');
$router->get('/logout', 'Auth@logout');

// Rutas de verificación de email
$router->get('/verify-email', 'Auth@verifyEmail');
$router->get('/verification-sent', 'Auth@verificationSent');
$router->post('/auth/resend-verification', 'Auth@resendVerification');

// Rutas de medicamentos
$router->get('/medicamentos', 'Medicamento@index');
$router->get('/medicamentos/create', 'Medicamento@create');
$router->post('/medicamentos', 'Medicamento@store');
$router->get('/medicamentos/{id}', 'Medicamento@show');
$router->get('/medicamentos/{id}/edit', 'Medicamento@edit');
$router->post('/medicamentos/{id}', 'Medicamento@update');
$router->delete('/medicamentos/{id}', 'Medicamento@delete');
$router->get('/medicamentos/search', 'Medicamento@search');
$router->get('/medicamentos/low-stock', 'Medicamento@lowStock');
$router->get('/medicamentos/expiring-soon', 'Medicamento@expiringSoon');

// Rutas del dashboard
$router->get('/dashboard', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }
    
    $role = $_SESSION['user_role'];
    switch ($role) {
        case 'admin':
        case 'empleado':
            header('Location: /admin/dashboard.php');
            break;
        case 'cliente':
            header('Location: /public/dashboard.php');
            break;
        default:
            header('Location: /');
    }
    exit();
});

// Ruta principal
$router->get('/', function() {
    header('Location: /public/index.php');
    exit();
});

// Resolver ruta actual
$router->resolve();

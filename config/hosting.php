<?php
/**
 * Configuración para Hosting - FarmaXpress
 * Archivo de configuración específico para hosting en producción
 */

// Configuración de base de datos para hosting
// Reemplaza estos valores con los de tu hosting
define('DB_HOST', 'localhost'); // o la IP de tu servidor de BD
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_CHARSET', 'utf8mb4');

// Configuración de email para hosting
// Reemplaza con los datos de tu proveedor de email
define('SMTP_HOST', 'smtp.tu-proveedor.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu_email@tu-dominio.com');
define('SMTP_PASSWORD', 'tu_contraseña_email');
define('FROM_EMAIL', 'noreply@tu-dominio.com');
define('FROM_NAME', 'FarmaXpress');

// Configuración de OAuth para hosting
// Reemplaza con tus credenciales de OAuth
define('GOOGLE_CLIENT_ID', 'tu_google_client_id');
define('GOOGLE_CLIENT_SECRET', 'tu_google_client_secret');
define('APPLE_CLIENT_ID', 'tu_apple_client_id');
define('APPLE_CLIENT_SECRET', 'tu_apple_client_secret');
define('MICROSOFT_CLIENT_ID', 'tu_microsoft_client_id');
define('MICROSOFT_CLIENT_SECRET', 'tu_microsoft_client_secret');

// URLs de redirección para OAuth
define('GOOGLE_REDIRECT_URI', 'https://tu-dominio.com/auth/google-callback.php');
define('APPLE_REDIRECT_URI', 'https://tu-dominio.com/auth/apple-callback.php');
define('MICROSOFT_REDIRECT_URI', 'https://tu-dominio.com/auth/microsoft-callback.php');

// Configuración de la aplicación
define('APP_NAME', 'FarmaXpress');
define('APP_URL', 'https://tu-dominio.com');
define('APP_ENV', 'production'); // production, development, testing

// Configuración de seguridad
define('SECURITY_SALT', 'tu_salt_secreto_aqui');
define('SESSION_LIFETIME', 3600); // 1 hora
define('RATE_LIMIT_ENABLED', true);

// Configuración de logging
define('LOG_ENABLED', true);
define('LOG_FILE', '/tmp/farmaxpress.log');
define('SECURITY_LOG_FILE', '/tmp/farmaxpress_security.log');

// Configuración de archivos
define('UPLOAD_PATH', '/uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB

// Configuración de cache
define('CACHE_ENABLED', true);
define('CACHE_PATH', '/tmp/cache/');

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de errores para producción
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_FILE);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Solo si usas HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Configuración de memoria y tiempo de ejecución
ini_set('memory_limit', '128M');
ini_set('max_execution_time', 30);
ini_set('max_input_time', 30);

// Configuración de uploads
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_file_uploads', 20);

// Función para obtener configuración
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Función para verificar si estamos en producción
function isProduction() {
    return getConfig('APP_ENV') === 'production';
}

// Función para obtener URL base
function getBaseUrl() {
    return getConfig('APP_URL', 'http://localhost');
}

// Función para logging
function logMessage($message, $level = 'INFO') {
    if (getConfig('LOG_ENABLED', false)) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents(getConfig('LOG_FILE', '/tmp/app.log'), $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Función para logging de seguridad
function logSecurity($action, $details = '') {
    if (getConfig('LOG_ENABLED', false)) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $logEntry = "[$timestamp] SECURITY [$action] IP: $ip | UA: $userAgent | Details: $details" . PHP_EOL;
        file_put_contents(getConfig('SECURITY_LOG_FILE', '/tmp/security.log'), $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Verificar configuración crítica
$required_configs = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SMTP_HOST', 'SMTP_USERNAME'];
foreach ($required_configs as $config) {
    if (!defined($config) || constant($config) === 'tu_' . strtolower($config)) {
        logMessage("Configuración faltante o no personalizada: $config", 'ERROR');
    }
}

logMessage('Configuración de hosting cargada correctamente');
?>

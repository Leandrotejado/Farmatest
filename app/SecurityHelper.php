<?php
/**
 * Security Helper - FarmaXpress
 * Funciones de seguridad centralizadas
 */

class SecurityHelper {
    
    /**
     * Sanitizar entrada de datos
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validar email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validar contraseña
     */
    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }
    
    /**
     * Generar token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verificar token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Validar número entero positivo
     */
    public static function validatePositiveInt($value) {
        return is_numeric($value) && (int)$value > 0;
    }
    
    /**
     * Validar número decimal positivo
     */
    public static function validatePositiveDecimal($value) {
        return is_numeric($value) && (float)$value > 0;
    }
    
    /**
     * Limpiar entrada de texto
     */
    public static function cleanText($text) {
        return preg_replace('/[<>"\']/', '', trim($text));
    }
    
    /**
     * Verificar autenticación de admin
     */
    public static function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: login.php');
            exit();
        }
    }
    
    /**
     * Verificar autenticación de usuario
     */
    public static function requireUser() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }
    }
    
    /**
     * Verificar email verificado
     */
    public static function requireVerifiedEmail() {
        if (!isset($_SESSION['email_verified']) || $_SESSION['email_verified'] != 1) {
            header('Location: verify-code.php');
            exit();
        }
    }
    
    /**
     * Rate limiting básico
     */
    public static function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
        $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $now = time();
        
        // Limpiar intentos antiguos
        if (isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = array_filter(
                $_SESSION['rate_limit'][$key],
                function($timestamp) use ($now, $time_window) {
                    return ($now - $timestamp) < $time_window;
                }
            );
        } else {
            $_SESSION['rate_limit'][$key] = [];
        }
        
        // Verificar límite
        if (count($_SESSION['rate_limit'][$key]) >= $max_attempts) {
            return false;
        }
        
        // Agregar intento actual
        $_SESSION['rate_limit'][$key][] = $now;
        
        return true;
    }
    
    /**
     * Log de seguridad
     */
    public static function logSecurity($action, $details = '') {
        $log_entry = date('Y-m-d H:i:s') . " - " . $action . " - " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " - " . $details . "\n";
        error_log($log_entry, 3, 'security.log');
    }
}
?>

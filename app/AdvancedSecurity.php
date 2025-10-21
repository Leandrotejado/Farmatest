<?php
/**
 * Security Enhancement - FarmaXpress
 * Mejoras de seguridad avanzadas para contraseñas y autenticación
 */

class AdvancedSecurity {
    
    /**
     * Verificar si una contraseña está comprometida usando HaveIBeenPwned
     */
    public static function checkPasswordBreach($password) {
        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);
        
        try {
            $url = "https://api.pwnedpasswords.com/range/" . $prefix;
            $response = file_get_contents($url);
            
            if ($response !== false) {
                $lines = explode("\n", $response);
                foreach ($lines as $line) {
                    if (strpos($line, $suffix) !== false) {
                        return true; // Contraseña comprometida
                    }
                }
            }
        } catch (Exception $e) {
            // Si no se puede verificar, asumir que está bien
            return false;
        }
        
        return false; // Contraseña segura
    }
    
    /**
     * Generar contraseña segura
     */
    public static function generateSecurePassword($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    /**
     * Validar fortaleza de contraseña
     */
    public static function validatePasswordStrength($password) {
        $score = 0;
        $feedback = [];
        
        // Longitud mínima
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = "Debe tener al menos 8 caracteres";
        }
        
        // Longitud recomendada
        if (strlen($password) >= 12) {
            $score += 1;
        }
        
        // Contiene minúsculas
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Debe contener letras minúsculas";
        }
        
        // Contiene mayúsculas
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Debe contener letras mayúsculas";
        }
        
        // Contiene números
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Debe contener números";
        }
        
        // Contiene símbolos especiales
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = "Debe contener símbolos especiales";
        }
        
        // No contiene patrones comunes
        $commonPatterns = ['123', 'abc', 'qwe', 'password', 'admin', 'user'];
        foreach ($commonPatterns as $pattern) {
            if (stripos($password, $pattern) !== false) {
                $score -= 1;
                $feedback[] = "No debe contener patrones comunes";
                break;
            }
        }
        
        return [
            'score' => max(0, $score),
            'max_score' => 6,
            'feedback' => $feedback,
            'is_strong' => $score >= 4
        ];
    }
    
    /**
     * Hash seguro de contraseña con salt único
     */
    public static function hashPassword($password) {
        // Generar salt único
        $salt = bin2hex(random_bytes(32));
        
        // Hash con Argon2ID (más seguro que bcrypt)
        if (function_exists('password_hash') && defined('PASSWORD_ARGON2ID')) {
            $hash = password_hash($password . $salt, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536, // 64 MB
                'time_cost' => 4,       // 4 iteraciones
                'threads' => 3          // 3 hilos
            ]);
        } else {
            // Fallback a bcrypt
            $hash = password_hash($password . $salt, PASSWORD_BCRYPT, ['cost' => 12]);
        }
        
        return [
            'hash' => $hash,
            'salt' => $salt
        ];
    }
    
    /**
     * Verificar contraseña con salt
     */
    public static function verifyPassword($password, $hash, $salt) {
        return password_verify($password . $salt, $hash);
    }
    
    /**
     * Generar token de autenticación de dos factores
     */
    public static function generate2FAToken() {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verificar token 2FA con ventana de tiempo
     */
    public static function verify2FAToken($token, $storedToken, $window = 300) {
        // Verificar que el token no haya expirado (5 minutos)
        if (time() - $storedToken['timestamp'] > $window) {
            return false;
        }
        
        return hash_equals($token, $storedToken['token']);
    }
    
    /**
     * Detectar intentos de fuerza bruta
     */
    public static function detectBruteForce($ip, $maxAttempts = 5, $timeWindow = 900) {
        $key = 'brute_force_' . $ip;
        
        if (!isset($_SESSION['security'])) {
            $_SESSION['security'] = [];
        }
        
        if (!isset($_SESSION['security'][$key])) {
            $_SESSION['security'][$key] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'blocked_until' => 0
            ];
        }
        
        $data = $_SESSION['security'][$key];
        
        // Verificar si está bloqueado
        if ($data['blocked_until'] > time()) {
            return [
                'blocked' => true,
                'remaining_time' => $data['blocked_until'] - time()
            ];
        }
        
        // Resetear contador si ha pasado el tiempo
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION['security'][$key] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'blocked_until' => 0
            ];
            $data = $_SESSION['security'][$key];
        }
        
        // Incrementar intentos
        $data['attempts']++;
        
        // Bloquear si excede el límite
        if ($data['attempts'] >= $maxAttempts) {
            $data['blocked_until'] = time() + 3600; // Bloquear por 1 hora
        }
        
        $_SESSION['security'][$key] = $data;
        
        return [
            'blocked' => $data['attempts'] >= $maxAttempts,
            'attempts' => $data['attempts'],
            'remaining_time' => $data['blocked_until'] - time()
        ];
    }
    
    /**
     * Limpiar datos de seguridad antiguos
     */
    public static function cleanupSecurityData() {
        if (isset($_SESSION['security'])) {
            foreach ($_SESSION['security'] as $key => $data) {
                if (isset($data['first_attempt']) && time() - $data['first_attempt'] > 3600) {
                    unset($_SESSION['security'][$key]);
                }
            }
        }
    }
    
    /**
     * Log de eventos de seguridad
     */
    public static function logSecurityEvent($event, $details = '', $level = 'INFO') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'event' => $event,
            'details' => $details,
            'level' => $level
        ];
        
        $logFile = 'security.log';
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Verificar integridad de archivos críticos
     */
    public static function verifyFileIntegrity() {
        $criticalFiles = [
            'config/db.php',
            'config/email.php',
            'admin/login.php',
            'public/login.php'
        ];
        
        $results = [];
        foreach ($criticalFiles as $file) {
            if (file_exists($file)) {
                $results[$file] = [
                    'exists' => true,
                    'size' => filesize($file),
                    'modified' => filemtime($file),
                    'hash' => hash_file('sha256', $file)
                ];
            } else {
                $results[$file] = ['exists' => false];
            }
        }
        
        return $results;
    }
}
?>

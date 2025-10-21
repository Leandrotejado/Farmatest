<?php
require_once 'BaseModel.php';

/**
 * Modelo para gestión de usuarios
 */
class UserModel extends BaseModel {
    protected $table = 'usuarios';
    
    /**
     * Autenticar usuario
     */
    public function authenticate($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function createUser($data) {
        // Hash de la contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create($data);
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getByRole($role) {
        return $this->findBy('rol', $role);
    }
    
    /**
     * Actualizar último acceso
     */
    public function updateLastAccess($userId) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET ultimo_acceso = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Verificar si el email existe
     */
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
    
    /**
     * Crear usuario con verificación pendiente
     */
    public function createUserWithVerification($data) {
        // Hash de la contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Generar token de verificación
        $data['verification_token'] = bin2hex(random_bytes(32));
        $data['email_verified'] = 0; // No verificado
        $data['verification_expires'] = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        return $this->create($data);
    }
    
    /**
     * Verificar email con token
     */
    public function verifyEmail($token) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM {$this->table} 
            WHERE verification_token = ? 
            AND email_verified = 0 
            AND verification_expires > NOW()
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Marcar como verificado
            $updateStmt = $this->pdo->prepare("
                UPDATE {$this->table} 
                SET email_verified = 1, 
                    verification_token = NULL, 
                    verification_expires = NULL,
                    email_verified_at = NOW()
                WHERE id = ?
            ");
            $updateStmt->execute([$user['id']]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario por token de verificación
     */
    public function getByVerificationToken($token) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE verification_token = ? 
            AND email_verified = 0 
            AND verification_expires > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    /**
     * Verificar si el email está verificado
     */
    public function isEmailVerified($email) {
        $stmt = $this->pdo->prepare("SELECT email_verified FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ? (bool)$result['email_verified'] : false;
    }
    
    /**
     * Generar token de recuperación de contraseña
     */
    public function generatePasswordResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET password_reset_token = ?, 
                password_reset_expires = ?
            WHERE email = ?
        ");
        
        return $stmt->execute([$token, $expires, $email]) ? $token : false;
    }
    
    /**
     * Verificar token de recuperación de contraseña
     */
    public function verifyPasswordResetToken($token) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM {$this->table} 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    /**
     * Actualizar contraseña con token
     */
    public function updatePasswordWithToken($token, $newPassword) {
        $user = $this->verifyPasswordResetToken($token);
        
        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                UPDATE {$this->table} 
                SET password = ?, 
                    password_reset_token = NULL, 
                    password_reset_expires = NULL
                WHERE id = ?
            ");
            return $stmt->execute([$hashedPassword, $user['id']]);
        }
        
        return false;
    }
}

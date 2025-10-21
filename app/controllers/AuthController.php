<?php
require_once 'BaseController.php';
require_once '../models/UserModel.php';

/**
 * Controlador para autenticación de usuarios
 */
class AuthController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        // Si ya está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard.php');
        }
        
        $this->render('auth/login');
    }
    
    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $this->validateRequired($_POST, ['email', 'password']);
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            
            // Actualizar último acceso
            $this->userModel->updateLastAccess($user['id']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Login exitoso',
                'redirect' => $this->getRedirectUrl($user['rol'])
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }
    }
    
    /**
     * Mostrar formulario de registro
     */
    public function showRegister() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard.php');
        }
        
        $this->render('auth/register');
    }
    
    /**
     * Procesar registro
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $this->validateRequired($_POST, ['nombre', 'email', 'password', 'confirm_password']);
        
        $data = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'rol' => 'cliente' // Rol por defecto para nuevos usuarios
        ];
        
        // Validar que las contraseñas coincidan
        if ($data['password'] !== $_POST['confirm_password']) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ], 400);
        }
        
        // Verificar si el email ya existe
        if ($this->userModel->emailExists($data['email'])) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'El email ya está registrado'
            ], 400);
        }
        
        // Crear usuario con verificación
        if ($this->userModel->createUserWithVerification($data)) {
            // Obtener el token de verificación
            $user = $this->userModel->getByVerificationToken($data['verification_token']);
            
            // Enviar correo de verificación
            require_once '../services/EmailService.php';
            $emailService = new EmailService();
            $emailResult = $emailService->sendVerificationEmail(
                $data['email'], 
                $data['nombre'], 
                $data['verification_token']
            );
            
            if ($emailResult['success']) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente. Revisa tu correo para verificar tu cuenta.',
                    'redirect' => '/verification-sent.php'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuario registrado pero error al enviar correo de verificación: ' . $emailResult['message']
                ], 500);
            }
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al registrar usuario'
            ], 500);
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
    
    /**
     * Verificar email con token
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            $this->render('auth/verification-error', [
                'message' => 'Token de verificación no válido'
            ]);
            return;
        }
        
        if ($this->userModel->verifyEmail($token)) {
            // Obtener datos del usuario para enviar correo de bienvenida
            $user = $this->userModel->getByVerificationToken($token);
            
            // Enviar correo de bienvenida
            require_once '../services/EmailService.php';
            $emailService = new EmailService();
            $emailService->sendWelcomeEmail($user['email'], $user['nombre']);
            
            $this->render('auth/verification-success');
        } else {
            $this->render('auth/verification-error', [
                'message' => 'Token de verificación expirado o inválido'
            ]);
        }
    }
    
    /**
     * Mostrar página de verificación enviada
     */
    public function verificationSent() {
        $this->render('auth/verification-sent');
    }
    
    /**
     * Reenviar correo de verificación
     */
    public function resendVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email requerido'
            ], 400);
        }
        
        // Verificar si el usuario existe y no está verificado
        $user = $this->userModel->findBy('email', $email);
        
        if (empty($user)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email no encontrado'
            ], 404);
        }
        
        $user = $user[0];
        
        if ($user['email_verified']) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'El email ya está verificado'
            ], 400);
        }
        
        // Generar nuevo token
        $newToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $this->userModel->pdo->prepare("
            UPDATE usuarios 
            SET verification_token = ?, 
                verification_expires = ?
            WHERE email = ?
        ");
        
        if ($stmt->execute([$newToken, $expires, $email])) {
            // Enviar correo de verificación
            require_once '../services/EmailService.php';
            $emailService = new EmailService();
            $emailResult = $emailService->sendVerificationEmail($email, $user['nombre'], $newToken);
            
            if ($emailResult['success']) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Correo de verificación reenviado exitosamente'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Error al reenviar correo: ' . $emailResult['message']
                ], 500);
            }
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al generar nuevo token'
            ], 500);
        }
    }
    
    /**
     * Obtener URL de redirección según el rol
     */
    private function getRedirectUrl($role) {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard.php';
            case 'empleado':
                return '/admin/dashboard.php';
            case 'cliente':
                return '/public/dashboard.php';
            default:
                return '/';
        }
    }
}
